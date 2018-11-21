<?php
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Search Purchase Requisition"), false, false, "", $js);
}
if (isset($_GET['order_number']))
{
	$order_number = $_GET['order_number'];
}

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders')) 
{
	$Ajax->activate('orders_tbl');
} elseif (get_post('_order_number_changed')) 
{
	$disable = get_post('order_number') !== '';

	$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
	$Ajax->addDisable(true, 'OrdersToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	$Ajax->addDisable(true, 'SelectStockFromList', $disable);

	if ($disable) {
		$Ajax->addFocus(true, 'order_number');
	} else
		$Ajax->addFocus(true, 'OrdersAfterDate');

	$Ajax->activate('orders_tbl');
}
//---------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
	start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

supplier_list_cells(_("Select a supplier:"), 'supplier_id', null, true, false, false, !@$_GET['popup']);
ref_cells(_("#:"), 'order_number', '',null, '', true);
ref_cells(_("PARTY A/C:"), 'party', '',null, '', true);

date_cells(_("from:"), 'OrdersAfterDate', '', null, -30);
date_cells(_("to:"), 'OrdersToDate');

//locations_list_cells(_("into location:"), 'StockLocation', null, true);
//current_user_locations_list_cells(_("into location ").":", 'StockLocation',  null,  _("All Locations"), false,
//	$_SESSION["wa_current_user"]->user);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(_("for item:"), 'SelectStockFromList', null, true);

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_row();
end_table(1);
//---------------------------------------------------------------------------------------------
if (isset($_POST['order_number'])&& ($_POST['order_number'] != ""))
{
	$order_number = $_POST['order_number'];
}

if (isset($_POST['party'])&& ($_POST['party'] != ""))
{
	$party = $_POST['party'];
}
if (isset($_POST['supplier_id']))
{
	$supplier_id = $_POST['supplier_id'];
}

if (isset($_POST['SelectStockFromList']) &&	($_POST['SelectStockFromList'] != "") &&
	($_POST['SelectStockFromList'] != ALL_TEXT))
{
 	$selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
	unset($selected_stock_item);
}

//---------------------------------------------------------------------------------------------
function trans_view($trans)
{
	return get_trans_view_str(ST_PURCHREQ, $trans["requisition_id"]);
}

function edit_link($row) 
{
	if (@$_GET['popup'])
		return '';
  	return pager_link( _("Edit"),
		"/modules/requisitions/requisitions.php?" 
		. "ModifyOrderNumber=" . $row["requisition_id"], ICON_EDIT);
}

function prt_link($row)
{
	return print_document_link($row['requisition_id'], _("Print"), true, 93, ICON_PRINT);
}

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_pr_search_completed();

$cols = array(
		_("#") => array('fun'=>'trans_view'),
		_("Party A/C"), 
		_("Supplier Name") => array(/*'ord'=>''*/),
		_("Application Date") => array( 'type'=>'date'),
		_("Currency") => array('align'=>'center'), 

);
	if(!user_check_access('SA_SUPPPRICES')) {
 array_append($cols,array(
					_("Order Total") => array('type'=>'amount'),
));


}

array_append($cols,array(
					array('insert'=>true, 'fun'=>'prt_link'),
));


if ($_SESSION["wa_current_user"]->can_access('SA_PURCHASEORDEREDIT'))
{
	 array_append($cols,array(
					array('insert'=>true, 'fun'=>'edit_link'),
));
}

if (get_post('StockLocation') != $all_items) {
	$cols[_("Location")] = 'skip';
}
//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page();
}	
?>
