<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

check_db_has_tax_groups(_("There are no tax groups defined in the system. At least one tax group is required before proceeding."));

if (isset($_GET['employee_id'])) 
{
	$_POST['employee_id'] = $_GET['employee_id'];
}

$employee_id = get_post('employee_id'); 
//--------------------------------------------------------------------------------------------
function supplier_settings(&$employee_id)
{

	start_outer_table(TABLESTYLE2);

	table_section(1);

	if ($employee_id) 
	{
		//SupplierID exists - either passed when calling the form or from the form itself
//		$myrow = get_employee($_POST['supplier_id']);
		$myrow = get_employee($_POST['employee_id']);

		$_POST['emp_code'] = $myrow["emp_code"];
		$_POST['emp_name'] = $myrow["emp_name"];
		$_POST['emp_father']  = $myrow["emp_father"];
		$_POST['emp_cnic']  = $myrow["emp_cnic"];
		$_POST['emp_reference']  = $myrow["emp_reference"];
		$_POST['emp_home_phone']  = $myrow["emp_home_phone"];
		$_POST['emp_mobile']  = $myrow["emp_mobile"];
		$_POST['emp_email']  = $myrow["emp_email"];
		$_POST['emp_dept']  = $myrow["emp_dept"];
		$_POST['emp_desig']  = $myrow["emp_desig"];
		
		$_POST['emp_address']  = $myrow["emp_address"];
		$_POST['notes']  = $myrow["notes"];
	 	$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
		$_POST['emp_code'] = $_POST['emp_name'] = $_POST['emp_father'] = $_POST['emp_cnic'] = 
			$_POST['emp_reference'] = $_POST['emp_home_phone'] = $_POST['emp_mobile'] = $_POST['emp_email'] = $_POST['emp_dept'] = $_POST['emp_desig'] = $_POST['emp_address'] = $_POST['notes'] = 
'';
	}

	table_section_title(_("Basic Data"));

	text_row(_("*Code:"), 'emp_code', null, 12, 12);
	text_row(_("*Employee Full Name:"), 'emp_name', null, 42, 40);
	text_row(_("*Employee's Father Name:"), 'emp_father', null, 42, 40);	
	text_row(_("*CNIC:"), 'emp_cnic', null, 13, 13);
	text_row(_("Reference:"), 'emp_reference', null, 20, 20);
	text_row(_("Home Phone:"), 'emp_home_phone', null, 15, 15);
	text_row(_("Mobile:"), 'emp_mobile', null, 15, 15);		
	text_row(_("Email:"), 'emp_email', null, 42, 40);	
	text_row(_("*Department:"), 'emp_dept', null, 42, 40);	
	text_row(_("*Designation:"), 'emp_desig', null, 42, 40);		
	

	table_section(2);

	table_section_title(_("Addresses"));
	textarea_row(_("Physical Address:"), 'emp_address', null, 35, 5);

	table_section_title(_("General"));
	textarea_row(_("General Notes:"), 'notes', null, 35, 5);


	if ($employee_id)  {
		start_row();
		echo '<td class="label">'._('Click here to').'</td>';
	  	hyperlink_params_separate_td($path_to_root . "/employee/reporting/prn_redirect.php?PARAM_0=$employee_id&REP_ID=1033",
			'<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Print")).'</b>');
		end_row();
	}

//http://hisaab.pk/employee/reporting/prn_redirect.php?PARAM_0=298-13&PARAM_1=298-13&PARAM_2=0&PARAM_3=0&PARAM_4=&PARAM_5=0&REP_ID=110
//, "PARAM_0=".$employee_id.(@$_REQUEST['popup'] ? '&popup=1':'')
	end_outer_table(1);

	div_start('controls');
	if ($employee_id) 
	{
		submit_center_first('submit', _("Update Employee"), 
		  _('Update Employee data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', get_post('employee_id'), _("Select this employee and return to document entry."));
		submit_center_last('delete', _("Delete Employee"), 
		  _('Delete employee data if have been never used'), true);
	}
	else 
	{
		submit_center('submit', _("Add New Employee Details"), true, '', 'default');
	}
	div_end();
}

if (isset($_POST['submit'])) 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['emp_code']) == 0 || $_POST['emp_code'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee code must be entered."));
		set_focus('emp_code');
	}
	if (strlen($_POST['emp_name']) == 0 || $_POST['emp_name'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee name must be entered."));
		set_focus('emp_name');
	}
		if (strlen($_POST['emp_father']) == 0 || $_POST['emp_father'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee father name must be entered."));
		set_focus('emp_father');
	}
		if (strlen($_POST['emp_cnic']) == 0 || $_POST['emp_cnic'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee CNIC must be entered."));
		set_focus('emp_cnic');
	}
		if (strlen($_POST['emp_dept']) == 0 || $_POST['emp_dept'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee depertment must be entered."));
		set_focus('emp_dept');
	}
		if (strlen($_POST['emp_desig']) == 0 || $_POST['emp_desig'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee designation must be entered."));
		set_focus('emp_desig');
	}
	
	if ($input_error !=1 )
	{

		begin_transaction();
		if ($employee_id) 
		{
			update_employee($_POST['employee_id'], $_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'], $_POST['emp_cnic'], $_POST['emp_reference'], $_POST['emp_home_phone'], $_POST['emp_mobile'], $_POST['emp_email'], $_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_address'], $_POST['notes']);

			$Ajax->activate('employee_id'); // in case of status change
			display_notification(_("Employee has been updated."));
		} 
		else 
		{
			add_employee($_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'], $_POST['emp_cnic'], $_POST['emp_reference'], $_POST['emp_home_phone'], $_POST['emp_mobile'], $_POST['emp_email'], $_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_address'], $_POST['notes']);

			display_notification(_("A new employee has been added."));
			$Ajax->activate('_page_body');
		}
		commit_transaction();
	}

} 
elseif (isset($_POST['delete']) && $_POST['delete'] != "") 
{
	//the link to delete a selected record was clicked instead of the submit button

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'supp_trans' , purch_orders

	
	if ($cancel_delete == 0) 
	{
		delete_employee($_POST['employee_id']);

		unset($_SESSION['employee_id']);
		$employee_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete supplier
}

start_form();

if (db_has_suppliers()) 
{
	start_table(false, "", 3);
//	start_table(TABLESTYLE_NOBORDER);
	start_row();
//	employee_list_cells(_("Select a employee: "), 'employee_id', null,
//		  _('New supplier'), true, check_value('show_inactive'));

	employee_list_cells(_("Select a employee: "), 'employee_id', null,
		  _('New employee'), true);

	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('employee_id');
		set_focus('employee_id');
	}
} 
else 
{
	hidden('employee_id', get_post('employee_id'));
}

if (!$employee_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
		'settings' => array(_('&General'), $employee_id),
		//'contacts' => array(_('&Contacts'), $contacts),
		//'transactions' => array(_('&Transactions'), $supplier_id),
		//'orders' => array(_('Purchase &Orders'), $supplier_id),
	));
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			supplier_settings($employee_id); 
			break;
		case 'printcard':
			//$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/reporting/reports_main.php?Class=0&REP_ID=1033");
			break;

		case 'transactions':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/supplier_inquiry.php");
			break;
		case 'orders':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/po_search_completed.php");
			break;
	};
br();
tabbed_content_end();
hidden('popup', @$_REQUEST['popup']);
end_form();

end_page(@$_REQUEST['popup']);

?>