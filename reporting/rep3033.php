<?php

$page_security = 'SA_ITEMS_STOCK';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Stock Check Sheet
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_stock_check();
function get_batch($batch)
{
	$sql = "SELECT batch from ".TB_PREF."stock_moves where batch != '' 
	";
	if ($batch != 0)
		$sql .= " AND batch = ".db_escape($batch);
	$sql .= " GROUP BY  stock_id";
	return db_query($sql,"No transactions were returned");
}
function getTransactions($category, $location, $item_like,$batch)
{
	$sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id, item.units,
			item.description, item.inactive,
			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code,
			SUM(IF(move.stock_id IS NULL,0,move.qty)) AS QtyOnHand,move.batch
		FROM ("
			.TB_PREF."stock_master item,"
			.TB_PREF."stock_category category)
			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id
		WHERE item.category_id=category.category_id
		AND (item.mb_flag='B' OR item.mb_flag='M')";
	if ($category != 0)
		$sql .= " AND item.category_id = ".db_escape($category);
	if ($batch != 0)
		$sql .= " AND move.batch = ".db_escape($batch);

	if ($location != 'all')
		$sql .= " AND IF(move.stock_id IS NULL, '1=1',move.loc_code = ".db_escape($location).")";
  if($item_like)
  {
    $regexp = null;

    if(sscanf($item_like, "/%s", $regexp)==1)
      $sql .= " AND item.stock_id RLIKE ".db_escape($regexp);
    else
      $sql .= " AND item.stock_id LIKE ".db_escape($item_like);
  }
	$sql .= " GROUP BY stock_id
		ORDER BY category_id,
		stock_id";

    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root, $SysPrefs;

   	$category = $_POST['PARAM_0'];
   	$location = $_POST['PARAM_1'];
	$like     = $_POST['PARAM_2'];
	$batch     = $_POST['PARAM_3'];
//   	$pictures = $_POST['PARAM_2'];
//   	$check    = $_POST['PARAM_3'];
//   	$shortage = $_POST['PARAM_4'];
   	$no_zeros = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];
	$destination = $_POST['PARAM_7'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
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
	if ($shortage)
	{
		$short = _('Yes');
		$available = _('Shortage');
	}
	else
	{
		$short = _('No');
		$available = _('Available');
	}
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');
	if ($check)
	{
		$cols = array(0, 75, 225, 250, 295);
		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Quantity'));
		$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right', 'right');
	}
	else
	{
		$cols = array(0, 75,175, 305,  450);
		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Batch'), _('Quantity'));
		$aligns = array('left',	'left','left',	'left', 'right');
	}


    	$params =   array( 	0 => $comments,
    				1 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				3 => array('text' => _('Only Shortage'), 'from' => $short, 'to' => ''),
					4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

   	$rep = new FrontReport(_('Stock Check Sheets'), "StockCheckSheet", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


	$catt = '';

	$result=get_batch($batch);
$batch_id='';

	while($myrow=db_fetch($result)) {
		$batch = get_batch_by_id($myrow['batch']);
		$rep->font('b');
		if($batch_id != $myrow['batch'])
		$rep->TextCol(0, 1, $batch['name']);
		$rep->font('');
		$rep->NewLine();
		$batch_id = $myrow['batch'];
		$res = getTransactions($category, $location, $like,$myrow['batch']);
		while ($trans = db_fetch($res)) {
		if ($location == 'all')
			$loc_code = "";
		else
			$loc_code = $location;
		$demandqty = get_demand_qty($trans['stock_id'], $loc_code);
		$demandqty += get_demand_asm_qty($trans['stock_id'], $loc_code);
		$onorder = get_on_porder_qty($trans['stock_id'], $loc_code);
		$flag = get_mb_flag($trans['stock_id']);
		if ($flag == 'M')
			$onorder += get_on_worder_qty($trans['stock_id'], $loc_code);
		if ($no_zeros && $trans['QtyOnHand'] == 0 && $demandqty == 0 && $onorder == 0)
			continue;
		if ($shortage && $trans['QtyOnHand'] - $demandqty >= 0)
			continue;
		if ($catt != $trans['cat_description'])
		{
			if ($catt != '')
			{
				$rep->Line($rep->row - 2);
				$rep->NewLine(2, 3);
			}
//			$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(1, 2, $trans['cat_description']);

			$catt = $trans['cat_description'];
			$rep->NewLine();
		}
		$rep->NewLine();
//
		$dec = get_qty_dec($trans['stock_id']);
		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(1, 2, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
		$rep->TextCol(2, 3, $trans['units']);
		$rep->TextCol(3, 4, $batch['name']);
		$rep->AmountCol(4, 5, $trans['QtyOnHand'], $dec);
//		if ($check)
//		{
//			$rep->TextCol(4, 5, "_________");
//			$rep->AmountCol(5, 6, $demandqty, $dec);
//			$rep->AmountCol(6, 7, $trans['QtyOnHand'] - $demandqty, $dec);
//			$rep->AmountCol(7, 8, $onorder, $dec);
//		}
//		else
//		{
//			$rep->AmountCol(4, 5, $demandqty, $dec);
//			$rep->AmountCol(5, 6, $trans['QtyOnHand'] - $demandqty, $dec);
//			$rep->AmountCol(6, 7, $onorder, $dec);
//		}
		if ($pictures)
		{
			$image = company_path() . '/images/'
				. item_img_name($trans['stock_id']) . '.jpg';
			if (file_exists($image))
			{
				$rep->NewLine();
				if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
					$rep->NewPage();
				$rep->AddImage($image, $rep->cols[1], $rep->row - $SysPrefs->pic_height, 0, $SysPrefs->pic_height);
				$rep->row -= $SysPrefs->pic_height;
				$rep->NewLine();
			}
		}
		}
		$rep->NewLine();
	}
	$rep->Line($rep->row - 4);
	$rep->NewLine();
    $rep->End();
}

