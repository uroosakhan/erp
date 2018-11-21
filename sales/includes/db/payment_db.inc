<?php

/*
  Write/update customer payment.

  Warning: $rate is leaved here for extensions compatibility reasons, will be removed in 2.4
	since 2.3.17 is not used: use $bank_amount instead.

	$amount - in customer currency (ex. discount)
	$discount - in customer currency
	$bank_amount - in bank currency (before charge)
	$charge - in bank currency
*/
function get_wht_account_code($code)
{
	$sql = "SELECT co_account FROM  ".TB_PREF."wth_tax_types
     WHERE id=".db_escape($code);

	$result = db_query($sql,"could not get wht account.");
	$myrow = db_fetch($result);
	return $myrow["co_account"];
}
function get_wht_account_code_for_supplier($code)
{
	$sql = "SELECT co_account_supplier FROM  ".TB_PREF."wth_tax_types
     WHERE id=".db_escape($code);

	$result = db_query($sql,"could not get wht account.");
	$myrow = db_fetch($result);
	return $myrow["co_account_supplier"];
}
//Ryan :06-05-17
function write_customer_payment($trans_no, $customer_id, $branch_id, $bank_account,
	$date_, $ref, $amount, $discount,$dimension_id, $dimension2_id, $memo_, $rate=0, $charge=0, $bank_amount=0 , $gst_wh,$supply_tax_id=0, $service_tax_id=0, $fbr_tax_id=0, $srb_tax_id=0, $supply_disc=0, $service_disc=0,
       $fbr_disc=0, $srb_disc=0, $write_back=0,$cheque_date=0, $cheque_no=0,
		$text_1=0,$text_2=0,$text_3=0,$so_no)
{ 
	global $Refs, $db_connections;



	begin_transaction();
	$args = func_get_args(); while (count($args) < 12) $args[] = 0;
	$args = (object)array_combine(array('trans_no', 'customer_id', 'branch_id', 'bank_account', 
		'date_', 'ref', 'amount', 'discount', 'memo_','rate','charge', 'bank_amount'), $args);
	hook_db_prewrite($args, ST_CUSTPAYMENT);

	$company_record = get_company_prefs();

	if ($trans_no != 0) {
	  	delete_comments(ST_CUSTPAYMENT, $trans_no);
	  	void_bank_trans(ST_CUSTPAYMENT, $trans_no, true);
	  	void_gl_trans(ST_CUSTPAYMENT, $trans_no, true);
	  	void_cust_allocations(ST_CUSTPAYMENT, $trans_no, $date_);
	}

	$bank = get_bank_account($bank_account);

	if (!$bank_amount) 	// backward compatibility workaround
	{
		if(!$rate)
			$rate = get_exchange_rate_from_to(get_customer_currency($customer_id),
				$bank['bank_curr_code'], $date_ );

		$bank_amount = $amount/$rate;
	}

	// do not use $rate here: global rate stored in exrate table is always used
	//Ryan :06-05-17
	$payment_no = write_customer_trans(ST_CUSTPAYMENT, $trans_no, $customer_id, $branch_id,
	$date_, $ref, $amount, $discount,0,0,0,0,0,0,0,0,0,$dimension_id,$dimension2_id,0,0,0, $supply_disc, $service_disc, $fbr_disc, $srb_disc, $write_back,
	$supply_tax_id, $service_tax_id, $fbr_tax_id, $srb_tax_id,0,0,0,$cheque_date,
	$cheque_no,$text_1,$text_2,$text_3,0,0,$so_no);
	
	
	//SMS Configure for DMNWS (S.H.G)
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS')
    {
        $path_to_root = "..";
        include_once($path_to_root . "/includes/ui/sms_send.inc");
        $outstanding = get_customer_details_balance_sms($customer_id, $customer_id);
        $balance = $outstanding["Balance"];
        $original = array('amount', 'outstanding');
        $replaced = array($amount,  $balance);
        $GetTemplate = get_select_template_sms(ST_CUSTPAYMENT);
        $Message = $GetTemplate['template'];
        $sentence = str_replace($original, $replaced, $Message);
        $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
            SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($customer_id).") );";
        $query = db_query($sql, "Error");
        $fetch = db_fetch($query);
        if($company_record['auto_send_sms'] == 1) // enable from Company Setup
            sms_send($fetch['phone'], htmlspecialchars_decode($sentence));
    }
//========================================================================

	
	

	$bank_gl_account = get_bank_gl_account($bank_account);

	$total = 0;

	/* Bank account entry first */
	$total += add_gl_trans(ST_CUSTPAYMENT, $payment_no, $date_,
		$bank_gl_account, 0, 0, '', ($bank_amount - $charge),  $bank['bank_curr_code'],
		PT_CUSTOMER, $customer_id,0,0,0,$cheque_date,
		$cheque_no,$text_1,$text_2,$text_3);

	if ($branch_id != ANY_NUMERIC) {

		$branch_data = get_branch_accounts($branch_id);

		$debtors_account = $branch_data["receivables_account"];
		$discount_account = $branch_data["payment_discount_account"];

	} else {
		$debtors_account = $company_record["debtors_act"];
		$discount_account = $company_record["default_prompt_payment_act"];
	}
	$gst_wh_account = $company_record["bank_charge_act"];
	$write_back_account = $company_record["write_back_act"];

	if (($discount + $amount + $gst_wh + $supply_disc + $service_disc + $fbr_disc + $srb_disc) != 0)	{
	/* Now Credit Debtors account with receipts + discounts */
		$tot  = round($discount + $amount + $gst_wh + $supply_disc + $service_disc + $fbr_disc + $srb_disc - $write_back, user_price_dec());
		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
		$debtors_account, 0, 0, -($tot), $customer_id,
		"Cannot insert a GL transaction for the debtors account credit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}
	if ($discount != 0)	{
		/* Now Debit discount account with discounts allowed*/
		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$discount_account, 0, 0, $discount, $customer_id,
			"Cannot insert a GL transaction for the payment discount debit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}

	if ($charge != 0)	{
		/* Now Debit bank charge account with charges */
		$charge_act = get_bank_charge_account($bank_account);
		$total += add_gl_trans(ST_CUSTPAYMENT, $payment_no, $date_,	$charge_act, 0, 0, '', 
			$charge, $bank['bank_curr_code'], PT_CUSTOMER,  $customer_id,0,0,0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}
//Ryan :06-05-17
	if ($supply_disc != 0)	{



		$supply_acc = get_wht_account_code($supply_tax_id);

		/* Now Debit discount account with Supply discounts allowed*/
		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$supply_acc, 0, 0, $supply_disc, $customer_id,
			"Cannot insert a GL transaction for the payment discount debit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);

	}


	if ($service_disc != 0)	{
		/* Now Debit discount account with Service discounts allowed*/
		$service_acc = get_wht_account_code($service_tax_id);
		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$service_acc, 0, 0, $service_disc, $customer_id,
			"Cannot insert a GL transaction for the payment discount debit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}

	if ($fbr_disc != 0)	{
		/* Now Debit discount account with FBR discounts allowed*/
		$fbr_acc = get_wht_account_code($fbr_tax_id);

		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$fbr_acc, 0, 0, $fbr_disc, $customer_id,
			"Cannot insert a GL transaction for the payment discount debit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);

	}

	if ($srb_disc != 0)	{
		/* Now Debit discount account with SRB Discount allowed*/
		$srb_acc = get_wht_account_code($srb_tax_id);

		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$srb_acc, 0, 0, $srb_disc, $customer_id,
			"Cannot insert a GL transaction for the payment discount debit",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}

	if($write_back != 0){
		$total += add_gl_trans_customer(ST_CUSTPAYMENT, $payment_no, $date_,
			$write_back_account, 0, 0, -($write_back), $customer_id,
			"Cannot insert a GL transaction for the GST WH",0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);


	}

//---------------end---------------------------
	/*Post a balance post if $total != 0 due to variance in AR and bank posted values*/
	if ($total != 0)
	{
		$variance_act = get_company_pref('exchange_diff_act');
		add_gl_trans(ST_CUSTPAYMENT, $payment_no, $date_,	$variance_act, 0, 0, '',
			-$total, null, PT_CUSTOMER,  $customer_id,0,0,0,$cheque_date,
			$cheque_no,$text_1,$text_2,$text_3);
	}

	/*now enter the bank_trans entry */

    add_bank_trans(ST_CUSTPAYMENT, $payment_no, $bank_account, $ref,
        $date_, $bank_amount - $charge, PT_CUSTOMER, $customer_id,0,0,0,$cheque_no,$cheque_date,
        $text_1,$text_2,$text_3,0,0,0,0,0,0,0,$payment_no,ST_CUSTPAYMENT);

	add_comments(ST_CUSTPAYMENT, $payment_no, $date_, $memo_);

	$Refs->save(ST_CUSTPAYMENT, $payment_no, $ref);

	$args->trans_no = $payment_no;
	hook_db_postwrite($args, ST_CUSTPAYMENT);
	commit_transaction();

	return $payment_no;
}

//-------------------------------------------------------------------------------------------------

function void_customer_payment($type, $type_no)
{
	begin_transaction();

	hook_db_prevoid($type, $type_no);
	void_bank_trans($type, $type_no, true);
	void_gl_trans($type, $type_no, true);
	void_cust_allocations($type, $type_no);
	void_customer_trans($type, $type_no);

	commit_transaction();
}

/*
	Retrieve bank charge amount from GL postings for customer payment.
	. Bank charge is not stored explicitly in database as of 2.3.xx
	. Due to roundings the retrieved charge can differ from the original amount when bank_curr!=home_curr && bank_curr!=cust_curr
*/
function get_cust_bank_charge($type, $trans_no)
{

	// restore charge amount from amounts in bank currency if possible, otherwise calculate from GL posting with exchange rate used for amount posting
	$sql = "SELECT	IF(act.bank_curr_code=home_curr.value, charge.amount,
					IF(act.bank_curr_code=debtor.curr_code, -(trans.amount-ar.ov_amount+ar.ov_discount),
					IFNULL(charge.amount*trans.amount/pmt.amount, 0)))
			FROM ".TB_PREF."bank_trans trans
				LEFT JOIN ".TB_PREF."bank_accounts act ON trans.bank_act=act.id
				LEFT JOIN ".TB_PREF."sys_prefs charge_act ON charge_act.name='bank_charge_act'
				LEFT JOIN ".TB_PREF."sys_prefs home_curr ON home_curr.name='curr_default'
				LEFT JOIN ".TB_PREF."gl_trans charge ON charge.type=trans.type AND charge.type_no=trans.trans_no AND charge.account=charge_act.value
				LEFT JOIN ".TB_PREF."gl_trans pmt ON pmt.type=trans.type AND pmt.type_no=trans.trans_no AND pmt.account=act.account_code
				LEFT JOIN ".TB_PREF."debtors_master debtor ON trans.person_id=debtor.debtor_no AND trans.person_type_id=".PT_CUSTOMER."
				LEFT JOIN ".TB_PREF."debtor_trans ar ON trans.type=ar.type AND trans.trans_no=ar.trans_no
			WHERE pmt.amount!=0 AND charge.amount!=0 AND trans.amount!=0
				AND trans.type=".db_escape($type)." AND trans.trans_no=".db_escape($trans_no);

	$result = db_query($sql, "cannot retrieve bank charge");

	if (!db_num_rows($result))
		return 0;

	$myrow = db_fetch($result);
	return $myrow['0'];
}

