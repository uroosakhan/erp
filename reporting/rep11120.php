<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Quotations
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------

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




function get_user_id1($trans_no,$type)
{
    $sql= "SELECT user FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not process Requisition to Purchase Order");
    $row = db_fetch_row($result);
    return $row[0] ;
}

// function get_designation_name1($id)
// {
//     $sql="SELECT description FROM security_roles where id=".db_escape($id)."";
//     $db = db_query($sql,'Can not get Designation name');
//     $ft = db_fetch($db);
//     return $ft[0];
// }
function get_security_role11120($id)
{
    $sql = "SELECT description FROM ".TB_PREF."security_roles WHERE id=".($id);
    $ret = db_query($sql, "could not retrieve security roles");
    $row = db_fetch_row($ret);
    return $row[0];
}


function get_user_name_70123($user_id)
{
    $sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}


function get_user_name_($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}


function get_phone_($debtor_no)
{
    $sql = "SELECT phone FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

    $db  = db_query($sql,"item prices could not be retreived");
    $ft = db_fetch_row($db);
    return $ft[0];


}function get_fax_($debtor_no)
{
    $sql = "SELECT fax FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

    $db  = db_query($sql,"item prices could not be retreived");
    $ft = db_fetch_row($db);
    return $ft[0];


}


function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 1";
    $result = db_query($sql, 'error');
    return $result;
}
print_sales_quotations();

function print_sales_quotations()
{
	global $path_to_root, $print_as_quote, $print_invoice_no, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(2,28,  50, 80,400,470, 450);

	// $headers in doctext.inc
	$aligns = array('left','left', 'centre', 'left', 'left', 'left', 'left' , 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_("SALES QUOTATION"), "SalesQuotationBulk", user_pagesize(), 8, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_sales_order_header($i, ST_SALESQUOTE);
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 8, $orientation);
			if ($print_invoice_no == 1)
				$rep->filename = "SalesQuotation" . $i . ".pdf";
			else	
				$rep->filename = "SalesQuotation" . $myrow['reference'] . ".pdf";
		}

		$rep->SetHeaderType('Header11120');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESQUOTE, $contacts);
		//$rep->headerFunc = 'Header2';
		$rep->NewPage();

		$result = get_sales_order_details($i, ST_SALESQUOTE);
		$SubTotal = 0;
		$items = $prices = array();
        $myrow3 = db_fetch(get_tax_rate_1());
        $DisplaySubTot=0;
        $DisplayFreight=0;
        $price_net=0;
		$Total_tax =0;
		$Total_gross =0;
		$DisplayTotal =0;
		$s_no =0;
$total_gst_amount=0;
        while ($myrow2=db_fetch($result))
		{
			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
			   user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = $myrow2["quantity"];
			$DisplayNet = number_format($Net,$dec);

			$tax_= number_format(($myrow2["quantity"]*$myrow2["unit_price"]*$myrow3['rate'])/100, $dec);
			$tax__= round2(($myrow2["quantity"]*$myrow2["unit_price"]*$myrow3['rate'])/100, user_price_dec());
			//$Total_tax += $tax__;
			$gross_amount_= number_format((($myrow2["quantity"]*$myrow2["unit_price"]*$myrow3['rate'])/100)+($myrow2["quantity"]*$myrow2["unit_price"]), $dec);
			$gross_amount__= round2((($myrow2["quantity"]*$myrow2["unit_price"]*$myrow3['rate'])/100)+($myrow2["quantity"]*$myrow2["unit_price"]), user_price_dec());
			$Total_gross +=$gross_amount__;
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			//$rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
			$oldrow = $rep->row;
		 $pref = get_company_pref();
                $item=get_item($myrow2['stk_code']);//category_id
                //  $rep->TextCol(2, 3, $item['category_id'], -2);
            if($pref['alt_uom'] == 1)
            {
                $rep->TextCol(2, 3, $myrow2['units_id'], -2);
            }
            else
            {
                $rep->TextCol(2, 3,$myrow2['units'], -2);
            }
//			$newrow = $rep->row;
//			$rep->row = $oldrow;

$unit_price=$myrow2["unit_price"];			
$Net_amount=round2( $myrow2["unit_price"] * $myrow2["quantity"],$dec);
 $total_net_amount=($myrow2["quantity"] * $unit_price);
// $sales_tax_amount=round2($total_net_amount * 0.17,$dec);
$Gross=$unit_price + $sales_tax_amount;


$DisplaySubTot += $total_net_amount;
$Total_tax += $sales_tax_amount;

if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{

                $s_no ++;
				$rep->TextCol(0, 1,	$s_no, -2);
				$rep->TextCol(1, 2,	$DisplayQty, -2);
				$rep->AmountCol(4, 5,	$myrow2["unit_price"], $dec);
                $rep->AmountCol(5, 6,$total_net_amount, $dec);

                 $item=get_item($myrow2['stk_code']);
                $fno = get_item($myrow2['stk_code']);
//                $f1 = .get_category_name($myrow2['category_id']);
                $desc = "Make:".get_category_name($myrow2['category_id'])."\n"."Model:".$myrow2['description']."\n"."Delivery Time:".$myrow2['text5']."\n"."Description:".$fno['long_description'].$fno['text1']." ".$fno['text2'];

//                 $rep->TextCol(3, 4,	"Make:".get_category_name($myrow2['category_id']), -2);
//					$rep->NewLine();
////					$rep->NewLine();
//				$rep->TextCol(3, 4,	"Model:".$myrow2['description'], -2);
//					$rep->NewLine();
//                $rep->TextCol(3, 4,	"Delivery Time:".$myrow2['text5'], -2);
//                $rep->NewLine();
					$rep->TextColLines(3, 4,	$desc, -2);
//                $rep->NewLine(2);
//					$rep->NewLine();


			
                                //$rep->TextCol(3, 4,	 $total_net_amount, $dec);
				// $rep->AmountCol(5, 6,	$total_net_amount, $dec);


//    $rep->Line($rep->row);
                //$rep->AmountCol(5, 6,	$tax_, $dec);
                // $rep->AmountCol(6, 7,	$sales_tax_amount, $dec);
			}
//			$rep->row = $newrow;
//			$rep->NewLine();
//			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//				$rep->NewPage();
            if ($rep->row < $rep->bottomMargin +(5*$rep->lineHeight))
            {
                $rep->LineTo($rep->leftMargin, 47.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-130,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-130, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-60,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-60, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-450,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-450, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-500,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-500, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-480,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-480, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-360,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-360, $rep->row);
//                $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }

		}
        $rep->LineTo($rep->leftMargin, 47.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  47.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-130,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-130, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-60,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-60, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-450,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-450, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-500,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-500, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-480,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-480, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-360,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-360, $rep->row);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  47.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
        $rep->Line($rep->row);
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			//$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		
		    if($myrow["discount1"] != 0)
        {
            // $rep->NewLine();
            // $rep->TextCol(3, 6, _("Discount"), -2);
            // $rep->TextCol(4, 5,	price_format($total_discount), -2);
            $discount_value =$myrow["discount1"];
              $rep->MultiCell(100, 30, "Discount ",0, 'L', 0, 2, 120,629, true);
                    $rep->MultiCell(100, 30, price_format($myrow["discount1"]),0, 'R', 0, 2, 460,629, true);

             $rep->NewLine();
        }
        if($myrow["discount2"] != 0)
        {
            // $rep->TextCol(3, 6, _("Discount"), -2);
           $discount_value =$myrow["disc2"];
           $rep->MultiCell(100, 30, "Discount",0, 'L', 0, 2, 120,618, true);
                    $rep->MultiCell(100, 30, price_format($myrow["discount2"]),0, 'R', 0, 2, 460,618, true);

        }
		$DisplaySubTot = $SubTotal;
		$doctype = ST_SALESQUOTE;
        $rep->NewLine();
		$rep->TextCol(3,6, _("Total Net Amount"), -2);
$rep->Amountcol(5, 6, $DisplaySubTot, $dec);
		$rep->NewLine();
		if ($myrow['tax_included'] == 0) {
			$rep->NewLine();
		}
         $total_displaytax  = 0;
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
				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
				{
					if ($first)
					{
						$rep->TextCol(3,6, _("Total Tax Excluded"), -2);
						$rep->TextCol(5, 6,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
						$rep->NewLine(-0.8);
					$rep->TextCol(3,6, $tax_type_name, -2);
					$rep->TextCol(5, 6,	$DisplayTax, -2);
					$total_displaytax = $tax_item['Value'];
					$rep->NewLine(+0.8);
					$first = false;
				}
//				else
//					$rep->TextCol(3, 6, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
					$rep->NewLine(-0.8);
				$rep->TextCol(3,6, $tax_type_name, -2);
				$rep->TextCol(5, 6,	$DisplayTax, -2);
				$total_displaytax = $tax_item['Value'];
					$rep->NewLine(+0.8);
			}
			$rep->NewLine();
		}

// 		$rep->NewLine(-0.5);
        $DisplayTotal = $Total_gross + $myrow["freight_cost"];
		$rep->Font('bold');
                // $rep->newline(0.5);

                $total_gst_amount = $DisplaySubTot + $total_displaytax;
	$rep->NewLine(-0.5);
		$rep->TextCol(3,6 ,_("TOTAL Quote Value"), - 2);
		$rep->AmountCol(5, 6,	$total_gst_amount - $myrow["discount1"] - $myrow["discount2"], $dec);
	$rep->NewLine(+0.5);

		   
		  //  $rep->MultiCell(800, 20, "Amount in Words:" . "  " . convert_number_to_words($total_gst_amount) . " " . "Only", 0, 'L', 0, 2, 45, 680, true);

		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
       // $rep->MultiCell(250, 20, "Tel:".get_phone_($myrow['debtor_no']), 0, 'L', 0, 2, 45,230, true);
		
     //   $rep->MultiCell(250, 20, "Fax:".get_fax_($myrow['debtor_no']), 0, 'L', 0, 2, 45,240, true);

        if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}	
		$rep->Font();
		if ($email == 1)
		{
			if ($print_invoice_no == 1)
				$myrow['reference'] = $i;
			$rep->End($email);
		}
	}
// 		$rep->MultiCell(800, 25, "This document is system generated, doesn't require signature" ,0, 'L', 0, 2, 170,810, true);


    // $user =get_user_id($myrow['order_no'],ST_SALESQUOTE);
    // $rep->MultiCell(200, 25, "".get_user_name_70123($myrow['salesman']) ,0, 'L', 0, 2, 50,805, true);
    // $username =get_salesman_name($myrow['order_no']);
    // $rep->MultiCell(200, 25, "".get_user_name_70123($user) ,0, 'L', 0, 2, 50,805, true);
    $rep->NewLine();
		   		$rep->TextCol(0, 8,"Amount in Words:" . "  " . convert_number_to_words($total_gst_amount) . " " . "Only", $dec);
		   		$rep->NewLine();
		   		$rep->TextCol(0, 8, "Note:", $dec);
		   		$rep->TextCol(2, 10, $myrow['comments'], $dec);
		   			$rep->NewLine();
		   		$rep->TextCol(0, 8, "Term and Conditions:", $dec);
// 		$rep->NewLine();
		   		$rep->TextCol(0, 8, "_________________", $dec);
		   		$rep->NewLine();
		   		$rep->TextCol(1, 10, "Payment Terms:       ".$myrow['f_text2'], $dec);
	$rep->NewLine();
		   		$rep->TextCol(1, 10, "Quotation Validity:    ".$myrow['f_text3'], $dec);

	$rep->NewLine();
		   		$rep->TextCol(1, 10, "Warranty :                ".$myrow['f_text7'] , $dec);

	$rep->NewLine();
		  // 		$rep->TextCol(1, 10, get_user_name_70123($myrow['salesman']) , $dec);

// 	$rep->NewLine(.5);/
		  // 		$rep->TextCol(1, 10, "Thanks & Regards" , $dec);


$rep->MultiCell(800, 25, "Thanks & Regards"  ,0, 'L', 0, 2, 40,800, true);
        $rep->MultiCell(800, 25, get_user_name_70123($myrow['salesman'])  ,0, 'L', 0, 2, 40,810, true);


    $rep->fontSize -= 11;
    $rep->setfontsize(9);
    
    $rep->MultiCell(800, 25, "This document is system generated, doesn't require signature" ,0, 'L', 0, 2, 160,815, true);
    $rep->setfontsize(14);
    $rep->Font('bold');
    // $rep->MultiCell(250, 20, "Note:", 0, 'L', 0, 2, 45,693, true);
    $rep->Font('');
    $rep->setfontsize(10);
    // $rep->MultiCell(800, 20,"".$myrow['comments'] , 0, 'L', 0, 2, 90,696, true);





//     $rep->MultiCell(250, 20, "Term and Conditions:", 0, 'L', 0, 2, 45,722, true);
//     $rep->MultiCell(250, 20, "_________________", 0, 'L', 0, 2, 45,722, true);
//     $rep->Font('');
//     $rep->setfontsize(10);
    // $rep->MultiCell(800, 20,"Payment Terms:       ".$myrow['f_text2'] , 0, 'L', 0, 2, 55,742, true);
//     $rep->MultiCell(800, 20,"Quotation Validity:    ".$myrow['f_text3'] , 0, 'L', 0, 2, 55,756, true);
//     $rep->MultiCell(800, 20,"Warranty :          ".$myrow['f_text7'] , 0, 'L', 0, 2, 55,770, true);
// //    f_text7
    // $rep ->MultiCell(170, 150, "Thanks & Regards" , 0, 'L', 0, 2, 50,790, true);



    $rep->fontSize += 11;
    if ($email == 0)
		$rep->End();
}

?>