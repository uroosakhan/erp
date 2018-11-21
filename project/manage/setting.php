<?php
$page_security = 'SS_CRM_C_S';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Setting"));

include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/project/includes/db/task_db.inc");

simple_page_mode(true);
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{

	$input_error = 0;

	if ($input_error != 1)
	{
		//display_error($_POST['task_tout']);
		update_setting('task_tout', $_POST['task_tout']);

		display_notification_centered(_("Company setup has been updated."));
	}
	//set_focus('task_tout');
	$Ajax->activate('_page_body');
} /* end of if submit */

//---------------------------------------------------------------------------------------------


start_form(true);
//$myrow = get_sett();
//
//$_POST['task_tout'] = $myrow['task_tout'];

start_outer_table(TABLESTYLE2);

table_section();

//text_row_ex(_("Task Timeout:"), 'task_tout', 10, 10, '', null, null, _('seconds'));
text_row_ex(_("Task Timeout:"),'task_tout',10,10,null,get_sett_());

end_outer_table();

//hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

?>
