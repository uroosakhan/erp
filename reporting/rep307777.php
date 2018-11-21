<?php
//$page_security = 'SA_ITEMSVALREP';
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

function get_stock_movements_before_reports ($stock_id, $StockLocation, $AfterDate, $batch_id)
{
    $after_date = date2sql($AfterDate);

    $sql = "SELECT SUM(qty) 
		FROM ".TB_PREF."stock_moves 
		WHERE stock_id=".db_escape($stock_id) . "
		AND batch = ".db_escape($batch_id) . "
		AND loc_code = ".db_escape( $StockLocation) . "
		AND tran_date < '" . $after_date . "'";
    $before_qty = db_query($sql, "The starting quantity on hand could not be calculated");

    $before_qty_row = db_fetch_row($before_qty);
    return $before_qty_row[0];
}

function get_stock_movements_for_reports($stock_id, $StockLocation='',$BeforeDate, $AfterDate, $category, $sys_type, $sorter)
{
    $before_date = date2sql($BeforeDate);
    $after_date = date2sql($AfterDate);
    $sql = "SELECT ".TB_PREF."stock_moves.type, ".TB_PREF."stock_moves.trans_no, ".TB_PREF."stock_moves.tran_date, ".TB_PREF."stock_moves.person_id, 
	".TB_PREF."stock_moves.qty, 	".TB_PREF."stock_moves.amount3, ".TB_PREF."stock_moves.reference,".TB_PREF."stock_moves.stock_id,".TB_PREF."stock_moves.standard_cost,
	".TB_PREF."stock_master.material_cost,".TB_PREF."stock_moves.price,".TB_PREF."stock_moves.qty,".TB_PREF."stock_moves.loc_code
		FROM ".TB_PREF."stock_moves
		INNER JOIN ".TB_PREF."stock_master ON ".TB_PREF."stock_moves.stock_id=".TB_PREF."stock_master.stock_id";

    $sql .= " WHERE ".TB_PREF."stock_moves.tran_date >= '". $after_date . "'
		AND ".TB_PREF."stock_moves.tran_date <= '" . $before_date ."'";
    if($StockLocation !='')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code=".db_escape($StockLocation);

    if ($category != "")
        $sql .= " AND ".TB_PREF."stock_master.category_id =".db_escape($category);
    if ($stock_id != "")
        $sql .= " AND ".TB_PREF."stock_moves.stock_id =".db_escape($stock_id);
    if ($sys_type != -1)
        $sql .= " AND ".TB_PREF."stock_moves.type =".db_escape($sys_type);
    if ($sorter == 1)
        $sql .= " ORDER BY ".TB_PREF."stock_moves.tran_date ASC";
    if ($sorter == 2)
        $sql .= " ORDER BY ".TB_PREF."stock_moves.trans_no ASC";
    if ($sorter == 3)
        $sql .= " ORDER BY ".TB_PREF."stock_moves.qty ASC";
    if ($sorter == 4)
        $sql .= " ORDER BY ".TB_PREF."stock_moves.loc_code ASC";
    if ($sorter == 0)
        $sql .=	" GROUP BY 
     ".TB_PREF."stock_moves.trans_id";
    // $sql .=	" GROUP BY ".TB_PREF."stock_moves.tran_date,".TB_PREF."stock_moves.trans_id";
    return db_query($sql, "could not query stock moves");
}

function get_movement_type_report($type_id)
{
    $sql = "SELECT * FROM ".TB_PREF."movement_types WHERE id=".db_escape($type_id);

    $result = db_query($sql, "could not get item movement type");

    return db_fetch($result);
}

//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root,$systypes_array;;

    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
    $item = $_POST['PARAM_2'];
    $category = $_POST['PARAM_3'];
    $location = $_POST['PARAM_4'];
    $sys_type = $_POST['PARAM_5'];
    $sorter = $_POST['PARAM_6'];
    $comments = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_9'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');
//    if ($batch  == ALL_NUMERIC)
//        $batch = 0;
//    if ($batch == 0)
//        $batches = _('All');
//    else
//        $batches = get_batch($batch);
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
    if ($sorter == 0)
        $sort = _('All');
    elseif($sorter == 1)
        $sort =  _('Trans Date');
    elseif($sorter == 2)
        $sort =  _('Trans No');
    elseif($sorter == 3)
        $sort =  _('Quantity');
    elseif($sorter == 4)
        $sort =  _('Location');

    $result1 = get_stock_movements_for_reports($item,$location,$to_date,$from_date,$category,$sys_type,$sorter);

    $myrow1=db_fetch($result1);
    if ($sys_type == -1)
        $s_type = _('All');
    else
        $s_type = $systypes_array[$myrow1['type']];

    //$cols = array(0, 100, 300, 365, 440, 540, 640, 715);
    $cols = array(0, 95, 120, 200, 250, 410, 460, 500, 540,595,650,710,770);

    $headers = array(_('Type'), _('#'),	_('Ref.'),	_('Date'), _('Detail'), _('Moving Avg.'), _('Qty In '),
        _('Qty Out '), _('Qty On Hand'),_('Total Value'),_('Actual Rate'),_('Actual Total'));

    $aligns = array('left',	'left',	'left', 'left', 'left', 'right', 'right','right','right','right','right','right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        4 => array('text' => _('Item'), 'from' => get_description_name($item), 'to' => ''),
        5 => array('text' => _('Type'), 'from' => $s_type, 'to' => ''),
        6 => array('text' => _('Sorter'), 'from' => $sort, 'to' => '')
    );

    $rep = new FrontReport(_('Inventory Movements'), "InventoryMovements", user_pagesize(), 9, $orientation);
    // if ($orientation == 'L')
    // 	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $result = get_stock_movements_for_reports($item,$location,$to_date,$from_date,$category,$sys_type,$sorter);

    $catgor = '';
    if ($sys_type == -1) {
        $rep->Font('bold');
        $rep->TextCol(2, 8, _("Quantity on hand before") . " " . $from_date);
    }
    $before_qty_negative = get_negative_uoh_on_date($item,$location,add_days($from_date, -1),$batch);
    $before_qty_positive = get_positive_uoh_on_date($item,$location,add_days($from_date, -1),$batch);
    $total_out = $total_in = 0;
//    $total_out1 = $total_in1 = 0;
//	$after_qty = $before_qty;

//    $before_qty_negative= - $before_qty_negative;
$equal =  ($before_qty_positive - $before_qty_negative) ;
    $after_qty = $equal;
    $dec = get_qty_dec($myrow1['stock_id']);
    if ($sys_type == -1) {
//        $rep->AmountCol(8, 9, $before_qty, $dec);
    }
//    while ($myrow=db_fetch($result))
//    {
//
//        if ($myrow["qty"] > 0)
//        {
//            $quantity_formatted = number_format2($myrow["amount3"], $dec);
//            $total_in1 += $myrow["amount3"];
//        }
//        else
//        {
//            $quantity_formatted = number_format2(-$myrow["amount3"], $dec);
//            $total_out1 += -$myrow["amount3"];
//        }
//
//    }
    $rep->AmountCol(8, 9, $equal, $dec);
    $rep->Font('');
    $rep->NewLine(2);





    while ($myrow=db_fetch($result))
    {

        $dec = get_qty_dec($myrow['stock_id']);

        global $systypes_array;



        if (($myrow["qty"] >= 0 ))
        {
        
            $quantity_formatted = number_format2($myrow["amount3"], $dec);
            $total_in += $myrow["amount3"];
            $after_qty += $myrow["amount3"];
        }
        elseif (($myrow["qty"] < 0 && $myrow["amount3"] < 0))
        {
            $quantity_formatted = number_format2(abs($myrow["amount3"]), $dec);
            $total_in += abs($myrow["amount3"]);
            $after_qty += abs($myrow["amount3"]);
        }
        else
        {
           
            $quantity_formatted = number_format2(-$myrow["amount3"], $dec);
            $total_out += -$myrow["amount3"];
            $after_qty += -$myrow["amount3"];
        }


        $person = $myrow["person_id"];
        $gl_posting = "";


 if (($myrow["type"] == ST_CUSTDELIVERY) || ($myrow["type"] == ST_CUSTCREDIT))
        {
            $cust_row = get_customer_details_from_trans($myrow["type"], $myrow["trans_no"]);

            if (strlen($cust_row['name']) > 0)
                $person = $cust_row['name'] . " (" . $cust_row['br_name'] . ")";

        }
        elseif ($myrow["type"] == ST_SUPPRECEIVE || $myrow['type'] == ST_SUPPCREDIT)
        {
            // get the supplier name
            $supp_name = get_supplier_name($myrow["person_id"]);

            if (strlen($supp_name) > 0)
                $person = $supp_name;
        }
        elseif ($myrow["type"] == ST_LOCTRANSFER || $myrow["type"] == ST_INVADJUST)
        {
            // get the adjustment type
            $movement_type = get_movement_type_report($myrow["person_id"]);
            $person = $movement_type["name"];
        }
        elseif ($myrow["type"]==ST_WORKORDER || $myrow["type"] == ST_MANUISSUE  ||
            $myrow["type"] == ST_MANURECEIVE)
        {
            $person = "";
        }
//        $rep->NewLine();

        $qty_in = ($myrow["amount3"] >= 0) ? $quantity_formatted : "";

//		$rep->NewLine();
        $type_name = $systypes_array[$myrow["type"]];
        $rep->TextCol(0, 1, $type_name);
        $rep->TextCol(1, 2, $myrow['trans_no']);
          if($myrow['type'] == 29)
           $ref =  get_reference(26, $myrow['trans_no']);
        else
            $ref =  get_reference($myrow['type'], $myrow['trans_no']);
            
          $rep->TextCol(2, 3,  $ref );    
            
        $rep->TextCol(3, 4, sql2date($myrow["tran_date"]));
        $rep->AmountCol(5, 6, $myrow['standard_cost'],$dec);
        if($myrow["qty"] >= 0) {
            $rep->TextCol(6, 7, ($myrow["amount3"]));
        }
        if($myrow["qty"] < 0 &&  $myrow["amount3"] > 0  ) {
            $rep->TextCol(7, 8, ($myrow["amount3"]));
        }
        if($myrow["qty"] < 0 &&  $myrow["amount3"] < 0  ) {
            $rep->TextCol(6, 7, (-$myrow["amount3"]));
        }

        $rep->AmountCol(8, 9, $after_qty, $dec);
       
        $rep->AmountCol(9, 10, $myrow['standard_cost'] * $after_qty,$dec);

        if($myrow['type'] == 25 || $myrow['type'] == 17 || $myrow['type'] == 13) {
            $rep->AmountCol(10, 11, $myrow['price'], $dec);
            $rep->AmountCol(11, 12, $myrow['price'] * $myrow["qty"], $dec);
        }
        if ($location == "") {
            if ($destination == 0) {
//	$rep->NewLine(+1);

                $rep->TextCollines(4, 5, $person." ".$myrow["loc_code"] );

//	$rep->NewLine(-1);
            }
            if ($destination == 1) {
                $rep->TextCol(4, 5, $person." ".$myrow["loc_code"] );
            }
        }
        else{

            if ($destination == 0) {
//	$rep->NewLine(+1);

                $rep->TextCollines(4, 5, $person);

//	$rep->NewLine(-1);
            }
            if ($destination == 1) {
                $rep->TextCol(4, 5, $person);
            }

        }
        $rep->NewLine(0, 1);
    }


//    $rep->NewLine(-2);

    $rep->Line($rep->row -14);
    if ($sys_type == -1) {
        $rep->NewLine(4);
        $rep->Font('bold');
        $rep->TextCol(2, 8, _("Quantity on hand after") . "    " . $to_date);
        $rep->Font('');

        $rep->AmountCol(6, 7, $total_in, $dec);
        $rep->AmountCol(7, 8, $total_out, $dec);
        $rep->AmountCol(8, 9, $after_qty, $dec);
    }
    $rep->NewLine();
    $rep->End();
}

?>