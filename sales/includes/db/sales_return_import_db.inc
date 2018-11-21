<?php

//function get_items_total_dispatch_import1($qty_dispatched, $price, $discount_percent=0)
//{
//	$total = 0;
//
//	$total += round(($qty_dispatched * $price - $discount_percent), user_price_dec());
//
//	return $total;
//}
//function write_credit_note_OB_import($trans_no, $customer_id, $Branch, $reference, $stock_id, $description, $date, $qty, $price)
//{
//	global $Refs;
//
////	$credit_invoice = is_array($credit_note->src_docs) ?
////	reset(array_keys($credit_note->src_docs)) : $credit_note->src_docs;
//
//	$credit_date = $date;
////	$tax_group_id = $credit_note->tax_group_id;
//
//	$trans_no = 0;
////	if (is_array($trans_no)) {
////		$trans_no = key($trans_no);
////	}
//
//	$credit_type = 'Return' ;
//
//	begin_transaction();
//	hook_db_prewrite($credit_note, ST_CUSTCREDIT);
//
////	$company_data = get_company_prefs();
//	$branch_data = get_branch_accounts($Branch);
//	$stock_gl_codes = get_stock_gl_code($stock_id);
//	$credit_note_total = get_items_total_dispatch_import1($qty, $price, 0);
//	$freight_tax = 0;
//
////	$taxes = $credit_note->get_taxes();
//
////	$tax_total = 0;
////	foreach ($taxes as $taxitem) {
////		$taxitem['Value'] =  round2($taxitem['Value'], user_price_dec());
////		$tax_total += $taxitem['Value'];
////	}
//
////	if ($credit_note->tax_included == 0) {
////		$items_added_tax = $tax_total-$freight_tax;
////		$freight_added_tax = $freight_tax;
////	} else {
//		$items_added_tax = 0;
//		$freight_added_tax = 0;
////	}
//	// 2006-06-14. If the Customer Branch AR Account is set to a Bank Account,
//	// the transaction will be settled at once.
//	if (is_bank_account($branch_data['receivables_account']))
//		$alloc = $credit_note_total + $items_added_tax + 0/*$credit_note->freight_cost*/ + $freight_added_tax;
//	else
//		$alloc = 0;
//
//	//	$sales_order=$invoice->order_no;	//?
//	//    if (is_array($sales_order)) $sales_order = $sales_order[0]; //?
////	if (!isset($credit_note->order_no))
////		$credit_note->order_no = 0;
//
//	/*Now insert the Credit Note into the debtor_trans table with the allocations as calculated above*/
//	// all amounts in debtor's currency
//	$credit_no = write_customer_trans(ST_CUSTCREDIT, $trans_no, $customer_id,
//	$Branch, $credit_date, $reference,
//	$credit_note_total, 0, $items_added_tax,
//	0/*$credit_note->freight_cost*/, $freight_added_tax,
//	/*$credit_note->sales_type*/1, 0/*$credit_note->order_no*/, 1/*$credit_note->ship_via*/,
//	null, $alloc, 0, 0/*$credit_note->dimension_id*/, 0/*$credit_note->dimension2_id*/,
//	3/*$credit_note->payment*/, 0 ,0, 0, 0, 1);
//	// 2008-06-14 extra $alloc, 2008-11-12 dimension_id Joe Hunt
//
////	if ($trans_no==0) {
////		$credit_note->trans_no = array($credit_no=>0);
////	} else {
////		delete_comments(ST_CUSTCREDIT, $credit_no);
////		void_cust_allocations(ST_CUSTCREDIT, $credit_no, $credit_date);
////		void_gl_trans(ST_CUSTCREDIT, $credit_no, true);
////		void_stock_move(ST_CUSTCREDIT, $credit_no);
////		void_trans_tax_details(ST_CUSTCREDIT, $credit_no);
////	}
//	if ($credit_invoice) {
//		update_customer_trans_version(get_parent_type(ST_CUSTCREDIT), $credit_note->src_docs );
//		$total = $credit_note_total + $credit_note->freight_cost + $items_added_tax
//		+ $freight_added_tax;
//		if (!credit_sales_invoice_allocate($credit_invoice, $credit_no, $total, $credit_date))
//			return false;
//	}
//
//	$total = 0;
////	foreach ($credit_note->line_items as $credit_line)
//	{
//
////		if ($credit_invoice && $credit_line->qty_dispatched!=$credit_line->qty_old ) {
////			update_parent_line(ST_CUSTCREDIT, $credit_line->src_id,($credit_line->qty_dispatched
////			-$credit_line->qty_old));
////		}
//
//		$line_taxfree_price = get_tax_free_price_for_item($stock_id, $price,
//		0, 0/*$credit_note->tax_included*/, 0/*$credit_note->tax_group_array*/);
//
//		$line_tax = get_full_price_for_item($stock_id, $price,
//		0, 0/*$credit_note->tax_included*/, 0/*$credit_note->tax_group_array*/) - $line_taxfree_price;
//
//		write_customer_trans_detail_item(ST_CUSTCREDIT, $credit_no, $stock_id,
//		$description, $qty,
//		$price, $line_tax, 0/*$credit_line->discount_percent*/,
//		0/*$credit_line->standard_cost*/, 0/*$credit_line->src_id*/, 0/*$trans_no==0 ? 0:  $credit_line->id*/, 1);
//
////		add_credit_movements_item($credit_note, $credit_line,
////		$credit_type, $line_taxfree_price+$line_tax, $credit_invoice, 1);
//
//		add_stock_move_customer(ST_CUSTCREDIT,  $stock_id,
//			$credit_no, 'DEF',
//			$credit_date, $reference, $qty,
//			/*$curr_std_cost*/0,  0, $price,
//			0/*$credit_line->discount_percent*/, 1);
//
//
//		if ($branch_data['sales_account'] != "")
//			$sales_account = $branch_data['sales_account'];
//		else
//			$sales_account = $stock_gl_codes['sales_account'];
//		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $sales_account, 0, 0,
//			($line_taxfree_price * $qty), $customer_id,
//			"The credit note GL posting could not be inserted");
//
//
////		$total += add_gl_trans_credit_costs($credit_note, $credit_line, $credit_no,
////		$credit_date, $credit_type, 0, $branch_data);
//	} /*end of credit_line loop */
//
//	/*Post credit note transaction to GL credit debtors,
//	debit freight re-charged and debit sales */
//
//	if (($credit_note_total + 0/*$credit_note->freight_cost*/) != 0) {
//
//		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $branch_data["receivables_account"], 0, 0,
//		-($credit_note_total + 0/*$credit_note->freight_cost*/ + $items_added_tax + $freight_added_tax),
//		$customer_id,
//		"The total debtor GL posting for the credit note could not be inserted",0,1);
//	}
//
////	if ($credit_note->freight_cost != 0) {
////		$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $company_data["freight_act"], 0, 0,
////		$credit_note->get_tax_free_shipping(), $credit_note->customer_id,
////		"The freight GL posting for this credit note could not be inserted");
////	}
//
////	foreach ($taxes as $taxitem) {
////		if ($taxitem['Net'] != 0) {
////
////			$ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($credit_note->customer_id), $credit_note->document_date);
////			add_trans_tax_details(ST_CUSTCREDIT, $credit_no, $taxitem['tax_type_id'],
////			$taxitem['rate'], $credit_note->tax_included, $taxitem['Value'],
////			$taxitem['Net'], $ex_rate,
////			$credit_note->document_date, $credit_note->reference);
////
////			$total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $taxitem['sales_gl_code'], 0, 0,
////			$taxitem['Value'], $credit_note->customer_id,
////			"A tax GL posting for this credit note could not be inserted");
////		}
////	}
//	/*Post a balance post if $total != 0 */
//	add_gl_balance(ST_CUSTCREDIT, $credit_no, $credit_date, -$total, PT_CUSTOMER, $customer_id);
//
//	add_comments(ST_CUSTCREDIT, $credit_no, $credit_date, "OB");
//
//	if ($trans_no == 0)
//	{
//		$Refs->save(ST_CUSTCREDIT, $credit_no, $reference);
//	}
//	hook_db_postwrite($credit_note, ST_CUSTCREDIT);
//	commit_transaction();
//
//	return $credit_no;
//}


function write_customer_import_credit_note($customer_id, $Item, $date,
       $qty, $price, $payment_terms, $sales_type_id, $dimension_id,
       $dimension2_id, $default_ship_via, $location, $reference, $comments, 
       $due_date)
{
    global $Refs;

//    if (is_array($credit_note->src_docs)) {
//        $docs = array_keys($credit_note->src_docs);
//        $credit_invoice = reset($docs);
//    }
//    else
//        $credit_invoice = $credit_note->src_docs;

    $credit_date = $date;
//    $tax_group_id = $credit_note->tax_group_id;
//    $trans_no = $credit_note->trans_no;
//    if (is_array($trans_no)) {
//        $trans_no = key($trans_no);
//    }

    $Comments = "Opening Balance";
    $trans_type = ST_CUSTCREDIT;
    $credit_type =  'Return';
    if($reference == '')
        $reference = $Refs->get_next($trans_type, null, array('date' => $credit_date,
            'customer' => $customer_id['customer_id'], 'branch' => $customer_id['branch_code']));
    begin_transaction();
    hook_db_prewrite($credit_note, ST_CUSTCREDIT);

//	$company_data = get_company_prefs();
//	S.H
//  $GetShipper = get_shipper_for_invoice($credit_note->ship_via);

    $branch_data = get_branch_accounts($credit_note->Branch);
    $ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($customer_id), $credit_date);
    add_new_exchange_rate(get_customer_currency($customer_id['customer_id']), $credit_date, $ex_rate);

    $credit_note_total = $price;
//    $freight_tax = 0;
//    $taxes = $credit_note->get_taxes();
//    $tax_total = 0;
//    foreach ($taxes as $taxitem) {
//        $taxitem['Value'] =  round2($taxitem['Value'], user_price_dec());
//        $tax_total += $taxitem['Value'];
//    }
//    if ($credit_note->tax_included == 0) {
//        $items_added_tax = $tax_total-$freight_tax;
//        $freight_added_tax = $freight_tax;
//    } else {
//        $items_added_tax = 0;
//        $freight_added_tax = 0;
//    }
    // If the Customer Branch AR Account is set to a Bank Account,
    // the transaction will be settled at once.
//    if (is_bank_account($branch_data['receivables_account']))
//        $alloc = $credit_note_total + $items_added_tax + $credit_note->freight_cost + $freight_added_tax;
//    else
//        $alloc = 0;
//
//    if (!isset($credit_note->order_no))
//        $credit_note->order_no = 0;

    /*Now insert the Credit Note into the debtor_trans table with the allocations as calculated above*/
    // all amounts in debtor's currency
    $credit_no = write_customer_trans(ST_CUSTCREDIT, 0, $customer_id['customer_id'],
        $customer_id['branch_code'], $credit_date, $reference,
        $credit_note_total, 0, 0,
        0, 0,
        $sales_type_id, 0, $default_ship_via,
        $due_date, 0, 0, $dimension_id, $dimension2_id,
        $payment_terms, 1, 0,
        0,0,0,0,0,0,
        0,0,0,0,0, 0,
        0,0,0,0,0,0);

    $sql= 'UPDATE '.TB_PREF. 'debtor_trans SET version=version+1
			WHERE type='.db_escape(ST_CUSTCREDIT).' AND trans_no='.db_escape($credit_no);
    db_query($sql, 'Concurrent editing conflict');


//    if ($trans_no == 0) {
//        $credit_note->trans_no = array($credit_no=>0);
//    } else {
//        delete_comments(ST_CUSTCREDIT, $credit_no);
//        void_cust_allocations(ST_CUSTCREDIT, $credit_no, $credit_date);
//        void_gl_trans(ST_CUSTCREDIT, $credit_no, true);
//        void_stock_move(ST_CUSTCREDIT, $credit_no);
//        void_trans_tax_details(ST_CUSTCREDIT, $credit_no);
//    }
//    if ($credit_invoice) {
//        update_customer_trans_version(get_parent_type(ST_CUSTCREDIT), $credit_note->src_docs );
//        $total = $credit_note_total ;
//
//        if (!credit_sales_invoice_allocate($credit_invoice, $credit_no, $total, $credit_date))
//            return false;
//    }

    $total = 0;

//    foreach ($credit_note->line_items as $credit_line) {
//        if ($credit_invoice && $credit_line->qty_dispatched!=$credit_line->qty_old ) {
//            update_parent_line(ST_CUSTCREDIT, $credit_line->src_id,($credit_line->qty_dispatched
//                -$credit_line->qty_old));
//        }
//
//        $line_taxfree_price = get_tax_free_price_for_item($credit_line->stock_id, $credit_line->price,
//            0, $credit_note->tax_included, $credit_note->tax_group_array);
//
//        $line_tax = get_full_price_for_item($credit_line->stock_id, $credit_line->price,
//                0, $credit_note->tax_included, $credit_note->tax_group_array) - $line_taxfree_price;
        //-------------conversion-factor----------------------------------------------------------
//        $item=get_item($credit_line->stock_id);
//        if($credit_line->units_id !=  $item['units'])
//        {
//            if($item['con_type'] == 0)
//                $qty_dispatched = $credit_line->qty_dispatched / $credit_line->con_factor;
//            else
//                $qty_dispatched = $credit_line->con_factor / $credit_line->qty_dispatched  ;
//        }
//        else{
//            $qty_dispatched = $credit_line->qty_dispatched;
//        }
////		$pref=get_company_prefs();
//        $myrow_factors= get_company_item_pref('con_factor');
//        if($myrow_factors['sale_enable'] == 0 && $myrow_factors['label_value'] ){
//            $item_info = get_item_edit_info($credit_line->stock_id);
//            $credit_line->con_factor  = $item_info['con_factor'];
//        }
//        else
//            $credit_line->con_factor;

        //--------------------------end-------------------------------------------
        write_customer_trans_detail_item(ST_CUSTCREDIT, $credit_no, $Item['stock_id'],
            $Item['description'], $qty,$price, 0, 0,0, 0, 0,
            $Item['units'], 0, '','','','','','',0,
            0, 0,0,0,0,0,0,0,0,
            0, 0,0,0,0,0);
        add_stock_move(ST_CUSTCREDIT,  $Item['stock_id'],
            $credit_no, $location, $credit_date, $credit_type, $qty,
            0, $price,0,0,'', '', '', '', '',
            '', 0, 0, 0, 0,0, 0,
            0, 0, 0, 0, 0, 0,0, 0, 0,
            $Item['units'],0,0);
        $stock_gl_code = get_stock_gl_code($Item['stock_id']);
        $total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, 301002, $dimension_id, 0,
            ($credit_note_total),
            $customer_id['customer_id'],
            "The total debtor GL posting for the credit note could not be inserted");
//      if ($credit_type == 'Return')
//            add_credit_movements_item($credit_note, $credit_line,
//                $credit_type, ($line_taxfree_price+$line_tax)*(1-$credit_line->discount_percent), $credit_invoice,$credit_line->person_id,$credit_line->discount_pecent,
//                $credit_line->text1,$credit_line->text2,
//                $credit_line->text3,$credit_line->text4,$credit_line->text5,$credit_line->text6,
//                $credit_line->amount1,$credit_line->amount2,
//                $credit_line->amount3,$credit_line->amount4,$credit_line->amount5,$credit_line->amount6,
//                $credit_line->date1,$credit_line->date2,
//                $credit_line->date3,
//                $credit_line->combo1,$credit_line->combo2,
//                $credit_line->combo3,$credit_line->combo4,$credit_line->combo5,
//                $credit_line->combo6,$credit_line->batch,$credit_line->units_id,$credit_line->con_factor,$qty_dispatched);
        // $total += add_gl_trans_credit_costs($credit_note, $credit_line, $credit_no,
            // $credit_date, $credit_type, $write_off_acc, $branch_data);
//    } /*end of credit_line loop */
    /*Post credit note transaction to GL credit debtors,
    debit freight re-charged and debit sales */

    // if (($credit_note_total) != 0) 
    {
        $total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, 101001, $dimension_id, 0,
            -($credit_note_total),
            $customer_id['customer_id'],
            "The total debtor GL posting for the credit note could not be inserted");
    }
//    if ($credit_note->freight_cost != 0) {
//        $total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date,  /*$company_data["freight_act"]*/$GetShipper['shipping_account'], 0, 0,
//            $credit_note->get_tax_free_shipping(), $credit_note->customer_id,
//            "The freight GL posting for this credit note could not be inserted");
//    }

//    foreach ($taxes as $taxitem) {
//        if ($taxitem['Net'] != 0) {
            $ex_rate = get_exchange_rate_from_home_currency(get_customer_currency($customer_id['customer_id']), $credit_date);
            add_trans_tax_details(ST_CUSTCREDIT, $credit_no, 0,
                $ex_rate, 0, 0,
                $price, $ex_rate,
                $customer_id['customer_id'], $reference, TR_OUTPUT);

//            $total += add_gl_trans_customer(ST_CUSTCREDIT, $credit_no, $credit_date, $taxitem['sales_gl_code'], 0, 0,
//                $taxitem['Value'], $credit_note->customer_id,
//                "A tax GL posting for this credit note could not be inserted");
//        }
//    }

    /*Post a balance post if $total != 0 */
    add_gl_balance(ST_CUSTCREDIT, $credit_no, $credit_date, -$total, PT_CUSTOMER, $customer_id['customer_id']);
    add_comments(ST_CUSTCREDIT, $credit_no, $credit_date, $Comments);
//  if ($trans_no == 0)
    $Refs->save(ST_CUSTCREDIT, $credit_no, $reference);
    hook_db_postwrite($credit_note, ST_CUSTCREDIT);
    commit_transaction();
    return $credit_no;
}
function get_stock_master_info_credit_ob($stock_id)
{
	$sql = "SELECT *
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql, "The standard cost cannot be retrieved");
	return db_fetch($result);
}

?>