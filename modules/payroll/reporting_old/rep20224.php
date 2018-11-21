<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
include_once($path_to_root . "/payroll/includes/db/dept_db.inc");
include_once($path_to_root . "/payroll/includes/db/desg_db.inc");
include_once($path_to_root . "/payroll/includes/db/project_db.inc");
include_once($path_to_root . "/payroll/includes/db/man_month_db.inc");
//----------------------------------------------------------------------------------------------------

print_employee_balances();


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
	$employee = $_POST['PARAM_0'];
	$orientation = $_POST['PARAM_1'];
	$destination = $_POST['PARAM_2'];


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


	function getTransactions_for_employee($employee)
	{


		$sql = " SELECT * FROM ".TB_PREF."employee where inactive = 0 ";
		if ($employee != ALL_TEXT)
			$sql .= "AND employee_id=".db_escape($employee);

			$result=  db_query($sql,"No transactions were returned");
			$row=db_fetch($result);
			return $row;
	}

	function getTransactions_for_increment($employee)
	{
		$sql = " SELECT * FROM ".TB_PREF."increment where id != 0 ";
		if ($employee != ALL_TEXT)
			$sql .= " AND emp_id = ".db_escape($employee);

		return  db_query($sql,"No transactions were returned");

		//return $TransResult;
	}


//if ($month_name == ALL_TEXT)
//		$month = _('All');
//	else
//		$month = get_month_name($month_name);
//
//	if ($dept == ALL_TEXT)
//		$dept_name = _('All');
//	else
//		$dept_name = get_emp_dept_name($dept);
			
//  if ($employee == ALL_TEXT)
//		$emp = _('All');
//	else
//		$emp = get_employee_name11($employee);
//    	$dec = user_price_dec();




		//$month_name = get_month_name($month);
		
		
	if ($approve) $approval = _('Approve');
	else $approval = _('Unapprove');

	$cols = array(0, 30, 100, 170, 230, 290, 360,440, 540);

	$headers = array(_('ID'), _('Emp. Code'), _('Emp. Name'),  _('Designation'), _('Current salary'),
						_('Increment Date'), _('Increment Amount'), _('Remarks'));

	$aligns = array('left', 'left', 'left', 'left', 'left','left', 'left','left', 'left');

    $params =   array( 	0 => $comments,
				//1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			//1 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
    			//1 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),  
				//3 => array('text' => _('Approval'), 'from' => $approval, 'to' => $to)   			

			);
$orientation = 'L';
    $rep = new FrontReport(_('Employee Increament History'.get_month_name($month)), "SupplierBalances", user_pagesize(), 10, $orientation);

	

	if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
	//$rep->MultiCell(222.5, 15, "Month of ".get_month_name($month) ,0, 'L', 0, 2, 400,30, true);
	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];


   	$rep->NewLine(1);

$result = getTransactions_for_increment($employee);


$sum=0;
	while ($myrow=db_fetch($result))
	{

//		$sum += $myrow['man_month_value'];
		$employee=getTransactions_for_employee($myrow['emp_id']);
		$emp_data = get_employee_data($myrow['emp_id']);

		$desg=get_emp_desg($employee['emp_desig']);


		$rep->TextCol(0, 1, $myrow['id']);
	    $rep->TextCol(1, 2, $myrow['increment_code']);
		$rep->TextCol(2, 3, $emp_data['emp_name']);
		$rep->TextCol(3, 4, $desg['description']);
		$rep->TextCol(4, 5, $emp_data['basic_salary']);
		$rep->TextCol(5, 6, sql2date($employee['j_date']));
		$rep->TextCol(6, 7, $myrow['increament_amount']);
		$rep->TextCol(7, 8, $myrow['remarks']);
		//$rep->TextCol(5, 6, $myrow['DOB']);
		//$rep->TextCol(6	, 7, $project['description']);
		//$rep->TextCol(6	, 7, $project['emp_address']);

		
		
		
		
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
	$rep->Font('b');
	//	$rep->TextCol(0, 1,"Grand Total");
	//	$rep->TextCol(3, 4,$sum);
	$rep->NewLine();
    $rep->End();
}

?>