<?php

$page_security = 'SS_CRM_CALL';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");






if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Call Log"), isset($_GET['debtor_no']), false, "", $js);
}
if (isset($_GET['debtor_no'])){
	$_POST['debtor_no'] = $_GET['debtor_no'];
}
if (isset($_GET['FromDate'])){
	$_POST['from_date'] = $_GET['from_date'];
}
if (isset($_GET['ToDate'])){
	$_POST['to_date'] = $_GET['to_date'];
}
if (isset($_GET['status'])){
	$_POST['status'] = $_GET['status'];
}

if (isset($_GET['id'])){
	$_POST['id'] = $_GET['id'];
}
function get_task_nw($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."task WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get task");
	return db_fetch($result);
}
/*

if ($employee_id && !is_new_employee($employee_id))
	{
		label_row(_("Department:"), $_POST['emp_dept']);
		hidden('emp_dept', $_POST['emp_dept']);
	}
	else
	{
		emp_dept_row(_("Department:"), 'emp_dept', null);
	}	*/
//------------------------------------------------------------------------------------------------


if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['debtor_no']))
	$_POST['debtor_no'] = get_global_supplier();
label_cell("<center> <a href=../inquiry/task_inquiry.php? style=\"color: #CC0000\">GO TO TASK INQUIRY</a> </center>");

start_table(TABLESTYLE_NOBORDER);
start_row();
if (!@$_GET['popup'])
	//employee_list2_cells(_("Select a Employee:"), 'employee_id', null, true, false, false, !@$_GET['popup']);
	//customer_list_cells(_("Customer:"), 'debtor_no', null,true, true);
	text_cells(_("Company Name:"), 'description_',null, 30);
date_cells(_("From:"), 'from_date', '', null);

date_cells(_("To:"), 'to_date', '', null,0, 0, 0, null, true);
//supp_transactions_list_cell("filterType", null, true);
//emp_dept_cells(_("Department:"), 'emp_dept', null,true);
//emp_grade_cells(_("Grade:"), 'emp_grade', null,true);
//emp_desg_cells(_("Designation:"), 'emp_desig', null,true);
//date_cells(_("From:"), 'start_date', '', null, -30);
//date_cells(_("To:"), 'end_date');

//status_list_cells(null, 'status', $_POST['status'], true);

end_row();
end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();

//if($_SESSION['wa_current_user']->access == 2){
//
//	users_query_list_cells(_("Assign To:"), 'users_', null,true,true);
//	users_query_list_cells(_("Assign By:"), 'assign_by', null,true,true);
//}
call_type_list_cells(_("Call Type:"), 'call_type_', null,true, false);
users_query_list_cells(_("Forwarded To:"), 'forwarded_to_', null,true, false);
text_cells(_("Person Name:"), 'other_cust_',null, 30);
end_row();
end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();
text_cells(_("Remarks:"), 'remarks_',null, 30);
text_cells(_("Contact No:"), 'contact_no_',null, 30);
//duration_list_cells(_("Plan:"), 'plan_',null, _("Select"), true);
submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

label_cell("<center> <a href=../task.php?type=call style=\"color: #CC0000\">ADD CALL</a> </center>");
if($_SESSION['wa_current_user']->access == 2){
//label_cell("<center> <a href=../view/view_all_history.php style=\"color: #CC0000\">VIEW HISTORY</a> </center>");
	$viewer = "project/view/view_call_log.php?";
	echo "<td>";
	echo 	viewer_link('View All History', $viewer, $class, $id,  $icon);
}
//check_cells(_("Show All:"), 'show_all', null, true);
end_row();
end_table();
set_global_supplier($_POST['debtor_no']);

//------------------------------------------------------------------------------------------------
/*
function display_supplier_summary($supplier_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');
	fhj

    start_table(TABLESTYLE, "width=80%");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));

	table_header($th);
    start_row();
	label_cell($supplier_record["curr_code"]);
    label_cell($supplier_record["terms"]);
    amount_cell($supplier_record["Balance"] - $supplier_record["Due"]);
    amount_cell($supplier_record["Due"] - $supplier_record["Overdue1"]);
    amount_cell($supplier_record["Overdue1"] - $supplier_record["Overdue2"]);
    amount_cell($supplier_record["Overdue2"]);
    amount_cell($supplier_record["Balance"]);
    end_row();
    end_table(1);
} */

//ansar edit

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if (($_POST['debtor_no'] != "") && ($_POST['debtor_no'] != ALL_TEXT))
{
	//$supplier_record = get_supplier_details($_POST['supplier_id'], $_POST['TransToDate']);
	//display_supplier_summary($supplier_record);
}
div_end();

if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}
//----------------------------------------------------------------------------------------------
/*if (isset($_POST['update_values']))
{
	foreach ($_POST['description'] as $delivery => $branch) {

		$checkbox = 'description' . $delivery;

		if (check_value($checkbox)) {
			if (strlen($_POST["description" . $delivery]) != 0) {

				$user_value = $_POST["description" . $delivery];

				$sql = "Update 0_task SET description=" . db_escape($user_value) . "
                        WHERE id=" . db_escape($delivery) . "";

				db_query($sql, "Error");


			}

		}

	}
	foreach ($_POST['remarks'] as $delivery => $branch) {

		$checkbox = 'remarks' . $delivery;

		if (check_value($checkbox)) {
			if (strlen($_POST["remarks" . $delivery]) != 0) {

				$user_value = $_POST["remarks" . $delivery];

				$sql = "Update 0_task SET remarks=" . db_escape($user_value) . "
                        WHERE id=" . db_escape($delivery) . "";

				db_query($sql, "Error");


			}

		}

	}
}*/
$id = find_submit('update_button');

if ($id !=-1)
{

	foreach ($_POST['update_button'] as $delivery => $branch) {

		$checkbox = 'update_button' . $delivery;

		if (check_value($checkbox))
		{
			/***
			For Remarks
			 */
			if (strlen($_POST["remarks" . $delivery]) != 0)
			{
				$user_value = $_POST["remarks" . $delivery];
				$myrow = get_task_nw($delivery);
				$_POST['start_date'] = $myrow["start_date"];
				$_POST['end_date'] = $myrow["end_date"];
				$_POST['Stamp'] = $myrow["Stamp"];
				$_POST['debtor_no'] = $myrow["debtor_no"];
				$_POST['task_type'] = $myrow["task_type"];
				$_POST['call_type'] = $myrow["call_type"];
				$_POST['contact_no'] = $myrow["contact_no"];
				$_POST['other_cust'] = $myrow["other_cust"];
				$_POST['remarks'] = $myrow["remarks"];

				$start_date_db = date2sql($start_date);
				$end_date_db = date2sql($end_date);

				if($myrow["remarks"] != $user_value)
				{
					$sql11 = "INSERT INTO ".TB_PREF."task_history(task_id,start_date, end_date, Stamp, debtor_no,task_type,
					 remarks,call_type, contact_no,other_cust)
					VALUES (".db_escape($delivery) . ",".db_escape($_POST['start_date']) . ", "
						.db_escape($_POST['end_date']) . ", "
						.db_escape($_POST['Stamp']) . ", "
						.db_escape($_POST['debtor_no']) . ", "
						.db_escape($_POST['task_type']) . ", "
						.db_escape($_POST['call_type']).", "
						.db_escape($_POST['contact_no']).", "
						.db_escape($_POST['other_cust']).", "
						.db_escape($user_value) .")";
					db_query($sql11, "The insert of the task failed");
				}

				$sql = " UPDATE 0_task SET remarks=" . db_escape($user_value) . "
                        WHERE id=" . db_escape($delivery) . "";
				db_query($sql, "Error");

			}
			/***
//			For Description
//			 */
//			if (strlen($_POST["description" . $delivery]) != 0) {
//
//				$user_value = $_POST["description" . $delivery];
//
//				$myrow = get_task_nw($delivery);
//
//				$_POST['start_date'] = $myrow["start_date"];
//				$_POST['end_date'] = $myrow["end_date"];
//				$_POST['description'] = $myrow["description"];
//				$_POST['debtor_no'] = $myrow["debtor_no"];
//				$_POST['task_type'] = $myrow["task_type"];
//				$_POST['status'] = $myrow["status"];
//				$_POST['user_id'] = $myrow["user_id"];
//				$_POST['plan'] = $myrow["plan"];
//				$_POST['actual'] = $myrow["actual"];
//				$_POST['remarks'] = $myrow["remarks"];
//				$start_date_db = date2sql($start_date);
//				$end_date_db = date2sql($end_date);
//
//				if ($myrow["description"] != $user_value)
//				{
//					$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
//		            status,user_id,plan,actual, remarks, Stamp)
//		            VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
//						. db_escape($_POST['end_date']) . ", "
//						. db_escape($user_value) . ", "
//						. db_escape($_POST['debtor_no']) . ", "
//						. db_escape($_POST['task_type']) . ", "
//						. db_escape($_POST['status']) . ", "
//						. db_escape($_POST['user_id']) . ", "
//						. db_escape($_POST['plan']) . ", "
//						. db_escape($_POST['actual']) . ", "
//						. db_escape($_POST['remarks']) . ", "
//						. db_escape(date("d-m-Y h:i:sa")) . ")";
//					db_query($sql11, "The insert of the task failed");
//				}
//				$sql = " UPDATE 0_task SET description=" . db_escape($user_value) . "
//                        WHERE id=" . db_escape($delivery) . "";
//				db_query($sql, "Error");
//			}
//
//			/***
//			For Actual
//			 */
//			if (strlen($_POST["actual" . $delivery]) != 0) {
//
//				$user_value = $_POST["actual" . $delivery];
//				$myrow = get_task_nw($delivery);
//
//				$_POST['start_date'] = $myrow["start_date"];
//				$_POST['end_date'] = $myrow["end_date"];
//				$_POST['description'] = $myrow["description"];
//				$_POST['debtor_no'] = $myrow["debtor_no"];
//				$_POST['task_type'] = $myrow["task_type"];
//				$_POST['status'] = $myrow["status"];
//				$_POST['user_id'] = $myrow["user_id"];
//				$_POST['plan'] = $myrow["plan"];
//				$_POST['actual'] = $myrow["actual"];
//				$_POST['remarks'] = $myrow["remarks"];
//				$start_date_db = date2sql($start_date);
//				$end_date_db = date2sql($end_date);
//
//				if ($myrow["actual"] != $user_value)
//				{
//					$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
//		        status,user_id,plan,actual, remarks, Stamp)
//		        VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
//						. db_escape($_POST['end_date']) . ", "
//						. db_escape($_POST['description']) . ", "
//						. db_escape($_POST['debtor_no']) . ", "
//						. db_escape($_POST['task_type']) . ", "
//						. db_escape($_POST['status']) . ", "
//						. db_escape($_POST['user_id']) . ", "
//						. db_escape($_POST['plan']) . ", "
//						. db_escape($_POST['actual']) . ", "
//						. db_escape($user_value) . ", "
//						. db_escape(date("d-m-Y h:i:sa")) . ")";
//					db_query($sql11, "The insert of the task failed");
//				}
//
//				$sql = " UPDATE 0_task SET actual=" . db_escape($user_value) . "
//                        WHERE id=" . db_escape($delivery) . "";
//				db_query($sql, "Error");
//			}
//			/***
//			For Status
//			 */
//			if (strlen($_POST["status" . $delivery]) != 0) {
//
//				$user_value = $_POST["status" . $delivery];
//				$myrow = get_task_nw($delivery);
//
//				$_POST['start_date'] = $myrow["start_date"];
//				$_POST['end_date'] = $myrow["end_date"];
//				$_POST['description'] = $myrow["description"];
//				$_POST['debtor_no'] = $myrow["debtor_no"];
//				$_POST['task_type'] = $myrow["task_type"];
//				$_POST['status'] = $myrow["status"];
//				$_POST['user_id'] = $myrow["user_id"];
//				$_POST['plan'] = $myrow["plan"];
//				$_POST['actual'] = $myrow["actual"];
//				$_POST['remarks'] = $myrow["remarks"];
//				$start_date_db = date2sql($start_date);
//				$end_date_db = date2sql($end_date);
//
//				if ($myrow["status"] != $user_value)
//				{
//					$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
//					status, user_id, plan, actual, remarks, Stamp)
//					VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
//						. db_escape($_POST['end_date']) . ", "
//						. db_escape($_POST['description']) . ", "
//						. db_escape($_POST['debtor_no']) . ", "
//						. db_escape($_POST['task_type']) . ", "
//						. db_escape($user_value) . ", "
//						. db_escape($_POST['user_id']) . ", "
//						. db_escape($_POST['plan']) . ", "
//						. db_escape($_POST['actual']) . ", "
//						. db_escape($_POST['remarks']) . ", "
//						. db_escape(date("d-m-Y h:i:sa")) . ")";
//					db_query($sql11, "The insert of the task failed");
//				}
//				$sql = " UPDATE 0_task SET status=" . db_escape($user_value) . "
//                        WHERE id=" . db_escape($delivery) . "";
//				db_query($sql, "Error");

			//}
		}
	}
}


function update_description($row)
{

	$trans_no = "description" . $row['id'];
	$trans_no1 = $row['description'];

	return $row['Done'] ? '' :
//		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['description'] . '"width="100px" >'
		"<textarea name='$trans_no''"
//		.($title ? " title='$title'" : '')
		.">$trans_no1</textarea>\n"
		. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}
function update_remarks($row)
{

	$trans_no = "remarks" . $row['id'];
	$trans_no1 = $row['remarks'];

	return $row['Done'] ? '' :
//		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['remarks'] . '"width="100px" >'
		"<textarea name='$trans_no''"
//		.($title ? " title='$title'" : '')
		.">$trans_no1</textarea>\n"
		. '<input name="remarks[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}
function update_actual1($row)
{
	$trans_no = "actual" . $row['id'];

	return $row['Done'] ? '' :
		'<input type="text" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . $row['actual'] . '" >'

		. '<input name="actual[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}

function update_button($row)
{

	$trans_no = "update_button" . $row['id'];

	return $row['Done'] ? '' :
		'<input type="submit" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value=update>'

		. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
		. $row['id'] . '">';
}

//---------------------------------------------------------------------------------------------------
function update_actual ($row)
{
	$name = "actual".$row['id'];
	echo "<input name='actual[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return duration_list( $name,$row['actual'],_("Select"),false);
}
function update_status ($row)
{
	$name = "status".$row['id'];
	echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
	return pstatus_list( $name,$row['status'],_("Select"),false);
}
//------------------------------------------------------------------------------------------------
function systype_name($dummy, $type)
{
	global $systypes_array;
	return $systypes_array[$type];
}

function prt_link($row)
{
	if ($row['type'] == ST_SUPPAYMENT || $row['type'] == ST_BANKPAYMENT || $row['type'] == ST_SUPPCREDIT)
		return print_document_link($row['trans_no']."-".$row['type'], _("Print Remittance"), true, ST_SUPPAYMENT, ICON_PRINT);
}

function check_overdue($row)
{
	return $row['OverDue'] == 1
	&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0);
}


function edit_link($row)
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
	return pager_link( _("Edit"),
		"/project/task.php?type=call&$modify=" .$row['id'], ICON_EDIT);

}

function get_users_name($row)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['user_id']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_users_realname($row)

{
	$sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($row['assign_by']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}function get_call_type($row)

{
	$sql = "SELECT call_type FROM ".TB_PREF."call_type WHERE id=".db_escape($row['call_type']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}function get_user_name1($row)

{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['user_id']);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
//function get_user_realname($row)
//{
//	$row = $_SESSION['wa_current_user']->user;
//
//	$result = db_query($sql, "could not get customer");
//
//	$row = db_fetch_row($result);
//
//	return $row;
//}
function get_plan($row)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($row['plan']);

	$result = db_query($sql, "could not get duration of plan");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_actual($row)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($row['actual']);

	$result = db_query($sql, "could not get duration of actual");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_status($row)
{
	$sql = "SELECT status FROM ".TB_PREF."pstatus WHERE id=".db_escape($row['status']);

	$result = db_query($sql, "could not get duration of actual");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_settin()
{
	$sql = "SELECT value FROM ".TB_PREF."settings WHERE name =".db_escape('task_tout');

	$result = db_query($sql, "could not get time for refresh");

	$row = db_fetch_row($result);

	return $row[0];
}
function view_task_link($type, $trans_no, $label="", $icon=false,
						$class='', $id='')
{

	$viewer = "project/view/view_task.php?trans_no=$trans_no";
	//else
	//	return null;

	//if ($label == "")
	$label = $trans_no;

	return viewer_link($label, $viewer, $class, $id,  $icon);
}

function view_history($row)
{

	$viewer = "project/view/view_history.php?trans_no=".db_escape($row['id']);
	//else
	//	return null;

	//if ($label == "")
	//$label1 = $trans_no;

	return viewer_link('view', $viewer, null, $row['id']);
}

function view_history_new($row)
{

	$viewer = "project/view/view_history.php?trans_no=".$row['id'];
	//else
	//	return null;

	//if ($label == "")
	$label1 = $trans_no;

	return viewer_link('view', $viewer, $class, $id,  $icon);
}


//---------------------------------------------------------------------------------------------

function get_sql_for_task_inquiry($from_date,$to_date,$other_cust,$description_,$call_type,$forwarded_to_,$contact_no,$remarks)
{
	$start_date = date2sql($from_date);
	$end_date = date2sql($to_date);

	$sql = " SELECT ".TB_PREF."task.`id`,
					".TB_PREF."task.`start_date`,
					".TB_PREF."task.Stamp,
					".TB_PREF."task.`call_type`,
					".TB_PREF."task.description,
					".TB_PREF."task.`other_cust`,
	                ".TB_PREF."task.`contact_no`,
	                ".TB_PREF."task.`user_id`,
	 				".TB_PREF."task.`remarks`,
	 				".TB_PREF."task.`id` AS trans ,
	 				".TB_PREF."task.inactive 
	 FROM `".TB_PREF."task` 
	WHERE 
	".TB_PREF."task.`start_date`>='$start_date'
	AND
	".TB_PREF."task.`start_date`<='$end_date'
	AND ".TB_PREF."task.`task_type` = 2
	";


//	if ($status != '')
//	{
//
//		$sql .= " AND ".TB_PREF."task.status = ".db_escape($status);
//	}
//	if ($show_all != '')
//		$sql .= " AND ".TB_PREF."task.inactive IN (".db_escape(0).", ".db_escape(1).")";
//	else
//		$sql .= " AND ".TB_PREF."task.inactive = 0";
//
//	if ($users != '')
//	{
//
//		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($users);
//	}
//	if ($assign_by != '')
//	{
//
//		$sql .= " AND ".TB_PREF."task.assign_by = ".db_escape($assign_by);
//	}
//	if ($debtor_no != '')
//	{
//		$sql .= " AND ".TB_PREF."task.debtor_no = ".db_escape($debtor_no);
//	}
//	if ($description!= '')
//	{
//		$number_like = "%".$description."%";
//		$sql .= " AND ".TB_PREF."task.description LIKE ".db_escape($number_like);
//	}
	if ($other_cust!= '')
	{
		$number_like = "%".$other_cust."%";
		$sql .= " AND ".TB_PREF."task.other_cust LIKE ".db_escape($number_like);
	}
	if ($call_type != '')
	{
		$sql .= " AND ".TB_PREF."task.call_type = ".db_escape($call_type);
	}if ($forwarded_to_ != '')
	{
		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($forwarded_to_);
	}
	if ($contact_no!= '')
	{
		$number_like = "%".$contact_no."%";
		$sql .= " AND ".TB_PREF."task.contact_no LIKE ".db_escape($number_like);
	}
	if ($remarks!= '')
	{
		$number_like = "%".$remarks."%";
		$sql .= " AND ".TB_PREF."task.remarks LIKE ".db_escape($number_like);
	}if ($description_!= '')
	{
		$number_like = "%".$description_."%";
		$sql .= " AND ".TB_PREF."task.description LIKE ".db_escape($number_like);
	}

//	if ($plan!= '')
//	{
//		$number_like = "%".$plan."%";
//		$sql .= " AND ".TB_PREF."task.plan LIKE ".db_escape($number_like);
//	}
//	if ($_SESSION["wa_current_user"]->access != 2)
//	{
//		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($_SESSION["wa_current_user"]->user);
//	}
	$sql .= " ORDER BY id DESC";
	return $sql;
//
}
if ($_SESSION["wa_current_user"]->access == 2)
{
	$page = $_SERVER['PHP_SELF'];
	$company = get_settin();
	$sec =$company;
	header("Refresh: $sec; url=$page");
}

//======================================================================================================
function rec_checkbox($row)
{
	$name = "rec_" .$row['id'];
	$hidden = 'last['.$row['id'].']';
	$value = $row['inactive'];
	if($row['status'] == 6)
		return checkbox(null, $name, $value, true, _('Close This Task'))
		. hidden($hidden, $value, false);

}

if (isset($_POST['Reconcile'])) {
	set_focus('bank_date');
	foreach($_POST['last'] as $id => $value)
		if ($value != check_value('rec_'.$id))
			if(!change_tpl_flag_for_task($id)) break;
	$Ajax->activate('_page_body');
}
$id = find_submit('_rec_');
if ($id != -1)
	change_tpl_flag_for_task($id);
function change_tpl_flag_for_task($reconcile_id)
{
	global $Ajax;

	$reconcile_value = check_value("rec_".$reconcile_id);

	update_task_inactive($reconcile_id, $reconcile_value);

	$Ajax->activate('reconciled');
	$Ajax->activate('difference');
	return true;
}
function update_task_inactive($reconcile_id, $reconcile_value)
{
	$sql = "UPDATE ".TB_PREF."task SET inactive=$reconcile_value"
		." WHERE id=".db_escape($reconcile_id);

	db_query($sql, "Can't change reconciliation status");

}

function get_format_date($row)
{
//	$g = sql2date();
	$Format_Date = date("d-m-Y h:i:sa", strtotime($row['Stamp']));
	return $Format_Date;
}



//======================================================================================================


$sql = get_sql_for_task_inquiry( $_POST['from_date'], $_POST['to_date'],
	$_POST['other_cust_'],$_POST['description_'],$_POST['call_type_'],$_POST['forwarded_to_'],  $_POST['contact_no_'],  $_POST['remarks_']);
$cols = array(
	array('insert'=>true, 'fun'=>'edit_link'),
	_("#") => array('fun'=>'view_task_link'),
	// _("X")=>array('insert'=>true, 'fun'=>'delete_checkbox'),
	_("Start date")=> array( 'type'=>'date'),
	_("Stamp")=> array('fun'=>'get_format_date'),
	_("Call Type") => array('fun'=>'get_call_type', 'align'=>'center'),
	_("Company Name") => array(/*'fun'=>'get_call_type', 'align'=>'center'*/),
	_("Person Name"),
	_("Contact No")=> array(/*'insert'=>true, 'fun'=>'get_call_type', 'align'=>'center'*/) ,
	_("Forwarded To")=> array('insert'=>true, 'fun'=>'get_user_name1', 'align'=>'center') ,
	_("Remarks") => array('fun'=>'update_remarks', 'align'=>'left'),
	_("update")=>array('fun'=>'update_button'),
);


/*
if ($_POST['debtor_no'] != ALL_TEXT)
{
	$cols[_("Supplier")] = 'skip';
	$cols[_("Currency")] = 'skip';
}
*/

//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "85%";



display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], false, false);
}
?>

