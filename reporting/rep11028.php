<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// draft version!
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

$packing_slip = 0;
//----------------------------------------------------------------------------------------------------

print_deliveries();

//----------------------------------------------------------------------------------------------------

function print_deliveries()
{
	global $path_to_root, $packing_slip, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

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
/*
	$cols = array(4, 60, 225, 300, 325, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left', 'right', 'right', 'right');
*/


$cols = array(4, 40, 100, 240, 325, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'left', 'right', 'right');


	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 10, $orientation);
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
					$rep->title = _('DELIVERY ORDER');
					$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
				}
				else
				{
					$rep->title = _('PACKING SLIP');
					$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
				}
			}
			$rep->SetHeaderType('Header11028');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
			$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
			$SerialNumber = 0;
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;
					
				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
				$SerialNumber += 1;
				
				
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				
				$rep->TextCol(0, 1,	$SerialNumber, -2);
				$rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{			
						$nop = $DisplayQty/$myrow2['carton'];
						
						$tot_nop += $nop;
						
						
						$rep->TextCol(3, 4,	$nop, -2); // 	
						$rep->TextCol(4, 5,	$myrow2['carton'], -2); // uom				
						$rep->TextCol(5, 6,	$DisplayQty, -2); // quantity
						$total_qty += $DisplayQty; // shariq
						$rep->TextCol(6, 7,	$myrow2['units'], -2); // uom
					
					if ($packing_slip == 0)
					{
						
					
				//$rep->TextCol(5, 6,	$DisplayPrice, -2); //price
			
			
				//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
			
			
				//$rep->TextCol(6, 7,	$DisplayNet, -2); // total
						
						
				
				
						//$rep->TextCol(4, 5,	$DisplayPrice, -2);
						//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
						//$rep->TextCol(6, 7,	$DisplayNet, -2);
						
						
					}
				}	
				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_CUSTDELIVERY, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				 $rep->multicell(300,20,"".$memo,0,'L',0,0,340,273);
				// $rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($myrow["ov_freight"],$dec);

    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
			if ($packing_slip == 0)
			{
				$rep->NewLine(-16);
				$rep->Font(b);
				$rep->TextCol(4, 6, _("Total Quantity :"), -2);
				$rep->TextCol(6, 7,	$total_qty, -2);
				$rep->NewLine();
				$rep->TextCol(4, 6, _("Total No of Packing :"), -2);
				$rep->TextCol(6, 7,	$tot_nop, -2);
				$rep->Font();				
				//$rep->TextCol(3, 6, _("Sub-total"), -2);
				//$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
				//$rep->TextCol(3, 6, _("Shipping"), -2);
				//$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
/*				$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
				$first = true;
    			while ($tax_item = db_fetch($tax_items))
    			{
    				if ($tax_item['amount'] == 0)
    					continue;
    				$DisplayTax = number_format2($tax_item['amount'], $dec);
 
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
								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
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
		*/		
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
				//$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. GST"), - 2);
				//$rep->TextCol(6, 7,	$DisplayTotal, -2);
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				}	
				$rep->Font();
			}
			
			
			
			$rep->NewLine(6);
		
		$rep->Font('bold');

        $rep->multicell(200,100,"  Received By ______________________",0,'L',0,0,367,480);
        $rep->multicell(200,100,"  Prepared By ______________________",0,'L',0,0,50,480);
        $rep->multicell(30,100,"  Gate Copy",0,'R',0,0,100,515);
        $rep->multicell(50,100,"  Customer Copy",0,'R',0,0,175,501);
        $rep->multicell(45,100,"  Factory Copy",0,'R',0,0,260,515);
        $rep->multicell(45,100,"  Bill Copy",0,'R',0,0,330,515);
        $rep->multicell(48,100,"  Accounts Copy",0,'R',0,0,420,501);
        $rep->multicell(20,20,"",1,'L',0,0,70,515);
        $rep->multicell(20,20,"",1,'L',0,0,150,515);
        $rep->multicell(20,20,"",1,'L',0,0,240,515);
        $rep->multicell(20,20,"",1,'L',0,0,310,515);
        $rep->multicell(20,20,"",1,'L',0,0,390,515);

        $rep->Font('');
			
				$rep->NewLine(3);
		/*	$rep->Font(b);
			$rep->TextCol(0, 6, _("Karachi"), - 2);
			$rep->TextCol(4, 6, _("Lahore"), - 2);
			$rep->Font();
			$rep->NewLine();
			$rep->TextCol(0, 6, _("B-2/A, First Floor, Carlton Court P-52,"), - 2);
			$rep->TextCol(4, 7, _("360 F-2, Wapda Town"), - 2);
			$rep->NewLine();
			$rep->TextCol(0, 6, _("Main Korangi Road, DHA Phase-II Extension"), - 2);
			$rep->TextCol(4, 7, _("Lahore, Pakistan"), - 2);
			$rep->NewLine();
			$rep->TextCol(0, 6, _("Karachi, Pakistan"), - 2);
			$rep->TextCol(4, 7, _("Tel: 042-35188347 & 8, Fax: 042-35968347"), - 2);
			$rep->NewLine();
			$rep->TextCol(0, 6, _("Tel: 021-35383258 & 9 Fax: 021-35383260"), - 2);
			$rep->TextCol(4, 7, _("Email: finance@appliedinside.com"), - 2);
			$rep->NewLine();
			$rep->TextCol(0, 7, _("Email: aps.khi@appliedinside.com"), - 2);*/
			//$rep->NewLine(-7);		
			
			
			
				
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>