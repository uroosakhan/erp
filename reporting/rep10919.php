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
function get_user_name_701234($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
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

    $cols = array(4, 30, 85,  250, 310, 365, 400,470);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'left', 'left', 'right', 'right', 'right');

    $params = array('comments' => $comments, 'print_quote' => $print_as_quote);

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
        if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
            continue;
        }
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1)
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
        // $rep->SetHeaderType('Header1090');
        $rep->currency = $cur;
        $rep->Font();
        if ($print_as_quote == 1)
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
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);


          $rep->SetHeaderType('Header10919');
        $rep->NewPage();

// 	{
// // 		$image = company_path() . '/images/'. $rep ->company['coy_logo'];
// 		$imageheader = company_path() . '/images/Footer.png';
// //		if (file_exists($image))
// //		{
// //			display_error("gj01");
// 		//$rep->NewLine();
// 		if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
// 			$rep->NewPage();
// 			$rep->AddImage($image, $rep->cols[1] +300, $rep->row +225, null,$rep->company['logo_w'], $rep->company['logo_h']);

// 		$rep->AddImage($imageheader, $rep->cols[1] -55, $rep->row -478, 510,20, $SysPrefs->pic_height);
// //		echo '<center><img src='headers.PNG' ></center>';

// //			$rep->AddImage($imageheader, $rep->cols[1] +320, $rep->row +580, 100, $SysPrefs->pic_height);
// //		$rep->Text(cols[1] +300, $rep->company['coy_name'], $icol);
// //				$rep->row -= $SysPrefs->pic_height;
// 		$rep->NewLine();
// 		//}
// 	}




        $result = get_sales_order_details($i, ST_SALESORDER);
        $SubTotal = 0;
        $items = $prices = array();
        $serial =0;
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
                $serial++;

            $rep->TextCol(0, 1, $serial, -2);

            $rep->TextCol(1, 2,	$myrow2['stk_code'], -2);
            $oldrow = $rep->row;

            $rep->TextColLines(2, 3, $myrow2['description'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
             //   $rep->TextCol(2, 3,	$DisplayQty, -2);
                $pref = get_company_pref();
                $item=get_item($myrow2['stk_code']);
                // if($pref['alt_uom'] == 1)
                // {
                //     $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                // }
                // else
                // {
                //     $rep->TextCol(3, 4,	$myrow2['units'], -2);
                // }



   if($myrow2['units_id']=='pack'){
                    $rep->TextCol(3, 4,$DisplayQty	, -2);

                    $carton=$myrow2["quantity"]/$item["con_factor"];
                    $rep->TextCol(4, 5,number_format2($carton,user_percent_dec())	, -2);
                    $total_cartons +=$carton;

                    $total_packing +=$myrow2["quantity"];

                }
                else{
                    
                    $cartons=$myrow2["quantity"]*$item["con_factor"];

                    $rep->TextCol(3, 4,$cartons	, -2);
                    $rep->TextCol(4, 5,$DisplayQty	, -2);

                    $total_cartons +=$myrow2["quantity"];

                    $total_packing +=$cartons;



                }






                $rep->TextCol(5, 6,	$DisplayPrice, -2);
				$rep->TextCol(6, 7,	$DisplayDiscount, -2);
                $rep->TextCol(7, 8,	$DisplayNet, -2);
            }
            $rep->row = $newrow;
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }
        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal,$dec);
        $total_discount += ($myrow["discount1"]);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESORDER;

        //$rep->TextCol(3, 6, _("Sub-total"), -2);
        //$rep->TextCol(6, 7,	$DisplaySubTot, -2);
        $rep->NewLine();
        if($myrow["discount1"] != 0)
        {
            $rep->TextCol(3, 6, _("Discount").$myrow["discount_percent"], -2);
            $rep->TextCol(6, 7,	price_format($total_discount), -2);
             $rep->NewLine();
        }
        if($myrow["discount2"] != 0)
        {
            $rep->TextCol(3, 6, _("Discount"), -2);
            $rep->AmountCol(6, 7,	$myrow["discount2"], $dec);
        }
        $rep->NewLine();
        if ($myrow['freight_cost'] != 0.0)
        {
            $DisplayFreight = number_format2($myrow["freight_cost"],$dec);
            $rep->TextCol(3, 6, _("Shipping"), -2);
            $rep->TextCol(6, 7,	$DisplayFreight, -2);
            $rep->NewLine();
        }
        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal -$myrow["discount2"] , $dec);
        if ($myrow['tax_included'] == 0) {
            $rep->TextCol(4, 7, _("TOTAL ORDER EX TAXES"), - 2);
            $rep->TextCol(7, 8,	$DisplayTotal , -2);
            $rep->NewLine();
        }

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
                if ($SysPrefs->alternative_tax_include_on_docs() == 1)
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
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount"). ": " , -2);
                $rep->TextCol(6, 7,	$DisplayTax , -2);
            }
            else
            {
                     
                $SubTotal += $tax_item['Value'];
                $rep->TextCol(3, 6, $tax_type_name, -2);
                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
      
            $rep->NewLine();
        }

        $rep->NewLine();

        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal -$myrow["discount1"] - $myrow["discount2"] , $dec);
        $rep->Font('bold');
        $rep->TextCol(4, 7, _("TOTAL ORDER INCL.TAXES").' '.'(' .$rep->formData['curr_code'].')', - 2);
        $rep->TextCol(7, 8,	$DisplayTotal, -2);
        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
 
            $user =get_user_id($myrow['order_no'],ST_SALESORDER);
        $rep->MultiCell(100, 25, "".get_user_name_701234($user) ,0, 'C', 0, 2, 87,755, true);

        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    
    ///hareem
 
    
    
    if ($email == 0)
        $rep->End();
}

