<?php
$page_security = 'SA_SALESMAN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
page(_($help_context = "Gate Pass Dashboard"));
//----------------------------------------------------------------------------------------------

//---------------------------------------------------------------------------------------------
//	Query format functions
//
//function check_overdue($row)
//{
//    global $trans_type;
//    if ($trans_type == ST_SALESQUOTE)
//        return (date1_greater_date2(Today(), sql2date($row['delivery_date'])));
//    else
//        return ($row['type'] == 0
//            && date1_greater_date2(Today(), sql2date($row['delivery_date']))
//            && ($row['TotDelivered'] < $row['TotQuantity']));
//}

//function view_link($dummy, $order_no)
//{
//    global $trans_type;
//    return  get_customer_trans_view_str($trans_type, $order_no);
//}
//function get_dimension_string_new($row, $html=false, $space=' ')
//{
//
//
//    $row1 = get_dimension($row['dimension_id'], true);
//    $dim = $row1['name'];
//
//
//    return $dim;
//}


function edit_link($row)
{
//    return trans_editor_link(ST_WORKORDER, $row['parent']);
    return pager_link( _("Dispatch"),
        "/sales/manage/multiple_gate_pass.php?gate_pass_no=" .$row['gate_pass_no'], ICON_EDIT);
}

//==============================================================================
$show_dates = !in_array($_POST['order_view_mode'], array('InvoiceTemplates', 'DeliveryTemplates'));
//---------------------------------------------------------------------------------------------
//	Order range form
//
//if (get_post('SearchOrders'))
//{
//$Ajax->activate('gate_tbl');
//}

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("Order #:"), 'OrderNumber', '',null, '', true);
//ref_cells(_("Ref"), 'OrderReference', '',null, '', true);
//date_cells(_("Order Date:"), 'OrdersAfterDate', '', null, -205);
//date_cells(_("Required Date:"), 'OrdersToDate', '', null, 159);
//locations_list_cells(_("Location:"), 'StockLocation', null, true, true);
//stock_items_list_cells(_("Item:"), 'SelectStockFromList', null, true, true);
//customer_list_cells(_("Select a customer: "), 'customer_id', null, true, true);
//customer_branches_list_cells(_("Branch:"),
//        $_POST['customer_id'], 'branch_id', null, true, true);

//submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
//hidden('order_view_mode', $_POST['order_view_mode']);
//hidden('type', $trans_type);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------
//	Orders inquiry table
//


$sql = get_sql_for_gate_pass();


$cols = array(
    _("id") =>array('ord'=>''),
    _("Gate Pass No"),
    _("Driver Name"),
    _("Vehicle No"),
    _("Delivery No"),
    _("Gate Pass Date")
);

array_append($cols, array(
    array('insert'=>true, 'fun'=>'edit_link')));

$table =&new_db_pager('gate_tbl', $sql, $cols);


$table->width = "80%";

display_db_pager($table);
//submit_center('Update', _("Update"), true, '', null);

end_form();
end_page();
