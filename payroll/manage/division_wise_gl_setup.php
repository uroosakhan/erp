<?php
$page_security = 'SA_SUPPLIER';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Location"));

include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/payroll/includes/db/division_wise_gl_db.inc");

include($path_to_root . "includes/ui/ui_lists.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

    $input_error = 0;

    if ($input_error != 1)
    {
        if ($selected_id != -1)
        {

            update_division_wise_gl($selected_id, $_POST['payroll_expenses'],$_POST['advance_receivable'],
                $_POST['payment'],$_POST['payroll_liabilty']
                ,$_POST['tax_liability'],$_POST['eobi_liability'],$_POST['loan_account'],$_POST['advance_account'],
                $_POST['salary_account'],$_POST['bonus_account'],$_POST['deduction_account'],$_POST['division'],
                $_POST['project'], $_POST['location'], $_POST['inactive']);
            $note = _('Selected Account has been updated');
        }
        else
        {

            add_division_wise_gl($_POST['payroll_expenses'],$_POST['advance_receivable'],$_POST['payment'],
                $_POST['payroll_liabilty']
            ,$_POST['tax_liability'],$_POST['eobi_liability'],$_POST['loan_account'],$_POST['advance_account'],
                $_POST['salary_account'],$_POST['bonus_account'],$_POST['deduction_account'],$_POST['division'],
                $_POST['project'], $_POST['location'], $_POST['inactive']);
            $note = _('New Account has been added');
        }

        display_notification($note);
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    if ($cancel_delete == 0)
    {
        delete_division_wise_gl($selected_id);
        display_notification(_('Selected Account has been deleted'));
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

function get_payroll_dimension($id)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_payroll_gl_setup($id)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($id);
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
//-------------------------------------------------------------------------------------------------

$result = get_division_wise_gl2(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(_("Id"),_("Division"),_("Project"),_("Location"),_("Salary Account"),_("Emp Expense"),
    _("Over Time"),_("Arrer Account"),_("Tax Liability Account"),_("C.Deduction Account"),_("M.Deduction Account"),_("A.Deduction Account"),
    _("Payroll Liability Account"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

    alt_table_row_color($k);

    label_cell($myrow["id"]);
    label_cell(get_dim_name($myrow["division"]));
    label_cell(get_dim_name($myrow["project"]));
    label_cell(get_dim_name($myrow["location"]));
label_cell(get_payroll_gl_setup($myrow["salary_account"]));
label_cell(get_payroll_gl_setup($myrow["bonus_account"]));
    label_cell(get_payroll_gl_setup($myrow["payroll_expenses"]));
label_cell(get_payroll_gl_setup($myrow["loan_account"]));
label_cell(get_payroll_gl_setup($myrow["tax_liability"]));
label_cell(get_payroll_gl_setup($myrow["eobi_liability"]));
label_cell(get_payroll_gl_setup($myrow["deduction_account"]));
label_cell(get_payroll_gl_setup($myrow["advance_receivable"]));
    label_cell(get_payroll_gl_setup($myrow["payroll_liabilty"]));
    //label_cell(get_payroll_gl_setup($myrow["payment"]));
    //label_cell(get_payroll_gl_setup($myrow["advance_account"]));
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'payroll_gl_setup', 'id');
    edit_button_cell("Edit".$myrow["id"], _("Edit"));
    delete_button_cell("Delete".$myrow["id"], _("Delete"));
    end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) {
    if ($Mode == 'Edit') {
        //editing an existing group
        $myrow = get_division_wise_gl($selected_id);

        $_POST['division']  = ($myrow["division"]);
        $_POST['project']  = $myrow["project"];
        $_POST['location']  = $myrow["location"];
        $_POST['payroll_expenses']  = $myrow["payroll_expenses"];
        $_POST['payroll_liabilty']  = $myrow["payroll_liabilty"];
        $_POST['advance_receivable']  = $myrow["advance_receivable"];
        $_POST['payment']  = $myrow["payment"];
        $_POST['tax_liability']  = $myrow["tax_liability"];
        $_POST['eobi_liability']  = $myrow["eobi_liability"];
        $_POST['loan_account']  = $myrow["loan_account"];
        $_POST['advance_account']  = $myrow["advance_account"];
        $_POST['salary_account']  = $myrow["salary_account"];
        $_POST['bonus_account']  = $myrow["bonus_account"];
        $_POST['deduction_account']  = $myrow["deduction_account"];
    }
    hidden("selected_id", $selected_id);
    label_row(_("ID"), $myrow["id"]);
}
    dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1, true);
    pro_list_row(_("Project"), 'project', $_POST['project'], 'All Projects', "", false, 2, true, $_POST['division']);
    loc_list_row(_("Location"), 'location', null, 'All Locations', "", false, 3, true, $_POST['project']);

    gl_all_accounts_list_row(_("Salary Account:"), 'salary_account', $_POST['salary_account']);
    gl_all_accounts_list_row(_("Emp Allowance:"), 'bonus_account', $_POST['bonus_account']);
    gl_all_accounts_list_row(_("Over Time:"), 'payroll_expenses', $_POST['payroll_expenses']);
    gl_all_accounts_list_row(_("Arrer Account:"), 'loan_account', $_POST['loan_account']);

gl_all_accounts_list_row(_("Tax Liability Account:"), 'tax_liability', $_POST['tax_liability']);
gl_all_accounts_list_row(_("Deduction Account:"), 'eobi_liability', $_POST['eobi_liability']);
gl_all_accounts_list_row(_("Late/Absent Deduction Account:"), 'deduction_account', $_POST['deduction_account']);
gl_all_accounts_list_row(_("A.Deduction Account:"), 'advance_receivable', $_POST['advance_receivable']);
gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', $_POST['payroll_liabilty']);
   
   // gl_all_accounts_list_row(_("Payment Account:"), 'payment', $_POST['payment']);
   // gl_all_accounts_list_row(_("Advacne:"), 'advance_account', $_POST['advance_account']);
    
    
    

//}
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
