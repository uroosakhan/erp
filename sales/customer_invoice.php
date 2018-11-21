<?php

//---------------------------------------------------------------------------
//
//	Entry/Modify Sales Invoice against single delivery
//	Entry/Modify Batch Sales Invoice against batch of deliveries
//
$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");
include_once($path_to_root . "/admin/db/shipping_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if (user_use_date_picker()) {
	$js .= get_js_date_picker();
}

if (isset($_GET['ModifyInvoice'])) {
	$_SESSION['page_title'] = sprintf(_("Modifying Sales Invoice # %d.") ,$_GET['ModifyInvoice']);
	$help_context = "Modifying Sales Invoice";
} elseif (isset($_GET['DeliveryNumber'])) {
	$_SESSION['page_title'] = _($help_context = "Issue an Invoice for Delivery Note");
} elseif (isset($_GET['BatchInvoice'])) {
	$_SESSION['page_title'] = _($help_context = "Issue Batch Invoice for Delivery Notes");
} elseif (isset($_GET['AllocationNumber']) || isset($_GET['InvoicePrepayments'])) {
	$_SESSION['page_title'] = _($help_context = "Prepayment or Final Invoice Entry");
}
page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------

check_edit_conflicts(get_post('cart_id'));

if (isset($_GET['AddedID'])) {

	$invoice_no = $_GET['AddedID'];
	$trans_type = ST_SALESINVOICE;

	display_notification(_("Selected deliveries has been processed"), true);
	display_note(get_customer_trans_view_str($trans_type, $invoice_no, _("&View This Invoice")), 0, 1);
	display_note(print_document_link($invoice_no."-".$trans_type, _("&Print This Invoice"), true, ST_SALESINVOICE));
	display_note(print_document_link($invoice_no."-".$trans_type, _("&Email This Invoice"), true, ST_SALESINVOICE, false, "printlink", "", 1),1);

	display_note(get_gl_view_str($trans_type, $invoice_no, _("View the GL &Journal Entries for this Invoice")),1);

	hyperlink_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", _("Select Another &Delivery For Invoicing"), "OutstandingOnly=1");

	if (!db_num_rows(get_allocatable_from_cust_transactions(null, $invoice_no, $trans_type)))
		hyperlink_params("$path_to_root/sales/customer_payments.php", _("Entry &customer payment for this invoice"),
		"SInvoice=".$invoice_no);

	hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$invoice_no");

	display_footer_exit();

} elseif (isset($_GET['UpdatedID']))  {

	$invoice_no = $_GET['UpdatedID'];
	$trans_type = ST_SALESINVOICE;

	display_notification_centered(sprintf(_('Sales Invoice # %d has been updated.'),$invoice_no));

	display_note(get_trans_view_str(ST_SALESINVOICE, $invoice_no, _("&View This Invoice")));
	echo '<br>';
	display_note(print_document_link($invoice_no."-".$trans_type, _("&Print This Invoice"), true, ST_SALESINVOICE));
	display_note(print_document_link($invoice_no."-".$trans_type, _("&Email This Invoice"), true, ST_SALESINVOICE, false, "printlink", "", 1),1);

	hyperlink_no_params($path_to_root . "/sales/inquiry/customer_inquiry.php", _("Select Another &Invoice to Modify"));

	display_footer_exit();

} elseif (isset($_GET['RemoveDN'])) {

	for($line_no = 0; $line_no < count($_SESSION['Items']->line_items); $line_no++) {
		$line = &$_SESSION['Items']->line_items[$line_no];
		if ($line->src_no == $_GET['RemoveDN']) {
			$line->quantity = $line->qty_done;
			$line->qty_dispatched=0;
		}
	}
	unset($line);

    // Remove also src_doc delivery note
    $sources = &$_SESSION['Items']->src_docs;
    unset($sources[$_GET['RemoveDN']]);
}

//-----------------------------------------------------------------------------

if ( (isset($_GET['DeliveryNumber']) && ($_GET['DeliveryNumber'] > 0) )
	|| isset($_GET['BatchInvoice'])) {

	processing_start();

	if (isset($_GET['BatchInvoice'])) {
		$src = $_SESSION['DeliveryBatch'];
		unset($_SESSION['DeliveryBatch']);
	} else {
		$src = array($_GET['DeliveryNumber']);
	}

	/*read in all the selected deliveries into the Items cart  */
	$dn = new Cart(ST_CUSTDELIVERY, $src, true);

	if ($dn->count_items() == 0) {
		hyperlink_params($path_to_root . "/sales/inquiry/sales_deliveries_view.php",
			_("Select a different delivery to invoice"), "OutstandingOnly=1");
		die ("<br><b>" . _("There are no delivered items with a quantity left to invoice. There is nothing left to invoice.") . "</b>");
	}

	$_SESSION['Items'] = $dn;
	copy_from_cart();

} elseif (isset($_GET['ModifyInvoice']) && $_GET['ModifyInvoice'] > 0) {

	check_is_editable(ST_SALESINVOICE, $_GET['ModifyInvoice']);

	processing_start();
	$_SESSION['Items'] = new Cart(ST_SALESINVOICE, $_GET['ModifyInvoice']);

	if ($_SESSION['Items']->count_items() == 0) {
		echo"<center><br><b>" . _("All quantities on this invoice has been credited. There is nothing to modify on this invoice") . "</b></center>";
		display_footer_exit();
	}
	copy_from_cart();
} elseif (isset($_GET['AllocationNumber']) || isset($_GET['InvoicePrepayments'])) {

	check_deferred_income_act(_("You have to set Deferred Income Account in GL Setup to entry prepayment invoices."));

	if (isset($_GET['AllocationNumber']))
	{
		$payments = array(get_cust_allocation($_GET['AllocationNumber']));

		if (!$payments || ($payments[0]['trans_type_to'] != ST_SALESORDER))
		{
			display_error(_("Please select correct Sales Order Prepayment to be invoiced and try again."));
			display_footer_exit();
		}
		$order_no = $payments[0]['trans_no_to'];
	}
	else {
		$order_no = $_GET['InvoicePrepayments'];
		//ansar 26-08-17
		//$payments =  get_payments_for($_GET['InvoicePrepayments'], ST_SALESORDER);
	}
	processing_start();

	$_SESSION['Items'] = new Cart(ST_SALESORDER, $order_no, ST_SALESINVOICE);
	$_SESSION['Items']->order_no = $order_no;
	$_SESSION['Items']->src_docs = array($order_no);
	$_SESSION['Items']->trans_no = 0;
	$_SESSION['Items']->trans_type = ST_SALESINVOICE;
    //ansar 26-08-17
	//$_SESSION['Items']->prepayments = $payments;
	$_SESSION['Items']->update_payments();

	copy_from_cart();
}
elseif (!processing_active()) {
	/* This page can only be called with a delivery for invoicing or invoice no for edit */
	display_error(_("This page can only be opened after delivery selection. Please select delivery to invoicing first."));

	hyperlink_no_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", _("Select Delivery to Invoice"));

	end_page();
	exit;
} elseif (!isset($_POST['process_invoice']) && (!$_SESSION['Items']->is_prepaid() && !check_quantities())) {
	display_error(_("Selected quantity cannot be less than quantity credited nor more than quantity not invoiced yet."));
}

if (isset($_POST['Update'])) {
	$Ajax->activate('Items');
}
if (isset($_POST['_InvoiceDate_changed'])) {
	$_POST['due_date'] = get_invoice_duedate($_SESSION['Items']->payment, $_POST['InvoiceDate']);
	$Ajax->activate('due_date');
}

//-----------------------------------------------------------------------------
function check_quantities()
{
	$ok =1;
	foreach ($_SESSION['Items']->line_items as $line_no=>$itm) {
		if (isset($_POST['Line'.$line_no])) {
			if($_SESSION['Items']->trans_no) {
				$min = $itm->qty_done;
				$max = $itm->quantity;
			} else {
				$min = 0;
				$max = $itm->quantity - $itm->qty_done;
			}
			if (check_num('Line'.$line_no, $min, $max)) {
				$_SESSION['Items']->line_items[$line_no]->qty_dispatched =
				    input_num('Line'.$line_no);
			}
			else {
				$ok = 0;
			}
				
		}

		if (isset($_POST['Line'.$line_no.'Desc'])) {
			$line_desc = $_POST['Line'.$line_no.'Desc'];
			if (strlen($line_desc) > 0) {
				$_SESSION['Items']->line_items[$line_no]->item_description = $line_desc;
			}
		}
				$_SESSION['Items']->line_items[$line_no]->text1 = $_POST['text1'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text2 = $_POST['text2'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text3 = $_POST['text3'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text4 = $_POST['text4'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text5 = $_POST['text5'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text6 = $_POST['text6'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->text7 = $_POST['text7'.$line_no];

		$_SESSION['Items']->line_items[$line_no]->amount1 = input_num('amount1'.$line_no);
		$_SESSION['Items']->line_items[$line_no]->amount2 = input_num('amount2'.$line_no);
		$_SESSION['Items']->line_items[$line_no]->amount3 = input_num('amount3'.$line_no);
		$_SESSION['Items']->line_items[$line_no]->amount4 = input_num('amount4'.$line_no);
		$_SESSION['Items']->line_items[$line_no]->amount5 = input_num('amount5'.$line_no);
		$_SESSION['Items']->line_items[$line_no]->amount6 = input_num('amount6'.$line_no);

		$_SESSION['Items']->line_items[$line_no]->date1 = $_POST['date1'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->date2 = $_POST['date2'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->date3 = $_POST['date3'.$line_no];

		$_SESSION['Items']->line_items[$line_no]->combo1 = $_POST['combo1'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->combo2 = $_POST['combo2'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->combo3 = $_POST['combo3'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->combo4 = $_POST['combo4'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->combo5 = $_POST['combo5'.$line_no];
		$_SESSION['Items']->line_items[$line_no]->combo6 = $_POST['combo6'.$line_no];
	}
 return $ok;
}

function set_delivery_shipping_sum($delivery_notes) 
{
    
    $shipping = 0;
    
    foreach($delivery_notes as $delivery_num) 
    {
        $myrow = get_customer_trans($delivery_num, ST_CUSTDELIVERY);

        $shipping += $myrow['ov_freight'];
    }
    $_POST['ChargeFreightCost'] = price_format($shipping);
}


function copy_to_cart()
{
	$cart = &$_SESSION['Items'];
	$cart->due_date = $cart->document_date =  $_POST['InvoiceDate'];
	$cart->Comments = $_POST['Comments'];
	$cart->due_date =  $_POST['due_date'];
	$cart->cheque_date =  $_POST['cheque_date'];
	$cart->text1 =  $_POST['text1'];
	if (($cart->pos['cash_sale'] || $cart->pos['credit_sale']) && isset($_POST['payment'])) {
		$cart->payment = $_POST['payment'];
		$cart->payment_terms = get_payment_terms($_POST['payment']);
	}
	if ($_SESSION['Items']->trans_no == 0)
		$cart->reference = $_POST['ref'];
	if (!$cart->is_prepaid())
	{
		$cart->ship_via = $_POST['ship_via'];
		$cart->freight_cost = input_num('ChargeFreightCost');
	}

	$cart->update_payments();

	$cart->dimension_id =  $_POST['dimension_id'];
	$cart->dimension2_id =  $_POST['dimension2_id'];
	$cart->cust_ref = $_POST['cust_ref'];

	$cart->discount1 = input_num('discount1');
	$cart->discount2 = input_num('discount2');
	$cart->salesman = $_POST['salesman'];

}
//-----------------------------------------------------------------------------

function copy_from_cart()
{
	$cart = &$_SESSION['Items'];
 	$_POST['Comments']= $cart->Comments;
	$_POST['InvoiceDate']= $cart->document_date;
 	$_POST['ref'] = $cart->reference;
	$_POST['cart_id'] = $cart->cart_id;
	$_POST['due_date'] = $cart->due_date;
	$_POST['cheque_date'] = $cart->cheque_date;
	$_POST['text1'] = $cart->text1;
 	$_POST['payment'] = $cart->payment;
	if (!$_SESSION['Items']->is_prepaid())
	{
		$_POST['ship_via'] = $cart->ship_via;
		$_POST['ChargeFreightCost'] = price_format($cart->freight_cost);
	}
	$_POST['dimension_id'] = $cart->dimension_id;
	$_POST['dimension2_id'] = $cart->dimension2_id;
	$_POST['cust_ref'] = $cart->cust_ref;
	$_POST['discount1'] = price_format($cart->discount1);
	$_POST['discount2'] = price_format($cart->discount2);
	$_POST['salesman'] = $cart->salesman;
}

//-----------------------------------------------------------------------------

function check_data()
{
	global $Refs;

	$prepaid = $_SESSION['Items']->is_prepaid();

	if (!isset($_POST['InvoiceDate']) || !is_date($_POST['InvoiceDate'])) {
		display_error(_("The entered invoice date is invalid."));
		set_focus('InvoiceDate');
		return false;
	}

	if (!is_date_in_fiscalyear($_POST['InvoiceDate'])) {
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('InvoiceDate');
		return false;
	}


	if (!$prepaid &&(!isset($_POST['due_date']) || !is_date($_POST['due_date'])))	{
		display_error(_("The entered invoice due date is invalid."));
		set_focus('due_date');
		return false;
	}

	if ($_SESSION['Items']->trans_no == 0) {
		if (!$Refs->is_valid($_POST['ref'], ST_SALESINVOICE)) {
			display_error(_("You must enter a reference."));
			set_focus('ref');
			return false;
		}
	}

	if(!$prepaid) 
	{
		if ($_POST['ChargeFreightCost'] == "") {
			$_POST['ChargeFreightCost'] = price_format(0);
		}
		if ($_POST['discount1'] == "") {
			$_POST['discount1'] = price_format(0);
		}
		if ($_POST['discount2'] == "") {
			$_POST['discount2'] = price_format(0);
		}

// 		if (!check_num('ChargeFreightCost', 0)) {
// 			display_error(_("The entered shipping value is not numeric."));
// 			set_focus('ChargeFreightCost');
// 			return false;
// 		}
		if (!check_num('discount1', 0)) {
			display_error(_("The entered shipping value is not numeric."));
			set_focus('discount1');
			return false;
		}
		if (!check_num('discount2', 0)) {
			display_error(_("The entered shipping value is not numeric."));
			set_focus('discount2');
			return false;
		}

		if ($_SESSION['Items']->has_items_dispatch() == 0 && input_num('ChargeFreightCost') == 0) {
			display_error(_("There are no item quantities on this invoice."));
			return false;
		}

		if (!check_quantities()) {
			display_error(_("Selected quantity cannot be less than quantity credited nor more than quantity not invoiced yet1. $itm->quantity"));
			return false;
		}
	} else {//ansar 26-08-17
		if (($_SESSION['Items']->payment_terms['days_before_due'] ==-1) && !count($_SESSION['Items']->prepayments)) {
			display_error(_("There is no non-invoiced payments for this order. If you want to issue final invoice, select delayed or cash payment terms."));
			return false;
		}
	}

	return true;
}

//-----------------------------------------------------------------------------
if ((isset($_POST['process_invoice']) || $_GET['AUTOINVOICE'] == 1 ) && check_data()) {
	$newinvoice=  $_SESSION['Items']->trans_no == 0;
	copy_to_cart();
	if ($newinvoice) 
		new_doc_date($_SESSION['Items']->document_date);

	$invoice_no = $_SESSION['Items']->write();
	if ($invoice_no == -1)
	{
		display_error(_("The entered reference is already in use."));
		set_focus('ref');
	}
	else
	{
		processing_end();

		if ($newinvoice) {
			meta_forward($_SERVER['PHP_SELF'], "AddedID=$invoice_no");
		} else {
			meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$invoice_no");
		}
	}	
}

if(list_updated('payment')) {
	$order = &$_SESSION['Items']; 
	copy_to_cart();
	$order->payment = get_post('payment');
	$order->payment_terms = get_payment_terms($order->payment);
	$_POST['due_date'] = $order->due_date = get_invoice_duedate($order->payment, $order->document_date);
	$_POST['Comments'] = '';
	$Ajax->activate('due_date');
	$Ajax->activate('options');
	if ($order->payment_terms['cash_sale']) {
		$_POST['Location'] = $order->Location = $order->pos['pos_location'];
		$order->location_name = $order->pos['location_name'];
	}
}

// find delivery spans for batch invoice display
$dspans = array();
$lastdn = ''; $spanlen=1;

for ($line_no = 0; $line_no < count($_SESSION['Items']->line_items); $line_no++) {
	$line = $_SESSION['Items']->line_items[$line_no];
	if ($line->quantity == $line->qty_done) {
		continue;
	}
	if ($line->src_no == $lastdn) {
		$spanlen++;
	} else {
		if ($lastdn != '') {
			$dspans[] = $spanlen;
			$spanlen = 1;
		}
	}
	$lastdn = $line->src_no;
}
$dspans[] = $spanlen;

//-----------------------------------------------------------------------------

$is_batch_invoice = count($_SESSION['Items']->src_docs) > 1;
$prepaid = $_SESSION['Items']->is_prepaid();

$is_edition = $_SESSION['Items']->trans_type == ST_SALESINVOICE && $_SESSION['Items']->trans_no != 0;
start_form();
hidden('cart_id');

start_table(TABLESTYLE2, "width='80%'", 5);

start_row();
$colspan = 1;
$dim = get_company_pref('use_dimension');
if ($dim > 0) 
	$colspan = 3;
label_cells(_("Customer"), $_SESSION['Items']->customer_name, "class='tableheader2'");
label_cells(_("Branch"), get_branch_name($_SESSION['Items']->Branch), "class='tableheader2'");
if (($_SESSION['Items']->pos['credit_sale'] || $_SESSION['Items']->pos['cash_sale'])) {
	$paymcat = !$_SESSION['Items']->pos['cash_sale'] ? PM_CREDIT :
		(!$_SESSION['Items']->pos['credit_sale'] ? PM_CASH : PM_ANY);
	label_cells(_("Payment terms:"), sale_payment_list('payment', $paymcat),
		"class='tableheader2'", "colspan=$colspan");
} else
	label_cells(_('Payment:'), $_SESSION['Items']->payment_terms['terms'], "class='tableheader2'", "colspan=$colspan");

end_row();
start_row();

if ($_SESSION['Items']->trans_no == 0) {
	ref_cells(_("Reference"), 'ref', '', null, "class='tableheader2'", false, ST_SALESINVOICE,
		array('customer' => $_SESSION['Items']->customer_id,
			'branch' => $_SESSION['Items']->Branch,
			'date' => get_post('InvoiceDate')));
} else {
	label_cells(_("Reference"), $_SESSION['Items']->reference, "class='tableheader2'");
}

label_cells(_("Sales Type"), $_SESSION['Items']->sales_type_name, "class='tableheader2'");

label_cells(_("Currency"), $_SESSION['Items']->customer_currency, "class='tableheader2'");
if ($dim > 0) {
	label_cell(_("Dimension").":", "class='tableheader2'");
	$_POST['dimension_id'] = $_SESSION['Items']->dimension_id;
	dimensions_list_cells(null, 'dimension_id', null, true, ' ', false, 1, false);
}		
else
	hidden('dimension_id', 0);

end_row();
start_row();

if (!isset($_POST['ship_via'])) {
	$_POST['ship_via'] = $_SESSION['Items']->ship_via;
}
label_cell(_("Shipping Company"), "class='tableheader2'");
if ($prepaid)
{
	$shipper = get_shipper($_SESSION['Items']->ship_via);
	label_cells(null, $shipper['shipper_name']);
} else
	shippers_list_cells(null, 'ship_via', $_POST['ship_via']);

if (!isset($_POST['InvoiceDate']) || !is_date($_POST['InvoiceDate'])) {
	$_POST['InvoiceDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['InvoiceDate'])) {
		$_POST['InvoiceDate'] = end_fiscalyear();
	}
}

date_cells(_("Date"), 'InvoiceDate', '', $_SESSION['Items']->trans_no == 0, 
	0, 0, 0, "class='tableheader2'", true);

if (!isset($_POST['due_date']) || !is_date($_POST['due_date'])) {
	$_POST['due_date'] = get_invoice_duedate($_SESSION['Items']->payment, $_POST['InvoiceDate']);
}

date_cells(_("Due Date"), 'due_date', '', null, 0, 0, 0, "class='tableheader2'");
global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SHAMIM')
{
    date_cells(_("Bill T Date"), 'cheque_date', '', null, 0, 0, 0, "class='tableheader2'");
}

if ($dim > 1) {
	label_cell(_("Dimension")." 2:", "class='tableheader2'");
	$_POST['dimension2_id'] = $_SESSION['Items']->dimension2_id;
	dimensions_list_cells(null, 'dimension2_id', null, true, ' ', false, 2, false);
}		
else
	hidden('dimension2_id', 0);
$myrow234 = get_company_item_pref('sales_persons');

if($myrow234['sale_enable'] == 0){}
else {
    sales_persons_list_row(_("Sales Person:"), 'salesman', $_SESSION['Items']->salesman);
}

if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SHAMIM')
{
    text_row(_("Bill T No:"), 'text1', null, 10, 40);
}

end_row();
end_table();

$row = get_customer_to_order($_SESSION['Items']->customer_id);
if ($row['dissallow_invoices'] == 1)
{
	display_error(_("The selected customer account is currently on hold. Please contact the credit control personnel to discuss."));
	end_form();
	end_page();
	exit();
}	

display_heading($prepaid ? _("Sales Order Items") : _("Invoice Items"));

div_start('Items');

start_table(TABLESTYLE, "width='80%'");

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

if ($pref['bonus'] == 1) {

    if ($myrow233['sale_enable'] == 1) {
        $myrow_factor = $myrow233['label_value'];

        if ($pref['batch'] == 1) {
            if ($pref['alt_uom'] == 1) {
                array_append($th, array(_("Delivered"),_("Bonus"), _("Batch"), _("Exp.Date"),
                    _("Units"), $myrow_factor, _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
            } else {
                array_append($th, array(_("Delivered"),_("Bonus"), _("Batch"), _("Exp.Date"),
                    _("Units"),/*_("Con.factor"),*/
                    _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

            }
        } else {

            if ($pref['alt_uom'] == 1) {
                array_append($th, array(_("Delivered"),_("Bonus"),
                    _("Units"), $myrow_factor, _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
            } else {
                array_append($th, array(_("Delivered"),_("Bonus"),
                    _("Units"),/*_("Con.factor"),*/
                    _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

            }
        }
    } else {

        if ($pref['batch'] == 1) {
            if ($pref['alt_uom'] == 1) {
                array_append($th, array(_("Delivered"),_("Bonus"), _("Batch"), _("Exp.Date"),
                    _("Units"), _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
            } else {
                array_append($th, array(_("Delivered"),_("Bonus"), _("Batch"), _("Exp.Date"),
                    _("Units"),/*_("Con.factor"),*/
                    _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

            }
        } else {

            if ($pref['alt_uom'] == 1) {
                array_append($th, array(_("Delivered"),_("Bonus"),
                    _("Units"), _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
            } else {
                array_append($th, array(_("Delivered"),_("Bonus"),
                    _("Units"),/*_("Con.factor"),*/
                    _("Invoice"), _("This Invoice"),
                    _("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

            }
        }
    }

}
else{
if ($myrow233['sale_enable'] == 1) {
	$myrow_factor = $myrow233['label_value'];

	if ($pref['batch'] == 1) {
		if ($pref['alt_uom'] == 1) {
			array_append($th, array(_("Delivered"), _("Batch"), _("Exp.Date"),
				_("Units"), $myrow_factor, _("Invoice"), _("This Invoice"),
				_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
		} else {
			array_append($th, array(_("Delivered"), _("Batch"), _("Exp.Date"),
				_("Units"),/*_("Con.factor"),*/
				_("Invoice"), _("This Invoice"),
				_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

		}
	} else {

		if ($pref['alt_uom'] == 1) {
			array_append($th, array(_("Delivered"),
                    _("Units"), $myrow_factor, _("Invoice"), _("This Invoice"),
				_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
		} else {
			array_append($th, array(_("Delivered"),
				_("Units"),/*_("Con.factor"),*/
				_("Invoice"), _("This Invoice"),
				_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

		}
	}
    } else {

        if ($pref['batch'] == 1) {
			if ($pref['alt_uom'] == 1) {
                array_append($th, array(_("Delivered"), _("Batch"), _("Exp.Date"),
					_("Units"), _("Invoice"), _("This Invoice"),
					_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
			} else {
                array_append($th, array(_("Delivered"), _("Batch"), _("Exp.Date"),
					_("Units"),/*_("Con.factor"),*/
					_("Invoice"), _("This Invoice"),
					_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

			}
        } else {

			if ($pref['alt_uom'] == 1) {
				array_append($th, array(_("Delivered"),
                    _("Units"), _("Invoice"), _("This Invoice"),
					_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));
			} else {
				array_append($th, array(_("Delivered"),
					_("Units"),/*_("Con.factor"),*/
					_("Invoice"), _("This Invoice"),
					_("Price"), _("Tax Type"), _("Discount %"), _("Total"), ("")));

            }
        }
	}
}
if ($is_batch_invoice) {
    $th[] = _("DN");
    $th[] = "";
}

if ($is_edition) {
    $th[4] = _("Credited");
}


table_header($th);
$k = 0;
$has_marked = false;
$show_qoh = true;

$dn_line_cnt = 0;

foreach ($_SESSION['Items']->line_items as $line=>$ln_itm) {
	
	if (!$prepaid && ($ln_itm->quantity == $ln_itm->qty_done)) 
	{
		continue; // this line was fully invoiced
	}
	alt_table_row_color($k);
	view_stock_status_cell($ln_itm->stock_id);

	if ($prepaid)
		label_cell($ln_itm->item_description);
	else
		text_cells(null, 'Line'.$line.'Desc', $ln_itm->item_description, 30, 50);

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
			text_cells(null, $myrow_1['name'].$line, $ln_itm->$myrow_1['name'], 20, 60);
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
    $prefs=get_company_prefs();
	$dec = get_qty_dec($ln_itm->stock_id);
	if (!$prepaid)
		qty_cell($ln_itm->quantity, false, $dec);

    if($prefs['bonus'] == 1)
        label_cell($ln_itm->bonus);



	if($prefs['batch'] == 1) {
		$batch = get_batch_by_id($ln_itm->batch);
		label_cell($batch['name']);
		label_cell(sql2date($batch['exp_date']));
	}

	if($pref['alt_uom'] == 1) {
		label_cell($ln_itm->units_id);
		//$myrow_factors= get_company_item_pref('con_factor');


		$myrow233 = get_company_item_pref('con_factor');
		if($myrow233['sale_enable'] == 0){}
		else {
			qty_cell($ln_itm->$myrow233['name']);
		}
		//$myrow_factorss= get_company_item_pref('con_factor');
		hidden($myrow233['name'],$ln_itm->con_factor);

	//	qty_cell($ln_itm->con_factor, false, $dec);

	}
	else{
		label_cell($ln_itm->units);
	}

	$myrow_factorss= get_company_item_pref('con_factor');
	hidden($myrow_factorss['name'],$ln_itm->con_factor);
	if (!$prepaid)
		qty_cell($ln_itm->qty_done, false, $dec);

	if ($is_batch_invoice || $prepaid) {
		// for batch invoices we can only remove whole deliveries
		echo '<td nowrap align=right>';
		hidden('Line' . $line, $ln_itm->qty_dispatched );
		echo number_format2($ln_itm->qty_dispatched, $dec).'</td>';
	} else {
		 global $SysPrefs;
        if ($SysPrefs->edit_qty() == 0) {
            small_qty_cells(null, 'Line' . $line, qty_format($ln_itm->qty_dispatched, $ln_itm->stock_id, $dec), null, null, $dec);
        } else {
            qty_cell($ln_itm->qty_dispatched, false, $dec);
        }
	}
	$display_discount_percent = percent_format($ln_itm->discount_percent*100) ;

	$line_total = ($ln_itm->qty_dispatched * $ln_itm->price * (1 - $ln_itm->discount_percent));

	amount_cell($ln_itm->price);
	label_cell($ln_itm->tax_type_name);
	label_cell($display_discount_percent, "nowrap align=right");
	amount_cell($line_total);

	if ($is_batch_invoice) {
		if ($dn_line_cnt == 0) {
			$dn_line_cnt = $dspans[0];
			$dspans = array_slice($dspans, 1);
			label_cell($ln_itm->src_no, "rowspan=$dn_line_cnt class='oddrow'");
			label_cell("<a href='" . $_SERVER['PHP_SELF'] . "?RemoveDN=".
				$ln_itm->src_no."'>" . _("Remove") . "</a>", "rowspan=$dn_line_cnt class='oddrow'");
		}
		$dn_line_cnt--;
	}
	end_row();
}

/*Don't re-calculate freight if some of the order has already been delivered -
depending on the business logic required this condition may not be required.
It seems unfair to charge the customer twice for freight if the order
was not fully delivered the first time ?? */

if (!isset($_POST['ChargeFreightCost']) || $_POST['ChargeFreightCost'] == "") {
	if ($_SESSION['Items']->any_already_delivered() == 1) {
		$_POST['ChargeFreightCost'] = price_format(0);
	} else {
		$_POST['ChargeFreightCost'] = price_format($_SESSION['Items']->freight_cost);
	}

	if (!check_num('ChargeFreightCost')) {
		$_POST['ChargeFreightCost'] = price_format(0);
	}
	if (!check_num('discount1')) {
		$_POST['discount1'] = price_format(0);
	}
	if (!check_num('discount2')) {
		$_POST['discount2'] = price_format(0);
	}
}

$accumulate_shipping = get_company_pref('accumulate_shipping');
if ($is_batch_invoice && $accumulate_shipping)
	set_delivery_shipping_sum(array_keys($_SESSION['Items']->src_docs));

$colspan = $prepaid ? 21 : 23;
start_row();
label_cell(_("Shipping Cost"), "colspan=$colspan align=right");
if ($prepaid)
	label_cell($_POST['ChargeFreightCost'], 'align=right');
else
	small_amount_cells(null, 'ChargeFreightCost', null);
if ($is_batch_invoice) {
label_cell('', 'colspan=2');
}

end_row();
$TotalDiscount = $_SESSION['View']->discount1 + $_SESSION['View']->discount2;

$inv_items_total = $_SESSION['Items']->get_items_total_dispatch();

$display_sub_total = price_format($inv_items_total + input_num('ChargeFreightCost') - $TotalDiscount);


echo "<tr>";
label_cell(_("Discount% 1 :"), "colspan=$colspan align=right");
small_amount_cells(null, 'discount1', $_SESSION['Items']->discount1);
//$discount_amount1 = /*$total*input_num('discount1')/100*/input_num('discount1');
echo "<tr>";
label_cell(_("Discount% 2 :"), "colspan=$colspan align=right");
small_amount_cells(null, 'discount2', $_SESSION['Items']->discount2);


label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right", $is_batch_invoice ? 2 : 0);

$taxes = $_SESSION['Items']->get_taxes(input_num('ChargeFreightCost'));
$tax_total = display_edit_tax_items($taxes, $colspan, $_SESSION['Items']->tax_included, $is_batch_invoice ? 2 : 0);

$display_total = price_format(($inv_items_total + input_num('ChargeFreightCost') + $tax_total - $TotalDiscount));

label_row(_("Invoice Total"), $display_total, "colspan=$colspan align=right","align=right", $is_batch_invoice ? 2 : 0);

end_table(1);
div_end();
div_start('options');
start_table(TABLESTYLE2);
if ($prepaid)
{

	label_row(_("Sales order:"), get_trans_view_str(ST_SALESORDER, $_SESSION['Items']->order_no, get_reference(ST_SALESORDER, $_SESSION['Items']->order_no)));

	$list = array(); $allocs = 0;
	if (count($_SESSION['Items']->prepayments))
	{
		foreach($_SESSION['Items']->prepayments as $pmt)
		{
			$list[] = get_trans_view_str($pmt['trans_type_from'], $pmt['trans_no_from'], get_reference($pmt['trans_type_from'], $pmt['trans_no_from']));
			$allocs += $pmt['amt'];
		}
	}

	label_row(_("Payments received:"), implode(',', $list));
	label_row(_("Invoiced here:"), price_format($_SESSION['Items']->prep_amount), 'class=label');
	label_row(_("Left to be invoiced:"), price_format($_SESSION['Items']->get_trans_total()-max($_SESSION['Items']->prep_amount, $allocs)), 'class=label');
}
text_row(_("Customer Reference:"), 'cust_ref',$_SESSION['Items']->cust_ref, 25, 25,
	_('Customer reference number for this order (if any)'));
textarea_row(_("Memo:"), 'Comments', null, 50, 4);

end_table(1);
div_end();
submit_center_first('Update', _("Update"),
  _('Refresh document page'), true);
submit_center_last('process_invoice', _("Process Invoice"),
  _('Check entered data and save document'), 'default');

end_form();

end_page();

