<?php
$page_security = 'SA_ITEMSPLANNINGREPORT';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Inventory Planning
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_planning();

function getTransactions($category, $location, $item)
{
	$sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.description, item.inactive,
			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code,
			SUM(IFNULL(move.stock_id, 0)) AS qty_on_hand
		FROM (".TB_PREF."stock_master item,"
		.TB_PREF."stock_category category)
			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id
		WHERE item.category_id=category.category_id
		AND (item.mb_flag='B' OR item.mb_flag='M')";
	if ($category != 0)
		$sql .= " AND item.category_id = ".db_escape($category);
	if ($location != 'all')
		$sql .= " AND IF(move.stock_id IS NULL, '1=1',move.loc_code = ".db_escape($location).")";
	if ($item != '')
		$sql .= " AND item.stock_id = ".db_escape($item);
	$sql .= " GROUP BY item.category_id,
		category.description,
		item.stock_id,
		item.description
		ORDER BY item.category_id,
		item.stock_id";

	return db_query($sql,"No transactions were returned");

}

function getPeriods($stockid,$loc_code)
{
	$date8 = date('Y-m-d');
	$date7 = date('Y-m-d',mktime(0,0,0,date('m'),1,date('Y')));
	$date6 = date('Y-m-d',mktime(0,0,0,date('m')-1,1,date('Y')));
	$date5 = date('Y-m-d',mktime(0,0,0,date('m')-2,1,date('Y')));
	$date4 = date('Y-m-d',mktime(0,0,0,date('m')-3,1,date('Y')));
	$date3 = date('Y-m-d',mktime(0,0,0,date('m')-4,1,date('Y')));
	$date2 = date('Y-m-d',mktime(0,0,0,date('m')-5,1,date('Y')));
	$date1 = date('Y-m-d',mktime(0,0,0,date('m')-6,1,date('Y')));
	$date0 = date('Y-m-d',mktime(0,0,0,date('m')-7,1,date('Y')));


	$sql = "SELECT
  				SUM(CASE WHEN tran_date >= '$date0' AND tran_date < '$date1' THEN -qty ELSE 0 END) AS prd0,
 				SUM(CASE WHEN tran_date >= '$date1' AND tran_date < '$date2' THEN -qty ELSE 0 END) AS prd1,
	   			SUM(CASE WHEN tran_date >= '$date2' AND tran_date < '$date3' THEN -qty ELSE 0 END) AS prd2,
				SUM(CASE WHEN tran_date >= '$date3' AND tran_date < '$date4' THEN -qty ELSE 0 END) AS prd3,
				SUM(CASE WHEN tran_date >= '$date4' AND tran_date < '$date5' THEN -qty ELSE 0 END) AS prd4,
				SUM(CASE WHEN tran_date >= '$date5' AND tran_date <= '$date6' THEN -qty ELSE 0 END) AS prd5,
				SUM(CASE WHEN tran_date >= '$date6' AND tran_date <= '$date7' THEN -qty ELSE 0 END) AS prd6,
				SUM(CASE WHEN tran_date >= '$date7' AND tran_date <= '$date8' THEN -qty ELSE 0 END) AS prd7
			FROM ".TB_PREF."stock_moves
			WHERE stock_id='$stockid'
			
			AND (type=13 OR type=11)
				
			";
	if ($loc_code != '')
		$sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($loc_code);


	$TransResult = db_query($sql,"No transactions were returned");
	return db_fetch($TransResult);
}

//----------------------------------------------------------------------------------------------------

function print_inventory_planning()
{
	global $path_to_root;

	$category = $_POST['PARAM_0'];
	$location = $_POST['PARAM_1'];
	$item = $_POST['PARAM_2'];
	$no_zeros = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	//$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation =  'L' ;
	if ($category == ALL_NUMERIC)
		$category = 0;
	if ($category == 0)
		$cat = _('All');
	else
		$cat = get_category_name($category);

	if ($location == ALL_TEXT)
		$location = 'all';
	if ($location == 'all')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	if ($no_zeros)
		$nozeros = _('Yes');
	else
		$nozeros = _('No');

	if ($item == '')
		$itm = _('All');
	else
		$itm = $item;

	$cols = array(0, 50, 125, 160, 185, 215, 240, 270, 300, 335, 370, 400, 440, 480,520);

	$per0 = strftime('%b',mktime(0,0,0,date('m'),1,date('Y')));
	$per1 = strftime('%b',mktime(0,0,0,date('m')-1,1,date('Y')));
	$per2 = strftime('%b',mktime(0,0,0,date('m')-2,1,date('Y')));
	$per3 = strftime('%b',mktime(0,0,0,date('m')-3,1,date('Y')));
	$per4 = strftime('%b',mktime(0,0,0,date('m')-4,1,date('Y')));
	$per5 = strftime('%b',mktime(0,0,0,date('m')-5,1,date('Y')));
	$per6 = strftime('%b',mktime(0,0,0,date('m')-6,1,date('Y')));
	$per7 = strftime('%b',mktime(0,0,0,date('m')-7,1,date('Y')));

	$headers = array(_('Category'), '', $per7, $per6, $per5, $per4, $per3, $per2, $per1, $per0, '3*M',
		_('QOH'), _('Cust Ord'), _('Supp Ord'), _('Sugg Ord'));

	$aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right',
		'right', 'right', 'right', 'right', 'right', 'right');


	$params =   array( 	0 => $comments,
		1 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
		2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
		3 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''),
		4 => array('text' => _('Item'), 'from' => $itm, 'to' => ''));

	$rep = new FrontReport(_('Inventory Planning Report'), "InventoryPlanning", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);
	$rep->NewPage();

	$res = getTransactions($category, $location, $item);
	$catt = '';
	while ($trans=db_fetch($res))
	{


		if ($catt != $trans['cat_description'])
		{
			if ($catt != '')
			{
				$rep->Line($rep->row - 2);
				$rep->NewLine(2, 3);
			}
			$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(1, 2, $trans['cat_description']);
			$catt = $trans['cat_description'];
			$rep->NewLine();
		}
		if ($location == 'all')
			$loc_code = "";
		else
			$loc_code = $location;
		$custqty = get_demand_qty($trans['stock_id'], $loc_code);
		$custqty += get_demand_asm_qty($trans['stock_id'], $loc_code);
		$suppqty = get_on_porder_qty($trans['stock_id'], $loc_code);
		$suppqty += get_on_worder_qty($trans['stock_id'], $loc_code);
		$period = getPeriods($trans['stock_id'],$loc_code);


		$rep->NewLine();
		$dec = get_qty_dec($trans['stock_id']);
		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(1, 2, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
		$qoh = get_qoh_on_date($trans['stock_id'], null,null );
		if ($no_zeros == 0 && $period['prd0'] != 0)
			$rep->AmountCol(2, 3, $period['prd0'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(2, 3,"", $dec);

		if ($no_zeros == 1 && $period['prd1'] != 0)
			$rep->AmountCol(3, 4, $period['prd1'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(3, 4, $period['prd1'], $dec);

		if ($no_zeros == 1 && $period['prd2'] != 0)
			$rep->AmountCol(4, 5, $period['prd2'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(4, 5, $period['prd2'], $dec);

		if ($no_zeros == 1 && $period['prd3'] != 0)
			$rep->AmountCol(5, 6, $period['prd3'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(5, 6, $period['prd3'], $dec);

		if ($no_zeros == 1 && $period['prd4'] != 0)
			$rep->AmountCol(6, 7, $period['prd4'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(6, 7, $period['prd4'], $dec);

	if ($no_zeros == 1 && $period['prd5'] != 0)
			$rep->AmountCol(7, 8, $period['prd5'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(7, 8, $period['prd5'], $dec);

	if ($no_zeros == 1 && $period['prd6'] != 0)
			$rep->AmountCol(8, 9, $period['prd6'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(8, 9, $period['prd6'], $dec);

	if ($no_zeros == 1 && $period['prd7'] != 0)
			$rep->AmountCol(9, 10, $period['prd7'], $dec);
		elseif($no_zeros == 0)
			$rep->AmountCol(9, 10, $period['prd7'], $dec);

		$MaxMthSales = Max($period['prd0'], $period['prd1'], $period['prd2'], $period['prd3'], $period['prd4'], $period['prd5'], $period['prd6']);
		$IdealStockHolding = $MaxMthSales * 3;
		$rep->AmountCol(10, 11, $IdealStockHolding, $dec);

		$rep->AmountCol(11, 12, $qoh, $dec);
		$rep->AmountCol(12, 13, $custqty, $dec);
		$rep->AmountCol(13, 14, $suppqty, $dec);

		$SuggestedTopUpOrder = $IdealStockHolding - $trans['qty_on_hand'] + $custqty - $suppqty;
		if ($SuggestedTopUpOrder < 0.0)
			$SuggestedTopUpOrder = 0.0;
		$rep->AmountCol(14, 15, $SuggestedTopUpOrder, $dec);
	}
	$rep->Line($rep->row - 4);
	$rep->NewLine();
	$rep->End();
}