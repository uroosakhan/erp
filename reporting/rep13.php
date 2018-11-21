<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------
function get_invoice_range($from, $to)
{
    global $SysPrefs;

    $ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");

    $sql = "SELECT trans.trans_no, trans.reference
		FROM ".TB_PREF."debtor_trans trans 
			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
		WHERE trans.type=".ST_SALESINVOICE
        ." AND ISNULL(voided.id)"
        ." AND trans.reference>=".db_escape(get_reference(ST_SALESINVOICE, $from))
        ." AND trans.reference<=".db_escape(get_reference(ST_SALESINVOICE, $to))
        ." ORDER BY trans.tran_date, trans.$ref";

    return db_query($sql, "Cant retrieve invoice range");
}
function get_employee($employee_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get employee");

    return $result;
}

function get_employee_comp($employee_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get employee");

    $row=db_fetch($result);
    return $row;
}


function get_desgg($id)
{
    $sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_increment($id)
{
    $sql = "SELECT * FROM ".TB_PREF."increment,".TB_PREF."employee WHERE 
	 ".TB_PREF."increment.emp_id=".TB_PREF."employee.employee_id
	 AND ".TB_PREF."increment.emp_id=".db_escape($id)."";

    return $result = db_query($sql, "could not get supplier");

    //$row = db_fetch($result);

//	return $row;
}
print_invoices();

//----------------------------------------------------------------------------------------------------

function print_invoices()
{
    global $path_to_root, $SysPrefs;

    $show_this_payment = true; // include payments invoiced here in summary

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $month = $_POST['PARAM_0'];
    $dept = $_POST['PARAM_1'];
    $employee = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];

//    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();
//
//    $fno = explode("-", $from);
//    $tno = explode("-", $to);
//    $from = min($fno[0], $tno[0]);
//    $to = max($fno[0], $tno[0]);

    //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
    $cols = array(4, 40, 100, 160, 225, 320, 390,450,490);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'left','left','right');

//    $header = array(_("S.No"), _("Incre-Code"), _("Incre-Date"),
//        _("Valid From"), _("Emp-Name"), _("Incre-Amount"), _("Last Sal"), _("Current Sal"), _("Remarks"));

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

//    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, '', $aligns);

    $rep->SetCommonData('', '', '', '', 100, '');
    $rep->SetHeaderType('Header13');

if($employee==-1 || $employee==null)
    $sql="select * from ".TB_PREF."employee ";
    else
        $sql="select * from ".TB_PREF."employee where employee_id=$employee";
    $result=db_query($sql);
    $logo = company_path() . "/	images/" . 1;

    if ($rep->company['coy_logo'] != '' && file_exists($logo))
    {


    }


//    $rep->NewPage();

//    $rep->MultiCell(50, 50, "Name: ",0, 'L', 0, 2, 30,50, true);
//    $rep->MultiCell(50, 50, "Name: ",0, 'L', 0, 2, 30,100, true);

    while($row = db_fetch($result))
    {

if($row['employee_id']) {


    $rep->NewPage();
    $rep->MultiCell(100, 50, "Employee Code: ", 0, 'L', 0, 2, 30, 50, true);

    $rep->MultiCell(400, 650, "__________", 0, 'L', 0, 2, 110, 55, true);


    $rep->MultiCell(100, 50, "Current Salary: ", 0, 'L', 0, 2, 380, 50, true);
    $rep->MultiCell(400, 650, "______________________ ", 0, 'L', 0, 2, 450, 55, true);
//
    $rep->MultiCell(100, 50, "Employee Name: ", 0, 'L', 0, 2, 30, 90, true);
    $rep->MultiCell(500, 650, "______________________________________________", 0, 'L', 0, 2, 110, 95, true);
//
//
    $rep->MultiCell(100, 50, "Special Allow: ", 0, 'L', 0, 2, 380, 95, true);
    $rep->MultiCell(400, 650, "______________________ ", 0, 'L', 0, 2, 450, 95, true);
//
    $rep->MultiCell(100, 50, "Designation: ", 0, 'L', 0, 2, 30, 130, true);
    $rep->MultiCell(500, 650, "______________________________________________", 0, 'L', 0, 2, 110, 130, true);
//
    $rep->MultiCell(100, 50, "Feild Expense: ", 0, 'L', 0, 2, 380, 130, true);
    $rep->MultiCell(400, 650, "______________________ ", 0, 'L', 0, 2, 450, 130, true);
//
    $rep->MultiCell(100, 50, "Date of Joining: ", 0, 'L', 0, 2, 30, 170, true);
    $rep->MultiCell(500, 650, "______________________", 0, 'L', 0, 2, 110, 170, true);
//
    $rep->MultiCell(100, 50, "Total: ", 0, 'L', 0, 2, 380, 170, true);
    $rep->MultiCell(400, 650, "  ", 0, 'L', 0, 2, 450, 170, true);
//
    $rep->MultiCell(200, 50, "Salary At the time of appointment: ", 0, 'L', 0, 2, 30, 210, true);
    $rep->MultiCell(500, 650, "______________________", 0, 'L', 0, 2, 170, 210, true);

    $rep->newline();
    $rep->MultiCell(400, 650, " " . $row['emp_code'], 0, 'L', 0, 2, 110, 55, true);
    $rep->MultiCell(400, 650, " " . $row['basic_salary'], 0, 'L', 0, 2, 450, 55, true);
    $rep->MultiCell(500, 650, " " . $row['emp_name'], 0, 'L', 0, 2, 110, 95, true);
    $rep->MultiCell(400, 650, " " . $row['social_sec'], 0, 'L', 0, 2, 450, 95, true);
    $rep->MultiCell(500, 650, " " . get_desgg($row['emp_desig']), 0, 'L', 0, 2, 110, 130, true);
    $rep->MultiCell(500, 650, " " . $row['j_date'], 0, 'L', 0, 2, 110, 170, true);
    $rep->MultiCell(500, 650, " " . $row['prev_salary'], 0, 'L', 0, 2, 170, 210, true);

    $rep->MultiCell(400, 650, "  " . $row['basic_salary'], 0, 'L', 0, 2, 450, 170, true);

    $incre = get_increment($row['employee_id']);

    while ($myrow1 = db_fetch($incre)) {
        $rep->TextCol(0, 1, $myrow1['id'], -2);
        $rep->TextCol(1, 2, $myrow1['increment_code'], -2);
        $rep->TextCol(2, 3, sql2date($myrow1['increment_date']), -2);
        $rep->TextCol(3, 4, $myrow1['valid_from'], -2);
        $rep->TextCol(4, 5, ($myrow1['emp_name']), -2);
        $rep->TextCol(5, 6, $myrow1['increament_amount'], -2);
        $rep->TextCol(6, 7, $myrow1['last_salary'], -2);
        $rep->TextCol(7, 8, $myrow1['basic_salary'], -2);
        $rep->TextCol(8, 9, $myrow1['remarks'], -2);


        $rep->NewLine();

    }
}
    }


//   if ($email == 0)
        $rep->End();
}

