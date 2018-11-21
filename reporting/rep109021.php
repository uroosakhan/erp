<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Orderssal
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------
function get_shipping_through10902($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers
		WHERE shipper_id=".db_escape($id)."";
    $result = db_query($sql,"check failed");
    $row = db_fetch($result);
    $shipper_name = $row['shipper_name'];
    return $shipper_name;
}
function get_phone_10902($debtor_no)
{
	$sql = "SELECT * FROM ".TB_PREF."debtors_master WHERE debtor_no =".db_escape($debtor_no)." ";

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db);
	return $ft;


}
function get_information($debtor_no)
{
	$sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db);
	return $ft;


}
function get_payment_terms_names($selected_id)
{
	$sql = "SELECT  terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");
	$row =db_fetch_row($result);
	return $row[0];
}
print_sales_orders();

function print_sales_orders()
{
    global $path_to_root, $SysPrefs;

    // $user= get_user($_SESSION["wa_current_user"]->user);
    // if($user['role_id'] == 2)
        include_once($path_to_root . "/reporting/includes/pdf_report_email.inc");
    // else
    //     include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$print_as_quote = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(0, 20, 75, 120, 250, 320, 350, 390,  440, 450,480);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right', 'right', 'left', 'left');

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
		$rep->SetHeaderType('Header109022');
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
	//	$rep->SetHeaderType('Header10902');
		$rep->NewPage();

		$result = get_sales_order_details($i, ST_SALESORDER);
		$SubTotal = 0;
		$items = $prices = array();
		$serial =0;
		while ($myrow2=db_fetch($result))
		{
		    
		    $serial++;
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
			$rep->TextCol(0, 1,$serial, -2);
						$rep->TextCol(1, 2,$myrow2['text4'], -2);

				$rep->TextCol(2, 3,$myrow2['stk_code'], -2);
			
		//	 $rep->NewLine();
			$oldrow = $rep->row;
			$rep->TextColLines(3, 4, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
			{
				$rep->TextCol(4, 5,	$myrow2['text1'], -2);
				 $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
//                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                }
                else
                {
//                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
                }

				$rep->TextCol(5, 6,	$DisplayQty, -2);
//				$rep->TextCol(5, 6,	$DisplayDiscount, -2);
				$rep->TextCol(6, 7,	$DisplayPrice, -2);
				
					$rep->TextCol(7, 8,	$DisplayNet, -2);
				 	$rep->TextCol(9, 10,		$myrow2['text3'], -2);
				
			}
			$rep->row = $newrow;
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
	//	if ($myrow['comments'] != "")
	//	{
	//		$rep->NewLine();
	//		$rep->TextColLines(1, 5, $myrow['comments'], -2);
	//	}
		$DisplaySubTot = number_format2($SubTotal,$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESORDER;

//		$rep->TextCol(3, 6, _("Sub-total"), -2);
//		$rep->TextCol(6, 7,	$DisplaySubTot, -2);
		$rep->NewLine();
		if ($myrow['freight_cost'] != 0.0)
		{
			$DisplayFreight = number_format2($myrow["freight_cost"],$dec);
			$rep->TextCol(3, 6, _("Shipping"), -2);
			$rep->TextCol(6, 7,	$DisplayFreight, -2);
			$rep->NewLine();
		}	
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		
		
		
		
		if ($myrow['tax_included'] == 0) {
		//	$rep->TextCol(3, 6, _("TOTAL ORDER EX VAT"), - 2);
		//	$rep->TextCol(6, 7,	$DisplayTotal, -2);
			$rep->NewLine();
		}

		$tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
		  $myrow['tax_group_id'], $myrow['tax_included'],  null);
		$first = true;
		foreach($tax_items as $tax_item)
		{
			if ($tax_item['Value'] == 0)
				continue;
			$DisplayTax = ($tax_item['Value']);

			$tax_type_name = $tax_item['tax_type_name'];
  $rep->amountcol(6, 7,$DisplayTax ."             ", $dec);
  	$rep->TextCol(3, 6 ,"                        ". $tax_type_name , -2);
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
				$SubTotal_tax += $tax_item['Value'];
			
// $rep->MultiCell(600, 5, $tax_type_name."%",0, 'L', 0, 2, 400, 635, true);
//	$rep->NewLine();
			//	$rep->AmountCol(5, 6,	" ".$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
//		$rep->Font('bold');
//		$rep->TextCol(3, 6, _("TOTAL ORDER VAT INCL."). ' ', - 2);
//		$rep->TextCol(6, 7,	$DisplayTotal.' '.'(' .$rep->formData['curr_code'].')', -2);
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
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
			
//-----------for footer

$total_inclusive_tax = $tax_type_name/100;

$total_with_tax =$SubTotal*$total_inclusive_tax;
$grand_total =$SubTotal+$DisplayTax;
$rep->MultiCell(600, 5, "Total Value Exclusive of Sales Tax            " .price_format($SubTotal),0, 'L', 0, 2, 220, 620, true);
// $rep->MultiCell(600, 5, "Sales Tax                                                   " ,0, 'L', 0, 2, 220, 635, true);
// $rep->MultiCell(600, 5, "Total Value Inclusive of Sales Tax             " .price_format($DisplayTax),0, 'L', 0, 2, 220, 655, true);
// $rep->MultiCell(500, 5, "Transportation                                           ". get_shipping_through10902($myrow['ship_via'])  ,0, 'L', 0, 2, 220, 665, true);
$rep->MultiCell(500, 5, "Amount Received                                      " .price_format($myrow['so_advance']),0, 'L', 0, 2, 220, 668, true);

$rep->MultiCell(500, 5, "Grand Total                                               " .price_format($grand_total - $myrow['so_advance']),0, 'L', 0, 2, 220, 681, true);

    $rep->setfontsize (14);
	$rep->Font('bold');
	$rep->MultiCell(200, 5, "IMEC SOLUTIONS", 0, 'L', 0, 2, 48, 37, true);
	$rep->Font();
//	$rep->setfontsize (8);



	if ($email == 0)
		$rep->End();
}

