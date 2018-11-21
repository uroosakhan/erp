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

function get_open_balance($supplier_id, $to)
{
	$to = date2sql($to);

    $sql = "SELECT SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS charges,
    	SUM(IF(".TB_PREF."supp_trans.type <> ".ST_SUPPINVOICE." AND ".TB_PREF."supp_trans.type <> ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS credits,
		SUM(".TB_PREF."supp_trans.alloc) AS Allocated,
		SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.",
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount - ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh),
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh))) AS OutStanding
		FROM ".TB_PREF."supp_trans
    	WHERE ".TB_PREF."supp_trans.tran_date < '$to'
		AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id' GROUP BY supplier_id";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
function get_grn_details_($order_no)
{
    $sql = "SELECT ".TB_PREF."grn_items.*, units
		FROM ".TB_PREF."grn_items
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."grn_items.id=".TB_PREF."stock_master.stock_id
		WHERE grn_batch_id =".db_escape($order_no)." ";
//	$sql .= " GROUP BY grn_batch_id";
    $sql .= " ORDER BY grn_batch_id";
    return db_query($sql, "Retreive order Line Items");
}
function get_purch_details_($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.*, ".TB_PREF."purch_orders.dimension
		FROM ".TB_PREF."purch_order_details,".TB_PREF."purch_orders
		LEFT JOIN ".TB_PREF."grn_batch
		ON ".TB_PREF."purch_order_details.order_no=".TB_PREF."grn_batch.purch_order_no
		WHERE ".TB_PREF."purch_order_details.order_no =".db_escape($order_no)." 
		AND ".TB_PREF."purch_order_details.order_no=".TB_PREF."purch_orders.order_no
		";
//	$sql .= " GROUP BY grn_batch_id";
    $sql .= " ORDER BY ".TB_PREF."purch_order_details.order_no";
    return db_query($sql, "Retreive order Line Items");
}
function get_po_details($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.unit_price, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.po_detail_item=".TB_PREF."stock_master.stock_id
		WHERE po_detail_item =".db_escape($order_no)." ";
//	$sql .= " GROUP BY grn_batch_id";
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
}
function get_grn_header($from,$to,$fromsupp)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT * FROM ".TB_PREF."grn_batch
     WHERE delivery_date >= ".db_escape($from)." AND delivery_date <=".db_escape($to)."
      ";
    if ($fromsupp != '' )
        $sql.= " AND supplier_id =".db_escape($fromsupp)." ";

    $result = db_query($sql, "Could not retreive GRN batch id");
    return $result;
}


function getTransactions($supplier_id, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."supp_trans.*,
				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh)
				AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.")
					AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue
    			FROM ".TB_PREF."supp_trans
    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from' AND ".TB_PREF."supp_trans.tran_date <= '$to' 
    			AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id'
    				ORDER BY ".TB_PREF."supp_trans.tran_date";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}

function getTransactions2($supplier_id, $from, $to, $transno)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."supp_trans.*, ".TB_PREF."supp_invoice_items.*,
				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount)
				AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.")
					AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue

    			FROM ".TB_PREF."supp_trans, ".TB_PREF."supp_invoice_items

    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from' 
			AND ".TB_PREF."supp_trans.tran_date <= '$to' 
    			AND ".TB_PREF."supp_trans.supplier_id = ".db_escape($supplier_id)."

		AND ".TB_PREF."supp_invoice_items.supp_trans_type  =  ".TB_PREF."supp_trans.type
		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".TB_PREF."supp_trans.trans_no 
		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".db_escape($transno)."


    				ORDER BY ".TB_PREF."supp_trans.tran_date";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
/////////////////////
function getTransactions_dim($dim_id, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT  25 as type, trans.id as trans_no, trans.reference, item.qty_recd, item.description,
supplier.supp_name, po.requisition_no AS supp_reference, 
'' as due_date, supplier.curr_code, 0 as Balance, '' AS TotalAmount, '' AS Allocated,
 0 as OverDue, 1 as Settled,details.unit_price as price
FROM 0_grn_batch as trans,0_grn_items as item, 0_suppliers as supplier, 0_purch_orders as po
,0_purch_order_details as details
WHERE supplier.supplier_id = trans.supplier_id
AND  details.po_detail_item = item.po_detail_item
AND  trans.id = item.grn_batch_id
AND trans.purch_order_no = po.order_no
AND trans.delivery_date >= '$from' 
AND trans.delivery_date <= '$to'";
if ($dim_id != 0)
		$sql .= "AND po.dimension =".db_escape($dim_id);
   // GROUP BY po.dimension
    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}

//----------------------------------------------------------------------------------------------------

function print_supplier_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
        $dim_id = $_POST['PARAM_2'];
//    	$currency = $_POST['PARAM_3'];
//    	$no_zeros = $_POST['PARAM_4'];
    	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if ($dim_id == 0)
		$dim = _('All');
	else
		$dim = get_dimension($dim_id);
    	$dec = user_price_dec();

//	if ($currency == ALL_TEXT)
//	{
//		$convert = true;
//		$currency = _('Balances in Home currency');
//	}
//	else
//		$convert = false;

//	if ($no_zeros) $nozeros = _('Yes');
//	else $nozeros = _('No');

//	$cols = array(0, 50, 120, 190,  230, 265, 315, 370, 425, 475, 525);
//
//	$headers = array(_('No.'), _('Date/Narration'), _(''), _('Rate'), _('Qty'), _('Discount'),
//		_('Total'), 	_('Payments'), _('Bill'), _('Balance'));
//
//	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right', 'right', 'right');
    $cols = array(0, 100, 230, 400,  450, 515);

    $headers = array(_('Reference'), _('Suplier'), _('Item Name'), _('Quantity'), _('Total'));

    $aligns = array('left',	'left',	'left',	'right', 'right');
    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			2 => array('text' => _('Supplier'), 'from' => $dim, 'to' => ''));

    $rep = new FrontReport(_('Job Detail Report'), "SupplierBalancesDetailed", user_pagesize());

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array();
	$grandtotal = array(0,0,0,0);

	$sql = "SELECT id, name FROM ".TB_PREF."dimensions WHERE type_=1";
	if ($dim_id != 0)
		$sql .= " AND id=".db_escape($dim_id);
	$sql .= " ORDER BY name";
	$result = db_query($sql, "The customers could not be retrieved");

   /* $result = get_grn_header($from,$to,$fromsupp);
	while ($myrow=db_fetch($result))
	{

        $rep->TextCol(0, 1,	get_supplier_name($myrow['supplier_id']));
        $rep->NewLine(1, 2);

        $result1=get_grn_details_($myrow['id']);
        while ($myrow1=db_fetch($result1)) {
            $unit_price=get_purch_details_($myrow['purch_order_no']);
            $grn_batch =db_fetch($unit_price);
            $rep->TextCol(1, 2,	$grn_batch['dimension']);
            $rep->TextCol(2, 3,	$myrow1['description']);
            $rep->TextCol(3, 4,	$myrow1['qty_recd']);
            $rep->TextCol(4, 5,	$grn_batch['unit_price']);
            $rep->NewLine(1);

        }

        $rep->Line($rep->row  - 4);
        $rep->NewLine();


	}*/
////dime
    while ($myrow=db_fetch($result)) {
        $rep->fontSize += 2;
        $rep->TextCol(0, 1, $myrow['name']);
        $rep->fontSize -= 2;
        $rep->NewLine(1, 2);
        $result2 = getTransactions_dim($myrow['id'], $from, $to);
        while ($myrow1 = db_fetch($result2)) {
            // $unit_price=get_purch_details_($myrow['purch_order_no']);
            // $grn_batch =db_fetch($unit_price);
            //$rep->TextCol(1, 2,	$grn_batch['dimension']);
            $rep->TextCol(0, 1, $myrow1['reference']);
            $rep->TextCol(1, 2, $myrow1['supp_name']);
            $rep->TextCol(2, 3, $myrow1['description']);
            $rep->TextCol(3, 4, number_format2($myrow1['qty_recd'],get_qty_dec($_POST['stock_id'])), -2);
            $rep->TextCol(4, 5, number_format2($myrow1['qty_recd'] * $myrow1['price'],get_qty_dec($_POST['stock_id'])),-2);

            //$rep->TextCol(4, 5, $myrow1['description']);
            $rep->NewLine(1);

        }
        $rep->NewLine(1);
    }
//	$rep->Font();
//	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

?>