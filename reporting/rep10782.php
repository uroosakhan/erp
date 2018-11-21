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
	global $SysPrefs, $db_connection;

	$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");

	$sql = "SELECT trans.trans_no, trans.reference
		FROM ".TB_PREF."debtor_trans trans 
			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
		WHERE trans.type=".ST_SALESINVOICE
		." AND ISNULL(voided.id)";
// 		if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'NOMI')

    		$sql .=	" AND trans.trans_no >=".db_escape(/*get_reference(ST_SALESINVOICE, $from)*/$from)
    			." AND trans.trans_no <=".db_escape(/*get_reference(ST_SALESINVOICE, $to)*/$to);
		$sql .=	" ORDER BY trans.tran_date, trans.$ref";

	return db_query($sql, "Cant retrieve invoice range");
}

print_invoices();

//----------------------------------------------------------------------------------------------------

function get_user_name_70123($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}


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
//if($pref['batch'] == 1){
//    //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
//	$cols = array(4, 60, 175, 220,260, 315, 355, 395, 450, 475);
//
//	// $headers in doctext.inc
//	$aligns = array('left',	'left',	'left',	'left',	'right', 'center', 'right', 'right', 'right');
//}
//else{
      //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
	$cols = array(2, 33, 150, 175, 215, 250,  288,320 ,355,385,410, 445, 480, 515);
	$cols2 = array(2, 33, 150, 175, 215, 250,  288,320 ,355,385,410, 445, 480,  515);

	// $headers in doctext.inc
    $aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');
    $aligns2 = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');

//}
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
            $rep->Info($params, $cols, null, $aligns, $cols2, null,$aligns2);

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->SetHeaderType('Header10782');
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

   			$result = get_customer_trans_details(ST_SALESINVOICE, $row['trans_no']);
			$SubTotal = 0;
        $total_qty = 0;
        $c=0;
        while ($myrow2=db_fetch($result))
			{
			    	$pref=get_company_prefs();
				if ($myrow2["quantity"] == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
                $c++;
				$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);

                $oldrow = $rep->row;
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{
				    $rep->AmountCol(2, 3,	$myrow2['mmWidth']);
                    $rep->AmountCol(3, 4,	$myrow2['ActualWidth'], $dec);
                    $rep->AmountCol(4, 5,	$myrow2['mmLength']);
                    $rep->AmountCol(5, 6,	$myrow2['ActualLength'], $dec);
                    $rep->AmountCol(6, 7,	$myrow2['StdWidth']);
                    $rep->AmountCol(7, 8,	$myrow2['StdLength']);
                    $rep->AmountCol(8, 9,	$myrow2['Sft'], $dec);
                    $rep->AmountCol(9, 10,	$myrow2['Pcs'], $dec);
                    $rep->AmountCol(10, 11,	$myrow2['quantity'], $dec);
                    $rep->AmountCol(11, 12, $myrow2['unit_price'], $dec);
                    $rep->AmountCol(12, 13, $myrow2['quantity'] * $myrow2['unit_price'], $dec);
                    $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);

                    $total_Pcs += $myrow2['Pcs'];
                    $total_Sft += $myrow2['quantity'];
                    $total += $myrow2['quantity'] * $myrow2['unit_price'];
                }
                $rep->NewLine();
                if ($rep->row < $rep->bottomMargin +(22 * $rep->lineHeight))
                {
                    $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-44,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-44, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-79,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-79, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-114,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-114, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-139,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-139, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-169,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-169, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-204,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-204, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-235,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-235, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-274,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-274, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-308,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-308, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-348,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-348, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-385,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-385, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-495,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-495, $rep->row);
                    $rep->Line($rep->row);
                    $rep->NewPage();
                }

            }

        $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-44,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-44, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-79,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-79, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-114,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-114, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-139,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-139, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-169,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-169, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-204,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-204, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-235,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-235, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-274,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-274, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-308,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-308, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-348,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-348, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-385,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-385, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-495,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-495, $rep->row);
        $rep->Line($rep->row);


        $memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}
        $rep->NewLine();

        $rep->TextCol(1, 2, _("Grand Total"), -2);
        $rep->AmountCol(9, 10,	$total_Pcs, $dec);
        $rep->AmountCol(10, 11,	$total_Sft, $dec);
        $rep->AmountCol(12, 13, $total, $dec);

        $rep->NewLine();
        $DisplaySubTot = number_format2($SubTotal);
        $DisplayFreight = number_format2($myrow["freight_cost"],$dec);

        if($myrow["discount1"] != 0)
        {
            $rep->TextCol(6, 9, _("Discount"), -2);
            $rep->TextCol(10, 11,	$myrow["discount1"], -2);
            $rep->NewLine();
        }
        if($myrow["discount2"] != 0)
        {
            $rep->TextCol(6, 9, _("Discount"), -2);
            $rep->AmountCol(10, 11,	$myrow["discount2"], $dec);
        }
        $rep->NewLine();
        $rep->TextCol(6, 9, _("Sub-total"), -2);
        $rep->TextCol(10, 11,	$DisplaySubTot, -2);
        $rep->NewLine(1);
        $rep->TextCol(6, 9, _("Shipping"), -2);
        $rep->TextCol(10, 11,	$DisplayFreight, -2);
        $rep->NewLine();
        $rep->NewLine();
   			$DisplaySubTot = number_format2($SubTotal,$dec);

			// set to start of summary line:
//    		$rep->row = $summary_start_row;
//
//			$doctype = ST_SALESINVOICE;
//    		$rep->row = $summary_start_row;
//			$rep->cols[2] += 20;
//			$rep->cols[3] += 20;
//			$rep->aligns[3] = 'left';
            $pref=get_company_prefs();

		$total_amount = abs($SubTotal-$myrow['discount1']-$myrow['discount2'] +$DisplayFreight);

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
							$rep->TextCol(5, 9, _("Total Tax Excluded"), -2);
							$rep->TextCol(10, 11,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}

						$rep->TextCol(5, 9, $tax_type_name, -2);
						$rep->TextCol(10, 11,	$DisplayTax, -2);
						$first = false;
    				}
				}
    			else
    			{	$rep->NewLine(-1);
					$rep->TextCol(10, 11,	$DisplayTax, -2);
					$rep->TextCol(5, 9, $tax_type_name, -2);
				}
				$rep->NewLine();
    		}
    // 		$rep->NewLine();
 $rep->TextCol(5, 9,  _("TOTAL INVOICE"). ' (' . $rep->formData['curr_code'].')', -2);
		$rep->Amountcol(10, 11, $total_amount, $dec);
		
	$pref=get_company_prefs();
    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"] - ($myrow['discount1'] + $myrow['discount2'])),$dec);
			$rep->Font('bold');
			if (!$myrow['prepaid']) $rep->Font('bold');
		
            if($pref['batch'] ==1){
				$rep->TextCol(6, 8, $rep->formData['prepaid'] ? _("TOTAL ORDER VAT INCL.") : _("TOTAL INVOICE"). ' ' . $rep->formData['curr_code'], - 2);
			$rep->TextCol(10, 11,$DisplayTotal, -2);
            }
			if ($rep->formData['prepaid'])
			{
				$rep->NewLine();
				$rep->Font('bold');
				$rep->TextCol(3, 6, $rep->formData['prepaid']=='final' ? _("THIS INVOICE") : _("TOTAL INVOICE"), - 2);
				$rep->TextCol(6, 8, number_format2($myrow['prep_amount'], $dec), -2);
			}
			$words = price_in_words($rep->formData['prepaid'] ? $myrow['prep_amount'] : $myrow['Total']
				, array( 'type' => ST_SALESINVOICE, 'currency' => $myrow['curr_code']));
			if ($words != "")
			{
				$rep->NewLine(1);
				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
			}
//        $rep->MultiCell(525, 30, "Terms And Conditions : " ,0, 'L', 0, 2, 40,350, true);//S.no
//        $rep->MultiCell(800, 30,  $myrow['comments'] ,0, 'L', 0, 2, 40,370, true);//S.no
//        $rep->MultiCell(525, 30, "Account Details As follows: " ,0, 'L', 0, 2, 40,488, true);//S.no
//        $rep->MultiCell(200, 30, "".$myrow['f_text1'],0, 'L', 0, 2, 40,505, true);//S.no
//        $rep->MultiCell(200, 30, $myrow['f_text2'] ,0, 'L', 0, 2, 200,505, true);//S.no
//					$user =get_user_id($row['trans_no'],ST_SALESINVOICE);
//			$rep->MultiCell(400, 25, "Signature:__________________",0, 'L', 0, 2, 35,630, true);
//			$rep->MultiCell(400, 25, "Customer Signature:__________________",0, 'L', 0, 2, 350,630, true);

			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

