<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
	
page(_($help_context = "Leave"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Leave"), $js);

include($path_to_root . "/payroll/includes/db/emp_info_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{


	if (!is_date($_POST['to_date']))
	{
		display_error( _("Invalid END date "));
		set_focus('to_date');
		return false;
	}
	$input_error = 0;

	if (strlen($_POST['emp_id']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Employee Name cannot be empty."));
		set_focus('emp_id');
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
    }
    $duplication=employee_leave_attendance_duplication($_POST['emp_id'],$_POST['f_year']);
    if($duplication>0)
    {
        $input_error = 1;
        display_error(_("Already Apply"));
    }
	if ($input_error != 1)
	{
		$login_date = strtotime(date2sql($_POST['from_date'])); // change x with your login date var
        $current_date = strtotime(date2sql($_POST['to_date'])); // change y with your current date var
        $datediff = $current_date - $login_date;
        $days = floor($datediff/(60*60*24));
		$days1= $days+1;
 //display_error($datediff."---".$days);
    	if ($selected_id != -1) 
    	{
    		update_emp_info($selected_id, $_POST['emp_id'], $_POST['from_date'], $_POST['to_date'], $_POST['leave_type'], $_POST['no_of_leave'],$_POST['reason'],$_POST['approve'],$_POST['f_year']);
			$note = _('Selected leave  has been updated');
    	} 
    	else 
    	{
    		add_emp_info( $_POST['emp_id'], $_POST['from_date'], $_POST['to_date'], $_POST['leave_type'],
				$days1,$_POST['reason'],$_POST['approve'],$_POST['f_year']);
			$note = _('New leave  has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this leave type because customers have been created using this group."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_emp_info($selected_id);
		display_notification(_('Selected leave  has been deleted'));
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



function handle_delete($selected_id)
{
		delete_emp_info($selected_id);
  display_notification(_("Leave record has been deleted."));		
}
if (isset($_POST['delete'])) 
{
handle_delete($_POST['id']);
}
function handle_update($selected_id)
{
update_emp_info($selected_id, $_POST['emp_id'], $_POST['from_date'], $_POST['to_date'], $_POST['leave_type'], $_POST['no_of_leave'],$_POST['reason'],$_POST['approve'],$_POST['f_year']);
  display_notification(_("Leave record has been Updated."));		
}
if (isset($_POST['update'])) 
{
handle_update($_POST['id']);
}
///=======================================================

//$result = get_emp_leave_types(check_value('show_inactive'));
$id=$_GET['id'];
echo $id;
start_form();
/*start_table(TABLESTYLE, "width=30%");
$th = array(_("ID"), _("Leave Type"), "Edit", "Delete");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($myrow["description"]);
	label_cell($myrow["description"]);
	label_cell($myrow["description"]);
	label_cell($myrow["description"]);
	
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'dept', 'id');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);*/

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1) 
//{
 	//if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_emp_info($_GET['id']);

		$_POST['emp_id']  = $myrow["emp_id"];
		$_POST['from_date']  = sql2date($myrow["from_date"]);
		$_POST['to_date']  = sql2date($myrow["to_date"]);
		$_POST['leave_type']  = $myrow["leave_type"];
		$_POST['no_of_leave']  = $myrow["no_of_leave"];
		$_POST['reason']  = $myrow["reason"];
		$_POST['approve']  = $myrow["approve"];
		
		
	//}
	hidden("no_of_leave", $days1);
	//hidden("selected_id", $selected_id);
	hidden("id", $id);

	//label_row(_("ID"), $myrow["id"]);
//} 
/*
	$d1 = new DateTime($_POST['from_date']);
    $d2 = new DateTime($_POST['to_date']);
    $interval = $d1->diff($d2);

echo $interval->format('%R%a days');*/
//
 



employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true);
date_row(_("From Date"),'from_date', null,null, 0, 0, 0, null, true);
date_row(_("To Date:"),'to_date', null,null, 0, 0, 0, null, true);
//date_cells(_("From Date:"), 'from_date');
// date_cells(_("To Date:"), 'to_date');
    

emp_info_leave_type_row(_("Leave Type:"), 'leave_type', null,true);

$f_year = get_current_fiscalyear();
//fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
hidden('f_year',$f_year['id']);
//text_row_ex(_("No Of Leave:"), 'no_of_leave', 30);
textarea_row(_("Reason:"), 'reason', null, 35, 5); 
//text_row_ex(_("Reason:"), 'reason', 30, null, null, $_POST['emp_id'] );
 
//hidden("no_of_leave",$interval->format('%R%a days')); 
//hidden("no_of_leave",floor($datediff/(60*60*24)));
end_table(1);

	if($id==0)
	{
		submit_add_or_update_center(-1, '', 'both');
	}
	else
	{
    start_table(TABLESTYLE2);
    approve_list_row(_("Approval:"),'approve', $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false);
    submit_center_first('update', _("Update"), '', '', true);
	submit_center_last('delete', _("Delete"), '', '', true);	
    end_table(1);
	}




end_form();

end_page();
?>
