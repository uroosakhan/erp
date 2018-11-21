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

function get_invoices($customer_id, $to, $all=true,$types, $dim, $no_of_days, $columns)
{
    if($columns == 0)
    {
        $todate = date2sql($to);
        $PastDueDays1 = $no_of_days;
        $PastDueDays2 = 2 * $PastDueDays1;
    }
    else
        {
            $todate = date2sql($to);
            $PastDueDays1 = $no_of_days;
            $PastDueDays2 = 2 * $PastDueDays1;
            $PastDueDays3 = 3 * $PastDueDays1;
            $PastDueDays4 = 4 * $PastDueDays1;
            $PastDueDays5 = 5 * $PastDueDays1;
        }
    // Removed allocated from sql
    // Ryan :06-05-17
    if ($all)
        /*$value = "(ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount + trans.gst_wh +
		trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc + trans.discount1 - trans.discount2)";*/
		 $value = "IF (type=".ST_JOURNAL." && ov_amount < 0, -ov_amount + ov_gst +
		  ov_freight + ov_freight_tax + ov_discount + trans.gst_wh + trans.supply_disc +
		  trans.service_disc + trans.fbr_disc + trans.srb_disc + trans.discount1 - 
		  trans.discount2, ov_amount + ov_gst + ov_freight + ov_freight_tax + 
		  ov_discount + trans.gst_wh + trans.supply_disc + trans.service_disc + 
		  trans.fbr_disc + trans.srb_disc + trans.discount1 - trans.discount2)";
 
    else
    {
           $value = "IF (type=".ST_JOURNAL." && ov_amount < 0, -ov_amount + ov_gst +
            ov_freight + ov_freight_tax + ov_discount +	trans.gst_wh  + 
            trans.supply_disc + trans.service_disc + trans.fbr_disc + 
            trans.srb_disc + trans.discount1 - trans.discount2 - alloc, 
           ov_amount + ov_gst + ov_freight + ov_freight_tax + ov_discount +
           	trans.gst_wh  + trans.supply_disc + trans.service_disc + trans.fbr_disc +
           	 trans.srb_disc + trans.discount1 - trans.discount2 - alloc)";
 
    }
                
    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CRV,ST_CUSTPAYMENT,ST_BANKDEPOSIT))."), -1, 1)";
    $due = "IF (type=".ST_SALESINVOICE.", due_date, tran_date)";
    if($columns == 0)
    {
        $sql = "SELECT type, reference, tran_date, due_date,
		$sign*$value as Balance,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$sign*$value,0) AS Due,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays1,$sign*$value,0) AS Overdue1,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays2,$sign*$value,0) AS Overdue2
		FROM " . TB_PREF . "debtor_trans trans
		WHERE type <> " . ST_CUSTDELIVERY . "
		AND debtor_no = $customer_id 
		AND tran_date <= '$todate'
		AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh +
        trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) > " . FLOAT_COMP_DELTA . " ";
    }
    else
    {
        $sql = "SELECT type, reference, tran_date, due_date,
		$sign*$value as Balance,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$sign*$value,0) AS Due,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays1,$sign*$value,0) AS Overdue1,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays2,$sign*$value,0) AS Overdue2,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays3,$sign*$value,0) AS Overdue3,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays4,$sign*$value,0) AS Overdue4,
		IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $PastDueDays5,$sign*$value,0) AS Overdue5
		FROM " . TB_PREF . "debtor_trans trans
		WHERE type <> " . ST_CUSTDELIVERY . "
		AND debtor_no = $customer_id 
		AND tran_date <= '$todate'
		AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh +
        trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) > " . FLOAT_COMP_DELTA . " ";

    }

    if (!$all)
        $sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh +
		trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA." ";
    if($types != -1)
        $sql .= " AND type =".$types;
    //if($dim != 0)
        //$sql .= " AND trans.dimension_id=".db_escape($dim);

    $sql .= " ORDER BY
		  	tran_date";
    return db_query($sql, "The customer transactions could not be retrieved");
}
function get_customer_details_for_aged_reports($customer_id, $to=null, $all=true, $dim, $no_of_days, $columns)
{

    if ($to == null)
        $todate = date("Y-m-d");
    else
        $todate = date2sql($to);


    if($columns == 0)
    {
//        $todate = date2sql($to);
        $past1 = $no_of_days;
        $past2 = 2 * $past1;
    }
    else
    {
//        $todate = date2sql($to);
        $past1 = $no_of_days;
        $past2 = 2 * $past1;
        $past3 = 3 * $past1;
        $past4 = 4 * $past1;
        $past5 = 5 * $past1;
    }


    // removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
//    if ($all)
        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";
    /*  else
          $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
  + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
              trans.alloc),0)"; */

//    else		//dz 24.7.18
//        $value = "IFNULL(
//    	IF (type=".ST_JOURNAL." && ov_amount < 0,
//    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
//    		trans.alloc),
//    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
//    		trans.alloc)
//    		)
//    		,0)";

    $due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    if($columns == 0)
    {
        $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM " . TB_PREF . "debtors_master debtor
				 LEFT JOIN " . TB_PREF . "debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> " . ST_CUSTDELIVERY . ","
            . TB_PREF . "payment_terms terms,"
            . TB_PREF . "credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
    }
    else
    {
        $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past3,$value,0)) AS Overdue3,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past4,$value,0)) AS Overdue4,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past5,$value,0)) AS Overdue5
			FROM " . TB_PREF . "debtors_master debtor
				 LEFT JOIN " . TB_PREF . "debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> " . ST_CUSTDELIVERY . ","
            . TB_PREF . "payment_terms terms,"
            . TB_PREF . "credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
    }
    if ($customer_id)
        $sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

//    if (!$all)
//        $sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight +
//        trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 -
//        trans.alloc) > ".FLOAT_COMP_DELTA;

    //if($dim != 0)
       // $sql .= " AND trans.dimension_id = ".db_escape($dim);
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
function get_customer_details_for_alloc_aged_reports($customer_id, $to=null, $all=true, $dim, $no_of_days, $columns)
{

    if ($to == null)
        $todate = date("Y-m-d");
    else
        $todate = date2sql($to);


    if($columns == 0)
    {
//        $todate = date2sql($to);
        $past1 = $no_of_days;
        $past2 = 2 * $past1;
    }
    else
    {
//        $todate = date2sql($to);
        $past1 = $no_of_days;
        $past2 = 2 * $past1;
        $past3 = 3 * $past1;
        $past4 = 4 * $past1;
        $past5 = 5 * $past1;
    }


    // removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
//    if ($all)
//    $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";
    /*  else
          $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
  + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
              trans.alloc),0)"; */

//    else		//dz 24.7.18
        $value = "IFNULL(
    	IF (type=".ST_JOURNAL." && ov_amount < 0,
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
    		trans.alloc),
    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
    		trans.alloc)
    		)
    		,0)";

    $due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
    if($columns == 0)
    {
        $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM " . TB_PREF . "debtors_master debtor
				 LEFT JOIN " . TB_PREF . "debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> " . ST_CUSTDELIVERY . ","
            . TB_PREF . "payment_terms terms,"
            . TB_PREF . "credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
    }
    else
    {
        $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past3,$value,0)) AS Overdue3,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past4,$value,0)) AS Overdue4,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past5,$value,0)) AS Overdue5
			FROM " . TB_PREF . "debtors_master debtor
				 LEFT JOIN " . TB_PREF . "debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> " . ST_CUSTDELIVERY . ","
            . TB_PREF . "payment_terms terms,"
            . TB_PREF . "credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
    }
    if ($customer_id)
        $sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

//    if (!$all)
        $sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight +
        trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 -
        trans.alloc) > ".FLOAT_COMP_DELTA;

    //if($dim != 0)
    // $sql .= " AND trans.dimension_id = ".db_escape($dim);
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
function customer_phone_no102($debtor_no)
{
    $sql="SELECT * FROM `0_crm_persons` WHERE `id` IN (
  SELECT person_id FROM `0_crm_contacts` WHERE `type`='customer' 
  AND `action`='general' 
  AND entity_id IN (
  SELECT branch_code FROM `0_cust_branch` WHERE debtor_no='$debtor_no')) ";

    $result = db_query($sql, "Cannot retreive a wo issue");

    return db_fetch($result);
}
//----------------------------------------------------------------------------------------------------

function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array, $SysPrefs, $dim;

	if ($dim > 0)
    {
    $to = $_POST['PARAM_0'];
    $no_of_days = $_POST['PARAM_1'];
    $orientation = $_POST['PARAM_2'];
    $fromcust = $_POST['PARAM_3'];
    $folk = $_POST['PARAM_4'];
    $dim = $_POST['PARAM_5'];
    $currency = $_POST['PARAM_6'];
    $types = $_POST['PARAM_7'];
    $show_all = $_POST['PARAM_8'];
    $show_diff = $_POST['PARAM_9'];
    $columns = $_POST['PARAM_10'];
    $inv_due = $_POST['PARAM_11'];
    $summaryOnly = $_POST['PARAM_12'];
    $no_zeros = $_POST['PARAM_13'];
    $graphics = $_POST['PARAM_14'];
    $comments = $_POST['PARAM_15'];
    $destination = $_POST['PARAM_16'];
    }
    else
    {
        $to = $_POST['PARAM_0'];
    $no_of_days = $_POST['PARAM_1'];
    $orientation = $_POST['PARAM_2'];
    $fromcust = $_POST['PARAM_3'];
    $folk = $_POST['PARAM_4'];
    $currency = $_POST['PARAM_5'];
    $types = $_POST['PARAM_6'];
    $show_all = $_POST['PARAM_7'];
    $show_diff = $_POST['PARAM_8'];
    $columns = $_POST['PARAM_9'];
    $inv_due = $_POST['PARAM_10'];
    $summaryOnly = $_POST['PARAM_11'];
    $no_zeros = $_POST['PARAM_12'];
    $graphics = $_POST['PARAM_13'];
    $comments = $_POST['PARAM_14'];
    $destination = $_POST['PARAM_15'];
    }

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    if($columns == 0)
    {
        $orientation = ($orientation ? 'L' : 'P');
    }
    else
    {
        $orientation = 'L';
    }
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

    if($columns == 0)
    {
        $PastDueDays1 = $no_of_days;
        $PastDueDays2 = 2 * $PastDueDays1;
        $nowdue = "1-" . $PastDueDays1 . " " . _('Days');
        $pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
        $pastdue2 = _('Over') . " " . $PastDueDays2 . " " . _('Days');

        if ($orientation == 'P')
        {
            $cols = array(0, 80, 150, 210, 280, 320, 385, 450, 515);
            $headers = array(_('Customer'),_(''),_(''),_(''),_(''),
                _('Total Balance'),
                _('Total Alloc Bal'),
                _('Diff'));
        }
        else
            {
                $cols = array(0, 80, 140, 200, 270, 320, 350, 400, 445, 495, 545);
                if($summaryOnly==0) {
                    $headers = array(_('Customer'), 'Reference', '', 'Due Date', 'No of Days', _('Current'), $nowdue, $pastdue1, $pastdue2,
                        _('Total Balance'));
                }
                else
                    {
                        $headers = array(_('Customer'), '', '', 'Salesman', 'Phone#', _('Current'), $nowdue, $pastdue1, $pastdue2,
                            _('Total Balance'));
                    }
            }
            $aligns = array('left',	'left',	'left','left','left',	'right', 'right', 'right', 'right',	'right');
    }
    else
        {
            $PastDueDays1 = $no_of_days;
            $PastDueDays2 = 2 * $PastDueDays1;
            $PastDueDays3 = 3 * $PastDueDays1;
            $PastDueDays4 = 4 * $PastDueDays1;
            $PastDueDays5 = 5 * $PastDueDays1;
            $nowdue = "1-" . $PastDueDays1 . " " . _('Days');
            $pastdue1 = $PastDueDays1 + 1 . "-" . $PastDueDays2 . " " . _('Days');
            $pastdue3 = $PastDueDays2 + 1 . "-" . $PastDueDays3 . " " . _('Days');
            $pastdue4 = $PastDueDays3 + 1 . "-" . $PastDueDays4 . " " . _('Days');
            $pastdue5 = $PastDueDays4 + 1 . "-" . $PastDueDays5 . " " . _('Days');
            $pastdue2 = _('Over') . " " . $PastDueDays5 . " " . _('Days');

            $cols = array(0, 80, 140, 180, 220, 260, 310, 350, 395, 445, 495, 550);

            if($summaryOnly==0) {
                $headers = array(_('Customer'), 'Reference', '', _('Current'), $nowdue, $pastdue1,
                    $pastdue3, $pastdue4, $pastdue5, $pastdue2, _('Total Balance'));
            }
            else
            {
                $headers = array(_('Customer'), 'Salesman', 'Phone', _('Current'), $nowdue, $pastdue1, $pastdue3, $pastdue4, $pastdue5, $pastdue2,
                    _('Total Balance'));
            }
            $aligns = array('left',	'left',	'left','right','right',	'right', 'right', 'right', 'right', 'right',	'right');

        }

    $params =   array( 	0 => $comments,
        1 => array('text' => _('End Date'), 'from' => $to, 'to' => ''),
        2 => array('text' => _('Customer'),	'from' => $from, 'to' => ''),
        3 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
        4 => array('text' => _('Type'),		'from' => $summary,'to' => ''),
        5 => array('text' => _('Show Also Allocated'), 'from' => $show, 'to' => ''),
        6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));
    if($summaryOnly==0) {
        if ($convert)
            $headers[2] = _('Invoice Date');
    }
        $rep = new FrontReport(_('Aged Customer Analysis'), "AgedCustomerAnalysis", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);


    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    if($columns == 0)
    {
        $total = array(0, 0, 0, 0, 0);
        $all_total = array(0, 0, 0, 0, 0);
    }
    else
    {
        $total = array(0, 0, 0, 0, 0, 0, 0, 0);
        $all_total = array(0, 0, 0, 0, 0, 0, 0, 0);
    }

    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no, ".TB_PREF."debtors_master.name,
     ".TB_PREF."debtors_master.debtor_ref, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."cust_branch.salesman
      FROM ".TB_PREF."debtors_master
	
		INNER JOIN ".TB_PREF."cust_branch ON 
		".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no


		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
	";
    if ($fromcust != ALL_TEXT)
        $sql .= " WHERE  ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
    if ($folk != 0)
        $sql .= " AND ".TB_PREF."salesman.salesman_code=".db_escape($folk);
    if ($dim != 0)
        $sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dim);
        
    $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no ORDER BY Name"; 
    $result = db_query($sql, "The customers could not be retrieved");

    while ($myrow=db_fetch($result))
    {
        if (!$convert && $currency != $myrow['curr_code'])
            continue;

        if ($convert) $rate = get_exchange_rate_from_home_currency($myrow['curr_code'], $to);
        else $rate = 1.0;
        $custrec = get_customer_details_for_aged_reports($myrow['debtor_no'], $to, $show_all, $dim, $no_of_days, $columns);
        $custrec_all = get_customer_details_for_alloc_aged_reports($myrow['debtor_no'], $to, $show_all, $dim, $no_of_days, $columns);
        if (!$custrec || !$custrec_all)
            continue;
        if($show_diff == 1)
        {
            if ($custrec_all["Balance"]-$custrec["Balance"]==0)
                continue;
        }
        if($columns == 0)
        {
            $custrec['Balance'] *= $rate;
            $custrec['Due'] *= $rate;
            $custrec['Overdue1'] *= $rate;
            $custrec['Overdue2'] *= $rate;
            $str = array($custrec["Balance"] - $custrec["Due"],
                $custrec["Due"] - $custrec["Overdue1"],
                $custrec["Overdue1"] - $custrec["Overdue2"],
                $custrec["Overdue2"],
                $custrec["Balance"]);



            $custrec_all['Balance'] *= $rate;
            $custrec_all['Due'] *= $rate;
            $custrec_all['Overdue1'] *= $rate;
            $custrec_all['Overdue2'] *= $rate;
            $str = array($custrec_all["Balance"] - $custrec_all["Due"],
                $custrec_all["Due"] - $custrec_all["Overdue1"],
                $custrec_all["Overdue1"] - $custrec_all["Overdue2"],
                $custrec_all["Overdue2"],
                $custrec_all["Balance"]);
        }
        else
        {
            $custrec['Balance'] *= $rate;
            $custrec['Due'] *= $rate;
            $custrec['Overdue1'] *= $rate;
            $custrec['Overdue2'] *= $rate;
            $custrec['Overdue3'] *= $rate;
            $custrec['Overdue4'] *= $rate;
            $custrec['Overdue5'] *= $rate;
            $str = array($custrec["Balance"] - $custrec["Due"],
                $custrec["Due"] - $custrec["Overdue1"],
                $custrec["Overdue1"] - $custrec["Overdue2"],
                $custrec["Overdue2"] - $custrec["Overdue3"],
                $custrec["Overdue3"] - $custrec["Overdue4"],
                $custrec["Overdue4"] - $custrec["Overdue5"],
                $custrec["Overdue5"],
                $custrec["Balance"]);


            $custrec_all['Balance'] *= $rate;
            $custrec_all['Due'] *= $rate;
            $custrec_all['Overdue1'] *= $rate;
            $custrec_all['Overdue2'] *= $rate;
            $custrec_all['Overdue3'] *= $rate;
            $custrec_all['Overdue4'] *= $rate;
            $custrec_all['Overdue5'] *= $rate;
            $str = array($custrec_all["Balance"] - $custrec_all["Due"],
                $custrec_all["Due"] - $custrec_all["Overdue1"],
                $custrec_all["Overdue1"] - $custrec_all["Overdue2"],
                $custrec_all["Overdue2"] - $custrec_all["Overdue3"],
                $custrec_all["Overdue3"] - $custrec_all["Overdue4"],
                $custrec_all["Overdue4"] - $custrec_all["Overdue5"],
                $custrec_all["Overdue5"],
                $custrec_all["Balance"]);
        }
        if ($no_zeros && floatcmp(array_sum($str), 0) == 0) continue;
        $rep->fontSize += 2;
        if ($orientation == 'L')
        {
            global $db_connections;
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SPECTRA' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='SPECTRA2')
            {
                $rep->TextCol(0, 2, $myrow["debtor_ref"].'   -   '.$myrow["name"]);
            }
            else
                {
                    // if($columns == 1)
                    {
                        $rep->TextCol(0, 1, $myrow["name"]);
                     
                    }
                    // else
                    // {
                    //     $rep->TextCol(0, 1, $myrow["name"]);
                    // }
                }
                $contact_no = customer_phone_no102($myrow["debtor_no"]);
            if($columns == 0)
            {
//                $rep->TextCol(3, 4, get_salesman_name($myrow["salesman"]));
//                $rep->TextCol(4, 5, $contact_no['phone']);
            }
            else
            {
                $rep->TextCol(1, 2, get_salesman_name($myrow["salesman"]));
                $rep->TextCol(2, 3, $contact_no['phone']);
            }
        }
        else
            $rep->TextCol(0, 2, $myrow["name"]);
        
        if ($convert) {
            if($columns == 0) {
//                $rep->TextCol(2, 3, $myrow['curr_code']);
            }
//            else{
//                $rep->TextCol(1, 2, get_salesman_name($myrow["salesman"]));
//            }
        }
        $rep->fontSize -= 2;
        if($columns == 0)
        {
            $total[0] += ($custrec["Balance"] - $custrec["Due"]);
            $total[1] += ($custrec["Due"] - $custrec["Overdue1"]);
            $total[2] += ($custrec["Overdue1"] - $custrec["Overdue2"]);
            $total[3] += $custrec["Overdue2"];
            $total[4] += $custrec["Balance"];

            $all_total[0] += ($custrec_all["Balance"] - $custrec_all["Due"]);
            $all_total[1] += ($custrec_all["Due"] - $custrec_all["Overdue1"]);
            $all_total[2] += ($custrec_all["Overdue1"] - $custrec_all["Overdue2"]);
            $all_total[3] += $custrec_all["Overdue2"];
            $all_total[4] += $custrec_all["Balance"];
            if ($orientation == 'L') {
//                for ($i = 0; $i < count($str); $i++)
//                    $rep->AmountCol($i + 5, $i + 6, $str[7], $dec);
            }
            else {
//                for ($i = 0; $i < count($str); $i++)
                    $rep->AmountCol(5, 6, $custrec["Balance"], $dec);
                    $rep->AmountCol(6, 7, $custrec_all["Balance"], $dec);
                    $rep->AmountCol(7, 8, $custrec_all["Balance"]-$custrec["Balance"], $dec);
            }
        }
        else
        {
            $total[0] += ($custrec["Balance"] - $custrec["Due"]);
            $total[1] += ($custrec["Due"] - $custrec["Overdue1"]);
            $total[2] += ($custrec["Overdue1"] - $custrec["Overdue2"]);
            $total[3] += ($custrec["Overdue2"] - $custrec["Overdue3"]);
            $total[4] += ($custrec["Overdue3"] - $custrec["Overdue4"]);
            $total[5] += ($custrec["Overdue4"] - $custrec["Overdue5"]);
            $total[6] += $custrec["Overdue5"];
            $total[7] += $custrec["Balance"];

            for ($i = 0; $i < count($str); $i++)
                $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
        }
        $rep->NewLine(1, 2);
        if (!$summaryOnly)
        {
            $res = get_invoices($myrow['debtor_no'], $to, $show_all,$types, $dim, $no_of_days, $columns);
            if (db_num_rows($res)==0)
                continue;
            $rep->Line($rep->row + 4);
            if($columns == 0)
            {
                while ($trans = db_fetch($res))
                {
                    $rep->NewLine(1, 2);
                    $rep->TextCol(0, 1, $systypes_array[$trans['type']], -2);
                    $rep->TextCol(1, 2, $trans['reference'], -2);
                    if ($columns == 0) {
                        if ($orientation == 'L')
                        {
                            $rep->DateCol(2, 3, $trans['tran_date'], true, -2);
                            $rep->DateCol(3, 4, $trans['due_date'], true, -2);
                            $today = date2sql(Today());
                            if($inv_due==0)
                            $date_ = $trans['due_date'];
                            else
                                $date_ = $trans['tran_date'];
                            $mo = date("m", strtotime($date_));
                            $yr = date("Y", strtotime($date_));
                            $dy = date("d", strtotime($date_));
                            global $db_connections;
                            if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BNT2') {
                                $date1 = date_create(date("Y-m-d", mktime(0, 0, 0, $mo, $dy + 1, $yr)));
                            }
                            else
                                {
                                    if($inv_due==0)
                                        $date1 = date_create($trans['due_date']);
                                    else
                                        $date1 = date_create($trans['tran_date']);
                                }
                            $date2 = date_create(date2sql(Today()));
                            $diff = date_diff($date1, $date2);
                            $date_diff2 = $diff->format("%R%a days");
                            if ($trans['type'] == 10)
                                $rep->TextCol(4, 5, $date_diff2, -2);
                        }
                        else
                            {
                            $rep->DateCol(2, 3, $trans['tran_date'], true, -2);
                            }
                    }
                    else
                        {
                        $rep->DateCol(2, 3, $trans['tran_date'], true, -2);
                        }

                    if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV) {
                        $trans['Balance'] *= -1;
                        $trans['Due'] *= -1;
                        $trans['Overdue1'] *= -1;
                        $trans['Overdue2'] *= -1;
                    }
                    foreach ($trans as $i => $value)
                        $trans[$i] *= $rate;
                    {
                        $str = array($trans["Balance"] - $trans["Due"],
                            $trans["Due"] - $trans["Overdue1"],
                            $trans["Overdue1"] - $trans["Overdue2"],
                            $trans["Overdue2"],
                            $trans["Balance"]);

                        if ($orientation == 'L') {
                            for ($i = 0; $i < count($str); $i++)
                                $rep->AmountCol($i + 5, $i + 6, $str[$i], $dec);
                        } else {
                            for ($i = 0; $i < count($str); $i++)
                                $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
                        }
                    }
                }
            }
            else
            {
                while ($trans = db_fetch($res))
                {
                    $rep->NewLine(1, 2);
                    $rep->TextCol(0, 1, $systypes_array[$trans['type']], -2);
                    $rep->TextCol(1, 2, $trans['reference'], -2);

                        {
                        $rep->DateCol(2, 3, $trans['tran_date'], true, -2);
                        }

                    if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV) {
                        $trans['Balance'] *= -1;
                        $trans['Due'] *= -1;
                        $trans['Overdue1'] *= -1;
                        $trans['Overdue2'] *= -1;
                        $trans['Overdue3'] *= -1;
                        $trans['Overdue4'] *= -1;
                        $trans['Overdue5'] *= -1;
                    }
                    foreach ($trans as $i => $value)
                        $trans[$i] *= $rate;

                        {
                        $str = array($trans["Balance"] - $trans["Due"],
                            $trans["Due"] - $trans["Overdue1"],
                            $trans["Overdue1"] - $trans["Overdue2"],
                            $trans["Overdue2"] - $trans["Overdue3"],
                            $trans["Overdue3"] - $trans["Overdue4"],
                            $trans["Overdue4"] - $trans["Overdue5"],
                            $trans["Overdue5"],
                            $trans["Balance"]);

                        for ($i = 0; $i < count($str); $i++)
                            $rep->AmountCol($i + 3, $i + 4, $str[$i], $dec);
                    }
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
    if($columns == 0) {
        for ($i = 0; $i < count($total); $i++)
        {
            if ($orientation == 'L') {
                $rep->AmountCol($i + 5, $i + 6, $total[$i], $dec);
            }
            if ($graphics && $i < count($total) - 1) {
                $pg->y[$i] = abs($total[$i]);
            }
        }
        $rep->AmountCol(5, 6, $total[4], $dec);
        $rep->AmountCol(6, 7, $all_total[4], $dec);
        $rep->AmountCol(7, 8, $all_total[4]-$total[4], $dec);
    }
    else
    {
        for ($i = 0; $i < count($total); $i++)
        {
            $rep->AmountCol($i + 3, $i + 4, $total[$i], $dec);
        }

    }
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
