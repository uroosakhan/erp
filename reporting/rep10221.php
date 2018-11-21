<?php

$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Aged Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_customer_analysis();

function get_invoices($customer_id, $to, $all=true,$types)
{
    $todate = date2sql($to);
    $PastDueDays1 = get_company_pref('past_due_days');
    $PastDueDays2 = 2 * $PastDueDays1;

    // Removed allocated from sql
    //Ryan :06-05-17
    if ($all)
        $value = "(ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + trans.gst_wh +
		trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc + trans.discount1 - trans.discount2)";
    else
        $value = "(ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount +	trans.gst_wh  + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc + trans.discount1 - trans.discount2 - alloc)";
    $sign = "IF(`type` IN(".implode(',',  array(ST_CPV,ST_CUSTCREDIT,ST_CRV,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
    $due = "IF (type=".ST_SALESINVOICE.", due_date, tran_date)";

    $sql = "SELECT type, reference, tran_date, due_date,
		$sign*$value as Balance,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$sign*$value,0) AS Due,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays1,$sign*$value,0) AS Overdue1,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays2,$sign*$value,0) AS Overdue2

		FROM ".TB_PREF."debtor_trans trans

		WHERE type <> ".ST_CUSTDELIVERY."
			AND debtor_no = $customer_id 
			AND tran_date <= '$todate'
			AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh +
trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) > ".FLOAT_COMP_DELTA." ";
    if (!$all)
        $sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh +
		trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA." ";
  if($types != -1)
    $sql .= "AND type =".$types;

    return db_query($sql, "The customer transactions could not be retrieved");
}

function get_salesman_names($id)
{
    $sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
//----------------------------------------------------------------------------------------------------

function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array, $SysPrefs;

    $to = $_POST['PARAM_0'];
    $fromcust = $_POST['PARAM_1'];
    $folk = $_POST['PARAM_2'];
    $currency = $_POST['PARAM_3'];
    $types = $_POST['PARAM_4'];
    $show_all = $_POST['PARAM_5'];
    $summaryOnly = $_POST['PARAM_6'];
    $no_zeros = $_POST['PARAM_7'];
    $graphics = $_POST['PARAM_8'];
    $comments = $_POST['PARAM_9'];
    $orientation = $_POST['PARAM_10'];
    $destination = $_POST['PARAM_11'];
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $orientation = ($orientation ? 'L' : 'P');
    if ($graphics)
    {
        include_once($path_to_root . "/reporting/includes/class.graphic.inc");
        $pg = new graph();
    }

    if ($fromcust == ALL_TEXT)
        $from = _('All');
    else
        $from = get_customer_name($fromcust);
    $dec = user_price_dec();

    if ($summaryOnly == 1)
        $summary = _('Summary Only');
    else
        $summary = _('Detailed Report');
    if ($currency == ALL_TEXT)
    {
        $convert = true;
        $currency = _('Balances in Home Currency');
    }
    else
        $convert = false;
    if ($folk == ALL_NUMERIC)
        $folk = 0;
    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');
    if ($show_all) $show = _('Yes');
    else $show = _('No');

    $PastDueDays1 = get_company_pref('past_due_days');
    $PastDueDays2 = 2 * $PastDueDays1;
    $nowdue = "1-" . $PastDueDays1 . " " . _('Days');
    $pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
    $pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');
    if ($orientation == 'P') {
        $cols = array(0, 80, 150, 325, 385, 490, 515);
        $headers = array(_('Customer'), '', 'Invoice Date',  $pastdue1, _('Total Balance'),
            "");
    }
    else
    {
        $cols = array(0, 80, 140, 200,270, 310, 400, 400, 445, 495, 545);
        $headers = array(_('Customer'), 'References', 'Invoice Date','Due Date','No of Days',  'More than 30 Days',
            );
    }
    $aligns = array('left',	'left',	'left','left','left',	'right', 'right', 'right', 'right',	'right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
        2 => array('text' => _('Customer'),	'from' => $from, 'to' => ''),
        3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
        4 => array('text' => _('Type'),		'from' => $summary,'to' => ''),
        5 => array('text' => _('Show Also Allocated'), 'from' => $show, 'to' => ''),
        6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    if ($convert)
        $headers[2] = _('Invoice Date');
    $rep = new FrontReport(_('Aged Customer Analysis'), "AgedCustomerAnalysis", user_pagesize(), 11, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $total = array(0,0,0,0, 0);

    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no,".TB_PREF."cust_branch.salesman, ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code FROM ".TB_PREF."debtors_master
	
		INNER JOIN ".TB_PREF."cust_branch ON 
		".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no


		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
	";
    if ($fromcust != ALL_TEXT)
        $sql .= " WHERE  ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
    if ($folk != 0)
        $sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
    $sql .= " ORDER BY name";
    $result = db_query($sql, "The customers could not be retrieved");

    while ($myrow=db_fetch($result))
    {
        if (!$convert && $currency != $myrow['curr_code'])
            continue;

        if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
        else $rate = 1.0;
        $custrec = get_customer_details_new($myrow['debtor_no'], $to, $show_all,$types);
        if (!$custrec)
            continue;
        $custrec['Balance'] *= $rate;
        $custrec['Due'] *= $rate;
        $custrec['Overdue1'] *= $rate;
        $custrec['Overdue2'] *= $rate;
        $str = array($custrec["Balance"] - $custrec["Due"],
//            $custrec["Due"]-$custrec["Overdue1"],
            $custrec["Overdue1"]-$custrec["Overdue2"] +  $custrec["Overdue2"],
//            $custrec["Overdue2"],
            $custrec["Balance"]);
        if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;

        $rep->fontSize += 2;
        $rep->TextCol(0, 2, $myrow["name"]);
//        $rep->TextCol(2, 3, get_salesman_names($myrow["salesman"]));
        if ($convert) $rep->TextCol(2, 3,	get_salesman_names($myrow["salesman"])."  ".$myrow['curr_code']);
        $rep->fontSize -= 2;
        $total[0] += ($custrec["Balance"] - $custrec["Due"]);
//        $total[1] += ($custrec["Due"]-$custrec["Overdue1"]);
        $total[1] += ($custrec["Overdue1"]-$custrec["Overdue2"]) +  $custrec["Overdue2"];
//        $total[2] += $custrec["Overdue2"];
        $total[2] += $custrec["Balance"];
        if ($orientation == 'L') {
//            for ($i = 0; $i < count($str); $i++)
//                $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
        }
        else
        {
            for ($i = 0; $i < count($str); $i++)
                $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
        }
        $rep->NewLine(1, 2);
        if (!$summaryOnly)
        {
            $res = get_invoices($myrow['debtor_no'], $to, $show_all,$types);
            if (db_num_rows($res)==0)
                continue;
            $rep->Line($rep->row + 4);

            while ($trans=db_fetch($res))
            {
                if($trans["Overdue1"] - $trans["Overdue2"] + $trans["Overdue2"] == 0 ) continue;



                $rep->NewLine(1, 2);
                $rep->TextCol(0, 1, $systypes_array[$trans['type']], -2);
                $rep->TextCol(1, 2,	$trans['reference'], -2);
                if ($orientation == 'L') {
                    $rep->DateCol(2, 3, $trans['tran_date'], true, -2);
                    $rep->DateCol(3, 4, $trans['due_date'], true, -2);
                    $today = date2sql(Today());
                    $date_=$trans['tran_date'];
    $mo = date("m",strtotime($date_));
	$yr = date("Y",strtotime($date_));
    $dy = date("d",strtotime($date_));
    global $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SHAMIM')
    {
    $date1 = date_create(date("Y-m-d", mktime(0, 0, 0, $mo , $dy+1,$yr)));
    }
    else{
                    $date1 = date_create($trans['due_date']);
    }
                     //display_error($date1);
                    $date2 = date_create(date2sql(Today()));
                    $diff = date_diff($date1, $date2);
                    $date_diff2 = $diff->format("%R%a days");
                    if($trans['type'] == 10)
                        $rep->TextCol(4, 5, $date_diff2, -2);
                }
                else{
                    $rep->DateCol(2, 3, $trans['due_date'], true, -2);
                }
                if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
                {
                    $trans['Balance'] *= -1;
                    $trans['Due'] *= -1;
                    $trans['Overdue1'] *= -1;
                    $trans['Overdue2'] *= -1;
                }

                foreach ($trans as $i => $value)
                    $trans[$i] *= $rate;
                $str = array($trans["Balance"] - $trans["Due"],
//                    $trans["Due"]-$trans["Overdue1"] ,
                    $trans["Overdue1"] - $trans["Overdue2"] + $trans["Overdue2"],
//                    $trans["Overdue2"],
                    $trans["Balance"]);
//                $str = array($custrec["Balance"] - $custrec["Due"],
////            $custrec["Due"]-$custrec["Overdue1"],
//                    $custrec["Overdue1"]-$custrec["Overdue2"] +  $custrec["Overdue2"],
////            $custrec["Overdue2"],
//                    $custrec["Balance"]);
                if ($orientation == 'L') {
//                    for ($i = 0; $i < count($str); $i++)
                        $rep->AmountCol(5,6, $trans["Overdue1"] - $trans["Overdue2"] + $trans["Overdue2"], $dec);
//                    $rep->AmountCol(5,6, $str[2], $dec);

//                    $rep->AmountCol($i + , $i + 5, $str[$i], $dec);
                }
                else{
//                    for ($i = 0; $i < count($str); $i++)
//                        $rep->AmountCol($i + 2, $i + 3, $str[$i], $dec);
                }
            }
            $rep->Line($rep->row - 8);
            $rep->NewLine(2);
        }
    }
    if ($summaryOnly)
    {
        $rep->Line($rep->row  + 4);
        $rep->NewLine();
    }
    $rep->fontSize += 2;
    $rep->TextCol(0, 3, _('Grand Total'));
    $rep->fontSize -= 2;
//    $rep->AmountCol(5, 6,	$total[0], -2);

//    $rep->AmountCol(6, 7,	$total[1], -2);
    $rep->AmountCol(5, 6,	$total[1], -2);
//    for ($i = 0; $i < count($total); $i++)
//    {
//        if ($orientation == 'L') {
//            $rep->AmountCol($i +2, $i + 3, $total[$i], $dec);
//        }
//        else{
//            $rep->AmountCol($i + 3, $i + 4, $total[$i], $dec);
//        }
//        if ($graphics && $i < count($total) - 1)
//        {
//            $pg->y[$i] = abs($total[$i]);
//        }
//    }
    $rep->Line($rep->row - 8);
    if ($graphics)
    {
        $pg->x = array(_('Current'), $nowdue, $pastdue1, $pastdue2);
        $pg->title     = $rep->title;
        $pg->axis_x    = _("Days");
        $pg->axis_y    = _("Amount");
        $pg->graphic_1 = $to;
        $pg->type      = $graphics;
        $pg->skin      = $SysPrefs->graph_skin;
        $pg->built_in  = false;
        $pg->latin_notation = ($SysPrefs->decseps[user_dec_sep()] != ".");
        $filename = company_path(). "/pdf_files/". random_id("").".png";
        $pg->display($filename, true);
        $w = $pg->width / 1.5;
        $h = $pg->height / 1.5;
        $x = ($rep->pageWidth - $w) / 2;
        $rep->NewLine(2);
        if ($rep->row - $h < $rep->bottomMargin)
            $rep->NewPage();
        $rep->AddImage($filename, $x, $rep->row - $h, $w, $h);
    }
    $rep->NewLine();
    $rep->End();
}
