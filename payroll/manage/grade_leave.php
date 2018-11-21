<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Grade Leave Setup"));

include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/payroll/includes/db/grade_leave_db.inc");

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
    		update_grade_leav($selected_id,$_POST['description'], $_POST['pl'], $_POST['sl'], $_POST['cl']);
			$note = _('Selected Grade Leave name has been updated');
    	} 
    	else 
    	{
    		add_grade_leav($_POST['description'], $_POST['pl'], $_POST['sl'],$_POST['cl']);
			$note = _('New Grade Leave name has been added');
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
		delete_grade_leav($selected_id);
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

$result = get_grade_leav(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(_("Id"),_("Description"),_("PL"),_("SL"),_("CL"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell(get_grade_name($myrow["description"]));
	label_cell($myrow["pl"]);
	label_cell($myrow["sl"]);
	label_cell($myrow["cl"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'grade_leave_setup', 'id');
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
		$myrow = get_grade_leav12($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['pl']  = $myrow["pl"];
		$_POST['sl']  = $myrow["sl"];
		$_POST['cl']  = $myrow["cl"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 

//grade_list_row(_("Grade Leave Setup:"), 'description', 40); 
grade_list_row(_("Grade Leave Setup:"), 'description', null, false, null, false, true);

text_row_ex(_("PL:"), 'pl', 40); 
text_row_ex(_("SL:"), 'sl', 40); 
text_row_ex(_("CL:"), 'cl', 40); 

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
