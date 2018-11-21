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

function get_designation_names($id)
{
    $sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
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
    $cols = array(4, 150, 270, 350, 425, 385, 450, 515);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');

    $header = array(_("Item Code"), _("Item Description"), _("Quantity"),
        _("Unit"), _("Price"), _("Discount %"), _("Total"));

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

//    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);


if($employee==-1 || $employee==null)
    $sql="select * from ".TB_PREF."employee ";
    else
        $sql="select * from ".TB_PREF."employee where employee_id=$employee";
    $result=db_query($sql);

    $logo = company_path() . "/	images/" . 1;

    if ($rep->company['coy_logo'] != '' && file_exists($logo))
    {


    }


    $rep->Font();
    $rep->Info($params, $cols, $header, $aligns);

    $rep->SetCommonData('', '', '', '', 11, '');
    $rep->SetHeaderType('Header11');
//    $rep->NewPage();

//    $rep->MultiCell(50, 50, "Name: ",0, 'L', 0, 2, 30,50, true);
//    $rep->MultiCell(50, 50, "Name: ",0, 'L', 0, 2, 30,100, true);




    while($row = db_fetch($result))
    {
        $rep->NewPage();

        $rep->MultiCell(300, 300, "Employee History Report: ",0, 'L', 0, 2, 40,60, true);


        $rep->MultiCell(50, 50, "Name: ",0, 'L', 0, 2, 30,150, true);

        $rep->MultiCell(400, 650, "___________________________________" ,0, 'L', 0, 2, 85,160, true);


        $rep->MultiCell(100, 50, "Father Name: " ,0, 'L', 0, 2, 290,150, true);
        $rep->MultiCell(400, 650, "___________________________________ " ,0, 'L', 0, 2,360,160, true);

        $rep->MultiCell(100, 50, "Designation: " ,0, 'L', 0, 2, 30,200, true);
        $rep->MultiCell(500, 650, "____________________________________" ,0, 'L', 0, 2, 85,210, true);

        $rep->MultiCell(400, 650, " ". $row['emp_name'] ,0, 'L', 0, 2, 85,150, true);
        $rep->MultiCell(400, 650, "  " . $row['emp_father'] ,0, 'L', 0, 2,360,150, true);
        $rep->MultiCell(500, 650, " ".get_designation_names($row['emp_desig'])  ,0, 'L', 0, 2, 85,200, true);


        $range = get_employee_comp($row['employee_id']);
//        $rep->TextCol(0, 3,	$row['employee_id'], -2);
        $rep->TextCol(0, 3,	$range['company_name'], -2);
        $rep->TextCol(1, 3,	get_designation_names($range['designation']), -2);
        $rep->TextCol(2, 3,	$range['date_from'], -2);
        $rep->TextCol(3, 4,	$range['date_to'], -2);


//        {
//            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
//            $rep->title = _('INVOICE');
//            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
//        }
//        $rep->currency = $cur;




        // calculate summary start row for later use
        $summary_start_row = $rep->bottomMargin + (15 * $rep->lineHeight);



//        $result = get_employee($employee);
//        $SubTotal = 0;
//        while ($myrow2=db_fetch($result))
//        {
//            $rep->TextCol(0, 3,	$myrow2['emp_name'], -2);
//
//            //$rep->NewLine(1);
//            if ($rep->row < $summary_start_row)
//                $rep->NewPage();
//        }

        $rep->Font();
//        if ($email == 1)
//        {
//            $rep->End($email);
//        }
        $rep->newline();
    }
//    if ($email == 0)
        $rep->End();
}

