<?php

$page_security = 'SA_LOCREORDER';
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

function getTransactions($category, $location,$supplier, $item_like, $inactive,$date)
{
//     $dates = date2sql($date);
	$sql = "SELECT item.category_id, item.material_cost,
			category.description AS cat_description,
			item.stock_id, item.units,
			item.description, item.inactive,
			
  		
			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code,
			SUM(IF(move.stock_id IS NULL,0,move.qty)) AS QtyOnHand
		FROM ("
			.TB_PREF."stock_master item
			,".TB_PREF."stock_category category)
			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id
				
		WHERE item.category_id=category.category_id
		AND item.mb_flag != 'D'
		";
	if ($category != 0)
		$sql .= " AND item.category_id = ".db_escape($category);
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
	$sql .= " GROUP BY item.category_id,
	item.stock_id
		

";
/*//		category.description,
//		item.stock_id,
//		item.description
//		ORDER BY item.category_id,
//		item.stock_id*/
    return db_query($sql,"No transactions were returned");
}
function get_locationwise_data($stock_id)
{

    $sql = "SELECT *
		FROM ".TB_PREF."loc_stock	
		WHERE stock_id=".db_escape($stock_id)
		;
    return db_query($sql,"No transactions were returned");
}
function get_rol($stock_id, $loc_code)
{
    $sql = "SELECT reorder_level
    FROM ".TB_PREF."loc_stock
    WHERE stock_id=".db_escape($stock_id)."
    AND loc_code=".db_escape($loc_code)
    ;
    $result = db_query($sql, "could not retreive location name");
    $row = db_fetch_row($result);
    return $row['0'];
}
function get_locode_name($loc_code)
{
    $sql = "SELECT location_name FROM ".TB_PREF."locations 
	WHERE loc_code = ".db_escape($loc_code);
    $result = db_query($sql, "could not retreive location name");
    $row = db_fetch_row($result);
    return $row['0'];
}
function get_location_name_for_header()
{
//    $start_limit = $limit - 1;
//    $end_limit=($limit2)-($start_limit);
    $sql = "SELECT *
    FROM ".TB_PREF."locations LIMIT 10";

    return db_query($sql,"items could not be retreived");
}
function get_total_location_name_for_header()
{

    $sql = "SELECT count(*)
    FROM ".TB_PREF."locations WHERE inactive=0";

    $result= db_query($sql,"items could not be retreived");
    $row = db_fetch_row($result);
    return $row['0'];
}
function get_sales_price($stock_id)
{
    $sql = "SELECT price FROM ".TB_PREF."prices 
	WHERE stock_id = ".db_escape($stock_id)."
    AND sales_type_id = 1 ";
    $result = db_query($sql, "could not retreive location name");
    $row = db_fetch_row($result);
    return $row['0'];
}
//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root, $SysPrefs;
//	$date = $_POST['PARAM_0'];
//   	$category = $_POST['PARAM_1'];
//    $location = $_POST['PARAM_2'];
//    $supplier = $_POST['PARAM_3'];
//    $inactive = $_POST['PARAM_4'];
//    $pictures = $_POST['PARAM_5'];
//   	$check    = $_POST['PARAM_6'];
//   	$shortage = $_POST['PARAM_7'];
//   	$no_zeros = $_POST['PARAM_8'];
    $category = $_POST['PARAM_0'];
   	$price = $_POST['PARAM_1'];
   	$comments = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
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

    if ($price == 1)
        $PS = _('Standard Cost');
    else
        $PS = _('Price');
//	if ($check)
//	{
//		$cols = array(0, 75, 225, 250, 295, 345, 390, 445,	515);
//		$headers = array(_('Stock ID'), _('Description'), _('UOM'), _('Quantity'), _('Check'), _('Demand'), $available, _('On Order'));
//		$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right', 'right');
//	}
//	else
//	{
//		$cols = array(0, 75, 225, 230, 300, 390, 405,	515);
////		$headers = array(_('Items'), _(''), _(''), _('QOH'), _('ROL'), "", _('ROL * Price/ST.Cost'));
//		$aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right');
//	}

    $cols = array(0, 30, 55, 114, 150, 190, 250, 290, 335, 400, 450, 490, 550, 600, 645, 705,
        750, 800, 860, 900, 950, 1010, 1055, 1105, 1160, 1200,1245,1300,1350,1395, 1450);
    //-------------0--1---2----3----4----5----6----7----8----9----10---11---12---13---14---15-
    //----------------------------------------------------------------------------------------
    $aligns = array('left', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right');

    $cols2 = array(0, 30, 55, 114, 150, 190, 250, 290, 335, 400, 450, 490, 550, 600, 645, 705,
        750, 800, 860, 900, 950, 1010, 1055, 1105, 1160, 1200,1245,1300,1350,1395, 1450);
    //-------------0--1---2----3----4----5----6----7----8----9----10---11---12---13---14---15-
    //----------------------------------------------------------------------------------------
    $aligns2 = array('left', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right');

    $get_loc_name = get_location_name_for_header();
    $headers2 = array();
//    $headers = array();

    $i = 0;
    $j = 1;
    $data = array();

    while($row = db_fetch($get_loc_name))
    {

        $bank[$i] = $row['loc_code'];
        $id[$i]= $row['loc_code'];
        $data[$i] = $row['loc_code'];
        if($j==1){}
//        array_append($headers2);
        else
            array_append($headers2, array('','ROL'));
//        array_append($headers, array(''));
        array_append($headers2, array($bank[$i]
        ));

        array_append($headers, array(_('QOH'), _('ROL'), _('x PRC/SC')
        ));

        $i++;
        $j++;

    }

    $params =   array( 	0 => $comments,
    				1 => array('text' => _('Price'), 'from' => $PS, 'to' => ''),
            2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
            /*3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
            4 => array('text' => _('Only Shortage'), 'from' => $short, 'to' => ''),
            5 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''),
            6 => array('text' => _('Inactive'), 'from' => $in_active, 'to' => '')*/);

   	$rep = new FrontReport(_('Location Wise Re-order Report'), "StockCheckSheet", 'A4_LOC', 9, $orientation);
//    if ($orientation == 'L')
//    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns,$cols2,$headers2,$aligns2);
    $rep->NewPage();

	$res = getTransactions($category, $location,$supplier, $like, $inactive,$date);
	$catt = '';
    $item = '';
    $qoht =array(0,0,0,0,0,0,0,0);
    $rolt =array(0,0,0,0,0,0,0,0);
	while ($trans=db_fetch($res))
	{
		if ($location == 'all')
			$loc_code = "";
		else
			$loc_code = $location;
		$demandqty = get_demand_qty($trans['stock_id'], $loc_code);
		$demandqty += get_demand_asm_qty($trans['stock_id'], $loc_code);
		$onorder = get_on_porder_qty($trans['stock_id'], $loc_code);
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
            $rep->font('b');
//			$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(0, 1, $trans['cat_description']);
            $rep->font('');
			$catt = $trans['cat_description'];
			$rep->NewLine();

		}

		$rep->NewLine(0.2);
		$dec = get_qty_dec($trans['stock_id']);
        $rep->font('b');
//		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(0, 7,$trans['stock_id']."  ". $trans['description'].($trans['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
        $rep->font('');

        $res2 = get_locationwise_data($trans['stock_id']);
        $rep->NewLine();

//        while ($trans2=db_fetch($res2))
        {
//            if ($trans2['reorder_level']==0)
//                continue;
            $rep->NewLine(0.2);



//            $rep->TextCol(0, 1, $trans2['loc_code']);
//            $rep->TextCol(1, 2, get_locode_name($trans2['loc_code']));
            $tot_loc=get_total_location_name_for_header();

            for ($i = 0; $i < $tot_loc; $i++)
            {
                $qoh = get_qoh_on_date($trans['stock_id'], $data[$i]);
                $rol = get_rol($trans['stock_id'], $data[$i]);
                $rolt[$i] += get_rol($trans['stock_id'], $data[$i]);
                $qoht[$i] +=get_qoh_on_date($trans['stock_id'], $data[$i]);
                $s_n=$i*3;

                $rep->AmountCol(0+$s_n, 1+$s_n, $qoh, 2);

                $rep->AmountCol($s_n+1, $s_n+2, $rol, 2);
                if($price == 0)
                    $rep->AmountCol($s_n+2, $s_n+3, $rol * get_sales_price($trans['stock_id']), 2);
                elseif($price == 1)
                    $rep->AmountCol($s_n+2, $s_n+3, $rol * $trans['material_cost'], 2);

            }


//            $rep->AmountCol(4, 5, $trans2['reorder_level'], 2);
//            if($price == 0)
//            $rep->AmountCol(6, 7, $trans2['reorder_level'] * get_sales_price($trans['stock_id']), 2);
//            elseif($price == 1)
//            $rep->AmountCol(6, 7, $trans2['reorder_level'] * $trans['material_cost'], 2);
        }
        $rep->NewLine();

	}
	$rep->Line($rep->row - 2);
    $rep->NewLine();
	$rep->font('b');
   $rep->TextCol(0, 1, "TOTAL");
   $rep->NewLine();
    $tot_loc=get_total_location_name_for_header();

    for ($i = 0; $i < $tot_loc; $i++)
    {
        $s_n1=$i*3;
        $rep->AmountCol(0+$s_n1 , 1+$s_n1 , $qoht[$i], 2);
        $rep->AmountCol($s_n1+1, $s_n1+2, $rolt[$i], 2);
    }
    $rep->font('');

    $rep->Line($rep->row - 2);
    $rep->End();
}