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
    $dec = 0;

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(6, 45, 140, 180, 230, 280, 350, 390, 450, 560);
    $cols2 = array(6, 45, 190, 250, 320, 380, 430, 550);
    // $headers in doctext.inc
    $aligns = array('left','left','right','right','right','right','right','right','right');
    $aligns2 = array('left','left','left','left','left','left','left');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 8, $orientation);
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
          $rep->SetHeaderType('Header1164');
         else
        $rep->SetHeaderType('Header1174');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns, $cols2, null,$aligns2);


        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $SubTotal = 0;
        $total_price =0;
        $total_including_tax=0;
        $total_amount=0;
        $DisplayPq =0;
        $amount_including_tax = 0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;

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
            //$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            $newrow = $rep->row;
            //   $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
                $rep->TextCol(0, 1,	$DisplayQty, -2);
//					$rep->TextCol(3, 4,	$myrow2['units'], -2);
                $rep->TextCol(2, 3,	$DisplayPrice, -2);
                $rep->TextCol(3, 4,	$DisplayDiscount, -2);
                $rep->TextCol(4, 5,	$total_disc, -2);
                $vallue_discount = $DisplayPq - $total_disc; // Value excluding 
                $rep->TextCol(5, 6,	$vallue_discount, $dec);
                $rep->TextCol(6, 7,	round2(get_tax_rate($i, $myrow2['stock_id']),$dec)."%", -2);
              $tax = round2(get_tax_rate($i, $myrow2['stock_id']));
              $tax1 = $tax/100;
                //$rate_ = get_tax($myrow2['tax_type_id']);
                //$amount_of_sales_taxincluding_sales_tax  = $rate_ / 100;
                //$amount_including_tax=  $DisplayPq * $amount_of_sales_tax  ;

                // $amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);

                //$rep->TextCol(5, 6,	price_format($amount_including_tax), -2);
                $vallue_discount1 = $vallue_discount*$tax1;
                $rep->TextCol(7, 8,	$vallue_discount1, -2);

                $including_sales_tax=  $vallue_discount + $amount_including_tax + $vallue_discount1;
                $excluding_sales_tax = $DisplayPq;
                $rep->TextCol(8, 9,	price_format($including_sales_tax), -2);


                $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);


                $total_price += $myrow2["unit_price"];
                $total_value_excl_tax += $vallue_discount;
                $excluding_discount = $total_value_excl_tax ;
                $total_amount += $amount_including_tax;
                $total_including_tax += $including_sales_tax;

            }
            //$rep->row = $newrow;
            // $rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (22 * $rep->lineHeight))
                $rep->NewPage();
        }

        $memo = get_comments_string(ST_SALESINVOICE, $i);
        $rep->SetFontSize(10);
        $rep->Font('b');
        $rep->MultiCell(120,10,"Comments:",0,'L', 0, 2,40,620,true);

        $rep->Font('');
        $rep->MultiCell(350,100,$memo,0,'L', 0, 2,40,635,true);
        $rep->SetFontSize(8);
        $rep->Font('b');
        // $rep->MultiCell(120,10,"General Sales Tax (17%)",0,'L', 0, 2,336,647,true);
        // $rep->MultiCell(120,10,"PRA (16%)",0,'L', 0, 2,388,659,true);
        if ($memo != "")
        {
            // if ($rep->row < $rep->bottomMargin + (20 * $rep->lineHeight))
            //     $rep->NewPage();
            // // $rep->NewLine();
            // $rep->TextColLines(1, 2, $memo, -2);
        }

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        //$rep->NewLine(14.5);
        $rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
        //	$rep->NewLine();

        $rep->NewLine(-17);
        $rep->Font('bold');

        $rep->NewLine(2);

        $rep->TextCol(1, 2, "TOTAL"." ".$rep->formData['curr_code'], -2);
        $rep->TextCol(2, 3,price_format($total_price), -2);
        $rep->TextCol(5, 6,price_format($excluding_discount ), -2);
        // $rep->TextCol(7, 8,price_format($total_amount), -2);

        $rep->TextCol(8, 9,price_format($total_including_tax), -2);
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
                        $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
                        $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }
                    $rep->TextCol(3, 6, $tax_type_name, -2);
                    $rep->TextCol(6, 7,	$DisplayTax, -2);
            
                   
                    $first = false;
                }
                else
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
                $rep->NewLine(+6);

                $rep->Font('bold');
 
                $rep->TextCol(5, 7, $tax_type_name, -2);
               
                
              $rep->TextCol(8, 9,	$DisplayTax, -2);
               
               $total_tax +=abs($tax_item['amount']);
               
        

                $rep->NewLine(-6);
            }
            $rep->NewLine();
        }
         $total_invoice =$total_value_excl_tax + $total_tax- $myrow["discount1"]-$myrow["discount2"];
        //  display_error($myrow["discount2"]);
         
        $rep->NewLine(2);
        $rep->Font('bold');

        $rep->TextCol(5, 7, "Sub-Total Excluding Tax", -2);
        $rep->TextCol(8, 9, $total_value_excl_tax, -2);
        $rep->NewLine();
        if($myrow["discount1"] != 0)
{        
        $rep->TextCol(5, 7, "Discount", -2);
        $rep->TextCol(8, 9, $myrow["discount1"], -2);
        $rep->NewLine(5);
        
        
}
else
{
    $rep->NewLine(5);
}
if($myrow["discount2"] != 0)
{
        $rep->TextCol(5, 7, "Discount", -2);
        $rep->TextCol(8, 9, $myrow["discount2"], -2);
        $rep->NewLine();
    
}
else
{
    $rep->NewLine();
}

        // $rep->NewLine();
        $rep->TextCol(5, 7, "Total Including Tax", -2);
        $rep->TextCol(8, 9, price_format($total_invoice), -2);
        $rep->NewLine();
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

        $user =get_user_id($myrow['trans_no'],ST_SALESINVOICE);
        $rep->MultiCell(100, 25, "".get_user_name_70123($user) ,0, 'C', 0, 2, 80,790, true);

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