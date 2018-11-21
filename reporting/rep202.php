<?php

$page_security = 'SA_SUPPLIERANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Ages Supplier Analysis
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_supplier_analysis();

//----------------------------------------------------------------------------------------------------

function get_invoices($supplier_id, $to, $all=true,$types)
{
	$todate = date2sql($to);
	$PastDueDays1 = get_company_pref('past_due_days');
	$PastDueDays2 = 2 * $PastDueDays1;

	// Revomed allocated from sql
	if ($all)
    	$value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount - ( supply_disc + service_disc + fbr_disc +  srb_disc) )";
    else
    	$value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT." OR trans.type=".ST_CRV.", 
    	(trans.ov_amount + trans.ov_gst + trans.ov_discount - trans.alloc),
    	(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.alloc))";
	$due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT." OR trans.type=".ST_SUPPCREDIT_IMPORT.",trans.due_date,trans.tran_date)";
	$sql = "SELECT trans.type,
		trans.reference,
		trans.tran_date,
		$value as Balance,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0) AS Due,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays1,$value,0) AS Overdue1,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays2,$value,0) AS Overdue2

		FROM ".TB_PREF."suppliers supplier,
			".TB_PREF."supp_trans trans

	   	WHERE supplier.supplier_id = trans.supplier_id
			AND trans.supplier_id = $supplier_id
			AND trans.tran_date <= '$todate'
			AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) > ".FLOAT_COMP_DELTA."
			AND trans.type NOT IN(".ST_SUPPCREDIT_IMPORT.")";
	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount) - trans.alloc > ".FLOAT_COMP_DELTA;


    if($types != -1)
        $sql .= " AND type =".$types;

    $sql .= " ORDER BY trans.tran_date";

	return db_query($sql, "The supplier details could not be retrieved");
}

//----------------------------------------------------------------------------------------------------

function print_aged_supplier_analysis()
{
    global $path_to_root, $systypes_array, $SysPrefs;

    $to = $_POST['PARAM_0'];
    $fromsupp = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
//    $types = $_POST['PARAM_3'];
   	$show_all = $_POST['PARAM_3'];
	$summaryOnly = $_POST['PARAM_4'];
    $no_zeros = $_POST['PARAM_5'];
    $types = $_POST['PARAM_6'];
    $graphics = $_POST['PARAM_7'];
    $comments = $_POST['PARAM_8'];
	$orientation = $_POST['PARAM_9'];
	$destination = $_POST['PARAM_10'];


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

	if ($fromsupp == ALL_TEXT)
		$from = _('All');
	else
		$from = get_supplier_name($fromsupp);
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

	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');
	if ($show_all) $show = _('Yes');
	else $show = _('No');

	$PastDueDays1 = get_company_pref('past_due_days');
	$PastDueDays2 = 2 * $PastDueDays1;
	$nowdue = "1-" . $PastDueDays1 . " " . _('Days');
	$pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');

	/*$cols = array(0, 100, 140, 190,	250, 320, 385, 450,	515);

	$headers = array(_('Supplier'),	'',	'',	_('Current'), $nowdue, $pastdue1,$pastdue2,
		_('Total Balance'));
	$aligns = array('left',	'left',	'left',	'right', 'right', 'right', 'right',	'right');*/
	if ($orientation == 'P') {
        $cols = array(0, 100, 150, 190,	250, 320, 385, 450,	515);

        $headers = array(_('Supplier'),	'Reference',	'',	_('Current'), $nowdue, $pastdue1,$pastdue2,
            _('Total Balance'));

        $aligns = array('left',	'left',	'left',	'right', 'right', 'right', 'right',	'right');
    }
    else
    {
        $cols = array(0, 80, 140, 200,270, 320, 350, 400, 445, 495, 545);
        $headers = array(_('Supplier'), 'Reference', '','', 'No of Days', _('Current'), $nowdue, $pastdue1, $pastdue2,
            _('Total Balance'));
        $aligns = array('left',	'left',	'left','left','left',	'right', 'right', 'right', 'right',	'right');
    }

    	$params =   array( 	0 => $comments,
    				1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
    				2 => array('text' => _('Supplier'), 'from' => $from, 'to' => ''),
    				3 => array('text' => _('Currency'),'from' => $currency,'to' => ''),
                    		4 => array('text' => _('Type'), 'from' => $summary,'to' => ''),
                    5 => array('text' => _('Show Also Allocated'), 'from' => $show, 'to' => ''),		
				6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

	if ($convert)
		$headers[2] = _('Date/Currency');
    $rep = new FrontReport(_('Aged Supplier Analysis'), "AgedSupplierAnalysis", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$total = array();
	$total[0] = $total[1] = $total[2] = $total[3] = $total[4] = 0.0;
	$PastDueDays1 = get_company_pref('past_due_days');
	$PastDueDays2 = 2 * $PastDueDays1;

	$nowdue = "1-" . $PastDueDays1 . " " . _('Days');
	$pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');

	$sql = "SELECT supplier_id, supp_name AS name, curr_code FROM ".TB_PREF."suppliers";
	if ($fromsupp != ALL_TEXT)
		$sql .= " WHERE supplier_id=".db_escape($fromsupp);
	$sql .= " ORDER BY supp_name";
	$result = db_query($sql, "The suppliers could not be retrieved");

	while ($myrow=db_fetch($result))
	{
		if (!$convert && $currency != $myrow['curr_code']) continue;

		if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
		else $rate = 1.0;

		$supprec = get_supplier_details($myrow['supplier_id'], $to, $show_all);
		if (!$supprec)
			continue;
		$supprec['Balance'] *= $rate;
		$supprec['Due'] *= $rate;
		$supprec['Overdue1'] *= $rate;
		$supprec['Overdue2'] *= $rate;

		$str = array($supprec["Balance"] - $supprec["Due"],
			$supprec["Due"]-$supprec["Overdue1"],
			$supprec["Overdue1"]-$supprec["Overdue2"],
			$supprec["Overdue2"],
			$supprec["Balance"]);

		if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;

		$rep->fontSize += 2;
		$rep->TextCol(0, 2,	$myrow['name']);
		if ($convert) $rep->TextCol(2, 3,	$myrow['curr_code']);
		$rep->fontSize -= 2;
		$total[0] += ($supprec["Balance"] - $supprec["Due"]);
		$total[1] += ($supprec["Due"]-$supprec["Overdue1"]);
		$total[2] += ($supprec["Overdue1"]-$supprec["Overdue2"]);
		$total[3] += $supprec["Overdue2"];
		$total[4] += $supprec["Balance"];
// 		for ($i = 0; $i < count($str); $i++)
// 			$rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
 if ($orientation == 'L') {
		for ($i = 0; $i < count($str); $i++)
			$rep->AmountCol($i + 5, $i + 6, $str[$i], $dec);
        }
        else
        {
            for ($i = 0; $i < count($str); $i++)
                $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
        }
		$rep->NewLine(1, 2);
		if (!$summaryOnly)
		{
			$res = get_invoices($myrow['supplier_id'], $to, $show_all,$types);
    		if (db_num_rows($res)==0)
				continue;
    		$rep->Line($rep->row + 4);
			while ($trans=db_fetch($res))
			{
				$rep->NewLine(1, 2);
        		$rep->TextCol(0, 1, $systypes_array[$trans['type']], -2);
				$rep->TextCol(1, 2,	$trans['reference'], -2);
				$rep->TextCol(2, 3,	sql2date($trans['tran_date']), -2);
				if ($orientation == 'L') {
                   // $rep->TextCol(2, 3, sql2date($trans['tran_date']), -2);
                    $rep->DateCol(3, 4, $trans['due_date'], true, -2);
                    $today = date2sql(Today());
                    $date1 = date_create($trans['tran_date']);
                    $date2 = date_create(date2sql(Today()));
                    $diff22 = date_diff($date1, $date2);
                   // display_error($today."--".$date2);

                    $date_diff22 = $diff22->format("%R%a days");
                   
                        $rep->TextCol(4, 5, $date_diff22, -2);

                }
                else {
                    $rep->DateCol(2, 3, $trans['due_date'], true, -2);
                }
				
				foreach ($trans as $i => $value)
					$trans[$i] *= $rate;
				$str = array($trans["Balance"] - $trans["Due"],
					$trans["Due"]-$trans["Overdue1"],
					$trans["Overdue1"]-$trans["Overdue2"],
					$trans["Overdue2"],
					$trans["Balance"]);
				// for ($i = 0; $i < count($str); $i++)
				// 	$rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
					if ($orientation == 'L') {
                    for ($i = 0; $i < count($str); $i++)
                        $rep->AmountCol($i + 5, $i + 6, $str[$i], $dec);
                } else {
                    for ($i = 0; $i < count($str); $i++)
                        $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
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
	$rep->TextCol(0, 3,	_('Grand Total'));
	$rep->fontSize -= 2;
	for ($i = 0; $i < count($total); $i++)
	{
	//	$rep->AmountCol($i + 3, $i + 4, $total[$i], $dec);
		 if ($orientation == 'L') {
        $rep->AmountCol($i + 5, $i + 6, $total[$i], $dec);
    }
        else {
            $rep->AmountCol($i + 3, $i + 4, $total[$i], $dec);
        }
		if ($graphics && $i < count($total) - 1)
		{
			$pg->y[$i] = abs($total[$i]);
		}
	}
   	$rep->Line($rep->row  - 8);
   	$rep->NewLine();
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
    $rep->End();
}

