<?php

$page_security = 'SA_WORKORDERENTRYREQ';
$path_to_root = "..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (isset($_GET['outstanding_only']) && ($_GET['outstanding_only'] == true))
{
// curently outstanding simply means not closed
	$outstanding_only = 1;
	page(_($help_context = "Search Outstanding Work Orders Requisition"), false, false, "", $js);
}
else
{
	$outstanding_only = 0;
	page(_($help_context = "Search Work Orders Requisition"), false, false, "", $js);
}
//-----------------------------------------------------------------------------------
// Ajax updates
//
if(isset($_GET["UpdatedID"]))
    display_notification_centered(_("The work order requisition has been updated."));
elseif (isset($_GET["AddedID"]))
    display_notification_centered(_("The work order requisition has been added."));
if (get_post('SearchOrders')) 
{
	$Ajax->activate('orders_tbl');
} elseif (get_post('_OrderNumber_changed')) 
{
	$disable = get_post('OrderNumber') !== '';

	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, 'OverdueOnly', $disable);
	$Ajax->addDisable(true, 'OpenOnly', $disable);
	$Ajax->addDisable(true, 'SelectedStockItem', $disable);

	if ($disable) {
		set_focus('OrderNumber');
	} else
		set_focus('StockLocation');

	$Ajax->activate('orders_tbl');
}

//--------------------------------------------------------------------------------------

if (isset($_GET["stock_id"]))
	$_POST['SelectedStockItem'] = $_GET["stock_id"];

//--------------------------------------------------------------------------------------

start_form(false, false, $_SERVER['PHP_SELF'] ."?outstanding_only=$outstanding_only");

hyperlink_params("$path_to_root/manufacturing/work_order_entry_cart_req.php", _("Make new work order requisitions"), "NewWorkOrder=1");

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("#:"), 'OrderId', '',null, '', true);
ref_cells(_("Reference:"), 'OrderNumber', '',null, '', true);

locations_list_cells(_("at Location:"), 'StockLocation', null, true);

end_row();
end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();

//check_cells( _("Only Overdue:"), 'OverdueOnly', null);

if ($outstanding_only==0)
	check_cells( _("Only Open:"), 'OpenOnly', null);

stock_manufactured_items_list_cells(_("for item:"), 'SelectedStockItem', null, true);

submit_cells('SearchOrders', _("Search"),'',_('Select documents'),  'default');
end_row();
end_table();

//-----------------------------------------------------------------------------
function check_overdue($row)
{
	return (!$row["closed"] 
		&& date_diff2(Today(), sql2date($row["required_by"]), "d") > 0);
}

function view_link($dummy, $order_no)
{
	return $order_no;//get_trans_view_str(ST_MANUORDERREQ, $order_no);
}

function view_stock($row)
{
	return view_stock_status($row["stock_id"], $row["description"], false);
}

function wo_type_name($type)
{
//    display_error($type);
	global $wo_types_array;
	
	return $wo_types_array[$type];
}

function edit_link($row)
{
	return  $row['closed'] ? '<i>'._('Closed').'</i>' :
		trans_editor_link(ST_MANUORDERREQ, $row["id"]);
}

function make_wo_link($row)
{
	return pager_link(_('Make WO'), "/manufacturing/work_order_entry_cart.php?WorkOrder=" . $row["id"]);
}

function produce_link($row)
{
	return $row["closed"] || !$row["released"] ? '' :
		pager_link(_('Produce'),
			"/manufacturing/work_order_add_finished.php?trans_no=" .$row["id"]);
}

function costs_link($row)
{
	return $row["closed"] || !$row["released"] ? '' :
		pager_link(_('Costs'),
			"/manufacturing/work_order_costs.php?trans_no=" .$row["id"]);
}

function view_gl_link($row)
{
	return get_gl_view_str(ST_WORKORDER, $row['id']);
}

function dec_amount($row, $amount)
{
	return number_format2($amount, $row['decimals']);
}
function prt_link($row)
{
    return print_document_link($row['id']."-".$row['type'], _("Print Work Order Requisition"), true, ST_MANUORDERREQ, ICON_PRINT);
}

//
//function generate_link($row)
//{
//    if($row['closed'] == 0)
//    {
//        $Show = 0;
//        $short_qty = 0;
//        $result = get_bom($row["stock_id"]);
//        while($row1 = db_fetch($result)) {
////            $qoo = get_on_porder_qty($row1["component"], $row1["loc_code"]);
////            $qoh = get_qoh_on_date($row1["component"], $row1["loc_code"]);
////            $demand_qty = get_demand_asm_qty($row1["component"], $row1["loc_code"]);
////            $RequiredQty = $demand_qty - $qoo - $qoh;
////            display_error($row1["quantity"]."++".$RequiredQty);
////            if ($row1["quantity"] < $RequiredQty) old
//            $qoh = get_qoh_on_date($row1["component"], null, null);
//            $wo =  get_work_order($row['id']);
//           $quantity =  $qoh - ($row1["quantity"] * $wo['units_reqd']) ;
//
//            if($quantity > 0)
//                $short_qty = $quantity;
//            if ($short_qty > 0)
//                $Show = 1;
//        }
//        if($Show == 1)
//            return
//                custom_pager_link(_('Generate PO'),
//                    "/manufacturing/manage/generate.php?item_code=".$row["stock_id"]."&units_reqd=".$row["units_reqd"]."&wo_id=".$row['id']);
//        else
//            return '';
//    }
//    else
//        return '';
//}

$sql = get_sql_for_work_orders_requisition($outstanding_only, get_post('SelectedStockItem'), get_post('StockLocation'),
	get_post('OrderId'), get_post('OrderNumber'), check_value('OverdueOnly'));

$cols = array(
	_("#") => array('fun'=>'view_link', 'ord'=>''), 
	_("Reference") => array('ord'=>'', 'align'=>'center'), // viewlink 2 ?
//	_("Type") => array('fun'=>'wo_type_name'),
	_("Location") => array('align'=>'center'),
	_("Item") => array('fun'=>'view_stock', 'ord'=>'', 'align'=>'left'),
	_("Required") => array('fun'=>'dec_amount', 'align'=>'center'),
//	_("Manufactured") => array('fun'=>'dec_amount', 'align'=>'right'),
	_("Date") => array('name'=>'date_', 'type'=>'date', 'ord'=>'desc', 'align'=>'center'),
	_("Required By") => array('type'=>'date', 'ord'=>''),
	array('insert'=>true, 'fun'=> 'edit_link'),
	array('insert'=>true, 'fun'=> 'make_wo_link'),
	array('insert'=>true, 'fun'=> 'prt_link'),
);

$table =& new_db_pager('orders_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "90%";

display_db_pager($table);

end_form();
end_page();
