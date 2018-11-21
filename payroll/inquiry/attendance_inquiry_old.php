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
//emp_dept_cells(_("Department:"), 'emp_dept', null,true,true, false,  false);
//emp_grade_cells(_("Grade"),'grade', null,true,true, false,  false);
//month_list_cells( _("Select Month:"), 'month', null,true,true, false,  false);

//dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
//pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
//loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));
fiscalyearss_list_cells(_("Fiscal Year:"), 'f_year', null,true);
//display_error( $_POST['f_year']);
//$f_year = get_current_fiscalyear();

//    fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
//	date_cells(_("Date:"), 'date' , '');
//hidden('f_year', $f_year['id']);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();



//date_cells(_("From:"), 'TransAfterDate', '', null, -30);
//date_cells(_("To:"), 'TransToDate');

//supp_transactions_list_cell("filterType", null, true);


//emp_desg_cells(_("Designation:"), 'emp_desig', null,true,true, false,  false);
//date_cells(_("From:"), 'datefrom', '', null, -30);
//date_cells(_("To:"), 'dateto');

//submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

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

        $modify = 'trans_no';
    return pager_link( _("Edit"),
        "/payroll/payroll.php?trans_no=".$row['trans_no'].'&month='.$row['month'], ICON_EDIT);
}
//
////------------------------------------------------------------------------------------------------
//function get_sql_for_employee_inquiry($employee_id, $emp_dept, $emp_grade,$emp_desig,$month, $datefrom,$dateto)
//{
//
//    $date = '2017-11-01';
//    $end = '2017-11-' . date('t', strtotime($date)); //get end date of month
//    ?>
<!--    <table>-->
<!---->
<!--        --><?php
//        $array = array();
//        while(strtotime($date) <= strtotime($end)) {
//            $day_num = date('d', strtotime($date));
//            $day_name = date('l', strtotime($date));
//
//            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
////            echo "<td>$day_name <br/>$day_num </td>";
//            $array[] = $day_name.' '.$day_num;
//    display_error($day_num);
//
//
//
//    $f_year =$_POST['f_year'];
//    $employee_id=$_POST['employee_id'];
//
//    $month = $_POST['month'];
//
//    $sql = "SELECT ".TB_PREF."employee.emp_name,".TB_PREF."emp_attendance.empl_id ,".TB_PREF."emp_attendance.month_id,
//    ".TB_PREF."emp_attendance.fiscal_year , concat(".TB_PREF."emp_attendance.".$day_num."_type,'  ',".TB_PREF."emp_attendance.".$day_num."_in,'  ',".TB_PREF."emp_attendance.".$day_num."_out) FROM ".TB_PREF."emp_attendance
//
//INNER JOIN ".TB_PREF."employee ON ".TB_PREF."emp_attendance.`empl_id` =".TB_PREF."employee.`employee_id`
//
// WHERE ".TB_PREF."emp_attendance.empl_id  != 0
//
//"/*  . db_escape("%" . $day_num. "%")*/;
//
//    if ($employee_id != '')
//    {
//        $sql .= " AND ".TB_PREF."emp_attendance.empl_id = ".db_escape($employee_id);
//    }
//
//    if ($month != '')
//    {
//        $sql .= " AND ".TB_PREF."emp_attendance.month_id = ".db_escape($month);
//    }
//    if ($f_year != '')
//    {
//        $sql .= " AND ".TB_PREF."emp_attendance.fiscal_year = ".db_escape($f_year);
//    }
//    /*	if ($emp_grade != ALL_TEXT)
//        {
//               $sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
//        }
//
//        if ($employee_id != ALL_TEXT)
//        {
//               $sql .= " AND ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
//        }
//    */
//            return $sql;
//        }
//
//}
//
//$date = '2017-11-01';
//$end = '2017-11-' . date('t', strtotime($date)); //get end date of month
//
//
//$array = array();
//while(strtotime($date) <= strtotime($end)) {
//    $day_num = date('d', strtotime($date));
//    $day_name = date('l', strtotime($date));
//
//    $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
////    echo "<td>$day_name <br/>$day_num </td>";
//    $array[] = $day_name . ' ' . $day_num;
//}
////}
//
//    $sql = get_sql_for_employee_inquiry();
////
////$cols = array(
////    _("Employee Name"),
////    _("Employee ID"),
////    $show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
////    $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
////    $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30,
////    $show31
////
////    //_("present"),
////);
////foreach ($array as $array2 => $array3)
////{
//////    $hello = explode($array3, ',');
//////    $hello2 = $hello[$array2];
//////    $hi .= $array3.',';
////}
//$cols = array(
//    _("Employee Name"),
//    _("Employee ID"),
//    $array[0],
//    $array[1],
//    $array[2],
//    $array[3],
//    $array[4],
//    $array[5],
//    $array[6],
//    $array[7],
//    $array[8],
//    $array[9],
//    $array[10],
//    $array[11],
//    $array[12],
//    $array[13],
//    $array[14],
//    $array[15],
//    $array[16],
//    $array[17],
//    $array[18],
//    $array[19],
//    $array[20],
//    $array[21],
//    $array[22],
//    $array[23],
//    $array[24],
//    $array[25],
//    $array[26],
//    $array[27],
//    $array[28],
//    $array[29]
//
//
//);
////$hello = explode($array, ',');
////var_dump($array);
////var_dump($hello[1]);
////$cols = array(
//////    "Employee Name", "Employee ID",  "Check In","","","","",""
////    $hello2
////);
////
////$headers = array(   _('Activity'),
////    $show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
////    $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
////    $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30,
////    $show31, "Total"
////
////);
////
////$aligns = array( 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
////    'left', 'left', 'left', 'left');
//
//array_append($cols, array(
//    array(
////          'insert'=>true, 'fun'=>'functio'),
//        'insert'=>true, 'fun'=>'edit_link')));
//
//if ($_POST['employee_id'] != ALL_TEXT)
//{
//    $cols[_("Supplier")] = 'skip';
//    $cols[_("Currency")] = 'skip';
//}
//
//
////------------------------------------------------------------------------------------------------
//
///*show a table of the transactions returned by the sql */
//$table =& new_db_pager('trans_tbl', $sql, $cols);
//$table->set_marker('check_overdue', _(""));
//
//$table->width = "85%";
//
//
//
//display_db_pager($table);

//function counts()
//{
//    $sql="select distinct EMPLOYEE_NAME, count(Day1) as [DAYSPASTDUE > 60 < 90],
// count(Day2) as [DAYSPASTDUE > 60 < 90],
// count(Day3) from
// db_ITG.dbo.training_report where Day1 <> 0 or Day2 <> 0 or Day3 <> 0
// group by EMployee_Name,Day1, Day2,Day3";
//
//
//    return db_query($sql, "Error");
//
//}
//
//function getsum($emp_id)
//{
//    $sql = "select ".TB_PREF."emp_attendance.01_type ,".TB_PREF."emp_attendance.02_type ,".TB_PREF."emp_attendance.03_type ,
//    ".TB_PREF."emp_attendance.04_type ,".TB_PREF."emp_attendance.05_type ,".TB_PREF."emp_attendance.06_type ,
//    ".TB_PREF."emp_attendance.07_type ,".TB_PREF."emp_attendance.08_type ,".TB_PREF."emp_attendance.09_type ,".TB_PREF."emp_attendance.10_type
//    ,".TB_PREF."emp_attendance.11_type ,".TB_PREF."emp_attendance.12_type ,".TB_PREF."emp_attendance.13_type ,".TB_PREF."emp_attendance.14_type
//     ,".TB_PREF."emp_attendance.15_type ,".TB_PREF."emp_attendance.16_type ,".TB_PREF."emp_attendance.17_type ,".TB_PREF."emp_attendance.18_type ,
//     ".TB_PREF."emp_attendance.19_type ,".TB_PREF."emp_attendance.20_type ,".TB_PREF."emp_attendance.21_type ,".TB_PREF."emp_attendance.22_type
//     ,".TB_PREF."emp_attendance.23_type ,".TB_PREF."emp_attendance.24_type ,".TB_PREF."emp_attendance.25_type ,".TB_PREF."emp_attendance.26_type
//     ,".TB_PREF."emp_attendance.27_type ,".TB_PREF."emp_attendance.28_type ,".TB_PREF."emp_attendance.29_type ,".TB_PREF."emp_attendance.30_type ,
//    ".TB_PREF."emp_attendance.31_type  COUNT(*) as count FROM ".TB_PREF."emp_attendance WHERE
//    empl_id = $emp_id
//     AND ".TB_PREF."emp_attendance.01_type != 1
//	";
//
//    $result = db_query($sql, "Could not get employees.");
//    $myrow = db_fetch($result);
//    return $myrow;
//}
function getsum($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.01_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.01_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum1($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.02_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.02_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum3($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.03_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.03_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum4($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.04_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.04_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum5($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.05_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.05_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum6($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.06_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.06_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum7($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.07_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.07_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum8($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.08_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.08_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum9($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.09_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.09_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum10($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.10_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.10_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum11($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.11_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.11_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum12($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.12_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.12_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum13($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.13_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.13_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum14($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.14_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.14_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum15($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.15_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.15_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum16($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.16_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.16_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum17($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.17_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.17_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum18($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.18_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.18_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum19($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.19_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.19_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum20($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.20_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.20_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function getsum21($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.21_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.21_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}


function getsum22($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.22_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.22_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}


function getsum23($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.23_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.23_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum24($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.24_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.24_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum25($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.25_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.25_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum26($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.26_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.26_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum27($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.27_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.27_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum28($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.28_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.28_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum29($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.29_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.29_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum30($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.30_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.30_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

function getsum31($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.31_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.31_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

//function getleave($empl_id)
//{
//
//    $sql ="SELECT ".TB_PREF."leave_type.description FROM ".TB_PREF."leave_type
//
//        WHERE
//         ".TB_PREF."emp_attendance.31_type in (3,4,5,2)
//         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";
//
//    $result = db_query($sql, "Could not get employees.");
//    $myrow = db_fetch_row($result);
//    return $myrow[0];
//}


function get_advance_name_neww($emp_id)
{
    $sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($emp_id);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}

///---
function get_0_emp_attendance($day_num=01)
{

    $f_year =$_POST['f_year'];
    $employee_id=$_POST['employee_id'];

    $month = $_POST['month'];

    $sql = "SELECT ".TB_PREF."employee.emp_name,".TB_PREF."emp_attendance.empl_id ,".TB_PREF."emp_attendance.month_id,
    ".TB_PREF."emp_attendance.fiscal_year , 
   
    
    concat(".TB_PREF."emp_attendance.01_type,'-',".TB_PREF."emp_attendance.01_in,'  ',".TB_PREF."emp_attendance.01_out) as Record01,
    concat(".TB_PREF."emp_attendance.02_type,'-',".TB_PREF."emp_attendance.02_in,'  ',".TB_PREF."emp_attendance.02_out) as Record02,
    concat(".TB_PREF."emp_attendance.03_type,'-',".TB_PREF."emp_attendance.03_in,'  ',".TB_PREF."emp_attendance.03_out) as Record03,
    concat(".TB_PREF."emp_attendance.04_type,'-',".TB_PREF."emp_attendance.04_in,'  ',".TB_PREF."emp_attendance.04_out) as Record04,
    concat(".TB_PREF."emp_attendance.05_type,'-',".TB_PREF."emp_attendance.05_in,'  ',".TB_PREF."emp_attendance.05_out) as Record05,
    concat(".TB_PREF."emp_attendance.06_type,'-',".TB_PREF."emp_attendance.06_in,'  ',".TB_PREF."emp_attendance.06_out) as Record06,
    concat(".TB_PREF."emp_attendance.07_type,'-',".TB_PREF."emp_attendance.07_in,'  ',".TB_PREF."emp_attendance.07_out) as Record07,
    concat(".TB_PREF."emp_attendance.08_type,'-',".TB_PREF."emp_attendance.08_in,'  ',".TB_PREF."emp_attendance.08_out) as Record08,
    concat(".TB_PREF."emp_attendance.09_type,'-',".TB_PREF."emp_attendance.09_in,'  ',".TB_PREF."emp_attendance.09_out) as Record09,
    concat(".TB_PREF."emp_attendance.10_type,'-',".TB_PREF."emp_attendance.10_in,'  ',".TB_PREF."emp_attendance.10_out) as Record10,
    concat(".TB_PREF."emp_attendance.11_type,'-',".TB_PREF."emp_attendance.11_in,'  ',".TB_PREF."emp_attendance.11_out) as Record11,
    concat(".TB_PREF."emp_attendance.12_type,'-',".TB_PREF."emp_attendance.12_in,'  ',".TB_PREF."emp_attendance.12_out) as Record12,
    concat(".TB_PREF."emp_attendance.13_type,'-',".TB_PREF."emp_attendance.13_in,'  ',".TB_PREF."emp_attendance.13_out) as Record13,
    concat(".TB_PREF."emp_attendance.14_type,'-',".TB_PREF."emp_attendance.14_in,'  ',".TB_PREF."emp_attendance.14_out) as Record14,
    concat(".TB_PREF."emp_attendance.15_type,'-',".TB_PREF."emp_attendance.15_in,'  ',".TB_PREF."emp_attendance.15_out) as Record15,
    concat(".TB_PREF."emp_attendance.16_type,'-',".TB_PREF."emp_attendance.16_in,'  ',".TB_PREF."emp_attendance.16_out) as Record16,
    concat(".TB_PREF."emp_attendance.17_type,'-',".TB_PREF."emp_attendance.17_in,'  ',".TB_PREF."emp_attendance.17_out) as Record17,
    concat(".TB_PREF."emp_attendance.18_type,'-',".TB_PREF."emp_attendance.18_in,'  ',".TB_PREF."emp_attendance.18_out) as Record18,
    concat(".TB_PREF."emp_attendance.19_type,'-',".TB_PREF."emp_attendance.19_in,'  ',".TB_PREF."emp_attendance.19_out) as Record19,
    concat(".TB_PREF."emp_attendance.20_type,'-',".TB_PREF."emp_attendance.20_in,'  ',".TB_PREF."emp_attendance.20_out) as Record20,
    concat(".TB_PREF."emp_attendance.21_type,'-',".TB_PREF."emp_attendance.21_in,'  ',".TB_PREF."emp_attendance.21_out) as Record21,
    concat(".TB_PREF."emp_attendance.22_type,'-',".TB_PREF."emp_attendance.22_in,'  ',".TB_PREF."emp_attendance.22_out) as Record22,
    concat(".TB_PREF."emp_attendance.23_type,'-',".TB_PREF."emp_attendance.23_in,'  ',".TB_PREF."emp_attendance.23_out) as Record23,
    concat(".TB_PREF."emp_attendance.24_type,'-',".TB_PREF."emp_attendance.24_in,'  ',".TB_PREF."emp_attendance.24_out) as Record24,
    concat(".TB_PREF."emp_attendance.25_type,'-',".TB_PREF."emp_attendance.25_in,'  ',".TB_PREF."emp_attendance.25_out) as Record25,
    concat(".TB_PREF."emp_attendance.26_type,'-',".TB_PREF."emp_attendance.26_in,'  ',".TB_PREF."emp_attendance.26_out) as Record26,
    concat(".TB_PREF."emp_attendance.27_type,'-',".TB_PREF."emp_attendance.27_in,'  ',".TB_PREF."emp_attendance.27_out) as Record27,
    concat(".TB_PREF."emp_attendance.28_type,'-',".TB_PREF."emp_attendance.28_in,'  ',".TB_PREF."emp_attendance.28_out) as Record28,
    concat(".TB_PREF."emp_attendance.29_type,'-',".TB_PREF."emp_attendance.29_in,'  ',".TB_PREF."emp_attendance.29_out) as Record29,
    concat(".TB_PREF."emp_attendance.30_type,'-',".TB_PREF."emp_attendance.30_in,'  ',".TB_PREF."emp_attendance.30_out) as Record30,
    concat(".TB_PREF."emp_attendance.31_type,'-',".TB_PREF."emp_attendance.31_in,'  ',".TB_PREF."emp_attendance.31_out) as Record31
    
    
    FROM ".TB_PREF."emp_attendance

INNER JOIN ".TB_PREF."employee ON ".TB_PREF."emp_attendance.`empl_id` =".TB_PREF."employee.`employee_id`


 WHERE ".TB_PREF."emp_attendance.empl_id  != 0";



    if ($employee_id != '')
    {
        $sql .= " AND ".TB_PREF."emp_attendance.empl_id = ".db_escape($employee_id);
    }

    if ($month != '')
    {
        $sql .= " AND ".TB_PREF."emp_attendance.month_id = ".db_escape($month);
    }
    if ($f_year != '')
    {
        $sql .= " AND ".TB_PREF."emp_attendance.fiscal_year = ".db_escape($f_year);
    }
//    	if ($emp_grade != ALL_TEXT)
//        {
//               $sql .= " AND ".TB_PREF."employee.emp_grade = ".db_escape($emp_grade);
//        }
//
//        if ($employee_id != ALL_TEXT)
//        {
//               $sql .= " AND ".TB_PREF."employee.employee_id = ".db_escape($employee_id);
//        }

    return db_query($sql, "Error");
}
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
    _("Leave Days"),

);


function get_employee_namee_new($employee_id)
{

    $sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}


//$th = array(_("Item Code"));
table_header($th);

$date ='2017-'.$_POST['month'].'-01';
$end = '2017-'.$_POST['month'].'-' . date('t', strtotime($date)); //get end date of month
//        while(strtotime($date) <= strtotime($end))
        {
//            display_error("ASdasdsa");
            $day_num = date('d', strtotime($date));
            $day_name = date('l', strtotime($date));

            $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
//            echo "<td>$day_name <br/>$day_num </td>";
            $array[] = $day_name . ' ' . $day_num;

$k = 1;  //row colour counter
$result = get_0_emp_attendance($day_num);
$d = date("d",strtotime($end));
while($myrow = db_fetch($result))
{

    alt_table_row_color($k);
    label_cell($myrow['empl_id']);
    label_cell(get_employee_namee_new($myrow['empl_id']));


    $ex = explode('-', $myrow['Record01']);
    $leave_name= get_advance_name_neww($ex[0]);
    $myrow['Record01'] = $leave_name.'<br>'.$ex[1];

    $ex2 = explode('-', $myrow['Record02']);
    $leave_name= get_advance_name_neww($ex2[0]);
    $myrow['Record02'] = $leave_name.'<br>'.$ex2[1];


    $ex3 = explode('-', $myrow['Record03']);
    $leave_name= get_advance_name_neww($ex3[0]);
    $myrow['Record03'] = $leave_name.'<br>'.$ex3[1];


    $ex4 = explode('-', $myrow['Record04']);
    $leave_name= get_advance_name_neww($ex4[0]);
    $myrow['Record04'] = $leave_name.'<br>'.$ex4[1];

    $ex5 = explode('-', $myrow['Record05']);
    $leave_name= get_advance_name_neww($ex5[0]);
    $myrow['Record05'] = $leave_name.'<br>'.$ex5[1];

    $ex6 = explode('-', $myrow['Record06']);
    $leave_name= get_advance_name_neww($ex6[0]);
    $myrow['Record06'] = $leave_name.'<br>'.$ex6[1];

    $ex7 = explode('-', $myrow['Record07']);
    $leave_name= get_advance_name_neww($ex7[0]);
    $myrow['Record07'] = $leave_name.'<br>'.$ex7[1];

    $ex8 = explode('-', $myrow['Record08']);
    $leave_name= get_advance_name_neww($ex8[0]);
    $myrow['Record08'] = $leave_name.'<br>'.$ex8[1];

    $ex9 = explode('-', $myrow['Record09']);
    $leave_name= get_advance_name_neww($ex9[0]);
    $myrow['Record09'] = $leave_name.'<br>'.$ex9[1];

    $ex10 = explode('-', $myrow['Record10']);
    $leave_name= get_advance_name_neww($ex10[0]);
    $myrow['Record10'] = $leave_name.'<br>'.$ex10[1];


    $ex11 = explode('-', $myrow['Record11']);
    $leave_name= get_advance_name_neww($ex11[0]);
    $myrow['Record11'] = $leave_name.'<br>'.$ex11[1];

    $ex12 = explode('-', $myrow['Record12']);
    $leave_name= get_advance_name_neww($ex12[0]);
    $myrow['Record12'] = $leave_name.'<br>'.$ex12[1];


    $ex13 = explode('-', $myrow['Record13']);
    $leave_name= get_advance_name_neww($ex13[0]);
    $myrow['Record13'] = $leave_name.'<br>'.$ex13[1];


    $ex14 = explode('-', $myrow['Record14']);
    $leave_name= get_advance_name_neww($ex14[0]);
    $myrow['Record14'] = $leave_name.'<br>'.$ex14[1];

    $ex15 = explode('-', $myrow['Record15']);
    $leave_name= get_advance_name_neww($ex15[0]);
    $myrow['Record15'] = $leave_name.'<br>'.$ex15[1];

    $ex16 = explode('-', $myrow['Record16']);
    $leave_name= get_advance_name_neww($ex16[0]);
    $myrow['Record16'] = $leave_name.'<br>'.$ex16[1];

    $ex17 = explode('-', $myrow['Record17']);
    $leave_name= get_advance_name_neww($ex17[0]);
    $myrow['Record17'] = $leave_name.'<br>'.$ex17[1];

    $ex18 = explode('-', $myrow['Record18']);
    $leave_name= get_advance_name_neww($ex18[0]);
    $myrow['Record18'] = $leave_name.'<br>'.$ex18[1];

    $ex19 = explode('-', $myrow['Record19']);
    $leave_name= get_advance_name_neww($ex19[0]);
    $myrow['Record19'] = $leave_name.'<br>'.$ex19[1];

    $ex20 = explode('-', $myrow['Record20']);
    $leave_name= get_advance_name_neww($ex20[0]);
    $myrow['Record20'] = $leave_name.'<br>'.$ex20[1];

    $ex21 = explode('-', $myrow['Record21']);
    $leave_name= get_advance_name_neww($ex21[0]);
    $myrow['Record21'] = $leave_name.'<br>'.$ex21[1];

    $ex22 = explode('-', $myrow['Record22']);
    $leave_name= get_advance_name_neww($ex22[0]);
    $myrow['Record22'] = $leave_name.'<br>'.$ex22[1];


    $ex23 = explode('-', $myrow['Record23']);
    $leave_name= get_advance_name_neww($ex3[0]);
    $myrow['Record23'] = $leave_name.'<br>'.$ex23[1];


    $ex24 = explode('-', $myrow['Record24']);
    $leave_name= get_advance_name_neww($ex24[0]);
    $myrow['Record24'] = $leave_name.'<br>'.$ex24[1];

    $ex25 = explode('-', $myrow['Record25']);
    $leave_name= get_advance_name_neww($ex25[0]);
    $myrow['Record25'] = $leave_name.'<br>'.$ex25[1];

    $ex26 = explode('-', $myrow['Record26']);
    $leave_name= get_advance_name_neww($ex26[0]);
    $myrow['Record26'] = $leave_name.'<br>'.$ex26[1];

    $ex27 = explode('-', $myrow['Record27']);
    $leave_name= get_advance_name_neww($ex27[0]);
    $myrow['Record27'] = $leave_name.'<br>'.$ex27[1];

    $ex28 = explode('-', $myrow['Record28']);
    $leave_name= get_advance_name_neww($ex28[0]);
    $myrow['Record28'] = $leave_name.'<br>'.$ex28[1];

    $ex29 = explode('-', $myrow['Record29']);
    $leave_name= get_advance_name_neww($ex29[0]);
    $myrow['Record29'] = $leave_name.'<br>'.$ex29[1];

    $ex30 = explode('-', $myrow['Record30']);
    $leave_name= get_advance_name_neww($ex30[0]);
    $myrow['Record30'] = $leave_name.'<br>'.$ex30[1];

    $ex31 = explode('-', $myrow['Record31']);
    $leave_name= get_advance_name_neww($ex31[0]);
    $myrow['Record31'] = $leave_name.'<br>'.$ex31[1];


    label_cell($myrow['Record01']);
    label_cell($myrow['Record02']);
    label_cell($myrow['Record03']);
    label_cell($myrow['Record04']);
    label_cell($myrow['Record05']);
    label_cell($myrow['Record06']);
    label_cell($myrow['Record07']);
    label_cell($myrow['Record08']);
    label_cell($myrow['Record09']);
    label_cell($myrow['Record10']);
    label_cell($myrow['Record11']);
    label_cell($myrow['Record12']);
    label_cell($myrow['Record13']);
    label_cell($myrow['Record14']);
    label_cell($myrow['Record15']);
    label_cell($myrow['Record16']);
    label_cell($myrow['Record17']);
    label_cell($myrow['Record18']);
    label_cell($myrow['Record19']);
    label_cell($myrow['Record20']);
    label_cell($myrow['Record21']);
    label_cell($myrow['Record22']);
    label_cell($myrow['Record23']);
    label_cell($myrow['Record24']);
    label_cell($myrow['Record25']);
    label_cell($myrow['Record26']);
    label_cell($myrow['Record27']);
    label_cell($myrow['Record28']);
    label_cell($myrow['Record29']);
    label_cell($myrow['Record30']);
    label_cell($myrow['Record31']);
    label_cell($d);
    label_cell(getsum($myrow['empl_id'])+getsum1($myrow['empl_id'])+getsum3($myrow['empl_id'])+getsum4($myrow['empl_id'])
        +getsum5($myrow['empl_id'])
        +getsum6($myrow['empl_id'])
        +getsum7($myrow['empl_id'])
        +getsum8($myrow['empl_id'])
        +getsum9($myrow['empl_id'])
        +getsum10($myrow['empl_id'])
        +getsum11($myrow['empl_id'])
        +getsum12($myrow['empl_id'])
        +getsum13($myrow['empl_id'])
        +getsum14($myrow['empl_id'])
        +getsum15($myrow['empl_id'])
        +getsum16($myrow['empl_id'])
        +getsum17($myrow['empl_id'])
        +getsum18($myrow['empl_id'])
        +getsum19($myrow['empl_id'])
        +getsum20($myrow['empl_id'])
        +getsum21($myrow['empl_id'])
        +getsum22($myrow['empl_id'])
        +getsum23($myrow['empl_id'])
        +getsum24($myrow['empl_id'])
        +getsum25($myrow['empl_id'])
        +getsum26($myrow['empl_id'])
        +getsum27($myrow['empl_id'])
        +getsum28($myrow['empl_id'])
        +getsum29($myrow['empl_id'])
        +getsum30($myrow['empl_id'])
        +getsum31($myrow['empl_id'])
    );

//            $count = getsum($myrow['empl_id']);

//    label_cell($count["count"]);

    end_row();
}

        }


if (!@$_GET['popup'])
{
    end_form();
    end_page(@$_GET['popup'], false, false);
}
?>