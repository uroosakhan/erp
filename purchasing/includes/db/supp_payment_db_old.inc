<?php

function write_supp_payment($trans_no, $supplier_id, $bank_account,
	$date_, $ref, $supp_amount, $supp_discount, $memo_, $bank_charge=0, $bank_amount=0, $gst_wh,$supply_tax_id=0
	, $service_tax_id=0, $fbr_tax_id=0, $srb_tax_id=0, $supply_disc=0, $service_disc=0,
							$fbr_disc=0, $srb_disc=0, $write_back=0,$cheque,$cheque_date,$text_1,$text_2,$text_3)
{
	
	global $Refs;

	begin_transaction();
	$args = func_get_args(); while (count($args) < 10) $args[] = 0;
	$args = (object)array_combine(array('trans_no', 'supplier_id', 'bank_account', 'date_',
		 'ref', 'bank_amount', 'supp_amount', 'supp_discount', 'memo_', 'bank_charge'), $args);
	$args->trans_no = 0;
	hook_db_prewrite( $args, ST_SUPPAYMENT);

	if ($trans_no != 0) {
	  delete_comments(ST_SUPPAYMENT, $trans_no);
	  void_bank_trans(ST_SUPPAYMENT, $trans_no, true);
	  void_gl_trans(ST_SUPPAYMENT, $trans_no, true);
	  void_cust_allocations(ST_SUPPAYMENT, $trans_no, $date_);
	}
    $bank = get_bank_account($bank_account);
    if (!$bank_amount) 	// auto
    {
        $rate = get_exchange_rate_from_to(get_supplier_currency($supplier_id),
            $bank['bank_curr_code'], $date_ );
        $bank_amount = $supp_amount/$rate;
    }
    if ($bank['bank_curr_code'] == get_company_currency()) // [0002506]
        $rate = $bank_amount/$supp_amount;
    else
        $rate = 0;

	/* Create a supp_trans entry for the supplier payment */
	$payment_id = write_supp_trans(ST_SUPPAYMENT, 0, $supplier_id, $date_, $date_,
		$ref, "", -$supp_amount, 0, -$supp_discount,0,0,0, $supply_disc, $service_disc, $fbr_disc, $srb_disc, $write_back,
		$supply_tax_id, $service_tax_id, $fbr_tax_id, $srb_tax_id,$cheque,$cheque_date,$text_1,$text_2,$text_3);

	// Now debit creditors account with payment + discount

	$total = 0;
    $supplier_accounts = get_supplier_accounts($supplier_id);
	$total += add_gl_trans_supplier(ST_SUPPAYMENT, $payment_id, $date_, $supplier_accounts["payable_account"], 0, 0,
		$supp_amount + $supp_discount, $supplier_id,0,0,0,$cheque_date,$text_1,$text_2,$text_3,$cheque);

	// Now credit discount received account with discounts
	if ($supp_discount != 0)
	{
		$total += add_gl_trans_supplier(ST_SUPPAYMENT, $payment_id, $date_, $supplier_accounts["payment_discount_account"], 0, 0,
			-$supp_discount, $supplier_id,0,0,0,$cheque_date,$text_1,$text_2,$text_3,$cheque);
	}

	$bank = get_bank_account($bank_account);

	if ($bank_charge != 0)
	{
		$charge_act = get_company_pref('bank_charge_act');
		$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_, $charge_act, 0, 0, '',
			$bank_charge, $bank['bank_curr_code'], PT_SUPPLIER, $supplier_id, "", $rate,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);
	}
	if ($supply_disc != 0)	{



		$supply_acc = get_wht_account_code($supply_tax_id);

		/* Now Debit discount account with Supply discounts allowed*/
		$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_,
			$supply_acc, 0, 0, '',$supply_disc, $supplier_id, "", $rate,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);

	}


	if ($service_disc != 0)	{
		/* Now Debit discount account with Service discounts allowed*/
		$service_acc = get_wht_account_code($service_tax_id);
		$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_,
			$service_acc, 0, 0,'', $service_disc, $supplier_id, "", $rate,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);
	}

	if ($fbr_disc != 0)	{
		/* Now Debit discount account with FBR discounts allowed*/
		$fbr_acc = get_wht_account_code($fbr_tax_id);

		$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_,
			$fbr_acc, 0, 0,'', $fbr_disc, $supplier_id,
			"Cannot insert a GL transaction for the payment discount debit",0,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);

	}

	if ($srb_disc != 0) {
		/* Now Debit discount account with SRB Discount allowed*/
		$srb_acc = get_wht_account_code($srb_tax_id);

		$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_,
			$srb_acc, 0, 0,'', $srb_disc, $supplier_id,
			"Cannot insert a GL transaction for the payment discount debit");
	}

	$total += add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_, $bank['account_code'], 0, 0, '',
		-($bank_amount + $bank_charge), $bank['bank_curr_code'], PT_SUPPLIER, $supplier_id,0,0,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);

	/*Post a balance post if $total != 0 due to variance in AP and bank posted values*/
	if ($total != 0)
	{
		$variance_act = get_company_pref('exchange_diff_act');
		add_gl_trans(ST_SUPPAYMENT, $payment_id, $date_, $variance_act, 0, 0, '',
			-$total, null, PT_SUPPLIER,  $supplier_id,0,0,0,$cheque_date,$cheque,$text_1,$text_2,$text_3);
	}

   /*now enter the bank_trans entry */
	add_bank_trans(ST_SUPPAYMENT, $payment_id, $bank_account, $ref,
		$date_, -($bank_amount + $bank_charge), PT_SUPPLIER, $supplier_id,0,0,0,$cheque,$cheque_date,$text_1,$text_2,$text_3);

	add_comments(ST_SUPPAYMENT, $payment_id, $date_, $memo_);

	$Refs->save(ST_SUPPAYMENT, $payment_id, $ref);

	$args->trans_no = $payment_id;
	hook_db_postwrite($args, ST_SUPPAYMENT);

	commit_transaction();

	return $payment_id;
}

//------------------------------------------------------------------------------------------------

function void_supp_payment($type, $type_no)
{
	begin_transaction();
	hook_db_prevoid($type, $type_no);

	void_bank_trans($type, $type_no, true);
	void_gl_trans($type, $type_no, true);
	void_supp_allocations($type, $type_no);
	void_supp_trans($type, $type_no);

	commit_transaction();
}


