<?php

//-----------------------------------------------------------------------------
// insert/update sales delivery
//
function write_sales_delivery(&$delivery,$bo_policy)
{
	global $Refs;

	$trans_no = $delivery->trans_no;
	if (is_array($trans_no)) $trans_no = key($trans_no);

	begin_transaction();
	$delivery->bo_policy = $bo_policy;
	hook_db_prewrite($delivery, ST_CUSTDELIVERY);

	$customer = get_customer($delivery->customer_id);

	add_new_exchange_rate($customer['curr_code'], $delivery->document_date, $delivery->ex_rate);

	$delivery_items_total = $delivery->get_items_total_dispatch();
	$freight_tax = $delivery->get_shipping_tax();

// mark sales order for concurrency conflicts check
	update_sales_order_version($delivery->src_docs);

	$tax_total = 0;
	$taxes = $delivery->get_taxes(); // all taxes with freight_tax

	foreach ($taxes as $taxitem) {
		$taxitem['Value'] =  round2($taxitem['Value'], user_price_dec());
		$tax_total +=  $taxitem['Value'];
	}

	/* Insert/update the debtor_trans */
	$delivery_no = write_customer_trans(ST_CUSTDELIVERY, $trans_no, $delivery->customer_id,
		$delivery->Branch, $delivery->document_date, $delivery->reference,
		$delivery_items_total, 0,
		$delivery->tax_included ? 0 : $tax_total-$freight_tax,
		$delivery->freight_cost,
		$delivery->tax_included ? 0 : $freight_tax,
		$delivery->sales_type, $delivery->order_no,
		$delivery->ship_via, $delivery->due_date, 0, 0, $delivery->dimension_id,
		$delivery->dimension2_id, $delivery->payment, $delivery->tax_included, $delivery->prep_amount,
		0,0,0,0,0,0,0,0,0,0,$delivery->discount1, $delivery->discount2,0,0
		,$delivery->h_text2,$delivery->text2,0,$delivery->salesman,$delivery->h_amount1 );

	if ($trans_no == 0) {
		$delivery->trans_no = array($delivery_h_text2no=>0);
	} else {
		move_trans_attachments(ST_CUSTDELIVERY, $trans_no, $delivery_no);
		void_gl_trans(ST_CUSTDELIVERY, $delivery_no, true);
		void_stock_move(ST_CUSTDELIVERY, $delivery_no);
		void_trans_tax_details(ST_CUSTDELIVERY, $delivery_no);
		delete_comments(ST_CUSTDELIVERY, $delivery_no);
	}


	// for prepaid delivery repost income from deffered account
	if ($delivery->is_prepaid())
	{
		$branch_data = get_branch_accounts($delivery->Branch);
		$total = 0;
	}
	foreach ($delivery->line_items as $line_no => $delivery_line) {

		$line_price = $delivery_line->line_price();
		$line_taxfree_price = get_tax_free_price_for_item($delivery_line->stock_id,
			$delivery_line->price, 0, $delivery->tax_included,
			$delivery->tax_group_array);

		$line_tax = get_full_price_for_item($delivery_line->stock_id, $delivery_line->price,
				0, $delivery->tax_included, $delivery->tax_group_array) - $line_taxfree_price;

		$delivery_line->standard_cost = get_unit_cost($delivery_line->stock_id);
		$item=get_item($delivery_line->stock_id);
		$qty="";
		if($delivery_line->units_id !=  $item['units'])
		{
			if($item['con_type'] == 0)
			{
				$qty = $delivery_line->qty_dispatched /$delivery_line->con_factor;
			}
			else{
					$qty = $delivery_line->con_factor / $delivery_line->qty_dispatched ;
			}
		}
		else{
			$qty = $delivery_line->qty_dispatched;
		}
		
		$pref = get_company_prefs();
		if($pref['item_location'] == 1)
			$location=$delivery_line->item_location;
		else
			$location=$delivery->Location;

		/* add delivery details for all lines */
		write_customer_trans_detail_item(ST_CUSTDELIVERY, $delivery_no, $delivery_line->stock_id,
			$delivery_line->item_description,$qty,
			$delivery_line->line_price(), $line_tax,
			$delivery_line->discount_percent, $delivery_line->standard_cost, $delivery_line->src_id,
			$trans_no ? $delivery_line->id : 0,$delivery_line->units_id,$delivery_line->con_factor,
			$delivery_line->text1,$delivery_line->text2,
			$delivery_line->text3,$delivery_line->text4,$delivery_line->text5,$delivery_line->text6,
			$delivery_line->amount1,$delivery_line->amount2,
			$delivery_line->amount3,$delivery_line->amount4,$delivery_line->amount5,$delivery_line->amount6,
			$delivery_line->date1,$delivery_line->date2,
			$delivery_line->date3,
			$delivery_line->combo1,$delivery_line->combo2,
			$delivery_line->combo3,$delivery_line->combo4,$delivery_line->combo5,
			$delivery_line->combo6,$delivery_line->batch,$location,$delivery_line->text7,$delivery_line->bonus);

		// Now update sales_order_details for the quantity delivered
		if ($delivery_line->qty_old != $delivery_line->qty_dispatched)
			update_parent_line(ST_CUSTDELIVERY, $delivery_line->src_id,
				$qty-$delivery_line->qty_old);

		if ($delivery_line->qty_dispatched != 0) {
			$pref=get_company_prefs();
			if($pref['batch'] == 1) {
				$batch=$delivery_line->batch;

			}
			else{
				$batch="";
			}
				
			add_stock_move(ST_CUSTDELIVERY, $delivery_line->stock_id, $delivery_no,
				$location, $delivery->document_date, $delivery->reference,
				-$qty, $delivery_line->standard_cost,
				$line_price*(1-$delivery_line->discount_percent),$delivery->customer_id,0,
				$delivery_line->text1,$delivery_line->text2,
				$delivery_line->text3,$delivery_line->text4,$delivery_line->text5,$delivery_line->text6,
				$delivery_line->amount1,$delivery_line->amount2,
				$delivery_line->amount3,$delivery_line->amount4,$delivery_line->amount5,$delivery_line->amount6,
				$delivery_line->date1,$delivery_line->date2,
				$delivery_line->date3,
				$delivery_line->combo1,$delivery_line->combo2,
				$delivery_line->combo3,$delivery_line->combo4,$delivery_line->combo5,
				$delivery_line->combo6,$delivery_line->units_id,$delivery_line->con_factor,$batch,$location);

            if($pref['bonus'] == 1) {
                add_stock_move(ST_CUSTDELIVERY, $delivery_line->stock_id, $delivery_no,
                    $location, $delivery->document_date, $delivery->reference,
                    -$delivery_line->bonus, $delivery_line->standard_cost,
                    $line_price * (1 - $delivery_line->discount_percent), $delivery->customer_id, 0,
                    $delivery_line->text1, $delivery_line->text2,
                    $delivery_line->text3, $delivery_line->text4, $delivery_line->text5, $delivery_line->text6,
                    $delivery_line->amount1, $delivery_line->amount2,
                    $delivery_line->amount3, $delivery_line->amount4, $delivery_line->amount5, $delivery_line->amount6,
                    $delivery_line->date1, $delivery_line->date2,
                    $delivery_line->date3,
                    $delivery_line->combo1, $delivery_line->combo2,
                    $delivery_line->combo3, $delivery_line->combo4, $delivery_line->combo5,
                    $delivery_line->combo6, $delivery_line->units_id, $delivery_line->con_factor, $batch, $location);
            }

			$mb_flag = get_mb_flag($delivery_line->stock_id);

			if (is_fixed_asset($mb_flag)) {
				$sql = "UPDATE ".TB_PREF."stock_master SET inactive=1, material_cost=0
					WHERE stock_id=".db_escape($delivery_line->stock_id);
				db_query($sql,"The cost details for the fixed asset could not be updated");
			}

			$stock_gl_code = get_stock_gl_code($delivery_line->stock_id);

			// If there is a Customer Dimension, then override with this,
			// else take the Item Dimension (if any)
			$dim = ($delivery->dimension_id != $customer['dimension_id'] ? $delivery->dimension_id :
				($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
			$dim2 = ($delivery->dimension2_id != $customer['dimension2_id'] ? $delivery->dimension2_id :
				($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));
			if ($delivery->is_prepaid())
			{
				$sales_account = ($branch_data['sales_account'] != "" ? $branch_data['sales_account'] : $stock_gl_code['sales_account']);

				$total += add_gl_trans_customer(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date, $sales_account, $dim, $dim2,
					-$line_taxfree_price*$delivery_line->qty_dispatched, // FIXME - calculation order should be exactly like in invoice to minimalize roundings
					$delivery->customer_id, "The sales price GL posting could not be inserted");
			}
			/* insert gl_trans to credit stock and debit cost of sales at standard cost*/
			if (is_inventory_item($delivery_line->stock_id)) {
				// Fixed Assets
				if ($delivery->fixed_asset) {
					$fa_purchase_cost = get_purchase_cost($delivery_line->stock_id);
					$fa_depreciation = $fa_purchase_cost - $delivery_line->standard_cost;
					/*first remove depreciation*/
					add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no,
						$delivery->document_date, $stock_gl_code["adjustment_account"], $dim, $dim2, "",
						$fa_depreciation,
						PT_CUSTOMER, $delivery->customer_id,
						"The cost of sales GL posting could not be inserted");
					/*then remove asset*/
					add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
						$stock_gl_code["inventory_account"], 0, 0, "",
						-$fa_purchase_cost,
						PT_CUSTOMER, $delivery->customer_id,
						"The stock side of the cost of sales GL posting could not be inserted");
					/*finally adjust sales account with the remaining*/
					add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
						$stock_gl_code["cogs_account"], 0, 0, "",
						($fa_purchase_cost - $fa_depreciation),
						PT_CUSTOMER, $delivery->customer_id,
						"The stock side of the cost of sales GL posting could not be inserted");
				}
				// Invetory Items
				else if ($delivery_line->standard_cost != 0) {
				
				 $prefs = get_company_prefs();
                    $item = get_item($delivery_line->stock_id);
                    // $bonus_amount = $delivery_line->bonus * $delivery_items_total;
                     $bonus_amount = $delivery_line->bonus * $delivery_line->standard_cost;
                     
                    
//                    display_error( $prefs['alt_uom']."/".$delivery_line->units_id."+".$item['units']."//".$delivery_line->con_factor);
                    if($prefs['alt_uom'] == 1 && $delivery_line->units_id != $item['units'])
                        $delivery_line->standard_cost =     $delivery_line->standard_cost / $delivery_line->con_factor;
				
				
					/*first the cost of sales entry*/
					add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no,
						$delivery->document_date, $stock_gl_code["cogs_account"], $dim, $dim2, "",
						$delivery_line->standard_cost * $delivery_line->qty_dispatched,
						PT_CUSTOMER, $delivery->customer_id,
						"The cost of sales GL posting could not be inserted");

					/*now the stock entry*/
				if($prefs['bonus'] == 1) {
                        add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
                            $stock_gl_code["inventory_account"], 0, 0, "",
                            -($delivery_line->standard_cost * $delivery_line->qty_dispatched )
                            ,
                            PT_CUSTOMER, $delivery->customer_id,
                            "The stock side of the cost of sales GL posting could not be inserted");
                            add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
                            $stock_gl_code["inventory_account"], 0, 0, "",
                            -($bonus_amount)
                            ,
                            PT_CUSTOMER, $delivery->customer_id,
                            "The stock side of the cost of sales GL posting could not be inserted");
                    }
                    else{
                        add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
                            $stock_gl_code["inventory_account"], 0, 0, "",
                            (-$delivery_line->standard_cost * $delivery_line->qty_dispatched),
                            PT_CUSTOMER, $delivery->customer_id,
                            "The stock side of the cost of sales GL posting could not be inserted");
                    }


                    $company_record = get_company_prefs();
                    if($prefs['bonus'] == 1) {
                        add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
                            $company_record["default_sales_discount_act"], 0, 0, "",
                            ($delivery_line->bonus * $delivery_line->standard_cost),
                            PT_CUSTOMER, $delivery->customer_id,
                            "The stock side of the cost of sales GL posting could not be inserted");
                    }
				}

			} /* end of if GL and stock integrated and standard cost !=0 */

		} /*quantity dispatched is more than 0 */
	} /*end of order_line loop */

//	if($delivery->discount1 != 0)
//	{
//		add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
//			112233, 0, 0, "",
//			($delivery->discount1),
//			PT_CUSTOMER, $delivery->customer_id,
//			"The stock side of the cost of sales GL posting could not be inserted");
//	}
//	if($delivery->discount2 != 0)
//	{
//		add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date,
//			112233, 0, 0, "",
//			($delivery->discount2),
//			PT_CUSTOMER, $delivery->customer_id,
//			"The stock side of the cost of sales GL posting could not be inserted");
//	}
	if ($delivery->is_prepaid())
	{
		$deferred_act = get_company_pref('deferred_income_act');
		add_gl_trans_customer(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date, $deferred_act, 0, 0,
			-$total, $delivery->customer_id, "The deferred income GL posting could not be inserted");
	}

	if ($bo_policy == 0) {
		// if cancelling any remaining quantities
		close_sales_order($delivery->order_no);
	}

	// taxes - this is for printing purposes
	foreach ($taxes as $taxitem) {
		if ($taxitem['Net'] != 0) {
			$ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($delivery->customer_id), $delivery->document_date);
			add_trans_tax_details(ST_CUSTDELIVERY, $delivery_no, $taxitem['tax_type_id'],
				$taxitem['rate'], $delivery->tax_included, $taxitem['Value'],
				$taxitem['Net'], $ex_rate, $delivery->document_date, $delivery->reference, null);
		}
	}

	add_comments(ST_CUSTDELIVERY, $delivery_no, $delivery->document_date, $delivery->Comments);

	if ($trans_no == 0) {
		$Refs->save(ST_CUSTDELIVERY, $delivery_no, $delivery->reference);
	}
	hook_db_postwrite($delivery, ST_CUSTDELIVERY);
	commit_transaction();

	return $delivery_no;
}

//--------------------------------------------------------------------------------------------------
function adjust_shipping_charge(&$delivery, $trans_no)
{
	$sql = "SELECT sum(ov_freight) as freight FROM ".TB_PREF."debtor_trans WHERE order_ = $trans_no AND type = " . ST_CUSTDELIVERY . " AND debtor_no = " . $delivery->customer_id;
	$result = db_query($sql, "Can not find delivery notes");
	$row = db_fetch_row($result);
	if (!$row[0]) $freight = 0;
	else $freight = $row[0];
	if ($freight < $delivery->freight_cost) $delivery->freight_cost = $delivery->freight_cost - $freight;
	else $delivery->freight_cost = 0;
}

//--------------------------------------------------------------------------------------------------

function void_sales_delivery($type, $type_no, $transactions=true)
{
	if ($transactions)
		begin_transaction();

	hook_db_prevoid($type, $type_no);

	void_gl_trans($type, $type_no, true);

	// reverse all the changes in the sales order
	$items_result = get_customer_trans_details($type, $type_no);

	$order = get_customer_trans_order($type, $type_no);

	if ($order) {
		$auto = (get_reference(ST_SALESORDER, $order) == "auto");
		while ($row = db_fetch($items_result)) {
			update_parent_line(ST_CUSTDELIVERY, $row['src_id'], -$row['quantity'], $auto);
		}
	}

	// clear details after they've been reversed in the sales order
	void_customer_trans_details($type, $type_no);

	void_trans_tax_details($type, $type_no);

	void_cust_allocations($type, $type_no);

	// do this last because other voidings can depend on it
	// DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
	void_customer_trans($type, $type_no);

	if ($transactions)
		commit_transaction();
}

