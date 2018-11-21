<?php
//$page_security = 'SS_PAYROLL';
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Deduction"));

include($path_to_root . "/payroll/includes/db/deduction_db.inc");
include($path_to_root . "/payroll/includes/db/document_type_db.inc");


include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The deduction description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			update_document_type($selected_id, $_POST['description']);
			$note = _('Selected deduction has been updated');
			refresh('deduction.php');
		}
		else
		{
			add_document_type($_POST['description']);
			$note = _('New deduction has been added');
			refresh('deduction.php');
		}

		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'payroll_deduction', 'deduct_id'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this deduction because payroll deduction have been created using this deduction."));
	}
	if ($cancel_delete == 0)
	{
		delete_emp_deduction($selected_id);
		display_notification(_('Selected deduction has been deleted'));
		refresh('deduction.php');
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

$result = get_document_type(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"), _("Deduction Name"), "Edit", "Delete");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'deduction', 'id');
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
		$myrow = get_document_types($selected_id);

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

text_row_ex(_("Deduction Name:"), 'description', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
