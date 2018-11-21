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
function get_invoice_range($from, $to)
{
	global $SysPrefs;

	$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");

	$sql = "SELECT trans.trans_no, trans.reference
		FROM ".TB_PREF."debtor_trans trans 
			LEFT JOIN ".TB_PREF."voided voided ON trans.type=voided.type AND trans.trans_no=voided.id
		WHERE trans.type=".ST_SALESINVOICE
			." AND ISNULL(voided.id)"
			." AND trans.reference>=".db_escape(get_reference(ST_SALESINVOICE, $from))
			." AND trans.reference<=".db_escape(get_reference(ST_SALESINVOICE, $to))
		." ORDER BY trans.tran_date, trans.$ref";

	return db_query($sql, "Cant retrieve invoice range");
}
function get_customer_total_outstand_of($customer_id)
{
    $sql = "SELECT SUM( ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + gst_wh +  `alloc` ) AS Payments FROM ".TB_PREF."debtor_trans
 WHERE debtor_no=".db_escape($customer_id)."
	AND TYPE IN ( 10 )";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_customer_total_payment_of($customer_id)
{
    $sql = "SELECT SUM( ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + gst_wh +  `alloc` ) AS Payments FROM ".TB_PREF."debtor_trans
 WHERE debtor_no=".db_escape($customer_id)."
	AND TYPE IN ( 12,11,2 )";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_refs($id, $type)
{
    $sql = "SELECT reference FROM ".TB_PREF."refs
 WHERE id=".db_escape($id)."
	AND type=".db_escape($type)."";
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
function get_shipping_through($id)
{
    $sql = "SELECT shipper_name FROM ".TB_PREF."shippers
		WHERE shipper_id=".db_escape($id)."";
    $result = db_query($sql,"check failed");
    $row = db_fetch($result);
    $shipper_name = $row['shipper_name'];
    return $shipper_name;
}
function get_prepared_by_name($transtype,$trans)
{
    $sql = "SELECT  user  FROM ".TB_PREF."audit_trail"
        ." WHERE type=".db_escape($transtype)." AND trans_no="
        .db_escape($trans);

    $query= db_query($sql, "Cannot get all audit info for transaction");
    $fetch=db_fetch_row($query);
    return $fetch[0];
}
function get_user_name_1($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get user $user_id");

    $myrow = db_fetch($result);

    return $myrow[0];
}

function get_order_no($order_no)
{
    $sql = "SELECT disc1 , disc2 FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($order_no);

    $result = db_query($sql, "could not get user $user_id");

    $myrow = db_fetch($result);

    return $myrow[0];
}

function get_payment_terms_names_rep($id)
{
    $sql = "SELECT terms FROM ".TB_PREF."payment_terms WHERE terms_indicator =" .db_escape($id);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
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

print_invoices();

//----------------------------------------------------------------------------------------------------

function print_invoices()
{
	global $path_to_root, $SysPrefs;
	
	$show_this_payment = true; // include payments invoiced here in summary

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$pay_service = $_POST['PARAM_4'];
	$comments = $_POST['PARAM_5'];
	$customer = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

 	$fno = explode("-", $from);
	$tno = explode("-", $to);
	$from = min($fno[0], $tno[0]);
	$to = max($fno[0], $tno[0]);

	//-------------code-Descr-Qty--uom--tax--prc--Disc-Tot--//


	$cols = array(0, 17,80,120, 240, 290,285, 335, 330, 385, 410, 440, 490);

	// $headers in doctext.inc
    // $headers = array(_("S.No"),_("BARCODE"), _("Brand"), _("Description"), _("Pack"),
    //     _(""), _("Ordered"), _("Scheme"),  _("Unit"),  _("Rate"),_("Trade"),  _("Disc"),  _("Scheme"));

    $aligns = array('left',	'left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'right','right','right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 7, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$range = get_invoice_range($from, $to);
	while($row = db_fetch($range))
	{
			if (!exists_customer_trans(ST_SALESINVOICE, $row['trans_no']))
				continue;
			$sign = 1;
			$myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE);
				// $myrow3 = get_sales_order_header($row['trans_no'], ST_SALESINVOICE);
            $Outstanding=get_customer_total_outstand_of($myrow['debtor_no']);
            $payment=get_customer_total_payment_of($myrow['debtor_no']);
			if ($customer && $myrow['debtor_no'] != $customer) {
				continue;
			}
			if ($currency != ALL_TEXT && $myrow['curr_code'] != $currency) {
				continue;
			}
			$baccount = get_default_bank_account($myrow['curr_code']);
			$params['bankaccount'] = $baccount['id'];

			$branch = get_branch($myrow["branch_code"]);
			$sales_order = get_sales_order_header($myrow["order_"], ST_SALESORDER);
			if ($email == 1)
			{
				$rep = new FrontReport("", "", user_pagesize(   ), 7, $orientation);
				$rep->title = _('INVOICE');
				$rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
			}
			$rep->currency = $cur;
			$rep->Font();

			$contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
			$baccount['payment_service'] = $pay_service;
			$rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);

        $rep->Info($params, $cols, $headers, $aligns);

        $rep->SetHeaderType('Header10751');
			$rep->NewPage();
			// calculate summary start row for later use
			$summary_start_row = $rep->bottomMargin + (8 * $rep->lineHeight);

			if ($rep->formData['prepaid'])
			{
				$result = get_sales_order_invoices($myrow['order_']);
				$prepayments = array();
				while($inv = db_fetch($result))
				{
					$prepayments[] = $inv;
					if ($inv['trans_no'] == $row['trans_no'])
					break;
				}

				if (count($prepayments) > ($show_this_payment ? 0 : 1))
					$summary_start_row += (count($prepayments)) * $rep->lineHeight;
				else
					unset($prepayments);
			}

   			$result = get_customer_trans_details(ST_SALESINVOICE, $row['trans_no']);

			$SubTotal = 0;
        $s=1;
        $totqty = 0;
        $tottrade = 0;
        $net_amount1 = 0;
        $total_scheme1 = 0;
        $total_qty_issued_2 = 0;
        $scheme_total = 0;
        $total_qty_issued = 0;
        $rep->NewLine(1);
//        $logo = company_path() . "/images/" . $myrow['coy_logo'];
        $logo = company_path() . "/images/IBP_Logo_1.png";
//display_error(company_path());
//        if ($myrow['coy_logo'] != '' && file_exists($logo))
//        {
//            $rep->AddImage($logo, 40, $rep->row,$myrow['logo_w'], $myrow['logo_h']);
//            $rep->AddImage($logo, $myrow['logo_w'], $myrow['logo_h']);
            $rep->AddImage($logo, 40,  770, -40, 50.5);

//        }
//        else
//        {
//            $this->fontSize += 4;
//            $this->Font('bold');
//	$this->Text($ccol, $this->company['coy_name'], $icol);
//            $this->Font();
//            $this->fontSize -= 4;
//        }
        $rep->setfontsize(15);
        $rep->multicell(300,20,"Invoice " ,0,'L',0,0,450,20);
        $rep->setfontsize(7);
        $myformatdoc_date =date('d F, Y', strtotime($myrow['document_date']));

        $rep->multicell(300,20,"Invoice Date: " ,0,'L',0,0,435,80);
        $rep->multicell(90,20,"".$myformatdoc_date ,0,'R',0,0,478,80);
        $rep->multicell(300,20,"Invoice No:" ,0,'L',0,0,435,65);

        $myformatpo_date =date('d F, Y', strtotime($myrow['po_date']));
        $rep->multicell(135,19, $myrow['address']."",0,'L',0,0,40,135);

        $myformatdue_date =date('d F, Y', strtotime($myrow['due_date']));
        $myformatso_date =date('d F, Y', strtotime($myrow['ord_date']));

        $so = get_sales_order_header($myrow['order_'],30);
        $rep->multicell(135,20,"".$so['delivery_address'] ,0,'L',0,0,435,135);

        $delivery = get_customer_trans($myrow['trans_no'],13);
        $rep->MultiCell(300,19, "".$so['phone'],0,'L',0,0,130,260);

        $rep->MultiCell(410, 30, "".$so['h_text3'] ,0, 'L', 0, 2, 294,260, true);//sale officer
        $rep->MultiCell(410, 30, "".$so['h_text4'] ,0, 'L', 0, 2, 215,260, true);//sale officer
        $myformatpo_date =date('d F, Y', strtotime($so['po_date']));

// display_error($so['po_date']);

        $rep->MultiCell(100, 10, "".$myformatpo_date ,0, 'L', 0, 2, 385,215, true);//po date
        $myformatdo_date =date('d F, Y', strtotime($delivery['tran_date']));
        $rep->multicell(200,19, "".$myformatdue_date,0,'L',0,0,479,260);
        $rep->MultiCell(90, 20, "".$so['h_text5'] ,0, 'L', 0, 2, 490,215, true);
        $rep->multicell(200,19, "".$so['reference'],0,'L',0,0,70,215);
        $rep->multicell(200,19, "".$delivery['reference'],0,'L',0,0,225,215);
        $rep->multicell(200,19, "".$myformatdo_date,0,'L',0,0,300,215);
        $rep->multicell(200,19, "".$myformatso_date,0,'L',0,0,138,215);
        $rep->multicell(150,19, $myrow['DebtorName'],0,'L',0,0,120,120);
        $rep->multicell(200,19, "".get_payment_terms_names_rep($myrow['payment_terms']),0,'L',0,0,390,260);
        $rep->multicell(100,10,"Invoice To" ,0,'C',1,0,40,80);
        $rep->multicell(100,10,"CUSTOMER ID:" ,0,'L',0,0,40,120);
        $rep->multicell(130,10,"Shipping Address" ,0,'C',1,0,435,115);
//
//2n$repumn
        $rep->SetDrawColor(169,169,169);
        $rep->multicell(525,20,"" ,0,'L',1,0,40,190);
        $rep->multicell(525,20,"" ,0,'L',1,0,40,235);
        $rep->SetTextColor(0,0,0);

        $rep->multicell(100,40,"P.O ISSUED BY" ,0,'L',0,0,50,240);
        $rep->multicell(120,40,"CONTACT NO" ,0,'L',0,0,130,240);
        $rep->multicell(120,40,"SALES OFFICER " ,0,'L',0,0,203,240);
        $rep->multicell(115,40,"SALES OFF NO" ,0,'L',0,0,295,240);
        $rep->multicell(115,40,"PAYMENT TERMS" ,0,'L',0,0,370,240);
        $rep->multicell(115,40,"PAYMENT DUE DATE" ,0,'L',0,0,468,240);
        $rep->Font();
        $rep->multicell(100,40,$so['f_comment1'],0,'L',0,0,50,260);
        $rep->multicell(100,40,$so['h_text2'],0,'L',0,0,135,260);
        $rep->multicell(100,40,"S.O NO" ,0,'L',0,0,70,195);
        $rep->multicell(120,40,"S.O DATE" ,0,'L',0,0,140,195);
        $rep->multicell(120,40,"D.O NO" ,0,'L',0,0,230,195);
        $rep->multicell(115,40,"D.O DATE" ,0,'L',0,0,305,195);
        $rep->multicell(115,40,"P.O DATE" ,0,'L',0,0,390,195);
        $rep->multicell(115,40,"P.O NO" ,0,'L',0,0,490,195);


        $rep->multicell(115,40,"S.No" ,0,'L',0,0,41,285);
        $rep->multicell(115,40,"BARCODE" ,0,'L',0,0,65,285);
        $rep->multicell(115,40,"Brand" ,0,'L',0,0,123,285);
        $rep->multicell(115,40,"Description" ,0,'L',0,0,160,285);
        $rep->multicell(115,40,"Pack" ,0,'L',0,0,283,285);
        $rep->multicell(115,40,"Ordered" ,0,'L',0,0,321,285);
        $rep->multicell(115,40,"Scheme" ,0,'L',0,0,354,285);
        $rep->multicell(115,40,"Unit" ,0,'L',0,0,390,285);
        $rep->multicell(115,40,"Rate" ,0,'L',0,0,430,285);
        $rep->multicell(115,40,"Trade" ,0,'L',0,0,465,285);
        $rep->multicell(115,40,"Disc" ,0,'L',0,0,510,285);
        $rep->multicell(115,40,"Scheme" ,0,'L',0,0,535,285);



        while ($myrow2=db_fetch($result))
			{
			    $item=get_item($myrow2['stk_code']);
				//display_error($item);
			     $pref = get_company_prefs();

		     if($pref['alt_uom'] == 1 && $item['units'] != $myrow2['units_id'])
		    	$qty=$myrow2['quantity'] * $myrow2['con_factor'];
		    	else
		    	$qty=$myrow2['quantity'];

				if ($qty == 0)
					continue;

				$Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $qty),
				   user_price_dec());
				$SubTotal += $Net;
	    		$DisplayPrice = number_format2($myrow2["unit_price"],$dec);
                $DisplayQty = number_format2($sign*$qty,get_qty_dec($myrow2['stock_id']));
                $trade_value =  $myrow2["unit_price"] * $qty;
                // $trade_value =  $myrow2["unit_price"] * $qty * ( 1 -  $myrow2['discount_percent']);

                $total_scheme = $myrow2['bonus'] * $myrow2["unit_price"];
                if( $pref['disc_in_amount'] == 1) {
                    $line_discount = $myrow2['discount_percent'];
                }
                else{

                    $line_discount = $myrow2['discount_percent'] * 100 ."%";
                    $total_dics += ($trade_value * ($myrow2['discount_percent'] ));
                    // display_error($total_dics);
                }
                $DisplayNet = number_format2($Net,$dec);
	    		if ($myrow2["discount_percent"]==0)
		  			$DisplayDiscount ="";
	    		else
		  			$DisplayDiscount = number_format2($myrow2["discount_percent"]*100,user_percent_dec()) . "%";
				$c=0;
				    $DisplayAmount3 = number_format2($myrow2["amount3"],user_qty_dec());
				$rep->TextCol(0, 1,	$s++, -2);
				$oldrow = $rep->row;
				$rep->TextCol(1, 2,	$myrow2['text3'], -2);
//				$rep->TextCol(1, 2,	$myrow2['bonus'], -2);
				$rep->TextCol(7, 8,	"    ".$myrow2['bonus'], -2);
                $scheme_total +=$myrow2['bonus'];
                $rep->TextCollines(3, 4, $myrow2['StockDescription'], -2);

                $newrow = $rep->row;
				$rep->row = $oldrow;
				if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount())
				{

                    $rep->TextCol(4, 5, $myrow2['text4']." Gms", -2);
                    $rep->Amountcol(5, 6,$qty, $dec);
                    $totqty +=$qty;
                    if($pref['alt_uom'] == 1)
                    {
                        $rep->TextCol(8, 9, $myrow2['units_id'], -2);
                    }
                    else
                    {
                        $rep->TextCol(8, 9, $myrow2['units'], -2);
                    }
                    $item=get_item($myrow2['stk_code']);
//                    display_error($myrow2['discount_percent']);
                    $rep->TextCol(11, 12, $line_discount, -2);
					$rep->amountcol(10,11,	$trade_value, -2);
                    $trade_value1 += $trade_value;
                    $tottrade +=$trade_value;
                    $tot_amount3 += $myrow2["amount3"];
                    if($pref['alt_uom'] == 1)
                    {
                        $rep->TextCol(9, 10, $DisplayPrice , -2);
                    }
                    else
                    {
                        $rep->TextCol(9, 10,$DisplayPrice, -2);
                    }
                    $rep->TextCol(12, 13, $total_scheme, -2);
                    $total_scheme1 +=$total_scheme;
                    $disc_amount = $trade_value * $myrow2["discount_percent"];
                    // $total_qty_issued = ($qty) / $myrow2['carton'];
                    // $total_qty_issued_2 += $total_qty_issued;
                    $total_qty_issued_qty_wali = $qty / $myrow2['carton'];
                    $total_qty_issued_scheme = $myrow2['bonus'] / $myrow2['carton'];
                    $total_qty_issued_2 += $total_qty_issued_qty_wali;
                    $total_qty_issued_scheme_2 += $total_qty_issued_scheme;



					$rep->TextCol(2, 3,	get_category_name($myrow2['category_id']), -2);

                }

                 $rep->row = $newrow;
//				 $rep->NewLine();
				if ($rep->row < $summary_start_row)
                    if ($rep->row < $rep->bottomMargin + (2 * $rep->lineHeight))
					$rep->NewPage();
			}

        $rep->NewLine(+2);
//        display_error($total_dics);
        $total_gross1 = $trade_value1  ;
        $sumof_discount_value = $total_dics;
        if ($myrow['ov_freight'] != 0.0)
        {
            $DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
        }
        $total_net = $total_gross1 - $sumof_discount_value + $myrow['ov_freight'] - $myrow['discount1'];
        $rep->Font('bold');
        $rep->TextCol(5, 7, "      ".price_format($totqty) , - 2);
                $rep->TextCol(7, 8, price_format($scheme_total) , - 2);

        $rep->TextCol(9,11, "  ".price_format($trade_value1) , - 2);
        $rep->TextCol(12,13, price_format($total_scheme1) , - 2);
        $rep->Font('');
        $rep->NewLine(2);
        $rep->Font('bold');
        $rep->TextCol(0,7, "Rupees ".convert_number_to_words(abs($total_net))." Only" , - 2);
        $rep->Font('');
        $rep->NewLine();
        $rep->TextCol(0,7, "1- Claims in respect of this invoice must be received in writing to us within" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "     three days from date of delivery, after which no complains will be solicited" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "2- Make all cheques payable to M/s. International Brandz Pakistan. " , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "3- If you have any questions concering this invoice, or any other please" , - 2);
        $rep->NewLine(1);
        $rep->TextCol(0,7, "    free to contact us" , - 2);
        $rep->NewLine(1.5);
        $rep->TextCol(0,7, "IBP@SAPGROUP.COM.PK / +92 300 821 81 64" , - 2);
        $rep->NewLine(-2);

        $rep->NewLine(-3);

        $rep->TextCol(6, 10, "TOTAL TRADE VALUE	" , - 2);


        $rep->TextCol(11, 13, number_format2($trade_value1) , - 2);
        $rep->NewLine();
        $rep->TextCol(6, 10, "Total Scheme Value	" , - 2);
        $rep->TextCol(11, 13, price_format($total_scheme1) , - 2);
        $rep->TextCol(10, 11, "".price_format($scheme_total)."%", - 2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(6, 10, "Total Gross Amount	" , - 2);
        $rep->TextCol(11, 13, number_format2($total_gross1) , - 2);
        $rep->Font('');

        $rep->NewLine();
        $rep->TextCol(6, 10, "Total Discount Value 	" , - 2);
        $rep->TextCol(11, 13, number_format2($total_dics) , - 2);
        $rep->NewLine();
        $rep->TextCol(6, 10, "Damage & Expiry 	" , - 2);
        $rep->TextCol(11, 13, price_format($myrow['discount1']) , - 2);

        $rep->NewLine();
        $rep->TextCol(6, 10, "SHIPPING & HANDLING 	" , - 2);
        $rep->TextCol(11, 13, $DisplayFreight , - 2);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(6, 10, "TOTAL NET AMOUNT 	" , - 2);
        $rep->TextCol(11, 13, price_format($total_net) , - 2);
        $rep->Font('');
        $rep->NewLine();
        $rep->Font('b');
        $rep->NewLine();

        $logo1 = company_path() . "/images/img.PNG";
//        $rep->NewLine(3);


// 		$rep->AddImage($logo1, $rep->cols[1] -55, $rep->row -478, 510,20);
//        $rep->AddImage($logo1, $rep->cols[1] +300, $rep->row +225, null,$rep->company['logo_w'], $rep->company['logo_h']);
        $rep->NewLine();
        $rep->AddImage($logo1, 40, $rep->row, -20, 20.5);
                $rep->NewLine(-0.5);

        $rep->TextCol(1, 3, "DRIVER / SALESMAN" , - 2);
        $rep->TextCol(3,4, "           SALES MAN" , - 2);
        $rep->TextCol(4,6, "VEHICLE" , - 2);
        $rep->TextCol(7,9, "VEHICLE NO" , - 2);
        $rep->TextCol(10,13, "TOTAL QTY ISSUED(CTN)" , - 2);

        $rep->NewLine(2);
        $rep->TextCol(3,4, "          ".$sales_order['f_text2'] , - 2);
        $rep->TextCol(1,3, $sales_order['f_text1'] , - 2);
        $rep->TextCol(4,6, $sales_order['f_text3'] , - 2);
        $rep->TextCol(7,9, $sales_order['f_text4'], - 2);
        // $rep->TextCol(10,13,  price_format($total_qty_issued_2), - 2);

        $rep->TextCol(10,13,  price_format($total_qty_issued_2 + $total_qty_issued_scheme_2), - 2);

        $rep->NewLine(3);
        $rep->AddImage($logo1, 40, $rep->row , -10, 20.5);
        $rep->NewLine(-0.5);
        $rep->TextCol(1, 3, "TRANSPORTER NAME" , - 2);
        $rep->TextCol(3,4, "             TR NO" , - 2);
        $rep->TextCol(4,6, "TR DATE" , - 2);
//    $rep->TextCol(7,9, "VEHICLE NO" , - 2);
        $rep->TextCol(10,13, "TR QUANTITY (CTN)" , - 2);
        $rep->NewLine(1.5);
        $rep->TextCol(10,13, $sales_order['f_text9'] , - 2);
        $rep->TextCol(4,7, $sales_order['f_text8'] , - 2);
        $rep->TextCol(3,4,"             ". $sales_order['f_text7'], - 2);
        $rep->TextCol(1,3, $sales_order['f_text6'], - 2);




//
//        $rep->NewLine();

        $rep->NewLine();
        $rep->TextCol(9, 12, "Receiver Details" , - 2);
        $rep->NewLine();
        $rep->NewLine();
//        $rep->NewLine(-5);
        $rep->TextCol(9, 13, "Receiver Name    ___________________" , - 2);
        $rep->NewLine(2);
        $rep->TextCol(9, 13, "Receiver Date    ___________________" , - 2);
        $rep->NewLine(3);
        $rep->TextCol(8, 13, "              Receiver Stamp & Signature" , - 2);

//
        $rep->TextCol(1, 3, "___________________" , - 2);
        $rep->TextCol(2, 6, "                     ___________________" , - 2);
        $rep->TextCol(2, 9, "                                                                           ________________________" , - 2);
        $rep->NewLine();
        $rep->TextCol(1, 3, "          Prepared By" , - 2);
        $rep->TextCol(3, 6, "               Verified  By" , - 2);
        $rep->TextCol(4, 9, "Authorized Signatory	" , - 2);





//        $rep->NewLine(+5);
//        $rep->MultiCell(410, 30, price_format($totqty) ,0, 'L', 0, 2, 325,410, true);
//        $rep->MultiCell(410, 30, "".price_format($trade_value1) ,0, 'L', 0, 2, 480,410, true);
//        $rep->MultiCell(410, 30, "".price_format($scheme_total) ,0, 'L', 0, 2, 360,410, true);
//        $rep->MultiCell(410, 30, "dfhfg".price_format($total_scheme1) ,0, 'L', 0, 2, 545,410, true);

//        $rep->MultiCell(500,20,  price_format($total_qty_issued_2) ,0,'L',0,0,493,555);
                $rep->Font('');

			$memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
			if ($memo != "")
			{
				$rep->NewLine(-9);
//				$rep->MultiCell(800,20,"Remarks".$memo ,0,'L',0,0,40,160);
			}
   			$DisplaySubTot = number_format2($SubTotal,$dec);

			// set to start of summary line:
    		$rep->row = $summary_start_row;
			if (isset($prepayments))
			{
				// Partial invoices table
				$rep->TextCol(0, 3,_("Prepayments invoiced to this order up to day:"));
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->cols[2] -= 20;
				$rep->aligns[2] = 'right';
				$rep->NewLine(); $c = 0; $tot_pym=0;
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->TextCol($c++, $c, _("Date"));
				$rep->TextCol($c++, $c,	_("Invoice reference"));
				$rep->TextCol($c++, $c,	_("Amount"));

				foreach ($prepayments as $invoice)
				{
					if ($show_this_payment || ($invoice['reference'] != $myrow['reference']))
					{
						$rep->NewLine();
						$c = 0; $tot_pym += $invoice['prep_amount'];
						$rep->TextCol($c++, $c,	sql2date($invoice['tran_date']));
						$rep->TextCol($c++, $c,	$invoice['reference']);
						$rep->TextCol($c++, $c, number_format2($invoice['prep_amount'], $dec));
					}
					if ($invoice['reference']==$myrow['reference']) break;
				}
				$rep->TextCol(0, 3,	str_pad('', 150, '_'));
				$rep->NewLine();
				$rep->TextCol(1, 2,	_("Total payments:"));
				$rep->TextCol(2, 3,	number_format2($tot_pym, $dec));
			}


			$doctype = ST_SALESINVOICE;
    		$rep->row = $summary_start_row;
			$rep->cols[2] += 20;
			$rep->cols[3] += 20;
			$rep->aligns[3] = 'left';

//			$rep->TextCol(3, 6, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
//		$rep->NewLine();
        $total_discount += ($myrow["discount1"]);


			if ($myrow['ov_freight'] != 0.0)
			{
   				$DisplayFreight = number_format2($sign*$myrow["ov_freight"],$dec);
//				$rep->TextCol(3, 6, _("Shipping"), -2);
//				$rep->Amountcol(6, 7,	$DisplayFreight, -2);
//				$rep->NewLine();
			}
		if($myrow['discount1']) {
// 			$rep->TextCol(3, 6, _("Discount 1"), -2);
// 			$rep->Amountcol(6, 7, $myrow['discount1'], $dec);
 $discount_value =$myrow["disc1"]/100;
//        $rep->MultiCell(410, 30, $myrow['discount1']."%" ,0, 'R', 0, 2, 90,478, true);

                    // $rep->MultiCell(410, 30, "gfj".price_format(get_order_no($myrow["disc1"]))."%" ,0, 'R', 0, 2, 403,465, true);

//			$rep->NewLine();
		}
		if($myrow['discount2']) {
// 			$rep->TextCol(4, 5, _("Discount 2"), -2);
// 			$rep->Amountcol(5, 6, $myrow['discount2'], $dec);
 $discount_value =$myrow["disc2"]/100;
//         $rep->MultiCell(410, 30, $myrow['discount2']."%" ,0, 'R', 0, 2, 90,490, true);


            $tot_amt =$tot_net - $myrow['discount2'];
            //$rep->NewLine();
		}
		else{
//            $rep->multicell(150,35,number_format2($tot_net,get_qty_dec($myrow2['stock_id'])),1,'R',0,0,415,692);
//            $rep->multicell(30,35,"Total:",0,'l',0,0,415,692);
            $tot_amt = $tot_net;
        }
			$tax_items = get_trans_tax_details(ST_SALESINVOICE, $row['trans_no']);
			$first = true;
    		while ($tax_item = db_fetch($tax_items))
    		{
    			if ($tax_item['amount'] == 0)
    				continue;
    			$DisplayTax = number_format2($sign*$tax_item['amount'], $dec);

    			if ($SysPrefs->suppress_tax_rates() == 1)
    				$tax_type_name = $tax_item['tax_type_name'];
    			else
    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

    			if ($myrow['tax_included'])
    			{
    				if ($SysPrefs->alternative_tax_include_on_docs() == 1)
    				{
    					if ($first)
    					{
//							$rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
//							$rep->TextCol(6, 7,	number_format2($sign*$tax_item['net_amount'], $dec), -2);
							$rep->NewLine();
    					}
//						$rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
						$first = false;
    				}
//    				else
//						$rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ": " . $DisplayTax, -2);
				}
    			else
    			{
//					$rep->TextCol(3, 6, $tax_type_name, -2);
//					$rep->TextCol(6, 7,	$DisplayTax, -2);
				}
				$rep->NewLine();
    		}

    	$prepared_by = get_prepared_by_name($myrow['type'], $myrow['trans_no']);
        $customer_record = get_customer_balance($myrow['debtor_no']);
        $total_balance=$customer_record["Balance"];
        $previous_balance = $total_balance - $tot_net;
//        $tot_balance=($tot_amt + $previous_balance);
//        $rep->multicell(100,20,"",0,'R',0,0,265,451);
        $rep->font('');


        // $rep->MultiCell(500,20, "gvh".$sales_order['f_text5'],0,'L',0,0,490,555);
        // display_error($sales_order['f_comment3']);


            $rep->setfontsize(8);
    		$rep->NewLine();
			$DisplayTotal = number_format2($sign*($myrow["ov_freight"] + $myrow["ov_gst"] +
				$myrow["ov_amount"]+$myrow["ov_freight_tax"] - ($myrow['discount1'] + $myrow['discount2'])),$dec);
			$rep->Font('bold');
			if (!$myrow['prepaid']) $rep->Font('bold');
//		$rep->NewLine(-15);


//display_error()
//$total_scheme_percent = $total_scheme1 / $trade_value1 * 100;

// $rep->MultiCell(200, 10, "Total Scheme Value" ,0, 'L', 0, 2, 349,439, true);
// $rep->MultiCell(100, 10, "fdg".price_format($scheme_total)."%" ,0, 'R', 0, 2, 403,439, true);

// display_error($discount_value);
//        $rep->MultiCell(410, 30, "dsf".number_format2($trade_value1) ,0, 'R', 0, 2, 149,430, true);
        // $scheme = $trade_value1 * 0.12;
//        $rep->MultiCell(410, 30, "cv".number_format2($total_scheme1) ,0, 'R', 0, 2, 149,439, true);

//        $rep->MultiCell(410, 30, "".abs($total_gross1) ,0, 'R', 0, 2, 149,450, true);
        // $tot_discount_value = $total_gross1 * 0.1;
//        $rep->MultiCell(410, 30, number_format2($sumof_discount_value),0, 'R', 0, 2, 145,465, true);
//           $rep->MultiCell(410, 30, number_format2($DisplayFreight) ,0, 'R', 0, 2, 147,490, true);

        // display_error($total_gross1);
//        $rep->MultiCell(410, 30, abs($total_net) ,0, 'R', 0, 2, 149,505, true);

//		$rep->MultiCell(250,15,"Rupees ".convert_number_to_words(abs($total_net))." Only",0,'L',0,0,50,425);
//			if ($rep->formData['prepaid'])
//			{
//				$rep->NewLine();
//				$rep->Font('bold');
////				$rep->TextCol(3, 6, $rep->formData['prepaid']=='final' ? _("THIS INVOICE") : _("TOTAL INVOICE"), - 2);
////				$rep->TextCol(6, 7, number_format2($myrow['prep_amount'], $dec), -2);
//			}
//
//			$words = price_in_words($rep->formData['prepaid'] ? $myrow['prep_amount'] : $myrow['Total']
//				, array( 'type' => ST_SALESINVOICE, 'currency' => $myrow['curr_code']));
//			if ($words != "")
//			{
//				$rep->NewLine(1);
////				$rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
//			}
			$rep->Font();
			if ($email == 1)
			{
				$rep->End($email);
			}


				//	$net_amount1  += $net_amount;
//				$rep->NewLine(-6.3);
//					$rep->TextCol(10,11, $tottrade, -2);
//						$rep->TextCol(5, 6, $myrow2['units'], -2);
//						$rep->TextCol(11,12, $total_scheme1, -2);
//    			$rep->TextCol(6, 7, "".price_format($totqty), -2);
//



	}

//    $myformatdoc_date =date('d F, Y', strtotime($myrow['document_date']));
//
//    $rep->multicell(300,20,"Invoice Date: " ,0,'L',0,0,435,80);
//    $rep->multicell(90,20,"".$myformatdoc_date ,0,'R',0,0,478,80);
//    $rep->multicell(300,20,"Invoice No:" ,0,'L',0,0,435,65);
//    $rep->multicell(90,19, "".$myrow['document_number'],0,'R',0,0,478,65);
//
//    $myformatpo_date =date('d F, Y', strtotime($myrow['po_date']));
//    $rep->multicell(135,19, $myrow['address']."",0,'L',0,0,40,135);
////$this->multicell(200,19, "".get_salesman_name($this->formData['salesman']),0,'L',0,0,90,210);
//

//    $myformatdue_date =date('d F, Y', strtotime($myrow['due_date']));
//    $myformatso_date =date('d F, Y', strtotime($myrow['ord_date']));
////display_error($this->formData['due_date']);
////display_error($Payment_Terms1);
//    $so = get_sales_order_header($myrow['order_'],30);
//    $rep->multicell(135,20,"".$so['delivery_address'] ,0,'L',0,0,435,135);
//
//    $delivery = get_customer_trans($myrow['trans_no'],13);
//    $rep->MultiCell(300,19, "".$so['phone'],0,'L',0,0,130,260);
//
//    $rep->MultiCell(410, 30, "".$so['h_text3'] ,0, 'L', 0, 2, 294,260, true);//sale officer
//    $rep->MultiCell(410, 30, "".$so['h_text4'] ,0, 'L', 0, 2, 215,260, true);//sale officer
//    $myformatpo_date =date('d F, Y', strtotime($so['po_date']));
//
//// display_error($so['po_date']);
//
//    $rep->MultiCell(100, 10, "".$myformatpo_date ,0, 'L', 0, 2, 385,215, true);//po date
//
//
//    $myformatdo_date =date('d F, Y', strtotime($delivery['tran_date']));
//
//    $rep->multicell(200,19, "".$myformatdue_date,0,'L',0,0,479,260);
//    $rep->MultiCell(90, 20, "".$so['h_text5'] ,0, 'L', 0, 2, 490,215, true);
//
//    $rep->multicell(200,19, "".$so['reference'],0,'L',0,0,70,215);
//    $rep->multicell(200,19, "".$delivery['reference'],0,'L',0,0,225,215);
//    $rep->multicell(200,19, "".$myformatdo_date,0,'L',0,0,300,215);
//    $rep->multicell(200,19, "".$myformatso_date,0,'L',0,0,138,215);
//    $rep->multicell(150,19, $myrow['DebtorName'],0,'L',0,0,120,120);
//
//    $rep->multicell(100,10,"Invoice To" ,0,'C',1,0,40,80);
//    $rep->multicell(100,10,"CUSTOMER ID:" ,0,'L',0,0,40,120);
//    $rep->multicell(130,10,"Shipping Address" ,0,'C',1,0,435,115);
////
////2n$repumn
//    $rep->multicell(525,20,"" ,0,'L',1,0,40,190);
//    $rep->multicell(525,20,"" ,0,'L',1,0,40,235);
//    $rep->multicell(100,40,"P.O ISSUED BY" ,0,'L',0,0,50,240);
//    $rep->multicell(120,40,"CONTACT NO" ,0,'L',0,0,130,240);
//    $rep->multicell(120,40,"SALES OFFICER " ,0,'L',0,0,203,240);
//    $rep->multicell(115,40,"SALES OFF NO" ,0,'L',0,0,295,240);
//    $rep->multicell(115,40,"PAYMENT TERMS" ,0,'L',0,0,370,240);
//    $rep->multicell(115,40,"PAYMENT DUE DATE" ,0,'L',0,0,468,240);
//    $rep->Font();
//    $rep->multicell(100,40,$so['f_comment1'],0,'L',0,0,50,260);
//    $rep->multicell(100,40,$so['h_text2'],0,'L',0,0,135,260);
//    $rep->multicell(100,40,"S.O NO" ,0,'L',0,0,70,195);
//    $rep->multicell(120,40,"S.O DATE" ,0,'L',0,0,140,195);
//    $rep->multicell(120,40,"D.O NO" ,0,'L',0,0,230,195);
//    $rep->multicell(115,40,"D.O DATE" ,0,'L',0,0,305,195);
//    $rep->multicell(115,40,"P.O DATE" ,0,'L',0,0,390,195);
//    $rep->multicell(115,40,"P.O NO" ,0,'L',0,0,490,195);

	if ($email == 0)


    $rep->End();
}

