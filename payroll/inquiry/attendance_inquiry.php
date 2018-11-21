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
    $month = $_POST['month'];
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
    $year1 = date('Y', strtotime($f_year['end']));
    else
    $year1 = date('Y', strtotime($f_year['begin']));
    $yr = date('Y-m-d',mktime(0,0,0,1,1,$year1));
    $ds1 = date('Y-m-d',mktime(0,0,0,$month,1,$yr));
    $ds2 = date('Y-m-d',mktime(0,0,0,$month,2,$yr));
    $ds3 = date('Y-m-d',mktime(0,0,0,$month,3,$yr));
    $ds4 = date('Y-m-d',mktime(0,0,0,$month,4,$yr));
    $ds5 = date('Y-m-d',mktime(0,0,0,$month,5,$yr));
    $ds6 = date('Y-m-d',mktime(0,0,0,$month,6,$yr));
    $ds7 = date('Y-m-d',mktime(0,0,0,$month,7,$yr));
    $ds8 = date('Y-m-d',mktime(0,0,0,$month,8,$yr));
    $ds9 = date('Y-m-d',mktime(0,0,0,$month,9,$yr));
    $ds10 = date('Y-m-d',mktime(0,0,0,$month,10,$yr));
    $ds11 = date('Y-m-d',mktime(0,0,0,$month,11,$yr));
    $ds12 = date('Y-m-d',mktime(0,0,0,$month,12,$yr));
    $ds13 = date('Y-m-d',mktime(0,0,0,$month,13,$yr));
    $ds14 = date('Y-m-d',mktime(0,0,0,$month,14,$yr));
    $ds15 = date('Y-m-d',mktime(0,0,0,$month,15,$yr));
    $ds16 = date('Y-m-d',mktime(0,0,0,$month,16,$yr));
    $ds17 = date('Y-m-d',mktime(0,0,0,$month,17,$yr));
    $ds18 = date('Y-m-d',mktime(0,0,0,$month,18,$yr));
    $ds19 = date('Y-m-d',mktime(0,0,0,$month,19,$yr));
    $ds20 = date('Y-m-d',mktime(0,0,0,$month,20,$yr));
    $ds21 = date('Y-m-d',mktime(0,0,0,$month,21,$yr));
    $ds22 = date('Y-m-d',mktime(0,0,0,$month,22,$yr));
    $ds23 = date('Y-m-d',mktime(0,0,0,$month,23,$yr));
    $ds24 = date('Y-m-d',mktime(0,0,0,$month,24,$yr));
    $ds25 = date('Y-m-d',mktime(0,0,0,$month,25,$yr));
    $ds26 = date('Y-m-d',mktime(0,0,0,$month,26,$yr));
    $ds27 = date('Y-m-d',mktime(0,0,0,$month,27,$yr));
    $ds28 = date('Y-m-d',mktime(0,0,0,$month,28,$yr));
    $ds29 = date('Y-m-d',mktime(0,0,0,$month,29,$yr));
    $ds30 = date('Y-m-d',mktime(0,0,0,$month,30,$yr));
    $ds31 = date('Y-m-d',mktime(0,0,0,$month,31,$yr));
    $sql = "SELECT  `empl_id`,month_id,fiscal_year,COUNT(*) AS tday, 
    (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds1' AND empl_id = t1.empl_id group by  t1.empl_id) AS D1,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds2' AND empl_id = t1.empl_id group by  t1.empl_id) AS D2,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds3' AND empl_id = t1.empl_id group by  t1.empl_id) AS D3,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds4' AND empl_id = t1.empl_id group by  t1.empl_id) AS D4,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds5' AND empl_id = t1.empl_id group by  t1.empl_id) AS D5,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds6' AND empl_id = t1.empl_id group by  t1.empl_id) AS D6,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds7' AND empl_id = t1.empl_id group by  t1.empl_id) AS D7,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds8' AND empl_id = t1.empl_id group by  t1.empl_id) AS D8,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds9' AND empl_id = t1.empl_id group by  t1.empl_id) AS D9,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds10' AND empl_id = t1.empl_id group by  t1.empl_id) AS D10,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds11' AND empl_id = t1.empl_id group by  t1.empl_id) AS D11,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds12' AND empl_id = t1.empl_id  group by  t1.empl_id) AS D12,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds13' AND empl_id = t1.empl_id group by  t1.empl_id) AS D13,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds14' AND empl_id = t1.empl_id group by  t1.empl_id) AS D14,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds15' AND empl_id = t1.empl_id group by  t1.empl_id) AS D15,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds16' AND empl_id = t1.empl_id group by  t1.empl_id) AS D16,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds17' AND empl_id = t1.empl_id group by  t1.empl_id) AS D17,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds18' AND empl_id = t1.empl_id group by  t1.empl_id) AS D18,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds19' AND empl_id = t1.empl_id group by  t1.empl_id) AS D19,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds20' AND empl_id = t1.empl_id group by  t1.empl_id) AS D20,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds21' AND empl_id = t1.empl_id group by  t1.empl_id) AS D21,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds22' AND empl_id = t1.empl_id group by  t1.empl_id) AS D22,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds23' AND empl_id = t1.empl_id group by  t1.empl_id) AS D23,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds24' AND empl_id = t1.empl_id group by  t1.empl_id) AS D24,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds25' AND empl_id = t1.empl_id group by  t1.empl_id) AS D25,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds26' AND empl_id = t1.empl_id group by  t1.empl_id) AS D26,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds27' AND empl_id = t1.empl_id group by  t1.empl_id) AS D27,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds28' AND empl_id = t1.empl_id group by  t1.empl_id) AS D28,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds29' AND empl_id = t1.empl_id group by  t1.empl_id) AS D29,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds30' AND empl_id = t1.empl_id  group by  t1.empl_id) AS D30,
   (SELECT TIMEDIFF(`check_out`,`check_in`) FROM 0_emp_attendance WHERE `att_date` = '$ds31' AND empl_id = t1.empl_id group by  t1.empl_id) AS D31
   
  FROM 0_emp_attendance AS t1 WHERE t1.month_id = '$month'   ";
    
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

$f_year = get_current_fiscalyear();
$month=$_POST['month'];
if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
    $year1 = date('Y', strtotime($f_year['end']));
else
    $year1 = date('Y', strtotime($f_year['begin']));

$date =$year1.'-'.$_POST['month'].'-01';

$end = $year1.'-'.$_POST['month'].'-' . date('t', strtotime($date)); //get end date of month
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
    $array[0],
    $array[1],
    $array[2],
    $array[3],
    $array[4],
    $array[5],
    $array[6],
    $array[7],
    $array[8],
    $array[9],
    $array[10],
    $array[11],
    $array[12],
    $array[13],
    $array[14],
    $array[15],
    $array[16],
    $array[17],
    $array[18],
    $array[19],
    $array[20],
    $array[21],
    $array[22],
    $array[23],
    $array[24],
    $array[25],
    $array[26],
    $array[27],
    $array[28],
    $array[29],
    $array[30],
    _("Working Days"),
    _("Absent Days"),
    _("Leave Days"),
    _("Gazzet Days"),
    _("O.T Hours"),
    _("Deduction Days"),
    _("Half Day Deductions"),

);


function get_employee_namee_new($employee_id)
{
    $sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_absent_days($employee_id,$month,$f_year)
{
    $sql = "SELECT COUNT(*) FROM `0_emp_attendance`
 WHERE `check_in`=0 AND `check_out`=0 
 AND `empl_id`=$employee_id AND `month_id`=$month AND `fiscal_year`=$f_year ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
/*
function get_employee_ot_hours($month,$emp_id,$fiscal_year)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $duty_hour=get_employee_duty_hour($emp_id);
    $total_absent=get_employee_absent_days($emp_id,$month,$fiscal_year);
    $sql1="SELECT SUM(TIMEDIFF(`check_out`,`check_in`) ) AS D1,COUNT(*) AS tday FROM 0_emp_attendance 
WHERE `att_date` >= '$from'  AND `att_date` <= '$to'  
AND empl_id = $emp_id AND `fiscal_year`=$fiscal_year
AND check_out>'17:00'";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $t_hours= $myrow1['D1'];
   /* $t_days=$myrow1['tday']-$total_absent;
    $t_w_hours=$t_days*$duty_hour;
    $t_ot_val=$t_w_hours-$t_hours;*/

   /* $converted_time = date('H:i', mktime(0,$t_hours));
    return $converted_time;
}*/
function get_employee_set_ot_hour($employee_id)
{
    $sql = "SELECT ".TB_PREF."employee.ot_hours FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}

function get_employee_ot_hours($month,$emp_id,$fiscal_year)
{
    $ot_limit=get_employee_set_ot_hour($emp_id);
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $duty_hour=get_employee_duty_hour($emp_id);
   // $total_absent=get_employee_absent_days($emp_id,$month,$fiscal_year);
    $sql1="SELECT SUM(HOUR(TIMEDIFF(check_out,'$duty_hour'))) AS thour,(SUM(MINUTE(TIMEDIFF(check_out,'$duty_hour')))) AS tmin  FROM 0_emp_attendance 
WHERE `att_date` >= '$from'  AND `att_date` <= '$to'  
AND empl_id = $emp_id AND `fiscal_year`=$fiscal_year
AND check_out>'$duty_hour'  AND check_out!='00:00:00'";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    
    
    //$t_days=$myrow1['tday'];
    $t_hours= $myrow1['thour'].":".$myrow1['tmin'];
   // $t_w_hours=$t_days*$duty_hour;
   // $t_ot_val=$t_hours-$t_w_hours;

    $converted_time = date('H:i:s', mktime(0,$myrow1['D1']));
  //  display_error($t_hours."---".($converted_time)."total days".$t_w_hours."");
    return $t_hours;
}
function get_employee_deduction_days($month,$emp_id)
{
    $sql1="SELECT * 
FROM  `0_attendance_policy` 
INNER JOIN 0_employee ON 0_attendance_policy.`grade` = 0_employee.emp_grade
GROUP BY 0_attendance_policy.`grade` ";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $ded_start=$myrow1['deduction_start_time'];
    $ded_end=$myrow1['deduction_end_time'];
    $ded_val=$myrow1['deduction_value'];
    $ded_h_val=$myrow1['deduction_value_days'];

    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT
			(SUM(CASE WHEN `check_in` >= '$ded_start' AND `check_in` < '$ded_end' THEN $ded_val ELSE 0 END) )  as ded1			FROM 0_emp_attendance
			WHERE `att_date` between '$from' AND '$to'
			AND `empl_id`=$emp_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return floor($myrow['0']/$ded_h_val);
}
function get_employee_ded_h_days($month,$emp_id)
{
    $sql1="SELECT * 
FROM  `0_attendance_policy` 
INNER JOIN 0_employee ON 0_attendance_policy.`grade` = 0_employee.emp_grade
ORDER BY 0_attendance_policy.`id` DESC ";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $ded_start=$myrow1['deduction_start_time'];
    $ded_end=$myrow1['deduction_end_time'];
    $ded_val=$myrow1['deduction_value'];
    $ded_h_val=$myrow1['deduction_value_days'];

    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = $year1."-".$month."-01";
    $to = $year1."-".$month."-31";
    $sql = "SELECT
			(SUM(CASE WHEN `check_in` >= '$ded_start' AND `check_in` < '$ded_end' THEN $ded_val ELSE 0 END) )  as ded1			FROM 0_emp_attendance
			WHERE `att_date` between '$from' AND '$to'
			AND `empl_id`=$emp_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return ($myrow['0']/$ded_h_val);
}
function get_employee_duty_hour($employee_id)
{
    $sql = "SELECT ".TB_PREF."employee.ot_hours FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}

function get_gazzet_holidays_days($month)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT COUNT( * ) 
FROM  `0_gazetted_holidays` 
WHERE DATE
BETWEEN  '$from'
AND  '$to' ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_emp_t_leave_days($emp_id,$month,$year)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT COUNT( * ) 
FROM  `0_leave` 
WHERE  `emp_id` =$emp_id
AND  `from_date` >=  '$from'
AND  `to_date` <=  '$to' 
AND f_year=$year";
/*
$sql = "SELECT SUM(no_of_leave) 
FROM  `0_leave` 
WHERE  `emp_id` =$emp_id
AND f_year=$year";*/
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
///
function get_emp_el_leave_days($emp_id,$month,$year)
{
$sql = "SELECT SUM(no_of_leave) 
FROM  `0_leave` 
WHERE  `emp_id` =$emp_id
AND f_year=$year
AND leave_type=2";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_off_days($employee_id,$month,$f_year)
{
    $sql = "SELECT COUNT(*) FROM `0_emp_attendance`
 WHERE  DAYNAME(`att_date`) IN ('Sunday')
 AND `empl_id`=$employee_id AND `month_id`=$month AND `fiscal_year`=$f_year ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_month_t_days($month)
{
    $sql = "SELECT `days` FROM `0_month` WHERE `id`=$month ";
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
    

    $total_absent=get_employee_absent_days($myrow['empl_id'],$myrow['month_id'],$myrow['fiscal_year']);
    $total_off_days=get_employee_off_days($myrow['empl_id'],$myrow['month_id'],$myrow['fiscal_year']);
    $total_gazzet=get_gazzet_holidays_days($myrow['month_id']);
    $total_emp_leave_days=get_emp_t_leave_days($myrow['empl_id'],$myrow['month_id'],$myrow['fiscal_year']);
    $earn_and_late_leave=get_emp_el_leave_days($myrow['empl_id'],$myrow['month_id'],$myrow['fiscal_year']);

    $t_ded=get_employee_deduction_days($myrow['month_id'],$myrow['empl_id']);
    $t_h_d_ded=get_employee_ded_h_days($myrow['month_id'],$myrow['empl_id']);
    $ot_hours=get_employee_ot_hours($myrow['month_id'],$myrow['empl_id'],$myrow['fiscal_year']);

    $t_m_days=get_month_t_days($myrow['month_id']);
   
    if($myrow['D23']!=0)
        $d23 =$myrow['D23']-$duty_hour;
     if($myrow['D22']!=0)
         $d22 =$myrow['D22']-$duty_hour;
      if($myrow['D21']!=0)
          $d21 =$myrow['D21']-$duty_hour;
      if($myrow['D20']!=0)
          $d20 =$myrow['D20']-$duty_hour;
       if($myrow['D19']!=0)
           $d19 =$myrow['D19']-$duty_hour;
       if($myrow['D18']!=0)
           $d18 =$myrow['D18']-$duty_hour;
       if($myrow['D17']!=0)
           $d17 =$myrow['D17']-$duty_hour;
       if($myrow['D16']!=0)
           $d16 =$myrow['D16']-$duty_hour;
       if($myrow['D15']!=0)
           $d15 =$myrow['D15']-$duty_hour;
       if($myrow['D14']!=0)
           $d14 =$myrow['D14']-$duty_hour;
       if($myrow['D13']!=0)
           $d13 =$myrow['D13']-$duty_hour;
       if($myrow['D12']!=0)
           $d12 =$myrow['D12']-$duty_hour;
        if($myrow['D11']!=0)
            $d11 =$myrow['D11']-$duty_hour;
        if($myrow['D10']!=0)
            $d10 =$myrow['D10']-$duty_hour;
        if($myrow['D9']!=0)
            $d9 =$myrow['D9']-$duty_hour;
        if($myrow['D8']!=0)
            $d8 =$myrow['D8']-$duty_hour;
        if($myrow['D7']!=0)
            $d7 =$myrow['D7']-$duty_hour;
        if($myrow['D6']!=0)
            $d6 =$myrow['D6']-$duty_hour;
        if($myrow['D5']!=0)
            $d5 =$myrow['D5']-$duty_hour;
        if($myrow['D4']!=0)
            $d4 =$myrow['D4']-$duty_hour;
        if($myrow['D3']!=0)
            $d3 =$myrow['D3']-$duty_hour;
        if($myrow['D2']!=0)
            $d2 =$myrow['D2']-$duty_hour;
        if($myrow['D1']!=0)
            $d1 =$myrow['D1']-$duty_hour;
        if($myrow['D24']!=0)
            $d24 =$myrow['D24']-$duty_hour;
        if($myrow['D25']!=0)
            $d25 =$myrow['D25']-$duty_hour;
        if($myrow['D26']!=0)
            $d26 =$myrow['D26']-$duty_hour;
        if($myrow['D27']!=0)
            $d27 =$myrow['D27']-$duty_hour;
        if($myrow['D28']!=0)
            $d28 =$myrow['D28']-$duty_hour;
        if($myrow['D29']!=0)
            $d29 =$myrow['D29']-$duty_hour;
        if($myrow['D30']!=0)
            $d30 =$myrow['D30']-$duty_hour;
        if($myrow['D31']!=0)
            $d31 =$myrow['D31']-$duty_hour;
//        $ot_hours=$d1+$d2+$d3+$d4+$d5+$d6+$d7+$d8+$d9+$d10+$d11+$d12+$d13+$d14+$d15+$d16+$d17+$d18+$d19+$d20
//            +$d21+$d22+$d23+$d24+$d25+$d26+$d27+$d28+$d29+$d30+$d31;
                                                           // $WD=$myrow['D1']+$myrow['D2']+$myrow['D3']+$myrow['D4']+$myrow['D5']+$myrow['D6'];
    alt_table_row_color($k);
    label_cell($myrow['empl_id']);
    label_cell(get_employee_namee_new($myrow['empl_id']));

    label_cell($myrow['D1']);
    label_cell($myrow['D2']);
    label_cell($myrow['D3']);
    label_cell($myrow['D4']);
    label_cell($myrow['D5']);
    label_cell($myrow['D6']);
    label_cell($myrow['D7']);
    label_cell($myrow['D8']);
    label_cell($myrow['D9']);
    label_cell($myrow['D10']);
    label_cell($myrow['D11']);
    label_cell($myrow['D12']);
    label_cell($myrow['D13']);
    label_cell($myrow['D14']);
    label_cell($myrow['D15']);
    label_cell($myrow['D16']);
    label_cell($myrow['D17']);
    label_cell($myrow['D18']);
    label_cell($myrow['D19']);
    label_cell($myrow['D20']);
    label_cell($myrow['D21']);
    label_cell($myrow['D22']);
    label_cell($myrow['D23']);
    label_cell($myrow['D24']);
    label_cell($myrow['D25']);
    label_cell($myrow['D26']);
    label_cell($myrow['D27']);
    label_cell($myrow['D28']);
    label_cell($myrow['D29']);
    label_cell($myrow['D30']);
    label_cell($myrow['D31']);
    label_cell($t_m_days-($total_off_days+$total_gazzet+$total_emp_leave_days));
    label_cell($total_absent-($total_off_days+$total_gazzet+$total_emp_leave_days));
    label_cell($total_emp_leave_days);
    label_cell($total_gazzet);
    label_cell($ot_hours);
    
    label_cell($t_dedte); // $t_ded-$earn_and_late_leave   if company policy that leave deduct hn late py na ky salary
    label_cell($t_h_d_ded);

// display_error($t_m_days."-".$total_off_days."-".$total_gazzet."-".$total_emp_leave_days);

    end_row();
}

        }


if (!@$_GET['popup'])
{
    end_form();
    end_page(@$_GET['popup'], false, false);
}
?>