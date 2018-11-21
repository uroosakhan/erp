<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Sales Quotations
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------

print_sales_quotations();
function get_designation_name($id)
{
    $sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
}

function get_user_name_70123($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_user_name_($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function print_sales_quotations()
{
    global $path_to_root, $print_as_quote, $print_invoice_no, $no_zero_lines_amount;

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

    $cols = array(4, 60, 245, 340, 400, 385, 450, 515);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'left', 'left', 'right', 'right');

    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_("SALES QUOTATION"), "SalesQuotationBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {
        $myrow = get_sales_order_header($i, ST_SALESQUOTE);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            if ($print_invoice_no == 1)
                $rep->filename = "SalesQuotation" . $i . ".pdf";
            else
                $rep->filename = "SalesQuotation" . $myrow['reference'] . ".pdf";
        }
        $rep->SetHeaderType('Header11113');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESQUOTE, $contacts);
        //$rep->headerFunc = 'Header11100';
        $rep->NewPage();

        $result = get_sales_order_details($i, ST_SALESQUOTE);
        $SubTotal = 0;
        $items = $prices = array();
        while ($myrow2=db_fetch($result))
        {
            $Net = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],get_qty_dec($myrow2['stk_code']));
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            $rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
            $oldrow = $rep->row;
            $rep->TextColLines(1, 2, $myrow2['description'] ."\n".$myrow2['text1'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
                 $rep->TextCol(2, 3,	$DisplayQty, -2);
                $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                if($pref['alt_uom'] == 1)
                {
                    $rep->TextCol(3, 4,	$myrow2['units_id'], -2);
                    $rep->TextCol(4, 5,	$DisplayPrice, -2);
                    $rep->TextCol(5, 6,	$DisplayDiscount, -2);
                    $rep->TextCol(6, 7,	$DisplayNet, -2);

                }
                else{
                    $rep->TextCol(3, 4,	$myrow2['units'], -2);
                    $rep->TextCol(4, 5,	$DisplayPrice, -2);
                    $rep->TextCol(5, 6,	$DisplayDiscount, -2);
                    $rep->TextCol(6, 7,	$DisplayNet, -2);
                }
            }
//               if ($myrow2['text1'] != "")
//             {
//                 $rep->NewLine(3);
//                 $rep->TextColLines(1, 5, $myrow2['text1'], -2);
// //                $rep->NewLine();
//             }
            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }
        
        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($myrow["freight_cost"],$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESQUOTE;

        $rep->TextCol(3, 6, _("Sub-total"), -2);
        $rep->TextCol(6, 7,	$DisplaySubTot, -2);
        $rep->NewLine();
//        $rep->TextCol(3, 6, _("Shipping"), -2);
//        $rep->TextCol(6, 7,	$DisplayFreight, -2);
        $rep->NewLine();

        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
        if ($myrow['tax_included'] == 0) {
//            $rep->TextCol(3, 6, _("TOTAL ORDER EX GST"), - 2);
//            $rep->TextCol(6, 7,	$DisplayTotal, -2);
            $rep->NewLine();
        }

        $tax_items = get_tax_for_items($items, $prices, $myrow["freight_cost"],
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
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
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

        $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
        $rep->Font('bold');
//        $rep->TextCol(3, 6, _("TOTAL ORDER GST INCL."), - 2);
//        $rep->TextCol(6, 7,	$DisplayTotal, -2);
        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESQUOTE);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }

        $user =get_user_id($i,ST_SALESINVOICE);
        $rep->MultiCell(100, 25, "Created by: ".get_user_name_70123($user) ,0, 'C', 0, 2, 60,815, true);
        $rep->MultiCell(100,12,get_designation_name($user),0,'L',0,0,160,815);


        $rep->Font();
        if ($email == 1)
        {
            if ($print_invoice_no == 1)
                $myrow['reference'] = $i;
            $rep->End($email);
        }
    }
//     $rep->MultiCell(30,10,"User:",0,'L',0,0,45,826);
//     $rep->MultiCell(100,12,get_user_name_($_SESSION['wa_current_user']->user),0,'L',0,0,70,795);
    if ($email == 0)
        $rep->End();
}

?>