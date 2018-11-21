<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
	'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Orders
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

function get_load_sheet($ship_no ,$date_from ,$date_to,$orders_no)
{
    $date_from = date2sql($date_from);
    $date_to = date2sql($date_to);
    $sql = "SELECT " . TB_PREF . "sales_order_details.stk_code," . TB_PREF . "sales_order_details.description,
    " . TB_PREF . "sales_order_details.bonus," . TB_PREF . "sales_orders.ship_via,
    " . TB_PREF . "sales_order_details.order_no, " . TB_PREF . "sales_orders.order_no
    FROM " . TB_PREF . "sales_order_details
    LEFT JOIN " . TB_PREF . "sales_orders ON " . TB_PREF . "sales_order_details.order_no=" . TB_PREF . "sales_orders.order_no
    WHERE  " . TB_PREF . "sales_orders.trans_type= '30'
    AND " . TB_PREF . "sales_orders.ship_via=" . db_escape($ship_no) . "
    AND " . TB_PREF . "sales_orders.ord_date >= '$date_from'
    AND  " . TB_PREF . "sales_orders.ord_date <= '$date_to'
";
    $sql .= " GROUP BY " . TB_PREF . "sales_order_details.stk_code ";
    return db_query($sql, "Retreive order Line Items");
}
function get_packing($stock_id)
{
    $sql = "SELECT carton
    FROM ".TB_PREF."stock_master
    WHERE stock_id = ".db_escape($stock_id);
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}

function get_shipping_through($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_order_no($stk_code ,$date_from ,$date_to,$orders_no){
    $date_from = date2sql($date_from);
    $date_to = date2sql($date_to);
    $sql = "SELECT SUM(" . TB_PREF . "sales_order_details.quantity) as quantity,
    " . TB_PREF . "sales_orders.salesman,  " . TB_PREF . "sales_order_details.bonus
    FROM " . TB_PREF . "sales_order_details
    LEFT JOIN " . TB_PREF . "sales_orders ON " . TB_PREF . "sales_order_details.order_no=" . TB_PREF . "sales_orders.order_no
    WHERE  " . TB_PREF . "sales_orders.trans_type= '30'
    AND " . TB_PREF . "sales_order_details.stk_code=" . db_escape($stk_code) . "
    AND " . TB_PREF . "sales_orders.ord_date>='$date_from'
    AND  " . TB_PREF . "sales_orders.ord_date<='$date_to'
";
    $sql .= " GROUP BY " . TB_PREF . "sales_order_details.stk_code ";
    $result = db_query($sql,"an item could not be retreived");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_salesmann($ord_no)
{
    $sql = "SELECT sales.*, salesman.salesman_name 
		FROM "
        .TB_PREF."sales_orders sales,"
        .TB_PREF."salesman salesman
		WHERE sales.salesman=salesman.salesman_code 
			AND order_no=".db_escape($ord_no);

    $result = db_query($sql, "Cannot retreive a customer branch");

    return db_fetch($result);
}

print_sales_orders();

function print_sales_orders()
{
	global $path_to_root, $SysPrefs,$db_connections;

if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RPL2'){
     include_once($path_to_root . "/reporting/includes/pdf_report_email.inc");


}
else{
	 	include_once($path_to_root . "/reporting/includes/pdf_report.inc");
}
	$from = $_POST['PARAM_0'];
	$date_to = $_POST['PARAM_1'];
    $date_from = $_POST['PARAM_2'];
	$currency = $_POST['PARAM_3'];
	$email = $_POST['PARAM_4'];
	$print_as_quote = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];

	//if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();
$myrow233 = get_company_item_pref('con_factor');
$pref = get_company_prefs();

	$cols = array(4, 31, 160, 213, 255, 310, 270, 382,470, 515);
    $cols2 = array(4, 31, 160, 213, 255, 310,270, 382,470, 515);
	// $headers in doctext.inc
	$aligns = array('left',	'left',	'left', 'left','left', 'left', 'left', 'left', 'left', 'left');
    $aligns2 = array('left',	'left',	'left', 'left','left', 'left', 'left', 'left', 'left', 'left');


    $params = array('comments' => $comments, 'print_quote' => $print_as_quote);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
    {

        if ($print_as_quote == 0)
            $rep = new FrontReport(_("Load Sheet"), "SalesOrderBulk", user_pagesize(), 9, $orientation);
        else
            $rep = new FrontReport(_("QUOTE"), "QuoteBulk", user_pagesize(), 9, $orientation);
    }
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $from; $i++)
    {
        $myrow = get_sales_order_header($i, ST_SALESORDER);
        $myrow4 =get_shipping_through($i);
        if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
            continue;
        }
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_salesmann($myrow["branch_code"]);
        if ($email == 1)
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
        $rep->SetHeaderType('Header120');
        $rep->currency = $cur;
        $rep->Font();
        if ($print_as_quote == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            if ($print_as_quote == 1)
            {
                $rep->title = _('Load Sheet');
                $rep->filename = "Load Sheet" . $i . ".pdf";
            }
            else
            {
                $rep->title = _("Load Sheet");
                $rep->filename = "LoadSheet" . $i . ".pdf";
            }
        }
        else
            $rep->title = ($print_as_quote==1 ? _("Load Sheet") : _("Load Sheet"));
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns, $cols2, null, $aligns2);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
        $rep->SetHeaderType('Header120');
        $rep->NewPage();

        $result = get_load_sheet($from ,$date_to ,$date_from);
        $SubTotal = 0;
        $ToatlQty =0;
        $Schem=0;
        $items = $prices = array();
        $s=1;
        while ($myrow2=db_fetch($result))
        {
            $packing=get_packing($myrow2['stk_code'],$date_to ,$date_from);
            $totalqty= get_order_no($myrow2['stk_code'],$date_to ,$date_from);
                $issue= number_format2($totalqty/$packing , $dec);
           // $rep->Amountcol(5, 6, $myrow2['bonus'], -2);
            $rep->TextCol(0, 1,	$s++, -2);
            $rep->Amountcol(2, 3, $packing, -2);
          // $rep->Amountcol(4, 5, get_order_no($myrow2['stk_code'],$date_to ,$date_from), -2);
            $rep->TextCol(3, 4,$issue, 1);
            $rep->TextColLines(1, 2, $myrow2['description'], -2);




            $item=get_item($myrow2['stk_code']);
            $pref = get_company_prefs();

            if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
                $qty=$myrow2['quantity'] * $myrow2['con_factor'];
            else
                $qty=$myrow2['quantity'];

            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
                user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $ToatlQty += $issue;
            $Schem +=  $myrow2['bonus'];
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($qty,get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";

            $oldrow = $rep->row;

//            display_error(get_loads_sheet($myrow2['quantity'])."ututyu");



            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $item=get_item($myrow2['stk_code']);
                $pref = get_company_prefs();


               // $rep->Amountcol(4, 5,	$qty ,$dec);


//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                }
                else
                {
                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
                }
                $myrow233 = get_company_item_pref('con_factor');
                $pref = get_company_prefs();
                if($pref['alt_uom'] == 1  && $myrow233['sale_enable'] == 1){

//                    $rep->Amountcol(5, 6,	$myrow2['con_factor'], $dec);

                    if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
                        $rep->TextCol(6, 7, $DisplayPrice, -2);
                       // $rep->TextCol(7, 8, $DisplayDiscount, -2);
                        $rep->TextCol(8, 9, $DisplayNet, -2);
                    }
                }
                else{

                    if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
                        //$rep->TextCol(5, 6, $DisplayPrice, -2);
                        $rep->TextCol(6, 7, $DisplayDiscount, -2);
                      //  $rep->TextCol(7, 8, $DisplayNet, -2);
                    }
                }



            }

            $rep->row = $newrow;
//            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//                $rep->NewPage();
            if ($rep->row < $rep->bottomMargin + ( $rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 54.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.3 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-78,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-78, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-170,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-170, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-256,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-256, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-317,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-317, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-368,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-368, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-497,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-497, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }


        }
        $rep->LineTo($rep->leftMargin, 54.3 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  54.3 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-78,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-78, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-170,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-170, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-256,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-256, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-317,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-317, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-368,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-368, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-497,  54.3 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-497, $rep->row);
        $rep->Line($rep->row);
        $rep->Line($rep->row);
        $rep->NewLine();

        ///TOTAL
        $rep->Font('bold');
        $rep->TextCol(2, 3, _("Total :"), -2);
        $rep->TextCol(3, 4 , $ToatlQty,-2);
//        $rep->TextCol(5, 6 , $Schem,-2);
        $rep->Font('');
        /////
        $rep->MultiCell(105,16,$myrow['shipper_name'],0,'C', 0, 2,339,103,true);

        $DisplaySubTot = number_format2($SubTotal,$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESORDER;
        $myrow3 = get_company_item_pref('con_factor');

        if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
            if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
//                $rep->TextCol(1, 2, _("Sub-total"), -2);
//                $rep->TextCol(9, 10, $DisplaySubTot, -2);
            } else {
               // $rep->TextCol(2, 3, _("Sub-total"), -2);
                //$rep->TextCol(7, 8, $DisplaySubTot, -2);
            }
            $rep->NewLine();
            if ($myrow['freight_cost'] != 0.0) {
                $DisplayFreight = number_format2($myrow["freight_cost"], $dec);
                $rep->TextCol(4, 7, _("Shipping"), -2);
               // $rep->TextCol(7, 8, $DisplayFreight, -2);
                $rep->NewLine();
            }
            $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
            if ($myrow['tax_included'] == 0) {
                if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                    $rep->TextCol(5, 7, _("TOTAL ORDER EX GST"), -2);
                    $rep->TextCol(8, 9, $DisplayTotal, -2);
                } else {
                    $rep->TextCol(4, 7, _("TOTAL ORDER EX GST"), -2);
                  $rep->TextCol(7, 8, $DisplayTotal, -2);
                }
                $rep->NewLine();
            }

            $tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
                $myrow['tax_group_id'], $myrow['tax_included'], null);
            $first = true;
            foreach ($tax_items as $tax_item) {
                if ($tax_item['Value'] == 0)
                    continue;
                $DisplayTax = number_format2($tax_item['Value'], $dec);

                $tax_type_name = $tax_item['tax_type_name'];

                if ($myrow['tax_included']) {
                    if ($SysPrefs->alternative_tax_include_on_docs() == 1) {
                        if ($first) {
                            $rep->TextCol(4, 7, _("Total Tax Excluded"), -2);
                           $rep->TextCol(7, 8, number_format2($sign * $tax_item['net_amount'], $dec), -2);
                            $rep->NewLine();
                        }
                        if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                            $rep->TextCol(5, 7, $tax_type_name, -2);
                            $rep->TextCol(7, 8, $DisplayTax, -2);
                        } else {
                            $rep->TextCol(4, 7, $tax_type_name, -2);
                          $rep->TextCol(7, 8, $DisplayTax, -2);
                        }
                        $first = false;
                    } else
                        $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
                } else {
                    $SubTotal += $tax_item['Value'];
                    if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                        $rep->TextCol(5, 7, $tax_type_name, -2);
                       $rep->TextCol(8, 9, $DisplayTax, -2);
                    } else {
                        $rep->TextCol(4, 7, $tax_type_name, -2);
                        $rep->TextCol(7, 8, $DisplayTax, -2);
                    }
                }
                $rep->NewLine();
            }

            $rep->NewLine();

            $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
            $rep->Font('bold');
            if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                $rep->TextCol(5, 7, _("TOTAL ORDER GST INCL.") . ' ' . $rep->formData['curr_code'], -2);
                $rep->TextCol(8, 9, $DisplayTotal, -2);
            } else {
                $rep->TextCol(4, 7, _("TOTAL ORDER GST INCL.") . ' ' . $rep->formData['curr_code'], -2);
              $rep->TextCol(7, 8, $DisplayTotal, -2);
            }
        }

        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }

        $rep->Font();



        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    if ($email == 0)
        $rep->End();
}

