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
function get_pr($order_no)
{
    $sql = "SELECT ".TB_PREF."requisitions.*, ".TB_PREF."suppliers.supplier_id,".TB_PREF."suppliers.supp_name,  "
        .TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms, ".TB_PREF."locations.location_name,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, 
   	    ".TB_PREF."suppliers.tax_group_id, ".TB_PREF."suppliers.gst_no, ".TB_PREF."suppliers.ntn_no
		FROM ".TB_PREF."requisitions, ".TB_PREF."requisition_details, ".TB_PREF."suppliers, ".TB_PREF."locations
		WHERE ".TB_PREF."requisition_details.requisition_id = ".TB_PREF."requisitions.requisition_id 
		AND ".TB_PREF."suppliers.supplier_id = ".TB_PREF."requisition_details.supplier_id 
		AND ".TB_PREF."requisitions.requisition_id = ".db_escape($order_no);
    $result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

//----------------------------------------------------------------------------------------------------
function get_pr_header($order_no)
{
    $sql = "SELECT ".TB_PREF."requisitions.*
		FROM ".TB_PREF."requisitions
		WHERE ".TB_PREF."requisitions.requisition_id = ".db_escape($order_no);
    $result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_pr_details($order_no)
{
    $sql = "SELECT ".TB_PREF."requisition_details.*, units,".TB_PREF."stock_master.description
	,".TB_PREF."stock_master.tax_type_id
		FROM ".TB_PREF."requisition_details
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."requisition_details.item_code=".TB_PREF."stock_master.stock_id
		WHERE requisition_id =".db_escape($order_no)." ";
    $sql .= " ORDER BY requisition_detail_id";
    return db_query($sql, "Retreive order Line Items");
}
function get_tax_rate_1()
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
function get_quantity_order($item_code)
{
//    $sql = "SELECT qty_invoiced, unit_price, delivery_date
//    FROM ".TB_PREF."purch_order_details
//    WHERE item_code = ".db_escape($item_code)
//    ;
//    $sql .= " ORDER BY order_no DESC LIMIT 1";
//    $result = db_query($sql, "could not get qty");
//
//    return db_fetch($result);

$sql = " SELECT qty_invoiced, unit_price, delivery_date
 FROM ".TB_PREF."purch_order_details
WHERE order_no NOT IN ( SELECT Max(order_no)
 
 FROM ".TB_PREF."purch_order_details WHERE
    item_code=".db_escape($item_code)."  )
    
    
 AND item_code=".db_escape($item_code)."  
    ";
$sql .= "  ORDER BY order_no DESC LIMIT 1 ";
        $result = db_query($sql, "could not get sales type");

        $row = db_fetch($result);
        return $row;
}

function get_user_realname($user_id)
{
    $sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
//function get_tax_rate_2()
//{
//    $sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
//	 WHERE ".TB_PREF."tax_types.id = 2";
//    $result = db_query($sql, 'error');
//    return $result;
//}
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

    $orientation = ( 'L' );
    $dec = user_price_dec();

//    $cols = array(0, 30,100,115, 135, 165, 200, 230,270,310,360,420,490);
    $cols2 = array(0, 50,170,220, 250,300, 360, 400,450,520,570,630,700);
//    $cols2 = array(0, 10/*,160,190, 230, 300, 370, 430,470*/);

    // $headers in doctext.inc
//    $aligns = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');
    $aligns2 = array('left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right');
//    $aligns2 = array('center',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('PURCHASE REQUISITION'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {
        $myrow = get_pr($i);
        $GetHeader_details = get_pr_details($i); // for header
        $header_in_detials = db_fetch($GetHeader_details);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
//
        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('PURCHASE REQUISITION');
            $rep->filename = "PurchaseOrder" . $i . ".pdf";
        }
        $rep->SetHeaderType('Header207');
        $rep->currency = $cur;
        $rep->Font();
//	$rep->Info($params, $cols, $headers, $aligns, $cols2, $headers2, $aligns2);

        $rep->Info($params, $cols2, null, $aligns2/*, $cols2, null, $aligns2*/);
//
        $contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
        $rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts, $header_in_detials);
        $rep->NewPage();
//
        $result = get_pr_details($i);
        $SubTotal = 0;
        $items = $prices = array();
        $gst_nos = get_pr_header($i);
        $serial = 0;
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
            $Net1 = $myrow2["unit_price"] * $myrow2["quantity"];
            $prices[] = $Net;
            $items[] = $myrow2['item_code'];
            $SubTotal += $Net;
            $am_inwords +=$Net1;
            $myrow3 = db_fetch(get_tax_rate_1());
//            $myrow4 = db_fetch(get_tax_rate_2());
            $dec2 = 0;
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['item_code']));
            $DisplayPrice = price_decimal_format($myrow2["quantity"]*$myrow2["price"],$dec2);
            $Gross_Amount= round($myrow2['price']*$myrow2["quantity"]);
            $Gross_Amount1= $myrow2['price']*$myrow2["quantity"];
            $gross_total += $Gross_Amount;
            global $db_connections;
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CHI')
            {
            if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] == 1)
            {
                $sales_tax_amount =  round(($myrow3['rate']/100)*$Gross_Amount1);;
                $sales_tax_amount1 = ($myrow3['rate']/100)*$Gross_Amount1;
                $sale_tax_tot += $sales_tax_amount;
            }
            }
            else
            {
              if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] == 2)
            {
                $sales_tax_amount =  round(($myrow3['rate']/100)*$Gross_Amount1);;
                $sales_tax_amount1 = ($myrow3['rate']/100)*$Gross_Amount1;
                $sale_tax_tot += $sales_tax_amount;
            }  
            }
            $tax_amt_tot += $sales_tax_amount;
//                $sales_tax_amount2 = (($myrow4['rate']*$Gross_Amount)/100);
            //else
            //  $sales_tax_amount = 0;
            $grosstot += $DisplayPrice;
            $DisplayNet = round($Gross_Amount1 + $sales_tax_amount1);
//            $DisplayNet1 = $Gross_Amount1 + $sales_tax_amount1;
            $net += $DisplayNet;
            // if ($show_po_item_codes) {
            $rep->TextCol(0, 1,	$myrow2['item_code'], -2);

            // } else
            $rate = number_format2($myrow2['price'],get_qty_dec($myrow2['item_code']));
            $qoh = get_qoh_on_date($myrow2['item_code'], null,null );
            //$rep->TextCol(0, 2,	$myrow2['description'], -2);
            $rep->TextCol(2, 3,	$DisplayQty, -2);
            $total_qty += $myrow2["quantity"];
//            $rep->TextCol(3, 4,	$DisplayQty, -2);
            $rep->TextCol(3, 4,	$myrow2['units'], -2);
            $rep->TextCol(4, 5,	$rate, -2);
            $rep->TextCol(5, 6,	price_format($Gross_Amount), -2);
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CHI')
            {
            if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] == 1)
                $rep->TextCol(6, 7,	$myrow3['rate'] ."%", -2);
            }
            else
            {
            if($myrow['tax_group_id'] == 1 && $myrow2['tax_type_id'] == 2)
                $rep->TextCol(6, 7,	$myrow3['rate'] ."%", -2);   
            }
            $rep->TextCol(7, 8,price_format($sales_tax_amount)	, -2);
//            $rep->TextCol(8, 9,$myrow4['rate'] ."%"	, -2);
//            $rep->TextCol(9, 10,$sales_tax_amount2	, -2);
            $rep->TextCol(8, 9,	price_format($DisplayNet), -2);
            $purch_values =get_quantity_order($myrow2['item_code']);
            $rep->TextCol(9, 10,number_format2($purch_values['qty_invoiced'],get_qty_dec($myrow2['item_code']))	, -2);
            $rep->TextCol(10, 11,	number_format2($purch_values['unit_price'],get_qty_dec($myrow2['item_code'])), -2);
            $rep->TextCol(11, 12,	sql2date($purch_values['delivery_date']), -2);
            $rep->AmountCol(12, 13,	$qoh, $dec);

            $fno = get_item_name($myrow2['item_code']);
            $desc = $fno."\n".$myrow2['purpose'];

            $rep->TextCollines(1, 2, $desc);
//            if ($myrow2['purpose'] != "")
//            {
////                $rep->NewLine();
//            $rep->TextCol(1, 2, $myrow2['purpose'], -2);
//            }
//            $rep->NewLine();

//            $rep->multicell(126,297,"",1,'L',0,0,87,148);
//            $rep->multicell(50,312,"",1,'L',0,0,208,133);
//            $rep->multicell(30,312,"",1,'L',0,0,258,133);
//            $rep->multicell(50,312,"",1,'L',0,0,288,133);
//            $rep->multicell(60,312,"",1,'L',0,0,338,133);
//            $rep->multicell(40,297,"",1,'L',0,0,398,148);
//            $rep->multicell(50,297,"",1,'L',0,0,438,148);
//            $rep->multicell(70,312,"",1,'L',0,0,488,133);
//            $rep->multicell(50,297,"",1,'L',0,0,558,148);
//            $rep->multicell(60,297,"",1,'L',0,0,608,148);
//            $rep->multicell(70,297,"",1,'L',0,0,668,148);
//            $rep->multicell(74,312,"",1,'L',0,0,738,133);
////
            $rep->multicell(172,42,"",1,'L',0,0,40,132);
            $rep->multicell(50,42,"",1,'L',0,0,212,132);
            $rep->multicell(30,42,"",1,'L',0,0,262,132);
            $rep->multicell(50,42,"",1,'L',0,0,292,132);
            $rep->multicell(60,42,"",1,'L',0,0,342,132);
            $rep->multicell(90,42,"",1,'L',0,0,402,132);
            $rep->multicell(70,42,"",1,'L',0,0,492,132);
            $rep->multicell(180,42,"",1,'L',0,0,562,132);
//            $rep->multicell(74,41,"",1,'L',0,0,738,133);

            if ($rep->row < $rep->bottomMargin + (4 * $rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 35 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-70,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-70, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-140,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-140, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-200,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-200, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-250,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-250, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-320,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-320, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-370,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-370, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-410,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-410, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-470,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-470, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-520,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-520, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-550,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-550, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-600,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-600, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-725,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-725, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  38.6 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }
        }

        $rep->LineTo($rep->leftMargin, 35 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-70,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-70, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-140,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-140, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-200,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-200, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-250,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-250, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-320,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-320, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-370,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-370, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-410,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-410, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-470,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-470, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-520,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-520, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-550,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-550, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-600,  35 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-600, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin-725,  37.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-725, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  38.6 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin, $rep->row);
        $rep->Line($rep->row);


        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
//            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal,$dec);
//        $rep->row = $rep->bottomMargin + (9 * $rep->lineHeight);
        $doctype = ST_PURCHORDER;

//        $rep->TextCol(3, 6, _("Sub-total"), -2);
//        $rep->TextCol(6, 7,	$DisplaySubTot, -2);
//        $rep->NewLine();
//
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
//                        $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//                        $rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
//                        $rep->NewLine();
                    }
//                    $rep->TextCol(3, 6, $tax_type_name, -2);
//                    $rep->TextCol(6, 7,	$DisplayTax, -2);
                    $first = false;
                }
                else
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
                $SubTotal += $tax_item['Value'];
//                $rep->TextCol(3, 6, $tax_type_name, -2);
//                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
//            $rep->NewLine();
        }

        $rep->NewLine();
        $DisplayTotal = number_format2($SubTotal, $dec);
        $rep->Font('bold');
        $rep->TextCol(1, 2, _("TOTAL AMOUNT"), - 2);
        $rep->AmountCol(2, 3,	price_format($total_qty), 2);
        $rep->TextCol(5, 6,	price_format($gross_total), -2);
        $rep->TextCol(7,8,	price_format($sale_tax_tot), -2);
        $rep->TextCol(8,9,	price_format($net), -2);

        $words = price_in_words($SubTotal, ST_PURCHORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
//            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
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
//    $amount_in_words = _number_to_words($am_inwords+$DisplayTax);
//    $rep->font('b');
//    $rep->multicell(85,15,"  Amount in words:",0,'L',0,0,40,500);
//    $rep->font('');
//    $rep->multicell(400,15," ".$amount_in_words,0,'L',0,0,125,500);
//    $rep->NewLine(+2.5);
//    $rep->TextCol(8, 9,	$net, -2);
//    $rep->NewLine(-2.5);
//    $rep->multicell(85,15," NET AMOUNT:",0,'L',0,0,300,480);
//    $rep->multicell(85,15,"Payment Terms:",0,'L',0,0,40,550);
////    $rep->multicell(450,30,"",1,'L',0,0,110,110);
//    $rep->multicell(85,15,"Note:",0,'L',0,0,40,585);
//    $rep->multicell(450,200,$myrow['comments'],0,'L',0,0,125,550);
//    $rep->multicell(85,15,"Shipping Instruction:",0,'L',0,0,40,650);
////    $rep->multicell(450,30,"",1,'L',0,0,125,110);
//    $rep->multicell(85,15,"Invoice To:",0,'L',0,0,40,680);
//    $rep->multicell(450,50,"",1,'L',0,0,110,680);
//    $rep->multicell(85,15,"Remarks:",0,'L',0,0,40,735);
//    $rep->multicell(450,50,"",1,'L',0,0,110,735);

    $rep->NewLine(4);
    $rep->TextCol(0, 2,	"________________________", -2);
    $rep->TextCol(5, 8,	"________________________", -2);
    $rep->TextCol(10, 12,	"________________________", -2);
    $rep->NewLine();
    $rep->TextCol(0, 2,	"           Prepared by", -2);
    $rep->TextCol(5, 8,	"Checked by             ", -2);
    $rep->TextCol(10, 12,"Approved by           ", -2);
    $rep->NewLine(2);
    $application_date = date("d-m-Y h:i:s", strtotime($myrow['application_date']));
    $rep->TextCol(0, 2,	"User Name:" ."  ".get_user_realname($_SESSION['wa_current_user']->user), -2);
    $rep->NewLine();
    $rep->TextCol(0, 2,	"Date & Time:"." ".$application_date, -2);
//    $rep->multicell(150,15,"________________________",0,'L',0,0,40,500);
//    $rep->multicell(150,15,"Prepared by",0,'L',0,0,70,510);
//    $rep->multicell(150,15,"________________________",0,'L',0,0,340,500);
//    $rep->multicell(150,15,"Checked by",0,'L',0,0,370,510);
//    $rep->multicell(150,15,"________________________",0,'L',0,0,640,500);
//    $rep->multicell(150,15,"Approved by",0,'L',0,0,670,510);

//    $rep->multicell(50, 10,"User Name:",0,'L',0,0,40,530);
//    $rep->multicell(200, 12,get_user_realname($_SESSION['wa_current_user']->user),0,'L',0,0,100,530);
//    $rep->multicell(60, 10,"Date & Time:",0,'L',0,0,40,545);
    // $d=strtotime("next Saturday");

    // $rep->multicell(200, 12, $application_date, 0,'L', 0, 0, 100, 545);
//    $rep->multicell(200,12,$application_date,0,'L',0,0,100,545);
//    $rep->multicell(50,10,"Time:",0,'L',0,0,40,560);
//    $rep->multicell(200,12,"",0,'L',0,0,90,560);

    if ($email == 0)
        $rep->End();
}

?>