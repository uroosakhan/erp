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

function get_payment_terms_names_rep($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator =" .db_escape($id);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}


function get_user_name_70123($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
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
        return $negative . convert_number_to_words(abs($number));
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
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
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

function print_sales_orders()
{
    global $path_to_root, $SysPrefs;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $pictures = $_POST['PARAM_4'];
    $print_as_quote = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $cols = array(0, 22,80,120, 240, 270,280, 300, 335, 385, 410, 463);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left',	'left', 'left', 'left', 'right', 'center', 'center', 'right','right', 'right');

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
        // $rep->SetHeaderType('Header1090');
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

        $sales_order = get_sales_order_header($i, ST_SALESORDER);

        $rep->SetHeaderType('Header10923');
        $rep->NewPage();

        $result = get_sales_order_details($i, ST_SALESORDER);
        $result1 = get_sales_order_invoices($myrow['order_']);
        $SubTotal = 0;
        $items = $prices = array();
        $serial =0;
        $totqty = 0;
        $tottrade = 0;
        $net_amount1 = 0;
        $total_scheme1 = 0;
        $total_qty_issued_2 = 0;
        $scheme_total = 0;
        $logo = company_path() . "/images/IBP_Logo_1.png";
        $rep->AddImage($logo, 40,  770, -40, 50.5);

        $total_qty_issued = 0;
//        $rep->Font('bold');
        $rep->setfontsize(15);
        $rep->multicell(300,20,"Sale Order " ,0,'L',0,0,450,20);
        $rep->setfontsize(7);
        $rep->multicell(100,10,"Order To" ,0,'C',1,0,40,80);
        $rep->multicell(100,10,"CUSTOMER ID:" ,0,'L',0,0,40,100);
        $rep->multicell(110,10,"Shipping Address" ,0,'C',1,0,452,100);
//
//2nd column
        $rep->multicell(524,20,"" ,0,'L',1,0,40,235);

        $rep->multicell(500,50,"PAYMENT TERMS" ,0,'L',0,0,50,240);
        $rep->multicell(500,50,"PAYMENT DUE DATE" ,0,'L',0,0,150,240);
        $rep->multicell(500,50,"SHIPPING TERMS" ,0,'L',0,0,270,240);
        $rep->multicell(500,50,"SALES OFFICER" ,0,'L',0,0,367,240);
        $rep->multicell(500,50,"SALES OFFICER #" ,0,'L',0,0,465,240);

//
        $rep->multicell(524,20,"" ,0,'L',1,0,40,190);
        $rep->multicell(100,40,"P.O NO" ,0,'L',0,0,74,195);
        $rep->multicell(120,40,"P.O DATE" ,0,'L',0,0,178,195);
        $rep->multicell(120,40,"P.O ISSUED BY" ,0,'L',0,0,275,195);
        $rep->multicell(115,40,"CONTACT NO" ,0,'L',0,0,373,195);
        $rep->multicell(115,40,"DELIVERY DATE" ,0,'L',0,0,469,195);
        $rep->multicell(115,40,"QTY" ,0,'L',0,0,325,294);
        $rep->multicell(115,40,"Value" ,0,'L',0,0,480,294);
        $rep->multicell(115,40,"Amount" ,0,'L',0,0,535,294);
        $rep->multicell(115,40,"Size" ,0,'L',0,0,280,294);
        $rep->multicell(525,2,"" ,0,'L',1,0,40,294);
        $rep->multicell(300,20,"S.O Date: " ,0,'L',0,0,450,80);
        $myformat12 =date('d F, Y', strtotime($myrow['document_date']));
        $rep->multicell(90,20,"".$myformat12 ,0,'R',0,0,473,80);
        $rep->multicell(300,20,"Order No:" ,0,'L',0,0,450,65);
        $contractDateBegin = date('Y-m-d', strtotime("+ 11days"));
        $myFormatForView = sql2date($myrow['delivery_date']);
        $Doformat = date('d F, Y', strtotime($myFormatForView));
        $myformat =date('d F, Y', strtotime($contractDateBegin));
        $rep->multicell(300,20,$myrow['DebtorName'] ,0,'L',0,0,40,115);
        $rep->multicell(100,19,$myrow['address'],0,'L',0,0,451,120);
        $rep->multicell(300,20,"NTN   : ".$myrow['ntn_no'] ,0,'L',0,0,40,157);
        $rep->multicell(300,20,"STRN : ".$myrow['tax_id'] ,0,'L',0,0,40,171);
        $rep->multicell(200,19, "".$myrow['address'],0,'L',0,0,40,130);
        $rep->multicell(90,19, "".$myrow['document_number'],0,'R',0,0,473,65);
        $rep->multicell(90,19, "".get_payment_terms_names_rep($myrow['payment_terms']),0,'L',0,0,73,259);
        $rep->multicell(100,19, "".$myformat,0,'C',0,0,142,259);
        $rep->multicell(100,19, $Doformat,0,'C',0,0,456,215);
        $rep->multicell(150,19, $myrow['debtor_no'],0,'L',0,0,120,100);
        $rep->multicell(115,40,"S.No" ,0,'L',0,0,41,285);
        $rep->multicell(115,40,"BARCODE" ,0,'L',0,0,65,285);
        $rep->multicell(115,40,"Brand" ,0,'L',0,0,123,285);
        $rep->multicell(115,40,"Description" ,0,'L',0,0,160,285);
        $rep->multicell(115,40,"Pack" ,0,'L',0,0,280,285);
        $rep->multicell(115,40,"Ordered" ,0,'L',0,0,321,285);
        $rep->multicell(115,40,"Scheme" ,0,'L',0,0,354,285);
        $rep->multicell(115,40,"Unit" ,0,'L',0,0,390,285);
        $rep->multicell(115,40,"Rate" ,0,'L',0,0,430,285);
        $rep->multicell(115,40,"Trade" ,0,'L',0,0,480,285);
//        $rep->multicell(115,40,"Disc" ,0,'L',0,0,505,285);
        $rep->multicell(115,40,"Scheme" ,0,'L',0,0,535,285);
        $myformat1 =date('d F, Y', strtotime($sales_order['po_date']));
        $rep->MultiCell(90, 20, "".$sales_order['h_text5'] ,0, 'C', 0, 2, 40,215, true);
        $rep->MultiCell(100, 10, $myformat1 ,0, 'C', 0, 2, 147,215, true);//po date
        $rep->MultiCell(100, 30, "".$sales_order['h_text4'] ,0, 'C', 0, 2, 350,259, true);//sale officer
        $rep->MultiCell(100, 30, "".$sales_order['h_text3'] ,0, 'C', 0, 2, 456,259, true);//sale officer
        $rep->MultiCell(100, 30, "".$sales_order['h_text2'] ,0, 'C', 0, 2, 350,215, true);//sale officer

        $rep->NewLine(1);

        while ($myrow2=db_fetch($result))
        {
            $item=get_item($myrow2['stk_code']);
            //display_error($item);
            $pref = get_company_prefs();

            if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
                $qty=$myrow2['quantity'] * $myrow2['con_factor'];
            else
                $qty=$myrow2['quantity'];



            if ($qty == 0)
                continue;
            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $total_dis +=(($myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]);
            $total_trade_n +=($myrow2["unit_price"] * $myrow2["quantity"]);
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net,$dec);
//            $total_scheme = $myrow2['bonus'] * $myrow2["unit_price"];
            $trade_value =  $myrow2["unit_price"] * $qty;
            // $trade_value =  $myrow2["unit_price"] * $qty * ( 1 -  $myrow2['discount_percent']);

            $total_scheme = $myrow2['bonus'] * $myrow2["unit_price"];
            if( $pref['disc_in_amount'] == 1) {
                $line_discount = $myrow2['discount_percent'];
            }
            else{

                $line_discount = $myrow2['discount_percent'] * 100 ."%";
                $total_dics += ($trade_value * ($myrow2['discount_percent'] ));
                // display_error($total_dics);
            }
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            $serial++;

            $rep->TextCol(0, 1, $serial, -2);

            $rep->TextCol(1, 2,	$item['text3'], -2);
            $rep->TextCol(7, 8,	"    ".$myrow2['bonus'], -2);
            $scheme_total +=$myrow2['bonus'];
            $oldrow = $rep->row;
//            $rep->TextCol(7, 8,	"    ".$item['text2'], -2);
//            $scheme_total +=$item['text2'];
            $rep->TextColLines(3, 4, $myrow2['description'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $pref = get_company_pref();
                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(8, 9,	$myrow2['units_id'], -2);
                }
                else
                {
                    $rep->TextCol(8, 9,	$myrow2['units'], -2);
                }
                $pref = get_company_prefs();
                $item=get_item($myrow2['stk_code']);

                $rep->Amountcol(6, 7,	$qty, $dec);

                $totqty +=$qty;


                $trade_value = ($qty + $item['text2']) * $myrow2["unit_price"];
                $rep->TextCol(4, 5, $item['text4']." GMS", -2);
//                $rep->TextCol(11, 12, $line_discount, -2);

                $rep->TextCol(10,11,	number_format2($trade_value), -2);
                $trade_value1 += $trade_value;
                $tot_amount3 += $item["amount3"];

                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(9, 10, $DisplayPrice ."/" . $myrow2['units_id'], -2);
                }
                else
                {
                    $rep->TextCol(9, 10,$DisplayPrice, -2);
                }
                $rep->TextCol(11, 12, $total_scheme, -2);
                $total_scheme1 +=$total_scheme;
                $disc_amount = $trade_value * $myrow2["discount_percent"];

                $total_qty_issued_qty_wali = $qty / $item['carton'];
                $total_qty_issued_scheme = $myrow2['bonus'] / $item['carton'];
                $total_qty_issued_2 += $total_qty_issued_qty_wali;
                $total_qty_issued_scheme_2 += $total_qty_issued_scheme;

                $rep->TextCol(2, 3,	get_category_name($myrow2['category_id']), -2);
            }
            $rep->row = $newrow;
            if ($rep->row < $rep->bottomMargin + (2 * $rep->lineHeight))
                $rep->NewPage();

        }
        $rep->NewLine(2);
//        $rep->MultiCell(90, 20, "".price_format($tot_qty) ,0, 'C', 0, 2, 295,403, true);
//        $rep->MultiCell(90, 20, "".$scheme_total ,0, 'C', 0, 2, 345,403, true);
//
        $total_gross1 = $trade_value1 - $total_scheme1 - $scheme_total ;
        $sumof_discount_value = $total_dics;
        if ($myrow['ov_freight'] != 0.0)
        {
            $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
        }
        $total_net = $total_gross1 - $sumof_discount_value + $DisplayFreight - $myrow['discount1'];
        $rep->Font('bold');
        $rep->TextCol(5, 7, price_format($totqty) , - 2);
        $rep->TextCol(7, 8, price_format($scheme_total) , - 2);
        $rep->TextCol(10,11, "  ".price_format($trade_value1) , - 2);
        $rep->TextCol(11, 12, price_format($total_scheme1) , - 2);
//

        $rep->Font('');
        $rep->NewLine(3);
        $rep->Font('bold');
        $rep->TextCol(0,7, "Rupees ".convert_number_to_words(abs($total_net))." Only" , - 2);
        $rep->Font('');
        $rep->NewLine();
        $rep->TextCol(0,7, "1- Claims in respect of this invoice must be received in writing to us within" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "     three days from date of delivery, after which no complains will be solicited" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "2- Make all cheques payable to M/s. International Brandz Pakistan. " , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "3- If you have any questions concering this invoice, or any other please" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "    free to contact us" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "IBP@SAPGROUP.COM.PK / +92 300 821 81 64" , - 2);
        $rep->NewLine(-2);

        $rep->NewLine(-3);

        $rep->TextCol(6, 10, "TOTAL TRADE VALUE	" , - 2);


        $rep->TextCol(11, 13, price_format($trade_value1) , - 2);
        $rep->NewLine();
        $rep->TextCol(6, 10, "Total Scheme Value	" , - 2);
        $rep->TextCol(11, 13, price_format($total_scheme1) , - 2);
        $rep->TextCol(6, 11, "                                                     ".price_format($scheme_total)."%", - 2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(6, 10, "Total Gross Amount	" , - 2);
        $rep->TextCol(11, 13, price_format($total_gross1) , - 2);
        $rep->Font('');

        $rep->NewLine();
        $rep->TextCol(6, 10, "Total Discount Value 	" , - 2);
        $rep->TextCol(11, 13, number_format2($total_dics) , - 2);
        $rep->NewLine();
        $rep->TextCol(6, 10, "Damage & Expiry 	" , - 2);
        $rep->TextCol(11, 13, price_format($myrow['discount1']) , - 2);

        $rep->NewLine();
        $rep->TextCol(6, 10, "SHIPPING & HANDLING 	" , - 2);
        $rep->TextCol(11, 13, number_format2($myrow["ov_freight"]) , - 2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(6, 10, "TOTAL NET AMOUNT 	" , - 2);
        $rep->TextCol(11, 13, price_format($total_net) , - 2);
        $rep->Font('');
        $rep->NewLine();
        $rep->Font('b');
        $rep->NewLine();
        $logo1 = company_path() . "/images/img.PNG";
        $rep->NewLine();

        $rep->AddImage($logo1, 40, $rep->row , -10, 20.6);

// 		$rep->AddImage($logo1, $rep->cols[1] -55, $rep->row -478, 510,20);
//         $rep->AddImage($logo1, $rep->cols[1] +300, $rep->row +225, null,$rep->company['logo_w'], $rep->company['logo_h']);
        $rep->NewLine(-0.5);

        $rep->TextCol(1, 3, "DRIVER / SALESMAN" , - 2);
        $rep->TextCol(3,4, "           SALES MAN" , - 2);
        $rep->TextCol(4,6, "VEHICLE" , - 2);
        $rep->TextCol(7,9, "VEHICLE NO" , - 2);
        $rep->TextCol(10,13, "TOTAL QTY ISSUED(CTN)" , - 2);

        $rep->NewLine(2);
        $rep->TextCol(3,4, "          ".$sales_order['f_text2'] , - 2);
        $rep->TextCol(1,3, $sales_order['f_text1'] , - 2);
        $rep->TextCol(4,6, $sales_order['f_text3'] , - 2);
        $rep->TextCol(7,9, $sales_order['f_text4'], - 2);

        $rep->TextCol(10,13,  price_format($total_qty_issued_2 + $total_qty_issued_scheme_2), - 2);
//        display_error($total_qty_issued_2);
//        display_error($total_qty_issued_scheme_2);

        $rep->NewLine(3);
        $rep->AddImage($logo1, 40, $rep->row , -10, 20.6);
        $rep->NewLine(-0.5);
        $rep->TextCol(1, 3, "TRANSPORTER NAME" , - 2);
        $rep->TextCol(3,4, "             TR NO" , - 2);
        $rep->TextCol(4,6, "TR DATE" , - 2);
//    $rep->TextCol(7,9, "VEHICLE NO" , - 2);
        $rep->TextCol(10,13, "TR QUANTITY (CTN)" , - 2);
        $rep->NewLine(1.5);
        $rep->TextCol(10,13, $sales_order['f_text9'] , - 2);
        $rep->TextCol(4,7, $sales_order['f_text8'] , - 2);
        $rep->TextCol(3,4,"             ". $sales_order['f_text7'], - 2);
        $rep->TextCol(1,3, $sales_order['f_text6'], - 2);



//        $rep->NewLine(6);
        $rep->NewLine(2);
        $rep->TextCol(8, 13, "                Receiver Details" , - 2);
        $rep->NewLine(2);
//        $rep->NewLine(-5);
        $rep->TextCol(8, 13, "Receiver Name    ___________________" , - 2);
        $rep->NewLine(2);
        $rep->TextCol(8, 13, "Receiver Date    ___________________" , - 2);
        $rep->NewLine(3);
        $rep->TextCol(8, 13, "              Receiver Stamp & Signature" , - 2);

//
        $rep->TextCol(1, 3, "___________________" , - 2);
        $rep->TextCol(2, 6, "                           ___________________" , - 2);
        $rep->TextCol(4, 9, "   ______________________" , - 2);
        $rep->NewLine();
        $rep->TextCol(1, 3, "          Prepared By" , - 2);
        $rep->TextCol(3, 6, "                Verified  By" , - 2);
        $rep->TextCol(4, 9 , "       Authorized Signatory	" , - 2);
        $rep->Font('');
        $DisplaySubTot = number_format2($SubTotal,$dec);
        $total_discount += ($myrow["discount1"]);
        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal -$myrow["discount1"] - $myrow["discount2"] , $dec);
        $rep->Font('bold');
        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }

        $rep->MultiCell(62, 10, _("Page 1 Of") . ' ' . $rep->pageNumber, 0, 'R', 0, 2, 498, 35, true);
        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }

    if ($email == 0)
        $rep->End();
}

