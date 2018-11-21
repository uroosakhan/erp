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

include_once($path_to_root . "/POS/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/POS/includes/sales_ui.inc");
include_once($path_to_root . "/POS/includes/ui/sales_order_ui.inc");
include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_types_db.inc");
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

if ($use_popup_windows) {
    $js .= get_js_open_window(900, 500);
}

if ($use_date_picker) {
    $js .= get_js_date_picker();
}

if (isset($_GET['NewDelivery']) && is_numeric($_GET['NewDelivery'])) {

    $_SESSION['page_title'] = _($help_context = "Direct Sales Delivery");
    create_cart(ST_CUSTDELIVERY, 0);

} elseif (isset($_GET['NewInvoice']) && is_numeric($_GET['NewInvoice'])) {

    $_SESSION['page_title'] = _($help_context = "Direct Sales Invoice");

    create_cart(ST_SALESINVOICE, 0);

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
    $_SESSION['Items']->customer_id = $_GET['customer_id'];
} elseif (isset($_GET['NewQuotation'])) {

    $_SESSION['page_title'] = _($help_context = "New Sales Quotation Entry");
    create_cart(ST_SALESQUOTE, 0);
} elseif (isset($_GET['NewQuoteToSalesOrder'])) {
    $_SESSION['page_title'] = _($help_context = "Sales Order Entry");
    create_cart(ST_SALESQUOTE, $_GET['NewQuoteToSalesOrder']);
}

page($_SESSION['page_title'], false, false, "", $js);
//-----------------------------------------------------------------------------

if (list_updated('branch_id')) {
    // when branch is selected via external editor also customer can change
    $br = get_branch(get_post('branch_id'));
    $_POST['customer_id'] = $br['debtor_no'];
    $Ajax->activate('customer_id');
}

if (isset($_GET['AddedID'])) {
    global $path_to_root, $pdf_debug,$def_print_orientation;
    $invoice_no = $_GET['AddedID'];
    $trans_type = ST_SALESINVOICE;

    $url = $path_to_root.'/reporting/prn_redirect.php?';
    $def_orientation = (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0);
    $rep = get_reports_id($trans_type);
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

     var a=   document.getElementById('thickboxId').click();
              document.getElementById('thickboxform').click();



        });
    </script>
    <?php
    echo'<a id="thickboxform" href="../POS/sales_modify_entry.php?NewOrder=Yes" target="_self"></a>';
    echo'<a id="thickboxId" href="../reporting/prn_redirect.php?PARAM_0='.$invoice_no.'&PARAM_1='.$invoice_no.'&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6='.$def_orientation.'&REP_ID='.$rep.'" target="_blank"
onclick="print.js(\'thickboxId\')"></a>';
    display_footer_exit();

} elseif (isset($_GET['UpdatedID'])) {
    $order_no = $_GET['UpdatedID'];
    submenu_thermal_print(_("&Print KOT By category "), ST_SALESORDER4, $order_no, 'prtopt');
    submenu_thermal_print(_("&Print KOT"), ST_SALESORDER2, $order_no, 'prtopt');
    //submenu_thermal_print(_("&Print Whole Order Thermal Print"), ST_SALESORDER3, $order_no, 'prtopt');
    submenu_thermal_print(_("&Print Pre Payment Bill"), ST_SALESORDER5, $order_no, 'prtopt');

    display_notification_centered(sprintf( _("Order # %d has been updated."),$order_no));

    submenu_view(_("&View This Order"), ST_SALESORDER, $order_no);


    //submenu_print(_("&Print This Order"), ST_SALESORDER, $order_no, 'prtopt');
    //submenu_print(_("&Email This Order"), ST_SALESORDER, $order_no, null, 1);
    set_focus('prtopt');

    //submenu_option(_("Confirm Order Quantities and Make &Delivery"),
    //	"/sales/customer_delivery.php?OrderNumber=$order_no");
    submenu_option(_("Click To Settle"),
        "/sales/manage/orders_tables.php?");

    //submenu_option_new(_("Settle"), "/sales/cust_delivery.php?OrderNumber=".$order_no, ICON_DOC,
    //	_("You are about to generate DN and Invoice. Do you want to continue?") );

    submenu_option(_("Modify Order"), "/sales/sales_order_entry.php?ModifyOrderNumber=" . $order_no."&&change=YES", ICON_DOC,
        _("You are about to generate DN and Invoice. Do you want to continue?") );


//	submenu_option(_("Select A Different &Order"),
    //	"/sales/inquiry/sales_orders_view.php?OutstandingOnly=1");




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

    //submenu_option(_("Enter a New &Quotation"),	"/sales/sales_order_entry.php?NewQuotation=0");

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

    submenu_view(_("&View This Invoice dasdasdasdasdasd"), ST_SALESINVOICE, $invoice);

    submenu_print(_("&Print Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, 'prtopt');
    submenu_print(_("&Email Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, null, 1);
    set_focus('prtopt');

    $sql = "SELECT trans_type_from, trans_no_from FROM ".TB_PREF."cust_allocations
			WHERE trans_type_to=".ST_SALESINVOICE." AND trans_no_to=".db_escape($invoice);
    $result = db_query($sql, "could not retrieve customer allocation");
    $row = db_fetch($result);
    if ($row !== false)
        submenu_print(_("Print &Receipt"), $row['trans_type_from'], $row['trans_no_from']."-".$row['trans_type_from'], 'prtopt');

    display_note(get_gl_view_str(ST_SALESINVOICE, $invoice, _("View the GL &Journal Entries for this Invoice")),0, 1);

    if ((isset($_GET['Type']) && $_GET['Type'] == 1))
        submenu_option(_("Enter a &New Template Invoice"),
            "/sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes");
    else
        submenu_option(_("Enter a &New Direct Invoice"),
            "/sales/sales_order_entry.php?NewInvoice=0");

    submenu_option(_("Add an Attachment"), "/admin/attachments.php?filterType=".ST_SALESINVOICE."&trans_no=$invoice");

    display_footer_exit();
}// else
   // check_edit_conflicts();
//-----------------------------------------------------------------------------

function copy_to_cart()
{
    $cart = &$_SESSION['Items'];

    $cart->reference = $_POST['ref'];

    $cart->Comments =  $_POST['Comments'];
    $cart->change =  $_POST['change'];

    $cart->card_num = $_POST['card_num'];
    $cart->w_id = $_POST['w_id'];


    $cart->document_date = $_POST['OrderDate'];
    $cart->ToBankAccount = $_POST['ToBankAccount'];

    $newpayment = false;

    if (isset($_POST['payment']) && ($cart->payment != $_POST['payment'])) {
        $cart->payment = $_POST['payment'];
        $cart->payment_terms = get_payment_terms($_POST['payment']);
        $newpayment = true;
    }
    if ($cart->payment_terms['cash_sale']) {
        if ($newpayment) {
            $cart->due_date = $cart->document_date;
            $cart->phone = $cart->cust_ref = $cart->delivery_address = '';
            $cart->ship_via = 0;
            $cart->deliver_to = '';
             $cart->salesman = $cart->salesman;
        }
    } else {
        $cart->due_date = $_POST['delivery_date'];
        $cart->cust_ref = $_POST['cust_ref'];
        $cart->deliver_to = $_POST['deliver_to'];
        $cart->delivery_address = $_POST['delivery_address'];
        $cart->phone = $_POST['phone'];
        $cart->ship_via = $_POST['ship_via'];
    }
//    $cart->Location = $_POST['Location'];
    $cart->freight_cost = input_num('freight_cost');

    $cart->total_discount_pos = $_POST['total_discount_pos'];
    $cart->total_discount_pos1 = $_POST['total_discount_pos1'];
    $cart->total_discount = $_POST['total_discount'];
    $cart->CashTender = input_num('CashTender');
    $cart->CashReturn = input_num('CashReturn');
    $cart->CashGst = input_num('CashGst');
     $cart->salesman = $_POST['salesman'];

    if (isset($_POST['email']))
        $cart->email =$_POST['email'];
    else
        $cart->email = '';
//    $cart->customer_id	= $_POST['customer_id'];
//    $cart->Branch = $_POST['branch_id'];
//    $cart->sales_type = $_POST['sales_type'];

    if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
        $cart->dimension_id = $_POST['dimension_id'];
        $cart->dimension2_id = $_POST['dimension2_id'];
    }
}

//-----------------------------------------------------------------------------

function copy_from_cart()
{
    $cart = &$_SESSION['Items'];
    $_POST['ref'] = $cart->reference;
    $_POST['Comments'] = $cart->Comments;
    $_POST['change'] = $cart->change;

    $_POST['card_num'] = $cart->card_num  ;
     $_POST['salesman'] = $cart->salesman  ;

    $_POST['w_id'] = $cart->w_id  ;

    $_POST['OrderDate'] = $cart->document_date;
    $_POST['delivery_date'] = $cart->due_date;
    $_POST['cust_ref'] = $cart->cust_ref;
    $_POST['freight_cost'] = price_format($cart->freight_cost);

    $_POST['total_discount_pos'] = $cart->total_discount_pos;
    $_POST['total_discount_pos1'] = $cart->total_discount_pos1;
    $_POST['total_discount'] = $cart->total_discount;
    $_POST['ToBankAccount'] = $cart->ToBankAccount;
    $_POST['CashTender'] = $cart->CashTender;
    $_POST['CashReturn'] = $cart->CashReturn;
    $_POST['CashGst'] = $cart->CashGst;
    $_POST['deliver_to'] = $cart->deliver_to;
    $_POST['delivery_address'] = $cart->delivery_address;
    $_POST['phone'] = $cart->phone;
    $_POST['Location'] = $cart->Location;
    $_POST['ship_via'] = $cart->ship_via;

    $_POST['customer_id'] = $cart->customer_id;

    $_POST['branch_id'] = $cart->Branch;
    $_POST['sales_type'] = $cart->sales_type;
    // POS
    $_POST['payment'] = $cart->payment;
    if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
        $_POST['dimension_id'] = $cart->dimension_id;
        $_POST['dimension2_id'] = $cart->dimension2_id;
    }
    $_POST['cart_id'] = $cart->cart_id;

}
//--------------------------------------------------------------------------------

global 	$Ajax;
$_POST['DealID'] = 2;
$Ajax->activate('DealsRefresh');
function line_start_focus() {
    global 	$Ajax;
    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}

//--------------------------------------------------------------------------------
function can_process() {
    global $Refs;

//	if (!get_post('customer_id'))
//	{
//		display_error(_("There is no customer selected."));
//		set_focus('customer_id');
//		return false;
//	}
//	if (!get_post('branch_id'))
//	{
//		display_error(_("This customer has no branch defined."));
//		set_focus('branch_id');
//		return false;
//	}
//
//	if (!is_date($_POST['OrderDate'])) {
//		display_error(_("The entered date is invalid."));
//		set_focus('OrderDate');
//		return false;
//	}
//	if ($_SESSION['Items']->trans_type!=ST_SALESORDER && $_SESSION['Items']->trans_type!=ST_SALESQUOTE && !is_date_in_fiscalyear($_POST['OrderDate'])) {
//		display_error(_("The entered date is not in fiscal year"));
//		set_focus('OrderDate');
//		return false;
//	}
	if (count($_SESSION['Items']->line_items) == 0)	{
		display_error(_("You must enter at least one non empty item line."));
		set_focus('AddItem');
		return false;
	}
    if ($_SESSION['Items']->payment_terms['cash_sale'] == 0) {
        /*if (strlen($_POST['deliver_to']) <= 1) {
            display_error(_("You must enter the person or company to whom delivery should be made to."));
            set_focus('deliver_to');
            return false;
        }
            if ($_SESSION['Items']->trans_type != ST_SALESQUOTE && strlen($_POST['delivery_address']) <= 1) {
                display_error( _("You should enter the street address in the box provided. Orders cannot be accepted without a valid street address."));
                set_focus('delivery_address');
                return false;
            }*/

        if ($_POST['freight_cost'] == "")
            $_POST['freight_cost'] = price_format(0);

        if (!check_num('freight_cost',0)) {
            display_error(_("The shipping cost entered is expected to be numeric."));
            set_focus('freight_cost');
            return false;
        }
        if (!is_date($_POST['OrderDate'])) {
                display_error(_("The delivery date is invalid."));
            set_focus('OrderDate');
            return false;
        }
        //if (date1_greater_date2($_SESSION['Items']->document_date, $_POST['delivery_date'])) {
//        if (date1_greater_date2($_POST['OrderDate'], $_POST['delivery_date'])) {
//            if ($_SESSION['Items']->trans_type==ST_SALESQUOTE)
//                display_error(_("The requested valid date is before the date of the quotation."));
//            else
//                display_error(_("The requested delivery date is before the date of the order."));
//            set_focus('delivery_date');
//            return false;
//        }
    }
    else
    {
        if (!db_has_cash_accounts())
        {
            display_error(_("You need to define a cash account for your Sales Point."));
            return false;
        }
    }
    /*if (!$Refs->is_valid($_POST['ref'])) {
        display_error(_("You must enter a reference."));;
        set_focus('ref');
        return false;
    }*/
    if (!db_has_currency_rates($_SESSION['Items']->customer_currency, $_POST['OrderDate'])){
        display_error("Invoice total amount cannot be less than zero.");
        return false;
    }

    $sql = "SELECT discount FROM ".TB_PREF."discount WHERE id = ".db_escape($_POST['total_discount_pos1']);
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    hidden('total_discount_pos', $fetch['discount']);
    $dis = $_POST['total_discount_pos']/100;
    $Discount = input_num('AmountCheck')*$dis;
    
    if(input_num('TotalAmountReceived') != input_num('AmountCheck')-$Discount)
    {
        display_warning("Kindly press \"Update Amount\" button to adjust discount and proceed.");
        return false;
    }
    
    
    
//    if ($_SESSION['Items']->get_items_total() < 0) {
//        display_error("Invoice total amount cannot be less than zero.");
//        return false;
//    }
    return true;
}

//-----------------------------------------------------------------------------

if (isset($_POST['update']) || isset($_POST['total_discount_pos'])) {
    copy_to_cart();
    $Ajax->activate('items_table');
}

if (isset($_POST['ProcessOrder']) && can_process()) {

    unset($_SESSION['total_deal']);

    copy_to_cart();
    $modified = ($_SESSION['Items']->trans_no != 0);
    $so_type = $_SESSION['Items']->so_type;
    $_SESSION['Items']->reference = $Refs->get_next($_SESSION['Items']->trans_type);
    $ret = $_SESSION['Items']->write(1);
    if ($ret == -1)
    {
        //display_error(_("The entered reference is already in use."));
        $ref = get_next_reference($_SESSION['Items']->trans_type);
        if ($ref != $_SESSION['Items']->reference)
        {
            display_error(_("The reference number field has been increased. Please save the document again."));
            $_POST['ref'] = $_SESSION['Items']->reference = $ref;
            $Ajax->activate('ref');
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
        meta_forward($_SERVER['PHP_SELF'], "NewOrder=Yes");
//		if ($modified) {
//			if ($trans_type == ST_SALESQUOTE)
//			else
//				meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$trans_no");
//		} elseif ($trans_type == ST_SALESORDER) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
//		} elseif ($trans_type == ST_SALESQUOTE) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedQU=$trans_no");
//		} elseif ($trans_type == ST_SALESINVOICE) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedDI=$trans_no&Type=$so_type");
//		} else {
//			meta_forward($_SERVER['PHP_SELF'], "AddedDN=$trans_no&Type=$so_type");
//		}
    }
}
if (isset($_POST['ProcessInvoice']) && can_process()) {

    unset($_SESSION['total_deal']);

    copy_to_cart();
    $_SESSION['Items']->trans_no = 0;
    $_SESSION['Items']->trans_type = ST_SALESINVOICE;
    $modified = ($_SESSION['Items']->trans_no != 0);
    $so_type = $_SESSION['Items']->so_type;
    $_SESSION['Items']->reference = $Refs->get_next($_SESSION['Items']->trans_type);
    $ret = $_SESSION['Items']->write(1);
    if ($ret == -1)
    {
        //display_error(_("The entered reference is already in use."));
        $ref = get_next_reference($_SESSION['Items']->trans_type);
        if ($ref != $_SESSION['Items']->reference)
        {
            display_error(_("The reference number field has been increased. Please save the document again."));
            $_POST['ref'] = $_SESSION['Items']->reference = $ref;
            $Ajax->activate('ref');
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
        meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
//		if ($modified) {
//			if ($trans_type == ST_SALESQUOTE)
//			else
//				meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$trans_no");
//		} elseif ($trans_type == ST_SALESORDER) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
//		} elseif ($trans_type == ST_SALESQUOTE) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedQU=$trans_no");
//		} elseif ($trans_type == ST_SALESINVOICE) {
//			meta_forward($_SERVER['PHP_SELF'], "AddedDI=$trans_no&Type=$so_type");
//		} else {
//			meta_forward($_SERVER['PHP_SELF'], "AddedDN=$trans_no&Type=$so_type");
//		}
    }
}

//--------------------------------------------------------------------------------

function check_item_data()
{
    global $SysPrefs, $allow_negative_prices;

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
    } elseif (!check_num('price', 0) && (!$allow_negative_prices || $is_inventory_item)) {
        display_error( _("Price for inventory item must be entered and can not be less than 0"));
        set_focus('price');
        return false;
    } elseif (isset($_POST['LineNo']) && isset($_SESSION['Items']->line_items[$_POST['LineNo']])
        && !check_num('qty', $_SESSION['Items']->line_items[$_POST['LineNo']]->qty_done)) {

        set_focus('qty');
        display_error(_("You attempting to make the quantity ordered a quantity less than has already been delivered. The quantity delivered cannot be modified retrospectively."));
        return false;
    } // Joe Hunt added 2008-09-22 -------------------------
    elseif ($is_inventory_item && $_SESSION['Items']->trans_type!=ST_SALESORDER && $_SESSION['Items']->trans_type!=ST_SALESQUOTE
        && !$SysPrefs->allow_negative_stock())
    {
        $qoh = get_qoh_on_date($_POST['stock_id'], $_POST['Location'], $_POST['OrderDate']);
        if (input_num('qty') > $qoh)
        {
            $stock = get_item($_POST['stock_id']);
            display_error(_("The delivery cannot be processed because there is an insufficient quantity for item:") .
                " " . $stock['stock_id'] . " - " . $stock['description'] . " - " .
                _("Quantity On Hand") . " = " . number_format2($qoh, get_qty_dec($_POST['stock_id'])));
            return false;
        }
        return true;
    }
    $cost_home = get_standard_cost(get_post('stock_id')); // Added 2011-03-27 Joe Hunt
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
        //display_warning(sprintf(_("Price %s is below Standard Cost %s"), $price, $std_cost));
    }
    return true;
}

//--------------------------------------------------------------------------------

function handle_update_item()
{
    if ($_POST['UpdateItem'] != '' && check_item_data()) {
        
                $sql = "SELECT discount FROM ".TB_PREF."discount WHERE id = ".db_escape($_POST['Disc']);
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    
        $_SESSION['Items']->update_cart_item($_POST['LineNo'],
            input_num('qty'), input_num('price'),
            $fetch['discount'] / 100, $_POST['item_description'], $_POST['Disc'] );
    }
    page_modified();
    line_start_focus();
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

//--------------------------------------------------------------------------------
function get_price_itm($id)
{
    $sql="SELECT price FROM 0_prices where stock_id='".$id."' ";
    $db = db_query($sql,'error');
    $ft = db_fetch($db);
    return $ft[0];
}
function get_deal_amount($item_code)
{
    $sql = "SELECT item_code FROM 0_item_codes ";
    $db = db_query($sql,'error');
    //$ft = db_fetch($db);

    while($myraw = db_fetch($db))
    {
        if($item_code == $myraw['item_code'])
            $item_code;
    }

    $sql ="SELECT `deal_amount` FROM `0_deal_amount` 
	INNER JOIN 0_item_codes ON 0_item_codes.item_code=0_deal_amount.`item_code`
	 WHERE 0_deal_amount.`item_code`='$item_code'";
    $db = db_query($sql,'error');
    $ft = db_fetch($db);
    return $ft[0];


}
function handle_new_item($stk_id)
{
    if (!check_item_data()) {
        return;
    }
    $deal_price = get_deal_amount($stk_id);

    $qty = /*$_POST['qty_num'.$stk_id]*/ 1 ; // By Default Qty is 1
    $price = get_price_itm($stk_id);
    $price_discount = get_discount($_SESSION['Items']->sales_type, $stk_id);

    add_to_order($_SESSION['Items'], $stk_id, $qty,
        $price, $price_discount/100, get_post('stock_id_text'), $deal_price, $stk_id, $_POST['Disc'], $_POST['deal']);
    if($_POST['deal'] == 1) {
        add_to_order($_SESSION['Items'], $stk_id, $qty,
            $price, $price_discount/100, get_post('stock_id_text'), $deal_price, $stk_id, $_POST['Disc'], $_POST['deal']);

    }
    unset($_POST['_stock_id_edit'], $_POST['stock_id']);
    page_modified();
    line_start_focus();
}

function handle_new_item_old()
{
    if (!check_item_data()) {
        return;
    }
    /*$deal_price = get_deal_amount($stk_id);

    $qty = $_POST['qty_num'.$stk_id] ;
    $price = get_price_itm($stk_id);*/
    $deal_price = get_deal_amount(get_post('stock_id'));
    $price = get_price_itm(get_post('stock_id'));
    $sql = "SELECT discount FROM ".TB_PREF."discount WHERE id = ".db_escape($_POST['Disc']);
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    
    // hidden('Disc', $fetch['discount']);
    add_to_order($_SESSION['Items'], get_post('stock_id'), input_num('qty'),
        $price, $fetch['discount'] / 100, get_post('stock_id_text'), $deal_price,
        get_post('stock_id'), $_POST['Disc']);
    /*add_to_order($_SESSION['Items'], $stk_id, $qty,
        $price, input_num('Disc') / 100, get_post('stock_id_text'),$deal_price);*/
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
        submenu_option(_("Enter a New Sales Delivery"),	"/POS/sales_modify_entry.php?NewOrder=Yes");
    } elseif ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
        display_notification(_("Direct invoice entry has been cancelled as requested."), 1);
        submenu_option(_("Enter a New Sales Invoice"),	"/POS/sales_modify_entry.php?NewOrder=Yes");
    } elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE)
    {
        delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);
        display_notification(_("This sales quotation has been cancelled as requested."), 1);
        submenu_option(_("Enter a New Sales Quotation"), "/POS/sales_modify_entry.php?NewOrder=Yes");
    } else { // sales order
        if ($_SESSION['Items']->trans_no != 0) {
            $order_no = key($_SESSION['Items']->trans_no);
            if (sales_order_has_deliveries($order_no)) {
                close_sales_order($order_no);
                display_notification(_("Undelivered part of order has been cancelled as requested."), 1);
                submenu_option(_("Select Another Sales Order for Edition"), "/sales/inquiry/sales_orders_view.php?type=".ST_SALESORDER);
            } else {
                delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);

                display_notification(_("This sales order has been cancelled as requested."), 1);
                submenu_option(_("Enter a New Sales Order"), "/POS/sales_modify_entry.php?NewOrder=Yes");
            }
        } else {
            processing_end();
            meta_forward($path_to_root.'/index.php','application=pos');
        }
    }
    $Ajax->activate('_page_body');
    processing_end();
    display_footer_exit();
}

//--------------------------------------------------------------------------------
function check_recieve_paymenttt($order_no, $type)
{
    $sql = "SELECT  SUM(`quantity`) AS qty, SUM(`qty_sent`) AS qty_snt  
            FROM " . TB_PREF . "sales_order_details WHERE `order_no`=" . db_escape($order_no) . " AND 
            `trans_type`=" . db_escape($type) . " ";
    $result = db_query($sql, "Could not get delivery details.");
    $row = db_fetch_row($result);
    $amt = $row['0'] - $row['1'];

    return $amt;
    //$sql="SELECT (ov_amount - alloc) as amount from 0_debtor_trans order_=.$order_no";
    //$result = db_query($sql, "could not get customer");
    //$row = db_fetch_row($result);
    //if(db_num_rows($result) > 0)
}
function get_customer_order_amountss($customer_id,$order_no)
{
    $sql = "SELECT (total - (total * total_discount / 100)) FROM `0_sales_orders`
            WHERE  0_sales_orders.debtor_no='$customer_id'
            AND 0_sales_orders.order_no='$order_no'";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

function create_cart($type, $trans_no)
{
    global $Refs;

    if (!$_SESSION['SysPrefs']->db_ok) // create_cart is called before page() where the check is done
        return;

    processing_start();

    if (isset($_GET['NewQuoteToSalesOrder']))
    {
        $trans_no = $_GET['NewQuoteToSalesOrder'];
        $doc = new Cart(ST_SALESQUOTE, $trans_no, true);
        $doc->Comments = _("Sales Quotation") . " # " . $trans_no;
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
        $doc->reference = $Refs->get_next($doc->trans_type);
        //$doc->Comments='';
        foreach($doc->line_items as $line_no => $line) {
            $doc->line_items[$line_no]->qty_done = 0;
        }
        $_SESSION['Items'] = $doc;
    } else{

        $_SESSION['Items'] = new Cart($type, array($trans_no));
    }

    copy_from_cart();
}

//--------------------------------------------------------------------------------

if (isset($_POST['CancelOrder']))
    handle_cancel_order();

$stk_id = find_submit_new('AddItem2');
if ($stk_id!=-1)
    handle_new_item($stk_id);


$Modify = find_submit('Modify');
//	display_error($Modify);
if ($Modify != -1){
//	display_error("Asdasdsa".$Modify);
    handle_update_item();
    global $trans_type;
    $modify = "ModifyOrderNumber=$Modify";
    meta_forward($_SERVER['PHP_SELF'], $modify."&change=YES");

}

$id = find_submit('Delete');
if ($id!=-1)
    handle_delete_item($id);

if (isset($_POST['UpdateItem']))
    handle_update_item();

if (isset($_POST['AddItem']))
    handle_new_item_old();

if (isset($_POST['CancelItemChanges'])) {
    line_start_focus();
}

//--------------------------------------------------------------------------------
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
    $orderitems = _("");
    $deliverydetails = _("Enter Delivery Details and Confirm Order");
    $cancelorder = _("Cancel Order");
    $porder = _("Place Order");
    $corder = _("Commit Order Changes");
}
function get_customer_order_maxss($customer_id)
{
    $sql = "SELECT MAX(`order_no`) FROM `0_sales_orders`
            WHERE  0_sales_orders.debtor_no=$customer_id
            AND 0_sales_orders.trans_type = 30";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_cust_branches($branch_code)
{
    $sql = "SELECT br_name FROM ".TB_PREF."cust_branch WHERE branch_code=".db_escape($branch_code);
    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function custom_pager_link_pos($link_text, $url, $icon=false)
{
    global $path_to_root;

    if (user_graphic_links() && $icon)
        $link_text = set_icon($icon, $link_text);

    $href = $path_to_root . $url;

    return "<a href='$href'>" . $link_text . "</a>";

}
start_form();
global $Ajax;
echo '
<script>

    function myFunction(a)
    {

        var total_amount = document.getElementById("total_amount"+a).value;
        var order_no = document.getElementById("order_num"+a).value;


        var cash_recieved = document.getElementById("cash_recieved"+a).value;
        var remaining_amount = total_amount - cash_recieved ;

        document.getElementById("cash"+a).value = remaining_amount;

        var i= 1;

        $.ajax({
            type: "POST",
            url: "../POS/manage/handler.php?id="+cash_recieved+"&&order_no="+order_no,
            async: false,
            success: function (response)
            {
                var result = response;
                console.log(result);
            },
            failure: function (msg) {
        alert(msg);
    }
        });

    }


</script>
';
hidden('cart_id');

//
//echo ' <div class="col-sm-2" style="background-color:; float: right; width: 9%; height: 20px; margin-top: 0px;  ">
//<table class="table">
//  <thead>
//
//  </thead>
//  <tbody>
//
//
//    <tr>
//      <th scope="row">
//<button type="button" class="btn btn"><a href="sales_order_entry.php?NewOrder=Yes">add new</a></button>
//
//</th>
//
//    </tr>
//
//  </tbody>
//</table>
//</div> ';

//submit_center_last('CancelOrder', $cancelorder,
//    _('Cancels document entry or removes sales order when editing an old document'));
echo "<br>";
customer_pos_list_row(_("Customer:"), 'customer_id', null, false, true, false, true);


$time=date('h:i');
$time1=('03:00:00');
$time2=('12:00:00');

if($time2 < $time || $time1 > $time ){
$_POST['OrderDate']= get_current_company_date(3);
}
else{
  $_POST['OrderDate']; 
    
}

date_row($idate, 'OrderDate', _('Date of order receive'),
    $order->trans_no==0, 0, 0, 0, null, false);

$order = $_SESSION['Items'];
if ($order->pos['cash_sale'] || $order->pos['credit_sale']) {
    // editable payment type
    if (get_post('payment') !== $order->payment) {
        $order->payment = get_post('payment');
        $order->payment_terms = get_payment_terms($order->payment);
        $order->due_date = get_invoice_duedate_pos($order->payment, $order->document_date);
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
    sale_pos_payment_list_cells(_('Payment:'), 'payment',
        (in_array($order->trans_type, array(ST_SALESQUOTE, ST_SALESORDER))
            ? PM_ANY : $paymcat), null);
} else {
    label_cells(_('Payment:'), $order->payment_terms['terms']);
}

    sales_pos_types_list_cells(_("Price List:"), 'sales_type', null, true);

// display_error($order->pos['account_code']);
$pref = get_company_pref();
$ByDefaultAccount = get_user_bank_account($_SESSION['wa_current_user']->user);
if($pref['pos_cash_account'] == 1){
    bank_accounts_list_all_row_pos(_("Cash Account:"), 'ToBankAccount', $order->pos['account_code'], false);
}
else{
    label_row(_("Cash account:"), $order->pos['bank_account_name']);
}

//------------------pos_user----------------------//

global  $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CW'|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CB' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RMS'){

    // $a=$_SESSION['wa_current_user']->access == 2;
    // if ($a) {
    //     users_list_row_(_("Users ") . ":", 'salesman', null, null, false, true);
    // }
    // else
    // {
    hidden('salesman',$_SESSION['wa_current_user']->user);
        // users_list_row_(_("Users ") . ":", 'salesman', $_SESSION['wa_current_user']->user, null, false, true);
    // }

   }
//------------------pos_user----------------------//

//type = 1 = sales invoice
echo viewer_link_pos(null, "POS/inquiry/pos_inquiry.php?type=1", null, $i, 'log.png');
//echo "<div><style margin-right: 20px;>    ";
if ($_SESSION['Items']->trans_no == 0) {

    submit_cells('ProcessInvoice', "Cash Invoice", "colspan=1 align='right'",
        _('Add new item to document'), true);
    submit_cells('ProcessOrder', $porder, "colspan=1 align='right'",
        _('Add new item to document'), true);
//    submit_cells('CancelOrder', _("Cancel Order"), "colspan=1 align='right'",
//        _('Add new item to document'), true);
//    submit_center_first('ProcessOrder', $porder,
//        _('Check entered data and save document'), 'default');
//    submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));
} else {
    submit_cells('ProcessOrder', $corder, "colspan=1 align='right'",
        _('Add new item to document'), true);
//    submit_cells('CancelOrder', _("Cancel Order"), "colspan=1 align='right'",
//        _('Add new item to document'), true);
}


div_start('DealsRefresh');
if($_POST['DealID'] == 2)
{
    $Null = null;
    $ByDefault = 1;
}
label_cell(radio("Deal", 'deal', "1", $Null, false));
label_cell(radio("Without Deal", 'deal', "2", $ByDefault, false));
div_end();

//echo "</style></div>";
$customer_error = display_order_header($_SESSION['Items'], ($_SESSION['Items']->any_already_delivered() == 0), $idate);
if ($customer_error == "") {

//--

  //  echo '<div  style="float: left; ">';
       // echo '<p>Running Orders</p>';
    $result = get_all_sales_orderss();
    if(db_num_rows($result) != 0) {


        echo '<div class="row">
  <div class="col-md-5.5 " >';
        start_table_pos_running_orders(TABLESTYLE, "width=0%");

        $cash = db_has_cash_accounts();
        $th = array(_('Tables'), _('Time'), _('Total'), _('KOT'), _('P.P'), _('Cash Tendered'), _('Cash Return'),
            _('Settle '), '', 'Done', '', '');
        inactive_control_column($th);
        table_header($th);
        $k = 0;
        $base_sales = get_base_sales_type();
//	$trans_type = $_SESSION['Items']->trans_type;
//	$trans_no = key($_SESSION['Items']->trans_no);
        $num = 1;
        $result = get_all_sales_orderss();
        while ($myrow = db_fetch($result)) {
//if ($myrow["cash_recieved"] == 0)continue;
//			start_row("class='overduebg'");
//		else
//			alt_table_row_color($k);
            $amount = $myrow["TotalAmount"];

//            $max_order=get_customer_order_maxss($myrow['debtor_no']);
//            if(check_recieve_paymenttt($max_order, ST_SALESORDER) != 0)
//            {
//                $amount = get_customer_order_amountss($myrow['debtor_no'], $max_order);
//            }
            echo "<input type='hidden' value='$amount' id='total_amount$num' name='txt_total_amnt'>";
            echo "<input type='hidden' value=" . $myrow['order_no'] . " id='order_num$num' >";
            $getorder_no = get_all_sales_order_details($myrow["order_no"]);
            $dec = user_price_dec();
            if ($getorder_no != 0) {
                label_cell(get_cust_branches($myrow["branch_code"]));
                label_cell($myrow["order_time"]);
                label_cell(number_format2($myrow["TotalAmount"], $dec));
                $max_order = get_customer_order_maxss($myrow['debtor_no']);
                inactive_control_cell($myrow["id"], $myrow["inactive"], 'sales_types', 'id');
                label_cell_pos_orange(print_document_link($myrow['order_no'], _("Print"), true, ST_SALESORDER2, ICON_PRINT));
                label_cell_pos_blue(print_document_link($myrow['order_no'], _("Print"), true, ST_SALESORDER6, ICON_PRINT));
                // label_cell_pos_green(print_document_link($myrow['order_no'], _("Print"), true, ST_SALESINVOICE, ICON_PRINT));
                label_cell("<input type='text' style='color:black; width:50px;'  onfocusout='myFunction($num)' id='cash_recieved$num' name='txt_cash_recieved'");
                label_cell("<input type='text' style='color:black;width:50px;'   style='margin-top:5px;' id='cash$num' name='cash$num' readonly='true'");
                label_cell_pos(custom_pager_link_pos(_("settle"), "/POS/cust_delivery.php?OrderNumber=" . $myrow["order_no"] . "&&cashrecieved=" . $_POST['total_amount' . $num], ICON_PRINT));
//                 echo "<td>";
// //                submit("submit", 'Settle');
//                 label_cell_pos(submenu_option_customize(_("Settle"), "/POS/cust_delivery.php?OrderNumber=".$myrow["order_no"]."&&cashrecieved=".$_POST['total_amount'.$num], ICON_DOC));
//                 echo "</td>";
//                if(isset($_POST['settle']))
//                    refresh('sales_modify_entry.php');
//                if(isset($_POST['settle']))
//                    meta_forward($path_to_root . "/POS/sales_modify_entry.php", "?OrderNumber=".$myrow["order_no"]."&cashrecieved=".$_POST['total_amount'.$num]);
//                 $sql="select * from 0_debtor_trans , 0_debtor_trans_details
//                       where
//                     0_debtor_trans_details.qty_done < 0_debtor_trans_details.quantity
//                     AND
//                         0_debtor_trans.type = 10
//                     AND
//                       order_=".$myrow["order_no"];
//                 $sql .=" order by trans_no desc ";
//                 $query=db_query($sql,"error");
//                 $fetch=db_fetch($query);
// $allow=0;
//                 if($fetch['trans_no']!=0)
//                 { $allow=1;
//                     label_cell_pos(print_document_link($fetch['trans_no'], _("Print"), true, ST_SALESINVOICE, ICON_PRINT));
//                 }
//                 if($allow==0) {
//                     label_cell_pos()    ;
// ///
// //                    label_cell(print_document_link($myrow["order_no"], _("Print"), true, ST_SALESORDER2, ICON_PRINT));
//            }
// //                check_box("", 1, "set_value(this)", true);
                //label_cell(  ' <input type="checkbox" name="chk'.$num.'" value="" >');
                label_cell_pos(' <input type="checkbox" name="chk' . $num . '" value="" onclick="if(this.checked){this.form.submit();}" >');
//                if($_POST['chk' . $num . '']) {
//                    meta_forward($path_to_root . "/POS/sales_modify_entry.php", "NewOrder=Yes");
//                }
//                else{
//                    label_cell(' <input type="checkbox" name="chk' . $num . '" value="" >');
//                }
                edit_button_cell("Modify" . $myrow['order_no'], _(" Edit"));
                
                if ($_SESSION["wa_current_user"]->access == 2 || $_SESSION["wa_current_user"]->access == 5)
                    delete_button_cell("DeleteOrders" . $myrow['order_no'], _("Delete"));
            }
            end_row();
            $num++;
        }
        // inactive_control_row($th);
        end_table();
        //   echo '</div>';
        echo '</div>';
        echo '<div class="col-md-5 " style="margin-top: -40px;">';
        echo "<tr><td>";
//    display_order_summary($orderitems, $_SESSION['Items'], true);
        echo "</td></tr>";
        echo '</div>';
        echo '</div>';
    }
   /*
    $result = get_today_invoices_for_reprint();
    if(db_num_rows($result) != 0) {
 echo "<center><h2>Today Invoices</h2></center><br><br>";

        echo '<div class="row">
        <div class="col-md-5.5 " >';
        start_table_pos_running_orders(TABLESTYLE, "width=0%");

        $cash = db_has_cash_accounts();
        $th = array(_('#'), _('Tables'), _('Date'), _('Time'), _('Gross Amount'), _('Discount'), _('Net Amount'), _('Cash Tendered'), _('Cash Return'), _('Re-Print'));
        inactive_control_column($th);
        table_header($th);
        $k = $TotalAmount = 0;
        $base_sales = get_base_sales_type();
        $num = 1;
        $result = get_today_invoices_for_reprint();
        while ($myrow = db_fetch($result)) {
            start_row();
            $amount = $myrow["TotalAmount"];
            label_cell($num);
            label_cell(get_customer_name($myrow["debtor_no"]));
            label_cell(sql2date($myrow["tran_date"]));
            label_cell($myrow["order_time"]);
            amount_cell($myrow["total"]);
            amount_cell($myrow["discount1"]);
            amount_cell($amount);
            label_cell($myrow["h_text1"]);
            label_cell($myrow["h_text2"]);
            label_cell_pos_blue(print_document_link($myrow['trans_no'], _("Print"), true, ST_SALESINVOICE, ICON_PRINT));
            end_row();
            $num++;
            $TotalAmount += $amount;
        }
        start_row();
        label_cell("");
        label_cell('');
        label_cell('');
        label_cell("<b><font color=black>" . _("Today Sale :") . "</font></b>");
        label_cell(number_format($TotalAmount));


        end_row();
        end_table();
        //   echo '</div>';
        echo '</div>';
        echo '<div class="col-md-5 " style="margin-top: -40px;">';
        echo "<tr><td>";
//    display_order_summary($orderitems, $_SESSION['Items'], true);
        echo "</td></tr>";
        echo '</div>';
        echo '</div>';
    }*/
    // echo '</div>';
    $delete = find_submit("DeleteOrders");
    if($delete != -1) {
        $sql = "DELETE FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($delete);
        $sql1 = "DELETE FROM ".TB_PREF."sales_order_details WHERE order_no=".db_escape($delete);
        db_query($sql,"The query could not be deleted");
        db_query($sql1,"The query could not be deleted");
        display_notification(_(" Order has been cancelled as requested."), 1);
        meta_forward($_SERVER['PHP_SELF'], "NewOrder=Yes");
    }
}

end_form();
end_page();
?>


