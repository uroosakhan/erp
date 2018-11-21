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
function get_employe_name($employee_id)
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
	WHERE id=".db_escape($id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_location_name21($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_designation_names($id)
{
	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_payment_wise_salary($emp_id)
{
	$sql = "SELECT (0_payroll.`net_salary`-(0_payroll.tax+0_payroll.eobi+0_payroll.advance_deduction)) AS Total_net,0_salary.id
FROM  `0_payroll` 
LEFT JOIN 0_employee ON 0_employee.employee_id = 0_payroll.emp_id
LEFT JOIN 0_salary ON 0_salary.id = 0_employee.salary
WHERE 0_employee.employee_id=$emp_id ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft;
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

if ($divison == ALL_TEXT)
		$div = _('All');
	else
		$div = get_division_name($divison);

	if ($project == ALL_TEXT)
		$pro = _('All');
	else
		$pro = get_location_name2($project);

  if ($location == ALL_TEXT)
		$loc = _('All');
	else
		$loc = get_location_name21($location);

  if ($employee == ALL_TEXT)
		$emp = _('All');
	else
		$emp = get_employee_name11($employee);
    	$dec = user_price_dec();


  if ($month == ALL_TEXT)
		$mon = _('All');
	else
		$mon= get_month_name($month);
	$cols = array(0, 28, 90, 150, 185, 230, 255, 295, 320, 350, 380, 420, 440, 460 , 480, 500,520,540, 580);

	$headers = array(_('Code'), _('Name'), _('Desig.'), _('ManMon'), _('Gross Sal.'),
		 _('Arrears'), _('Total Sal.'), _('IT'),_('Adv.'), _('EOBI'), _('Total Ded.'),
		_('Cash'),_('Bank'),_('Cheque'),_('Draft'),_('Acct T/R'),_('Other'), _('Amount pay.'));

	$aligns = array('left', 'left', 'left', 'left', 'left',  'left', 'left', 'left','left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Divison'), 'from' => $div, 'to' => $to),
    			2 => array('text' => _('Project'), 'from' => $pro, 'to' => $to),
    			3 => array('text' => _('Location'), 'from' => $loc, 'to' => $to),
				4 => array('text' => _('Employee'), 'from' => $mon, 'to' => $to)

			);
$orientation = 'L';
    $rep = new FrontReport(_('Monthly Payroll'), "MonthlySalarySheet", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
			$rep->SetHeaderType('Header');
			$rep->Info($params, $cols, $headers, $aligns);
    $rep->Font();

    $rep->NewPage();

	$sql = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month != ALL_TEXT)
		$sql .= "WHERE id=".db_escape($month);
	$sql .= " ORDER BY id";
	$res = db_query($sql, "The customers could not be retrieved");
	while ($myrow1=db_fetch($res)) {
		$result = getTransactions($divison, $project, $location, $employee, $myrow1['id']);
		$rep->TextCol(0, 15, $myrow1['description']);
		$rep->Line($rep->row - 4);
		$rep->NewLine(2);

		while ($myrow = db_fetch($result)) {
			$emp = get_employe_name($myrow['emp_id']);
			$rep->TextCol(0, 1, $emp['emp_code']);
			$rep->TextCol(1, 2, $emp['emp_name']);
			$rep->TextCol(2, 3, get_designation_names($emp['emp_desig']));
			$rep->AmountCol(3, 4, $myrow['man_month_value'], $dec);
			$rep->AmountCol(4, 5, $myrow['basic_salary'], $dec);
			$rep->AmountCol(5, 6, $myrow['arrer']);
			$rep->AmountCol(6, 7, $myrow['net_salary'], $dec);
			$rep->AmountCol(7, 8, $myrow['tax']);
			$rep->AmountCol(8, 9, $myrow['advance'], $dec);
			$rep->AmountCol(9, 10, $myrow['eobi']);
			$rep->AmountCol(10, 11, $myrow['total_deduction'], $dec);
			$payment = get_payment_wise_salary($myrow['emp_id']);
			if ($payment['id'] == 6) {
				$rep->AmountCol(11, 12, $payment['Total_net'], $dec);
				$total_payment_net1 += $payment['Total_net'];
			}
			if ($payment['id'] == 7) {
				$rep->AmountCol(12, 13, $payment['Total_net'], $dec);
				$total_payment_net2 += $payment['Total_net'];
			}
			if ($payment['id'] == 2) {
				$rep->AmountCol(13, 14, $payment['Total_net'], $dec);
				$total_payment_net3 += $payment['Total_net'];
			}
			if ($payment['id'] == 3) {
				$rep->AmountCol(14, 15, $payment['Total_net'], $dec);
				$total_payment_net4 += $payment['Total_net'];
			}
			if ($payment['id'] == 4) {
				$rep->AmountCol(15, 16, $payment['Total_net'], $dec);
				$total_payment_net5 += $payment['Total_net'];
			}
			if ($payment['id'] == 5) {
				$rep->AmountCol(16, 17, $payment['Total_net'], $dec);
				$total_payment_net6 += $payment['Total_net'];
			}
			$TotalManMonth += $myrow['man_month_value'];
			$TotalBasicSalary += $myrow['basic_salary'];
			$TotalArrears += $myrow['arrer'];
			$TotalNetSalary += $myrow['net_salary'];
			$TotalTax += $myrow['tax'];
			$TotalAdvance += $myrow['advance'];
			$TotalEobi += $myrow['eobi'];
			$TotalDeduction += $myrow['total_deduction'];
			$TotalAmountPayable += $myrow['net_salary'] - $myrow['total_deduction'];
			$rep->NewLine();

		}


		$rep->Line($rep->row - 4);
		$rep->Font(b);
		$rep->AmountCol(3, 4, $TotalManMonth, $dec);
		$rep->AmountCol(4, 5, $TotalBasicSalary, 0);
		$rep->AmountCol(5, 6, $TotalArrears, 0);
		$rep->AmountCol(6, 7, $TotalNetSalary, 0);
		$rep->AmountCol(7, 8, $TotalTax, 0);
		$rep->AmountCol(8, 9, $TotalAdvance, 0);
		$rep->AmountCol(9, 10, $TotalEobi, 0);
		$rep->AmountCol(10, 11, $TotalDeduction, $dec);
		$rep->AmountCol(11, 12, $total_payment_net1, $dec);
		$rep->AmountCol(12, 13, $total_payment_net2, $dec);
		$rep->AmountCol(13, 14, $total_payment_net3, $dec);
		$rep->AmountCol(14, 15, $total_payment_net4, $dec);
		$rep->AmountCol(15, 16, $total_payment_net5, $dec);
		$rep->AmountCol(16, 17, $total_payment_net6, $dec);
		$rep->AmountCol(17, 18, $TotalAmountPayable, $dec);


		$GrandManMonth += $TotalManMonth;
		$GrandBasicSalary += $TotalBasicSalary;
		$GrandArrears += $TotalArrears;
		$GrandNetSalary += $TotalNetSalary;
		$GrandTax += $TotalTax;
		$GrandAdvance += $TotalAdvance;
		$GrandEobi += $TotalEobi;
		$GrandDeduction += $TotalDeduction;
		$GrandAmountPayable += $TotalNetSalary - $TotalDeduction;
		$Gtotal_payment_net1 += $total_payment_net1;
		$Gtotal_payment_net2 += $total_payment_net2;
		$Gtotal_payment_net3 += $total_payment_net3;
		$Gtotal_payment_net4 += $total_payment_net4;
		$Gtotal_payment_net5 += $total_payment_net5;
		$Gtotal_payment_net6 += $total_payment_net6;
		$rep->Font();




	}

	$rep->Line($rep->row  - 4);
   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
		$rep->AmountCol(3, 4, $GrandManMonth, $dec);
		$rep->AmountCol(4, 5, $GrandBasicSalary, 0);
		$rep->AmountCol(5, 6, $GrandArrears, 0);
		$rep->AmountCol(6, 7, $GrandNetSalary, 0);
		$rep->AmountCol(7, 8, $GrandTax, 0);
		$rep->AmountCol(8, 9, $GrandAdvance, 0);
		$rep->AmountCol(9, 10, $GrandEobi, 0);
		$rep->AmountCol(10, 11, $GrandDeduction, $dec);
	$rep->AmountCol(11, 12, $Gtotal_payment_net1, $dec);
	$rep->AmountCol(12, 13, $Gtotal_payment_net2, $dec);
	$rep->AmountCol(13, 14, $Gtotal_payment_net3, $dec);
	$rep->AmountCol(14, 15, $Gtotal_payment_net4, $dec);
	$rep->AmountCol(15, 16, $Gtotal_payment_net5, $dec);
	$rep->AmountCol(16, 17, $Gtotal_payment_net6, $dec);
	$rep->AmountCol(17, 18, $GrandAmountPayable, $dec);
	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>