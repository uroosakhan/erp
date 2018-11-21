<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/payroll/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Clients Deadline Inquiry"), isset($_GET['emp_id']), false, "", $js);
}
if (isset($_GET['emp_id'])){
	$_POST['emp_id'] = $_GET['emp_id'];
}
if (isset($_GET['FromDate'])){
	$_POST['TransAfterDate'] = $_GET['FromDate'];
}
if (isset($_GET['ToDate'])){
	$_POST['TransToDate'] = $_GET['ToDate'];
}

if (isset($_GET['id'])){
	$_POST['id'] = $_GET['id'];
}
//------------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['emp_id']))
	$_POST['emp_id'] = get_global_supplier();

//for second line
start_table(TABLESTYLE_NOBORDER);
start_row();
if (!@$_GET['popup'])
employee_deadline_list_cells(_("Employee:"), 'emp_id', null,true);
emp_dept_cells(_("Department:"), 'dept_id', null,true);	
client_list_cells1(_("Client:"), 'client_id', null,true);

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

if (!@$_GET['popup'])
clients_stock_list_cells(_("Nature Of Work :"),'stock_id', null,true);
date_cells(_("Dead Line Date:"), 'deadline_date', '', null, -30);
date_cells(_("Delivery Date:"), 'delivery_date');

submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();
set_global_supplier($_POST['emp_id']);

div_start('totals_tbl');
if (($_POST['emp_id'] != "") && ($_POST['emp_id'] != ALL_TEXT))
{
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

function trans_view($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
	return ($row["type"]== ST_SUPPINVOICE) || ($row["type"]== ST_SUPPCREDIT) ? $row["due_date"] : '';
}

function gl_view($row)
{
	return get_gl_view_str($row["type"], $row["trans_no"]);
}

function credit_link($row)
{
	if (@$_GET['popup'])
		return '';
	return $row['type'] == ST_SUPPINVOICE && $row["TotalAmount"] - $row["Allocated"] > 0 ?
		pager_link(_("Credit This"),
			"/purchasing/supplier_credit.php?New=1&invoice_no=".
			$row['trans_no'], ICON_CREDIT)
			: '';
}

function fmt_debit($row)
{
	$value = $row["TotalAmount"];
	return $value>0 ? price_format($value) : '';

}

function fmt_credit($row)
{
	$value = -$row["TotalAmount"];
	return $value>0 ? price_format($value) : '';
}

function prt_link($row)
{
  	if ($row['type'] == ST_SUPPAYMENT || $row['type'] == ST_BANKPAYMENT || $row['type'] == ST_SUPPCREDIT) 
 		return print_document_link($row['trans_no']."-".$row['type'], _("Print Remittance"), true, ST_SUPPAYMENT, ICON_PRINT);
}

function check_overdue($row)
{
	return $row['OverDue'] == 1
		&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0);
} 

function edit_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
  return pager_link( _("Delete"),
    "/payroll/manage/clients_deadline.php?$modify=" .$row['id'], ICON_EDIT);
}


function delete_emp_info($delete)
{
	$sql="DELETE FROM ".TB_PREF."leave WHERE id=".db_escape($delete);
	db_query($sql,"could not delete Leave");
	display_notification(_('Selected leave  has been deleted'));
}
//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($emp_id, $emp_dept, $client_id,$stock_id, $datefrom,$dateto)
{
	
	 $sql = "SELECT ".TB_PREF."clieny_dealine.`date`, 
	 ".TB_PREF."employee.emp_name,
	 ".TB_PREF."debtors_master.name As clientname,
	 ".TB_PREF."dept.description As department,
	 ".TB_PREF."stock_master.description As worklist,
	 
	 ".TB_PREF."clieny_dealine.`deadline_date`,
	  ".TB_PREF."clieny_dealine.`delivery_date`,
	  ".TB_PREF."clieny_dealine.`comments`,
	   ".TB_PREF."clieny_dealine.`id` FROM ".TB_PREF."clieny_dealine
INNER JOIN ".TB_PREF."employee ON ".TB_PREF."clieny_dealine.`emp_id` = ".TB_PREF."employee.`employee_id` 
INNER JOIN ".TB_PREF."debtors_master ON ".TB_PREF."clieny_dealine.`client_id` = ".TB_PREF."debtors_master.`debtor_no` 
INNER JOIN ".TB_PREF."dept ON ".TB_PREF."dept.`id` = ".TB_PREF."clieny_dealine.`dept_id`
INNER JOIN ".TB_PREF."stock_master ON ".TB_PREF."stock_master.`stock_id` = ".TB_PREF."clieny_dealine.`stock_id`
WHERE (
".TB_PREF."clieny_dealine.`deadline_date` >=  '$datefrom'
AND ".TB_PREF."clieny_dealine.`delivery_date` <=  '$dateto')";
	if ($emp_id != '') 
	{
   		$sql .= " AND ".TB_PREF."clieny_dealine.emp_id = ".db_escape($emp_id);
	}
	if ($emp_dept != '') 
	{
   		$sql .= " AND ".TB_PREF."clieny_dealine.dept_id = ".db_escape($emp_dept);
	}
	if ($client_id != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."clieny_dealine.client_id = ".db_escape($client_id);
	}
	if ($stock_id !='') 
	{
   		$sql .= " AND ".TB_PREF."clieny_dealine.stock_id = ".db_escape($stock_id);
	}
   	return $sql;
	
}


$sql = get_sql_for_employee_inquiry($_POST['emp_id'],$_POST['dept_id'],$_POST['client_id'],$_POST['stock_id'],sql2date($_POST['deadline_date']),sql2date($_POST['delivery_date']));
 $cols = array(
            _("Date")=> array('name'=>'date', 'type'=>'date', 'ord'=>'desc'),
  			_("Employee Name"),
			_("Client Name"),
			_("Department"),
			_("Nature Of Work"),
			_("Dead Line Date")=> array('name'=>'deadline_date', 'type'=>'date', 'ord'=>'desc'),
			_("Delivery Date")=> array('name'=>'delivery_date', 'type'=>'date', 'ord'=>'desc'),
		   _("Comments"),
    );
	array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));


		

if ($_POST['emp_id'] != ALL_TEXT)
{
	$cols[_("Supplier")] = 'skip';
	$cols[_("Currency")] = 'skip';
}


//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "85%";


	
display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
}
?>
