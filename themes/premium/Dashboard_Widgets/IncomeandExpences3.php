<?php
$page_security = 'SA_INCOME';

global $path_to_root;
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/dashboard/charts/charts_utils.php");
class IncomeAndExpences
{
    public function IEwidget()
    {

        echo "<form method='post'>";
        echo "<div class='col-md-12'>";
        echo "<div class='box-body'>";
        echo "<center>";
        month_list_cells(_("Month:"), 'month_', null,  _('Month Entry'), true, check_value('show_inactive'));

        echo "<input type='submit' name='save' value='Search'/> 
        </form>";
        echo "<div class='box-body' style='background-color:#F9F9F9;'>";
        echo "<div class='col-md-10'>";
        echo "<p class='text-center'>";
        $begin = begin_fiscalyear();
        $f_year = get_current_fiscalyear();
        $mo=$_POST['month_'];
        if($mo=7 || $mo=8 || $mo=9 || $mo=10 || $mo=11 || $mo=12)
            $yr = date('Y', strtotime($f_year['end']));
        else
            $yr = date('Y', strtotime($f_year['begin']));
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
//      $mo = date("m", strtotime($begin1));
//      $yr = date("Y", strtotime($begin1));
        $date13 = date('Y-M-d', mktime(0, 0, 0, $mo + 12, 1, $yr));
        $date12 = date('Y-m-d', mktime(0, 0, 0, $mo + 11, 1, $yr));
        $date11 = date('Y-m-d', mktime(0, 0, 0, $mo + 10, 1, $yr));
        $date10 = date('Y-m-d', mktime(0, 0, 0, $mo + 9, 1, $yr));
        $date09 = date('Y-m-d', mktime(0, 0, 0, $mo + 8, 1, $yr));
        $date08 = date('Y-m-d', mktime(0, 0, 0, $mo + 7, 1, $yr));
        $date07 = date('Y-m-d', mktime(0, 0, 0, $mo + 6, 1, $yr));
        $date06 = date('Y-m-d', mktime(0, 0, 0, $mo + 5, 1, $yr));
        $date05 = date('Y-m-d', mktime(0, 0, 0, $mo + 4, 1, $yr));
        $date04 = date('Y-m-d', mktime(0, 0, 0, $mo + 3, 1, $yr));
        $date03 = date('Y-m-d', mktime(0, 0, 0, $mo + 2, 1, $yr));
        $date02 = date('Y-m-d', mktime(0, 0, 0, $mo + 1, 1, $yr));
        $date01 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));

        //dz 31.10.15
        $fybegin1 = date2sql($begin);
        $fydate13 = date('Y-m-d', mktime(0, 0, 0, $mo + 12, -0, $yr));

        echo "<strong>Monthly Sales and Recovery:</strong>";
        echo "</p>";
        //FOR MONTHLY GRAPH

        //return db_fetch($result2);
        //var_dump($date13);
        $yrdata1 = strtotime($date01);
        $yrdata13 = strtotime($date02);
        $yrdata12 = strtotime($date03);
        $yrdata11 = strtotime($date04);
        $yrdata10 = strtotime($date05);
        $yrdata09 = strtotime($date06);
        $yrdata08 = strtotime($date07);
        $yrdata07 = strtotime($date08);
        $yrdata06 = strtotime($date09);
        $yrdata05 = strtotime($date10);
        $yrdata04 = strtotime($date11);
        $yrdata03 = strtotime($date12);
        ////////////////////////Today
        $month = date('M', strtotime($today));

        $mo=$_POST['month_'];
//        if($mo=7 || $mo=8 || $mo=9 || $mo=10 || $mo=11 || $mo=12)
//            $yr = date('Y', strtotime($f_year['end']));
//        else
//            $yr = date('Y', strtotime($f_year['begin']));
        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $show1 = date('d', mktime(0, 0, 0, $mo, 1, $yr));
        $show2 = date('d', mktime(0, 0, 0, $mo, 2, $yr));
        $show3 = date('d', mktime(0, 0, 0, $mo, 3, $yr));
        $show4 = date('d', mktime(0, 0, 0, $mo, 4, $yr));
        $show5 = date('d', mktime(0, 0, 0, $mo, 5, $yr));
        $show6 = date('d', mktime(0, 0, 0, $mo, 6, $yr));
        $show7 = date('d', mktime(0, 0, 0, $mo, 7, $yr));
        $show8 = date('d', mktime(0, 0, 0, $mo, 8, $yr));
        $show9 = date('d', mktime(0, 0, 0, $mo, 9, $yr));
        $show10 = date('d', mktime(0, 0, 0, $mo, 10, $yr));
        $show11 = date('d', mktime(0, 0, 0, $mo, 11, $yr));
        $show12 = date('d', mktime(0, 0, 0, $mo, 12, $yr));
        $show13 = date('d', mktime(0, 0, 0, $mo, 13, $yr));
        $show14 = date('d', mktime(0, 0, 0, $mo, 14, $yr));
        $show15 = date('d', mktime(0, 0, 0, $mo, 15, $yr));
        $show16 = date('d', mktime(0, 0, 0, $mo, 16, $yr));
        $show17 = date('d', mktime(0, 0, 0, $mo, 17, $yr));
        $show18 = date('d', mktime(0, 0, 0, $mo, 18, $yr));
        $show19 = date('d', mktime(0, 0, 0, $mo, 19, $yr));
        $show20 = date('d', mktime(0, 0, 0, $mo, 20, $yr));
        $show21 = date('d', mktime(0, 0, 0, $mo, 21, $yr));
        $show22 = date('d', mktime(0, 0, 0, $mo, 22, $yr));
        $show23 = date('d', mktime(0, 0, 0, $mo, 23, $yr));
        $show24 = date('d', mktime(0, 0, 0, $mo, 24, $yr));
        $show25 = date('d', mktime(0, 0, 0, $mo, 25, $yr));
        $show26 = date('d', mktime(0, 0, 0, $mo, 26, $yr));
        $show27 = date('d', mktime(0, 0, 0, $mo, 27, $yr));
        $show28 = date('d', mktime(0, 0, 0, $mo, 28, $yr));
        $show29 = date('d', mktime(0, 0, 0, $mo, 29, $yr));
        $show30 = date('d', mktime(0, 0, 0, $mo, 30, $yr));
        $show31 = date('d', mktime(0, 0, 0, $mo, 31, $yr));
        //////////////
        $mo=$_POST['month_'];
//        if($mo=7 || $mo=8 || $mo=9 || $mo=10 || $mo=11 || $mo=12)
//            $yr = date('Y', strtotime($f_year['end']));
//        else
//            $yr = date('Y', strtotime($f_year['begin']));
        $date1 = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $date2 = date('Y-m-d', mktime(0, 0, 0, $mo, 2, $yr));
        $date3 = date('Y-m-d', mktime(0, 0, 0, $mo, 3, $yr));
        $date4 = date('Y-m-d', mktime(0, 0, 0, $mo, 4, $yr));
        $date5 = date('Y-m-d', mktime(0, 0, 0, $mo, 5, $yr));
        $date6 = date('Y-m-d', mktime(0, 0, 0, $mo, 6, $yr));
        $date7 = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
        $date8 = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $date9 = date('Y-m-d', mktime(0, 0, 0, $mo, 9, $yr));
        $date10 = date('Y-m-d', mktime(0, 0, 0, $mo, 10, $yr));
        $date11 = date('Y-m-d', mktime(0, 0, 0, $mo, 11, $yr));
        $date12 = date('Y-m-d', mktime(0, 0, 0, $mo, 12, $yr));
        $date13 = date('Y-m-d', mktime(0, 0, 0, $mo, 13, $yr));
        $date14 = date('Y-m-d', mktime(0, 0, 0, $mo, 14, $yr));
        $date15 = date('Y-m-d', mktime(0, 0, 0, $mo, 15, $yr));
        $date16 = date('Y-m-d', mktime(0, 0, 0, $mo, 16, $yr));
        $date17 = date('Y-m-d', mktime(0, 0, 0, $mo, 17, $yr));
        $date18 = date('Y-m-d', mktime(0, 0, 0, $mo, 18, $yr));
        $date19 = date('Y-m-d', mktime(0, 0, 0, $mo, 19, $yr));
        $date20 = date('Y-m-d', mktime(0, 0, 0, $mo, 20, $yr));
        $date21 = date('Y-m-d', mktime(0, 0, 0, $mo, 21, $yr));
        $date22 = date('Y-m-d', mktime(0, 0, 0, $mo, 22, $yr));
        $date23 = date('Y-m-d', mktime(0, 0, 0, $mo, 23, $yr));
        $date24 = date('Y-m-d', mktime(0, 0, 0, $mo, 24, $yr));
        $date25 = date('Y-m-d', mktime(0, 0, 0, $mo, 25, $yr));
        $date26 = date('Y-m-d', mktime(0, 0, 0, $mo, 26, $yr));
        $date27 = date('Y-m-d', mktime(0, 0, 0, $mo, 27, $yr));
        $date28 = date('Y-m-d', mktime(0, 0, 0, $mo, 28, $yr));
        $date29 = date('Y-m-d', mktime(0, 0, 0, $mo, 29, $yr));
        $date30 = date('Y-m-d', mktime(0, 0, 0, $mo, 30, $yr));
        $date31 = date('Y-m-d', mktime(0, 0, 0, $mo, 31, $yr));


        $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date = '$date3'  
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per31
		FROM 
			" . TB_PREF . "debtor_trans trans
		WHERE  trans.type IN(10)
		
		GROUP BY trans.debtor_no";
        $result = db_query($sql, "Transactions could not be calculated");
///Recovery
        $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date = '$date3'  
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31' 
THEN ((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
               trans.ov_discount + trans.gst_wh
               + trans.supply_disc + trans.service_disc + trans.fbr_disc +
               trans.srb_disc - trans.discount1 - trans.discount2) * rate ) ELSE 0 END) AS per31
		FROM 
			" . TB_PREF . "debtor_trans trans
		WHERE  trans.type IN(2,12,41)
		GROUP BY trans.debtor_no";
        $result1 = db_query($sql, "Transactions could not be calculated");


        echo '
        <canvas id="line-chart" width="800" height="300" ;"></canvas>
        ';
        echo "<div class='chart'>";
        //<!-- Sales Chart Canvas -->
        echo '<canvas id="salesChart" style="height: 310px;"></canvas>';
        echo "</div>";//<!-- /.chart-responsive -->
        echo "</div>";//<!-- /.col --> .
        // var_dump($date01);
        // var_dump($M3);
        echo " <div class='col-md-5'>";
        echo " <p class='text-center'>";
//            echo" <strong>Sales Funnel</strong>";
        echo " </p>";


//include_once("./themes/".user_theme(). "/All_dashboardcharts/top10bank/bank.php");
//                            $_dash= new secondrow();
//
//                            $_dash->toptenbank();

        echo "</div>";//<!-- /.col -->
        echo "</div>";//<!-- /.row -->
        echo "</div>";//<!-- ./box-body -->

        echo "<div class='box-footer'>";
        echo "<div class='row'>";

        echo "<div class='col-sm-3 col-xs-6'>";
        echo "<div class='description-block border-right'>";
        $i = 0;
        while ($myrow = db_fetch($result)) {

            $per01[$i] = $myrow['per01'];
            $per02[$i] = $myrow['per02'];
            $per03[$i] = $myrow['per03'];
            $per04[$i] = $myrow['per04'];
            $per05[$i] = $myrow['per05'];
            $per06[$i] = $myrow['per06'];
            $per07[$i] = $myrow['per07'];
            $per08[$i] = $myrow['per08'];
            $per09[$i] = $myrow['per09'];
            $per10[$i] = $myrow['per10'];
            $per11[$i] = $myrow['per11'];
            $per12[$i] = $myrow['per12'];
            $per13[$i] = $myrow['per13'];
            $per14[$i] = $myrow['per14'];
            $per15[$i] = $myrow['per15'];
            $per16[$i] = $myrow['per16'];
            $per17[$i] = $myrow['per17'];
            $per18[$i] = $myrow['per18'];
            $per19[$i] = $myrow['per19'];
            $per20[$i] = $myrow['per20'];
            $per21[$i] = $myrow['per21'];
            $per22[$i] = $myrow['per22'];
            $per23[$i] = $myrow['per23'];
            $per24[$i] = $myrow['per24'];
            $per25[$i] = $myrow['per25'];
            $per26[$i] = $myrow['per26'];
            $per27[$i] = $myrow['per27'];
            $per28[$i] = $myrow['per28'];
            $per29[$i] = $myrow['per29'];
            $per30[$i] = $myrow['per30'];
            $per31[$i] = $myrow['per31'];
            $i++;
        }

//For Purchasing
        $j = 0;
        $data1 = array();
        $string1 = array();
        while ($myrow = db_fetch($result1)) {
            $qer01[$j] = $myrow['per01'];
            $qer02[$j] = $myrow['per02'];
            $qer03[$j] = $myrow['per03'];
            $qer04[$j] = $myrow['per04'];
            $qer05[$j] = $myrow['per05'];
            $qer06[$j] = $myrow['per06'];
            $qer07[$j] = $myrow['per07'];
            $qer08[$j] = $myrow['per08'];
            $qer09[$j] = $myrow['per09'];
            $qer10[$j] = $myrow['per10'];
            $qer11[$j] = $myrow['per11'];
            $qer12[$j] = $myrow['per12'];
            $qer13[$j] = $myrow['per13'];
            $qer14[$j] = $myrow['per14'];
            $qer15[$j] = $myrow['per15'];
            $qer16[$j] = $myrow['per16'];
            $qer17[$j] = $myrow['per17'];
            $qer18[$j] = $myrow['per18'];
            $qer19[$j] = $myrow['per19'];
            $qer20[$j] = $myrow['per20'];
            $qer21[$j] = $myrow['per21'];
            $qer22[$j] = $myrow['per22'];
            $qer23[$j] = $myrow['per23'];
            $qer24[$j] = $myrow['per24'];
            $qer25[$j] = $myrow['per25'];
            $qer26[$j] = $myrow['per26'];
            $qer27[$j] = $myrow['per27'];
            $qer28[$j] = $myrow['per28'];
            $qer29[$j] = $myrow['per29'];
            $qer30[$j] = $myrow['per30'];
            $qer31[$j] = $myrow['per31'];
            $data1[$j] = $myrow['total'];
            $string1[$j] = $myrow['class_name'];
            $j++;;
        }
///////////Purchasing
//For Purchasing
//        $k = 0;
//        $data2 = array();
//        $string2 = array();
//        while ($myrow = db_fetch($result2)) {
//            $ver01[$k] = $myrow['ver01'];
//            $ver02[$k] = $myrow['ver02'];
//            $ver03[$k] = $myrow['ver03'];
//            $ver04[$k] = $myrow['ver04'];
//            $ver05[$k] = $myrow['ver05'];
//            $ver06[$k] = $myrow['ver06'];
//            $ver07[$k] = $myrow['ver07'];
//            $ver08[$k] = $myrow['ver08'];
//            $ver09[$k] = $myrow['ver09'];
//            $ver10[$k] = $myrow['ver10'];
//            $ver11[$k] = $myrow['ver11'];
//            $ver12[$k] = $myrow['ver12'];
//            $ver13[$k] = $myrow['ver13'];
//            $ver14[$k] = $myrow['ver14'];
//            $ver15[$k] = $myrow['ver15'];
//            $ver16[$k] = $myrow['ver16'];
//            $ver17[$k] = $myrow['ver17'];
//            $ver18[$k] = $myrow['ver18'];
//            $ver19[$k] = $myrow['ver19'];
//            $ver20[$k] = $myrow['ver20'];
//            $ver21[$k] = $myrow['ver21'];
//            $ver22[$k] = $myrow['ver22'];
//            $ver23[$k] = $myrow['ver23'];
//            $ver24[$k] = $myrow['ver24'];
//            $ver25[$k] = $myrow['ver25'];
//            $ver26[$k] = $myrow['ver26'];
//            $ver27[$k] = $myrow['ver27'];
//            $ver28[$k] = $myrow['ver28'];
//            $ver29[$k] = $myrow['ver29'];
//            $ver30[$k] = $myrow['ver30'];
//            $ver31[$k] = $myrow['ver31'];
//            $data2[$k] = $myrow['total'];
//            $string2[$k] = $myrow['class_name'];
//            $k++;;
//        }
//        echo ' <script>var salesChartCanvas = $("#salesChart").get(0).getContext("2d");
//  var salesChart = new Chart(salesChartCanvas, salesChartData);
//
//  var salesChartData = {
//    labels: ["' . $show1 . '", "' . $show2 . '", "' . $show3 . '", "' . $show4 . '", "' . $show5 . '", "' . $show6 . '",
//     "' . $show7 . '", "' . $show8 . '", "' . $show9 . '", "' . $show10 . '", "' . $show11 . '", "' . $show12 . '",
//     "' . $show13 . '", "' . $show14 . '", "' . $show15 . '", "' . $show16 . '", "' . $show17 . '", "' . $show18 . '",
//     "' . $show19 . '", "' . $show20 . '", "' . $show21 . '", "' . $show22 . '", "' . $show23 . '", "' . $show24 . '",
//     "' . $show25 . '", "' . $show26 . '", "' . $show27 . '", "' . $show28 . '", "' . $show29 . '", "' . $show30 . '",
//     "' . $show31 . '"
//     ],
//    datasets: [
//      {
//        label: "Electronics",
////        fillColor: "rgb(210, 214, 222)",
////        strokeColor: "rgb(210, 214, 222)",
////        pointColor: "rgb(210, 214, 222)",
////        pointStrokeColor: "#c1c7d1",
////        pointHighlightFill: "#fff",
////        pointHighlightStroke: "rgb(220,220,220)",
//
//         data: [' . $per01[0] . ', ' . $per02[0] . ', ' . $per03[0] . ', ' . $per04[0] . ', ' . $per05[0] . ',
//         ' . $per06[0] . ', ' . $per07[0] . ', ' . $per08[0] . ', ' . $per09[0] . ', ' . $per10[0] . ',
//         ' . $per11[0] . ', ' . $per12[0] . ', ' . $per13[0] . ', ' . $per14[0] . ', ' . $per15[0] . ',
//         ' . $per16[0] . ', ' . $per17[0] . ', ' . $per18[0] . ', ' . $per19[0] . ', ' . $per20[0] . ',
//         ' . $per21[0] . ', ' . $per22[0] . ', ' . $per23[0] . ', ' . $per24[0] . ', ' . $per25[0] . ',
//         ' . $per26[0] . ', ' . $per27[0] . ', ' . $per28[0] . ', ' . $per29[0] . ', ' . $per30[0] . ',
//         ' . $per31[0] . '
//        ],
//        backgroundColor: "blue",
//                borderColor: "lightblue",
//                fill: false,
//                lineTension: 0,
//                radius: 5
//      },
//      {
//        label: "Digital Goods",
////        fillColor: "rgba(60,141,188,0.9)",
////        strokeColor: "rgba(60,141,188,0.8)",
////        pointColor: "#3b8bba",
////        pointStrokeColor: "rgba(60,141,188,1)",
////        pointHighlightFill: "#fff",
////        pointHighlightStroke: "rgba(60,141,188,1)",
//       data: [' . $qer01[0] . ', ' . $qer02[0] . ', ' . $qer03[0] . ', ' . $qer04[0] . ', ' . $qer05[0] . ',
//         ' . $qer06[0] . ', ' . $qer07[0] . ', ' . $qer08[0] . ', ' . $qer09[0] . ', ' . $qer10[0] . ',
//         ' . $qer11[0] . ', ' . $qer12[0] . ', ' . $qer13[0] . ', ' . $qer14[0] . ', ' . $qer15[0] . ',
//         ' . $qer16[0] . ', ' . $qer17[0] . ', ' . $qer18[0] . ', ' . $qer19[0] . ', ' . $qer20[0] . ',
//         ' . $qer21[0] . ', ' . $qer22[0] . ', ' . $qer23[0] . ', ' . $qer24[0] . ', ' . $qer25[0] . ',
//         ' . $qer26[0] . ', ' . $qer27[0] . ', ' . $qer28[0] . ', ' . $qer29[0] . ', ' . $qer30[0] . ',
//         ' . $qer31[0] . ']
//      }
//    ],
//    backgroundColor: "green",
//                borderColor: "lightgreen",
//                fill: false,
//                lineTension: 0,
//                radius: 5
//  };
//  //options
//    var options = {
//        responsive: true,
//        title: {
//            display: true,
//            position: "top",
//            text: "Line Graph",
//            fontSize: 18,
//            fontColor: "#111"
//        },
//        legend: {
//            display: true,
//            position: "bottom",
//            labels: {
//                fontColor: "#333",
//                fontSize: 16
//            }
//        }
//    };
//
//    //create Chart class object
//    var chart = new Chart(ctx, {
//        type: "line",
//        data: data,
//        options: options
//    });
//  </script>';


        echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales And Recovery</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
</head>
<body>
<canvas id="line-chart" width="800" height="450"></canvas></body>

<script>
    new Chart(document.getElementById("line-chart"), {
        type: \'line\',
        data: {

             labels: ["' . $show1 . '", "' . $show2 . '", "' . $show3 . '", "' . $show4 . '", "' . $show5 . '", "' . $show6 . '",
     "' . $show7 . '", "' . $show8 . '", "' . $show9 . '", "' . $show10 . '", "' . $show11 . '", "' . $show12 . '",
     "' . $show13 . '", "' . $show14 . '", "' . $show15 . '", "' . $show16 . '", "' . $show17 . '", "' . $show18 . '",
     "' . $show19 . '", "' . $show20 . '", "' . $show21 . '", "' . $show22 . '", "' . $show23 . '", "' . $show24 . '",
     "' . $show25 . '", "' . $show26 . '", "' . $show27 . '", "' . $show28 . '", "' . $show29 . '", "' . $show30 . '",
     "' . $show31 . '"
     ],
           datasets:   [{ 
         data: [' . $per01[0] . ', ' . $per02[0] . ', ' . $per03[0] . ', ' . $per04[0] . ', ' . $per05[0] . ',
         ' . $per06[0] . ', ' . $per07[0] . ', ' . $per08[0] . ', ' . $per09[0] . ', ' . $per10[0] . ',
         ' . $per11[0] . ', ' . $per12[0] . ', ' . $per13[0] . ', ' . $per14[0] . ', ' . $per15[0] . ',
         ' . $per16[0] . ', ' . $per17[0] . ', ' . $per18[0] . ', ' . $per19[0] . ', ' . $per20[0] . ',
         ' . $per21[0] . ', ' . $per22[0] . ', ' . $per23[0] . ', ' . $per24[0] . ', ' . $per25[0] . ',
         ' . $per26[0] . ', ' . $per27[0] . ', ' . $per28[0] . ', ' . $per29[0] . ', ' . $per30[0] . ',
         ' . $per31[0] . '
        ],
        label: "Sales",
        borderColor: "#3e95cd",
        fill: false
      }, { 
       data: [' . $qer01[0] . ', ' . $qer02[0] . ', ' . $qer03[0] . ', ' . $qer04[0] . ', ' . $qer05[0] . ',
         ' . $qer06[0] . ', ' . $qer07[0] . ', ' . $qer08[0] . ', ' . $qer09[0] . ', ' . $qer10[0] . ',
         ' . $qer11[0] . ', ' . $qer12[0] . ', ' . $qer13[0] . ', ' . $qer14[0] . ', ' . $qer15[0] . ',
         ' . $qer16[0] . ', ' . $qer17[0] . ', ' . $qer18[0] . ', ' . $qer19[0] . ', ' . $qer20[0] . ',
         ' . $qer21[0] . ', ' . $qer22[0] . ', ' . $qer23[0] . ', ' . $qer24[0] . ', ' . $qer25[0] . ',
         ' . $qer26[0] . ', ' . $qer27[0] . ', ' . $qer28[0] . ', ' . $qer29[0] . ', ' . $qer30[0] . ',
         ' . $qer31[0] . '],
        label: "Recovery",
        borderColor: "#8e5ea2",
        fill: false
      }
    ]
        },
        options: {
            title: {
                display: true
            }
        }
    });


</script>


</html>';


        ?>




<!--<script>-->
<!--            alert("work");-->
<!--            var salesChartCanvas = $("#line-chart").get(0).getContext("2d");-->
<!--            var salesChart = new Chart(salesChartCanvas, salesChartData);-->
<!--            //-->
<!--             var salesChartData = {-->
<!--            // new Chart(document.getElementById("line-chart"),{-->
<!---->
<!--  data: {-->
<!--            labels: [1500,1600,1700,1750,1800,1850,1900,1950,1999,2050],-->
<!--    datasets: [{-->
<!--                data: [86,114,106,106,107,111,133,221,783,2478],-->
<!--        label: "Africa",-->
<!--        borderColor: "#3e95cd",-->
<!--        fill: false-->
<!--      }, {-->
<!--                data: [282,350,411,502,635,809,947,1402,3700,5267],-->
<!--        label: "Asia",-->
<!--        borderColor: "#8e5ea2",-->
<!--        fill: false-->
<!--      }, {-->
<!--                data: [168,170,178,190,203,276,408,547,675,734],-->
<!--        label: "Europe",-->
<!--        borderColor: "#3cba9f",-->
<!--        fill: false-->
<!--      }, {-->
<!--                data: [40,20,10,16,24,38,74,167,508,784],-->
<!--        label: "Latin America",-->
<!--        borderColor: "#e8c3b9",-->
<!--        fill: false-->
<!--      }, {-->
<!--                data: [6,3,2,2,7,26,82,172,312,433],-->
<!--        label: "North America",-->
<!--        borderColor: "#c45850",-->
<!--        fill: false-->
<!--      }-->
<!--    ]-->
<!--  },-->
<!--  options: {-->
<!--            title: {-->
<!--                display: true,-->
<!--      text: 'World population per region (in millions)'-->
<!--    }-->
<!--        }-->
<!--};-->
<!---->
<!--    </script>-->





<!--        <!DOC<!--TYPE HTML>-->
<!--<html>-->
<!--<head>-->
<!--  <script type="text/javascript">-->
<!--    window.onload = function () {-->
<!--        var chart = new CanvasJS.Chart("chartContainer",-->
<!--    {-->

<!--      title:{-->
<!--            text: "Multi-Series Line Chart"-->
<!--      },-->
<!--      data: [-->
<!--      {-->
<!--          type: "line",-->
<!--        dataPoints: [-->
<!--        { label: "Jan", y: 21 },-->
<!--        { label: "Feb", y: 25 },-->
<!--        { label: "March", y: 20 },-->
<!--        { label: "April", y: 25 }-->
<!--        // { x: 50, y: 27 },-->
<!--        // { x: 60, y: 28 },-->
<!--        // { x: 70, y: 28 },-->
<!--        // { x: 80, y: 24 },-->
<!--        // { x: 90, y: 26 }-->
<!---->
<!--        ]-->
<!--      }/*,-->
<!--        {-->
<!--            type: "line",-->
<!--        dataPoints: [-->
<!--        { x: 10, y: 31 },-->
<!--        { x: 20, y: 35},-->
<!--        { x: 30, y: 30 },-->
<!--        { x: 40, y: 35 },-->
<!--        { x: 50, y: 35 },-->
<!--        { x: 60, y: 38 },-->
<!--        { x: 70, y: 38 },-->
<!--        { x: 80, y: 34 },-->
<!--        { x: 90, y: 44}-->
<!---->
<!--        ]-->
<!--      },-->
<!--        {type: "line",-->
<!--        dataPoints: [-->
<!--        { x: 10, y: 45 },-->
<!--        { x: 20, y: 50},-->
<!--        { x: 30, y: 40 },-->
<!--        { x: 40, y: 45 },-->
<!--        { x: 50, y: 45 },-->
<!--        { x: 60, y: 48 },-->
<!--        { x: 70, y: 43 },-->
<!--        { x: 80, y: 41 },-->
<!--        { x: 90, y: 28}-->
<!---->
<!--        ]-->
<!--      },-->
<!--        {-->
<!--        type: "line",-->
<!--        dataPoints: [-->
<!--        { x: 10, y: 71 },-->
<!--        { x: 20, y: 55},-->
<!--        { x: 30, y: 50 },-->
<!--        { x: 40, y: 65 },-->
<!--        { x: 50, y: 95 },-->
<!--        { x: 60, y: 68 },-->
<!--        { x: 70, y: 28 },-->
<!--        { x: 80, y: 34 },-->
<!--        { x: 90, y: 14}-->
<!---->
<!--        ]-->
<!--      }*/-->
<!--      ]-->
<!--    });-->
<!---->
<!--    chart.render();-->
<!--  }-->
<!--  </script>-->
<!-- <script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script></head>-->
<!--<body>-->
<!--  <div id="chartContainer" style="height: 300px; width: 100%;">-->
<!--  </div>-->
<!--</body>-->
<!--</html>-->

     <?php
















// echo"<h5 class='description-header'>".number_format(abs($data[2]))."</h5>";
        //  echo"<span class='description-text'>".$string[2]."</span>";
//               echo"<span class='description-text'>INCOME</span>";
        echo "</div>";//<!-- /.description-block -->
        echo "</div>";//<!-- /.col -->
        echo "<div class='col-sm-3 col-xs-6'>";
        echo "       <div class='description-block border-right'>";
        //  echo"         <span class='description-percentage text-yellow'><i class='fa fa-caret-left'></i> 0%</span>";
//               echo"         <h5 class='description-header'>".number_format(abs($data[3]))."</h5>";
        //       echo"         <span class='description-text'>".$string[2]."</span>";
//               echo"<span class='description-text'>EXPENSE</span>";

        echo "       </div>";//<!-- /.description-block -->
        echo "</div>";//<!-- /.col -->

        echo "<div class='col-sm-3 col-xs-6'>";
        echo "        <div class='description-block border-right'>";
        //  echo"          <span class='description-percentage text-green'><i class='fa fa-caret-up'></i> 20%</span>";
        $totalreturn = (-$data[2] - $data[3]);
//               echo"         <h5 class='description-header'>".number_format(abs($totalreturn))."</h5>";

//               echo"         <h5 class='description-header'>".number_format($totalreturn)."</h5>";
        if ($totalreturn > 0) {
//               echo"         <span class='description-text'>NET PROFIT</span>";
        } else {
//               echo"         <span class='description-text'>NET LOSS</span>";
        }
        echo "       </div>";//<!-- /.description-block -->
        echo "</div>";//<!-- /.col -->

        echo "<div class='col-sm-3 col-xs-6'>";
        echo "        <div class='description-block'>";
        //  echo"          <span class='description-percentage text-red'><i class='fa fa-caret-down'></i> 18%</span>";

        $gp_percent = ($totalreturn / -$data[2] * 100);
//              echo"          <h5 class='description-header'>".number_format(abs($gp_percent),2)."%</h5>";
//              echo"          <h5 class='description-header'>".number_format($gp_percent,2)."%</h5>";

//              echo"          <span class='description-text'>PERCENTAGE</span>";
        echo "        </div>";//<!-- /.description-block -->
        echo " </div>";
        echo "</div>";// <!-- /.row -->
        echo "</div>";//<!-- /.box-footer -->

        echo "</div>";//<!-- /.box -->
        echo "</div>";

    }
}
$_IE = new IncomeAndExpences();
//
$_IE->IEwidget();







