<?php
global $path_to_root;
$path_to_root="../../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");

include_once($path_to_root ."/themes/premium/dashboard_db.php");


$today = Today();
$date = date2sql($today);

$last_date = date('d-m-Y', strtotime('-1 days'));


$mo = date('m', strtotime($date));
$yr = date('Y', strtotime($date));
$mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);

$this_month_start_date = date('d-m-Y', mktime(0, 0, 0, $mo, 1, $yr));
if ($mon_days == 30)
    $this_month_end_date = date('d-m-Y', mktime(0, 0, 0, $mo, 30, $yr));
else
    $this_month_end_date = date('d-m-Y', mktime(0, 0, 0, $mo, 31, $yr));


$mo = date('m', strtotime($date));
$mo = $mo - 1;
$yr = date('Y', strtotime($date));
$mon_days = cal_days_in_month(CAL_GREGORIAN, $mo, $yr);
$last_month_start_date = date('d-m-Y', mktime(0, 0, 0, $mo, 1, $yr));

if ($mon_days == 30)
    $last_month_end_date = date('d-m-Y', mktime(0, 0, 0, $mo, 30, $yr));
else
    $last_month_end_date = date('d-m-Y', mktime(0, 0, 0, $mo, 31, $yr));


echo'<div>';
echo'<h4>Item Valuation</h4>';
echo'<table class="table tb" style="color: white">';
echo'<thead class="bg-blue"><tr><td>Today</td><td>Yesterday</td><td>This Month</td><td>Last Month</td></tr></thead>';
echo'<tr><tbody style="color:white;">';
echo'<td>'.number_format2(get_item_data($today,$today) , 2).'</td>
                    <td> '.number_format2(get_item_data($last_date,$last_date),2).'</td>
                    <td> '.number_format2(get_item_data($this_month_start_date,$this_month_end_date),2).'</td>
                    <td> '.number_format2(get_item_data($last_month_start_date,$last_month_end_date),2).'</td>';
echo'</tr></tbody></table>';
echo'</div>';


//Profit and loss
echo "<div >";
echo'<h4>Profit And Loss</h4>';
echo'<style>
         #dd :hover{background-color:#1a2226;}
         .tb tbody tr:hover{background-color: #1b1f26;}.tb tbody {color: whitesmoke;}
         .tb thead{color:#b8c7ce;}
 </style>';
//            $path_to_root="../..";
//            include_once($path_to_root . "/gl/includes/gl_db.inc");

$k = 0; // row color
echo'<table class="table tb" style="color: white">';
echo'<thead class="bg-blue"><tr><td>Name</td><td>Today</td><td>Yesterday</td><td>This Month</td><td>Last Month</td></tr></thead>';
echo'<tr><tbody style="color:white;">';
//Get classes for PL
$classresult = get_account_classes(false, 0);
while ($class = db_fetch($classresult)) {
    $typeresult = get_account_types(false, $class['cid'], -1);
    while ($accounttype = db_fetch($typeresult)) {

        $today_data = get_gl_sum_account_type($accounttype["id"], $today, $today);
        $yesterdayday = get_gl_sum_account_type($accounttype["id"], $last_date, $last_date);
        $this_month = get_gl_sum_account_type($accounttype["id"], $this_month_start_date, $this_month_end_date);
        $last_month = get_gl_sum_account_type($accounttype["id"], $last_month_start_date, $last_month_end_date);

        if($today_data !='' || $yesterdayday!=''|| $this_month!=''|| $last_month!='')
        {
            $url = "<a href='$path_to_root/gl/inquiry/profit_loss.php?TransFromDate="
                . $from . "&TransToDate=" . $to . "&Compare=" . $compare . "&Dimension=" . $dimension . "&Dimension2=" . $dimension2
                . "&AccGrp=" . $accounttype['id'] . "' target='_blank' style='color:whitesmoke;'>" . $accounttype['id'] . " " . $accounttype['name'] . "</a>";
            echo '<td>' . $url . '</td>';
            echo '<td>' . number_format2(abs($today_data)) . '</td>';
            echo '<td>' . number_format2(abs($yesterdayday)) . '</td>';
            echo '<td>' . number_format2(abs($this_month)) . '</td>';
            echo '<td>' . number_format2(abs($last_month)) . '</td>';
        }

        end_row();

    }
}
echo'</tbody>';
echo'</tr></table>';


echo"</div>";

echo'</div>';

?>