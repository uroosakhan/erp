<?php

// get supplier id from table
function get_id_from_supplier_table($name)
{
	$sql = "SELECT *
			FROM ".TB_PREF."suppliers
			WHERE supplier_id = ".db_escape($name);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

function get_stock_master_info_purchase($stock_id)
{
    $sql = "SELECT *
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($stock_id);
    $result = db_query($sql, "The standard cost cannot be retrieved");
    return db_fetch($result);
}


function add_OB_import_po($supplier_id, $date, $item, $qty, $price, $location, $payment_terms, $dimension_id, $dimension2_id)
{
    global $Refs;
    // abc1
    begin_transaction();
    hook_db_prewrite($po_obj, ST_PURCHORDER);
    $trans_type = ST_PURCHORDER;
    $Comments = 'Opening Balance';
    $orig_order_date = date2sql($date);
    $Location = $location;
    $tax_included = 0;
    $payments = $payment_terms;
    $dimension = $dimension_id;
    $dimension2 = $dimension2_id;
    $reference = 'auto'/*$Refs->get_next($trans_type, null, array('date' => $orig_order_date,
        'supplier' => $supplier_id['supplier_id']))*/;

    $sql = "INSERT INTO ".TB_PREF."purch_orders (supplier_id, Comments, ord_date, reference, 
     	requisition_no, into_stock_location, delivery_address, total, tax_included,
     	payments, dimension, dimension2) VALUES(";
    $sql .= db_escape($supplier_id['supplier_id']) . "," .
        db_escape($Comments) . "," .
        db_escape($orig_order_date) . ", " .
        db_escape($reference) . ", " .
        db_escape($supplier_id['supp_name']) . ", " .
        db_escape($Location) . ", " .
        db_escape($supplier_id['address']) . ", " .
        db_escape($price). ", " .
        db_escape($tax_included). ", " .
        db_escape($payments). ",".
        db_escape($dimension). ",".
        db_escape($dimension2). ")";

    db_query($sql, "The purchase order header record could not be inserted");

    /*Get the auto increment value of the order number created from the sql above */
    $order_no = db_insert_id();
    $req_del_date = $orig_order_date;

    $sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, item_code, description, delivery_date,	
            unit_price, act_price, quantity_ordered, quantity_received,	qty_invoiced, units_id) VALUES (";
    $sql .= $order_no . ", " . db_escape($item['stock_id']). "," .
        db_escape($item['description']). "," .
        db_escape($req_del_date) . "," .
        db_escape($price) . ", " .
        db_escape($price) . ", " .
        db_escape($qty). ", " .
        db_escape($qty). ", " .
        db_escape($qty). ", " .
        db_escape($item['units']).")";
        db_query($sql, "One of the purchase order detail records could not be inserted");

    $Refs->save($trans_type, $order_no, $reference);

    add_audit_trail($trans_type, $order_no, $orig_order_date);
    hook_db_postwrite($po_obj, $trans_type);
    commit_transaction();

    return $order_no;
}


function add_OB_import_grn($order_no, $supplier_id, $date, $item,
         $qty, $price, $location, $payment_terms,
         $dimension_id, $dimension2_id)
{
//  abc2
    global $Refs;
    $date_ = $date;
    $reference = 'auto';
    $ex_rate = 0;
    begin_transaction();
    hook_db_prewrite($po, ST_SUPPRECEIVE);
    if (!is_company_currency($supplier_id['curr_code']))
    {
        if (!$ex_rate)
            $ex_rate = get_date_exchange_rate($supplier_id['curr_code'], $date_);
        else
            add_new_exchange_rate($supplier_id['curr_code'], $date_, $ex_rate);
    } else
        $ex_rate = 1;
    $grn = add_grn_batch($order_no, $supplier_id['supplier_id'], $reference, $location, $date_, $ex_rate, $supplier_id['supp_name']);
    $clearing_act = get_company_pref('grn_clearing_act');
//    if ($clearing_act) {	// otherwise GRN clearing account is not used
//        $total = 0;
//    }
//    foreach ($po->line_items as $line_no => $order_line)
    {
//        if ($order_line->receive_qty != 0 && $order_line->receive_qty != "" && isset($order_line->receive_qty))
        {
            $stock_gl_code = get_stock_gl_code($item['stock_id']);

            /*Update sales_order_details for the new quantity received and the standard cost used for postings to GL
              and recorded in the stock movements for FIFO/LIFO stocks valuations*/
            //------------------- update average material cost and clearing account --------------------------------
//            if (is_inventory_item($order_line->stock_id))

//                if ($clearing_act)
//                    $total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $stock_gl_code["inventory_account"],
//                        $stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//                        $price, $supplier_id['supplier_id'], '', 0, $item['stock_id']);
//                update_average_material_cost($po->supplier_id, $order_line->stock_id, $order_line->price,
//                    $order_line->receive_qty, $date_);
            //------------------------------------------------------------------------------------------------------
//            if ($order_line->qty_received == 0)

                /*This must be the first receipt of goods against this line */
                /*Need to get the standard cost as it is now so we can process GL jorunals later*/
//                $order_line->standard_cost = get_unit_cost($order_line->stock_id);

            // Update the purchase data table
            add_or_update_purchase_data($supplier_id['supplier_id'], $item['stock_id'], $price,
                $item['description']);

            /*$itm=get_item($order_line->stock_id);
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
            }*/
            /*Need to insert a grn item */ // also need to check for over-receive.(within allowance)
//            if ($order_line->receive_qty + $order_line->qty_received > $order_line->quantity)
//                $order_line->quantity = $order_line->receive_qty + $order_line->qty_received;
//            $item=get_item($order_line->stock_id);
            /*if($pref['alt_uom'] == 1) {
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
            }*/

            $grn_item = add_grn_detail_item($grn, $order_no, $item['stock_id'],
            $item['description'], 0, $qty, $price, $qty,
                0,'','','','','','',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,$item['units']);

//            $po->line_items[$line_no]->grn_item_id = $grn_item;
            /* Update location stock records - NB  a po cannot be entered for a service/kit parts done automatically */
            add_stock_move(ST_SUPPRECEIVE, $item['stock_id'], $grn, $location, $date_, "",
            $qty, 0, $price, $supplier_id['supplier_id'], 0,0, 0,0,0,0,0,0,
            0,0,0,0,0,0,
            0,0,0,0,0,0,0,
            0, $item['units'],0,0,$location);
            //ansar 26-08-2017 $order_line->price change into $order_line->taxfree_charge_value($po)/$order_line->receive_qty

        } /*quantity received is != 0 */
    } /*end of order_line loop */

//    if ($clearing_act && $total != 0.0) {
//        $total += add_gl_trans_supplier(ST_SUPPRECEIVE, $grn, $date_, $clearing_act,
//            0, 0, -$total, null);
//    }
    $Refs->save(ST_SUPPRECEIVE, $grn, $reference);

    add_audit_trail(ST_SUPPRECEIVE, $grn, $date_);

    $trans_no = $grn;
    hook_db_postwrite($po, ST_SUPPRECEIVE);
    commit_transaction();

    return $trans_no;
}


function add_import_OB_supp_invoice($grn_no, $order_no, $supplier_id, $date, $Item,
         $qty, $price, $location, $payment_terms,
         $dimension_id, $dimension2_id)
{
    global $Refs;

    //$company_currency = get_company_currency();
    $trans_no = $grn_no;
    $trans_type = ST_SUPPINVOICE;
    $tax_included = 0;
    $Comments = "";
    $reference = $Refs->get_next($trans_type, null, array('date' => $date,
        'supplier' => $supplier_id['supplier_id']));
//    $supplier = get_supplier($supplier_id['supplier_id']);

    begin_transaction();
    hook_db_prewrite($supp_trans, $trans_type);
//    $tax_total = 0;
//    $taxes = $supp_trans->get_taxes($supp_trans->tax_group_id);
//    if ($trans_no) {
//        $allocs = get_payments_for($trans_no, $trans_type, $supp_trans->supplier_id); //ansar 26-08-17 save allocations
//        void_transaction($trans_type, $trans_no, Today(), _("Document reentered."));
//        $Refs->restore_last($trans_type, $trans_no);
//    } else
//        $allocs = get_po_prepayments($supp_trans);

    add_new_exchange_rate($supplier_id['curr_code'], $date, $supp_trans->ex_rate);

//    foreach ($taxes as $n => $taxitem)
//    {
//        $taxes[$n]['Value'] =  round2($taxitem['Value'], user_price_dec());
//        $tax_total += $taxes[$n]['Value'];
//    }

    $invoice_items_total = $price;

    $item_added_tax = 0;
//    if (!$supp_trans->tax_included)
//    {
//        $taxes = $supp_trans->get_taxes($supp_trans->tax_group_id);
//        foreach ($taxes as $n => $taxitem)
//            $item_added_tax += isset($taxitem['Override']) ? $taxitem['Override'] : round2($taxitem['Value'], user_price_dec());
//    }

//    if ($trans_type == ST_SUPPCREDIT)
//    {
//        // let's negate everything because it's a credit note
//        $invoice_items_total = -$invoice_items_total;
//        $tax_total = -$tax_total;
//        $supp_trans->ov_discount = -$supp_trans->ov_discount; // this isn't used at all...
//        $item_added_tax = -$item_added_tax;
//    }

    $date_ = $date;
    $ex_rate = get_exchange_rate_from_home_currency($supplier_id['curr_code'], $date_);

    /*First insert the invoice into the supp_trans table*/
    $invoice_id = write_supp_trans($trans_type, 0, $supplier_id['supplier_id'], $date_, $date_,
        $reference, $supplier_id['supp_name'],
        $invoice_items_total, $item_added_tax, 0, "", 0, $tax_included);

//    if ($trans_no)
//        move_trans_attachments($trans_type, $trans_no, $invoice_id);

//    $trans_no = $invoice_id;

    $total = 0;

    /* Now the TAX account */
//    $taxes = $supp_trans->get_taxes($supp_trans->tax_group_id, 0, false);
//    $net_diff = 0;

//    foreach ($taxes as $taxitem)
//    {
//        if ($taxitem['Net'] != 0)
//        {
//            if (isset($taxitem['Override'])) {
//                if ($supp_trans->tax_included) { // if tax included, fix net amount to preserve overall line price
//                    $net_diff += $taxitem['Override'] - $taxitem['Value'];
//                    $taxitem['Net'] += $taxitem['Override'] - $taxitem['Value'];
//                }
//                $taxitem['Value'] = $taxitem['Override'];
//            }
            add_trans_tax_details($trans_type, $invoice_id,
                0, 0, 0, 0,
               $price, $ex_rate, $date_, $supplier_id['supp_name'], TR_INPUT);
//
//            if (isset($taxitem['purchasing_gl_code']))
//            {
//                if ($trans_type == ST_SUPPCREDIT)
//                    $taxitem['Value'] = -$taxitem['Value'];
//                $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_,
//                    $taxitem['purchasing_gl_code'], 0, 0, $taxitem['Value'],
//                    $supp_trans->supplier_id,
//                    "A general ledger transaction for the tax amount could not be added");
//            }
//        }
//    }
//    if ($trans_type == ST_SUPPCREDIT)
//        $net_diff = -$net_diff;

    /* Now the AP account */
    $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $supplier_id["payable_account"], 0, 0,
        -($invoice_items_total +  $item_added_tax),
        $supplier_id['supplier_id'],
        "The general ledger transaction for the control total could not be added");

//    $to_allocate = ($invoice_items_total + $item_added_tax + 0);
/*
    foreach ($supp_trans->gl_codes as $entered_gl_code)
    {
//        GL Items are straight forward - just do the debit postings to the GL accounts specified -
//        the credit is to creditors control act  done later for the total invoice value + tax

        if ($trans_type == ST_SUPPCREDIT)
            $entered_gl_code->amount = -$entered_gl_code->amount;

        $memo_ = $entered_gl_code->memo_;
        $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $entered_gl_code->gl_code,
            $entered_gl_code->gl_dim, $entered_gl_code->gl_dim2, $entered_gl_code->amount, $supp_trans->supplier_id, "", 0, $memo_);

        add_supp_invoice_gl_item($trans_type, $invoice_id, $entered_gl_code->gl_code, $entered_gl_code->amount, $memo_,
            $entered_gl_code->gl_dim, $entered_gl_code->gl_dim2);

        // store tax details if the gl account is a tax account
        if ($trans_type == ST_SUPPCREDIT)
            $entered_gl_code->amount = -$entered_gl_code->amount;
        add_gl_tax_details($entered_gl_code->gl_code,
            $trans_type, $invoice_id, $entered_gl_code->amount,
            $ex_rate, $date_, $supp_trans->supp_reference, $supp_trans->tax_included);
    }
*/
    $clearing_act = get_company_pref('grn_clearing_act');
//    foreach ($supp_trans->grn_items as $line_no => $entered_grn)
    {

//        if ($trans_type == ST_SUPPCREDIT)
//        {
//            $entered_grn->this_quantity_inv = -$entered_grn->this_quantity_inv;
//            set_grn_item_credited($entered_grn, $supplier_id['supplier_id'], $invoice_id, $date_);
//        }

        // For tax included pricelist the net price is calculated down from tax_included price.
        // To avoid rounding errors we have to operate on line value instead of price
        // Additionally we have to take into account differences in net value
        // due to variations in tax calculations on supplier side. More over there is no direct relation between
        // taxes and sales accounts, so we add net_diff just to first posted net value. This is _ugly_hack_
        // which save us from rewriting whole routine, and works right only for small tax differences.

//        $taxfree_line = get_tax_free_price_for_item($entered_grn->item_code, $entered_grn->this_quantity_inv * $entered_grn->chg_price,
//                $supp_trans->tax_group_id, $supp_trans->tax_included) - $net_diff; $net_diff = 0;
//
//        $line_tax = get_full_price_for_item($entered_grn->item_code,
//                $entered_grn->this_quantity_inv * $entered_grn->chg_price, 0, $supp_trans->tax_included) - $taxfree_line;

        $stock_gl_code = get_stock_gl_code($Item['stock_id']);

        $dim = !empty($supp_trans->dimension) ? $supp_trans->dimension :
            ($supplier_id['dimension_id'] ? $supplier_id['dimension_id'] : $stock_gl_code['dimension_id']);
        $dim2 = !empty($supp_trans->dimension2) ? $supp_trans->dimension2 :
            ($supplier_id['dimension2_id'] ? $supplier_id['dimension2_id'] : $stock_gl_code['dimension2_id']);
//        if ($trans_type == ST_SUPPCREDIT)
//        {
//            $iv_act = (is_inventory_item($entered_grn->item_code) ? $stock_gl_code["inventory_account"] :
//                ($supplier_id["purchase_account"] ? $supplier_id["purchase_account"] : $stock_gl_code["cogs_account"]));
//            $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
//                $dim, $dim2, $price, $supp_trans->supplier_id);
//        }
//        else
        {
            // -------------- if price changed since po received.
            $iv_act = is_inventory_item($entered_grn->item_code) ? ($clearing_act ? $clearing_act : $stock_gl_code["inventory_account"]) :
                ($supplier_id["purchase_account"] ? $supplier_id["purchase_account"] : $stock_gl_code["cogs_account"]);
//            $old = update_supp_received_items_for_invoice($entered_grn->id, $entered_grn->po_detail_item,
//                $entered_grn->this_quantity_inv, $entered_grn->chg_price,$entered_grn->item_code,$entered_grn->units_id,
//                $entered_grn->con_factor);
            $sql = "UPDATE ".TB_PREF."grn_items
                    SET quantity_inv = quantity_inv + ".db_escape($qty)."
                    WHERE id = ".db_escape($grn_no);
            db_query($sql, "The quantity invoiced off the items received record could not be updated");

            // Since the standard cost is always calculated on basis of the po unit_price,
            // this is also the price that should be the base of calculating the price diff.
            // In cases where there is two different po invoices on the same delivery with different unit prices this will not work either
//            $old_price = $old[2];
//            $old_date = sql2date($old[1]);
//            if (!is_inventory_item($entered_grn->item_code))
//                $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
//                    $dim, $dim2, $taxfree_line, $supp_trans->supplier_id);
//            else
            {
//                $ex_rate = get_exchange_rate_from_home_currency($supplier_id['curr_code'], $old_date);
//                $ex_rate = 1;
//                $old_value = get_tax_free_price_for_item($entered_grn->item_code, $entered_grn->this_quantity_inv * $old_price,
//                    $supp_trans->tax_group_id, $supp_trans->tax_included);
//                $currency = get_supplier_currency($supp_trans->supplier_id);

                $total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
                    $dim, $dim2, $price, $supplier_id['supplier_id'], "", $ex_rate);
//                $diff = get_diff_in_home_currency($supp_trans->supplier_id, $old_date, $date_, $old_value,
//                    $taxfree_line);
//                $mat_cost = update_average_material_cost(null, $entered_grn->item_code,
//                    $diff/$entered_grn->this_quantity_inv, $entered_grn->this_quantity_inv, null, true);
                //Add GL transaction for GRN Provision in case of difference
//                if ($diff != 0)
//                {
//                    $total += add_gl_trans($trans_type, $invoice_id, $date_, $stock_gl_code["inventory_account"],
//                        $dim, $dim2, 'GRN Provision', $diff, null, null, null,
//                        "The general ledger transaction could not be added for the GRN of the inventory item");
//
//                    //If QOH is 0 or negative then update_average_material_cost will be skipped
//                    //Thus difference in PO and Supp Invoice should be handled separately
//                    $qoh = get_qoh_on_date($entered_grn->item_code);
//                    if ($qoh <= 0)
//                    {
//                        global $Refs;
//
//                        $id = get_next_trans_no(ST_JOURNAL);
//                        $ref = $Refs->get_next(ST_JOURNAL, null, $date_);
//                        add_journal(ST_JOURNAL, $id, $diff, $date_, get_company_currency(), $ref);
//                        $stock_id = $entered_grn->item_code;
//                        $stock_gl_code = get_stock_gl_code($stock_id);
//                        $memo = _("Supplier invoice adjustment for zero inventory of ").$stock_id." "._("Invoice")." ".$supp_trans->reference;
//                        //Reverse the inventory effect if $qoh <=0
//                        add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
//                            $stock_gl_code["inventory_account"],
//                            $dim, $dim2, $memo, -$diff);
//                        //GL Posting to inventory adjustment account
//                        add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
//                            $stock_gl_code["adjustment_account"],
//                            $dim, $dim2, $memo, $diff);
//
//                        add_audit_trail(ST_JOURNAL, $id, $date_);
//                        add_comments(ST_JOURNAL, $id, $date_, $memo);
//                        $Refs->save(ST_JOURNAL, $id, $ref);
//                    }
//                }
            }
            add_or_update_purchase_data($supplier_id['supplier_id'], $Item['stock_id'], $price);
        }
        update_purchase_value($Item['stock_id'], $price * $ex_rate);
       /* $item=get_item($entered_grn->item_code);
        $pref=get_company_prefs();
        if($pref['alt_uom'] == 1) {
            if ($entered_grn->units_id != $item['units'])
            {
                if ($item['con_type'] == 0) {
                    $this_quantity_inv = $entered_grn->this_quantity_inv / $entered_grn->con_factor;
                    $myrow_factors = get_company_item_pref('con_factor');
                    if ($myrow_factors['purchase_enable'] == 0) {
                        $item_info = get_item_edit_info($entered_grn->stock_id);
                        $entered_grn->con_factor = $item_info['con_factor'];
                    }
                    else {
                        $entered_grn->con_factor;
                    }
                } else {
                    $this_quantity_inv = $entered_grn->con_factor / $entered_grn->this_quantity_inv;
                }
            } else {
                $myrow_factors = get_company_item_pref('con_factor');
                if ($myrow_factors['purchase_enable'] == 0) {
                    $item_info = get_item_edit_info($entered_grn->stock_id);
                    $entered_grn->con_factor = $item_info['con_factor'];
                }
                else {
                    $entered_grn->con_factor;
                }
                $this_quantity_inv = $entered_grn->this_quantity_inv;
            }

        }
        else{
            $this_quantity_inv = $entered_grn->this_quantity_inv;
        }*/
        add_supp_invoice_item($trans_type,$invoice_id,$Item['stock_id'],
            $Item['description'], 0, 	$price, 0,
            $qty, $grn_no, $order_no, "",0,0,
            '','','','','',''
            , 0,0,0,0,0,0
            ,'','',''
            ,0,0,0,0,
            0,0,0,0,$Item['units']
        );
    } /* end of GRN postings */

    /*Post a balance post if $total != 0 */
    add_gl_balance($trans_type, $invoice_id, $date_, -$total, PT_SUPPLIER, $supplier_id['supplier_id']); // ??

    add_comments($trans_type, $invoice_id, $date_, $Comments);

    $Refs->save($trans_type, $invoice_id, $reference);

//    if (count($supp_trans->src_docs) == 1)
//    {
//        $invoice_no = key($supp_trans->src_docs);
//        $invoice_alloc_balance = get_supp_trans_allocation_balance(ST_SUPPINVOICE, $supp_trans->src_docs);
//        if ($invoice_alloc_balance > 0)
//        { 	//the invoice is not already fully allocated
//
//            $trans = get_supp_trans($invoice_id, ST_SUPPCREDIT);
//            $total = -$trans['Total'];
//
//            $allocate_amount = ($invoice_alloc_balance > $total) ? $total : $invoice_alloc_balance;
//
//            /*Now insert the allocation record if > 0 */
//            if ($allocate_amount != 0)
//            {
//                add_supp_allocation($allocate_amount, ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $invoice_no,
//                    $supp_trans->supplier_id, $date_);
//                update_supp_trans_allocation(ST_SUPPINVOICE, $invoice_no, $supp_trans->supplier_id);
//                update_supp_trans_allocation(ST_SUPPCREDIT, $invoice_id, $supp_trans->supplier_id);
//                exchange_variation(ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $supp_trans->src_docs, $date_,
//                    $allocate_amount, PT_SUPPLIER);
//            }
//        }
//    }

//_vd($allocs);
    //reallocate_payments($invoice_id, ST_SUPPINVOICE, $date_, $to_allocate, $allocs);
    /*reallocate_payments($invoice_id, ST_SUPPINVOICE, $date_, $net_total+$tax_total, $allocs, $supp_trans->supplier_id);*/ //ansar 26-08-17
//    reallocate_payments($invoice_id, ST_SUPPINVOICE, $date_, $to_allocate, $allocs, $supp_trans->supplier_id); //dz 27.9.17
//_vd(get_payments_for($sales_order, ST_PURCHORDER));
//_vd(get_payments_for($invoice_id, ST_SUPPINVOICE));
//exit;
    $trans_no = $invoice_id;
    hook_db_postwrite($supp_trans, $trans_type);
    commit_transaction();

    return $trans_no ;
}













//
//function add_supplier_OB_invoices($supplier_id, $item, $qty, $date, $account_code, $dim, $dim2, $price, $memo, $Comments)
//{
//	global $Refs;
//
//	begin_transaction();
//	$trans_type = ST_SUPPINVOICE;
//	hook_db_prewrite($supp_trans, $trans_type);
//    $date_ = $date;
//    $reference = $Refs->get_next($trans_type, null, array('date' => $date_,
//        'supplier' => $supplier_id));
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
//
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
//		-($invoice_items_total),
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
////		add_supp_invoice_gl_item($trans_type, $invoice_id, $account_code,
////			$invoice_items_total, $memo_);
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
//		add_supp_invoice_item($trans_type, $invoice_id, $item['stock_id'],
//            $item['description'], 0, 	$price, 0,
//			$qty, $entered_grn->id, $entered_grn->po_detail_item, "","");
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

?>