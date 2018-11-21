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

function GetSalesOrders($from, $to, $category=0, $location=null, $backorder=0, $items, $delivered)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);
    if($delivered == 0)
    {
        $sql= "SELECT sorder.order_no,
				sorder.debtor_no,
				sorder.reference,
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
                details.`quantity` AS invoiced
            FROM ".TB_PREF."sales_orders sorder
	           	INNER JOIN ".TB_PREF."sales_order_details line
            	    ON sorder.order_no = line.order_no
            	    AND sorder.trans_type = line.trans_type
            	    AND sorder.trans_type = ".ST_SALESORDER."
            	INNER JOIN ".TB_PREF."stock_master item
            	    ON line.stk_code = item.stock_id
            	LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.`order_` = line.`order_no`
                   LEFT JOIN ".TB_PREF."debtor_trans_details details ON details.`debtor_trans_no` = trans.`trans_no`
                   AND details.`debtor_trans_type` = trans.type 
                   AND details.`debtor_trans_type` = '10'  
            WHERE sorder.ord_date >='$fromdate'
                AND sorder.ord_date <='$todate'";
    }
    elseif($delivered == 1)
    {
        $sql= "SELECT sorder.order_no,
				sorder.debtor_no,
				sorder.reference,
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
    }
    elseif($delivered == 2)
    {
        $sql= "SELECT sorder.order_no,
				sorder.debtor_no,
				sorder.reference,
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
                line.unit_price
                
                FROM ".TB_PREF."sales_orders sorder
	           	INNER JOIN ".TB_PREF."sales_order_details line
            	    ON sorder.order_no = line.order_no
            	    AND sorder.trans_type = line.trans_type
            	    AND sorder.trans_type = ".ST_SALESORDER."
            	    AND (line.quantity - line.qty_sent) > 0
            	INNER JOIN ".TB_PREF."stock_master item
            	    ON line.stk_code = item.stock_id
            WHERE sorder.ord_date >='$fromdate'
                AND sorder.ord_date <='$todate'";
    }
	if ($category > 0)
		$sql .= " AND item.category_id=".db_escape($category);
	if ($location != null)
		$sql .= " AND sorder.from_stk_loc=".db_escape($location);
	if ($backorder)
		$sql .= " AND line.quantity - line.qty_sent > 0";
	 if ($items != 0)
        $sql .= " AND line.stk_code=".db_escape($items);	
	$sql .= " ORDER BY sorder.order_no";

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
	$delivered = $_POST['PARAM_9'];
	$destination = $_POST['PARAM_10'];
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
	$cols = array(0, 60, 150, 240,310, 350,	440, 530, 590, 660);

	$headers2 = array(_('Order'), _('Customer'), _('Branch'), _('Customer Ref'), _(' '),
		_('Ord Date'),	_('Del Date'),	_('Loc'), _('Price'),	_('Total'));

	$aligns = array('left',	'left',	'right', 'right','right', 'right', 'right',	'right',  'right');

	$headers = array(_('Code'),	_('Description'), _('Ordered'),	_('Delivered'),	_('Invoiced'),
		_('Outstanding'), '', '', '', '');

    $params =   array( 	0 => $comments,
	    				1 => array(  'text' => _('Period'), 'from' => $from, 'to' => $to),
	    				2 => array(  'text' => _('Category'), 'from' => $cat,'to' => ''),
	    				3 => array(  'text' => _('Location'), 'from' => $loc, 'to' => ''),
	    				4 => array(  'text' => _('Selection'),'from' => $back,'to' => ''));

    if($groupby_items ==0)
    {
        $cols = array(0, 50, 320, 370, 430, 500, 560, 610, 660);
        $aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right');

        $headers2 = array(_('Code'), _('Description'), _('Stock'), _('Order'), _('Delivered'),
            _('Invoiced'),	_('ShortFall'), _(' % '));
        $headers ='';
    }

	$aligns2 = $aligns;

	$rep = new FrontReport(_('Order Status Listing'), "OrderStatusListing", user_pagesize(), 9, $orientation);
//    if ($orientation == 'L')
//    	recalculate_cols($cols);
	$cols2 = $cols;
	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns, $cols2, $headers2, $aligns2);

	$rep->NewPage();
	$orderno = 0;
    $tot_unitprice = 0;
    $total = 0;
    $dec = 0;

    if($groupby_items!=0)
        $result = GetSalesOrders($from, $to, $category, $location, $backorder, $items, $delivered);
    else
        $result = GetSalesOrders2($from, $to, $category, $location, $backorder, $items);

    if($groupby_items!=0)
    {
        while ($myrow=db_fetch($result))
        {

            if ($orderno != $myrow['order_no'] )
            {
                if ($orderno != 0 )
                {
                    $rep->NewLine();
                    $rep->TextCol(7, 8, "Total");
                    $rep->AmountCol(8, 9, $tot_unitprice, $dec);
                    $rep->AmountCol(9, 10, $total, $dec);
                    $tot_unitprice = 0;
                    $total = 0;
                    $rep->Line($rep->row - 2);
                    $rep->NewLine(1.2);
                }
                $rep->TextCol(0, 1,	$myrow['reference']);
                $rep->TextCol(1, 2,	get_customer_name($myrow['debtor_no']));
                $rep->TextCol(2, 3,	get_branch_name($myrow['branch_code']));
                $rep->TextCol(3, 4,	$myrow['customer_ref']);
                $rep->TextCol(4, 5,	' ');
                $rep->DateCol(5, 6,	$myrow['ord_date'], true);
                $rep->DateCol(6, 7,	$myrow['delivery_date'], true);
                $rep->TextCol(7, 8,	$myrow['from_stk_loc']);
                $rep->NewLine(2);
                $orderno = $myrow['order_no'];
            }
            $rep->TextCol(0, 1,	$myrow['stk_code']);
            $rep->TextCol(1, 2,	$myrow['description']);
            $dec = get_qty_dec($myrow['stk_code']);
            $rep->AmountCol(2, 3, $myrow['quantity'], $dec);
            $rep->AmountCol(3, 4, $myrow['qty_sent'], $dec);
            $rep->AmountCol(4, 5,	$myrow['invoiced'], $dec);
            $rep->AmountCol(5, 6, $myrow['quantity'] - $myrow['qty_sent'], $dec);
            $rep->AmountCol(8, 9, $myrow['unit_price'], $dec);
            $grand_unitprice += $myrow['unit_price'];
            $rep->AmountCol(9, 10, $myrow['quantity'] * $myrow['unit_price'], $dec);
            $grand_total += $myrow['quantity'] * $myrow['unit_price'];
            $tot_unitprice += $myrow['unit_price'];
            $total += $myrow['quantity'] * $myrow['unit_price'];
            if ($myrow['quantity'] - $myrow['qty_sent'] > 0)
            {
                $rep->Font('italic');
                $rep->TextCol(6, 7,	_('Outstanding'));
                $rep->Font();
            }
//        $rep->NewLine();
//        $rep->Line($rep->row);
//        $rep->NewLine(2);/
//        $rep->TextCol(6, 7, "Total");
//        $rep->AmountCol(7, 8, $tot_unitprice, $dec);
            $rep->NewLine();
        }
        $rep->NewLine();
        $rep->TextCol(7, 8, "Total");
        $rep->AmountCol(8, 9, $tot_unitprice, $dec);
        $rep->AmountCol(9, 10, $total, $dec);
        $rep->Line($rep->row - 2);
        $rep->NewLine();
        $rep->TextCol(7, 8, "Grand Total");
        $rep->AmountCol(8, 9, $grand_unitprice, $dec);
        $rep->AmountCol(9, 10, $grand_total, $dec);
        $rep->NewLine(1);
        $rep->End();
    }else
    {


        while ($myrow=db_fetch($result)) {
            $qoh = qoh_date_wise($myrow['stk_code'],$from,$to);
            $rep->TextCol(2, 3, $qoh);
            $rep->TextCol(0, 1, $myrow['stk_code']);
            $rep->TextCol(1, 2, $myrow['description']);
            $dec = get_qty_dec($myrow['stk_code']);
            $rep->AmountCol(3, 4, $myrow['qty_ordered'], $dec);
            $rep->AmountCol(4, 5, $myrow['qty_delivered'], $dec);
            $rep->AmountCol(5, 6, $myrow['invoiced'], $dec);
            $rep->AmountCol(6, 7, $myrow['qty_ordered'] - $myrow['qty_delivered'], $dec);
//            $rep->AmountCol(7, 8, $myrow['priced'], $dec);

            $grand_qty_ordered += $myrow['qty_ordered'];
            $grand_qty_delivered += $myrow['qty_delivered'];
            $grand_invoiced += $myrow['invoiced'];


            $percent = ($myrow['qty_ordered'] -$myrow['qty_delivered']) /$myrow['qty_ordered'] * 100;
            $rep->AmountCol(7, 8,  $percent."%" , $dec);

            $grand_percent += $percent;

            $grand_total += $myrow['quantity'] * $myrow['unit_price'];
            $tot_unitprice += $myrow['unit_price'];
            $total += $myrow['quantity'] * $myrow['unit_price'];
//            if ($myrow['quantity'] - $myrow['qty_sent'] > 0) {
//                $rep->Font('italic');
//                $rep->TextCol(6, 7, _('Outstanding'));
//                $rep->Font();
//            }
//        $rep->NewLine();
//        $rep->Line($rep->row);
//        $rep->NewLine(2);/
//        $rep->TextCol(6, 7, "Total");
//        $rep->AmountCol(7, 8, $tot_unitprice, $dec);
            $rep->NewLine();
        }
        $rep->NewLine();

        $rep->Line($rep->row - 2);
        $rep->NewLine();
        $rep->TextCol(2, 3, "Grand Total");
        $rep->TextCol(3, 4, number_format2($grand_qty_ordered,2));
        $rep->TextCol(4, 5, number_format2($grand_qty_delivered,2));
        $rep->TextCol(5, 6, number_format2($grand_invoiced,2));
//        $rep->AmountCol(7, 8, $tot_unitprice, $dec);
        $rep->AmountCol(7, 8, $grand_percent, $dec);
//        $rep->TextCol(2, 3, "Grand Total");
//        $rep->TextCol(3, 4, "Total ORDER");
//        $rep->TextCol(4, 5, "Delivery");
//        $rep->TextCol(5, 6, "Invoiced");
//        $rep->AmountCol(7, 8, $grand_unitprice, $dec);
//        $rep->AmountCol(8, 9, $grand_total, $dec);
//        $rep->NewLine(1);
        $rep->End();
    }


}

