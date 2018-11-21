<?php
$page_security = 'SA_SALESANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Inventory Sales Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_sales();

function getTransactions($category, $location, $fromcust, $from, $to, $item_location, $unit)
{
	$from = date2sql($from);
	$to = date2sql($to);


	$sql = "SELECT
				".TB_PREF."sales_order_details.description,
				SUM(".TB_PREF."sales_order_details.quantity) AS qty,
				".TB_PREF."stock_master.carton,
				".TB_PREF."stock_master.stock_id,
				".TB_PREF."stock_master.units
	
			FROM
				".TB_PREF."sales_order_details,
				".TB_PREF."sales_orders,
				".TB_PREF."stock_master,			
				".TB_PREF."debtors_master,
				".TB_PREF."item_units
	
			WHERE 
				".TB_PREF."stock_master.stock_id = ".TB_PREF."sales_order_details.stk_code
			AND
				".TB_PREF."sales_order_details.order_no = ".TB_PREF."sales_orders.order_no
			AND
				".TB_PREF."sales_orders.debtor_no = ".TB_PREF."debtors_master.debtor_no
			AND
				".TB_PREF."item_units.abbr = ".TB_PREF."stock_master.units
			AND
				".TB_PREF."item_units.abbr = '$unit'
			AND
				".TB_PREF."sales_orders.ord_date >= '$from'
			AND
				".TB_PREF."sales_orders.ord_date <= '$to'
			AND
				(".TB_PREF."stock_master.mb_flag='B'
			OR
			".TB_PREF."stock_master.mb_flag='M')";
			//$sql .= " GROUP BY ".TB_PREF."stock_master.stock_id
	
	
			if ($category != 0)
				$sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
			if ($location != '')
				$sql .= " AND ".TB_PREF."sales_orders.from_stk_loc= ".db_escape($location);
			if ($item_location != -1)
				$sql .= " AND ".TB_PREF."stock_master.item_location = ".db_escape($item_location);
			if ($fromcust != '')
	$sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromcust);

	$sql .= " GROUP BY ".TB_PREF."sales_order_details.description
			ORDER BY
				".TB_PREF."stock_master.units";


		//$sql .= " GROUP BY ".TB_PREF."stock_master.stock_id
			//ORDER BY
			//".TB_PREF."stock_master.stock_id";
    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
    $location = $_POST['PARAM_3'];
	$item_location = $_POST['PARAM_4'];
    $fromcust = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];
	$destination = $_POST['PARAM_8'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

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

	if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);

	$cols = array(0, 75, 175, 250, 300, 375, 450,	515);

	$headers = array(_('Serial No.'), _('Stock Id'), _('Description'), _(''), _(''), _('Units'), _('Quantity'));

	$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Bulk Delivery Order Report'), "BulkDeliveryReport", user_pagesize(), 10, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$sql = "SELECT * FROM ".TB_PREF."item_units";
	$fetch = db_query($sql , "hello");
	$S_no = 0;



	while($get_unit = db_fetch($fetch))
	{
		$rep->TextCol(0, 1, $get_unit['name']);
		$rep->NewLine();
		$res = getTransactions($category, $location, $fromcust, $from, $to, $item_location, $get_unit['abbr']);
		$grandqty = $grandtotal0 = 0.0;
		while ($trans = db_fetch($res))
		{
if($trans['qty'] == 0)
continue;
			$rep->NewLine();
			$rep->fontSize -= 2;
			$crtn = $trans['qty'] / $trans['carton'];
			$round_crtn = floor($crtn);
			$dec_crtn = $crtn - $round_crtn;
			//$loose_units = $dec_crtn * $trans['carton'];
			$S_no++;
			$rep->TextCol(0, 1, $S_no);
			$rep->TextCol(1, 2, $trans['stock_id']);
			$rep->TextCol(2, 4, $trans['description']);
			//$rep->TextCol(5, 6, $trans['units']);
			$rep->AmountCol(6, 7, $trans['qty'], get_qty_dec($trans['stock_id']));// qty
			$rep->fontSize += 2;
			$grandqty += $trans['qty'];
		}
		$rep->Line($rep->row - 2);
		$rep->NewLine();
if($grandqty == 0)
continue;
		$rep->TextCol(0, 4, _('Total'));
		$rep->AmountCol(6, 7, $grandqty, $dec);// shariq
		$rep->Line($rep->row - 2);
		$rep->NewLine();
	}
	$rep->NewLine();
	//$rep->TextCol(0, 4, _('Grand Total'));
	//$rep->AmountCol(6, 7, $grandqty, $dec);// shariq
	//$rep->Line($rep->row - 2);
	$rep->NewLine(5);
	$rep->TextCol(0, 2, _("____________________"));
	$rep->NewLine();
	$rep->TextCol(0, 2, _("AUTHORISED SIGNATURE"));
    $rep->End();
}

?>