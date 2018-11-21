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

function getTransactions($from, $to,$employee,$division,$project,$location,$approve)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);
	$sql = "Select * ,lea.emp_id
FROM  ".TB_PREF."leave AS lea,
".TB_PREF."employee AS emp
WHERE  
	 lea.emp_id = emp.employee_id";

//if ($approve != ALL_TEXT)
//$sql .= " AND lea.approve =".db_escape($approve);
	if ($division != 0)
		$sql .= " AND emp.division =".db_escape($division);
	if ($project != 0)
		$sql .= " AND emp.project =".db_escape($project);
	if ($location != 0)
		$sql .= " AND emp.location =".db_escape($location);
	$sql .= " AND lea.from_date >=".db_escape($fromdate);
	$sql .= " AND lea.from_date <=".db_escape($todate);

	if ($employee !=0)
		$sql .= " AND lea.emp_id = ".db_escape($employee);


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
function get_leave_type($id)
{
	$sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_project($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions WHERE type_= 2
	AND id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_designation($id)
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}


//function getTransactions($month,$dept,$employee)
//{
//	$sql = "SELECT  ".TB_PREF."employee.*
//
//    			FROM ".TB_PREF."employee
//    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee);
//
//
//	$TransResult = db_query($sql,"No transactions were returned");
//
//	return $TransResult;
//}
//function get_employee_present($month,$dept,$employee)
//{
//	$sql = "SELECT present FROM ".TB_PREF."presence WHERE employee_id=".db_escape($employee)."
//	AND emp_dept=".db_escape($dept)."
//	AND month_id=".db_escape($month);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//function get_employee_allowance_name($allow_id)
//{
//	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($allow_id);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//function get_employee_allowances($employee)
//{
//	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);
//
//	$result = db_query($sql, "could not get supplier");
//	return $result;
//}
//function get_employee_salary($employee)
//{
//	$sql = "SELECT SUM(basic_salary) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//function get_employee_allowance($employee)
//{
//	$sql = "SELECT SUM(allowance) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//function get_employee_tax($employee)
//{
//	$sql = "SELECT SUM(tax) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//function get_employee_earning($employee)
//{
//	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);
//
//	$result = db_query($sql, "could not get supplier");
//	return $result;
//}
//function get_employee_name_h($id)
//{
//	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($id);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}
//
//function get_emp_title_($group_no)
//{
//	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
//	$result = db_query($sql, "could not get group");
//	$row = db_fetch($result);
//	return $row[0];
//}
//function get_location_($id)
//{
//	$sql = "SELECT name FROM ".TB_PREF."dimensions
//	WHERE id=".db_escape($id)." AND type_='3' ";
//	$result = db_query($sql, "Could't get employee name");
//	$myrow = db_fetch($result);
//	return $myrow[0];
//}
//function get_project_($id)
//{
//	$sql = "SELECT name FROM ".TB_PREF."dimensions
//	WHERE id=".db_escape($id)." AND type_='2' ";
//	$result = db_query($sql, "Could't get employee name");
//	$myrow = db_fetch($result);
//	return $myrow[0];
//}
//function get_division_($id)
//{
//	$sql = "SELECT name FROM ".TB_PREF."dimensions
//	WHERE id=".db_escape($id)." AND type_='1' ";
//	$result = db_query($sql, "Could't get employee name");
//	$myrow = db_fetch($result);
//	return $myrow[0];
//}
//function get_employee_info($employee_id)
//{
//	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id);
//	$result = db_query($sql, "Error");
//	return db_fetch($result);
//}
//function get_employee_history($employee_history)
//{
//	$sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_history = ".db_escape($employee_history);
//	$result = db_query($sql, "Error");
//	return db_fetch($result);
//}
//function get_designation_name_new($id)
//{
//	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
//	$db = db_query($sql,'Can not get Designation name');
//	$ft = db_fetch($db);
//	return $ft[0];
//}
//function get_grade_name_new($id)
//{
//	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);
//
//	$result = db_query($sql, "could not get sales type");
//
//	$row = db_fetch_row($result);
//	return $row[0];
//}
//function get_mode_of_payment($id)
//{
//	$sql = "SELECT description FROM ".TB_PREF."salary WHERE id=".db_escape($id);
//
//	$result = db_query($sql, "could not get sales type");
//
//	$row = db_fetch_row($result);
//	return $row[0];
//}
//function get_emp_bank_account_name_n($id)
//{
//	$sql = "SELECT bank_account_name FROM " . TB_PREF . "bank_accounts WHERE id = " . db_escape($id);
//	$result = db_query($sql, "could not get allowance");
//	$row = db_fetch($result);
//	return $row[0];
//}
//
//function get_employment_history($selected_id)
//{
//	$sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_id=" .db_escape($selected_id);
//
//	return  $result = db_query($sql,"could not get company.");
//
//}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;
	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$approve = $_POST['PARAM_2'];
	//$dept = $_POST['PARAM_2'];
	$employee = $_POST['PARAM_3'];
	$division = $_POST['PARAM_4'];
	$project = $_POST['PARAM_5'];
	$location = $_POST['PARAM_6'];
	$comments = $_POST['PARAM_7'];
	$orientation = $_POST['PARAM_8'];
	$destination = $_POST['PARAM_9'];
	//if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else

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

	if ($month_name == ALL_TEXT)
		$month = _('All');
	else
		$month = get_month_name($month_name);

	if ($dept == ALL_TEXT)
		$dept_name = _('All');
	else
		$dept_name = get_emp_dept_name($dept);

	if ($employee == ALL_TEXT)
		$emp = _('All');
	else
		$emp = get_employee_name11($employee);
	$dec = user_price_dec();

//	if($division!=0)
//		$sql .= " AND division = ".db_escape($division);
//
//	if($project!=0)
//		$sql .= " AND project_name = ".db_escape($project);
//
//	if($location!=0)
//		$sql .= " AND location = ".db_escape($location);
//$month_name = get_month_name($month);

	if ($approve) $approval = _('Approve');
	else $approval = _('Unapprove');
	$orientation = ($orientation ? 'L' : 'P');
//	if ($banks == ALL_TEXT)
//		$bank = _('All');
//	else
//		$bank_name1 = get_bank_account($banks);
//	$bank_name = $bank_name1['bank_account_name'];

//    	$dec = user_price_dec();
//	$dec = 0;

//	$month_name = get_month_name($month);


//	if ($no_zeros) $nozeros = _('Yes');
//	else $nozeros = _('No');

	$cols = array(0, 65, 200,270, 330,	440);

	$headers = array(_('Leave Type'), _('Approved By'), _('Date From'), _('Date To'), _('Number of Days'), _('Comp.Date'));

	$aligns = array('left',	'left',	'left',	'left',	'left',	'left');

	$params =   array( 	0 => $comments,
		1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
		//2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
		2 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
		3 => array('text' => _('Approval'), 'from' => $approval, 'to' => $to)


	);

	$rep = new FrontReport(_('Employee Leave Status'), "SupplierBalances", user_pagesize(), 9, $orientation);
	//if ($orientation == 'P' )
		recalculate_cols($cols);

	$rep->SetHeaderType('Header20180');
	$rep->Info($params, $cols, null, $aligns);

	$rep->Font();

	$rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];

	$rep->Font('b');
//	$rep->TextCol(0, 3, $letter_date);
//	$rep->NewLine();
//	$rep->TextColLines(0, 3, $bank_address);
//	$rep->Font();
//
//	$rep->NewLine();
//	$rep->NewLine();
//
//	$rep->Font('b');
	$rep->setfontsize(14);
	$rep->multicell(200,13,"Employee Leave Status",0,'L',0,0,220,70,true);

	$rep->Font();

//	$rep->NewLine(2);
//	$rep->NewLine(1);
	//header

	for($i = 0; $i < 2 ; $i++)
	{
		$rep->setfontsize(8);

//		$rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,110, true);
		$rep->multicell(60,13,"UpTo:",0,'L',0,0,230,90,true);
		$rep->multicell(150,13,"",0,'L',0,0,290,90,true);
		$rep->multicell(80,13,"Project",0,'L',0,0,25,120,true);

//		$rep->multicell(270,20,get_employee_name_h($employee),0,'L',0,0,105,95,true);
////		$rep->Font('','','');
//		$rep->MultiCell(560, 50, "" ,1, 'C', 0,0, 25,120, true);
//		$rep->MultiCell(150, 50, "" ,1, 'C', 0,0, 25,120, true);
		$rep->MultiCell(80, 13, "Name" ,0, 'L', 0,0, 25,140, true);

		$rep->MultiCell(80, 13, "Designation:" ,0, 'L', 0,0, 25,160, true);

		$rep->MultiCell(80, 13, "D.O.J" ,0, 'L', 0,0, 25,180, true);

//		$rep->MultiCell(150, 13, "Salary 230, true);
	}
//
	$sql = "SELECT employee_id, emp_name, emp_dept,project,j_date,emp_desig FROM ".TB_PREF."employee ";
	if ($employee != ALL_TEXT)
		$sql .= "WHERE employee_id=".db_escape($employee);
//	if ($dept != ALL_TEXT)
//		$sql .= "AND emp_dept=".db_escape($dept);
	$sql .= " GROUP BY employee_id";
	$result1 = db_query($sql, "The customers could not be retrieved");

while ($myrow1 = db_fetch($result1))
{
	$result = getTransactions($from,$to,$myrow1['employee_id'],$division,$project,$location,$approve);
	$rep->multicell(150,13,get_project($myrow1['project']),0,'L',0,0,105,120,true);
	$rep->MultiCell(150, 13,$myrow1['emp_name'],0, 'L', 0,0, 105,140, true);
	$rep->MultiCell(150, 13,get_designation($myrow1['emp_desig']),0, 'L', 0,0, 105,160, true);
	$rep->MultiCell(150, 13, sql2date($myrow1['j_date']) ,0, 'L', 0,0, 105,180, true);
//	$employee_hist = get_employment_history($employee);
//	$rep->NewLine(+5);
	while ($myrow=db_fetch($result))
	{

	$rep->TextCol(0, 1,get_leave_type( $myrow['leave_type']));
	$rep->TextCol(2, 3,sql2date($myrow['from_date']));
//	$rep->TextCol(2, 4,"  ".$myrow['date_to']);
	$rep->TextCol(3, 4,sql2date($myrow['to_date']));
	$rep->TextCol(4, 5,$myrow['no_of_leave']);
		$rep->NewLine();
$no_of_days_total += $myrow['no_of_leave'];
		$rep->MultiCell(100, 13,$no_of_days_total,0, 'L', 0,0, 460,425, true);
	}

	$rep->NewLine();
	$rep->End();
}}

?>
