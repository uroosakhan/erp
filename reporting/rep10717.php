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
function get_customer_total_outstand_of($customer_id)
{
    $sql = "SELECT SUM( ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + gst_wh +  `alloc` ) AS Payments FROM ".TB_PREF."debtor_trans
 WHERE debtor_no=".db_escape($customer_id)."
	AND TYPE IN ( 10 )";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_customer_total_payment_of($customer_id)
{
    $sql = "SELECT SUM( ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + gst_wh +  `alloc` ) AS Payments FROM ".TB_PREF."debtor_trans
 WHERE debtor_no=".db_escape($customer_id)."
	AND TYPE IN ( 12,11,2 )";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_refs($id, $type)
{
    $sql = "SELECT reference FROM ".TB_PREF."refs
 WHERE id=".db_escape($id)."
	AND type=".db_escape($type)."";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_customer_balance($customer_id, $to=null, $all=true)
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
			 AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id)." ";
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
function get_shipping_through($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers
		WHERE shipper_id=".db_escape($id)."";
    $result = db_query($sql,"check failed");
    $row = db_fetch($result);
    $shipper_name = $row['shipper_name'];
    return $shipper_name;
}
function get_prepared_by_name($transtype,$trans)
{
    $sql = "SELECT  user  FROM ".TB_PREF."audit_trail"
        ." WHERE type=".db_escape($transtype)." AND trans_no="
        .db_escape($trans);

    $query= db_query($sql, "Cannot get all audit info for transaction");
    $fetch=db_fetch_row($query);
    return $fetch[0];
}
function get_user_name_1($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get user $user_id");

    $myrow = db_fetch($result);

    return $myrow[0];
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

	//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
	$cols = array(4, 50, 225, 290, 350, 400, 450);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'center', 'right','right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$range = get_invoice_range($from, $to);
	while($row = db_fetch($range))
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $row['trans_no']))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE);
            $Outstanding=get_customer_total_outstand_of($myrow['debtor_no']);
            $payment=get_customer_total_payment_of($myrow['debtor_no']);
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
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->SetHeaderType('Header10717');
			$rep->NewPage();
			// calculate summary start row for later use
			$summary_start_row = $rep->bottomMargin + (15 * $rep->lineHeight);

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
$memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
			if ($memo != "")
			{
			   
		            $rep->NewLine(-5);
					$rep->TextColLines(1, 5, $memo."             ", -2);
					$rep->NewLine(+5);
					
			}
   			$result = get_customer_trans_details(ST_SALESINVOICE, $row['trans_no']);
			$SubTotal = 0;
        $s=1;
			while ($myrow2=db_fetch($result))
			{
			    $item=get_item($myrow2['stk_code']);
			     $pref = get_company_prefs();
		    
		     if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
		    	$qty=$myrow2['quantity'] * $myrow2['con_factor'];
		    	else
		    	$qty=$myrow2['quantity'];
			    
			    
			    
				if ($qty == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
                $DisplayQty = number_format2($sign*$qty,get_qty_dec($myrow2['stock_id']));

                $DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				$c=0;
				    $DisplayAmount3 = number_format2($myrow2["amount3"],user_qty_dec());
				$rep->TextCol(0, 1,	$s++, -2);
				$oldrow = $rep->row;
				$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{
                    if($pref['alt_uom'] == 1)
                    {
                        $rep->TextCol(2, 3, $myrow2['units_id'], -2);
                    }
                    else
                    {
                        $rep->TextCol(2, 3, $myrow2['units'], -2);
                    }
                    $pref = get_company_prefs();
                    $item=get_item($myrow2['stk_code']);
               
					$rep->Amountcol(3, 4,	$qty, $dec);
					 $tot_qty += $qty;
					
                   
                    $tot_amount3 += $myrow2["amount3"];
                    
//                $item=get_item($myrow2['stk_code']);
                    if($pref['alt_uom'] == 1)
                    {
                        $rep->TextCol(4, 5,$DisplayPrice ."/" . $myrow2['units_id'], -2);
                    }
                    else
                    {
                        $rep->TextCol(4, 5,$DisplayPrice, -2);
                    }

//					$rep->TextCol(4, 5,	$DisplayPrice ."/", -2);
					$rep->TextCol(5, 6,	$DisplayDiscount, -2);
					$rep->TextCol(6, 7,	$DisplayNet, -2);
                    $tot_net += $Net;
				}
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $summary_start_row)
					$rep->NewPage();
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

//			$rep->TextCol(3, 6, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
//		$rep->NewLine();

			if ($myrow['ov_freight'] != 0.0)
			{
   				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
//				$rep->TextCol(3, 6, _("Shipping"), -2);
//				$rep->Amountcol(6, 7,	$DisplayFreight, -2);
//				$rep->NewLine();
			}
		if($myrow['discount1']) {
//			$rep->TextCol(3, 6, _("Discount 1"), -2);
//			$rep->Amountcol(6, 7, $myrow['discount1'], $dec);
//			$rep->NewLine();
		}
		if($myrow['discount2']) {
//             $rep->NewLine(-15.5);
// 			$rep->TextCol(4, 5, _("Discount 2"), -2);
// 			$rep->Amountcol(5, 6, $myrow['discount2'], $dec);
            $rep->multicell(150,35,number_format2($tot_net - $myrow['discount2'],get_qty_dec($myrow2['stock_id'])),1,'R',0,0,415,692);
            $rep->multicell(30,35,"Total:",0,'l',0,0,415,692);
            $tot_amt =$tot_net - $myrow['discount2'];
            $rep->NewLine();
		}
		else{
            $rep->multicell(150,35,number_format2($tot_net,get_qty_dec($myrow2['stock_id'])),1,'R',0,0,415,692);
            $rep->multicell(30,35,"Total:",0,'l',0,0,415,692);
            $tot_amt = $tot_net;
        }
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $row['trans_no']);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
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
//							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
						$first = false;
    				}
//    				else
//						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
//					$rep->TextCol(3, 6, $tax_type_name, -2);
//					$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}
    	$prepared_by = get_prepared_by_name($myrow['type'], $myrow['trans_no']);
        $customer_record = get_customer_balance($myrow['debtor_no']);
        $total_balance=$customer_record["Balance"];
//        $tot_balance=($total_balance-$DisplayTotal_new);
        $previous_balance = $total_balance - $tot_net;
        $tot_balance=($tot_amt + $previous_balance);
        $rep->multicell(100,20,"",0,'R',0,0,265,451);
//        $rep->multicell(60,20,number_format2($tot_amount3,get_qty_dec($myrow2['stock_id'])),0,'C',0,0,295,451);
//        $rep->multicell(60,20,number_format2($tot_qty,get_qty_dec($myrow2['stock_id'])),0,'C',0,0,365,451);
        $rep->multicell(150,35,number_format2($tot_balance,$dec),1,'R',0,0,415,727);
        $rep->multicell(50,35,"Closing Bal:",0,'l',0,0,415,727);
         $rep->font('b');
        $rep->multicell(200,10,"Opening Balance:",0,'L',0,0,344,115);
        $rep->multicell(200,10,"DN No:",0,'L',0,0,344,130);
        $rep->multicell(200,10,"SO No:",0,'L',0,0,344,145);
        $rep->font('');
          $rep->multicell(200,10,number_format2($previous_balance,$dec),0,'L',0,0,424,115);

          $rep->multicell(200,10,$sales_order['reference'],0,'L',0,0,424,145);

//        $rep->setfontsize(12);
//        $rep->multicell(160,40,$memo,1,'L',0,0,150,570);
//        $rep->multicell(160,20,get_shipping_through($myrow['ship_via']),1,'L',0,0,152,532);
//
//        $rep->multicell(170,20," ".get_user_name_1($prepared_by),1,'L',0,0,115,650);
            $rep->setfontsize(8);
    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"] - ($myrow['discount1'] + $myrow['discount2'])),$dec);
			$rep->Font('bold');
			if (!$myrow['prepaid']) $rep->Font('bold');
//				$rep->TextCol(3, 6, $rep->formData['prepaid'] ? _("TOTAL ORDER VAT INCL.") : _("TOTAL INVOICE"). ' ' . $rep->formData['curr_code'], - 2);
//			$rep->TextCol(6, 7, $DisplayTotal, -2);
			if ($rep->formData['prepaid'])
			{
				$rep->NewLine();
				$rep->Font('bold');
//				$rep->TextCol(3, 6, $rep->formData['prepaid']=='final' ? _("THIS INVOICE") : _("TOTAL INVOICE"), - 2);
//				$rep->TextCol(6, 7, number_format2($myrow['prep_amount'], $dec), -2);
			}
			$words = price_in_words($rep->formData['prepaid'] ? $myrow['prep_amount'] : $myrow['Total']
				, array( 'type' => ST_SALESINVOICE, 'currency' => $myrow['curr_code']));
			if ($words != "")
			{
				$rep->NewLine(1);
//				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

