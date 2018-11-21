<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Quotations
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------

print_sales_quotations();
function get_user_name_70123($user_id)
{
    $sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_salesman_name1111($id)
{
    $sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_combo1_name1111($stock_id,$name)
{
    $sql = "SELECT description FROM ".TB_PREF."combo1  WHERE combo_code =".db_escape($stock_id)."
	";

    $result = db_query($sql, "could not retreive the location name for $stock_id");


    $row = db_fetch_row($result);
    return $row[0];

}
function get_payment_terms_name1111($selected_id)
{
    $sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

    $result = db_query($sql,"could not get payment term");
    $row =db_fetch_row($result);
    return $row[0];
}
function get_shipment1111($selected_id)
{
    $sql = "SELECT  shipper_name
	 FROM ".TB_PREF."shippers  WHERE shipper_id=".db_escape($selected_id);

    $result = db_query($sql,"could not get payment term");
    $row =db_fetch_row($result);
    return $row[0];
}
function get_security_role11117($id)
{
    $sql = "SELECT description FROM ".TB_PREF."security_roles WHERE id=".($id);
    $ret = db_query($sql, "could not retrieve security roles");
    $row = db_fetch_row($ret);
    return $row[0];
}
function customer_phone_no1111($debtor_no)
{
    $sql="SELECT * FROM `0_crm_persons` WHERE `id` IN (
  SELECT person_id FROM `0_crm_contacts` WHERE `type`='customer' 
  AND `action`='general' 
  AND entity_id IN (
  SELECT branch_code FROM `0_cust_branch` WHERE debtor_no='$debtor_no')) ";

    $result = db_query($sql, "Cannot retreive a wo issue");

    return db_fetch($result);
}
function print_sales_quotations()
{
	global $path_to_root, $SysPrefs;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$pictures = $SysPrefs->print_item_images_on_quote();
	// If you want a larger image, then increase pic_height f.i.
	// $SysPrefs->pic_height += 25;
	$myrow233 = get_company_item_pref('con_factor');
	$pref = get_company_prefs();
//     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
	$cols = array(4, 25, 67, 140, 280, 360, 430);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left',  'right', 'right', 'right');
//     }
//     else{
//         	$cols = array(4, 60, 225, 290, 325, 385, 450, 515);
//
//	// $headers in doctext.inc
//	$aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');
//     }

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_("SALES QUOTATION"), "SalesQuotationBulk", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_sales_order_header($i, ST_SALESQUOTE);
		if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
			continue;
		}
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			if ($SysPrefs->print_invoice_no() == 1)
				$rep->filename = "SalesQuotation" . $i . ".pdf";
			else
				$rep->filename = "SalesQuotation" . $myrow['reference'] . ".pdf";
		}
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESQUOTE, $contacts);
		$rep->SetHeaderType('Header11117');
		$rep->NewPage();
        $summary_start_row = $rep->bottomMargin + (25 * $rep->lineHeight);

		$result = get_sales_order_details($i, ST_SALESQUOTE);
		$SubTotal = 0;
		$items = $prices = array();
        $a=1;
		while ($myrow2=db_fetch($result))
		{
			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
            $qty_total += $myrow2["quantity"];
			$DisplayNet = number_format2($Net,$dec);
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			$rep->TextCol(0, 1,	$a++, -2);
			$oldrow = $rep->row;
			$item=get_item($myrow2['stk_code']);
				$rep->TextCol(1, 2, $myrow2['stk_code'], -2);
			$rep->TextCol(2, 3, get_category_name($myrow2['category_id']), -2);
			$rep->TextColLines(3, 4, $myrow2['description']."".$item['long_description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
			{    $item=get_item($myrow2['stk_code']);
				$pref = get_company_prefs();
				if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
                $rep->TextCol(1, 2,	($myrow2['text1']), -2);
                $rep->TextCol(2, 3,	get_combo1_name1111($myrow2['combo1']), -2);
                $rep->TextCol(4, 5,	$DisplayQty, -2);
                $rep->TextCol(5, 6,	$DisplayPrice, -2);
                $rep->TextCol(6, 7,number_format2($myrow2['quantity'] * $myrow2['unit_price'],get_qty_dec($myrow2['stk_code'])), -2);
                $total_amt_ += $myrow2['quantity'] * $myrow2['unit_price'];
			}

//			$rep->row = $newrow;

//			if ($pictures)
//			{
//				$image = company_path(). "/images/" . item_img_name($myrow2['stk_code']) . ".jpg";
//				if (file_exists($image))
//				{
//					if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
//						$rep->NewPage();
//					$rep->AddImage($image, $rep->cols[1], $rep->row - $SysPrefs->pic_height, 0, $SysPrefs->pic_height);
//					$rep->row -= $SysPrefs->pic_height;
//					$rep->NewLine();
//				}
//			}
            $rep->row = $newrow;
            if ($rep->row < $summary_start_row) {

//                if ($rep->row < $rep->bottomMargin - (25 * $rep->lineHeight))
                    $rep->NewPage();
            }
//			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//				$rep->NewPage();
		}

		$rep->MultiCell(525,110,"",1,'L',0,0,40,550);
		$rep->MultiCell(525,20,"Terms and Conditions",1,'C',1,0,40,550);
        $rep->font('b');
        $rep->MultiCell(95, 10, 'TOTAL' ,0, 'L', 0, 2, 180,500, true);
        $rep->font('');
        $rep->MultiCell(50, 10,number_format($qty_total,get_qty_dec($myrow2['item_code'])) ,0, 'R', 0, 2, 349,500, true);

        if ($myrow['comments'] != "")
		{
			$rep->NewLine();
// 			$rep->TextColLines(1, 5, $myrow['comments'], -2);
			$rep->MultiCell(400,20,"".$myrow['comments'],0,'L',0,0,40,630);

		}
		$DisplaySubTot = number_format2($SubTotal,$dec);

//		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESQUOTE;
		$myrow3 = get_company_item_pref('con_factor');
		if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
//      	$rep->TextCol(5, 7, _("Sub-total"), -2);
//		$rep->TextCol(7, 8,	$DisplaySubTot, -2);
		}
		else{
//               $rep->TextCol(3, 6, _("Sub-total"), -2);
//	        	$rep->TextCol(6, 7,	$DisplaySubTot, -2);
		}
		$rep->NewLine();
		if ($myrow['freight_cost'] != 0.0)
		{
			$DisplayFreight = number_format2($myrow["freight_cost"],$dec);
//			$rep->TextCol(3, 6, _("Shipping"), -2);
//			$rep->TextCol(6, 7,	$DisplayFreight, -2);
			$rep->NewLine();
		}
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		if ($myrow['tax_included'] == 0) {
			if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
//			$rep->TextCol(5, 7, _("TOTAL ORDER EX VAT"), - 2);
//			$rep->TextCol(7, 8,	$DisplayTotal, -2);
			}
			else{
//		          $rep->TextCol(3, 6, _("TOTAL ORDER EX VAT"), - 2);
//		          $rep->TextCol(6, 7,	$DisplayTotal, -2);
			}


			$rep->NewLine();
			if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
//			if($myrow['disc1'] != 0)
//				$rep->TextCol(5, 7, _("Discount ".$myrow['disc1']."%"), - 2);
//			else
//				$rep->TextCol(5, 7, _("Discount"), - 2);

//			$rep->TextCol(7, 8, $myrow['discount1'], -2);
			}
			else{
//			      	if($myrow['disc1'] != 0)
//				$rep->TextCol(3, 6, _("Discount ".$myrow['disc1']."%"), - 2);
//			else
//				$rep->TextCol(3, 6, _("Discount"), - 2);
//
//			$rep->TextCol(6, 7, $myrow['discount1'], -2);
			}
			$rep->NewLine();
		}
		$around = $SubTotal - $myrow['discount1'];
		$tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
			$myrow['tax_group_id'], $myrow['tax_included'],  null);
		$first = true;
		foreach($tax_items as $tax_item)
		{
			if ($tax_item['Value'] == 0)
				continue;
			$DisplayTax = number_format2($tax_item['Value'], $dec);

			$tax_type_name = $tax_item['tax_type_name'];

			if ($myrow['tax_included'])
			{
				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
				{
					if ($first)
					{
					    	$rep->NewLine(-15);
						$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
						$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine(+15);
						$rep->NewLine();
					}
					$rep->NewLine(-15);
					$rep->TextCol(3, 6, $tax_type_name, -2);
					$rep->TextCol(6, 7,	$DisplayTax, -2);
					$rep->NewLine(+15);
					$first = false;
				}
				else
					$rep->NewLine(-15);
					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
					$rep->NewLine(+15);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
				    	$rep->NewLine(-15);
				$rep->TextCol(5, 7, $tax_type_name, -2);
				$rep->TextCol(7, 8,	$DisplayTax, -2);
					$rep->NewLine(+15);
				}
				else{
				    	$rep->NewLine(-15);
				    	$rep->TextCol(3, 6, $tax_type_name, -2);
				$rep->TextCol(6, 7,	$DisplayTax, -2);
				$rep->NewLine(+15);
				}
			}
			$rep->NewLine();
		}

		$rep->NewLine();

		$DisplayTotal = ($myrow["freight_cost"] + $SubTotal - $myrow['discount1']);
		$rep->Font('bold');
		if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
// 		$rep->TextCol(5, 7, _("TOTAL ORDER VAT INCL."). ' ' . $rep->formData['curr_code'], - 2);
// 		$rep->TextCol(7, 8,	number_format2($DisplayTotal, $dec), -2);
 $rep->MultiCell(50, 10,number_format2($DisplayTotal, $dec) ,0, 'R', 0, 2, 510,500, true);
		}
		else{
		  //  	$rep->TextCol(3, 6, _("TOTAL ORDER VAT INCL."). ' ' . $rep->formData['curr_code'], - 2);
// 		$rep->TextCol(6, 7,	number_format2($DisplayTotal, $dec), -2);
		        $rep->MultiCell(50, 10,number_format2($DisplayTotal, $dec) ,0, 'R', 0, 2, 510,500, true);

		}
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
		if ($words != "")
		{
			$rep->NewLine(1);
//			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}
		$rep->Font();
		if ($email == 1)
		{
			if ($SysPrefs->print_invoice_no() == 1)
				$myrow['reference'] = $i;
			$rep->End($email);
		}
//		$rep->MultiCell(525, 30, "Discount         ".$DisplayDiscount ,0, 'L', 0, 2, 330,645, true);//S.no
//		$rep->MultiCell(525, 30, "".$myrow['discount1'] ,0, 'L', 0, 2, 540,645, true);//S.no
        // $rep->MultiCell(125,20,$baccount['bank_name'],1,'L',0,0,442,570);
        // $rep->MultiCell(125,20,$baccount['bank_account_name'],1,'L',0,0,442,590);
        // $rep->MultiCell(125,20,$baccount['bank_account_number'],1,'L',0,0,442,610);
        // $rep->MultiCell(125,20,$baccount['bank_curr_code'],1,'L',0,0,442,630);
        // $rep->MultiCell(125,20,$baccount['swift_code'],1,'L',0,0,442,650);
        // $rep->MultiCell(125,20,$baccount['iban'],1,'L',0,0,442,670);

    $user =get_user_id($myrow['order_no'],ST_SALESQUOTE);
    $rep->MultiCell(200, 25, "".get_user_name_70123($user) ,0, 'L', 0, 2, 90,755, true);


	$rep->MultiCell(525, 30, 'Dear Sir, we thank you for your inquiry and we are pleased to submit our offer as follows.' ,0, 'L', 0, 2, 40,220, true);
	$rep->MultiCell(525, 30, '1.Above prices are based on quantity and prices may change as per quantity difference.' ,0, 'L', 0, 2, 40,570, true);
	$rep->MultiCell(525, 30, 'Hope the provided offer will meet your requirment and please do not hesitate to contact us if you need any further assistance or clarification.' ,0, 'L', 0, 2, 40,670, true);
	
	$rep->MultiCell(525, 30, '2.Pricing quoted are in Pakistan rupees, inclusive of all charges.' ,0, 'L', 0, 2, 40,580, true);

		$rep->MultiCell(525, 30, '3.Quote validity is of 30 days, effective from the issuing date.' ,0, 'L', 0, 2, 40,590, true);
		$rep->MultiCell(525, 30, '4.Delivery will be made within 3 to 4 weeks after confirmation of order.' ,0, 'L', 0, 2, 40,600, true);
		$rep->MultiCell(525, 30, '5.Payment terms: within 30 days of delivery.' ,0, 'L', 0, 2, 40,610, true);

			$rep->MultiCell(525, 30, '6.The quoted price may be change if there is any fluctuations in curreny rates.' ,0, 'L', 0, 2, 40,620, true);

	
	
	
	$rep->MultiCell(525, 30, 'Thanks & kind regards, ' ,0, 'L', 0, 2, 40,700, true);
		$rep->Font('bold');
// 		$rep->MultiCell(525, 30, get_salesman_name1111($myrow['salesman']),0, 'L', 0, 2, 40,755, true);
		$rep->Font();
		$rep->MultiCell(525, 30, 'Sales Manager' ,0, 'L', 0, 2, 90,767, true);
		$rep->MultiCell(525, 30, '_______________________________' ,0, 'L', 0, 2, 40,745, true);

// 		$rep->MultiCell(525, 30,'______________________________________________________________________________________________________' , 0, 'L', 0, 2, 40,770, true);

		$rep->font('b');
		

		$rep->font();
		$rep->MultiCell(525, 30,$rep->company['postal_address'], 0, 'L', 0, 2, 360,60, true);
		$rep->MultiCell(525, 30,"Tel:".$rep->company['phone']."," ."Fax:". $rep->company['fax'] , 0, 'L', 0, 2, 360,85, true);
		$rep->MultiCell(525, 30,"NTN:".$rep->company['coy_no'].","."Email:".$rep->company['email']  , 0, 'L', 0, 2, 360,98, true);

		$rep->setfont('','','16');
		$rep->SetTextColor(0, 71, 179);

		$rep->MultiCell(525, 30,$rep->company['coy_name'] , 0, 'L', 0, 2, 360,40, true);
		$rep->SetTextColor(0, 0, 0);
		$rep->font('b');
		$rep->MultiCell(525, 30, 'QUOTATION' , 0, 'L', 0, 2, 230,80, true);
		$rep->font();
		$rep->setfont('','','');
	}
	if ($email == 0)

		$rep->end();
}

