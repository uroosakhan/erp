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

//	$cols = array(0, 30, 70, 135, 160, 190, 230, 270, 320, 360, 410, 460, 510, 565);
	$cols = array(0, 10, 15, 20, 25,
		30, 35, 40, 45, 50,
		55, 60, 65, 70,75,
		80,85,90,95,100,
		105,110,115,120,125,
		130,135,140,145,150,
		155,160,165,170,175,
		180,185,190,195,200
		,205,210,215,220,225
	,230,235


		);

	$headers = array(_('Divison'), _('Project'), _('Location'), _('Code'), _('Title'),
		_('Employee Full Name'), _('Employee\'s Father Name'), _('Age'), _('Report To'), _('Employee Type'),
		_('Vehicle Provided To Employee'), _('Marital Status'), _('Income Tax Deduction'),
		_('Grautuity applicable'), _('Leave encashment applicable'), _('Sessi applicable'), _('EOBI applicable'), _('Gender'),
		_('Date of Birth'), _('Date of joining'), _('Date of leaving'), _('Reference'), _('Home Phone'),
		_('Mobile'), _('Email'), _('Department'), _('Designation'), _('Grade'),
		_('Employee Bank A/C No'), _('Employee Bank Name'), _('Employee Bank Branch'), _('Mode Of Salary Payment'), _('Company Bank'),
		_('Initial Salary'), _('Previous Salary'), _('Duty Hours'), _('CNIC'), _('CNIC Expiry Date'),
		_('PEC No'), _('PEC Expiry Date'), _('Social Security'), _('NTN'), _('EOBI No'),
			_('License No'), _('License Expiry Date'), _('Physical Address'), _('General Notes')

		);

	$aligns = array('left', 'left', 'left', 'left', 'right',
		'right', 'right', 'right','right', 'right',
		'right', 'right', 'right', 'left', 'right',
		'right', 'right', 'right','right', 'right',
		'right', 'right', 'right', 'left', 'right',
		'right', 'right', 'right','right', 'right',
		'right', 'right', 'right', 'left', 'right',
		'right', 'right', 'right','right', 'right',
		'right', 'right', 'right', 'left', 'right',
		'right', 'right');

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
	function yes_no_name($id)
	{

		if($id == 0)
		{
			$a ="Yes";

		}
		else{
			$a ="No";
		}
		return $a;

	}

	function get_mb_flag_name($id)
	{

		if($id == 'S')
		{
			$a ="Service Provider";

		}
		elseif ($id == 'N')
		{
			$a ="Normal Employment";

		}
		elseif ($id == 'T')
		{
			$a ="Temporary Employment";

		}

		return $a;

	}
	function get_division_name($id,$type)
	{
		$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_=".db_escape($type)." ";
		$result = db_query($sql, "Could't get employee name");
		$myrow = db_fetch($result);
		return $myrow[0];
	}
	function get_emp_project_name($dept_id)
	{
		$sql = "SELECT description FROM ".TB_PREF."project WHERE id= ".db_escape($dept_id);
		$result = db_query($sql, "could not get project");
		$row = db_fetch($result);
		return $row[0];
	}
	function get_location_name1($loc_code)
	{
		$sql = "SELECT location_name FROM ".TB_PREF."locations WHERE loc_code=".db_escape($loc_code);

		$result = db_query($sql, "could not retreive the location name for $loc_code");

		if (db_num_rows($result) == 1)
		{
			$row = db_fetch_row($result);
			return $row[0];
		}

		display_db_error("could not retreive the location name for $loc_code", $sql, true);
	}
	function get_gender($row)
	{
		$sql = "SELECT description FROM ".TB_PREF."gen WHERE id=".db_escape($row['gender']);

		$result = db_query($sql, "could not get sales type");

		$row = db_fetch_row($result);
		return $row[0];
	}


	$result = getTransactions($employee);
	function get_emp_division_name1($dept_id)
	{
		$sql = "SELECT description FROM ".TB_PREF."divison WHERE id = ".db_escape($dept_id);
		$result = db_query($sql, "could not get division");
		$row = db_fetch($result);
		return $row[0];
	}
	function get_emp_title_name($group_no)
	{
		$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
		$result = db_query($sql, "could not get group");
		$row = db_fetch($result);
		return $row[0];
	}
	function get_emp_dept_name1($dept_id)
	{
		$sql = "SELECT description FROM ".TB_PREF."deduction WHERE id = ".db_escape($dept_id);
		$result = db_query($sql, "could not get deduction");
		$row = db_fetch_row($result);
		return $row[0];
	}


	function get_designation_name1($id)
	{
		$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
		$result = db_query($sql, "could not get Designation");
		$row = db_fetch_row($result);
		return $row[0];
	}
	function get_grade_name1($id)
	{
		$sql="SELECT description FROM 0_grade where id=".db_escape($id)." ";
		$result = db_query($sql, "could not get Designation");
		$row = db_fetch_row($result);
		return $row[0];
	}
	function get_bank_account_name1($id)
	{
		$sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_type=".db_escape($id);

		$result = db_query($sql, "could not retreive bank account");

		$row = db_fetch_row($result);

		return $row[0];
	}

	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

		//$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['Tax'] + $myrow['Overtime'] ;
		//$SerialNo += 1;


		$rep->TextCol(0, 1, get_division_name($myrow['division'],1));
		$rep->TextCol(1, 2, get_division_name($myrow['project'],2));
		$rep->TextCol(2, 3,get_division_name( $myrow['location'],3));
		$rep->TextCol(3, 4, $myrow['emp_code']);
		$rep->TextCol(4, 5,get_emp_title_name( $myrow['emp_title']));
		$rep->TextCol(5, 6, $myrow['emp_name']);
		$rep->TextCol(6, 7, $myrow['emp_father']);
		$rep->TextCol(7, 8, $myrow['age']);
		$rep->TextCol(8, 9, $myrow['report']);
		$rep->TextCol(9, 10,get_mb_flag_name( $myrow['mb_flag']));
		$rep->TextCol(10, 11, yes_no_name( $myrow['vehicle']));
		$rep->TextCol(11, 12, yes_no_name($myrow['status']));
		$rep->TextCol(12, 13, yes_no_name($myrow['tax_deduction']));
		$rep->TextCol(13, 14, yes_no_name( $myrow['applicable']));
		$rep->TextCol(14, 15, yes_no_name($myrow['leave_applicable']));
		$rep->TextCol(15, 16, yes_no_name($myrow['sessi_applicable']));
		$rep->TextCol(16, 17, yes_no_name($myrow['eobi_applicable']));
		$rep->TextCol(17, 18,get_gender( $myrow['emp_gen']));
		$rep->TextCol(18, 19,sql2date( $myrow['DOB']));
		$rep->TextCol(19, 20, sql2date($myrow['j_date']));
		$rep->TextCol(20, 21, sql2date($myrow['l_date']));
		$rep->TextCol(21, 22, $myrow['emp_reference']);
		$rep->TextCol(22, 23, $myrow['emp_home_phone']);
		$rep->TextCol(23, 24, $myrow['emp_mobile']);
		$rep->TextCol(24, 25, $myrow['emp_email']);
		$rep->TextCol(25, 26,get_emp_dept_name1( $myrow['emp_dept']));
		$rep->TextCol(26, 27, get_designation_name1($myrow['emp_desig']));
		$rep->TextCol(27, 28, get_grade_name1($myrow['emp_grade']));
		$rep->TextCol(28, 29, $myrow['emp_bank']);
		$rep->TextCol(29, 30, $myrow['bank_name']);
		$rep->TextCol(30, 31, $myrow['bank_branch']);
//		$rep->TextCol(31, 32,get_salary_name( $myrow['salary']));
		$rep->TextCol(32, 33,get_bank_account_name1($myrow['company_bank']));
		$rep->TextCol(33, 34, $myrow['basic_salary']);
		$rep->TextCol(34, 35, $myrow['prev_salary']);
		$rep->TextCol(35, 36, $myrow['duty_hours']);
		$rep->TextCol(36, 37, $myrow['emp_cnic']);
		$rep->TextCol(37, 38, sql2date($myrow['cnic_expiry_date']));
		$rep->TextCol(38, 39, $myrow['pec_no']);
		$rep->TextCol(39, 40, sql2date($myrow['pec_expiry_date']));
		$rep->TextCol(40, 41, $myrow['social_sec']);
		$rep->TextCol(41, 42, $myrow['emp_ntn']);
		$rep->TextCol(42, 43, $myrow['emp_eobi']);
		$rep->TextCol(43, 44, $myrow['license_no']);
		$rep->TextCol(44, 45, sql2date($myrow['license_expiry_date']));
		$rep->TextCol(45, 46, $myrow['emp_address']);
		$rep->TextCol(46, 47, $myrow['notes']);


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