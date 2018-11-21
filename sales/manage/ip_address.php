<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "IP Address"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['ip_address']) == 0)
	{
		$input_error = 1;
		display_error(_("The ip address cannot be empty."));
		set_focus('ip_address');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
			update_ip($selected_id, $_POST['ip_address']);
			$note = _('Selected ip address has been updated');
    	} 
    	else 
    	{
			add_ip($_POST['ip_address']);
			$note = _('New ip address has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'stock_master', 'combo1'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this Entry because items have been created using this Combo."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_ip($selected_id);

		display_notification(_('Selected IP has been deleted'));
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

$result = get_ip(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("ID"), "IP Address", "" , "" , "");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);

	label_cell($myrow["id"]);
	add_new_po($myrow["id"],$myrow["ip_address"]);
	label_cell1($myrow["ip_address"]);
	
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'ip', 'id');

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
		$myrow = get_ip_($selected_id);

		$_POST['ip_address']  = $myrow["ip_address"];
	}
	hidden("selected_id", $selected_id);
} 

text_row_ex(_("IP Address:"), 'ip_address', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
