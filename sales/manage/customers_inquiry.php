<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$page_security = 'SA_SALESTRANSVIEW';

set_page_security( @$_POST['order_view_mode'],
	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
		'InvoiceTemplates' => 'SA_SALESINVOICE'),
	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
		'InvoiceTemplates' => 'SA_SALESINVOICE')
);

if (get_post('type'))
	$trans_type = $_POST['type'];
elseif (isset($_GET['type']) && $_GET['type'] == ST_SALESQUOTE)
	$trans_type = ST_SALESQUOTE;
else
	$trans_type = ST_SALESORDER;

if ($trans_type == ST_SALESORDER)
{
	if (isset($_GET['OutstandingOnly']) && ($_GET['OutstandingOnly'] == true))
	{
		$_POST['order_view_mode'] = 'OutstandingOnly';
		$_SESSION['page_title'] = _($help_context = "Search Outstanding Sales Orders");
	}
	elseif (isset($_GET['InvoiceTemplates']) && ($_GET['InvoiceTemplates'] == true))
	{
		$_POST['order_view_mode'] = 'InvoiceTemplates';
		$_SESSION['page_title'] = _($help_context = "Search Template for Invoicing");
	}
	elseif (isset($_GET['DeliveryTemplates']) && ($_GET['DeliveryTemplates'] == true))
	{
		$_POST['order_view_mode'] = 'DeliveryTemplates';
		$_SESSION['page_title'] = _($help_context = "Select Template for Delivery");
	}
	elseif (!isset($_POST['order_view_mode']))
	{
		$_POST['order_view_mode'] = false;
		$_SESSION['page_title'] = _($help_context = "Customers Inquiry");
	}
}
else
{
	$_POST['order_view_mode'] = "Quotations";
	$_SESSION['page_title'] = _($help_context = "Search All Sales Quotations");
}

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 600);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page($_SESSION['page_title'], false, false, "", $js);
}

if (isset($_GET['selected_customer']))
{
	$selected_customer = $_GET['selected_customer'];
}
elseif (isset($_POST['selected_customer']))
{
	$selected_customer = $_POST['selected_customer'];
}
else
	$selected_customer = -1;

//---------------------------------------------------------------------------------------------

if (isset($_POST['SelectStockFromList']) && ($_POST['SelectStockFromList'] != "") &&
	($_POST['SelectStockFromList'] != ALL_TEXT))
{
	$selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
	unset($selected_stock_item);
}
//---------------------------------------------------------------------------------------------
//	Query format functions
//
function check_overdue($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESQUOTE)
		return (date1_greater_date2(Today(), sql2date($row['delivery_date'])));
	else
		return ($row['type'] == 0
			&& date1_greater_date2(Today(), sql2date($row['ord_date']))
			&& ($row['TotDelivered'] < $row['TotQuantity']));
}

function view_link($dummy, $order_no)
{
	global $trans_type;
	return  get_customer_trans_view_str($trans_type, $order_no);
}

function prt_link($row)
{
	global $trans_type;
	return print_document_link($row['debtor_no'], _("Print"), true, ST_SALESORDER12, ICON_PRINT);
}

function edit_link($row)
{
	if (@$_GET['popup'])
		return '';
	global $trans_type;
	$modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
	return pager_link( _("Edit"),
		"/sales/manage/customers.php?debtor_no=" . $row['debtor_no'], ICON_EDIT);
}

function dispatch_link($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESORDER)
		return pager_link( _("Dispatch"),
			"/sales/customer_delivery.php?OrderNumber=" .$row['order_no'], ICON_DOC);
	else
		return pager_link( _("Sales Order"),
			"/sales/sales_order_entry.php?OrderNumber=" .$row['order_no'], ICON_DOC);
}

function invoice_link($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESORDER)
		return pager_link( _("Invoice"),
			"/sales/sales_order_entry.php?NewInvoice=" .$row["order_no"], ICON_DOC);
	else
		return '';
}

function delivery_link($row)
{
	return pager_link( _("Delivery"),
		"/sales/sales_order_entry.php?NewDelivery=" .$row['order_no'], ICON_DOC);
}

function order_link($row)
{
	return pager_link( _("Sales Order"),
		"/sales/sales_order_entry.php?NewQuoteToSalesOrder=" .$row['order_no'], ICON_DOC);
}

function followup_link($row)
{

    return viewer_link('',"sales/manage/crm_info.php?debtor_no=".$row['debtor_no']."", "btn btn-default btn-xs",'',("Show customer of this task"));
}

function tmpl_checkbox($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESQUOTE)
		return '';
	if (@$_GET['popup'])
		return '';
	$name = "chgtpl" .$row['order_no'];
	$value = $row['type'] ? 1:0;

// save also in hidden field for testing during 'Update'

	return checkbox(null, $name, $value, true,
		_('Set this order as a template for direct deliveries/invoices'))
	. hidden('last['.$row['order_no'].']', $value, false);
}
//---------------------------------------------------------------------------------------------
// Update db record if respective checkbox value has changed.
//
function change_tpl_flag($id)
{
	global	$Ajax;

	$sql = "UPDATE ".TB_PREF."sales_orders SET type = !type WHERE order_no=$id";

	db_query($sql, "Can't change sales order type");
	$Ajax->activate('orders_tbl');
}

$id = find_submit('_chgtpl');
if ($id != -1)
	change_tpl_flag($id);

if (isset($_POST['Update']) && isset($_POST['last'])) {
	foreach($_POST['last'] as $id => $value)
		if ($value != check_value('chgtpl'.$id))
			change_tpl_flag($id);
}

$show_dates = !in_array($_POST['order_view_mode'], array('OutstandingOnly', 'InvoiceTemplates', 'DeliveryTemplates'));
//---------------------------------------------------------------------------------------------
//	Order range form
//
///marina--------------
function get_sale_type($sales_type_id)
{
	$sql = "SELECT sales_type FROM ".TB_PREF."sales_types WHERE id=".db_escape($sales_type_id['sales_type']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
if (get_post('_OrderNumber_changed') || get_post('_OrderReference_changed')) // enable/disable selection controls
{
	$disable = get_post('OrderNumber') !== '' || get_post('OrderReference') !== '';

	if ($show_dates) {
		$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
		$Ajax->addDisable(true, 'OrdersToDate', $disable);
	}

	$Ajax->activate('orders_tbl');
}

if (!@$_GET['popup'])
	start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();

ref_cells(_("Customer Name:"), 'name', '',null, '', true);
ref_cells(_("Short Name:"), 'debtor_ref', '',null, '', true);
ref_cells(_("Address:"), 'address', '',null, '', true);

global $leftmenu_save, $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DSSLS') {
    ref_cells(_("File No"), 'ntn_no', '', null, '', true);
    ref_cells(_("Age"), 'tax_id', '',null, '', true);
}
else
{
    ref_cells(_("NTN No:"), 'ntn_no', '', null, '', true);
    ref_cells(_("STRN:"), 'tax_id', '',null, '', true);
}

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

currencies_dashboard_list_cells(_("Customer's Currency"), 'curr_code', null, true, false, 1);
sales_type_list_cells(_("Sales Type"), 'sales_type', null, true);
payment_term_list_cells(_("Payment Terms"), 'payment_terms', null, true);

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
credit_stats_list_cells(_("Credit Status"), 'credit_status', null, true);
dimensions_list_cells("Dimension1", 'dimension_id', null, true, " ", 1);
dimensions_list_cells("Dimension2", 'dimension2_id', null, true, " ", 2);

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

if (!@$_GET['popup'])

if ($trans_type == ST_SALESQUOTE)
	check_cells(_("Show All:"), 'show_all');


hidden('order_view_mode', $_POST['order_view_mode']);
hidden('type', $trans_type);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------
//	Orders inquiry table
//
$sql = get_sql_for_customer_log_view($selected_customer, $_POST['name'], $_POST['debtor_ref'],
	$_POST['address'], $_POST['ntn_no'], $_POST['tax_id'], $_POST['curr_code'], $_POST['sales_type'],
	$_POST['payment_terms'], $_POST['credit_status'], $_POST['dimension_id'], $_POST['dimension2_id'],
	@$_POST['customer_id']);

global $leftmenu_save, $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DSSLS') {
    
$cols = array(
        array('insert'=>true, 'fun'=>'edit_link'),
	_("CUSTOMER NAME"),
	_("SHORT NAME"),
	_("ADDRESS"),
	_("FILE NO"),
	_("AGE"),
	_("CURRENCY"),
	_("SALES TYPE") => array('fun'=>'get_sale_type'),
	_("DISCOUNT %") => array('type'=>'percent'),
	_("PAYMENT DISCOUNT %") => skip,
	_("CREDIT LIMIT") => array('type'=>'amount'),
	_("CREDIT ALLOWED") => array('type'=>'amount'),
	_("PAYMENT TERMS") => array('fun'=>'get_payment_term_name'),
	_("CREDIT STATUS") => array('fun'=>'get_credit_status_name'),
	_("DIMENSION 1") => array('fun'=>'get_dimension_name'),
	_("DIMENSION 2") => array('fun'=>'get_dimension2_name'),
	_("REG DATE"),
	_("Other Info")=>array('insert'=>true, 'fun'=>'followup_link'),
	  array('insert'=>true, 'fun'=>'edit_link'),
        array('insert'=>true, 'fun'=>'prt_link')
);
}
else
{
    $cols = array(
        array('insert'=>true, 'fun'=>'edit_link'),
	_("CUSTOMER NAME"),
	_("SHORT NAME"),
	_("ADDRESS"),
	_("NTN NO."),
	_("STRN"),
	_("CURRENCY"),
	_("SALES TYPE") => array('fun'=>'get_sale_type'),
	_("DISCOUNT %") => array('type'=>'percent'),
	_("PAYMENT DISCOUNT %") => skip,
	_("CREDIT LIMIT") => array('type'=>'amount'),
	_("CREDIT ALLOWED") => array('type'=>'amount'),
	_("PAYMENT TERMS") => array('fun'=>'get_payment_term_name'),
	_("CREDIT STATUS") => array('fun'=>'get_credit_status_name'),
	_("DIMENSION 1") => array('fun'=>'get_dimension_name'),
	_("DIMENSION 2") => array('fun'=>'get_dimension2_name'),
	_("GENERAL NOTES"),
	_("Other Info")=>array('insert'=>true, 'fun'=>'followup_link'),
	  array('insert'=>true, 'fun'=>'edit_link'),
        array('insert'=>true, 'fun'=>'prt_link')
	  );
}



$table =& new_db_pager('orders_tbl', $sql, $cols);
// $table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "100%";

//	hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");

    
    echo '<center><a href="customers.php" target="_blank"><input type="button" value="+ ADD CUSTOMER"></a>
    <a href="/modules/import_items/import_customers.php?action=import" target="_blank"><input type="button" value="IMPORT"></a>
    <a href="/modules/import_items/import_customers.php?action=export" target="_blank"><input type="button" value="EXPORT"></a>
    </center> ';


display_db_pager($table);
//submit_center('Update', _("Update"), true, '', null);


if (!@$_GET['popup'])
{
	end_form();
	end_page();
}
?>