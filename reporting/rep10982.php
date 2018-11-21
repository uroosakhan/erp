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
function get_order_no($datefrom, $dateto, $customer_id, $branch_id)
{
	$from = date2sql($datefrom);
	$to = date2sql($dateto);
	$sql = " SELECT orders.order_no
			FROM 0_sales_orders orders
			WHERE trans_type = 30 
			AND orders.delivery_date = '$from' 
			 ";
	if($customer_id != 0)
		$sql .= "AND orders.debtor_no = '$customer_id'";
	if($branch_id != 0)
		$sql .= "AND orders.branch_code = '$branch_id'";
	
		$sql .= "ORDER BY orders.order_no";
//AND orders.ord_date  <= '$to'
	return db_query($sql,"No transactions were returned");
}
function print_sales_orders()
{
	global $path_to_root, $print_as_quote, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from_date = $_POST['PARAM_0'];
//	$to_date = $_POST['PARAM_1'];
	$customer_id = $_POST['PARAM_1'];
	$branch_id = $_POST['PARAM_2'];
	$currency = $_POST['PARAM_3'];
	$email = $_POST['PARAM_4'];
	$print_as_quote = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];

	//if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();


	$cols = array(4, 40, 100,200, 250, 365, 430, 440);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left',  'left', 'right', 'right', 'right');

	$params = array('comments' => $comments);

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


	$get = get_order_no($from_date, $to_date, $customer_id, $branch_id);

	//for ($i = $from; $i <= $to; $i++)
	while($mydata = db_fetch($get))
	{

		$myrow = get_sales_order_header($mydata['order_no'], ST_SALESORDER);
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			if ($print_as_quote == 1)
			{
				$rep->title = _('QUOTE');
				$rep->filename = "Quote" . $mydata['order_no'] . ".pdf";
			}
			else
			{
				$rep->title = _("SALES ORDER");
				$rep->filename = "SalesOrder" . $mydata['order_no'] . ".pdf";
			}
		}
		else
			$rep->title = ($print_as_quote==1 ? _("QUOTE") : _("SALES ORDER"));
		$rep->SetHeaderType('Header10981');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, $headers, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
		$rep->NewPage();

		$result = get_sales_order_details_for_report($mydata['order_no'], ST_SALESORDER);
		$SubTotal = 0;
		$items = $prices = array();
		$sr_no = 0;
		$rep->MultiCell(180, 10,"Print Person Name:   ".$_SESSION["wa_current_user"]->name , 0, 'L', 0, 2, 405,90, true);

		$rep->MultiCell(180, 10,"Print Out:   ". date("d-m-Y")   , 0, 'L', 0, 2, 40,90, true);
		$rep->MultiCell(180, 10,"Period :   ". sql2date($myrow['ord_date']) , 0, 'L', 0, 2, 40,110, true);
		$rep->MultiCell(300, 10,"Customer Name:   ". get_customer_name($myrow['debtor_no']) , 0, 'L', 0, 2, 40,125, true);
		$rep->MultiCell(180, 10,"Branch Name:   ". get_branch_name($myrow['branch_code']) , 0, 'L', 0, 2, 40,140, true);
		while ($myrow2 = db_fetch($result))
		{
			if($myrow2["quantity"] == 0 || $myrow2['stk_code'] == 'OB') continue;
			$sr_no+=1;
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
			$rep->Font('bold');
			$rep->TextCol(1, 2,	get_item_location_name($myrow2['item_location']), -2);
			
			

$rep->NewLine();
			$rep->Font('');
			$rep->TextCol(0, 1,	$sr_no, -2);

			$rep->TextCol(1, 2,	$myrow2['stk_code'], -2);

$rep->TextCol(4, 5, $myrow2['urdu'], -2);

			$oldrow = $rep->row;
			$rep->TextColLines(2, 4, $myrow2['description'], -2);


			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
			{
				$rep->TextCol(5, 6,	$myrow2['units'], -2);
				$rep->TextCol(6, 7,	$DisplayQty, -2);
			}
			$rep->NewLine(0.2);
			$rep->TextCol(0, 8,	"___________________________________________________________________________________________________________________________________________", -2);

			$rep->row = $newrow;
			//$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (6 * $rep->lineHeight))
				$rep->NewPage();

		}
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		$DisplaySubTot = number_format2($SubTotal,$dec);
		$DisplayFreight = number_format2($myrow["freight_cost"],$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESORDER;

	//	$rep->TextCol(3, 6, _("Sub-total"), -2);
	//	$rep->TextCol(6, 7,	$DisplaySubTot, -2);
		$rep->NewLine();
		//$rep->TextCol(3, 6, _("Shipping"), -2);
		//$rep->TextCol(6, 7,	$DisplayFreight, -2);
		$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		if ($myrow['tax_included'] == 0) {
		//	$rep->TextCol(3, 6, _("TOTAL ORDER EX GST"), - 2);
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
			$DisplayTax = number_format2($tax_item['Value'], $dec);

			$tax_type_name = $tax_item['tax_type_name'];

			if ($myrow['tax_included'])
			{
				if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
				{
					if ($first)
					{
						//$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
						//$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
					//$rep->TextCol(3, 6, $tax_type_name, -2);
					//$rep->TextCol(6, 7,	$DisplayTax, -2);
					$first = false;
				}
				//else
					//$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount"). ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
			//	$rep->TextCol(3, 6, $tax_type_name, -2);
			//	$rep->TextCol(6, 7,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		$rep->Font('bold');
		//$rep->TextCol(3, 6, _("TOTAL ORDER GST INCL."), - 2);
		//$rep->TextCol(6, 7,	$DisplayTotal, -2);
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
	if ($email == 0)
		$rep->End();
}

?>