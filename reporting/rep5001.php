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
function get_user_name_5000($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time5000($trans_no,$type)
{
    $sql= "SELECT user,  UNIX_TIMESTAMP(stamp) as unix_stamp
    FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}
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
function get_sale_order_ref($order_no)
{
	$sql = "SELECT reference FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($order_no) ."";
	$query =  db_query($sql, "The work order issues could not be retrieved");
	$fetch = db_fetch_row ($query);
	return $fetch [0];
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

    $cols = array(4, 60, 240, 305, 350, 435,  515);

	// $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'right', 'right',  'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('WORK ORDER REQUISITION'), "WorkOrderBulk", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++) {
		$myrow = get_work_order_requisition_report($i);
		$user = get_user_id_time5000($myrow['id'],ST_MANUORDERREQ);

		if ($myrow === false)
			continue;
		if ($email == 1) {
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep->title = _('WORK ORDER REQUISITION');
			$rep->filename = "WorkOrderRequisition" . $myrow['wo_ref'] . ".pdf";
		}
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contact = array('email' => $myrow['email'], 'lang' => $dflt_lang, // ???
			'name' => $myrow['contact'], 'name2' => '', 'contact');

		$rep->SetCommonData($myrow, null, null, '', ST_MANUORDERREQ, $contact);
		$rep->SetHeaderType('Header5001');
		$rep->NewPage();

		$result = get_work_order_requisition_details($i);
//		$result = get_bom($myrow['stock_id']);
//		$rep->TextCol(0, 5, _("Work Order"), -2);
//		$rep->NewLine(2);
		while ($myrow2 = db_fetch($result)) {
            $qoh = 0;
            $show_qoh = true;
            // if it's a non-stock item (eg. service) don't show qoh
//            if (!has_stock_holding($myrow2["mb_flag"]))
//                $show_qoh = false;
//
//            if ($show_qoh)
//                $qoh = get_qoh_on_date($myrow2["stock_id"], $myrow2["loc_code"], null);
//
//            if ($show_qoh && ($myrow2["units_req"] * $myrow["units_issued"] > $qoh))
//            {
//                // oops, we don't have enough of one of the component items
//                $has_marked = true;
//            }
//            else
//                $has_marked = false;
//            if ($has_marked)
//                $str = $myrow2['stock_id']." ***";
//            else
                $str = $myrow2['stock_id'];
            $rep->TextCol(0, 1,	$str, -2);
            $rep->TextCol(1, 2, $myrow2['description'], -2);

            //$rep->TextCol(2, 3,	$myrow2['location_name'], -2);
            $rep->TextCol(2, 3,	$myrow2['units'], -2);
            //$rep->TextCol(3, 4,	$myrow2['WorkCentreDescription'], -2);
            $dec = get_qty_dec($myrow2["stock_id"]);

            $qty += $myrow2['units_req'];
            $tot_qty_cons += $myrow2['units_req'] * $myrow['units_reqd'];
            $rep->AmountCol(3, 4,	$myrow2['units_req'], $dec, -2);
            $rep->AmountCol(4, 5,	$myrow2['units_req'] * $myrow['units_reqd'], $dec, -2);
            $rep->AmountCol(5, 6,	$myrow2['units_req'] * $myrow2['ComponentCost'], $dec, -2);
            $amt += $myrow2['units_req'] * $myrow2['ComponentCost'];
//            $qoh=get_qoh_on_date($myrow2["stock_id"], null, null, 0, 0);
//            $rep->AmountCol(6, 7,	$qoh, $dec, -2);
//            $tot_qoh += $qoh;
            $rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
		}
        $rep->NewLine(1);
		$memo = get_comments_string(ST_WORKORDER, $i);
//		if ($memo != "")
//		{
// 			$rep->NewLine(-18);
// 			$rep->TextColLines(0, 5, $memo, -2);
//		}
        $rep->MultiCell(300, 20, $memo ,0, 'L', 0, 2, 50,220, true);
		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['contact'];
			$myrow['reference'] = $myrow['wo_ref'];
 			$rep->End($email);
		}
	}

    $rep->multicell(525,12,"",1,'L',0,1,40,716);
    $rep->multicell(50,12,"Total",0,'R',0,1,290,716);
//
    $rep->multicell(50,6,number_format($qty,2),1,'R',0,1,340,716);
    $rep->multicell(90,6,number_format($tot_qty_cons,2),1,'R',0,1,390,716);
    $rep->multicell(85,6,number_format($amt,2),1,'R',0,1,480,716);
//  $array =explode(' ', $user['stamp']);

    // $rep->MultiCell(225, 60,get_user_name_5000($user['user'])." ".sql2date($array[0]) ." ". $array[1], 0, 'L', 0, 2, 45,770, true);
    
    $rep->MultiCell(225, 60,get_user_name_5000($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp']))/*." ".  date("H:i:s", $user['unix_stamp'])*/, 0, 'L', 0, 2, 45,770, true);

    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 45,780, true);
    $rep->MultiCell(225, 60, "Prepared by"  , 0, 'L', 0, 2, 100,800, true);
 
    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 410,780, true);
    $rep->MultiCell(225, 60, "Approved by"  , 0, 'L', 0, 2, 460,800, true);

	if ($email == 0)

	$rep->End();
}

