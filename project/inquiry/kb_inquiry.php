<?php
$page_security = 'SS_CRM_BASE_I';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/project/includes/db/kb_db.inc");

//include_once($path_to_root . "/project/includes/db/task_db.inc");

include_once($path_to_root . "/reporting/includes/reporting.inc");

if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1)
{
	$row = get_attachment_kb($view_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
			header("Content-type: ".$type);
			header('Content-Length: '.$row['filesize']);
			//if ($type == 'application/octet-stream')
			//	header('Content-Disposition: attachment; filename='.$row['filename']);
			//else
			header("Content-Disposition: inline");
			echo file_get_contents(company_path(). "/attachments/".$row['unique_name']);
			exit();
		}
	}
}
if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1)
{
	$row = get_attachment_kb($download_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {

			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
			header("Content-type: ".$type);
			header('Content-Length: '.$row['filesize']);
			header('Content-Disposition: attachment; filename='.$row['filename']);
			echo file_get_contents(company_path()."/attachments/".$row['unique_name']);
			exit();
		}
	}
}

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();

if (isset($_GET['user_id']) || isset($_GET['debtor_no']))
	page(_($help_context = "Knowledge base Inquiry"), true, false, "", $js);
else
	page(_($help_context = "Knowledge base Inquiry"), false, false, "", $js);

}
if (isset($_GET['FromDate'])){
	$_POST['date'] = $_GET['date'];
}
if (isset($_GET['ToDate'])){
	$_POST['date'] = $_GET['date'];
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
//function get_gk_nw1($selected_id)
//{
//	$sql = "SELECT *  FROM ".TB_PREF."knowledge_base WHERE id=".db_escape($selected_id);
//
//	$result = db_query($sql,"could not get task");
//	return db_fetch($result);
//}
//------------------------------------------------------------------------------------------------
	//echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/project/project.css" />';


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


if (!@$_GET['popup'])
	start_form();
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
category_list_cells(_("*Category: "), 'category', null, _("category"), true, false, true);
//pstatus_list_cells(_("*Status: "), 'status', 5, _("Select"));
//duration_list_cells(_("*Plan: "), 'plan', null, _("Select"));
//users_query_list_cells(_("*Assign To: "), 'user_id', null, _("Select"));

	end_row();
start_row();
///date_cells(_("Start date:"), 'start_date');
//date_cells(_("End date:"), 'end_date');
//text_cells(_("description:"), 'description1', null, 20, 50);
//	display_error(123);

//text_cells(_("Title:"), 'title', null, 20, 50);
//hidden('user_id', $_SESSION['wa_current_user']->user);
//label_cells(_("*User: "), get_user_realname($_SESSION['wa_current_user']->user));

end_row();
}
start_row();

function add_kb11($category, $title, $description,$date)
{
//	$start_date_db = date2sql($start_date);
//	$end_date_db = date2sql($end_date);
	
	$sql = "INSERT INTO ".TB_PREF."knowledge_base(category, title, description ,date)
		VALUES (" .db_escape($category) . ", "
				 .db_escape($title) . ", "
				 .db_escape($description) . ","
		         .db_escape($date) . ", 
				 )";
   	db_query($sql, "The insert of the task failed");
	
}

if (isset($_POST['add']))
{
add_kb11($_POST['category'], $_POST['title'], $_POST['description1'], $_POST['date']);
}

end_row();

echo"</table>";
//end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();

function get_task_count($inactive, $task_type, $status, $user_id, $date, $last_update)
{
	$last_update_ = sql2date($last_update);

	$sql = "SELECT COUNT(id) FROM ".TB_PREF."task 
	WHERE inactive IN ($inactive)";
	$sql .= " AND task_type = ".db_escape($task_type);

	if($status != 0)
	$sql .= " AND status =".db_escape($status)
	;
	if($user_id != 0)
	$sql .= " AND user_id=".db_escape($user_id)
	;
	if($date != 0)
	$sql .= " AND start_date=".db_escape($date)
	;
	if($last_update != 0)
	$sql .= " AND Stamp LIKE '%$last_update_%' "
	;
	$result = db_query($sql, "could not get task type count");
	$row = db_fetch_row($result);
	return $row[0];
}

	if($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13)
	{ $user_id = 0;
	 } else
	{$user_id = $_SESSION['wa_current_user']->user; 
	 }

//($inactive, $task_type, $status, $user_id, $date, $last_update)

	$count1 = get_task_count(0, 1, 1,  $user_id, 0, 0);
	$count2 = get_task_count(0, 1, 2, $user_id, 0, 0);
	$count4 = get_task_count(0, 1, 4, $user_id, 0, 0);
	$count5 = get_task_count(0, 1, 5, $user_id, 0, 0);
	$count6 = get_task_count(0, 1, 6, $user_id, 0, 0);
	$count7 = get_task_count(0, 1, 7, $user_id, 0, 0);
	$count112 = get_task_count(0, 1, 0, $user_id, 0, 0);

	$today = date("Y-m-d");
	$todays_call_count = get_task_count(0, 2, 0, 0, $today, 0);

	$yesterday = date('Y-m-d', strtotime("-1 days"));
	$tasks_added_today = get_task_count(0, 1, 0, 0, $today);
	$tasks_completed_today = get_task_count(('0,1'), 1, 1, 0, 0, $today);
	$tasks_added_yesterday = get_task_count(0, 1, 0, 0, $yesterday);

if($tasks_added_today != 0)
{
echo "Tasks added Today = ";
echo $tasks_added_today;
echo "</br>";
}
if($tasks_completed_today != 0)
{
echo "Tasks completed Today = ";
echo $tasks_completed_today ;
echo "</br>";
}
if($tasks_added_yesterday != 0)
{
echo "Tasks added Yesterday = ";
echo $tasks_added_yesterday ;
echo "</br>";
}


function get_last_update1($type)
{
	$sql = "SELECT Stamp, entry_user, id
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

//$diff = ago('2017-06-19 10:39:34');    // output: 23 hrs ago

$diff = dateDiff_kb($get_last_history[0]);

echo "Last edit was $diff ago by $last_user_name";



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
<a href=../task.php?type=call  color: black; class='btn btn-warning' '>&nbsp ADD CALL<span class='badge'>$todays_call_count</span></a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-calendar'></span> 
<a href=../inquiry/calender.php?  color: black; class='btn btn-success' '>&nbsp CALENDAR</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-dashboard'></span> 
<a href=../view/view_task_inquiry.php  color: black;  class='btn btn-default' '>&nbsp HISTORY</a> 
</span></div></center>");


end_row();
end_table();

//blank table for space
start_table(TABLESTYLE_NOBORDER);
start_row();
end_row();
end_table();


start_table(TABLESTYLE_NOBORDER);
start_row();


echo "
<center> 
<div class='container'>
  <div class='btn-group'>
";
 
//if($_GET['status'] == 6) {echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-th-list' ></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=6 ' >&nbsp&nbsp&nbspUN ASSIGNED &nbsp<span class='badge' >$count6</span>
//</a></span></button>
//";}
//else
//{echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-th-list'></span>
//<button type='button' class='btn btn-default' >
//<a href=../inquiry/task_inquiry.php?status=6 '>&nbsp&nbsp&nbspUN ASSIGNED &nbsp<span class='badge'>$count6</span>
//</a></span></button>
//";}


//if($_GET['status'] == 5) {echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=5 '>&nbsp&nbsp&nbspASSIGNED&nbsp<span class='badge'>$count5</span>
//</a></span></button>
//";}
//else
//{echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=5 '>&nbsp&nbsp&nbspASSIGNED&nbsp<span class='badge'>$count5</span>
//</a></span></button>
//";}

//if($_GET['status'] == 4) {echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=4 '>&nbsp&nbsp&nbspPENDING&nbsp<span class='badge'>$count4</span>
//</a></span></button>
//";}
//else
//{echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=4 '>&nbsp&nbsp&nbspPENDING&nbsp<span class='badge'>$count4</span>
//</a></span></button>
//";}


//if($_GET['status'] == 2) {echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=2 '>&nbsp&nbsp&nbspON HOLD&nbsp<span class='badge'>$count2</span>
//</a></span></button>
//";}
//else
//{echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=2 '>&nbsp&nbsp&nbspON HOLD&nbsp<span class='badge'>$count2</span>
//</a></span></button>
//";}


//if($_GET['status'] == 7)
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-play'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=7 '>&nbsp&nbsp&nbspIN PROCESS&nbsp<span class='badge'>$count7</span>
//</a></span></button>
//";
//}
//else
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-play'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=7 '>&nbsp&nbsp&nbspIN PROCESS&nbsp<span class='badge'>$count7</span>
//</a></span></button>
//";
//}


//if($_GET['status'] == 1)
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=1 '>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'>$count1</span></a>
//</span></button>";
//}
//
//else
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=1 '>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'>$count1</span></a>
//</span></button>";
//}

//if($_GET['status'] == 112)
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
//<button type='button' class='btn btn-default active'>
//<a href=../inquiry/task_inquiry.php?status=112 '>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'>$count112</span>
//</a></span></button>
//";
//}
//else
//{
//echo "
//<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
//<button type='button' class='btn btn-default'>
//<a href=../inquiry/task_inquiry.php?status=112 '>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'>$count112</span>
//</a></span></button>
//";
//}

echo "
  </div>
</div>
</center> 
";

label_cell("&nbsp&nbsp&nbsp&nbsp");

end_row();
end_table();

//blank table for space
/*
start_table(TABLESTYLE_NOBORDER);
start_row();
label_cell("&nbsp&nbsp&nbsp&nbsp");
end_row();
end_table();
*/

start_table(TABLESTYLE_NOBORDER);
category_list_cells(_("Category:"), 'category', null,true, true);
end_table();


start_row();

if (!@$_GET['popup'])


if($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13){


}


end_row();
end_table();


start_table(TABLESTYLE_NOBORDER);
start_row();


text_cells(_("Description:"), 'description_',null, 30);

text_cells(_("Title:"), 'title',null, 30);
date_cells(_("From:"), 'start_date');
date_cells(_("To:"), 'end_date');
submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();


//------------------------------------------------------------------------------------------------
if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}
//----------------------------------------------------------------------------------------------

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
			if (strlen($_POST["category" . $delivery]) != 0 ||
				strlen($_POST["title" . $delivery]) != 0 ||
				strlen($_POST["description" . $delivery]) != 0||
				strlen($_POST["date" . $delivery]) != 0||
				strlen($_POST["filename" . $delivery]) != 0)
			{
				$category= $_POST["category" . $delivery];
				$title = $_POST["title" . $delivery];
				$description = $_POST["description" . $delivery];
				$date = $_POST["date" . $delivery];
				$filename = $_POST["filename" . $delivery];

				$myrow = get_gk_nw1($delivery);


//				if($myrow["category"] != $category|| $myrow["title"] != $title || $myrow["description"] != $description )	{
//					add_kb_($myrow["category"], $myrow["title"], $myrow["description"]);
//				}


				if($myrow["category"] != $category|| $myrow["title"] != $title || $myrow["description"] != $description|| $myrow["filename"] != $filename)
				{

					update_knowledge_base($myrow["id"],$myrow["category"], $myrow["title"], $description , $date, $myrow['filename']);
				}
//				$sql = "UPDATE  0_knowledge_base SET category=" . db_escape($category) . ",
//				title=".db_escape($title) . ",
//				description=".db_escape($description) . ",
//				WHERE id = ".db_escape($selected_id);
//				db_query($sql, "Error");
//				$sql = "UPDATE ".TB_PREF."knowledge_base SET category=".db_escape($category) . ",
//				title=".db_escape($title) . ",
//				description=".db_escape($description) . ",
//				WHERE id = ".db_escape($selected_id);
//				db_query($sql, "Error");

			}
			
		}
	}
}
//if($myrow["category"] != $category|| $myrow["title"] != $title || $myrow["description"] != $description )
//{
//	update_kb1($myrow["category"], $myrow["title"], $myrow["description"]);
//}

//function update_description($row)
//{
//	$trans_no = "description" . $row['id'];
//	$trans_no1 = $row['description'];
//
//	return $row['Done'] ? '' :
////		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['description'] . '"width="100px" >'
//	"<textarea  name='$trans_no''"
////		.($title ? " title='$title'" : '')
//		.">$trans_no1</textarea>\n"
//		. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '">';
//
//}
//function update_remarks($row)
//{
//
//	$trans_no = "remarks" . $row['id'];
//	$trans_no1 = $row['remarks'];
//
//	return $row['Done'] ? '' :
////		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['remarks'] . '"width="100px" >'
//		"<textarea   name='$trans_no''"
////		.($title ? " title='$title'" : '')
//		.">$trans_no1</textarea>\n"
//		. '<input name="remarks[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
//		. $row['id'] . '">';
//}
//function update_actual1($row)
//{
//	$trans_no = "actual" . $row['id'];

//	return $row['Done'] ? '' :

//		' input type="text" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . //$row['actual'] . '" >'
//		. '<input name="actual[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" //value="'
//		. $row['id'] . '">';
//}

function update_button($row)
{

	$trans_no = "update_button" . $row['id'];

	return $row['Done'] ? '' :
		'
 <span class="icon-input-btn"><span class="glyphicon glyphicon-edit"></span>

<input type="submit"  class="btn btn-primary btn-xs" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value=update 
>'

		. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . ' ">';
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
function update_actual ($row)
{
	$name = "actual".$row['id'];
	echo "<input name='actual[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return duration_list( $name,$row['actual'],_("Select"),false);
}
function update_status ($row)
{
	$name = "status".$row['id'];
	echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return pstatus_list( $name,$row['status'],_("Select"),false);
}
//------------------------------------------------------------------------------------------------

function check_overdue($row)
{
	/*return $row['OverDue'] == 1
		&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0);*/

	return $row['inactive'] == 1;

}

function edit_link($row)
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
//	return add_lapp_function(null, _("Edit"),"/project/task.php?&$modify=" .$row['id'],null,ICON_ADD);
  return pager_link( _("Edit"),
    "/project/knowledge_base_attachments.php?$modify=" .$row['id'] ,ICON_EDIT);
	



}
function clone_link($row)
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
	return pager_link( _("Clone"),
		"/project/task.php?$modify=" .$row['id']." && Type=cloning",ICON_DOC);

}

function get_users_name_($row)
{
	$sql = "SELECT id, user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['user_id']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	//return $row[1];

	return viewer_link(_("")."".$row[1], "project/inquiry/task_inquiry.php?status=112&user_id=$row[0]", "btn btn-default glyphicon glyphicon-user");
}

function get_cust_name_view($row)
{
$cust_name =htmlspecialchars_decode(get_customer_name($row['debtor_no'])) ;
//return get_customer_name($row['debtor_no']);
return viewer_link($cust_name, "project/inquiry/task_inquiry.php?status=112&debtor_no=$row[debtor_no]", "btn btn-default");


/*
label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-tasks'></span> 
<a href=../task.php?type=task color: black;  class='btn btn-info' '>&nbsp&nbsp&nbsp&nbsp ADD TASK</a> 
</span></div></center>");
*/

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

	return $row[0];
}
//function get_user_realname($row)
//{
//	$row = $_SESSION['wa_current_user']->user;
//
//	$result = db_query($sql, "could not get customer");
//
//	$row = db_fetch_row($result);
//
//	return $row;
//}
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
	return duration1_list( $name,$row['actual1'],_("Select"),false);
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
function get_title($row)
{
	$sql = "SELECT title FROM ".TB_PREF."knowledge_base WHERE title =".db_escape($row['title']);

	$result = db_query($sql, "could not get time for refresh");

	$row = db_fetch_row($result);

	return $row[0];
}
//function get_des($row)
//{
//	$sql = "SELECT LEFT(description, 40) FROM ".TB_PREF."knowledge_base WHERE description =".db_escape($row['description']);
//
//	$result = db_query($sql, "could not get time for refresh");
//
//	$row = db_fetch_row($result);
//
//	return $row[0];
//}



function get_category($row)
{
	$sql = "SELECT category FROM ".TB_PREF."category WHERE id =".db_escape($row['category']);

	$result = db_query($sql, "could not get time for refresh");

	$row = db_fetch_row($result);

	return $row[0];
}

function view_task_link($type, $trans_no, $label="", $icon=false,
									   $class='', $id='')
{

		$viewer = "project/view/view_kb.php?trans_no=$trans_no";
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

function view_history_new($row)
{

	$viewer = "project/view/view_history.php?trans_no=".$row['id'];
	//else
	//	return null;

	//if ($label == "")
	$label1 = $trans_no;

	return viewer_link('view', $viewer, $class, $id,  ICON_VIEW);
}
hidden('type', $status);

//---------------------------------------------------------------------------------------------

function get_sql_for_task_inquiry($id,$start_date,$category,$title,$description,$filename, $filesize, $filetype,$end_date)
{
	$start_date = date2sql($start_date);
	$end_date = date2sql($end_date);

	$sql="SELECT  0_knowledge_base.id ,0_knowledge_base.date,".TB_PREF."knowledge_base.category,title,description,trans_no,filename,filesize,filetype from  
 ".TB_PREF."knowledge_base ,".TB_PREF."category 
 WHERE ".TB_PREF."knowledge_base.id !=-1 
 AND ".TB_PREF."knowledge_base.category = ".TB_PREF."category.id
 AND

		".TB_PREF."knowledge_base.date>='$start_date'
	AND
		".TB_PREF."knowledge_base.date<='$end_date'
 ";


//	$sql = " SELECT ".TB_PREF.".id, ".TB_PREF."debtors_master.debtor_no, ".TB_PREF."task.user_id,
//	".TB_PREF."task.description,".TB_PREF."task.remarks,".TB_PREF."task.start_date,
//	".TB_PREF."task.status,
//	".TB_PREF."task.plan,".TB_PREF."task.plan1,".TB_PREF."task.actual,".TB_PREF."task.actual1,
//	".TB_PREF."task.assign_by,".TB_PREF."task.end_date,".TB_PREF."task.Stamp,
//".TB_PREF."task.task_type,
//	".TB_PREF."task.id AS trans,  ".TB_PREF."task.inactive AS inactive
//	 FROM ".TB_PREF."task_type, ".TB_PREF."task
//	INNER JOIN
//		".TB_PREF."pstatus ON ".TB_PREF."pstatus.id=".TB_PREF."task.status
//	INNER JOIN  ".TB_PREF."debtors_master ON
//		".TB_PREF."debtors_master.debtor_no=".TB_PREF."task.debtor_no
//	WHERE
//		".TB_PREF."task.start_date>='$start_date'
//	AND
//		".TB_PREF."task.start_date<='$end_date'
//	AND
//		".TB_PREF."task_type.id = ".TB_PREF."task.task_type
//	AND
//		".TB_PREF."task_type.check_type = 0";
//	if ($status != '')
//	{
//		$sql .= " AND ".TB_PREF."task.status = ".db_escape($status);
//	}
//    if ($show_all != '')
//		$sql .= " AND ".TB_PREF."task.inactive IN (".db_escape(0).", ".db_escape(1).")";
//	else
//		$sql .= " AND ".TB_PREF."task.inactive = 0";
//
//	if ($users != '')
//	{
//
//		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($users);
//	}
//
//        if ($assign_by != '')
//	{
//
//		$sql .= " AND ".TB_PREF."task.assign_by = ".db_escape($assign_by);
//	}
//
//	if ($debtor_no != '')
//	{
//   		$sql .= " AND ".TB_PREF."task.debtor_no = ".db_escape($debtor_no);
//	}
    if ($category!= '')
	{
		$number_like = "%".$category."%";
		$sql .= " AND ".TB_PREF."category.id LIKE ".db_escape($number_like);
	}

//	if ($category != "")
//		$sql .= " AND category = ".db_escape($category);
//
//
//	if ($description != "")
//		$sql .= " AND description = ".db_escape($description);
//
//	if ($title != ALL_TEXT)
//		$sql .= " AND title=" . db_escape($title);


//	if ($category != "") {
//
//		$number_like = "%" . $category . "%";
//		$sql .= " AND category LIKE " . db_escape($number_like);
//
//	}
	if ($description != "") {

		$number_like = "%" . $description . "%";
		$sql .= " AND description LIKE " . db_escape($number_like);

	}
	if ($title != "") {

		$number_like = "%" . $title . "%";
		$sql .= " AND title LIKE " . db_escape($number_like);

	}

//	if ($start_date != "") {
//
//		$number_like = "%" . $start_date . "%";
//		$sql .= " AND date LIKE " . db_escape($number_like);
//
//	}
//	if ($end_date != "") {
//
//		$number_like = "%" . $end_date . "%";
//		$sql .= " AND date LIKE " . db_escape($number_like);
//
//	}
//	if ($_SESSION["wa_current_user"]->access != 2 AND $_SESSION["wa_current_user"]->access != 13)
//	{
//			$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($_SESSION["wa_current_user"]->user);
//	}
//	$sql .= " ORDER BY id DESC";
	$sql .= " ORDER BY trans_no DESC ";
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
function get_format_date($row)
{
//	$g = sql2date();
	$Format_Date = date("d-m-Y h:i:sa", strtotime($row['Stamp']));
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

				$task_ = get_gk_nw1 ($id);


if ($id != -1)
{
	change_tpl_flag_for_task($id);


	$reconcile_value = check_value("rec_".$id);

				add_task_history($task_["category"], $task_["title"], $task_["description"], $task_["date"]);
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
function download_link($row)
{
	return button('download'.$row["id"], _("Download"), _("Download"), ICON_DOWN);
}
function get_file_name($row)
{
	$sql = "SELECT filename  FROM ".TB_PREF."knowledge_base WHERE id =".db_escape($row['id']);

	$result = db_query($sql, "could not get time for refresh");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_decode($row)
{
	$g=substr($row['description'],0,500);

//htmlspecialchars_decode is used to convert & and other special char
	$cust_name =htmlspecialchars_decode($g) ;

	return $cust_name;

}
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


$sql = get_sql_for_task_inquiry( $_POST['id'], $_POST['start_date'], $_POST['category'],$_POST['title'], $_POST['description_'],$filename, $filesize, $filetype,$_POST['end_date']);

if($_SESSION['wa_current_user']->access == 2 || $_SESSION["wa_current_user"]->access == 13)
{
//	if($_SESSION['status'] == 1 || $_SESSION['status'] == 13  || $_SESSION['status'] == '')
//		$cols = array("Apl." => array('insert' => true, 'fun'=>'approved', 'align'=>'center'));
}

	array_append($cols,
		array(
			//_("Update") => array('fun' => 'update_button'),
			//array('insert' => true, 'fun' => 'edit_link'),
//			array('insert'=>true, 'fun'=>'download_link'),
//			array('insert' => true, 'fun' => 'update_button'),
			_("#") => array('fun' => 'view_task_link', 'ord' => ''),
			_("Date") => array('type'=>'date'),
			_("Category")=> array('fun' => 'get_category'),
			_("Tile") => array('fun' => 'get_title','align' => 'left'),
			_("Description") => array('fun' => 'get_decode'),
//			_("File Name")=> array('fun' => 'get_file_name', 'align' => 'left'),
			array('insert'=>true, 'fun'=>'edit_link'),
			array('insert'=>true, 'fun'=>'view_link')
//			array('insert'=>true, 'fun'=>'download_link')


			//_("Clone") => array('fun' => 'clone_link'),

	));

//function display_rows($type)
//{
//	$sql = get_sql_for_attached_documents($type);
//	$cols = array(
//		_("#") => array('fun'=>'trans_view', 'ord'=>''),
//		_("Description") => array('name'=>'description'),
//		_("Title") => array('name'=>'filename'),
////	    _("Size") => array('name'=>'filesize'),
////	    _("Filetype") => array('name'=>'filetype'),
//		_("Date") => array('name'=>'date', 'type'=>'date'),
//		array('insert'=>true, 'fun'=>'edit_link'),
//		array('insert'=>true, 'fun'=>'view_link'),
//		array('insert'=>true, 'fun'=>'download_link'),
//		array('insert'=>true, 'fun'=>'delete_link')
//	);
//	$table =& new_db_pager('trans_tbl', $sql, $cols);
//
//	$table->width = "60%";
//
//	display_db_pager($table);
//}


//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */

$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "100%";


	
display_db_pager($table);
if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
//end_page(true, false, false, ST_DIMENSION, $id);

}
?>

