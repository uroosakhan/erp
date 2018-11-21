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

function get_emp_allowance_rep($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee_id);
	$result2 = db_query($sql,"could not get group");

	return $result2;
}
function get_allow_name_rep($id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($id);
	$result = db_query($sql,"could not get group");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_emp_history_rep($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_id=".db_escape($employee_id);
	$result3 = db_query($sql,"could not get group");

	return $result3;
}

function get_emp_deduction_rep($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_deduction WHERE emp_id=".db_escape($employee_id);
	$result4 = db_query($sql,"could not get group");

	return $result4;
}
function get_deduct_name_rep($id)
{
	$sql = "SELECT description FROM ".TB_PREF."deduction WHERE id=".db_escape($id);
	$result = db_query($sql,"could not get group");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_emp_qualification_rep($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."man_qualification WHERE employee_id=".db_escape($employee_id);
	$result5 = db_query($sql,"could not get group");

	return $result5;
}

function get_emp_nominee_rep($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_nomination WHERE employee_id=".db_escape($employee_id);
	$result6 = db_query($sql,"could not get group");

	return $result6;
}

function get_emp_desg_rep($id)
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($id);
	$result = db_query($sql,"could not get group");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_grade_name_rep($id)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;
        $from = $_POST['PARAM_0'];
	    $to = $_POST['PARAM_1'];
    	//$approve = $_POST['PARAM_0'];
    	$dept = $_POST['PARAM_2'];
		$employee = $_POST['PARAM_3'];
    	$comments = $_POST['PARAM_4'];
	    $orientation = $_POST['PARAM_5'];
	    $destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

if ($month_name == ALL_TEXT)
		$month = _('All');
	else
		$month = get_month_name($month_name);
	
	if ($dept == ALL_TEXT)
		$dept_name = _('All');
	else
		$dept_name = get_emp_dept_name($dept);	
			
//  if ($employee == ALL_TEXT)
//		$emp = _('All');
//	else
//		$emp = get_employee_name11($employee);
//    	$dec = user_price_dec();


	$cols = array(0, 30, 125, 170, 210, 250, 280, 310, 330, 360, 390, 420, 450, 480, 510, 540, 570, 600);

	$headers = array(_('Emp Code'), _('Emp Name'), _('Age'), _('Grade'), _('Address'), _('Desination'),
		_('Department'), _('Emp Father'), _('CNIC'), _('DOB'), _('Joining Date'), _('Last Date'),
		_('Reference'), _('Home#'), _('Mobile'), _('Email@'), _('Bank A/C'), _('Notes'));

	$aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'right', 'right','right',
		'right', 'right', 'right', 'right', 'right', 'right', 'left');

    $params =   array( 	0 => $comments,
				1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
    			3 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
			);
$orientation = 'L';
    $rep = new FrontReport(_('Monthly Salary Sheet'), "SupplierBalances", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();

    $rep->NewPage();
	$sql = "SELECT * FROM ".TB_PREF."employee ";
	if ($employee != ALL_TEXT)
		$sql .= "WHERE employee_id=".db_escape($employee);
	$sql .= " ORDER BY emp_name";
	$result1 = db_query($sql, "The customers could not be retrieved");


while ($myrow1 = db_fetch($result1))
{
	$rep->NewLine();

	$rep->TextCol(0, 1, $myrow1['emp_code']);
	$rep->TextCol(1, 2, $myrow1['emp_name']);
	$rep->TextCol(2, 3, $myrow1['age']);
	$rep->TextCol(3, 4, get_grade_name_rep($myrow1['emp_grade']));
	$rep->TextCol(4, 5, $myrow1['emp_address']);
	$rep->TextCol(5, 6, get_emp_desg_rep($myrow1['emp_desig']));
	$rep->TextCol(6, 7, get_emp_dept_name($myrow1['emp_dept']));
	$rep->TextCol(7, 8, $myrow1['emp_father']);
	$rep->TextCol(8, 9, $myrow1['emp_cnic']);
	$rep->TextCol(9, 10, $myrow1['DOB']);
	$rep->TextCol(10, 11, $myrow1['j_date']);
	$rep->TextCol(11, 12, $myrow1['l_date']);
	$rep->TextCol(12, 13, $myrow1['emp_reference']);
	$rep->TextCol(13, 14, $myrow1['emp_home_phone']);
	$rep->TextCol(14, 15, $myrow1['emp_mobile']);
	$rep->TextCol(15, 16, $myrow1['emp_email']);
	$rep->TextCol(16, 17, $myrow1['emp_bank']);
	$rep->TextCol(17, 18, $myrow1['notes']);
	$rep->NewLine();

	$result2 = get_emp_allowance_rep($myrow1['employee_id']);//allowance
	$result3 = get_emp_history_rep($myrow1['employee_id']);//history
	$result4 = get_emp_deduction_rep($myrow1['employee_id']);//deduction
	$result5 = get_emp_qualification_rep($myrow1['employee_id']);//qualification
	$result6 = get_emp_nominee_rep($myrow1['employee_id']);//nomination
	$rep->Font('bold');
	$rep->TextCol(1, 2, "ALLOWANCES: ");
	$rep->TextCol(2, 5, "HISTORY: ");
	$rep->TextCol(5, 7, "DEDUCTIONS: ");
	$rep->TextCol(8, 10, "QUALIFICATION: ");
	$rep->TextCol(11, 13, "NOMINATION: ");
	$rep->Font('');


	$rep->NewLine();
}
	/////////////////////////////////

	while ($myrow2 = db_fetch($result2))
	{
		$rep->NewLine();
		$rep->TextCol(1, 2, get_allow_name_rep($myrow2['allow_id'])." ".$myrow2['amount']);
		//$rep->NewLine(-1);
	}
//	$rep->NewLine(-1);
	$rep->Font('bold');

	$rep->Font('');
	while ($myrow3 = db_fetch($result3))
	{
		$rep->NewLine();
		$rep->TextCol(2, 5, $myrow3['company_name']." ".$myrow3['date_from']." ".$myrow3['date_to']);
		//$rep->NewLine(-1);
	}
//	$rep->NewLine(-2);
	$rep->Font('bold');

	$rep->Font('');
	while ($myrow4 = db_fetch($result4))
	{
		$rep->NewLine();
		$rep->TextCol(5, 7, get_deduct_name_rep($myrow4['deduc_id'])." ".$myrow4['amount']);
		//$rep->NewLine(-1);
	}
//	$rep->NewLine(-3);
	$rep->Font('bold');

	$rep->Font('');
	while ($myrow5 = db_fetch($result5))
	{
		$rep->NewLine();
		$rep->TextCol(8, 10, $myrow5['degree']." ".$myrow5['passing_year']." ".$myrow5['institute']." ".$myrow5['passing_percent']);
		//$rep->NewLine(-1);
	}
//	$rep->NewLine(-4);
	$rep->Font('bold');

	$rep->Font('');
	while ($myrow6 = db_fetch($result6))
	{
		$rep->NewLine();
		$lines = $rep->getNumLines($myrow6['nominee_name']);
		$rep->TextCol(11, 13, $myrow6['nominee_name']." ".$myrow6['relation']." ".$myrow6['age']."ansar".$lines);
		//$rep->NewLine(-1);
	}
	////////////////////////////////
	$rep->Font();
	$rep->NewLine();
    $rep->End();
}
