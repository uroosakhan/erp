<?php
$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Aged Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_customer_analysis();

function get_invoices($customer_id, $to, $all=true)
{

	$todate = date2sql($to);
    $mo = date('m',strtotime($to));
    $yr = date('Y',strtotime($to));
//	$PastDueDays1 = get_company_pref('past_due_days');
//	$PastDueDays2 = 2 * $PastDueDays1;

	$date12 = date('Y-m-d');
	$date11 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
	$date10 = date('Y-m-d',mktime(0,0,0,$mo-1,1,$yr));
	$date9 = date('Y-m-d',mktime(0,0,0,$mo-2,1,$yr));
	$date8 = date('Y-m-d',mktime(0,0,0,$mo-3,1,$yr));
	$date7 = date('Y-m-d',mktime(0,0,0,$mo-4,1,$yr));
	$date6 = date('Y-m-d',mktime(0,0,0,$mo-5,1,$yr));
	$date5 = date('Y-m-d',mktime(0,0,0,$mo-6,1,$yr));
	$date4 = date('Y-m-d',mktime(0,0,0,$mo-7,1,$yr));
	$date3 = date('Y-m-d',mktime(0,0,0,$mo-8,1,$yr));
	$date2 = date('Y-m-d',mktime(0,0,0,$mo-9,1,$yr));
	$date1 = date('Y-m-d',mktime(0,0,0,$mo-10,1,$yr));
	$date0 = date('Y-m-d',mktime(0,0,0,$mo-11,1,$yr));
	// Revomed allocated from sql

    	$value = "(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + "
			.TB_PREF."debtor_trans.ov_freight + ".TB_PREF."debtor_trans.ov_freight_tax + "
			.TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1  - ".TB_PREF."debtor_trans.discount2)";

	$due = "IF (".TB_PREF."debtor_trans.type=".ST_SALESINVOICE.",".TB_PREF."debtor_trans.due_date,".TB_PREF."debtor_trans.tran_date)";
	$sql = "SELECT ".TB_PREF."debtor_trans.type, ".TB_PREF."debtor_trans.reference,
		".TB_PREF."debtor_trans.tran_date,
		$value as Balance,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date0' AND ".TB_PREF."debtor_trans.tran_date < '$date1',$value,0) AS prd0,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date1' AND ".TB_PREF."debtor_trans.tran_date < '$date2',$value,0) AS prd1,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date2' AND ".TB_PREF."debtor_trans.tran_date < '$date3',$value,0) AS prd2,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date3' AND ".TB_PREF."debtor_trans.tran_date < '$date4',$value,0) AS prd3,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date4' AND ".TB_PREF."debtor_trans.tran_date <= '$date5',$value,0) AS prd4,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date5' AND ".TB_PREF."debtor_trans.tran_date <= '$date6',$value,0) AS prd5,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date6' AND ".TB_PREF."debtor_trans.tran_date <= '$date7',$value,0) AS prd6,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date7' AND ".TB_PREF."debtor_trans.tran_date <= '$date8',$value,0) AS prd7,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date8' AND ".TB_PREF."debtor_trans.tran_date <= '$date9',$value,0) AS prd8,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date9' AND ".TB_PREF."debtor_trans.tran_date <= '$date10',$value,0) AS prd9,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date10' AND ".TB_PREF."debtor_trans.tran_date <= '$date11',$value,0) AS prd10,
		IF (".TB_PREF."debtor_trans.tran_date >= '$date11' AND ".TB_PREF."debtor_trans.tran_date <= '$date12',$value,0) AS prd11

		
		FROM ".TB_PREF."debtors_master,
			".TB_PREF."debtor_trans

		WHERE ".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE."
			AND ".TB_PREF."debtors_master.debtor_no = ".TB_PREF."debtor_trans.debtor_no
			AND ".TB_PREF."debtor_trans.debtor_no = $customer_id 

			AND ABS(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + ".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1 - ".TB_PREF."debtor_trans.discount2) > ".FLOAT_COMP_DELTA." ";

	return db_query($sql, "The customer details could not be retrieved");
}

//----------------------------------------------------------------------------------------------------

function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array;

	$to = $_POST['PARAM_0'];
	$fromcust = $_POST['PARAM_1'];
	$area = $_POST['PARAM_2'];
	$folk = $_POST['PARAM_3'];
	$currency = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
//	$orientation = ($orientation ? 'L' : 'P');
	
	     	$orientation = 'L';
/*	if ($graphics)
	{
		include_once($path_to_root . "/reporting/includes/class.graphic.inc");
		$pg = new graph();
	}
*/
	if ($fromcust == ALL_TEXT)
		$from = _('All');
	else
		$from = get_customer_name($fromcust);
//    	$dec = user_price_dec();
    	$dec = 0;
		
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
/*
	if ($summaryOnly == 1)
		$summary = _('Summary Only');
	else
		$summary = _('Detailed Report');
*/
	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;
/*
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');
	if ($show_all) $show = _('Yes');
	else $show = _('No');
*/
/*	$PastDueDays1 = get_company_pref('past_due_days');
	$PastDueDays2 = 2 * $PastDueDays1;
	$nowdue = "1-" . $PastDueDays1 . " " . _('Days');
	$pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');
*/
//	$cols = array(0, 100, 130, 190,	250, 320, 385, 450,	515);
//	$headers = array(_('Customer'),	'',	'',	_('Current'), $nowdue, $pastdue1, $pastdue2,
//		_('Total Balance'));

//	$aligns = array('left',	'left',	'left',	'right', 'right', 'right', 'right',	'right');

    $mo = date('m',strtotime($to));
    $yr = date('Y',strtotime($to));
//	$cols = array(0, 0, 95, 150, 195, 250, 295, 350, 395, 450, 495, 550);
	$cols = array(0, 0, 55, 125, 160, 195, 230, 265, 300, 335, 370, 405, 440, 475, 510, 555);

	$per0 = strftime('%b',mktime(0,0,0,$mo,1,$yr));
	$per1 = strftime('%b',mktime(0,0,0,$mo-1,1,$yr));
	$per2 = strftime('%b',mktime(0,0,0,$mo-2,1,$yr));
	$per3 = strftime('%b',mktime(0,0,0,$mo-3,1,$yr));
	$per4 = strftime('%b',mktime(0,0,0,$mo-4,1,$yr));
	$per5 = strftime('%b',mktime(0,0,0,$mo-5,1,$yr));
	$per6 = strftime('%b',mktime(0,0,0,$mo-6,1,$yr));
	$per7 = strftime('%b',mktime(0,0,0,$mo-7,1,$yr));
	$per8 = strftime('%b',mktime(0,0,0,$mo-8,1,$yr));
	$per9 = strftime('%b',mktime(0,0,0,$mo-9,1,$yr));
	$per10 = strftime('%b',mktime(0,0,0,$mo-10,1,$yr));
	$per11 = strftime('%b',mktime(0,0,0,$mo-11,1,$yr));

	$headers = array(_('Customers'), '', $per11, $per10, $per9, $per8, $per7, $per6, $per5,  $per4,  $per3, $per2,  $per1, $per0, _('Total'));

	$aligns = array('left',	'left', 'right','right','right','right',	'right', 'right', 'right', 'right', 'right', 'right',
		'right', 'right', 'right', 'right', 'right');


    	$params =   array( 	0 => $comments,
    				1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
    				2 => array('text' => _('Customer'),	'from' => $from, 'to' => ''),
    				3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
    				4 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),						
    				5 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
			);

//	if ($convert)
//		$headers[2] = _('Currency');
    $rep = new FrontReport(_('Customer Sales History'), "AgedCustomerAnalysis", user_pagesize(), 9, $orientation);
//    if ($orientation == 'L')

    	recalculate_cols($cols);
 
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array(0,0,0,0,0);
/*
	$sql = "SELECT debtor_no, name, curr_code FROM ".TB_PREF."debtors_master";
	if ($fromcust != ALL_TEXT)
		$sql .= " WHERE debtor_no=".db_escape($fromcust);
	$sql .= " ORDER BY name";
	$result = db_query($sql, "The customers could not be retrieved");
*/

	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
			".TB_PREF."debtors_master.name 
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

		if ($fromcust != ALL_TEXT )
			{
				if ($area != 0 || $folk != 0) continue;
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
		

	$sql .= " ORDER BY name";	
	$result = db_query($sql, "The customers could not be retrieved");

	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code'])
			continue;

		if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
		else $rate = 1.0;
//		$custrec = get_customer_details($myrow['debtor_no'], $to, $show_all);
		$custrec = get_customer_details($myrow['debtor_no'], $to);
		if (!$custrec)
			continue;
/*		$custrec['Balance'] *= $rate;
		$custrec['Due'] *= $rate;
		$custrec['Overdue1'] *= $rate;
		$custrec['Overdue2'] *= $rate;
*/
		$custrec['prd0'] *= $rate;
		$custrec['prd1'] *= $rate;
		$custrec['prd2'] *= $rate;
		$custrec['prd3'] *= $rate;
		$custrec['prd4'] *= $rate;		
		$custrec['prd5'] *= $rate;
		$custrec['prd6'] *= $rate;
		$custrec['prd7'] *= $rate;
		$custrec['prd8'] *= $rate;
		$custrec['prd9'] *= $rate;
		$custrec['prd10'] *= $rate;
		$custrec['prd11'] *= $rate;

		$str = array(
			$custrec["prd0"],
			$custrec["prd1"],
			$custrec["prd2"],
			$custrec["prd3"],
			$custrec["prd4"],
			$custrec["prd5"],
			$custrec["prd6"],
			$custrec["prd7"],
			$custrec["prd8"],
			$custrec["prd9"],
			$custrec["prd10"],
			$custrec["prd11"]);
	//	if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;

		$rep->fontSize -= 2;
		$rep->TextCol(0, 2, $myrow['name']);
	//	if ($convert) $rep->TextCol(2, 3,	$myrow['curr_code']);
		$rep->fontSize += 2;
/*
		$total[0] += $custrec["prd0"];
		$total[1] += $custrec["prd1"];
		$total[2] += $custrec["prd2"];
		$total[3] += $custrec["prd3"];
		$total[4] += $custrec["prd4"];
*/
/*		$total[0] += ($custrec["Balance"] - $custrec["Due"]);
		$total[1] += ($custrec["Due"]-$custrec["Overdue1"]);
		$total[2] += ($custrec["Overdue1"]-$custrec["Overdue2"]);
		$total[3] += $custrec["Overdue2"];
		$total[4] += $custrec["Balance"];*/
		
		$total[0] = 0;
		$total[1] = 0;
		$total[2] = 0;
		$total[3] = 0;
		$total[4] = 0;
		$total[5] = 0;
		$total[6] = 0;
		$total[7] = 0;
		$total[8] = 0;
		$total[9] = 0;
		$total[10] = 0;
		$total[11] = 0;
		$linetotal = 0;

			$res = get_invoices($myrow['debtor_no'], $to);
			while ($trans=db_fetch($res))
			{
				foreach ($trans as $i => $value)
					$trans[$i] *= $rate;
				$str = array(
					$trans["prd0"],		
					$trans["prd1"],		
					$trans["prd2"],		
					$trans["prd3"],					
					$trans["prd4"],
					$trans["prd5"],
					$trans["prd6"],
					$trans["prd7"],
					$trans["prd8"],
					$trans["prd9"],
					$trans["prd10"],
					$trans["prd11"]
				);

					$total[0] += $str["0"];
					$total[1] += $str["1"];
					$total[2] += $str["2"];
					$total[3] += $str["3"];
					$total[4] += $str["4"];
					$total[5] += $str["5"];
					$total[6] += $str["6"];
					$total[7] += $str["7"];
					$total[8] += $str["8"];
					$total[9] += $str["9"];
					$total[10] += $str["10"];
					$total[11] += $str["11"];

					$linetotal = $total[0] + $total[1] + $total[2] + $total[3] + $total[4] + $total[5] + $total[6] + $total[7] + $total[8] + $total[9] + $total[10] + $total[11];
										
					$grandtotal[0] += $str["0"];
					$grandtotal[1] += $str["1"];
					$grandtotal[2] += $str["2"];
					$grandtotal[3] += $str["3"];
					$grandtotal[4] += $str["4"];
					$grandtotal[5] += $str["5"];
					$grandtotal[6] += $str["6"];
					$grandtotal[7] += $str["7"];
					$grandtotal[8] += $str["8"];
					$grandtotal[9] += $str["9"];
					$grandtotal[10] += $str["10"];
					$grandtotal[11] += $str["11"];


					
			//	for ($i = 0; $i < count($str); $i++)
			//		$rep->AmountCol($i + 4, $i + 5, $str[$i], $dec);
		/*			$rep->AmountCol(2, 3, $str["0"], $dec);					
					$rep->AmountCol(3, 4, 1, $dec);		
					$rep->AmountCol(4, 5, $str["1"], $dec);					
					$rep->AmountCol(5, 6, 1, $dec);		
					$rep->AmountCol(6, 7, $str["2"], $dec);					
					$rep->AmountCol(7, 8, 1, $dec);		
					$rep->AmountCol(8, 9, $str["3"], $dec);					
					$rep->AmountCol(9, 10, 1, $dec);		
					$rep->AmountCol(10, 11, $str["4"], $dec);					
*/

			}
					$rep->AmountCol(2, 3, $total[0], $dec);
					$ID1 = number_format2(($total['1'] - $total['0']) / $total['0']/**100*/, $dec);
//					$rep->TextCol(3, 4, $ID1 ." %", -2);
					$rep->AmountCol(3, 4, $total[1], $dec);
					$rep->AmountCol(4, 5, $total[2], $dec);
					$rep->AmountCol(5, 6, $total[3], $dec);
					$rep->AmountCol(6, 7, $total[4], $dec);
					$rep->AmountCol(7, 8, $total[5], $dec);
					$rep->AmountCol(8, 9, $total[6], $dec);
					$rep->AmountCol(9, 10, $total[7], $dec);
					$rep->AmountCol(10, 11, $total[8], $dec);
					$rep->AmountCol(11, 12, $total[9], $dec);
					$rep->AmountCol(12, 13, $total[10], $dec);
					$rep->AmountCol(13, 14, $total[11], $dec);
				    $rep->Font(b);
					$rep->AmountCol(14, 15, $linetotal, $dec);
				    $rep->Font();					
					$grosstotal += $linetotal;

			$rep->NewLine();

		
	}
	
/*	if ($summaryOnly)
	{
    	$rep->Line($rep->row  + 4);
    	$rep->NewLine();
	}
*/
   	$rep->Line($rep->row  + 4);
   	$rep->NewLine();
	$rep->fontSize += 2;
	$rep->TextCol(0, 3, _('Grand Total'));
	$rep->fontSize -= 2;
//	for ($i = 0; $i < count($total); $i++)
//	{
	    $rep->Font(b);
		$rep->AmountCol(2, 3, $grandtotal[0], $dec);
		$rep->AmountCol(3, 4, $grandtotal[1], $dec);
		$rep->AmountCol(4, 5, $grandtotal[2], $dec);
		$rep->AmountCol(5, 6, $grandtotal[3], $dec);
		$rep->AmountCol(6, 7, $grandtotal[4], $dec);
		$rep->AmountCol(7, 8, $grandtotal[5], $dec);
		$rep->AmountCol(8, 9, $grandtotal[6], $dec);
		$rep->AmountCol(9, 10, $grandtotal[7], $dec);
		$rep->AmountCol(10, 11, $grandtotal[8], $dec);
		$rep->AmountCol(11, 12, $grandtotal[9], $dec);
		$rep->AmountCol(12, 13, $grandtotal[10], $dec);
		$rep->AmountCol(13, 14, $grandtotal[11], $dec);
		$rep->AmountCol(14, 15, $grosstotal, $dec);
	    $rep->Font();
//		$rep->AmountCol($i + 2, $i + 3, $total[$i], $dec);
/*		if ($graphics && $i < count($total) - 1)
		{
			$pg->y[$i] = abs($total[$i]);
		}
*/
//	}
 //  	$rep->Line($rep->row - 8);

	$rep->NewLine();
    $rep->End();
}

?>
