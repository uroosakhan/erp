<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

//include_once($path_to_root . "/payroll/includes/purchasing_ui.inc");
include_once($path_to_root . "/payroll/includes/db/dayattendance_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
if (!@$_GET['popup'])
{
    $js = "";
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if (user_use_date_picker())
        $js .= get_js_date_picker();
    page(_($help_context = "Payroll Inquiry"), isset($_GET['employee_id']), false, "", $js);
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
if (isset($_GET['trans_no'])){
    $_POST['trans_no'] = $_GET['trans_no'];
}
if (isset($_GET['month'])){
    $_POST['month'] = $_GET['month'];
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
    employee_list2_cells(_("Select a Employee:"), 'employee_id', null,true,true, false,  false);
month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));
fiscalyearss_list_cells(_("Fiscal Year:"), 'f_year', null,true);
$f_year = get_current_fiscalyear();
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();


end_row();
end_table();
set_global_supplier($_POST['employee_id']);

//------------------------------------------------------------------------------------------------


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

        $modify = 'trans_no';
    return pager_link( _("Edit"),
        "/payroll/payroll.php?trans_no=".$row['trans_no'].'&month='.$row['month'], ICON_EDIT);
}

function get_advance_name_neww($emp_id)
{
    $sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($emp_id);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}

///---
function get_0_emp_attendance($month)
{
  
    $f_year = get_current_fiscalyear();

    $year1 = date('Y', strtotime($f_year['begin']));
    $year2 = date('Y', strtotime($f_year['end']));
   // $yr = date('Y', strtotime('2017-01-01'));

    $f_year =$_POST['f_year'];
    $employee_id=$_POST['employee_id'];
    $month = $_POST['month'];

    $sql = "SELECT  `empl_id`,`check_in`,`check_out`
FROM 0_emp_attendance AS t1

 ";
    
    $sql.=" group by  t1.empl_id ";
    return db_query($sql, "Error");
}



///---



function get_employee_date_new()
{
    $f_year=$_POST['f_year'];
    $month=$_POST['month'];
    $sql = "SELECT ".TB_PREF."emp_attendance.att_date FROM ".TB_PREF."emp_attendance 
	
	WHERE 
	fiscal_year=".db_escape($f_year)
        ." AND month_id=".db_escape($month);

    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}

start_table(TABLESTYLE, "width='95%'");


$date ='2017-'.$_POST['month'].'-01';

$end = '2017-'.$_POST['month'].'-' . date('t', strtotime($date)); //get end date of month
$array = array();
while(strtotime($date) <= strtotime($end)) {
    $day_num = date('d', strtotime($date));
    $day_name = date('l', strtotime($date));
    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
    $array[] = $day_name . ' ' . $day_num;
}

$th = array(
    _("Employee ID"),
    _("Employee Name"),
    _("Cheque In"),
    _("Cheque Out"),

);


function get_employee_namee_new($employee_id)
{

    $sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}


table_header($th);
{
$k = 1;  //row colour counter
$result = get_0_emp_attendance($day_num);
$d = date("d",strtotime($end));


while($myrow = db_fetch($result))
{
    
    alt_table_row_color($k);
    label_cell($myrow['empl_id']);
    label_cell(get_employee_namee_new($myrow['empl_id']));
    label_cell($myrow['check_in']);
    label_cell($myrow['check_out']);
    

    end_row();
}

        }


if (!@$_GET['popup'])
{
    end_form();
    end_page(@$_GET['popup'], false, false);
}
?>