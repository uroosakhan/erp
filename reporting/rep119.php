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


function get_quotation_no($ref)
{
	$sql = "SELECT reference FROM ".TB_PREF."sales_orders WHERE trans_type=32
	AND order_no=".db_escape($ref)."";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_purch_no($item_code)
{
    $sql = "SELECT reference FROM ".TB_PREF."purch_orders WHERE trans_type=18
	AND order_no=".db_escape($item_code)."";

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_phone_1107($debtor_no)
{
	$sql = "SELECT phone FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';

	$db  = db_query($sql,"item prices could not be retreived");
	$ft = db_fetch_row($db);
	return $ft[0];
}
function get_gatepass_data($id)
{
    $sql = "SELECT * FROM ".TB_PREF."multiple_gate_pass WHERE gate_pass_no=".db_escape($id)."";

    return db_query($sql, "could not get customer");

}

function get_user_name_701234($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
print_deliveries();

//----------------------------------------------------------------------------------------------------

function print_deliveries()
{
	global $path_to_root, $packing_slip, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$email = $_POST['PARAM_2'];
	$packing_slip = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];


	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

//	$fno = explode("-", $from);
//	$tno = explode("-", $to);
//	$from = min($fno[0], $tno[0]);
//	$to = max($fno[0], $tno[0]);

	$cols = array(3, 60, 260, 330, 380, 460, 520);

	// $headers in doctext.inc
	$aligns = array('center', 'left', 'center', 'center', 'center',	'center');

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
	for ($i = $from; $i <= $to; $i++)
	{
//		if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
//			continue;
//		$myrow = get_customer_trans($i, ST_CUSTDELIVERY);
//		$branch = get_branch($myrow["branch_code"]);
//		$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
//		if ($email == 1)
//		{
//			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
//			if ($packing_slip == 0)
//			{
//				$rep->title = _('DELIVERY NOTE');
//				$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
//			}
//			else
//			{
//				$rep->title = _('PACKING SLIP');
//				$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
//			}
//		}

        $GatePassHeader = get_gatepass_data($i);
		$rep->SetHeaderType('Header119');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);
        $GatePassHeader = db_fetch($GatePassHeader);
        $rep->SetCommonData($GatePassHeader, null, null, '', ST_CUSTDELIVERY, null);
        $rep->NewPage();
        $GatePassDelivery = get_gatepass_data($i);
        $total_carton = 0;
        $packing = 0;
        $total_qty_carton = 0;
        while($myrow = db_fetch($GatePassDelivery))
        {
            $rep->TextCol(3, 4, $myrow['delivery_no']);
//            display_error($myrow['delivery_no']);
            $result = get_customer_trans_details(ST_CUSTDELIVERY, $myrow['delivery_no']);
            while ($myrow2=db_fetch($result))
            {
                $rep->TextCol(0, 1, $myrow2['stock_id']);
                $rep->TextCol(2, 3, $myrow2['carton']);
                $total_carton += $myrow2['quantity'];
                $packing += $myrow2['carton'];
              
                $rep->TextCol(5, 6, $myrow2['quantity']);
                  
                //   $rep->NewLine(-1);
          
                $item=get_item($myrow2['stock_id']);
                $qty_in_carton = $myrow2['quantity'] / $item["con_factor"];
                $rep->TextCol(4, 5, $qty_in_carton);
                $total_qty_carton +=  $qty_in_carton;
//                $rep->TextCol(2, 3, $item['carton'], -2);
                if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
                {
                    $item=get_item($myrow2['stock_id']);

//                    $rep->TextCol(2, 3, $item['carton'], -2);

//                    if ($get_debtor_trans['tax_included'] == 1) {



                        if($myrow2['units_id']!='pack'){

                            $cartons=$myrow2["quantity"]*$item["con_factor"];


//                            $rep->TextCol(6, 7, $unit_price, -2);
//                            $rep->TextCol(4, 5,$myrow2["quantity"]	, -2);
                           // $rep->TextCol(2, 3,$item['carton']	, -2);

//                            $exclusive_value=$unit_price*$myrow2["quantity"];
//                            $total_packing +=$myrow2["quantity"];
//
//                            $total_cartons +=$cartons;
//

                        }
                              $rep->TextColLines(1, 3, $myrow2['description']);
                }
                $rep->NewLine();
            }
        }

//   $rep->TextColLines(1, 3,$total_carton);
 $rep->MultiCell(115, 15, "".$total_qty_carton, 0, 'L', 0, 2, 450,665, true);
 //$rep->MultiCell(115, 15, "".$packing, 0, 'L', 0, 2, 320,665, true);
 $rep->MultiCell(115, 15, "".$total_carton, 0, 'L', 0, 2, 515,665, true);
//		$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
//		$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
//
//		$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
//		$SubTotal = 0;
//		$a=1;
//
//		while ($myrow2=db_fetch($result))
//		{
//			if ($myrow2["quantity"] == 0)
//				continue;
//
//			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
//				user_price_dec());
//			$SubTotal += $Net;
//			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
//			$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
//			$DisplayNet = number_format2($Net,$dec);
//			if ($myrow2["discount_percent"]==0)
//				$DisplayDiscount ="";
//			else
//				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
////			$rep->TextCol(0, 1,	$a, -2);
//			$oldrow = $rep->row;
////			$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
//			$newrow = $rep->row;
//			$rep->row = $oldrow;
//			if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
//			{
//				$pref = get_company_pref();
////                $item=get_item($myrow2['stk_code']);
//				if($pref['alt_uom'] == 1)
//				{
////					$rep->TextCol(2, 3, $myrow2["quantity"]." ".$myrow2['units_id'], -2);
//				}
//				else {
////					$rep->TextCol(2, 3, $myrow2["quantity"]." ".$myrow2['units'], -2);
//				}
//				//$rep->TextCol(6, 7,	$myrow2['units'], -2);
//				if ($packing_slip == 0)
//				{
//					//$rep->TextCol(4, 5,	$DisplayPrice, -2);
//					//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
//					//$rep->TextCol(6, 7,	$DisplayNet, -2);
//				}
//			}
//			$a++;
//			$rep->row = $newrow;
//			//$rep->NewLine(1);
//			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//				$rep->NewPage();
//		}
////        $rep->MultiCell(115, 15, "".$sales_order['reference'], 0, 'L', 0, 2, 450,246, true);
////        $rep->MultiCell(115, 15, "".$myrow['reference'], 0, 'L', 0, 2, 450,200, true);
//                		$memo = get_comments_string(ST_CUSTDELIVERY, $i);
//		if ($memo != "")
//		{
//			$rep->NewLine();
//			//$rep->TextColLines(1, 5, $memo, -2);
//		}
//
//		$DisplaySubTot = number_format2($SubTotal,$dec);
//		$DisplayFreight = number_format2($myrow["ov_freight"],$dec);
//
//		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
//		$doctype=ST_CUSTDELIVERY;
//		if ($packing_slip == 0)
//		{
//			//$rep->TextCol(3, 6, _("Sub-total"), -2);
//			//$rep->TextCol(6, 7,	$DisplaySubTot, -2);
//			$rep->NewLine();
//			//$rep->TextCol(3, 6, _("Shipping"), -2);
//			//$rep->TextCol(6, 7,	$DisplayFreight, -2);
//			$rep->NewLine();
//			$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
//			$first = true;
//			while ($tax_item = db_fetch($tax_items))
//			{
//				if ($tax_item['amount'] == 0)
//					continue;
//				$DisplayTax = number_format2($tax_item['amount'], $dec);
//
//				if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
//					$tax_type_name = $tax_item['tax_type_name'];
//				else
//					$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";
//
//				if ($tax_item['included_in_price'])
//				{
//					if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1)
//					{
//						if ($first)
//						{
//							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//							$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
//							$rep->NewLine();
//						}
//						//$rep->TextCol(3, 6, $tax_type_name, -2);
//						//$rep->TextCol(6, 7,	$DisplayTax, -2);
//						$first = false;
//					}
//					//else
//					//$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
//				}
//				else
//				{
//					//$rep->TextCol(3, 6, $tax_type_name, -2);
//					//$rep->TextCol(6, 7,	$DisplayTax, -2);
//				}
//				$rep->NewLine();
//			}
//// 			$rep->NewLine();
//			$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
//				$myrow["ov_amount"],$dec);
//			$rep->Font('bold');
//
//			//$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. GST"), - 2);
//			//$rep->TextCol(6, 7,	$DisplayTotal, -2);
//			$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
//			if ($words != "")
//			{
//				//$rep->NewLine(1);
//				//$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
//			}
//			$rep->Font();
//		}
//        $rep->MultiCell(115, 15, "".get_quotation_no($i), 0, 'L', 0, 2, 450,215, true);
////
//
//        $rep->Font('bold');
//        $rep->TextCol(1, 2, _("Total"), -2);
//        $rep->TextCol(2, 5, _("378.00"), -2);
//        $rep->TextCol(5, 7, _("3024.00"), -2);
//        $rep->Font('');
////        $rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
//
//        if ($email == 1)
//		{
//			$rep->End($email);
//		}


  $user =get_user_id($myrow['delivery_no'],ST_CUSTDELIVERY);
        $rep->MultiCell(100, 25, "".get_user_name_701234($user) ,0, 'C', 0, 2, 87,755, true);


	}

	if ($email == 0)
		$rep->End();
}

?>