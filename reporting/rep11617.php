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

print_invoices();

//----------------------------------------------------------------------------------------------------
function get_price_tax ($selected_id)
{
    $sql = "SELECT price FROM ".TB_PREF."prices  WHERE stock_id=".db_escape($selected_id)." AND sales_type_id=2";

    $result = db_query($sql,"could not get payment term");

    $ft = db_fetch_row($result);
    return $ft[0];
}

function get_payment_terms_name($selected_id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

    $result = db_query($sql,"could not get payment term");

    return db_fetch_row($result);
}

function get_phone($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

    $db  = db_query($sql,"item prices could not be retreived");
    $ft = db_fetch_row($db);
    return $ft[0];


}

function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.* FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 1";
    $result = db_query($sql, 'error');
    return db_fetch($result);

}

function print_invoices()
{
    global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $pay_service = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
 //   $dec = user_price_dec();
    $dec = 0;

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(0, 45, 145, 190, 245, 290, 360, 400, 450);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'left', 'left', 'left','left', 'right', 'right');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    for ($i = $from; $i <= $to; $i++)
    {
        $TotalPriceWithsalesTax = 0;
        $total_amount=0;
        $total_price_before_disc=0;
        $myrow3 = get_tax_rate_1();
        $discTotal = 0;
        $disc_amt = 0.0;
        $TotalAmount=0;
        $NetTotal=0;
        
        $SubTotal=$total_item_gst=0.0;
        $new_total=0;
        $total_tax=0;
        if (!exists_customer_trans(ST_SALESINVOICE, $i))
            continue;
        $sign = 1;
        $myrow = get_customer_trans($i, ST_SALESINVOICE);
        $myrow4 = get_phone($myrow['debtor_no']);


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
        $rep->SetHeaderType('Header11617');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);
        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        
        $baccount['payment_service'] = $pay_service;
        
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);

        
       
     
//		$rep->MultiCell(150, 10, $myrow4['phone'] , 1, 'L', 0, 2, 340,210, true);
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;
            $DisplayPrice = number_format2($myrow2["unit_price"],get_qty_dec($myrow2['stock_id']));
//				if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] !=1)
            $tax_price = get_price_tax($myrow2['stock_id']);

            if($myrow['tax_group_id'] != 0)
                $PriceWithsalesTax = number_format2(($tax_price*$myrow2["quantity"]*$myrow3['unit_price']),$dec);
            else
                $PriceWithsalesTax = 0;

            $TotalPriceWithsalesTax += $PriceWithsalesTax;
            $DisplayQty = number_format2($sign*$myrow2["quantity"], get_qty_dec($myrow2['stock_id']));
            $Net = round2($sign * (((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"] )),
                user_price_dec());

            $disc_amt = round2($sign * ((($myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"])),
               0);
            $SubTotal += $Net;
            $discTotal += $disc_amt;

            $DisplayNet = ($Net);
            if ($myrow2["discount_percent"] == 0)
            {
                $DisplayDiscount ="";
                $LessDiscount = $myrow2["unit_price"]*$myrow2["discount_percent"];
                $DiscountAmount = $myrow2["unit_price"] - $LessDiscount;
                $TotalAmount = $DiscountAmount * $myrow2["quantity"];
            }
            else
            {
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
                $LessDiscount = $myrow2["unit_price"]*$myrow2["discount_percent"];
                $DiscountAmount = $myrow2["unit_price"] - $LessDiscount;
                $TotalAmount = $DiscountAmount * $myrow2["quantity"];
            }


            $oldrow = $rep->row;
            $rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
            $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) ||  $no_zero_lines_amount == 0)
            {
                $rep->TextCol(2, 3,	$DisplayQty.' '.$myrow2['units'], -2);
                $rep->TextCol(3, 4,	$DisplayPrice, -2);
                $rep->TextCol(4, 5,	$DisplayDiscount, -2);
                $rep->TextCol(5, 6,	price_format($TotalAmount), -2);
                
                $price_before_disc =$DisplayQty*$DisplayPrice;
                
            $total_price_before_disc    +=$myrow2["quantity"]*$myrow2["unit_price"] ;

        $total_amount+= $TotalAmount;

//		if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] !=1)
            
            
                if($myrow['tax_group_id'] != 0)
                {
                    $rep->TextCol(6, 7,	$myrow3['rate']." %", -2);


                   $total_tax = $TotalAmount*($myrow3['rate']/100);

$tax1 =$myrow3['rate']/100;
$inc_tax1=($myrow3['rate']+100)/100;
$new_price1=get_price_tax($myrow2['stock_id']);

              $total_tax1 = ($new_price1/($inc_tax1))*($myrow3['rate']/100);


                    $rep->TextCol(7, 8,	price_format($DisplayQty*$total_tax1), -2);



$new_price=get_price_tax($myrow2['stock_id']);
$new_total=$DisplayQty*$new_price;
$to_tax +=($new_total);
$tax =$myrow3['rate']/100;
$inc_tax=($myrow3['rate']+100)/100;

//$tax_amount =(($SubTotal)*$tax);
$total_item_gst =(($to_tax/$inc_tax)*$tax);


                }
                else
                {
                    $rep->TextCol(6, 7,	'', -2);
                    $rep->TextCol(7, 8,	'', -2);
                }

                $rep->TextCol(8, 9,	price_format($DisplayNet+($DisplayQty*$total_tax1)), -2);
//					$rep->TextCol(5, 6,	$DisplayDiscount, -2);
//					$rep->TextCol(6, 7,	$DisplayNet, -2);
            }
            $rep->Line($rep->row  - 4);
            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }
  
        $memo = get_comments_string(ST_SALESINVOICE, $i);
        if ($memo != "")
        {
            $rep->NewLine();
			$rep->TextColLines(1, 5, $memo, -2);
        }

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;

//			$rep->TextCol(3, 6, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
//			$rep->NewLine();
//			$rep->TextCol(3, 6, _("Shipping"), -2);
//			$rep->TextCol(6, 7,	$DisplayFreight, -2);
        $rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;
//    		while ($tax_item = db_fetch($tax_items))
//    		{
//    			if ($tax_item['amount'] == 0)
//    				continue;
//    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
//
//    			if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
//    				$tax_type_name = $tax_item['tax_type_name'];
//    			else
//    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";
//
//    			if ($tax_item['included_in_price'])
//    			{
//    				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
//    				{
//    					if ($first)
//    					{
////			$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
////		  $rep->TextCol(6, 7,number_format2($sign*$tax_item['net_amount'], $dec), -2);
//							$rep->NewLine();
//    					}
////						$rep->TextCol(3, 6, $tax_type_name, -2);
////						$rep->TextCol(6, 7,	$DisplayTax, -2);
//						$first = false;
//    				}
////    				else
////$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount"). ": " . $DisplayTax, -2);
//				}
////    			else
////    			{
////					$rep->TextCol(3, 6, $tax_type_name, -2);
////					$rep->TextCol(6, 7,	$DisplayTax, -2);
////				}
//				$rep->NewLine();
//    		}
        $rep->NewLine(7);
        $DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] + $myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
       
       $DisplayTotal_tax=$SubTotal+$total_item_gst;
       
        $rep->Font('bold');
//	    $rep->TextCol(3, 6, _("TOTAL INVOICE"), - 2);
        $rep->TextCol(4, 5, price_format($discTotal), -2);
        $rep->TextCol(5, 6, price_format($total_amount), -2);
        $rep->TextCol(7, 8, price_format($total_item_gst), -2);
        $rep->TextCol(8, 9, price_format($SubTotal+$total_item_gst) , -2);

        $total_tax=($SubTotal+$total_item_gst);

        $ov_freight = $myrow["ov_freight"];
        $Total_Amount = $total_amount + $discTotal ;
        $disc = $total_amount + $ov_freight;
        $NetTotal = price_format($ov_freight + $total_tax);
 
        $rep->NewLine(2);
        $rep->TextCol(6,8, "Gross Val. Before Disc.:", -2);
        $rep->TextCol(8,9, price_format($total_price_before_disc) , -2);
        $rep->NewLine();
        $rep->TextCol(6,8, "Trade Disc.:", -2);
        $rep->TextCol(8,9, "(".price_format($discTotal).")", -2);
        $rep->NewLine();
        $rep->TextCol(6,8, "Transportation Charges:", -2);
        $rep->TextCol(8,9, price_format($myrow["ov_freight"]), -2);
        $rep->NewLine();
        $rep->TextCol(6,8, "Gross Val. After Disc:", -2);
        $rep->TextCol(8,9, price_format($disc), -2);
        $rep->NewLine();
        $rep->TextCol(6,8, "GST:", -2);
        $rep->TextCol(8,9, price_format($total_item_gst), -2);
        $rep->NewLine();
        $rep->TextCol(6,8, "Net Amount:", -2);
        $rep->TextCol(8,9, $NetTotal, -2);
 
        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
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
    
    if ($email == 0)
        $rep->End();
}

?>