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
	$sql = " SELECT ".TB_PREF."employee.*

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


function get_desg($id)
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}



function get_nomini($id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_nomination,".TB_PREF."employee WHERE 
	 ".TB_PREF."employee_nomination.employee_id=".TB_PREF."employee.employee_id
	 AND ".TB_PREF."employee_nomination.employee_id=".db_escape($id)."";

	return $result = db_query($sql, "could not get supplier");

	//$row = db_fetch($result);

//	return $row;
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



//	$headers = array(_("Nominee Name"), _("Age"), _("Share"),
//		_("Relation"), _("Remarks"));

	$cols = array(20, 150, 250,300, 450);

	$aligns = array('left',	'left',	'left',	'left',	'left');

	$params =   array( 	0 => $comments,
		1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
		//2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

	);

	$rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);


	$rep->Info($params, $cols, $headers, $aligns,null,$headers,null);
	$rep->SetCommonData('', '', '', '', 11, '');
	$rep->SetHeaderType('Header10');

//	$sql="SELECT * FROM ".TB_PREF."employee ";
//	if ($employee != '')
//		$sql .= " where employee_id=".db_escape($employee);
//	$result = db_fetch($sql, "Error");

//	$rep->Font();




//	$rep->Font('b');
//	$rep->TextCol(0, 3, $letter_date);
//	$rep->NewLine();
//	$rep->TextColLines(0, 3, $bank_address);
//	$rep->Font();
//
//	$rep->NewLine();
//	$rep->NewLine();
//
//	$rep->Font('b');
//	$rep->NewLine();
//	$rep->Font();
//
//	$rep->NewLine(2);
//	$rep->NewLine(1);
	//header
//	$rep->MultiCell(545, 70, "" ,1, 'L', 0, 2, 25,90, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 25,165, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 395,165, true);
//	$rep->MultiCell(545, 100, "" ,1, 'L', 0, 2, 25,465, true);



	
	
	$sql = "SELECT * FROM ".TB_PREF."employee ";
	if ($employee != '')
		$sql .= "WHERE employee_id=".db_escape($employee);
	//if ($dept != ALL_TEXT)
	//	$sql .= "AND emp_dept=".db_escape($dept);
	$result = db_query($sql, "The customers could not be retrieved");


//	for($i = 0; $i < 2 ; $i++){


//
//


//	}
//	$emp_earn = get_employee_salary($employee);
//	$emp_allownace = get_employee_allowance($employee);
//	$emp_tax = get_employee_tax($employee);
//	$earn = $emp_earn + $emp_allownace;
	while ($myrow=db_fetch($result)) {

//			$resultt = getTransactions($month,$dept,$employee);

		$rep->NewPage();

		$rep->MultiCell(50, 50, "Name: ", 0, 'L', 0, 2, 40, 90, true);

		$rep->MultiCell(400, 650, "___________________________________", 0, 'L', 0, 2, 92, 95, true);


		$rep->MultiCell(100, 50, "Father Name: ", 0, 'L', 0, 2, 290, 90, true);
		$rep->MultiCell(400, 50, "___________________________________ ", 0, 'L', 0, 2, 360, 95, true);

		$rep->MultiCell(100, 50, "Designation: ", 0, 'L', 0, 2, 40, 130, true);
		$rep->MultiCell(500, 50, "____________________________________", 0, 'L', 0, 2, 92, 135, true);

		$rep->MultiCell(100, 50, "File No: ", 0, 'L', 0, 2, 290, 130, true);
		$rep->MultiCell(200, 50, "____________________________________", 0, 'L', 0, 2, 360, 135, true);


		$rep->MultiCell(400, 50, "I hersby nominate the person/persons mention below who is/are member/members of my ", 0, 'L', 0, 2, 40, 160, true);

		$rep->MultiCell(400, 50, "family to receive the leave encashment gratuty any amount due to me and the sum issues ", 0, 'L', 0, 2, 40, 180, true);

		$rep->MultiCell(400, 50, "(group insurance/accidential insurance in the event of my death during service): ", 0, 'L', 0, 2, 40, 200, true);


//		$rep->multicell(570,10,"         nominee name                             age                            share                                       relation                               Remarks",1,'L',0,0,25,200,false);

//		$rep->multicell(570,65,"                   ",1,'L',0,0,25,255,true);
		$rep->MultiCell(400, 50, "certified that the member of my family mentioned above reside with me and are wholy", 0, 'L', 0, 2, 40, 335, true);
		$rep->MultiCell(100, 50, "depend upon me", 0, 'L', 0, 2, 40, 355, true);
		$rep->MultiCell(400, 50, "earlier nomination made by me may kindly be treated as cancelled", 0, 'L', 0, 2, 40, 380, true);

		$rep->MultiCell(50, 50, "Date: ", 0, 'L', 0, 2, 40, 410, true);
		$rep->MultiCell(550, 50, "__________________________________________ ", 0, 'L', 0, 2, 75, 415, true);
		$rep->MultiCell(550, 50, "__________________________________________ ", 0, 'L', 0, 2, 360, 415, true);


		$rep->MultiCell(150, 50, "Signature/thumb impression ", 0, 'L', 0, 2, 390, 430, true);
		$rep->MultiCell(150, 50, "The Employee", 0, 'L', 0, 2, 430, 440, true);


		$rep->MultiCell(150, 50, "Witness ", 0, 'L', 0, 2, 40, 400, true);

		$rep->MultiCell(150, 50, "-1: ", 0, 'L', 0, 2, 40, 570, true);
		$rep->MultiCell(550, 50, " __________________________________________", 0, 'L', 0, 2, 75, 590, true);
		$rep->MultiCell(150, 50, "Signature/Thumb Impression", 0, 'L', 0, 2, 80, 610, true);

		$rep->MultiCell(150, 50, "-2: ", 0, 'L', 0, 2, 330, 570, true);
		$rep->MultiCell(550, 50, " __________________________________________", 0, 'L', 0, 2, 360, 590, true);
		$rep->MultiCell(150, 50, "Signature/Thumb Impression", 0, 'L', 0, 2, 400, 610, true);

		$rep->MultiCell(550, 50, " __________________________________________", 0, 'L', 0, 2, 75, 710, true);
		$rep->MultiCell(150, 50, "Name & Designation", 0, 'L', 0, 2, 110, 730, true);

		$rep->MultiCell(550, 50, " __________________________________________", 0, 'L', 0, 2, 360, 710, true);
		$rep->MultiCell(150, 50, "Name & Designation", 0, 'L', 0, 2, 400, 730, true);



//		while ($myrow = db_fetch($resultt)) {
//			$rep->NewPage();


//========================
			$rep->MultiCell(400, 50, " " . $myrow['emp_name'], 0, 'L', 0, 2, 90, 85, true);
			$rep->MultiCell(400, 50, " " . $myrow['emp_father'], 0, 'L', 0, 2, 360, 85, true);
			$rep->MultiCell(500, 50, " " . get_desg($myrow['emp_desig']), 0, 'L', 0, 2, 90, 125, true);
			$rep->MultiCell(200, 50, " " . $myrow['emp_code'], 0, 'L', 0, 2, 360, 125, true);

			$rep->MultiCell(550, 50, " " . $myrow['j_date'], 0, 'L', 0, 2, 75, 410, true);

			$rep->NewLine(-4);


			$nominee = get_nomini($employee);
			while ($myrow1 = db_fetch($nominee)) {
				$rep->TextCol(0, 1, $myrow1['nominee_name'], -2);
				$rep->TextCol(1, 2, $myrow1['age'], -2);
				$rep->TextCol(2, 3, $myrow1['share'], -2);
				$rep->TextCol(3, 4, $myrow1['relation'], -2);
				$rep->TextCol(4, 5, $myrow1['remarks'], -2);
				$rep->NewLine();

			}
//		}
	}
//	$rep->NewLine(-14);
//	$rep->Font('bold');
//
//	$rep->Font('');
//	$rep->NewLine(+14);
//
//	$rep->Font('bold');
//	$rep->NewLine(5);
//	$rep->fontSize += 2;
//	$rep->NewLine(-5);
//	$rep->Font('');
//
//	$rep->NewLine();
	$rep->End();
}

?>

