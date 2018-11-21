<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL,
	as published by the Free Software Foundation, either version 3
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/

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
function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}

function get_po($order_no)
{
   	$sql = "SELECT po.*, supplier.supp_name, supplier.supp_account_no,supplier.tax_included,
   		supplier.gst_no AS tax_id,
   		supplier.ntn_no ,
   		supplier.address ,
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

function print_po()
{
	global $path_to_root, $SysPrefs;

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

	$cols = array(4, 50,120,155, 210, 230, 280,328,360,405, 460, 455, 470);

	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left',	'left',	'left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 7.8, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_po($i);
	//	display_error($myrow['tax_id']."+".$myrow['ntn_no']);
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

		$rep->SetHeaderType('Header20924');
		$rep->NewPage();

		$result = get_po_details($i);
		$SubTotal = 0;
		$items = $prices = array();
		$total_alt = 0;
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
				  $item=get_item($myrow2['item_code']);
			if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
			$DisplayQty = number_format2($myrow2["quantity_ordered"] * $item['con_factor'],get_qty_dec($myrow2['item_code']));
			else
			$DisplayQty = number_format2($myrow2["quantity_ordered"] ,get_qty_dec($myrow2['item_code']));

				     if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100 ,user_percent_dec());
                 
            $pref=get_company_prefs();
			$item = get_item($myrow2['item_code']);
				   
  $discount_amount =$myrow2["unit_price"] * $discount_rate ;
// 		display_error("discount".$discount_rate);
//  display_error("amountdiscount".$discount_amount);
//  	
 	$discounted_price = $myrow2["unit_price"] - $discount_amount;
//  	display_error($discounted_price * $myrow2["quantity_ordered"] );
 	$discounted_amount = $discounted_price * $myrow2["quantity_ordered"];
			$items[] = $myrow2['item_code'];
		
			$dec2 = 0;
			$DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
		
			 //  $discount_rate = $DisplayDiscount / 100;
    
                 
                    //   $discount_amount = $DisplayPrice * $DisplayDiscount;
                    // if($DisplayDiscount == 0)
                    // {
                        // $rep->TextCol(9,8,	"net".$DisplayNet, -2);
                    // }
                    // else
                    // {
                      
                    // }
                 
                       
                        
                 
			if($pref['alt_uom'] == 1 ) {
			$item = get_item($myrow2['item_code']);
			

			
			$DisplayQty = number_format2($myrow2["quantity_ordered"]  ,get_qty_dec($myrow2['item_code']));
			$rep->TextCol(8, 9, $DisplayDiscount."%", -2);
		
              
            
                  
                  
				 if($item['units'] != $myrow2['units_id'] && $item['units'] != $myrow2['units_id'] ){
				$Net = round2(($myrow2["unit_price"] * ($myrow2["quantity_ordered"]  * $item['con_factor'] )), user_price_dec());
//                     display_error($Net);
				 }
				 else{
				     	$Net = round2(($myrow2["unit_price"] * ($myrow2["quantity_ordered"]   )), user_price_dec());
//                     display_error($Net);
				 }

                $prices[] = $Net;
			}
				$SubTotal += $Net;
			
// 			$DisplayQty = number_format2($myrow2["quantity_ordered"]  ,get_qty_dec($myrow2['item_code']));
			$DisplayNet = number_format2($Net,$dec);
		
			
            

           
            
// 			$rep->TextCol(2, 3,	$item['amount2'].	$item['text3'], -2);
				
				// 	$rep->TextCol(3, 4,	$item['text4'].	$item['text1'], -2);

            $csv = str_replace('MICRON', 'MIC', $item['text2']);

				// 	$rep->TextCol(4, 5,	$item['amount6'].	$csv, -2);
				
					 if($item['units'] != $myrow2['units_id'] ){
            $rep->TextCol(5, 6,	number_format2($myrow2["quantity_ordered"]  * $item['con_factor'],2), -2);
            $rep->TextCol(7,8 ,	number_format2($myrow2["unit_price"]), -2);
            $discount_rate = $DisplayDiscount / 100;
            $rep->TextCol(9, 8,number_format2(($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])-(($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])*$discount_rate), -2));
              $total_alt += ($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])-(($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])*$discount_rate);
        //   display_error($total_alt);
        
					 }
            else{
                $discount_rate = $DisplayDiscount / 100;
               $rep->TextCol(5, 6,	number_format2($myrow2["quantity_ordered"]  ,2), -2);
               $rep->TextCol(7,8 ,	number_format2($myrow2["unit_price"]), -2);
               $rep->TextCol(9, 8,number_format2(($myrow2["quantity_ordered"]*$myrow2["unit_price"])-($myrow2["quantity_ordered"]*$myrow2["unit_price"])*$discount_rate, -2));
              //$total_alt += ($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])-(($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])*$discount_rate);
  $total_alt +=  $myrow2["quantity_ordered"]*$myrow2["unit_price"]-($myrow2["quantity_ordered"]*$myrow2["unit_price"])*$discount_rate;
        //   display_error($total_alt);
            }
            $rep->TextCol(6, 7,	$myrow2['units_id'], -2);
//            $rep->TextCol(7, 8,	$myrow2["amount3"]."/Rolls", -2);
            
		//	$rep->TextCol(6, 7,	$myrow2['units_id'], -2);
//			$rep->TextCol(7, 8,	$DisplayPrice, -2);

$rep->TextCol(10, 11,	sql2date($myrow2['delivery_date']), -2);
			
				 $rep->TextCol(0, 1,	$myrow2['item_code'], -2);
				$rep->TextCollines(1, 3,	$myrow2['description'], -2);
				// if ($SysPrefs->show_po_item_codes()) {
				
// 				$rep->TextCollines(1, 2,	$myrow2['description'], -2);
// 			} else
// 				$rep->TextCollines(0, 2,	$myrow2['description'], -2);
			
// 			$rep->NewLine(0.1);
// 				$rep->TextCol(2, 3,	$item['text1'], -2);
// 				$rep->TextCol(3, 4,	$item['text3'], -2);
// 				$rep->TextCol(4, 5,	$item['text2'], -2);
// 				$rep->TextCol(8, 9,$myrow['curr_code'], -2);
 			// 	$rep->TextCol(8, 9,$myrow['comments'], -2);
				
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();
		}

//		$rep->font('b');
//$rep->MultiCell(350, 20,"Instructions" , 0, 'L', 0, 2, 40, 767, true);
//$rep->font('');
//$rep->MultiCell(350, 20,$myrow['Comments2'] , 0, 'L', 0, 2, 130, 767, true);

		$DisplaySubTot = number_format2($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])-(($myrow2["quantity_ordered"]  * $item['con_factor']*$myrow2["unit_price"])*$discount_rate);

		$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
		$doctype = ST_PURCHORDER;

// 		$rep->TextCol(4, 8, _("Sub-total"), -2);
// 		$rep->TextCol(8, 9,	$total_alt, -2);
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
				$tax_type_name = $tax_item['tax_type_name'];

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
					$rep->TextCol(4, 6, $tax_type_name, -2);
					$rep->TextCol(8, 9,	$DisplayTax, -2);
					$first = false;
				}
				else
					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
				$rep->TextCol(4, 6, $tax_type_name, -2);
				$rep->TextCol(8, 9,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine();
		$DisplayTotal = number_format2($SubTotal, $dec);
	
	
		$rep->Font('bold');
		$rep->TextCol(4, 8, _("TOTAL PO"), - 2);
		$rep->TextCol(8, 9,	number_format2($total_alt), -2);
		$words = price_in_words($SubTotal, ST_PURCHORDER);
	$rep->MultiCell(451, 20, "Amount in Words:" . "  " . convert_number_to_words($total_alt) . " " . "Only", 0, 'L', 0, 2, 41, 690, true);
	
	

$rep->MultiCell(451, 20, "Terms and Conditions:", 0, 'L', 0, 2, 41, 718, true);

        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
            //	$rep->TextColLines(1, 4,  "".htmlspecialchars_decode//($myrow['comments']), -2);
            $rep->Font('');
            $str=str_replace("&amp","",$myrow['comments']);
            $rep->MultiCell(355, 50,"".$str , 0, 'L', 0, 2, 130, 717, true);
        }
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
	if ($email == 0)
		$rep->End();
}
