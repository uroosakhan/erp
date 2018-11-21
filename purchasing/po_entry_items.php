<?php

$path_to_root = "..";
$page_security = 'SA_PURCHASEORDER';
include_once($path_to_root . "/purchasing/includes/po_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/purchasing/includes/db/suppliers_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

set_page_security( @$_SESSION['PO']->trans_type,
    array(	ST_PURCHORDER => 'SA_PURCHASEORDER',
        ST_SUPPRECEIVE => 'SA_GRN',
        ST_SUPPINVOICE => 'SA_SUPPLIERINVOICE'),
    array(	'NewOrder' => 'SA_PURCHASEORDER',
        'ModifyOrderNumber' => 'SA_PURCHASEORDER',
        'AddedID' => 'SA_PURCHASEORDER',
        'NewGRN' => 'SA_GRN',
        'AddedGRN' => 'SA_GRN',
        'NewInvoice' => 'SA_SUPPLIERINVOICE',
        'AddedPI' => 'SA_SUPPLIERINVOICE')
);

$js = '';
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();

if (isset($_GET['ModifyOrderNumber']) && is_numeric($_GET['ModifyOrderNumber'])) {

    $_SESSION['page_title'] = _($help_context = "Modify Purchase Order #") . $_GET['ModifyOrderNumber'];
    create_new_po(ST_PURCHORDER, $_GET['ModifyOrderNumber']);
    copy_from_cart();
} elseif (isset($_GET['NewOrder'])) {

    $_SESSION['page_title'] = _($help_context = "Purchase Order Entry");
    create_new_po(ST_PURCHORDER, 0);
    copy_from_cart();
} elseif (isset($_GET['NewGRN'])) {

    $_SESSION['page_title'] = _($help_context = "Direct GRN Entry");
    create_new_po(ST_SUPPRECEIVE, 0);
    copy_from_cart();
} elseif (isset($_GET['NewInvoice'])) {

    create_new_po(ST_SUPPINVOICE, 0);
    copy_from_cart();

    if (isset($_GET['FixedAsset'])) {
        $_SESSION['page_title'] = _($help_context = "Fixed Asset Purchase Invoice Entry");
        $_SESSION['PO']->fixed_asset = true;
    } else
        $_SESSION['page_title'] = _($help_context = "Direct Purchase Invoice Entry");
}

page($_SESSION['page_title'], false, false, "", $js);

if (isset($_GET['ModifyOrderNumber']))
    check_is_editable(ST_PURCHORDER, $_GET['ModifyOrderNumber']);

//---------------------------------------------------------------------------------------------------

check_db_has_suppliers(_("There are no suppliers defined in the system."));

//---------------------------------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
    $order_no = $_GET['AddedID'];
    $order_no1 = $_GET['Updated'];
    $trans_type = ST_PURCHORDER;
    $trans_type1 = 7899;

    $sql = "SELECT * 
			FROM ".TB_PREF."purch_orders
			WHERE order_no =".db_escape($order_no);
    $result = db_query($sql, "Could not find dimension");
    $approval = db_fetch($result);
    global $SysPrefs;

    if (!isset($_GET['Updated']))
        display_notification_centered(_("Purchase Order has been entered"));
    else
        display_notification_centered(_("Purchase Order has been updated") . " #$order_no");
    display_note(get_trans_view_str($trans_type, $order_no, _("&View this order")), 0, 1);

if (isset($_GET['AddedID'])) {
          if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {
           
            display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
        }
        elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {}

        elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
           
            display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
        }
        else
        {
            display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
        }
    }
    elseif (isset($_GET['Updated'])) {
        if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
            if ($approval['approval'] == 0) {
                display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
            } elseif ($approval['approval'] == 1) {
            }
        }
         elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {}
        elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
            display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);

        } else {
            if ($approval['approval'] == 0) {
                display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
            }
            elseif ($approval['approval'] == 1) {
            }
        }
    }

    display_note(print_document_link($order_no, _("&Email This Order"), true, $trans_type, false, "printlink", "", 1));
    if (isset($_GET['AddedID'])) {
        
        if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {
            hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no");
        }
        elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {}

        elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
            hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no");
        }
        else
        {
           hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no"); 
        }
    }
    elseif (isset($_GET['Updated'])) {
        if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
            if ($approval['approval'] == 0) {
                hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no1");
            } elseif ($approval['approval'] == 1) {
            }
        }
             elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
        {}
        elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
            hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no1");

        } else {
            if ($approval['approval'] == 0) {
                hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no1");
            }
            elseif ($approval['approval'] == 1) {
            }
        }
    }


    // TODO, for fixed asset
    hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Purchase Order"), "NewOrder=yes");
    

    hyperlink_no_params($path_to_root."/purchasing/inquiry/po_search.php", _("Select An &Outstanding Purchase Order"));

    display_footer_exit();

}
//elseif (isset($_GET['AddedID']))
//{
//    $order_no = $_GET['AddedID'];
//    $trans_type = ST_PURCHORDER;
//
//    $sql = "SELECT *
//			FROM ".TB_PREF."purch_orders
//			WHERE order_no =".db_escape($order_no);
//    $result = db_query($sql, "Could not find dimension");
//    $approval = db_fetch($result);
//
//    if (!isset($_GET['Updated']))
//        display_notification_centered(_("Purchase Order has been entered"));
//    else
//        display_notification_centered(_("Purchase Order has been updated") . " #$order_no");
//    display_note(get_trans_view_str($trans_type, $order_no, _("&View this order")), 0, 1);
//
//    display_note(print_document_link($order_no, _("&Print This Order"), true, $trans_type), 0, 1);
//
//    display_note(print_document_link($order_no, _("&Email This Order"), true, $trans_type, false, "printlink", "", 1));
//
//    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
//        if ($approval['approval'] == 1) {
//            hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no");
//        }
//        elseif($approval['approval'] == 0)
//        {}
//    }
//    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
//        hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no");
//
//    }
//    else{
//        if($approval['approval'] == 1)
//        {
//            hyperlink_params($path_to_root . "/purchasing/po_receive_items.php", _("&Receive Items on this Purchase Order"), "PONumber=$order_no");
//        }
//        elseif($approval['approval'] == 0)
//        {}
//    }
//    // TODO, for fixed asset
//    hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Purchase Order"), "NewOrder=yes");
//
//    hyperlink_no_params($path_to_root."/purchasing/inquiry/po_search.php", _("Select An &Outstanding Purchase Order"));
//
//    display_footer_exit();
//
//}
elseif (isset($_GET['AddedGRN'])) {

    $trans_no = $_GET['AddedGRN'];
    $trans_type = ST_SUPPRECEIVE;

    display_notification_centered(_("Direct GRN has been entered"));

    display_note(get_trans_view_str($trans_type, $trans_no, _("&View this GRN")), 0);

    $clearing_act = get_company_pref('grn_clearing_act');
    if ($clearing_act)
        display_note(get_gl_view_str($trans_type, $trans_no, _("View the GL Journal Entries for this Delivery")), 1);

    hyperlink_params("$path_to_root/purchasing/supplier_invoice.php",
        _("Enter purchase &invoice for this receival"), "New=1");

    hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"),
        "filterType=$trans_type&trans_no=$trans_no");

    hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another GRN"), "NewGRN=Yes");

    display_footer_exit();

} elseif (isset($_GET['AddedPI'])) {

    $trans_no = $_GET['AddedPI'];
    $trans_type = ST_SUPPINVOICE;
    $trans_type1 = 7899;

    display_notification_centered(_("Direct Purchase Invoice has been entered"));

    display_note(get_trans_view_str($trans_type, $trans_no, _("&View this Invoice")), 0);

    display_note(get_gl_view_str($trans_type, $trans_no, _("View the GL Journal Entries for this Invoice")), 1);

    hyperlink_params("$path_to_root/purchasing/supplier_payment.php", _("Entry supplier &payment for this invoice"),
        "trans_type=$trans_type&PInvoice=".$trans_no);

    hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"),
        "filterType=$trans_type&trans_no=$trans_no");

    hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Direct Invoice"), "NewInvoice=Yes");
	echo "</br>";
submenu_print(_("&Print This Purchase invoice"), $trans_type1, $trans_no, 'prtopt'); 

    display_footer_exit();
}

if ($_SESSION['PO']->fixed_asset)
    check_db_has_purchasable_fixed_assets(_("There are no purchasable fixed assets defined in the system."));
else
    check_db_has_purchasable_items(_("There are no purchasable inventory items defined in the system."));
//--------------------------------------------------------------------------------------------------

function line_start_focus() {
    global 	$Ajax;

    $Ajax->activate('items_table');
    set_focus('_stock_id_edit');
}
//--------------------------------------------------------------------------------------------------

function unset_form_variables() {
    unset($_POST['stock_id']);
    unset($_POST['qty']);
    unset($_POST['price']);
    unset($_POST['req_del_date']);
}

//---------------------------------------------------------------------------------------------------

function handle_delete_item($line_no)
{
    if($_SESSION['PO']->some_already_received($line_no) == 0)
    {
        $_SESSION['PO']->remove_from_order($line_no);
        unset_form_variables();
    }
    else
    {
        display_error(_("This item cannot be deleted because some of it has already been received."));
    }
    line_start_focus();
}

//---------------------------------------------------------------------------------------------------

function handle_cancel_po()
{
    global $path_to_root;

    //need to check that not already dispatched or invoiced by the supplier
    if(($_SESSION['PO']->order_no != 0) &&
        $_SESSION['PO']->any_already_received() == 1)
    {
        display_error(_("This order cannot be cancelled because some of it has already been received.")
            . "<br>" . _("The line item quantities may be modified to quantities more than already received. prices cannot be altered for lines that have already been received and quantities cannot be reduced below the quantity already received."));
        return;
    }

    $fixed_asset = $_SESSION['PO']->fixed_asset;

    if($_SESSION['PO']->order_no != 0)
        delete_po($_SESSION['PO']->order_no);
    else {
        unset($_SESSION['PO']);

        if ($fixed_asset)
            meta_forward($path_to_root.'/index.php','application=assets');
        else
            meta_forward($path_to_root.'/index.php','application=AP');
    }

    $_SESSION['PO']->clear_items();
    $_SESSION['PO'] = new purch_order;

    display_notification(_("This purchase order has been cancelled."));

    hyperlink_params($path_to_root . "/purchasing/po_entry_items.php", _("Enter a new purchase order"), "NewOrder=Yes");
    echo "<br>";

    end_page();
    exit;
}

//---------------------------------------------------------------------------------------------------

function check_data()
{
    
     $pref=get_company_prefs();
    if(!get_post('stock_id_text', true)) {
        display_error( _("Item description cannot be empty."));
        set_focus('stock_id_edit');
        return false;
    }

    $dec = get_qty_dec($_POST['stock_id']);
    $min = 1 / pow(10, $dec);
    global $SysPrefs;
    if($SysPrefs->show_text_qty2()== 0) {
        if (!check_num('qty', $min)) {
            $min = number_format2($min, $dec);
            display_error(_("The quantity of the order item must be numeric and not less than ") . $min);
            set_focus('qty');
            return false;
        }
    }
    
    
      if (input_num('Disc')  > input_num('price') && $pref['disc_in_amount'] == 1 ){
        display_error(_("Discount cannot be more than price."));
        set_focus('Disc');
        return false;
    }

    if ($_POST['Disc'] > 100  && $pref['disc_in_amount'] == 0 ){
        display_error(_("Discount cannot be more than 100 %."));

        set_focus('Disc');
        return false;
    }
//     if (!check_num('price', 0))
//     {
// 	   	display_error(_("The price entered must be numeric and not less than zero."));
// 		set_focus('price');
// 	   	return false;
//     }
    if ($_SESSION['PO']->trans_type == ST_PURCHORDER && !is_date($_POST['req_del_date'])){
        display_error(_("The date entered is in an invalid format."));
        set_focus('req_del_date');
        return false;
    }

    return true;
}

//---------------------------------------------------------------------------------------------------

function handle_update_item()
{
    $allow_update = check_data();

    if ($allow_update)
    {
        if ($_SESSION['PO']->line_items[$_POST['line_no']]->qty_inv > input_num('qty') ||
            $_SESSION['PO']->line_items[$_POST['line_no']]->qty_received > input_num('qty'))
        {
            display_error(_("You are attempting to make the quantity ordered a quantity less than has already been invoiced or received.  This is prohibited.") .
                "<br>" . _("The quantity received can only be modified by entering a negative receipt and the quantity invoiced can only be reduced by entering a credit note against this item."));
            set_focus('qty');
            return;
        }
        $prefs=get_company_prefs();
        if( $prefs['disc_in_amount'] == 1){

            $disc = input_num('Disc') ;

        }
        else{
            $disc = input_num('Disc') / 100;
        }
        $_SESSION['PO']->update_order_item($_POST['line_no'], input_num('qty'), input_num('price'),
            @$_POST['req_del_date'], $_POST['item_description'],
            $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],$_POST['text7'],
            $_POST['amount1'], $_POST['amount2'], $_POST['amount3'], $_POST['amount4'], $_POST['amount5'], $_POST['amount6'],
            $_POST['date1'], $_POST['date2'], $_POST['date3'],
            $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
            $_POST['batch'],$_POST['exp_date'],$_POST['con_factor'],$_POST['units_id'],$disc);
        unset_form_variables();
    }
    line_start_focus();
}
//=====================================================================================================
//RAMSHA
function handle_last_purchase_items()
{
    unset($_SESSION['PO']->line_items);
    $order_info = get_last_purchase_order(get_post('supplier_id'));
    $i = 0;
    $data = array();
    while($myrow1 = db_fetch($order_info))
    {
        $data[$i] = $myrow1['order_no'];
        $i++;
    }
    $acc_detail = get_last_purchase_items($data['0'], $data['1'], $data['2'], $data['3'], $data['4'],
        $data['5'], $data['6']);
    while($myrow = db_fetch($acc_detail))
    {
//        $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $myrow['item_code'],
//            0, get_post('stock_id_text'),  0, '', '', 0, 0, abs($myrow['average_order_qty']));
        $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $myrow['item_code'], 0,
            get_post('stock_id_text'), //$myrow["description"],
            0, '', // $myrow["units"], (retrived in cart)
            $_SESSION['PO']->trans_type == ST_PURCHORDER ? $_POST['req_del_date'] : '', 0, 0,
            '', '', '', '', '', '',
            0, 0, 0, 0, 0, 0,
            '', '', '',
            0, 0, 0, 0, 0, 0,
            0, '', 0, 0, abs($myrow['average_order_qty']));

    }
//    unset_form_variables();
    line_start_focus();
}

//---------------------------------------------------------------------------------------------------


function handle_add_new_item()
{

    $AllowKit = get_company_pref('allow_sales_kit_in_po');
    $AllowKit1 = 0;
    /*if($AllowKit == '')
        $AllowKit1 = 0;
    elseif($AllowKit == 0)
        $AllowKit1 = 0;
    else*/
    if($AllowKit == 1)
        $AllowKit1 = 1;


    $allow_update = check_data();

    if ($allow_update == true)
    {
        if (count($_SESSION['PO']->line_items) > 0)
        {
            foreach ($_SESSION['PO']->line_items as $order_item)
            {
                /* do a loop round the items on the order to see that the item
                is not already on this order */
                if (($order_item->stock_id == $_POST['stock_id']))
                {
                    display_warning(_("The selected item is already on this order."));
                }
            } /* end of the foreach loop to look for pre-existing items of the same code */
        }

        if ($allow_update == true) {

            if($AllowKit1 == 0){
                $result = get_short_info($_POST['stock_id']);
                if (db_num_rows($result) == 0) {
                    $allow_update = false;
                }
            }elseif($AllowKit1 == 1)
                $allow_update = true; // For Kit
            if ($allow_update)
            {
                $item_info = get_item_edit_info($_POST['stock_id']);
                $formula = $item_info["formula"];
                $value = explode(",", $formula);
                $maxs = max(array_keys($value));
                $amount3 = $value[0];
                $amount2 = $value[2];
                if($formula != '') {
                    if (input_num($value[$maxs]) == 1) {
                        $b = ($_POST[$amount3] . "" . $value[1] . "" . $_POST[$amount2]);
                        $_POST[$value[$maxs]] = eval('return '.$b.';');
                    }
                }
//                $std_price = get_kit_price($new_item, $order->customer_currency,
//                    $order->sales_type,	$order->price_factor, get_post('OrderDate'), true);
//
//                if ($std_price == 0)
//                    $price_factor = 0;
//                else
//                    $price_factor = $price/$std_price;
//                display_error($_SESSION['PO']->customer_currency."+".$_SESSION['PO']->sales_type."+".$_SESSION['PO']->price_factor);
//                $std_price = get_kit_price($_POST['stock_id'], $_SESSION['PO']->customer_currency,
//                    $_SESSION['PO']->sales_type, $_SESSION['PO']->price_factor, get_post('OrderDate'), true);
//                if ($std_price == 0)
//                    $price_factor = 0;
//                else
//                    $price_factor = input_num('price')/$std_price;

                $kit = get_item_kit($_POST['stock_id']);
                $item_num = db_num_rows($kit);
                while($item = db_fetch($kit)) {
                    $std_price = get_kit_price($item['stock_id'], $_SESSION['PO']->customer_currency,
                        $_SESSION['PO']->sales_type, $_SESSION['PO']->price_factor, get_post('OrderDate'), true);
                    // rounding differences are included in last price item in kit
//                    $item_num--;
//                    if ($item_num) {
//                        $price -= $item['quantity']*$std_price*$price_factor;
//                        $item_price = $std_price*$price_factor;
//                    } else {
//                        if ($item['quantity'])
//                            $price = $price/$item['quantity'];
//                        $item_price = $price;
//                    }
//                    $item_price = round($item_price, get_qty_dec($_POST['stock_id']));
//                    $std_price = get_kit_price($item['stock_id'], $order->customer_currency,
//                        $order->sales_type,	$order->price_factor, get_post('OrderDate'), true);
//
//                    // rounding differences are included in last price item in kit
//                    $item_num--;
//                    if ($item_num) {
//                        $price -= $item['quantity']*$std_price*$price_factor;
//                        $item_price = $std_price*$price_factor;
//                    } else {
//                        if ($item['quantity'])
//                            $price = $price/$item['quantity'];
//                        $item_price = $price;
//                    }
//                    $item_price = round($item_price, get_qty_dec($_POST['stock_id']));

                    $prefs=get_company_prefs();
                    if( $prefs['disc_in_amount'] == 1){

                        $disc = input_num('Disc') ;

                    }
                    else{
                        $disc = input_num('Disc') / 100;
                    }
                    $purchase_price = get_purchase_price($_POST['supplier_id'], $item['stock_id']);
                    if (!$item['is_foreign'] && $item['item_code'] != $item['stock_id']) {	// this is sales kit - recurse
                        $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $item['stock_id'], input_num('qty')*$item['quantity'],
                            '', //$myrow["description"],
                            $purchase_price, '', // $myrow["units"], (retrived in cart)
                            ($_SESSION['PO']->trans_type == ST_PURCHORDER ) ? $_POST['req_del_date'] : '', 0, 0,
                            $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],$_POST['text7'],
                            $_POST['amount1'], $_POST['amount2'], $_POST['amount3'], $_POST['amount4'], $_POST['amount5'], $_POST['amount6'],
                            $_POST['date1'], $_POST['date2'], $_POST['date3'],
                            $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
                            $_POST['batch'], $_POST['exp_date'], $_POST['con_factor'],$_POST['units_id'],null,null,null,null,$disc);
                    }
                    else {	// stock item record eventually with foreign code
                        // check duplicate stock item

                        $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $_POST['stock_id'], input_num('qty'),
                            get_post('stock_id_text'), //$myrow["description"],
                            input_num('price'), '', // $myrow["units"], (retrived in cart)
                            ($_SESSION['PO']->trans_type == ST_PURCHORDER) ? $_POST['req_del_date'] : '', 0, 0,
                            $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],$_POST['text7'],
                            $_POST['amount1'], $_POST['amount2'], $_POST['amount3'], $_POST['amount4'], $_POST['amount5'], $_POST['amount6'],
                            $_POST['date1'], $_POST['date2'], $_POST['date3'],
                            $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
                            $_POST['batch'], $_POST['exp_date'], $_POST['con_factor'],$_POST['units_id'],null,null,null,null,$disc);
                    }
                }
//                $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $_POST['stock_id'], input_num('qty'),
//                    get_post('stock_id_text'), //$myrow["description"],
//                    input_num('price'), '', // $myrow["units"], (retrived in cart)
//                    $_SESSION['PO']->trans_type == ST_PURCHORDER ? $_POST['req_del_date'] : '', 0, 0,
//                    $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],
//                    $_POST['amount1'], $_POST['amount2'], $_POST['amount3'], $_POST['amount4'], $_POST['amount5'], $_POST['amount6'],
//                    $_POST['date1'], $_POST['date2'], $_POST['date3'],
//                    $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
//                    $_POST['batch'], $_POST['exp_date'], $_POST['con_factor'],$_POST['units_id']);

                unset_form_variables();
                $_POST['stock_id']	= "";
            }
//            else
//            {
//                display_error(_("The selected item does not exist or it is a kit part and therefore cannot be purchased."));
//            }

        } /* end of if not already on the order and allow input was true*/
    }
    line_start_focus();
}



// function handle_add_new_item()
// {
//     $allow_update = check_data();

//     if ($allow_update == true)
//     {
//         if (count($_SESSION['PO']->line_items) > 0)
//         {
//             foreach ($_SESSION['PO']->line_items as $order_item)
//             {
//                 /* do a loop round the items on the order to see that the item
//                 is not already on this order */
//                 if (($order_item->stock_id == $_POST['stock_id']))
//                 {
//                     display_warning(_("The selected item is already on this order."));
//                 }
//             } /* end of the foreach loop to look for pre-existing items of the same code */
//         }

//         if ($allow_update == true)
//         {
//             $result = get_short_info($_POST['stock_id']);

//             if (db_num_rows($result) == 0)
//             {
//                 $allow_update = false;
//             }

//             if ($allow_update)
//             {

//                 $item_info = get_item_edit_info($_POST['stock_id']);

//                 $formula = $item_info["formula"];

//                 $value = explode(",", $formula);
//                 $maxs = max(array_keys($value));
//                 $amount3 = $value[0];
//                 $amount2 = $value[2];
//                 if($formula != '') {
//                     if ($_POST[$value[$maxs]] == 1) {
//                         $b = ($_POST[$amount3] . "" . $value[1] . "" . $_POST[$amount2]);
//                         $_POST[$value[$maxs]] = eval('return '.$b.';');
//                     }

//                 }

//                 $_SESSION['PO']->add_to_order (count($_SESSION['PO']->line_items), $_POST['stock_id'], input_num('qty'),
//                     get_post('stock_id_text'), //$myrow["description"],
//                     input_num('price'), '', // $myrow["units"], (retrived in cart)
//                     $_SESSION['PO']->trans_type == ST_PURCHORDER ? $_POST['req_del_date'] : '', 0, 0,
//                     $_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'],
//                     $_POST['amount1'], $_POST['amount2'], $_POST['amount3'], $_POST['amount4'], $_POST['amount5'], $_POST['amount6'],
//                     $_POST['date1'], $_POST['date2'], $_POST['date3'],
//                     $_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
//                     $_POST['batch'], $_POST['exp_date'], $_POST['con_factor'],$_POST['units_id']);

//                 unset_form_variables();
//                 $_POST['stock_id']	= "";
//             }
//             else
//             {
//                 display_error(_("The selected item does not exist or it is a kit part and therefore cannot be purchased."));
//             }

//         } /* end of if not already on the order and allow input was true*/
//     }
//     line_start_focus();
// }

//---------------------------------------------------------------------------------------------------
function can_commit()
{
    $pref=get_company_prefs();
    if($pref['batch'] == 1) {
        if ($_SESSION['PO']->trans_type == 20) {
            foreach ($_SESSION['PO']->line_items as $order_line) {
                $itm = get_item($order_line->stock_id);

                if ($itm['batch_status'] != 1 && $order_line->grn_batch == "") {
                    display_error(_("Null Batch not allowed."));
                    return false;

                }

            }
        }
    }

 if (input_num('discount1') > input_num('sub_total')  )
    {
        display_error(_("Discount  can not exceed total purchase amount ."));
        set_focus('location');
        return false;
    }


    if (!get_post('supplier_id'))
    {
        display_error(_("There is no supplier selected."));
        set_focus('supplier_id');
        return false;
    }
	if (!get_post('StkLocation')) 
	{
		display_error(_("There is no location selected."));
		set_focus('StkLocation');
		return false;
	} 
    if (!is_date($_POST['OrderDate']))
    {
        display_error(_("The entered order date is invalid."));
        set_focus('OrderDate');
        return false;
    }
    if (($_SESSION['PO']->trans_type == ST_SUPPRECEIVE || $_SESSION['PO']->trans_type == ST_SUPPINVOICE)
        && !is_date_in_fiscalyear($_POST['OrderDate'])) {
        display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('OrderDate');
        return false;
    }

    if (($_SESSION['PO']->trans_type==ST_SUPPINVOICE) && !is_date($_POST['due_date']))
    {
        display_error(_("The entered due date is invalid."));
        set_focus('due_date');
        return false;
    }

    if (!$_SESSION['PO']->order_no)
    {
        if (!check_reference(get_post('ref'), $_SESSION['PO']->trans_type))
        {
            set_focus('ref');
            return false;
        }
    }

    if ($_SESSION['PO']->trans_type == ST_SUPPINVOICE && trim(get_post('supp_ref')) == false)
    {
        display_error(_("You must enter a supplier's invoice reference."));
        set_focus('supp_ref');
        return false;
    }
    if ($_SESSION['PO']->trans_type==ST_SUPPINVOICE
        && is_reference_already_there($_SESSION['PO']->supplier_id, get_post('supp_ref'), $_SESSION['PO']->order_no))
    {
        display_error(_("This invoice number has already been entered. It cannot be entered again.") . " (" . get_post('supp_ref') . ")");
        set_focus('supp_ref');
        return false;
    }
    if ($_SESSION['PO']->trans_type == ST_PURCHORDER && get_post('delivery_address') == '')
    {
        display_error(_("There is no delivery address specified."));
        set_focus('delivery_address');
        return false;
    }
    if (get_post('StkLocation') == '')
    {
        display_error(_("There is no location specified to move any items into."));
        set_focus('StkLocation');
        return false;
    }
    if (!db_has_currency_rates($_SESSION['PO']->curr_code, $_POST['OrderDate'], true))
        return false;
    if ($_SESSION['PO']->order_has_items() == false)
    {
        display_error (_("The order cannot be placed because there are no lines entered on this order."));
        return false;
    }
    if (floatcmp(input_num('prep_amount'), $_SESSION['PO']->get_trans_total()) > 0)
    {
        display_error(_("Required prepayment is greater than total invoice value."));
        set_focus('prep_amount');
        return false;
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

        if($row2 > $now )
        {
            display_error("You are not allowed to enter data $allowed_time pm");
            return false ;
        }

    }
    return true;
}

function handle_commit_order()
{
    $cart = &$_SESSION['PO'];


    foreach ($_SESSION['PO']->line_items as $line=>$itm)
    {
        if (check_num('qty'.$line))
        {
            //display_notification(_("This sales quantity is updated"), 1);
            //$_SESSION['PO']->line_items[$line]->qty_dispatched = input_num('qty'.$line);
            $_SESSION['PO']->line_items[$line]->quantity = input_num('qty'.$line);
            //display_error("Qwerty".input_num('qty'.$line));
        }
    }


    if (can_commit()) {

        copy_to_cart();
        new_doc_date($cart->orig_order_date);
        if ($cart->order_no == 0) { // new po/grn/invoice
            $trans_no = add_direct_supp_trans($cart);
            if ($trans_no) {
                unset($_SESSION['PO']);
                if ($cart->trans_type == ST_PURCHORDER)
                    meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
                elseif ($cart->trans_type == ST_SUPPRECEIVE)
                    meta_forward($_SERVER['PHP_SELF'], "AddedGRN=$trans_no");
                else
                    meta_forward($_SERVER['PHP_SELF'], "AddedPI=$trans_no");
            }
        } else { // order modification
            $order_no = update_po($cart);
            unset($_SESSION['PO']);
            meta_forward($_SERVER['PHP_SELF'], "AddedID=$order_no&Updated=1");
        }
    }
}
//---------------------------------------------------------------------------------------------------
if (isset($_POST['update'])) {
    copy_to_cart();
    $Ajax->activate('items_table');
}

$id = find_submit('Delete');
if ($id != -1)
    handle_delete_item($id);

if (isset($_POST['Commit']))
{
    handle_commit_order();
}
if (isset($_POST['UpdateLine']))
    handle_update_item();

if (isset($_POST['EnterLine']))
    handle_add_new_item();

if (isset($_POST['CancelOrder']))
    handle_cancel_po();

if (isset($_POST['CancelUpdate']))
    unset_form_variables();

if (isset($_POST['CancelUpdate']) || isset($_POST['UpdateLine'])) {
    line_start_focus();
}
if(isset($_POST['getitems'])){
    handle_last_purchase_items();
}

//---------------------------------------------------------------------------------------------------

start_form();

display_po_header($_SESSION['PO']);
echo "<br>";

display_po_items($_SESSION['PO']);

start_table(TABLESTYLE2);


if ($_SESSION['PO']->trans_type == ST_SUPPINVOICE) {
    cash_accounts_list_row(_("Payment:"), 'cash_account', null, false, _('Delayed'));
}
global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='WTRUCK') {
    textarea_row(_("Delivery:"), 'Comments1', null, 70, 4);
    textarea_row(_("Warranty:"), 'Comments2', null, 70, 4);
}

global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SKF') {

    text_row(_("Purchase Person:"), 'h_text1', null, 16, 15);
}
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'){

textarea_row(_("Memo:"), 'Comments', "
1.Purchase order number must be mentioned in invoice , delivery challan and all relevent documents.
2..Only 10% +/-  quantity variation are acceptable..
3.Payment as agreed terms .
4.All taxes levied by the government are applicable unless exemption certificates are provided.
5.All Supplies must be made between 8:00am to 5:00pm (Monday to Saturday).
", 150, 7);
    
}
else{
textarea_row(_("Memo:"), 'Comments', null, 70, 4);
}
$myrow7 = get_company_purch_pref('footer_long_text1');
if($myrow7['po_enable']==0){}
else
{
    textarea_row($myrow7["label_value"]._(""), 'Comments1', null, 70, 4);

}
//
////
//


$supp = get_supplier($_POST['supplier_id']);
$_POST['Comments2'] = $supp['notes'];
$myrow8 = get_company_purch_pref('footer_long_text2');
if($myrow8['po_enable']==0){}
else
{
    textarea_row($myrow8["label_value"]._(""), 'Comments2', null, 70, 4);

}


$myrow9 = get_company_purch_pref('footer_long_text3');
if($myrow9['po_enable']==0){}
else {
    textarea_row($myrow9["label_value"]._(""), 'Comments3', null, 70, 4);
}
$myrow10 = get_company_purch_pref('footer_long_text4');
if($myrow10['po_enable']==0){}
else {
    textarea_row($myrow10["label_value"]._(""), 'Comments4', null, 70, 4);
}
$myrow11 = get_company_purch_pref('footer_long_text5');
if($myrow11['po_enable']==0){}
else {
    textarea_row($myrow11["label_value"]._(""), 'Comments5', null, 70, 4);
}


end_table(1);

div_start('controls', 'items_table');
$process_txt = _("Place Order");
$update_txt = _("Update Order");
$cancel_txt = _("Cancel Order");
if ($_SESSION['PO']->trans_type == ST_SUPPRECEIVE) {
    $process_txt = _("Process GRN");
    $update_txt = _("Update GRN");
    $cancel_txt = _("Cancel GRN");
}
elseif ($_SESSION['PO']->trans_type == ST_SUPPINVOICE) {
    $process_txt = _("Process Invoice");
    $update_txt = _("Update Invoice");
    $cancel_txt = _("Cancel Invoice");
}
if ($_SESSION['PO']->order_has_items())
{
    if ($_SESSION['PO']->order_no)
        submit_center_first('Commit', $update_txt, '', 'default');
    else
        submit_center_first('Commit', $process_txt, '', 'default');
    submit_center_last('CancelOrder', $cancel_txt);
}
else
    submit_center('CancelOrder', $cancel_txt, true, false, 'cancel');
div_end();
//---------------------------------------------------------------------------------------------------

end_form();
end_page();
