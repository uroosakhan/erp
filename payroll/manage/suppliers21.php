<?php
$page_security = 'SA_SUPPLIER';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");


check_db_has_tax_groups(_("There are no tax groups defined in the system. At least one tax group is required before proceeding."));

if (isset($_GET['employee_id'])) 
{
	$_POST['employee_id'] = $_GET['employee_id'];
}

$employee_id = get_post('employee_id'); 

if (list_updated('employee_id')) {
	$_POST['NewEmployeeID'] = $employee_id = get_post('employee_id');
    clear_data();
	$Ajax->activate('details');
	$Ajax->activate('controls');
}

if (get_post('cancel')) {
	$_POST['NewEmployeeID'] = $employee_id = $_POST['employee_id'] = '';
    clear_data();
	set_focus('employee_id');
	$Ajax->activate('_page_body');
}
$upload_file = "";
if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') 
{
	$employee_id = $_POST['NewEmployeeID'];
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes'; //Assume all is well to start off with
	$filename = company_path().'/images';
	if (!file_exists($filename))
	{
		mkdir($filename);
	}	
	$filename .= "/".item_img_name($employee_id).".jpg";
	
	//But check for the worst 
	if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
		$imagetype = $type;
	else
		$imagetype = false;
	//$imagetype = exif_imagetype($_FILES['pic']['tmp_name']);
	if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG)
	{	//File type Check
		display_warning( _('Only graphics files can be uploaded'));
		$upload_file ='No';
	}	
	elseif (@strtoupper(substr(trim($_FILES['pic']['name']), @in_array(strlen($_FILES['pic']['name']) - 3)), array('JPG','PNG','GIF')))
	{
		display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
		$upload_file ='No';
	} 
	elseif ( $_FILES['pic']['size'] > ($max_image_size * 1024)) 
	{ //File Size Check
		display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $max_image_size);
		$upload_file ='No';
	} 
	elseif (file_exists($filename))
	{
		$result = unlink($filename);
		if (!$result) 
		{
			display_error(_('The existing image could not be removed'));
			$upload_file ='No';
		}
	}
	
	if ($upload_file == 'Yes')
	{
		$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
	}
	$Ajax->activate('details');
 /* EOF Add Image upload for New Item  - by Ori */
}







//--------------------------------------------------------------------------------------------
function supplier_settings(&$employee_id)
{

	start_outer_table(TABLESTYLE2);

	table_section(1);

	if ($employee_id) 
	{
		//SupplierID exists - either passed when calling the form or from the form itself
//		$myrow = get_employee($_POST['supplier_id']);
		$myrow = get_employee($_POST['employee_id']);

		$_POST['emp_code'] = $myrow["emp_code"];
		$_POST['emp_name'] = $myrow["emp_name"];
		$_POST['emp_father']  = $myrow["emp_father"];
		$_POST['emp_cnic']  = $myrow["emp_cnic"];
	    $_POST['DOB'] = $myrow["DOB"];
	    $_POST['j_date'] = $myrow["j_date"];
		$_POST['l_date'] = $myrow["l_date"];
		$_POST['emp_reference']  = $myrow["emp_reference"];
		$_POST['emp_home_phone']  = $myrow["emp_home_phone"];
		$_POST['emp_mobile']  = $myrow["emp_mobile"];
		$_POST['emp_email']  = $myrow["emp_email"];
		$_POST['emp_bank'] = $myrow["emp_bank"];
		$_POST['company_bank'] = $myrow["company_bank"];		
		$_POST['basic_salary'] = $myrow["basic_salary"];	
		$_POST['prev_salary'] = $myrow["prev_salary"];	
		$_POST['duty_hours'] = $myrow["duty_hours"];
		$_POST['social_sec'] = $myrow["social_sec"];	
		$_POST['emp_ntn'] = $myrow["emp_ntn"];	
		$_POST['emp_eobi'] = $myrow["emp_eobi"];										
		$_POST['emp_address']  = $myrow["emp_address"];
		$_POST['notes']  = $myrow["notes"];
		$_POST['emp_title'] = $myrow["emp_title"];
		$_POST['emp_gen']=$myrow['emp_gen'];
		$_POST['emp_dept']  = $myrow["emp_dept"];
		$_POST['emp_desig']  = $myrow["emp_desig"];
		$_POST['emp_grade']  = $myrow["emp_grade"];
	 	$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
		$_POST['emp_code']  = $_POST['emp_name'] = $_POST['emp_father'] =
	    $_POST['emp_cnic'] = $_POST['DOB']=$_POST['j_date'] = $_POST['l_date']=
		$_POST['emp_reference'] = $_POST['emp_home_phone'] = $_POST['emp_mobile'] = 
		$_POST['emp_email']= $_POST['emp_bank'] = $_POST['emp_bank'] = $_POST['basic_salary'] = $_POST['prev_salary'] = $_POST['duty_hours']  = $_POST['social_sec']  = $_POST['emp_ntn']  =
		$_POST['emp_eobi']  = $_POST['emp_address'] = $_POST['notes'] = 
		$_POST['emp_title'] =$_POST['emp_gen'] = $_POST['emp_dept']  = 
		$_POST['emp_desig']  = $_POST['emp_grade']   = 
'';}


	table_section_title(_("Basic Data"));
	
	text_row(_("*Code:"), 'emp_code', null, 13, 13);
	
	if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Title:"), $_POST['emp_title']);
		hidden('emp_title', $_POST['emp_title']);
	} 
	else 
	{
		emp_title_row(_("Title:"), 'emp_title', null,true);
	}
	
	text_row(_("*Employee Full Name:"), 'emp_name', null, 42, 40);
	text_row(_("*Employee's Father Name:"), 'emp_father', null, 42, 40);
	
	if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Gender:"), $_POST['emp_gen']);
		hidden('emp_gen', $_POST['emp_gen']);
	} 
	else 
	{
		emp_gen_row(_("Gender:"), 'emp_gen', null,true);
	}
	
	date_row(_("Date of Birth"),'DOB', null,null, 0, 0, 0, null, true); 
    date_row(_("Date of joining"),'j_date', null,null, 0, 0, 0, null, true);
	date_row(_("Date of leaving"),'l_date', null,null, 0, 0, 0, null, true);  
			
	text_row(_("Reference:"), 'emp_reference', null, 42, 40);
	text_row(_("Home Phone:"), 'emp_home_phone', null, 13, 13);
	text_row(_("Mobile:"), 'emp_mobile', null, 13, 13);		
	text_row(_("Email:"), 'emp_email', null, 42, 40);
	
	if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Department:"), $_POST['emp_dept']);
		hidden('emp_dept', $_POST['emp_dept']);
	} 
	else 
	{
		emp_dept_row(_("Department:"), 'emp_dept', null,true);
	}	
	
	if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Designation:"), $_POST['emp_desig']);
		hidden('emp_desig', $_POST['emp_desig']);
	} 
	else 
	{
		emp_desg_row(_("Designation:"), 'emp_desig', null,true);
	}
	 
	 if ($employee_id && !is_new_employee($employee_id)) 
	{
		label_row(_("Grade:"), $_POST['emp_grade']);
		hidden('emp_grade', $_POST['emp_grade']);
	} 
	else 
	{
		emp_grade_row(_("Grade:"), 'emp_grade', null,true);
	}

	table_section(2);
	table_section_title(_("Salary Details"));	
	text_row(_("Employee Bank A/C No.:"), 'emp_bank', null, 20, 20);		
	bank_accounts_list_row(_("Company Bank:"), 'company_bank');
	text_row(_("Basic Salary:"), 'basic_salary', null, 10, 10);
	text_row(_("Previous Salary:"), 'prev_salary', null, 10, 10);
	text_row(_("Duty Hours:"), 'duty_hours', null, 10, 10);
//	amount_row(_("Basic Salary:"), 'basic_salary');	
//	amount_row(_("Previous Salary:"), 'prev_salary');		

	table_section_title(_("Identification Details"));
	text_row(_("*CNIC:"), 'emp_cnic', null, 13, 13);
	text_row(_("Social Security:"), 'social_sec', null, 13, 13);		
	text_row(_("NTN:"), 'emp_ntn', null, 8, 8);		
	text_row(_("EOBI No:"), 'emp_eobi', null, 13, 13);				
	
	table_section_title(_("Addresses"));
	textarea_row(_("Physical Address:"), 'emp_address', null, 35, 5);

	table_section_title(_("General"));
	textarea_row(_("General Notes:"), 'notes', null, 35, 5);
	
	
	
	
	

	if ($employee_id)  {
		start_row();
		echo '<td class="label">'._('Click here to').'</td>';
	  	hyperlink_params_separate_td($path_to_root . "/emp2/reporting/prn_redirect.php?PARAM_0=$employee_id&REP_ID=1033",
			'<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Print")).'</b>');
		end_row();
	}
table_section_title(_("Other"));

	// Add image upload for New Item  - by Joe
	file_row(_("Image File (.jpg)") . ":", 'pic', 'pic');
	// Add Image upload for New Item  - by Joe
	$stock_img_link = "";
	$check_remove_image = false;
	if (isset($_POST['NewEmployeeID']) && file_exists(company_path().'/images/'
		.item_img_name1($_POST['NewEmployeeID']).".jpg")) 
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		$stock_img_link .= "<img id='item_img' alt = '[".$_POST['NewEmployeeID'].".jpg".
			"]' src='".company_path().'/images/'.item_img_name1($_POST['NewEmployeeID']).
			".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= _("No image");
	}

	label_row("&nbsp;", $stock_img_link);
	if ($check_remove_image)
		check_row(_("Delete Image:"), 'del_image');

	
	end_outer_table(1);
	
	
//http://hisaab.pk/employee/reporting/prn_redirect.php?PARAM_0=298-13&PARAM_1=298-13&PARAM_2=0&PARAM_3=0&PARAM_4=&PARAM_5=0&REP_ID=110
//, "PARAM_0=".$employee_id.(@$_REQUEST['popup'] ? '&popup=1':'')
	end_outer_table(1);

	div_start('controls');
	if ($employee_id) 
	{
		submit_center_first('submit', _("Update Employee"), 
		  _('Update Employee data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', get_post('employee_id'), _("Select this employee and return to document entry."));
		submit_center_last('delete', _("Delete Employee"), 
		  _('Delete employee data if have been never used'), true);
	}
	else 
	{
		submit_center('submit', _("Add New Employee Details"), true, '', 'default');
	}
	div_end();
}

if (isset($_POST['submit'])) 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	/* actions to take once the user has clicked the submit button
	ie the page has called itself with some user input */

	//first off validate inputs sensible

	if (strlen($_POST['emp_code']) == 0 || $_POST['emp_code'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee code must be entered."));
		set_focus('emp_code');
	}
	if (strlen($_POST['emp_name']) == 0 || $_POST['emp_name'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee name must be entered."));
		set_focus('emp_name');
	}
		if (strlen($_POST['emp_father']) == 0 || $_POST['emp_father'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee father name must be entered."));
		set_focus('emp_father');
	}
		if (strlen($_POST['emp_cnic']) == 0 || $_POST['emp_cnic'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee CNIC must be entered."));
		set_focus('emp_cnic');
	}
/*
		if (strlen($_POST['emp_dept']) == 0 || $_POST['emp_dept'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee depertment must be entered."));
		set_focus('emp_dept');
	}
		if (strlen($_POST['emp_desig']) == 0 || $_POST['emp_desig'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee designation must be entered."));
		set_focus('emp_desig');
	}
*/	
	if ($input_error !=1 )
	{

		begin_transaction();
		if ($employee_id) 
		{
			update_employee($_POST['employee_id'], $_POST['emp_code'], $_POST['emp_name'], 
			$_POST['emp_father'],
			$_POST['emp_cnic'], $_POST['DOB'], $_POST['j_date'] , $_POST['l_date'] ,
		    $_POST['emp_reference'], $_POST['emp_home_phone'], 
			$_POST['emp_mobile'], $_POST['emp_email'],  $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'], 
			$_POST['prev_salary'], $_POST['duty_hours'],  $_POST['social_sec'],  $_POST['emp_ntn'],  $_POST['emp_eobi'], 
			$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
			$_POST['emp_gen'],$_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade']);

			$Ajax->activate('employee_id'); // in case of status change
			display_notification(_("Employee has been updated."));
		} 
		else 
		{
			add_employee( $_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'],
			$_POST['emp_cnic'], $_POST['DOB'], $_POST['j_date'] , $_POST['l_date'] ,
		    $_POST['emp_reference'], $_POST['emp_home_phone'], 
			$_POST['emp_mobile'], $_POST['emp_email'],  $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'], 
			$_POST['prev_salary'], $_POST['duty_hours'],  $_POST['social_sec'],  $_POST['emp_ntn'],  $_POST['emp_eobi'], 			
			$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
			$_POST['emp_gen'],$_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade']);

	
			display_notification(_("A new employee has been added."));
			$Ajax->activate('_page_body');
		}
		commit_transaction();
	}

} 
elseif (isset($_POST['delete']) && $_POST['delete'] != "") 
{
	//the link to delete a selected record was clicked instead of the submit button

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'supp_trans' , purch_orders

	
	if ($cancel_delete == 0) 
	{
		delete_employee($_POST['employee_id']);

		unset($_SESSION['employee_id']);
		$employee_id = '';
		$Ajax->activate('_page_body');
	} //end if Delete supplier
}

start_form();

if (db_has_suppliers()) 
{
	start_table(false, "", 3);
//	start_table(TABLESTYLE_NOBORDER);
	start_row();
	  
		  employee_list_cells(_("Select a employee: "), 'employee_id', null,
		  _('New employee'), true, check_value('show_inactive')); 


	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('employee_id');
		set_focus('employee_id');
	}
} 
else 
{
	hidden('employee_id', get_post('employee_id'));
}

if (!$employee_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
		'settings' => array(_('&General'), $employee_id),
		//'contacts' => array(_('&Contacts'), $contacts),
		//'transactions' => array(_('&Transactions'), $supplier_id),
		//'orders' => array(_('Purchase &Orders'), $supplier_id),
	));
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			supplier_settings($employee_id); 
			break;
		case 'printcard':
			//$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/reporting/reports_main.php?Class=0&REP_ID=1033");
			break;

		case 'transactions':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/supplier_inquiry.php");
			break;
		case 'orders':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/po_search_completed.php");
			break;
	};
br();
tabbed_content_end();
hidden('popup', @$_REQUEST['popup']);
end_form();

end_page(@$_REQUEST['popup']);

?>