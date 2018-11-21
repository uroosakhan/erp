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
/*
function get_tax ($id)
{
	$sql = "SELECT rate FROM ".TB_PREF."tax_types WHERE id = ".db_escape($id)."
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
}
*/

function get_tax_rate($trans_no, $stock_id)
{
	$sql = "SELECT (unit_tax/unit_price*100) FROM ".TB_PREF."debtor_trans_details
	WHERE debtor_trans_no = ".db_escape($trans_no)."
	AND stock_id= ".db_escape($stock_id)."
	AND debtor_trans_type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch_row($result);
	return $row['0'];
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
function get_debtor_trans_info($trans_no)
{
	$sql = "SELECT * FROM ".TB_PREF."debtor_trans 
	WHERE trans_no = ".db_escape($trans_no)."
	AND type = 10
	
	";
	$result = db_query($sql, "could not retreive default customer currency code");
	$row = db_fetch($result);
	return $row;
}
function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 2";
    $result = db_query($sql, 'error');
    return $result;
}
function get_phoneno_for_suppliers_($customer_id)
{
    $sql = "SELECT * FROM ".TB_PREF."crm_persons WHERE `id` IN (
            SELECT person_id FROM ".TB_PREF."crm_contacts WHERE `type`='customer' AND `action`='general' AND entity_id IN (
            SELECT branch_code FROM ".TB_PREF."cust_branch WHERE debtor_no=".db_escape($customer_id)."))";
    $query = db_query($sql, "Error");
    return db_fetch($query);
}
function customer_phone_no10799($debtor_no)
{
    $sql="SELECT * FROM `0_crm_persons` WHERE `id` IN (
  SELECT person_id FROM `0_crm_contacts` WHERE `type`='customer' 
  AND `action`='general' 
  AND entity_id IN (
  SELECT branch_code FROM `0_cust_branch` WHERE debtor_no='$debtor_no')) ";

    $result = db_query($sql, "Cannot retreive a wo issue");

    return db_fetch($result);
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

    $cols = array(6, 45, 120,  380,  430, 480);
    $cols2 = array(6, 45, 120,  380,  430, 480);
    // $headers in doctext.inc
    $aligns = array('left','left','left','left','right','right');
    $aligns2 = array('left','left','left','left','right','right');

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
			$rep->SetHeaderType('Header10726');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns, $cols2, null,$aligns2);
			

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
			$rep->NewPage();
   			$result = get_customer_trans_details(ST_SALESINVOICE, $i);
			$SubTotal = 0;
            $total_price =0;
            $total_including_tax=0;
            $total_amount=0;
            $DisplayPq =0;
            $amount_including_tax = 0;
            $myrow3 = db_fetch(get_tax_rate_1());
            $a = 1;
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
                $rate = $myrow3['rate'];
                $unit_price=$myrow2["unit_price"] - $myrow2["unit_tax"];
                $val_exc=$unit_price * $myrow2["quantity"];
                $val_inc = $myrow2["unit_price"] * $myrow2["quantity"];
                $amount_salestax=($rate / 100) * $val_exc;
                $amount_salestax2 = ($rate / 100) * $DisplayPq;
                $val_exc_=$amount_salestax + $val_exc;
                $val_inc_ =$amount_salestax2 + $DisplayPq;
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
                $rep->TextCol(0, 1,	$a++ , -2);
//                $rep->TextCol(1, 2,	$sales_order['f_comment1']." ".$sales_order['f_text10'], -2);
                $oldrow = $rep->row;
				$rep->TextColLines(2, 3, $myrow2['description'], -2);
				$newrow = $rep->row;
				$rep->row = $oldrow;
                $get_debtor_trans = get_debtor_trans_info($myrow['trans_no']);
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{

                    if ($get_debtor_trans['tax_included'] == 1) {
//                        $rep->TextCol(2, 3, $unit_price, -2);
//                        $rep->TextCol(3, 4, $val_exc, -2);
//                        $rep->TextCol(4, 5, $rate . "%", -2);
//                        $rep->TextCol(5, 6, ($rate / 100) * $val_exc, $dec);
//                        $rep->AmountCol(6, 7, ($val_exc_), $dec);
                    }
                    else
                    {
//                        $rep->TextCol(2, 3, $myrow2["unit_price"], -2);
//                        $rep->TextCol(3, 4, $DisplayPq, -2);
//                        $rep->TextCol(4, 5, $rate . "%", -2);
//                        $rep->TextCol(5, 6, $amount_salestax2, $dec);
//                        $rep->AmountCol(6, 7, $amount_salestax2 + $DisplayPq, $dec);
                    }
					//$rate_ = get_tax($myrow2['tax_type_id']);
					//$amount_of_sales_taxincluding_sales_tax  = $rate_ / 100;
					//$amount_including_tax=  $DisplayPq * $amount_of_sales_tax  ;

					$amount_including_tax = get_tax_amount($i, $myrow2['stock_id']);

					$rep->TextCol(3, 4,	number_format2($myrow2['quantity'],get_qty_dec($myrow2['item_code'])), -2);
					$rep->TextCol(4, 5,	number_format2($myrow2['unit_price'],get_qty_dec($myrow2['item_code'])), -2);
					$rep->TextCol(5, 6,	number_format2($myrow2['unit_price']*$myrow2['quantity'],get_qty_dec($myrow2['item_code'])), -2);
                    $total_qty +=  ($myrow2["quantity"]);
                    $total_amt += $myrow2['unit_price']*$myrow2['quantity'];
					$amount_excluding_tax = $DisplayPq-$DisplayPrice;



					$including_sales_tax=  $DisplayPq + $amount_including_tax;
				//	$rep->TextCol(6, 7,	price_format($including_sales_tax), -2);


                    $a=$DisplayPq -$amount_including_tax;

					//$total_price += $myrow2["unit_price"];
					$total_value_excl_tax += $val_exc;
					$total_amount += $amount_salestax;
					$total_including_tax += ($val_exc_);

                    $total_value_excl_tax2 += $DisplayPq;
                    $total_amount2 += $amount_salestax2;
                    $total_including_tax2 += ($val_inc_);

				}
				//$rep->row = $newrow;
				$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
					$rep->NewPage();
			}

			$memo = get_comments_string(ST_SALESINVOICE, $i);
			if ($memo != "")
			{
				//$rep->NewLine();
				//$rep->TextColLines(1, 5, $memo, -2);
			}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

		//$rep->NewLine(14.5);
		$rep->row = $rep->bottomMargin + (5 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;
	//	$rep->NewLine();

		$rep->NewLine(-12);
		$rep->Font('bold');
		$rep->TextCol(1, 2, " TOTAL", -2);
//        if ($get_debtor_trans['tax_included'] == 1) {
			//$rep->TextCol(2, 3,price_format($total_price), -2);
			$rep->TextCol(3, 4,number_format2($total_qty,get_qty_dec($myrow2['item_code'])), -2);
//			$rep->TextCol(5, 6,price_format($total_amount), -2);
			$rep->TextCol(5, 6,number_format2($total_amt,get_qty_dec($myrow2['item_code'])), -2);
//        }
//        else
//        {
//            $rep->TextCol(3, 4,price_format($total_value_excl_tax2 ), -2);
//            $rep->TextCol(5, 6,price_format($total_amount2), -2);
//            $rep->TextCol(6, 7,price_format($total_including_tax2), -2);
//        }
		$rep->Font('');
		//$rep->NewLine(-4);
//			$rep->TextCol(6, 7,	$total_price, -2);
//			$rep->NewLine();
//			$rep->TextCol(3, 6, _("Shipping"), -2);
//			$rep->TextCol(6, 7,	$DisplayFreight, -2);
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
//							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
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
        $amount_in_words = _number_to_words($total_amt);
        $rep->Font('bold');
        $rep->MultiCell(525, 30, 'Amount in words:'.$amount_in_words ,0, 'L', 0, 2, 40,610, true);
        $rep->Font();
        $rep->MultiCell(100, 11,$sales_order['f_comment2']." "."Kg",0, 'L', 0, 2, 115,630, true);
        $rep->MultiCell(100, 11, $sales_order['f_comment3']." "."Kg",0, 'L', 0, 2, 115,645, true);
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
	}
	if ($email == 0)
		$rep->End();
}

?>