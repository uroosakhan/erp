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

//-----------------------------------------------------------------
function get_bank_details()
{
    $sql = "SELECT 	bank_address,bank_account_number,bank_name FROM ".TB_PREF."bank_accounts 
	WHERE dflt_curr_act = 1";
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch($result);
    return $row;
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

function get_delivery_date($trans_no)
{
    $sql = "SELECT tran_date FROM ".TB_PREF."debtor_trans 
	WHERE trans_no = ".db_escape($trans_no)."
	AND type= ".db_escape(13);
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_do_no($order_)
{
    $sql = "SELECT reference FROM ".TB_PREF."debtor_trans 
	WHERE order_ = ".db_escape($order_)."
	AND type= ".db_escape(13);
    $result = db_query($sql, "could not retreive default customer currency code");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_name_1077($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time1077($trans_no,$type)
{
    $sql= "SELECT user,  UNIX_TIMESTAMP(stamp) as unix_stamp FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
}
print_invoices();

//----------------------------------------------------------------------------------------------------
function convert_number_to_words2($number) {

    $hyphen      = '-';
    $conjunction = ' AND ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'ZERO',
        1                   => 'ONE',
        2                   => 'TWO',
        3                   => 'THREE',
        4                   => 'FOUR',
        5                   => 'FIVE',
        6                   => 'SIX',
        7                   => 'SEVEN',
        8                   => 'EIGHT',
        9                   => 'NINE',
        10                  => 'TEN',
        11                  => 'ELEVEN',
        12                  => 'TWELVE',
        13                  => 'THIRTEEN',
        14                  => 'FOURTEEN',
        15                  => 'FIFTEEN',
        16                  => 'SIXTEEN',
        17                  => 'SEVENTEEN',
        18                  => 'EIGHTEEN',
        19                  => 'NINETEEN',
        20                  => 'TWENTY',
        30                  => 'THIRTY',
        40                  => 'FOURTY',
        50                  => 'FIFTY',
        60                  => 'SIXTY',
        70                  => 'SEVENTY',
        80                  => 'EIGHTY',
        90                  => 'NINETY',
        100                 => 'HUNDRED',
        1000                => 'THOUSAND',
        1000000             => 'MILLION',
        1000000000          => 'BILLION',
        1000000000000       => 'TRILLION',
        1000000000000000    => 'QUADRILLION',
        1000000000000000000 => 'QUINTILLION'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words2(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words2($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words2($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words2($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function buyers_phone($debtor_no)
{
    $sql="SELECT * FROM `0_crm_persons` WHERE `id` IN (
  SELECT person_id FROM `0_crm_contacts` WHERE `type`='customer' 
  AND `action`='order' 
  OR `action`='general' 
  AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no='$debtor_no')) ";

    $result = db_query($sql, "Cannot retreive a wo issue");

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

    $orientation = ('P');
    //$dec = user_price_dec();
    $dec = 0;

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(0,18, 40, 200, 250, 330, 370, 440, 550);
    $cols2 = array(0,20, 40, 200, 250, 320, 350, 440, 550);
    // $headers in doctext.inc
    $aligns = array('left','right','left','right','right','right','right','right','right');
    $aligns2 = array('left','right','left','left','left','left','left','left','left');

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
        $user =get_user_id_time1077($myrow['trans_no'],ST_SALESINVOICE);
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
        $rep->SetHeaderType('Header1163');
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
        $s=0;
        $rep->MultiCell(90,15, get_reference(ST_SALESORDER, $myrow['order_']),0,'L', 0, 2,450,193,true);
        $total_value_excl_tax = $total_amount = $tot_displaynet =0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;
            $s++;
            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],(get_qty_dec($myrow2['item_code'])));
            $DisplayQty = number_format2($sign*$myrow2["quantity"], get_qty_dec($myrow2['item_code']));
            $DisplayQty1 = ($sign*$myrow2["quantity"]);
            $DisplayQty2 = ($myrow2["unit_price"] * $DisplayQty1);
            $DisplayPq =  price_format($DisplayQty2);
            $DisplayPq2 =  ($DisplayQty2);
            $DisplayNet = price_format($Net);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            //$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
//            $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
            $oldrow = $rep->row;

            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {

                $rep->setfontsize(+9);
                $rep->TextCol(0, 1,$s, -2);
                $rep->TextCol(1, 2,	$DisplayQty, -2);
//					$rep->TextCol(3, 4,	$myrow2['units'], -2);
                $rep->TextCol(3, 4,	$DisplayPrice, -2);
                $rep->TextCol(4, 5,$DisplayPq. ".00");
               // display_error($DisplayQty1*$myrow2["unit_price"]);
                $rep->TextCol(5, 6,	round2(get_tax_rate($i, $myrow2['stock_id']))."%", -2);
                //$rate_ = get_tax($myrow2['tax_type_id']);
                //$amount_of_sales_taxincluding_sales_tax  = $rate_ / 100;
                //$amount_including_tax=  $DisplayPq * $amount_of_sales_tax  ;
                $amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);
                //$rep->TextCol(5, 6,	price_format($amount_including_tax), -2);
                $rep->TextCol(6, 7,	price_format($myrow2["quantity"]*$myrow2["unit_tax"]).".00");

$a = ($myrow2["quantity"]*$myrow2["unit_tax"]);
$b = $myrow2["unit_price"] * $myrow2["quantity"];

$c = $a +$b;

                $including_sales_tax =  round(($DisplayQty2) + ($myrow2["quantity"]*$myrow2["unit_tax"]));
                //today
                
                $rep->AmountCol(7, 8,$c,2);
                
                $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
                //$total_price += $myrow2["unit_price"];
                $total_value_excl_tax += $DisplayPq2;
                $total_amount += ($myrow2["quantity"]*$myrow2["unit_tax"]);
                $total_including_tax += $including_sales_tax;
                $rep->setfontsize(+8);

            }
            if ($myrow2['text1'] != "")
            {
//                $rep->NewLine();
                $rep->TextColLines(2, 3, $myrow2['text1'], -2);
//                $rep->NewLine();
            }
            //$rep->row = $newrow;
            // $rep->NewLine(1);
            // if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
            //     $rep->NewPage();
                
                 $tot_displaynet += $including_sales_tax;



            if ($rep->row < $rep->bottomMargin + ( $rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 41.3 * $rep->lineHeight, $rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin, 41.3 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-82,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-82, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-155,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-155, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-273,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-273, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-328,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-328, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-487,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-487, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-511,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-511, $rep->row);
                $rep->Line($rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }

        }


        $rep->LineTo($rep->leftMargin, 41.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  41.3* $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-82,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-82, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-155,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-155, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-273,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-273, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-328,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-328, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-487,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-487, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-511,  41.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-511, $rep->row);
        $rep->Line($rep->row);

                $memo = get_comments_string(ST_SALESINVOICE, $i);
//          if ($memo != "")
//        {
//            // $rep->NewLine();
//
//            if ($rep->row < $rep->bottomMargin + (20 * $rep->lineHeight))
//                $rep->NewPage();
//                $rep->TextColLines(2, 3, $memo, -2);
//        }

$rep->setfontsize(+10);

$rep->Font('');

        

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        //$rep->NewLine(14.5);
//        $rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
        //	$rep->NewLine();

        $rep->NewLine();
        $rep->Font('bold');
        $rep->setfontsize(+10);
        $rep->TextCol(2, 3, "TOTAL", -2);

        //$rep->TextCol(2, 3,price_format($total_price), -2);
        $rep->TextCol(4, 5,price_format($total_value_excl_tax).".00");
        $rep->TextCol(6, 7,price_format($total_amount).".00");
        $rep->AmountCol(7, 8,round2($tot_displaynet),2);
        $rep->Font('');
        $rep->LineTo($rep->leftMargin, 40 * $rep->lineHeight ,$rep->leftMargin, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  40* $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-82,  40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-82, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-155,  40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-155, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-190, 40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-273,  40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-273, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-328,  40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-328, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-487,  40 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-487, $rep->row-13);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-511,  40* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-511, $rep->row-13);

        $rep->Line($rep->row-13.8);
        $amount_in_words = convert_number_to_words2($total_including_tax);
        $rep->font('bold');
        $rep->setfontsize(+10);
        $rep->NewLine(2);
        //$rep->MultiCell(530,15,$rep->company['coy_name'],0,'L', 0, 2,40,680,true);

        $rep->TextCol(0, 3, "AMOUNT IN WORDS:", -2);
        $rep->font();
        $rep->NewLine();
        $rep->TextCol(0, 10, "RUPEES ".$amount_in_words." ONLY", -2);
        $rep->NewLine(2);
        $rep->TextCol(0, 3, $rep->company['coy_name'], -2);
        $rep->NewLine(2);
        $rep->TextCol(0, 4, get_user_name_1077($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp'])), -2);
        $rep->NewLine(0.5);
        $rep->TextCol(0, 7, "________________________", -2);
        $rep->TextCol(3, 5, "________________________", 5);
        $rep->TextCol(6, 9, "________________________", -2);
        $rep->NewLine();
        $rep->TextCol(0, 7, "           Prepared by", -2);
        $rep->TextCol(3, 5, "Checked by               ", -2);
        $rep->TextCol(6, 9, "Approved by            ", -2);;



        $rep->setfontsize(+8);
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
            $DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

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
                //$rep->TextCol(3, 6, $tax_type_name, -2);
                //$rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->NewLine();
        }

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
        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }


//    $rep->setfontsize(+8);
//    $rep->Font('b');

//    $rep->MultiCell(225, 60,get_user_name_1077($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp'])), 0, 'L', 0, 2, 45,720, true);

    if ($email == 0)
        $rep->End();
}

?>