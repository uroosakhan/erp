<?php


$page_security = 'SA_USERS';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");
page(_($help_context = "User  Dimension"));
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admin/db/users_db.inc");

simple_page_mode(true);

//dimensions_list_cells(_("Dimensions") , 'id');

function get_dimension_id(){
    $sql = "SELECT  *  FROM ".TB_PREF."dimensions ORDER BY id";
    return db_query($sql, "could not get users");
}


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;
	
	$num = check_user_dimension_duplication(get_post('user_id'), get_post('dim_id'));
	
	if (!get_post('user_id'))
	{
		$input_error = 1;
		display_error( _("User has not been selected."));
		set_focus('user_id');
		
	}
	elseif (!get_post('dim_id'))
	{
//		$input_error = 1;
//		display_error( _("Dimension has not been selected."));
//		set_focus('dim_id');
		
	}
	elseif ($num > 0)
	{
		$input_error = 1;
		display_error( _("Selected user has already this location ".get_post('dim_id')));
		set_focus('dim_id');
		
	}
	

	

	if ($input_error != 1)
	{
    	if ($selected_id != -1)
    	{
//            update_users_dimension($selected_id, $_POST['user_id'], $_POST['dim_id']);
//    		display_notification_centered(_("The selected user's dimension has been updated."));
    	}
    	else
    	{
            ///for add multiple dimension
            foreach($_POST['dim_id'] as $dim) {
                add_user_dimension($_POST['user_id'], $dim);
            }
            ///for add multiple users
            foreach($_POST['user_id'] as $user) {
                add_dimension_users($_POST['dim_id'], $user);
            }

            $id = db_insert_id();
			// use current user display preferences as start point for new user
			display_notification_centered(_("A new user's Dimension has been added."));
    	}

		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

    delete_user_dimension($selected_id);
	display_notification_centered(_("User's dimension has been deleted."));
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

//users_list_row_(_("Users ").":", 'user_id', null, null, true, true);
//dimensions_list_cells(_("Dimensions") , 'id' , null ,_("All Dimension") );
//submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
//$id=$_POST['user_id'];
//$dim_id=$_POST['dim_id'];

if($_GET['user_id']!='') {
    if (get_post('user_id'))
        $user_id = get_post('user_id');
    $result = get_dimension_user_wise(check_value('show_inactive'),$_GET['user_id']);
}
elseif($_GET['dim_id']!='') {
    $result = get_users_dimension_wise(check_value('show_inactive'),$_GET['dim_id']);
}

 start_table(TABLESTYLE2, "width=40%");

$th = array(  _("Users"), _("Dimension"));
inactive_control_column($th);
table_header($th);	
$k = 0; //row colour counter



while ($myrow = db_fetch($result)) 
{


	alt_table_row_color($k);
	
//	label_cell($myrow['user_id']);
	label_cell(get_user_name($myrow["user_id"]));
	label_cell(get_dimension_description($myrow["dim_id"]));
    inactive_control_cell($myrow["id"], $myrow["inactive"], 'user_locations', 'id');
	//edit_button_cell("Edit".$myrow["id"], _("Edit"));
// 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	

	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

 start_table(TABLESTYLE2, "width=30%");


if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_user_dim($selected_id);

		$_POST['id'] = $myrow["id"];
		$_POST['user_id'] = $myrow["user_id"];
		$_POST['dim_id'] = $myrow["dim_id"];
		$_POST['status'] = $myrow["inactive"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
}

if($_GET['user_id']!='') {


    dimensionn_tag_list_row(_("Dimension Tags:"), 'dim_id', 5, TAG_ACCOUNT, true);
    hidden("user_id", $_GET['user_id']);
}
elseif($_GET['dim_id']!='') {

    user_tag_list_row(_("Account Tags:"), 'user_id', 5, TAG_ACCOUNT, true);
    hidden("dim_id", $_GET['dim_id']);
}

//label_row(_("You"), $_SESSION["wa_current_user"]->username);
//current_user_locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $current_user)
//current_user_locations_list_row(_("Users ").":", 'asad',  null, false, false, $_SESSION["wa_current_user"]->user);
//user
//bank_accounts_list_all_row( $payment ? _("Bank:") : _("Bank:"), 'loc_code', null, true);
//dimensions_list_cells(_("Dimensions") , 'id');


//current_user_locations_list_row(_("Location :"), 'loc_code');

//check_row(_("Status :"), 'status', $_POST['status'], false, _('Set the status for user \'s location.'));

end_table(1);
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
