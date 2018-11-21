<?php
//----------------------------------------------------------------------------------------
// if ($writeoff_acc==0) return goods into $cart->Location
// if src_docs!=0 => credit invoice else credit note
//

function write_credit_note_voucher($debtor_trans_I, $debtor_trans_details_I,
         $trans_tax_details_I, $get_gl, $stock_moves, $memo_)
{
	global $Refs;
	$credit_date = sql2date($debtor_trans_I['tran_date']);
	begin_transaction();
	hook_db_prewrite($credit_note, ST_CUSTCREDIT);
    $reference = $Refs->get_next(ST_CUSTCREDIT, null, sql2date($credit_date));
    $credit_no = write_customer_trans(ST_CUSTCREDIT, 0, $debtor_trans_I['debtor_no'],
    $debtor_trans_I['branch_code'],$credit_date, $reference,
    $debtor_trans_I['ov_amount'], 0,  $debtor_trans_I['ov_gst'],
    $debtor_trans_I['ov_freight'], $debtor_trans_I['ov_freight_tax'],
    $debtor_trans_I['tpe'], $debtor_trans_I['order_no'], $debtor_trans_I['ship_via'],
    null,  $debtor_trans_I['alloc'], 0, $debtor_trans_I['dimension_id'], $debtor_trans_I['dimension2_id'],
    $debtor_trans_I['payment_terms'], $debtor_trans_I['tax_included'], $debtor_trans_I['prep_amount'],
    $debtor_trans_I['supply_disc'],$debtor_trans_I['service_disc'],
    $debtor_trans_I['fbr_disc'], $debtor_trans_I['srb_disc'], $debtor_trans_I['write_back'], $debtor_trans_I['supply_tax_id'], $debtor_trans_I['service_tax_id'],
    $debtor_trans_I['fbr_tax_id'], $debtor_trans_I['srb_tax_id'], $debtor_trans_I['fbr_tax_id'], $debtor_trans_I['discount1'],
    $debtor_trans_I['discount2'], $debtor_trans_I['cheque_date'], $debtor_trans_I['cheque_no'], $debtor_trans_I['text_1'],
    $debtor_trans_I['text_2'], $debtor_trans_I['text_3'], $debtor_trans_I['salesman'], $debtor_trans_I['h_amount1'],
    $debtor_trans_I['so_no'], $debtor_trans_I['cust_ref']);
	$total = 0;
    while($AddDTD = db_fetch($debtor_trans_details_I)) {
        write_customer_trans_detail_item(ST_CUSTCREDIT, $credit_no, $AddDTD['stock_id'],
        $AddDTD['description'], $AddDTD['quantity'],
        $AddDTD['unit_price'], $AddDTD['unit_tax'], $AddDTD['discount_percent'],
        $AddDTD['standard_cost'], $AddDTD['src_id'], 0/* for true condition */, $AddDTD['units_id'],
        $AddDTD['con_factor'], $AddDTD['text1'], $AddDTD['text2'], $AddDTD['text3'], $AddDTD['text4'],$AddDTD['text5'], $AddDTD['text6'],
        $AddDTD['amount1'], $AddDTD['amount2'], $AddDTD['amount3'], $AddDTD['amount4'],$AddDTD['amount5'], $AddDTD['amount6'],
        $AddDTD['date1'], $AddDTD['date2'], $AddDTD['date3'],
        $AddDTD['combo1'], $AddDTD['combo2'], $AddDTD['combo3'],$AddDTD['combo4'], $AddDTD['combo5'],
        $AddDTD['combo6'],$AddDTD['batch'],$AddDTD['item_location']);
    }
    while($AddSM = db_fetch($stock_moves)) {
        add_stock_move(ST_CUSTCREDIT,  $AddSM['stock_id'],
            $credit_no, $AddSM['loc_code'],
            sql2date($AddSM['tran_date']), $reference, $AddSM['qty'],
            $AddSM['standard_cost'], $AddSM['price'], $AddSM['person_id'],
            $AddSM['discount_percent'] , $AddSM['text1'],
            $AddSM['text2'],$AddSM['text3'],$AddSM['text4'],
            $AddSM['text5'],$AddSM['text6'],$AddSM['amount1'],
            $AddSM['amount2'],$AddSM['amount3'],$AddSM['amount4'],
            $AddSM['amount5'],$AddSM['amount6'], sql2date($AddSM['date1']),
            sql2date($AddSM['date2']), sql2date($AddSM['date3']),
            $AddSM['combo1'], $AddSM['combo2'], $AddSM['combo3'], $AddSM['combo4'],
            $AddSM['combo5'], $AddSM['combo6'],
            $AddSM['units_id'], $AddSM['con_factor'], $AddSM['batch'], $AddSM['loc_code'],
            $AddSM['to_stk_loc']);
    }
    while($AddGL = db_fetch($get_gl)) {
        $total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, sql2date($AddGL['tran_date']), $AddGL['account'], 0, 0,
            $AddGL['amount'], $AddGL['person_id'],
        "The total debtor GL posting for the credit note could not be inserted", 0, $AddGL['cheque_date'],
            $AddGL['cheque_no'], $AddGL['text_1'], $AddGL['text_2'], $AddGL['text_3']);
    }
    while($AddTT = db_fetch($trans_tax_details_I)) {
        add_trans_tax_details(ST_CUSTCREDIT, $credit_no, $AddTT['tax_type_id'],
            $AddTT['rate'], $AddTT['included_in_price'], $AddTT['amount'],
            $AddTT['net_amount'], $AddTT['ex_rate'],
            sql2date($AddTT['tran_date']), $reference);
    }
    inactive_gl(ST_CUSTCREDIT, $credit_no);
	add_comments(ST_CUSTCREDIT, $credit_no, $credit_date, $memo_);
    $Refs->save(ST_CUSTCREDIT, $credit_no, $reference);
	hook_db_postwrite($credit_note, ST_CUSTCREDIT);
	commit_transaction();
	return $credit_no;
}


//----------------------------------------------------------------------------------------
// Insert a stock movement coming back in to show the credit note and
// 	a reversing stock movement to show the write off
//

function add_credit_movements_item_voucher(&$credit_note, &$credit_line,
	$credit_type, $price, $credited_invoice=0)
{

    //Chaitanya : Stamp current cost in stock moves $credit_line does not fetch cost
    $curr_std_cost = get_standard_cost($credit_line->stock_id);

	if ($credit_type == "Return") {

		$reference = "Return ";
		if ($credited_invoice) {
			$reference .= "Ex Inv: " . $credited_invoice;
		}

	} elseif ($credit_type == "WriteOff") {

		$reference = "WriteOff ";
		if ($credited_invoice)
			$reference .= "Ex Inv: " . $credited_invoice;

   	 	add_stock_move_customer(ST_CUSTCREDIT,  $credit_line->stock_id,
        	key($credit_note->trans_no), $credit_note->Location,
        	$credit_note->document_date, $reference, -$credit_line->qty_dispatched,
        	$curr_std_cost,  0, $price,
        	$credit_line->discount_percent);

	}
    add_stock_move_customer(ST_CUSTCREDIT,  $credit_line->stock_id,
        key($credit_note->trans_no), $credit_note->Location,
        $credit_note->document_date, $reference, $credit_line->qty_dispatched,
        $curr_std_cost,  0, $price,$credit_line->discount_percent,
		$credit_line->color,$credit_line->cap,$credit_line->capcolor,$credit_line->packing);

}

//----------------------------------------------------------------------------------------

function add_gl_trans_credit_costs_voucher($order, $order_line, $credit_no, $date_,
	$credit_type, $write_off_gl_code, &$branch_data)
{
	$stock_gl_codes = get_stock_gl_code($order_line->stock_id);
	$customer = get_customer($order->customer_id);
	// 2008-08-01. If there is a Customer Dimension, then override with this,
	// else take the Item Dimension (if any)
	$dim = ($order->dimension_id != $customer['dimension_id'] ? $order->dimension_id : 
		($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_codes["dimension_id"]));
	$dim2 = ($order->dimension2_id != $customer['dimension2_id'] ? $order->dimension2_id : 
		($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_codes["dimension2_id"]));

	$total = 0;
	/* insert gl_trans to credit stock and debit cost of sales at standard cost*/
	$standard_cost = get_standard_cost($order_line->stock_id);
	if ($standard_cost != 0) {
		/*first the cost of sales entry*/

		$total += add_gl_trans_std_cost(ST_CUSTCREDIT, $credit_no, $date_, $stock_gl_codes["cogs_account"],
			$dim, $dim2, "", -($standard_cost * $order_line->qty_dispatched),
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
			"", ($standard_cost * $order_line->qty_dispatched),
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

		// 2008-06-14. If there is a Branch Sales Account, then override with this,
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

?>