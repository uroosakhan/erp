<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

$js = "";
//if ($use_popup_windows)
$js .= get_js_open_window(900, 500);
//if ($use_date_picker)
$js .= get_js_date_picker();

page(_($help_context = "Daily Attendance"));

include($path_to_root . "/payroll/includes/db/grade_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include($path_to_root . "/includes/ui.inc");





simple_page_mode(true);

/*if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{

	$input_error = 0;

/*	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The leave type description cannot be empty."));
		set_focus('description');
	}*/
	/*if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			update_daily_attendance($selected_id, $_POST['employee_id'], $_POST['check_in'], $_POST['check_out'], $_POST['fiscal_year'], $_POST['division'], $_POST['project'], $_POST['location']);
			$note = _('Selected leave type has been updated');
		}
		else
		{
			add_daily_attendance($_POST['employee_id'], $_POST['check_in'], $_POST['check_out'], $_POST['fiscal_year'], $_POST['division'], $_POST['project'], $_POST['location']);

		}
		display_notification($note);
		$Mode = 'RESET';
	}
}*/

if (isset($_POST['update']))
{
	display_error("asdasdasdasd");
	/*update_daily_attendance($selected_id, $_POST['employee_id'], $_POST['check_in'], $_POST['check_out'], $_POST['fiscal_year'], $_POST['division'], $_POST['project'], $_POST['location']);
	$note = _('Employee Attendance Has been Updated');
	display_notification($note);
	$Mode = 'RESET';*/
}


if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'leave', 'leave_type'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this leave type because leave have been created using this leave type."));
	}
	if ($cancel_delete == 0)
	{
		delete_att_policy($selected_id);
		display_notification(_('Selected leave type has been deleted'));
	} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}


///=======================================================

/*$emp_id = $_GET['emp_id'];
$date = $_GET['date'];

//$result = get_daily_att($emp_id,$date);

start_form();
start_table(TABLESTYLE, "width = 50%");
$th = array(_("Employee"), _("Check In"),_("Check Out"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

$result =get_daily_att($emp_id,$date);

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);
	label_cell(get_employee_name_for_att($myrow["employee"]));
	label_cell($myrow["check_in"]);
	label_cell($myrow["check_out"]);
//	label_cell($myrow["fiscal_year"]);

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'dept', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);*/

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1)
{

//	if ($Mode == 'Edit') 
	
	{
		//editing an existing group.
		$emp_id = $_GET['emp_id'];
		$date = $_GET['date'];
		
		$myrow = get_all_data_($emp_id,$date);

		$_POST['employee_id']  = $myrow["employee"];
		$_POST['check_in']  = $myrow["check_in"];
		$_POST['check_out']  = $myrow["check_out"];

		$_POST['division']  = $myrow["division"];
		$_POST['project']  = $myrow["project"];
		$_POST['location']  = $myrow["location"];
		$_POST['date_of_attendance']  = sql2date($myrow["att_date"]);
		$selected_id = $myrow["id"];

//		$_POST['fiscal_year']  = $myrow["fiscal_year"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

//text_row_ex(_("Leave Type:"), 'description', 30);
//text_row_ex(_("Days:"), 'leave_days', 30);
employee_list_cells(_("Select a employee: "), 'employee_id', null,'Select Employee');

dimensions_list_row(_("Divison"), 'division', $_POST['divisionoooo'], 'All division', "", false, 1,true);
pro_list_row(_("Project"), 'project',null, 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

text_row_ex(_("Check In"), 'check_in', 30);
text_row_ex(_("Check Out"), 'check_out', 30);
date_row(_("Date of Attendance"),'date_of_attendance', null,null, 0, 0, 0, null, true);
$f_year = get_current_fiscalyear();
//fiscalyears_list_row(_("Fiscal Year"),'fiscal_year');
hidden("fiscal_year", $f_year['id']);
/*
text_row_ex(_("Office Start"), 'office_start', 30);
text_row_ex(_("Deduction(.25) /day"), 'deduction1', 30);
text_row_ex(_("Deduction(.50) /day"), 'deduction2', 30);
text_row_ex(_("Office End"), 'office_end', 30);*/

end_table(1);

//submit_add_or_update_center( -1, '', 'both');
submit_center_first('update', _("Update"), '', '', true);
//submit_center_last('delete', _("Delete"), '', '', true);

end_form();

end_page();
?>
