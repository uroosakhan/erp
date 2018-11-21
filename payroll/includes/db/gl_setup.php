<?php
$page_security = 'SA_SALESGROUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Payroll GL Setup"));

include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if (isset($_POST['submit']))
{

	$input_error = 0;

	if ($input_error != 1)
	{
    	if ($account != -1) 
    	{
    		update_sys_pay_ex($_POST['payroll_expenses'], $_POST['account']);
			update_sys_pay_ad($_POST['advance_receivable'], $_POST['account']);
			update_sys_pay_pa($_POST['payment'], $_POST['account']);
			update_sys_pay_li($_POST['payroll_liabilty'], $_POST['account']);
			$note = _('Selected gl accounts has been updated');
    	} 
    	else 
    	{
    		//add_emp_dept($_POST['description']);
			//$note = _('New sales group has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}

start_form();
  
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

gl_all_accounts_list_row(_("Payroll Expense Account:"), 'payroll_expenses', get_sys_pay_pref('payroll_expenses'));

gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', get_sys_pay_pref('payroll_liabilty'));

gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', get_sys_pay_pref('advance_receivable'));

gl_all_accounts_list_row(_("Payment Account:"), 'payment', get_sys_pay_pref('payment'));


end_table(1);

submit_center('submit', _("Update"), true, '', 'default');
end_form();

end_page();
?>
