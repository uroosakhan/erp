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
print_sales_quotations();

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
function get_security_role1($id)
{
	$sql = "SELECT description FROM ".TB_PREF."security_roles WHERE id=".($id);
	$ret = db_query($sql, "could not retrieve security roles");
	$row = db_fetch_row($ret);
	return $row[0];
}


function get_user_name_70123($user_id)
{
	$sql = "SELECT * FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch($result);
	return $row;
}


function get_user_name_($user_id)
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

	$cols = array(6, 50, 200, 255, 320,  390, 460);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'center', 'center', 'center', 'center', 'center');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_("SALES QUOTATION"), "SalesQuotationBulk", user_pagesize(), 9, $orientation);
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
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			if ($print_invoice_no == 1)
				$rep->filename = "SalesQuotation" . $i . ".pdf";
			else	
				$rep->filename = "SalesQuotation" . $myrow['reference'] . ".pdf";
		}
		$rep->SetHeaderType('Header111000121');
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
$s=1;
		while ($myrow2=db_fetch($result))
		{
			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
			   user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
			$DisplayNet = number_format2($Net,$dec);
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			//$rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
				$rep->SetTextColor(0, 0, 0);
$rep->TextCol(0, 1,	"   ".$s++, -2);
			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
		
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
//				$rep->TextCol(3, 4,	, -2);
				$rep->TextCol(4, 5,	$DisplayQty, -2);
                $rep->TextCol(6, 7,	number_format2($Net,$dec), -2);

//				$rep->TextCol(4, 5,	$myrow2['text3'], -2);
//				$rep->TextCol(6, 5,	"  ".$myrow2['text5'], -2);
                $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
//                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                }
                else{
//                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
                }
//                    $rep->TextCol(5, 6,	$myrow2['text1']."  ", -2);
                    $rep->TextCol(5, 6,	$DisplayPrice, -2);

                $rep->MultiCell(525, 30, "1" ,0, 'L', 0, 2, 49,420, true);//S.no
                $rep->MultiCell(525, 30, "2" ,0, 'L', 0, 2, 49,450, true);//S.no
                $rep->MultiCell(525, 30, "3" ,0, 'L', 0, 2, 49,480, true);//S.no
                $rep->MultiCell(525, 30, "4" ,0, 'L', 0, 2, 49,507, true);//S.no
                $rep->MultiCell(525, 30, "5" ,0, 'L', 0, 2, 49,540, true);//S.no
                $rep->MultiCell(410, 30, "".$myrow['f_text2'] ,0, 'L', 0, 2, 152,440, true);//S.no delivery
                $rep->MultiCell(410, 30, "".$myrow['f_text3'] ,0, 'L', 0, 2, 152,472, true);//S.no payment
                $rep->MultiCell(410, 30, "".$myrow['f_text4'] ,0, 'L', 0, 2, 152,500, true);//S.no warranty
                $rep->MultiCell(410, 30, "".$myrow['f_text5'] ,0, 'L', 0, 2, 152,530, true);//S.no validty
                $rep->MultiCell(825, 30, "".$myrow['h_text2'] ,0, 'L', 0, 2, 40,123, true);//S.no
                $rep->MultiCell(825, 30, "".$myrow['h_text3'] ,0, 'L', 0, 2, 40,110, true);//S.no
            
              
           
              //  $rep->MultiCell(800, 20, "Grand Total Inclusive of Taxes  ",0, 'L', 0, 2, 299,312, true);
    // $rep->Font('bold');

			}
			                $rep->MultiCell(525, 30, "".$myrow['f_text1'] ,0, 'L', 0, 2, 152,413, true);//S.no

			                $rep->MultiCell(825, 30, "".$myrow['f_text6'] ,0, 'L', 0, 2, 65,186, true);//S.no

			     $rep->MultiCell(825, 30, "This refers to the subject. Please find below our most competitive prices as under." ,0, 'L', 0, 2, 40,210, true);//S.no
                $rep->MultiCell(825, 30, "Dear Sir," ,0, 'L', 0, 2, 40,200, true);//S.no
			    $rep->MultiCell(860, 30, "".$myrow['delivery_address'] ,0, 'L', 0, 2, 40,145, true);//S.no
                $rep->MultiCell(860, 30, "".$myrow['deliver_to'] ,0, 'L', 0, 2, 40,135, true);//S.no

            $rep->row = $newrow;
			//$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
 	//	$rep->MultiCell(525, 30, "".$myrow['comments'],0, 'L', 0, 2, 40,740, true);//S.no
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
// 			$rep->TextColLines(1, 5, $myrow['comments'], -2);
						


		}
		$DisplaySubTot = number_format2($SubTotal,$dec);
		$DisplayFreight = number_format2($myrow["freight_cost"],$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESQUOTE;
        $rep->NewLine(-26);
 		$rep->TextCol(3, 4, _("    Sub-total"), -2);
		$rep->TextCol(6, 7,	$DisplaySubTot, -2);
		$rep->NewLine();
		//$rep->TextCol(3, 5, _("Shipping"), -2);
//		$rep->TextCol(5, 7,	$DisplayFreight, -2);
		//$rep->NewLine();
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);

		if ($myrow['tax_included'] == 0) {
//			$rep->TextCol(3, 6, _("TOTAL ORDER EX GST"), - 2);
//			$rep->TextCol(6, 7,	$DisplayTotal, -2);
			//$rep->NewLine();
		}

		$tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
		  $myrow['tax_group_id'], $myrow['tax_included'],  null);
		$first = true;
		foreach($tax_items as $tax_item)
		{
			if ($tax_item['Value'] == 0)
				continue;
			$DisplayTax = $tax_item['Value'];
         //   display_error($SubTotal);
			$tax_type_name = $tax_item['tax_type_name'];

			if ($myrow['tax_included'])
			{
				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
				{
					if ($first)
					{
						$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
						$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
				// 	$rep->TextCol(3, 6, $tax_type_name, -2);
					 $rep->MultiCell(800, 20, $tax_type_name,0, 'L', 0, 2, 285,300, true);
                    $rep->TextCol(6, 7,	$DisplayTax, -2);
                    	$rep->NewLine(2);
            $rep->TextCol(2, 4, _("Grand Total Inclusive of Taxes"), -2);
			$rep->TextCol(6, 7,	number_format2($SubTotal +$DisplayTax , $dec), -2);
					$first = false;
				}
				else
{
    	$rep->NewLine(-2);
//					$rep->TextCol(3, 7, $tax_type_name, -2);
					$rep->TextCol(6, 7,	$DisplayTax, -2);
    $rep->MultiCell(800, 20, $tax_type_name,0, 'L', 0, 2, 285,300, true);
    	$rep->NewLine(2);
            $rep->TextCol(2, 4, _("Grand Total Inclusive of Taxes"), -2);
			$rep->TextCol(6, 7,	number_format2($SubTotal +$DisplayTax , $dec), -2);

}
			}
			else
			{
			    $rep->NewLine(-2);
				$SubTotal += $tax_item['Value'];
				$inclusive_tax = $tax_item['Value'] + $DisplayPrice;
              	 $rep->MultiCell(800, 20, $tax_type_name,0, 'L', 0, 2, 285,300, true);
                	$rep->TextCol(6, 7,	$DisplayTax, -2);
              

			
						  //  $rep->NewLine(-2);
						    		     $rep->NewLine(2);
						    		         $rep->TextCol(2, 4, _("Grand Total Inclusive of Taxes"), -2);
			$rep->TextCol(6, 7,	number_format2($SubTotal +$DisplayTax , $dec), -2);
             

            }
			$rep->NewLine();
            

        }
        $rep->Font('bold');
        $rep->MultiCell(280, 20, "Rupees  ".convert_number_to_words($SubTotal +$DisplayTax) ."  Only",0, 'L', 0, 2, 75,340, true);

		//$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		$rep->Font('bold');
// 		$rep->TextCol(4, 6, _("TOTAL ORDER"), - 2);
// 		$rep->TextCol(6, 7,	$DisplayTotal, -2);
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}
			$user =get_user_id($i ,ST_SALESQUOTE);
			
			$a =get_user_name_70123($user);
        $rep->MultiCell(100, 25, "".$a['real_name'] ,0, 'L', 0, 2, 40,590, true);
  
        $rep->MultiCell(100,12, get_security_role1($a['role_id']),0,'L',0,0,40,623);


$rep->Font();
$rep->NewLine(3.5);
                $subtotal_in_words = _number_to_words($SubTotal + $myrow["freight_cost"] ,ST_SALESQUOTE);
		//$rep->TextCol(0, 7, "Amount in words: ".$subtotal_in_words, -2);
//$rep->MultiCell(525, 30, "Amount in words: ".$subtotal_in_words ,0, 'L', 0, 2, 40,720, true);//S.no
// $rep->MultiCell(525, 30, "2. Validity of Quotation : 5 - Working Days in terms of price & delivery." ,0, 'L', 0, 2, 40,760, true);//S.no
// $rep->MultiCell(525, 30, "3. Lead Time: 6-7 Weeks after confirmation of your order along with P.O." ,0, 'L', 0, 2, 40,780, true);//S.no
        $rep->Font('b');
        $rep->MultiCell(525, 30, "Regards" ,0, 'L', 0, 2, 40,570, true);//S.no
        // $rep->Font('bold');

        $rep->MultiCell(825, 30, "Ref # ".$myrow["reference"] ,0, 'L', 0, 0, 453,134, true);//S.no
 

        if ($email == 1)
		{
			if ($print_invoice_no == 1)
				$myrow['reference'] = $i;
			$rep->End($email);
		}
	}
	if ($email == 0)
		$rep->End();
}

?>