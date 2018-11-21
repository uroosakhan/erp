<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SUPPTRANSVIEW' : 'SA_SUPPBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Purchase Orders
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/db/crm_contacts_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------

print_po();

//----------------------------------------------------------------------------------------------------
function get_po2($trans_no)
{
   	$sql = "SELECT ".TB_PREF."supp_trans.*, ".TB_PREF."suppliers.supp_name,".TB_PREF."supp_trans.supp_reference,  "
   		.TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms,".TB_PREF."suppliers.ntn_no,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, ".TB_PREF."suppliers.tax_group_id
		FROM ".TB_PREF."supp_trans, ".TB_PREF."suppliers
		WHERE ".TB_PREF."supp_trans.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."supp_trans.trans_no = ".db_escape($trans_no);
   	$result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_po_details2($supp_trans_no)
{
	$sql = "SELECT ".TB_PREF."supp_invoice_items.*, ".TB_PREF."stock_master.units
		FROM ".TB_PREF."supp_invoice_items
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."supp_invoice_items.stock_id=".TB_PREF."stock_master.stock_id
		WHERE supp_trans_no =".db_escape($supp_trans_no)." ";
	$sql .= " ORDER BY supp_trans_no";
	return db_query($sql, "Retreive order Line Items");
}

function get_ref_for_order_no($trans_no)
{
    $sql = "SELECT reference 
    FROM ".TB_PREF."purch_orders, ".TB_PREF."purch_order_details, ".TB_PREF."grn_items,
    ".TB_PREF."supp_invoice_items
	WHERE ".TB_PREF."purch_orders.order_no = ".TB_PREF."purch_order_details.order_no
	AND ".TB_PREF."purch_order_details.po_detail_item = ".TB_PREF."grn_items.po_detail_item
	AND ".TB_PREF."grn_items.id = ".TB_PREF."supp_invoice_items.grn_item_id
	AND ".TB_PREF."supp_invoice_items.supp_trans_no = ".db_escape($trans_no)."
	";
    return db_query($sql, "could not retreive default customer currency code");

}

function print_po()
{
	global $path_to_root, $show_po_item_codes;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(4, 60,  300, 340, 385, 450, 515);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'right', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 7, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_po2($i);
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];

		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 7, $orientation);
			$rep->title = _('PURCHASE ORDER');
			$rep->filename = "PurchaseOrder" . $i . ".pdf";
		}	
		$rep->SetHeaderType('Header214');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
		$rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
		$rep->NewPage();

		$result = get_po_details2($i);
		$SubTotal = 0;
        $serial_no = 0;
		$items = $prices = array();
		while ($myrow2=db_fetch($result))
    {
        $data = get_purchase_data($myrow['supplier_id'], $myrow2['item_code']);
        if ($data !== false)
        {
            if ($data['supplier_description'] != "")
                $myrow2['description'] = $data['supplier_description'];
            if ($data['suppliers_uom'] != "")
                $myrow2['units'] = $data['suppliers_uom'];
            if ($data['conversion_factor'] != 1)
            {
                $myrow2['unit_price'] = round2($myrow2['unit_price'] * $data['conversion_factor'], user_price_dec());
                $myrow2['quantity_ordered'] = round2($myrow2['quantity_ordered'] / $data['conversion_factor'], user_qty_dec());
            }
        }
        $Net = round2(($myrow2["unit_price"] * $myrow2["quantity"]), user_price_dec());
        $prices[] = $Net;
        $items[] = $myrow2['item_code'];
        $SubTotal += $Net;
        $dec2 = 0;
        $DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
        $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
        $DisplayNet = number_format2($Net,$dec);
        $serial_no ++;
        $rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
//            $rep->TextCol(1, 2,	$myrow2['description'], -2);
        $rep->TextCol(2, 3,	$DisplayQty, -2);
        $rep->TextCol(3, 4,	$myrow2['units'], -2);
        $rep->TextCol(4, 5,	$DisplayPrice, -2);
        $rep->TextCol(5, 6,	$DisplayNet, -2);
        $rep->TextCollines(1, 2, $myrow2['description'], -2);
        if($myrow2['text1'] != '')
            $rep->TextCollines(1, 2, $myrow2['text1'], -2);
//			$rep->NewLine(1);
        if ($rep->row < $rep->bottomMargin + (2 * $rep->lineHeight))
            $rep->NewPage();
    }
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}



        if($serial_no >= 2) {
            $rep->NewLine(33 - $serial_no);
        }
        else{
            $rep->NewLine(32);
        }
        $serial_no = 0;

        $result1 = get_po_details2($i);
        while ($myrow2=db_fetch($result1))
        {
            $data = get_purchase_data($myrow['supplier_id'], $myrow2['item_code']);
            if ($data !== false)
            {
                if ($data['supplier_description'] != "")
                    $myrow2['description'] = $data['supplier_description'];
                if ($data['suppliers_uom'] != "")
                    $myrow2['units'] = $data['suppliers_uom'];
                if ($data['conversion_factor'] != 1)
                {
                    $myrow2['unit_price'] = round2($myrow2['unit_price'] * $data['conversion_factor'], user_price_dec());
                    $myrow2['quantity_ordered'] = round2($myrow2['quantity_ordered'] / $data['conversion_factor'], user_qty_dec());
                }
            }
            $Net1 = round2(($myrow2["unit_price"] * $myrow2["quantity"]), user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['item_code'];
            $SubTotal1 += $Net1;
            $dec2 = 0;
            $DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            $serial_no++;
            $rep->TextCol(0, 1,	$myrow2['stock_id'], -2);
//            $rep->TextCol(1, 2,	$myrow2['description'], -2);
            $rep->TextCol(2, 3,	$DisplayQty, -2);
            $rep->TextCol(3, 4,	$myrow2['units'], -2);
            $rep->TextCol(4, 5,	$DisplayPrice, -2);
            $rep->TextCol(5, 6,	$DisplayNet, -2);
            $rep->TextCollines(1, 2, $myrow2['description'], -2);
            if($myrow2['text1'] != '')
                $rep->TextCollines(1, 2, $myrow2['text1'], -2);
//			$rep->NewLine(1);
//            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//                $rep->NewPage();
        }
		$DisplaySubTot = number_format2($SubTotal,$dec);

//		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_PURCHORDER;
		
        $rep->NewLine(-20.5);
		$rep->TextCol(4, 5, _("Sub-total"), -2);
		$rep->TextCol(5, 6,	$DisplaySubTot, -2);
        $rep->NewLine(+20.5);
		$rep->NewLine();
//   $rep->NewLine(-20.5);
	
		
        // $rep->NewLine(+20.5);
		$tax_items = get_tax_for_items($items, $prices, 0,
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
						$rep->TextCol(4, 5, _("Total Tax Excluded"), -2);
						$rep->TextCol(5, 6,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
					$rep->TextCol(4, 5, $tax_type_name, -2);
					$rep->TextCol(5, 6,	$DisplayTax, -2);
					$first = false;
				}
				else
					$rep->TextCol(4, 5, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				$rep->TextCol(4, 5, $tax_type_name, -2);
				$rep->TextCol(5, 6,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine(-20.5);
		$DisplayTotal = number_format2($SubTotal, $dec);
		$rep->Font('bold');
		$rep->TextCol(4, 5, _("TOTAL PO"), - 2);
		$rep->TextCol(5, 6,	$DisplayTotal, -2);
        $rep->NewLine(+20.5);
        
        
        
        	$rep->NewLine(+12);
		$DisplayTotal1 = number_format2($SubTotal1, $dec);
	
			$rep->Font('');
        $rep->TextCol(4, 5, _("Sub-total"), -2);
		$rep->TextCol(5, 6,	$DisplaySubTot, -2);
			$rep->NewLine();
				$rep->Font('bold');
			$rep->TextCol(4, 5, _("TOTAL PO"), - 2);
		$rep->TextCol(5, 6,	$DisplayTotal1, -2);
        	$rep->NewLine(-12);
        
        
        
        
		$words = price_in_words($SubTotal, ST_PURCHORDER);
        $amount_in_words = _number_to_words($SubTotal);
		if ($words != "")
		{
			$rep->NewLine(1);
			$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
		}
		$rep->Font();
		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['supp_name'];

			if ($myrow['reference'] == "")
				$myrow['reference'] = $myrow['order_no'];
			$rep->End($email);
		}
        $do_no1 =get_ref_for_order_no($myrow["trans_no"]);
        $trans_ = '';
        while($row = db_fetch($do_no1))
        {
            if($trans_ != '')
                $trans_ .= ',';

            $trans_ .=  $row['reference'];
        }


        $rep->MultiCell(100,13," PO No: ",0,'L', 0, 2,393,80,true);
        $rep->MultiCell(100,13, $trans_,0,'L', 0, 2,450,80,true);
        
        
   $rep->MultiCell(100,13," PO No: ",0,'L', 0, 2,393,510,true);
        $rep->MultiCell(100,13, $trans_,0,'L', 0, 2,450,510,true);
    }

    $rep->font('b');
    $rep->multicell(85,15,"Amount in words:",0,'L',0,0,40,375);
    $rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 375);
    $rep->font('');

    $rep->multicell(150,15,"________________________",0,'L',0,0,90,410);
    $rep->multicell(150,15,"Prepared by",0,'L',0,0,40,410);
    $rep->multicell(150,15,"________________________",0,'L',0,0,435,410);
    $rep->multicell(150,15,"Approved by",0,'L',0,0,390,410);


    $rep->font('b');
    $rep->multicell(85,15,"Amount in words:",0,'L',0,0,40,775);
    $rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 775);
    $rep->font('');

    $rep->multicell(150,15,"________________________",0,'L',0,0,90,810);
    $rep->multicell(150,15,"Prepared by",0,'L',0,0,40,810);
    $rep->multicell(150,15,"________________________",0,'L',0,0,435,810);
    $rep->multicell(150,15,"Approved by",0,'L',0,0,390,810);

    if ($email == 0)
		$rep->End();
}

?>