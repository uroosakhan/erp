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
//include_once($path_to_root . "/reporting/includes/excel_report.inc");
//----------------------------------------------------------------------------------------------------

print_po();


//----------------------------------------------------------------------------------------------------
function get_po($from,$to)
{  
   	$sql = "SELECT ".TB_PREF."purch_orders.*, ".TB_PREF."suppliers.supp_name,  "
   		.TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms, ".TB_PREF."locations.location_name,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, ".TB_PREF."suppliers.tax_group_id
		FROM ".TB_PREF."purch_orders, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE ".TB_PREF."purch_orders.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."purch_orders.ord_date >= '$from' 
		AND ".TB_PREF."purch_orders.ord_date <= '$to'
		AND ".TB_PREF."locations.loc_code = into_stock_location";

	$result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_po_details($item,$supplier,$from,$to,$lc_ref,$lading_no)
{
    $from = date2sql($from);
    $to= date2sql($to);

	$sql = "SELECT ".TB_PREF."purch_order_details.*,
	 ".TB_PREF."purch_orders.*,".TB_PREF."stock_master.units, 
	 ".TB_PREF."locations.location_name
		FROM ".TB_PREF."purch_order_details,".TB_PREF."purch_orders,"
		.TB_PREF."locations,".TB_PREF."stock_master
	
		WHERE  ".TB_PREF."purch_orders.ord_date >= '$from' 
		AND ".TB_PREF."purch_orders.ord_date <= '$to'
		 
		AND ".TB_PREF."purch_orders.order_no=".TB_PREF."purch_order_details.order_no 
		
		
		AND ".TB_PREF."stock_master.stock_id=".TB_PREF."purch_order_details.item_code 
		
		AND  ".TB_PREF."locations.loc_code =  ".TB_PREF."purch_orders.into_stock_location";

		 if($item!=''){
			 $sql.="  AND ".TB_PREF."purch_order_details.item_code=".db_escape($item)."";
		 }

		 if($supplier != ALL_TEXT){
			 $sql.="  AND ".TB_PREF."purch_orders.supplier_id=".db_escape($supplier)."";
		 }



if($lc_ref !=''){
			 $sql.="  AND ".TB_PREF."purch_orders.reference=".db_escape($lc_ref)."";
		 }

if($lading_no !=''){
			 $sql.="  AND ".TB_PREF."purch_orders.receive_ref =".db_escape($lading_no)."";
		 }
	$sql .= " ORDER BY  ".TB_PREF."purch_order_details.po_detail_item";
	return db_query($sql, "Retreive order Line Items");
}


function get_order_total($supplier_id,$from,$to)
{
    $sql = "SELECT (line.quantity_ordered-line.quantity_received)
FROM   ".TB_PREF."purch_orders as porder,
".TB_PREF."purch_order_details as line
WHERE 
       porder.supplier_id=".db_escape($supplier_id)."
   AND porder.order_no = line.order_no
    AND porder.ord_date >= '$from' 
		AND porder.ord_date <= '$to'
   ";
    $result = db_query($sql, "could not get supplier");
    $row = db_fetch_row($result);

    return $row[0];
}



function print_po()
{
	global $path_to_root, $show_po_item_codes;

//	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$supplier = $_POST['PARAM_2'];
	$item = $_POST['PARAM_3'];
        $lc_ref  = $_POST['PARAM_4'];
        $lading_no = $_POST['PARAM_5'];
	$currency = $_POST['PARAM_6'];
	$email = $_POST['PARAM_7'];
	$comments = $_POST['PARAM_8'];
	$orientation = $_POST['PARAM_9'];
	$destination = $_POST['PARAM_10'];
		$no_zeros = $_POST['PARAM_11'];

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	if (!$from || !$to) return;

	$orientation =  'L';
	$dec = user_price_dec();

	$cols = array(0, 100, 150, 235, 315, 370, 500, 570,630,700,700);

	// $headers in doctext.inc
	$aligns = array('left',	'left','left',	'left', 'left', 'left', 'left','left', 'right', 'right', 'right', 'right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrder", user_pagesize(), 9, $orientation);


//	for ($i = $from; $i <= $to; $i++)
	{
		$myrow = get_po($from,$to);

		if ($email == 1)
		{
			$rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
			$rep->title = _('PURCHASE ORDER');
			$rep->filename = "PurchaseOrder" . $i . ".pdf";
		}
		$rep->Info($params, $cols, null, $aligns);
		$rep->SetHeaderType('Header20921');
		$rep->currency = $cur;
		$rep->Font();

		$contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
//		$rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
		$rep->NewPage();


		$SubTotal = 0;
		$items = $prices = array();
		$s_no=0;

		$sql = "SELECT supplier_id, supp_name AS name, curr_code FROM ".TB_PREF."suppliers";
		if ($supplier != ALL_TEXT)
			$sql .= " WHERE supplier_id=".db_escape($supplier);
		$sql .= " ORDER BY supp_name";
		$results = db_query($sql, "The customers could not be retrieved");


		while ($myrow=db_fetch($results)){


			$rep->Line($rep->row +10);
			$rep->Font('bold');

        
            $result = get_po_details($item,$myrow['supplier_id'],$from,$to,$lc_ref,$lading_no);

           
//  $total =get_order_total($myrow['supplier_id'],$from,$to);
//  display_error($total);
    if($no_zeros && $total!=0 )continue;
    
            $rep->TextCol(0, 2,		get_supplier_name($myrow['supplier_id'] ), -2);
            $rep->NewLine(1,2);
            $rep->Font('');
            $rep->Line($rep->row + 10);

            while ($myrow2=db_fetch($result))
		{
   
            $DisplayQty = number_format2($myrow2["quantity_ordered"]-$myrow2["quantity_received"],get_qty_dec($myrow2['item_code']));
            $Net = round2(($myrow2["quantity_ordered"]-$myrow2["quantity_received"])*$myrow2["unit_price"] , user_price_dec());
            if ($Net > 0) {
                $prices[] = $Net;
                $items[] = $myrow2['item_code'];
                $SubTotal += $Net;
                $dec2 = 0;
                $DisplayPrice = price_decimal_format($myrow2["unit_price"], $dec2);
                $DisplayNet = number_format2($Net, $dec);


                $rep->TextCol(5, 6, $myrow2['description'], -2);
                $rep->TextCol(1, 2, ($myrow2['order_no']), -2);

                $rep->TextCol(2, 3, ($myrow2['reference']), -2);



                $rep->TextCol(3, 4, ($myrow2['receive_ref']), -2);

                $rep->TextCol(4, 5, ($myrow2['location_name']), -2);

                $rep->TextCol(6, 7, $DisplayQty, -2);
                $rep->TextCol(7, 8, sql2date($myrow2['ord_date']), -2);

                $rep->TextCol(8, 9, sql2date($myrow2['arrival_date']), -2);
                if(!user_check_access('SA_SUPPPRICES'))
                $rep->TextCol(9, 10, ($DisplayNet), -2);
                $rep->NewLine();
            }
            
			if ($rep->row < $rep->bottomMargin + (13 * $rep->lineHeight))
				$rep->NewPage();
		}

		$doctype = ST_PURCHORDER;

		$rep->NewLine();

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
					//	$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
					//	$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
						$rep->NewLine();
					}
//					$rep->TextCol(3, 6, $tax_type_name, -2);
//					$rep->TextCol(6, 7,	$DisplayTax, -2);
					$first = false;
				}
//				else
//					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
			}
			else
			{
				$SubTotal += $tax_item['Value'];
//				$rep->TextCol(3, 6, $tax_type_name, -2);
//				$rep->TextCol(6, 7,	$DisplayTax, -2);
			}
			$rep->NewLine();
		}

		$rep->NewLine();
		$DisplayTotal = number_format2($SubTotal, $dec);
		$rep->Font('bold');

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
	}}
	if ($email == 0)
		$rep->End();
}

?>