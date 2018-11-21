<?php
//-----------------------------------------------------------------------------
//	Add or update Sales Invoice
//
function write_sales_invoice_voucher($sales_order_I, $sales_order_details_I, $debtor_trans_I, $debtor_trans_details_I, $trans_tax_details_I, $get_gl, $order_no)
{
	global $Refs;

	$trans_no = 0;
	if (is_array($trans_no))
		$trans_no = key($trans_no);

	$date_ = sql2date($debtor_trans_I['tran_date']);
	$due_date = sql2date($debtor_trans_I['due_date']);
    if($debtor_trans_I['reference'] == 'auto')
        $reference = 'auto';
    else
        $reference = $Refs->get_next(ST_SALESINVOICE, null, array('date' => $debtor_trans_I['tran_date'],
            'customer' => $debtor_trans_I['debtor_no'], 'branch' => $debtor_trans_I['branch_code']));
    begin_transaction();

	hook_db_prewrite($invoice, ST_SALESINVOICE);
	$items_total = $debtor_trans_I["ov_amount"];

 	if ($trans_no != 0) {
         delete_comments(ST_SALESINVOICE, $trans_no);
         void_gl_trans(ST_SALESINVOICE, $trans_no, true);
         void_cust_allocations(ST_SALESINVOICE, $trans_no); // ?
         void_trans_tax_details(ST_SALESINVOICE, $trans_no);
    } 
    $invoice_no = write_customer_trans(ST_SALESINVOICE, $trans_no, $debtor_trans_I["debtor_no"],
	$debtor_trans_I["branch_code"], $date_, $reference, $items_total, 0,
	$debtor_trans_I['ov_gst'], $debtor_trans_I['ov_freight'], $debtor_trans_I['ov_freight_tax'],
	$debtor_trans_I['tpe'], $order_no, $debtor_trans_I['ship_via'],
	$due_date, 0, 0, $debtor_trans_I['dimension_id'],
	$debtor_trans_I['dimension2_id'], $debtor_trans_I['payment_terms'], $debtor_trans_I['tax_included'], $debtor_trans_I['prep_amount'],
    $debtor_trans_I['supply_disc'],$debtor_trans_I['service_disc'],
    $debtor_trans_I['fbr_disc'], $debtor_trans_I['srb_disc'], $debtor_trans_I['write_back'], $debtor_trans_I['supply_tax_id'], $debtor_trans_I['service_tax_id'],
    $debtor_trans_I['fbr_tax_id'], $debtor_trans_I['srb_tax_id'], $debtor_trans_I['fbr_tax_id'], $debtor_trans_I['discount1'],
    $debtor_trans_I['discount2'], $debtor_trans_I['cheque_date'], $debtor_trans_I['cheque_no'], $debtor_trans_I['text_1'],
    $debtor_trans_I['text_2'], $debtor_trans_I['text_3'], $debtor_trans_I['salesman'], $debtor_trans_I['h_amount1'],
    $debtor_trans_I['so_no'], $debtor_trans_I['cust_ref']);

	$total = 0;
	while($myrow = db_fetch($debtor_trans_details_I)) {
		$get_src_id = get_invoice_parent_lines(ST_CUSTDELIVERY);
		write_customer_trans_detail_item(ST_SALESINVOICE, $invoice_no, $myrow['stock_id'],
			$myrow['description'], $myrow['quantity'],
			$myrow['unit_price'], $myrow['unit_tax'], $myrow['discount_percent'],
			$myrow['standard_cost'], $get_src_id, 0, $myrow['units_id'],
            $myrow['con_factor'], $myrow['text1'], $myrow['text2'], $myrow['text3'], $myrow['text4'],$myrow['text5'], $myrow['text6'],
            $myrow['amount1'], $myrow['amount2'], $myrow['amount3'], $myrow['amount4'],$myrow['amount5'], $myrow['amount6'],
            $myrow['date1'], $myrow['date2'], $myrow['date3'],
            $myrow['combo1'], $myrow['combo2'], $myrow['combo3'],$myrow['combo4'], $myrow['combo5'],
            $myrow['combo6'],$myrow['batch'],$myrow['item_location']);
	}
	while($myrow2 = db_fetch($get_gl)) {
		$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $myrow2['account'], $myrow2['dimension_id'], $myrow2['dimension2_id'],
			$myrow2['amount'], $myrow2['person_id'], "The total debtor GL posting could not be inserted", 0, $myrow['cheque_date'],
            $myrow['cheque_no'], $myrow['text_1'], $myrow['text_2'], $myrow['text_3']);
	}
    inactive_gl(ST_SALESINVOICE, $debtor_trans_I["trans_no"]);
	add_gl_balance(ST_SALESINVOICE, $invoice_no, $date_, -$total, PT_CUSTOMER, $debtor_trans_I["debtor_no"]);
	add_comments(ST_SALESINVOICE, $invoice_no, $date_, $debtor_trans_I['memo_']);
	if ($trans_no == 0) {
		$Refs->save(ST_SALESINVOICE, $invoice_no, $reference);
	}
	hook_db_postwrite($invoice, ST_SALESINVOICE);
	commit_transaction();
	return $invoice_no;
}

//--------------------------------------------------------------------------------------------------

function void_sales_invoice_voucher($type, $type_no)
{
	begin_transaction();

	hook_db_prevoid($type, $type_no);
	void_bank_trans($type, $type_no, true);
	void_gl_trans($type, $type_no, true);

	// reverse all the changes in parent document(s)

	$parents = get_sales_parent_numbers($type, $type_no);

	if ($parents !== 0) {
		if ($type == ST_SALESINVOICE && count($parents) == 1 && get_reference(ST_CUSTDELIVERY, $parents[0]) == "auto")
		{
			void_sales_delivery(ST_CUSTDELIVERY, $parents[0], false);
			$date_ = Today();
			add_audit_trail(ST_CUSTDELIVERY, $parents[0], $date_, _("Voided."));
			add_voided_entry(ST_CUSTDELIVERY, $parents[0], $date_, "");
		}
		else
		{
			$srcdetails = get_sales_parent_lines($type, $type_no);
			while ($row = db_fetch($srcdetails)) {
				update_parent_line($type, $row['id'], -$row['quantity']);
			}
		}
	}
	// clear details after they've been reversed in the sales order
	void_customer_trans_details($type, $type_no);

	void_stock_move($type, $type_no); // in case of credit note with return

	void_trans_tax_details($type, $type_no);

	void_cust_allocations($type, $type_no);

	// do this last because other voidings can depend on it - especially voiding
	// DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
	void_customer_trans($type, $type_no);

	commit_transaction();
}

?>