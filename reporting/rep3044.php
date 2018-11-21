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

function getTransactions2($category, $location, $fromcust, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_category.description AS cat_description,
			".TB_PREF."stock_master.stock_id,
			".TB_PREF."stock_master.description, ".TB_PREF."stock_master.inactive,
			".TB_PREF."stock_moves.loc_code,
			".TB_PREF."debtor_trans.debtor_no,
			".TB_PREF."debtors_master.name AS debtor_name,
			".TB_PREF."stock_moves.tran_date,
			SUM(-".TB_PREF."stock_moves.qty) AS qty,
			SUM(-".TB_PREF."stock_moves.qty*".TB_PREF."stock_moves.price*(1-".TB_PREF."stock_moves.discount_percent)) AS amt,
			SUM(-IF(".TB_PREF."stock_moves.standard_cost <> 0, ".TB_PREF."stock_moves.qty * ".TB_PREF."stock_moves.standard_cost, ".TB_PREF."stock_moves.qty *(".TB_PREF."stock_master.material_cost + ".TB_PREF."stock_master.labour_cost + ".TB_PREF."stock_master.overhead_cost))) AS cost
		FROM ".TB_PREF."stock_master,
			".TB_PREF."stock_category,
			".TB_PREF."debtor_trans,
			".TB_PREF."debtors_master,
			".TB_PREF."stock_moves
		WHERE ".TB_PREF."stock_master.stock_id=".TB_PREF."stock_moves.stock_id
		AND ".TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id
		AND ".TB_PREF."debtor_trans.debtor_no=".TB_PREF."debtors_master.debtor_no
		AND ".TB_PREF."stock_moves.type=".TB_PREF."debtor_trans.type
		AND ".TB_PREF."stock_moves.trans_no=".TB_PREF."debtor_trans.trans_no
		AND ".TB_PREF."stock_moves.tran_date>='$from'
		AND ".TB_PREF."stock_moves.tran_date<='$to'
		AND (".TB_PREF."debtor_trans.type=".ST_CUSTDELIVERY." OR ".TB_PREF."stock_moves.type=".ST_CUSTCREDIT.")
		AND (".TB_PREF."stock_master.mb_flag='B' OR ".TB_PREF."stock_master.mb_flag='M')";
		if ($category != 0)
			$sql .= " AND ".TB_PREF."stock_master.category_id = ".db_escape($category);
		if ($location != '')
			$sql .= " AND ".TB_PREF."stock_moves.loc_code = ".db_escape($location);
		if ($fromcust != '')
			$sql .= " AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($fromcust);
		$sql .= " GROUP BY ".TB_PREF."stock_master.stock_id, ".TB_PREF."debtors_master.name ORDER BY ".TB_PREF."stock_master.category_id,
			".TB_PREF."stock_master.stock_id, ".TB_PREF."debtors_master.name";
    return db_query($sql,"No transactions were returned");

}
function getTransactions($loc_frm, $loc_to, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."stock_moves.stock_id, ".TB_PREF."stock_moves.qty,".TB_PREF."stock_moves.reference, 
".TB_PREF."stock_moves.price,".TB_PREF."stock_master.description,".TB_PREF."stock_master.material_cost
FROM ".TB_PREF."stock_moves, ".TB_PREF."stock_master 
WHERE ".TB_PREF."stock_moves.stock_id = ".TB_PREF."stock_master.stock_id 
AND ".TB_PREF."stock_moves.type = 16 
AND ".TB_PREF."stock_moves.loc_code IN ('$loc_frm','$loc_to')
AND ".TB_PREF."stock_moves.qty>0	
AND ".TB_PREF."stock_moves.tran_date>='$from' 
AND ".TB_PREF."stock_moves.tran_date<='$to'";

    return db_query($sql,"No transactions were returned");

}
//----------------------------------------------------------------------------------------------------

function print_inventory_sales()
{
    global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $from_loc = $_POST['PARAM_2'];
    $to_loc = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];

    if (!$from_loc || !$to_loc) return;

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");


	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

	$cols = array(0, 55, 120, 250,  365, 440,	520);

	$headers = array(_('Item Code'), _('Reference'), _('Description'), _('Quantity'), _('Price'), _('Amount'));
	if ($fromcust != '')
		$headers[2] = '';

	$aligns = array('left',	'left','left',	'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to));

    $rep = new FrontReport(_('Inventory Location Transfer'), "InventorySalesReport", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();



    $res = getTransactions($from_loc, $to_loc, $from, $to);
    while ($trans = db_fetch($res)) {

    $rep->TextCol(0, 1, $trans['stock_id']);
    $rep->TextCol(1, 2, $trans['reference']);
    $rep->TextCol(2, 3, $trans['description']);
    $rep->AmountCol(3, 4, abs($trans['qty']), $dec);
    $rep->AmountCol(4, 5, $trans['material_cost'], $dec);
    $rep->AmountCol(5, 6, $trans['qty'] * $trans['material_cost'], $dec);

        $amt_tot += $trans['qty'] * $trans['material_cost'];
        $qt_tot += abs($trans['qty']);

    $rep->NewLine();
    }
    $rep->Line($rep->row  - 4);
    $rep->NewLine(2);
    $rep->TextCol(2, 3, "Total");
    $rep->AmountCol(3, 4, $qt_tot, $dec);
    $rep->AmountCol(5, 6, $amt_tot, $dec);

    $rep->Line($rep->row  - 4);
    $rep->End();
}

?>