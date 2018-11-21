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

function getTransactions($month, $dept)
{
    $sql = "SELECT  ".TB_PREF."payroll.*, 
    ".TB_PREF."employee.emp_code,
    ".TB_PREF."employee.emp_name, 
    ".TB_PREF."employee.emp_desig,     
    ".TB_PREF."presence.*  
    FROM 
     ".TB_PREF."payroll,  ".TB_PREF."employee,  ".TB_PREF."presence
     WHERE
    ".TB_PREF."payroll.emp_id =  ".TB_PREF."presence.employee_id

	AND 
	".TB_PREF."payroll.month = ".TB_PREF."presence.month_id
	
	AND 
	".TB_PREF."presence.employee_id = ".TB_PREF."employee.employee_id
	AND
	".TB_PREF."payroll.month = ".db_escape($month) ;


		$sql .= "AND ".TB_PREF."payroll.dept_id =".db_escape($dept);
	
    $sql .= "ORDER BY  ".TB_PREF."employee.employee_id ";
	

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
//    			AND ".TB_PREF."employee.company_bank = ".db_escape($banks) ."

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$month = $_POST['PARAM_0'];
    	$dept = $_POST['PARAM_1'];
    	$comments = $_POST['PARAM_2'];
	    $orientation = $_POST['PARAM_3'];
	    $destination = $_POST['PARAM_4'];
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


	
	if ($dept == ALL_TEXT)
		$dept_name = _('All');
	else
		$dept_name = get_emp_dept_name($dept);		



    	$dec = user_price_dec();

		$month_name = get_month_name($month);
		
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 20, 70, 140, 150, 200, 240, 270, 310, 360, 410, 460, 510, 565);

	$headers = array(_('Code'), _('Name'), _('Designation'), _(''), _('Basic'), _('Present'), _('Absent'), _('O/T hours'), _('O/T Amount'), _('Tax'), _('Late Deduct.'), _('Adv. deduction'), _('Net Salary'));

	$aligns = array('left', 'left', 'left', 'left', 'right',  'right', 'right', 'right','right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to),
    			2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to)    			

			);
$orientation = 'L';
    $rep = new FrontReport(_('Monthly Salary Sheet'), "SupplierBalances", user_pagesize(), 10, $orientation);
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

	$sql = "SELECT id, description FROM ".TB_PREF."dept ";
	if ($dept != ALL_TEXT)
		$sql .= "WHERE id=".db_escape($dept);
	$sql .= " ORDER BY description";
	$result1 = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;

	while ($myrow1 = db_fetch($result1))
{
	$result = getTransactions($month, $myrow1['id']);
		if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow1['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();

	while ($myrow=db_fetch($result))
	{
//		if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['overtime'] ;
		$SerialNo += 1;
		$rep->TextCol(0, 1, $myrow['emp_code']);
		$rep->TextCol(1, 2, $myrow['emp_name']);
		$rep->TextCol(2, 3, get_employee_desg($myrow['emp_desig']) );
	//	$rep->TextCol(3, 4, get_employee_dept($myrow['dept_id']));
		$rep->AmountCol(4, 5, $myrow['basic_salary'], $dec);
		$rep->TextCol(5, 6, $myrow['present']);
		$rep->TextCol(6, 7, $myrow['absent']);
		$rep->AmountCol(7, 8, $myrow['over_time'], $dec);
		$rep->AmountCol(8, 9, $myrow['overtime'], $dec);
		$rep->AmountCol(9, 10, $myrow['tax'], $dec);
		$rep->AmountCol(10, 11, $myrow['late_deduction'], $dec);
		$rep->AmountCol(11, 12, $myrow['advance_deduction'], $dec);
		$rep->AmountCol(12, 13, $NetSalary, $dec);

		$TotalBasicSalary += $myrow['basic_salary'];
		$TotalPresent += $myrow['present'];
		$TotalAbsent += $myrow['absent'];
		$TotalOvertime += $myrow['over_time'];
		$TotalOver_Time += $myrow['overtime'];
		$TotalTax += $myrow['tax'];
		$TotalLateDeduction += $myrow['late_deduction'];
		$TotalAdvDeduction += $myrow['advance_deduction'];
		$TotalNetSalary += $NetSalary;

		$GrandBasicSalary += $myrow['basic_salary'];
		$GrandPresent += $myrow['present'];
		$GrandAbsent += $myrow['absent'];
		$GrandOvertime += $myrow['over_time'];
		$GrandOver_Time += $myrow['overtime'];
		$GrandTax += $myrow['tax'];
		$GrandLateDeduction += $myrow['late_deduction'];
		$GrandAdvDeduction += $myrow['advance_deduction'];
		$GrandNetSalary += $NetSalary;

		$GrandNetSalary += $NetSalary;
		
		
    	$rep->NewLine();


	}
	$rep->Font(b);
		$rep->TextCol(0, 15, _('Department Total'));

		$rep->AmountCol(4, 5, $TotalBasicSalary, $dec);
		$rep->AmountCol(5, 6, $TotalPresent, 0);
		$rep->AmountCol(6, 7, $TotalAbsent, 0);
		$rep->AmountCol(7, 8, $TotalOvertime, $dec);
		$rep->AmountCol(8, 9, $TotalOver_Time, $dec);
		$rep->AmountCol(9, 10, $TotalTax, $dec);
		$rep->AmountCol(10, 11, $TotalLateDeduction, $dec);
		$rep->AmountCol(11, 12, $TotalAdvDeduction, $dec);
		$rep->AmountCol(12, 13, $TotalNetSalary, $dec);
		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalNetSalary = 0;
	$rep->Font();
    	$rep->NewLine(2);

}
	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
		$rep->AmountCol(4, 5, $GrandBasicSalary, $dec);
		$rep->AmountCol(5, 6, $GrandPresent, 0);
		$rep->AmountCol(6, 7, $GrandAbsent, 0);
		$rep->AmountCol(7, 8, $GrandOvertime, $dec);
		$rep->AmountCol(8, 9, $GrandOver_Time, $dec);
		$rep->AmountCol(9, 10, $GrandTax, $dec);
		$rep->AmountCol(10, 11, $GrandLateDeduction, $dec);
		$rep->AmountCol(11, 12, $GrandAdvDeduction, $dec);
	$rep->AmountCol(12, 13,	$GrandNetSalary, $dec);	
	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>