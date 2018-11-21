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

function get_user_name_11011($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time($trans_no,$type)
{
    $sql= "SELECT user, UNIX_TIMESTAMP(stamp) as unix_stamp FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}
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

	$cols = array(4, 25, 80, 390, 450, 500);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_pref('curr_default');

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
			$rep->SetHeaderType('Header1109');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
			$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
			$rep->NewPage();
        $total_qty=0;
   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
			$sr =1;
			while ($myrow2=db_fetch($result))
			{
			    
				if ($myrow2["quantity"] == 0)
					continue;
					
				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
				$total_qty += $myrow2["quantity"];
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				$rep->TextCol(0, 1,	$sr++, -2);
				$rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['StockDescription']."\n".$myrow2['text1'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{			
					$rep->TextCol(3, 4,	$DisplayQty, -2);
					$pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                    if($pref['alt_uom'] == 1)
                    {
                        $rep->TextCol(4, 5, $myrow2['units_id'], -2);
                    }
                    else {
                        $rep->TextCol(4, 5, $myrow2['units'], -2);
                    }
					if ($packing_slip == 0)
					{
						//$rep->TextCol(4, 5,	$DisplayPrice, -2);
						//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
						//$rep->TextCol(6, 7,	$DisplayNet, -2);
					}
				}	
				$rep->row = $newrow;
				//$rep->NewLine(1);
				  if ($rep->row < $rep->bottomMargin +  (5 * $rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 39.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->leftMargin, 39.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                      $rep->LineTo($rep->pageWidth - $rep->rightMargin,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                      $rep->LineTo($rep->pageWidth - $rep->rightMargin-502,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-502, $rep->row);
                      $rep->LineTo($rep->pageWidth - $rep->rightMargin-449,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-449, $rep->row);
                      $rep->LineTo($rep->pageWidth - $rep->rightMargin-119,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-119, $rep->row);
                      $rep->LineTo($rep->pageWidth - $rep->rightMargin-55,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-55, $rep->row);



//                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-106,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-106, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-160,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-160, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-197,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-197, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-250,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-250, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-289,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-289, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-360,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-360, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }
			}


        $rep->LineTo($rep->leftMargin, 39.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-502,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-502, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-449,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-449, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-119,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-119, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-55,  39.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-55, $rep->row);
//



//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-289,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-289, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-360,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-360, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
        $rep->Line($rep->row);
 			$memo = get_comments_string(ST_CUSTDELIVERY, $i);

// 			if ($memo != "")
// 			{
// 				$rep->NewLine();
// 				$rep->TextColLines(1, 5, $memo, -2);
// 			}
 			$rep->NewLine();
        $rep->font('b');
        $rep->TextCol(2, 3, "Total Qty  ");
        $rep->TextCol(3, 4, number_format2($total_qty,get_qty_dec($myrow2['stock_id'])));
        $rep->font('');




//        $rep->MultiCell(320, 10,"Total Qty  ", 1, 'R', 0, 1, 115,716);
//$rep->MultiCell(60, 10,number_format2($total_qty,get_qty_dec($myrow2['stock_id'])), 1, 'R', 0, 1, 435,716);
//$rep->MultiCell(70, 12,"", 1, 'R', 0, 1, 495,716);
//$rep->MultiCell(23, 12,"", 1, 'R', 0, 1, 40,716);
//$rep->MultiCell(23, 12,"", 1, 'R', 0, 1, 40,716);
//$rep->MultiCell(52, 12,"", 1, 'R', 0, 1, 63,716);
//        $rep->TextCol(2, 3,	$total_qty, -2);


        $DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($myrow["ov_freight"],$dec);

    // 		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;

        if ($packing_slip == 0)
			{
			//	$rep->TextCol(3, 6, _("Sub-total"), -2);
				//$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
				//$rep->TextCol(3, 6, _("Shipping"), -2);
				//$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
				$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
				$first = true;
//    			while ($tax_item = db_fetch($tax_items))
//    			{
//    				if ($tax_item['amount'] == 0)
//    					continue;
//    				$DisplayTax = number_format2($tax_item['amount'], $dec);
//
// 					if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
// 		   				$tax_type_name = $tax_item['tax_type_name'];
// 		   			else
// 		   				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";
//
// 					if ($tax_item['included_in_price'])
//    				{
//   						if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
//    					{
//    						if ($first)
//    						{
//								$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
//								$rep->NewLine();
//    						}
//							$rep->TextCol(3, 6, $tax_type_name, -2);
//							$rep->TextCol(6, 7,	$DisplayTax, -2);
//							$first = false;
//    					}
//    					else
//							$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
//					}
//    				else
//    				{
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
//					}
//					$rep->NewLine();
//    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
			//	$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. GST"), - 2);
			//	$rep->TextCol(6, 7,	$DisplayTotal, -2);
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				}
      $rep->Font();
$user =get_user_id_time($myrow["trans_no"],ST_CUSTDELIVERY);
            $array =explode(' ', $user['stamp']);





//            $rep->NewLine(6);
//            $rep->TextCol(0, 9,get_user_name_11011($user['user'])." ". sql2date($array[0]), -2);
//
//            $rep->Font('b');
//	$rep->NewLine(0.5);
//        $rep->TextCol(0, 9,	_("___________________                                                                     ___________________                                                                    ___________________"), -2);
//
//	$rep->NewLine();
//        $rep->TextCol(0, 9,	_("Prepared By                                                                    Approved By                                                                 GM/Factory"), -2);
//
//	$rep->NewLine(3);
//        $rep->TextCol(0, 9,	_("________________"), -2);
//
//	$rep->NewLine();
//            $rep->TextCol(0, 9,	_("________________"), -2);
//        $rep->TextCol(0, 9,	_("Received By"), -2);
//
//	$rep->NewLine(-7);
//
//				$rep->Font();
//
             $rep->MultiCell(100, 10,"Remarks: ",0, 'L', 0, 2, 40, 340, true);
            $rep->MultiCell(485, 20,htmlspecialchars_decode($memo),0, 'L', 0, 2, 80, 340, true);



            $rep->NewLine();
            $rep->font('b');

//            $rep->NewLine(3);
            $rep->TextCol(0, 3, "      ".get_user_name_11011($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp'])), -2);
            $rep->NewLine(0.4);
            $rep->TextCol(0, 3, "   ____________________", -2);
            $rep->TextCol(2, 4, "                                                 ____________________", -2);
            $rep->TextCol(3, 5, "   ____________________", -2);
            $rep->NewLine();
            $rep->TextCol(0, 3, "      PREPARED BY", -2);
            $rep->TextCol(2, 4, "                                                        CHECKED BY    ", -2);
            $rep->TextCol(3, 5, "GM / FACTORY       ", -2);
            $rep->NewLine();
            $rep->NewLine();
            $rep->TextCol(0, 3,"   ____________________", -2);

            $rep->NewLine();

            $rep->TextCol(0, 3,	"      RECEIVED BY", -2);

//            $rep->TextCol(0, 2, "     Prepared by", -2);
//            $rep->TextCol(2, 5, "Checked by               ", -2);
//            $rep->TextCol(5, 8, "Authorised by            ", -2);

        }
        
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	
	if ($email == 0)
		$rep->End();
}

?>