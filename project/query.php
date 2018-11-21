<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Query"));
$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Query"), false, false, "", $js);
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/sales/includes/db/query_db.inc");
include($path_to_root . "includes/ui/ui_lists.inc");





simple_page_mode(true);

//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['name']) == 0)
	{
		$input_error = 1;
		display_error(_("The Name cannot be empty."));
		set_focus('name');
	}
//	if (strlen($_POST['business_name']) == 0)
//	{
//		$input_error = 1;
//		display_error(_("The Business Name cannot be empty."));
//		set_focus('business_name');
//	}
	if (strlen($_POST['mobile']) == 0)
	{
		$input_error = 1;
		display_error(_("The mobile cannot be empty."));
		set_focus('mobile');
	}
	
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_query($selected_id, $_POST['date'], $_POST['name'], $_POST['business_name'],$_POST['care_of'], $_POST['status'],$_POST['source_status'], $_POST['phone1'],$_POST['phone2'], $_POST['mobile'], $_POST['email'],  $_POST['remarks'], $_POST['user'], $_POST['stock_id']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_query($_POST['date'], $_POST['name'], $_POST['business_name'],$_POST['care_of'], $_POST['status'],$_POST['source_status'], $_POST['phone1'],$_POST['phone2'], $_POST['mobile'], $_POST['email'], $_POST['remarks'], $_POST['user'], $_POST['stock_id']);
    
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected query data have been updated'));
		else
			display_notification(_('New query data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	//if (key_in_foreign_table($selected_id, 'cust_branch', 'salesman'))
	//{
		//display_error(_("Cannot delete this sales-person because branches are set up referring to this sales-person - first alter the branches concerned."));
//	}
	//else
	//{
		delete_query($selected_id);
		display_notification(_('Selected query data have been deleted'));
	//}
	$Mode = 'RESET';
}

//if ($Mode == 'RESET')
//{
	//$selected_id = -1;
	//$sav = get_post('show_inactive');
	//unset($_POST);
	//$_POST['show_inactive'] = $sav;
//}

function handle_update($selected_id)
{
	update_query($selected_id, $_POST['date'], $_POST['name'], $_POST['business_name'],$_POST['care_of'],$_POST['status'],$_POST['source_status'], $_POST['phone1'],$_POST['phone2'], $_POST['mobile'], $_POST['email'],  $_POST['remarks'], $_POST['user']);

	    display_notification(_("query record has been Updated."));
		
}
	if (isset($_POST['update'])) 
{
	
		
		handle_update($_POST['id']);
}

function handle_delete($selected_id)
{
		delete_query($selected_id);

	    display_notification(_("query record has been deleted."));
		
}
	if (isset($_POST['delete'])) 
{
	
		
		handle_delete($_POST['id']);
}
$id=$_GET['id'];
if(isset($_GET['id']))
$selected_id = $id;
$user_name= $_SESSION["wa_current_user"]->user;
	

//------------------------------------------------------------------------------------------------

start_form();
$k = 0;

end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------
//$_POST['salesman_email'] = "";

if ($selected_id != -1) {
 	// 	if ($Mode == 'Edit') {
         

		//editing an existing Sales-person
		$myrow = get_query($_GET['id']);
		
		
		$_POST['date'] = sql2date( $myrow["date"]);
		$_POST['name'] = $myrow["name"];
		$_POST['business_name'] = $myrow["business_name"];
		$_POST['care_of'] = $myrow["care_of"];
		$_POST['status'] = $myrow["status"];
		$_POST['source_status'] = $myrow["source_status"];
        $_POST['phone1'] = $myrow["phone1"];
		$_POST['phone2'] = $myrow["phone2"];
		$_POST['mobile'] = $myrow["mobile"];
		$_POST['email'] = $myrow["email"];
	//	$_POST['package'] = $myrow["package"];
	//	$_POST['package_final'] = $myrow["package_final"];
	//	$_POST['address'] = $myrow["address"];
		$_POST['remarks'] = $myrow["remarks"];
        $_POST['user'] = $myrow["user"];
           $_POST['stock_id'] = $myrow["stock_id"];
//}
	hidden('user', $user_name);
	hidden('selected_id', $selected_id);
	hidden('id', $id);
}
hidden('user', $user_name);
start_outer_table(TABLESTYLE2);

//start_table(TABLESTYLE2);

table_section(1);
table_section_title(_("Basic Data"));

date_row(_("Date"), 'date');
text_row_ex(_("*Name:"), 'name', 30);
text_row_ex(_("*Business Name/Nature:"), 'business_name', 30);
text_row_ex(_("*Mobile:"), 'mobile', 30);

table_section(2);
table_section_title(_("Other Details"));
text_row_ex(_("Care Of:"), 'care_of', 30);
text_row_ex(_("Phone1:"), 'phone1', 30);
text_row_ex(_("Phone2:"), 'phone2', 30);
text_row_ex(_("Email:"), 'email', 30, 100);


source_status_query_list_row(_("Source:"), 'source_status', null, false, null, false, true);


table_section(3);
table_section_title(_("Memo"));
//textarea_row(_("Address:"), 'address', null, 35, 5);
//text_row_ex(_("Remarks:"), 'remarks', 30);
textarea_row(_("Remarks:"), 'remarks', null, 35, 5);




table_section(4);
table_section_title(_("Financials"));

// text_row_ex(_("Followup Date:"), 'package', 30);
//date_cells(_("Followup Date:"), 'package', '', null, 0, 0, -0);
echo "<tr>";
//stock_costable_items_list_cells(_("Item:"), 'stock_id','',null,true);
//stock_costable_items_list_cells(_("Item:"), 'stock_id','',_('Select Variant'));
stock_costable_items_list_row_new(_("Item:"), 'stock_id','',_('Select Variant'));


//text_row_ex(_("Package Finalized:"), 'package_final', 30);
status_query_list_row(_("Status:"), 'status', null, false, null, false, true);

end_outer_table(1);


echo "<center><a href='inquiry/query_inquiry.php?'>"._("Go to Query Inquiry")."</a></center></br>\n";



if($id==0)
	{
		submit_add_or_update_center(-1, '', 'both');
	}
	else
	{
		start_table(TABLESTYLE2);
		div_start(controls);
		submit_center_last('update', _("Update"), '', '', true);
		submit_center_last('delete', _("Delete"), '', '', true);	
			
        div_end();
		
end_table(1);
	}

end_form();

end_page();

?>
