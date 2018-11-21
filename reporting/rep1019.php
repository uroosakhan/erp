<?php
$page_security = 'SA_SALESANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Inventory Sales Report
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_category_db.inc");

//----------------------------------------------------------------------------------------------------

print_inventory_sales();

function getTransactions($types)
{
    $end = date2sql(end_fiscalyear());
    $yr = date('Y', strtotime($end));
    //current
    $sdate12 = date('Y-m-d',mktime(0,0,0,12,1,$yr));
    $sdate11 = date('Y-m-d',mktime(0,0,0,11,1,$yr));
    $sdate10 = date('Y-m-d',mktime(0,0,0,10,1,$yr));
    $sdate9 = date('Y-m-d',mktime(0,0,0,9,1,$yr));
    $sdate8 = date('Y-m-d',mktime(0,0,0,8,1,$yr));
    $sdate7 = date('Y-m-d',mktime(0,0,0,7,1,$yr));
    $sdate6 = date('Y-m-d',mktime(0,0,0,6,1,$yr));
    $sdate5 = date('Y-m-d',mktime(0,0,0,5,1,$yr));
    $sdate4 = date('Y-m-d',mktime(0,0,0,4,1,$yr));
    $sdate3 = date('Y-m-d',mktime(0,0,0,3,1,$yr));
    $sdate2 = date('Y-m-d',mktime(0,0,0,2,1,$yr));
    $sdate1 = date('Y-m-d',mktime(0,0,0,1,1,$yr));

    $edate12 = date('Y-m-d',mktime(0,0,0,12,31,$yr+1));
    $edate11 = date('Y-m-d',mktime(0,0,0,11,30,$yr));
    $edate10 = date('Y-m-d',mktime(0,0,0,10,31,$yr));
    $edate9 = date('Y-m-d',mktime(0,0,0,9,30,$yr));
    $edate8 = date('Y-m-d',mktime(0,0,0,8,31,$yr));
    $edate7 = date('Y-m-d',mktime(0,0,0,7,31,$yr));
    $edate6 = date('Y-m-d',mktime(0,0,0,6,30,$yr));
    $edate5 = date('Y-m-d',mktime(0,0,0,5,31,$yr));
    $edate4 = date('Y-m-d',mktime(0,0,0,4,30,$yr));
    $edate3 = date('Y-m-d',mktime(0,0,0,3,31,$yr));
    $edate2 = date('Y-m-d',mktime(0,0,0,2,28,$yr));
    $edate1 = date('Y-m-d',mktime(0,0,0,1,31,$yr));
    ////end current
    // current -1
    $sdate24 = date('Y-m-d',mktime(0,0,0,12,1,$yr-1));
    $sdate23 = date('Y-m-d',mktime(0,0,0,11,1,$yr-1));
    $sdate22 = date('Y-m-d',mktime(0,0,0,10,1,$yr-1));
    $sdate21 = date('Y-m-d',mktime(0,0,0,9,1,$yr-1));
    $sdate20 = date('Y-m-d',mktime(0,0,0,8,1,$yr-1));
    $sdate19 = date('Y-m-d',mktime(0,0,0,7,1,$yr-1));
    $sdate18 = date('Y-m-d',mktime(0,0,0,6,1,$yr-1));
    $sdate17 = date('Y-m-d',mktime(0,0,0,5,1,$yr-1));
    $sdate16 = date('Y-m-d',mktime(0,0,0,4,1,$yr-1));
    $sdate15 = date('Y-m-d',mktime(0,0,0,3,1,$yr-1));
    $sdate14 = date('Y-m-d',mktime(0,0,0,2,1,$yr-1));
    $sdate13 = date('Y-m-d',mktime(0,0,0,1,1,$yr-1));

    $edate24 = date('Y-m-d',mktime(0,0,0,12,31,$yr-1));
    $edate23 = date('Y-m-d',mktime(0,0,0,11,30,$yr-1));
    $edate22 = date('Y-m-d',mktime(0,0,0,10,31,$yr-1));
    $edate21 = date('Y-m-d',mktime(0,0,0,9,30,$yr-1));
    $edate20 = date('Y-m-d',mktime(0,0,0,8,31,$yr-1));
    $edate19 = date('Y-m-d',mktime(0,0,0,7,31,$yr-1));
    $edate18 = date('Y-m-d',mktime(0,0,0,6,30,$yr-1));
    $edate17 = date('Y-m-d',mktime(0,0,0,5,31,$yr-1));
    $edate16 = date('Y-m-d',mktime(0,0,0,4,30,$yr-1));
    $edate15 = date('Y-m-d',mktime(0,0,0,3,31,$yr-1));
    $edate14 = date('Y-m-d',mktime(0,0,0,2,28,$yr-1));
    $edate13 = date('Y-m-d',mktime(0,0,0,1,31,$yr-1));
    ////end current -1
    // current -2
    $sdate36 = date('Y-m-d',mktime(0,0,0,12,1,$yr-2));
    $sdate35 = date('Y-m-d',mktime(0,0,0,11,1,$yr-2));
    $sdate34 = date('Y-m-d',mktime(0,0,0,10,1,$yr-2));
    $sdate33 = date('Y-m-d',mktime(0,0,0,9,1,$yr-2));
    $sdate32 = date('Y-m-d',mktime(0,0,0,8,1,$yr-2));
    $sdate31 = date('Y-m-d',mktime(0,0,0,7,1,$yr-2));
    $sdate30 = date('Y-m-d',mktime(0,0,0,6,1,$yr-2));
    $sdate29 = date('Y-m-d',mktime(0,0,0,5,1,$yr-2));
    $sdate28 = date('Y-m-d',mktime(0,0,0,4,1,$yr-2));
    $sdate27 = date('Y-m-d',mktime(0,0,0,3,1,$yr-2));
    $sdate26 = date('Y-m-d',mktime(0,0,0,2,1,$yr-2));
    $sdate25 = date('Y-m-d',mktime(0,0,0,1,1,$yr-2));

    $edate36 = date('Y-m-d',mktime(0,0,0,12,31,$yr-2));
    $edate35 = date('Y-m-d',mktime(0,0,0,11,30,$yr-2));
    $edate34 = date('Y-m-d',mktime(0,0,0,10,31,$yr-2));
    $edate33 = date('Y-m-d',mktime(0,0,0,9,30,$yr-2));
    $edate32 = date('Y-m-d',mktime(0,0,0,8,31,$yr-2));
    $edate31 = date('Y-m-d',mktime(0,0,0,7,31,$yr-2));
    $edate30 = date('Y-m-d',mktime(0,0,0,6,30,$yr-2));
    $edate29 = date('Y-m-d',mktime(0,0,0,5,31,$yr-2));
    $edate28 = date('Y-m-d',mktime(0,0,0,4,30,$yr-2));
    $edate27 = date('Y-m-d',mktime(0,0,0,3,31,$yr-2));
    $edate26 = date('Y-m-d',mktime(0,0,0,2,28,$yr-2));
    $edate25 = date('Y-m-d',mktime(0,0,0,1,31,$yr-2));
     ////end current -2
      // current -3
      $sdate48 = date('Y-m-d',mktime(0,0,0,12,1,$yr-3));
      $sdate47 = date('Y-m-d',mktime(0,0,0,11,1,$yr-3));
      $sdate46 = date('Y-m-d',mktime(0,0,0,10,1,$yr-3));
      $sdate45 = date('Y-m-d',mktime(0,0,0,9,1,$yr-3));
      $sdate44 = date('Y-m-d',mktime(0,0,0,8,1,$yr-3));
      $sdate43 = date('Y-m-d',mktime(0,0,0,7,1,$yr-3));
      $sdate42 = date('Y-m-d',mktime(0,0,0,6,1,$yr-3));
      $sdate41 = date('Y-m-d',mktime(0,0,0,5,1,$yr-3));
      $sdate40 = date('Y-m-d',mktime(0,0,0,4,1,$yr-3));
      $sdate39 = date('Y-m-d',mktime(0,0,0,3,1,$yr-3));
      $sdate38 = date('Y-m-d',mktime(0,0,0,2,1,$yr-3));
      $sdate37 = date('Y-m-d',mktime(0,0,0,1,1,$yr-3));

      $edate48 = date('Y-m-d',mktime(0,0,0,12,31,$yr-3));
      $edate47 = date('Y-m-d',mktime(0,0,0,11,30,$yr-3));
      $edate46 = date('Y-m-d',mktime(0,0,0,10,31,$yr-3));
      $edate45 = date('Y-m-d',mktime(0,0,0,9,30,$yr-3));
      $edate44 = date('Y-m-d',mktime(0,0,0,8,31,$yr-3));
      $edate43 = date('Y-m-d',mktime(0,0,0,7,31,$yr-3));
      $edate42 = date('Y-m-d',mktime(0,0,0,6,30,$yr-3));
      $edate41 = date('Y-m-d',mktime(0,0,0,5,31,$yr-3));
      $edate40 = date('Y-m-d',mktime(0,0,0,4,30,$yr-3));
      $edate39 = date('Y-m-d',mktime(0,0,0,3,31,$yr-3));
      $edate38 = date('Y-m-d',mktime(0,0,0,2,28,$yr-3));
      $edate37 = date('Y-m-d',mktime(0,0,0,1,31,$yr-3));
      ////end current -3
      // current -4
      $sdate60 = date('Y-m-d',mktime(0,0,0,12,1,$yr-4));
      $sdate59 = date('Y-m-d',mktime(0,0,0,11,1,$yr-4));
      $sdate58 = date('Y-m-d',mktime(0,0,0,10,1,$yr-4));
      $sdate57 = date('Y-m-d',mktime(0,0,0,9,1,$yr-4));
      $sdate56 = date('Y-m-d',mktime(0,0,0,8,1,$yr-4));
      $sdate55 = date('Y-m-d',mktime(0,0,0,7,1,$yr-4));
      $sdate54 = date('Y-m-d',mktime(0,0,0,6,1,$yr-4));
      $sdate53 = date('Y-m-d',mktime(0,0,0,5,1,$yr-4));
      $sdate52 = date('Y-m-d',mktime(0,0,0,4,1,$yr-4));
      $sdate51 = date('Y-m-d',mktime(0,0,0,3,1,$yr-4));
      $sdate50 = date('Y-m-d',mktime(0,0,0,2,1,$yr-4));
      $sdate49 = date('Y-m-d',mktime(0,0,0,1,1,$yr-4));

      $edate60 = date('Y-m-d',mktime(0,0,0,12,31,$yr-4));
      $edate59= date('Y-m-d',mktime(0,0,0,11,30,$yr-4));
      $edate58 = date('Y-m-d',mktime(0,0,0,10,31,$yr-4));
      $edate57 = date('Y-m-d',mktime(0,0,0,9,30,$yr-4));
      $edate56 = date('Y-m-d',mktime(0,0,0,8,31,$yr-4));
      $edate55 = date('Y-m-d',mktime(0,0,0,7,31,$yr-4));
      $edate54 = date('Y-m-d',mktime(0,0,0,6,30,$yr-4));
      $edate53 = date('Y-m-d',mktime(0,0,0,5,31,$yr-4));
      $edate52 = date('Y-m-d',mktime(0,0,0,4,30,$yr-4));
      $edate51 = date('Y-m-d',mktime(0,0,0,3,31,$yr-4));
      $edate50 = date('Y-m-d',mktime(0,0,0,2,28,$yr-4));
      $edate49 = date('Y-m-d',mktime(0,0,0,1,31,$yr-4));
      ////end current -4


    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$sdate1' AND trans.tran_date <= '$edate1' 
THEN (ov_amount) ELSE 0 END) AS prd1,
SUM(CASE WHEN trans.tran_date >= '$sdate2' AND trans.tran_date <= '$edate2' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd2,
SUM(CASE WHEN trans.tran_date >= '$sdate3' AND trans.tran_date <= '$edate3' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd3,
SUM(CASE WHEN trans.tran_date >= '$sdate4' AND trans.tran_date <= '$edate4' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd4,
SUM(CASE WHEN trans.tran_date >= '$sdate5' AND trans.tran_date <= '$edate5' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd5,
SUM(CASE WHEN trans.tran_date >= '$sdate6' AND trans.tran_date <= '$edate6' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd6,
SUM(CASE WHEN trans.tran_date >= '$sdate7' AND trans.tran_date <= '$edate7' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd7,
SUM(CASE WHEN trans.tran_date >= '$sdate8' AND trans.tran_date <= '$edate8' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd8,
SUM(CASE WHEN trans.tran_date >= '$sdate9' AND trans.tran_date <= '$edate9' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd9,
SUM(CASE WHEN trans.tran_date >= '$sdate10' AND trans.tran_date <= '$edate10' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd10,
SUM(CASE WHEN trans.tran_date >= '$sdate11' AND trans.tran_date <= '$edate11' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd11,
SUM(CASE WHEN trans.tran_date >= '$sdate12' AND trans.tran_date <= '$edate12' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd12,

SUM(CASE WHEN trans.tran_date >= '$sdate13' AND trans.tran_date <= '$edate13' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd13,
SUM(CASE WHEN trans.tran_date >= '$sdate14' AND trans.tran_date <= '$edate14' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd14,
SUM(CASE WHEN trans.tran_date >= '$sdate15' AND trans.tran_date <= '$edate15' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd15,
SUM(CASE WHEN trans.tran_date >= '$sdate16' AND trans.tran_date <= '$edate16' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd16,
SUM(CASE WHEN trans.tran_date >= '$sdate17' AND trans.tran_date <= '$edate17' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd17,
SUM(CASE WHEN trans.tran_date >= '$sdate18' AND trans.tran_date <= '$edate18' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd18,
SUM(CASE WHEN trans.tran_date >= '$sdate19' AND trans.tran_date <= '$edate19' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd19,
SUM(CASE WHEN trans.tran_date >= '$sdate20' AND trans.tran_date <= '$edate20' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd20,
SUM(CASE WHEN trans.tran_date >= '$sdate21' AND trans.tran_date <= '$edate21' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd21,
SUM(CASE WHEN trans.tran_date >= '$sdate22' AND trans.tran_date <= '$edate22' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd22,
SUM(CASE WHEN trans.tran_date >= '$sdate23' AND trans.tran_date <= '$edate23' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd23,
SUM(CASE WHEN trans.tran_date >= '$sdate24' AND trans.tran_date <= '$edate24' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd24,

SUM(CASE WHEN trans.tran_date >= '$sdate25' AND trans.tran_date <= '$edate25' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd25,
SUM(CASE WHEN trans.tran_date >= '$sdate26' AND trans.tran_date <= '$edate26' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd26,
SUM(CASE WHEN trans.tran_date >= '$sdate27' AND trans.tran_date <= '$edate27' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd27,
SUM(CASE WHEN trans.tran_date >= '$sdate28' AND trans.tran_date <= '$edate28' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd28,
SUM(CASE WHEN trans.tran_date >= '$sdate29' AND trans.tran_date <= '$edate29' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd29,
SUM(CASE WHEN trans.tran_date >= '$sdate30' AND trans.tran_date <= '$edate30' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd30,
SUM(CASE WHEN trans.tran_date >= '$sdate31' AND trans.tran_date <= '$edate31' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd31,
SUM(CASE WHEN trans.tran_date >= '$sdate32' AND trans.tran_date <= '$edate32' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd32,
SUM(CASE WHEN trans.tran_date >= '$sdate33' AND trans.tran_date <= '$edate33' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd33,
SUM(CASE WHEN trans.tran_date >= '$sdate34' AND trans.tran_date <= '$edate34' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd34,
SUM(CASE WHEN trans.tran_date >= '$sdate35' AND trans.tran_date <= '$edate35' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd35,
SUM(CASE WHEN trans.tran_date >= '$sdate36' AND trans.tran_date <= '$edate36' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd36,

SUM(CASE WHEN trans.tran_date >= '$sdate37' AND trans.tran_date <= '$edate37' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd25,
SUM(CASE WHEN trans.tran_date >= '$sdate38' AND trans.tran_date <= '$edate38' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd26,
SUM(CASE WHEN trans.tran_date >= '$sdate39' AND trans.tran_date <= '$edate39' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd27,
SUM(CASE WHEN trans.tran_date >= '$sdate40' AND trans.tran_date <= '$edate40' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd28,
SUM(CASE WHEN trans.tran_date >= '$sdate41' AND trans.tran_date <= '$edate41' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd29,
SUM(CASE WHEN trans.tran_date >= '$sdate42' AND trans.tran_date <= '$edate42' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd30,
SUM(CASE WHEN trans.tran_date >= '$sdate43' AND trans.tran_date <= '$edate43' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd31,
SUM(CASE WHEN trans.tran_date >= '$sdate44' AND trans.tran_date <= '$edate44' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd32,
SUM(CASE WHEN trans.tran_date >= '$sdate45' AND trans.tran_date <= '$edate45' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd33,
SUM(CASE WHEN trans.tran_date >= '$sdate46' AND trans.tran_date <= '$edate46' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd34,
SUM(CASE WHEN trans.tran_date >= '$sdate47' AND trans.tran_date <= '$edate47' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd35,
SUM(CASE WHEN trans.tran_date >= '$sdate48' AND trans.tran_date <= '$edate48' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd36,


SUM(CASE WHEN trans.tran_date >= '$sdate49' AND trans.tran_date <= '$edate49' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd25,
SUM(CASE WHEN trans.tran_date >= '$sdate50' AND trans.tran_date <= '$edate50' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd26,
SUM(CASE WHEN trans.tran_date >= '$sdate51' AND trans.tran_date <= '$edate51' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd27,
SUM(CASE WHEN trans.tran_date >= '$sdate52' AND trans.tran_date <= '$edate52' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd28,
SUM(CASE WHEN trans.tran_date >= '$sdate53' AND trans.tran_date <= '$edate53' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd29,
SUM(CASE WHEN trans.tran_date >= '$sdate54' AND trans.tran_date <= '$edate54' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd30,
SUM(CASE WHEN trans.tran_date >= '$sdate55' AND trans.tran_date <= '$edate55' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd31,
SUM(CASE WHEN trans.tran_date >= '$sdate56' AND trans.tran_date <= '$edate56' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd32,
SUM(CASE WHEN trans.tran_date >= '$sdate57' AND trans.tran_date <= '$edate57' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd33,
SUM(CASE WHEN trans.tran_date >= '$sdate58' AND trans.tran_date <= '$edate58' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd34,
SUM(CASE WHEN trans.tran_date >= '$sdate59' AND trans.tran_date <= '$edate59' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd35,
SUM(CASE WHEN trans.tran_date >= '$sdate60' AND trans.tran_date <= '$edate60' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd36

		FROM ".TB_PREF."debtor_trans trans
		WHERE trans.type = $types";
    /*if ($category != 0)
        $sql .= " AND item.category_id = ".db_escape($category);*/
  //  $sql .= "
	//	ORDER BY trans.tran_date";

      $db =  db_query($sql,"No transactions were returned");

    return $db;

}

//----------------------------------------------------------------------------------------------------

function get_total_num_fiscals_year()
{
    $sql ="SELECT COUNT(*) FROM `".TB_PREF."fiscal_year` WHERE `closed`=0";
    $result =  db_query($sql,'could not get Fiscal year');
    $myrow = db_fetch($result);
    return $myrow[0];
}


function get_fiscals_year()
{
    $sql ="SELECT  `end` FROM 0_fiscal_year";
    return  db_query($sql,'could not get Fiscal year');
    //$ft = db_fetch($db);
   // return $ft[0];
}
function print_inventory_sales()
{
    global $path_to_root, $systypes_array, $SysPrefs;

   
    $comments = $_POST['PARAM_0'];
    $types = $_POST['PARAM_1'];
    $orientation = $_POST['PARAM_2'];
    $destination = $_POST['PARAM_3'];

/*	$to = $_POST['PARAM_1'];

    $location = $_POST['PARAM_3'];
    $fromcust = $_POST['PARAM_4'];

	*/

	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();
    $dec = 0;    

	//if ($types == -1)
	//	$types = 0;
		
	if ($types == -1)
		$show_type = _('NO TYPE SELECTED');
	else
		$show_type = $systypes_array[$types];

	/*if ($location == '')
		$loc = _('All');
	else
		$loc = get_location_name($location);*/

	/*if ($fromcust == '')
		$fromc = _('All');
	else
		$fromc = get_customer_name($fromcust);*/



   // $cols    = array();
   /* $headers = array();

    $headers[0] = _("Month");
    $ft = get_fiscals_year();
    while($mayrow = db_fetch($ft))
    {
        $var = $mayrow['end'];

        $fiscal_year = $var;
        $headers[] = _(".$fiscal_year.");
    }


    $headers[] = _("Grand Total");


    $aligns  = array();*/

    $cols    = array();
    $headers = array();
    $aligns  = array();

     $ft = get_total_num_fiscals_year();
     $myrow2 = get_current_fiscalyear();

    $cols[0]    = 0;
    $headers[0] = _("Month");
    $aligns[0]  = 'left';
    $year2 = date("Y", strtotime($myrow2["end"]));

        for($i=0; $i <= 5; $i++)
        {
            $year = date("Y-m-d", strtotime($myrow2["end"]));
            $lastyear = strtotime("-".$i." year", strtotime($year));
            $var = date("Y", $lastyear);
            $stock_id[$i] = $var;

        }

    $cols    = array(0, 80,130,200, 270, 340,  420,510);
    $headers = array(_('Month'), _("$year2"), _("$stock_id[1]"),  _("$stock_id[2]"),  _("$stock_id[3]"), _("$stock_id[4]"),  _('Grand Total'));
    $aligns = array('left',	'right', 'right','right','right', 'right',  'right');

	if ($fromcust != '')
		$headers[2] = '';



    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Type'), 'from' => $show_type, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Yearly / Monthly Comparision'), "YearlyMonthly Comparision", user_pagesize(), 9, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $res = getTransactions($types);
    while ($myrow=db_fetch($res)) {
        $j_tot = $myrow['prd1'] + $myrow['prd13'] + $myrow['prd25'] + $myrow['prd37'] + $myrow['prd49'];
        $f_tot = $myrow['prd2'] + $myrow['prd14'] + $myrow['prd26'] + $myrow['prd38'] + $myrow['prd50'];
        $m_tot = $myrow['prd3'] + $myrow['prd15'] + $myrow['prd27'] + $myrow['prd39'] + $myrow['prd51'];
        $a_tot = $myrow['prd4'] + $myrow['prd16'] + $myrow['prd28'] + $myrow['prd40'] + $myrow['prd52'];
        $ma_tot = $myrow['prd5'] + $myrow['prd17'] + $myrow['prd29'] + $myrow['prd41'] + $myrow['prd53'];
        $jun_tot = $myrow['prd6'] + $myrow['prd18'] + $myrow['prd30'] + $myrow['prd42'] + $myrow['prd54'];
        $jul_tot = $myrow['prd7'] + $myrow['prd19'] + $myrow['prd31'] + $myrow['prd43'] + $myrow['prd55'];
        $au_tot = $myrow['prd8'] + $myrow['prd20'] + $myrow['prd32'] + $myrow['prd44'] + $myrow['prd56'];
        $s_tot = $myrow['prd9'] + $myrow['prd21'] + $myrow['prd33'] + $myrow['prd45'] + $myrow['prd57'];
        $o_tot = $myrow['prd10'] + $myrow['prd22'] + $myrow['prd34'] + $myrow['prd46'] + $myrow['prd58'];
        $n_tot = $myrow['prd11'] + $myrow['prd23'] + $myrow['prd35'] + $myrow['prd47'] + $myrow['prd59'];
        $d_tot = $myrow['prd12'] + $myrow['prd24'] + $myrow['prd36'] + $myrow['prd48'] + $myrow['prd60'];
        $g_tot = $j_tot + $f_tot + $m_tot + $a_tot + $ma_tot + $jun_tot + $jul_tot + $au_tot + $s_tot + $o_tot + $n_tot + $d_tot;

        $rep->TextCol(0, 1, _("January"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd1']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd13']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd25']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd37']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd49']), $dec);
        $rep->AmountCol(6, 7, abs($j_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("Febuary"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd2']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd14']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd26']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd38']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd50']), $dec);
        $rep->AmountCol(6, 7, abs($f_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("March"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd3']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd15']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd27']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd39']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd51']), $dec);
        $rep->AmountCol(6, 7, abs($m_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("April"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd4']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd16']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd28']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd40']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd52']), $dec);
        $rep->AmountCol(6, 7, abs($a_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("May"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd5']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd17']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd29']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd41']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd53']), $dec);
        $rep->AmountCol(6, 7, abs($ma_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("June"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd6']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd18']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd30']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd42']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd54']), $dec);
        $rep->AmountCol(6, 7, abs($jun_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("July"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd7']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd19']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd31']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd43']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd55']), $dec);
        $rep->AmountCol(6, 7, abs($jul_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("August"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd8']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd20']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd32']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd44']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd56']), $dec);
        $rep->AmountCol(6, 7, abs($au_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("September"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd9']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd21']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd33']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd45']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd57']), $dec);
        $rep->AmountCol(6, 7, abs($s_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("October"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd10']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd22']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd34']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd46']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd58']), $dec);
        $rep->AmountCol(6, 7, abs($o_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("November"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd11']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd23']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd35']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd47']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd59']), $dec);
        $rep->AmountCol(6, 7, abs($n_tot), $dec);

        $rep->NewLine();
        $rep->TextCol(0, 1, _("December"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd12']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd24']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd36']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd48']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd60']), $dec);
        $rep->AmountCol(6, 7, abs($d_tot), $dec);

        $rep->NewLine();
        $rep->NewLine();        
        $rep->Font(b);
        $rep->TextCol(0, 1, _("Total"), $dec);
        $rep->AmountCol(1, 2, abs($myrow['prd1']) + abs($myrow['prd2']) + abs($myrow['prd3']) + abs($myrow['prd4']) + abs($myrow['prd5']) + abs($myrow['prd6']) + abs($myrow['prd7']) + abs($myrow['prd8']) + abs($myrow['prd9']) + abs($myrow['prd10']) + abs($myrow['prd11']) + abs($myrow['prd12']), $dec);
        $rep->AmountCol(2, 3, abs($myrow['prd13']) + abs($myrow['prd14']) + abs($myrow['prd15']) + abs($myrow['prd16']) + abs($myrow['prd17']) + abs($myrow['prd18']) +  abs($myrow['prd19']) + abs($myrow['prd20']) + abs($myrow['prd21']) + abs($myrow['prd22']) + abs($myrow['prd23']) + abs($myrow['prd24']), $dec);
        $rep->AmountCol(3, 4, abs($myrow['prd25']) + abs($myrow['prd26']) + abs($myrow['prd27']) + abs($myrow['prd28']) + abs($myrow['prd29']) + abs($myrow['prd30']) +  abs($myrow['prd31']) + abs($myrow['prd32']) + abs($myrow['prd33']) + abs($myrow['prd34']) + abs($myrow['prd35']) + abs($myrow['prd36']), $dec);
        $rep->AmountCol(4, 5, abs($myrow['prd37']) + abs($myrow['prd38']) + abs($myrow['prd39']) + abs($myrow['prd40']) + abs($myrow['prd41']) + abs($myrow['prd42']) +  abs($myrow['prd43']) + abs($myrow['prd44']) + abs($myrow['prd45']) + abs($myrow['prd46']) + abs($myrow['prd47']) + abs($myrow['prd48']), $dec);
        $rep->AmountCol(5, 6, abs($myrow['prd49']) + abs($myrow['prd50']) + abs($myrow['prd51']) + abs($myrow['prd52']) + abs($myrow['prd53']) + abs($myrow['prd54']) +  abs($myrow['prd55']) + abs($myrow['prd56']) + abs($myrow['prd57']) + abs($myrow['prd58']) + abs($myrow['prd59']) + abs($myrow['pr60']), $dec);
        $rep->AmountCol(6, 7, abs($g_tot), $dec);

        $rep->Font();
        $rep->NewLine();
    }
//    $rep->fontSize -= 2;
//    $rep->TextCol(6, 7, round2($grand_grand_total), $dec);
//    $rep->fontSize += 2;


        $rep->Line($rep->row  - 4);
	$rep->NewLine();
    $rep->End();
}

?>