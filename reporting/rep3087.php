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

function get_stock_movements_for_reports($stock_id,$stock_id2, $StockLocation='',$BeforeDate, $AfterDate, $category, $sys_type, $sorter)
{
    $before_date = date2sql($BeforeDate);
    $after_date = date2sql($AfterDate);
    $sql = "SELECT ".TB_PREF."stock_moves.type,".TB_PREF."stock_master.units,
    ".TB_PREF."stock_moves.trans_no, ".TB_PREF."stock_moves.tran_date, 
    ".TB_PREF."stock_moves.person_id, 
	".TB_PREF."stock_moves.qty, 
	".TB_PREF."stock_moves.reference,
	".TB_PREF."stock_moves.stock_id,
	".TB_PREF."stock_moves.standard_cost,
	".TB_PREF."stock_master.material_cost,".TB_PREF."stock_moves.price,".TB_PREF."stock_moves.qty,".TB_PREF."stock_moves.loc_code,


SUM(IF(".TB_PREF."stock_moves.standard_cost <> 0, ".TB_PREF."stock_moves.qty * ".TB_PREF."stock_moves.standard_cost, ".TB_PREF."stock_moves.qty *(".TB_PREF."stock_master.material_cost + ".TB_PREF."stock_master.labour_cost + ".TB_PREF."stock_master.overhead_cost))) AS cost



		FROM ".TB_PREF."stock_moves
		INNER JOIN ".TB_PREF."stock_master ON ".TB_PREF."stock_moves.stock_id=".TB_PREF."stock_master.stock_id";

    $sql .= " WHERE ".TB_PREF."stock_moves.tran_date >= '". $after_date . "'
		AND ".TB_PREF."stock_moves.tran_date <= '" . $before_date ."'
		AND ".TB_PREF."stock_moves.stock_id >= ".db_escape($stock_id)."
		AND ".TB_PREF."stock_moves.stock_id <= ".db_escape($stock_id2)."
		
		";
    if($StockLocation !='')
        $sql .= " AND ".TB_PREF."stock_moves.loc_code=".db_escape($StockLocation);

    if ($category != "")
        $sql .= " AND ".TB_PREF."stock_master.category_id =".db_escape($category);
//    if ($stock_id != "")
//        $sql .= " AND ".TB_PREF."stock_moves.stock_id =".db_escape($stock_id);
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
        $sql .=	" GROUP BY ".TB_PREF."stock_moves.tran_date,".TB_PREF."stock_moves.trans_id
        ORDER BY ".TB_PREF."stock_moves.stock_id, ".TB_PREF."stock_moves.tran_date ASC";
//    $sql .=	" GROUP BY ".TB_PREF."stock_moves.tran_date,".TB_PREF."stock_moves.trans_id";
    return db_query($sql, "could not query stock moves");
}

function get_movement_type_report($type_id)
{
    $sql = "SELECT * FROM ".TB_PREF."movement_types WHERE id=".db_escape($type_id);

    $result = db_query($sql, "could not get item movement type");

    return db_fetch($result);
}
function get_sales_order($trans_no)
{
    $sql = "SELECT ".TB_PREF."sales_orders.reference, ".TB_PREF."sales_orders.debtor_no FROM ".TB_PREF."sales_orders,".TB_PREF."workorders,
    ".TB_PREF."wo_manufacture
     WHERE ".TB_PREF."sales_orders.order_no = ".TB_PREF."workorders.sale_order
     AND ".TB_PREF."workorders.id = ".TB_PREF."wo_manufacture.workorder_id
     AND ".TB_PREF."wo_manufacture.id =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch($result);
    return $row;
}
function get_salesorder_customer_name($debtor_no)
{
    $sql = "SELECT debtor_ref FROM ".TB_PREF."debtors_master
     WHERE debtor_no =".db_escape($debtor_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_wo_ref($trans_no)
{
    $sql = "SELECT ".TB_PREF."workorders.wo_ref FROM ".TB_PREF."workorders, ".TB_PREF."wo_manufacture
     WHERE ".TB_PREF."workorders.id =".TB_PREF."wo_manufacture.workorder_id
     AND ".TB_PREF."wo_manufacture.id = ".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_description_name_3087($stock_id)
{
   $sql = "SELECT CONCAT(stock_id,'-',description) FROM ".TB_PREF."stock_master  WHERE stock_id ="
      .db_escape($stock_id);

   $result = db_query($sql, "could not retreive the location name for $stock_id");

      $row = db_fetch_row($result);
      return $row[0];
}
//----------------------------------------------------------------------------------------------------

function inventory_movements()
{
    global $path_to_root,$systypes_array;;

    $from_date = $_POST['PARAM_0'];
    $to_date = $_POST['PARAM_1'];
    $item = $_POST['PARAM_2'];
    $item2 = $_POST['PARAM_3'];
    $category = $_POST['PARAM_4'];
    $location = $_POST['PARAM_5'];
    $sys_type = $_POST['PARAM_6'];
    $sorter = $_POST['PARAM_7'];
    $show_cost = $_POST['PARAM_8'];
    $comments = $_POST['PARAM_9'];
    $orientation = $_POST['PARAM_10'];
    $destination = $_POST['PARAM_11'];
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

    $result1 = get_stock_movements_for_reports($item,$item2,$location,$to_date,$from_date,$category,$sys_type,$sorter);

    $myrow1=db_fetch($result1);
    if ($sys_type == -1)
        $s_type = _('All');
    else
        $s_type = $systypes_array[$myrow1['type']];

    $cols = array(0, 26, 50, 125, 175, 310, 310, 350, 410,470,510,560,610,653,708,773);

    $headers = array(_('Type'), _('#'),	_('Ref.'),	_('Date'), _('Detail'), _(''), _('Qty '),
        _('RATE '),  _('AMT '), _('QTY'),_('RATE'),_('AMT'),_('QTY'),_('       RATE'),_('AMT'));

    $aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'right','right','right','right',
        'right','right','right','right','right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from_date, 'to' => $to_date),
        2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
        3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
        4 => array('text' => _('Items'), 'from' => get_description_name_3087($item), 'to' => get_description_name_3087($item2)),
        5 => array('text' => _('Type'), 'from' => $s_type, 'to' => ''),
        6 => array('text' => _('Sorter'), 'from' => $sort, 'to' => '')
    );

    $rep = new FrontReport(_('Inventory Movements'), "InventoryMovements", user_pagesize(), 9, $orientation);
    // if ($orientation == 'L')
    // 	recalculate_cols($cols);
    $rep->SetHeaderType('Header3087');
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    
    $rep->NewPage();

    $result = get_stock_movements_for_reports($item,$item2,$location,$to_date,$from_date,$category,$sys_type,$sorter);

    $catgor = '';
    if ($sys_type == -1) {
        $rep->Font('bold');
        $rep->TextCol(2, 8, _("Quantity on hand before") . " " . $from_date);
    }

    $before_qty = get_qoh_on_date($item,$location,add_days($from_date, -1),$batch);
    $total_out = $total_in = 0;
    $after_qty = $before_qty;
    $item_total_in = $item_total_out = $item_after_qty=0;

    $dec = get_qty_dec($myrow1['stock_id']);
    if ($sys_type == -1) {
        $rep->AmountCol(6, 7, $before_qty, $dec);
    }

    $rep->Font('');
    $rep->NewLine();
    $catt = '';
    while ($myrow=db_fetch($result))
    {

        if ($catt != $myrow['stock_id'])
        {
            if ($catt != '')
            {
                $rep->Line($rep->row + 9);
                $rep->NewLine(0.5);
                $rep->Font('bold');
                $rep->TextCol(2, 3, _('Total'));
                $rep->AmountCol(6, 7, $item_total_in, $dec);
                $rep->AmountCol(9, 10, $item_total_out, $dec);
                $rep->AmountCol(12,13, $item_after_qty, $dec);
                $rep->Font('');

                $rep->Line($rep->row - 2);
                $rep->NewLine();
                $item_total_in = $item_total_out = $item_after_qty = 0;
            }
            $rep->Font('bold');
            $rep->TextCol(0, 7, _("PRODUCT:") . " " . get_description_name_3087($myrow['stock_id']). " " . ($myrow['units']));
            $rep->Font('');
            $catt = $myrow['stock_id'];
            $rep->NewLine();
        }

        $dec = get_qty_dec($myrow['stock_id']);

        global $systypes_array;

        if ($myrow["qty"] > 0)
        {
            $quantity_formatted = number_format2($myrow["qty"], $dec);
            $total_in += $myrow["qty"];
            $item_total_in += $myrow["qty"];
        }
        else
        {
            $quantity_formatted = number_format2(-$myrow["qty"], $dec);
            $total_out += -$myrow["qty"];
            $item_total_out += -$myrow["qty"];
        }
        $after_qty += $myrow["qty"];
        $item_after_qty += $myrow["qty"];

        $person = $myrow["person_id"];

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

        $qty_in = ($myrow["qty"] >= 0) ? $quantity_formatted : "";

        if($myrow['type'] == 29)
        $sales_order = get_sales_order($myrow['trans_no']);
        $type_name = $systypes_array[$myrow["type"]];
        if($myrow["type"] == ST_MANURECEIVE)
            $rep->TextCol(0, 1, "WOP");
        elseif($myrow["type"] == ST_CUSTDELIVERY)
            $rep->TextCol(0, 1, "DN");
        elseif($myrow["type"] == ST_INVADJUST)
            $rep->TextCol(0, 1, "IA");
        elseif($myrow["type"] == ST_CUSTCREDIT)
            $rep->TextCol(0, 1, "CCN");
        elseif($myrow["type"] == ST_SUPPCREDIT)
            $rep->TextCol(0, 1, "SCN");
        elseif($myrow["type"] == ST_SUPPRECEIVE)
            $rep->TextCol(0, 1, "GRN");
        elseif($myrow["type"] == ST_FAADJUST)
            $rep->TextCol(0, 1, "FAA");
        elseif($myrow["type"] == ST_PURCHREQ)
            $rep->TextCol(0, 1, "PR");
        elseif($myrow["type"] == ST_WORKORDER)
            $rep->TextCol(0, 1, "WO");
        $rep->TextCol(1, 2, $myrow['trans_no']);
        $rep->TextCol(2, 3,  get_reference($myrow['type'], $myrow['trans_no']));
        $rep->TextCol(3, 4, sql2date($myrow["tran_date"]));
        $rep->TextCol(6, 7, (($myrow["qty"] >= 0) ? $quantity_formatted : ""));

        $rep->TextCol(9, 10, (($myrow["qty"] < 0) ? $quantity_formatted : ""));

        $rep->AmountCol(12, 13, $after_qty, $dec);

        if(!user_check_access('SA_ITEMSPRICES'))
        { if($show_cost == 1) {
            if ($myrow['type'] == 25) {

                $rep->AmountCol(7, 8, $myrow['price'], $dec);
                $rep->AmountCol(8, 9, $myrow['price'] * $myrow["qty"], $dec);

            }


            if ($myrow["qty"] >= 0 && $myrow['type'] == 26) {

                $rep->AmountCol(7, 8, $myrow['standard_cost'], $dec);
                $rep->AmountCol(8, 9, $myrow['standard_cost'] * $myrow["qty"], $dec);

            }

            if ($myrow['type'] == 29 || $myrow["qty"] < 0) {

                $rep->AmountCol(10, 11, abs($myrow['standard_cost']), $dec);
                $rep->AmountCol(11, 12, abs($myrow['standard_cost'] * $myrow["qty"]), $dec);
            }

//        if($myrow['type'] == 13 ) {
//
//            $rep->AmountCol(10, 11, abs($myrow['price']), $dec);
//            $rep->AmountCol(11, 12, abs($myrow['price'] * $myrow["qty"]), $dec);
//
//        }


            if ($myrow['type'] == 25 || $myrow['type'] == 13) {

                $rep->AmountCol(13, 14, abs($myrow['price']), $dec);
                $rep->AmountCol(14, 15, abs($myrow['price'] * $myrow["qty"]), $dec);

            }
            if ($myrow['type'] == 26 || $myrow['type'] == 17 || $myrow['type'] == 16 || $myrow['type'] == 29) {

                $rep->AmountCol(13, 14, $myrow['standard_cost'], $dec);
                $rep->AmountCol(14, 15, $myrow['standard_cost'] * $after_qty, $dec);

            }
        }
        }
        
        if ($location == "") {
            if ($destination == 0) {
//	$rep->NewLine(+1);
if($myrow['type'] == 29)
                $rep->TextCollines(4, 5, $person." ".$myrow["loc_code"]." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".get_wo_ref($myrow['trans_no'])." ".($myrow['units']));
else
    $rep->TextCollines(4, 5, $person." ".$myrow["loc_code"]." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".($myrow['units']));

//	$rep->NewLine(-1);
            }
            else {
                if($myrow['type'] == 29)
                $rep->TextCol(4, 5, $person." ".$myrow["loc_code"]." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".get_wo_ref($myrow['trans_no'])." ".($myrow['units']));
          else
              $rep->TextCol(4, 5, $person." ".$myrow["loc_code"]." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".($myrow['units']));
$rep->NewLine();
            }
        }
        else{

            if ($destination == 0) {
//	$rep->NewLine(+1);
                if($myrow['type'] == 29)
                $rep->TextCollines(4, 5, $person." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".get_wo_ref($myrow['trans_no'])." ".($myrow['units']));
else
    $rep->TextCollines(4, 5, $person." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".($myrow['units']));

//	$rep->NewLine(-1);
            }
            else{
                if($myrow['type'] == 29)
                $rep->TextCol(4, 5, $person." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".get_wo_ref($myrow['trans_no'])." ".($myrow['units']));
           else
               $rep->TextCol(4, 5, $person." ".$sales_order['reference'] ." ".get_salesorder_customer_name($sales_order['debtor_no'])." ".($myrow['units']));
$rep->NewLine();

            }

        }

// 		$rep->NewLine(0, 1);
        			if ($rep->row < $rep->bottomMargin + (2 * $rep->lineHeight))
				$rep->NewPage();

    }
    $rep->Line($rep->row + 9);
    $rep->NewLine(0.2);
    $rep->Font('bold');

    $rep->TextCol(2, 3, _('Total'));
    $rep->AmountCol(6, 7, $item_total_in, $dec);

    $rep->AmountCol(9, 10, $item_total_out, $dec);
    $rep->AmountCol(12,13, $item_after_qty, $dec);
    $rep->Font('');
//    $rep->NewLine(-2);

    $rep->Line($rep->row -2);

    if ($sys_type == -1) {
        $rep->NewLine(2);
        $rep->Font('bold');
        $rep->TextCol(2, 8, _("Quantity on hand after") . "    " . $to_date);
        $rep->Font('');

        $rep->AmountCol(6, 7, $total_in, $dec);

        $rep->AmountCol(9, 10, $total_out, $dec);
        $rep->AmountCol(12,13, $after_qty, $dec);
    }
    $rep->NewLine();
     
    $rep->End();
}

?>