<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Multiple Task"));

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/project/includes/db/task_db.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The description cannot be empty."));
		set_focus('description');
	}
	if (strlen($_POST['plan']) == 0)
	{
		$input_error = 1;
		display_error(_("The plan cannot be empty."));
		set_focus('plan');
	}
	if (strlen($_POST['task_type']) == 0)
	{
		$input_error = 1;
		display_error(_("The task type cannot be empty."));
		set_focus('task_type');
	}
	if (strlen($_POST['status']) == 0)
	{
		$input_error = 1;
		display_error(_("The status cannot be empty."));
		set_focus('status');
	}
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_multiple($selected_id, $_POST['description'],$_POST['plan'],$_POST['task_type'],$_POST['status']);
			$note = _('Selected task has been updated');
    	} 
    	else 
    	{
			add_multiple($_POST['description'],$_POST['plan'],$_POST['task_type'],$_POST['status']);
			$note = _('New task has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
/*
	if (key_in_foreign_table($selected_id, 'industry', 'name'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this area because customer branches have been created using this area."));
	} */
	if ($cancel_delete == 0) 
	{
		delete_multiple($selected_id);

		display_notification(_('Selected task been deleted'));
	} //end if Delete area
	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

//-------------------------------------------------------------------------------------------------

$result = get_multiple(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");

$th = array( "Description","Plan","Task Type", "Status","","");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		

	label_cell($myrow["description"]);
	label_cell($myrow["plan"]);
	label_cell($myrow["task_type"]);
	label_cell($myrow["status"]);


//	inactive_control_cell($myrow["id"], $myrow["inactive"], 'id', 'description');

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}
	
inactive_control_row($th);
end_table();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing area
		$myrow = get_multiple1($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['plan']  = $myrow["plan"];
		$_POST['task_type']  = $myrow["task_type"];
		$_POST['status']  = $myrow["status"];
	}
	hidden("selected_id", $selected_id);
}


textarea_row(_("Description: *"),'description', null, 25.3, 8);
duration_list_row(_("*Plan: "), 'plan',null, _("Select"));
task_type_list_row(_("*Task Type: "), 'task_type',null, _("Select"));
pstatus_list_row(_("*Status: "), 'status',1, _("Select"));

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
