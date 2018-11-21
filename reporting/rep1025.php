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

function getTransactions($group_id, $yr, $mo)
{
//    $end = date2sql(end_fiscalyear());
//    $yr = date('Y', strtotime($end));
    //current
    $date13 = date('Y-m-d',mktime(0,0,0,$mo+1,1,$yr));
    $date12 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));
    $date11 = date('Y-m-d',mktime(0,0,0,$mo-1,1,$yr));
    $date10 = date('Y-m-d',mktime(0,0,0,$mo-2,1,$yr));
    $date09 = date('Y-m-d',mktime(0,0,0,$mo-3,1,$yr));
    $date08 = date('Y-m-d',mktime(0,0,0,$mo-4,1,$yr));
    $date07 = date('Y-m-d',mktime(0,0,0,$mo-5,1,$yr));
    $date06 = date('Y-m-d',mktime(0,0,0,$mo-6,1,$yr));
    $date05 = date('Y-m-d',mktime(0,0,0,$mo-7,1,$yr));
    $date04 = date('Y-m-d',mktime(0,0,0,$mo-8,1,$yr));
    $date03 = date('Y-m-d',mktime(0,0,0,$mo-9,1,$yr));
    $date02 = date('Y-m-d',mktime(0,0,0,$mo-10,1,$yr));
    $date01 = date('Y-m-d',mktime(0,0,0,$mo-11,1,$yr));

      ////end current -4


    $sql = "SELECT
SUM(CASE WHEN trans.tran_date >= '$date01' AND trans.tran_date < '$date02' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd1,
SUM(CASE WHEN trans.tran_date >= '$date02' AND trans.tran_date < '$date03' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd2,
SUM(CASE WHEN trans.tran_date >= '$date03' AND trans.tran_date < '$date04' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd3,
SUM(CASE WHEN trans.tran_date >= '$date04' AND trans.tran_date < '$date05' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd4,
SUM(CASE WHEN trans.tran_date >= '$date05' AND trans.tran_date < '$date06' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd5,
SUM(CASE WHEN trans.tran_date >= '$date06' AND trans.tran_date < '$date07' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd6,
SUM(CASE WHEN trans.tran_date >= '$date07' AND trans.tran_date < '$date08' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd7,
SUM(CASE WHEN trans.tran_date >= '$date08' AND trans.tran_date < '$date09' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd8,
SUM(CASE WHEN trans.tran_date >= '$date09' AND trans.tran_date < '$date10' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd9,
SUM(CASE WHEN trans.tran_date >= '$date10' AND trans.tran_date < '$date11' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd10,
SUM(CASE WHEN trans.tran_date >= '$date11' AND trans.tran_date < '$date12' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd11,
SUM(CASE WHEN trans.tran_date >= '$date12' AND trans.tran_date < '$date13' 
THEN (ov_amount + ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ ov_freight + ov_freight_tax + ov_discount - discount1 - discount2) ELSE 0 END) AS prd12

FROM ".TB_PREF."debtor_trans trans
					LEFT JOIN ".TB_PREF."cust_branch branch ON trans.debtor_no = branch.debtor_no

		WHERE trans.type = 10
		AND branch.group_no =  " . db_escape($group_id);
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

function get_groups()
{
    $sql ="SELECT * FROM ".TB_PREF."groups WHERE id!=0";
    $result =  db_query($sql,"No transactions were returned");
    return $result;
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
    $year = $_POST['PARAM_1'];
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
//     $myrow2 = get_current_fiscalyear();

    $cols[0]    = 0;
    $headers[0] = _("Month");
    $aligns[0]  = 'left';


    $sql = "SELECT begin, end, YEAR(end) AS yr, MONTH(end) AS mo FROM ".TB_PREF."fiscal_year WHERE id=".db_escape($year);
    $result = db_query($sql, "could not get fiscal year");
    $myrow2 = db_fetch($result);
//    $year2 = date("Y", strtotime($myrow2["end"]));
    $year = sql2date($myrow2['begin'])." - ".sql2date($myrow2['end']);
    $yr = $myrow2['yr'];
    $mo = $myrow2['mo'];
    $da = 1;

    $per12 = strftime('%b',mktime(0,0,0,$mo,$da,$yr));
    $per11 = strftime('%b',mktime(0,0,0,$mo-1,$da,$yr));
    $per10 = strftime('%b',mktime(0,0,0,$mo-2,$da,$yr));
    $per09 = strftime('%b',mktime(0,0,0,$mo-3,$da,$yr));
    $per08 = strftime('%b',mktime(0,0,0,$mo-4,$da,$yr));
    $per07 = strftime('%b',mktime(0,0,0,$mo-5,$da,$yr));
    $per06 = strftime('%b',mktime(0,0,0,$mo-6,$da,$yr));
    $per05 = strftime('%b',mktime(0,0,0,$mo-7,$da,$yr));
    $per04 = strftime('%b',mktime(0,0,0,$mo-8,$da,$yr));
    $per03 = strftime('%b',mktime(0,0,0,$mo-9,$da,$yr));
    $per02 = strftime('%b',mktime(0,0,0,$mo-10,$da,$yr));
    $per01 = strftime('%b',mktime(0,0,0,$mo-11,$da,$yr));

        for($i=0; $i <= 5; $i++)
        {
            $year = date("Y-m-d", strtotime($myrow2["end"]));
            $lastyear = strtotime("-".$i." year", strtotime($year));
            $var = date("Y", $lastyear);
            $stock_id[$i] = $var;
        }

    $cols    = array(0, 50,80,120, 160, 200,  240, 280,320,360, 400, 440,  480, 520);
    $headers = array(_('Groups'), _("$per01"), _("$per02"),  _("$per03"),  _("$per04"), _("$per05"),  _("$per06"), _("$per07"), _("$per08"),
        _("$per09"),  _("$per10"), _("$per11"),  _("$per12"));
    $aligns = array('left',	'right', 'right','right','right', 'right', 'right', 'right', 'right','right','right', 'right',  'right');

	if ($fromcust != '')
		$headers[2] = '';

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'),'from' => $from, 'to' => $to),
    				    2 => array('text' => _('Type'), 'from' => $show_type, 'to' => ''),
    				    3 => array('text' => _('Location'), 'from' => $loc, 'to' => ''),
    				    4 => array('text' => _('Customer'), 'from' => $fromc, 'to' => ''));

    $rep = new FrontReport(_('Yearly / Monthly Comparision'), "YearlyMonthly Comparision", user_pagesize(), 8, $orientation);
   	if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();
    $res2 = get_groups();

    while ($myrow2=db_fetch($res2)) {
        $rep->TextCol(0, 1, $myrow2['description'], $dec);

        $res = getTransactions($myrow2['id'], $yr, $mo);

        while ($myrow = db_fetch($res)) {
//            $j_tot = $myrow['prd1'] + $myrow['prd13'] + $myrow['prd25'] + $myrow['prd37'] + $myrow['prd49'];
//            $f_tot = $myrow['prd2'] + $myrow['prd14'] + $myrow['prd26'] + $myrow['prd38'] + $myrow['prd50'];
//            $m_tot = $myrow['prd3'] + $myrow['prd15'] + $myrow['prd27'] + $myrow['prd39'] + $myrow['prd51'];
//            $a_tot = $myrow['prd4'] + $myrow['prd16'] + $myrow['prd28'] + $myrow['prd40'] + $myrow['prd52'];
//            $ma_tot = $myrow['prd5'] + $myrow['prd17'] + $myrow['prd29'] + $myrow['prd41'] + $myrow['prd53'];
//            $jun_tot = $myrow['prd6'] + $myrow['prd18'] + $myrow['prd30'] + $myrow['prd42'] + $myrow['prd54'];
//            $jul_tot = $myrow['prd7'] + $myrow['prd19'] + $myrow['prd31'] + $myrow['prd43'] + $myrow['prd55'];
//            $au_tot = $myrow['prd8'] + $myrow['prd20'] + $myrow['prd32'] + $myrow['prd44'] + $myrow['prd56'];
//            $s_tot = $myrow['prd9'] + $myrow['prd21'] + $myrow['prd33'] + $myrow['prd45'] + $myrow['prd57'];
//            $o_tot = $myrow['prd10'] + $myrow['prd22'] + $myrow['prd34'] + $myrow['prd46'] + $myrow['prd58'];
//            $n_tot = $myrow['prd11'] + $myrow['prd23'] + $myrow['prd35'] + $myrow['prd47'] + $myrow['prd59'];
//            $d_tot = $myrow['prd12'] + $myrow['prd24'] + $myrow['prd36'] + $myrow['prd48'] + $myrow['prd60'];
//            $g_tot = $j_tot + $f_tot + $m_tot + $a_tot + $ma_tot + $jun_tot + $jul_tot + $au_tot + $s_tot + $o_tot + $n_tot + $d_tot;
            $c = 1;

//            for ($i = 0; $i < 12; $i++)
//            {
//                $rep->TextCol($c++, $c, abs($myrow['prd1']));
//            }

            $balance = array(1 => $myrow['prd1'], $myrow['prd2'], $myrow['prd3'], $myrow['prd4'],
                $myrow['prd5'], $myrow['prd6'], $myrow['prd7'], $myrow['prd8'],
                $myrow['prd9'], $myrow['prd10'], $myrow['prd11'], $myrow['prd12']);
//            $rep->TextCol(0, 1,	$account['account_code']);
//            $rep->TextCol(1, 2,	$account['account_name']);

            for ($i = 1; $i <= 12; $i++)
            {
                $rep->AmountCol($c++, $c, $balance[$i], $dec);
            }

//        $rep->TextCol(0, 1, _("January"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd1']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd13']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd25']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd37']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd49']), $dec);
//        $rep->AmountCol(6, 7, abs($j_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("Febuary"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd2']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd14']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd26']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd38']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd50']), $dec);
//        $rep->AmountCol(6, 7, abs($f_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("March"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd3']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd15']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd27']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd39']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd51']), $dec);
//        $rep->AmountCol(6, 7, abs($m_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("April"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd4']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd16']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd28']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd40']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd52']), $dec);
//        $rep->AmountCol(6, 7, abs($a_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("May"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd5']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd17']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd29']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd41']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd53']), $dec);
//        $rep->AmountCol(6, 7, abs($ma_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("June"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd6']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd18']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd30']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd42']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd54']), $dec);
//        $rep->AmountCol(6, 7, abs($jun_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("July"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd7']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd19']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd31']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd43']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd55']), $dec);
//        $rep->AmountCol(6, 7, abs($jul_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("August"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd8']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd20']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd32']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd44']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd56']), $dec);
//        $rep->AmountCol(6, 7, abs($au_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("September"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd9']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd21']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd33']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd45']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd57']), $dec);
//        $rep->AmountCol(6, 7, abs($s_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("October"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd10']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd22']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd34']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd46']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd58']), $dec);
//        $rep->AmountCol(6, 7, abs($o_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("November"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd11']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd23']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd35']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd47']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd59']), $dec);
//        $rep->AmountCol(6, 7, abs($n_tot), $dec);

//        $rep->NewLine();
//        $rep->TextCol(0, 1, _("December"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd12']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd24']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd36']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd48']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd60']), $dec);
//        $rep->AmountCol(6, 7, abs($d_tot), $dec);

//        $rep->NewLine();
//        $rep->NewLine();
//        $rep->Font(b);
//        $rep->TextCol(0, 1, _("Total"), $dec);
//        $rep->AmountCol(1, 2, abs($myrow['prd1']) + abs($myrow['prd2']) + abs($myrow['prd3']) + abs($myrow['prd4']) + abs($myrow['prd5']) + abs($myrow['prd6']) + abs($myrow['prd7']) + abs($myrow['prd8']) + abs($myrow['prd9']) + abs($myrow['prd10']) + abs($myrow['prd11']) + abs($myrow['prd12']), $dec);
//        $rep->AmountCol(2, 3, abs($myrow['prd13']) + abs($myrow['prd14']) + abs($myrow['prd15']) + abs($myrow['prd16']) + abs($myrow['prd17']) + abs($myrow['prd18']) +  abs($myrow['prd19']) + abs($myrow['prd20']) + abs($myrow['prd21']) + abs($myrow['prd22']) + abs($myrow['prd23']) + abs($myrow['prd24']), $dec);
//        $rep->AmountCol(3, 4, abs($myrow['prd25']) + abs($myrow['prd26']) + abs($myrow['prd27']) + abs($myrow['prd28']) + abs($myrow['prd29']) + abs($myrow['prd30']) +  abs($myrow['prd31']) + abs($myrow['prd32']) + abs($myrow['prd33']) + abs($myrow['prd34']) + abs($myrow['prd35']) + abs($myrow['prd36']), $dec);
//        $rep->AmountCol(4, 5, abs($myrow['prd37']) + abs($myrow['prd38']) + abs($myrow['prd39']) + abs($myrow['prd40']) + abs($myrow['prd41']) + abs($myrow['prd42']) +  abs($myrow['prd43']) + abs($myrow['prd44']) + abs($myrow['prd45']) + abs($myrow['prd46']) + abs($myrow['prd47']) + abs($myrow['prd48']), $dec);
//        $rep->AmountCol(5, 6, abs($myrow['prd49']) + abs($myrow['prd50']) + abs($myrow['prd51']) + abs($myrow['prd52']) + abs($myrow['prd53']) + abs($myrow['prd54']) +  abs($myrow['prd55']) + abs($myrow['prd56']) + abs($myrow['prd57']) + abs($myrow['prd58']) + abs($myrow['prd59']) + abs($myrow['pr60']), $dec);
//        $rep->AmountCol(6, 7, abs($g_tot), $dec);

            $rep->Font();
//            $rep->NewLine();
        }
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