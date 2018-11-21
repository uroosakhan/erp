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

	$cols = array(2, 40, 160, 220, 280, 327,  400 ,460, 500);

	// $headers in doctext.inc
	$aligns = array('center',	'left',	'left', 'left', 'right', 'right', 'right', 'right');

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
		$rep->SetHeaderType('Header11121');
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
        $total_qty = 0;
		while ($myrow2=db_fetch($result))
		{
			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
			   user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];

			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
			$DisplayNet = $Net;
            $SubTotal += $Net;
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			$rep->TextCol(2, 3,	$myrow2['stk_code'], -2);
$rep->TextCol(0, 1,	$s++, -2);
			$oldrow = $rep->row;
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$rep->AmountCol(4, 5,	$myrow2['amount2'], $dec);
				$total_qty += $myrow2['amount2'];
                $pref = get_company_pref();
                $item=get_item($myrow2['stk_code']);
                    $rep->TextCol(6, 7,	$DisplayPrice, -2);
                    $rep->AmountCol(3, 4,	$myrow2['amount1'], $dec);
                $DisplayNet = $myrow2['amount1'] * $myrow2['amount2'];
                    $rep->TextCol(5, 6,	$DisplayNet, -2);
                $TotalWeight += $DisplayNet;
                $amount = $DisplayPrice * $DisplayNet;
                $rep->TextCol(7, 8,	$amount, -2);

			}
            $rep->TextColLines(1, 2, $myrow2['description'], -2);

            $rep->NewLine();
            if ($rep->row < $rep->bottomMargin +(22 * $rep->lineHeight))
            {
                $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-120,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-310,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-310, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-371,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-371, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-490,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-490, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-50,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-50, $rep->row);


                $rep->Line($rep->row);
                $rep->NewPage();
            }

        }

        $rep->LineTo($rep->leftMargin, 54.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-120,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-190,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-190, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-310,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-310, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-371,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-371, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-490,   54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-490, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-50,  54.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-50, $rep->row);
        $rep->Line($rep->row);

		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
//			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}

        $rep->NewLine();
        $rep->TextCol(1, 2, _("Grand Total"), -2);
        $rep->TextCol(4, 5, $total_qty, -2);
        $rep->TextCol(5, 6, $TotalWeight, -2);
        
        $rep->NewLine();
		$DisplayFreight = number_format2($myrow["freight_cost"],$dec);

//		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESQUOTE;

		$rep->NewLine();
        if($myrow["discount1"] != 0)
        {
            $rep->TextCol(3, 5, _("Discount"), -2);
            $rep->TextCol(6, 8,	$myrow["discount1"], -2);
            $rep->NewLine();
        }
        if($myrow["discount2"] != 0)
        {
            $rep->TextCol(3, 5, _("Discount"), -2);
            $rep->AmountCol(6, 8,	$myrow["discount2"], $dec);
        }
        $DisplaySubTot = number_format2($SubTotal);

        $rep->NewLine();
        $rep->TextCol(3, 5, _("Sub-total"), -2);
        $rep->TextCol(6, 8,	$DisplaySubTot, -2);
        $rep->NewLine(1);
        $rep->TextCol(3, 5, _("Shipping"), -2);
        $rep->TextCol(6, 8,	$DisplayFreight, -2);
        $rep->NewLine();
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);

		if ($myrow['tax_included'] == 0) {
			//$rep->TextCol(3, 6, _("TOTAL ORDER EX GST"), - 2);
			//$rep->TextCol(6, 7,	$DisplayTotal, -2);
			//$rep->NewLine();
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
				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
				{
					if ($first)
					{
						$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
						$rep->TextCol(6, 8,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}

                    $rep->TextCol(3, 6, $tax_type_name, -2);
					$rep->TextCol(6, 8,	$DisplayTax, -2);
					$first = false;
				}
				else
{

					$rep->TextCol(3, 7, $tax_type_name, -2);
$rep->TextCol(6, 8,	$DisplayTax, -2);
}
			}
			else
			{

                $SubTotal +=$tax_item['Value'];
				$rep->TextCol(3, 6, $tax_type_name, -2);
				$rep->TextCol(6, 8,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

        //$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal - $myrow["discount1"] - $myrow["discount2"], $dec);
		$rep->Font('bold');
		$rep->TextCol(3, 6, _("TOTAL ORDER"), - 2);
		$rep->TextCol(6, 8,	$DisplayTotal, -2);
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}
	
                $subtotal_in_words = _number_to_words($SubTotal + $myrow["freight_cost"] ,ST_SALESQUOTE);

$rep->MultiCell(200, 30, "Terms And Conditions : " ,0, 'L', 0, 2, 40,500, true);//S.no
$rep->Font();
$rep->MultiCell(530, 50,  $myrow['term_cond'] ,0, 'L', 0, 2, 40,520, true);//S.no
        $rep->MultiCell(525, 30, "Account Details As follows: " ,0, 'L', 0, 2, 40,640, true);//S.no
        $rep->MultiCell(200, 60, "".$myrow['f_text1'],0, 'L', 0, 2, 40,660, true);//S.no
        $rep->MultiCell(200, 60, $myrow['f_text2'] ,0, 'L', 0, 2, 300,660, true);//S.no


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