<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Detailed
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
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2 - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1 + t.alloc)";
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
/*
function get_open_balance($debtorno, $to, $convert)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
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
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to, $sys_type, $dimension, $folk, $payment_terms, $sls_group)
{
	$from = date2sql($from);
	$to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*,
	(".TB_PREF."debtor_trans.ov_amount +".TB_PREF."debtor_trans.supply_disc + 
	".TB_PREF."debtor_trans.service_disc + ".TB_PREF."debtor_trans.fbr_disc +
	".TB_PREF."debtor_trans.srb_disc+".TB_PREF."debtor_trans.ov_gst +
	".TB_PREF."debtor_trans.ov_freight +".TB_PREF."debtor_trans.ov_freight_tax + 
	".TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1 - 
	".TB_PREF."debtor_trans.discount2) AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated";
    if ($sys_type == -1)
    $sql .= ", ((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue";
    $sql .=  " FROM ".TB_PREF."debtor_trans
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY;
        // if ($sys_type != -1)
		  //  $sql .= " AND ".TB_PREF."debtor_trans.type = 10"/*.db_escape($sys_type)*/;
// 	    if ($dimension != 0 )
// 		{
//         	$sql .= " AND ".TB_PREF."debtor_trans.dimension_id=".db_escape($dimension);
// 		}    
		if ($folk != 0 )
		{
        	$sql .= " AND ".TB_PREF."debtor_trans.salesman=".db_escape($folk);
		}    

		if ($payment_terms != 0 )
		{
        	$sql .= " AND ".TB_PREF."debtor_trans.payment_terms=".db_escape($payment_terms);
		}   
		
		if ($sls_group != 0 )
		{
        	$sql .= " AND ".TB_PREF."debtor_trans.text_1=".db_escape($sls_group);
		}   
		    
    	$sql .=  " ORDER BY ".TB_PREF."debtor_trans.tran_date";


    return db_query($sql,"No transactions were returned");
}

function get_memo_($type,$type_no)
{
	$sql = "SELECT * FROM ".TB_PREF."gl_trans
    	WHERE type = ".db_escape($type) ." AND type_no = ".db_escape($type_no)  ." ORDER BY counter LIMIT 1";
	$result = db_query($sql,"No transactions were returned");
	$data = db_fetch($result);
	return $data;
}
function get_transactions2($debtorno, $from, $to, $transno, $type)
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
    	AND ".TB_PREF."debtor_trans_details.debtor_trans_type = $type	
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";


    return db_query($sql,"No transactions were returned");
}

function get_payment_terms_names($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
	$row =db_fetch_row($result);
	return $row[0];
}
function get_sls_group($selected_id)
{
	$sql = "SELECT description
	 FROM ".TB_PREF."combo3  WHERE combo_code=".db_escape($selected_id);
	$result = db_query($sql,"could not get payment term");
	$row = db_fetch_row($result);
	return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$fromcust = $_POST['PARAM_2'];
    // 	$dimension = $_POST['PARAM_3'];
        $payment_terms = $_POST['PARAM_3'];
        $sls_group = $_POST['PARAM_4'];
	   // $area = $_POST['PARAM_5'];
    	$folk = $_POST['PARAM_5'];
    	$currency = $_POST['PARAM_6'];
    	$no_zeros = $_POST['PARAM_7'];
    	$comments = $_POST['PARAM_8'];
	    $destination = $_POST['PARAM_9'];
	
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if ($fromcust == ALL_TEXT)
		$cust = _('All');
	else
		$cust = get_customer_name($fromcust);
    	$dec = user_price_dec();

	if ($area == ALL_NUMERIC)
		$area = 0;
		
	if ($payment_terms == 0)
		$payment_terms_name = _('All');
	else
		$payment_terms_name = get_payment_terms_names($payment_terms);
			
	if ($sls_group == 0)
		$sls_group_name = _('All');
	else
		$sls_group_name = get_sls_group($sls_group);
		
	if ($folk == ALL_NUMERIC)
		$folk = 0;

	if ($folk == 0)
		$salesfolk = _('All');
	else
		$salesfolk = get_salesman_name($folk);

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 80, 150, 170,  190, 240, 320, 360, 420, 475, 530, 600);

	$headers = array(_('No.'), _('Date/Narration'), _(''), _('Rate'), _('Qty'), _('Disc.'),
		_('Total'), 	_('Dr'), _('Cr'), _(''));

	$aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 	'to' => $to),
    				    2 => array('text' => _('Location'), 'from' => $cust, 'to' => ''),
    				    3 => array('text' => _('Payment Terms'), 'from' => $payment_terms_name, 'to' => ''),
    				    4 => array('text' => _('SLS Group'), 'from' => $sls_group_name, 'to' => ''),
    				    5 => array('text' => _('Cashier'), 'from' => $salesfolk, 'to' => ''),
    				    6 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						7 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));


    $rep = new FrontReport(_('POS Daily Sheet'), "POSDailySheet", user_pagesize());
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

		$sql .= " WHERE ".TB_PREF."cust_branch.inactive != 1";
		if ($fromcust != ALL_TEXT )
 		{
			$sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
		}
			
	/*	elseif ($dimension != 0 )
			
			{
	        	$sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
			}*/
	
	/*	elseif ($area != 0)
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
			}*/



	$sql .= " ORDER BY Name";	
	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;

	while ($myrow = db_fetch($result))
	{
//		if (!$convert && $currency != $myrow['curr_code']) continue;
		$accumulate = 0;
       	$deb_sum_total = array(0,0,0,0);	
		$deb_sum = array(0,0,0,0);	

		$bal = get_open_balance($myrow['DebtorNo'], $from, $convert);
		$init[0] = $init[1] = 0.0;
// 		$init[0] = round2(abs($bal['charges']), $dec);
// 		$init[1] = round2(Abs($bal['credits']), $dec);
		//$init[2] = round2($bal['OutStanding'], $dec);;
        // $init[0] = $init[1] = 0.0;
	    $init[2] = $init[0] - $init[1];
		$accumulate += $init[2];
		
		$res = get_transactions($myrow['DebtorNo'], $from, $to, 0, $dimension, $folk, $payment_terms, $sls_group);
		if ($no_zeros && db_num_rows($res) == 0) continue;

 		$num_lines++;
		$rep->fontSize += 2;
		$rep->Font('bold');		
		$rep->TextCol(0, 3, $myrow['Name']);
// 			$rep->fontSize -= 2;
		$rep->Font();		


		$rep->fontSize -= 2;
// 		$rep->TextCol(3, 5,	_("Open Balance"));
// 		$rep->AmountCol(7, 8, $init[0], $dec);
// 		$rep->AmountCol(8, 9, $init[1], $dec);
// 		$rep->AmountCol(9, 10, $init[2], $dec);
		
		$total =  $grandtotal = array(0,0,0,0);
		for ($i = 0; $i < 3; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		$rep->NewLine(1, 2);
		if (db_num_rows($res)==0)
			continue;
// 		$rep->Line($rep->row + 4);
		while ($trans = db_fetch($res))
		{
			if ($no_zeros && floatcmp($trans['TotalAmount']) == 0) continue;

		    $rep->Line($rep->row - 2); //new added
			$rep->NewLine(1, 2);
			
			$rep->Font('bold');
			$rep->TextCol(0, 1,	$trans['reference']);
			$rep->Font();
			
			$rep->DateCol(1, 2,	$trans['tran_date'], true);
//			if ($trans['type'] == ST_SALESINVOICE)
//				$rep->DateCol(3, 4,	$trans['due_date'], true);
			$item[0] = $item[1] = $item[3] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
			if ($trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
				$trans['TotalAmount'] *= -1;
//             $a = get_memo_($trans['type'], $trans['trans_no']);
// 			$rep->TextColLines(2, 3, _('') . " ". $a['memo_']);
// 				$rep->NewLine(-1);
			if ($trans['TotalAmount'] > 0.0)
			{
			    $foo = true;
				$a = 1;
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->TextCol(2, 6, $systypes_array[$trans['type']]);
				$rep->Font('bold');							
				$rep->AmountCol(7, 8, $item[0], $dec);
				$accumulate += $item[0];
				$rep->Font();		
				$rep->NewLine();	
				 
				$res2 = get_transactions2($myrow['DebtorNo'], $from, $to, $trans['trans_no'], $trans['type']);
							
				while ($trans2 = db_fetch($res2))
 				{				
    				$rep->NewLine();		
    				$str =  $trans2['description'];
    				if (strlen($str) > 15)
                    $str = substr($str, 0, 41).'...';
    				$rep->TextCol(0, 4, $str, $dec);
    				$rep->AmountCol(3, 4, $trans2['unit_price'], $dec);
    				$rep->AmountCol(4, 5, $trans2['quantity'], $dec);
    				$DiscountAmount = (($trans2['unit_price'] * $trans2['quantity']) * $trans2['discount_percent']);
    				$rep->TextCol(5, 6, $DiscountAmount." ".$trans2['text1'] , -2);
    				// $TotalAmount = (($trans2['unit_price'] * $trans2['quantity']) - $DiscountAmount);
    				$TotalAmount = ((1 - $trans2["discount_percent"]) * $trans2["unit_price"] * $trans2["quantity"]);
    				
    				$rep->AmountCol(6, 7, $TotalAmount, $dec);
    
    				$rep->NewLine(0.2);
    				$rep->TextCol(0, 11, _("................................................................................................................................................................................................................."), $dec);
                    // 		$rep->Line($rep->row - 2); //new added				
    				$deb_sum['0'] = $trans2['unit_price'];
    				$deb_sum['1'] = $trans2['quantity'];
    				$deb_sum['2'] = $DiscountAmount;
    				$deb_sum['3'] = $TotalAmount;
    
    				for ($i = 0; $i < 4; $i++)
    	    		{
        		 		$deb_sum_total[$i] += $deb_sum[$i];
        				$deb_sum_total_grandtotal[$i] += $deb_sum[$i];
    				}
				
               }//while	
			 
			}//if
			else
			{
				$rep->TextCol(2, 4, $systypes_array[$trans['type']]);
                /*if ($trans['type'] == ST_CUSTPAYMENT)
				    $item[1] = round2(Abs($trans['TotalAmount']) * $rate  -( $trans['service_disc'] + $trans['fbr_disc'] + $trans['srb_disc']+ $trans['supply_disc']), $dec);
                else*/
                    $item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
//				$rep->Font('bold');
                $rep->AmountCol(8, 9, $item[1], $dec);
                $accumulate -= $item[1];
//				$rep->Font();							
			} //else

            if ($trans['type'] == ST_CUSTPAYMENT)
            {
                if ($trans['supply_disc'] != 0 )
                    $wh1 = 'WHT on Supplies Amount: ' . $trans['supply_disc']." ";
                if ($trans['service_disc'] != 0)
                    $wh2 = 'WHT on Services Amount: '.$trans['service_disc']." ";
                if ($trans['fbr_disc'] != 0)
                    $wh3 = 'ST WH FBR Amount: '.$trans['fbr_disc']." ";
                if ($trans['srb_disc'] != 0)
                    $wh4 = 'ST WH SRB/PRA Amount: '.$trans['srb_disc']." ";
                    $rep->NewLine();
                    $rep->TextCollines(0, 9, $wh1.$wh2.$wh3.$wh4, -2);

            }
            if ($trans['discount1'] != 0)
            {
                $rep->NewLine();
                $rep->TextCol(0, 2, "Total Discount", -2);
                $rep->AmountCol(6, 7, $trans['discount1'], $dec);
            }
            if ($trans['ov_gst'] != 0)
            {
                $rep->NewLine();
                $rep->TextCol(0, 2, "GST 16%", -2);
                $rep->AmountCol(6, 7, $trans['ov_gst'], $dec);
            }
            $memo = get_comments_string($trans['type'], $trans['trans_no']);
            if ($memo != "")
            {
                $rep->NewLine();
                $rep->Font('bold');	
                $rep->TextColLines(0, 7,"MEMO : ".$memo, -2);
                $rep->Font('');	
            }

	/*if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT || $trans['type'] == ST_CPV)
				$item[3] = $item[0] + $item[1] - $item[2];
			else	
				$item[3] = $item[0] - $item[1] + $item[2];
*/

		//	$item[3] = round2($trans['Allocated'] * $rate, $dec);
		for ($i = 0; $i < 3; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}
			$rep->Font('bold');	
// 			$rep->AmountCol(9, 10, $accumulate, $dec);
			$rep->Font();						
		}

		$rep->Line($rep->row - 2);
		$rep->NewLine(2);
		$rep->Font('bold');

			$total[2] = $total[0] - $total[1];
		$rep->TextCol(0, 3, _('Total'));
		for ($i = 0; $i < 2; $i++)
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
	
	for ($i = 0; $i < 2; $i++)
	{
	
	//	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total_grandtotal[$i], $dec);
		$rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);
	}
	$rep->Font();				
	$rep->Line($rep->row  - 4);
	$rep->NewLine(2);
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT SUM(ov_amount+ov_gst-discount1) as GrossSales FROM 0_debtor_trans 
	        WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		    AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		    AND type = 10";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
    $rep->Font('bold');	
	$rep->TextCol(0, 3, _('Gross Sales'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['GrossSales'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT SUM(ov_gst) as SalesTax FROM 0_debtor_trans 
        WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
	    AND ".TB_PREF."debtor_trans.tran_date <= '$to'
	    AND type = 10";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Sales Tax 16%'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['SalesTax'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT SUM(ov_amount) as NetSales FROM 0_debtor_trans trans
        WHERE trans.tran_date >= '$from'
	    AND trans.tran_date <= '$to'
	    AND trans.type = 10";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Net Sales'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['NetSales'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT SUM(ov_amount+ov_gst-discount1) as CashSales FROM 0_debtor_trans 
        WHERE tran_date >= '$from'
	    AND tran_date <= '$to'
	    AND type = 10
	    AND payment_terms = 4";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Cash Sales'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['CashSales'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT SUM(ov_amount+ov_gst-discount1) as CreditCardSales FROM 0_debtor_trans 
        WHERE tran_date >= '$from'
	    AND tran_date <= '$to'
	    AND type = 10
	    AND payment_terms IN(5, 6, 8)";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Credit Card Sales'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['CreditCardSales'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT COUNT(*) as CashTransaction FROM 0_debtor_trans 
        WHERE tran_date >= '$from'
	    AND tran_date <= '$to'
	    AND type = 10
	    AND payment_terms = 4";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Cash Transaction'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['CashTransaction'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT COUNT(*) as CashCreditTransaction FROM 0_debtor_trans 
        WHERE tran_date >= '$from'
	    AND tran_date <= '$to'
	    AND type = 10
	    AND payment_terms IN(5, 6, 8)";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Credit Card Transaction'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['CashCreditTransaction'], $dec);
	$rep->Line($rep->row  - 2);
	$rep->NewLine();
	$sql = "SELECT COUNT(*) as TotalTransaction FROM 0_debtor_trans 
        WHERE tran_date >= '$from'
	    AND tran_date <= '$to'
	    AND type = 10";
	$query = db_query($sql, "Error");
	$fetch = db_fetch($query);
// 	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Total Transaction'));
// 	$rep->fontSize -= 2;
	$rep->AmountCol(7, 8, $fetch['TotalTransaction'], $dec);
	$rep->Line($rep->row  - 2);
	
	
	
    $rep->End();
}

?>