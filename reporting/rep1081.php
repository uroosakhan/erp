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
function get_user_name_70123($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
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
	$pictures = $_POST['PARAM_4'];
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

	//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
		if($pref['batch'] == 1){
		$cols = array(0, 20, 100, 246, 311, 348, 387, 422 ,480,480, 485);
		// $headers in doctext.inc
		$aligns = array('center', 'left', 'left', 'center', 'center', 'right',	'right', 'right', 'right');
	}
		else
		{
		   
		$cols = array(0, 20, 95, 365, 400,  425, 480);
    	$aligns = array('center', 'left', 'left', 'center', 'center', 'right', 'right');
}

	// $headers in doctext.inc
	

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
			$rep->Info($params, $cols, $headers, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			if ($pictures)
			$rep->SetHeaderType('Header10792');
			else
			$rep->SetHeaderType('Header10793');

			$rep->NewPage();

			
			// calculate summary start row for later use
			$summary_start_row = $rep->bottomMargin + (20 * $rep->lineHeight);

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
		$total_discount=0;
			$s_no=0;
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;

$total_ =abs($discount-$myrow2["unit_price"]);

					$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				   
				   $subtotal+=$total_;
				$SubTotal += $Net;
				

	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				$c=0;
				$s_no++;
				
				$rep->TextCol(1, 2,	"", -2);
				$oldrow = $rep->row;
                $rep->TextColLines(2, 3, "", -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
			{
				    	if($pref['batch'] == 1){
				    	    $rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
	$rep->TextCol(0, 1,	" ".$s_no , -2);
						$batch=get_batch_by_id($myrow2['batch']);
						$rep->TextCol(3, 4, $batch['name'], -2);
						$rep->TextCol(4, 5, " ".sql2date($batch['exp_date']), -2);
						$rep->TextCol(9, 10,	$DisplayNet, -2);
							$rep->TextCol(7, 8,	$DisplayPrice, -2);
							 $rep->TextCol(6, 7, $myrow2['units_id'], -2);
	$rep->TextCol(5, 6,	$DisplayQty, -2);
		$rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);

							
					}
					else{
					$rep->TextCol(0, 1,	" ".$s_no , -2);
	                $rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
	                $rep->TextCol(3, 4,	$DisplayQty."    ", -2);
	                $rep->TextCol(4, 5, " ".$myrow2['units_id'], -2);
					$rep->TextCol(5, 6,	$DisplayPrice, -2);
					$rep->TextCol(6, 7,	$DisplayDiscount, -2);
					$rep->TextCol(6, 7,	$DisplayNet, -2);
                    $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
					}

                    $pref = get_company_pref();

				}
			
			 if ($rep->row < $rep->bottomMargin +(5*$rep->lineHeight))
            {
                   $rep->LineTo($rep->leftMargin, 43.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
       
       
              	if($pref['batch'] == 0){
              	    
}
else{
            $rep->LineTo($rep->pageWidth - $rep->rightMargin-125,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-125, $rep->row);

    
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-167,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-167, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-219,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-219, $rep->row);
}
       
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-278,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-278, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-430,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-430, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-507,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-507, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-46,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-46, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-105,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-105, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-130,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-130, $rep->row);
  $rep->Line($rep->row);
                $rep->NewPage();
            }

        }
        $rep->LineTo($rep->leftMargin, 43.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-167,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-167, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-95,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-95, $rep->row);
       
       	if($pref['batch'] == 0){
}
else{
            $rep->LineTo($rep->pageWidth - $rep->rightMargin-219,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-219, $rep->row);
       
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-278,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-278, $rep->row);
        
}
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-430,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-430, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-507,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-507, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-46,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-46, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-130,  43.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-130, $rep->row);
  $rep->Line($rep->row);
			$memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
//			if ($memo != "")
//			{
//				$rep->NewLine();
//				// $rep->TextColLines(1, 5, "Term And Conditions", -2);
////				$rep->TextColLines(1, 5, $memo, -2);
//			}
		if($pref['batch'] == 0){
		    
 $rep->NewLine(16);
		}
		
		else{ $rep->NewLine(9);
		    
		    
		}
   			$DisplaySubTot = number_format2($SubTotal,$dec);
		$total_discount += ($myrow["discount1"]);
			// set to start of summary line:
    		$rep->row = $summary_start_row;
			if (isset($prepayments))
			{
			
				$rep->cols[2] -= 20;
				$rep->aligns[2] = 'right';
				$rep->NewLine(); $c = 0; $tot_pym=0;

				foreach ($prepayments as $invoice)
				{
					if ($show_this_payment || ($invoice['reference'] != $myrow['reference']))
					{
						$rep->NewLine();
				
					}
					if ($invoice['reference']==$myrow['reference']) break;
				}
				
				$rep->NewLine();
			
			}

			$doctype = ST_SALESINVOICE;
    		$rep->row = $summary_start_row;
			$rep->cols[2] += 20;
			$rep->cols[3] += 20;
			$rep->aligns[3] = 'left';
	if($pref['batch'] == 1){
	      $rep->NewLine(2);
			$rep->TextCol(4, 6, _("Sub-total"), -2);
			$rep->TextCol(9, 10,	$DisplaySubTot, -2);
	}
	else
	{
	    $rep->NewLine(2);
	    	$rep->TextCol(4, 6, _("Sub-total"), -2);
			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
	}
		    $rep->NewLine(1);
		    if($pref['batch'] == 0){
            if($myrow["discount1"] != 0)
            {
                $rep->TextCol(4, 6, _("Discount").$myrow["discount_percent"], -2);
			    $rep->TextCol(6, 7,	price_format($total_discount), -2);
			     $rep->NewLine();
            }
            if($myrow["discount2"] != 0)
            {
                $rep->TextCol(4, 6, _("Discount"), -2);
                $rep->AmountCol(6, 7,	$myrow["discount2"], $dec);
            }
			$rep->NewLine();
			if ($myrow['ov_freight'] != 0.0)
			{
   				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
				$rep->TextCol(4, 6, _("Shipping"), -2);
				$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
			}
		    }
		    else
		    {
		       if($myrow["discount1"] != 0)
            {
                $rep->TextCol(4, 6, _("Discount").$myrow["discount_percent"], -2);
			    $rep->TextCol(9, 10,	price_format($total_discount), -2);
			     $rep->NewLine();
            }
            if($myrow["discount2"] != 0)
            {
                $rep->TextCol(4, 6, _("Discount"), -2);
                $rep->AmountCol(9, 10,	$myrow["discount2"], $dec);
            }
			$rep->NewLine();
			if ($myrow['ov_freight'] != 0.0)
			{
   				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
				$rep->TextCol(4, 6, _("Shipping"), -2);
				$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
			}  
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
							$rep->TextCol(4, 6, _("Total Tax Excluded"), -2);
							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
						$rep->TextCol(4, 6, $tax_type_name, -2);
						$rep->TextCol(8, 9,	$DisplayTax, -2);
						$first = false;
    				}
    				else
						$rep->TextCol(4, 7, _("Included") . " " . $tax_type_name . _("Amount") . ":", -2);
                    $rep->TextCol(6, 7, $DisplayTax, -2);
    			}
    				if($pref['batch'] == 1){
    				   $rep->TextCol(4, 6, $tax_type_name, -2);
					$rep->TextCol(9, 10,	$DisplayTax, -2); 
    				}
    			else
    			{
					$rep->TextCol(4, 6, $tax_type_name, -2);
					$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}

    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]-$myrow["discount1"]-$myrow["discount2"]),$dec);
			$rep->Font('bold');
			if (!$myrow['prepaid']) $rep->Font('bold');
				$rep->TextCol(4, 6, $rep->formData['prepaid'] ? _("TOTAL ORDER VAT INCL.") : _("TOTAL INVOICE").' '.'(' .$rep->formData['curr_code'].')' , - 2);
			if($pref['batch'] == 1){
			    			
			    $rep->TextCol(9, 10, $DisplayTotal, -2);

			}else{
			    $rep->TextCol(6, 7, $DisplayTotal, -2);

			}
		
			if ($rep->formData['prepaid'])
			{
				$rep->NewLine();
				$rep->Font('bold');
				$rep->TextCol(4, 6, $rep->formData['prepaid']=='final' ? _("THIS INVOICE") : _("TOTAL INVOICE"), - 2);
				$rep->TextCol(6, 7, number_format2($myrow['prep_amount'], $dec), -2);
			}
			$words = price_in_words($rep->formData['prepaid'] ? $myrow['prep_amount'] : $myrow['Total']
				, array( 'type' => ST_SALESINVOICE, 'currency' => $myrow['curr_code']));
			if ($words != "")
			{
				$rep->NewLine(1);
				$rep->TextCol(1, 2, $myrow['curr_code'] . ": " . $words, - 2);
			}
			$rep->fontSize -= 2;
        $rep->NewLine(-4.5);
        
        $rep->Text($mcol + 100, "Warranty under the Medical Devices Rules, 2017");
        $rep->Text($mcol + 100, "___________________________________________");
            
        $rep->NewLine();
        $rep->Text($mcol + 50, "I, Ch. Khalid Nawaz being authorized by M/s SAKUF TRADING, 2nd floor, G-34,");
       
        $rep->NewLine();
        $rep->Text($mcol + 50, "Phase-1, commercial area, DHA, Lahore, authorized vide letter no. ST-HR-150");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "818/1, dated 15th August 2018 , do hereby give this warranty that the medical");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "devices here-under described as sold  by me  and  contained in the bill of sale,");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "invoice,bill of lading or other document describing the medical devices referred");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "to herein do not  contravene in any way the provisions of the DRAP Act,2012");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "and the rules framed there-under.");
        
       
        $rep->fontSize += 2;
        $rep->NewLine(+2); 
        $rep->NewLine();
        	$rep->fontSize -= 2;
        $rep->TextColLines(3, 7, "".$memo, -2);
       			$rep->fontSize += 2;

        $rep->Text($mcol + 50, "____________________________");

        $rep->NewLine();
        $rep->Text($mcol + 50, "Ch. Khaild Nawaz");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "Pharmacist/Distribution Manager");
        
        $rep->NewLine();
        $rep->Text($mcol + 50, "Date:");
        

			$rep->Font('');
			if ($email == 1)
			{
				$rep->End($email);
			}
			
	}
	
	if ($email == 0)
		$rep->End();
}