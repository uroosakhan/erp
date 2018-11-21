<?php

$page_security = 'SA_INVENTORYADJUSTMENT';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/items_cart.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/fixed_assets/includes/fixed_assets_db.inc");
include_once($path_to_root . "/inventory/includes/item_adjustments_ui.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
if (isset($_GET['NewAdjustment'])) {
	if (isset($_GET['FixedAsset'])) {
		$page_security = 'SA_ASSETDISPOSAL';
		$_SESSION['page_title'] = _($help_context = "Fixed Assets Adjustments");

	} else {
		$_SESSION['page_title'] = _($help_context = "Item Adjustments Note");

	}
}


page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_INVADJUST;

	if($_GET['FixedAsset'] == 1)
        $trans_type = ST_FAADJUST;
	else
        $trans_type = ST_INVADJUST;


  $result = get_stock_adjustment_items($trans_no,$trans_type);
  $row = db_fetch($result);

  if (is_fixed_asset($row['mb_flag'])) {
    display_notification_centered(_("Fixed Assets disposal has been processed"));
    display_note(get_trans_view_str($trans_type, $trans_no, _("&View this Adjustment")));

    display_note(get_gl_view_str($trans_type, $trans_no, _("View the GL &Postings for this Disposal")), 1, 0);
	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Disposal"), "NewAdjustment=1&FixedAsset=1");
  }
  else {
    display_notification_centered(_("Items adjustment has been processed"));
    display_note(get_trans_view_str($trans_type, $trans_no, _("&View this adjustment")));


    display_note(get_gl_view_str($trans_type, $trans_no, _("View the GL &Postings for this Adjustment")), 1, 0);

	  hyperlink_params($_SERVER['PHP_SELF'], _("Enter &Another Adjustment"), "NewAdjustment=1");
  }

	hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$trans_no");

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
	if (isset($_SESSION['adj_items']))
	{
		$_SESSION['adj_items']->clear_items();
		unset ($_SESSION['adj_items']);
	}

    $_SESSION['adj_items'] = new items_cart(ST_INVADJUST);
    $_SESSION['adj_items']->fixed_asset = isset($_GET['FixedAsset']);
	$_POST['AdjDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['AdjDate']))
		$_POST['AdjDate'] = end_fiscalyear();
	$_SESSION['adj_items']->tran_date = $_POST['AdjDate'];	
}

//-----------------------------------------------------------------------------------------------

function can_process()
{

	global $SysPrefs;



	$adj = &$_SESSION['adj_items'];

    if($_SESSION['adj_items']->fixed_asset ) {
        $trans_type = ST_FAADJUST;
    }
    else {
        $trans_type = ST_INVADJUST;
    }



	if (count($adj->line_items) == 0)	{
		display_error(_("You must enter at least one non empty item line."));
		set_focus('stock_id');
		return false;
	}


	if (!check_reference($_POST['ref'], $trans_type))
	{
		set_focus('ref');
		return false;
	}

	if (!is_date($_POST['AdjDate'])) 
	{
		display_error(_("The entered date for the adjustment is invalid."));
		set_focus('AdjDate');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_POST['AdjDate'])) 
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('AdjDate');
		return false;
	}
	elseif (!$SysPrefs->allow_negative_stock())
	{
		$low_stock = $adj->check_qoh($_POST['StockLocation'], $_POST['AdjDate'], $_POST['adj_type']);

		if ($low_stock)
		{
    		display_error(_("The adjustment cannot be processed because it would cause negative inventory balance for marked items as of document date or later."));
			unset($_POST['Process']);
			return false;
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

		if($diff > $row ){

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
	return true;
}

//-------------------------------------------------------------------------------

if (isset($_POST['Process']) && can_process()){

    $fixed_asset = $_SESSION['adj_items']->fixed_asset;
    $trans_no = add_stock_adjustment($_SESSION['adj_items']->line_items,
		$_POST['StockLocation'], $_POST['AdjDate'],	$_POST['ref'], $_POST['memo_'], $_POST['batch'],
		$_POST['adj_type'],$_POST['units_id'],$_POST['con_factor'],$fixed_asset);
	new_doc_date($_POST['AdjDate']);
	$_SESSION['adj_items']->clear_items();
	unset($_SESSION['adj_items']);

  if ($fixed_asset)
   	meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no&FixedAsset=1");
  else
   	meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");

} /*end of process credit note */

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (input_num('qty') == 0)
	{
		display_error(_("The quantity entered is invalid."));
		set_focus('qty');
		return false;
	}
	$prefs=get_company_prefs();
	$myrow = get_item($_POST['stock_id']);
	if($prefs['batch'] == 1 && $_POST['batch'] == "" && $myrow['batch_status'] == 0)
	{
		display_error(_("Null Batch not allowed."));
		set_focus('batch');
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
    
    
    
    
	$id = $_POST['LineNo'];
   	$_SESSION['adj_items']->update_cart_item($id, input_num('qty'), 
		input_num('std_cost'),'','','','','',
		$_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'], input_num('amount1'),
		input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'), input_num('amount6'),
		$_POST['date1'], $_POST['date2'], $_POST['date3'],
		$_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
		$_POST['batch'], $_POST['exp_date'],'', '', '', '', get_post('AccountCode'), $_POST['dimension_id']);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id)
{
	$_SESSION['adj_items']->remove_from_cart($id);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_new_item()
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
	add_to_order($_SESSION['adj_items'], $_POST['stock_id'],
	input_num('qty'), input_num('std_cost'),'','','','',$_POST['units_id'],$_POST['con_factor'],
	$_POST['text1'], $_POST['text2'], $_POST['text3'], $_POST['text4'], $_POST['text5'], $_POST['text6'], input_num('amount1'),
	input_num('amount2'), input_num('amount3'), input_num('amount4'), input_num('amount5'), input_num('amount6'),
	$_POST['date1'], $_POST['date2'], $_POST['date3'],
	$_POST['combo1'], $_POST['combo2'], $_POST['combo3'],$_POST['combo4'], $_POST['combo5'], $_POST['combo6'],
	$_POST['batch'],$_POST['exp_date'], get_post('AccountCode'), $_POST['dimension_id']);
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

if (isset($_GET['NewAdjustment']) || !isset($_SESSION['adj_items']))
{

//	if (isset($_GET['FixedAsset']))
//		check_db_has_disposable_fixed_assets(_("There are no fixed assets defined in the system."));
//	else
		check_db_has_costable_items(_("There are no inventory items defined in the system which can be adjusted (Purchased or Manufactured)."));

	handle_new_order();
}

//-----------------------------------------------------------------------------------------------
start_form();

if ($_SESSION['adj_items']->fixed_asset) {
	$items_title = _("Disposal Items");
	$button_title = _("Process Disposal");
} else {
	$items_title = _("Adjustment Items");
	$button_title = _("Process Adjustment");
}

display_order_header($_SESSION['adj_items']);

start_outer_table(TABLESTYLE, "width='70%'", 10);

display_adjustment_items($items_title, $_SESSION['adj_items']);
adjustment_options_controls();

end_outer_table(1, false);

submit_center_first('Update', _("Update"), '', null);
submit_center_last('Process', $button_title, '', 'default');

end_form();
end_page();

