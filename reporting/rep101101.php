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
function get_sql_for_purch_orders_report_count($fromsupp)
{

    $sql = " SELECT COUNT(*) as record
		FROM 
		 ".TB_PREF."purch_orders as porder
		WHERE 
		porder.supplier_id = $fromsupp";

    $result = db_query($sql, "Error");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_sql_for_purch_orders_report($from, $to,$fromsupp,$reference,$cat)
{

    $sql = " SELECT
		line.order_no, 
		porder.ord_date, 
		porder.reference, 
		line.description,  
		(line.quantity_ordered) as quantity_ordered,  
		(line.quantity_received) as quantity_received,
		line.con_factor,
		line.units_id,
		porder.supplier_id
	    
		FROM ".TB_PREF."purch_order_details as line,
		 ".TB_PREF."purch_orders as porder,
		  ".TB_PREF."stock_master as sm
		WHERE 
		porder.order_no = line.order_no
		AND line.item_code = sm.stock_id
		
		";



    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND porder.ord_date >= '$date_after'"
        ." AND porder.ord_date <= '$date_before'";

    if ($fromsupp != '')
        $sql .= " AND porder.supplier_id=".db_escape($fromsupp);

    if ($reference != '')
        $sql .= " AND porder.order_no=".db_escape($reference);

    if ($cat != -1)
        $sql .= " AND sm.category_id = ".db_escape($cat)."";


    return db_query($sql, "Error");
}








function get_sql_for_purch_orders_report_check($from, $to,$fromsupp,$reference,$cat)
{

    $sql = " SELECT
		line.order_no, 
		porder.ord_date, 
		porder.reference, 
		line.description,  
		(line.quantity_ordered) as quantity_ordered,  
		(line.quantity_received) as quantity_received,
		line.con_factor,
		line.units_id,
		porder.supplier_id
	    
		FROM ".TB_PREF."purch_order_details as line,
		 ".TB_PREF."purch_orders as porder,
		  ".TB_PREF."stock_master as sm
		WHERE 
		porder.order_no = line.order_no
		AND line.item_code = sm.stock_id
		
		";



    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND porder.ord_date >= '$date_after'"
        ." AND porder.ord_date <= '$date_before'";

    if ($fromsupp != '')
        $sql .= " AND porder.supplier_id=".db_escape($fromsupp);

    if ($reference != '')
        $sql .= " AND porder.order_no=".db_escape($reference);

    if ($cat != -1)
        $sql .= " AND sm.category_id = ".db_escape($cat)."";



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
    $reference = $_POST['PARAM_2'];
    $cat = $_POST['PARAM_3'];

    $fromsupp = $_POST['PARAM_4'];
    $currency = $_POST['PARAM_5'];
    $no_zeros = $_POST['PARAM_6'];
    $comments = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_9'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = 'L';

    if ($fromsupp == ALL_TEXT)
        $supp = _('All');
    else
        $supp = get_supplier_name($fromsupp);
    $dec = user_price_dec();
    if ($currency == ALL_TEXT) {
        $convert = true;
        $currency = _('Balances in Home currency');
    } else
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

    $cols = array(0, 40, 100, 250, 300, 350, 410, 460, 510);

    $headers = array(_('PO.No.'), _('Ord.Date'), _('Description'), _('Pri. Qty Ord.'), _('Pri. Qty Rec.'), _('Pri. Qty Balance'), _('Sec. Qty Ord.'), _('Sec. Qty Rec.'), _('Sec. Qty Balance'));

    //	$headers[7] = _('Balance');


    $aligns = array('left', 'left', 'left', 'right', 'right', 'right', 'right', 'right', 'right');

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Customer'), 'from' => $supp, 'to' => ''),
        3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
        4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_(' Order - Detailed'), "CustomerBalancesDetailed", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $grandtotal = array(0, 0, 0, 0);
    $deb_sum_total_grandtotal = array(0, 0, 0, 0);
    $a = 0;
    $sql = "SELECT supplier_id, supp_name AS name, curr_code FROM " . TB_PREF . "suppliers WHERE  supplier_id != -1 ";
    if ($fromsupp != ALL_TEXT)
        $sql .= " AND  supplier_id=" . db_escape($fromsupp);
        
        
        if (!$convert )
        $sql .= " AND curr_code  = " . db_escape($currency);
        
    $sql .= " ORDER BY supp_name";
    $result = db_query($sql, "The customers could not be retrieved");


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


    $grandtotal = $grandtotal1 = $grandtotal2 =$grandtotal3 =  $grandtotal4 =$grandtotal5 =0.0;


    $catt = '';
    $order_no = "";
    while ($myrow=db_fetch($result))
    {
        $count=get_sql_for_purch_orders_report_count($myrow['supplier_id']);
        $GetRecord1 = get_sql_for_purch_orders_report_check($from, $to, $myrow['supplier_id'],$reference,$cat);
        if(db_num_rows($GetRecord1) == 0)
            continue;
        $rep->NewLine(2, 3);
        if($count == 0)
            continue;


        $rep->TextCol(0, 4, $myrow['name']);
//        $rep->TextCol(0, 4, $count);
        $rep->NewLine(2);
        $pry_ord_tot="";
        $pry_rec_tot="";
        $pry_bal="";
        $sec_ord_tot="";
        $sec_rec_tot="";
        $sec_bal="";

        $GetRecord = get_sql_for_purch_orders_report($from, $to, $myrow['supplier_id'],$reference,$cat);
        while ($myrow = db_fetch($GetRecord)) {
if(!$myrow)
    continue;

        $pry_ord_tot += $myrow['quantity_ordered'];
        $pry_rec_tot += $myrow['quantity_received'];
        $pry_bal += $myrow['quantity_ordered'] - $myrow['quantity_received'];
//        $sec_total += $myrow['quantity_ordered'];
//        $sec_total += $myrow['quantity_ordered'];
        if ($myrow['reference'] == 'auto')
            continue;
        if ($no_zeros && $myrow['quantity_ordered'] == 0)
            continue;

////
//        if ($catt != $myrow['order_no']) {
//
//
//            if ($catt != '') {
//                $rep->NewLine(2, 3);
//                $rep->TextCol(0, 4, _('Total'));
//
//                $rep->AmountCol(3, 4, $pry_ord_tot, $dec);
//                $rep->AmountCol(4, 5, $pry_rec_tot, $dec);
//                $rep->AmountCol(5, 6, $pry_bal, $dec);
//                $rep->AmountCol(6, 7, $sec_ord_tot, $dec);
//                $rep->AmountCol(7, 8, $sec_rec_tot, $dec);
//                $rep->AmountCol(8, 9, $sec_bal, $dec);
//                $rep->Line($rep->row - 2);
//                $rep->NewLine();
//                $rep->NewLine();
//                $pry_ord_tot = $pry_rec_tot = $pry_bal = $total3 = $total4 = $total5 = 0.0;
//
//
//            }
//
//        }
//        $catt = $myrow['order_no'];
//
            $rep->TextCol(0, 1, $myrow['reference']);
            $rep->TextCol(1, 2, $myrow['ord_date']);
            $rep->TextCol(2, 3, $myrow['description']);
            $rep->AmountCol(3, 4, $myrow['quantity_ordered'], $dec);
            $rep->AmountCol(4, 5, $myrow['quantity_received'], $dec);
            $rep->AmountCol(5, 6, $myrow['quantity_ordered'] - $myrow['quantity_received'], $dec);

        $pref = get_company_prefs();
        if ($pref['alt_uom'] == 1) {
            $item = get_item($myrow["item_code"]);
            $dec = 2;
            if ($myrow['units_id'] != $item['units']) {
                if ($item['con_type'] == 0) {
//                    $rep->NewLine();
                    $qty = round2($myrow["quantity_ordered"] * $myrow['con_factor'], $dec);
                    $rep->AmountCol(6, 7, $qty, $dec);
                    $sec_ord_tot += $qty;
                    $quantity_received = round2($myrow["quantity_received"] * $myrow['con_factor'], $dec);
                    $rep->AmountCol(7, 8, $quantity_received, $dec);
                    $sec_rec_tot += $quantity_received;
                    $rep->AmountCol(8, 9, $qty - $quantity_received, $dec);
                    $sec_bal += $qty - $quantity_received;
                }
            }
        }
            $rep->NewLine();
            $grandtotal +=  $myrow['quantity_ordered'];
            $grandtotal1 += $myrow['quantity_received'];
            $grandtotal2 += $myrow['quantity_ordered'] - $myrow['quantity_received'];
            $grandtotal3 +=  $myrow["quantity_ordered"] * $myrow['con_factor'];
            $grandtotal4 += $myrow["quantity_received"] * $myrow['con_factor'];
            $grandtotal5 += $qty - $quantity_received;
//        $rep->NewLine();
//        $pri_bal = $myrow['quantity_ordered'] - $myrow['quantity_received'];
//        $sec_bal = $qty - $quantity_received;
//        $total += $myrow['quantity_ordered'];
//        $total1 += $myrow['quantity_received'];
//        $total2 += $pri_bal;
//        $total3 += $qty;
//        $total4 += $quantity_received;
//        $total5 += $sec_bal;


        }
        $rep->NewLine();
        $rep->TextCol(0, 4, _('Total'));
        $rep->AmountCol(3, 4, $pry_ord_tot, $dec);
        $rep->AmountCol(4, 5, $pry_rec_tot, $dec);
        $rep->AmountCol(5, 6, $pry_bal, $dec);
        $rep->AmountCol(6, 7, $sec_ord_tot, $dec);
        $rep->AmountCol(7, 8, $sec_rec_tot, $dec);
        $rep->AmountCol(8, 9, $sec_bal, $dec);
        $rep->Line($rep->row - 2);
    }
//    $rep->NewLine();
//    $rep->TextCol(0, 4, _('Total'));


    $rep->Line($rep->row - 2);
    $rep->NewLine();
//    $rep->NewLine(2, 1);
    $rep->TextCol(0, 4, _('Grand Total'));
    $rep->AmountCol(3, 4, $grandtotal, $dec);
    $rep->AmountCol(4, 5, $grandtotal1, $dec);
    $rep->AmountCol(5, 6, $grandtotal2, $dec);
    $rep->AmountCol(6, 7, $grandtotal3, $dec);
    $rep->AmountCol(7, 8, $grandtotal4, $dec);
    $rep->AmountCol(8, 9, $grandtotal5, $dec);
    $rep->End();
}

?>