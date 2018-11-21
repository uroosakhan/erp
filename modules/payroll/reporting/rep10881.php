<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

//----------------------------------------------------------------------------------------------------

print_employee_balances();

function getTransactions($month,$dept,$employee)
{
	$sql = "SELECT  ".TB_PREF."employee.*

    			FROM ".TB_PREF."employee
    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee);


	$TransResult = db_query($sql,"No transactions were returned");

	return $TransResult;
}
function get_employee_present($month,$dept,$employee)
{
	$sql = "SELECT present FROM ".TB_PREF."presence WHERE employee_id=".db_escape($employee)."
	AND emp_dept=".db_escape($dept)."
	AND month_id=".db_escape($month);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowance_name($allow_id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($allow_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowances($employee)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");
	return $result;
}
function get_employee_salary($employee)
{
	$sql = "SELECT SUM(basic_salary) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_allowance($employee)
{
	$sql = "SELECT SUM(allowance) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_tax($employee)
{
	$sql = "SELECT SUM(tax) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_earning($employee)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

	$result = db_query($sql, "could not get supplier");
	return $result;
}
function get_department_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_emp_title_($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
function get_location_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='3' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_project_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='2' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_division_($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='1' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_employee_info($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id);
	$result = db_query($sql, "Error");
	return db_fetch($result);
}
function get_designation_name_new($id)
{
	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_grade_name_new($id)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_mode_of_payment($id)
{
	$sql = "SELECT description FROM ".TB_PREF."salary WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_emp_bank_account_name_n($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$month = $_POST['PARAM_0'];
	$dept = $_POST['PARAM_1'];
	$employee = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	//if ($destination)
	//	include_once($path_to_root . "/reporting/includes/excel_report.inc");
	//else


	$orientation = ($orientation ? 'L' : 'P');
	if ($banks == ALL_TEXT)
		$bank = _('All');
	else
		$bank_name1 = get_bank_account($banks);
	$bank_name = $bank_name1['bank_account_name'];

//    	$dec = user_price_dec();
	$dec = 0;

	$month_name = get_month_name($month);


	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 20, 120,250, 290,	350,400,450, 510);

	$headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

	$aligns = array('left',	'left',	'left',	'left',	'right');

	$params =   array( 	0 => $comments,
		1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
		//2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

	);

	$rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->SetHeaderType('Header10881');
	$rep->Info($params, $cols, null, $aligns);

	$rep->Font();

	$rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];

	$rep->Font('b');
	$rep->TextCol(0, 3, $letter_date);
	$rep->NewLine();
	$rep->TextColLines(0, 3, $bank_address);
	$rep->Font();

	$rep->NewLine();
	$rep->NewLine();

	$rep->Font('b');
	$rep->NewLine();
	$rep->Font();

	$rep->NewLine(2);
	$rep->NewLine(1);
	//header
//	$rep->MultiCell(545, 70, "" ,1, 'L', 0, 2, 25,90, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 25,165, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 395,165, true);
//	$rep->MultiCell(545, 100, "" ,1, 'L', 0, 2, 25,465, true);
	//$logo = company_path() . "/images/" . 1;

	//if ($rep->company['coy_logo'] != '' && file_exists($logo))
	//{


	//}
	//$rep->multicell(100,100,"".$logo,1,'C',1,0,40,10,true);
	for($i = 0; $i < 2 ; $i++){

		$rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,110, true);
//		$rep->MultiCell(182.5, 15, "PAYSLIP FOR THE MONTH OF " ,0, 'L', 0, 2, 30,90, true);
//		$rep->MultiCell(220, 30, "Designation: ",0, 'L', 0, 2, 320,120.8, true);
//		$rep->MultiCell(50, 15, "Project: ",0, 'L', 0, 2, 30,140, true);
//		$rep->MultiCell(50, 15, "Employee Code: ",0, 'L', 0, 2, 190,110, true);
//		$rep->MultiCell(50, 15, "Date Of Joining :",0, 'L', 0, 2, 190,136, true);
//		$rep->MultiCell(50, 15, "NTN# ",0, 'L', 0, 2, 320,136, true);
		$rep->Font('times','','16');
		$rep->multicell(545,29," Employee Information",0,'C',1,0,25,172,true);
		$rep->multicell(545,29," ",0,'C',1,0,25,160,true);
		$rep->Font('','','');
		$rep->MultiCell(50, 50, "Division:" ,0, 'L', 0, 2, 30,210, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,212, true);
		$rep->MultiCell(50, 50, "Project:" ,0, 'L', 0, 2, 30,235, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,238, true);
		$rep->MultiCell(50, 50, "Location:" ,0, 'L', 0, 2, 30,258, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,260, true);
		$rep->MultiCell(150, 50, "Code:" ,0, 'L', 0, 2, 30,278, true);
		$rep->MultiCell(600, 650, "______________________",0, 'L', 0, 2, 157,280, true);
		$rep->MultiCell(50, 50, "Title:" ,0, 'L', 0, 2, 30,298, true);
		$rep->MultiCell(550, 50, "______________________" ,0, 'L', 0, 2, 157,300, true);

		$rep->MultiCell(150, 50, "Employee Full Name:" ,0, 'L', 0, 2, 30,318, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,320, true);

		$rep->MultiCell(150, 50, "Employee's Father Name:" ,0, 'L', 0, 2, 30,338, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,340, true);

		$rep->MultiCell(50, 50, "Age:" ,0, 'L', 0, 2, 30,358, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,360, true);

		$rep->MultiCell(200, 50, "Report to:" ,0, 'L', 0, 2, 30,378, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,380, true);


		$rep->MultiCell(200, 50, "Employee Type:" ,0, 'L', 0, 2, 30,398, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,400, true);

		$rep->MultiCell(200, 50, "Vehicle Provided To Employee:" ,0, 'L', 0, 2, 30,418, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,420	, true);


		$rep->MultiCell(70, 50, "Marital Status:" ,0, 'L', 0, 2, 30,438, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,440, true);


		$rep->MultiCell(150, 50, "Income Tax Deduction: " ,0, 'L', 0, 2, 30,458, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,460, true);
		//$rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,425, true);
		$rep->MultiCell(150,50,"Grautuity Applicable:",0,'L',0,2,30,478,true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,480, true);
		$rep->MultiCell(150, 50, "Leave Encashment Applicable:" ,0, 'L', 0, 2, 30,498, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,500, true);
		$rep->MultiCell(150, 50, "Sessi Applicable: " ,0, 'L', 0, 2, 30,518, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,520, true);
		$rep->MultiCell(150, 50, "EOBI Applicable: " ,0, 'L', 0, 2, 30,538, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,540, true);
		$rep->MultiCell(70, 50, "Gender: " ,0, 'L', 0, 2, 30,558, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,560, true);
		$rep->MultiCell(70, 50, "Date Of Birth: " ,0, 'L', 0, 2, 30,578, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,580, true);
		$rep->MultiCell(70, 50, "Date Of Joining: " ,0, 'L', 0, 2, 30,598, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,600, true);
		$rep->MultiCell(70, 50, "Date Of Leaving: " ,0, 'L', 0, 2, 30,618, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,620, true);
		$rep->MultiCell(70, 50, "Reference: " ,0, 'L', 0, 2, 30,638, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,640, true);
		$rep->MultiCell(70, 50, "Home Phone: " ,0, 'L', 0, 2, 30,658, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,660, true);
		$rep->MultiCell(70, 50, "Mobile: " ,0, 'L', 0, 2, 30,678, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,680, true);
		$rep->MultiCell(70, 50, "Email: " ,0, 'L', 0, 2, 30,698, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,700, true);
		$rep->MultiCell(70, 50, "Department: " ,0, 'L', 0, 2, 30,718, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,720, true);
		$rep->MultiCell(70, 50, "Designation: " ,0, 'L', 0, 2, 30,738, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,740, true);
		$rep->MultiCell(70, 50, "Grade: " ,0, 'L', 0, 2, 30,758, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 157,760, true);
		$rep->MultiCell(150, 50, "Employee Bank Account No.:" ,0, 'L', 0, 2, 280,210, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,212, true);
		$rep->MultiCell(150, 50, "Employee Bank Name:" ,0, 'L', 0, 2, 280,230, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,232, true);
		$rep->MultiCell(150, 50, "Employee Bank Branch:" ,0, 'L', 0, 2, 280,250, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,252, true);
		$rep->MultiCell(150, 50, "Mode Of Salary Payment:" ,0, 'L', 0, 2, 280,270, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,272, true);
		$rep->MultiCell(150, 50, "Company Bank:" ,0, 'L', 0, 2, 280,290, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,292, true);
		$rep->MultiCell(150, 50, "Initial Salary:" ,0, 'L', 0, 2, 280,310, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,312, true);
		$rep->MultiCell(150, 50, "Previous Salary:" ,0, 'L', 0, 2, 280,330, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,332, true);
		$rep->MultiCell(150, 50, "Duty Hours:" ,0, 'L', 0, 2, 280,350, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,352, true);
		$rep->MultiCell(150, 50, "CNIC:" ,0, 'L', 0, 2, 280,370, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,372, true);
		$rep->MultiCell(150, 50, "CNIC Expiry Date:" ,0, 'L', 0, 2, 280,390, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,392, true);
		$rep->MultiCell(150, 50, "PEC No.:" ,0, 'L', 0, 2, 280,410, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,412, true);
		$rep->MultiCell(150, 50, "PEC Expiry Date:" ,0, 'L', 0, 2, 280,430, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,432, true);
		$rep->MultiCell(150, 50, "Social Security:" ,0, 'L', 0, 2, 280,450, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,452, true);
		$rep->MultiCell(150, 50, "NTN:" ,0, 'L', 0, 2, 280,470, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,472, true);
		$rep->MultiCell(150, 50, "EOBI No.:" ,0, 'L', 0, 2, 280,490, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,492, true);
		$rep->MultiCell(150, 50, "Licence No.:" ,0, 'L', 0, 2, 280,510, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,512, true);
		$rep->MultiCell(150, 50, "Licence Expiry Date:" ,0, 'L', 0, 2, 280,530, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,532, true);
		$rep->MultiCell(150, 50, "Physical Address:" ,0, 'L', 0, 2, 280,550, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,552, true);
		$rep->MultiCell(150, 50, "Employee Status:" ,0, 'L', 0, 2, 280,570, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,572, true);
		$rep->MultiCell(150, 50, "General Notes:" ,0, 'L', 0, 2, 280,590, true);
		$rep->MultiCell(550, 650, "______________________" ,0, 'L', 0, 2, 400,592, true);

	}

//		if($myrow['status']==0)
//			$status='Single';
//		elseif($myrow['status']==1)
//			$status='Married';
////		$rep->MultiCell(150, 50, " ".$status ,0, 'L', 0, 2, 380,345, true);
//		/////
////		$rep->MultiCell(250, 50, " ".get_emp_title_($myrow['emp_title']) ,0, 'L', 0, 2, 55,450, true);
////		$rep->MultiCell(250, 50, " ".$myrow['emp_code'] ,0, 'L', 0, 2, 375,450, true);
////		$rep->MultiCell(250, 50, " ".$myrow['report']  ,0, 'L', 0, 2, 75,480, true);
////		$rep->MultiCell(250, 50, " ".get_department_name($myrow['emp_dept'] ) ,0, 'L', 0, 2, 375,480, true);
////		$rep->MultiCell(250, 50, " ".get_location_($myrow['location'] ) ,0, 'L', 0, 2, 90,510, true);
////		$rep->MultiCell(250, 50, " ".$myrow['emp_email'] ,0, 'L', 0, 2, 375,510, true);
////		$rep->MultiCell(250, 50, " ".$myrow['emp_mobile'] ,0, 'L', 0, 2, 90,540, true);
////		$rep->MultiCell(250, 50, " ".$myrow['emp_mobile'] ,0, 'L', 0, 2, 375,540, true);
////		$rep->MultiCell(250, 50, " ".sql2date($myrow['j_date']) ,0, 'L', 0, 2, 90,570, true);
////		$rep->MultiCell(250, 50, " ".number_format($myrow['basic_salary']),0, 'L', 0, 2, 375,570, true);
//


		$logo = company_path() . "/images/".$employee.".jpg";
		$rep->AddImage($logo, 40, 750, 0, 60);
	$employee_info = get_employee_info($employee);
	$rep->MultiCell(550, 650, get_division_($employee_info['division'] ) ,0, 'L', 0, 2, 157,212, true);
	$rep->MultiCell(550, 650,  get_project_($employee_info['project'] ) ,0, 'L', 0, 2, 157,238, true);
	$rep->MultiCell(550, 650,  get_location_($employee_info['location'] ),0, 'L', 0, 2, 157,260, true);
	$rep->MultiCell(600, 650, $employee_info['emp_code'],0, 'L', 0, 2, 157,280, true);
	$rep->MultiCell(550, 50, get_emp_title_($employee_info['emp_title']) ,0, 'L', 0, 2, 157,300, true);
	$rep->MultiCell(550, 650, $employee_info['emp_name'] ,0, 'L', 0, 2, 157,320, true);
	$rep->MultiCell(550, 650, $employee_info['emp_father'] ,0, 'L', 0, 2, 157,340, true);
	$rep->MultiCell(550, 650, $employee_info['age'] ,0, 'L', 0, 2, 157,360, true);
	$rep->MultiCell(550, 650, $employee_info['report'] ,0, 'L', 0, 2, 157,380, true);
	if($employee_info['mb_flag']=='N')
	{
		$emp_type='Normal Employee';
	}
	elseif($employee_info['mb_flag']=='S')
	{
		$emp_type='Service Employee';
	}
	else{
		$emp_type='Temporary Employee';
	}
	$rep->MultiCell(550, 650, $emp_type ,0, 'L', 0, 2, 157,400, true);
	if($employee_info['vehicle']==1)
	$vehcal='Yes';
	else $vehcal='No';
	$rep->MultiCell(550, 650, $vehcal ,0, 'L', 0, 2, 157,420	, true);
		if($employee_info['status']==0)
			$status='Single';
		elseif($employee_info['status']==1)
			$status='Married';
	$rep->MultiCell(550, 650, $status ,0, 'L', 0, 2, 157,440, true);
	if($employee_info['tax_deduction']==1)
		$i_come='Yes';
	else $i_come='No';
	$rep->MultiCell(550, 650, $i_come ,0, 'L', 0, 2, 157,460, true);
	if($employee_info['applicable']==1)
		$applicable='Yes';
	else $applicable='No';
	$rep->MultiCell(550, 650, $applicable ,0, 'L', 0, 2, 157,480, true);

	if($employee_info['leave_applicable']==1)
		$leave_applicable='Yes';
	else $leave_applicable='No';
	$rep->MultiCell(550, 650, $leave_applicable ,0, 'L', 0, 2, 157,500, true);

	if($employee_info['sessi_applicable']==1)
		$sessi_applicable='Yes';
	else $sessi_applicable='No';
	$rep->MultiCell(550, 650, $sessi_applicable ,0, 'L', 0, 2, 157,520, true);

	if($employee_info['eobi_applicable']==1)
		$eobi_applicable='Yes';
	else $eobi_applicable='No';
	$rep->MultiCell(550, 650, $eobi_applicable ,0, 'L', 0, 2, 157,540, true);
	if($employee_info['emp_gen']==4)
		$emp_gen='Male';
	else $emp_gen='Female';
	$rep->MultiCell(550, 650, $emp_gen ,0, 'L', 0, 2, 157,560, true);
	$rep->MultiCell(550, 650, sql2date($employee_info['DOB']) ,0, 'L', 0, 2, 157,580, true);
	$rep->MultiCell(550, 650, sql2date($employee_info['j_date']) ,0, 'L', 0, 2, 157,600, true);
	$rep->MultiCell(550, 650, sql2date($employee_info['l_date']),0, 'L', 0, 2, 157,620, true);
	$rep->MultiCell(550, 650, $employee_info['emp_reference'] ,0, 'L', 0, 2, 157,640, true);
	$rep->MultiCell(550, 650,$employee_info['emp_home_phone'],0, 'L', 0, 2, 157,660, true);
	$rep->MultiCell(550, 650, $employee_info['emp_mobile'] ,0, 'L', 0, 2, 157,680, true);
	$rep->MultiCell(550, 650, $employee_info['emp_email'] ,0, 'L', 0, 2, 157,700, true);
	$rep->MultiCell(550, 650, get_department_name($employee_info['emp_dept'] ) ,0, 'L', 0, 2, 157,720, true);
	$rep->MultiCell(550, 650, get_designation_name_new($employee_info['emp_desig']) ,0, 'L', 0, 2, 157,740, true);
	$rep->MultiCell(550, 650, get_grade_name_new($employee_info['emp_grade']) ,0, 'L', 0, 2, 157,760, true);
	$rep->MultiCell(550, 650, $employee_info['emp_bank'] ,0, 'L', 0, 2, 400,212, true);
	$rep->MultiCell(550, 650, $employee_info['bank_name'] ,0, 'L', 0, 2, 400,232, true);
	$rep->MultiCell(550, 650, $employee_info['bank_branch'] ,0, 'L', 0, 2, 400,252, true);
	$rep->MultiCell(550, 650, get_mode_of_payment($employee_info['salary'])  ,0, 'L', 0, 2, 400,272, true);
	$rep->MultiCell(550, 650, get_emp_bank_account_name_n($employee_info['company_bank']),0, 'L', 0, 2, 400,292, true);
	$rep->MultiCell(550, 650, number_format2($employee_info['basic_salary']) ,0, 'L', 0, 2, 400,312, true);
	$rep->MultiCell(550, 650, number_format2($employee_info['prev_salary']) ,0, 'L', 0, 2, 400,332, true);
	$rep->MultiCell(550, 650, $employee_info['duty_hours'] ,0, 'L', 0, 2, 400,352, true);
	$rep->MultiCell(550, 650, $employee_info['emp_cnic'] ,0, 'L', 0, 2, 400,372, true);
	$rep->MultiCell(550, 650, sql2date($employee_info['cnic_expiry_date'] ) ,0, 'L', 0, 2, 400,392, true);
	$rep->MultiCell(550, 650, $employee_info['pec_no'] ,0, 'L', 0, 2, 400,412, true);
	$rep->MultiCell(550, 650, sql2date($employee_info['pec_expiry_date']) ,0, 'L', 0, 2, 400,432, true);
	$rep->MultiCell(550, 650, $employee_info['social_sec'] ,0, 'L', 0, 2, 400,452, true);
	$rep->MultiCell(550, 650, $employee_info['emp_ntn'] ,0, 'L', 0, 2, 400,472, true);
	$rep->MultiCell(550, 650, $employee_info['emp_eobi']  ,0, 'L', 0, 2, 400,492, true);
//	$rep->MultiCell(550, 650, $employee_info['emp_eobi'] ,0, 'L', 0, 2, 95,238, true);
//	$rep->MultiCell(550, 650, $employee_info['emp_name']  ,0, 'L', 0, 2, 95,260, true);
//	$rep->MultiCell(600, 650,  $employee_info['emp_father']  ,0, 'L', 0, 2, 95,280, true);
//	$rep->MultiCell(550, 50, $employee_info['age']  ,0, 'L', 0, 2, 95,300, true);
//	$rep->MultiCell(550, 650, $employee_info['report'],0, 'L', 0, 2, 95,320, true);
//	$rep->MultiCell(550, 650, $employee_info['report'] ,0, 'L', 0, 2, 95,340, true);
//	$rep->MultiCell(550, 650, $employee_info['DOB'] ,0, 'L', 0, 2, 95,380, true);
//	$rep->MultiCell(550, 650,  $employee_info['j_date'] ,0, 'L', 0, 2, 95,400, true);
//	$rep->MultiCell(550, 650, $employee_info['I_date'] ,0, 'L', 0, 2, 95,420	, true);
	$rep->NewLine();
	$rep->End();
}

?>
