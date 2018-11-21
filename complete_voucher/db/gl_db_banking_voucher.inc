<?php
function add_exchange_variation_voucher($trans_no, $date_, $acc_id, $account,
    $currency, $person_type_id=null, $person_id = "")
{
	if (is_company_currency($currency))
		return false;
	if ($date_ == null)
		$date_ = Today();
	$for_amount = 0;

	// We have to calculate all the currency accounts belonging to the GL account
	// upto $date_ and calculate with the exchange rates. And then compare with the GL account balance.
	// 2010-02-23 Joe Hunt with help of Ary Wibowo
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
	if ($diff != 0)
	{
		if ($trans_no == null)
			$trans_no = get_next_trans_no(ST_JOURNAL);
		if ($person_type_id == null)
			$person_type_id = PT_MISC;
		add_gl_trans(ST_JOURNAL, $trans_no, $date_, $account, 0, 0, _("Exchange Variance"),
    		-$diff, null, $person_type_id, $person_id,$cheque);
    	add_gl_trans(ST_JOURNAL, $trans_no, $date_, get_company_pref('exchange_diff_act'), 0, 0, 
    		_("Exchange Variance"),	$diff, null, $person_type_id, $person_id,$cheque);
	}
	return ($diff != 0);
}

function add_bank_transfer_voucher($bank_trans, $currency, $bank_gl_account, $memo_, $GetGl)
{
	global $Refs;
	
	begin_transaction();
	$args = func_get_args(); if (count($args) < 7) $args[] = 0;
	hook_db_prewrite($args, ST_BANKTRANSFER);
//    $ref = $Refs->get_next(ST_BANKTRANSFER);

	$trans_type = ST_BANKTRANSFER;
	$trans_no = get_next_trans_no($trans_type);
	$total = 0;
//	Add GL
//  Data fetch from company 0 and insert in 1 company
    while($AddGL = db_fetch($GetGl))
    {
        $total += add_gl_trans($trans_type, $trans_no, sql2date($AddGL['tran_date']), $AddGL['account'], 0, 0, $AddGL['person_id'],
            $AddGL['amount'], $currency['bank_curr_code']);
        inactive_gl(ST_BANKTRANSFER, $AddGL['type_no']);
    }

//  Add Bank Transaction
//  Data fetch from company 0 and insert in 1 company
    while($AddBT = db_fetch($bank_trans))
    {
        $ref = $Refs->get_next(ST_BANKTRANSFER, null, sql2date($AddBT['trans_date']));
        add_bank_trans($trans_type, $trans_no, $AddBT['bank_act'], $ref,
            sql2date($AddBT['trans_date']), $AddBT['amount'],
            $AddBT['person_type_id'], $AddBT['person_id'], $currency['bank_curr_code'],
            "Cannot insert a source bank transaction");
        $date_ = $AddBT['trans_date'];
    }
	add_comments($trans_type, $trans_no, $date_, $memo_);
	$Refs->save($trans_type, $trans_no, $ref);
	add_audit_trail($trans_type, $trans_no, $date_);
	hook_db_postwrite($args, ST_BANKTRANSFER);
	commit_transaction();
	return $trans_no;
}
function get_vouchers_from_bank_transfer($trans_type, $trans_no)
{
    $sql = "SELECT * FROM 0_bank_trans
			WHERE type = " . db_escape($trans_type) ."
			AND trans_no = " . db_escape($trans_no);

    $result = db_query($sql, "Error");
    return db_fetch($result);
}
function get_bank_account_name_voucher($id)
{
    $sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id = ".db_escape($id);
    $result = db_query($sql, "could not get allowance");
    $row = db_fetch($result);
    return $row[0];
}
?>