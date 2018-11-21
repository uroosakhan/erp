<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Project"));

include($path_to_root . "/payroll/includes/db/project_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Project description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
			update_emp_project($selected_id, $_POST['description']);
			$note = _('Selected Project has been updated');
    	} 
    	else 
    	{
			add_emp_project($_POST['description']);
			$note = _('New Project has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'employee', 'emp_dept'))
//	{
//		$cancel_delete = 1;
//		display_error(_("Cannot delete this department because Employee have been created using this dept."));
//	}
	if ($cancel_delete == 0) 
	{
		delete_emp_project($selected_id);
		display_notification(_('Selected department has been deleted'));
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

$result = get_emp_project(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"), _("Project Name"), "Edit", "Delete");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'project', 'id');
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
		$myrow = get_emp_project_($selected_id);

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 

text_row_ex(_("Project Name:"), 'description', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
