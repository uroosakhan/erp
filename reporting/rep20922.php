<?php

$page_security ='SA_SUPPTAX';
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
    $sql = "SELECT ".TB_PREF."purch_orders.*, ".TB_PREF."suppliers.supp_name,  "
        .TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms, ".TB_PREF."locations.location_name,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, ".TB_PREF."suppliers.tax_group_id
		FROM ".TB_PREF."purch_orders, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE ".TB_PREF."purch_orders.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."locations.loc_code = into_stock_location
		AND ".TB_PREF."purch_orders.order_no = ".db_escape($order_no);
    $result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_po_details($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE order_no =".db_escape($order_no)." ";
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
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

    $cols = array(0, 30, 55, 110, 148, 210,280,340,390,430,480);
    //$cols = array(4, 60, 120, 180, 220, 290, 320, 350,380,410,440  ,440,480,500);

    // $headers in doctext.inc
    $aligns = array('left','left','centre', 'left', 'left',
        'left','left','left','centre','left',
        'left');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {
        $myrow = get_supp_trans($i,43);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('PURCHASE ORDER');
            $rep->filename = "PurchaseOrder" . $i . ".pdf";
        }
        $rep->SetHeaderType('Header20922');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
        $rep->SetCommonData($myrow, null, $myrow, $baccount, 2099, $contacts);
        $rep->NewPage();

        $result = get_supp_invoice_items(ST_SUPPCREDIT_IMPORT,$i);
        $SubTotal = 0;
        $total_ex_purchase_amt=0;
        $total_sales_tax=0;
        $total_custom_duty=0;
        $unit_price_total=0;
        $total_assesed_import=0;
        $total_Duty=0;
        $total_qty=0;
        $total_purchases=0;
        $total_add_custom=0;
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

                $rep->TextCol(0, 1,	$myrow2['quantity'], -2);
                $rep->TextCol(1, 2,	$myrow2['units'], -2);
        // $rep->NewLine(-3);

 $assesed_import_value=$myrow2['Gross_Amt'];
 $custom_duty= $assesed_import_value* $myrow2['Landing']/100;
$price =($myrow2['Gross_Amt']+$custom_duty)/$myrow2['quantity'];



                $rep->TextCol(3, 4, $price, -2);

           
            $additonal_custom=$myrow2['Gross_Amt'] * $myrow2['INS']/100;
             $ex_value_purchase=$assesed_import_value + $custom_duty +$additonal_custom;
            $sales_tax=$ex_value_purchase * $myrow2['F_E_D']/100;


$total_price +=$price;
$total_add_custom +=$additonal_custom;
            $total_ex_purchase_amt += $ex_value_purchase;
            $total_sales_tax +=$sales_tax;
            $total_custom_duty += $custom_duty;
            $unit_price_total += $myrow2['unit_price'];
            $total_purchase = $ex_value_purchase + $sales_tax;
            $total_purchases+=$total_purchase;
            $total_assesed_import += $assesed_import_value;
            $total_qty +=$myrow2['quantity'];
            $F_E_D=$myrow2['F_E_D'];
            $LANDING = $myrow2['Landing'];//rate
            $INS=$myrow2['INS'];//rate
            $income_tax_deduction  += $myrow2['Duty_Amt'];
           $total_Duty = $total_custom_duty+$total_add_custom+$total_sales_tax +$income_tax_deduction ;


            $rep->TextCol(4, 5,number_format2($myrow2['Gross_Amt']), -2);
            //$rep->TextCol(5, 6,	number_format2($myrow2['Landing']), -2);
            $rep->TextCol(5, 6,	number_format2($custom_duty), -2);
           // $rep->TextCol(7, 8,	number_format2($INS), -2);
            $rep->TextCol(6, 7,	number_format2($additonal_custom), -2);
            $rep->TextCol(7, 8,	number_format2($ex_value_purchase), -2);
            $rep->TextCol(8, 9,	number_format2($F_E_D), -2);
            $rep->TextCol(9, 10,	number_format2($sales_tax) , -2);
            $rep->TextCol(10, 11,	number_format2($total_purchase) , -2);
             $rep->TextColLines(2, 3,	$myrow2['description'], -2);

//            $rep->NewLine(1);

            if ($rep->row < $rep->bottomMargin + (10* $rep->lineHeight))
                $rep->NewPage();
        }
       
        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal,$dec);

        $rep->row = $rep->bottomMargin + (18 * $rep->lineHeight);
        $doctype = ST_PURCHORDER;
        $rep->NewLine(2.5);
        $rep->TextCol(0, 1,	$total_qty, -2);
            $rep->TextCol(3, 4,	number_format2($total_price), -2);
            $rep->TextCol(4, 5,	number_format2($total_assesed_import), -2);
        //$rep->TextCol(5, 6,	number_format2($LANDING), -2);
        $rep->TextCol(5, 6,	number_format2($total_custom_duty), -2);
        //$rep->TextCol(7, 8,	number_format2($INS), -2);
        $rep->TextCol(6, 7,	number_format2($total_add_custom), -2);
        $rep->TextCol(7, 8,	number_format2($total_ex_purchase_amt), -2);
        $rep->TextCol(8, 9,	number_format2($F_E_D), -2);
        $rep->TextCol(9, 10,	number_format2($total_sales_tax), -2);
        $rep->TextCol(10, 11,	number_format2($total_purchases), -2);
 $rep->NewLine(2);
        $rep->font('bold');

        $rep->TextCol(6, 11,	"Total Ex. val. of Purchase Amt", -2);
        $rep->TextCol(10, 14,	number_format2($total_ex_purchase_amt), -2);
        $rep->NewLine(1);
        $rep->TextCol(6, 11,	"Total Sales Tax", -2);
        $rep->TextCol(10, 14,	number_format2($total_sales_tax), -2);
        $rep->NewLine(1);
        $rep->TextCol(6, 11,	"Total Inc. value of Purchase ", -2);
        $rep->TextCol(10, 14,	number_format2($total_purchases), -2);
        $rep->NewLine(1);

        $rep->TextCol(6, 11,	"Deduction Of Income Tax ", -2);
        $rep->TextCol(10, 14,number_format2($income_tax_deduction), -2);
        $rep->NewLine(1);
        $rep->TextCol(6, 11,	"Total Duties ", -2);
        $rep->TextCol(10, 14,	number_format2($total_Duty), -2);
//        $rep->TextCol(3, 6, _("Sub-total"), -2);
//        $rep->TextCol(6, 7,	$DisplaySubTot, -2);
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
                        $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
                        $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
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
                $SubTotal += $tax_item['Value'];
                $rep->TextCol(3, 6, $tax_type_name, -2);
                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->NewLine();
        }

        $rep->NewLine();
        $DisplayTotal = number_format2($SubTotal, $dec);
        $rep->Font('bold');
//        $rep->TextCol(3, 6, _("TOTAL PO"), - 2);
//        $rep->TextCol(6, 7,	$DisplayTotal, -2);
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
    if ($email == 0)
        $rep->End();
}

?>