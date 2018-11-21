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
function get_qoh_on_date12($stock_id, $location=null, $date_=null, $exclude=0, $customer_id)
{



        $date = date2sql($date_);
    $sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
            WHERE stock_id=".db_escape($stock_id);

    $result = db_query($sql, "QOH calulcation failed");

    $myrow = db_fetch_row($result);
    if ($exclude > 0)
    {
        $sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves
            WHERE stock_id=".db_escape($stock_id)
            ." AND type=".db_escape($exclude)
            ." AND tran_date = '$date'";

        $result = db_query($sql, "QOH calulcation failed");
        $myrow2 = db_fetch_row($result);
        if ($myrow2 !== false)
            $myrow[0] -= $myrow2[0];
    }

    return $myrow[0];
}

//----------------------------------------------------------------------------------------------------

print_po();
//----------------------------------------------------------------------------------------------------
function get_po($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_orders.*, ".TB_PREF."purch_orders.payments as PaymentsTerm, ".TB_PREF."suppliers.supp_name,  "
        .TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms, ".TB_PREF."locations.location_name,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, 
   		".TB_PREF."purch_orders.requisition_no AS number,".TB_PREF."suppliers.tax_group_id, ".TB_PREF."suppliers.gst_no, ".TB_PREF."suppliers.ntn_no
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
function get_payment_terms_22($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($id);
    $result = db_query($sql,"could not get paymentterms");
    return db_fetch($result);

}
function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 1";
    $result = db_query($sql, 'error');
    return $result;
}

function get_tax_rate_2()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 2";
    $result = db_query($sql, 'error');
    return $result;
}
function print_po()
{
    global $path_to_root, $show_po_item_codes,$db_connections;

if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RPL2'){
     include_once($path_to_root . "/reporting/includes/pdf_purchase_report_email.inc");
}
else{
     include_once($path_to_root . "/reporting/includes/pdf_report.inc");
}
   

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $cols = array(0, 60, 195, 275, 345, 405 ,470);
//    $cols2 = array(0, 10/*,160,190, 230, 300, 370, 430,470*/);

    // $headers in doctext.inc
    $aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'right', 'right', 'right');
//    $aligns2 = array('center',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++) {
        $myrow = get_po($i);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        if ($email == 1) {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('PURCHASE ORDER');
            $rep->filename = "PurchaseOrder" . $i . ".pdf";
        }
        $rep->SetHeaderType('Header2092');
        $rep->currency = $cur;
        $rep->Font();

        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
        $rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
        $rep->NewPage();

        $result = get_po_details($i);
        $SubTotal = 0;
        $items = $prices = array();


        while ($myrow2 = db_fetch($result)) {
            $data = get_purchase_data($myrow['supplier_id'], $myrow2['item_code']);
            if ($data !== false) {
                if ($data['supplier_description'] != "")
                    $myrow2['description'] = $data['supplier_description'];
                if ($data['suppliers_uom'] != "")
                    $myrow2['units'] = $data['suppliers_uom'];
                if ($data['conversion_factor'] != 1) {
                    $myrow2['unit_price'] = round2($myrow2['unit_price'] * $data['conversion_factor'], user_price_dec());
                    $myrow2['quantity_ordered'] = round2($myrow2['quantity_ordered'] / $data['conversion_factor'], user_qty_dec());
                }
            }
            $Net = round2(($myrow2["unit_price"] * $myrow2["quantity_ordered"]), user_price_dec());
            $Net1 = $myrow2["unit_price"] * $myrow2["quantity"];
            $prices[] = $Net;
            $items[] = $myrow2['item_code'];
            $SubTotal += $Net;
            $am_inwords += $Net1;
            $myrow3 = db_fetch(get_tax_rate_1());
            $myrow4 = db_fetch(get_tax_rate_2());
            $dec2 = 0;
            $DisplayQty = number_format2($myrow2["quantity_ordered"], get_qty_dec($myrow2['item_code']));
            $gross_amt = round($DisplayQty * $myrow2["unit_price"]);
            $gross_amt2 = ($DisplayQty * $myrow2["unit_price"]);
            $rate = number_format2($myrow2['unit_price'], get_qty_dec($myrow2['item_code']));
            // if($myrow['tax_group_id'] == 1 &&  $myrow2['tax_type_id'] == 2)
            $rate1 = ($myrow3['rate']) / 100;
            $rate2 = ($myrow4['rate']) / 100;
            $sales_tax_amount = round($rate1 * $gross_amt2);
            $sales_tax_amount3 = ($rate1 * $gross_amt2);
            $sales_tax_amount2 = (($rate2 + $gross_amt));
            //else
            $sale_tax += $sales_tax_amount;

            $TOTAL=$myrow2["quantity_ordered"]*$myrow2['unit_price'];
            $grosstot += $TOTAL;
            
            $TOTAL_QTY +=$myrow2["quantity_ordered"];
//            $grosstot1 = round2($grosstot);
            $DisplayNet = ($rate + $sales_tax_amount + $sales_tax_amount2);
            $net += $DisplayNet;
            // if ($show_po_item_codes) {
            $rep->TextCol(0, 1, $myrow2['item_code'], -2);
//            $oldrow = $rep->row;
//            $newrow = $rep->row;
//            $rep->row = $oldrow;
            // } else
            $net_amt = round($gross_amt + $sales_tax_amount);
            $net_amt2 = $gross_amt2 + $sales_tax_amount3;
            $net_amt_tot += $net_amt;
            $net_amt_tot1 = round2($net_amt_tot, (get_qty_dec($myrow2['item_code'])));
            $qoh = get_qoh_on_date($myrow2['item_code'], null, null, null);
//            $rep->NewLine();
//                $rep->TextColLines(1, 2,	$myrow2['description'], -2);
            $rep->TextCol(3, 4, $DisplayQty, -2);
            $rep->TextCol(4, 5, $myrow2['units'], -2);
            $rep->TextCol(5, 6, $rate, -2);
            
            $rep->TextCol(6, 7, price_format($TOTAL) , -2);
         $TOTAL_RATE +=$rate;

            $rep->TextCollines(1, 2, $myrow2['description'], -2);
//            $rep->NewLine(1);
//            $rep->NewLine(+3);
            if ($rep->row < $rep->bottomMargin + (20 * $rep->lineHeight))
                $rep->NewPage();
        }

        $rep->row = $rep->bottomMargin + (30 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
        if ($myrow['comments'] != "") {
            $rep->NewLine();
            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal, $dec);

//        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
//        $doctype = ST_PURCHORDER;


        $rep->NewLine();

        $tax_items = get_tax_for_items($items, $prices, 0,
            $myrow['tax_group_id'], $myrow['tax_included'], null);
        $first = true;
        foreach ($tax_items as $tax_item) {
            if ($tax_item['Value'] == 0)
                continue;
            $DisplayTax = number_format2($tax_item['Value'], $dec);

            $tax_type_name = $tax_item['tax_type_name'];

            if ($myrow['tax_included']) {
                if (isset($alternative_tax_include_on_docs) && $alternative_tax_include_on_docs == 1) {
                    if ($first) {
//                        $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//                        $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }
                    // $rep->TextCol(3, 6, $tax_type_name, -2);
                    // $rep->TextCol(6, 7,	$DisplayTax, -2);
                     $rep->multicell(200, 5, "".$tax_type_name, 0, 'L', 0, 0, 400, 560);

                    $rep->multicell(80, 5, "".$DisplayTax, 0, 'L', 0, 0, 520, 560);
                    $first = false;
                } else
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            } else {
                $SubTotal += $tax_item['Value'];
                // $rep->TextCol(3, 6, $tax_type_name, -2);
                // $rep->TextCol(6, 7,	$DisplayTax, -2);
                                    $rep->multicell(200, 5, "".$tax_type_name, 0, 'L', 0, 0, 400, 560);

                    $rep->multicell(80, 5, "".$DisplayTax, 0, 'L', 0, 0, 520, 560);

            }
            $rep->NewLine();

        }
        // display_error($SubTotal);

        $rep->NewLine(+8);
        $DisplayTotal = number_format2($SubTotal, $dec);
        $rep->Font('bold');


        $words = price_in_words($SubTotal, ST_PURCHORDER);
        if ($words != "") {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, -2);
        }
        $rep->Font();
        if ($email == 1) {
            $myrow['DebtorName'] = $myrow['supp_name'];

            if ($myrow['reference'] == "")
                $myrow['reference'] = $myrow['order_no'];
            $rep->End($email);
        }

    }
    $rep->Font('bold');
    $rep->multicell(80, 5, " TOTAL AMOUNT", 0, 'L', 0, 0, 40, 580);
    $rep->multicell(85, 5, price_format($TOTAL_QTY), 0, 'R', 0, 0, 280, 580);

    if ($myrow['tax_group_id'] == 1)
    {
        $rep->multicell(90, 5, price_format($TOTAL_RATE), 0, 'R', 0, 0, 390, 580);
        $rep->multicell(65, 5, price_format($SubTotal), 0, 'R', 0, 0, 500, 580);
        $amount_in_words = _number_to_words($SubTotal);
        $rep->multicell(400, 15, " " . $amount_in_words, 0, 'L', 0, 0, 125, 600);
    }
    else
    {
     $rep->multicell(65, 5, price_format($SubTotal), 0, 'R', 0, 0, 475, 575);
     $amount_in_words = _number_to_words($SubTotal);
     $rep->multicell(400, 15, " " . $amount_in_words, 0, 'L', 0, 0, 125, 600);
    }
        $rep->Font('');

    $rep->font('b');
    $rep->multicell(85,15,"  Amount in words:",0,'L',0,0,40,600);
    $rep->font('');

    $rep->NewLine(+2);

    $rep->NewLine(-1.1);

  //  $rep->setfontsize(+7);
   $rep->multicell(800,15,"TERM AND CONDITIONS",0,'L',0,0,40,615);

 $rep->multicell(800,15,"1- PRODUCT SHOULD BE DELIVERED AS PER  ABOVE  MENTION SPECIFICATION AND QUALITY",0,'L',0,0,40,630);
  $rep->multicell(510,15,"2- IN CASE OF ANY CHANGES IN GOOD'S SPECIFICATION, QUALITY OR PRICE WRITTEN APPROVAL SHOULD BE TAKEN OTHER WISE DELIVERY WILL NOT BE ACCEPTABLE",0,'L',0,0,40,641);
  $rep->multicell(450,15,"3- IF THE GOODS ORDERED ARE NOT SUPPLIED ON THE STIPULATED TIME GIVEN; THE ORDER MAY BE TREATED AS CANCELLED",0,'L',0,0,40,665);
  $rep->multicell(450,15,"4- IF IN CASE OF ANY DELAY, INTIMATION SHOULD BE SENT TO US IMMEDIATELY",0,'L',0,0,40,688);
 $rep->multicell(450,15,"5- OUR ORDER NUMBER TO BE STATED ON DELIVERY CHALLAN AND BILL / INVOICE ",0,'L',0,0,40,702);
  $rep->multicell(450,15,"6- ACKNOWLEDGMENT OF PURCHASE ORDER TO BE GIVEN IMMEDIATELY AFTER RECEIPT, OF P.O VIA COURIER , E-MAIL OR TAX ",0,'L',0,0,40,715);
  $rep->multicell(450,15,"7- ANY CONDITION IF MENTIONED ON THE VENDOR'S DELIVERY CHALLAN OR INVOICE/BILL , WOULD NOT BE ACCEPTABLE UNLESS A FORMAL AGREEMENT GIVEN BY US.",0,'L',0,0,40,738);

 
   $rep->multicell(450,15,"8- GOOD SHOULD BE DELIVER AT SPECIFIED LOCATION IN P.O ",0,'L',0,0,40,762);

 

    $rep->multicell(150,15,"_______________________",0,'L',0,0,40,785);
    $rep->multicell(150,15,"Prepared by",0,'L',0,0,65,797);
    $rep->multicell(150,15,"_______________________",0,'L',0,0,190,785);
    $rep->multicell(150,15,"Checked by",0,'L',0,0,220,797);
    $rep->multicell(150,15,"_______________________",0,'L',0,0,335,785);
    $rep->multicell(150,15,"Authorised by",0,'L',0,0,370,797);
    $rep->multicell(150,15,"_______________________",0,'R',0,0,420,785);
    $rep->multicell(150,15,"Approved by",0,'R',0,0,390,797);

    if ($email == 0)
        $rep->End();
}

?>