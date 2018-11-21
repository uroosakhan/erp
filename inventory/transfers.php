<?php

$page_security = 'SA_LOCATIONTRANSFER';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/stock_transfers_ui.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

if (isset($_GET['NewTransfer'])) {
	if (isset($_GET['FixedAsset'])) {
		$page_security = 'SA_ASSETTRANSFER';
		$_SESSION['page_title'] = _($help_context = "Fixed Assets Location Transfers");
	}
	else {
		$_SESSION['page_title'] = _($help_context = "Inventory Location Transfers");
	}
}
page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------------------------

check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_LOCTRANSFER;

	display_notification_centered(_("Inventory transfer has been processed"));
	$pref=get_company_prefs();
	if($pref['item_location'] == 1){
		display_note(get_trans_view_str(117, $trans_no, _("&View this transfer")));
	}
	else{
		display_note(get_trans_view_str($trans_type, $trans_no, _("&View this transfer")));
	}


  $itm = db_fetch(get_stock_transfer_items($_GET['AddedID']));

  if (is_fixed_asset($itm['mb_flag']))
	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Fixed Assets Transfer"), "NewTransfer=1&FixedAsset=1");
  else
	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Inventory Transfer"), "NewTransfer=1");

	display_footer_exit();
}
//--------------------------------------------------------------------------------------------------

function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_stock_id_edit');
}
//-----------------------------------------------------------------------------------------------

function handle_new_order()
{
	if (isset($_SESSION['transfer_items']))
	{
		$_SESSION['transfer_items']->clear_items();
		unset ($_SESSION['transfer_items']);
	}

	$_SESSION['transfer_items'] = new items_cart(ST_LOCTRANSFER);
  $_SESSION['transfer_items']->fixed_asset = isset($_GET['FixedAsset']);
	$_POST['AdjDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['AdjDate']))
		$_POST['AdjDate'] = end_fiscalyear();
	$_SESSION['transfer_items']->tran_date = $_POST['AdjDate'];	
}

//-----------------------------------------------------------------------------------------------

if (isset($_POST['Process']))
{

	$tr = &$_SESSION['transfer_items'];
	$input_error = 0;

	if (count($tr->line_items) == 0)	{
		display_error(_("You must enter at least one non empty item line."));
		set_focus('stock_id');
		$input_error = 1;
	}
	if (!check_reference($_POST['ref'], ST_LOCTRANSFER))
	{
		set_focus('ref');
		$input_error = 1;
	} 
	elseif (!is_date($_POST['AdjDate'])) 
	{
		display_error(_("The entered transfer date is invalid."));
		set_focus('AdjDate');
		$input_error = 1;
	} 
	elseif (!is_date_in_fiscalyear($_POST['AdjDate'])) 
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('AdjDate');
		$input_error = 1;
	} 
	elseif ($_POST['FromStockLocation'] == $_POST['ToStockLocation'])
	{
		display_error(_("The locations to transfer from and to must be different."));
		set_focus('FromStockLocation');
		$input_error = 1;
	}
	elseif (!$SysPrefs->allow_negative_stock())
	{
		$low_stock = $tr->check_qoh($_POST['FromStockLocation'], $_POST['AdjDate'], true);

		if ($low_stock)
		{
    		display_error(_("The transfer cannot be processed because it would cause negative inventory balance in source location for marked items as of document date or later."));
			$input_error = 1;
		}
	}



	$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['AdjDate'], 'd');

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

	$diff_futuredays   =  date_diff2($_POST['AdjDate'],date('d-m-Y'), 'd');
			
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
	if ($input_error == 1)
		unset($_POST['Process']);
}

//-------------------------------------------------------------------------------

if (isset($_POST['Process']))
{

	$trans_no = add_stock_transfer($_SESSION['transfer_items']->line_items,
		$_POST['FromStockLocation'], $_POST['ToStockLocation'],
		$_POST['AdjDate'], $_POST['ref'], $_POST['memo_']);
	new_doc_date($_POST['AdjDate']);
	$_SESSION['transfer_items']->clear_items();
	unset($_SESSION['transfer_items']);

   	meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
} /*end of process credit note */

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (!check_num('qty', 0) || input_num('qty') == 0)
	{
		display_error(_("The quantity entered must be a positive number."));
		set_focus('qty');
		return false;
	}
   	return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item()
{
	$id = $_POST['LineNo'];
   	if (!isset($_POST['std_cost']))
   		$_POST['std_cost'] = $_SESSION['transfer_items']->line_items[$id]->standard_cost;
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
   		
   		
   	$_SESSION['transfer_items']->update_cart_item($id, input_num('qty'), $_POST['std_cost'],'','','','','',
		$_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'], input_num('amount1'),
		input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'), input_num('amount6'),
		$_POST['date1'], $_POST['date2'], $_POST['date3'],
		$_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],0,0,$_POST['FromStockLocation'], $_POST['ToStockLocation']);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id)
{
	$_SESSION['transfer_items']->remove_from_cart($id);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item()
{
	if (!isset($_POST['std_cost']))
   		$_POST['std_cost'] = 0;
   		
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
	
	add_to_order($_SESSION['transfer_items'], $_POST['stock_id'], input_num('qty'), $_POST['std_cost'],'','','',$_POST['units_id'],$_POST['con_factor'],
		$_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'], input_num('amount1'),
		input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'), input_num('amount6'),
		$_POST['date1'], $_POST['date2'], $_POST['date3'],
		$_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
		$_POST['FromStockLocation'], $_POST['ToStockLocation'],$_POST['batch']);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------
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

if (isset($_GET['NewTransfer']) || !isset($_SESSION['transfer_items']))
{
	if (isset($_GET['fixed_asset']))
		check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
	else
		check_db_has_costable_items(_("There are no inventory items defined in the system (Purchased or manufactured items)."));

	handle_new_order();
}

//-----------------------------------------------------------------------------------------------
start_form();

display_order_header($_SESSION['transfer_items']);

start_table(TABLESTYLE, "width='70%'", 10);
start_row();
echo "<td>";
display_transfer_items(_("Items"), $_SESSION['transfer_items']);
transfer_options_controls();
echo "</td>";
end_row();
end_table(1);

submit_center_first('Update', _("Update"), '', null);
submit_center_last('Process', _("Process Transfer"), '',  'default');

end_form();
end_page();

