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

function get_supplier_information_report($debtor_no)
{
	$sql = "SELECT * 
	FROM `0_crm_persons`
	WHERE `id`
	IN (SELECT person_id
		FROM `0_crm_contacts`
		WHERE `type`='supplier'
		AND `action`='general'
		AND entity_id
		IN (SELECT supplier_id
			FROM `0_suppliers`
			WHERE supplier_id = '$debtor_no'))";
	$result = db_query($sql, "Error");
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
				 
	$cols = array(4, 60, 225, 300, 340, 365, 410,470);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'right', 'left', 'left', 'left','right');
    }
    else{
       	$cols = array(4, 60, 225, 300, 340, 385, 450);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'right', 'left', 'right','right'); 
        
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





		$rep->SetHeaderType('Header20910');
		$rep->NewPage();

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
			$DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
			$DisplayQty = number_format2($myrow2["quantity_ordered"],get_qty_dec($myrow2['item_code']));
			$DisplayNet = number_format2($Net,$dec);
// 			if ($SysPrefs->show_po_item_codes()) {
 				$rep->TextCol(0, 1,	$myrow2['item_code'], -2);

// 				$rep->TextCollines(1, 2,	$myrow2['description'], -2);
// 											$rep->NewLine(1);

// 			} else

// 				$rep->TextCol(1, 2,	$myrow2['description'], -2);
						//	$rep->NewLine(-1);

			$rep->TextCol(2, 3,	sql2date($myrow2['delivery_date']), -2);
						//	$rep->NewLine(-1);


			 $pref = get_company_prefs();
              $item=get_item($myrow2['item_code']);

                if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])

			$rep->Amountcol(3, 4,	$myrow2["quantity_ordered"] * $item['con_factor'], $dec);
			else
			$rep->Amountcol(3, 4,	$myrow2["quantity_ordered"], $dec);
			
			//$rep->TextCol(4, 5,	$myrow2['units'], -2);
			$rep->TextCol(4, 5,	$myrow2['units_id'], -2);
			$myrow3 = get_company_item_pref('con_factor');
			if($pref['alt_uom'] == 1 ){
			    if($myrow3['purchase_enable'] == 1){
            $rep->TextCol(5, 6,	$myrow2['con_factor'], -2);
             $rep->TextCol(6, 7,	$DisplayPrice, -2);
			$rep->TextCol(7, 8,	$DisplayNet, -2);

			    }
			   
           
			}
			else{
			    $rep->TextCol(5, 6,	$DisplayPrice, -2);
			$rep->TextCol(6, 7,	$DisplayNet, -2);
			}
//				if ($SysPrefs->show_po_item_codes()) {
//				$rep->TextCol(0, 1,	$myrow2['item_code'], -2);

				$rep->TextCollines(1, 2,	$myrow2['description'], -2);
										//	$rep->NewLine(1);

//			} else

//				$rep->TextCollines(1, 2,	$myrow2['description'], -2);
				
				
				
			$rep->NewLine(1);
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
		$doctype = ST_PURCHORDER;
  $pref = get_company_prefs();
     
       if($pref['alt_uom'] == 1 ){
           $myrow3 = get_company_item_pref('con_factor');
           if($myrow3['purchase_enable'] == 1)
           {
					$rep->TextCol(5, 7, _("Sub-total"), -2);
					$rep->TextCol(7, 8,	$DisplaySubTot, -2);
           }
          
       }
        else{
                 $rep->TextCol(3, 6, _("Sub-total"), -2);
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
		$DisplayTotal = number_format2($SubTotal, $dec);
		$rep->Font('bold');
		$rep->TextCol(5, 6, _("TOTAL PO"), - 2);
		$rep->TextCol(6, 7,	$DisplayTotal, -2);
		$words = _number_to_words($SubTotal);
//		if ($words != "")
//		{
			$rep->NewLine(1);
			$rep->TextCol(2, 4, "In Words:", - 2);
		$rep->NewLine(1.5);
			$rep->TextCol(1, 6,  $words ." ".$myrow['curr_code'] . " Only" , - 2);
//		}
		$rep->Font();
		if ($email == 1)
		{
			$myrow['DebtorName'] = $myrow['supp_name'];

			if ($myrow['reference'] == "")
				$myrow['reference'] = $myrow['order_no'];
			$rep->End($email);
		}
		
	}
	
//	if ($pictures)
//	{
//		$image = company_path() . '/images/'. $rep ->company['coy_logo'];
//		$imageheader = company_path() . '/images/Footer.png';
////		if (file_exists($image))
////		{
////			display_error("gj01");
//			//$rep->NewLine();
//			if ($rep->row - $SysPrefs->pic_height < $rep->bottomMargin)
//				$rep->NewPage();
//					$rep->AddImage($image, $rep->cols[1] +320, $rep->row +610, 105,null,$rep->company['logo_w'], $rep->company['logo_h']);
//
//			$rep->AddImage($imageheader, $rep->cols[1] -100, $rep->row -170, 600,20, $SysPrefs->pic_height);
////		echo '<center><img src='headers.PNG' ></center>';
//
////			$rep->AddImage($imageheader, $rep->cols[1] +320, $rep->row +580, 100, $SysPrefs->pic_height);
////		$rep->Text(cols[1] +300, $rep->company['coy_name'], $icol);
////				$rep->row -= $SysPrefs->pic_height;
//			$rep->NewLine();
//		//}
//	}
	if ($email == 0)
		$rep->End();
}

