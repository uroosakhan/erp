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
function get_sales_order_details1093($order_no, $trans_type) {
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
			    cust.tax_group_id
				item.material_cost
			FROM ".TB_PREF."sales_order_details line,"
        .TB_PREF."cust_branch cust,"
        .TB_PREF."stock_master item
			WHERE line.stk_code = item.stock_id
			AND line.supplier_id = supplier.supplier_id
				AND order_no =".db_escape($order_no)
        ." AND trans_type = ".db_escape($trans_type) . " ORDER BY id";

    return db_query($sql, "Retreive order Line Items");
}
function print_sales_orders()
{
	global $path_to_root, $SysPrefs;

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
$myrow233 = get_company_item_pref('con_factor');
$pref = get_company_prefs();
     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
	$cols = array(4, 50, 160, 220, 260, 320, 370, 420,440, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left','left', 'left', 'right', 'right', 'right', 'right');
     }
     else{
         	$cols = array(4, 50, 160, 220, 260, 320, 400, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'right', 'left', 'right', 'right', 'right', 'right', 'right');
     }

	$params = array('comments' => $comments, 'print_quote' => $print_as_quote);

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

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_sales_order_header($i, ST_SALESORDER);
		if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
			continue;
		}
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];
		$branch = get_branch($myrow["branch_code"]);
		if ($email == 1)
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
		$rep->SetHeaderType('Header2');
		$rep->currency = $cur;
		$rep->Font();
		if ($print_as_quote == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
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
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
		$rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
		$rep->SetHeaderType('Header1093');
		$rep->NewPage();

		$result = get_sales_order_details($i, ST_SALESORDER);
		$SubTotal = 0;
		$items = $prices = array();
		while ($myrow2=db_fetch($result))
		{
		     $item=get_item($myrow2['stk_code']);
			     $pref = get_company_prefs();
		    
		     if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
		    	$qty=$myrow2['quantity'] * $myrow2['con_factor'];
		    	else
		    	$qty=$myrow2['quantity'];

			$Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
			   user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['stk_code'];
			$SubTotal += $Net;
			$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
			$DisplayQty = number_format2($qty,get_qty_dec($myrow2['stk_code']));
			$DisplayNet = number_format2($Net,$dec);
			if ($myrow2["discount_percent"]==0)
				$DisplayDiscount ="";
			else
				$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
			$rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
			$oldrow = $rep->row;
			$rep->TextColLines(1, 2, $myrow2['description'], -2);
			$newrow = $rep->row;
			$rep->row = $oldrow;
			if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
			{
			    $item=get_item($myrow2['stk_code']);
			    	 $pref = get_company_prefs();
			    
		
			   $rep->Amountcol(4, 5,	$qty ,$dec);
			
			
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                }
                else
                {
                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
                }
                	$myrow233 = get_company_item_pref('con_factor');
$pref = get_company_prefs();
     if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){
     
         $rep->Amountcol(5, 6,	$myrow2['con_factor'], $dec);
         	$rep->TextCol(6, 7,	$DisplayPrice, -2);
				$rep->TextCol(7, 8,	$DisplayDiscount, -2);
				$rep->TextCol(8, 9,	$DisplayNet, -2);
     }
     else{
         $rep->TextCol(5, 6,	$DisplayPrice, -2);
				$rep->TextCol(6, 7,	$DisplayDiscount, -2);
				$rep->TextCol(7, 8,	$DisplayNet, -2);
     }
     

			
			}
			$rep->row = $newrow;
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		$DisplaySubTot = number_format2($SubTotal,$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_SALESORDER;
$myrow3 = get_company_item_pref('con_factor');
           if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
		$rep->TextCol(5,8, _("Sub-total"), -2);
		$rep->TextCol(9, 10,	$DisplaySubTot, -2);
           }
           else{
               $rep->TextCol(4, 7, _("Sub-total"), -2);
		$rep->TextCol(7, 8,	$DisplaySubTot, -2);
           }
		$rep->NewLine();
		if ($myrow['freight_cost'] != 0.0)
		{
			$DisplayFreight = number_format2($myrow["freight_cost"],$dec);
			$rep->TextCol(4, 7, _("Shipping"), -2);
			$rep->TextCol(7, 8,	$DisplayFreight, -2);
			$rep->NewLine();
		}	
		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		if ($myrow['tax_included'] == 0) {
		     if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
			$rep->TextCol(5, 7, _("TOTAL ORDER EX GST"), - 2);
			$rep->TextCol(8, 9,	$DisplayTotal, -2);
		     }
		     else{
		         $rep->TextCol(4, 7, _("TOTAL ORDER EX GST"), - 2);
			$rep->TextCol(7, 8,	$DisplayTotal, -2);
		     }
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
				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
				{
					if ($first)
					{
						$rep->TextCol(4, 7, _("Total Tax Excluded"), -2);
						$rep->TextCol(7, 8,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
					if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
					$rep->TextCol(5, 7, $tax_type_name, -2);
					$rep->TextCol(7, 8,	$DisplayTax, -2);
					}
					else{
					    	$rep->TextCol(4, 7, $tax_type_name, -2);
					$rep->TextCol(7, 8,	$DisplayTax, -2);
					}
					$first = false;
				}
				else
					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount"). ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
				$rep->TextCol(5, 7, $tax_type_name, -2);
				$rep->TextCol(8, 9,	$DisplayTax, -2);
				}
				else{
				    $rep->TextCol(4, 7, $tax_type_name, -2);
				$rep->TextCol(7, 8,	$DisplayTax, -2);
				}
			}
			$rep->NewLine();
		}

		$rep->NewLine();

		$DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
		$rep->Font('bold');
		if( $pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1){
		$rep->TextCol(5, 7, _("TOTAL ORDER GST INCL."). ' ' . $rep->formData['curr_code'], - 2);
		$rep->TextCol(8, 9,	$DisplayTotal, -2);
		}
		else{
		    	$rep->TextCol(4, 7, _("TOTAL ORDER GST INCL."). ' ' . $rep->formData['curr_code'], - 2);
		$rep->TextCol(7, 8,	$DisplayTotal, -2);
		}
		$words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}	
		$rep->Font();
		        $rep->MultiCell(105,15,get_salesman_name($branch['salesman']),0,'C', 0, 2,145,270,true);

		if ($email == 1)
		{
			$rep->End($email);
		}
	}
	if ($email == 0)
		$rep->End();
}

