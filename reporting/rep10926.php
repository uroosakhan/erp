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

print_sales_orders();

function get_customer_name12($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}


function print_sales_orders()
{
	global $path_to_root, $SysPrefs,$db_connections;

if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RPL2'){
     include_once($path_to_root . "/reporting/includes/pdf_report_email.inc");


}
else{
	 	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
}
	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$print_as_quote = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();
$myrow233 = get_company_item_pref('con_factor');
$pref = get_company_prefs();
     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
	$cols = array(4, 50, 160, 220, 260, 320, 370, 420,440, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left','left', 'left', 'right', 'right', 'right', 'right');
     }
     else{
         	$cols = array(4, 50, 230, 300, 340, 380, 420, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left', 'right', 'right', 'right', 'right', 'right');
     }

	$params = array('comments' => $comments, 'print_quote' => $print_as_quote);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{

		if ($print_as_quote == 0)
			$rep = new FrontReport(_("SALES ORDER"), "SalesOrderBulk", user_pagesize(), 7, $orientation);
		else
			$rep = new FrontReport(_("QUOTE"), "QuoteBulk", user_pagesize(), 7, $orientation);
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
			$rep = new FrontReport("", "", user_pagesize(), 7, $orientation);
		$rep->SetHeaderType('Header10925');
		$rep->currency = $cur;
		$rep->Font();
		if ($print_as_quote == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 7, $orientation);
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
		$rep->SetHeaderType('Header10925');
		$rep->NewPage();

		$result = get_sales_order_details($i, ST_SALESORDER);
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
			{
			    $item=get_item($myrow2['stk_code']);
			    	 $pref = get_company_prefs();
                        $pack = $myrow2["quantity"] / $item['carton'];
                        $rep->Amountcol(2, 3,	$pack, $dec);
		
			   $rep->Amountcol(4, 5,	$qty ,$dec);
			
			
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    // $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                }
                else
                {
                    // $rep->TextCol(3, 4,	$myrow2['units'], -2);
                }
                	$myrow233 = get_company_item_pref('con_factor');
$pref = get_company_prefs();
     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
     
         $rep->Amountcol(5, 6,	$myrow2['con_factor'], $dec);

         if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
             $rep->TextCol(6, 7, $DisplayPrice, -2);
             $rep->TextCol(7, 8, $DisplayDiscount, -2);
             $rep->TextCol(8, 9, $DisplayNet, -2);
         }
     }
     else{

         if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
             $rep->TextCol(5, 6, $DisplayPrice, -2);
             $rep->TextCol(6, 7, $DisplayDiscount, -2);
             $rep->TextCol(7, 8, $DisplayNet, -2);
         }
     }
     

			
			}
			$rep->row = $newrow;
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
		$doctype = ST_SALESORDER;
$myrow3 = get_company_item_pref('con_factor');

        if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
            if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                // $rep->TextCol(3, 7, _("Sub-total"), -2);
                // $rep->TextCol(9, 10, $DisplaySubTot, -2);
            } else {
                // $rep->TextCol(3, 7, _("Sub-total"), -2);
                // $rep->TextCol(7, 8, $DisplaySubTot, -2);
            }
            $rep->NewLine();
            if ($myrow['freight_cost'] != 0.0) {
                $DisplayFreight = number_format2($myrow["freight_cost"], $dec);
                $rep->TextCol(4, 7, _("Shipping"), -2);
                $rep->TextCol(7, 8, $DisplayFreight, -2);
                $rep->NewLine();
            }
             $rep->Font('bold');
            	if($myrow['discount1'] != 0) {

                  $discount_value =$myrow["disc1"]/100;
 
                    $rep->MultiCell(410, 30, "".price_format(($myrow["discount1"])) ,0, 'L', 0, 2, 515,650, true);
                    $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 350,650, true);

		}
		
		if($myrow['discount2'] != 0) {

 $discount_value =$myrow["disc2"]/100;
                    $rep->MultiCell(410, 30, "".price_format($myrow["discount2"]) ,0, 'L', 0, 2, 515,650, true);
                    $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 350,650, true);

            $tot_amt =$tot_net - $myrow['discount2'];
            $rep->NewLine();
		}
		 $rep->Font('');
            $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal + $DisplayTotal - $myrow["discount1"] - $myrow["discount2"] , $dec);
            if ($myrow['tax_included'] == 0) {
                if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                    $rep->TextCol(3, 7, _("TOTAL ORDER EX GST"), -2);
                    $rep->TextCol(8, 9, $DisplayTotal , -2);
                } else {
                    $rep->TextCol(3, 7, _("TOTAL ORDER EX GST"), -2);
                    $rep->TextCol(7, 8, $DisplayTotal , -2);
                }
                $rep->NewLine();
            }

            $tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
                $myrow['tax_group_id'], $myrow['tax_included'], null);
            $first = true;
            foreach ($tax_items as $tax_item) {
                if ($tax_item['Value'] == 0)
                    continue;
                $DisplayTax = number_format2($tax_item['Value'], $dec);

                $tax_type_name = $tax_item['tax_type_name'];

                if ($myrow['tax_included']) {
                    if ($SysPrefs->alternative_tax_include_on_docs() == 1) {
                        if ($first) {
                            $rep->TextCol(3, 7, _("Total Tax Excluded"), -2);
                            $rep->TextCol(7, 8, number_format2($sign * $tax_item['net_amount'], $dec), -2);
                            $rep->NewLine();
                        }
                        if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {

                            // $rep->TextCol(5, 7, $tax_type_name, -2);
                            // $rep->TextCol(7, 8, $DisplayTax, -2);
                        } else {

                            // $rep->TextCol(4, 7, $tax_type_name, -2);
                            // $rep->TextCol(7, 8, $DisplayTax, -2);
                        }
                        $first = false;
                    }
                    // else
                    //     $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": ", -2);
                        //  $rep->TextCol(7, 8,  $DisplayTax, -2);
                } else {
                    $SubTotal += $tax_item['Value'];
                    if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                        // $rep->TextCol(5, 7, $tax_type_name, -2);
                        // $rep->TextCol(8, 9, $DisplayTax, -2);
                    } else {
                        // $rep->TextCol(4, 7, $tax_type_name, -2);
                        // $rep->TextCol(7, 8, $DisplayTax, -2);
                    }
                }
                $rep->NewLine();
                
            }
// 		$rep->MultiCell(300, 10,"Customer Name:   ". get_customer_name($myrow['debtor_no']) , 0, 'L', 0, 2, 40,125, true);

            $rep->NewLine();
            $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal - $myrow["discount1"] - $myrow["discount2"] , $dec);
            $rep->Font('bold');
            if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                $rep->TextCol(3, 7, _("TOTAL ORDER GST INCL.") . ' ' . $rep->formData['curr_code'], -2);
                $rep->TextCol(8, 9, $DisplayTotal , -2);
            } else {
                $rep->TextCol(3, 7, _("TOTAL ORDER GST INCL.") . ' ' . $rep->formData['curr_code'], -2);
                $rep->TextCol(7, 8, $DisplayTotal , -2);
            }
        }
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}	
		
		
	$rep->MultiCell(400, 25, "Term And Conditions",0, 'L', 0, 2, 65,627, true);
			$rep->MultiCell(400, 25, "______________________",0, 'L', 0, 2, 58,630, true);

				$rep->Font('');
				$rep->MultiCell(400, 25, "1- Sign this invoice and  return to head office.",0, 'L', 0, 2, 50,640, true);
$rep->MultiCell(400, 25, "2- Without last receiving new order will not proceed. ",0, 'L', 0, 2, 50,652, true);
$rep->MultiCell(400, 25, "3- Payment to be made by crossed cheque or online. ",0, 'L', 0, 2, 50,665, true);
$rep->MultiCell(300, 25, "4- official receipt must be obtained for cash payment otherwise company will not                      responsible for payments. ",0, 'L', 0, 2, 50,678, true);
$rep->MultiCell(300, 25, "5- Bank Al-habib A/c No. 10260081015184015 Allama Iqbal branch KHI. ",0, 'L', 0, 2, 50,699, true);


			
		
		
		
		$rep->Font();
	$rep->MultiCell(800, 50, "".$myrow['term_cond'], 0, 'L', 0, 2, 83, 490, true);
            $rep->MultiCell(400, 25, "Signature:__________________",0, 'L', 0, 2, 35,765, true);
			$rep->MultiCell(400, 25, "Customer Signature:__________________",0, 'L', 0, 2, 428,765, true);
		      //  $rep->MultiCell(105,15,get_salesman_name($myrow['salesman']),0,'L', 0, 2,100,270,true);
		if ($email == 1)
		{
			$rep->End($email);
		}
	}
	if ($email == 0)
		$rep->End();
}

