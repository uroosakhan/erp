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

print_invoices();

//----------------------------------------------------------------------------------------------------
function get_batch_name_by_id2($batch)
{
    $sql="SELECT  name FROM ".TB_PREF."batch WHERE id=".db_escape($batch);

	$result = db_query($sql,"a location could not be retrieved");

	return db_fetch($result);
}

function get_phone($debtor_no)
{
	$sql = "SELECT phone FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';
	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db,'could not get retail Price');
	return $ft[0];
}

function get_fax($debtor_no)
{
	$sql = "SELECT fax FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db,'could not get retail Price');
	return $ft[0];
}
function get_email_deb($debtor_no)
{
	$sql = "SELECT email FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db,'could not get retail Price');
	return $ft[0];
}

function get_depart_deb($debtor_no)
{
	$sql = "SELECT ref FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';
	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch($db,'could not get retail Price');
	return $ft[0];
}
/*
function get_shipper_name_($id)
{
	$sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

	$result = db_query($sql, "could not get shipper");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_tax_rate_1($tax_type_id)
{
	$sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = $tax_type_id  ";
	$result = db_query($sql, 'error');
	return $result;
}
function get_tax_rate_2()
{
	$sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 2";
	$result = db_query($sql, 'error');
	return $result;
}*/

function get_customer_ntn($customer_id)
{
	$sql = "SELECT ntn_id FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);
	return $row[0];
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
	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();
	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);
	$cols = array(4, 45, 245, 310, 395, 450, 455, 515);
	$aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right');
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
// 		$myrow = get_customer_trans($i, ST_SALESINVOICE);
		$myrow = get_sales_order_header($i, ST_SALESQUOTE);

		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];

		$branch = get_branch($myrow["branch_code"]);
		$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 8, $orientation);
			$rep->title = _('INVOICE');
			$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
		}
		$rep->SetHeaderType('Header10744');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);
		$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
		$baccount['payment_service'] = $pay_service;
		$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESQUOTE, $contacts);
		$rep->NewPage();
// 		$result = get_customer_trans_details(ST_SALESINVOICE, $i);
		$result = get_sales_order_details($i, ST_SALESQUOTE);
		$SubTotal = 0;
		//$other_tax_rate = db_fetch(get_tax_rate_2());
		$total_sales_tax = 0;
		$total_other_sales_tax = 0;
		$gross=0;
		$myrow6 = get_customer($myrow['debtor_no']);
		$sum_inc_salex_tax=0;
		while ($myrow2=db_fetch($result))
		{
			//$tax_rate = db_fetch($tax_items = get_trans_tax_details(ST_SALESINVOICE, $i));
			$tax_rate_tax = db_fetch($tax_items = get_trans_tax_details_tax(ST_SALESINVOICE, $i));
			$tax_rate_extra = db_fetch($tax_items = get_trans_tax_details_extra(ST_SALESINVOICE, $i));

			if ($myrow2["quantity"] == 0)
				continue;

			$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				user_price_dec());
			$Net1 = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]));
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
//	    		$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
			$DisplayNet = number_format2($Net,$dec);
			if($myrow['tax_group_id'] == 1 &&  $myrow2['tax_type_id'] == 2 ||  $myrow2['tax_type_id'] == 3)
			{
				//if($myrow6['ntn_id'] != '')
				//{
					$sales_tax = (("".$tax_rate_tax['rate']*$Net1)/100) ;
					$total_sales_tax += $sales_tax;
				//}

				$other_sales_tax = (($tax_rate_extra['rate']*$Net1)/100) ;
				$total_other_sales_tax += $other_sales_tax;
			}
			else
			{$sales_tax = 0;
				$other_sales_tax = 0;}

			$TotalTax = number_format2($total_sales_tax + $total_other_sales_tax ,$dec);
			$TotalTax20percent = number_format2(((($total_sales_tax + $total_other_sales_tax)*20)/100) ,$dec);

			$sum_inc_salex_tax += $Net + $sales_tax ;
			$gross += $Net;
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//				$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
			$oldrow = $rep->row;
			$rep->Font('bold');
			$rep->TextColLines(1, 2, "".$myrow2['description'], -2);
			$rep->Font('');
			$rep->TextColLines(1, 2, "".get_category_name($myrow2['category_id']), -2);

			$rep->TextColLines(1, 2, "".$myrow2['stock_id'], -2);

                    //   display_error($myrow2['batch']);
$sm=get_batch_name_by_id2($myrow2['batch']);
			$rep->TextColLines(1, 2, "Batch No. : ".$sm['name'],-2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$sales_tax = (($tax_rate_tax['rate']*$Net1)/100) ;
				$rep->TextCol(0, 1,	$myrow2["quantity"]." ".$myrow2['units'], -2);
				$rep->TextCol(2, 3,	$DisplayPrice, -2);
				$rep->TextCol(3, 4,	$DisplayNet, -2);
					$rep->TextCol(4, 5,	$sales_tax, -2);
					$tsales_tax +=$sales_tax;
				if($myrow['tax_group_id'] == 1 &&  $myrow2['tax_type_id'] == 2 ||  $myrow2['tax_type_id'] == 3)
				{
//					if($myrow6['ntn_id'] != '')
//					{
						$rep->Textcol(4, 5, "(".$tax_rate_tax['rate']."%)".$sales_tax,$dec);
				// 		$rep->Textcol(4, 5, $sales_tax,$dec);

//					}

				}
				if($myrow6['ntn_id'] != '') {
					$rep->AmountCol(6, 7, $Net + $sales_tax , $dec);
				}
				else
				{
					$rep->AmountCol(6, 7, $Net + $sales_tax  , $dec);
				}

			}
			$rep->row = $newrow;
			if ($rep->row < $rep->bottomMargin + (10 * $rep->lineHeight))
				$rep->NewPage();
		}

		$memo = get_comments_string(ST_SALESQUOTE, $i);
		if ($memo != "")
		{
			$rep->NewLine();
			//	$rep->TextColLines(1, 5, $memo, -2);
		}

		$DisplaySubTot = number_format2($SubTotal,$dec);
		$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;

		$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
		$first = true;
		while ($tax_item = db_fetch($tax_items))
		{
			if ($tax_item['amount'] == 0)
				continue;
			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
			$DisplayTax1 = round2($sign*$tax_item['amount'], $dec);

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
				//else
				//$rep->NewLine(-5);
				//$rep->TextCol(2, 5, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
			}
//    			else
//    			{
//					//$rep->TextCol(3, 6, $tax_type_name, -2);
//					//$rep->TextCol(6, 7,	$DisplayTax, -2);
//				}
//				$rep->NewLine();
		}

		$rep->NewLine(+4.4);


			$DisplayTotal = $tsales_tax + $SubTotal;

		$rep->Font('bold');

		$rep->TextCol(0, 1, _("Total Rs."), - 2);

//			$rep->TextCol(4, 5, $DisplayTax, - 2);
		$rep->TextCol(3, 4, $DisplaySubTot, -2);
		$rep->AmountCol(4, 5, $tsales_tax, $dec);
		$rep->AmountCol(5, 6, $total_other_sales_tax, $dec);
		$rep->Amountcol(6, 7, $DisplayTotal, $dec);
		$rep->NewLine(-4.4);
		$rep->NewLine(3);
		// $rep->TextCol(0, 4, _("Further Sales Tax @2% of Gross Value of Invoice Amount for Unregistered Person"), - 2);
		if($myrow6['ntn_id'] != '') {
			//  $rep->AmountCol(6, 7, $total_other_sales_tax, $dec);
		}
		$rep->NewLine();
		// $rep->TextCol(0, 4, _("Sales Tax to be withheld and deposited with FBR @ 20% of GST "), - 2);
		if($myrow6['ntn_id'] != '')
		{
			// $rep->TextCol(6, 7, $TotalTax20percent, -2);
		}
		else
		{
			// $rep->TextCol(6, 7, $total_other_sales_tax, -2);
		}
		$rep->NewLine(2.5);
// 		$rep->TextCol(0, 4, _("Total Payable to AMAC (PVT) LTD"), - 2);
// 		$rep->Amountcol(6, 7, $sum_inc_salex_tax, $dec);
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
		$myrow5 = get_branch($myrow['debtor_no']);
// 		$rep->MultiCell(180, 18, $myrow5['br_name'], 0, 'C', 0, 1, 40, 750, true);
		$rep->MultiCell(100, 18, "".$sales_order['reference'], 0, 'C', 0, 1, 150, 302, true);
//		$rep->MultiCell(100, 18, $sales_quotation['reference'], 0, 'C', 0, 1, 250, 302, true);
	}

	$rep->AddPage();
	$rep->NewLine(-55);
	$rep->Font('bold');

	$rep->TextCol(1, 3, _("Terms & Conditions"), - 2);
	$rep->Font('');
	$rep->NewLine(1.5);
	$rep->TextCol(0, 1, _("  1."), - 2);
	$rep->TextColLines(1, 7, "If you have any questions concerning this invoice, Please contact +92-321-9200190.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  2."), - 2);
	$rep->TextColLines(1, 7, "Purchase Order must be minimum 25000 Liter for Fuel or two (2) Drums at-least for Lubricant.", -2);

	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  3."), - 2);
	$rep->TextColLines(1, 7, "Delivery of product shall be within (5) working days after receipt of Purchase Order along with Payment Instrument.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  4."), - 2);
	$rep->TextColLines(1, 7, "Upon acceptance of this document In any case buyer will not deduct any amount or tax under any name, neither income tax nor sales tax against any provision of income tax or sales tax act or law of CBR or FBR of Government of Pakistan, and this cannot be challenged in any court. All the Tax has already been paid by the Company, and other labilities shall be paid by Company.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  5."), - 2);
	$rep->TextColLines(1, 7, "Taxes are included with the unit price and company must not deduct any tax under any name except on service charges for SRB if apply.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  6."), - 2);
	$rep->TextColLines(1, 7, "Income Tax undersection 153(i)a, shall not be withheld by the buyer. In support please review \"Income Tax Ordinance 2001 of WHT u/s 153(I)a, vide: S.R.O. 57(I)/2012, dated 14th Jan 2012, schedule (b), clause 43C. (The provision of clause (a) of sub-section (1) of section 153 shall not be applicable to any payment received by a petroleum agent or distributor who is registered under Sales Tax Act, 1990 on account of supply of petroleum products).", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  7."), - 2);
	$rep->TextColLines(1, 7, "The provision of Sales Tax Special Procedure (Withholding) Rules 2007 is not applicable to the OMC's and its dealers and distributors. Vide: S.R.O. 660(I)/2007, dated: 30th June, 2007. Sales Tax Special Procedure (Withholding) Rules 2007, Rule (5) Exclusions, clause (iii).", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  8."), - 2);
	$rep->TextColLines(1, 7, "Specification of certain registered person to whom Section 8B(1) shall not apply, Vide: S.R.O. 647(I)/2007, dated 27th June, 2007. Sales Tax Act 1990, Chapter-II, Scope and Payment of Tax.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  9."), - 2);
	$rep->TextColLines(1, 7, "The price of the product will be fluctuating with the market as OGRA Pakistan will announce.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  10."), - 2);
	$rep->TextColLines(1, 7, "Payment shall be 100% through bank draft, pay order or any value paid instrument along purchase order.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  11."), - 2);
	$rep->TextColLines(1, 7, "After due date of Invoice financial charges of 19% per month will be charge to customer on the total amount of Invoice and this can not be challenged in any court upon acceptance of this documet.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  12."), - 2);
	$rep->TextColLines(1, 7, "The Sales Tax Invoice will not be submitted in monthly sales tax return in FBR if remain unpaid and only paid/cleared invoices shall be submitted in monthly sales tax return.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  13."), - 2);
	$rep->TextColLines(1, 7, "Make all the instruments payable to AMAC PRIVATE LIMITED, and for Link Account to CHEVRON PAKISTAN LUBRICANTS (PVT) LTD.", -2);
	$rep->NewLine(0.25);
	$rep->TextCol(0, 1, _("  14."), - 2);
	$rep->TextColLines(1, 7, "Quotation, invoices and other documents or informaton shared with the company are considered as Confidential. You are required to maintain confidentiality of these documents and or information, any disclosure of confidential documents and or information will be deemed illegal and will be subject to litigation.", -2);
	// $rep->MultiCell(500, 10, "Suite No. 1, Highway Trade Center, Main Super Highway, M-9 Motorway, Karachi, Pakistan, 74700" ,0, 'C', 0, 1, 50,760, true);
	// $rep->MultiCell(500, 10, "Tell: +92-21-36672525, Fax: +92-21-36631428, Cell: 0321-9200190, Email: amac.pk@gmail.com, info@amac.pk" ,0, 'C', 0, 1, 50,770, true);
	$rep->MultiCell(500, 10, "_______________________________________________________________________________________________________________" ,0, 'C', 0, 1, 50,782, true);
	// $rep->MultiCell(500, 10, "w w w . a m a c . p k" ,0, 'C', 0, 1, 50,781, true);
	$rep->MultiCell(500, 10, "\" This is a computer generated document and requires no signature \"" ,0, 'C', 0, 1, 50,792, true);
	if ($email == 0)
		$rep->End();
}

?>