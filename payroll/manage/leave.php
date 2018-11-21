<?php

$page_security = 'SS_PAYROLL';

$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Leave"));

include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/payroll/includes/db/leave_db.inc");

simple_page_mode(true);

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{
	display_error("asdsa");
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
    		update_emp_leave_type($selected_id, $_POST['description'], $_POST['leave_days'], $_POST['emp_grade']);
			$note = _('Selected leave type has been updated');
    	} 
    	else
    	{
			add_emp_leave_type($_POST['description'], $_POST['leave_days'], $_POST['emp_grade']);
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
		delete_emp_leave_type($selected_id);
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



start_form();
start_table(TABLESTYLE, "width = 50%");
$th = array(_("ID"), _("Leave Type"),_("Leave Days"),_("Grade"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

$result = get_emp_leave_types(check_value('show_inactive'));

while ($myrow = db_fetch($result)) 
{

	alt_table_row_color($k);
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($myrow["leave_days"]);
	label_cell($myrow["emp_grade"]);
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
		$myrow = get_emp_leave_type($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['leave_days']  = $myrow["leave_days"];
		$_POST['emp_grade']  = $myrow["emp_grade"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 

text_row_ex(_("Leave Type:"), 'description', 30);
text_row_ex(_("Days:"), 'leave_days', 30);
emp_grade_row2(_("*Grade:"), 'emp_grade', null,true);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
