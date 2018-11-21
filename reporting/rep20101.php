<?php

$page_security = 'SA_SUPPLIERANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Supplier Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_supplier_balances();

function get_open_balance($supplier_id, $to)
{
	$to = date2sql($to);

    $sql = "SELECT
    	SUM(IF(type IN(".ST_SUPPINVOICE.",".ST_BANKDEPOSIT.",".ST_CRV.") OR (type = ".ST_JOURNAL." AND ov_amount>0), (ov_amount + ov_gst + ov_discount-(supply_disc + service_disc + fbr_disc +  srb_disc)), 0)) AS charges,
    	SUM(IF(type NOT IN(".ST_SUPPINVOICE.",".ST_BANKDEPOSIT.",".ST_CRV.")AND NOT(type = ".ST_JOURNAL." AND ov_amount>0), (ov_amount - ov_gst + ov_discount-(supply_disc + service_disc + fbr_disc +  srb_disc)), 0)) AS credits,
		SUM(IF(type NOT IN(".ST_SUPPINVOICE.",".ST_BANKDEPOSIT.",".ST_CRV.") AND NOT(type = ".ST_JOURNAL." AND ov_amount>0) ,alloc * -1, alloc)) AS Allocated,
		SUM(IF(type IN(".ST_SUPPINVOICE.",".ST_BANKDEPOSIT.",".ST_CRV."), (ov_amount + ov_gst + ov_discount-alloc-(supply_disc + service_disc + fbr_disc +  srb_disc)),
				(ov_amount + ov_gst + ov_discount+ alloc-(supply_disc + service_disc + fbr_disc +  srb_disc)))) AS OutStanding
		FROM ".TB_PREF."supp_trans
    	WHERE tran_date < '$to'
		AND supplier_id = '$supplier_id'
		AND type != ".ST_SUPPCREDIT_IMPORT." GROUP BY supplier_id";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function getTransactions($supplier_id, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT *,
				(ov_amount + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc +  srb_disc) ) AS TotalAmount,
				alloc AS Allocated,
				((type = ".ST_SUPPINVOICE.") AND due_date < '$to') AS OverDue
   			FROM ".TB_PREF."supp_trans
   			WHERE tran_date >= '$from' AND tran_date <= '$to' 
    			AND supplier_id = '$supplier_id'   
    			AND type != ".ST_SUPPCREDIT_IMPORT." AND ov_amount!=0
    				ORDER BY tran_date";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}

//----------------------------------------------------------------------------------------------------

function print_supplier_balances()
{
	global $path_to_root, $systypes_array;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$fromsupp = $_POST['PARAM_2'];
	$show_balance = $_POST['PARAM_3'];
	$currency = $_POST['PARAM_4'];
	$no_zeros = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];
	$destination = $_POST['PARAM_8'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	if ($fromsupp == ALL_TEXT)
		$supp = _('All');
	else
		$supp = get_supplier_name($fromsupp);
    	$dec = user_price_dec();

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home currency');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(4, 60, 120, 270, 355, 440, 520);

	$headers = array(_('Date'), _('Num'), _('Memo'), _('Debit'), _('Credit'), _('Balance'));

	$aligns = array('left',	'left',	'left',	'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			2 => array('text' => _('Supplier'), 'from' => $supp, 'to' => ''),
    			3 => array(  'text' => _('Currency'),'from' => $currency, 'to' => ''),
				4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Supplier Balances'), "SupplierBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array();
	$grandtotal = array(0,0,0,0);

	$sql = "SELECT supplier_id, supp_name AS name, curr_code FROM ".TB_PREF."suppliers";
	if ($fromsupp != ALL_TEXT)
		$sql .= " WHERE supplier_id=".db_escape($fromsupp);
	$sql .= " ORDER BY supp_name";
	$result = db_query($sql, "The customers could not be retrieved");

	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code'])
			continue;
		$accumulate = 0;
		$rate = $convert ? get_exchange_rate_from_home_currency($myrow['curr_code'], Today()) : 1;
		$bal = get_open_balance($myrow['supplier_id'], $from);
		$init[0] = $init[1] = 0.0;
		$init[0] = round2(abs($bal['charges']*$rate), $dec);
		$init[1] = round2(Abs($bal['credits']*$rate), $dec);
		$init[2] = round2($bal['Allocated']*$rate, $dec);
		if ($show_balance)
		{
			$init[3] = $init[0] - $init[1];
			$accumulate += $init[3];
		}	
		else	
			$init[3] = round2($bal['OutStanding']*$rate, $dec);
		$res = getTransactions($myrow['supplier_id'], $from, $to);
		if ($no_zeros && db_num_rows($res) == 0) continue;

		$rep->fontSize += 2;
		$rep->TextCol(0, 2, $myrow['name']);
		if ($convert) $rep->TextCol(2, 3,	$myrow['curr_code']);
		$rep->fontSize -= 2;
		$rep->TextCol(2, 4,	"                              "._("Open Balance"));
		$rep->AmountCol(3, 4, $init[0], $dec);
		$rep->AmountCol(4, 5, $init[1], $dec);
		$rep->AmountCol(5, 6, $init[3], $dec);
		$total = array(0,0,0,0);
		for ($i = 0; $i < 4; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		$rep->NewLine(1, 2);
		$rep->Line($rep->row + 4);
		if (db_num_rows($res)==0) {
			$rep->NewLine(1, 2);
			continue;
		}	
		while ($trans=db_fetch($res))
		{
		   
			if ($no_zeros && floatcmp(abs($trans['TotalAmount'])) == 0) continue;			
			$rep->NewLine(1, 2);
			$rep->TextCol(1, 2,	$trans['reference']);
			$rep->DateCol(0, 1,	$trans['tran_date'], true);
			if ($trans['type'] == ST_SUPPINVOICE)
				$rep->DateCol($trans['due_date'], true);
			$item[0] = $item[1] = 0.0;
			if ($trans['TotalAmount'] < 0.0)
			{
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->AmountCol(3, 4, $item[0], $dec);
				$accumulate += $item[0];
			}
			else
			{
				$item[1] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->AmountCol(4, 5, $item[1], $dec);
				$accumulate -= $item[1];
			}
			$item[2] = round2($trans['Allocated'] * $rate, $dec);
			if ($trans['TotalAmount'] > 0.0)
				$item[3] = $item[0] - $item[2];
			else	
				$item[3] = ($item[1] - $item[2]) * -1;
			if ($show_balance)
				$rep->AmountCol(5, 6, $accumulate, $dec);
			else
				$rep->AmountCol(5, 6, $item[3], $dec);
				
			$memo = get_comments_string($trans['type'], $trans['trans_no']);
			if ($memo != "")
			{
				$rep->TextColLines(2, 3, "Memo : ".$memo, -2);
			}
			
			for ($i = 0; $i < 4; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}
			if ($show_balance)
				$total[3] = $total[0] - $total[1];
				
// 			if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
// 				{
		$rep->LineTo($rep->leftMargin, 57.4* $rep->lineHeight ,$rep->leftMargin, $rep->row-2);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,57.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row-2);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 82, 57.4* $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 82, $rep->row-2);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 166, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 166, $rep->row-2);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 256, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 256, $rep->row-2);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 407, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 407, $rep->row-2);
        $rep->Line($rep->row-2);	
				
// 		$rep->NewPage();
// 				}
		}
		
		$rep->LineTo($rep->leftMargin, 57.4* $rep->lineHeight ,$rep->leftMargin, $rep->row-52);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,57.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row-52);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 82, 57.4* $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 82, $rep->row-52);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 166, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 166, $rep->row-52);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 256, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 256, $rep->row-52);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 407, 57.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 407, $rep->row-52);

		$rep->NewLine(2);
		$rep->Font('bold');
		$rep->TextCol(0, 3,	_('Total'));

		$rep->AmountCol(3,  4, $total[0], $dec);
        $rep->AmountCol(4,  5, $total[1], $dec);
        $rep->AmountCol(5,  6, $total[3], $dec);
        $rep->Font('');
        $total[0] = 0.0;
        $total[1] = 0.0;
        $total[3] = 0.0;
    	$rep->Line($rep->row  - 4);
    	$rep->NewLine(2);
	}
	$rep->fontSize += 2;
	$rep->Font('bold');
	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->fontSize -= 2;
	if ($show_balance)
		$grandtotal[3] = $grandtotal[0] - $grandtotal[1];
    $rep->AmountCol(3,  4, $grandtotal[0], $dec);
    $rep->AmountCol(4,  5, $grandtotal[1], $dec);
    $rep->AmountCol(5,  6, $grandtotal[3], $dec);
    $rep->Font('');
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
	
	
    $rep->End();
}

