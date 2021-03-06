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

function get_invoice_disc($trans_no)
{

    $sql = "SELECT *
		FROM ".TB_PREF."debtor_trans_details 
		WHERE debtor_trans_no=".db_escape($trans_no);


    return db_query($sql, "Cant retrieve invoice range");
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
	$cols = array(1,25, 170, 210, 250, 290, 310,340,405, 460);

	// $headers in doctext.inc
	$aligns = array('left','left',	'left',	'left', 'left', 'left', 'left', 'left', 'left', 'left','right');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('INVOICE'), "InvoiceBulk", user_pagesize(), 9, $orientation);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$range = get_invoice_range($from, $to);
	while($row = db_fetch($range)) {
        if (!exists_customer_trans(ST_SALESINVOICE, $row['trans_no']))
            continue;
        $sign = 1;
        $myrow = get_customer_trans($row['trans_no'], ST_SALESINVOICE);

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
        if ($email == 1) {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('INVOICE');
            $rep->filename = "Invoice" . $myrow['reference'] . ".pdf";
        }
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns);

        $contacts = get_branch_contacts($branch['branch_code'], 'invoice', $branch['debtor_no'], true);
        $baccount['payment_service'] = $pay_service;
        $rep->SetCommonData($myrow, $branch, $sales_order, $baccount, ST_SALESINVOICE, $contacts);
        $rep->SetHeaderType('Header10739');
        $rep->NewPage();
        // calculate summary start row for later use
        $s_no = 0;
        $summary_start_row = $rep->bottomMargin + (15 * $rep->lineHeight);

        if ($rep->formData['prepaid']) {
            $result = get_sales_order_invoices($myrow['order_']);
            $prepayments = array();
            while ($inv = db_fetch($result)) {
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
        $total_discount = 0;
        $s_no = 0;

        while ($myrow2 = db_fetch($result)) {
            if ($myrow2["quantity"] == 0)
                continue;

            $Net = round2($sign * ((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
                user_price_dec());
            $SubTotal += $Net;


            $DisplayPrice = number_format2($myrow2["unit_price"], $dec);
            $DisplayQty = number_format2($sign * $myrow2["quantity"], get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net, $dec);
            if ($myrow2["discount_percent"] == 0)
                $DisplayDiscount = "";
            else
                $DisplayDiscount = number_format2($myrow2["discount_percent"] * 100, user_percent_dec()) . "%";
            $s_no++;
            $rep->TextCol(0, 1, $s_no, -2);
            $oldrow = $rep->row;
            $rep->TextColLines(1, 2, $myrow2['StockDescription'], -2);
            $newrow = $rep->row;
            $rep->row = $oldrow;
            if ($Net != 0.0 || !is_service($myrow2['mb_flag']) || !$SysPrefs->no_zero_lines_amount()) {
                $rep->TextCol(6, 7, $DisplayQty, -2);
                $pref = get_company_pref();
//                $item=get_item($myrow2['stk_code']);
                if ($pref['alt_uom'] == 1) {
                    $rep->TextCol(4, 5, $myrow2['unit_price'], -2);
                } else {
                    $rep->TextCol(4, 5, $myrow2['units'], -2);
                }
///$DisplayPricebatch_id
//					display_error($myrow2['amount5']);
                $batch = get_batch_by_id($myrow2['batch']);
                $rep->TextCol(5, 6, $batch['name'], -2);
                $rep->TextCol(6, 7, $batch['exp_date'], -2);
                $rep->TextCol(8, 9, " " . $myrow2['amount5'], -2);
                $rep->TextCol(8, 9, " " . $myrow2['h_text5'], -2);
                $rep->TextCol(7, 8, $DisplayNet, -2);
                // $rep->TextCol(0, 11, "__________________________________________________________________________________________________________________________________________________________________________________________________", -2);


            }
            $rep->row = $newrow;
            //$rep->NewLine(1);
            if ($rep->row < $summary_start_row)
                $rep->NewPage();
        }

        $memo = get_comments_string(ST_SALESINVOICE, $row['trans_no']);
        if ($memo != "") {
            $rep->NewLine();
            // $rep->TextColLines(1, 5, $memo, -2);


        }


        $DisplaySubTot = number_format2($SubTotal, $dec);
        $total_discount += ($myrow["discount1"]);
        // set to start of summary line:
        $rep->row = $summary_start_row;
        if (isset($prepayments)) {
            // Partial invoices table
            $rep->TextCol(0, 3, _("Prepayments invoiced to this order up to day:"));
            // $rep->TextCol(0, 3, str_pad('', 150, '_'));
            $rep->cols[2] -= 20;
            $rep->aligns[2] = 'right';
            $rep->NewLine();
            $c = 0;
            $tot_pym = 0;
            // $rep->TextCol(0, 3, str_pad('', 150, '_'));
            $rep->TextCol($c++, $c, _("Date"));
            // $rep->TextCol($c++, $c,	_("Invoice reference"));
            $rep->TextCol($c++, $c, _("Amount"));

            foreach ($prepayments as $invoice) {
                if ($show_this_payment || ($invoice['reference'] != $myrow['reference'])) {
                    $rep->NewLine();
                    $c = 0;
                    $tot_pym += $invoice['prep_amount'];
                    $rep->TextCol($c++, $c, sql2date($invoice['tran_date']));
                    $rep->TextCol($c++, $c, $invoice['reference']);
                    $rep->TextCol($c++, $c, number_format2($invoice['prep_amount'], $dec));
                }
                if ($invoice['reference'] == $myrow['reference']) break;
            }
            // $rep->TextCol(0, 3, str_pad('', 150, '_'));
            $rep->NewLine();
            $rep->TextCol(1, 2, _("Total payments:"));
            $rep->TextCol(2, 3, number_format2($tot_pym, $dec));
        }

        $doctype = ST_SALESINVOICE;
        $rep->row = $summary_start_row;
        $rep->cols[2] += 20;
        $rep->cols[3] += 20;
        $rep->aligns[3] = 'left';

//			$rep->TextCol(3, 6, _("Sub-total"), -2);
//			$rep->TextCol(6, 7,	$DisplaySubTot, -2);
        $rep->NewLine(1);
        if ($myrow["discount1"] != 0) {
            $rep->TextCol(3, 6, _("Discount") . $myrow["discount_percent"], -2);
            $rep->TextCol(6, 7, "   " . price_format($total_discount), -2);
        }
        if ($myrow["discount2"] != 0) {
            $rep->TextCol(3, 6, _("Discount"), -2);
            $rep->AmountCol(6, 7, "  " . $myrow["discount2"], $dec);


        }

        $rep->NewLine();
        if ($myrow['ov_freight'] != 0.0) {
            $DisplayFreight = number_format2($sign * $myrow["ov_freight"], $dec);
            $rep->TextCol(3, 6, _("Shipping"), -2);
            $rep->TextCol(6, 7, $DisplayFreight, -2);
            $rep->NewLine();
        }
        $tax_items = get_trans_tax_details(ST_SALESINVOICE, $row['trans_no']);
        $first = true;

        while ($tax_item = db_fetch($tax_items)) {
            if ($tax_item['amount'] == 0)
                continue;
            $DisplayTax = number_format2($sign * $tax_item['amount'], $dec);

            if ($SysPrefs->suppress_tax_rates() == 1)
                $tax_type_name = $tax_item['tax_type_name'];
            else
//    				$tax_type_name = $tax_item['tax_type_name']." (".$tax_item['rate']."%) ";

                if ($myrow['tax_included']) {
                    if ($SysPrefs->alternative_tax_include_on_docs() == 1) {
                        if ($first) {
                            $rep->TextCol(3, 6, _("Total Tax Excluded"), -2);
                            $rep->TextCol(6, 7, number_format2($sign * $tax_item['net_amount'], $dec), -2);
                            $rep->NewLine();
                        }
                        $rep->TextCol(3, 6, $tax_type_name, -2);
//						$rep->TextCol(6, 7,	$DisplayTax, -2);
                        $first = false;
                    } else
                        $rep->TextCol(3, 7, _("Included") . " " . $tax_type_name . _("Amount") . ":", -2);
//                    $rep->TextCol(6, 7, $DisplayTax, -2);
                } else {
                    $rep->TextCol(3, 6, $tax_type_name, -2);
//					$rep->TextCol(6, 7,	$DisplayTax, -2);
                }
            $rep->NewLine();

        }

        $rep->NewLine(-16);
        $DisplayTotal = number_format2($sign * ($myrow["ov_freight"] + $myrow["ov_gst"] +
                $myrow["ov_amount"] + $myrow["ov_freight_tax"] - $myrow["discount1"] - $myrow["discount2"]), $dec);
        $rep->Font('bold');
        if (!$myrow['prepaid']) $rep->Font('bold');
        $rep->TextCol(7, 9, _("Total Amount        :-"), -2);
//			$rep->TextCol(10, 12, $DisplayTotal."  ", -2);
        $rep->NewLine(+1);
        $rep->TextCol(7, 9, _("Less Amount         :-"), -2);
        $rep->NewLine(+1);
        $rep->TextCol(9, 11, _("_______________"), -2);

        $rep->TextCol(0, 4, _("Less Amount Details"), -2);
        $rep->TextCol(0, 4, _("__________________"), -2);

        $rep->NewLine(+1);
        $rep->TextCol(7, 9, _("Net. Amount          :-"), -2);
        $rep->NewLine(+0.5);
        $rep->TextCol(9, 11, _("_______________"), -2);
        $result = get_invoice_disc($row['trans_no']);
        $rep->NewLine(2);
       $rep->TextCol(0, 3, _("Description"), -2);
        $rep->TextCol(5, 8, _("Values"), -2);
        $rep->Font('');
        while ($myrow3 = db_fetch($result)) {
            $rep->NewLine(1.5);
            $rep->TextCol(0, 3, $myrow3["description"], -2);
            $rep->TextCol(4, 6, $myrow3["quantity"], -2);
            $rep->TextCol(5, 9, $myrow3["unit_price"], -2);
            
        }
	}

	if ($email == 0)
		$rep->End();
}