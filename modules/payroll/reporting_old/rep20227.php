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

function get_total_no_of_allowed_leaves2()
{
	$sql="SELECT SUM(leave_days) FROM ".TB_PREF."leave_type ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_no_of_leave_availed($emp_id)
{
	$sql="SELECT no_of_leave FROM ".TB_PREF."leave where emp_id='$emp_id' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_encashed_days($employee_id,$f_year)
{
	$sql="SELECT SUM(days) FROM ".TB_PREF."leave_encashment where  approve=1 AND emp_name='$employee_id' AND f_year='$f_year' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_leave_encashed($employee_id,$f_year)
{
	$sql="SELECT SUM(amount) FROM ".TB_PREF."leave_encashment where  approve=1 AND emp_name='$employee_id' AND f_year='$f_year' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_gratuity($employee_id,$f_year)
{
	$sql="SELECT SUM(amount) FROM ".TB_PREF."gratuity where  approve=1 AND emp_name='$employee_id' AND f_year='$f_year' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}

function get_leave_encashment_income_tax($employee_id,$f_year)
{
	$sql="SELECT SUM(income_tax) FROM ".TB_PREF."leave_encashment where  approve=1 AND emp_name='$employee_id' AND f_year='$f_year' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_garuity_income_tax($employee_id,$f_year)
{
	$sql="SELECT SUM(income_tax) FROM ".TB_PREF."gratuity where  approve=1 AND emp_name='$employee_id' AND f_year='$f_year' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_advance_against_salary($employee_id,$f_year,$type)
{
	$sql="SELECT SUM(amount) FROM ".TB_PREF."advance where  approve=1 AND emp_id='$employee_id' AND f_year='$f_year' AND advance_on_base_of=$type ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}

function get_employee_name_leave($employee_id)
{
	$sql = "SELECT emp_code FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_gratuity_leave($id)
{
	$sql = "SELECT amount FROM ".TB_PREF."gratuity WHERE emp_name=".db_escape($id);

	$result = db_query($sql, "could not get gratuity");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_designation_name($id)
{
	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
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

	$employee_id = $_POST['PARAM_0'];

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

	function getTransactions_for_employee_leave($employee)
	{
		$sql = " SELECT * FROM ".TB_PREF."employee where inactive = 0 AND employee_id=".db_escape($employee)."";
		$result=  db_query($sql,"No transactions were returned");
		return db_fetch($result);
//		return $row;
	}
	function get_transactions_leave($employee){


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
$orientation = 'P';
    $rep = new FrontReport(_(''), "SupplierBalances", user_pagesize(), 10, $orientation);

	if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header22');
			//$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();

	$employee_data = getTransactions_for_employee_leave($employee_id);
	$f_year = get_current_fiscalyear();

	$result = get_transactions_leave($employee);
	$desg=get_emp_desg($employee['emp_desig']);
//	$man_month=get_emp_man_month_from_emp($result['emp_id']);
    $date = Today();
	$rep->NewPage();
	//$rep->setfontsize(18);
	$rep->font('b');
	$rep->MultiCell(422.5, 15, "Full & Final Settlement" ,0, 'L', 0, 2, 80,60, true);
	$rep->MultiCell(422.5, 15, "___________________ " ,0, 'L', 0, 2, 80,65, true);
	$rep->font('');
//	//$rep->setfontsize(-16);
//	$rep->setfontsize(12);

//    $rep->MultiCell(170, 25, $employee['emp_name'] ,0, 'L', 0, 2, 180,150, true);
//    $rep->MultiCell(170, 25, $desg['description'] ,0, 'L', 0, 2, 200,135, true);
//    $rep->MultiCell(170, 25, sql2date($employee['j_date']) ,0, 'L', 0, 2, 170,165, true);//joining_date
//    $rep->MultiCell(170, 25, $date ,0, 'L', 0, 2, 170,195, true);//no.of.days
//    $rep->MultiCell(170, 25, $employee['basic_salary'] ,0, 'L', 0, 2, 600,110, true);//salary
//    $total_amount= $result['allowance']+ $result['overtime'];
//    $rep->MultiCell(170, 25,$total_amount ,0, 'L', 0, 2, 600,140, true);
//
//    $total_salary =  $employee['basic_salary'] + $total_amount ;
//    $rep->MultiCell(170, 25, $total_salary ,0, 'L', 0, 2, 600,180, true);//total_salary
//	$rep->MultiCell(170, 25, $result['advance_deduction'] ,0, 'L', 0, 2, 500,250, true);
//	$rep->MultiCell(170, 25, $employee['emp_eobi'] ,0, 'L', 0, 2, 500,275, true);
//


//    $month =date_diff2(Today(), sql2date($employee["j_date"]), "m");
//    $days =date_diff2(Today(), sql2date($employee["j_date"]), "d");
    $rep->font('b');

  $rep->MultiCell(60, 20, "Project" ,0, 'C', 0, 2, 350,100, true);
    $rep->MultiCell(160, 70, "",1, 'C', 0, 2, 400,90, true);
    $rep->MultiCell(150, 60, "",1, 'C', 0, 2, 405,95, true);
    $rep->MultiCell(100, 20, "Administrative Dep.t (OverHead)",0, 'L', 0, 2, 405,95, true);



    $rep->MultiCell(225, 70, "" ,1, 'L', 0, 2, 610,90, true);
    $rep->MultiCell(235, 80, "" ,1, 'L', 0, 2, 605,85, true);

    $rep->MultiCell(200, 25, "Employee Code" ,0, 'L', 0, 2, 82,100, true);
    $rep->MultiCell(170, 25, "".$employee_data['emp_code'] ,0, 'C', 0, 2, 110,100, true);
    $rep->MultiCell(100, 25, "" ,1, 'L', 0, 2, 168,95, true);
    $rep->MultiCell(107, 33, "" ,1, 'L', 0, 2, 165,91, true);



    $rep->MultiCell(170, 25, "Name : " ,0, 'L', 0, 2, 82,150, true);
	$rep->font();
	$rep->SetFontSize(8);
    $rep->MultiCell(360, 25, $employee_data['emp_name'] ,0, 'L', 0, 2, 160,150, true);
	$rep->font('b');
    $rep->MultiCell(360, 25, "____________________________________" ,0, 'L', 0, 2, 150,152, true);


	$rep->MultiCell(170, 25, "Designation : " ,0, 'L', 0, 2, 82,180, true);
	$rep->font();
	$rep->SetFontSize(8);
	$rep->MultiCell(370, 25, get_designation_name($employee_data['emp_desig']),0, 'L', 0, 2, 150,180, true);
	$rep->font('b');
	$rep->MultiCell(370, 25, "____________________________________" ,0, 'L', 0, 2, 150,182, true);

//
//
//
	$rep->MultiCell(170, 25, "Date Of Joining :" ,0, 'L', 0, 2, 82.5,205, true);
	$rep->font();
	$rep->SetFontSize(8);
	$rep->MultiCell(370, 25, sql2date($employee_data['j_date']),0, 'L', 0, 2,152,205, true);
	$rep->font('b');
	$rep->MultiCell(370, 25, "____________" ,0, 'L', 0, 2,150,207, true);
//

	$rep->MultiCell(170, 25, "Termination Date :" ,0, 'L', 0, 2,260,205, true);
	$rep->font();
	$rep->SetFontSize(8);
	$rep->MultiCell(370, 25,  sql2date($employee_data['l_date']) ,0, 'L', 0, 2, 335,205, true);
	$rep->font('b');
	$rep->MultiCell(370, 25, "____________" ,0, 'L', 0, 2, 335,207, true);


	//---------------- TOTAL LEAVE
	$total_no_of_leave = get_total_no_of_allowed_leaves2();
	$total_no_of_leave_availed = get_no_of_leave_availed($employee_data['employee_id']);
	$days_encashed = get_encashed_days($employee_data['employee_id'],$f_year['id']);
	$total_leave_due = $total_no_of_leave - $total_no_of_leave_availed - $days_encashed;

    $rep->MultiCell(170, 25, "Total Leaves Due :" ,0, 'L', 0, 2, 80,270, true);
    $rep->MultiCell(370, 25, $total_leave_due,0, 'L', 0, 2, 175,270, true);
    $rep->MultiCell(370, 25, "_______" ,0, 'L', 0, 2, 172,272, true);


	$rep->MultiCell(170, 25, "Leaves Availed :" ,0, 'L', 0, 2, 80,295, true);
	$rep->MultiCell(370, 25, $total_no_of_leave_availed ,0, 'L', 0, 2, 175,295, true);
	$rep->MultiCell(370, 25, "_______" ,0, 'L', 0, 2, 172,297, true);


	$rep->MultiCell(170, 25, "Leaves Enchased :" ,0, 'L', 0, 2, 80,320, true);
	$rep->MultiCell(370, 25, $days_encashed ,0, 'L', 0, 2, 175,320, true);
	$rep->MultiCell(370, 25, "_______" ,0, 'L', 0, 2, 172,322, true);


    $rep->MultiCell(170, 25, "Balance Leaves Due :" ,0, 'L', 0, 2, 80,340, true);
    $rep->MultiCell(370, 25, $total_leave_due+ $total_no_of_leave_availed +$days_encashed ,0, 'L', 0, 2, 175,340, true);
    $rep->MultiCell(370, 25, "_______" ,0, 'L', 0, 2, 172,342, true);

	$levae_encashed = get_leave_encashed($employee_data['employee_id'],$f_year['id']);
	$get_gratuity = get_gratuity($employee_data['employee_id'],$f_year['id']);

    $rep->MultiCell(170, 25, "Leave Enchasment :" ,0, 'L', 0, 2, 80,400, true);
    $rep->MultiCell(370, 25, $levae_encashed ,0, 'L', 0, 2, 400,400, true);
    $rep->MultiCell(370, 25, "__________________" ,0, 'L', 0, 2, 400,402, true);

    $rep->MultiCell(370, 25, "Gratuity :" ,0, 'L', 0, 2, 80,420, true);
    $rep->MultiCell(370, 25, $get_gratuity ,0, 'L', 0, 2, 400,420, true);
    $rep->MultiCell(370, 25, "__________________" ,0, 'L', 0, 2, 400,422, true);


    $rep->MultiCell(370, 25, "Salary For :" ,0, 'L', 0, 2, 80,440, true);
    $rep->MultiCell(370, 25, "__________________" ,0, 'L', 0, 2, 400,440, true);


    $rep->MultiCell(370, 25, "Salary in Lieu of notice period :" ,0, 'L', 0, 2, 80,460, true);
    $rep->MultiCell(370, 25, "__________________" ,0, 'L', 0, 2, 400,460, true);
    $rep->MultiCell(370, 25, "Total Rs" ,0, 'L', 0, 2, 80,480, true);
    //$rep->MultiCell(370, 25, "_________________________________________" ,0, 'L', 0, 2, 82,340, true);
    $rep->Line($rep->row -385, 1);

    $rep->font('');


	$leave_encashment_income_tax = get_leave_encashment_income_tax($employee_data['employee_id'],$f_year['id']);
	$gratuity_income_tax = get_garuity_income_tax($employee_data['employee_id'],$f_year['id']);
	$tot= $leave_encashment_income_tax+ $gratuity_income_tax;

    $rep->MultiCell(170, 25, "Less Income Tax" ,0, 'L', 0, 2, 220,540, true);
    $rep->MultiCell(370, 25, $tot ,0, 'L', 0, 2, 400,540, true);
    $rep->MultiCell(370, 25, "___________________      " ,0, 'L', 0, 2, 400,542, true);

	$get_advance_against_salary = get_advance_against_salary($employee_data['employee_id'],$f_year['id'],2);
	$get_advance_against_gratuity = get_advance_against_salary($employee_data['employee_id'],$f_year['id'],1);

    $rep->MultiCell(170, 25, "Less Advance Against Salary" ,0, 'L', 0, 2,220,560, true);
    $rep->MultiCell(370, 25, $get_advance_against_salary ,0, 'L', 0, 2, 400,560, true);
    $rep->MultiCell(370, 25, "____________________ " ,0, 'L', 0, 2, 400,562, true);



    $rep->MultiCell(170, 25, "Less Advance Against Gratuity" ,0, 'L', 0, 2,220,580, true);
    $rep->MultiCell(370, 25, $get_advance_against_gratuity ,0, 'L', 0, 2, 400,580, true);
    $rep->MultiCell(370, 25, "____________________" ,0, 'L', 0, 2, 400,580, true);

	$rep->MultiCell(170, 25, "Deduction Against" ,0, 'L', 0, 2,220,600, true);
	$rep->MultiCell(370, 25, "_ _ _ _ _ _ ____________________" ,0, 'L', 0, 2, 400,600, true);
	$rep->font('b');
	$rep->MultiCell(170, 25, "Total Deduction" ,0, 'L', 0, 2,220,620, true);
	$rep->MultiCell(370, 25, "____________________" ,0, 'L', 0, 2, 400,620, true);

	$rep->font('');
   /* $rep->MultiCell(170, 25, "Add Field Expense" ,0, 'L', 0, 2,220,640, true);
    $rep->MultiCell(370, 25, "___________________________      " ,0, 'L', 0, 2, 400,640, true);

    $rep->MultiCell(170, 25, "Add Field Expense in lieu of Notice Period " ,0, 'L', 0, 2,220,660, true);
    $rep->MultiCell(370, 25, "___________________________" ,0, 'L', 0, 2, 400,660, true);*/


    $rep->MultiCell(170, 25, "Net Payable" ,0, 'L', 0, 2,220,690, true);
    $rep->MultiCell(170, 25, "  " ,1, 'L', 0, 2, 400,690, true);

//    $rep->MultiCell(170, 25, "Total Gratuity" ,0, 'L', 0, 2, 80,315, true);

//    $grauity= get_gratuity_leave( $employee['employee_id']);
//    $rep->MultiCell(170, 25,  $grauity ."  /  ". 365 ."  *  " .$total_salary,0, 'L', 0, 2, 250,315, true);

    //$total_grauity = $grauity /365 * $total_salary;
    //$rep->MultiCell(170, 25,  number_format2($total_grauity) ,0, 'L', 0, 2, 410,315, true);


    //$rep->MultiCell(170, 25, "Gratuity Drawn" ,0, 'L', 0, 2, 80,340, true);



//    $rep->MultiCell(370, 25, "_______________________________________________________" ,0, 'L', 0, 2, 80,350, true);
//    $rep->MultiCell(170, 25, "Balance Gratuity" ,0, 'L', 0, 2, 80,367, true);
//    $rep->MultiCell(370, 25, "_______________________________________________________" ,0, 'L', 0, 2, 80,375, true);


//   $rep->MultiCell(600, 25, "_______________________________" ,0, 'L', 0, 2, 450,555, true);
//    $rep->MultiCell(170, 25, "Net Payable" ,0, 'L', 0, 2, 450,572, true);
//    $rep->MultiCell(600, 25, "________________________________" ,0, 'L', 0, 2, 450,577, true);

	$months = date_diff2(Today(),sql2date($employee_data['j_date']),"m");
	$days = date_diff2(Today(),sql2date($employee_data['j_date']),"d");

	$rep->font('b');
    $rep->MultiCell(170, 25, "Total Services :" ,0, 'L', 0, 2, 82.5,240, true);
	$rep->font();
	$rep->SetFontSize(8);
    $rep->MultiCell(370, 25, $months ." Months" ,0, 'L', 0, 2, 145,240, true);
    $rep->MultiCell(370, 25, "__________" ,0, 'L', 0, 2, 145,242, true);
//    $rep->MultiCell(170, 25, "Months" ,0, 'L', 0, 2, 250,240, true);

    $rep->MultiCell(370, 25, $days ." Days",0, 'L', 0, 2, 215,240, true);
    $rep->MultiCell(370, 25, "_________" ,0, 'L', 0, 2, 215,242, true);
//    $rep->MultiCell(170, 25, "Days" ,0, 'L', 0, 2, 370,240, true);

  //  $rep->setfontsize(18);
   // $rep->fontSize += 12;
   // $rep->MultiCell(370, 25, "Gratuity" ,0, 'L', 0, 2, 80,280, true);
    //$rep->MultiCell(370, 25, "_______" ,0, 'L', 0, 2, 80,283, true);

  //$rep->MultiCell(370, 25, "Leave Enchashment" ,0, 'L', 0, 2, 80,400, true);
    //$rep->MultiCell(370, 25, "__________________" ,0, 'L', 0, 2, 80,405, true);
  //  $rep->fontSize -= 12;

    $rep->font('');



//	$rep->MultiCell(170, 25, "In words" ,0, 'L', 0, 2, 82.5,355, true);
//	$rep->MultiCell(370, 25, "_____________________________" ,0, 'L', 0, 2, 170,355, true);


	//-----right side---------------
//    $rep->setfontsize(12);



//
//	$rep->MultiCell(170, 25, "Mode Of Payment" ,0, 'L', 0, 2,82,410, true);
//	$rep->MultiCell(200, 25, "" ,1, 'L', 0, 2,182,407, true);
//
//	$rep->MultiCell(130, 25, "  Manager " ,1, 'L', 0, 2,82,437, true);
//	$rep->MultiCell(130, 25, "  Account" ,1, 'L', 0, 2,212,437, true);
//	$rep->MultiCell(350, 25, "  Receiver Signature & date" ,1, 'L', 0, 2,342,437, true);
//	//-----end----------------------

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