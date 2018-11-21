<?php
$page_security = 'SS_CRM_TASK';
$path_to_root = "..";
include_once($path_to_root . "/includes/session.inc");


include_once($path_to_root . "/includes/db_pager.inc");
//include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


//page(_($help_context = "Task"));
$js = "";
if ($use_date_picker)
	$js .= get_js_date_picker();

if($_GET['type'] == "task" || $_GET['type'] == "cloning")
page(_($help_context = "Task"), false, false, "", $js);
if($_GET['type'] == "call")
page(_($help_context = "Call"), false, false, "", $js);
if($_GET['type'] == "event")
page(_($help_context = "Event"), false, false, "", $js);

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/project/includes/db/task_db.inc");
include_once($path_to_root . "/sales/includes/db/query_db.inc");


echo '<style>
.city {
   float: left;
   margin: 10px;
   padding: 10px;
   max-width: 300px;
   height: 300px;
   border: 1px solid black;
}   
</style>';
simple_page_mode(true);

//------------------------------------------------------------------------------------------------
//dz copied from customer_inquiry to get the last customer name selected on customer drop down
if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();


//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {
	$_POST['task_type'] = get_post('task_type');
	$_POST['call_type'] = get_post('call_type');
	//initialise no input errors assumed initially before we test
	$input_error = 0;
	//if (($_GET['type'] == "call"))
	{
		if (strlen($_POST['start_date']) == 0) {
			$input_error = 1;
			display_error(_("The start date cannot be empty."));
			set_focus('start_date');
		}

	if (strlen($_POST['end_date']) == 0 && $_GET['type'] == 'task') {
		$input_error = 1;
		display_error(_("The end date cannot be empty."));
		set_focus('end_date');
	}
	//$count1 = get_des_count($id);

	//if ($count1 == 0)

		if (strlen($_POST['description']) == 0 && $_POST['task_type'] == 1)
	 {
			$input_error = 1;
			display_error(_("The task description cannot be empty."));
			set_focus('description');
		}

		if (strlen($_POST['description']) == 0 && $_POST['task_type'] == 2)
	 {
			$input_error = 1;
			display_error(_("The company name cannot be empty."));
			set_focus('description');
		}

		if (strlen($_POST['description']) == 0 && $_POST['task_type'] == 3)
	 {
			$input_error = 1;
			display_error(_("The event description cannot be empty."));
			set_focus('description');
		}

	if ($_POST['customer_id'] == 0  && strlen($_POST['customer_']) == 0 && ($_POST['task_type'] == 1 || $_POST['task_type'] == 3 )) {
		$input_error = 1;
		display_error(_("The customer name cannot be empty."));
		set_focus('customer_id');
	}

	if (strlen($_POST['other_cust']) == 0 && $_POST['task_type'] == 2 ) {
		$input_error = 1;
		display_error(_("The call customer name cannot be empty."));
		set_focus('other_cust');
	}
	if (strlen($_POST['contact_no']) == 0 && $_POST['task_type'] == 2) {
		$input_error = 1;
		display_error(_("The contact number cannot be empty."));
		set_focus('contact_no');
	}
	if (strlen($_POST['user_id']) == 0 && $_POST['task_type'] != 1 ) {
		$input_error = 1;
		display_error(_("The assigned to cannot be empty."));
		set_focus('user_id');
	}


	//$count4 = get_status2($id);
	//if ($count4 == 0) {
		if (!get_post('status') && ($_POST['task_type']) == 1) {
			$input_error = 1;
			display_error(_("The status cannot be empty."));
			set_focus('status');
		}
	//}
	//$count2 = get_plan2($id);
	//if ($count2 == 0) {
		if (strlen($_POST['plan']) == 0 && ($_POST['task_type']) == 1) {
			$input_error = 1;
			display_error(_("The plan cannot be empty."));
			set_focus('plan');
		}
	}
//}
	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
						$task = update_task($selected_id, $_POST['start_date'], $_POST['end_date'],$_POST['description'],$_POST['customer_id'],$_POST['task_type'],$_POST['call_type'], $_POST['contact_no'],$_POST['other_cust'], $_POST['status'], $_POST['user_id'],$_POST['plan'],$_POST['plan1'],$_POST['actual'],$_POST['actual1'],$_POST['remarks'], $_POST['time'],$_POST['priority'],$_POST['progress'],$_POST['customer_'],$_POST['amount']);

						add_task_history($selected_id,$_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'],$_POST['assign_by'], $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'],$_POST['customer_'],$_POST['priority'],$_POST['progress'], $_SESSION['wa_current_user']->user, 0, 1,$_POST['amount']);

			//update_history($selected_id, $_POST['start_date'], $_POST['end_date'], $_POST['description'],$_POST['debtor_no'],$_POST['task_type'],$_POST['call_type'], $_POST['status'], $_POST['user_id'],$_POST['plan'],$_POST['actual'], $_POST['remarks']);

		}
		else
		{
			
								add_task1($_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['branch_id'], $_POST['task_type'],$_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'], $_POST['status'], $_POST['user_id'],  $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'],$_POST['priority'],$_POST['progress'],$_POST['customer_'],$_POST['amount']);

								add_task_history(get_task_max_id(), $_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['branch_id'], $_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'], $_POST['assign_by'], $_POST['plan'],$_POST['plan1'], 0,0,  $_POST['remarks'], $_POST['time'],$_POST['customer_'],$_POST['priority'],$_POST['progress'], $_SESSION['wa_current_user']->user, 0, 1,$_POST['amount']);

if($_POST['call_type'] == 1)
			add_query($_POST['start_date'], $_POST['other_cust'], $_POST['description'],$_POST['care_of'], 1,9,$_POST['phone1'] ,$_POST['phone2'],$_POST['contact_no'], $_POST['email'],$_POST['package'],$_POST['package_final'],$_POST['address'], $_POST['remarks'], $_POST['assign_by']);

			
		}
			display_notification(_('New task data has been added'));
			//delete_task_multiple($selected_id);



		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	delete_task($selected_id);

	display_notification(_('Selected task data have been deleted'));
	$Mode = 'RESET';
}
//		text_cells(null, 'Line'.$line_no.'Desc', $ln_itm->item_description, 30, 50);

//if ($Mode == 'RESET')
//{
//$selected_id = -1;
//$sav = get_post('show_inactive');
////unset($_POST);
//$_POST['show_inactive'] = $sav;
//}
function handle_update($selected_id)
{
				update_task($selected_id, $_POST['start_date'], $_POST['end_date'], $_POST['description'],$_POST['customer_id'],$_POST['task_type'],$_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'], $_POST['status'],  $_POST['user_id'], $_POST['assign_by'], $_POST['plan'],$_POST['plan1'],$_POST['actual'],$_POST['actual1'], $_POST['remarks'], $_POST['time'],$_POST['priority'],$_POST['progress'],$_POST['customer_'],$_POST['amount']);


				add_task_history($selected_id,$_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'],$_POST['assign_by'], $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'],$_POST['customer_'],$_POST['priority'],$_POST['progress'], $_SESSION['wa_current_user']->user, 0, 1,$_POST['amount']);


	display_notification(_("Task record has been Updated."));

}
if (isset($_POST['update']))
{

	handle_update($_POST['id']);
}

function handle_delete($selected_id)
{
	delete_task($selected_id);


		add_task_history($selected_id,$_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'],$_POST['assign_by'], $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'],$_POST['customer_'],$_POST['priority'],$_POST['progress'], $_SESSION['wa_current_user']->user, 0, 1,$_POST['amount']);



	display_notification(_("Task record has been deleted."));

}
if (isset($_POST['delete']))
{


	handle_delete($_POST['id']);
}
$id=$_GET['id'];

//------------------------------------------------------------------------------------------------

start_form();
$k = 0;
end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------


//editing an existing Sales-person
if($_GET['id'])
{
	$myrow = get_task($_GET['id']);

	$_POST['start_date'] = sql2date($myrow["start_date"]);
	$_POST['end_date'] = sql2date($myrow["end_date"]);
	$_POST['description'] = $myrow["description"];
	$_POST['customer_id'] = $myrow["debtor_no"];
	$_POST['task_type'] = $myrow["task_type"];
	$_POST['call_type'] = $myrow["call_type"];
	$_POST['contact_no'] = $myrow["contact_no"];
	$_POST['other_cust'] = $myrow["other_cust"];
	$_POST['status'] = $myrow["status"];
	$_POST['user_id'] = $myrow["user_id"];
	$_POST['assign_by'] = $myrow["assign_by"];
	$_POST['plan'] = $myrow["plan"];
	$_POST['plan1'] = $myrow["plan1"];
	$_POST['actual'] = $myrow["actual"];
	$_POST['actual1'] = $myrow["actual1"];
	$_POST['remarks'] = $myrow["remarks"];
	$_POST['time'] = $myrow["time"];
	$_POST['priority'] = $myrow["priority"];
	$_POST['progress'] = $myrow["progress"];
	$_POST['customer_'] = $myrow["customer_"];

    $_POST['branch_id'] = $myrow["branch_id"];

    $_POST['amount'] = $myrow["amount"];

	hidden('selected_id', $selected_id);
	hidden('id', $id);
}
start_outer_table_task(TABLESTYLE_NOBORDER);

start_row();
customer_list_cells(_("Customer:"), 'customer_id', null, false, true, false, true);

if ($order->customer_id != get_post('customer_id', -1))
{
    $Ajax->activate('branch_id');
}

customer_branches_list_cells(_("Branch:"),
    $_POST['customer_id'], 'branch_id', null, false, true, true, true);

end_outer_table();
end_row();
if($_GET['type'] == "call")
{
	start_outer_table(TABLESTYLE2);
	table_section(1);
	table_section_title(_("Call Details"));
	date_row(_("Call date:"), 'start_date');
	//task_type2_list_row(_("Task Type:"), 'task_type', null, true, false);
	hidden(task_type, 2);

	call_type_list_row(_("Call Type: "), 'call_type', null, false, true);
	$_POST['call_type'] = get_post('call_type');

	//if(get_post('call_type') == 4)
		//customer_list_row(_("*Customer: "), 'customer_id', null, _("Select"), true, false, true);
		text_row(_("*Company Name:"), 'description', null, 20, 50);
		text_row(_("*Person Name:"), 'other_cust', null, 20, 50);
		//$_POST['debtor_no']=get_post('customer_id');
		text_row(_("*Contact No:"), 'contact_no', null, 20, 50);
		textarea_row(_("Remarks:"), 'remarks', null, 25.3, 8);

			//hidden('customer_id', 503);
$_POST['debtor_no']=$_POST['customer_id'];

	users_query_list_row(_("*Forwarded To: "), 'user_id', null, _("Select"));
	label_cells(_("*Assign By: "), get_user_realname($_SESSION['wa_current_user']->user));
	hidden('assign_by', $_SESSION['wa_current_user']->user);

		end_row();

	end_outer_table(1);

}

if($_GET['type'] == "event")
{
	start_outer_table_task(TABLESTYLE_NOBORDER);
start_row();
// 	customer_list_cells(_("*Customer: "), 'customer_id', null, _("Select Customer"), false, false, true);
text_row(_("Customer:"), 'customer_', null, 20, 30);
end_row();
	end_table();

	start_outer_table_task(TABLESTYLE2);

	table_section(1);
	table_section_title(_("Event Details"));

	if($_GET['date'])
	$_POST['start_date']= $_GET['date'];

	date_row(_("Date:"), 'start_date');
   text_cells_new(_("*Time: "),'time', null);
	//task_type2_list_row(_("Task Type:"), 'task_type', null, true, false);
	hidden(task_type, 3);

		//text_row(_("*Customer Name:"), 'other_cust', null, 20, 50);
		$_POST['debtor_no']=get_post('customer_id');
		textarea_row(_("Description: *"), 'description', null, 25.3, 8);

		end_row();

	table_section(2);
	table_section_title(_("Remarks"));
		textarea_row(_("Remarks:"), 'remarks', null, 25.3, 8);

	users_query_list_row(_("*Task Owner: "), 'user_id', null, _("Select"));
	label_row(_("*Assign By: "), get_user_realname($_SESSION['wa_current_user']->user));
	hidden('assign_by', $_SESSION['wa_current_user']->user);
	amount_cells("Amount", 'amount', null, 15, null, 2);

	end_outer_table(1);

}


if ($_GET['type'] == "task" || $_GET['type'] == "cloning")
{
	global $Ajax;
	start_outer_table_task(TABLESTYLE_NOBORDER);

	start_row();

//		if (!@$_GET['popup'])
//			customer_list_cells(_("Customer: "), 'customer_id', null, _("Select a Customer"), true, false, !@$_GET['popup']);

//
//	customer_list_cells(_("Select a customer: "), 'customer_id_data', null, true, true);
//display_error($_POST['customer_id_data']);
//	customer_branches_list_cells(_("Branch:"),
//		$_POST['customer_id_data'], 'branch_id', null, true, true);


		date_cells(_("Date:"), 'start_date');
		date_cells(_("End date:"), 'end_date');


	end_row();
	end_table();
		start_outer_table_task(TABLESTYLE_NOBORDER);
		start_row();
		duration_list_cells(_("Planned Hours: "), 'plan', null, _("Select"));
		duration1_list_cells(_("Minutes"), 'plan1', null, _("Select"));
	if($id !=0 && $_GET['type'] == "task")
	    progress_list_cells(_("Progress: "), 'progress','', _("Select"));
		end_row();

		start_row();
		if ($id != 0)
				{duration_list_cells(_("Actual Hours: "), 'actual', null, _("Select"));
			duration1_list_cells(_("Minutes"), 'actual1', null, _("Select"));}


		end_row();
		END_TABLE();

	start_outer_table_task(TABLESTYLE);

	table_section(1);
	table_section_title(_("Description"));

	//task_type_list_row(_("*Task Type:"), 'task_type', null, true, true);
	hidden('task_type', 1);
	}
	if ($_GET['type'] == "task" || $_GET['type'] == "cloning")

	{
		//customer_list_row(_("*Customer: "), 'customer_id', null, _("Select"), true, false, true);
//	$_POST['debtor_no']=get_post('customer_id');

		textarea_row(_("Task Description: *"), 'description', null, 35, 12);

		end_row();
		start_row();


		table_section(2);
		table_section_title(_("Remarks"));
		textarea_row(_("Remarks:"), 'remarks', null, 35, 12);


		table_section(3);
		table_section_title(_("Status"));

		//date_row(_("Start date:"), 'start_date');

		//date_row(_("End date:"), 'end_date');


		if($id)
		{
		pstatus_list_row(_("*Status: "), 'status', '', _("Select"));
                priority_list_row(_("Set Priority: "), 'priority', '');
		}
		else
		{
		pstatus_list_row(_("*Status: "), 'status', 5, _("Select"));

                priority_list_row(_("Set Priority: "), 'priority', 2);

		}


		if ($_SESSION['wa_current_user']->access == 2 ||  $_SESSION["wa_current_user"]->access == 13 )
		{
			users_query_list_row(_("*Assign To: "), 'user_id', null, _("Select"));
			label_row(_("*Task Owner: "), get_user_realname($_SESSION['wa_current_user']->user));
			hidden('assign_by', $_SESSION['wa_current_user']->user);
		}
		else
		{
			users_query_list_row(_("*Assign To: "), 'user_id',$_SESSION['wa_current_user']->user);
			hidden('assign_by', $_SESSION['wa_current_user']->user);
			label_cells(_("*User: "), get_user_realname($_SESSION['wa_current_user']->user));
		}
//text_row_ex(_("Remarks:"), 'remarks', 30);
	}
	

end_outer_table(1);

//if($_GET['type'] == 'task'  || $_GET['type'] == "cloning")
	//echo "<center><a href='multiple_task.php?'>" . _("ADD MULTIPLE TASK") . "</a></center></br>\n";

if($_GET['type'] == 'task' || $_GET['type'] == "cloning")
	echo "<center><a href='inquiry/task_inquiry.php?'>"._("Go to Task Inquiry")."</a></center></br>\n";

if($_GET['type'] == 'call')
	echo "<center><a href='inquiry/call_log.php?'>"._("Go To Call Log")."</a></center></br>\n";

if($id==0 || $_GET['type'] == "cloning")
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
