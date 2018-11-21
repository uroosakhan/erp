<?php

class AllDonutCharts
{
    public function customers()
    { global $path_to_root;
        echo "<script src='$path_to_root/themes/".user_theme()."/all_js/Chart.min.js'></script>";

        echo'<style type="text/css">
                .text_short{
                  text-overflow: ellipsis;width : 90px;overflow:hidden;
                  display:inline-block; white-space: nowrap;
                }
                .li_total
                {
                    background-color: #ecf0f5;
                }
                .li_total_dark
                    {
                        background-color: #d8d8d8;
                        
                    }

            </style>';


        echo'<div style="float: left;width: 100%;" class="col-lg-12;">';

        echo"<div class='col-md-3 col-sm-10 col-xs-10'>";
        echo'<div class="box box-info"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Customer Balances</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>                   
                  </div>
                </div><!-- /.box-header -->                 
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="chart-responsive">
                        <canvas id="pieChart7" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                <ul class="nav nav-pills nav-stacked">';

        $total = array(0,0,0,0, 0);

//	if ($to == null)
        $todate = date("Y-m-d");
//	else
//		$todate = date2sql($to);
        $past1 = get_company_pref('past_due_days');
        $past2 = 2 * $past1;


        $charges = " IF(trans.type = ".ST_SALESINVOICE." OR (trans.type = ".ST_JOURNAL." AND trans.ov_amount>0) OR trans.type = ". ST_BANKPAYMENT." OR trans.type = ". ST_CPV.",
        abs(((trans.ov_amount*rate) + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - trans.discount1 - trans.discount2) ),0)";

        $credits = " IF(trans.type != ".ST_SALESINVOICE." AND NOT(trans.type = ".ST_JOURNAL." AND trans.ov_amount>0) AND NOT (trans.type = ". ST_BANKPAYMENT.") AND NOT (trans.type = ". ST_CPV."),
        abs(((trans.ov_amount*rate) + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - trans.discount1 - trans.discount2 ) ),0)";

        $sql = "SELECT ".TB_PREF."debtors_master.name,".TB_PREF."debtors_master.debtor_no,
		Sum(IFNULL($charges - $credits,0)) AS Balance

		FROM ".TB_PREF."debtors_master, ".TB_PREF."debtor_trans trans 	 
		WHERE ".TB_PREF."debtors_master.debtor_no = trans.debtor_no
            AND trans.type <> 13
            AND	trans.tran_date <= '$todate' ";

        $sql .= "GROUP BY
			  ".TB_PREF."debtors_master.debtor_ref
";
        $sql .= " ORDER BY Balance DESC LIMIT 10";
        $result = db_query($sql,"The Client details could not be retrieved");

        // $client_record = db_fetch($result);
        $i = 0;
        $total_of_top_ten = 0;
        $percent = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;



        $today = Today();
        $today1 = date2sql($today);

        function get_open_balance_for_donut($to)
        {

            $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
     	((t.ov_amount*rate) + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";


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

        $cust_balance_current = get_open_balance_for_donut($today1);

        $total_amount_recieveable = ($cust_balance_current['charges'] - $cust_balance_current['credits']);
        echo '<li style="font-weight: bold">';
        echo'<span style="margin-left: 17px;"> Name</span>
                         <span class="pull-right text-red" style="margin-right: 13px;"> % </span>
                         <div class="pull-right text-black" style="margin-right: 16px;" >Balances</div>
                  </li>';
        while ($myrow = db_fetch($result))
        {
            //for pecentage
            $a = $myrow['Balance'];
            $b = $total_amount_recieveable;
            $percent = $a / $b  * 100;

            echo '<li>';
            echo"<a target='_blank' " . ($credit<0 ? 'class="redfg"' : '')."href='./sales/inquiry/customer_inquiry.php?customer_id=".$myrow['debtor_no']."'"
                ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >";

            echo' 
                         <span class="text_short">'.$myrow['name'].'</span>
                         <span class="pull-right text-red" id="#value" >'.number_format($percent ).'</span>
                         <div class="pull-right text-black" style="margin-right: 14px;" >'.number_format2($myrow['Balance'],1).'</div>
                     </a>
                   </li>';

//            <div class="pull right text-black" style="margin-left: 50px;text-align: right;float: right;margin-right: 42px;" >'.number_format2($myrow['Balance'],2).'</div>
            $data[$i]=$myrow['Balance'];
            $string[$i] =$myrow['name'];
            if( $data[0]>0){$data[$i]=$myrow['Balance']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow['Balance']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow['Balance']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow['Balance']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow['Balance']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow['Balance']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow['Balance']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow['Balance']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow['Balance']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow['Balance']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9

            $i++;
            $total_of_top_ten += $myrow['Balance'];
            $total_percent +=$percent;
            ;}



        if($total_of_top_ten !=0) {
            echo '<li class="li_total" >
                     <a style="font-size:12px;" href="#">
                         <span class="text_short" style="font-weight: bold;">Total Top 10</span>
                         <span class="pull-right text-red" id="#value">' . number_format($total_percent) . '</span>
                         <div class="text-black pull-right" style="margin-right: 20px;font-weight: bold;">' . number_format2($total_of_top_ten,1) . '</div>
                         </a>
                   </li>';
            $datestart = begin_fiscalyear();
            $dateend = end_fiscalyear();
            echo '<li class="li_total_dark">
                     <a style="font-size:12px;" href="reporting/prn_redirect.php?PARAM_0='.$datestart.'&PARAM_1='.$dateend.'&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=101_2=null&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=0&REP_ID=101" target="_blank" id="prtopt" class="printlink" accesskey="P"">
                         <span class="text_short" style="font-weight: bold;" >Total Of All</span>
                         <span class="pull-right text-red" >100</span>
                         <div class="text-black pull-right" style="margin-right: 20px;font-weight: bold;">' . number_format2($total_amount_recieveable,1) . '</div>

                         </a>
                   </li>';
        }

        echo '<script>
  var pieChartCanvas = $("#pieChart7").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';

        echo"</div>";
        echo"<div class='col-md-3  col-sm-10 col-xs-10'>";
        echo'<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Supplier Balances</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div>';

//        <!-- /.box-header -->


        echo "<script src='./themes/premium/all_js/canvasjs.min.js'></script>";
        echo'<div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="chart-responsive">';
        echo'<canvas id="pieChart8" height="200" width="169" style="width: 169px; height: 200px;"></canvas>';
//                      echo'<div id="chartContainer_supp" style="height: 300px; width: 100%;"></div>';
        echo'</div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/

        $total = array(0,0,0,0, 0);

//	if ($to == null)
        $todate = date("Y-m-d");
//	else
//		$todate = date2sql($to);
        $past1 = get_company_pref('past_due_days');
        $past2 = 2 * $past1;
        // removed - debtor_trans.alloc from all summations
//if ($all)
//    	$value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh)";
//    else

    
    	 $value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT." OR (trans.type = ".ST_JOURNAL." AND trans.ov_amount>0),
    		((trans.ov_amount *trans.rate)
    		 + trans.ov_gst + trans.ov_discount  + trans.gst_wh - trans.alloc),
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh + trans.alloc))";
    		
        $due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT." OR trans.type=".ST_SUPPCREDIT_IMPORT.",trans.due_date,trans.tran_date)";
        $sql = "SELECT supp.supp_name,supp.supplier_id, supp.curr_code, ".TB_PREF."payment_terms.terms,
		Sum(IFNULL($value,0)) AS Balance
		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id AND trans.tran_date <= '$todate',
			 ".TB_PREF."payment_terms

		WHERE
			 supp.payment_terms = ".TB_PREF."payment_terms.terms_indicator
			  ";
//	if (!$all)
        $sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh) - trans.alloc > ".FLOAT_COMP_DELTA." ";
        $sql .= "GROUP BY
			  supp.supp_name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month";
        $sql .= " ORDER BY Balance DESC LIMIT 10";


        $result = db_query($sql,"The client details could not be retrieved");




        //for supplier balances Total
//        $sql3 = " SELECT
//				SUM( (ov_amount * rate ) + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc +  srb_disc) ) AS TotalAmount
//
//   			FROM ".TB_PREF."supp_trans
//   			WHERE  type != ".ST_SUPPCREDIT_IMPORT." AND ov_amount!=0
//    				ORDER BY tran_date";

//         $sql3="SELECT 
//  SUM(IF(TYPE IN(20,2,42), ( (ov_amount * rate) + ov_gst + ov_discount-alloc-(supply_disc + service_disc + fbr_disc + srb_disc)), 
//  (ov_amount + ov_gst + ov_discount+ alloc-(supply_disc + service_disc + fbr_disc + srb_disc)))) AS TotalAmount
//  FROM 0_supp_trans 
// WHERE tran_date <= '".$todate."'";

$sql3="SELECT  SUM((ov_amount + ov_gst + ov_discount - ( supply_disc + service_disc + fbr_disc + srb_disc) ) ) AS TotalAmount
        FROM 0_supp_trans 
        WHERE  tran_date <= '".$todate."' 
        AND TYPE != 43 AND ov_amount!=0 ORDER BY tran_date";

        $result3 = db_query($sql3,"The client details could not be retrieved");
        $supp_balance_total = db_fetch($result3,'can not fetch supplioer balances');

        // $client_record = db_fetch($result);
        $i = 0;
        $supp_balance_toptentotal = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;
        echo '<li style="font-weight: bold">';
        echo'<span style="margin-left: 17px;"> Name</span>
                         <span class="pull-right text-red" style="margin-right: 13px;"> % </span>
                         <div class="pull-right text-black" style="margin-right: 16px;" >Balances</div>
                  </li>';
        while ($myrow = db_fetch($result))
        {
            $a= $myrow['Balance'];
            $b = $supp_balance_total['TotalAmount'];
            $percent_supp = $a/$b * 100;
//            echo '<li><a style="font-size:12px;" href="#">'.$myrow['supp_name'].'<span class="pull-right text-red" id="#value">'.number_format($myrow['Balance']).'</span></a></li>';
            echo '<li>';
            echo"<a target='_blank' " . ($credit<0 ? 'class="redfg"' : '')
                ."href='./purchasing/inquiry/supplier_inquiry.php?supplier_id=".$myrow['supplier_id']."'"
                ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >";
            echo'
                         <span class="text_short">'.$myrow['supp_name'].'</span>
                         <span class="pull-right text-red" >'.number_format($percent_supp).'</span>
                         <div class="pull-right text-black" style="margin-right: 16px;" >'.number_format2($myrow['Balance'],1).'</div>
                     </a>
                   </li>';
            $data[$i]=$myrow['Balance'];
            $string[$i] =$myrow['supp_name'];
            if( $data[0]>0){$data[$i]=$myrow['Balance']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow['Balance']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow['Balance']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow['Balance']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow['Balance']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow['Balance']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow['Balance']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow['Balance']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow['Balance']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow['Balance']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['supp_name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['supp_name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['supp_name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['supp_name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['supp_name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['supp_name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['supp_name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['supp_name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['supp_name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['supp_name'];}else{$string[9]='no';} //9

            $i++;
            $supp_balance_toptentotal += $myrow['Balance'];
            $total_percent_supp +=$percent_supp;
            ;}
        if($supp_balance_toptentotal !='')
        {
            echo '<li class="li_total">
                     <a style="font-size:12px;" href="#">
                         <span class="text_short" style="font-weight: bold;">Total Top 10</span>
                         <span class="pull-right text-red" id="#value"> '.number_format($total_percent_supp).'</span>
                         <div class="text-black pull-right" style="margin-right: 20px;font-weight: bold;">'.number_format2($supp_balance_toptentotal,1).'</div>
                         </a>
                   </li>';
            echo '<li class="li_total_dark">
                     <a style="font-size:12px;" href="./reporting/prn_redirect.php?PARAM_0='.$datestart.'&PARAM_1='.$dateend.'&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=&PARAM_7=0&REP_ID=101_2=null&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=0&REP_ID=201" target="_blank" id="prtopt" class="printlink" accesskey="P">
                         <span class="text_short" style="font-weight: bold;">Total Of All</span>
                         <span class="pull-right text-red" id="#value">100</span>
                         <div class="text-black pull-right" style="margin-right: 20px;font-weight: bold;">'.number_format2($supp_balance_total['TotalAmount'],1).'</div>

                         </a>
                   </li>';
        }
        echo '
				   <script>
  var pieChartCanvas = $("#pieChart8").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }

  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
//   echo'<script>
//var chart = new CanvasJS.Chart("chartContainer_supp", {
//    animationEnabled: true,
//	title: {
//		text: ""
//	},
//	data: [{
//		type: "pie",
//		startAngle: 240,
//		yValueFormatString: false,
//		indexLabel: false,
//		dataPoints: [
//			{y: 79.45, label: "Google"},
//			{y: 7.31, label: "Bing"},
//			{y: 7.06, label: "Baidu"},
//			{y: 4.91, label: "Yahoo"},
//			{y: 1.26, label: "Others"}
//		]
//	}]
//});
//chart.render();
//
//</script>';


        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo"<div class='col-md-3  col-sm-10 col-xs-10'>";
        echo'<div class="box box-warning"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Troubled Customer</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    
                  </div>
                </div><!-- /.box-header -->
                  
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="chart-responsive">
                          <canvas id="pieChart3" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
//varticle chart bar
//                        <div id="chartContainernew" style="height: 300px; width: 100%;"></div>
        $today = date2sql(Today());
        $sql = "SELECT debtor.name,trans.trans_no,
			(ov_amount+ov_gst+ov_freight+ov_freight_tax-trans.discount1-trans.discount2) AS total,
			DATEDIFF('$today', trans.due_date) AS days

			FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor,
				".TB_PREF."cust_branch as branch
			WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
				AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight
				+ trans.ov_freight_tax + trans.ov_discount -trans.discount1-trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA."
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC LIMIT 10";
        $result = db_query($sql);

        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        echo'<li style="font-weight:bold "><span style="margin-left: 17px;"> Name</span>
                         <span class="pull-right text-red" style="margin-right: 13px;"> DAYS</span>
                         <div class="pull-right text-black" style="margin-right: 16px;" >Balances</div>
                  </li>';
        $sumtotal = 0;
        while ($myrow = db_fetch($result))
        {
            echo '<li>';
            echo"<a target='_blank' " . ($credit<0 ? 'class="redfg"' : '')
                ."href='./sales/view/view_invoice.php?trans_no=".$myrow['trans_no']."&trans_type=10'"
                ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >";

            echo'<span class="text_short">'.$myrow['name'].'</span>
                      <span class="pull-right text-red" id="#value">'.number_format($myrow['days']).'</span>
                      <span class="pull-right" id="#value" style="margin-right: 20px;">'.number_format($myrow['total']).'</span>
                      </a>
                  </li>';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            $sumtotal += $myrow['total'];
            ;}
        if($sumtotal > 0)
        {
            echo '<li class="li_total_dark">
                     <a style="font-size:12px;" href="#">
                         <span class="text_short" style="font-weight: bold;">Total</span>
                         <div class="text-black pull-right" style="margin-right: 36px;font-weight: bold;">'.number_format2($sumtotal,1).'</div>
                         </a>
                   </li>';
        }
        echo '
				   <script>
  var pieChartCanvas = $("#pieChart3").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }

  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo"<div class='col-md-3  col-sm-10 col-xs-12'>";
        echo'<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart5" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/
        $created_by = $_SESSION["wa_current_user"]->user;

        $begin = begin_fiscalyear();
        $today = Today();
//		$begin1 = date2sql($begin);
        $today1 = date2sql($today);
        /*$sql = "SELECT ".TB_PREF."chart_master.*,".TB_PREF."chart_types.name AS AccountTypeName FROM ".TB_PREF."chart_master,".TB_PREF."chart_types WHERE ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id AND account_type='15' ORDER BY account_name ASC LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");*/


        $sql1 = "SELECT SUM(".TB_PREF."gl_trans.amount) as balance,
                    ".TB_PREF."chart_master.* ,".TB_PREF."bank_accounts.id
                    FROM ".TB_PREF."gl_trans,
                    ".TB_PREF."chart_master,".TB_PREF."chart_types, 
                    ".TB_PREF."chart_class , ".TB_PREF."bank_accounts
                    WHERE ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code 
                    AND ".TB_PREF."chart_master.account_type=".TB_PREF."chart_types.id 
                      AND ".TB_PREF."bank_accounts.account_code=".TB_PREF."chart_master.account_code 
                    AND ".TB_PREF."chart_types.class_id=".TB_PREF."chart_class.cid  
                    AND ".TB_PREF."gl_trans.tran_date > IF(ctype>0 AND ctype<4, '0000-00-00', '$today1') 
                    AND tran_date <= '$today1' ";
        $sql1 .="GROUP BY ".TB_PREF."chart_master.account_code";
        $sql1 .=" ORDER BY balance DESC LIMIT 10";
        $result1 = db_query($sql1, "Transactions could not be calculated");
        $i = 0;
        $total_of_top10=0;


        $sql2 = "SELECT SUM(`amount`) as total FROM 0_bank_trans t 
                    LEFT JOIN 0_voided v ON t.type=v.type 
                    AND t.trans_no=v.id WHERE 
                    ISNULL(v.date_) 
                    AND trans_date <= '".$today1."' 
                    AND amount != 0
                    ORDER BY trans_date, t.id";

        $result2 = db_query($sql2, "Transactions could not be calculated");
        $result_all_banks = db_fetch($result2);
        $total_of_all_banks =$result_all_banks[0];

//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        echo'<li style="font-weight:bold "><span style="margin-left: 17px;"> Name</span>
                         <span class="pull-right text-red" style="margin-right: 13px;"> Balance</span>
                  </li>';
        while ($myrow1 = db_fetch($result1))
        {
            echo '<li >';
            echo"<a target='_blank' " . ($credit<0 ? 'class="redfg"' : '')
                ."href='./gl/inquiry/bank_inquiry.php?bank_account=".$myrow1['id']."'"
                ." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >";
            echo '<span class="text_short" style="width: 130px;">'.$myrow1['account_name'].'</span>
                  <span class="pull-right text-red" id="#value"> 
                '.number_format($myrow1['balance']).'</span>
                </a>
                </li>';

            $data[$i]=$myrow1['balance'];
            $string[$i] =$myrow1['account_name'];
            if( $data[0]!=0){$data[$i]=$myrow1['balance']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow1['balance']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow1['balance']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow1['balance']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow1['balance']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow1['balance']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow1['balance']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow1['balance']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow1['balance']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow1['balance']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow1['account_name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow1['account_name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow1['account_name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow1['account_name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow1['account_name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow1['account_name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow1['account_name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow1['account_name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow1['account_name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow1['account_name']; }else{$string[9]='no';} //9
            $i++;
            $total_of_top10 += $myrow1['balance'];
        };
        if($total_of_top10 > 0)
        {
            echo '<li class="li_total">
                     <a style="font-size:12px;" href="#">
                         <span class="text_short" style="font-weight: bold;">Total of top 10</span>
                         <div class="text-black pull-right" style="font-weight: bold;">'.number_format2($total_of_top10,1).'</div>

                         </a>
                   </li>';
            echo '<li class="li_total_dark">
                     <a style="font-size:12px;" href="#">
                         <span class="text_short" style="font-weight: bold;">Total Of All</span>
                         <div class="text-black pull-right" style="font-weight: bold;">'.number_format2($total_of_all_banks,1).'</div>
                         </a>
                   </li>';
        }


        echo '<script>
  var pieChartCanvas = $("#pieChart5").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";


        echo'</div>';//main div

        echo'<div style="">';
        echo"<div class='row'>"; /** row starts **/

        echo"<div class='col-md-12'>";

        include_once("./themes/".user_theme()."/Dashboard_Widgets/invoicesWidget.php");
        $_Invoices = new invoiceswidgets();
        $_Invoices->AllInvoices();

        echo"</div>";
        echo"</div>"; /** row ends here */
        echo'</div>';
    }


    public function sales()
    {

        echo'<div style="float: left;width: 100%;" class="col-lg-12;">';

        echo"<div class='col-md-3  col-sm-12 col-xs-12'>";
        echo'<div class="box box-success"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Salesman Balances</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart102" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //

//		if ($to == null)
        $todate = date("Y-m-d");
//	else
//		$todate = date2sql($to);
        $past1 = get_company_pref('past_due_days');
        $past2 = 2 * $past1;
        // removed - debtor_trans.alloc from all summations
//	if ($all)
//    	$value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1)
//    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh),0)";
//    else
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1)
    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - 
    		trans.alloc),0)";
        $due = "IF (trans.type=10, trans.due_date, trans.tran_date)";
        $sql = "SELECT ".TB_PREF."salesman.salesman_code,

		Sum(IFNULL($value,0)) AS Balance,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2

		FROM ".TB_PREF."debtors_master
		     INNER JOIN ".TB_PREF."cust_branch
		     ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		     INNER JOIN ".TB_PREF."areas
			 ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		     INNER JOIN ".TB_PREF."salesman
			 ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
			 LEFT JOIN ".TB_PREF."debtor_trans trans ON 
			 trans.tran_date <= '$todate' AND ".TB_PREF."debtors_master.debtor_no = trans.debtor_no AND trans.type <> 13
,
			 ".TB_PREF."payment_terms,
			 ".TB_PREF."credit_status

		WHERE
			 ".TB_PREF."debtors_master.payment_terms = ".TB_PREF."payment_terms.terms_indicator
 			 AND ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			 ";
        $sql .= " GROUP BY ".TB_PREF."salesman.salesman_code";
        $sql .= " ORDER BY Balance DESC LIMIT 10";



        $result2 = db_query($sql,"The clients details could not be retrieved");
        $i = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow2 = db_fetch($result2))
        {
            echo '<li><a style="font-size:12px;" href="#">'.get_salesman_name($myrow2['salesman_code']).'<span class="pull-right text-red" id="#value">'.number_format($myrow2['Balance']).'</span></a></li>';
            $data[$i]=$myrow2['Balance'];
            $string[$i] =get_salesman_name($myrow2['salesman_code']);
            if( $data[0]>0){$data[$i]=$myrow2['Balance']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow2['Balance']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow2['Balance']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow2['Balance']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow2['Balance']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow2['Balance']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow2['Balance']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow2['Balance']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow2['Balance']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow2['Balance']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[9]='no';} //9

            $i++;
            ;}
        echo '
				   <script>
  var pieChartCanvas = $("#pieChart102").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";
        //
        echo"<div class='col-md-3  col-sm-12 col-xs-12'>";
        echo'<div class="box box-warning"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Zones</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
             
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart11" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        $today = Today();
        $today1 = date2sql($today);
        $sql = "SELECT SUM(IF(t.type = 10 OR t.type = 1, (t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,".TB_PREF."areas.area_code FROM ".TB_PREF."debtor_trans t INNER JOIN ".TB_PREF."cust_branch ON t.debtor_no=".TB_PREF."cust_branch.debtor_no INNER JOIN ".TB_PREF."areas ON ".TB_PREF."cust_branch.area=".TB_PREF."areas.area_code INNER JOIN ".TB_PREF."salesman ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code WHERE t.type <> 13 AND t.tran_date < '$today1'
			 ";
        $sql .= " GROUP BY ".TB_PREF."areas.area_code";
        $sql .= " ORDER BY charges DESC LIMIT 10";



        $result2 = db_query($sql,"The clients details could not be retrieved");
        $i = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow2 = db_fetch($result2))
        {
            echo '<li><a style="font-size:12px;" href="#">'.get_area_name($myrow2['area_code']).'<span class="pull-right text-red" id="#value">'.number_format($myrow2['charges']).'</span></a></li>';

            $data[$i]=$myrow2['charges'];
            $string[$i] =get_area_name($myrow2['area_code']);
            if( $data[0]>0){$data[$i]=$myrow2['charges']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow2['charges']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow2['charges']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow2['charges']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow2['charges']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow2['charges']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow2['charges']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow2['charges']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow2['charges']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow2['charges']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[9]='no';} //9

            $i++;
        }
        echo '
				   <script>
  var pieChartCanvas = $("#pieChart11").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
		var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";
        //top 10 saleman debit

        echo"<div class='col-md-3  col-sm-12 col-xs-12'>";
        echo'<div class="box box-info"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Salesmen</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart12" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        $begin = begin_fiscalyear();
        $begin1 = date2sql($begin);
        $today = Today();
        $today1 = date2sql($today);
        $sql = "SELECT SUM(IF(t.type = 10 OR t.type = 1, (t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,".TB_PREF."salesman.salesman_code
	 FROM ".TB_PREF."debtor_trans t 
	 INNER JOIN ".TB_PREF."cust_branch ON t.debtor_no=".TB_PREF."cust_branch.debtor_no 
	 INNER JOIN ".TB_PREF."areas ON ".TB_PREF."cust_branch.area=".TB_PREF."areas.area_code 
	 INNER JOIN ".TB_PREF."salesman ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code WHERE t.type <> 13  AND t.tran_date < '$today1'
			 ";
        $sql .= " GROUP BY ".TB_PREF."salesman.salesman_code";
        $sql .= " ORDER BY charges DESC LIMIT 10";



        $result2 = db_query($sql,"The clients details could not be retrieved");
        $i = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow2 = db_fetch($result2))
        {
            echo '<li><a style="font-size:12px;" href="#">'.get_salesman_name($myrow2['salesman_code']).'<span class="pull-right text-red" id="#value">'.number_format($myrow2['charges']).'</span></a></li>';


            $data[$i]=$myrow2['charges'];
            $string[$i] =get_salesman_name($myrow2['salesman_code']);

            if( $data[0]>0){$data[$i]=$myrow2['charges']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow2['charges']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow2['charges']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow2['charges']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow2['charges']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow2['charges']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow2['charges']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow2['charges']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow2['charges']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow2['charges']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=get_salesman_name($myrow2['salesman_code']); }else{$string[9]='no';} //9


            $i++;
            ;}

        echo '
				   <script>
  var pieChartCanvas = $("#pieChart12").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";



        echo"<div class='col-md-3 col-sm-12 col-xs-12'>";
        echo'<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Zone Balances</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart9" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //

//		if ($to == null)
        $todate = date("Y-m-d");
//	else
//		$todate = date2sql($to);
        $past1 = get_company_pref('past_due_days');
        $past2 = 2 * $past1;
        // removed - debtor_trans.alloc from all summations
//	if ($all)
//    	$value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1)
//    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh),0)";
//    else
        $value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - 
    		trans.alloc),0)";
        $due = "IF (trans.type=10, trans.due_date, trans.tran_date)";
        $sql = "SELECT ".TB_PREF."areas.area_code,

		Sum(IFNULL($value,0)) AS Balance,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2

		FROM ".TB_PREF."debtors_master
		     INNER JOIN ".TB_PREF."cust_branch
		     ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		     INNER JOIN ".TB_PREF."areas
			 ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		     INNER JOIN ".TB_PREF."salesman
			 ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code
			 LEFT JOIN ".TB_PREF."debtor_trans trans ON 
			 trans.tran_date <= '$todate' AND ".TB_PREF."debtors_master.debtor_no = trans.debtor_no AND trans.type <> 13
,
			 ".TB_PREF."payment_terms,
			 ".TB_PREF."credit_status

		WHERE
			 ".TB_PREF."debtors_master.payment_terms = ".TB_PREF."payment_terms.terms_indicator
 			 AND ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			 ";
        $sql .= " GROUP BY ".TB_PREF."areas.area_code";
        $sql .= " ORDER BY Balance DESC LIMIT 10";



        $result2 = db_query($sql,"The client details could not be retrieved");
        $i = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow2 = db_fetch($result2))
        {
            echo '<li><a style="font-size:12px;" href="#">'.get_area_name($myrow2['area_code']).'<span class="pull-right text-red" id="#value">'.number_format($myrow2['Balance']).'</span></a></li>';
            $data[$i]=$myrow2['Balance'];
            $string[$i] =get_area_name($myrow2['area_code']);
            if( $data[0]>0){$data[$i]=$myrow2['Balance']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow2['Balance']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow2['Balance']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow2['Balance']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow2['Balance']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow2['Balance']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow2['Balance']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow2['Balance']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow2['Balance']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow2['Balance']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=get_area_name($myrow2['area_code']); }else{$string[9]='no';} //9

            $i++;
            ;}
        echo '
				   <script>
  var pieChartCanvas = $("#pieChart9").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo'</div>';//Main div

        echo'<div style="">';


        echo"<div class='col-md-12'>";

        include_once("./themes/".user_theme()."/Dashboard_Widgets/invoicesWidget2.php");
        $_Invoices = new invoiceswidgets2();
        $_Invoices->AllInvoices2();

        echo"</div>";


        echo'</div>';
    }
    public function Suppliers()
    {
        echo'<div style="float: left;width: 100%;" class="col-lg-12;">';




        echo"<div class='col-md-3 col-sm-10 col-xs-12'>";
        echo'<div class="box box-info"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Customers</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart1" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/
        $created_by = $_SESSION["wa_current_user"]->user;

        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM((ov_amount + ov_discount) * rate*IF(trans.type = ".ST_CUSTCREDIT.", -1, 1)) AS total,d.debtor_no, d.name FROM
		".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d WHERE trans.debtor_no=d.debtor_no
		AND (trans.type = ".ST_SALESINVOICE." OR trans.type = ".ST_CUSTCREDIT.")
		AND tran_date >= '$begin1' AND tran_date <= '$today1' GROUP by d.debtor_no ORDER BY total DESC, d.debtor_no 
		LIMIT 10";

        $result = db_query($sql);
        $i = 0;
        $data = array();
        $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';

            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
        }


        echo '
				   <script>
  var pieChartCanvas = $("#pieChart1").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';




        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo"<div class='col-md-3  col-sm-12 col-xs-12'>";
        echo'<div class="box box-warning"> 
                <div class="box-header with-border">';
        echo"<h3 class='box-title'>Clients's Profitability</h3>";
        echo' <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart130" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        $begin = begin_fiscalyear();
        $begin1 = date2sql($begin);
        $today = Today();
        $today1 = date2sql($today);
        $sql = "SELECT ".TB_PREF."debtors_master.name AS debtor_name, (SUM(-".TB_PREF."stock_moves.qty*".TB_PREF."stock_moves.price*(1-".TB_PREF."stock_moves.discount_percent))-SUM(-IF(".TB_PREF."stock_moves.standard_cost <> 0, ".TB_PREF."stock_moves.qty * ".TB_PREF."stock_moves.standard_cost, ".TB_PREF."stock_moves.qty *(".TB_PREF."stock_master.material_cost + ".TB_PREF."stock_master.labour_cost + ".TB_PREF."stock_master.overhead_cost)))) as cntrbt FROM ".TB_PREF."stock_master, ".TB_PREF."stock_category, ".TB_PREF."debtor_trans, ".TB_PREF."debtors_master, ".TB_PREF."stock_moves WHERE ".TB_PREF."stock_master.stock_id=".TB_PREF."stock_moves.stock_id AND ".TB_PREF."stock_master.category_id=".TB_PREF."stock_category.category_id AND ".TB_PREF."debtor_trans.debtor_no=".TB_PREF."debtors_master.debtor_no AND ".TB_PREF."stock_moves.type=".TB_PREF."debtor_trans.type AND ".TB_PREF."stock_moves.trans_no=".TB_PREF."debtor_trans.trans_no AND ".TB_PREF."stock_moves.tran_date>='$begin1'
	AND ".TB_PREF."stock_moves.tran_date<='$today1' AND (".TB_PREF."debtor_trans.type=13 OR ".TB_PREF."stock_moves.type=11) AND (".TB_PREF."stock_master.mb_flag='B' OR ".TB_PREF."stock_master.mb_flag='M') 
			 ";
        $sql .= " GROUP BY ".TB_PREF."debtors_master.debtor_no";
        $sql .= " ORDER BY cntrbt DESC LIMIT 10";



        $result2 = db_query($sql,"The clients details could not be retrieved");
        $i = 0;
//					 $string = array();
//		             $data = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

//		$color1 = array("#f56954", "#00a65a", "#f39c12", "#00c0ef", "#d2d6de", "#f56954", "#00a65a", "#f39c12", "#00c0ef", "#d2d6de");
        while ($myrow2 = db_fetch($result2))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow2['debtor_name'].'<span class="pull-right text-red" id="#value">'.number_format($myrow2['cntrbt']).'</span></a></li>';
            $data[$i]=$myrow2['cntrbt'];
            $string[$i] =$myrow2['debtor_name'];
//if( $data[0]>0){$data[$i]=$myrow2['charges']; }else{$data[0]=2;} //0
            if( $data[0]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[0]=2;} //0
            if( $data[1]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[1]=2;} //1
            if( $data[2]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[2]=2;} //2
            if( $data[3]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[3]=2;} //3
            if( $data[4]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[4]=2;} //4
            if( $data[5]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[5]=2;} //5
            if( $data[6]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[6]=2;} //6
            if( $data[7]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[7]=2;} //7
            if( $data[8]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[8]=2;} //8
            if( $data[9]>0){$data[$i]=$myrow2['cntrbt']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow2['debtor_name']; }else{$string[9]='no';} //9

            $i++;
            ;}

        echo '
				   <script>
  var pieChartCanvas = $("#pieChart130").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
  ]; 
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";


        //area wise debit

        echo"<div class='col-md-3  col-sm-10 col-xs-10'>";
        echo'<div class="box box-success"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Supplier</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                  
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart6" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/
        $created_by = $_SESSION["wa_current_user"]->user;

        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM((trans.ov_amount + trans.ov_discount) * rate) AS total, s.supplier_id, s.supp_name FROM
			".TB_PREF."supp_trans AS trans, ".TB_PREF."suppliers AS s WHERE trans.supplier_id=s.supplier_id
			AND (trans.type = ".ST_SUPPINVOICE." OR trans.type = ".ST_SUPPCREDIT." OR trans.type = ".ST_SUPPCREDIT_IMPORT.")
			AND tran_date >= '$begin1' AND tran_date <= '$today1' GROUP by s.supplier_id ORDER BY total DESC, s.supplier_id 
			LIMIT 10";

        $result = db_query($sql);
        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['supp_name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>  ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['supp_name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['supp_name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['supp_name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['supp_name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['supp_name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['supp_name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['supp_name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['supp_name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['supp_name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['supp_name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['supp_name']; }else{$string[9]='no';} //9
            $i++;
            ;}


        echo '
				   <script>
  var pieChartCanvas = $("#pieChart6").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';




        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo"<div class='col-md-3  col-sm-10 col-xs-10'>";
        echo'<div class="box box-warning"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Items Profitability</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    
                  </div>
                </div><!-- /.box-header -->
                 
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart333" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/
        $created_by = $_SESSION["wa_current_user"]->user;

        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM((trans.unit_price * trans.quantity) * d.rate) AS total, s.stock_id, s.description, 
			SUM(trans.quantity) AS qty FROM
			".TB_PREF."debtor_trans_details AS trans, ".TB_PREF."stock_master AS s, ".TB_PREF."debtor_trans AS d 
			WHERE trans.stock_id=s.stock_id AND trans.debtor_trans_type=d.type AND trans.debtor_trans_no=d.trans_no
			AND (d.type = ".ST_SALESINVOICE." OR d.type = ".ST_CUSTCREDIT.") ";
//		if ($manuf)
//			$sql .= "AND s.mb_flag='M' ";
        $sql .= "AND d.tran_date >= '$begin1' AND d.tran_date <= '$today1' GROUP by s.stock_id ORDER BY total DESC, s.stock_id 
			LIMIT 10";
        $result = db_query($sql);
        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['description'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['description'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['description']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['description']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['description']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['description']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['description']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['description']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['description']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['description']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['description']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['description']; }else{$string[9]='no';} //9
            $i++;
        }


        echo '
				   <script>
  var pieChartCanvas = $("#pieChart333").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';




        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";

        echo'</div>'; // Main Div

        echo'<div>';

        echo"<div class='row'>"; /** row starts **/

        echo"<div class='col-md-12'>";

        include_once("./themes/".user_theme()."/Dashboard_Widgets/invoicesWidget3.php");
        $_Invoices = new invoiceswidgets3();

        $_Invoices->AllInvoices3();

        echo"</div>";
        echo"</div>"; /** row ends here */

        echo'</div>';
    }


    public function cost_center()
    {
        echo '<div>';

        echo"<div class='col-md-3  col-sm-12 col-xs-12' >";
        echo'<div class="box box-success"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Cost Centres</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                
               
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart200" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM(-t.amount) AS total, d.reference, d.name FROM
			".TB_PREF."gl_trans AS t,".TB_PREF."dimensions AS d WHERE
			(t.dimension_id = d.id OR t.dimension2_id = d.id) AND
			t.tran_date >= '$begin1' AND t.tran_date <= '$today1' GROUP BY d.id ORDER BY total DESC LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">'.number_format($myrow['total']).'</span></a></li>';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
        }

        echo '
<script>
  var pieChartCanvas = $("#pieChart200").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';

        echo"</div>";

        echo"<div class='col-md-3  col-sm-10 col-xs-10'>";
        echo'<div class="box box-warning"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Sold Items</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    
                  </div>
                </div><!-- /.box-header -->
                  
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-10">
                      <div class="chart-responsive">
                          <canvas id="pieChart31" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';

        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM((trans.unit_price * trans.quantity) * d.rate) AS total, s.stock_id, s.description, 
			SUM(trans.quantity) AS qty FROM
			".TB_PREF."debtor_trans_details AS trans, ".TB_PREF."stock_master AS s, ".TB_PREF."debtor_trans AS d 
			WHERE trans.stock_id=s.stock_id AND trans.debtor_trans_type=d.type AND trans.debtor_trans_no=d.trans_no
			AND (d.type = ".ST_SALESINVOICE." OR d.type = ".ST_CUSTCREDIT.") ";
//		if ($manuf)
//			$sql .= "AND s.mb_flag='M' ";
        $sql .= "AND d.tran_date >= '$begin1' AND d.tran_date <= '$today1' GROUP by s.stock_id ORDER BY total DESC, s.stock_id 
			LIMIT 10";
        $result = db_query($sql);
        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow = db_fetch($result))
        {
            echo '<li>
                    <a style="font-size:12px;" href="#">
                    <span class="text_short">'.$myrow['description'].'</span>                    
                    <span class="pull-right text-red" id="#value">'.number_format($myrow['total']).'</span></a>
                    </li>';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['description'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['description']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['description']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['description']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['description']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['description']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['description']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['description']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['description']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['description']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['description']; }else{$string[9]='no';} //9
            $i++;
            ;}

        echo '
				   <script>
  var pieChartCanvas = $("#pieChart31").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }

  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";


        echo'</div>';//main div

        echo'<div >';
        echo"<div class='col-md-12'>";


        echo"</div>";
        echo'</div>';
    }

//     public function Items()
//     {
//      //ansar today /
//   /*  echo ' <script>
// function showUser(str) {
//     if (str == "") {
//         document.getElementById("txtHint").innerHTML = "";
//         return;
//     } else {
//         if (window.XMLHttpRequest) {
//             // code for IE7+, Firefox, Chrome, Opera, Safari
//             xmlhttp = new XMLHttpRequest();
//         } else {
//             // code for IE6, IE5
//             xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
//         }
//         xmlhttp.onreadystatechange = function() {
//             if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
//                 document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
//             }
//         };
//         xmlhttp.open("GET","ChartsDonuts.php?q="+str,true);
//         xmlhttp.send();




//     }
// }
// </script> ';  */
//  //window.location.href = "./index.php"; alert(str);
//  //window.location.href = "./themes/grayblue/Dashboard_Widgets/ChartsDonuts.php";
// //var_dump($_GET['q']);


//     }
    public function General()
    {



        echo"<div class='col-md-6  col-sm-12 col-xs-12' >";
        echo'<div class="box box-success"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Cost Centres</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
                
               
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                        <canvas id="pieChart200" height="200" width="169" style="width: 169px; height: 200px;"></canvas>
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">';
        //
        /*$sql="SELECT emp_name, emp_code from 0_employee LIMIT 3";
               $result = db_query($sql);*/
        $created_by = $_SESSION["wa_current_user"]->user;

        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT SUM(-t.amount) AS total, d.reference, d.name FROM
			".TB_PREF."gl_trans AS t,".TB_PREF."dimensions AS d WHERE
			(t.dimension_id = d.id OR t.dimension2_id = d.id) AND
			t.tran_date >= '$begin1' AND t.tran_date <= '$today1' GROUP BY d.id ORDER BY total DESC LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
//					 $data = array();
//					 $string = array();
        $data[0] = $data[1]  = $data[2]  = $data[3]  = $data[4]  = $data[5]  = $data[6]  = $data[7]  = $data[8] =  $data[9] = 0;
        $string[0] = $string[1] = $string[2] = $string[3] = $string[4] = $string[5] = $string[6] = $string[7] = $string[8] = $string[9] = 0;

        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}


        echo '
				   <script>
  var pieChartCanvas = $("#pieChart200").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 50, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';




        echo' </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->';
        echo"</div>";


    }

    public function HR()
    {
//
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can1" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee` 
ORDER BY basic_salary DESC 
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}



        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">


 
  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';




        echo '
				   <script>
  var pieChartCanvas = $("#can1").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';


//<!-- 2 -->
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can2" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee` 
ORDER BY basic_salary DESC 
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}


        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">

 
  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';



        echo '
				   <script>
  var pieChartCanvas = $("#can2").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';



//<!-- 3 -->
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary"> 
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                   
                  </div>
                </div><!-- /.box-header -->
               
                
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can3" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee` 
ORDER BY basic_salary DESC 
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>
					
                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}

        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">

 
  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';


        echo '
				   <script>
  var pieChartCanvas = $("#can3").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';
    }


    public function Payroll()
    {
//-----1-----
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

                  </div>
                </div><!-- /.box-header -->


                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can4" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee`
ORDER BY basic_salary DESC
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>

                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}


        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">
 

  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';

        echo '
				   <script>
  var pieChartCanvas = $("#can4").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';


//<!-- 2 -->
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

                  </div>
                </div><!-- /.box-header -->


                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can5" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee`
ORDER BY basic_salary DESC
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>

                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}


        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">



  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';



        echo '
				   <script>
  var pieChartCanvas = $("#can5").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

//<!-- 3 -->
        echo'<div id="box">
<div class=\'col-md-4  col-sm-12 col-xs-12\'>
  	<div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">Top 10 Bank Position</h3>
                  <div class="box-tools pull-right">
                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>

                  </div>
                </div><!-- /.box-header -->


                <div class="box-body">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="chart-responsive">
                      </div><!-- ./chart-responsive -->
                    </div><!-- /.col -->
                    <div class="content">


<div id="circle">
<canvas id="can6" width="180" height="180" />
</div>';
        $begin = begin_fiscalyear();
        $today = Today();
        $begin1 = date2sql($begin);
        $today1 = date2sql($today);
        $sql = "SELECT  `emp_name` as name ,  `basic_salary` as total
FROM  `0_employee`
ORDER BY basic_salary DESC
LIMIT 10";
        $result = db_query($sql, "Transactions could not be calculated");


        $i = 0;
        $data = array();
        $string = array();
        while ($myrow = db_fetch($result))
        {
            echo '<li><a style="font-size:12px;" href="#">'.$myrow['name'].'<span class="pull-right text-red" id="#value">                      '.number_format($myrow['total']).'</span></a></li>

                   ';
            $data[$i] = $myrow['total'];
            $string[$i] =$myrow['name'];
            if( $data[0]!=0){$data[$i]=$myrow['total']; }else{$data[0]=2;} //0
            if( $data[1]!=0){$data[$i]=$myrow['total']; }else{$data[1]=2;} //1
            if( $data[2]!=0){$data[$i]=$myrow['total']; }else{$data[2]=2;} //2
            if( $data[3]!=0){$data[$i]=$myrow['total']; }else{$data[3]=2;} //3
            if( $data[4]!=0){$data[$i]=$myrow['total']; }else{$data[4]=2;} //4
            if( $data[5]!=0){$data[$i]=$myrow['total']; }else{$data[5]=2;} //5
            if( $data[6]!=0){$data[$i]=$myrow['total']; }else{$data[6]=2;} //6
            if( $data[7]!=0){$data[$i]=$myrow['total']; }else{$data[7]=2;} //7
            if( $data[8]!=0){$data[$i]=$myrow['total']; }else{$data[8]=2;} //8
            if( $data[9]!=0){$data[$i]=$myrow['total']; }else{$data[9]=2;} //9							 //user
            if( $string[0]!=''){$string[$i]=$myrow['name']; }else{$string[0]='no';} //0
            if( $string[1]!=''){$string[$i]=$myrow['name']; }else{$string[1]='no';} //1
            if( $string[2]!=''){$string[$i]=$myrow['name']; }else{$string[2]='no';} //2
            if( $string[3]!=''){$string[$i]=$myrow['name']; }else{$string[3]='no';} //3
            if( $string[4]!=''){$string[$i]=$myrow['name']; }else{$string[4]='no';} //4
            if( $string[5]!=''){$string[$i]=$myrow['name']; }else{$string[5]='no';} //5
            if( $string[6]!=''){$string[$i]=$myrow['name']; }else{$string[6]='no';} //6
            if( $string[7]!=''){$string[$i]=$myrow['name']; }else{$string[7]='no';} //7
            if( $string[8]!=''){$string[$i]=$myrow['name']; }else{$string[8]='no';} //8
            if( $string[9]!=''){$string[$i]=$myrow['name']; }else{$string[9]='no';} //9
            $i++;
            ;}

        echo '
</div>
</div>
                  </div><!-- /.row -->
                </div><!-- /.box-body -->
                <div class="box-footer no-padding">
                  <ul class="nav nav-pills nav-stacked">

  </ul>
                </div><!-- /.footer -->
              </div><!-- /.box -->
  </div>';

        echo '
				   <script>
  var pieChartCanvas = $("#can6").get(0).getContext("2d");
  var pieChart = new Chart(pieChartCanvas);
	var PieData = [
    {
      value: '.$data[0].',
      color: "#f56954",
      highlight: "#f56954",
      label: "'.$string[0].'"
    },
    {
      value: '.$data[1].',
      color: "#00a65a",
      highlight: "#00a65a",
      label: "'.$string[1].'"
    },
    {
      value: '.$data[2].',
      color: "#f39c12",
      highlight: "#f39c12",
      label: "'.$string[2].'"
    },
    {
      value: '.$data[3].',
      color: "#00c0ef",
      highlight: "#00c0ef",
      label: "'.$string[3].'"
    },
    {
      value: '.$data[4].',
      color: "#3c8dbc",
      highlight: "#3c8dbc",
      label: "'.$string[4].'"
    },
    {
      value: '.$data[5].',
      color: "#d2d6de",
      highlight: "#d2d6de",
      label: "'.$string[5].'"
    },
	{
      value: '.$data[6].',
      color: "#82E0FF",
      highlight: "#82E0FF",
      label: "'.$string[6].'"
    },
	{
      value: '.$data[7].',
      color: "#4141FF",
      highlight: "#4141FF",
      label: "'.$string[7].'"
    },
	{
      value: '.$data[8].',
      color: "#00AAAA",
      highlight: "#00AAAA",
      label: "'.$string[8].'"
    },
	{
      value: '.$data[9].',
      color: "#7575A3",
      highlight: "#7575A3",
      label: "'.$string[9].'"
    }
 
  ];
  var pieOptions = {
    //Boolean - Whether we should show a stroke on each segment
    segmentShowStroke: true,
    //String - The colour of each segment stroke
    segmentStrokeColor: "#fff",
    //Number - The width of each segment stroke
    segmentStrokeWidth: 1,
    //Number - The percentage of the chart that we cut out of the middle
    percentageInnerCutout: 0, // This is 0 for Pie charts
    //Number - Amount of animation steps
    animationSteps: 100,
    //String - Animation easing effect
    animationEasing: "easeOutBounce",
    //Boolean - Whether we animate the rotation of the Doughnut
    animateRotate: true,
    //Boolean - Whether we animate scaling the Doughnut from the centre
    animateScale: false,
    //Boolean - whether to make the chart responsive to window resizing
    responsive: true,
    // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
    maintainAspectRatio: false,
    //String - A legend template
    legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>",
    //String - A tooltip template
    tooltipTemplate: "<%=value %> <%=label%> users"
  };
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  pieChart.Doughnut(PieData, pieOptions);
  </script>';

    }


}
?>