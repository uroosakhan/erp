<?php

$page_security = 'SA_SUPPLIER';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

page(_($help_context = "Suppliers Inquiry"), false, false, "", $js);

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

check_db_has_tax_groups(_("There are no tax groups defined in the system. At least one tax group is required before proceeding."));

if (isset($_GET['supplier_id']))
{
	$_POST['supplier_id'] = $_GET['supplier_id'];
}

$supplier_id = get_post('supplier_id');
//--------------------------------------------------------------------------------------------
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
	return print_document_link($row['supplier_id'], _("Print"), true, ST_PURCHORDER12, ICON_PRINT);
}

function edit_link($row)
{
	if (@$_GET['popup'])
		return '';
	global $trans_type;
	$modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
	return pager_link( _("Edit"),
		"/purchasing/manage/suppliers.php?supplier_id=" . $row['supplier_id'], ICON_EDIT);
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
function get_tax_group_name($tax_group_id)
{
	$sql = "SELECT name FROM ".TB_PREF."tax_groups WHERE id=".db_escape($tax_group_id['tax_group_id']);

	$result = db_query($sql, "could not get supplier");

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

ref_cells(_("Supplier Name"), 'supp_name', '',null, '', true);
ref_cells(_("Supplier Short Name"), 'supp_ref', '',null, '', true);
ref_cells(_("Mailing Address"), 'address', '',null, '', true);
ref_cells(_("Physical Address"), 'supp_address', '',null, '', true);
ref_cells(_("NTN No"), 'ntn_no', '',null, '', true);
end_row();

start_row();
ref_cells(_("GST No"), 'gst_no', '',null, '', true);
ref_cells(_("Customer No"), 'supp_account_no', '',null, '', true);
ref_cells(_("Website"), 'website', '',null, '', true);
ref_cells(_("Bank Name/Account"), 'bank_account', '',null, '', true);
ref_cells(_("Contact"), 'contact', '',null, '', true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

currencies_dashboard_list_cells(_("Supplier's Currency"), 'curr_code', null, true, false, 1);
tax_groups_list_cells(_("Tax Group"), 'tax_group_id', null, true);
payment_term_list_cells(_("Payment Terms"), 'payment_terms', null, true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
dimensions_list_cells("Dimension1 :", 'dimension_id', null, true, " ", 1);
dimensions_list_cells("Dimension2 :", 'dimension2_id', null, true, " ", 2);

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

if (!@$_GET['popup'])


	if ($trans_type == ST_SALESQUOTE)
		check_cells(_("Show All:"), 'show_all');

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
hidden('order_view_mode', $_POST['order_view_mode']);
hidden('type', $trans_type);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------
//	Orders inquiry table
//
$sql = get_sql_for_supplier_log_view( $_POST['supp_name'], $_POST['supp_ref'],
	$_POST['address'], $_POST['supp_address'], $_POST['ntn_no'], $_POST['gst_no'], $_POST['contact'],
	$_POST['supp_account_no'], $_POST['website'], $_POST['bank_account'], $_POST['curr_code'],
	$_POST['payment_terms'], $_POST['tax_included'], $_POST['tax_group_id'], $_POST['credit_limit'],
	$_POST['purchase_account'], $_POST['payable_account'], $_POST['payment_discount_account'],
	$_POST['dimension_id'], $_POST['dimension2_id'], $_POST['notes']);


$cols = array(
    array('insert'=>true, 'fun'=>'edit_link'),
	_("Supplier Name"),
	_("Supplier Short Name"),
	_("Mailing Address"),
	_("Physical Address"),
	_("NTN No"),
	_("GST No"),
	_("Contact"),
	_("Customer No"),
	_("Website"),
	_("Bank Name/Account"),
	_("Supplier's Currency"),
	_("Payment Terms") => array('fun'=>'get_payment_term_name'),
	_("Tax Included")=> array('fun'=>'get_tax_included'),
	_("Tax Group") => array('fun'=>'get_tax_group_name'),
	_("Credit Limit"),
	_("Purchase Account") => array('fun'=>'get_gl_account1'),
	_("Payable Account") => array('fun'=>'get_gl_account2'),
	_("Purchase Discount Account") => array('fun'=>'get_gl_account3'),
	_("Dimension 1") => array('fun'=>'get_dimension_name'),
	_("Dimension 2") => array('fun'=>'get_dimension2_name'),
	_("General Notes"),
	array('insert'=>true, 'fun'=>'edit_link'),
	array('insert'=>true, 'fun'=>'prt_link')

);



$table =& new_db_pager('orders_tbl', $sql, $cols);
// $table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "80%";

//	hyperlink_params($path_to_root . "/sales/customer_invoice.php", _("Confirm Delivery and Invoice"), "DeliveryNumber=$delivery_no");




echo '<center><a href="suppliers.php" target="_blank"><input type="button" value="Add Supplier"></a></center>';

display_db_pager($table);
//submit_center('Update', _("Update"), true, '', null);


if (!@$_GET['popup'])
{
	end_form();
	end_page();
}
?>