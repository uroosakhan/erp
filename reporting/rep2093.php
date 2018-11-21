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

function get_user_name_209111($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_id_time209111($trans_no,$type)
{
    $sql= "SELECT user,  UNIX_TIMESTAMP(stamp) as unix_stamp
    FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
    $result = db_query($sql, "could not get customer");

    return db_fetch($result);
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

/*
function get_po_details($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code =".TB_PREF."stock_master.stock_id

		WHERE order_no =".db_escape($order_no)."
		
		";
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
}*/
function get_po_details($order_no)
{
    $sql = " ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE order_no =".db_escape($order_no)." ";
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
}
function get_po_details11($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details.*, units
		FROM ".TB_PREF."purch_order_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE order_no =".db_escape($order_no)." ";
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
}
function get_tax_rate_1()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 1";
    $result = db_query($sql, 'error');
    return $result;
}
//function get_purpose($trans_id)
//{
//    $sql = "SELECT purpose
//    FROM ".TB_PREF."requisition_details,
//         ".TB_PREF."purch_order_details
//    WHERE ".TB_PREF."requisition_details.requisition_detail_id = ".db_escape($trans_id)."
//    AND ".TB_PREF."requisition_details.lpo_id = ".TB_PREF."purch_order_details.order_no "
//    ;
//    return db_fetch(db_query($sql), "Cannot retrieve customer allocation $trans_id");
//}


function get_tax_rate_2()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 2";
    $result = db_query($sql, 'error');
    return $result;
}

function get_item_name($item)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($item);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_tax_rate_fida()
{
    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 3";
    $result = db_query($sql, 'error');
    return $result;
}
function get_phoneno_for_suppliers ($supplier_id)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN ( 
            SELECT person_id FROM `0_crm_contacts`
            WHERE `type`='supplier' AND `action`='general' AND entity_id IN ( 
            SELECT supplier_id FROM `0_suppliers` WHERE supplier_id = ".db_escape($supplier_id)."))";
    $query = db_query($sql, "Error");
    return db_fetch($query);
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

    $cols = array(0, 40, 160, 203, 230, 275, 330, 360, 420);
//    $cols2 = array(0, 10/*,160,190, 230, 300, 370, 430,470*/);

    // $headers in doctext.inc
    $aligns = array('left', 'left', 'right', 'center', 'right', 'right', 'left', 'right', 'right');
//    $aligns2 = array('center',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++) {
        $myrow = get_po($i);
        $user =get_user_id_time209111($myrow['order_no'],ST_PURCHORDER);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        if ($email == 1) {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('PURCHASE ORDER');
            $rep->filename = "PurchaseOrder" . $i . ".pdf";
        }
        $rep->SetHeaderType('Header2093');
        $rep->currency = $cur;
        $rep->Font();

        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
        $rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
        $rep->NewPage();

        $result = get_po_details11($i);
        $SubTotal = 0;
        $items = $prices = array();


        while ($myrow2 = db_fetch($result)) {
            $data = get_purchase_data($myrow['supplier_id'], $myrow2['item_code']);
            if ($data !== false) {
//                if ($data['supplier_description'] != "")
//                    $myrow2['description'] = $data['supplier_description'];
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
            
            global $db_connections;
            
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='FIDA' ){
                
                            $myrow4 = db_fetch(get_tax_rate_fida());

                
            }
            else{
                
                
                            $myrow4 = db_fetch(get_tax_rate_2());

            }
	
            
            
            
            
            $dec2 = 0;
            $DisplayQty = number_format2($myrow2["quantity_ordered"], get_qty_dec($myrow2['item_code']));
            $DisplayQty2 += $myrow2["quantity_ordered"];
            $gross_amt = round($myrow2["quantity_ordered"] * $myrow2["unit_price"]);
            $gross_amt2 = ($DisplayQty * $myrow2["unit_price"]);
            $rate = number_format2($myrow2['unit_price'], get_qty_dec($myrow2['item_code']));
            // if($myrow['tax_group_id'] == 1 &&  $myrow2['tax_type_id'] == 2)
            $rate1 = ($myrow3['rate']) / 100;
            $rate2 = ($myrow4['rate']) / 100;
            $sales_tax_amount = round($rate2 * $gross_amt);
            $sales_tax_amount3 = ($rate1 * $gross_amt2);
            $sales_tax_amount2 = (($rate2 + $gross_amt));
            //else
            $sale_tax += $sales_tax_amount;
//            $sale_tax1 = round2($sale_tax, (get_qty_dec($myrow2['item_code'])));
            //  $sales_tax_amount = 0;
            $grosstot += $gross_amt;
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
            $rep->TextCol(2, 3, $DisplayQty, -2);
//            $rep->TextCol(3, 4,	$DisplayQty, -2);
            $rep->TextCol(3, 4, $myrow2['units'], -2);
            $rep->TextCol(4, 5, $rate, -2);
            $rep->TextCol(5, 6, price_format($gross_amt) , -2);
            if ($myrow['tax_group_id'] == 1) {
                $rep->TextCol(6, 7, $myrow4['rate'] . "%", -2);
                $rep->TextCol(7, 8, price_format($sales_tax_amount) , -2);
                $rep->TextCol(8, 9, price_format($net_amt) , -2);

            } else
                $rep->TextCol(8, 9, price_format($gross_amt), -2);
            //$rep->TextCol(9, 10, $qoh, -2);
//            $rep->TextCol(8, 9,$myrow4['rate'] ."%"	, -2);
//            $rep->TextCol(9, 10,$sales_tax_amount2	, -2);
            // $rep->multicell(38, 322, "", 1, 'L', 0, 0, 40, 250);
            


            $break =print '<br />';
//            if($myrow2['text1'] != '')
            $fno = get_item_name($myrow2['item_code']);

            $desc = $fno."\n".$myrow2['text1'];
//            else
//                $fno = get_item_name($myrow2['item_code']);

//            $description =explode('\n',$myrow2['description']);
//            $strText = str_replace("/n", $break, $fno);
            $rep->TextColLines(1, 2, $desc);
//            if($myrow2['text1'] != '')
//                $rep->TextCollines(1, 2, $myrow2['text1'], -2);
//            $rep->NewLine(1);
//            $rep->NewLine(+3);
//            $purpose = get_purpose($_POST['requisition_detail_id']);
//            if ($myrow2['purpose'] != "")
//            {
//                $rep->NewLine();
//                $rep->TextCollines(1, 2, $myrow2['purpose'], -2);
//            }
//            $rep->NewLine();

            if ($rep->row < $rep->bottomMargin + ( $rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 49.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-106,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-106, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-160,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-160, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-197,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-197, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-250,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-250, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-289,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-289, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-370,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-370, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }
        }
        $rep->LineTo($rep->leftMargin, 49.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  49.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-106,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-106, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-160,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-160, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-197,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-197, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-250,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-250, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-289,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-289, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-322,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-322, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-370,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-370, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-488,  49.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-488, $rep->row);
        $rep->Line($rep->row);
//        $rep->row = $rep->bottomMargin + (30 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;
//        if ($myrow['comments'] != "") {
//            $rep->NewLine();
////            $rep->TextColLines(1, 5, $myrow['comments'], -2);
//        }
        $DisplaySubTot = number_format2($SubTotal, $dec);

//        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
//        $doctype = ST_PURCHORDER;

//        $rep->TextCol(3, 6, _("Sub-total"), -2);
//        $rep->TextCol(6, 7,	$DisplaySubTot, -2);
        $rep->NewLine();

//        $tax_items = get_tax_for_items($items, $prices, 0,
//            $myrow['tax_group_id'], $myrow['tax_included'], null);
//        $first = true;
        /*foreach ($tax_items as $tax_item) {
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
//                    $rep->TextCol(3, 6, $tax_type_name, -2);
//                    $rep->TextCol(6, 7,	$DisplayTax, -2);
                    $first = false;
                } else
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            } else {
                $SubTotal += $tax_item['Value'];
//                $rep->TextCol(3, 6, $tax_type_name, -2);
//                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->NewLine();

        }*/

//        $rep->NewLine(+8);
        $DisplayTotal = number_format2($SubTotal, $dec);
        $rep->Font('bold');
//        $rep->TextCol(2, 5, _("TOTAL AMOUNT"), - 2);
//        $rep->TextCol(5, 6,	number_format2($grosstot,(get_qty_dec($myrow2['item_code']))), -2);
//        $rep->TextCol(7, 8,	number_format2($sale_tax,(get_qty_dec($myrow2['item_code']))), -2);
//        $rep->TextCol(8, 9,	number_format2($net_amt_tot,(get_qty_dec($myrow2['item_code']))), -2);

        $words = price_in_words($SubTotal, ST_PURCHORDER);
        /*if ($words != "") {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, -2);
        }*/
        $rep->Font();
        if ($email == 1) {
            $myrow['DebtorName'] = $myrow['supp_name'];

            if ($myrow['reference'] == "")
                $myrow['reference'] = $myrow['order_no'];
            $rep->End($email);
        }


        $rep->Font('bold');
        $rep->TextCol(0, 2, " TOTAL AMOUNT:" , -2);
        $rep->TextCol(5, 6, price_format($grosstot));
        $rep->TextCol(2, 3, number_format2($DisplayQty2, get_qty_dec($myrow2['item_code'])));
//    $rep->multicell(80, 5, " TOTAL AMOUNT:", 0, 'L', 0, 0, 40, 575);
//    $rep->multicell(85, 5, price_format($grosstot), 0, 'R', 0, 0, 280, 575);
//    $rep->multicell(85, 5,number_format2($DisplayQty2, get_qty_dec($myrow2['item_code'])), 0, 'R', 0, 0, 155, 575);

        if ($myrow['tax_group_id'] == 1)
        {
            $rep->TextCol(7, 8, price_format($sale_tax));
            $rep->TextCol(8, 9, price_format($net_amt_tot));
//        $rep->multicell(90, 5, price_format($sale_tax)."12", 0, 'R', 0, 0, 365, 575);
//        $rep->multicell(102, 5, price_format($net_amt_tot)."454", 0, 'R', 0, 0, 460, 575);
            $amount_in_words = _number_to_words($net_amt_tot);
//        $rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 595);

        }
        else
        {

//        $rep->multicell(102, 5, price_format($grosstot), 0, 'R', 0, 0, 460, 575);
            $amount_in_words = _number_to_words($grosstot);
            $rep->TextCol(8, 9, price_format($grosstot));
//        $rep->TextCol(2, 9, "Rupees ".$amount_in_words." Only");
//        $rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 595);
        }
        $rep->Font('');
        $rep->NewLine(2);
        $rep->font('b');
        $rep->TextCol(0, 9, "  Amount in words:" ." "."Rupees ".$amount_in_words." Only", -2);

//    $rep->multicell(85,15,"  Amount in words:",0,'L',0,0,40,595);
        $rep->font('');

        $rep->NewLine(2);
//    $rep->TextCol(8, 9,	number_format2($net_amt_tot,(get_qty_dec($myrow2['item_code']))), -2);
//    $rep->multicell(73,15," NET AMOUNT:",0,'L',0,0,295,585);
        $payment_terms = "Payment Terms:";
        $Note = "Note:";
        $Shipping_Instruction = "Shipping Instruction:";
        $Invoice_To = "Invoice To:";
        $Remarks = "Remarks:";
        $rep->TextColLines(0, 9, $payment_terms." ".$myrow['Comments1'], -2);

        $rep->TextColLines(0, 9, $Note." ".$myrow['Comments2'], -2);

        $rep->TextColLines(0, 9, $Shipping_Instruction." ".$myrow['Comments3'], -2);

        $rep->TextColLines(0, 9, $Invoice_To." ".$myrow['Comments4'], -2);

        $rep->TextColLines(0, 9, $Remarks." ".$myrow['Comments'], -2);

        $rep->NewLine(3);
        $rep->TextCol(0, 2, get_user_name_209111($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp'])), -2);
        $rep->NewLine();
        $rep->TextCol(0, 2, "________________________", -2);
        $rep->TextCol(2, 5, "________________________", -2);
        $rep->TextCol(5, 8, "________________________", -2);
        $rep->NewLine();
        $rep->TextCol(0, 2, "              Prepared by", -2);
        $rep->TextCol(2, 5, "Checked by               ", -2);
        $rep->TextCol(5, 8, "Authorised by            ", -2);
//    $rep->multicell(85,13,"Payment Terms:",0,'L',0,0,40,610);
//    $rep->multicell(85,13,"Note:",0,'L',0,0,40,640);
//    $rep->multicell(85,13,"Shipping Instruction:",0,'L',0,0,40,692);
//    $rep->multicell(85,13,"Invoice To:",0,'L',0,0,40,710);
//    $rep->multicell(85,13,"Remarks:",0,'L',0,0,40,760);
        $rep->setfontsize(+7);
//    $rep->multicell(440,30,$myrow['Comments1'],0,'L',0,0,125,610);
//    $rep->multicell(440,50,$myrow['Comments2'],0,'L',0,0,125,640);
//    $rep->multicell(440,18,$myrow['Comments3'],0,'L',0,0,125,692);
//    $rep->multicell(440,50,$myrow['Comments4'],0,'L',0,0,125,710);
//    $rep->multicell(440,30,$myrow['comments'],0,'L',0,0,125,760);

//    $rep->MultiCell(225, 60,get_user_name_209111($user['user'])." ".  sql2date(date("Y-m-d", $user['unix_stamp']))/*." ".  date("H:i:s", $user['unix_stamp'])*/, 0, 'L', 0, 2, 45,810, true);
//
//    $rep->multicell(150,15,"________________________",0,'L',0,0,40,820);
//    $rep->multicell(150,15,"Prepared by",0,'L',0,0,65,830);
//    $rep->multicell(150,15,"________________________",0,'L',0,0,200,820);
//    $rep->multicell(150,15,"Checked by",0,'L',0,0,230,830);
//    $rep->multicell(150,15,"________________________",0,'L',0,0,350,820);
//    $rep->multicell(150,15,"Authorised by",0,'L',0,0,380,830);







    }

    if ($email == 0)
        $rep->End();
}

?>