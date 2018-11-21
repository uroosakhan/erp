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
function get_payment_terms_names_rep($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator =" .db_escape($id);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}

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

	$cols = array(4, 20, 85, 125, 170, 275, 335,390,420, 455,520);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left',	'left',	'left',	'left',	'left',	'right', 'right', 'right');

	$params = array('comments' => $comments, 'packing_slip' => $packing_slip);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 8, $orientation);
		else
			$rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 8, $orientation);
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
				$rep = new FrontReport("", "", user_pagesize(), 8, $orientation);
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
			$rep->SetHeaderType('Header11031');
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
			$S_no = 0;
        $total_qty = 0;
        $total_carton = 0;
        $total_isued_carton = 0;
        $total_isued_qty = 0;
        $delivery = get_customer_trans($myrow['trans_no'],13);
        $so = get_sales_order_header($myrow['order_'],30);
        $myformatdue_date =date('d F, Y', strtotime($myrow['due_date']));
        $myformatso_date =date('d F, Y', strtotime($myrow['ord_date']));
        $myformatdoc_date =date('d F, Y', strtotime($myrow['document_date']));
        $myformatpo_date =date('d F, Y', strtotime($myrow['po_date']));
        $myformatdo_date =date('d F, Y', strtotime($myrow['document_date']));
        $logo = company_path() . "/images/IBP_Logo_1.png";
        $rep->AddImage($logo, 40,  770, -40, 50.5);
//$this->fontSize += 10;

        $rep->multicell(300,20,"D.O Date: " ,0,'L',0,0,435,80);
        $rep->multicell(90,20,"".$myformatdoc_date ,0,'R',0,0,478,80);
        $rep->multicell(300,20,"D.O No:" ,0,'L',0,0,435,65);
        $rep->multicell(90,19, "".$myrow['document_number'],0,'R',0,0,478,65);
        $rep->multicell(135,19, $myrow['address']."",0,'L',0,0,40,135);
        $rep->multicell(135,20,"".$myrow['delivery_address'] ,0,'L',0,0,435,135);
        $rep->multicell(150,19, $myrow['DebtorName'],0,'L',0,0,120,120);
        $rep->multicell(100,10,"Invoice To" ,0,'C',1,0,40,80);
        $rep->multicell(100,10,"CUSTOMER ID:" ,0,'L',0,0,40,120);
        $rep->multicell(130,10,"Shipping Address" ,0,'C',1,0,435,115);

        $rep->multicell(90,19, "".get_payment_terms_names_rep($myrow['payment_terms']),0,'L',0,0,73,259);
        $rep->Font('bold');
        $rep->multicell(100,40,"SHIPPING TERMS" ,0,'L',0,0,50,240);
        $rep->multicell(120,40,"CONTACT NO" ,0,'L',0,0,260,240);
        $rep->multicell(120,40,"SALES OFFICER " ,0,'L',0,0,150,240);
        $rep->multicell(115,40,"SALES MAN " ,0,'L',0,0,380,240);
        $rep->multicell(115,40,"CONTACT NO" ,0,'L',0,0,490,240);
        $rep->multicell(100,40,"S.O NO" ,0,'L',0,0,70,195);
        $rep->multicell(120,40,"S.O DATE" ,0,'L',0,0,160,195);
        $rep->multicell(120,40,"P.O NO" ,0,'L',0,0,255,195);
        $rep->Font('');
        $rep->multicell(200,19, "".$myformatdo_date,0,'L',0,0,395,215);
        $rep->MultiCell(100, 10, "".$sales_order['h_text5'] ,0, 'R', 0, 2, 175,215, true);//po date
        $rep->Font('bold');
        $rep->multicell(115,40,"P.O DATE" ,0,'L',0,0,328,195);
        $rep->Font('');
        $rep->MultiCell(100, 10, "".$myformatpo_date ,0, 'L', 0, 2, 310,215, true);//po date
        $rep->multicell(100,19, "".$sales_order['reference'],0,'L',0,0,70,215);
        $rep->multicell(200,19, "".$myformatso_date,0,'L',0,0,140,215);
        $rep->MultiCell(410, 30, "".$sales_order['h_text4'] ,0, 'L', 0, 2, 169,260, true);//sale officer
        $rep->MultiCell(300,19, "".$sales_order['phone'],0,'L',0,0,265,260);
        $rep->MultiCell(300,19, "".$sales_order['h_text2'],0,'L',0,0,495,260);
        $rep->MultiCell(300,19, get_salesman_name($myrow['salesman']),0,'L',0,0,390,260);

        $rep->multicell(525,20,"" ,0,'L',1,0,40,190);
        $rep->multicell(525,20,"" ,0,'L',1,0,40,235);
        $rep->Font('bold');
        $rep->multicell(115,40,"DELIVERY DATE" ,0,'L',0,0,400,195);
        $rep->multicell(115,40,"P.O ISSUED BY" ,0,'L',0,0,490,195);
        $rep->Font('');
        $rep->multicell(100,40,"".$sales_order['f_comment1'],0,'L',0,0,495,215);
        $rep->Font('b');

        $rep->multicell(520,10,"Units" ,0,'L',0,0,378,289);
        $rep->multicell(520,10,"Cartons" ,0,'L',0,0,417,289);

        $rep->multicell(520,10,"Units" ,0,'L',0,0,480,289);
        $rep->multicell(520,10,"Cartons" ,0,'L',0,0,519,289);
        $rep->multicell(525,25,"" ,0,'L',1,0,40,278);

        $rep->multicell(115,40,"S.No" ,0,'L',0,0,41,280);
        $rep->multicell(115,40,"BARCODE" ,0,'L',0,0,65,280);
        $rep->multicell(115,40,"Product" ,0,'L',0,0,123,280);
        $rep->multicell(115,40,"Brand" ,0,'L',0,0,162,280);
        $rep->multicell(115,40,"Description" ,0,'L',0,0,208,280);
        $rep->multicell(115,40,"Pack Size" ,0,'L',0,0,320,280);
        $rep->multicell(115,40,"Order" ,0,'L',0,0,378,280);
        $rep->multicell(115,40,"Quantity" ,0,'L',0,0,416,280);
        $rep->multicell(115,40,"SCHEME" ,0,'L',0,0,471,280);
        $rep->multicell(115,40,"Quantity" ,0,'L',0,0,519,280);
        $rep->Font('');
        $rep->NewLine(1);
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
                $S_no ++;
                $ordered_qty_cartons = $qty / $myrow2['carton'];
                $ordered_scheme_cartons = $myrow2['bonus'] / $myrow2['carton'];

                $rep->TextCol(0, 1,	$S_no, -2);
				$rep->TextCol(2, 3,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
                $rep->TextCol(1, 2,	$myrow2['text3'], -2);
                $rep->TextCol(3, 4,	get_category_name($myrow2['category_id']), -2);
                $rep->TextCol(5, 6, $myrow2['carton']."  ".$myrow2['text4']." Gms", -2);
                $rep->TextCol(6, 7, "     ".$qty, -2);

                $rep->TextCol(6, 8, "                    ".price_format($ordered_qty_cartons), -2);
                $rep->Amountcol(8, 9, $myrow2['bonus'],$dec);
                $rep->Amountcol(9, 10, $ordered_scheme_cartons,$dec);
                $rep->TextColLines(4, 5, $myrow2['StockDescription'], -2);
                $total_qty += $qty;
                $total_carton += $myrow2['bonus'];
                $total_isued_carton += $ordered_scheme_cartons;
                $total_isued_qty += $ordered_qty_cartons;
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{
				    
			     
			   
//			  $rep->Amountcol(2, 3,	$qty ,$dec);
			
					
				
//                $item=get_item($myrow2['stk_code']);
//                if($pref['alt_uom'] == 1)
//                {
//                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
//                }
//                else
//                {
//                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
//                }
//
//					if ($packing_slip == 0)
//					{
//						$rep->TextCol(4, 5,	$DisplayPrice, -2);
//						$rep->TextCol(5, 6,	$DisplayDiscount, -2);
//						$rep->TextCol(6, 7,	$DisplayNet, -2);
//					}

                }
				$rep->row = $newrow;
				// $rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (14 * $rep->lineHeight))
					$rep->NewPage();
			}
        $logo1 = company_path() . "/images/img.PNG";

        $rep->NewLine(3);
        // $rep->AddImage($logo1, 310, $rep->row , -25, 10);
   $rep->Font('b');
        $rep->TextCol(5, 8, "TOTAL ORDER QUANTITY (PCS)" , - 2);
           $rep->Font();
        $rep->TextCol(9, 10, "".price_format($total_qty) , - 2);
        $rep->NewLine();
//        $rep->NewLine();
           $rep->Font('b');
        $rep->TextCol(5, 8, "TOTAL SCHEME QUANTITY (PCS)" , - 2);
           $rep->Font('');
        $rep->TextCol(9, 10, "".price_format($total_carton) , - 2);
        $rep->NewLine();
        $rep->NewLine();

        $total_isued_carton1 = $total_qty + $total_carton;
           $rep->Font('b');
        $rep->TextCol(5, 8, "TOTAL QUANTITY ISSUED (PCS)" , - 2);
           $rep->Font();
        $rep->TextCol(9, 10, "".price_format($total_isued_carton1) , - 2);
        $rep->NewLine();



        $total_issued_carton = $total_isued_carton + $total_isued_qty;
           $rep->Font('b');
        $rep->TextCol(5, 8, "TOTAL QUANTITY ISSUED (CTN)" , - 2);
           $rep->Font();
        $rep->TextCol(9, 10, "".price_format($total_issued_carton) , - 2);


        $rep->NewLine(3);
        $rep->TextCol(5, 12, "                                                           Receiver Details" , - 2);
        $rep->NewLine(2);
//        $rep->NewLine(-5);
        $rep->TextCol(6, 13, "Receiver Name    ___________________" , - 2);
        $rep->NewLine(2);
        $rep->TextCol(6, 13, "Receiver Date    ___________________" , - 2);
        $rep->NewLine(3);
        $rep->TextCol(5, 13, "                                               Receiver Stamp & Signature" , - 2);

//
        $rep->NewLine(2);
        $rep->TextCol(1, 3, "___________________" , - 2);
        $rep->TextCol(2, 6, "                           ___________________" , - 2);
        $rep->TextCol(3, 9, "                                                             ___________________" , - 2);
        $rep->NewLine();
        $rep->TextCol(1, 3, "       Checked By" , - 2);
        $rep->TextCol(3, 6, "            Warehouse Incharge" , - 2);
        $rep->TextCol(4, 7, "                                                     Sales Man" , - 2);
        $rep->NewLine(4);
                $rep->AddImage($logo1, 40, $rep->row , -10, 20.5);
        $rep->NewLine(-0.5);

        $rep->TextCol(1, 3, "DRIVER" , - 2);
        $rep->TextCol(3,5, "     DRIVER #" , - 2);
        $rep->TextCol(4,5, "                   VEHICLE" , - 2);
        $rep->TextCol(5,7, "             VEHICLE NO" , - 2);
        $rep->TextCol(6,10, "                                  TOTAL QTY ISSUED(CTN)" , - 2);
        $rep->NewLine(+2);
        $rep->TextCol(3,6, "      ".$sales_order['f_text2'] , - 2);
        $rep->TextCol(1,3, $sales_order['f_text1'] , - 2);
        $rep->TextCol(4,6, "                    ".$sales_order['f_text3'] , - 2);
        $rep->TextCol(5,8, "           ".$sales_order['f_text4'], - 2);
        $rep->NewLine(-2);
        $rep->NewLine(4);
        
        $rep->AddImage($logo1, 40, $rep->row , -10, 20.5);
        $rep->NewLine(-0.5);
        $rep->TextCol(1, 4, "          TRANSPORTER NAME" , - 2);
        $rep->TextCol(4, 5, "                       TR NO" , - 2);
        $rep->TextCol(5, 7, "                TR DATE" , - 2);
        $rep->NewLine(2);

        $rep->TextCol(5, 7, "                ".$sales_order['f_text8'] , - 2);
        $rep->TextCol(4,5,"                       ". $sales_order['f_text7'], - 2);
        $rep->TextCol(1,4, "          ".$sales_order['f_text6'], - 2);
        $memo = get_comments_string(ST_CUSTDELIVERY, $i);
			if ($memo != "")
			{
				$rep->NewLine();
				$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);

    		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
			if ($packing_slip == 0)
			{
//				$rep->TextCol(3, 6, _("Sub-total"), -2);
//				$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
				if ($myrow['ov_freight'] != 0.0)
				{
					$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
//					$rep->TextCol(3, 6, _("Shipping"), -2);
//					$rep->TextCol(6, 7,	$DisplayFreight, -2);
					$rep->NewLine();
				}	
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
//								$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
								$rep->NewLine();
    						}
//							$rep->TextCol(3, 6, $tax_type_name, -2);
//							$rep->TextCol(6, 7,	$DisplayTax, -2);
							$first = false;
    					}
//    					else
//							$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
					}
    				else
    				{
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
					}
					$rep->NewLine();
    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
				}
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

