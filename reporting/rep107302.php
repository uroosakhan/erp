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
function get_user_name_70123($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_customer_information($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}

function get_tax_rate($trans_no, $stock_id)
{
    $sql = "SELECT (unit_tax/unit_price*100) FROM ".TB_PREF."debtor_trans_details
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
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

//---------------------------------------------------------------------------

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

    $cols = array(6, 338, 390, 455, 520);

    $aligns = array('left','center','center','right');


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
        $rep->SetHeaderType('Header107302');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $headers, $aligns);


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
            $qty_total_new =$myrow2["quantity"];
            $price_new +=$myrow2["unit_price"];
            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $count +=1;
            $DisplayPrice = ($myrow2["unit_price"]);
            $DisplayQty = number_format2($sign*$myrow2["quantity"], 0);
            $DisplayPq =  ($myrow2["unit_price"] * $myrow2["quantity"]);
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//            $rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
//            
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
//                $rep->TextCol(0, 1,	$myrow2['id']);
                $rep->TextCol(0, 1, $myrow2['StockDescription'], -2);
                // $rep->TextCol(1, 2,	$DisplayQty);

//					$rep->TextCol(3, 4,	$myrow2['units'], -2);

                // $rep->TextCol(2, 3,	$DisplayPrice);
                $rep->AmountCol(3, 4, $DisplayPq, $dec);
                // $rep->TextCol(4, 5,	get_tax_rate($i, $myrow2['stock_id'])."%", -2);
                //$rate_ = get_tax($myrow2['tax_type_id']);
                //$amount_of_sales_taxincluding_sales_tax  = $rate_ / 100;
                //$amount_including_tax=  $DisplayPq * $amount_of_sales_tax  ;

                $amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);

                //$rep->TextCol(5, 6,	price_format($amount_including_tax), -2);

                // $rep->AmountCol(5, 6,	$amount_including_tax, $dec);

                $including_sales_tax=  $DisplayPq + $amount_including_tax;
                // $rep->TextCol(6, 7,	price_format($including_sales_tax), -2);




                //$total_price += $myrow2["unit_price"];
                $total_value_excl_tax += $DisplayPq;
                $total_amount += $amount_including_tax;
                $total_including_tax += $including_sales_tax;

            }

            //$rep->row = $newrow;
            $rep->NewLine();
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }
///
        $rep->NewLine(-$count);
        $rep->TextCol(1, 2,	$qty_total_new);
        $rep->TextCol(2, 3,	$price_new, -2);
        $rep->NewLine(+$count);

        $memo = get_comments_string(ST_SALESINVOICE, $i);
        if ($memo != "")
        {
            //$rep->NewLine();
            //$rep->TextColLines(1, 5, $memo, -2);
        }

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        //$rep->NewLine(14.5);
        $rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
        //	$rep->NewLine();

        $rep->NewLine(-9);
        $rep->Font('bold');
        $rep->TextCol(0, 1, "                                                                                                                      TOTAL AMOUNT", -2);
        //$rep->TextCol(2, 3,price_format($total_price), -2);


//        $rep->TextCol(5, 6,price_format($total_amount), -2);
//        $rep->TextCol(6, 7,price_format($total_including_tax).' '.'(' .$rep->formData['curr_code'].')', -2);
        $rep->Font('');

//        $rep->MultiCell(185,42,"",1,'L', 0, 2,380,665,true);
        $rep->MultiCell(340,42,"",1,'R', 0, 2,40,604,true);
        $rep->MultiCell(185,42,"",1,'L', 0, 2,380,604,true);
        $rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;
        while ($tax_item = db_fetch($tax_items))
        {
            if ($tax_item['amount'] == 0)
                continue;
            $DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
            $tax_amount +=$tax_item['amount'];

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
                $rep->NewLine(-3);
                $rep->Font('bold');
                $rep->TextCol(0, 1, "                                                                                                      ".$tax_type_name, -2);
                $rep->TextCol(3, 4, $DisplayTax, -2);
                $rep->Font('');
                $rep->NewLine(+3);
            }
            $rep->NewLine();
        }
        //$rep->TextCol(3, 4,price_format($total_value_excl_tax ), -2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->NewLine(-4);
        $rep->TextCol(3, 4,price_format($total_value_excl_tax + $tax_amount), -2);
        $rep->NewLine(+4);
        $rep->Font('');
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
        // $rep->MultiCell(100, 25, "".get_user_name_70123($user) ,0, 'C', 0, 2, 80,645, true);


        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }


    if ($pictures)
    {
        $image = company_path() . '/images/'. $rep ->company['coy_logo'];
        $imageheader = company_path() . '/images/headers.PNG';

        if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
            $rep->NewPage();
        $rep->AddImage($image, $rep->cols[1] +320, $rep->row +535, 140,60, $SysPrefs->pic_height);
        $rep->AddImage($imageheader, $rep->cols[1] -100, $rep->row -170, 600, $SysPrefs->pic_height);

        $rep->NewLine();
        //}
    }

    if ($email == 0)
        $rep->End();


}

?>