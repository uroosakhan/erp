<?php

$page_security = 'SA_MANUFISSUE';
$path_to_root = "..";

include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
include_once($path_to_root . "/manufacturing/includes/work_order_issue_ui.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

page(_($help_context = "Issue Items to Work Order"), false, false, "", $js);

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$id = $_GET['AddedID'];
   	display_notification(_("The work order issue has been entered."));

    display_note(get_trans_view_str(ST_WORKORDER, $id, _("View this Work Order")));

   	display_note(get_gl_view_str(ST_WORKORDER, $id, _("View the GL Journal Entries for this Work Order")), 1);

   	hyperlink_no_params("search_work_orders.php", _("Select another &Work Order to Process"));

	display_footer_exit();
}
//--------------------------------------------------------------------------------------------------

function line_start_focus() {
  global $Ajax;

  $Ajax->activate('items_table');
  set_focus('_stock_id_edit');
}

//--------------------------------------------------------------------------------------------------

function handle_new_order()
{
	if (isset($_SESSION['issue_items']))
	{
		$_SESSION['issue_items']->clear_items();
		unset ($_SESSION['issue_items']);
	}

     $_SESSION['issue_items'] = new items_cart(ST_MANUISSUE);
     $_SESSION['issue_items']->order_id = $_GET['trans_no'];
}

//-----------------------------------------------------------------------------------------------
function can_process()
{
	if (!is_date($_POST['date_']))
	{
		display_error(_("The entered date for the issue is invalid."));
		set_focus('date_');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_POST['date_']))
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('date_');
		return false;
	}
	if (!check_reference($_POST['ref'], ST_MANUISSUE))
	{
		set_focus('ref');
		return false;
	}

	$failed_item = $_SESSION['issue_items']->check_qoh($_POST['Location'], $_POST['date_'], !$_POST['IssueType']);
	if ($failed_item)
	{
   		display_error(_("The issue cannot be processed because it would cause negative inventory balance for marked items as of document date or later.".$failed_item[0]));
		return false;
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

		if($diff > $row) {
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
	if($row1 != '') {
	    $diff_futuredays   =  date_diff2($_POST['date_'],date('d-m-Y'), 'd');
		if( $diff_futuredays > $row1) {
		    display_error("You are not allowed to enter data $row1 day/s ahead");
            return false ;
		}
	}
	if($row2 != '')
	{
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

if (isset($_POST['Process']) && can_process())
{
    $AllowProcess = 0;
    if(count($_SESSION['issue_items']->line_items) != 0)
    {
        foreach ($_SESSION['issue_items']->line_items as $line_no => $stock_item)
        {
            $qoh = get_qoh_on_date($stock_item->stock_id, null, null);
            $quantity = $stock_item->quantity - $qoh;
            $short_qty = 0;
            if($quantity > 0)
                $short_qty = $quantity;
            $Quantity = $stock_item->quantity - $short_qty;
            $issue_no = get_work_order_issue_no($_SESSION['issue_items']->order_id);
            $get_issued_items = get_wo_issue_items($issue_no['issue_no'], $stock_item->stock_id);

            if($get_issued_items['id'] != 0) {
                $Quantity = $Quantity ;
            }

            if($Quantity <= 0 && !$SysPrefs->allow_negative_stock()) {
                display_error(_("The process cannot be completed because there is an insufficient total quantity for a component.") . "<br>"
                    . _("Component is :"). $stock_item->stock_id . "<br>");
                $AllowProcess = 1;
            }

        }
    } else {
        $result = get_bom($_POST['Stock']);
        while ($myrow = db_fetch($result))
        {
            $qoh = get_qoh_on_date($myrow["component"], null, null);
            $quantity = $myrow["quantity"] - $qoh;
            $short_qty = 0;
            if($quantity > 0)
                $short_qty = $quantity;
            $Quantity = $myrow["quantity"] - $short_qty;
            $issue_no = get_work_order_issue_no($_SESSION['issue_items']->order_id);
            $get_issued_items = get_wo_issue_items($issue_no['issue_no'], $myrow["component"]);
            if($get_issued_items['id'] != 0) {
                $Quantity = $Quantity - $get_issued_items['qty_issued'];
            }
            if($Quantity < 0)
                $Quantity = 0;
            if($Quantity <= 0) {
                display_error(_("The process cannot be completed because there is an insufficient total quantity for a component.") . "<br>"
                    . _("Component is :"). $myrow["component"] . "<br>");
                $AllowProcess = 1;
            }
        }
    }

	// if failed, returns a stockID
    if($AllowProcess == 0)
    {
        $failed_data = add_work_order_issue($_SESSION['issue_items']->order_id,
            $_POST['ref'], $_POST['IssueType'], $_SESSION['issue_items']->line_items,
            $_POST['Location'], $_POST['WorkCentre'], $_POST['date_'], $_POST['memo_']);

        if ($failed_data != null) {
            display_error(_("The process cannot be completed because there is an insufficient total quantity for a component.") . "<br>"
                . _("Component is :"). $failed_data[0] . "<br>"
                . _("From location :"). $failed_data[1] . "<br>");
        }
        else {
            meta_forward($_SERVER['PHP_SELF'], "AddedID=".$_SESSION['issue_items']->order_id);
        }
    }


} /*end of process credit note */

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (input_num('qty') == 0 || !check_num('qty', 0))
	{
		display_error(_("The quantity entered is negative or invalid."));
		set_focus('qty');
		return false;
	}

	if (!check_num('std_cost', 0))
	{
		display_error(_("The entered standard cost is negative or invalid."));
		set_focus('std_cost');
		return false;
	}

   	return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item()
{
    if($_POST['UpdateItem'] != "" && check_item_data()) {
		$id = $_POST['LineNo'];
        $qoh = get_qoh_on_date($_POST['stock_id'], null, null);
        $quantity = input_num('qty') - $qoh;
        $short_qty = 0;
        if($quantity > 0)
            $short_qty = $quantity;
        $Quantity = input_num('qty') - $short_qty;
        $_SESSION['issue_items']->update_cart_item($id, $Quantity,
        input_num('std_cost'), 0, 0, 0,0,0,
        '', '', '', '', '', '', 0, 0, 0, 0,0, 0,'', '', '',0, 0, 0,0,
        0, 0,0,'','', '', $short_qty, $qoh);
    }
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------


if (isset($_POST['CartLoad1'])) {

    $_SESSION['issue_items']->clear_items();

    line_start_focus();
}


function handle_delete_item($id)
{
	$_SESSION['issue_items']->remove_from_cart($id);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item()
{
	if (!check_item_data())
		return;
    $qoh = get_qoh_on_date($_POST['stock_id'], null, null);
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=
    $quantity = input_num('qty') - $qoh;
    $short_qty = 0;
    if($quantity > 0)
        $short_qty = $quantity;
    $Quantity = input_num('qty') - $short_qty;
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=
    $issue_no = get_work_order_issue_no($_SESSION['issue_items']->order_id);
    $get_issued_items = get_wo_issue_items($issue_no['issue_no'], $_POST['stock_id']);

    if($get_issued_items['id'] != 0) {

        //$Quantity = $Quantity - $get_issued_items['qty_issued'];
//        $quantity = $Quantity - $qoh;
//        $short_qty = 0;
//        if($quantity > 0)
//            $short_qty = $quantity;
    }

    add_to_issue($_SESSION['issue_items'], $_POST['stock_id'], $Quantity,
		 input_num('std_cost'), $short_qty, $qoh);
	line_start_focus();
}

function load_child_items($qty_req)
{
    
     $_SESSION['issue_items']->clear_items();
    
    unset($SelectedItem);
    $result = get_bom($_POST['Stock']);
    while ($myrow = db_fetch($result))
    {

        $wo_no = get_work_order_no($_SESSION['issue_items']->order_id);
        $qoh = get_qoh_on_date($myrow["component"], null, null);
        $quantity1 = $myrow["quantity"]*$qty_req;
        $quantity = ($quantity1 - $qoh);
        $short_qty = 0;

        if($quantity > 0)
            $short_qty = $quantity;
        $Quantity = $quantity1 - $short_qty*$wo_no['units_reqd'];
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=--=-=-=
        $issue_no = get_work_order_issue_no($_SESSION['issue_items']->order_id);
        $get_issued_items = get_wo_issue_items($issue_no['issue_no'], $myrow["component"]);
        if($get_issued_items['id'] != 0) {
            $Quantity = $Quantity - $get_issued_items['qty_issued'];
        }
        if($Quantity < 0)
            $Quantity = 0;
        add_to_issue($_SESSION['issue_items'], $myrow["component"], $Quantity,
            input_num('std_cost'), $short_qty, $qoh);
    }
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------
$id = find_submit('Delete');
if ($id != -1)
	handle_delete_item($id);

if (isset($_POST['AddItem']))
	handle_new_item();
	


if (isset($_POST['UpdateItem']))
	handle_update_item();

if (isset($_POST['CancelItemChanges'])) {
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

if (isset($_GET['trans_no']))
{
	handle_new_order();
}

//-----------------------------------------------------------------------------------------------

$stock_id = display_wo_details($_SESSION['issue_items']->order_id);
hidden('Stock', $stock_id);
hidden('manu_item');
echo "<br>";

start_form();


if (isset($_POST['CartLoad'])) {
    load_child_items($_POST['manu_item']);
}

start_table(TABLESTYLE, "width='90%'", 10);
submit_cells('CartLoad', _("Load BOM"), "colspan=2",
    _('Load Bill of material'), true);
    
    
// submit_cells('CartLoad1', _("Clear BOM"), "colspan=2",
//     _('Load Bill of material'), true);


    
    
    
echo "<tr><td>";
display_issue_items(_("Items to Issue"), $_SESSION['issue_items']);

issue_options_controls();
echo "</td></tr>";

end_table();

submit_center('Process', _("Process Issue"), true, '', 'default');

end_form();

//------------------------------------------------------------------------------------------------

end_page();

?>

    <script>


        document.getElementById("CartLoad").addEventListener("click", myFunction11);


        function myFunction11() {
            var qty_req =   document.getElementById('qty_req').value;
            document.getElementById('manu_item').value = qty_req;
        }
    </script>

<?php

