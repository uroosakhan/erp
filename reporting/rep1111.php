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

//function item_img_names($stock_id)
//{
//	$stock_id = strtr($stock_id, "><\\/:|*?", '________');
//	return clean_file_name($stock_id);
//}

function get_description($stk_code)
{
	$sql = "SELECT line.description FROM ".TB_PREF."sales_order_details line,".TB_PREF."stock_master master 
	WHERE line.stk_code = master.stock_id
	AND line.trans_type = 32
	AND line.stk_code=".db_escape($stk_code);

	$db = db_query($sql, "could not get customer");

	$ft = db_fetch_row($db);

	return $ft[0];
}
print_sales_quotations();

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
     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
	$cols = array(4, 60, 205, 250, 315, 355, 400, 455, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left',  'left', 'left', 'left', 'right');
     }
     else{
         	$cols = array(4, 60, 225, 290, 325, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');
     }

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

		$rep->SetHeaderType('Header_jasco');
		$rep->NewPage();
		$rep->SetHeaderType('Header_jasco_cover_letter');
		$rep->NewPage();
		$rep->SetHeaderType('Header1111');
		$rep->NewPage();


		$result = get_sales_order_details($i, ST_SALESQUOTE);
		$SubTotal = 0;
		$items = $prices = array();
		while ($myrow2=db_fetch($result))
		{
		     $item=get_item($myrow2['stk_code']);
			     $pref = get_company_prefs();
		    
		      if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
		    	$qty=$myrow2['quantity'] * $myrow2['con_factor'];
		    	else
		    	$qty=$myrow2['quantity'];
		    	
		    
			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
			   user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = number_format2($qty,get_qty_dec($myrow2['stk_code']));
			$DisplayNet = number_format2($Net,$dec);
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			$rep->TextCol(0, 1,	$myrow2['stk_code'], -2);


			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
			{    $item=get_item($myrow2['stk_code']);
			     $pref = get_company_prefs();
			     
			 
			   $rep->Amountcol(3, 4,	$qty,$dec);
				
                $myrow233 = get_company_item_pref('con_factor');
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(4, 5,	$myrow2['units_id'], -2);
                    if($myrow233['sale_enable'] == 1)
                    $rep->TextCol(5, 6,	$myrow2['con_factor'], -2);
                    
                }
                else{
                    $rep->TextCol(4, 5,	$myrow2['units'], -2);
                }
                if($myrow233['sale_enable'] == 1){
                    $rep->TextCol(6, 7,	$DisplayPrice, -2);
//                    $rep->TextCol(2, 3,	$DisplayDiscount, -2);
                    $rep->TextCol(7, 8,	$DisplayNet, -2);
                }
                else{
                     $rep->TextCol(5, 6,	$DisplayPrice, -2);
//                    $rep->TextCol(2, 3,	$DisplayDiscount, -2);
                    $rep->TextCol(6, 7,	$DisplayNet, -2);
                }


			}
			$rep->row = $newrow;


//			if ($pictures)
//			{
//				$image = company_path(). "/images/.jpg";
//				if (file_exists($image))
//				{
//
//					if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
//						$rep->NewPage();
//					$rep->AddImage($image, $rep->cols[1], $rep->row - $SysPrefs->pic_height, 0, $SysPrefs->pic_height);
//					$rep->row -= $SysPrefs->pic_height;
//					$rep->NewLine();
//				}
//			}
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		$DisplaySubTot = number_format2($SubTotal,$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESQUOTE;
			$myrow3 = get_company_item_pref('con_factor');
           if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
      	$rep->TextCol(5, 7, _("Sub-total"), -2);
		$rep->TextCol(7, 8,	$DisplaySubTot, -2);
           }
           else{
               $rep->TextCol(3, 6, _("Sub-total"), -2);
	        	$rep->TextCol(6, 7,	$DisplaySubTot, -2);
           }
		$rep->NewLine();
		if ($myrow['freight_cost'] != 0.0)
		{
			$DisplayFreight = number_format2($myrow["freight_cost"],$dec);
			$rep->TextCol(3, 6, _("Shipping"), -2);
			$rep->TextCol(6, 7,	$DisplayFreight, -2);
			$rep->NewLine();
		}	
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		if ($myrow['tax_included'] == 0) {
		      if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
			$rep->TextCol(5, 7, _("TOTAL ORDER EX VAT"), - 2);
			$rep->TextCol(7, 8,	$DisplayTotal, -2);
		      }
		      else{
		          $rep->TextCol(3, 6, _("TOTAL ORDER EX VAT"), - 2);
		          $rep->TextCol(6, 7,	$DisplayTotal, -2);
		      }


			$rep->NewLine();
			  if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
			if($myrow['disc1'] != 0)
				$rep->TextCol(5, 7, _("Discount ".$myrow['disc1']."%"), - 2);
			else
				$rep->TextCol(5, 7, _("Discount"), - 2);

			$rep->TextCol(7, 8, $myrow['discount1'], -2);
			  }
			  else{
			      	if($myrow['disc1'] != 0)
				$rep->TextCol(3, 6, _("Discount ".$myrow['disc1']."%"), - 2);
			else
				$rep->TextCol(3, 6, _("Discount"), - 2);

			$rep->TextCol(6, 7, $myrow['discount1'], -2);
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
						$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
						$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
					$rep->TextCol(3, 6, $tax_type_name, -2);
					$rep->TextCol(6, 7,	$DisplayTax, -2);
					$first = false;
				}
				else
					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
				$rep->TextCol(5, 7, $tax_type_name, -2);
				$rep->TextCol(7, 8,	$DisplayTax, -2);
				}
				else{
				    	$rep->TextCol(3, 6, $tax_type_name, -2);
				$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
			}
			$rep->NewLine();
		}

		$rep->NewLine();

		$DisplayTotal = ($myrow["freight_cost"] + $SubTotal - $myrow['discount1']);
		$rep->Font('bold');
		if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
		$rep->TextCol(5, 7, _("TOTAL ORDER VAT INCL."). ' ' . $rep->formData['curr_code'], - 2);
		$rep->TextCol(7, 8,	number_format2($DisplayTotal, $dec), -2);
		}
		else{
		    	$rep->TextCol(3, 6, _("TOTAL ORDER VAT INCL."). ' ' . $rep->formData['curr_code'], - 2);
		$rep->TextCol(6, 7,	number_format2($DisplayTotal, $dec), -2);
		}
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
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


	$rep->SetHeaderType('Header_jasco_last_page');
	$rep->NewPage();
	$stock_id = get_sales_order_details($i, ST_SALESQUOTE);

	while ($pictures_new=db_fetch($stock_id)) {
//		$description=$pictures_new['description'];
//		$explode_description = explode("-",$description);
//
//		$picture = $explode_description[1];
//		display_error($explode_description[0]);
//
////	if ($pictures)
////	{
//		//$logos= company_path(). "/images/" . item_img_name($myrow2['stk_code']) . ".jpg";
//	$logos = company_path() . "/images/".$picture.".jpg";
//		$companyCol = $rep->endLine - 630;
//		$rep->row -= $rep->lineHeight - 750;
//		$rep->AddImage($logos, $companyCol, $rep->row, 670, -1);
		//$rep->NewLine(+55);
	//}

//		if ($pictures)
//		{
		$rep->row -= $SysPrefs->pic_height-470;
			$image = company_path(). "/images/" . item_img_name($pictures_new['stk_code']) . ".jpg";
			if (file_exists($image))
			{

				if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
					$rep->NewPage();
				$rep->AddImage($image, $rep->cols[1]-70, $rep->row - $SysPrefs->pic_height, 300, $SysPrefs->pic_height+250);
				//$rep->row -= $SysPrefs->pic_height-600;
				$rep->NewLine();
			}
//		}
//

	}
	}
	if ($email == 0)
		$rep->End();
}