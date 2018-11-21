<?php


$page_security = 'SS_DASHBOARD';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/modules/dashboard/charts/charts_utils.php");

$created_by = $_SESSION["wa_current_user"]->user;

$chart_type = 'AreaChart';

	if (isset($_GET['chart_type'])) {
		$chart_type = $_GET['chart_type'];
	} if (isset($_POST['chart_type'])) {
		$chart_type = $_POST['chart_type'];
	}
	
	$begin = begin_fiscalyear();
		$today = Today();
		$begin1 = date2sql($begin);
		$today1 = date2sql($today);
		$sql = "SELECT SUM(amount) AS total, c.class_name, c.ctype FROM
			".TB_PREF."gl_trans,".TB_PREF."chart_master AS a, ".TB_PREF."chart_types AS t, 
			".TB_PREF."chart_class AS c WHERE
			account = a.account_code AND a.account_type = t.id AND t.class_id = c.cid
			AND IF(c.ctype > 3, tran_date >= '$begin1', tran_date >= '0000-00-00') 
			AND tran_date <= '$today1' GROUP BY c.cid ORDER BY c.cid"; 
		$result = db_query($sql, "Transactions could not be calculated");
		$title = _("Profit and Loss");
		
		$totalRec = db_num_rows($result);
		//$totalRec = db_num_rows($result) -1;
		if ($totalRec < 0)
			$totalRec = 0;
	
	echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/widget-styles.css" />';
?>   

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:['table','corechart']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', '<?php echo _("Class"); ?>');
        data.addColumn('number', '<?php echo _("Amount"); ?>');
        data.addRows(<?php echo $totalRec; ?>);
        
                      <?php 
                      		$i = 0;
							$total = 0;
							while ($myrow = db_fetch($result))
							{
								if ($myrow['ctype'] > 3)
								{
							    	$total += $myrow['total'];
									$myrow['total'] = -$myrow['total'];
									?>
									data.setValue(<?php echo $i; ?>,0,'<?php echo $myrow['class_name']; ?>');									
									<?php
									if ($chart_type == 'PieChart') {						    	
							    	?>							    	
							    		data.setValue(<?php echo $i; ?>,1,<?php echo abs($myrow['total']); ?>);
		                  	        <?php } else { ?>	
		                  	      		data.setValue(<?php echo $i; ?>,1,<?php echo $myrow['total']; ?>);
		                  	        <?php
		                  	        }
							    	$i++;
							    }	
							}
							
							$calculated = _("Calculated Return");
							?>
							data.setValue(<?php echo $i; ?>,0,'<?php echo $calculated; ?>');
							data.setValue(<?php echo $i; ?>,1,<?php echo -$total; ?>);                  			
                  

        var formatter = new google.visualization.NumberFormat();
        formatter.format(data, 1);

        <?php if ($chart_type == 'Table') { ?>
	        var table = new google.visualization.Table(document.getElementById('chart_div'));
	        table.draw(data, null);
		<?php } else { ?>    
	        var chart = new google.visualization.<?php echo $chart_type; ?>(document.getElementById('chart_div'));
	        
	        <?php if ($chart_type == 'BarChart') { ?>
			        
			        chart.draw(data, {title: '<?php echo $title; ?>',
			        	chartArea:{left:100},        				  
			                          vAxis: {title: '<?php echo _("Class"); ?>', titleTextStyle: {color: 'red'}},
			                          hAxis: {title: '<?php echo _("Amount"); ?>', format:'#,###', titleTextStyle: {color: 'blue'}}
			                         });
	        <?php } else { ?>       
	
			        chart.draw(data, {title: '<?php echo $title; ?>',
			        				  chartArea:{left:100},		        
			        				  is3D: true,				          				  
			                          hAxis: {title: '<?php echo _("Class"); ?>', titleTextStyle: {color: 'red'}},
			                          vAxis: {title: '<?php echo _("Amount"); ?>', format:'#,###', titleTextStyle: {color: 'blue'}}
			                         });                
	        <?php } ?>
	    <?php } ?> 
      }

      function setOption(param) {           
    	  document.FormSwitch.chart_type.value = param;         
          document.FormSwitch.submit();
        }
      
    </script>
    
    <form name="FormSwitch" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        
        <div align="right">
        
	    <?php     	
	    	$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name FROM ".TB_PREF."bank_accounts";	
	    	$resultBank = db_query($sql, "Bank Account could not be retrieved");
	    	
	    	while ($bankRow = db_fetch($resultBank))
				{
	    			$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act= '" . $bankRow['id'] . "'";
	    			 
	    			$resultTotal = db_query($sql, "Bank Account Sum Amount could not be retrieved");
	    			$rowTotal = db_fetch_row($resultTotal);
	    			
	    			echo '<span class="headingtext">';
	    			echo _($bankRow['bank_account_name']);
	    			echo ' :&nbsp;' . price_format($rowTotal[0]) . '</span>&nbsp;&nbsp;';	    			
				}
			echo '&nbsp;&nbsp;';
    	?>
    
		<span class="headingtext"><?php echo _("View By"); ?>:&nbsp;</span>
		
			  <?php

			  $_arrchartType = array("AreaChart" => _("Area Chart"),
			  						 "BarChart" => _("Bar Chart"),	
			   				   		 "ColumnChart" => _("Column Chart"),
			  						 "LineChart" => _("Line Chart"),
			  						 "PieChart" => _("Pie Chart"),
			  						 "Table" => _("Table Data"));

			  echo getComboxBoxOnChangeEvent($_arrchartType, 'chart_type', '1', 'combo', 'setOption(this.options[this.selectedIndex].value);', trim($chart_type)) ?>
			  &nbsp;&nbsp;	  
		</div>
	</form>
 
    <div id="chart_div"  style="width: 550px; height: 300px;"></div>
    