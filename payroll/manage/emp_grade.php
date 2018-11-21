<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Departments"));

include($path_to_root . "/payroll/includes/db/dept_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;
//
//	if (strlen($_POST['description']) == 0)
//	{
//		$input_error = 1;
//		display_error(_("The department description cannot be empty."));
//		set_focus('description');
//	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
			update_hourly_rate($selected_id,$_POST['dept'],$_POST['grade'],$_POST['salary_from'],$_POST['salary_to'],$_POST['ot'],$_POST['state']);
			$note = _('Selected department has been updated');
    	} 
    	else 
    	{
			add_hourly_rate($_POST['dept'],$_POST['grade'],$_POST['salary_from'],$_POST['salary_to'],$_POST['ot'],$_POST['state']);
			$note = _('New department has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'employee', 'emp_dept'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employee have been created using this dept."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_hourly_rate($selected_id);
		display_notification(_('Selected department has been deleted'));
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



///=======================================================

$result = get_hourly_rates(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"), _("Grade"),  _("OT"),_("Status"), "Edit", "Delete");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
	label_cell($myrow["id"]);
// 	label_cell(get_emp_dept_name($myrow["dept"]));
	label_cell(get_emp_grade($myrow["grade"]));
// 	label_cell($myrow["salary_from"]);
// 	label_cell($myrow["salary_to"]);
	label_cell($myrow["ot"]);


	if($myrow["state"] == 1) {
		label_cell('Yes');
	}
	else
		{
			label_cell('No');
		}


	inactive_control_cell($myrow["id"], $myrow["inactive"], 'hourly_rate', 'id');
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
		$myrow = get_hourly_rate($selected_id);

// 		$_POST['dept']  = $myrow["dept"];

		$_POST['grade']  = $myrow["grade"];
// 		$_POST['salary_from']  = $myrow["salary_from"];
// 		$_POST['salary_to']  = $myrow["salary_to"];
		$_POST['ot']  = $myrow["ot"];
		$_POST['state']  = $myrow["state"];


	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

// emp_dept_cells(_("Department:"), 'dept', null,true);
emp_grade_row2(_("Grade:"), 'grade', null,true);
// text_row_ex(_("Salary From:"), 'salary_from', 30);
// text_row_ex(_("Salary To:"), 'salary_to', 30);
text_row_ex(_("OT Rate:"), 'ot', 30);
yesno_list_row(_("State:"), 'state');
//salary_from_list_cells_new(_("Salary From:"), 'from', null, "", "", false);
//salary_to_list_cells_new(_("Salary To:"), 'to', null, "", "", false);

//emp_grade_row2(_("OT Rate:"), 'emp_grade', null,true);

//emp_salary_row(_("OT Rate:"), 'late', null,true);
//emp_salary_row(_("Over Time:"), 'over_time', null,true);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
