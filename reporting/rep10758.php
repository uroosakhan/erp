<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_invoices();

//----------------------------------------------------------------------------------------------------
//sahriq
function getuser_realname($trans_no)
{

    $sql = "SELECT real_name
		FROM  ".TB_PREF."audit_trail, ".TB_PREF."debtor_trans , ".TB_PREF."users
		WHERE ".TB_PREF."audit_trail.trans_no = ".TB_PREF."debtor_trans.trans_no
		AND ".TB_PREF."audit_trail.user = ".TB_PREF."users.id
              	AND ".TB_PREF."debtor_trans.trans_no = ".db_escape($trans_no);



    $result = db_query($sql, "could not get sales type");
    $row = db_fetch_row($result);
    return $row[0];
//return db_query($sql,"No transactions were returned");

}

function print_invoices()
{
    global $path_to_root, $alternative_tax_include_on_docs, $suppress_tax_rates, $no_zero_lines_amount;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $pay_service = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);

    $cols = array(150, 180, 420, 470);

    // $headers in doctext.inc
    $aligns = array('left',	'left',	'left', 'right');
    $header = array(_("Qty"), _("Item Description"), _("Unit"),_("Total"));
    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    for ($i = $from; $i <= $to; $i++)
    {
        if (!exists_customer_trans(ST_SALESINVOICE, $i))
            continue;
        $sign = 1;
        $myrow = get_customer_trans($i, ST_SALESINVOICE);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        $branch = get_branch($myrow["branch_code"]);
        $sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('INVOICE');
            //	$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
        }
        $rep->SetHeaderType('Header10758');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $header, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $SubTotal = 0;

        // while
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
            //$rep->TextCol(0, 1,	$myrow2['stock_id'], -2);

//				$rep->NewLine(-1.8);

            $oldrow = $rep->row;
            $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
                $rep->TextCol(0, 1,	$DisplayQty, -2);
                $rep->TextCol(2, 3,	$DisplayPrice, -2);
                $rep->TextCol(3, 4,	$DisplayNet, -2);


                //	$rep->NewLine(30);
                //	$rep->TextCol(0, 1,	$DisplayQty, -2);
                //	$rep->TextCol(4, 5,	$DisplayPrice, -2);
                //	$rep->TextCol(6, 7,	$DisplayNet, -2);
                //	$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
                //	$rep->NewLine(-30);

                //$rep->TextCol(3, 4,	$myrow2['units'], -2);

            }
            $rep->row = $newrow;
            //$rep->NewLine(1.8);


            $rep->NewLine(28);

            $oldrow = $rep->row;
//				$rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $description =$myrow2['StockDescription'];
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {

                $rep->NewLine(3);
//					$rep->MultiCell(105, 12, "".$DisplayQty, 0, 'L', 0, 0, 290,760, true);
                $rep -> SetX(218);
                $rep->Cell(40,930,$DisplayQty);
                $rep->Cell(40,930,$description);
//					$rep->Cell(-200,500,$DisplayQty,0,1,'L');
//					$rep->Cell(1,970, $DisplayQty,0,1,'R', false,'',0,false,'R','R');
//					$rep->Cell(1,970,$myrow2['StockDescription'],0,1,'L', false,'',0,false,'L','L');
//					$rep->TextCol(1, 2,	$DisplayQty, -2);
                $rep -> SetX(428);
                $rep->Cell(3, 930,	$DisplayPrice);
                $rep -> SetX(497);
                $rep->Cell(2, 930,	$DisplayNet);
                //$mytotal=
                //	$rep->TextCol(3, 4,	$DisplayNet, -2);


                //	$rep->NewLine(30);
//					$rep->TextCol(0, 1,	$DisplayQty, -2);

//					$rep->TextCol(6, 7,	$DisplayNet, -2);
//					$rep->TextCol(1, 5, $myrow2['StockDescription'], +100);
                //	$rep->NewLine(-30);

//					$rep->TextCol(3, 4,	$myrow2['units'], -2);

            }
            $rep->row = $newrow;
            $rep->NewLine(-28);










            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }



        $memo = get_comments_string(ST_SALESINVOICE, $i);
        if ($memo != "")
        {
            //$rep->NewLine();
            //$rep->TextColLines(1, 5, $memo, -2);
//				$rep->MultiCell(180, 12, $memo, 0, 'L', 0, 0, 400,195, true);
        }

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;

        //$rep->TextCol(3, 6, _("Sub-total"), -2);
        //$rep->TextCol(6, 7,	$DisplaySubTot, -2);
        //$rep->NewLine();
        //$rep->TextCol(3, 6, _("Shipping"), -2);
        //$rep->TextCol(6, 7,	$DisplayFreight, -2);
        //$rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;
        while ($tax_item = db_fetch($tax_items))
        {
            if ($tax_item['amount'] == 0)
                continue;
            $DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

            if (isset($suppress_tax_rates) && $suppress_tax_rates == 1)
                $tax_type_name = $tax_item['tax_type_name'];
            else
                $tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

            if ($tax_item['included_in_price'])
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
                $rep->TextCol(3, 6, $tax_type_name, -2);
                $rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->NewLine();
        }


        $DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
        $rep->Font('bold');
        $rep->NewLine(-25);
        $rep->MultiCell(85,20, "TOTAL AMOUNT", 1, 'C', 0, 0, 410,355, true);
        $rep->MultiCell(80,20, "    ".$DisplayTotal, 1, 'L', 0, 0, 495,355, true);

//		$rep->TextCol(1, 3, _("TOTAL AMOUNT"), - 2);
//			$rep->TextCol(3, 4, $DisplayTotal, -2);
        $rep->NewLine(+18);
        $rep->NewLine(18);


        //$rep->TextCol(1, 3, _("TOTAL INVOICE"), - 2);
        $rep -> SetX(500);
        $rep->Cell(3, 830, $DisplayTotal);
        $name = getuser_realname($i);
        $rep->Font('');
        // $rep->NewLine(2);
        // $rep->TextCol(1, 3,  $name, - 2);

        //$rep->NewLine(-18);
        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
        if ($words != "")
        {
            //$rep->NewLine(1);
//				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
        $rep->Font();
        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    $rep->Font('bold');
    $rep->MultiCell(390, 140, "", 1, 'L', 0, 0, 185,215, true);
    $rep->MultiCell(390, 20, "", 1, 'L', 0, 0, 185,195, true);
    $rep->MultiCell(75, 12, "Ref.#: ".$memo, 0, 'L', 0, 0, 370,160, true);
    $rep->MultiCell(75, 12, "Estimate.# ".$myrow['reference'], 0, 'L', 0, 0, 200,160, true);
    $rep->MultiCell(75, 12, "Signature: ".$_SESSION["wa_current_user"]->name, 0, 'L', 0, 0, 400,430, true);

    //$rep->MultiCell(75, 12, "Inv#", 0, 'L', 0, 0, 420,620, true);
    $rep->MultiCell(75, 12, "Date: ".sql2date($rep->formData['document_date']), 0, 'L', 0, 0, 200,140, true);
    $rep->Font('bold');
    $rep->MultiCell(45, 12, "Qty", 0, 'C', 0, 0, 200,650, true);
    $rep->MultiCell(325, 12, "Item Description", 0, 'C', 0, 0, 120,650, true);
    $rep->MultiCell(85, 12, " Unit Price ", 0, 'C', 0, 0, 400,650, true);
    $rep->MultiCell(70, 12, " Total ", 0, 'R', 0, 0, 455,650, true);

    $rep->MultiCell(75, 12, "Date: ".sql2date($rep->formData['document_date']), 0, 'L', 0, 0, 60,650, true);

    $rep->MultiCell(75, 12, "Ref.#: ".$memo, 0, 'L', 0, 0, 60,695, true);
    $rep->MultiCell(75, 12, "Estimate.# ".$myrow['reference'], 0, 'L', 0, 0, 60,720, true);
    $rep->MultiCell(75, 12, "User ID: ".$_SESSION["wa_current_user"]->name, 0, 'L', 0, 0, 60,740, true);
//			$rep->MultiCell(70, 170, "  ", 0, 'R', 0, 0, 495,652, true);
//			$rep->MultiCell(85, 170, "  ", 0, 'C', 0, 0, 410,652, true);
    //$rep->MultiCell(325, 170, "", 0, 'C', 0, 0, 85,652, true);
    //$rep->MultiCell(45, 170, "", 0, 'C', 0, 0, 40,652, true);
    $logo = company_path() . "/images/" . 'footr.png';

//$rep->SetXY(200, 200);
//$rep->Image($logo, '185', '580', 200, 15, '', '', 'T', false, 200, '', false, false, 1, false, false, false);
    $logo = company_path() . "/images/" . 'muslm.png';

//$rep->SetXY(200, 200);
//$rep->Image($logo, '35', '30', 530, 200, '', '', 'T', false, 200, '', false, false, 1, false, false, false);


//$rep->MultiCell(45, 12, $rep->formData['document_number'], 0, 'L', 0, 0, 144,136, true); // uppar
//$rep->MultiCell(75, 12,  sql2date($rep->formData['document_date']), 0, 'L', 0, 0, 144,156, true); // upar



    if ($email == 0)
        $rep->End();
}

?>