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

function getTransactions($payroll_id)
{
	$sql = "SELECT amount,allow_id FROM ".TB_PREF."payroll_allowance
WHERE payroll_id=".db_escape($payroll_id);

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
function getTransactions2($payroll_id)
{
	$sql = "SELECT payroll_id,deduct_id,emp_id,amount FROM ".TB_PREF."payroll_deduction
WHERE payroll_id=".db_escape($payroll_id);

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
function get_employee_details($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);
	$result = db_query($sql, "could not get supplier");
	//$row = db_fetch_row($result);
	return $result;
}

function get_emp_allowance1_name($allow_id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id = ".db_escape($allow_id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
//for salary
function get_emp_total_salary($employee,$month_name)
{
	$sql = "SELECT SUM((basic_salary+allowance)-(deduction+emp_cpf))  FROM ".TB_PREF."payroll
 WHERE emp_id = ".db_escape($employee)."
 AND month<=".db_escape($month_name);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function get_emp_total_cpf($employee,$month_name)
{
	$sql = "SELECT SUM(emp_cpf)  FROM ".TB_PREF."payroll
 WHERE emp_id = ".db_escape($employee)."
 AND month<=".db_escape($month_name);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function get_employer_total_cpf($employee,$month_name)
{
	$sql = "SELECT SUM(employer_cpf)  FROM ".TB_PREF."payroll
 WHERE emp_id = ".db_escape($employee)."
 AND month<=".db_escape($month_name);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
//
function get_emp_deduction1_name($deduct_id)
{
	$sql = "SELECT description FROM ".TB_PREF."deduction WHERE id = ".db_escape($deduct_id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function get_emp_bank_account_name($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
//for date convert into year
function gedate($then) {
    $then_ts = strtotime($then);
    $then_year = date('Y', $then_ts);
    return $then_year;
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$month_name = $_POST['PARAM_0'];
    	//$dept = $_POST['PARAM_1'];
		$employee = $_POST['PARAM_1'];
    	$comments = $_POST['PARAM_2'];
	    $orientation = $_POST['PARAM_3'];
	    $destination = $_POST['PARAM_4'];
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

	$cols = array(0, 20, 70, 335, 435, 500);

	$headers = array(_('Code'), _('Name'), _('Basic Salary'), _('Advance'));

	$aligns = array('left', 'left', 'left', 'left');

  /*  $params =   array( 	0 => $comments,
    			1 => array('text' => _('Month'), 'from' => $month, 'to' => $to),
    			2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),  
				3 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to)   			

			); */
//$orientation = 'L';
    $rep = new FrontReport(_('Pay Slip'), "PaySlip", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header2011');
			$rep->Info($params, $cols, null, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];


   	$rep->NewLine(1);


 $sqll = "SELECT * FROM ".TB_PREF."payroll 
	 WHERE emp_id=".db_escape($employee);
		
		if ($month_name != ALL_TEXT)
		$sqll .= "AND month=".db_escape($month_name);
	
	$result2 = db_query($sqll, "The customers could not be retrieved");

	/*$sql = "SELECT payroll_id,deduct_id,emp_id,amount FROM ".TB_PREF."payroll_deduction ";
	if ($employee != ALL_TEXT)
		$sql .= "WHERE emp_id=".db_escape($employee);
	$sql .= " ORDER BY payroll_id";
	$result = db_query($sql, "The customers could not be retrieved");*/
	
	//$num_lines = 0;
	//$result = getTransactions($month_name,$dept,$employee);
while ($myrow2 = db_fetch($result2))
{
	
		//if (db_num_rows($result) == 0) continue;
    $emp_detail=db_fetch(get_employee_details($myrow2['emp_id']));
//	$rep->fontSize += 2;
   	
	//$rep->Font();
	$employee=get_employee_name11($myrow2['emp_id']);
	$month= get_month_name($myrow2['month']);
	$date=sql2date($myrow2['date']);
	$department=get_emp_dept_name($myrow2['dept_id']);
	$bank_name=get_emp_bank_account_name($emp_detail['company_bank']);
	$totalsalaryamount=number_format2(get_emp_total_salary($myrow2['emp_id'],$myrow2['month']));
	$totalemployeecpf=number_format2(get_emp_total_cpf($myrow2['emp_id'],$myrow2['month']));
	$totalemployercpf=number_format2(get_employer_total_cpf($myrow2['emp_id'],$myrow2['month']));	
//$rep->MultiCell(182.5, 15 ,  "Name" , 1, ''  , 1, 1, 120, 132.8, true, 0, false, true, 40, 'M');
$rep->MultiCell(182.5, 15, " ".$employee ,1, 'L', 0, 2, 120,132.8, true); 
$rep->MultiCell(182.5, 15, " ".$month ,1, 'L', 0, 2, 382.5,132.8, true);
$rep->MultiCell(182.5, 15, " ".$emp_detail['emp_code'] ,1, 'L', 0, 2, 120,148.8, true); 
$rep->MultiCell(182.5, 15, " ".$date ,1, 'L', 0, 2, 382.5,148.8, true); 
$rep->MultiCell(182.5, 15, " ".$department ,1, 'L', 0, 2, 120,164.8, true); 
$rep->MultiCell(182.5, 15, " ".$bank_name ,1, 'L', 0, 2, 382.5,164.8, true);
$rep->MultiCell(182.5, 15, "" ,1, 'L', 0, 2, 120,180.8, true); 
$rep->MultiCell(182.5, 15, " ".$emp_detail['emp_bank'] ,1, 'L', 0, 2, 382.5,180.8, true); 
//for employer
$rep->MultiCell(362.5, 15, " Employer CPF" ,1, 'L', 0, 2, 40,647.8, true); 
$rep->MultiCell(162.5, 15," ".$myrow2['employer_cpf'] ,1, 'L', 0, 2, 402.5,647.8, true);
//for pay items detail
$rep->MultiCell(362.5, 15, " Salary/OT" ,1, 'L', 0, 2, 40,735.8, true); 
$rep->MultiCell(162.5, 15, " ".$totalsalaryamount ,1, 'L', 0, 2, 402.5,735.8, true);
$rep->MultiCell(362.5, 15, " Employee CPF" ,1, 'L', 0, 2, 40,750.8, true); 
$rep->MultiCell(162.5, 15, " ".$totalemployeecpf ,1, 'L', 0, 2, 402.5,750.8, true);
	$rep->MultiCell(362.5, 15, " Employer CPF" ,1, 'L', 0, 2, 40,765.8, true); 
$rep->MultiCell(162.5, 15, " ".$totalemployercpf ,1, 'L', 0, 2, 402.5,765.8, true);
$rep->MultiCell(362.5, 15, " CPF Fund" ,1, 'L', 0, 2, 40,780.8, true); 
$rep->MultiCell(162.5, 15, "" ,1, 'L', 0, 2, 402.5,780.8, true);		
	$employer_cpf =$myrow2['employer_cpf'];
	$basic_salary=$myrow2['basic_salary'];
	
	//$timestamp = strtotime(sql2date($myrow2['date']));
	// $date=date('Y');
	$employee_cpf= $myrow2['emp_cpf'];
	
	
    	$rep->NewLine(-1);
	$result1 = getTransactions($myrow2['trans_no']);
	$result = getTransactions2($myrow2['trans_no']);

   	$rep->Font(b);
	$rep->TextCol(1, 3, _('Addition Pay Items'));
	$rep->TextCol(4, 5, gedate($myrow2['date']));
   	$rep->Font();
	$rep->NewLine();
	$rep->TextCol(1, 3,_("Employee Salary"));
	$rep->AmountCol(4, 5, $basic_salary,$dec);
   	$rep->NewLine();
	
//loop for allowances
		while ($myrow1 = db_fetch($result1))
		{
	
		//if (db_num_rows($result) == 0) continue;
	//$rep->fontSize += 2;    	
			$rep->TextCol(1, 3, get_emp_allowance1_name($myrow1['allow_id']));	
			$rep->AmountCol(4, 5,$myrow1['amount'],$dec);
	    	$rep->NewLine();	
			$allowancetotal +=$myrow1['amount'];
				
		}

   	$rep->NewLine(2);		
   	$rep->Font(b);	
	$rep->TextCol(1, 3, _('Deduction Pay Items'));	
	$rep->TextCol(4, 5, _('Deducted Amount'));
   	$rep->Font();
   	$rep->NewLine();
	
	
//loop for deduction
		while ($myrow=db_fetch($result))
		{
	//if ($no_zeros && db_num_rows($res) == 0) continue;
//		$rep->fontSize += 2;
		$rep->TextCol(1, 3, get_emp_deduction1_name($myrow['deduct_id']));
		$rep->AmountCol(4, 5, $myrow['amount'],$dec);
    	$rep->NewLine();
		$deducttotal +=$myrow['amount'];
		
			}
			$rep->TextCol(1, 3, _('Employee CPF'));
			$rep->TextCol(4, 5, $employee_cpf);	
			$grandtotal=(($basic_salary+$allowancetotal)-($deducttotal+$employee_cpf));
			$rep->NewLine();
			$rep->NewLine();
	$rep->Font(b);
	$rep->TextCol(1, 2, _('Net Pay'));
	$rep->AmountCol(4, 5, $grandtotal,$dec);
	$rep->Font();
//ansar
$rep->SetFillColor(192,192,192);
						$rep->SetFont('arial', 'b', 14);


						$txt = _(" Salary Advice: Williams Limited");		
//						$rep->MultiCell(255, 20.8 ,  "".$txt , 1, 'J'  , 1, 1, 310, 145, true, 1 , true);
						$rep->MultiCell(525, 20.8 ,  "".$txt , 1, ''  , 1, 1, 40, 112, true, 0, false, true, 40, 'M');
						
						$rep->MultiCell(523, 15.8 ,  " Pay Items Detail: ".$month." ".gedate($myrow2['date']), 1, ''  , 1, 1, 42, 225, true, 0, false, true, 40, 'M');
						
					    $rep->MultiCell(525, 20.8 ,  " Employer Contributions" , 1, ''  , 1, 1, 40, 612, true, 0, false, true, 40, 'M');
						$rep->MultiCell(525, 20.8 ,  " Pay Items Detail:Year To Date (Jan  ".gedate($myrow2['date'])." To ".$month." ".gedate($myrow2['date']).")" , 1, ''  , 1, 1, 40, 700, true, 0, false, true, 40, 'M');
						$rep->SetFont('arial', '', 9);
				$rep->MultiCell(80, 15 ,  " Name" , 1, ''  , 1, 1, 40, 132.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  " Pay Month" , 1, ''  , 1, 1, 302.5, 132.8, true, 0, false, true, 40, 'M');
			    $rep->MultiCell(80, 15 ,  " Employee ID" , 1, ''  , 1, 1, 40, 148.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  " Pay Date" , 1, ''  , 1, 1, 302.5, 148.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  " Department" , 1, ''  , 1, 1, 40, 164.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  " Bank Name" , 1, ''  , 1, 1, 302.5, 164.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  "" , 1, ''  , 1, 1, 40, 180.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(80, 15 ,  " Account #" , 1, ''  , 1, 1, 302.5, 180.8, true, 0, false, true, 40, 'M');
				//for employer
				$rep->MultiCell(362.5, 15 ,  " Pay Item" , 1, ''  , 1, 1, 40, 632.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(162.5, 15 ,  " Paid Amount" , 1, ''  , 1, 1, 402.5, 632.8, true, 0, false, true, 40, 'M');
				
				$rep->MultiCell(362.5, 15 ,  " Total Employer Contribution" , 1, ''  , 1, 1, 40, 662.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(162.5, 15 ,  " ".$employer_cpf , 1, ''  , 1, 1, 402.5, 662.8, true, 0, false, true, 40, 'M');
				//for pay item detail
				$rep->MultiCell(362.5, 15 ,  " Pay Items" , 1, ''  , 1, 1, 40, 720.8, true, 0, false, true, 40, 'M');
				$rep->MultiCell(162.5, 15 ,  " Paid Amount" , 1, ''  , 1, 1, 402.5, 720.8, true, 0, false, true, 40, 'M');
				
				
				
				
//$logo4 = company_path() . "/images/" . 'payroll_logo.PNG';
			//$rep->Image($logo4, '40', '10', 530, 100, '', '', 'T', false, 100, '', false, false, //1, false, false, false);
			
//    	$rep->NewLine();
//}
}
	
	//$rep->Line($rep->row  - 4);
    $rep->End();
}

?>