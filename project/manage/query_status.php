<?php
$page_security = 'SS_CRM_C_STATUS';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Query"));

include($path_to_root . "/includes/ui.inc");

include($path_to_root . "/sales/includes/db/query_status_db.inc");

simple_page_mode(true);
//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

/*	if (strlen($_POST['salesman_name']) == 0)
	{
		$input_error = 1;
		display_error(_("The sales person name cannot be empty."));
		set_focus('salesman_name');
	}
	$pr1 = check_num('provision', 0,100);
	if (!$pr1 || !check_num('provision2', 0, 100)) {
		$input_error = 1;
		display_error( _("Salesman provision cannot be less than 0 or more than 100%."));
		set_focus(!$pr1 ? 'provision' : 'provision2');
	}
	if (!check_num('break_pt', 0)) {
		$input_error = 1;
		display_error( _("Salesman provision breakpoint must be numeric and not less than 0."));
		set_focus('break_pt');
	}
    */
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_query_status($selected_id, $_POST['description']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_query_status($_POST['description']);
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected query status data have been updated'));
		else
			display_notification(_('New query status data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'query','status'))
	{
		display_error(_("Cannot delete this query because transactions have been entered."));
	}
	else
	{
		delete_query_status($selected_id);
		display_notification(_('Selected query status data have been deleted'));
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

$result = get_query_statuses(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("Name"),  "", "");
inactive_control_column($th);
table_header($th);

$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

   label_cell($myrow["status"]);
   	
    inactive_control_cell($myrow["id"], $myrow["inactive"],
		'query_status', 'id');
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
		$myrow = get_query_status($selected_id);

		$_POST['description'] = $myrow["description"];
	}
	hidden('selected_id', $selected_id);
} 

start_table(TABLESTYLE2);

text_row_ex(_("name:"), 'description', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

?>
