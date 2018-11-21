<?php

$page_security = 'SA_INCOME2';
global $path_to_root;
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/dashboard/charts/charts_utils.php");

//class IncomeAndExpences
//{
//    public function IEwidget()
//    {

        $begin = begin_fiscalyear();
        $f_year = get_current_fiscalyear();
        $month = $_POST['vale'];
        $mo = $month;
//        display_error($mo);

        if($_POST['month_']=7 || $_POST['month_']=8 || $_POST['month_']=9 || $_POST['month_']=10 || $_POST['month_']=11 || $_POST['month_']=12)
        {
            $yr = date('Y', strtotime($f_year['end']));
        }
        if($_POST['month_']=1 || $_POST['month_']=2 || $_POST['month_']=3 || $_POST['month_']=4 || $_POST['month_']=5 || $_POST['month_']=6)
        {
            $yr = date('Y', strtotime($f_year['begin']));
        }

        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        //$mo = date("m", strtotime($begin1));
        //$yr = date("Y", strtotime($begin1));
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

        //FOR MONTHLY GRAPH

        //return db_fetch($result2);
        //var_dump($date13);

        ////////////////////////Today
//        $month = date('M', strtotime($today));
//        $begin = begin_fiscalyear();
//        $f_year = get_current_fiscalyear();
//        $mo=$_POST['month_'];

        if($_POST['month_']=7 || $_POST['month_']=8 || $_POST['month_']=9 || $_POST['month_']=10 || $_POST['month_']=11 || $_POST['month_']=12)
        {
            $yr = date('Y', strtotime($f_year['end']));
        }
        if($_POST['month_']=1 || $_POST['month_']=2 || $_POST['month_']=3 || $_POST['month_']=4 || $_POST['month_']=5 || $_POST['month_']=6)
        {
            $yr = date('Y', strtotime($f_year['begin']));
        }

        //$yr = date('Y', strtotime($today));
//        $mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
        $show1  = date('d', mktime(0, 0, 0, $mo, 1, $yr));
        $show2  = date('d', mktime(0, 0, 0, $mo, 2, $yr));
        $show3  = date('d', mktime(0, 0, 0, $mo, 3, $yr));
        $show4  = date('d', mktime(0, 0, 0, $mo, 4, $yr));
        $show5  = date('d', mktime(0, 0, 0, $mo, 5, $yr));
        $show6  = date('d', mktime(0, 0, 0, $mo, 6, $yr));
        $show7  = date('d', mktime(0, 0, 0, $mo, 7, $yr));
        $show8  = date('d', mktime(0, 0, 0, $mo, 8, $yr));
        $show9  = date('d', mktime(0, 0, 0, $mo, 9, $yr));
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
//        $mo =$_POST['month_'];// date('m', strtotime($today1));
//        display_error($mo."sdfyusdify");
        //$yr = date('Y', strtotime($today1));
        $date1  = date('Y-m-d', mktime(0, 0, 0, $mo, 1, $yr));
        $date2  = date('Y-m-d', mktime(0, 0, 0, $mo, 2, $yr));
        $date3  = date('Y-m-d', mktime(0, 0, 0, $mo, 3, $yr));
        $date4  = date('Y-m-d', mktime(0, 0, 0, $mo, 4, $yr));
        $date5  = date('Y-m-d', mktime(0, 0, 0, $mo, 5, $yr));
        $date6  = date('Y-m-d', mktime(0, 0, 0, $mo, 6, $yr));
        $date7  = date('Y-m-d', mktime(0, 0, 0, $mo, 7, $yr));
        $date8  = date('Y-m-d', mktime(0, 0, 0, $mo, 8, $yr));
        $date9  = date('Y-m-d', mktime(0, 0, 0, $mo, 9, $yr));
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
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per02,
SUM(CASE WHEN trans.tran_date = '$date3'  AND trans.tran_date < '$date4'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per30,
SUM(CASE WHEN trans.tran_date = '$date31'
THEN ((trans.ov_amount + trans.ov_gst +  trans.ov_discount + trans.gst_wh + trans.supply_disc + 
trans.service_disc + trans.fbr_disc + trans.srb_disc) * rate ) ELSE 0 END) AS per31
		FROM
			" . TB_PREF . "supp_trans trans
		WHERE  trans.type IN(22,1,41)

		";
        $result = db_query($sql, "Transactions could not be calculated");

////////Purchasing
        $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date1' AND trans.tran_date < '$date2'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver01,
SUM(CASE WHEN trans.tran_date >= '$date2' AND trans.tran_date < '$date3'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver02,
SUM(CASE WHEN trans.tran_date = '$date3' AND trans.tran_date < '$date4'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver03,
SUM(CASE WHEN trans.tran_date >= '$date4' AND trans.tran_date < '$date5'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver04,
SUM(CASE WHEN trans.tran_date >= '$date5' AND trans.tran_date < '$date6'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver05,
SUM(CASE WHEN trans.tran_date >= '$date6' AND trans.tran_date < '$date7'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver06,
SUM(CASE WHEN trans.tran_date >= '$date7' AND trans.tran_date < '$date8'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver07,
SUM(CASE WHEN trans.tran_date >= '$date8' AND trans.tran_date < '$date9'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver08,
SUM(CASE WHEN trans.tran_date >= '$date9' AND trans.tran_date < '$date10'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver09,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver12,
SUM(CASE WHEN trans.tran_date >= '$date13' AND trans.tran_date < '$date14'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver13,
SUM(CASE WHEN trans.tran_date >= '$date14' AND trans.tran_date < '$date15'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver14,
SUM(CASE WHEN trans.tran_date >= '$date15' AND trans.tran_date < '$date16'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver15,
SUM(CASE WHEN trans.tran_date >= '$date16' AND trans.tran_date < '$date17'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver16,
SUM(CASE WHEN trans.tran_date >= '$date17' AND trans.tran_date < '$date18'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver17,
SUM(CASE WHEN trans.tran_date >= '$date18' AND trans.tran_date < '$date19'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver18,
SUM(CASE WHEN trans.tran_date >= '$date19' AND trans.tran_date < '$date20'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver19,
SUM(CASE WHEN trans.tran_date >= '$date20' AND trans.tran_date < '$date21'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver20,
SUM(CASE WHEN trans.tran_date >= '$date21' AND trans.tran_date < '$date22'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver21,
SUM(CASE WHEN trans.tran_date >= '$date22' AND trans.tran_date < '$date23'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver22,
SUM(CASE WHEN trans.tran_date >= '$date23' AND trans.tran_date < '$date24'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver23,
SUM(CASE WHEN trans.tran_date >= '$date24' AND trans.tran_date < '$date25'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver24,
SUM(CASE WHEN trans.tran_date >= '$date25' AND trans.tran_date < '$date26'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver25,
SUM(CASE WHEN trans.tran_date >= '$date26' AND trans.tran_date < '$date27'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver26,
SUM(CASE WHEN trans.tran_date >= '$date27' AND trans.tran_date < '$date28'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver27,
SUM(CASE WHEN trans.tran_date >= '$date28' AND trans.tran_date < '$date29'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver28,
SUM(CASE WHEN trans.tran_date >= '$date29' AND trans.tran_date < '$date30'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver29,
SUM(CASE WHEN trans.tran_date >= '$date30' AND trans.tran_date < '$date31'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver30,
SUM(CASE WHEN trans.tran_date = '$date31'
THEN ((trans.ov_amount) * trans.rate) ELSE 0 END) AS ver31
		FROM
			" . TB_PREF . "supp_trans trans
		WHERE  trans.type = 20

		";
        $result2 = db_query($sql, "Transactions could not be calculated");

        /// ////////////////////end
        echo '
        <canvas id="line-chart"  width="800" height="450" ;"></canvas>
        ';

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
        $k = 0;
        $data2 = array();
        $string2 = array();
        while ($myrow = db_fetch($result2)) {
            $ver01[$k] = $myrow['ver01'];
            $ver02[$k] = $myrow['ver02'];
            $ver03[$k] = $myrow['ver03'];
            $ver04[$k] = $myrow['ver04'];
            $ver05[$k] = $myrow['ver05'];
            $ver06[$k] = $myrow['ver06'];
            $ver07[$k] = $myrow['ver07'];
            $ver08[$k] = $myrow['ver08'];
            $ver09[$k] = $myrow['ver09'];
            $ver10[$k] = $myrow['ver10'];
            $ver11[$k] = $myrow['ver11'];
            $ver12[$k] = $myrow['ver12'];
            $ver13[$k] = $myrow['ver13'];
            $ver14[$k] = $myrow['ver14'];
            $ver15[$k] = $myrow['ver15'];
            $ver16[$k] = $myrow['ver16'];
            $ver17[$k] = $myrow['ver17'];
            $ver18[$k] = $myrow['ver18'];
            $ver19[$k] = $myrow['ver19'];
            $ver20[$k] = $myrow['ver20'];
            $ver21[$k] = $myrow['ver21'];
            $ver22[$k] = $myrow['ver22'];
            $ver23[$k] = $myrow['ver23'];
            $ver24[$k] = $myrow['ver24'];
            $ver25[$k] = $myrow['ver25'];
            $ver26[$k] = $myrow['ver26'];
            $ver27[$k] = $myrow['ver27'];
            $ver28[$k] = $myrow['ver28'];
            $ver29[$k] = $myrow['ver29'];
            $ver30[$k] = $myrow['ver30'];
            $ver31[$k] = $myrow['ver31'];
            $data2[$k] = $myrow['total'];
            $string2[$k] = $myrow['class_name'];
            $k++;;
        }


echo'<script>

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
        label: "Payment",
        borderColor: "#3e95cd",
        fill: false
      }, { 
       data: ['.$ver01[0].', '.$ver02[0].', '.$ver03[0].', '.$ver04[0].', '.$ver05[0].', 
         '.$ver06[0].', '.$ver07[0].', '.$ver08[0].', '.$ver09[0].', '.$ver10[0].', 
         '.$ver11[0].', '.$ver12[0].', '.$ver13[0].', '.$ver14[0].', '.$ver15[0].', 
         '.$ver16[0].', '.$ver17[0].', '.$ver18[0].', '.$ver19[0].', '.$ver20[0].', 
         '.$ver21[0].', '.$ver22[0].', '.$ver23[0].', '.$ver24[0].', '.$ver25[0].', 
         '.$ver26[0].', '.$ver27[0].', '.$ver28[0].', '.$ver29[0].', '.$ver30[0].', 
         '.$ver31[0].'],
        label: "Purchase",
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


</script>';



//
//
//    }
//}
//
//
//
//
//$_IE = new IncomeAndExpences();
//$_IE->IEwidget();



