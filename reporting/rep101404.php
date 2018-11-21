<?php
$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Summary
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_customer_analysis();

//----------------------------------------------------------------------------------------------------
function get_sales($type,$start_date,$end_date)
{
    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
            + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS sales,d.debtor_no, d.name
            FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d
            WHERE trans.debtor_no=d.debtor_no
            AND (trans.type = $type )
            AND tran_date >= '$start_date' AND tran_date <= '$end_date' ";


    $salesresult = db_query($sql);
    $salesmyrow = db_fetch($salesresult);
    $salesmyrow = $salesmyrow['sales'];;

    if($salesmyrow > 0)
    {
        return $salesmyrow;
    }
    else
    {
        return $salesmyrow = 0;
    }

}

function recovery($from,$to)
{
    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS recovery
	FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d 
	WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_CUSTPAYMENT." OR trans.type = ".ST_CRV.")
		AND tran_date >= '$from'
		AND tran_date <= '$to'";
    $recoveryresult_e = db_query($sql);
    $recoverymyrow_e = db_fetch($recoveryresult_e);

    if($recoverymyrow_e['recovery'] =='' )
        return 0;
    else
        return $recoverymyrow_e['recovery'] ;
}
function get_expence($from,$to)
{
    $sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,
      ".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype IN (5,6)
AND  ".TB_PREF."gl_trans.tran_date >= '$from'
AND  ".TB_PREF."gl_trans.tran_date <= '$to'
";
    $profitresult = db_query($sql);
    $profitmyrow = db_fetch($profitresult);

    if($profitmyrow['t_amount']=='' )
        return 0;
    else
        return $profitmyrow['t_amount'] ;
}
function get_customer_balance($to)
{
    //This is for recieveable

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
     	( (t.ov_amount*rate) + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";

    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
     	((t.ov_amount*rate) + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * (
     	    IF (type=".ST_JOURNAL." && ov_amount < 0, -1, 1)
     	    ), 0)) AS credits
     	    
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.type <> ".ST_CUSTDELIVERY."

    	AND t.tran_date <= '$to'   ";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
function inventory_sales($from,$to)
{
    $total =0;
    $sql="SELECT 
            SUM(-(0_stock_moves.qty*0_stock_moves.price)-(0_stock_moves.discount_percent)) AS amt 
           FROM 0_stock_master, 0_stock_category, 0_debtor_trans, 0_debtors_master, 0_cust_branch, 0_salesman, 
           0_stock_moves WHERE 0_stock_master.stock_id=0_stock_moves.stock_id 
           AND 0_stock_master.category_id=0_stock_category.category_id 
           AND 0_debtor_trans.debtor_no=0_debtors_master.debtor_no AND 0_debtors_master.debtor_no=0_cust_branch.debtor_no 
           AND 0_cust_branch.salesman=0_salesman.salesman_code AND 0_stock_moves.type=0_debtor_trans.type 
           AND 0_stock_moves.trans_no=0_debtor_trans.trans_no 
           AND 0_stock_moves.tran_date>='$from' 
           AND 0_stock_moves.tran_date<='$to' 
           AND (0_debtor_trans.type=13 OR 0_stock_moves.type=11)
          AND (0_stock_master.mb_flag='B' OR 0_stock_master.mb_flag='M') 
          GROUP BY 0_debtors_master.name ORDER BY 0_debtors_master.name";

    $result = db_query($sql,"No transactions were returned");


    while($dt = db_fetch($result))
    {
        $total += $dt['amt'];
    }

    return $total;
}
function get_investment_amount()
{
    $sql = "SELECT SUM(trans.`amount`) FROM `0_gl_trans` trans,0_chart_master chart , 0_chart_types c_type  
            WHERE 
            trans.`account` = chart.`account_code`
            AND chart.`account_type` = c_type.id
            AND c_type.`id`=22 AND trans.`type`=0;";
    $result = db_query($sql,"No transactions were returned");
    $dt = db_fetch($result);
    return $dt[0];
}


function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array;

    $to = $_POST['PARAM_0'];
    $group_by = $_POST['PARAM_1'];
    $fromcust = $_POST['PARAM_2'];
    $dimension = $_POST['PARAM_3'];
    $area = $_POST['PARAM_4'];
    $folk = $_POST['PARAM_5'];
    $currency = $_POST['PARAM_6'];
    //$show_all = $_POST['PARAM_5'];
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

    $rep = new FrontReport(_(' '), "CustomerBalancesSummary", user_pagesize(), 9, $orientation,null,null,1);

    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params="", $cols="", $headers="", $aligns="");
    $rep->NewPage();

    $rep->SetFontSize(16);
    $rep->Font('b');
    $rep->MultiCell(250,30,"ITMEDVISION HEALTH CARE",0,'L', 0, 2,180,100,true);
    $rep->Font();
    $rep->SetFontSize(12);
    $rep->MultiCell(250,20,"Monthly Summary Report",0,'L', 0, 2,220,125,true);
    $rep->SetFontSize(9);
//    $from = Today();
//    $to = Today();

    $today = Today();
    $begin = begin_fiscalyear();
    $begin1 = date2sql($begin);
    $today_date = date2sql($today);

    $mo = date('m',strtotime($today_date));
    $yr = date('Y',strtotime($today_date));
    $mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);

    $last_day_date =  date('Y-m-d', strtotime('-1 day', strtotime($today_date)));

    $start_date_of_this_moth = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));

    if($mon_days==30)
        $end_date_of_this_month = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
    else
        $end_date_of_this_month = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));



    //start and Date for previous month
    $total_no_days_pre_month = cal_days_in_month(CAL_GREGORIAN,$mo-1,$yr);
    $start_date_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,1,$yr));
    if($total_no_days_pre_month==30)
        $date_end_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,30,$yr));
    else
        $date_end_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,31,$yr));



    $Current_month_balance = get_customer_balance($end_date_of_this_month);
    $current_month_recovery = recovery($start_date_of_this_moth,$end_date_of_this_month);
    $current_month_expense = get_expence($start_date_of_this_moth,$end_date_of_this_month);
    $previous_market_balance = get_customer_balance($date_end_of_previous_month);
    $last_month_inventory_amount=inventory_sales($start_date_of_previous_month,$date_end_of_previous_month);
    $this_month_inventory_sales=inventory_sales($start_date_of_this_moth,$end_date_of_this_month);
    $current_month_inventory_value = $last_month_inventory_amount - $this_month_inventory_sales;
    $payment_to_transfer= 0.00;
    $current_month_investment= get_investment_amount();


//    $rep->Line($rep->row - 22);

    $rep->MultiCell(350,20,"",1,'L', 0, 2,120,120,true); // 1st Box
    $rep->NewLine();
    $rep->MultiCell(500,110,"",1,'L', 0, 2,56,150,true); // 2nd Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,290,true); // 3rd Box
//    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,234,true); // 4th Box
//    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,256,true); // 5th Box
//    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,277,true); // 6th Box
//    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,300,true); // 7th Box

    $rep->MultiCell(250,30,"Previous Market  Balance :",0,'L', 0, 2,60,155,true);
    $rep->MultiCell(250,30,number_format2($previous_market_balance[0],2),0,'R', 0, 2,200,155,true);

    $rep->MultiCell(250,30,"Current Month Balance :",0,'L', 0, 2,60,170,true);
    $rep->MultiCell(250,30,number_format2($Current_month_balance[0],2),0,'R', 0, 2,200,170,true);

    $rep->MultiCell(250,30,"Current Month Recovery : ",0,'L', 0, 2,60,185,true);
    $rep->MultiCell(250,30,number_format2($current_month_recovery,2),0,'R', 0, 2,200,185,true);


    $rep->MultiCell(250,30,"Current Month Expense :",0,'L', 0, 2,60,200,true);
    $rep->MultiCell(250,30,number_format2($current_month_expense,2),0,'R', 0, 2,200,200,true);

    $rep->MultiCell(250,30,"Payment To Tranfer:",0,'L', 0, 2,60,215,true);
    $rep->MultiCell(250,30,number_format2($payment_to_transfer,2),0,'R', 0, 2,200,215,true);

    $rep->MultiCell(250,30,"Current Month Investment :",0,'L', 0, 2,60,229,true);
    $rep->MultiCell(250,30,number_format2($current_month_investment,2),0,'R', 0, 2,200,229,true);


    $rep->MultiCell(250,30,"Current Month Inventory Amount Value :",0,'L', 0, 2,60,244,true);
    $rep->MultiCell(250,30,number_format2($current_month_inventory_value,2),0,'R', 0, 2,200,244,true);


    $rep->SetFontSize(12);
    $rep->MultiCell(250,20,"Current Month Market Balance",0,'L', 0, 2,206,275,true);
    $rep->SetFontSize(9);
    $rep->MultiCell(250,30,number_format2($Current_month_balance[0],2),0,'R', 0, 2,50,297,true);


    $rep->SetFontSize(12);
    $rep->MultiCell(350,20,"__________________________",0,'L', 0, 2,60,375,true);
    $rep->SetFontSize(9);
    $rep->MultiCell(250,20,"Checked By FURUKH",0,'L', 0, 2,85,394,true);


    $rep->SetFontSize(12);
    $rep->MultiCell(350,20,"__________________________",0,'L', 0, 2,340,375,true);
    $rep->SetFontSize(9);
    $rep->MultiCell(250,20,"Checked By ZEESHAN",0,'L', 0, 2,375,394,true);


    $rep->MultiCell(250,20,"CC : DR. PROF. DR.AKMAL WAHEED",0,'L', 0, 2,60,454,true);
    $rep->MultiCell(250,20,"CC : MIAN MUHAMMED AHMED",0,'L', 0, 2,60,474,true);



    //    $rep->MultiCell(250,30,"Total Cheque Amount :",0,'L', 0, 2,130,217,true);
//    $rep->MultiCell(250,30,"Cash Recieved Amount :",0,'L', 0, 2,130,239,true);
//    $rep->MultiCell(250,30, number_format2($cash_recieved_amount,2),0,'R', 0, 2,200,240,true);
//
//    $rep->MultiCell(250,30,"Cheque Recieved Amount :",0,'L', 0, 2,130,260,true);
//    $rep->MultiCell(250,30, number_format2($cheque_recieved_amount,2),0,'R', 0, 2,200,260,true);
//
//
//
//    $rep->MultiCell(250,30,"Online Recieved Amount :",0,'L', 0, 2,130,283,true);
//
//    $rep->MultiCell(250,30,"Total Return Amount :",0,'L', 0, 2,130,305,true);
//    $rep->MultiCell(250,30, number_format2($total_return_amount,2),0,'R', 0, 2,200,305,true);


    $rep->NewLine();
    $rep->End();
}

?>