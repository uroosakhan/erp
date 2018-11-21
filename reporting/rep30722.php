<?php
$page_security = 'SA_ITEMSMOVREP';
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

function fetch_items($category=0, $inactive)
{
		$sql = "SELECT stock.inactive, stock_id, stock.description AS name,
				stock.category_id,
				stock.material_cost,
				units,
				cat.description
			FROM ".TB_PREF."stock_master stock LEFT JOIN ".TB_PREF."stock_category cat ON stock.category_id=cat.category_id
				WHERE mb_flag <> 'D'";
		if ($category != 0)
			$sql .= " AND cat.category_id = ".db_escape($category);
		//if ($inactive != 0)
			$sql .= " AND stock.inactive = ".db_escape($inactive);
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
function get_stock_price1($stock_id)
{
	$sql = "SELECT * FROM ".TB_PREF."prices WHERE stock_id=".db_escape($stock_id);
	
	$result = db_query($sql,"price could not be retreived");
	
	return db_fetch($result);
}
//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root;

    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
	$inactive = $_POST['PARAM_3'];
	$location = $_POST['PARAM_4'];
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

//	if ($location == ALL_TEXT)
//		$location = '';
	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	//$cols = array(0, 100, 300, 365, 420, 510, 640, 715);
	$cols = array(0, 25, 80, 200, 220, 250, 290, 330, 380, 440,480, 520);

	$headers = array(_('S.No'),_('Category'), _('Description'), _('UOM'), _('S.Price'), _('S.Cost'), _('Opening'), _('Quantity In'), _('Quantity Out'), _('Balance'), _('ROL'));

	$aligns = array('left','left',	'left',	'left', 'right', 'right', 'right', 'right', 'right','right','right');

    $params =   array( 	0 => $comments,
						1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
						3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Movements'), "InventoryMovements", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$result = fetch_items($category, $inactive);

	$catgor = '';
	while ($myrow=db_fetch($result))
	{
		if ($catgor != $myrow['description'])
		{
			$rep->Line($rep->row  - $rep->lineHeight);
			$rep->NewLine(2);
			$rep->fontSize += 2;
			$rep->TextCol(0, 3, $myrow['category_id'] . " - " . $myrow['description']);
			$catgor = $myrow['description'];
			$rep->fontSize -= 2;
			$rep->NewLine();
			$serial_no = 0;
			$qty1 = 0;
		}
		$serial_no ++ ;
		
		$rep->NewLine();
		$rep->TextCol(0, 1,	$serial_no);
		$rep->TextCol(1, 2,	$myrow['stock_id']);
		//$rep->TextCol(2, 3, $myrow['name']);
		$rep->TextCol(2, 3, $myrow['name'].($myrow['inactive']==1 ? " ("._("Inactive").")" : ""), -1);
		$rep->TextCol(3, 4, $myrow['units']);
		$qoh_start= $inward = $outward = $qoh_end = 0; 
		
		$qoh_start += get_qoh_on_date($myrow['stock_id'], $location, add_days($from_date, -1));
		$qoh_end += get_qoh_on_date($myrow['stock_id'], $location, $to_date);
		
		$inward += trans_qty($myrow['stock_id'], $location, $from_date, $to_date);
		$outward += trans_qty($myrow['stock_id'], $location, $from_date, $to_date, false);
		$prices = get_stock_price1($myrow['stock_id']);
        if(!user_check_access('SA_ITEMSPRICES')) {
            $rep->AmountCol(4, 5, $prices['price']);
            $rep->AmountCol(5, 6, $myrow['material_cost']);
        }
		//$rep->AmountCol(4, 5, $qoh_start, get_qty_dec($myrow['stock_id']));
		$rep->AmountCol(6, 7, $qoh_start, get_qty_dec($myrow['stock_id']));//opening
		$rep->AmountCol(7, 8, $inward, get_qty_dec($myrow['stock_id']));//in qty
		$rep->AmountCol(8, 9, $outward, get_qty_dec($myrow['stock_id']));//out qty
        if(!user_check_access('SA_ITEMSPRICES')) {
		$rep->AmountCol(9, 10, $qoh_end, get_qty_dec($myrow['stock_id']));//balance
            }
		if($qoh_end < 5)
			$rep->TextCol(10, 11, "Yes");//ROL
		else
			$rep->TextCol(10, 11, "No");//ROL
$grand_total_balance += $qoh_end;// balance
$grand_total_price += $prices['price'];// s price
$grand_total_material_cost += $myrow['material_cost'];// s cost
		
		
		$rep->NewLine(0, 1);
	}
$rep->Line($rep->row - 4);
	

	$rep->NewLine(2);
	$rep->TextCol(0, 3, "Grand Total");//GRAND TOTAL
    if(!user_check_access('SA_ITEMSPRICES')) {
        $rep->AmountCol(9, 10, $grand_total_balance, get_qty_dec($myrow['stock_id'])); // Balance Grand Total
        $rep->AmountCol(4, 5, $grand_total_price, get_qty_dec($myrow['stock_id'])); // Price Grand Total
        $rep->AmountCol(5, 6, $grand_total_material_cost, get_qty_dec($myrow['stock_id'])); // Material_Cost Grand Total
    }
$rep->Line($rep->row - 4);
	
    $rep->End();
}

?>