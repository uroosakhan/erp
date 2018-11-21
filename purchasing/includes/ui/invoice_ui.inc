<?php
//--------------------------------------------------------------------------------------------------

function copy_from_trans(&$supp_trans)
{
	$_POST['Comments'] = $supp_trans->Comments;
	$_POST['tran_date'] = $supp_trans->tran_date;
	$_POST['due_date'] = $supp_trans->due_date;
	$_POST['supp_reference'] = $supp_trans->supp_reference;
	$_POST['reference'] = $supp_trans->reference;
	$_POST['supplier_id'] = $supp_trans->supplier_id;
    $_POST['dimension'] = $supp_trans->dimension;
    $_POST['dimension2'] = $supp_trans->dimension2;
	$_POST['_ex_rate'] = $supp_trans->ex_rate;
	if (isset($supp_trans->tax_overrides))
	    foreach($supp_trans->tax_overrides as $id => $value)
		    $_POST['mantax'][$id] = price_format($value);
}

//--------------------------------------------------------------------------------------------------

function copy_to_trans(&$supp_trans)
{
	$supp_trans->Comments = $_POST['Comments'];
	$supp_trans->tran_date = $_POST['tran_date'];
	$supp_trans->due_date = $_POST['due_date'];
	$supp_trans->supp_reference = $_POST['supp_reference'];
	$supp_trans->reference = $_POST['reference'];
   	$supp_trans->dimension = @$_POST['dimension'];
	$supp_trans->dimension2 =  @$_POST['dimension2'];



	$supp_trans->ex_rate = input_num('_ex_rate', null);

	$supp_trans->ov_amount = $supp_trans->ov_discount = 0; /* for starters */
    if (isset($_POST['mantax'])) {
		foreach($_POST['mantax'] as $id => $tax) {
	    	$supp_trans->tax_overrides[$id] = user_numeric($_POST['mantax'][$id]);
	    	}
	}
	else
		unset($supp_trans->tax_overrides);

	if (count($supp_trans->grn_items) > 0)
	{
		foreach ( $supp_trans->grn_items as $grn)
		{

            $pref =   get_company_prefs();
		    if($pref['disc_in_amount'] == 1) {
                $supp_trans->ov_amount += round2((  $grn->chg_price - $grn->discount_percent) * $grn->this_quantity_inv ,
                    user_price_dec());
            }
		    else {
                $supp_trans->ov_amount += round2((1 - $grn->discount_percent / 100) * ($grn->this_quantity_inv * $grn->chg_price),
                    user_price_dec());
            }
		}
	}
	if (count($supp_trans->gl_codes) > 0)
	{
		foreach ( $supp_trans->gl_codes as $gl_line)
		{
			if (!is_tax_account($gl_line->gl_code) || $supp_trans->tax_included)
				$supp_trans->ov_amount += $gl_line->amount;
		}
	}
}

//--------------------------------------------------------------------------------------------------

function invoice_header(&$supp_trans)
{
	global $Ajax, $Refs;
	
	// if vars have been lost, recopy
	if (!isset($_POST['tran_date']))
		copy_from_trans($supp_trans);

	start_outer_table(TABLESTYLE2, "width='95%'");

	table_section(1);

	if ($supp_trans->trans_type == ST_SUPPCREDIT && $supp_trans->src_docs)
	{
		$_POST['supplier_id'] = $supp_trans->supplier_id;
		$supp = $supp_trans->supplier_name." - ".$supp_trans->currency;
		label_row(_("Supplier:"), $supp.hidden('supplier_id', $_POST['supplier_id'], false));
	}
	else
	{
    	if (!isset($_POST['supplier_id']) && (get_global_supplier() != ALL_TEXT))
    		$_POST['supplier_id'] = get_global_supplier();

		if (!$supp_trans->trans_no)
			supplier_list_row(_("Supplier:"), 'supplier_id', $_POST['supplier_id'], false, true);
		else
			label_row(_("Supplier:"), $supp_trans->supplier_name
			.($supp_trans->currency ? ' - '.$supp_trans->currency  : '')
			.hidden('supplier_id', $_POST['supplier_id'], false));
	}
	if ($supp_trans->supplier_id != $_POST['supplier_id'])
	{
		// supplier has changed
		// delete all the order items - drastic but necessary because of
		// change of currency, etc
		$supp_trans->clear_items();
		read_supplier_details_to_trans($supp_trans, $_POST['supplier_id']);
		copy_from_trans($supp_trans);
	}

   	date_row(_("Date") . ":", 'tran_date', '', true, 0, 0, 0, "", true);

   	// ref_row(_("Reference:"), 'reference', '', $Refs->get_next($supp_trans->trans_type, null, 
   	// 	array('supplier' => get_post('supplier_id'), 'date' => get_post('tran_date'))), false, $supp_trans->trans_type);

    	ref_row(_("Reference:"), 'reference', '', null, false, $supp_trans->trans_type, array('date'=> @$_POST['tran_date']));

	if ($supp_trans->trans_type == ST_SUPPCREDIT)
	{
		label_row(_("Source Invoices:"), implode(',' , $supp_trans->src_docs),'','','src_docs');
	}
		text_row(_("Supplier's Ref.:"), 'supp_reference', $_POST['supp_reference'], 20, 60);
	table_section(2, "33%");

	if (isset($_POST['_tran_date_changed'])) {
		$Ajax->activate('_ex_rate');
		$supp_trans->tran_date = $_POST['tran_date'];
		get_duedate_from_terms($supp_trans);
		$_POST['due_date'] = $supp_trans->due_date;
		$Ajax->activate('due_date');
	}

    date_row(_("Due Date") . ":", 'due_date');

    label_row(_("Terms:"), $supp_trans->terms['description']);

	if (get_company_pref('use_dimension'))
		dimensions_list_row(_('Dimension').':', 'dimension', null, true, _('Default'), false, 1);

	if (get_company_pref('use_dimension') == 2)
		dimensions_list_row(_('Dimension 2').':', 'dimension2', null, true, _('Default'), false, 2);

	table_section(3, "33%");

	set_global_supplier($_POST['supplier_id']);

	$supplier_currency = get_supplier_currency($supp_trans->supplier_id);

	$company_currency = get_company_currency();

	if ($supplier_currency != $company_currency)
	{
        label_row(_("Supplier's Currency:"), "<b>" . $supplier_currency . "</b>");
		exchange_rate_display($company_currency, $supplier_currency, $_POST['tran_date']);
	}

  	label_row(_("Tax Group:"), $supp_trans->tax_description);
	supplier_credit_row($supp_trans->supplier_id, $supp_trans->credit);

	end_outer_table(1);
}

//--------------------------------------------------------------------------------------------------

function invoice_totals(&$supp_trans)
{
	global $Ajax;

	copy_to_trans($supp_trans);

	$dim = get_company_pref('use_dimension');
 	$colspan = ($dim == 2 ? 7 : ($dim == 1 ? 6 : 5));
 	div_start('tax_table');
   	start_table(TABLESTYLE, "width='95%'");
   	$pref = get_company_prefs();
    if($pref['disc_in_amount'] == 1) {

        label_row(_("Sub-total:"), price_format($supp_trans->ov_amount ), "colspan=$colspan align=right", "align=right");
    }
    else{
        label_row(_("Sub-total:"), price_format($supp_trans->ov_amount), "colspan=$colspan align=right", "align=right");
    }
    $taxes = $supp_trans->get_taxes($supp_trans->tax_group_id);
    $tax_total = display_edit_tax_items($taxes, $colspan, $supp_trans->tax_included, 0, true);

    $display_total = price_format($supp_trans->ov_amount + $tax_total);

	if ($supp_trans->trans_type == ST_SUPPINVOICE)
    	label_row(_("Invoice Total:"), 
    		$display_total, "colspan=$colspan align=right style='font-weight:bold;'", "align=right style='font-weight:bold;'");
    else
		label_row(_("Credit Note Total"),
			$display_total, "colspan=$colspan align=right style='font-weight:bold;color:red;'", "nowrap align=right style='font-weight:bold;color:red;'");

    end_table(1);
    start_table(TABLESTYLE2);
    textarea_row(_("Memo:"), "Comments", null, 50, 3);
    end_table(1);
	div_end();
}

//--------------------------------------------------------------------------------------------------
function display_gl_controls(&$supp_trans, $k)
{
$disc_amount = 0;
    foreach ($supp_trans->grn_items as $entered_grn){
        $grn_batch = get_grn_batch_from_item($entered_grn->id);
        $row = get_grn_batch($grn_batch);
        $disc_amount += $row['discount1'];


    }
    global $Ajax ;
//    $_POST['amount'] = price_format($disc_amount);


	$accs = get_supplier_accounts($supp_trans->supplier_id);
	$_POST['gl_code'] = $accs['purchase_account'] ? 
		$accs['purchase_account'] : get_company_pref('default_cogs_act');

 $_POST['amount'] = -$disc_amount;
 
	alt_table_row_color($k);
	echo gl_all_accounts_list('gl_code', null, true, true);
	$dim = get_company_pref('use_dimension');
	if ($dim >= 1)
		dimensions_list_cells(null, 'dimension_id', null, true, " ", false, 1);
	if ($dim > 1) 
        dimensions_list_cells(null, 'dimension2_id', null, true, " ", false, 2);
        amount_cells_ex(null, 'amount', 12,null,null,null,$_POST['amount'] );
    
    
	if ($dim < 1)	
		text_cells_ex(null, 'memo_', 35, 50, null, null, null, hidden('dimension_id', 0, false).hidden('dimension2_id', 0, false));
	elseif ($dim < 2)	
		text_cells_ex(null, 'memo_', 35, 50, null, null, null, hidden('dimension2_id', 0, false));
	else	
		text_cells_ex(null, 'memo_', 35, 50, null, null, null);
	submit_cells('AddGLCodeToTrans', _("Add"), "",
		    _('Add GL Line'), true);
	submit_cells('ClearFields', _("Reset"), "",
		    _("Clear all GL entry fields"), true);
		    if(($disc_amount) > 0)
{
    $Ajax->activate('amount');
}

	end_row();	
}

// $mode = 0 none at the moment
//		 = 1 display on invoice/credit page
//		 = 2 display on view invoice
//		 = 3 display on view credit

function display_gl_items(&$supp_trans, $mode=0,$grn_id)
{
	global $path_to_root, $Ajax;


    // if displaying in form, and no items, exit
    if (($mode == 2 || $mode == 3) && count($supp_trans->gl_codes) == 0)
    	return 0;

	if ($supp_trans->trans_type == ST_SUPPINVOICE)
		$heading = _("GL Items for this Invoice");
	else
		$heading = _("GL Items for this Credit Note");

	start_outer_table(TABLESTYLE, "width='95%'");

	if ($mode == 1)
	{
		$qes = has_quick_entries(QE_SUPPINV);
		if ($qes !== false)
		{
			echo "<div style='float:right;'>";
			echo _("Quick Entry:")."&nbsp;"; 
			echo quick_entries_list('qid', null, QE_SUPPINV, true);
			$qid = get_quick_entry(get_post('qid'));
			if (list_updated('qid')) {
				unset($_POST['totamount']); // enable default
				$Ajax->activate('totamount');
			}
			echo "&nbsp;".$qid['base_desc'].":"."&nbsp;";

			$amount = input_num('totamount', $qid['base_amount']);
			$dec = user_price_dec();
			echo "<input class='amount' type='text' name='totamount' size='7' maxlength='12' dec='$dec' value='$amount'>&nbsp;";
			submit('go', _("Go"), true, false, true);
			echo "</div>";

		}	
	}
	display_heading($heading);

	end_outer_table(0, false);

	div_start('gl_items');
	start_table(TABLESTYLE, "width='95%'");
    	$supplier_currenciess = get_supplier_currency($supp_trans->supplier_id);

	$dim = get_company_pref('use_dimension');
	if ($dim == 2)
    	$th = array(_("Account"), _("Name"), _("Dimension")." 1", _("Dimension")." 2", _("Amount .($supplier_currenciess)"), _("Memo"));
	elseif ($dim == 1)
    	$th = array(_("Account"), _("Name"), _("Dimension"), _("Amount .($supplier_currencies)"), _("Memo"));
    else
    	$th = array(_("Account"), _("Name"), _("Amount .($supplier_currenciess)"), _("Memo"));

	if ($mode == 1)
	{
		$th[] = "";
		$th[] = "";
	}	
	table_header($th);
	$total_gl_value=$total = 0;
	$i = $k = 0;

	if (count($supp_trans->gl_codes) > 0)
	{

		foreach ($supp_trans->gl_codes as $entered_gl_code)
		{

			alt_table_row_color($k);

			if ($mode == 3)
				$entered_gl_code->amount = -$entered_gl_code->amount;

			label_cell($entered_gl_code->gl_code);
			label_cell($entered_gl_code->gl_act_name);

			if ($dim >= 1)
   				label_cell(get_dimension_string($entered_gl_code->gl_dim, true));
			if ($dim > 1)
   				label_cell(get_dimension_string($entered_gl_code->gl_dim2, true));

			amount_cell($entered_gl_code->amount, true);
			label_cell($entered_gl_code->memo_);

			if ($mode == 1)
			{
				delete_button_cell("Delete2" . $entered_gl_code->Counter, _("Delete"),
					  _('Remove line from document'));
				label_cell("");
			}	
			end_row();
			if ($mode > 1) {
				if ($supp_trans->tax_included || !is_tax_account($entered_gl_code->gl_code))
					$total_gl_value += $entered_gl_code->amount;
			}		
			else	
				$total_gl_value += $entered_gl_code->amount;
			$total += $entered_gl_code->amount;	
			$i++;
			if ($i > 15)
			{
				$i = 0;
				table_header($th);
			}
		}

	}
	if ($mode == 1)
		display_gl_controls($supp_trans, $k);
	$colspan = ($dim == 2 ? 4 : ($dim == 1 ? 3 : 2));
	label_row(_("Total"), price_format($total),
		"colspan=".$colspan." align=right", "nowrap align=right", ($mode==1?3:1));

	end_table(1);
	div_end();

	return $total_gl_value;
}

//--------------//-----------------------------------------------------------------------------------------

function display_grn_items_for_selection(&$supp_trans, $k)
{
	if ($supp_trans->trans_type == ST_SUPPINVOICE)	// outstanding grns and eventually for selected invoice
		$result = get_grn_items(0, $supp_trans->supplier_id, true, false, $supp_trans->trans_no,"","",0);
	else
	{ // only invoiced
		if (isset($_POST['receive_begin']) && isset($_POST['receive_end']) && isset($_POST['invoice_no']))
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true, $_POST['invoice_no'], $_POST['receive_begin'], $_POST['receive_end']);
		elseif ($supp_trans->src_docs)
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true, array_keys($supp_trans->src_docs));
		else
			$result = get_grn_items(0, $supp_trans->supplier_id, false, true);
	}
    if (db_num_rows($result) == 0)
    {
    	return false;
    }

    /*Set up a table to show the outstanding GRN items for selection */
    while ($myrow = db_fetch($result))
    {
		$grn_already_on_invoice = false;

    	foreach ($supp_trans->grn_items as $entered_grn)
    	{
    		if ($entered_grn->id == $myrow["id"])
    		{
    			$grn_already_on_invoice = true;
    		}
    	}

    	if ($grn_already_on_invoice == false)
    	{
			alt_table_row_color($k);
			$myrow_1 = get_company_item_pref_from_position(1);
			$myrow_2 = get_company_item_pref_from_position(2);
			$myrow_3 = get_company_item_pref_from_position(3);
			$myrow_4 = get_company_item_pref_from_position(4);
			$myrow_5 = get_company_item_pref_from_position(5);
			$myrow_6 = get_company_item_pref_from_position(6);
			$myrow_7 = get_company_item_pref_from_position(7);
			$myrow_8 = get_company_item_pref_from_position(8);
			$myrow_9 = get_company_item_pref_from_position(9);
			$myrow_10 = get_company_item_pref_from_position(10);
			$myrow_11 = get_company_item_pref_from_position(11);
			$myrow_12 = get_company_item_pref_from_position(12);
			$myrow_13 = get_company_item_pref_from_position(13);
			$myrow_14 = get_company_item_pref_from_position(14);
			$myrow_15 = get_company_item_pref_from_position(15);
			$myrow_16 = get_company_item_pref_from_position(16);
			$myrow_17 = get_company_item_pref_from_position(17);
			$myrow_18 = get_company_item_pref_from_position(18);
			$myrow_19 = get_company_item_pref_from_position(19);
			$myrow_20 = get_company_item_pref_from_position(20);
			$myrow_21 = get_company_item_pref_from_position(21);
			$myrow_22 = get_company_item_pref_from_position(22);

			
			
			$n = $myrow["id"];
			$batch_name=get_batch_name_by_id($myrow['grn_batch']);
			 global $SysPrefs;
            if ($SysPrefs->show_doc_ref() == 0) {
                label_cell(get_trans_view_str(ST_SUPPRECEIVE, $myrow["grn_batch_id"]));
            }
            else{
                label_cell(get_trans_view_str(ST_SUPPRECEIVE, $myrow["grn_batch_id"], get_reference(ST_SUPPRECEIVE,$myrow["grn_batch_id"])));
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
		   	hidden('qty_recd'.$n, $qty_recd);
           	hidden('item_code'.$n, $myrow["item_code"]);
           	hidden('item_description'.$n, $myrow["description"]);

           	hidden('prev_quantity_inv'.$n, $myrow['quantity_inv']);
           	hidden('order_price'.$n, $myrow['unit_price']);
           	hidden('std_cost_unit'.$n, $myrow['std_cost_unit']);
           	hidden('po_detail_item'.$n, $myrow['po_detail_item']);
           	hidden('batch'.$n, $myrow['grn_batch']);
			hidden('con_factor'.$n, $myrow['con_factor']);
			hidden('units_id'.$n, $myrow['units_id']);
			hidden('dimension'.$n, $Dimension['dimension']);
			hidden('dimension2'.$n, $Dimension['dimension2']);
            hidden('discount_percent'.$n, $myrow['discount_percent']);
            hidden('grn_id'.$n, $myrow['grn_batch_id']);
        	 if ($SysPrefs->show_doc_ref() == 0) {
        	    $row1 = get_supp_invoice_id($myrow["purch_order_no"]);
                label_cell(get_trans_view_str(ST_PURCHORDER, $myrow["purch_order_no"]));
                label_cell(get_trans_view_str(ST_SUPPINVOICE, $row1));
            }
            else{
                $row1 = get_supp_invoice_id($myrow["purch_order_no"]);
                label_cell(get_trans_view_str(ST_PURCHORDER, $myrow["purch_order_no"], get_reference(ST_PURCHORDER,$myrow["purch_order_no"])));
                label_cell(get_trans_view_str(ST_SUPPINVOICE, $row1, get_reference(ST_SUPPINVOICE,$row1)));
            }
			$pref=get_company_prefs();
			$con_factor = get_company_item_pref('con_factor');
		
			$DimensionName = get_dimension_name_($Dimension['dimension']);
			label_cell($DimensionName);
            label_cell($myrow["item_code"]);
            label_cell($myrow["description"]);
			if($pref['alt_uom'] == 1) {
				label_cell($myrow["units_id"]);
				if($con_factor['purchase_enable'] == 1)
				amount_cell($myrow["con_factor"]);
			}
			else{
                label_cell($myrow["units"]);
            }
			if($pref['batch'] == 1){
			label_cell($batch_name['name']);
			$batch=get_batch_by_id($myrow["grn_batch"]);
			label_cell(sql2date($batch["exp_date"]));
}

			if($myrow_1['purchase_enable'])
			{
				if($myrow_1['type'] == 1)
					amount_cells(null, $myrow_1['name'].$n, $myrow[$myrow_1['name']]);
				elseif($myrow_1['type'] == 2)
					combo1_list_cells(null, $myrow_1['name'].$n, $myrow[$myrow_1['name']]);
				elseif($myrow_1['type'] == 3)
				{	$_POST[$myrow_1['name']]= $myrow[$myrow_1['name']];
					date_cells(null, $myrow_1['name'].$n);}
				elseif($myrow_1['type'] == 4)
					text_cells(null, $myrow_1['name'].$n, $myrow[$myrow_1['name']], 20, 60);
			}
			if($myrow_2['purchase_enable'])
			{
				if($myrow_2['type'] == 1)
					amount_cells(null, $myrow_2['name'].$n, $myrow[$myrow_2['name']]);
				elseif($myrow_2['type'] == 2)
					combo1_list_cells(null, $myrow_2['name'].$n, $myrow[$myrow_2['name']]);
				elseif($myrow_2['type'] == 3)
				{	$_POST[$myrow_2['name']] = $myrow[$myrow_2['name']];
					date_cells(null, $myrow_2['name'].$n);}
				elseif($myrow_2['type'] == 4)
					text_cells(null, $myrow_2['name'].$n, $myrow[$myrow_2['name']], 20, 60);
			}
			if($myrow_3['purchase_enable'])
			{
				if($myrow_3['type'] == 1)
					amount_cells(null, $myrow_3['name'].$n, $myrow[$myrow_3['name']]);
				elseif($myrow_3['type'] == 2)
					combo1_list_cells(null, $myrow_3['name'].$n, $myrow[$myrow_3['name']]);
				elseif($myrow_3['type'] == 3)
				{	$_POST[$myrow_3['name']] = $myrow[$myrow_3['name']];
					date_cells(null, $myrow_3['name'].$n);}
				elseif($myrow_3['type'] == 4)
					text_cells(null, $myrow_3['name'].$n, $myrow[$myrow_3['name']], 20, 60);
			}
			if($myrow_4['purchase_enable'])
			{
				if($myrow_4['type'] == 1)
					amount_cells(null, $myrow_4['name'].$n, $myrow[$myrow_4['name']]);
				elseif($myrow_4['type'] == 2)
					combo1_list_cells(null, $myrow_4['name'].$n, $myrow[$myrow_4['name']]);
				elseif($myrow_4['type'] == 3)
				{	$_POST[$myrow_4['name']] = $myrow[$myrow_4['name']];
					date_cells(null, $myrow_4['name'].$n);}
				elseif($myrow_4['type'] == 4)
					text_cells(null, $myrow_4['name'].$n, $myrow[$myrow_4['name']], 20, 60);
			}
			if($myrow_5['purchase_enable'])
			{
				if($myrow_5['type'] == 1)
					amount_cells(null, $myrow_5['name'].$n, $myrow[$myrow_5['name']]);
				elseif($myrow_5['type'] == 2)
					combo1_list_cells(null, $myrow_5['name'].$n, $myrow[$myrow_5['name']]);
				elseif($myrow_5['type'] == 3)
				{	$_POST[$myrow_5['name']] = $myrow[$myrow_5['name']];
					date_cells(null, $myrow_5['name'].$n);}
				elseif($myrow_5['type'] == 4)
					text_cells(null, $myrow_5['name'].$n, $myrow[$myrow_5['name']], 20, 60);
			}
			if($myrow_6['purchase_enable'])
			{
				if($myrow_6['type'] == 1)
					amount_cells(null, $myrow_6['name'].$n, $myrow[$myrow_6['name']]);
				elseif($myrow_6['type'] == 2)
					combo1_list_cells(null, $myrow_6['name'].$n, $myrow[$myrow_6['name']]);
				elseif($myrow_6['type'] == 3)
				{	$_POST[$myrow_6['name']] = $myrow[$myrow_6['name']];
					date_cells(null, $myrow_6['name'].$n);}
				elseif($myrow_6['type'] == 4)
					text_cells(null, $myrow_6['name'].$n, $myrow[$myrow_6['name']], 20, 60);
			}
			if($myrow_7['purchase_enable'])
			{
				if($myrow_7['type'] == 1)
					amount_cells(null, $myrow_7['name'].$n, $myrow[$myrow_7['name']]);
				elseif($myrow_7['type'] == 2)
					combo1_list_cells(null, $myrow_7['name'].$n, $myrow[$myrow_7['name']]);
				elseif($myrow_7['type'] == 3)
				{	$_POST[$myrow_7['name']] = $myrow[$myrow_7['name']];
					date_cells(null, $myrow_7['name'].$n);}
				elseif($myrow_7['type'] == 4)
					text_cells(null, $myrow_7['name'].$n, $myrow[$myrow_7['name']], 20, 60);
			}
			if($myrow_8['purchase_enable'])
			{
				if($myrow_8['type'] == 1)
					amount_cells(null, $myrow_8['name'].$n, $myrow[$myrow_8['name']]);
				elseif($myrow_8['type'] == 2)
					combo1_list_cells(null, $myrow_8['name'].$n, $myrow[$myrow_8['name']]);
				elseif($myrow_8['type'] == 3)
				{	$_POST[$myrow_8['name']] = $myrow[$myrow_8['name']];
					date_cells(null, $myrow_8['name'].$n);}
				elseif($myrow_8['type'] == 4)
					text_cells(null, $myrow_8['name'].$n, $myrow[$myrow_8['name']], 20, 60);
			}
			if($myrow_9['purchase_enable'])
			{
				if($myrow_9['type'] == 1)
					amount_cells(null, $myrow_9['name'].$n, $myrow[$myrow_9['name']]);
				elseif($myrow_9['type'] == 2)
					combo1_list_cells(null, $myrow_9['name'].$n, $myrow[$myrow_9['name']]);
				elseif($myrow_9['type'] == 3)
				{	$_POST[$myrow_9['name']] = $myrow[$myrow_9['name']];
					date_cells(null, $myrow_9['name'].$n);}
				elseif($myrow_9['type'] == 4)
					text_cells(null, $myrow_9['name'].$n, $myrow[$myrow_9['name']], 20, 60);
			}
			if($myrow_10['purchase_enable'])
			{
				if($myrow_10['type'] == 1)
					amount_cells(null, $myrow_10['name'].$n, $myrow[$myrow_10['name']]);
				elseif($myrow_10['type'] == 2)
					combo1_list_cells(null, $myrow_10['name'].$n, $myrow[$myrow_10['name']]);
				elseif($myrow_10['type'] == 3)
				{	$_POST[$myrow_10['name']] = $myrow[$myrow_10['name']];
					date_cells(null, $myrow_10['name']);}
				elseif($myrow_10['type'] == 4)
					text_cells(null, $myrow_10['name'].$n,$myrow[$myrow_10['name']], 20, 60);
			}
			if($myrow_11['purchase_enable'])
			{
				if($myrow_11['type'] == 1)
					amount_cells(null, $myrow_11['name'].$n, $myrow[$myrow_11['name']]);
				elseif($myrow_11['type'] == 2)
					combo1_list_cells(null, $myrow_11['name'].$n,  $myrow[$myrow_11['name']]);
				elseif($myrow_11['type'] == 3)
				{	$_POST[$myrow_11['name']] =  $myrow[$myrow_11['name']];
					date_cells(null, $myrow_11['name'].$n);}
				elseif($myrow_11['type'] == 4)
					text_cells(null, $myrow_11['name'].$n,  $myrow[$myrow_11['name']], 20, 60);
			}
			if($myrow_12['purchase_enable'])
			{
				if($myrow_12['type'] == 1)
					amount_cells(null, $myrow_12['name'].$n,  $myrow[$myrow_12['name']]);
				elseif($myrow_12['type'] == 2)
					combo1_list_cells(null, $myrow_12['name'].$n, $myrow[$myrow_12['name']]);
				elseif($myrow_12['type'] == 3)
				{	$_POST[$myrow_12['name']] = $myrow[$myrow_12['name']];
					date_cells(null, $myrow_12['name'].$n);}
				elseif($myrow_12['type'] == 4)
					text_cells(null, $myrow_12['name'].$n, $myrow[$myrow_12['name']], 20, 60);
			}
			if($myrow_13['purchase_enable'])
			{
				if($myrow_13['type'] == 1)
					amount_cells(null, $myrow_13['name'].$n, $myrow[$myrow_13['name']]);
				elseif($myrow_13['type'] == 2)
					combo1_list_cells(null, $myrow_13['name'].$n, $myrow[$myrow_13['name']]);
				elseif($myrow_13['type'] == 3)
				{	$_POST[$myrow_13['name']] = $myrow[$myrow_13['name']];
					date_cells(null, $myrow_13['name'].$n);}
				elseif($myrow_13['type'] == 4)
					text_cells(null, $myrow_13['name'].$n, $myrow[$myrow_13['name']], 20, 60);
			}
			if($myrow_14['purchase_enable'])
			{
				if($myrow_14['type'] == 1)
					amount_cells(null, $myrow_14['name'].$n, $myrow[$myrow_14['name']]);
				elseif($myrow_14['type'] == 2)
					combo1_list_cells(null, $myrow_14['name'].$n, $myrow[$myrow_14['name']]);
				elseif($myrow_14['type'] == 3)
				{	$_POST[$myrow_14['name']] = $myrow[$myrow_14['name']];
					date_cells(null, $myrow_14['name'].$n);}
				elseif($myrow_14['type'] == 4)
					text_cells(null, $myrow_14['name'].$n, $myrow[$myrow_14['name']], 20, 60);
			}
			if($myrow_15['purchase_enable'])
			{
				if($myrow_15['type'] == 1)
					amount_cells(null, $myrow_15['name'].$n, $myrow[$myrow_15['name']]);
				elseif($myrow_15['type'] == 2)
					combo1_list_cells(null, $myrow_15['name'].$n, $myrow[$myrow_15['name']]);
				elseif($myrow_15['type'] == 3)
				{	$_POST[$myrow_15['name']] = $myrow[$myrow_15['name']];
					date_cells(null, $myrow_15['name'].$n);}
				elseif($myrow_15['type'] == 4)
					text_cells(null, $myrow_15['name'].$n, $myrow[$myrow_15['name']], 20, 60);
			}
			if($myrow_16['purchase_enable'])
			{
				if($myrow_16['type'] == 1)
					amount_cells(null, $myrow_16['name'].$n, $myrow[$myrow_16['name']]);
				elseif($myrow_16['type'] == 2)
					combo1_list_cells(null, $myrow_16['name'].$n, $myrow[$myrow_16['name']]);
				elseif($myrow_16['type'] == 3)
				{	$_POST[$myrow_16['name']] = $myrow[$myrow_16['name']];
					date_cells(null, $myrow_16['name'].$n);}
				elseif($myrow_16['type'] == 4)
					text_cells(null, $myrow_16['name'].$n, $myrow[$myrow_16['name']], 20, 60);
			}
			if($myrow_17['purchase_enable'])
			{
				if($myrow_17['type'] == 1)
					amount_cells(null, $myrow_17['name'].$n, $myrow[$myrow_17['name']]);
				elseif($myrow_17['type'] == 2)
					combo1_list_cells(null, $myrow_17['name'].$n, $myrow[$myrow_17['name']]);
				elseif($myrow_17['type'] == 3)
				{	$_POST[$myrow_17['name']] = $myrow[$myrow_17['name']];
					date_cells(null, $myrow_17['name'].$n);}
				elseif($myrow_17['type'] == 4)
					text_cells(null, $myrow_17['name'].$n, $myrow[$myrow_17['name']], 20, 60);
			}
			if($myrow_18['purchase_enable'])
			{
				if($myrow_18['type'] == 1)
					amount_cells(null, $myrow_18['name'].$n, $myrow[$myrow_18['name']]);
				elseif($myrow_18['type'] == 2)
					combo1_list_cells(null, $myrow_18['name'].$n, $myrow[$myrow_18['name']]);
				elseif($myrow_18['type'] == 3)
				{	$_POST[$myrow_18['name']] = $myrow[$myrow_18['name']];
					date_cells(null, $myrow_18['name'].$n);}
				elseif($myrow_18['type'] == 4)
					text_cells(null, $myrow_18['name'].$n, $myrow[$myrow_18['name']], 20, 60);
			}
			if($myrow_19['purchase_enable'])
			{
				if($myrow_19['type'] == 1)
					amount_cells(null, $myrow_19['name'].$n, $myrow[$myrow_19['name']]);
				elseif($myrow_19['type'] == 2)
					combo1_list_cells(null, $myrow_19['name'].$n, $myrow[$myrow_19['name']]);
				elseif($myrow_19['type'] == 3)
				{	$_POST[$myrow_19['name']] = $myrow[$myrow_19['name']];
					date_cells(null, $myrow_19['name'].$n);}
				elseif($myrow_19['type'] == 4)
					text_cells(null, $myrow_19['name'].$n,$myrow[$myrow_19['name']], 20, 60);
			}
			if($myrow_20['purchase_enable'])
			{
				if($myrow_20['type'] == 1)
					amount_cells(null, $myrow_20['name'].$n, $myrow[$myrow_20['name']]);
				elseif($myrow_20['type'] == 2)
					combo1_list_cells(null, $myrow_20['name'].$n, $myrow[$myrow_20['name']]);
				elseif($myrow_20['type'] == 3)
				{	$_POST[$myrow_20['name']] = $myrow[$myrow_20['name']];
					date_cells(null, $myrow_20['name'].$n);}
				elseif($myrow_20['type'] == 4)
					text_cells(null, $myrow_20['name'].$n, $myrow[$myrow_20['name']], 20, 60);
			}
			if($myrow_21['purchase_enable'])
			{
				if($myrow_21['type'] == 1)
					amount_cells(null, $myrow_21['name'].$n, $myrow[$myrow_21['name']]);
				elseif($myrow_21['type'] == 2)
					combo1_list_cells(null, $myrow_21['name'].$n, $myrow[$myrow_21['name']]);
				elseif($myrow_21['type'] == 3)
				{	$_POST[$myrow_21['name']] = $myrow[$myrow_21['name']];
					date_cells(null, $myrow_21['name'].$n);}
				elseif($myrow_21['type'] == 4)
					text_cells(null, $myrow_21['name'].$n, $myrow[$myrow_21['name']], 20, 60);
			}
			
			
				if($myrow_22['purchase_enable'])
			{
				// if($myrow_21['type'] == 1)
				// 	amount_cells(null, $myrow_21['name'].$n, $myrow[$myrow_21['name']]);
				// elseif($myrow_21['type'] == 2)
				// 	combo1_list_cells(null, $myrow_21['name'].$n, $myrow[$myrow_21['name']]);
				// elseif($myrow_21['type'] == 3)
				// {	$_POST[$myrow_21['name']] = $myrow[$myrow_21['name']];
				// 	date_cells(null, $myrow_21['name'].$n);}
				// elseif($myrow_21['type'] == 4)
				textarea_cells('','text7'.$n, $myrow[$myrow_22['name']], $myrow_22["s_width"], 3);

			}
			
			
			
            label_cell(sql2date($myrow["delivery_date"]));
            $dec = get_qty_dec($myrow["item_code"]);
            qty_cell($qty_recd, false, $dec);
			if($pref['alt_uom'] == 1)
			{
				$item = get_item($myrow["item_code"]);
				$dec = 2;
				if ($myrow['units_id'] != $item['units']) {
					if ($item['con_type'] == 0) {
						$quantity_inv = round2($myrow["quantity_inv"] * $myrow['con_factor'], $dec);

					} else {

						$quantity_inv = $myrow['con_factor'] / $myrow["quantity_inv"];

					}
				} else {
					$quantity_inv = $myrow["quantity_inv"];
				}
			}
			else{

			}
            qty_cell($myrow["quantity_inv"], false, $dec);
            if ($supp_trans->trans_type == ST_SUPPINVOICE)
            	qty_cells(null, 'this_quantity_inv'.$n, number_format2($qty_recd - $myrow["quantity_inv"], $dec),
            		null, null, $dec);
            else
            	qty_cells(null, 'This_QuantityCredited'.$n, number_format2(max($quantity_inv, 0), $dec),
            		null, null, $dec);
            
            $dec2 = 0;
            if ($supp_trans->trans_type == ST_SUPPINVOICE)
            {
                $pref = get_company_prefs();

                if($pref['disc_in_amount'] == 1) {
                    $disc = $myrow["discount_percent"] ;
                }
                else {
                    $disc = $myrow["discount_percent"] ;
                }

                label_cells("", $disc);
            	amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["unit_price"], $dec2), null, null, $dec2);

                if($pref['disc_in_amount'] == 1) {
                    amount_cell(round2(($myrow["unit_price"] - $myrow["discount_percent"] ) * ($qty_recd - $myrow["quantity_inv"])  , user_price_dec()));
                }
                else{

                    amount_cell(round2( (1 - $myrow["discount_percent"] / 100) *  $myrow["unit_price"] * ($qty_recd - $myrow["quantity_inv"] ), user_price_dec()));
                }

                }
            else
            {
                if($pref['disc_in_amount'] == 1) {
                    $disc = $myrow["discount_percent"] ;
                }
                else {
                    $disc = $myrow["discount_percent"] * 100;
                }
                label_cells("", $disc);

            	amount_cells(null, 'ChgPrice'.$n, price_decimal_format($myrow["act_price"], $dec2), null, null, $dec2);
            	amount_cell(round2($myrow["act_price"] * max($myrow['quantity_inv'], 0), user_price_dec()));
            }

            if ($supp_trans->trans_type == ST_SUPPINVOICE)
        		submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Invoice"), true);
        	else	
        		submit_cells('grn_item_id'.$n, _("Add"), '', _("Add to Credit Note"), true);
    		if (($supp_trans->trans_type == ST_SUPPINVOICE) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE')) {	// Added 2008-10-18 by Joe Hunt. Special access rights needed.
        		submit_cells('void_item_id'.$n, _("Remove"), '', _("WARNING! Be careful with removal. The operation is executed immediately and cannot be undone !!!"), true);
				submit_js_confirm('void_item_id'.$n,
					sprintf(_('You are about to remove all yet non-invoiced items from delivery line #%d. This operation also irreversibly changes related order line. Do you want to continue ?'), $n));
			}
			end_row();
    	}
    }
    return true;
}

//------------------------------------------------------------------------------------

// $mode = 0 none at the moment
//		 = 1 display on invoice/credit page
//		 = 2 display on view invoice
//		 = 3 display on view credit

function display_grn_items(&$supp_trans, $mode=0)
{
	global $path_to_root;

    $ret = true;
	$myrow_1 = get_company_item_pref_from_position(1);
	$myrow_2 = get_company_item_pref_from_position(2);
	$myrow_3 = get_company_item_pref_from_position(3);
	$myrow_4 = get_company_item_pref_from_position(4);
	$myrow_5 = get_company_item_pref_from_position(5);
	$myrow_6 = get_company_item_pref_from_position(6);
	$myrow_7 = get_company_item_pref_from_position(7);
	$myrow_8 = get_company_item_pref_from_position(8);
	$myrow_9 = get_company_item_pref_from_position(9);
	$myrow_10 = get_company_item_pref_from_position(10);
	$myrow_11 = get_company_item_pref_from_position(11);
	$myrow_12 = get_company_item_pref_from_position(12);
	$myrow_13 = get_company_item_pref_from_position(13);
	$myrow_14 = get_company_item_pref_from_position(14);
	$myrow_15 = get_company_item_pref_from_position(15);
	$myrow_16 = get_company_item_pref_from_position(16);
	$myrow_17 = get_company_item_pref_from_position(17);
	$myrow_18 = get_company_item_pref_from_position(18);
	$myrow_19 = get_company_item_pref_from_position(19);
	$myrow_20 = get_company_item_pref_from_position(20);
	$myrow_21 = get_company_item_pref_from_position(21);
	$myrow_22 = get_company_item_pref_from_position(22);


    // if displaying in form, and no items, exit
    if (($mode == 2  || $mode == 3) && count($supp_trans->grn_items) == 0)
    	return 0;

	start_outer_table("style='border:1px solid #cccccc;' width='95%'");

	$heading2 = "";
	if ($mode == 1)
	{
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
		{
			$heading = _("Items Received Yet to be Invoiced");
    		if ($_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))	// Added 2008-10-18 by Joe Hunt. Only admins can remove GRNs
				$heading2 = _("WARNING! Be careful with removal. The operation is executed immediately and cannot be undone !!!");
		}
		else
			$heading = _("Delivery Item Selected For Adding To A Supplier Credit Note");
	}
	else
	{
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
			$heading = _("Received Items Charged on this Invoice");
		else
			$heading = _("Received Items Credited on this Note");
	}

	display_heading($heading);

	if ($mode == 1)
	{
		if ($supp_trans->trans_type == ST_SUPPCREDIT)
		{
			echo "</td>";
			text_cells(_("Invoice #"), 'invoice_no');
			date_cells(_("Received between"), 'receive_begin', "", null, 
				-user_transaction_days(), 0, 0, "valign=middle");
			date_cells(_("and"), 'receive_end', '', null, 1, 0, 0, "valign=middle");
			submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), true);
			echo "<td>";
		}

		if ($heading2 != "")
		{
			display_note($heading2, 0, 0, "class='overduefg'");
		}
		echo "</td><td width='10%' align='right'>";
		submit('InvGRNAll', _("Add All Items"), true, false,true);
	}

	end_outer_table(0, false);

  	div_start('grn_items');
	start_table(TABLESTYLE, "width='95%'");
    $pref=get_company_prefs();
    	$supplier_currencies = get_supplier_currency($supp_trans->supplier_id);

    if($pref['disc_in_amount'] == 1) {
        $disc ="Discount";
    }
    else {
        $disc ="Discount %";
    }
	if ($mode == 1)
	{
		$pref=get_company_prefs();
			$con_factor = get_company_item_pref('con_factor');
		if($pref['alt_uom'] == 1 && $con_factor['purchase_enable'] == 1) {
			if ($pref['batch'] == 1)
				$th = array(_("Delivery"), _("P.O."),_("Invoice #"), _("Dim 1"), _("Item"), _("Description"), _("Units"), _("Con factor"), _("Batch"), _("Exp.Date"));
			else
				$th = array(_("Delivery"), _("P.O."), _("Invoice #"),_("Dim 1"), _("Item"), _("Description"),_("Units"), _("Con factor"),);

		}
		else{
			if ($pref['batch'] == 1)
				$th = array(_("Delivery"), _("P.O."),_("Invoice #"), _("Dim 1"), _("Item"), _("Description"), _("Units"), _("Batch"),_("Exp.Date"));
			else
				$th = array(_("Delivery"), _("P.O."), _("Invoice #"),_("Dim 1"), _("Item"), _("Description"),_("Units"));
		}//Text Boxes Headings

		if($myrow_1['purchase_enable']) {
			array_append($th, array($myrow_1['label_value']._("")) );
		}
		if($myrow_2['purchase_enable']) {
			array_append($th, array($myrow_2['label_value']._("")) );
		}
		if($myrow_3['purchase_enable']) {
			array_append($th, array($myrow_3['label_value']._("")) );
		}
		if($myrow_4['purchase_enable']) {
			array_append($th, array($myrow_4['label_value']._("")) );
		}
		if($myrow_5['purchase_enable']) {
			array_append($th, array($myrow_5['label_value']._("")) );
		}
		if($myrow_6['purchase_enable']) {
			array_append($th, array($myrow_6['label_value']._("")) );
		}
		if($myrow_7['purchase_enable']) {
			array_append($th, array($myrow_7['label_value']._("")) );
		}
		if($myrow_8['purchase_enable']) {
			array_append($th, array($myrow_8['label_value']._("")) );
		}
		if($myrow_9['purchase_enable']) {
			array_append($th, array($myrow_9['label_value']._("")) );
		}
		if($myrow_10['purchase_enable']) {
			array_append($th, array($myrow_10['label_value']._("")) );
		}
		if($myrow_11['purchase_enable']) {
			array_append($th, array($myrow_11['label_value']._("")) );
		}
		if($myrow_12['purchase_enable']) {
			array_append($th, array($myrow_12['label_value']._("")) );
		}
		if($myrow_13['purchase_enable']) {
			array_append($th, array($myrow_13['label_value']._("")) );
		}
		if($myrow_14['purchase_enable']) {
			array_append($th, array($myrow_14['label_value']._("")) );
		}
		if($myrow_15['purchase_enable']) {
			array_append($th, array($myrow_15['label_value']._("")) );
		}
		if($myrow_16['purchase_enable']) {
			array_append($th, array($myrow_16['label_value']._("")) );
		}
		if($myrow_17['purchase_enable']) {
			array_append($th, array($myrow_17['label_value']._("")) );
		}
		if($myrow_18['purchase_enable']) {
			array_append($th, array($myrow_18['label_value']._("")) );
		}
		if($myrow_19['purchase_enable']) {
			array_append($th, array($myrow_19['label_value']._("")) );
		}
		if($myrow_20['purchase_enable']) {
			array_append($th, array($myrow_20['label_value']._("")) );
		}
		if($myrow_21['purchase_enable']) {
			array_append($th, array($myrow_21['label_value']._("")) );
		}
		
			if($myrow_22['purchase_enable']) {
			array_append($th, array($myrow_22['label_value']._("")) );
		}
		{

                array_append($th, array(_("Received On"), _("Quantity Received"), _("Quantity Invoiced"),
                    _("Qty Yet To Invoice"), $supp_trans->tax_included ? _("Price after Tax .($supplier_currencies)") : $disc, _("Price before Tax .($supplier_currencies)"),
                    _("Total")));

		}


    }
    else
	{
		$pref=get_company_prefs();
		$con_factor = get_company_item_pref('con_factor');
		if($pref['alt_uom'] == 1 && $con_factor['purchase_enable'] == 1){
			if($pref['batch'] == 1)
				$th = array(_("Delivery"),  _("Item"), _("Description"),  _("Units"),  _("Con factor"), _("Batch"), _("Exp.Date"));
			else
				$th = array(_("Delivery"),  _("Item"), _("Description") ,_("Units"),  _("Con factor"),);
		}
		else{
			if($pref['batch'] == 1)
				$th = array(_("Delivery"),  _("Item"), _("Description"), ("Units"), _("Batch"), _("Exp.Date"));
			else
				$th = array(_("Delivery"),  _("Item"), _("Description"),("Units"));
		}


		//Text Boxes Headings

		if($myrow_1['purchase_enable']) {
			array_append($th, array($myrow_1['label_value']._("")) );
		}
		if($myrow_2['purchase_enable']) {
			array_append($th, array($myrow_2['label_value']._("")) );
		}
		if($myrow_3['purchase_enable']) {
			array_append($th, array($myrow_3['label_value']._("")) );
		}
		if($myrow_4['purchase_enable']) {
			array_append($th, array($myrow_4['label_value']._("")) );
		}
		if($myrow_5['purchase_enable']) {
			array_append($th, array($myrow_5['label_value']._("")) );
		}
		if($myrow_6['purchase_enable']) {
			array_append($th, array($myrow_6['label_value']._("")) );
		}
		if($myrow_7['purchase_enable']) {
			array_append($th, array($myrow_7['label_value']._("")) );
		}
		if($myrow_8['purchase_enable']) {
			array_append($th, array($myrow_8['label_value']._("")) );
		}
		if($myrow_9['purchase_enable']) {
			array_append($th, array($myrow_9['label_value']._("")) );
		}
		if($myrow_10['purchase_enable']) {
			array_append($th, array($myrow_10['label_value']._("")) );
		}
		if($myrow_11['purchase_enable']) {
			array_append($th, array($myrow_11['label_value']._("")) );
		}
		if($myrow_12['purchase_enable']) {
			array_append($th, array($myrow_12['label_value']._("")) );
		}
		if($myrow_13['purchase_enable']) {
			array_append($th, array($myrow_13['label_value']._("")) );
		}
		if($myrow_14['purchase_enable']) {
			array_append($th, array($myrow_14['label_value']._("")) );
		}
		if($myrow_15['purchase_enable']) {
			array_append($th, array($myrow_15['label_value']._("")) );
		}
		if($myrow_16['purchase_enable']) {
			array_append($th, array($myrow_16['label_value']._("")) );
		}
		if($myrow_17['purchase_enable']) {
			array_append($th, array($myrow_17['label_value']._("")) );
		}
		if($myrow_18['purchase_enable']) {
			array_append($th, array($myrow_18['label_value']._("")) );
		}
		if($myrow_19['purchase_enable']) {
			array_append($th, array($myrow_19['label_value']._("")) );
		}
		if($myrow_20['purchase_enable']) {
			array_append($th, array($myrow_20['label_value']._("")) );
		}
		if($myrow_21['purchase_enable']) {
			array_append($th, array($myrow_21['label_value']._("")) );
		}
		
			if($myrow_22['purchase_enable']) {
			array_append($th, array($myrow_22['label_value']._("")) );
		}
		
		{

			array_append($th, array(_("Quantity"),$disc, _("Price"), _("Line Value")) );
		}
	}

	table_header($th);
    $total_grn_value = $total_qty_value = 0;
    $i = $k = 0;

	if (count($supp_trans->grn_items) > 0)
	{

    	foreach ($supp_trans->grn_items as $entered_grn)
    	{

    		alt_table_row_color($k);
			$myrow_1 = get_company_item_pref_from_position(1);
			$myrow_2 = get_company_item_pref_from_position(2);
			$myrow_3 = get_company_item_pref_from_position(3);
			$myrow_4 = get_company_item_pref_from_position(4);
			$myrow_5 = get_company_item_pref_from_position(5);
			$myrow_6 = get_company_item_pref_from_position(6);
			$myrow_7 = get_company_item_pref_from_position(7);
			$myrow_8 = get_company_item_pref_from_position(8);
			$myrow_9 = get_company_item_pref_from_position(9);
			$myrow_10 = get_company_item_pref_from_position(10);
			$myrow_11 = get_company_item_pref_from_position(11);
			$myrow_12 = get_company_item_pref_from_position(12);
			$myrow_13 = get_company_item_pref_from_position(13);
			$myrow_14 = get_company_item_pref_from_position(14);
			$myrow_15 = get_company_item_pref_from_position(15);
			$myrow_16 = get_company_item_pref_from_position(16);
			$myrow_17 = get_company_item_pref_from_position(17);
			$myrow_18 = get_company_item_pref_from_position(18);
			$myrow_19 = get_company_item_pref_from_position(19);
			$myrow_20 = get_company_item_pref_from_position(20);
			$myrow_21 = get_company_item_pref_from_position(21);


			$grn_batch = get_grn_batch_from_item($entered_grn->id);
//     		label_cell(get_trans_view_str(ST_SUPPRECEIVE, $grn_batch));


//     		if ($mode == 1)
//     		{
// //				label_cell($entered_grn->id);
//  				$row = get_grn_batch($grn_batch);
//  				$row1 = get_supp_invoice_id($row["purch_order_no"]);
// 				label_cell(get_trans_view_str(ST_PURCHORDER, $row["purch_order_no"])); // PO
// 				label_cell(get_trans_view_str(ST_SUPPINVOICE, $row1)); // INVOICE #

// 			}
			
			global $SysPrefs;
         if ($SysPrefs->show_doc_ref() == 0) {
             label_cell(get_trans_view_str(ST_SUPPRECEIVE, $grn_batch));
         }
         else{
             label_cell(get_trans_view_str(ST_SUPPRECEIVE, $grn_batch, get_reference(ST_SUPPRECEIVE,$grn_batch)));

         }
if ($mode == 1)
          {
//          label_cell($entered_grn->id);
            $row = get_grn_batch($grn_batch);
                $row1 = get_supp_invoice_id($row["purch_order_no"]);
                if ($SysPrefs->show_doc_ref() == 0) {
                    label_cell(get_trans_view_str(ST_PURCHORDER, $row["purch_order_no"])); // PO
                    label_cell(get_trans_view_str(ST_SUPPINVOICE, $row1)); // INVOICE #
                }
                else{
                    $row1 = get_supp_invoice_id($row["purch_order_no"]);
                    label_cell(get_trans_view_str(ST_PURCHORDER, $row["purch_order_no"], get_reference(ST_PURCHORDER,$row["purch_order_no"])));
                    label_cell(get_trans_view_str(ST_SUPPINVOICE, $row1, get_reference(ST_SUPPINVOICE,$row1)));

                }

         }
         
			$DimensionName = get_dimension_name_($entered_grn->dimension);
            if ($mode == 1)
			label_cell($DimensionName);

			label_cell($entered_grn->item_code);
			label_cell($entered_grn->item_description);
			$con_factor = get_company_item_pref('con_factor');
			if($pref['alt_uom'] == 1) {
				label_cell($entered_grn->units_id);
				if($con_factor['purchase_enable'] == 1)
				amount_cell($entered_grn->con_factor);
				
			}
			else {
                label_cell($entered_grn->units_id);
			}



			$batch=get_batch_by_id($entered_grn->grn_batch);
			$pref=get_company_prefs();
			if($pref['batch'] == 1){
			label_cell($batch['name']);
			label_cell(sql2date($batch['exp_date']));
			}

		/*	if($myrow_1['purchase_enable'])
			{
				if($myrow_1['type'] == 1)
					amount_cells(null, $myrow_1['name'], $entered_grn->$myrow_1['name']);
				elseif($myrow_1['type'] == 2)
					combo1_list_cells(null, $myrow_1['name'], $entered_grn->$myrow_1['name']);
				elseif($myrow_1['type'] == 3)
				{	$_POST[$myrow_1['name']]= $entered_grn->$myrow_1['name'];
					date_cells(null, $myrow_1['name']);}
				elseif($myrow_1['type'] == 4)
					text_cells(null, $myrow_1['name'], $entered_grn->$myrow_1['name'], 20, 60);
			}
			if($myrow_2['purchase_enable'])
			{
				if($myrow_2['type'] == 1)
					amount_cells(null, $myrow_2['name'], $entered_grn->$myrow_2['name']);
				elseif($myrow_2['type'] == 2)
					combo1_list_cells(null, $myrow_2['name'], $entered_grn->$myrow_2['name']);
				elseif($myrow_2['type'] == 3)
				{	$_POST[$myrow_2['name']] = $entered_grn->$myrow_2['name'];
					date_cells(null, $myrow_2['name']);}
				elseif($myrow_2['type'] == 4)
					text_cells(null, $myrow_2['name'], $entered_grn->$myrow_2['name'], 20, 60);
			}
			if($myrow_3['purchase_enable'])
			{
				if($myrow_3['type'] == 1)
					amount_cells(null, $myrow_3['name'], $entered_grn->$myrow_3['name']);
				elseif($myrow_3['type'] == 2)
					combo1_list_cells(null, $myrow_3['name'], $entered_grn->$myrow_3['name']);
				elseif($myrow_3['type'] == 3)
				{	$_POST[$myrow_3['name']] = $entered_grn->$myrow_3['name'];
					date_cells(null, $myrow_3['name']);}
				elseif($myrow_3['type'] == 4)
					text_cells(null, $myrow_3['name'], $entered_grn->$myrow_3['name'], 20, 60);
			}
			if($myrow_4['purchase_enable'])
			{
				if($myrow_4['type'] == 1)
					amount_cells(null, $myrow_4['name'], $entered_grn->$myrow_4['name']);
				elseif($myrow_4['type'] == 2)
					combo1_list_cells(null, $myrow_4['name'], $entered_grn->$myrow_4['name']);
				elseif($myrow_4['type'] == 3)
				{	$_POST[$myrow_4['name']] = $entered_grn->$myrow_4['name'];
					date_cells(null, $myrow_4['name']);}
				elseif($myrow_4['type'] == 4)
					text_cells(null, $myrow_4['name'], $entered_grn->$myrow_4['name'], 20, 60);
			}
			if($myrow_5['purchase_enable'])
			{
				if($myrow_5['type'] == 1)
					amount_cells(null, $myrow_5['name'], $entered_grn->$myrow_5['name']);
				elseif($myrow_5['type'] == 2)
					combo1_list_cells(null, $myrow_5['name'], $entered_grn->$myrow_5['name']);
				elseif($myrow_5['type'] == 3)
				{	$_POST[$myrow_5['name']] = $entered_grn->$myrow_5['name'];
					date_cells(null, $myrow_5['name']);}
				elseif($myrow_5['type'] == 4)
					text_cells(null, $myrow_5['name'], $entered_grn->$myrow_5['name'], 20, 60);
			}
			if($myrow_6['purchase_enable'])
			{
				if($myrow_6['type'] == 1)
					amount_cells(null, $myrow_6['name'], $entered_grn->$myrow_6['name']);
				elseif($myrow_6['type'] == 2)
					combo1_list_cells(null, $myrow_6['name'], $entered_grn->$myrow_6['name']);
				elseif($myrow_6['type'] == 3)
				{	$_POST[$myrow_6['name']] = $entered_grn->$myrow_6['name'];
					date_cells(null, $myrow_6['name']);}
				elseif($myrow_6['type'] == 4)
					text_cells(null, $myrow_6['name'], $entered_grn->$myrow_6['name'], 20, 60);
			}
			if($myrow_7['purchase_enable'])
			{
				if($myrow_7['type'] == 1)
					amount_cells(null, $myrow_7['name'], $entered_grn->$myrow_7['name']);
				elseif($myrow_7['type'] == 2)
					combo1_list_cells(null, $myrow_7['name'], $entered_grn->$myrow_7['name']);
				elseif($myrow_7['type'] == 3)
				{	$_POST[$myrow_7['name']] = $entered_grn->$myrow_7['name'];
					date_cells(null, $myrow_7['name']);}
				elseif($myrow_7['type'] == 4)
					text_cells(null, $myrow_7['name'], $entered_grn->$myrow_7['name'], 20, 60);
			}
			if($myrow_8['purchase_enable'])
			{
				if($myrow_8['type'] == 1)
					amount_cells(null, $myrow_8['name'], $entered_grn->$myrow_8['name']);
				elseif($myrow_8['type'] == 2)
					combo1_list_cells(null, $myrow_8['name'], $entered_grn->$myrow_8['name']);
				elseif($myrow_8['type'] == 3)
				{	$_POST[$myrow_8['name']] = $entered_grn->$myrow_8['name'];
					date_cells(null, $myrow_8['name']);}
				elseif($myrow_8['type'] == 4)
					text_cells(null, $myrow_8['name'], $entered_grn->$myrow_8['name'], 20, 60);
			}
			if($myrow_9['purchase_enable'])
			{
				if($myrow_9['type'] == 1)
					amount_cells(null, $myrow_9['name'], $entered_grn->$myrow_9['name']);
				elseif($myrow_9['type'] == 2)
					combo1_list_cells(null, $myrow_9['name'], $entered_grn->$myrow_9['name']);
				elseif($myrow_9['type'] == 3)
				{	$_POST[$myrow_9['name']] = $entered_grn->$myrow_9['name'];
					date_cells(null, $myrow_9['name']);}
				elseif($myrow_9['type'] == 4)
					text_cells(null, $myrow_9['name'], $entered_grn->$myrow_9['name'], 20, 60);
			}
			if($myrow_10['purchase_enable'])
			{
				if($myrow_10['type'] == 1)
					amount_cells(null, $myrow_10['name'], $entered_grn->$myrow_10['name']);
				elseif($myrow_10['type'] == 2)
					combo1_list_cells(null, $myrow_10['name'], $entered_grn->$myrow_10['name']);
				elseif($myrow_10['type'] == 3)
				{	$_POST[$myrow_10['name']] = $entered_grn->$myrow_10['name'];
					date_cells(null, $myrow_10['name']);}
				elseif($myrow_10['type'] == 4)
					text_cells(null, $myrow_10['name'], $entered_grn->$myrow_10['name'], 20, 60);
			}
			if($myrow_11['purchase_enable'])
			{
				if($myrow_11['type'] == 1)
					amount_cells(null, $myrow_11['name'], $entered_grn->$myrow_11['name']);
				elseif($myrow_11['type'] == 2)
					combo1_list_cells(null, $myrow_11['name'], $entered_grn->$myrow_11['name']);
				elseif($myrow_11['type'] == 3)
				{	$_POST[$myrow_11['name']] = $entered_grn->$myrow_11['name'];
					date_cells(null, $myrow_11['name']);}
				elseif($myrow_11['type'] == 4)
					text_cells(null, $myrow_11['name'], $entered_grn->$myrow_11['name'], 20, 60);
			}
			if($myrow_12['purchase_enable'])
			{
				if($myrow_12['type'] == 1)
					amount_cells(null, $myrow_12['name'], $entered_grn->$myrow_12['name']);
				elseif($myrow_12['type'] == 2)
					combo1_list_cells(null, $myrow_12['name'], $entered_grn->$myrow_12['name']);
				elseif($myrow_12['type'] == 3)
				{	$_POST[$myrow_12['name']] = $entered_grn->$myrow_12['name'];
					date_cells(null, $myrow_12['name']);}
				elseif($myrow_12['type'] == 4)
					text_cells(null, $myrow_12['name'], $entered_grn->$myrow_12['name'], 20, 60);
			}
			if($myrow_13['purchase_enable'])
			{
				if($myrow_13['type'] == 1)
					amount_cells(null, $myrow_13['name'], $entered_grn->$myrow_13['name']);
				elseif($myrow_13['type'] == 2)
					combo1_list_cells(null, $myrow_13['name'], $entered_grn->$myrow_13['name']);
				elseif($myrow_13['type'] == 3)
				{	$_POST[$myrow_13['name']] = $entered_grn->$myrow_13['name'];
					date_cells(null, $myrow_13['name']);}
				elseif($myrow_13['type'] == 4)
					text_cells(null, $myrow_13['name'], $entered_grn->$myrow_13['name'], 20, 60);
			}
			if($myrow_14['purchase_enable'])
			{
				if($myrow_14['type'] == 1)
					amount_cells(null, $myrow_14['name'], $entered_grn->$myrow_14['name']);
				elseif($myrow_14['type'] == 2)
					combo1_list_cells(null, $myrow_14['name'], $entered_grn->$myrow_14['name']);
				elseif($myrow_14['type'] == 3)
				{	$_POST[$myrow_14['name']] = $entered_grn->$myrow_14['name'];
					date_cells(null, $myrow_14['name']);}
				elseif($myrow_14['type'] == 4)
					text_cells(null, $myrow_14['name'], $entered_grn->$myrow_14['name'], 20, 60);
			}
			if($myrow_15['purchase_enable'])
			{
				if($myrow_15['type'] == 1)
					amount_cells(null, $myrow_15['name'], $entered_grn->$myrow_15['name']);
				elseif($myrow_15['type'] == 2)
					combo1_list_cells(null, $myrow_15['name'], $entered_grn->$myrow_15['name']);
				elseif($myrow_15['type'] == 3)
				{	$_POST[$myrow_15['name']] = $entered_grn->$myrow_15['name'];
					date_cells(null, $myrow_15['name']);}
				elseif($myrow_15['type'] == 4)
					text_cells(null, $myrow_15['name'], $entered_grn->$myrow_15['name'], 20, 60);
			}
			if($myrow_16['purchase_enable'])
			{
				if($myrow_16['type'] == 1)
					amount_cells(null, $myrow_16['name'], $entered_grn->$myrow_16['name']);
				elseif($myrow_16['type'] == 2)
					combo1_list_cells(null, $myrow_16['name'], $entered_grn->$myrow_16['name']);
				elseif($myrow_16['type'] == 3)
				{	$_POST[$myrow_16['name']] = $entered_grn->$myrow_16['name'];
					date_cells(null, $myrow_16['name']);}
				elseif($myrow_16['type'] == 4)
					text_cells(null, $myrow_16['name'], $entered_grn->$myrow_16['name'], 20, 60);
			}
			if($myrow_17['purchase_enable'])
			{
				if($myrow_17['type'] == 1)
					amount_cells(null, $myrow_17['name'], $entered_grn->$myrow_17['name']);
				elseif($myrow_17['type'] == 2)
					combo1_list_cells(null, $myrow_17['name'], $entered_grn->$myrow_17['name']);
				elseif($myrow_17['type'] == 3)
				{	$_POST[$myrow_17['name']] = $entered_grn->$myrow_17['name'];
					date_cells(null, $myrow_17['name']);}
				elseif($myrow_17['type'] == 4)
					text_cells(null, $myrow_17['name'], $entered_grn->$myrow_17['name'], 20, 60);
			}
			if($myrow_18['purchase_enable'])
			{
				if($myrow_18['type'] == 1)
					amount_cells(null, $myrow_18['name'], $entered_grn->$myrow_18['name']);
				elseif($myrow_18['type'] == 2)
					combo1_list_cells(null, $myrow_18['name'], $entered_grn->$myrow_18['name']);
				elseif($myrow_18['type'] == 3)
				{	$_POST[$myrow_18['name']] = $entered_grn->$myrow_18['name'];
					date_cells(null, $myrow_18['name']);}
				elseif($myrow_18['type'] == 4)
					text_cells(null, $myrow_18['name'], $entered_grn->$myrow_18['name'], 20, 60);
			}
			if($myrow_19['purchase_enable'])
			{
				if($myrow_19['type'] == 1)
					amount_cells(null, $myrow_19['name'], $entered_grn->$myrow_19['name']);
				elseif($myrow_19['type'] == 2)
					combo1_list_cells(null, $myrow_19['name'], $entered_grn->$myrow_19['name']);
				elseif($myrow_19['type'] == 3)
				{	$_POST[$myrow_19['name']] = $entered_grn->$myrow_19['name'];
					date_cells(null, $myrow_19['name']);}
				elseif($myrow_19['type'] == 4)
					text_cells(null, $myrow_19['name'], $entered_grn->$myrow_19['name'], 20, 60);
			}
			if($myrow_20['purchase_enable'])
			{
				if($myrow_20['type'] == 1)
					amount_cells(null, $myrow_20['name'], $entered_grn->$myrow_20['name']);
				elseif($myrow_20['type'] == 2)
					combo1_list_cells(null, $myrow_20['name'], $entered_grn->$myrow_20['name']);
				elseif($myrow_20['type'] == 3)
				{	$_POST[$myrow_20['name']] = $entered_grn->$myrow_20['name'];
					date_cells(null, $myrow_20['name']);}
				elseif($myrow_20['type'] == 4)
					text_cells(null, $myrow_20['name'], $entered_grn->$myrow_20['name'], 20, 60);
			}
			if($myrow_21['purchase_enable'])
			{
				if($myrow_21['type'] == 1)
					amount_cells(null, $myrow_21['name'], $entered_grn->$myrow_21['name']);
				elseif($myrow_21['type'] == 2)
					combo1_list_cells(null, $myrow_21['name'], $entered_grn->$myrow_21['name']);
				elseif($myrow_21['type'] == 3)
				{	$_POST[$myrow_21['name']] = $entered_grn->$myrow_21['name'];
					date_cells(null, $myrow_21['name']);}
				elseif($myrow_21['type'] == 4)
					text_cells(null, $myrow_21['name'], $entered_grn->$myrow_21['name'], 20, 60);
			}*/
			//text boxes labels
			if($myrow_1['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_1['name']);
			}
			if($myrow_2['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_2['name']);
			}
			if($myrow_3['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_3['name']);
			}
			if($myrow_4['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_4['name']);
			}
			if($myrow_5['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_5['name']);
			}
			if($myrow_6['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_6['name']);
			}
			if($myrow_7['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_7['name']);
			}
			if($myrow_8['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_8['name']);
			}
			if($myrow_9['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_9['name']);
			}
			if($myrow_10['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_10['name']);
			}
			if($myrow_11['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_11['name']);
			}
			if($myrow_12['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_12['name']);
			}

			///combo inputs
			if($myrow_13['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_13['name']);
			}
			if($myrow_14['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_14['name']);
			}
			if($myrow_15['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_15['name']);
			}
			if($myrow_16['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_16['name']);
			}
			if($myrow_17['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_17['name']);
			}
			if($myrow_18['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_18['name']);
			}
			if($myrow_19['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_19['name']);
			}
			if($myrow_20['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_20['name']);
			}
			if($myrow_21['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_21['name']);
			}
			
				if($myrow_22['purchase_enable'])
			{
				label_cell($entered_grn->$myrow_22['name']);
			}

            $dec = get_qty_dec($entered_grn->item_code);
            if ($mode == 1)
            {
           		label_cell(sql2date($row['delivery_date']));
 				qty_cell($entered_grn->qty_recd, false, $dec);
				qty_cell($entered_grn->prev_quantity_inv, false, $dec);

            }
            $pref = get_company_prefs();

			qty_cell(abs($entered_grn->this_quantity_inv), true, $dec);

if($pref['disc_in_amount'] == 1) {
    qty_cell($entered_grn->discount_percent , false, 0);

    amount_decimal_cell($entered_grn->chg_price - $entered_grn->discount_percent);
    amount_cell(round2(($entered_grn->chg_price   -  $entered_grn->discount_percent) * abs($entered_grn->this_quantity_inv)  , user_price_dec()), true);
}
else{
    qty_cell($entered_grn->discount_percent , false, 0);
    amount_decimal_cell($entered_grn->chg_price );
    amount_cell(round2($entered_grn->chg_price * (1 - $entered_grn->discount_percent / 100) * abs($entered_grn->this_quantity_inv), user_price_dec()), true);
        }


            
            
			if ($mode == 1)
			{
				delete_button_cell("Delete" . $entered_grn->id, _("Edit"), _('Edit document line'));
				if (($supp_trans->trans_type == ST_SUPPINVOICE) && $_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))	  
					label_cell("");
			}	
			end_row();
if($pref['disc_in_amount'] == 1) {
    $total_grn_value += round2( (( abs($entered_grn->chg_price)  - $entered_grn->discount_percent ) * $entered_grn->this_quantity_inv ),
        user_price_dec());
}
else{
    $total_grn_value += round2((1 - $entered_grn->discount_percent / 100) * $entered_grn->chg_price * abs($entered_grn->this_quantity_inv),
        user_price_dec());
}

$total_qty_value += $entered_grn->this_quantity_inv;
    		$i++;
    		if ($i > 15)
    		{
    		 	$i = 0;
    		 	table_header($th);
    		}
    	}
    }
	if ($mode == 1)
	{
		$ret = display_grn_items_for_selection($supp_trans, $k);
    	$colspan = 10;
	}
	else
		$colspan = 5;
	echo "<td>";
echo "<td>";
echo "<td>";
   label_cell(_("Total"));
   amount_cell(($total_qty_value));
   label_cell('');
   amount_cell(($total_grn_value));
   echo "</td>";
   echo "</td>";
   echo "</td>";

	if (!$ret)
	{
		start_row();
		echo "<td colspan=".($colspan + 1).">";
		if ($supp_trans->trans_type == ST_SUPPINVOICE)
			display_note(_("There are no outstanding items received from this supplier that have not been invoiced by them."), 0, 0);
		else
		{
			display_note(_("There are no received items for the selected supplier that have been invoiced."));
			display_note(_("Credits can only be applied to invoiced items."), 0, 0);
		}
		echo "</td>";
		end_row();
	}	
    end_table(1);
    div_end();

	return $total_grn_value;
}

//--------------------------------------------------------------------------------------------------
function get_duedate_from_terms(&$trans)
{
	$date = get_class($trans) == 'purch_order' ? $trans->orig_order_date : $trans->tran_date;

	if (!is_date($date))
	{
		$date = Today();
	}
	if ($trans->terms['day_in_following_month'])
	{ /*Its a day in the following month when due */
		$trans->due_date =
			add_days(end_month($date), $trans->terms["day_in_following_month"]);
	}
	else
	{ /*Use the Days Before Due to add to the invoice date */
		$trans->due_date = add_days($date, $trans->terms["days_before_due"]);
	}
}

//--------------------------------------------------------------------------------------------------

