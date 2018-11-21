<?php

//$page_security = 'SA_STOCKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Jujuk
// date_:	2011-05-24
// Title:	Stock Movements
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui/ui_input.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

//----------------------------------------------------------------------------------------------------

inventory_movements();

function get_service_item($order_no)
{
		$sql = "SELECT GROUP_CONCAT(pod.description SEPARATOR ', ') FROM ".TB_PREF."stock_master stock
			LEFT JOIN ".TB_PREF."purch_order_details pod ON stock.stock_id=pod.item_code
				WHERE  pod.order_no =".db_escape($order_no)
				." AND stock.mb_flag = 'D'";

	$res = db_query($sql, 'cannot find oldest delivery date');
	$date = db_fetch_row($res);
	return $date[0];
}

function get_dimension_rep($dimension)
{
	$sql = "SELECT CONCAT(name) AS tname FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
//function get_dimen_only_ref($dimension)
//{
//    $sql = "SELECT reference FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension);
//    $result = db_query($sql, "could not get customer");
//    $row = db_fetch_row($result);
//    return $row[0];
//}

function get_custt_name($dimension_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master master
	LEFT JOIN ".TB_PREF."sales_orders orders ON master.debtor_no=orders.debtor_no
	WHERE orders.dimension_id=".db_escape($dimension_id);

	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_color_total($comb_1,$stock_id)
{
//	$sql = "SELECT COUNT(*) FROM ".TB_PREF."purch_orders
//	WHERE h_comb1=".db_escape($comb_1);
$sql = "SELECT * FROM " . TB_PREF . "purch_orders po
	LEFT JOIN " . TB_PREF . "purch_order_details pod ON po.order_no=pod.order_no
WHERE  `h_comb1` =$comb_1
AND pod.item_code='$stock_id' ";
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_color_name($h_comb1)
{
    $sql = "SELECT description FROM ".TB_PREF."combo1 WHERE combo_code=".db_escape($h_comb1);
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_grn_value($order)
{
	$sql = "SELECT COUNT(*) 
FROM  `".TB_PREF."grn_batch` 
WHERE  `purch_order_no` =$order";

	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_po_color_value($color,$stock_id)
{
	$sql = "SELECT * FROM " . TB_PREF . "purch_orders po
	LEFT JOIN " . TB_PREF . "purch_order_details pod ON po.order_no=pod.order_no
WHERE  `h_comb1` =$color
AND pod.item_code='$stock_id' ";

	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_stock_grn()
{
$sql = "SELECT master.description,stock_id,COUNT(stock_id) As tqty FROM ".TB_PREF."stock_master master
	LEFT JOIN ".TB_PREF."grn_items items ON master.stock_id=items.item_code
	WHERE master.mb_flag != 'D' 
	GROUP BY items.item_code";

	return db_query($sql, "could not get customer");
}

//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root;

    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
	$location = $_POST['PARAM_2'];
	$dimension = $_POST['PARAM_3'];
    $color = $_POST['PARAM_4'];
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

	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	$cols = array(0, 100, 200, 240, 280, 330, 410, 470, 520);

	$headers = array(_('O.N / Dimension'), _('Variant'), _('Color'), _('Chasis #'),
		_('Engine #'), _('Optional Features'), _('Location'), _('Customer'));

	$aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
						1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
						3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Stock Report'), "InventoryMovements", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $res=get_stock_grn();
    while ($myrow2=db_fetch($res)) {
        $rep->Font('bold');
        $rep->fontSize += 2;
        $t_qty=$myrow2['tqty'];
$rep->TextCol(0, 1,$myrow2['description']);


$rep->fontSize -= 2;
$rep->Font('');
                

        $sql1 = "SELECT combo_code, description FROM " . TB_PREF . "combo1 WHERE combo_code != 0";
       if ($color != 0)
          $sql1 .= " AND combo_code=" . db_escape($color);
       $sql1 .= " GROUP BY combo_code";
        $result1 = db_query($sql1, "The customers could not be retrieved");
       while ($myrow1 = db_fetch($result1)) {
           $check_color=get_po_color_value($myrow1['combo_code'],$myrow2['stock_id']);
           if ($check_color == 0) continue;
           $rep->NewLine();
            $rep->Font('bold');
            //$rep->fontSize += 3;
             $rep->TextCol(0, 1, $myrow1['description']);
              $color1 = $myrow1['combo_code'];
           $stock_id = $myrow2['stock_id'];
            $color_total = get_color_total($color1,$stock_id);
           //  $rep->TextCol(2, 3, $color_total);
            $rep->Font('');
            
            //$rep->fontSize -= 3;
           

            $sql = "SELECT * FROM " . TB_PREF . "purch_orders po
	LEFT JOIN " . TB_PREF . "purch_order_details pod ON po.order_no=pod.order_no
	LEFT JOIN " . TB_PREF . "grn_batch grn ON po.order_no=grn.purch_order_no
    WHERE po.order_no != 0
   
    AND pod.item_code='$stock_id'
    AND po.h_comb1='$color1'
	";
            if ($dimension != 0)
                $sql .= " AND po.dimension = " . db_escape($dimension);
            if ($location != '')
                $sql .= " AND po.into_stock_location=" . db_escape($location);
            $sql .= " ORDER BY pod.item_code ";
            $result = db_query($sql, "The customers could not be retrieved");
            $i = 1;
            
            while ($myrow = db_fetch($result)) {
                $check_order = get_grn_value($myrow['order_no']);
                $qoh = get_qoh_on_date($stock_id, $myrow["into_stock_location"]);
                if ($check_order == 0 || $qoh==0) continue;
                 $rep->NewLine(-1);
                $rep->TextCol(2, 3,$qoh);
                $t_qoh +=$qoh;
                $rep->NewLine(+1);
//                //$rep->NewLine();
//                $rep->TextCol(0, 1, $myrow1['description']);
                $rep->NewLine();
                
                $rep->TextCol(0, 1, get_stock_grn($myrow['mb_flag']));
                //$rep->NewLine();
                $rep->TextCol(0, 1, get_dimension_rep($myrow['dimension']));
                $rep->TextCol(1, 2, $myrow['description']);
                $rep->TextCol(2, 3, get_color_name($myrow['h_comb1']));
                $rep->TextCol(3, 4, $myrow['h_text1']);
                $rep->TextCol(4, 5, $myrow['h_text2']);
                $rep->TextCol(5, 6, get_service_item($myrow['order_no']));
                $rep->TextCol(6, 7, get_location_name($myrow['into_stock_location']));
                $rep->TextCol(7, 8, get_custt_name($myrow['dimension']));
                $i++;
            }
            $color_total=0;
            $rep->TextCol(2, 3,$t_qoh);
            $t_qty=0;
         //  $rep->NewLine();
}
 $rep->NewLine();
    }
    //$rep->NewLine();
    $rep->NewLine();
    $rep->End();
}

