<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Sales Offers"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

//--------------------marina setup forms for offers------------------//
function add_sales_offers($code, $account_code, $cost_center_code, $customer_id)
{
    $sql = "INSERT INTO ".TB_PREF."sales_offers (code, account_code, cost_center_code, customer_id) VALUES (".db_escape($code) .",
    ".db_escape($account_code). ", ".db_escape($cost_center_code). ", ".db_escape($customer_id). ")";
    db_query($sql,"The offers could not be added");
}

function update_sales_offers($selected_id, $code, $account_code, $cost_center_code, $customer_id)
{
    $sql = "UPDATE ".TB_PREF."sales_offers 
            SET code=".db_escape($code).",
            account_code=".db_escape($account_code).", 
            cost_center_code=".db_escape($cost_center_code)."
            customer_id=".db_escape($customer_id)."
            WHERE id = ".db_escape($selected_id);
    db_query($sql,"The offers could not be updated");
}

function delete_sales_offers($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."sales_offers WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete offers");
}


function get_sales_offers($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."sales_offers";
    if (!$show_inactive) $sql .= " WHERE !inactive";

    return db_query($sql,"could not get offers");
}

function get_sales_offers1($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."sales_offers WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get offer");
    return db_fetch($result);
}
function get_bank_account_name_sales_offers($id)
{
    $sql = "SELECT CONCAT(account_code ,'  ', account_name) as AccountName FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($id);

    $result = db_query($sql, "could not retreive bank account");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_dimension_name_sales_offers($dimension_id)
{
    $sql = "SELECT CONCAT(reference, ' ',name) as DimensionName FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	$input_error = 0;
	if (strlen($_POST['code']) == 0)
	{
		$input_error = 1;
		display_error(_("The code cannot be empty."));
		set_focus('code');
	}

	if (!$_POST['account_code'])
	{
		$input_error = 1;
		display_error(_("The account should be select."));
		set_focus('account_code');
	}

// 	if (!$_POST['cost_center_code'])
// 	{
// 		$input_error = 1;
// 		display_error(_("The cost center should be select."));
// 		set_focus('cost_center_code');
// 	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) {
            update_sales_offers($selected_id, $_POST['code'], $_POST['account_code'], $_POST['cost_center_code'], $_POST['customer_id']);
			$note = _('Selected offers has been updated');
    	} else {
            add_sales_offers($_POST['code'], $_POST['account_code'], $_POST['cost_center_code'], $_POST['customer_id']);
			$note = _('New offers has been added');
    	}
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
    $cancel_delete = 0;
    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
//    if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
//    {
//        $cancel_delete = 1;
//        display_error(_("Cannot delete this area because customer branches have been created using this area."));
//    }
    if ($cancel_delete == 0)
    {
        delete_sales_offers($selected_id);

        display_notification(_('Selected offer has been deleted'));
    } //end if Delete area
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
//    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

//-------------------------------------------------------------------------------------------------

$result = get_sales_offers(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Code"), _("Account Code"), _("Cost Center Code"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	alt_table_row_color($k);
    label_cell($myrow["code"]);
    label_cell(get_bank_account_name_sales_offers($myrow["account_code"]));
	label_cell(get_dimension_name_sales_offers($myrow["cost_center_code"]));
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'sales_offers', 'id');
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
		$myrow = get_sales_offers1($selected_id);

		$_POST['code']  = $myrow["code"];
		$_POST['account_code']  = $myrow["account_code"];
		$_POST['cost_center_code']  = $myrow["cost_center_code"];
	}
	hidden("selected_id", $selected_id);
}
hidden('customer_id', $_GET['customer_id']);

text_row("Code:",'code', null, 20, 150);
gl_all_accounts_list_cells(_("Account:"), 'account_code', null, false, false, _("All Accounts"));
dimensions_list_row("Cost Center Code:", 'cost_center_code', null, true, " ", false, 1);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();