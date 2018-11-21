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
function get_user_name_4090($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time4090($trans_no,$type)
{
    $sql= "SELECT user, UNIX_TIMESTAMP(stamp) as unix_stamp
    FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}

print_workorders();

//----------------------------------------------------------------------------------------------------

function print_workorders()
{
	global $path_to_root, $SysPrefs, $dflt_lang;

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

	$cols = array(4, 60, 190, 235, 320, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('WORK ORDER'), "WorkOrderBulk", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_work_order($i);
	    $user =get_user_id_time4090($myrow['id'],ST_WORKORDER);

		if ($myrow === false)
			continue;
		$date_ = sql2date($myrow["date_"]);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep->title = _('WORK ORDER');
			$rep->filename = "WorkOrder" . $myrow['wo_ref'] . ".pdf";
		}
		$rep->SetHeaderType('Header4090');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contact = array('email' =>$myrow['email'],'lang' => $dflt_lang, // ???
			'name' => $myrow['contact'], 'name2' => '', 'contact');

		$rep->SetCommonData($myrow, null, null, '', ST_WORKORDER, $contact);
		$rep->NewPage();

		$result = get_wo_requirements($i);
		//$rep->TextCol(0, 5,_("Work Order Requirements"), -2);
// 		$rep->NewLine(2);
		$has_marked = false;
		while ($myrow2=db_fetch($result))
		{
			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow2["mb_flag"]))
				$show_qoh = false;

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow2["stock_id"], $myrow2["loc_code"], $date_);

			if ($show_qoh && ($myrow2["units_req"] * $myrow["units_issued"] > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				$has_marked = true;
			}
			else
				$has_marked = false;
			if ($has_marked)
				$str = $myrow2['stock_id']." ***";
			else
				$str = $myrow2['stock_id'];
			$rep->TextCol(0, 1,	$str, -2);
			$rep->TextCol(1, 2, $myrow2['description'], -2);

			//$rep->TextCol(2, 3,	$myrow2['location_name'], -2);
            $rep->TextCol(2, 3,	$myrow2['units'], -2);
			//$rep->TextCol(3, 4,	$myrow2['WorkCentreDescription'], -2);
			$dec = get_qty_dec($myrow2["stock_id"]);

            $qty += $myrow2['units_req'];
            $tot_qty_cons += $myrow2['units_req'] * $myrow['units_issued'];
			$rep->AmountCol(3, 4,	$myrow2['units_req'], $dec, -2);
			$rep->AmountCol(4, 5,	$myrow2['units_req'] * $myrow['units_issued'], $dec, -2);
			$rep->AmountCol(5, 6,	$myrow2['units_req'] * $myrow2['ComponentCost'], $dec, -2);
			$amt += $myrow2['units_req'] * $myrow2['ComponentCost'];
           $qoh=get_qoh_on_date($myrow2["stock_id"], null, null, 0, 0);
            $rep->AmountCol(6, 7,	$qoh, $dec, -2);
            $tot_qoh += $qoh;
			$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
		$rep->NewLine(1);
		//$rep->TextCol(0, 5," *** = "._("Insufficient stock"), -2);

		$memo = get_comments_string(ST_WORKORDER, $i);
		if ($memo != "")
		{
// 			$rep->NewLine();
// 			$rep->TextColLines(1, 5, $memo, -2);
		}
		$rep->font('b');
		$rep->MultiCell(300, 20, "Customer Name:" ,0, 'L', 0, 2, 40,230, true);
		$rep->font('');
$rep->MultiCell(300, 20, $memo ,0, 'L', 0, 2, 50,245, true);
		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['contact'];
			$myrow['reference'] = $myrow['wo_ref'];
 			$rep->End($email);
		}
	}
	    $rep->multicell(50,22,"Total",0,'R',0,1,235,694);

    $rep->multicell(50,22,number_format($qty,2),1,'R',0,1,290,694);
    $rep->multicell(100,22,number_format($tot_qty_cons,2),1,'R',0,1,340,694);
    $rep->multicell(60,22,number_format($amt,2),1,'R',0,1,440,694);

//  $array =explode(' ', $user['stamp']);
 
//  if (user_date_format() == 0)
    $rep->MultiCell(225, 60,get_user_name_4090($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp']))/*." ".  date("H:i:s", $user['unix_stamp'])*/, 0, 'L', 0, 2, 45,770, true);
// else
//     $rep->MultiCell(225, 60,get_user_name_4090($user['user'])." ". sql2date(date("Y-m-d", $user['unix_stamp']))." ". date("h:i:s a", $user['unix_stamp']), 0, 'L', 0, 2, 45,770, true);



    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 45,780, true);
   $rep->MultiCell(225, 60, "Prepared by"  , 0, 'L', 0, 2, 100,800, true);
 
    $rep->MultiCell(225, 60, "_______________________________" , 0, 'L', 0, 2, 410,780, true);
    $rep->MultiCell(225, 60, "Approved by"  , 0, 'L', 0, 2, 460,800, true);



    if ($email == 0)
		$rep->End();
}

?>