<?php

$page_security = 'SA_SUPPPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Payment Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_payment_report();

function getTransactions($supplier, $date)
{
	$date = date2sql($date);
	$dec = user_price_dec();

	$sql = "SELECT  supp_reference, tran_date, due_date, trans_no, type, rate,
			(ABS( ov_amount) + ABS( ov_gst) -  alloc) AS Balance,
			(ABS( ov_amount) + ABS( ov_gst) ) AS TranTotal
		FROM ".TB_PREF."supp_trans
		WHERE  supplier_id = '$supplier'
		AND ROUND(ABS( ov_amount),$dec) + ROUND(ABS( ov_gst),$dec) - 
		ROUND( alloc,$dec) != 0
		AND  tran_date <='$date'
		AND type NOT IN(".ST_SUPPCREDIT_IMPORT.")
		ORDER BY  type,
			 trans_no";

    return db_query($sql, "No transactions were returned");
}
function get_bank_account_($type, $trans_no)
{
	$sql = "SELECT *
	FROM ".TB_PREF."bank_trans
	where type = ".db_escape($type)."
	AND trans_no = ".db_escape($trans_no);

	$result = db_query($sql, "could not get customer");
	return db_fetch($result);
}
function get_bank_account_name($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($id);

	$result = db_query($sql, "could not retreive bank account");

	$row = db_fetch_row($result);

	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_payment_report()
{
	global $path_to_root, $systypes_array;

	$to = $_POST['PARAM_0'];
	$fromsupp = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$no_zeros = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	if ($fromsupp == ALL_TEXT)
		$from = _('All');
	else
		$from = get_supplier_name($fromsupp);

    	$dec = user_price_dec();

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 100, 130, 190,	250, 320, 385, 450,	515);

	$headers = array(_('Trans Type'), _('#'), _('Due Date'), '', 'Bank/Cash',
		'', _('Total'), _('Balance'));

	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
    			2 => array('text' => _('Supplier'), 'from' => $from, 'to' => ''),
    			3 => array(  'text' => _('Currency'),'from' => $currency, 'to' => ''),
				4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Payment Report'), "PaymentReport", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array();
	$grandtotal = array(0,0);

	$sql = "SELECT supplier_id, supp_name AS name, curr_code, ".TB_PREF."payment_terms.terms FROM ".TB_PREF."suppliers, ".TB_PREF."payment_terms
		WHERE ";
	if ($fromsupp != ALL_TEXT)
		$sql .= "supplier_id=".db_escape($fromsupp)." AND ";
	$sql .= "".TB_PREF."suppliers.payment_terms = ".TB_PREF."payment_terms.terms_indicator
		ORDER BY supp_name";
	$result = db_query($sql, "The customers could not be retrieved");

	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code']) continue;

		$res = getTransactions($myrow['supplier_id'], $to);
		if ($no_zeros && db_num_rows($res)==0) continue;

		$rep->fontSize += 2;
		$rep->TextCol(0, 6, $myrow['name'] . " - " . $myrow['terms']);
		if ($convert)
			$rep->TextCol(6, 7,	$myrow['curr_code']);
		$rep->fontSize -= 2;
		$rep->NewLine(1, 2);
		if (db_num_rows($res)==0)
			continue;
		$rep->Line($rep->row + 4);
		$total[0] = $total[1] = 0.0;
		while ($trans=db_fetch($res))
		{
			if ($no_zeros && $trans['TranTotal'] == 0 && $trans['Balance'] == 0) continue;

			if ($convert) $rate = $trans['rate'];
			else $rate = 1.0;

			$rep->NewLine(1, 2);
			$rep->TextCol(0, 1, $systypes_array[$trans['type']]);
			$rep->TextCol(1, 2,	$trans['supp_reference']);
			if ($trans['type'] == ST_SUPPINVOICE)
				$rep->DateCol(2, 3,	$trans['due_date'], true);
			else	
				$rep->DateCol(2, 3,	$trans['tran_date'], true);
			if ($trans['type'] != ST_SUPPINVOICE)
			{
				$trans['TranTotal'] = -$trans['TranTotal'];
				$trans['Balance'] = -$trans['Balance'];
			}
			$bank_act = get_bank_account_($trans['type'], $trans['trans_no']);
			$bank_name = get_bank_account_name($bank_act['bank_act']);
			$rep->TextCol(4, 5, $bank_name);
				if(!user_check_access('SA_SUPPPRICES')) {
			$item[0] = $trans['TranTotal'] * $rate;
			$rep->AmountCol(6, 7, $item[0], $dec);
			$item[1] = $trans['Balance'] * $rate;
			$rep->AmountCol(7, 8, $item[1], $dec);
				}
			for ($i = 0; $i < 2; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}
		}
		$rep->Line($rep->row - 8);
		$rep->NewLine(2);
		if(!user_check_access('SA_SUPPPRICES')) {
		$rep->TextCol(0, 3,	_('Total'));
		for ($i = 0; $i < 2; $i++)
		{
			$rep->AmountCol($i + 6, $i + 7, $total[$i], $dec);
			$total[$i] = 0.0;
		}
		}
    	$rep->Line($rep->row  - 4);
    	$rep->NewLine(2);
	}
	$rep->fontSize += 2;
	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->fontSize -= 2;
	for ($i = 0; $i < 2; $i++)
		$rep->AmountCol($i + 6, $i + 7,$grandtotal[$i], $dec);
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

