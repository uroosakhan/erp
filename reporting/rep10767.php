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

function get_customer_information($debtor_no)
{
    $sql = "SELECT * FROM ".TB_PREF."cust_branch WHERE debtor_no = ".db_escape($debtor_no)."";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}

function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return $string;
}
function get_purchasing_date1($item_code,$batch)
{
    $sql = "SELECT date1 FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($item_code)."AND batch=".db_escape($batch);

    $result = db_query($sql, "could not get Dates");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_credit_balance($debtorno)
{
    $sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans
    	WHERE  ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type = 10
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}
function get_credit_payment($debtorno)
{
    $sql = "SELECT ".TB_PREF."debtor_trans.*,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated,
		((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans
    	WHERE  ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type = 12
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}
function get_purchasing_date($item_code,$batch)
{
    $sql = "SELECT date1 FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($item_code)."AND batch=".db_escape($batch);

    $result = db_query($sql, "could not get Dates");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_shipper_name($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_name_sale($order)
{
    $sql ="SELECT sup_order_num FROM 0_sales_orders where 
	
	0_sales_orders.order_no=".$order;

    $db = db_query($sql);
    $ft = db_fetch($db);
    return $ft[0];
}
function get_consignee($customer_id)
{
    $sql = "SELECT consignee FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($customer_id);

    $result = db_query($sql, "could not get consigee");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_customer_reference($customer_id)
{
    $sql = "SELECT debtor_ref FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_customer_balance($customer_id, $to=null, $all=true)
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
			 AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id)." ";
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
function get_invoice_through_dn($trans_no)
{
    $sql = " SELECT reference,tran_date FROM ".TB_PREF."debtor_trans WHERE order_=".db_escape($trans_no);
    $sql .= " AND type ='13' ORDER BY reference LIMIT 3";

    return db_query($sql, "error");

}

function get_invoice_date_through_dn($type,$trans_no)
{
    $sql = "SELECT * FROM ".TB_PREF."debtor_trans WHERE trans_no=".db_escape($trans_no);
    $sql .= " AND type =$type ";
    $result = db_query($sql, "could not query reference table");
    $row = db_fetch($result);
    return sql2date($row['tran_date']);

}
function get_location_through_dn($type,$trans_no)
{
    $sql = "SELECT from_stk_loc,location_name, customer_ref,loc_code FROM ".TB_PREF."debtor_trans ,".TB_PREF."sales_orders,".TB_PREF."locations
 WHERE  ".TB_PREF."sales_orders.order_no=".TB_PREF."debtor_trans.order_ 
  AND ".TB_PREF."sales_orders.from_stk_loc=".TB_PREF."locations.loc_code
  AND ".TB_PREF."debtor_trans.trans_no=".db_escape($trans_no);
    $sql .= " AND  ".TB_PREF."debtor_trans.type =$type ";
    $result = db_query($sql, "could not query reference table");
    $row = db_fetch($result);
    return $row['location_name'];

}
function get_invoice_location_through_dn($trans_no)
{
    $sql = "SELECT ship_via FROM ".TB_PREF."debtor_trans WHERE order_=".db_escape($trans_no);
    $sql .= " AND type ='13'";
    return db_query($sql, "error");

}
function get_payment_terms_ ($selected_id)
{
    $sql = "SELECT terms
	 FROM ".TB_PREF."payment_terms  WHERE terms_indicator=".db_escape($selected_id);

    $result = db_query($sql,"could not get payment term");
    $row = db_fetch_row($result);
    return $row[0];



}//----------------------------------------------------------------------------------------------------

print_invoices();

//----------------------------------------------------------------------------------------------------
function get_ref_dev($trans_no,$type)
{
    $sql="SELECT reference FROM 0_debtor_trans 
			where 	trans_no=".db_escape($trans_no)." AND type=".db_escape($type)."";
    $db = db_query($sql,'error');
    $ft = db_fetch($db);
    return$ft[0];
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


    $headers = array(_("S.No"),_("Description"),_("Batch No."),_("Quantity"),_("   Bonus"),_("Rate"),_("Gross"),_("Discount"),_("Net")
    );
    $headers2 = array(_(""),_(""),_(""),_(""),_(""),_(""),_("Amount"),_("Amount"),_("Amount")
    );


    $cols = array(4,28,180,220,260,300,340,410,460);
    $cols1 = array(4,28,180,220,260,300,340,410,460);

    // $headers in doctext.inc

    $aligns = array('left','left','left','center','left','right','right','right','right');
    $aligns1 = array('left','left','left','center','right','right','right','right','right');
    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 8, $orientation);
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
            $rep = new FrontReport("", "", user_pagesize(), 8, $orientation);
            $rep->title = _('INVOICE');

            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
        }
        $rep->SetHeaderType('Header10767');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, $headers, $aligns,$cols1,$headers2,$aligns1);

        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE2, $contacts);
        $rep->NewPage();


        $result = get_customer_trans_details(ST_SALESINVOICE, $i);
        $customer_record = get_customer_balance($branch['debtor_no']);
        $credit_balance = get_credit_balance($branch['debtor_no']);
        $credit_payment = get_credit_payment($branch['debtor_no']);
        $SubTotal = 0;
        $image = company_path() . '/images/sign'.'.png';
        $rep->NewLine(39);
        if (file_exists($image))
        {
            $rep->NewLine(2.7);
            if ($rep->row - $pic_height < $rep->bottomMargin)
                $rep->NewPage();
            $rep->AddImage($image, $rep->cols[0]+9, $rep->row - $pic_height, 100,30);
            $rep->row -= $pic_height;
            $rep->NewLine(-2.7);
        }
        //$rep->multicell(100,10, $myrow['customer_order_no'],0,'L',0,0,420,150);
        //$rep->multicell(100,10, sql2date($myrow['customer_order_date']),0,'L',0,0,430,165);
        //$rep->multicell(100,10, get_invoice_through_dn($myrow['order_']),0,'L',0,0,430,170);

        $rep->NewLine(-39);

        //$rep->row = $newrow;
        //$rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        //$rep->row = $newrow;
        //	$rep->NewLine(43);
        //		$image = company_path() . '/images/Text'.'.jpg';
        /*if (file_exists($image))
        {
            $rep->NewLine(2);
            if ($rep->row - $pic_height < $rep->bottomMargin)
                $rep->NewPage();
            $rep->AddImage($image, $rep->cols[5]+8, $rep->row - $pic_height, 215,200);
            $rep->row -= $pic_height;
            $rep->NewLine(-2);
        }*/
        //$rep->NewLine(-43);
        $g_total = 0;
        $s_no=0;
        while ($myrow2=db_fetch($result))
        {
            if ($myrow2["quantity"] == 0)
                continue;
            $s_no++;
            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            //		$SubTotal += $Net;
            $DisplayPrice = number_format2($myrow2["unit_price"],$dec);
            $DisplayQty = number_format2($sign*$myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            $unit =$myrow2['units'];

            $baba = get_name_sale($myrow['order_']);

            $DisplayDiscount = number_format2($myrow2["discount_percent"]*100,2) . "%";
            $rep->SetFontSize(+9.5);

            ;
            $rep->TextCol(0, 1,	$s_no, -2);
            $value_exclude_sale_tax = number_format2($myrow2['unit_price'] * $myrow2["quantity"],$dec);
            //$amount_sales_tax =  $value_exclude_sale_tax * (17/100);
            $value_include_sale_tax = $value_exclude_sale_tax ;

            $rep->TextCol(2, 3,$myrow2['batch'], -2);
            $rep->TextCol(3, 4,$myrow2["quantity"], -2);
            $rep->TextCol(4, 5,"     ".$myrow2['bonus'], -2);
            $rep->TextCol(5, 6,$DisplayPrice, -2);
            $rep->TextCol(6, 7,$value_exclude_sale_tax, -2);

            $rep->TextCol(7, 8,$DisplayDiscount, -2);
            $rep->TextCol(8, 9,number_format2($Net,$dec), -2);
            $rep->Font('');

            $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);

            $expired_date = get_purchasing_date1($myrow2['stock_id'],$myrow2['batch']);
            $rep->NewLine();

            $rep->SetFontSize(-9.5);


            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !isset($no_zero_lines_amount) || $no_zero_lines_amount == 0)
            {
                $amount = $myrow2["quantity"] * $myrow2["unit_price"];

                $TotalQuantity += $DisplayQty;
//				$g_total +=$Net ;
//				$rep->TextCol(2, 3,$myrow2['batch'], -2);
//
//				$DisplayAmount = number_format2($TotalAmount,$dec);
                $DisplayAmount1 = round2($TotalAmount,$dec);
//
//

            }
//			$rep->multicell(220,32,"".get_consignee($myrow["order_"]),0,'L',0,0,42,198);

            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
                $rep->NewPage();
            $rep->NewLine(1.5);
        }


        $memo = get_comments_string(ST_SALESINVOICE, $i);
        if ($memo != "")
        {
            $rep->NewLine();
            //$rep->TextColLines(1, 2, $memo, -2);
        }

        $DisplaySubTot = number_format2($SubTotal,$dec);
        $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);

        $rep->row = $rep->bottomMargin + (15 * $rep->lineHeight);
        $doctype = ST_SALESINVOICE;

        //$rep->TextCol(5, 7, _("Sub-total"), -2);
        //$rep->TextCol(8, 9,	"     ".$DisplaySubTot, -2);
        $rep->NewLine();
//		$rep->TextCol(5, 7, _("Shipping"), -2);
        //$rep->TextCol(8, 9,	"     ".$DisplayFreight, -2);
        $rep->NewLine();
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $i);
        $first = true;
        $grossamount+=$Net;
        while ($tax_item = db_fetch($tax_items))
        {
            if ($tax_item['amount'] == 0)
                continue;
            $DisplayTax = number_format2($sign*$tax_item['amount'], $dec);
            $rep->NewLine(-6);
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
//						$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//						$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
                        $rep->NewLine();
                    }
//					$rep->TextCol(3, 6, $tax_type_name, -2);
//					$rep->TextCol(6, 7,	$DisplayTax, -2);
                    $first = false;
                }
//				else
//					$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
            }
            else
            {
                //$rep->TextCol(3, 5, $tax_type_name, -2);
                //$rep->TextCol(6, 7,	$DisplayTax, -2);
            }
            $rep->NewLine();
        }

        $rep->NewLine(2.6);
        $DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"]+$myrow["ov_freight_tax"]),$dec);


        $rep->Font('bold');

        $rep->SetFontSize(+8.5);

        $rep->TextCol(3, 4 , $TotalQuantity." ".$unit, - 2);
        $rep->TextCol(5, 6, _("Total Amount"), - 2);
        $rep->TextCol(8, 9, $DisplayTotal, - 2 );
        $rep->Font('bold');
        $rep->SetFontSize(+8.75);

        $rep->multicell(300, 10, "In words : ".$myrow['curr_code'] . ":".convert_number_to_words($grossamount)." Only", 0, 'L', 0, 0, 48, 700);

        $rep->multicell(150, 10, "Declaration", 0, 'L', 0, 0, 50, 726);
        $rep->multicell(200, 10, "Amount Chargeable(in words)", 0, 'L', 0, 0, 50, 680);
        $rep->SetFontSize(-8.75);


        $rep->Font('');


        $total_1 = $myrow["ov_freight"] + $myrow["ov_gst"] +$myrow["ov_amount"]+$myrow["ov_freight_tax"];
        $words = price_in_words($myrow['Total'], ST_SALESINVOICE);
        $words1 = price_in_words($DisplayAmount, ST_SALESINVOICE);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }


        $rep->SetFontSize(+9.5);

        $rep->multicell(255, 30, "We declare that this invoice shows the actual price of the goods described and that all particulars are true and correct.", 0, 'L', 0, 0, 50, 740);
        $rep->multicell(525, 100, "", 1, 'L', 0, 0, 40, 677);
        $rep->multicell(100, 10, "Authorised Signatory", 0, 'R', 0, 0, 463, 753);
        $rep->multicell(150, 10, "Thank you for your business", 0, 'C', 0, 0, 210,785);
        $myrow3 = get_customer($myrow['debtor_no'], $i);



        $rep->Font('italic');
        $rep->multicell(100,10,"E. & O. E.",0,'R',0,0,450,725);
        $rep->multicell(80,10,"NTN# ".$rep->company['coy_no'],0,'L',0,0,42,125);
        $rep->SetFontSize(-9.5);

        $rep->Font('');



        //$rep->multicell(200, 80, "", 1, 'L', 0, 0, 365, 700);
        //$rep->multicell(250, 50, "Gross Amount", 0, 'L', 0, 0, 370, 720);
        //$rep->multicell(250, 50, "".number_format2($grossamount,$dec), 0, 'L', 0, 0, 450, 720);
        //$rep->multicell(250, 50, "Additional Discount", 0, 'L', 0, 0, 370, 730);
        //$rep->multicell(250, 50, "Carriage & freight", 0, 'L', 0, 0, 370, 740);
//$rep->multicell(250, 50, $myrow['ov_freight'], 0, 'L', 0, 0, 450, 740);


//$net_amount =$grossamount+$myrow['ov_freight'];
        //$rep->multicell(250, 50, "Net AMount", 0, 'L', 0, 0, 370, 750);
        //$rep->multicell(250, 50, "".number_format2($net_amount), 0, 'L', 0, 0, 450, 750);


        //$rep->multicell(150, 10, "Thank you for your business", 0, 'C', 0, 0, 210,785);

        $myrow3 = get_customer($myrow['debtor_no'], $i);


        //$rep->multicell(80,10,$myrow3['debtor_ref'],0,'l',0,0,348,198);
        $rep->Font();

//		$rep->Font('italic');
//		$rep->multicell(100,10,"E. & O. E.",0,'R',0,0,450,725);
//		$rep->multicell(80,10,"NTN# ".$rep->company['coy_no'],0,'L',0,0,42,155);
//		$rep->Font('');


        //$rep->multicell(80,10,get_ref_dev($myrow['type'],13),0,'L',0,0,490,130);


        if ($email == 1)
        {
            $rep->End($email);
        }
        //image
        //$rep->NewLine(39);

        ///$rep->row = $newrow;
    }
    if ($email == 0)
        $rep->End();
}

?>