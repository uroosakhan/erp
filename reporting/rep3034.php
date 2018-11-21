<?php

//$page_security = 'SA_ITEMS_STOCK';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Stock Check Sheet
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

//------------------------------------------------------------------------

print_stock_check();

function getTransactions()
{
	$sql = "SELECT 
			item.stock_id,
			item.description
		FROM ".TB_PREF."stock_master item
		WHERE item.inactive = 0";

    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_stock_check()
{
    global $path_to_root;

   	$comments = $_POST['PARAM_0'];
	$orientation = $_POST['PARAM_1'];
	$destination = $_POST['PARAM_2'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $cols = array(0, 75);
    $headers = array(_('Stock ID'), _('Description'));
    $aligns = array('left',	'left');

    $params =   array( 	0 => $comments);

   	$rep = new FrontReport(_('Items Detail Listing'), "StockCheckSheet", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$res = getTransactions();

	while ($trans=db_fetch($res))
	{

		$rep->TextCol(0, 1, $trans['stock_id']);
		$rep->TextCol(1, 2, $trans['description'], -1);
        $rep->NewLine();

	}
	$rep->Line($rep->row - 4);
	$rep->NewLine();
    $rep->End();
}