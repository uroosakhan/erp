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

function getTransactions($month_name, $dept,$employee)
{
	$sql = "SELECT ".TB_PREF."payroll.* FROM `".TB_PREF."payroll`,".TB_PREF."payroll_head
WHERE ".TB_PREF."payroll_head.trans_no=".TB_PREF."payroll.`payroll_head`";
if ($month_name != ALL_TEXT)
$sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($month_name);

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name, emp_cnic FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

//	$row = db_fetch_row($result);

	return $result;
}

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$month_name = $_POST['PARAM_0'];
    	$comments = $_POST['PARAM_3'];
	    $orientation = $_POST['PARAM_4'];
	    $destination = $_POST['PARAM_5'];
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
		
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 50, 180, 300, 400, 500);

	$headers = array(_('Sr. No.'), _('Name'), _('CNIC'), _('Tax Rate'), _('Tax Amount'));

	$aligns = array('left', 'left', 'left', 'right', 'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Month'), 'from' => $month, 'to' => $to),
			);
$orientation = 'P';
    $rep = new FrontReport(_('Employee Tax Report'), "EmpTaxReport", user_pagesize(), 10, $orientation);
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


 $sqll = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month_name != ALL_TEXT)
		$sqll .= "WHERE id=".db_escape($month_name);
	$sqll .= " ORDER BY id";
	$result2 = db_query($sqll, "The customers could not be retrieved");

	$sql = "SELECT id, description FROM ".TB_PREF."dept ";
	if ($dept != ALL_TEXT)
		$sql .= "WHERE id=".db_escape($dept);
	$sql .= " ORDER BY description";
	$result1 = db_query($sql, "The customers could not be retrieved");
	
	//$num_lines = 0;
	
while ($myrow2 = db_fetch($result2))
{
	
		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow2['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
$result = getTransactions($myrow2['id'],$dept,$employee);

//	while ($myrow1 = db_fetch($result1))
{
	
		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	$rep->TextCol(0, 5, $myrow1['description']);			
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
$result = getTransactions($myrow2['id'],$myrow1['id'],$employee);
	
	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;

		if($myrow['tax'] == 0) continue;

		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;
		$SerialNo += 1;
		$emp_details = db_fetch(get_employee_name11($myrow['emp_id']));
		$rep->TextCol(0, 1, $SerialNo, -2);
		$rep->TextCol(1, 2, $emp_details['emp_name'], -2);
		$rep->TextCol(2, 3, $emp_details['emp_cnic'], -2);
		$rep->AmountCol(3, 4, $myrow['tax_rate'], $dec);
		$rep->AmountCol(4, 5, $myrow['tax'], $dec);

		$TotalTax += $myrow['tax'];
		$TotalTax_rate += $myrow['tax_rate'];

		$GrandTax += $myrow['tax'];
		$GrandTax_rate += $myrow['tax_rate'];
		
		
    	$rep->NewLine();

	}


		$TotalTax = 0;
    	$rep->NewLine(2);
}
}

	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
		$rep->AmountCol(4, 5, $GrandTax, $dec);

	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>