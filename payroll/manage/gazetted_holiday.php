<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Gazetted Holidays"));
$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();

page(_($help_context = "Gazetted Holidays"), false, false, "", $js);
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/payroll/includes/db/gazetted_holidays_db.inc");

//include($path_to_root . "includes/ui/ui_lists.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Description name cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_gaz_holiday($selected_id, $_POST['description'],$_POST['date']);
			$note = _('Selected Description name has been updated');
    	} 
    	else 
    	{
    		add_gaz_holiday($_POST['description'],$_POST['date']);
			$note = _('New Description name has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this group because customers have been created using this group."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_gaz_holiday($selected_id);
		display_notification(_('Selected Description Name has been deleted'));
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

$result = get_gaz_holiday(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(_("Id"),_("Description"),_("Date"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell(sql2date($myrow["date"]));
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'gazetted_holidays', 'id');
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
		$myrow = get_gaz_holidays($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['date']  = sql2date($myrow["date"]);
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 

text_row_ex(_("Description:"), 'description', 40); 
//date_row(_("Date"), 'date');
date_cells(_("Date:"), 'date', '', null, 0, 0, -5);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
