<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_MANUFTRANSVIEW' : 'SA_MANUFBULKREP';
// ----------------------------------------------------------------
// Title:	Work Orders
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");

//----------------------------------------------------------------------------------------------------

print_workorders();

//----------------------------------------------------------------------------------------------------
$woid = 0;
if ($_GET['trans_no'] != "")
{
	$woid = $_GET['trans_no'];
}

function get_work_order_productions1($woid)
{
	$sql = "SELECT * FROM ".TB_PREF."wo_manufacture WHERE workorder_id="
		.db_escape($woid)." ORDER BY id";
	return db_query($sql, "The work order issues could not be retrieved");
	//return db_fetch($result);
}
function get_work_order_issues1($woid)
{
	$sql = "SELECT * FROM ".TB_PREF."wo_issues WHERE workorder_id=".db_escape($woid)
		." ORDER BY issue_no";
	return db_query($sql, "The work order issues could not be retrieved");

}

//function get_work_order_costing2($stock_id)
//{
//	$sql = " * FROM ".TB_PREF."wo_issue_items WHERE stock_id=".db_escape($stock_id);
//	return db_query($sql, "The work order issues could not be retrieved");
//
//}
function get_work_order_produce1($id)
{
	$sql = "SELECT prod.*, wo.stock_id, item.description AS StockDescription, wo.closed
			FROM ".TB_PREF."wo_manufacture prod,"
		.TB_PREF."workorders wo,"
		.TB_PREF."stock_master item
		WHERE prod.workorder_id=wo.id
		AND item.stock_id=wo.stock_id
		AND prod.id=".db_escape($id);
	$result = db_query($sql, "The work order production could not be retrieved");

	return db_fetch($result);
}
function get_wo_costing1($workorder_id)
{
	$sql="SELECT * 
		FROM ".TB_PREF."wo_costing cost,
			".TB_PREF."journal jl,".TB_PREF."gl_trans gl
		WHERE
			cost.trans_type=gl.type
			 AND cost.trans_no=jl.trans_no
			 AND jl.trans_no=gl.type_no
			 AND jl.type=gl.type
			  AND gl.amount < 0
			AND workorder_id=".db_escape($workorder_id);

	return db_query($sql, "The work order issues could not be retrieved");
	//return db_fetch($result);
}

//function get_account_name1($workorder_id)
//{
//	$sql="SELECT *
//		FROM ".TB_PREF."gl_trans gl,
//			".TB_PREF."journal jl,".TB_PREF."journal jl
//		WHERE
//			jl.trans_no=gl.type_no
//			 AND cost.trans_no=gl.type_no
//			 AND jl.trans_no=gl.type_no
//			AND workorder_id=".db_escape($workorder_id);
//
//	return db_query($sql, "The work order issues could not be retrieved");
//	//return db_fetch($result);
//}

function print_workorders()
{
	global $path_to_root, $dflt_lang;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$email = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(4, 60, 190, 255, 320, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('WORK ORDER'), "WorkOrderBulk", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++) {
		$myrow = get_work_order($i);
		if ($myrow === false)
			continue;
		if ($email == 1) {
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep->title = _('WORK ORDER');
			$rep->filename = "WorkOrder" . $myrow['wo_ref'] . ".pdf";
		}
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contact = array('email' => $myrow['email'], 'lang' => $dflt_lang, // ???
			'name' => $myrow['contact'], 'name2' => '', 'contact');

		$rep->SetCommonData($myrow, null, null, '', 26, $contact);
		$rep->SetHeaderType('Header4099');
		$rep->NewPage();

		$result = get_wo_requirements($i);
		$rep->TextCol(0, 5, _("Work Order Requirements"), -2);
		$rep->NewLine(2);
		while ($myrow2 = db_fetch($result)) {
			$rep->TextCol(0, 1, $myrow2['stock_id'], -2);
			$rep->TextCol(1, 2, $myrow2['description'], -2);

			$rep->TextCol(2, 3, $myrow2['location_name'], -2);
			$rep->TextCol(3, 4, $myrow2['WorkCentreDescription'], -2);
			$dec = get_qty_dec($myrow2["stock_id"]);

			$rep->AmountCol(4, 5, $myrow2['units_req'], $dec, -2);
			$rep->AmountCol(5, 6, $myrow2['units_req'] * $myrow['units_issued'], $dec, -2);
			$rep->AmountCol(6, 7, $myrow2['units_issued'], $dec, -2);
			$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
		$result = get_work_order_productions1($i);
		$rep->NewLine(9);

		$rep->TextCol(0, 1, "Id", -2);
		$rep->TextCol(1, 2, "Reference", -2);
		$rep->TextCol(2, 3, "date", -2);
		$rep->TextCol(3, 4, "Quantity", -2);
		while ($myrow1 = db_fetch($result)) {

//			$rep->MultiCell(190, 20, "Id", 0, 'L', 0, 2, 45, 520, true);
//			$rep->MultiCell(190, 20, "Reference", 0, 'L', 0, 2, 95, 520, true);
//			$rep->MultiCell(190, 20, "date", 0, 'L', 0, 2, 240, 520, true);
//			$rep->MultiCell(190, 20, "Quantity", 0, 'L', 0, 2, 290, 520, true);
//			$rep->MultiCell(190, 20, "Productions", 0, 'L', 0, 2, 130, 500, true);


			//$total_qty += $myrow1['quantity'];
			$rep->NewLine(1);


			$rep->TextCol(0, 1, $myrow1['id'], -2);
			$rep->TextCol(1, 2, $myrow1['reference'], -2);

			$rep->TextCol(2, 3, sql2date($myrow1['date_']), -2);
//			display_error(get_work_order_produce1($myrow1['quantity']));
			$rep->TextCol(3, 4, $myrow1['quantity'], -2);
//			qty_cell(get_work_order_produce1($myrow1['quantity']), -2);
			end_row();
		}//end of while

		$result = get_additional_issues($i);
		$result11 = get_wo_costing1($i);
//		$rep->TextCol($cols+200, "Issues", -2);
		$rep->NewLine(5);
		$rep->TextCol(0, 1, "Id", -2);
		$rep->TextCol(1, 2, "Reference", -2);
		$rep->TextCol(2, 3, "date", -2);
		$rep->TextCol(3, 4, "Quantity", -2);
		while ($myrow3 = db_fetch($result)) {
//			$rep->Text($mcol + 100, _("Date"));

//			$rep->MultiCell(190, 20, "Issues", 0, 'L', 0, 2, 130, 600, true);
//			$rep->MultiCell(190, 20, "Id", 0, 'L', 0, 2, 45, 610, true);
//			$rep->MultiCell(190, 20, "Reference", 0, 'L', 0, 2, 95, 610, true);
//			$rep->MultiCell(190, 20, "date", 0, 'L', 0, 2, 240, 610, true);
//			$rep->MultiCell(190, 20, "Quantity", 0, 'L', 0, 2, 290, 610, true);

			alt_table_row_color($i);
			$rep->NewLine(1);
//
			$rep->TextCol(0, 1, $myrow3["issue_no"], -2);
			$rep->TextCol(1, 2, $myrow3['name'], -2);
			$rep->TextCol(2, 3, sql2date($myrow3["issue_date"]), -2);
			$rep->TextCol(3, 4, $myrow3["qty_issued"], -2);
			end_row();

		end_table();
	}
		

		global $path_to_root, $wo_cost_types;

//		$rep->TextCol(3, 4, "Quantity", -2);
		$rep->NewLine(2.5);

		$rep->TextCol(0, 1, "Id", -2);
		$rep->TextCol(1, 2, "Date", -2);
		$rep->TextCol(2, 3, "Account Name", -2);
		$rep->TextCol(3, 4, "Amount", -2);
		while ($myrow4 = db_fetch($result11))
		{
			alt_table_row_color($i);
			$rep->NewLine(1);

//			display_error($i);
//			$rep->NewLine(15);
//			$rep->MultiCell(190, 20, "Additional Costs", 0, 'L', 0, 2, 130, 650, true);
//			$rep->MultiCell(190, 20, "Date", 0, 'L', 0, 2, 45, 670, true);
//			$rep->MultiCell(190, 20, "Account Name", 0, 'L', 0, 2, 105, 670, true);
//			$rep->MultiCell(190, 20, "Amount", 0, 'L', 0, 2, 225, 670, true);

			$rep->TextCol(0, 1, $myrow4["id"], -2);
//			$rep->TextCol(1, 2, $wo_cost_types[$myrow4['stock_id']], -2);
//			$rep->NewLine(4.5);
			$rep->TextCol(1, 2, sql2date($myrow4["tran_date"]), -2);
//
			$rep->TextCol(3, 5, $myrow4['amount'], -2);
			$rep->TextCol(2, 3, get_gl_account_name($myrow4['account']), -2);
			end_row();
		end_table();
	}
		//en
		$memo = get_comments_string(ST_WORKORDER, $i);
		if ($memo != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $memo, -2);
		}

		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['contact'];
			$myrow['reference'] = $myrow['wo_ref'];
 			$rep->End($email);
		}
	}

	function display_wo_productions($woid)
	{
		global $path_to_root;


		if (db_num_rows($result) == 0)
		{
			display_note(_("There are no Productions for this Order."), 1, 1);
		}
		else
		{
			start_table(TABLESTYLE);
			$th = array(_("#"), _("Reference"), _("Date"), _("Quantity"));

			table_header($th);

			$k = 0; //row colour counter
			$total_qty = 0;

//			while ($myrow1 = db_fetch($result))
//			{
//
//				alt_table_row_color($k);
//
//				$total_qty += $myrow1['quantity'];
//				$rep->TextCol(0, 1,	$myrow1['id'], -2);
//				$rep->TextCol(1, 2, $myrow1['reference'], -2);
//
//				$rep->TextCol(2, 3,	$myrow1['date_'], -2);
//				qty_cell($myrow1['quantity'], false, get_qty_dec($myrow1['reference']));
//				$rep->MultiCell(190, 20, "hareem".$myrow1['reference'] ,0, 'C', 0, 2, 210,70, true);
//				end_row();
//			}//end of while

			label_row(_("Total"), number_format2($total_qty,user_qty_dec()),
				"colspan=3", "nowrap align=right");

			end_table();
		}

	}



	if ($email == 0)

	$rep->End();
}

