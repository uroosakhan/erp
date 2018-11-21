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

function getTransactions($month_name, $dept,$employee)
{
	$sql = "SELECT ".TB_PREF."payroll.* FROM `".TB_PREF."payroll`,".TB_PREF."payroll_head
WHERE ".TB_PREF."payroll_head.trans_no=".TB_PREF."payroll.`payroll_head`";
if ($month_name != ALL_TEXT)
$sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($month_name);





if ($dept != ALL_TEXT)
$sql .= "AND ".TB_PREF."payroll_head.dept_id =".db_escape($dept);
if ($employee != ALL_TEXT)
$sql .= "AND ".TB_PREF."payroll.emp_id =".db_escape($employee);

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}


function getpreviousslary($month_name, $dept, $employee)
{
    $sql = "SELECT ".TB_PREF."payroll.* FROM `".TB_PREF."payroll`,".TB_PREF."payroll_head
WHERE ".TB_PREF."payroll_head.trans_no=".TB_PREF."payroll.`payroll_head`";
//if ($month_name != ALL_TEXT)
//$sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($month_name);


    if($month_name==1){
        $previous_month = 12;
        $sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($previous_month);
    }elseif($month_name!=1){
        $previous_month = $month_name -1 ;
        $sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($previous_month);
    }

    if ($dept != ALL_TEXT)
        $sql .= "AND ".TB_PREF."payroll_head.dept_id =".db_escape($dept);
    if ($employee != ALL_TEXT)
        $sql .= "AND ".TB_PREF."payroll.emp_id =".db_escape($employee);

    $TransResult = db_query($sql,"No transactions were returned");

    return db_fetch($TransResult);
}





function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$month_name = $_POST['PARAM_0'];
    	$dept = $_POST['PARAM_1'];
		$employee = $_POST['PARAM_2'];
    	$comments = $_POST['PARAM_3'];
	    $orientation = $_POST['PARAM_4'];
	    $destination = $_POST['PARAM_5'];
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




		//$month_name = get_month_name($month);
		
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 40, 130, 170, 200, 240,280,310);

	$headers = array(_('Name'), _(''),_("Current Salary"),_(""), _('Previous Salary'),_(""),_("Net"));

	$aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Month'), 'from' => $month, 'to' => $to),
    			2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),  
				3 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to)   			

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


 $sqll = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month_name != ALL_TEXT)
		$sqll .= "WHERE id=".db_escape($month_name);
	$sqll .= " ORDER BY id";
	$result2 = db_query($sqll, "The customers could not be retrieved");

	$sql = "SELECT id, description FROM ".TB_PREF."dept ";
	if ($dept != ALL_TEXT)
		$sql .= "WHERE id=".db_escape($dept);
	$sql .= " ORDER BY description";
	$result1 = db_query($sql, "The customers could not be retrieved");
	
	//$num_lines = 0;
	
while ($myrow2 = db_fetch($result2))
{
	
		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow2['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
//$result = getTransactions($myrow2['id'],$dept, $employee);

	while ($myrow1 = db_fetch($result1))
{

		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow1['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
$result = getTransactions($myrow2['id'],$myrow1['id'],$employee);
	
	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;
//		$rep->fontSize += 2;
        //$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;
		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] -
            $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;

        //$SerialNo += 1;
		//$rep->TextCol(0, 1, get_emp_dept_name($myrow['dept_id']));
		$rep->TextCol(0, 2, get_employee_name11($myrow['emp_id']));
        $rep->AmountCol(2, 3, $NetSalary, $dec);

        $previous_salary = getpreviousslary($myrow2['id'], $myrow1['id'], $myrow['emp_id']);

        $rep->AmountCol(4, 5, $previous_salary['basic_salary'] -
            $previous_salary['advance_deduction'] - $previous_salary['late_deduction']
            - $previous_salary['tax'] + $previous_salary['Overtime'], $dec);

        $previous_value =$previous_salary['basic_salary'] -
        $previous_salary['advance_deduction'] - $previous_salary['late_deduction']
        - $previous_salary['tax'] + $previous_salary['Overtime'];

        $difference = price_format($NetSalary-($previous_value));

         $rep->TextCol(5, 6, $difference, $dec);
        //$rep->AmountCol(4, 5, $myrow['absent']);
        //$rep->TextCol(5, 6, $myrow['leave']);
        //$rep->TextCol(6, 7, $myrow['Over_time_hour']);
        //$rep->AmountCol(7, 8, $myrow['Overtime'], $dec);
        //$rep->AmountCol(8, 9, $myrow['tax'], $dec);
        //$rep->AmountCol(9, 10, $myrow['tax_rate'], $dec);
        //$rep->AmountCol(10, 11, $myrow['late_deduction'], $dec);
        //$rep->AmountCol(11, 12, $myrow['advance_deduction'], $dec);
        //$rep->AmountCol(12, 13, $myrow['allowance'], $dec);
        //$rep->AmountCol(13, 14, $myrow['deduction'], $dec);

		$TotalBasicSalary += $myrow['basic_salary'];
//		$TotalAdvance += $myrow['Advance'];
//		$TotalPresent += $myrow['absent'];
//		$TotalLeave += $myrow['leave'];
		//$TotalAbsent += $myrow['absent'];
//		$Totaldutyhours += $myrow['Over_time_hour'];
//		$TotalOver_Time += $myrow['overtime'];
//		$TotalTax += $myrow['tax'];
//		$TotalTax_rate += $myrow['tax_rate'];
//		$TotalLateDeduction += $myrow['late_deduction'];
//		$TotalAdvDeduction += $myrow['advance_deduction'];
//		$TotalEmpAllowances += $myrow['allowance'];
//		$TotalEmpDeductions += $myrow['deduction'];
		$TotalNetSalary += $NetSalary;
        $GrandNetSalary += $NetSalary;

        //	$GrandBasicSalary += $myrow['basic_salary'];
//		$GrandAdvance += $myrow['Advance'];
//		$GrandPresent += $myrow['absent'];
//		$GrandLeave += $myrow['leave'];
        //$GrandAbsent += $myrow['absent'];
//		$Granddutyhours += $myrow['Over_time_hour'];
//		$GrandOver_Time += $myrow['overtime'];
//		$GrandTax += $myrow['tax'];
//		$GrandTax_rate += $myrow['tax_rate'];
//		$GrandLateDeduction += $myrow['late_deduction'];
//		$GrandAdvDeduction += $myrow['advance_deduction'];
//		$GrandEmpAllowances += $myrow['allowance'];
//		$GrandEmpDeductions += $myrow['deduction'];

		
		
    	$rep->NewLine();

	}

	$rep->Font(b);
		$rep->TextCol(0, 15, _('Department Total'));

		//$rep->AmountCol(2, 3, $TotalBasicSalary, $dec);
		//$rep->AmountCol(3, 4, $TotalAdvance, 0);
	//	$rep->AmountCol(4, 5, $TotalPresent, 0);
	//	$rep->AmountCol(5, 6, $TotalLeave, 0);
	//	$rep->AmountCol(6, 7, $Totaldutyhours, $dec);
	//	$rep->AmountCol(7, 8, $TotalOver_Time, $dec);
	//	$rep->AmountCol(8, 9, $TotalTax, $dec);
	//	$rep->AmountCol(9,10, $TotalTax_rate, $dec);
	//	$rep->AmountCol(10, 11, $TotalLateDeduction, $dec);
	//	$rep->AmountCol(11, 12, $TotalAdvDeduction, $dec);
	//	$rep->AmountCol(12, 13, $TotalEmpAllowances, $dec);
	//	$rep->AmountCol(13, 14, $TotalEmpDeductions, $dec);
		$rep->AmountCol(2, 3, $TotalNetSalary, $dec);
		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalEmpAllowances = $TotalEmpDeductions = $TotalNetSalary = 0;
	$rep->Font();
    	$rep->NewLine(2);
}
}

	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
		//$rep->AmountCol(2, 3, $GrandBasicSalary, $dec);
//		$rep->AmountCol(3, 4, $GrandAdvance, 0);
//		$rep->AmountCol(4, 5, $GrandPresent, 0);
//		$rep->AmountCol(5, 6, $GrandLeave, 0);
//		$rep->AmountCol(6, 7, $Granddutyhours, $dec);
//		$rep->AmountCol(7, 8, $GrandOver_Time, $dec);
//		$rep->AmountCol(8, 9, $GrandTax, $dec);
//		$rep->AmountCol(9, 10, $GrandTax_rate, $dec);
//		$rep->AmountCol(10, 11, $GrandLateDeduction, $dec);
//		$rep->AmountCol(11, 12, $GrandAdvDeduction, $dec);
//		$rep->AmountCol(12, 13, $GrandEmpAllowances, $dec);
//		$rep->AmountCol(13, 14, $GrandEmpDeductions, $dec);
	    $rep->AmountCol(2, 3,	$GrandNetSalary, $dec);
	    $rep->fontSize -= 2;
	    $rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>