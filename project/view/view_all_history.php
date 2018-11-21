<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "View All History"), true, false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/dimensions/includes/dimensions_ui.inc");

include_once($path_to_root . "/project/includes/db/task_db.inc");

//-------------------------------------------------------------------------------------------------
function get_actual($selected_id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($selected_id);

	$result = db_query($sql, "could not get duration of actual");

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

//function get_users_name($row)
//{
//	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row);
//
//	$result = db_query($sql, "could not get customer");

//	$row = db_fetch_row($result);

//	return $row[0];
//}
function get_users_real_name($row)

{
	$sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($row);

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




//display_heading($systypes_array[ST_DIMENSION] . " # " . $id);

br(1);


$result = get_all_history($id);


start_table(TABLESTYLE);

$th = array(_("#"), _("Start_Date"), _("End_Date"),_("Stamp"), _("Description"), _("Customers"), _("Status"), _("Assign To"), _("Assign By"), _("Plan"), _("Actual"), _("Remarks"));
table_header($th);


while ($myrow = db_fetch($result))
{
	start_row();

		label_cell($myrow["id"]);
		label_cell(sql2date($myrow["start_date"]));
		label_cell(sql2date($myrow["end_date"]));
		label_cell($myrow["Stamp"]);
		label_cell($myrow["description"]);
		label_cell(get_customer_name($myrow["debtor_no"]));
		label_cell(get_pstatus($myrow["status"]));
		label_cell(get_users_name($myrow["user_id"]));
	    label_cell(get_users_real_name($myrow["assign_by"]));
		label_cell(get_plan($myrow["plan"]));
		label_cell(get_actual($myrow["actual"]));
		label_cell($myrow["remarks"]);

	end_row();
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