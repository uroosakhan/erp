<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Man_month"),true);

include($path_to_root . "/payroll/includes/db/man_month_db.inc");

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 
simple_page_mode(true);

$id=$_GET['id'];
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	//if (strlen($_POST['description']) == 0)
	/*{
		$input_error = 1;
		display_error(_("The allowances description cannot be empty."));
		set_focus('description');
	}*/

	if ($input_error != 1)
	{
		
    	if ($selected_id != -1) 
    	{
    		update_man_month($selected_id,$_POST['text_field_1'],$_POST['text_field_2'],$_POST['text_field_3']
				,$_POST['text_field_4']);
			$note = _('Selected field has been updated');
			refresh('emp_allowance.php');
    	} 
    	else 
    	{
    		add_man_month($_POST['text_field_1'],$_POST['text_field_2'],$_POST['text_field_3']
			,$_POST['text_field_4'],$_POST['id']);
			$note = _('New field has been added');
			refresh('man_month.php');
    	}
   
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'payroll_allowance', 'allow_id'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employee have been created using this dept."));
	} 
	if ($cancel_delete == 0) 
	{*/
		delete_man_month($selected_id);
		display_notification(_('Selected field has been deleted'));
		refresh('man_month.php');
	//} //end if Delete group
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

$result = get_emp_man_month11($_GET['id']);

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("Employee Name"), _("Employee Project"), _("Employee Project"),
	_("Employee Project"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);

	label_cell($myrow["text_field_1"]);
	label_cell($myrow["text_field_2"]);
	label_cell($myrow["text_field_3"]);
	label_cell($myrow["text_field_4"]);
	//$_POST['month']  = $myrow["month"];

	//label_cell(get_employee_name11($myrow["emp_id"]));
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'man_month', 'text_field_1');
	edit_button_cell("Edit" . $myrow["id"], _("Edit"));
	delete_button_cell("Delete" . $myrow["id"], _("Delete"));
	end_row();
}
//inactive_control_row($th);
end_table(1);

//echo $emp_id;
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		$myrow = get_emp_man_month ($selected_id);

		$_POST['text_field_1']  = $myrow["text_field_1"];
		$_POST['text_field_2']  = $myrow["text_field_2"];
		$_POST['text_field_3']  = $myrow["text_field_3"];
		$_POST['text_field_4']  = $myrow["text_field_4"];
		//$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	
//	label_row(_("text_field_1"), $myrow["text_field_1"]);
//	label_row(_("text_field_2"), $myrow["text_field_2"]);
//	label_row(_("text_field_3"), $myrow["text_field_3"]);
//	label_row(_("text_field_4"), $myrow["text_field_4"]);

} 
//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
hidden("id", $id);
 //emp_dept_row( _("Department:"), 'dept_id', null,  _("Select department: "), 
//	true, check_value('show_inactive'));
	///month_list_cells( _("Month:"), 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
text_row(_("Employee Name:"), "text_field_1");
text_row(_("Employee Project:"), "text_field_2");
text_row(_("Employee Project:"), "text_field_3");
text_row(_("Employee Project:"), "text_field_4");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(true, false, false, ST_BANKPAYMENT, $trans_no);
?>
