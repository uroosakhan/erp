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
        //$from = $_POST['PARAM_0'];
	    //$to = $_POST['PARAM_1'];
    	//$approve = $_POST['PARAM_2'];
    	//$dept = $_POST['PARAM_2'];
		//$employee = $_POST['PARAM_0'];
    	//$comments = $_POST['PARAM_1'];

	$employee = $_POST['PARAM_0'];

	//$project = $_POST['PARAM_1'];
	//$month = $_POST['PARAM_1'];
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
	function get_transactions($employee){


		$sql = "SELECT * from ".TB_PREF."payroll where emp_id=".db_escape($employee) ;
//		if ($employee != ALL_TEXT)
//			$sql .= "WHERE ".TB_PREF."payroll.emp_id =".db_escape($employee);
		$TransResult = db_query($sql,"No transactions were returned");

		$result= db_fetch($TransResult);
		return $result;
	}

/*
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
	}*/


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

	//$cols = array(0, 30, 100, 170, 220, 270, 310, 360,  390,  470);

//	$headers = array(_('Sr.No'), _('Name'), _('Designation'),  _('DOJ'), _('Current salary'), _('Add Amt'), _('FLD Exp')
//	, _('Net Amt'), _('Increment Date'), _('Increment Amt')
//
//	);

	//$aligns = array('left', 'left', 'left', 'left', 'left','left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
				//1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			//1 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
    			//1 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
				//3 => array('text' => _('Approval'), 'from' => $approval, 'to' => $to)

			);
$orientation = 'L';
    $rep = new FrontReport(_(''), "SupplierBalances", user_pagesize(), 10, $orientation);

	if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header22');
			//$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();

	$result = get_transactions($employee);
	$employee=getTransactions_for_employee($result['emp_id']);
	$desg=get_emp_desg($employee['emp_desig']);
	$man_month=get_emp_man_month_from_emp($result['emp_id']);
	$rep->NewPage();
	$rep->setfontsize(16);
	$rep->font('b');
	$rep->MultiCell(422.5, 15, "Salary Payment Voucher For month of December 2016" ,0, 'L', 0, 2, 200,60.8, true);
	$rep->font('');
	//$rep->setfontsize(-16);
	$rep->setfontsize(12);

	$rep->MultiCell(170, 25, get_employee_name11($result['emp_id']) ,0, 'L', 0, 2, 180,180.8, true);
	$rep->MultiCell(170, 25, $desg['description'] ,0, 'L', 0, 2, 180,200, true);
	$rep->MultiCell(170, 25, $result['basic_salary'] ,0, 'L', 0, 2, 180,226, true);
	$rep->MultiCell(170, 25, $result['allowance'] ,0, 'L', 0, 2, 180,250, true);
	$rep->MultiCell(170, 25, $result['remarks'] ,0, 'L', 0, 2, 180,325, true);
	//$rep->MultiCell(170, 25, $result['basic_salary'] ,0, 'L', 0, 2, 180,345, true);

	$rep->MultiCell(170, 25, $man_month['man_month_value'] ,0, 'L', 0, 2, 500,180, true);
	$rep->MultiCell(170, 25, $result['basic_salary'] * $result['allowance'],0, 'L', 0, 2, 500,200, true);
	$rep->MultiCell(170, 25, $result['tax'] ,0, 'L', 0, 2, 500,225, true);
	$rep->MultiCell(170, 25, $result['advance_deduction'] ,0, 'L', 0, 2, 500,250, true);
	$rep->MultiCell(170, 25, $employee['emp_eobi'] ,0, 'L', 0, 2, 500,275, true);





	$rep->MultiCell(60, 20, "Code" ,1, 'C', 0, 2, 82.5,120.8, true);
	$rep->MultiCell(60, 20, "Project" ,1, 'C', 0, 2, 382.5,120.8, true);
	$rep->MultiCell(170, 25, "Name" ,0, 'L', 0, 2, 82.5,180.8, true);


	$rep->MultiCell(360, 25, "_____________________________" ,0, 'L', 0, 2, 170,180.8, true);

	$rep->MultiCell(170, 25, "Designation" ,0, 'L', 0, 2, 82.5,205, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,205, true);




	$rep->MultiCell(170, 25, "Gross Salary" ,0, 'L', 0, 2, 82.5,230, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,230, true);


	$rep->MultiCell(170, 25, "Special Allow" ,0, 'L', 0, 2, 82.5,255, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,255, true);

	$rep->MultiCell(170, 25, "No of Days" ,0, 'L', 0, 2, 82.5,280, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,280, true);

	$rep->MultiCell(170, 25, "Arrear if any" ,0, 'L', 0, 2, 82.5,305, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,305, true);

	$rep->MultiCell(170, 25, "Reamrks" ,0, 'L', 0, 2, 82.5,330, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,330, true);

	$rep->MultiCell(170, 25, "In words" ,0, 'L', 0, 2, 82.5,355, true);
	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,355, true);


	//-----right side---------------
	$rep->MultiCell(170, 25, "Man Month" ,0, 'L', 0, 2, 420,180.8, true);
	$rep->MultiCell(370, 25, "__________________________      " ,0, 'L', 0, 2, 500,180.8, true);

	$rep->MultiCell(170, 25, "Net Salary" ,0, 'L', 0, 2,420,205, true);
	$rep->MultiCell(370, 25, "___________________________      " ,0, 'L', 0, 2, 500,205, true);

	$rep->MultiCell(170, 25, "Income Tax" ,0, 'L', 0, 2,420,230, true);
	$rep->MultiCell(370, 25, "___________________________      " ,0, 'L', 0, 2, 500,230, true);

	$rep->MultiCell(170, 25, "Less Advance" ,0, 'L', 0, 2,420,255, true);
	$rep->MultiCell(370, 25, "___________________________      " ,0, 'L', 0, 2, 500,255, true);

	$rep->MultiCell(170, 25, "EOBI. Cont" ,0, 'L', 0, 2,420,280, true);
	$rep->MultiCell(370, 25, "___________________________      " ,0, 'L', 0, 2, 500,280, true);


	$rep->MultiCell(170, 25, "Net Payable" ,0, 'L', 0, 2,420,310, true);
	$rep->MultiCell(170, 25, "  " ,1, 'L', 0, 2, 500,310, true);



	$rep->MultiCell(170, 25, "Mode Of Payment" ,0, 'L', 0, 2,82,410, true);
	$rep->MultiCell(200, 25, "" ,1, 'L', 0, 2,182,407, true);

	$rep->MultiCell(130, 25, "  Manager " ,1, 'L', 0, 2,82,437, true);
	$rep->MultiCell(130, 25, "  Account" ,1, 'L', 0, 2,212,437, true);
	$rep->MultiCell(350, 25, "  Receiver Signature & date" ,1, 'L', 0, 2,342,437, true);
	//-----end----------------------

   	$rep->NewLine(1);






	/*while ($myrow=db_fetch($result))
	{

		$employee=getTransactions_for_employee($myrow['employee_id']);

		$desg=get_emp_desg($employee['emp_desig']);


		$rep->TextCol(0, 1, $myrow['id']);
	    $rep->TextCol(1, 2, $myrow['employee_name']);
		$rep->TextCol(2, 3, $desg['description']);
		$rep->TextCol(3, 4, $myrow['man_month_value']);
		$rep->TextCol(4, 5, sql2date($employee['j_date']));
		//$rep->TextCol(5, 6, $myrow['DOB']);
		//$rep->TextCol(6	, 7, $project['description']);
		//$rep->TextCol(6	, 7, $project['emp_address']);

		
		
		
		
    	$rep->NewLine();

	}*/




//	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;

	$rep->fontSize -= 2;
	$rep->Font('b');
	//$rep->TextCol(0, 1,"Grand Total");
	//$rep->TextCol(3, 4,$sum);
	$rep->NewLine();
    $rep->End();
}

?>