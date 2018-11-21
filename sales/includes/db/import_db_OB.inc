<?php
//-----------------------------------------------------------------------------
//	Add or update Sales Invoice
//
function get_items_total_dispatch_import($qty_dispatched, $price, $discount_percent=0)
{
	$total = 0;

	$total += round(($qty_dispatched * $price  - $discount_percent),
		user_price_dec());

	return $total;
}
function get_taxes_import1($shipping_cost=null, $stock_id, $qty_dispatched, $price)
{

	$items = array();
	$prices = array();
	if($shipping_cost==null)
		$shipping_cost = 0;


	$items[] = $stock_id;
	//orginal m line_mpi ki jaga line_price() ay ga
	$prices[] = round((($qty_dispatched) * $price),  user_price_dec());//$ln_itm->line_price();

	$taxes = get_tax_for_items($items, $prices, $shipping_cost, $tax_group, $tax_included=null, $tax_items_array=null);

	// Adjustment for swiss franken, we always have 5 rappen = 1/20 franken
	if ($this->customer_currency == 'CHF') {
		$val = $taxes['1']['Value'];
		$val1 = (floatval((intval(round(($val*20),0)))/20));
		$taxes['1']['Value'] = $val1;
	}
	return $taxes;
}
function write_sales_invoice_import_ob($customer_id, $Branch, $date_, $stock_id,$qty_dispatched, $item_description, $price,
    $delivery_no, $order_no, $payment_terms, $sales_type_id, $item, $Customer, $reference, $comments, $due_date, $dimension_id, $location)
{
	global $Refs;



	$trans_type = ST_SALESINVOICE;
	$trans_no = 0;
    $dimension_id = $dimension_id;
    $dim = $dimension_id;
    $dim2 = 0;
    $dimension2_id = 0;
    $total = 0;
    $discount_percent = 0;
    $charge_shipping = 0;
    $Comments = '';
    if($comments != '')
       $Comments = $comments;
    if($reference == '')
        $reference = $Refs->get_next($trans_type, null, array('date' => $date_,
        'customer' => $customer_id, 'branch' => $Branch));
//	$reference = $references;
	begin_transaction();

	hook_db_prewrite($invoice, ST_SALESINVOICE);
	$company_data = get_company_prefs();

	$branch_data = get_branch_accounts($Branch);

	$customer = get_customer($customer_id);

	// offer price values without freight costs
//	$items_total = get_items_total_dispatch_import($qty_dispatched, $price, $discount_percent);
	$items_total = $price;


	$invoice_no = write_customer_trans(ST_SALESINVOICE, $trans_no, $customer_id,
		$Branch, $date_, $reference,
		$items_total, 0, 0, 0, 0, $sales_type_id, $order_no, 1, $due_date, 0, 0,
		$dimension_id, $dimension2_id, $payment_terms, 1);
	// 2008-06-14 extra $alloc, 2008-11-12 added dimension_id Joe Hunt

	{
//		$qty = $invoice_line->qty_dispatched;
//		$line_taxfree_price = get_tax_free_price_for_item($stock_id,
//			$price * $qty_dispatched, 0, 0,
//			0, $mpi, $qty_dispatched);
//
//		$line_tax = get_full_price_for_item($invoice_line->stock_id,
//				$invoice_line->price * $qty, 0, $invoice->tax_included,
//				$invoice->tax_group_array) - $line_taxfree_price;
        $get_src_id = get_invoice_parent_lines_import(ST_CUSTDELIVERY);
		write_customer_trans_detail_item(ST_SALESINVOICE, $invoice_no, $stock_id,
			$item_description, $qty_dispatched,
			$price, 0, 0,
			0, $get_src_id,
			$trans_no, $item['units'], 0, 0, 0, 0
            , 0, 0 ,0 ,0 ,0 ,0 ,0, 0
            , 0, 0, 0, 0,0, 0, 0, 0
            , 0, 0, 0, $location);

		// Update delivery items for the quantity invoiced
//		if ($invoice_line->qty_old != $invoice_line->qty_dispatched)
//			update_parent_line(ST_SALESINVOICE, $invoice_line->src_id, ($invoice_line->qty_dispatched-$invoice_line->qty_old));
//

//		$sql1 = "UPDATE ".TB_PREF."sales_order_details
//				SET qty_sent = $qty_dispatched";
//		$sql1 .= " WHERE order_no=".db_escape($order_no);
//		db_query($sql1, "The parent document detail record could not be updated");

		$sql2 = "UPDATE ".TB_PREF."debtor_trans_details
				SET qty_done = qty_done + $qty_dispatched
				WHERE debtor_trans_no=".db_escape($invoice_no)."
				AND debtor_trans_type = ".ST_CUSTDELIVERY;
		db_query($sql2, "The parent document detail record could not be updated");

		if ($qty_dispatched != 0) {
			$stock_gl_code = get_stock_gl_code($stock_id);
 
			if ($price != 0)
			{
				//if ($invoice_line->mpi != 0) {
				//Post sales transaction to GL credit sales

				// 2008-06-14. If there is a Branch Sales Account, then override with this,
				// else take the Item Sales Account
				$sales_account = /*($branch_data['sales_account'] != "" ? $branch_data['sales_account'] : */$stock_gl_code['sales_account']/*)*/;
				// 2008-08-01. If there is a Customer Dimension, then override with this,
				// else take the Item Dimension (if any)
				// $dim = ($invoice->dimension_id != $customer['dimension_id'] ? $invoice->dimension_id :
				// 	($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
				// $dim2 = ($invoice->dimension2_id != $customer['dimension2_id'] ? $invoice->dimension2_id :
				// 	($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));
				$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $sales_account, $dim, $dim2,
					-$price ,
					$customer_id, "The sales price GL posting could not be inserted");

				if ($discount_percent != 0) {

					$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_,
						$branch_data["sales_discount_account"], $dim, $dim2,
						($discount_percent),
                        $customer_id, "The sales discount GL posting could not be inserted");
				} /*end of if discount !=0 */
			}
		} /*quantity dispatched is more than 0 */
	} /*end of delivery_line loop */

	if (($items_total + $charge_shipping) != 0) {
		$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $branch_data["receivables_account"], 0, 0,
			($items_total),
			$customer_id, "The total debtor GL posting could not be inserted");
	}

//	if ($charge_shipping != 0) {
//		$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $company_data["freight_act"], 0, 0,
//			-$invoice->get_tax_free_shipping(), $customer_id,
//			"The freight GL posting could not be inserted");
//	}
	// post all taxes
//	foreach ($taxes as $taxitem) {
//
//		if ($taxitem['Net'] != 0) {
			$ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($customer_id), $date_);
			add_trans_tax_details(ST_SALESINVOICE, $invoice_no, 0,
                $ex_rate, 1, 0,
                $items_total, $ex_rate, $date_, $reference, 0);
//
//			$total += add_gl_trans_customer(ST_SALESINVOICE, $invoice_no, $date_, $taxitem['sales_gl_code'], 0, 0,
//				(-$taxitem['Value']), $customer_id,
//				"A tax GL posting could not be inserted");
//
//		}
//	}

	/*Post a balance post if $total != 0 */
	$Refs->save(ST_SALESINVOICE, $invoice_no, $reference);
	add_gl_balance(ST_SALESINVOICE, $invoice_no, $date_, -$total, PT_CUSTOMER, $customer_id);

	add_comments(ST_SALESINVOICE, $invoice_no, $date_, $Comments);
	
	hook_db_postwrite($invoice, ST_SALESINVOICE);
	commit_transaction();

	return $invoice_no;
}

/// For Delivery

//-----------------------------------------------------------------------

function get_delivery_parent_lines_import($trans_type)
{
    $sql = "SELECT COUNT(*) as TotalRecord 
			FROM ".TB_PREF."sales_order_details 
			WHERE trans_type = ".db_escape($trans_type);

    $query = db_query($sql, "Error");
    $result = db_fetch_row($query);
    return $result[0];
}
//-----------------------------------------------------------------------
function get_invoice_parent_lines_import($trans_type)
{
    $sql = "SELECT COUNT(*) as TotalRecord 
			FROM ".TB_PREF."debtor_trans_details 
			WHERE debtor_trans_type 
			IN(".db_escape($trans_type).", ".db_escape(ST_SALESINVOICE).")";

    $query = db_query($sql, "Error");
    $result = db_fetch_row($query);
    return $result[0];
}
function write_sales_delivery_import_ob($customer_id, $Branch,
		$document_date, $stock_id, $qty_dispatched, $description,
        $price, $order_no, $payment_terms, $sales_type_id, $item, $Customer, $comments, $due_date, $dimension_id, $location)
{
	global $Refs;

//	$trans_no = $delivery->trans_no;
//	if (is_array($trans_no)) $trans_no = key($trans_no);

	begin_transaction();
//	$delivery->bo_policy = $bo_policy;
	hook_db_prewrite($delivery, ST_CUSTDELIVERY);
	$discount_percent = 0;
    $dimension_id = $dimension_id;
    $dimension2_id = 0;
	$src_id = $order_no;
	$trans_type = ST_CUSTDELIVERY;
	$trans_no = 0;
    $Comments = '';
    if($comments != '')
       $Comments = $comments;
    $reference = 'auto'/*$Refs->get_next($trans_type, null, array('date' => $document_date,
        'customer' => $customer_id, 'branch' => $Branch))*/;
//	$reference = $references/*$Refs->get_next($trans_type)*/;
//	$customer = get_customer($customer_id);
//	$delivery_items_total = get_items_total_dispatch_import($qty_dispatched, $price, $discount_percent);
	$delivery_items_total = $price;
//	$freight_tax = $delivery->get_shipping_tax();

// mark sales order for concurrency conflicts check
//	update_sales_order_version($delivery->src_docs);

//	$tax_total = 0;
//	$taxes = get_taxes_import($shipping_cost=null, $stock_id, $qty_dispatched, $price); // all taxes with freight_tax
//
//	foreach ($taxes as $taxitem) {
//		$taxitem['Value'] =  round2($taxitem['Value'], user_price_dec());
//		$tax_total +=  $taxitem['Value'];
//	}
	/* Insert/update the debtor_trans */
	$delivery_no = write_customer_trans(ST_CUSTDELIVERY, $trans_no, $customer_id,
		$Branch, $document_date, $reference, $delivery_items_total,
        0, 0, 0, 0, $sales_type_id, $order_no, 1,
        $due_date, 0, 0, $dimension_id, $dimension2_id, $payment_terms);
//  Update version
	$sql = "UPDATE ".TB_PREF."debtor_trans SET
			version = 1
			WHERE type = 13
			AND trans_no = ".db_escape($delivery_no);
    db_query($sql, "Error");
//	if ($trans_no == 0) {
//		$delivery->trans_no = array($delivery_no=>0);
//	} else {
//		void_gl_trans(ST_CUSTDELIVERY, $delivery_no, true);
//		void_stock_move(ST_CUSTDELIVERY, $delivery_no);
//		void_trans_tax_details(ST_CUSTDELIVERY, $delivery_no);
//		delete_comments(ST_CUSTDELIVERY, $delivery_no);
//	}

//	foreach ($delivery->line_items as $line_no => $delivery_line)
	{

		//$line_price = $delivery_line->line_price();
//		if($delivery_line->mpi = 0)
//			$line_price = $delivery_line->mpi;
//		else
			$line_price = $price;
//		$line_taxfree_price = get_tax_free_price_for_item($stock_id,
//			$price * $qty_dispatched, 0, 0,
//			0, $mpi, $qty_dispatched);

//		$line_tax = get_full_price_for_item($stock_id, $price,
//				0, $delivery->tax_included, $delivery->tax_group_array) - $line_taxfree_price;

		//if ($trans_no != 0) // Inserted 2008-09-25 Joe Hunt. This condition is removed after experience by Chaitanya
		$standard_cost = 0/*get_standard_cost($stock_id)*/;

		/* add delivery details for all lines */
        $get_src_id = get_delivery_parent_lines_import(ST_SALESORDER);
		write_customer_trans_detail_item(ST_CUSTDELIVERY, $delivery_no, $stock_id,
			$description, $qty_dispatched, $price, 0, $discount_percent,
			$standard_cost, $get_src_id, 0, $item['units'], 0, 0, 0
        , 0, 0 ,0 ,0 ,0 ,0 ,0, 0
        , 0, 0, 0, 0,0, 0, 0, 0
        , 0, 0, 0, 0, $location);

		// Now update sales_order_details for the quantity delivered
//		if ($delivery_line->qty_old != $delivery_line->qty_dispatched)
//			update_parent_line(ST_CUSTDELIVERY, $delivery_line->src_id,
//				$delivery_line->qty_dispatched-$delivery_line->qty_old);

		if ($qty_dispatched != 0) {
			add_stock_move(ST_CUSTDELIVERY, $stock_id, $delivery_no,
                $location, $document_date, $reference,
				-$qty_dispatched, 0, $price,
                $customer_id, $discount_percent,'','','','',
                '','',0,0,0,0,0,
                0,0,0,0,0,0,0,0,
                0,0,$item['units'],0,0, $location);
		} /*quantity dispatched is more than 0 */
	} /*end of order_line loop */

//	if ($bo_policy == 0) {
//		// if cancelling any remaining quantities
//		close_sales_order($delivery->order_no);
//	}

	// taxes - this is for printing purposes
//	foreach ($taxes as $taxitem)
//	{
//		if ($taxitem['Net'] != 0) {
			$ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($customer_id), $document_date);
			add_trans_tax_details(ST_CUSTDELIVERY, $delivery_no, 0,
                $ex_rate, 1, 0,
                $line_price, $ex_rate, $document_date, $reference);
//		}
//	}

	add_comments(ST_CUSTDELIVERY, $delivery_no, $document_date, $Comments);

	if ($trans_no == 0) {
		$Refs->save(ST_CUSTDELIVERY, $delivery_no, $reference);
	}

	hook_db_postwrite($delivery, ST_CUSTDELIVERY);
	commit_transaction();

	return $delivery_no;
}
// sales order
function get_shipping_tax_import()
{
//	$tax_group_array = null;
	$tax_items = get_shipping_tax_as_array();
	$tax_rate = 0;
	if ($tax_items != null) {
		foreach ($tax_items as $item_tax) {
			$index = $item_tax['tax_type_id'];
			if (isset($tax_group_array[$index]['rate'])) {
				$tax_rate += $item_tax['rate'];
			}
		}
	}
	if($tax_included)
		return round($freight_cost*$tax_rate/($tax_rate+100),  user_price_dec());
	else
		return round($freight_cost*$tax_rate/100,  user_price_dec());
}
function get_items_total_import($quantity, $price, $discount_percent)
{
	$total = 0;

//	foreach ($this->line_items as $ln_itm)
	{
		 //$ln_itm->line_price();
		$total += round($quantity * $price  - $discount_percent,
			user_price_dec());
	}
	return $total;
}
function get_trans_total_import($freight_cost, $tax_included, $quantity, $price, $discount_percent) {

	$total = get_items_total_import($quantity, $price, $discount_percent) + $freight_cost;
	$dec = user_price_dec();
	if (!$tax_included ) {
		$total += get_shipping_tax_import();
		$taxes = get_taxes_import1($shipping_cost=null, $stock_id, $qty_dispatched, $price);
		foreach($taxes as $tax)
			$total += round($tax['Value'], $dec);
	}

	return $total;
}
function add_sales_order_import_ob($customer_id, $branch_id, $date, $stock_id, $qty,
        $description, $price, $GetCustInfo, $payment_terms, $sales_type_id, $Item, $comments, $due_date, $dimension_id, $location)
{
	global $Refs;

	begin_transaction();
	$trans_type = ST_SALESORDER;
	hook_db_prewrite($order, $trans_type);
	$DocumentDate = date2sql($date);
	$order_no = get_next_trans_no($trans_type);
	$reference = 'auto'/*$Refs->get_next($trans_type, null, array('date' => $DocumentDate,
            'customer' => $customer_id, 'branch' => $branch_id))*/;
	
	$cust_ref = $Comments = $phone = '';
	if($comments != '')
	     $Comments = $comments;
    $deliver_to = $GetCustInfo['DebtorName'];
    $delivery_address = $GetCustInfo['address'];
	$order_type = 1; // this is default on new order
    $freight_cost = 0; // this is default on new order
    $discount_percent = 0; // this is default on new order

	$sql = "INSERT INTO ".TB_PREF."sales_orders (order_no, type, debtor_no, 
	trans_type, version, branch_code, customer_ref, reference, comments, 
	ord_date,order_type, ship_via, deliver_to, delivery_address, contact_phone,
		freight_cost, from_stk_loc, delivery_date, payment_terms, total, dimension_id)
		VALUES (" .db_escape($order_no) . "," .db_escape($order_type) . "," . db_escape($customer_id) .
		", " .db_escape($trans_type) . "," .db_escape(1) . "," .db_escape($branch_id) . ", ".
		db_escape($cust_ref) .",".
		db_escape($reference) .",".
		db_escape($Comments) ."," .
		db_escape($DocumentDate) . ", " .
		db_escape($order_type) . ", " .
		db_escape($sales_type_id)."," .
		db_escape($deliver_to) . "," .
		db_escape($delivery_address) . ", " .
		db_escape($phone) . ", " .
		db_escape($freight_cost) .", ".
        db_escape($location).", " .
		db_escape($DocumentDate) . "," .
		db_escape($payment_terms) . "," .
		db_escape($price) . "," .
		db_escape($dimension_id). ")";

	db_query($sql, "order Cannot be Added");

	$sql = "INSERT INTO ".TB_PREF."sales_order_details (order_no, trans_type,
	 stk_code, description, qty_sent, unit_price, quantity, discount_percent, 
	 units_id, item_location) VALUES (";
	$sql .= db_escape($order_no). ",".db_escape($trans_type).
		",".db_escape($stock_id).", "
		.db_escape($description).", '$qty', '$price',
			'$qty', '$discount_percent', 
			".db_escape($Item['units']).", 
			".db_escape($location).")";
	db_query($sql, "order Details Cannot be Added");

	 /* inserted line items into sales order details */
	add_audit_trail($trans_type, $order_no, $DocumentDate);
	$Refs->save($trans_type, $order_no, $reference);

	hook_db_postwrite($order, $trans_type);
	commit_transaction();

	return $order_no;
}
// get customer debtor_no from table
function get_data_from_customer_table_where_debtor_ref_ob($name)
{
	$sql = "SELECT *, master.debtor_no as customer_id, 
                  branch.branch_code as branch_id, master.name as DebtorName
			FROM ".TB_PREF."debtors_master master, ".TB_PREF."cust_branch branch
			WHERE master.debtor_ref = ".db_escape($name)."
			AND master.debtor_no = branch.debtor_no ";
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_data_from_customer_table_where_debtorno_ob($debtor_no)
{
	$sql = "SELECT *, master.debtor_no as customer_id, 
                  branch.branch_code as branch_id, master.name as DebtorName
			FROM ".TB_PREF."debtors_master master, ".TB_PREF."cust_branch branch
			WHERE master.debtor_no = ".db_escape($debtor_no)."
			AND master.debtor_no = branch.debtor_no";
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
// get supplier id from table
//function get_id_from_supplier_table($name)
//{
//	$sql = "SELECT suppliers.supplier_id as supplier_id
//			FROM ".TB_PREF."suppliers suppliers
//			WHERE suppliers.supp_name = ".db_escape($name);
//	$query = db_query($sql, "Error");
//	return db_fetch($query);
//}
//
//// Supplier OB
//
//
//function add_supplier_OB_invoices($trans_no, $supplier_id, $date, $account_code, $dim, $dim2, $price, $memo, $Comments)
//{
//	global $Refs;
//
//	begin_transaction();
//	$trans_type = ST_SUPPINVOICE;
//	hook_db_prewrite($supp_trans, $trans_type);
//
//	$reference = $Refs->get_next($trans_type);
//
//	$tax_total = 0;
//
////	$taxes = $supp_trans->get_taxes($supp_trans->tax_group_id);
//
////	foreach ($taxes as $n => $taxitem)
////	{
////		$taxes[$n]['Value'] =  round2($taxitem['Value'], user_price_dec());
////		$tax_total += $taxes[$n]['Value'];
////	}
//
//	$invoice_items_total = $price;
//
////	if($supp_trans->tax_included==0)
////	{
////		$item_added_tax = $tax_total;
////	}
////	else
////	{
////		$item_added_tax = 0;
////	}
//
////	$trans_type = $supp_trans->trans_type;
//
////	if ($trans_type == ST_SUPPCREDIT)
////	{
////		// let's negate everything because it's a credit note
////		$invoice_items_total = -$invoice_items_total;
////		$tax_total = -$tax_total;
////		$supp_trans->ov_discount = -$supp_trans->ov_discount; // this isn't used at all...
////		$item_added_tax = -$item_added_tax;
////	}
//
//	$date_ = $date;
//	$ex_rate = get_exchange_rate_from_home_currency(get_supplier_currency($supp_trans->supplier_id), $date_);
//
//	/*First insert the invoice into the supp_trans table*/
//	$invoice_id = write_supp_trans($trans_type, 0, $supplier_id, $date_, $date_,
//	$reference, $reference, $invoice_items_total,0,0,0,"",0,0,0,0,0);
//
//	$total = 0;
//	/* Now the control account */
//	$supplier_accounts = get_supplier_accounts($supplier_id);
//	$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $supplier_accounts["payable_account"], 0, 0,
//		-($invoice_items_total +  0 + 0),
//		$supplier_id,
//		"The general ledger transaction for the control total could not be added");
//
////	foreach ($supp_trans->gl_codes as $entered_gl_code)
//	{
////		if ($trans_type == ST_SUPPCREDIT)
////			$entered_gl_code->amount = -$entered_gl_code->amount;
//
//		$memo_ = $memo;
//		$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $account_code,
//			$dim, $dim2, $invoice_items_total, $supplier_id, "", 0, $memo_);
//
//		add_supp_invoice_gl_item($trans_type, $invoice_id, $account_code,
//			$invoice_items_total, $memo_);
//
//		// store tax details if the gl account is a tax account
////		if ($trans_type == ST_SUPPCREDIT)
////			$entered_gl_code->amount = -$entered_gl_code->amount;
//		add_gl_tax_details($account_code,
//			$trans_type, $invoice_id, $invoice_items_total,
//			$ex_rate, $date_, $reference, 0);
//	}
//
////	$clearing_act = get_company_pref('grn_clearing_act');
////	foreach ($supp_trans->grn_items as $line_no => $entered_grn)
////	{
////
////		if ($trans_type == ST_SUPPCREDIT)
////		{
////			$entered_grn->this_quantity_inv = -$entered_grn->this_quantity_inv;
////			set_grn_item_credited($entered_grn, $supp_trans->supplier_id, $invoice_id, $date_);
////		}
////		$line_taxfree = $entered_grn->taxfree_charge_price($supp_trans->tax_group_id);
////		$line_tax = $entered_grn->full_charge_price($supp_trans->tax_group_id) - $line_taxfree;
////		$stock_gl_code = get_stock_gl_code($entered_grn->item_code);
////
////		if ($trans_type == ST_SUPPCREDIT)
////		{
////			$iv_act = (is_inventory_item($entered_grn->item_code) ? $stock_gl_code["inventory_account"] :
////				($supplier_accounts["purchase_account"] ? $supplier_accounts["purchase_account"] : $stock_gl_code["cogs_account"]));
////			$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
////				$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////				$entered_grn->this_quantity_inv * $line_taxfree, $supp_trans->supplier_id);
////		}
////		else
////		{
////			// -------------- if price changed since po received. 16 Aug 2008 Joe Hunt
////			$iv_act = is_inventory_item($entered_grn->item_code) ? ($clearing_act ? $clearing_act : $stock_gl_code["inventory_account"]) :
////				($supplier_accounts["purchase_account"] ? $supplier_accounts["purchase_account"] : $stock_gl_code["cogs_account"]);
////			$old = update_supp_received_items_for_invoice($entered_grn->id, $entered_grn->po_detail_item,
////				$entered_grn->this_quantity_inv, $entered_grn->chg_price);
////			// Since the standard cost is always calculated on basis of the po unit_price,
////			// this is also the price that should be the base of calculating the price diff.
////			// In cases where there is two different po invoices on the same delivery with different unit prices this will not work either
////
////			//$old_price = $old[0];
////
////			$old_price = $old[2];
////			// adjust for tax included.
////			$old_price = get_tax_free_price_for_item($entered_grn->item_code, $old_price,
////				$supp_trans->tax_group_id, $entered_grn->tax_included);
////
////			/*
//// 			If statement is removed. Should always check for deliveries nomatter if there has been a price change.
////			*/
////			//if ($old_price != $line_taxfree) // price-change, so update
////			//{
////			//$diff = $line_taxfree - $old_price;
////			$old_date = sql2date($old[1]);
////			if (!is_inventory_item($entered_grn->item_code))
////				$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
////					$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////					$entered_grn->this_quantity_inv * $line_taxfree, $supp_trans->supplier_id);
////			else
////			{
////				$currency = get_supplier_currency($supp_trans->supplier_id);
////				$ex_rate = get_exchange_rate_from_home_currency($currency, $old_date);
////				$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
////					$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////					$entered_grn->this_quantity_inv * $old_price, $supp_trans->supplier_id, "", $ex_rate);
////				$diff = get_diff_in_home_currency($supp_trans->supplier_id, $old_date, $date_, $old_price,
////					$line_taxfree);
////
////				// always return due to change in currency.
////				/*$mat_cost = update_average_material_cost(null, $entered_grn->item_code,
////					$diff, $entered_grn->this_quantity_inv, $old_date, true); */
////				$mat_cost = update_average_material_cost(null, $entered_grn->item_code,
////					$diff, $entered_grn->this_quantity_inv, null, true);
////
////				//Add GL transaction for GRN Provision in case of difference
////				if (($diff * $entered_grn->this_quantity_inv) != 0 )
////				{
////					$diff_amt = $diff * $entered_grn->this_quantity_inv;
////					$total += add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["inventory_account"],
////						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'], 'GRN Provision',
////						$diff_amt, null, null, null,
////						"The general ledger transaction could not be added for the GRN of the inventory item");
////
////					//Chaitanya
////					//If QOH is 0 or negative then update_average_material_cost will be skipped
////					//Thus difference in PO and Supp Invoice should be handled separately
////
////					$qoh = get_qoh_on_date($entered_grn->item_code);
////					if ($qoh <= 0)
////					{
////						global $Refs;
////
////						//Chaitanya : Post a journal entry
////						$id = get_next_trans_no(ST_JOURNAL);
////						$ref = $Refs->get_next(ST_JOURNAL);
////						$stock_id = $entered_grn->item_code;
////						$stock_gl_code = get_stock_gl_code($stock_id);
////						$memo = _("Supplier invoice adjustment for zero inventory of ").$stock_id." "._("Invoice")." ".$supp_trans->reference;
////						//Reverse the inventory effect if $qoh <=0
////						add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
////							$stock_gl_code["inventory_account"],
////							$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////							$memo, -$entered_grn->this_quantity_inv * $diff);
////						//GL Posting to inventory adjustment account
////						add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
////							$stock_gl_code["adjustment_account"],
////							$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////							$memo, $entered_grn->this_quantity_inv * $diff);
////
////						add_audit_trail(ST_JOURNAL, $id, $date_);
////						add_comments(ST_JOURNAL, $id, $date_, $memo);
////						$Refs->save(ST_JOURNAL, $id, $ref);
////					}
////
////					/*$qoh = get_qoh_on_date($entered_grn->item_code);
////					if ($qoh <= 0)
////					{
////						$memo = "Diff. in cost: ".$diff;
////						//Reverse the inventory effect if $qoh <=0
////						add_gl_trans_supplier($trans_type, $invoice_id, $date_,
////						$stock_gl_code["inventory_account"],
////						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////						-$entered_grn->this_quantity_inv * $diff, $supp_trans->supplier_id, "", null, $memo);
////						//GL Posting to inventory adjustment account
////						add_gl_trans_supplier($trans_type, $invoice_id, $date_,
////						$stock_gl_code["adjustment_account"],
////						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
////						$entered_grn->this_quantity_inv * $diff, $supp_trans->supplier_id, "", null, $memo);
////					}*/
////
////					//add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["cogs_account"],
////					//	0, 0, 'GRN Provision', -$diff_amt, null, null, null,
////					//	"The general ledger transaction could not be added for the GRN of the inventory item");
////				}
////			}
////			// added 2008-12-08 Joe Hunt. Update the purchase data table
////			add_or_update_purchase_data($supp_trans->supplier_id, $entered_grn->item_code,
////				$entered_grn->chg_price);
////			/*$deliveries = get_deliveries_between($entered_grn->item_code, $old_date, Today()); // extend the period, if invoice is before any deliveries.
////			if ($deliveries[0] != 0) // have deliveries been done during the period?
////			{
////				$deliveries[1] /= $deliveries[0];
////				$amt = ($mat_cost - $deliveries[1]) * $deliveries[0]; // $amt in home currency
////				if ($amt != 0.0)
////				{
////					$_s = $entered_grn->item_code;
////					$_d = $deliveries[0];
////					$_od = $old_date;
////					$_cd = $mat_cost - $deliveries[1];
////					$memo = _("Cost difference adjustment for $_s. $_d items delivered since $_od. The cost difference is $_cd");
////					add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["cogs_account"],
////						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'], $memo,
////						$amt, null, null, null,
////						"The general ledger transaction could not be added for the price variance of the inventory item");
////					add_gl_trans($trans_type, $invoice_id, $date_,	$iv_act,
////						0, 0, $memo, -$amt, null, null, null,
////						"The general ledger transaction could not be added for the price variance of the inventory item");
////				}
////				update_stock_move_pid(ST_CUSTDELIVERY, $entered_grn->item_code, $old_date, $date_, 0, $mat_cost);
////			}
////			if (is_inventory_item($entered_grn->item_code))
////				update_stock_move_pid(ST_SUPPRECEIVE, $entered_grn->item_code, $old_date, $old_date, $supp_trans->supplier_id, $mat_cost);
////			*/
////			//}
////		}
////		// ----------------------------------------------------------------------
////
////		add_supp_invoice_item($trans_type, $invoice_id, $entered_grn->item_code,
////			$entered_grn->item_description, 0, 	$entered_grn->chg_price, $line_tax,
////			$entered_grn->this_quantity_inv, $entered_grn->id, $entered_grn->po_detail_item, "","",
////			$entered_grn->color,$entered_grn->cap,$entered_grn->capcolor);
////	} /* end of GRN postings */
//	/* Now the TAX account */
////	$taxes = $supp_trans->get_taxes($supp_trans->tax_group_id, 0, false); // 2009.08-18 Joe Hunt. We have already got the gl lines
////	foreach ($taxes as $taxitem)
////	{
////		if ($taxitem['Net'] != 0)
////		{
////
////			if ($trans_type == ST_SUPPCREDIT)
////			{
////				$taxitem['Net'] = -$taxitem['Net'];
////				$taxitem['Value'] = -$taxitem['Value'];
////			}
////			add_trans_tax_details($trans_type, $invoice_id,
////				$taxitem['tax_type_id'], $taxitem['rate'], $supp_trans->tax_included, $taxitem['Value'],
////				$taxitem['Net'], $ex_rate, $date_, $supp_trans->supp_reference);
////
////			if ($trans_type == ST_SUPPCREDIT)
////				$taxitem['Value'] = -$taxitem['Value'];
////			$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_,
////				$taxitem['purchasing_gl_code'], 0, 0, $taxitem['Value'],
////				$supp_trans->supplier_id,
////				"A general ledger transaction for the tax amount could not be added");
////		}
////	}
//
//	/*Post a balance post if $total != 0 */
//	add_gl_balance($trans_type, $invoice_id, $date_, -$total, PT_SUPPLIER, $supplier_id);
//
//	add_comments($trans_type, $invoice_id, $date_, $Comments);
//
//	$Refs->save($trans_type, $invoice_id, $reference);
//
////	if ($invoice_no != 0)
////	{
////		$invoice_alloc_balance = get_supp_trans_allocation_balance(ST_SUPPINVOICE, $invoice_no);
////		if ($invoice_alloc_balance > 0)
////		{ 	//the invoice is not already fully allocated
////
////			$trans = get_supp_trans($invoice_no, ST_SUPPINVOICE);
////			$total = $trans['Total'];
////
////			$allocate_amount = ($invoice_alloc_balance > $total) ? $total : $invoice_alloc_balance;
////			/*Now insert the allocation record if > 0 */
////			if ($allocate_amount != 0)
////			{
////				update_supp_trans_allocation(ST_SUPPINVOICE, $invoice_no, $allocate_amount);
////				update_supp_trans_allocation(ST_SUPPCREDIT, $invoice_id, $allocate_amount); // ***
////				add_supp_allocation($allocate_amount, ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $invoice_no,
////					$date_);
////				// Exchange Variations Joe Hunt 2008-09-20 ////////////////////////////////////////
////
////				exchange_variation(ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $invoice_no, $date_,
////					$allocate_amount, PT_SUPPLIER);
////
////				///////////////////////////////////////////////////////////////////////////
////			}
////		}
////	}
//
////	$supp_trans->trans_no = $invoice_id;
//	hook_db_postwrite($supp_trans, $trans_type);
//	commit_transaction();
//
//	return $invoice_id;
//}
//


function get_stock_master_info_ob($stock_id)
{
	$sql = "SELECT *
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql, "The standard cost cannot be retrieved");
	return db_fetch($result);
}


?>