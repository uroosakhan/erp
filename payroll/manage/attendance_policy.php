<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Attendance Policy"));

include($path_to_root . "/payroll/includes/db/grade_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{
	$input_error = 0;
	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			$check = $_POST['check_in_hour'.$i];
			$chkh=$_POST['check_in_minute'.$i];
			$checkout = $_POST['check_out_hour'.$i];
			$chkhout=$_POST['check_out_minute'.$i];
			
			update_attendance_policy($selected_id, $_POST['emp_grade'], $check, $chkh,
                $checkout,  $chkhout, $_POST['deduction_value'], $_POST['deduction_value_days'], $_POST['deduction_applicable']
				, $_POST['duty_h_aplicable']);
			$note = _('Selected leave type has been updated');
		}
		else
		{
            $check = $_POST['check_in_hour'.$i];
            $chkh=$_POST['check_in_minute'.$i];
            $checkout = $_POST['check_out_hour'.$i];
            $chkhout=$_POST['check_out_minute'.$i];

			add_att_policy($_POST['emp_grade'], $check, $chkh,
                $checkout,  $chkhout, $_POST['deduction_value'], $_POST['deduction_value_days'], $_POST['deduction_applicable']
				, $_POST['duty_h_aplicable']);
				

		}
		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'leave', 'leave_type'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this leave type because leave have been created using this leave type."));
	}
	if ($cancel_delete == 0)
	{
		delete_att_policy($selected_id);
		display_notification(_('Selected leave type has been deleted'));
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

//$result = get_emp_grades(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width = 50%");
$th = array(_("Grade"), _("Deduction Start From"),_("Deduction Start To"),_("Deduction Value"),_("Deduction Value Days"),_("Deduction Applicable"), _("Duty Hour Applicable"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

$result = get_attendance_policy(check_value('show_inactive'));

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);
	label_cell(get_grande_name($myrow["grade"]));
	label_cell($myrow["deduction_start_time"]);
	label_cell($myrow["deduction_end_time"]);
	label_cell($myrow["deduction_value"]);
	label_cell($myrow["deduction_value_days"]);
	if($myrow["deduction_applicable"]==0)
	label_cell("Yes");
	else
        label_cell("No");
    if($myrow["duty_h_aplicable"]==0)
        label_cell("Yes");
    else
        label_cell("No");


	inactive_control_cell($myrow["id"], $myrow["inactive"], 'dept', 'id');
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
		$myrow = get_att_policy($selected_id);

		$_POST['emp_grade']  = $myrow["grade"];
		$_POST['deduction_start_time']  = $myrow["deduction_start_time"];
		$_POST['deduction_end_time']  = $myrow["deduction_end_time"];
		$_POST['deduction_value']  = $myrow["deduction_value"];
		$_POST['deduction_value_days']  = $myrow["deduction_value_days"];
		$_POST['deduction_applicable']  = $myrow["deduction_applicable"];
		$_POST['duty_h_aplicable']  = $myrow["duty_h_aplicable"];
		
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

start_outer_table(TABLESTYLE2);

table_section(1);
table_section(2);
table_section_title(_("Working Days"));
emp_grade_row2(_("Grade:"), 'emp_grade', $_POST['emp_grade'],true);
intime_list_cells("Deduction Start From", 'check_in_hour', 'check_in_minute', 'check_in_am_pm', null, '', '', false);
intime_list_row("Deduction Start To", 'check_out_hour', 'check_out_minute', 'check_out_am_pm', null, '', '', false);
text_row_ex("Deduction", 'deduction_value', 30);
text_row_ex("Deduction Value Days", 'deduction_value_days', 30);
duty_status_list_row(_("Deduction Applicable:"), 'deduction_applicable');
deduction_status_list_row(_("Duty Hours Applicable:"), 'duty_h_aplicable');
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
