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

//----------------------------------------------------------------------------------------------------
print_sales_orders();
function get_customer_phone($ref)
{
    $sql="SELECT * FROM 0_crm_persons where ref='".$ref."' and inactive=0";
    $db = db_query($sql);
    return db_fetch($db);
}
function get_customer_balance($customer_id)
{


    if ($to == null)
        $todate = date("Y-m-d");
    else
        $todate = date2sql($to);
    $past1 = get_company_pref('past_due_days');
    $past2 = 2 * $past1;
    // removed - debtor_trans.alloc from all summations
    if ($all)
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount),0)";
    else
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - 
    		trans.alloc),0)";
    $due = "IF (trans.type=10, trans.due_date, trans.tran_date)";
    $sql = "SELECT ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."payment_terms.terms,
		".TB_PREF."debtors_master.credit_limit, ".TB_PREF."credit_status.dissallow_invoices, ".TB_PREF."credit_status.reason_description,

		Sum(IFNULL($value,0)) AS Balance,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2

		FROM ".TB_PREF."debtors_master
			 LEFT JOIN ".TB_PREF."debtor_trans trans ON 
			 trans.tran_date <= '$todate' AND ".TB_PREF."debtors_master.debtor_no = trans.debtor_no AND trans.type <> 13,
			 ".TB_PREF."payment_terms,
			 ".TB_PREF."credit_status

		WHERE
			 ".TB_PREF."debtors_master.payment_terms = ".TB_PREF."payment_terms.terms_indicator
 			 AND ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			 AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id)." 
			";
    if (!$all)
        $sql .= "AND ABS(trans.ov_amount + trans.ov_gst - trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.alloc) > ".FLOAT_COMP_DELTA." ";
    $sql .= "GROUP BY
			  ".TB_PREF."debtors_master.name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month,
			  ".TB_PREF."debtors_master.credit_limit,
			  ".TB_PREF."credit_status.dissallow_invoices,
			  ".TB_PREF."credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}

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
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $print_as_quote = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];

    if (!$from || !$to) return;

    $orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();
    $myrow233 = get_company_item_pref('con_factor');
    $pref = get_company_prefs();
    if($orientation == 'P') {
        //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
        $cols = array(4, 22, 60, 217, 235, 285, 345);

        // $headers in doctext.inc
        $aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');

    }

//    elseif($orientation == 'L')
//    {
//        if($pref['batch'] == 1){
//            //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
//            $cols = array(4, 40, 195, 220,260, 315, 355, 395, 450, 475);
//
//            // $headers in doctext.inc
//            $aligns = array('left',	'left',	'left',	'left',	'right', 'center', 'right', 'right', 'right');
//        }
//        else{
//            //-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//
//            $cols = array(4, 40, 265, 140, 285, 315, 350, 515);
//
//            // $headers in doctext.inc
//            $aligns = array('left',	'left',	'left', 'left', 'right', 'right', 'right');
//
//        }
//    }
    $params = array('comments' => $comments, 'print_quote' => $print_as_quote);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
    {

        if ($print_as_quote == 0)
            $rep = new FrontReport(_("SALES ORDER"), "SalesOrderBulk", 'A5', 7, $orientation);
        else
            $rep = new FrontReport(_("QUOTE"), "QuoteBulk", 'A5', 7, $orientation);
    }
    if ($orientation == 'L')
        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {
        $myrow = get_sales_order_header($i, ST_SALESORDER);
        if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
            continue;
        }



        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];
        $branch = get_branch($myrow["branch_code"]);
        if ($email == 1)
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
        $rep->SetHeaderType('Header10929');
        $rep->currency = $cur;
        $rep->Font();
        if ($print_as_quote == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            if ($print_as_quote == 1)
            {
                $rep->title = _('QUOTE');
                $rep->filename = "Quote" . $i . ".pdf";
            }
            else
            {
                $rep->title = _("SALES ORDER");
                $rep->filename = "SalesOrder" . $i . ".pdf";
            }
        }
        else
            $rep->title = ($print_as_quote==1 ? _("QUOTE") : _("SALES ORDER"));
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'order', $branch['debtor_no'], true);
        $rep->SetCommonData($myrow, $branch, $myrow, $baccount, ST_SALESORDER, $contacts);
        $rep->SetHeaderType('Header10929');
        $rep->NewPage();

        $result = get_sales_order_details($i, ST_SALESORDER);
        $SubTotal = 0;
        $items = $prices = array();
        $serial = 1 ;
        while ($myrow2=db_fetch($result))
        {
            $customer_phone = get_customer_phone($myrow['name']);
            $rep->MultiCell(200, 10, "Phone : ".$customer_phone['phone'], 0, 'L', 0, 1, 40, 85, true);

            $rep->TextCol(0, 1,	$serial, -2);


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
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($qty,get_qty_dec($myrow2['stk_code']));
            $DisplayNet = number_format2($Net,$dec);
            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
//            $rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
            $oldrow = $rep->row;
            $rep->TextColLines(2, 3, $myrow2['description'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
            {
                $item=get_item($myrow2['stk_code']);
                $pref = get_company_prefs();


                $rep->Amountcol(1, 2,	$qty ,$dec);


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
//                    display_error(12);

//                    $rep->Amountcol(5, 6,	$myrow2['con_factor'], $dec);

                    if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
                        $rep->TextCol(4, 5, $DisplayPrice, -2);
//                        $rep->TextCol(7, 8, $DisplayDiscount, -2);
                        $rep->TextCol(5, 6, $DisplayNet, -2);
                    }
                }
                else{

                    if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
                        $rep->TextCol(4,5, $DisplayPrice, -2);
//                        $rep->TextCol(6, 7, $DisplayDiscount, -2);
                        $rep->TextCol(5, 6, $DisplayNet, -2);
                    }
                }



            }
            $rep->row = $newrow;
            if ($rep->row < $rep->bottomMargin + (7 * $rep->lineHeight))
                $rep->NewPage();

            $serial ++ ;
        }
        if ($myrow['comments'] != "")
        {
//            $rep->NewLine();
//            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal,$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESORDER;
        $myrow3 = get_company_item_pref('con_factor');
$rep->newline(4);
        if(!$_SESSION["wa_current_user"]->can_access('SA_SALESORDER_PDF')) {
//            if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
//
//                $rep->TextCol(2, 3, _("Sub-total"), -2);
//                $rep->TextCol(5, 6, $DisplaySubTot, -2);
//            } else {
//                $rep->TextCol(2, 3, _("Sub-total"), -2);
//                $rep->TextCol(5, 6, $DisplaySubTot, -2);
//            }
            $rep->NewLine();
            if ($myrow['freight_cost'] != 0.0) {
                $DisplayFreight = number_format2($myrow["freight_cost"], $dec);
                $rep->TextCol(2, 3,  _("Shipping"), -2);
                $rep->TextCol(5, 6, $DisplayFreight, -2);
                $rep->NewLine();
            }
            $DisplayTotal = number_format2($myrow["freight_cost"] + $SubTotal, $dec);
            $sub_total= $myrow["freight_cost"] + $SubTotal;
//            if ($myrow['tax_included'] == 0) {
//                if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
//                    $rep->TextCol(2, 3,  _("TOTAL ORDER EX GST"), -2);
//                    $rep->TextCol(5, 6, $DisplayTotal, -2);
//                } else {
//                    $rep->TextCol(2, 3,  _("TOTAL ORDER EX GST"), -2);
//                    $rep->TextCol(5, 6, $DisplayTotal, -2);
//                }
//
//            }

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
                            $rep->TextCol(2, 3,  _("Total Tax Excluded"), -2);
                            $rep->TextCol(5, 6, number_format2($sign * $tax_item['net_amount'], $dec), -2);
                            $rep->NewLine();
                        }
                        if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                            $rep->TextCol(2, 3,  $tax_type_name, -2);
                            $rep->TextCol(5, 6, $DisplayTax, -2);
                        } else {
                            $rep->TextCol(2, 3,  $tax_type_name, -2);
                            $rep->TextCol(5, 6,  $DisplayTax, -2);
                        }
                        $first = false;
                    } else
                        $rep->TextCol(2, 3,  _("Included") . " " . $tax_type_name . " " . _("Amount") . ": " . $DisplayTax, -2);
                } else {
                    $SubTotal += $tax_item['Value'];
                    if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
                        $rep->TextCol(2, 3,  $tax_type_name, -2);
                        $rep->TextCol(5, 6, $DisplayTax, -2);
                    } else {
                        $rep->TextCol(2, 3,  $tax_type_name, -2);
                        $rep->TextCol(5, 6, $DisplayTax, -2);
                    }
                }
                $rep->NewLine();
            }

            $rep->NewLine();

            $DisplayTotal = $myrow["freight_cost"] + $SubTotal;
        }
        $rep->NewLine(+4.5);
        $rep->Font('bold');
        if ($pref['alt_uom'] == 1 && $myrow3['sale_enable'] == 1) {
            $rep->Font(8);
            $rep->TextCol(3, 5,  _("This Invoice") , -2);
            $rep->TextCol(5, 6,number_format2($DisplayTotal,2), -2);
            $rep->NewLine();
            $pre_balance= get_customer_balance($myrow['debtor_no']);

            if ($pre_balance['Balance'] ==0)
                $pre_bal=0;
            else
                $pre_bal= $pre_balance['Balance']-$sub_total  ;

            $rep->TextCol(3, 5,  _("Other Balances") , -2);
            $rep->TextCol(5, 6,number_format2($pre_bal,2), -2);
            $rep->NewLine();
            $total =$pre_bal + $sub_total;
            $rep->TextCol(3, 5,  _("Total") , -2);
            $rep->TextCol(5, 6,number_format2($total,2), -2);
            $rep->Font();

        } else {
            $rep->Font(7);
            $rep->TextCol(3, 5,  _("This Invoice") , -2);
            $rep->TextCol(5, 6,number_format2($DisplayTotal,2), -2);
            $rep->NewLine();
            $pre_balance= get_customer_balance($myrow['debtor_no']);

         if ($pre_balance['Balance'] ==0)
                $pre_bal=0;
            else
                $pre_bal= $pre_balance['Balance']-$sub_total  ;

            $rep->TextCol(3, 5,  _("Other Balances") , -2);
            $rep->TextCol(5, 6,number_format2($pre_bal,2), -2);
            $rep->NewLine();
            $total =$pre_bal + $sub_total;
            $rep->TextCol(3, 5,  _("Total") , -2);
            $rep->TextCol(5, 6,number_format2($total,2), -2);
            $rep->Font();

        }
        $rep->NewLine(-4.5);


        $words = price_in_words($myrow["freight_cost"] + $SubTotal, ST_SALESORDER);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
        $rep->Font();
//        $rep->MultiCell(105,15,get_salesman_name($branch['salesman']),0,'C', 0, 2,145,270,true);

        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    if ($email == 0)
        $rep->End();
}

