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


function get_transactions($debtorno, $from, $to, $location)
{
	$from = date2sql($from);
	$to = date2sql($to);

	$sql = "SELECT ".TB_PREF."debtor_trans.*,
	(".TB_PREF."debtor_trans.ov_amount +".TB_PREF."debtor_trans.supply_disc + ".TB_PREF."debtor_trans.gst_wh+
	".TB_PREF."debtor_trans.service_disc + ".TB_PREF."debtor_trans.fbr_disc +
	".TB_PREF."debtor_trans.srb_disc+".TB_PREF."debtor_trans.ov_gst +
	".TB_PREF."debtor_trans.ov_freight +".TB_PREF."debtor_trans.ov_freight_tax +
	".TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1 -
	".TB_PREF."debtor_trans.discount2) AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated";

		$sql .= ", ((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue";
	$sql .=  " FROM ".TB_PREF."debtor_trans,".TB_PREF."debtor_trans_details
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		
	AND ".TB_PREF."debtor_trans.trans_no = ".TB_PREF."debtor_trans_details.debtor_trans_no
			
		AND ".TB_PREF."debtor_trans.type = ".TB_PREF."debtor_trans_details.debtor_trans_type
				
	AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY;
	if($location !=''){
		$sql .=" AND ".TB_PREF."debtor_trans_details.item_location=".db_escape($location)."";
	}
	$sql .=  " GROUP BY  ".TB_PREF."debtor_trans.reference  ORDER BY ".TB_PREF."debtor_trans.tran_date";


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


//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
    	$fromcust = $_POST['PARAM_2'];
    	$dimension = $_POST['PARAM_3'];
	    $area = $_POST['PARAM_4'];
    	$folk = $_POST['PARAM_5'];
	    $location = $_POST['PARAM_6'];
    	$currency = $_POST['PARAM_7'];
    	$no_zeros = $_POST['PARAM_8'];
    	$comments = $_POST['PARAM_9'];
	    $orientation = $_POST['PARAM_10'];
    	$destination = $_POST['PARAM_11'];
	
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

	if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);

	if ($folk == 0)
		$salesfolk = _('All Sales Man');
	else
		$salesfolk = get_salesman_name($folk);
		
	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

	$cols = array(0, 50, 150, 260,  310, 301, 340, 375, 430, 460, 530, 600);

	$headers = array(_('Date'), _('Particular'), _('Memo'), _('Qty'), _('Item'), _(''), _(''),
			_('Dr'), _('Cr'), _('Balance'));

	$aligns = array('left',	'left',	'left',	'left',	'left', 'left', 'left', 'left', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
    				    3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),						
    				    4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
    				    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						6 => array('text' => _('Suppress Zeros'), 'from' > $nozeros, 'to' => ''));


    $rep = new FrontReport(_('Customer Balances Detailed 2'), "CustomerBalancesDetailed2", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	$deb_sum_total_grandtotal = array(0,0,0,0);
	$a = 0;
	


 $current_user = $_SESSION["wa_current_user"]->user;

		$sql .="SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
		".TB_PREF."debtors_master.debtor_ref AS Name,
		".TB_PREF."debtors_master.curr_code,".TB_PREF."debtors_master.inactive 
		FROM ".TB_PREF."debtors_master
   
		INNER JOIN ".TB_PREF."cust_branch ON 
		".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no

		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			

		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code

		INNER JOIN ".TB_PREF."user_locations ON 
		".TB_PREF."cust_branch.default_location=".TB_PREF."user_locations.loc_code
        
		WHERE ".TB_PREF."user_locations.user_id=$current_user
       ";

		if ($fromcust != ALL_TEXT )
			{
//				if ($area != 0 || $folk != 0) continue;
				$sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
			}
			
    	elseif ($dimension != 0 )
			
			{
		$sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
			}
	
		elseif ($area != 0)
			{
				if ($folk != 0)
					$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code =".db_escape($area);
				else
					$sql .= " AND ".TB_PREF."areas.area_code =".db_escape($area);
			}			
		elseif ($folk != 0 )
			{
				$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
			}			
		

	$sql .= " GROUP BY Name ORDER BY Name";	
	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;			

	while ($myrow = db_fetch($result))
	{
//		if (!$convert && $currency != $myrow['curr_code']) continue;

       	$deb_sum_total = array(0,0,0,0);	
		$deb_sum = array(0,0,0,0);	

		$bal = get_open_balance($myrow['DebtorNo'], $from, $convert);
		$init[0] = $init[1] = 0.0;
		$init[0] = round2(abs($bal['charges']), $dec);
		$init[1] = round2(Abs($bal['credits']), $dec);
		//$init[2] = round2($bal['OutStanding'], $dec);;
		$init[2] = $init[0] - $init[1];
		$res = get_transactions($myrow['DebtorNo'], $from, $to,$location);
		if ($no_zeros && db_num_rows($res) == 0) continue;

 		$num_lines++;
		$rep->fontSize += 2;
		$rep->Font('bold');		
		$rep->TextCol(1, 2, $myrow['Name']);
		$rep->Font();


		$rep->fontSize -= 2;
		$rep->TextCol(3, 5,	_("Open Balance"));
		$rep->AmountCol(7, 8, $init[0], $dec);
		$rep->AmountCol(8, 9, $init[1], $dec);
		$rep->AmountCol(9, 10, $init[2], $dec);
		$total =  $grandtotal = array(0,0,0,0);
		for ($i = 0; $i < 4; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		$rep->NewLine(1, 2);
		if (db_num_rows($res)==0)
			continue;
		$rep->Line($rep->row + 4);
		while ($trans = db_fetch($res))
		{
			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
			
		  
			$rep->NewLine(1, 2);
			

			$rep->Font('bold');
//			$rep->TextCol(2, 3,	$trans['reference']);
			$rep->Font();
			
			
			$rep->DateCol(0, 1,	$trans['tran_date'], true);

			$item[0] = $item[1] = $item[3] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
			if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
				$trans['TotalAmount'] *= -1;

	
				
			if ($trans['TotalAmount'] > 0.0)
			{
			    $foo = true;
				$a = 1;
				$item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
				$rep->TextCol(1, 2, $systypes_array[$trans['type']]." ".$trans['reference']);
				$rep->Font('bold');							
				$rep->AmountCol(7, 8, $item[0], $dec);
				$rep->Font();				
				
				$res2 = get_transactions2($myrow['DebtorNo'], $from, $to, $trans['trans_no']);	
							
				while ($trans2 = db_fetch($res2))
		
 				{				
			//	$rep->NewLine();					
				$rep->TextCol(4, 5, $trans2['stock_id'], $dec);
			//	$rep->AmountCol(4, 5, $trans2['unit_price'], $dec);
				$rep->AmountCol(3, 4, $trans2['quantity'], $dec);
                    $rep->NewLine();
				$DiscountAmount= (($trans2['unit_price'] * $trans2['quantity']) - $trans2['discount_percent']);
				
				
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
				$rep->TextCol(1, 4, $systypes_array[$trans['type']]);
				$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
//				$rep->Font('bold');							
				$rep->AmountCol(8, 9, $item[1], $dec);
//				$rep->Font();							
			} //else
		
			$memo = get_comments_string($trans['type'], $trans['trans_no']);
			if ($memo != "")
			{
				//$rep->NewLine();
				$rep->TextCol(2, 3, $memo, -2);
			}
//		
			$item[2] = $item[0] - $item[1] ;


		for ($i = 0; $i < 3; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			}

		// For loop for adding running balance column
			$rep->Font('bold');	
			for ($i = 2; $i < 3; $i++)
			$rep->AmountCol(9 ,10, $total[$i], $dec); //Balance
			$rep->Font();						
		
		}



		$rep->Line($rep->row - 2);
		$rep->NewLine(2);
		$rep->Font('bold');
		//------------------------
	//	 for ($i = 0; $i < 4; $i++)
	//	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total[$i], $dec);			
		//------------------------

		$total[2] = $total[0] - $total[1];


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


//	$rep->MultiCell(60, 30,"Item", 0, 'L', 0, 2, 390,200, true); // 3

	$grandtotal[2] = $grandtotal[0] - $grandtotal[1];
	for ($i = 0; $i < 3; $i++)
	{

	//	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total_grandtotal[$i], $dec);
		$rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);
	}
	$rep->Font();				
	$rep->Line($rep->row  - 4);
	$rep->NewLine();
    	$rep->End();
}

?>