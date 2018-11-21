<?php
function add_supplier_OB_purchase_return($supplier_id, $date, $price) // do not receive as ref because we change locally
{
	global $Refs;

	//$company_currency = get_company_currency();
	/*Start an sql transaction */
	begin_transaction();
	$trans_type = ST_SUPPCREDIT;
	hook_db_prewrite($supp_trans, $trans_type);
	$tax_total = 0;
    $reference = $Refs->get_next($trans_type, null, array('date' => $date,
        'supplier' => $supplier_id['supplier_id']));
    $memo = $reference;
    $Comments = '';
//	$taxes = $supp_trans->get_taxes($supp_trans->tax_group_id);

//	foreach ($taxes as $n => $taxitem)
//	{
//		$taxes[$n]['Value'] =  round2($taxitem['Value'], user_price_dec());
//		$tax_total += $taxes[$n]['Value'];
//	}

	$invoice_items_total = $price;
//	$invoice_items_total = $supp_trans->get_total_taxfree($supp_trans->tax_group_id);

//	if($supp_trans->tax_included==0) {
//		$item_added_tax = $tax_total;
//	} else {
	$item_added_tax = 0;
//
//	}

	if ($trans_type == ST_SUPPCREDIT)
	{
		// let's negate everything because it's a credit note
		$invoice_items_total = -$invoice_items_total;
		$tax_total = -$tax_total;
		$supp_trans->ov_discount = -$supp_trans->ov_discount; // this isn't used at all...
		$item_added_tax = -$item_added_tax;
	}

	$date_ = $date;
//	$ex_rate = get_exchange_rate_from_home_currency(get_supplier_currency($supplier_id['supplier_id']), $date_);

	/*First insert the invoice into the supp_trans table*/
	$invoice_id = write_supp_trans($trans_type, 0, $supplier_id['supplier_id'], $date_, $date_/*$due_date*/,
	$reference, $reference/*$supp_reference*/,
	$invoice_items_total, $item_added_tax, /*$gst_wh*/0,
    /*$supp_trans->ov_discount*/0, "", 0,
    /*$supp_trans->tax_included*/0);

	$total = 0;
	/* Now the control account */
	$supplier_accounts = get_supplier_accounts($supplier_id['supplier_id']);
	$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $supplier_accounts["payable_account"], 0, 0,
		-($invoice_items_total +  $item_added_tax + 0/*$supp_trans->ov_discount*/),
        $supplier_id['supplier_id'],
		"The general ledger transaction for the control total could not be added");

//	foreach ($supp_trans->gl_codes as $entered_gl_code)
	{

		/*GL Items are straight forward - just do the debit postings to the GL accounts specified -
        the credit is to creditors control act  done later for the total invoice value + tax*/

		if ($trans_type == ST_SUPPCREDIT)
			$price = -$price/*$entered_gl_code->amount*/;

		$memo_ = $memo;
		$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, '2010101004',
			0/*$entered_gl_code->gl_dim*/, 0/*$entered_gl_code->gl_dim2*/, $price, $supplier_id['supplier_id'], "", 0, $memo_);

		add_supp_invoice_gl_item($trans_type, $invoice_id, 0,
			$price, $memo_);

		// store tax details if the gl account is a tax account
		if ($trans_type == ST_SUPPCREDIT)
			$price = -$price/*$entered_gl_code->amount*/;
//		add_gl_tax_details($account_code,
//			$trans_type, $invoice_id, $price,
//			$ex_rate, $date_, $reference, 0);
	}

//	$clearing_act = get_company_pref('grn_clearing_act');
//	
//	foreach ($supp_trans->grn_items as $line_no => $entered_grn)
//	{
//
//		if ($trans_type == ST_SUPPCREDIT)
//		{
//			$entered_grn->this_quantity_inv = -$entered_grn->this_quantity_inv;
//			set_grn_item_credited($entered_grn, $supp_trans->supplier_id, $invoice_id, $date_);
//		}
//		$line_taxfree = $entered_grn->taxfree_charge_price($supp_trans->tax_group_id);
//		$line_tax = $entered_grn->full_charge_price($supp_trans->tax_group_id) - $line_taxfree;
//		$stock_gl_code = get_stock_gl_code($entered_grn->item_code);
//
//		if ($trans_type == ST_SUPPCREDIT)
//		{
//			$iv_act = (is_inventory_item($entered_grn->item_code) ? $stock_gl_code["inventory_account"] :
//				($supplier_accounts["purchase_account"] ? $supplier_accounts["purchase_account"] : $stock_gl_code["cogs_account"]));
//			$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
//				$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//				$entered_grn->this_quantity_inv * $line_taxfree, $supp_trans->supplier_id);
//		}
//		else
//		{
//			// -------------- if price changed since po received. 16 Aug 2008 Joe Hunt
//			$iv_act = is_inventory_item($entered_grn->item_code) ? ($clearing_act ? $clearing_act : $stock_gl_code["inventory_account"]) :
//				($supplier_accounts["purchase_account"] ? $supplier_accounts["purchase_account"] : $stock_gl_code["cogs_account"]);
//			$old = update_supp_received_items_for_invoice($entered_grn->id, $entered_grn->po_detail_item,
//				$entered_grn->this_quantity_inv, $entered_grn->chg_price);
//			// Since the standard cost is always calculated on basis of the po unit_price,
//			// this is also the price that should be the base of calculating the price diff.
//			// In cases where there is two different po invoices on the same delivery with different unit prices this will not work either
//
//			//$old_price = $old[0];
//
//			$old_price = $old[2];
//			// adjust for tax included.
//			$old_price = get_tax_free_price_for_item($entered_grn->item_code, $old_price,
//				$supp_trans->tax_group_id, $entered_grn->tax_included);
//
//			/*
// 			If statement is removed. Should always check for deliveries nomatter if there has been a price change. 
//			*/
//			//if ($old_price != $line_taxfree) // price-change, so update
//			//{
//			//$diff = $line_taxfree - $old_price;
//			$old_date = sql2date($old[1]);
//			if (!is_inventory_item($entered_grn->item_code))
//				$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
//					$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//					$entered_grn->this_quantity_inv * $line_taxfree, $supp_trans->supplier_id);
//			else
//			{
//				$currency = get_supplier_currency($supp_trans->supplier_id);
//				$ex_rate = get_exchange_rate_from_home_currency($currency, $old_date);
//				$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_, $iv_act,
//					$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//					$entered_grn->this_quantity_inv * $old_price, $supp_trans->supplier_id, "", $ex_rate);
//				$diff = get_diff_in_home_currency($supp_trans->supplier_id, $old_date, $date_, $old_price,
//					$line_taxfree);
//
//				// always return due to change in currency.
//				/*$mat_cost = update_average_material_cost(null, $entered_grn->item_code,
//					$diff, $entered_grn->this_quantity_inv, $old_date, true); */
//				$mat_cost = update_average_material_cost(null, $entered_grn->item_code,
//					$diff, $entered_grn->this_quantity_inv, null, true);
//
//				//Add GL transaction for GRN Provision in case of difference
//				if (($diff * $entered_grn->this_quantity_inv) != 0 )
//				{
//					$diff_amt = $diff * $entered_grn->this_quantity_inv;
//					$total += add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["inventory_account"],
//						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'], 'GRN Provision',
//						$diff_amt, null, null, null,
//						"The general ledger transaction could not be added for the GRN of the inventory item");
//
//					//Chaitanya
//					//If QOH is 0 or negative then update_average_material_cost will be skipped
//					//Thus difference in PO and Supp Invoice should be handled separately
//
//					$qoh = get_qoh_on_date($entered_grn->item_code);
//					if ($qoh <= 0)
//					{
//						global $Refs;
//
//						//Chaitanya : Post a journal entry
//						$id = get_next_trans_no(ST_JOURNAL);
//						$ref = $Refs->get_next(ST_JOURNAL);
//						$stock_id = $entered_grn->item_code;
//						$stock_gl_code = get_stock_gl_code($stock_id);
//						$memo = _("Supplier invoice adjustment for zero inventory of ").$stock_id." "._("Invoice")." ".$supp_trans->reference;
//						//Reverse the inventory effect if $qoh <=0
//						add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
//							$stock_gl_code["inventory_account"],
//							$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//							$memo, -$entered_grn->this_quantity_inv * $diff);
//						//GL Posting to inventory adjustment account
//						add_gl_trans_std_cost(ST_JOURNAL, $id, $date_,
//							$stock_gl_code["adjustment_account"],
//							$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//							$memo, $entered_grn->this_quantity_inv * $diff);
//
//						add_audit_trail(ST_JOURNAL, $id, $date_);
//						add_comments(ST_JOURNAL, $id, $date_, $memo);
//						$Refs->save(ST_JOURNAL, $id, $ref);
//					}
//
//					/*$qoh = get_qoh_on_date($entered_grn->item_code);
//					if ($qoh <= 0)
//					{
//						$memo = "Diff. in cost: ".$diff;
//						//Reverse the inventory effect if $qoh <=0
//						add_gl_trans_supplier($trans_type, $invoice_id, $date_, 
//						$stock_gl_code["inventory_account"],
//						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//						-$entered_grn->this_quantity_inv * $diff, $supp_trans->supplier_id, "", null, $memo);
//						//GL Posting to inventory adjustment account
//						add_gl_trans_supplier($trans_type, $invoice_id, $date_, 
//						$stock_gl_code["adjustment_account"],
//						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'],
//						$entered_grn->this_quantity_inv * $diff, $supp_trans->supplier_id, "", null, $memo);						
//					}*/
//
//					//add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["cogs_account"],
//					//	0, 0, 'GRN Provision', -$diff_amt, null, null, null,
//					//	"The general ledger transaction could not be added for the GRN of the inventory item");				
//				}
//			}
//			// added 2008-12-08 Joe Hunt. Update the purchase data table
//			add_or_update_purchase_data($supp_trans->supplier_id, $entered_grn->item_code, $entered_grn->chg_price);
//			/*$deliveries = get_deliveries_between($entered_grn->item_code, $old_date, Today()); // extend the period, if invoice is before any deliveries.
//			if ($deliveries[0] != 0) // have deliveries been done during the period?
//			{
//				$deliveries[1] /= $deliveries[0];
//				$amt = ($mat_cost - $deliveries[1]) * $deliveries[0]; // $amt in home currency
//				if ($amt != 0.0)
//				{
//					$_s = $entered_grn->item_code;
//					$_d = $deliveries[0];
//					$_od = $old_date;
//					$_cd = $mat_cost - $deliveries[1];
//					$memo = _("Cost difference adjustment for $_s. $_d items delivered since $_od. The cost difference is $_cd");
//					add_gl_trans($trans_type, $invoice_id, $date_,	$stock_gl_code["cogs_account"],
//						$stock_gl_code['dimension_id'], $stock_gl_code['dimension2_id'], $memo,
//						$amt, null, null, null,
//						"The general ledger transaction could not be added for the price variance of the inventory item");
//					add_gl_trans($trans_type, $invoice_id, $date_,	$iv_act,
//						0, 0, $memo, -$amt, null, null, null,
//						"The general ledger transaction could not be added for the price variance of the inventory item");
//				}		
//				update_stock_move_pid(ST_CUSTDELIVERY, $entered_grn->item_code, $old_date, $date_, 0, $mat_cost);
//			} 
//			if (is_inventory_item($entered_grn->item_code))
//				update_stock_move_pid(ST_SUPPRECEIVE, $entered_grn->item_code, $old_date, $old_date, $supp_trans->supplier_id, $mat_cost);
//			*/
//			//}
//		}
//		// ----------------------------------------------------------------------
//
//		add_supp_invoice_item($trans_type, $invoice_id, $entered_grn->item_code,
//			$entered_grn->item_description, 0, 	$entered_grn->chg_price, $line_tax,
//			$entered_grn->this_quantity_inv, $entered_grn->id, $entered_grn->po_detail_item, "");
//	} /* end of GRN postings */
	/* Now the TAX account */
//	$taxes = $supp_trans->get_taxes($supp_trans->tax_group_id, 0, false); // 2009.08-18 Joe Hunt. We have already got the gl lines
//	foreach ($taxes as $taxitem)
//	{
//		if ($taxitem['Net'] != 0)
//		{
//
//			if ($trans_type == ST_SUPPCREDIT)
//			{
//				$taxitem['Net'] = -$taxitem['Net'];
//				$taxitem['Value'] = -$taxitem['Value'];
//			}
//    add_trans_tax_details($trans_type, $invoice_id,
//        0, 0, 0, 0,
//        $price, $ex_rate, $date_, $supplier_id['supp_name'], TR_INPUT);//
////			if ($trans_type == ST_SUPPCREDIT)
////				$taxitem['Value'] = -$taxitem['Value'];
////			$total += add_gl_trans_supplier($trans_type, $invoice_id, $date_,
////				$taxitem['purchasing_gl_code'], 0, 0, $taxitem['Value'],
////				$supp_trans->supplier_id,
////				"A general ledger transaction for the tax amount could not be added");
////		}
////	}

	/*Post a balance post if $total != 0 */
	add_gl_balance($trans_type, $invoice_id, $date_, -$total, PT_SUPPLIER, $supplier_id['supplier_id']);

	add_comments($trans_type, $invoice_id, $date_, $Comments);

	$Refs->save($trans_type, $invoice_id, $reference);

//	if ($invoice_no != 0)
//	{
//		$invoice_alloc_balance = get_supp_trans_allocation_balance(ST_SUPPINVOICE, $invoice_no);
//		if ($invoice_alloc_balance > 0)
//		{ 	//the invoice is not already fully allocated 
//
//			$trans = get_supp_trans($invoice_no, ST_SUPPINVOICE);
//			$total = $trans['Total'];
//
//			$allocate_amount = ($invoice_alloc_balance > $total) ? $total : $invoice_alloc_balance;
//			/*Now insert the allocation record if > 0 */
//			if ($allocate_amount != 0)
//			{
//				update_supp_trans_allocation(ST_SUPPINVOICE, $invoice_no, $allocate_amount);
//				update_supp_trans_allocation(ST_SUPPCREDIT, $invoice_id, $allocate_amount); // ***
//				add_supp_allocation($allocate_amount, ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $invoice_no,
//					$date_);
//				// Exchange Variations Joe Hunt 2008-09-20 ////////////////////////////////////////
//
//				exchange_variation(ST_SUPPCREDIT, $invoice_id, ST_SUPPINVOICE, $invoice_no, $date_,
//					$allocate_amount, PT_SUPPLIER);
//
//				///////////////////////////////////////////////////////////////////////////
//			}
//		}
//	}
//	$supp_trans->trans_no = $invoice_id;
	hook_db_postwrite($supp_trans, $trans_type);
	commit_transaction();

	return $invoice_id;
}



?>