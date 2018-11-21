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

print_customer_balances();

function get_open_balance($debtorno, $to,$convert,$dimension,$dimension2)
{
   
	if($to)
		$to = date2sql($to);

     $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
     	-abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
     $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
     	abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1, 0)) AS credits,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0), t.alloc * -1, t.alloc)) AS Allocated,";

 	$sql .=	"SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			(abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";

    if ($dimension != 0 )
    {
        $sql .= " AND t.dimension_id=".db_escape($dimension);
    }
    if ($dimension2 != 0 )
    {
        $sql .= " AND t.dimension2_id=".db_escape($dimension2);
    }
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to,$dimension,$dimension2)
{
	$from = date2sql($from);
	$to = date2sql($to);

 	$allocated_from = 
 			"(SELECT trans_type_from as trans_type, trans_no_from as trans_no, date_alloc, sum(amt) amount
 			FROM ".TB_PREF."cust_allocations alloc
 				WHERE person_id=".db_escape($debtorno)."
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_from, trans_no_from) alloc_from";
 	$allocated_to = 
 			"(SELECT trans_type_to as trans_type, trans_no_to as trans_no, date_alloc, sum(amt) amount
 			FROM ".TB_PREF."cust_allocations alloc
 				WHERE person_id=".db_escape($debtorno)."
 					AND date_alloc <= '$to'
 				GROUP BY trans_type_to, trans_no_to) alloc_to";

     $sql = "SELECT trans.*,
 		(trans.ov_amount + trans.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2) AS TotalAmount,
 		IFNULL(alloc_from.amount, alloc_to.amount) AS Allocated,
 		((trans.type = ".ST_SALESINVOICE.")	AND trans.due_date < '$to') AS OverDue
     	FROM ".TB_PREF."debtor_trans trans
 			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
 			LEFT JOIN $allocated_from ON alloc_from.trans_type = trans.type AND alloc_from.trans_no = trans.trans_no
 			LEFT JOIN $allocated_to ON alloc_to.trans_type = trans.type AND alloc_to.trans_no = trans.trans_no

     	WHERE trans.tran_date >= '$from'
 			AND trans.tran_date <= '$to'
 			AND trans.debtor_no = ".db_escape($debtorno)."
 			AND trans.type <> ".ST_CUSTDELIVERY."
 			AND ISNULL(voided.id)
     	";

    if ($dimension != 0 )

    {
        $sql .= " AND trans.dimension_id=".db_escape($dimension);
    }
    if ($dimension2 != 0 )

    {
        $sql .= " AND trans.dimension2_id=".db_escape($dimension2);
    }

    $sql .= " ORDER BY trans.tran_date " ;
    return db_query($sql,"No transactions were returned");
}

//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$fromcust = $_POST['PARAM_2'];
    	$dimension = $_POST['PARAM_3'];
    	$dimension2 = $_POST['PARAM_4'];
        $area = $_POST['PARAM_5'];
        $folk = $_POST['PARAM_6'];
    	$show_balance = $_POST['PARAM_7'];
    	$currency = $_POST['PARAM_8'];
    	$no_zeros = $_POST['PARAM_9'];
    	$comments = $_POST['PARAM_10'];
        $orientation = $_POST['PARAM_11'];
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

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 70, 125, 195,	250, 320, 385, 450,	515);

	$headers = array(_('Customer Name'), _(''), _(''), _(''), _('Debit'), _('Credits'),
		_(''), 	_(''));

	if ($show_balance)
		$headers[7] = _('Balance');
	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
                        3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),
                        4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
    				    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Agreement Invoices'), "CustomerBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);



//    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS Debtor_no,
//     ".TB_PREF."debtors_master.name AS Name,
//      ".TB_PREF."debtors_master.curr_code
//    FROM ".TB_PREF."debtors_master
//    INNER JOIN ".TB_PREF."cust_branch
//			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
//    INNER JOIN ".TB_PREF."areas
//			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code
//		INNER JOIN ".TB_PREF."salesman
//			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";
//    $sql .= "  WHERE debtor_no != 0";
//    if ($fromcust != ALL_TEXT )
//    {
//        if ($area != 0 || $folk != 0)
//            continue;
//        $sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
//    }
//    if ($dimension != 0)
//        $sql .= " AND dimension_id=".db_escape($dimension);
//    if ($area != 0)
//    {
//        if ($folk != 0)
//            $sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
//						AND ".TB_PREF."areas.area_code=".db_escape($area);
//        else
//            $sql .= " WHERE ".TB_PREF."areas.area_code=".db_escape($area);
//    }
//    if ($folk != 0 )
//    {
//        $sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);
//    }
//
//    $sql .= " ORDER BY Name";
//    $result = db_query($sql, "The customers could not be retrieved");




    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
			".TB_PREF."debtors_master.name AS Name
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

    $sql .= " WHERE ".TB_PREF."cust_branch.inactive != 1";

    if ($fromcust != ALL_TEXT )
    {
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
    }
    if ($area != 0)
    {
            $sql .= " AND ".TB_PREF."areas.area_code=".db_escape($area);
    }
    if ($folk != 0 )
    {
        $sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
    }
//    if ($dimension != 0 )
//
//    {
//        $sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
//    }
//    if ($dimension2 != 0 )
//
//    {
//        $sql .= " AND ".TB_PREF."debtors_master.dimension2_id=".db_escape($dimension2);
//    }


    // 
    $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no ORDER BY Name"; 
    $result = db_query($sql, "The customers could not be retrieved");



	while ($myrow = db_fetch($result))
	{

		if (!$convert && $currency != $myrow['curr_code']) continue;
		
		$accumulate = 0;
		$rate = $convert ? get_exchange_rate_from_home_currency($myrow['curr_code'], Today()) : 1;
		$bal = get_open_balance($myrow['DebtorNo'], $from, $convert,$dimension,$dimension2);
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

		$res = get_transactions($myrow['DebtorNo'], $from, $to,$dimension,$dimension2);
		if ($no_zeros && db_num_rows($res) == 0) continue;

		$rep->fontSize += 2;
		$rep->TextCol(0, 2, $myrow['Name']);
		if ($convert)
			$rep->TextCol(2, 3,	$myrow['curr_code']);
		$rep->fontSize -= 2;
//		$rep->TextCol(3, 4,	_("Open Balance"));
//		$rep->AmountCol(4, 5, $init[0], $dec);
//		$rep->AmountCol(5, 6, $init[1], $dec);
//		$rep->AmountCol(6, 7, $init[2], $dec);
//		$rep->AmountCol(7, 8, $init[3], $dec);
        $rep->AmountCol(4, 5, $total[0], $dec);
        $rep->AmountCol(5, 6, $total[1], $dec);
        $rep->AmountCol(7, 8, $total[3], $dec);
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
		while ($trans = db_fetch($res))
		{
		    
		   
			if ($no_zeros && floatcmp($trans['TotalAmount'] == 0)) continue;
			/*if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;*/
		//	$rep->NewLine(1, 2);
			//$rep->TextCol(2, 3, $systypes_array[$trans['type']]);
			//$rep->TextCol(0, 1,	$trans['reference']);
			//$rep->DateCol(1, 2,	$trans['tran_date'], true);
			if ($trans['type'] == ST_SALESINVOICE)
				//$rep->DateCol(3, 4,	$trans['due_date'], true);
			$item[0] = $item[1] = 0.0;
			if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
				$trans['TotalAmount'] *= -1;
				
			if ($trans['TotalAmount'] > 0.0)
			{
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
			//	$rep->AmountCol(4, 5, $item[0], $dec);
				$accumulate += $item[0];
			}
			else
			{
			//	$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
			
			 $item[1] = round2(Abs($trans['TotalAmount']) * $rate , $dec);
			
			
				//$rep->AmountCol(5, 6, $item[1], $dec);
				$accumulate -= $item[1];
				
			 
			}
		  
 $item[2] = round2(Abs($trans['Allocated']) * $rate, $dec) ;
		//	$rep->AmountCol(6, 7, $item[2], $dec);
            $memo = get_comments_string($trans['type'], $trans['trans_no']);
            if ($memo != "")
            {
               // $rep->NewLine();
               // $rep->TextCol(0, 8, $memo, -2);
            }
			if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT ||    $trans['type'] == ST_CPV||
                $trans['type'] == ST_JOURNAL)
				$item[3] = $item[0] + $item[1] - $item[2];
			else	
				$item[3] = $item[0] - $item[1] + $item[2];
			//if ($show_balance)
				//$rep->AmountCol(7, 8, $accumulate, $dec);
			//else
				//$rep->AmountCol(7, 8, $item[3], $dec);
				
			$memo = get_comments_string($trans['type'], $trans['trans_no']);
			if ($memo != "")
			{
				$rep->Font('bold');
				//$rep->NewLine();
				//$rep->TextColLines(0,2, "Memo : ".$memo, -2);
				$rep->Font();
			}
			
			for ($i = 0; $i < 4; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}
// 			if ($show_balance)
				$total[3] = $total[0] - $total[1];
		}

		//$rep->Line($rep->row - 8);
		$rep->NewLine(2);
        $rep->font('b');
		//$rep->TextCol(0, 3, _('Total'));
        $rep->font('');
		/*for ($i = 0; $i < 4; $i++)
			$rep->AmountCol($i + 4, $i + 5, $total[$i], $dec);*/
//        $rep->AmountCol(4, 5, $total[0], $dec);
//        $rep->AmountCol(5, 6, $total[1], $dec);
//        $rep->AmountCol(7, 8, $total[3], $dec);

      //  $rep->Line($rep->row  - 4);
   		//$rep->NewLine(2);
	}
	$rep->fontSize += 2;
    $rep->font('b');
	$rep->TextCol(0, 3, _('Grand Total'));
    $rep->font('');
	$rep->fontSize -= 2;
// 	if ($show_balance)
		$grandtotal[3] =  $grandtotal[0]- $grandtotal[1];
			//	$grandtotal[1] += $total[1];

		
	/*for ($i = 0; $i < 4; $i++)
		$rep->AmountCol($i + 4, $i + 5, $grandtotal[$i], $dec);*/
        $rep->AmountCol(4, 5, $grandtotal[0], $dec);
        $rep->AmountCol(5, 6, $grandtotal[1], $dec); 
        $rep->AmountCol(7, 8, $grandtotal[3], $dec);  
				
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    	$rep->End();
}