<?php

$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------

print_customer_balances();

function get_transactions22($emp_id, $month, $to)
{
    //$from = date2sql($from);
//	$to = date2sql($to);

    $sql = "SELECT payroll.trans_no,payroll.net_salary,payroll.date
     	FROM 
     	".TB_PREF."payroll payroll
     
     	WHERE  payroll.trans_no !=0
     	
 			";
    if ($emp_id != 0)
        $sql .= " AND payroll.emp_id = ".db_escape($emp_id);
    if ($month != 0)
        $sql .= " AND payroll.month = ".db_escape($month);
//    if ($to != 0)
//        $sql .= " AND payroll.f_year= ".db_escape($to);
    return db_query($sql,"No transactions were returned");
}
function get_bank_($emp_id, $month, $to)
{
    //$from = date2sql($from);
//	$to = date2sql($to);

    $sql = "SELECT bank.amount
     	FROM 
     	".TB_PREF."bank_trans bank
     
     	WHERE  bank.trans_no !=0
     	
 			";
    if ($emp_id != 0)
        $sql .= " AND bank.person_id = ".db_escape($emp_id);
    if ($month != 0)
        $sql .= " AND bank.month_= ".db_escape($month);
//    if ($to != 0)
//        $sql .= " AND bank.fiscal= ".db_escape($to);
    $result = db_query($sql, "Could't get amount");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_division_name101110($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_location_name101110($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_location_name2101110($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_employee_name101110($employee_id)
{
    $sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_month_name101110($month_id)
{
    $sql = "SELECT description AS month_name FROM ".TB_PREF."month WHERE id=".db_escape($month_id);

    $result = db_query($sql, "could not get month name");

    $row = db_fetch_row($result);

    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    global $path_to_root;

    $to = $_POST['PARAM_0'];
    $employee = $_POST['PARAM_1'];
    $month = $_POST['PARAM_2'];
    $divison = $_POST['PARAM_3'];
    $project = $_POST['PARAM_4'];
    $location  = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];
    $destination = $_POST['PARAM_8'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');
    if ($employee == ALL_TEXT)
        $emp = _('All');
    else
        $emp = get_employee_name101110($employee);
    $dec = user_price_dec();

    if ($month == ALL_TEXT)
        $mon = _('All');
    else
        $mon= get_month_name101110($month);

    if ($divison == ALL_TEXT)
        $div = _('All');
    else
        $div = get_division_name101110 ($divison);

    if ($project == ALL_TEXT)
        $pro = _('All');
    else
        $pro = get_location_name101110($project);

    if ($location == ALL_TEXT)
        $loc = _('All');
    else
        $loc = get_location_name2101110($location);


    $cols = array(0, 30, 180, 220,	250, 320, 385, 450,	515);

    $headers = array(_('#'), _('Date'), _(''), _(''), _('Charges'), _('Credits'),
        _('Allocated'), 	_('Balance'));

    $aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $to),
        2 => array('text' => _('Divison'), 'from' => $div, 'to' => $to),
        3 => array('text' => _('Project'), 'from' => $pro, 'to' => $to),
        4 => array('text' => _('Location'), 'from' => $loc, 'to' => $to),
        5 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
        6 => array('text' => _('Month'), 'from' => $mon, 'to' => $to)
    );

    $rep = new FrontReport(_('Employee Balances'), "CustomerBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $grandtotal = array(0,0,0,0);

    $sql = "SELECT employee_id, emp_name 
    FROM ".TB_PREF."employee 
    WHERE employee_id != 0";
    if ($employee != ALL_TEXT)
        $sql .= " AND employee_id=".db_escape($employee);

    $sql .= " ORDER BY emp_name";
    $result = db_query($sql, "The customers could not be retrieved");
    $grandtotal = $grandtotal1 = $grandtota2 = 0.0;
    $total = $total1 = $tota2 = 0.0;
    while ($myrow = db_fetch($result))
    {

        $rep->fontSize += 2;
        $rep->TextCol(0, 2, $myrow['emp_name']);
        $rep->fontSize -= 2;
        $rep->NewLine(1, 2);
        $res = get_transactions22($myrow['employee_id'], $month, $to);

        while ($trans = db_fetch($res))
        {
            //$bal = get_open_balance($myrow['employee_id'], $from, $convert);
            //$init= round2(abs($bal['charges']*$rate), $dec);
            $rep->NewLine(1, 2);

            $rep->TextCol(0, 2, $trans['trans_no']);
            $rep->DateCol(1, 2,	$trans['date'], true);
            $rep->AmountCol(4, 5,  $trans['net_salary'], $dec);
            $amount = get_bank_($myrow['employee_id'], $month,$to);
            $rep->AmountCol(5, 6,  abs($amount), $dec);
            $rep->AmountCol(7, 8,  $trans['net_salary'] - (abs($amount)), $dec);
            $total += $trans['net_salary'];
            $total1 += ($amount);
            $total2 += $trans['net_salary'] - ($amount);

            $grandtotal += $trans['net_salary'];
            $grandtotal1 += ($amount);
            $grandtotal2 += $trans['net_salary'] - ($amount);

        }

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->TextCol(0, 3, _('Total'));

        $rep->AmountCol(4, 5, $total, $dec);
        $rep->AmountCol(5, 6, abs($total1), $dec);
        $rep->AmountCol(7, 8, $total2, $dec);
        $total = $total1 = $tota2 = 0.0;


        $rep->Line($rep->row  - 4);
        $rep->NewLine(2);
    }
    $rep->fontSize += 2;
    $rep->TextCol(0, 3, _('Grand Total'));
    $rep->fontSize -= 2;



    $rep->AmountCol(4, 5, $grandtotal, $dec);
    $rep->AmountCol(5, 6, abs($grandtotal1), $dec);
    $rep->AmountCol(7, 8, $grandtotal2, $dec);

    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}