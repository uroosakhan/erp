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
function get_po($order_no)
{
   	$sql = "SELECT po.*, supplier.supp_name, supplier.supp_account_no,supplier.tax_included,
   		supplier.gst_no AS tax_id,po.payments as PaymentsTerm,
   		supplier.curr_code, supplier.payment_terms, loc.location_name,
   		supplier.address, supplier.contact, supplier.tax_group_id
		FROM ".TB_PREF."purch_orders po,"
			.TB_PREF."suppliers supplier,"
			.TB_PREF."locations loc
		WHERE po.supplier_id = supplier.supplier_id
		AND loc.loc_code = into_stock_location
		AND po.order_no = ".db_escape($order_no);
   	$result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_po_details($order_no)
{
	$sql = "SELECT poline.*, units
		FROM ".TB_PREF."purch_order_details poline
			LEFT JOIN ".TB_PREF."stock_master item ON poline.item_code=item.stock_id
		WHERE order_no =".db_escape($order_no)." ";
	$sql .= " ORDER BY po_detail_item";
	return db_query($sql, "Retreive order Line Items");
}
function get_payment_terms_22($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
    $result = db_query($sql,"could not get paymentterms");
    return db_fetch($result);

}

function print_po()
{
	global $path_to_root, $SysPrefs;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pictures = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

    $pref = get_company_prefs();
     	$myrow3 = get_company_item_pref('con_factor');
    if($pref['alt_uom'] == 1  && $myrow3['purchase_enable'] == 1){
				 
	$cols = array(4,10, 40, 225, 300, 340, 365, 410,470);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left',	'left', 'right', 'left', 'left', 'left','right');
    }
    else{
       	$cols = array(4,30, 70, 215, 280,340, 390, 420, 465,470);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left',	'left', 'right', 'left', 'left', 'right','right'); 
        
    }

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_po($i);
		if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
			continue;
		}
		$baccount = get_default_bank_account($myrow['curr_code']);
		$params['bankaccount'] = $baccount['id'];

		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep->title = _('PURCHASE ORDER');
			$rep->filename = "PurchaseOrder" . $i . ".pdf";
		}	
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
		$rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);

          $rep->SetHeaderType('Header209100');
         
		$rep->NewPage();
        $S_no =0;
        $total_amount = 0;
		$result = get_po_details($i);
		$SubTotal = 0;
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
			$Net = round2(($myrow2["unit_price"] * $myrow2["quantity_ordered"]), user_price_dec());
			$prices[] = $Net;
			$items[] = $myrow2['item_code'];
			$SubTotal += $Net;
			$dec2 = 0;
			     if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100 ,user_percent_dec());
                 

			$DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
			  $item=get_item($myrow2['item_code']);
			if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
			$DisplayQty = number_format2($myrow2["quantity_ordered"] * $item['con_factor'],get_qty_dec($myrow2['item_code']));
			else
			$DisplayQty = number_format2($myrow2["quantity_ordered"] ,get_qty_dec($myrow2['item_code']));

			
			$DisplayNet = number_format2($Net,$dec);
			
			$rep->TextCol(3, 4,	sql2date($myrow2['delivery_date']), -2);
			 $pref = get_company_prefs();
			 
            
              $S_no ++;
                $rep->TextCol(0, 1, $S_no, -2);
                 $rep->TextCol(7, 8, $DisplayDiscount."%", -2);
                 $discount_rate = $DisplayDiscount / 100;
                  $discount_amount = price_format($myrow2["unit_price"]*$myrow2["quantity_ordered"] * $discount_rate) ;
                  
                  
                //  display_error($myrow2["quantity_ordered"]);
             $rep->TextCol(1, 2, $myrow2['item_code'], -2);
               
			$rep->Amountcol(4, 5,	$DisplayQty, $dec);
		
			
			//$rep->TextCol(4, 5,	$myrow2['units'], -2);
			$rep->TextCol(5, 6,	$myrow2['units_id'], -2);
			$myrow3 = get_company_item_pref('con_factor');
			if($pref['alt_uom'] == 1 ){
			    if($myrow3['purchase_enable'] == 1){
            $rep->TextCol(6, 7,	$myrow2['con_factor'], -2);//new
             $rep->TextCol(6, 7,	$DisplayPrice, -2);
           
			$rep->TextCol(8, 9,	$discount_amount, -2);

			    }
			   
           
			}
			else{
			    $rep->TextCol(6, 7,	$DisplayPrice, -2);
			 //$total_with_discountedamount = $DisplayNet * $discount_amount;
			$rep->TextCol(8, 9,	$discount_amount, -2);
			}
			  $rep->TextCol(6, 7,	$DisplayPrice, -2);
// 		 $total_with_discountedamount = $DisplayNet * $discount_amount;
			$rep->TextCol(8, 9, $discount_amount, -2);
			$total_amount += $myrow2["unit_price"] * $myrow2["quantity_ordered"] * $discount_rate;
				if ($SysPrefs->show_po_item_codes()) {
				$rep->TextCol(0, 1,	$myrow2['item_code'], -2);

				$rep->TextCollines(2, 3,	$myrow2['description'], -2);
										//	$rep->NewLine(1);

			} else

				$rep->TextCollines(2, 3,	$myrow2['description'], -2);
				
				
				
			$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}
// 		 $rep->multicell(100,15,"".$total_amount,0,'L',0,0,40,700);
		if ($myrow['comments'] != "")
		{
			$rep->NewLine();
			$rep->TextColLines(1, 5, $myrow['comments'], -2);
		}
		$DisplaySubTot = number_format2($SubTotal,$dec);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_PURCHORDER;
  $pref = get_company_prefs();
     
       if($pref['alt_uom'] == 1 ){
           $myrow3 = get_company_item_pref('con_factor');
           if($myrow3['purchase_enable'] == 1)
           {
		$rep->TextCol(3, 5, _("Sub-total"), -2);
		$rep->TextCol(7, 8,	$DisplaySubTot, -2);
           }
          
       }
        else{
                 $rep->TextCol(3, 5, _("Sub-total"), -2);
		$rep->TextCol(6, 7,	$DisplaySubTot, -2);
           }
       
       
         
    
		$rep->NewLine();

		$tax_items = get_tax_for_items($items, $prices, 0,
		  $myrow['tax_group_id'], $myrow['tax_included'],  null, TCA_LINES);
		$first = true;
		foreach($tax_items as $tax_item)
		{
			if ($tax_item['Value'] == 0)
				continue;
			$DisplayTax = number_format2($tax_item['Value'], $dec);

			if ($SysPrefs->suppress_tax_rates() == 1)
				$tax_type_name = $tax_item['tax_type_name'];
			else
				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

			if ($myrow['tax_included'])
			{
				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
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
					$rep->TextCol(4, 6, _("Included") . " " . $tax_type_name . _("Amount") . ": ", -2);
                $rep->TextCol(6, 7,	$DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				$myrow3 = get_company_item_pref('con_factor');
           if( $pref['alt_uom'] == 1 && $myrow3['purchase_enable'] == 1){
				$rep->TextCol(4, 7, $tax_type_name, -2);
				$rep->TextCol(7, 8,	$DisplayTax, -2);
           }
           else{
               	$rep->TextCol(3, 6, $tax_type_name, -2);
				$rep->TextCol(6, 7,	$DisplayTax, -2);
           }
				
			}
			$rep->NewLine();
		}

		$rep->NewLine();
		$DisplayTotal = number_format2($total_amount, $dec);
		$rep->Font('bold');
		$rep->TextCol(3, 5, _("TOTAL PO").' '.'(' .$rep->formData['curr_code'].')', - 2);
		$rep->TextCol(8, 9,	price_format($total_amount), -2);
		    $rep->multicell(100,15,"  Amount in words:",0,'L',0,0,40,718);
		       $amount_in_words = _number_to_words($total_amount);
     $rep->multicell(400, 15, " " . $amount_in_words, 0, 'L', 0, 0, 135, 718);

		$words = price_in_words($SubTotal, ST_PURCHORDER);
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
		
	}
	
    $rep->multicell(150,15,"________________________",0,'L',0,0,40,760);
    $rep->multicell(150,15,"Prepared by",0,'L',0,0,65,775);
    $rep->multicell(150,15,"________________________",0,'L',0,0,190,760);
    $rep->multicell(150,15,"Checked by",0,'L',0,0,220,775);
    $rep->multicell(150,15,"________________________",0,'L',0,0,340,760);
    $rep->multicell(150,15,"Authorised by",0,'L',0,0,370,775);
    $rep->multicell(150,15,"________________________",0,'R',0,0,420,760);
    $rep->multicell(150,15,"Approved by",0,'R',0,0,390,775);
	
// 	if ($pictures)
// 	{
// 		$image = company_path() . '/images/'. $rep ->company['coy_logo'];
// 		$imageheader = company_path() . '/images/Footer.png';
// 			if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
// 				$rep->NewPage();
// 					$rep->AddImage($image, $rep->cols[1] +300, $rep->row +570, null,$rep->company['logo_w'], $rep->company['logo_h']);
// 		$rep->MultiCell(500, 20, "This is computer generated document and does not require any stamp or signature" ,0, 'L', 0, 2, 130,790, true);
// 			$rep->AddImage($imageheader, $rep->cols[1] -55, $rep->row -180, 510,20, $SysPrefs->pic_height);
// 			$rep->NewLine();
// 	}
	if ($email == 0)
		$rep->End();
}

