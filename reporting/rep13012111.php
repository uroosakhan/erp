<?php
$page_security = 'SA_CUSTPAYMREP';

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

print_customer_balances();


function print_customer_balances()
{
    global $path_to_root;

    $from = $_POST['PARAM_0'];
    $p_q = $_POST['PARAM_1'];
    $graphics = $_POST['PARAM_2'];
    $destination = $_POST['PARAM_3'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation = 'L' );

    if ($graphics)
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
        0, 50 , 66, 82, 98, 114, 130, 146, 162, 178, 194, 210, 226, 242, 258, 274, 290, 306, 322, 338, 354, 370, 386, 402, 418, 434, 450, 466, 482, 498, 514, 526, 539, 570, 590
    );
    if($mon_days==31)
        $headers = array(   _('Activity'),
            $show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
            $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
            $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30,
            $show31, "Total");
    else
        $headers = array(   _('Activity'),
            $show1, $show2, $show3, $show4, $show5, $show6, $show7, $show8, $show9, $show10,
            $show11, $show12, $show13, $show14, $show15, $show16, $show17, $show18, $show19, $show20,
            $show21, $show22, $show23, $show24, $show25, $show26, $show27, $show28, $show29, $show30, "  ",
            "Total");

    $aligns = array( 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left',
        'left', 'left', 'left', 'left');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 		'' => ''),
        2 => array('text' => _('Month'), 'from' => $month, 		'' => ''),
    );

    $rep = new FrontReport(_('Monthly Wise Inventory Sales Report'), "AfterSalesDailyActivityReport", user_pagesize(), 7, $orientation);
    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns/*, $cols, $headers2*/);
    $rep->NewPage();
    $sql = "SELECT  `stock_id`,description 
FROM  `0_stock_master` WHERE stock_id!=''
 ";
//    if ($fromcust != ALL_TEXT)
//        $sql .= " AND debtor_no=".db_escape($fromcust);
    $sql .= " GROUP BY stock_id";
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

    $rep->Font('bold');
    $rep->TextCol(1, 2, $day1);
    $rep->TextCol(2, 3, $day2);
    $rep->TextCol(3, 4, $day3);
    $rep->TextCol(4, 5, $day4);
    $rep->TextCol(5, 6, $day5);
    $rep->TextCol(6, 7, $day6);
    $rep->TextCol(7, 8, $day7);
    $rep->TextCol(8, 9, $day8);
    $rep->TextCol(9, 10,  $day9);
    $rep->TextCol(10, 11, $day10);
    $rep->TextCol(11, 12, $day11);
    $rep->TextCol(12, 13, $day12);
    $rep->TextCol(13, 14, $day13);
    $rep->TextCol(14, 15, $day14);
    $rep->TextCol(15, 16, $day15);
    $rep->TextCol(16, 17, $day16);
    $rep->TextCol(17, 18, $day17);
    $rep->TextCol(18, 19, $day18);
    $rep->TextCol(19, 20, $day19);
    $rep->TextCol(20, 21, $day20);
    $rep->TextCol(21, 22, $day21);
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

    $rep->Font('');
    $rep->Line($rep->row - 2);

    //----------------------//

    $tot_per1=$tot_per2=$tot_per3=0;
    while ($myrow=db_fetch($result)) {

        if($p_q==0)
            $res=getTransactions($from,$myrow['stock_id']);
        else
            $res=getTransactions2($from,$myrow['stock_id']);

        while ($Data13=db_fetch($res)) {
            // $Total13 = $Data13['per01'] + $Data13['per02'] + $Data13['per03'] + $Data13['per04'] +
            //     $Data13['per05'] + $Data13['per06'] + $Data13['per07'] + $Data13['per08'] + $Data13['per09'] +
            //     $Data13['per10'] + $Data13['per11'] + $Data13['per12'] + $Data13['per13'] + $Data13['per14'] +
            //     $Data13['per15'] + $Data13['per16'] + $Data13['per17'] + $Data13['per18'] + $Data13['per19'] +
            //     $Data13['per20'] + $Data13['per21'] + $Data13['per22'] + $Data13['per23'] + $Data13['per24'] +
            //     $Data13['per25'] + $Data13['per26'] + $Data13['per27'] + $Data13['per28'] + $Data13['per29'] +
            //     $Data13['per30'] + $Data13['per31'];
            // if($Data13==0)continue;

            $rep->NewLine();
            $rep->TextCol(0, 1, $myrow['description']);
            $rep->AmountCol(1, 2, $Data13['per01']);
            $rep->AmountCol(2, 3, $Data13['per02']);
            $rep->AmountCol(3, 4, $Data13['per03']);
            $rep->AmountCol(4, 5, $Data13['per04']);
            $rep->AmountCol(5, 6, $Data13['per05']);
            $rep->AmountCol(6, 7, $Data13['per06']);
            $rep->AmountCol(7, 8, $Data13['per07']);
            $rep->AmountCol(8, 9, $Data13['per08']);
            $rep->AmountCol(9, 10, $Data13['per09']);
            $rep->AmountCol(10, 11, $Data13['per10']);
            $rep->AmountCol(11, 12, $Data13['per11']);
            $rep->AmountCol(12, 13, $Data13['per12']);
            $rep->AmountCol(13, 14, $Data13['per13']);
            $rep->AmountCol(14, 15, $Data13['per14']);
            $rep->AmountCol(15, 16, $Data13['per15']);
            $rep->AmountCol(16, 17, $Data13['per16']);
            $rep->AmountCol(17, 18, $Data13['per17']);
            $rep->AmountCol(18, 19, $Data13['per18']);
            $rep->AmountCol(19, 20, $Data13['per19']);
            $rep->AmountCol(20, 21, $Data13['per20']);
            $rep->AmountCol(21, 22, $Data13['per21']);
            $rep->AmountCol(22, 23, $Data13['per22']);
            $rep->AmountCol(23, 24, $Data13['per23']);
            $rep->AmountCol(24, 25, $Data13['per24']);
            $rep->AmountCol(25, 26, $Data13['per25']);
            $rep->AmountCol(26, 27, $Data13['per26']);
            $rep->AmountCol(27, 28, $Data13['per27']);
            $rep->AmountCol(28, 29, $Data13['per28']);
            $rep->AmountCol(29, 30, $Data13['per29']);
            $rep->AmountCol(30, 31, $Data13['per30']);
            if($mon_days==31) {
                $rep->AmountCol(31, 32, $Data13['per31']);
            }

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

            $rep->Font('bold');
            $rep->AmountCol(32, 33, $Total);
            $rep->Font('');

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

            if($mon_days==31) {
                $tot_per31 += $Data13['per31'];
            }

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
        $rep->Line($rep->row - 2);
    }

//    $rep->Line($rep->row - 8);

//    if ($graphics)
//    {
//        $pg->x[] = $typename;
//        $pg->y[] = abs($code_open_balance + $open_balance_total);
//        $pg->z[] = abs($code_period_balance + $period_balance_total);
//    }





    if($p_q==0){
        $rep->Font('bold');
        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('Grand Total'));
        $rep->AmountCol(1, 2, $tot_per1, 0);
        $rep->AmountCol(2, 3, $tot_per2, 0);
        $rep->AmountCol(3, 4, $tot_per3, 0);
        $rep->AmountCol(4, 5, $tot_per4, 0);
        $rep->AmountCol(5, 6, $tot_per5, 0);
        $rep->AmountCol(6, 7, $tot_per6, 0);
        $rep->AmountCol(7, 8, $tot_per7, 0);
        $rep->AmountCol(8, 9, $tot_per8, 0);
        $rep->AmountCol(9, 10, $tot_per9, 0);
        $rep->AmountCol(10, 11, $tot_per10, 0);
        $rep->AmountCol(11, 12, $tot_per11, 0);
        $rep->AmountCol(12, 13, $tot_per12, 0);
        $rep->AmountCol(13, 14, $tot_per13, 0);
        $rep->AmountCol(14, 15, $tot_per14, 0);
        $rep->AmountCol(15, 16, $tot_per15, 0);
        $rep->AmountCol(16, 17, $tot_per16, 0);
        $rep->AmountCol(17, 18, $tot_per17, 0);
        $rep->AmountCol(18, 19, $tot_per18, 0);
        $rep->AmountCol(19, 20, $tot_per19, 0);
        $rep->AmountCol(20, 21, $tot_per20, 0);
        $rep->AmountCol(21, 22, $tot_per21, 0);
        $rep->AmountCol(22, 23, $tot_per22, 0);
        $rep->AmountCol(23, 24, $tot_per23, 0);
        $rep->AmountCol(24, 25, $tot_per24, 0);
        $rep->AmountCol(25, 26, $tot_per25, 0);
        $rep->AmountCol(26, 27, $tot_per26, 0);
        $rep->AmountCol(27, 28, $tot_per27, 0);
        $rep->AmountCol(28, 29, $tot_per28, 0);
        $rep->AmountCol(29, 30, $tot_per29, 0);
        $rep->AmountCol(30, 31, $tot_per30, 0);

        if($mon_days==31) {
            $rep->AmountCol(31, 32, $tot_per31, 0);
        }

        $rep->AmountCol(32, 33, $grand_total, 0);

        //-------------for no of invoices row------------//

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('No Of Invoices'));
        $rep->AmountCol(1, 2, $invoices['per01'], 0);
        $rep->AmountCol(2, 3, $invoices['per02'], 0);
        $rep->AmountCol(3, 4, $invoices['per03'], 0);
        $rep->AmountCol(4, 5, $invoices['per04'], 0);
        $rep->AmountCol(5, 6, $invoices['per05'], 0);
        $rep->AmountCol(6, 7, $invoices['per06'], 0);
        $rep->AmountCol(7, 8, $invoices['per07'], 0);
        $rep->AmountCol(8, 9, $invoices['per08'], 0);
        $rep->AmountCol(9, 10, $invoices['per09'], 0);
        $rep->AmountCol(10, 11, $invoices['per10'], 0);
        $rep->AmountCol(11, 12, $invoices['per11'], 0);
        $rep->AmountCol(12, 13, $invoices['per12'], 0);
        $rep->AmountCol(13, 14, $invoices['per13'], 0);
        $rep->AmountCol(14, 15, $invoices['per14'], 0);
        $rep->AmountCol(15, 16, $invoices['per15'], 0);
        $rep->AmountCol(16, 17, $invoices['per16'], 0);
        $rep->AmountCol(17, 18, $invoices['per17'], 0);
        $rep->AmountCol(18, 19, $invoices['per18'], 0);
        $rep->AmountCol(19, 20, $invoices['per19'], 0);
        $rep->AmountCol(20, 21, $invoices['per20'], 0);
        $rep->AmountCol(21, 22, $invoices['per21'], 0);
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

        $rep->AmountCol(32, 33, $invoices_total, 0);

        $rep->Line($rep->row - 8);

    }
    else{
        $rep->Font('bold');
        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('Grand Total'));
        $rep->AmountCol(1, 2, $tot_per1, 0);
        $rep->AmountCol(2, 3, $tot_per2, 0);
        $rep->AmountCol(3, 4, $tot_per3, 0);
        $rep->AmountCol(4, 5, $tot_per4, 0);
        $rep->AmountCol(5, 6, $tot_per5, 0);
        $rep->AmountCol(6, 7, $tot_per6, 0);
        $rep->AmountCol(7, 8, $tot_per7, 0);
        $rep->AmountCol(8, 9, $tot_per8, 0);
        $rep->AmountCol(9, 10, $tot_per9, 0);
        $rep->AmountCol(10, 11, $tot_per10, 0);
        $rep->AmountCol(11, 12, $tot_per11, 0);
        $rep->AmountCol(12, 13, $tot_per12, 0);
        $rep->AmountCol(13, 14, $tot_per13, 0);
        $rep->AmountCol(14, 15, $tot_per14, 0);
        $rep->AmountCol(15, 16, $tot_per15, 0);
        $rep->AmountCol(16, 17, $tot_per16, 0);
        $rep->AmountCol(17, 18, $tot_per17, 0);
        $rep->AmountCol(18, 19, $tot_per18, 0);
        $rep->AmountCol(19, 20, $tot_per19, 0);
        $rep->AmountCol(20, 21, $tot_per20, 0);
        $rep->AmountCol(21, 22, $tot_per21, 0);
        $rep->AmountCol(22, 23, $tot_per22, 0);
        $rep->AmountCol(23, 24, $tot_per23, 0);
        $rep->AmountCol(24, 25, $tot_per24, 0);
        $rep->AmountCol(25, 26, $tot_per25, 0);
        $rep->AmountCol(26, 27, $tot_per26, 0);
        $rep->AmountCol(27, 28, $tot_per27, 0);
        $rep->AmountCol(28, 29, $tot_per28, 0);
        $rep->AmountCol(29, 30, $tot_per29, 0);
        $rep->AmountCol(30, 31, $tot_per30, 0);

        if($mon_days==31) {
            $rep->AmountCol(31, 32, $tot_per31, 0);
        }

        $rep->AmountCol(32, 33, $grand_total, 0);

        //-------------for discount row------------//

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('Overall Discount'));
        $rep->AmountCol(1, 2, $dis['per01'], 0);
        $rep->AmountCol(2, 3, $dis['per02'], 0);
        $rep->AmountCol(3, 4, $dis['per03'], 0);
        $rep->AmountCol(4, 5, $dis['per04'], 0);
        $rep->AmountCol(5, 6, $dis['per05'], 0);
        $rep->AmountCol(6, 7, $dis['per06'], 0);
        $rep->AmountCol(7, 8, $dis['per07'], 0);
        $rep->AmountCol(8, 9, $dis['per08'], 0);
        $rep->AmountCol(9, 10, $dis['per09'], 0);
        $rep->AmountCol(10, 11, $dis['per10'], 0);
        $rep->AmountCol(11, 12, $dis['per11'], 0);
        $rep->AmountCol(12, 13, $dis['per12'], 0);
        $rep->AmountCol(13, 14, $dis['per13'], 0);
        $rep->AmountCol(14, 15, $dis['per14'], 0);
        $rep->AmountCol(15, 16, $dis['per15'], 0);
        $rep->AmountCol(16, 17, $dis['per16'], 0);
        $rep->AmountCol(17, 18, $dis['per17'], 0);
        $rep->AmountCol(18, 19, $dis['per18'], 0);
        $rep->AmountCol(19, 20, $dis['per19'], 0);
        $rep->AmountCol(20, 21, $dis['per20'], 0);
        $rep->AmountCol(21, 22, $dis['per21'], 0);
        $rep->AmountCol(22, 23, $dis['per22'], 0);
        $rep->AmountCol(23, 24, $dis['per23'], 0);
        $rep->AmountCol(24, 25, $dis['per24'], 0);
        $rep->AmountCol(25, 26, $dis['per25'], 0);
        $rep->AmountCol(26, 27, $dis['per26'], 0);
        $rep->AmountCol(27, 28, $dis['per27'], 0);
        $rep->AmountCol(28, 29, $dis['per28'], 0);
        $rep->AmountCol(29, 30, $dis['per29'], 0);
        $rep->AmountCol(30, 31, $dis['per30'], 0);

        if($mon_days==31) {
            $rep->AmountCol(31, 32, $dis['per31'], 0);
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
            $overall_discount = $dis['per01'] + $dis['per02'] + $dis['per03'] + $dis['per04'] +
                $dis['per05'] + $dis['per06'] + $dis['per07'] + $dis['per08'] + $dis['per09'] +
                $dis['per10'] + $dis['per11'] + $dis['per12'] + $dis['per13'] + $dis['per14'] +
                $dis['per15'] + $dis['per16'] + $dis['per17'] + $dis['per18'] + $dis['per19'] +
                $dis['per20'] + $dis['per21'] + $dis['per22'] + $dis['per23'] + $dis['per24'] +
                $dis['per25'] + $dis['per26'] + $dis['per27'] + $dis['per28'] + $dis['per29'] +
                $dis['per30'];

        $rep->AmountCol(32, 33, $overall_discount, 0);

//-------------for net amount row------------//

        $rep->Line($rep->row - 8);
        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('Net Total'));
        $rep->AmountCol(1, 2, $tot_per1 - $dis['per01'], 0);
        $rep->AmountCol(2, 3, $tot_per2 - $dis['per02'], 0);
        $rep->AmountCol(3, 4, $tot_per3 - $dis['per03'], 0);
        $rep->AmountCol(4, 5, $tot_per4 - $dis['per04'], 0);
        $rep->AmountCol(5, 6, $tot_per5 - $dis['per05'], 0);
        $rep->AmountCol(6, 7, $tot_per6 - $dis['per06'], 0);
        $rep->AmountCol(7, 8, $tot_per7 - $dis['per07'], 0);
        $rep->AmountCol(8, 9, $tot_per8 - $dis['per08'], 0);
        $rep->AmountCol(9, 10, $tot_per9 - $dis['per09'], 0);
        $rep->AmountCol(10, 11, $tot_per10 - $dis['per10'], 0);
        $rep->AmountCol(11, 12, $tot_per11 - $dis['per11'], 0);
        $rep->AmountCol(12, 13, $tot_per12 - $dis['per12'], 0);
        $rep->AmountCol(13, 14, $tot_per13 - $dis['per13'], 0);
        $rep->AmountCol(14, 15, $tot_per14 - $dis['per14'], 0);
        $rep->AmountCol(15, 16, $tot_per15 - $dis['per15'], 0);
        $rep->AmountCol(16, 17, $tot_per16 - $dis['per16'], 0);
        $rep->AmountCol(17, 18, $tot_per17 - $dis['per17'], 0);
        $rep->AmountCol(18, 19, $tot_per18 - $dis['per18'], 0);
        $rep->AmountCol(19, 20, $tot_per19 - $dis['per19'], 0);
        $rep->AmountCol(20, 21, $tot_per20 - $dis['per20'], 0);
        $rep->AmountCol(21, 22, $tot_per21 - $dis['per21'], 0);
        $rep->AmountCol(22, 23, $tot_per22 - $dis['per22'], 0);
        $rep->AmountCol(23, 24, $tot_per23 - $dis['per23'], 0);
        $rep->AmountCol(24, 25, $tot_per24 - $dis['per24'], 0);
        $rep->AmountCol(25, 26, $tot_per25 - $dis['per25'], 0);
        $rep->AmountCol(26, 27, $tot_per26 - $dis['per26'], 0);
        $rep->AmountCol(27, 28, $tot_per27 - $dis['per27'], 0);
        $rep->AmountCol(28, 29, $tot_per28 - $dis['per28'], 0);
        $rep->AmountCol(29, 30, $tot_per29 - $dis['per29'], 0);
        $rep->AmountCol(30, 31, $tot_per30 - $dis['per30'], 0);

        if($mon_days==31) {
            $rep->AmountCol(31, 32, $tot_per31 - $dis['per31'], 0);
        }

        $rep->AmountCol(32, 33, $grand_total - $overall_discount, 0);

        $rep->Line($rep->row - 8);

        //-------------for no of invoices row------------//

        $rep->NewLine(2);
        $rep->TextCol(0, 1, _('No Of Invoices'));
        $rep->AmountCol(1, 2, $invoices['per01'], 0);
        $rep->AmountCol(2, 3, $invoices['per02'], 0);
        $rep->AmountCol(3, 4, $invoices['per03'], 0);
        $rep->AmountCol(4, 5, $invoices['per04'], 0);
        $rep->AmountCol(5, 6, $invoices['per05'], 0);
        $rep->AmountCol(6, 7, $invoices['per06'], 0);
        $rep->AmountCol(7, 8, $invoices['per07'], 0);
        $rep->AmountCol(8, 9, $invoices['per08'], 0);
        $rep->AmountCol(9, 10, $invoices['per09'], 0);
        $rep->AmountCol(10, 11, $invoices['per10'], 0);
        $rep->AmountCol(11, 12, $invoices['per11'], 0);
        $rep->AmountCol(12, 13, $invoices['per12'], 0);
        $rep->AmountCol(13, 14, $invoices['per13'], 0);
        $rep->AmountCol(14, 15, $invoices['per14'], 0);
        $rep->AmountCol(15, 16, $invoices['per15'], 0);
        $rep->AmountCol(16, 17, $invoices['per16'], 0);
        $rep->AmountCol(17, 18, $invoices['per17'], 0);
        $rep->AmountCol(18, 19, $invoices['per18'], 0);
        $rep->AmountCol(19, 20, $invoices['per19'], 0);
        $rep->AmountCol(20, 21, $invoices['per20'], 0);
        $rep->AmountCol(21, 22, $invoices['per21'], 0);
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

        $rep->AmountCol(32, 33, $invoices_total, 0);

        $rep->Line($rep->row - 8);






    }

    $rep->Font('');

    if ($graphics)
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
        $pg->type      = $graphics;
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
    $rep->NewLine();

    $rep->End();
}

?>
