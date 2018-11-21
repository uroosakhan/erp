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

function getTransactions($month, $banks)
{
    $sql = "SELECT ".TB_PREF."payroll.*, ".TB_PREF."employee.*

    			FROM ".TB_PREF."payroll, ".TB_PREF."employee
    			WHERE ".TB_PREF."employee.employee_id = ".TB_PREF."payroll.emp_id 
    			AND ".TB_PREF."employee.company_bank = ".db_escape($banks) ."
    			AND ".TB_PREF."payroll.month = ".db_escape($month) ."


    				ORDER BY ".TB_PREF."employee.emp_name";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
//    			AND ".TB_PREF."employee.company_bank = ".db_escape($banks) ."
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

    	$month = $_POST['PARAM_0'];
    	$banks = $_POST['PARAM_1'];
    	$letter_date = $_POST['PARAM_2'];
    	$cheque = $_POST['PARAM_3'];
    	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	if ($banks == ALL_TEXT)
		$bank = _('All');
	else
		$bank_name1 = get_bank_account($banks);
		$bank_name = $bank_name1['bank_account_name'];		

    	$dec = user_price_dec();

		$month_name = get_month_name($month);
		
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 20, 120, 290,	350, 510);

	$headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

	$aligns = array('left',	'left',	'left',	'left',	'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to),
    			2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

			);

    $rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header3');
			$rep->Info($params, $cols, null, $aligns);

    $rep->Font();
//    $rep->Info($params, $cols, $headers, $aligns);

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
	$rep->TextColLines(0, 5, _('This is to Certify that the below mentioned personnel are employed with us and Maintaining account in your bank for the purpose of salary transfer. Therefore you are requested to transfer their salary from company account to employees Account separately.'));
   	$rep->NewLine();

    $rep->Font('b');
	$rep->TextCol(0, 2, _('Company Account Title'));
	$rep->TextCol(2, 3, _('Pearl Fabrics Company'));	
   	$rep->NewLine();
	$rep->TextCol(0, 2, _('Company Account No'));	
	$rep->TextCol(2, 3, $bank_account_no);
   	$rep->NewLine();
	$rep->TextCol(0, 2, _('Cheque No'));		
	$rep->TextCol(2, 3, $cheque);
    $rep->Font();

   	$rep->NewLine(2);
	
	 $rep->Font('b');
	$rep->TextCol(0, 1, _('Sr. No'));
	$rep->TextCol(1, 2, _('Account No'));
	$rep->TextCol(2, 3, _('Employee Name'));
	$rep->TextCol(3, 4, _('Designation'));
	$rep->TextCol(4, 5, _('Amount'));
    $rep->Font('');
	
   	$rep->NewLine(1);

	$result = getTransactions($month, $banks);
			
	while ($myrow=db_fetch($result))
	{
//		if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;

		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['overtime'] ;
		$SerialNo += 1;
		$rep->TextCol(0, 1, $SerialNo);
		$rep->TextCol(1, 2, $myrow['emp_bank']);
		$rep->TextCol(2, 3, $myrow['emp_name']);
		$rep->TextCol(3, 4, get_employee_desg($myrow['emp_desig']) );
		$rep->AmountCol(4, 5, $NetSalary, $dec);

		$TotalNetSalary += $NetSalary;
		
		
    	$rep->NewLine();
	}

	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->AmountCol(4, 5,	$TotalNetSalary, $dec);	
	$rep->fontSize -= 2;


	$rep->NewLine();
    $rep->End();
}

?>
