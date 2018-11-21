<?php


$page_security = 'SA_USERS';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
page(_($help_context = "User Accounts"));
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admin/db/users_db.inc");

simple_page_mode(true);


function get_account_id(){
    $sql = "SELECT  *  FROM ".TB_PREF."chart_master ORDER BY account_code";
    return db_query($sql, "could not get users");
}



//-------------------------------------------------------------------------------------------------
if(isset($_POST['SearchOrders']))
{
	global $Ajax;
	$Ajax->activate('refresh');
}
start_form();
//users_list_row_(_("Users ").":", 'user_id', null, null, true, true);
//gl_all_accounts_list_cells(_("Account Code:") , 'account_code', null , false,false , _("All Account") );

//$id=$_POST['user_id'];
//$account_code=$_POST['account_code'];
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{


    $input_error = 0;

    $num = check_user_account_duplication(get_post('user_id'), get_post('account_code'));

    if (!get_post('user_id'))
    {
//		$input_error = 1;
//		display_error( _("User has not been selected."));
//		set_focus('user_id');

    }
	elseif (!get_post('account_code'))
    {
        $input_error = 1;
        display_error( _("Account has not been selected."));
        set_focus('account_code');

    }
	elseif ($num > 0)
    {
        $input_error = 1;
        display_error( _("Selected user has already this location ".get_post('account_code')));
        set_focus('account_code');

    }




    if ($input_error != 1)
    {
        if ($selected_id != -1)
        {
//            update_users_locations($selected_id,  $_POST['user_id'], $_POST['account_code']);
//            display_notification_centered(_("The selected user's location has been updated."));
        }
        else
        {
            ///for add multiple locations
            foreach($_POST['account_code'] as $accountcode) {
                add_user_account($_POST['user_id'], $accountcode);

            }
            ///for add multiple users
            foreach($_POST['user_id'] as $user) {
                add_accounts_users($_POST['account_code'], $user);
            }



            $id = db_insert_id();
            // use current user display preferences as start point for new user
            display_notification_centered(_("A new user's location has been added."));
        }

        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

    delete_user_accounts($selected_id);
    display_notification_centered(_("User's location has been deleted."));
    $Mode = 'RESET';

}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);	// clean all input fields
//	$_POST['show_inactive'] = $sav;
}
//-------------------------------------------------------------------------------------------------


start_form();
if($_GET['user_id']!='') {
    if (get_post('user_id'))
        $user_id = get_post('user_id');
    $result = get_account_user_wise(check_value('show_inactive'), $_GET['user_id']);
}
elseif($_GET['account_code']!='') {
    $result = get_users_accounts_wise(check_value('show_inactive'),$_GET['account_code']);
}
start_table(TABLESTYLE2, "width=100%");



$th = array( _("User Id"), _("User"), _("Account Name"),);
inactive_control_column($th);
table_header($th);	
$k = 0; //row colour counter



while ($myrow = db_fetch($result)) 
{
//    display_error($account_code);
	alt_table_row_color($k);
	
	//label_cell($myrow['id']);
    label_cell($myrow['user_id']);
	label_cell(get_user_name($myrow["user_id"]));
	label_cell(get_account_description($myrow["account_code"]));

    inactive_control_cell($myrow["id"], $myrow["inactive"], 'user_locations', 'id');
	//edit_button_cell("Edit".$myrow["id"], _("Edit"));
// 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	
	
	end_row();
}

//inactive_control_row($th);
//end_table(1);
div_end();


label_row();
label_row();

if($_GET['user_id']!='') {


    account_tag_list_row(_("Account Tags:"), 'account_code', 5, TAG_ACCOUNT, true);
    hidden("user_id", $_GET['user_id']);
}
elseif($_GET['account_code']!='') {

    user_tag_list_row(_("User  Tags:"), 'user_id', 5, TAG_ACCOUNT, true);
    hidden("account_code", $_GET['account_code']);
}
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');
end_form();
end_page();
?>
