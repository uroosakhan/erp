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

print_invoices();

//----------------------------------------------------------------------------------------------------
//function get_phone1($customer_id)
//{
//    $sql = "SELECT `phone` FROM ".TB_PREF."crm_persons WHERE `name`=".db_escape($customer_id);
//
//    $result = db_query($sql, "could not get customer phone");
//
//    $row = db_fetch_row($result);
//
//    return $row[0];
//}
function get_item_name_pos($item)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($item);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_daily_sales($from, $to, $users)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT *, SUM(qty*-1) as SalesQuantity FROM ".TB_PREF."stock_moves moves LEFT JOIN 
            ".TB_PREF."audit_trail audit 
            ON moves.trans_no = audit.trans_no
            AND moves.type = audit.type
            AND moves.type = ".ST_CUSTDELIVERY."
            WHERE moves.tran_date >= ".db_escape($from)."
            AND moves.tran_date <= ".db_escape($to);
    if($users != -1)
        $sql .= " AND audit.user = ".db_escape($users); // date filter
    $sql .= " GROUP BY moves.stock_id 
            ORDER BY moves.stock_id ";

    return db_query($sql, "Cannot get all audit info for transaction");
}
function print_invoices()
{
    global $path_to_root;
    include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $users = $_POST['PARAM_2'];

    $email = 0;
    $cols = array(8, 40, 65, 90, 125, 150, 200);
    // $headers in doctext.inc
    $aligns = array('left',	'center',	'left', 'left','left','left');
    $cur = get_company_Pref('curr_default');
    if ($email == 0)
    {
        $rep = new FrontReport(_('ESTIMATE'), "InvoiceBulk", 'POS3', '9');
        $rep->SetHeaderType('Header107779');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info(null, $cols, '', $aligns);
    }
    $TotalQty = 0;
    $rep->SetCommonData(null, null, null, null, ST_SALESINVOICE, null);
    $rep->NewPage();
    $result = get_daily_sales($from, $to, $users);
    while ($myrow = db_fetch($result)) {
        $rep->TextCol(0, 3,	get_item_name_pos($myrow['stock_id']), -2);
        $rep->TextCol(5, 6,	$myrow['SalesQuantity'], -2);
        $rep->NewLine();
        $TotalQty += $myrow['SalesQuantity'];
    }
    $rep->Font('b');
    $rep->TextCol(0, 3,	"Total Quantity:", -2);
    $rep->TextCol(5, 6,	$TotalQty, -2);
    $rep->Font('');

    if ($email == 0)
        $rep->End();
}

?>