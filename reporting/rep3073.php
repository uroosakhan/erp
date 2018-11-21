<?php
$page_security = 'SA_ITEMSMOVREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Jujuk
// date_:	2011-05-24
// Title:	Stock Movements
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui/ui_input.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

inventory_movements();


function get_domestic_price3073($myrow, $stock_id)
{
    if ($myrow['type'] == ST_SUPPRECEIVE || $myrow['type'] == ST_SUPPCREDIT)
    {
        $price = $myrow['price'];
        if ($myrow['person_id'] > 0)
        {
            // Do we have foreign currency?
            $supp = get_supplier($myrow['person_id']);
            $currency = $supp['curr_code'];
            $ex_rate = get_exchange_rate_to_home_currency($currency, sql2date($myrow['tran_date']));
            $price /= $ex_rate;
        }
    }
    else
        $price = $myrow['standard_cost']; // Item Adjustments just have the real cost
    return $price;
}

function getAverageCost3073($stock_id, $location, $from_date, $to_date, $type)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);
    $to_date = date2sql($to_date);

    $sql = "SELECT move.*, IF(ISNULL(supplier.supplier_id), debtor.debtor_no, supplier.supplier_id) person_id
  		FROM ".TB_PREF."stock_moves move
				LEFT JOIN ".TB_PREF."supp_trans credit ON credit.trans_no=move.trans_no AND credit.type=move.type
				LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=move.trans_no AND 25=move.type
				LEFT JOIN ".TB_PREF."suppliers supplier ON IFNULL(grn.supplier_id, credit.supplier_id)=supplier.supplier_id
				LEFT JOIN ".TB_PREF."debtor_trans cust_trans ON cust_trans.trans_no=move.trans_no AND cust_trans.type=move.type
				LEFT JOIN ".TB_PREF."debtors_master debtor ON cust_trans.debtor_no=debtor.debtor_no
			WHERE stock_id=".db_escape($stock_id)."
			AND standard_cost > 0.001 AND qty <> 0 AND move.type <> ".ST_LOCTRANSFER;
    if ($type == 'from')
    $sql .= " AND move.tran_date < '$from_date'";
    if ($type == 'to')
    $sql .= " AND move.tran_date <= '$to_date'";

    if ($location != '')
        $sql .= " AND move.loc_code = ".db_escape($location);

    $sql .= " ORDER BY tran_date";

    $result = db_query($sql, "No standard cost transactions were returned");

    if ($result == false)
        return 0;
    $qty = $tot_cost = 0;
    while ($row=db_fetch($result))
    {
        $qty += $row['qty'];
        $price = get_domestic_price3073($row, $stock_id);
        $tran_cost = $row['qty'] * $price;
        $tot_cost += $tran_cost;
    }
    if ($qty == 0)
        return 0;
    return $tot_cost / $qty;
}

function fetch_items($category=0)
{
		$sql = "SELECT stock_id, stock.description AS name,
				stock.category_id,
				units,
				cat.description,
				stock.text1,
				stock.material_cost AS UnitCost
			FROM ".TB_PREF."stock_master stock LEFT JOIN ".TB_PREF."stock_category cat ON stock.category_id=cat.category_id
				WHERE mb_flag <> 'D' AND mb_flag <>'F'";
		if ($category != 0)
			$sql .= " AND cat.category_id = ".db_escape($category);
		$sql .= " ORDER BY stock.category_id";

    return db_query($sql,"No transactions were returned");
}

//function trans_qty($stock_id, $location=null, $from_date, $to_date, $inward = true)
//{
//	if ($from_date == null)
//		$from_date = Today();
//
//	$from_date = date2sql($from_date);
//
//	if ($to_date == null)
//		$to_date = Today();
//
//	$to_date = date2sql($to_date);
//
//	$sql = "SELECT ".($inward ? '' : '-')."SUM(qty) FROM ".TB_PREF."stock_moves
//		WHERE stock_id=".db_escape($stock_id)."
//		AND tran_date >= '$from_date'
//		AND tran_date <= '$to_date'";
//
//	if ($location != '')
//		$sql .= " AND loc_code = ".db_escape($location);
//
//	if ($inward)
//		$sql .= " AND qty > 0 ";
//	else
//		$sql .= " AND qty < 0 ";
//
//	$result = db_query($sql, "QOH calculation failed");
//
//	$myrow = db_fetch_row($result);
//
//	return $myrow[0];
//
//}

function opening_qty($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date < '$from_date' ";
	//	AND tran_date <= '$to_date'";

   // $sql .= " AND type = ".db_escape(ST_INVADJUST);

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Opening Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}

function purchases_qty($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_SUPPRECEIVE).",".db_escape(ST_SUPPCREDIT)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Purchases Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function purchases_values_invoice($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty*price) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_SUPPRECEIVE)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function purchases_values_credit($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty*price) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_SUPPCREDIT)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function cogs_sales($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(-1*qty*standard_cost) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_CUSTDELIVERY).",".db_escape(ST_CUSTCREDIT)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function sales_qty($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(-1*qty) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_CUSTDELIVERY).",".db_escape(ST_CUSTCREDIT)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function sales_values_invoice($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty*price) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_CUSTDELIVERY)." )";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function sales_values_credit($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(details.quantity*details.unit_price) FROM ".TB_PREF."debtor_trans_details details, ".TB_PREF."debtor_trans trans
		WHERE details.stock_id=".db_escape($stock_id)."
		AND trans.tran_date >= '$from_date' 
		AND trans.tran_date <= '$to_date'";

    $sql .= " AND trans.trans_no = details.debtor_trans_no ";
    $sql .= " AND trans.type = details.debtor_trans_type ";
    $sql .= " AND trans.type IN ( ".db_escape(ST_CUSTCREDIT)." )";

    if ($location != '')
        $sql .= " AND details.item_location = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function adjustment_qty($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type = ".db_escape(ST_INVADJUST)." ";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}

function adjustment_values_positive($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty*standard_cost) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_INVADJUST)." )";

    $sql .= " AND qty > 0 ";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function adjustment_values_negative($stock_id, $location=null, $from_date, $to_date)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(qty*standard_cost) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

    $sql .= " AND type IN ( ".db_escape(ST_INVADJUST)." )";

    $sql .= " AND qty < 0 ";

    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);

    $result = db_query($sql, "Sales Qty calculation failed");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}
function get_demand_asm_qty_custom($stock_id, $location)
{

    $sql = " SELECT SUM(line.quantity-line.qty_sent) AS Demmand
		   FROM ".TB_PREF."sales_order_details line,
				".TB_PREF."sales_orders sorder,
				".TB_PREF."stock_master item
		   WHERE sorder.order_no = line.order_no
		   		AND sorder.trans_type=".ST_SALESORDER." 
		   		AND sorder.trans_type=line.trans_type
				AND line.quantity-line.qty_sent > 0
				AND item.stock_id=line.stk_code";
    if ($location != "")
        $sql .= " AND sorder.from_stk_loc =".db_escape($location);

    $sql .= " AND item.stock_id = ".db_escape($stock_id);

    $sql .= " GROUP BY line.stk_code";
    $result = db_query($sql, "No transactions were returned");
    $myrow = db_fetch_row($result);
//    while ($row = db_fetch_row($result)) {
//        $demand_qty += stock_demand_manufacture($row[0], $row[1], $stock_id, $location);
//    }
    return $myrow[0];
}

//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root;
    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
	$location = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ('L');
	if ($category == ALL_NUMERIC)
		$category = 0;
	if ($category == 0)
		$cat = _('All');
	else
		$cat = get_category_name($category);
	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);
	$cols = array(0, 30, 80, 100, 130, 170, 210, 255, 285, 325, 360, 395, 430, 470, 510, 550, 580);
	$headers = array(_('Item Name'), _('Part No.'),	_('Category'),	_('Opening Qty'),	_('Opening Value'),
                     _('Purchases Qty'), _('Purchases Value'), _('Sales Qty'), _('Sales Value'), _('COGS'),
                     _('Net Adj Qty'), _('Net Adj Value'), _('Closing Qty'), _('Closing Value'), _('Qty on PO\'s'),
                     _('Qty on SO\'s'));
	$aligns = array('left',	'left',	'left', 'right', 'right', 'right','right','right','right','right','right',
                    'right','right','right','right','right');
    $params =   array( 	0 => '',
						1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
						3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));
    $rep = new FrontReport(_('Item Activity Report'), "ItemActivityReport", 'A4_CUSTOM', 6, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
	$result = fetch_items($category);
    $PurchaseTotal = $SalesTotal = $AdjustmentTotal = 0.0;
    $total_opening_qty=$total_opening_value=$total_purchases_qty=$total_purchases_value=$total_sales_qty=$total_sales_total=$total_cogs_value=$totalAdjustmentTotal=$totaladjustment_qty=$total_closing_qty=$total_demand_qty=$totalonorder=$total_closing_value=0.0;
	while ($myrow=db_fetch($result))
	{
//		if ($catgor != $myrow['description'])
//		{
//			$rep->Line($rep->row  - $rep->lineHeight);
//			$rep->NewLine(2);
//			$rep->fontSize += 2;
//			$rep->TextCol(0, 3, $myrow['description']);
//			$catgor = $myrow['description'];
//			$rep->fontSize -= 2;
//			$rep->NewLine();
//		}
//		$rep->NewLine();
// if($destination==0){
// 		$rep->TextCol(0, 1,	$myrow['stock_id']);
// }
// else{
    
    
    		$rep->TextCol(0, 1,	$myrow['name']);

// }
		
		$rep->TextCol(1, 2, $myrow['text1']);
//		$rep->TextCol(1, 2, $myrow['UnitCost']);

        $rep->TextCol(2, 3, $myrow['description']);
//		$rep->TextCol(2, 3, $myrow['units']);
        $opening_qty = $purchases_qty = $purchases_qty_invoice = $purchases_qty_credit = $sales_qty =
        $sales_values_invoice = $sales_values_invoice = $sales_values_credit = $adjustment_qty = $closing_qty =
        $demandqty = $onorder = $adjustment_qty_positive = $adjustment_qty_negative = $cogs_sales = 0;
        $opening_qty += opening_qty($myrow['stock_id'], $location, $from_date, $to_date); // Type = 17
        $purchases_qty += purchases_qty($myrow['stock_id'], $location, $from_date, $to_date); // Type = 25 & 21
        $purchases_qty_invoice += purchases_values_invoice($myrow['stock_id'], $location, $from_date, $to_date); // Type = 25 & 21
        $purchases_qty_credit += purchases_values_credit($myrow['stock_id'], $location, $from_date, $to_date); // Type = 25 & 21
        $sales_qty += sales_qty($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $cogs_sales += cogs_sales($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $sales_values_invoice += sales_values_invoice($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $sales_values_credit += sales_values_credit($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $adjustment_qty += adjustment_qty($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $adjustment_qty_positive += adjustment_values_positive($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $adjustment_qty_negative += adjustment_values_negative($myrow['stock_id'], $location, $from_date, $to_date); // Type = 13 & 11
        $closing_qty += get_qoh_on_date($myrow['stock_id'], $location, $to_date); // NO Type
        $demandqty += get_demand_asm_qty_custom($myrow['stock_id'], $location);
        $onorder = get_on_porder_qty($myrow['stock_id'], $location);
		$rep->AmountCol(3, 4, $opening_qty, get_qty_dec($myrow['stock_id']));
		
		$total_opening_qty +=$opening_qty;
		
		
//
		
		
		
		$rep->AmountCol(5, 6, $purchases_qty, get_qty_dec($myrow['stock_id']));
       
       $total_purchases_qty+=$purchases_qty;
       
        $PurchaseTotal = abs($purchases_qty_invoice) - abs($purchases_qty_credit);
//
	
	
	$total_purchases_value+=$PurchaseTotal;
	
		$rep->AmountCol(7, 8, $sales_qty, get_qty_dec($myrow['stock_id']));
        
        $total_sales_qty+=$sales_qty;
        
        
        $SalesTotal = abs($sales_values_invoice) - abs($sales_values_credit);
//
	
	$total_sales_total+=$SalesTotal;
	
	
	
//		$total_cogs_value +=$sales_qty*$myrow['UnitCost'];
		$total_cogs_value +=$cogs_sales;

		
		$rep->AmountCol(10, 11, $adjustment_qty, get_qty_dec($myrow['stock_id']));
		
		
		$totaladjustment_qty+=$adjustment_qty;
		
		
        $AdjustmentTotal = abs($adjustment_qty_positive) - abs($adjustment_qty_negative);
//
		
				$totalAdjustmentTotal+=$AdjustmentTotal;

		
		$rep->AmountCol(12, 13, $closing_qty, get_qty_dec($myrow['stock_id']));
	
	
		$total_closing_qty+=$closing_qty;
        if(!user_check_access('SA_ITEMSPRICES')) {
            
             $UnitCostOpening = getAverageCost3073($myrow['stock_id'], $location, $from_date, $to_date, 'from');
                      $op_value = $opening_qty * $UnitCostOpening;

             $UnitCostClosing = getAverageCost3073($myrow['stock_id'], $location, $from_date, $to_date, 'to');
                      $closing_value = $closing_qty * $UnitCostClosing;
                   //     $closing_value = $op_value + $PurchaseTotal -  $cogs_sales + $AdjustmentTotal;

            $rep->AmountCol(4, 5, $op_value, get_qty_dec($myrow['stock_id']));
            $rep->AmountCol(6, 7, $PurchaseTotal, get_qty_dec($myrow['stock_id']));
            $rep->AmountCol(8, 9, $SalesTotal, get_qty_dec($myrow['stock_id']));
    		$rep->AmountCol(9, 10, $cogs_sales, get_qty_dec($myrow['stock_id']));
            
            $rep->AmountCol(11, 12, $AdjustmentTotal, get_qty_dec($myrow['stock_id']));
/*            $closing_qty_value = ($opening_qty * $myrow['UnitCost']) + $PurchaseTotal - $SalesTotal - $AdjustmentTotal;
            $closing_value = $op_value + $PurchaseTotal - ($sales_qty*$myrow['UnitCost']) - $AdjustmentTotal;
            */
            $rep->AmountCol(13, 14, $closing_value, get_qty_dec($myrow['stock_id']));
        }
		
		$total_opening_value+=$op_value;
	
		$total_closing_value+=$closing_value;
		
		$rep->AmountCol(14, 15, $onorder, get_qty_dec($myrow['stock_id']));
/*
$diff_ = ($opening_qty * $myrow['UnitCost']) + 
$PurchaseTotal * $myrow['UnitCost']
-$sales_qty*$myrow['UnitCost'] -
- $AdjustmentTotal * $myrow['UnitCost']
 - $closing_qty * $myrow['UnitCost'];

		$rep->AmountCol(14, 15, $diff_, get_qty_dec($myrow['stock_id'])); //dz
  */     
       $totalonorder+=$onorder;
       
        $rep->AmountCol(15, 16, $demandqty, get_qty_dec($myrow['stock_id']));
	
	$total_demand_qty+=$demandqty;
	
	
		$rep->NewLine();
	}	
	
	$rep->NewLine();

	
		$rep->Line($rep->row  + 8);

	
	   $rep->TextCol(0, 4, _('Total'));
               $rep->AmountCol(3, 4, abs($total_opening_qty), $dec);
                $rep->AmountCol(4, 5, abs($total_opening_value), $dec);
                $rep->AmountCol(5, 6, abs($total_purchases_qty), $dec);
                $rep->AmountCol(6, 7, abs($total_purchases_value), $dec);
                 $rep->AmountCol(7, 8, abs($total_sales_qty), $dec);
                 $rep->AmountCol(8, 9, abs($total_sales_total), $dec);
                 $rep->AmountCol(9, 10, abs($total_cogs_value), $dec);
                 $rep->AmountCol(10, 11, abs($totaladjustment_qty), $dec);
                 $rep->AmountCol(11, 12, abs($totalAdjustmentTotal), $dec);

                 $rep->AmountCol(12, 13,abs($total_closing_qty), $dec);
                 $rep->AmountCol(13, 14,abs($total_closing_value), $dec);
                 $rep->AmountCol(14, 15,abs($totalonorder), $dec);

                   $rep->AmountCol(15, 16,abs($total_demand_qty), $dec);

                
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}