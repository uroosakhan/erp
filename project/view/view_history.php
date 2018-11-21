<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "View History"), true, false, "", $js);


include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/dimensions/includes/dimensions_ui.inc");

include_once($path_to_root . "/project/includes/db/task_db.inc");

//-------------------------------------------------------------------------------------------------
function get_actual($selected_id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($selected_id);

	$result = db_query($sql, "could not get duration of actual for hours");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_actual1($selected_id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration_min WHERE id=".db_escape($selected_id);

	$result = db_query($sql, "could not get duration of actual for minutes");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_plan($selected_id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($selected_id);

	$result = db_query($sql, "could not get duration of plan");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_plan1($selected_id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration_min WHERE id=".db_escape($selected_id);

	$result = db_query($sql, "could not get duration of plan for minutes");

	$row = db_fetch_row($result);

	return $row[0];
}
//function get_users_name($row)
//{
//	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row);
//
//	$result = db_query($sql, "could not get customer");

//	$row = db_fetch_row($result);

//	return $row[0];
//}
function get_pstatus($selected_id)
{
	$sql = "SELECT status  FROM ".TB_PREF."pstatus WHERE id=".db_escape($selected_id);
	$result = db_query($sql, "could not get dimension from reference");
	$row=db_fetch_row($result);

return $row[0];

}
function get_priority($selected_id)
{
	$sql = "SELECT priority  FROM ".TB_PREF."priority WHERE id=".db_escape($selected_id);
	$result = db_query($sql, "could not get priority");
	$row=db_fetch_row($result);

return $row[0];

}
function get_progress($selected_id)
{
	$sql = "SELECT progress  FROM ".TB_PREF."progress WHERE id=".db_escape($selected_id);
	$result = db_query($sql, "could not get progress");
	$row=db_fetch_row($result);

return $row[0];

}

if (isset($_GET['trans_no']) && $_GET['trans_no'] != "")
{
	$id = $_GET['trans_no'];
}

if (isset($_POST['Show']))
{
	$id = $_POST['trans_no'];
}


//display_heading($systypes_array[ST_DIMENSION] . " # " . $id);

br(1);


$result = get_history1($id);


label_cell("Task ID:" . " " . $id);
start_table(TABLESTYLE);

$th = array(_("Sr. No."), _("Customer"), _("Priority"), _("Assign To"),_("Description"), _("Remarks"), _("Start Date"),
	_("Status"),_("Progress"), _("Plan Hrs."),_("Plan Mins."),
_("Actual Hrs."),_("Actual Mins."),_("End Date"), _("Task Owner"), _("Last Updated By"),  _("Approved"), _("Time Stamp"));
table_header($th);

//$sr_no = get_task_history_count($id);
	$sr_no = 1;
    $priority = '';
	$description = '';
	$remarks = '';
	$status = '';
	$progress = '';
	$plan = '';
	$plan1 = '';
	$actual = '';
	$actual1 = '';
	$end_date = '';
	$assign_by = '';
	$entry_user = '';
	$approved = '' ;


	while ($myrow = db_fetch($result)) {
	start_row();

	if($myrow["deleted"] == 1)
	{
	start_table(TABLESTYLE);

	label_cell( 'This task has been deleted on ' . sql2date(date("Y-m-d", Strtotime($myrow["Stamp"]))) ." at ". (date("h:i:s a", strtotime($myrow["Stamp"]))) . ' by ' . get_users_realname1($myrow["entry_user"]) , "bgcolor='#ff0000'");
	end_table(TABLESTYLE);

	}
	else
	{

//	label_cell($myrow["task_id"]);
	label_cell($sr_no);
	label_cell(get_customer_name($myrow["debtor_no"]));
	//label_cell(get_priority($myrow["priority"]));
		if($myrow["priority"] == $priority || $priority == '')
			label_cell(get_priority($myrow["priority"]));
		else
			label_cell(get_priority($myrow["priority"]), "bgcolor='#66b3ff'");
	label_cell(get_users_name($myrow["user_id"]));

	if($myrow["description"] == $description || $description == '')
	label_cell($myrow["description"]);
	else
	label_cell($myrow["description"], "bgcolor='#66b3ff'");

	if($myrow["remarks"] == $remarks|| $remarks== '')
	label_cell($myrow["remarks"]);
	else
	label_cell($myrow["remarks"], "bgcolor='#66b3ff'");

	label_cell(sql2date($myrow["start_date"]));

	if($myrow["status"] == $status || $status == '')
	label_cell(get_pstatus($myrow["status"]));
	else
	label_cell(get_pstatus($myrow["status"]), "bgcolor='#ffff00'");

	if($myrow["progress"] == $progress || $progress == '')
	label_cell(get_progress($myrow["progress"]));
	else
	label_cell(get_progress($myrow["progress"]), "bgcolor='#ffff00'");

	if($myrow["plan"] == $plan || $plan == '')
	label_cell(get_plan($myrow["plan"]));
	else
	label_cell(get_plan($myrow["plan"]), "bgcolor='#66b3ff'");

	if($myrow["plan1"] == $plan1 || $plan1 == '')
	label_cell(get_plan1($myrow["plan1"]));
	else
	label_cell(get_plan1($myrow["plan1"]), "bgcolor='#66b3ff'");

	if($myrow["actual"] == $actual || $actual == '')
	label_cell(get_actual($myrow["actual"]));
	else
	label_cell(get_actual($myrow["actual"]), "bgcolor='#66b3ff'");

	if($myrow["actual1"] == $actual1 || $actual1 == '')
	label_cell(get_actual1($myrow["actual1"]));
	else
	label_cell(get_actual1($myrow["actual1"]), "bgcolor='#66b3ff'");

	if($myrow["end_date"] == $end_date || $end_date == '')
	label_cell(sql2date($myrow["end_date"]));
	else
	label_cell(sql2date($myrow["end_date"]), "bgcolor='#66b3ff'");

	if($myrow["assign_by"] == $assign_by || $assign_by == '')
	label_cell(get_users_realname1($myrow["assign_by"]));
	else
	label_cell(get_users_realname1($myrow["assign_by"]), "bgcolor='#66b3ff'");

	if($myrow["entry_user"] == $assign_by || $entry_user == '')
	label_cell(get_users_realname1($myrow["entry_user"]));
	else
	label_cell(get_users_realname1($myrow["entry_user"]), "bgcolor='#66b3ff'");

	if($myrow["approved"] == 1) 
	$approved_text = "Approved"; 
	else 
	$approved_text = "Open";

	if($myrow["approved"] == $approved || $entry_user == '')
	label_cell($approved_text);
	else
	label_cell($approved_text, "bgcolor='#00ff00'");

	label_cell(sql2date(date("Y-m-d", strtotime($myrow["Stamp"]))) ." ". (date("h:i:s a", strtotime($myrow["Stamp"]))));

	$priority = $myrow["priority"];
	$description = $myrow["description"];
	$remarks = $myrow["remarks"];
	$status = $myrow["status"];
	$status = $myrow["progress"];
	$plan = $myrow["plan"];
	$plan1 = $myrow["plan1"];
	$actual = $myrow["actual"];
	$actual1 = $myrow["actual1"];
	$end_date = $myrow["end_date"];
	$assign_by = $myrow["assign_by"];
	$entry_user = $myrow["entry_user"];
	$approved = $myrow["approved"];

	}
end_row();

//$sr_no -= 1;
$sr_no += 1;

}


//comments_display_row(ST_DIMENSION, $id);

end_table();

if ($myrow["closed"] == true)
{
	display_note(_("This Cost Centres is closed."));
}

start_form();
/*
start_table(TABLESTYLE_NOBORDER);

start_row();

if (!isset($_POST['TransFromDate']))
	$_POST['TransFromDate'] = begin_fiscalyear();
if (!isset($_POST['TransToDate']))
	$_POST['TransToDate'] = Today();
date_cells(_("from:"), 'TransFromDate');
date_cells(_("to:"), 'TransToDate');
submit_cells('Show',_("Show"), '', false);

end_row();
*/
end_table();
hidden('trans_no', $id);
end_form();

//display_dimension_balance($id, $_POST['TransFromDate'], $_POST['TransToDate']);

br(1);

end_page(true, false, false, ST_DIMENSION, $id);

?>