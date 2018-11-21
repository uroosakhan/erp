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



if (!@$_GET['popup'])
	start_form();
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
			/***
			 For Remarks
			*/
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

				if($myrow["remarks"] != $remarks|| $myrow["description"] != $description || $myrow["actual"] != $actual || $myrow["actual1"] != $actual1 || $myrow["status"] != $status || $myrow["priority"] != $priority || $myrow["progress"] != $progress)

			{
				add_task_history($delivery, sql2date($myrow["start_date"]), sql2date($myrow["end_date"]), $description,
				  $myrow["debtor_no"], $myrow["task_type"], '','','', $status, $myrow["user_id"], $myrow["assign_by"], $myrow["plan"], $myrow["plan1"], $actual, $actual1, $remarks, '',$priority,$progress, $_SESSION['wa_current_user']->user);
				}


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

			}
			
		}
	}
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
onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'>$user_name_
</a>
</span>
";	}
	else
	{
		$preview_str1 = "<span class='icon-input-btn'>
<a target='_blank' $class $id href=../inquiry/task_inquiry.php?status=112&user_id=$row_[0] 
onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'>$user_name_
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

return viewer_link(strtoupper($cust_name), "project/inquiry/task_inquiry.php?status=112&debtor_no=$row[debtor_no]", "btn btn-default btn-xs");

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

	return viewer_link($label, $viewer, $class, $id,  $icon);
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

function get_activity_stream($from_date,$to_date, $status,$users,$assign_by, $debtor_no,$description,$remarks,$plan,$show_all)
{
	$start_date = date2sql($from_date);
	$end_date = date2sql($to_date);

	$sql = " SELECT *
	 FROM ".TB_PREF."task_history";
	
	$sql .= " ORDER BY id DESC";

	$sql .= " LIMIT 25";

    return db_query($sql,"No transactions were returned");
//
}

echo '
   <!-- Main content -->
    <section class="content">

      <!-- row -->
      <div class="row">
        <div class="col-md-12">
          <!-- The time line -->
          <ul class="timeline">
            <!-- timeline time label -->
           
';

$taskrow = get_activity_stream();


while($trans = db_fetch($taskrow))
{
	$stamp = date('d-m-Y', strtotime($trans['Stamp']));
	$cust_name =(htmlspecialchars_decode(get_customer_name($trans['debtor_no'])));

echo '
             '; if($line_stamp != $stamp){ echo'              
             <li class="time-label">
                  <span class="bg-green">
                    '.$stamp.'
              </span>
            </li>
             '; } $line_stamp = $stamp; echo' 


            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-envelope bg-blue"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> '. dateDiff1($trans['Stamp']) .' </span>

                <h3 class="timeline-header"><a href="#" </a> '.$trans['task_id'].' - '.get_user_name($trans['entry_user']).' edited a task to '.get_user_name($trans['user_id']).' </h3>

                <div class="timeline-body">

                  '. $cust_name .' </br>
                  '.$trans['description'].'

                </div>
                <div class="timeline-footer">
                  <a href="../view/view_history.php?trans_no='.$trans["task_id"].' " class="btn btn-primary btn-xs">History</a>
                  <a href="../task.php?type=task&id='.$trans["task_id"].' "class="btn btn-danger btn-xs">Edit</a>
                </div>
              </div>
            </li> 
            <!-- END timeline item -->
            <!-- timeline item -->


';
}


echo '
                             <li>
              <i class="fa fa-user bg-aqua"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> 5 mins ago</span>

                <h3 class="timeline-header no-border"><a href="#">Sarah Young</a> accepted your friend request</h3>
              </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-comments bg-yellow"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> 27 mins ago</span>

                <h3 class="timeline-header"><a href="#">Jay White</a> commented on your post</h3>

                <div class="timeline-body">
                  Take me to your leader!
                  Switzerland is small and neutral!
                  We are more like Germany, ambitious and misunderstood!
                </div>
                <div class="timeline-footer">
                  <a class="btn btn-warning btn-flat btn-xs">View comment</a>
                </div>
              </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline time label -->
            <li class="time-label">
                  <span class="bg-green">
                    3 Jan. 2014
                  </span>
            </li>
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-camera bg-purple"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                <div class="timeline-body">
                  <img src="http://placehold.it/150x100" alt="..." class="margin">
                  <img src="http://placehold.it/150x100" alt="..." class="margin">
                  <img src="http://placehold.it/150x100" alt="..." class="margin">
                  <img src="http://placehold.it/150x100" alt="..." class="margin">
                </div>
              </div>
            </li>
            <!-- END timeline item -->
            <!-- timeline item -->
            <li>
              <i class="fa fa-video-camera bg-maroon"></i>

              <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i> 5 days ago</span>

                <h3 class="timeline-header"><a href="#">Mr. Doe</a> shared a video</h3>

                <div class="timeline-body">
                  <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/tMWkeBIohBs"
                            frameborder="0" allowfullscreen></iframe>
                  </div>
                </div>
                <div class="timeline-footer">
                  <a href="#" class="btn btn-xs bg-maroon">See comments</a>
                </div>
              </div>
            </li>
            <!-- END timeline item -->
            <li>
              <i class="fa fa-clock-o bg-gray"></i>
            </li>
          </ul>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
';

?>