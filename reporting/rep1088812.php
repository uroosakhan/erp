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
function get_employee_qualification($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."man_qualification WHERE employee_id=".db_escape($selected_id);

	$result = db_query($sql,"could not get employee qualification ");
	return $result;
}
function get_employee_history_108812($employee_history)
{
	$sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_id = ".db_escape($employee_history);
	$result = db_query($sql, "Error");
	return $result;
}

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$employee = $_POST['PARAM_0'];
	$comments = $_POST['PARAM_1'];
	$orientation = $_POST['PARAM_2'];
	$destination = $_POST['PARAM_3'];
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

	$cols = array(0, 20, 200,250, 290,	350,400,450, 710, 950);

	$headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

	$aligns = array('centre',	'centre',	'centre',	'centre',	'centre',	'centre',	'centre',	'centre',	'centre');

	$params =   array( 	0 => $comments,
		1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
		//2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

	);

	$rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);


	$rep->SetHeaderType('Header1088812');
	$rep->Info($params, $cols, null, $aligns);
//	$rep->NewPage();



	if($employee == -1 || $employee == "" )
		$sql = " SELECT * from 0_employee ";

	else
		$sql = "SELECT * from 0_employee where employee_id =".$employee;
	   $result=db_query($sql,"could not get employee");

//	$employee_info = get_employee_info($employee);

	while($myrow = db_fetch($result)) {
		$qualification=get_employee_qualification($myrow['employee_id']);
		$department=get_employee_dept($myrow['emp_dept']);
		$history=get_employee_history_108812($myrow['employee_id']);

		if($myrow) {
//


			$rep->NewPage();
			//$logo = company_path() . "/images/".$myrow['employee_id'].".jpg";

			//$rep->AddImage($logo, 210, $rep->row + 140, 120, 77);


	$logo = company_path() . "/images/".$myrow['employee_id'].".jpg";
	if (file_exists($logo))
	{
		$rep->AddImage($logo, 210, $rep->row + 140, 120, 77);
	}
			$rep->MultiCell(190, 650, "", 1, 'L', 1, 2, 1, 100, true);
			$rep->SetFontSize(17);
			//$rep->SetTextColor(255,255,255);
			$rep->MultiCell(240, 150, "" . $myrow['emp_name'], 0, 'L', 0, 2, 30, 30, true);
			$rep->SetFontSize(8);
			$rep->MultiCell(150, 150, "" . get_designation_name_new($myrow['emp_desig']), 0, 'L', 0, 2, 30, 49, true);
			$rep->SetFontSize(8);
//			$rep->MultiCell(90, 50, "Email:", 0, 'L', 1, 2, 20, 120, true);

			$rep->SetTextColor(255, 255, 255);
//			$rep->MultiCell(90, 50, "" . $myrow['emp_email'], 0, 'L', 1, 2, 60, 120, true);
			$rep->SetFontSize(12);
			$rep->MultiCell(170, 50, "Contact ", 0, 'L', 1, 2, 20, 120, true);
			$rep->SetFontSize(9);
			$rep->MultiCell(90, 50, "Phone no:", 0, 'L', 1, 2, 20, 150, true);
			$rep->MultiCell(90, 50, "" . $myrow['emp_home_phone'] . "(phone)", 0, 'L', 1, 2, 60, 150, true);
			$rep->MultiCell(90, 50, "Mobile no:", 0, 'L', 1, 2, 20, 170, true);
			$rep->MultiCell(90, 50, "" . $myrow['emp_mobile'] . "(mobile)", 0, 'L', 1, 2, 60, 170, true);
			$rep->MultiCell(90, 50, "Address:", 0, 'L', 1, 2, 20, 190, true);
			$rep->MultiCell(90, 50, "" . $myrow['emp_address'], 0, 'L', 1, 2, 60, 190, true);
			$rep->SetFontSize(12);
			$rep->MultiCell(170, 50, "Bank Information       ", 0, 'L', 1, 2, 20, 270, true);
			$rep->SetFontSize(9);
			$rep->MultiCell(90, 50, "Bank Info:", 0, 'L', 1, 2, 20, 290, true);
			$rep->MultiCell(90, 50, $myrow['bank_name'], 0, 'L', 1, 2, 75, 290, true);
			$rep->MultiCell(90, 50, "Bank Name:", 0, 'L', 1, 2, 20, 310, true);
			$rep->MultiCell(90, 50, $myrow['bank_name'], 0, 'L', 1, 2, 75, 310, true);
			$rep->MultiCell(90, 50, "Bank Branch:", 0, 'L', 1, 2, 20, 330, true);
			$rep->MultiCell(90, 50, $myrow['bank_branch'], 0, 'L', 1, 2, 75, 330, true);
			$rep->SetFontSize(12);
			$rep->MultiCell(170, 50, "Other Information       ", 0, 'L', 1, 2, 20, 420, true);
			$rep->SetFontSize(9);
			$rep->MultiCell(90, 50, "CNIC:", 0, 'L', 1, 2, 20, 450, true);
			$rep->MultiCell(90, 50, $myrow['emp_cnic'], 0, 'L', 1, 2, 60, 450, true);

			$rep->MultiCell(90, 50, "PEC:", 0, 'L', 1, 2, 20, 475, true);
			$rep->MultiCell(90, 50, $myrow['pec_no'], 0, 'L', 1, 2, 60, 475, true);
			$rep->SetTextColor(0, 0,0 );
			$rep->SetFontSize(10);
			$rep->font('b');
			$rep->MultiCell(170, 50, "Current Salary : ", 0, 'L', 0, 2, 220, 200, true);
			$rep->font('');
			$rep->MultiCell(170, 50, number_format2($myrow['basic_salary'],$dec) , 0, 'L', 0, 2, 310, 200, true);
			$rep->font('b');
			$rep->MultiCell(170, 50, "Report To : ", 0, 'L', 0, 2, 220, 220, true);
			$rep->font('');
			$rep->MultiCell(170, 50, $myrow['report'] , 0, 'L', 0, 2, 310, 220, true);
			$rep->font('b');
			$rep->MultiCell(170, 50, "Department :  ", 0, 'L', 0, 2, 220, 240, true);
			$rep->font('');
			$rep->MultiCell(170, 50, $department , 0, 'L', 0, 2, 310, 240, true);


			$rep->SetFontSize(12);
			$rep->font('b');
			$rep->MultiCell(170, 50, "Education  ", 0, 'L', 0, 2, 220, 280, true);
			$rep->SetFontSize(10);
			$rep->font('');
//			$rep->MultiCell(170, 50,". " .$qualification['degree'], 0, 'L', 0, 2, 220, 230, true);
			$rep->MultiCell(170, 50, "Qualification :", 0, 'L', 0, 2, 220, 295, true);
			while($myrow1=db_fetch($qualification)) {

				$rep->TextCol(2,9,". " . $myrow1['degree'] . "  from " . $myrow1['institute'] . "    in " . $myrow1['passing_percent'] . "%" . "       Passing year(" . $myrow1['passing_year'] . ")");
//				$rep->MultiCell(470, 50, ". " . $myrow1['degree'] . "  from " . $myrow1['institute'] . "    in " . $myrow1['passing_percent'] . "%" . "       (" . $myrow1['passing_year'] . ")", 0, 'L', 0, 2, 220, 320, true);
				$rep->newline(1.4);
			}
//			$rep->MultiCell(170, 50,". " .$qualification['institute']."Institute", 0, 'L', 0, 2, 220, 335, true);
//			$rep->MultiCell(170, 50,". " .$qualification['passing_percent']."%", 0, 'L', 0, 2, 220, 350, true);
//			$rep->MultiCell(170, 50,". " .$qualification['passing_year']."", 0, 'L', 0, 2, 220, 365, true);
			$rep->SetFontSize(12);
			$rep->font('b');
			$rep->MultiCell(170, 50, "Work History ", 0, 'L', 0, 2, 220, 420, true);
			$rep->SetFontSize(10);
			$rep->font('');
			$rep->newline(9);
			while($myrow2=db_fetch($history)) {

				$rep->TextCol(2,9,"Woeked for " .$myrow2['company_name'] ."  as a ".$myrow2['designation']."  from ".$myrow2['date_from']."  To ".$myrow2['date_to']."   ");
//				$rep->newline(1.4);
//				$rep->TextCol(2,9," Designation :" .$myrow2['designation']);
//				$rep->newline(1.4);
//				$rep->TextCol(2,9," Remarks :" .$myrow2['remarks']);
				$rep->newline(2);

			}
			$rep->SetFontSize('b');

			$rep->MultiCell(90, 50, "Current Salary:", 0, 'L', 0, 2, 230, 150, true);
//		$newrow = $rep->row;
//
//		$rep->row = $newrow;
//		if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))

		}
	}



//	$rep->NewLine();
	$rep->End();
}

?>
