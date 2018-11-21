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



function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_bank_account_name($id)
{
	$sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($id);

	$result = db_query($sql, "could not retreive bank account");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_gl_trans_all($account)
{
	$sql = "SELECT * FROM ".TB_PREF."gl_trans";
//	if($account != '')
	$sql .= " WHERE account =".db_escape($account);
	return db_query($sql, 'error');
}


//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;

	$accounts = $_POST['PARAM_0'];
	$orientation = $_POST['PARAM_1'];
	$destination = $_POST['PARAM_2'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

//	if ($employee == ANY_NUMERIC)
//		$emp = _('All');
//	else
//		$emp = get_employee_name11($employee);

	if ($accounts == ALL_TEXT)
		$acc = _('All');
	else
		$acc = get_bank_account_name($accounts);


	if ($accounts == ALL_TEXT)
		$acc = _('All');
	else
		$acc = get_bank_account_name($accounts);



	function getTransactions_for_employee($employee)
	{


		$sql = " SELECT * FROM ".TB_PREF."employee where inactive = 0 ";
		if ($employee != ALL_TEXT)
			$sql .= "AND employee_id=".db_escape($employee);

		$result=  db_query($sql,"No transactions were returned");
		$row=db_fetch($result);
		return $row;
	}
	function get_transactions($employee){


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


//	if ($approve) $approval = _('Approve');
//	else $approval = _('Unapprove');

	$cols = array(0, 30, 150, 300, 500);

	$headers = array(_('Sr.No'), _('Account Title'),_('CNIC#'), _('Rupees In Words'),  _('Amount'));

	$aligns = array('left', 'left', 'left', 'left', 'right');

	$params =   array(
		0 => array('text' => _('Account'), '' => $acc, '' => ''),

	);
	$orientation = 'L';
	$rep = new FrontReport(_(''), "SupplierBalances", user_pagesize(), 9, $orientation);

	if ($orientation == 'L')
		recalculate_cols($cols);

//	$rep->SetHeaderType('Header22');
	$rep->Info($params, $cols, $headers, $aligns);

	$rep->Font();
	$rep->NewPage();

	$sql = "SELECT account_name AS name, account_code FROM ".TB_PREF."chart_master ";
	if ($accounts != -1)
		$sql .= " WHERE account_code=".db_escape($accounts);
	$sql .= " ORDER BY name";
	$result = db_query($sql, "The account could not be retrieved");

	while($myrow = db_fetch($result))

	{
		$rep->NewLine(1.5);
		$rep->fontSize += 2;
		$rep->TextCol(1, 2, $myrow['name']);
		$rep->Line($rep->row - 2);
		$rep->fontSize -= 2;

		$res = get_gl_trans_all($myrow['account_code']);
//		$rep->Line($rep->row + 2);
		$rep->NewLine();
//		if (db_num_rows($res)==0)
//			continue;

		$serial_no =1;
		while($myrow2 = db_fetch($res))
		{
			$rep->NewLine(1, 2);
			$rep->TextCol(0, 1, $serial_no);
			$serial_no++;
			$rep->TextCol(3, 4, convert_number_to_words($myrow2['amount']));
			$rep->AmountCol(4, 5, ($myrow2['amount']));

		}


	}

//$result = get_gl_trans()


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

//	$rep->NewLine(2);
//	$rep->Font(b);
//	$rep->fontSize += 2;
//
//	$rep->fontSize -= 2;
//	$rep->Font('b');
	//$rep->TextCol(0, 1,"Grand Total");
	//$rep->TextCol(3, 4,$sum);
//	$rep->NewLine();
	$rep->End();
}

?>