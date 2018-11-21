<?php

$page_security = 'SA_DIMTRANSVIEW';
$path_to_root="../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

function count_dim($role_id){

    $sql="SELECT COUNT(dim_id) FROM 0_user_dim WHERE dim_id = ".db_escape($role_id['id'])."";
    $result = db_query($sql, "could not process Requisition to Purchase Order");
    $row = db_fetch_row($result);

    global $path_to_root;

    label_cell(
        "<a target='_blank' "
        ."href='$path_to_root/admin/user_dimension.php?dim_id=".$role_id['id']."'"
        ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >"
        . round2($row[0])
        ."</a>");

}

if (isset($_GET['outstanding_only']) && $_GET['outstanding_only'])
{
	$outstanding_only = 1;
	page(_($help_context = "Search Outstanding Dimensions"), false, false, "", $js);
}
else
{
	$outstanding_only = 0;
	page(_($help_context = "Search Dimensions"), false, false, "", $js);
}
//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders'))
{
	$Ajax->activate('dim_table');
} elseif (get_post('_OrderNumber_changed'))
{
	$disable = get_post('OrderNumber') !== '';

	$Ajax->addDisable(true, 'FromDate', $disable);
	$Ajax->addDisable(true, 'ToDate', $disable);
	$Ajax->addDisable(true, 'type_', $disable);
	$Ajax->addDisable(true, 'OverdueOnly', $disable);
	$Ajax->addDisable(true, 'OpenOnly', $disable);

	if ($disable) {
		set_focus('OrderNumber');
	} else
		set_focus('type_');

	$Ajax->activate('dim_table');
}

//--------------------------------------------------------------------------------------

if (isset($_GET["stock_id"]))
	$_POST['SelectedStockItem'] = $_GET["stock_id"];

//--------------------------------------------------------------------------------------

start_form(false, false, $_SERVER['PHP_SELF'] ."?outstanding_only=$outstanding_only");

start_table(TABLESTYLE_NOBORDER);
start_row();

ref_cells(_("Reference:"), 'OrderNumber', '',null, '', true);

number_list_cells(_("Type"), 'type_', null, 1, 2, _("All"));
date_cells(_("From:"), 'FromDate', '', null, 0, 0, -5);
date_cells(_("To:"), 'ToDate');

start_row();
customer_list_cells('Customer', 'cust_name_', null,null, true);
sales_persons_list_cells("Sales Person", 'sales_person_', null, " ") ;
sales_areas_list_cells("Area", 'area_', null, " ");
end_row();
start_row();
text_cells(_("Quotation Nos") . ":", 'ref_1_', null, 25);
text_cells(_("PO Nos") . ":", 'ref_2_', null, 25);
text_cells(_("Working Status") . ":", 'ref_3_', null, 25);
end_row();

check_cells( _("Only Overdue:"), 'OverdueOnly', null);

if (!$outstanding_only)
{
   	check_cells( _("Only Open:"), 'OpenOnly', null);
}
else
	$_POST['OpenOnly'] = 1;

submit_cells('SearchOrders', _("Search"), '', '', 'default');

end_row();
end_table();

$dim = get_company_pref('use_dimension');

function view_link($row) 
{
	return get_dimensions_trans_view_str(ST_DIMENSION, $row["id"]);
}

function sum_dimension($row) 
{
	return get_dimension_balance($row['id'], $_POST['FromDate'], $_POST['ToDate']); 
}

function is_closed($row)
{
	return $row['closed'] ? _('Yes') : _('No');
}

function is_overdue($row)
{
	return date_diff2(Today(), sql2date($row["due_date"]), "d") > 0;
}

function edit_link($row)
{
	return pager_link(_("Edit"),
			"/dimensions/dimension_entry.php?trans_no=" . $row["id"], ICON_EDIT);
}
function get_customer_($row)
{
    $sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($row['cust_name']);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_sales_person($row)
{
    $sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($row['sales_person']);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_area($row)
{
    $sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($row['area']);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SE1')
{
$sql = get_sql_for_search_dimensions($dim, $_POST['FromDate'], $_POST['ToDate'],
	$_POST['OrderNumber'], $_POST['type_'], check_value('OpenOnly'), check_value('OverdueOnly'),
    $_POST['cust_name_'],$_POST['sales_person_'],$_POST['area_'],$_POST['ref_1_'],$_POST['ref_2_'],$_POST['ref_3_']);


$cols = array(
	_("#") => array('fun'=>'view_link'),
    _("Date") =>'date',
	_("Project ID"),
	_("Client") => array ('fun'=>'get_customer_'),
	_("Project Description"),
	_("Type"), 
	_("Sales Person") => array ('fun'=>'get_sales_person'),
	_("Area")  => array ('fun'=>'get_area'),
	_("Quotation Nos."),
	_("PO Nos."),
	_("Working Status"),
	_("Due Date") => array('name'=>'due_date', 'type'=>'date', 'ord'=>'asc'), 
	_("Closed") => array('fun'=>'is_closed'),
	_("Balance") => array('type'=>'amount', 'insert'=>true, 'fun'=>'sum_dimension'),
 _("User") => array ('fun'=>'count_dim'),
	array('insert'=>true, 'fun'=>'edit_link'),
_(""));
}
else
    {
        $sql = get_sql_for_search_dimensions2($dim, $_POST['FromDate'], $_POST['ToDate'],
            $_POST['OrderNumber'], $_POST['type_'], check_value('OpenOnly'), check_value('OverdueOnly')/*,
            $_POST['cust_name_'],$_POST['sales_person_'],$_POST['area_'],$_POST['ref_1_'],$_POST['ref_2_'],$_POST['ref_3_']*/);

        $cols = array(
            _("#") => array('fun'=>'view_link'),
            _("Date") =>'date',
            _("Reference"),
//            _("Client") => array ('fun'=>'get_customer_'),
            _("Name"),
            _("Type"),
//            _("Sales Person") => array ('fun'=>'get_sales_person'),
//            _("Area")  => array ('fun'=>'get_area'),
//            _("Quotation Nos."),
//            _("PO Nos."),
//            _("Working Status"),
            _("Due Date") => array('name'=>'due_date', 'type'=>'date', 'ord'=>'asc'),
            _("Closed") => array('fun'=>'is_closed'),
            _("Balance") => array('type'=>'amount', 'insert'=>true, 'fun'=>'sum_dimension'),
           _("User") =>   array('fun'=>'count_dim'),
            array('insert'=>true, 'fun'=>'edit_link'),
            _(""),
            );
    }


if ($outstanding_only) {
	$cols[_("Closed")] = 'skip';
}

$table =& new_db_pager('dim_tbl', $sql, $cols);
$table->set_marker('is_overdue', _("Marked dimensions are overdue."));

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();

