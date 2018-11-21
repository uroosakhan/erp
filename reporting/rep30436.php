<?php
$page_security = 'SA_SALESANALYTIC';
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

print_inventory_sales();

function getTransactions($category, $location, $fromcust, $item, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."stock_moves.trans_no,
    ".TB_PREF."stock_master.category_id,
    ".TB_PREF."stock_category.description AS cat_description,
    ".TB_PREF."stock_master.stock_id, 
    ".TB_PREF."stock_master.description,
    ".TB_PREF."stock_master.inactive,
    ".TB_PREF."stock_moves.loc_code,
    ".TB_PREF."stock_moves.type,
    ".TB_PREF."debtors_master.name AS debtor_name,
    ".TB_PREF."stock_moves.reference as ref,
    ".TB_PREF."stock_moves.tran_date,
    ".TB_PREF."stock_moves.qty AS qty,
    ".TB_PREF."stock_moves.price*(1- ".TB_PREF."stock_moves.discount_percent) AS price
    FROM ".TB_PREF."stock_master, ".TB_PREF."stock_category, ".TB_PREF."stock_moves
    
    LEFT JOIN ".TB_PREF."debtor_trans ON 
    (".TB_PREF."stock_moves.type=".TB_PREF."debtor_trans.type 
    AND ".TB_PREF."stock_moves.trans_no=".TB_PREF."debtor_trans.trans_no
    
    )
    
    LEFT JOIN ".TB_PREF."debtors_master ON 
    (".TB_PREF."debtor_trans.debtor_no=".TB_PREF."debtors_master.debtor_no
    )
    
    WHERE ".TB_PREF."stock_master.stock_id= ".TB_PREF."stock_moves.stock_id
    AND ".TB_PREF."stock_master.category_id= ".TB_PREF."stock_category.category_id 
    AND ".TB_PREF."stock_moves.tran_date>= '$from'
    AND ".TB_PREF."stock_moves.tran_date<= '$to' 
   
    
    ";

    if ($fromcust != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromcust);

    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);

    $sql .= " AND ( ".TB_PREF."stock_moves.type=11 OR ".TB_PREF."stock_moves.type=13 OR ".TB_PREF."stock_moves.type=17)";

    if ($location != '')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);

    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);

    $sql .= "  
    ORDER BY
 ".TB_PREF."stock_moves.type,
 ".TB_PREF."stock_master.category_id, 
 ".TB_PREF."stock_moves.tran_date";

    return db_query($sql,"No transactions were returned");

}

function get_item_price_30436($debtor_trans_no,$debtor_trans_type,$stock_id)
{
    $sql = "SELECT unit_price
    FROM  ".TB_PREF."debtor_trans_details
    WHERE  debtor_trans_no =  $debtor_trans_no
    AND stock_id = ".db_escape($stock_id)."
    AND debtor_trans_type = $debtor_trans_type";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_invoice_reference_($trans_no,$type)
{
    $sql = "SELECT order_ FROM ".TB_PREF."debtor_trans 
    WHERE type = ".db_escape($type)."
	AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_invoice_referencess_($trans_no)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans 
    WHERE type = 10
	AND order_ =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_credit_referencess_($trans_no,$type)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = ".db_escape($type)."
	
	AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
//function get_party_name($trans_no,$type)
//{
//    $sql = "SELECT ".TB_PREF."debtors_master.name
//    FROM ".TB_PREF."debtors_master,".TB_PREF."debtor_trans
//    WHERE ".TB_PREF."debtor_trans.type = ".db_escape($type)."
//	AND ".TB_PREF."debtor_trans.trans_no =".db_escape($trans_no)."
//	AND ".TB_PREF."debtor_trans.debtor_no = ".TB_PREF."debtors_master.debtor_no";
//    $result = db_query($sql, "error");
//    $row = db_fetch_row($result);
//    return $row[0];
//
//}
function get_purchase_price_($stock_id)
{
    $sql = "SELECT unit_price FROM ".TB_PREF."supp_invoice_items
    WHERE supp_trans_type = 20
	AND stock_id =".db_escape($stock_id)."
	ORDER BY supp_trans_no DESC ";
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$items = $_POST['PARAM_2'];
	$category = $_POST['PARAM_3'];
    $location = $_POST['PARAM_4'];
    $fromcust = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];
	$destination = $_POST['PARAM_8'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

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

	if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);

	$cols = array(0, 50, 140, 240, 300, 350, 400, 480, 525);

	$headers = array(_('Inv. #'), _('Party'), _('Product'), _('Qty'),  _('Date'),  _('Sales Price'), _('Purchasing Cost'), _('Amount'));
	if ($fromcust != '')
		$headers[2] = '';

	$aligns = array('left',	'left', 'left', 'right', 'right','right','right','right','right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Sales Invoice Register'), "InventorySalesReport", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
	$res = getTransactions($category, $location, $fromcust,$items, $from, $to);
	$total = $grandtotal = 0.0;
	$total1 = $grandtotal1 = 0.0;
	$total2 = $grandtotal2 = 0.0;
    $grandtotal_qty = 0.0;
	$total_qty = 0.0;
	$catt = '';
	while ($trans=db_fetch($res))
	{


        $rep->fontSize -= 2;
        $order_no = get_invoice_reference_($trans['trans_no'],$trans['type']);

        if($trans['type']==13) {
            $purch_price = get_purchase_price_($trans['stock_id']);
            $reference = get_invoice_referencess_($order_no);
        }
        elseif($trans['type']==11) {
            $purch_price = get_purchase_price_($trans['stock_id']);
            $reference = get_credit_referencess_($trans['trans_no'], $trans['type']);
        }
        else{
            $purch_price = 0;
            $reference = $trans['ref'];
        }
        $curr = get_customer_currency($trans['debtor_no']);
		$rate = get_exchange_rate_from_home_currency($curr, sql2date($trans['tran_date']));
        $rep->TextCol(0, 1,$reference);


		$rep->TextCol(1, 2, $trans['debtor_name']);
        $rep->TextCol(2, 3, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);

        $rep->AmountCol(3, 4, abs($trans['qty']), get_qty_dec($trans['stock_id']));
        $rep->DateCol(4, 5, $trans['tran_date'], true);
//		$rep->AmountCol(4, 5, $trans['price'], $dec);
        $sales_price = get_item_price_30436($trans['trans_no'],$trans['type'],$trans['stock_id']);
        if(!user_check_access('SA_ITEMSPRICES'))
        {
            $rep->AmountCol(5, 6, abs($sales_price), $dec);
            $rep->AmountCol(6, 7, $purch_price, $dec);
            $net_total = $trans['qty'] * $trans['price'];
            $rep->AmountCol(7, 8, abs($net_total), get_qty_dec($trans['stock_id']), $dec);
        }
		$rep->fontSize += 2;
        $rep->NewLine();
		$grandtotal += $sales_price;
		$grandtotal2 += $net_total;
		$grandtotal_qty += $trans['qty'];  // qty
	} //while 

	$rep->Line($rep->row - 2);
	$rep->NewLine();
	$rep->TextCol(0, 4, _('Grand Total'));
	$rep->AmountCol(3, 4, abs($grandtotal_qty), get_qty_dec($trans['stock_id']), $dec);  // qty
    if(!user_check_access('SA_ITEMSPRICES')) {
        $rep->AmountCol(5, 6, abs($grandtotal), get_qty_dec($trans['stock_id']), $dec);
        $rep->AmountCol(7, 8, abs($grandtotal2), get_qty_dec($trans['stock_id']), $dec);
    }

	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

?>