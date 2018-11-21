<?php

$page_security = 'SA_SALESMAN';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/cashflow_db.inc");

page(_($help_context = "Cashflow Categories"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
//	$input_error = 0;
//
//	if (strlen($_POST['name']) == 0)
//	{
//		$input_error = 1;
//		display_error(_("The cashflow categories name cannot be empty."));
//		set_focus('name');
//	}
//	$pr1 = check_num('provision', 0,100);
//	if (!$pr1 || !check_num('provision2', 0, 100)) {
//		$input_error = 1;
//		display_error( _("Salesman provision cannot be less than 0 or more than 100%."));
//		set_focus(!$pr1 ? 'provision' : 'provision2');
//	}
//	if (!check_num('break_pt', 0)) {
//		$input_error = 1;
//		display_error( _("Salesman provision breakpoint must be numeric and not less than 0."));
//		set_focus('break_pt');
//	}
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_cashflow_category($selected_id, $_POST['name'], $_POST['c_type'], $_POST['flowtype']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_cash_flow_category($_POST['name'], $_POST['c_type'], $_POST['flowtype']);
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected cashflow categories data have been updated'));
		else
			display_notification(_('New cashflow categories data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'cust_branch', 'cashflow_categories'))
//	{
//		display_error(_("Cannot delete this cashflow categories because categories are set up referring to this cashflow_categories - first alter the categories concerned."));
//	}
//	else
//	{
		delete_cashflow_category($selected_id);
		display_notification(_('Selected cashflow categories data have been deleted'));
//	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//------------------------------------------------------------------------------------------------

$result = get_cashflow_category(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='50%'");
$th = array(_("Name"), _("Cashflow Type") , _("Flow Type") , "", "");
inactive_control_column($th);
table_header($th);

$k = 0;

//marina
while ($myrow = db_fetch($result))
{


	if ($myrow["flowtype"] == 0)

	{
		$a = 'Payments';

	}
	else
	{
		$a = 'Receipts';

	}
////////////
	alt_table_row_color($k);
    label_cell($myrow["name"]);
   	label_cell(get_cash_flow_type($myrow["c_type"]));
   	label_cell(($a));
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'cashflow_categories', 'id');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
  	end_row();

} //END WHILE LIST LOOP

inactive_control_row($th);
end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------

$_POST['salesman_email'] = "";
if ($selected_id != -1) {
	if ($Mode == 'Edit') {
		//editing an existing Sales-person
		$myrow = get_cashflow_category2($selected_id);

		$_POST['name'] = $myrow["name"];
		$_POST['c_type'] = $myrow["c_type"];
		$_POST['flowtype'] = $myrow["flowtype"];
	}
	hidden('selected_id', $selected_id);
}
else{
//} elseif ($Mode != 'ADD_ITEM') {
//	$_POST['provision'] = percent_format(0);
//	$_POST['break_pt'] = price_format(0);
//	$_POST['provision2'] = percent_format(0);	
}

start_table(TABLESTYLE2);

text_row_ex(_("Name:"), 'name', 30);
cashflow_cat_list_row( _("Cashflow Type:"), 'c_type', null);
//text_row_ex(_("Cashflow Type:"), 'c_type', 20);
yesno_list_row(_("Flow Type:"), 'flowtype', $_POST['flowtype'] , "Receipts", "Payments", false);
//text_row_ex(_("Flow Type:"), 'flowtype', 20);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

