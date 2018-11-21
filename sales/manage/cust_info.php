<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Query"));
$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();
page(_($help_context = "Distributor Profile"), false, false, "", $js);
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/sales/includes/db/cust_info_db.inc");
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
	if (strlen($_POST['phone2']) == 0)
	{
		$input_error = 1;
		display_error(_("The mobile cannot be empty."));
		set_focus('phone2');
	}
	
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_cust_info($selected_id, $_POST['name'], $_POST['title'], $_POST['contact_person'],
                $_POST['credit_days'], $_POST['phone'],$_POST['phone2'], $_POST['credit_limit'],
                $_POST['email'], $_POST['address'], $_POST['address2'], $_POST['city'],
                $_POST['state'], $_POST['country'], $_POST['str_no'], $_POST['payment_terms'],
                $_POST['dimension_id']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_cust_info($_POST['name'], $_POST['title'], $_POST['contact_person'],
				$_POST['credit_days'], $_POST['phone'],$_POST['phone2'], $_POST['credit_limit'],
				$_POST['email'], $_POST['address'], $_POST['address2'], $_POST['city'],
				$_POST['state'], $_POST['country'], $_POST['str_no'], $_POST['payment_terms'],
                $_POST['dimension_id']);
    
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected customer info data have been updated'));
		else
			display_notification(_('New customer info data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
		delete_cust_info($selected_id);
		display_notification(_('Selected customer info data have been deleted'));
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
	update_cust_info($selected_id, $_POST['name'], $_POST['title'], $_POST['contact_person'],
        $_POST['credit_days'], $_POST['phone'],$_POST['phone2'], $_POST['credit_limit'],
        $_POST['email'], $_POST['address'], $_POST['address2'], $_POST['city'], $_POST['state'],
		$_POST['country'], $_POST['str_no'], $_POST['payment_terms'], $_POST['dimension_id']);

	    display_notification(_("customer info record has been Updated."));
		
}
	if (isset($_POST['update'])) 
{
		handle_update($_POST['id']);
}

function handle_delete($selected_id)
{
		delete_query($selected_id);

	    display_notification(_("customer info record has been deleted."));
		
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
		$myrow = get_cust_infor($_GET['id']);

		$_POST['id'] = $myrow["id"];
		$_POST['name'] = $myrow["name"];
		$_POST['title'] = $myrow["title"];
		$_POST['contact_person'] = $myrow["contact_person"];
		$_POST['credit_days'] = $myrow["credit_days"];
		$_POST['phone'] = $myrow["phone"];
		$_POST['phone2'] = $myrow["phone2"];
		$_POST['credit_limit'] = $myrow["credit_limit"];
		$_POST['email'] = $myrow["email"];
		$_POST['address'] = $myrow["address"];
		$_POST['address2'] = $myrow["address2"];
		$_POST['city'] = $myrow["city"];
		$_POST['state'] = $myrow["state"];
        $_POST['country'] = $myrow["country"];
        $_POST['str_no'] = $myrow["str_no"];
        $_POST['payment_terms'] = $myrow["payment_terms"];
        $_POST['dimension_id'] = $myrow["dimension_id"];
//}
//	hidden('user', $user_name);
	hidden('selected_id', $selected_id);
	hidden('id', $id);
}
//hidden('user', $user_name);
start_outer_table(TABLESTYLE2);

//start_table(TABLESTYLE2);

table_section(1);
table_section_title(_("Distributor Profile"));

text_row_ex(_("ID"), 'id', 10);
text_row_ex(_("Name"), 'name', 30);
text_row_ex(_("Title"), 'title', 30);
text_row_ex(_("Credit Days"), 'credit_days', 30);
text_row_ex(_("Phone No"), 'phone', 30);
text_row_ex(_("Mobile No"), 'phone2', 30);
text_row_ex(_("Office Address"), 'address', 30);
text_row_ex(_("Factory Address"), 'address2', 30);
text_row_ex(_("Country"), 'country', 30);
text_row_ex(_("Sales Tax Reg.No"), 'str_no', 30);
text_row_ex(_("Payment Terms"), 'payment_terms', 30);
dimensions_list_cells(_('Cost Center'), 'dimension_id', null, true, null, true);

table_section(2);
table_section_title(_("Distributor Profile"));
text_row_ex(_("Contact Person"), 'contact_person', 30);
text_row_ex(_("Credit Limit"), 'credit_limit', 30);
text_row_ex(_("Email:"), 'email', 30, 100);
text_row_ex(_("City"), 'city', 30);
text_row_ex(_("State"), 'state', 30);

end_outer_table(1);


//echo "<center><a href='inquiry/query_inquiry.php?'>"._("Go to Query Inquiry")."</a></center></br>\n";



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
