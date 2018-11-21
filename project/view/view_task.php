<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "View Tasks"), true, false, "", $js);

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

	$result = db_query($sql, "could not get duration of plan for hours");

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

//	$result = db_query($sql, "could not get customer");

//	$row = db_fetch_row($result);

//	return $row[0];
//}
function get_cal_type($row)
{
	$sql = "SELECT call_type FROM ".TB_PREF."call_type WHERE id=".db_escape($row);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

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


$myrow = get_task($id);

if (strlen($myrow[0]) == 0)
{
	echo _("The Cost Centres number sent is not valid.");
	exit;
}

start_table(TABLESTYLE);

$th = array(_("#"),_("Stamp"),_("Customers"), _("Priority"),_("Assign To"),  _("Description"), _("Remarks"),_("Start Date"),_("Call Type"),_("Contact No"),_("Other Customers"), _("Status"),_("Progress"),_("Plan Hrs."), _("Plan Mins."),_("Actual Hrs."),_("Actual Mins."),_("End_Date"),_("Task Owner"), );
table_header($th);

start_row();
label_cell($myrow["id"]);
label_cell($myrow["Stamp"]);
label_cell(get_customer_name($myrow["debtor_no"]));
label_cell(get_priority($myrow["priority"]));
label_cell(get_users_name($myrow["user_id"]));
label_cell($myrow["description"]);
label_cell($myrow["remarks"]);
label_cell(sql2date($myrow["start_date"]));
label_cell(get_cal_type($myrow["call_type"]));
label_cell($myrow["contact_no"]);
label_cell($myrow["other_cust"]);
label_cell(get_pstatus($myrow["status"]));
label_cell(get_progress($myrow["progress"]));
label_cell(get_plan($myrow["plan"]));
label_cell(get_plan1($myrow["plan1"]));
label_cell(get_actual($myrow["actual"]));
label_cell(get_actual1($myrow["actual1"]));
label_cell(sql2date($myrow["end_date"]));
label_cell(get_users_realname1($myrow["assign_by"]));

end_row();

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