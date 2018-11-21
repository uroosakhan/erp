<?php

//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "..";
$page_security = 'SA_SALESORDER';

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]!='LAKHANIGLASS')
{
    include_once($path_to_root . "/sales/includes/ui/sales_order_ui.inc");
}
else
{
    include_once($path_to_root . "/sales/includes/ui/sales_order_ui_lakhani.inc");
}
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_groups_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

set_page_security( @$_SESSION['Items']->trans_type,
	array(	ST_SALESORDER=>'SA_SALESORDER',
			ST_SALESQUOTE => 'SA_SALESQUOTE',
			ST_CUSTDELIVERY => 'SA_SALESDELIVERY',
			ST_SALESINVOICE => 'SA_SALESINVOICE'),
	array(	'NewOrder' => 'SA_SALESORDER',
			'ModifyOrderNumber' => 'SA_SALESORDER',
			'AddedID' => 'SA_SALESORDER',
			'UpdatedID' => 'SA_SALESORDER',
			'NewQuotation' => 'SA_SALESQUOTE',
			'ModifyQuotationNumber' => 'SA_SALESQUOTE',
			'NewQuoteToSalesOrder' => 'SA_SALESQUOTE',
			'AddedQU' => 'SA_SALESQUOTE',
			'UpdatedQU' => 'SA_SALESQUOTE',
			'NewDelivery' => 'SA_SALESDELIVERY',
			'AddedDN' => 'SA_SALESDELIVERY', 
			'NewInvoice' => 'SA_SALESINVOICE',
			'AddedDI' => 'SA_SALESINVOICE'
			)
);

$js = '';

if ($SysPrefs->use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}

if (user_use_date_picker()) {
	$js .= get_js_date_picker();
}

if (isset($_GET['NewDelivery']) && is_numeric($_GET['NewDelivery'])) {

	$_SESSION['page_title'] = _($help_context = "Direct Sales Delivery");
	create_cart(ST_CUSTDELIVERY, $_GET['NewDelivery']);

} elseif (isset($_GET['NewInvoice']) && is_numeric($_GET['NewInvoice'])) {

	create_cart(ST_SALESINVOICE, $_GET['NewInvoice']);

	if (isset($_GET['FixedAsset'])) {
		$_SESSION['page_title'] = _($help_context = "Fixed Assets Sale");
		$_SESSION['Items']->fixed_asset = true;
  	} else
		$_SESSION['page_title'] = _($help_context = "Direct Sales Invoice");

} elseif (isset($_GET['ModifyOrderNumber']) && is_numeric($_GET['ModifyOrderNumber'])) {

	$help_context = 'Modifying Sales Order';
	$_SESSION['page_title'] = sprintf( _("Modifying Sales Order # %d"), $_GET['ModifyOrderNumber']);
	create_cart(ST_SALESORDER, $_GET['ModifyOrderNumber']);

} elseif (isset($_GET['ModifyQuotationNumber']) && is_numeric($_GET['ModifyQuotationNumber'])) {

	$help_context = 'Modifying Sales Quotation';
	$_SESSION['page_title'] = sprintf( _("Modifying Sales Quotation # %d"), $_GET['ModifyQuotationNumber']);
	create_cart(ST_SALESQUOTE, $_GET['ModifyQuotationNumber']);

} elseif (isset($_GET['NewOrder'])) {

	$_SESSION['page_title'] = _($help_context = "New Sales Order Entry");
	create_cart(ST_SALESORDER, 0);
} elseif (isset($_GET['NewQuotation'])) {

	$_SESSION['page_title'] = _($help_context = "New Sales Quotation Entry");
	create_cart(ST_SALESQUOTE, 0);
} elseif (isset($_GET['NewQuoteToSalesOrder'])) {
	$_SESSION['page_title'] = _($help_context = "Sales Order Entry");
	create_cart(ST_SALESQUOTE, $_GET['NewQuoteToSalesOrder']);
}

page($_SESSION['page_title'], false, false, "", $js);
//ansar 26-08-2017
if (isset($_GET['ModifyOrderNumber']) && is_prepaid_order_open($_GET['ModifyOrderNumber']))
{
    display_error(_("This order cannot be edited because there are invoices or payments related to it, and prepayment terms were used."));
    end_page(); exit;
}
if (isset($_GET['ModifyOrderNumber']))
	check_is_editable(ST_SALESORDER, $_GET['ModifyOrderNumber']);
elseif (isset($_GET['ModifyQuotationNumber']))
	check_is_editable(ST_SALESQUOTE, $_GET['ModifyQuotationNumber']);

//-----------------------------------------------------------------------------

if (list_updated('branch_id')) {
	// when branch is selected via external editor also customer can change
	$br = get_branch(get_post('branch_id'));
	$_POST['customer_id'] = $br['debtor_no'];
	$Ajax->activate('customer_id');
}

if (isset($_GET['AddedID'])) {
	$order_no = $_GET['AddedID'];
	   // $order_no = $_GET['AddedID'];
    $sql = "SELECT * 
			FROM ".TB_PREF."sales_orders
			WHERE trans_type = 30 
			AND order_no =".db_escape($order_no);
			 $result = db_query($sql, "Could not find dimension");
    $approval = db_fetch($result);
	display_notification_centered(sprintf( _("Order # %d has been entered."),$order_no));

	submenu_view(_("&View This Order"), ST_SALESORDER, $order_no);

    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {
        submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
        	submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
	set_focus('prtopt');
    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {}

    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0) {
      $so =  get_sales_order_header($_GET['AddedID'],30);
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'
            || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT') {

            if($so['h_combo2'] == 2  ) {
                submenu_print(_("&Print This Order"), 10921, $order_no, 'prtopt');
            }
            else{
                submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
            }

        }
        else{
            submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
        }

        	submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
	set_focus('prtopt');
    }
     else {
	   if($approval['approval'] == 0) {
            submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
             submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
            set_focus('prtopt');
        }
        elseif($approval['approval'] == 1)
        {}
	}
	
// 	submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
// 	set_focus('prtopt');
	global $SysPrefs;

    // if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1) {

    //     submenu_option(_("Make &Delivery Against This Order"),
    //         "/sales/customer_delivery.php?OrderNumber=$order_no");
    // }
   if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {
    submenu_option(_("Make &Delivery Against This Order"),
            "/sales/customer_delivery.php?OrderNumber=$order_no");
    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {}

    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0) {
        submenu_option(_("Make &Delivery Against This Order"),
            "/sales/customer_delivery.php?OrderNumber=$order_no");
    }

    else
    {
        submenu_option(_("Make &Delivery Against This Order"),
            "/sales/customer_delivery.php?OrderNumber=$order_no");
    }

	submenu_option(_("Work &Order Entry"),	"/manufacturing/work_order_entry.php?");

	submenu_option(_("Enter a &New Order"),	"/sales/sales_order_entry.php?NewOrder=0");

	display_footer_exit();

} elseif (isset($_GET['UpdatedID'])) {
$order_no = $_GET['UpdatedID'];
    $sql = "SELECT * 
			FROM ".TB_PREF."sales_orders
			WHERE trans_type = 30 
			AND order_no =".db_escape($order_no);
    $result = db_query($sql, "Could not find dimension");
    $approval = db_fetch($result);

	display_notification_centered(sprintf( _("Order # %d has been updated."),$order_no));

	submenu_view(_("&View This Order"), ST_SALESORDER, $order_no);

    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {
        if($approval['approval'] == 0) {
	submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
	 submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
            set_focus('prtopt');
        }
        elseif($approval['approval'] == 1)
        {}

    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {}
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0) {
       
       
       
       
       
       
       
     $so =  get_sales_order_header($_GET['UpdatedID'],30);
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'
            || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT') {

            if($so['h_combo2'] == 2  ) {
                submenu_print(_("&Print This Order"), 10921, $order_no, 'prtopt');
            }
            else{
                submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
            }

        }
        else{
            submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
        }
         submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
            set_focus('prtopt');
    }
    else{
        if($approval['approval'] == 0) {
            submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
             submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
            set_focus('prtopt');
        }
        elseif($approval['approval'] == 1)
        {}
    }
    
// 	submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
// 	set_focus('prtopt');
	
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
    {
    if($approval['approval'] == 0) {

    submenu_option(_("Confirm Order Quantities and Make &Delivery"),
        "/sales/customer_delivery.php?OrderNumber=$order_no");
    }
    elseif($approval['approval'] == 1)
    {}

    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0) {
        submenu_option(_("Confirm Order Quantities and Make &Delivery"),
            "/sales/customer_delivery.php?OrderNumber=$order_no");
    }
    else{
        if($approval['approval'] == 0)
        {
            submenu_option(_("Confirm Order Quantities and Make &Delivery"),
                "/sales/customer_delivery.php?OrderNumber=$order_no");
        }
        elseif($approval['approval'] == 1)
        {}
    }

	submenu_option(_("Select A Different &Order"),
		"/sales/inquiry/sales_orders_view.php?OutstandingOnly=1");

	display_footer_exit();

} elseif (isset($_GET['AddedQU'])) {
	$order_no = $_GET['AddedQU'];
	display_notification_centered(sprintf( _("Quotation # %d has been entered."),$order_no));

	submenu_view(_("&View This Quotation"), ST_SALESQUOTE, $order_no);

	submenu_print(_("&Print This Quotation"), ST_SALESQUOTE, $order_no, 'prtopt');
	submenu_print(_("&Email This Quotation"), ST_SALESQUOTE, $order_no, null, 1);
	set_focus('prtopt');
	
	submenu_option(_("Make &Sales Order Against This Quotation"),
		"/sales/sales_order_entry.php?NewQuoteToSalesOrder=$order_no");

	submenu_option(_("Enter a New &Quotation"),	"/sales/sales_order_entry.php?NewQuotation=0");

	display_footer_exit();

} elseif (isset($_GET['UpdatedQU'])) {
	$order_no = $_GET['UpdatedQU'];

	display_notification_centered(sprintf( _("Quotation # %d has been updated."),$order_no));

	submenu_view(_("&View This Quotation"), ST_SALESQUOTE, $order_no);

	submenu_print(_("&Print This Quotation"), ST_SALESQUOTE, $order_no, 'prtopt');
	submenu_print(_("&Email This Quotation"), ST_SALESQUOTE, $order_no, null, 1);
	set_focus('prtopt');

	submenu_option(_("Make &Sales Order Against This Quotation"),
		"/sales/sales_order_entry.php?NewQuoteToSalesOrder=$order_no");

	submenu_option(_("Select A Different &Quotation"),
		"/sales/inquiry/sales_orders_view.php?type=".ST_SALESQUOTE);

	display_footer_exit();
} elseif (isset($_GET['AddedDN'])) {
	$delivery = $_GET['AddedDN'];

	display_notification_centered(sprintf(_("Delivery # %d has been entered."),$delivery));

	submenu_view(_("&View This Delivery"), ST_CUSTDELIVERY, $delivery);

	submenu_print(_("&Print Delivery Note"), ST_CUSTDELIVERY, $delivery, 'prtopt');
	submenu_print(_("&Email Delivery Note"), ST_CUSTDELIVERY, $delivery, null, 1);
	submenu_print(_("P&rint as Packing Slip"), ST_CUSTDELIVERY, $delivery, 'prtopt', null, 1);
	submenu_print(_("E&mail as Packing Slip"), ST_CUSTDELIVERY, $delivery, null, 1, 1);
	set_focus('prtopt');

	display_note(get_gl_view_str(ST_CUSTDELIVERY, $delivery, _("View the GL Journal Entries for this Dispatch")),0, 1);

	submenu_option(_("Make &Invoice Against This Delivery"),
		"/sales/customer_invoice.php?DeliveryNumber=$delivery");

	if ((isset($_GET['Type']) && $_GET['Type'] == 1))
		submenu_option(_("Enter a New Template &Delivery"),
			"/sales/inquiry/sales_orders_view.php?DeliveryTemplates=Yes");
	else
		submenu_option(_("Enter a &New Delivery"), 
			"/sales/sales_order_entry.php?NewDelivery=0");

	display_footer_exit();

} elseif (isset($_GET['AddedDI'])) {
	$invoice = $_GET['AddedDI'];

	display_notification_centered(sprintf(_("Invoice # %d has been entered."), $invoice));

	submenu_view(_("&View This Invoice"), ST_SALESINVOICE, $invoice);
		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='EURO' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='EURO2' )
	{
	    
submenu_print(_("&Print This Voucher - A4"),ST_SALESINVOICE1,$invoice, 'prtopt');
	}
	
	
	$trans = get_customer_trans($invoice,10);
   	$so = get_sales_order_header($trans['order_'],30);
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' 
    || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT') {
  
        if ($trans['tax_group_id'] == 2 ) {
            
          
            submenu_print(_("&Print Sales Invoice"), 10733, $invoice, 'prtopt');
        }
       else
        {
            submenu_print(_("&Print Sales Invoice"), 10730, $invoice , 'prtopt');
        }
//         else {
//             display_error(3);
//          submenu_print(_("&Print Sales Invoice"), ST_SALESINVOICE, $invoice . "-" . ST_SALESINVOICE, 'prtopt');
//
//        }
    }
    
    
     if($db_connections[$_SESSION["wa_current_user"]->company]["name"] !='BNT2'
     && $db_connections[$_SESSION["wa_current_user"]->company]["name"] !='BNT'){
     submenu_print(_("&Print Sales Invoice"), ST_SALESINVOICE, $invoice . "-" . ST_SALESINVOICE, 'prtopt');
     }

	
	
	
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='AOIS')
	{
	    
	submenu_print(_("&Print Sales Invoice - new"), 10755, $invoice, 'prtopt');
	}
	
	
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DMNWS') {
	    
		$url = $path_to_root . '/reporting/prn_redirect.php?';
		$def_orientation = (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0);

		$invoice_no = $_GET['AddedDI'];
		$trans_type = ST_SALESINVOICE;
		$doc_no = $invoice . "-" . $trans_type;
		//$rep = $type_no==ST_CUSTCREDIT ? 113 : 107;
		// from, to, currency, email, paylink, comments, orientation
			$rep = get_reports_id($trans_type);
		$ar = array(
			'PARAM_0' => $doc_no,
			'PARAM_1' => $doc_no,
			'PARAM_2' => '',
			'PARAM_3' => $email,
			'PARAM_4' => '',
			'PARAM_5' => '',
			'PARAM_6' => $def_orientation);


// header("Location: " . $path_to_root . "/reporting/prn_redirect.php?PARAM_0=$doc_no&PARAM_1=$doc_no&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=$def_orientation&REP_ID=107773");

?>
<script>
 document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('thickboxId').click();
    document.getElementById('thickboxform').click();

});
</script>
<?php

echo'<a id="thickboxId" href="../reporting/prn_redirect.php?PARAM_0='.$doc_no.'&PARAM_1='.$doc_no.'&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6='.$def_orientation.'&REP_ID='.$rep.'" target="_blank"></a>';



echo'<a id="thickboxform" href="../sales/sales_order_entry.php?NewInvoice=0" 
target="_self"></a>';


	}
	
	
	
	
	
	
	
	
	
	
	submenu_print(_("&Email Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, null, 1);
	set_focus('prtopt');

	$row = db_fetch(get_allocatable_from_cust_transactions(null, $invoice, ST_SALESINVOICE));
	if ($row !== false)
		submenu_print(_("Print &Receipt"), $row['type'], $row['trans_no']."-".$row['type'], 'prtopt');

	display_note(get_gl_view_str(ST_SALESINVOICE, $invoice, _("View the GL &Journal Entries for this Invoice")),0, 1);

	if ((isset($_GET['Type']) && $_GET['Type'] == 1))
		submenu_option(_("Enter a &New Template Invoice"), 
			"/sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes");
	else
		submenu_option(_("Enter a &New Direct Invoice"),
			"/sales/sales_order_entry.php?NewInvoice=0");

	if ($row === false)
		submenu_option(_("Enter &customer payment for this invoice"), "/sales/customer_payments.php?SInvoice=".$invoice);

	submenu_option(_("Add an Attachment"), "/admin/attachments.php?filterType=".ST_SALESINVOICE."&trans_no=$invoice");

	display_footer_exit();
} else
	check_edit_conflicts(get_post('cart_id'));
//-----------------------------------------------------------------------------

function copy_to_cart()
{
	$cart = &$_SESSION['Items'];

	$cart->reference = $_POST['ref'];

	$cart->Comments =  $_POST['Comments'];
$cart->cash_received =  $_POST['cash_received'];
	$cart->document_date = $_POST['OrderDate'];

	$newpayment = false;

	if (isset($_POST['payment']) && ($cart->payment != $_POST['payment'])) {
		$cart->payment = $_POST['payment'];
		$cart->payment_terms = get_payment_terms($_POST['payment']);
		$newpayment = true;
	}
	if ($cart->payment_terms['cash_sale']) {
	//	if ($newpayment) {
		$cart->due_date = $_POST['delivery_date'];
		$cart->cust_ref = $_POST['cust_ref'];
		$cart->deliver_to = $_POST['deliver_to'];
		$cart->delivery_address = $_POST['delivery_address'];
		$cart->phone = $_POST['phone'];
		$cart->ship_via = $_POST['ship_via'];
//		}
	} else {
		$cart->due_date = $_POST['delivery_date'];
		$cart->cust_ref = $_POST['cust_ref'];
		$cart->deliver_to = $_POST['deliver_to'];
		$cart->delivery_address = $_POST['delivery_address'];
		$cart->phone = $_POST['phone'];
		$cart->ship_via = $_POST['ship_via'];
		if (!$cart->trans_no || ($cart->trans_type == ST_SALESORDER && !$cart->is_started()))
			$cart->prep_amount = input_num('prep_amount', 0);
	}
	$cart->Location = $_POST['Location'];
	$cart->freight_cost = input_num('freight_cost');
	if (isset($_POST['email']))
		$cart->email =$_POST['email'];
	else
		$cart->email = '';
	$cart->customer_id	= $_POST['customer_id'];
	$cart->Branch = $_POST['branch_id'];
	$cart->sales_type = $_POST['sales_type'];

	//if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
	$cart->dimension_id = $_POST['dimension_id'];
	$cart->dimension2_id = $_POST['dimension2_id'];
// 	}
	$cart->ex_rate = input_num('_ex_rate', null);
	$cart->sample = $_POST['sample'];
	$cart->supply = $_POST['supply'];
	$cart->dc = $_POST['dc'];
	$cart->invoice = $_POST['invoice'];
	$cart->application = $_POST['application'];


    $cart->advance_amount = 	$_POST['advance_amount'];
	$cart->advance_cheque_no = $_POST['advance_cheque_no'];
    $cart->bank_account =  $_POST['bank_account'];
	$cart->discount1 = input_num('discount1');
	$cart->discount2 = input_num('discount2');
	$cart->disc1 = input_num('disc1');
	$cart->disc2 = input_num('disc2');
	$cart->po_date = $_POST['po_date'];
	$cart->term_cond = $_POST['term_cond'];
	//
	$cart->h_text1 = $_POST['h_text1'];
	$cart->h_text2 = $_POST['h_text2'];
	$cart->h_text3 = $_POST['h_text3'];
    $cart->h_text4 = $_POST['h_text4'];
    $cart->h_text5 = $_POST['h_text5'];
    $cart->h_text6 = $_POST['h_text6'];
	$cart->h_amount1 = input_num('h_amount1');
	$cart->h_amount2 = $_POST['h_amount2'];
	$cart->h_amount3 = $_POST['h_amount3'];
	$cart->h_date1 = $_POST['h_date1'];
	$cart->h_date2 = $_POST['h_date2'];
	$cart->h_date3 = $_POST['h_date3'];
	$cart->h_combo1 = $_POST['h_combo1'];
	$cart->h_combo2 = $_POST['h_combo2'];
	$cart->h_combo3 = $_POST['h_combo3'];
	////
	$cart->f_text1 = $_POST['f_text1'];
	$cart->f_text2 = $_POST['f_text2'];
	$cart->f_text3 = $_POST['f_text3'];
    $cart->f_text4 = $_POST['f_text4'];
    $cart->f_text5 = $_POST['f_text5'];
    $cart->f_text6 = $_POST['f_text6'];
    $cart->f_text7 = $_POST['f_text7'];
    $cart->f_text8 = $_POST['f_text8'];
    $cart->f_text9 = $_POST['f_text9'];
    $cart->f_text10 = $_POST['f_text10'];
	$cart->f_comment1 = $_POST['f_comment1'];
	$cart->f_comment2 = $_POST['f_comment2'];
	$cart->f_comment3 = $_POST['f_comment3'];
	$cart->f_date1 = $_POST['f_date1'];
	$cart->f_date2 = $_POST['f_date2'];
	$cart->f_date3 = $_POST['f_date3'];
	$cart->f_combo1 = $_POST['f_combo1'];
	$cart->f_combo2 = $_POST['f_combo2'];
	$cart->f_combo3 = $_POST['f_combo3'];
	$cart->salesman = $_POST['salesman'];
	$cart->ToBankAccount = $_POST['ToBankAccount'];
	$cart->tax_type_id = $_POST['tax_type_id'];
}

//-----------------------------------------------------------------------------

function copy_from_cart()
{
	$cart = &$_SESSION['Items'];
	$_POST['ref'] = $cart->reference;
	$_POST['Comments'] = $cart->Comments;

	$_POST['OrderDate'] = $cart->document_date;
	$_POST['delivery_date'] = $cart->due_date;
	$_POST['cust_ref'] = $cart->cust_ref;
	$_POST['freight_cost'] = price_format($cart->freight_cost);
    $_POST['cash_received'] = $cart->cash_received;
	$_POST['deliver_to'] = $cart->deliver_to;
	$_POST['delivery_address'] = $cart->delivery_address;
	$_POST['phone'] = $cart->phone;
	$_POST['Location'] = $cart->Location;
	$_POST['ship_via'] = $cart->ship_via;

	$_POST['customer_id'] = $cart->customer_id;

	$_POST['branch_id'] = $cart->Branch;
	$_POST['sales_type'] = $cart->sales_type;
	$_POST['prep_amount'] = price_format($cart->prep_amount);
	// POS 
	$_POST['payment'] = $cart->payment;
//	if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
	$_POST['dimension_id'] = $cart->dimension_id;
	$_POST['dimension2_id'] = $cart->dimension2_id;
//	}
	$_POST['cart_id'] = $cart->cart_id;
	$_POST['_ex_rate'] = $cart->ex_rate;
	$_POST['sample'] = $cart->sample;
	$_POST['supply'] = $cart->supply;
	$_POST['dc'] = $cart->dc;
	$_POST['invoice'] = $cart->invoice;
	$_POST['application'] = $cart->application;
	$_POST['discount1'] = price_format($cart->discount1);
	$_POST['discount2'] = price_format($cart->discount2);
	$_POST['disc1'] = price_format($cart->disc1);
	$_POST['disc2'] = price_format($cart->disc2);
	 $_POST['po_date'] = $cart->po_date;
	 $_POST['term_cond'] = $cart->term_cond;
	//
	$_POST['h_text1']=$cart->h_text1;
	$_POST['h_text2']=$cart->h_text2;
	$_POST['h_text3']=$cart->h_text3 ;
    $_POST['h_text4']=$cart->h_text4;
    $_POST['h_text5']=$cart->h_text5;
    $_POST['h_text6']=$cart->h_text6 ;
	$_POST['h_amount1']=$cart->h_amount1;
	$_POST['h_amount2']=$cart->h_amount2;
	$_POST['h_amount3']=$cart->h_amount3;
	$_POST['h_date1']=$cart->h_date1;
	$_POST['h_date2']=$cart->h_date2;
	$_POST['h_date3']=$cart->h_date3;
	$_POST['h_combo1']=$cart->h_combo1;
	$_POST['h_combo2']=$cart->h_combo2;
	$_POST['h_combo3']=$cart->h_combo3;
	////
	$_POST['f_text1']=$cart->f_text1;
	$_POST['f_text2']=$cart->f_text2;
	$_POST['f_text3']=$cart->f_text3;
    $_POST['f_text4']=$cart->f_text4;
    $_POST['f_text5']=$cart->f_text5;
    $_POST['f_text6']=$cart->f_text6;
    $_POST['f_text7']=$cart->f_text7;
    $_POST['f_text8']=$cart->f_text8;
    $_POST['f_text9']=$cart->f_text9;
    $_POST['f_text10']=$cart->f_text10;
	$_POST['f_comment1']=$cart->f_comment1;
	$_POST['f_comment2']=$cart->f_comment2;
	$_POST['f_comment3']=$cart->f_comment3;
	$_POST['f_date1']=$cart->f_date1;
	$_POST['f_date2']=$cart->f_date2;
	$_POST['f_date3']=$cart->f_date3;
	$_POST['f_combo1']=$cart->f_combo1;
	$_POST['f_combo2']=$cart->f_combo2;
	$_POST['f_combo3']=$cart->f_combo3;
	$_POST['salesman']=$cart->salesman;
	$_POST['ToBankAccount']=$cart->ToBankAccount;
	$_POST['advance_amount'] = $cart->advance_amount;
	$_POST['advance_cheque_no'] = $cart->advance_cheque_no;
	$_POST['bank_account'] = $cart->bank_account;
	$_POST['tax_type_id'] = $cart->tax_type_id;
}
//--------------------------------------------------------------------------------

function line_start_focus() {
  	global 	$Ajax;

  	$Ajax->activate('items_table');
  	set_focus('_stock_id_edit');
}
function Offer_dscount_line_start_focus() {
  	global $Ajax;
  	$Ajax->activate('offer_items_table');
//  	set_focus('_stock_edit');
}

//--------------------------------------------------------------------------------
function can_process() {

	global $Refs, $SysPrefs,$db_connections;

	copy_to_cart();

	if (!get_post('customer_id')) 
	{
		display_error(_("There is no customer selected."));
		set_focus('customer_id');
		return false;
	} 
	if (!get_post('Location')) 
	{
		display_error(_("There is no location selected."));
		set_focus('location');
		return false;
	} 
	
	 if (input_num('discount1') > input_num('sub_total')  )
    {
        display_error(_("Discount  can not exceed total sales amount ."));
        set_focus('location');
        return false;
    }
	
	$DebtorNo = get_branch_code(get_post('branch_id'));
	if(get_post('customer_id') != $DebtorNo)
	{
		display_error(_("Please select correct branch by again clicking the customer"));
		set_focus('branch_id');
		return false;
	}
	
	if (!get_post('branch_id')) 
	{
		display_error(_("This customer has no branch defined."));
		set_focus('branch_id');
		return false;
	} 
	
	if (!is_date($_POST['OrderDate'])) {
		display_error(_("The entered date is invalid."));
		set_focus('OrderDate');
		return false;
	}
	if ($_SESSION['Items']->trans_type!=ST_SALESORDER && $_SESSION['Items']->trans_type!=ST_SALESQUOTE && !is_date_in_fiscalyear($_POST['OrderDate'])) {
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('OrderDate');
		return false;
	}
	if (count($_SESSION['Items']->line_items) == 0)	{
		display_error(_("You must enter at least one non empty item line."));
		set_focus('AddItem');
		return false;
	}
	if (!$SysPrefs->allow_negative_stock() && ($low_stock = $_SESSION['Items']->check_qoh()))
	{
		display_error(_("This document cannot be processed because there is insufficient quantity for items marked."));
		return false;
	}
	if ($_SESSION['Items']->payment_terms['cash_sale'] == 0) { //ansar 26-08-2017
		if (!$_SESSION['Items']->is_started() && ($_SESSION['Items']->payment_terms['days_before_due'] ==-1) && ((input_num('prep_amount')<=0) ||
			input_num('prep_amount')>$_SESSION['Items']->get_trans_total())) {
			display_error(_("Pre-payment required have to be positive and less than total amount."));
			set_focus('prep_amount');
			return false;
		}
		if (strlen($_POST['deliver_to']) <= 1) {
			display_error(_("You must enter the person or company to whom delivery should be made to."));
			set_focus('deliver_to');
			return false;
		}

		if ($_SESSION['Items']->trans_type != ST_SALESQUOTE && !$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DMNWS' && strlen($_POST['delivery_address']) <= 1) {
			display_error( _("You should enter the street address in the box provided. Orders cannot be accepted without a valid street address."));
			set_focus('delivery_address');
			return false;
		}

		if ($_POST['freight_cost'] == "")
			$_POST['freight_cost'] = price_format(0);

// 		if (!check_num('freight_cost',0)) {
// 			display_error(_("The shipping cost entered is expected to be numeric."));
// 			set_focus('freight_cost');
// 			return false;
// 		}
		if (!is_date($_POST['delivery_date'])) {
			if ($_SESSION['Items']->trans_type==ST_SALESQUOTE)
				display_error(_("The Valid date is invalid."));
			else	
				display_error(_("The delivery date is invalid."));
			set_focus('delivery_date');
			return false;
		}
		if (date1_greater_date2($_POST['OrderDate'], $_POST['delivery_date'])) {
			if ($_SESSION['Items']->trans_type==ST_SALESQUOTE)
				display_error(_("The requested valid date is before the date of the quotation."));
			else	
				display_error(_("The requested delivery date is before the date of the order."));
			set_focus('delivery_date');
			return false;
		}
	}
	else
	{
		if (!db_has_cash_accounts())
		{
			display_error(_("You need to define a cash account for your Sales Point."));
			return false;
		}	
	}	
	if (!$Refs->is_valid($_POST['ref'], $_SESSION['Items']->trans_type)) {
		display_error(_("You must enter a reference."));
		set_focus('ref');
		return false;
	}
	if (!db_has_currency_rates($_SESSION['Items']->customer_currency, $_POST['OrderDate']))
		return false;
	
   	if ($_SESSION['Items']->get_items_total() < 0) {
		display_error("Invoice total amount cannot be less than zero.");
		return false;
	}

global $SysPrefs;
     if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL6'))
    {
//  restriction for credit allowed
        $customer_record = get_customer_details($_SESSION['Items']->customer_id, $_POST['DispatchDate']);
        $inv_items_total = $_SESSION['Items']->get_items_total();

        $display_sub_total = $inv_items_total + input_num('ChargeFreightCost');
        $total_amount = ($customer_record['Balance'] + $display_sub_total);

        if ($total_amount > $customer_record['credit_allowed'] && $customer_record['credit_allowed'] != 0) 
        {
            display_error(_("The Total Delivery amount " . "(" . number_format2($inv_items_total, 0) . ")" . " exceeds the credit allowed " . "(" . number_format2($customer_record['credit_allowed'], 0) . ")"));
            return false;
        }
    }

	$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['OrderDate'], 'd');

		if($row == 0)
	
		{
			$allowed_days = 'before yesterday.';
		}
		
		else
			$allowed_days =  'more than '. $row . ' day old' ;

		if($diff > $row  ){

			display_error("You are not allowed to enter entries $allowed_days");
			return false;
		}

//		else
//		{
//			if($diff < 0 )
//			{
//				display_error("You are not allowed to enter data $row day/s ahead");
//				return false;
//			}

		//}

	}

	if($row1 != '')
	{

	$diff_futuredays   =  date_diff2($_POST['OrderDate'],date('d-m-Y'), 'd');
			
				if( $diff_futuredays > $row1)
			{
			//	display_error($diff_futuredays);
		display_error("You are not allowed to enter data $row1 day/s ahead");

       return false ;

			}

	}
	if($row2 != '')
	{

		$now = date('h:i:s');

		if($row2 != 0)
		{
			$allowed_time = 'after '. $row2;
		}
		else
			$allowed_time=  '' ;

	if($row2 < $now )
		{
			display_error("You are not allowed to enter data $allowed_time pm");
			return false ;
		}

	}
	
	


	return true;
}

//-----------------------------------------------------------------------------

if(isset($_POST['update_tax']))
{
    global $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'IMEC' || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO'  || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'WTRUCK') {
        $sql = "UPDATE ".TB_PREF."cust_branch SET tax_group_id =".$_POST['tax_type_id']." where debtor_no=".db_escape($_POST['customer_id'])." ";
        db_query($sql, "Error");
    }
    if($_SESSION['Items']->trans_type == 32) {
        meta_forward('../sales/sales_order_entry.php?NewQuotation=Yes');
    }
    if($_SESSION['Items']->trans_type == 30)
    {
        meta_forward('../sales/sales_order_entry.php?NewOrder=Yes');
    }
    if($_SESSION['Items']->trans_type == 10){
        meta_forward('../sales/sales_order_entry.php?NewInvoice=Yes');
    }


}
if (isset($_POST['update'])) {

	copy_to_cart();
	$Ajax->activate('items_table');
}
if (isset($_POST['Updatecart']) && can_process()) {
    foreach ($_SESSION['Items']->line_items as $line=>$itm)
    {
            $_SESSION['Items']->line_items[$line]->text7 = $_POST['text7'.$line];
    }
}
if (isset($_POST['ProcessOrder']) && can_process()) {



    foreach ($_SESSION['Items']->line_items as $line=>$itm)
    {
        if (check_num('qty'.$line))
        {
            //display_notification(_("This sales quantity is updated"), 1);
            $_SESSION['Items']->line_items[$line]->qty_dispatched = input_num('qty'.$line);
            $_SESSION['Items']->line_items[$line]->quantity = input_num('qty'.$line);
            $_SESSION['Items']->line_items[$line]->text7 = input_num('text7'.$line);

            //display_error("Qwerty".input_num('qty'.$line));
        }
    }


	$modified = ($_SESSION['Items']->trans_no != 0);
	$so_type = $_SESSION['Items']->so_type;

	$ret = $_SESSION['Items']->write(1);
//	if ($ret == -1)
//	{
//		display_error(_("The entered reference is already in use."));
//		$ref = $Refs->get_next($_SESSION['Items']->trans_type, null, array('date' => Today()));
//		if ($ref != $_SESSION['Items']->reference)
//		{
//			display_error(_("The reference number field has been increased. Please save the document again."));
//			$_POST['ref'] = $_SESSION['Items']->reference = $ref;
//			$Ajax->activate('ref');
//		}
//		set_focus('ref');
//	}
    if ($ret == -1)
    {
        display_error(_("The entered reference is already in use."));
        $ref = $Refs->get_next($_SESSION['Items']->trans_type, null, array('date' => Today()));
        if ($ref != $_SESSION['Items']->reference)
        {
            unset($_POST['ref']); // force refresh reference
            display_error(_("The reference number field has been increased. Please save the document again."));
        }
        set_focus('ref');
    }
	else
	{
		if (count($messages)) { // abort on failure or error messages are lost
			$Ajax->activate('_page_body');
			display_footer_exit();
		}
		$trans_no = key($_SESSION['Items']->trans_no);
		$trans_type = $_SESSION['Items']->trans_type;
		new_doc_date($_SESSION['Items']->document_date);
		processing_end();
		if ($modified) {
			if ($trans_type == ST_SALESQUOTE)
				meta_forward($_SERVER['PHP_SELF'], "UpdatedQU=$trans_no");
			else	
				meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$trans_no");
		} elseif ($trans_type == ST_SALESORDER) {
			meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
		} elseif ($trans_type == ST_SALESQUOTE) {
			meta_forward($_SERVER['PHP_SELF'], "AddedQU=$trans_no");
		} elseif ($trans_type == ST_SALESINVOICE) {
			meta_forward($_SERVER['PHP_SELF'], "AddedDI=$trans_no&Type=$so_type");
		} else {
			meta_forward($_SERVER['PHP_SELF'], "AddedDN=$trans_no&Type=$so_type");
		}
	}	
}

//--------------------------------------------------------------------------------

function check_item_data()
{
	global $SysPrefs;
	
	$is_inventory_item = is_inventory_item(get_post('stock_id'));
	if(!get_post('stock_id_text', true)) {
		display_error( _("Item description cannot be empty."));
		set_focus('stock_id_edit');
		return false;
	}
	elseif (!check_num('qty', 0) || !check_num('Disc', 0, 100)) {
		display_error( _("The item could not be updated because you are attempting to set the quantity ordered to less than 0, or the discount percent to more than 100."));
		set_focus('qty');
		return false;
	} elseif (!check_num('price', 0) && (!$SysPrefs->allow_negative_prices() || $is_inventory_item)) {
		display_error( _("Price for inventory item must be entered and can not be less than 0"));
		set_focus('price');
		return false;
	} elseif (isset($_POST['LineNo']) && isset($_SESSION['Items']->line_items[$_POST['LineNo']])
	    && !check_num('qty', $_SESSION['Items']->line_items[$_POST['LineNo']]->qty_done)) {

		set_focus('qty');
		display_error(_("You attempting to make the quantity ordered a quantity less than has already been delivered. The quantity delivered cannot be modified retrospectively."));
		return false;
	}

	$cost_home = get_unit_cost(get_post('stock_id')); // Added 2011-03-27 Joe Hunt
	$cost = $cost_home / get_exchange_rate_from_home_currency($_SESSION['Items']->customer_currency, $_SESSION['Items']->document_date);
	if (input_num('price') < $cost)
	{
		$dec = user_price_dec();
		$curr = $_SESSION['Items']->customer_currency;
		$price = number_format2(input_num('price'), $dec);
		if ($cost_home == $cost)
			$std_cost = number_format2($cost_home, $dec);
		else
		{
			$price = $curr . " " . $price;
			$std_cost = $curr . " " . number_format2($cost, $dec);
		}
		display_warning(sprintf(_("Price %s is below Standard Cost %s"), $price, $std_cost));
	}
	
	$prefs = get_company_prefs();
 if($prefs['item_location'] == 1)
    {
            if (!get_post('item_location')) {
                display_error(_("There is no location selected."));
                set_focus('item_location');
                return false;
            }

    }
    
     if(get_post('stock_id') == '')
    {
        display_error(_("There is no Item selected."));
        set_focus('stock_id');
        return false;
    }
 
	
	return true;
}

//--------------------------------------------------------------------------------

function handle_update_item()
{
	if ($_POST['UpdateItem'] != '' && check_item_data()) {
	    
	     $item_info = get_item_edit_info($_POST['stock_id']);

   $formula = $item_info["formula"];

   $value = explode(",", $formula);
   $maxs = max(array_keys($value));
   $amount3 = $value[0];
   $amount2 = $value[2];
	 if($formula != '')
   {
	        if(input_num($value[$maxs]) == 1){
      $b= ($_POST[$amount3]."".$value[1]."".$_POST[$amount2]);
		  $_POST[$value[$maxs]]=eval('return '.$b.';');
		  
	        }
	       
	   }
	    
		$_SESSION['Items']->update_cart_item($_POST['LineNo'],
		 input_num('qty'), input_num('price'),
		 input_num('Disc') / 100, $_POST['item_description'],$_POST['units_id'],$_POST['con_factor'],
		$_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],
            input_num('amount1'),
			input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'), input_num('amount6'),
		$_POST['date1'], $_POST['date2'], $_POST['date3'],
		$_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
            $_POST['batch'], $_POST['item_location'], $_POST['text7'], $_POST['bonus']);
	}
	page_modified();
  line_start_focus();
}

if (isset($_POST['import']))
{
	if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '')
	{
		$filename = $_FILES['imp']['tmp_name'];
		$sep = ',';
		$fp = @fopen($filename, "r");
		if (!$fp)
			die("can not open file $filename");
		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = 0;
		unset($_SESSION['Items']->line_items);
		while ($data = fgetcsv($fp, 4096, $sep))
		{
			if ($lines++ == 0) continue;
			list($stock_id, $qty, $price, $discount) = $data;

			add_to_order($_SESSION['Items'], $stock_id, $qty,
				$price, $discount / 100, get_post('stock_id_text'));

			page_modified();
			line_start_focus();
		}
		@fclose($fp);
	} 
	else display_error("No CSV file selected");
}
//--------------------------------------------------------------------------------

function handle_delete_item($line_no)
{
    if ($_SESSION['Items']->some_already_delivered($line_no) == 0) {
	    $_SESSION['Items']->remove_from_cart($line_no);
    } else {
		display_error(_("This item cannot be deleted because some of it has already been delivered."));
    }
    line_start_focus();
}
//------------------------------------------------------------------------------
//RAMSHA
function handle_last_sales_items()
{
    unset($_SESSION['Items']->line_items);
    $order_info = get_last_sales_order(get_post('customer_id'));
    $i = 0;
    $data = array();
    while($myrow1 = db_fetch($order_info))
    {
        $data[$i] = $myrow1['order_no'];
        $i++;
    }
//    $acc_detail = get_last_sales_items($data['0'], $data['1'], $data['2'], $data['3'], $data['4'],
//        $data['5'], $data['6']);
    $acc_detail = get_last_sales_items12($data);
    while($myrow = db_fetch($acc_detail))
    {
        $price1 = get_kit_price($myrow['stk_code'], 'PKR', get_post('sales_type'), 1,
            get_post('OrderDate'));
        add_to_order($_SESSION['Items'], $myrow['stk_code'], 0,
            $price1, 0, get_post('_stock_id_edit'), 0, 0,
            0, 0, 0, 0, 0, 0,
            0, 0, 0, 0, 0,0,
            0, 0, 0,
            0, 0, 0,0, 0, 0,0,0, abs($myrow['average_order_qty']));
    }
    unset($_POST['_stock_id_edit'], $_POST['stk_code']);
    page_modified();
    line_start_focus();
}
//------------------------------------------------------------------------------
function handle_new_item()
{
   if (!check_item_data()) {
         return;
   }
   $item_info = get_item_edit_info($_POST['stock_id']);

   $formula = $item_info["formula"];

   $value = explode(",", $formula);
   $maxs = max(array_keys($value));
   $amount3 = $value[0];
   $amount2 = $value[2];



     if($formula != '')
   {

      if(input_num($value[$maxs]) == 1){
      $b = ($_POST[$amount3]."".$value[1]."".$_POST[$amount2]);
      
      $_POST[$value[$maxs]] = eval('return '.$b.';');
       }
       
       
       global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO' )
{
    display_error($b);
}
      
      if ($_POST['qty'] != 0)
      {

//       if (strcmp($value[1], "*") == 0) {
//          $_POST['qty'] = $_POST[$amount3]* $_POST[$amount2];
//          // hidden('qty', $amount3 * $amount2);
//
//       } elseif (strcmp($value[1], "+") == 0)
//          $_POST['qty'] = $_POST[$amount3]+ $_POST[$amount2];
//
//
//       elseif (strcmp($value[1], "/") == 0) {
//
//          $_POST['qty'] = $_POST[$amount3]/$_POST[$amount2];
//
//       } elseif (strcmp($value[1], "-") == 0) {
//
//          $_POST['qty'] =  $_POST[$amount3]- $_POST[$amount2];
//
//       }
      }
   }
// else
//    if(strcmp($value[1], "*") == 0) {
//       $_POST['qty'] = $amount3 * input_num('amount2');
//       hidden('qty', $amount3 * input_num('amount2'));
//    }
//    elseif(strcmp($value[1], "+") == 0)
//       $_POST['qty'] = input_num('amount2') + input_num('amount3');
//    elseif(strcmp($value[1], "/") == 0)
//       $_POST['qty'] = input_num('amount2') / input_num('amount3');
//    elseif(strcmp($value[1], "-") == 0)
//       $_POST['qty'] = input_num('amount2') - input_num('amount3');
    if(get_company_pref('scheme_calculation')) {
        if($_POST['combo4'] == 1)
        {
            $total = input_num('amount6')/$_POST['text6'];
            $total1 = $total*input_num('qty');
            $total2 = input_num('amount6')-$total1;
            $_POST['price1312'] = number_format($total2, 2);
        }
        elseif ($_POST['combo4'] == 2)
        {
            $total2 = input_num('amount6');
            $_POST['price1312'] = number_format($total2, 2);
        }
    }
    add_to_order($_SESSION['Items'], get_post('stock_id'), input_num('qty'),
    input_num('price'), input_num('Disc') / 100, get_post('stock_id_text'),
    $_POST['units_id'], get_post('con_factor'),
    $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'],
    $_POST['text6'], input_num('amount1'),
    input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'),
    input_num('amount6'),
    $_POST['date1'], $_POST['date2'], $_POST['date3'],
    $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'],
    $_POST['combo5'], $_POST['combo6'], $_POST['batch'],$_POST['item_location'],'', input_num('price1312'),
        $_POST['text7'],$_POST['bonus']);

   unset($_POST['_stock_id_edit'], $_POST['stock_id']);
   page_modified();
   line_start_focus();
      

}
//--------------------------------------------------------------------------------

function  handle_cancel_order()
{
	global $path_to_root, $Ajax;


	if ($_SESSION['Items']->trans_type == ST_CUSTDELIVERY) {
		display_notification(_("Direct delivery entry has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Sales Delivery"),	"/sales/sales_order_entry.php?NewDelivery=1");
	} elseif ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
		display_notification(_("Direct invoice entry has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Sales Invoice"),	"/sales/sales_order_entry.php?NewInvoice=1");
	} elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE)
	{
		if ($_SESSION['Items']->trans_no != 0) 
			delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);
		display_notification(_("This sales quotation has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Sales Quotation"), "/sales/sales_order_entry.php?NewQuotation=Yes");
	} else { // sales order
		if ($_SESSION['Items']->trans_no != 0) {
			$order_no = key($_SESSION['Items']->trans_no);
			if (sales_order_has_deliveries($order_no))
			{
				close_sales_order($order_no);
				display_notification(_("Undelivered part of order has been cancelled as requested."), 1);
				submenu_option(_("Select Another Sales Order for Edition"), "/sales/inquiry/sales_orders_view.php?type=".ST_SALESORDER);
			} else {
				delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);

				display_notification(_("This sales order has been cancelled as requested."), 1);
				submenu_option(_("Enter a New Sales Order"), "/sales/sales_order_entry.php?NewOrder=Yes");
			}
		} else {
			processing_end();
			meta_forward($path_to_root.'/index.php','application=orders');
		}
	}
	processing_end();
	display_footer_exit();
}

function handle_update_item_offer_discount()
{
    $_SESSION['Items']->discount_offer_update_cart_item($_POST['OfferLineNo'], $_POST['Offer_Level'],
        $_POST['Rate'], $_POST['Flag'], $_POST['Exp_Value'], $_POST['Amount1'], $_POST['Amount2']);
    page_modified();
    Offer_dscount_line_start_focus();
}
//-------------------------------------------------------------------------------
function handle_delete_item_offer_discount($line_no)
{
    $_SESSION['Items']->discount_offer_remove_from_cart($line_no);
    Offer_dscount_line_start_focus();
}
//------------------------------------------------------------------------------
function handle_new_item_offer_discount()
{
//    $sql = "SELECT * FROM 0_offer_details";
//    $query = db_query($sql, "Error");
//    while($fetch = db_fetch($query))
//    {
//        if($fetch['offer_calc_level'] == 1)
//            $level = "Level-1";
//        elseif($fetch['offer_calc_level'] == 2)
//            $level = "Level-2";
//        add_to_order_offer_discount($_SESSION['Items'], $fetch['stock_id'], $fetch['offer_calc_level'],
//            $fetch['values_'], $_POST['Flag'], $_POST['Exp_Value'], $_POST['Amount1'], $_POST['Amount2']);
    foreach ($_SESSION['Items']->get_items() as $line_no => $stock_item)
    {
        if($stock_item->stock_id == 58001)
            add_to_order_offer_discount($_SESSION['Items'], get_post('Offers_Description'), $_POST['Offer_Level'],
                $_POST['Rate'], $_POST['Flag'], $_POST['Exp_Value'], $_POST['Amount1'], $_POST['Amount2']);
    }

//  }

    unset($_POST['Offers_Description']);
    page_modified();
    Offer_dscount_line_start_focus();
}
//------------------------------------------------------------------------------
function handle_fetch_offer_discount()
{
    $_SESSION['Items']->offers_clear_items();

    foreach ($_SESSION['Items']->get_items() as $line_no => $stock_item)
    {
        $sql = "SELECT description FROM ".TB_PREF."offers WHERE line_id = ".$_POST['offers']." AND description = ".$stock_item->stock_id;
        $query = db_query($sql, "Error");
        $fetch = db_fetch($query);
        if($stock_item->stock_id == $fetch['description']) {
            $sql = "SELECT * FROM ".TB_PREF."offer_details WHERE id = ".$_POST['offers'];
            $query = db_query($sql, "Error");
            $fetch = db_fetch($query);
            $item_info = get_item_edit_info($stock_item->stock_id);
            if($fetch['inn'] == 0)
                $Amount1 = $fetch['values_']/$item_info['amount4']*$stock_item->qty_dispatched;

            $line_total = $Amount1 = 0;
            foreach ($_SESSION['Items']->get_items() as $line_no1=>$stock_item1) {
                $line_total += round($stock_item1->qty_dispatched * $stock_item1->price1312 * (1 - $stock_item1->discount_percent));
            }
            if(count($_SESSION['Items']->get_items1()) > 0) {
                foreach ($_SESSION['Items']->get_items1() as $line=>$itm) {
//                    $line_total = $_SESSION['Items']->get_items1()[$line]->Amount2;
                }
            }
            if ($fetch['inn'] == 1) {
                $Amount1 = $fetch['values_'] * $line_total / 100;
            }
            $Amount2 = $line_total - $Amount1;

            add_to_order_offer_discount($_SESSION['Items'], $fetch['stock_id'], $fetch['offer_calc_level'],
                $fetch['values_'], $fetch['inn'], $line_total, $Amount1, $Amount2);
        }
    }

    unset($_POST['Offers_Description']);
    page_modified();
    Offer_dscount_line_start_focus();
}
//--------------------------------------------------------------------------------

function create_cart($type, $trans_no)
{ 
	global $Refs, $SysPrefs;

	if (!$SysPrefs->db_ok) // create_cart is called before page() where the check is done
		return;

	processing_start();

	if (isset($_GET['NewQuoteToSalesOrder']))
	{
		$trans_no = $_GET['NewQuoteToSalesOrder'];
		$doc = new Cart(ST_SALESQUOTE, $trans_no, true);
		$doc->Comments = _("Sales Quotation") . " # " . $trans_no;
		$doc->QuoteNo = $trans_no;
		$_SESSION['Items'] = $doc;
	}	
	elseif($type != ST_SALESORDER && $type != ST_SALESQUOTE && $trans_no != 0) { // this is template

		$doc = new Cart(ST_SALESORDER, array($trans_no));
		$doc->trans_type = $type;
		$doc->trans_no = 0;
		$doc->document_date = new_doc_date();
		if ($type == ST_SALESINVOICE) {
			$doc->due_date = get_invoice_duedate($doc->payment, $doc->document_date);
			$doc->pos = get_sales_point(user_pos());
		} else
			$doc->due_date = $doc->document_date;
		$doc->reference = $Refs->get_next($doc->trans_type, null, array('date' => Today()));
		//$doc->Comments='';
		foreach($doc->line_items as $line_no => $line) {
			$doc->line_items[$line_no]->qty_done = 0;
		}
		$_SESSION['Items'] = $doc;
	} else
		$_SESSION['Items'] = new Cart($type, array($trans_no));
	copy_from_cart();
}

//--------------------------------------------------------------------------------

if (isset($_POST['CancelOrder']))
	handle_cancel_order();

$id = find_submit('Delete');
if ($id!=-1)
	handle_delete_item($id);

if (isset($_POST['UpdateItem']))
	handle_update_item();

if (isset($_POST['AddItem']))
	handle_new_item();

if (isset($_POST['CancelItemChanges'])) {
	line_start_focus();
}
//    if (isset($_POST['AddDiscount'])) {
//        handle_new_item_offer_discount();
//    }

    if (isset($_POST['FetchOffers'])) {
        handle_fetch_offer_discount();
    }

    if (isset($_POST['OfferUpdateItem']))
        handle_update_item_offer_discount();

    $id = find_submit('offers_Delete');
    if ($id != -1)
        handle_delete_item_offer_discount($id);

    if (isset($_POST['getitems']))
        handle_last_sales_items();
//--------------------------------------------------------------------------------
if ($_SESSION['Items']->fixed_asset)
	check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
else
	check_db_has_stock_items(_("There are no inventory items defined in the system."));

check_db_has_customer_branches(_("There are no customers, or there are no customers with branches. Please define customers and customer branches."));

if ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
	$idate = _("Invoice Date:");
	$orderitems = _("Sales Invoice Items");
	$deliverydetails = _("Enter Delivery Details and Confirm Invoice");
	$cancelorder = _("Cancel Invoice");
	$porder = _("Place Invoice");
} elseif ($_SESSION['Items']->trans_type == ST_CUSTDELIVERY) {
	$idate = _("Delivery Date:");
	$orderitems = _("Delivery Note Items");
	$deliverydetails = _("Enter Delivery Details and Confirm Dispatch");
	$cancelorder = _("Cancel Delivery");
	$porder = _("Place Delivery");
} elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE) {
	$idate = _("Quotation Date:");
	$orderitems = _("Sales Quotation Items");
	$deliverydetails = _("Enter Delivery Details and Confirm Quotation");
	$cancelorder = _("Cancel Quotation");
	$porder = _("Place Quotation");
	$corder = _("Commit Quotations Changes");
} else {
	$idate = _("Order Date:");
	$orderitems = _("Sales Order Items");
	$deliverydetails = _("Enter Delivery Details and Confirm Order");
	$cancelorder = _("Cancel Order");
	$porder = _("Place Order");
	$corder = _("Commit Order Changes");
}
global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'IMEC' )
	$_SESSION['Items']->AllowCSVFile = 1;

if($_SESSION['Items']->AllowCSVFile == 0)
	start_form();


hidden('cart_id');
$customer_error = display_order_header($_SESSION['Items'], !$_SESSION['Items']->is_started(), $idate);

if($_SESSION['Items']->AllowCSVFile == 1)
	display_import_csv_file();


    if ($customer_error == "") {
        //start_table(TABLESTYLE, "width='80%'", 10);
        echo "<tr><td>";
        display_order_summary($orderitems, $_SESSION['Items'], true);
        echo "</td></tr>";
        if (get_company_pref('discount_offer')) {
            echo "<tr><td>";
            display_order_summary_offer_discount("Discount Offers", $_SESSION['Items'], true);
            echo "</td></tr>";
        }
        echo "<tr><td>";
        display_delivery_details($_SESSION['Items']);
        echo "</td></tr>";
        //end_table(1);

	if($_SESSION['Items']->AllowCSVFile == 1)
		echo "<br>";

	if ($_SESSION['Items']->trans_no == 0) {

		submit_center_first('ProcessOrder', $porder,
		    _('Check entered data and save document'), 'default');
		submit_center_last('CancelOrder', $cancelorder,
	   		_('Cancels document entry or removes sales order when editing an old document'));
		submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));
	} else {
		submit_center_first('ProcessOrder', $corder,
		    _('Validate changes and update document'), 'default');
		submit_center_last('CancelOrder', $cancelorder,
	   		_('Cancels document entry or removes sales order when editing an old document'));
		if ($_SESSION['Items']->trans_type==ST_SALESORDER)
			submit_js_confirm('CancelOrder', _('You are about to cancel undelivered part of this order.\nDo you want to continue?'));
		else
			submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));
	}

} else {
	display_error($customer_error);
}

end_form();
end_page();
