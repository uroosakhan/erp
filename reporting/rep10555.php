<?php

$page_security = 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Order Status List
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_order_status_list();

//----------------------------------------------------------------------------------------------------

function get_sales_order_header_group_wise($from, $to)
{
    $fromdate = date2sql($from);
    $todate = date2sql($to);

    $sql = "SELECT sorder.*,
	  cust.name,
	  cust.curr_code,
	  cust.address,
	  loc.location_name,
	  cust.discount,
	  stype.sales_type,
	  stype.id AS sales_type_id,
	  stype.tax_included,
	  stype.factor,
 	  ship.shipper_name,
	  tax_group.name AS tax_group_name,
	  tax_group.id AS tax_group_id,
	  cust.tax_id,
	  cust.ntn_no,
	  sorder.alloc,
	    sorder.debtor_no,
	    sorder.dimension_id,
	    sorder.h_text1, 
	    sorder.h_combo1,
	    sorder.h_text2,
	  branch.tax_group_id,
	  IFNULL(allocs.ord_allocs, 0)+IFNULL(inv.inv_allocs ,0) AS sum_paid,
	  sorder.prep_amount>0 as prepaid, sorder.discount1,sorder.discount2,sorder.disc1,sorder.disc2
	FROM ".TB_PREF."sales_orders sorder
			LEFT JOIN (SELECT trans_no_to, sum(amt) ord_allocs FROM ".TB_PREF."cust_allocations
				WHERE trans_type_to=".ST_SALESORDER." AND trans_no_to=".db_escape($order_no)." GROUP BY trans_no_to)
				 allocs ON sorder.trans_type=".ST_SALESORDER." AND allocs.trans_no_to=sorder.order_no
			LEFT JOIN (SELECT order_, sum(alloc) inv_allocs FROM ".TB_PREF."debtor_trans 
				WHERE type=".ST_SALESINVOICE." AND order_=".db_escape($order_no)."  GROUP BY order_)
				 inv ON sorder.trans_type=".ST_SALESORDER." AND inv.order_=sorder.order_no
			LEFT JOIN ".TB_PREF."shippers ship ON  ship.shipper_id = sorder.ship_via,"
        .TB_PREF."debtors_master cust,"
        .TB_PREF."sales_types stype, "
        .TB_PREF."tax_groups tax_group, "
        .TB_PREF."cust_branch branch,"
        .TB_PREF."locations loc
	WHERE sorder.order_type=stype.id
		AND branch.branch_code = sorder.branch_code
		AND branch.tax_group_id = tax_group.id
		AND sorder.debtor_no = cust.debtor_no
		AND loc.loc_code = sorder.from_stk_loc
		AND sorder.ord_date >='$fromdate'
        AND sorder.ord_date <='$todate'
		AND sorder.trans_type = " . db_escape(30);

    $sql .= " GROUP BY sorder.order_no";
    return db_query($sql, "Error getting order details");


}

function get_sales_order_details_group($order_no, $trans_type) {
    $sql = "SELECT id, stk_code, unit_price,
				line.description,
				line.quantity,
				line.units_id,
				line.con_factor,
				line.text1,
				line.text2,
				line.text3,
				line.text4,
				line.text5,
				line.text6,
				line.text7,
				line.amount1,
				line.amount2,
				line.amount3,
				line.amount4,
				line.amount5,
				line.amount6,
				line.date1,
				line.date2,
				line.date3,
				line.combo1,
				line.combo2,
				line.combo3,
				line.combo4,
				line.combo5,
				line.combo6,
				line.batch,
				line.item_location,
				discount_percent,
				qty_sent as qty_done,
				item.units,
				item.category_id,
			
				item.mb_flag,
			item.combo1 as Combo1,
				item.material_cost,
				line.order_no,
				line.bonus
			FROM ".TB_PREF."sales_order_details line,"
        .TB_PREF."stock_master item
			WHERE line.stk_code = item.stock_id
				AND order_no =".db_escape($order_no)
        ." AND trans_type = ".db_escape($trans_type) . " ORDER BY id";

    return db_query($sql, "Retreive order Line Items");
}

function GetSalesOrders($from, $to, $category=0, $location=null, $backorder=0, $items)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);

	$sql= "SELECT sorder.order_no,
				sorder.debtor_no,
                sorder.branch_code,
                sorder.customer_ref,
                sorder.ord_date,
                sorder.from_stk_loc,
                sorder.delivery_date,
                line.stk_code,
                item.description,
                item.units,
                line.quantity,
                line.qty_sent,
                line.unit_price,
                line.bonus,
                sorder.disc1,
                details.`quantity` AS invoiced
            FROM ".TB_PREF."sales_orders sorder
	           	INNER JOIN ".TB_PREF."sales_order_details line
            	    ON sorder.order_no = line.order_no
            	    AND sorder.trans_type = line.trans_type
            	    AND sorder.trans_type = ".ST_SALESORDER."
            	INNER JOIN ".TB_PREF."stock_master item
            	    ON line.stk_code = item.stock_id
            	INNER JOIN ".TB_PREF."debtor_trans trans ON trans.`order_` = line.`order_no`
                   INNER JOIN ".TB_PREF."debtor_trans_details details ON details.`debtor_trans_no` = trans.`trans_no`
                   AND details.`debtor_trans_type` = trans.type 
                   AND details.`debtor_trans_type` = '10'
    
            	    
            	    
            WHERE sorder.ord_date >='$fromdate'
                AND sorder.ord_date <='$todate'";
	if ($category > 0)
		$sql .= " AND item.category_id=".db_escape($category);
	if ($location != null)
		$sql .= " AND sorder.from_stk_loc=".db_escape($location);
	if ($backorder)
		$sql .= " AND line.quantity - line.qty_sent > 0";
	 if ($items != 0)
        $sql .= " AND line.stk_code=".db_escape($items);	
	//$sql .= " GROUP BY sorder.order_no,line.stk_code";

	return db_query($sql, "Error getting order details");
}
function GetSalesOrders2($from, $to, $category=0, $location=null, $backorder=0, $items)
{
    $fromdate = date2sql($from);
    $todate = date2sql($to);

    $sql= "SELECT line.stk_code, item.description,
  SUM(line.quantity) AS qty_ordered, SUM(line.qty_sent) AS qty_delivered, SUM(line.unit_price) AS priced , 
  SUM(details.`quantity`) AS invoiced
  
            FROM ".TB_PREF."sales_orders sorder
	           	INNER JOIN ".TB_PREF."sales_order_details line
            	    ON sorder.order_no = line.order_no
            	    AND sorder.trans_type = line.trans_type
            	    AND sorder.trans_type = ".ST_SALESORDER."
            	INNER JOIN ".TB_PREF."stock_master item
            	    ON line.stk_code = item.stock_id
            	INNER JOIN ".TB_PREF."debtor_trans trans ON trans.`order_` = line.`order_no`
                   INNER JOIN ".TB_PREF."debtor_trans_details details ON details.`debtor_trans_no` = trans.`trans_no`
                   AND details.`debtor_trans_type` = trans.type 
                   AND details.`debtor_trans_type` = '10'
    
            	    
            	    
            WHERE sorder.ord_date >='$fromdate'
                AND sorder.ord_date <='$todate'";
    if ($category > 0)
        $sql .= " AND item.category_id=".db_escape($category);
    if ($location != null)
        $sql .= " AND sorder.from_stk_loc=".db_escape($location);
    if ($backorder)
        $sql .= " AND line.quantity - line.qty_sent > 0";
    if ($items != 0)
        $sql .= " AND line.stk_code=".db_escape($items);
    $sql .= " GROUP BY line.`stk_code`";

    return db_query($sql, "Error getting order details");
}

//----------------------------------------------------------------------------------------------------

function print_order_status_list()
{
	global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$items = $_POST['PARAM_2'];
	$category = $_POST['PARAM_3'];
	$location = $_POST['PARAM_4'];
	$backorder = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];
	$groupby_items = $_POST['PARAM_8'];
	$destination = $_POST['PARAM_9'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$orientation = ('L');

	if ($category == ALL_NUMERIC)
		$category = 0;
	if ($location == ALL_TEXT)
		$location = null;
	if ($category == 0)
		$cat = _('All');
	else
		$cat = get_category_name($category);
	if ($location == null)
		$loc = _('All');
	else
		$loc = get_location_name($location);
	if ($backorder == 0)
		$back = _('All Orders');
	else
		$back = _('Back Orders Only');

    $cols = array(0, 125, 170, 226, 279, 349, 409, 480 , 555 , 620 , 700 , 750);

    $aligns = array('left',	'left', 'left', 'left','left', 'left', 'left', 'left', 'left', 'left', 'left');

    $headers = array(_('Product'), _('Sales'), _('Free'), _('Price'), _('Gross Amt.'),_('T/O Amt.%'), _('Discount'), _('Net Amt.'), _('CR Bill')
    , _('Cheque'), _('Cash'), _('S/R'));
    $params =   array( 	0 => $comments,
	    				1 => array(  'text' => _('Period'), 'from' => $from, 'to' => $to),
	    				2 => array(  'text' => _('Category'), 'from' => $cat,'to' => ''),
	    				3 => array(  'text' => _('Location'), 'from' => $loc, 'to' => ''),
	    				4 => array(  'text' => _('Selection'),'from' => $back,'to' => ''));





	$rep = new FrontReport(_('Booking Summary '), "BookingSummary", user_pagesize(), 9, $orientation);

	$cols2 = $cols;
	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);

	$rep->NewPage();

    $dec = 0;

    if($groupby_items!=0)



$res=get_sales_order_header_group_wise($from, $to);
    while ($myrow1=db_fetch($res)) {
        $rep->Font('bold');
        $rep->TextCol(0, 1, $myrow1['debtor_no'] ."              ".$myrow1['name']);
        $rep->NewLine();
        $rep->Font('');
        $result = get_sales_order_details_group($myrow1['order_no'],30);
        $total = 0.0;
        $total1= 0.0;
        $total2 = 0.0;
        $total4 = 0.0;
        $total3=0.0;
        $total_qty = 0.0;
        $rep->Line($rep->row - 2);
        while ($myrow = db_fetch($result)) {


            $rep->NewLine();
            $price = abs($myrow['unit_price']);
            $sales = abs($myrow['quantity']);
            $bonus = abs($myrow['bonus']);
            $GrossAmt = abs($myrow['quantity'] * $myrow['unit_price']);
            $discount = abs(($myrow['disc1'] / 100) * $GrossAmt);
            $NetAmt = abs($GrossAmt - $discount);
            $rep->TextCol(0, 1, $myrow['description']);
            $rep->TextCol(1, 2, $sales);
            $rep->AmountCol(2, 3, $bonus, $dec);
            $dec = get_qty_dec($myrow['stk_code']);

            $rep->AmountCol(3, 4, $price, $dec);
            $rep->AmountCol(4, 5, $GrossAmt, $dec);
            $rep->AmountCol(5, 6, $myrow['disc1'], $dec);
            $rep->AmountCol(6, 7, $discount, $dec);
            $rep->AmountCol(7, 8, $NetAmt, $dec);
            $rep->Line($rep->row - 2);
            $total1 += $price;
            $total2 += $GrossAmt;
            $total3 += $NetAmt;
            $total4 += $bonus;
            $total_qty += $sales;



        }

//        $rep->Line($rep->row - 2);
//        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->Font('bold');
        $rep->TextCol(0, 3, _('Total'));
        $rep->Font('');
        $rep->AmountCol(1, 2, $total_qty, $dec);  // qty
        $all_totalsqty += $total_qty;

        $rep->AmountCol(2, 3, $total4, $dec);  // qty
        $all_totals4 += $total4;
        //$rep->AmountCol(4, 5, $total, $dec);
        $rep->AmountCol(3, 4, $total1, $dec);
        $all_totals1 += $total1;

        $rep->AmountCol(4, 5, $total2, $dec);
        $all_totals2 += $total2;
        $rep->AmountCol(7, 8, $total3, $dec);
        $all_totals3 += $total3;
        $rep->Line($rep->row  - 4);
        $rep->NewLine(2);
    }////1st loop



        $rep->Line($rep->row - 2);
        $rep->NewLine();
    $rep->Font('bold');
    $rep->TextCol(0, 3, "Grand Total");
    $rep->Font('');
    $rep->AmountCol(1, 2, $all_totalsqty, $dec);
    $rep->AmountCol(2, 3, $all_totals4, $dec);
    $rep->AmountCol(3, 4, $all_totals1, $dec);
    $rep->AmountCol(4, 5, $all_totals2, $dec);
    $rep->AmountCol(7, 8, $all_totals3, $dec);
        $rep->NewLine(1);
        $rep->End();



}

