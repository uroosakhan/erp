<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Janusz Dobrwolski
// date_:	2008-01-14
// Title:	Print Delivery Notes
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_deliveries();

//----------------------------------------------------------------------------------------------------

function get_user_name_11019($user_id)
{
   $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_delivery_location($trans_no)
{
	$sql="SELECT loc_code FROM ".TB_PREF."stock_moves WHERE trans_no=".db_escape($trans_no)." AND type = 13";
	$result = db_query($sql,"a location could not be retrieved");
    $row = db_fetch_row($result);
	return $row['0'];
}

function get_delivery_location_name ($loc_code)
{
	$sql="SELECT location_name  FROM ".TB_PREF."locations WHERE loc_code=".db_escape($loc_code)." ";
	$result = db_query($sql,"a location could not be retrieved");
    $row = db_fetch_row($result);
	return $row['0'];
}
function print_deliveries()
{
    global $path_to_root, $SysPrefs;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $email = $_POST['PARAM_2'];
    $packing_slip = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(1, 35,100,270, 260,400,460);
    // $headers in doctext.inc
    $aligns = array('center',	'left',	'left',	'left',	'right','right','right');
    $headers = array(_("S.No"), _(" Item Code"), _("Item Description"),_("Location"), _(" Packing(Pcs)"), _(" Qty in ctn"),
        _(" Qty In Pack"));
    $params = array('comments' => $comments, 'packing_slip' => $packing_slip);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
    {
        if ($packing_slip == 0)
            $rep = new FrontReport(_('DELIVERY'), "DeliveryNoteBulk", user_pagesize(), 8, $orientation);
        else
            $rep = new FrontReport(_('PACKING SLIP'), "PackingSlipBulk", user_pagesize(), 8, $orientation);
    }
    if ($orientation == 'L')
        recalculate_cols($cols);
    for ($i = $from; $i <= $to; $i++)
    {
        if (!exists_customer_trans(ST_CUSTDELIVERY, $i))
            continue;
        $myrow = get_customer_trans($i, ST_CUSTDELIVERY);
        $branch = get_branch($myrow["branch_code"]);
        $sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER); // ?
        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 10, $orientation);
            if ($packing_slip == 0)
            {
                $rep->title = _('DELIVERY NOTE');
                $rep->filename = "Delivery" . $myrow['reference'] . ".pdf";
            }
            else
            {
                $rep->title = _('PACKING SLIP');
                $rep->filename = "Packing_slip" . $myrow['reference'] . ".pdf";
            }
        }
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $headers, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'delivery', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $sales_order, '', ST_CUSTDELIVERY, $contacts);
        $rep->SetHeaderType('Header11019');
        $rep->NewPage();

        $result = get_customer_trans_details(ST_CUSTDELIVERY, $i);
        $SubTotal = 0;
        $s_no=0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            $s_no++;
            $rep->TextCol(0, 1,	$s_no, -2);
            $rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            $rep->TextCol(3, 4,get_delivery_location_name($myrow2['item_location']) , -2);
            if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $pref = get_company_pref();
                $item=get_item($myrow2['stock_id']);
                if($myrow2['units_id']=='pack'){
                    // $loc1=get_delivery_location($myrow['trans_no']);

                    // $rep->TextCol(3, 4,get_delivery_location_name($loc1)	, -2);
                    $rep->TextCol(6, 7,$DisplayQty	, -2);
                    $carton=$myrow2["quantity"]/$item["con_factor"];
                    $rep->TextCol(5, 6,number_format2($carton)	, -2);
                    $total_cartons +=$carton;
                    $total_packing +=$myrow2["quantity"];
                }
                else{
                    // $rep->TextCol(3, 4,$myrow2['item_location']	, -2);
                    $cartons=$myrow2["quantity"]*$item["con_factor"];
                    $rep->TextCol(5, 6,$cartons	, -2);
                    $rep->TextCol(6, 7,$DisplayQty	, -2);
                    $total_cartons +=$myrow2["quantity"];
                    $total_packing +=$cartons;
                }
                $rep->TextCol(4, 5,$item['carton']	, -2);
                $qty_in_pack +=$item['carton'];
            }
            $rep->row = $newrow;

            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }


        ////////////////////second

//display_error($s_no);
if($s_no >= 2) {
    $rep->NewLine(36 - $s_no);
}
else{
    $rep->NewLine(35);
}

        $result1 = get_customer_trans_details(ST_CUSTDELIVERY, $i);
        $SubTotal = 0;
        $s_no=0;
        while ($myrow2=db_fetch($result1))
        {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            $s_no++;
            $rep->TextCol(0, 1,	$s_no, -2);
            $rep->TextCol(1, 2,	$myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            $rep->TextColLines(2, 3, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            $rep->TextCol(3, 4,get_delivery_location_name($myrow2['item_location']) , -2);

            if ($Net != 0.0  || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $pref = get_company_pref();
                $item=get_item($myrow2['stock_id']);
                if($myrow2['units_id']=='pack'){
                    // $loc1=get_delivery_location($myrow['trans_no']);

                    // $rep->TextCol(3, 4,get_delivery_location_name($loc1)	, -2);
                    $rep->TextCol(6, 7,$DisplayQty	, -2);
                    $carton=$myrow2["quantity"]/$item["con_factor"];
                    $rep->TextCol(5, 6,number_format2($carton)	, -2);
                    $total_cartons1 +=$carton;
                    $total_packing1 +=$myrow2["quantity"];
                }
                else{
                    // $rep->TextCol(3, 4,$myrow2['item_location']	, -2);
                    $cartons=$myrow2["quantity"]*$item["con_factor"];
                    $rep->TextCol(5, 6,$cartons	, -2);
                    $rep->TextCol(6, 7,$DisplayQty	, -2);
                    $total_cartons1 +=$myrow2["quantity"];
                    $total_packing1 +=$cartons;
                }
                $rep->TextCol(4, 5,$item['carton']	, -2);
                $qty_in_pack +=$item['carton'];
            }
            $rep->row = $newrow;
//            if($s_no >= 2) {
//                $rep->NewLine(-35 - $s_no);
//            }
//            else{
//                $rep->NewLine(-33);
//            }
            if ($rep->row < $rep->bottomMargin + (8 * $rep->lineHeight))
                $rep->NewPage();
        }

//        $rep->NewLine(-21);






        ////////////////////////end second
//        $memo = get_comments_string(ST_CUSTDELIVERY, $i);
//        if ($memo != "")
//        {
//            $rep->NewLine();
//            $rep->TextColLines(1, 5, $memo, -2);
//        }

        $DisplaySubTot = number_format2($SubTotal,$dec);

//        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype=ST_CUSTDELIVERY;
        if ($packing_slip == 0)
        {
            //$rep->TextCol(3, 6, _("Sub-total"), -2);
            //$rep->TextCol(5, 6,	$DisplaySubTot, -2);
//            $rep->NewLine();
            $tax_items = get_trans_tax_details(ST_CUSTDELIVERY, $i);
            $first = true;

            $DisplayTotal = number_format2($myrow["ov_freight"] +$myrow["ov_freight_tax"] + $myrow["ov_gst"] +
                $myrow["ov_amount"],$dec);
            $rep->Font('bold');
//            $rep->NewLine(2);

            $rep->MultiCell(525, 30,"TOTAL", 0, 'L', 0, 2, 80,290, true); // 3
            $rep->MultiCell(525, 25,"", 1, 'L', 0, 2, 40,283, true); // 3
            $rep->MultiCell(100, 20, number_format2($total_packing), 0, 'R', 0, 2, 460,290, true);
            $rep->MultiCell(100, 10, number_format2($total_cartons), 0, 'R', 0, 2, 395,290, true);


///second
 $rep->MultiCell(525, 25,"", 1, 'L', 0, 2, 40,725, true); // second
            $rep->MultiCell(525, 30,"TOTAL", 0, 'L', 0, 2, 80,730, true); // second
            $rep->MultiCell(100, 20, number_format2($total_packing1), 0, 'R', 0, 2, 460,730, true);
            $rep->MultiCell(100, 10, number_format2($total_cartons1), 0, 'R', 0, 2, 395,730, true);


            $words = price_in_words($myrow['Total'], ST_CUSTDELIVERY);
            if ($words != "")
            {
//                $rep->NewLine(1);
                $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
            }
            $rep->Font();
        }
        if ($email == 1)
        {
            $rep->End($email);
        }
        if($myrow['type'] == 13)
        {
$rep->MultiCell(250, 188,"DC No:", 0, 'L', 0, 2, 48,87, true);
$rep->MultiCell(250, 188,"DC No:", 0, 'L', 0, 2, 48,515, true);

}else{
$rep->MultiCell(250, 188,"LT No:", 0, 'L', 0, 2, 48,87, true);
$rep->MultiCell(250, 188,"LT No:", 0, 'L', 0, 2, 48,515, true);

}
        $sales_order = get_sales_order_header($myrow['order_'], ST_SALESORDER);
        $rep->Font('b');
        $rep->MultiCell(770, 30, "Transporter name:", 0, 'L', 0,1,46,124, true);
        $rep->MultiCell(770, 30, "".$sales_order['f_text2'], 0, 'L', 0,1,143,124, true);
//        		$loc1=get_delivery_location($myrow['trans_no']);

//         $rep->MultiCell(770, 30, "Location : ".get_delivery_location_name($loc1), 0, 'L', 0,1,380,124, true);//second
        $rep->MultiCell(770, 30, "Remarks:", 0, 'L', 0,1,40,310, true);
        $rep->MultiCell(770, 30, $rep->formData["comments"], 0, 'L', 0,1,100,310, true);
        $rep->MultiCell(770, 30, "Prepared By:", 0, 'L', 0,1,40,323, true);
        $rep->MultiCell(770, 30, "Store Incharge:", 0, 'L', 0,1,40,349, true);
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,110,327, true);
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,110,349, true);
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,420,352, true);
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,425,323, true);
        $rep->MultiCell(770, 30, "Verified By:", 0, 'L', 0,1,357,327, true);
        $rep->MultiCell(770, 30, "Transporter:", 0, 'L', 0,1,357,352, true);



    $user =get_user_id($myrow['trans_no'],ST_CUSTDELIVERY);
    $rep->MultiCell(200, 25, "".get_user_name_11019($user) ,0, 'L', 0, 2, 120,805, true);
  $rep->MultiCell(200, 25, "".get_user_name_11019($user) ,0, 'L', 0, 2, 120,323, true);
  
  
  
  
        $rep->MultiCell(770, 30, "Transporter name:", 0, 'L', 0,1,47,550, true);//second
        $rep->MultiCell(770, 30, "".$sales_order['f_text2'], 0, 'L', 0,1,143,550, true);//second
//        		$loc=get_delivery_location($myrow['trans_no']);

//         $rep->MultiCell(770, 30, "Location : ".get_delivery_location_name($loc), 0, 'L', 0,1,380,550, true);//second
        $rep->MultiCell(770, 30, "Remarks:", 0, 'L', 0,1,40,760, true);//second
        $rep->MultiCell(770, 30, $rep->formData["comments"], 0, 'L', 0,1,100,760, true);//second
        $rep->MultiCell(770, 30, "Prepared By:", 0, 'L', 0,1,40,805, true);//second
        $rep->MultiCell(770, 30, "Store Incharge:", 0, 'L', 0,1,40,780, true);//second
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,110,805, true);//second
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,110,780, true);//second
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,425,780, true);//second
        $rep->MultiCell(770, 30, "________________________", 0, 'L', 0,1,425,805, true);//second
        $rep->MultiCell(770, 30, "Verified By:", 0, 'L', 0,1,357,780, true);//second
        $rep->MultiCell(770, 30, "Transporter:", 0, 'L', 0,1,357,805 , true);//second




    }
    if ($email == 0)
        $rep->End();
}

