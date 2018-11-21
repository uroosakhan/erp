<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

//----------------------------------------------------------------------------------------------------
function convert_number_to_words($number) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'Zero',
		1                   => 'One',
		2                   => 'Two',
		3                   => 'Three',
		4                   => 'Four',
		5                   => 'Five',
		6                   => 'Six',
		7                   => 'Seven',
		8                   => 'Eight',
		9                   => 'Nine',
		10                  => 'Ten',
		11                  => 'Eleven',
		12                  => 'Twelve',
		13                  => 'Thirteen',
		14                  => 'Fourteen',
		15                  => 'Fifteen',
		16                  => 'Sixteen',
		17                  => 'Seventeen',
		18                  => 'Eighteen',
		19                  => 'Nineteen',
		20                  => 'Twenty',
		30                  => 'Thirty',
		40                  => 'Fourty',
		50                  => 'Fifty',
		60                  => 'Sixty',
		70                  => 'Seventy',
		80                  => 'Eighty',
		90                  => 'Ninety',
		100                 => 'Hundred',
		1000                => 'Thousand',
		1000000             => 'Million',
		1000000000          => 'Billion',
		1000000000000       => 'Trillion',
		1000000000000000    => 'Quadrillion',
		1000000000000000000 => 'Quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . convert_number_to_words(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . convert_number_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= convert_number_to_words($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return $string;
}
print_employee_balances();

function getTransactions($month,$dept,$employee)
{
//	$sql = "SELECT employee_id FROM ".TB_PREF."employee.
//    			FROM ".TB_PREF."employee
//    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee);
//
//
//	$TransResult = db_query($sql,"No transactions were returned");
//
//	return $TransResult;
	$sql = "SELECT  ".TB_PREF."employee.*,".TB_PREF."payroll.*

    			FROM ".TB_PREF."employee,".TB_PREF."payroll
    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee)." 
    			AND month = 11
				AND ".TB_PREF."payroll.emp_id=".TB_PREF."employee.employee_id";

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
//
//function get_employe_payroll($emp_id)
//{
//	$sql = "SELECT * FROM ".TB_PREF."payroll WHERE id=".db_escape($emp_id);
//
//	$result = db_query($sql, "could not get supplier");
//
//	$row = db_fetch($result);
//
//	return $row;
//}
function get_employe_arrer()
{
	$sql = "SELECT arrer FROM ".TB_PREF."payroll ";

	$result = db_query($sql, "could not get supplier");

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
function get_desg()
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape();

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_employe_basic_salary2()
{
	$sql = "SELECT basic_salary FROM ".TB_PREF."payroll WHERE id=".db_escape();

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
function get_designation($row)
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($row['designation']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_designation_names($id)
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
	$rep->SetHeaderType('Header108810');
	$rep->Info($params, $cols, null, $aligns);
	$rep->Font();
	$rep->NewPage();
	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];
	$rep->Font('b');
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

	if ($rep->company['coy_logo'] != '' && file_exists($logo))
	{


	}
//	$rep->multicell(100,100,"".$logo,1,'C',1,0,40,10,true);
	for($i = 0; $i < 2 ; $i++){
		$rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,110, true);
	                          }
	$emp_earn = get_employee_salary($employee);
	$emp_allownace = get_employee_allowance($employee);
	$emp_tax = get_employee_tax($employee);
	$earn = $emp_earn + $emp_allownace;
	$result = getTransactions($month,$dept,$employee);
	while ($myrow=db_fetch($result)) {
		$rep->NewLine();
		$rep->MultiCell(50, 50, "Name: ", 0, 'L', 0, 2, 30, 160, true);
		$rep->MultiCell(100, 50, "" .$myrow['emp_name'], 0, 'L', 0, 2, 100, 160, true);
		$rep->MultiCell(550, 650, "_______________________________________________ ", 0, 'L', 0, 2, 90, 160, true);
		$rep->MultiCell(50, 50, "Designation: ", 0, 'L', 0, 2, 30, 180, true);
		$rep->MultiCell(550, 50, "".get_designation_names($myrow['emp_desig']), 0, 'L', 0, 2, 100, 180, true);
		$rep->MultiCell(550, 650, "______________________________________________ ", 0, 'L', 0, 2, 90, 180, true);
		$rep->MultiCell(150, 50, "Net Salary: " , 0, 'L', 0, 2, 340, 180, true);
		$rep->MultiCell(150, 50, "" .$myrow['net_salary'], 0, 'L', 0, 2, 400, 180, true);
		$rep->MultiCell(150, 50, "" .$myrow['basic_salary'], 0, 'L', 0, 2, 100, 200, true);
		$rep->MultiCell(150, 50, "" .$myrow['remarks'], 0, 'L', 0, 2, 100, 280, true);
		$rep->MultiCell(250, 50, "Arrear If Any: ", 0, 'L', 0, 2, 30, 260, true);
		$rep->MultiCell(250, 50, "" .$myrow['arrer'], 0, 'L', 0, 2, 100, 260, true);
		$rep->MultiCell(150, 50, "EOBI.Cont: ", 0, 'L', 0, 2, 340, 240, true);
		$rep->MultiCell(150, 50, "" .$myrow['eobi'], 0, 'L', 0, 2, 400, 240, true);
		$rep->MultiCell(150, 50, "Man Month: ", 0, 'L', 0, 2, 340, 160, true);
		$rep->MultiCell(150, 50, "" .$myrow['man_month_value'], 0, 'L', 0, 2, 400, 160, true);
		$rep->MultiCell(150, 50, "" .$myrow['tax'], 0, 'L', 0, 2, 400, 200, true);
		$rep->MultiCell(150, 50, "Income Tax: ", 0, 'L', 0, 2, 340, 200, true);
		$rep->MultiCell(150, 50, "Special Allow: " ,0, 'L', 0, 2, 30,220, true);
		$rep->MultiCell(150, 50, "No. Of Days: " ,0, 'L', 0, 2, 30,240, true);
		$rep->MultiCell(150, 50, "".'30' ,0, 'L', 0, 2, 100,240, true);
		$rep->MultiCell(150, 50, "Less Advance: ".$myrow['advance_deduction'] ,0, 'L', 0, 2, 340,220, true);
		$rep->MultiCell(50, 50, "Code ",0, 'L', 0, 2, 30,140, true);
		$rep->MultiCell(50, 50, "".$myrow['emp_code'] ,0, 'L', 0, 2, 100,140, true);
		$rep->MultiCell(50, 50, "Project ",0, 'L', 0, 2, 300,140, true);
		$rep->MultiCell(50, 50, "".$myrow['project'] ,0, 'L', 0, 2, 350,140, true);
		$net_payable =  $myrow['net_salary']- $myrow['tax'] - $myrow['advance_deduction'] - $myrow['eobi'];
		$rep->MultiCell(100, 20, "".$net_payable ,1, 'L', 0, 2, 400,260, true);
		$rep->MultiCell(300, 50, "In Words: ",0, 'L', 0, 2, 30,300, true);
		$rep->MultiCell(300, 50, "".convert_number_to_words($net_payable) ,0, 'L', 0, 2, 100,300, true);
		$rep->NewLine(13);
		$logo = company_path()."/images/".$myrow['employee_id'].".jpg";
		$rep->AddImage($logo, 40, 690, 0, 60);

		$NetSalary = $myrow['basic_salary'] + $myrow['allowance'] - $myrow['deduction'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['overtime'];
		$SerialNo += 1;
		$yr = date('Y');


		$allowances=get_employee_allowances($myrow['emp_id']);

		while ($myrow1=db_fetch($allowances))
		{

			$rep->NewLine(-13);
			$rep->TextCol(1, 2,	"".get_employee_allowance_name($myrow1['allow_id']));
			$rep->TextCol(2, 5,	$myrow1['amount']);
			$total_allw +=$myrow1['amount'];
			$total1 = $total_allw + $myrow['basic_salary'];

			$rep->NewLine(+13);
			$rep->NewLine();
		}
		$rep->NewLine(-5);
		$rep->Font('bold');
		$rep->TextCol(2, 5, $total1);
		$rep->Font('');
		$rep->NewLine(+10);
		$total_dedu +=$myrow['deduction']+$myrow['cash_advance']+$myrow['emp_cpf']+$myrow['tax'];
		$TotalNetSalary += $myrow['gross_salary']+$total1-$total_dedu;
		$rep->NewLine();
	}
	$rep->NewLine(-14);
	$rep->Font('bold');
	$rep->Font('');
	$rep->NewLine(+14);
	$rep->Font('bold');
	$rep->NewLine(5);
	$rep->fontSize += 2;
	$rep->NewLine(-5);
	$rep->Font('');
	$rep->MultiCell(150, 50, "Gross Salary: ",0, 'L', 0, 2, 30,200, true);
	$rep->MultiCell(250, 50,  "_______________________________________________ " ,0, 'L', 0, 2, 90,200, true);
	$rep->MultiCell(250, 50,  "_______________________________________________ " ,0, 'L', 0, 2, 90,220, true);
	$rep->MultiCell(250, 50,  "_______________________________________________ " ,0, 'L', 0, 2, 90,240, true);
	$rep->MultiCell(250, 50,  "_______________________________________________ " ,0, 'L', 0, 2, 90,260, true);
	$rep->MultiCell(150, 50, "Remarks: " ,0, 'L', 0, 2, 30,280, true);
	$rep->MultiCell(250, 50,  "________________________________________________" ,0, 'L', 0, 2, 90,280, true);
	$rep->MultiCell(150, 50, "Mode Of Payment: " ,0, 'L', 0, 2, 30,320, true);
	$rep->MultiCell(150, 50, "Cash " ,0, 'L', 0, 2, 120,325, true);
	$rep->MultiCell(150, 50, "Cheque " ,0, 'L', 0, 2, 175,325, true);
	$rep->MultiCell(150, 50, "Draft " ,0, 'L', 0, 2, 240,325, true);
	$rep->MultiCell(150, 50, "transfer " ,0, 'L', 0, 2, 290,325, true);
	$rep->MultiCell(250, 30,  " " ,1, 'L', 0, 2, 110,320, true);
	$rep->MultiCell(20, 20,  " " ,1, 'L', 0, 2, 145,325, true);
	$rep->MultiCell(20, 20,  " " ,1, 'L', 0, 2, 210,325, true);
	$rep->MultiCell(20, 20,  " " ,1, 'L', 0, 2, 265,325, true);
	$rep->MultiCell(20, 20,  " " ,1, 'L', 0, 2, 325,325, true);
	$rep->MultiCell(450, 50,  "______________________________________________________________________________________" ,0, 'L', 0, 2, 90,300, true);
	$rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 400,160, true);
	$rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 400,180, true);
	$rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 400,200, true);
	$rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 400,220, true);
	$rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 400,240, true);
	$rep->MultiCell(150, 50, "Net Payable: " ,0, 'L', 0, 2, 340,260, true);
	$rep->MultiCell(100, 20, "" ,1, 'L', 0, 2, 400,260, true);
	$rep->MultiCell(500, 25, "" ,1, 'L', 0, 2, 30,365, true);
	$rep->MultiCell(150, 25, "Manager" ,1, 'L', 0, 2, 30,365, true);
	$rep->MultiCell(200, 25, "Accountant" ,1, 'L', 0, 2, 180,365, true);
	$rep->MultiCell(150, 25, "Receiver Signature & Date" ,1, 'L', 0, 2, 380,365, true);
	$rep->End();


}

?>
