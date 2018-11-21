<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Salary Breakup Fromula "));

include($path_to_root . "/payroll/includes/db/salary_breakup_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if (isset($_POST['submit']))
{

	$input_error = 0;

	if ($input_error != 1)
	{
    	if ($account != -1) 
    	{
    		update_salary_breakup_formula_basic_salary($_POST['basic_salary'], $_POST['formula']);
			update_salary_breakup_formula_hr($_POST['hr'], $_POST['formula']);
			update_salary_breakup_formula_ca($_POST['ca'], $_POST['formula']);
			update_salary_breakup_formula_adhoc_relief($_POST['adhoc_relief'], $_POST['formula']);
			update_salary_breakup_formula_medical($_POST['medical'], $_POST['formula']);
			update_salary_breakup_formula_others($_POST['others'], $_POST['formula']);
			update_salary_breakup_formula_eobi($_POST['eobi'], $_POST['formula']);
			update_salary_breakup_formula_income_tax($_POST['income_tax'], $_POST['formula']);
			$note = _('Selected formula has been updated');
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

//gl_all_accounts_list_row(_("Payroll Expense Account:"), 'payroll_expenses', get_sys_pay_pref('payroll_expenses'));

//gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', get_sys_pay_pref('payroll_liabilty'));

//gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', get_sys_pay_pref('advance_receivable'));

//gl_all_accounts_list_row(_("Payment Account:"), 'payment', get_sys_pay_pref('payment'));
start_row();
amount_cells(_("Basic Salary:"), 'basic_salary',get_salary_breakup_formula('basic_salary'));
amount_cells(_("HR:"), 'hr',get_salary_breakup_formula('hr'));
amount_cells(_("CA:"), 'ca',get_salary_breakup_formula('ca'));
amount_cells(_("Adhoc Relief:"), 'adhoc_relief',get_salary_breakup_formula('adhoc_relief'));
end_row();

start_row();
amount_cells(_("Medical:"), 'medical',get_salary_breakup_formula('medical'));
amount_cells(_("Others:"), 'others',get_salary_breakup_formula('others'));
amount_cells(_("EOBI:"), 'eobi',get_salary_breakup_formula('eobi')); 
amount_cells(_("Income Tax:"), 'income_tax',get_salary_breakup_formula('income_tax')); 
end_row();
end_table(1);

submit_center('submit', _("Update"), true, '', 'default');
end_form();

end_page();
?>
