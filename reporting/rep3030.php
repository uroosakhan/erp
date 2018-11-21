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
function get_pry_qty_sum($stock)
{
    $sql = "SELECT SUM(qty) 
    FROM ".TB_PREF."stock_moves 
			
		WHERE stock_id = ".db_escape($stock)."
		
		";

    $result = db_query($sql, "could not get tax amount");
    $row = db_fetch_row($result);
    return $row[0];


}

function get_pry_qty_sum_uom($stock)
{
    $sql = "SELECT SUM(qty / con_factor) 
    FROM ".TB_PREF."stock_moves 
			
		WHERE stock_id = ".db_escape($stock)."
		
		";

    $result = db_query($sql, "could not get tax amount");
    $row = db_fetch_row($result);
    return $row[0];


}
function getTransactions($category, $location, $from, $to,$supplier, $item_like, $inactive)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id, item.units,item.carton,item.alt_units,
			item.description, item.inactive,move.loc_code,
			item.amount1,
			item.amount4,
			SUM(move.con_factor) AS Sec_qty,
			IF(move.stock_id IS NULL, '', move.loc_code) AS loc_code,
			SUM(IF(move.stock_id IS NULL,0,move.qty)) AS QtyOnHand,
			move.type AS Type,move.units_id,item.carton
		FROM ("
        .TB_PREF."stock_master item,"
        .TB_PREF."stock_category category)
			LEFT JOIN ".TB_PREF."stock_moves move ON item.stock_id=move.stock_id
			
		WHERE item.category_id=category.category_id
		AND (item.mb_flag='B' OR item.mb_flag='M')
		AND move.tran_date>='$from'
		AND move.tran_date<='$to'
		";
    if ($category != 0)
        $sql .= " AND item.category_id = ".db_escape($category);
//		$sql .= " AND item.stock_id = ".db_escape(9010001);

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


function get_individual_stock_code_for_amount3($item_code,$location,$from,$to)
{
    $from = date2sql($from);
   $to =  date2sql($to);

    $sql = " SELECT * FROM ".TB_PREF."stock_moves WHERE stock_id = ".db_escape($item_code)."
      AND tran_date >= ".db_escape($from)."
      AND tran_date <= ".db_escape($to)."
     ";

    if ($location != 'all')
        $sql .= " AND IF(".TB_PREF."stock_moves.stock_id IS NULL, '1=1',".TB_PREF."stock_moves.loc_code = ".db_escape($location).")";

    $sql.="  GROUP BY trans_no,trans_id ";

    return db_query($sql, "Error");
}

function get_individual_stock_code_for_amount32($item_code,$location)
{
    $sql = " SELECT * FROM ".TB_PREF."workorders 
    WHERE id = ".db_escape($item_code);

     $query = db_query($sql, "Error");
    return db_fetch($query);
}
//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root, $SysPrefs;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $items = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $supplier = $_POST['PARAM_4'];
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
        $cols = array(0, 55, 290, 335, 380, 450, 515);
        $headers = array(_('Stock ID'), _('Description'), _('Location'), _('Pry Quantity'), _('Sec Quantity'), _('Units'));
        $aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right', 'right');
    }
    else
    {
        $cols = array(0, 55, 290, 335, 380, 450, 515);
        $headers = array(_('Stock ID'), _('Description'), _('Location'), _('Pry Quantity'), _('Sec Quantity'), _('Units'));
        $aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');
    }


    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('supplier'), 'from' => $sup, 'to' => ''),
        4 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        5 => array('text' => _('Only Shortage'), 'from' => $short, 'to' => ''),
        6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''),
        7 => array('text' => _('Inactive'), 'from' => $in_active, 'to' => ''));

    $rep = new FrontReport(_('Stock Check Sheets - Multi'), "StockCheckSheetMulti", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();


    $catt = $ItemCode = '';
    $pryTotal = $secTotal = $rollsTotal = $GrandPryQtyTotal = $GrandSecQtyTotal = $GrandEqualTotal = 0.0;
    foreach($items as $key => $value) {

        $res = getTransactions($value, $location, $from, $to,$supplier, $like, $inactive);

        while ($trans = db_fetch($res)) {

            if($location == 'all') {
                $trans['loc_code'] = $location;
            }

            $dec = get_qty_dec($trans['stock_id']);
$item = get_item($trans['stock_id']);
if($item['stock_id'] == $trans['stock_id']) {
    $sum = get_pry_qty_sum($trans['stock_id'],$trans['units_is']);
}
//            display_error($sum);
            if ($location == 'all')
                $loc_code = "";
            else
                $loc_code = $location;
            $demandqty = get_demand_qty($trans['stock_id'], $trans['loc_code']);
            $demandqty += get_demand_asm_qty($trans['stock_id'], $trans['loc_code']);
            $onorder = get_on_porder_qty($trans['stock_id'], $trans['loc_code']);
            $flag = get_mb_flag($trans['stock_id']);
            if ($flag == 'M')
                $onorder += get_on_worder_qty($trans['stock_id'], $trans['loc_code']);
           
            if ($shortage && $trans['QtyOnHand'] - $demandqty >= 0)
                continue;
            if ($catt != $trans['cat_description']) {
                if ($catt != '') {
                    $rep->font('b');
                    $rep->NewLine(1, 2);
                    $rep->TextCol(0, 1, "Total");
                    $rep->AmountCol(3, 4, $pryTotal, $dec);
                    $rep->AmountCol(4, 5, $secTotal, $dec);
                    $rep->AmountCol(5, 6, $rollsTotal, $dec);
                    $rep->font('');
                }


                if ($catt != '') {
                    $rep->Line($rep->row - 2);
                    $rep->NewLine();

                }
                $pryTotal = $secTotal = $rollsTotal = 0.0;
                $rep->font('B');
                $rep->TextCol(0, 1, $trans['category_id']);
                $rep->TextCol(1, 2, $trans['cat_description']);
                $rep->font('');
                $catt = $trans['cat_description'];
                $rep->NewLine();

            }





//      R.A

//            display_error($location."+".$trans['loc_code']);
//            die;
            $result = get_individual_stock_code_for_amount3($trans['stock_id'],$trans['loc_code'],$from,$to);
            $Positive = $Negative = $Equal = 0;
            $Positive_pry = $Negative_pry = $Equal_pry = 0;
            while ($myrow = db_fetch($result)) {




              $a = get_individual_stock_code_for_amount32($myrow['trans_no']);
                if ($ItemCode != $myrow['stock_id']) {

                    if ( $myrow['qty'] >= 0) {
                        $Positive += $myrow['amount3'];
                    }
                    elseif($myrow['qty'] < 0 && $myrow['amount3'] < 0 ){
                        // display_error(abs($myrow['amount3']));
                         $Positive += abs($myrow['amount3']);
                    }
                    else{
                        $Negative += $myrow['amount3'];
                    }
//------------------------Primary Quantity ---------------------
                    if ( $myrow['qty'] >= 0) {
                        $Positive_pry += $myrow['qty'];
                    }
                    else{
                        $Negative_pry += $myrow['qty'];
                    }
//------------------------ End ---------------------
//                    if ($myrow['type'] == ST_SUPPRECEIVE || $myrow['type'] == ST_CUSTCREDIT || ($myrow['type'] == ST_INVADJUST && $myrow['qty'] >= 0 ) ||  ($myrow['type'] == ST_LOCTRANSFER && $myrow['qty'] >= 0 ) )
//                        $Positive += $myrow['amount3'];
//                    if ($myrow['type'] == ST_CUSTDELIVERY  || $myrow['type'] == ST_SUPPCREDIT  ||  ($myrow['type'] == ST_LOCTRANSFER && $myrow['qty'] < 0 ) || ($myrow['type'] == ST_INVADJUST && $myrow['qty'] < 0 ) )
//                        $Negative += $myrow['amount3'];
//                    if ($myrow['type'] == ST_WORKORDER  && $myrow['qty'] >= 0)
//                        $Positive += $myrow['amount3'];
//                    if ($myrow['type'] == ST_WORKORDER   && $myrow['qty'] < 0)
//                        $Negative += $myrow['amount3'];
//                    if ($myrow['type'] == ST_MANURECEIVE && $myrow['qty'] >= 0)
//                        $Positive += $myrow['amount3'];
//                    if ($myrow['type'] == ST_MANURECEIVE && $myrow['qty'] < 0)
//                        $Negative += $myrow['amount3'];

                }
            }

            $Equal = ($Positive - $Negative);
 if ($no_zeros && $Equal == 0)
                continue;


            $Equal_pry = ($Positive_pry + $Negative_pry);




//              if($Equal == 0)
//                continue;


             $rep->TextCol(0, 1, $trans['stock_id']);

             $rep->TextCol(1, 2, $trans['description'] . ($trans['inactive'] == 1 ? " (" . _("Inactive") . ")" : ""), -1);

             $rep->TextCol(2, 3, $location);
             $item = get_item($trans['stock_id']);




if($item['units'] == 'in' &&   $item['alt_units']  == 'kg' )
{
    $sec_qty = number_format2($Equal_pry * $item['con_factor'],2);
}
else{
    $sec_qty = number_format2($Equal_pry ,2);
}
            $rep->TextCol(3, 4, number_format2($Equal_pry ,2) . " // " . $item['units']."/".$item['alt_units'] , 2);
            $rep->TextCol(4, 5, $sec_qty . " / " . $trans['alt_units'], $dec);
            $rep->TextCol(5, 6, $Equal . "/ " . $trans['carton'], $dec);
            $rep->Line($rep->row - 2);

            $pryTotal += $trans['amount1'] * $Equal;
            $GrandPryQtyTotal += $trans['amount1'] * $Equal;
            $secTotal += $trans['amount4'] * $Equal;
            $GrandSecQtyTotal += $trans['amount4'] * $Equal;
            $rollsTotal += $Equal;
            $GrandEqualTotal += $Equal;
            if ($check) {
//			$rep->TextCol(4, 5, "_________");
//			$rep->AmountCol(5, 6, $demandqty, $dec);
//			$rep->AmountCol(6, 7, $trans['QtyOnHand'] - $demandqty, $dec);
//			$rep->AmountCol(7, 8, $onorder, $dec);
            } else {
//			$rep->AmountCol(4, 5, $demandqty, $dec);
//			$rep->AmountCol(5, 6, $trans['QtyOnHand'] - $demandqty, $dec);
//			$rep->AmountCol(6, 7, $onorder, $dec);
            }
            if ($pictures) {
                $image = company_path() . '/images/'
                    . item_img_name($trans['stock_id']) . '.jpg';
                if (file_exists($image)) {
//                    $rep->NewLine();
                    if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
                        $rep->NewPage();
                    $rep->AddImage($image, $rep->cols[1], $rep->row - $SysPrefs->pic_height, 0, $SysPrefs->pic_height);
                    $rep->row -= $SysPrefs->pic_height;
//                    $rep->NewLine();
                }
            }

            $ItemCode = $trans['stock_id'];
            $rep->NewLine();
        }
    }
    if ($catt != '')
    {
        $rep->NewLine(1);

        $rep->TextCol(0, 4, _('Total'));

    }
    $rep->AmountCol(3, 4,$pryTotal,$dec);
    $rep->AmountCol(4, 5,$secTotal,$dec);
    $rep->AmountCol(5, 6,$rollsTotal,$dec);

    if ($catt != '')
    {
        $rep->Line($rep->row - 4);
        $rep->NewLine();
    }
    $rep->NewLine(2, 1);
    $rep->font('b');
    $rep->TextCol(0, 4, _('Grand Total'));
    $rep->AmountCol(3, 4, $GrandPryQtyTotal, $dec);
    $rep->AmountCol(4, 5, $GrandSecQtyTotal, $dec);
    $rep->AmountCol(5, 6, $GrandEqualTotal, $dec);
    $rep->font('');
    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}

