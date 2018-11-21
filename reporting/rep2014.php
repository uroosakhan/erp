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

function getTransactions($divison, $project,$location,$employee,$month)
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

    
if ($month != ALL_TEXT)
$sql .= "AND ".TB_PREF."payroll.month =".db_escape($month);

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
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$divison = $_POST['PARAM_0'];
    	$project = $_POST['PARAM_1'];
    	$location  = $_POST['PARAM_2'];
		$employee = $_POST['PARAM_3'];
		$month = $_POST['PARAM_4'];
    	$comments = $_POST['PARAM_5'];
	    $orientation = $_POST['PARAM_6'];
	    $destination = $_POST['PARAM_7'];
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


  if ($month == ALL_TEXT)
		$mon = _('All');
	else
		$mon= get_month_name($month);





		//$month_name = get_month_name($month);


	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 38, 63, 100, 135, 170, 220, 260, 300, 330, 370, 410, 460, 480, 520,570);

	$headers = array(_('Emp Code'), _('Emp'), _('Desig.'), _('Basic Sal.'), _('ManMon'), _('Sal. as Man Mon.'),  _('Tax Rate'), _('Tax Amt'), _('EOBI'), _('Adv. Ded.'), _('Advances'), _('Allowances'), _('Ded.'), _('Total Ded.'), _('Net Salary'));

	$aligns = array('left', 'left', 'left', 'left', 'left',  'left', 'left', 'left','left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Divison'), 'from' => $divison, 'to' => $to),
    			2 => array('text' => _('Project'), 'from' => $project, 'to' => $to),
    			3 => array('text' => _('Location'), 'from' => $location, 'to' => $to),
				4 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
				5 => array('text' => _('Employee'), 'from' => $month, 'to' => $to)

			);
$orientation = 'L';
    $rep = new FrontReport(_('Monthly Salary Sheet'), "MonthlySalarySheet", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];


   	$rep->NewLine(1);


// $sqll = "SELECT id, description FROM ".TB_PREF."month ";
	//if ($month_name != ALL_TEXT)
		//$sqll .= "WHERE id=".db_escape($month_name);
	//$sqll .= " ORDER BY id";
	//$result2 = db_query($sqll, "The customers could not be retrieved");

//	$sql = "SELECT id, description FROM ".TB_PREF."dept ";
//	//if ($dept != ALL_TEXT)
//		//$sql .= "WHERE id=".db_escape($dept);
//	$sql .= " ORDER BY description";
//	$result1 = db_query($sql, "The customers could not be retrieved");

	//$num_lines = 0;

//while ($myrow2 = db_fetch($result2))
//{

		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	//$rep->TextCol(0, 5, $myrow2['description']);
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
//$result = getTransactions($myrow2['id'],$dept,$employee);

	//while ($myrow1 = db_fetch($result1))
//{
	
		//if (db_num_rows($result) == 0) continue;

	//$rep->fontSize += 2;
    //	$rep->Font(b);
	//$rep->TextCol(0, 5, $myrow1['description']);
    //	$rep->Font();
	//$rep->fontSize -= 2;
    //	$rep->NewLine();

	function get_designation_names($id)
	{
		$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
		$db = db_query($sql,'Can not get Designation name');
		$ft = db_fetch($db);
		return $ft[0];
	}
$result = getTransactions($divison,$project,$location,$employee,$month);
	
	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;
		//$SerialNo += 1;
		//$rep->TextCol(0, 1, get_emp_dept_name($myrow['dept_id']));
		$emp = get_employe_name($myrow['emp_id']);
		$rep->TextCol(0, 1, $emp['emp_code']);
		$rep->TextCol (1, 2, $emp['emp_name'] );
		$rep->TextCol (2, 3, get_designation_names($emp['emp_desig']));
	    $rep->AmountCol(3, 4, $myrow['basic_salary'], $dec);
		$rep->AmountCol(4, 5, $myrow['absent']);
		$rep->TextCol(5, 6, $myrow['leave']);
		$rep->TextCol(6, 7, $myrow['Over_time_hour']); 
		$rep->AmountCol(7, 8, $myrow['Overtime'], $dec);
		$rep->AmountCol(8, 9, $myrow['tax'], $dec);
		$rep->AmountCol(9, 10, $myrow['tax_rate'], $dec);
		$rep->AmountCol(10, 11, $myrow['late_deduction'], $dec);
		$rep->AmountCol(11, 12, $myrow['advance_deduction'], $dec);
		$rep->AmountCol(12, 13, $myrow['allowance'], $dec);
		$rep->AmountCol(13, 14, $myrow['deduction'], $dec);
		$rep->AmountCol(14, 15, $NetSalary, $dec);

		$TotalBasicSalary += $myrow['basic_salary'];
		$TotalAdvance += $myrow['Advance'];
		$TotalPresent += $myrow['absent'];
		$TotalLeave += $myrow['leave'];
		//$TotalAbsent += $myrow['absent'];
		$Totaldutyhours += $myrow['Over_time_hour'];
		$TotalOver_Time += $myrow['overtime'];
		$TotalTax += $myrow['tax'];
		$TotalTax_rate += $myrow['tax_rate'];
		$TotalLateDeduction += $myrow['late_deduction'];
		$TotalAdvDeduction += $myrow['advance_deduction'];
		$TotalEmpAllowances += $myrow['allowance'];
		$TotalEmpDeductions += $myrow['deduction'];
		$TotalNetSalary += $NetSalary;

		$GrandBasicSalary += $myrow['basic_salary'];
		$GrandAdvance += $myrow['Advance'];
		$GrandPresent += $myrow['absent'];
		$GrandLeave += $myrow['leave'];
		//$GrandAbsent += $myrow['absent'];
		$Granddutyhours += $myrow['Over_time_hour'];
		$GrandOver_Time += $myrow['overtime'];
		$GrandTax += $myrow['tax'];
		$GrandTax_rate += $myrow['tax_rate'];
		$GrandLateDeduction += $myrow['late_deduction'];
		$GrandAdvDeduction += $myrow['advance_deduction'];
		$GrandEmpAllowances += $myrow['allowance'];
		$GrandEmpDeductions += $myrow['deduction'];
		$GrandNetSalary += $NetSalary;

		
		
    	$rep->NewLine();

	}

	$rep->Font(b);
		$rep->TextCol(0, 15, _('Total'));

		$rep->AmountCol(2, 3, $TotalBasicSalary, $dec);
		$rep->AmountCol(3, 4, $TotalAdvance, 0);
		$rep->AmountCol(4, 5, $TotalPresent, 0);
		$rep->AmountCol(5, 6, $TotalLeave, 0);
		$rep->AmountCol(6, 7, $Totaldutyhours, $dec);
		$rep->AmountCol(7, 8, $TotalOver_Time, $dec);
		$rep->AmountCol(8, 9, $TotalTax, $dec);
		$rep->AmountCol(9,10, $TotalTax_rate, $dec);
		$rep->AmountCol(10, 11, $TotalLateDeduction, $dec);
		$rep->AmountCol(11, 12, $TotalAdvDeduction, $dec);
		$rep->AmountCol(12, 13, $TotalEmpAllowances, $dec);
		$rep->AmountCol(13, 14, $TotalEmpDeductions, $dec);
		$rep->AmountCol(14, 15, $TotalNetSalary, $dec);
		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalEmpAllowances = $TotalEmpDeductions = $TotalNetSalary = 0;
	$rep->Font();
    	$rep->NewLine(2);
//}
//}

	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
		$rep->AmountCol(2, 3, $GrandBasicSalary, $dec);
		$rep->AmountCol(3, 4, $GrandAdvance, 0);
		$rep->AmountCol(4, 5, $GrandPresent, 0);
		$rep->AmountCol(5, 6, $GrandLeave, 0);
		$rep->AmountCol(6, 7, $Granddutyhours, $dec);
		$rep->AmountCol(7, 8, $GrandOver_Time, $dec);
		$rep->AmountCol(8, 9, $GrandTax, $dec);
		$rep->AmountCol(9, 10, $GrandTax_rate, $dec);
		$rep->AmountCol(10, 11, $GrandLateDeduction, $dec);
		$rep->AmountCol(11, 12, $GrandAdvDeduction, $dec);
		$rep->AmountCol(12, 13, $GrandEmpAllowances, $dec);
		$rep->AmountCol(13, 14, $GrandEmpDeductions, $dec);
	$rep->AmountCol(14, 15,	$GrandNetSalary, $dec);	
	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>