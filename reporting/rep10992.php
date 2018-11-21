<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Orders
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------


print_inventory_adjustment_report();


function get_inventory_adjustment($trans_no)
{
    $sql = "SELECT moves.*, master.description, master.units, loc.location_name FROM ".TB_PREF."stock_moves moves 
            INNER JOIN ".TB_PREF."stock_master master ON master.stock_id = moves.stock_id
            INNER JOIN ".TB_PREF."locations loc ON loc.loc_code = moves.loc_code
            WHERE moves.type = ".ST_INVADJUST."
            AND moves.trans_no = ".db_escape($trans_no);
    $sql .= " ORDER BY moves.trans_id";
    return db_query($sql, "Error in get stock moves function");
}
function get_inventory_adjustment1($trans_no)
{
    $sql = "SELECT moves.*, master.description, master.units, loc.location_name 
FROM ".TB_PREF."stock_moves moves 
            INNER JOIN ".TB_PREF."stock_master master ON master.stock_id = moves.stock_id
            INNER JOIN ".TB_PREF."locations loc ON loc.loc_code = moves.loc_code
            WHERE moves.type = ".ST_INVADJUST."
            AND moves.trans_no = ".db_escape($trans_no);
    $sql .= " ORDER BY moves.trans_id";
    return db_query($sql, "Error in get stock moves function");
}
function get_prepared_name_code($trans_no)
{
    $sql = "SELECT user 
            FROM ".TB_PREF."audit_trail 
            WHERE trans_no=".db_escape($trans_no)."
            AND type = ".ST_INVADJUST."";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];

}
function get_prepared_name($id)
{
    $sql = "SELECT  user_id 
            FROM ".TB_PREF."users  
            WHERE id=".db_escape($id)."";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];

}



function print_inventory_adjustment_report()
{
	global $path_to_root, $SysPrefs;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$print_as_quote = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(4, 70, 255, 350, 445);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'left');

	$params = array('comments' => $comments, 'print_quote' => $print_as_quote);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{

		if ($print_as_quote == 0)
			$rep = new FrontReport(_("SALES ORDER"), "SalesOrderBulk", user_pagesize(), 9, $orientation);
		else
			$rep = new FrontReport(_("QUOTE"), "QuoteBulk", user_pagesize(), 9, $orientation);
	}
    if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++) {

		$myrow = get_inventory_adjustment($i);

		if (db_num_rows($myrow) == 0)
			continue;
		$myrow = db_fetch($myrow);

		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
		$rep->SetHeaderType('Header10992');
		$rep->currency = $cur;
		$rep->Font();
		if ($print_as_quote == 1) {
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			if ($print_as_quote == 1) {
				$rep->title = _('QUOTE');
				$rep->filename = "Quote" . $i . ".pdf";
			} else {
				$rep->title = _("SALES ORDER");
				$rep->filename = "SalesOrder" . $i . ".pdf";
			}
		} else
			$rep->title = ($print_as_quote == 1 ? _("QUOTE") : _("SALES ORDER"));
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_INVADJUST, $contacts);
		$rep->SetHeaderType('Header10992');
		$rep->NewPage();

		$result = get_inventory_adjustment($i);
		$SubTotal = 0;
		$s_no = 0;
		$items = $prices = array();
		$rep->setfontsize(+10);
		$rep->MultiCell(150, 200, "Prepared By: ", 0, 'L', 0, 2, 260, 750, true);
		$rep->MultiCell(150, 250, "_____________________", 0, 'L', 0, 2, 336, 750, true);
		$rep->MultiCell(150, 250, " " . get_prepared_name(get_prepared_name_code($i)), 0, 'L', 0, 2, 345, 745, true);
		$rep->MultiCell(150, 200, "Prepared By: ", 0, 'L', 0, 2, 260, 385, true);
		$rep->MultiCell(150, 250, "_____________________", 0, 'L', 0, 2, 336, 385, true);
		$rep->MultiCell(150, 250, " " . get_prepared_name(get_prepared_name_code($i)), 0, 'L', 0, 2, 345, 380, true);
		$rep->setfontsize(-10);
        $total_qty =0;
		while ($myrow2 = db_fetch($result)) {

			$s_no++;
			$DisplayPrice = number_format2($myrow2["standard_cost"], $dec);
			$DisplayQty = $myrow2["qty"];
			$rep->TextCol(0, 1, $myrow2['stock_id'], -2);
			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			$trans_no = $myrow2['trans_no'];
			$rep->TextCol(2, 3, $DisplayQty, -2);
			$total_qty +=$DisplayQty;
			$rep->TextCol(3, 4, $myrow2['units'], -2);
			$rep->TextCol(4, 5, $DisplayPrice, -2);
			$rep->row = $newrow;
			if ($rep->row < $rep->bottomMargin + (2 * $rep->lineHeight))
				$rep->NewPage();
		}



		if ($s_no >= 2) {
			$rep->NewLine(34 - $s_no);
		} else {
			$rep->NewLine(33);
		}


		$s_no = 0;
		$totalQty = 0;
		$result1 = get_inventory_adjustment1($i);
		while ($myrow22 = db_fetch($result1)) {
			$s_no++;
			$DisplayPrice = number_format2($myrow22["standard_cost"], $dec);
			$DisplayQty = $myrow22["qty"];
			$rep->TextCol(0,  1, $myrow22['stock_id'], -2);
			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow22['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			$trans_no = $myrow22['trans_no'];
			$rep->TextCol(2, 3, number_format2($DisplayQty), -2);
			$totalQty += $DisplayQty;
			$rep->TextCol(3, 4, $myrow22['units'], -2);
			$rep->TextCol(4, 5, $DisplayPrice, -2);
			$rep->row = $newrow;
//			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//				$rep->NewPage();
		}
		$rep->MultiCell(150, 200, "".number_format2($totalQty), 0, 'L', 0, 2, 300, 710, true);
		$rep->MultiCell(150, 200, "Total Quantity", 0, 'L', 0, 2, 220, 710, true);
		$rep->MultiCell(150, 200, "".number_format2($total_qty), 0, 'L', 0, 2, 300, 315, true);
		$rep->MultiCell(150, 200,"Total Quantity", 0, 'L', 0, 2, 220, 315, true);

	}


	if ($email == 0)
		$rep->End();
}

