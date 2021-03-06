<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_invoices();

//----------------------------------------------------------------------------------------------------

function get_shippers_name_rep($id)
{
	$sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

	$result = db_query($sql, "could not get shippers name");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_customer_balance($customer_id, $to=null, $all=true)
{
    $date =today();
    $date1=date2sql($date);
//    if ($to == null)
//        $todate = date("Y-m-d");
//    else
//        $todate = date2sql($to);
    $past1 = get_company_pref('past_due_days');
    $past2 = 2 * $past1;
    // removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
    if ($all)
        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";
    else
        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc),0)";



    $due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$date1' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
        .TB_PREF."payment_terms terms,"
        .TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id
	 		
	 			";
    if ($customer_id)
        $sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

    if (!$all)
        $sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;

    if($dim != 0)
        $sql .= " AND trans.dimension_id = ".db_escape($dim);
    $sql .= " GROUP BY
		  	debtor.name,
		  	terms.terms,
		  	terms.days_before_due,
		  	terms.day_in_following_month,
		  	debtor.credit_limit,
		  	credit_status.dissallow_invoices,
		  	credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}
function print_invoices()
{
	global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;
	
	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pay_service = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$dec = user_price_dec();

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(2, 35, 240, 290, 340, 385, 450, 515);


			
	// $headers in doctext.inc
	$aligns = array('center','left','center', 'center', 'left', 'center', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');
	


	if ($email == 0)
	{
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize());
		$rep->SetHeaderType('Header1073');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);
	}
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $i))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($i, ST_SALESINVOICE);
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize());
			    $rep->SetHeaderType('Header1073');
				$rep->currency = $cur;
				$rep->Font();
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
				$rep->Info($params, $cols, null, $aligns);
			}
			else
				$rep->title = _('INVOICE');


			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], false);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$net_total =($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]));
//				$SubTotal +=( $Net-$myrow["ov_freight"]);
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
	    		
	    		$ZeroValue = "";
	    		
	    		
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";



	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
		  			
$SerialNumber += 1;

// Discount option item controled hiding code
if ($DisplayPrice < 0)	
	{
	$rep->TextCol(0, 1,	$ZeroValue, -2);
	}		
	else
				$rep->TextCol(0, 1,	$SerialNumber, -2);
				
//				$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
								$oldrow = $rep->row;
if ($DisplayPrice < 0)	
	{
	$rep->TextCol(1, 2,	$ZeroValue, -2);
	}		
	else								
				$rep->TextColLines(1, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{
				
				
			
					

	if ($DisplayPrice < 0)	
	{
	$rep->TextCol(2, 3,	$ZeroValue, -2);
	}		
	else
					$rep->TextCol(2, 3,	$DisplayQty, -2);
if ($DisplayPrice < 0)	
	{
	$rep->TextCol(3, 4,	$ZeroValue, -2);
	}		
	else					
					$rep->TextCol(3, 4,	$myrow2['units'], -2);

if ($DisplayPrice < 0)	
	{
	$rep->TextCol(4, 5,	$ZeroValue, -2);
	}		
	else	
					$rep->TextCol(4, 5,	$DisplayPrice, -2);
if ($DisplayPrice < 0)	
	{
	$rep->TextCol(5, 6,	$ZeroValue, -2);
	}		
	else														
					$rep->TextCol(5, 6,	$DisplayDiscount, -2);

if ($DisplayPrice < 0)	
	{
	$rep->TextCol(6, 7,	$ZeroValue, -2);
	}		
	else	
						
					$rep->TextCol(6, 7,	$DisplayNet, -2); 
				}	
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_SALESINVOICE, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*-$myrow["ov_freight"],$dec);

			//$rep->NewLine();

// Query for DISCOUNT hiding/showing column header

$sql = "
SELECT
SUM(discount_percent) AS DiscountAmount
FROM ".TB_PREF."debtor_trans_details
WHERE debtor_trans_no = '$myrow[trans_no]'
AND debtor_trans_type = 10
";
 $result = db_query($sql,"No transactions were returned");
   $bal1 = db_fetch($result);

$DiscountAmount = round2($bal1['DiscountAmount'], $dec); 

if ($DiscountAmount  == 0)
$DiscountHeader = "";
else
$DiscountHeader = Discount;
			$rep->Font('bold');
			$rep->NewLine(-3.6);
			$rep->TextCol(5, 6, $DiscountHeader, - 2);	
			$rep->NewLine();
			$rep->NewLine();
			$rep->NewLine();		


    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype = ST_SALESINVOICE;
			
// Discount option controled from service item		
		if ($DisplayPrice < 0)
				
				{	
					$rep->TextCol(4, 6, Total , -2);			
					$TotalAmount1 -= $Net-$SubTotal; //addition of discount into total amount
					$TotalAmount = number_format2($TotalAmount1,$dec);
					
					$rep->TextCol(6, 7, $TotalAmount , -2);
					$rep->NewLine();						
					$rep->TextCol(4, 6, _("Discount"), -2);
					$rep->TextCol(6, 7,	$DisplayPrice, -2);	}
	
		else		{
					$rep->TextCol(4, 6, _("Total"), -2);
					$rep->TextCol(6, 7, $DisplaySubTot, -2);
					
					$ZeroDiscount = "";
					$rep->TextCol(3, 6, _(""), -2);
					$rep->TextCol(6, 7,	$ZeroDiscount, -2);	
}

		//	$rep->NewLine();

		//	$rep->TextCol(3, 6, _("Sub-total"), -2);
		//	$rep->TextCol(6, 7,	$DisplaySubTot, -2);
			$rep->NewLine();
			$rep->TextCol(4, 6, _("Shipping"), -2);
			$rep->TextCol(6, 7,	$DisplayFreight, -2);
			$rep->NewLine();
			
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
    			
    			if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
    				$tax_type_name = $tax_item['tax_type_name'];
    			else
    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

    			if ($tax_item['included_in_price'])
    			{
    				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
    				{
    					if ($first)
    					{
							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
						$rep->TextCol(3, 6, $tax_type_name, -2);
						$rep->TextCol(6, 7,	$DisplayTax, -2);
						$first = false;
    				}
    				else
						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
					$rep->TextCol(3, 6, $tax_type_name, -2);
					$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}

    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*(-$myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
			$rep->Font('bold');
			$rep->TextCol(4, 6, _("TOTAL INVOICE"), - 2);
			    		
$rep->MultiCell(555, 18, _("Powered by www.hisaab.pk") , 0, 'L', 0, 2, 450,824, true);			
	
			    		
			$rep->TextCol(6, 7, $DisplayTotal, -2);
			$words = price_in_words($myrow['Total'], ST_SALESINVOICE);
			if ($words != "")
			{
				$rep->NewLine(1);
				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
			
// Account Status
//Debit 
$sql = "
SELECT
SUM(ov_amount) AS OutStanding
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type = 10
";
 $result = db_query($sql,"No transactions were returned");
   $bal2 = db_fetch($result);

//Credit
$sql = "
SELECT
SUM(ov_amount) AS Payments
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type IN (12 , 11 , 2, 42)
";

//AND type IN (11,12,2)
 $result = db_query($sql,"No transactions were returned");
   $bal3 = db_fetch($result);
   
$TotalCredit = round2($bal3['Payments'], $dec); //Total credit side balance

$TotalDebit = round2($bal2['OutStanding'], $dec); // Total debit side balance
				
$CurrentAmount = $SubTotal-$myrow["ov_freight"];

//$PreviousBalance = number_format2($TotalDebit-$TotalCredit-$SubTotal); 
        $date =today();
 $customer_record = get_customer_balance($myrow['debtor_no'],$date);
 $total_balance=$customer_record["Balance"];
 $tot_balance=($total_balance - $CurrentAmount);
$PreviousBalance = $total_balance ;

// $PreviousBalance = number_format2($TotalDebit-$TotalCredit);

		$TotalBalance2 = number_format2($TotalDebit-$TotalCredit-$SubTotal-$myrow["ov_freight"]);

			$rep->NewLine(5);			
			$rep->TextCol(4, 6, _("Previous Balance"), -2);
// 			if ($TotalBalance2 > 0)
			$rep->TextCol(6, 7, number_format2($tot_balance , $dec)); //previous balance
// 			else
			$rep->TextCol(6, 7, _("") , -2); 			
			$rep->NewLine();
			$rep->TextCol(4, 6, _("Current Amount"), -2);			
			$rep->TextCol(6, 7, number_format2($CurrentAmount, $dec)); // Current Amount
			$rep->NewLine();
			$rep->TextCol(4, 6, _("Total Balance"), -2);			
			$rep->TextCol(6, 7, number_format2($PreviousBalance,  $dec)); // TotalBalance
			$rep->NewLine();
			$rep->NewLine();
// Account status - end
			$rep->NewLine();
			$rep->TextCol(1, 3, _("_______________"), -2);	
			$rep->NewLine();
			$rep->TextCol(1, 3, _("      Signature"), -2);
// Invoice Header
			$rep->NewLine(-126);
			$rep->fontSize = 18;
			$rep->TextCol(1, 2, _("INVOICE"), -2);
			$rep->NewLine(11.6);
			$rep->fontSize = 9;
$rep->Font();
// $ShippingCompany = get_shipping_company($myrow['default_ship_via']);
		//	$rep->TextCol(2, 3, $ShippingCompany,  -2); // Shipping Company


			//$rep->NewLine(126);			

			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>