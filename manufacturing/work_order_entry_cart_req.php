<?php

$page_security = 'SA_WORKORDERENTRYREQ';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/wo_items_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/manufacturing.inc");
include_once($path_to_root . "/manufacturing/includes/work_order_entry_ui_cart_req.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

if (isset($_GET['NewWorkOrder'])) {
	if (isset($_GET['FixedAsset'])) {
		$page_security = 'SA_ASSETTRANSFER';
		$_SESSION['page_title'] = _($help_context = "Fixed Assets Location Transfers");
	}
	else {
		$_SESSION['page_title'] = _($help_context = "Work Order Entry");
	}
}
page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------------------------

check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
    $id = $_GET['AddedID'];
    $stype = ST_WORKORDER;
    include_once($path_to_root . "/reporting/includes/reporting.inc");
    display_notification_centered(_("The work order requisition has been added."));

    display_note(get_trans_view_str($stype, $id, _("View this Work Order")), 0, 1);
    if ($_GET['type'] == WO_ADVANCED)
        submenu_print(_("&Print This Work Order"), WO_ADVANCED, $id, 'prtopt');

    if ($_GET['type'] != WO_ADVANCED)
    {
        // include_once($path_to_root . "/reporting/includes/reporting.inc");

        submenu_print(_("&Print This Work Order"), ST_WORKORDER, $id, 'prtopt');
        submenu_print(_("&Email This Work Order"), ST_WORKORDER, $id, null, 1);
        display_note(get_gl_view_str($stype, $id, _("View the GL Journal Entries for this Work Order")), 1);
        $ar = array('PARAM_0' => $_GET['date'], 'PARAM_1' => $_GET['date'], 'PARAM_2' => $stype, 'PARAM_3' => '',
            'PARAM_4' => (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0));
        display_note(print_link(_("Print the GL Journal Entries for this Work Order"), 702, $ar), 1);
        hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$stype&trans_no=$id");
    }

    display_footer_exit();
}

if (isset($_GET['UpdatedID']))
{
    $id = $_GET['UpdatedID'];
    $stype = ST_WORKORDER;
    include_once($path_to_root . "/reporting/includes/reporting.inc");
    display_notification_centered(_("The work order requisition has been updated."));

    display_note(get_trans_view_str($stype, $id, _("View this Work Order")), 0, 1);
    if ($_GET['type'] == WO_ADVANCED)
        submenu_print(_("&Print This Work Order"), WO_ADVANCED, $id, 'prtopt');

    if ($_GET['type'] != WO_ADVANCED)
    {
        // include_once($path_to_root . "/reporting/includes/reporting.inc");

        submenu_print(_("&Print This Work Order"), ST_WORKORDER, $id, 'prtopt');
        submenu_print(_("&Email This Work Order"), ST_WORKORDER, $id, null, 1);
        display_note(get_gl_view_str($stype, $id, _("View the GL Journal Entries for this Work Order")), 1);
        $ar = array('PARAM_0' => $_GET['date'], 'PARAM_1' => $_GET['date'], 'PARAM_2' => $stype, 'PARAM_3' => '',
            'PARAM_4' => (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0));
        display_note(print_link(_("Print the GL Journal Entries for this Work Order"), 702, $ar), 1);
        hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$stype&trans_no=$id");
    }

    display_footer_exit();
}
/*if (isset($_GET['AddedID']))
{
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_LOCTRANSFER;

	display_notification_centered(_("Inventory transfer has been processed"));
	display_note(get_trans_view_str($trans_type, $trans_no, _("&View this transfer")));

//  $itm = db_fetch(get_stock_WorkOrder($_GET['AddedID']));
//  if (is_fixed_asset($itm['mb_flag']))
//	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Fixed Assets Transfer"), "NewTransfer=1&FixedAsset=1");
//  else
//	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Inventory Transfer"), "NewTransfer=1");

	display_footer_exit();
}*/
//--------------------------------------------------------------------------------------------------
//if(list_updated('type'))
//{
	global $Ajax;
	$Ajax->activate('footer');
//}

//--------------------------------------------------------------------------------------------------

function line_start_focus() {
  global $Ajax;

  $Ajax->activate('items_table');
  set_focus('_stock_id_edit');
}
//-----------------------------------------------------------------------------------------------
function copy_from_wo_cart()
{
    $cart = &$_SESSION['WorkOrderReq'];
    $_POST['wo_ref'] = $cart->wo_ref;
    $_POST['type_'] = $cart->wo_type;
    $_POST['stock_id_'] = $cart->stock_id_;
    $_POST['StockLocation'] = $cart->StockLocation;
    $_POST['amount3'] = $cart->amount3;
    $_POST['quantity'] = $cart->quantity;
    $_POST['date_'] = $cart->date_;
    $_POST['memo_'] = $cart->memo_;
    $_POST['Labour'] = $cart->Labour;
    $_POST['cr_lab_acc'] = $cart->cr_lab_acc;
    $_POST['Costs'] = $cart->Costs;
    $_POST['cr_acc'] = $cart->cr_acc;
    $_POST['sale_order'] = $cart->sale_order;
}
//-----------------------------------------------------------------------------------------------

function handle_new_order($WorkOrder)
{
//	if (isset($_SESSION['WorkOrderReq'])) {
//		$_SESSION['WorkOrderReq']->clear_items();
//		unset ($_SESSION['WorkOrderReq']);
//	}

    processing_start_wo();

    $_SESSION['WorkOrderReq'] = new items_cart_wo(ST_MANUORDERREQ, $WorkOrder);
  	$_SESSION['WorkOrderReq']->fixed_asset = isset($_GET['FixedAsset']);
  	$_SESSION['WorkOrderReq']->WTpe = $WorkOrder;
	$_POST['AdjDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['AdjDate']))
		$_POST['AdjDate'] = end_fiscalyear();
	$_SESSION['WorkOrderReq']->tran_date = $_POST['AdjDate'];

    copy_from_wo_cart();
}

//-----------------------------------------------------------------------------------------------
function can_process()
{
    global $selected_id, $SysPrefs, $Refs;

    if (!isset($selected_id)) {
        if (!$Refs->is_valid($_POST['wo_ref'], ST_MANUORDERREQ)) {
            display_error(_("You must enter a reference."));
            set_focus('wo_ref');
            return false;
        }
        if (!is_new_reference($_POST['wo_ref'], ST_MANUORDERREQ)) {
            display_error(_("The entered reference is already in use."));
            set_focus('wo_ref');
            return false;
        }
    }
    if (!check_num('quantity', 0)) {
        display_error( _("The quantity entered is invalid or less than zero."));
        set_focus('quantity');
        return false;
    }

    if (!is_date($_POST['date_'])) {
        display_error( _("The date entered is in an invalid format."));
        set_focus('date_');
        return false;
    } elseif (!is_date_in_fiscalyear($_POST['date_'])) {
        display_error(_("The entered date is not in fiscal year."));
        set_focus('date_');
        return false;
    }
    // only check bom and quantites if quick assembly
    if (!($_POST['type'] == WO_ADVANCED)) {
        if ( count($_SESSION['WorkOrderReq']->line_item_wo) == 0) {
                display_error(_("You must enter at least one non empty item line ."));
                set_focus('_stock_id__edit');
                return false;
            }

        if ($_POST['Labour'] == "")
            $_POST['Labour'] = price_format(0);
        if (!check_num('Labour', 0)) {
            display_error( _("The labour cost entered is invalid or less than zero."));
            set_focus('Labour');
            return false;
        }
        if ($_POST['Costs'] == "")
            $_POST['Costs'] = price_format(0);
        if (!check_num('Costs', 0)) {
            display_error(_("The cost entered is invalid or less than zero."));
            set_focus('Costs');
            return false;
        }
        if (!$SysPrefs->allow_negative_stock()) {
            if ($_POST['type'] == WO_ASSEMBLY) {
                // check bom if assembling
//                $result = get_bom($_POST['stock_id']);

//                while ($bom_item = db_fetch($result))
                foreach ($_SESSION['WorkOrderReq']->line_item_wo as $line_no=>$stock_item) {
                    $item_row = get_item($stock_item->stock_id);
                    if (has_stock_holding($item_row['mb_flag'])) {
//                      $ids = find_submit('qty');
//                		$quantity = $bom_item["quantity"] * input_num('quantity');
                        $quantity = /*input_num('qty'.$ids)*/$stock_item->issue_qty * input_num('quantity');
                        $qoh = get_qoh_on_date($stock_item->stock_id, $stock_item->loc_code1, $_POST['date_']);
                        if (-$quantity + $qoh < 0) {
                            display_error(_("The work order cannot be processed because there is an insufficient quantity for component:") .
                                " " . $stock_item->stock_id . " - " .  $stock_item->item_description . ".  " . _("Location:") . " " . $stock_item->loc_code1);
                            set_focus('quantity');
                            return false;
                        }
                    }
                }
            }
            elseif ($_POST['type'] == WO_UNASSEMBLY) {
                // if unassembling, check item to unassemble
                $qoh = get_qoh_on_date($_POST['stock_id_'], $_POST['StockLocation'], $_POST['date_']);
                if (-input_num('quantity') + $qoh < 0) {
                    display_error(_("The selected item cannot be unassembled because there is insufficient stock."));
                    return false;
                }
            }
        }
    } else {
        if (!is_date($_POST['RequDate'])) {
            set_focus('RequDate');
            display_error( _("The date entered is in an invalid format."));
            return false;
        }
        elseif (!is_date_in_fiscalyear($_POST['RequDate']))
        {
        	display_error(_("The entered date is not in fiscal year."));
        	return false;
        }
        if (isset($selected_id)) {
//            $myrow = get_work_order($selected_id, true);

            if ($_POST['units_issued'] > input_num('quantity')) {
                set_focus('quantity');
                display_error(_("The quantity cannot be changed to be less than the quantity already manufactured for this order."));
                return false;
            }
        }
    }
    $row = get_company_pref('back_days');
    $row1 = get_company_pref('future_days');
    $row2 = get_company_pref('deadline_time');
    if($row != '') {
        $diff   =  date_diff2(date('d-m-Y'),$_POST['date_'], 'd');
        if($row == 0) {
            $allowed_days = 'before yesterday.';
        }
        else
            $allowed_days =  'more than '. $row . ' day old' ;
        if($diff > $row  ){
            display_error("You are not allowed to enter entries $allowed_days");
            return false;
        }
    }
    if($row1 != '') {
        $diff_futuredays   =  date_diff2($_POST['date_'],date('d-m-Y'), 'd');
        if( $diff_futuredays > $row1) {
            display_error("You are not allowed to enter data $row1 day/s ahead");
            return false ;
        }
    }
    if($row2 != '') {
        $now = date('h:i:s');
        if($row2 != 0) {
            $allowed_time = 'after '. $row2;
        }
        else
            $allowed_time=  '' ;
        if($row2 < $now ) {
            display_error("You are not allowed to enter data $allowed_time pm");
            return false ;
        }
    }
    return true;
}

function handle_cancel_order_req()
{
    global $path_to_root;
    delete_work_order_req($_SESSION['WorkOrderReq']->Editable, ST_MANUORDERREQ, $_POST['date_']);
    meta_forward("$path_to_root/manufacturing/search_work_orders_req.php", "cancel=".$_SESSION['WorkOrderReq']->Editable);
    processing_end();
    display_footer_exit();
}


//-------------------------------------------------------------------------------

if (isset($_POST['Process']) && can_process())
{

    if (!isset($_POST['cr_acc']))
        $_POST['cr_acc'] = "";
    if (!isset($_POST['cr_lab_acc']))
        $_POST['cr_lab_acc'] = "";
    if($_SESSION['WorkOrderReq']->order_id == 0) {
        $WorkOrderId = add_work_order_req($_POST['wo_ref'], $_POST['StockLocation'],
            input_num('quantity'), $_POST['stock_id_'],  $_POST['type_'],
            $_POST['date_'], $_POST['RequDate'], $_POST['memo_'],
            input_num('Costs'), $_POST['cr_acc'],
            input_num('Labour'), $_POST['cr_lab_acc'],0,0,$_POST['sale_order'],
            $_SESSION['WorkOrderReq']);
        $URl = 'AddedID';
    } else {

        $WorkOrderId = update_work_order_req($_POST['wo_ref'], $_POST['StockLocation'],
            input_num('quantity'), $_POST['stock_id_'],  $_POST['type_'],
            $_POST['date_'], $_POST['RequDate'], $_POST['memo_'],
            input_num('Costs'), $_POST['cr_acc'],
            input_num('Labour'), $_POST['cr_lab_acc'],0,0,$_POST['sale_order'],
            $_SESSION['WorkOrderReq']);
        $URl = 'UpdatedID';
    }
    new_doc_date($_POST['date_']);

//    processing_end_wo();
    meta_forward("$path_to_root/manufacturing/search_work_orders_req.php", "$URl=$WorkOrderId");
} /*end of process Work order requisition */

//-----------------------------------------------------------------------------------------------
$Edit = find_submit('Edit');
$Delete = find_submit('Delete');
if( list_updated('stock_id_'))
{
	$_SESSION['WorkOrderReq']->clear_items();
	$result = get_bom($_POST['stock_id_']);
	$stock_id = '';
	while ($myrow = db_fetch($result)) {
		add_to_order($_SESSION['WorkOrderReq'], $myrow['component'], input_num('quantity'),
			$myrow['LocationCode'], $myrow['ID'], $myrow["quantity"], $_POST["amount33"]);
	}	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (!check_num('issue_qty', 0) || input_num('issue_qty') == 0)
	{
		display_error(_("The quantity entered must be a positive number."));
		set_focus('issue_qty');
		return false;
	}
   	return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item()
{
	$id = $_POST['LineNo'];
   	$_SESSION['WorkOrderReq']->update_cart_item($id, input_num('quantity'),
		$_POST['loc_code1'], $_POST['workcentre_added1'], $_POST['amount33'], input_num('issue_qty'));
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id)
{
	$_SESSION['WorkOrderReq']->remove_from_cart($id);
	line_start_focus();
}
//-----------------------------------------------------------------------------------------------
function handle_new_item()
{
    $Run = 0; // for Line start focus
	if (!isset($_POST['std_cost']))
   		$_POST['std_cost'] = 0;
    if($_POST['stock_id_'] != '') {
        // if (!check_for_recursive_bom($_POST['stock_id_'], $_POST['stock_id'])) {
        //     /*Now check to see that the component is not already on the bom */
        //     if (!is_component_already_on_bom($_POST['stock_id'], $_POST['workcentre_added'],
        //         $_POST['loc_code'], $_POST['stock_id_'])) {
                add_to_order($_SESSION['WorkOrderReq'], $_POST['stock_id'], input_num('quantity'),
                    $_POST['loc_code1'], $_POST['workcentre_added1'], input_num('issue_qty'),$_POST['amount33']);
        //     } else {
        //         /*The component must already be on the bom */
        //         display_error(_("The selected component is already on this bom. You can modify it's quantity but it cannot appear more than once on the same bom."));
        //     }
        // } //end of if its not a recursive bom
        // else {
        //     display_error(_("The selected component is a parent of the current item. Recursive BOMs are not allowed."));
        // }
    } else {
        display_error(_("Please select item."));
        set_focus('_stock_id__edit');
        $Run = 1;
    }
    if($Run == 0)
	    line_start_focus();
}
//-----------------------------------------------------------------------------------------------
if (isset($_POST['cancel'])){
    handle_cancel_order_req();
}

$id = find_submit('Delete');
if ($id != -1)
	handle_delete_item($id);

if (isset($_POST['AddItem']) && check_item_data())
	handle_new_item();

if (isset($_POST['UpdateItem']) && check_item_data())
	handle_update_item();

if (isset($_POST['CancelItemChanges'])) {
	line_start_focus();
}
//-----------------------------------------------------------------------------------------------
if (isset($_GET['NewWorkOrder']) || !isset($_SESSION['WorkOrderReq']))
{
	if (isset($_GET['fixed_asset']))
		check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
	else
		check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

	handle_new_order(0);
}
elseif (isset($_GET['WorkOrder']) || !isset($_SESSION['WorkOrderReq']))
{

    if (isset($_GET['fixed_asset']))
        check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
    else
        check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

    handle_new_order($_GET['WorkOrder']);
}
elseif (isset($_GET['ModifyOrderNumber']) || !isset($_SESSION['WorkOrderReq']))
{
    if (isset($_GET['fixed_asset']))
        check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
    else
        check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

    handle_new_order($_GET['ModifyOrderNumber']);
}
//-----------------------------------------------------------------------------------------------

if ($_SESSION['WorkOrderReq']->WTpe == 0) {
    $porder = _("Process");
} else {
    $porder = _("Update");
}
//-----------------------------------------------------------------------------------------------
start_form();

global $Ajax;
display_order_header($_SESSION['WorkOrderReq']);

div_start('footer');
start_table(TABLESTYLE, "width='60%'", 1);
//start_row();

echo "<td> <center>";
if ($_POST['type_'] != WO_ADVANCED)
display_adjustment_items(_("BOM"), $_SESSION['WorkOrderReq']);
else
{
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO' ||
        $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IMEC' ||
        $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='NKR')
        display_wo_bom_cart($_POST['stock_id_'], 1);
}
table_section(1);
/*
global  $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO') {
    $myrow_1 = get_company_item_pref_from_position(1);
    $myrow_2 = get_company_item_pref_from_position(2);
    $myrow_3 = get_company_item_pref_from_position(3);
    $myrow_4 = get_company_item_pref_from_position(4);
    $myrow_5 = get_company_item_pref_from_position(5);
    $myrow_6 = get_company_item_pref_from_position(6);

    $item_info = get_item_edit_info($_POST['stock_id']);


    $_POST['amount1'] = price_format($item_info["amount1"]);
    $_POST['amount2'] = price_format($item_info["amount2"]);
    $_POST['amount4'] = price_format($item_info["amount4"]);


    if (!isset($_POST['amount3'])) {
        $_POST['amount3'] = price_format($item_info["amount3"]);
    }


    $formula = $item_info["formula"];

    $value = explode(",", $formula);
    $maxs = max(array_keys($value));
    $amount3 = $value[0];
    $amount2 = $value[2];


    if ($formula != '') {
        $b = $_POST[$amount3] . "" . $value[1] . "" . $_POST[$amount2];

        $_POST['quantity'] = eval('return ' . $b . ';');
    }
    echo "<tr>";
    echo "<td>";
    ref_row($myrow_3['label_value'], $myrow_3['name'], null, null, true);
    echo "</td>";
    echo "</tr>";
}
*/

if ($_POST['type_'] == WO_ADVANCED) {
// 	ref_cells("Quantity Required:", 'quantity', null, null, null, true);
// 	if ($_POST['released'])
// 		label_row(_("Quantity Manufactured:"), number_format($_POST['units_issued'], get_qty_dec($_POST['stock_id'])));
// 	date_row(_("Date") . ":", 'date_', '', true);
	date_row(_("Date Required By") . ":", 'RequDate', '', null, $SysPrefs->default_wo_required_by());
} else {
// 	ref_cells("Quantity:", 'quantity', null, null, null, true);
// 	date_row(_("Date") . ":", 'date_', '', true);
	hidden('RequDate', '');

	$sql = "SELECT DISTINCT account_code FROM ".TB_PREF."bank_accounts";
	$rs = db_query($sql,"could not get bank accounts");
	$r = db_fetch_row($rs);
	if (!isset($_POST['Labour'])) {
		$_POST['Labour'] = price_format(0);
		$_POST['cr_lab_acc'] = $r[0];
	}
	amount_row($wo_cost_types[WO_LABOUR], 'Labour');
	gl_all_accounts_list_row(_("Credit Labour Account"), 'cr_lab_acc', null);
	if (!isset($_POST['Costs'])) {
		$_POST['Costs'] = price_format(0);
		$_POST['cr_acc'] = $r[0];
	}
	amount_row($wo_cost_types[WO_OVERHEAD], 'Costs');
	gl_all_accounts_list_row(_("Credit Overhead Account"), 'cr_acc', null);
}

echo "</td>";
end_outer_table(1);
//end_row();
end_table(1);
div_end();
submit_center_first('Process', $porder, '',  'default');
submit_center_last('cancel', _("Cancel"), '',  null);
end_form();
end_page();