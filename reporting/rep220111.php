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

    $sql = "SELECT SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS charges,
    	SUM(IF(".TB_PREF."supp_trans.type <> ".ST_SUPPINVOICE." AND ".TB_PREF."supp_trans.type <> ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS credits,
		SUM(".TB_PREF."supp_trans.alloc) AS Allocated,
		SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.",
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount - ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh),
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh))) AS OutStanding
		FROM ".TB_PREF."supp_trans
    	WHERE ".TB_PREF."supp_trans.tran_date < '$to'
		AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id' GROUP BY supplier_id";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function getTransactions($supplier_id, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."supp_trans.*,".TB_PREF."comments.memo_,
				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh - 
				".TB_PREF."supp_trans.supply_disc) AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.") AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue
    			FROM ".TB_PREF."supp_trans
    			LEFT JOIN ".TB_PREF."voided ON ".TB_PREF."supp_trans.type=".TB_PREF."voided.type 
    			AND ".TB_PREF."supp_trans.trans_no=".TB_PREF."voided.id
    			LEFT JOIN ".TB_PREF."comments ON ".TB_PREF."supp_trans.type=".TB_PREF."comments.type 
    			AND ".TB_PREF."supp_trans.trans_no=".TB_PREF."comments.id
    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from' 
    			AND ".TB_PREF."supp_trans.tran_date <= '$to' 
    			AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id'
    			AND ISNULL(".TB_PREF."voided.id)
    			ORDER BY ".TB_PREF."supp_trans.tran_date";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}

function getTransactions2($supplier_id, $from, $to, $transno)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."supp_trans.*, ".TB_PREF."supp_invoice_items.*,
				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount)
				AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.")
					AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue

    			FROM ".TB_PREF."supp_trans, ".TB_PREF."supp_invoice_items

    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from' 
			AND ".TB_PREF."supp_trans.tran_date <= '$to' 
    			AND ".TB_PREF."supp_trans.supplier_id = ".db_escape($supplier_id)."

		AND ".TB_PREF."supp_invoice_items.supp_trans_type  =  ".TB_PREF."supp_trans.type
		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".TB_PREF."supp_trans.trans_no 
		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".db_escape($transno)."


    				ORDER BY ".TB_PREF."supp_trans.tran_date";

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
    	$currency = $_POST['PARAM_3'];
    	$no_zeros = $_POST['PARAM_4'];
    	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];
	$destination = $_POST['PARAM_7'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if ($fromsupp == ALL_TEXT)
		$supp = _('All');
	else
		$supp = get_supplier_name($fromsupp);
    	$dec = user_price_dec();

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('PKR');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 70, 140, 272,  335, 380, 450);

	$headers = array(_('Date'), _('Voucher No'), _('Description'), _('Chq No'),_('Debit'), _('Credit'), _('Balance'));

	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
    			2 => array('text' => _('Supplier'), 'from' => $supp, 'to' => ''),
    			3 => array(  'text' => _('Currency'),'from' => $currency, 'to' => ''),
			4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Supplier Ledger'), "SupplierBalancesDetailed", user_pagesize());

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
		$bal = get_open_balance($myrow['supplier_id'], $from, $convert);
		$init[0] = $init[1] = 0.0;
		$init[0] = round2(abs($bal['charges']), $dec);
		$init[1] = round2(Abs($bal['credits']), $dec);

			$init[2] =  $init[1]-$init[0];
			$accumulate += $init[2];

		$total = array(0,0,0,0);
		for ($i = 0; $i < 3; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		$res = getTransactions($myrow['supplier_id'], $from, $to);
		if ($no_zeros && db_num_rows($res) == 0) continue;

		$rep->fontSize += 2;
		$rep->Font('bold');			
		$rep->TextCol(0, 2, $myrow['name']);
		$rep->Font('');			
//		if ($convert) $rep->TextCol(2, 3,	$myrow['curr_code']);
		$rep->fontSize -= 2;
//		$rep->TextCol(3, 4,	_("Open Balance"));
		$rep->AmountCol(4, 5, $init[1], $dec);
//		$rep->AmountCol(8, 9, $init[0], $dec);

//		$rep->AmountCol(9, 10, $init[2], $dec);
		$rep->NewLine(1, 2);
		if (db_num_rows($res)==0) continue;

		$rep->Line($rep->row + 9);
$QuantityTotal = 0;				
		while ($trans=db_fetch($res))
		{
			if ($no_zeros && floatcmp(abs($trans['TotalAmount'])) == 0) continue;
//			$rep->NewLine();

			$rep->Font('bold');
			$rep->DateCol(0, 1,	$trans['tran_date'], true);
			$rep->TextCol(1, 2,	$trans['reference']);
			$rep->Font();
			//$rep->DateCol(1, 2,	$trans['tran_date'], true);

		//	if ($trans['type'] == ST_SUPPINVOICE)
		//		$rep->DateCol(3, 4,	$trans['due_date'], true);
			$item[0] = $item[1] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
			if ($trans['TotalAmount'] >= 0.0)
			{
//				$rep->TextCol(2, 4, $systypes_array[$trans['type']]);
//				$rep->TextCol(2, 3, $trans['memo_']);
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->Font('bold');					
				$rep->AmountCol(5, 6, $item[0], $dec);
				$rep->TextCol(3, 4,$trans['cheque_no'], $dec);
				$accumulate -= $item[0];
				$rep->Font();

//				$rep->Line($rep->row - 2);

			}
			else
			{
				$item[1] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->TextCol(3, 4,$trans['cheque_no'], $dec);
				$rep->AmountCol(4, 5, $item[1], $dec);
				$accumulate += $item[1];
			}
		//	$item[2] = round2($trans['Allocated'] * $rate, $dec);
		//	$rep->AmountCol(6, 7, $item[2], $dec);



			/*
			if ($trans['type'] == 20)
				$item[3] = ($trans['TotalAmount'] - $trans['Allocated']) * $rate;
			else
				$item[3] = ($trans['TotalAmount'] + $trans['Allocated']) * $rate;
			*/	
			if ($trans['type'] == ST_SUPPINVOICE || $trans['type'] == ST_BANKDEPOSIT)
				$item[2] = $item[1]-$item[0];
			else	
				$item[2] = $item[1]-$item[0];

				$total[2] = $total[1]-$total[0];

//			$rep->AmountCol(8, 9, $item[3], $dec);
				$rep->AmountCol(6, 7, $accumulate, $dec);
            $rep->TextColLines(2, 3, $trans['memo_']);

			for ($i = 0; $i < 3; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}

				$total[2] = $total[1]-$total[0];

			$rep->Font('bold');	
			$rep->Font();						

		}
        $rep->Line($rep->row  + 10);
//		$rep->NewLine(2);
		$rep->Font('bold');	
		$rep->TextCol(0, 3,	_('Total'));
//		for ($i = 0; $i < 4; $i++)
		{
		//	$rep->AmountCol($i + 6, $i + 7, $total[$i], $dec);
			$rep->AmountCol(4, 5, $total[1], $dec);
			$rep->AmountCol(5, 6, $total[0], $dec);
			$rep->AmountCol(6, 7, $total[2], $dec);
//			$total[$i] = 0.0;
//			$rep->AmountCol( 4, 5, $QuantityTotal	, $dec);
		}
		$rep->Font();					
    		$rep->Line($rep->row  - 4);
    		$rep->NewLine(2);
        if ($rep->row < $rep->bottomMargin + (5 * $rep->lineHeight))
            $rep->NewPage();
	}
	$rep->fontSize += 2;
	$rep->Font('bold');		
//	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->fontSize -= 2;
//	for ($i = 0; $i < 4; $i++)
//		$rep->AmountCol($i + 7, $i + 8,$grandtotal[$i], $dec);

//			$rep->AmountCol(8, 9, $grandtotal[0], $dec);
//			$rep->AmountCol(7, 8, $grandtotal[1], $dec);
//			$rep->AmountCol(9, 10, $grandtotal[2], $dec);
//
//		$rep->AmountCol( 4, 5, $QuantityGrandTotal, $dec);
	$rep->Font();	
//	$rep->Line($rep->row  - 4);
//	$rep->NewLine();
    $rep->End();
}

?>