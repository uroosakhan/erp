<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/payroll/includes/purchasing_ui.inc");
include_once($path_to_root . "/payroll/includes/db/dayattendance_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Attendance Per Day Inquiry"), isset($_GET['employee_id']), false, "", $js);
}
if (isset($_GET['employee_id'])){
	$_POST['employee_id'] = $_GET['employee_id'];
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
if (isset($_GET['emp_dept'])){
	$_POST['emp_dept'] = $_GET['emp_dept'];
}

/*

if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Department:"), $_POST['emp_dept']);
		hidden('emp_dept', $_POST['emp_dept']);
	} 
	else 
	{
		emp_dept_row(_("Department:"), 'emp_dept', null);
	}	*/
//------------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['employee_id']))
	$_POST['employee_id'] = get_global_supplier();
//for second page
start_table(TABLESTYLE_NOBORDER);
start_row();
if (!@$_GET['popup'])
	employee_list2_cells(_("Select a Employee:"), 'employee_id', null, true, false, false, !@$_GET['popup']);
	emp_dept_cells(_("Department:"), 'emp_dept', null,true);
emp_grade_cells(_("Grade:"), 'emp_grade', null,true);
	
	
end_row();
end_table();	
	

start_table(TABLESTYLE_NOBORDER);
start_row();



//date_cells(_("From:"), 'TransAfterDate', '', null, -30);
//date_cells(_("To:"), 'TransToDate');

//supp_transactions_list_cell("filterType", null, true);


emp_desg_cells(_("Designation:"), 'emp_desig', null,true);	
//emp_grade_cells(_("Grade:"), 'emp_grade', null,true);
//emp_desg_cells(_("Designation:"), 'emp_desig', null,true);
date_cells(_("From:"), 'datefrom', '', null, -30);
date_cells(_("To:"), 'dateto');

submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();
set_global_supplier($_POST['employee_id']);

//------------------------------------------------------------------------------------------------
/*
function display_supplier_summary($supplier_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');
	

    start_table(TABLESTYLE, "width=80%");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));

	table_header($th);
    start_row();
	label_cell($supplier_record["curr_code"]);
    label_cell($supplier_record["terms"]);
    amount_cell($supplier_record["Balance"] - $supplier_record["Due"]);
    amount_cell($supplier_record["Due"] - $supplier_record["Overdue1"]);
    amount_cell($supplier_record["Overdue1"] - $supplier_record["Overdue2"]);
    amount_cell($supplier_record["Overdue2"]);
    amount_cell($supplier_record["Balance"]);
    end_row();
    end_table(1);
} */

//ansar edit

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if (($_POST['employee_id'] != "") && ($_POST['employee_id'] != ALL_TEXT))
{
	//$supplier_record = get_supplier_details($_POST['supplier_id'], $_POST['TransToDate']);
    //display_supplier_summary($supplier_record);
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
function get_presence_through_dept_date($dept_id,$date)
{
	
	$sql = "SELECT * FROM ".TB_PREF."presence WHERE emp_dept=".db_escape($dept_id)."
	AND
	date=".db_escape($date);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}
function edit_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'emp_dept';
  return pager_link( _("Edit"),
    "/payroll/manage/dayattendance.php?emp_dept=".$row['emp_dept'].'&date='.$row['date'], ICON_EDIT);
}

//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($employee_id, $emp_dept, $emp_grade,$emp_desig,$datefrom,$dateto)
{
	 $sql = "SELECT ".TB_PREF."employee.emp_name,".TB_PREF."grade.description As grade,".TB_PREF."desg.description As desination,".TB_PREF."dept.description, ".TB_PREF."presence.date,".TB_PREF."presence.remark, ".TB_PREF."presence.present, IF(  `present` =0,  'No',  'Yes' ) AS present,".TB_PREF."presence.`id`,".TB_PREF."presence.`emp_dept`
FROM ".TB_PREF."presence
INNER JOIN ".TB_PREF."employee ON ".TB_PREF."presence.`employee_id` = ".TB_PREF."employee.`employee_id` 
INNER JOIN ".TB_PREF."dept ON ".TB_PREF."dept.`id` = ".TB_PREF."presence.`emp_dept`
INNER JOIN ".TB_PREF."grade ON ".TB_PREF."grade.`id` = ".TB_PREF."employee.`emp_grade`
INNER JOIN ".TB_PREF."desg ON ".TB_PREF."desg.`id` = ".TB_PREF."employee.`emp_desig`
WHERE (
".TB_PREF."presence.date >=  '$datefrom'
AND ".TB_PREF."presence.date <=  '$dateto'
)";
	/* $sql = "SELECT ".TB_PREF."employee.emp_name, ".TB_PREF."presence.date, ".TB_PREF."presence.present
FROM ".TB_PREF."presence
INNER JOIN ".TB_PREF."employee ON ".TB_PREF."presence.`employee_id` = ".TB_PREF."employee.`employee_id`
WHERE ".TB_PREF."presence.date >=  '$datefrom'
AND ".TB_PREF."presence.date <=  '$dateto' ";*/
	
	
	
      /*  $sql = "SELECT 
	emp_code, emp_name, emp_father, emp_cnic, DOB, j_date, l_date ,emp_reference, emp_home_phone, emp_mobile, emp_email,emp_bank, emp_address, notes
	
    	FROM ".TB_PREF."employee
    	WHERE ".TB_PREF."employee.inactive != '1' ";*/
	
/*	if($employee_id != ALL_TEXT || $emp_dept != ALL_TEXT || $emp_grade != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
		$sql .= " AND ".TB_PREF."employee.emp_dept = ".db_escape($emp_dept);
		$sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
		$sql .= " AND ".TB_PREF."employee.emp_desig = ".db_escape($emp_desig);
	}
*/
	
	if ($employee_id != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
	}
	if ($emp_dept != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.emp_dept = ".db_escape($emp_dept);
	}
	if ($emp_grade != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
	}
	if ($emp_desig != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.emp_desig = ".db_escape($emp_desig);
	}

	
/*	if ($emp_grade != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
	}

	if ($employee_id != ALL_TEXT) 
	{
   		$sql .= " AND ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
	}
*/
   	return $sql;
}


$sql = get_sql_for_employee_inquiry($_POST['employee_id'],$_POST['emp_dept'],$_POST['emp_grade'],$_POST['emp_desig'],date2sql($_POST['datefrom']),date2sql($_POST['dateto']));
 $cols = array(
			_("Employee Name"),
			_("Grade"),
			_("Designation"),
			_("Department"),
			_("Date")=> array('name'=>'date', 'type'=>'date', 'ord'=>'desc'),
			_("Remarks"), 
			_("Present")
			
			//_("present"),
    );
	array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));

if ($_POST['employee_id'] != ALL_TEXT)
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
