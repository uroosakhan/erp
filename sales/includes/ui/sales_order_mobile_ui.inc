<?php

include_once($path_to_root . "/sales/includes/cart_class.inc");

//--------------------------------------------------------------------------------
function add_to_order(&$order, $new_item, $new_item_qty, $price, $discount, $description='',$units_id,$con_factor,
					  $text1, $text2, $text3, $text4, $text5, $text6,
					  $amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
					  $date1, $date2, $date3,
					  $combo1, $combo2, $combo3,$combo4, $combo5, $combo6)
{
	// calculate item price to sum of kit element prices factor for 
	// value distribution over all exploded kit items
	$std_price = get_kit_price($new_item, $order->customer_currency,
		$order->sales_type,	$order->price_factor, get_post('OrderDate'), true);

	if ($std_price == 0)
		$price_factor = 0;
	else
		$price_factor = $price/$std_price;

	$kit = get_item_kit($new_item);
	$item_num = db_num_rows($kit);

	while($item = db_fetch($kit)) {
		$std_price = get_kit_price($item['stock_id'], $order->customer_currency,
			$order->sales_type,	$order->price_factor, get_post('OrderDate'), true);

		// rounding differences are included in last price item in kit
		$item_num--;
		if ($item_num) {
			$price -= $item['quantity']*$std_price*$price_factor;
			$item_price = $std_price*$price_factor;
		} else {
			if ($item['quantity'])
				$price = $price/$item['quantity'];
			$item_price = $price;
		}
		$item_price = round($item_price, user_price_dec());

		if (!$item['is_foreign'] && $item['item_code'] != $item['stock_id'])
		{	// this is sales kit - recurse 
			add_to_order($order, $item['stock_id'], $new_item_qty*$item['quantity'],
				$item_price, $discount, '',$con_factor,
				$text1, $text2, $text3, $text4, $text5, $text6,
				$amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
				$date1, $date2, $date3,
				$combo1, $combo2, $combo3,$combo4, $combo5, $combo6);
		}
		else
		{	// stock item record eventually with foreign code

			// check duplicate stock item
			foreach ($order->line_items as $order_item)
			{
				if (strcasecmp($order_item->stock_id, $item['stock_id']) == 0)
				{
					display_warning(_("For Part :").$item['stock_id']. " "
						. _("This item is already on this document. You have been warned."));
					break;
				}
			}
			$order->add_to_cart (count($order->line_items),	$item['stock_id'],
				$new_item_qty*$item['quantity'], $item_price, $discount, 0,0,
				$description, 0, 0 , 0, $units_id,$con_factor,
				$text1, $text2, $text3, $text4, $text5, $text6,
				$amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
				$date1, $date2, $date3,
				$combo1, $combo2, $combo3,$combo4, $combo5, $combo6);
		}
	}

}
//---------------------------------------------------------------------------------

function get_customer_details_to_order(&$order, $customer_id, $branch_id)
{
	global $SysPrefs;

	$ret_error = "";

	$myrow = get_customer_to_order($customer_id);

	$name = $myrow['name'];

	if ($myrow['dissallow_invoices'] == 1)
		$ret_error = _("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");

	$deliver = $myrow['address']; // in case no branch address use company address

	$order->set_customer($customer_id, $name, $myrow['curr_code'],
		$myrow['discount'], $myrow['payment_terms'], $myrow['pymt_discount']);

	// the sales type determines the price list to be used by default
	$order->set_sales_type($myrow['salestype'], $myrow['sales_type'], $myrow['tax_included'],
		$myrow['factor']);

	$order->credit = $myrow['cur_credit'];

	if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE)
	{
		$order->dimension_id = $myrow['dimension_id'];
		$order->dimension2_id = $myrow['dimension2_id'];
	}
	$result = get_branch_to_order($customer_id, $branch_id);

	if (db_num_rows($result) == 0)
	{
		return _("The selected customer and branch are not valid, or the customer does not have any branches.");
	}

	$myrow = db_fetch($result);

	// FIX - implement editable contact selector in sales order
	$contact = get_branch_contacts($branch_id, 'order', $customer_id);
	$order->set_branch($branch_id, $myrow["tax_group_id"],
		$myrow["tax_group_name"], @$contact["phone"], @$contact["email"]);

	$address = trim($myrow["br_post_address"]) != '' ? $myrow["br_post_address"]
		: (trim($myrow["br_address"]) != '' ? $myrow["br_address"]:$deliver);

	$order->set_delivery($myrow["default_ship_via"], $myrow["br_name"],
		$address);
	if ($order->trans_type == ST_SALESINVOICE) {
		$order->due_date = get_invoice_duedate($order->payment, $order->document_date);
	}
	elseif ($order->trans_type == ST_SALESORDER)
		$order->due_date = add_days($order->document_date, $SysPrefs->default_delivery_required_by());
	elseif ($order->trans_type == ST_SALESQUOTE)
		$order->due_date = add_days($order->document_date, $SysPrefs->default_quote_valid_days());

	if($order->payment_terms['cash_sale']) {
		$order->set_location($order->pos["pos_location"], $order->pos["location_name"]);
	} else
		$order->set_location($myrow["default_location"], $myrow["location_name"]);

	return $ret_error;
}

//---------------------------------------------------------------------------------

function display_order_summary($title, &$order, $editable_items=false)
{

	display_heading($title);

	div_start('items_table');
	start_table(TABLESTYLE, "width='90%'");
	$pref = get_company_prefs();
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
	if($pref['alt_uom'] == 1)
	{
		array_append($th, array( _("Quantity"),_("Delivered"),

			_("Unit"),_("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _(" ")) );
	}
	else{
		array_append($th, array( _("Quantity"),_("Delivered"),

			_("Unit"),/*_("Con.factor"),*/ $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _(" ")) );
	}
	if($pref['alt_uom'] == 1) {
		if ($order->trans_no == 0) {
			unset($th[3]);
		}
	}
	else {
		if ($order->trans_no == 0) {
			unset($th[3]);

		}
	}
	if (count($order->line_items))
		$th[]= '';

	table_header($th);

	$total = $TotalQty = $totalqty = 0;
	$k = 0;  //row colour counter

	$id = find_submit('Edit');

	$low_stock = $order->check_qoh($_POST['OrderDate'], $_POST['Location']);
	foreach ($order->get_items() as $line_no=>$stock_item)
	{

		$line_total = round($stock_item->qty_dispatched * $stock_item->price * (1 - $stock_item->discount_percent),
			user_price_dec());
		$TotalQty = $stock_item->qty_dispatched;

		$qoh_msg = '';
		if (!$editable_items || $id != $line_no)
		{
			if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else
				alt_table_row_color($k);

			view_stock_status_cell($stock_item->stock_id);

			label_cell($stock_item->item_description);

			//text boxes labels
			if($myrow_1['sale_enable'])
			{
				label_cell($stock_item->$myrow_1['name']);
			}
			if($myrow_2['sale_enable'])
			{
				label_cell($stock_item->$myrow_2['name']);
			}
			if($myrow_3['sale_enable'])
			{
				label_cell($stock_item->$myrow_3['name']);
			}
			if($myrow_4['sale_enable'])
			{
				label_cell($stock_item->$myrow_4['name']);
			}
			if($myrow_5['sale_enable'])
			{
				label_cell($stock_item->$myrow_5['name']);
			}
			if($myrow_6['sale_enable'])
			{
				label_cell($stock_item->$myrow_6['name']);
			}
			if($myrow_7['sale_enable'])
			{
				label_cell($stock_item->$myrow_7['name']);
			}
			if($myrow_8['sale_enable'])
			{
				label_cell($stock_item->$myrow_8['name']);
			}
			if($myrow_9['sale_enable'])
			{
				label_cell($stock_item->$myrow_9['name']);
			}
			if($myrow_10['sale_enable'])
			{
				label_cell($stock_item->$myrow_10['name']);
			}
			if($myrow_11['sale_enable'])
			{
				label_cell($stock_item->$myrow_11['name']);
			}
			if($myrow_12['sale_enable'])
			{
				label_cell($stock_item->$myrow_12['name']);
			}

			///combo inputs
			if($myrow_13['sale_enable'])
			{
				label_cell($stock_item->$myrow_13['name']);
			}
			if($myrow_14['sale_enable'])
			{
				label_cell($stock_item->$myrow_14['name']);
			}
			if($myrow_15['sale_enable'])
			{
				label_cell($stock_item->$myrow_15['name']);
			}
			if($myrow_16['sale_enable'])
			{
				label_cell($stock_item->$myrow_16['name']);
			}
			if($myrow_17['sale_enable'])
			{
				label_cell($stock_item->$myrow_17['name']);
			}
			if($myrow_18['sale_enable'])
			{
				label_cell($stock_item->$myrow_18['name']);
			}
			if($myrow_19['sale_enable'])
			{
				label_cell($stock_item->$myrow_19['name']);
			}
			if($myrow_20['sale_enable'])
			{
				label_cell($stock_item->$myrow_20['name']);
			}
			if($myrow_21['sale_enable'])
			{
				label_cell($stock_item->$myrow_21['name']);
			}


			$dec = get_qty_dec($stock_item->stock_id);
			qty_cell($stock_item->qty_dispatched, false, $dec);

			if ($order->trans_no!=0)
				qty_cell($stock_item->qty_done, false, $dec);

			//change by ansar for client req	label_cell($stock_item->units);

			if($pref['alt_uom'] == 1) {
				label_cell($stock_item->units_id);
				amount_cell($stock_item->con_factor);

			}
			else {
				label_cell($stock_item->units);
			}

			amount_cell($stock_item->price);
			percent_cell($stock_item->discount_percent * 100);
			amount_cell($line_total);

			if ($editable_items)
			{
				edit_button_cell("Edit$line_no", _("Edit"),
					_('Edit document line'));
				delete_button_cell("Delete$line_no", _("Delete"),
					_('Remove line from document'));
			}
			end_row();
		}
		else
		{
			sales_order_item_controls($order, $k,  $line_no);
		}

		$total += $line_total;
		$totalqty += $TotalQty;
	}

	if ($id==-1 && $editable_items)
		sales_order_item_controls($order, $k);

	$colspan = 6;
	if ($order->trans_no!=0)
		++$colspan;

	start_row();

	$display_sub_total = price_format($total/* + input_num('freight_cost') - $discount_amount1 - $discount_amount2*/);
	label_row(_("Total Qty:"), $totalqty, "colspan=2 align=right","align=right", 2);
	hidden('total_qty', $totalqty);
	label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right", 2);
	hidden('sub_total',$display_sub_total);
//	label_cell(_("Shipping Charge"), "colspan=$colspan align=right");
//	small_amount_cells(null, 'freight_cost', price_format(get_post//('freight_cost',0)));
hidden('freight_cost',0);
	echo "<tr>";
	if($order->trans_no != 0)
		echo "<td>";
	label_cell(_("Discount%"), "colspan=4 align=right");

	$AllowLineDiscount = get_company_pref('discount_algorithm');
	if(!isset($_POST['update']) && $order->trans_no == 0)
	{
		if($AllowLineDiscount == 7)
			$_POST['disc3'] = percent_format($order->default_discount * 100);
		else
			$_POST['disc3'] = percent_format(0 * 100);
	}
	elseif($order->trans_no != 0)
	{
		if($AllowLineDiscount == 7)
			$_POST['disc3'] = percent_format($order->disc1);
		else
			$_POST['disc3'] = percent_format(0 * 100);
	}

	text_cells_new4(null, 'disc1', $_POST['disc3'], 5, 50, false, "", "", "");
	echo '<td>Disc.Amt.';
	$TotalPercent = $_POST['disc1']/100;
	$_POST['discount1'] = number_format2($total*$TotalPercent);
	global $Ajax;
	$Ajax->activate('discount1');
	text_cells_new4(null, 'discount1', null, 7);
	echo "<td/>";
	$discount_amount1 = /*$total*input_num('discount1')/100*/input_num('discount1');
	echo "<tr>";
	if($order->trans_no != 0)
		echo "<td>";
	label_cell(_("Discount x Qty"), "colspan=4 align=right");
	text_cells_new6(null, 'disc2', null, 5);
	echo '<td>Disc. Amt.';

	if($_POST['disc2'] != 0)
		$TotalQty2 = number_format2(input_num('disc2')*$totalqty);
	$Ajax->activate('discount2');
	text_cells_new6(null, 'discount2', $TotalQty2, 7);
	echo "<td/>";
	$discount_amount2 = /*$total*input_num('discount2')/100*/input_num('discount2');
	label_cell('', 'colspan=2');
	end_row();
	$taxes = $order->get_taxes(input_num('freight_cost'), input_num('discount1'), input_num('discount2'));
	$tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);
	$display_total = price_format(($total + input_num('freight_cost') + $tax_total - $discount_amount1 - $discount_amount2 ));
	start_row();
	label_cells(_("Amount Total"), $display_total, "colspan=$colspan align=right","align=right");
	submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true);
	end_row();

	end_table();
	if ($low_stock)
		display_note(_("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");

	div_end();
}

// ------------------------------------------------------------------------------

function display_order_header(&$order, $editable, $date_text)
{
	global $Ajax, $SysPrefs;
	$myrowh_1 = get_company_hf_pref_from_position(1);
	$myrowh_2 = get_company_hf_pref_from_position(2);
	$myrowh_3 = get_company_hf_pref_from_position(3);
	$myrowh_4 = get_company_hf_pref_from_position(4);
	$myrowh_5 = get_company_hf_pref_from_position(5);
	$myrowh_6 = get_company_hf_pref_from_position(6);
	$myrowh_7 = get_company_hf_pref_from_position(7);
	$myrowh_8 = get_company_hf_pref_from_position(8);
	$myrowh_9 = get_company_hf_pref_from_position(9);
	$myrowh_10 = get_company_hf_pref_from_position(10);
	$myrowh_11 = get_company_hf_pref_from_position(11);
	$myrowh_12 = get_company_hf_pref_from_position(12);

	start_outer_table(TABLESTYLE2, "width='80%'");

	table_section(1);
	$customer_error = "";
	$change_prices = 0;

	if (isset($order) && !$editable)
	{
		label_row(null, $order->customer_name . " - " . $order->deliver_to);
		hidden('customer_id', $order->customer_id);
		hidden('branch_id', $order->Branch);
		hidden('sales_type', $order->sales_type);
		if ($order->trans_type != ST_SALESORDER  && $order->trans_type != ST_SALESQUOTE) {
			hidden('dimension_id', $order->dimension_id); // 2008-11-12 Joe Hunt
			hidden('dimension2_id', $order->dimension2_id);
		}
	}
	else
	{
		customer_list_row(_("Customer:"), 'customer_id', null, false, true, false, true);

		if ($order->customer_id != get_post('customer_id', -1))
		{
			// customer has changed
			$Ajax->activate('branch_id');
		}
		customer_branches_list_row(_("Branch:"),
			$_POST['customer_id'], 'branch_id', null, false, true, true, true);

		if( ($order->customer_id != get_post('customer_id', -1)) ||
			($order->Branch != get_post('branch_id', -1)) ||
			list_updated('customer_id'))
		{

			if (!isset($_POST['branch_id']) || $_POST['branch_id'] == "")
			{
				// ignore errors on customer search box call
				if ($_POST['customer_id'] == '')
					$customer_error = _("No customer found for entered text.");
				else
					$customer_error = _("The selected customer does not have any branches. Please create at least one branch.");
				unset($_POST['branch_id']);
				$order->Branch = 0;
			}
			else
			{

				$old_order = (PHP_VERSION<5) ? $order : clone $order;

				$customer_error = get_customer_details_to_order($order, $_POST['customer_id'], $_POST['branch_id']);
				$_POST['Location'] = $order->Location;
				$_POST['deliver_to'] = $order->deliver_to;
				$_POST['delivery_address'] = $order->delivery_address;
				$_POST['phone'] = $order->phone;
				$_POST['delivery_date'] = $order->due_date;
				$_POST['po_date'] = $order->po_date;



				if (!in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
					&& ($order->pos['cash_sale'] != $order->pos['credit_sale'])
					&& (($order->payment_terms['cash_sale'] && !$order->pos['cash_sale']) ||
						(!$order->payment_terms['cash_sale'] && !$order->pos['credit_sale']))) {
					// force payment terms refresh if terms are editable
					// and pos have no permitions for terms selected in customer record.
					// Terms are set to first terms in allowed category below.
					display_warning(
						sprintf(_("Customer's payment terms '%s' cannot be selected on this POS"),
							$order->payment_terms['terms']));
					$order->payment = '';
				} elseif (get_post('payment') !== $order->payment) {
					$_POST['payment'] = $order->payment;
					$Ajax->activate('delivery');
					$Ajax->activate('payment');
				} else {
					if ($order->trans_type == ST_SALESINVOICE)
					{
						$_POST['delivery_date'] = $order->due_date;
						$Ajax->activate('delivery_date');
					}
					$Ajax->activate('Location');
					$Ajax->activate('deliver_to');
					$Ajax->activate('phone');
					$Ajax->activate('delivery_address');
				}
				// change prices if necessary
				// what about discount in template case?
				if ($old_order->customer_currency != $order->customer_currency) {
					$change_prices = 1;
				}
				if ($old_order->sales_type != $order->sales_type) {
					//  || $old_order->default_discount!=$order->default_discount
					$_POST['sales_type'] = $order->sales_type;
					$Ajax->activate('sales_type');
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
		} // changed branch
		else
		{
			$row = get_customer_to_order($_POST['customer_id']);
			if ($row['dissallow_invoices'] == 1)
				$customer_error = _("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");
		}
	}

	ref_row(_("Reference").':', 'ref', _('Reference number unique for this document type'), null, '', $order->trans_type, array('date'=> @$_POST['OrderDate']));

//	table_section(2);

	if (!is_company_currency($order->customer_currency) && in_array($order->trans_type, array(ST_SALESINVOICE, ST_CUSTDELIVERY)))
	{
		label_row(_("Customer Currency:"), $order->customer_currency);
		exchange_rate_display(get_company_currency(), $order->customer_currency,
			($editable ? $_POST['OrderDate'] : $order->document_date));
	}

	customer_credit_row($_POST['customer_id'], $order->credit);

//	label_row(_("Customer Discount:"), ($order->default_discount * 100) . "%");

//	table_section(3);
	start_row();
	if (($order->pos['cash_sale'] || $order->pos['credit_sale'])
		&& !$order->is_started()) {
		// editable payment type
		if (get_post('payment') !== $order->payment) {
			$order->payment = get_post('payment');
			$order->payment_terms = get_payment_terms($order->payment);
			$order->due_date = get_invoice_duedate($order->payment, $order->document_date);
			if ($order->payment_terms['cash_sale']) {
				$_POST['Location'] = $order->Location = $order->pos['pos_location'];
				$order->location_name = $order->pos['location_name'];
			}
			$Ajax->activate('items_table');
			$Ajax->activate('delivery');
		}
		$paymcat = !$order->pos['cash_sale'] ? PM_CREDIT :
			(!$order->pos['credit_sale'] ? PM_CASH : PM_ANY);
		// all terms are available for SO
		$is_order = in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER));
//		sale_payment_list_cells(_('Payment:'), 'payment', $is_order ? PM_ANY : $paymcat, null, true, $is_order);
        hidden('payment', $_POST['payment']);
	} else {
		label_cells(_('Payment:'), $order->payment_terms['terms'], "class='label'");
	}
	end_row();

	if($editable) {
//		$str = sales_types_list_row(_("Price List:"), 'sales_type', null, true);
        hidden('sales_type', $_POST['sales_type']);
	}
	else
    {
		label_row(_("Price List:"), $order->sales_type_name);
	}
	if ($order->sales_type != $_POST['sales_type']) {
		$myrow = get_sales_type($_POST['sales_type']);
		$order->set_sales_type($myrow['id'], $myrow['sales_type'],
			$myrow['tax_included'], $myrow['factor']);
		$Ajax->activate('sales_type');
		$change_prices = 1;
	}

//	table_section(4);

	if ($editable)
	{
		if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
			$_POST['OrderDate'] = $order->document_date;


		date_row($date_text, 'OrderDate', _('Date of order receive'),
			$order->trans_no==0, 0, 0, 0, null, true);
		if (isset($_POST['_OrderDate_changed']) || list_updated('payment')) {
			if (!is_company_currency($order->customer_currency)
				&& (get_base_sales_type()>0)) {
				$change_prices = 1;
			}
			$Ajax->activate('_ex_rate');
			if ($order->trans_type == ST_SALESINVOICE) {
				$_POST['delivery_date'] = get_invoice_duedate(get_post('payment'), get_post('OrderDate'));
			} else
				$_POST['delivery_date'] = add_days(get_post('OrderDate'), $SysPrefs->default_delivery_required_by());
			$Ajax->activate('items_table');
			$Ajax->activate('delivery_date');
		}
		if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE)
		{	// 2008-11-12 Joe Hunt added dimensions
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
		}
	}
	else
	{
		label_row($date_text, $order->document_date);
		hidden('OrderDate', $order->document_date);
	}
    locations_list_row(_("Deliver from Location:"), 'Location', null, false, true, $order->fixed_asset);

	if($myrowh_1['enable'])
	{
		table_section(5);
		text_row($myrowh_1['label_value'], $myrowh_1['name'], $order->$myrowh_1['name'], $myrowh_1["s_width"], 40);
	}
	if($myrowh_2['enable'])
	{
		text_row($myrowh_2['label_value'], $myrowh_2['name'], $order->$myrowh_2['name'], $myrowh_2["s_width"], 40);
	}
	if($myrowh_3['enable'])
	{
		text_row($myrowh_3['label_value'], $myrowh_3['name'], $order->$myrowh_3['name'], $myrowh_3["s_width"], 40);
	}

	if($myrowh_4['enable'])
	{	table_section(6);
		small_amount_row($myrowh_4['label_value'], $myrowh_4['name']);
	}
	if($myrowh_5['enable'])
	{
		small_amount_row($myrowh_5['label_value'], $myrowh_5['name']);
	}
	if($myrowh_6['enable'])
	{
		small_amount_row($myrowh_6['label_value'], $myrowh_6['name']);
	}

	if($myrowh_7['enable'])
	{table_section(7);
		combo1_list_row($myrowh_7['label_value'], $myrowh_7['name'],$_POST['$myrowh_7["name"]']);
	}
	if($myrowh_8['enable'])
	{
		combo1_list_row($myrowh_8['label_value'], $myrowh_8['name'],$_POST['$myrowh_8["name"]']);
	}
	if($myrowh_9['enable'])
	{
		combo1_list_row($myrowh_9['label_value'], $myrowh_9['name'],$_POST['$myrowh_9["name"]']);
		table_section(8);
	}

	if($myrowh_10['enable'])
	{$_POST[$myrowh_10['name']] = $order->$myrowh_10['name'];
		date_row($myrowh_10['label_value'], $myrowh_10['name'],null);
	}
	if($myrowh_11['enable'])
	{$_POST[$myrowh_11['name']] = $order->$myrowh_11['name'];
		date_row($myrowh_11['label_value'], $myrowh_11['name'],null);
	}
	if($myrowh_12['enable'])
	{$_POST[$myrowh_12['name']] = $order->$myrowh_12['name'];
		date_row($myrowh_12['label_value'], $myrowh_12['name'],null);
	}
	end_outer_table(1); // outer table

	if ($change_prices != 0) {
		foreach ($order->line_items as $line_no=>$item) {
			$line = &$order->line_items[$line_no];
			$line->price = get_kit_price($line->stock_id, $order->customer_currency,
				$order->sales_type, $order->price_factor, get_post('OrderDate'));
		}
		$Ajax->activate('items_table');
	}

	return $customer_error;
}

//--------------------------------------------------------------------------------

function sales_order_item_controls(&$order, &$rowcounter, $line_no=-1)
{
	global $Ajax;

	alt_table_row_color($rowcounter);
	$pref = get_company_prefs();
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

	$id = find_submit('Edit');
	if ($line_no!=-1 && $line_no == $id) // edit old line
	{
		$myrow_1 = get_company_item_pref_from_position(1);
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = number_format2($order->line_items[$id]->qty_dispatched, $dec);
		$_POST['price'] = price_format($order->line_items[$id]->price);
		$_POST['Disc'] = percent_format($order->line_items[$id]->discount_percent*100);
		$_POST['con_factor'] = $order->line_items[$id]->con_factor;
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


//		$units = $order->line_items[$id]->units;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
		$_POST['units_id'] = $order->line_items[$id]->units_id;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);

		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
			hidden('item_description', $_POST['item_description']);
			label_cell($_POST['item_description']);
		}

		$Ajax->activate('items_table');
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

	}
	else	// prepare new line
	{
		if ($order->fixed_asset)
			stock_disposable_fa_list_cells(null,'stock_id', null, _('[Select item]'), true, $order->line_items);
		else
			sales_items_list_cells(null,'stock_id', null, false, true, true);
		if (list_updated('stock_id')) {
			$Ajax->activate('price');
			$Ajax->activate('units');
			$Ajax->activate('qty');
			$Ajax->activate('line_total');
			$Ajax->activate('units_id');
			$Ajax->activate('con_factor');
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

		}

		$item_info = get_item_edit_info($_POST['stock_id']);
		$_POST['units_id'] = $item_info["units"];

		$dec = $item_info['decimals'];

		$price = get_kit_price($_POST['stock_id'],
			$order->customer_currency, $order->sales_type,
			$order->price_factor, get_post('OrderDate'));
		$_POST['price'] = price_format($price);
		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount3'] = price_format($item_info["amount3"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);
		$_POST['amount5'] = price_format($item_info["amount5"]);
		$_POST['amount6'] = price_format($item_info["amount6"]);
		$_POST['con_factor'] = price_format($item_info["con_factor"]);
		$_POST['date1'] = sql2date($item_info["date1"]);
		$_POST['date2'] = sql2date($item_info["date2"]);
		$_POST['date3'] = sql2date($item_info["date3"]);

		//$formula=array_splice($item_info["text1"]);


		$formula = $item_info["text1"];
		$value=explode(",",$formula);
		$amount1=str_replace($value[0],$_POST['amount1'],$value[0]);
		$amount2=str_replace($value[1],$_POST['amount2'],$value[1]);

		$amount4=str_replace($value[3],$_POST['qty'],$value[3]);
		$amount5=str_replace($value[4],$_POST['amount1'],$value[4]);

		if($_POST['qty']==1 || $_POST['qty']==0)
		{
			if(strcmp($value[2],"*")==0)
			{
				$_POST['qty']=$amount1*$amount2;
				hidden('qty', $amount1*$amount2);
			}
			elseif(strcmp($value[2],"+")==0)
			{
				$_POST['qty']=$amount1+$amount2;
			}
			elseif(strcmp($value[2],"/")==0)
			{
				$_POST['qty']=$amount1/$amount2;
			}
			elseif(strcmp($value[2],"-")==0)
			{
				$_POST['qty']=$amount1-$amount2;
			}

		}


		//text boxes entry
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



		// default to the customer's discount %
		$AllowLineDiscount = get_company_pref('discount_algorithm');
		if($AllowLineDiscount == 6)
			$_POST['Disc'] = percent_format($order->default_discount * 100);
		else
			$_POST['Disc'] = percent_format(0 * 100);
	}
	if ($order->fixed_asset) {
		label_cell(1, '', 'qty');
		hidden('qty', 1);
	} else
		qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);

	if ($order->trans_no!=0) {
		qty_cell($line_no==-1 ? 0 :$order->line_items[$line_no]->qty_done, false, $dec);
	}

	if($pref['alt_uom'] == 1) {
		stock_units_list_cell(null, 'units_id', $_POST['units_id'],1,$_POST['stock_id']);
		qty_cells("", 'con_factor', $_POST['con_factor']);


	}
	else {
		label_cell($_POST['units_id'], '', 'units_id');
		hidden('units_id',$_POST['units_id']);
		hidden('con_factor',$_POST['con_factor']);

	}
	amount_cells(null, 'price');
	small_amount_cells(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());
	$line_total = input_num('qty') * input_num('price') * (1 - input_num('Disc') / 100);
	amount_cell($line_total, false, '','line_total');

	if ($id!=-1)
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
		submit_cells('AddItem', _("Add Item"), "colspan=2 align='center'",
			_('Add new item to document'), true);
	}

	end_row();
}

//--------------------------------------------------------------------------------

function display_delivery_details(&$order)
{
	global $Ajax;

	div_start('delivery');
	$myrowf_1 = get_company_hf_pref_from_position(13);
	$myrowf_2 = get_company_hf_pref_from_position(14);
	$myrowf_3 = get_company_hf_pref_from_position(15);
	$myrowf_4 = get_company_hf_pref_from_position(16);
	$myrowf_5 = get_company_hf_pref_from_position(17);
	$myrowf_6 = get_company_hf_pref_from_position(18);
	$myrowf_7 = get_company_hf_pref_from_position(19);
	$myrowf_8 = get_company_hf_pref_from_position(20);
	$myrowf_9 = get_company_hf_pref_from_position(21);
	$myrowf_10 = get_company_hf_pref_from_position(22);
	$myrowf_11 = get_company_hf_pref_from_position(23);
	$myrowf_12 = get_company_hf_pref_from_position(24);

	if ($order->payment_terms['cash_sale']) {	// Direct payment sale
		$Ajax->activate('items_table');
		display_heading(_('Cash payment'));
		start_table(TABLESTYLE2, "width='60%'");

		locations_list_row(_("Deliver from Location:"), 'Location', null, false, true);

		if (list_updated('Location'))
			$Ajax->activate('items_table');
		label_row(_("Cash account:"), $order->pos['bank_account_name']);
		textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);
		end_table();
		hidden('delivery_date', $order->due_date);
	} else {
		if ($order->trans_type==ST_SALESINVOICE)
		{
			$title = _("Delivery Details");
			$delname = _("Due Date").':';
		}
		elseif ($order->trans_type==ST_CUSTDELIVERY)
		{
			$title = _("Invoice Delivery Details");
			$delname = _("Invoice before").':';
		}
		elseif ($order->trans_type==ST_SALESQUOTE)
		{
			$title = _("Quotation Delivery Details");
			$delname = _("Valid until").':';
		}
		else
		{
//			$title = _("Order Delivery Details");
			$delname = _("Required Delivery Date").':';
		}
		display_heading($title);
		start_outer_table(TABLESTYLE2, "width='90%'");
		table_section(1);

		if ($order->payment_terms['days_before_due'] ==-1)
		{
			$Ajax->addUpdate('items_table', 'prep_amount', price_format($order->get_trans_total())); // bind to items_table update
			if (!$order->is_started())
				amount_row(_("Pre-Payment Required:"), 'prep_amount');
			else
				label_row(_("Pre-Payment Required:"), price_format($order->prep_amount), "class='label'");
		}
		if (list_updated('Location'))
			$Ajax->activate('items_table');


//		date_row($delname, 'delivery_date',
//			$order->trans_type==ST_SALESORDER ?  _('Enter requested day of delivery')
//				: $order->trans_type==ST_SALESQUOTE ? _('Enter Valid until Date') : '');
        hidden('delivery_date', $_POST['delivery_date']);
//		text_row(_("Deliver To:"), 'deliver_to', $order->deliver_to, 50, 60,
//			_('Additional identifier for delivery e.g. name of receiving person'));
        hidden('deliver_to', $_POST['deliver_to']);

//		textarea_row(_("Address:"), 'delivery_address', $order->delivery_address, 35, 5,
//			_('Delivery address. Default is address of customer branch'));
        hidden('delivery_address', $_POST['delivery_address']);

        global $leftmenu_save, $db_connections;
//var_dump($db_connections[$_SESSION["wa_current_user"]->company]["name"]);
		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RPL')
		{
			yesno_list_row(_("Sample"), 'sample', null, _("Yes"), _("No"), true);
			yesno_list_row(_("Supply"), 'supply', null, _("Yes"), _("No"), true);
			yesno_list_row(_("DC"), 'dc', null, _("Yes"), _("No"), true);
			yesno_list_row(_("Invoice"), 'invoice', null, _("Yes"), _("No"), true);
			textarea_row(_("Application:"), 'application',null, 40,
				_('Additional identifier for delivery e.g. name of receiving person'));

		}
//		textarea_row(_("Terms and conditions:"), "term_cond", $order->term_cond, 50, 15);

//		table_section(2);
//		text_row(_("Contact Phone Number:"), 'phone', $order->phone, 25, 25,
//			_('Phone number of ordering person. Defaults to branch phone number'));
//		date_cells(_("P.O.Date"), 'po_date', '');
        hidden('po_date', $_POST['po_date']);

//		text_row(_("Customer Reference:"), 'cust_ref', $order->cust_ref, 25, 25,
//			_('Customer reference number for this order (if any)'));
//		textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);

//		shippers_list_row(_("Shipping Company:"), 'ship_via', $order->ship_via);
        hidden('ship_via', $order->ship_via);


//		if($myrowf_1['enable'])
//		{
//			text_row($myrowf_1['label_value'], $myrowf_1['name'], $order->$myrowf_1['name'], $myrowf_1["s_width"], 40);
//		}
//		if($myrowf_2['enable'])
//		{
//			text_row($myrowf_2['label_value'], $myrowf_2['name'], $order->$myrowf_2['name'], $myrowf_2["s_width"], 40);
//		}
//		if($myrowf_3['enable'])
//		{
//			text_row($myrowf_3['label_value'], $myrowf_3['name'], $order->$myrowf_3['name'], $myrowf_3["s_width"], 40);
//		}
////		table_section(6);
//		if($myrowf_4['enable'])
//		{
//			small_amount_row($myrowf_4['label_value'], $myrowf_4['name']);
//		}
//		if($myrowf_5['enable'])
//		{
//			small_amount_row($myrowf_5['label_value'], $myrowf_5['name']);
//		}
//		if($myrowf_6['enable'])
//		{
//			small_amount_row($myrowf_6['label_value'], $myrowf_6['name']);
//		}
////		table_section(7);
//		if($myrowf_7['enable'])
//		{
//			combo1_list_row($myrowf_7['label_value'], $myrowf_7['name'],$order->$myrowf_7["name"]);
//		}
//		if($myrowf_8['enable'])
//		{
//			combo1_list_row($myrowf_8['label_value'], $myrowf_8['name'],$order->$myrowf_8["name"]);
//		}
//		if($myrowf_9['enable'])
//		{
//			combo1_list_row($myrowf_9['label_value'], $myrowf_9['name'],$order->$myrowf_9["name"]);
//		}
////		table_section(8);
//		if($myrowf_10['enable'])
//		{
//			date_row($myrowf_10['label_value'], $myrowf_10['name'],null);
//		}
//		if($myrowf_11['enable'])
//		{
//			date_row($myrowf_11['label_value'], $myrowf_11['name'],null);
//		}
//		if($myrowf_12['enable'])
//		{
//			date_row($myrowf_12['label_value'], $myrowf_12['name'],null);
//		}

		end_outer_table(1);
	}
	div_end();
}

