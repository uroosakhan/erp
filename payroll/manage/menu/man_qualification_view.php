<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/man_qualification_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");


$page_security = 'SA_PAYROLL_SETUP';

//set_page_security( @$_POST['order_view_mode'],
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE'),
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE')
//);
simple_page_mode(true);

$id=$_GET['employee_id'];
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
			update_man_qualification($selected_id,$_POST['emp_name'],$_POST['degree'],$_POST['passing_year']
				,$_POST['institute'],$_POST['passing_percent'],$_POST['remarks']);
			$note = _('Selected employee Qualification has been updated');
			//refresh('man_qualification.php');
		}
		else
		{


				add_man_qualification($_POST['emp_name'], $_POST['degree'], $_POST['passing_year']
					, $_POST['institute'], $_POST['passing_percent'], $_POST['remarks'], $_POST['id']);
				$note = _('New employee Qualification has been added');
				//refresh('man_qualification.php');
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
	delete_man_qualification($selected_id);
	display_notification(_('Selected employee Qualification has been deleted'));
	//refresh('man_qualification.php');
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
$result = get_emp_man_qualification_all($_GET['employee_id']);
start_table(TABLESTYLE, "width=30%");
$th = array(/*_("Employee Name"),*/ _("Qualification Degree"), _("Passing Year"),
	_("Institute"),_("Passing %"),_("Remarks"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);

	//label_cell($myrow["emp_name"]);
	label_cell($myrow["degree"]);
	label_cell($myrow["passing_year"]);
	label_cell($myrow["institute"]);
	label_cell($myrow["passing_percent"]);
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
		$myrow = get_emp_man_qualification($selected_id);

		$_POST['emp_name']  = $myrow["emp_name"];
		$_POST['degree']  = $myrow["degree"];
		$_POST['passing_year']  = $myrow["passing_year"];
		$_POST['institute']  = $myrow["institute"];
		$_POST['passing_percent']  = $myrow["passing_percent"];
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
//text_row(_("Employee Name:"), "emp_name");
text_row(_("Qualification Degree:"), "degree");
text_row(_("Passing Year:"), "passing_year");
text_row(_("Institute:"), "institute");
text_row(_("Passing %:"), "passing_percent");
text_row(_("Remarks:"), "remarks");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(false, false, false, ST_BANKPAYMENT, $trans_no);
?>
