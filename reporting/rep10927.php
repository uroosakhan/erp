<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Orders
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------
function get_user_name_70123($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}




function get_part_no1 ($stock_id)
{
	$sql = "SELECT text1 FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($stock_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_customer_number_10983($debtor_no)
{
    $sql = "SELECT * FROM `".TB_PREF."crm_persons` WHERE `id` IN (
            SELECT person_id FROM `".TB_PREF."crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM `".TB_PREF."cust_branch` WHERE debtor_no=$debtor_no))";
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    return $fetch['phone'];
}
// S.H.G
function get_dn_no_10983($order_)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE type = 13 AND order_ = ".db_escape($order_);
    $query = db_query($sql, "Error");
    while($GetRef = db_fetch($query))
    {
        if($reference != '')
            $reference .= ',';
            
        $reference .= $GetRef['reference'];
    }
    return $reference;
}
print_sales_orders();

function print_sales_orders()
{
    global $path_to_root, $SysPrefs;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
 //   $pictures = $_POST['PARAM_4'];
    $print_as_quote = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    	$cols = array(3, 72, 340, 380, 450,520);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'right', 'right');
	
    $params = array('comments' => $comments, 'print_quote' => $print_as_quote);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
    {

        if ($print_as_quote == 0)
            $rep = new FrontReport(_("SALES ORDER"), "SalesOrderBulk", user_pagesize(), 9, $orientation);
        else
            $rep = new FrontReport(_("QUOTE"), "QuoteBulk", user_pagesize(), 9, $orientation);
    }
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {
        $myrow = get_sales_order_header($i, ST_SALESORDER);
        if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
            continue;
        }
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1)
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
        // $rep->SetHeaderType('Header1090');
        $rep->currency = $cur;
        $rep->Font();
        if ($print_as_quote == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            if ($print_as_quote == 1)
            {
                $rep->title = _('QUOTE');
                $rep->filename = "Quote" . $i . ".pdf";
            }
            else
            {
                $rep->title = _("SALES ORDER");
                $rep->filename = "SalesOrder" . $i . ".pdf";
            }
        }
        else
            $rep->title = ($print_as_quote==1 ? _("QUOTE") : _("SALES ORDER"));
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
         
          $rep->SetHeaderType('Header10927');
        $rep->NewPage();

// 	{
// // 		$image = company_path() . '/images/'. $rep ->company['coy_logo'];
// 		$imageheader = company_path() . '/images/Footer.png';
// //		if (file_exists($image))
// //		{
// //			display_error("gj01");
// 		//$rep->NewLine();
// 		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
// 			$rep->NewPage();
// 			$rep->AddImage($image, $rep->cols[1] +300, $rep->row +225, null,$rep->company['logo_w'], $rep->company['logo_h']);

// 		$rep->AddImage($imageheader, $rep->cols[1] -55, $rep->row -478, 510,20, $SysPrefs->pic_height);
// //		echo '<center><img src='headers.PNG' ></center>';

// //			$rep->AddImage($imageheader, $rep->cols[1] +320, $rep->row +580, 100, $SysPrefs->pic_height);
// //		$rep->Text(cols[1] +300, $rep->company['coy_name'], $icol);
// //				$rep->row -= $SysPrefs->pic_height;
// 		$rep->NewLine();
// 		//}
// 	}




        $result = get_sales_order_details($i, ST_SALESORDER);
        $SubTotal = 0;
        $items = $prices = array();
        while ($myrow2=db_fetch($result))
        {
            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
                $total_discount += ($myrow2["discount_percent"]*100);
                $oldrow = $rep->row;
            $rep->TextCol(0, 1,	get_part_no1($myrow2['stk_code']), -2);
                       // $rep->TextCol(0, 1,	$myrow['order_no'], -2);

            
            $rep->TextColLines(1, 2, $myrow2['description'], -2);
        $newrow = $rep->row;
				$rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $rep->TextCol(2, 3,	$DisplayQty, -2);
                $pref = get_company_pref();
                $rep->TextCol(3, 4,	$DisplayPrice, -2);
                $rep->TextCol(4, 5,	$DisplayNet, -2);
            }
            $rep->row = $newrow;
            	if ($myrow2['text7'] != "")
			{
			//	$rep->NewLine();
			
				$rep->TextColLines(1, 2, "Serial#  ".$myrow2['text7'], -2);
// 
			}
            if ($rep->row < $rep->bottomMargin + (17 * $rep->lineHeight))
                $rep->NewPage();
        }
        // if ($myrow['comments'] != "")
        // {
        //     $rep->NewLine();
        //     $rep->TextColLines(1, 5, $myrow['comments'], -2);
        // }
        $DisplaySubTot = number_format2($SubTotal,$dec);
        

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESORDER;
   
   $rep->NewLine(-6);
        $rep->TextCol(3, 4, _("Sub-total"), -2);
        $rep->TextCol(4, 5,	$DisplaySubTot, -2);
        $rep->NewLine();
        // if($myrow["discount1"] != 0)
        // {
            $rep->TextCol(3, 4, _("Discount"), -2);
            $rep->TextCol(4, 5,	price_format($total_discount), -2);
        //      $rep->NewLine();
        // }
        // if($myrow["discount2"] != 0)
        // {
        //     $rep->TextCol(3, 6, _("Discount"), -2);
        //     $rep->AmountCol(6, 7,	$myrow["discount2"], $dec);
        // }
        $rep->NewLine();
        if ($myrow['freight_cost'] != 0.0)
        {
            $DisplayFreight = number_format2($myrow["freight_cost"],$dec);
            $rep->TextCol(3, 4, _("Shipping"), -2);
            $rep->TextCol(4, 5,	$DisplayFreight, -2);
            $rep->NewLine();
        }
        
        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal -$myrow["discount2"] , $dec);
        

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
                    // if ($first)
                    // {
                    //     $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
                    //     $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                    //     $rep->NewLine();
                    // }
                    
                    $rep->TextCol(3, 4, $tax_type_name, -2);
                    $rep->TextCol(4, 5,	$DisplayTax, -2);
                    $first = false;
                }
                else
                    $rep->TextCol(3, 4, _("Included") . " " . $tax_type_name . " " . _("Amount"). ": " , -2);
                $rep->TextCol(4, 5,	$DisplayTax , -2);
            }
            else
            {
                     
                $SubTotal += $tax_item['Value'];
                $rep->TextCol(3, 4, $tax_type_name, -2);
                $rep->TextCol(4, 5,	$DisplayTax, -2);
            }
      
            $rep->NewLine();
        }

        $rep->NewLine();

        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal -$myrow["discount1"] - $myrow["discount2"] , $dec);
        $rep->Font('bold');
        // $rep->TextCol(3, 6, _("TOTAL ORDER INCL.TAXES").' '.'(' .$rep->formData['curr_code'].')', - 2);
        // $rep->TextCol(6, 7,	$DisplayTotal, -2);
        	$rep->MultiCell(74, 20, "  TOTAL (",1, 'L', 0, 2, 427,588, true);
	
	
		$rep->MultiCell(90, 20, "  ".$rep->formData['curr_code'].")",0, 'L', 0, 2, 463,588, true);

		$rep->MultiCell(64.5, 20, "      ".($DisplayTotal),1, 'L', 0, 2, 500,588, true);

	
		$rep->MultiCell(386, 20, "",1, 'L', 0, 2, 40,588, true);
		
		$rep->MultiCell(150, 15, "Terms And Conditions :",0, 'L', 0, 2, 40,610, true);
        $rep->Font();
        $rep->MultiCell(535, 100, " 

1. Product should be received in good physical condition along with original purchase invoice and complete packing with accessories
while sent for RMA Claim.

2. Components warranty period is given below:
(i) Toshiba HDD 3.5 2nd Year's Limited
(ii) Toshiba HDD 2.5 3rd Year's Limited
(iii) A-Data 3 Years Limited warranty.
(iv) Fit bit 30 Days Warranty.
(V) Gigabyte 3 Years Limited Warranty.

3. Charges may apply for limited warranty in 2nd or 3rd year.
4. Issuances of credit note in 2nd or 3rd year may apply depreciation charges.",0, 'L', 0, 2, 40,600, true);

// 		$rep->MultiCell(400, 25, "Received by:__________________",0, 'L', 0, 2, 35,770, true);
// 			$rep->MultiCell(400, 25, "Prepared by:  ____________________",0, 'L', 0, 2, 400,770, true);
			
// 				$rep->MultiCell(400, 25, "Checked by: _____________________",0, 'L', 0, 2, 400,820, true);
// 			$rep->MultiCell(400, 25, "Received by: ______________",0, 'L', 0, 2, 40,765, true);
// 			$rep->MultiCell(400, 25, "Prepared by: _______________",0, 'L', 0, 2, 430,765, true);
			
// 				$rep->MultiCell(400, 25, "Checked by: ______________",0, 'L', 0, 2, 220,765, true);
$rep->Font('bold');
				$rep->MultiCell(400, 25, "Prepared by: _______________",0, 'L', 0, 2, 445,760, true);

        $rep->MultiCell(400, 25, "Received By: ______________",0, 'L', 0, 2, 300,760, true);
        
        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
        		
    
        
        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    
    ///hareem
 
    
    
    if ($email == 0)
        $rep->End();
}

