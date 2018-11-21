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

//----------------------------------------------------------------------------------------------------
function get_purchasing_date1($item_code,$batch)
{
	$sql = "SELECT date1 FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($item_code)."AND batch=".db_escape($batch);

	$result = db_query($sql, "could not get Dates");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_consignee1($customer_id)
{
	$sql = "SELECT consignee FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get consigee");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_supplier_reference1($customer_id)
{
	$sql = "SELECT reference FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get consigee");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_invoice_through_dn($trans_no)
{
	$sql = "SELECT reference FROM ".TB_PREF."debtor_trans WHERE order_=".db_escape($trans_no);
$sql .= " AND type=10";
	$result = db_query($sql, "error");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_shipper_name($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_payment_terms_ ($selected_id)
{
    $sql = "SELECT terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

    $result = db_query($sql,"could not get payment term");
    $row = db_fetch_row($result);
    return $row[0];



}
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

	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	$cols = array( 40,300, 420);

	// $headers in doctext.inc
	$aligns = array('left','left',	'right');

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
			if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
        continue;
			$myrow = get_customer_trans($i, ST_CUSTDELIVERY);
			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
			
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
				if ($packing_slip == 0)
				{
					$rep->title = _('DELIVERY NOTE');
					$rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
				}
				else
				{
					$rep->title = _('PACKING SLIP');
					$rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
				}
			}
			$rep->SetHeaderType('Header11030');
			$rep->currency = $cur;
			$rep->Font();
			$rep->Info($params, $cols, null, $aligns);

			$contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
			$rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
			$rep->NewPage();

   			$result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
			$SubTotal = 0;
//			$rep->MultiCell(182.5, 15,"Despatch Through :      ".$sales_order['location_name'] ,0, 'L', 0, 2, 405,110, true);

        while ($myrow2=db_fetch($result))
			{
				if ($myrow2["quantity"] == 0)
					continue;
					
				$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
	    		$DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
	    		$DisplayNet = number_format2($Net,$dec);
				$DisplayUnit = $myrow2['units'];
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";

				$rep->Font('bold');
				$rep->TextCol(0, 1,	$myrow2['StockDescription']." (".$myrow2['stock_id'].")", -2);
				$rep->NewLine(1);
				$rep->Font();
				$rep->TextCol(0, 1,	"Batch: ".$myrow2['batch'], -2);
				$rep->NewLine();

				$oldrow = $rep->row;
//				$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
				$expired_date = get_purchasing_date1($myrow2['stock_id'],$myrow2['batch']);
				$newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
				{			
					//	$rep->TextCol(2, 3,	$DisplayQty, -2);
//					$rep->TextCol(2, 3,$myrow2['batch']."   ", -2);
				//	$rep->TextCol(3, 4,	$myrow2['units'], -2);
					if ($packing_slip == 0)
					{
						$rep->TextCol(0, 1,	"Expiry: ".$expired_date, -2);
//						$rep->TextCol(5, 6,	" ".$expired_date, -2);
						//$rep->TextCol(5, 6,	$DisplayDiscount, -2);
						$rep->NewLine(-2);
						$rep->TextCol(1, 2,$myrow2['bonus'], -2);
						$rep->TextCol(2, 3,	$DisplayQty." ".$myrow2['units'], -2);
//						$rep->TextCol(2, 3,	$DisplayPrice, -2);
//						$rep->TextCol(3, 4,	$myrow2['units'], -2);
//						$rep->TextCol(4, 5,	$DisplayNet, -2);
						$TotalQuantity += $DisplayQty;
					}
				}

				$rep->row = $newrow;
				//$rep->NewLine(1);
				if ($rep->row < $rep->bottomMargin + (17 * $rep->lineHeight))
					$rep->NewPage();


				$rep->NewLine(1.5);
			}

			$memo = get_comments_string(ST_CUSTDELIVERY, $i);
			if ($memo != "")
			{
				$rep->NewLine(-20);
			//	$rep->TextColLines(1, 2, $memo, -2);
                $rep->NewLine(+20);

            }

   			$DisplaySubTot = number_format2($SubTotal,$dec);
   			$DisplayFreight = number_format2($myrow["ov_freight"],$dec);
		

    		$rep->row = $rep->bottomMargin + (21 * $rep->lineHeight);
			$doctype=ST_CUSTDELIVERY;
			if ($packing_slip == 0)
//				$rep->TextCol(0,1, _("Total Quantity"), -2);

            $rep->NewLine(7);

        $rep->TextCol(1, 2, _('Total          ')." ".$TotalQuantity." ".$DisplayUnit, -2);
        $rep->NewLine(-7);

        {
				//$rep->TextCol(3, 6, _("Sub-total"), -2);
				//$rep->TextCol(6, 7,	$DisplaySubTot, -2);
				$rep->NewLine();
				//$rep->TextCol(3, 6, _("Shipping"), -2);
				//$rep->TextCol(6, 7,	$DisplayFreight, -2);
				$rep->NewLine();
				$tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
				$first = true;
    			while ($tax_item = db_fetch($tax_items))
    			{
    				if ($tax_item['amount'] == 0)
    					continue;
    				$DisplayTax = number_format2($tax_item['amount'], $dec);
 
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
								$rep->TextCol(6, 7,	number_format2($tax_item['net_amount'], $dec), -2);
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
						$rep->TextCol(3, 6, $tax_type_name, -2);
						$rep->TextCol(6, 7,	$DisplayTax, -2);
					}
					$rep->NewLine();
    			}
    			$rep->NewLine();
				$DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
					$myrow["ov_amount"],$dec);
				$rep->Font('bold');
				//$rep->TextCol(3, 6, _("TOTAL DELIVERY INCL. GST"), - 2);
			//	$rep->TextCol(6, 7,	$DisplayTotal, -2);
				$words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
				if ($words != "")
				{
					$rep->NewLine(1);
					$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);

				}

				$rep->Font();
				$myrow3 = get_invoice_through_dn($myrow['order_']);

//				$myrow4 = get_consignee1($myrow['order_']);
//				$rep->multicell(80,10,"".$myrow4,0,'L',0,0,485,128);

				$myrow5 = get_supplier_reference1($myrow['order_']);
				//$rep->multicell(150,10,"Supplier's Ref.No:   ".$myrow5,0,'L',0,0,405,130);



				//$rep->multicell(150,10,"Delivery Date:   ".$myrow['due_date'],0,'L',0,0,405,185);

				$fetch = get_customer($myrow['debtor_no']);
            //$rep->multicell(80,10,"NTN #:  ".$fetch['ntn_id'],0,'L',0,0,90,105);
//			$rep->multicell(150,10,$fetch['debtor_ref'],0,'L',0,0,85,200);


			$rep->multicell(150,10,"Recd. in Good Condition",0,'L',0,0,60,650);


			$rep->multicell(525, 110, "", 1, 'L', 0, 0, 40, 605);

			$rep->Font('italic');

			$rep->multicell(100,10,"E. & O. E.",0,'R',0,0,450,657);
			$rep->Font('');



            $rep->multicell(100,10,"Inventory Controller",0,'C',0,0,455,735);

			}	
			if ($email == 1)
			{
				$rep->End($email);
			}
	}
	if ($email == 0)
		$rep->End();
}

?>