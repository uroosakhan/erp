<?php
//-----------------------------------------------------------------------------
//
//	Entry/Modify Delivery Note against Sales Order
//
$page_security = 'SA_SALESDELIVERY';
$path_to_root = "..";

include_once($path_to_root . "/POS/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/manufacturing.inc");
include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/POS/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

$js = "";
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}

if (isset($_GET['OrderNumber'])) {
	$_SESSION['page_title'] = _($help_context = "Order Completion Note");
	processing_start();
}

page($_SESSION['page_title'], false, false, "", $js);

if (isset($_GET['AddedID'])) {
	$dispatch_no = $_GET['AddedID'];

	display_notification_centered(sprintf(_("Order # %d has been entered."),$dispatch_no));

	display_note(get_customer_trans_view_str(ST_CUSTDELIVERY, $dispatch_no, _("&View This Delivery")), 0, 1);

	display_note(print_document_link($dispatch_no, _("&Print Order Completion Note"), true, ST_CUSTDELIVERY));
	display_note(print_document_link($dispatch_no, _("&Email Order Completion Note"), true, ST_CUSTDELIVERY, false, "printlink", "", 1), 1, 1);
//	display_note(print_document_link($dispatch_no, _("P&rint as Packing Slip"), true, //ST_CUSTDELIVERY, false, "printlink", "", 0, 1));
//	display_note(print_document_link($dispatch_no, _("E&mail as Packing Slip"), true, //ST_CUSTDELIVERY, false, "printlink", "", 1, 1), 1);

	display_note(get_gl_view_str(13, $dispatch_no, _("View the GL Journal Entries for this Dispatch")),1);

	hyperlink_params("$path_to_root/POS/customer_invoice.php", _("Invoice This Order Completion"), "DeliveryNumber=$dispatch_no");

	hyperlink_params("$path_to_root/POS/inquiry/sales_orders_view.php", _("Select Another Order Completion"), "OutstandingOnly=1");

	display_footer_exit();

}
//-----------------------------------------------------------------------------

if (isset($_GET['OrderNumber']) && $_GET['OrderNumber'] > 0) {

	$ord = new Cart(ST_SALESORDER, $_GET['OrderNumber'], true);
//
	//header("http://localhost/ddhaba/ddhaba/sales/manage/pre_orders_tables.php?NewOrder=Yes");


	//if ($ord->count_items() == 0) {
	//	hyperlink_params($path_to_root . "/sales/inquiry/sales_orders_view.php",
		//	_("Select a different sales order to asdadadasda"), "OutstandingOnly=1");

//	header("http://localhost/ddhaba/ddhaba/POS/manage/orders_tables.php?");

		//die ("<br><b>" . _("This order has no items. There is nothing to delivery.") . "</b>");
//	}

 	// Adjust Shipping Charge based upon previous deliveries TAM
	adjust_shipping_charge_pos($ord, $_GET['OrderNumber']);
 
	$_SESSION['Items'] = $ord;
	
	//asad
	if ($_POST['bo_policy']) {
		$bo_policy = 0;
	} else {
		$bo_policy = 1;
	}
	$newdelivery = ($_SESSION['Items']->trans_no == 0);




$time=date('h:i:s');
$time1=('03:00:00');
$time2=('00:00:00');


if($time2 <= $time  && $time1 >= $time){
$date= get_current_company_date(3);
}
else{
   $date; 
    
}


// 	$date = get_current_company_date(get_company_pref('shop_hrs'));
	if ($newdelivery) new_doc_date($date);
	$delivery_no = $_SESSION['Items']->write($bo_policy);
	if ($delivery_no == -1)
	{
		display_error(_("The entered reference is already in use."));
		set_focus('ref');
	}
	else
	{
		processing_end();
		if ($newdelivery) {
			meta_forward($path_to_root."/POS/cust_invoice.php", "DeliveryNumber=$delivery_no");
		} 
	}	


	//asad
} elseif ( !processing_active() ) {
	/* This page can only be called with an order number for invoicing*/

	display_error(_("This page can only be opened if an order or delivery note has been selected. Please select it first."));

	hyperlink_params("$path_to_root/POS/inquiry/sales_orders_view.php", _("Select a Sales Order to Delivery"), "OutstandingOnly=1");

	end_page();
	exit;

} else {
	check_edit_conflicts();

	if (!check_quantities()) {
		display_error(_("Selected quantity cannot be less than quantity invoiced nor more than quantity	not dispatched on sales order."));

	} /*elseif(!check_num('ChargeFreightCost', 0)) {
		display_error(_("Freight cost cannot be less than zero"));
		set_focus('ChargeFreightCost');
	}*/
}

//-----------------------------------------------------------------------------
//------------------------------------------------------------------------------
//------------------------------------------------------------------------------
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
	}
// ...
//	else
//	  $_SESSION['Items']->freight_cost = input_num('ChargeFreightCost');
	return $ok;
}
//------------------------------------------------------------------------------


$row = get_customer_to_order_pos($_SESSION['Items']->customer_id);
if ($row['dissallow_invoices'] == 1)
{
	display_error(_("The selected customer account is currently on hold. Please contact the credit control personnel to discuss."));
	end_form();
	end_page();
	exit();
}	

display_heading(_("Job Details"));
div_start('Items');
start_table(TABLESTYLE, "width=80%");

$new = $_SESSION['Items']->trans_no==0;
$th = array(_("Code"), _("Head of Accounts"),   _("Dimension"),  _("Dimension 2"), 
	$new ? _("Qty") : _("Max. delivery"), _("Units"), $new ? _("Done") : _("Invoiced"),
	_("This Note"), _("Amount"), _("Tax Type"), _("Discount"), _("Total"));

table_header($th);
$k = 0;
$has_marked = false;

foreach ($_SESSION['Items']->line_items as $line=>$ln_itm) {
	if ($ln_itm->quantity==$ln_itm->qty_done) {
		continue; //this line is fully delivered
	}
	// if it's a non-stock item (eg. service) don't show qoh
	$show_qoh = true;
	if ($SysPrefs->allow_negative_stock() || !has_stock_holding($ln_itm->mb_flag) ||
		$ln_itm->qty_dispatched == 0) {
		$show_qoh = false;
	}

	if ($show_qoh) {
		$qoh = get_qoh_on_date($ln_itm->stock_id, $_POST['Location'], $_POST['DispatchDate']);
	}

	if ($show_qoh && ($ln_itm->qty_dispatched > $qoh)) {
		// oops, we don't have enough of one of the component items
		start_row("class='stockmankobg'");
		$has_marked = true;
	} else {
		alt_table_row_color($k);
	}
	view_stock_status_cell($ln_itm->stock_id);

	if ($ln_itm->descr_editable)
		text_cells(null, 'Line'.$line.'Desc', $ln_itm->item_description, 30, 50);
	else
		label_cell($ln_itm->item_description);

	$dec = get_qty_dec($ln_itm->stock_id);
	
	label_cell(get_dimension_string($ln_itm->dim)); //asad
	label_cell(get_dimension_string($ln_itm->dim2)); //asad

	qty_cell($ln_itm->quantity, false, $dec);
	label_cell($ln_itm->units);
	qty_cell($ln_itm->qty_done, false, $dec);

	small_qty_cells(null, 'Line'.$line, qty_format($ln_itm->qty_dispatched, $ln_itm->stock_id, $dec), null, null, $dec);

	$display_discount_percent = percent_format($ln_itm->discount_percent*100) . "%";

	$line_total = ($ln_itm->qty_dispatched * $ln_itm->price * (1 - $ln_itm->discount_percent));

	amount_cell($ln_itm->price);
	label_cell($ln_itm->tax_type_name);
	label_cell($display_discount_percent, "nowrap align=right");
	amount_cell($line_total);

	end_row();
}

$_POST['ChargeFreightCost'] =  get_post('ChargeFreightCost', 
	price_format($_SESSION['Items']->freight_cost));

$colspan = 11;

start_row();
label_cell(_("Shipping Cost"), "colspan=$colspan align=right");
small_amount_cells(null, 'ChargeFreightCost', $_SESSION['Items']->freight_cost);
end_row();

$inv_items_total = $_SESSION['Items']->get_items_total_dispatch();

$display_sub_total = price_format($inv_items_total + input_num('ChargeFreightCost'));

label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right");

$taxes = $_SESSION['Items']->get_taxes(input_num('ChargeFreightCost'));
$tax_total = display_edit_tax_items($taxes, $colspan, $_SESSION['Items']->tax_included);

$display_total = price_format(($inv_items_total + input_num('ChargeFreightCost') + $tax_total));

label_row(_("Amount Total"), $display_total, "colspan=$colspan align=right","align=right");

end_table(1);

if ($has_marked) {
	display_note(_("Marked items have insufficient quantities in stock as on day of delivery."), 0, 1, "class='red'");
}
start_table(TABLESTYLE2);

policy_list_row(_("Action For Balance"), "bo_policy", null);

textarea_row(_("Memo"), 'Comments', null, 50, 4);

end_table(1);
div_end();
submit_center_first('Update', _("Update"),
  _('Refresh document page'), true);
submit_center_last('process_delivery', _("Process Order Completion"),
  _('Check entered data and save document'), 'default');

end_form();


end_page();

?>
