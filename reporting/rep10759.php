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
function get_invoice_range($from, $to)
{
	global $SysPrefs;

	$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");

	$sql = "SELECT trans.trans_no, trans.reference
		FROM ".TB_PREF."debtor_trans trans 
			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
		WHERE trans.type=".ST_SALESINVOICE
			." AND ISNULL(voided.id)"
			." AND trans.reference>=".db_escape(get_reference(ST_SALESINVOICE, $from))
			." AND trans.reference<=".db_escape(get_reference(ST_SALESINVOICE, $to))
		." ORDER BY trans.tran_date, trans.$ref";

	return db_query($sql, "Cant retrieve invoice range");
}
function get_customer_balance($customer_id)
{


    if ($to == null)
        $todate = date("Y-m-d");
    else
        $todate = date2sql($to);
    $past1 = get_company_pref('past_due_days');
    $past2 = 2 * $past1;
    // removed - debtor_trans.alloc from all summations
    if ($all)
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount),0)";
    else
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - 
    		trans.alloc),0)";
    $due = "IF (trans.type=10, trans.due_date, trans.tran_date)";
    $sql = "SELECT ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."payment_terms.terms,
		".TB_PREF."debtors_master.credit_limit, ".TB_PREF."credit_status.dissallow_invoices, ".TB_PREF."credit_status.reason_description,

		Sum(IFNULL($value,0)) AS Balance,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2

		FROM ".TB_PREF."debtors_master
			 LEFT JOIN ".TB_PREF."debtor_trans trans ON 
			 trans.tran_date <= '$todate' AND ".TB_PREF."debtors_master.debtor_no = trans.debtor_no AND trans.type <> 13,
			 ".TB_PREF."payment_terms,
			 ".TB_PREF."credit_status

		WHERE
			 ".TB_PREF."debtors_master.payment_terms = ".TB_PREF."payment_terms.terms_indicator
 			 AND ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			 AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id)." 
			";
    if (!$all)
        $sql .= "AND ABS(trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.alloc) > ".FLOAT_COMP_DELTA." ";
    $sql .= "GROUP BY
			  ".TB_PREF."debtors_master.name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month,
			  ".TB_PREF."debtors_master.credit_limit,
			  ".TB_PREF."credit_status.dissallow_invoices,
			  ".TB_PREF."credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}
function get_customer_phone($ref)
{
    $sql="SELECT * FROM 0_crm_persons where ref='".$ref."' and inactive=0";
    $db = db_query($sql);
    return db_fetch($db);
}

function get_customer_deposit($debtor_no)
{
    $sql="SELECT SUM(ov_amount) FROM  0_debtor_trans where debtor_no='".$debtor_no."' and type=12";
    $db = db_query($sql);
    $ft = db_fetch($db);
    return $ft[0];
}
print_invoices();

//----------------------------------------------------------------------------------------------------

function print_invoices()
{
	global $path_to_root, $SysPrefs;
	
	$show_this_payment = true; // include payments invoiced here in summary

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pay_service = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$customer = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);
$pref=get_company_prefs();
	if($orientation == 'P') {
			//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
			$cols = array(4, 20, 55, 226, 266, 300, 230, 515);

			// $headers in doctext.inc
			$aligns = array('left',	'center',	'left', 'center', 'center', 'center', 'right');

		}

	elseif($orientation == 'L')
	{
		if($pref['batch'] == 11){
			//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
			$cols = array(4, 40, 195, 220,260, 315, 355, 395, 450, 475);

			// $headers in doctext.inc
			$aligns = array('left',	'left',	'left',	'left',	'right', 'center', 'right', 'right', 'right');
		}
		else{
			//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
			$cols = array(4, 40, 265, 140, 285, 305, 230, 515);

			// $headers in doctext.inc
			$aligns = array('left',	'left',	'left', 'left', 'left', 'left', 'right');

		}
	}

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", 'A5', 7, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$range = get_invoice_range($from, $to);
	while($row = db_fetch($range))
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $row['trans_no']))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE);

			if ($customer && $myrow['debtor_no'] != $customer) {
				continue;
			}
			if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
				continue;
			}
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}	
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE1, $contacts);
		if ($orientation == 'P')
			$rep->SetHeaderType('Header10759');
		else
			$rep->SetHeaderType('Header107220');
			$rep->NewPage();
			// calculate summary start row for later use
		if ($orientation == 'P')
			$summary_start_row = $rep->bottomMargin + (6 * $rep->lineHeight);
else
	$summary_start_row = $rep->bottomMargin + (3 * $rep->lineHeight);

		if ($rep->formData['prepaid'])
			{
				$result = get_sales_order_invoices($myrow['order_']);
				$prepayments = array();
				while($inv = db_fetch($result))
				{
					$prepayments[] = $inv;
					if ($inv['trans_no'] == $row['trans_no'])
					break;
				}

				if (count($prepayments) > ($show_this_payment ? 0 : 1))
					$summary_start_row += (count($prepayments)) * $rep->lineHeight;
				else
					unset($prepayments);
			}

   			$result = get_customer_trans_details(ST_SALESINVOICE, $row['trans_no']);
			$SubTotal = 0;
			$s_no = 0;
			while ($myrow2=db_fetch($result))
			{
			    	$pref=get_company_prefs();
				if ($myrow2["quantity"] == 0)
					continue;
$item = get_item($myrow2['stock_id']);
if($item['units'] != $myrow2['units_id'] ){
				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]* $item['con_factor']),
				   user_price_dec());
}
else
{
    $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"] ),
				   user_price_dec());
}
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],1);
	    		
	    		
	    		  if($item['units'] != $myrow2['units_id'] ){
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"] *  $item['con_factor'],1);
	    		  }
	    		  else{
	    		      	$DisplayQty = number_format2($sign*$myrow2["quantity"],1);
	    		  }
	    		
	    		
	    		
	    		$DisplayNet = number_format2($Net,1);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				$c=0;
                $s_no ++;
				$rep->TextCol(0, 1,	$s_no, -2);
				$oldrow = $rep->row;
				// display_error($myrow["quantity"]);
				
				
                    // $rep->TextCol(1, 2,	number_format2($DisplayQty * $item['con_factor'],2), -2);
              
                $rep->TextCol(1, 2,	$DisplayQty , -2);
                
                $rep->TextCol(3, 4, $myrow2['units_id'], -2);

                $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{
				    if($pref['batch'] == 1){
//				        $batch=get_batch_by_id($myrow2['batch']);
//				         $rep->TextCol(2, 3,	$batch['name'], -2);
//				          $rep->TextCol(3, 4,	sql2date($batch['exp_date']), -2);
//				        $rep->TextCol(4, 5,	number_format2($DisplayQty), -2);
                    $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
//                    if($pref['alt_uom'] == 1)
//                    {
////                        $rep->TextCol(5, 6, $myrow2['units_id'], -2);
//                    }
//                    else
//                    {
////                        $rep->TextCol(5, 6, $myrow2['units'], -2);
//                    }

					$rep->TextCol(6, 7,	($DisplayPrice), -2);
//					$rep->TextCol(7, 8,	$DisplayDiscount, -2);
					$rep->TextCol(8, 9,	$DisplayNet, -2);
				    }
				    
				 else
				    {
//				          $rep->TextCol(2, 3,	number_format2($DisplayQty), -2);
                    $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
//                    if($pref['alt_uom'] == 1)
//                    {
////                        $rep->TextCol(3, 4, $myrow2['units_id'], -2);
//                    }
//                    else
//                    {
////                        $rep->TextCol(3, 4, $myrow2['units'], -2);
//                    }

					$rep->TextCol(4, 5,	($DisplayPrice), -2);
				//	$rep->TextCol(5, 6,	$DisplayDiscount, -2);
					$rep->TextCol(6, 7,	$DisplayNet, -2);
				        
				    }
				}
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $summary_start_row)
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);

			// set to start of summary line:
    		$rep->row = $summary_start_row;
			if (isset($prepayments))
			{
				// Partial invoices table
				$rep->TextCol(0, 3,_("Prepayments invoiced to this order up to day:"));
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->cols[2] -= 20;
				$rep->aligns[2] = 'right';
				$rep->NewLine(); $c = 0; $tot_pym=0;
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->TextCol($c++, $c, _("Date"));
				$rep->TextCol($c++, $c,	_("Invoice reference"));
				$rep->TextCol($c++, $c,	_("Amount"));

				foreach ($prepayments as $invoice)
				{
					if ($show_this_payment || ($invoice['reference'] != $myrow['reference']))
					{
						$rep->NewLine();
						$c = 0; $tot_pym += $invoice['prep_amount'];
						$rep->TextCol($c++, $c,	sql2date($invoice['tran_date']));
						$rep->TextCol($c++, $c,	$invoice['reference']);
						$rep->TextCol($c++, $c, number_format2($invoice['prep_amount'], $dec));
					}
					if ($invoice['reference']==$myrow['reference']) break;
				}
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->NewLine();
				$rep->TextCol(1, 2,	_("Total payments:"));
				$rep->TextCol(2, 3,	number_format2($tot_pym, $dec));
			}


			$doctype = ST_SALESINVOICE;
    		$rep->row = $summary_start_row;
			$rep->cols[2] += 20;
			$rep->cols[3] += 20;
			$rep->aligns[3] = 'left';
            $pref=get_company_prefs();
//		if ($orientation == 'P')
//			$rep->NewLine(-12);
//			$rep->NewLine(-18);

		if($orientation == 'P')
		{
		    if($myrow['discount2']!=0 || $myrow['discount2']!=0)
		    {
		        $rep->NewLine(-32);
			$rep->NewLine(-19);
		    }
		    else
		    {
		        $rep->NewLine(-31);
			$rep->NewLine(-18);
		    }
		    	$rep->NewLine();
			
//			$rep->TextCol(2, 5, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
			if($myrow['discount1']) {
			$rep->TextCol(2, 5, _("Discount 1"), -2);
			$rep->Amountcol(6, 7, $myrow['discount1'], $dec);
				$rep->NewLine();
				$rep->TextCol(2, 5, _("Total"), -2);
			$rep->Amountcol(6, 7, $SubTotal-$myrow['discount2'], $dec);
			}
			if($myrow['discount2']) {
			$rep->TextCol(2, 5, _("Discount 2"), -2);
			$rep->Amountcol(6, 7, $myrow['discount2'], $dec);
			$rep->NewLine();
				$rep->TextCol(2, 5, _("Total"), -2);
			$rep->Amountcol(6, 7, $SubTotal-$myrow['discount2'], $dec);
		}
		
		
            }
           else{
			   $rep->NewLine(-28);
			   $rep->NewLine(-11);
//    	   $rep->TextCol(4, 6, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
            }
	     	$rep->NewLine();

			if ($myrow['ov_freight'] != 0.0)
			{
   				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
				$rep->TextCol(3, 6, _("Shipping"), -2);
				$rep->Amountcol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
			}
		if($myrow['discount1']) {
//			$rep->TextCol(3, 6, _("Discount 1"), -2);
//			$rep->Amountcol(6, 7, $myrow['discount1'], $dec);
			$rep->NewLine();
		}
		if($myrow['discount1']) {
//			$rep->TextCol(3, 6, _("Discount 2"), -2);
//			$rep->Amountcol(6, 7, $myrow['discount2'], $dec);
			$rep->NewLine();
		}
		else
		{
			if($pref['batch'] ==1){
//				$rep->TextCol(5, 8, _("Sub-total"), -2);
//				$rep->TextCol(8, 9,	$DisplaySubTot, -2);
//			}
//			else{
//				$rep->TextCol(3, 6, _("Sub-total"), -2);
//				$rep->TextCol(6, 7,	$DisplaySubTot, -2);
			}
			$rep->NewLine();

			if ($myrow['ov_freight'] != 0.0)
			{
				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
				$rep->TextCol(3, 6, _("Shipping"), -2);
				$rep->Amountcol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
			}
			if($myrow['discount1']) {
//				$rep->TextCol(3, 6, _("Discount 1"), -2);
//				$rep->Amountcol(6, 7, $myrow['discount1'], $dec);
				$rep->NewLine();
			}
			if($myrow['discount1']) {
//				$rep->TextCol(3, 6, _("Discount 2"), -2);
//				$rep->Amountcol(6, 7, $myrow['discount2'], $dec);
				$rep->NewLine();
			}
		}



        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $row['trans_no']);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    		    	$pref=get_company_prefs();
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

    			if ($SysPrefs->suppress_tax_rates() == 1)
    				$tax_type_name = $tax_item['tax_type_name'];
    			else
    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

    			if ($myrow['tax_included'])
    			{
    				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
    				{
    					if ($first)
    					{
				// 			$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
				// 			$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
				// 		$rep->TextCol(3, 6, $tax_type_name, -2);
				// 		$rep->TextCol(6, 7,	$DisplayTax, -2);
						$first = false;
    				}
    				else
    			if($pref['batch'] ==1){
				// 		$rep->TextCol(5, 8, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
    			}
    			else{
    			    	// $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
    			}
				}
    			else
    			{
				// 	$rep->TextCol(3, 6, $tax_type_name, -2);
				// 	$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}

$rep->MultiCell(200, 10, "Signature : ________________________________", 0, 'L', 0, 1, 50, 500, true);
$rep->fontSize += 2;
$customer_phone = get_customer_phone($sales_order['name']);
$rep->MultiCell(200, 10, "Phone : ".$customer_phone['phone'], 0, 'L', 0, 1, 40, 85, true);
$rep->fontSize -= 2;
	$pref=get_company_prefs();
    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"] - ($myrow['discount1'] + $myrow['discount2'])),$dec);
			$rep->Font('bold');
			if (!$myrow['prepaid']) $rep->Font('bold');
		
            if($pref['batch'] ==1){
				$rep->TextCol(6, 8, $rep->formData['prepaid'] ? _("TOTAL ORDER VAT INCL.") : _("TOTAL INVOICE"). ' ' . $rep->formData['curr_code'], - 2);
			$rep->TextCol(8, 9, $DisplayTotal, -2);
            }
			if ($rep->formData['prepaid'])
			{
				$rep->NewLine();
				$rep->Font('bold');
				$rep->TextCol(3, 6, $rep->formData['prepaid']=='final' ? _("THIS INVOICE") : _("TOTAL INVOICE"), - 2);
				$rep->TextCol(6, 7, number_format2($myrow['prep_amount'], $dec), -2);
			}
			$words = price_in_words($rep->formData['prepaid'] ? $myrow['prep_amount'] : $myrow['Total']
				, array( 'type' => ST_SALESINVOICE, 'currency' => $myrow['curr_code']));
			if ($words != "")
			{
				$rep->NewLine(1);
				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
            $trans_no = $row['trans_no'];
	}

    $pre_balance = get_customer_balance($myrow['debtor_no']);
    if($pre_balance['Balance'] == 0)
       $pre_bal= 0 ;
    else
      $pre_bal= $pre_balance['Balance'] - $SubTotal ;


    $total = $pre_bal + $SubTotal;
    $deposit = get_customer_deposit($myrow['debtor_no'],$trans_no);
    $balance = $total-$deposit;


    $rep->NewLine(-2.5);
    $rep->TextCol(3, 5, _("This Invoice : "), -2);
    $rep->TextCol(6, 7,	$DisplaySubTot, -2);

    $rep->NewLine();
    $rep->TextCol(3, 5, _("Other Balance : "), -2);
    $rep->TextCol(6, 7,	number_format2( $pre_bal,2) , -2);

    $rep->NewLine();
    $rep->TextCol(3, 5, _("Total : "), -2);
    $rep->TextCol(6, 7,	number_format2($total ,2), -2);

    // $rep->NewLine();
    // $rep->TextCol(3, 5, _("Deposit : "), -2);
    // $rep->TextCol(6, 7, number_format2($deposit,2), -2);

    // $rep->NewLine();
    // $rep->TextCol(3, 5, _("Balance : "), -2);
    // $rep->TextCol(6, 7,	number_format2($balance,2), -2);

    $rep->NewLine(+1);
	if ($email == 0)
		$rep->End();
}

