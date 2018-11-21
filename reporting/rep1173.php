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
function get_customer_information($debtor_no)
{
	$sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
	$result = db_query($sql,"Error");
	return db_fetch($result);
}

/*function get_tax ($id)
{
	$sql = "SELECT rate FROM ".TB_PREF."tax_types WHERE id = ".db_escape($id)."
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}*/


function get_tax_rate($trans_no, $tax_type_id)
{
	$sql = "SELECT rate, amount FROM ".TB_PREF."trans_tax_details
	WHERE trans_no = ".db_escape($trans_no)."
	AND tax_type_id = ".db_escape($tax_type_id)."
	AND trans_type = 10";
	$result = db_query($sql, "could not retreive default customer currency code");
    return db_fetch($result);

}

function get_tax_amount($trans_no, $stock_id)
{
	$sql = "SELECT (unit_tax * quantity) FROM ".TB_PREF."debtor_trans_details 
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}

print_invoices();

//----------------------------------------------------------------------------------------------------

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

	$orientation = ('P');
	//$dec = user_price_dec();
	$dec = 0;

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(6, 25, 125, 200, 250, 290, 340, 400, 460,415);
	$cols2 = array( 300, 420, 400,415);
	// $headers in doctext.inc
	$aligns = array('left','left','right','right','right','right','right','right','right','right','right');
	$aligns2 = array('left','left','right','right');

	$params = array('comments' => $comments);

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
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}	
			$rep->SetHeaderType('Header1173');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns, $cols2, null,$aligns2);
			

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESTAX, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;
            $total_price =0;
            $total_including_tax=0;
            $total_amount=0;
            $DisplayPq =0;
              $s_no =0;
            $amount_including_tax = 0;
			while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($sign*$myrow2["quantity"], 0);
				$DisplayPq =  ($myrow2["unit_price"] * $myrow2["quantity"] );
	    		$DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				//$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
				$oldrow = $rep->row;
				// $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{
				    $s_no ++;
					$rep->TextCol(0, 1,	$s_no, -2);
						$rep->TextCol(2, 3,	$DisplayQty, -2);
					$rep->TextCol(3, 4,	$myrow2['units'], -2);
					$rep->AmountCol(4, 5,	$myrow2['unit_price'], $dec);
					$exc_value = $myrow2["quantity"] * $myrow2['unit_price'];
					$rep->AmountCol(5, 6,	$exc_value, -2);
					$amt_1 = get_tax_rate($myrow2['debtor_trans_no'], 2);

                    $amt_2 = get_tax_rate($myrow2['debtor_trans_no'], 1);
                    $sales_tax1 = ($amt_1['rate']/100) * $DisplayPq;
                    $sales_tax2 = ($amt_2['rate']/100) * $DisplayPq;
                    if($amt_1['rate'])
                        $Tax1 = $amt_1['rate']."%";
                    else
                        $Tax1 = "-     ";
                    if($amt_2['rate'])
                        $Tax2 = $amt_2['rate']."%";
                    else
                        $Tax2 = "-     ";
                    if($amt_3['rate'])
                        $Tax3 = $amt_3['rate']."%";
                    else
                        $Tax3 = "-     ";
					$rep->TextCol(7, 8,	$Tax1." | ".$sales_tax1, -2);
					$rep->TextCol(6, 7,	$Tax2." | ".$sales_tax2, -2);
				

                    $tot_amt_salestax = $amt_salestax1 + $amt_salestax2 + $amt_salestax3;
					$including_sales_tax = $sales_tax1 + $sales_tax2 + $sales_tax3 + $DisplayPq;
					$rep->TextCol(10, 11,	price_format($including_sales_tax), -2);
	$amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);
	               // $rep->TextCol(6, 7,	"    ".$amt_1['rate']."   | ".$tot_amt_salestax, -2);
                    $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
					//$total_price += $myrow2["unit_price"];
					$total_value_excl_tax += $DisplayPq;
					$total_amount += $amount_including_tax;
					$total_including_tax += $including_sales_tax;
					$tot_sales_tax1 += $sales_tax1;
					$tot_sales_tax2 += $sales_tax2;
					$tot_sales_tax3 += $sales_tax3;

				}
				//$rep->row = $newrow;
				$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}

			
   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

		//$rep->NewLine(14.5);
		$rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;
	//	$rep->NewLine();

		
		$rep->Font('bold');

// 		if($tot_sales_tax1)
// 			$rep->AmountCol(7, 8,($tot_sales_tax1), 2);
// 		else
//             $rep->TextCol(7, 8,"", 2);
//         if($tot_sales_tax2)
// 			$rep->AmountCol(8, 9,($tot_sales_tax2), 2);
//         else
//             $rep->TextCol(8, 9,"", 2);
//         if($tot_sales_tax3)
// 			$rep->AmountCol(9, 10,($tot_sales_tax3), 2);
//         else
//             $rep->TextCol(9, 10,"", 2);
// 			$rep->TextCol(10, 11,price_format($total_including_tax), -2);
		$rep->Font('');
// 		$rep->NewLine(-20);
//			$rep->TextCol(6, 7,	$total_price, -2);
//			$rep->NewLine();
		$rep->NewLine(-19);
$rep->TextCol(3, 6, _("Shipping"), -2);
$rep->TextCol(8, 9,    $DisplayFreight, -2);
     $rep->NewLine(+19);

     		if($myrow['discount1'] != 0) {

                    $discount_value =$myrow["disc1"]/100;
                    $rep->MultiCell(410, 30, "".price_format(($myrow["discount1"])) ,0, 'R', 0, 2, 150,540, true);
                    $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 350,540, true);
		}
	     	if($myrow['discount2'] != 0) {
                    $discount_value =$myrow["disc2"]/100;
                    $rep->MultiCell(410, 30, "".price_format($myrow["discount2"]) ,0, 'R', 0, 2, 150,540, true);
                    $rep->MultiCell(410, 30, "Discount" ,0, 'L', 0, 2, 350,540, true);
                    $tot_amt =$tot_net - $myrow['discount2'];
                    $rep->NewLine();
		}
			
			$rep->NewLine();
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
    			
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
    				else
						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
					//$rep->TextCol(3, 6, $tax_type_name, -2);
					//$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}
    		$rep->NewLine(-19);
	$rep->TextCol(5, 6, "TOTAL", -2);
			//$rep->TextCol(2, 3,price_format($total_price), -2);
			$rep->TextCol(8, 9, price_format($total_including_tax - $myrow["discount1"] - $myrow["discount2"] + $myrow["ov_freight"]));
    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
			$rep->Font('bold');

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
			$memo = get_comments_string(ST_SALESINVOICE, $i);
// 			if ($memo != "")
			{
				//$rep->NewLine();
				//$rep->TextColLines(1, 5, $memo, -2);
				$rep->MultiCell(300,40,$memo,0,'L', 0, 2,40,580,true);
				
			}
			$rep->MultiCell(185,12,"".$sales_order['f_text10'],0,'L', 0, 2,380,194,true);


			
	}
	if ($email == 0)
		$rep->End();
}

?>