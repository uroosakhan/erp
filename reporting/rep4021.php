<?php

$page_security = 'SA_BOMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Work Order Listing
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_work_order_listing();

function getTransactions($from, $to,$items, $open_only, $location)
{
	$sql = "SELECT
		workorder.id,
		workorder.wo_ref,
		workorder.type,
		location.location_name,
		item.description,
		item.stock_id,
		item.con_type,
		item.con_factor,
		item.units,
		item.alt_units,
		SUM(workorder.units_reqd)as required,
		SUM(workorder.units_issued)as issued,
		workorder.date_,
		workorder.required_by,
		workorder.closed,
		workorder.stock_id
		FROM ".TB_PREF."workorders as workorder,"
			.TB_PREF."stock_master as item,"
			.TB_PREF."locations as location
		WHERE workorder.stock_id=item.stock_id 
			AND workorder.loc_code=location.loc_code";
    $date_after = date2sql($from);
    $date_before = date2sql($to);

    $sql .=  " AND workorder.date_ >= '$date_after'"
        ." AND workorder.date_ <= '$date_before'";
	if ($open_only != 0)
		$sql .= " AND workorder.closed=0";

	if ($location != '')
		$sql .= " AND workorder.loc_code=".db_escape($location);

	if ($items != '')
		$sql .= " AND workorder.stock_id=".db_escape($items);
	
	$sql .=" GROUP BY workorder.stock_id ORDER BY workorder.id";

    return db_query($sql,"No transactions were returned");

}

//----------------------------------------------------------------------------------------------------

function print_work_order_listing()
{
    global $path_to_root, $wo_types_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $item = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
    $open_only = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = 'L';

	if ($item == '')
		$items = _('All');
	else
	{
		$row = get_item($item);
		$items = $row['description']; 
	}

	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	$open = $open_only == 1 ? _('Yes') : _('No');
	
	$cols = array(0,30, 68, 135, 190, 240, 260,  300, 350,380);

	$headers = array( _('Location'),_('Stock ID'), _('Item'), _('Required'), _('Manufactured'), _('UoM'),_('Con Factor'),_('Alt Qty'),_('Alt UoM'));

	$aligns = array('left', 'left', 'left', 'right', 'right','right','right','right', 'right', 'left');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Items'), 'from' => $items, 'to' => ''),
    				    2 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    3 => array('text' => _('Open Only'), 'from' => $open, 'to' => ''));

    $rep = new FrontReport(_('Work Order Listing Summary'), "WorkOrderListingSummary", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions($from, $to,$item, $open_only, $location);
	while ($trans=db_fetch($res))
	{
		//$rep->TextCol(0, 1, $wo_types_array[$trans['type']]);
		//$rep->TextCol(1, 2, $trans['id'], -1);
		//$rep->TextCol(2, 3, $trans['wo_ref'], -1);
		$rep->TextCol(0, 1, $trans['location_name'], -1);
		$rep->TextCol(1, 2, $trans['stock_id'], -1);
		$rep->TextCol(2, 3, $trans['description'], -1);
		$dec = get_qty_dec($trans['stock_id']);
		$rep->AmountCol(3, 4, $trans['required'], $dec);
		$rep->AmountCol(4, 5, $trans['issued'], $dec);
		$rep->TextCol(5, 6, $trans['units'], -1);
		$rep->AmountCol(6, 7, $trans['con_factor'], $dec);
        if($trans['con_type'] == 1)
        {
            $rep->AmountCol(7, 8, $trans['issued']/$trans['con_factor'], $dec);

       $total_qty=$trans['issued']/$trans['con_factor'];

        }
        else
        {
            $rep->AmountCol(7, 8,  $trans['issued']*$trans['con_factor'], $dec);

			$total_qty=$trans['issued']*$trans['con_factor'];



		}
		$rep->TextCol(8, 9, $trans['alt_units'], -1);
		$total_required +=$trans['required'];
		$total_issued +=$trans['issued'];
		$total +=$total_qty;


//		$rep->TextCol(11, 12, '   '.sql2date($trans['date_']), -1);
//		$rep->TextCol(12, 13,' '.sql2date($trans['required_by']), -1);
//		$rep->TextCol(13, 14, $trans['closed'] ? ' ' : _('No'), -1);
		$rep->NewLine();
	}
	$rep->NewLine(-0.5);
	$rep->Font('b');

	$rep->Line($rep->row);
	$rep->NewLine(+0.5);

	$rep->fontSize += 2;
	$rep->NewLine();

	$rep->TextCol(0, 1, "Total", -1);
	$rep->TextCol(3, 4, number_format2($total_required,$dec), -1);
	$rep->TextCol(4, 5,number_format2($total_issued,$dec), -1);
	$rep->TextCol(7, 8, number_format2($total,$dec), -1);



    $rep->End();
}