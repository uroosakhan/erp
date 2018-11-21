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

function getTransactions($category, $location, $fromcust, $item, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."stock_moves.trans_no,
    ".TB_PREF."stock_moves.reference as ref,
    ".TB_PREF."stock_master.category_id,
    ".TB_PREF."stock_category.description AS cat_description,
     ".TB_PREF."stock_master.stock_id, 
     ".TB_PREF."stock_master.text1,
     ".TB_PREF."cust_branch.area,
     ".TB_PREF."cust_branch.tax_group_id,
     ".TB_PREF."stock_master.description,
     ".TB_PREF."stock_master.inactive,
     ".TB_PREF."stock_moves.loc_code,
     ".TB_PREF."stock_moves.type, 
     ".TB_PREF."debtors_master.debtor_no,
     ".TB_PREF."debtors_master.name, 
     ".TB_PREF."stock_moves.tran_date,
     ".TB_PREF."stock_moves.qty AS qty,
     ".TB_PREF."debtors_master.name AS debtor_name,
     ".TB_PREF."debtors_master.debtor_ref,
     ".TB_PREF."debtor_trans.ov_discount,
     ".TB_PREF."debtor_trans.salesman,
     ".TB_PREF."debtor_trans.trans_no as TransNo,
     ".TB_PREF."stock_moves.price*(1- ".TB_PREF."stock_moves.discount_percent) AS price,
     (-IF(".TB_PREF."stock_moves.standard_cost <> 0, 
	".TB_PREF."stock_moves.qty * ".TB_PREF."stock_moves.standard_cost, 
	".TB_PREF."stock_moves.qty *".TB_PREF."stock_master.material_cost)) AS cost
 FROM ".TB_PREF."stock_master, ".TB_PREF."stock_category, 
 ".TB_PREF."stock_moves
 
 LEFT JOIN ".TB_PREF."debtor_trans ON 
    (".TB_PREF."stock_moves.type=".TB_PREF."debtor_trans.type 
    AND ".TB_PREF."stock_moves.trans_no=".TB_PREF."debtor_trans.trans_no
    )
    
    LEFT JOIN ".TB_PREF."debtors_master ON 
    (".TB_PREF."debtor_trans.debtor_no=".TB_PREF."debtors_master.debtor_no
    )
    
    LEFT JOIN ".TB_PREF."cust_branch ON 
    (".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
    )
 
 
 WHERE ".TB_PREF."stock_master.stock_id= ".TB_PREF."stock_moves.stock_id
 AND ".TB_PREF."stock_master.category_id= ".TB_PREF."stock_category.category_id 
 AND ".TB_PREF."stock_moves.tran_date>= '$from'
 AND ".TB_PREF."stock_moves.tran_date<= '$to' ";

    if ($fromcust != ALL_TEXT)
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromcust);

    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);

    $sql .= " AND (".TB_PREF."stock_moves.type IN (13,11,17))";

    if ($location != '')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);

    if ($item != '')
        $sql .= " AND ".TB_PREF."stock_master.stock_id = ".db_escape($item);

    $sql .= " ORDER BY 
 
 ".TB_PREF."stock_moves.type,
 ".TB_PREF."stock_master.category_id, 
 ".TB_PREF."stock_moves.tran_date";
    return db_query($sql,"No transactions were returned");
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

function get_tot_payments_against_invoice($type, $trans_no)
{
    $sql = "SELECT *
            FROM ".TB_PREF."cust_allocations
			WHERE trans_type_to= ".db_escape($type)." 
			AND trans_no_to=".db_escape($trans_no);
    return db_query($sql, "error");
}


function get_invoice_referencess_($trans_no)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = 10
	
	AND order_ =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
//function get_return_referencess_($trans_no, $type)
//{
//    $sql = "SELECT * FROM ".TB_PREF."debtor_trans
//    WHERE type =".db_escape($type)."
//	AND order_ =".db_escape($trans_no);
//    $result = db_query($sql, "error");
//    return db_fetch($result);
//}
function get_credit_referencess_($trans_no,$type)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans
     WHERE type = ".db_escape($type)."
	AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
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

	$orientation = ('L');
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

	$cols = array(0, 28, 60, 90, 150, 180, 220, 240, 270, 308, 345, 400, 430, 450, 490, 520, 580);

//	$headers = array(_('Inv. #'), _('Party'), _('Product'), _('Qty'),  _('Date'),  _('Sales Price'), _('COGS(PKR)'), _('Amount'));
	$headers = array(_('Date'), _('Inv. #'), _('GST (Y/N)'), _('Item Name'),  _('Category'),
        _('Part No'), _('QTY'), _('Price List'), _('Sales(PKR)'), _('COGS(PKR)'), _('Cust. Short Name'),
        _('Cust. Name'), _('Area'), _('Sales Rep Name'), _('Total Payment Against Sales Invoice'),
        _('Date of Last Payment'));
//	if ($fromcust != '')
//		$headers[2] = '';

	$aligns = array('left',	'left', 'left', 'left', 'left','left','right','right','right','right',   'left','left','left','left','left','right','right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Sales Journal Report'), "SalesJournalReport", user_pagesize(), 8, $orientation);
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

        $sales_price = get_item_price_30436($trans['trans_no'],$trans['type'],$trans['stock_id']);
        $sales_price1 = $trans['qty'] * $trans['price'];
        $rep->fontSize -= 2;
        $order_no = get_invoice_reference_($trans['trans_no'],$trans['type']);
        $purch_price = get_purchase_price_($trans['stock_id']);
        if($trans['type']==13)
        {
            $reference = get_invoice_referencess_($order_no);
        }
        elseif($trans['type']==11) {
            $reference = get_credit_referencess_($trans['trans_no'], $trans['type']);
        }
        else{
            $reference = $trans['ref'];

        }
        $curr = get_customer_currency($trans['debtor_no']);
		$rate = get_exchange_rate_from_home_currency($curr, sql2date($trans['tran_date']));
// 		$tot_payment = get_tot_payments_against_invoice_amt($trans['TransNo']);
// 		$tot_payment = get_tot_payments_against_invoice_date($trans['TransNo']);
        $rep->DateCol(0, 1, $trans['tran_date'], true);
//        if($trans['tran_date']==13)
        {
            $rep->TextCol(1, 2, $reference);
        }
//        else
//        {
//            $rep->TextCol(1, 2, $reference['reference']);
//        }
        $rep->TextCol(2, 3, get_taxgroup_id_name($trans['tax_group_id']));
        $rep->TextCol(3, 4, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
        $rep->TextCol(4, 5, $trans['cat_description'], -1);
        $rep->TextCol(5, 6, $trans['text1'], -1);
        $rep->AmountCol(6, 7, abs($trans['qty']), get_qty_dec($trans['stock_id']));
        $rep->AmountCol(9, 10, $trans['cost'], $dec);
        $rep->TextCol(10, 11, $trans['debtor_ref']);
        $rep->TextCol(11, 12, $trans['debtor_name']);
        $rep->TextCol(12, 13, get_area_name($trans['area']));
        $rep->TextCol(13, 14, get_salesman_name($trans['salesman']));
        $result = get_tot_payments_against_invoice($reference['type'], $reference['trans_no']);
        $TotalPayment = 0.0;
        $lastPaymentDate = '';
        while ($alloc = db_fetch($result)) {
            $TotalPayment += $alloc['amt'];
            $lastPaymentDate = sql2date($alloc['date_alloc']);
        }
        $rep->AmountCol(14, 15, $TotalPayment);
        $rep->TextCol(15, 16, $lastPaymentDate);
////		$rep->AmountCol(4, 5, $trans['price'], $dec);
        if(!user_check_access('SA_ITEMSPRICES')) {
            $rep->AmountCol(7, 8, $sales_price, $dec);
            $rep->AmountCol(8, 9, abs($sales_price1), $dec);

//            $net_total = $trans['qty'] * $trans['price'];
//            $rep->AmountCol(7, 8, abs($net_total), get_qty_dec($trans['stock_id']), $dec);
        }
		$rep->fontSize += 2;
        $rep->NewLine();
		$grandtotal += $sales_price;
		$grandtotal2 += $sales_price1;
		$grandtotal_qty += $trans['qty'];  // qty
	} //while 

	$rep->Line($rep->row - 2);
	$rep->NewLine();
	$rep->TextCol(0, 4, _('Grand Total'));
	$rep->AmountCol(6, 7, abs($grandtotal_qty), get_qty_dec($trans['stock_id']), $dec);  // qty
    if(!user_check_access('SA_ITEMSPRICES')) {
        $rep->AmountCol(7, 8, abs($grandtotal), get_qty_dec($trans['stock_id']), $dec);
        $rep->AmountCol(8, 9, abs($grandtotal2), get_qty_dec($trans['stock_id']), $dec);
    }

	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

?>