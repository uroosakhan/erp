<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/sales/includes/db/wht_tax_category_db.inc");

page(_($help_context = "WHT Tax Category"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0) 
	{
		$input_error = 1;
		display_error(_("The WHT Tax Category description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_wht_tax_category($selected_id, $_POST['description']);
			$note = _('Selected WHT Tax Category has been updated');
    	} 
    	else 
    	{
			add_wht_tax_category($_POST['description']);
			$note = _('New WHT Tax Category has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this WHT Tax Category because customer branches have been created using this area."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_wht_tax_category($selected_id);

		display_notification(_('Selected WHT Tax Category has been deleted'));
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

$result = get_wht_tax_categories(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Name"), "");
inactive_control_column($th);
table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{

	alt_table_row_color($k);
		
	label_cell($myrow["description"]);

	inactive_control_cell($myrow["id"], $myrow	["inactive"], 'wht_tax_category', 'id');

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
// 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
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
		$myrow = get_wht_tax_category($selected_id);

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
} 

text_row_ex(_("WHT Tax Category Name:"), 'description', 130);

end_table(1);

if($selected_id != -1) {
	submit_add_or_update_center($selected_id == -1, '', 'both');
}
end_form();

end_page();
