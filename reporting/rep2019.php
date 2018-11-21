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

function getTransactions($employee)
{
	$sql = "SELECT * FROM ".TB_PREF."employee ";
	if ($employee != ALL_TEXT)
		$sql .= "WHERE employee_id=".db_escape($employee);
	
  return  db_query($sql,"No transactions were returned");

    //return $TransResult;
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
        //$from = $_POST['PARAM_0'];
	    //$to = $_POST['PARAM_1'];
    	//$approve = $_POST['PARAM_2'];
    	//$dept = $_POST['PARAM_2'];
		$employee = $_POST['PARAM_0'];
    	$comments = $_POST['PARAM_1'];
	    $orientation = $_POST['PARAM_2'];
	    $destination = $_POST['PARAM_3'];
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
		
		
	if ($approve) $approval = _('Approve');
	else $approval = _('Unapprove');

	$cols = array(0, 30, 70, 135, 160, 190, 230, 270, 320, 360, 410, 460, 510, 565);

	$headers = array(_('Code'), _('Name'), _('Department'), _('CNIC'), _('Join Date'), _('Last Date'), _('Reference'), _('Home#'), _('Mobile'), _('Email'), _('Bank A/C'), _('Address'), _('Notes'));

	$aligns = array('left', 'left', 'left', 'left', 'right',  'right', 'right', 'right','right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
				//1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			//1 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
    			1 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),  
				//3 => array('text' => _('Approval'), 'from' => $approval, 'to' => $to)   			

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


 /*$sqll = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month_name != ALL_TEXT)
		$sqll .= "WHERE id=".db_escape($month_name);
	$sqll .= " ORDER BY id";
	$result2 = db_query($sqll, "The customers could not be retrieved");
*/
	/*$sql = "SELECT employee_id, emp_name,emp_dept FROM ".TB_PREF."employee ";
	if ($employee != ALL_TEXT)
		$sql .= "WHERE employee_id=".db_escape($employee);
	//$sql .= " ORDER BY emp_name";
	$result1 = db_query($sql, "The customers could not be retrieved");*/
	
	//$num_lines = 0;
	
/*while ($myrow1 = db_fetch($result1))
{
	
		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow1['emp_name']);			
    	$rep->Font();
	$rep->fontSize -= 2;
	$rep->Font(b);
	$rep->NewLine();
	//$rep->TextCol(2,3, get_emp_dept_name($myrow1['emp_dept']));
    	$rep->Font();*/
//$result = getTransactions($employee);

/*	while ($myrow1 = db_fetch($result1))
{
	
		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow1['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();*/
$result = getTransactions($employee);
	
	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

		//$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['Tax'] + $myrow['Overtime'] ;
		//$SerialNo += 1;
		//$rep->TextCol(0, 1, get_emp_dept_name($myrow['dept_id']));
		//$rep->TextCol(0, 2, get_employee_name11($myrow['emp_id']));
		//$rep->TextCol(2, 3, $myrow['basic_salary'] );
		$rep->TextCol(0, 1, $myrow['emp_code']);
		$rep->TextCol(1,2, $myrow['emp_name']);
	 //$rep->TextCol(2, 3, $myrow['emp_cnic']);
		$rep->TextCol(3, 4, $myrow['emp_cnic']);
		//$rep->TextCol(4, 5, $myrow['DOB']);
		$rep->TextCol(4, 5, $myrow['j_date']);
		$rep->TextCol(5,6, $myrow['l_date']);
		$rep->TextCol(6, 7, $myrow['emp_reference']);
		$rep->TextCol(7, 8, $myrow['emp_home_phone']);
		$rep->TextCol(8, 9, $myrow['emp_mobile']);
		$rep->TextCol(9, 10, $myrow['emp_email']);
		$rep->TextCol(10, 11, $myrow['emp_bank']);
		$rep->TextCol(11, 12, $myrow['emp_address']);	 
		$rep->TextCol(12, 13, $myrow['notes']);
		//$rep->TextCol(6, 7, get_leave_type($myrow['leave_type']));
		//$rep->TextCol(8, 9, $myrow['no_of_leave']);
		//$rep->TextCol(9, 12, $myrow['reason']);
		//$rep->TextCol(9, 10, $myrow['present']);
		//$rep->TextCol(6, 7, $myrow['payment_shedule']);
		/*$rep->TextCol(6, 7, $myrow['Over_time_hour']); 
		$rep->AmountCol(7, 8, $myrow['Overtime'], $dec);
		$rep->AmountCol(8, 9, $myrow['Tax'], $dec);
		$rep->AmountCol(9, 10, $myrow['Tax_rate'], $dec);
		$rep->AmountCol(10, 11, $myrow['late_deduction'], $dec);
		$rep->AmountCol(11, 12, $myrow['advance_deduction'], $dec);
		//$rep->AmountCol(12, 13, $NetSalary, $dec);

		$TotalBasicSalary += $myrow['basic_salary'];
		$TotalAdvance += $myrow['Advance'];
		$TotalPresent += $myrow['absent'];
		$TotalLeave += $myrow['leave'];
		//$TotalAbsent += $myrow['absent'];
		$Totaldutyhours += $myrow['Over_time_hour'];
		$TotalOver_Time += $myrow['overtime'];
		$TotalTax += $myrow['Tax'];
		$TotalTax_rate += $myrow['Tax_rate'];
		$TotalLateDeduction += $myrow['late_deduction'];
		$TotalAdvDeduction += $myrow['advance_deduction'];
		$TotalNetSalary += $NetSalary;

		$GrandBasicSalary += $myrow['basic_salary'];
		$GrandAdvance += $myrow['Advance'];
		$GrandPresent += $myrow['absent'];
		$GrandLeave += $myrow['leave'];
		//$GrandAbsent += $myrow['absent'];
		$Granddutyhours += $myrow['Over_time_hour'];
		$GrandOver_Time += $myrow['overtime'];
		$GrandTax += $myrow['Tax'];
		$GrandTax_rate += $myrow['Tax_rate'];
		$GrandLateDeduction += $myrow['late_deduction'];
		$GrandAdvDeduction += $myrow['advance_deduction'];
		$GrandNetSalary += $NetSalary;

		$GrandNetSalary += $NetSalary;*/
		
		
    	$rep->NewLine();

	}

	/*$rep->Font(b);
		$rep->TextCol(0, 15, _('Department Total'));

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
		$rep->AmountCol(12, 13, $TotalNetSalary, $dec);*/
		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalNetSalary = 0;
	$rep->Font();
    	$rep->NewLine(2);



	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	/*$rep->TextCol(0, 3,	_('Grand Total'));
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
	$rep->AmountCol(12, 13,	$GrandNetSalary, $dec);	*/
	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>