<?php

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");

//--------------------------------------------------------------------------------

function add_to_order(&$order, $new_item, $new_item_qty, $standard_cost,$discount, $src_no, $src_id,$units_id,$con_factor,
					  $text1, $text2, $text3, $text4, $text5, $text6,
					  $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
					  $date1, $date2, $date3,
					  $combo1, $combo2, $combo3,$combo4, $combo5, $comb6, $FromStockLocation, $ToStockLocation,$batch)
{

	if ($order->find_cart_item($new_item,null,null,$FromStockLocation,$ToStockLocation))
		display_error(_("For Part :") . $new_item . " " . "This item is already on this document. You can change the quantity on the existing line if necessary.");
	else
		$order->add_to_cart(count($order->line_items), $new_item, $new_item_qty, $standard_cost, 0, $discount, $src_no, $src_id, $units_id,
			$con_factor, $text1, $text2, $text3, $text4, $text5, $text6,
			$amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
			$date1, $date2, $date3,
			$combo1, $combo2, $combo3, $combo4, $combo5, $comb6, $batch, 0, $FromStockLocation, $ToStockLocation);


	
}

//--------------------------------------------------------------------------------

function display_order_header(&$order)
{
	global $Refs;

	start_outer_table(TABLESTYLE, "width='70%'");

	table_section(1);
	$pref = get_company_pref();
	if($pref['item_location'] != 1) {
		locations_list_row(_("From Location:"), 'FromStockLocation', null, false, true, $order->fixed_asset);
		locations_to_list_row(_("To Location:"), 'ToStockLocation', null, false, false, $order->fixed_asset);
		table_section(2, "50%");
		date_row(_("Date:"), 'AdjDate', '', true, 0, 0, 0, "", true);

		ref_row(_("Reference:"), 'ref', '',null,
		false, ST_LOCTRANSFER, array('date'=> @$_POST['AdjDate']));

	}
	else{
		date_row(_("Date:"), 'AdjDate', '', true, 0, 0, 0, "", true);

        ref_row(_("Reference:"), 'ref', '',null,
            false, ST_LOCTRANSFER, array('date'=> @$_POST['AdjDate']));
		table_section(2, "50%");
	}


	end_outer_table(1); // outer table
}

//---------------------------------------------------------------------------------

function display_transfer_items($title, &$order)
{
	global $path_to_root;

	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='80%'");
	$myrow_1 = get_company_inventory_pref_from_position(1);
	$myrow_2 = get_company_inventory_pref_from_position(2);
	$myrow_3 = get_company_inventory_pref_from_position(3);
	$myrow_4 = get_company_inventory_pref_from_position(4);
	$myrow_5 = get_company_inventory_pref_from_position(5);
	$myrow_6 = get_company_inventory_pref_from_position(6);
	$myrow_7 = get_company_inventory_pref_from_position(7);
	$myrow_8 = get_company_inventory_pref_from_position(8);
	$myrow_9 = get_company_inventory_pref_from_position(9);
	$myrow_10 = get_company_inventory_pref_from_position(10);
	$myrow_11 = get_company_inventory_pref_from_position(11);
	$myrow_12 = get_company_inventory_pref_from_position(12);
	$myrow_13 = get_company_inventory_pref_from_position(13);
	$myrow_14 = get_company_inventory_pref_from_position(14);
	$myrow_15 = get_company_inventory_pref_from_position(15);
	$myrow_16 = get_company_inventory_pref_from_position(16);
	$myrow_17 = get_company_inventory_pref_from_position(17);
	$myrow_18 = get_company_inventory_pref_from_position(18);
	$myrow_19 = get_company_inventory_pref_from_position(19);
	$myrow_20 = get_company_inventory_pref_from_position(20);
	$myrow_21 = get_company_inventory_pref_from_position(21);
	$myrow_22 = get_company_item_pref_from_name('con_factor');




	$pref=get_company_prefs();
	if($pref['item_location'] == 1) {
		$th = array(_("From Location"), _("To Location"),_("Item Code"), _("Item Description"),_("QOH"));
	}
	else{
		$th = array(_("Item Code"), _("Item Description"),_("QOH"));
	}
	//Text Boxes Headings

	if($myrow_1['inventory_enable']) {
		array_append($th, array($myrow_1['label_value']._("")) );
	}
	if($myrow_2['inventory_enable']) {
		array_append($th, array($myrow_2['label_value']._("")) );
	}
	if($myrow_3['inventory_enable']) {
		array_append($th, array($myrow_3['label_value']._("")) );
	}
	if($myrow_4['inventory_enable']) {
		array_append($th, array($myrow_4['label_value']._("")) );
	}
	if($myrow_5['inventory_enable']) {
		array_append($th, array($myrow_5['label_value']._("")) );
	}
	if($myrow_6['inventory_enable']) {
		array_append($th, array($myrow_6['label_value']._("")) );
	}
	if($myrow_7['inventory_enable']) {
		array_append($th, array($myrow_7['label_value']._("")) );
	}
	if($myrow_8['inventory_enable']) {
		array_append($th, array($myrow_8['label_value']._("")) );
	}
	if($myrow_9['inventory_enable']) {
		array_append($th, array($myrow_9['label_value']._("")) );
	}
	if($myrow_10['inventory_enable']) {
		array_append($th, array($myrow_10['label_value']._("")) );
	}
	if($myrow_11['inventory_enable']) {
		array_append($th, array($myrow_11['label_value']._("")) );
	}
	if($myrow_12['inventory_enable']) {
		array_append($th, array($myrow_12['label_value']._("")) );
	}
	if($myrow_13['inventory_enable']) {
		array_append($th, array($myrow_13['label_value']._("")) );
	}
	if($myrow_14['inventory_enable']) {
		array_append($th, array($myrow_14['label_value']._("")) );
	}
	if($myrow_15['inventory_enable']) {
		array_append($th, array($myrow_15['label_value']._("")) );
	}
	if($myrow_16['inventory_enable']) {
		array_append($th, array($myrow_16['label_value']._("")) );
	}
	if($myrow_17['inventory_enable']) {
		array_append($th, array($myrow_17['label_value']._("")) );
	}
	if($myrow_18['inventory_enable']) {
		array_append($th, array($myrow_18['label_value']._("")) );
	}
	if($myrow_19['inventory_enable']) {
		array_append($th, array($myrow_19['label_value']._("")) );
	}
	if($myrow_20['inventory_enable']) {
		array_append($th, array($myrow_20['label_value']._("")) );
	}
	if($myrow_21['inventory_enable']) {
		array_append($th, array($myrow_21['label_value']._("")) );
	}
//    if($myrow_22['inventory_enable']) {
//        array_append($th, array($myrow_22['label_value']._("")) );
//    }
	{
		$pref=get_company_prefs();
		
		if($pref['alt_uom'] == 1 && $myrow_22['inventory_enable']==1) {

			if ($pref['batch'] == 1) {
				if ($order->fixed_asset) {

					array_append($th, array(_("Quantity"),_("Con factor"), _("Batch"),
						_("Unit"),  _("Initial Value"), ""));
				} else {
					array_append($th, array(_("Quantity"),_("Con factor"), _("Batch"),
						_("Unit"),  _("Total"), ""));
	}
			} else {
				if ($order->fixed_asset) {

					array_append($th, array(_("Quantity"),_("Con factor"),
						_("Unit"), _("Initial Value"), ""));
				} else {
					array_append($th, array(_("Quantity"),_("Con factor"),
						_("Unit"), _("Total"), ""));
				}

			}
		}
		else{
			if ($pref['batch'] == 1) {
				if ($order->fixed_asset) {

					array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
						_("Unit"),  _("Initial Value"), ""));
				} else {
					array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
						_("Unit"),  _("Total"), ""));
				}
			} else {
				if ($order->fixed_asset) {

					array_append($th, array(_("Quantity"),
						_("Unit"),  _("Initial Value"), ""));
				} else {
					array_append($th, array(_("Quantity"),
						_("Unit"),  _("Total"), ""));
				}

			}
		}
	}


	if ( count($order->line_items)) $th[] = '';

	table_header($th);
	$total = 0;
	$k = 0;  //row colour counter

	$id = find_submit('Edit');
	
	$TotalQty = $totalqty = 0;

    if($pref['item_location'] != 1 ){
        $low_stock = $order->check_qoh($_POST['AdjDate'], $_POST['FromStockLocation']);
    }

	foreach ($order->line_items as $line_no=>$stock_item)
	{

		if ($id != $line_no)
		{

            $pref=get_company_prefs();
            if($pref['item_location'] == 1 ){
                $low_stock = $order->check_qoh($_POST['AdjDate'],$stock_item->FromStockLocation);
                $qoh = get_qoh_on_date($stock_item->stock_id,$stock_item->FromStockLocation,($_POST['AdjDate']));
            }
            else{
                $qoh = get_qoh_on_date($stock_item->stock_id,$_POST['FromStockLocation'],($_POST['AdjDate']));
            }
            $_POST['qoh_label'] = db_escape($qoh);


			if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else 
				alt_table_row_color($k);
			$pref = get_company_pref();
			if($pref['item_location'] == 1) {
				label_cell(get_location_name($stock_item->FromStockLocation));
				label_cell(get_location_name($stock_item->ToStockLocation));
			}
			if ($order->fixed_asset)
				label_cell($stock_item->stock_id);
			else
				view_stock_status_cell($stock_item->stock_id);
    		label_cell($stock_item->item_description);
			//text boxes labels
            label_cells(null,number_format2($qoh,2));

//text boxes labels
			if($myrow_1['inventory_enable'])
			{
				label_cell($stock_item->$myrow_1['name']);
			}
			if($myrow_2['inventory_enable'])
			{
				label_cell($stock_item->$myrow_2['name']);
			}
			if($myrow_3['inventory_enable'])
			{
				label_cell($stock_item->$myrow_3['name']);
			}
			if($myrow_4['inventory_enable'])
			{
				label_cell($stock_item->$myrow_4['name']);
			}
			if($myrow_5['inventory_enable'])
			{
				label_cell($stock_item->$myrow_5['name']);
			}
			if($myrow_6['inventory_enable'])
			{
				label_cell($stock_item->$myrow_6['name']);
			}
			if($myrow_7['inventory_enable'])
			{
				label_cell($stock_item->$myrow_7['name']);
			}
			if($myrow_8['inventory_enable'])
			{
				label_cell($stock_item->$myrow_8['name']);
			}
			if($myrow_9['inventory_enable'])
			{
				label_cell($stock_item->$myrow_9['name']);
			}
			if($myrow_10['inventory_enable'])
			{
				label_cell($stock_item->$myrow_10['name']);
			}
			if($myrow_11['inventory_enable'])
			{
				label_cell($stock_item->$myrow_11['name']);
			}
			if($myrow_12['inventory_enable'])
			{
				label_cell($stock_item->$myrow_12['name']);
			}

			///combo inputs
			if($myrow_13['inventory_enable'])
			{
				label_cell($stock_item->$myrow_13['name']);
			}
			if($myrow_14['inventory_enable'])
			{
				label_cell($stock_item->$myrow_14['name']);
			}
			if($myrow_15['inventory_enable'])
			{
				label_cell($stock_item->$myrow_15['name']);
			}
			if($myrow_16['inventory_enable'])
			{
				label_cell($stock_item->$myrow_16['name']);
			}
			if($myrow_17['inventory_enable'])
			{
				label_cell($stock_item->$myrow_17['name']);
			}
			if($myrow_18['inventory_enable'])
			{
				label_cell($stock_item->$myrow_18['name']);
			}
			if($myrow_19['inventory_enable'])
			{
				label_cell($stock_item->$myrow_19['name']);
			}
			if($myrow_20['inventory_enable'])
			{
				label_cell($stock_item->$myrow_20['name']);
			}
			if($myrow_21['inventory_enable'])
			{
				label_cell($stock_item->$myrow_21['name']);
			}
			qty_cell($stock_item->quantity,false,get_qty_dec($stock_item->stock_id));
			
			$TotalQty = $stock_item->quantity;
			
			if($pref['alt_uom'] == 1 && $myrow_22['inventory_enable']==1) {
				label_cell($stock_item->con_factor);
			}

			if($pref['batch'] == 1) {

					$batch=get_batch_by_id($stock_item->batch);
					label_cell($batch['name']."(".sql2date($batch['exp_date']).")");


		
			}
			if($pref['alt_uom'] == 1 )
				label_cell($stock_item->units_id);
			else
    		label_cell($stock_item->units);

	     	if (!$order->fixed_asset)
				edit_button_cell("Edit$line_no", _("Edit"),	_('Edit document line'));
			delete_button_cell("Delete$line_no", _("Delete"), _('Remove line from document'));
    		end_row();
		}
		else
		{
			transfer_edit_item_controls($order, $line_no);
		}
		$totalqty += $TotalQty;
	}

	if ($id == -1)
		transfer_edit_item_controls($order);

$colspan = 2;


start_row();

label_row(_("Sub-total"), $totalqty, "colspan=$colspan align=right","align=right", 2);
hidden('sub_total',$totalqty);

end_row();

    end_table();
	if ($low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of transfer."), 0, 1, "class='stockmankofg'");
	div_end();
}

//---------------------------------------------------------------------------------

function transfer_edit_item_controls(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();
	$pref=get_company_prefs();
	$myrow_1 = get_company_inventory_pref_from_position(1);
	$myrow_2 = get_company_inventory_pref_from_position(2);
	$myrow_3 = get_company_inventory_pref_from_position(3);
	$myrow_4 = get_company_inventory_pref_from_position(4);
	$myrow_5 = get_company_inventory_pref_from_position(5);
	$myrow_6 = get_company_inventory_pref_from_position(6);
	$myrow_7 = get_company_inventory_pref_from_position(7);
	$myrow_8 = get_company_inventory_pref_from_position(8);
	$myrow_9 = get_company_inventory_pref_from_position(9);
	$myrow_10 = get_company_inventory_pref_from_position(10);
	$myrow_11 = get_company_inventory_pref_from_position(11);
	$myrow_12 = get_company_inventory_pref_from_position(12);
	$myrow_13 = get_company_inventory_pref_from_position(13);
	$myrow_14 = get_company_inventory_pref_from_position(14);
	$myrow_15 = get_company_inventory_pref_from_position(15);
	$myrow_16 = get_company_inventory_pref_from_position(16);
	$myrow_17 = get_company_inventory_pref_from_position(17);
	$myrow_18 = get_company_inventory_pref_from_position(18);
	$myrow_19 = get_company_inventory_pref_from_position(19);
	$myrow_20 = get_company_inventory_pref_from_position(20);
	$myrow_21 = get_company_inventory_pref_from_position(21);
	$myrow_22 = get_company_item_pref_from_name('con_factor');

	$id = find_submit('Edit');


    if (list_updated('item_location') || list_updated('FromStockLocation') || list_updated('stock_id') )
        $Ajax->activate('qoh');

    if($pref['item_location'] == 1 ) {
        $qoh = get_qoh_on_date($_POST['stock_id'], $_POST['FromStockLocation'], ($_POST['AdjDate']));
    }
    else {
        $qoh = get_qoh_on_date($_POST['stock_id'], $_POST['FromStockLocation'], ($_POST['AdjDate']));
    }
    $_POST['qoh'] = number_format2($qoh,2);
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, $order->line_items[$id]->stock_id, $dec);
		$_POST['units'] = $order->line_items[$id]->units;
		$_POST['units_id'] = $order->line_items[$id]->units_id;
		$_POST['text1'] = $order->line_items[$id]->text1;
		$_POST['text2'] = $order->line_items[$id]->text2;
		$_POST['text3'] = $order->line_items[$id]->text3;
		$_POST['text4'] = $order->line_items[$id]->text4;
		$_POST['text5'] = $order->line_items[$id]->text5;
		$_POST['text6'] = $order->line_items[$id]->text6;
		$_POST['amount1'] = price_format($order->line_items[$id]->amount1);
		$_POST['amount2'] = price_format($order->line_items[$id]->amount2);
		$_POST['amount3'] = price_format($order->line_items[$id]->amount3);
		$_POST['amount4'] = price_format($order->line_items[$id]->amount4);
		$_POST['amount5'] = price_format($order->line_items[$id]->amount5);
		$_POST['amount6'] = price_format($order->line_items[$id]->amount6);
		$_POST['date1'] = ($order->line_items[$id]->date1);
		$_POST['date2'] = ($order->line_items[$id]->date2);
		$_POST['date3'] = ($order->line_items[$id]->date3);
		$_POST['combo1'] = $order->line_items[$id]->combo1;
		$_POST['combo2'] = $order->line_items[$id]->combo2;
		$_POST['combo3'] = $order->line_items[$id]->combo3;
		$_POST['combo4'] = $order->line_items[$id]->combo4;
		$_POST['combo5'] = $order->line_items[$id]->combo5;
		$_POST['combo6'] = $order->line_items[$id]->combo6;

		hidden('stock_id', $_POST['stock_id']);
		if($pref['item_location'] == 1) {
			locations_list_cells(null, 'FromStockLocation', null, false, true, $order->fixed_asset);
			locations_list_cells(null, 'ToStockLocation', null, false, false, $order->fixed_asset);
		}
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description);
        ref_cells_disabled(null, 'qoh', null, null, null, true);

        $Ajax->activate('items_table');
	}
	else
	{
		if($pref['item_location'] == 1) {
			locations_list_cells(null, 'FromStockLocation', null, false, true, $order->fixed_asset);
			locations_list_cells(null, 'ToStockLocation', null, false, false, $order->fixed_asset);
		}
		if ($order->fixed_asset)
			stock_disposable_fa_list_cells(null, 'stock_id', null, false, true, $order->line_items);
		else
			stock_costable_items_list_cells(null, 'stock_id', null, false, true);

        ref_cells_disabled(null, 'qoh', null, null, null, true);

        if (list_updated('stock_id')) {
			$Ajax->activate('units_id');
			    $Ajax->activate('units');
			    $Ajax->activate('qty');
			$Ajax->activate('qty');
			$Ajax->activate('std_cost');
			$Ajax->activate('batch');
			$Ajax->activate('con_factor');
			$Ajax->activate('std_cost_view');
			$Ajax->activate('initial_price');
			$Ajax->activate('amount1');
			$Ajax->activate('amount2');
			$Ajax->activate('amount3');
			$Ajax->activate('amount4');
			$Ajax->activate('amount5');
			$Ajax->activate('amount6');
            $Ajax->activate('qoh');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);
		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount3'] = price_format($item_info["amount3"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);
		$_POST['amount5'] = price_format($item_info["amount5"]);
		$_POST['amount6'] = price_format($item_info["amount6"]);

		$dec = $item_info['decimals'];
   		$_POST['qty'] = number_format2(1, $dec);
		$_POST['units_id'] = $item_info["units"];
		$_POST['con_factor'] = $item_info["con_factor"];
	}
	if($myrow_1['inventory_enable'])
	{
		if($myrow_1['type'] == 1)
			small_amount_cells(null, $myrow_1['name']);
		elseif($myrow_1['type'] == 2)
			combo1_list_cells(null, $myrow_1['name'],$item_info['$myrow_1["name"]']);
		elseif($myrow_1['type'] == 3)
			date_cells(null, $myrow_1['name'],null);
		elseif($myrow_1['type'] == 4)
			text_cells(null, $myrow_1['name'], $item_info['$myrow_1["name"]'], $myrow_1["s_width"], 40);
	}
	else{
		hidden($myrow_1['name'],$_POST['amount1']);
	}
	if($myrow_2['inventory_enable'])
	{
		if($myrow_2['type'] == 1)
			small_amount_cells(null, $myrow_2['name']);
		elseif($myrow_1['type'] == 2)
			combo1_list_cells(null, $myrow_2['name'],$item_info['$myrow_2["name"]']);
		elseif($myrow_2['type'] == 3)
			date_cells(null, $myrow_2['name'],null);
		elseif($myrow_2['type'] == 4)
			text_cells(null, $myrow_2['name'], $item_info['$myrow_2["name"]'], $myrow_2["s_width"], 40);
	}
	else{
		hidden($myrow_2['name'],$_POST['amount2']);
	}
	if($myrow_3['inventory_enable'])
	{
		if($myrow_3['type'] == 1)
			small_amount_cells(null, $myrow_3['name']);
		elseif($myrow_3['type'] == 2)
			combo1_list_cells(null, $myrow_3['name'],$item_info['$myrow_3["name"]']);
		elseif($myrow_3['type'] == 3)
			date_cells(null, $myrow_3['name'],null);
		elseif($myrow_3['type'] == 4)
			text_cells(null, $myrow_3['name'], $item_info['$myrow_3["name"]'], $myrow_3["s_width"], 40);
	}
	else{
		hidden($myrow_3['name'],$_POST['amount3']);
	}
	if($myrow_4['inventory_enable'])
	{
		if($myrow_4['type'] == 1)
			small_amount_cells(null, $myrow_4['name']);
		elseif($myrow_4['type'] == 2)
			combo1_list_cells(null, $myrow_4['name'],$item_info['$myrow_4["name"]']);
		elseif($myrow_4['type'] == 3)
			date_cells(null, $myrow_4['name'],null);
		elseif($myrow_4['type'] == 4)
			text_cells(null, $myrow_4['name'], $item_info['$myrow_4["name"]'], $myrow_4["s_width"], 40);
	}
	else{
		hidden($myrow_4['name'],$_POST['amount4']);
	}
	if($myrow_5['inventory_enable'])
	{
		if($myrow_5['type'] == 1)
			small_amount_cells(null, $myrow_5['name']);
		elseif($myrow_5['type'] == 2)
			combo1_list_cells(null, $myrow_5['name'],$item_info['$myrow_5["name"]']);
		elseif($myrow_5['type'] == 3)
			date_cells(null, $myrow_5['name'],null);
		elseif($myrow_5['type'] == 4)
			text_cells(null, $myrow_5['name'], $item_info['$myrow_5["name"]'], $myrow_5["s_width"], 40);
	}
	else{
		hidden($myrow_5['name'],$_POST['amount5']);
	}
	if($myrow_6['inventory_enable'])
	{
		if($myrow_6['type'] == 1)
			small_amount_cells(null, $myrow_6['name']);
		elseif($myrow_6['type'] == 2)
			combo1_list_cells(null, $myrow_6['name'],$item_info['$myrow_6["name"]']);
		elseif($myrow_6['type'] == 3)
			date_cells(null, $myrow_6['name'],null);
		elseif($myrow_6['type'] == 4)
			text_cells(null, $myrow_6['name'], $item_info['$myrow_6["name"]'], $myrow_6["s_width"], 40);
	}
	else{
		hidden($myrow_6['name'],$_POST['amount6']);
	}
	if($myrow_7['inventory_enable'])
	{
		if($myrow_7['type'] == 1)
			small_amount_cells(null, $myrow_7['name']);
		elseif($myrow_7['type'] == 2)
			combo1_list_cells(null, $myrow_7['name'],$item_info['$myrow_7["name"]']);
		elseif($myrow_7['type'] == 3)
			date_cells(null, $myrow_7['name'],null);
		elseif($myrow_7['type'] == 4)
			text_cells(null, $myrow_7['name'], $item_info['$myrow_7["name"]'], $myrow_7["s_width"], 40);
	}
	if($myrow_8['inventory_enable'])
	{
		if($myrow_8['type'] == 1)
			small_amount_cells(null, $myrow_8['name']);
		elseif($myrow_8['type'] == 2)
			combo1_list_cells(null, $myrow_8['name'],$item_info['$myrow_8["name"]']);
		elseif($myrow_8['type'] == 3)
			date_cells(null, $myrow_8['name'],null);
		elseif($myrow_8['type'] == 4)
			text_cells(null, $myrow_8['name'], $item_info['$myrow_8["name"]'], $myrow_8["s_width"], 40);
	}
	if($myrow_9['inventory_enable'])
	{
		if($myrow_9['type'] == 1)
			small_amount_cells(null, $myrow_9['name']);
		elseif($myrow_9['type'] == 2)
			combo1_list_cells(null, $myrow_9['name'],$item_info['$myrow_9["name"]']);
		elseif($myrow_9['type'] == 3)
			date_cells(null, $myrow_9['name'],null);
		elseif($myrow_9['type'] == 4)
			text_cells(null, $myrow_9['name'], $item_info['$myrow_9["name"]'], $myrow_9["s_width"], 40);
	}
	if($myrow_10['inventory_enable'])
	{
		if($myrow_10['type'] == 1)
			small_amount_cells(null, $myrow_10['name']);
		elseif($myrow_10['type'] == 2)
			combo1_list_cells(null, $myrow_10['name'],$item_info['$myrow_10["name"]']);
		elseif($myrow_10['type'] == 3)
			date_cells(null, $myrow_10['name'],null);
		elseif($myrow_10['type'] == 4)
			text_cells(null, $myrow_10['name'], $item_info['$myrow_10["name"]'], $myrow_10["s_width"], 40);
	}
	if($myrow_11['inventory_enable'])
	{
		if($myrow_11['type'] == 1)
			small_amount_cells(null, $myrow_11['name']);
		elseif($myrow_11['type'] == 2)
			combo1_list_cells(null, $myrow_11['name'],$item_info['$myrow_11["name"]']);
		elseif($myrow_11['type'] == 3)
			date_cells(null, $myrow_11['name'],null);
		elseif($myrow_11['type'] == 4)
			text_cells(null, $myrow_11['name'], $item_info['$myrow_11["name"]'], $myrow_11["s_width"], 40);
	}
	if($myrow_12['inventory_enable'])
	{
		if($myrow_12['type'] == 1)
			small_amount_cells(null, $myrow_12['name']);
		elseif($myrow_12['type'] == 2)
			combo1_list_cells(null, $myrow_12['name'],$item_info['$myrow_12["name"]']);
		elseif($myrow_12['type'] == 3)
			date_cells(null, $myrow_12['name'],null);
		elseif($myrow_12['type'] == 4)
			text_cells(null, $myrow_12['name'], $item_info['$myrow_12["name"]'], $myrow_12["s_width"], 40);
	}
	if($myrow_13['inventory_enable'])
	{
		if($myrow_13['type'] == 1)
			small_amount_cells(null, $myrow_13['name']);
		elseif($myrow_13['type'] == 2)
			combo1_list_cells(null, $myrow_13['name'],$item_info['$myrow_13["name"]']);
		elseif($myrow_13['type'] == 3)
			date_cells(null, $myrow_13['name'],null);
		elseif($myrow_13['type'] == 4)
			text_cells(null, $myrow_13['name'], $item_info['$myrow_13["name"]'], $myrow_13["s_width"], 40);
	}
	if($myrow_14['inventory_enable'])
	{
		if($myrow_14['type'] == 1)
			small_amount_cells(null, $myrow_14['name']);
		elseif($myrow_14['type'] == 2)
			combo1_list_cells(null, $myrow_14['name'],$item_info['$myrow_14["name"]']);
		elseif($myrow_14['type'] == 3)
			date_cells(null, $myrow_14['name'],null);
		elseif($myrow_14['type'] == 4)
			text_cells(null, $myrow_14['name'], $item_info['$myrow_14["name"]'], $myrow_14["s_width"], 40);
	}
	if($myrow_15['inventory_enable'])
	{
		if($myrow_15['type'] == 1)
			small_amount_cells(null, $myrow_15['name']);
		elseif($myrow_15['type'] == 2)
			combo1_list_cells(null, $myrow_15['name'],$item_info['$myrow_15["name"]']);
		elseif($myrow_15['type'] == 3)
			date_cells(null, $myrow_15['name'],null);
		elseif($myrow_15['type'] == 4)
			text_cells(null, $myrow_15['name'], $item_info['$myrow_15["name"]'], $myrow_15["s_width"], 40);
	}
	if($myrow_16['inventory_enable'])
	{
		if($myrow_16['type'] == 1)
			small_amount_cells(null, $myrow_16['name']);
		elseif($myrow_16['type'] == 2)
			combo1_list_cells(null, $myrow_16['name'],$item_info['$myrow_16["name"]']);
		elseif($myrow_16['type'] == 3)
			date_cells(null, $myrow_16['name'],null);
		elseif($myrow_16['type'] == 4)
			text_cells(null, $myrow_16['name'], $item_info['$myrow_16["name"]'], $myrow_16["s_width"], 40);
	}
	if($myrow_17['inventory_enable'])
	{
		if($myrow_17['type'] == 1)
			small_amount_cells(null, $myrow_17['name']);
		elseif($myrow_17['type'] == 2)
			combo1_list_cells(null, $myrow_17['name'],$item_info['$myrow_17["name"]']);
		elseif($myrow_17['type'] == 3)
			date_cells(null, $myrow_17['name'],null);
		elseif($myrow_17['type'] == 4)
			text_cells(null, $myrow_17['name'], $item_info['$myrow_17["name"]'], $myrow_17["s_width"], 40);
	}
	if($myrow_18['inventory_enable'])
	{
		if($myrow_18['type'] == 1)
		{
			small_amount_cells(null, $myrow_18['name']);
		}
		elseif($myrow_18['type'] == 2)
		{	combo1_list_cells(null, $myrow_18['name'],$item_info['$myrow_18["name"]']);
		}
		elseif($myrow_18['type'] == 3)
			date_cells(null, $myrow_18['name'],null);
		elseif($myrow_18['type'] == 4)
		{
			text_cells(null, $myrow_18['name'], $item_info['$myrow_18["name"]'], $myrow_18["s_width"], 20);
		}
	}
	if($myrow_19['inventory_enable'])
	{
		if($myrow_19['type'] == 1)
		{
			small_amount_cells(null, $myrow_19['name']);
		}
		elseif($myrow_19['type'] == 2)
		{	combo1_list_cells(null, $myrow_19['name'],$item_info['$myrow_19["name"]']);
		}
		elseif($myrow_19['type'] == 3)
			date_cells(null, $myrow_19['name'],null);
		elseif($myrow_19['type'] == 4)
		{	text_cells(null, $myrow_19['name'], $item_info['$myrow_19["name"]'], $myrow_19["s_width"], 20);
		}
	}
	if($myrow_20['inventory_enable'])
	{
		if($myrow_20['type'] == 1)
			small_amount_cells(null, $myrow_20['name']);
		elseif($myrow_20['type'] == 2)
			combo1_list_cells(null, $myrow_20['name'],$item_info['$myrow_20["name"]']);
		elseif($myrow_20['type'] == 3)
			date_cells(null, $myrow_20['name'],null);
		elseif($myrow_20['type'] == 4)
			text_cells(null, $myrow_20['name'], $item_info['$myrow_20["name"]'], $myrow_20["s_width"], 40);
	}
	if($myrow_21['inventory_enable'])
	{
		if($myrow_21['type'] == 1)
			small_amount_cells(null, $myrow_21['name']);
		elseif($myrow_21['type'] == 2)
			combo1_list_cells(null, $myrow_21['name'],$item_info['$myrow_21["name"]']);
		elseif($myrow_21['type'] == 3)
			date_cells(null, $myrow_21['name'],null);
		elseif($myrow_21['type'] == 4)
			text_cells(null, $myrow_21['name'], $item_info['$myrow_21["name"]'], $myrow_21["s_width"], 40);
	}

	if ($order->fixed_asset) {
		hidden('qty', 1);
		qty_cell(1, false, 0);
	} else

		small_qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);

	if($pref['alt_uom'] == 1 && $myrow_22['inventory_enable']==1) {

		qty_cells("",'con_factor',$_POST['con_factor']);
	}
	else{
	hidden('con_factor',$_POST['con_factor']);
	}
	if($pref['batch'] == 1) {
		batch_list_cells(_(""), $_POST['stock_id'], 'batch', null, false, false, true, true, $_POST['StockLocation']);
	}
	if($pref['alt_uom'] == 1 ) {
		stock_units_list_cell("",'units_id', $_POST['units_id'],1,$_POST['stock_id']);
	}
	else{
		label_cell($_POST['units_id'], '', 'units_id');
	}

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	}
	else
	{
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}

	end_row();
}


//---------------------------------------------------------------------------------

function transfer_options_controls()
{
	  echo "<br>";
	  start_table();

	  textarea_row(_("Memo"), 'memo_', null, 50, 3);

	  end_table(1);
}


//---------------------------------------------------------------------------------

