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
function get_customer_number_11025($debtor_no)
{
    $sql = "SELECT * FROM `".TB_PREF."crm_persons` WHERE `id` IN (
            SELECT person_id FROM `".TB_PREF."crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM `".TB_PREF."cust_branch` WHERE debtor_no=$debtor_no))";
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    return $fetch['phone'];
}
function get_dn_no_11025($trans_no)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = 10 AND trans_no = ".db_escape($trans_no);
    $query = db_query($sql, "Error");
    while($GetRef = db_fetch($query))
    {
        if($reference != '')
            $reference .= ',';
            
        $reference .= $GetRef['reference'];
    }
    return $reference;
}
function get_part_no1($stock_id)
{
	$sql = "SELECT text1 FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
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

	$cols = array(3, 72, 320, 380, 450,520);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'right', 'right');
    
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
			$rep->SetHeaderType('Header11025');
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
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
		  			$total_discount += $myrow2["discount_percent"]*100;
				$rep->TextCol(0, 1,	get_part_no1($myrow2['stock_id']), -2);
				//	$rep->NewLine();

				$oldrow = $rep->row;
 $rep->TextColLines(1, 3, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{
				    
			     
			   
			  $rep->Amountcol(2, 3,	$qty ,$dec);

					if ($packing_slip == 0)
					{
						$rep->TextCol(3, 4,	$DisplayPrice, -2);
				// 		$rep->TextCol(5, 6,	$DisplayDiscount, -2);
						$rep->TextCol(4, 5,	$DisplayNet, -2);
					
						}
		

				
				//	$rep->NewLine();

			}
				$rep->row = $newrow;
					if ($myrow2['text7'] != "")
			{
			//	$rep->NewLine();
			
				$rep->TextColLines(1, 2, "Serial#  ".$myrow2['text7'], -2);
// 
			}
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}


// 			$memo = get_comments_string(ST_CUSTDELIVERY, $i);
// 			if ($memo != "")
// 			{
// 				$rep->NewLine();
// 				$rep->TextColLines(1, 5, $memo, -2);
// 			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);

    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
			if ($packing_slip == 0)
			{
			    	$rep->NewLine(4);
				$rep->TextCol(3, 4, _("Sub-total"), -2);
				$rep->TextCol(4, 5,	$DisplaySubTot, -2);
				$rep->NewLine();
				if ($myrow['ov_freight'] != 0.0)
				{
					$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
					$rep->TextCol(3, 4, _("Shipping"), -2);
					$rep->TextCol(4, 5,	$DisplayFreight, -2);
					$rep->NewLine();
				}	
					$rep->TextCol(3, 4, _("Discount "), -2);
			$rep->Amountcol(4, 5, $total_discount, $dec);
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
								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
								$rep->NewLine();
    						}
							$rep->TextCol(3, 4, $tax_type_name, -2);
							$rep->TextCol(4, 5,	$DisplayTax, -2);
							$first = false;
    					}
    					else
							$rep->TextCol(3, 4, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
					}
    				else
    				{
						$rep->TextCol(3, 4, $tax_type_name, -2);
						$rep->TextCol(4, 5,	$DisplayTax, -2);
					}
					$rep->NewLine();
    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
			//	$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. VAT"). ' ' . $rep->formData['curr_code'], - 2);
			
			
			
				$rep->MultiCell(70, 20, "  TOTAL (",1, 'L', 0, 2,425,717, true);
	
	
		$rep->MultiCell(90, 20, "    ".$rep->formData['curr_code'].")",0, 'L', 0, 2, 455,717, true);


		$rep->MultiCell(70, 20, "        ".($DisplayTotal),1, 'L', 0, 2, 495,717, true);

	
		$rep->MultiCell(385, 20, "",1, 'L', 0, 2, 40,717, true);

			
			
			
			
// 	$rep->MultiCell(400, 25, "Received by:__________________",0, 'L', 0, 2, 35,770, true);

// 		$rep->MultiCell(400, 25, "Prepared by:  ____________________",0, 'L', 0, 2, 400,770, true);
			
// 				$rep->MultiCell(400, 25, "Checked by: _____________________",0, 'L', 0, 2, 400,820, true);
			$rep->MultiCell(400, 25, "Received by: ______________",0, 'L', 0, 2, 40,765, true);
			$rep->MultiCell(400, 25, "Prepared by: _______________",0, 'L', 0, 2, 430,765, true);
			
				$rep->MultiCell(400, 25, "Checked by: ______________",0, 'L', 0, 2, 220,765, true);
				// $rep->TextCol(6, 7,	$DisplayTotal, -2);
				// $words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				// if ($words != "")
				// {
				// 	$rep->NewLine(1);
				// 	$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				// }	
				$rep->Font();
			}	
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

