<?php
$page_security = 'SA_SALESAREA';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/cashflow_db.inc");

page(_($help_context = "Cashflow Type"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

    $input_error = 0;

    if (strlen($_POST['name']) == 0)
    {
        $input_error = 1;
        display_error(_("The cashflow type cannot be empty."));
        set_focus('name');
    }

    if ($input_error != 1)
    {
        if ($selected_id != -1)
        {
            update_cashflow_type($selected_id, $_POST['name']);
            $note = _('Selected cashflow type has been updated');
        }
        else
        {
            add_cashflow_type($_POST['name']);
            $note = _('New cashflow type has been added');
        }

        display_notification($note);
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//    if (key_in_foreign_table($selected_id, 'cust_branch', 'cashflow'))
//    {
//        $cancel_delete = 1;
//        display_error(_("Cannot delete this type because customer branches have been created using this type."));
//    }
    if ($cancel_delete == 0)
    {
        delete_cashflow_type($selected_id);

        display_notification(_('Selected cashflow type has been deleted'));
    } //end if Delete area
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

//-------------------------------------------------------------------------------------------------

$result = get_cashflow_typee(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Name"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

    alt_table_row_color($k);

    label_cell($myrow["name"]);

    inactive_control_cell($myrow["id"], $myrow["inactive"], 'cashflow', 'id');

    edit_button_cell("Edit".$myrow["id"], _("Edit"));
    delete_button_cell("Delete".$myrow["id"], _("Delete"));
    end_row();
}

inactive_control_row($th);
end_table();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
    if ($Mode == 'Edit') {
        //editing an existing area
        $myrow = get_cashflow_type($selected_id);

        $_POST['name']  = $myrow["name"];
    }
    hidden("selected_id", $selected_id);
}

text_row_ex(_("Cashflow Type:"), 'name', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

