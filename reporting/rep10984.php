<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// draft version!
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

$packing_slip = 0;
//----------------------------------------------------------------------------------------------------

print_deliveries();
$print_as_quote = 0;
//----------------------------------------------------------------------------------------------------
function get_payment_terms12($id)
{
	$sql = "SELECT `terms` FROM ".TB_PREF."payment_terms WHERE ".TB_PREF."payment_terms.terms_indicator=".db_escape($id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_item_location_name10984($id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo1 WHERE combo_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_branch_name_code($branch_id)
{
	$sql = "SELECT br_name FROM ".TB_PREF."cust_branch 
		WHERE debtor_no = ".db_escape($branch_id);

	$result = db_query($sql,"could not retreive name for branch" . $branch_id);

	$myrow = db_fetch_row($result);
	return $myrow[0];
}
///get urdu name
/*function get_item_urdu_name_code($code)
{
	$sql = "SELECT `urdu` FROM `0_stock_master` WHERE `stock_id`='$code'";

	$result = db_query($sql,"could not retreive name for branch" . $branch_id);

	$myrow = db_fetch_row($result);
	return $myrow[0];
}*/

function get_customer_info($debtor_no)
{
	$sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
			SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND 
			`action`='general' AND entity_id IN (
			SELECT branch_code FROM `0_cust_branch` WHERE debtor_no='$debtor_no'))";

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}
function get_order_no($datefrom, $customer_id, $branch_id)
{
	$from = date2sql($datefrom);
//	$to = date2sql($dateto);
	$sql = "SELECT orders.order_no
			FROM 0_sales_orders orders
			WHERE trans_type = '30' 
			AND orders.delivery_date = '$from'";

	if($customer_id != 0)
		$sql .= " AND orders.debtor_no = '$customer_id'";

	if($branch_id != 0)
		$sql .= " AND orders.branch_code = '$branch_id'";
	
		$sql .= "ORDER BY orders.order_no";
// AND orders.ord_date  <= '$to'

	return db_query($sql,"No transactions were returned");
}
function get_sales_order_details_customize($order_no, $trans_type) {
	$sql = "SELECT id, stk_code, unit_price,
				line.description,
				line.quantity,
				line.units_id,
				line.con_factor,
				line.text1,
				line.text2,
				line.text3,
				line.text4,
				line.text5,
				line.text6,
				line.amount1,
				line.amount2,
				line.amount3,
				line.amount4,
				line.amount5,
				line.amount6,
				line.date1,
				line.date2,
				line.date3,
				line.combo1,
				line.combo2,
				line.combo3,
				line.combo4,
				line.combo5,
				line.combo6,
				line.batch,
				line.item_location,
				discount_percent,
				qty_sent as qty_done,
				item.units,
				item.mb_flag,
			item.combo1 as Combo1,
				item.material_cost
			FROM ".TB_PREF."sales_order_details line,"
		.TB_PREF."stock_master item,"
		.TB_PREF."combo1 combo
			WHERE line.stk_code = item.stock_id
				AND combo.combo_code = item.combo1
				AND order_no =".db_escape($order_no)
		." AND trans_type = ".db_escape($trans_type) . " ORDER BY combo.order_by";

	return db_query($sql, "Retreive order Line Items");
}
function print_deliveries()
{
	global $path_to_root, $packing_slip, $print_as_quote, $suppress_tax_rates, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from_date = $_POST['PARAM_0'];
//	$to_date = $_POST['PARAM_1'];
	$customer_id = $_POST['PARAM_1'];
	$branch_id = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$packing_slip = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	//if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	//$fno = explode("-", $from);
	//$tno = explode("-", $to);
	//$from = min($fno[0], $tno[0]);
	//$to = max($fno[0], $tno[0]);

	$cols = array(4, 30, 100,185, 300, 330, 430);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'center', 'center', 'left', 'center', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
	{
		if ($packing_slip == 0)
			$rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 9, $orientation);
		else
			$rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 9, $orientation);
	}
    if ($orientation == 'L')
    	recalculate_cols($cols);

	$get = get_order_no($from_date, $customer_id, $branch_id);

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
		$rep->SetHeaderType('Header10984');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, $headers, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
		$rep->NewPage();

//		$result = get_sales_order_details_for_report($mydata['order_no'], ST_SALESORDER);
		$SubTotal = 0;
		$items = $prices = array();
		$sr_no = 0;
        $catt = '';

			/*$rep->MultiCell(250, 10,"Customer Name:   ".$myrow['name'] , 0, 'L', 0, 2, 40,100, true);
			$rep->MultiCell(250, 10,"Branch Name:   ".get_branch_name($myrow['branch_code']) , 0, 'L', 0, 2, 40,115, true);
			$rep->MultiCell(250, 10,"Order person name:   ".$myrow['customer_ref'] , 0, 'L', 0, 2, 40,130, true);
			$rep->MultiCell(250, 10,"Order Currency:   ".$myrow['curr_code'], 0, 'L', 0, 2, 40,145, true);
			$rep->MultiCell(250, 10,"Payment Terms:   ".get_payment_terms12($myrow['payment_terms']), 0, 'L', 0, 2, 40,160, true);
			//$rep->MultiCell(250, 10,"Delivery Address:   ".$myrow['location_name'] , //0, 'L', 0, 2, 40,175, true);
			$rep->MultiCell(250, 10,"Sales Order No:   ".$myrow['order_no'] , 0, 'L', 0, 2, 40, 190, true);
			$rep->MultiCell(250, 10,"Telephone Number:   ".$myrow['contact_phone'] , 0, 'L', 0, 2, 40,205, true);
			$rep->MultiCell(250, 10,"E-Mail Address:   ".$myrow['contact_email'] , 0, 'L', 0, 2, 40,220, true);
			$rep->MultiCell(250, 10,"Comments:   ".$myrow['comments'] , 0, 'L', 0, 2, 40,235, true);
			
			$rep->MultiCell(180, 10,"Sale Order Ref.:   ".$myrow['reference'] , 0, 'L', 0, 2, 405,100, true);*/
            $rep->MultiCell(180, 10,"Deliver To:   ".$myrow['deliver_to'] , 0, 'L', 0, 2, 405,115, true);
			$rep->MultiCell(180, 10,"Required Delivery Date:   ".sql2date($myrow['delivery_date']) , 0, 'L', 0, 2, 405,130, true);
   			$result = get_sales_order_details_customize($mydata['order_no'], ST_SALESORDER);
			$SubTotal = 0;
			$s_no=1;
			while ($myrow2=db_fetch($result))
			{
				if($myrow2["quantity"] == 0 || $myrow2['stk_code'] == 'OB') continue;
				$sr_no+=1;
				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$prices[] = $Net;
				$items[] = $myrow2['stk_code'];
				$SubTotal += $Net;
//				$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
				$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
				$DisplayNet = number_format2($Net,$dec);
				if ($myrow2["discount_percent"]==0)
					$DisplayDiscount ="";
				else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";

                if ($catt != $myrow2['Combo1'])
                {
//                    if ($catt != '')
                    {
						$rep->Font('bold');
						$rep->TextCol(1, 2,	get_item_location_name10984($myrow2['Combo1']), -2);
						$rep->NewLine();
						$rep->Font('');
                    }
//                    $rep->Font('bold');
//                    $rep->TextCol(1, 2,	get_item_location_name10984($myrow2['category_id']), -2);
//                    $rep->NewLine();
//                    $rep->Font('');
                }
//				$rep->Font('bold');
//				$rep->TextCol(1, 2,	get_item_location_name($myrow2['item_location']), -2);
//				$rep->NewLine();
//				$rep->Font('');
				$rep->TextCol(0, 1,	$s_no, -2);
				$oldrow = $rep->row;
				$rep->TextColLines(1, 2, $myrow2['stk_code'], -2);

				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{

					$rep->TextCol(2, 3,	$myrow2['description'], -2);
				//$rep->NewLine(-0.2);


				//	$rep->TextCol(3, 4,	 get_item_urdu_name_code($myrow2['stk_code']), -2);
				//$rep->NewLine(+0.2);

					$rep->TextCol(4, 5,	$myrow2['units'], -2);
					if ($packing_slip == 0)
					{
						$rep->TextCol(5, 6,	$DisplayQty, -2);
						//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
					//	$rep->TextCol(6, 7,	$DisplayNet, -2);
					}

				}
				$rep->NewLine(0.2);
				$rep->TextCol(0, 8,	"___________________________________________________________________________________________________________________________________________", -2);

				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (9 * $rep->lineHeight))
					$rep->NewPage();
				$s_no++;
                $catt = $myrow2['Combo1'];
			}

			if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($myrow["ov_freight"],$dec);

    		$rep->row = $rep->bottomMargin + (9 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY2;
			if ($packing_slip == 0)
			{
			//	$rep->TextCol(3, 6, _("Sub-total"), -2);
			//	$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
			//	$rep->TextCol(3, 6, _("Shipping"), -2);
			//	$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
				$tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
		  $myrow['tax_group_id'], $myrow['tax_included'],  null);
		$first = true;

				$rep->Font();
			}
		$rep->NewLine(+7);
		$rep->Font('bold');

		$rep->MultiCell(175, 18, "Deliver By" , 0, 'C', 0, 2, 30,765, true);
		$rep->MultiCell(175, 18, "________________________" , 0, 'C', 0, 2, 30,750, true);
		//
		$rep->MultiCell(175, 18, "Delivery Time" , 0, 'C', 0, 2, 200,765, true);
		$rep->MultiCell(175, 18, "________________________" , 0, 'C', 0, 2, 200,750, true);
		//
		$rep->MultiCell(175, 18, "Customer Signature" , 0, 'C', 0, 2, 370,765, true);

		$rep->MultiCell(175, 18, "________________________" , 0, 'C', 0, 2, 370,750, true);
		$rep->Font('');


		$rep->NewLine(-7);
		$rep->NewLine(-7);
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>