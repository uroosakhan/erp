<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/emp_deduction_db.inc");

include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");


$page_security = 'SA_PAYROLL_SETUP';

//set_page_security( @$_POST['order_view_mode'],
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE'),
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE')
//);
simple_page_mode(true);

$employee_id=$_GET['employee_id'];
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0)
		/*{
            $input_error = 1;
            display_error(_("The allowances description cannot be empty."));
            set_focus('description');
        }*/

		if ($input_error != 1)
		{
			if ($selected_id != -1)
			{
				update_emp_deduction11($selected_id, $_POST['employee_id'], $_POST['deduc_id'], $_POST['amount']);
				$note = _('Selected deduction has been updated');
				//refresh('emp_deduction.php');
			}
			else
			{

				$result=validate_deduction($_POST['employee_id'],$_POST['deduc_id']);

				if(!$result) {
					add_emp_deduction11($_POST['employee_id'], $_POST['deduc_id'], $_POST['amount']);
					$note = _('New deduction has been added');
					//refresh('emp_deduction.php');
				}
				else{
					
					display_error("Cannot Add Duplicate Deduction Name");
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

	/*if (key_in_foreign_table($selected_id, 'employee', 'emp_dept'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this deduction because Employee have been created using this dept."));
	}
	if ($cancel_delete == 0)
	{*/
	delete_emp_deduction11($selected_id);
	display_notification(_('Selected deduction has been deleted'));
	//refresh('emp_deduction.php');
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


//display_error($_GET['employee_id']);
///=======================================================
$emp_id=$_GET['employee_id'];
$result = get_emp_deductions11($emp_id);

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"),  _("Deduction Name"), _("Amount"), "Edit", "Delete");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

	label_cell($myrow["id"]);
	//label_cell(get_employee_name11($myrow["emp_id"]));
	label_cell(get_emp_deduction_name($myrow["deduc_id"]));
	label_cell($myrow["amount"]);
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'dept', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
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
		$myrow = get_emp_deduction11($selected_id);

		$_POST['emp_id']  = $myrow["emp_id"];
		$_POST['deduc_id']  = $myrow["deduc_id"];
		$_POST['amount']  = $myrow["amount"];
	}
	hidden("selected_id", $selected_id);
	//hidden("emp_id", $emp_id);
	label_row(_("ID"), $myrow["id"]);
}

//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
hidden("emp_id", $_GET['employee_id']);
emp_deduction_row(_("Deduction Name:"), 'deduc_id', null,false);
text_row(_("Deduction Amount:"), "amount");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');





if (!@$_GET['popup'])
{
	end_form();
	end_page(false);
}
?>