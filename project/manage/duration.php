<?php
$page_security = 'SS_CRM_C_DURATION';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Duration"));

include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/project/includes/db/status_db.inc");

simple_page_mode(true);
//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;


	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_duration($selected_id, $_POST['duration']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_duration($_POST['duration']);
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected duration have been updated'));
		else
			display_notification(_('New duration have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'task','plan'))
	{
		display_error(_("Cannot delete this delete because transactions have been entered."));
	}
	else
	{
		delete_duration($selected_id);
		display_notification(_('Selected duration have been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	//$sav = get_post('show_inactive');
	unset($_POST);
	//$_POST['show_inactive'] = $sav;
}

//------------------------------------------------------------------------------------------------

$result = get_durations(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("Duration"),_("Edit"),_("Delete"),);
inactive_control_column($th);
table_header($th);

$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

    label_cell($myrow["duration"]);
   

    inactive_control_cell($myrow["id"], $myrow["inactive"],
		'duration', 'id');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
  	end_row();

} //END WHILE LIST LOOP

inactive_control_row($th);
end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------


if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing Sales-person
		$myrow = get_duration($selected_id);

		$_POST['duration'] = $myrow["duration"];
		
	}
	hidden('selected_id', $selected_id);
} 

start_table(TABLESTYLE2);

text_row_ex(_("Duration:"), 'duration', 30);


end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

?>
