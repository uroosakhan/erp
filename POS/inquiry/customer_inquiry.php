<?php
$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/POS/includes/sales_ui.inc");
include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Customer Transactions"), isset($_GET['customer_id']), false, "", $js);

if (isset($_GET['customer_id']))
{
	$_POST['customer_id'] = $_GET['customer_id'];
}

if (isset($_GET['type']))
{
	$_POST['filterType'] = $_GET['type'];
}

//------------------------------------------------------------------------------------------------

start_form();

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("Reference"),'reference',null,null,false,null,null);

if (!$page_nested)
	customer_list_cells(_("Select a customer: "), 'customer_id', null, true, false, false, true);

if(isset($_GET['type']))
    date_cells(_("From:"), 'TransAfterDate', '', null, 0);
else
    date_cells(_("From:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'TransToDate', '', null);

if (!isset($_POST['filterType']))
	$_POST['filterType'] = 0;

cust_allocations_list_cells(null, 'filterType', $_POST['filterType'], true);
dimensions_list_cells(_("Dimension")." 1:", 'Dimension', null, true, " ", false, 1);
submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');
end_row();
end_table();

set_global_customer($_POST['customer_id']);

//------------------------------------------------------------------------------------------------

function display_customer_summary($customer_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
    if ($customer_record["dissallow_invoices"] != 0)
    {
    	echo "<center><font color=red size=4><b>" . _("CUSTOMER ACCOUNT IS ON HOLD") . "</font></b></center>";
    }

	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');

    start_table(TABLESTYLE, "width='80%'");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));
    table_header($th);

	start_row();
    label_cell($customer_record["curr_code"]);
    label_cell($customer_record["terms"]);
	amount_cell($customer_record["Balance"] - $customer_record["Due"]);
	amount_cell($customer_record["Due"] - $customer_record["Overdue1"]);
	amount_cell($customer_record["Overdue1"] - $customer_record["Overdue2"]);
	amount_cell($customer_record["Overdue2"]);
	amount_cell($customer_record["Balance"]);
	end_row();

	end_table();
}
//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if ($_POST['customer_id'] != "" && $_POST['customer_id'] != ALL_TEXT)
{
	$customer_record = get_customer_details($_POST['customer_id'], $_POST['TransToDate']);
    display_customer_summary($customer_record);
    echo "<br>";
}
div_end();

if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}
//------------------------------------------------------------------------------------------------

function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function order_view($row)
{
	return $row['order_']>0 ?
		get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
		: "";
}

function trans_view($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
	return	$row["type"] == ST_SALESINVOICE	? $row["due_date"] : '';
}

function gl_view($row)
{
	return get_gl_view_str($row["type"], $row["trans_no"]);
}
//ansar 26-08-2017
function fmt_amount($row)
{
    $value =
        $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type']==ST_JOURNAL ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    return price_format($value);
}
function fmt_debit($row)
{
//dz 16.6.17
/*
$value =
	    $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL ?
		-$row["TotalAmount"] : $row["TotalAmount"];
*/
	$value =
	    $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV ?
		-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>=0 ? price_format($value) : '';

}

function fmt_credit($row)
{
//dz 16.6.17
/*
$value =
	    !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL) ?
		-$row["TotalAmount"] : $row["TotalAmount"];
*/
	$value =
	    !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV) ?
		-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>0 ? price_format($value) : '';
}

function get_dimension_string_new($row, $html=false, $space=' ')
{

    $row1 = get_dimension($row['dimension_id'], true);
    $dim = $row1['name'];
    return $dim;
}
function credit_link($row)
{
	global $page_nested;

	if ($page_nested)
		return '';
	return $row['type'] == ST_SALESINVOICE && $row["Outstanding"] > 0 ?
		pager_link(_("Credit This") ,
			"/sales/customer_credit_invoice.php?InvoiceNumber=". $row['trans_no'], ICON_CREDIT):'';
}

function edit_link($row)
{
	global $page_nested;

	$str = '';
	if ($page_nested)
		return '';

	return $row['type'] == ST_CUSTCREDIT && $row['order_'] ? '' : 	// allow  only free hand credit notes edition
			trans_editor_link($row['type'], $row['trans_no']);
}

function prt_link($row)
{
  	if ($row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT || $row['type'] == ST_CPV || $row['type'] == ST_CRV)
		return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_CUSTPAYMENT, ICON_PRINT);
  	elseif ($row['type'] == ST_BANKPAYMENT) // bank payment printout not defined yet.
		return '';
 	elseif($row['type'] == ST_CUSTDELIVERY )
    return print_document_link($row['dimension_id']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
    else
 		return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}
function prt_link2($row)
{
  	if ($row['type'] == ST_SALESINVOICE)
	    return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_SALESTAX, ICON_PRINT);
  	else
		return '';
// 	else
// 		return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}

function check_overdue($row)
{
	return $row['OverDue'] == 1
		&& floatcmp($row["TotalAmount"], $row["Allocated"]) != 0;
}
//------------------------------------------------------------------------------------------------
$sql = get_sql_for_customer_inquiry_pos(get_post('TransAfterDate'), get_post('TransToDate'),
	get_post('customer_id'), get_post('filterType'), get_post('reference'), get_post('Dimension'));

//------------------------------------------------------------------------------------------------
//db_query("set @bal:=0");

$cols = array(
	_("Type") => array('fun'=>'systype_name', 'ord'=>''),
	_("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
	_("Order") => array('fun'=>'order_view', 'align'=>'right'),
	_("Reference"),
    _("Dimension")=>array('fun'=>'get_dimension_string_new'),
	_("Date") => array('name'=>'tran_date', 'type'=>'date', 'ord'=>'desc'),
	_("Due Date") => array('type'=>'date', 'fun'=>'due_date'),
	_("Customer") => array('ord'=>''), 
	_("Branch") => array('ord'=>''),
    _("Currency") => array('align'=>'center'),
	_("Debit") => array('align'=>'right', 'fun'=>'fmt_debit'), 
	_("Credit") => array('align'=>'right','insert'=>true, 'fun'=>'fmt_credit'),
    _("Amount") => array('align'=>'right', 'fun'=>'fmt_amount'), //ansar 26-08-2017
    //_("RB") => array('align'=>'right', 'type'=>'amount'), ansar 26-08-2017
		array('insert'=>true, 'fun'=>'gl_view'),
		array('insert'=>true, 'fun'=>'credit_link'),
		array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link2'),
		array('insert'=>true, 'fun'=>'prt_link')

	);


if ($_POST['customer_id'] != ALL_TEXT) {
	$cols[_("Customer")] = 'skip';
	$cols[_("Currency")] = 'skip';
}
if ($_POST['filterType'] == ALL_TEXT)
	$cols[_("RB")] = 'skip';

$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "85%";

display_db_pager($table);

end_form();
end_page();
