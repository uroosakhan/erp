<?php
$page_security = 'SA_MONTHLYITEMISEDSALE';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");
include_once($path_to_root . "/admin/db/tags_db.inc");

//----------------------------------------------------------------------------------------------------

// trial_inquiry_controls();
function getTransactions( $from, $stock_id)
{
    $from = date2sql($from);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,2,$yr));
    $date3 = date('Y-m-d',mktime(0,0,0,$mo,3,$yr));
    $date4 = date('Y-m-d',mktime(0,0,0,$mo,4,$yr));
    $date5 = date('Y-m-d',mktime(0,0,0,$mo,5,$yr));
    $date6 = date('Y-m-d',mktime(0,0,0,$mo,6,$yr));
    $date7 = date('Y-m-d',mktime(0,0,0,$mo,7,$yr));
    $date8 = date('Y-m-d',mktime(0,0,0,$mo,8,$yr));
    $date9 = date('Y-m-d',mktime(0,0,0,$mo,9,$yr));
    $date10 = date('Y-m-d',mktime(0,0,0,$mo,10,$yr));
    $date11 = date('Y-m-d',mktime(0,0,0,$mo,11,$yr));
    $date12 = date('Y-m-d',mktime(0,0,0,$mo,12,$yr));
    $date13 = date('Y-m-d',mktime(0,0,0,$mo,13,$yr));
    $date14 = date('Y-m-d',mktime(0,0,0,$mo,14,$yr));
    $date15 = date('Y-m-d',mktime(0,0,0,$mo,15,$yr));
    $date16 = date('Y-m-d',mktime(0,0,0,$mo,16,$yr));
    $date17 = date('Y-m-d',mktime(0,0,0,$mo,17,$yr));
    $date18 = date('Y-m-d',mktime(0,0,0,$mo,18,$yr));
    $date19 = date('Y-m-d',mktime(0,0,0,$mo,19,$yr));
    $date20 = date('Y-m-d',mktime(0,0,0,$mo,20,$yr));
    $date21 = date('Y-m-d',mktime(0,0,0,$mo,21,$yr));
    $date22 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
    $date23 = date('Y-m-d',mktime(0,0,0,$mo,23,$yr));
    $date24 = date('Y-m-d',mktime(0,0,0,$mo,24,$yr));
    $date25 = date('Y-m-d',mktime(0,0,0,$mo,25,$yr));
    $date26 = date('Y-m-d',mktime(0,0,0,$mo,26,$yr));
    $date27 = date('Y-m-d',mktime(0,0,0,$mo,27,$yr));
    $date28 = date('Y-m-d',mktime(0,0,0,$mo,28,$yr));
    $date29 = date('Y-m-d',mktime(0,0,0,$mo,29,$yr));
    $date30 = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    $date31 = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN (qty)*-1 ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN (qty)*-1 ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date >= '$date3' AND trans.tran_date < '$date4' 
THEN (qty)*-1 ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN (qty)*-1 ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN (qty)*-1 ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN (qty)*-1 ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN (qty)*-1 ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN (qty)*-1 ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN (qty)*-1 ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN (qty)*-1 ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN (qty)*-1 ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN (qty)*-1 ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN (qty)*-1 ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN (qty)*-1 ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN (qty)*-1 ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN (qty)*-1 ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN (qty)*-1 ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN (qty)*-1 ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN (qty)*-1 ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN (qty)*-1 ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN (qty)*-1 ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN (qty)*-1 ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN (qty)*-1 ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN (qty)*-1 ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN (qty)*-1 ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN (qty)*-1 ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN (qty)*-1 ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN (qty)*-1 ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN (qty)*-1 ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN (qty)*-1 ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31' 
THEN (qty)*-1 ELSE 0 END) AS per31
		FROM 
			".TB_PREF."stock_moves trans
		WHERE  trans.type IN(13)
		AND trans.stock_id='$stock_id'";
    return db_query($sql,"No transactions were returned");

}
//---------------for quantity------------------------//

function getTransactions_qty_amount($from, $stock_id, $week)
{
    $from = date2sql($from);
    if($week==0 || $week==5) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        if ($mon_days == 30)
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 30, $yr));
        else
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 31, $yr));
    }
    elseif($week==1) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
    }
    elseif($week==2) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
    }
    elseif($week==3) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
    }
    elseif($week==4)
    {
        $mo = date('m',strtotime($from));
        $yr = date('Y',strtotime($from));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
        $date1 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
        if($mon_days==30)
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));
    }
    $sql = " SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date <= '$e_date' 
THEN (qty)*-1 ELSE 0 END) AS per01
		FROM 
			".TB_PREF."stock_moves trans
		WHERE  trans.type IN(13)
		AND trans.stock_id='$stock_id'";
    $res= db_query($sql,"No transactions were returned");
    $row = db_fetch_row($res);
    return $row[0];
}
//---------------for standard cost------------------------//

function getTransactions_standard_cost( $from, $stock_id, $week)
{
    $from = date2sql($from);
    if($week==0 || $week==5)
    {
        $mo = date('m',strtotime($from));
        $yr = date('Y',strtotime($from));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
        $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
        if($mon_days==30)
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));
    }
    elseif($week==1) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
    }
    elseif($week==2) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
    }
    elseif($week==3) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
    }
    elseif($week==4)
    {
        $mo = date('m',strtotime($from));
        $yr = date('Y',strtotime($from));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
        $date1 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
        if($mon_days==30)
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));
    }
    $sql = " SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date <= '$e_date' 
THEN (master.material_cost*trans.qty)*-1 ELSE 0 END) AS per01
		FROM 
			".TB_PREF."stock_moves trans,0_stock_master master
		WHERE  trans.stock_id=master.stock_id
		AND trans.type IN(13)
		AND trans.stock_id='$stock_id'";
    $res= db_query($sql,"No transactions were returned");
    $row = db_fetch_row($res);
    return $row[0];

}
//-------for quantity multiply cost in debtor trans details-----//

function getTransactions_qty_cost($from, $stock_id, $week)
{
    $from = date2sql($from);
    if($week==0 || $week==5) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        if ($mon_days == 30)
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 30, $yr));
        else
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 31, $yr));
    }
    elseif($week==1) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
    }
    elseif($week==2) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
    }
    elseif($week==3) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
    }
    elseif($week==4)
    {
        $mo = date('m',strtotime($from));
        $yr = date('Y',strtotime($from));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
        $date1 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
        if($mon_days==30)
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));
    }
    $sql = " SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date <= '$e_date' 
THEN (unit_price*quantity) ELSE 0 END) AS per01
		FROM `0_debtor_trans` trans , 0_debtor_trans_details details
 WHERE trans.`trans_no`= details.`debtor_trans_no`
  AND trans.`type`= details.`debtor_trans_type`
  AND details.`stock_id`='$stock_id'  
  AND trans.`type` = 13
  ";
    $res= db_query($sql,"No transactions were returned");
    $row = db_fetch_row($res);
    return $row[0];
}

//-------------------linewise discount-------------------//
function getTransactions_linewise_disc($from, $stock_id, $week)
{
        $from = date2sql($from);
    if($week==0 || $week==5) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        if ($mon_days == 30)
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 30, $yr));
        else
            $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 31, $yr));
    }
    elseif($week==1) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
    }
    elseif($week==2) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
    }
    elseif($week==3) {
        $mo = date('m', strtotime($from));
        $yr = date('Y', strtotime($from));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $e_date = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
    }
    elseif($week==4)
    {
        $mo = date('m',strtotime($from));
        $yr = date('Y',strtotime($from));
        $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
        $date1 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
        if($mon_days==30)
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
        else
            $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));
    }
    $sql = " SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date <= '$e_date' 
THEN (unit_price*quantity)*discount_percent ELSE 0 END) AS per01
		FROM `0_debtor_trans` trans , 0_debtor_trans_details details
 WHERE trans.`trans_no`= details.`debtor_trans_no`
  AND trans.`type`= details.`debtor_trans_type`
  AND details.`stock_id`='$stock_id'
  AND trans.`type` = 13
  ";
    $res= db_query($sql,"No transactions were returned");
    $row = db_fetch_row($res);
    return $row[0];

}
//------------------------------------------------//
function getTransactions_disc_qty2($from, $stock_id)
{
    $from = date2sql($from);

    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    if($mon_days==30)
        $e_date = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    else
        $e_date = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

    $sql = "SELECT  SUM((unit_price*quantity)*discount_percent) FROM `0_debtor_trans`,0_debtor_trans_details WHERE 0_debtor_trans.`trans_no`=0_debtor_trans_details.debtor_trans_no
  AND 0_debtor_trans.`type`=0_debtor_trans_details.debtor_trans_type
  AND 0_debtor_trans.`tran_date`>='$date1'
  AND 0_debtor_trans.`tran_date`<='$e_date' 
  AND 0_debtor_trans.type =13";
    $res= db_query($sql,"No transactions were returned");
    $value=db_fetch_row($res);
    return $value[0];
}
//------------------------------------------------//

function getTransactions2( $from, $stock_id)
{
    $from = date2sql($from);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,2,$yr));
    $date3 = date('Y-m-d',mktime(0,0,0,$mo,3,$yr));
    $date4 = date('Y-m-d',mktime(0,0,0,$mo,4,$yr));
    $date5 = date('Y-m-d',mktime(0,0,0,$mo,5,$yr));
    $date6 = date('Y-m-d',mktime(0,0,0,$mo,6,$yr));
    $date7 = date('Y-m-d',mktime(0,0,0,$mo,7,$yr));
    $date8 = date('Y-m-d',mktime(0,0,0,$mo,8,$yr));
    $date9 = date('Y-m-d',mktime(0,0,0,$mo,9,$yr));
    $date10 = date('Y-m-d',mktime(0,0,0,$mo,10,$yr));
    $date11 = date('Y-m-d',mktime(0,0,0,$mo,11,$yr));
    $date12 = date('Y-m-d',mktime(0,0,0,$mo,12,$yr));
    $date13 = date('Y-m-d',mktime(0,0,0,$mo,13,$yr));
    $date14 = date('Y-m-d',mktime(0,0,0,$mo,14,$yr));
    $date15 = date('Y-m-d',mktime(0,0,0,$mo,15,$yr));
    $date16 = date('Y-m-d',mktime(0,0,0,$mo,16,$yr));
    $date17 = date('Y-m-d',mktime(0,0,0,$mo,17,$yr));
    $date18 = date('Y-m-d',mktime(0,0,0,$mo,18,$yr));
    $date19 = date('Y-m-d',mktime(0,0,0,$mo,19,$yr));
    $date20 = date('Y-m-d',mktime(0,0,0,$mo,20,$yr));
    $date21 = date('Y-m-d',mktime(0,0,0,$mo,21,$yr));
    $date22 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
    $date23 = date('Y-m-d',mktime(0,0,0,$mo,23,$yr));
    $date24 = date('Y-m-d',mktime(0,0,0,$mo,24,$yr));
    $date25 = date('Y-m-d',mktime(0,0,0,$mo,25,$yr));
    $date26 = date('Y-m-d',mktime(0,0,0,$mo,26,$yr));
    $date27 = date('Y-m-d',mktime(0,0,0,$mo,27,$yr));
    $date28 = date('Y-m-d',mktime(0,0,0,$mo,28,$yr));
    $date29 = date('Y-m-d',mktime(0,0,0,$mo,29,$yr));
    $date30 = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    $date31 = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date = '$date3'  
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31'
THEN ((qty * price) * (1-discount_percent) )*-1 ELSE 0 END) AS per31
		FROM 
			".TB_PREF."stock_moves trans
		WHERE  trans.type IN(13)
		AND trans.price<>0
		AND trans.stock_id='$stock_id'";
    return db_query($sql,"No transactions were returned");

}
function getTransactions_discount( $from, $stock_id)
{
    $from = date2sql($from);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,2,$yr));
    $date3 = date('Y-m-d',mktime(0,0,0,$mo,3,$yr));
    $date4 = date('Y-m-d',mktime(0,0,0,$mo,4,$yr));
    $date5 = date('Y-m-d',mktime(0,0,0,$mo,5,$yr));
    $date6 = date('Y-m-d',mktime(0,0,0,$mo,6,$yr));
    $date7 = date('Y-m-d',mktime(0,0,0,$mo,7,$yr));
    $date8 = date('Y-m-d',mktime(0,0,0,$mo,8,$yr));
    $date9 = date('Y-m-d',mktime(0,0,0,$mo,9,$yr));
    $date10 = date('Y-m-d',mktime(0,0,0,$mo,10,$yr));
    $date11 = date('Y-m-d',mktime(0,0,0,$mo,11,$yr));
    $date12 = date('Y-m-d',mktime(0,0,0,$mo,12,$yr));
    $date13 = date('Y-m-d',mktime(0,0,0,$mo,13,$yr));
    $date14 = date('Y-m-d',mktime(0,0,0,$mo,14,$yr));
    $date15 = date('Y-m-d',mktime(0,0,0,$mo,15,$yr));
    $date16 = date('Y-m-d',mktime(0,0,0,$mo,16,$yr));
    $date17 = date('Y-m-d',mktime(0,0,0,$mo,17,$yr));
    $date18 = date('Y-m-d',mktime(0,0,0,$mo,18,$yr));
    $date19 = date('Y-m-d',mktime(0,0,0,$mo,19,$yr));
    $date20 = date('Y-m-d',mktime(0,0,0,$mo,20,$yr));
    $date21 = date('Y-m-d',mktime(0,0,0,$mo,21,$yr));
    $date22 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
    $date23 = date('Y-m-d',mktime(0,0,0,$mo,23,$yr));
    $date24 = date('Y-m-d',mktime(0,0,0,$mo,24,$yr));
    $date25 = date('Y-m-d',mktime(0,0,0,$mo,25,$yr));
    $date26 = date('Y-m-d',mktime(0,0,0,$mo,26,$yr));
    $date27 = date('Y-m-d',mktime(0,0,0,$mo,27,$yr));
    $date28 = date('Y-m-d',mktime(0,0,0,$mo,28,$yr));
    $date29 = date('Y-m-d',mktime(0,0,0,$mo,29,$yr));
    $date30 = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    $date31 = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN (discount1 + discount2) ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN (discount1 + discount2) ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date >= '$date3' AND trans.tran_date < '$date4' 
THEN (discount1 + discount2) ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN (discount1 + discount2) ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN (discount1 + discount2) ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN (discount1 + discount2) ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN (discount1 + discount2) ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN (discount1 + discount2) ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN (discount1 + discount2) ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN (discount1 + discount2) ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN (discount1 + discount2) ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN (discount1 + discount2) ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN (discount1 + discount2) ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN (discount1 + discount2) ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN (discount1 + discount2) ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN (discount1 + discount2) ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN (discount1 + discount2) ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN (discount1 + discount2) ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN (discount1 + discount2) ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN (discount1 + discount2) ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN (discount1 + discount2) ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN (discount1 + discount2) ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN (discount1 + discount2) ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN (discount1 + discount2) ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN (discount1 + discount2) ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN (discount1 + discount2) ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN (discount1 + discount2) ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN (discount1 + discount2) ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN (discount1 + discount2) ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN (discount1 + discount2) ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31' 
THEN (discount1 + discount2) ELSE 0 END) AS per31
		FROM 
			".TB_PREF."debtor_trans trans
		WHERE  trans.type IN(10)
		";
    $res= db_query($sql,"No transactions were returned");
    $value=db_fetch($res);
    return $value;

}

function getTransactions_invoices( $from, $stock_id)
{
    $from = date2sql($from);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,2,$yr));
    $date3 = date('Y-m-d',mktime(0,0,0,$mo,3,$yr));
    $date4 = date('Y-m-d',mktime(0,0,0,$mo,4,$yr));
    $date5 = date('Y-m-d',mktime(0,0,0,$mo,5,$yr));
    $date6 = date('Y-m-d',mktime(0,0,0,$mo,6,$yr));
    $date7 = date('Y-m-d',mktime(0,0,0,$mo,7,$yr));
    $date8 = date('Y-m-d',mktime(0,0,0,$mo,8,$yr));
    $date9 = date('Y-m-d',mktime(0,0,0,$mo,9,$yr));
    $date10 = date('Y-m-d',mktime(0,0,0,$mo,10,$yr));
    $date11 = date('Y-m-d',mktime(0,0,0,$mo,11,$yr));
    $date12 = date('Y-m-d',mktime(0,0,0,$mo,12,$yr));
    $date13 = date('Y-m-d',mktime(0,0,0,$mo,13,$yr));
    $date14 = date('Y-m-d',mktime(0,0,0,$mo,14,$yr));
    $date15 = date('Y-m-d',mktime(0,0,0,$mo,15,$yr));
    $date16 = date('Y-m-d',mktime(0,0,0,$mo,16,$yr));
    $date17 = date('Y-m-d',mktime(0,0,0,$mo,17,$yr));
    $date18 = date('Y-m-d',mktime(0,0,0,$mo,18,$yr));
    $date19 = date('Y-m-d',mktime(0,0,0,$mo,19,$yr));
    $date20 = date('Y-m-d',mktime(0,0,0,$mo,20,$yr));
    $date21 = date('Y-m-d',mktime(0,0,0,$mo,21,$yr));
    $date22 = date('Y-m-d',mktime(0,0,0,$mo,22,$yr));
    $date23 = date('Y-m-d',mktime(0,0,0,$mo,23,$yr));
    $date24 = date('Y-m-d',mktime(0,0,0,$mo,24,$yr));
    $date25 = date('Y-m-d',mktime(0,0,0,$mo,25,$yr));
    $date26 = date('Y-m-d',mktime(0,0,0,$mo,26,$yr));
    $date27 = date('Y-m-d',mktime(0,0,0,$mo,27,$yr));
    $date28 = date('Y-m-d',mktime(0,0,0,$mo,28,$yr));
    $date29 = date('Y-m-d',mktime(0,0,0,$mo,29,$yr));
    $date30 = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    $date31 = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));

    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN 1 ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN 1 ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date >= '$date3' AND trans.tran_date < '$date4' 
THEN 1 ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN 1 ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN 1 ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN 1 ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN 1 ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN 1 ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN 1 ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN 1 ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN 1 ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN 1 ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN 1 ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN 1 ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN 1 ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN 1 ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN 1 ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN 1 ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN 1 ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN 1 ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN 1 ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN 1 ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN 1 ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN 1 ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN 1 ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN 1 ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN 1 ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN 1 ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN 1 ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN 1 ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31' 
THEN 1 ELSE 0 END) AS per31
		FROM 
			".TB_PREF."debtor_trans trans
		WHERE  trans.type IN(10) 
		AND trans.ov_amount<>0
	
		";
    $res= db_query($sql,"No transactions were returned");
    $value=db_fetch($res);
    return $value;

}

function get_gl_trans_from_to_new($from_date,$to_date)
{
    $from = date2sql($from_date);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,$mon_days,$yr));
    $sql = " SELECT ".TB_PREF."chart_types.id,".TB_PREF."chart_types.name,SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=50";
    if ($from_date != "")
        $sql .= " AND ".TB_PREF."gl_trans.tran_date >= '$date1'";
    if ($to_date != "")
        $sql .= " AND ".TB_PREF."gl_trans.tran_date <= '$date2'";
    $sql .= " GROUP BY  ".TB_PREF."chart_types.id ";
    $result = db_query($sql, "Transactions for account  could not be calculated");
    return $result;
}

function get_price_for_item($stock_id)
{
    $sql = "SELECT price FROM ".TB_PREF."prices WHERE stock_id=".db_escape($stock_id);
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}

//////////////
function get_gl_trans_from_to_new11($from_date, $to_date, $account, $dimension=0, $dimension2=0)
{
    $from = date2sql($from_date);
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
    $date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date2 = date('Y-m-d',mktime(0,0,0,$mo,$mon_days,$yr));

    $sql = "SELECT ".TB_PREF."chart_master.account_name,SUM(amount) AS amt_t FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master
		WHERE ".TB_PREF."chart_master.account_code=".TB_PREF."gl_trans.account
		AND ".TB_PREF."chart_master.account_type='$account'";
    $sql .= " AND approval != 1";
    if ($from_date != "")
        $sql .= " AND tran_date >= '$date1'";
    if ($to_date != "")
        $sql .= " AND tran_date <= '$date2'";
    $sql .= " GROUP BY  ".TB_PREF."gl_trans.account ";
    $result = db_query($sql, "Transactions for account $account could not be calculated");
    return $result;
}

function get_item_name($item)
{
    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($item);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

print_customer_balances();

function print_customer_balances()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $p_q = $_POST['PARAM_1'];
    $graph = $_POST['PARAM_2'];
    $week = $_POST['PARAM_3'];
    $category = $_POST['PARAM_4'];
    $item = $_POST['PARAM_5'];
    $location = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_8'];

    if($p_q==0)
    {
        $p_q_value = "Quantity";
    }
    else
        $p_q_value = "Amount";

    if($week==0)
    {
        $week_value = "All Month";
    }
    else
        $week_value = $week;
        
    if($week==5)
    {
        $week_value = "All Four Weeks";
    }
    else
        $week_value = $week;

    if($category==0)
    {
        $category_value = _('All');
    }
    else
        $category_value = get_category_name($category);

    if($item==0)
    {
        $items_value = _('All Items');
    }
    else
        $items_value = get_item_name($item);

    if($location == ALL_TEXT)
    {
        $location_value = _('All Location');
    }
    else
        $location_value = get_location_name($location);


    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation = 'L' );

    // if ($graphics)
    {
        include_once($path_to_root . "/reporting/includes/class.graphic.inc");
        $pg = new graph();
    }

    $month = date('M',strtotime($from));
    $mo = date('m',strtotime($from));
    $yr = date('Y',strtotime($from));
    $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);
    $show1 = date('d',mktime(0,0,0,$mo,1,$yr));
    $show2 = date('d',mktime(0,0,0,$mo,2,$yr));
    $show3 = date('d',mktime(0,0,0,$mo,3,$yr));
    $show4 = date('d',mktime(0,0,0,$mo,4,$yr));
    $show5 = date('d',mktime(0,0,0,$mo,5,$yr));
    $show6 = date('d',mktime(0,0,0,$mo,6,$yr));
    $show7 = date('d',mktime(0,0,0,$mo,7,$yr));
    $show8 = date('d',mktime(0,0,0,$mo,8,$yr));
    $show9 = date('d',mktime(0,0,0,$mo,9,$yr));
    $show10 = date('d',mktime(0,0,0,$mo,10,$yr));
    $show11 = date('d',mktime(0,0,0,$mo,11,$yr));
    $show12 = date('d',mktime(0,0,0,$mo,12,$yr));
    $show13 = date('d',mktime(0,0,0,$mo,13,$yr));
    $show14 = date('d',mktime(0,0,0,$mo,14,$yr));
    $show15 = date('d',mktime(0,0,0,$mo,15,$yr));
    $show16 = date('d',mktime(0,0,0,$mo,16,$yr));
    $show17 = date('d',mktime(0,0,0,$mo,17,$yr));
    $show18 = date('d',mktime(0,0,0,$mo,18,$yr));
    $show19 = date('d',mktime(0,0,0,$mo,19,$yr));
    $show20 = date('d',mktime(0,0,0,$mo,20,$yr));
    $show21 = date('d',mktime(0,0,0,$mo,21,$yr));
    $show22 = date('d',mktime(0,0,0,$mo,22,$yr));
    $show23 = date('d',mktime(0,0,0,$mo,23,$yr));
    $show24 = date('d',mktime(0,0,0,$mo,24,$yr));
    $show25 = date('d',mktime(0,0,0,$mo,25,$yr));
    $show26 = date('d',mktime(0,0,0,$mo,26,$yr));
    $show27 = date('d',mktime(0,0,0,$mo,27,$yr));
    $show28 = date('d',mktime(0,0,0,$mo,28,$yr));
    $show29 = date('d',mktime(0,0,0,$mo,29,$yr));
    $show30 = date('d',mktime(0,0,0,$mo,30,$yr));
    $show31 = date('d',mktime(0,0,0,$mo,31,$yr));

    $cols = array(
        -2, 53, 68, 84, 99, 115, 130, 146, 161, 161, 177, 192, 208, 224, 240, 256, 272, 272,
        287, 303, 319, 334, 350, 366, 383, 383, 399, 415, 431, 447, 464, 480, 497, 515, 533,
        533, 560, 582, 600, 617, 645, 665
    );
    //-------------for week------------//

    if($week==1) {
        $cols = array(
            5, 130, 170, 210, 250, 290, 330, 370, 189, 206, 223, 240, 257, 274, 291, 308, 325, 342,
            359, 376, 393, 410, 427, 444, 461, 478, 495, 512, 529, 546, 563, 580, 480, 515, 550,
            585, 620, 655
        );
    }
    if($week==2) {
        $cols = array(
            5, 70, 87, 104, 121, 138, 155, 172, 130, 170, 210, 250, 290, 330, 370, 308, 325, 342,
            359, 376, 393, 410, 427, 444, 461, 478, 495, 512, 529, 546, 563, 580, 480, 515, 550,
            585, 620, 655
        );
    }
    if($week==3) {
        $cols = array(
            5, 70, 87, 104, 121, 138, 155, 172, 189, 206, 223, 240, 257, 274, 291, 130, 170, 210,
            250, 290, 330, 370, 427, 444, 461, 478, 495, 512, 529, 546, 563, 580, 480, 515, 550,
            585, 620, 655
        );
    }
    if($week==4) {
        $cols = array(
            5, 70, 87, 104, 121, 138, 155, 172, 189, 206, 223, 240, 257, 274, 291, 308, 325, 342,
            359, 376, 393, 410, 100, 135, 170, 205, 240, 275, 310, 345, 380, 415, 480, 515, 550,
            585, 620, 655
        );
    }
    if($week==5) {
        $cols = array(
        -2, 60, 73, 86, 99, 112, 125, 138, 152, 172, 185, 198, 212, 227, 243, 259, 272, 292,
        306, 319, 332, 347, 359, 372, 385, 406, 418, 433, 446, 461, 476, 491, 504, 517, 532,
        546, 570, 590, 610, 622, 645, 665
    );
    }
    //-----------for week end----------//

    if($p_q==1) {
        if ($mon_days == 31)
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, '', $show15, $show16, $show17, $show18, $show19,
                $show20, $show21, '', $show22, $show23, $show24, $show25, $show26, $show27, $show28,
                $show29, $show30, $show31, '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        else
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, '', $show15, $show16, $show17, $show18, $show19,
                $show20, $show21, '', $show22, $show23, $show24, $show25, $show26, $show27, $show28,
                $show29, $show30, "  ", '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
    }
    else
    {
        if ($mon_days == 31)
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', $show8, $show9,
                $show10, $show11, $show12, $show13, $show14, '', $show15, $show16, $show17,
                $show18, $show19, $show20, $show21, '', $show22, $show23, $show24, $show25,
                $show26, $show27, $show28, $show29, $show30, $show31, '', "Qty", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        else
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', $show8, $show9,
                $show10, $show11, $show12, $show13, $show14, '', $show15, $show16, $show17,
                $show18, $show19, $show20, $show21, '', $show22, $show23, $show24, $show25,
                $show26, $show27, $show28, $show29, $show30, "  ", '', "Qty", "Total", "Disc", "Cost", "Net Profit", " Net % ");
    }
//-------------------for weeks-----------------//

    if($p_q==1) {
        if ($week == 1) {
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', '', '',
                '', '', '', '', '', '', '', '', '', '',
                '', '', '', '', '', '', '', '', '', '',
                '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 2) {
            $headers = array(_(''),
                '', '', '', '', '', '', '', $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, '', '', '', '', '', '',
                '', '', '', '', '', '', '', '', '', '',
                '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 3) {
            $headers = array(_(''),
                '', '', '', '', '', '', '', '', '', '',
                '', '', '', '', $show15, $show16, $show17, $show18, $show19, $show20,
                $show21, '', '', '', '', '', '', '', '', '',
                '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 4) {
            if ($mon_days == 31)
                $headers = array(_(''),
                    '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '', '', '',
                    '', $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30,
                    $show31, "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
            else
                $headers = array(_(''),
                    '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '', '',
                    '', '', $show22, $show23, $show24, $show25, $show26, $show27, $show28,
                    $show29, $show30, "  ", "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        }
        elseif ($week == 5) {
            if ($mon_days == 31)
                $headers = array(_(''),
                '', '', '', '', '', "First", '', '', '', '', '',
                '', "Second", '', '', '', '', '', '', "Third", '',
                '', '', '', '', '', "Fourth", '', '', '', '',
                '', '', '', '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
            else
                $headers = array(_(''),
                '', '', '', '', '', "First", '', '', '', '',
                '', '', "Second", '', '', '', '', '', '',
                "Third", '', '', '', '', '', '', "Fourth", '',
                '', '', '', '', '', '', '', "Qty", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        }
    }
    else
    {
        if ($week == 1) {
            $headers = array(_(''),
                $show1, $show2, $show3, $show4, $show5, $show6, $show7, '', '', '',
                '', '', '', '', '', '', '', '', '',
                '', '', '', '', '', '', '', '', '',
                '', '', '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 2) {
            $headers = array(_(''),
                '', '', '', '', '', '', '', $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, '', '', '', '', '',
                '', '', '', '', '', '', '', '', '',
                '', '', '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 3) {
            $headers = array(_(''),
                '', '', '', '', '', '', '', '', '', '',
                '', '', '', '', $show15, $show16, $show17, $show18, $show19,
                $show20, $show21, '', '', '', '', '', '', '',
                '', '', '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        } elseif ($week == 4) {
            if ($mon_days == 31)
                $headers = array(_(''),
                    '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '', '',
                    '', '', $show22, $show23, $show24, $show25, $show26, $show27, $show28,
                    $show29, $show30, $show31, "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
            else
                $headers = array(_(''),
                    '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '', '',
                    '', '', $show22, $show23, $show24, $show25, $show26, $show27, $show28,
                    $show29, $show30, "  ", "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        }
        elseif ($week == 5) {
            if ($mon_days == 31)
                $headers = array(_(''),
                '', '', '', '', '', "First", '', '', '', '', '',
                '', "Second", '', '', '', '', '', '', "Third", '',
                '', '', '', '', '', "Fourth", '', '', '', '',
                '', '', '', '', "Quantity", "Total", "Disc", "Cost", "Net Profit", " Net % ");
            else
                $headers = array(_(''),
                '', '', '', '', '', "First", '', '', '', '',
                '', '', "Second", '', '', '', '', '', '',
                "Third", '', '', '', '', '', '', "Fourth", '',
                '', '', '', '', '', '', '', "Qty", "Total", "Disc", "Cost", "Net Profit", " Net % ");
        }
    }
//-------------------for weeks end-----------------//

    $aligns = array( 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'right', 'center');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 		'' => ''),
        2 => array('text' => _('Month'), 'from' => $month, 		'' => ''),
        3 => array('text' => _('Show Figures'), 'from' => $p_q_value, 		'' => ''),
        4 => array('text' => _('Weeks'), 'from' => $week_value, 		'' => ''),
        5 => array('text' => _('Inventory Category'), 'from' => $category_value, 		'' => ''),
        6 => array('text' => _('Items'), 'from' => $items_value, 		'' => ''),
        7 => array('text' => _('Location'), 'from' => $location_value, 		'' => ''),
    );

    $rep = new FrontReport(_('Monthly Wise Inventory Sales Report'), "AfterSalesDailyActivityReport", /*user_pagesize()*/'LEGAL', 6.5, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $sql = "SELECT master.`stock_id`,description 
FROM `0_stock_master` master
LEFT JOIN `0_stock_moves` moves ON moves.stock_id = master.stock_id
WHERE master.stock_id!=''
 ";
    if ($category != -1)
        $sql .= " AND master.category_id=".db_escape($category);
    if ($item != ALL_TEXT)
        $sql .= " AND master.stock_id=".db_escape($item);
    if ($location != ALL_TEXT)
        $sql .= " AND moves.loc_code=".db_escape($location);

    $sql .= " GROUP BY master.stock_id";
    $result = db_query($sql, "The customers could not be retrieved");

    $tot_per1 = 0;
    $tot_per2 = 0;
    $tot_per3 = 0;
    $tot_per4 = 0;
    $tot_per5 = 0;
    $tot_per6 = 0;
    $tot_per7 = 0;
    $tot_per8 = 0;
    $tot_per9 = 0;
    $tot_per10 = 0;
    $tot_per11 = 0;
    $tot_per12 = 0;
    $tot_per13 = 0;
    $tot_per14 = 0;
    $tot_per15 = 0;
    $tot_per16 = 0;
    $tot_per17 = 0;
    $tot_per18 = 0;
    $tot_per19 = 0;
    $tot_per20 = 0;
    $tot_per21 = 0;
    $tot_per22 = 0;
    $tot_per23 = 0;
    $tot_per24 = 0;
    $tot_per25 = 0;
    $tot_per26 = 0;
    $tot_per27 = 0;
    $tot_per28 = 0;
    $tot_per29 = 0;
    $tot_per30 = 0;
    $tot_per31 = 0;
    $grand_total = 0;

    $dis=getTransactions_discount($from);

    $invoices = getTransactions_invoices($from);

    //-----------for days---------------//
    $day1 = date('D',mktime(0,0,0,$mo,1,$yr));
    $day2 = date('D',mktime(0,0,0,$mo,2,$yr));
    $day3 = date('D',mktime(0,0,0,$mo,3,$yr));
    $day4 = date('D',mktime(0,0,0,$mo,4,$yr));
    $day5 = date('D',mktime(0,0,0,$mo,5,$yr));
    $day6 = date('D',mktime(0,0,0,$mo,6,$yr));
    $day7 = date('D',mktime(0,0,0,$mo,7,$yr));
    $day8 = date('D',mktime(0,0,0,$mo,8,$yr));
    $day9 = date('D',mktime(0,0,0,$mo,9,$yr));
    $day10 = date('D',mktime(0,0,0,$mo,10,$yr));
    $day11 = date('D',mktime(0,0,0,$mo,11,$yr));
    $day12 = date('D',mktime(0,0,0,$mo,12,$yr));
    $day13 = date('D',mktime(0,0,0,$mo,13,$yr));
    $day14 = date('D',mktime(0,0,0,$mo,14,$yr));
    $day15 = date('D',mktime(0,0,0,$mo,15,$yr));
    $day16 = date('D',mktime(0,0,0,$mo,16,$yr));
    $day17 = date('D',mktime(0,0,0,$mo,17,$yr));
    $day18 = date('D',mktime(0,0,0,$mo,18,$yr));
    $day19 = date('D',mktime(0,0,0,$mo,19,$yr));
    $day20 = date('D',mktime(0,0,0,$mo,20,$yr));
    $day21 = date('D',mktime(0,0,0,$mo,21,$yr));
    $day22 = date('D',mktime(0,0,0,$mo,22,$yr));
    $day23 = date('D',mktime(0,0,0,$mo,23,$yr));
    $day24 = date('D',mktime(0,0,0,$mo,24,$yr));
    $day25 = date('D',mktime(0,0,0,$mo,25,$yr));
    $day26 = date('D',mktime(0,0,0,$mo,26,$yr));
    $day27 = date('D',mktime(0,0,0,$mo,27,$yr));
    $day28 = date('D',mktime(0,0,0,$mo,28,$yr));
    $day29 = date('D',mktime(0,0,0,$mo,29,$yr));
    $day30 = date('D',mktime(0,0,0,$mo,30,$yr));
    $day31 = date('D',mktime(0,0,0,$mo,31,$yr));

    
    //---------------------month wise--------------//
    if($week == 5)
    {
        
    }
    else
    {
    $rep->Font('bold');
    if($week==0) {
        $rep->TextCol(1, 2, $day1);
        $rep->TextCol(2, 3, $day2);
        $rep->TextCol(3, 4, $day3);
        $rep->TextCol(4, 5, $day4);
        $rep->TextCol(5, 6, $day5);
        $rep->TextCol(6, 7, $day6);
        $rep->TextCol(7, 8, $day7);
        $rep->TextCol(9, 10, $day8);
        $rep->TextCol(10, 11, $day9);
        $rep->TextCol(11, 12, $day10);
        $rep->TextCol(12, 13, $day11);
        $rep->TextCol(13, 14, $day12);
        $rep->TextCol(14, 15, $day13);
        $rep->TextCol(15, 16, $day14);
        $rep->TextCol(17, 18, $day15);
        $rep->TextCol(18, 19, $day16);
        $rep->TextCol(19, 20, $day17);
        $rep->TextCol(20, 21, $day18);
        $rep->TextCol(21, 22, $day19);
        $rep->TextCol(22, 23, $day20);
        $rep->TextCol(23, 24, $day21);
        $rep->TextCol(25, 26, $day22);
        $rep->TextCol(26, 27, $day23);
        $rep->TextCol(27, 28, $day24);
        $rep->TextCol(28, 29, $day25);
        $rep->TextCol(29, 30, $day26);
        $rep->TextCol(30, 31, $day27);
        $rep->TextCol(31, 32, $day28);
        $rep->TextCol(32, 33, $day29);
        $rep->TextCol(33, 34, $day30);
        if ($mon_days == 31) {
            $rep->TextCol(34, 35, $day31);
        }
    }
    //------------------month wise-----------//
//-------------------for weeks-----------------//

    if($week==1)
    {
        $rep->TextCol(1, 2, $day1);
        $rep->TextCol(2, 3, $day2);
        $rep->TextCol(3, 4, $day3);
        $rep->TextCol(4, 5, $day4);
        $rep->TextCol(5, 6, $day5);
        $rep->TextCol(6, 7, $day6);
        $rep->TextCol(7, 8, $day7);
    }
    elseif($week==2)
    {
        $rep->TextCol(8, 9, $day8);
        $rep->TextCol(9, 10,  $day9);
        $rep->TextCol(10, 11, $day10);
        $rep->TextCol(11, 12, $day11);
        $rep->TextCol(12, 13, $day12);
        $rep->TextCol(13, 14, $day13);
        $rep->TextCol(14, 15, $day14);
    }
    elseif($week==3)
    {
        $rep->TextCol(15, 16, $day15);
        $rep->TextCol(16, 17, $day16);
        $rep->TextCol(17, 18, $day17);
        $rep->TextCol(18, 19, $day18);
        $rep->TextCol(19, 20, $day19);
        $rep->TextCol(20, 21, $day20);
        $rep->TextCol(21, 22, $day21);
    }
    elseif($week==4)
    {
        $rep->TextCol(22, 23, $day22);
        $rep->TextCol(23, 24, $day23);
        $rep->TextCol(24, 25, $day24);
        $rep->TextCol(25, 26, $day25);
        $rep->TextCol(26, 27, $day26);
        $rep->TextCol(27, 28, $day27);
        $rep->TextCol(28, 29, $day28);
        $rep->TextCol(29, 30, $day29);
        $rep->TextCol(30, 31, $day30);
        if($mon_days==31) {
            $rep->TextCol(31, 32, $day31);
        }
    }
//-------------------for weeks end-----------------//
    $rep->Font('');
    $rep->Line($rep->row - 2);
    }
    

    //----------------------//


    $tot_per1=$tot_per2=$tot_per3=0;

    //for dynamic description and total values in x-axis and y-axis of graph respectively
    $multi_description = array();
    $line_total = array();
    $i= 0;

    while ($myrow=db_fetch($result)) {

        if($p_q==0)
        {
            $res=getTransactions($from,$myrow['stock_id']);
            $s_cost=getTransactions_standard_cost($from,$myrow['stock_id'],$week);
            $qty=getTransactions_qty_amount($from, $myrow['stock_id'],$week);
            $qty_cost=getTransactions_qty_cost($from, $myrow['stock_id'], $week);
            $line_disc = getTransactions_linewise_disc($from, $myrow['stock_id'], $week);
        }
        else
        {
            $res=getTransactions2($from,$myrow['stock_id']);
            $s_cost=getTransactions_standard_cost($from,$myrow['stock_id'],$week);
            $qty=getTransactions_qty_amount($from, $myrow['stock_id'],$week);
            $qty_cost=getTransactions_qty_cost($from, $myrow['stock_id'], $week);
            $line_disc = getTransactions_linewise_disc($from, $myrow['stock_id'], $week);
        }

        while ($Data13=db_fetch($res)) {

            if($mon_days==31) {
                $Total = $Data13['per01'] + $Data13['per02'] + $Data13['per03'] + $Data13['per04'] +
                    $Data13['per05'] + $Data13['per06'] + $Data13['per07'] + $Data13['per08'] + $Data13['per09'] +
                    $Data13['per10'] + $Data13['per11'] + $Data13['per12'] + $Data13['per13'] + $Data13['per14'] +
                    $Data13['per15'] + $Data13['per16'] + $Data13['per17'] + $Data13['per18'] + $Data13['per19'] +
                    $Data13['per20'] + $Data13['per21'] + $Data13['per22'] + $Data13['per23'] + $Data13['per24'] +
                    $Data13['per25'] + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                    $Data13['per30'] + $Data13['per31'];
            }
            else
                $Total = $Data13['per01'] + $Data13['per02'] + $Data13['per03'] + $Data13['per04'] +
                    $Data13['per05'] + $Data13['per06'] + $Data13['per07'] + $Data13['per08'] + $Data13['per09'] +
                    $Data13['per10'] + $Data13['per11'] + $Data13['per12'] + $Data13['per13'] + $Data13['per14'] +
                    $Data13['per15'] + $Data13['per16'] + $Data13['per17'] + $Data13['per18'] + $Data13['per19'] +
                    $Data13['per20'] + $Data13['per21'] + $Data13['per22'] + $Data13['per23'] + $Data13['per24'] +
                    $Data13['per25'] + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                    $Data13['per30'];
if($p_q==1) {
    $Net_profit = $Total - $s_cost;

    $Net_percent = $Net_profit / $qty_cost * 100;
}
elseif ($p_q==0)
{
    $Net_profit = $qty_cost - $line_disc - $s_cost;

    $Net_percent = $Net_profit / $qty_cost * 100;
}
            //----------------for week-------------------//
            if($week==1){
                $Total_w1 = $Data13['per01'] + $Data13['per02'] + $Data13['per03'] + $Data13['per04'] +
                    $Data13['per05'] + $Data13['per06'] + $Data13['per07'];

                $Net_profit_w1 = $qty_cost - $s_cost - $line_disc;
            }
            elseif($week==2){
                $Total_w2 = $Data13['per08'] + $Data13['per09'] + $Data13['per10'] + $Data13['per11']
                    + $Data13['per12'] + $Data13['per13'] + $Data13['per14'];

                $Net_profit_w2 = $qty_cost - $s_cost - $line_disc;
            }
            elseif($week==3){
                $Total_w3 = $Data13['per15'] + $Data13['per16'] + $Data13['per17'] + $Data13['per18'] + $Data13['per19'] +
                    $Data13['per20'] + $Data13['per21'];

                $Net_profit_w3 = $qty_cost - $s_cost - $line_disc;
            }
            elseif($week==4){
                if($mon_days==31) {
                    $Total_w4 = $Data13['per22'] + $Data13['per23'] + $Data13['per24'] + $Data13['per25']
                        + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                        $Data13['per30'] + $Data13['per31'];
                }
                else
                    $Total_w4 = $Data13['per22'] + $Data13['per23'] + $Data13['per24'] + $Data13['per25']
                        + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                        $Data13['per30'];

                $Net_profit_w4 = $qty_cost - $s_cost - $line_disc;
            }
            //----------------for week-------------------//


            if($Total==0 && $qty ==0)continue;

            $multi_description[$i] = $myrow['description'];

            $rep->NewLine();
            //---------------------month wise
            
            $Total_w1 = $Data13['per01'] + $Data13['per02'] + $Data13['per03'] +
                    $Data13['per04'] + $Data13['per05'] + $Data13['per06'] + $Data13['per07'];
                    
                $Total_w2 = $Data13['per08'] + $Data13['per09'] + $Data13['per10'] +
                    $Data13['per11'] + $Data13['per12'] + $Data13['per13'] + $Data13['per14'];
                    
                $Total_w3 = $Data13['per15'] + $Data13['per16'] + $Data13['per17'] + $Data13['per18'] + $Data13['per19'] +
                    $Data13['per20'] + $Data13['per21'];
                    
                if ($mon_days == 31) {
                        $Total_w4 = $Data13['per22'] + $Data13['per23'] + $Data13['per24'] + $Data13['per25']
                        + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                        $Data13['per30'] + $Data13['per31'];
                }
                else {
                    $Total_w4 = $Data13['per22'] + $Data13['per23'] + $Data13['per24'] + $Data13['per25']
                        + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
                        $Data13['per30'];
                }
                
            if($week==0) {
                $prices_list = get_price_for_item($myrow['stock_id']);
                
        	if (!$destination)
    			{
    			$str = $myrow['description'];
    			if (strlen($str) > 20)
                $str = substr($str, 0, 18).'...';
    			$rep->TextCol(0, 1, $str . "-" . $prices_list);
    			    }
                else
    			$rep->TextCol(1, 2, $myrow['name']);

                $rep->TextCol(1, 2, round($Data13['per01']));
                $rep->TextCol(2, 3, round($Data13['per02']));
                $rep->TextCol(3, 4, round($Data13['per03']));
                $rep->TextCol(4, 5, round($Data13['per04']));
                $rep->TextCol(5, 6, round($Data13['per05']));
                $rep->TextCol(6, 7, round($Data13['per06']));
                $rep->TextCol(7, 8, round($Data13['per07']));
                
                // $rep->Font('bold');
                // $rep->TextCol(8, 9, round($Total_w1));
                // $rep->Font('');
                
                $rep->TextCol(9, 10, round($Data13['per08']));
                $rep->TextCol(10, 11, round($Data13['per09']));
                $rep->TextCol(11, 12, round($Data13['per10']));
                $rep->TextCol(12, 13, round($Data13['per11']));
                $rep->TextCol(13, 14, round($Data13['per12']));
                $rep->TextCol(14, 15, round($Data13['per13']));
                $rep->TextCol(15, 16, round($Data13['per14']));

                // $rep->Font('bold');
                // $rep->TextCol(16, 17, round($Total_w2));
                // $rep->Font('');

                $rep->TextCol(17, 18, round($Data13['per15']));
                $rep->TextCol(18, 19, round($Data13['per16']));
                $rep->TextCol(19, 20, round($Data13['per17']));
                $rep->TextCol(20, 21, round($Data13['per18']));
                $rep->TextCol(21, 22, round($Data13['per19']));
                $rep->TextCol(22, 23, round($Data13['per20']));
                $rep->TextCol(23, 24, round($Data13['per21']));

                // $rep->Font('bold');
                // $rep->TextCol(24, 25, round($Total_w3));
                // $rep->Font('');

                $rep->TextCol(25, 26, round($Data13['per22']));
                $rep->TextCol(26, 27, round($Data13['per23']));
                $rep->TextCol(27, 28, round($Data13['per24']));
                $rep->TextCol(28, 29, round($Data13['per25']));
                $rep->TextCol(29, 30, round($Data13['per26']));
                $rep->TextCol(30, 31, round($Data13['per27']));
                $rep->TextCol(31, 32, round($Data13['per28']));
                $rep->TextCol(32, 33, round($Data13['per29']));
                $rep->TextCol(33, 34, round($Data13['per30']));
                if ($mon_days == 31) {
                    $rep->TextCol(34, 35, round($Data13['per31']));
                }
                
                // $rep->Font('bold');
                // $rep->TextCol(35, 36, round($Total_w4));
                // $rep->Font('');

                $rep->Font('bold');
                if ($p_q == 1) {
                    $total_qty += $qty;
                    $rep->AmountCol(36, 37, $qty);
                }
                elseif($p_q == 0) {
                    $rep->AmountCol(36, 37, $Total);
                }
                    $total_qty_cost += $qty_cost;
                    $rep->AmountCol(37, 38, $qty_cost);

                    $total_line_disc += $line_disc;
                    $rep->AmountCol(38, 39, $line_disc);

                    $total_cost += $s_cost;
                    $rep->AmountCol(39, 40, $s_cost);

                    $total_net_profit += $Net_profit;
                    $rep->AmountCol(40, 41, $Net_profit);

                    $rep->AmountCol(41, 42, $Net_percent);
                    $rep->TextCol(41, 42, "        %");
                    $rep->Font('');

                $line_total[$i] = ($Total);
                $i++;
            }
            //---------------------month wise end--------------//

            //----------------------for weeks---------------//
            if($week==1)
            {
                $prices_list = get_price_for_item($myrow['stock_id']);
                $rep->TextCol(0, 1, $myrow['description']."-".$prices_list);
                $rep->TextCol(1, 2, round($Data13['per01']));
                $rep->TextCol(2, 3, round($Data13['per02']));
                $rep->TextCol(3, 4, round($Data13['per03']));
                $rep->TextCol(4, 5, round($Data13['per04']));
                $rep->TextCol(5, 6, round($Data13['per05']));
                $rep->TextCol(6, 7, round($Data13['per06']));
                $rep->TextCol(7, 8, round($Data13['per07']));

                $rep->Font('bold');
                if($p_q==1) {
                    $total_qty += $qty;
                    $rep->AmountCol(32, 33, $qty);
                }
                elseif ($p_q==0)
                {
                    $rep->AmountCol(32, 33, $Total_w1);
                }
                    $total_qty_cost += $qty_cost;
                    $rep->AmountCol(33, 34, $qty_cost);
                    $total_line_disc += $line_disc;
                    $rep->AmountCol(34, 35, $line_disc);
                    $total_cost += $s_cost;
                    $rep->AmountCol(35, 36, $s_cost);
                    $total_net_profit_w1 += $Net_profit_w1;
                    $rep->AmountCol(36, 37, $Net_profit_w1);

                    $Net_percent_w1 = $Net_profit_w1 / $qty_cost * 100;
                    $rep->AmountCol(37, 38, $Net_percent_w1);
                    $rep->TextCol(37, 38, "        %");
                    $rep->Font('');
            }
            elseif($week==2)
            {
                $prices_list = get_price_for_item($myrow['stock_id']);
                $rep->TextCol(0, 1, $myrow['description']."-".$prices_list);
                $rep->TextCol(8, 9, round($Data13['per08']));
                $rep->TextCol(9, 10, round($Data13['per09']));
                $rep->TextCol(10, 11, round($Data13['per10']));
                $rep->TextCol(11, 12, round($Data13['per11']));
                $rep->TextCol(12, 13, round($Data13['per12']));
                $rep->TextCol(13, 14, round($Data13['per13']));
                $rep->TextCol(14, 15, round($Data13['per14']));

                $rep->Font('bold');
                if($p_q==1) {
                    $total_qty += $qty;
                    $rep->AmountCol(32, 33, $qty);
                }
                elseif ($p_q==0)
                {
                    $rep->AmountCol(32, 33, $Total_w2);
                }
                $total_qty_cost += $qty_cost;
                $rep->AmountCol(33, 34, $qty_cost);
                $total_line_disc += $line_disc;
                $rep->AmountCol(34, 35, $line_disc);
                $total_cost += $s_cost;
                $rep->AmountCol(35, 36, $s_cost);
                $total_net_profit_w2 += $Net_profit_w2;
                $rep->AmountCol(36, 37, $Net_profit_w2);

                $Net_percent_w2 = $Net_profit_w2 / $qty_cost * 100;
                $rep->AmountCol(37, 38, $Net_percent_w2);
                $rep->TextCol(37, 38, "        %");
                $rep->Font('');
            }
            elseif($week==3)
            {
                $prices_list = get_price_for_item($myrow['stock_id']);
                $rep->TextCol(0, 1, $myrow['description']."-".$prices_list);
                $rep->TextCol(15, 16, round($Data13['per15']));
                $rep->TextCol(16, 17, round($Data13['per16']));
                $rep->TextCol(17, 18, round($Data13['per17']));
                $rep->TextCol(18, 19, round($Data13['per18']));
                $rep->TextCol(19, 20, round($Data13['per19']));
                $rep->TextCol(20, 21, round($Data13['per20']));
                $rep->TextCol(21, 22, round($Data13['per21']));

                $rep->Font('bold');
                if($p_q==1) {
                    $total_qty += $qty;
                    $rep->AmountCol(32, 33, $qty);
                }
                elseif ($p_q==0)
                {
                    $rep->AmountCol(32, 33, $Total_w3);
                }
                $total_qty_cost += $qty_cost;
                $rep->AmountCol(33, 34, $qty_cost);
                $total_line_disc += $line_disc;
                $rep->AmountCol(34, 35, $line_disc);
                $total_cost += $s_cost;
                $rep->AmountCol(35, 36, $s_cost);
                $total_net_profit_w3 += $Net_profit_w3;
                $rep->AmountCol(36, 37, $Net_profit_w3);

                $Net_percent_w3 = $Net_profit_w3 / $qty_cost * 100;
                $rep->AmountCol(37, 38, $Net_percent_w3);
                $rep->TextCol(37, 38, "        %");
                $rep->Font('');
            }
            elseif($week==4)
            {
                $prices_list = get_price_for_item($myrow['stock_id']);
                $rep->TextCol(0, 1, $myrow['description']."-".$prices_list);
                $rep->TextCol(22, 23, round($Data13['per22']));
                $rep->TextCol(23, 24, round($Data13['per23']));
                $rep->TextCol(24, 25, round($Data13['per24']));
                $rep->TextCol(25, 26, round($Data13['per25']));
                $rep->TextCol(26, 27, round($Data13['per26']));
                $rep->TextCol(27, 28, round($Data13['per27']));
                $rep->TextCol(28, 29, round($Data13['per28']));
                $rep->TextCol(29, 30, round($Data13['per29']));
                $rep->TextCol(30, 31, round($Data13['per30']));
                if($mon_days==31) {
                    $rep->TextCol(31, 32, round($Data13['per31']));
                }

                $rep->Font('bold');
                if($p_q==1) {
                    $total_qty += $qty;
                    $rep->AmountCol(32, 33, $qty);
                }
                elseif ($p_q==0)
                {
                    $rep->AmountCol(32, 33, $Total_w4);
                }
                $total_qty_cost += $qty_cost;
                $rep->AmountCol(33, 34, $qty_cost);
                $total_line_disc += $line_disc;
                $rep->AmountCol(34, 35, $line_disc);
                $total_cost += $s_cost;
                $rep->AmountCol(35, 36, $s_cost);
                $total_net_profit_w4 += $Net_profit_w4;
                $rep->AmountCol(36, 37, $Net_profit_w4);

                $Net_percent_w4 = $Net_profit_w4 / $qty_cost * 100;
                $rep->AmountCol(37, 38, $Net_percent_w4);
                $rep->TextCol(37, 38, "        %");
                $rep->Font('');
            }
            
              elseif($week==5)
            {
                $prices_list = get_price_for_item($myrow['stock_id']);
                $rep->TextCol(0, 1, $myrow['description']."-".$prices_list);
                $rep->TextCol(6, 8, round($Total_w1));
    			$rep->TextCol(13, 15, round($Total_w2));
    			$rep->TextCol(20, 22, round($Total_w3));
    			$rep->TextCol(27, 29, round($Total_w4));
                $rep->Font('bold');
                if ($p_q == 1) {
                    $total_qty += $qty;
                    $rep->AmountCol(36, 37, $qty);
                }
                elseif($p_q == 0) {
                    $rep->AmountCol(36, 37, $Total);
                }
                    $total_qty_cost += $qty_cost;
                    $rep->AmountCol(37, 38, $qty_cost);

                    $total_line_disc += $line_disc;
                    $rep->AmountCol(38, 39, $line_disc);

                    $total_cost += $s_cost;
                    $rep->AmountCol(39, 40, $s_cost);

                    $total_net_profit += $Net_profit;
                    $rep->AmountCol(40, 41, $Net_profit);

                    $rep->AmountCol(41, 42, $Net_percent);
                    $rep->TextCol(41, 42, "        %");
                    $rep->Font('');
            }
            
//----------------------for weeks end---------------//

            //-------------for grand_total row------------//
            $tot_per1 += $Data13['per01'];
            $tot_per2 += $Data13['per02'];
            $tot_per3 += $Data13['per03'];
            $tot_per4 += $Data13['per04'];
            $tot_per5 += $Data13['per05'];
            $tot_per6 += $Data13['per06'];
            $tot_per7 += $Data13['per07'];
            $tot_per8 += $Data13['per08'];
            $tot_per9 += $Data13['per09'];
            $tot_per10 += $Data13['per10'];
            $tot_per11 += $Data13['per11'];
            $tot_per12 += $Data13['per12'];
            $tot_per13 += $Data13['per13'];
            $tot_per14 += $Data13['per14'];
            $tot_per15 += $Data13['per15'];
            $tot_per16 += $Data13['per16'];
            $tot_per17 += $Data13['per17'];
            $tot_per18 += $Data13['per18'];
            $tot_per19 += $Data13['per19'];
            $tot_per20 += $Data13['per20'];
            $tot_per21 += $Data13['per21'];
            $tot_per22 += $Data13['per22'];
            $tot_per23 += $Data13['per23'];
            $tot_per24 += $Data13['per24'];
            $tot_per25 += $Data13['per25'];
            $tot_per26 += $Data13['per26'];
            $tot_per27 += $Data13['per27'];
            $tot_per28 += $Data13['per28'];
            $tot_per29 += $Data13['per29'];
            $tot_per30 += $Data13['per30'];
            $tot_per31 += $Data13['per31'];

            $total[0] = $tot_per1;
            $total[1] = $tot_per2;
            $total[2] = $tot_per3;
            $total[3] = $tot_per4;
            $total[4] = $tot_per5;
            $total[5] = $tot_per6;
            $total[6] = $tot_per7;
            $total[7] = $tot_per8;
            $total[8] = $tot_per9;
            $total[9] = $tot_per10;
            $total[10] = $tot_per11;
            $total[11] = $tot_per12;
            $total[12] = $tot_per13;
            $total[13] = $tot_per14;
            $total[14] = $tot_per15;
            $total[15] = $tot_per16;
            $total[16] = $tot_per17;
            $total[17] = $tot_per18;
            $total[18] = $tot_per19;
            $total[19] = $tot_per20;
            $total[20] = $tot_per21;
            $total[21] = $tot_per22;
            $total[22] = $tot_per23;
            $total[23] = $tot_per24;
            $total[24] = $tot_per25;
            $total[25] = $tot_per26;
            $total[26] = $tot_per27;
            $total[27] = $tot_per28;
            $total[28] = $tot_per29;
            $total[29] = $tot_per30;
            $total[30] = $tot_per31;

            if($mon_days==31)
            {
                $grand_total = $tot_per1 + $tot_per2 + $tot_per3 + $tot_per4 + $tot_per5 +
                    $tot_per6 + $tot_per7 + $tot_per8 + $tot_per9 + $tot_per10 + $tot_per11 +
                    $tot_per12 + $tot_per13 + $tot_per14 + $tot_per15 + $tot_per16 + $tot_per17 +
                    $tot_per18 + $tot_per19 + $tot_per20 + $tot_per21 + $tot_per22 + $tot_per23 +
                    $tot_per24 + $tot_per25 + $tot_per26 + $tot_per27 + $tot_per28 + $tot_per29 +
                    $tot_per30 + $tot_per31;
            }
            else
                $grand_total = $tot_per1 + $tot_per2 + $tot_per3 + $tot_per4 + $tot_per5 +
                    $tot_per6 + $tot_per7 + $tot_per8 + $tot_per9 + $tot_per10 + $tot_per11 +
                    $tot_per12 + $tot_per13 + $tot_per14 + $tot_per15 + $tot_per16 + $tot_per17 +
                    $tot_per18 + $tot_per19 + $tot_per20 + $tot_per21 + $tot_per22 + $tot_per23 +
                    $tot_per24 + $tot_per25 + $tot_per26 + $tot_per27 + $tot_per28 + $tot_per29 +
                    $tot_per30;
        }

        //----------  ----for weeks------------//
        if($week==1)
        {
            $grand_total_w1 = $tot_per1 + $tot_per2 + $tot_per3 + $tot_per4 + $tot_per5 +
                $tot_per6 + $tot_per7;
        }
        if($week==2)
        {
            $grand_total_w2 = $tot_per8 + $tot_per9 + $tot_per10 + $tot_per11 + $tot_per12
                + $tot_per13 + $tot_per14;
        }
        if($week==3)
        {
            $grand_total_w3 = $tot_per15 + $tot_per16 + $tot_per17 + $tot_per18 + $tot_per19
                + $tot_per20 + $tot_per21;
        }
        if($week==4)
        {
            if($mon_days==31){
                $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 +
                    $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30 + $tot_per31;
            }
            else
                $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 +
                    $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30;
        }
        //--------------for weeks end-----------//
        $rep->Line($rep->row - 2);
    }


    if($p_q==0){
        $rep->Font('bold');
        $rep->NewLine(2);
        //--------------month wise------------//
        $grand_total_w1 = $tot_per1 + $tot_per2 + $tot_per3 + $tot_per4 + $tot_per5 +
                $tot_per6 + $tot_per7;
                
        $grand_total_w2 = $tot_per8 + $tot_per9 + $tot_per10 + $tot_per11 + $tot_per12
                + $tot_per13 + $tot_per14;
                
        $grand_total_w3 = $tot_per15 + $tot_per16 + $tot_per17 + $tot_per18 + $tot_per19 + $tot_per20 + $tot_per21;
        
         if($mon_days==31) {
        $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 + $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30 + $tot_per31;
            }
            else
            {
        $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 + $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30;
            }
             
        if($week==0){
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(1, 2, $tot_per1);
            $rep->TextCol(2, 3, $tot_per2);
            $rep->TextCol(3, 4, $tot_per3);
            $rep->TextCol(4, 5, $tot_per4);
            $rep->TextCol(5, 6, $tot_per5);
            $rep->TextCol(6, 7, $tot_per6);
            $rep->TextCol(7, 8, $tot_per7);

            // $rep->TextCol(8, 9, "(".$grand_total_w1.")");

            $rep->TextCol(9, 10, $tot_per8);
            $rep->TextCol(10, 11, $tot_per9);
            $rep->TextCol(11, 12, $tot_per10);
            $rep->TextCol(12, 13, $tot_per11);
            $rep->TextCol(13, 14, $tot_per12);
            $rep->TextCol(14, 15, $tot_per13);
            $rep->TextCol(15, 16, $tot_per14);

            // $rep->TextCol(16, 17, "(".$grand_total_w2.")");

            $rep->TextCol(17, 18, $tot_per15);
            $rep->TextCol(18, 19, $tot_per16);
            $rep->TextCol(19, 20, $tot_per17);
            $rep->TextCol(20, 21, $tot_per18);
            $rep->TextCol(21, 22, $tot_per19);
            $rep->TextCol(22, 23, $tot_per20);
            $rep->TextCol(23, 24, $tot_per21);

            // $rep->TextCol(24, 25, "(".$grand_total_w3.")");

            $rep->TextCol(25, 26, $tot_per22);
            $rep->TextCol(26, 27, $tot_per23);
            $rep->TextCol(27, 28, $tot_per24);
            $rep->TextCol(28, 29, $tot_per25);
            $rep->TextCol(29, 30, $tot_per26);
            $rep->TextCol(30, 31, $tot_per27);
            $rep->TextCol(31, 32, $tot_per28);
            $rep->TextCol(32, 33, $tot_per29);
            $rep->TextCol(33, 34, $tot_per30);

            if($mon_days==31) {
                $rep->TextCol(34, 35, $tot_per31);
            }
          
            // $rep->TextCol(35, 36, "(".$grand_total_w4.")");
            
            $rep->AmountCol(36, 37, $grand_total, 0);

            $rep->AmountCol(37, 38, $total_qty_cost, 0);
            $rep->AmountCol(38, 39, $total_line_disc, 0);
            $rep->AmountCol(39, 40, $total_cost, 0);
            $rep->AmountCol(40, 41, $total_net_profit, 0);
}
//--------------month wise end------------//

        //--------------------for weeks------------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(1, 2, $tot_per1);
            $rep->TextCol(2, 3, $tot_per2);
            $rep->TextCol(3, 4, $tot_per3);
            $rep->TextCol(4, 5, $tot_per4);
            $rep->TextCol(5, 6, $tot_per5);
            $rep->TextCol(6, 7, $tot_per6);
            $rep->TextCol(7, 8, $tot_per7);
            $rep->AmountCol(32, 33, $grand_total_w1, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(8, 9, $tot_per8);
            $rep->TextCol(9, 10, $tot_per9);
            $rep->TextCol(10, 11, $tot_per10);
            $rep->TextCol(11, 12, $tot_per11);
            $rep->TextCol(12, 13, $tot_per12);
            $rep->TextCol(13, 14, $tot_per13);
            $rep->TextCol(14, 15, $tot_per14);
            $rep->AmountCol(32, 33, $grand_total_w2, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(15, 16, $tot_per15);
            $rep->TextCol(16, 17, $tot_per16);
            $rep->TextCol(17, 18, $tot_per17);
            $rep->TextCol(18, 19, $tot_per18);
            $rep->TextCol(19, 20, $tot_per19);
            $rep->TextCol(20, 21, $tot_per20);
            $rep->TextCol(21, 22, $tot_per21);
            $rep->AmountCol(32, 33, $grand_total_w3, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(22, 23, $tot_per22);
            $rep->TextCol(23, 24, $tot_per23);
            $rep->TextCol(24, 25, $tot_per24);
            $rep->TextCol(25, 26, $tot_per25);
            $rep->TextCol(26, 27, $tot_per26);
            $rep->TextCol(27, 28, $tot_per27);
            $rep->TextCol(28, 29, $tot_per28);
            $rep->TextCol(29, 30, $tot_per29);
            $rep->TextCol(30, 31, $tot_per30);

            if($mon_days==31) {
                $rep->TextCol(31, 32, $tot_per31);
            }

            $rep->AmountCol(32, 33, $grand_total_w4, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w4, 0);
        }
         elseif($week==5)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(6, 8, "(".$grand_total_w1.")");
            $rep->TextCol(13, 15, "(".$grand_total_w2.")");
            $rep->TextCol(20, 22, "(".$grand_total_w3.")");
            $rep->TextCol(27, 29, "(".$grand_total_w4.")");
            
            $rep->AmountCol(36, 37, $grand_total, 0);
            $rep->AmountCol(37, 38, $total_qty_cost, 0);
            $rep->AmountCol(38, 39, $total_line_disc, 0);
            $rep->AmountCol(39, 40, $total_cost, 0);
            $rep->AmountCol(40, 41, $total_net_profit, 0);
        }
   
        //-----------------weeks end---------------------//

        //-------------for no of invoices row------------//

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);

        //------------month wise---------//
        
        $invoices_total_w1 = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                $invoices['per07'];
                
        $invoices_total_w2 = $invoices['per08'] + $invoices['per09'] +
                $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                $invoices['per13'] + $invoices['per14'];
                
        $invoices_total_w3 = $invoices['per15'] +
                $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                $invoices['per19'] + $invoices['per20'] + $invoices['per21'];
                
        if ($mon_days == 31) {
        $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] + $invoices['per25'] + $invoices['per26'] + $invoices['per27'] + $invoices['per28'] + $invoices['per29'] + $invoices['per30'] + $invoices['per31'];
            }
            else
            {
        $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +$invoices['per25'] + $invoices['per26'] + $invoices['per27'] + $invoices['per28'] + $invoices['per29'] + $invoices['per30'];
            }
            
            if($mon_days==31) {
                $invoices_total = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                    $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                    $invoices['per07'] + $invoices['per08'] + $invoices['per09'] +
                    $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                    $invoices['per13'] + $invoices['per14'] + $invoices['per15'] +
                    $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                    $invoices['per19'] + $invoices['per20'] + $invoices['per21'] +
                    $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'] +
                    $invoices['per31'];
            }
            else
            {
                $invoices_total = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                    $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                    $invoices['per07'] + $invoices['per08'] + $invoices['per09'] +
                    $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                    $invoices['per13'] + $invoices['per14'] + $invoices['per15'] +
                    $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                    $invoices['per19'] + $invoices['per20'] + $invoices['per21'] +
                    $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'];
            }
            
        if($week==0) {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(1, 2, $invoices['per01'], 0);
            $rep->AmountCol(2, 3, $invoices['per02'], 0);
            $rep->AmountCol(3, 4, $invoices['per03'], 0);
            $rep->AmountCol(4, 5, $invoices['per04'], 0);
            $rep->AmountCol(5, 6, $invoices['per05'], 0);
            $rep->AmountCol(6, 7, $invoices['per06'], 0);
            $rep->AmountCol(7, 8, $invoices['per07'], 0);

            // $rep->TextCol(8, 9, "(".$invoices_total_w1.")");

            $rep->AmountCol(9, 10, $invoices['per08'], 0);
            $rep->AmountCol(10, 11, $invoices['per09'], 0);
            $rep->AmountCol(11, 12, $invoices['per10'], 0);
            $rep->AmountCol(12, 13, $invoices['per11'], 0);
            $rep->AmountCol(13, 14, $invoices['per12'], 0);
            $rep->AmountCol(14, 15, $invoices['per13'], 0);
            $rep->AmountCol(15, 16, $invoices['per14'], 0);

            // $rep->TextCol(16, 17, "(".$invoices_total_w2.")");

            $rep->AmountCol(17, 18, $invoices['per15'], 0);
            $rep->AmountCol(18, 19, $invoices['per16'], 0);
            $rep->AmountCol(19, 20, $invoices['per17'], 0);
            $rep->AmountCol(20, 21, $invoices['per18'], 0);
            $rep->AmountCol(21, 22, $invoices['per19'], 0);
            $rep->AmountCol(22, 23, $invoices['per20'], 0);
            $rep->AmountCol(23, 24, $invoices['per21'], 0);

            // $rep->TextCol(24, 25, "(".$invoices_total_w3.")");

            $rep->AmountCol(25, 26, $invoices['per22'], 0);
            $rep->AmountCol(26, 27, $invoices['per23'], 0);
            $rep->AmountCol(27, 28, $invoices['per24'], 0);
            $rep->AmountCol(28, 29, $invoices['per25'], 0);
            $rep->AmountCol(29, 30, $invoices['per26'], 0);
            $rep->AmountCol(30, 31, $invoices['per27'], 0);
            $rep->AmountCol(31, 32, $invoices['per28'], 0);
            $rep->AmountCol(32, 33, $invoices['per29'], 0);
            $rep->AmountCol(33, 34, $invoices['per30'], 0);

            if ($mon_days == 31) {
                $rep->AmountCol(34, 35, $invoices['per31'], 0);
            }
            
            // $rep->TextCol(35, 36, "(".$invoices_total_w4.")");
            
            $rep->AmountCol(36, 37, $invoices_total, 0);

        }
        //------------month wise end---------//

//---------------------for weeks------------------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(1, 2, $invoices['per01'], 0);
            $rep->AmountCol(2, 3, $invoices['per02'], 0);
            $rep->AmountCol(3, 4, $invoices['per03'], 0);
            $rep->AmountCol(4, 5, $invoices['per04'], 0);
            $rep->AmountCol(5, 6, $invoices['per05'], 0);
            $rep->AmountCol(6, 7, $invoices['per06'], 0);
            $rep->AmountCol(7, 8, $invoices['per07'], 0);

            $invoices_total_w1 = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                $invoices['per07'];

            $rep->AmountCol(33, 34, $invoices_total_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(8, 9, $invoices['per08'], 0);
            $rep->AmountCol(9, 10, $invoices['per09'], 0);
            $rep->AmountCol(10, 11, $invoices['per10'], 0);
            $rep->AmountCol(11, 12, $invoices['per11'], 0);
            $rep->AmountCol(12, 13, $invoices['per12'], 0);
            $rep->AmountCol(13, 14, $invoices['per13'], 0);
            $rep->AmountCol(14, 15, $invoices['per14'], 0);

            $invoices_total_w2 = $invoices['per08'] + $invoices['per09'] +
                $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                $invoices['per13'] + $invoices['per14'];

            $rep->AmountCol(33, 34, $invoices_total_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(15, 16, $invoices['per15'], 0);
            $rep->AmountCol(16, 17, $invoices['per16'], 0);
            $rep->AmountCol(17, 18, $invoices['per17'], 0);
            $rep->AmountCol(18, 19, $invoices['per18'], 0);
            $rep->AmountCol(19, 20, $invoices['per19'], 0);
            $rep->AmountCol(20, 21, $invoices['per20'], 0);
            $rep->AmountCol(21, 22, $invoices['per21'], 0);

            $invoices_total_w3 = $invoices['per15'] +
                $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                $invoices['per19'] + $invoices['per20'] + $invoices['per21'];

            $rep->AmountCol(33, 34, $invoices_total_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(22, 23, $invoices['per22'], 0);
            $rep->AmountCol(23, 24, $invoices['per23'], 0);
            $rep->AmountCol(24, 25, $invoices['per24'], 0);
            $rep->AmountCol(25, 26, $invoices['per25'], 0);
            $rep->AmountCol(26, 27, $invoices['per26'], 0);
            $rep->AmountCol(27, 28, $invoices['per27'], 0);
            $rep->AmountCol(28, 29, $invoices['per28'], 0);
            $rep->AmountCol(29, 30, $invoices['per29'], 0);
            $rep->AmountCol(30, 31, $invoices['per30'], 0);

            if($mon_days==31) {
                $rep->AmountCol(31, 32, $invoices['per31'], 0);
                $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'] +
                    $invoices['per31'];
            }
            else
                $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'];

            $rep->AmountCol(33, 34, $invoices_total_w4, 0);
        }
        
         elseif($week == 5)
            {
                $rep->TextCol(0, 1, _('No Of Invoices'));
                $rep->TextCol(6, 8, "(".$invoices_total_w1.")");
                $rep->TextCol(13, 15, "(".$invoices_total_w2.")");
                $rep->TextCol(20, 22, "(".$invoices_total_w3.")");
                $rep->TextCol(27, 29, "(".$invoices_total_w4.")");
                $rep->AmountCol(36, 37, $invoices_total, 0);
            }

        //----------------for weeks end-------------//

        //-------------for discount row------------//
        $rep->Line($rep->row - 8);
        $rep->NewLine(2);

        //----------------month wise------------//
        
        $overall_discount_w1 = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                $dis['per05'] + $dis['per06'] + $dis['per07'];
                
        $overall_discount_w2 = $dis['per08'] + $dis['per09'] +
                $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'];
                
        $overall_discount_w3 = $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18']
                + $dis['per19'] + $dis['per20'] + $dis['per21'];
                
        if ($mon_days == 31) {
            $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] + $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] + $dis['per30'] + $dis['per31'];
            }
            else
            {
                $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];
            }
                
            if($mon_days==31) {
                $overall_discount = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                    $dis['per05'] + $dis['per06'] + $dis['per07'] + $dis['per08'] + $dis['per09'] +
                    $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'] +
                    $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18'] + $dis['per19'] +
                    $dis['per20'] + $dis['per21'] + $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'] + $dis['per31'];
            }
            else
            {
                $overall_discount = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                    $dis['per05'] + $dis['per06'] + $dis['per07'] + $dis['per08'] + $dis['per09'] +
                    $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'] +
                    $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18'] + $dis['per19'] +
                    $dis['per20'] + $dis['per21'] + $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];
            }

        if($week==0) {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(1, 2, round($dis['per01']));
            $rep->TextCol(2, 3, round($dis['per02']));
            $rep->TextCol(3, 4, round($dis['per03']));
            $rep->TextCol(4, 5, round($dis['per04']));
            $rep->TextCol(5, 6, round($dis['per05']));
            $rep->TextCol(6, 7, round($dis['per06']));
            $rep->TextCol(7, 8, round($dis['per07']));

            // $rep->TextCol(8, 9, "(".round($overall_discount_w1).")");

            $rep->TextCol(9, 10, round($dis['per08']));
            $rep->TextCol(10, 11, round($dis['per09']));
            $rep->TextCol(11, 12, round($dis['per10']));
            $rep->TextCol(12, 13, round($dis['per11']));
            $rep->TextCol(13, 14, round($dis['per12']));
            $rep->TextCol(14, 15, round($dis['per13']));
            $rep->TextCol(15, 16, round($dis['per14']));

            // $rep->TextCol(16, 17, "(".round($overall_discount_w2).")");

            $rep->TextCol(17, 18, round($dis['per15']));
            $rep->TextCol(18, 19, round($dis['per16']));
            $rep->TextCol(19, 20, round($dis['per17']));
            $rep->TextCol(20, 21, round($dis['per18']));
            $rep->TextCol(21, 22, round($dis['per19']));
            $rep->TextCol(22, 23, round($dis['per20']));
            $rep->TextCol(23, 24, round($dis['per21']));

            // $rep->TextCol(24, 25, "(".round($overall_discount_w3).")");

            $rep->TextCol(25, 26, round($dis['per22']));
            $rep->TextCol(26, 27, round($dis['per23']));
            $rep->TextCol(27, 28, round($dis['per24']));
            $rep->TextCol(28, 29, round($dis['per25']));
            $rep->TextCol(29, 30, round($dis['per26']));
            $rep->TextCol(30, 31, round($dis['per27']));
            $rep->TextCol(31, 32, round($dis['per28']));
            $rep->TextCol(32, 33, round($dis['per29']));
            $rep->TextCol(33, 34, round($dis['per30']));

            if ($mon_days == 31) {
                $rep->TextCol(34, 35, round($dis['per31']));
                }

            // $rep->TextCol(35, 36, "(".round($overall_discount_w4).")");


            $rep->AmountCol(36, 37, $overall_discount, 0);
}
        //----------------month wise end------------//

        //-------------------for week----------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(1, 2, $dis['per01']);
            $rep->TextCol(2, 3, $dis['per02']);
            $rep->TextCol(3, 4, $dis['per03']);
            $rep->TextCol(4, 5, $dis['per04']);
            $rep->TextCol(5, 6, $dis['per05']);
            $rep->TextCol(6, 7, $dis['per06']);
            $rep->TextCol(7, 8, $dis['per07']);

            $overall_discount_w1 = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                $dis['per05'] + $dis['per06'] + $dis['per07'];

            $rep->AmountCol(33, 34, $overall_discount_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(8, 9, $dis['per08']);
            $rep->TextCol(9, 10, $dis['per09']);
            $rep->TextCol(10, 11, $dis['per10']);
            $rep->TextCol(11, 12, $dis['per11']);
            $rep->TextCol(12, 13, $dis['per12']);
            $rep->TextCol(13, 14, $dis['per13']);
            $rep->TextCol(14, 15, $dis['per14']);

            $overall_discount_w2 = $dis['per08'] + $dis['per09'] +
                $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'];

            $rep->AmountCol(33, 34, $overall_discount_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(15, 16, $dis['per15']);
            $rep->TextCol(16, 17, $dis['per16']);
            $rep->TextCol(17, 18, $dis['per17']);
            $rep->TextCol(18, 19, $dis['per18']);
            $rep->TextCol(19, 20, $dis['per19']);
            $rep->TextCol(20, 21, $dis['per20']);
            $rep->TextCol(21, 22, $dis['per21']);

            $overall_discount_w3 = $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18']
                + $dis['per19'] + $dis['per20'] + $dis['per21'];

            $rep->AmountCol(33, 34, $overall_discount_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(22, 23, $dis['per22']);
            $rep->TextCol(23, 24, $dis['per23']);
            $rep->TextCol(24, 25, $dis['per24']);
            $rep->TextCol(25, 26, $dis['per25']);
            $rep->TextCol(26, 27, $dis['per26']);
            $rep->TextCol(27, 28, $dis['per27']);
            $rep->TextCol(28, 29, $dis['per28']);
            $rep->TextCol(29, 30, $dis['per29']);
            $rep->TextCol(30, 31, $dis['per30']);

            if($mon_days==31) {
                $rep->TextCol(31, 32, $dis['per31']);
                $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'] + $dis['per31'];
            }
            else
                $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];

            $rep->AmountCol(33, 34, $overall_discount_w4, 0);
        }
        elseif($week == 5)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(6, 8, "(".round($overall_discount_w1).")");
            $rep->TextCol(13, 15, "(".round($overall_discount_w2).")");
            $rep->TextCol(20, 22, "(".round($overall_discount_w3).")");
            $rep->TextCol(27, 29, "(".round($overall_discount_w4).")");
            $rep->AmountCol(36, 37, $overall_discount, 0);
        }
//-------------------for week end----------------//
        //marina
        $rep->Line($rep->row - 8);
    }
    else{
        $rep->Font('bold');
        $rep->NewLine(2);
        //-----------moth wise-----------//
        
        $grand_total_w1 = $tot_per1 + $tot_per2 + $tot_per3 + $tot_per4 + $tot_per5 +
                $tot_per6 + $tot_per7;
                
        $grand_total_w2 = $tot_per8 + $tot_per9 + $tot_per10 + $tot_per11 + $tot_per12
                + $tot_per13 + $tot_per14;
                
        $grand_total_w3 = $tot_per15 + $tot_per16 + $tot_per17 + $tot_per18 + $tot_per19
                + $tot_per20 + $tot_per21;
                
        if ($mon_days == 31) {
        $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 +
                    $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30 + $tot_per31;
            }
            else
            {
        $grand_total_w4 = $tot_per22 + $tot_per23 + $tot_per24 + $tot_per25 + $tot_per26 +
                    $tot_per27 + $tot_per28 + $tot_per29 + $tot_per30;
            }
               
        if($week==0) {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(1, 2, round($tot_per1));
            $rep->TextCol(2, 3, round($tot_per2));
            $rep->TextCol(3, 4, round($tot_per3));
            $rep->TextCol(4, 5, round($tot_per4));
            $rep->TextCol(5, 6, round($tot_per5));
            $rep->TextCol(6, 7, round($tot_per6));
            $rep->TextCol(7, 8, round($tot_per7));

            // $rep->TextCol(8, 9, "(".round($grand_total_w1).")");

            $rep->TextCol(9, 10, round($tot_per8));
            $rep->TextCol(10, 11,round($tot_per9));
            $rep->TextCol(11, 12, round($tot_per10));
            $rep->TextCol(12, 13, round($tot_per11));
            $rep->TextCol(13, 14, round($tot_per12));
            $rep->TextCol(14, 15, round($tot_per13));
            $rep->TextCol(15, 16, round($tot_per14));

            // $rep->TextCol(16, 17, "(".round($grand_total_w2).")");

            $rep->TextCol(17, 18, round($tot_per15));
            $rep->TextCol(18, 19, round($tot_per16));
            $rep->TextCol(19, 20, round($tot_per17));
            $rep->TextCol(20, 21, round($tot_per18));
            $rep->TextCol(21, 22, round($tot_per19));
            $rep->TextCol(22, 23, round($tot_per20));
            $rep->TextCol(23, 24, round($tot_per21));
            
            // $rep->TextCol(24, 25, "(".round($grand_total_w3).")");

            $rep->TextCol(25, 26, round($tot_per22));
            $rep->TextCol(26, 27, round($tot_per23));
            $rep->TextCol(27, 28, round($tot_per24));
            $rep->TextCol(28, 29, round($tot_per25));
            $rep->TextCol(29, 30, round($tot_per26));
            $rep->TextCol(30, 31, round($tot_per27));
            $rep->TextCol(31, 32, round($tot_per28));
            $rep->TextCol(32, 33, round($tot_per29));
            $rep->TextCol(33, 34, round($tot_per30));

            if ($mon_days == 31) {
                $rep->TextCol(34, 35, round($tot_per31));
            }

            // $rep->TextCol(35, 36, "(".round($grand_total_w4).")");

            $rep->AmountCol(36, 37, $total_qty, 0);
            $rep->AmountCol(37, 38, $total_qty_cost, 0);
            $rep->AmountCol(38, 39, $total_line_disc, 0);
            $rep->AmountCol(39, 40, $total_cost, 0);
            $rep->AmountCol(40, 41, $total_net_profit, 0);

        }
        //-----------moth wise end-----------//

        //--------------------for weeks------------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(1, 2, round($tot_per1));
            $rep->TextCol(2, 3, round($tot_per2));
            $rep->TextCol(3, 4, round($tot_per3));
            $rep->TextCol(4, 5, round($tot_per4));
            $rep->TextCol(5, 6, round($tot_per5));
            $rep->TextCol(6, 7, round($tot_per6));
            $rep->TextCol(7, 8, round($tot_per7));

            $rep->AmountCol(32, 33, $total_qty, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w1, 0);

        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(8, 9, round($tot_per8));
            $rep->TextCol(9, 10, round($tot_per9));
            $rep->TextCol(10, 11, round($tot_per10));
            $rep->TextCol(11, 12, round($tot_per11));
            $rep->TextCol(12, 13, round($tot_per12));
            $rep->TextCol(13, 14, round($tot_per13));
            $rep->TextCol(14, 15, round($tot_per14));

            $rep->AmountCol(32, 33, $total_qty, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(15, 16, round($tot_per15));
            $rep->TextCol(16, 17, round($tot_per16));
            $rep->TextCol(17, 18, round($tot_per17));
            $rep->TextCol(18, 19, round($tot_per18));
            $rep->TextCol(19, 20, round($tot_per19));
            $rep->TextCol(20, 21, round($tot_per20));
            $rep->TextCol(21, 22, round($tot_per21));

            $rep->AmountCol(32, 33, $total_qty, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(22, 23, round($tot_per22));
            $rep->TextCol(23, 24, round($tot_per23));
            $rep->TextCol(24, 25, round($tot_per24));
            $rep->TextCol(25, 26, round($tot_per25));
            $rep->TextCol(26, 27, round($tot_per26));
            $rep->TextCol(27, 28, round($tot_per27));
            $rep->TextCol(28, 29, round($tot_per28));
            $rep->TextCol(29, 30, round($tot_per29));
            $rep->TextCol(30, 31, round($tot_per30));

            if($mon_days==31) {
                $rep->TextCol(31, 32, round($tot_per31));
            }

            $rep->AmountCol(32, 33, $total_qty, 0);
            $rep->AmountCol(33, 34, $total_qty_cost, 0);
            $rep->AmountCol(34, 35, $total_line_disc, 0);
            $rep->AmountCol(35, 36, $total_cost, 0);
            $rep->AmountCol(36, 37, $total_net_profit_w4, 0);
        }
        elseif($week == 5)
        {
            $rep->TextCol(0, 1, _('Grand Total'));
            $rep->TextCol(6, 8, "(".round($grand_total_w1).")");
            $rep->TextCol(13, 15, "(".round($grand_total_w2).")");
            $rep->TextCol(20, 22, "(".round($grand_total_w3).")");
            $rep->TextCol(27, 29, "(".round($grand_total_w4).")");
                    
            $rep->AmountCol(36, 37, $total_qty, 0);
            $rep->AmountCol(37, 38, $total_qty_cost, 0);
            $rep->AmountCol(38, 39, $total_line_disc, 0);
            $rep->AmountCol(39, 40, $total_cost, 0);
            $rep->AmountCol(40, 41, $total_net_profit, 0);
        }
        //-----------------weeks end---------------------//

        //-------------for discount row------------//
        $rep->Line($rep->row - 8);
        $rep->NewLine(2);

        //----------------month wise------------//
        
        $overall_discount_w1 = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                $dis['per05'] + $dis['per06'] + $dis['per07'];
        
        $overall_discount_w2 = $dis['per08'] + $dis['per09'] +
                $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'];
          
         $overall_discount_w3 = $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18']
                + $dis['per19'] + $dis['per20'] + $dis['per21'];  
                
        if ($mon_days == 31) {
        $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'] + $dis['per31'];
            }
            else
            {
        $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];
            }
            
             if($mon_days==31) {
                $overall_discount = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                    $dis['per05'] + $dis['per06'] + $dis['per07'] + $dis['per08'] + $dis['per09'] +
                    $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'] +
                    $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18'] + $dis['per19'] +
                    $dis['per20'] + $dis['per21'] + $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'] + $dis['per31'];
            }
            else
            {
                $overall_discount = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                    $dis['per05'] + $dis['per06'] + $dis['per07'] + $dis['per08'] + $dis['per09'] +
                    $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'] +
                    $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18'] + $dis['per19'] +
                    $dis['per20'] + $dis['per21'] + $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];
            }
        
        if($week==0) {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(1, 2, round($dis['per01']));
            $rep->TextCol(2, 3, round($dis['per02']));
            $rep->TextCol(3, 4, round($dis['per03']));
            $rep->TextCol(4, 5, round($dis['per04']));
            $rep->TextCol(5, 6, round($dis['per05']));
            $rep->TextCol(6, 7, round($dis['per06']));
            $rep->TextCol(7, 8, round($dis['per07']));

            // $rep->TextCol(8, 9, "(".round($overall_discount_w1).")");

            $rep->TextCol(9, 10, round($dis['per08']));
            $rep->TextCol(10, 11, round($dis['per09']));
            $rep->TextCol(11, 12, round($dis['per10']));
            $rep->TextCol(12, 13, round($dis['per11']));
            $rep->TextCol(13, 14, round($dis['per12']));
            $rep->TextCol(14, 15, round($dis['per13']));
            $rep->TextCol(15, 16, round($dis['per14']));

            // $rep->TextCol(16, 17, "(".round($overall_discount_w2).")");

            $rep->TextCol(17, 18, round($dis['per15']));
            $rep->TextCol(18, 19, round($dis['per16']));
            $rep->TextCol(19, 20, round($dis['per17']));
            $rep->TextCol(20, 21, round($dis['per18']));
            $rep->TextCol(21, 22, round($dis['per19']));
            $rep->TextCol(22, 23, round($dis['per20']));
            $rep->TextCol(23, 24, round($dis['per21']));

            // $rep->TextCol(24, 25, "(".round($overall_discount_w3).")");

            $rep->TextCol(25, 26, round($dis['per22']));
            $rep->TextCol(26, 27, round($dis['per23']));
            $rep->TextCol(27, 28, round($dis['per24']));
            $rep->TextCol(28, 29, round($dis['per25']));
            $rep->TextCol(29, 30, round($dis['per26']));
            $rep->TextCol(30, 31, round($dis['per27']));
            $rep->TextCol(31, 32, round($dis['per28']));
            $rep->TextCol(32, 33, round($dis['per29']));
            $rep->TextCol(33, 34, round($dis['per30']));

            if ($mon_days == 31) {
                $rep->TextCol(34, 35, round($dis['per31']));
            }

            // $rep->TextCol(35, 36, "(".round($overall_discount_w4).")");

            $rep->AmountCol(37, 38, $overall_discount, 0);
}
        //----------------month wise end------------//

        //-------------------for week----------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(1, 2, $dis['per01']);
            $rep->TextCol(2, 3, $dis['per02']);
            $rep->TextCol(3, 4, $dis['per03']);
            $rep->TextCol(4, 5, $dis['per04']);
            $rep->TextCol(5, 6, $dis['per05']);
            $rep->TextCol(6, 7, $dis['per06']);
            $rep->TextCol(7, 8, $dis['per07']);

            $overall_discount_w1 = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                $dis['per05'] + $dis['per06'] + $dis['per07'];

            $rep->AmountCol(33, 34, $overall_discount_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(8, 9, $dis['per08']);
            $rep->TextCol(9, 10, $dis['per09']);
            $rep->TextCol(10, 11, $dis['per10']);
            $rep->TextCol(11, 12, $dis['per11']);
            $rep->TextCol(12, 13, $dis['per12']);
            $rep->TextCol(13, 14, $dis['per13']);
            $rep->TextCol(14, 15, $dis['per14']);

            $overall_discount_w2 = $dis['per08'] + $dis['per09'] +
                $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'];

            $rep->AmountCol(33, 34, $overall_discount_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(15, 16, $dis['per15']);
            $rep->TextCol(16, 17, $dis['per16']);
            $rep->TextCol(17, 18, $dis['per17']);
            $rep->TextCol(18, 19, $dis['per18']);
            $rep->TextCol(19, 20, $dis['per19']);
            $rep->TextCol(20, 21, $dis['per20']);
            $rep->TextCol(21, 22, $dis['per21']);

            $overall_discount_w3 = $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18']
                + $dis['per19'] + $dis['per20'] + $dis['per21'];

            $rep->AmountCol(33, 34, $overall_discount_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('Overall Discount'));
            $rep->TextCol(22, 23, $dis['per22']);
            $rep->TextCol(23, 24, $dis['per23']);
            $rep->TextCol(24, 25, $dis['per24']);
            $rep->TextCol(25, 26, $dis['per25']);
            $rep->TextCol(26, 27, $dis['per26']);
            $rep->TextCol(27, 28, $dis['per27']);
            $rep->TextCol(28, 29, $dis['per28']);
            $rep->TextCol(29, 30, $dis['per29']);
            $rep->TextCol(30, 31, $dis['per30']);

            if($mon_days==31) {
                $rep->TextCol(31, 32, $dis['per31']);
                $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'] + $dis['per31'];
            }
            else
                $overall_discount_w4 = $dis['per22'] + $dis['per23'] + $dis['per24'] +
                    $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                    $dis['per30'];

            $rep->AmountCol(33, 34, $overall_discount_w4, 0);
        }
        elseif($week == 5)
        {
                $rep->TextCol(0, 1, _('Overall Discount'));
                $rep->TextCol(6, 8, "(".round($overall_discount_w1).")");
                $rep->TextCol(13, 15, "(".round($overall_discount_w2).")");
                $rep->TextCol(20, 22, "(".round($overall_discount_w3).")");
                $rep->TextCol(27, 29, "(".round($overall_discount_w4).")");
                $rep->AmountCol(37, 38, $overall_discount, 0);
        }
//-------------------for week end----------------//

//-------------for net amount row------------//

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);

        //----------------month wise-----------------//
        
        if($week==0) {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(1, 2, round($tot_per1 - $dis['per01']));
            $rep->TextCol(2, 3, round($tot_per2 - $dis['per02']));
            $rep->TextCol(3, 4, round($tot_per3 - $dis['per03']));
            $rep->TextCol(4, 5, round($tot_per4 - $dis['per04']));
            $rep->TextCol(5, 6, round($tot_per5 - $dis['per05']));
            $rep->TextCol(6, 7, round($tot_per6 - $dis['per06']));
            $rep->TextCol(7, 8, round($tot_per7 - $dis['per07']));

            // $rep->TextCol(8, 9, "(".(round($grand_total_w1 - $overall_discount_w1)).")", 0);

            $rep->TextCol(9, 10, round($tot_per8 - $dis['per08']));
            $rep->TextCol(10, 11, round($tot_per9 - $dis['per09']));
            $rep->TextCol(11, 12, round($tot_per10 - $dis['per10']));
            $rep->TextCol(12, 13, round($tot_per11 - $dis['per11']));
            $rep->TextCol(13, 14, round($tot_per12 - $dis['per12']));
            $rep->TextCol(14, 15, round($tot_per13 - $dis['per13']));
            $rep->TextCol(15, 16, round($tot_per14 - $dis['per14']));

            // $rep->TextCol(16, 17, "(".(round($grand_total_w2 - $overall_discount_w2)).")", 0);

            $rep->TextCol(17, 18, round($tot_per15 - $dis['per15']));
            $rep->TextCol(18, 19, round($tot_per16 - $dis['per16']));
            $rep->TextCol(19, 20, round($tot_per17 - $dis['per17']));
            $rep->TextCol(20, 21, round($tot_per18 - $dis['per18']));
            $rep->TextCol(21, 22, round($tot_per19 - $dis['per19']));
            $rep->TextCol(22, 23, round($tot_per20 - $dis['per20']));
            $rep->TextCol(23, 24, round($tot_per21 - $dis['per21']));

            // $rep->TextCol(24, 25, "(".(round($grand_total_w3 - $overall_discount_w3)).")", 0);

            $rep->TextCol(25, 26, round($tot_per22 - $dis['per22']));
            $rep->TextCol(26, 27, round($tot_per23 - $dis['per23']));
            $rep->TextCol(27, 28, round($tot_per24 - $dis['per24']));
            $rep->TextCol(28, 29, round($tot_per25 - $dis['per25']));
            $rep->TextCol(29, 30, round($tot_per26 - $dis['per26']));
            $rep->TextCol(30, 31, round($tot_per27 - $dis['per27']));
            $rep->TextCol(31, 32, round($tot_per28 - $dis['per28']));
            $rep->TextCol(32, 33, round($tot_per29 - $dis['per29']));
            $rep->TextCol(33, 34, round($tot_per30 - $dis['per30']));

            if ($mon_days == 31) {
                $rep->TextCol(34, 35, round($tot_per31 - $dis['per31']));
            }

            // $rep->TextCol(35, 36, "(".(round($grand_total_w4 - $overall_discount_w4)).")", 0);


            $rep->AmountCol(37, 38, $grand_total - $overall_discount, 0);
        }
//----------------moth wise end-----------------//

        //----------------for week--------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(1, 2, round($tot_per1 - $dis['per01']));
            $rep->TextCol(2, 3, round($tot_per2 - $dis['per02']));
            $rep->TextCol(3, 4, round($tot_per3 - $dis['per03']));
            $rep->TextCol(4, 5, round($tot_per4 - $dis['per04']));
            $rep->TextCol(5, 6, round($tot_per5 - $dis['per05']));
            $rep->TextCol(6, 7, round($tot_per6 - $dis['per06']));
            $rep->TextCol(7, 8, round($tot_per7 - $dis['per07']));
            $rep->AmountCol(33, 34, $grand_total_w1 - $overall_discount_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(8, 9, round($tot_per8 - $dis['per08']));
            $rep->TextCol(9, 10, round($tot_per9 - $dis['per09']));
            $rep->TextCol(10, 11, round($tot_per10 - $dis['per10']));
            $rep->TextCol(11, 12, round($tot_per11 - $dis['per11']));
            $rep->TextCol(12, 13, round($tot_per12 - $dis['per12']));
            $rep->TextCol(13, 14, round($tot_per13 - $dis['per13']));
            $rep->TextCol(14, 15, round($tot_per14 - $dis['per14']));
            $rep->AmountCol(33, 34, $grand_total_w2 - $overall_discount_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(15, 16, round($tot_per15 - $dis['per15']));
            $rep->TextCol(16, 17, round($tot_per16 - $dis['per16']));
            $rep->TextCol(17, 18, round($tot_per17 - $dis['per17']));
            $rep->TextCol(18, 19, round($tot_per18 - $dis['per18']));
            $rep->TextCol(19, 20, round($tot_per19 - $dis['per19']));
            $rep->TextCol(20, 21, round($tot_per20 - $dis['per20']));
            $rep->TextCol(21, 22, round($tot_per21 - $dis['per21']));
            $rep->AmountCol(33, 34, $grand_total_w3 - $overall_discount_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(22, 23, round($tot_per22 - $dis['per22']));
            $rep->TextCol(23, 24, round($tot_per23 - $dis['per23']));
            $rep->TextCol(24, 25, round($tot_per24 - $dis['per24']));
            $rep->TextCol(25, 26, round($tot_per25 - $dis['per25']));
            $rep->TextCol(26, 27, round($tot_per26 - $dis['per26']));
            $rep->TextCol(27, 28, round($tot_per27 - $dis['per27']));
            $rep->TextCol(28, 29, round($tot_per28 - $dis['per28']));
            $rep->TextCol(29, 30, round($tot_per29 - $dis['per29']));
            $rep->TextCol(30, 31, round($tot_per30 - $dis['per30']));

            if ($mon_days == 31) {
                $rep->TextCol(31, 32, round($tot_per31 - $dis['per31']));
            }
            $rep->AmountCol(33, 34, $grand_total_w4 - $overall_discount_w4, 0);
        }
        elseif($week == 5)
        {
            $rep->TextCol(0, 1, _('Net Total'));
            $rep->TextCol(6, 8, "(".(round($grand_total_w1 - $overall_discount_w1)).")", 0);
            $rep->TextCol(13, 15, "(".(round($grand_total_w2 - $overall_discount_w2)).")", 0);
            $rep->TextCol(20, 22, "(".(round($grand_total_w3 - $overall_discount_w3)).")", 0);
            $rep->TextCol(27, 29, "(".(round($grand_total_w4 - $overall_discount_w4)).")", 0);
            $rep->AmountCol(37, 38, $grand_total - $overall_discount, 0);
        }
        //------------for week end-----------//


        $rep->Line($rep->row - 8);

        //-------------for no of invoices row------------//

        $rep->NewLine(2);
        $rep->Font('bold');
        //------------month wise---------//
        
        $invoices_total_w1 = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                $invoices['per07'];
                
        $invoices_total_w2 = $invoices['per08'] + $invoices['per09'] +
                $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                $invoices['per13'] + $invoices['per14'];
                
        $invoices_total_w3 = $invoices['per15'] +
                $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                $invoices['per19'] + $invoices['per20'] + $invoices['per21'];
                
        if ($mon_days == 31) {
        $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'] +
                    $invoices['per31'];
            }
            else
            {
                $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'];
            }
            
             if($mon_days==31) {
                $invoices_total = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                    $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                    $invoices['per07'] + $invoices['per08'] + $invoices['per09'] +
                    $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                    $invoices['per13'] + $invoices['per14'] + $invoices['per15'] +
                    $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                    $invoices['per19'] + $invoices['per20'] + $invoices['per21'] +
                    $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'] +
                    $invoices['per31'];
            }
            else
            {
                $invoices_total = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                    $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                    $invoices['per07'] + $invoices['per08'] + $invoices['per09'] +
                    $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                    $invoices['per13'] + $invoices['per14'] + $invoices['per15'] +
                    $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                    $invoices['per19'] + $invoices['per20'] + $invoices['per21'] +
                    $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'];
            }
         
        if($week==0) {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(1, 2, $invoices['per01'], 0);
            $rep->AmountCol(2, 3, $invoices['per02'], 0);
            $rep->AmountCol(3, 4, $invoices['per03'], 0);
            $rep->AmountCol(4, 5, $invoices['per04'], 0);
            $rep->AmountCol(5, 6, $invoices['per05'], 0);
            $rep->AmountCol(6, 7, $invoices['per06'], 0);
            $rep->AmountCol(7, 8, $invoices['per07'], 0);

            // $rep->TextCol(8, 9, "(".$invoices_total_w1.")");

            $rep->AmountCol(9, 10, $invoices['per08'], 0);
            $rep->AmountCol(10, 11, $invoices['per09'], 0);
            $rep->AmountCol(11, 12, $invoices['per10'], 0);
            $rep->AmountCol(12, 13, $invoices['per11'], 0);
            $rep->AmountCol(13, 14, $invoices['per12'], 0);
            $rep->AmountCol(14, 15, $invoices['per13'], 0);
            $rep->AmountCol(15, 16, $invoices['per14'], 0);

            // $rep->TextCol(16, 17, "(".$invoices_total_w2.")");

            $rep->AmountCol(17, 18, $invoices['per15'], 0);
            $rep->AmountCol(18, 19, $invoices['per16'], 0);
            $rep->AmountCol(19, 20, $invoices['per17'], 0);
            $rep->AmountCol(20, 21, $invoices['per18'], 0);
            $rep->AmountCol(21, 22, $invoices['per19'], 0);
            $rep->AmountCol(22, 23, $invoices['per20'], 0);
            $rep->AmountCol(23, 24, $invoices['per21'], 0);

            // $rep->TextCol(24, 25, "(".$invoices_total_w3.")");

            $rep->AmountCol(25, 26, $invoices['per22'], 0);
            $rep->AmountCol(26, 27, $invoices['per23'], 0);
            $rep->AmountCol(27, 28, $invoices['per24'], 0);
            $rep->AmountCol(28, 29, $invoices['per25'], 0);
            $rep->AmountCol(29, 30, $invoices['per26'], 0);
            $rep->AmountCol(30, 31, $invoices['per27'], 0);
            $rep->AmountCol(31, 32, $invoices['per28'], 0);
            $rep->AmountCol(32, 33, $invoices['per29'], 0);
            $rep->AmountCol(33, 34, $invoices['per30'], 0);

            if ($mon_days == 31) {
                $rep->AmountCol(34, 35, $invoices['per31'], 0);
            }

            // $rep->TextCol(35, 36, "(".$invoices_total_w4.")");

            $rep->AmountCol(37, 38, $invoices_total, 0);

        }
        //------------month wise end---------//

//---------------------for weeks------------------------//
        if($week==1)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(1, 2, $invoices['per01'], 0);
            $rep->AmountCol(2, 3, $invoices['per02'], 0);
            $rep->AmountCol(3, 4, $invoices['per03'], 0);
            $rep->AmountCol(4, 5, $invoices['per04'], 0);
            $rep->AmountCol(5, 6, $invoices['per05'], 0);
            $rep->AmountCol(6, 7, $invoices['per06'], 0);
            $rep->AmountCol(7, 8, $invoices['per07'], 0);

            $invoices_total_w1 = $invoices['per01'] + $invoices['per02'] + $invoices['per03'] +
                $invoices['per04'] + $invoices['per05'] + $invoices['per06'] +
                $invoices['per07'];

            $rep->AmountCol(33, 34, $invoices_total_w1, 0);
        }
        elseif($week==2)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(8, 9, $invoices['per08'], 0);
            $rep->AmountCol(9, 10, $invoices['per09'], 0);
            $rep->AmountCol(10, 11, $invoices['per10'], 0);
            $rep->AmountCol(11, 12, $invoices['per11'], 0);
            $rep->AmountCol(12, 13, $invoices['per12'], 0);
            $rep->AmountCol(13, 14, $invoices['per13'], 0);
            $rep->AmountCol(14, 15, $invoices['per14'], 0);

            $invoices_total_w2 = $invoices['per08'] + $invoices['per09'] +
                $invoices['per10'] + $invoices['per11'] + $invoices['per12'] +
                $invoices['per13'] + $invoices['per14'];

            $rep->AmountCol(33, 34, $invoices_total_w2, 0);
        }
        elseif($week==3)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(15, 16, $invoices['per15'], 0);
            $rep->AmountCol(16, 17, $invoices['per16'], 0);
            $rep->AmountCol(17, 18, $invoices['per17'], 0);
            $rep->AmountCol(18, 19, $invoices['per18'], 0);
            $rep->AmountCol(19, 20, $invoices['per19'], 0);
            $rep->AmountCol(20, 21, $invoices['per20'], 0);
            $rep->AmountCol(21, 22, $invoices['per21'], 0);

            $invoices_total_w3 = $invoices['per15'] +
                $invoices['per16'] + $invoices['per17'] + $invoices['per18'] +
                $invoices['per19'] + $invoices['per20'] + $invoices['per21'];

            $rep->AmountCol(33, 34, $invoices_total_w3, 0);
        }
        elseif($week==4)
        {
            $rep->TextCol(0, 1, _('No Of Invoices'));
            $rep->AmountCol(22, 23, $invoices['per22'], 0);
            $rep->AmountCol(23, 24, $invoices['per23'], 0);
            $rep->AmountCol(24, 25, $invoices['per24'], 0);
            $rep->AmountCol(25, 26, $invoices['per25'], 0);
            $rep->AmountCol(26, 27, $invoices['per26'], 0);
            $rep->AmountCol(27, 28, $invoices['per27'], 0);
            $rep->AmountCol(28, 29, $invoices['per28'], 0);
            $rep->AmountCol(29, 30, $invoices['per29'], 0);
            $rep->AmountCol(30, 31, $invoices['per30'], 0);

            if($mon_days==31) {
                $rep->AmountCol(31, 32, $invoices['per31'], 0);
                $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'] +
                    $invoices['per31'];
            }
            else
                $invoices_total_w4 = $invoices['per22'] + $invoices['per23'] + $invoices['per24'] +
                    $invoices['per25'] + $invoices['per26'] + $invoices['per27'] +
                    $invoices['per28'] + $invoices['per29'] + $invoices['per30'];

            $rep->AmountCol(33, 34, $invoices_total_w4, 0);
        }
        elseif($week == 5)
        {
                $rep->TextCol(0, 1, _('No Of Invoices'));
                $rep->TextCol(6, 8, "(".$invoices_total_w1.")");
                $rep->TextCol(13, 15, "(".$invoices_total_w2.")");
                $rep->TextCol(20, 22, "(".$invoices_total_w3.")");
                $rep->TextCol(27, 29, "(".$invoices_total_w4.")");
                $rep->AmountCol(37, 38, $invoices_total, 0);
        }
        //----------------for weeks end-------------//

        $rep->Line($rep->row - 8);
    }
    $rep->Font('');
//-------------------------for revenue--------------------------------//
    if($p_q==1)
    {
        $rep->NewLine(1.5);
        $rep->Font('bold');
        //-----------------for month wise----------//
        if($week==0) {
            $rep->TextCol(29, 34, "Net Revenue");
            $rep->AmountCol(36, 39, $grand_total - $overall_discount, 0);
        }
        //-----------------for month wise end----------//

        //-------------for weeks end-------------//
        if($week==1) {
            $rep->TextCol(20, 28, "Net Revenue");
            $rep->AmountCol(32, 35, $grand_total_w1 - $overall_discount_w1, 0);
        }
        if($week==2) {
            $rep->TextCol(20, 28, "Net Revenue");
            $rep->AmountCol(32, 35, $grand_total_w2 - $overall_discount_w2, 0);
        }
        if($week==3) {
            $rep->TextCol(20, 28, "Net Revenue");
            $rep->AmountCol(32, 35, $grand_total_w3 - $overall_discount_w3, 0);
        }
        if($week==4) {
            $rep->TextCol(20, 28, "Net Revenue");
            $rep->AmountCol(32, 35, $grand_total_w4 - $overall_discount_w4, 0);
        }
        //-------------for weeks end-------------//
        $rep->Font('');
        $rep->Line($rep->row - 8);

//-------------------------for expenses----------------------------------//

        $per_balance=get_gl_trans_from_to_new($from,$from);

        $rep->NewLine(3);
        $expenses_total = 0;
        while ($expenses_db=db_fetch($per_balance)) {

            if($expenses_db['t_amount']==0 )
                continue;
            $rep->Font('bold');
            $rep->TextCol(26, 34, $expenses_db['name']);
            $rep->AmountCol(36, 40, $expenses_db['t_amount'], 0);
            $rep->Font('');
            $rep->Line($rep->row - 1);
            $rep->NewLine();
            $exp_detail=get_gl_trans_from_to_new11($from,$from,$expenses_db['id']);
            while ($exp_detail_db=db_fetch($exp_detail)) {
                if( $exp_detail_db['amt_t']==0 )
                    continue;
                $rep->TextCol(26, 34, $exp_detail_db['account_name']);
                $rep->AmountCol(36, 40, $exp_detail_db['amt_t'], 0);
                $rep->NewLine();
            }

            $expenses_total += $expenses_db['t_amount'];
        }

        $rep->Line($rep->row - 1);
        $rep->NewLine();
        $rep->Font('bold');
        $rep->TextCol(26, 34, "Total Expenses");
        $rep->AmountCol(36, 40, $expenses_total, 0);

        $Net_Total = $grand_total - $overall_discount;
        $End_Balance = $Net_Total - $expenses_total;

        $rep->Line($rep->row - 6);
        $rep->NewLine(+1.5);
        $rep->TextCol(26, 34, "Grand Amount");
        $rep->AmountCol(36, 40, $End_Balance, 0);
        $rep->Line($rep->row - 8);
        $rep->Font('');

    }
//-------------------------for expenses------------------------------//

//-------------------------1st graph--------------------------
    $rep->Font('');
    for ($i = 0; $i < count($total); $i++)
    {

        if ($i < count($total))
        {
            $pg->y[$i] = abs($total[$i]);

        }
    }
    $rep->Line($rep->row  - 8);

    if ($graph == 1) {
    {

        global $SysPrefs, $graph_skin;
        if($mon_days==31)
            $pg->x = array($show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
                $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30,
                $show31);
        else
            $pg->x = array($show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
                $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
                $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30);



        $pg->title     = $rep->title;
        $pg->axis_x    = _("Days");
        $pg->axis_y    = _("Amount");
        $pg->graphic_1 = $from;
        $pg->type      = 4;
        $pg->skin      = $SysPrefs->graph_skin;
        $pg->built_in  = false;
        $pg->latin_notation = ($SysPrefs->decseps[user_dec_sep()] != ".");

        $filename = company_path(). "/pdf_files/". uniqid("").".png";
        $pg->display($filename, true);
        $w = $pg->width / 1.6;
        $h = $pg->height / 2;
        $x = ($rep->pageWidth - $w) / 15;

        $rep->NewLine(2);

        if ($rep->row - $h < $rep->bottomMargin)

            $rep->NewPage();

        $rep->AddImage($filename, $x, $rep->row - $h, $w, $h);
    }
    $rep->NewLine(7);
//-------------------------2nd graph--------------------------
    for ($i = 0; $i < count($line_total); $i++)
    {

        if ($i < count($line_total))
        {

            $pg->y[$i] = $line_total[$i];

        }
    }

    // if ($graphics)
    {
        global $SysPrefs, $graph_skin;
        for ($i = 0; $i < count($multi_description); $i++)
        {
            if ($i < count($multi_description))
            {
                $pg->x[$i] = ($multi_description[$i]);
            }
        }

        $pg->title     = $rep->title;
        $pg->axis_x    = _("Items");
        $pg->axis_y    = _("Amount");
        $pg->graphic_1 = $from;
        $pg->type      = 5;
        $pg->skin      = $SysPrefs->graph_skin;
        $pg->built_in  = false;
        $pg->latin_notation = ($SysPrefs->decseps[user_dec_sep()] != ".");

        $filename = company_path(). "/pdf_files/". uniqid("").".png";
        $pg->display($filename, true);
        $w = $pg->width / 1.6;
        $h = $pg->height / 2;
        $x = ($rep->pageWidth - $w) / 15;

        $rep->NewLine(2);

        if ($rep->row - $h < $rep->bottomMargin)

            $rep->NewPage();

        $rep->AddImage($filename, $x, $rep->row - $h, $w, $h);
    }
    }
    $rep->NewLine();

    $rep->End();
}

?>
