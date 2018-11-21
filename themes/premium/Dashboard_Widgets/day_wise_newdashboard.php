<?php

class carosal_view
{
public function renderCarosal()
{
$path_to_root = "././.";
?>

<div class="row">
    <div class="col-md-12">
        <style type="text/css">
            /*body{padding-top:20px;}*/
            .carousel {
                margin-bottom: 0;
                padding: 0 0 -10px 0;
                /*background-color: #00a157;*/
            }

            /* The controlsy */
            .carousel-control {
                left: 127px;
                height: 112px;

                width: 40px;
                /*background: none repeat scroll 0 0 #222222;*/
                /*border: 4px solid #FFFFFF;*/
                /*border-radius: 23px 23px 23px 23px;*/
                margin-top: 41px;
            }

            .carousel-control.right {
                right: 50px;
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

            .asdf {
                background-color: #8c8c8c;;
                padding: 0;
                margin-left: -5px;
            }

            .boxes {
                width: 160px;
                float: left;
                margin-left: 10px;

            }

            .values {
                color: white;
                text-align: center;
                font-size: 14px;
            }

            .header_values {
                color: #656565;
                font-size: 14px;
                font-weight: bolder;
            }
        </style>

        <?php

        function get_revenue($from,$to)
        {
            $sql = "SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount FROM ".TB_PREF."gl_trans,".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class
WHERE ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
AND ".TB_PREF."chart_class.ctype = 4
AND  ".TB_PREF."gl_trans.tran_date >= '$from'
AND  ".TB_PREF."gl_trans.tran_date <= '$to'";
            $incomeresult = db_query($sql);
            $incomemyrow = db_fetch($incomeresult);

            if($incomemyrow['t_amount']=='' )
                return 0;
            else
                return abs($incomemyrow['t_amount']);
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
        function equity($to,$return_name=0)
        {
            $sql = "SELECT SUM(".TB_PREF."gl_trans.amount) AS t_amount, class_name as name FROM ".TB_PREF."gl_trans,
            ".TB_PREF."chart_master,".TB_PREF."chart_types, ".TB_PREF."chart_class WHERE
            ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
            AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id
            AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid
            AND ".TB_PREF."chart_class.ctype = '3'
            AND  ".TB_PREF."gl_trans.tran_date <= '$to'
            ";
            $equityresult = db_query($sql);
            $equitymyrow= db_fetch($equityresult);

            if($return_name ==1)
                return $equitymyrow['name'];
            else
                return $equitymyrow;

//    $total_equity = $equitymyrow['t_amount'];
//    $equity_name = $equitymyrow['class_name'];
        }
        function get_open_balance_for_dashboard($to)
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

    	AND t.tran_date <= '$to'";

            $result = db_query($sql,"No transactions were returned");
            return db_fetch($result);
        }
        function get_payable($to)
        {
            $sql="SELECT 
 SUM(IF(TYPE IN(20,2,42), ((ov_amount*rate) + ov_gst + ov_discount-alloc-(supply_disc + service_disc + fbr_disc + srb_disc)), 
 ((ov_amount*rate) + ov_gst + ov_discount+ alloc-(supply_disc + service_disc + fbr_disc + srb_disc)))) AS OutStanding
 FROM 0_supp_trans 
WHERE tran_date <= '".$to."'";

            $result3 = db_query($sql,"The client details could not be retrieved");
            $supp_balance_total = db_fetch($result3,'can not fetch supplioer balances');
            $total_amount_payable = (-1* $supp_balance_total[0]);

            return $total_amount_payable ;
        }
        function get_item_stock($from,$to)
        {
            $total = 0;
            $sql ="SELECT move.qty , item.material_cost , SUM(ROUND(move.qty,2) * item.material_cost) AS total
            FROM `0_stock_moves`  move ,  0_stock_master item
            WHERE 
            move.stock_id = item.stock_id 
            AND item.material_cost > 0
            AND move.tran_date >= '$from'
            AND move.tran_date <= '$to'
            AND item.mb_flag<>'D' AND mb_flag <> 'F' 
            GROUP BY move.stock_id ";
            $result = db_query($sql, "could not get sums");

            while($dtt = db_fetch($result))
            {
                $tot = $dtt['total'];
                $total +=$tot;
            }

            $total = $total;

            return $total ;

        }
        function get_banks_amount ($type,$from,$to)
        {
            $sql ="SELECT SUM(t.amount) as total FROM 0_bank_trans t LEFT JOIN 0_voided v ON t.type=v.type 
            AND t.trans_no=v.id, 0_bank_accounts banks 
            WHERE  ISNULL(v.date_) 
            AND t.trans_date >= '$from' 
            AND t.trans_date <= '$to' AND amount != 0	
            AND t.bank_act = banks.`id`
            AND banks.`account_type` = '$type'
            ORDER BY t.trans_date, t.id";


            $result = db_query($sql, "could not get sums");
            $dtt = db_fetch($result);
            return $dtt['total'];

        }
        function get_cogs_total($account_code,$from,$to)
        {
            $sql ="SELECT SUM(`amount`) FROM `0_gl_trans` WHERE `account`='".$account_code."' 
                    AND tran_date >='$from' AND tran_date <='$to' ";
            $result = db_query($sql, "could not get sums");
            $dtt = db_fetch($result);
            return $dtt[0];

        }
        //************************************************************************
        //************************************************************************
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


        //       display_error("To date = ".$today_date);
        //       display_error("Last date = ".$last_day_date);
        //       display_error("start date of this month = ".$start_date_of_this_moth);
        //       display_error("end date of this month = ".$end_date_of_this_month);
        //       display_error("start date of last month = ".$start_date_of_previous_month);
        //       display_error("end date of last month = ".$date_end_of_previous_month);


        //**Revenue
        $today_revenue = get_revenue($today_date,$today_date);
        $last_day_revenue = get_revenue($last_day_date,$last_day_date);
        $this_month_revenue = get_revenue($start_date_of_this_moth,$end_date_of_this_month);
        $last_month_revenue = get_revenue($start_date_of_previous_month,$date_end_of_previous_month);

        //**Expence
        $today_expence  =  get_expence($today_date,$today_date);
        $last_day_expence  =  get_expence($last_day_date,$last_day_date);
        $this_month_expence  =  get_expence($start_date_of_this_moth,$end_date_of_this_month);
        $last_month_expence  =  get_expence($start_date_of_previous_month,$date_end_of_previous_month);

        //**profit
        $today_net_loss = $today_revenue - $today_expence;
        $last_day_net_loss = $last_day_revenue - $last_day_expence;
        $this_month_net_loss = $this_month_revenue - $this_month_expence;
        $last_month_net_loss = $last_month_revenue - $last_month_expence;

        $account_code    = get_company_pref('default_cogs_act');
        $today_cogs      = get_cogs_total($account_code,$today_date,$today_date);
        $last_day_cogs   = get_cogs_total($account_code,$last_day_date,$last_day_date);
        $this_month_cogs = get_cogs_total($account_code,$start_date_of_this_moth,$end_date_of_this_month);
        $last_month_cogs = get_cogs_total($account_code,$start_date_of_previous_month,$date_end_of_previous_month);

        if($today_net_loss > 0)
        {
            $today_gros_profit = $today_revenue - $today_cogs;
            $today_net_profit = $today_revenue - $today_expence;
            $today_net_loss =0;
        }else{
            $today_gros_profit = 0;
            $today_net_profit = 0;
            $today_net_loss = $today_net_loss ;
        }

        if($last_day_net_loss > 0)
        {
            $last_day_gros_profit = $last_day_revenue - $last_day_cogs;
            $last_day_net_profit = $last_day_revenue - $last_day_expence;
            $last_day_net_loss =0;
        }else{
            $last_day_gros_profit = 0;
            $last_day_net_profit = 0;
            $last_day_net_loss = $last_day_net_loss ;
        }


        if($this_month_net_loss>0)
        {
            $this_month_gross_profit = $this_month_revenue - $this_month_cogs;
            $this_month_net_profit   = $this_month_revenue - $this_month_expence;
            $this_month_net_loss =0;
        }else
        {
            $this_month_gross_profit =0;
            $this_month_net_profit =0;
            $this_month_net_loss = $this_month_net_loss;
        }

        if($last_month_net_loss > 0)
        {
            $last_month_gross_profit = $last_month_revenue - $last_month_cogs;
            $last_month_net_profit = $last_month_revenue - $last_month_expence;
            $last_month_net_loss =0;
        }
        else{
            $last_month_gross_profit =0;
            $last_month_net_profit=0;
            $last_month_net_loss = $last_month_net_loss;
        }


        //** Recovery
        $today_recovery = recovery($today_date,$today_date);
        $last_day_recovery = recovery($last_day_date,$last_day_date);
        $this_month_recovery = recovery($start_date_of_this_moth,$end_date_of_this_month);
        $last_month_recovery = recovery($start_date_of_previous_month,$date_end_of_previous_month);

        //** Equity
        $equity_name = equity($today_date,1);

        $today_equity = equity($last_day_date);
        $last_day_equity = equity($end_date_of_this_month);
        $this_month_equity = equity($end_date_of_this_month);
        $last_month_equity = equity($date_end_of_previous_month);

        //** Recieveable
        $cust_balance_for_today_current = get_open_balance_for_dashboard($today_date);
        $today_recieveable = $cust_balance_for_today_current['charges'] - $cust_balance_for_today_current['credits'];



        $cust_balance_last_day = get_open_balance_for_dashboard($last_day_date);
        $last_day_recieveable = $cust_balance_last_day['charges'] - $cust_balance_last_day['credits'];

        $cust_balance_this_month = get_open_balance_for_dashboard($end_date_of_this_month);
        $this_month_recieveable = $cust_balance_this_month['charges'] - $cust_balance_this_month['credits'];

        $cust_balance_last_month = get_open_balance_for_dashboard($date_end_of_previous_month);
        $last_month_recieveable = $cust_balance_last_month['charges'] - $cust_balance_last_month['credits'];


        //**Payable
        $today_payable      = get_payable($today_date);
        $last_day_payable   = get_payable($last_day_date);
        $this_month_payable = get_payable($end_date_of_this_month);
        $last_month_payable = get_payable($date_end_of_previous_month);

        //** Stock */
        $today_stock = get_item_stock($today_date,$today_date);
        $last_day_stock = get_item_stock($last_day_date,$last_day_date);
        $this_month_stock = get_item_stock($start_date_of_this_moth,$end_date_of_this_month);
        $last_month_stock = get_item_stock($start_date_of_previous_month,$date_end_of_previous_month);

        //** Cash In hand
        $today_cash_in_hand = get_banks_amount (3,$today_date,$today_date);
        $last_day_cash_in_hand = get_banks_amount (3,$last_day_date,$last_day_date);
        $this_month_cash_in_hand = get_banks_amount (3,$start_date_of_this_moth,$end_date_of_this_month);
        $last_month_cash_in_hand = get_banks_amount (3,$start_date_of_previous_month,$date_end_of_previous_month);

        //** Banks */
        $today_bank = get_banks_amount (0,$today_date,$today_date);
        $last_day_bank = get_banks_amount (0,$last_day_date,$last_day_date);
        $this_month_bank = get_banks_amount (0,$start_date_of_this_moth,$end_date_of_this_month);
        $last_month_bank = get_banks_amount (0,$start_date_of_previous_month,$date_end_of_previous_month);

        //** Cheque In Hand */
        $today_cheque_in_hand = get_banks_amount (4,$today_date,$today_date);
        $last_day_cheque_in_hand = get_banks_amount (4,$last_day_date,$last_day_date);
        $this_month_cheque_in_hand = get_banks_amount (4,$start_date_of_this_moth,$end_date_of_this_month);
        $last_month_cheque_in_hand = get_banks_amount (4,$start_date_of_previous_month,$date_end_of_previous_month);
        ?>



        <div class="" >
            <div class="">
                <!--    <table class="table table-bordered col-lg-10">-->
                <!--        <tr><td>Bank</td></tr>-->
                <!--        <tr><td>1000</td></tr>-->
                <!--        <tr><td>2000</td></tr>-->
                <!--        <tr><td>3000</td></tr>-->
                <!--        <tr><td>4000</td></tr>-->
                <!--    </table>-->

                <div class="" style="margin-right:40px; ">

                    <div class="boxes" style="width: 110px;">
                        <table class="table table-bordered" >
                            <tr><td class="header_values">Period</td></tr>
                            <tr><td class="header_values">Today</td></tr>
                            <tr><td class="header_values">Yesterday</td></tr>
                            <tr><td class="header_values">This Month</td></tr>
                            <tr><td class="header_values">Last Month</td></tr>
                        </table>
                    </div>

                    <div id="Carousel" class="carousel slide" data-ride="carousel" style="float: left;">
                        <div class="carousel-inner">

                            <div class="item active" >

                                <div style="width: 100%">
                                    <div class="boxes">
                                        <div class="small-box bg-aqua">
                                            <table class="table " >
                                                <tr style="background-color: rgb(0, 173, 215);"><td><div style="color: white;text-align: center;">Revenue</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_revenue,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_revenue,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_revenue,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_revenue,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;" class="fas fa-signal"></i>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="boxes">
                                        <div class="small-box bg-red">
                                            <table class="table " >
                                                <tr style="background-color: rgb(199, 67, 51);"><td><div style="color: white;text-align: center;"> Expense </div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_expence,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_expence,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_expence,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_expence,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fas fa-calculator"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-orange">
                                            <table class="table " >
                                                <tr style="background-color: rgb(218, 140, 16);"><td><div style="color: white;text-align: center;">Profit</div></td></tr>
                                                <tr><td class="values"><?php echo ($today_gros_profit.'/'.$today_net_profit); ?></td></tr>
                                                <tr><td class="values"><?php echo ($last_day_gros_profit.'/'.$last_day_net_profit); ?></td></tr>
                                                <tr><td class="values"><?php echo ($this_month_gross_profit.'/'.$this_month_net_profit); ?></td></tr>
                                                <tr><td class="values"><?php echo ($last_month_gross_profit.'/'.$last_month_net_profit); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;" class="fa fa-line-chart"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-aqua">
                                            <table class="table " >
                                                <tr style="background-color: rgb(0, 173, 215);"><td><div style="color: white;text-align: center;">Loss</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$today_net_loss,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$last_day_net_loss,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$this_month_net_loss,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$last_month_net_loss,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;" class="fas fa-signal"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-green">
                                            <table class="table " >
                                                <tr style="background-color: rgb(0, 149, 81);"><td><div style="color: white;text-align: center;"><?php echo $equity_name; ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_equity['t_amount'],2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_equity['t_amount'],2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_equity['t_amount'],2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_equity['t_amount'],2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fa fa-percent"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-blue">
                                            <table class="table " >
                                                <tr style="background-color: rgb(0, 103, 164);"><td><div style="color: white;text-align: center;">Recovery</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_recovery,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_recovery,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_recovery,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_recovery,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;" class="fa fa-reply"></i>
                                            </div>
                                        </div>
                                    </div>


                                </div><!--.row-->

                            </div><!--.item-->

                            <div class="item ">

                                <div class="">



                                    <div class="boxes">
                                        <div class="small-box bg-red ">
                                            <table class="table " >
                                                <tr style="background-color: rgb(199, 67, 51);"><td><div style="color: white;text-align: center;">Payable</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$today_payable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$last_day_payable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$this_month_payable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2(-1*$last_month_payable,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;" class="fa fa-money"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-teal-gradient">
                                            <table class="table" >
                                                <tr style="background-color: #39b8b8;"><td><div style="color: white;text-align: center;">Receiveable</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_recieveable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_recieveable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_recieveable,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_recieveable,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fa fa-file-text-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-orange" >
                                            <table class="table " >
                                                <tr style="background-color:rgb(218, 140, 16);"><td><div style="color: white;text-align: center;">Stock</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_stock,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_stock,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_stock,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_stock,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fas fa-archive"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-blue" >
                                            <table class="table " >
                                                <tr style="background-color: rgb(0, 103, 164);"><td><div style="color: white;text-align: center;">Cash In Hand</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_cash_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_cash_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_cash_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_cash_in_hand,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fas fa-hand-holding-usd"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-teal-gradient" >
                                            <table class="table " >
                                                <tr style="background-color:rgb(57, 184, 184);"><td><div style="color: white;text-align: center;">Banks</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_bank,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_bank,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_bank,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_bank,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fa fa-bank"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boxes">
                                        <div class="small-box bg-green" >
                                            <table class="table " >
                                                <tr style="background-color:rgb(0, 149, 81);"><td><div style="color: white;text-align: center;">Cheque In Hand</div></td></tr>
                                                <tr><td class="values"><?php echo number_format2($today_cheque_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_day_cheque_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($this_month_cheque_in_hand,2); ?></td></tr>
                                                <tr><td class="values"><?php echo number_format2($last_month_cheque_in_hand,2); ?></td></tr>
                                            </table>
                                            <div class="icon">
                                                <i style="margin-top: 80px;"  class="fa fa-map"></i>
                                            </div>
                                        </div>
                                    </div>

                                </div><!--.row-->
                            </div><!--.item-->

                        </div>
                    </div>
                    <!--.carousel-inner-->
                    <a data-slide="next" href="#Carousel" class="right carousel-control"><i class="fa fa-angle-right"></i></a>
                    <a data-slide="prev" href="#Carousel" class="left carousel-control"><i class="fa  fa-angle-left"></i></a>
                </div><!--.Carousel-->

            </div>
        </div>
        <script>

            $('#Carousel').carousel({
                interval: 995500,
                pause: "hover"
            });

        </script>

    </div>

    <?php
    }
    }


    ?>

