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
function get_customer_information($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}

function get_user_name_70123($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_tax_rate($trans_no, $stock_id)
{
    $sql = "SELECT (unit_tax/unit_price)*100 FROM ".TB_PREF."debtor_trans_details
   WHERE debtor_trans_no = ".db_escape($trans_no)."
   AND stock_id= ".db_escape($stock_id)."
   AND debtor_trans_type = 10
   AND quantity > 0
   ";
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch_row($result);
    return $row['0'];
}

function get_tax_amount($trans_no, $stock_id)
{
    $sql = "SELECT (unit_tax * quantity) FROM ".TB_PREF."debtor_trans_details 
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
	";
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch_row($result);
    return $row['0'];
}

print_invoices();

//----------------------------------------------------------------------------------------------------

function print_invoices()
{
    global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $pictures = $_POST['PARAM_4'];
    $pay_service = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];

    if (!$from || !$to) return;

    $orientation = ('P');
    //$dec = user_price_dec();
    $dec = 0;

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(0,20, 75, 209, 225, 265, 313, 368, 410, 465,475, 560);
    $cols2 = array(0,20, 75, 209, 225, 265, 313, 368, 410, 465,475, 560);

    // $headers in doctext.inc
    $aligns = array('center','left','left','center','right','right','right','right','right','right');
    $aligns2 = array('center','left','left','center','right','right','right','right','right','right');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
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
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('INVOICE');
            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
        }

        if ($pictures)
            $rep->SetHeaderType('Header1196');
        else
            $rep->SetHeaderType('Header1197');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns, $cols2, null,$aligns2);


        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $SubTotal = 0;
        $S_no = 0;
        $total_price =0;
        $total_including_tax=0;
        $total_amount=0;
        $DisplayPq =0;
        $amount_including_tax = 0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;
            $S_no++;
            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($sign*$myrow2["quantity"],$dec);
            $DisplayPq =  $myrow2["unit_price"] * $myrow2["quantity"];
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100 ,user_percent_dec());

            $total_disc = number_format2($myrow2["discount_percent"]*$myrow2["unit_price"],user_percent_dec());
            $total_discount +=$total_disc;
            $rep->TextCol(0, 1, " ".$S_no, -2);
            $rep->TextCol(1, 2,$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            $newrow = $rep->row;
            //   $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {

                $rep->TextCol(4, 5,$DisplayPrice, -2);
                $rep->TextCol(5, 6,    $DisplayDiscount, -2);
                $vallue_discount = $DisplayPq - $total_disc; // Value excluding
                $rep->TextCol(6, 7,    $vallue_discount, $dec);
                $rep->TextCol(7, 8,    round2(get_tax_rate($i, $myrow2['stock_id']),$dec)."%", -2);
                $tax = round2(get_tax_rate($i, $myrow2['stock_id']));
                $tax1 = $tax/100;
                
                $vallue_discount1 = $vallue_discount*$tax1;
                $rep->TextCol(8, 9,    $vallue_discount1, -2);

                $including_sales_tax=  $vallue_discount + $amount_including_tax + $vallue_discount1;
                $excluding_sales_tax = $DisplayPq;
                $rep->TextCol(9, 10,   price_format($including_sales_tax), -2);


                $rep->TextCol(3, 4, " ".$DisplayQty, -2);

                $rep->TextColLines(2, 3,    $myrow2['StockDescription'], -2);

                $total_price += $myrow2["unit_price"];
                $total_value_excl_tax += $vallue_discount;
                $excluding_discount = $total_value_excl_tax ;
                $total_amount += $amount_including_tax;
                $total_including_tax += $including_sales_tax;

            }
            //$rep->row = $newrow;
            // $rep->NewLine(1);
//            if ($rep->row < $rep->bottomMargin + (22 * $rep->lineHeight))
//                $rep->NewPage();
            if ($rep->row < $rep->bottomMargin +(5*$rep->lineHeight))
            {
                $rep->LineTo($rep->leftMargin, 43.8 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-153,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-153, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-210,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-210, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-258,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-258, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-300,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-300, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-114,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-114, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-55,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-55, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-452,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-452, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-505,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-505, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-317,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-317, $rep->row);
//   $rep->NewLine(2);
                
                $rep->Line($rep->row);

                $rep->NewPage();
            }

        }
        $rep->LineTo($rep->leftMargin, 43.8 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-153,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-153, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-210,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-210, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-258,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-258, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-300,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-300, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-114,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-114, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-55,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-55, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-452,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-452, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-505,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-505, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-317,  43.8 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-317, $rep->row);
//   $rep->NewLine(2);

        $rep->Line($rep->row);
        
        $memo = get_comments_string(ST_SALESINVOICE, $i);

//        $rep->NewLine(2);

       
        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
      
        
        
        $rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
//   $rep->NewLine(-22.5);
// $rep->TextCol(1, 2, "TOTAL"." ".$rep->formData['curr_code'], -2);
//         $rep->TextCol(9, 10,"          ".price_format($total_price), -2);
//         $rep->TextCol(6, 7,price_format($excluding_discount ), -2);
        // $rep->NewLine(+22.5);
        $doctype = ST_SALESINVOICE;
        //	$rep->NewLine();

        $rep->NewLine(-17);
        $rep->Font('bold');

        $rep->NewLine(1);
        //
        //   $rep->Font('U');
        $rep->TextCol(6, 8, "Sub-Total Excluding Tax", -2);
        $rep->TextCol(9, 10, price_format($total_value_excl_tax), -2);
        $rep->NewLine();
        if($myrow["discount1"] != 0)
        {
            $rep->TextCol(6, 8, "Discount", -2);
            $rep->TextCol(9, 10, $myrow["discount1"], -2);
            $rep->NewLine(5);


        }
        else
        {
            $rep->NewLine(5);
        }
        if($myrow["discount2"] != 0)
        {
            $rep->TextCol(5, 8, "Discount", -2);
            $rep->TextCol(9, 10, $myrow["discount2"], -2);
            $rep->NewLine();

        }
        else
        {
            $rep->NewLine();
        }

        // $rep->NewLine(-1);
        $rep->Font('');
        //$rep->NewLine(-4);
//			$rep->TextCol(6, 7,	$total_price, -2);
//			$rep->NewLine();
//			$rep->TextCol(3, 6, _("Shipping"), -2);
//			$rep->TextCol(6, 7,	$DisplayFreight, -2);
        $rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;

        while ($tax_item = db_fetch($tax_items))
        {
            if ($tax_item['amount'] == 0)
                continue;
            $DisplayTax =$tax_item['amount'];


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
                        $rep->TextCol(6, 8, _("Total Tax Excluded"), -2);
                        $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }
                    $rep->NewLine(-3);
                    $rep->TextCol(4, 7, $tax_type_name, -2);
                    $rep->TextCol(6, 7,	$DisplayTax, -2);


                    $first = false;
                }
                else
                    $rep->TextCol(4, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
                $rep->NewLine(-3);

                $rep->Font('bold');

                $rep->TextCol(5, 8, $tax_type_name, -2);
                //   $rep->NewLine();
//                 $rep->TextCol(5, 7,  _("PRA (16%)"), -2);

                $rep->TextCol(9, 10,	$DisplayTax, -2);

                $total_tax +=abs($tax_item['amount']);



                // $rep->NewLine(-6);
            }
            $rep->NewLine();
        }
        $total_invoice =$total_value_excl_tax + $total_tax- $myrow["discount1"]-$myrow["discount2"];
 $rep->Font('b');
 $rep->NewLine(+2);
        $rep->TextCol(5, 8, "Total Including Tax", -2);
        $rep->TextCol(9, 10, price_format($total_invoice), -2);
// $rep->NewLine(-2);
        //  display_error($myrow["discount2"]);
 $rep->Font('b');
        $rep->NewLine(-7);
        $rep->fontSize -= 2;
        $rep->Text($mcol + 100, "Warranty under the Medical Devices Rules, 2017");
        $rep->MultiCell(800, 10, $memo , 0, 'L', 0, 2, 350,679, true);
    //   $rep->NewLine(-2);

        $rep->Text($mcol + 100, "__________________________________________");
        $rep->NewLine();
        $rep->Text($mcol + 50, "I, Ch. Khalid Nawaz being authorized by M/s SAKUF TRADING, 2nd floor, G-34,");
        $rep->NewLine();
        $rep->Text($mcol + 50, "Phase-1, commercial area, DHA, Lahore, authorized vide letter no. ST-HR-150");
        $rep->NewLine();
        $rep->Text($mcol + 50, "818/1, dated 15th August 2018 , do hereby give this warranty that the medical");
        $rep->NewLine();
        $rep->Text($mcol + 50, "devices here-under described as sold  by me  and  contained in the bill of sale,");
        $rep->NewLine();
        $rep->Text($mcol + 50, "invoice,bill of lading or other document describing the medical devices referred");
        $rep->NewLine();
        $rep->Text($mcol + 50, "to herein do not  contravene in any way the provisions of the DRAP Act,2012");
        $rep->NewLine();
        $rep->Text($mcol + 50, "and the rules framed there-under.");
        $rep->fontSize += 2;
        $rep->NewLine(+3);
        // $rep->TextCol(4, 6, ":", -2);
        $rep->NewLine();
//         $rep->fontSize -= 2;
//         $rep->TextColLines(4, 6, "".$memo, -2);
//         $rep->fontSize += 2;
        // $rep->NewLine(-3);

        // $rep->NewLine(-1);
        $rep->Text($mcol + 50, "__________________________");

        $rep->NewLine();
        $rep->Text($mcol + 50, "Ch.Khaild Nawaz");
        $rep->NewLine();
        $rep->Text($mcol + 50, "Pharmacist/Distribution Manager");
        $rep->NewLine();
        $rep->Text($mcol + 50, "Date:");

        $rep->NewLine(-13);
        $rep->Font('bold');

        $rep->NewLine(+7);
        $rep->NewLine();


        // $rep->NewLine();

        $rep->Font('');

        $rep->NewLine();
        $DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
        $rep->Font('bold');

        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }

//        $user =get_user_id($myrow['trans_no'],ST_SALESINVOICE);
//        $rep->MultiCell(100, 25, "".get_user_name_70123($user) ,0, 'C', 0, 2, 80,790, true);

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