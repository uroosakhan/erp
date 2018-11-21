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

//------------------------------------------------------------------------

print_stock_check();

//function getTransactions($category, $location,$supplier, $item_like, $inactive,$date)
//{
//     $dates = date2sql($date);
//	$sql = "SELECT item.category_id,
//			category.description AS cat_description,
//			item.stock_id, item.units,
//			item.description, item.inactive,
//			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code,
//			SUM(IF(move.stock_id IS NULL,0,move.qty)) AS QtyOnHand
//		FROM ("
//			.TB_PREF."stock_master item
//			,".TB_PREF."stock_category category)
//			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id
//
//		WHERE item.category_id=category.category_id
//		AND (item.mb_flag='B' OR item.mb_flag='M')
//			AND move.tran_date <= ".db_escape($dates);
//	if ($category != 0)
//		$sql .= " AND item.category_id = ".db_escape($category);
//
//    if ($supplier != 0)
//        $sql .= "AND move.person_id = ".db_escape($supplier);
//
//    if ($inactive != '')
//        $sql .= "AND item.inactive = ".db_escape($inactive);
//
//	if ($location != 'all')
//		$sql .= " AND IF(move.stock_id IS NULL, '1=1',move.loc_code = ".db_escape($location).")";
//  if($item_like)
//  {
//    $regexp = null;
//
//    if(sscanf($item_like, "/%s", $regexp)==1)
//      $sql .= " AND item.stock_id RLIKE ".db_escape($regexp);
//    else
//      $sql .= " AND item.stock_id LIKE ".db_escape($item_like);
//  }
//	$sql .= " GROUP BY item.category_id,
//		category.description,
//		item.stock_id,
//		item.description
//		ORDER BY item.category_id,
//		item.stock_id";
//
//    return db_query($sql,"No transactions were returned");
//}
function getTransactions($category, $location,$supplier, $item_like, $inactive,$date)
{
    $dates = date2sql($date);
    $sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id, item.units,move.person_id,move.type,
			item.description, item.inactive,
			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code
		FROM ("
        .TB_PREF."stock_master item
			,".TB_PREF."stock_category category)
			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id

		WHERE item.category_id=category.category_id
		AND (item.mb_flag='B' OR item.mb_flag='M')
			AND move.tran_date <= ".db_escape($dates);
    if ($category != 0)
        $sql .= " AND item.category_id = ".db_escape($category);

    if ($supplier != 0)
        $sql .= "AND move.person_id = ".db_escape($supplier);

    if ($inactive != '')
        $sql .= "AND item.inactive = ".db_escape($inactive);

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
    $sql .= " GROUP BY item.category_id,
		category.description,
		item.stock_id,
		item.description
		ORDER BY item.category_id,
		item.stock_id";

    return db_query($sql,"No transactions were returned");
}
function get_qoh($stock_id)
{

        $sql = "SELECT move.qty,move.type,move.person_id,move.trans_no
    FROM " . TB_PREF . "stock_moves move
    WHERE stock_id = " . db_escape($stock_id);


    return db_query($sql,"No transactions were returned");
}
//function get_qoh2($stock_id)
//{
//    $sql = "SELECT move.qty,move.type,move.person_id,move.trans_no
//    FROM " . TB_PREF . "stock_moves move
//    WHERE stock_id = " . db_escape($stock_id);
//
//
//    return db_query($sql,"No transactions were returned");
//}
function get_supplier_tax($person_id)
{
    $sql = "SELECT tax_group_id
    FROM ".TB_PREF."suppliers
    WHERE ".TB_PREF."suppliers.supplier_id = " . db_escape($person_id);
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}

function get_supplier_tax2($trans_no)
{
    $sql = "SELECT ".TB_PREF."suppliers.tax_group_id
    FROM ".TB_PREF."suppliers, ".TB_PREF."supp_trans
    WHERE ".TB_PREF."suppliers.supplier_id = ".TB_PREF."supp_trans.supplier_id
    AND ".TB_PREF."supp_trans.trans_no = " . db_escape($trans_no)
    ;
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_customer_tax($person_id)
{
    $sql = "SELECT tax_group_id
    FROM ".TB_PREF."cust_branch
    WHERE ".TB_PREF."cust_branch.debtor_no = " . db_escape($person_id);
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_customer_tax2($trans_no)
{
    $sql = "SELECT ".TB_PREF."cust_branch.tax_group_id
    FROM ".TB_PREF."cust_branch, ".TB_PREF."debtor_trans
    WHERE ".TB_PREF."cust_branch.debtor_no = ".TB_PREF."debtor_trans.debtor_no
    AND ".TB_PREF."debtor_trans.trans_no = " . db_escape($trans_no)
    ;
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_packing_valu($stock_id)
{
    $sql = "SELECT carton
    FROM ".TB_PREF."stock_master
    WHERE stock_id = ".db_escape($stock_id);
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_factor_valu($stock_id)
{
    $sql = "SELECT con_factor
    FROM ".TB_PREF."stock_master
    WHERE stock_id = ".db_escape($stock_id);
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root, $SysPrefs;
	$date = $_POST['PARAM_0'];
   	$category = $_POST['PARAM_1'];
    $location = $_POST['PARAM_2'];
    $supplier = $_POST['PARAM_3'];
    $tax = $_POST['PARAM_4'];
    $inactive = $_POST['PARAM_5'];
    $pictures = $_POST['PARAM_6'];
   	$check    = $_POST['PARAM_7'];
   	$shortage = $_POST['PARAM_8'];
   	$no_zeros = $_POST['PARAM_9'];
   	$like     = $_POST['PARAM_10'];
   	$comments = $_POST['PARAM_11'];
	$orientation = $_POST['PARAM_12'];
	$destination = $_POST['PARAM_13'];

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
//iqra

    if ($supplier == ALL_NUMERIC)
        $supplier = 0;
    if ($supplier == 0)
        $sup = _('All');
    else
        $sup = get_supplier_name($supplier);



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
	
	if ($inactive) $in_active = _('Yes');
	else $in_active = _('No');
	
	if ($check)
	{
		//$cols = array(0, 75, 225, 250, 295, 345, 390, 445,	515);
		$cols = array(0, 75, 225, 250, 300, 360, 415,470,515);
		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Quantity'), _('Check'), _('Demand'), $available, _('On Order'),('CTN'));
		$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right', 'right', 'right');
	}
	else
	{
		$cols = array(0, 75, 225, 250, 300, 360, 415,470,515);
		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Quantity'), _('Demand'), $available, _('On Order'),('CTN'));
		$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right','right');
	}


    	$params =   array( 	0 => $comments,
    				1 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
                    2 => array('text' => _('supplier'), 'from' => $sup, 'to' => ''),
    				3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				4 => array('text' => _('Only Shortage'), 'from' => $short, 'to' => ''),
					5 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''),
					6 => array('text' => _('Inactive'), 'from' => $in_active, 'to' => ''));

   	$rep = new FrontReport(_('Stock Check Sheets'), "StockCheckSheet", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions($category, $location,$supplier, $like, $inactive,$date);
	$catt = '';
	while ($trans=db_fetch($res))
	{
		if ($location == 'all')
			$loc_code = "";
		else
			$loc_code = $location;
		$demandqty = get_demand_qty($trans['stock_id'], $loc_code);
		$demandqty += get_demand_asm_qty($trans['stock_id'], $loc_code);
		$onorder = get_on_porder_qty($trans['stock_id'], $loc_code);

        $packing = get_packing_valu($trans['stock_id']);
        $con_factor = get_factor_valu($trans['stock_id']);
		$flag = get_mb_flag($trans['stock_id']);
		  global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'BNT2') {
            if ($flag == 'M')
                $onorder += get_on_worder_qty($trans['stock_id'], $loc_code);
        }
        else{
            if ($flag == 'M') {
                $onorder += get_on_worder_qty_bnt($trans['stock_id'], $loc_code);


            }
        }
//        $qoh = get_qoh($trans['stock_id'], $trans['type']);
		if ($no_zeros &&  $demandqty == 0 && $onorder == 0)
			continue;
		if ($shortage)
			continue;
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
		$rep->NewLine();
		$dec = get_qty_dec($trans['stock_id']);
		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(1, 2, $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
		$rep->TextCol(2, 3, $trans['units']);
//        $rep->NewLine();
        $res1 = get_qoh($trans['stock_id']);
        $tot_qty =0;
        $count =1;
        $count1 =0;

        while ($trans1=db_fetch($res1))
        {


//            $rep->AmountCol(0, 1, $count);
            $count1 +=$count;

            if($trans1['type']==25)
            {
                $item_tax =get_supplier_tax($trans1['person_id']);
//                $rep->AmountCol(1, 2, $item_tax);
            }
            elseif($trans1['type']==21)
            {
                $item_tax =get_supplier_tax2($trans1['trans_no']);
//                $rep->AmountCol(1, 2, $item_tax);
            }
            elseif($trans1['type']==13)
            {
                $item_tax =get_customer_tax($trans1['person_id']);
//                $rep->AmountCol(1, 2, $item_tax);
            }
            elseif($trans1['type']==11)
            {
                $item_tax =get_customer_tax2($trans1['trans_no']);
                $rep->AmountCol(1, 2, $item_tax);
            }
            else
            {
                $item_tax =0;
//                $rep->AmountCol(1, 2, $item_tax);
            }
            if($item_tax==$tax)
            {
//                $rep->AmountCol(3, 4, $trans1['qty'], $dec);
                $tot_qty +=$trans1['qty'];
            }
            elseif($item_tax==0)
            {
//                $rep->AmountCol(3, 4, $trans1['qty'], $dec);
                $tot_qty +=$trans1['qty'];
            }
            elseif($tax==0)
            {
//                $rep->AmountCol(3, 4, $trans1['qty'], $dec);
                $tot_qty +=$trans1['qty'];
            }


            global $db_connections;
            if ($check) {
//                $rep->TextCol(4, 5, "_________");
//                $rep->AmountCol(5, 6, $demandqty, $dec);
//                $rep->AmountCol(6, 7, $trans1['qty'] - $demandqty, $dec);
//                $rep->AmountCol(7, 8, $onorder, $dec);
//                $packing = get_packing_valu($trans1['stock_id']);
//                $con_factor = get_factor_valu($trans1['stock_id']);

                if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'SHAMIM') {
//                    $rep->AmountCol(7, 8, (($trans1['qty'] - $demandqty) / $con_factor), $dec);
                } else {
//                    $rep->AmountCol(7, 8, (($trans1['qty'] - $demandqty) / $packing), $dec);
                }
            }else {

//                $rep->AmountCol(4, 5, $demandqty, $dec);
//                $rep->AmountCol(5, 6, $trans1['qty'] - $demandqty, $dec);
//                $rep->AmountCol(6, 7, $onorder, $dec);
//                $packing = get_packing_valu($trans1['stock_id']);
//                $con_factor = get_factor_valu($trans1['stock_id']);

                if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'SHAMIM') {
//                    $rep->AmountCol(7, 8, (($trans1['qty'] - $demandqty) / $con_factor), $dec);
                }
                else {
//                    $rep->AmountCol(7, 8, (($trans1['qty'] - $demandqty) / $packing), $dec);
                }
            }
//            $rep->NewLine();

        }
//		display_error($count1);
//        $rep->Line($rep->row - 4);
//        $rep->NewLine(-1);
        $rep->AmountCol(3, 4, $tot_qty, $dec);
//        $rep->AmountCol(0, 1, $count1, $dec);



        if ($check) {
            $rep->TextCol(4, 5, "_________");
            $rep->AmountCol(5, 6, $demandqty, $dec);
            $rep->AmountCol(6, 7, $tot_qty - $demandqty, $dec);
            $rep->AmountCol(7, 8, $onorder, $dec);


            if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'SHAMIM')

                $rep->AmountCol(7, 8, (($tot_qty - $demandqty) / $con_factor), $dec);
            else
                $rep->AmountCol(7, 8, (($tot_qty - $demandqty) / $packing), $dec);
        }
        else
            {
            $rep->AmountCol(4, 5, $demandqty, $dec);
            $rep->AmountCol(5, 6, $tot_qty - $demandqty, $dec);
            $rep->AmountCol(6, 7, $onorder, $dec);
            $packing = get_packing_valu($trans1['stock_id']);
            $con_factor = get_factor_valu($trans1['stock_id']);

            if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'SHAMIM')
                $rep->AmountCol(7, 8, (($tot_qty - $demandqty) / $con_factor), $dec);
            else
                $rep->AmountCol(7, 8, (($tot_qty - $demandqty) / $packing), $dec);
            }







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
//    $rep->NewLine(+($count1));
	$rep->Line($rep->row - 4);
	$rep->NewLine();
    $rep->End();
}