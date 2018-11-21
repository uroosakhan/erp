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

function getTransactions($month_name,$employee,$date)
{
	$yr = date('Y', strtotime($date));
	//$yr=date("Y");
	$date1 = date('Y-m-d',mktime(0,0,0,1,1,$yr));
	$date01 = date('Y-m-d',mktime(0,0,0,1,31,$yr));

	$date2 = date('Y-m-d',mktime(0,0,0,2,1,$yr));
	$date02 = date('Y-m-d',mktime(0,0,0,2,28,$yr));

	$date3 = date('Y-m-d',mktime(0,0,0,3,1,$yr));
	$date03 = date('Y-m-d',mktime(0,0,0,3,31,$yr));

	$date4 = date('Y-m-d',mktime(0,0,0,4,1,$yr));
	$date04 = date('Y-m-d',mktime(0,0,0,4,30,$yr));

	$date5 = date('Y-m-d',mktime(0,0,0,5,1,$yr));
	$date05 = date('Y-m-d',mktime(0,0,0,5,31,$yr));

	$date6 = date('Y-m-d',mktime(0,0,0,6,1,$yr));
	$date06 = date('Y-m-d',mktime(0,0,0,6,30,$yr));

	$date7 = date('Y-m-d',mktime(0,0,0,7,1,$yr));
	$date07 = date('Y-m-d',mktime(0,0,0,7,31,$yr));

	$date8 = date('Y-m-d',mktime(0,0,0,8,1,$yr));
	$date08 = date('Y-m-d',mktime(0,0,0,8,31,$yr));

	$date9 = date('Y-m-d',mktime(0,0,0,9,1,$yr));
	$date09 = date('Y-m-d',mktime(0,0,0,9,30,$yr));

	$date10 = date('Y-m-d',mktime(0,0,0,10,1,$yr));
	$date010 = date('Y-m-d',mktime(0,0,0,10,31,$yr));

	$date11 = date('Y-m-d',mktime(0,0,0,11,1,$yr));
	$date011 = date('Y-m-d',mktime(0,0,0,11,30,$yr));

	$date12 = date('Y-m-d',mktime(0,0,0,12,1,$yr));
	$date012 = date('Y-m-d',mktime(0,0,0,12,31,$yr));
	$sql = "SELECT ".TB_PREF."leave.*, SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date1' AND `to_date` < '$date01') THEN `no_of_leave` ELSE 0 END) as per1,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date1' AND `to_date` < '$date01') THEN `no_of_leave` ELSE 0 END) as per2,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date1' AND `to_date` < '$date01') THEN `no_of_leave` ELSE 0 END) as per3,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date2' AND `to_date` < '$date02') THEN `no_of_leave` ELSE 0 END) as per4,
            SUM(case when (leave_type = 6 AND `approve`=1 AND `from_date` >= '$date2' AND `to_date` < '$date02') THEN `no_of_leave` ELSE 0 END) as per5,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date2' AND `to_date` < '$date02') THEN `no_of_leave` ELSE 0 END) as per6,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date3' AND `to_date` < '$date03') THEN `no_of_leave` ELSE 0 END) as per7,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date3' AND `to_date` < '$date03') THEN `no_of_leave` ELSE 0 END) as per8,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date3' AND `to_date` < '$date03') THEN `no_of_leave` ELSE 0 END) as per9,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date4' AND `to_date` < '$date04') THEN `no_of_leave` ELSE 0 END) as per10,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date4' AND `to_date` < '$date04') THEN `no_of_leave` ELSE 0 END) as per11,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date4' AND `to_date` < '$date04') THEN `no_of_leave` ELSE 0 END) as per12,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date5' AND `to_date` < '$date05') THEN `no_of_leave` ELSE 0 END) as per13,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date5' AND `to_date` < '$date05') THEN `no_of_leave` ELSE 0 END) as per14,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date5' AND `to_date` < '$date05') THEN `no_of_leave` ELSE 0 END) as per15,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date6' AND `to_date` < '$date06') THEN `no_of_leave` ELSE 0 END) as per16,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date6' AND `to_date` < '$date06') THEN `no_of_leave` ELSE 0 END) as per17,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date6' AND `to_date` < '$date06') THEN `no_of_leave` ELSE 0 END) as per18,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date7' AND `to_date` < '$date07') THEN `no_of_leave` ELSE 0 END) as per19,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date7' AND `to_date` < '$date07') THEN `no_of_leave` ELSE 0 END) as per20,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date7' AND `to_date` < '$date07') THEN `no_of_leave` ELSE 0 END) as per21,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date8' AND `to_date` < '$date08') THEN `no_of_leave` ELSE 0 END) as per22,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date8' AND `to_date` < '$date08') THEN `no_of_leave` ELSE 0 END) as per23,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date8' AND `to_date` < '$date08') THEN `no_of_leave` ELSE 0 END) as per24,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date9' AND `to_date` < '$date09') THEN `no_of_leave` ELSE 0 END) as per25,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date9' AND `to_date` < '$date09') THEN `no_of_leave` ELSE 0 END) as per26,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date9' AND `to_date` < '$date09') THEN `no_of_leave` ELSE 0 END) as per27,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date10' AND `to_date` < '$date010') THEN `no_of_leave` ELSE 0 END) as per28,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date10' AND `to_date` < '$date010') THEN `no_of_leave` ELSE 0 END) as per29,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date10' AND `to_date` < '$date010') THEN `no_of_leave` ELSE 0 END) as per30,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date11' AND `to_date` < '$date011') THEN `no_of_leave` ELSE 0 END) as per31,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date11' AND `to_date` < '$date011') THEN `no_of_leave` ELSE 0 END) as per32,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date11' AND `to_date` < '$date011') THEN `no_of_leave` ELSE 0 END) as per33,
			
			SUM(case when (leave_type = 5 AND `approve`=1 AND `from_date` >= '$date12' AND `to_date` < '$date012') THEN `no_of_leave` ELSE 0 END) as per34,
            SUM(case when (leave_type = 3 AND `approve`=1 AND `from_date` >= '$date12' AND `to_date` < '$date012') THEN `no_of_leave` ELSE 0 END) as per35,
			SUM(case when (leave_type = 2 AND `approve`=1 AND `from_date` >= '$date12' AND `to_date` < '$date012') THEN `no_of_leave` ELSE 0 END) as per36
			from ".TB_PREF."leave";
	if ($employee != ALL_TEXT)
		$sql .= " WHERE  ".TB_PREF."leave.emp_id =".db_escape($employee);

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
function get_employee_casual_leave($description)
{$sql = "SELECT cl FROM ".TB_PREF."grade_leave_setup
 WHERE description=".db_escape($description);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_employee_sick_leave($description)
{$sql = "SELECT sl FROM ".TB_PREF."grade_leave_setup
 WHERE description=".db_escape($description);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_employee_pay_leave($description)
{$sql = "SELECT pl FROM ".TB_PREF."grade_leave_setup
 WHERE description=".db_escape($description);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}


//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
	global $path_to_root, $systypes_array;
	$date = $_POST['PARAM_0'];
	//$month_name = $_POST['PARAM_1'];
	$dept = $_POST['PARAM_1'];
	$employee = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	/*if ($month_name == ALL_TEXT)
            $month = _('All');
        else
            $month = get_month_name($month_name);*/

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

	$cols = array(0, 20, 60, 80, 100, 110, 120, 132, 142, 152, 164, 174, 184, 198, 208, 218,230,240,250,260,270,
		280,294,304,314,325,335,345,360,370,380,395,405,415,430,440,450,470,480,490,515,530,545,552);

	$headers = array(_(''), _(''), _(''), _('C'), _('S'), _('P'), _('C'), _('S'), _('P'), _('C'), _('S'), _('P'), _('C'),  _('S'),
		_('P'), _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P')
	, _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P'), _('C'),  _('S'), _('P'), _('C/B'),  _('S/B'), _('P/B'));
	$aligns = array('left', 'left', 'right', 'right', 'right',  'right', 'right', 'right'
	,'right', 'right', 'right',  'right', 'right', 'right','right','right','right','right','right', 'right',
		'right',  'right', 'right', 'right','right','right','right','right','right', 'right',
		'right',  'right', 'right', 'right','right','right','right','right','right','right',
		'right','right');
	$cols1 = array(0, 30, 70,80, 90, 168, 215, 255, 295, 340, 385, 430, 480, 540, 582,638,695,769);

	$headers1 = array(_('S.No'), _('Name'), _(''), _(''), _('January'), _('February'), _('March'), _('April
'), _('May'), _('June'), _('July'), _('August'), _('September'),  _('October'), _('November'), _('December'), _('Balance Leave'));
	$aligns1 = array('left', 'left', 'right', 'right', 'right',  'right', 'right', 'right','right', 'right', 'right',  'right', 'right', 'right','right','right','right');
	$params =   array( 	0 => $comments,
		//1 => array('text' => _('Month'), 'from' => $month, 'to' => $to),
		1 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
		2 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to)

	);
	$orientation = 'L';
	$rep = new FrontReport(_('Annual Leave Status'), "MonthlySalarySheet", user_pagesize(), 10, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->SetHeaderType('Header');
	//$rep->Info($params, $cols, $headers,$aligns);
	$rep->Info($params, $cols, $headers,$aligns,$cols1,$headers1,$aligns1);

	$rep->Font();
	//  $rep->Info($params, $cols, $headers, $aligns);

	$rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];


	$rep->NewLine(1);
	$sql = "SELECT employee_id, emp_name,emp_dept,emp_grade FROM ".TB_PREF."employee";
	if ($employee != ALL_TEXT)
		$sql .= " WHERE employee_id=".db_escape($employee);
	elseif ($dept != ALL_TEXT)
		$sql .= " WHERE emp_dept=".db_escape($dept);
	else
		if ($employee != ALL_TEXT)
			$sql .= "AND employee_id=".db_escape($employee);
	if ($dept != ALL_TEXT)
		$sql .= "AND emp_dept=".db_escape($dept);
	$result2 = db_query($sql, "The customers could not be retrieved");
	while ($myrow2 = db_fetch($result2))
	{

		$rep->fontSize += 2;
		$rep->Font(b);
		$rep->Font();
		$rep->fontSize -= 2;
		$employee_id=$myrow2['employee_id'];
		$casual_leave=get_employee_casual_leave($myrow2['emp_grade']);
		$sick_leave=get_employee_sick_leave($myrow2['emp_grade']);
		$pay_leave=get_employee_pay_leave($myrow2['emp_grade']);
		$result = getTransactions($month_name,$myrow2['employee_id'],$date);

		while ($myrow=db_fetch($result))
		{
			if (($myrow['emp_id'])!= $employee_id) continue;
			//if(($total_casual_leave && $total_sick_leave && $total_pay_leave)==0)continue;
			$SerialNo += 1;
			$rep->TextCol(0, 1, "  ".$SerialNo);
			//$rep->Font(b);
			$rep->TextCol(1, 3, get_employee_name11($myrow['emp_id']));
			//$rep->Font();
			$rep->TextCol(5, 6, $myrow['per1']);
			$rep->TextCol(3, 4, $myrow['per2']);
			$rep->TextCol(4, 5, $myrow['per3']);

			$rep->TextCol(6, 7, $myrow['per5']);
			$rep->TextCol(7, 8, $myrow['per6']);
			$rep->TextCol(8, 9, $myrow['per4'] );


			$rep->TextCol(9, 10, $myrow['per8']);
			$rep->TextCol(10, 11, $myrow['per9']);
			$rep->TextCol(11, 12, $myrow['per7'] );


			$rep->TextCol(12, 13, $myrow['per11']);
			$rep->TextCol(13, 14, $myrow['per12']);
			$rep->TextCol(14, 15, $myrow['per10'] );


			$rep->TextCol(15, 16, $myrow['per14']);
			$rep->TextCol(16, 17, $myrow['per15']);
			$rep->TextCol(17, 18, $myrow['per13'] );


			$rep->TextCol(18, 19, $myrow['per17']);
			$rep->TextCol(19, 20, $myrow['per18']);
			$rep->TextCol(20, 21, $myrow['per16'] );


			$rep->TextCol(21, 22, $myrow['per20']);
			$rep->TextCol(22, 23, $myrow['per21']);
			$rep->TextCol(23, 24, $myrow['per19'] );


			$rep->TextCol(24, 25, $myrow['per23']);
			$rep->TextCol(25, 26, $myrow['per24']);
			$rep->TextCol(26, 27, $myrow['per22'] );


			$rep->TextCol(27, 28, $myrow['per26']);
			$rep->TextCol(28, 29, $myrow['per27']);
			$rep->TextCol(29, 30, $myrow['per25'] );


			$rep->TextCol(30, 31, $myrow['per29']);
			$rep->TextCol(31, 32, $myrow['per30']);
			$rep->TextCol(32, 33, $myrow['per28'] );


			$rep->TextCol(33, 34, $myrow['per32']);
			$rep->TextCol(34, 35, $myrow['per33']);
			$rep->TextCol(35, 36, $myrow['per31'] );


			$rep->TextCol(36, 37, $myrow['per35']);
			$rep->TextCol(37, 38, $myrow['per36']);
			$rep->TextCol(38, 39, $myrow['per34'] );
			$total_pay_leave=($myrow['per1']+$myrow['per4']+$myrow['per7']+$myrow['per10']
				+ $myrow['per13']+$myrow['per16']+$myrow['per19']+$myrow['per22']+$myrow['per25']
				+$myrow['per28']+$myrow['per31']+$myrow['per34']);

			$total_casual_leave=($myrow['per2']+$myrow['per5']+$myrow['per8']+$myrow['per11']
				+$myrow['per14']+$myrow['per17']+$myrow['per20']+$myrow['per23']+$myrow['per26']
				+$myrow['per29']+$myrow['per32']+$myrow['per35']);

			$total_sick_leave=($myrow['per3']+$myrow['per6']+$myrow['per9']+$myrow['per12']
				+$myrow['per15']+$myrow['per18']+$myrow['per21']+$myrow['per24']+$myrow['per27']
				+$myrow['per30']+$myrow['per33']+$myrow['per36']);

			$diff_cl=$casual_leave-$total_casual_leave;
			$diff_sl=$sick_leave-$total_sick_leave;
			$diff_pl=$pay_leave-$total_pay_leave;
			$rep->TextCol(39, 40, $diff_cl );
			$rep->TextCol(40, 41, $diff_sl);
			$rep->TextCol(41, 42, $diff_pl);
			$rep->TextCol(0, 43,_('________________________________________________________________________________________________________________________________________________________________________________________________________________'), - 2);
			$rep->NewLine(1.5);
			$rep->MultiCell(770, 15, " " ,1, 'L', 0, 2, 40,100, true);//S.no
			$rep->MultiCell(25, 465, " " ,1, 'L', 0, 2, 40,100, true);//S.no
			$rep->MultiCell(103, 465, " " ,1, 'L', 0, 2, 65,100, true);//Name
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 168,100, true);//jan
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 213,100, true);//feb
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 258,100, true);//mar
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 303,100, true);//april
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 348,100, true);//may
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 393,100, true);//june
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 438,100, true);//july
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 483,100, true);//august
			$rep->MultiCell(55, 465, " " ,1, 'L', 0, 2, 528,100, true);//sep
			$rep->MultiCell(45, 465, " " ,1, 'L', 0, 2, 583,100, true);//oct
			$rep->MultiCell(55, 465, " " ,1, 'L', 0, 2, 628,100, true);//nev
			$rep->MultiCell(55, 465, " " ,1, 'L', 0, 2, 683,100, true);//dec
			$rep->MultiCell(73, 465, " " ,1, 'L', 0, 2, 738,100, true);//Bal
		}

		$rep->Font(b);
		$_SESSION["TotalBasicSalary"]=$TotalBasicSalary;
		if ($TotalBasicSalary == 0) continue;
		$rep->Font();
		$rep->NewLine(2);
//}
	}

	//$rep->Line($rep->row  - 4);

	$rep->NewLine(2);
	$rep->Font(b);
	$rep->Font();

	$rep->NewLine();
	$rep->End();
}

?>