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
function get_man($employee_id)
{
    $sql = "SELECT man_month_value FROM ".TB_PREF."man_month WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
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

	$cols = array(10, 50, 150, 450, 520);

	$headers = array(_('Code'), _('Employee Name'), _('Net Salary'), _('Account No'), _('Remarks'));

	$aligns = array('left', 'left', 'left', 'left', 'left');

//    $params =   array( 	0 => $comments,
//    			1 => array('text' => _('Divison'), 'from' => $divison, 'to' => $to),
//    			2 => array('text' => _('Project'), 'from' => $project, 'to' => $to),
//    			3 => array('text' => _('Location'), 'from' => $location, 'to' => $to),
//				4 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
//				5 => array('text' => _('Employee'), 'from' => $month, 'to' => $to)

//			);
$orientation = 'L';
    $rep = new FrontReport(_('                                                 PayOrder/Demand Draft List For The Month Of June 2017'), "ManMonthReport", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header19');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();
    $rep->MultiCell(500, 50, "Kindly following amounts in names and account of our staff member maintaing in your askari bank ltd and debit the total amount rupees        ".$Totalnet_salary."        so to remitted techno consultant pvt ltd  A/C No        ".$acc['bank_account_number']."            in your bank: ", 0, 'L', 0, 2, 150, 100, true);

//	$bank_account_details = get_bank_account($banks);
//	$bank_account_no = $bank_account_details['bank_account_number'];
//	$bank_address = $bank_account_details['bank_address'];


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

//	$rep->fontSize += 2;
//    	$rep->Font();
//	//$rep->TextCol(0, 5, $myrow2['description']);
//    	$rep->Font();
//	$rep->fontSize -= 2;
//    	$rep->NewLine();
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


    function get_payroll2($employee_id)
    {
        $sql = "SELECT *,".TB_PREF."employee.emp_desig FROM ".TB_PREF."payroll,".TB_PREF."employee
       
         WHERE ".TB_PREF."employee.employee_id = ".TB_PREF."payroll.emp_id
        
        
        AND ".TB_PREF."payroll.emp_id=".db_escape($employee_id);

        $result = db_query($sql, "could not get supplier");

//        $fetch =db_fetch($result);
        return $result;
    }

    function get_employee_name15 ($employee_id)
    {
        $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

        $result = db_query($sql, "could not get supplier");

        $fetch =db_fetch($result);
        return $fetch;
    }
    function get_ac_num($acc_id)
    {
        $sql = "SELECT ".TB_PREF."bank_accounts.bank_account_number FROM ".TB_PREF."employee,".TB_PREF."bank_accounts
       
         WHERE ".TB_PREF."bank_accounts.id = ".TB_PREF."employee.company_bank
        
        
        AND ".TB_PREF."employee.employee_id=".db_escape($acc_id);

        $result = db_query($sql, "could not get supplier");

        $fetch =db_fetch($result);
        return $fetch;
    }
    $result = getTransactions($divison,$project,$location,$employee,$month);

    while ($myrow=db_fetch($result))
    {
        //if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

//		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;
        //$SerialNo += 1;
        //$rep->TextCol(0, 1, get_emp_dept_name($myrow['dept_id']));
        $emp = get_payroll2($myrow['emp_id']);
        $empp = get_employee_name15($myrow['emp_id']);
        $acc = get_ac_num($myrow['emp_id']);

        while ($myrow2=db_fetch($emp))
        {
        $rep->TextCol(0, 1, $myrow2['emp_code']);
        $rep->TextCol (1, 2, $empp['emp_name'] );
        $rep->TextCol (2, 3, $myrow2['net_salary']);
        $rep->TextCol(3, 4,$acc['bank_account_number'] );
        $rep->TextCol(4, 5,$myrow2['remarks']);
//        $rep->TextCol(5, 6, sql2date($emp['j_date']));
        $rep->NewLine();
        $rep->TextCol(0, 7, "_____________________________________________________________________________________________________________________________________________");
        // $rep->LineTo($rep->leftMargin, 0 ,$rep->leftMargin, 8);
            $Totalnet_salary += $myrow['net_salary'];


        $rep->NewLine();

    }}
//
//	$rep->Font(b);
//		$rep->TextCol(0, 15, _('Total'));
//
//		$rep->AmountCol(2, 3, $TotalBasicSalary, $dec);
//		$rep->AmountCol(3, 4, $TotalAdvance, 0);
//		$rep->AmountCol(4, 5, $TotalPresent, 0);
//		$rep->AmountCol(5, 6, $TotalLeave, 0);
//		$rep->AmountCol(6, 7, $Totaldutyhours, $dec);
//		$rep->AmountCol(7, 8, $TotalOver_Time, $dec);
//		$rep->AmountCol(8, 9, $TotalTax, $dec);
//		$rep->AmountCol(9,10, $TotalTax_rate, $dec);
//		$rep->AmountCol(10, 11, $TotalLateDeduction, $dec);
//		$rep->AmountCol(11, 12, $TotalAdvDeduction, $dec);
//		$rep->AmountCol(12, 13, $TotalEmpAllowances, $dec);
//		$rep->AmountCol(13, 14, $TotalEmpDeductions, $dec);
//		$rep->AmountCol(14, 15, $TotalNetSalary, $dec);
//		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalEmpAllowances = $TotalEmpDeductions = $TotalNetSalary = 0;
//	$rep->Font();
//    	$rep->NewLine(2);
////}
////}
//
//	$rep->Line($rep->row  - 4);
    $rep->Font(b);
    $rep->fontSize += 2;
    $rep->TextCol(0, 7, " _____________________________________________________________________________________________________________________");

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));

    $rep->AmountCol(2, 3, $Totalnet_salary, $dec);
    $rep->TextCol(0, 7, " _____________________________________________________________________________________________________________________");

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
//	$rep->AmountCol(14, 15,	$GrandNetSalary, $dec);
	$rep->fontSize -= 2;
	$rep->Font();
    $words = price_in_words($Totalnet_salary, ST_SUPPAYMENT);
    if ($words != "")
    {
        $rep->NewLine(2);
        $rep->TextCol(0, 7,'Rupees' . ": " . $words, - 2);
    }
	$rep->NewLine();
    $rep->End();
}

?>