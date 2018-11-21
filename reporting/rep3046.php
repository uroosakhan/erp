<?php

$page_security = 'SA_SALESREP';
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

function getTransactions($category, $location, $stock_id, $fromcust, $from, $to, $show_service)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = "SELECT item.category_id,item.material_cost,
			category.description AS cat_description,
			item.stock_id,
			debtor.address,
			item.description, item.inactive,
			item.mb_flag,
			move.loc_code,
			move.price,
			move.type,
			move.standard_cost,
			trans.debtor_no,
			trans.reference,
			trans.alloc,
			debtor.name AS debtor_name,
			move.tran_date,
			SUM(-move.qty) AS qty,
			SUM(-move.qty*move.price) AS amt,
			SUM(-IF(move.standard_cost <> 0, move.qty * move.standard_cost, move.qty *item.material_cost)) AS cost
		FROM ".TB_PREF."stock_master item,
			".TB_PREF."stock_category category,
			".TB_PREF."debtor_trans trans,
			".TB_PREF."debtors_master debtor,
			".TB_PREF."stock_moves move
		WHERE item.stock_id=move.stock_id
		AND item.category_id=category.category_id
		AND trans.debtor_no=debtor.debtor_no
		AND move.type=trans.type
		AND move.trans_no=trans.trans_no
		AND move.tran_date>='$from'
		AND move.tran_date<='$to'
		AND (trans.type=".ST_CUSTDELIVERY." OR move.type=".ST_CUSTCREDIT.")";

	if (!$show_service)
		$sql .= " AND (item.mb_flag='B' OR item.mb_flag='M')";
	else
		$sql .= " AND item.mb_flag<>'F'";
	if ($category != 0)
		$sql .= " AND item.category_id = ".db_escape($category);

	if ($location != '')
		$sql .= " AND move.loc_code = ".db_escape($location);

    if ($stock_id != "")
        $sql .= " AND move.stock_id =".db_escape($stock_id);

	if ($fromcust != '')
		$sql .= " AND debtor.debtor_no = ".db_escape($fromcust);

	$sql .= " GROUP BY item.stock_id, debtor.name ORDER BY item.category_id,
		item.stock_id, debtor.name";

    return db_query($sql,"No transactions were returned");

}

//----------------------------------------------------------------------------------------------------

function get_cust_info($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}

function get_last_purch_price3046($stock_id)
{
    $sql = "SELECT price FROM ".TB_PREF."stock_moves 
    WHERE type = 25
    AND stock_id =".db_escape($stock_id)."
    ORDER BY trans_id DESC
	LIMIT 1";
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $price = $_POST['PARAM_3'];
    $location = $_POST['PARAM_4'];
    $item = $_POST['PARAM_5'];
    $fromcust = $_POST['PARAM_6'];
	$show_service = $_POST['PARAM_7'];
	$pl_sales = $_POST['PARAM_8'];
	$comments = $_POST['PARAM_9'];
	$orientation = $_POST['PARAM_10'];
	$destination = $_POST['PARAM_11'];
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

    if ($item == '')
        $itm = _('All');
    else
        $itm = $item;

	if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);
	if ($show_service) $show_service_items = _('Yes');
	else $show_service_items = _('No');

	$cols = array(0, 38, 130, 170, 200, 308, 360,420,450,480, 540,590,640,675,710,770,810);
    if($pl_sales == 0)
	$headers = array(_('Supplier'), _('Product'), _('Date'), _('Bill No'), _('Customer'), _('Address'),_('Mobile No'),_('Model'), _('Qty'),_('Purch Price'), _('Sales Price'), _('P&L'), _('Paid Amt'), _('Bal Amt'), _('Remarks'));
	else
        $headers = array(_('Supplier'), _('Product'), _('Date'), _('Bill No'), _('Customer'), _('Address'),_('Mobile No'),_('Model'), _('Qty'),_(''), _('Sales Price'), _(''), _(''), _(''), _(''));

    if ($fromcust != '')
		$headers[2] = '';

	$aligns = array('left',	'left',	'left', 'left', 'left', 'left','left', 'left',	'right','right', 'right', 'right', 'right','right','right', 'left');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
                        4 => array('text' => _('Item'), 'from' => $itm, 'to' => ''),
    				    5 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''),
    				    6 => array('text' => _('Show Service Items'), 'from' => $show_service_items, 'to' => ''));
    if($pl_sales == 0)
    $rep = new FrontReport(_('Profit & Loss Report'), "InventorySalesReport", user_pagesize(), 9, $orientation);
    else
        $rep = new FrontReport(_('Sales Book Report'), "InventorySalesReport", user_pagesize(), 9, $orientation);


    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions($category, $location, $item, $fromcust, $from, $to, $show_service);
	$total = $grandtotal = 0.0;
	$total1 = $grandtotal1 = 0.0;
	$total2 = $grandtotal2 = 0.0;
	$total3 = $grandtotal3 = 0.0;
	$catt = '';
	while ($trans=db_fetch($res))
	{
		if ($catt != $trans['cat_description'])
		{
			if ($catt != '')
			{
				$rep->NewLine(2, 3);
				$rep->TextCol(0, 4, _('Total'));
                if(!user_check_access('SA_ITEMSPRICES')) {
                    if ($pl_sales == 0) {
                        $rep->AmountCol(10, 11, $total, $dec);
                        $rep->AmountCol(11, 12, $total1, $dec);
                        $rep->AmountCol(13, 14, $total2, $dec);
                    } else {
                        $rep->AmountCol(10, 11, $total, $dec);
                    }
                }
                $rep->Line($rep->row - 2);
				$rep->NewLine();
				$rep->NewLine();
				$total = $total1 = $total2 = $total3 =0.0;
			}
			$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(1, 6, $trans['cat_description']);
			$catt = $trans['cat_description'];
			$rep->NewLine();
		}
        $phone = get_cust_info($trans['debtor_no']);
		$curr = get_customer_currency($trans['debtor_no']);
		$rate = get_exchange_rate_from_home_currency($curr, sql2date($trans['tran_date']));
		$trans['amt'] *= $rate;
		$cb = $trans['amt'] - $trans['cost'];
		$rep->NewLine();
		$rep->fontSize -= 2;
		$rep->TextCol(0, 1, $trans['stock_id']);
        $rep->TextCol(1, 2, $trans['description'], -1);
        $rep->DateCol(2, 3, $trans['tran_date'], true);
        $rep->TextCol(3, 4, $trans['reference']);
		$rep->TextCol(5, 6, $trans['address']);
		$rep->TextCol(6, 7, $phone['phone']);
        $rep->AmountCol(8, 9, $trans['qty'], get_qty_dec($trans['stock_id']));
        $item= get_purchase_price($trans['category_id'], $trans['stock_id']);
        if(!user_check_access('SA_ITEMSPRICES')) {
            if ($pl_sales == 0) {
                if($price == 1)
                {
                    $purch_price = $trans['material_cost'];
                    $rep->AmountCol(9, 10, $purch_price, $dec);
                }
                else
                {
                    $purch_price = get_last_purch_price3046($trans['stock_id']);
                    $rep->AmountCol(9, 10, $purch_price, $dec);
                }
                $rep->AmountCol(10, 11, $trans['price'], $dec);

//                $profit = $trans['amt'] - $trans['cost'];
                $profit = $trans['qty'] * ($trans['price'] - $purch_price);
                //  $rep->AmountCol(11, 12,$trans['price'] - $item, get_qty_dec($trans['stock_id']));
                $rep->AmountCol(11, 12, $profit, get_qty_dec($trans['stock_id']));
                $total1 += $profit;
                $grandtotal1 += $profit;
                if ($trans['alloc'] != 0) {
                    $rep->AmountCol(12, 13, $trans['alloc'], $dec);
                    $rep->TextCol(14, 15, "Paid");
                } else {
                    $rep->AmountCol(13, 14, $trans['price'], $dec);
                    $rep->TextCol(14, 15, "Amount Pending");
                    $total2 += $trans['price'];
                    $grandtotal2 += $trans['price'];
                }
            }
            else
                {
                $rep->AmountCol(10, 11, $trans['price']);
                }

        }
        $rep->TextColLines(4, 5, $trans['debtor_name'], $dec);

        $rep->fontSize += 2;
		$total += $trans['price'];
		//$total1 += $trans['price'] - $item;


		$grandtotal += $trans['price'];



	}
	$rep->NewLine(2, 3);
	$rep->TextCol(0, 4, _('Total'));
    if(!user_check_access('SA_ITEMSPRICES')) {
        if ($pl_sales == 0) {
            $rep->AmountCol(10, 11, $total, $dec);
            $rep->AmountCol(11, 12, $total1, $dec);
            $rep->AmountCol(13, 14, $total2, $dec);
        } else {
            $rep->AmountCol(10, 11, $total, $dec);
        }
    }
	$rep->Line($rep->row - 2);
	$rep->NewLine();
	$rep->NewLine(2, 1);
	$rep->TextCol(0, 4, _('Grand Total'));
    if(!user_check_access('SA_ITEMSPRICES')) {
        if ($pl_sales == 0) {
            $rep->AmountCol(10, 11, $grandtotal, $dec);
            $rep->AmountCol(11, 12, $grandtotal1, $dec);
            $rep->AmountCol(13, 14, $grandtotal2, $dec);
        } else {
            $rep->AmountCol(10, 11, $grandtotal, $dec);
        }
    }
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}