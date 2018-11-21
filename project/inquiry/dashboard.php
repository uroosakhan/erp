<?php
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/db_pager.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Task Inquiry Dashboard"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");


include_once($path_to_root . "/inventory/includes/inventory_db.inc");

//
//$user_comp = user_company();
//$new_item = get_post('stock_id')=='' || get_post('cancel') || get_post('clone');
//------------------------------------------------------------------------------------
//
//if (isset($_GET['stock_id']))
//{
//	$_POST['stock_id'] = $_GET['stock_id'];
//}
//$stock_id = get_post('stock_id');
//if (list_updated('stock_id'))
//{
//	$_POST['NewStockID'] = $stock_id = get_post('stock_id');
//    clear_data();
//	$Ajax->activate('details');
//	$Ajax->activate('controls');
//}
//
//if (get_post('cancel')) {
//	$_POST['NewStockID'] = $stock_id = $_POST['stock_id'] = '';
//    clear_data();
//	set_focus('stock_id');
//	$Ajax->activate('_page_body');
//}
//if (list_updated('category_id') || list_updated('mb_flag')) {
//	$Ajax->activate('details');
//}
//$upload_file = "";
//if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '')
//{
//	$stock_id = $_POST['NewStockID'];
//	$result = $_FILES['pic']['error'];
// 	$upload_file = 'Yes'; //Assume all is well to start off with
//	$filename = company_path().'/images';
//	if (!file_exists($filename))
//	{
//		mkdir($filename);
//	}
//
//	$filename .= "/".item_img_name($stock_id).".jpg";
//
//	//But check for the worst
//	if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
//		$imagetype = $type;
//	else
//		$imagetype = false;
//	//$imagetype = exif_imagetype($_FILES['pic']['tmp_name']);
//	if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG)
//	{	//File type Check
//		display_warning( _('Only graphics files can be uploaded'));
//		$upload_file ='No';
//	}
//	elseif (@strtoupper(substr(trim($_FILES['pic']['name']), @in_array(strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF')))
//	{
//		display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
//		$upload_file ='No';
//	}
//	elseif ( $_FILES['pic']['size'] > ($max_image_size * 1024))
//	{ //File Size Check
//		display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
//		$upload_file ='No';
//	}
//	elseif (file_exists($filename))
//	{
//		$result = unlink($filename);
//		if (!$result)
//		{
//			display_error(_('The existing image could not be removed'));
//			$upload_file ='No';
//		}
//	}
//
//	if ($upload_file == 'Yes')
//	{
//		$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
//	}
//	$Ajax->activate('details');
// /* EOF Add Image upload for New Item  - by Ori */
//}
//
//check_db_has_stock_categories(_("There are no item categories defined in the system. At least one item category is required to add a item."));
//
//check_db_has_item_tax_types(_("There are no item tax types defined in the system. At least one item tax type is required to add a item."));
//
//function clear_data()
//{
//	unset($_POST['long_description']);
//	unset($_POST['description']);
//	unset($_POST['carton']);
//	unset($_POST['category_id']);
//	unset($_POST['tax_type_id']);
//	unset($_POST['units']);
//	unset($_POST['mb_flag']);
//	unset($_POST['NewStockID']);
//	unset($_POST['dimension_id']);
//	unset($_POST['dimension2_id']);
//	unset($_POST['no_sale']);
//}

//------------------------------------------------------------------------------------
//
//if (isset($_POST['addupdate']))
//{
//
//	$input_error = 0;
//	if ($upload_file == 'No')
//		$input_error = 1;
//	if (strlen($_POST['description']) == 0)
//	{
//		$input_error = 1;
//		display_error( _('The item name must be entered.'));
//		set_focus('description');
//	}
//	elseif (strlen($_POST['NewStockID']) == 0)
//	{
//		$input_error = 1;
//		display_error( _('The item code cannot be empty'));
//		set_focus('NewStockID');
//	}
//	elseif (strstr($_POST['NewStockID'], " ") || strstr($_POST['NewStockID'],"'") ||
//		strstr($_POST['NewStockID'], "+") || strstr($_POST['NewStockID'], "\"") ||
//		strstr($_POST['NewStockID'], "&") || strstr($_POST['NewStockID'], "\t"))
//	{
//		$input_error = 1;
//		display_error( _('The item code cannot contain any of the following characters -  & + OR a space OR quotes'));
//		set_focus('NewStockID');
//
//	}
//	elseif ($new_item && db_num_rows(get_item_kit($_POST['NewStockID'])))
//	{
//		  	$input_error = 1;
//      		display_error( _("This item code is already assigned to stock item or sale kit."));
//			set_focus('NewStockID');
//	}
//
//	if ($input_error != 1)
//	{
//		if (check_value('del_image'))
//		{
//			$filename = company_path().'/images/'.item_img_name($_POST['NewStockID']).".jpg";
//			if (file_exists($filename))
//				unlink($filename);
//		}
//
//		if (!$new_item)
//		{ /*so its an existing one */
//			update_item($_POST['NewStockID'], $_POST['description'],
//				$_POST['long_description'], $_POST['carton'], $_POST['category_id'],
//				$_POST['tax_type_id'], get_post('units'),
//				get_post('mb_flag'), $_POST['sales_account'],
//				$_POST['inventory_account'], $_POST['cogs_account'],
//				$_POST['adjustment_account'], $_POST['assembly_account'],
//				$_POST['dimension_id'], $_POST['dimension2_id'],
//				check_value('no_sale'), check_value('editable'));
//			update_record_status($_POST['NewStockID'], $_POST['inactive'],
//				'stock_master', 'stock_id');
//			update_record_status($_POST['NewStockID'], $_POST['inactive'],
//				'item_codes', 'item_code');
//			set_focus('stock_id');
//			$Ajax->activate('stock_id'); // in case of status change
//			display_notification(_("Item has been updated."));
//		}
//		else
//		{ //it is a NEW part
//
//			add_item($_POST['NewStockID'], $_POST['description'],
//				$_POST['long_description'], $_POST['carton'], $_POST['category_id'], $_POST['tax_type_id'],
//				$_POST['units'], $_POST['mb_flag'], $_POST['sales_account'],
//				$_POST['inventory_account'], $_POST['cogs_account'],
//				$_POST['adjustment_account'], $_POST['assembly_account'],
//				$_POST['dimension_id'], $_POST['dimension2_id'],
//				check_value('no_sale'), check_value('editable'));
//
//			display_notification(_("A new item has been added."));
//			$_POST['stock_id'] = $_POST['NewStockID'] =
//			$_POST['description'] = $_POST['long_description'] =  $_POST['carton'] = '';
//			$_POST['no_sale'] = $_POST['editable'] = 0;
//			set_focus('NewStockID');
//		}
//		$Ajax->activate('_page_body');
//	}
//}
//
//if (get_post('clone')) {
//	unset($_POST['stock_id']);
//	$stock_id = '';
//	unset($_POST['inactive']);
//	set_focus('NewStockID');
//	$Ajax->activate('_page_body');
//}

//------------------------------------------------------------------------------------

//function check_usage($stock_id, $dispmsg=true)
//{
//	$msg = item_in_foreign_codes($stock_id);
//
//	if ($msg != '')	{
//		if($dispmsg) display_error($msg);
//		return false;
//	}
//	return true;
//}

//------------------------------------------------------------------------------------

//if (isset($_POST['delete']) && strlen($_POST['delete']) > 1)
//{
//
////	if (check_usage($_POST['NewStockID'])) {
////
////		$stock_id = $_POST['NewStockID'];
////		delete_item($stock_id);
//		//$filename = company_path().'/images/'.item_img_name($stock_id).".jpg";
//	//	if (file_exists($filename))
//	//		unlink($filename);
//		//display_notification(_("Selected item has been deleted."));
//		$_POST['stock_id'] = '';
//	//	clear_data();
//		set_focus('stock_id');
//		$new_item = true;
//		$Ajax->activate('_page_body');
//	//}
//}

//function item_settings(&$stock_id)
//{
////	start_outer_table(TABLESTYLE2);
////
//////	start_table(TABLESTYLE_NOBORDER);
////	start_row();
////	customer_list_cells(_("Customer:"), 'debtor_no', null,true, true);
////
////	date_cells(_("From:"), 'from_date', '', null, -30);
////
////	date_cells(_("To:"), 'to_date', '', null, 0, 0, 0, null, true);
////	if($_SESSION['wa_current_user']->access == 2){
////
////		users_query_list_cells(_("Assign To:"), 'users_', null,true,true);
////		users_query_list_cells(_("Assign By:"), 'assign_by', null,true,true);
////
////	}
////	pstatus_list_cells(_("Status:"), 'status_', null,true, false);
////	text_cells(_("Description:"), 'description_',null, 30);
////	end_row();
//////	end_table();
//////	start_table(TABLESTYLE_NOBORDER);
////	start_row();
////	text_cells(_("Remarks:"), 'remarks_',null, 30);
////	duration_list_cells(_("Plan:"), 'plan_',null, _("Select"), true);
////	submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');
////
////	label_cell("<center> <a href=../task.php?NewBooking=Yes style=\"color: #CC0000\">ADD TASK</a> </center>");
////	if($_SESSION['wa_current_user']->access == 2){
//////label_cell("<center> <a href=../view/view_all_history.php style=\"color: #CC0000\">VIEW HISTORY</a> </center>");
////		$viewer = "project/view/view_task_inquiry.php?";
////		echo "<td>";
////		echo 	viewer_link('View All History', $viewer, $class, $id,  $icon);
////	}
////	if($_SESSION['wa_current_user']->access == 2)
////		check_cells(_("Show All:"), 'show_all', null, true);
////
////	end_row();
////	end_table();
//
//// ===========================================================================================================
////	function get_sql_for_task_inquiry($from_date,$to_date, $status,$users,$assign_by, $debtor_no,$description,$remarks,$plan,$show_all)
////	{
////		$start_date = date2sql($from_date);
////		$end_date = date2sql($to_date);
////		$sql = " jkoljipi ".TB_PREF."task.`id`,".TB_PREF."task.`start_date`,
////			".TB_PREF."task.`end_date`,".TB_PREF."task.Stamp,".TB_PREF."task.task_type,
////			".TB_PREF."debtors_master.debtor_ref,".TB_PREF."task.`status`,
////			".TB_PREF."task.`user_id`,".TB_PREF."task.`plan`,".TB_PREF."task.`actual`,
////			".TB_PREF."task.description,".TB_PREF."task.`remarks`,".TB_PREF."task.`assign_by`,".TB_PREF."task.`id` AS trans
////			FROM `".TB_PREF."task`
////			INNER JOIN  ".TB_PREF."pstatus ON ".TB_PREF."pstatus.id=".TB_PREF."task.`status`
////			INNER JOIN  ".TB_PREF."debtors_master ON
////			".TB_PREF."debtors_master.`debtor_no`=".TB_PREF."task.`debtor_no`
////			WHERE
////			".TB_PREF."task.`start_date`>='$start_date'
////			AND
////			".TB_PREF."task.`start_date`<='$end_date' ";
////		if ($status != '')
////		{
////
////			$sql .= " AND ".TB_PREF."task.status = ".db_escape($status);
////		}
////		if ($show_all != '')
////			$sql .= " AND ".TB_PREF."task.inactive IN (".db_escape(0).", ".db_escape(1).")";
////		else
////			$sql .= " AND ".TB_PREF."task.inactive = 0";
////
////		if ($users != '')
////		{
////
////			$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($users);
////		}
////
////		if ($assign_by != '')
////		{
////
////			$sql .= " AND ".TB_PREF."task.assign_by = ".db_escape($assign_by);
////		}
////
////		if ($debtor_no != '')
////		{
////			$sql .= " AND ".TB_PREF."task.debtor_no = ".db_escape($debtor_no);
////		}
////		if ($description!= '')
////		{
////			$number_like = "%".$description."%";
////			$sql .= " AND ".TB_PREF."task.description LIKE ".db_escape($number_like);
////		}
////		if ($remarks!= '')
////		{
////			$number_like = "%".$remarks."%";
////			$sql .= " AND ".TB_PREF."task.remarks LIKE ".db_escape($number_like);
////		}
////		if ($plan!= '')
////		{
////			$number_like = "%".$plan."%";
////			$sql .= " AND ".TB_PREF."task.plan LIKE ".db_escape($number_like);
////		}
////		if ($_SESSION["wa_current_user"]->access != 2)
////		{
////			$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($_SESSION["wa_current_user"]->user);
////		}
////		$sql .= " ORDER BY id DESC";
////		return $sql;
////
////	}
//	function check_overdue($row)
//	{
//		return $row['OverDue'] == 1
//		&& (abs($row["TotalAmount"]) - $row["Allocated"] != 0);
//	}
//	function view_history_new($row)
//	{
//
//		$viewer = "project/view/view_history.php?trans_no=".$row['id'];
//		//else
//		//	return null;
//
//		//if ($label == "")
//		$label1 = $trans_no;
//
//		return viewer_link('view', $viewer, $class, $id,  $icon);
//	}
//
//	function view_history($row)
//	{
//		$viewer = "project/view/view_history.php?trans_no=".db_escape($row['id']);
//		//else
//		//	return null;
//
//		//if ($label == "")
//		//$label1 = $trans_no;
//
//		return viewer_link('view', $viewer, null, $row['id']);
//	}
//	function get_task_type($row)
//	{
//		$sql = "SELECT task_type FROM ".TB_PREF."task_type WHERE id=".db_escape($row['task_type']);
//
//		$result = db_query($sql, "could not get task type");
//
//		$row = db_fetch_row($result);
//
//		return $row[0];
//	}
//	function update_status ($row)
//	{
//		$name = "status".$row['id'];
//		echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
//		return pstatus_list( $name,$row['status'],_("Select"),false);
//	}
//	function get_users_name($row)
//	{
//		$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row['user_id']);
//
//		$result = db_query($sql, "could not get customer");
//
//		$row = db_fetch_row($result);
//
//		return $row[0];
//	}
//	function get_plan($row)
//	{
//		$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($row['plan']);
//
//		$result = db_query($sql, "could not get duration of plan");
//
//		$row = db_fetch_row($result);
//
//		return $row[0];
//	}
//	function update_actual ($row)
//	{
//		$name = "actual".$row['id'];
//		echo "<input name='actual[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
//		return duration_list( $name,$row['actual'],_("Select"),false);
//	}
//	function update_description($row)
//	{
//
//		$trans_no = "description" . $row['id'];
//		$trans_no1 = $row['description'];
//
//		return $row['Done'] ? '' :
////		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['description'] . '"width="100px" >'
//			"<textarea name='$trans_no''"
////		.($title ? " title='$title'" : '')
//			.">$trans_no1</textarea>\n"
//			. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
//			. $row['id'] . '">';
//	}
//	function update_remarks($row)
//	{
//
//		$trans_no = "remarks" . $row['id'];
//		$trans_no1 = $row['remarks'];
//
//		return $row['Done'] ? '' :
////		'<input type="textarea" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value="' . "". $row['remarks'] . '"width="100px" >'
//			"<textarea name='$trans_no''"
////		.($title ? " title='$title'" : '')
//			.">$trans_no1</textarea>\n"
//			. '<input name="remarks[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
//			. $row['id'] . '">';
//	}
//	function get_users_realname($row)
//	{
//		$sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($row['assign_by']);
//
//		$result = db_query($sql, "could not get customer");
//
//		$row = db_fetch_row($result);
//
//		return $row[0];
//	}
//	function edit_link($row)
//	{
//		if (@$_GET['popup'])
//			//return '';
//			$delete=delete_emp_info($row['id']);
//		$modify = 'id';
//		return pager_link( _("Edit"),
//			"/project/task.php?$modify=" .$row['id'], ICON_EDIT);
//
//	}
////	function clone_link($row)
////	{
////		if (@$_GET['popup'])
////			//return '';
////			//$delete=delete_emp_info($row['id']);
////		$modify = 'id';
////		return pager_link( _("Edit"),
////			"/project/task.php?$modify=" .$row['id']." && Type=cloning",ICON_ADD);
////
////	}
////	function update_button($row)
////	{
////		$trans_no = "update_button" . $row['id'];
////
////		return $row['Done'] ? '' :
////			'<input type="submit" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value=update>'
////
////			. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="'
////			. $row['id'] . '">';
////	}
////	$id = find_submit('update_button');
////
////	if ($id !=-1)
////	{
////		foreach ($_POST['update_button'] as $delivery => $branch) {
////
////			$checkbox = 'update_button' . $delivery;
////
////			if (check_value($checkbox))
////			{
////				/***
////				For Remarks
////				 */
////				if (strlen($_POST["remarks" . $delivery]) != 0)
////				{
////					$user_value = $_POST["remarks" . $delivery];
////					$myrow = get_task_nw($delivery);
////					$_POST['start_date'] = $myrow["start_date"];
////					$_POST['end_date'] = $myrow["end_date"];
////					$_POST['description'] = $myrow["description"];
////					$_POST['debtor_no'] = $myrow["debtor_no"];
////					$_POST['task_type'] = $myrow["task_type"];
////					$_POST['status'] = $myrow["status"];
////					$_POST['user_id'] = $myrow["user_id"];
////					$_POST['plan'] = $myrow["plan"];
////					$_POST['actual'] = $myrow["actual"];
////					$_POST['remarks'] = $myrow["remarks"];
////
////					$start_date_db = date2sql($start_date);
////					$end_date_db = date2sql($end_date);
////
////					if($myrow["remarks"] != $user_value)
////					{
////						$sql11 = "INSERT INTO ".TB_PREF."task_history(task_id,start_date, end_date, description, debtor_no,task_type,
////					status,user_id,plan,actual, remarks, Stamp)
////					VALUES (".db_escape($delivery) . ",".db_escape($_POST['start_date']) . ", "
////							.db_escape($_POST['end_date']) . ", "
////							.db_escape($_POST['description']) . ", "
////							.db_escape($_POST['debtor_no']) . ", "
////							.db_escape($_POST['task_type']) . ", "
////							.db_escape($_POST['status']).", "
////							.db_escape($_POST['user_id']).", "
////							.db_escape($_POST['plan']).", "
////							.db_escape($_POST['actual']).", "
////							.db_escape($user_value).", "
////							.db_escape(date("d-m-Y h:i:sa")).")";
////						db_query($sql11, "The insert of the task failed");
////					}
////
////					$sql = " UPDATE 0_task SET remarks=" . db_escape($user_value) . "
////                        WHERE id=" . db_escape($delivery) . "";
////					db_query($sql, "Error");
////
////				}
////				/***
////				For Description
////				 */
////				if (strlen($_POST["description" . $delivery]) != 0) {
////
////					$user_value = $_POST["description" . $delivery];
////
////					$myrow = get_task_nw($delivery);
////
////					$_POST['start_date'] = $myrow["start_date"];
////					$_POST['end_date'] = $myrow["end_date"];
////					$_POST['description'] = $myrow["description"];
////					$_POST['debtor_no'] = $myrow["debtor_no"];
////					$_POST['task_type'] = $myrow["task_type"];
////					$_POST['status'] = $myrow["status"];
////					$_POST['user_id'] = $myrow["user_id"];
////					$_POST['plan'] = $myrow["plan"];
////					$_POST['actual'] = $myrow["actual"];
////					$_POST['remarks'] = $myrow["remarks"];
////					$start_date_db = date2sql($start_date);
////					$end_date_db = date2sql($end_date);
////
////					if ($myrow["description"] != $user_value)
////					{
////						$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
////		            status,user_id,plan,actual, remarks, Stamp)
////		            VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
////							. db_escape($_POST['end_date']) . ", "
////							. db_escape($user_value) . ", "
////							. db_escape($_POST['debtor_no']) . ", "
////							. db_escape($_POST['task_type']) . ", "
////							. db_escape($_POST['status']) . ", "
////							. db_escape($_POST['user_id']) . ", "
////							. db_escape($_POST['plan']) . ", "
////							. db_escape($_POST['actual']) . ", "
////							. db_escape($_POST['remarks']) . ", "
////							. db_escape(date("d-m-Y h:i:sa")) . ")";
////						db_query($sql11, "The insert of the task failed");
////					}
////					$sql = " UPDATE 0_task SET description=" . db_escape($user_value) . "
////                        WHERE id=" . db_escape($delivery) . "";
////					db_query($sql, "Error");
////				}
////
////				/***
////				For Actual
////				 */
////				if (strlen($_POST["actual" . $delivery]) != 0) {
////
////					$user_value = $_POST["actual" . $delivery];
////					$myrow = get_task_nw($delivery);
////
////					$_POST['start_date'] = $myrow["start_date"];
////					$_POST['end_date'] = $myrow["end_date"];
////					$_POST['description'] = $myrow["description"];
////					$_POST['debtor_no'] = $myrow["debtor_no"];
////					$_POST['task_type'] = $myrow["task_type"];
////					$_POST['status'] = $myrow["status"];
////					$_POST['user_id'] = $myrow["user_id"];
////					$_POST['plan'] = $myrow["plan"];
////					$_POST['actual'] = $myrow["actual"];
////					$_POST['remarks'] = $myrow["remarks"];
////					$start_date_db = date2sql($start_date);
////					$end_date_db = date2sql($end_date);
////
////					if ($myrow["actual"] != $user_value)
////					{
////						$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
////		        status,user_id,plan,actual, remarks, Stamp)
////		        VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
////							. db_escape($_POST['end_date']) . ", "
////							. db_escape($_POST['description']) . ", "
////							. db_escape($_POST['debtor_no']) . ", "
////							. db_escape($_POST['task_type']) . ", "
////							. db_escape($_POST['status']) . ", "
////							. db_escape($_POST['user_id']) . ", "
////							. db_escape($_POST['plan']) . ", "
////							. db_escape($_POST['actual']) . ", "
////							. db_escape($user_value) . ", "
////							. db_escape(date("d-m-Y h:i:sa")) . ")";
////						db_query($sql11, "The insert of the task failed");
////					}
////
////					$sql = " UPDATE 0_task SET actual=" . db_escape($user_value) . "
////                        WHERE id=" . db_escape($delivery) . "";
////					db_query($sql, "Error");
////				}
////				/***
////				For Status
////				 */
////				if (strlen($_POST["status" . $delivery]) != 0) {
////
////					$user_value = $_POST["status" . $delivery];
////					$myrow = get_task_nw($delivery);
////
////					$_POST['start_date'] = $myrow["start_date"];
////					$_POST['end_date'] = $myrow["end_date"];
////					$_POST['description'] = $myrow["description"];
////					$_POST['debtor_no'] = $myrow["debtor_no"];
////					$_POST['task_type'] = $myrow["task_type"];
////					$_POST['status'] = $myrow["status"];
////					$_POST['user_id'] = $myrow["user_id"];
////					$_POST['plan'] = $myrow["plan"];
////					$_POST['actual'] = $myrow["actual"];
////					$_POST['remarks'] = $myrow["remarks"];
////					$start_date_db = date2sql($start_date);
////					$end_date_db = date2sql($end_date);
////
////					if ($myrow["status"] != $user_value)
////					{
////						$sql11 = "INSERT INTO " . TB_PREF . "task_history(task_id,start_date, end_date, description, debtor_no,task_type,
////					status, user_id, plan, actual, remarks, Stamp)
////					VALUES (" . db_escape($delivery) . "," . db_escape($_POST['start_date']) . ", "
////							. db_escape($_POST['end_date']) . ", "
////							. db_escape($_POST['description']) . ", "
////							. db_escape($_POST['debtor_no']) . ", "
////							. db_escape($_POST['task_type']) . ", "
////							. db_escape($user_value) . ", "
////							. db_escape($_POST['user_id']) . ", "
////							. db_escape($_POST['plan']) . ", "
////							. db_escape($_POST['actual']) . ", "
////							. db_escape($_POST['remarks']) . ", "
////							. db_escape(date("d-m-Y h:i:sa")) . ")";
////						db_query($sql11, "The insert of the task failed");
////					}
////					$sql = " UPDATE 0_task SET status=" . db_escape($user_value) . "
////                        WHERE id=" . db_escape($delivery) . "";
////					db_query($sql, "Error");
////				}
////			}
////		}
////	}
////	function rec_checkbox($row)
////	{
////		$name = "rec_" .$row['id'];
////		$hidden = 'last['.$row['id'].']';
////		$value = $row['inactive'] != '';
////		if($row['status'] == 1)
////			return checkbox(null, $name, $value, true, _('Close This Task'))
////			. hidden($hidden, $value, false);
////	}
////
////	if (isset($_POST['Reconcile'])) {
////		set_focus('bank_date');
////		foreach($_POST['last'] as $id => $value)
////			if ($value != check_value('rec_'.$id))
////				if(!change_tpl_flag_for_task($id)) break;
////		$Ajax->activate('_page_body');
////	}
////	$id = find_submit('_rec_');
////	if ($id != -1)
////		change_tpl_flag_for_task($id);
////	function change_tpl_flag_for_task($reconcile_id)
////	{
////		global $Ajax;
////
////		$reconcile_value = check_value("rec_".$reconcile_id);
////
////		update_task_inactive($reconcile_id, $reconcile_value);
////
////		$Ajax->activate('reconciled');
////		$Ajax->activate('difference');
////		return true;
////	}
////	function update_task_inactive($reconcile_id, $reconcile_value)
////	{
////		$sql = "UPDATE ".TB_PREF."task SET inactive=$reconcile_value" ."
////				WHERE id=".db_escape($reconcile_id);
////
////		db_query($sql, "Can't change reconciliation status");
////
////	}
////	function view_task_link($type, $trans_no, $label="", $icon=false, $class='', $id='')
////	{
////		$viewer = "project/view/view_task.php?trans_no=$trans_no";
////
////		$label = $trans_no;
////
////		return viewer_link($label, $viewer, $class, $id,  $icon);
////	}
//// ===========================================================================================================
////	include_once($path_to_root . "/includes/db_pager.inc");
////	$_SESSION['sql1'] = get_sql_for_task_inquiry( $_POST['from_date'], $_POST['to_date'], $_POST['status_'],$_POST['users_'], $_POST['assign_by'],$_POST['debtor_no'],$_POST['description_'],$_POST['remarks_'],$_POST['plan_'], check_value('show_all'));
////	$_SESSION['cols1'] = array(
////		array('insert'=>true, 'fun'=>'edit_link'),
////		_("#") => array('fun'=>'view_task_link'),
////		// _("X")=>array('insert'=>true, 'fun'=>'delete_checkbox'),
////		_("Start date")=> array( 'type'=>'date'),
////		_("End date")=> array( 'type'=>'date'),
////		_("Stamp")=> array(/*'fun'=>'get_format_date'*/),
////		_("Task Type")=> array('fun'=>'get_task_type', 'align'=>'center'),
////		_("Customers"),
////		_("Status")=> array('insert'=>true, 'fun'=>'update_status', 'align'=>'center'),
////		_("Assign To")=>array('fun'=>'get_users_name'),
////		_("Plan")=>array('fun'=>'get_plan'));
////	if($_SESSION['wa_current_user']->access == 2)
////		array_append($_SESSION['cols1'], array(
////			_("Actual") => array('insert'=>true, 'fun'=>'update_actual', 'align'=>'center'),
////			_("Description") => array('insert'=>true, 'fun'=>'update_description', 'align'=>'left'),
////			_("Remarks") => array('insert'=>true, 'fun'=>'update_remarks', 'align'=>'left'),
////			_("Assign By")=>array('fun'=>'get_users_realname'),
////			_("View History") => array('fun'=>'view_history_new'),
////			array('insert'=>true, 'fun'=>'edit_link'),
////			_("update")=>array('fun'=>'update_button'),
////			_("Cloning") =>array('insert'=>true, 'fun'=>'clone_link'),
////			"Approved"=>array('insert'=>true, 'fun'=>'rec_checkbox', 'align'=>'center')));
////	else
////		array_append($_SESSION['cols1'], array(
////			_("Actual") => array('insert'=>true, 'fun'=>'update_actual', 'align'=>'center'),
////			_("Description") => array('insert'=>true, 'fun'=>'update_description', 'align'=>'left'),
////			_("Remarks") => array('insert'=>true, 'fun'=>'update_remarks', 'align'=>'left'),
////			_("Assign By")=>array('fun'=>'get_users_realname'),
////			_("View History") => array('fun'=>'view_history_new'),
////			array('insert'=>true, 'fun'=>'edit_link'),
////			_("update")=>array('fun'=>'update_button'),));
//
//
//	//------------------------------------------------------------------------------------
////	if ($new_item)
////	{
////		text_row(_("Item Code:"), 'NewStockID', null, 21, 20);
////
////		$_POST['inactive'] = 0;
////	}
////	else
////	{ // Must be modifying an existing item
////		if (get_post('NewStockID') != get_post('stock_id') || get_post('addupdate')) { // first item display
////
////			$_POST['NewStockID'] = $_POST['stock_id'];
////
////			$myrow = get_item($_POST['NewStockID']);
////
////			$_POST['long_description'] = $myrow["long_description"];
////			$_POST['description'] = $myrow["description"];
////			$_POST['carton'] = $myrow["carton"];
////			$_POST['category_id']  = $myrow["category_id"];
////			$_POST['tax_type_id']  = $myrow["tax_type_id"];
////			$_POST['units']  = $myrow["units"];
////			$_POST['mb_flag']  = $myrow["mb_flag"];
////
////			$_POST['sales_account'] =  $myrow['sales_account'];
////			$_POST['inventory_account'] = $myrow['inventory_account'];
////			$_POST['cogs_account'] = $myrow['cogs_account'];
////			$_POST['adjustment_account']	= $myrow['adjustment_account'];
////			$_POST['assembly_account']	= $myrow['assembly_account'];
////			$_POST['dimension_id']	= $myrow['dimension_id'];
////			$_POST['dimension2_id']	= $myrow['dimension2_id'];
////			$_POST['no_sale']	= $myrow['no_sale'];
////			$_POST['del_image'] = 0;
////			$_POST['inactive'] = $myrow["inactive"];
////			$_POST['editable'] = $myrow["editable"];
////		}
////		label_row(_("Item Code:"),$_POST['NewStockID']);
////		hidden('NewStockID', $_POST['NewStockID']);
////		set_focus('description');
////	}
////
////	text_row(_("Name:"), 'description', null, 52, 200);
////
////	textarea_row(_('Description:'), 'long_description', null, 42, 3);
////
////	text_row(_("Packing:"), 'carton', null, 5, 5);
////
////
////	stock_categories_list_row(_("Category:"), 'category_id', null, false, $new_item);
////
////	if ($new_item && (list_updated('category_id') || !isset($_POST['units']))) {
////
////		$category_record = get_item_category($_POST['category_id']);
////
////		$_POST['tax_type_id'] = $category_record["dflt_tax_type"];
////		$_POST['units'] = $category_record["dflt_units"];
////		$_POST['mb_flag'] = $category_record["dflt_mb_flag"];
////		$_POST['inventory_account'] = $category_record["dflt_inventory_act"];
////		$_POST['cogs_account'] = $category_record["dflt_cogs_act"];
////		$_POST['sales_account'] = $category_record["dflt_sales_act"];
////		$_POST['adjustment_account'] = $category_record["dflt_adjustment_act"];
////		$_POST['assembly_account'] = $category_record["dflt_assembly_act"];
////		$_POST['dimension_id'] = $category_record["dflt_dim1"];
////		$_POST['dimension2_id'] = $category_record["dflt_dim2"];
////		$_POST['no_sale'] = $category_record["dflt_no_sale"];
////		$_POST['editable'] = 0;
////
////	}
////	$fresh_item = !isset($_POST['NewStockID']) || $new_item
////		|| check_usage($_POST['stock_id'],false);
////
////	item_tax_types_list_row(_("Item Tax Type:"), 'tax_type_id', null);
////
////	stock_item_types_list_row(_("Item Type:"), 'mb_flag', null, $fresh_item);
////
////	stock_units_list_row(_('Units of Measure:'), 'units', null, $fresh_item);
////
////	check_row(_("Editable description:"), 'editable');
////
////	check_row(_("Exclude from sales:"), 'no_sale');
////
////	table_section(2);
////
////	$dim = get_company_pref('use_dimension');
////	if ($dim >= 1)
////	{
////		table_section_title(_("Cost Centres"));
////
////		dimensions_list_row(_("Cost Centre")." 1", 'dimension_id', null, true, " ", false, 1);
////		if ($dim > 1)
////			dimensions_list_row(_("Cost Centre")." 2", 'dimension2_id', null, true, " ", false, 2);
////	}
////	if ($dim < 1)
////		hidden('dimension_id', 0);
////	if ($dim < 2)
////		hidden('dimension2_id', 0);
////
////	table_section_title(_("GL Accounts"));
////
////	gl_all_accounts_list_row(_("Sales Account:"), 'sales_account', $_POST['sales_account']);
////
////	if (!is_service($_POST['mb_flag']))
////	{
////		gl_all_accounts_list_row(_("Inventory Account:"), 'inventory_account', $_POST['inventory_account']);
////		gl_all_accounts_list_row(_("C.O.G.S. Account:"), 'cogs_account', $_POST['cogs_account']);
////		gl_all_accounts_list_row(_("Inventory Adjustments Account:"), 'adjustment_account', $_POST['adjustment_account']);
////	}
////	else
////	{
////		gl_all_accounts_list_row(_("C.O.G.S. Account:"), 'cogs_account', $_POST['cogs_account']);
////		hidden('inventory_account', $_POST['inventory_account']);
////		hidden('adjustment_account', $_POST['adjustment_account']);
////	}
////
////
////	if (is_manufactured($_POST['mb_flag']))
////		gl_all_accounts_list_row(_("Item Assembly Costs Account:"), 'assembly_account', $_POST['assembly_account']);
////	else
////		hidden('assembly_account', $_POST['assembly_account']);
////
////	table_section_title(_("Other"));
////
////	// Add image upload for New Item  - by Joe
////	file_row(_("Image File (.jpg)") . ":", 'pic', 'pic');
////	// Add Image upload for New Item  - by Joe
////	$stock_img_link = "";
////	$check_remove_image = false;
////	if (isset($_POST['NewStockID']) && file_exists(company_path().'/images/'
////		.item_img_name($_POST['NewStockID']).".jpg"))
////	{
////	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
////		$stock_img_link .= "<img id='item_img' alt = '[".$_POST['NewStockID'].".jpg".
////			"]' src='".company_path().'/images/'.item_img_name($_POST['NewStockID']).
////			".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";
////		$check_remove_image = true;
////	}
////	else
////	{
////		$stock_img_link .= _("No image");
////	}
////
////	label_row("&nbsp;", $stock_img_link);
////	if ($check_remove_image)
////		check_row(_("Delete Image:"), 'del_image');
////
////	record_status_list_row(_("Item status:"), 'inactive');
////	end_outer_table(1);
////
////	div_start('controls');
////	if (!isset($_POST['NewStockID']) || $new_item)
////	{
////		submit_center('addupdate', _("Insert New Item"), true, '', 'default');
////	}
////	else
////	{
////		submit_center_first('addupdate', _("Update Item"), '',
////			@$_REQUEST['popup'] ? true : 'default');
////		submit_return('select', get_post('stock_id'),
////			_("Select this items and return to document entry."), 'default');
////		submit('clone', _("Clone This Item"), true, '', true);
////		submit('delete', _("Delete This Item"), true, '', true);
////		submit_center_last('cancel', _("Cancel"), _("Cancel Edition"), 'cancel');
////	}
////
////	div_end();
//}

//==========================================================================================================
//function 	display_call_log(&$stock_id)
//{
//
//	start_outer_table(TABLESTYLE2);
//
//	start_table(TABLESTYLE_NOBORDER);
//	start_row();
//
//	function get_sql_for_call_log($from_date,$to_date,$debtor_no,$other_cust,$call_type,$contact_no,$remarks)
//	{
//		$start_date = date2sql($from_date);
//		$end_date = date2sql($to_date);
//
//		$sql = " SELECT ".TB_PREF."task.`id`,
//					".TB_PREF."task.`start_date`,
//					".TB_PREF."task.`end_date`,
//					".TB_PREF."task.Stamp,
//					".TB_PREF."debtors_master.debtor_ref,
//					".TB_PREF."task.`call_type`,
//	                ".TB_PREF."task.`contact_no`,
//	 				".TB_PREF."task.`other_cust`,
//	 				".TB_PREF."task.`remarks`,
//	 				".TB_PREF."task.`id` AS trans ,
//	 				".TB_PREF."task.inactive
//	 FROM `".TB_PREF."task`
//	INNER JOIN  ".TB_PREF."debtors_master ON
//	 ".TB_PREF."debtors_master.`debtor_no`=".TB_PREF."task.`debtor_no`
//	WHERE
//	".TB_PREF."task.`start_date`>='$start_date'
//	AND
//	".TB_PREF."task.`start_date`<='$end_date'
//	AND ".TB_PREF."task.`task_type` = 9
//	";
//
//
////	if ($status != '')
////	{
////
////		$sql .= " AND ".TB_PREF."task.status = ".db_escape($status);
////	}
////	if ($show_all != '')
////		$sql .= " AND ".TB_PREF."task.inactive IN (".db_escape(0).", ".db_escape(1).")";
////	else
////		$sql .= " AND ".TB_PREF."task.inactive = 0";
////
////	if ($users != '')
////	{
////
////		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($users);
////	}
////	if ($assign_by != '')
////	{
////
////		$sql .= " AND ".TB_PREF."task.assign_by = ".db_escape($assign_by);
////	}
//		if ($debtor_no != '')
//		{
//			$sql .= " AND ".TB_PREF."task.debtor_no = ".db_escape($debtor_no);
//		}
////	if ($description!= '')
////	{
////		$number_like = "%".$description."%";
////		$sql .= " AND ".TB_PREF."task.description LIKE ".db_escape($number_like);
////	}
//		if ($other_cust!= '')
//		{
//			$number_like = "%".$other_cust."%";
//			$sql .= " AND ".TB_PREF."task.other_cust LIKE ".db_escape($number_like);
//		}
//		if ($call_type != '')
//		{
//			$sql .= " AND ".TB_PREF."task.call_type = ".db_escape($call_type);
//		}
//		if ($contact_no!= '')
//		{
//			$number_like = "%".$contact_no."%";
//			$sql .= " AND ".TB_PREF."task.contact_no LIKE ".db_escape($number_like);
//		}
//		if ($remarks!= '')
//		{
//			$number_like = "%".$remarks."%";
//			$sql .= " AND ".TB_PREF."task.remarks LIKE ".db_escape($number_like);
//		}
//
////	if ($plan!= '')
////	{
////		$number_like = "%".$plan."%";
////		$sql .= " AND ".TB_PREF."task.plan LIKE ".db_escape($number_like);
////	}
////	if ($_SESSION["wa_current_user"]->access != 2)
////	{
////		$sql .= " AND ".TB_PREF."task.user_id = ".db_escape($_SESSION["wa_current_user"]->user);
////	}
////		$sql .= " ORDER BY id DESC";
////		return $sql;
////
//	}
////	$_SESSION['sql12'] = get_sql_for_call_log( $_POST['from_date'], $_POST['to_date'],
////		$_POST['debtor_no'],$_POST['other_cust_'],$_POST['call_type_'],  $_POST['contact_no_'],  $_POST['remarks_']);
////
////	$_SESSION['cols12'] = array(
////		array('insert'=>true, 'fun'=>'edit_link'),
////		_("#") => array('fun'=>'view_task_link'),
////		// _("X")=>array('insert'=>true, 'fun'=>'delete_checkbox'),
////		_("Start date")=> array( 'type'=>'date'),
////		_("End date")=> array( 'type'=>'date'),
////		_("Stamp")=> array(/*'fun'=>'get_format_date'*/),
////		_("Customers"),
////		_("Call Type") => array('fun'=>'get_call_type', 'align'=>'center'),
////		_("Contacat No")=> array(/*'insert'=>true, 'fun'=>'get_call_type', 'align'=>'center'*/) ,
////		_("Other Customers (For Sale)"),
////		_("Remarks") => array('fun'=>'update_remarks', 'align'=>'left'),
////		_("update")=>array('fun'=>'update_button'),
////	);
//
////------------------------------------------------------------------------------------------------
//
//
//}

start_table(TABLESTYLE_NOBORDER);
label_cell("<center> <a href=../inquiry/task_inquiry.php? style=\"color: #CC0000\">TASK INQUIRY</a> </center>");
label_cell("<center> <a href=../inquiry/call_log.php? style=\"color: #CC0000\">CALL LOG</a> </center>");

end_table();
//--------------------------------------------------------------------------------------------
start_form(true);

//if (db_has_stock_items())
//{
//	start_table(TABLESTYLE_NOBORDER);
//	start_row();
//
//	$new_item = get_post('stock_id') == '';
//	//check_cells(_("Show inactive:"), 'show_inactive', null, true);
//	end_row();
//	end_table();
//
////	if (get_post('_show_inactive_update')) {
////		$Ajax->activate('stock_id');
////		set_focus('stock_id');
////	}
//}
//else
//{
//	hidden('stock_id', get_post('stock_id'));
//}

div_start('details');

$stock_id = 1;

//tabbed_content_start('tabs', array(
//		'task_inquiry' => array(_('Task Inquiry'), $stock_id),
//		'call_log' => array(_('Call Log'), $stock_id),
//
//	)
//);
//$hello = 0;
//	switch (get_post('_tabs_sel'))
//	{
//		default:
//		case 'task_inquiry':
//			item_settings($stock_id);
//
//			$hello = 2;
//
//		break;
//
////		case 'call_log':
////			unset($_SESSION['sql1']);
////			unset($_SESSION['cols1']);
////			display_call_log($stock_id);
////			$hello = 3;
////
////			break;
//	case 'call_log':
//		$_GET['customer_id'] = $selected_id;
//		$_GET['popup'] = 1;
//		include_once($path_to_root."/project/inquiry/call_log.php");
//		break;
//	};
//
//br();
//tabbed_content_end();

//if($hello == 2)
//{
//	$table = & new_db_pager('trans_tbl', $_SESSION['sql1'], $_SESSION['cols1']);
//}
//elseif($hello == 3)
//{
//	$table = & new_db_pager('trans_tbl', $_SESSION['sql12'], $_SESSION['cols12'], null, null, 0, 1);
//}


//$table->set_marker('check_overdue', _(""));
$table->width = "85%";
display_db_pager($table);
div_end();


hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);

?>