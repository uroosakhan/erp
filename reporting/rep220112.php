<?php
$page_security = 'SA_SUPPLIERANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Supplier Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_supplier_balances();


function get_purch_details_($supplier_id)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.*,".TB_PREF."purch_orders.reference,
    ".TB_PREF."purch_orders.ord_date,".TB_PREF."grn_batch.delivery_date as DeliveryDate
		FROM ".TB_PREF."purch_order_details,
		".TB_PREF."purch_orders
		LEFT JOIN ".TB_PREF."grn_batch
		ON ".TB_PREF."purch_orders.order_no=".TB_PREF."grn_batch.purch_order_no
		WHERE ".TB_PREF."purch_order_details.order_no=".TB_PREF."purch_orders.order_no
		";
    if ($supplier_id != '')
        $sql .= " AND ".TB_PREF."purch_orders.supplier_id = ".db_escape($supplier_id);
    $sql .= " ORDER BY ".TB_PREF."purch_order_details.order_no";
    return db_query($sql, "Retreive order Line Items");
}

function get_sql_for_on_time_delivery()
{

    $sql = " SELECT ((quantity_ordered/quantity_received)*100) AS QTY
		FROM 
		 ".TB_PREF."purch_order_details,".TB_PREF."grn_batch
		 
		WHERE 
		".TB_PREF."purch_order_details.order_no = ".TB_PREF."grn_batch.purch_order_no
		AND ".TB_PREF."purch_order_details.delivery_date <= ".TB_PREF."grn_batch.delivery_date
		 ";


    $result = db_query($sql, "Error");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_sql_for_quality($po_detail_item)
{

    $sql = "SELECT  text1
		FROM 
		 ".TB_PREF."grn_items
		 
		WHERE 
		 ".TB_PREF."grn_items.po_detail_item =  ".db_escape($po_detail_item)."
		
		 ";


    $result = db_query($sql, "Error");
    $row = db_fetch_row($result);
    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_supplier_balances()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromsupp = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');

    if ($fromsupp == ALL_TEXT)
        $supp = _('All');
    else
        $supp = get_supplier_name($fromsupp);
    $dec = user_price_dec();

    $cols = array(0, 25, 185, 250,  300, 350,400,440,490,540,590,670,720);

    $headers = array(_('S.No'), _('Product/Services Detail'), _('PO Ref'), _('Ord Date'),
        _('Req Date'), _('Rcv Date'), _('Qty Ord'), _('Qty Rcvd'),
        _('Quality %'), _('Quantity %'), _('On time delivery %'), _('Average %'), _('Overall Rating')
    );

    $aligns = array('left',	'left',	'left',	'left', 'left','left',	'right',	'right',	'right', 'right',	'right',	'right', 'centre');
    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Supplier'), 'from' => $supp, 'to' => ''));

    $rep = new FrontReport(_(''), "SupplierBalancesDetailed", user_pagesize(), 9, $orientation);
    $rep->SetHeaderType('Header220112');
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $total = array();
    $grandtotal = array(0,0,0,0);

    $sql = "SELECT supplier_id, supp_name AS name, curr_code FROM " . TB_PREF . "suppliers WHERE  supplier_id != -1 ";
    if ($fromsupp != ALL_TEXT)
        $sql .= " AND  supplier_id=" . db_escape($fromsupp);


    $sql .= " ORDER BY supp_name";
    $result = db_query($sql, "The customers could not be retrieved");

    $result = get_purch_details_($fromsupp);
    $sr = 1;
    while ($myrow=db_fetch($result))
    {

        $quantity = round2(($myrow['quantity_received']/$myrow['quantity_ordered'])*100);
        $average = round2((get_sql_for_on_time_delivery() + $quantity + get_sql_for_quality($myrow['po_detail_item']))/3);
        $rep->TextCol(0, 1,	$sr++);
        $rep->TextCol(2, 3,	$myrow['reference']);
        $rep->DateCol(3, 4,	$myrow["ord_date"], true);
        $rep->DateCol(4, 5,	$myrow["delivery_date"], true);
        $rep->DateCol(5, 6,	$myrow["DeliveryDate"], true);
        $rep->TextCol(6, 7,	$myrow['quantity_ordered']);
        $rep->TextCol(7, 8,	$myrow['quantity_received']);
        $rep->TextCol(8, 9,	get_sql_for_quality($myrow['po_detail_item'])."%");
        $rep->TextCol(9, 10, $quantity ."%",2);
        $rep->TextCol(10, 11,	get_sql_for_on_time_delivery() ."%");
        $rep->TextCol(11, 12,	$average ."%");
        if($average >= 90)
            $rep->TextCol(12, 13,"    "."Excellent");
        if($average >= 80 && $average <= 89)
            $rep->TextCol(12, 13,"    "."Very Good");
        if($average >= 70 && $average <= 79)
            $rep->TextCol(12, 13,"    "."Good");
        if($average >= 60 && $average <= 69)
            $rep->TextCol(12, 13,"    "."Normal");
        if($average >= 50 && $average <= 59)
            $rep->TextCol(12, 13,"    "."Not Acceptable");

        $str =  $myrow['description'];
        if (strlen($str) > 20)
            $str = substr($str, 0, 30).'...';
        $rep->TextCol(1, 2, $str, $dec);
        $rep->NewLine(1);

        // $rep->TextColLines(1, 2,	$myrow['description']);
        if ($rep->row < $rep->bottomMargin + (7 * $rep->lineHeight))
            $rep->NewPage();

    }
//    $rep->NewLine();
    // $rep->multicell(770,140,"",1,'L',0,0,40,450);
    $rep->setfontsize(15);
    $rep->font('b');
    $rep->TextCol(0, 13, "PERFORMANCE RATING:");
    // $rep->multicell(770,10,"PERFORMANCE RATING:",0,'L',0,0,40,460);
    $rep->setfontsize(11);
    $rep->NewLine(2);
    $rep->TextCol(0, 13, "1. Quality ---- 100% (0% Rejection), 99-90% (01-10% Rejection), 89-80% (11-20% Rejection), 79-70% (21-30% Rejection), 69-60% (30-39% Rejection),");
    // $rep->multicell(770,10,"1. Quality ---- 100% (0% Rejection), 99-90% (01-10% Rejection), 89-80% (11-20% Rejection), 79-70% (21-30% Rejection), 69-60% (30-39% Rejection),",0,'L',0,0,40,490);
    $rep->NewLine(2);
    $rep->TextCol(0, 13, "1. Quantity ---- 100% (10% Shortage), 99-95% (11-15% Shortage), 94-90% (16-20% Shortage), 89-85% (21-25% Shortage), 84-80% (26-30% Shortage),");
    // $rep->multicell(770,10,"1. Quantity ---- 100% (10% Shortage), 99-95% (11-15% Shortage), 94-90% (16-20% Shortage), 89-85% (21-25% Shortage), 84-80% (26-30% Shortage),",0,'L',0,0,40,530);
    $rep->NewLine(2);
    $rep->TextCol(0, 13, "1. On Time Delivery ---- 100% (W/in Time), 99-95% (1-5 Days Delay), 94-90% (6-10 Days Delay), 89-85% (11-15 Days Delay)");
    // $rep->multicell(770,10,"1. On Time Delivery ---- 100% (W/in Time), 99-95% (1-5 Days Delay), 94-90% (6-10 Days Delay), 89-85% (11-15 Days Delay)",0,'L',0,0,40,570);
    $rep->font('');
    $rep->NewLine();
    $rep->End();
}

?>