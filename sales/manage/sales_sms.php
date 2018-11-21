<?php
$page_security = 'SA_SALESGROUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/sales_sms_db.inc");
page(_($help_context = "SMS Template"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['source']) == 0) 
	{
		$input_error = 1;
		display_error(_("The source cannot be empty."));
		set_focus('source');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_sms_template($selected_id, $_POST['source'], $_POST['mask'], $_POST['message'], $_POST['password'], $_POST['api']);
			$note = _('Selected Api has been updated');
    	} 
    	else 
    	{
    		add_sms_template($_POST['source'], $_POST['mask'], $_POST['message'], $_POST['password'], $_POST['api']);
			$note = _('New Api detail has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this group because customers have been created using this group."));
	} */
	if ($cancel_delete == 0) 
	{
		delete_sms_template($selected_id);
		display_notification(_('Selected sales group has been deleted'));
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
//-------------------------------------------------------------------------------------------------

$result = get_sms_templates();

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("Source"), _("Mask"),  _("Message"), _("Password"),  _("Api"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["source"]);
	label_cell($myrow["mask"]);
	label_cell($myrow["message"]);
	label_cell($myrow["password"]);
	label_cell($myrow["api"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'sms', 'id');
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
		$myrow = get_sms_template($selected_id);

		$_POST['source']  = $myrow["source"];
		$_POST['mask']  = $myrow["mask"];
		$_POST['message']  = $myrow["message"];
		$_POST['password']  = $myrow["password"];
		$_POST['api']  = $myrow["api"];
		
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 

text_row_ex(_("Source:"), 'source', 15); 
text_row_ex(_("Mask:"), 'mask', 15);  
textarea_row(_("Message"), 'message', $_POST['message'], 35, 3);
text_row_ex(_("Password:"), 'password', 15); 
text_row_ex(_("Api:"), 'api', 50, 100); 

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>