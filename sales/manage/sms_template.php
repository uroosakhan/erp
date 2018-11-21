<?php
$page_security = 'SA_SALESGROUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/sales_sms_db.inc");
page(_($help_context = "Add Template"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

$template = get_all_templates();
//var_dump($template);
//$temp_name = $my_row['temp_name'];
//print_r($temp_name);
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	$input_error = 0;

	/*if (strlen($_POST['template']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Template Name Cannot Be Empty."));
		set_focus('source');
	}*/

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_template($selected_id, $_POST['template'], $_POST['filterType']);
			$note = _('Selected Template Has Been Updated');
    	} 
    	else
		{
			add_template($_POST['template'], $_POST['filterType']);
			$note = ('Template Has Been Again Added Edit Your Template');
    	/*	display_error(_("The Template Name Cannot Be Empty."));*/
    	}
		
    	
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
	if ($cancel_delete == 0) 
	{
		delete_template($selected_id);
		display_notification(_('Selected Template Has Been Deleted'));
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

$result = get_templates();

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array( _("Template") ,_("Type Name"), "", "");

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	//label_cell($myrow["temp_name"]);
	label_cell($myrow["template"]);
	label_cell(get_types_names($myrow["filterType"]));
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_template($selected_id);

		//$_POST['temp_name']  = $myrow["temp_name"];
		$_POST['template']  = $myrow["template"];
		$_POST['filterType']  = $myrow["filterType"];
		
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
} 
//text_row_ex(_("Template Name:"), 'temp_name', 15);  
textarea_row(_("Template"), 'template', $_POST['template'], 50, 10);
types_name_list_cells("Type Name", 'filterType');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
