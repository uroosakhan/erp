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
function get_card_num($order_no)
{
	$sql = "SELECT card_num,order_time,cash_recieved,deliver_to  from ".TB_PREF."sales_orders where `order_no`=".$order_no;
	$db = db_query($sql);
	$ft = db_fetch($db);
	return $ft;
}
function get_user_login($order_no, $trans_type)
{
	$sql= "SELECT user FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($trans_type)." AND trans_no = ".db_escape($order_no);
	$result = db_query($sql, "could not process Requisition to Purchase Order");
	return db_fetch($result);

}
function get_name($debtor_no)
{
	$sql = "SELECT `name` from ".TB_PREF."debtors_master where `debtor_no`=".$debtor_no;
	$db = db_query($sql);
	$ft = db_fetch($db);
	return $ft[0];
}
function get_phone1($customer_id)
{
	$sql = "SELECT `phone` FROM ".TB_PREF."crm_persons WHERE ref=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer phone");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_user_name_($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get user name");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_server_name_($salesman_id)
{
	$sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($salesman_id);

	$result = db_query($sql, "could not get salesman name");

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

	if (!$from || !$to) return;

	$dec = user_price_dec();
	$dec = 0;

	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array(8, 15, 90, 125, 160, 245, 270);
	// $headers in doctext.inc
	$aligns = array('left','center','center', 'left','left');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		$rep = new FrontReport(_('ESTIMATE'), "InvoiceBulk", 'POS', '8');
		$rep->SetHeaderType('Header107');
		$rep->currency = $cur;
		//$rep->Font();

//		$rep->fontSize -= 2;
		$rep->Info($params, $cols, $headers, $aligns);
//		$rep->fontSize += 2;
	}
	for ($i = $from; $i <= $to; $i++)
	{
		if (!exists_customer_trans(ST_SALESINVOICE, $i))
			continue;
		$sign = 1;
		$myrow = get_customer_trans_invoice($i, ST_SALESINVOICE);
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];

		$branch = get_branch($myrow["branch_code"]);
		$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize());
//			$rep->SetHeaderType('Header1071');
			$rep->currency = $cur;
			//$rep->Font();
			$rep->title = _('');
			$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			$rep->Info($params, $cols, null, $aligns);
		}
		else
			$rep->title = _('');

		$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], false);
		$baccount['payment_service'] = $pay_service;
		$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
		$rep->NewPage();
		$result = get_customer_trans_details_invoice(ST_SALESINVOICE, $i);
		$SubTotal = 0;
		$total_p;
		$user =get_user_login($i, ST_SALESORDER);
		while ($myrow2=db_fetch($result))
		{
			if ($myrow2["quantity"] == 0)
				continue;
$rep->NewLine(0.2);
			$rep->TextCol(0, 6,	_("........................................................................................................................."), -2);
			$rep->NewLine(-0.2);
			$Net = round2($sign * ( ($myrow2["unit_price"] * $myrow2["quantity"]) * (1-$myrow2["discount_percent"])),
				user_price_dec());
			$SubTotal += $Net;

			$DisplayPrice = number_format2($myrow2["unit_price"],0);

			$DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
			$DisplayNet = number_format2($Net,0);

			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100, 0) . "" . "%";

			$rep->NewLine();
			//$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
			$oldrow = $rep->row;

			//$rep->fontSize += 1;
			/*if(!is_numeric($myrow2['stk_id'])){
				$rep->TextColLines(0, 2, $myrow2['StockDescription']."(".$myrow2['stk_id'].")", -2);
			}else{
				$rep->TextColLines(0, 2, $myrow2['StockDescription'], -2);
			}*/
$rep->TextColLines(0, 2, $myrow2['StockDescription'], -2);
			//$rep->fontSize -= 1;
			$newrow = $rep->row;
			$rep->row = $oldrow;

			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$rep->TextCol(2, 3,	$myrow2["quantity"], -2);
				//	$rep->TextCol(3, 4,	$myrow2['units'], -2);
				$rep->TextCol(3, 4,	$DisplayPrice, -2);
				//$rep->TextCol(4, 5,	$DisplayDiscount, -2);
				$rep->TextCol(4, 5,	$DisplayNet, -2);

			}
				$total_p += $DisplayNet;

			
			$rep->row = $newrow;
			//$rep->NewLine(1);
			//if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
			//	$rep->NewPage();
		}

		$memo = get_comments_string(ST_SALESINVOICE, $i);
		if ($memo != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $memo, -2);
		}
		//$rep->fontSize -= 1;


		$DisplaySubTot = number_format2($SubTotal,0);
		$DisplayFreight = number_format2($sign*$myrow["ov_freight"],0);

		//$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESINVOICE;
		//	$rep->NewLine(-25);
		//	$rep->TextCol(0, 2, _("Sub-total"), -2);
		//	$rep->TextCol(2, 4,	$DisplaySubTot, -2);
		//	$rep->NewLine();
		$tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
		$first = true;
	//	while ($tax_item = db_fetch($tax_items))
	//	{
		/*	if ($tax_item['amount'] == 0)
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
						$rep->TextCol(0, 1, _("Total Tax Excluded"), -2);
						$rep->TextCol(1, 2,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
					$rep->TextCol(0, 1, $tax_type_name, -2);
					$rep->TextCol(1, 2,	$DisplayTax, -2);
					$first = false;
				}
				else
					$rep->TextCol(1, 5, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$rep->TextCol(0, 1, $tax_type_name, -2);
				$rep->TextCol(1, 2,	$DisplayTax, -2);
			}*/
			//$rep->NewLine();
	//	}

		//card num
		$myraw = get_card_num($myrow["order_"]);
//		$rep->MultiCell(100, 10, $myrow['order_no'], 0, 'R', 0, 2, 110, 150);

//		//while($myraw = db_fetch($time_and_card_no))  {
//		//$rep->MultiCell(252, 148, get_name($myrow['debtor_no']), 0, 'L', 0, 2, 145,200, true); // 3
//		$rep->MultiCell(252, 148, (" ").get_phone1($branch['br_name']) , 0, 'L', 0, 2, 83,300, true);
////		$rep->MultiCell(100, 10,get_user_name_($user['user']), 0, 'R', 0, 2, 110,120, true);
//		$rep->MultiCell(100, 10, get_server_name_($myrow['w_id']), 0, 'R', 0, 2, 110, 190);
//			$rep->MultiCell(182.5, 15, $myraw['card_num'], 0, 'L', 0, 2, 70, 136);
//			$rep->MultiCell(182.5, 15, $myraw['order_time'], 0, 'L', 0, 2, 160, 134);
////		$rep->MultiCell(100, 10, "dskghskj".$myrow2['reference'], 0, 'L', 0, 2, 11, 145);

//		$rep->MultiCell(100, 10, get_name($myrow['debtor_no']), 0, 'R', 0, 2, 110,200, true); // 3
////		$rep->MultiCell(100, 10,get_user_name_($user['user']), 0, 'R', 0, 2, 110,120, true);
//		$rep->MultiCell(100, 10, $myrow['card_num'], 0, 'R', 0, 2, 110, 160);
////		$rep->MultiCell(100, 10, $myrow['reference'], 0, 'L', 0, 2, 8, 135);
		$rep->MultiCell(100, 10, "".$myrow['trans_no'], 0, 'L', 0, 2, 8, 95);
		$a = get_sales_order_header($myrow['order_'],30);
		$rep->MultiCell(100, 10, "".get_server_name_($a['w_id']), 0, 'R', 0, 2, 80, 135);
		//$rep->MultiCell(100, 10, $myrow['order_'], 0, 'R', 0, 2, 110, 175);
//		$rep->MultiCell(100, 10, $myrow['ord_date'], 0, 'L', 0, 2, 8, 175);

		//}
		$rep->NewLine();
$rep->MultiCell(100, 148,$myrow['order_'], 0, 'L', 0, 2, 5,105, true);
		$rep->TextCol(0, 2, _("SUB - TOTAL "), - 2);
		$rep->TextCol(4, 5, $DisplaySubTot , $dec);

		$rep->NewLine();
		$rep->TextCol(0, 2, _("Total Discount(%)"), - 2);
		$rep->AmountCol(4, 5, $myrow["total_discount"].'%', -2);
$rep->NewLine();
		$rep->TextCol(0, 2, _("Total Discount Amount"), - 2);
		$rep->AmountCol(4, 5, $DisplaySubTot*($myrow["total_discount"]/100), -2);

		$rep->NewLine();
		$DisplayTotal = number_format2($sign*($myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]),0);
		$DisplayTotal_back =$sign*($myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"]);


		$rep->Font('bold');
		$rep->TextCol(0, 2, _("TOTAL RECEIPT"), - 2);
		$rep->TextCol(4, 5, $DisplayTotal, -2);
		$rep->Font();

		if($myraw['deliver_to'] == 'Take Away' || $myraw['deliver_to'] == 'Delivery' ) {
			$rep->NewLine(1);
		}else
		{
			$rep->NewLine(2);
			$rep->TextCol(0, 2, _("Cash Received"), -2);
			$rep->AmountCol(4, 5, $myraw['cash_recieved'], $dec);
			if($myraw['cash_recieved']!=0)
			{
				$remainning_cash = -($DisplayTotal_back - $myraw['cash_recieved']);
			}
			else{
				$remainning_cash = 0;
			}
			
			$rep->NewLine();
			$rep->TextCol(0, 2, _("Remaining Cash"), -2);
			$rep->AmountCol(4, 5, $remainning_cash, $dec);
		}

		$words = price_in_words($myrow['Total'], ST_SALESINVOICE);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 2, $myrow['curr_code'] . ": " . $words, - 2);
		}

//		$rep->NewLine();
//		$rep->MultiCell(100, 10, $myrow['card_num'], 0, 'R', 0, 2, 110, 160);
//		$rep->MultiCell(100, 10, $myrow['reference'], 0, 'L', 0, 2, 8, 135);
//		$rep->MultiCell(100, 10, $myrow['order_no'], 0, 'R', 0, 2, 110, 150);
////		$rep->MultiCell(100, 10, "User", 0, 'L', 0, 2, 8, 120);
//		$rep->MultiCell(100, 10, "Order #.", 0, 'L', 0, 2, 8, 150);
//		$rep->MultiCell(100, 10, "Token #.", 0, 'L', 0, 2, 8, 160);
		$rep->MultiCell(100, 10, get_server_name_($myrow['w_id']), 0, 'R', 0, 2, 110, 190);
//		$rep->MultiCell(100, 10, $myrow['order_time'], 0, 'R', 0, 2, 110, 173);
//		$rep->MultiCell(100, 10, $myrow['ord_date'], 0, 'L', 0, 2, 8, 173);
//		$rep->MultiCell(252, 148, "Table:", 0, 'L', 0, 2, 8,200, true); // 3
//		$rep->MultiCell(252, 148, "Server", 0, 'L', 0, 2, 8,187, true); // 3
//		//$rep->NewLine(3);

		/*$rep->TextCol(0, 6, _("- No exchange without receipt."), -2);
		$rep->NewLine();
		$rep->TextCol(0, 6, _("- Imported items have no warranty."), -2);
		$rep->NewLine();
		$rep->TextCol(0, 6, _("- Exchange within 7 days."), -2);*/
		$rep->NewLine(2);
		$rep->TextCol(0, 6, _("--------------------- Powered By www.hisaab.pk ---------------------"), -2);



		/*
        // Account Status
        //Debit
        $sql = "
        SELECT
        SUM(ov_amount+ov_freight) AS OutStanding
        FROM ".TB_PREF."debtor_trans
        WHERE debtor_no = '$myrow[debtor_no]'
        AND type = 10
        ";
         $result = db_query($sql,"No transactions were returned");
           $bal2 = db_fetch($result);

        //Credit
        $sql = "
        SELECT
        SUM(ov_amount) AS Payments
        FROM ".TB_PREF."debtor_trans
        WHERE debtor_no = '$myrow[debtor_no]'
        AND type IN (12 , 11 , 2)
        ";

        //AND type IN (11,12,2)
         $result = db_query($sql,"No transactions were returned");
           $bal3 = db_fetch($result);

        $TotalCredit = round2($bal3['Payments'], $dec); //Total credit side balance

        $TotalDebit = round2($bal2['OutStanding'], $dec); // Total debit side balance

        $CurrentAmount = number_format2($SubTotal+$myrow["ov_freight"]);

        $PreviousBalance = number_format2($TotalDebit-$TotalCredit-($SubTotal+$myrow["ov_freight"]));

        $TotalBalance2 = number_format2($TotalDebit-$TotalCredit);

                    $rep->NewLine(5);
                    $rep->TextCol(0, 5, _("Previous Balance"), -2);
                    if ($PreviousBalance > 0)
                    $rep->TextCol(5, 6, $PreviousBalance , -2); //previous balance
                    else
                    $rep->TextCol(5, 6, _("") , -2);
                    $rep->NewLine();
                    $rep->TextCol(0, 5, _("Current Amount"), -2);
                    $rep->TextCol(5, 6, $CurrentAmount, -2); // Current Amount

                    $rep->NewLine();
                    $rep->TextCol(0, 5, _("Total Balance"), -2);
                    $rep->TextCol(5, 6, $TotalBalance2,  -2); // TotalBalance
                    $rep->NewLine();


        */
		//	$rep->TextCol(0, 6, _("                       Powered By www.hisaab.pk"), - 2);
		$rep->Font();
		if ($email == 1)
		{
			$rep->End($email);
		}
		//$rep->NewLine(25);
	}
	if ($email == 0)
		$rep->End();
}

?>