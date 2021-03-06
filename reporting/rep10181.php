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

//----------------------------------------------------------------------------------------------------

// trial_inquiry_controls();
print_customer_balances();
/*
function get_open_balance($debtorno, $to, $convert)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount2 - t.discount2)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount2 - t.discount2)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount2 - t.discount2 - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount2 - t.discount2) * -1 + t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ")) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
*/
function get_open_balance($debtorno, $to)
{
	if($to)
		$to = date2sql($to);

     $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0),
     	-abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
     $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND (t.type = ".ST_JOURNAL." AND t.ov_amount<0),
     	abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1, 0)) AS credits,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount<0), t.alloc * -1, t.alloc)) AS Allocated,";

 	$sql .=	"SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			(-abs(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
function get_salesman_code10181($id)
{
    $sql = "SELECT break_pt FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
/*
function get_transactions($debtorno, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1 - ".TB_PREF."debtor_trans.discount2)
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
*/
function get_transactions($debtorno, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh +
		".TB_PREF."debtor_trans.supply_disc + ".TB_PREF."debtor_trans.service_disc - ".TB_PREF."debtor_trans.discount1 - ".TB_PREF."debtor_trans.discount2)
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
//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

        $from = $_POST['PARAM_0'];
        $to = $_POST['PARAM_1'];
        $folk = $_POST['PARAM_2'];
        $dimension = $_POST['PARAM_3'];
        $currency = $_POST['PARAM_4'];
        $no_zeros = $_POST['PARAM_5'];
        $comments = $_POST['PARAM_6'];
        $destination = $_POST['PARAM_7'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if ($fromcust == ALL_TEXT)
		$cust = _('All');
	else
		$cust = get_customer_name($fromcust);
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


if ($folk == ALL_NUMERIC)
		$folk = 0;


	if ($folk == 0)
		$salesfolk = _('All Sales Person');
	else
		$salesfolk = get_salesman_name($folk);


	$cols = array(0, 150, 220, 270,	350, 400, 450, 525);

	$headers = array(_('Customer'), _('Sales Target'), _('Net Sale'), _('Difference Sales'), _('Recovery'), _('Difference'),
_('Cust Balance'));

	$aligns = array('left',	'right',	'right',	'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
				2 => array('text' => _('Sales Person'), 'from' => $salesfolk, 	'to' => ''),
    				    3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
				4 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => '')



						);

    $rep = new FrontReport(_('Salesman Recovery Report - Summary'), "CustomerBalances", user_pagesize());
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);

	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
	 ".TB_PREF."debtors_master.name, 
	 ".TB_PREF."debtors_master.curr_code,
	  ".TB_PREF."debtors_master.address,
	   ".TB_PREF."cust_branch.salesman as Salesman_
	FROM 
	".TB_PREF."debtors_master ,".TB_PREF."cust_branch";

$sql .= " WHERE ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no";

//	    AND ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code			
	if ($fromcust != ALL_TEXT)
		$sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);

if ($folk != 0)
		{
		$sql .= " AND ".TB_PREF."cust_branch.salesman=".db_escape($folk);
		}

elseif ($dimension != 0 )

{
	$sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
}

	$sql .= " ORDER BY name";
	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;

	while ($myrow = db_fetch($result))
	{

		if (!$convert && $currency != $myrow['curr_code']) continue;

		$bal = get_open_balance($myrow['debtor_no'], $from, $convert);
		$init[0] = $init[1] = 0.0;
		//$init[0] = round2(abs($bal['charges']), $dec);
		//$init[1] = round2(Abs($bal['credits']), $dec);
		//$init[2] = round2($bal['Allocated'], $dec);
		//$init[3] = round2($bal['OutStanding'], $dec);
		$init[4] = round2(abs($bal['charges']), $dec); // added new for opening balance
		$init[5] = round2(Abs($bal['credits']), $dec);  // added new for opening balance

		$res = get_transactions($myrow['debtor_no'], $from, $to);
		if ($no_zeros && db_num_rows($res) == 0) continue;

 		$num_lines++;
		$rep->fontSize += 2;
		$rep->NewLine();
		$rep->TextCol(0, 1, $myrow['name']);
		//if ($convert)
		//	$rep->TextCol(2, 3,	$myrow['curr_code']);

		$rep->fontSize -= 4;
		//$rep->TextCol(2, 4,	$myrow['address']);
		$rep->fontSize += 2;
		//$rep->TextCol(3, 4,	_("Open Balance"));
		//$rep->AmountCol(4, 5, $init[0], $dec);
		//$rep->AmountCol(5, 6, $init[1], $dec);
		//$rep->AmountCol(6, 7, $init[2], $dec);
		//$rep->AmountCol(6, 7, $init[3], $dec);
	
		$init[6] = $init[4] - $init[5];  // added new for opening balance
    $ob = $init[6];
			//	$item[2] = $init[6];	
		//$rep->AmountCol(2, 3, $init[6], $dec); 
		$total = array(0,0,0);
		for ($i = 0; $i < 3; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}

//		$rep->NewLine(1, 2);
//		if (db_num_rows($res)==0)
//			continue;
	//	$rep->Line($rep->row + 4);
		while ($trans = db_fetch($res))
		{
			//if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
			//$rep->NewLine(1, 2);
			//$rep->TextCol(0, 1, $systypes_array[$trans['type']]);
			//$rep->TextCol(1, 2,	$trans['reference']);
			//$rep->DateCol(2, 3,	$trans['tran_date'], true);
			//if ($trans['type'] == ST_SALESINVOICE)
				//$rep->DateCol(3, 4,	$trans['due_date'], true);
			$item[0] = $item[1] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
            if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
                $trans['TotalAmount'] *= -1;
			if ($trans['TotalAmount'] > 0.0)
			{
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				//$rep->AmountCol(4, 5, $item[0], $dec);
			}
			else
			{
				$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
				//$rep->AmountCol(5, 6, $item[1], $dec);
			}
			//$item[2] = round2($trans['Allocated'] * $rate, $dec);
			//$rep->AmountCol(6, 7, $item[2], $dec);
			/*
			if ($trans['type'] == 10)
				$item[3] = ($trans['TotalAmount'] - $trans['Allocated']) * $rate;
			else
				$item[3] = ($trans['TotalAmount'] + $trans['Allocated']) * $rate;
			*/
//            if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT || $trans['type'] == ST_CPV)
//				$item[2] = $item[0] - $item[1]; // previously $item[0] + $item[1] dz
//			else
//				$item[2] = $item[0] - $item[1] ;

			for ($i = 0; $i < 2; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}
		}
		//$rep->Line($rep->row - 8);		
	//	$rep->NewLine(2);
		//$rep->TextCol(0, 3, _('Total'));

		{
//		for ($i = 0; $i < 2; $i++)

	{
            $target = get_salesman_code10181($myrow['Salesman_']);
            $rep->AmountCol(1, 2, $target, $dec);
			$rep->AmountCol(2, 3, $total[0], $dec);
			$rep->AmountCol(3, 4, $total[0] - $target , $dec);
			$rep->AmountCol(4, 5, $total[1], $dec);
			$rep->AmountCol(5, 6, $total[0] - $total[1], $dec);

			//$total[2] = $init[6] + $total[0] - $total[1];
			//$rep->AmountCol(6,  7, $total[2], $dec);
	}
			//$RemainingBalance = $init[6] + $total[0] - $total[1];
			//$rep->AmountCol( 6, 7, $RemainingBalance, $dec);

//				$RemainingBalance += $s2;
		}


//start	3.9.12
		// For loop for adding running balance column
//			for ($i = 3; $i < 4; $i++)

			
		//	$rep->AmountCol( 6, 7, $item[3], $dec);
	//$RemainingBalance += $s2;
		
    		//$rep->Line($rep->row  - 4);
  		/*	for ($i = 3; $i < 4; $i++)
		{
			$RemainingBalance += $s2;
		}*/

	}
	
	$rep->Line($rep->row  - 4);
	$rep->NewLine(1.5);
	$rep->fontSize += 2;
	$rep->Font(bold);	

	$rep->TextCol(0, 3, _('Grand Total'));
	$rep->fontSize -= 2;
	for ($i = 0; $i < 3; $i++)
		$rep->AmountCol($i + 3, $i + 4, $grandtotal[$i], $dec);

	//$rep->NewLine(2);
    	$rep->End();
}

?>