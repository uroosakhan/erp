<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
include_once($path_to_root . "/sales/includes/product_class.inc");

//--------------------------------------------------------------------------------
function add_to_order(&$order, $new_item, $from_date,
        $to_date, $dozen, $packing1, $packing2,
        $packing3, $scheme, $scheme_on_ctn, $scheme_from_date, $scheme_to_date)
{
    $order->add_to_cart (count($order->line_items),	$new_item, $from_date,
    $to_date, $dozen, $packing1, $packing2, $packing3, $scheme, $scheme_on_ctn,
    $scheme_from_date, $scheme_to_date);
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
	$th = array(_(""), _(""), _("Rate Update"), _(""),
		_("TP/"), _("") , _(""), _(""), _(""), _("Scheme On"), _("Scheme"), _(""), _(""), "", "");
	$th1 = array(_(""), _("Item"), _("From Date"), _("To Date"),
		_("Dozen"), _("Packing") , _(""), _(""), _("Scheme"), _("CTN"), _("From Date"), _("To Date"), "", "Values", "");
//	if ($order->trans_no == 0) {
//	unset( $th[3] );
//	}
//
	if (count($order->line_items))
	     $th[]= '';
	if (count($order->line_items))
	     $th1[]= '';
	table_header($th);
	table_header($th1);
	$k = 0;  //row colour counter

	$id = find_submit('Edit');

//	$low_stock = $order->check_qoh($_POST['OrderDate'], $_POST['Location']);
	foreach ($order->get_items() as $line_no=>$stock_item)
	{
        alt_table_row_color($k);
	    if (!$editable_items || $id != $line_no)
		{
            label_cell($stock_item->stock_id);
            label_cell($stock_item->description);
            label_cell($stock_item->from_date);
            label_cell($stock_item->to_date);
            amount_cell($stock_item->dozen);
            label_cell($stock_item->packing1);
            label_cell($stock_item->packing2);
            label_cell($stock_item->packing3);
            label_cell($stock_item->scheme);
            label_cell($stock_item->scheme_on_ctn);
            label_cell($stock_item->scheme_from_date);
            label_cell($stock_item->scheme_to_date);
            check_cells(null, 'on_value1'.$line_no, null, false);

            $Plus = $stock_item->packing3 + $stock_item->scheme;
            $Divide= $stock_item->dozen / $Plus;
            $Values = $Divide * $stock_item->scheme;
            amount_cell($Values);
            $order->line_items[$line_no]->values = $Values;
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
//		$total += $line_total;
	}
	if ($id==-1 && $editable_items)
		sales_order_item_controls($order, $k);
	$colspan = 6;
	if ($order->trans_no!=0)
		++$colspan;
//	start_row();
//	label_cell(_("Shipping Charge"), "colspan=$colspan align=right");
//	small_amount_cells(null, 'freight_cost', price_format(get_post('freight_cost',0)));
//	label_cell('', 'colspan=2');
//	end_row();
//	$display_sub_total = price_format($total + input_num('freight_cost'));
//	label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right", 2);
//	$taxes = $order->get_taxes(input_num('freight_cost'));
//	$tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2);
//	$display_total = price_format(($total + input_num('freight_cost') + $tax_total));
//	start_row();
//	label_cells(_("Amount Total"), $display_total, "colspan=$colspan align=right","align=right");
//	submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true);
//	end_row();
	end_table();
//	if ($low_stock)
//		display_note(_("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");

    div_end();
}

// ------------------------------------------------------------------------------

function display_order_header(&$order, $editable, $date_text)
{
    start_outer_table(TABLESTYLE2, "width='80%'");

	table_section(1);
	$customer_error = "";
//	if (isset($order) && !$editable)
//	{
//		label_row(null, $order->customer_name . " - " . $order->deliver_to);
//		hidden('customer_id', $order->customer_id);
//		hidden('branch_id', $order->Branch);
//		hidden('sales_type', $order->sales_type);
//		if ($order->trans_type != ST_SALESORDER  && $order->trans_type != ST_SALESQUOTE) {
//			hidden('dimension_id', $order->dimension_id); // 2008-11-12 Joe Hunt
//			hidden('dimension2_id', $order->dimension2_id);
//		}
//	}
//	else
//	{
//		customer_list_row(_("Customer:"), 'customer_id', null, false, true, false, true);
//
//		if ($order->customer_id != get_post('customer_id', -1))
//		{
//			// customer has changed
//			$Ajax->activate('branch_id');
//  		}
//		customer_branches_list_row(_("Branch:"),
//	  	  $_POST['customer_id'], 'branch_id', null, false, true, true, true);
//
//		if( ($order->customer_id != get_post('customer_id', -1)) ||
//			($order->Branch != get_post('branch_id', -1)) ||
//				list_updated('customer_id'))
//		{
//
//			if (!isset($_POST['branch_id']) || $_POST['branch_id'] == "")
//			{
//				// ignore errors on customer search box call
//				if ($_POST['customer_id'] == '')
//					$customer_error = _("No customer found for entered text.");
//				else
//					$customer_error = _("The selected customer does not have any branches. Please create at least one branch.");
//				unset($_POST['branch_id']);
//				$order->Branch = 0;
//			}
//			else
//			{
//
//				$old_order = (PHP_VERSION<5) ? $order : clone $order;
//
//				$customer_error = get_customer_details_to_order($order, $_POST['customer_id'], $_POST['branch_id']);
//				$_POST['Location'] = $order->Location;
//				$_POST['deliver_to'] = $order->deliver_to;
//				$_POST['delivery_address'] = $order->delivery_address;
//				$_POST['phone'] = $order->phone;
//				$_POST['delivery_date'] = $order->due_date;
//
//				if (!in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
//					&& ($order->pos['cash_sale'] != $order->pos['credit_sale'])
//					&& (($order->payment_terms['cash_sale'] && !$order->pos['cash_sale']) ||
//						(!$order->payment_terms['cash_sale'] && !$order->pos['credit_sale']))) {
//							// force payment terms refresh if terms are editable
//							// and pos have no permitions for terms selected in customer record.
//							// Terms are set to first terms in allowed category below.
//							display_warning(
//								sprintf(_("Customer's payment terms '%s' cannot be selected on this POS"),
//									$order->payment_terms['terms']));
//							$order->payment = '';
//				} elseif (get_post('payment') !== $order->payment) {
//					$_POST['payment'] = $order->payment;
//					$Ajax->activate('delivery');
//					$Ajax->activate('payment');
//				} else {
//					if ($order->trans_type == ST_SALESINVOICE)
//					{
//						$_POST['delivery_date'] = $order->due_date;
//						$Ajax->activate('delivery_date');
//					}
//					$Ajax->activate('Location');
//					$Ajax->activate('deliver_to');
//					$Ajax->activate('phone');
//					$Ajax->activate('delivery_address');
//				}
//				// change prices if necessary
//				// what about discount in template case?
//				if ($old_order->customer_currency != $order->customer_currency) {
//					$change_prices = 1;
//				}
//				if ($old_order->sales_type != $order->sales_type) {
//				//  || $old_order->default_discount!=$order->default_discount
//					$_POST['sales_type'] = $order->sales_type;
//					$Ajax->activate('sales_type');
//					$change_prices = 1;
//				}
//				if ($old_order->dimension_id != $order->dimension_id) {
//					$_POST['dimension_id'] = $order->dimension_id;
//				    $Ajax->activate('dimension_id');
//				}
//				if ($old_order->dimension2_id != $order->dimension2_id) {
//					$_POST['dimension2_id'] = $order->dimension2_id;
//				    $Ajax->activate('dimension2_id');
//				}
//				unset($old_order);
//			}
//			set_global_customer($_POST['customer_id']);
//		} // changed branch
//		else
//		{
//			$row = get_customer_to_order($_POST['customer_id']);
//			if ($row['dissallow_invoices'] == 1)
//				$customer_error = _("The selected customer account is currently on hold. Please contact the credit control personnel to discuss.");
//		}
//
//	ref_row(_("Offer Code"), 'ref', _('Reference number unique for this document type'), null, '', $order->trans_type, array('date'=> @$_POST['OrderDate']));
	text_row((""), 'subject', null, 150, 200);
//    textarea_row(_("Note"), 'offer_note', null, 50, 4);
//	table_section(2);
//	if (!is_company_currency($order->customer_currency) && in_array($order->trans_type, array(ST_SALESINVOICE, ST_CUSTDELIVERY)))
//	{
//		label_row(_("Customer Currency:"), $order->customer_currency);
//		exchange_rate_display(get_company_currency(), $order->customer_currency,
//			($editable ? $_POST['OrderDate'] : $order->document_date));
//	}
//	customer_credit_row($_POST['customer_id'], $order->credit);
//	label_row(_("Customer Discount:"), ($order->default_discount * 100) . "%");
//	table_section(3);
//	start_row();
//	if (($order->pos['cash_sale'] || $order->pos['credit_sale'])
//		&& !$order->is_started()) {
//		 // editable payment type
//		if (get_post('payment') !== $order->payment) {
//			$order->payment = get_post('payment');
//			$order->payment_terms = get_payment_terms($order->payment);
//			$order->due_date = get_invoice_duedate($order->payment, $order->document_date);
//			if ($order->payment_terms['cash_sale']) {
//				$_POST['Location'] = $order->Location = $order->pos['pos_location'];
//				$order->location_name = $order->pos['location_name'];
//			}
//			$Ajax->activate('items_table');
//			$Ajax->activate('delivery');
//		}
//		$paymcat = !$order->pos['cash_sale'] ? PM_CREDIT :
//			(!$order->pos['credit_sale'] ? PM_CASH : PM_ANY);
//		// all terms are available for SO
//		$is_order = in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER));
//		sale_payment_list_cells(_('Payment:'), 'payment', $is_order ? PM_ANY : $paymcat, null, true, $is_order);
//	} else {
//		label_cells(_('Payment:'), $order->payment_terms['terms'], "class='label'");
//	}
//	end_row();
//
//	if($editable) {
//		$str = sales_types_list_row(_("Price List:"), 'sales_type', null, true);
//	} else {
//		label_row(_("Price List:"), $order->sales_type_name);
//	}
//	if ($order->sales_type != $_POST['sales_type']) {
//		$myrow = get_sales_type($_POST['sales_type']);
//		$order->set_sales_type($myrow['id'], $myrow['sales_type'],
//			$myrow['tax_included'], $myrow['factor']);
//		$Ajax->activate('sales_type');
//		$change_prices = 1;
//	}
//	table_section(4);
//	if ($editable)
//	{
//		if (!isset($_POST['OrderDate']) || $_POST['OrderDate'] == "")
//			$_POST['OrderDate'] = $order->document_date;
//
//    date_row($date_text, 'OfferDate', _('Date of offer'),
//        0, 0, 0, 0, null, true);
//    echo "<td>".pager_link(_("Product Regs.") ,
//        "/sales/customer_credit_invoice.php?InvoiceNumber=". 123);
//		if (isset($_POST['_OrderDate_changed']) || list_updated('payment')) {
//			if (!is_company_currency($order->customer_currency)
//				&& (get_base_sales_type()>0)) {
//					$change_prices = 1;
//			}
//			$Ajax->activate('_ex_rate');
//			if ($order->trans_type == ST_SALESINVOICE) {
//				$_POST['delivery_date'] = get_invoice_duedate(get_post('payment'), get_post('OrderDate'));
//			} else
//				$_POST['delivery_date'] = add_days(get_post('OrderDate'), $SysPrefs->default_delivery_required_by());
//			$Ajax->activate('items_table');
//			$Ajax->activate('delivery_date');
//		}
//		if ($order->trans_type != ST_SALESORDER && $order->trans_type != ST_SALESQUOTE)
//		{	// 2008-11-12 Joe Hunt added dimensions
//			$dim = get_company_pref('use_dimension');
//			if ($dim > 0)
//				dimensions_list_row(_("Dimension").":", 'dimension_id',
//					null, true, ' ', false, 1, false);
//			else
//				hidden('dimension_id', 0);
//			if ($dim > 1)
//				dimensions_list_row(_("Dimension")." 2:", 'dimension2_id',
//					null, true, ' ', false, 2, false);
//			else
//				hidden('dimension2_id', 0);
//		}
//	}
//	else
//	{
//		label_row($date_text, $order->document_date);
//		hidden('OrderDate', $order->document_date);
//	}
	end_outer_table(1);
	// outer table
//	if ($change_prices != 0) {
//		foreach ($order->line_items as $line_no=>$item) {
//			$line = &$order->line_items[$line_no];
//			$line->price = get_kit_price($line->stock_id, $order->customer_currency,
//				$order->sales_type, $order->price_factor, get_post('OrderDate'));
//		}
//	    $Ajax->activate('items_table');
//	}
	return $customer_error;
}

//--------------------------------------------------------------------------------

function sales_order_item_controls(&$order, &$rowcounter, $line_no=-1)
{
    global $Ajax;

	alt_table_row_color($rowcounter);

	$id = find_submit('Edit');
	if ($line_no!=-1 && $line_no == $id) // edit old line
	{
		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
		$_POST['qty'] = number_format2($order->line_items[$id]->qty_dispatched, $dec);
		$_POST['price'] = price_format($order->line_items[$id]->price);
		$_POST['Disc'] = percent_format($order->line_items[$id]->discount_percent*100);
//		$units = $order->line_items[$id]->units;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
            hidden('item_description', $_POST['item_description']);
            label_cell($_POST['item_description']);
		}
	    $Ajax->activate('items_table');
	}
	else	// prepare new line
	{
		if ($order->fixed_asset)
			stock_disposable_fa_list_cells(null,'stock_id', null, _('[Select item]'), true, $order->line_items);
		else
			sales_items_list_cells(null,'stock_id', null, false, true, true);
		if (list_updated('stock_id')) {
			    $Ajax->activate('packing1');
			    $Ajax->activate('packing2');
		}
		$item_info = get_item_edit_info($_POST['stock_id']);
//		$units = $item_info["units"];
//		$dec = $item_info['decimals'];
		$_POST['packing1'] = number_format2($item_info['amount2'], $dec);
//		$price = get_kit_price($_POST['stock_id'],
//			$order->customer_currency, $order->sales_type,
//			$order->price_factor, get_post('OrderDate'));
		$_POST['packing2'] = price_format($item_info['amount4']);
		$_POST['dozen'] = price_format($item_info['text2']);
//		// default to the customer's discount %
//		$_POST['Disc'] = percent_format($order->default_discount * 100);
	}
//    yesno_list_cells(null,'item_brand',null,'Body Soule','No Body Soule',false);
//    yesno_list_cells(null,'in',null,'%','Fixed',false);
//
    date_cells(null, 'from_date');
    date_cells(null, 'to_date');
    amount_cells(null, 'dozen');
    text_cells_readonly(null,'packing1', null, 20, 150);
    text_cells_readonly(null,'packing2', null, 20, 150);
    text_cells(null,'packing3', null, 20, 150);
    text_cells(null,'scheme', null, 20, 150);
    text_cells(null,'scheme_on_ctn', null, 20, 150);
    date_cells(null, 'scheme_from_date');
    date_cells(null, 'scheme_to_date');
    label_cell("");
//    yesno_list_cells(null,'offer_status',null,'Active','Inactive',false);
//    yesno_list_cells(null,'status',null,'Item Level','Invoice Level',false);
//    yesno_list_cells(null,'offer_on',null,'All','Special',false);
//    levels_search_list_cells(null, 'offer_calc_level', null,false,"", "", '','','','',false);

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
			$title = _("Order Delivery Details");
			$delname = _("Required Delivery Date").':';
		}
		display_heading($title);
		start_outer_table(TABLESTYLE2, "width='90%'");
		table_section(1);

		locations_list_row(_("Deliver from Location:"), 'Location', null, false, true, $order->fixed_asset);
		if ($order->payment_terms['days_before_due'] == -1)
		{
			$Ajax->addUpdate('items_table', 'prep_amount', price_format($order->get_trans_total())); // bind to items_table update
			if (!$order->is_started())
				amount_row(_("Pre-Payment Required:"), 'prep_amount');
			else
				label_row(_("Pre-Payment Required:"), price_format($order->prep_amount), "class='label'");
		}
		if (list_updated('Location'))
			$Ajax->activate('items_table');


		date_row($delname, 'delivery_date',
			$order->trans_type==ST_SALESORDER ?  _('Enter requested day of delivery') 
				: $order->trans_type==ST_SALESQUOTE ? _('Enter Valid until Date') : '');
		text_row(_("Deliver To:"), 'deliver_to', $order->deliver_to, 50, 60,
			_('Additional identifier for delivery e.g. name of receiving person'));

		textarea_row(_("Address:"), 'delivery_address', $order->delivery_address, 35, 5,
			_('Delivery address. Default is address of customer branch'));

		table_section(2);

		text_row(_("Contact Phone Number:"), 'phone', $order->phone, 25, 25,
		    _('Phone number of ordering person. Defaults to branch phone number'));
		text_row(_("Customer Reference:"), 'cust_ref', $order->cust_ref, 25, 25,
		  _('Customer reference number for this order (if any)'));
		textarea_row(_("Comments:"), "Comments", $order->Comments, 31, 5);

		shippers_list_row(_("Shipping Company:"), 'ship_via', $order->ship_via);

		end_outer_table(1);
	}
	div_end();
}

function levels_search_list($name, $selected_id=null,$spec_option=false, $Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=false)
{
    $items = array();
    $items['0'] =  _(" ");
    $items['1'] =  _("Level-1");
    $items['2'] =  _("Level-2");
    $items['3'] =  _("Level-3");
    $items['4'] =  _("Level-4");
    $items['5'] =  _("Level-5");
    $items['6'] =  _("Level-6");
    $items['7'] =  _("Level-7");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'spec_option' => $spec_option === true ? _("All ") : $spec_option,
            'async' => false ) ); // FIX?
}

function levels_search_list_cells($label, $name, $selected_id=null,$spec_option=false,$Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo levels_search_list($name, $selected_id,$spec_option,$Combo1, $Combo2, $Combo3,$Combo4,$Combo5,$Combo6,$submit_on_change);
    echo "</td>\n";
}

function levels_search_list_row($label, $name, $selected_id=null, $spec_option=false,$Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    levels_search_list_cells(null, $name, $selected_id, $spec_option,$Combo1, $Combo2, $Combo3,$Combo4,$Combo5,$Combo6,$submit_on_change);
    echo "</tr>\n";
}


function pager_link($link_text, $url, $icon=false)
{
    global $path_to_root;

    if (user_graphic_links() && $icon)
        $link_text = set_icon($icon, $link_text);

    $href = $path_to_root . $url;
    return "<a href='$href'>" . $link_text . "</a>";
}