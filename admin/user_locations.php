<?php


$page_security = 'SA_USERS';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
page(_($help_context = "User Locations"));
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admin/db/users_db.inc");

simple_page_mode(true);


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;
	
	$num = check_user_location_duplication(get_post('user_id'), get_post('loc_code'));
	
	if (!get_post('user_id'))
	{
		$input_error = 1;
		display_error( _("User has not been selected."));
		set_focus('user_id');
		
	}
	elseif (!get_post('loc_code'))
	{
		$input_error = 1;
		display_error( _("Location has not been selected."));
		set_focus('loc_code');
		
	}
	elseif ($num > 0)
	{
		$input_error = 1;
		display_error( _("Selected user has already this location ".get_post('loc_code')));
		set_focus('loc_code');
		
	}
	

	

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_users_locations($selected_id, $_POST['user_id'], $_POST['loc_code']);
    		display_notification_centered(_("The selected user's location has been updated."));
    	} 
    	else 
    	{
    		add_user_locations( $_POST['user_id'], $_POST['loc_code']);
			$id = db_insert_id();
			// use current user display preferences as start point for new user
			display_notification_centered(_("A new user's location has been added."));
    	}
    
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	delete_user_location($selected_id);
	display_notification_centered(_("User's location has been deleted."));
	$Mode = 'RESET';
	
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);	// clean all input fields
//	$_POST['show_inactive'] = $sav;
}
//-------------------------------------------------------------------------------------------------

if(get_post('user_id'))
$user_id = get_post('user_id');

$result = get_users_locations(check_value('show_inactive'), $user_id);
start_form();
 start_table(TABLESTYLE2, "width=100%");

$th = array(  _(" "), _("User"), _("Location"), "");
//inactive_control_column($th);
table_header($th);	
$k = 0; //row colour counter



while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
	
	label_cell($myrow['id']);
	label_cell(get_user_name($myrow["user_id"]));
	label_cell(get_location_description($myrow["loc_code"]));
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'user_locations', 'id');
	//edit_button_cell("Edit".$myrow["id"], _("Edit")); //dz 19-8-18
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	
	
	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

 start_table(TABLESTYLE2, "width=30%");

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_user_location($selected_id);

		$_POST['id'] = $myrow["id"];
		$_POST['user_id'] = $myrow["user_id"];
		$_POST['loc_code'] = $myrow["loc_code"];
		$_POST['status'] = $myrow["inactive"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 


label_row(_("You"), $_SESSION["wa_current_user"]->username);
//current_user_locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $current_user)
//current_user_locations_list_row(_("Users ").":", 'asad',  null, false, false, $_SESSION["wa_current_user"]->user);
//user
users_list_row_(_("Users ").":", 'user_id', null, null, true, true);
current_user_locations_list_row(_("Location :"), 'loc_code');

//check_row(_("Status :"), 'status', $_POST['status'], false, _('Set the status for user \'s location.'));

end_table(1);


submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
