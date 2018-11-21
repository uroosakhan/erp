<?php

$page_security = 'SA_ITEMSPURREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Inventory Sales Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_purchase();
function get_supp_name($debtor_no)
{
    $sql = "SELECT `supp_name` from ".TB_PREF."suppliers where `supplier_id`=".$debtor_no;
    $db = db_query($sql);
    $ft = db_fetch($db);
    return $ft[0];
}
function getTransactions($category, $location, $date)
{
    $date = date2sql($date);
    $sql = "SELECT *, master_.stock_id, SUM(moves.qty) as qty, 
            SUM(price.price*moves.qty) as StockAmount, 
            (master_.category_id) as StockCategory 
            FROM 0_stock_master master_ 
            LEFT JOIN 0_stock_moves moves ON master_.stock_id = moves.stock_id 
            LEFT JOIN 0_prices price ON moves.stock_id = price.stock_id 
            WHERE master_.material_cost > 0 
            AND master_.mb_flag<>'D' 
            AND master_.mb_flag <> 'F' 
            AND moves.tran_date <= '$date'";

//    $sql = "SELECT item.category_id,
//    item.stock_id,
//    category.description AS cat_description,
//    category.short_name As supplier_id,
//    SUM(move.qty) AS StockQty,
//    SUM(move.qty * item.material_cost) AS StockValue,
//    SUM(move.price) AS StockPrice
//    FROM 0_stock_moves move,
//    0_stock_master item, 0_stock_category category
// WHERE item.stock_id=move.stock_id
//		AND item.category_id=category.category_id
//		AND item.mb_flag<>'D' AND mb_flag <> 'F'
//		AND move.tran_date <= '$date'
//		GROUP BY item.category_id";

    if ($category != -1)
        $sql .= " AND  master_.category_id = ".db_escape($category);
    if ($location != '')
        $sql .= " AND move.loc_code = ".db_escape($location);
//    if ($fromsupp != '')
//        $sql .= " AND supplier.supplier_id = ".db_escape($fromsupp);
//    if ($item != '')
//        $sql .= " AND item.stock_id = ".db_escape($item);

    $sql .= "GROUP BY master_.stock_id
            
            HAVING SUM(moves.qty) != 0";


    return db_query($sql,"No transactions were returned");

}

function get_supplier_balances($supplier_id,  $to)
{
    // $from = date2sql($from);
    $to = date2sql($to);
//    $sql = "SELECT  Sum(IFNULL(IF (trans.type=20 OR trans.type=2 OR trans.type=42, (trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh - trans.alloc), (trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh + trans.alloc)),0)) AS Balance FROM 0_suppliers supp
//LEFT JOIN 0_supp_trans trans ON supp.supplier_id = trans.supplier_id
//    AND trans.tran_date<='$to',
//     0_payment_terms
//WHERE supp.payment_terms = 0_payment_terms.terms_indicator
//AND supp.supplier_id=$supplier_id";
    $sql = " SELECT  Sum(IFNULL((trans.ov_amount + trans.ov_gst + trans.ov_discount- ( supply_disc + service_disc + fbr_disc + srb_disc) ),0)) AS Balance
FROM 0_suppliers supp 
LEFT JOIN 0_supp_trans trans ON supp.supplier_id = trans.supplier_id 
AND trans.tran_date <= '$to', 0_payment_terms
WHERE supp.payment_terms = 0_payment_terms.terms_indicator 
AND supp.supplier_id = $supplier_id ";

    $result = db_query($sql, "Retreive currency of supplier $supplier_id");

    $myrow=db_fetch_row($result);
    return $myrow[0];
}
function get_supp_inv_reference($supplier_id, $stock_id, $date)
{
    $sql = "SELECT trans.supp_reference
		FROM ".TB_PREF."supp_trans trans,
			".TB_PREF."supp_invoice_items line,
			".TB_PREF."grn_batch batch,
			".TB_PREF."grn_items item
		WHERE trans.type=line.supp_trans_type
		AND trans.trans_no=line.supp_trans_no
		AND item.grn_batch_id=batch.id
		AND item.item_code=line.stock_id
		AND trans.supplier_id=".db_escape($supplier_id)."
		AND line.stock_id=".db_escape($stock_id)."
		AND trans.tran_date=".db_escape($date);
    $result = db_query($sql,"No transactions were returned");
    $row = db_fetch_row($result);
    if (isset($row[0]))
        return $row[0];
    else
        return '';
}
function get_item_price_3047($stock_id)
{
    $sql = "SELECT  SUM(price) as StockPrice
FROM  `".TB_PREF."prices` 
WHERE  `stock_id` =  '$stock_id'
AND  `sales_type_id` = 1";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_category_items($category_id)
{
    $sql = "SELECT  stock_id
FROM  `".TB_PREF."stock_master` 
WHERE  `category_id` =  '$category_id'";

    return db_query($sql, "could not get customer");

}
//----------------------------------------------------------------------------------------------------

function print_inventory_purchase()
{
    global $path_to_root;

    $date = $_POST['PARAM_0'];
    $category = $_POST['PARAM_1'];
    $location = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
    $orientation = $_POST['PARAM_4'];
    $destination = $_POST['PARAM_5'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

//    if ($category == ALL_NUMERIC)
//        $category = 0;
//    if ($category == 0)
//        $cat = _('All');
//    else
//        $cat = get_category_name($category);

    if ($location == '')
        $loc = _('All');
    else
        $loc = get_location_name($location);

    if ($fromsupp == '')
        $froms = _('All');
    else
        $froms = get_supplier_name($fromsupp);

    if ($item == '')
        $itm = _('All');
    else
        $itm = $item;

    $cols = array(0, 40, 180,  260, 350, 425, 490,	520);

    $headers = array(_('Sr #'), _('Company Name'), _('Total Quantity Of Stock'), _('Total Value Of Stock'), _('Supplier Balance'), _('Difference'), _('%'));
    if ($fromsupp != '')
        $headers[4] = '';

    $aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
        1 =>  array('text' => _('End Date'), 'from' => $date, 		'to' => ''),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Purchasing Report'), "InventoryPurchasingReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $sql = "SELECT * FROM ".TB_PREF."stock_category ";
    if($category != -1)
        $sql .= " WHERE category_id = ".db_escape($category);
    $sql .= " ORDER BY short_name";
    $result = db_query($sql, "Error");
    $sr = 0;
    $total = $total_supp = 0.0;
    $total_qty = 0.0;
    $tot_stock = $tot_diff = $tot_supp_bal = $tot_qty = $per = 0.0;
    $grandtotal = $grandtotal1 = $grandtotal2 = $grandtotal3 = 0.0;
    $catt = $short_name = $stock_id = '';
    $TotalPrice = $TotalQty = 0;
    $sr=0;
    while ($GetCategory=db_fetch($result)) {
        $TotalPrice = $TotalQty = 0;
        $res = getTransactions($GetCategory['category_id'], $location, $date);

        while ($trans = db_fetch($res)) {

            $TotalPrice += $trans['StockAmount'];
            $TotalQty += $trans['qty'];
        }
        $supp_bal_duplicate = get_supplier_balances($GetCategory['short_name'], $date);
        if ($catt != $GetCategory['short_name'])
        {
            if ($catt != '')
            {
                if($TotalQty == 0)
                continue;
                $tot_diff1 = $tot_stock - $supp_bal;
                $tot_diff2 = $tot_diff1/$supp_bal;
                $per = $tot_diff2*100;
                $rep->NewLine(1, 2);
                $rep->TextCol(0, 4, _('Total'));
                $rep->AmountCol(3, 4, $tot_stock, $dec);
                $rep->AmountCol(4, 5,$supp_bal, $dec);
                $rep->AmountCol(5, 6, $tot_diff1, $dec);
                $rep->AmountCol(6, 7, $per, $dec);
                $grandtotal += $supp_bal_duplicate;
                $rep->Line($rep->row - 2);
                $rep->NewLine();
                $rep->NewLine();
                $tot_stock = $tot_qty = $tot_supp_bal = $tot_diff = $supp_bal = $per = 0.0;
            }
//            $rep->NewLine();
            $rep->TextCol(0, 3, get_supp_name($GetCategory['short_name']));

            $catt = $GetCategory['short_name'];

            $rep->NewLine();
        }
        if($TotalQty == 0)
            continue;
        $rep->TextCol(0, 1, $sr = $sr+1);
        $rep->TextCol(1, 2, $GetCategory['description']);
        $rep->AmountCol(2, 3, $TotalQty, $dec);
        $rep->AmountCol(3, 4, $TotalPrice, $dec);
        $rep->NewLine();
        $tot_stock += $TotalPrice;
        $tot_qty += $TotalQty;
        $supp_bal = get_supplier_balances($GetCategory['short_name'], $date);
        // $supp_bal_tot += get_supplier_balances($GetCategory['short_name'], $date);
        $tot_diff =  $TotalPrice - get_supplier_balances($GetCategory['short_name'], $date);
//      $grandtotal1 += $tot_stock - $supp_bal_tot;
        $tot_diff1 = $tot_stock - $supp_bal;
        $grandtotal3 += $TotalPrice;
        
        $grandtotal1 =  $grandtotal3 - $grandtotal;
        $catt = $GetCategory['short_name'];
    }
    $sr++;
    $rep->NewLine(1, 2);

    $rep->Line($rep->row - 2);
    $rep->TextCol(0, 4, _('Total'));
    $rep->AmountCol(3, 4, $tot_stock, $dec);
    $rep->AmountCol(4, 5,$supp_bal, $dec);
    $rep->AmountCol(5, 6, $tot_diff1, $dec);
    $rep->AmountCol(6, 7, $per, $dec);

    $rep->Line($rep->row - 2);
	$rep->NewLine();
	$rep->NewLine(2, 1);
	$rep->TextCol(0, 4, _('Grand Total'));
	$rep->AmountCol(3, 4, $grandtotal3, $dec);
	$rep->AmountCol(4, 5, $grandtotal, $dec);
	$rep->AmountCol(5, 6, $grandtotal1, $dec);

	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

