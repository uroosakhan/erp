<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Leave Types"));

include($path_to_root . "/payroll/includes/db/grade_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The leave type description cannot be empty."));
		set_focus('description');
	}
	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			$maxl=$_POST['leave_days']* $_POST['f_years'];
			update_emp_leave_type_policy($selected_id, $_POST['description'], $_POST['leave_days'], $_POST['emp_grade'], $_POST['encash'], $_POST['f_years'],
				$_POST['division'], $_POST['location'], $_POST['project'],$maxl);
			$note = _('Selected leave type has been updated');
		}
		else
		{

			$maxl=$_POST['leave_days']* $_POST['f_years'];

			add_emp_leave_type_policy($_POST['description'], $_POST['leave_days'], $_POST['emp_grade'], $_POST['encash'], $_POST['f_years'],
				$_POST['division'], $_POST['location'], $_POST['project'],$maxl);
			$note = _('New leave type has been added');
		}
		display_notification($note);
		$Mode = 'RESET';
	}
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
		delete_emp_leave_type_new($selected_id);
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

//$result = get_emp_grades(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width = 50%");
$th = array(_("ID"), _("Leave Type"),_("Leave Days"),_("Grade"),_("Encash"),_("Up To How Many Fiscal Year"),_("Max Accumulated Leaves"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

$result = get_emp_leave_types_new(check_value('show_inactive'));

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($myrow["leave_days"]);
	label_cell(get_grande_name($myrow["emp_grade"]));

	$getv=$myrow["encash"];
	if($getv=='1')
	{

		$getv='Yes';
	}
else{

	$getv='NO';
}

	label_cell($getv);
	label_cell($myrow["no_of_f_years"]);
	label_cell($myrow["max_accum_leaves"]);

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'dept', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{

	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_emp_leave_type_new($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['leave_days']  = $myrow["leave_days"];
		$_POST['emp_grade']  = $myrow["emp_grade"];
		$_POST['f_years']  = $myrow["no_of_f_years"];
		$_POST['encash']  = $myrow["encash"];

		$_POST['division']  = $myrow["division"];
		$_POST['project']  = $myrow["project"];
		$_POST['location']  = $myrow["location"];


	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

text_row_ex(_("Leave Type:"), 'description', 30);
text_row_ex(_("Days:"), 'leave_days', 30);
emp_grade_row2(_("Grade:"), 'emp_grade', $_POST['emp_grade'],true);
text_row_ex(_("Fiscal Years:"), 'f_years', 30);
yesno_list_row(_("Encash:"), 'encash');

dimensions_list_row(_("Division"), 'division', null, 'All division', "", false, 1,true);
pro_list_row(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
