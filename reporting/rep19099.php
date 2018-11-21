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

print_sales_orders();

$print_as_quote = 0;


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
function get_user_login($order_no, $trans_type)
{
    $sql= "SELECT user FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($trans_type)." AND trans_no = ".db_escape($order_no);
    $result = db_query($sql, "could not process Requisition to Purchase Order");
    return db_fetch($result);

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
function print_sales_orders()
{
	global $path_to_root, $print_as_quote, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

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

	//$cols = array(4, 60, 225, 300, 325, 385, 450, 515);
	$cols = array(8, 40,  110, 146);


	// $headers in doctext.inc
//	$aligns = array('left',	'left',	'right', 'left', 'right', 'right', 'right');
	$aligns = array('left',	'left', 'left','left');


	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		//if ($print_as_quote == 0)
		$rep = new FrontReport(_('ESTIMATE'), "SalesOrderBulk", 'SPOS', '8');
		//	$rep = new FrontReport(_("SALES ORDER"), "SPOS", user_pagesize(), 9, $orientation);
		//else
		//	$rep = new FrontReport(_("QUOTE"), "SPOS", user_pagesize(), 9, $orientation);
	}
	if ($orientation == 'L')
		recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_sales_order_header($i, ST_SALESORDER);



		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
		{
			//$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep = new FrontReport(_('ESTIMATE'), "SalesOrderBulk", 'SPOS', '8');
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
		$rep->SetHeaderType('Header19099');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, $headers, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
		$rep->NewPage();
        $user =get_user_login($i, ST_SALESORDER);
		$result = get_sales_order_details($i, ST_SALESORDER);
		$SubTotal = 0;
		$items = $prices = array();
		$rep->SetFontSize(8);
		$rep->MultiCell(100, 10, get_name($myrow['debtor_no']), 0, 'R', 0, 2, 80,190, true); // 3
		$rep->MultiCell(100, 10,get_user_name_($user['user']), 0, 'R', 0, 2, 80,120, true);
        $rep->MultiCell(100, 10, $myrow['card_num'], 0, 'R', 0, 2, 80, 163);
        $rep->MultiCell(100, 10, $myrow['reference'], 0, 'R', 0, 2, 80, 135);
        $rep->MultiCell(100, 10, $myrow['order_no'], 0, 'R', 0, 2, 80, 150);
//        $rep->MultiCell(100, 10, get_server_name_($myrow['w_id']), 0, 'R', 0, 2, 80, 190);
        $rep->MultiCell(100, 10, $myrow['order_time'], 0, 'R', 0, 2, 80, 175);
        $rep->MultiCell(100, 10, sql2date($myrow['ord_date']), 0, 'L', 0, 2, 8, 175);
//		$rep->MultiCell(252, 148, (" ").get_phone1($branch['br_name']) , 0, 'L', 0, 2, 83,88, true);

		$rep->Font();
		$rep->NewLine(7.2);
		while ($myrow2=db_fetch($result))
		{
			$rep->SetFontSize(7);
			$Net = round2(( $myrow2["unit_price"] * $myrow2["quantity"]),
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
//			$rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$rep->TextCol(0, 1,	$DisplayQty, -2);
				//	$rep->TextCol(3, 4,	$myrow2['units'], -2);
				$rep->TextCol(2, 3,	$DisplayPrice, -2);
				//	$rep->TextCol(5, 6,	$DisplayDiscount, -2);
				$rep->TextCol(3, 4,	$DisplayNet, -2);
			}

			$rep->row = $newrow;
			//$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();

			$rep->Font();
		}
		$rep->NewLine(0.2);
		$rep->TextCol(0, 8,	_("........................................................................................................................................................................................................."), -2);
		$rep->NewLine(-0.2);
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		$DisplaySubTot = number_format2($SubTotal,$dec);
		$DisplayFreight = number_format2($myrow["freight_cost"],$dec);
		$rep->NewLine();
		//	$rep->row = $rep->bottomMargin + (50 * $rep->lineHeight);
		$doctype = ST_SALESORDER;

		$rep->TextCol(0,2 , _("Sub-total"), -2);
		$rep->TextCol(3,4,	$DisplaySubTot, -2);
		$rep->NewLine();
	//	$rep->TextCol(0, 2, _("Shipping"), -2);
	//	$rep->TextCol(2, 3,	$DisplayFreight, -2);
	//	$rep->NewLine();

		$rep->TextCol(0, 2, _("Total Discount"), -2);
		$rep->TextCol(3, 4,	$myrow["total_discount_pos"], -2);
		$rep->NewLine();

//		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
//		if ($myrow['tax_included'] == 0) {
//			$rep->TextCol(0,2, _("TOTAL ORDER EX GST"), - 2);
//			$rep->TextCol(3, 4,'45'	, -2);
//			$rep->NewLine();
//		}

		$tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
			$myrow['tax_group_id'], $myrow['tax_included'],  null);
		$first = true;

	//	$myraw2 = get_card_num($myrow["order_"]);
		//while($myraw = db_fetch($time_and_card_no))  {


		foreach($tax_items as $tax_item)
		{
			/*if ($tax_item['Value'] == 0)
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
					$rep->TextCol(2,4, $tax_type_name, -2);
					$rep->TextCol(4, 5,	$DisplayTax, -2);
					$first = false;
				}
				else
					$rep->TextCol(2, 5, _("Included") . " " . $tax_type_name . " " . _("Amount"). ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				$rep->TextCol(1, 4, $tax_type_name, -2);
				$rep->TextCol(4, 5,	$DisplayTax, -2);
			}*/
			//$rep->NewLine();
		}

	//	$rep->NewLine();

		//$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal , $dec);
		$total = ($myrow["freight_cost"] + $SubTotal) * $myrow["total_discount_pos"] / 100;
        $DisplayTotal =  $SubTotal - $total;
		$rep->Font('bold');
		$rep->TextCol(0, 2, _("Grand Total"), - 2);
		$rep->TextCol(3, 4,	number_format($DisplayTotal, 2), -2);
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
		/*if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}*/

		$rep->NewLine();
		$rep->TextCol(1, 6, _(" ------------------- Powered By www.hisaab.pk ---------------"), -2);
		
		$rep->Font();
		if ($email == 1)
		{
			$rep->End($email);
		}
	}
	if ($email == 0)
		$rep->End();


	$sql="UPDATE ".TB_PREF."sales_order_details SET status_so=1 WHERE order_no=".$from;
	db_query($sql,'Could not update This Order');
}

?>