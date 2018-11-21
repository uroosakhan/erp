<?php

//------------------- update average material cost ---------------------------------------------- 
function update_average_material_cost($supplier, $stock_id, $price, $qty, $date, $adj_only=false,$units_id,$con_factor,$type,$grn_id)
{

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

	// Handle if inventory will become negative
    // Skip negative inventory adjustment for case of adjust_only
    
    $prefs = get_company_prefs();
    $item = get_item($stock_id);
    
    if (is_inventory_item($stock_id) && !$adj_only) 
        handle_negative_inventory($stock_id, $qty, $price_in_home_currency, $date);
	
	$sql = "SELECT mb_flag, material_cost FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql);
	$myrow = db_fetch($result);
	$material_cost = $myrow['material_cost'];
	
//	if($prefs['alt_uom'] == 1 && $units_id != $item['units'] && $type == 20 && $units_id != '')
//	$material_cost = $myrow['material_cost'] / $con_factor;
	
	$cost_adjust = false;

	$qoh = get_qoh_on_date($stock_id);

	if ($adj_only)
	{
		if ($qoh > 0)
			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	$qoh;
	//	display_error($qoh."Material".$material_cost."qty".$qty."price"//.$price_in_home_currency);
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
			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	($qoh + $qty);
	}

  


//--------Ryan 03-10-18
// if($type == 43)
// {
//     $sql = "UPDATE ".TB_PREF."stock_moves SET standard_cost = ".db_escape($material_cost) ." 
//             WHERE stock_id =".db_escape($stock_id)."
//             AND trans_no =".db_escape($grn_id)."
//             AND type =".db_escape(25)." ";
//     db_query($sql, "The stock moves standard cost  cannot be updated  ");
// }
//-------------------


  //  if($prefs['alt_uom'] == 1 && $units_id != $item['units'] && $units_id != '')
  //      $material_cost = $material_cost * $con_factor;



	if ($cost_adjust) // Material_cost replaced with price
		adjust_deliveries($stock_id, $price_in_home_currency_, $date);
	$sql = "UPDATE ".TB_PREF."stock_master SET material_cost=".db_escape($material_cost)."
		WHERE stock_id=".db_escape($stock_id);

	db_query($sql,"The cost details for the inventory item could not be updated");
	return $material_cost;
}
function update_average_material_cost_import($supplier, $stock_id, $price, $qty, $date, $adj_only=false)
{

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

	// Handle if inventory will become negative
    // Skip negative inventory adjustment for case of adjust_only
    if (is_inventory_item($stock_id) && !$adj_only) 
        handle_negative_inventory($stock_id, $qty, $price_in_home_currency, $date);
	
	$sql = "SELECT mb_flag, material_cost FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql);
	$myrow = db_fetch($result);
	$material_cost = $myrow['material_cost'];
	
	$cost_adjust = false;

	$qoh = get_qoh_on_date($stock_id);

	if ($adj_only)
	{
		if ($qoh > 0)
			$material_cost = ($material_cost + $price_in_home_currency) ;
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
			$material_cost = ($qoh * $material_cost + $qty * $price_in_home_currency) /	($qoh + $qty);
	}

	if ($cost_adjust) // Material_cost replaced with price
		adjust_deliveries($stock_id, $price_in_home_currency_, $date);
	$sql = " ".TB_PREF."stock_master SET material_cost=".db_escape($material_cost)."
		WHERE stock_id=".db_escape($stock_id);

	db_query($sql,"The cost details for the inventory item could not be updated");
	return $material_cost;
}

//-------------------------------------------------------------------------------------------------------------

function add_grn(&$po)
{
	global $Refs;

	$date_ = $po->orig_order_date;

	begin_transaction();
	hook_db_prewrite($po, ST_SUPPRECEIVE);

	if (!is_company_currency($po->curr_code))
	{
		if (!$po->ex_rate)
			$po->ex_rate = get_date_exchange_rate($po->curr_code, $date_);
		else
			add_new_exchange_rate($po->curr_code, $date_, $po->ex_rate);
	} else
		$po->ex_rate = 1;

$grn = add_grn_batch($po->order_no,$po->supplier_id, $po->reference, $po->Location,$date_, $po->ex_rate, $po->requisition_no ,$po->h_text1,$po->h_text2,$po->h_text3,$po->h_comb1,$po->h_comb2,$po->h_comb3,
$po->Comments1,$po->Comments2,$po->dc_no,$po->transaction_type,$po->discount1);

    $clearing_act = get_company_pref('grn_clearing_act');
	if ($clearing_act) {	// otherwise GRN clearing account is not used
	    $total = 0;
	}
	foreach ($po->line_items as $line_no => $order_line)
	{
		if ($order_line->receive_qty != 0 && $order_line->receive_qty != "" && isset($order_line->receive_qty))
		{
			$stock_gl_code = get_stock_gl_code($order_line->stock_id);

			/*Update sales_order_details for the new quantity received and the standard cost used for postings to GL 
			  and recorded in the stock movements for FIFO/LIFO stocks valuations*/
			//------------------- update average material cost and clearing account --------------------------------
//            display_error(( 1 - $order_line->discount_percent) * $order_line->price  );
//            die;
            $pref = get_company_prefs();
            if($pref['disc_in_amount'] == 1) {
                $price = $order_line->price - $order_line->discount_percent;
            }
            else {
                $price = (1 - $order_line->discount_percent) * $order_line->price;
            }
			if (is_inventory_item($order_line->stock_id))
			{

				if ($clearing_act)
					$total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $stock_gl_code["inventory_account"],
						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
					    $order_line->taxfree_charge_value($po), $po->supplier_id, '', 0, $order_line->stock_id);
 				update_average_material_cost($po->supplier_id, $order_line->stock_id, $price,
					$order_line->receive_qty, $date_,null,$order_line->units_id,$order_line->con_factor);


			}
			//------------------------------------------------------------------------------------------------------
			if ($order_line->qty_received == 0)
			{
				/*This must be the first receipt of goods against this line */
			   	/*Need to get the standard cost as it is now so we can process GL jorunals later*/
			   	$order_line->standard_cost = get_unit_cost($order_line->stock_id);
			}
			// Update the purchase data table
			add_or_update_purchase_data($po->supplier_id, $order_line->stock_id, $price,
				$order_line->item_description);

			$itm=get_item($order_line->stock_id);
			$pref=get_company_prefs();
			if($pref['batch'] == 1) {
				if ($itm['batch_status'] == 1 && $order_line->grn_batch == "") {
				} else {
					$result = get_batch_id($order_line->grn_batch);
					if (!$result['id'])
						add_batch($order_line->grn_batch,$order_line->exp_date);
				}
				$batch_id=get_batch_id($order_line->grn_batch);
				$batch=$batch_id['id'];
			}
			else{
				$batch="";
			}
		/*Need to insert a grn item */ // also need to check for over-receive.(within allowance)
			if ($order_line->receive_qty + $order_line->qty_received > $order_line->quantity)
				$order_line->quantity = $order_line->receive_qty + $order_line->qty_received;
			$item=get_item($order_line->stock_id);
			if($pref['alt_uom'] == 1) {
				if ($order_line->units_id != $item['units']) {
					if ($item['con_type'] == 0) {
						$receive_qty = $order_line->receive_qty / $order_line->con_factor;
						$quantity = $order_line->quantity / $order_line->con_factor;
					} else {
						$receive_qty = $order_line->con_factor / $order_line->receive_qty;
						$quantity = $order_line->con_factor / $order_line->quantity;
					}
				} else {
					$receive_qty = $order_line->receive_qty;
					$quantity = $order_line->quantity;
				}
				$myrow_factors = get_company_item_pref('con_factor');
				if ($myrow_factors['purchase_enable'] == 0) {

					$item_info = get_item_edit_info($order_line->stock_id);
					$order_line->con_factor = $item_info['con_factor'];
				} else {

					$order_line->con_factor;
					$order_line->quantity;

				}
			}
			else{
				$receive_qty = $order_line->receive_qty;
				$quantity = $order_line->quantity;
			}



			$grn_item = add_grn_detail_item($grn, $order_line->po_detail_rec,
				$order_line->stock_id, $order_line->item_description,
				$order_line->standard_cost,/*	$order_line->receive_qty*/$receive_qty, $order_line->price, $quantity,
				$batch,$order_line->text1,$order_line->text2,$order_line->text3,$order_line->text4,$order_line->text5,$order_line->text6,$order_line->text7,
				$order_line->amount1,$order_line->amount2,$order_line->amount3,$order_line->amount4,$order_line->amount5,$order_line->amount6
			,$order_line->date1,$order_line->date2,$order_line->date3
			,$order_line->combo1,$order_line->combo2,$order_line->combo3,$order_line->combo4,$order_line->combo5,
				$order_line->combo6,$order_line->con_factor,$order_line->units_id,$order_line->discount_percent);

			$po->line_items[$line_no]->grn_item_id = $grn_item;
			/* Update location stock records - NB  a po cannot be entered for a service/kit parts done automatically */
			add_stock_move(ST_SUPPRECEIVE, $order_line->stock_id, $grn, $po->Location, $date_, "",
				$receive_qty, $order_line->standard_cost,
                $order_line->taxfree_charge_value($po)/$order_line->receive_qty,$po->supplier_id,$order_line->discount_percent,$order_line->text1,$order_line->text2,$order_line->text3,$order_line->text4,$order_line->text5,$order_line->text6,
				$order_line->amount1,$order_line->amount2,$order_line->amount3,$order_line->amount4,$order_line->amount5,$order_line->amount6
				,$order_line->date1,$order_line->date2,$order_line->date3
				,$order_line->combo1,$order_line->combo2,$order_line->combo3,$order_line->combo4,$order_line->combo5,
				$order_line->combo6,$order_line->units_id,$order_line->con_factor,$batch,null,null,$order_line->text7);
            //ansar 26-08-2017 $order_line->price change into $order_line->taxfree_charge_value($po)/$order_line->receive_qty

		} /*quantity received is != 0 */
	} /*end of order_line loop */

	if ($clearing_act && $total != 0.0) {

		$total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $clearing_act,
			0, 0, -$total, null);
	}
	$Refs->save(ST_SUPPRECEIVE, $grn, $po->reference);

	add_audit_trail(ST_SUPPRECEIVE, $grn, $date_);

	$po->trans_no = $grn;
	hook_db_postwrite($po, ST_SUPPRECEIVE);
	commit_transaction();

	return $grn;
}

//----------------------------------------------------------------------------------------

function add_grn_batch($po_number, $supplier_id, $reference, $location, $date_, $rate,$requisition_no,$h_text1,
                       $h_text2,$h_text3,$h_comb1,$h_comb2,$h_comb3,$Comments1,$Comments2,$dc_no,$transaction_type,$discount1)
{
	$date = date2sql($date_);

	$sql = " INSERT INTO ".TB_PREF."grn_batch (purch_order_no, delivery_date, supplier_id, reference, loc_code,
	 rate,requisition_no,h_text1,h_text2,h_text3,h_comb1,h_comb2,h_comb3,Comments1,Comments2,dc_no,transaction_type,discount1)
			VALUES (".db_escape($po_number).", ".db_escape($date).", "
			.db_escape($supplier_id).", ".db_escape($reference).", ".db_escape($location)."
			,".db_escape($rate)."
			,".db_escape($requisition_no)."
			,".db_escape($h_text1)."
			,".db_escape($h_text2)."
			,".db_escape($h_text3)."
			,".db_escape($h_comb1)."
			,".db_escape($h_comb2)."
			,".db_escape($h_comb3)."
			,".db_escape($Comments1)."
			,".db_escape($Comments2)."
			,".db_escape($dc_no)."
			,".db_escape($transaction_type)."
			,".db_escape($discount1)."
)";

	db_query($sql, "A grn batch record could not be inserted.");

	return db_insert_id();
}

//-------------------------------------------------------------------------------------------------------------

function add_grn_detail_item($grn_batch_id, $po_detail_item, $item_code, $description, $standard_unit_cost,
	$quantity_received, $price, $quantity,$batch,$text1, $text2, $text3, $text4, $text5, $text6, $text7,$amount1, $amount2, $amount3, $amount4,$amount5, $amount6, $date1, $date2, $date3, $combo1, $combo2, $combo3,$combo4, $combo5, $comb6, $con_factor, $units_id,$discount_percent)
{
	$sql = "UPDATE ".TB_PREF."purch_order_details
        SET quantity_received = quantity_received + ".db_escape($quantity_received).",
        std_cost_unit=".db_escape($standard_unit_cost).",
        quantity_ordered=".db_escape($quantity).",
        act_price=".db_escape($price)."
        WHERE po_detail_item = ".db_escape($po_detail_item);
	db_query($sql, "a purchase order details record could not be updated. This receipt of goods has not been processed ");

	$sql = " INSERT INTO ".TB_PREF."grn_items (grn_batch_id, po_detail_item, item_code, description,
	 qty_recd,grn_batch,text1, text2, text3, text4,text5, text6,text7,
				amount1, amount2, amount3, amount4,amount5, amount6,
				date1, date2, date3,
				combo1, combo2, combo3,combo4, combo5, combo6, con_factor, units_id,discount_percent)
		VALUES (".db_escape($grn_batch_id).", "
		.db_escape($po_detail_item).", ".db_escape($item_code).", ".db_escape($description)
		.", ".db_escape($quantity_received).", ".db_escape($batch).",".db_escape($text1).",".db_escape($text2).",".db_escape($text3).", ".db_escape($text4).",".db_escape($text5).",".db_escape($text6).",".db_escape($text7).",
		".db_escape($amount1).",".db_escape($amount2).",".db_escape($amount3).",".db_escape($amount4).",".db_escape($amount5).",".db_escape($amount6)."
		,".db_escape(date2sql($date1)).",".db_escape(date2sql($date2)).",".db_escape(date2sql($date3)).",".db_escape($combo1).",".db_escape($combo2).",".db_escape($combo3).",".db_escape($combo4).",".db_escape($combo5).",".db_escape($comb6)."
		,".db_escape($con_factor).",".db_escape($units_id).",".db_escape($discount_percent).")";

	db_query($sql, "A GRN detail item could not be inserted.");

	return db_insert_id();
}

//----------------------------------------------------------------------------------------
function get_grn_batch_from_item($item)
{
	$sql = "SELECT grn_batch_id FROM ".TB_PREF."grn_items WHERE id=".db_escape($item);
	$result = db_query($sql, "Could not retreive GRN batch id");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_grn_batch($grn)
{
	$sql = "SELECT * FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn);
	$result = db_query($sql, "Could not retreive GRN batch id");
	return db_fetch($result);
}

function set_grn_item_credited(&$entered_grn, $supplier, $transno, $date)
{
$mcost = update_average_material_cost($supplier, $entered_grn->item_code,
		$entered_grn->chg_price, $entered_grn->this_quantity_inv, $date);

 	$sql = "SELECT grn.*, item.*
    	FROM ".TB_PREF."grn_batch grn,"
    		.TB_PREF."grn_items item
    	WHERE item.grn_batch_id=grn.id
		AND item.id=".db_escape($entered_grn->id)."
    	AND item.item_code=".db_escape($entered_grn->item_code);

	$result = db_query($sql, "Could not retreive GRNS");
	$myrow = db_fetch($result);

	$sql = "UPDATE ".TB_PREF."purch_order_details
        SET quantity_received = quantity_received + "
        	.db_escape($entered_grn->this_quantity_inv).",
        quantity_ordered = quantity_ordered + "
        .db_escape($entered_grn->this_quantity_inv).",
        qty_invoiced = qty_invoiced + ".db_escape($entered_grn->this_quantity_inv).",
        std_cost_unit=".db_escape($mcost).",
        act_price=".db_escape($entered_grn->chg_price)."
        WHERE po_detail_item = ".$myrow["po_detail_item"];
	db_query($sql, "a purchase order details record could not be updated. This receipt of goods has not been processed ");

	//$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=0, quantity_inv=0 WHERE id=$entered_grn->id";
	$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=qty_recd+".db_escape($entered_grn->this_quantity_inv)
	.",quantity_inv=quantity_inv+".db_escape($entered_grn->this_quantity_inv)
	." WHERE id=".db_escape($entered_grn->id);
	db_query($sql);
//---------------------------conversion factor --------------------------------------
	$item=get_item($entered_grn->item_code);
	$pref=get_company_prefs();
	if($pref['alt_uom'] == 1) {
		if ($entered_grn->units_id != $item['units']) {
			if ($item['con_type'] == 0) {
				$this_quantity_inv = $entered_grn->this_quantity_inv / $entered_grn->con_factor;
//				$quantity = $entered_grn->this_quantity_inv / $order_line->con_factor;
			} else {
				$this_quantity_inv = $entered_grn->con_factor / $entered_grn->this_quantity_inv;
//				$quantity = $order_line->con_factor / $entered_grn->this_quantity_inv;
			}
		} else {
			$this_quantity_inv = $entered_grn->this_quantity_inv;
//			$quantity = $order_line->quantity;
		}
		$myrow_factors = get_company_item_pref('con_factor');
		if ($myrow_factors['purchase_enable'] == 0) {

			$item_info = get_item_edit_info($entered_grn->item_code);
			$entered_grn->con_factor = $item_info['con_factor'];
		} else {

			$entered_grn->con_factor;
			$entered_grn->this_quantity_inv;

		}
	}
	else{
		$this_quantity_inv = $entered_grn->this_quantity_inv;

	}
//---------------------------conversion factor end --------------------------------------


    add_stock_move(ST_SUPPCREDIT, $entered_grn->item_code, $transno, $myrow['loc_code'], $date, "",
		$this_quantity_inv, $mcost, $entered_grn->chg_price,0,0,
        $entered_grn->text1,$entered_grn->text2,$entered_grn->text3,$entered_grn->text4,
        $entered_grn->text5,$entered_grn->text6
		,$entered_grn->amount1,$entered_grn->amount2,$entered_grn->amount3,$entered_grn->amount4,$entered_grn->amount5,$entered_grn->amount6
		,$entered_grn->date1,$entered_grn->date2,$entered_grn->date3
		,$entered_grn->combo1,$entered_grn->combo2,$entered_grn->combo3,$entered_grn->combo4,$entered_grn->combo5,
		$entered_grn->combo6,$entered_grn->units_id,$entered_grn->con_factor,$entered_grn->grn_batch,$entered_grn->text7);
}
// function get_grn_items($grn_batch_id=0, $supplier_id="", $outstanding_only=false,$is_invoiced_only=false, $invoice_no=0, $begin="", $end="",$transaction_type,$order_no)
// {
//     $sql = "SELECT grn.*, grn_item.*,
// 		grn_item.quantity_inv".($invoice_no ? "-invoice_item.quantity" : '')."  quantity_inv,
//     	po_item.unit_price,
//     	po_item.act_price,
//     	po_item.quantity_ordered,
//     	po_item.std_cost_unit, 	units
//     	FROM	".TB_PREF."grn_batch grn,"
//     			.TB_PREF."purch_order_details po_item, "
//     			.TB_PREF."stock_master stock_item,"
// 	   			.TB_PREF."grn_items grn_item ";
 
//     if ($invoice_no != 0) {
//     	$sql .= " LEFT JOIN ".TB_PREF."supp_invoice_items invoice_item ON ";

// 		$inv_cond = "invoice_item.supp_trans_type=".ST_SUPPINVOICE." AND invoice_item.supp_trans_no ";
// 		if (is_array($invoice_no))
// 			$inv_cond .= " IN (".implode(',' , $invoice_no) . ")";
// 		else
// 			$inv_cond .= " = $invoice_no";
// 		$sql .= "($inv_cond) AND grn_item.id=invoice_item.grn_item_id";
// 	}
//     $sql .= " WHERE grn_item.grn_batch_id=grn.id
// 		AND grn_item.po_detail_item=po_item.po_detail_item
// 		AND grn.approval=0";

// 	if ($outstanding_only)
//     	$sql .= " AND (".($invoice_no ? "$inv_cond OR ":'')."grn_item.qty_recd - grn_item.quantity_inv > 0)";

// 	$sql .= " AND stock_item.stock_id=grn_item.item_code ";
	
// 	if($transaction_type != 0)
// 	$sql .= " AND grn.transaction_type=".db_escape($transaction_type)."";



// 	if ($begin != "")
// 		$sql .= " AND grn.delivery_date>='".date2sql($begin)."'";
// 	if ($end != "")
// 		$sql .= " AND grn.delivery_date<='".date2sql($end)."'";
// 	if ($grn_batch_id != 0)
// 		$sql .= " AND grn.id=".db_escape($grn_batch_id)
// 			." AND grn_item.grn_batch_id=".db_escape($grn_batch_id);

// 	if ($is_invoiced_only)
// 		$sql .= " AND grn_item.quantity_inv > 0";

// 	if ($supplier_id != "")
// 		$sql .= " AND grn.supplier_id =".db_escape($supplier_id);

// if ($order_no != "")
// 		$sql .= " AND grn.purch_order_no =".db_escape($order_no);



// 	$sql .= " ORDER BY grn.delivery_date, grn.id, grn_item.id";

// 	return db_query($sql, "Could not retreive GRNS");
// }

function get_grn_items($grn_batch_id=0, $supplier_id="", $outstanding_only=false,$is_invoiced_only=false, $invoice_no=0, $begin="", $end="",$transaction_type,$order_no, $inv_no)
{
    $sql = "SELECT grn.*, grn_item.*,
      grn_item.quantity_inv".($invoice_no ? "-invoice_item.quantity" : '')."  quantity_inv,
       po_item.unit_price,
       po_item.act_price,
       po_item.quantity_ordered,
       po_item.std_cost_unit,     units
       FROM ".TB_PREF."grn_batch grn,"
              .TB_PREF."purch_order_details po_item, "
              .TB_PREF."stock_master stock_item,"
              .TB_PREF."grn_items grn_item ";
    if($invoice_no != 0) {
       $sql .= " LEFT JOIN ".TB_PREF."supp_invoice_items invoice_item ON ";
      $inv_cond = "invoice_item.supp_trans_type=".ST_SUPPINVOICE." 
      AND invoice_item.supp_trans_no ";
      if (is_array($invoice_no))
         $inv_cond .= " IN (".implode(',' , $invoice_no) . ")";
      else
         $inv_cond .= " = $invoice_no";
      $sql .= "($inv_cond) ";
    }
//    if ($inv_no != 0) {
//        display_error("$inv_no");
//     $sql .= " LEFT JOIN ".TB_PREF."supp_invoice_items invoice_item ON ";
//
//    $inv_cond = "invoice_item.supp_trans_type=".ST_SUPPINVOICE."
//    AND invoice_item.supp_trans_no ";
////      if (is_array($inv_no))
////         $inv_cond .= " IN (".implode(',' , $inv_no) . ")";
////      else
////         $inv_cond .= " = $inv_no";
//    $sql .= "($inv_cond) AND grn_item.id=invoice_item.grn_item_id";
//
//        $sql .= " AND invoice_item.supp_trans_no=".db_escape($inv_no)."";
//    }
    $sql .= " WHERE grn_item.grn_batch_id=grn.id
            AND grn_item.po_detail_item=po_item.po_detail_item
            AND grn.approval=0 ";
    if($invoice_no != 0)
        $sql .= " AND grn_item.id=invoice_item.grn_item_id ";



   if ($outstanding_only)
       $sql .= " AND (".($invoice_no ? "$inv_cond OR ":'')."grn_item.qty_recd - grn_item.quantity_inv > 0)";

   $sql .= " AND stock_item.stock_id=grn_item.item_code ";
   
   if($transaction_type != 0)
   $sql .= " AND grn.transaction_type=".db_escape($transaction_type)."";

   if ($begin != "")
      $sql .= " AND grn.delivery_date>='".date2sql($begin)."'";
   if ($end != "")
      $sql .= " AND grn.delivery_date<='".date2sql($end)."'";
   if ($grn_batch_id != 0)
      $sql .= " AND grn.id=".db_escape($grn_batch_id)
         ." AND grn_item.grn_batch_id=".db_escape($grn_batch_id);

   if ($is_invoiced_only)
      $sql .= " AND grn_item.quantity_inv > 0";

   if ($supplier_id != "")
      $sql .= " AND grn.supplier_id =".db_escape($supplier_id);

if ($order_no != "")
      $sql .= " AND grn.purch_order_no =".db_escape($order_no);



   $sql .= " ORDER BY grn.delivery_date, grn.id, grn_item.id";

   return db_query($sql, "Could not retreive GRNS");
}

function get_supp_invoice_id($item)
{
   $sql = "SELECT ".TB_PREF."supp_trans.trans_no 
   FROM ".TB_PREF."supp_trans,".TB_PREF."supp_invoice_items ,".TB_PREF."purch_order_details 
   WHERE ".TB_PREF."supp_trans.trans_no=".TB_PREF."supp_invoice_items.supp_trans_no
   AND ".TB_PREF."supp_invoice_items.po_detail_item_id=".TB_PREF."purch_order_details.po_detail_item
   AND ".TB_PREF."purch_order_details.order_no=".db_escape($item)."
   AND ".TB_PREF."supp_invoice_items.supp_trans_type = 20";
   $result = db_query($sql, "Could not retreive GRN batch id");
   $row = db_fetch_row($result);
   return $row[0];
}

function get_grn_import_items($grn_batch_id=0, $supplier_id="", $outstanding_only=false,
                              $is_invoiced_only=false, $invoice_no=0, $begin="", $end="",
                              $transaction_type,$order_no)
{

    if (!is_array($grn_batch_id))
        $grn_batch_id = array( 0=>$grn_batch_id );

        $sql = "SELECT grn.*, grn_item.*,
		grn_item.quantity_inv".($invoice_no ? "-invoice_item.quantity" : '')."  quantity_inv,
    	po_item.unit_price,
    	po_item.act_price,
    	po_item.quantity_ordered,
    	po_item.std_cost_unit, 	units
    	FROM	".TB_PREF."grn_batch grn,"
    			.TB_PREF."purch_order_details po_item, "
    			.TB_PREF."stock_master stock_item,"
	   			.TB_PREF."grn_items grn_item ";

    if ($invoice_no != 0) {
    	$sql .= " LEFT JOIN ".TB_PREF."supp_invoice_items invoice_item ON ";

		$inv_cond = "invoice_item.supp_trans_type=".ST_SUPPINVOICE." AND invoice_item.supp_trans_no ";
		if (is_array($invoice_no))
			$inv_cond .= " IN (".implode(',' , $invoice_no) . ")";
		else
			$inv_cond .= " = $invoice_no";

		$sql .= "($inv_cond) AND grn_item.id=invoice_item.grn_item_id";
	}

    $sql .= " WHERE grn_item.grn_batch_id=grn.id
		AND grn_item.po_detail_item=po_item.po_detail_item";

	if ($outstanding_only)
    	$sql .= " AND (".($invoice_no ? "$inv_cond OR ":'')."grn_item.qty_recd - grn_item.quantity_inv > 0)";

	$sql .= " AND stock_item.stock_id=grn_item.item_code ";
	$sql .= " AND grn.transaction_type=".db_escape($transaction_type)."";


	if ($begin != "")
		$sql .= " AND grn.delivery_date >='".date2sql($begin)."'";
	if ($end != "")
		$sql .= " AND grn.delivery_date <='".date2sql($end)."'";

   $sql .= " AND (";
    $tr=array();
    foreach ($grn_batch_id as $trans_no)
        $tr[] = "  grn.id=".db_escape($trans_no);
    $sql .= implode(' OR ', $tr);
$sql .= " )";

//	if ($grn_batch_id != 0)
//		$sql .= " AND grn.id=".db_escape($grn_batch_id)
//			." AND grn_item.grn_batch_id=".db_escape($grn_batch_id);

	if ($is_invoiced_only)
		$sql .= " AND grn_item.quantity_inv > 0";

	if ($supplier_id != "")
		$sql .= " AND grn.supplier_id =".db_escape($supplier_id);

if ($order_no != "")
		$sql .= " AND grn.purch_order_no =".db_escape($order_no);


$sql .= "  AND grn_item.grn_batch_id= grn.id";



	$sql .= " ORDER BY grn.delivery_date, grn.id, grn_item.id";

	return db_query($sql, "Could not retreive GRNS");
}


//----------------------------------------------------------------------------------------

// get the details for a given grn item

function get_grn_item_detail($grn_item_no)
{
	$sql = "SELECT grn.*, po.unit_price, grn.qty_recd - grn.quantity_inv AS QtyOstdg,
    		po.std_cost_unit
		FROM ".TB_PREF."grn_items grn,"
			.TB_PREF."purch_order_details po,"
			.TB_PREF."stock_master item
		WHERE grn.po_detail_item=po.po_detail_item
 			AND item.stock_id=grn.item_code
			AND grn.id=".db_escape($grn_item_no);

	$result = db_query($sql, "could not retreive grn item details");
	return db_fetch($result);
}

function get_transaction_type ($id)
{
    $sql = "SELECT transaction_type  FROM ".TB_PREF."grn_batch WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------------

function read_grn_items_to_order($grn_batch, &$order)
{
$transaction_type = get_transaction_type ($grn_batch);

   if($transaction_type==0){

       $result = get_grn_items($grn_batch);

   }
   else{

       $result = get_grn_items($grn_batch,'',false,false,
           0,"","",1);

       }


  	if (db_num_rows($result) > 0)
  	{

	 	while ($myrow = db_fetch($result))
	 	{
	 		if ($myrow['qty_recd'] == 0 && $myrow['quantity_inv'] == 0)
				continue; // We will not have empty credited rows.
		 	if (is_null($myrow["units"]))
		 	{
				$units = "";
		 	}
		 	else
		 	{
				$units = $myrow["units"];
		 	}
			$pref=get_company_prefs();
			if($pref['alt_uom'] == 1 ) {
				$item = get_item($myrow["item_code"]);
				$dec = 2;
				if ($myrow['units_id'] != $item['units']) {
					if ($item['con_type'] == 0) {
						$qty_recd = round2($myrow["qty_recd"] * $myrow['con_factor'], $dec);

					} else {

						$qty_recd = $myrow['con_factor'] / $myrow["qty_recd"];

					}
				} else {
					$qty_recd = $myrow["qty_recd"];
				}
			}
			else{
				$qty_recd =$myrow["qty_recd"];
			}


			$Dimension = get_purchase_order_dimensions($myrow["purch_order_no"]);
			$order->add_to_order($order->lines_on_order, $myrow["item_code"],
				$qty_recd,$myrow["description"], $myrow["unit_price"],$units,
				sql2date($myrow["delivery_date"]), $myrow["quantity_inv"],
				$qty_recd,$myrow["text1"],$myrow["text2"],$myrow["text3"],
                $myrow["text4"],$myrow["text5"],$myrow["text6"],$myrow["text7"],$myrow["amount1"],$myrow["amount2"],$myrow["amount3"]
				,$myrow["amount4"],$myrow["amount5"],$myrow["amount6"],$myrow["date1"],$myrow["date2"],$myrow["date3"]
				,$myrow["combo1"],$myrow["combo2"],$myrow["combo3"]
				,$myrow["combo4"],$myrow["combo5"],$myrow["combo6"],$myrow["grn_batch"],0,$myrow["con_factor"],$myrow["units_id"],
				$Dimension['dimension'], $Dimension['dimension2'],null,null,$myrow["discount_percent"]);

			$order->line_items[$order->lines_on_order-1]->po_detail_rec = $myrow["po_detail_item"];
		} /* line po from purchase order details */
  	} //end of checks on returned data set
}

//----------------------------------------------------------------------------------------

// read a grn into an order class

function read_grn($grn_batch, &$order)
{
	$sql= "SELECT *	FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn_batch);

	$result = db_query($sql, "The grn sent is not valid");

	$row = db_fetch($result);
	$po_number = $row["purch_order_no"];
	$order->ex_rate = $row["rate"];

	$result = read_po_header($po_number, $order);

	if ($result)
	{

		$order->trans_type = ST_SUPPRECEIVE;
		$order->orig_order_date = sql2date($row["delivery_date"]);
		$order->Location = $row["loc_code"];
		$order->reference = $row["reference"];

		read_grn_items_to_order($grn_batch, $order);
	}
}

//----------------------------------------------------------------------------------------------------------

// get the GRNs (batch info not details) for a given po number

function get_po_grns($po_number)
{
    $sql = "SELECT * FROM ".TB_PREF."grn_batch WHERE purch_order_no=".db_escape($po_number);

	return db_query($sql, "The grns for the po $po_number could not be retreived");
}

//----------------------------------------------------------------------------------------------------------

function exists_grn($grn_batch)
{
	$sql = "SELECT id FROM ".TB_PREF."grn_batch WHERE id=".db_escape($grn_batch);
	$result = db_query($sql, "Cannot retreive a grn");

    return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------------------------

function exists_grn_on_invoices($grn_batch)
{
	$sql = "SELECT inv.id
		FROM ".TB_PREF."supp_invoice_items inv,"
			.TB_PREF."grn_items grn
		WHERE inv.grn_item_id=grn.id
		AND quantity != 0
		AND grn_batch_id=".db_escape($grn_batch);
	$result = db_query($sql, "Cannot query GRNs");

    return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------------------------

function void_grn($grn_batch)
{
	// if this grn is references on any invoices/credit notes, then it
	// can't be voided
	if (exists_grn_on_invoices($grn_batch))
		return false;

	begin_transaction();
	hook_db_prevoid(ST_SUPPRECEIVE, $grn_batch);

	void_bank_trans(ST_SUPPRECEIVE, $grn_batch, true);
	void_gl_trans(ST_SUPPRECEIVE, $grn_batch, true);

	// clear the quantities of the grn items in the POs and invoices
//	$result = get_grn_items($grn_batch);
$transaction_type = get_transaction_type ($grn_batch);

   if($transaction_type==0){

       $result = get_grn_items($grn_batch);

   }
   else{

       $result = get_grn_items($grn_batch,'',false,false,
           0,"","",1);

       }
    if (db_num_rows($result) > 0)
    {

    	while ($myrow = db_fetch($result))
        {

        	$sql = "UPDATE ".TB_PREF."purch_order_details
                SET quantity_received = quantity_received - " . $myrow["qty_recd"] . "
                WHERE po_detail_item = " . $myrow["po_detail_item"];

        	db_query($sql, "a purchase order details record could not be voided.");
        }
    }

	// clear the quantities in the grn items
	$sql = "UPDATE ".TB_PREF."grn_items SET qty_recd=0, quantity_inv=0
		WHERE grn_batch_id=".db_escape($grn_batch);

	db_query($sql, "A grn detail item could not be voided.");

    // clear the stock move items
    void_stock_move(ST_SUPPRECEIVE, $grn_batch);

	commit_transaction();

	return true;
}

//----------------------------------------------------------------------------------------------------------


function get_purchase_order_dimensions($order_no)
{
	$sql = "SELECT * FROM ".TB_PREF."purch_orders WHERE order_no = ".db_escape($order_no);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_dimension_name_($dimension_id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
