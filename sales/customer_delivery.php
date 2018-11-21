<?php

//-----------------------------------------------------------------------------
//
//	Entry/Modify Delivery Note against Sales Order
//
$page_security = 'SA_SALESDELIVERY';
$path_to_root = "..";

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

$js = "";
if ($SysPrefs->use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if (user_use_date_picker()) {
	$js .= get_js_date_picker();
}

if (isset($_GET['ModifyDelivery'])) {
	$_SESSION['page_title'] = sprintf(_("Modifying Delivery Note # %d."), $_GET['ModifyDelivery']);
	$help_context = "Modifying Delivery Note";
	processing_start();
} elseif (isset($_GET['OrderNumber'])) {
	$_SESSION['page_title'] = _($help_context = "Deliver Items for a Sales Order");
	processing_start();
}

page($_SESSION['page_title'], false, false, "", $js);

if (isset($_GET['AddedID'])) {
	$dispatch_no = $_GET['AddedID'];

	display_notification_centered(sprintf(_("Delivery # %d has been entered."),$dispatch_no));

	display_note(get_customer_trans_view_str(ST_CUSTDELIVERY, $dispatch_no, _("&View This Delivery")), 0, 1);

// 	display_note(print_document_link($dispatch_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
// 	display_note(print_document_link($dispatch_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
// 	display_note(print_document_link($dispatch_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
// 	display_note(print_document_link($dispatch_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

    global $SysPrefs;
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {
        display_note(print_document_link($dispatch_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
        display_note(print_document_link($dispatch_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
        display_note(print_document_link($dispatch_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
        display_note(print_document_link($dispatch_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

        if (!isset($_GET['prepaid']))
            hyperlink_params("$path_to_root/sales/customer_invoice.php", _("Invoice This Delivery"), "DeliveryNumber=$dispatch_no");

    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {}

    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 0) {

        display_note(print_document_link($dispatch_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
        display_note(print_document_link($dispatch_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
        display_note(print_document_link($dispatch_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
        display_note(print_document_link($dispatch_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

        if (!isset($_GET['prepaid']))
            hyperlink_params("$path_to_root/sales/customer_invoice.php", _("Invoice This Delivery"), "DeliveryNumber=$dispatch_no");

    }
    else {
        if($approval['approval'] == 0) {

            display_note(print_document_link($dispatch_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
            display_note(print_document_link($dispatch_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
            display_note(print_document_link($dispatch_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
            display_note(print_document_link($dispatch_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

            if (!isset($_GET['prepaid']))
                hyperlink_params("$path_to_root/sales/customer_invoice.php", _("Invoice This Delivery"), "DeliveryNumber=$dispatch_no");

        }
        elseif($approval['approval'] == 1)
        {}
    }

	display_note(get_gl_view_str(13, $dispatch_no, _("View the GL Journal Entries for this Dispatch")),1);

// 	if (!isset($_GET['prepaid']))
// 		hyperlink_params("$path_to_root/sales/customer_invoice.php", _("Invoice This Delivery"), "DeliveryNumber=$dispatch_no");

	hyperlink_params("$path_to_root/sales/inquiry/sales_orders_view.php", _("Select Another Order For Dispatch"), "OutstandingOnly=1");

	display_footer_exit();

} elseif (isset($_GET['UpdatedID'])) {

	$delivery_no = $_GET['UpdatedID'];

    $sql = "SELECT * 
			FROM ".TB_PREF."debtor_trans
			WHERE type = 13 
			AND trans_no =".db_escape($delivery_no);
    $result = db_query($sql, "Could not find dimension");
    $approval = db_fetch($result);

	display_notification_centered(sprintf(_('Delivery Note # %d has been updated.'),$delivery_no));

	display_note(get_trans_view_str(ST_CUSTDELIVERY, $delivery_no, _("View this delivery")), 0, 1);

// 	display_note(print_document_link($delivery_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
// 	display_note(print_document_link($delivery_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
// 	display_note(print_document_link($delivery_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
// 	display_note(print_document_link($delivery_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

// 	if (!isset($_GET['prepaid']))
// 		hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");

    global $SysPrefs;
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {
        display_note(print_document_link($delivery_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
        display_note(print_document_link($delivery_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
        display_note(print_document_link($delivery_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
        display_note(print_document_link($delivery_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

        if (!isset($_GET['prepaid']))
            hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");

    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {}

    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 0) {
        display_note(print_document_link($delivery_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
        display_note(print_document_link($delivery_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
        display_note(print_document_link($delivery_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
        display_note(print_document_link($delivery_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

        if (!isset($_GET['prepaid']))
            hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");

    }
    else {
        if($approval['approval'] == 0) {
            display_note(print_document_link($delivery_no, _("&Print Delivery Note"), true, ST_CUSTDELIVERY));
            display_note(print_document_link($delivery_no, _("&Email Delivery Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
            display_note(print_document_link($delivery_no, _("P&rint as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
            display_note(print_document_link($delivery_no, _("E&mail as Packing Slip"), true, ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

            if (!isset($_GET['prepaid']))
                hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");

        }
        elseif($approval['approval'] == 1)
        {}
    }

	hyperlink_params($path_to_root . "/sales/inquiry/sales_deliveries_view.php", _("Select A Different Delivery"), "OutstandingOnly=1");

	display_footer_exit();
}
//-----------------------------------------------------------------------------

if (isset($_GET['OrderNumber']) && $_GET['OrderNumber'] > 0) {
  
    
    $current_user = $_SESSION["wa_current_user"]->user;
    $location = check_user_location($current_user);
      $rows =  db_num_rows($location);
    if(!$rows )
    {
        display_error("No Location assigned/allowed, kindly ask your administrator to assign a location to you");
        display_footer_exit();
    }
	$ord = new Cart(ST_SALESORDER, $_GET['OrderNumber'], true);
	if ($ord->is_prepaid())
		check_deferred_income_act(_("You have to set Deferred Income Account in GL Setup to entry prepayment invoices."));

	if ($ord->count_items() == 0) {
		hyperlink_params($path_to_root . "/sales/inquiry/sales_orders_view.php",
			_("Select a different sales order to delivery"), "OutstandingOnly=1");
		echo "<br><center><b>" . _("This order has no items. There is nothing to delivery.") .
			"</center></b>";
		display_footer_exit();
	} else if (!$ord->is_released()) {
		hyperlink_params($path_to_root . "/sales/inquiry/sales_orders_view.php",_("Select a different sales order to delivery"),
			"OutstandingOnly=1");
		echo "<br><center><b>"._("This prepayment order is not yet ready for delivery due to insufficient amount received.")
			."</center></b>";
		display_footer_exit();
	}
 	// Adjust Shipping Charge based upon previous deliveries TAM
	adjust_shipping_charge($ord, $_GET['OrderNumber']);
 
	$_SESSION['Items'] = $ord;
	copy_from_cart();

} elseif (isset($_GET['ModifyDelivery']) && $_GET['ModifyDelivery'] > 0) {

	check_is_editable(ST_CUSTDELIVERY, $_GET['ModifyDelivery']);
	$_SESSION['Items'] = new Cart(ST_CUSTDELIVERY,$_GET['ModifyDelivery']);

	if (!$_SESSION['Items']->prepaid && $_SESSION['Items']->count_items() == 0) {
		hyperlink_params($path_to_root . "/sales/inquiry/sales_orders_view.php",
			_("Select a different delivery"), "OutstandingOnly=1");
		echo "<br><center><b>" . _("This delivery has all items invoiced. There is nothing to modify.") .
			"</center></b>";
		display_footer_exit();
	}

	copy_from_cart();
	
} elseif ( !processing_active() ) {
	/* This page can only be called with an order number for invoicing*/

	display_error(_("This page can only be opened if an order or delivery note has been selected. Please select it first."));

	hyperlink_params("$path_to_root/sales/inquiry/sales_orders_view.php", _("Select a Sales Order to Delivery"), "OutstandingOnly=1");

	end_page();
	exit;

} else {
	check_edit_conflicts(get_post('cart_id'));

	if (!check_quantities()) {
		display_error(_("Selected quantity cannot be less than quantity invoiced nor more than quantity	not dispatched on sales order."));

	} elseif(!check_num('ChargeFreightCost', 0)) {
		display_error(_("Freight cost cannot be less than zero"));
		set_focus('ChargeFreightCost');
	}elseif(!check_num('discount1', 0)) {
		display_error(_("Discount1 cannot be less than zero"));
		set_focus('discount1');
	}elseif(!check_num('discount2', 0)) {
		display_error(_("Discount2 cannot be less than zero"));
		set_focus('discount2');
	}
}

//-----------------------------------------------------------------------------

function check_data()
{
	global $Refs, $SysPrefs;

	$customer_record = get_customer_details($_SESSION['Items']->customer_id, $_POST['DispatchDate']);
	$inv_items_total = $_SESSION['Items']->get_items_total_dispatch();

	$display_sub_total = $inv_items_total + input_num('ChargeFreightCost');
	$total_amount = ($customer_record['Balance'] + $display_sub_total);

	if (!isset($_POST['DispatchDate']) || !is_date($_POST['DispatchDate']))	{
		display_error(_("The entered date of delivery is invalid."));
		set_focus('DispatchDate');
		return false;
	}
	
	
	/*
	$prefs = get_company_prefs();
	if($prefs['item_location'] == 1)
    {
        foreach ($_SESSION['Items']->line_items as $line=>$itm) {
            if (!get_post('item_location'.$line)) {
                display_error(_("There is no location selected."));
                set_focus('item_location');
                return false;
            }
        }
    }
    else{
        if (!get_post('Location'))
        {
            display_error(_("There is no location selected."));
            set_focus('location');
            return false;
        }
    }
    */
	
	

	if (!is_date_in_fiscalyear($_POST['DispatchDate'])) {
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('DispatchDate');
		return false;
	}

	if (!isset($_POST['due_date']) || !is_date($_POST['due_date']))	{
		display_error(_("The entered dead-line for invoice is invalid."));
		set_focus('due_date');
		return false;
	}

	if ($_SESSION['Items']->trans_no==0) {
		if (!$Refs->is_valid($_POST['ref'], ST_CUSTDELIVERY)) {
			display_error(_("You must enter a reference."));
			set_focus('ref');
			return false;
		}
	}

	if ($_POST['ChargeFreightCost'] == "") {
		$_POST['ChargeFreightCost'] = price_format(0);
	}	
	if ($_POST['discount1'] == "") {
		$_POST['discount1'] = price_format(0);
	}	
	if ($_POST['discount2'] == "") {
		$_POST['discount2'] = price_format(0);
	}

	if (!check_num('ChargeFreightCost',0)) {
		display_error(_("The entered shipping value is not numeric."));
		set_focus('ChargeFreightCost');
		return false;
	}
	if (!check_num('discount1',0)) {
		display_error(_("The entered discount1 value is not numeric."));
		set_focus('discount1');
		return false;
	}	
	if (!check_num('discount2',0)) {
		display_error(_("The entered discount2 value is not numeric."));
		set_focus('discount2');
		return false;
	}

	if ($_SESSION['Items']->has_items_dispatch() == 0 && input_num('ChargeFreightCost') == 0) {
		display_error(_("There are no item quantities on this delivery note."));
		return false;
	}

	if (!check_quantities()) {
		return false;
	}
	
	if ( $total_amount >  $customer_record['credit_allowed'] && $customer_record['credit_allowed'] != 0 ) {
		display_error(_("The Total Delivery amount "."(".number_format2($inv_items_total,$dec).")"." exceeds the credit allowed "."(".number_format2($customer_record['credit_allowed'],$dec).")" ));
		return false;
	}

	copy_to_cart();

	if (!$SysPrefs->allow_negative_stock() && ($low_stock = $_SESSION['Items']->check_qoh()))
	{
		display_error(_("This document cannot be processed because there is insufficient quantity for items marked.".$low_stock[0].",".$low_stock[1]));
		return false;
	}
	$row = get_company_pref('date');

	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['DispatchDate'], 'd');


		if($diff > $row  ){

			display_error("You are not allowed to enter data ");
			return false;
		}
		else
		{
			if($diff < 0 )
			{
				display_error("You are not allowed to enter data ");
				return false;
			}



		}
	}
	return true;
}
//------------------------------------------------------------------------------
function copy_to_cart()
{
	$cart = &$_SESSION['Items'];
	$cart->ship_via = $_POST['ship_via'];
	$cart->freight_cost = input_num('ChargeFreightCost');
	$cart->document_date = $_POST['DispatchDate'];
	$cart->due_date =  $_POST['due_date'];
	$cart->Location = $_POST['Location'];
	$cart->Comments = $_POST['Comments'];
// 	$cart->dimension_id = $_POST['dimension_id'];
// 	$cart->dimension2_id = $_POST['dimension2_id'];
	$cart->cust_ref = $_POST['cust_ref'];

	if ($cart->trans_no == 0)
		$cart->reference = $_POST['ref'];

	$cart->discount1 = input_num('discount1');
	$cart->discount2 = input_num('discount2');
	$cart->salesman = $_POST['salesman'];
	$cart->h_text2 = $_POST['h_text2'];
	

	

}
//------------------------------------------------------------------------------

function copy_from_cart()
{
	$cart = &$_SESSION['Items'];
	$_POST['ship_via'] = $cart->ship_via;
	$_POST['ChargeFreightCost'] = price_format($cart->freight_cost);
	$_POST['DispatchDate'] = $cart->document_date;
	$_POST['due_date'] = $cart->due_date;
	$_POST['Location'] = $cart->Location;
	$_POST['Comments'] = $cart->Comments;
// 	$_POST['dimension_id'] = $cart->dimension_id;
// 	$_POST['dimension2_id'] = $cart->dimension2_id;
	$_POST['cart_id'] = $cart->cart_id;
	$_POST['ref'] = $cart->reference;
	$_POST['cust_ref'] = $cart->cust_ref;
	$_POST['discount1'] = price_format($cart->discount1);
	$_POST['discount2'] = price_format($cart->discount2);
	$_POST['salesman'] = $cart->salesman;
	$_POST['h_text2'] = $cart->h_text2;
	
}
//------------------------------------------------------------------------------



function check_quantities()
{
	$ok =1;
	// Update cart delivery quantities/descriptions
	foreach ($_SESSION['Items']->line_items as $line=>$itm) {
		if (isset($_POST['Line'.$line])) {
			if($_SESSION['Items']->trans_no) {
				$min = $itm->qty_done;
				$max = $itm->quantity;
			} else {
				$min = 0;
				$max = $itm->quantity - $itm->qty_done;
			}

			if (check_num('Line'.$line, $min, $max)) {
				$_SESSION['Items']->line_items[$line]->qty_dispatched =
				  input_num('Line'.$line);

			} else {
				set_focus('Line'.$line);
				$ok = 0;
			}

		}

		if (isset($_POST['Line'.$line.'Desc'])) {
			$line_desc = $_POST['Line'.$line.'Desc'];
			if (strlen($line_desc) > 0) {
				$_SESSION['Items']->line_items[$line]->item_description = $line_desc;
			}
		}
		if($_GET['AUTODELIVERY'] != 1){
		$_SESSION['Items']->line_items[$line]->text1 = $_POST['text1'.$line];
		$_SESSION['Items']->line_items[$line]->text2 = $_POST['text2'.$line];
		$_SESSION['Items']->line_items[$line]->text3 = $_POST['text3'.$line];
		$_SESSION['Items']->line_items[$line]->text4 = $_POST['text4'.$line];
		$_SESSION['Items']->line_items[$line]->text5 = $_POST['text5'.$line];
		$_SESSION['Items']->line_items[$line]->text6 = $_POST['text6'.$line];
		$_SESSION['Items']->line_items[$line]->text7 = $_POST['text7'.$line];

		$_SESSION['Items']->line_items[$line]->amount1 = input_num('amount1'.$line);
		$_SESSION['Items']->line_items[$line]->amount2 = input_num('amount2'.$line);
		$_SESSION['Items']->line_items[$line]->amount3 = input_num('amount3'.$line);
		$_SESSION['Items']->line_items[$line]->amount4 = input_num('amount4'.$line);
		$_SESSION['Items']->line_items[$line]->amount5 = input_num('amount5'.$line);
		$_SESSION['Items']->line_items[$line]->amount6 = input_num('amount6'.$line);

		$_SESSION['Items']->line_items[$line]->date1 = $_POST['date1'.$line];
		$_SESSION['Items']->line_items[$line]->date2 = $_POST['date2'.$line];
		$_SESSION['Items']->line_items[$line]->date3 = $_POST['date3'.$line];

		$_SESSION['Items']->line_items[$line]->combo1 = $_POST['combo1'.$line];
		$_SESSION['Items']->line_items[$line]->combo2 = $_POST['combo2'.$line];
		$_SESSION['Items']->line_items[$line]->combo3 = $_POST['combo3'.$line];
		$_SESSION['Items']->line_items[$line]->combo4 = $_POST['combo4'.$line];
		$_SESSION['Items']->line_items[$line]->combo5 = $_POST['combo5'.$line];
		$_SESSION['Items']->line_items[$line]->combo6 = $_POST['combo6'.$line];
		
		
		$_SESSION['Items']->line_items[$line]->batch = $_POST['batch'.$line];
		$_SESSION['Items']->line_items[$line]->price = input_num('price'.$line);
		
		
//		$_SESSION['Items']->line_items[$line]->dimension_id = $_POST['dimension_id'.$line];
		$_SESSION['Items']->line_items[$line]->item_location = $_POST['item_location'.$line];
		
		$_SESSION['Items']->line_items[$line]->discount_percent = $_POST['Disc'.$line];
		}

	}
	return $ok;
}

//------------------------------------------------------------------------------

if ( (isset($_POST['process_delivery']) || $_GET['AUTODELIVERY'] == 1)  && check_data()) {
	$dn = &$_SESSION['Items'];

	if ($_POST['bo_policy']) {
		$bo_policy = 0;
	} else {
		$bo_policy = 1;
	}
	$newdelivery = ($dn->trans_no == 0);

	if ($newdelivery)
		new_doc_date($dn->document_date);

	$delivery_no = $dn->write($bo_policy);

	if ($delivery_no == -1)
	{
		display_error(_("The entered reference is already in use."));
		set_focus('ref');
	}
	else
	{
		$is_prepaid = $dn->is_prepaid() ? "&prepaid=Yes" : '';

		processing_end();
        if($_GET['AUTODELIVERY'] == 1){
            meta_forward($path_to_root."/sales/customer_invoice.php","DeliveryNumber=$delivery_no"."&&AUTOINVOICE=1");
        }
        elseif($newdelivery) {

			meta_forward($_SERVER['PHP_SELF'], "AddedID=$delivery_no$is_prepaid");
		} else {
			meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$delivery_no$is_prepaid");
		}
	}
}

if (isset($_POST['Update']) || isset($_POST['_Location_update']) || isset($_POST['qty']) || isset($_POST['process_delivery'])) {
	$Ajax->activate('Items');
}
//------------------------------------------------------------------------------
$text_field1 = get_company_item_pref_from_position(7);
$text_field2 = get_company_item_pref_from_position(8);


start_form();
hidden('cart_id');

start_table(TABLESTYLE2, "width='80%'", 5);
echo "<tr><td>"; // outer table

start_table(TABLESTYLE, "width='100%'");
start_row();
label_cells(_("Customer"), $_SESSION['Items']->customer_name, "class='tableheader2'");
label_cells(_("Branch"), get_branch_name($_SESSION['Items']->Branch), "class='tableheader2'");
label_cells(_("Currency"), $_SESSION['Items']->customer_currency, "class='tableheader2'");
end_row();
start_row();

if ($_SESSION['Items']->trans_no==0) {
	ref_cells(_("Reference"), 'ref', '', null, "class='tableheader2'", false, ST_CUSTDELIVERY,
	array('customer' => $_SESSION['Items']->customer_id,
			'branch' => $_SESSION['Items']->Branch,
			'date' => get_post('DispatchDate')));
} else {
	label_cells(_("Reference"), $_SESSION['Items']->reference, "class='tableheader2'");
}

if ($SysPrefs->show_doc_ref() == 0) {
    label_cells(_("For Sales Order"), get_customer_trans_view_str(ST_SALESORDER, $_SESSION['Items']->order_no), "class='tableheader2'");
}
else{
    label_cells(_("For Sales Order"), get_customer_trans_view_str(ST_SALESORDER,get_reference(ST_SALESORDER, $_SESSION['Items']->order_no) ), "class='tableheader2'");

}

label_cells(_("Sales Type"), $_SESSION['Items']->sales_type_name, "class='tableheader2'");
end_row();
start_row();

if (!isset($_POST['Location'])) {
	$_POST['Location'] = $_SESSION['Items']->Location;
}
$pref = get_company_prefs();
if($pref['item_location'] == 0) {
	label_cell(_("Delivery From"), "class='tableheader2'");
	locations_list_cells(null, 'Location', null, false, true);
}
if (!isset($_POST['ship_via'])) {
	$_POST['ship_via'] = $_SESSION['Items']->ship_via;
}
label_cell(_("Shipping Company"), "class='tableheader2'");
shippers_list_cells(null, 'ship_via', $_POST['ship_via']);

// set this up here cuz it's used to calc qoh
if (!isset($_POST['DispatchDate']) || !is_date($_POST['DispatchDate'])) {
	$_POST['DispatchDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['DispatchDate'])) {
		$_POST['DispatchDate'] = end_fiscalyear();
	}
}
date_cells(_("Date"), 'DispatchDate', '', $_SESSION['Items']->trans_no==0, 0, 0, 0, "class='tableheader2'");
$myrow234 = get_company_item_pref('sales_persons');

if($myrow234['sale_enable'] == 0){}
else {
    sales_persons_list_row(_("Sales Person:"), 'salesman', $_SESSION['Items']->salesman);
}


if($text_field1['sale_enable'])
{

        text_cells($text_field1['label_value'] , $text_field1['name'], null,20,50);
}
if($text_field2['sale_enable'])
{

        text_cells($text_field1['label_value'], $text_field1['name'], null,20,50);
}




end_row();

end_table();

echo "</td><td>";// outer table

start_table(TABLESTYLE, "width='90%'");
global $db_connections;

if (!isset($_POST['due_date']) || !is_date($_POST['due_date'])) {
	$_POST['due_date'] = get_invoice_duedate($_SESSION['Items']->payment, $_POST['DispatchDate']);
}
customer_credit_row($_SESSION['Items']->customer_id, $_SESSION['Items']->credit, "class='tableheader2'");


$dim = get_company_pref('use_dimension');
if ($dim > 0) {
	start_row();
	label_cell(_("Dimension").":", "class='tableheader2'");
	label_cell(get_dimension_name($_SESSION['Items']->dimension_id));
	end_row();
}
else
	hidden('dimension_id', 0);
if ($dim > 1) {
	start_row();
	label_cell(_("Dimension")." 2:", "class='tableheader2'");
    label_cell(get_dimension2_name($_SESSION['Items']->dimension2_id));
	end_row();
}
else
	hidden('dimension2_id', 0);
//---------
start_row();
date_cells(_("Invoice Dead-line"), 'due_date', '', null, 0, 0, 0, "class='tableheader2'");

label_row(_("Gate Pass"),"<a blank_
	href='$path_to_root/sales/manage/gate_pass.php?dimension_id=".$_SESSION['Items']->dimension_id."'"
	." onclick=\"javascript:openWindow(this.href,this.target); return false;\" > Gate Pass "
	."</a>");

text_row('Builty Number', 'h_text2', $_SESSION['Items']->h_text2, null,20,50);

end_row();
end_table();

echo "</td></tr>";
end_table(1); // outer table

$row = get_customer_to_order($_SESSION['Items']->customer_id);
if ($row['dissallow_invoices'] == 1)
{
	display_error(_("The selected customer account is currently on hold. Please contact the credit control personnel to discuss."));
	end_form();
	end_page();
	exit();
}	
display_heading(_("Delivery Items"));
div_start('Items');
start_table(TABLESTYLE, "width='80%'");

$new = $_SESSION['Items']->trans_no==0;

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
$prefs=get_company_prefs();
if($prefs['batch'] == 1)
$th = array(_("Item Code"), _("Item Description"), _("Batch"));
else
$th = array(_("Item Code"), _("Item Description"));

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
if($myrow_22['sale_enable']) {
	array_append($th, array($myrow_22['label_value']._("")) );
}
$pref = get_company_pref();

$myrow233 = get_company_item_pref('con_factor');


if ($SysPrefs->show_text_qty()== 0 ) {
    if($pref['bonus'] == 1){
        
        
        if($pref['item_location'] == 1) {
        if ($myrow233['sale_enable'] == 1) {
            $myrow_factor = $myrow233['label_value'];
            if ($pref['alt_uom'] == 1) {
                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),_("Bonus"), _("Units"), $myrow_factor, $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Location"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            } else {

                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"),/*_("Con.factor"),*/
                    $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"),_("Location"),  _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            }
        } else {

            if ($pref['alt_uom'] == 1) {
                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Location"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            } else {

                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"),/*_("Con.factor"),*/
                    $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Price"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            }
        }
    } else {
        if ($myrow233['sale_enable'] == 1) {
            $myrow_factor = $myrow233['label_value'];
            if ($pref['alt_uom'] == 1) {
                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"), $myrow_factor, $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            } else {

                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"),/*_("Con.factor"),*/
                    $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            }
        } else {

            if ($pref['alt_uom'] == 1) {
                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            } else {

                array_append($th, array(
                    $new ? _("Ordered") : _("Max. delivery"),("Bonus"), _("Units"),/*_("Con.factor"),*/
                    $new ? _("Delivered") : _("Invoiced"),
                    _("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
            }
        }
    }
        
        
    }
    else{
    
    
    
    
if($pref['item_location'] == 1) {
	if ($myrow233['sale_enable'] == 1) {
		$myrow_factor = $myrow233['label_value'];
		if ($pref['alt_uom'] == 1) {
			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"), $myrow_factor, $new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Location"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		} else {

			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/
				$new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"),_("Location"),  _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		}
	} else {

		if ($pref['alt_uom'] == 1) {
			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Location"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		} else {

			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/
				$new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Price"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		}
	}
    } else {
	if ($myrow233['sale_enable'] == 1) {
		$myrow_factor = $myrow233['label_value'];
		if ($pref['alt_uom'] == 1) {
			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"), $myrow_factor, $new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		} else {

			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/
				$new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		}
	} else {

		if ($pref['alt_uom'] == 1) {
			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		} else {

			array_append($th, array(
				$new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/
				$new ? _("Delivered") : _("Invoiced"),
				_("This Delivery"), _("Price"), _("Tax Type"), _("Discount %"), _("Total")));
		}
	}
    }
}
}


else
{

        if ($pref['item_location'] == 1) {
            if ($myrow233['sale_enable'] == 1) {
                $myrow_factor = $myrow233['label_value'];
                if ($pref['alt_uom'] == 1) {
                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),
                        _("This Delivery"), _("Location"), _("Price"), _("Discount"), _("Total")));
                } else {

                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/

                        _("This Delivery"), _("Location"), _("Price"), _("Discount"), _("Total")));
                }
            } else {

                if ($pref['alt_uom'] == 1) {
                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),
                        _("This Delivery"), _("Location"), _("Price"), _("Discount"), _("Total")));
                } else {

                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/

                        _("This Delivery"), _("Price"), _("Price"),  _("Discount"), _("Total")));
                }
            }
        } else {
            if ($myrow233['sale_enable'] == 1) {
                $myrow_factor = $myrow233['label_value'];
                if ($pref['alt_uom'] == 1) {
                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),
                        _("This Delivery"), _("Price"),  _("Discount"), _("Total")));
                } else {

                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/

                        _("This Delivery"), _("Price"), _("Discount"), _("Total")));
                }
            } else {

                if ($pref['alt_uom'] == 1) {
                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),
                        _("This Delivery"), _("Price"),  _("Discount"), _("Total"),
                        _("Cost"), _("Total Cost"), _("Margin")));
                } else {

                    array_append($th, array(
                        $new ? _("Ordered") : _("Max. delivery"), _("Units"),/*_("Con.factor"),*/
                        _("This Delivery"), _("Price"), _("Discount"), _("Total"),
                        _("Cost"), _("Total Cost"), _("Margin")));
                }
            }
        }

}
/*

if ($SysPrefs->show_prices_dn() == '') {
    $th = array(_("Item Code"), _("Item Description"),
		_("Text1"), _("Text2"), _("Text3"), _("Text4"),
		_("Amount1"), _("Amount2"), _("Amount3"), _("Amount4"),
		_("Date1"), _("Date2"), _("Date3"),
		_("Combo1"), _("Combo2"), _("Combo3"),
		$new ? _("Ordered") : _("Max. delivery"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
        _("This Delivery"));
}
else {
    $th = array(_("Item Code"), _("Item Description"),
		_("Text1"), _("Text2"), _("Text3"), _("Text4"),
		_("Amount1"), _("Amount2"), _("Amount3"), _("Amount4"),
		_("Date1"), _("Date2"), _("Date3"),
		_("Combo1"), _("Combo2"), _("Combo3"),
		$new ? _("Ordered") : _("Max. delivery"), _("Units"), $new ? _("Delivered") : _("Invoiced"),
        _("This Delivery"), _("Price"), _("Tax Type"), _("Discount"), _("Total"));
}*/

table_header($th);
$k = 0;
$has_marked = false;

foreach ($_SESSION['Items']->line_items as $line=>$ln_itm) {
	if ($ln_itm->quantity==$ln_itm->qty_done) {
		continue; //this line is fully delivered
	}
	if(isset($_POST['_Location_update']) || isset($_POST['clear_quantity']) || isset($_POST['reset_quantity'])) {
		// reset quantity
		$ln_itm->qty_dispatched = $ln_itm->quantity-$ln_itm->qty_done;
	}
	// if it's a non-stock item (eg. service) don't show qoh
	$row_classes = null;
	if (has_stock_holding($ln_itm->mb_flag) && $ln_itm->qty_dispatched) {
		// It's a stock : call get_dispatchable_quantity hook  to get which quantity to preset in the
		// quantity input box. This allows for example a hook to modify the default quantity to what's dispatchable
		// (if there is not enough in hand), check at other location or other order people etc ...
		// This hook also returns a 'reason' (css classes) which can be used to theme the row.
		//
		// FIXME: hook_get_dispatchable definition does not allow qoh checks on transaction level
		// (but anyway dispatch is checked again later before transaction is saved)
	$qty = $ln_itm->qty_dispatched;
$pref = get_company_prefs();
        if($pref['batch'] == 1) {
	
            if ($check = check_negative_stock($ln_itm->stock_id, $ln_itm->qty_done - $ln_itm->qty_dispatched, $_POST['Location'],
                $_POST['DispatchDate'], $ln_itm->batch))
                $qty = $check['qty'];
        }
        else{
            if ($check = check_negative_stock($ln_itm->stock_id, $ln_itm->qty_done - $ln_itm->qty_dispatched, $_POST['Location'],
                $_POST['DispatchDate']))
                $qty = $check['qty'];
        }
		
		if($pref['item_location'] == 0)
		$q_class =  hook_get_dispatchable_quantity($ln_itm, $_POST['Location'], $_POST['DispatchDate'], $qty);
		else
		$q_class =  hook_get_dispatchable_quantity($ln_itm, $_POST['item_location'], $_POST['DispatchDate'], $qty);

	  global  $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'BNT2'
            && $db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'BNC'  ) {
            // Skip line if needed
            if ($q_class === 'skip') continue;
            if (is_array($q_class)) {
                list($ln_itm->qty_dispatched, $row_classes) = $q_class;
                $has_marked = true;
            }
        }
	}
  if ($check['qty'] < 0) {
        start_row("class='stockmankobg'");
	    
	    // oops, we don't have enough of one of the component items
$has_marked = true;

    } else {
        alt_table_row_color($k);

    }
// 	alt_table_row_color($k);
	view_stock_status_cell($ln_itm->stock_id);

	if ($ln_itm->descr_editable)
		text_cells(null, 'Line'.$line.'Desc', $ln_itm->item_description, 30, 50);
	else
		label_cell($ln_itm->item_description);
	$prefs=get_company_prefs();
	if($prefs['batch'] == 1) {
		$batch=get_batch_name_by_id($ln_itm->batch);
		batch_list_cells(_(""), $ln_itm->stock_id, 'batch'.$line, $ln_itm->batch, false, false, true, true, $_POST['Location']);
//		label_cell($batch['name']);
	}

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

	if($myrow_1['sale_enable'])
	{
		if($myrow_1['type'] == 1)
		amount_cells(null, $myrow_1['name'].$line, $ln_itm->$myrow_1['name']);
		elseif($myrow_1['type'] == 2)
		combo1_list_cells(null, $myrow_1['name'].$line, $ln_itm->$myrow_1['name']);
		elseif($myrow_1['type'] == 3)
		{	$_POST[$myrow_1['name'].$line] = $ln_itm->$myrow_1['name'];
		date_cells(null, $myrow_1['name'].$line);}
		elseif($myrow_1['type'] == 4)
			text_cells(null, $myrow_1['name'].$line, $ln_itm->$myrow_1['name'], 20, 500);
	}
	if($myrow_2['sale_enable'])
	{
		if($myrow_2['type'] == 1)
			amount_cells(null, $myrow_2['name'].$line, $ln_itm->$myrow_2['name']);
		elseif($myrow_2['type'] == 2)
			combo1_list_cells(null, $myrow_2['name'].$line, $ln_itm->$myrow_2['name']);
		elseif($myrow_2['type'] == 3)
		{	$_POST[$myrow_2['name'].$line] = $ln_itm->$myrow_2['name'];
			date_cells(null, $myrow_2['name'].$line);}
		elseif($myrow_2['type'] == 4)
			text_cells(null, $myrow_2['name'].$line, $ln_itm->$myrow_2['name'], 20, 60);
	}
	if($myrow_3['sale_enable'])
	{
		if($myrow_3['type'] == 1)
			amount_cells(null, $myrow_3['name'].$line, $ln_itm->$myrow_3['name']);
		elseif($myrow_3['type'] == 2)
			combo1_list_cells(null, $myrow_3['name'].$line, $ln_itm->$myrow_3['name']);
		elseif($myrow_3['type'] == 3)
		{	$_POST[$myrow_3['name'].$line] = $ln_itm->$myrow_3['name'];
			date_cells(null, $myrow_3['name'].$line);}
		elseif($myrow_3['type'] == 4)
			text_cells(null, $myrow_3['name'].$line, $ln_itm->$myrow_3['name'], 20, 60);
	}
	if($myrow_4['sale_enable'])
	{
		if($myrow_4['type'] == 1)
			amount_cells(null, $myrow_4['name'].$line, $ln_itm->$myrow_4['name']);
		elseif($myrow_4['type'] == 2)
			combo1_list_cells(null, $myrow_4['name'].$line, $ln_itm->$myrow_4['name']);
		elseif($myrow_4['type'] == 3)
		{	$_POST[$myrow_4['name'].$line] = $ln_itm->$myrow_4['name'];
			date_cells(null, $myrow_4['name'].$line);}
		elseif($myrow_4['type'] == 4)
			text_cells(null, $myrow_4['name'].$line, $ln_itm->$myrow_4['name'], 20, 60);
	}
	if($myrow_5['sale_enable'])
	{
		if($myrow_5['type'] == 1)
			amount_cells(null, $myrow_5['name'].$line, $ln_itm->$myrow_5['name']);
		elseif($myrow_5['type'] == 2)
			combo1_list_cells(null, $myrow_5['name'].$line, $ln_itm->$myrow_5['name']);
		elseif($myrow_5['type'] == 3)
		{	$_POST[$myrow_5['name'].$line] = $ln_itm->$myrow_5['name'];
			date_cells(null, $myrow_5['name'].$line);}
		elseif($myrow_5['type'] == 4)
			text_cells(null, $myrow_5['name'].$line, $ln_itm->$myrow_5['name'], 20, 60);
	}
	if($myrow_6['sale_enable'])
	{
		if($myrow_6['type'] == 1)
			amount_cells(null, $myrow_6['name'].$line, $ln_itm->$myrow_6['name']);
		elseif($myrow_6['type'] == 2)
			combo1_list_cells(null, $myrow_6['name'].$line, $ln_itm->$myrow_6['name']);
		elseif($myrow_6['type'] == 3)
		{	$_POST[$myrow_6['name'].$line] = $ln_itm->$myrow_6['name'];
			date_cells(null, $myrow_6['name'].$line);}
		elseif($myrow_6['type'] == 4)
			text_cells(null, $myrow_6['name'].$line, $ln_itm->$myrow_6['name'], 20, 60);
	}
	if($myrow_7['sale_enable'])
	{
		if($myrow_7['type'] == 1)
			amount_cells(null, $myrow_7['name'].$line, $ln_itm->$myrow_7['name']);
		elseif($myrow_7['type'] == 2)
			combo1_list_cells(null, $myrow_7['name'].$line, $ln_itm->$myrow_7['name']);
		elseif($myrow_7['type'] == 3)
		{	$_POST[$myrow_7['name'].$line] = $ln_itm->$myrow_7['name'];
			date_cells(null, $myrow_7['name'].$line);}
		elseif($myrow_7['type'] == 4)
			text_cells(null, $myrow_7['name'].$line, $ln_itm->$myrow_7['name'], 20, 60);
	}
	if($myrow_8['sale_enable'])
	{
		if($myrow_8['type'] == 1)
			amount_cells(null, $myrow_8['name'].$line, $ln_itm->$myrow_8['name']);
		elseif($myrow_8['type'] == 2)
			combo1_list_cells(null, $myrow_8['name'].$line, $ln_itm->$myrow_8['name']);
		elseif($myrow_8['type'] == 3)
		{	$_POST[$myrow_8['name'].$line] = $ln_itm->$myrow_8['name'];
			date_cells(null, $myrow_8['name'].$line);}
		elseif($myrow_8['type'] == 4)
			text_cells(null, $myrow_8['name'].$line, $ln_itm->$myrow_8['name'], 20, 60);
	}
	if($myrow_9['sale_enable'])
	{
		if($myrow_9['type'] == 1)
			amount_cells(null, $myrow_9['name'].$line, $ln_itm->$myrow_9['name']);
		elseif($myrow_9['type'] == 2)
			combo1_list_cells(null, $myrow_9['name'].$line, $ln_itm->$myrow_9['name']);
		elseif($myrow_9['type'] == 3)
		{	$_POST[$myrow_9['name'].$line] = $ln_itm->$myrow_9['name'];
			date_cells(null, $myrow_9['name'].$line);}
		elseif($myrow_9['type'] == 4)
			text_cells(null, $myrow_9['name'].$line, $ln_itm->$myrow_9['name'], 20, 60);
	}
	if($myrow_10['sale_enable'])
	{
		if($myrow_10['type'] == 1)
			amount_cells(null, $myrow_10['name'].$line, $ln_itm->$myrow_10['name']);
		elseif($myrow_10['type'] == 2)
			combo1_list_cells(null, $myrow_10['name'].$line, $ln_itm->$myrow_10['name']);
		elseif($myrow_10['type'] == 3)
		{	$_POST[$myrow_10['name'].$line] = $ln_itm->$myrow_10['name'];
			date_cells(null, $myrow_10['name'].$line);}
		elseif($myrow_10['type'] == 4)
			text_cells(null, $myrow_10['name'].$line, $ln_itm->$myrow_10['name'], 20, 60);
	}
	if($myrow_11['sale_enable'])
	{
		if($myrow_11['type'] == 1)
			amount_cells(null, $myrow_11['name'].$line, $ln_itm->$myrow_11['name']);
		elseif($myrow_11['type'] == 2)
			combo1_list_cells(null, $myrow_11['name'].$line, $ln_itm->$myrow_11['name']);
		elseif($myrow_11['type'] == 3)
		{	$_POST[$myrow_11['name'].$line] = $ln_itm->$myrow_11['name'];
			date_cells(null, $myrow_11['name'].$line);}
		elseif($myrow_11['type'] == 4)
			text_cells(null, $myrow_11['name'].$line, $ln_itm->$myrow_11['name'], 20, 60);
	}
	if($myrow_12['sale_enable'])
	{
		if($myrow_12['type'] == 1)
			amount_cells(null, $myrow_12['name'].$line, $ln_itm->$myrow_12['name']);
		elseif($myrow_12['type'] == 2)
			combo1_list_cells(null, $myrow_12['name'].$line, $ln_itm->$myrow_12['name']);
		elseif($myrow_12['type'] == 3)
		{	$_POST[$myrow_12['name'].$line] = $ln_itm->$myrow_12['name'];
			date_cells(null, $myrow_12['name'].$line);}
		elseif($myrow_12['type'] == 4)
			text_cells(null, $myrow_12['name'].$line, $ln_itm->$myrow_12['name'], 20, 60);
	}
	if($myrow_13['sale_enable'])
	{
		if($myrow_13['type'] == 1)
			amount_cells(null, $myrow_13['name'].$line, $ln_itm->$myrow_13['name']);
		elseif($myrow_13['type'] == 2)
			combo1_list_cells(null, $myrow_13['name'].$line, $ln_itm->$myrow_13['name']);
		elseif($myrow_13['type'] == 3)
		{	$_POST[$myrow_13['name'].$line] = $ln_itm->$myrow_13['name'];
			date_cells(null, $myrow_13['name'].$line);}
		elseif($myrow_13['type'] == 4)
			text_cells(null, $myrow_13['name'].$line, $ln_itm->$myrow_13['name'], 20, 60);
	}
	if($myrow_14['sale_enable'])
	{
		if($myrow_14['type'] == 1)
			amount_cells(null, $myrow_14['name'].$line, $ln_itm->$myrow_14['name']);
		elseif($myrow_14['type'] == 2)
			combo1_list_cells(null, $myrow_14['name'].$line, $ln_itm->$myrow_14['name']);
		elseif($myrow_14['type'] == 3)
		{	$_POST[$myrow_14['name'].$line] = $ln_itm->$myrow_14['name'];
			date_cells(null, $myrow_14['name'].$line);}
		elseif($myrow_14['type'] == 4)
			text_cells(null, $myrow_14['name'].$line, $ln_itm->$myrow_14['name'], 20, 60);
	}
	if($myrow_15['sale_enable'])
	{
		if($myrow_15['type'] == 1)
			amount_cells(null, $myrow_15['name'].$line, $ln_itm->$myrow_15['name']);
		elseif($myrow_15['type'] == 2)
			combo1_list_cells(null, $myrow_15['name'].$line, $ln_itm->$myrow_15['name']);
		elseif($myrow_15['type'] == 3)
		{	$_POST[$myrow_15['name'].$line] = $ln_itm->$myrow_15['name'];
			date_cells(null, $myrow_15['name'].$line);}
		elseif($myrow_15['type'] == 4)
			text_cells(null, $myrow_15['name'].$line, $ln_itm->$myrow_15['name'], 20, 60);
	}
	if($myrow_16['sale_enable'])
	{
		if($myrow_16['type'] == 1)
			amount_cells(null, $myrow_16['name'].$line, $ln_itm->$myrow_16['name']);
		elseif($myrow_16['type'] == 2)
			combo1_list_cells(null, $myrow_16['name'].$line, $ln_itm->$myrow_16['name']);
		elseif($myrow_16['type'] == 3)
		{	$_POST[$myrow_16['name'].$line] = $ln_itm->$myrow_16['name'];
			date_cells(null, $myrow_16['name'].$line);}
		elseif($myrow_16['type'] == 4)
			text_cells(null, $myrow_16['name'].$line, $ln_itm->$myrow_16['name'], 20, 60);
	}
	if($myrow_17['sale_enable'])
	{
		if($myrow_17['type'] == 1)
			amount_cells(null, $myrow_17['name'].$line, $ln_itm->$myrow_17['name']);
		elseif($myrow_17['type'] == 2)
			combo1_list_cells(null, $myrow_17['name'].$line, $ln_itm->$myrow_17['name']);
		elseif($myrow_17['type'] == 3)
		{	$_POST[$myrow_17['name'].$line] = $ln_itm->$myrow_17['name'];
			date_cells(null, $myrow_17['name'].$line);}
		elseif($myrow_17['type'] == 4)
			text_cells(null, $myrow_17['name'].$line, $ln_itm->$myrow_17['name'], 20, 60);
	}
	if($myrow_18['sale_enable'])
	{
		if($myrow_18['type'] == 1)
			amount_cells(null, $myrow_18['name'].$line, $ln_itm->$myrow_18['name']);
		elseif($myrow_18['type'] == 2)
			combo1_list_cells(null, $myrow_18['name'].$line, $ln_itm->$myrow_18['name']);
		elseif($myrow_18['type'] == 3)
		{	$_POST[$myrow_18['name'].$line] = $ln_itm->$myrow_18['name'];
			date_cells(null, $myrow_18['name'].$line);}
		elseif($myrow_18['type'] == 4)
			text_cells(null, $myrow_18['name'].$line, $ln_itm->$myrow_18['name'], 20, 60);
	}
	if($myrow_19['sale_enable'])
	{
		if($myrow_19['type'] == 1)
			amount_cells(null, $myrow_19['name'].$line, $ln_itm->$myrow_19['name']);
		elseif($myrow_19['type'] == 2)
			combo1_list_cells(null, $myrow_19['name'].$line, $ln_itm->$myrow_19['name']);
		elseif($myrow_19['type'] == 3)
		{	$_POST[$myrow_19['name'].$line] = $ln_itm->$myrow_19['name'];
			date_cells(null, $myrow_19['name'].$line);}
		elseif($myrow_19['type'] == 4)
			text_cells(null, $myrow_19['name'].$line, $ln_itm->$myrow_19['name'], 20, 60);
	}
	if($myrow_20['sale_enable'])
	{
		if($myrow_20['type'] == 1)
			amount_cells(null, $myrow_20['name'].$line, $ln_itm->$myrow_20['name']);
		elseif($myrow_20['type'] == 2)
			combo1_list_cells(null, $myrow_20['name'].$line, $ln_itm->$myrow_20['name']);
		elseif($myrow_20['type'] == 3)
		{	$_POST[$myrow_20['name'].$line] = $ln_itm->$myrow_20['name'];
			date_cells(null, $myrow_20['name'].$line);}
		elseif($myrow_20['type'] == 4)
			text_cells(null, $myrow_20['name'].$line, $ln_itm->$myrow_20['name'], 20, 60);
	}
	if($myrow_21['sale_enable'])
	{
		if($myrow_21['type'] == 1)
			amount_cells(null, $myrow_21['name'].$line, $ln_itm->$myrow_21['name']);
		elseif($myrow_21['type'] == 2)
			combo1_list_cells(null, $myrow_21['name'].$line, $ln_itm->$myrow_21['name']);
		elseif($myrow_21['type'] == 3)
		{	$_POST[$myrow_21['name'].$line] = $ln_itm->$myrow_21['name'];
			date_cells(null, $myrow_21['name'].$line);}
		elseif($myrow_21['type'] == 4)
			text_cells(null, $myrow_21['name'].$line, $ln_itm->$myrow_21['name'], 20, 60);
	}
	if($myrow_22['sale_enable'])
	{
//		if($myrow_22['type'] == 1)
//			amount_cells(null, $myrow_22['name'].$line, $ln_itm->$myrow_22['name']);
//		elseif($myrow_22['type'] == 2)
//			combo1_list_cells(null, $myrow_22['name'].$line, $ln_itm->$myrow_22['name']);
//		elseif($myrow_22['type'] == 3)
//		{	$_POST[$myrow_22['name'].$line] = $ln_itm->$myrow_22['name'];
//			date_cells(null, $myrow_22['name'].$line);}
		if($myrow_22['type'] == 4)
			textarea_cells(null, $myrow_22['name'].$line, $ln_itm->$myrow_22['name'], 20, 3);
	}


	$dec = get_qty_dec($ln_itm->stock_id);
	qty_cell($ln_itm->quantity, false, $dec);

    if($pref['bonus'] == 1)
        label_cell($ln_itm->bonus);

	if($pref['alt_uom'] == 1) {
		label_cell($ln_itm->units_id);


		$myrow233 = get_company_item_pref('con_factor');
		if($myrow233['sale_enable'] == 0){}
		else {
			qty_cell($ln_itm->$myrow233['name']);
		}
		$myrow_factors= get_company_item_pref('con_factor');
		hidden($myrow_factors['name'],$ln_itm->con_factor);

		//qty_cell($ln_itm->con_factor, false, $dec);
	}
	else {
		label_cell($ln_itm->units);
	}
	$myrow_factorss= get_company_item_pref('con_factor');
	hidden($myrow_factorss['name'],$ln_itm->con_factor);
    if ($SysPrefs->show_text_qty() == 0) {
	qty_cell($ln_itm->qty_done, false, $dec);
		hidden($ln_itm->qty_done);
    } else {
    }
	if(isset($_POST['clear_quantity'])) {
		$ln_itm->qty_dispatched = 0;
	}
	if ($SysPrefs->show_text_qty() == 1) {
		$_POST['Line' . $line] = round2($ln_itm->qty_dispatched, $dec); /// clear post so value displayed in the fiel is the 'new' quantity
	}
	small_qty_cells(null, 'Line'.$line, qty_format($ln_itm->qty_dispatched, $ln_itm->stock_id, $dec), null, null, $dec);
	if( $pref['item_location'] == 1 )
	locations_list_cells(null, 'item_location'.$line,$ln_itm->item_location);

		$display_discount_percent = percent_format($ln_itm->discount_percent * 100);



    if ($SysPrefs->show_text_qty() == 0) {

	amount_cell($ln_itm->price);
//		hidden($ln_itm->price. $line);
		hidden('price' . $line, $ln_itm->price);
    }
	else {

        $_POST['price' . $line] = $ln_itm->price;
        amount_cells(null, 'price' . $line);
    }
    	$line_total = ( $ln_itm->price );

 	$line_total = ($ln_itm->qty_dispatched * $ln_itm->price * (1 - $ln_itm->discount_percent));
    $line_total_sum += $line_total;
    if ($SysPrefs->show_text_qty() == 0) {
	label_cell($ln_itm->tax_type_name);
    } else {
    }
    if ($SysPrefs->show_text_qty() == 0) {
	label_cell($display_discount_percent, "nowrap align=right");
//		hidden($display_discount_percent. $line);
		hidden('Disc'.$line, $ln_itm->discount_percent);
    } else {
		$_POST['Disc' . $line] = $ln_itm->discount_percent;
        small_amount_cells(null, 'Disc' . $line, percent_format($_POST['Disc']), null, null, user_percent_dec());
    }
	amount_cell($line_total);

    $last_purch_price = get_last_purch_price($ln_itm->stock_id);

    $cost_total = $ln_itm->quantity * $last_purch_price;

    $margin_total = $line_total - $cost_total;

    if ($SysPrefs->show_text_qty() == 1)
    {
        label_cell($ln_itm->last_purch_price);
        amount_cell($cost_total);
        amount_cell($margin_total);
    }
	end_row();
}

$_POST['ChargeFreightCost'] =  get_post('ChargeFreightCost', 
	price_format($_SESSION['Items']->freight_cost));

$colspan = 15;

start_row();
if ($SysPrefs->show_text_qty() == 1)
{
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    label_cell(_("Total: ") . " " . $line_total_sum, "colspan=$colspan align=left");
    echo "</td>";
    echo "</td>";
    echo "</td>";
    echo "</td>";
    echo "</td>";
    echo "</td>";
    echo "</td>";
}

//if ($SysPrefs->show_prices_dn() == '')
$AllowLineDiscount = get_company_pref('discount_algorithm');
if ($AllowLineDiscount != 7)
{
    hidden('discount1', $_SESSION['Items']->discount1);
	hidden('discount2', $_SESSION['Items']->discount2);
	hidden('ChargeFreightCost', $_SESSION['Items']->freight_cost);
}
else
{
    echo "<tr>";
    label_cell(_("Shipping Cost"), "colspan=$colspan align=right");
    small_amount_cells(null, 'ChargeFreightCost', $_SESSION['Items']->freight_cost);
    echo "<tr>";
    label_cell(_("Discount% 1 :"), "colspan=$colspan align=right");
    small_amount_cells(null, 'discount1', $_SESSION['Items']->discount1);
    echo "<tr>";
    label_cell(_("Discount% 2 :"), "colspan=$colspan align=right");
    small_amount_cells(null, 'discount2', $_SESSION['Items']->discount2);
    label_cell('', 'colspan=2');
    end_row();
    $TotalDiscount = input_num('discount1') + input_num('discount2');
    $inv_items_total = $_SESSION['Items']->get_items_total_dispatch();
    $display_sub_total = price_format($inv_items_total + input_num('ChargeFreightCost') - $TotalDiscount);
    label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right");
    $taxes = $_SESSION['Items']->get_taxes(input_num('ChargeFreightCost'));
    $tax_total = display_edit_tax_items($taxes, $colspan, $_SESSION['Items']->tax_included);
    $display_total = price_format(($inv_items_total + input_num('ChargeFreightCost') + $tax_total - $TotalDiscount));
    label_row(_("Amount Total"), $display_total, "colspan=$colspan align=right","align=right");
}
end_table(1);

if ($has_marked) {
	display_note(_("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='stockmankofg'");
}
start_table(TABLESTYLE2);
if ($SysPrefs->show_prices_dn() == '')
{
    policy_list_row(_("Action For Balance"), "bo_policy", null);
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='NKR' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CHI' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='FIDA')
    textarea_row(_("Remarks"), 'Comments', null, 50, 4);
    else
        textarea_row(_("Memo"), 'Comments', null, 50, 4);
}
else {
policy_list_row(_("Action For Balance"), "bo_policy", null);
text_row(_("Customer Reference:"), 'cust_ref',$_SESSION['Items']->cust_ref, 25, 25,
	_('Customer reference number for this order (if any)'));
textarea_row(_("Memo"), 'Comments', null, 50, 4);
$customer_record = get_customer_details($_SESSION['Items']->customer_id, $_POST['DispatchDate']);
	label_row(_("Closing Balance"),number_format2($customer_record['Balance'],$dec),"align=left");
	label_row(_("Credit Allowed"),number_format2($customer_record['credit_allowed'],$dec), "align=left");
}
end_table(1);
div_end();
submit_center_first('Update', _("Update"),
	_('Refresh document page'), true);
if(isset($_POST['clear_quantity'])) {
	submit('reset_quantity', _('Reset quantity'), true, _('Refresh document page'));
}
else  {
	submit('clear_quantity', _('Clear quantity'), true, _('Refresh document page'));
}
submit_center_last('process_delivery', _("Process Dispatch"),
	_('Check entered data and save document'), 'default');

end_form();


end_page();
