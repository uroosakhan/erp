<?php
$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SALESTRANSVIEW' : 'SA_SALESBULKREP';
// ----------------------------------------------------------------
// $ Revision: 2.0 $
// Creator:    Joe Hunt
// date_:  2005-05-19
// Title:  Print Invoices
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

//----------------------------------------------------------------------------------------------------

print_invoices();

//----------------------------------------------------------------------------------------------------

function get_customer_information($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}


function get_name($order){

    $sql = "SELECT *
            FROM ".TB_PREF."sales_orders 
            
            WHERE ".TB_PREF."sales_orders.order_no=".db_escape($order);

    $result = db_query($sql,"Error");
    $row = db_fetch($result);
    return $row;
}


function get_phone_($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
   SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general'
    AND entity_id IN (
   SELECT branch_code FROM `0_cust_branch` WHERE debtor_no=".db_escape($debtor_no).')) ';
    $db  = db_query($sql,"item prices could not be retreived");
    $ft = db_fetch_row($db);
    return $ft[0];


}


function get_area($id)
{
    $sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}



function get_saleman($id)
{
    $sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

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

function get_shippers_name_rep($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

    $result = db_query($sql, "could not get shippers name");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_customer_balance($customer_id, $to=null, $all=true)
{
    $date =today();
    $date1=date2sql($date);
//    if ($to == null)
//        $todate = date("Y-m-d");
//    else
//        $todate = date2sql($to);
    $past1 = get_company_pref('past_due_days');
    $past2 = 2 * $past1;
    // removed - debtor_trans.alloc from all summations

// $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
    if ($all)
        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";
    else
        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
          trans.alloc),0)";



    $due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
             credit_status.dissallow_invoices, credit_status.reason_description,
            Sum(IFNULL($value,0)) AS Balance
         FROM ".TB_PREF."debtors_master debtor
             LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$date1' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
        .TB_PREF."payment_terms terms,"
        .TB_PREF."credit_status credit_status
         WHERE
               debtor.payment_terms = terms.terms_indicator
            AND debtor.credit_status = credit_status.id
         
            ";
    if ($customer_id)
        $sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

    if (!$all)
        $sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;

    if($dim != 0)
        $sql .= " AND trans.dimension_id = ".db_escape($dim);
    $sql .= " GROUP BY
         debtor.name,
         terms.terms,
         terms.days_before_due,
         terms.day_in_following_month,
         debtor.credit_limit,
         credit_status.dissallow_invoices,
         credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

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



    if (!$from || !$to) return;

    $dec = user_price_dec();

    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);



    $cols = array(1, 20,190, 220,260, 340, 395, 460, 520);
    // $headers in doctext.inc
    $aligns = array('left','left','center', 'center','center', 'center', 'right', 'right');
    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

//display_error($this->formData['Charge']);
//display_error($this->formData['Builty Number']);


    if ($email == 0)
    {
        $rep = new FrontReport(_(''), "InvoiceBulk", user_pagesize());
        $rep->SetHeaderType('Header107322');

//        $rep->SetHeaderType('Header22');

        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);
    }
    for ($i = $from; $i <= $to; $i++)
    {
        if (!exists_customer_trans(ST_SALESINVOICE, $i))
            continue;
        $sign = 1;

        $myrow = get_customer_trans($i, ST_SALESINVOICE);

        $debtor_no=$myrow['debtor_no'];



        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        $branch = get_branch($myrow["branch_code"]);
        $sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);

        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize());
            $rep->SetHeaderType('Header107322');
            $rep->currency = $cur;
            $rep->Font();
            $rep->title = _('');
            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
            $rep->Info($params, $cols, null, $aligns);
        }
        else
            $rep->title = _('');


        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], false);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->NewPage();
        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $SubTotal = 0;
        $sub_total=0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $Net1 = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]));
            $net_total =($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]));
//          $SubTotal +=( $Net-$myrow["ov_freight"]);
            $SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);

            $ZeroValue = "";


            if ($myrow2["discount_percent"]==0)
                $DisplayDiscount ="";



            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";

            $SerialNumber += 1;

// Discount option item controled hiding code
            if ($DisplayPrice < 0)
            {
                $rep->TextCol(0, 1,$ZeroValue, -2);
            }
            else
                $rep->TextCol(0, 1,$SerialNumber, -2);

//          $rep->TextCol(0, 1,    $myrow2['stock_id'], -2);
            $oldrow = $rep->row;
            if ($DisplayPrice < 0)
            {
//                $rep->TextCol(1, 2,  $ZeroValue, -2);
            }
            else
                $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
//            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
//            {







                $rep->TextCol(2, 3,$myrow2["quantity"], -2);
                $rep->TextCol(3, 4,    $myrow2['units_id'], -2);
                $rep->TextCol(4, 5,    $DisplayPrice, -2);
                $rep->TextCol(5, 6,    $DisplayDiscount, -2);
                $rep->TextCol(6, 7,    $DisplayNet, -2);
                $sub_total += (1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"];
                $net_price=number_format2($Net/$myrow2["quantity"], $dec);
                $rep->TextCol(7, 8,    $net_price, -2);

            //}


            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
        }

//

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*-$myrow["ov_freight"],$dec);

        //$rep->NewLine();

// Query for DISCOUNT hiding/showing column header

        $sql = "
SELECT
SUM(discount_percent) AS DiscountAmount
FROM ".TB_PREF."debtor_trans_details
WHERE debtor_trans_no = '$myrow[trans_no]'
AND debtor_trans_type = 10
";
        $result = db_query($sql,"No transactions were returned");
        $bal1 = db_fetch($result);


        $rep->NewLine(-3.6);
//        $rep->TextCol(5, 6, $DiscountHeader, - 2);
        $rep->NewLine();
        $rep->NewLine();
        $rep->NewLine();


        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;

// Discount option controled from service item
        if ($DisplayPrice < 0)

        {
//            $rep->TextCol(4, 6, Total , -2);
            $TotalAmount1 -= $Net-$SubTotal; //addition of discount into total amount
            $TotalAmount = number_format2($TotalAmount1,$dec);

//
        }

        else      {
//            $rep->TextCol(4, 6, _("Total"), -2);
//            $rep->TextCol(6, 7, $DisplaySubTot, -2);

            $ZeroDiscount = "";
            $rep->TextCol(3, 6, _(""), -2);
            $rep->TextCol(6, 7,    $ZeroDiscount, -2);
        }



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
                        $rep->TextCol(6, 7,    number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }


//                    $rep->TextCol(3, 6, $tax_type_name, -2);
//                    $rep->TextCol(6, 7,  $DisplayTax, -2);
                    $first = false;
                }

                else
                    $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
//                $rep->TextCol(3, 6, $tax_type_name, -2);
//                $rep->TextCol(6, 7,  $DisplayTax, -2);
            }


            $rep->NewLine();
        }

        $rep->NewLine();
        $DisplayTotal = number_format2($sign*(-$myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);
//        $rep->Font('bold');

//        $rep->TextCol(4, 6, _("TOTAL INVOICE"), - 2);

//        $rep->MultiCell(555, 18, _("Powered by www.hisaab.pk") , 0, 'L', 0, 2, 450,824, true);




//        $rep->TextCol(6, 7, $DisplayTotal, -2);
        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }







// Account Status
//Debit
        $sql = "
SELECT
SUM(ov_amount) AS OutStanding
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type = 10
";
        $result = db_query($sql,"No transactions were returned");
        $bal2 = db_fetch($result);

//Credit
        $sql = "
SELECT
SUM(ov_amount) AS Payments
FROM ".TB_PREF."debtor_trans 
WHERE debtor_no = '$myrow[debtor_no]'
AND type IN (12 , 11 , 2, 42)
";

//AND type IN (11,12,2)
        $result = db_query($sql,"No transactions were returned");
        $bal3 = db_fetch($result);

        $TotalCredit = round2($bal3['Payments'], $dec); //Total credit side balance

        $TotalDebit = round2($bal2['OutStanding'], $dec); // Total debit side balance

        $CurrentAmount = $SubTotal-$myrow["ov_freight"];

//$PreviousBalance = number_format2($TotalDebit-$TotalCredit-$SubTotal);
        $date =today();
        $customer_record = get_customer_balance($myrow['debtor_no'],$date);
        $total_balance=$customer_record["Balance"];
        $tot_balance=($total_balance - $CurrentAmount);
        $PreviousBalance = $total_balance ;

// $PreviousBalance = number_format2($TotalDebit-$TotalCredit);

        $TotalBalance2 = number_format2($TotalDebit-$TotalCredit-$SubTotal-$myrow["ov_freight"]);


//        foo
//        $rep->NewLine(2);
//        $rep->TextCol(0, 1, _("Note:"), -2);
//        $rep->TextCol(1, 4, _(""), -2);
        $rep->Font();

        $my_row1=get_customer_information($myrow['debtor_no']);
        $area_name=get_area($my_row1['area']);
        $sale_man=get_saleman($my_row1['salesman']);

        $ship_via=get_shippers_name_rep($myrow['ship_via']);
        $rep->MultiCell(250, 20, $sales_order['name'], 0, 'L', 0, 2, 145,90, true);
        $rep->MultiCell(300, 18,"".$branch['br_name'], 0, 'L', 0, 2, 145,100, true);
        $rep->MultiCell(220, 18,"".$branch['br_address'], 0, 'L', 0, 2, 145,112, true);
        $rep->MultiCell(300, 18,"".$my_row1['phone'], 0, 'L', 0, 2, 145,137, true);
        $rep->MultiCell(300, 18,"".get_area($branch['area']), 0, 'L', 0, 2, 145,150, true);






        $rep->MultiCell(150, 18,"".$rep->formData['tran_date'], 0, 'L', 0, 2,442,100, true);



        $rep->MultiCell(150, 18,"".$rep->formData['reference'], 0, 'L', 0, 2, 442,115, true);
        $rep->MultiCell(150, 18,"".$branch['salesman_name'], 0, 'L', 0, 2, 442,126, true);
        $rep->MultiCell(150, 18,"".$sales_order['shipper_name'], 0, 'L', 0, 2, 442,138, true);
        $rep->MultiCell(150, 18,"".$rep->formData['h_text2'],   0, 'L', 0, 2, 442,150, true);



//display_error($my_row1);
        $rep->Font('bold');
        $rep->NewLine();
        $order_no= get_name($myrow['order_']) ;

//        $note=get_name($order_no);
        $rep->MultiCell(50, 18, _("Note:") , 0, 'L', 0, 2, 40,660, true);


        $rep->TextCol(5,7, _("SUB TOTAL"), -2);
//           if ($TotalBalance2 > 0)
//        $rep->TextCol(7,8, number_format2($tot_balance , $dec));
        $rep->TextCol(7,8, number_format2($sub_total,user_price_dec()));
        $special_discount=($sub_total * $myrow['ov_discount']/100);
        //previous balance
//           else
        $rep->TextCol(6, 7, _("") , -2);
        $rep->NewLine();
        $rep->TextCol(5,7, _("SPECIAL DISCOUNT"), -2);
//        $rep->TextCol(7,8, number_format2($CurrentAmount, $dec));// Current Amount
        $rep->TextCol(6,7, $myrow['ov_discount'].'%');

        $dis_amount=$sub_total *$myrow['ov_discount']/100;

        $rep->TextCol(7,8,    number_format2($dis_amount,user_price_dec()));
        $rep->NewLine();


        $rep->TextCol(5,7, _("ADD CHARGE"), -2);

//        $rep->TextCol(7,8, number_format2($rep->formData['f_text1'],user_price_dec()), -2);
        $rep->TextCol(7,8, number_format2($myrow['ov_freight'],user_price_dec()), -2);


        $rep->NewLine();
        $net_amount=abs($sub_total-$special_discount+$myrow['ov_freight']);
        $rep->TextCol(5,7, _("Net AMOUNT"), -2);
        $rep->TextCol(7,8, number_format2($net_amount,user_price_dec()), -2);

        $rep->NewLine();

        $rep->NewLine();
        $rep->Font();



        if ($email == 1)
        {
            $rep->End($email);
        }
    }
    $rep->MultiCell(300, 70,$order_no['term_cond'], 0, 'L', 0, 2, 70,660, true);
    $rep->Font();
    if ($email == 0)
        $rep->End();
}

?>