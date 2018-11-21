<?php

function add_exchange_variation($trans_no, $date_, $acc_id, $account,
    $currency, $person_type_id=null, $person_id = "")
{
	if (is_company_currency($currency))
		return false;
	if ($date_ == null)
		$date_ = Today();
	$for_amount = 0;

	// We have to calculate all the currency accounts belonging to the GL account
	// upto $date_ and calculate with the exchange rates. And then compare with the GL account balance.
	$sql = "SELECT SUM(bt.amount) AS for_amount, ba.bank_curr_code 		
		FROM ".TB_PREF."bank_trans bt, ".TB_PREF."bank_accounts ba
		WHERE ba.id = bt.bank_act AND ba.account_code = ".db_escape($account)." AND bt.trans_date<='".date2sql($date_)."'
		GROUP BY ba.bank_curr_code";	
	$result = db_query($sql, "Transactions for bank account $acc_id could not be calculated");
	while ($row = db_fetch($result))
	{
		if ($row['for_amount'] == 0)
			continue;
		$rate = get_exchange_rate_from_home_currency($row['bank_curr_code'], $date_);
		$for_amount += round2($row['for_amount'] * $rate, user_price_dec());
	}	
	$amount = get_gl_trans_from_to("", $date_, $account);
	$diff = $amount - $for_amount;
	if (floatcmp($diff,0))
	{
		// generate journal entry // FIXME: use cart?
		global $Refs;

		begin_transaction();
		$trans_no = get_next_trans_no(ST_JOURNAL);
		$ref = $Refs->get_next(ST_JOURNAL);

		add_journal(ST_JOURNAL, $trans_no, $diff, $date_ , get_company_currency(), $ref, '', 1, $date_, $date_);
		$Refs->save(ST_JOURNAL, $trans_no, $ref);
		add_audit_trail(ST_JOURNAL, $trans_no, $date_);

		if ($person_type_id == null)
			$person_type_id = PT_MISC;
		add_gl_trans(ST_JOURNAL, $trans_no, $date_, $account, 0, 0, _("Exchange Variance"),
    		$diff, null, $person_type_id, $person_id);

    	add_gl_trans(ST_JOURNAL, $trans_no, $date_, get_company_pref('exchange_diff_act'), 0, 0, 
    		_("Exchange Variance"),	-$diff, null, $person_type_id, $person_id);
		commit_transaction();
		return true;
	}
	return false;
}

function add_cust_supp_revaluation($ledger_act, $ex_act, $date, $amount, $person_type_id, $person_id, $memo)
{
	global $Refs;
	if ($amount == 0)
		return;

	begin_transaction();
	$trans_no = get_next_trans_no(ST_JOURNAL);
	$ref = $Refs->get_next(ST_JOURNAL);

	add_journal(ST_JOURNAL, $trans_no, $amount, $date, get_company_currency(), $ref, '', 1, $date, $date);
	$Refs->save(ST_JOURNAL, $trans_no, $ref);
	add_audit_trail(ST_JOURNAL, $trans_no, $date);

	add_gl_trans(ST_JOURNAL, $trans_no, $date, $ledger_act, 0, 0, _("Exchange Variance"),
		$amount, null, $person_type_id, $person_id);
	add_gl_trans(ST_JOURNAL, $trans_no, $date, $ex_act, 0, 0, 
		_("Exchange Variance"),	-$amount, null, $person_type_id, $person_id);
	add_comments(ST_JOURNAL, $trans_no, $date, $memo);
	commit_transaction();
}

function get_cust_account_curr_balances($date)
{
	$to = date2sql($date);

    $sql = "SELECT SUM(IF(t.type IN(". implode(',', array(ST_CUSTCREDIT, ST_CUSTPAYMENT, ST_BANKDEPOSIT, ST_JOURNAL))."),
			-(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 
	    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount))) AS amount,
	    	dt.debtor_no, dt.name, dt.curr_code, b.receivables_account
		FROM ".TB_PREF."debtor_trans t 
			LEFT JOIN ".TB_PREF."debtors_master dt ON t.debtor_no = dt.debtor_no
			LEFT JOIN ".TB_PREF."cust_branch b ON t.debtor_no = b.debtor_no
			LEFT JOIN ".TB_PREF."voided as v ON v.type = t.type and v.id=t.trans_no
    	WHERE ISNULL(v.date_) AND t.type <> ".ST_CUSTDELIVERY." AND t.tran_date <= '$to' 
    		AND t.branch_code=b.branch_code AND dt.curr_code<>'".get_company_pref('curr_default')."' 
    		GROUP BY t.debtor_no, b.receivables_account";

    $result = db_query($sql,"Open balances in foreign currency for cannot be retrieved");
	return  $result;
}

function get_supp_account_curr_balances($date)
{
	$to = date2sql($date);

    $sql = "SELECT SUM(-(t.ov_amount + t.ov_gst + t.ov_discount)) AS amount,
	    	supp.supplier_id, supp.supp_name, supp.curr_code, supp.payable_account
		FROM ".TB_PREF."supp_trans t 
			LEFT JOIN ".TB_PREF."suppliers supp ON t.supplier_id = supp.supplier_id
			LEFT JOIN ".TB_PREF."voided as v ON v.type = t.type and v.id=t.trans_no
    	WHERE ISNULL(v.date_) AND t.tran_date <= '$to' 
    		AND supp.curr_code<>'".get_company_pref('curr_default')."' 
    		GROUP BY t.supplier_id";

    $result = db_query($sql,"Open balances in foreign currency for cannot be retrieved");
	return  $result;
}

function get_account_home_balance($person_id, $person_type, $rec_account, $to_date)
{
	$to = date2sql($to_date);

    $sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans  
			WHERE person_id=".db_escape($person_id)." AND account='$rec_account' 
			AND person_type_id=$person_type AND tran_date <= '$to'";
	$result = db_query($sql, "The AR/AP balance for customer $person_id could not be calculated");
	$row = db_fetch_row($result);
	return $row[0];
}
//------------- New helper functions for revaluation of customer/supplier currencies 2011-05-08 Joe Hunt.

function add_exchange_variation_all($date=null, $ref="", $memo)
{
	global $Refs;
	begin_transaction();
	$exchanged = false;
	$trans_no = get_next_trans_no(ST_JOURNAL);
	$sql = "SELECT * FROM ".TB_PREF."bank_accounts";
	$result = db_query($sql, "could not retreive bank accounts");
	while ($myrow = db_fetch($result))
	{
		if (add_exchange_variation($trans_no, $date, $myrow['id'], $myrow['account_code'],
			$myrow['bank_curr_code']))
			$exchanged = true;
	}
	if ($exchanged)
	{
		add_comments(ST_JOURNAL, $trans_no, $date, $memo);
		if ($ref == "")
			$ref = $Refs->get_next(ST_JOURNAL, null, $date);
		$Refs->save(ST_JOURNAL, $trans_no, $ref);
		add_audit_trail(ST_JOURNAL, $trans_no, $date);
	}	

	//------------- BEGIN inserted for revaluation of customer/supplier currencies 2011-05-08 Joe Hunt.
	if ($date == null)
		$date = Today();
	$exchange_act = get_company_pref('exchange_diff_act');
	$je = 0;
	// debtors
	$res = get_cust_account_curr_balances($date);
	
	while($row = db_fetch($res)) 
	{
		$exrate = get_exchange_rate_from_home_currency($row['curr_code'], $date);
		$foreign = round2($row['amount']*$exrate, user_price_dec());
		$home = round2(get_account_home_balance($row['debtor_no'], PT_CUSTOMER, $row['receivables_account'], $date), user_price_dec());
		if ($foreign != $home) 
		{
			$amount = $foreign - $home;	
			add_cust_supp_revaluation($row['receivables_account'], $exchange_act, $date, $amount, PT_CUSTOMER, 
				$row['debtor_no'], $memo);
			$je++;	
		}
	}
	// creditors
	$res = get_supp_account_curr_balances($date);
	
	while($row = db_fetch($res)) 
	{
		$exrate = get_exchange_rate_from_home_currency($row['curr_code'], $date);
		$foreign = round2($row['amount']*$exrate, user_price_dec());
		$home = round2(get_account_home_balance($row['supplier_id'], PT_SUPPLIER, $row['payable_account'], $date), user_price_dec());
		if ($foreign != $home) 
		{
			$amount = $foreign - $home;	
			add_cust_supp_revaluation($row['payable_account'], $exchange_act, $date, $amount, PT_SUPPLIER, 
				$row['supplier_id'], $memo);
			$je++;	
		}
	}
	//------------- END

	commit_transaction();
	return array(($exchanged ? $trans_no : 0), $je);
}
//===================================================================
function get_parentid($parent_id, $parent_type)
{
    $sql = "SELECT parent_id FROM ".TB_PREF."bank_trans 
    WHERE trans_no=".db_escape($parent_id)." 
    AND type=".db_escape($parent_type);

    $result = db_query($sql, "The debtor transaction could not be queried");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_parenttype($parent_id, $parent_type)
{
    $sql = "SELECT parent_type FROM ".TB_PREF."bank_trans 
    WHERE trans_no=".db_escape($parent_id)." 
    AND type=".db_escape($parent_type);

    $result = db_query($sql, "The debtor transaction could not be queried");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------
//	Add bank tranfer to database.
//
//	$from_account - source bank account id
//	$to_account   -	target bank account id
//	

function add_bank_transfer($from_account, $to_account, $date_,
                           $amount, $ref, $memo_, $charge=0, $target_amount=0,$cheque, $parent_id, $parent_type, $cheque_date)
{
    global $Refs, $SysPrefs;

    begin_transaction();
    $args = func_get_args(); if (count($args) < 8) $args[] = 0;
    $args = (object)array_combine(array('from_account', 'to_account', 'date_', 'amount',
        'ref', 'memo_', 'charge', 'target_amount','cheque'), $args);
    $args->trans_no = 0;
    hook_db_prewrite($args, ST_BANKTRANSFER);

    $trans_type = ST_BANKTRANSFER;

    $currency = get_bank_account_currency($from_account);

    $trans_no = get_next_trans_no($trans_type);

    $fromact = get_bank_account($from_account);
    $toact = get_bank_account($to_account);
    $person_id = _("From")." ".$fromact['bank_account_name']." "._("To")." ".$toact['bank_account_name'];

    $from_gl_account = get_bank_gl_account($from_account);
    $to_gl_account = get_bank_gl_account($to_account);

    $exchanged = false;
    $total = 0;
    // do the source account postings
    $total += add_gl_trans($trans_type, $trans_no, $date_, $from_gl_account, 0, 0, $person_id,
        -($amount + $charge), $currency,0,0,0,0,$cheque,$cheque_date);
//display_error($parent_id."++".$parent_type);
//    if($parent_id != 0)
//    {
//        update_parant_transaction($parent_id, $parent_type);
//    }
    if($parent_type==12) {
        $parent_id2 = $parent_id;
        $parent_type2 = $parent_type;
    }
    else{

        $parent_id2 = get_parentid($parent_id,$parent_type);
        $parent_type2 = get_parenttype($parent_id,$parent_type);
    }
    add_bank_trans($trans_type, $trans_no, $from_account, $ref,
        $date_, -($amount + $charge),
        PT_MISC, $person_id, $currency,
        "Cannot insert a source bank transaction",0,$cheque,$cheque_date,0,0,0,0,0,
        0,0,0,0,0,$parent_id2,$parent_type2);
    if ($SysPrefs->auto_currency_revaluation())
    {
        $trans_no1 = get_next_trans_no(ST_JOURNAL);
        if (add_exchange_variation($trans_no1, $date_, $from_account, $from_gl_account,
            $currency, PT_MISC, $person_id))
            $exchanged = true;
    }
    if ($charge != 0)
    {
        /* Now Debit bank charge account with charges */
        $charge_act = get_bank_charge_account($from_account);
        $total += add_gl_trans($trans_type, $trans_no, $date_,
            $charge_act, 0, 0, $person_id, $charge, $currency,0,0,0,0,$cheque,$cheque_date);
    }

    // provide backward compatibility for extension modules (target amount can be not passed)
    $to_currency = $target_amount ? $toact['bank_curr_code'] : $currency;
    $to_amount = $target_amount ? $target_amount : $amount;

    // do the destination account postings
    $total += add_gl_trans($trans_type, $trans_no, $date_, $to_gl_account, 0, 0, $person_id,
        $to_amount, $to_currency,0,0,0,0,$cheque,$cheque_date);

    /*Post a balance post if $total != 0 */
    if ($currency == $to_currency)
        add_gl_balance($trans_type, $trans_no, $date_, -$total);
    else	// in this case those are exchange variances between bank and home rates
        add_gl_trans($trans_type, $trans_no, $date_, get_company_pref('exchange_diff_act'),
            0, 0, _("Exchange Variance"), -$total,null,0,0,0,0,$cheque,$cheque_date);

    add_bank_trans($trans_type, $trans_no, $to_account, $ref,
        $date_, $to_amount, PT_MISC, $person_id,
        $to_currency, "Cannot insert a destination bank transaction",0,$cheque,$cheque_date,0,0,0,
        0,0,0,0,0,0,0,$parent_id2,$parent_type2);

    if ($SysPrefs->auto_currency_revaluation())
    {
        $currency = get_bank_account_currency($to_account);
        if (add_exchange_variation($trans_no1, $date_, $to_account, $to_gl_account,
            $currency, PT_MISC, $person_id))
            $exchanged = true;
    }
    if ($exchanged == true)
    {
        $ref1 = $Refs->get_next(ST_JOURNAL, null, $date_);
        $Refs->save(ST_JOURNAL, $trans_no1, $ref1);
        add_audit_trail(ST_JOURNAL, $trans_no1, $date_);
    }
    add_comments($trans_type, $trans_no, $date_, $memo_);

    $Refs->save($trans_type, $trans_no, $ref);
    add_audit_trail($trans_type, $trans_no, $date_);

    $args->trans_no = $trans_no;
    hook_db_postwrite($args, ST_BANKTRANSFER);
    commit_transaction();

    return $trans_no;
}

function check_bank_transfer($trans_no, $from_account, $to_account, $date_,
    $amount, $target_amount=0)
{
	$dbResult = get_bank_trans(ST_BANKTRANSFER, $trans_no);

	$old_from = db_fetch($dbResult);
	$old_to = db_fetch($dbResult);
	if ($old_to['amount'] < 0.0) {
		$tmp = $old_from;
		$old_from = $old_to;
		$old_to = $tmp;
	}
	// There are four accounts to consider:
	// 1) The original from account that is being voided. This results in funds being put back which is always fine.
	// 2) The original to account that is being voided. This results in funds being removed which may result in a
	//    negative balance in the account at some time and therefore needs to be checked.
	$problemTransaction = check_bank_account_history(-$old_to['amount'], $old_to['bank_act'], sql2date($old_from['trans_date']));
	if ($problemTransaction) {
		// If the destination account is the same as that being edited, it may be that this edit will resolve the
		// problem of voiding.
		if ($to_account == $old_to['bank_act'] && sql_date_comp($problemTransaction['trans_date'], date2sql($date_)) > 0) {
			$problemTransaction['amount'] += $amount;
			if ($problemTransaction['amount'] >= 0) {
				$problemTransaction = null;
			}
		}
		if ($problemTransaction != null) {
			$problemTransaction['account'] = $old_to['bank_act'];
			$problemTransaction['bank_account_name'] = $old_to['bank_account_name'];
			return $problemTransaction;
		}
	}

	// 3) The edited from account, that is having funds removed which may result in a
	//    negative balance in the account at some time and therefore needs to be checked.
	$balance_offset = 0;
	if ($from_account == $old_from['bank_act'] && sql_date_comp(date2sql($date_), $old_from['trans_date']) >= 0) {
		// If the edited from_account is the same as the original transaction, and the new date is later than the old date
		// then add the amount from the original from transaction when checking the history to simulate the void that would
		// take effect from that date.
		$balance_offset = -$old_from['amount'];
	}
	$problemTransaction = check_bank_account_history(-$amount, $from_account, $date_, null, $balance_offset);
	if ($problemTransaction != null) {
		$problemTransaction['account'] = $old_from['bank_act'];
		$problemTransaction['bank_account_name'] = $old_from['bank_account_name'];
		return $problemTransaction;
	}
	// 4) The edited to account, that is having funds added which is always ok.

	return $problemTransaction;
}

function update_bank_transfer(
	$trans_no, $from_account, $to_account, $date_,
	$amount, $ref, $memo_, $charge=0, $target_amount=0,$cheque, $parent_id, $parent_type, $cheque_date)
{
	begin_transaction();
	delete_comments(ST_BANKTRANSFER, $trans_no);
	void_bank_trans(ST_BANKTRANSFER, $trans_no, true);
	void_gl_trans(ST_BANKTRANSFER, $trans_no, true);
	$new_trans_no = add_bank_transfer(
		$from_account, $to_account, $date_, $amount,
		$ref, $memo_, $charge, $target_amount,$cheque, $parent_id, $parent_type, $cheque_date
	);
	commit_transaction();
	return $new_trans_no;
}

//----------------------------------------------------------------------------------
//	Add bank payment or deposit to database.
//
//	$from_account - bank account id
//  $items - transaction cart (line amounts in bank account's currency); negative for deposit
//  $person_type_id - defines type of $person_id identifiers
//  $person_id	- supplier/customer/other id
//  $person_detail_id - customer branch id or not used
//  $settled_amount - settled amount in AR/AP (if applicable) in customer/supplier currency (always non-negative number)
//
// returns an array of (inserted trans type, trans no)
//
// FIXME -revise code for update case
//
//ansar 20-05-2017
function get_employee_data($emp_id)
{
    $sql="select * FROM ".TB_PREF."employee where 	employee_id=".$emp_id." ";
    $db = db_query($sql,'Cant get Employee data');
    return db_fetch($db);
}

function write_bank_transaction($trans_type, $trans_no, $from_account, $items, $date_,
                                $person_type_id, $person_id, $person_detail_id,	$ref, $memo_, $use_transaction=true, $cheque,$settled_amount=null,$cheque_date
    ,$text_1,$text_2,$text_3,$month_, $parent_id, $parent_type)
{

    global $Refs, $SysPrefs, $db_connections;

    // we can only handle type 1 (payment)and type 2 (deposit)
    if ($trans_type != ST_BANKPAYMENT && $trans_type != ST_BANKDEPOSIT && $trans_type != ST_CPV && $trans_type != ST_CRV)
        display_db_error("Invalid type ($trans_type) sent to add_bank_transaction");

    $do_exchange_variance = false;
    $exchanged = false;
    if ($use_transaction)
        begin_transaction();

    $args = func_get_args(); if (count($args) < 11) $args[] = true;
    $args = (object)array_combine(array('trans_type', 'trans_no', 'from_account', 'items', 'date_',
        'person_type_id', 'person_id', 'person_detail_id', 'ref', 'memo_', 'use_transaction', 'settled_amount'),
        $args);
    hook_db_prewrite($args, $trans_type);

    $aid = 0;
    if ($trans_no) {
        $old_trans = $trans_no;
        $Refs->restore_last($trans_type, $trans_no);
        $aid = has_attachment($trans_type, $trans_no);
    } else
        $old_trans = false;

    $currency = get_bank_account_currency($from_account);
    $bank_gl_account = get_bank_gl_account($from_account);

    // the gl items are already inversed/negated for type 2 (deposit)
    $total_amount = $items->gl_items_total();

    if($parent_type==12) {
        $parent_id2 = $parent_id;
        $parent_type2 = $parent_type;
    }
    else{

        $parent_id2 = get_parentid($parent_id,$parent_type);
        $parent_type2 = get_parenttype($parent_id,$parent_type);
    }
//    if($parent_id != 0)
//    {
//        update_parant_transaction($parent_id, $parent_type);
//    }

    if ($person_type_id == PT_CUSTOMER)
    {
        // we need to add a customer transaction record
        // convert to customer currency
        if (!$settled_amount) // leaved for backward/ext compatibility
            $cust_amount = exchange_from_to(abs($total_amount), $currency, get_customer_currency($person_id), $date_);
        else
            $cust_amount = $settled_amount;

        if ($trans_type == ST_BANKPAYMENT)
            $cust_amount = -$cust_amount;

        $trans_no = write_customer_trans($trans_type, 0, $person_id, $person_detail_id,
            $date_, $ref, $cust_amount,0,0,0,0,0,0,0,0,0,0,0,0,0,0,
            0,0,0,0,0,0,0,0,0,0,0,0,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);


//SMS Configure on for DMNWS (S.H.G)
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS')
        {
            //SMS Configure on for DMNWS
            $path_to_root = "..";
            include_once($path_to_root . "/includes/ui/sms_send.inc");
            $outstanding = get_customer_details_balance_sms($person_id, $person_id);
            $balance = $outstanding["Balance"];
            $original = array('amount', 'outstanding');
            $replaced = array($cust_amount,  $balance);
            $GetTemplate = get_select_template_sms(ST_CUSTPAYMENT);
            $Message = $GetTemplate['template'];
            $sentence = str_replace($original, $replaced, $Message);
            $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
            SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($person_id).") );";
            $query = db_query($sql, "Error");
            $fetch = db_fetch($query);
            sms_send($fetch['phone'], htmlspecialchars_decode($sentence));
        }
//========================================================================



        if ($old_trans)
            move_trans_attachments($trans_type, $old_trans, $trans_no);
    }
    elseif ($person_type_id == PT_SUPPLIER)
    {
        // we need to add a supplier transaction record
        // convert to supp currency
        if (!$settled_amount) // leaved for for backward/ext compatibility
            $supp_amount = exchange_from_to(abs($total_amount), $currency, get_supplier_currency($person_id), $date_);
        else
            $supp_amount = $settled_amount;

        if ($trans_type == ST_BANKPAYMENT || $trans_type == ST_CPV)
            $supp_amount = -$supp_amount;

        $trans_no = write_supp_trans($trans_type, 0, $person_id, $date_, '',
            $ref, "", $supp_amount, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0,$cheque,$cheque_date,$text_1,$text_2,$text_3);

        if ($old_trans)
            move_trans_attachments($trans_type, $old_trans, $trans_no);
    }
    elseif ($person_type_id == PT_EMPLOYEE)
    {
        $emp_amount = -$total_amount;
        $trans_no = write_emp_trans($trans_type, 0, $person_id, $date_, '',
            $ref, "", $emp_amount, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0,$cheque,$cheque_date,$text_1,$text_2,$text_3);
    }
    else
    {
        $trans_no = get_next_trans_no($trans_type);
        $do_exchange_variance = $SysPrefs->auto_currency_revaluation();
        if ($do_exchange_variance)
            $trans_no1 = get_next_trans_no(ST_JOURNAL);
    }
    if ($aid != 0)
    {
        $row = get_attachment($aid);
        update_attachment($aid, $row['type_no'], $trans_no, $row['description'],
            $row['filename'], $row['unique_name'], $row['filesize'], $row['filetype']);
    }
    // do the source account postings

    add_bank_trans($trans_type, $trans_no, $from_account, $ref,
        $date_, -$total_amount,
        $person_type_id, $person_id,
        $currency,
        "Cannot insert a source bank transaction",0,$cheque,$cheque_date,$text_1,$text_2,$text_3,0,0,0,
        0,0,0,$month_, $parent_id2, $parent_type2);

    $total = 0;
    foreach ($items->gl_items as $gl_item)
    {
        $is_bank_to = is_bank_account($gl_item->code_id);

        if ($trans_type == ST_BANKPAYMENT AND $is_bank_to)
        {
            // we don't allow payments to go to a bank account. use transfer for this !
            display_db_error("invalid payment entered. Cannot pay to another bank account", "");
        }

        // do the destination account postings

        $total += add_gl_trans($trans_type, $trans_no, $date_, $gl_item->code_id,
            $gl_item->dimension_id, $gl_item->dimension2_id, $gl_item->reference,
            $gl_item->amount, $currency, $person_type_id, $person_id,'',0,$cheque,$cheque_date,0,$text_1,$text_2,$text_3);
        if ($is_bank_to)
        {
            add_bank_trans($trans_type, $trans_no, $is_bank_to, $ref,
                $date_, $gl_item->amount,
                $person_type_id, $person_id, $currency,
                "Cannot insert a destination bank transaction",0,$cheque,$cheque_date,$text_1,$text_2,$text_3,0,0,
                0,0,0,0,$month_, $parent_id2, $parent_type2);
            if ($do_exchange_variance)
            {
                add_exchange_variation($trans_no1, $date_, $is_bank_to, $gl_item->code_id,
                    $currency, $person_type_id, $person_id);
            }
        }
        // store tax details if the gl account is a tax account

        $amount = $gl_item->amount;
        $ex_rate = get_exchange_rate_from_home_currency($currency, $date_);

        add_gl_tax_details($gl_item->code_id, $trans_type, $trans_no, -$amount,
            $ex_rate, $date_, $memo_);
    }

    // do the source account postings

    add_gl_trans($trans_type, $trans_no, $date_, $bank_gl_account, 0, 0, $memo_,
        -$total, null, $person_type_id, $person_id,'',0,$cheque,$cheque_date,$text_1,$text_2,$text_3);

//display_error($cheque);
    if ($do_exchange_variance)
    {
        if ($exchanged || add_exchange_variation($trans_no1, $date_, $from_account, $bank_gl_account,
                $currency, $person_type_id, $person_id))
        {
            $ref1 = $Refs->get_next(ST_JOURNAL, null, $date_);
            $Refs->save(ST_JOURNAL, $trans_no1, $ref1);
            add_audit_trail(ST_JOURNAL, $trans_no1, $date_);
        }
    }

    add_comments($trans_type, $trans_no, $date_, $memo_);

    $Refs->save($trans_type, $trans_no, $ref);
    add_audit_trail($trans_type, $trans_no, $date_);

    // old transaction can be voided only after new transaction is entered,
    //  otherwise the operation could fail for cash accounts due to temporary negative balance
    if ($old_trans)
    {
        $msg = void_transaction($trans_type, $old_trans, Today(), _("Document reentered."));
        if ($msg)
        {
            display_error($msg);
            return false;
        }
    }


    $args->trans_no = $trans_no;
    hook_db_postwrite($args, $trans_type);
    if ($use_transaction)
        commit_transaction();

    return array($trans_type, $trans_no);
}
function write_bank_transaction_new($trans_type, $trans_no, $from_account, $items, $date_,
    $person_type_id, $person_id, $person_detail_id,	$ref, $memo_,
    $use_transaction=true,$code_id,$person_id_new,$amount_per_line,
    $cf_ref,$cheque_no_line,$cheque_date_line,$dimension_id, $dimension2_id,
    $dimension3_id,$month,$mod_payment,$reference2,$serial,$division,$fiscal,
    $text_1,$text_2,$text_3,$month_)
{


    global $Refs, $SysPrefs;
    //display_error($fiscal);
    $month1 = date("m",strtotime($date_));
    if($month1==01)
        $month_=1;
    elseif ($month1==02)
        $month_=2;
    elseif ($month1==03)
        $month_=3;
    elseif ($month1==04)
        $month_=4;
    elseif ($month1==05)
        $month_=5;
    elseif ($month1==06)
        $month_=6;
    elseif ($month1==07)
        $month_=7;
    elseif ($month1==08)
        $month_=8;
    elseif ($month1==09)
        $month_=9;
    elseif ($month1==10)
        $month_=10;
    elseif ($month1==11)
        $month_=11;
    elseif ($month1==12)
        $month_=12;

    // we can only handle type 1 (payment)and type 2 (deposit)
    if ($trans_type != ST_BANKPAYMENT && $trans_type != ST_BANKDEPOSIT)
        display_db_error("Invalid type ($trans_type) sent to add_bank_transaction");

    $do_exchange_variance = false;
    $exchanged = false;
    if ($use_transaction)
        begin_transaction();

    $args = func_get_args(); if (count($args) < 11) $args[] = true;
    $args = (object)array_combine(array('trans_type', 'trans_no', 'from_account', 'items', 'date_',
        'person_type_id', 'person_id', 'person_detail_id', 'ref', 'memo_', 'use_transaction'),
        $args);
    hook_db_prewrite($args, $trans_type);
    $aid = 0;
    if ($trans_no) {
        $old_trans = $trans_no;
        $Refs->restore_last($trans_type, $trans_no);
        $aid = has_attachment($trans_type, $trans_no);
    }

    $currency = get_bank_account_currency($from_account);
    $bank_gl_account = get_bank_gl_account($from_account);

    // the gl items are already inversed/negated for type 2 (deposit)
//    $total_amount = $items->gl_items_total();

     if ($person_type_id == PT_CUSTOMER)
    {
        // we need to add a customer transaction record

        // convert to customer currency
        $cust_amount = exchange_from_to($amount_per_line, $currency, get_customer_currency($person_id_new), $date_);
        // we need to negate it too
        $cust_amount = -$cust_amount;

        $trans_no = write_customer_trans($trans_type, 0, $person_id_new, $person_detail_id, $date_,
        $ref, $cust_amount,0,0,0,0,0,0,0,0,0,0,0,0,0,0,$cheque_no_line,$cheque_date_line);

    }
    elseif ($person_type_id == PT_SUPPLIER)
    {
        // we need to add a supplier transaction record
        // convert to supp currency
        $supp_amount = exchange_from_to($amount_per_line, $currency, get_supplier_currency($person_id), $date_);

        // we need to negate it too
        $supp_amount = -$supp_amount;

        $trans_no = write_supp_trans($trans_type, 0, $person_id, $date_, '',
        $ref, "", $supp_amount, 0, 0);

    }
     elseif ($person_type_id == PT_EMPLOYEE)
     {
         $emp_amount = -$amount_per_line;
         $trans_no = write_emp_trans($trans_type, 0, $person_id, $date_, '',
         $ref, "", $emp_amount, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0,$cheque_no_line,$cheque_date_line,
         $text_1,$text_2,$text_3);
     }
     else
    {
        $trans_no = get_next_trans_no($trans_type);
        $do_exchange_variance = $SysPrefs->auto_currency_revaluation();
        if ($do_exchange_variance)
            $trans_no1 = get_next_trans_no(ST_JOURNAL);
    }
    if ($aid != 0)
    {
        $row = get_attachment($aid);
        update_attachment($aid, $row['type_no'], $trans_no, $row['description'],
            $row['filename'], $row['unique_name'], $row['filesize'], $row['filetype']);
    }
    // do the source account postings

    add_bank_trans($trans_type, $trans_no, $from_account, $ref,
        $date_, -$amount_per_line,
        $person_type_id, $person_id_new,
        $currency,
        "Cannot insert a source bank transaction",0,$cheque_no_line,$cheque_date_line,$text_1,$text_2,$text_3,$reference2,$serial,$division,$month,$mod_payment,$fiscal,$month_);
    $total = 0;


    //foreach ($items->gl_items as $gl_item)
    {
        $is_bank_to = is_bank_account($code_id);

        if ($trans_type == ST_BANKPAYMENT AND $is_bank_to)
        {
            // we don't allow payments to go to a bank account. use transfer for this !
            display_db_error("invalid payment entered. Cannot pay to another bank account", "");
        }

        // do the destination account postings
        $total += add_gl_trans($trans_type, $trans_no, $date_,$code_id,
        $dimension_id, $dimension2_id, $cf_ref,
        $amount_per_line, $currency, $person_type_id, $person_id_new,0,
        0, $cheque_no_line, $cheque_date_line, $month, $mod_payment);

//        $total += add_gl_trans($trans_type, $trans_no, $date_, $gl_item->code_id,
//            $gl_item->dimension_id, $gl_item->dimension2_id, $gl_item->dimension3_id, $gl_item->reference,
//            $amount_per_line, $currency, $person_type_id, $person_id,'',0, trim($cheque_no_line));
        if ($is_bank_to)
        {
            add_bank_trans($trans_type, $trans_no, $is_bank_to, $ref,
                $date_, $amount_per_line,
                $person_type_id, $person_id_new, $currency,
                "Cannot insert a destination bank transaction",0,$cheque_no_line,$cheque_date_line,$reference2,$serial,$division);
            if ($do_exchange_variance)
            {
                add_exchange_variation($trans_no, $date_, $is_bank_to, $code_id,
                    $currency, $person_type_id, $person_id_new);
            }
        }
        // store tax details if the gl account is a tax account

        $amount = $amount_per_line;
        $ex_rate = get_exchange_rate_from_home_currency($currency, $date_);

        add_gl_tax_details($code_id, $trans_type, $trans_no, -$amount,
            $ex_rate, $date_, $memo_);
    }
    /*	$amount = $gl_item->amount;
        add_gl_tax_details($gl_item->code_id, $trans_type, $trans_no, -$amount,
            $ex_rate, $date_, $memo_);*/

    // do the source account postings
//    add_gl_trans($trans_type, $trans_no, $date_, $bank_gl_account, 0, 0, $memo_,
//        -$total, null, $person_type_id, $person_id_new,0,0,$cheque_no_line,$cheque_date_line);
    add_gl_trans($trans_type, $trans_no, $date_,$bank_gl_account,
        $dimension_id, $dimension2_id, $cf_ref,
        -$total, $currency, $person_type_id, $person_id_new,0,0,$cheque_no_line,$cheque_date_line,$month,$mod_payment);

    if ($do_exchange_variance)
    {
        if ($exchanged || add_exchange_variation($trans_no1, $date_, $from_account, $bank_gl_account,
                $currency, $person_type_id, $person_id_new))
        {
            $ref1 = $Refs->get_next(ST_JOURNAL);
            $Refs->save(ST_JOURNAL, $trans_no1, $ref1);
            add_audit_trail(ST_JOURNAL, $trans_no1, $date_);
        }
    }

    add_comments($trans_type, $trans_no, $date_, $memo_);
    $Refs->save($trans_type, $trans_no, $cf_ref);
    $Refs->save(44, $trans_no, $cf_ref);


    add_audit_trail($trans_type, $trans_no, $date_);

    // old transaction can be voided only after new transaction is entered,
    //  otherwise the operation could fail for cash accounts due to temporary negative balance
    if (@$old_trans)
        void_transaction($trans_type, $old_trans, Today(), _("Document reentered."));

    $args->trans_no = $trans_no;
    hook_db_postwrite($args, $trans_type);
    if ($use_transaction)
        commit_transaction();

    return array($trans_type, $trans_no);
}

function get_employee_account($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($selected_id);

    $result = db_query($sql,"could not get employee nomination");
    return db_fetch($result);
}
function get_employee_name_bulk($employee_id)
{
    $sql = "SELECT  emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_account_type_id($id)
{

    $sql = "SELECT account_type
      FROM ".TB_PREF."bank_accounts 
      WHERE id = ".db_escape($id);
    $result = db_query($sql,"an item could not be retreived");

    $row = db_fetch_row($result);
    return $row[0];
}