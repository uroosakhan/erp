<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/employee_account.inc");


$page_security = 'SA_PAYROLL_SETUP';

//set_page_security( @$_POST['order_view_mode'],
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE'),
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE')
//);
simple_page_mode(true);

$id=$_GET['employee_id'];
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

    $input_error = 0;

    //if (strlen($_POST['description']) == 0)
    /*{
        $input_error = 1;
        display_error(_("The allowances description cannot be empty."));
        set_focus('description');
    }*/

    if ($input_error != 1)
    {

        if ($selected_id != -1)
        {
            update_employee_accounts($selected_id, $_POST['loan_account'],$_POST['advance_account'],$_POST['salary_account'],$_POST['bonus_account'],$_POST['payroll_expenses']
                ,$_POST['payroll_liabilty'],$_POST['advance_receivable'],$_POST['payment_account'],$_POST['tax_liability'],$_POST['deduction_account']);
            $note = _('Selected employee nomination has been updated');
            //	refresh('employee_nomination.php');
        }
        else
        {

            $result=validate_employee_nomination($_POST['nominee_name']);

            if(!$result) {
                add_employee_nomination($_POST['nominee_name'], $_POST['relation'], $_POST['age']
                    , $_POST['share'], $_POST['remarks'], $_POST['id']);
                $note = _('New employee nomination has been added');
                //	refresh('employee_nomination.php');
            }
            else{
                display_error("Cannot Add Duplicate Nominee Name");

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

    /*if (key_in_foreign_table($selected_id, 'payroll_allowance', 'allow_id'))
    {
        $cancel_delete = 1;
        display_error(_("Cannot delete this department because Employee have been created using this dept."));
    }
    if ($cancel_delete == 0)
    {*/
    delete_employee_nomination($selected_id);
    display_notification(_('Selected employee nomination has been deleted'));
    //refresh('employee_nomination.php');
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

start_form();
$result = get_employee_nomination_all($_GET['employee_id']);
start_table(TABLESTYLE, "width=30%");


table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

    alt_table_row_color($k);

    label_cell($myrow["nominee_name"]);
    label_cell($myrow["relation"]);
    label_cell($myrow["age"]);
    label_cell($myrow["share"]);
    label_cell($myrow["remarks"]);
    //$_POST['month']  = $myrow["month"];

    //label_cell(get_employee_name11($myrow["emp_id"]));
    //inactive_control_cell($myrow["id"], $myrow["inactive"], 'man_month', 'text_field_1');
    edit_button_cell("Edit" . $myrow["id"], _("Edit"));
    delete_button_cell("Delete" . $myrow["id"], _("Delete"));
    end_row();
}
//inactive_control_row($th);
end_table(1);

//echo $emp_id;
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);
$selected_id=$_GET['employee_id'];

if ($selected_id != -1)
{
   // if ($Mode == 'Edit')
    {
        $myrow = get_employee_account($selected_id);

        $_POST['payroll_expenses']  = $myrow["payroll_expenses"];
        $_POST['payroll_liabilty']  = $myrow["payroll_liabilty"];
        $_POST['advance_receivable']  = $myrow["advance_receivable"];
        $_POST['payment_account']  = $myrow["payment_account"];
        $_POST['tax_liability']  = $myrow["tax_liability"];
        $_POST['deduction_account']  = $myrow["deduction_account"];
        $_POST['loan_account']  = $myrow["loan_account"];
        $_POST['advance_receivable']  = $myrow["advance_receivable"];
        $_POST['advance_account']  = $myrow["advance_account"];
        $_POST['tax_liability']  = $myrow["tax_liability"];
        //$_POST['month']  = $myrow["month"];
    }
    hidden("selected_id", $selected_id);

//	label_row(_("text_field_1"), $myrow["text_field_1"]);
//	label_row(_("text_field_2"), $myrow["text_field_2"]);
//	label_row(_("text_field_3"), $myrow["text_field_3"]);
//	label_row(_("text_field_4"), $myrow["text_field_4"]);

}
//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
hidden("id", $id);
    gl_all_accounts_list_row(_("Payroll Expense Account:"), 'payroll_expenses', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Payment Account:"), 'payment_account', null, false, false, _("All Accounts"));
gl_all_accounts_list_row(_("Tax Liability Account:"), 'tax_liability', null, false, false, _("All Accounts"));
gl_all_accounts_list_row(_("EOBI Account:"), 'deduction_account', null, false, false, _("All Accounts"));

gl_all_accounts_list_row(_("Loan:"), 'loan_account', null, false, false, _("All Accounts"));
gl_all_accounts_list_row(_("Advacne:"), 'advance_account', null, false, false, _("All Accounts"));
gl_all_accounts_list_row(_("Salary:"), 'salary_account', null, false, false, _("All Accounts"));
gl_all_accounts_list_row(_("Bonuc:"), 'bonus_account', null, false, false, _("All Accounts"));

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(false, false, false, ST_BANKPAYMENT, $trans_no);
?>
