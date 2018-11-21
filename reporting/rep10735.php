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

// function get_customer_ovgst($customer_id)
// {
// 	$sql = "SELECT ntn_id FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

// 	$result = db_query($sql, "could not get customer");

// 	$row = db_fetch_row($result);
// 	return $row[0];
// }
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
		$myrow = get_customer_trans($i, ST_SALESINVOICE);
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
		$rep->SetHeaderType('Header10735');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);
		$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
		$baccount['payment_service'] = $pay_service;
		$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
		$rep->NewPage();
		$result = get_customer_trans_details(ST_SALESINVOICE, $i);
		$SubTotal = 0;
		//$other_tax_rate = db_fetch(get_tax_rate_2());
		$total_sales_tax = 0;
		$total_other_sales_tax = 0;
		$gross=0;
$total_tax_values = 0;
		$myrow6 = get_customer($myrow['debtor_no']);
		$sum_inc_salex_tax=0;
		$value_inc = 0;
		$total_gross_with_saletax =0;
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
			{
			    $sales_tax = 0;
				$other_sales_tax = 0;
			    
			}

			$TotalTax = number_format2($total_sales_tax + $total_other_sales_tax ,$dec);
			$TotalTax20percent = number_format2(((($total_sales_tax + $total_other_sales_tax)*20)/100) ,$dec);
	$sales_tax = (($tax_rate_tax['rate']*$Net1)/100) ;
			$sum_inc_salex_tax += $Net + $sales_tax ;
			$gross = $Net + $sales_tax;
			$value_inc = $gross +  $myrow['ov_gst'];
// 			display_error($gross +  $myrow['ov_gst']);
            //   display_error($sales_tax);
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//				$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
			$oldrow = $rep->row;
			$rep->Font('bold');
			$rep->TextColLines(1, 2, "".$myrow2['StockDescription'], -2);
			$rep->Font('');
			$rep->TextColLines(1, 2, "".get_category_name($myrow2['category_id']), -2);

			$rep->TextColLines(1, 2, "".$myrow2['stock_id'], -2);

                       
//$sm=get_batch_from_stock_moves($myrow2['stock_id']);
			$rep->TextColLines(1, 2, "Batch No. : ".$myrow2['batch'],-2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
			
				$rep->TextCol(0, 1,	$myrow2["quantity"]." ".$myrow2['units'], -2);
				$rep->TextCol(2, 3,	$DisplayPrice, -2);
				$rep->TextCol(3, 4,	$DisplayNet, -2);
// display_error($value_inc);
					$rep->TextCol(4, 5,"". $tax_rate_tax['rate']."%"  , -2);
                $rep->AmountCol(6, 7, $value_inc  , $dec);
                $total_gross_with_saletax +=$value_inc;
                
				if($myrow['tax_group_id'] == 1 &&  $myrow2['tax_type_id'] == 2 ||  $myrow2['tax_type_id'] == 3)
				{
//					if($myrow6['ntn_id'] != '')
//					{
//						$rep->Textcol(4, 5, "(".$tax_rate_tax['rate']."%)".$sales_tax,$dec);
//					}

				}
//				if($myrow6['ntn_id'] != '') {
//					$rep->AmountCol(6, 7, $myrow['ov_gst'] , $dec);
//				}
//				else
//				{
//					$rep->AmountCol(6, 7, $myrow['ov_gst']  , $dec);
//				}

			}
			$rep->row = $newrow;
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}

		$memo = get_comments_string(ST_SALESINVOICE, $i);
		if ($memo != "")
		{
			$rep->NewLine();
			//	$rep->TextColLines(1, 5, $memo, -2);
		}

		$DisplaySubTot = number_format2($SubTotal,$dec);
		$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
		if($myrow['discount1'] != 0) {

                  $discount_value =$myrow["disc1"]/100;
 
                    $rep->MultiCell(410, 30, "".price_format(($myrow["discount1"])) ,0, 'R', 0, 2, 150,658, true);
  $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 305,660, true);
		}
		
		if($myrow['discount2'] != 0) {

 $discount_value =$myrow["disc2"]/100;
                    $rep->MultiCell(410, 30, "".price_format($myrow["discount2"]) ,0, 'R', 0, 2, 150,658, true);
                    $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 305,658, true);

            $tot_amt =$tot_net - $myrow['discount2'];
            $rep->NewLine();
		}
		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;
$total_tax_values = 0;
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


			$DisplayTotal = number_format2($sign * ($myrow["ov_freight"] + $myrow["ov_gst"] +
					$myrow["ov_amount"] + $myrow["ov_freight_tax"] +  $total_other_sales_tax), $dec);

		$rep->Font('bold');

		$rep->TextCol(0, 1, _("Total Rs."), - 2);
// display_error($gross."++".$sales_tax);
$total_gross_with_saletax = $sum_inc_salex_tax - $myrow["discount1"] - $myrow["discount2"];
// display_error($total_gross_with_saletax);

//			$rep->TextCol(4, 5, $DisplayTax, - 2);
		$rep->TextCol(3, 4, $DisplaySubTot, -2);
		$rep->AmountCol(4, 5, $total_sales_tax, $dec);
// 		$rep->AmountCol(5, 6, $total_other_sales_tax + $total_tax_values, $dec);
		$rep->Amountcol(6, 7, $total_gross_with_saletax, $dec);
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
		$rep->TextCol(0, 4, _("Total Payable to ").$rep->company['coy_name'], - 2);
		$rep->Amountcol(6, 7, $sum_inc_salex_tax + $myrow['ov_gst'] - $myrow["discount1"] - $myrow["discount2"] , $dec);
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
// 		$rep->MultiCell(100, 18, $sales_order['reference'], 0, 'C', 0, 1, 145, 302, true);
//		$rep->MultiCell(100, 18, $sales_quotation['reference'], 0, 'C', 0, 1, 250, 302, true);
	}

	if ($email == 0)
		$rep->End();
}

?>