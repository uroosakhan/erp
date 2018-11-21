<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/employee_nomination_db.inc");


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
			update_employee_family_details($selected_id,$_POST['nominee_name'],$_POST['relation'],$_POST['age']
				,$_POST['share'],$_POST['remarks']);
			$note = _('Selected employee nomination has been updated');
		//	refresh('employee_nomination.php');
		}
		else
		{

//			$result=validate_employee_family_details($_POST['nominee_name']);

		 {
				add_employee_family_details($_POST['nominee_name'], $_POST['relation'], $_POST['age']
					, $_POST['share'], $_POST['remarks'], $_POST['id']);
				$note = _('New employee nomination has been added');
				//	refresh('employee_nomination.php');
			}


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
	delete_employee_family_details($selected_id);
	display_notification(_('Selected employee nomination has been deleted'));
	//refresh('employee_nomination.php');
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
$result = get_employee_family_details_all($_GET['employee_id']);
start_table(TABLESTYLE, "width=30%");
$th = array(_("Nominee Name"), _("Relation with Nominee"), _("Nominee Age"),
	_("Nominee Share"),_("Remarks"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);

	label_cell($myrow["nominee_name"]);
	label_cell($myrow["relation"]);
	label_cell($myrow["age"]);
	label_cell($myrow["share"]);
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
		$myrow = get_employee_family_details($selected_id);

		$_POST['nominee_name']  = $myrow["nominee_name"];
		$_POST['relation']  = $myrow["relation"];
		$_POST['age']  = $myrow["age"];
		$_POST['share']  = $myrow["share"];
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
text_row(_("Nominee Name:"), "nominee_name");
text_row(_("Relation with Nominee:"), "relation");
text_row(_("Nominee Age:"), "age");
text_row(_("Nominee Share:"), "share");
text_row(_("Remarks:"), "remarks");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(false, false, false, ST_BANKPAYMENT, $trans_no);
?>
