<?php

//$page_security = 'SA_ITEMSALESSUMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Chaitanya
// date_:	2005-05-19
// Title:	Sales Summary Report
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

function getTransactions($category, $from, $to, $folk)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT item.category_id,
			category.description AS cat_description,
			item.stock_id,
			item.description,
			line.unit_price * trans.rate AS unit_price,
			SUM(line.quantity) as quantity
		FROM ".TB_PREF."stock_master item,
			".TB_PREF."stock_category category,
			".TB_PREF."debtor_trans trans,
			".TB_PREF."debtor_trans_details line
		WHERE line.stock_id = item.stock_id
		AND item.category_id=category.category_id
		AND line.debtor_trans_type=trans.type
		AND line.debtor_trans_no=trans.trans_no
		AND trans.tran_date>='$from'
		AND trans.tran_date<='$to'
		AND line.quantity<>0
		AND item.mb_flag <>'F'
		AND line.debtor_trans_type = ".ST_SALESINVOICE;

		if ($category != 0)
			$sql .= " AND item.category_id = ".db_escape($category);
	   if ($folk != 0)
		$sql .= " AND trans.salesman=".db_escape($folk);

		$sql .= " GROUP BY item.category_id,
			category.description,
			item.stock_id,
			item.description,
			line.unit_price
		ORDER BY item.category_id, item.stock_id, line.unit_price";
			
	//display_notification($sql);
	
    return db_query($sql,"No transactions were returned");

}

//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $category = $_POST['PARAM_2'];
	$folk = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
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

	if ($folk == ALL_NUMERIC)
		$folk = 0;

	$cols = array(0, 100, 260, 300, 350, 425, 430, 515);

	$headers = array(_('Item/Category'), _('Description'), _('Qty'), _('Unit Price'), _('Sales'), '', _('Remark'));	

	$aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'left');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Category'), 'from' => $cat, 'to' => ''));

    $rep = new FrontReport(_('Item Sales Summary Report'), "ItemSalesSummaryReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions($category, $from, $to, $folk);
	$total = $grandtotal = $total1 = $grandtotal1=$item_total=$item_total2=$item_total3 = 0.0;
	$catt = '';
	$item ='';
	while ($trans=db_fetch($res))
	{
		if ($catt != $trans['cat_description'])
		{
			if ($catt != '')
			{
				$rep->NewLine(2, 3);
				$rep->TextCol(0, 4, _('Total'));
				$rep->AmountCol(2, 3, $total1, get_qty_dec($trans['stock_id']));
				$rep->AmountCol(4, 5, $total, $dec);
				$rep->Line($rep->row - 2);
				$rep->NewLine();
				$rep->NewLine();
				$total = 0.0;
				$total1 = 0.0;

			}
			$rep->TextCol(0, 1, $trans['category_id']);
			$rep->TextCol(1, 7, $trans['cat_description']);
			$catt = $trans['cat_description'];
			$rep->NewLine();
		}



		///ansar
		if ($item != $trans['description'])
		{
			if ($item != '')
			{
				$rep->NewLine(2, 3);
				$rep->TextCol(0, 4, _('Total'));
				$rep->AmountCol(2, 3, $item_total);
				$rep->AmountCol(3, 4, $item_total3, $dec);
				$rep->AmountCol(4, 5, $item_total2, $dec);
				$rep->Line($rep->row - 2);
				$rep->NewLine();
				//$rep->NewLine();
				$item_total2 = 0.0;
				$item_total = 0.0;
				$item_total3=0.0;

			}
			$rep->TextCol(0, 1, $trans['stock_id']);
			$rep->TextCol(1, 7, $trans['description']);
			$item = $trans['description'];
			//$rep->NewLine();
		}
		$rep->NewLine();
		$rep->fontSize -= 2;
		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(1, 2, $trans['description']);
		$rep->AmountCol(2, 3, $trans['quantity'], get_qty_dec($trans['stock_id']));
		$rep->AmountCol(3, 4, $trans['unit_price'], $dec);
		$rep->AmountCol(4, 5, $trans['quantity']*$trans['unit_price'], $dec);
		if ($trans['unit_price'] == 0)
			$rep->TextCol(6, 7, _('Gift'));
		$rep->fontSize += 2;
		$total += $trans['quantity']*$trans['unit_price'];
		$total1 += $trans['quantity'];
		$item_total +=$trans['quantity'];
		$item_total3+=$trans['unit_price'];
		$item_total2 +=$trans['quantity']*$trans['unit_price'];
		$grandtotal += $trans['quantity']*$trans['unit_price'];
	    $grandtotal1 += $trans['quantity'];

	}
	$rep->NewLine(2, 3);
	$rep->TextCol(0, 4, _('Total'));
	$rep->AmountCol(2, 3, $item_total);
	$rep->AmountCol(3, 4, $item_total3);
	$rep->AmountCol(4, 5, $item_total2, $dec);

	$rep->NewLine(2, 3);
	$rep->TextCol(0, 4, _('Total'));
	$rep->AmountCol(2, 3, $total1,  get_qty_dec($trans['stock_id']));
		$rep->AmountCol(4, 5, $total, $dec);

	$rep->Line($rep->row - 2);
	$rep->NewLine();
	$rep->NewLine(2, 1);
	$rep->TextCol(0, 4, _('Grand Total'));
	$rep->AmountCol(2, 3, $grandtotal1, get_qty_dec($trans['stock_id']));
	$rep->AmountCol(4, 5, $grandtotal, $dec);

	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

