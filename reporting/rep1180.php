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
function get_phone1($customer_id)
{
    $sql = "SELECT `phone` FROM ".TB_PREF."crm_persons WHERE `name`=".db_escape($customer_id);

    $result = db_query($sql, "could not get customer phone");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_acc_type($account_code)
{
    $sql = "SELECT account_type, bank_account_name FROM ".TB_PREF."bank_accounts
     WHERE account_code =".db_escape($account_code);

    $result = db_query($sql, "could not get customer phone");

    $row = db_fetch($result);

    return $row;
}
function get_discount_name($h_text3)
{
    $sql = "SELECT ".TB_PREF."chart_master.account_name FROM ".TB_PREF."chart_master, ".TB_PREF."discount 
    WHERE ".TB_PREF."chart_master.account_code = ".TB_PREF."discount.dis_account
    AND ".TB_PREF."discount.id =".db_escape($h_text3)
    ;

    $result = db_query($sql, "could not get customer phone");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_sales_orderstamp_time($order_no)
{
    $sql = "SELECT  order_time  FROM ".TB_PREF."sales_orders"
        ." WHERE order_no=".db_escape($order_no);

    $query= db_query($sql, "Cannot get all audit info for transaction");
    $fetch=db_fetch_row($query);
    return $fetch[0];
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

    if (!$from || !$to) return;

    $dec = user_price_dec();

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

     $cols = array(8, 30, 65, 90, 125, 160, 230);
   // $cols = array(8, 30, 65, 90, 120, 145, 400);
    // $headers in doctext.inc
    $headers = array( _("Item"), _(""), _("Qty"), _("Price"), _(" Dis%"), _("Total"."           "));
    // $aligns = array('left',	'center',	'left', 'left','left','left');
    $aligns = array('left',	'center', 'center', 'center','right','right');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');
    $message = get_company_Pref('legal_text');

    if ($email == 0)
    {
        $rep = new FrontReport(_('ESTIMATE'), "InvoiceBulk", 'POS3', '9');
        $rep->SetHeaderType('Header1180');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $headers, $aligns);
    }
    for ($i = $from; $i <= $to; $i++)
    {
        if (!exists_customer_trans(ST_SALESINVOICE, $i))
            continue;
        $sign = 1;
        $myrow = get_customer_trans($i, ST_SALESINVOICE);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        $branch = get_branch($myrow["branch_code"]);
        $sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize());
            $rep->SetHeaderType('Header1180');
            $rep->currency = $cur;
            $rep->Font();
            $rep->title = _('');
            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
            $rep->Info($params, $cols, null, $aligns);
        }
        else
            $rep->title = _('');

        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], false);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $SubTotal = $Discount = $Qty = $TotalPrice = 0;

        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;

            //if($myrow2["qty2"] == $myrow2["quantity"])
            //	{
            $DisplayPrice = number_format2($myrow2["unit_price"],0);
            //	}
            //else
            //	{
            //$DisplayPrice = number_format2(($myrow2["unit_price"]/$myrow2["qty2"])*($myrow2["quantity"]),0);
            //	}

            $DisplayQty = number_format2($sign*$myrow2["quantity"],0/*get_qty_dec($myrow2['stock_id'])*/);
            //$DisplayQty = number_format2($sign*$myrow2["qty2"],0);
            $DisplayNet = number_format2($Net, 0);

            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100) . "%";
            $line_disc_name = get_discount_name($myrow2['text1']);
            $rep->NewLine();
            //$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            $rep->fontSize -= 1;
            $rep->TextColLines(0, 2, $myrow2['StockDescription'], -2);
            $rep->NewLine(0.2);
            if($myrow2['text1'] != 16)
            $rep->TextCol(4, 5, $line_disc_name);
            $rep->fontSize += 1;
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0) {
                $rep->TextCol(2, 3,	$DisplayQty, -2);
                //$rep->TextCol(3, 4,	$myrow2['units'], -2);
                $rep->TextCol(3, 4,	$DisplayPrice, -2);
                $rep->TextCol(4, 5,	$DisplayDiscount, -2);
                $rep->TextCol(5, 6,	$DisplayNet."           ", -2);
            }

            $display_price = $myrow2["unit_price"] * $myrow2["quantity"];
            $Discount += round($display_price*$myrow2["discount_percent"]);
            $Qty += $myrow2["quantity"];
            $TotalPrice += $myrow2["unit_price"];
            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();

            $rep->NewLine(0.2);
            $rep->TextCol(0, 6,	_("........................................................................................................................."), -2);
//            $rep->NewLine(0.8);
        }

        $memo = get_comments_string(ST_SALESINVOICE, $i);
        if ($memo != "")
        {
            $rep->NewLine();
            $rep->TextColLines(1, 5, $memo, -2);
        }

        $DisplaySubTot = number_format2($SubTotal,0);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],0);

        //$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
        //	$rep->NewLine(-25);
        //	$rep->TextCol(0, 2, _("Sub-total"), -2);
        //	$rep->TextCol(2, 4,	$DisplaySubTot, -2);
        //	$rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;
//         while ($tax_item = db_fetch($tax_items))
//         {
//             if ($tax_item['amount'] == 0)
//                 continue;
//             $DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

//             if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
//                 $tax_type_name = $tax_item['tax_type_name'];
//             else
//                 $tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

//             if ($tax_item['included_in_price'])
//             {
//                 if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
//                 {
//                     if ($first)
//                     {
//                         $rep->TextCol(0, 2, _("Total Tax Excluded"), -2);
//                         $rep->TextCol(1, 2,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
//                         $rep->NewLine();
//                     }
//                     $rep->TextCol(0, 1, $tax_type_name, -2);
//                     $rep->TextCol(1, 2,	$DisplayTax, -2);
//                     $first = false;
//                 }
//                 else
//                     {
//                         // $rep->TextCol(1, 3, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
//                     	$rep->TextCol(2, 3,	$DisplayTax, -2);
//                     }
//             }
//             else
//             {
//                 $rep->NewLine(+2.2);
//                 $rep->Font('bold');
// 				$rep->TextCol(0, 4, $tax_type_name, -2);
// 			//	$rep->TextCol(4, 7,	"                    ".$DisplayTax, -2);
// 				$rep->TextCol(5, 6, $DisplayTax."           ", -2);
// 				 $rep->NewLine(-2.2);
// 				 $rep->Font('');
//             }
//             //$rep->NewLine();
//         }
//
//        $rep->NewLine();
//        $DisSubplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
//                $myrow["ov_amount"]+$myrow["ov_freight_tax"]),0);

        $DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"]+$myrow["ov_freight_tax"]-$myrow["discount1"]),0);

        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(0, 3,  _("Total"), - 2);
        $rep->TextCol(2, 3, $Qty, -2);
        //  $rep->TextCol(3, 4, $TotalPrice, -2);
        $rep->TextCol(4, 5, $Discount, -2);
        $rep->TextCol(5, 6, $DisplaySubTot."           ", -2);
        $rep->Font('');
        $rep->NewLine();
  
//        $rep->NewLine();
        $total_disc_name = get_discount_name($sales_order["h_text3"]);
        //$rep->TextCol(0, 2, _("Discount"), - 2);
        if($sales_order["total_discount_pos"]) {
            $rep->TextCol(0, 3, _("Discount") ." / ".$total_disc_name);
            $rep->TextCol(4, 5, number_format2($sales_order["total_discount_pos"]) . "%", -2);
        
        $rep->TextCol(5, 6, number_format2($myrow["discount1"])."           ", -2);
        }
        $rep->NewLine();
        $rep->TextCol(0, 3,  _("After Discount"), - 2);
        $rep->TextCol(5, 6, number_format2($SubTotal-$myrow["discount1"])."           ", -2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(0, 3,  _("GST (13%)"), - 2);
        $rep->TextCol(5, 6, number_format2($myrow["ov_gst"])."           ", -2);
        $rep->Font('');
        $rep->NewLine();
        if($myrow["ov_freight"] != 0) {
            $rep->NewLine();
            $rep->TextCol(0, 3, _("Delivery Charges"), - 2);
            $rep->TextCol(5, 6, number_format2($myrow["ov_freight"], $dec)."           ", -2);
        }

        $rep->NewLine(1.5);
        $rep->Font('bold');
        $rep->fontSize += 1;
        $bank_data = get_acc_type($sales_order["h_combo1"]);
        if($bank_data['account_type'] == 3) {
            $rep->TextCol(0, 4, _("TOTAL AMOUNT"), -2);
            $rep->TextCol(5, 6, $DisplayTotal . "         ", -2);
        }
        else{
            $rep->TextCol(0, 4, _("TOTAL AMOUNT")."-".$bank_data['bank_account_name'], -2);
            $rep->TextCol(5, 6, $DisplayTotal . "          ", -2);
        }
        $rep->fontSize -= 1;

        if($bank_data['account_type'] == 3)
        {
            $rep->NewLine(1.5);
            $rep->Font('bold');
            $rep->fontSize += 1;
            $rep->TextCol(0, 4, _("Cash Tendered"), -2);
            $rep->TextCol(5, 6, number_format2($sales_order['h_text1']) . "         ", -2);
            $rep->fontSize -= 1;
            $rep->NewLine(1.5);
            $rep->Font('bold');
            $rep->fontSize += 1;
            $rep->TextCol(0, 4, _("Cash Returned"), -2);
            $rep->TextCol(5, 6, number_format2($sales_order['h_text2']) . "         ", -2);
            $rep->fontSize -= 1;
        }

        if($message != "")
        {
            $rep->NewLine(2.5);
            $rep->Font('bold');
            $rep->TextColLines(0, 6, $message, - 25);
            $rep->Font('');
            $rep->NewLine(-2);
        }

        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 2, $myrow['curr_code'] . ": " . $words, - 2);
        }
        $rep->NewLine(3);
        $rep->Font('bold');
        $rep->TextCol(0, 2, "Wifi:", -2);
        $rep->TextCol(4, 6, $rep->company['domicile'] . "           ", -2);
        $rep->NewLine();
        $rep->TextCol(0, 6, "Phone#:                                  ".$rep->company['phone'], - 2);
        $rep->NewLine();
        $rep->TextCol(0, 6,"Website:               ".$rep->company['website'], - 2);
        $rep->NewLine();
        $rep->TextCol(0, 6, "Email:                 ".$rep->company['email'], - 2);
        $rep->NewLine();
        $rep->TextCol(0, 6, "SSTR#:                                        ".$rep->company['sst_no'], - 2);
        $rep->NewLine(2);
        $rep->TextCol(0, 6, _("---------- Powered By www.hisaab.pk ---------"), -2);
        $rep->Font('');

// 		$rep->MultiCell(252, 148, (" ").get_phone1($myrow['debtor_ref']) , 0, 'L', 0, 2, 90,87, true);


// Account Status
//Debit 
        $sql = "
SELECT
SUM(ov_amount+ov_freight) AS OutStanding
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type = 10
";
        $result = db_query($sql,"No transactions were returned");
        $bal2 = db_fetch($result);

//Credit
        $sql = "
SELECT
SUM(ov_amount) AS Payments
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type IN (12 , 11 , 2)
";

//AND type IN (11,12,2)
        $result = db_query($sql,"No transactions were returned");
        $bal3 = db_fetch($result);

        $TotalCredit = round2($bal3['Payments'], $dec); //Total credit side balance

        $TotalDebit = round2($bal2['OutStanding'], $dec); // Total debit side balance

        $CurrentAmount = number_format2($SubTotal+$myrow["ov_freight"]);

        $PreviousBalance = number_format2($TotalDebit-$TotalCredit-($SubTotal+$myrow["ov_freight"]));

        $TotalBalance2 = number_format2($TotalDebit-$TotalCredit);

        /*$rep->NewLine(5);
        $rep->TextCol(0, 2, _("Previous Balance"), -2);
        if ($PreviousBalance > 0)
        $rep->TextCol(2, 3, $PreviousBalance , -2); //previous balance
        else
        $rep->TextCol(5, 6, _("") , -2); 			
        $rep->NewLine();
        $rep->TextCol(0, 2, _("Current Amount"), -2);
        $rep->TextCol(2, 3, $CurrentAmount, -2); // Current Amount

        $rep->NewLine();
        $rep->TextCol(0, 2, _("Total Balance"), -2);
        $rep->TextCol(2, 3, $TotalBalance2,  -2); // TotalBalance
        $rep->NewLine();*/
//		$rep->NewLine(3);
        $rep->Font('');
//		$rep->Font('b');
//		$rep->fontSize += 1;
//		$rep->TextCol(0, 5, _("No Exchange Once Open, No Refund Policy"));
        //$rep->fontSize -= 1;
//		$rep->Font();
//		$rep->Font('b');
//		$rep->NewLine(4);
//		$rep->TextCol(0, 4, _("Customer Signature ....................................."), -2);

//		$rep->NewLine(-10);
        //	$rep->TextCol(0, 6, _("                       Powered By www.hisaab.pk"), - 2);
//		$rep->Font();
// force print dialog
$js .= 'print(true);';

// set javascript
$rep->IncludeJS($js);
        if ($email == 1)
        {
            $rep->End($email);
        }
        //$rep->NewLine(25);
    }

    if ($email == 0)
        $rep->End();
}

?>