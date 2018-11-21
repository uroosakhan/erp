<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Reports Preference"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

function get_max_rep_no($rep_type)
{
    $sql = "SELECT MAX(rep_no)+1 FROM ".TB_PREF."reports_preference 
    WHERE rep_type =".db_escape($rep_type)." ";
    $result = db_query($sql, "could not get Report No");
    $row = db_fetch_row($result);
    return $row[0];
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['rep_name']) == 0)
	{
		$input_error = 1;
		display_error(_("The reports name cannot be empty."));
		set_focus('rep_name');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
            update_reports_pref($selected_id, $_POST['rep_type'], $_POST['rep_name'], $_POST['rep_no']);
			$note = _('Selected reports preference has been updated');
    	} 
    	else 
    	{
            add_reports_pref($_POST['rep_type'], $_POST['rep_name'], $_POST['rep_no']);
			$note = _('New reports preference has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	if ($cancel_delete == 0) 
	{
        delete_reports_pref($selected_id);

		display_notification(_('Selected reports preference has been deleted'));
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

$result = get_reports_prefs(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Report Type"), _("Report Name"), _("Report No"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["rep_type"]);
	label_cell($myrow["rep_name"]);
	label_cell($myrow["rep_no"]);

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'reports_preference', 'id');

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
		$myrow = get_reports_pref($selected_id);

		$_POST['rep_type']  = $myrow["rep_type"];
		$_POST['rep_name']  = $myrow["rep_name"];
		$_POST['rep_no']  = $myrow["rep_no"];
	}
	hidden("selected_id", $selected_id);
}

rep_types_list_cells(_("Report Type:"), 'rep_type', null, false, true);

$get_rep_no=get_max_rep_no($_POST['rep_type']);

global $Ajax;

$Ajax->activate('rep_no');

text_row_ex(_("Report Name:"), 'rep_name', 30, 200);
text_row_ex(_("Report No:"), 'rep_no', 30, 200, null, $get_rep_no);

end_table(1);
start_table(TABLESTYLE2);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
