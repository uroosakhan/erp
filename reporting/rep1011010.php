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

// trial_inquiry_controls();
print_purchase_balances();

/*
function get_open_balance($debtorno, $to, $convert)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight +  t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
//    	((t.ov_amount + t.ov_gst + t.ov_freight +  t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ")) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
*/
function get_sql_for_purch_orders_report($from, $to,$fromsupp)
{

    $sql = "SELECT 
		line.order_no, 
		porder.ord_date, 
		line.description,  
		(line.quantity_ordered) as quantity_ordered,  
		(line.unit_price) as unit_price
	    
		FROM ".TB_PREF."purch_order_details as line,
		 ".TB_PREF."purch_orders as porder
		WHERE 
		porder.order_no = line.order_no";

//    if (isset($trans_no) && $trans_no != "")
//    {
//        // search orders with number like
//        $number_like = "%".$trans_no;
//        $sql .= " AND sorder.order_no LIKE ".db_escape($number_like);
////				." GROUP BY sorder.order_no";
//    }
//    elseif ($ref != "")
//    {
//        // search orders with reference like
//        $number_like = "%".$ref."%";
//        $sql .= " AND sorder.reference LIKE ".db_escape($number_like);
////				." GROUP BY sorder.order_no";
//    }
//    else	// ... or select inquiry constraints
//    {
//        if ($filter!='DeliveryTemplates' && $filter!='InvoiceTemplates' && $filter!='OutstandingOnly')
//        {
            $date_after = date2sql($from);
            $date_before = date2sql($to);

            $sql .=  " AND porder.ord_date >= '$date_after'"
                ." AND porder.ord_date <= '$date_before'";
//        }
//    }
//    if ($trans_type == ST_SALESQUOTE && !check_value('show_all'))
//        $sql .= " AND sorder.delivery_date >= '".date2sql(Today())."' AND line.qty_sent=0"; // show only outstanding, not realized quotes
    if ($fromsupp != '')
        $sql .= " AND porder.supplier_id=".db_escape($fromsupp);
//    if ($selected_customer != -1)
//        $sql .= " AND sorder.debtor_no=".db_escape($selected_customer);
//
//    if (isset($stock_item))
//        $sql .= " AND line.stk_code=".db_escape($stock_item);
//
//    if ($location)
//        $sql .= " AND sorder.from_stk_loc = ".db_escape($location);
//
//    if ($filter=='OutstandingOnly')
//        $sql .= " AND line.qty_sent < line.quantity";
//
//    elseif ($filter=='InvoiceTemplates' || $filter=='DeliveryTemplates')
//        $sql .= " AND sorder.type=1";
//
//    //Chaiatanya : New Filter
//    if ($customer_id != ALL_TEXT)
//        $sql .= " AND sorder.debtor_no = ".db_escape($customer_id);
$sql .= " ";
    return db_query($sql, "Error");
}
function get_open_balance($debtorno, $to)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE." AND t.type <> ".ST_BANKPAYMENT.",
	    	(t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1, 0)) AS credits,
		SUM(t.alloc) AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc),
	    	((t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1 + t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to,$salesgroup)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = " SELECT ".TB_PREF."debtor_trans.*,".TB_PREF."cust_branch.group_no,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans,".TB_PREF."cust_branch
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.branch_code = ".TB_PREF."cust_branch.branch_code
		
		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY." ";

	if ($salesgroup)
		$sql .= " AND  ".TB_PREF."cust_branch.group_no = '$salesgroup'";

    	$sql .= " ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}


/*
function get_transactions($debtorno, $from, $to,$salesgroup)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = " ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight +
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."


		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY." ";


	$sql .= " ORDER BY ".TB_PREF."debtor_trans.tran_date";

	return db_query($sql,"No transactions were returned");
}*/


function get_transactions2($debtorno, $from, $to, $transno)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*, ".TB_PREF."debtor_trans_details.*
		
    	FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtor_trans_details
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans_details.debtor_trans_type  =  ".TB_PREF."debtor_trans.type
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no 
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".db_escape($transno)."
	AND ".TB_PREF."debtor_trans_details.debtor_trans_type = 10	
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}


//----------------------------------------------------------------------------------------------------

function print_purchase_balances()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromsupp = $_POST['PARAM_2'];
    $currency = $_POST['PARAM_3'];
    $no_zeros = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_7'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'L' ;

	if ($fromsupp == ALL_TEXT)
		$supp = _('All');
	else
        $supp = get_customer_name($fromsupp);
    	$dec = user_price_dec();
    if ($currency == ALL_TEXT)
    {
        $convert = true;
        $currency = _('Balances in Home currency');
    }
    else
        $convert = false;

//	if ($area == ALL_NUMERIC)
//		$area = 0;
//
//	if ($area == 0)
//		$sarea = _('All Areas');
//	else
//		$sarea = get_area_name($area);
//
//	if ($folk == ALL_NUMERIC)
//		$folk = 0;
//
//	if ($folk == 0)
//		$salesfolk = _('All Sales Man');
//	else
//		$salesfolk = get_salesman_name($folk);
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 40,   100,  325, 400,  500);

	$headers = array(_('PO.No.'),_('Ord.Date'), _('Description'), _('Qty Ordered'), _('Unit Price'), _('Total Price'));

	//	$headers[7] = _('Balance');


	$aligns = array('left',	'left',	'left',	'left',  'left' );

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $supp,   	'to' => ''),
    				    3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('P.O.Delivery with Unit Price (exclude tax)'), "CustomerBalancesDetailed", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	$deb_sum_total_grandtotal = array(0,0,0,0);
	$a = 0;
	

//	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
//			".TB_PREF."debtors_master.name AS Name
//		FROM ".TB_PREF."debtors_master
//		INNER JOIN ".TB_PREF."cust_branch
//			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
//		INNER JOIN ".TB_PREF."areas
//			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code
//		INNER JOIN ".TB_PREF."salesman
//			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";
//
//		if ($fromcust != ALL_TEXT )
//			{
//				//if ($area != 0 || $folk != 0) continue;
//				$sql .= " WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
//			}
//
//		elseif ($area != 0)
//			{
//				if ($folk != 0)
//					$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
//						AND ".TB_PREF."areas.area_code=".db_escape($area);
//				else
//					$sql .= " WHERE ".TB_PREF."areas.area_code=".db_escape($area);
//			}
//		elseif ($folk != 0 )
//			{
//				$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);
//			}
//
//
//	$sql .= " ORDER BY Name";
//	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;

    $GetRecord = get_sql_for_purch_orders_report( $from, $to, $fromsupp);
    $total = $grandtotal = 0.0;
    $total1 = $grandtotal1 = 0.0;

    $catt = '';
	while ($myrow = db_fetch($GetRecord))
	{
        if($myrow['reference'] == 'auto')
            continue;
        if ($no_zeros && $myrow['unit_price'] == 0)
            continue;

        if ($catt != $myrow['order_no'])
        {
            if ($catt != '')
            {
                $rep->NewLine(2, 3);
                $rep->TextCol(0, 4, _('Total'));
                $rep->AmountCol(3, 4, $total, $dec);
                $rep->AmountCol(5, 6, $total1, $dec);
                $rep->Line($rep->row - 2);
                $rep->NewLine();
                $rep->NewLine();
                $total = $total1 = $total2 = 0.0;
            }

        }
        $catt = $myrow['order_no'];
        $total2=  $myrow['quantity_ordered'] * $myrow['unit_price'];
        $rep->TextCol(0, 1, $myrow['order_no']);
        $rep->TextCol(1, 2, $myrow['ord_date']);
        $rep->TextCol(2, 3, $myrow['description']);
        $rep->TextCol(3, 4, $myrow['quantity_ordered']);
        $rep->TextCol(4, 5, $myrow['unit_price']);
        $rep->TextCol(5, 6, $total2);
//        $rep->TextCol(5, 6,	$myrow['ord_date']);
//        $rep->TextCol(6, 7,	$myrow['delivery_date']);
//        $rep->TextCol(7, 8,	$myrow['deliver_to']);
//        $rep->TextCol(8, 9,	$myrow['OrderValue']);
        $rep->NewLine();
        $total += $myrow['quantity_ordered'];
        $total1 += $total2;
        $grandtotal += $myrow['quantity_ordered'];
        $grandtotal1 += $total2;
	}
    $rep->NewLine();
    $rep->TextCol(0, 4, _('Total'));
    $rep->AmountCol(3, 4, $total, $dec);
    $rep->AmountCol(5, 6, $total1, $dec);
	$rep->Line($rep->row - 2);
    $rep->NewLine();
//    $rep->NewLine(2, 1);
    $rep->TextCol(0, 4, _('Grand Total'));
    $rep->AmountCol(3, 4, $grandtotal, $dec);
    $rep->AmountCol(5, 6, $grandtotal1, $dec);
    	$rep->End();
}

?>