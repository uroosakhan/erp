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
function convert_number_to_words_1113($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
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
        return $negative . convert_number_to_words_1113(abs($number));
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
                $string .= $conjunction . convert_number_to_words_1113($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words_1113($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words_1113($remainder);
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
print_sales_orders();

$print_as_quote = 0;

function print_sales_orders()
{
    global $path_to_root, $print_as_quote, $no_zero_lines_amount;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $show_cost = $_POST['PARAM_2'];
    $currency = $_POST['PARAM_3'];
    $email = $_POST['PARAM_4'];
    $print_as_quote = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $cols = array(4, 30, 80, 260, 320, 356, 400,460, 515);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'right', 'left','right', 'right', 'right', 'right');

    $params = array('comments' => $comments);

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
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1)
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
        $rep->SetHeaderType('Header10910');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
        $rep->NewPage();

        $result = get_sales_order_details($i, ST_SALESORDER);
        $SubTotal = 0;
        $items = $prices = array();
        $sr=1;
        while ($myrow2=db_fetch($result))
        {
            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],get_qty_dec($_POST['stock_id']));
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
            $tot_qty +=$myrow2["quantity"];
            $DisplayNet = number_format2($Net,$dec);
            $rep->TextCol(0, 1,	$sr++);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            $rep->TextCol(1, 2,	$myrow2['stk_code'], -2);
            $oldrow = $rep->row;
            $rep->TextColLines(2, 3, $myrow2['description']."\n".$myrow2['text1'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
                $rep->TextCol(3, 4,	$DisplayQty, -2);
                $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(4, 5,	$myrow2['units_id'], -2);
                }
                else
                {
                    $rep->TextCol(4, 5,	$myrow2['units'], -2);
                }

                if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF'))
                {
                    if($show_cost == 0) {
                        $rep->TextCol(5, 6, $DisplayPrice, -2);
                        $rep->TextCol(6, 7, $DisplayDiscount, -2);
                        $rep->TextCol(7, 8, $DisplayNet, -2);
                    }
                }
            }
// 			if ($myrow2['text1'] != "")
//             {
//                 $rep->NewLine(2);
//                 $rep->TextColLines(1, 5, $myrow2['text1'], -2);
//             }
            $rep->row = $newrow;
            //$rep->NewLine(1);
//			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//				$rep->NewPage();

            if ($rep->row < $rep->bottomMargin + ($rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 39.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->leftMargin,  39.4* $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,   39.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-445, 39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-445, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-205, 39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-205, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-120, 39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-498,   39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-498, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-172.3,  39.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-172.3, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-65,   39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-65, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }



        }
        $rep->LineTo($rep->leftMargin,  39.4* $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,   39.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,   39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-445,  39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-445, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-205,  39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-205, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-120,  39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-498,  39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-498, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-172.3,   39.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-172.3, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-65,   39.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-65, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  43.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-500,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-500, $rep->row);
        $rep->Line($rep->row);
// 		if ($myrow['comments'] != "")
// 		{
// 			$rep->NewLine();
// 			$rep->TextColLines(1, 5, $myrow['comments'], -2);
// 		}
        $rep->NewLine();
        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($myrow["freight_cost"],$dec);

//		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESORDER;
        $rep->TextCol(2, 3, _("Qty-Total"), -2);
        $rep->TextCol(3, 4, number_format2($tot_qty,get_qty_dec($myrow2['stk_code'])), -2);
        $rep->NewLine();
        if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF'))
        {
            if($show_cost == 0) {
                $rep->TextCol(4, 7, _("Sub-total"), -2);
                $rep->TextCol(7, 8, $DisplaySubTot, -2);
                $rep->NewLine();
                $rep->TextCol(4, 7, _("Shipping"), -2);
                $rep->TextCol(7, 8, $DisplayFreight, -2);
                $rep->NewLine();

                $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
                if ($myrow['tax_included'] == 0) {
                    $rep->TextCol(4, 7, _("TOTAL ORDER EX GST"), -2);
                    $rep->TextCol(7, 8, $DisplayTotal, -2);
                    $rep->NewLine();
                }

                $tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
                    $myrow['tax_group_id'], $myrow['tax_included'], null);
                $first = true;
                foreach ($tax_items as $tax_item) {
                    if ($tax_item['Value'] == 0)
                        continue;
                    $DisplayTax = number_format2($tax_item['Value'], $dec);
                    $DisplayTax1 = $tax_item['Value'];

                    $tax_type_name = $tax_item['tax_type_name'];

                    if ($myrow['tax_included']) {
                        if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1) {
                            if ($first) {
                                $rep->TextCol(4, 7, _("Total Tax Excluded"), -2);
                                $rep->TextCol(7, 8, number_format2($sign * $tax_item['net_amount'], $dec), -2);
                                $rep->NewLine();
                            }
                            $rep->TextCol(4, 7, $tax_type_name, -2);
                            $rep->TextCol(7, 8, $DisplayTax, -2);
                            $first = false;
                        } else
                            $rep->TextCol(4, 8, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
                    } else {
                        $SubTotal += $tax_item['Value'];
                        $rep->TextCol(4, 7, $tax_type_name, -2);
                        $rep->TextCol(7, 8, $DisplayTax, -2);
                    }
                    $rep->NewLine();
                }
                $rep->NewLine();

                $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
                $rep->Font('bold');
                $rep->TextCol(4, 7, _("TOTAL ORDER GST INCL."), -2);
                $rep->TextCol(7, 8, $DisplayTotal, -2);
                $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
                if ($words != "") {
                    $rep->NewLine(1);
                    $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, -2);
                }
            }
        }
        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    $rep->NewLine();
    $rep->SetFontSize(13);
    $rep->Font('bold');
    $rep->TextCol(0, 6,	_('Terms and Conditions:'));
    $rep->Font('');
    $rep->SetFontSize(9);

    $text = $rep->formData['term_cond'];
    $breaks = array("<br />","<br>","<br/>","<br />","&lt;br /&gt;","&lt;br/&gt;","&lt;br&gt;");
    $text = str_ireplace($breaks, "\r\n", $text);
    $rep->NewLine(1.5);
    $rep->TextColLines(0, 4,   $text);

    $rep->multicell(175,10,$myrow['comments'],0,'L',0,0,406,127);

//	$amount_in_words = convert_number_to_words_1113($rep->formData['package_deal']);
//	$rep->font('b');
//	$rep->multicell(85,15,"  Amount in words:",0,'L',0,0,40,700);
//	$rep->font('');
//	$rep->multicell(400,15," ".$amount_in_words,0,'L',0,0,125,700);
    $rep->NewLine(0.4);
    $rep->TextCol(0, 25,"______________________", -2);

    $rep->TextCol(2, 9,"                                                                                                                       ______________________", -2);
    // $rep->NewLine(-15);
    $rep->NewLine(0.7);
    $rep->TextCol(1, 25,"Prepared By", -2);
    $rep->TextCol(1, 25,"                                                                                                                                                   Approved By", -2);
    $rep->NewLine(-16);
    if ($email == 0)
        $rep->End();
}

?>