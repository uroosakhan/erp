
application/x-httpd-php rep1016.php ( PHP script text )
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

function get_transactions($debtorno, $from, $to,$dimension,$dimension2, $invoice_salesman, $invoice_customer)
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
 			AND trans.type = ".ST_SALESINVOICE."
 			AND ISNULL(voided.id)
     	";


    if ($debtorno != 0 )
    {
 		$sql .= " AND trans.debtor_no = ".db_escape($debtorno);
    }
    if ($invoice_salesman != 0 )
    {
 		$sql .= " AND trans.salesman = ".db_escape($invoice_salesman);
    }
    if ($invoice_customer != 0 )
    {
 		$sql .= " AND trans.debtor_no = ".db_escape($invoice_customer);
    }
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
    	$group_by = $_POST['PARAM_2'];
    	$fromcust = $_POST['PARAM_3'];
	    $area = $_POST['PARAM_4'];
    	$folk = $_POST['PARAM_5'];		
    	$currency = $_POST['PARAM_6'];
    	$summary = $_POST['PARAM_7'];
    	$suppres = $_POST['PARAM_8'];
    	$comments = $_POST['PARAM_9'];
        $destination = $_POST['PARAM_10'];
	

	
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
		

	$cols = array(0, 60, 140, 290,  360, 440, 520);
    
    if($group_by==0 || $group_by==2)
	$headers = array(_('Date'), _('Reference'), _(''), _('Amount'), _('Received'), _('Balance'));
   if($group_by==1)
	$headers = array(_('Date'), _('Reference'), _('Customer Name'), _('Amount'), _('Received'), _('Balance'));
    
	$aligns = array('left',	'left',	'left',	'right',	'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
    				    3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),						
    				    4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
    				    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => '')
						);


    $rep = new FrontReport(_('Customer Invoices Report'), "CustomerInvoices", user_pagesize());
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	$deb_sum_total_grandtotal = array(0,0,0,0);
	$a = 0;
	

if ($group_by==0)
{
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
	$customer_name = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;			
}
if ($group_by==1) //salesman by invoice
{
   	$sql = "SELECT `salesman_code`,`salesman_name` 
   	FROM ".TB_PREF."salesman";

	if ($folk != 0)
		$sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);

	$sql .= " GROUP BY salesman_code ORDER BY salesman_name";	
	$salesman_inv_name = db_query($sql, "The salesman could not be retrieved");
	$num_lines = 0;		  

}
if ($group_by==2) //salesman by setup
{
   	$sql = "SELECT `salesman_code`,`salesman_name` 
   	FROM ".TB_PREF."salesman";

	$sql .= " GROUP BY salesman_code ORDER BY salesman_name";	
	$salesman_setup_name = db_query($sql, "The salesman could not be retrieved");
	
	
}

        if ($group_by==0)
        $result1 = $customer_name;
        if ($group_by==1)
        $result1 = $salesman_inv_name;
        if ($group_by==2)
        $result1 = $salesman_setup_name;


    if ($group_by==2)//for salesman/setup
	while ($salesman_row = db_fetch($result1))
    {
	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
			".TB_PREF."debtors_master.name AS Name,".TB_PREF."debtors_master.inactive, ".TB_PREF."salesman.salesman_name
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

		$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($salesman_row['salesman_code']);
					
	if ($folk != 0)
		$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);

	if ($area != 0)
		$sql .= " AND ".TB_PREF."areas.area_code=".db_escape($area);
		

	$sql .= " GROUP BY DebtorNo ORDER BY Name";	
	$result2 = db_query($sql, "The customers could not be retrieved");
	$num_lines = 0;		  

    		if (db_num_rows($result2)==0) continue;
        		$rep->fontSize += 2;
        		$rep->Font('bold');	
        		$rep->TextCol(0, 3, $salesman_row['salesman_name']);
        		$rep->fontSize -= 2;
        		
            	$rep->NewLine();

        if ($group_by==2)
        $result3 = $result2;

        
    	while ($myrow = db_fetch($result3))
    	{
    		if (!$convert && $currency != $myrow['curr_code']) continue;
    
           	$deb_sum_total = array(0,0,0,0);	
    		$deb_sum = array(0,0,0,0);	
    
    
    
    
    
    		$res = get_transactions($myrow['DebtorNo'], $from, $to,$dimension,$dimension2, $myrow['salesman_code']);
    		if (db_num_rows($res) == 0) continue;
    
     		$num_lines++;
    		$rep->fontSize += 2;
    		$rep->Font('bold');	
    		if ($group_by==1)
    		$rep->TextCol(0, 3, $myrow['salesman_name']);
    		if($suppres!=1)
    		if ($group_by==0 || $group_by==2 )
    		$rep->TextCol(0, 3, $myrow['Name']);
    		$rep->Font();		
    
    
    		$rep->fontSize -= 2;
    		$rep->TextCol(3, 5,	_(""));
    		
    		$total = array(0,0,0,0);
    		$alloc = 0;
    		$balance = 0;
    	
    		//$rep->NewLine(1, 2);
    		if (db_num_rows($res)==0)
    			continue;
    		//$rep->Line($rep->row + 4);
    		while ($trans = db_fetch($res))
    		{
    			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
    
    			if(!$summary)
    		   {
    			$rep->NewLine();
    			$rep->DateCol(0, 1,	$trans['tran_date'], true);
    			$rep->TextCol(1, 2,	$trans['reference']);
    		if ($group_by==1)
    			$rep->TextCol(2, 3,	get_customer_name($trans['debtor_no']));
    			
    		  }
    			$item[0] = $item[1] = 0.0;
    			if ($convert)
    				$rate = $trans['rate'];
    			else
    				$rate = 1.0;
    				
                
    				$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
                    $item[2] = round2(Abs($trans['alloc']) * $rate, $dec) ;
                    $invoice_balance = $item[1] - $item[2];
                    
  
                    if(!$summary)
                    {
                	$rep->AmountCol(3, 4, $item[1], 0); 
                	$rep->AmountCol(4, 5, $item[2], 0); 
                	$rep->AmountCol(5, 6, $invoice_balance, 0); 
                    }
    
    				$total[1] += $item[1];
    				$alloc += $item[2];
    				$balance += $invoice_balance;
    
    				$grandtotal[1] += $item[1];
    				$grandalloc += $item[2];
    				$grandbalance += $invoice_balance;
    
    		}
    
    		$rep->Line($rep->row - 2);
    
            		if(!$summary)
            		$rep->NewLine();
    		    
            		$rep->Font('bold');
    
                    if(!$summary)
                    {
        		   // $rep->TextCol(0, 3, _('Total'));
                    }
        			$rep->AmountCol(3, 4, $total[1], 0); 
        			$rep->AmountCol(4, 5, $alloc, 0); 
        			$rep->AmountCol(5, 6, $balance, 0); 
    
        			$rep->Font();					
            
                    if(!$summary)
            		$rep->NewLine(2);
            		else
            		 $rep->NewLine(1.2);
    
    		    	
    	}
    }


    if ($group_by!=2)
    {

        if ($group_by==0)
        $result3 = $customer_name;
        if ($group_by==1)
        $result3 = $salesman_inv_name;


    		
    	while ($myrow = db_fetch($result3))
    	{
    		if (!$convert && $currency != $myrow['curr_code']) continue;
    
    
    
  
    
           	$deb_sum_total = array(0,0,0,0);	
    		$deb_sum = array(0,0,0,0);	
    
    
    
        if ($group_by==0)//for customers
    		$res = get_transactions($myrow['DebtorNo'], $from, $to,$dimension,$dimension2, $myrow['salesman_code']);

        if ($group_by==1)//for salesman invoice
    		$res = get_transactions($myrow['DebtorNo'], $from, $to,$dimension,$dimension2, $myrow['salesman_code'], $fromcust);
    		
    		if (db_num_rows($res) == 0) continue;
    
     		$num_lines++;
    		$rep->fontSize += 2;
    		$rep->Font('bold');	
    // 		display_error($group_by);
    		if ($group_by==1)
    		$rep->TextCol(0, 3, $myrow['salesman_name']);
    		
    		
    		
    
    		
    		
    		if($suppres!=1)
    		if ($group_by==0 || $group_by==2)
    		$rep->TextCol(0, 3, $myrow['Name']);
    		$rep->Font();		
    
    
    		$rep->fontSize -= 2;
    		$rep->TextCol(3, 5,	_(""));
    		
    		$total = array(0,0,0,0);
    		$alloc = 0;
    		$balance = 0;
    	
    		//$rep->NewLine(1, 2);
    		if (db_num_rows($res)==0)
    			continue;
    		//$rep->Line($rep->row + 4);
    		$debtor_no = '';
    		while ($trans = db_fetch($res))
    		{
    			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
    			
    			
    		
    			
                   // if(!$suppres)
                   // if ($invoice_balance== 0) continue;
    
    			if(!$summary && $suppres!=1)
    		   {
    			$rep->NewLine();
    			$rep->DateCol(0, 1,	$trans['tran_date'], true);
    			$rep->TextCol(1, 2,	$trans['reference']);
    		if ($group_by==1)
    			$rep->TextCol(2, 3,	get_customer_name($trans['debtor_no']));
    			
    		  }
    	
    		
    			$item[0] = $item[1] = 0.0;
    			if ($convert)
    				$rate = $trans['rate'];
    			else
    				$rate = 1.0;

                          	$item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
                    $item[2] = round2(Abs($trans['alloc']) * $rate, $dec) ;
                    $invoice_balance = $item[1] - $item[2];  

                    if(!$summary)
                    {
                    if($suppres==1 && $invoice_balance==0)continue;
                    if($suppres==1)
                    {
                        if($debtor_no != $trans['debtor_no'])
                        {
                            $rep->font('b');
                        	  $rep->TextCol(0, 3,get_customer_name(	$trans['debtor_no']));
                        $rep->font('');
                            
                        }
                    $rep->NewLine();
                    $rep->DateCol(0, 1,	$trans['tran_date'], true);
    			    $rep->TextCol(1, 2,	$trans['reference']);
    			  
                    $rep->AmountCol(3, 4, $item[1], 0); 
                	$rep->AmountCol(4, 5, $item[2], 0); 
                	$rep->AmountCol(5, 6, $invoice_balance, 0);
                	$debtor_no = $trans['debtor_no'];
                    }
                    else
                    {
                    $rep->AmountCol(3, 4, $item[1], 0); 
                	$rep->AmountCol(4, 5, $item[2], 0); 
                	$rep->AmountCol(5, 6, $invoice_balance, 0); 
                    }
                
                    }
    
    				$total[1] += $item[1];
    				$alloc += $item[2];
    				$balance += $invoice_balance;
    
    				$grandtotal[1] += $item[1];
    				$grandalloc += $item[2];
    				$grandbalance += $invoice_balance;
    
    		}
    
    		$rep->Line($rep->row - 2);
    
            	//	if(!$summary)
            	//	$rep->NewLine();
    		    
            		$rep->Font('bold');
    
                   
                    if($suppres==1 && $balance==0)continue;
                    if($suppres==1 && $summary==1)
                    {
                        $rep->NewLine();
                        if($summary!=1)
                        $rep->TextCol(0, 3, _('Total'));
                    $rep->TextCol(0, 3, $myrow['Name']);
        			$rep->AmountCol(3, 4, $total[1], 0); 
        			$rep->AmountCol(4, 5, $alloc, 0); 
        			$rep->AmountCol(5, 6, $balance, 0); 
                    }
                   else
                   {
                       
                       $rep->NewLine();
                       if($suppres==1 && $summary==1)
                       $rep->TextCol(0, 3, _('Total'));
                       	$rep->AmountCol(3, 4, $total[1], 0); 
        			$rep->AmountCol(4, 5, $alloc, 0); 
        			$rep->AmountCol(5, 6, $balance, 0);
        			
                   }
    
        			$rep->Font();					
            
            //         if(!$summary)
            // 		$rep->NewLine(2);
            // 		else
            // 		 $rep->NewLine(1.2);
    
    		    	
    	}
    }    
    $rep->NewLine(2);
	$rep->fontSize += 2;
	$rep->Font('bold');		
	$rep->TextCol(0, 3, _('Grand Total'));

	
	$rep->AmountCol(3, 4, $grandtotal[1], 0); 
	$rep->AmountCol(4, 5, $grandalloc, 0); 
	$rep->AmountCol(5, 6, $grandbalance, 0); 

    $rep->Font();				
	$rep->fontSize -= 2;

	//$rep->Line($rep->row  - 4);
	$rep->NewLine();
    	$rep->End();
        	
        	
}

?>