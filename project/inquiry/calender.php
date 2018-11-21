<?php
$page_security = 'SS_CRM_QUERY_C';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
page(_($help_context = "Calendar"));
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/project/includes/db/task_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


simple_page_mode(true);
//$fdate='2014-06-21';
function getNewsData2($fdate)
{
	$result1=get_sales_order_information($fdate);
	$loopdata = '';
	while($row = mysql_fetch_array($result1))
	{
		//$loopdata .='<table border=1px bgcolor="#17309A"><tr><td>'.get_customer_name($row['0']).'</td> <td>'.$row['1'].'</td> <td>'.$row['2'].'</td></tr></table>';
		$loopdata .='<html>
   <head>
      <style>
         table.fixed {table-layout:fixed; width:90px;}/*Setting the table width is important!*/
         table.fixed td {overflow:hidden;}/*Hide text outside the cell.*/
         table.fixed td:nth-of-type(1) {width:40px;}/*Setting the width of column 1.*/
         table.fixed td:nth-of-type(2) {width:30px;}/*Setting the width of column 2.*/
         table.fixed td:nth-of-type(3) {width:35px;}/*Setting the width of column 3.*/
         table.fixed 
         {
    border-style: solid;
    border-color: #17309A
}


	table.fixed{
		width:100%; 
		border-collapse:collapse; 
	}
	table.fixed td{ 
		padding:7px; border:#4e95f4 1px solid;
	}
	/* provide some minimal visual accomodation for IE8 and below */
	table.fixed tr{
		background: #b8d1f3;
	}
	/*  Define the background color for all the ODD background rows  */
	table.fixed tr:nth-child(odd){ 
		background: #b8d1f3;
	}
	/*  Define the background color for all the EVEN background rows  */
	table.fixed tr:nth-child(even){
		background: #dae5f4;
	}


      </style>
   </head>
   <body>
      <table class="fixed" border=1px bgcolor="#90bade">
         <tr><td>'.get_customer_name($row['0']).'</td> <td>'.$row['1'].'</td> <td>'.$row['2'].'</td></tr>
      </table>
   </body>
</html>';
	}
	return $loopdata;
}
label_cell("<center> <a href=../inquiry/calendar_inquiry.php? style=\"color: #CC0000\">GO TO CALENDAR INQUIRY</a> </center>");

class Calendar
{
	var $events;

	function Calendar($date)
	{
		if(empty($date)) $date = time();
		define('NUM_OF_DAYS', date('t',$date));
		define('CURRENT_DAY', date('j',$date));
		define('CURRENT_MONTH_A', date('F',$date));
		define('CURRENT_MONTH_N', date('n',$date));
		define('CURRENT_YEAR', date('Y',$date));
		define('START_DAY', (int) date('N', mktime(0,0,0,CURRENT_MONTH_N,1, CURRENT_YEAR)) - 1);
		define('COLUMNS', 7);
		define('PREV_MONTH', $this->prev_month());
		define('NEXT_MONTH', $this->next_month());
		$this->events = array();
	}

	function prev_month()
	{
		return mktime(0,0,0,
			(CURRENT_MONTH_N == 1 ? 12 : CURRENT_MONTH_N - 1),
			(checkdate((CURRENT_MONTH_N == 1 ? 12 : CURRENT_MONTH_N - 1), CURRENT_DAY, (CURRENT_MONTH_N == 1 ? CURRENT_YEAR - 1 : CURRENT_YEAR)) ? CURRENT_DAY : 1),
			(CURRENT_MONTH_N == 1 ? CURRENT_YEAR - 1 : CURRENT_YEAR));
	}

	function next_month()
	{
		return mktime(0,0,0,
			(CURRENT_MONTH_N == 12 ? 1 : CURRENT_MONTH_N + 1),
			(checkdate((CURRENT_MONTH_N == 12 ? 1 : CURRENT_MONTH_N + 1) , CURRENT_DAY ,(CURRENT_MONTH_N == 12 ? CURRENT_YEAR + 1 : CURRENT_YEAR)) ? CURRENT_DAY : 1),
			(CURRENT_MONTH_N == 12 ? CURRENT_YEAR + 1 : CURRENT_YEAR));
	}

	function getEvent($timestamp)
	{
		$event = NULL;
		if(array_key_exists($timestamp, $this->events))
			$event = $this->events[$timestamp];
		return $event;
	}

	function addEvent($event, $day = CURRENT_DAY, $month = CURRENT_MONTH_N, $year = CURRENT_YEAR)
	{
		$timestamp = mktime(0, 0, 0, $month, $day, $year);
		if(array_key_exists($timestamp, $this->events))
			array_push($this->events[$timestamp], $event);
		else
			$this->events[$timestamp] = array($event);
	}

	function makeEvents()
	{
		if($events = $this->getEvent(mktime(0, 0, 0, CURRENT_MONTH_N, CURRENT_DAY, CURRENT_YEAR)))
			foreach($events as $event) echo $event.'<br />';
	}


	function makeCalendar()
	{
		echo '<html>
   <head>
      <style>@import "compass/css3";

table {
  
  margin: 25px auto;
  border-collapse: collapse;
  border: 1px solid #eee;
  border-bottom: 2px solid #00cccc;
  
  tr {
     &:hover {
      background: #f4f4f4;
      
      td {
        color: #555;
      }
    }
  }
  th, td {
    color: #999;
    border: 1px solid #eee;
    padding: 5px 35px;
    border-collapse: collapse;
  }
  th {
    background: #00cccc;
    color: #fff;
    text-transform: uppercase;
    font-size: 25px;
    &.last {
      border-right: none;
    }
  }
}</style>
<script>
$("table tr").each(function(){
  $(this).find("th").first().addClass("first");
  $(this).find("th"td").first().addClass("first");
  $(this).find("td").last().addClass("last");
});

$("table tr").first().addClass("row-first");
$("table tr").last().addClass("row-last");
</script>
   </head>';
		echo '<table border="1" cellspacing="50" width="100%"><tr height="30%">';
		echo '<td width="15%"><a href="?date='.PREV_MONTH.'">&lt;&lt;</a></td>';
		echo '<td colspan="5" style="text-align:center" width="70%" >'.CURRENT_MONTH_A .' - '. CURRENT_YEAR.'</td>';
		echo '<td width="15%"><a href="?date='.NEXT_MONTH.'">&gt;&gt;</a></td>';
		echo '</tr><tr height="50">';
		echo '<th width="14%">Mon</th>';
		echo '<th width="14%">Tue</th>';
		echo '<th width="14%">Wed</th>';
		echo '<th width="14%">Thu</th>';
		echo '<th width="14%">Fri</th>';
		echo '<th width="14%">Sat</th>';
		echo '<th width="14%">Sun</th>';
		echo '</tr><tr height="80">';
		echo str_repeat('<td>&nbsp;</td>', START_DAY);

		$rows = 1;

		for($i = 1; $i <= NUM_OF_DAYS; $i++)
		{
			if($i == CURRENT_DAY)
			{
				$date = date('Y-m-d',(mktime(0, 0, 0, CURRENT_MONTH_N, $i, CURRENT_YEAR)));
				$count_func_code = get_count_func($date);

				$order_date = date('d-m-Y',(mktime(0, 0, 0, CURRENT_MONTH_N, $i, CURRENT_YEAR)));

				$icon = false;
				$rep = 1016;
				$link_text = $i;
				$id='prtopt';
				$class = 'printlink';
				$dir = '';
				// from, to, currency, email, quote, comments, orientation
				$pars = array(
					'date' => $order_date,
					'type' => "event",
					);

				global $path_to_root, $pdf_debug;

				$url = $dir == '' ?  $path_to_root.'/project/task.php?' : $dir;

				$id = default_focus($id);
				foreach($pars as $par => $val) {
					$pars[$par] = "$par=".urlencode($val);
				}
				//$pars[] = 'REP_ID='.urlencode($rep);
				$url .= implode ('&', $pars);

				if ($class != '')
					$class = $pdf_debug ? '' : " class='$class'";
				if ($id != '')
					$id = " id='$id'";
				$pars = access_string($link_text);
				if (user_graphic_links() && $icon)
					$pars[0] = set_icon($icon, $pars[0]);

				echo "<td style='background-color:#8cacbb;'><a target='_blank' href='$url'$id $pars[1]>$pars[0]";
				$countS = 1 ;
				while($dt = db_fetch($count_func_code))
				{
					if($dt['is_cancel'] == 1)
						$back_color = '#FF0000';
					else
						$back_color = '#7FFF00';
//					if($dt['marge_new_ref_num'] != '' && $countS == 1)
//						echo '<div> <a href="../inquiry/customer_inquiry_deliveries.php?type=32&&today_date='.$order_date.'" target="_blank">View Dashboard</a></div>';

						echo' <div style="background-color:#dae5f4;border: 1px solid black;margin-top: 2px;">
<a href="../task.php?type=event&id='.$dt['order_no'].'" target="_blank" title='.$dt['description'].' >'.$dt="Time:".''.$dt['time'].'
<br><span style="color:	#A52A2A;">' .$dt="Person:".''.get_users_name($dt['user_id']).'
<br><span style="color:	#00008B;">'.get_cust_name($dt['debtor_no']).''."      ".' '.$dt['customer_'].' 
<br><span style="color:	#1D8348;">' .$dt="Amount:".''.$dt['amount'].' 
</span></a>

				  </div> ';

					$countS++;
				}
				echo'</td>';
			}
			//	echo '<td style="background-color: #C0C0C0"><strong>'.$i.'</strong></td>';
			//else if($event = $this->getEvent(mktime(0, 0, 0, CURRENT_MONTH_N, $i, CURRENT_YEAR)))
			else
			{

				$date = date('Y-m-d',(mktime(0, 0, 0, CURRENT_MONTH_N, $i, CURRENT_YEAR)));
				$count_func_code2 = get_count_func($date);
				$order_date = date('d-m-Y',(mktime(0, 0, 0, CURRENT_MONTH_N, $i, CURRENT_YEAR)));

				$icon = false;
				$rep = 1016;
				$link_text = $i;
				$id='prtopt';
				$class = 'printlink';
				$dir = '';
				// from, to, currency, email, quote, comments, orientation
				$pars = array(
					'date' => $order_date,
					'type' => "event",
					);

				global $path_to_root, $pdf_debug;

				$url = $dir == '' ?  $path_to_root.'/project/task.php?' : $dir;

				$id = default_focus($id);
				foreach($pars as $par => $val) {
					$pars[$par] = "$par=".urlencode($val);
				}
				//$pars[] = 'REP_ID='.urlencode($rep);
				$url .= implode ('&', $pars);

				if ($class != '')
					$class = $pdf_debug ? '' : " class='$class'";
				if ($id != '')
					$id = " id='$id'";
				$pars = access_string($link_text);
				if (user_graphic_links() && $icon)
					$pars[0] = set_icon($icon, $pars[0]);

					echo "<td><a target='_blank' href='$url'$id $pars[1]>$pars[0]";

				$count = 1 ;
				while($dt2 = db_fetch($count_func_code2))
				{
					if($dt2['marge_new_ref_num'] != '' && $count == 1){
//							echo '<div> <a href="../manage/add_waiter.php?date='.$order_date.'" target="_blank">Add Waiters</a></div>';
//							echo '<div> <a href="../manage/supervisor.php?date='.$order_date.'" target="_blank">Add Supervisor</a></div>';
//							echo '<div> <a href="../manage/vip_team.php?date='.$order_date.'" target="_blank">Add Vip Team</a></div>';
					}


//					if($dt2['marge_new_ref_num'] != '' && $count == 1)
//						echo '<div> <a href="../inquiry/customer_inquiry_deliveries.php?type=32&&today_date='.$order_date.'" target="_blank">View Dashboard</a></div>';

echo ' <div style="background-color:#dae5f4;border: 1px solid black;margin-top: 2px;">
<a href="../task.php?type=event&id='.$dt2['order_no'].'" target="_blank"  title='.$dt2['description'].' >'.$dt2="Time:".''.$dt2['time'].'
<br><span style="color:	#A52A2A;">' .$dt2="Person:".''.get_users_name($dt2['user_id']).'
<br><span style="color:	#00008B;">'.get_cust_name($dt2['debtor_no']).''."      ".' '.$dt2['customer_'].'
<br><span style="color:	#1D8348;">' .$dt2="Amount:".''.$dt2['amount'].'
</span></a>						
						</div>';
					$count++;
				}
				echo'</td>';
			}
			//	echo '<td><a href="?date='.mktime(0 ,0 ,0, CURRENT_MONTH_N, $i, CURRENT_YEAR).'">'.$i.'</a></td>';



			if((($i + START_DAY) % COLUMNS) == 0 && $i != NUM_OF_DAYS)
			{
				echo '</tr><tr height="80">';
				$rows++;
			}
		}
		echo str_repeat('<td>&nbsp;</td>', (COLUMNS * $rows) - (NUM_OF_DAYS + START_DAY)).'</tr></table></html>';
	}
}
//$date11=$_GET['date'];
if(empty($_GET['date'])) $_GET['date'] = time();


$cal = new Calendar($_GET['date']);

//$cal->addEvent('event 1');
//$cal->addEvent('event 2', 10);
//$cal->addEvent('event 3', 10, 10);
//$cal->addEvent('event 4', 10, 10, 10);
$cal->makeCalendar();
$cal->makeEvents();
//echo getNewsData2('2014-06-21');


end_page();

?>