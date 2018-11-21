<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
if ($use_date_picker)
	$js .= get_js_date_picker();
//page(_($help_context = "Grade"));
page(_($help_context = "Employee Document"), false, false, "", $js);
include($path_to_root . "/payroll/includes/db/employee_document_db.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 
//for view and download
/*if (isset($_GET['vw']))
	$view_id = $_GET['vw'];
else
	$view_id = find_submit('view');
if ($view_id != -1)
{
	$row = get_employee_docs($view_id);
	if ($row['filename'] != "")
	{
		if(in_ajax()) {
			$Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
		} else {
			$type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';	
    		header("Content-type: ".$type);
    		header('Content-Length: '.$row['filesize']);
	    	//if ($type == 'application/octet-stream')
    		//	header('Content-Disposition: attachment; filename='.$row['filename']);
    		//else
	 			header("Content-Disposition: inline");
	    	echo file_get_contents(company_path(). "/attachments/".$row['unique_name']);
    		exit();
		}
	}	
}*/
$stock_img_link = "";
	$check_remove_image = false;
if (isset($_GET['id']) && file_exists(company_path().'/images/'
		.trim($_GET['id']).".jpg")) 
	{
		$stock_img_link .= "<img id='item_img' alt = '[".$_GET['id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_GET['id']).
			".jpg?nocache=".rand()."'"." height='300' border='0'>";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= _("No image");
	}
	
if (isset($_GET['dl']))
	$download_id = $_GET['dl'];
else
	$download_id = find_submit('download');

if ($download_id != -1)
{
	//$row = get_employee_docs($download_id);
	if ($row['id'] != "")
	{
		if(in_ajax()) {
			$Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
		} else {
			//$type = ($row['id']) ? $row['id'] : 'application/octet-stream';	
    		//header("Content-type: ".$type);
	    	//header('Content-Length: '.$row['filesize']);
    		//header('Content-Disposition: attachment; filename='.$row['filename']);
    		echo file_get_contents(company_path()."/images/".$_GET['id'].".jpg");
	    	exit();
		}
	}	
}

//$js = "";
//if ($use_popup_windows)
	//$js .= get_js_open_window(800, 500);

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

 $input_error = 0;
//for image
//$description =  str_replace(" ", "", $_POST['description']);

//
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		update_employee_doc($selected_id, $_POST['employee_id'],$_POST['document_type'],$_POST['document_no'],$_POST['expiry_date'],$_POST['remarks']);
			$note = _('Selected Employee Document Type has been updated');
    	} 
    	else 
    	{
    		add_employee_doc($_POST['employee_id'],$_POST['document_type'],$_POST['document_no'],$_POST['expiry_date'],$_POST['remarks']);
			$note = _('New Employee Document Type has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this group because customers have been created using this group."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_employee_doc($selected_id);
		display_notification(_('Selected Employee Document Type has been deleted'));
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



///=======================================================
$result = get_employee_doc(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=80%");
$th = array(_("Employee ID"), _("Employee Name"), _("Document Category"), _("Expiry Date"),"Image","Edit", "Delete");

inactive_control_column($th);

table_header($th);
$k = 0; 
while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["employee_id"]);
	$id=$myrow["id"];
	label_cell(get_employee_doc_name($myrow["employee_id"]));
	label_cell(get_employee_document_type($myrow["document_type"]));
	//label_cell($myrow["document_no"]);
	label_cell($myrow["expiry_date"]);
	label_cell("<a href='cust_pic_upload.php?id=$id'>Upload Image</a>");
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'employee_doc', 'id');
	//submenu_option(_("Enter New Commision"), "/payroll/manage/cust_pic_upload.php?InvoiceNumber=$id");
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
    delete_button_cell("Delete".$myrow["id"], _("Delete"));
	button_cell('download'.$myrow["id"], _("Download"), _("Download"), ICON_DOWN);
	end_row();
}

inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_employee_docs($selected_id);

		$_POST['employee_id']  = $myrow["employee_id"];
		$_POST['document_type']  = $myrow["document_type"];
		$_POST['document_no']  = $myrow["document_no"];
		$_POST['expiry_date']  = $myrow["expiry_date"];
		$_POST['remarks']  = $myrow["remarks"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
	
} 
employee_list2_cells(_("Select a Employee:"), 'employee_id', null, true, false, false);
text_row_ex(_("Document :"), 'document_no', 30); 
date_cells(_("Expiry Date:"), 'expiry_date', '', null, 0, 0, -5);
start_row();
emp_document_type_cells(_("Select Document:"), 'document_type', null, true, false, false);
end_row();
text_row_ex(_("Remarks :"), 'remarks', 40);
//header("Location:cust_pic_upload.php");
echo $id;
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>