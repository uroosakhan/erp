<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_deliveries();

//----------------------------------------------------------------------------------------------------

function print_deliveries()
{
	global $path_to_root, $SysPrefs;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$email = $_POST['PARAM_2'];
	$packing_slip = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

    $cols = array(2, 40, 160, 220, 280, 327,  400 ,460, 500);

	// $headers in doctext.inc
    $aligns = array('center',	'left',	'left', 'left', 'right', 'right', 'right', 'right');

	$params = array('comments' => $comments, 'packing_slip' => $packing_slip);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 9, $orientation);
		else
			$rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 9, $orientation);
	}
    if ($orientation == 'L')
    	recalculate_cols($cols);
	for ($i = $from; $i <= $to; $i++)
	{
			if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
				continue;
			$myrow = get_customer_trans($i, ST_CUSTDELIVERY);
			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				if ($packing_slip == 0)
				{
					$rep->title = _('DELIVERY NOTE');
					$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
				}
				else
				{
					$rep->title = _('PACKING SLIP');
					$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
				}
			}
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
			$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
			$rep->SetHeaderType('Header11033');
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
			$c = 0;
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

				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($qty,get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//				$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
                $c ++;
                $rep->TextCol(2, 3,	$myrow2['stock_id'], -2);
                $rep->TextCol(0, 1,	$c, -2);
				$oldrow = $rep->row;
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{



                    $item=get_item($myrow2['stock_id']);
                    $rep->TextCol(4, 5,	$DisplayQty, -2);
                    $total_qty += $myrow2["quantity"];
                    $rep->TextCol(6, 7,	$DisplayPrice, -2);
                    $rep->TextCol(3, 4,	"       ".$item['text1'], -2);
                    $DisplayNet = $item['text1'] * $DisplayQty;
                    $rep->TextCol(5, 6,	$DisplayNet, -2);
                    $amount = $DisplayPrice * $DisplayNet;
                    $rep->TextCol(7, 8,	$amount, -2);
                    $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
					
				
//                $item=get_item($myrow2['stk_code']);



				}
                $rep->NewLine();
                if ($rep->row < $rep->bottomMargin +(22 * $rep->lineHeight))
                {
                    $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-120,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-310,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-310, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-371,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-371, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-490,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-490, $rep->row);
                    $rep->LineTo($rep->pageWidth - $rep->rightMargin-50,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-50, $rep->row);


                    $rep->Line($rep->row);
                    $rep->NewPage();
                }

            }

        $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-120,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-310,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-310, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-371,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-371, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-490,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-490, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-50,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-50, $rep->row);
        $rep->Line($rep->row);


        $memo = get_comments_string(ST_CUSTDELIVERY, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);

//    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
//			if ($packing_slip == 0)
//			{
////				$rep->TextCol(3, 6, _("Sub-total"), -2);
////				$rep->TextCol(6, 7,	$DisplaySubTot, -2);
//				$rep->NewLine();
//				if ($myrow['ov_freight'] != 0.0)
//				{
//					$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
////					$rep->TextCol(3, 6, _("Shipping"), -2);
////					$rep->TextCol(6, 7,	$DisplayFreight, -2);
//					$rep->NewLine();
//				}
                $rep->TextCol(1, 2, _("Grand Total"), -2);
                $rep->TextCol(4, 5, $total_qty, -2);
                $rep->NewLine();
                $DisplaySubTot = number_format2($SubTotal);
                $DisplayFreight = number_format2($myrow["freight_cost"],$dec);

                if($myrow["discount1"] != 0)
                {
                    $rep->TextCol(3, 5, _("Discount"), -2);
                    $rep->TextCol(6, 8,	$myrow["discount1"], -2);
                    $rep->NewLine();
                }
                if($myrow["discount2"] != 0)
                {
                    $rep->TextCol(3, 5, _("Discount"), -2);
                    $rep->AmountCol(6, 8,	$myrow["discount2"], $dec);
                }
                $rep->NewLine();
                $rep->TextCol(3, 5, _("Sub-total"), -2);
                $rep->TextCol(6, 8,	$DisplaySubTot, -2);
                $rep->NewLine(1);
                $rep->TextCol(3, 5, _("Shipping"), -2);
                $rep->TextCol(6, 8,	$DisplayFreight, -2);
                $rep->NewLine();
                $rep->NewLine();
				$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
				$first = true;
    			while ($tax_item = db_fetch($tax_items))
    			{
    				if ($tax_item['amount'] == 0)
    					continue;
    				$DisplayTax = number_format2($tax_item['amount'], $dec);
 
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
								$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
								$rep->TextCol(6, 8,	number_format2($tax_item['net_amount'], $dec), -2);
								$rep->NewLine();
    						}
							$rep->TextCol(3, 6, $tax_type_name, -2);
							$rep->TextCol(6, 8,	$DisplayTax, -2);
							$first = false;
    					}
    					else
							$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
					}
    				else
    				{
						$rep->TextCol(3, 6, $tax_type_name, -2);
						$rep->TextCol(6, 8,	$DisplayTax, -2);
					}
					$rep->NewLine();
    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
				$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. VAT"). ' ' . $rep->formData['curr_code'], - 2);
				$rep->TextCol(6, 8,	$DisplayTotal, -2);
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				}	
				$rep->Font();
//			}

        $rep->MultiCell(525, 30, "Terms And Conditions : " ,0, 'L', 0, 2, 40,350, true);//S.no
        $rep->MultiCell(800, 30,  $myrow['comments'] ,0, 'L', 0, 2, 40,370, true);//S.no
        $rep->MultiCell(525, 30, "Account Details As follows: " ,0, 'L', 0, 2, 40,488, true);//S.no
        $rep->MultiCell(200, 30, "".$myrow['f_text1'],0, 'L', 0, 2, 40,505, true);//S.no
        $rep->MultiCell(200, 30, $myrow['f_text2'] ,0, 'L', 0, 2, 200,505, true);//S.no
        $user =get_user_id($row['trans_no'],ST_SALESINVOICE);
        $rep->MultiCell(400, 25, "Signature:__________________",0, 'L', 0, 2, 35,630, true);
        $rep->MultiCell(400, 25, "Customer Signature:__________________",0, 'L', 0, 2, 350,630, true);

        if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

