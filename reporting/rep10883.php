<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

//----------------------------------------------------------------------------------------------------

print_employee_balances();

function getTransactions($month,$dept,$employee)
{
	$sql = "SELECT  ".TB_PREF."employee.*

    			FROM ".TB_PREF."employee
    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee);


	$TransResult = db_query($sql,"No transactions were returned");

	return $TransResult;
}
function get_employee_present($month,$dept,$employee)
{
	$sql = "SELECT present FROM ".TB_PREF."presence WHERE employee_id=".db_escape($employee)."
	AND emp_dept=".db_escape($dept)."
	AND month_id=".db_escape($month);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowance_name($allow_id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($allow_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowances($employee)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");
	return $result;
}
function get_employee_salary($employee)
{
	$sql = "SELECT SUM(basic_salary) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowance($employee)
{
	$sql = "SELECT SUM(allowance) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_tax($employee)
{
	$sql = "SELECT SUM(tax) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_earning($employee)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");
	return $result;
}
function get_department_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_emp_title_($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
function get_location_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='3' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_project_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='2' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_division_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='1' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_employee_info($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id);
	$result = db_query($sql, "Error");
	return db_fetch($result);
}
function get_designation_name_new($id)
{
	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_grade_name_new($id)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_mode_of_payment($id)
{
	$sql = "SELECT description FROM ".TB_PREF."salary WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_emp_bank_account_name_n($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function get_employment_qualification($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."man_qualification WHERE employee_id=" .db_escape($selected_id);

	return  $result = db_query($sql,"could not get company.");

}
function get_employee_name_q($id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$month = $_POST['PARAM_0'];
	$dept = $_POST['PARAM_1'];
	$employee = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	//if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else


	$orientation = ($orientation ? 'L' : 'P');
	if ($banks == ALL_TEXT)
		$bank = _('All');
	else
		$bank_name1 = get_bank_account($banks);
	$bank_name = $bank_name1['bank_account_name'];

//    	$dec = user_price_dec();
	$dec = 0;

	$month_name = get_month_name($month);


	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 20, 120, 250, 290, 350, 400, 450, 510);

	$headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

	$aligns = array('left', 'left', 'left', 'left', 'right');

	$params = array(0 => $comments,
		1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
		//2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

	);

	$rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->SetHeaderType('Header10883');
	$rep->Info($params, $cols, null, $aligns);

	$rep->Font();

	$rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];

	$rep->Font('b');
	$rep->TextCol(0, 3, $letter_date);
	$rep->NewLine();
	$rep->TextColLines(0, 3, $bank_address);
	$rep->Font();

	$rep->NewLine();
	$rep->NewLine();

	$rep->Font('b');
	$rep->NewLine();
	$rep->Font();

	$rep->NewLine(2);
	$rep->NewLine(1);
	//header
//	$rep->MultiCell(545, 70, "" ,1, 'L', 0, 2, 25,90, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 25,165, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 395,165, true);
//	$rep->MultiCell(545, 100, "" ,1, 'L', 0, 2, 25,465, true);
	//$logo = company_path() . "/images/" . 1;

	//if ($rep->company['coy_logo'] != '' && file_exists($logo))
	//{


	//}
	//$rep->multicell(100,100,"".$logo,1,'C',1,0,40,10,true);
	for ($i = 0; $i < 2; $i++) {

		$rep->MultiCell(50, 15, "", 0, 'L', 0, 2, 30, 110, true);
//		$rep->MultiCell(182.5, 15, "PAYSLIP FOR THE MONTH OF " ,0, 'L', 0, 2, 30,90, true);
//		$rep->MultiCell(220, 30, "Designation: ",0, 'L', 0, 2, 320,120.8, true);
//		$rep->MultiCell(50, 15, "Project: ",0, 'L', 0, 2, 30,140, true);
//		$rep->MultiCell(50, 15, "Employee Code: ",0, 'L', 0, 2, 190,110, true);
//		$rep->MultiCell(50, 15, "Date Of Joining :",0, 'L', 0, 2, 190,136, true);
//		$rep->MultiCell(50, 15, "NTN# ",0, 'L', 0, 2, 320,136, true);
//		$rep->Font('times','','16');
		$rep->multicell(270, 30, " Employee Name:", 0, 'L', 0, 0, 25, 95, true);
		$rep->multicell(270,20,"",1,'L',0,0,98,92,true);
		$rep->multicell(270,20,get_employee_name_q($employee),0,'L',0,0,105,95,true);
//		$rep->Font('','','');
		$rep->MultiCell(560, 50, "", 1, 'C', 0, 0, 25, 120, true);
		$rep->MultiCell(150, 50, "", 1, 'C', 0, 0, 25, 120, true);
		$rep->MultiCell(150, 50, "Qualification Degree", 0, 'C', 0, 0, 25, 135, true);
		$rep->MultiCell(80, 50, "", 1, 'C', 0, 0, 175, 120, true);
		$rep->MultiCell(80, 50, "Passing Year", 0, 'C', 0, 0, 175, 135, true);
		$rep->MultiCell(80, 50, "", 1, 'C', 0, 0, 255, 120, true);
		$rep->MultiCell(80, 50, "Institute", 0, 'C', 0, 0, 255, 135, true);
		$rep->MultiCell(93, 50, "", 1, 'C', 0, 0, 335, 120, true);
		$rep->MultiCell(93, 50, "Passing %", 0, 'C', 0, 0, 335, 135, true);
		$rep->MultiCell(157, 50, "", 1, 'C', 0, 0, 428, 120, true);
		$rep->MultiCell(157, 50, "Remarks", 0, 'C', 0, 0, 428, 135, true);
		$rep->MultiCell(560, 700, "", 1, 'C', 0, 0, 25, 120, true);
		$rep->MultiCell(150, 700, "", 1, 'C', 0, 0, 25, 120, true);
		$rep->MultiCell(80, 700, "", 1, 'C', 0, 0, 175, 120, true);
		$rep->MultiCell(80, 700, "", 1, 'C', 0, 0, 255, 120, true);
		$rep->MultiCell(93, 700, "", 1, 'C', 0, 0, 335, 120, true);
		$rep->MultiCell(157, 700, "", 1, 'C', 0, 0, 428, 120, true);
	}

	$employee_qualification = get_employment_qualification($employee);
	$rep->NewLine(+5);
	while ($myrow=db_fetch($employee_qualification))
	{

		$rep->TextCol(0, 1, $myrow['degree']);
		$rep->TextCol(2, 3, "          ".$myrow['passing_year']);
		$rep->TextCol(2, 4,"                                         ".$myrow['institute']);
		$rep->TextCol(4, 5, "          ".$myrow['passing_percent']);
		$rep->TextCol(5, 7, "                ".$myrow['remarks']);
		$rep->NewLine();

	}
	$rep->NewLine();
	$rep->End();
}

?>
