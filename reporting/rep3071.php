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
function getTransactions($category, $location, $date)
{
    $date = date2sql($date);
    $sql = "SELECT ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_category.description AS cat_description,
			".TB_PREF."stock_master.stock_id,
			".TB_PREF."stock_master.units,
			".TB_PREF."stock_master.description, ".TB_PREF."stock_master.inactive,
			".TB_PREF."stock_moves.loc_code,
			SUM(".TB_PREF."stock_moves.qty) AS QtyOnHand,
			".TB_PREF."stock_master.material_cost + ".TB_PREF."stock_master.labour_cost + ".TB_PREF."stock_master.overhead_cost AS UnitCost,
			SUM(".TB_PREF."stock_moves.qty) *(".TB_PREF."stock_master.material_cost + ".TB_PREF."stock_master.labour_cost + ".TB_PREF."stock_master.overhead_cost) AS ItemTotal
		FROM ".TB_PREF."stock_master,
			".TB_PREF."stock_category,
			".TB_PREF."stock_moves
		WHERE ".TB_PREF."stock_master.stock_id=".TB_PREF."stock_moves.stock_id
		AND ".TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id
		AND ".TB_PREF."stock_master.mb_flag<>'D' 
		AND ".TB_PREF."stock_moves.tran_date <= '$date'
		GROUP BY ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_category.description, ";
    if ($location != 'all')
        $sql .= TB_PREF."stock_moves.loc_code, ";
    $sql .= "UnitCost,
			".TB_PREF."stock_master.stock_id,
			".TB_PREF."stock_master.description
		HAVING SUM(".TB_PREF."stock_moves.qty) != 0";
    if ($category != 0)
        $sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
    if ($location != 'all')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);
    $sql .= " ORDER BY ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_master.stock_id";

    return db_query($sql,"No transactions were returned");
}

function fetch_category($category_id)
{
    $sql = "SELECT 
            cat.category_id,
            cat.description
            FROM ".TB_PREF."stock_category cat 
			WHERE cat.category_id != 0";
    if($category_id != 0)
        $sql .=" AND cat.category_id=".db_escape($category_id);
    $sql.=" GROUP BY cat.category_id";
    return db_query($sql,"No transactions were returned");
}
function fetch_items_codes($category_id, $item)
{
    $sql = "SELECT 
            stock.stock_id,
			stock.description,
			stock.category_id
			FROM ".TB_PREF."stock_master stock
			WHERE mb_flag <> 'D'";
    $sql .= " AND stock.category_id = ".db_escape($category_id);
    if($item)
        $sql .= " AND stock.stock_id = ".db_escape($item);
    $sql.=" ORDER BY stock.stock_id";
    return db_query($sql,"No transactions were returned");
}



function get_stock_price1($stock_id,$batch,$from_date, $to_date, $location=null)
{
    $sql = "SELECT SUM(price) as cost, SUM(- qty * price) AS amt,standard_cost 
	 FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($stock_id)." AND batch=".db_escape($batch)." ";
    $sql.= " AND tran_date >= '$from_date' 
		AND tran_date <= '$to_date' ";
    if ($location != '')
        $sql .= " AND loc_code = ".db_escape($location);
//	$sql.= "GROUP BY stock_id";
    $result = db_query($sql, "error");
    return $result;
}

function trans_qty($stock_id,$location=null, $from_date, $to_date, $inward = true)
{
    if ($from_date == null)
        $from_date = Today();

    $from_date = date2sql($from_date);

    if ($to_date == null)
        $to_date = Today();

    $to_date = date2sql($to_date);

    $sql = "SELECT ".($inward ? '' : '-')."SUM(qty), price,
	 SUM(-qty*price*(1-discount_percent)) AS amt
	 FROM ".TB_PREF."stock_moves
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

function get_standard_cost_for($stock_id,$type)
{
    $sql ="SELECT price FROM ".TB_PREF."stock_moves WHERE
			stock_id=".db_escape($stock_id)."
			AND type=".db_escape($type)."";
    $db = db_query($sql,'ERROR');
    $ft = db_fetch($db);
    return $ft[0];
}
function get_standard_sale_for($stock_id)
{
    $sql ="SELECT price AS prc FROM ".TB_PREF."stock_moves WHERE
			stock_id=".db_escape($stock_id)."
			AND type='13' ";
    $db = db_query($sql,'ERROR');
    $ft = db_fetch($db);
    return $ft[0];
}
function get_items_($item)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id = ".db_escape($item);
    $result = db_query($sql,"items could not be retreived");
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_locations()
{
    $sql = "SELECT loc_code,location_name FROM ".TB_PREF."locations";
    return db_query($sql,"items could not be retreived");


}
//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root;

    $date = $_POST['PARAM_0'];
    $category = $_POST['PARAM_1'];
    $location = $_POST['PARAM_2'];
    $detail = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];
    $destination = $_POST['PARAM_6'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation = 'L');


//	if ($location == ALL_TEXT)
//		$location = '';
    if ($location == '')
        $loc = _('All');
    else
        $loc = get_location_name($location);

    if ($items == '')
        $it = _('All');
    else
        $it = get_items_($items);

    $cols = array(0, 75, 150, 210, 235, 270, 300, 330, 360, 400, 430, 460, 490, 520, 550, 580,
        610, 640, 680, 720, 750, 780, 810, 835, 865, 895, 925, 950,975,1000,1025);

    $loc =  array();
    $i=0;
    $location_name = get_locations();

    while($row = db_fetch($location_name)) {
        $loc[$i] =  $row['location_name'];
        $loc_code[$i] =  $row['loc_code'];



        $i++;
    }
    $headers = array(_('Category'),_('Description'),$loc_code[0],$loc_code[1],$loc_code[2],$loc_code[3],$loc_code[4],$loc_code[5],$loc_code[6],$loc_code[7],$loc_code[8],
        $loc[9],$loc[10],$loc[11],$loc[12],$loc[13],$loc_code[14],$loc_code[15],$loc_code[16],
$loc_code[17],$loc_code[18],$loc_code[19],$loc[20],
        $loc_code[21],$loc_code[22],$loc_code[23],$loc_code[24],$loc_code[25],$loc_code[26],$loc_code[27],$loc_code[28],_("Total available  Qty")	);

    $aligns = array('left','left',	'right', 'right', 'right','right','right','right','right','right','right',
        'right','right','right','right','right','right','right','right','right','right','right','right','right',
        'right','right','right','right','right','right','right','right');

    $params =   array( 	0 => $comments,

        1 => array('text' => _('Item'), 'from' => $it, 'to' => ''),
        2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''));

    $rep = new FrontReport(_('Inventory Status'), "InventoryMovements", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $res = fetch_category($category);


    while ($trans=db_fetch($res))
    {
        $rep->Font('b');
        $rep->TextCol(0, 1, get_category_name($trans['category_id'])."..........");
        $rep->Font('');
        $rep->NewLine(2);
        $items_name = fetch_items_codes($trans['category_id'], $items);
        while ($myrow = db_fetch($items_name))
        {
            $qty_on_hand0 =    get_qoh_on_date($myrow['stock_id'], $loc_code[0]) ;
            $qty_on_hand1 =    get_qoh_on_date($myrow['stock_id'], $loc_code[1]);
            $qty_on_hand2 =    get_qoh_on_date($myrow['stock_id'], $loc_code[2]);
            $qty_on_hand3 =    get_qoh_on_date($myrow['stock_id'], $loc_code[3]);
            $qty_on_hand4=    get_qoh_on_date($myrow['stock_id'], $loc_code[4]);
            $qty_on_hand5=    get_qoh_on_date($myrow['stock_id'], $loc_code[5]);
            $qty_on_hand6=    get_qoh_on_date($myrow['stock_id'], $loc_code[6]);
            $qty_on_hand7=    get_qoh_on_date($myrow['stock_id'], $loc_code[7]);
            $qty_on_hand8=    get_qoh_on_date($myrow['stock_id'], $loc_code[8]);
            $qty_on_hand9=    get_qoh_on_date($myrow['stock_id'], $loc_code[9]);
            $qty_on_hand10 =    get_qoh_on_date($myrow['stock_id'], $loc_code[10]);
            $qty_on_hand11 =    get_qoh_on_date($myrow['stock_id'], $loc_code[11]);
            $qty_on_hand12 =    get_qoh_on_date($myrow['stock_id'], $loc_code[12]);
            $qty_on_hand13 =    get_qoh_on_date($myrow['stock_id'], $loc_code[13]);
            $qty_on_hand14 =    get_qoh_on_date($myrow['stock_id'], $loc_code[14]);
            $qty_on_hand15 =    get_qoh_on_date($myrow['stock_id'], $loc_code[15]);
            $qty_on_hand16 =    get_qoh_on_date($myrow['stock_id'], $loc_code[16]);
            $qty_on_hand17 =    get_qoh_on_date($myrow['stock_id'], $loc_code[17]);
            $qty_on_hand18 =    get_qoh_on_date($myrow['stock_id'], $loc_code[18]);
            $qty_on_hand19 =    get_qoh_on_date($myrow['stock_id'], $loc_code[19]);
            $qty_on_hand20 =    get_qoh_on_date($myrow['stock_id'], $loc_code[20]);
            $qty_on_hand21 =    get_qoh_on_date($myrow['stock_id'], $loc_code[21]);
            $qty_on_hand22 =    get_qoh_on_date($myrow['stock_id'], $loc_code[22]);
            $qty_on_hand23 =    get_qoh_on_date($myrow['stock_id'], $loc_code[23]);
            $qty_on_hand24 =    get_qoh_on_date($myrow['stock_id'], $loc_code[24]);
            $qty_on_hand25 =    get_qoh_on_date($myrow['stock_id'], $loc_code[25]);
            $qty_on_hand26 =    get_qoh_on_date($myrow['stock_id'], $loc_code[26]);
            $qty_on_hand27 =    get_qoh_on_date($myrow['stock_id'], $loc_code[27]);
             $qty_on_hand28 =    get_qoh_on_date($myrow['stock_id'], $loc_code[28]);
            $rep->TextCol(0, 1, ($myrow['stock_id']));
            $rep->TextCol(2, 3,   $qty_on_hand0);
            $rep->TextCol(3, 4,   $qty_on_hand1);
            $rep->TextCol(4, 5,   $qty_on_hand2);
            $rep->TextCol(5, 6 ,  $qty_on_hand3);
            $rep->TextCol(6, 7 ,  $qty_on_hand4);
            $rep->TextCol(7, 8,   $qty_on_hand5);
            $rep->TextCol(8, 9,   $qty_on_hand6);
            $rep->TextCol(9, 10,  $qty_on_hand7);
            $rep->TextCol(10, 11, $qty_on_hand8);
            $rep->TextCol(11, 12, $qty_on_hand9);
            $rep->TextCol(12, 13, $qty_on_hand10);
            $rep->TextCol(13, 14, $qty_on_hand11);
            $rep->TextCol(14, 15, $qty_on_hand12);
            $rep->TextCol(15, 16, $qty_on_hand13);
            $rep->TextCol(16, 17, $qty_on_hand14);
            $rep->TextCol(17, 18, $qty_on_hand15);
            $rep->TextCol(18, 19, $qty_on_hand16);
            $rep->TextCol(19, 20, $qty_on_hand17);
            $rep->TextCol(20, 21, $qty_on_hand18);
            $rep->TextCol(21, 22, $qty_on_hand19);
            $rep->TextCol(22, 23, $qty_on_hand20);
            $rep->TextCol(23, 24, $qty_on_hand21);
            $rep->TextCol(24, 25, $qty_on_hand22);
            $rep->TextCol(25, 26, $qty_on_hand23);
            $rep->TextCol(26, 27, $qty_on_hand24);
            $rep->TextCol(27, 28, $qty_on_hand25);
            $rep->TextCol(28, 29, $qty_on_hand26);
            $rep->TextCol(29, 30, $qty_on_hand27);
            $rep->TextCol(30, 31, $qty_on_hand28);


            $total_qty_onhand =  $qty_on_hand0 + $qty_on_hand1 + $qty_on_hand2 + $qty_on_hand3 + $qty_on_hand4+ $qty_on_hand5+ $qty_on_hand6+
            $qty_on_hand7+ $qty_on_hand8+ $qty_on_hand9+    $qty_on_hand10+ $qty_on_hand11+ $qty_on_hand12+ $qty_on_hand13+ $qty_on_hand14+
            $qty_on_hand15+ $qty_on_hand16+ $qty_on_hand17+ $qty_on_hand18+ $qty_on_hand19+ $qty_on_hand20+ $qty_on_hand21+ $qty_on_hand22+
            $qty_on_hand23+ $qty_on_hand24+ $qty_on_hand25+ $qty_on_hand26+ $qty_on_hand27+$qty_on_hand28;
            $rep->TextCol(31, 32, $total_qty_onhand);



if($destination ==0)

{

$rep->TextColLines(1, 2, $myrow['description']);
}else{

$rep->TextCol(1, 2, $myrow['description']);



}
            


$grand_total +=$total_qty_onhand;
            $rep->NewLine(2);
        }


 $rep->TextCol(1, 2, _("Total"));

 $rep->TextCol(30, 31,$grand_total);





    }
    $rep->End();
}
?>