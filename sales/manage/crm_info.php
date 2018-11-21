<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Personal Information"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['spouse_name']) == 0)
	{
		$input_error = 1;
		display_error(_("The area description cannot be empty."));
		set_focus('spouse_name');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_crm_info($selected_id, $_POST['spouse_name'], $_POST['child_name'],
                $_POST['birth_date'], $_POST['customer_id']);
			$note = _('Selected sales area has been updated');
            refresh('cust_info.php');
    	} 
    	else 
    	{
    		add_crm_info($_POST['spouse_name'], $_POST['child_name'], $_POST['birth_date'],
                $_POST['customer_id']);
			$note = _('New sales area has been added');
            refresh('cust_info.php');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
//	{
//		$cancel_delete = 1;
//		display_error(_("Cannot delete this area because customer branches have been created using this area."));
//	}
	if ($cancel_delete == 0) 
	{
        delete_crm_info($selected_id);
		display_notification(_('Selected sales area has been deleted'));
        refresh('cust_info.php');
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

$result = get_crm_info($_GET['debtor_no']);

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Spouse Name"), _("Child's Name"), _("Birth Date"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);

	label_cell($myrow["spouse_name"]);
	label_cell($myrow["child_name"]);
	label_cell(sql2date($myrow["birth_date"]));

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'crm_info', 'id');

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
		$myrow = get_crm_infor($selected_id);
		$_POST['spouse_name']  = $myrow["spouse_name"];
		$_POST['child_name']  = $myrow["child_name"];
		$_POST['birth_date']  = sql2date($myrow["birth_date"]);
	}
	hidden("selected_id", $selected_id);
}

text_row_ex(_("Spouse Name: "), 'spouse_name', 30);
text_row_ex(_("Child's Name: "), 'child_name', 30);

date_row(_("Birth Date: "), 'birth_date');

$customer_id=$_GET['debtor_no'];
hidden("customer_id", $customer_id);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
