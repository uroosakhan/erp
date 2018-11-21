<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");
include_once($path_to_root . "/admin/db/tags_db.inc");
//----------------------------------------------------------------------------------------------------

function display_type($type, $typename, $from, $to, $begin, $end, $compare, $convert, &$dec, &$pdec, &$rep, $dimension, $dimension2,
					   $tags, &$pg, $graphics)
{
	$code_per_balance = 0;
	$code_acc_balance = 0;
	$per_balance_total = 0;
	$acc_balance_total = 0;
	$totals_arr = array();

	$printtitle = 0; //Flag for printing type name

	//Get Accounts directly under this group/type
	$result = get_gl_accounts(null, null, $type);
	while ($account = db_fetch($result))
	{
		if ($tags != -1 && is_array($tags) && $tags[0] != false)
		{
			if (!is_record_in_tags($tags, TAG_ACCOUNT, $account['account_code']))
				continue;
		}
		$per_balance = get_gl_trans_from_to($from, $to, $account["account_code"], $dimension, $dimension2);

		if ($compare == 2)
			$acc_balance = get_budget_trans_from_to($begin, $end, $account["account_code"], $dimension, $dimension2);
		else
			$acc_balance = get_gl_trans_from_to($begin, $end, $account["account_code"], $dimension, $dimension2);
		if (!$per_balance && !$acc_balance)
			continue;

		//Print Type Title if it has atleast one non-zero account
		if (!$printtitle)
		{
			$printtitle = 1;
			$rep->row -= 4;
			$rep->TextCol(0, 5, $typename);
			$rep->row -= 4;
			$rep->Line($rep->row);
			$rep->NewLine();
		}

		$rep->TextCol(0, 1,	$account['account_code']);
		$rep->TextCol(1, 3,	$account['account_name']);

		$rep->AmountCol(6, 7, $per_balance * $convert, $dec);
		$rep->AmountCol(8, 10, $acc_balance * $convert, $dec);
		$rep->AmountCol(10, 11, Achieve($per_balance, $acc_balance), $pdec);

		$rep->NewLine();

		if ($rep->row < $rep->bottomMargin + 3 * $rep->lineHeight)
		{
			$rep->Line($rep->row - 2);
			$rep->NewPage();
		}

		$code_per_balance += $per_balance;
		$code_acc_balance += $acc_balance;
	}
//
//	//Get Account groups/types under this group/type
	$result = get_account_types(false, false, $type);
	while ($accounttype=db_fetch($result))
	{
//		//Print Type Title if has sub types and not previously printed
		if (!$printtitle)
		{
			$printtitle = 1;
			$rep->row -= 4;
			$rep->TextCol(0, 5, $typename);
			$rep->row -= 4;
			$rep->Line($rep->row);
			$rep->NewLine();
		}

		$totals_arr = display_type($accounttype["id"], $accounttype["name"], $from, $to, $begin, $end, $compare, $convert, $dec,
			$pdec, $rep, $dimension, $dimension2, $tags, $pg, $graphics);
		$per_balance_total += $totals_arr[0];
		$acc_balance_total += $totals_arr[1];
	}

//	Display Type Summary if total is != 0 OR head is printed (Needed in case of unused hierarchical COA)
	if (($code_per_balance + $per_balance_total + $code_acc_balance + $acc_balance_total) != 0 || $printtitle)
	{
		$rep->row += 6;
		$rep->Line($rep->row);
		$rep->NewLine();
		$rep->TextCol(0, 3,	_('Total') . " " . $typename);
		$rep->AmountCol(6, 7, ($code_per_balance + $per_balance_total) * $convert, $dec);
		$rep->AmountCol(8, 10, ($code_acc_balance + $acc_balance_total) * $convert, $dec);
		$rep->AmountCol(10, 11, Achieve(($code_per_balance + $per_balance_total), ($code_acc_balance + $acc_balance_total)), $pdec);
		if ($graphics)
		{
			$pg->x[] = $typename;
			$pg->y[] = abs($code_per_balance + $per_balance_total);
			$pg->z[] = abs($code_acc_balance + $acc_balance_total);
		}
		$rep->NewLine();
	}

	$totals_arr[0] = $code_per_balance + $per_balance_total;
	$totals_arr[1] = $code_acc_balance + $acc_balance_total;
	return $totals_arr;
}

// trial_inquiry_controls();
print_customer_balances();


function Achieve($d1, $d2)
{
	if ($d1 == 0 && $d2 == 0)
		return 0;
	elseif ($d2 == 0)
		return 999;
	$ret = ($d1 / $d2 * 100.0);
	if ($ret > 999)
		$ret = 999;
	return $ret;
}

function get_bank_gl_account_name($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE account_code=".db_escape($id);

	$result = db_query($sql, "could not retreive bank account for $id");

	$bank_account = db_fetch($result);

	return $bank_account['bank_account_name'];
}

function get_gl_trans_code($type,$trans_no )
{
	$sql = "SELECT account FROM ".TB_PREF."gl_trans WHERE  type="
		.db_escape($type)."AND type_no=$trans_no";

	$result = db_query($sql, "query for gl trans value");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_bank_account_name2($id)
{
	$sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($id);

	$result = db_query($sql,"could not retreive bank account");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_bank_trans_for_bank_account_new( $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT SUM(t.amount) As payment, t.* FROM "
		.TB_PREF."bank_trans t LEFT JOIN ".TB_PREF."voided v ON t.type=v.type AND t.trans_no=v.id
		WHERE ISNULL(v.date_)
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		
		GROUP BY t.bank_act DESC";

	return db_query($sql,"The transactions for '' could not be retrieved");
}
// ==========================================

function get_class_name_for_report()
{
	$sql = "SELECT * FROM 0_chart_class WHERE !inactive AND ctype>3 OR ctype=0 ORDER BY ctype, cid";
	return db_query($sql, "Error");
}
function get_name_for_report($class_id)
{
	$sql = "SELECT * FROM 0_chart_types WHERE !inactive AND class_id='$class_id' AND (parent = '' OR parent = '-1') ORDER BY class_id, id, parent";
	return db_query($sql, "Error");
}
function get_account_code_for_report($account_type)
{
	$sql = "SELECT 0_chart_master.*,0_chart_types.name AS AccountTypeName FROM 0_chart_master,0_chart_types WHERE 0_chart_master.account_type=0_chart_types.id AND account_type='$account_type' ORDER BY account_code";
	return db_query($sql, "Error");
}
function get_account_code_amount_for_report($account_code, $from, $to)
{
	$from_date = date2sql($from);
	$to_date = date2sql($to);

	$sql = "SELECT SUM(amount) 
			FROM 0_gl_trans 
			WHERE account='$account_code' 
			AND tran_date >= '$from_date' 
			AND tran_date <= '$to_date'";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
	return $fetch[0];
}
// ==========================================
function get_open_balance($debtorno, $to)
{
	if($to)
		$to = date2sql($to);

	$sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE." AND t.type <> ".ST_BANKPAYMENT.",
	    	(t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1, 0)) AS credits,
		SUM(t.alloc) AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc),
	    	((t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1 + t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
	if ($to)
		$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

	$result = db_query($sql,"No transactions were returned");
	return db_fetch($result);
}

function get_transactions($debtorno, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY."
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";

	return db_query($sql,"No transactions were returned");
}


function get_transactions2($debtorno, $from, $to, $transno)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = "SELECT ".TB_PREF."debtor_trans.*, ".TB_PREF."debtor_trans_details.*
		
    	FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtor_trans_details
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans_details.debtor_trans_type  =  ".TB_PREF."debtor_trans.type
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no 
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".db_escape($transno)."
	AND ".TB_PREF."debtor_trans_details.debtor_trans_type = 10	
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";

	return db_query($sql,"No transactions were returned");
}


//---------------------------------------------------------------------------------------------------------

function print_customer_balances()
{
	global $path_to_root, $systypes_array;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$fromcust = $_POST['PARAM_2'];
	$area = $_POST['PARAM_3'];
	$folk = $_POST['PARAM_4'];
	$currency = $_POST['PARAM_5'];
	$no_zeros = $_POST['PARAM_6'];
	$comments = $_POST['PARAM_7'];
	$compare = $_POST['PARAM_8'];
	$tags = $_POST['PARAM_9'];
	$graphics = $_POST['PARAM_10'];
	$orientation= $_POST['PARAM_11'];
	$destination = $_POST['PARAM_12'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');

	if ($fromcust == ALL_TEXT)
		$cust = _('All');
	else
		$cust = get_customer_name($fromcust);
	$dec = user_price_dec();

	if ($area == ALL_NUMERIC)
		$area = 0;

	if ($area == 0)
		$sarea = _('All Areas');
	else
		$sarea = get_area_name($area);

	if ($folk == ALL_NUMERIC)
		$folk = 0;

	if ($folk == 0)
		$salesfolk = _('All Sales Man');
	else
		$salesfolk = get_salesman_name($folk);

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 50, 120, 160,  200, 250, 280, 335, 380, 420, 460, 460,400);

	$headers = array(_('Name'), _(''), _('Reference'), _('Product'), _(''), _(''),_(''), _('Size'),
		_('Qty'), _('Price'),_('Bank Name'));

	//	$headers[7] = _('Balance');


	$aligns = array('left',	'left',	'center',	'left',	'left', 'right', 'right', 'right', 'right', 'right','right');
//
	$params =   array( 	0 => $comments,
		1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
		2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
		3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),
		4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
		5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
		6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));


	$rep = new FrontReport(_('Customer Balances - Detailed'), "CustomerBalancesDetailed", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);
	$rep->NewPage();

	$grandtotal = array(0,0,0,0);
	$deb_sum_total_grandtotal = array(0,0,0,0);
	$a = 0;


	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
			".TB_PREF."debtors_master.name AS Name
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

	if ($fromcust != ALL_TEXT )
	{
		if ($area != 0 || $folk != 0) continue1;
		$sql .= " WHERE ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
	}

	elseif ($area != 0)
	{
		if ($folk != 0)
			$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code=".db_escape($area);
		else
			$sql .= " WHERE ".TB_PREF."areas.area_code=".db_escape($area);
	}
	elseif ($folk != 0 )
	{
		$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);
	}


	//$sql .= " ORDER BY Name";
	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;


	while ($myrow = db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code']) continue;

		$deb_sum_total = array(0,0,0,0);
		$deb_sum = array(0,0,0,0);

		$bal = get_open_balance($myrow['DebtorNo'], $from, $convert);
		$init[0] = $init[1] = 0.0;
		$init[0] = round2(abs($bal['charges']), $dec);
		$init[1] = round2(Abs($bal['credits']), $dec);

		{
			$init[2] = $init[0] - $init[1];
			$accumulate += $init[2];
		}


		$res = get_transactions($myrow['DebtorNo'], $from, $to);
		if ($no_zeros && db_num_rows($res) == 0) continue;

		$num_lines++;
		//$rep->fontSize += 2;
		//$rep->Font('bold');
		//$rep->TextCol(1, 3, $myrow['Name']);
		//$rep->Font();


		//$rep->fontSize -= 2;

		$total = array(0,0,0,0);
		for ($i = 0; $i < 4; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		//$rep->NewLine(1, 2);
		if (db_num_rows($res)==0)
			continue;
		//$rep->Line($rep->row + 4);
		while ($trans = db_fetch($res))
		{
//			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
			if ($no_zeros && floatcmp($trans['TotalAmount']) == 0) continue;
			//$rep->NewLine(1, 2);


			//$rep->Font('bold');
			//$rep->TextCol(0, 1,	$trans['reference']);
			//$rep->Font();
			$bank_code=get_gl_trans_code($trans['type'],$trans['trans_no']);
			$result2=get_bank_trans_for_bank_account_new($from,$to);
			//$rep->NewLine(+10);

			//$rep->NewLine(-10);
			
			$rep->TextCol(10, 11, get_bank_gl_account_name($bank_code));

			//if ($trans['type'] == ST_SALESINVOICE)
				//$rep->DateCol(3, 4,	$trans['due_date'], true);
			$item[0] = $item[1] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
			if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT)
				$trans['TotalAmount'] *= -1;



			if ($trans['TotalAmount'] > 0.0)
			{
				$foo = true;
				$a = 1;
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				//$rep->TextCol(1, 3, $trans['reference']);
				$rep->Font('bold');
				//$rep->AmountCol(7, 8, $item[0], $dec);
				$rep->Font();

				$res2 = get_transactions2($myrow['DebtorNo'], $from, $to, $trans['trans_no']);
				 while ($trans2 = db_fetch($res2))

				{
					$rep->NewLine();
//$rep->DateCol(0, 1,	$trans['tran_date'], true);
                                       $rep->TextCol(0, 2, get_customer_name($myrow['DebtorNo']), $dec);
$rep->TextCol(2, 3, $trans['reference']);
					$rep->TextCol(3,5, $trans2['description'], $dec);

					$rep->NewLine(0,1);
					$rep->AmountCol(9, 10, $trans2['unit_price'], $dec);

					$rep->NewLine(0,1);
					$rep->AmountCol(8, 9, $trans2['quantity'], $dec);
					$rep->NewLine(0,1);
					$DiscountAmount= (($trans2['unit_price'] * $trans2['quantity']) * $trans2['discount_percent']);
					//$rep->AmountCol(6, 7, $DiscountAmount , $dec);
					$TotalAmount = (($trans2['unit_price'] * $trans2['quantity']) - $DiscountAmount);
					$rep->AmountCol(7, 8, $TotalAmount, $dec);
					//$rep->TextCol(10,11, get_bank_gl_account_name($myrow['bank_account_name']));

					$deb_sum['0'] = $trans2['unit_price'];
					$deb_sum['1'] = $trans2['quantity'];
					$deb_sum['2'] = $DiscountAmount;

					//$deb_sum['3'] = $TotalAmount;

					for ($i = 0; $i < 4; $i++)
					{
						$deb_sum_total[$i] += $deb_sum[$i];
						$deb_sum_total_grandtotal[$i] += $deb_sum[$i];
					}


				}//while

			}//if
			else
			{$rep->NewLine();
				$rep->AmountCol(0, 1, $trans['type'], $dec);
				$rep->AmountCol(1, 2, $trans['trans_no'], $dec);
				//$rep->NewLine(2);


				$rep->TextCol(2, 4, $systypes_array[$trans['type']]);
			$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
				$rep->Font('bold');
				$rep->AmountCol(8, 9, $item[1], $dec);
				$rep->Font();
			} //else
			if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT)
				$item[2] = $item[0] - $item[1] ; // previously $item[0] + $item[1] dz
			else
				$item[2] = $item[0] - $item[1] ;

			//	$rep->AmountCol(7, 8, $accumulate, $dec);


			for ($i = 0; $i < 3; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}

			$total[2] = $total[0] - $total[1];
			$rep->Font('bold');
			// For loop for adding running balance column

			for ($i = 2; $i < 3; $i++)
			//	$rep->AmountCol(10 ,11, $total[$i], $dec); //Balance
			$rep->Font();

		}

		$rep->Line($rep->row - 2);
		$rep->NewLine(2);
		$rep->Font('bold');
		//------------------------
		//	 for ($i = 0; $i < 4; $i++)
		//	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total[$i], $dec);
		//------------------------




		$rep->TextCol(0, 3, _('Total'));
		for ($i = 0; $i < 3; $i++)
			$rep->AmountCol($i + 7, $i + 8, $total[$i], $dec);
		$rep->Font();
		$rep->Line($rep->row  - 4);
		$rep->NewLine(2);
	}
	$rep->fontSize += 2;
	$rep->Font('bold');
	$rep->TextCol(0, 3, _('Grand Total'));

	$rep->fontSize -= 2;

	$grandtotal[2] = $grandtotal[0] - $grandtotal[1];
	for ($i = 0; $i < 3; $i++)
		$rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);

	/*
        for ($i = 0; $i < 3; $i++)
        {

        //	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total_grandtotal[$i], $dec);
            $rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);
        }
    */
$rep->NewLine(3);
$rep->Line($rep->row  - 4);
$rep->fontSize += 2;
	$rep->Font('bold');
	$rep->TextCol(0, 3, _('Today All Banks Deposit'));
	$rep->fontSize -= 2;
	$bank_total=0;
$rep->NewLine(3);
	while ($trans3 = db_fetch($result2))
	{
		//$rep->NewLine(1);
		$rep->TextCol(4, 6, get_bank_account_name2($trans3['bank_act']));
		$rep->TextCol(11, 12, $trans3['payment']);
		//$bank_total += $trans3['amount'];
				//$rep->NewLine(0,1);
		$rep->NewLine();
	}


	$rep->Font();
	$rep->Line($rep->row  - 4);
	$rep->NewLine();

// GL Report Start
//	Syed Hamza 20.09.2016
	$dimension = $dimension2 = 0;
	$pdec = user_percent_dec();
	$salesper = 0.0;
	$salesacc = 0.0;

	$classresult = get_account_classes(false, 0);
	while ($class = db_fetch($classresult))
	{
		$class_per_total = 0;
		$class_acc_total = 0;
		$convert = get_class_type_convert($class["ctype"]);

		//Print Class Name
		$rep->Font('bold');
		$rep->TextCol(0, 5, $class["class_name"]);
		$rep->Font();
		$rep->NewLine();


		if ($compare == 0 || $compare == 2)
		{
			$end = $to;
			if ($compare == 2)
			{
				$begin = $from;
				$headers[3] = _('Budget');
			}
			else
				$begin = begin_fiscalyear();
		}
		elseif ($compare == 1)
		{
			$begin = add_months($from, -12);
			$end = add_months($to, -12);
			$headers[3] = _('Period Y-1');
		}


		//Get Account groups/types under this group/type with no parents
		$typeresult = get_account_types(false, $class['cid'], -1);
		while ($accounttype = db_fetch($typeresult))
		{
			$classtotal = display_type($accounttype["id"], $accounttype["name"], $from, $to, $begin, $end, $compare, $convert, $dec,
				$pdec, $rep, $dimension, $dimension2, $tags, $pg, $graphics);
			$class_per_total += $classtotal[0];
			$class_acc_total += $classtotal[1];
		}

//		Print Class Summary
		$rep->row += 6;
		$rep->Line($rep->row);
		$rep->NewLine();
		$rep->Font('bold');
		$rep->TextCol(0, 3,	_('Total') . " " . $class["class_name"]);
		$rep->AmountCol(6, 7, $class_per_total * $convert, $dec);
		$rep->AmountCol(8, 10, $class_acc_total * $convert, $dec);
		$rep->AmountCol(10, 11, Achieve($class_per_total, $class_acc_total), $pdec);
		$rep->Font();
		$rep->NewLine(2);

		$salesper += $class_per_total;
		$salesacc += $class_acc_total;
	}
	$rep->Font('bold');
	$rep->TextCol(0, 3,	_('Calculated Return'));
	$rep->AmountCol(6, 7, $salesper *-1, $dec); // always convert
	$rep->AmountCol(8, 10, $salesacc * -1, $dec);
	$rep->AmountCol(10, 11, Achieve($salesper, $salesacc), $pdec);
	if ($graphics)
	{
		$pg->x[] = _('Calculated Return');
		$pg->y[] = abs($salesper);
		$pg->z[] = abs($salesacc);
	}
	$rep->Font();
	$rep->NewLine();
	$rep->Line($rep->row);
	if ($graphics)
	{
		global $decseps, $graph_skin;
		$pg->title     = $rep->title;
		$pg->axis_x    = _("Group");
		$pg->axis_y    = _("Amount");
		$pg->graphic_1 = $headers[2];
		$pg->graphic_2 = $headers[3];
		$pg->type      = $graphics;
		$pg->skin      = $graph_skin;
		$pg->built_in  = false;
		$pg->latin_notation = ($decseps[$_SESSION["wa_current_user"]->prefs->dec_sep()] != ".");
		$filename = company_path(). "/pdf_files/". uniqid("").".png";
		$pg->display($filename, true);
		$w = $pg->width / 1.5;
		$h = $pg->height / 1.5;
		$x = ($rep->pageWidth - $w) / 2;
		$rep->NewLine(2);
		if ($rep->row - $h < $rep->bottomMargin)
			$rep->NewPage();
		$rep->AddImage($filename, $x, $rep->row - $h, $w, $h);
	}
	$rep->End();
}

?>