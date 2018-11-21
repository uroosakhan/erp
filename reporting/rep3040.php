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

function getTransactions($category, $location, $stock_id, $fromcust, $from, $to,
                         $show_service, $area, $group, $sales_man)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.description, item.inactive,
			item.mb_flag,
			move.loc_code,
			trans.debtor_no,
			debtor.name AS debtor_name,
			move.tran_date,
			SUM(-move.qty) AS qty,
			SUM(-move.qty*move.price) AS amt,
			SUM(-IF(move.standard_cost <> 0, move.qty * move.standard_cost, move.qty *item.material_cost)) AS cost,
			move.units_id,
			move.con_factor
		FROM ".TB_PREF."stock_master item,
			".TB_PREF."stock_category category,
			".TB_PREF."debtor_trans trans,
			".TB_PREF."debtors_master debtor,
			".TB_PREF."stock_moves move,
			".TB_PREF."cust_branch branch
		WHERE item.stock_id=move.stock_id
		AND item.category_id=category.category_id
		AND trans.debtor_no=debtor.debtor_no
		AND debtor.debtor_no=branch.debtor_no
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

	if ($area != 0)
		$sql .= " AND branch.area = ".db_escape($area);

	if ($group != 0)
		$sql .= " AND branch.group_no = ".db_escape($group);

	if ($sales_man != -1)
		$sql .= " AND branch.salesman = ".db_escape($sales_man);

	$sql .= " GROUP BY item.stock_id, debtor.name ORDER BY 
		item.stock_id, debtor.name";

    return db_query($sql,"No transactions were returned");

}

//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $item = $_POST['PARAM_4'];
    $fromcust = $_POST['PARAM_5'];
    $area = $_POST['PARAM_6'];
    $group = $_POST['PARAM_7'];
    $sales_man = $_POST['PARAM_8'];
	$show_service = $_POST['PARAM_9'];
	$summary = $_POST['PARAM_10'];
	$comments = $_POST['PARAM_11'];
	$orientation = $_POST['PARAM_12'];
	$destination = $_POST['PARAM_13'];

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

    if ($item == '')
        $itm = _('All');
    else
        $itm = $item;

	if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);

    if ($area == 0)
        $area_name = _('All');
    else
        $area_name = get_area_name($area);

    if ($group == 0)
        $groups_name = _('All');
    else
        $groups_name = get_sales_group_name($group);

	if ($show_service) $show_service_items = _('Yes');
	else $show_service_items = _('No');

	$cols = array(0, 75, 175, 250, 300, 375, 450,	515);
    if($summary != 1)
    {
	$headers = array(_('Item'), _('Description'), _('Customer'), _('Qty'), _('Sales'), _('Cost'), _('Contribution'));
    }
    else
    {
    $headers = array(_('Item'), _('Description'), _(''), _('Qty'), _('Sales'), _('Cost'), _('Contribution'));
    }
	if ($fromcust != '')
		$headers[2] = '';

	$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
                        4 => array('text' => _('Item'), 'from' => $itm, 'to' => ''),
    				    5 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''),
    				    6 => array('text' => _('Show Service Items'), 'from' => $show_service_items, 'to' => ''),
                        7 => array('text' => _('Area'), 'from' => $area_name, 'to' => ''),
                        8 => array('text' => _('Groups'), 'from' => $groups_name, 'to' => ''));

    $rep = new FrontReport(_('Inventory Sales Report-Item Wise'), "InventorySalesReport", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $pref = get_company_prefs();

	$res = getTransactions($category, $location, $item, $fromcust, $from, $to, $show_service, $area, $group, $sales_man);
	$total = $grandtotal = 0.0;
	$total1 = $grandtotal1 = 0.0;
	$total2 = $grandtotal2 = 0.0;
	$total3 = $grandtotal3 = 0.0;
	$catt = '';

	while ($trans=db_fetch($res))
	{
		if ($catt != $trans['stock_id'])
		{
			if ($catt != '')
			{
                if($summary != 1) {
                    $rep->NewLine(2, 3);
                    $rep->TextCol(0, 4, _('Total'));
                    $rep->AmountCol(3, 4, $total3, $dec);
                    if(!user_check_access('SA_ITEMSPRICES')) {
                        $rep->AmountCol(4, 5, $total, $dec);
                        $rep->AmountCol(5, 6, $total1, $dec);
                        $rep->AmountCol(6, 7, $total2, $dec);
                    }
                    $rep->Line($rep->row - 2);
                    $rep->NewLine();
                    $rep->NewLine();
                }
                else
                {$rep->Line($rep->row - 2);
//                    $rep->NewLine();
//                    $rep->TextCol(0, 4, _('Total'));
                    $rep->AmountCol(3, 4, $total3, $dec);
                    if(!user_check_access('SA_ITEMSPRICES')) {
                        $rep->AmountCol(4, 5, $total, $dec);
                        $rep->AmountCol(5, 6, $total1, $dec);
                        $rep->AmountCol(6, 7, $total2, $dec);
                    }
                    $rep->NewLine();
                    $rep->Line($rep->row - 2);
                }

//                    $rep->NewLine();
//                    $rep->NewLine();
                    $total = $total1 = $total2 = $total3 = 0.0;

			}
			$rep->TextCol(0, 1, $trans['stock_id']);
            if($summary != 1)
            {
                $rep->TextCol(1, 6, $trans['description']);
                $catt = $trans['stock_id'];
                $rep->NewLine();
            }
            else
            {
                $rep->TextCol(1, 3, $trans['description']);
                $catt = $trans['stock_id'];
//                $rep->NewLine();
            }

		}

		$curr = get_customer_currency($trans['debtor_no']);
		$rate = get_exchange_rate_from_home_currency($curr, sql2date($trans['tran_date']));
		$trans['amt'] *= $rate;
			$item = get_item($trans['stock_id']);
		 if($trans['units_id'] != $item['units'] && $trans['units_id']!='' && $pref['alt_uom'] == 1)
        {
		$cb = $trans['amt'] - ($trans['cost'] / $trans['con_factor']) ;
        }
        else{
        $cb = $trans['amt'] - $trans['cost'];    
        }
		if($summary != 1) {
            $rep->NewLine();
            $rep->fontSize -= 2;
            $rep->TextCol(0, 1, $trans['category_id']);
            if ($fromcust == ALL_TEXT)
            {
                $rep->TextCol(1, 2, $trans['cat_description'] . ($trans['inactive'] == 1 ? " (" . _("Inactive") . ")" : ""), -1);
                $rep->TextCol(2, 3, $trans['debtor_name']);
            }
            else
                $rep->TextCol(1, 3, $trans['cat_description'] . ($trans['inactive'] == 1 ? " (" . _("Inactive") . ")" : ""), -1);
            $rep->AmountCol(3, 4, $trans['qty'], get_qty_dec($trans['stock_id']));
            if(!user_check_access('SA_ITEMSPRICES')) {
                $rep->AmountCol(4, 5, $trans['amt'], $dec);

                if (is_service($trans['mb_flag']))
                    $rep->TextCol(5, 6, "---");
                else
                    $rep->AmountCol(5, 6, $trans['cost'], $dec);
                $rep->AmountCol(6, 7, $cb, $dec);
            }
            $rep->fontSize += 2;
        }


        $qty = 0;
        $cost = 0;
        if($trans['units_id'] != $item['units'] && $trans['units_id']!='' && $pref['alt_uom'] == 1)
        {
            $qty = $trans['qty'] * $trans['con_factor'];
            $cost = $trans['cost'] / $trans['con_factor'];
            $amt = $trans['amt'] * $trans['con_factor'];
            $cb = $cb * $trans['con_factor'];
        }
        else{
            $qty += $trans['qty'] ;
            $cost = $trans['cost'] ;
            $amt = $trans['amt'] ;
        }
        $total += $amt;
        $total1 += $cost;
        $total2 += $cb;
        $total3 += $qty;
        $grandtotal += $amt;
        $grandtotal1 += $cost;
        $grandtotal2 += $cb;
        $grandtotal3 += $qty;


	}
    if($summary != 1)
    {
        $rep->NewLine(2, 3);
        $rep->TextCol(0, 4, _('Total'));
        $rep->AmountCol(3, 4, $total3, $dec);
        if(!user_check_access('SA_ITEMSPRICES')) {
            $rep->AmountCol(4, 5, $total, $dec);
            $rep->AmountCol(5, 6, $total1, $dec);
            $rep->AmountCol(6, 7, $total2, $dec);
        }
        $rep->Line($rep->row - 2);
    }
    else
        {
        $rep->AmountCol(3, 4, $total3, $dec);
        if(!user_check_access('SA_ITEMSPRICES')) {
            $rep->AmountCol(4, 5, $total, $dec);
            $rep->AmountCol(5, 6, $total1, $dec);
            $rep->AmountCol(6, 7, $total2, $dec);
        }
        $rep->NewLine(1);
    }

	$rep->NewLine();
	$rep->NewLine(2, 1);
	$rep->TextCol(0, 4, _('Grand Total'));
	$rep->AmountCol(3, 4, $grandtotal3, $dec);
    if(!user_check_access('SA_ITEMSPRICES')) {
	$rep->AmountCol(4, 5, $grandtotal, $dec);
	$rep->AmountCol(5, 6, $grandtotal1, $dec);
	$rep->AmountCol(6, 7, $grandtotal2, $dec);}

	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}
