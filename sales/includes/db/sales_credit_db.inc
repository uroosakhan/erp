<?php

//----------------------------------------------------------------------------------------
// if ($writeoff_acc==0) return goods into $cart->Location
// if src_docs!=0 => credit invoice else credit note
//
function write_credit_note(&$credit_note, $write_off_acc)
{
	global $Refs, $db_connections;

	if (is_array($credit_note->src_docs))
	{
		$docs = array_keys($credit_note->src_docs);
		$credit_invoice = reset($docs);
	}
	else
		$credit_invoice = $credit_note->src_docs;

	$credit_date = $credit_note->document_date;
	$tax_group_id = $credit_note->tax_group_id;

	$trans_no = $credit_note->trans_no;
	if (is_array($trans_no)) {
		$trans_no = key($trans_no);
	}

	$credit_type = $write_off_acc == 0 ? 'Return' : 'WriteOff';

	begin_transaction();
	hook_db_prewrite($credit_note, ST_CUSTCREDIT);

	$company_data = get_company_prefs();
	
//	S.H
	$GetShipper = get_shipper_for_invoice($credit_note->ship_via);

	$branch_data = get_branch_accounts($credit_note->Branch);

	add_new_exchange_rate(get_customer_currency($credit_note->customer_id), $credit_date, $credit_note->ex_rate);

	$credit_note_total = $credit_note->get_items_total_dispatch();
	$freight_tax = $credit_note->get_shipping_tax();

	$taxes = $credit_note->get_taxes();

	$tax_total = 0;
	foreach ($taxes as $taxitem) {
		$taxitem['Value'] =  round2($taxitem['Value'], user_price_dec());
		$tax_total += $taxitem['Value'];
	}

	if ($credit_note->tax_included == 0) {
	    $items_added_tax = $tax_total-$freight_tax;
	    $freight_added_tax = $freight_tax;
	} else {
	    $items_added_tax = 0;
	    $freight_added_tax = 0;
	}
	// If the Customer Branch AR Account is set to a Bank Account,
	// the transaction will be settled at once.
	if (is_bank_account($branch_data['receivables_account']))
		$alloc = $credit_note_total + $items_added_tax + $credit_note->freight_cost + $freight_added_tax;
	else
		$alloc = 0;

	if (!isset($credit_note->order_no))
		$credit_note->order_no = 0;

	/*Now insert the Credit Note into the debtor_trans table with the allocations as calculated above*/
	// all amounts in debtor's currency
	$credit_no = write_customer_trans(ST_CUSTCREDIT, $trans_no, $credit_note->customer_id,
		$credit_note->Branch, $credit_date, $credit_note->reference,
		$credit_note_total, 0, $items_added_tax,
		$credit_note->freight_cost, $freight_added_tax,
		$credit_note->sales_type, $credit_note->order_no, $credit_note->ship_via,
		null, $alloc, 0, $credit_note->dimension_id, $credit_note->dimension2_id,
		$credit_note->payment, $credit_note->tax_included, $credit_note->prep_amount,
        0,0,0,0,0,0,0,0,0,0,$credit_note->discount1, $credit_note->discount2,0,0,0,0,0,$credit_note->salesman);

    //SMS Configure on for DMNWS (S.H.G)
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS')
    {
        $path_to_root = "..";
        include_once($path_to_root . "/includes/ui/sms_send.inc");
        $outstanding = get_customer_details_balance_sms($credit_note->customer_id, $credit_note->customer_id);
        $balance = $outstanding["Balance"];
        $original = array('amount', 'outstanding');
        $replaced = array($credit_note_total, $balance);
        $GetTemplate = get_select_template_sms(ST_CUSTCREDIT);
        $Message = $GetTemplate['template'];
        $sentence = str_replace($original, $replaced, $Message);
        $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
            SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($credit_note->customer_id).") );";
        $query = db_query($sql, "Error");
        $fetch = db_fetch($query);
        if($company_data['auto_send_sms'] == 1) // enable from Company Setup
            sms_send($fetch['phone'], htmlspecialchars_decode($sentence));
    }
//========================================================================




	if ($trans_no==0) {
		$credit_note->trans_no = array($credit_no=>0);
	} else {
		delete_comments(ST_CUSTCREDIT, $credit_no);
		void_cust_allocations(ST_CUSTCREDIT, $credit_no, $credit_date);
		void_gl_trans(ST_CUSTCREDIT, $credit_no, true);
		void_stock_move(ST_CUSTCREDIT, $credit_no);
		void_trans_tax_details(ST_CUSTCREDIT, $credit_no);
	}
	if ($credit_invoice) {
		update_customer_trans_version(get_parent_type(ST_CUSTCREDIT), $credit_note->src_docs );

		$total = $credit_note_total + $credit_note->freight_cost + $items_added_tax 
			+ $freight_added_tax;

		if (!credit_sales_invoice_allocate($credit_invoice, $credit_no, $total, $credit_date))
			return false;
	}

	$total = 0;
	foreach ($credit_note->line_items as $credit_line) {

		if ($credit_invoice && $credit_line->qty_dispatched!=$credit_line->qty_old ) {
			update_parent_line(ST_CUSTCREDIT, $credit_line->src_id,($credit_line->qty_dispatched
					-$credit_line->qty_old));
		}

		$line_taxfree_price = get_tax_free_price_for_item($credit_line->stock_id, $credit_line->price,
		  0, $credit_note->tax_included, $credit_note->tax_group_array);

		$line_tax = get_full_price_for_item($credit_line->stock_id, $credit_line->price,
		  0, $credit_note->tax_included, $credit_note->tax_group_array) - $line_taxfree_price;

		//-------------conversion factor----------------------------------------------------------
		$item=get_item($credit_line->stock_id);
		if($credit_line->units_id !=  $item['units'])
		{
			if($item['con_type'] == 0)
			{
				$qty_dispatched = $credit_line->qty_dispatched / $credit_line->con_factor;
			}
			else
			{
				$qty_dispatched =$credit_line->con_factor / $credit_line->qty_dispatched  ;
			}
		}
		else{
			$qty_dispatched = $credit_line->qty_dispatched;
		}
		$pref=get_company_prefs();
		$myrow_factors= get_company_item_pref('con_factor');
		if($myrow_factors['sale_enable'] == 0 && $myrow_factors['label_value'] ){
			$item_info = get_item_edit_info($credit_line->stock_id);
			$credit_line->con_factor  = $item_info['con_factor'];

		}
		else{
			$credit_line->con_factor;
		}
		//--------------------------   end -------------------------------------------
		write_customer_trans_detail_item(ST_CUSTCREDIT, $credit_no, $credit_line->stock_id,
			$credit_line->item_description, $qty_dispatched,
			$credit_line->line_price(), $line_tax, $credit_line->discount_percent,
			$credit_line->standard_cost, $credit_line->src_id, $trans_no==0 ? 0:  $credit_line->id,$credit_line->units_id,
			$credit_line->con_factor, $credit_line->text1,$credit_line->text2,
			$credit_line->text3,$credit_line->text4,$credit_line->text5,$credit_line->text6,
			$credit_line->amount1,$credit_line->amount2,
			$credit_line->amount3,$credit_line->amount4,$credit_line->amount5,$credit_line->amount6,
			$credit_line->date1,$credit_line->date2,
			$credit_line->date3,
			$credit_line->combo1,$credit_line->combo2,
			$credit_line->combo3,$credit_line->combo4,$credit_line->combo5,
			$credit_line->combo6,$credit_line->batch);


		if ($credit_type == 'Return')
			add_credit_movements_item($credit_note, $credit_line,
				$credit_type, ($line_taxfree_price+$line_tax)*(1-$credit_line->discount_percent), $credit_invoice,$credit_line->person_id,$credit_line->discount_pecent,
				$credit_line->text1,$credit_line->text2,
				$credit_line->text3,$credit_line->text4,$credit_line->text5,$credit_line->text6,
				$credit_line->amount1,$credit_line->amount2,
				$credit_line->amount3,$credit_line->amount4,$credit_line->amount5,$credit_line->amount6,
				$credit_line->date1,$credit_line->date2,
				$credit_line->date3,
				$credit_line->combo1,$credit_line->combo2,
				$credit_line->combo3,$credit_line->combo4,$credit_line->combo5,
				$credit_line->combo6,$credit_line->batch,$credit_line->units_id,$credit_line->con_factor,$qty_dispatched);

		$total += add_gl_trans_credit_costs($credit_note, $credit_line, $credit_no,
		    $credit_date, $credit_type, $write_off_acc, $branch_data);
	} /*end of credit_line loop */

	/*Post credit note transaction to GL credit debtors,
	debit freight re-charged and debit sales */

	if (($credit_note_total + $credit_note->freight_cost) != 0) {

		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $branch_data["receivables_account"], 0, 0,
			-($credit_note_total + $credit_note->freight_cost + $items_added_tax + $freight_added_tax),
			$credit_note->customer_id,
			"The total debtor GL posting for the credit note could not be inserted");
	}

	if ($credit_note->freight_cost != 0) {
		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date,  /*$company_data["freight_act"]*/$GetShipper['shipping_account'], 0, 0,
			$credit_note->get_tax_free_shipping(), $credit_note->customer_id,
			"The freight GL posting for this credit note could not be inserted");
	}

	foreach ($taxes as $taxitem) {
		if ($taxitem['Net'] != 0) {

			$ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($credit_note->customer_id), $credit_note->document_date);
			add_trans_tax_details(ST_CUSTCREDIT, $credit_no, $taxitem['tax_type_id'],
				$taxitem['rate'], $credit_note->tax_included, $taxitem['Value'],
				$taxitem['Net'], $ex_rate,
				$credit_note->document_date, $credit_note->reference, TR_OUTPUT);

			$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $taxitem['sales_gl_code'], 0, 0,
				$taxitem['Value'], $credit_note->customer_id,
				"A tax GL posting for this credit note could not be inserted");
		}
	}
	/*Post a balance post if $total != 0 */
	add_gl_balance(ST_CUSTCREDIT, $credit_no, $credit_date, -$total, PT_CUSTOMER, $credit_note->customer_id);	

	add_comments(ST_CUSTCREDIT, $credit_no, $credit_date, $credit_note->Comments);

	if ($trans_no == 0) {
		$Refs->save(ST_CUSTCREDIT, $credit_no, $credit_note->reference);
	}
	hook_db_postwrite($credit_note, ST_CUSTCREDIT);
	commit_transaction();

	return $credit_no;
}

//----------------------------------------------------------------------------------------
// Insert a stock movement coming back in to show the credit note and
// 	a reversing stock movement to show the write off
//
function add_credit_movements_item(&$credit_note, &$credit_line,
	$credit_type, $price, $credited_invoice=0,$person_id,$discount_percent,
								   $text1, $text2, $text3, $text4, $text5, $text6,
								   $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
								   $date1, $date2, $date3,
								   $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$batch,$units_id,$con_factor,$qty_dispatched)
{
    //Chaitanya : Stamp current cost in stock moves $credit_line does not fetch cost
    $curr_std_cost = get_unit_cost($credit_line->stock_id);

	$reference = _("Return");
	if ($credited_invoice)
	//{
		$reference .= ' '._("Ex Inv:").' ' . $credited_invoice;

	    add_stock_move(ST_CUSTCREDIT,  $credit_line->stock_id,
   		    key($credit_note->trans_no), $credit_note->Location,
       		$credit_note->document_date, $reference, $qty_dispatched,
	        $curr_std_cost, $price,$person_id,$discount_percent,$text1, $text2, $text3, $text4, $text5, $text6,
								   $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
								   $date1, $date2, $date3,
								   $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$units_id,$con_factor,$batch);
	//}//ansar 26-08-17
}

//----------------------------------------------------------------------------------------

function add_gl_trans_credit_costs($order, $order_line, $credit_no, $date_,
	$credit_type, $write_off_gl_code, &$branch_data)
{
	$stock_gl_codes = get_stock_gl_code($order_line->stock_id);
	$customer = get_customer($order->customer_id);
	// If there is a Customer Dimension, then override with this,
	// else take the Item Dimension (if any)
	$dim = ($order->dimension_id != $customer['dimension_id'] ? $order->dimension_id : 
		($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_codes["dimension_id"]));
	$dim2 = ($order->dimension2_id != $customer['dimension2_id'] ? $order->dimension2_id : 
		($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_codes["dimension2_id"]));

	$total = 0;
	/* insert gl_trans to credit stock and debit cost of sales at standard cost*/
	$unit_cost = get_unit_cost($order_line->stock_id);
	if ($unit_cost != 0) {
		/*first the cost of sales entry*/

		$total += add_gl_trans_std_cost(ST_CUSTCREDIT, $credit_no, $date_, $stock_gl_codes["cogs_account"],
			$dim, $dim2, "", -($unit_cost * $order_line->qty_dispatched),
			PT_CUSTOMER, $order->customer_id,
			"The cost of sales GL posting could not be inserted");

		/*now the stock entry*/
		if ($credit_type == "WriteOff") {
			$stock_entry_account = $write_off_gl_code;
		} else {
			$stock_gl_code = get_stock_gl_code($order_line->stock_id);
			$stock_entry_account = $stock_gl_code["inventory_account"];
		}

		$total += add_gl_trans_std_cost(ST_CUSTCREDIT, $credit_no, $date_, $stock_entry_account, 0, 0,
			"", ($unit_cost * $order_line->qty_dispatched),
			PT_CUSTOMER, $order->customer_id,
			"The stock side (or write off) of the cost of sales GL posting could not be inserted");

	} /* end of if GL and stock integrated and standard cost !=0 */

	if ($order_line->line_price() != 0) {

		$line_taxfree_price =
		get_tax_free_price_for_item($order_line->stock_id, $order_line->price,
		  0, $order->tax_included, $order->tax_group_array);

		$line_tax = get_full_price_for_item($order_line->stock_id, $order_line->price,
		  0, $order->tax_included, $order->tax_group_array) - $line_taxfree_price;


		//Post sales transaction to GL credit sales

		// If there is a Branch Sales Account, then override with this,
		// else take the Item Sales Account
		if ($branch_data['sales_account'] != "")
			$sales_account = $branch_data['sales_account'];
		else
			$sales_account = $stock_gl_codes['sales_account'];
		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $date_, $sales_account, $dim, $dim2,
			($line_taxfree_price * $order_line->qty_dispatched), $order->customer_id,
			"The credit note GL posting could not be inserted");

		if ($order_line->discount_percent != 0) {

			$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $date_, $branch_data["sales_discount_account"],
				$dim, $dim2, -($line_taxfree_price * $order_line->qty_dispatched * $order_line->discount_percent),
				$order->customer_id,
				"The credit note discount GL posting could not be inserted");

		} /*end of if discount !=0 */
	} /*if line_price!=0 */
	return $total;
}

