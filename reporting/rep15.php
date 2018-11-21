<?php
$page_security = 'SA_OPEN';

$path_to_root="..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

////----------------------------------------------------------------------------------------------------

print_employee_balances();
//
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
	$sql = "SELECT ((0_payroll.`basic_salary`*0_payroll.man_month_value)-(0_payroll.tax+0_payroll.eobi+0_payroll.advance_deduction)) AS Total_net,0_salary.id
FROM  `0_payroll` 
LEFT JOIN 0_employee ON 0_employee.employee_id = 0_payroll.emp_id
LEFT JOIN 0_salary ON 0_salary.id = 0_employee.salary
WHERE 0_employee.employee_id=$emp_id ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft;
}
////----------------------------------------------------------------------------------------------------

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

    $orientation ='L' ;

    /*
    if ($banks == ALL_TEXT)
        $bank = _('All');
    else
//		$bank_name1 = get_bank_account($banks);
//		$bank_name = $bank_name1['bank_account_name'];
//*/

if ($divison == ALL_TEXT)
		$div = _('All');
	else
		$div = get_division_name ($divison);

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





		$month_name = get_month_name($month);


//	if ($no_zeros) $nozeros = _('Yes');
//	else $nozeros = _('No');

//	$cols = array(0, 10, 70, 110, 140, 180, 190, 220, 260, 300, 320, 350, 400, 390, 430,500);
	$cols = array(0, 10, 70, 110,140,180,190,220,260,300,320,350,400,420,460,500,500,580);
//,400,430,450,500
	$headers = array(_('Code Name'), _('Desig'), _('Man Month.'), _('Basic Sal.'), _('ManMon'), _('Sal. as Man Mon.'),  _('Tax Rate'), _('Tax Amt'), _('EOBI'), _('Adv. Ded.'), _('Advances'), _('Allowances'), _('Ded.'), _('Total Ded.'), _('Net Salary'));

//	$aligns = array('left', 'left', 'left', 'left', 'left',  'left', 'left', 'left','left', 'left', 'left', 'left', 'left', 'left', 'left', 'right');
    $aligns = array('left', 'left', 'left', 'left','left','left','left','left','left','left','left','left'   ,'left','left','left','left','right');

//,'right','right','right','right'

//
//    $params =   array( 	0 => $comments,
//    			1 => array('text' => _('Divison'), 'from' => $divison, 'to' => $to),
//    			2 => array('text' => _('Project'), 'from' => $project, 'to' => $to),
//    			3 => array('text' => _('Location'), 'from' => $location, 'to' => $to),
//				4 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
//				5 => array('text' => _('Employee'), 'from' => $month, 'to' => $to)
//
//			);

    $rep = new FrontReport(_('Monthly Salary Sheet'), "MonthlySalarySheet", user_pagesize(), 9, 'L');
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header15');
			$rep->Info($params, $cols, '', $aligns);



    $rep->Font();
//    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $rep->MultiCell(750, 50, "", 1, 'L', 0, 2, 20, 50, true);
    $rep->MultiCell(150, 50, "C-Name     Desig      Man Month", 0, 'L', 0, 2, 40, 70, true);

    $rep->MultiCell(170, 10, "         Salary And Allowance", 1, 'L', 0, 2, 200, 50, true);
    $rep->MultiCell(170, 50, " ", 1, 'L', 0, 2, 200, 50, true);
    $rep->MultiCell(170, 10, "    Gross       Special              Total", 0, 'L', 0, 2, 200, 70, true);
    $rep->MultiCell(170, 10, "    Salary       Allow    Arears   Salary", 0, 'L', 0, 2, 200, 80, true);


    $rep->MultiCell(170, 10, "                 Deductions ", 1, 'L', 0, 2, 365, 50, true);
    $rep->MultiCell(170, 50, " ", 1, 'L', 0, 2, 365, 50, true);
    $rep->MultiCell(170, 10, "     I.T         Adv      EOBI        Total", 0, 'L', 0, 2, 365, 70, true);
    $rep->MultiCell(170, 10, "                                                DED", 0, 'L', 0, 2, 365, 80, true);

    $rep->MultiCell(170, 10, "                Salary Payable ", 1, 'L', 0, 2, 540, 50, true);
    $rep->MultiCell(170, 50, " ", 1, 'L', 0, 2, 540, 50, true);
    $rep->MultiCell(170, 10, "   Cash      Bank       Checque     Draft", 0, 'L', 0, 2, 540, 70, true);



    $rep->MultiCell(100, 10, "Amount", 0, 'L', 0, 2, 720, 60, true);
    $rep->MultiCell(100, 10, "Payable", 0, 'L', 0, 2, 720, 70, true);
    $rep->Font('b');
    $rep->MultiCell(750, 18, "Division->".$div." Project->".$pro." Location->".$loc , 0, 'L', 0, 2, 20, 105, true);
    $rep->Font();

	$sql = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month != ALL_TEXT)
		$sql .= "WHERE id=".db_escape($month);
	$sql .= " ORDER BY id";
	$res = db_query($sql, "The customers could not be retrieved");
	$rep->NewLine();
	$rep->Line($rep->row - 4);
	while ($myrow1=db_fetch($res)) {
		    $result = getTransactions($divison, $project, $location, $employee, $myrow1['id']);
		$rep->TextCol(0, 15, $myrow1['description']);
		$rep->Line($rep->row - 4);
		$rep->NewLine(2);

		while ($myrow = db_fetch($result)) {
			$emp = get_employe_name($myrow['emp_id']);

$rep->fontSize -= 2;
			$rep->TextCol(0, 1, $emp['emp_code']);
			$rep->TextCol(1, 2, $emp['emp_name']);
			$rep->TextCol(2, 3, get_designation_names($emp['emp_desig']));
			$rep->AmountCol(3, 4, $myrow['man_month_value'], $dec);
			$rep->AmountCol(4, 5, $myrow['basic_salary'], $dec);
			$rep->TextCol(5, 6, $myrow['allowance']);
			$rep->TextCol(6, 7, $myrow['arrer']);
			$rep->AmountCol(7, 8, $myrow['basic_salary']*$myrow['man_month_value'], $dec);
			$rep->AmountCol(8, 9, $myrow['tax'], $dec);
			$rep->AmountCol(9, 10, $myrow['advance_deduction'], $dec);
			$rep->AmountCol(10, 11, $myrow['eobi'], $dec);
			$rep->AmountCol(11, 12, $myrow['total_deduction'], $dec);


			$payment = get_payment_wise_salary($myrow['emp_id']);
			if ($payment['id'] == 6) {
				$rep->AmountCol(12, 13, $payment['Total_net'], $dec);
				$total_payment_net1 += $payment['Total_net'];
			}
			if ($payment['id'] == 4) {
				$rep->AmountCol(13, 14, $payment['Total_net'], $dec);
				$total_payment_net2 += $payment['Total_net'];
			}
			if ($payment['id'] == 2) {
				$rep->AmountCol(14, 15, $payment['Total_net'], $dec);
				$total_payment_net3 += $payment['Total_net'];
			}
			if ($payment['id'] == 3) {
				$rep->AmountCol(15, 16, $payment['Total_net'], $dec);
				$total_payment_net4 += $payment['Total_net'];
			}
$rep->fontSize += 2;
			$TotalManMonth += $myrow['man_month_value'];
			$TotalBasicSalary += $myrow['basic_salary'];
			$TotalArrears += $myrow['arrer'];
			$TotalNetSalary += ($myrow['basic_salary']*$myrow['man_month_value']);
			$TotalTax += $myrow['tax'];
			$TotalAdvance += $myrow['advance'];
			$TotalEobi += $myrow['eobi'];
			$TotalDeduction += $myrow['total_deduction'];
			$TotalAmountPayable += $myrow['net_salary'] ;
			$rep->NewLine();

		}



		$rep->Font(b);
		$rep->AmountCol(3, 4, $TotalManMonth, $dec);
		$rep->AmountCol(4, 5, $TotalBasicSalary, 0);

		$rep->AmountCol(6, 7, $TotalArrears, 0);
		$rep->AmountCol(7, 8, $TotalNetSalary, 0);
		$rep->AmountCol(8, 9, $TotalTax, 0);
		$rep->AmountCol(9, 10, $TotalAdvance, 0);
		$rep->AmountCol(10, 11, $TotalEobi, 0);
		$rep->AmountCol(11, 12, $TotalDeduction, $dec);

		$rep->AmountCol(12, 13, $total_payment_net1, $dec);
		$rep->AmountCol(13, 14, $total_payment_net2, $dec);
		$rep->AmountCol(14, 15, $total_payment_net3, $dec);
		$rep->AmountCol(15, 16, $total_payment_net4, $dec);

		$rep->AmountCol(16, 17, $TotalAmountPayable, $dec);


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

		$rep->Font();
		$TotalManMonth= $TotalBasicSalary =$TotalArrears=$TotalNetSalary=$TotalTax=$TotalAdvance=$TotalEobi=
		$TotalDeduction=$TotalAmountPayable=$total_payment_net1=$total_payment_net2=$total_payment_net3=$total_payment_net4=0;
	}

	$rep->Line($rep->row  - 4);
	$rep->NewLine(2);
	$rep->Font('b');
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->AmountCol(3, 4, $GrandManMonth, $dec);
	$rep->AmountCol(4, 5, $GrandBasicSalary, 0);
	$rep->AmountCol(6, 7, $GrandArrears, 0);
	$rep->AmountCol(7, 8, $GrandNetSalary, 0);
	$rep->AmountCol(8, 9, $GrandTax, 0);
	$rep->AmountCol(9, 10, $GrandAdvance, 0);
	$rep->AmountCol(10, 11, $GrandEobi, 0);
	$rep->AmountCol(11, 12, $GrandDeduction, $dec);
	$rep->AmountCol(12, 13, $Gtotal_payment_net1, $dec);
	$rep->AmountCol(13, 14, $Gtotal_payment_net2, $dec);
	$rep->AmountCol(14, 15, $Gtotal_payment_net3, $dec);
	$rep->AmountCol(15, 16, $Gtotal_payment_net4, $dec);

	$rep->AmountCol(16, 17, $GrandAmountPayable, $dec);
	$rep->fontSize -= 2;
	$rep->Font();



	$rep->NewLine();
    $rep->End();
}

?>