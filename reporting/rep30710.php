<?php
$page_security = 'SA_ITEMSVALREP';
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

function fetch_items($category=0)
{
		$sql = "SELECT stock_id, stock.description AS name,
				stock.category_id,
				units,
				cat.description
			FROM ".TB_PREF."stock_master stock LEFT JOIN ".TB_PREF."stock_category cat ON stock.category_id=cat.category_id
				WHERE mb_flag <> 'D'";
		if ($category != 0)
			$sql .= " AND cat.category_id = ".db_escape($category);
		$sql .= " ORDER BY stock.category_id, stock_id";

    return db_query($sql,"No transactions were returned");
}

function trans_qty($stock_id, $location=null, $from_date, $to_date, $inward = true)
{
	if ($from_date == null)
		$from_date = Today();

	$from_date = date2sql($from_date);	

	if ($to_date == null)
		$to_date = Today();

	$to_date = date2sql($to_date);

	$sql = "SELECT ".($inward ? '' : '-')."SUM(qty) FROM ".TB_PREF."stock_moves
		WHERE stock_id=".db_escape($stock_id)."
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'";

	if ($location != '')
		$sql .= " AND loc_code = ".db_escape($location);

	if ($inward)
		$sql .= " AND qty > 0 ";
	else
		$sql .= " AND qty < 0 ";

	$result = db_query($sql, "QOH calculation failed");

	$myrow = db_fetch_row($result);	

	return $myrow[0];

}
function get_quantity_for_location_DEF($stock_id, $StockLocation, $from_dates, $to_dates)
{

    $from_date = date2sql($from_dates);
    $to_date = date2sql($to_dates);

    $sql = "SELECT SUM(-1 * qty) as qty
		FROM ".TB_PREF."stock_moves moves
		WHERE loc_code=".db_escape($StockLocation)."
		AND type IN(13, 16)
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'
		AND stock_id = ".db_escape($stock_id);
    $query =  db_query($sql, "could not query stock moves");
    $fetch = db_fetch($query);
    return $fetch[0];
}
function get_quantity_for_location_STORE($stock_id, $StockLocation, $from_dates, $to_dates)
{
    $from_date = date2sql($from_dates);
    $to_date = date2sql($to_dates);

    $sql = "SELECT SUM(-1 * qty) as qty
		FROM ".TB_PREF."stock_moves moves
		WHERE loc_code=".db_escape($StockLocation)."
		AND type IN(13, 16)
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'
		AND stock_id = ".db_escape($stock_id);
    $query =  db_query($sql, "could not query stock moves");
    $fetch = db_fetch($query);
    return $fetch[0];
}
function get_quantity_for_location_SHOP2($stock_id, $StockLocation, $from_date, $to_date)
{
    $from_date = date2sql($from_date);
    $to_date = date2sql($to_date);

    $sql = "SELECT SUM(-1 * qty) as qty
		FROM ".TB_PREF."stock_moves moves
		WHERE loc_code=".db_escape($StockLocation)."
		AND type = 13
		AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date'
		
		AND stock_id = ".db_escape($stock_id);
    $query =  db_query($sql, "could not query stock moves");
    $fetch = db_fetch($query);
    return $fetch[0];
}
//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root;

    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];

//	$location = 'DEF'; //dz 27.1.13

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
		$location = '';
	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	//$cols = array(0, 100, 300, 365, 440, 540, 640, 715);
//	$cols = array(0, 120, 140, 220, 300, 400, 480, 500);
	$cols = array(0, 45, 105, 175, 245, 325, 390, 465);

	$headers = array(_('Category'), _('Description'), _('Qty Sold Shop'), _('Stock Shop'), _('Qty Sold Store'),  _('Stock Store'),
        _('Qty Sold RAIL.'), _('Stock RAIL.'));

	$aligns = array('left',	'right',	'right', 'right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
						1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => '')
						);

    $rep = new FrontReport(_('Daily Sales Report'), "InventoryMovements", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$result = fetch_items($category);

	$catgor = '';

    $sql = "SELECT * FROM ".TB_PREF."locations";
    if($location != '')
        $sql .= " WHERE loc_code = '$location'";
    $query = db_query($sql, "Error");

   // while($fetch = db_fetch($query))
    {
        while ($myrow=db_fetch($result))
        {
            $qoh_start= $inward = $outward = $qoh_end = $qoh_end_shop  = $qoh_end_shop2 = 0;
//            only location transfer and delivery quantity show
            $outward += trans_qty($myrow['stock_id'], $location, $from_date, $to_date, false);

            if($location == ''){
                $quantity_DEF = get_quantity_for_location_DEF($myrow['stock_id'], DEF, $from_date, $to_date);
                $quantity_STORE = get_quantity_for_location_STORE($myrow['stock_id'], STORE, $from_date, $to_date);
                $quantity_SHOP2 = get_quantity_for_location_SHOP2($myrow['stock_id'], RR, $from_date, $to_date);
            }
            else//only delivery quantity show
                $quantity = get_quantity_for_location_DEF($myrow['stock_id'], $location);
            if ($outward == 0) continue;

            if ($catgor != $myrow['description'])
            {
                $rep->Line($rep->row  - $rep->lineHeight);
                $rep->NewLine(2);
                $rep->fontSize += 1;
                $rep->TextCol(0, 3, $myrow['category_id'] . " - " . $myrow['description']);
                $catgor = $myrow['description'];
                $rep->fontSize -= 1;
                $rep->NewLine();
            }


            $rep->NewLine();

            $rep->TextCol(0, 2,	$myrow['stock_id']);
            //$rep->TextCol(1, 2, $myrow['name']);
            //$rep->TextCol(2, 3, $myrow['units']);

            //DEF  = SHOP
            //STORE = STORE
            //SHOP2 = RAILWAY ROAD
            if($location == '')
                $rep->AmountCol(2, 3, $quantity_DEF);
            if($location == 'DEF')
                $rep->AmountCol(2, 3, $quantity);
            if($location == '')
                $rep->AmountCol(4, 5, $quantity_STORE);
            if($location == 'STORE')
                $rep->AmountCol(4, 5, $quantity);
            if($location == '')
                $rep->AmountCol(6, 7, $quantity_SHOP2);
            if($location == 'RR')
                $rep->AmountCol(6, 7, $quantity);
//            $qoh_start += get_qoh_on_date($myrow['stock_id'], $fetch['loc_code'], add_days($from_date, -1));
//            $qoh_end_shop += get_qoh_on_date($myrow['stock_id'], DEF, $to_date);
//            $qoh_end += get_qoh_on_date($myrow['stock_id'], STORE, $to_date);
//            $qoh_end_shop2 += get_qoh_on_date($myrow['stock_id'], SHOP2, $to_date);

            $qoh_end_shop += get_qoh_on_date($myrow['stock_id'], DEF, $to_date);
            $qoh_end += get_qoh_on_date($myrow['stock_id'], STORE, $to_date);
            $qoh_end_shop2 += get_qoh_on_date($myrow['stock_id'], RR, $to_date);

//            $inward += trans_qty($myrow['stock_id'], $fetch['loc_code'], $from_date, $to_date);
//    		$outward += trans_qty($myrow['stock_id'], $location, $from_date, $to_date, false);



            //$rep->AmountCol(3, 4, $outward, get_qty_dec($myrow['stock_id']));
            if($location == 'DEF')
             $rep->AmountCol(3, 4, $qoh_end_shop, get_qty_dec($myrow['stock_id']));
            if($location == '')
                $rep->AmountCol(3, 4, $qoh_end_shop, get_qty_dec($myrow['stock_id']));

            if($location == 'STORE')
                $rep->AmountCol(5, 6, $qoh_end, get_qty_dec($myrow['stock_id']));
            if($location == '')
                $rep->AmountCol(5, 6, $qoh_end, get_qty_dec($myrow['stock_id']));

            if($location == 'RR')
                $rep->AmountCol(7, 8, $qoh_end_shop2, get_qty_dec($myrow['stock_id']));
            if($location == '')
                $rep->AmountCol(7, 8, $qoh_end_shop2, get_qty_dec($myrow['stock_id']));


            $rep->NewLine(0, 1);

            $total_outward += $outward;

            $qoh_end_shop_total += $qoh_end_shop;
            $qoh_end_total += $qoh_end;
            $qoh_end_shop2_total += $qoh_end_shop2;
        }
    }
	$rep->Line($rep->row  - 4);

//	$rep->NewLine(2);
//    $rep->TextCol(0, 1, _("Total"));
//    $rep->AmountCol(3, 4, _("$qoh_end_shop_total"));
//    $rep->AmountCol(5, 6, _("$qoh_end_total"));
//    $rep->AmountCol(7, 8, _("$qoh_end_shop2_total"));


		//$rep->TextCol(3, 4, _("$total_outward"));

    $rep->End();
}

?>