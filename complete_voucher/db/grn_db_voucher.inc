<?php
//------------------- update average material cost ------------------------------------------ Joe Hunt Mar-03-2008
function update_average_material_cost_voucher($supplier, $stock_id, $price, $qty, $date, $adj_only=false)
{
	//Handle if inventory will become negative
    //8-OCT-2011 : Skip negative inventory adjustment for case of adjust_only
    if (is_inventory_item($stock_id) && !$adj_only) 
        handle_negative_inventory($stock_id, $qty, $price, $date);	

	// probably this function should be optimized
	// passing transaction cart as argument. This would
	// save a couple of db calls like get_supplier()
	
	$supp = get_supplier($supplier);
	if ($supplier != null)
		$currency = $supp['curr_code'];
	else
		$currency = null;

	if ($supp['tax_included'])
		$price = get_tax_free_price_for_item($stock_id, $price, $supp['tax_group_id'],
			$supp['tax_included']);

	if ($currency != null)
	{
		$ex_rate = get_exchange_rate_to_home_currency($currency, $date);
		$price_in_home_currency = $price / $ex_rate;
	}	
	else
		$price_in_home_currency = $price;
	
	$price_in_home_currency_ = $price_in_home_currency;
	
	$sql = "SELECT mb_flag, material_cost, labour_cost, overhead_cost FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql);
	$myrow = db_fetch($result);
	$material_cost = $myrow['material_cost'];
	
	//Price adjustment for manufactured item
	if (!$adj_only && $myrow['mb_flag'] == 'M') 
	{
		$standard_cost = get_standard_cost($stock_id);
		//reduce overhead_cost and labour_cost from price as those will remain as is
		$price_in_home_currency = $price_in_home_currency - $myrow['labour_cost'] - $myrow['overhead_cost'];
	}
	
	if ($adj_only)
		$exclude = ST_CUSTDELIVERY;
	else
		$exclude = 0;
	$cost_adjust = false;
	$qoh = get_qoh_on_date($stock_id);

	if ($adj_only)
	{
		if ($qoh > 0)
			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	$qoh;
	}
	else
	{
		if ($qoh < 0)
		{
			if ($qoh + $qty >= 0)
				$cost_adjust = true;
			$qoh = 0;
		}
		if ($qoh + $qty > 0)
			$material_cost 
= ($qoh * $material_cost + $qty * $price_in_home_currency) /	($qoh + $qty);
	}	
	
	if ($cost_adjust) // new 2010-02-10 //Chaitanya : Material_cost replaced with price
		adjust_deliveries($stock_id, $price_in_home_currency_, $date);
	$sql = "UPDATE ".TB_PREF."stock_master SET material_cost=".db_escape($material_cost)."
		WHERE stock_id=".db_escape($stock_id);
	db_query($sql,"The cost details for the inventory item could not be updated");
	return $material_cost;
}

//-------------------------------------------------------------------------------------------------------------

function add_grn_voucher($Get_po, $po_no, $Get_curr_PO_Details)
{
	global $Refs;

	$date_ = sql2date($Get_po['ord_date']);
    if($Get_po['reference'] == 'auto')
        $reference = 'auto';
    else
        $reference = $Refs->get_next(ST_SUPPRECEIVE, null, sql2date($date_));

	begin_transaction();
	hook_db_prewrite($po, ST_SUPPRECEIVE);

	$grn = add_grn_batch_voucher($po_no, $Get_po['supplier_id'], $reference,
        $Get_po['into_stock_location'], $date_);

    $clearing_act = get_company_pref('grn_clearing_act');
	if ($clearing_act) {	// otherwise GRN clearing account is not used
	    $total = 0;
	}

    while($Add_PO_Details = db_fetch($Get_curr_PO_Details))
	{

//		if ($Add_PO_Details['quantity_received'] != 0 && $Add_PO_Details['quantity_received'] != "" && isset($Add_PO_Details['quantity_received']))
		{
			$stock_gl_code = get_stock_gl_code($Add_PO_Details['item_code']);

			/*Update sales_order_details for the new quantity received and the standard cost used for postings to GL and recorded in the stock movements for FIFO/LIFO stocks valuations*/
			//------------------- update average material cost and clearing account ----- Joe Hunt Mar-03-2008
			if (is_inventory_item($Add_PO_Details['item_code']))
			{
				if ($clearing_act)
					$total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $stock_gl_code["inventory_account"],
						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
                        $Add_PO_Details['quantity_received'] * $Add_PO_Details['unit_price'], $Get_po['supplier_id'], '', 0, $Add_PO_Details['item_code']);
				update_average_material_cost($Get_po['supplier_id'], $Add_PO_Details['item_code'], $Add_PO_Details['unit_price'],
                    $Add_PO_Details['quantity_received'], $date_);
			}

			//----------------------------------------------------------------------------------------------------------------
			if ($Add_PO_Details['quantity_received'] == 0)
			{
				/*This must be the first receipt of goods against this line */
			   	/*Need to get the standard cost as it is now so we can process GL jorunals later*/
			   	$standard_cost = get_standard_cost_voucher($Add_PO_Details['item_code']);
			}
			// added 2008-12-08 Joe Hunt. Update the purchase data table
			add_or_update_purchase_data($Get_po['supplier_id'], $Add_PO_Details['item_code'], $Add_PO_Details['unit_price'],
                $Add_PO_Details['description']);

            /*Need to insert a grn item */ // also need to check for over-receive.(within allowance)
//			if ($order_line->receive_qty + $order_line->qty_received > $order_line->quantity)
//				$order_line->quantity = $order_line->receive_qty + $order_line->qty_received;
			$grn_item = add_grn_detail_item($grn, $Add_PO_Details['po_detail_item'],
                $Add_PO_Details['item_code'], $Add_PO_Details['description'],
                $standard_cost,	$Add_PO_Details['quantity_ordered'], $Add_PO_Details['unit_price'],
                $Add_PO_Details['quantity_ordered'] ,$Add_PO_Details['grn_batch'],$Add_PO_Details['text1'],$Add_PO_Details['text2'],
                $Add_PO_Details['text3'], $Add_PO_Details['text4'],$Add_PO_Details['text5'],$Add_PO_Details['text6'],
                $Add_PO_Details['amount1'],$Add_PO_Details['amount2'],
                $Add_PO_Details['amount3'], $Add_PO_Details['amount4'],$Add_PO_Details['amount5'],$Add_PO_Details['amount6'],
                sql2date($Add_PO_Details['date1']), sql2date($Add_PO_Details['date2']), sql2date($Add_PO_Details['date3']),
                $Add_PO_Details['combo1'], $Add_PO_Details['combo2'], $Add_PO_Details['combo3'], $Add_PO_Details['combo4'],
                $Add_PO_Details['combo5'], $Add_PO_Details['combo6'], $Add_PO_Details['con_factor'], $Add_PO_Details['units_id'],
                $Add_PO_Details['discount_percent']);

//			$grn_item_id = $grn_item;
			/* Update location stock records - NB  a po cannot be entered for a service/kit parts done automatically */
			add_stock_move(ST_SUPPRECEIVE, $Add_PO_Details['item_code'], $grn, $Get_po['into_stock_location'], $date_, $reference,
                $Add_PO_Details['quantity_ordered'], $standard_cost,$Add_PO_Details['unit_price'],
                $Get_po['supplier_id'], $Add_PO_Details['discount_percent'], $Add_PO_Details['text1'],
                $Add_PO_Details['text2'],$Add_PO_Details['text3'],$Add_PO_Details['text4'],
                $Add_PO_Details['text5'],$Add_PO_Details['text6'],$Add_PO_Details['amount1'],
                $Add_PO_Details['amount2'],$Add_PO_Details['amount3'],$Add_PO_Details['amount4'],
                $Add_PO_Details['amount5'],$Add_PO_Details['amount6'], sql2date($Add_PO_Details['date1']),
                sql2date($Add_PO_Details['date2']), sql2date($Add_PO_Details['date3']),
                $Add_PO_Details['combo1'], $Add_PO_Details['combo2'], $Add_PO_Details['combo3'], $Add_PO_Details['combo4'],
                $Add_PO_Details['combo5'], $Add_PO_Details['combo6'],
                $Add_PO_Details['units_id'], $Add_PO_Details['con_factor'], $Add_PO_Details['batch'], $Add_PO_Details['loc_code'],
                $Add_PO_Details['to_stk_loc']);

		} /*quantity received is != 0 */
	} /*end of order_line loop */

	if ($clearing_act && $total != 0.0) {
		$total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $clearing_act,
			0, 0, -$total, null);
	}
	$Refs->save(ST_SUPPRECEIVE, $grn, $reference);
	add_audit_trail(ST_SUPPRECEIVE, $grn, $date_);
//	$trans_no = $grn;
	hook_db_postwrite($po, ST_SUPPRECEIVE);
	commit_transaction();
	return $grn;
}

function get_standard_cost_voucher($stock_id)
{
    $sql = "SELECT (material_cost + labour_cost + overhead_cost) AS std_cost
		FROM ".TB_PREF."stock_master s WHERE stock_id=".db_escape($stock_id);
    $result = db_query($sql, "The standard cost cannot be retrieved");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}

//----------------------------------------------------------------------------------------

function add_grn_batch_voucher($po_number, $supplier_id, $reference, $location, $date_)
{
	$date = date2sql($date_);

	$sql = "INSERT INTO ".TB_PREF."grn_batch (purch_order_no, delivery_date, supplier_id, 
	reference, loc_code)
			VALUES (".db_escape($po_number).", ".db_escape($date).", "
			.db_escape($supplier_id).", ".db_escape($reference).", ".db_escape($location).")";

	db_query($sql, "A grn batch record could not be inserted.");

	return db_insert_id();
}

//-------------------------------------------------------------------------------------------------------------

function add_grn_detail_item_voucher($grn_batch_id, $po_detail_item, $item_code, $description, $standard_unit_cost,
	$quantity_received, $price, $quantity,$color,$cap,$capcolor)
{
	$sql = "UPDATE ".TB_PREF."purch_order_details
        SET quantity_received = quantity_received + ".db_escape($quantity_received).",
        std_cost_unit=".db_escape($standard_unit_cost).",
        quantity_ordered=".db_escape($quantity).",
        act_price=".db_escape($price).",
        color=".db_escape($color).",
        cap=".db_escape($cap).",
        capcolor=".db_escape($capcolor)."
        
        WHERE po_detail_item = ".db_escape($po_detail_item);

	db_query($sql, "a purchase order details record could not be updated. This receipt of goods has not been processed ");

	$sql = "INSERT INTO ".TB_PREF."grn_items (grn_batch_id, po_detail_item, item_code, 
	description, qty_recd,color,cap,capcolor)
		VALUES (".db_escape($grn_batch_id).", "
		.db_escape($po_detail_item).",
		 ".db_escape($item_code).",
		  ".db_escape($description).",
		   ".db_escape($quantity_received).",
		   ".db_escape($color).",
		   ".db_escape($cap).",
		   ".db_escape($capcolor)."
		
		
		)";

	db_query($sql, "A GRN detail item could not be inserted.");

	return db_insert_id();
}

//----------------------------------------------------------------------------------------
//function get_grn_batch_from_item($item)
//{
//	$sql = "SELECT grn_batch_id FROM ".TB_PREF."grn_items WHERE id=".db_escape($item);
//	$result = db_query($sql, "Could not retreive GRN batch id");
//	$row = db_fetch_row($result);
//	return $row[0];
//}
//
//function get_grn_batch($grn)
//{
//	$sql = " SELECT * FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn);
//	$result = db_query($sql, "Could not retreive GRN batch id");
//	return db_fetch($result);
//}
//
//function set_grn_item_credited(&$entered_grn, $supplier, $transno, $date)
//{
//	$mcost = update_average_material_cost($supplier, $entered_grn->item_code,
//		$entered_grn->chg_price, $entered_grn->this_quantity_inv, $date);
//
// 	$sql = "SELECT ".TB_PREF."grn_batch.*, ".TB_PREF."grn_items.*
//    	FROM ".TB_PREF."grn_batch, ".TB_PREF."grn_items
//    	WHERE ".TB_PREF."grn_items.grn_batch_id=".TB_PREF."grn_batch.id
//		AND ".TB_PREF."grn_items.id=".db_escape($entered_grn->id)."
//    	AND ".TB_PREF."grn_items.item_code=".db_escape($entered_grn->item_code);
//	$result = db_query($sql, "Could not retreive GRNS");
//	$myrow = db_fetch($result);
//
//	$sql = "UPDATE ".TB_PREF."purch_order_details
//        SET quantity_received = quantity_received + "
//        	.db_escape($entered_grn->this_quantity_inv).",
//        quantity_ordered = quantity_ordered + "
//        .db_escape($entered_grn->this_quantity_inv).",
//        qty_invoiced = qty_invoiced + ".db_escape($entered_grn->this_quantity_inv).",
//        std_cost_unit=".db_escape($mcost).",
//        act_price=".db_escape($entered_grn->chg_price)."
//        WHERE po_detail_item = ".$myrow["po_detail_item"];
//	db_query($sql, "a purchase order details record could not be updated. This receipt of goods has not been processed ");
//
//	//$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=0, quantity_inv=0 WHERE id=$entered_grn->id";
//	$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=qty_recd+".db_escape($entered_grn->this_quantity_inv)
//	.",quantity_inv=quantity_inv+".db_escape($entered_grn->this_quantity_inv)
//	." WHERE id=".db_escape($entered_grn->id);
//	db_query($sql);
//
//    add_stock_move(ST_SUPPCREDIT, $entered_grn->item_code, $transno, $myrow['loc_code'], $date, "",
//       	$entered_grn->this_quantity_inv, $mcost, $supplier, 1, $entered_grn->chg_price);
//}
//
//function get_grn_items($grn_batch_id=0, $supplier_id="", $outstanding_only=false,
//	$is_invoiced_only=false, $invoice_no=0, $begin="", $end="")
//{
//    $sql = "SELECT ".TB_PREF."grn_batch.*, ".TB_PREF."grn_items.*, "
//    	.TB_PREF."purch_order_details.unit_price,"
//    	.TB_PREF."purch_order_details.act_price,"
//    	.TB_PREF."purch_order_details.quantity_ordered,"
//		.TB_PREF."purch_order_details.std_cost_unit, units
//    	FROM ".TB_PREF."grn_batch, ".TB_PREF."grn_items, "
//    	.TB_PREF."purch_order_details, ".TB_PREF."stock_master";
//    if ($invoice_no != 0)
//    	$sql .= ", ".TB_PREF."supp_invoice_items";
//    $sql .= " WHERE ".TB_PREF."grn_items.grn_batch_id=".TB_PREF."grn_batch.id
//		AND ".TB_PREF."grn_items.po_detail_item=".TB_PREF."purch_order_details.po_detail_item";
//	if ($invoice_no != 0)
//		$sql .= " AND ".TB_PREF."supp_invoice_items.supp_trans_type=".ST_SUPPINVOICE." AND
//			".TB_PREF."supp_invoice_items.supp_trans_no=$invoice_no AND
//			".TB_PREF."grn_items.id=".TB_PREF."supp_invoice_items.grn_item_id";
//	$sql .= " AND ".TB_PREF."stock_master.stock_id=".TB_PREF."grn_items.item_code ";
//
//	if ($begin != "")
//		$sql .= " AND ".TB_PREF."grn_batch.delivery_date>='".date2sql($begin)."'";
//	if ($end != "")
//		$sql .= " AND ".TB_PREF."grn_batch.delivery_date<='".date2sql($end)."'";
//	if ($grn_batch_id != 0)
//		$sql .= " AND ".TB_PREF."grn_batch.id=".db_escape($grn_batch_id)
//			." AND ".TB_PREF."grn_items.grn_batch_id=".db_escape($grn_batch_id);
//
//	if ($is_invoiced_only)
//		$sql .= " AND ".TB_PREF."grn_items.quantity_inv > 0";
//
//	if ($outstanding_only)
//    	$sql .= " AND ".TB_PREF."grn_items.qty_recd - ".TB_PREF."grn_items.quantity_inv > 0";
//
//	if ($supplier_id != "")
//		$sql .= " AND ".TB_PREF."grn_batch.supplier_id =".db_escape($supplier_id);
//
//	$sql .= " ORDER BY ".TB_PREF."grn_batch.delivery_date, ".TB_PREF."grn_batch.id, ".TB_PREF."grn_items.id";
//
//	return db_query($sql, "Could not retreive GRNS");
//}
//
////----------------------------------------------------------------------------------------
//
//// get the details for a given grn item
//
//function get_grn_item_detail($grn_item_no)
//{
//	$sql = "SELECT ".TB_PREF."grn_items.*, ".TB_PREF."purch_order_details.unit_price,
//    	".TB_PREF."grn_items.qty_recd - ".TB_PREF."grn_items.quantity_inv AS QtyOstdg,
//    	".TB_PREF."purch_order_details.std_cost_unit
//		FROM ".TB_PREF."grn_items, ".TB_PREF."purch_order_details, ".TB_PREF."stock_master
//		WHERE ".TB_PREF."grn_items.po_detail_item=".TB_PREF."purch_order_details.po_detail_item
// 			AND ".TB_PREF."stock_master.stock_id=".TB_PREF."grn_items.item_code
//			AND ".TB_PREF."grn_items.id=".db_escape($grn_item_no);
//
//	$result = db_query($sql, "could not retreive grn item details");
//	return db_fetch($result);
//}

//----------------------------------------------------------------------------------------

//function read_grn_items_to_order($grn_batch, &$order)
//{
//	$result = get_grn_items($grn_batch);
//
//  	if (db_num_rows($result) > 0)
//  	{
//
//	 	while ($myrow = db_fetch($result))
//	 	{
//	 		if ($myrow['qty_recd'] == 0 && $myrow['quantity_inv'] == 0)
//				continue; // 2011-01-18 Joe Hunt. We will not have empty credited rows.
//		 	if (is_null($myrow["units"]))
//		 	{
//				$units = "";
//		 	}
//		 	else
//		 	{
//				$units = $myrow["units"];
//		 	}
//
//			$order->add_to_order($order->lines_on_order, $myrow["item_code"],
//				$myrow["qty_recd"],$myrow["description"], $myrow["unit_price"],$units,
//				sql2date($myrow["delivery_date"]), $myrow["quantity_inv"],
//				$myrow["qty_recd"],0,$myrow["color"],$myrow["cap"],$myrow["capcolor"]);
//
//			$order->line_items[$order->lines_on_order-1]->po_detail_rec = $myrow["po_detail_item"];
//		} /* line po from purchase order details */
//  	} //end of checks on returned data set
//}
//
////----------------------------------------------------------------------------------------
//
//// read a grn into an order class
//
//function read_grn($grn_batch, &$order)
//{
//	$sql= "SELECT *	FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn_batch);
//
//	$result = db_query($sql, "The grn sent is not valid");
//
//	$row = db_fetch($result);
//	$po_number = $row["purch_order_no"];
//
//	$result = read_po_header($po_number, $order);
//
//	if ($result)
//	{
//
//		$order->trans_type = ST_SUPPRECEIVE;
//		$order->orig_order_date = sql2date($row["delivery_date"]);
//		$order->Location = $row["loc_code"];
//		$order->reference = $row["reference"];
//
//		read_grn_items_to_order($grn_batch, $order);
//	}
//}
//
////----------------------------------------------------------------------------------------------------------
//
//// get the GRNs (batch info not details) for a given po number
//
//function get_po_grns($po_number)
//{
//    $sql = "SELECT * FROM ".TB_PREF."grn_batch WHERE purch_order_no=".db_escape($po_number);
//
//	return db_query($sql, "The grns for the po $po_number could not be retreived");
//}
//
////----------------------------------------------------------------------------------------------------------
//
//function exists_grn($grn_batch)
//{
//	$sql = "SELECT id FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn_batch);
//	$result = db_query($sql, "Cannot retreive a grn");
//
//    return (db_num_rows($result) > 0);
//}
//
////----------------------------------------------------------------------------------------------------------
//
//function exists_grn_on_invoices($grn_batch)
//{
//	$sql = "SELECT ".TB_PREF."supp_invoice_items.id FROM ".TB_PREF."supp_invoice_items,".TB_PREF."grn_items
//		WHERE ".TB_PREF."supp_invoice_items.grn_item_id=".TB_PREF."grn_items.id
//		AND quantity != 0
//		AND grn_batch_id=".db_escape($grn_batch);
//	$result = db_query($sql, "Cannot query GRNs");
//
//    return (db_num_rows($result) > 0);
//}
//
////----------------------------------------------------------------------------------------------------------
//
//function void_grn($grn_batch)
//{
//	// if this grn is references on any invoices/credit notes, then it
//	// can't be voided
//	if (exists_grn_on_invoices($grn_batch))
//		return false;
//
//	begin_transaction();
//	hook_db_prevoid(ST_SUPPRECEIVE, $grn_batch);
//
//	void_bank_trans(ST_SUPPRECEIVE, $grn_batch, true);
//	void_gl_trans(ST_SUPPRECEIVE, $grn_batch, true);
//
//	// clear the quantities of the grn items in the POs and invoices
//	$result = get_grn_items($grn_batch);
//
//    if (db_num_rows($result) > 0)
//    {
//
//    	while ($myrow = db_fetch($result))
//        {
//
//        	$sql = "UPDATE ".TB_PREF."purch_order_details
//                SET quantity_received = quantity_received - " . $myrow["qty_recd"] . "
//                WHERE po_detail_item = " . $myrow["po_detail_item"];
//
//        	db_query($sql, "a purchase order details record could not be voided.");
//        }
//    }
//
//	// clear the quantities in the grn items
//	$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=0, quantity_inv=0
//		WHERE grn_batch_id=".db_escape($grn_batch);
//
//	db_query($sql, "A grn detail item could not be voided.");
//
//    // clear the stock move items
//    void_stock_move(ST_SUPPRECEIVE, $grn_batch);
//
//	commit_transaction();
//
//	return true;
//}

//----------------------------------------------------------------------------------------------------------

?>