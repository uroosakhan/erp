<?php
$page_security = 'SA_WORKORDERENTRY';
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/manufacturing.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Work Order Entry"), false, false, "", $js);


check_db_has_manufacturable_items(_("There are no manufacturable items defined in the system."));

check_db_has_locations(("There are no inventory locations defined in the system."));
?>

<?php
//---------------------------------------------------------------------------------------

if (isset($_GET['trans_no']))
{
	$selected_id = $_GET['trans_no'];
}
elseif(isset($_POST['selected_id']))
{
	$selected_id = $_POST['selected_id'];
}


if(isset($_GET['WOType'])) {
    $_POST['type'] = $_GET['WOType'];

}
function get_sale_order_ref_wo($order_no)
{
	$sql = "SELECT reference FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($order_no) ."";
	$query =  db_query($sql, "The work order issues could not be retrieved");
	$fetch = db_fetch_row ($query);
	return $fetch [0];
}
//---------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
	$id = $_GET['AddedID'];
	$stype = ST_WORKORDER;
    include_once($path_to_root . "/reporting/includes/reporting.inc");
	display_notification_centered(_("The work order been added."));

    display_note(get_trans_view_str($stype, $id, _("View this Work Order")), 0, 1);
	
	if ($_GET['type'] == WO_ADVANCED)
		submenu_print(_("&Print This Work Order"), WO_ADVANCED, $id, 'prtopt');

	if ($_GET['type'] != WO_ADVANCED)
	{
// 		include_once($path_to_root . "/reporting/includes/reporting.inc");

		submenu_print(_("&Print This Work Order"), ST_WORKORDER, $id, 'prtopt');
		submenu_print(_("&Email This Work Order"), ST_WORKORDER, $id, null, 1);
    	display_note(get_gl_view_str($stype, $id, _("View the GL Journal Entries for this Work Order")), 1);
    	$ar = array('PARAM_0' => $_GET['date'], 'PARAM_1' => $_GET['date'], 'PARAM_2' => $stype, 'PARAM_3' => '',
    		'PARAM_4' => (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0)); 
    	display_note(print_link(_("Print the GL Journal Entries for this Work Order"), 702, $ar), 1);
		hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$stype&trans_no=$id");
	}
	
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['UpdatedID']))
{
	$id = $_GET['UpdatedID'];

	display_notification_centered(_("The work order been updated."));
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['DeletedID']))
{
	$id = $_GET['DeletedID'];

	display_notification_centered(_("Work order has been deleted."));
	safe_exit();
}

//---------------------------------------------------------------------------------------

if (isset($_GET['ClosedID']))
{
	$id = $_GET['ClosedID'];

	display_notification_centered(_("This work order has been closed. There can be no more issues against it.") . " #$id");
	safe_exit();
}

//---------------------------------------------------------------------------------------

function safe_exit()
{
	global $path_to_root;

	hyperlink_no_params("", _("Enter a new work order"));
	hyperlink_no_params("search_work_orders.php", _("Select an existing work order"));
	
	display_footer_exit();
}

//-------------------------------------------------------------------------------------
if (!isset($_POST['date_']))
{
	$_POST['date_'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['date_']))
		$_POST['date_'] = end_fiscalyear();
}

function can_process()
{
	global $selected_id, $SysPrefs, $Refs;


$pref=get_company_prefs();


	if (!isset($selected_id))
	{
    	if (!$Refs->is_valid($_POST['wo_ref'], ST_WORKORDER))
    	{
    		display_error(_("You must enter a reference."));
			set_focus('wo_ref');
    		return false;
    	}

    	if (!is_new_reference($_POST['wo_ref'], ST_WORKORDER))
    	{
    		display_error(_("The entered reference is already in use."));
			set_focus('wo_ref');
    		return false;
    	}
	}

	if (!check_num('quantity', 0))
	{
		display_error( _("The quantity entered is invalid or less than zero."));
		set_focus('quantity');
		return false;
	}
	
	$prefs = get_company_prefs();
	
	 $batch_row = get_batch_by_name($_POST['batch']);
    $row  = get_batch_from_stock_moves($batch_row['id']);
global $db_connections;
    if ($row['trans_no'] != '' && $batch_row['id'] != '' && $prefs['batch'] == 1
     && $db_connections[$_SESSION["wa_current_user"]->company]["name"] !='VETZ'
    )
    {
        display_error( _("Batch No. Already exists."));
        set_focus('batch');
        return false;
    }


    if($prefs['batch'] == 1 && ($_POST['batch'] == "" || $_POST['batch'] == -1 ) )
    {
        display_error(_("Select Batch No."));
        set_focus('batch');
        return false;

    }

	
    if ($_POST['batch'] == '' && $_POST['type'] != 1 && $pref['batch'] == 1)
    {
        display_error( _("Enter Batch number."));
        set_focus('batch');
        return false;
    }
    elseif (!is_date_in_fiscalyear($_POST['date_']))
    {
        display_error(_("The entered date is not in fiscal year."));
        set_focus('date_');
        return false;
    }
//    display_error($_POST['Cost'] ."/".$_POST['cost_manufactured']);
//	die;
global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["BNT2"] ){
	if (input_num('Cost') > input_num('cost_manufactured') )
	{
		display_error( _("The components Cost.:".input_num('Cost')." should be less than Manufactured Item Cost .".input_num('cost_manufactured')));
		set_focus('Cost');
		return false;
	}
}

	// only check bom and quantites if quick assembly
	if (!($_POST['type'] == WO_ADVANCED))
	{
        if (!has_bom($_POST['stock_id']))
        {
        	display_error(_("The selected item to manufacture does not have a bom."));
			set_focus('stock_id');
        	return false;
        }

		if ($_POST['Labour'] == "")
			$_POST['Labour'] = price_format(0);
    	if (!check_num('Labour', 0))
    	{
    		display_error( _("The labour cost entered is invalid or less than zero."));
			set_focus('Labour');
    		return false;
    	}
		if ($_POST['Costs'] == "")
			$_POST['Costs'] = price_format(0);
    	if (!check_num('Costs', 0))
    	{
    		display_error( _("The cost entered is invalid or less than zero."));
			set_focus('Costs');
    		return false;
    	}

        if (!$SysPrefs->allow_negative_stock())
        {
        	if ($_POST['type'] == WO_ASSEMBLY)
        	{
        		// check bom if assembling
                $result = get_bom($_POST['stock_id']);

            	while ($bom_item = db_fetch($result))
            	{

            		if (has_stock_holding($bom_item["ResourceType"]))
            		{
				// 		$ids = find_submit('qty');

//                		$quantity = $bom_item["quantity"] * input_num('quantity');
                // 		$quantity = input_num('qty'.$ids) * input_num('quantity');
                        $quantity = input_num('qty'.$bom_item['id']) ;

                        $qoh = get_qoh_on_date($bom_item["component"], $bom_item["loc_code"], $_POST['date_']);
                		if (-$quantity + $qoh < 0)
                		{
                			display_error(_("The work order cannot be processed because there is an insufficient quantity for component:") .
                				" " . $bom_item["component"] . " - " .  $bom_item["description"] . ".  " . _("Location:") . " " . $bom_item["location_name"]."//".input_num('qty'.$bom_item['id']));
							set_focus('quantity');
        					return false;
                		}
            		}
            	}
        	}
        	elseif ($_POST['type'] == WO_UNASSEMBLY)
        	{
        		// if unassembling, check item to unassemble
				$qoh = get_qoh_on_date($_POST['stock_id'], $_POST['StockLocation'], $_POST['date_']);
        		if (-input_num('quantity') + $qoh < 0)
        		{
        			display_error(_("The selected item cannot be unassembled because there is insufficient stock."));
					return false;
        		}
        	}
    	}
     }
     else
     {
    	if (!is_date($_POST['RequDate']))
    	{
			set_focus('RequDate');
    		display_error( _("The date entered is in an invalid format."));
    		return false;
		}
		//elseif (!is_date_in_fiscalyear($_POST['RequDate']))
		//{
		//	display_error(_("The entered date is not in fiscal year."));
		//	return false;
		//}
    	if (isset($selected_id))
    	{
    		$myrow = get_work_order($selected_id, true);

    		if ($_POST['units_issued'] > input_num('quantity'))
    		{
				set_focus('quantity');
    			display_error(_("The quantity cannot be changed to be less than the quantity already manufactured for this order."));
        		return false;
    		}
    	}
	}


		$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['date_'], 'd');

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

	$diff_futuredays   =  date_diff2($_POST['date_'],date('d-m-Y'), 'd');
			
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

//-------------------------------------------------------------------------------------

if(isset($_POST['ADD_ITEM']) && can_process())
{
	if (!isset($_POST['cr_acc']))
		$_POST['cr_acc'] = "";
	if (!isset($_POST['cr_lab_acc']))
		$_POST['cr_lab_acc'] = "";
	
	$ids = find_submit('qty');


	
	$id = add_work_order($_POST['wo_ref'], $_POST['StockLocation'], input_num('quantity'),
		$_POST['stock_id'],  $_POST['type'], $_POST['date_'],
		$_POST['RequDate'], $_POST['memo_'], input_num('Costs'), $_POST['cr_acc'], input_num('Labour'), $_POST['cr_lab_acc'], 
		input_num('qty'.$ids),$_POST['amount3'],$_POST['sale_order'],0,0,input_num('amount3'.$ids),$_POST['batch'],$_POST['exp_date']);

	new_doc_date($_POST['date_']);
	meta_forward($_SERVER['PHP_SELF'], "AddedID=$id&type=".$_POST['type']."&date=".$_POST['date_']);
}

//-------------------------------------------------------------------------------------

if (isset($_POST['UPDATE_ITEM']) && can_process())
{

	update_work_order($selected_id, $_POST['StockLocation'], input_num('quantity'),
		$_POST['stock_id'],  $_POST['date_'], $_POST['RequDate'], $_POST['memo_'], $_POST['sale_order']);
	new_doc_date($_POST['date_']);
	meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$selected_id");
}

//--------------------------------------------------------------------------------------

if (isset($_POST['delete']))
{
	//the link to delete a selected record was clicked instead of the submit button

	$cancel_delete = false;

	// can't delete it there are productions or issues
	if (work_order_has_productions($selected_id) ||
		work_order_has_issues($selected_id)	||
		work_order_has_payments($selected_id))
	{
		display_error(_("This work order cannot be deleted because it has already been processed."));
		$cancel_delete = true;
	}

	if ($cancel_delete == false)
	{ //ie not cancelled the delete as a result of above tests

		// delete the actual work order
		delete_work_order($selected_id);
		meta_forward($_SERVER['PHP_SELF'], "DeletedID=$selected_id");
	}
}

//-------------------------------------------------------------------------------------

if (isset($_POST['close']))
{

	// update the closed flag in the work order
	close_work_order($selected_id);
	meta_forward($_SERVER['PHP_SELF'], "ClosedID=$selected_id");
}

//-------------------------------------------------------------------------------------
if (get_post('_type_update') || $selected_id)
{
  $Ajax->activate('_page_body');
}
//-------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE2);

$existing_comments = "";

$dec = 0;
if (isset($selected_id))
{
	$myrow = get_work_order($selected_id);

	if (strlen($myrow[0]) == 0)
	{
		echo _("The order number sent is not valid.");
		safe_exit();
	}

	// if it's a closed work order can't edit it
	if ($myrow["closed"] == 1)
	{
		echo "<center>";
		display_error(_("This work order is closed and cannot be edited."));
		safe_exit();
	}

	$_POST['wo_ref'] = $myrow["wo_ref"];
	$_POST['stock_id'] = $myrow["stock_id"];
	//$_POST['quantity'] = qty_format($myrow["units_reqd"], $_POST['stock_id'], $dec);
	$_POST['quantity'] = $myrow["units_reqd"];
	$_POST['StockLocation'] = $myrow["loc_code"];
	$_POST['released'] = $myrow["released"];
	$_POST['closed'] = $myrow["closed"];
	$_POST['type'] = $myrow["type"];
	$_POST['date_'] = sql2date($myrow["date_"]);
	$_POST['RequDate'] = sql2date($myrow["required_by"]);
	$_POST['released_date'] = sql2date($myrow["released_date"]);
	$_POST['memo_'] = "";
	$_POST['units_issued'] = $myrow["units_issued"];
	$_POST['Costs'] = price_format($myrow["additional_costs"]);

	$_POST['memo_'] = get_comments_string(ST_WORKORDER, $selected_id);
	$_POST['sale_order'] = $myrow["sale_order"];

	hidden('wo_ref', $_POST['wo_ref']);
	hidden('units_issued', $_POST['units_issued']);
	hidden('released', $_POST['released']);
	hidden('released_date', $_POST['released_date']);
	hidden('selected_id',  $selected_id);
	hidden('old_qty', $myrow["units_reqd"]);
	hidden('old_stk_id', $myrow["stock_id"]);
//	hidden('sale_order', $myrow["sale_order"]);

	label_row(_("Reference:"), $_POST['wo_ref']);
	label_row(_("Type:"), $wo_types_array[$_POST['type']]);
	hidden('type', $myrow["type"]);
	hidden('sale_order', $myrow["sale_order"]);
}
else
{
	$_POST['units_issued'] = $_POST['released'] = 0;
	ref_row(_("Reference:"), 'wo_ref', '', $Refs->get_next(ST_WORKORDER));





	if($_POST['type'] == 0) {
        label_cell("Type");
        label_cell("Assemble");
        hidden('type',0);
    }
    elseif ($_POST['type']  == 1)
    {
        label_cell("Type");
        label_cell("Un-Assemble");
        hidden('type',1);

    }
    else{
        label_cell("Type");
        label_cell("Manufacture");
        hidden('type',2);

    }
// 	wo_types_list_row(_("Type:"), 'type', null);
}

if (get_post('released'))
{
	hidden('stock_id', $_POST['stock_id']);
	hidden('StockLocation', $_POST['StockLocation']);
	hidden('type', $_POST['type']);
	hidden('sale_order', $_POST["sale_order"]);

	label_row(_("Item:"), $myrow["StockItemName"]);
	label_row(_("Destination Location:"), $myrow["location_name"]);
}
else
{
    global $Ajax ;
	stock_manufactured_items_list_row(_("Item:"), 'stock_id', null, true, true);

	if(isset($_POST['stock_id']))
        $Ajax->activate('batch');

	$prefs = get_company_prefs();
	if($prefs['batch'] == 1) {
        if($_POST['type'] == 1){
            batch_list_row("Batch",$_POST['stock_id'],'batch');
        }
        else{
            ref_row(_("Batch:"), 'batch', '', $Refs->get_next(46));
            date_row("Batch Expiry:", 'exp_date');
        }

    }
	if (list_updated('stock_id') || isset($_POST['Update12']))
	$Ajax->activate('formula');
		$Ajax->activate('quantity');
		$Ajax->activate('bom');
		$Ajax->activate('qoh');


//		$Ajax->activate('hi');

	locations_list_row(_("Destination Location:"), 'StockLocation', null,false,true);
	
	$qoh =	get_qoh_on_date($_POST['stock_id'],$_POST['StockLocation'],null);
	$_POST['qoh'] = db_escape($qoh);
}
if (isset($selected_id)) {

	label_row(_("Sales Order Reference:"), get_sale_order_ref_wo($_POST['sale_order']));
}
else
{
	sales_order_cells(_("Sales Order Reference:"), 'sale_order', NULL, true, false);

}
//	div_end();



if (!isset($_POST['quantity']))
	$_POST['quantity'] = qty_format(1, $_POST['stock_id'], $dec);
else
	$_POST['quantity'] = qty_format($_POST['quantity'], $_POST['stock_id'], $dec);

	
	if (isset($_POST['amount3']) ) {
	$Ajax->activate('quantity');
	$Ajax->activate('amount1');
	$Ajax->activate('amount3');
}
global  $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX') {
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
	

if (get_post('type') == WO_ADVANCED)
{

//    qty_row(_("Quantity Required:"), 'quantity', null, null, null, $dec);
	ref_cells("Quantity Required:", 'quantity', null, null, null, false);

	display_wo_bom($_POST['stock_id'], 1);
//	display_wo_bom($_POST['stock_id']);
    if ($_POST['released'])
    	label_row(_("Quantity Manufactured:"), number_format($_POST['units_issued'], get_qty_dec($_POST['stock_id'])));
    date_row(_("Date") . ":", 'date_', '', true);
	date_row(_("Date Required By") . ":", 'RequDate', '', null, $SysPrefs->default_wo_required_by());
}
else
{
    $item = get_item($_POST['stock_id']);
	ref_cells("Quantity:", 'quantity', null, null, null, true);
   // hidden('quantity',$_POST['quantity']);
	$cost = number_format2($item['material_cost'] * $_POST['quantity'],2);

   $_POST['cost'] = $cost ;

$_POST['units'] = $item['units'] ;
   if(isset($_POST['quantity']))
   {
       $Ajax->activate('cost');
   }
   
if(isset($_POST['stock_id']))
{
    $Ajax->activate('Cost');
    $Ajax->activate('units');
    $Ajax->activate('cost_manufactured');
}

   
ref_cells_disabled("QOH", 'qoh', null, null, null, true);
label_row("UOM :",$item['units'] );
ref_cells_disabled("Cost :", 'cost', null, null, null, true);
hidden('cost_manufactured',$cost);


display_wo_bom($_POST['stock_id']);

if(isset($_POST['stock_id']))
{
    $Ajax->activate('Cost');

}

//    qty_row(_("Quantity:"), 'quantity', null, null, null, $dec);
    date_row(_("Date") . ":", 'date_', '', true);
	hidden('RequDate', '');

	$sql = "SELECT DISTINCT account_code FROM ".TB_PREF."bank_accounts";
	$rs = db_query($sql,"could not get bank accounts");
	$r = db_fetch_row($rs);
	if (!isset($_POST['Labour']))
	{
		$_POST['Labour'] = price_format(0);
		$_POST['cr_lab_acc'] = $r[0];
	}
	amount_row($wo_cost_types[WO_LABOUR], 'Labour');
	gl_all_accounts_list_row(_("Credit Labour Account"), 'cr_lab_acc', null);
	if (!isset($_POST['Costs']))
	{
		$_POST['Costs'] = price_format(0);
		$_POST['cr_acc'] = $r[0];
	}
	amount_row($wo_cost_types[WO_OVERHEAD], 'Costs');
	gl_all_accounts_list_row(_("Credit Overhead Account"), 'cr_acc', null);

//    $_POST['Cost'] = $_POST['cost'];
	
}

if (get_post('released'))
	label_row(_("Released On:"),$_POST['released_date']);

//$cost_sub_total = ($_POST['cost'] + $_POST['Labour'] + $_POST['Costs'])  ;


amount_row("Cost Sub-total ", 'Cost');

textarea_row(_("Memo:"), 'memo_', null, 40, 5);

end_table(1);

if (isset($selected_id))
{
	echo "<table align=center><tr>";
	submit_cells('UPDATE_ITEM', _("Update"), '', _('Save changes to work order'), 'default');
	if (get_post('released'))
	{
		submit_cells('close', _("Close This Work Order"),'','',true);
	}
	submit_cells('delete', _("Delete This Work Order"),'','',true);

	echo "</tr></table>";
}
else
{
	submit_center('ADD_ITEM', _("Add Workorder"), true, '', 'default');
}

end_form();

// Add more details

// function display_wo_bom($stock_id)
// {
// 	$path_to_root = "..";
// 	end_table(1);
// //	----------------------------------------------------------------------
// 	global  $db_connections;
// 	$myrow_3 = get_company_item_pref_from_position(3);
// 	display_heading("Bill of Materials");
// 	$result = get_bom($stock_id);
// 	$item_info = get_item_edit_info($stock_id);





// 	div_start('bom');
// 	start_table(TABLESTYLE, "width=60%");
// 	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'
// 	|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
// 	{
// 		$th = array(_("Code"), _("Description"), _("Location"),
// 			_("Work Centre"),"Enter (".$myrow_3['label_value'].")",_("Quantity"), _("Issued Qty."), _("Units")/*,'',''*/);
// 	}
// 	else{
// 		$th = array(_("Code"), _("Description"), _("Location"),
// 			_("Work Centre"),_("Quantity"), _("Issued Qty."), _("Units")/*,'',''*/);
// 	}


// 	table_header($th);

// 	$k = 0;
// 	while ($myrow = db_fetch($result))
// 	{
// 		$item_info = get_item_edit_info($myrow["component"]);


// 		$_POST['amount1'] = price_format($item_info["amount1"]);
// 		$_POST['amount2'] = price_format($item_info["amount2"]);
// 		$_POST['amount4'] = price_format($item_info["amount4"]);


// 		if(!isset($_POST['amount3']) ) {
// 		$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
// 		}

// 			$formula = $item_info["formula"];

// 	$value = explode(",", $formula);
// 	$maxs = max(array_keys($value));
// 	$amount3 = $value[0];
// 	$amount2 = $value[2];


// 	if($formula != '')
// 	{

// 		if(isset($_POST[$myrow_3['name'].$myrow['id']]) && $_POST[$myrow_3['name'].$myrow['id']] > 0 ) {

// 				$b = $_POST[$myrow_3['name'].$myrow['id']] . "" . $value[1] . "" . $_POST[$amount3];

// 			$_POST['qty'.$myrow['id']] = eval('return '.$b.';');

// 		}
// 		else{
		   

// 			$b = $item_info["amount3"] . "" . $value[1] . "" . $_POST[$amount3];
// 			$_POST['qty'.$myrow['id']] = eval('return '.$b.';');


// 		}

// 	}

// 		alt_table_row_color($k);
// 		label_cell($myrow["component"]);
// 		label_cell($myrow["description"]);
// 		label_cell($myrow["location_name"]);
// 		label_cell($myrow["WorkCentreDescription"]);

// 		if(!isset($_POST['amount3'.$myrow['id']]))
// 		{
// 			$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
// 		}

// 		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
// 		{
// 			ref_cells("", $myrow_3['name'].$myrow['id'], null, null, null, true);
// 		}
// 		qty_cells(null, 'qty'.$myrow['id'], number_format2($myrow["quantity"],  get_qty_dec($myrow["component"])));
// 		$IssuedQty = $_POST['qty'.$myrow['id']]*$_POST['quantity'];

// 		qty_cell($IssuedQty);
// 		label_cell($myrow["units"]);

// //		hyperlink_params("$path_to_root/manufacturing/manage/bom_edit.php?", _("Modify BOM"), "filterType=$stype&trans_no=$id");
// //		edit_button_cell("Edit".$myrow['id'], _("Edit"));
// //		delete_button_cell("Delete".$myrow['id'], _("Delete"));
// 		end_row();


// 	} //END WHILE LIST LOOP
// 	if(db_num_rows($result) != 0)
// 	{
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
// //		submit_cells('Update', _("Update"),
// //			_('Refresh document page'), true);

// 	}
// 		end_table(1);
// 	div_end();

// //	----------------------------------------------------------------------
// 	start_table(TABLESTYLE, "width=60%");
// }
function display_wo_bom($stock_id, $enable = 0)
{
	$path_to_root = "..";
	end_table(1);
//	----------------------------------------------------------------------
	global  $db_connections;
	$myrow_3 = get_company_item_pref_from_position(3);
	display_heading("Bill of Materials");
	$result = get_bom($stock_id);
	$item_info = get_item_edit_info($stock_id);

	div_start('bom');
	start_table(TABLESTYLE, "width=60%");
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX'
	)
	{
		$th = array(_("Code"), _("Description"), _("Location"),
			_("Work Centre"),"Enter (".$myrow_3['label_value'].")",_("Total.Quantity"),_("Quantity"), _("Cost"),_("Issued Qty."), _("Units")/*,'',''*/);
	}
	

	else{
		$th = array(_("Code"), _("Description"), _("Location"),
			_("Work Centre"),_("Quantity"), _("Cost"),_("Issued Qty."), _("Units"));
	}


	table_header($th);

	$k = 0;
    $cost_sum = 0;
    $total = 0;


    $row_id=array();
    $material_cost =array();
//    $store_array = array();
	while ($myrow = db_fetch($result))
	{


		$item_info = get_item_edit_info($myrow["component"]);


		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);


		if(!isset($_POST['amount3']) ) {
		$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
		}

			$formula = $item_info["formula"];

	$value = explode(",", $formula);
	$maxs = max(array_keys($value));
	$amount3 = $value[0];
	$amount2 = $value[2];

        global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX') {
            if ($formula != '') {
                if(!$_POST[$myrow_3['name'].$myrow['id']] ){
                    $_POST[$myrow_3['name'].$myrow['id']] = 1;
                }
                    $b = $_POST[$myrow_3['name'].$myrow['id']] . "" . $value[1] . "" . $_POST[$amount3];

                    $_POST['qty_box'.$myrow['id']] = eval('return '.$b.';');
                    $_POST['qty'.$myrow['id']] = $_POST['qty_box'.$myrow['id']] / $_POST['quantity'];
            }

        }
        else{
            if ($formula != '') {
                if (isset($_POST[$myrow_3['name'] . $myrow['id']]) && $_POST[$myrow_3['name'] . $myrow['id']] > 0) {
                    $b = $_POST[$myrow_3['name'] . $myrow['id']] . "" . $value[1] . "" . $_POST[$amount3];
                    $_POST['qty' . $myrow['id']] = eval('return ' . $b . ';');
                } else {
                    $b = $item_info["amount3"] . "" . $value[1] . "" . $_POST[$amount3];
                    $_POST['qty' . $myrow['id']] = eval('return ' . $b . ';');
                }

            }
        }

	   $qoh = get_qoh_on_date($myrow["component"],$_POST['StockLocation'],$_POST['date_']);

        if($_POST['qty'.$myrow['id']] != 0 || $_POST['qty'.$myrow['id']] != '') {

            if ($_POST['qty' . $myrow['id']] * $_POST['quantity'] > $qoh)
                start_row("class='stockmankobg'");
            else
                alt_table_row_color($k);
        }
        else{
            if ($qoh <= 0)
                start_row("class='stockmankobg'");
            else
                alt_table_row_color($k);
        }
		view_stock_status_cell($myrow["component"]);
		//label_cell($myrow["component"]);
		label_cell($myrow["description"]);
		label_cell($myrow["location_name"]);
		label_cell($myrow["WorkCentreDescription"]);

		if(!isset($_POST['amount3'.$myrow['id']]))
		{
			$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
		}

		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX')
		{
			ref_cells("", $myrow_3['name'].$myrow['id'], null, null, null, true);
		}



		if($enable == 0) {
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX' ) {
                
                qty_cells_readonly(null, 'qty_box' . $myrow['id'], number_format2($myrow["quantity"] * $_POST['quantity'], get_qty_dec($myrow["component"])));
                
//                 $_POST['qty'.$myrow['id']] =  number_format2($myrow["quantity"]*$_POST['quantity'],  get_qty_dec($myrow["component"]));
                
                  ref_cells("", 'qty'.$myrow['id'], null, null, null, true);
            }
            else {

                qty_cells(null, 'qty' . $myrow['id'], number_format2($myrow["quantity"] * $_POST['quantity'], get_qty_dec($myrow["component"])));
            }
            $row_id[] = $myrow['id'];
		}
				elseif($enable == 1) {
			qty_cell($_POST['quantity']);
                }

               $item =  get_item($myrow["component"]);
        $material_cost[] = $item['material_cost' ];
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX' ) {
            $_POST['cost' . $myrow['id']] = $item['material_cost'] * ($_POST['qty' . $myrow['id']] * $_POST['quantity']);
            $IssuedQty = $_POST['qty_box' . $myrow['id']];
        }
        else{
            $IssuedQty = $myrow["quantity"]* input_num('quantity');
            $_POST['cost' . $myrow['id']] = $item['material_cost'] * $_POST['qty' . $myrow['id']] ;
        }
        $cost_sum += $_POST['cost'.$myrow['id']];
        $total += $_POST['cost'.$myrow['id']];
        qty_cells_readonly(null, 'cost'.$myrow['id'] , number_format2($_POST['cost'], get_qty_dec($myrow["component"])));


		qty_cell($IssuedQty);
		label_cell($myrow["units"]);

//		hyperlink_params("$path_to_root/manufacturing/manage/bom_edit.php?", _("Modify BOM"), "filterType=$stype&trans_no=$id");
//		edit_button_cell("Edit".$myrow['id'], _("Edit"));
//		delete_button_cell("Delete".$myrow['id'], _("Delete"));
		end_row();


	}


    start_row();

//    qty_cells_readonly(null, 'cost_sum', number_format2($_POST['cost_sum'], get_qty_dec($myrow["component"])));
if($_POST['quantity']==1)
    $_POST['total'] = $cost_sum;
    label_row(_("Cost-total"), $cost_sum, "colspan=6 align=right","align=right", 2);
    echo "<input type='hidden' id='cost_sum' value='$cost_sum'>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    echo "<td>";
    qty_cells_readonly("Total", 'total', number_format2($_POST['total'], get_qty_dec($myrow["component"])));
//    label_row(_("Total"), $total, "colspan=6 align=right","align=right", 2);
//    echo "<input type='hidden' id='total' >";
//    ref_cells_disabled("Total:", 'total', null, null, null, true);
//    hidden('total', $total);
global $Ajax;

//    display_error($_POST['cost_sum']."+".$cost_sum);
    if (isset($_POST['stock_id']))
    {
        $Ajax->activate('Cost');


    $_POST['Cost'] =  $cost_sum;
//    $_POST['total'] =  $total;

    }
	//END WHILE LIST LOOP
	if(db_num_rows($result) != 0)
	{
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
// 		echo "<td>";
//		submit_cells('Update', _("Update"),
//			_('Refresh document page'), true);

	}
		end_table(1);
	div_end();

//	----------------------------------------------------------------------
	start_table(TABLESTYLE, "width=60%");
}
?>

<script>
    document.getElementById("Labour").onchange = function() {myFunction()};
    document.getElementById("Costs").onchange = function() {myFunction1()};
    document.getElementById("quantity").onchange = function() {myFunction2()};


    
    
      var x = document.getElementById("Labour").value;
        var y = document.getElementById("Costs").value;

        var c = document.getElementById("cost_sum").value;


    // var cost_total = document.getElementById("cost_sum").value;
    // document.getElementById('Cost').value=c;
       
    // function myFunction() {

    //     var x = document.getElementById("Labour").value;
    //     var y = document.getElementById("Costs").value;
    //     var c = document.getElementById("cost_sum").value;
    //     var d = parseInt(x) +  parseInt(y)  +  parseInt(c) ;
    //     document.getElementById('Cost').value=d;

    //     // console.log(12);
    // }
    // function myFunction1() {
    //     var x = document.getElementById("Labour").value;
    //     var y = document.getElementById("Costs").value;
    //     var c = document.getElementById("cost_sum").value;
    //     var d = parseInt(x) +  parseInt(y) +  parseInt(c) ;
    //     document.getElementById('Cost').value=d;

    //     // console.log(12);
    // }
  var sum=0;
  
  var x=0,c=0;
  
  var  y = document.getElementById("Costs").value.replace(/,/g, '');
    function myFunction() {
        
        //document.getElementById('txt2').value = document.getElementById('txt1').value.replace(/,/g, '');
         x = document.getElementById("Labour").value.replace(/,/g, '');


         c = document.getElementById("cost_sum").value.replace(/,/g, '');

        //alert(c);
        
        var d1=Number(x) +  Number(c)
        var d =Number(y) + d1 ;
        //console.log('d',d);
        //console.log(typeof(d));
        //alert(d);
        document.getElementById('Cost').value=Number(d);
        sum=d;
        
        //console.log('sum',sum);
        // console.log(12);
    }
    function myFunction1() {
        
         x = document.getElementById("Labour").value.replace(/,/g, '');
         y = document.getElementById("Costs").value.replace(/,/g, '');
         c = document.getElementById("cost_sum").value.replace(/,/g, '');
        var check =Number(y) + Number(x) + Number(c);
        document.getElementById('Cost').value=Number(check);

       // console.log(typeof(check));
        //alert(typeof(check));
        //alert(check);

        // console.log(12);
    }
    function myFunction2() {

         x = document.getElementById("cost_sum").value.replace(/,/g, '');
         y = document.getElementById("quantity").value.replace(/,/g, '');
         // c = document.getElementById("total").value.replace(/,/g, '');
        var check =Number(y) * Number(x);
        document.getElementById('total').value=Number(check);
       // console.log(typeof(check));
        //alert(typeof(check));
        //alert(check);

        // console.log(12);
    }
   
</script>;
<?php
end_page();


?>

