<?php

include_once($path_to_root . "/includes/ui.inc");

// ------------------------------------------------------------------------------

function display_credit_header(&$order)
{
	global $Ajax, $Refs;

	start_outer_table(TABLESTYLE, "width='80%'");
	table_section(1);

	$customer_error = "";
	$change_prices = 0;

    if (!isset($_POST['customer_id']) && (get_global_customer() != ALL_TEXT))
    	$_POST['customer_id'] = get_global_customer();

	customer_list_row(_("Customer:"), 'customer_id', null, false, true, false, true);

	if ($order->customer_id != $_POST['customer_id'] /*|| $order->sales_type != $_POST['sales_type_id']*/)
	{
		// customer has changed
		$Ajax->activate('branch_id');
	}

	customer_branches_list_row(_("Branch:"), $_POST['customer_id'], 
	  	'branch_id', null, false, true, true, true);

	if (($order->customer_id != $_POST['customer_id']) ||
		($order->Branch != $_POST['branch_id']))
	{

		$old_order = (PHP_VERSION<5) ? $order : clone $order;
		$customer_error = get_customer_details_to_order($order, $_POST['customer_id'], $_POST['branch_id']);

		$_POST['Location'] = $order->Location;
		$_POST['deliver_to'] = $order->deliver_to;
		$_POST['delivery_address'] = $order->delivery_address;
		$_POST['phone'] = $order->phone;
		$Ajax->activate('Location');
		$Ajax->activate('deliver_to');
		$Ajax->activate('phone');
		$Ajax->activate('delivery_address');
		// change prices if necessary
		// what about discount in template case?
		if ($old_order->customer_currency != $order->customer_currency) {
			$change_prices = 1;
		}
		if ($old_order->sales_type != $order->sales_type) {
			$_POST['sales_type_id'] = $order->sales_type;
			$Ajax->activate('sales_type_id');
			$change_prices = 1;
		}
		if ($old_order->dimension_id != $order->dimension_id) {
			$_POST['dimension_id'] = $order->dimension_id;
			$Ajax->activate('dimension_id');
		}
		if ($old_order->dimension2_id != $order->dimension2_id) {
			$_POST['dimension2_id'] = $order->dimension2_id;
			$Ajax->activate('dimension2_id');
		}
		unset($old_order);
	}
	set_global_customer($_POST['customer_id']);

	if (!isset($_POST['ref']))
        $_POST['ref'] = $Refs->get_next(ST_CUSTCREDIT, null, array('customer' => get_post('customer_id'),
            'branch' => get_post('branch_id'), 'date' => get_post('OrderDate')));
	if ($order->trans_no==0)
	    ref_row(_("Reference").':', 'ref', '',null, false, ST_CUSTCREDIT, array('date'=> @$_POST['OrderDate']));
	else
	    label_row(_("Reference").':', $order->reference);


	if (!is_company_currency($order->customer_currency))
	{
		table_section(2);
		
		label_row(_("Customer Currency:"), $order->customer_currency);
		exchange_rate_display(get_company_currency(), $order->customer_currency,
			$_POST['OrderDate']);
	}
	table_section(3);

    if (!isset($_POST['sales_type_id']))
    	$_POST['sales_type_id'] = $order->sales_type;
    sales_types_list_row(_("Sales Type"), 'sales_type_id', $_POST['sales_type_id'], true);
	
	if ($order->sales_type != $_POST['sales_type_id']) {
		$myrow = get_sales_type($_POST['sales_type_id']);
		$order->set_sales_type($myrow['id'], $myrow['sales_type'],
		$myrow['tax_included'], $myrow['factor']);
		$Ajax->activate('sales_type_id');
		$change_prices = 1;
	}

    shippers_list_row(_("Shipping Company:"), 'ShipperID', $order->ship_via);

	label_row(_("Customer Discount:"), ($order->default_discount * 100) . "%");

	table_section(4);

	if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
		$_POST['OrderDate'] = $order->document_date;

	date_row(_("Date:"), 'OrderDate', '', $order->trans_no==0, 0, 0, 0, null, true);
	if (isset($_POST['_OrderDate_changed'])) {
		if (!is_company_currency($order->customer_currency) 
			&& (get_base_sales_type()>0)) {
				$change_prices = 1;
		}
		$Ajax->activate('_ex_rate');
	}
    $myrow234 = get_company_item_pref('sales_persons');

    if($myrow234['sale_enable'] == 0){}
    else {
        sales_persons_list_row(_("Sales Person:"), 'salesman', $_SESSION['Items']->salesman);
    }
	// added dimensions
	$dim = get_company_pref('use_dimension');
	if ($dim > 0)
		dimensions_list_row(_("Dimension").":", 'dimension_id', 
			null, true, ' ', false, 1, false);
	else
		hidden('dimension_id', 0);
	if ($dim > 1)
		dimensions_list_row(_("Dimension")." 2:", 'dimension2_id', 
			null, true, ' ', false, 2, false);
	else
		hidden('dimension2_id', 0);

	end_outer_table(1); // outer table

	if ($change_prices != 0) {
		foreach ($order->line_items as $line_no=>$item) {
			$line = &$order->line_items[$line_no];
			$line->price = get_price($line->stock_id, $order->customer_currency,
				$order->sales_type, $order->price_factor, get_post('OrderDate'));
		}
	    $Ajax->activate('items_table');
	}


	return $customer_error;
}

//---------------------------------------------------------------------------------

function display_credit_items($title, &$order)
{
    global $path_to_root;

    display_heading($title);
    div_start('items_table');
    start_table(TABLESTYLE, "width='90%'");
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
	$th = array(_("Item Code"), _("Item Description"));

$currency = $order->customer_currency;
	//Text Boxes Headings

	if($myrow_1['sale_enable']) {
		array_append($th, array($myrow_1['label_value']._("")) );
	}
	if($myrow_2['sale_enable']) {
		array_append($th, array($myrow_2['label_value']._("")) );
	}
	if($myrow_3['sale_enable']) {
		array_append($th, array($myrow_3['label_value']._("")) );
	}
	if($myrow_4['sale_enable']) {
		array_append($th, array($myrow_4['label_value']._("")) );
	}
	if($myrow_5['sale_enable']) {
		array_append($th, array($myrow_5['label_value']._("")) );
	}
	if($myrow_6['sale_enable']) {
		array_append($th, array($myrow_6['label_value']._("")) );
	}
	if($myrow_7['sale_enable']) {
		array_append($th, array($myrow_7['label_value']._("")) );
	}
	if($myrow_8['sale_enable']) {
		array_append($th, array($myrow_8['label_value']._("")) );
	}
	if($myrow_9['sale_enable']) {
		array_append($th, array($myrow_9['label_value']._("")) );
	}
	if($myrow_10['sale_enable']) {
		array_append($th, array($myrow_10['label_value']._("")) );
	}
	if($myrow_11['sale_enable']) {
		array_append($th, array($myrow_11['label_value']._("")) );
	}
	if($myrow_12['sale_enable']) {
		array_append($th, array($myrow_12['label_value']._("")) );
	}
	if($myrow_13['sale_enable']) {
		array_append($th, array($myrow_13['label_value']._("")) );
	}
	if($myrow_14['sale_enable']) {
		array_append($th, array($myrow_14['label_value']._("")) );
	}
	if($myrow_15['sale_enable']) {
		array_append($th, array($myrow_15['label_value']._("")) );
	}
	if($myrow_16['sale_enable']) {
		array_append($th, array($myrow_16['label_value']._("")) );
	}
	if($myrow_17['sale_enable']) {
		array_append($th, array($myrow_17['label_value']._("")) );
	}
	if($myrow_18['sale_enable']) {
		array_append($th, array($myrow_18['label_value']._("")) );
	}
	if($myrow_19['sale_enable']) {
		array_append($th, array($myrow_19['label_value']._("")) );
	}
	if($myrow_20['sale_enable']) {
		array_append($th, array($myrow_20['label_value']._("")) );
	}
	if($myrow_21['sale_enable']) {
		array_append($th, array($myrow_21['label_value']._("")) );
	}

	{
		$pref=get_company_prefs();
		if($pref['alt_uom'] == 1) {
			if ($pref['batch'] == 1) {
				array_append($th, array(_("Quantity"), _("Batch"), _("Unit"),_("Con factor"),
					_("Price .($currency)"), _("Discount %"), _("Total"), ''));
			} else {
				array_append($th, array(_("Quantity"), _("Unit"),_("Con factor"),
					_("Price .($currency)"), _("Discount %"), _("Total"), ''));
			}
		}
		else{
			
			if ($pref['batch'] == 1) {
				array_append($th, array(_("Quantity"), _("Batch"), _("Unit"),_("Con factor"),
					_("Price .($currency)"), _("Discount %"), _("Total"), ''));
			} else {
				array_append($th, array(_("Quantity"), _("Unit"),
					_("Price .($currency)"), _("Discount %"), _("Total"), ''));
		
			}
		}
	}


    if (count($order->line_items)) $th[]= '';

    table_header($th);

    $subtotal = 0;
    $k = 0;  //row colour counter

    $id = find_submit('Edit');

    foreach ($order->get_items() as $line_no=>$line)
    {
	$line_total =	round($line->qty_dispatched * $line->price * (1 - $line->discount_percent),
	   user_price_dec());

	if ( $id != $line_no)
	{
	    alt_table_row_color($k);

    	    label_cell("<a target='_blank' href='$path_to_root/inventory/inquiry/stock_status.php?stock_id=" . $line->stock_id . "'>$line->stock_id</a>");
    	    label_cell($line->item_description, "nowrap");
    	   /// uroosa
        // $price = get_last_price_credit($line->stock_id, $order->customer_id);
        // global $SysPrefs;
        // if ($SysPrefs->show_view_creditnote() == 1)
        // {
        //     echo "<td>";

        //     echo "<a target='_blank' href='$path_to_root/sales/view/view_credit_history.php?stock_id=" . $line->stock_id . "&customer_id=" . $order->customer_id . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\" >" . number_format2($price, 2) . "</a>";
        // }
        //Uroosa
		//text boxes labels
		if($myrow_1['sale_enable'])
		{
			label_cell($line->$myrow_1['name']);
		}
		if($myrow_2['sale_enable'])
		{
			label_cell($line->$myrow_2['name']);
		}
		if($myrow_3['sale_enable'])
		{
			label_cell($line->$myrow_3['name']);
		}
		if($myrow_4['sale_enable'])
		{
			label_cell($line->$myrow_4['name']);
		}
		if($myrow_5['sale_enable'])
		{
			label_cell($line->$myrow_5['name']);
		}
		if($myrow_6['sale_enable'])
		{
			label_cell($line->$myrow_6['name']);
		}
		if($myrow_7['sale_enable'])
		{
			label_cell($line->$myrow_7['name']);
		}
		if($myrow_8['sale_enable'])
		{
			label_cell($line->$myrow_8['name']);
		}
		if($myrow_9['sale_enable'])
		{
			label_cell($line->$myrow_9['name']);
		}
		if($myrow_10['sale_enable'])
		{
			label_cell($line->$myrow_10['name']);
		}
		if($myrow_11['sale_enable'])
		{
			label_cell($line->$myrow_11['name']);
		}
		if($myrow_12['sale_enable'])
		{
			label_cell($line->$myrow_12['name']);
		}

		///combo inputs
		if($myrow_13['sale_enable'])
		{
			label_cell($line->$myrow_13['name']);
		}
		if($myrow_14['sale_enable'])
		{
			label_cell($line->$myrow_14['name']);
		}
		if($myrow_15['sale_enable'])
		{
			label_cell($line->$myrow_15['name']);
		}
		if($myrow_16['sale_enable'])
		{
			label_cell($line->$myrow_16['name']);
		}
		if($myrow_17['sale_enable'])
		{
			label_cell($line->$myrow_17['name']);
		}
		if($myrow_18['sale_enable'])
		{
			label_cell($line->$myrow_18['name']);
		}
		if($myrow_19['sale_enable'])
		{
			label_cell($line->$myrow_19['name']);
		}
		if($myrow_20['sale_enable'])
		{
			label_cell($line->$myrow_20['name']);
		}
		if($myrow_21['sale_enable'])
		{
			label_cell($line->$myrow_21['name']);
		}

		qty_cell($line->qty_dispatched, false, get_qty_dec($line->stock_id));
		$batch=get_batch_by_id($line->batch);
		if($pref['batch'] == 1)
			label_cell($batch['name']);

		if($pref['alt_uom'] == 1) {
			label_cell($line->units_id);
			label_cell($line->con_factor);
		}
		else{
			label_cell($line->units);
		}

    	    amount_cell($line->price);

	    	percent_cell($line->discount_percent * 100);
    	    amount_cell($line_total);

    	    edit_button_cell("Edit$line_no", _('Edit'),
				_('Edit document line'));
    	    delete_button_cell("Delete$line_no", _('Delete'),
				_('Remove line from document'));

    	    end_row();
        }
        else
        {
	    credit_edit_item_controls($order, $k, $line_no);
	}

	$subtotal += $line_total;
    }

    if ($id==-1)
        credit_edit_item_controls($order, $k);

	$colspan = 6;
    $display_sub_total = price_format($subtotal);
    label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right", "align=right", 2);

    if (!isset($_POST['ChargeFreightCost']) OR ($_POST['ChargeFreightCost'] == ""))
		$_POST['ChargeFreightCost'] = 0;
	start_row();
	label_cell(_("Shipping"), "colspan=$colspan align=right");
	small_amount_cells(null, 'ChargeFreightCost', price_format(get_post('ChargeFreightCost',0)));
    label_cell('', 'colspan=2');
	end_row();

    $taxes = $order->get_taxes($_POST['ChargeFreightCost']);

    $tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);

    $display_total = price_format(($subtotal + $_POST['ChargeFreightCost'] + $tax_total));

    label_row(_("Credit Note Total"), $display_total, "colspan=$colspan align=right","class='amount'", 2);

    end_table();
    div_end();
}

//---------------------------------------------------------------------------------

function credit_edit_item_controls(&$order, $rowcounter, $line_no=-1)
{
	global $Ajax;
	alt_table_row_color($rowcounter);
	$id = find_submit('Edit');
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
	if ($line_no!=-1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = qty_format($order->line_items[$id]->qty_dispatched, $_POST['stock_id'], $dec);
		$_POST['price'] = price_format($order->line_items[$id]->price);
		$_POST['Disc'] = percent_format(($order->line_items[$id]->discount_percent)*100);
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
		$_POST['batch'] = $order->line_items[$id]->batch;
		$_POST['con_factor'] = $order->line_items[$id]->con_factor;
		$units = $order->line_items[$id]->units;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		label_cell($order->line_items[$id]->item_description, "nowrap");
	    $Ajax->activate('items_table');
	}
	else
	{
		stock_items_list_cells(null,'stock_id', null, false, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('price');
			$Ajax->activate('units_id');
			$Ajax->activate('text1');
			$Ajax->activate('text2');
			$Ajax->activate('text3');
			$Ajax->activate('text4');
			$Ajax->activate('text5');
			$Ajax->activate('text6');
			$Ajax->activate('amount1');
			$Ajax->activate('amount2');
			$Ajax->activate('amount3');
			$Ajax->activate('amount4');
			$Ajax->activate('amount5');
			$Ajax->activate('amount6');
			$Ajax->activate('combo1');
			$Ajax->activate('combo2');
			$Ajax->activate('combo3');
			$Ajax->activate('combo4');
			$Ajax->activate('combo5');
			$Ajax->activate('combo6');
			$Ajax->activate('date1');
			$Ajax->activate('date2');
			$Ajax->activate('date3');
			$Ajax->activate('batch');
			$Ajax->activate('con_factor');
			$Ajax->activate('qty');
			    $Ajax->activate('units');
			    $Ajax->activate('line_total');
		}
  		$item_info = get_item_edit_info($_POST['stock_id']);

		$dec = $item_info['decimals'];
   		$_POST['qty'] = number_format2(1, $dec);
		$units = $item_info["units"];
		$_POST['con_factor'] = $item_info["con_factor"];
		$price = get_price($_POST['stock_id'],
			$order->customer_currency, $order->sales_type,
			$order->price_factor, get_post('OrderDate'));
		$_POST['price'] = price_format($price);
		
		// default to the customer's discount %
		$_POST['Disc'] = percent_format($order->default_discount * 100);
	}
//For updation
	if($myrow_1['sale_enable'])
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
	if($myrow_2['sale_enable'])
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
	if($myrow_3['sale_enable'])
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
	if($myrow_4['sale_enable'])
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
	if($myrow_5['sale_enable'])
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
	if($myrow_6['sale_enable'])
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
	if($myrow_7['sale_enable'])
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
	if($myrow_8['sale_enable'])
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
	if($myrow_9['sale_enable'])
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
	if($myrow_10['sale_enable'])
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
	if($myrow_11['sale_enable'])
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
	if($myrow_12['sale_enable'])
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
	if($myrow_13['sale_enable'])
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
	if($myrow_14['sale_enable'])
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
	if($myrow_15['sale_enable'])
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
	if($myrow_16['sale_enable'])
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
	if($myrow_17['sale_enable'])
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
	if($myrow_18['sale_enable'])
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
	if($myrow_19['sale_enable'])
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
	if($myrow_20['sale_enable'])
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
	if($myrow_21['sale_enable'])
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
	
	//////uroosa

    // if($order->trans_type == 11)
    // {
    //     global $path_to_root, $SysPrefs;

    //     $price = get_last_price_credit($_POST['stock_id'], $order->customer_id);
    //     if ($SysPrefs->show_view_creditnote() == 1) {
    //         echo "<td>";
    //         div_start('hi');
    //         echo "<a target='_blank' href='$path_to_root/sales/view/view_credit_history.php?stock_id=" . $_POST['stock_id'] . "&customer_id=" . $_POST['customer_id'] . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\" >" . number_format2($price, 2) . "</a>";
    //         div_end();
    //     } else {

    //     }
    // }

///

	qty_cells(null, 'qty', $_POST['qty']);
	$pref=get_company_prefs();
	if($pref['batch'] == 1)
	batch_list_cells(_(""), $_POST['stock_id'], 'batch', null, false, false, true, true, $_POST['Location']);

	if($pref['alt_uom'] == 1){
		stock_units_list_cell("",'units_id',$units,1,$_POST['stock_id']);
		qty_cells("",'con_factor',$_POST['con_factor']);
	}

	else{
		label_cell($units, '', 'units');
	}

	amount_cells(null, 'price');
	small_amount_cells(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());

	amount_cell(input_num('qty') * input_num('price') * (1 - input_num('Disc')/100), false, '', 'line_total');

	if ($id!=-1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('line_no', $line_no);
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

function credit_options_controls($credit)
{
	global $Ajax;
	br();

	if (isset($_POST['_CreditType_update']))
		$Ajax->activate('options');

	div_start('options');
	start_table(TABLESTYLE2);

	credit_type_list_row(_("Credit Note Type"), 'CreditType', null, true);

	if ($_POST['CreditType'] == "Return")
	{

		/*if the credit note is a return of goods then need to know which location to receive them into */
		if (!isset($_POST['Location']))
			$_POST['Location'] = $credit->Location;
	   	locations_list_row(_("Items Returned to Location"), 'Location', $_POST['Location']);
	}
	else
	{
		/* the goods are to be written off to somewhere */
		gl_all_accounts_list_row(_("Write off the cost of the items to"), 'WriteOffGLCode', null);
	}

	textarea_row(_("Memo"), "CreditText", null, 51, 3);
	echo "</table>";
 div_end();
}


//---------------------------------------------------------------------------------

