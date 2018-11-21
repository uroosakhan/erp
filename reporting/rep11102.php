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

	$cols = array(4,  30, 60, 290, 345, 410, 460);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'centre', 'left', 'left' , 'right');

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

		$rep->SetHeaderType('Header223');
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
//                $item=get_item($myrow2['stk_code']);
            if($pref['alt_uom'] == 1)
            {
                $rep->TextCol(1, 2, $myrow2['units_id'], -2);
            }
            else
            {
                $rep->TextCol(1, 2, $myrow2['units'], -2);
            }
			$newrow = $rep->row;
			$rep->row = $oldrow;

$unit_price=$myrow2["unit_price"] / 1.17;			
$Net_amount=round2( $myrow2["unit_price"] * $myrow2["quantity"],$dec);
 $total_net_amount=($myrow2["quantity"] * $unit_price);
$sales_tax_amount=round2($total_net_amount * 0.17,$dec);
$Gross=$total_net_amount + $sales_tax_amount;


$DisplaySubTot += $total_net_amount;
$Total_tax += $sales_tax_amount;

if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$rep->TextCol(0, 1,	$DisplayQty, -2);

               
				$rep->TextCol(1, 2,	$myrow2['name'], -2);
                                    $rep->TextCol(2, 3,	$myrow2['description'], -2);
				$rep->AmountCol(3, 4,	$unit_price, $dec);
                                //$rep->TextCol(3, 4,	 $total_net_amount, $dec);
				$rep->AmountCol(4, 5,	$total_net_amount, $dec);



                //$rep->AmountCol(5, 6,	$tax_, $dec);
                $rep->AmountCol(5, 6,	$sales_tax_amount, $dec);
                $rep->AmountCol(6, 7,$Gross, $dec);
			}
			$rep->row = $newrow;
			$rep->NewLine(1.5);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();

		}
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			//$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
	//	$DisplaySubTot = number_format2($SubTotal,$dec);
	//	$DisplayFreight = number_format2($myrow["freight_cost"],$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESQUOTE;
//        $DisplaySubTot += $total_net_amount;
//        $DisplayFreight +=$tax_grand_amount;
		$rep->NewLine(1.5);
$total_gst_amount =$DisplaySubTot+ $Total_tax;
		$rep->TextCol(3, 5, _("Total Net Amount"), -2);
$rep->Amountcol(6, 7, $DisplaySubTot, $dec);
		//$rep->AmountCol(6, 7,	$SubTotal, $dec);
		$rep->NewLine();
		$rep->TextCol(3, 5, _("Delivery charges"), -2);

$rep->AmountCol(6, 7, $myrow['freight_cost'], $dec);
		$rep->NewLine();
$rep->TextCol(3, 5, _("Total Sales Tax"), -2);
		$rep->AmountCol(6, 7,	$Total_tax, $dec);
		$rep->NewLine();


		if ($myrow['tax_included'] == 0) {
		//	$rep->TextCol(3, 6, _("TOTAL ORDER EX GST"), - 2);
			//$rep->TextCol(6, 7,	$DisplayTotal, -2);
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
//				else
//					$rep->TextCol(3, 6, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				$rep->TextCol(3, 6, $tax_type_name, -2);
				$rep->TextCol(6, 7,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine(-0.5);
        $DisplayTotal = $Total_gross + $myrow["freight_cost"];
		$rep->Font('bold');
                $rep->newline(0.5);
		$rep->TextCol(3, 6 ,_("TOTAL Quote Including 17% GST."), - 2);
		$rep->AmountCol(6, 7,	$total_gst_amount, $dec);
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
	if ($email == 0)
		$rep->End();
}

?>