<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/project/includes/db/task_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
//function page($title, $no_menu=false, $is_index=false, $onload="", $js="", $script_only=false, $css='')


if(user_theme() =='blackgold')
	page(_($help_context = "Task Inquiry"), true, false, "", $js);
else
	page(_($help_context = "Task Inquiry"), false, false, "", $js);


}
if (isset($_GET['FromDate'])){
	$_POST['from_date'] = $_GET['from_date'];
}
if (isset($_GET['ToDate'])){
	$_POST['to_date'] = $_GET['to_date'];
}
if (isset($_GET['status'])){
	$_POST['status'] = $_GET['status'];
}if (isset($_GET['status'])){
	$_SESSION['status'] = $_GET['status'];
}if (isset($_GET['user_id'])){
	$_POST['user_id'] = $_GET['user_id'];
}if (isset($_GET['user_id'])){
	$_SESSION['user_id'] = $_GET['user_id'];
}if (isset($_GET['debtor_no'])){
	$_POST['debtor_no'] = $_GET['debtor_no'];
}if (isset($_GET['debtor_no'])){
	$_SESSION['debtor_no'] = $_GET['user_id'];
}if (isset($_GET['unset_all'])){
	unset($_SESSION['status']);
}

if (isset($_GET['id'])){
	$_POST['id'] = $_GET['id'];
}
function get_task_nw($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."task WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get task");
	return db_fetch($result);
}
//------------------------------------------------------------------------------------------------
	//echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/project/project.css" />';
echo '
<style>

html {
	overflow-x: scroll;
visibility:visible;
position:absolute;

}
</style>
';

	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">';
	//echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">';
	//echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>';
	//echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>';
	echo '<style type="text/css">
    .bs-example{
    	margin: 20px;
    }
    .icon-input-btn{
        display: inline-block;
        position: relative;
    }
    .icon-input-btn input[type="submit"]{
        padding-left: 2em;
    }
    .icon-input-btn .glyphicon{
        display: inline-block;
        position: absolute;
        left: 0.65em;
        top: 30%;
    }
</style> ';

	echo '<script type="text/javascript">
$(document).ready(function(){
	$(".icon-input-btn").each(function(){
        var btnFont = $(this).find(".btn").css("font-size");
        var btnColor = $(this).find(".btn").css("color");
		$(this).find(".glyphicon").css("font-size", btnFont);
        $(this).find(".glyphicon").css("color", btnColor);
        if($(this).find(".btn-xs").length){
            $(this).find(".glyphicon").css("top", "24%");
        }
	}); 
});
</script> ';
//alert blink icon
echo '<style type="text/css">
.iconPM1 {
  background: url(https://raw.githubusercontent.com/stephenhutchings/typicons.font/493867748439a8e1ac3b92e275221f359577bd91/src/png-24px/warning-outline.png) center no-repeat;
  background-size: 16px 16px;
  width: 16px;
  height: 16px;
  border: none;
  display:inline-block;
  
  animation: blink 2s ease-in infinite;
}

@keyframes blink {
  from, to { opacity: 1 }
  50% { opacity: 0 }
}
</style>
';

/*
button1:before {
    content: attr(data-count);
    width: 20px;
    height: 20px;
    line-height: 18px;
    text-align: center;
    display: block;
    border-radius: 50%;
    background: red;
    border: 1px solid #FFF;
    box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    color: #FFF;
    position: absolute;
    top: -10px;
    left: -8px;
}
*/
echo '<style type="text/css">

button1 {
}

button1:before {
    content: attr(data-count);
    line-height: 18px;
    text-align: center;
    display: block;
    border-radius: 30%;
    background: #dd4b39;
    border: 1px solid #FFF;
    box-shadow: 0 1px 3px rgba(0,0,0,0.4);
    color: #FFF;
    position: absolute;
    top: -15px;
    left: 5px;
    padding: 1px 2px;

}

button1.animation:before {

    animation: blink 2s ease-in infinite;
}

button1.badge-top-right:before {
    left: auto;
    right: -7px;
}

button1.badge-bottom-right:before {
    left: auto;
    top: auto;
    right: -7px;
    bottom: -7px;
}

button1.badge-bottom-left:before {
    top: auto;
    bottom: -7px;
}
</style>
';

if (!@$_GET['popup'])
	start_form();

function get_last_update1($type)
{
	$sql = "SELECT Stamp, entry_user, id, task_id
	FROM ".TB_PREF."task_history
	WHERE task_type = ".db_escape($type)."
	ORDER BY id DESC
	LIMIT 1
	";

	$result = db_query($sql, "could not get last update");
	//$row = db_fetch_row($result);
	return $result;
}

$history_row = get_last_update1(1);
$get_last_history = db_fetch_row($history_row);

$last_user_id = $get_last_history[1];
$last_user_name = get_user_name($last_user_id);

$diff = dateDiff1($get_last_history[0]);

if($diff != 0)
{echo "<center> <u><b>"; 
echo viewer_link("Last edit was $diff ago by $last_user_name", "project/view/view_task_inquiry.php?");
echo "</b></u> </center>";
}
echo viewer_link("View Activity Stream", "project/inquiry/activity_stream.php?");


//widgets
if(user_theme() == premium)
{
echo '
<right>
 <!-- =========================================================== -->
      <div class="row" style="width: 1600px;">
        <div class="col-md-3 col-sm-16 col-xs-120">
          <div class="info-box bg-blue">
            <span class="info-box-icon2"><i class="fa fa-tasks"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">ASSIGNED TASKS</span>

          <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text"> '; echo "$tasks_added_today"; echo ' Today </span>
           <div class="progress">

                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text">'; echo "$tasks_added_yesterday"; echo ' Yesterday</span>  
         
          <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text"> '; echo "$tasks_added_week"; echo ' This Week</span>

   
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-green">
            <span class="info-box-icon2"><i class="fa fa-thumbs-o-up"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Completed Tasks</span>

           <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text">Today '; echo "$tasks_completed_today"; echo '</span>
             <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
  
              <span class="info-box-small-text">Yesterday '; echo "$tasks_completed_yesterday"; echo '</span>


          <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text"> '; echo "$tasks_completed_week"; echo ' This Week</span>
                

            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
 
       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-aqua">
            <span class="info-box-icon2"><i class="fa fa-check"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Approved Tasks</span>

           <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>

              <span class="info-box-small-text">Today '; echo "$tasks_approved_today"; echo '</span>

             <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
  
              <span class="info-box-small-text">Yesterday '; echo "$tasks_approved_yesterday"; echo '</span>

             <div class="progress">
                <div class="progress-bar" style="width: 100%"></div>
              </div>
  
              <span class="info-box-small-text">Yesterday '; echo "$tasks_approved_week"; echo '</span>
          

            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->

       <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box bg-red">
            <span class="info-box-icon2"><i class="fa fa-frown-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Oldest Tasks</span>



              <span class="info-box-xs-text">Today '; echo "DYS Solutions Task number file the attendance"; echo '</span>

              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number remove the bugs"; echo '</span>
          

              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number clean the database 1 month old"; echo '</span>
          

              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number clean the database 1 month old"; echo '</span>


              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number clean the database 1 month old"; echo '</span>


              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number clean the database 1 month old"; echo '</span>

              <span class="info-box-xs-text">Yesterday '; echo "DYS Solutions Task number clean the database 1 month old"; echo '</span>

            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
       </div>
      <!-- /.row -->



';
}
else
{
echo "<b>Tasks Assigned:</b> Today = $tasks_added_today, Yesterday = $tasks_added_yesterday";
echo " | ";
echo "<b>Tasks Completed: </b> Today = $tasks_completed_today, Yesterday = $tasks_completed_yesterday";
echo " | ";
echo "<b>Tasks Approved: </b>Today = $tasks_approved_today, Yesterday = $tasks_approved_yesterday";
echo "</br>";

}
/*
////ansar
echo '<button class="btn btn-info " type="submit" name="submit"> <span class="glyphicon glyphicon-align-left" aria-hidden="true"></span>Add New Task Here</button>';
if (isset($_POST['submit']))
{
//start_table(TABLESTYLE_NOBORDER);
echo '"<center> <div class="bs-example"><table class="table table-bordered background-color: red" style="background-color: GRAY"></div></center>"
';
echo'<tr>
    <td align="center"><button class="btn btn-success" type="submit" name="add">Submit</button></td>
    
  </tr>';
start_row();
customer_list_cells(_("*Customer: "), 'customer_id', null, _("Select Customer"), false, false, true);
pstatus_list_cells(_("*Status: "), 'status', 5, _("Select"));
duration_list_cells(_("*Plan: "), 'plan', null, _("Select"));
users_query_list_cells(_("*Assign To: "), 'user_id', null, _("Select"));			
end_row();
start_row();
date_cells(_("Start date:"), 'start_date');
date_cells(_("End date:"), 'end_date');
text_cells(_("description:"), 'description1', null, 20, 50);
hidden('user_id', $_SESSION['wa_current_user']->user);
label_cells(_("*User: "), get_user_realname($_SESSION['wa_current_user']->user));

end_row();
}
start_row();
*/

function add_task2($start_date, $end_date, $description,
	$debtor_no,$task_type,$call_type, $contact_no, $other_cust,$status,$user_id,$plan,$actual, $remarks)
{
	$start_date_db = date2sql($start_date);
	$end_date_db = date2sql($end_date);
	
	$sql = "INSERT INTO ".TB_PREF."task (start_date, end_date, description, debtor_no,task_type,call_type,contact_no,other_cust,
		status,user_id,assign_by,plan,actual, remarks, Stamp)
		VALUES (".db_escape($start_date_db) . ", "
				 .db_escape($end_date_db) . ", "
				 .db_escape($description) . ", "
				 .db_escape($debtor_no) . ", "
				 .db_escape($task_type) . ", "
				 .db_escape($call_type) . ", "
				 .db_escape($contact_no) . ", "
				 .db_escape($other_cust) . ", "
				 .db_escape($status).", "
				 .db_escape($user_id).", "
		         .db_escape($_SESSION['wa_current_user']->user).", "
				 .db_escape($plan).", "
				 .db_escape($actual).", "
				 .db_escape($remarks).", "
				 .db_escape(date("d-m-Y H:i:s")).")";
   	db_query($sql, "The insert of the task failed");
	
}
if (isset($_POST['add']))
{
add_task2($_POST['start_date'], $_POST['end_date'], $_POST['description1'], $_POST['customer_id'], $_POST['task_type'],$_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'], $_POST['status'], $_POST['user_id'], $_POST['plan'], 0, $_POST['remarks']);
}

end_row();

start_table(TABLESTYLE_NOBORDER);
start_row();

function get_task_count($inactive, $task_type, $status, $user_id, $date, $last_update, $priority)
{
	$last_update_ = sql2date($last_update);
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."task 
	WHERE inactive IN ($inactive)";
	$sql .= " AND task_type = ".db_escape($task_type);

	if($status != 0)
	$sql .= " AND status =".db_escape($status);
	if($user_id != 0)
	$sql .= " AND user_id=".db_escape($user_id);
	if($date != 0)
	$sql .= " AND start_date=".db_escape($date);
	if($last_update != 0)
	$sql .= " AND Stamp LIKE '%$last_update_%' ";
	if($priority != 0)
	$sql .= " AND priority = ".db_escape($priority);

	$result = db_query($sql, "could not get task type count");
	$row = db_fetch_row($result);
	return $row[0];
}

	$user_id_status = $_GET['user_id'];
	$user_name1 = strtoupper(get_user_name($user_id_status));

	if($_GET['user_id'] != 0)
	{
	echo "<b>$user_name1</b>";
	echo "</br>";
	}

	if($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13 && $_GET['user_id'] != 0)
	{
	$user_id = $_GET['user_id'];
	 }
	elseif($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13)
	{ $user_id = 0;
	 }
	else
	{$user_id = $_SESSION['wa_current_user']->user; 
	 }

//($inactive, $task_type, $status, $user_id, $date, $last_update, $priority)
	$count1 = get_task_count(0, 1, 1,  $user_id, 0, 0);
	$count2 = get_task_count(0, 1, 2, $user_id, 0, 0);
	$count4 = get_task_count(0, 1, 4, $user_id, 0, 0);
	$count5 = get_task_count(0, 1, 5, $user_id, 0, 0);
	$count6 = get_task_count(0, 1, 6, $user_id, 0, 0);
	$count7 = get_task_count(0, 1, 7, $user_id, 0, 0);
	$count112 = get_task_count(0, 1, 0, $user_id, 0, 0);

	$hp_count1 = get_task_count(0, 1, 1,  $user_id, 0, 0, 1);
	$hp_count2 = get_task_count(0, 1, 2, $user_id, 0, 0, 1);
	$hp_count4 = get_task_count(0, 1, 4, $user_id, 0, 0, 1);
	$hp_count5 = get_task_count(0, 1, 5, $user_id, 0, 0, 1);
	$hp_count6 = get_task_count(0, 1, 6, $user_id, 0, 0, 1);
	$hp_count7 = get_task_count(0, 1, 7, $user_id, 0, 0, 1);
	$hp_count112 = get_task_count(0, 1, 0, $user_id, 0, 0, 1);

	$today = date("Y-m-d");
	$todays_call_count = get_task_count(0, 2, 0, 0, $today, 0);
	$todays_calendar_count = get_task_count(0, 3, 0, 0, $today, 0);

//function get_task_count($inactive, $task_type, $status, $user_id, $date, $last_update)

	$yesterday = date('Y-m-d', strtotime("-1 days"));

	$tasks_added_today = get_task_count(0, 1, 0, $_GET['user_id'], $today);
	$tasks_added_yesterday = get_task_count(0, 1, 0, $_GET['user_id'], $yesterday);

	$tasks_completed_today = get_task_count(('0,1'), 1, 1, $_GET['user_id'], $today);
	$tasks_completed_yesterday = get_task_count(('0,1'), 1, 1, $_GET['user_id'], $yesterday);

	$tasks_approved_today = get_task_count(1, 1, 1, $_GET['user_id'], $today);
	$tasks_approved_yesterday = get_task_count(1, 1, 1, $_GET['user_id'], $yesterday);

//widgets

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-tasks'></span> 

<a href=../task.php?type=task color: black;  class='btn btn-info' '>&nbsp&nbsp ADD TASK</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-phone-alt'></span> 
<a href=../query.php? color: black;  class='btn btn-primary' '>&nbsp&nbsp ADD QUERY</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-phone'></span> 
<a href=../task.php?type=call  color: black; class='btn btn-warning' '>&nbsp ADD CALL</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span> 
<a href=../inquiry/call_log.php? color: black; class='btn btn-primary' '>&nbsp CALL LOG <span class='badge'>$todays_call_count</span></a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-blackboard'></span> 
<a href=../demo.php?type=task  color: black; class='btn btn-danger' '>&nbsp ADD KB</span></a> 
</span></div></center>");


label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-calendar'></span> 
<a href=../inquiry/calender.php?  color: black; class='btn btn-success' '>&nbsp CALENDAR <span class='badge'>$todays_calendar_count</span></a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-arrow-left'></span> 
<a href=../index.php?application=pmgmt  color: black; class='btn btn-success' '>&nbsp BACK</a> 
</span></div></center>");

echo "
<center> 
<div class='container'>
  <div class='btn-group'>
";
/* 
if($_GET['status'] == 6) {echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-th-list' ></span>
<button type='button' class='btn btn-default active'>
<a href=../inquiry/task_inquiry.php?status=6 ' >&nbsp&nbsp&nbspUN ASSIGNED &nbsp<span class='badge' >$count6</span>
</a></span></button>
";}


{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-th-list'></span>
<button type='button' class='btn btn-default' >
<a href=../inquiry/task_inquiry.php?status=6 '>&nbsp&nbsp&nbspUN ASSIGNED &nbsp<span class='badge'>$count6</span>
</a></span></button>
";}
*/

if($_GET['status'] == 5) {echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count5) echo "<button1 data-count=$hp_count5>"; echo "
<a href=../inquiry/task_inquiry.php?status=5&user_id=$user_id_status'>&nbsp&nbsp&nbspASSIGNED&nbsp<span class='badge'>$count5</button1></span>
</a></span></button>
";}
else
{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
<button type='button' class='btn btn-default'>"; if($hp_count5) echo "<button1 data-count=$hp_count5>"; echo "
<a href=../inquiry/task_inquiry.php?status=5&user_id=$user_id_status>&nbsp&nbsp&nbspASSIGNED&nbsp<span class='badge'>$count5</span>
</a></span></button1></button>
";}
/*
if($_GET['status'] == 4) {echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
<button type='button' class='btn btn-default active'>"; if($hp_count4 != 0) echo "<button1 data-count=$hp_count4>"; echo "
<a href=../inquiry/task_inquiry.php?status=4&user_id=$user_id_status '>&nbsp&nbsp&nbspPENDING&nbsp<span class='badge'>$count4</span>
</a></span></button1></button>
";}
else
{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
<button type='button' class='btn btn-default'> "; if($hp_count4 != 0) echo "<button1 data-count=$hp_count4>"; echo "
<a href=../inquiry/task_inquiry.php?status=4&user_id=$user_id_status '>&nbsp&nbsp&nbspPENDING&nbsp<span class='badge'>$count4</span>
</a></span></button1></button>
";}
*/

if($_GET['status'] == 2) {echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count2 != 0) echo "<button1 data-count=$hp_count2>"; echo "
<a href=../inquiry/task_inquiry.php?status=2&user_id=$user_id_status '>&nbsp&nbsp&nbspON HOLD&nbsp<span class='badge'>$count2</span>
</a></span></button>
";}
else
{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
<button type='button' class='btn btn-default'> "; if($hp_count2 != 0) echo "<button1 data-count=$hp_count2>"; echo "
<a href=../inquiry/task_inquiry.php?status=2&user_id=$user_id_status '>&nbsp&nbsp&nbspON HOLD&nbsp<span class='badge'>$count2</span>
</a></span></button>
";}

if($_GET['status'] == 7) 
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-play'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count7 != 0) echo "<button1 data-count=$hp_count7>"; echo "
<a href=../inquiry/task_inquiry.php?status=7&user_id=$user_id_status '>&nbsp&nbsp&nbspTODAY'S&nbsp<span class='badge'>$count7</span>
</a></span></button>
";
}
else
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-play'></span>
<button type='button' class='btn btn-default'> "; if($hp_count7 != 0) echo "<button1 data-count=$hp_count7>"; echo "
<a href=../inquiry/task_inquiry.php?status=7&user_id=$user_id_status '>&nbsp&nbsp&nbspTODAY'S&nbsp<span class='badge'>$count7</span>
</a></span></button>
";
}


if($_GET['status'] == 1) 
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/task_inquiry.php?status=1&user_id=$user_id_status '>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'>$count1</span></a>
</span></button>";
}

else
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/task_inquiry.php?status=1&user_id=$user_id_status '>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'>$count1</span></a>
</span></button>";
}

if($_GET['status'] == 112) 
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count112 != 0) echo "<button1 data-count=$hp_count112 class=animation>"; echo "
<a href=../inquiry/task_inquiry.php?status=112&user_id=$user_id_status '>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'>$count112</span>
</a></span></button>
";
}
else
{
echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
<button type='button' class='btn btn-default'> "; if($hp_count112 != 0) echo "<button1 data-count=$hp_count112 class=animation>"; echo "
<a href=../inquiry/task_inquiry.php?status=112&user_id=$user_id_status '>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'>$count112</span>
</a></span></button>
";
}

echo "
  </div>
</div>
</center> 
";


end_row();
end_table();




//------------------------------------------------------------------------------------------------
if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}

$id = find_submit('update_button');

if ($id !=-1)
{

	foreach ($_POST['update_button'] as $delivery => $branch) {

		$checkbox = 'update_button' . $delivery;

		if (check_value($checkbox))
		{
			
			if (strlen($_POST["remarks" . $delivery]) != 0 ||
				strlen($_POST["description" . $delivery]) != 0 ||
				strlen($_POST["actual" . $delivery]) != 0 ||
				strlen($_POST["actual1" . $delivery]) != 0 ||
				strlen($_POST["status" . $delivery]) != 0||
				strlen($_POST["priority" . $delivery]) != 0||
				strlen($_POST["progress" . $delivery]) != 0)
			{
				$remarks= $_POST["remarks" . $delivery];
				$description = $_POST["description" . $delivery];
				$actual = $_POST["actual" . $delivery];
				$actual1 = $_POST["actual1" . $delivery];
				$status = $_POST["status" . $delivery];
				$priority = $_POST["priority" . $delivery];
				$progress = $_POST["progress" . $delivery];

				$myrow = get_task_nw($delivery);

				$start_date_db = date2sql($start_date);
				$end_date_db = date2sql($end_date);

				if($myrow["remarks"] != $remarks || $myrow["description"] != $description || $myrow["actual"] != $actual || $myrow["actual1"] != $actual1 || $myrow["status"] != $status || $myrow["priority"] != $priority || $myrow["progress"] != $progress)
			{
				add_task_history($delivery, sql2date($myrow["start_date"]), sql2date($myrow["end_date"]), $description,
				  $myrow["debtor_no"], $myrow["task_type"], '','','', $status, $myrow["user_id"], $myrow["assign_by"], $myrow["plan"], $myrow["plan1"], $actual, $actual1, $remarks, '', $priority, $progress, $_SESSION['wa_current_user']->user);
				

				$sql = "UPDATE ".TB_PREF."task SET remarks=" . db_escape($remarks) . ",
				description=" . db_escape($description) .",
				actual=" . db_escape($actual) . ",
				actual1=" . db_escape($actual1) .",
				status=" . db_escape($status) . ",
				priority=" . db_escape($priority) . ",
				progress=" . db_escape($progress) . ",
				Stamp=" . db_escape(date("Y-m-d H:i:s")) . "
				WHERE id=" . db_escape($delivery) . "";
				db_query($sql, "Error");  

			display_notification(_('Task has been updated.'));
				}
				else
			display_error(_("No changes have been saved."));


			}
			
		}
	}
}

function update_description($row)
{
	$trans_no = "description" . $row['id'];
	$description = $row['description'];

	$desc_height = mb_strlen($row['description']);
	$remarks_height = mb_strlen($row['remarks']);
	
	if($desc_height > $remarks_height)
	$box_height = mb_strlen($row['description'])/25;
	else
	$box_height = mb_strlen($row['remarks'])/20;
	
	return $row['Done'] ? '' :
	"<textarea rows=$box_height cols=35 name='$trans_no''"
		." title=\"Show or add description of the task\">$description</textarea>\n"
		. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_remarks($row)
{

	$trans_no = "remarks" . $row['id'];
	$trans_no1 = $row['remarks'];

	$desc_height = mb_strlen($row['description']);
	$remarks_height = mb_strlen($row['remarks']);	

	if($desc_height > $remarks_height)
	$box_height = mb_strlen($row['description'])/25;
	else
	$box_height = mb_strlen($row['remarks'])/20;
	
	return $row['Done'] ? '' :
		"<textarea rows=$box_height cols=20 name='$trans_no''"
		." title=\"Show or add remarks of the task\">$trans_no1</textarea>\n"
		. '<input name="remarks[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}

function update_button($row)
{
	$trans_no = "update_button" . $row['id'];
	return $row['Done'] ? '' :
		'<input type="submit"  class="btn btn-primary btn-xs" title="To update text fields" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value= "UPDATE"
>'. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . ' 

">';
}

function clone_button($row)
{

	$trans_no = "clone_button" . $row['id'];

	return $row['Done'] ? '' :
		'<input type="submit" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value=clone>'
		. '<input name="clone_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}


//---------------------------------------------------------------------------------------------------
/*
function update_status($row)
{
	echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";

	return pstatus_list( $name,$row['status'],_("Select"),false);

//	if($row['status'] == 1) //done - dark green
//	echo "label_cell('pstatus_list($name, $row[status]) ')";

//	$status = label_cell(pstatus_list($name,$row['status']), "bgcolor= #2db300");

//	elseif($row['status'] == 2)//On Hold - red
//	$status = label_cell(pstatus_list($name,$row['status']), "bgcolor= #ff5c33");

	elseif($row['status'] == 4)//Pending - yellow
	$status = label_cell(pstatus_list( $name,$row['status'],_("Select"),false), "bgcolor= #ffff1a");
	elseif($row['status'] == 5)//assigned - blue
	$status = label_cell(pstatus_list( $name,$row['status'],_("Select"),false), "bgcolor= #4da6ff");
	elseif($row['status'] == 6)//un-assigned - orange
	$status = label_cell(pstatus_list( $name,$row['status'],_("Select"),false), "bgcolor= #ff9900");
	elseif($row['status'] == 7)//in process - light green
	$status = label_cell(pstatus_list( $name,$row['status'],_("Select"),false), "bgcolor= #66ff33");


//return "$status";

}
*/
function update_actual ($row)
{
	$name = "actual".$row['id'];
	echo "<input name='actual[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return duration_list( $name,$row['actual'],_("-"),false);
}
function update_status ($row)
{
	$name = "status".$row['id'];
	echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return pstatus_list( $name,$row['status'],_("-"),false);
}
function update_priority ($row)
{

	$name = "priority".$row['id'];
	echo "<input name='priority[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";

	if($row['priority'] == 1)
	return '<span class="iconPM1"> </span>'. priority_list($name,$row['priority'], false ,false);
	else
	return priority_list($name,$row['priority'], false ,false) ;

}
function update_progress ($row)
{
	$name = "progress".$row['id'];
	echo "<input name='progress[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return progress_list( $name,$row['progress'],  _("-")  ,false);
}
//------------------------------------------------------------------------------------------------

function check_overdue($row)
{
	/*return $row['OverDue'] == 1
		&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0);*/

	return $row['inactive'] == 1;

}

function check_highp($row)
{
	return $row['priority'] == 1;

}

function check_mediump($row)
{
	return $row['priority'] == 2;

}

function check_lowp($row)
{
	return $row['priority'] == 3;

}

function edit_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
//	return add_lapp_function(null, _("Edit"),"/project/task.php?&$modify=" .$row['id'],null,ICON_ADD);
  return pager_link( _("Edit"),
    "/project/task.php?type=task&$modify=" .$row['id'] ,ICON_EDIT);

}
function clone_link($row)
{
	$modify = 'id';
	return pager_link( _("Add new task"),
		"/project/task.php?$modify=" .$row['id']."&type=cloning",ICON_DOC);

}

function get_users_name_($row)
{
	$sql = "SELECT id, user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['user_id']);
	$result = db_query($sql, "could not get customer");
	$row_ = db_fetch_row($result);
	$user_name_ =  truncate_text(strtoupper($row_[1]), 10, '');
	$user_in_process_tasks = get_task_count(0, 1, 7,  $row['user_id'], 0, 0);

	if($user_in_process_tasks == 0)
	{
		$preview_str1 = "<span class='icon-input-btn'>
<a target='_blank' $class $id href=../inquiry/task_inquiry.php?status=112&user_id=$row_[0] 
onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'  title='Person whom this task to be assigned'>$user_name_
</a>
</span>
";	}
	else
	{
		$preview_str1 = "<span class='icon-input-btn'>
<a target='_blank' $class $id href=../inquiry/task_inquiry.php?status=112&user_id=$row_[0] 
onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'  title='Person whom this task to be assigned'>$user_name_
<span class='badge''>$user_in_process_tasks</span>
</a>
</span>
";	}


return $preview_str1;
}

function get_cust_name_view($row)
{
//htmlspecialchars_decode is used to convert & and other special char
$cust_name =truncate_text((htmlspecialchars_decode(get_customer_name($row['debtor_no']))), 12, '') ;

return viewer_link(strtoupper($cust_name), "project/inquiry/task_inquiry.php?status=112&debtor_no=$row[debtor_no]", "btn btn-default btn-xs",'','',("Show customer of this task"));

}

function get_task_type_($row)
{
	$sql = "SELECT task_type FROM ".TB_PREF."task_type WHERE id=".db_escape($row['task_type']);

	$result = db_query($sql, "could not get task type");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_users_realname($row)

{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['assign_by']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return strtoupper($row[0]);
}
function get_plan($row)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($row['plan']);

	$result = db_query($sql, "could not get duration of plan");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_plan1($row)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration_min WHERE id=".db_escape($row['plan1']);

	$result = db_query($sql, "could not get duration of plan for minutes");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_actual($row)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($row['actual']);

	$result = db_query($sql, "could not get duration of actual");

	$row = db_fetch_row($result);

	return $row[0];
}
function update_actual1 ($row)
{
	$name = "actual1".$row['id'];
	echo "<input name='actual1[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return duration1_list( $name,$row['actual1'],_("-"),false);
}
function get_status($row)
{
	$sql = "SELECT status FROM ".TB_PREF."pstatus WHERE id=".db_escape($row['status']);

	$result = db_query($sql, "could not get duration of actual");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_settin()
{
	$sql = "SELECT value FROM ".TB_PREF."settings WHERE name =".db_escape('task_tout');

	$result = db_query($sql, "could not get time for refresh");

	$row = db_fetch_row($result);

	return $row[0];
}

function view_task_link($type, $trans_no, $label="", $icon=false, $class='', $id='')
{

		$viewer = "project/view/view_task.php?trans_no=$trans_no";
	//else
	//	return null;

	//if ($label == "")
	$label = $trans_no;

	return viewer_link($label, $viewer, $class, $id,  $icon, ("Id"));
}

function view_history($row)
{

	$viewer = "project/view/view_history.php?trans_no=".db_escape($row['id']);
	//else
	//	return null;

	//if ($label == "")
	//$label1 = $trans_no;

	return viewer_link('view', $viewer, null, $row['id']);
}


function get_duration($row)
{
	$sql = "SELECT Stamp FROM ".TB_PREF."task_history WHERE task_id=".db_escape($row['id'])."
	AND status = ".db_escape($row['status'])."
	ORDER BY Stamp ASC	
	LIMIT 1
	";

	$result = db_query($sql, "could not get task duration");
	$row = db_fetch_row($result);
	$row[0];
	$duration_diff = dateDiff1($row[0]);

	return $duration_diff;
}

hidden('type', $status);

//---------------------------------------------------------------------------------------------

function get_sql_for_task_inquiry($from_date,$to_date, $status,$users,$assign_by, $debtor_no,$description,$remarks,$plan,$show_all)
{
	$start_date = date2sql($from_date);
	$end_date = date2sql($to_date);

	$sql = " SELECT ".TB_PREF."task.id, ".TB_PREF."debtors_master.debtor_no, ".TB_PREF."task.priority,".TB_PREF."task.user_id,
	".TB_PREF."task.description,".TB_PREF."task.remarks,".TB_PREF."task.start_date,
	".TB_PREF."task.status,".TB_PREF."task.progress,
	".TB_PREF."task.plan,".TB_PREF."task.plan1,".TB_PREF."task.actual,".TB_PREF."task.actual1,
	".TB_PREF."task.assign_by,".TB_PREF."task.end_date,".TB_PREF."task.Stamp,
".TB_PREF."task.task_type,
	".TB_PREF."task.id AS trans,  ".TB_PREF."task.inactive AS inactive
	 FROM ".TB_PREF."task_type, ".TB_PREF."task
	INNER JOIN  
		".TB_PREF."pstatus ON ".TB_PREF."pstatus.id=".TB_PREF."task.status
	INNER JOIN  ".TB_PREF."debtors_master ON
		".TB_PREF."debtors_master.debtor_no=".TB_PREF."task.debtor_no  
	WHERE
		".TB_PREF."task.start_date>='$start_date'
	AND
		".TB_PREF."task.start_date<='$end_date'
	AND
		".TB_PREF."task_type.id = ".TB_PREF."task.task_type
	AND
		".TB_PREF."task_type.check_type = 0";
	if ($status != '')
	{
		$sql .= " AND ".TB_PREF."task.status = ".db_escape($status);
	}
    if ($show_all != '')
		$sql .= " AND ".TB_PREF."task.inactive IN (".db_escape(0).", ".db_escape(1).")";
	else
		$sql .= " AND ".TB_PREF."task.inactive = 0";

	if ($users != '')
	{

		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($users);
	}

        if ($assign_by != '')
	{

		$sql .= " AND ".TB_PREF."task.assign_by = ".db_escape($assign_by);
	}

	if ($debtor_no != '')
	{
   		$sql .= " AND ".TB_PREF."task.debtor_no = ".db_escape($debtor_no);
	}
    if ($description!= '')
	{
		$number_like = "%".$description."%";
		$sql .= " AND ".TB_PREF."task.description LIKE ".db_escape($number_like);
	}
	if ($remarks!= '')
	{
		$number_like = "%".$remarks."%";
		$sql .= " AND ".TB_PREF."task.remarks LIKE ".db_escape($number_like);
	}
	if ($plan!= '')
	{
		$number_like = "%".$plan."%";
		$sql .= " AND ".TB_PREF."task.plan LIKE ".db_escape($number_like);
	}

	if ($_SESSION["wa_current_user"]->access != 2 AND $_SESSION["wa_current_user"]->access != 13)
	{
			$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($_SESSION["wa_current_user"]->user);


	}
	$sql .= " ORDER BY id DESC";
   	return $sql;
//
}
if ($_SESSION["wa_current_user"]->access == 2 || $_SESSION["wa_current_user"]->access == 13)
{
	$page = $_SERVER['PHP_SELF'];
	$company = get_settin();
	$sec =$company;
	header("Refresh: $sec; url=$page");
}

function view_history_new($row)
{
	$viewer = "project/view/view_history.php?trans_no=".$row['id'];
	$label1 = $trans_no;
	return viewer_link('view', $viewer, $class, $id,  ICON_VIEW);
}

function get_stamp_format_date($row)
{
	$viewer = "project/view/view_history.php?trans_no=".$row['id'];
	$Format_Date = date("d-m-Y h:i:sa", strtotime($row['Stamp']));
	//return $Format_Date;
	return viewer_link($Format_Date, $viewer,'','','',("Show time of last update"));
}

function get_format_date($row)
{
//	$Format_Date = date("y-j-n", strtotime($row['start_date']));
	$Format_Date = date("D,d M, y", strtotime($row['start_date']));

	return $Format_Date;
	
}

//======================================================================================================



function approved($row)
{
	$name = "rec_" .$row['id'];
	$hidden = 'last['.$row['id'].']';
	$value = $row['inactive'];
	if($row['status'] == 1)
		return checkbox(null, $name, $value, true, _('Approve This Task'))
		. hidden($hidden, $value, false);

}

if (isset($_POST['Reconcile'])) {
	set_focus('bank_date');
	foreach($_POST['last'] as $id => $value)
		if ($value != check_value('rec_'.$id))
			if(!change_tpl_flag_for_task($id)) break;
	$Ajax->activate('_page_body');
}
$id = find_submit('_rec_');

				$task_ = get_task_nw($id);


if ($id != -1)
{
	change_tpl_flag_for_task($id);


	$reconcile_value = check_value("rec_".$id);

				add_task_history($id, sql2date($task_["start_date"]), sql2date($task_["end_date"]), $task_["description"],
				  $task_["debtor_no"], $task_["task_type"], '','','', $task_["status"], $task_["user_id"], $task_["assign_by"], $task_["plan"], $task_["plan1"], $task_["actual"], $task_["actual1"], $task_["remarks"], '',  $task_["priority"],  $task_["progress"], $_SESSION['wa_current_user']->user, $reconcile_value);

				}


function change_tpl_flag_for_task($reconcile_id)
{
	global $Ajax;

	$reconcile_value = check_value("rec_".$reconcile_id);

	update_task_inactive($reconcile_id, $reconcile_value);

	$Ajax->activate('reconciled');
	$Ajax->activate('difference');

	return true;
}
function update_task_inactive($reconcile_id, $reconcile_value)
{
	$sql = "UPDATE ".TB_PREF."task SET inactive=$reconcile_value"
		." WHERE id=".db_escape($reconcile_id);

	db_query($sql, "Can't approve task");
}

//search filter
start_table(TABLESTYLE_NOBORDER);
start_row();
//text_cells('#', 'task_id_',null, 1);
//label_cells("&nbsp&nbsp");
//
//customer_list_cells('', 'debtor_no', null,_("Search by Customer:"), true);
//
//if($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13){
//	users_query_list_cells("", 'users_id', null,_("Assign To:"),true);
//}
//
//
//text_cells('', 'description_',null, 30, "", "Search Description", "", "", "placeholder='Search Description'");
//text_cells('', 'remarks_',null, 15, "", "Search Remarks", "", "", "placeholder='Search Remarks'");
//
//date_cells(_("From:"), 'from_date', '', null, -365);
//date_cells(_("To:"), 'to_date', '', null, 365, 0, 0, null, true);
//result_list_cells( _("Show:"), 'result', '', '', '', true);


if($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13){
//	users_query_list_cells('', 'assign_by', null,_("Task Owner:"),false);
//	check_cells(_("Show Approved:"), 'show_all', null, true);

}


//submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_table();
end_row();

/*
echo '
 <!-- /input-group -->
              <div class="input-group margin">
                <input type="text" class="form-control">

                    <span class="input-group-btn">

                      <button type="button" class="btn btn-info btn-flat">ADD TASK</button>
                    </span>
              </div>
              <!-- /input-group -->

';*/
//======================================================================================================
if($_SESSION['status'] == 112)
	unset($_SESSION['status']);

if($_GET['user_id'] == '')
$user_id = $_POST['users_id'];
else
$user_id = $_GET['user_id'];

if($_GET['debtor_no'] == '')
$debtor_no = $_POST['debtor_no'];
else
$debtor_no = $_GET['debtor_no'];


$sql = get_sql_for_task_inquiry( $_POST['from_date'], $_POST['to_date'],$_SESSION['status'],$user_id, $_POST['assign_by'],$debtor_no,$_POST['description_'],$_POST['remarks_'],$_POST['plan_'], check_value('show_all'));

?>
    <!----------------grid------------------>
    <html>
    <head>
        <title>GRID PHP</title>
        <link rel="stylesheet" href="http://cdn.kendostatic.com/2012.2.710/styles/kendo.common.min.css" />
        <!--	<link rel="stylesheet" href="http://cdn.kendostatic.com/2012.2.710/styles/kendo.blueopal.min.css" />-->
        <script type="text/javascript" src="http://cdn.kendostatic.com/2012.2.710/js/jquery.min.js"></script>
        <script type="text/javascript" src="http://cdn.kendostatic.com/2012.2.710/js/kendo.all.min.js"></script>
        <link rel="stylesheet" href="//kendo.cdn.telerik.com/2015.2.805/styles/kendo.common-material.min.css" />
        <link rel="stylesheet" href="//kendo.cdn.telerik.com/2015.2.805/styles/kendo.material.min.css" />

        <!--	<script src="http://cdn.kendostatic.com/2015.1.429/js/jszip.min.js"></script>-->


        <script type="text/javascript">
//function test(e){

		//	return '<a class="k-button" href="task_gridassigned.php" id="toolbar-add_user" >Assigned</a>';
		//};


		

            $(function() {
                $("#grid").kendoGrid({

                    dataSource: {
                        transport: {
                            read: "data/fetchassigneddata.php",
                            update: {url:"data/update.php", type:"POST" ,
                                complete: function(e) {
                                    $("#grid").data("kendoGrid").dataSource.read();
                                }},
                            create: {url:"data/create.php",type:"POST",
                                complete: function(e) {
                                    $("#grid").data("kendoGrid").dataSource.read();
                                }
                            },
                            destroy: {url:"data/destroy.php",type:"POST"}



                        },

                        batch: true,
//                autoSync: true,
                        schema: {
                            model: {
                                id: "id",
                                fields: {
                                    id: { editable: false  },
                                    start_date: { type: "date" },
                                    end_date: { type: "string" },
                                    Stamp: { type: "string" },
                                    description: { type: "string" },
                                    remarks: { type: "string" },
                                    debtor_no: { type: "string" },
                                    status: { type: "string"  },
                                    assign_by: { type: "string" },
                                    plan: {  editable: false },
                                    actual: { type: "number"  },
                                    priority: { type: "string"  },
                                    progress: { type: "string" },

                                }
                            }
                        },
//					pageSize: 25,


//
                        group:
                            [ {field: "assign_by"},
                                {field: "debtor_no"}
                               
                        ]
                    },

                    //--
//				selectable: "multiple",
                    pageable: {
                        refresh: true,
//					pageSizes: true,
                        pageSizes: [30, 50, 100, "all"],
                        buttonCount: 30,
                    },
                    groupable: true,
                    reorderable: true,
                    editable:  "inline",//				editable:  "incell",
				resizable: true,
					scrollable: false,

                    //height: 600,
                    filterable: true,
                    sortable: true,
//				pageable: true,
                    columnMenu: true,



              // toolbar: ["create",{
					//name:'add_user',
					//template:'#= test()#'
				//}],

				toolbar: ["create",{template: '<a class="k-button" href="task_gridassigned.php" >Assigned</a>'}

					,{template: '<a class="k-button" href="task_griddone.php" >Done</a>'}
					,{template: '<a class="k-button" href="task_gridonhold.php" >On Hold</a>'}
					,{template: '<a class="k-button" href="task_gridall.php" >All</a>'}
					,{template: '<a class="k-button" href="task_gridinprocess.php" >ToDay</a>'}
				],

                    columns: [

                        { command: [{text:" ", name:"edit"}, {text:" ",name:"destroy"}], title: " ", width: "180px" },


                        {
                         template:'<a  href="../view/view_task.php?trans_no=#=id#" >#=id#</a>',
                            field: "id",
                            title: "#",					width: "100px",

                        },
                        {
                            field: "debtor_no",					width: "220px",

                            title: "Customer",       editor: debtornoDropDownEditor,
                            template: "#=debtor_no#",
                        },

                        {
                            field: "priority",
                            width: "150px",
                            title: "priority",
                            editor: categoryDropDownEditor,
                            template: "#=priority#",



                        },

                        {
                            field: "assign_by",					width: "150px",
                            title: "Assigned Person",
                            editor: assignbyDropDownEditor,
                            template: "#=assign_by#",
                        },

                        {
                            field: "description",
                            title: "description",					width: "300px",

                        },
                        {
                            field: "remarks",
                            title: "remarks",					width: "300px",

                        },

                        {
                            field:"start_date",
                            title: "start_date",
                            width: "200px",
                            editor:  function (container, options) {
                                $('<input data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
                                    .appendTo(container)
                                    .kendoDatePicker({});

                            },

//                        formate:"MM-dd-yyyy",
                            format:"{0:yyyy-MM-dd}",
//						template: "#=#",

                        },
                        {
                            field: "status",
                            title: "status",
                            editor: statusDropDownEditor,

                            template: "#=status#",
                            width: "150px",

                        },
                        {
                            field: "progress",
                            title: "progress",
                            editor: progressDropDownEditor,
                            template: "#=progress#",
                            width: "200px",

                        },

                        {
                            field: "end_date",
                            title: "end_date",					width: "100px",

                        },

                        {
                            field: "plan",					width: "100px",

                            title: "plan",
                        },

                        {
                            field: "actual",					width: "150px",

                            title: "actual",
                            format:"{0:HH:mm}",
                            editor: function (container, options) {
                                $('<input data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
                                    .appendTo(container)
                                    .kendoTimePicker({});
                            },

                        },
                        {
                            field: "Stamp",
                            title: "Entry Log",					width: "100px",

                        }

                    ]

                });



            });

            //        function startdatetimeEditor(container, options) {
            //            $('<input data-text-field="' + options.field + '" data-value-field="' + options.field + '" data-bind="value:' + options.field + '" data-format="' + options.format + '"/>')
            //                .appendTo(container)
            //                .kendoDatePicker({});
            //        }
            //		function startdatetimeEditor(container, options) {
            //			$('<input required  name="' + options.field + '" />')
            //				.appendTo(container)
            //				.kendoDatePicker({
            //                    value: new Date(),
            //                    dateInput: true
            ////                    format: "ddd dd MMM yyyy",
            ////                    min: new Date(now.getFullYear(), now.getMonth(), now.getDate()),
            ////                    value: new Date(now.getFullYear(), now.getMonth(), now.getDate())
            //
            //				});
            //
            //		}

            function categoryDropDownEditor(container, options) {
                $('<input id="color"  required name="' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        optionLabel: 'Select a Value',
                        dataTextField: "priority",
                        dataValueField: "priority",

                        dataSource: {

                            transport: {

                                read: "data/dropdown.php"

                            }

                        }


                    });

            }
            //--status
            function statusDropDownEditor(container, options) {
                $('<input  required name="' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
//                    autoBind: true,

                        optionLabel: 'Select a Value',
                        dataTextField: "status",
                        dataValueField: "status",

                        dataSource: {
                            transport: {

                                read: "data/statusdropdown.php"
//                            update: {url:"data/update.php", type:"POST"},
//                            create: {url:"data/create.php",type:"POST"},
//                            destroy: {url:"data/destroy.php",type:"POST"}
                            }
                        }
                    });
            }
            //--progress
            function progressDropDownEditor(container, options) {
                $('<input required name="' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        autoBind: true,
                        optionLabel: 'Select a Value',
                        dataTextField: "progress",
                        dataValueField: "progress",
                        dataSource: {
                            transport: {

                                read: "data/progressdropdown.php"
//                            update: {url:"data/update.php", type:"POST"},
//                            create: {url:"data/create.php",type:"POST"},
//                            destroy: {url:"data/destroy.php",type:"POST"}
                            }
                        }
                    });
            }
            //--debtorno
            //--progress
            function debtornoDropDownEditor(container, options) {
                $('<input required name="' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        autoBind: true,
                        optionLabel: 'Select a Value',
                        dataTextField: "debtor_no",
                        dataValueField: "debtor_no",
                        dataSource: {
                            transport: {

                                read: "data/debtornodropdown.php"
//                            update: {url:"data/update.php", type:"POST"},
//                            create: {url:"data/create.php",type:"POST"},
//                            destroy: {url:"data/destroy.php",type:"POST"}
                            }
                        }
                    });
            }
            //--assignby
            function assignbyDropDownEditor(container, options) {
                $('<input required name="' + options.field + '"/>')
                    .appendTo(container)
                    .kendoDropDownList({
                        autoBind: true,
                        optionLabel: 'Select a Value',
                        dataTextField: "assign_by",
                        dataValueField: "assign_by",
                        dataSource: {
                            transport: {

                                read: "data/assignbydropdown.php"
//                            update: {url:"data/update.php", type:"POST"},
//                            create: {url:"data/create.php",type:"POST"},
//                            destroy: {url:"data/destroy.php",type:"POST"}
                            }
                        }
                    });
            }

            function timeEditor(container, options) {
                $('<input data-text-field="' + options.field + '" data-value-field="' +
                    options.field + '" data-bind="value:' + options.field + '" data-format="'
                    + options.format + '"/>')
                    .appendTo(container)
                    .kendoTimePicker({

//					autoBind: true,
//					dataTextField: "actual",
//					dataValueField: "id",
                        dataSource: {
                            transport: {

//							read: "data/fetch.php"
//                            update: {url:"data/update.php", type:"POST"},
//                            create: {url:"data/create.php",type:"POST"},
//                            destroy: {url:"data/destroy.php",type:"POST"}
                            }
                        }

                    });
            }




        </script>
    </head>
    <body><div id="example">


        <div id="grid" ></div></div>
    </body>
    </html>
<?php
/*

if($_SESSION['wa_current_user']->access == 2 || $_SESSION["wa_current_user"]->access == 13)
{
	if($_SESSION['status'] == 1 || $_SESSION['status'] == 13  || $_SESSION['status'] == '')
		$cols = array("Apl." => array('insert' => true, 'fun'=>'approved', 'align'=>'center'));
}

	array_append($cols,
		array(
			//_("Update") => array('fun' => 'update_button'),
			//array('insert' => true, 'fun' => 'edit_link'),
			_("UPDATE") => array('insert' => true, 'fun' => 'update_button'),
			_("#") => array('fun' => 'view_task_link', 'ord' => ''),
			_("CUSTOMER") => array('fun' => 'get_cust_name_view',  'ord' => ''),
			_("PRIORITY") => array('fun' => 'update_priority',  'ord' => '', 'align' => 'center'),
			_("ASSIGNED PERSON") => array('fun' => 'get_users_name_', 'ord' => ''),
			_("TASK DESCRIPTION") => array('fun' => 'update_description', 'align' => 'left'),
			_("MEMO") => array('fun' => 'update_remarks', 'align' => 'left'),
			// _("X")=>array('insert'=>true, 'fun'=>'delete_checkbox'),
			_("DATE") => array('fun'=>'get_format_date', 'ord' => ''),
			_("STATUS") => array('fun' => 'update_status', 'align' => 'center', 'ord' => ''),
			_("TASK %AGE") => array('fun' => 'update_progress', 'ord' => '',  'align' => 'left'),
			_("P HRS") => array('fun' => 'get_plan', 'ord' => ''),
			_("P MIN") => array('fun' => 'get_plan1', 'ord' => ''),
			_("ACTUAL H") => array('fun' => 'update_actual', 'align' => 'center'),
			_("ACTUAL M") => array('fun' => 'update_actual1', 'align' => 'center'),
			_("TASK OWNER") => array('fun' => 'get_users_realname', 'ord' => ''),
			//_("End date") => array('type' => 'date'),
			_("DEADLINE") => 'skip',
			_("ENTRY LOG") => array('fun'=>'get_stamp_format_date', 'type'=>'dtamp', 'ord' => '', 'align' => 'left'),
			//_("Type") => array('fun' => 'get_task_type_', 'align' => 'center'),
			_("TYPE") => 'skip',
			_("SPAN")  => array('fun' => 'get_duration', 'ord' => 'DESC'),
			//_("History") => array('fun' => 'view_history_new'),
			array('insert' => true, 'fun' => 'edit_link'),
			_("CLONE") => array('fun' => 'clone_link')

	));*/

//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
//$table =& new_db_pager('trans_tbl', $sql, $cols, null, null, get_post('result'));
$table =& new_db_pager_('trans_tbl', $sql, $cols, null, null, get_post('result'));

$table->set_marker('check_overdue', _(""), 'approvedbg');

$table->set_marker_hp('check_highp', _(""), 'highbg');

$table->set_marker_mp('check_mediump', _(""), 'mediumbg');

$table->set_marker_lp('check_lowp', _(""), 'lowbg');


$table->width = "100%";


	
//display_db_pager($table);
if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
//end_page(true, false, false, ST_DIMENSION, $id);

}
?>