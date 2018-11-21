<?php

class carosal_view
{
public function renderCarosal()
{
$path_to_root="././.";
?>


<style type="text/css">
    /*body{padding-top:20px;}*/
    .carousel {
        margin-bottom: 0;
        padding: 0 0 -10px 0;
        /*background-color: #00a157;*/
    }
    /* The controlsy */
    .carousel-control {
        left: -17px;
        height: 15px;
        width: 40px;
        /*background: none repeat scroll 0 0 #222222;*/
        /*border: 4px solid #FFFFFF;*/
        /*border-radius: 23px 23px 23px 23px;*/
        margin-top: 41px;
    }
    .carousel-control.right {
        right: -5px;
    }
    /* The indicators */
    .carousel-indicators {
        right: 30%;
        top: 10%;
        bottom: -10px;
        margin-right: -19px;
    }
    /* The colour of the indicators */
    .carousel-indicators li {
        background: #cecece;
    }
    .carousel-indicators .active {
        background: #428bca;
    }
</style>

<?php
$today = Today();
$begin = begin_fiscalyear();
$begin1 = date2sql($begin);
$today1 = date2sql($today);

$mo = date('m',strtotime($today1));
$yr = date('Y',strtotime($today1));
$mon_days=cal_days_in_month(CAL_GREGORIAN,$mo,$yr);

$date1 = date('Y-m-d',mktime(0,0,0,$mo,1,$yr));

if($mon_days==30)
    $date_end = date('Y-m-d',mktime(0,0,0,$mo,30,$yr));
else
    $date_end = date('Y-m-d',mktime(0,0,0,$mo,31,$yr));



//start and Date for previous month
$total_no_days_pre_month = cal_days_in_month(CAL_GREGORIAN,$mo-1,$yr);
$start_date_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,1,$yr));
if($total_no_days_pre_month==30)
    $date_end_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,30,$yr));
else
    $date_end_of_previous_month = date('Y-m-d',mktime(0,0,0,$mo-1,31,$yr));



//// Previous month income
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class
WHERE ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype = 4
AND  ".TB_PREF."gl_trans.tran_date >= '$start_date_of_previous_month'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end_of_previous_month'";
$incomeresult = db_query($sql);
$incomemyrow = db_fetch($incomeresult);
$total_income_of_previous_month = $incomemyrow['t_amount'];

////////For INCOME
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class
WHERE ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype = 4
AND  ".TB_PREF."gl_trans.tran_date >= '$date1'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end'";
$incomeresult = db_query($sql);
$incomemyrow = db_fetch($incomeresult);
$total_income = $incomemyrow['t_amount'];
$curr_m_reve = number_format(-1*$total_income);

///////For Expense
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,
      ".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype IN (5,6)
AND  ".TB_PREF."gl_trans.tran_date >= '$date1'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end'
";
$profitresult = db_query($sql);
$profitmyrow = db_fetch($profitresult);
$total_profit = $profitmyrow['t_amount'];

/////// Previous Month Expense
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype IN (5,6)
AND  ".TB_PREF."gl_trans.tran_date >= '$start_date_of_previous_month'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end_of_previous_month'
";
$profitresult = db_query($sql);
$profitmyrow = db_fetch($profitresult);
$total_expense_pre_month = $profitmyrow['t_amount'];


/// Net profit ---- Bank balance
$sql1 = "SELECT SUM(amount) As balance
		FROM ".TB_PREF."bank_trans,".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_trans.`bank_act`=".TB_PREF."bank_accounts.id
            AND trans_date <= '$date_end'";
$balanceresult_e = db_query($sql1);
$balancemyrow_e = db_fetch($balanceresult_e);
$total_b_rec = $balancemyrow_e['balance'];
//////////////////Net Revenue
$net_revenue=(-1*$total_income)-$total_profit;

/// Net profit For previos month ---- Bank balance
$sql1 = "SELECT SUM(amount) As balance
		FROM ".TB_PREF."bank_trans,".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_trans.`bank_act`=".TB_PREF."bank_accounts.id
            AND trans_date <= '$date_end_of_previous_month'";
$balanceresult_e = db_query($sql1);
$balancemyrow_e = db_fetch($balanceresult_e);
$total_b_rec = $balancemyrow_e['balance'];
//////////////////Net Revenue
$net_revenue=(-1*$total_income)-$total_profit;
$net_revenue_for_p_m = (-1* $total_income_of_previous_month) - $total_expense_pre_month ;

////customer balances
$sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS recovery
	FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d 
	WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_CUSTPAYMENT." OR trans.type = ".ST_CRV.")
		AND tran_date >= '$date1'
		AND tran_date <= '$date_end'";
$recoveryresult_e = db_query($sql);
$recoverymyrow_e = db_fetch($recoveryresult_e);
$total_c_rec = $recoverymyrow_e['recovery'];

////customer balances for Previous month
$sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS recovery
	FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d 
	WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_CUSTPAYMENT." OR trans.type = ".ST_CRV.")
		AND tran_date >= '$start_date_of_previous_month'
		AND tran_date <= '$date_end_of_previous_month'";
$recoveryresult_e = db_query($sql);
$recoverymyrow_e = db_fetch($recoveryresult_e);
$total_c_rec_for_p_month = $recoverymyrow_e['recovery'];

///Cash-bank balances
$sql1 = "SELECT SUM(amount) As balance
		FROM ".TB_PREF."bank_trans,".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_trans.`bank_act`=".TB_PREF."bank_accounts.id
            AND trans_date <= '$date_end'";
$balanceresult_e = db_query($sql1);
$balancemyrow_e = db_fetch($balanceresult_e);
$total_c_b_balance = $balancemyrow_e['balance'];

///Cash-bank balances for previous month
$sql1 = "SELECT SUM(amount) As balance
		FROM ".TB_PREF."bank_trans,".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_trans.`bank_act`=".TB_PREF."bank_accounts.id
            AND trans_date <= '$date_end_of_previous_month'";
$balanceresult_e = db_query($sql1);
$balancemyrow_e = db_fetch($balanceresult_e);
$total_c_b_balance_p_m = $balancemyrow_e['balance'];



///////For Current Month Equity
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount, class_name FROM ".TB_PREF."gl_trans,
      ".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype = '3'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end'
";
$equityresult = db_query($sql);
$equitymyrow = db_fetch($equityresult);
$total_equity = $equitymyrow['t_amount'];
$equity_name = $equitymyrow['class_name'];

/////// Previous Month Equity
$sql = " SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype = '3'
AND  ".TB_PREF."gl_trans.tran_date <= '$date_end_of_previous_month'
";
$equityresult = db_query($sql);
$equitymyrow = db_fetch($equityresult);
$total_equity_pre_month = $equitymyrow['t_amount'];



function get_open_balance_for_dashboard($to)
{

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
     	( (t.ov_amount*rate) + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";


    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
     	((t.ov_amount*rate) + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * (
     	    IF (type=".ST_JOURNAL." && ov_amount < 0, -1, 1)
     	    ), 0)) AS credits
     	    
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.type <> ".ST_CUSTDELIVERY."
    	
    	AND t.tran_date <= '$to'";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

$cust_balance_current = get_open_balance_for_dashboard($today1);

$total_amount_recieveable = ($cust_balance_current['charges'] - $cust_balance_current['credits']);

$cust_balance_previous = get_open_balance_for_dashboard($date_end_of_previous_month);

$total_amount_recieveable_for_pre = ($cust_balance_previous['charges'] - $cust_balance_previous['credits']);


///Supplier Payable ..
///
$sql3="SELECT  SUM((ov_amount + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc + srb_disc) ) ) AS TotalAmount
        FROM 0_supp_trans 
        WHERE  tran_date <= '".$today1."' 
        AND TYPE != 43 AND ov_amount!=0 ORDER BY tran_date";
        
// $sql3="SELECT 
//  SUM(IF(TYPE IN(20,2,42), ((ov_amount*rate) + ov_gst + ov_discount-alloc-(supply_disc + service_disc + fbr_disc + srb_disc)), 
//  ((ov_amount*rate) + ov_gst + ov_discount+ alloc-(supply_disc + service_disc + fbr_disc + srb_disc)))) AS OutStanding
//  FROM 0_supp_trans 
// WHERE tran_date <= '".$today1."'";

$result3 = db_query($sql3,"The client details could not be retrieved");
$supp_balance_total = db_fetch($result3,'can not fetch supplioer balances');
$total_amount_payable = (-1* $supp_balance_total[0]);

// payable for previous month
$sql1="SELECT  SUM((ov_amount + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc + srb_disc) ) ) AS TotalAmount
        FROM 0_supp_trans 
        WHERE  tran_date <= '".$date_end_of_previous_month."' 
        AND TYPE != 43 AND ov_amount!=0 ORDER BY tran_date";

// $sql1 = " SELECT
// 				SUM( (ov_amount * rate ) + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc +  srb_disc) ) AS TotalAmount
				
//   			FROM ".TB_PREF."supp_trans
//   			WHERE tran_date <='".$date_end_of_previous_month."' AND type != ".ST_SUPPCREDIT_IMPORT." AND ov_amount!=0
//     				ORDER BY tran_date";
$acc_p_result = db_query($sql1);
$acc_p_result = db_fetch($acc_p_result);
$total_pre_amount_payable = $acc_p_result['TotalAmount'];



?>
<div class="row">
    <div class="col-md-12">
        <div id="Carousel" class="carousel slide" data-ride="carousel">

            <!--                <ol class="carousel-indicators">-->
            <!--                    <li data-target="#Carousel" data-slide-to="0" class="active"></li>-->
            <!--                    <li data-target="#Carousel" data-slide-to="1"></li>-->
            <!--                    <li data-target="#Carousel" data-slide-to="2"></li>-->
            <!--                </ol>-->

            <!-- Carousel items -->
            <div class="carousel-inner">

                <div class="item active">
                    <div class="row">


                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./gl/inquiry/profit_loss.php?" target="_blank">
                                <div class="small-box bg-aqua">
                                    <div class="inner" >
                                        <?php
                                        if((-1 * $total_income_of_previous_month) > (-1*$total_income) ) {
                                            echo'<img src = "./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height="33px" width="23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        ?>
                                        <p style="font-size: 18px;margin-left: 38px;">Current Month Revenue</p>

                                        <p style="font-size: 18px;color: white;margin-top:-14px;margin-left: 38px; "> <?php echo number_format(-1*$total_income) ?></p></td>
                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format((-1 * $total_income_of_previous_month))?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                  <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i class="ion ion-stats-bars"></i>
                                    </div>

                                </div></a>
                        </div>

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./gl/inquiry/profit_loss.php?" target="_blank">
                                <div class="small-box bg-red">
                                    <div class="inner" >
                                        <?php
                                        if(($total_expense_pre_month) > ($total_profit) )
                                        {
                                            echo'<img src = "./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height="33px" width="23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        ?>

                                        <p style="font-size: 18px;margin-left: 38px;">Current Month Expense   </p>
                                        <p style="font-size: 18px;margin-left: 38px;color: white;margin-top:-14px;"> <?php echo number_format($total_profit) ?></p></td>

                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format($total_expense_pre_month) ?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i class="ion ion-calculator"></i>
                                    </div>

                                </div>
                            </a>
                        </div>

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./gl/inquiry/profit_loss.php?" target="_blank">
                                <div class="small-box bg-yellow">
                                    <div class="inner" >
                                        <?php
                                        if(($net_revenue_for_p_m) > ($net_revenue) )
                                        {
                                            echo'<img src="./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }

                                        if($net_revenue < 0)
                                        {
                                            echo '<p style="font-size: 18px;margin-left: 38px;">Net Loss </p>';
                                        }else
                                        {
                                            echo '<p style="font-size: 18px;margin-left: 38px;">Net Profit </p>';
                                        }
                                        ?>
                                        <p style="font-size: 18px;color: white;margin-top:-14px; "> <?php echo number_format($net_revenue) ." / ". 
                                          number_format(($net_revenue/ (-1*$total_income))*100).'%'  
                                         
                                        ?></p></td>

                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format($net_revenue_for_p_m) 
                                    ." / ".
                                    number_format(($net_revenue_for_p_m/-$total_income_of_previous_month)*100).'%'
                                    ?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i style="" class="fa fa-line-chart"></i>

                                        <!--                                    <img src="./themes/premium/images/if_Stock Index Up_27881.png" height="60px" width="46px" style="margin-top: 30px;" >-->
                                        <!--                                      <i class="fa fa-arrow-circle-up" style="margin-top:15px;"></i>-->
                                    </div>
                                </div>
                            </a>
                        </div>
                                        
    
                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./gl/inquiry/balance_sheet.php?" target="_blank">
                                <div class="small-box bg-green">
                                    <div class="inner" >
                                        <?php
                                        
                             $total_equity_plus_profit = $total_equity - $net_revenue;
                             $total_equity_pre_month_plus_profit = $total_equity_pre_month + $total_income_of_previous_month;
                        
                                        if($total_equity > $total_equity_pre_month) 
                                        {
                                            echo'<img src="./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                            echo'<p style="font-size:18px;">'; echo $equity_name; 
                                           echo '</p>';
                                        
                                        ?>
                                        <p style="font-size: 18px;color: white;margin-top:-14px;margin-left:8px; ">
                                            <?php 
                                            
                                            $total_equity = $total_equity * -1;
                                            $total_equity_plus_profit = $total_equity_plus_profit * -1;

                                            $total_equity_pre_month = $total_equity_pre_month * -1;
                                            
                                            $total_equity_pre_month_plus_profit = $total_equity_pre_month_plus_profit * -1;

                                            echo number_format($total_equity)
                                           .' / '. number_format($total_equity_plus_profit)
                                            ?> </p></td>
                                        <p style="font-size:12px;">  </p>
                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format($total_equity_pre_month) 
                                    .' / '.
                                    number_format($total_equity_pre_month_plus_profit) 
                                    ?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i style="" class="fa fa-percent"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div><!--.row-->
                </div><!--.item-->

                <div class="item">
                    <div class="row">

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./reporting/reports_main.php?Class=0&REP_ID=1016" target="_blank">
                                <div class="small-box bg-aqua">
                                    <div class="inner" >
                                        <?php
                                        if(($total_c_rec_for_p_month) > ($total_c_rec) )
                                        {
                                            echo'<img src="./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;">';
                                        }
                                        ?>
                                        <p style="font-size: 18px;margin-left:38px;">Current Month Recovery </p>
                                        <p style="font-size: 18px;color: white;margin-top:-14px;margin-left:38px; ">
                                            <?php echo number_format($total_c_rec) ?></p></td>
                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format($total_c_rec_for_p_month)?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./sales/inquiry/customer_inquiry.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i style="" class="fa fa-file-text-o"></i>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="#" target="_blank">
                                <div class="small-box bg-teal-gradient">
                                    <div class="inner" >
                                        <?php
                                        if(($total_c_b_balance_p_m) > ($total_c_b_balance) )
                                        {
                                            echo'<img src="./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        ?>
                                        <p style="font-size:18px;margin-left:38px;">Cash/Bank Balance</p>
                                        <p style="font-size: 18px;color: white;margin-top:-14px;margin-left:38px; ">
                                            <?php echo number_format($total_c_b_balance) ?></p></td>
                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php number_format($total_c_b_balance_p_m)?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./gl/inquiry/balance_sheet.php?TransFromDate=01-07-2018&TransToDate=17-07-2018&Dimension=0&Dimension2=0&AccGrp=6000" class="small-box-footer" target="_blank">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i style="" class="fa fa-money"></i>

                                        <!--                                    <img src="./themes/premium/images/if_Stock Index Up_27881.png" height="60px" width="46px" style="margin-top: 30px;" >-->
                                        <!--                                      <i class="fa fa-arrow-circle-up" style="margin-top:15px;"></i>-->
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./reporting/reports_main.php?Class=0&REP_ID=101" target="_blank">
                                <div class="small-box bg-aqua">
                                    <div class="inner" >
                                        <?php
                                        if(($total_amount_recieveable) < ($total_amount_recieveable_for_pre) ) {
                                            echo'<img src = "./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height="33px" width="23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        ?>
                                        <p style="font-size: 18px;margin-left: 38px;">Total Receiveable</p>
                                        <p style="font-size: 18px;color: white;margin-top:-14px;margin-left: 38px; "> <?php echo number_format($total_amount_recieveable) ?></p></td>
                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format(( $total_amount_recieveable_for_pre))?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                  <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i class="fas fa-hand-holding-usd"></i>
                                    </div>

                                </div></a>
                        </div>

                        <div class="col-lg-3 col-6">
                            <!-- small box -->
                            <a href="./reporting/reports_main.php?Class=1&REP_ID=201" target="_blank">
                                <div class="small-box bg-red">
                                    <div class="inner" >
                                        <?php
                                        if(($total_expense_pre_month) > ($total_profit) )
                                        {
                                            echo'<img src = "./themes/premium/images/arrow_down.png" height = "33px" width = "23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        else{
                                            echo'<img src="./themes/premium/images/arrow_up.png" height="33px" width="23px" style = "margin-top: 8px;margin-left:9px;float: left;" >';
                                        }
                                        ?>

                                        <p style="font-size: 18px;margin-left: 38px;">Total Payable   </p>
                                        <p style="font-size: 18px;margin-left: 38px;color: white;margin-top:-14px;"> <?php echo number_format(-1*$total_amount_payable) ?></p></td>

                                    </div>
                                    <div style="text-align: right;" class="small-box-footer">Previous Month : <?php echo number_format(-1* (-1*$total_pre_amount_payable)) ?> &nbsp;&nbsp;&nbsp;</div>
                                    <!--                                <a href="./gl/inquiry/profit_loss.php?" class="small-box-footer">View Month Detail <i class="fa fa-arrow-circle-right"></i></a>-->
                                    <div class="icon">
                                        <i class="ion ion-calculator"></i>
                                    </div>

                                </div>
                            </a>
                        </div>


                    </div><!--.row
                </div><!--.item-->

                </div><!--.carousel-inner-->
                <a data-slide="next" href="#Carousel" class="right carousel-control"><i class="fa fa-angle-right"></i></a>
                <a data-slide="prev" href="#Carousel" class="left carousel-control"><i class="fa  fa-angle-left"></i></a>
            </div><!--.Carousel-->

        </div>
    </div>
    <script>

        $('#Carousel').carousel({
            interval: 4500,
            pause: "hover"
        });

    </script>
    <?php
    }
    }


    ?>

