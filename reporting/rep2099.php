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

function get_payment_terms_report ($id)
{
   $sql = "SELECT terms  FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);

   $result = db_query($sql, "could not get payment terms");

   $row = db_fetch_row($result);

   return $row[0];
}
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
function get_payment_terms_23($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
    $result = db_query($sql,"could not get paymentterms");
    return db_fetch($result);

}
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

    $cols = array(5, 30,160,238, 315, 364, 400, 470);

    // $headers in doctext.inc
    $aligns = array('left',	'left','left',	'left',	'left', 'left', 'left', 'left');

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
        $rep->SetHeaderType('Header2099');
        $rep->NewPage();

        $result = get_po_details($i);
        $SubTotal = 0;
        $items = $prices = array();
        $s_no=0;
        
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
                        $Net_ = ($myrow2["unit_price"] * $myrow2["quantity_ordered"]);
   $DisplayTotal += $myrow2["unit_price"] * $myrow2["quantity_ordered"];
            $items[] = $myrow2['item_code'];
            $SubTotal += $Net;
            $dec2 = 0;
            $DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
            $DisplayQty = number_format2($myrow2["quantity_ordered"],get_qty_dec($myrow2['item_code']));
            $DisplayNet = number_format2($Net,$dec);
            $s_no++;
//			if ($SysPrefs->show_po_item_codes())
//			{
//				$rep->TextCol(0, 1,$s_no, -2);
//
//			}
//			else
            $rep->TextCol(0, 1,	$s_no, -2);
       //	$rep->TextCol(2, 3,	sql2date($myrow2['delivery_date']), -2);
            $rep->TextCol(4, 5,	$DisplayQty, -2);
            $rep->TextCol(5, 6,	$myrow2['units'], -2);
            $rep->TextCol(6, 7,	$DisplayPrice, -2);
            $rep->TextCol(7, 8,	$DisplayNet, -2);
          
            $rep->TextCollines(1, 2, $myrow2['description'], -2);
              $rep->NewLine(1);
            $rep->NewLine(+1);

           $rep->NewLine(-3);
            $rep->TextColLines(2, 3, $myrow2['text1'], -2);
           // $rep->NewLine(-2);
            $rep->TextCol(3, 4,$myrow2['text2']	, -2);
           // $rep->NewLine(+2);

            //$rep->NewLine(+1.5);

            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (5 * $rep->lineHeight))
                $rep->NewPage();
        }
        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
           // $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal, $dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_PURCHORDER;

        //$rep->TextCol(3, 6, _("Sub-total"), -2);
        //$rep->TextCol(6, 7,	$DisplaySubTot, -2);
        $rep->NewLine();

        $tax_items = get_tax_for_items($items, $prices, 0,
            $myrow['tax_group_id'], $myrow['tax_included'],  null, TCA_LINES);
        $first = true;
        foreach($tax_items as $tax_item)
        {
            if ($tax_item['Value'] == 0)
                continue;
            $DisplayTax = ($tax_item['Value']);

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
                        //  $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }
                    $rep->TextCol(3, 6, $tax_type_name, -2);
                    $rep->TextCol(6, 7,	$DisplayTax, -2);
                    $first = false;
                }
                //$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
                $SubTotal += $tax_item['Value'];
//                $rep->TextCol(3, 6, $tax_type_name, -2);
//                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->Font('bold');
            $rep->MultiCell(80, 10, "Sales Tax(17%)", 0, 'L', 0,1,400,425, true);
            $rep->MultiCell(91, 13, number_Format($DisplayTax, 2), 1, 'C', 0,1,488,423, true);
            $rep->Font('');
            $rep->NewLine();
        }

        $rep->NewLine(-17.5);
        $display_tax = number_Format($DisplayTax, 2);
     
        $TAX_TOTAL=($Net - $DisplayTax);
        $total_ = $DisplayTax + $Net;
        $rep->Font('bold');
        //$rep->TextCol(3, 6, _("TOTAL PO"), - 2);
         $rep-> AmountCol (7, 8,	$DisplayTotal + $DisplayTax, $dec);
        $words = price_in_words($SubTotal, ST_PURCHORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
        $rep->Font();
        $rep->MultiCell(535, 20,"Amount In Words:  ". _number_to_words($SubTotal) , 1, 'L', 0,1,45,450, true);

        if ($email == 1)
        {
            $myrow['DebtorName'] = $myrow['supp_name'];

            if ($myrow['reference'] == "")
                $myrow['reference'] = $myrow['order_no'];
            $rep->End($email);
        }
    }
    /////boxes
    $rep->Font('bold');
    $rep->MultiCell(535, 200, "", 1, 'L', 0,1,45,250, true);
// 	$rep->MultiCell(230, 60, "", 1, 'L', 0,1,350,490, true);

    $rep->MultiCell(535, 15, "Delivery Term:", 1, 'L', 0,1,45,490, true);

    $rep->MultiCell(240, 15, $myrow['delivery_term'], 0, 'L', 0,1,110,490, true);
    $rep->MultiCell(535, 15, "Sale Tax:", 1, 'L', 0,1,45,520, true);
    $rep->MultiCell(250, 15, $myrow['sales_tax'], 0, 'L', 0,1,90,520, true);
    $rep->MultiCell(535, 15, "Note:", 1, 'L', 0,1,45,535, true);
    $rep->MultiCell(535, 15, $myrow['comments'], 0, 'L', 0,1,80,535, true);
    $rep->MultiCell(535, 15, "Shipment:", 1, 'L', 0,1,45,550, true);
    $rep->MultiCell(490, 15,$myrow['Comments1'], 1, 'L', 0,1,90,550, true);
    $rep->MultiCell(535, 15, "Loading Port:", 1, 'L', 0,1,45,565, true);

    $rep->MultiCell(472, 15, $myrow['Comments2'], 1, 'L', 0,1,108,565, true);

    $rep->MultiCell(250, 15, "FOR OFFICIAL USE ONLY", 1, 'C', 0,1,45,580, true);
    $rep->MultiCell(535, 200, "", 1, 'L', 0,1,45,565, true);

    $rep->MultiCell(285, 185, "", 1, 'L', 0,1,295,580, true);

    $rep->MultiCell(285, 15, "For Supplier ", 1, 'C', 0,1,295,650, true);

    $rep->MultiCell(285, 15, "For Ressichem Pvt.Ltd ", 1, 'C', 0,1,295,750, true);

    $rep->MultiCell(500, 15, "", 1, 'L', 0,1,80,535, true);//Note


    $rep->MultiCell(80, 10, "Total Amount", 0, 'L', 0,1,400,438, true);
    $rep->MultiCell(91, 13, ($DisplaySubTot), 1, 'C', 0,1,488,410, true);
    $rep->MultiCell(91, 13, "Total", 0, 'L', 0,1,400,414, true);

    $rep->MultiCell(280, 80, "", 1, 'L', 0,1,300,170, true);

    $rep->MultiCell(24, 200, "", 1, 'L', 0,1,45,250, true);//S.NO

    $rep->MultiCell(76, 160, "", 1, 'L', 0,1,198,250, true);//size

    $rep->MultiCell(45, 160, "", 1, 'L', 0,1,352.5,250, true);//material/qty


    $rep->MultiCell(70, 160, "", 1, 'L', 0,1,435,250, true);//unit

    $rep->MultiCell(535, 40, "", 1, 'L', 0,1,45,410, true);//rate


    $rep->MultiCell(92,  40,"", 1, 'L', 0,1,488,410, true);//rate
// 	$rep->MultiCell(535, 50, "", 1, 'L', 0,1,45,400, true);//rate
// 	$rep->MultiCell(535, 50, "", 1, 'L', 0,1,45,400, true);//rate
// 	$rep->MultiCell(535, 50, "", 1, 'L', 0,1,45,400, true);//rate


    $rep->MultiCell(255, 80, "", 1, 'L', 0,1,45,170, true);

    $rep->MultiCell(255, 20, "SUPPLIER/SHIPPER", 1, 'C', 0,1,45,149, true);

    $rep->MultiCell(280, 20, "BILL TO/DELIVER TO/SHIP TO", 1, 'C', 0,1,300,149, true);
    $rep->MultiCell(280, 20, "P.R #: ".$myrow['pr'], 0, 'C', 0,1,355,138, true);

    $rep->MultiCell(535, 20, "Term & Conditions" , 1, 'C', 0,1,45,470, true);
    $rep->setfontsize(13.5);

    $rep->MultiCell(255, 20, "adding life and value to your property", 0, 'C', 0,1,195,60, true);
    $rep->setfontsize(14);
    $rep->Font('bold');
    $rep->Font('italic');
    $rep->MultiCell(255, 20, "Purchase Order", 0, 'C', 0,1,195,75, true);
    if ($email == 0)
        $rep->End();
}

?>