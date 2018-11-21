<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/db_pager.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Employment History"), @$_REQUEST['popup'], false, "", $js);


//page(_($help_context = "Employment History"),true, false, "", $js);

include($path_to_root . "/payroll/includes/db/employment_history_db.inc");

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
			update_employment_history($selected_id,$_POST['company_name'],$_POST['date_from'],$_POST['date_to']
				,$_POST['designation'],$_POST['remarks']);
			$note = _('Selected company has been updated');
			refresh('employment_history.php');
    	} 
    	else 
    	{
			add_employment_history($_POST['company_name'],$_POST['date_from'],$_POST['date_to']
			,$_POST['designation'],$_POST['remarks'],$_POST['id']);
			$note = _('New company has been added');
			refresh('employment_history.php');
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
	delete_employment_history($selected_id);
		display_notification(_('Selected company has been deleted'));
		refresh('employment_history.php');
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



start_form();
$result = get_employment_history_all($_GET['id']);
start_table(TABLESTYLE, "width=30%");
$th = array(_("Company Name"), _("Date From"), _("Date To"),
	_("Designation"),_("Remarks"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);

	label_cell($myrow["company_name"]);
	label_cell(sql2date($myrow["date_from"]));
	label_cell(sql2date($myrow["date_to"]));
	label_cell($myrow["designation"]);
	label_cell($myrow["remarks"]);
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
		$myrow = get_employment_history($selected_id);

		$_POST['company_name']  = $myrow["company_name"];
		$_POST['date_from']  = sql2date($myrow["date_from"]);
		$_POST['date_to']  = sql2date($myrow["date_to"]);
		$_POST['designation']  = $myrow["designation"];
		$_POST['remarks']  = $myrow["remarks"];
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
text_row(_("Company Name:"), "company_name");
date_row(_("Date From:"), "date_from");
date_row(_("Date To:"), "date_to");
text_row(_("Designation:"), "designation");
text_row(_("Remarks:"), "remarks");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(true, false, false, ST_BANKPAYMENT, $trans_no);
?>
