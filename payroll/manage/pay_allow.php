<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Allowances"),true);

include($path_to_root . "/payroll/includes/db/emp_allowance_db.inc");

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 
simple_page_mode(true);

$emp_id=$_GET['emp_id'];
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
    		//update_emp_allowance11($selected_id, $_POST['emp_id'], $_POST['allow_id'], $_POST['amount'],$_POST['dept_id'], $_POST['month']);
			$note = _('Selected allowances has been updated');
			refresh('emp_allowance.php');
    	} 
    	else 
    	{
    		//add_emp_allowance11($_POST['emp_id'], $_POST['allow_id'], $_POST['amount']);
			for($j = 1; $j <= $_POST['count']; $j++)
			{
			add_payroll_allowance_for_pay(77, $_POST['allow_id'.$j], $_POST['amount'.$j],$_POST['emp_id'.$j]);
			}
			$note = _('New allowances has been added');
			refresh('emp_allowance.php');
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
		//delete_emp_allowance11($selected_id);
		display_notification(_('Selected allowances has been deleted'));
		refresh('emp_allowance.php');
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

$result = get_payroll_allowances11($emp_id);

start_form();
start_table(TABLESTYLE, "width=30%");
$th = array( _("ID"),_("Allowances Name"), _("Amount"));
inactive_control_column($th);

table_header($th);
$k = 0; 
$allowance_amount=get_payroll_allowances11_count($emp_id);
$_POST['count']=$allowance_amount;
$a=1;
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["emp_id"]);
	label_cell(get_emp_allowance_name($myrow["allow_id"]));
	label_cell($myrow["amount"]);
	hidden('allow_id'.$a, $myrow["allow_id"]);
	hidden('amount'.$a, $myrow["amount"]);
	hidden('emp_id'.$a, $myrow["emp_id"]);
	hidden('count', $a);
		$a++;
		echo "</tr>";
	end_row();
}

//inactive_control_row($th);
end_table(1);

//echo $emp_id;
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

/*if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		$myrow = get_emp_allowance11($selected_id);

		$_POST['emp_id']  = $myrow["emp_id"];
		$_POST['allow_id']  = $myrow["allow_id"];
		$_POST['amount']  = $myrow["amount"];
		$_POST['dept_id']  = $myrow["dept_id"];
		$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	
	label_row(_("ID"), $myrow["id"]);
} */
//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
//hidden("emp_id", $emp_id);

// emp_dept_row( _("Department:"), 'dept_id', null,  _("Select department: "), 
	//true, check_value('show_inactive'));
	//month_list_cells( _("Month:"), 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
//emp_allowance_row(_("Allowance:"), 'allow_id', null,false);
//text_row(_("Allowances Amount:"), "amount");
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(true, false, false, null, null);
?>
