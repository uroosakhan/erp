<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Increment"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Leave"), $js);
include($path_to_root . "/payroll/includes/db/attendance.inc");
include($path_to_root . "/payroll/includes/db/increment_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc"); 
include($path_to_root . "/includes/ui.inc");



simple_page_mode(true);

$fiscal_year = get_company_pref('f_year');
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['increament_amount']) == 0)
	{
		$input_error = 1;
		display_error(_("The Employee Amount can not be empty."));
		set_focus('increament_amount');
	}
/*	if (!is_numeric($_POST['amount']))
	{
		$input_error = 1;
		display_error( _("Value must be numeric."));
		set_focus('amount');
	}*/
//	if (!is_numeric($_POST['payment_shedule']))
//	{
//		$input_error = 1;
//		display_error( _("Value must be numeric."));
//		set_focus('payment_shedule');
//	}
//	if(strlen($_POST['payment_shedule']) >= 120)
//	{
//		$input_error = 1;
//		display_error( _("Plz select less than 120 valu"));
//		set_focus('payment_shedule');
//	}


	/*	if (!is_date($_POST['to_date']))
	{
		display_error( _("Invalid END date "));
		set_focus('to_date');
		return false;
	}
	
	if (strlen($_POST['leave_type']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Leave cannot be empty."));
		set_focus('emp_id');
	}
	$date1=$_POST['from_date'];
	$date2=$_POST['to_date'];
	if ($_POST['to_date']<$_POST['from_date']) 
	{
		$input_error = 1;
		display_error(_("From date should be less than To date ."));
		set_focus('to_date');
		
	}
	$get_duplication=get_emp_info_no($_POST['emp_id'],$_POST['from_date']);
   if($get_duplication>0)
    {
	$input_error = 1;
	display_error(_("Already Exist"));
    }*/
	if ($input_error != 1)
	{
		//$login_date = strtotime($_POST['from_date']); // change x with your login date var
       // $current_date = strtotime($_POST['to_date']); // change y with your current date var
       // $datediff = $current_date - $login_date;
       // $days = floor($datediff/(60*60*24));
		//$days1= $days+1;
 
    	if ($selected_id != -1)
    	{
    		//update_advance($selected_id, $_POST['date'], $_POST['emp_id'], $_POST['amount'], $_POST['type'], $_POST['remarks'],$_POST['approve'],$_POST['payroll_id'],$_POST['payment_shedule'],$_POST['payment']);
			//$note = _('Selected Advance  has been updated');
    	} 
    	else 
    	{
			 $f_year = get_current_fiscalyear();
			add_emp_increament($_POST['increment_code'],$_POST['emp_id'],$_POST['valid_date'],
				$_POST['increment_date'],$_POST['increament_amount'],$_POST['current_salary'],$_POST['remarks'],$f_year['id'],$_POST['division'],
$_POST['project'],$_POST['location']);
			update_emp_basic_salary($_POST['emp_id'],$_POST['increament_amount'],$_POST['current_salary']);
			$note = _('Increment  has been added');

    	}




		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

function handle_delete($selected_id)
{
	delete_advance($selected_id);
   display_notification(_("Increment record has been deleted."));
}

if (isset($_POST['delete'])) 
{		
	handle_delete($_POST['id']);
}

	if (isset($_POST['update'])) 
{		
	handle_update($_POST['id']);
}

start_form();

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);
start_outer_table(TABLESTYLE2);
table_section(1);
dimensions_list_row(_("Divison"), 'division', $_POST['divisionoooo'], 'All division', "", false, 1,true);
pro_list_row(_("Project"), 'project',null, 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);
display_heading("Employee Data");
employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true,true);

if(list_updated('emp_id'))
{
	$emp_data  = get_employee_inc($_POST['emp_id']);
	$_POST['increment_code'] = $emp_data['emp_code'];
	$_POST['current_salary'] = $emp_data['basic_salary'];
	$Ajax->activate('increment_code');
	$Ajax->activate('current_salary');
}

text_cells("Increment Code:",'increment_code');
date_row(_("Valid from"),'valid_date', null,null, 0, 0, 0, null, true);
//month_list_row("Valid From", 'valid_date', null,  _('Month Entry '), true, check_value('show_inactive'));

date_row(_("Increment Date"),'increment_date', null,null, 0, 0, 0, null, true);
textarea_row(_("Remarks:"), 'remarks', null, 20, 2);

hidden("payment", $payment_permonth);
table_section(2);
display_heading("Salary");
text_row("Current Salary:",'current_salary', null, 20, 10);
text_row("Increament Amount:",'increament_amount', null, 20, 10);
table_section(1);
submit_add_or_update_center(-1, '', 'both');




end_outer_table(1);

end_form();
end_page();
?>
