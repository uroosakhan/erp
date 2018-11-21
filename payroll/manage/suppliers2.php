<?php
//$page_security = 'SS_PAYROLL';
$page_security = 'SA_HUMAIN_EMP_SETUP';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/payroll/includes/db/gl_setup_db.inc");


//include_once($path_to_root . "/includes/db/crm_contacts_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);
if (user_use_date_picker())
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
		return false;
	}

//	if ($_POST['mb_flag'] == 'S' && $_POST['eobi_applicable'] == 1)
//	{
//		$input_error = 1;
//		display_error(_("A Service Provider Employee can not be EOBI applicable"));
//		set_focus('eobi_applicable');
//		return false;
//	}

		if (strlen($_POST['emp_father']) == 0 || $_POST['emp_father'] == "")
	{
		$input_error = 1;
		display_error(_("The employee father name must be entered."));
		set_focus('emp_father');
		return false;
	}
	if ($_POST['emp_dept'] == "")
	{
		$input_error = 1;
		display_error(_("The department must be selected."));
		set_focus('emp_dept');
		return false;
	}

	if ($_POST['emp_gen'] == "")
	{
		$input_error = 1;
		display_error(_("The gender must be selected."));
		set_focus('emp_gen');
		return false;
	}
	///
//ansar
function check_employee_cnic_duplication($cnic)
{
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE emp_cnic=".db_escape($cnic);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
if($_POST['employee_id']==0)
{
$check_cnic=check_employee_cnic_duplication($_POST['emp_cnic']);
if ($check_cnic > 0)
	{
		$input_error = 1;
		display_error(_("The employee cnic already exist.$employee_id"));
		return false;
	}


//
    if ($_POST['emp_cnic'] == "")
    {
        $input_error = 1;
        display_error(_("CNIC number can not empty."));
        set_focus('emp_cnic');
        return false;
    }
}
	/*$code=get_employee_code($_POST['emp_code']);
	if ($code > 0)
	{
		$input_error = 1;
		display_error(_("The employee code is already exist."));
		return false;
	}*/
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

function get_emp_id_1($emp_code)
{
	$sql="SELECT employee_id FROM ".TB_PREF."employee where emp_code='".$emp_code."' ";
	$db = db_query($sql,'can not get');
	$ft = db_fetch($db);
	return $ft[0];
}
function handle_submit($employee_id)
{
	if (can_process())
	{
		if (check_value('del_image'))
		{
			$filename = company_path().'/images/'.trim($employee_id).".jpg";
			if (file_exists($filename))
				unlink($filename);
		}
		$log_date=date('d-m-Y');


		if ($employee_id) {
			if($_POST['inactive']==2){
				$inactive=0;

			}
			else if($_POST['inactive']==0)
			{
				$inactive=0;
			}
			else{
				$inactive=1;
			}
			update_employee(
				$_POST['employee_id'],
				$_POST['emp_code'],
				$_POST['emp_name'],
				$_POST['emp_father'],
				$_POST['emp_cnic'],
				$_POST['DOB'],
				$_POST['j_date'],
				$_POST['l_date'],
				$_POST['dur_period1'],
				$_POST['dur_period2'],
				$_POST['emp_reference'],
				$_POST['emp_home_phone'],
				$_POST['emp_mobile'],
				$_POST['emp_email'],
				$_POST['emp_bank'],
				$_POST['company_bank'],
				$_POST['basic_salary'],
				$_POST['prev_salary'],
				$_POST['duty_hours'],
				$_POST['ot_hours'],
				$_POST['social_sec'],
				$_POST['emp_ntn'],
				$_POST['emp_eobi'],
				$_POST['emp_address'],
				$_POST['notes'],
				$_POST['emp_title'],
				$_POST['emp_gen'],
				$_POST['emp_dept'],
				$_POST['emp_desig'],
				$_POST['emp_grade'],
				$_POST['cpf'],
				$_POST['employer_cpf'],
				$inactive,
				$_POST['division'],
				$_POST['project'],
				$_POST['age'],
				$_POST['report'],
				$_POST['location'],
				$_POST['vehicle'],
				$_POST['status'],
				$_POST['tax_deduction'],
				$_POST['applicable'],
				$_POST['leave_applicable'],
				$_POST['sessi_applicable'],
				$_POST['eobi_applicable'],
				$_POST['mb_flag'],
				$_POST['active_filer'],
				$_POST['bank_name'],
				$_POST['bank_branch'],
				$_POST['salary'],
				$_POST['cnic_expiry_date'],
				$_POST['pec_no'],
				$_POST['pec_expiry_date'],
				$_POST['license_no'],
				$_POST['license_expiry_date'],
				$_POST['text_filer'],
				$_POST['text_non_filer'],
				$_POST['loan_account'],
				$_POST['advance_account'],
				$_POST['salary_account'],
				$_POST['bonus_account'],

				$_POST['payroll_expenses'],
				$_POST['payroll_liabilty'],
				$_POST['advance_receivable'],
				$_POST['payment_account'],
				$_POST['tax_liability'],
				$_POST['deduction_account'],
				$_POST['over_time'],
				$_POST['blood_group']
			);


			add_employee_log($_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'],
				$_POST['emp_cnic'], $_POST['DOB'],$log_date, $log_date,$_POST['dur_period1'], $_POST['dur_period2'],
				$_POST['emp_reference'], $_POST['emp_home_phone'],
				$_POST['emp_mobile'], $_POST['emp_email'], $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'],
				$_POST['prev_salary'], $_POST['duty_hours'],$_POST['ot_hours'], $_POST['social_sec'], $_POST['emp_ntn'], $_POST['emp_eobi'],
				$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
				$_POST['emp_gen'], $_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade'], $_POST['cpf'], $_POST['employer_cpf'], $_POST['inactive']
				, $_POST['division'], $_POST['project']
				, $_POST['age'], $_POST['report'], $_POST['location'], $_POST['vehicle'], $_POST['status'], $_POST['tax_deduction'],
				$_POST['applicable'], $_POST['leave_applicable'], $_POST['sessi_applicable']
				, $_POST['eobi_applicable'], $_POST['mb_flag'], $_POST['active_filer'], $_POST['bank_name'], $_POST['bank_branch'], $_POST['salary'],
				$_POST['cnic_expiry_date'], $_POST['pec_no'], $_POST['pec_expiry_date'], $_POST['license_no'],
				$_POST['license_expiry_date'], $_POST['text_filer'], $_POST['text_non_filer'],$_POST['loan'],$_POST['over_time'],$_POST['blood_group']
			);

			display_notification(_("Employee has been updated."));
			//$Ajax->activate('employee_id'); // in case of status change

		} else {    //it is a new customer
			begin_transaction();
			$code=get_employee_code($_POST['emp_code']);
			if ($code < 1) {
				add_employee(
					$_POST['emp_code'],
					$_POST['emp_name'],
					$_POST['emp_father'],
					$_POST['emp_cnic'],
					$_POST['DOB'],
					$_POST['j_date'],
					$_POST['l_date'],
					$_POST['dur_period1'],
					$_POST['dur_period2'],
					$_POST['emp_reference'],
					$_POST['emp_home_phone'],
					$_POST['emp_mobile'],
					$_POST['emp_email'],
					$_POST['emp_bank'],
					$_POST['company_bank'],
					$_POST['basic_salary'],
					$_POST['prev_salary'],
					$_POST['duty_hours'],
					$_POST['ot_hours'],
					$_POST['social_sec'],
					$_POST['emp_ntn'],
					$_POST['emp_eobi'],
					$_POST['emp_address'],
					$_POST['notes'],
					$_POST['emp_title'],
					$_POST['emp_gen'],
					$_POST['emp_dept'],
					$_POST['emp_desig'],
					$_POST['emp_grade'],
					$_POST['cpf'],
					$_POST['employer_cpf'],
					$_POST['division'],
					$_POST['project'],
					$_POST['age'],
					$_POST['report'],
					$_POST['location'],
					$_POST['vehicle'],
					$_POST['status'],
					$_POST['tax_deduction'],
					$_POST['applicable'],
					$_POST['leave_applicable'],
					$_POST['sessi_applicable'],
					$_POST['eobi_applicable'],
					$_POST['mb_flag'],
					$_POST['active_filer'],
					$_POST['bank_name'],
					$_POST['bank_branch'],
					$_POST['salary'],
					$_POST['cnic_expiry_date'],
					$_POST['pec_no'],
					$_POST['pec_expiry_date'],
					$_POST['license_no'],
					$_POST['license_expiry_date'],
					$_POST['text_filer'],
					$_POST['text_non_filer'],
					$_POST['loan_account'],
					$_POST['advance_account'],
					$_POST['salary_account'],
					$_POST['bonus_account'],

					$_POST['payroll_expenses'],
					$_POST['payroll_liabilty'],
					$_POST['advance_receivable'],
					$_POST['payment_account'],
					$_POST['tax_liability'],
					$_POST['deduction_account'],
					$_POST['over_time'],
					$_POST['blood_group']
				);


//				$month = Today();
				$month1 = date("m",strtotime($_POST['j_date']));
				if($month1==01)
				    $month=1;
				elseif ($month1==02)
                    $month=2;
                elseif ($month1==03)
                    $month=3;
                elseif ($month1==04)
                    $month=4;
                elseif ($month1==05)
                    $month=5;
                elseif ($month1==06)
                    $month=6;
                elseif ($month1==07)
                    $month=7;
                elseif ($month1==08)
                    $month=8;
                elseif ($month1==09)
                    $month=9;
                elseif ($month1==10)
                    $month=10;
                elseif ($month1==11)
                    $month=11;
                elseif ($month1==12)
                    $month=12;

				$emp_id = get_emp_id_1($_POST['emp_code']);

				$f_year = get_current_fiscalyear();
				add_man_month_dir($_POST['emp_name'], $_POST['project'], 1 ,$month, $emp_id,$_POST['division'],$_POST['location'],$f_year['id']);


				add_employee_log($_POST['emp_code'], $_POST['emp_name'], $_POST['emp_father'],
					$_POST['emp_cnic'], $_POST['DOB'], $log_date, $log_date,
                    $_POST['dur_period1'], $_POST['dur_period2'],
					$_POST['emp_reference'], $_POST['emp_home_phone'],
					$_POST['emp_mobile'], $_POST['emp_email'], $_POST['emp_bank'], $_POST['company_bank'], $_POST['basic_salary'],
					$_POST['prev_salary'], $_POST['duty_hours'],$_POST['ot_hours'], $_POST['social_sec'], $_POST['emp_ntn'], $_POST['emp_eobi'],
					$_POST['emp_address'], $_POST['notes'], $_POST['emp_title'],
					$_POST['emp_gen'], $_POST['emp_dept'], $_POST['emp_desig'], $_POST['emp_grade'], $_POST['cpf'], $_POST['employer_cpf'], $_POST['inactive']
					, $_POST['division'], $_POST['project']
					, $_POST['age'], $_POST['report'], $_POST['location'], $_POST['vehicle'], $_POST['status'], $_POST['tax_deduction'],
					$_POST['applicable'], $_POST['leave_applicable'], $_POST['sessi_applicable']
					, $_POST['eobi_applicable'], $_POST['mb_flag'], $_POST['active_filer'], $_POST['bank_name'], $_POST['bank_branch'], $_POST['salary'],
					$_POST['cnic_expiry_date'], $_POST['pec_no'], $_POST['pec_expiry_date'], $_POST['license_no'],
					$_POST['license_expiry_date'], $_POST['text_filer'], $_POST['text_non_filer'], $_POST['over_time'], $_POST['blood_group']
				);
			}
			else{
				display_error(_("The employee code is already exist."));
			}

			commit_transaction();
			display_notification(_("A new employee has been added."));
		//	$Ajax->activate('_page_body');

		}
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
	elseif ( $_FILES['pic']['size'] > (1000 * 1024))
	{ //File Size Check
		display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . 1000);
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
/*	$cancel_delete = 0;
		if (key_in_foreign_table($employee_id, 'payroll', 'emp_id'))
	{
		$cancel_delete = 1;
		display_error(_("This Employee cannot be deleted because there are transactions that refer to it."));
               // update_employee_inactive($employee_id);
	}


	if ($cancel_delete == 0)*/
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
	global $pic_height,$Refs;
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
		$_POST['dur_period1'] = sql2date($myrow["dur_period1"]);
		$_POST['dur_period2'] = sql2date($myrow["dur_period2"]);
		$_POST['emp_reference']  = $myrow["emp_reference"];
		$_POST['emp_home_phone']  = $myrow["emp_home_phone"];
		$_POST['emp_mobile']  = $myrow["emp_mobile"];
		$_POST['emp_email']  = $myrow["emp_email"];
		$_POST['emp_bank'] = $myrow["emp_bank"];
		$_POST['company_bank'] = $myrow["company_bank"];
		$_POST['basic_salary'] = $myrow["basic_salary"];
		$_POST['prev_salary'] = $myrow["prev_salary"];
		$_POST['duty_hours'] = $myrow["duty_hours"];
		$_POST['ot_hours'] = $myrow["ot_hours"];
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

		$_POST['loan_account']  = $myrow["loan_account"];
		$_POST['advance_account']  = $myrow["advance_account"];
		$_POST['salary_account']  = $myrow["salary_account"];
		$_POST['bonus_account']  = $myrow["bonus_account"];



		$_POST['cpf']  = $myrow["cpf"];
		$_POST['employer_cpf']= $myrow["employer_cpf"];
	 	$_POST['inactive'] = $myrow["inactive"];
//        $_POST['division']= $myrow["division"];

		$_POST['divisionoooo']= $myrow["division"];

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
        $_POST['cnic_expiry_date']= sql2date($myrow["cnic_expiry_date"]);
        $_POST['pec_no']= $myrow["pec_no"];
        $_POST['pec_expiry_date']=  sql2date($myrow["pec_expiry_date"]);
        $_POST['license_no']= $myrow["license_no"];
        $_POST['license_expiry_date']= sql2date($myrow["license_expiry_date"]);
        $_POST['text_filer']= $myrow["text_filer"];
        $_POST['text_non_filer']= $myrow["text_non_filer"];
        $_POST['over_time']= $myrow["over_time"];
        $_POST['blood_group']= $myrow["blood_group"];


	}
	else
	{
		$_POST['emp_code']  = $_POST['emp_name'] = $_POST['emp_father'] =
	    $_POST['emp_cnic'] = $_POST['DOB']=$_POST['j_date'] = $_POST['l_date']=
        $_POST['dur_period1'] = $_POST['dur_period2']= $_POST['blood_group'] =
		$_POST['emp_reference'] = $_POST['emp_home_phone'] = $_POST['emp_mobile'] =
		$_POST['emp_email']= $_POST['emp_bank'] = $_POST['emp_bank'] = $_POST['basic_salary'] = $_POST['prev_salary'] = $_POST['duty_hours']  = $_POST['ot_hours']  = $_POST['social_sec']  = $_POST['emp_ntn']  =
		$_POST['emp_eobi']  = $_POST['emp_address'] = $_POST['notes'] =
		$_POST['emp_title'] =$_POST['emp_gen'] = $_POST['emp_dept']  =
		$_POST['emp_desig']  = $_POST['emp_grade']  = $_POST['cpf']  =  $_POST['employer_cpf']  =  '';
	}

	start_outer_table(TABLESTYLE2);

	table_section(1);

		table_section_title(_("Basic Data"));


		dimensions_list_row(_("Divison"), 'division', $_POST['divisionoooo'], 'All division', "", false, 1,true);
		pro_list_row(_("Project"), 'project',null, 'All Projects', "", false, 2,true,$_POST['division']);
		loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);


	if ($employee_id)
	{
        label_row(_("Code:"), $_POST['emp_code']);
        hidden('emp_code', $_POST['emp_code']);
	}
	else
	{
global $Refs;
       $emp_ref = $Refs->get_next(ST_EMPLOYEECODE);
        text_row(_("*Code:"),'emp_code', $emp_ref, null);

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

    //dimensions_list_row(_("Project"),'project',null,true);


//dimensions_list_row(_("Location"), 'location', null, 'All Locations', " ", false, 3);

	//var_dump($_POST['project']);
	//divison_list_row(_("Divison"),'divison',null,true);
	//var_dump($_POST['divison']);
	//number_list_row(_("Employee Location"), 'location', null, 1, $dim, true);

//	employee_loc_list_row(_("Employee Location"),'location',null,true);

// 	text_row(_("Age"), 'age', null, 10, 40);
	text_row(_("Report To"), 'report', null, 20, 40);
	employe_types_list_row2(_("Employee Type:"), 'mb_flag', null, true, true);

if($_POST['mb_flag'] == 'T') {
    date_row(_("Joining Period"), 'dur_period1', null, null, 0, 0, 0, null, false);
    date_row(_("Ending Period"), 'dur_period2', null, null, 0, 0, 0, null, false);
}

    yesno_list_cells_blood_group(_("Blood Group:"), 'blood_group', null, true);

    yesno_list_row(_("Vehicle Provided To Employee:"), 'vehicle');
    marital_list_row(_("Marital Status"), 'status');


    yesno_list_row(_("Income Tax Deduction:"), 'tax_deduction');
    yesno_list_row(_("Grautuity applicable:"), 'applicable');
    yesno_list_row(_("Leave encashment applicable:"), 'leave_applicable');
    yesno_list_row(_("Sessi applicable:"), 'sessi_applicable');
    yesno_list_row(_("EOBI applicable:"), 'eobi_applicable');
    yesno_list_row(_("Over Time:"), 'over_time');


//	if ($employee_id && !is_new_employee($employee_id))
//	{
//		label_row(_("Gender:"), $_POST['emp_gen']);
//		hidden('emp_gen', $_POST['emp_gen']);
//	}
//	else
//	{
		emp_gen_row(_("*Gender:"), 'emp_gen', null,true);
//	}

    //marina--------start---------//

// 	$dateOfBirth = $_POST['DOB'];
// 	$today = date("Y-m-d");
// 	$diff = date_diff(date_create($dateOfBirth), date_create($today));
// 	$diff->format('%y');
// 	$_POST['age'] = $diff->format('%y');

	//marina--------start---------//
	
	date_row(_("Date of Birth"),'DOB', null,null, 0, 0, 0, null, false);
	
	text_row(_("Age"), 'age', null,  10, 40);
	
    date_row(_("Date of joining"),'j_date', null,null, 0, 0, 0, null, false);
	date_row(_("Date of leaving"),'l_date', null,null, 0, 0, 0, null, false);

	text_row(_("Reference:"), 'emp_reference', null, 42, 40);
	text_row(_("Home Phone:"), 'emp_home_phone', null, 30, 30);
	text_row(_("Mobile:"), 'emp_mobile', null, 30, 30);
	text_row(_("Email:"), 'emp_email', null, 42, 40);


    emp_dept_row(_("*Department:"), 'emp_dept', null,true);

    emp_desg_row(_("*Designation:"), 'emp_desig', null,true);

    table_section_title(_("Income Tax Status"));
    check_row(_("Tax Filer:"), 'text_filer', null, false);
   // check_row(_("Text Non Filer:"), 'text_non_filer', null, true);
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
		emp_grade_row(_("*Grade:"), 'emp_grade', null,true);
	//table_section_title(_("Accounts"));
	/*gl_all_accounts_list_row(_("Loan:"), 'loan_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Advacne:"), 'advance_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Salary:"), 'salary_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Bonuc:"), 'bonus_account', null, false, false, _("All Accounts"));

	gl_all_accounts_list_row(_("Payroll Expense Account:"), 'payroll_expenses', get_sys_pay_pref('payroll_expenses'));
	gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', get_sys_pay_pref('payroll_liabilty'));
	gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', get_sys_pay_pref('advance_receivable'));
	gl_all_accounts_list_row(_("Payment Account:"), 'payment_account', get_sys_pay_pref('payment'));
	gl_all_accounts_list_row(_("Tax Liability Account:"), 'tax_liability', get_sys_pay_pref('tax_liability'));
*/
	/*gl_all_accounts_list_row(_("EOBI Account:"), 'eobi_liability', get_sys_pay_pref('eobi_liability'));
	gl_all_accounts_list_row(_("Loan:"), 'loan_account', get_sys_pay_pref('loan_account'));
	gl_all_accounts_list_row(_("Advacne:"), 'advance_account', get_sys_pay_pref('advance_account'));
	gl_all_accounts_list_row(_("Salary:"), 'salary_account', get_sys_pay_pref('salary_account'));
	gl_all_accounts_list_row(_("Bonus:"), 'bonus_account', get_sys_pay_pref('bonus_account'));*/



		if($employee_id)
		{
		//record_status_list_row(_("Employee status:"), 'inactive');
			//record_status_list_row_new(_("Employee status:"), 'inactive');
       yesno_list_row_new(_("Employee status:"), 'inactive', null, 	_('Inactive'), _('Active'),_("Rejoin"));
		}

//	}
	table_section(2);
	if($_POST['emp_name'] !='')
	{
		table_section_title(_("Add Employee Documents"));
		echo'<td style="text-align: center"><a href="../../payroll/manage/menu/emp_documents_view.php?employee_id='.$_POST['employee_id'].'"> Add employee Documents</a></td>';
	}

	table_section_title(_("Salary Details"));
	text_row(_("Employee Bank A/C No.:"), 'emp_bank', null, 20, 20);
	text_row(_("Employee Bank Name.:"), 'bank_name', null, 20, 20);
	text_row(_("Employee Bank Branch:"), 'bank_branch', null, 20, 20);
    salary_list_row(_("Mode Of Salary Payment"),'salary',null,false);

   bank_accounts_list_row_emp(_("Company Bank:"), 'company_bank',null,false,$_POST['division']);
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
       // employee_emp_allowance_cell1($employee_id,'Add Allowance');

    }
    text_row(_("Previous Salary:"), 'prev_salary', null, 10, 10);
	text_row(_("Duty Hours:"), 'duty_hours', null, 10, 10);
	
	text_row(_("OT Hours:"), 'ot_hours', null, 10, 10);


    if ($employee_id)  {

       // employee_man_deduction_cell($employee_id,'Add Man Month');
//        employee_man_qualification_cell($employee_id,'Add Employee Qualification');
       // employee_emp_deduction_cell1($employee_id,'Add Deduction');
		//employee_history_cell($employee_id,'Add Employee History');
		//man_qualification_cell($employee_id,'Add Employee Qualification');
		//employee_nomination_cell($employee_id,'Add Employee Nomination');

    }
	table_section_title(_("Identification Details"));
	text_row(_("*CNIC:"), 'emp_cnic', null, 30, 30);
    date_row(_("CNIC Expiry Date:"), 'cnic_expiry_date', null, 30, 30);
    text_row(_("*PEC No:"), 'pec_no', null, 30, 30);
    date_row(_("PEC Expiry Date:"), 'pec_expiry_date', null, 30, 30);

    text_row(_("Social Security:"), 'social_sec', null, 30, 30);
		text_row(_("NTN:"), 'emp_ntn', null, 30, 30);
	text_row(_("EOBI No:"), 'emp_eobi', null, 30, 30);
	//text_row(_("Employee CPF:"), 'cpf', null, 30, 30);
	//text_row(_("Employer CPF:"), 'employer_cpf', null, 30, 30);
	text_row(_("License No"), 'license_no', null, 30, 30);
	date_row(_("License Expiry Date:"), 'license_expiry_date', null, 30, 30);





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

	/*if (isset($_POST['employee_id']) && file_exists(company_path().'/images/'
		.trim($_POST['employee_id']).".jpg"))
	{
	$stock_thumb_link .= "<img id='item_img' alt = '[".$_POST['employee_id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_POST['employee_id']).
			".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";
	}	 */


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
br(2);
	//table_section_title(_("Accounts"));
	/*gl_all_accounts_list_row(_("Loan:"), 'loan_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Advacne:"), 'advance_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Salary:"), 'salary_account', null, false, false, _("All Accounts"));
	gl_all_accounts_list_row(_("Bonuc:"), 'bonus_account', null, false, false, _("All Accounts"));*/

	/*gl_all_accounts_list_row(_("Payroll Expense Account:"), 'payroll_expenses', get_sys_pay_pref('payroll_expenses'));
	gl_all_accounts_list_row(_("Payroll Liability Account:"), 'payroll_liabilty', get_sys_pay_pref('payroll_liabilty'));
	gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', get_sys_pay_pref('advance_receivable'));
	gl_all_accounts_list_row(_("Payment Account:"), 'payment', get_sys_pay_pref('payment'));
	gl_all_accounts_list_row(_("Tax Liability Account:"), 'tax_liability', get_sys_pay_pref('tax_liability'));*/
/*
	gl_all_accounts_list_row(_("EOBI Account:"), 'eobi_liability', get_sys_pay_pref('eobi_liability'));
	gl_all_accounts_list_row(_("Loan:"), 'loan_account', get_sys_pay_pref('loan_account'));
	gl_all_accounts_list_row(_("Advacne:"), 'advance_account', get_sys_pay_pref('advance_account'));
	gl_all_accounts_list_row(_("Salary:"), 'salary_account', get_sys_pay_pref('salary_account'));
	gl_all_accounts_list_row(_("Bonus:"), 'bonus_account', get_sys_pay_pref('bonus_account'));
	gl_all_accounts_list_row(_("Deduction Account:"), 'deduction_account', get_sys_pay_pref('deduction_account'));*/

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
		submit_center_last_alert('delete', _("Delete Employee"),
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
//	  employee_list_cells(_("Select a employeeasdasdas: "), 'employee_id', null,
//		  _('New employee'), true, check_value('show_inactive'));

	emp_list_cells(_("Select a Employee: "), 'employee_id', null,
		_('New Employee'), true, check_value('show_inactive'));

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
	    'allowance' => array(_('&Allowance'), $employee_id),
		'man_month' => array(_('&Attendance'), $employee_id),
	'emp_deduction' => array(_('&Deductions'), $employee_id),
	'employment_history' => array(_('&History'), $employee_id),
	'man_qualification' => array(_('&Qualification'), $employee_id),
	'employee_nomination' => array(_('&Nomination'), $employee_id),
    'employee_coa' => array(_('&Employee Accounts'), $employee_id),
    'employee_family_details' => array(_('&Employee Family Details'), $employee_id),
	));

	switch (get_post('_tabs_sel')) {
		default:
		case 'settings':
			supplier_settings($employee_id);
			break;
		case 'allowance':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/emp_allowance_view.php");
		break;


		case 'man_month':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/man_month_view.php");
            break;
		case 'emp_deduction':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/emp_deduction_view.php");
			break;

		case 'employment_history':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/employment_history_view.php");
			break;

		case 'man_qualification':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/man_qualification_view.php");
			break;

		case 'employee_nomination':

			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/employee_nomination_view.php");
			break;
        case 'employee_coa':

            $_GET['employee_id'] = $employee_id;
            $_GET['popup'] = 1;
            include_once($path_to_root."/payroll/manage/menu/employee_account.php");
            break;

        case 'employee_family_details':

            $_GET['employee_id'] = $employee_id;
            $_GET['popup'] = 1;
            include_once($path_to_root."/payroll/manage/menu/employee_family_details_view.php");
            break;

		/*case 'employee_documentation':
			$_GET['employee_id'] = $employee_id;
			$_GET['popup'] = 1;
			include_once($path_to_root."/payroll/manage/menu/attachments.php?filterType=20");
			break;*/

	};


br();
tabbed_content_end();

div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);
?>
<script>
	function myFunction() {
		alert("I am an alert box!");
	}
</script>
