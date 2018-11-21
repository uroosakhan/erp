<?php
//$page_security = 'SS_PAYROLL';
$page_security = 'SA_CUSTOMER';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
//include_once($path_to_root . "/includes/db/crm_contacts_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Employees"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 


$user_comp = user_company();


if (isset($_GET['employee_id'])) 
{
	$_POST['employee_id'] = $_GET['employee_id'];
}

$employee_id = get_post('employee_id','');

function can_process()
{
	$input_error = 0;
/*
      $chech=check_emp_code_duplication($_POST['emp_code']);
	
	if($chech>0)
	{
		$input_error = 1;
		display_error(_("Already Inserted"));
		set_focus('emp_code');
                return false;
		
	}
//*/
//	if (strlen($_POST['emp_code']) == 0 || $_POST['emp_code'] == "")
//	{
//		$input_error = 1;
//		display_error(_("The employee code must be entered."));
//		set_focus('emp_code');
//	}
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
	if ($_POST['emp_dept'] == "") 
	{
		$input_error = 1;
		display_error(_("The department must be selected."));
		set_focus('emp_dept');
	}

	if ($_POST['emp_gen'] == "") 
	{
		$input_error = 1;
		display_error(_("The gender must be selected."));
		set_focus('emp_gen');
	}
//	if ($_POST['emp_grade'] == "")
//	{
//		$input_error = 1;
//		display_error(_("The grade must be selected."));
//		set_focus('emp_grade');
//	}

		if (strlen($_POST['emp_cnic']) == 0 || $_POST['emp_cnic'] == "") 
	{
		$input_error = 1;
		display_error(_("The employee CNIC must be entered."));
		set_focus('emp_cnic');
	}
	
	
	if ($upload_file == 'No')
	{
	display_error(_("Upload File false."));
	return false;	
	}

	return true;
}


function handle_submit($employee_id)
{


	if (!can_process())
		return;
	
	if (check_value('del_image'))
	{
			$filename = company_path().'/images/'.trim($employee_id).".jpg";
			if (file_exists($filename))
				unlink($filename);
	}	
		
	if ($employee_id) 
	{
	 	update_employee($_POST['employee_id'], $_POST['emp_code'], $_POST['emp_name'], 
			$_POST['emp_father'],
			$_POST['emp_cnic'], $_POST['DOB'], $_POST['j_date'] , $_POST['l_date'] ,
		    $_POST['emp_reference'], $_POST['emp_home_phone'], 
			$_POST['emp_mobile'], $_POST['emp_email'],  $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'], 
			$_POST['prev_salary'], $_POST['duty_hours'],  $_POST['social_sec'],  $_POST['emp_ntn'],  $_POST['emp_eobi'], 
			$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
			$_POST['emp_gen'],$_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade'],
            $_POST['cpf'], $_POST['employer_cpf'], $_POST['inactive'],$_POST['division'],$_POST['project']
        ,$_POST['age'],$_POST['report'],$_POST['location'],$_POST['vehicle'],$_POST['status'],$_POST['tax_deduction'],
         $_POST['applicable'],$_POST['leave_applicable'], $_POST['sessi_applicable']
            ,$_POST['eobi_applicable'],$_POST['mb_flag'],$_POST['active_filer'],$_POST['bank_name'],$_POST['bank_branch'],$_POST['salary'],
            $_POST['cnic_expiry_date'],$_POST['pec_no'],$_POST['pec_expiry_date'],$_POST['license_no'],
            $_POST['license_expiry_date'],$_POST['text_filer'],$_POST['text_non_filer']);


        display_notification(_("Employee has been updated."));
		$Ajax->activate('employee_id'); // in case of status change
			
	} 
	else
	{ 	//it is a new customer

		 begin_transaction();
		add_employee( $_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'],
			$_POST['emp_cnic'], $_POST['DOB'], $_POST['j_date'] , $_POST['l_date'] ,
		    $_POST['emp_reference'], $_POST['emp_home_phone'], 
			$_POST['emp_mobile'], $_POST['emp_email'],  $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'], 
			$_POST['prev_salary'], $_POST['duty_hours'],  $_POST['social_sec'],  $_POST['emp_ntn'],  $_POST['emp_eobi'], 			
			$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
			$_POST['emp_gen'],$_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade'], $_POST['cpf'], $_POST['employer_cpf'],$_POST['inactive']
            ,$_POST['division'],$_POST['project']
            ,$_POST['age'],$_POST['report'],$_POST['location'],$_POST['vehicle'],$_POST['status'],$_POST['tax_deduction'],
            $_POST['applicable'],$_POST['leave_applicable'], $_POST['sessi_applicable']
            ,$_POST['eobi_applicable'],$_POST['mb_flag'],$_POST['active_filer'],$_POST['bank_name'],$_POST['bank_branch'],$_POST['salary'],
            $_POST['cnic_expiry_date'],$_POST['pec_no'],$_POST['pec_expiry_date'],$_POST['license_no'],
            $_POST['license_expiry_date'],$_POST['text_filer'],$_POST['text_non_filer']
        );
		commit_transaction();
		display_notification(_("A new employee has been added."));
			$Ajax->activate('_page_body');
		
	}
}

//------------------------------------------------------------------------------------
//Customer Image
$upload_file = "";

if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') 
{
	$employee_id = $_POST['employee_id'];
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes'; //Assume all is well to start off with
	$filename = company_path().'/images/';
	if (!file_exists($filename))
	{
		mkdir($filename);
	}	
	$filename .= "/".trim($employee_id).".jpg";
	if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
		$imagetype = $type;
	else
		$imagetype = false;
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

if (isset($_POST['addupdate'])) 
{

	handle_submit($employee_id);
	
}
if (isset($_POST['delete'])) 
{
	$cancel_delete = 0;
		if (key_in_foreign_table($employee_id, 'payroll', 'emp_id'))
	{
		$cancel_delete = 1;
		display_error(_("This Employee cannot be deleted because there are transactions that refer to it."));
               // update_employee_inactive($employee_id);
	} 
	
	
	if ($cancel_delete == 0) 
	{
	delete_employee($_POST['employee_id']);

		unset($_SESSION['employee_id']);
		$employee_id = '';
		$Ajax->activate('_page_body');
	 //end 
		
		$filename = company_path().'/images/'.trim($employee_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		//display_notification(_("Selected Employee has been deleted."));
		unset($_POST['employee_id']);
		$employee_id = '';
		$Ajax->activate('_page_body');
	}

}


function supplier_settings($employee_id) 
{
	global $pic_height;
	if ($employee_id) 
	{
		$myrow = get_employee($_POST['employee_id']);

		$_POST['emp_code'] = $myrow["emp_code"];
		$_POST['emp_name'] = $myrow["emp_name"];
		$_POST['emp_father']  = $myrow["emp_father"];
		$_POST['emp_cnic']  = $myrow["emp_cnic"];
	    $_POST['DOB'] = sql2date($myrow["DOB"]);
	    $_POST['j_date'] = sql2date($myrow["j_date"]);
		$_POST['l_date'] = sql2date($myrow["l_date"]);
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
		$_POST['cpf']  = $myrow["cpf"];
		$_POST['employer_cpf']= $myrow["employer_cpf"];
	 	$_POST['inactive'] = $myrow["inactive"];
        $_POST['division']= $myrow["division"];
        $_POST['project']= $myrow["project"];
        $_POST['age']= $myrow["age"];
        $_POST['report']= $myrow["report"];
        $_POST['location']= $myrow["location"];
        $_POST['vehicle']= $myrow["vehicle"];
        $_POST['status']= $myrow["status"];

        $_POST['tax_deduction']= $myrow["tax_deduction"];
        $_POST['applicable']= $myrow["applicable"];
        $_POST['leave_applicable']= $myrow["leave_applicable"];
        $_POST['sessi_applicable']= $myrow["sessi_applicable"];
        $_POST['eobi_applicable']= $myrow["eobi_applicable"];
        $_POST['mb_flag']= $myrow["mb_flag"];
        $_POST['active_filer']= $myrow["active_filer"];
        $_POST['bank_name']= $myrow["bank_name"];
        $_POST['bank_branch']= $myrow["bank_branch"];
        $_POST['salary']= $myrow["salary"];
        $_POST['cnic_expiry_date']= $myrow["cnic_expiry_date"];
        $_POST['pec_no']= $myrow["pec_no"];
        $_POST['pec_expiry_date']= $myrow["pec_expiry_date"];
        $_POST['license_no']= $myrow["license_no"];
        $_POST['license_expiry_date']= $myrow["license_expiry_date"];
        $_POST['text_filer']= $myrow["text_filer"];
        $_POST['text_non_filer']= $myrow["text_non_filer"];

	} 
	else 
	{
		$_POST['emp_code']  = $_POST['emp_name'] = $_POST['emp_father'] =
	    $_POST['emp_cnic'] = $_POST['DOB']=$_POST['j_date'] = $_POST['l_date']=
		$_POST['emp_reference'] = $_POST['emp_home_phone'] = $_POST['emp_mobile'] = 
		$_POST['emp_email']= $_POST['emp_bank'] = $_POST['emp_bank'] = $_POST['basic_salary'] = $_POST['prev_salary'] = $_POST['duty_hours']  = $_POST['social_sec']  = $_POST['emp_ntn']  =
		$_POST['emp_eobi']  = $_POST['emp_address'] = $_POST['notes'] = 
		$_POST['emp_title'] =$_POST['emp_gen'] = $_POST['emp_dept']  = 
		$_POST['emp_desig']  = $_POST['emp_grade']   = $_POST['cpf']   =  $_POST['employer_cpf']   = 
'';}

	start_outer_table(TABLESTYLE2);

	table_section(1);
	
		table_section_title(_("Basic Data"));

	if ($employee_id) 	
	{

        label_row(_("Code:"), $_POST['emp_code']);
        hidden('emp_code', $_POST['emp_code']);
	}
	else
	{
        $emp_ref = get_next(ST_EMPLOYEECODE);
        text_row(_("*Code:"),'emp_code', $emp_ref);


	}
	
//	if ($employee_id && !is_new_employee($employee_id)) 
//	{
//		label_row(_("Title:"), $_POST['emp_title']);
//		hidden('emp_title', $_POST['emp_title']);
//	} 
//	else 
//	{
		emp_title_row(_("Title:"), 'emp_title', null,true);
//	}
    $dim = get_company_pref('use_dimension');


    text_row(_("*Employee Full Name:"), 'emp_name', null, 42, 40);
	text_row(_("*Employee's Father Name:"), 'emp_father', null, 42, 40);
    divison_list_row(_("Divison"),'divison',null,false);
    project_list_row(_("Project"),'project',null,false);
	text_row(_("Age"), 'age', null, 10, 40);
	text_row(_("Report To"), 'report', null, 20, 40);
    number_list_row(_("Employee Location"), 'location', null, 1, $dim);
    yesno_list_row(_("Vehicle Provided To Employee:"), 'vehicle');
    marital_list_row(_("Marital Status"), 'status');


    yesno_list_row(_("Income Tax Deduction:"), 'tax_deduction');
    yesno_list_row(_("Grautuity applicable:"), 'applicable');
    yesno_list_row(_("Leave encashment applicable:"), 'leave_applicable');
    yesno_list_row(_("Sessi applicable:"), 'sessi_applicable');
    yesno_list_row(_("EOBI applicable:"), 'eobi_applicable');


//	if ($employee_id && !is_new_employee($employee_id)) 
//	{
//		label_row(_("Gender:"), $_POST['emp_gen']);
//		hidden('emp_gen', $_POST['emp_gen']);
//	} 
//	else 
//	{
		emp_gen_row(_("*Gender:"), 'emp_gen', null,true);
//	}
	
	date_row(_("Date of Birth"),'DOB', null,null, 0, 0, 0, null, true); 
    date_row(_("Date of joining"),'j_date', null,null, 0, 0, 0, null, true);
	date_row(_("Date of leaving"),'l_date', null,null, 0, 0, 0, null, true);  
			
	text_row(_("Reference:"), 'emp_reference', null, 42, 40);
	text_row(_("Home Phone:"), 'emp_home_phone', null, 13, 13);
	text_row(_("Mobile:"), 'emp_mobile', null, 13, 13);		
	text_row(_("Email:"), 'emp_email', null, 42, 40);
    employe_types_list_row(_("Employee Type:"), 'mb_flag', null, true);
    emp_dept_row(_("*Department:"), 'emp_dept', null,true);
    emp_desg_row(_("*Designation:"), 'emp_desig', null,true);

    table_section_title(_("Income Tax Status"));
    check_row(_("Text Filer:"), 'text_filer', null, true);
    check_row(_("Text Non Filer:"), 'text_non_filer', null, true);
//	if ($employee_id && !is_new_employee($employee_id))
//	{
//		label_row(_("Department:"), $_POST['emp_dept']);
//		hidden('emp_dept', $_POST['emp_dept']);
//	} 
//	else 
//	{
//	}
	
//	if ($employee_id && !is_new_employee($employee_id)) 
//	{
//		label_row(_("Designation:"), $_POST['emp_desig']);
//		hidden('emp_desig', $_POST['emp_desig']);
//	} 
//	else 
//	{
//	}
	 
//	 if ($employee_id && !is_new_employee($employee_id)) 
//	{
//		label_row(_("Grade:"), $_POST['emp_grade']);
//		hidden('emp_grade', $_POST['emp_grade']);
//	} 
//	else 
//	{
		//emp_grade_row(_("*Grade:"), 'emp_grade', null,true);
		if($employee_id)
		{
		record_status_list_row(_("Employee status:"), 'inactive');
		}
		
//	}


	table_section(2);
	table_section_title(_("Salary Details"));	
	text_row(_("Employee Bank A/C No.:"), 'emp_bank', null, 20, 20);
	text_row(_("Employee Bank Name.:"), 'bank_name', null, 20, 20);
	text_row(_("Employee Bank Branch:"), 'bank_branch', null, 20, 20);
    salary_list_row(_("Mode Of Salary Payment"),'salary',null,false);
    bank_accounts_list_row(_("Company Bank:"), 'company_bank');
	text_cells(_("Initial Salary:"), 'basic_salary', null, 10, 10);
   // if ($employee_id)  {
        start_row();
      //  echo '<td class="label">'._('Click here to').'</td>';
//        hyperlink_params_separate_td($path_to_root . "/emp2/reporting/prn_redirect.php?PARAM_0=$employee_id&REP_ID=1033",
//            '<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Print")).'</b>');
//        end_row();
//    }
    //ansar
    /*if ($employee_id)  {
        start_row();
        echo '<td class="label">'._(' Employee Allowance').'</td>';
          hyperlink_params_separate_td($path_to_root . "/payroll_ktc/payroll/payroll/manage/emp_allowance.php?emp_id=$employee_id",
            '<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Add Allowance")).'</b>');
        end_row();
    }
        if ($employee_id)  {
        start_row();
        echo '<td class="label">'._(' Employee Deduction').'</td>';
          hyperlink_params_separate_td($path_to_root . "/payroll_ktc/payroll/payroll/manage/emp_deduction.php?emp_id=$employee_id",
            '<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Add Deduction")).'</b>');
        end_row();
    }*/
    if ($employee_id)  {
        employee_emp_allowance_cell1($employee_id,'Add Allowance');

    }
    text_row(_("Previous Salary:"), 'prev_salary', null, 10, 10);
	text_row(_("Duty Hours:"), 'duty_hours', null, 10, 10);

    if ($employee_id)  {

        employee_man_deduction_cell($employee_id,'Add Man Month');
        employee_emp_deduction_cell1($employee_id,'Add Deduction');

    }
	table_section_title(_("Identification Details"));
	text_row(_("*CNIC:"), 'emp_cnic', null, 13, 13);
    date_row(_("CNIC Expiry Date:"), 'cnic_expiry_date', null, 13, 13);
    text_row(_("*PEC No:"), 'pec_no', null, 13, 13);
    date_row(_("PEC Expiry Date:"), 'pec_expiry_date', null, 13, 13);

    text_row(_("Social Security:"), 'social_sec', null, 13, 13);
	text_row(_("NTN:"), 'emp_ntn', null, 8, 8);		
	text_row(_("EOBI No:"), 'emp_eobi', null, 13, 13);	
	text_row(_("Employee CPF:"), 'cpf', null, 13, 13);			
	text_row(_("Employer CPF:"), 'employer_cpf', null, 13, 13);
	text_row(_("License No"), 'license_no', null, 13, 13);
	date_row(_("License Expiry Date:"), 'license_expiry_date', null, 13, 13);





    table_section_title(_("Addresses"));
	textarea_row(_("Physical Address:"), 'emp_address', null, 35, 5);

	table_section_title(_("General"));
	textarea_row(_("General Notes:"), 'notes', null, 35, 5);


	table_section_title(_("Others"));
	//------------------------------------------------------------------------------------
	//if($employee_id)
	{
	file_row(_("Employee Image (.jpg)") . ":", 'pic', 'pic');
	}// Add Image upload for New Item  - by Joe
	$stock_img_link = "";
	$check_remove_image = false;
	
	if (isset($_POST['employee_id']) && file_exists(company_path().'/images/'
		.trim($_POST['employee_id']).".jpg"))
	{
	$stock_thumb_link .= "<img id='item_img' alt = '[".$_POST['employee_id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_POST['employee_id']).
			".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";	
	}	 
	
	
	if (isset($_POST['employee_id']) && file_exists(company_path().'/images/'
		.trim($_POST['employee_id']).".jpg")) 
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		
		$stock_img_link .= "<img id='item_img' alt = '[".$_POST['employee_id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_POST['employee_id']).
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

	div_start('controls');
	if (!$employee_id)
	{
		submit_center('addupdate', _("Add New Employee"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('addupdate', _("Update Employee"), 
		  _('Update Employee data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $employee_id, _("Select this Employee and return to document entry."));
		submit_center_last('delete', _("Delete Employee"), 
		  _('Delete Employee data if have been never used'), true);
	}
	

	div_end();
}

//--------------------------------------------------------------------------------------------
start_form(true);

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
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
	hidden('employee_id');
}



div_start('details');

//if (!$employee_id)
	//unset($_POST['_tabs_sel']); // force settings tab for new customer


	
	
//supplier_settings($employee_id); 

//br();
//tabbed_content_end();
if (!$employee_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer

tabbed_content_start('tabs', array(
		'settings' => array(_('&General settings'), $employee_id),
		//'contacts' => array(_('&Contacts'), $employee_id),
		//'transactions' => array(_('&Transactions'), $employee_id),
		//'orders' => array(_('Purchase &Orders'), $employee_id),
	));
	
	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			supplier_settings($employee_id); 
			break;
		case 'contacts':
			$contacts = new contacts('contacts', $employee_id, 'employee');
			$contacts->show();
			break;
		case 'transactions':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/allowance.php?");
			break;
		case 'orders':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/purchasing/inquiry/po_search_completed.php");
			break;
	};
br();
tabbed_content_end();

div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);
?>