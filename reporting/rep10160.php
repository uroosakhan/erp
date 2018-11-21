<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Receipts
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

function get_reference_from($trans_type_to,$trans_no)
{
    $sql = "SELECT * FROM ".TB_PREF."cust_allocations
    	WHERE trans_type_from =".db_escape($trans_type_to) ." 
    	AND trans_type_to = 10 
    	AND trans_no_from = ".db_escape($trans_no);
    return db_query($sql,"No transactions were returned");

}
function get_customer_payment_ref($trans_no,$type)
{
    $sql = "SELECT reference FROM ".TB_PREF."refs 
    WHERE type = ".db_escape($type)."
	AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_cash_and_bank_acc($type_no, $ov_amount)
{
    $sql = "SELECT ".TB_PREF."gl_trans.account 
    FROM ".TB_PREF."gl_trans, ".TB_PREF."chart_master 
    WHERE ".TB_PREF."gl_trans.type = 0
	AND ".TB_PREF."gl_trans.type_no =".db_escape($type_no)."
	AND ".TB_PREF."gl_trans.account =".TB_PREF."chart_master.account_code
	AND ".TB_PREF."chart_master.account_type IN(1011,1012)
	AND ".TB_PREF."gl_trans.amount !=".db_escape($ov_amount);
    $result = db_query($sql, "error");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_transactions($debtorno, $from, $to)
{
	$from = date2sql($from);
	$to = date2sql($to);
    $sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst 
		+ ".TB_PREF."debtor_trans.supply_disc + ".TB_PREF."debtor_trans.service_disc +
		".TB_PREF."debtor_trans.fbr_disc + ".TB_PREF."debtor_trans.srb_disc +
		".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount -
		".TB_PREF."debtor_trans.discount1 - ".TB_PREF."debtor_trans.discount2)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type IN( ".ST_CUSTPAYMENT."
		,".ST_BANKDEPOSIT.", ".ST_CRV.", ".ST_JOURNAL.")
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
	    $area = $_POST['PARAM_3'];
    	$folk = $_POST['PARAM_4'];		
    	$currency = $_POST['PARAM_5'];
    	$summary = $_POST['PARAM_6'];
    	$comments = $_POST['PARAM_7'];
        $destination = $_POST['PARAM_8'];
	

	
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
		

	$cols = array(0, 50, 115, 160,  290, 310, 520);


	$headers = array(_('Date'), _('Reference'), _('Invoice'), _('Memo'), _(''), _(''));

	
	$aligns = array('left',	'left',	'left',	'left',	'left', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
    				    3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),						
    				    4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
    				    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => '')
						);


    $rep = new FrontReport(_('Customer Receipts Report'), "CustomerReceipts", user_pagesize());
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	$deb_sum_total_grandtotal = array(0,0,0,0);
	$a = 0;


	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
			".TB_PREF."debtors_master.name AS Name,".TB_PREF."debtors_master.inactive
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
		

	$sql .= " GROUP BY DebtorNo ORDER BY Name";	
	$result = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;
    $total = array(0,0,0,0);
    $amt = array(0,0,0,0);
    $amt1 = array(0,0,0,0);
	while ($myrow = db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code']) continue;

       	$deb_sum_total = array(0,0,0,0);	
		$deb_sum = array(0,0,0,0);	

		$res = get_transactions($myrow['DebtorNo'], $from, $to);
		if (db_num_rows($res) == 0) continue;


 		$num_lines++;
		$rep->fontSize += 2;
		$rep->Font('bold');		
		$rep->TextCol(0, 3, $myrow['Name']);
		$rep->Font();		


		$rep->fontSize -= 2;
		$rep->TextCol(3, 5,	_(""));
		


		//$rep->NewLine(1, 2);
		if (db_num_rows($res)==0)
			continue;
		//$rep->Line($rep->row + 4);
		while ($trans = db_fetch($res))
		{
			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
            $jv = get_cash_and_bank_acc($trans['trans_no'],$trans['TotalAmount']);

            if($jv == '')
                continue;
			if(!$summary)
		   {
			$rep->NewLine();

               if($trans['type'] != 0) {
                   $rep->DateCol(0, 1,	$trans['tran_date'], true);
                   $rep->TextCol(1, 2, $trans['reference']);
               }
			
		  }
			$item[0] = $item[1] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
				
			if(!$summary)
           {

               if ($trans['supply_disc'] != 0 )
                    $wh1 = 'WHT on Supplies Amount: ' . $trans['supply_disc']." ";
                if ($trans['service_disc'] != 0)
                    $wh2 = 'WHT on Services Amount: '.$trans['service_disc']." ";
                if ($trans['fbr_disc'] != 0)
                    $wh3 = 'ST WH FBR Amount: '.$trans['fbr_disc']." ";
                if ($trans['srb_disc'] != 0)
                    $wh4 = 'ST WH SRB/PRA Amount: '.$trans['srb_disc']." ";
            	$memo = get_comments_string($trans['type'], $trans['trans_no']);
				if ($memo != "" )
                    $memo = $memo;
               if($trans['type'] != 0) {
                   $rep->TextCol(3, 7, $memo . $wh1 . $wh2 . $wh3 . $wh4, -2);
               }
               $item[1] = round2(($trans['TotalAmount']) * $rate, $dec);
//


               if($trans['type'] == 0)
               {
                   $rep->DateCol(0, 1,	$trans['tran_date'], true);
                   $rep->TextCol(1, 2, $trans['reference']);
//                   $rep->TextCol(2, 3, " ".$jv);
                   $rep->AmountCol(5, 6, (-1*$item[1]), 0);
                   $rep->TextCol(3, 7, $memo . $wh1 . $wh2 . $wh3 . $wh4, -2);

                   $amt[1] +=(-1*$item[1]);
               }
               else{
                   $rep->AmountCol(5, 6, $item[1], 0);
                   $amt1[1] +=$item[1];
               }
               $transaction_from = get_reference_from($trans['type'], $trans['trans_no']);
               while($get_ref = db_fetch($transaction_from))
               {

                   $reference_ = get_reference($get_ref['trans_type_to'], $get_ref['trans_no_to']);
                   $rep->TextCol(2, 3,	$reference_);
                   $rep->NewLine();

               }
           }
		}
        $total[1] += $amt[1] + $amt1[1];
        $grandtotal[1] += $amt[1] + $amt1[1];
		$rep->Line($rep->row - 2);

        		if(!$summary)
        		$rep->NewLine();
		    
        		$rep->Font('bold');

                if(!$summary)
                {
    		    $rep->TextCol(0, 3, _('Total'));
                }
    			$rep->AmountCol(5, 6, $total[1], 0);

    			$rep->Font();					
        
                if(!$summary)
        		$rep->NewLine(2);
        		else
        		 $rep->NewLine(1.2);

		    	
	}
	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Grand Total'));

	
	$rep->AmountCol(5, 6, $grandtotal[1], 0); 
    $rep->Font();				
	$rep->fontSize -= 2;

	//$rep->Line($rep->row  - 4);
	$rep->NewLine();
    	$rep->End();
}

?>