<?php

$page_security = 'SA_ITEMSSTATVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

$js = "";
if ($SysPrefs->use_popup_windows && $SysPrefs->use_popup_search)
	$js .= get_js_open_window(900, 500);

if (isset($_GET['stock_id']))
	$_POST['stock_id'] = $_GET['stock_id'];

page(_($help_context = "Inventory Item Status"), isset($_GET['stock_id']), false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

if (list_updated('stock_id')) 
	$Ajax->activate('status_tbl');
//----------------------------------------------------------------------------------------------------

check_db_has_stock_items(_("There are no items defined in the system."));

start_form();
global $Ajax;
if (!isset($_POST['stock_id']))
	$_POST['stock_id'] = get_global_stock_item();

//if (!$page_nested)
//{
//	echo "<center> " . _("Item:"). " ";
//	echo stock_costable_items_list('stock_id', $_POST['stock_id'], false, true);
//}

function get_loc_code_from_sales_order($order_no)
{
    $sql = "SELECT * FROM ".TB_PREF."sales_orders WHERE order_no = ".db_escape($order_no);
    $sql .= " AND trans_type = ".db_escape(30);
    $result = db_query($sql, "Error");
    return db_fetch($result);
}
if(list_updated('stock_id')){
	$Ajax->activate('batch');
}
$pref=get_company_prefs();
if($pref['batch'] == 1)
batch_list_cells(_("Batch"), $_POST['stock_id'], 'batch', null, true, false, true, true, $_POST['StockLocation']);

echo "<br>";

echo "<hr></center>";
$customer_id=$_GET['customer_id'];
set_global_stock_item($_POST['stock_id']);

$mb_flag = get_mb_flag($_POST['stock_id']);
$kitset_or_service = false;

div_start('status_tbl');
if (is_service($mb_flag))
{
	display_note(_("This is a service and cannot have a stock holding, only the total quantity on outstanding sales orders is shown."), 0, 1);
	$kitset_or_service = true;
}
//if($_GET['type'] == 10)
    $result = get_history($_GET['stock_id'], $customer_id);
//elseif($_GET['type'] == 30)
//    $result = get_history_sales($_GET['stock_id'], $customer_id);

start_table(TABLESTYLE);

$th = array(_("Trans No"),_("Date"), _("Quantity"), _("UOM"), _("Rate"), _("Location Code"));

table_header($th);
$dec = get_qty_dec($_POST['stock_id']);
$j = 1;
$k = 0; //row colour counter
//if($_GET['type'] == 10)
//{
while ($myrow = db_fetch($result))
{
    $GetSaleOrder = get_loc_code_from_sales_order($myrow['order_']);
    start_row();
    view_stock_delivery_cell($myrow["debtor_trans_no"]);
    label_cell(sql2date($myrow["tran_date"]));
    amount_cell(abs($myrow["quantity"]),null,$dec);
    label_cell($myrow["units_id"]);
    amount_cell($myrow["unit_price"],null,$dec);
    label_cell(get_location_name($GetSaleOrder["from_stk_loc"]));

    end_row();

}
//}
//elseif($_GET['type'] == 30)
//{
//    while ($myrow = db_fetch($result))
//    {
//        start_row();
//        view_stock_delivery_cell($myrow["order_no"]);
////      label_cell($myrow["trans_no"]);
//        label_cell(sql2date($myrow["ord_date"]));
//        amount_cell(abs($myrow["unit_price"]), null, $dec);
//        label_cell($myrow["units_id"]);
//        amount_cell($myrow["price"], null, $dec);
//        label_cell($myrow["from_stk_loc"]);
//        end_row();
//    }
//}

end_table();
div_end();
end_form();
end_page();

