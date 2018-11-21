<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
include_once($path_to_root . "/payroll/includes/db/dept_db.inc");
//----------------------------------------------------------------------------------------------------

print_employee_balances();

function getTransactions($divison, $project,$location,$employee)
{
    $sql = "SELECT ".TB_PREF."payroll.*,".TB_PREF."payroll.emp_id AS employee FROM `".TB_PREF."payroll`,".TB_PREF."payroll_head
WHERE ".TB_PREF."payroll_head.trans_no=".TB_PREF."payroll.`payroll_head`";
    if ($divison != 0)
        $sql .= "AND ".TB_PREF."payroll.divison =".db_escape($divison);
    if ($project != 0)
        $sql .= "AND ".TB_PREF."payroll.project =".db_escape($project);
    if ($location != 0)
        $sql .= "AND ".TB_PREF."payroll.location =".db_escape($location);

    if ($employee != ALL_TEXT)
        $sql .= "AND ".TB_PREF."payroll.emp_id =".db_escape($employee);


//if ($month != ALL_TEXT)
//$sql .= "AND ".TB_PREF."payroll.month =".db_escape($month);

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
function get_employee_name11($employee_id)
{
    $sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employe_name ($employee_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $fetch =db_fetch($result);
    return $fetch;
}
function get_division_name($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}

function get_location_name2($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE main_project=".db_escape($id)." AND type_='2' ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_location_name21($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE main_project=".db_escape($id)." AND type_='3' ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_man($employee_id)
{
    $sql = "SELECT man_month_value FROM ".TB_PREF."man_month WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    global $path_to_root, $systypes_array;

    $divison = $_POST['PARAM_0'];
    $project = $_POST['PARAM_1'];
    $location  = $_POST['PARAM_2'];
    $employee = $_POST['PARAM_3'];
//		$month = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];
    $destination = $_POST['PARAM_6'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');
    /*
    if ($banks == ALL_TEXT)
        $bank = _('All');
    else
        $bank_name1 = get_bank_account($banks);
        $bank_name = $bank_name1['bank_account_name'];
*/

    if ($divison == ALL_TEXT)
        $div = _('All');
    else
        $div = get_division_name ($divison);

    if ($project == ALL_TEXT)
        $pro = _('All');
    else
        $pro = get_location_name2($project);

    if ($location == ALL_TEXT)
        $pro = _('All');
    else
        $pro = get_location_name21($location);

    if ($employee == ALL_TEXT)
        $emp = _('All');
    else
        $emp = get_employee_name11($employee);
    $dec = user_price_dec();

//
//  if ($month == ALL_TEXT)
//		$mon = _('All');
//	else
//		$mon= get_month_name($month);
//

    //$month_name = get_month_name($month);


    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');

    $cols = array(0, 100, 200, 300, 400, 500);

    $headers = array(_('Code'), _('Employee Name'), _('Designation.'), _('Last Month.'), _('Current Man Month'), _('Date Of Joining'));

    $aligns = array('left', 'left', 'left', 'left', 'left',  'left');

//
    $orientation = 'L';
    $rep = new FrontReport(_(''), "ManMonthReport", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->SetHeaderType('Header12');
    $rep->Info($params, $cols, $headers, $aligns);
    $logo = company_path() . "/	images/" . 1;

    if ($rep->company['coy_logo'] != '' && file_exists($logo))
    {


    }

    $rep->Font();
    //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();


    $rep->NewLine(1);



    function get_designation_names($id)
    {
        $sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
        $db = db_query($sql,'Can not get Designation name');
        $ft = db_fetch($db);
        return $ft[0];
    }
    $result = getTransactions($divison,$project,$location,$employee);

    while ($myrow=db_fetch($result))
    {
        $rep->fontSize += 2;
        $rep->MultiCell(600, 600, "Employee Man Month Report: ",0, 'L', 0, 2, 60,60, true);
        $rep->fontSize -= 2;


        $emp = get_employe_name($myrow['emp_id']);
        $empp = get_man($myrow['emp_id']);


        $rep->TextCol(0, 1, $emp['emp_code']);
        $rep->TextCol (1, 2, $emp['emp_name'] );
        $rep->TextCol (2, 3, get_designation_names($emp['emp_desig']));
        $rep->TextCol(3, 4,sql2date($emp['l_date']) );
        $rep->TextCol(4, 5,$empp);
        $rep->TextCol(5, 6, sql2date($emp['j_date']));
        $rep->NewLine();
        $rep->TextCol(0, 7, "_____________________________________________________________________________________________________________________________________________");

        $rep->NewLine();

    }

    $rep->End();
}

?>