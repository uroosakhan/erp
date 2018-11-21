<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Outstanding Summary Report
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
     	(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
     	
     	
     $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
     	(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * (
     	    IF (type=".ST_JOURNAL." && ov_amount < 0, -1, 1)
     	    ), 0)) AS credits,";
     	
     	
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0), t.alloc * -1, t.alloc)) AS Allocated,";

 	$sql .=	"SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			((t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
 			
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_credit_note_amount($debtorno, $from, $to, $branch_code){
     $from = date2sql($from);
    $to = date2sql($to);
    $sql =" SELECT SUM(ov_amount)  FROM ".TB_PREF."debtor_trans t
     WHERE debtor_no = ".db_escape($debtorno)."
      AND t.branch_code = '$branch_code'
      AND type = 11
     AND (month_ != 0 OR f_year != '')
       ";
    $sql .= " AND t.tran_date >= '$from'";
    $sql .= " AND t.tran_date <= '$to'";
    $result = db_query($sql,"No transactions were returned");
    $row = db_fetch_row($result);
    return $row[0];
}
//////////////credit note
function get_credit_note_service($debtorno, $from, $to, $branch_code)
{

    $from = date2sql($from);
    $to = date2sql($to);

    $sql = " SELECT 
    SUM(IF(t.type = ".ST_CUSTCREDIT." AND s.mb_flag = 'D' AND t.tax_included = 1 , (
    ( (d.unit_price*d.quantity) )
     ), 0)) 
     
    	FROM ".TB_PREF."debtor_trans t
	
    LEFT JOIN ".TB_PREF."debtor_trans_details d
		ON d.debtor_trans_type  =  t.type
		AND d.debtor_trans_no =  t.trans_no 
	LEFT JOIN ".TB_PREF."stock_master s
			ON d.stock_id  =  s.stock_id
	WHERE t.debtor_no = ".db_escape($debtorno)." 

	AND t.branch_code = '$branch_code'
	AND t.type <> ".ST_CUSTDELIVERY;

    $sql .= " AND t.tran_date >= '$from'";
    $sql .= " AND t.tran_date <= '$to'";

    $sql .= " GROUP BY t.debtor_no";


   $result = db_query($sql,"No transactions were returned");
    $row = db_fetch_row($result);
    return $row[0];  
}

function get_trans2($debtorno, $from, $to, $branch_code)
{

    $from = date2sql($from);
    $to = date2sql($to);

    $sql = " SELECT 
    SUM(IF(t.type = ".ST_CUSTCREDIT." AND s.mb_flag = 'D' AND t.tax_included = 1 , (
    ( (d.unit_price*d.quantity) )
     ), 0)) AS credit_note_tax_incl,


    SUM(IF(t.type = ".ST_CUSTCREDIT." AND s.mb_flag != 'D'  AND t.tax_included = 1, (
        ( (d.unit_price*d.quantity)) 
        ), 0)) AS sales_return_tax_incl,

    SUM(IF(t.type = ".ST_CUSTCREDIT." AND s.mb_flag = 'D'  AND t.tax_included = 0, (
    ( (d.unit_price*d.quantity) + (d.unit_tax*d.quantity) )
     ), 0)) AS credit_note,


    SUM(IF(t.type = ".ST_CUSTCREDIT." AND s.mb_flag != 'D' AND t.tax_included = 0 , (
        ( (d.unit_price*d.quantity) + (d.unit_tax*d.quantity) ) 
        ), 0)) AS sales_return	

    	FROM ".TB_PREF."debtor_trans t
	
    LEFT JOIN ".TB_PREF."debtor_trans_details d
		ON d.debtor_trans_type  =  t.type
		AND d.debtor_trans_no =  t.trans_no 
		
	LEFT JOIN ".TB_PREF."stock_master s
			ON d.stock_id  =  s.stock_id

	WHERE t.debtor_no = ".db_escape($debtorno)." 

	AND t.branch_code = '$branch_code'
	AND t.type <> ".ST_CUSTDELIVERY;

    $sql .= " AND t.tran_date >= '$from'";
    $sql .= " AND t.tran_date <= '$to'";

    $sql .= " GROUP BY t.debtor_no";


    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);    
   
 


}

function get_trans($debtorno, $from, $to, $branch_code)
{
	
	$from = date2sql($from);
	$to = date2sql($to);


    $sql = "SELECT SUM(IF(t.type = ".ST_BANKPAYMENT." OR t.type = ".ST_CPV.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS refunds,	
    	
    SUM(IF(t.type = ".ST_JOURNAL.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS journal,
    	
   SUM(IF(t.type = ".ST_SALESINVOICE."  ,(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + 
 		t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0) )
 		AS sales, 	
 		
   SUM(IF(t.type = ".ST_CUSTPAYMENT."  OR t.type = ".ST_BANKDEPOSIT." OR t.type = ".ST_CRV.",(t.ov_amount + t.ov_gst +
      supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax +
       t.ov_discount - t.discount1 - t.discount2), 0)) AS customer_receipts,
   
   SUM(t.alloc) AS Allocated,
   SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			(abs(t.ov_amount + t.ov_gst + t.supply_disc + t.service_disc + t.fbr_disc +  t.srb_disc +
 			 t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - 
 			 abs(t.alloc))) AS OutStanding, t.tax_included AS tax_included
 	FROM ".TB_PREF."debtor_trans t

		WHERE t.debtor_no = ".db_escape($debtorno)." 
        AND t.ov_amount!= 0
		AND t.branch_code = '$branch_code'
		AND t.type <> ".ST_CUSTDELIVERY;


    	$sql .= " AND t.tran_date >= '$from'";
    	$sql .= " AND t.tran_date <= '$to'";

	$sql .= " GROUP BY t.debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to)
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
 		((trans.type = ".ST_SALESINVOICE.")	AND trans.due_date < '$to') AS OverDue, trans.ov_gst AS ov_gst
     	FROM ".TB_PREF."debtor_trans trans
 			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
 			LEFT JOIN $allocated_from ON alloc_from.trans_type = trans.type AND alloc_from.trans_no = trans.trans_no
 			LEFT JOIN $allocated_to ON alloc_to.trans_type = trans.type AND alloc_to.trans_no = trans.trans_no

     	WHERE trans.tran_date >= '$from'
 			AND trans.tran_date <= '$to'
 			AND trans.debtor_no = ".db_escape($debtorno)."
 			AND trans.type <> ".ST_CUSTDELIVERY."
 			AND ISNULL(voided.id)
     	ORDER BY trans.tran_date";
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




function get_customer_name_data($fromcust, $area, $branch_code, $folk, $groups, $dimension)
{
	$sql = "SELECT ".TB_PREF."debtors_master.debtor_no,
			".TB_PREF."debtors_master.name,
			".TB_PREF."debtors_master.debtor_ref,
			".TB_PREF."debtors_master.curr_code,
			".TB_PREF."cust_branch.br_name,
			".TB_PREF."cust_branch.branch_code,
			".TB_PREF."cust_branch.area,
			".TB_PREF."cust_branch.group_no
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code


			WHERE ".TB_PREF."debtors_master.debtor_no != 0

";
		if ($fromcust != ALL_TEXT )
			{
			$sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
			}

		if ($branch_code != 0)
			{
			$sql .= " AND ".TB_PREF."cust_branch.branch_code=".db_escape($branch_code);
			}
			
		elseif ($dimension != 0 )

		{
			$sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
		}

		elseif ($area != 0)
			{
				if ($folk != -1 )
				{
					$sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code=".db_escape($area);
				}

				else
				{
					$sql .= " AND ".TB_PREF."areas.area_code=".db_escape($area);
				//	$sql .= " AND ".TB_PREF."salesman.salesman_code= ".db_escape($folk);
				}

			}
		elseif ($folk != -1)
			{
				$sql .= " AND ".TB_PREF."salesman.salesman_code= ".db_escape($folk);
			}
		elseif ($groups != 0)
			{
				$sql .= " AND ".TB_PREF."cust_branch.group_no= ".db_escape($groups);
			}

		$sql .= "  ORDER BY Name";
		return db_query($sql, "The customers could not be retrieved");
}
function area_loop()
{
	$sql = "SELECT * FROM ".TB_PREF."areas";
	 return db_query($sql,"No transactions were returned");
}

function get_area($id)
{
    $sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}


//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
	global $path_to_root;

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$fromcust = $_POST['PARAM_2'];
	$branch_code = $_POST['PARAM_3'];
	$dimension = $_POST['PARAM_4'];
	$area = $_POST['PARAM_5'];
	$folk = $_POST['PARAM_6'];
	$groups = $_POST['PARAM_7'];
	$currency = $_POST['PARAM_8'];
	$no_zeros = $_POST['PARAM_9'];
	$comments = $_POST['PARAM_10'];
	$destination = $_POST['PARAM_11'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ('L');

	if ($fromcust == ALL_TEXT)
		$cust = _('All');
	else
		$cust = get_customer_name($fromcust);
//    	$dec = user_price_dec();
    	$dec = 0;

	if ($folk == -1)
		$folk_name = _('All');
	else
		$folk_name = get_salesman_name($folk);

	if ($area == -1)
			$area_name = _('All');
		else
			$area_name = get_area_name($area);

	if ($groups == 0)
		$groups_name = _('All');
	else
		$groups_name = get_sales_group_name($groups);

	if ($currency == ALL_TEXT)
	{
		$convert = true;
		$currency = _('Balances in Home Currency');
	}
	else
		$convert = false;

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');


    $cols = array(0, 25, 200, 250, 310, 360, 420, 470, 530, 580, 640, 700, 760);
	$headers = array(_('S.'), _('Customer Name '), _('Group'), _('City'), _('Opening '), _('Sales'), _('Sales'),
		_('Customer'),_('Customer'),_('Credit'),_('Journal'),_('Balance'));
	$headers2 = array(_('No'), _(''), _(''), _(''), _('Balance'), _('Invoices'), _('Return'), _('Receipts'),_('Refunds'),_('Note'),_('Entries'),
		_(''));
	$aligns = array('left',	'left', 'left',	'left',	'right','right', 'right', 'right', 'right', 'right', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
    				    3 => array('text' => _('Area'), 'from' => $area_name, 'to' => ''),
    				    4 => array('text' => _('Salesman'), 'from' => $folk_name, 'to' => ''),
    				    5 => array('text' => _('Groups'), 'from' => $groups_name, 'to' => ''),
    				    6 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						7 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Customer Outstanding Summary'), "CustomerOutstandingSummary", user_pagesize(),9, $orientation);

    $rep->Font();
	$rep->Info($params, $cols, $headers2, $aligns, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	
	$num_lines = 0;

	$result = get_customer_name_data($fromcust, $area, $branch_code, $folk, $groups, $dimension );
$total_cust_balance=0;
$total_bal=0;
	while ($myrow = db_fetch($result))
	{

		if (!$convert && $currency != $myrow['curr_code']) continue;

global  $db_connections;
		$bal1 = get_open_balance($myrow['debtor_no'], $from, $myrow['branch_code']);
		$ob = ($bal1['charges'] -$bal1['credits']);

		$bal = get_trans($myrow['debtor_no'], $from, $to, $myrow['branch_code']);
		
		$bal2 = get_trans2($myrow['debtor_no'], $from, $to, $myrow['branch_code']);
  
        $credit_note = get_credit_note_amount($myrow['debtor_no'], $from, $to, $myrow['branch_code']);
        $credit_note_service =get_credit_note_service($myrow['debtor_no'], $from, $to, $myrow['branch_code']);
  

            $num_lines++;
		$total = array(0,0,0,0);
		$init[0] = $init[1] = 0.0;
		$init[0] = round2(($bal['sales']), $dec);
       
        
            $init[1] = round2(($credit_note+$credit_note_service), $dec);
        
        
		$init[2] = round2(($bal2['sales_return_tax_incl'] + $bal2['sales_return']), $dec);
		$init[3] = round2(($bal['refunds']), $dec); //customer refunds
		$init[4] = round2(($bal['customer_receipts']), $dec);//customer receipts

		$init[5] = round2(($bal['journal']), $dec);
		

       	for ($i = 0; $i < 4; $i++)
		{
			$total[$i] += $init[$i];
			$grandtotal[$i] += $init[$i];
		}
		$res = get_transactions($myrow['debtor_no'], $from, $to, $myrow['branch_code']);
		if ($no_zeros && db_num_rows($res) == 0) continue;
 		
		$sales = 0;
		$totalcr = 0;
		$s += 1;
		$total_bal=0;
	
		while ($trans = db_fetch($res))
		{
			if ($no_zeros && floatcmp($trans['TotalAmount'], $trans['Allocated']) == 0) continue;
			$item[0] = $item[1] = 0.0;
			if ($convert)
				$rate = $trans['rate'];
			else
				$rate = 1.0;
// 			if ($trans['type'] == ST_SALESINVOICE )
				$trans['TotalAmount'] *= -1;
			if ($trans['TotalAmount'] > 0.0)
			{
				$item[0] = round2(($trans['TotalAmount']) * $rate, $dec);
			}
			else
			{
				$item[1] = round2(($trans['TotalAmount']) * $rate, $dec);
			}
			$item[2] = round2($trans['Allocated'] * $rate, $dec);

			if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT || $trans['type'] == ST_CPV)
				$item[3] = $item[0] + $item[1] - $item[2];
			else	
			
			
			$item[3] = $item[0] + $item[1] - $item[2];
	    	$gt_gst += $trans['ov_gst'];

				for ($i = 0; $i < 4; $i++)
			{
				$total[$i] += $item[$i];
				$grandtotal[$i] += $item[$i];
			
			}
				$sales += 	$item[3];
				//$totalcr += $item[1];

				$grandtotaldr += $item[0];
				$grandtotalcr += $item[1];

		}
				//$grandtotalop += $init[2];
				//$recipts = $init[2] + $sales - $totalcr;
		        $payments = $init[3] + $sales - $totalcr;//closing balance
		//		$total_cust_balance += $recipts;

		$gt_op += $ob;
		$gt_sales += $init[0];
		$gt_sales_return += $init[1];
		$gt_receipts += $init[4];
		$gt_payments += $init[3];
		$gt_cr_notes += $init[2];
		$gt_journal += $init[5];

// 		$total_cust_balance += $line_total;



		if($debtor_no != $myrow['debtor_no'])
		{
			$rep->AmountCol(0, 1, $s);
			
    $rep->Font('bold');
    $rep->TextCol(1, 2, $myrow['name']);
    $rep->Font('');
    $rep->TextCol(2, 3, get_sales_group_name($myrow['group_no']), $dec);
    $rep->TextCol(3, 4, get_area($myrow['area']), $dec); //opening balance

		}

		$rep->Font('bold');
		$rep->Font('');

	    $line_total = $ob + $init[0] - $init[1] - $init[2] - $init[4] + $init[3] + $init[5] + $init[6];
	    
        $total_cust_balance += $line_total;

        $saless=$item[0] + $item[1] + $item[2]+ $init[0];

    $rep->AmountCol(4, 5, $ob, $dec); //opening balance
		$rep->AmountCol(5, 6, $init[0], $dec); //sales
		
		$rep->AmountCol(6, 7, $init[2], $dec); //sales return
		
		$rep->AmountCol(7, 8, $init[4], $dec); //customer receipts
		$rep->AmountCol(8, 9, $init[3], $dec); //customer refunds
		
		$rep->AmountCol(9, 10, $init[1], $dec); //credit notes
		
		
		$rep->AmountCol(10, 11, $init[5], $dec); //journal
		$rep->AmountCol(11, 12, ($line_total), $dec);

    		$rep->NewLine();
		$debtor_no = $myrow['debtor_no'];
//	}
	}
	
	$rep->NewLine();
	$rep->Line($rep->row  - 4);
	$rep->Font('bold');	
	$rep->fontSize += 2;
	$rep->TextCol(0, 3, _('Grand Total'));
	$rep->fontSize -= 2;

$total_bal =$gt_op+$gt_sales-$gt_cr_notes-$gt_sales_return-$gt_receipts-$gt_payments-$gt_journal;
$net_sales = $gt_sales - $gt_cr_notes;


    $rep->AmountCol(4, 5, $gt_op, $dec);
		$rep->AmountCol(5, 6, $gt_sales, $dec);
		$rep->AmountCol(6, 7, $gt_cr_notes, $dec);
		$rep->AmountCol(7, 8, $gt_receipts, $dec);		
		$rep->AmountCol(8, 9, $gt_payments, $dec);
		$rep->AmountCol(9, 10, $gt_sales_return, $dec);
		
		$rep->AmountCol(10, 11, $gt_journal, $dec);
		
		$rep->AmountCol(11, 12, ($total_cust_balance), $dec);

	    $rep->NewLine(1.3);
		$rep->TextCol(5, 6, _("Net Sales"), $dec);    	
		$rep->AmountCol(6, 7, $net_sales, $dec);    	
	if($gt_gst>0)	
	{
	$rep->NewLine(1);
		$rep->TextCol(5, 6, _("Sales Tax"), $dec);    	
		$rep->AmountCol(6, 7, $gt_gst , $dec); 		
	$rep->NewLine(1);
		$rep->TextCol(5, 6, _("Sales Excl. Tax"), $dec);    	
		$rep->AmountCol(6, 7, $net_sales-$gt_gst , $dec); 		
	}
	
$rep->Font('');	

$rep->End();
}

?>
