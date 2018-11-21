<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
//include_once($path_to_root . "/includes/db/crm_contacts_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Add Applicant"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/payroll/includes/db/add_applicant_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");


$user_comp = user_company();


if (isset($_GET['add_id'])) 
{
	$_POST['add_id'] = $_GET['add_id'];
}

$add_id = get_post('add_id','');

function can_process()
{
	$input_error = 0;

	if (strlen($_POST['applicant_code']) == 0 || $_POST['applicant_code'] == "") 
	{
		$input_error = 1;
		display_error(_("The Applicant code must be entered."));
		set_focus('applicant_code');
	}
	$chech=check_applicant_code_duplication($_POST['applicant_code']);
	
	if($chech>0)
	{
		$input_error = 1;
		display_error(_("Aleardy Inserted"));
		set_focus('applicant_code');
                return false;
		
	}
	
	

	return true;
}


function handle_submit($add_id)
{
	global $path_to_root, $Ajax, $auto_create_branch;


	if (!can_process())
		return;
	
		
	if ($add_id) 
	{
	 	update_applicant($_POST['add_id'], $_POST['applicant_code'],$_POST['date_applicant'], $_POST['full_name'], $_POST['date_of_birth'],$_POST['nationality'], $_POST['present_address'], $_POST['permanent_address'] , $_POST['telephone_no'] ,$_POST['mobile_no'], $_POST['gender'],$_POST['institution1'],$_POST['institution2'],$_POST['institution3'],$_POST['institution4'],$_POST['institution5'],$_POST['year_attended1'],$_POST['year_attended2'],$_POST['year_attended3'],$_POST['year_attended4'],$_POST['year_attended5'],$_POST['degree_diploma1'],$_POST['degree_diploma2'],$_POST['degree_diploma3'],$_POST['degree_diploma4'],$_POST['degree_diploma5'],$_POST['major_subjects1'],$_POST['major_subjects2'],$_POST['major_subjects3'],$_POST['major_subjects4'],$_POST['major_subjects5'],$_POST['prof_inst1'],$_POST['prof_inst2'],$_POST['prof_inst3'],$_POST['prof_year1'],$_POST['prof_year1'],$_POST['prof_year3'],$_POST['prof_cour1'],$_POST['prof_cour2'],$_POST['prof_cour3'],$_POST['organ1'],$_POST['organ2'],$_POST['organ3'],$_POST['pos_held1'],$_POST['pos_held2'],$_POST['pos_held3'],$_POST['employee1'],$_POST['employee2'],$_POST['employee3'],$_POST['reason1'],$_POST['reason2'],$_POST['reason3']);
		
		update_record_status($_POST['add_id'], $_POST['inactive'],
			'add_applicant', 'add_id');

		$Ajax->activate('add_id'); 
		display_notification(_("Applicant has been updated."));
		// in case of status change
			
	} 
	else 
	{ 	//it is a new customer

		 begin_transaction();
		add_applicant($_POST['applicant_code'],$_POST['date_applicant'], $_POST['full_name'], $_POST['date_of_birth'],$_POST['nationality'], $_POST['present_address'], $_POST['permanent_address'], $_POST['telephone_no'], $_POST['mobile_no'], $_POST['gender'],$_POST['institution1'],$_POST['institution2'],$_POST['institution3'],$_POST['institution4'],$_POST['institution5'],$_POST['year_attended1'],$_POST['year_attended2'],$_POST['year_attended3'],$_POST['year_attended4'],$_POST['year_attended5'],$_POST['degree_diploma1'],$_POST['degree_diploma2'],$_POST['degree_diploma3'],$_POST['degree_diploma4'],$_POST['degree_diploma5'],$_POST['major_subjects1'],$_POST['major_subjects2'],$_POST['major_subjects3'],$_POST['major_subjects4'],$_POST['major_subjects5'],$_POST['prof_inst1'],$_POST['prof_inst2'],$_POST['prof_inst3'],$_POST['prof_year1'],$_POST['prof_year1'],$_POST['prof_year3'],$_POST['prof_cour1'],$_POST['prof_cour2'],$_POST['prof_cour3'],$_POST['organ1'],$_POST['organ2'],$_POST['organ3'],$_POST['pos_held1'],$_POST['pos_held2'],$_POST['pos_held3'],$_POST['employee1'],$_POST['employee2'],$_POST['employee3'],$_POST['reason1'],$_POST['reason2'],$_POST['reason3']);
		
		display_notification(_("A New Applicant has been added."));
		commit_transaction();
		
			$Ajax->activate('_page_body');
		
	}
}

//------------------------------------------------------------------------------------
//applicant Image

$upload_file = "";

if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') 
{
	$add_id = $_POST['add_id'];
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes'; //Assume all is well to start off with
	$filename = company_path().'/applicant/';
	if (!file_exists($filename))
	{
		mkdir($filename);
	}	
	$filename .= "/".trim($add_id).".jpg";
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

	handle_submit($add_id);
	
}
if (isset($_POST['delete'])) 
{
	$cancel_delete = 0;
		/*
		if (key_in_foreign_table($employee_id, 'payroll', 'emp_id'))
	{
		$cancel_delete = 1;
		display_error(_("This Employee cannot be deleted because there are transactions that refer to it."));
	} 
	*/
	
	if ($cancel_delete == 0) 
	{
	delete_applicant($_POST['add_id']);

		unset($_SESSION['add_id']);
		$add_id = '';
		$Ajax->activate('_page_body');
	 //end 
		
		$filename = company_path().'/applicant/'.trim($add_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		//display_notification(_("Selected Employee has been deleted."));
		unset($_POST['add_id']);
		$add_id = '';
		$Ajax->activate('_page_body');
	}

}


function supplier_settings($add_id) 
{
	global $pic_height;
	if ($add_id) 
	{
		$myrow = get_applicant($_POST['add_id']);
		$_POST['applicant_code'] = $myrow["applicant_code"];
		$_POST['date_applicant'] =sql2date( $myrow["date_applicant"]);
		$_POST['full_name'] = $myrow["full_name"];
	    $_POST['date_of_birth'] =sql2date( $myrow["date_of_birth"]);
		$_POST['nationality']  = $myrow["nationality"];
	    $_POST['present_address'] = $myrow["present_address"];
	    $_POST['permanent_address']  = $myrow["permanent_address"];
		$_POST['telephone_no']  = $myrow["telephone_no"];
		$_POST['mobile_no']  = $myrow["mobile_no"];
		$_POST['gender']  = $myrow["gender"];
		$_POST['institution1']  = $myrow["institution1"];
		$_POST['institution2']  = $myrow["institution2"];
		$_POST['institution3']  = $myrow["institution3"];
		$_POST['institution4']  = $myrow["institution4"];
		$_POST['institution5']  = $myrow["institution5"];
		$_POST['year_attended1']  = $myrow["year_attended1"];
		$_POST['year_attended2']  = $myrow["year_attended2"];
		$_POST['year_attended3']  = $myrow["year_attended3"];
		$_POST['year_attended4']  = $myrow["year_attended4"];
		$_POST['year_attended5']  = $myrow["year_attended5"];
		$_POST['degree_diploma1']  = $myrow["degree_diploma1"];
		$_POST['degree_diploma2']  = $myrow["degree_diploma2"];
		$_POST['degree_diploma3']  = $myrow["degree_diploma3"];
		$_POST['degree_diploma4']  = $myrow["degree_diploma4"];
		$_POST['degree_diploma5']  = $myrow["degree_diploma5"];
		$_POST['major_subjects1']  = $myrow["major_subjects1"];
		$_POST['major_subjects2']  = $myrow["major_subjects2"];
		$_POST['major_subjects3']  = $myrow["major_subjects3"];
		$_POST['major_subjects4']  = $myrow["major_subjects4"];
		$_POST['major_subjects5']  = $myrow["major_subjects5"];
		$_POST['prof_inst1']  = $myrow["prof_inst1"];
		$_POST['prof_inst2']  = $myrow["prof_inst2"];
		$_POST['prof_inst3']  = $myrow["prof_inst3"];
		$_POST['prof_year1']  = $myrow["prof_year1"];
		$_POST['prof_year2']  = $myrow["prof_year2"];
		$_POST['prof_year3']  = $myrow["prof_year3"];
		$_POST['prof_cour1']  = $myrow["prof_cour1"];
		$_POST['prof_cour2']  = $myrow["prof_cour2"];
		$_POST['prof_cour3']  = $myrow["prof_cour3"];
		$_POST['organ1']= $myrow["organ1"];
		$_POST['organ2']= $myrow["organ2"];
		$_POST['organ3']= $myrow["organ3"];
		$_POST['pos_held1']= $myrow["pos_held1"];
		$_POST['pos_held2']= $myrow["pos_held2"];
		$_POST['pos_held3']= $myrow["pos_held3"];
		$_POST['employee1']= $myrow["employee1"];
		$_POST['employee2']= $myrow["employee2"];
		$_POST['employee3']= $myrow["employee3"];
		$_POST['reason1']= $myrow["reason1"];
		$_POST['reason2']= $myrow["reason2"];
		$_POST['reason3']= $myrow["reason3"];
		
	 	$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
		$_POST['applicant_code'] = $_POST['date_applicant']  = $_POST['full_name'] = $_POST['date_of_birth'] = $_POST['nationality'] = $_POST['present_address']=$_POST['permanent_address'] = $_POST['telephone_no']=$_POST['mobile_no'] = $_POST['gender'] =$_POST['institution1'] =$_POST['institution2'] =$_POST['institution3'] =$_POST['institution4'] =$_POST['institution5'] =$_POST['year_attended1'] =$_POST['year_attended2'] =$_POST['year_attended3'] =$_POST['year_attended4'] =$_POST['year_attended5'] =$_POST['degree_diploma1'] =$_POST['degree_diploma2'] =$_POST['degree_diploma3'] =$_POST['degree_diploma4'] =$_POST['degree_diploma5'] =$_POST['major_subjects1'] =$_POST['major_subjects2'] =$_POST['major_subjects3'] =$_POST['major_subjects4'] =$_POST['major_subjects5'] =$_POST['prof_inst1'] =$_POST['prof_inst2'] =$_POST['prof_inst3'] =$_POST['prof_year1'] = $_POST['prof_year2'] = $_POST['prof_year3'] = $_POST['prof_cour1'] = $_POST['prof_cour2'] =$_POST['prof_cour3'] = $_POST['organ1'] = $_POST['organ2'] = $_POST['organ3']=$_POST['pos_held1']=$_POST['pos_held2']=$_POST['pos_held3']=$_POST['employee1']=$_POST['employee2']=$_POST['employee3']=$_POST['reason1']=$_POST['reason2']=$_POST['reason3']='';} 


	start_outer_table(TABLESTYLE2);

	table_section(1);
	
		table_section_title(_("Personal Information"));
	text_row_ex(_("Applicant Code:"), 'applicant_code', null, 13, 13);
	date_row(_("Date of Applicant:"),'date_applicant', null,null, 0, 0, 0, null, true);
	text_row_ex(_("Full Name:"), 'full_name', null,  22, 5);
    date_row(_("Date of Birth :"),'date_of_birth', null,null, 0, 0, 0, null, true); 
   
	if ($add_id && !is_new_applicant($add_id)) 
	{
		label_row(_("Nationality:"), $_POST['qualification']);
		hidden('nationality', $_POST['nationality']);
	}
	else 
	{
		applicant_national_row(_("Nationality:"), 'nationality', null,true);
	}
	
	 textarea_row(_("Present Address :"), 'present_address', null, 22, 5);
	 textarea_row(_("Permanent Address :"), 'permanent_address', null, 22, 5);
	 text_row_ex(_("Telephone No:"), 'telephone_no', null, 13, 13);
	 
	 text_row_ex(_("Mobile No:"), 'mobile_no', null, 13, 13);
	
	
	if ($add_id && !is_new_applicant($add_id)) 
	{
		label_row(_("Gender:"), $_POST['gender']);
		hidden('gender', $_POST['gender']);
	} 
	else
	{
		emp_gen_row(_("Gender:"), 'gender', null,true);
	}
	
	
	
	if($add_id)
		record_status_list_row(_("applicant status:"), 'inactive');


	
	//------------------------------------------------------------------------------------
	
	//Applicant image
	if($add_id)
	{
	file_row(_("Applicant Image (.jpg)") . ":", 'pic', 'pic');
	}// Add Image upload for New Item  - by Joe
	$stock_img_link = "";
	$check_remove_image = false;
	
	if (isset($_POST['add_id']) && file_exists(company_path().'/applicant/'
		.trim($_POST['add_id']).".jpg"))
	{
	$stock_img_link .= "<img id='item_img' alt = '[".$_POST['add_id'].".jpg".
			"]' src='".company_path().'/applicant/'.trim($_POST['add_id']).
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
	
//------------------------------------------------------------------------------------
	end_outer_table(1);

start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Education"));
end_outer_table(1);

start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Institution"));
 text_row_ex(_(""), 'institution1', 40);
 text_row_ex(_(""), 'institution2', 40);
 text_row_ex(_(""), 'institution3', 40);
 text_row_ex(_(""), 'institution4', 40);
 text_row_ex(_(""), 'institution5', 40);
	 
table_section(2);
table_section_title(_("Year Attended"));	 
 text_row_ex(_(""), 'year_attended1',10);
 text_row_ex(_(""), 'year_attended2',10);
 text_row_ex(_(""), 'year_attended3', 10);
 text_row_ex(_(""), 'year_attended4',10);
 text_row_ex(_(""), 'year_attended5', 10);

table_section(3);
table_section_title(_("Degree/ Diploma"));	 
 text_row_ex(_(""), 'degree_diploma1',20);
 text_row_ex(_(""), 'degree_diploma2',20);
 text_row_ex(_(""), 'degree_diploma3',20);
 text_row_ex(_(""), 'degree_diploma4',20);
 text_row_ex(_(""), 'degree_diploma5',20);

table_section(4);
table_section_title(_("Major Subjects"));	 
 text_row_ex(_(""), 'major_subjects1',20);
 text_row_ex(_(""), 'major_subjects2',20);
 text_row_ex(_(""), 'major_subjects3', 20);
 text_row_ex(_(""), 'major_subjects4', 20);
 text_row_ex(_(""), 'major_subjects5', 20);
end_outer_table(1);




start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Professional Course Attended"));
end_outer_table(1);

start_outer_table(TABLESTYLE2);

table_section(1);
table_section_title(_("Institution"));	 
 text_row_ex(_(""), 'prof_inst1',40);
 text_row_ex(_(""), 'prof_inst2',40);
 text_row_ex(_(""), 'prof_inst3', 40);

table_section(2);
table_section_title(_("Year Attended"));
 text_row_ex(_(""), 'prof_year1',10);
 text_row_ex(_(""), 'prof_year2', 10);
 text_row_ex(_(""), 'prof_year3',10);
	 


table_section(3);
table_section_title(_("Course"));	 
 text_row_ex(_(""), 'prof_cour1', 20);
 text_row_ex(_(""), 'prof_cour2', 20);
 text_row_ex(_(""), 'prof_cour3',20);
end_outer_table(1);

start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Working Experience"));
end_outer_table(1);

start_outer_table(TABLESTYLE2);

table_section(1);
table_section_title(_("Organization"));	 
 text_row_ex(_(""), 'organ1',40);
 text_row_ex(_(""), 'organ2', 40);
 text_row_ex(_(""), 'organ3',40);
 
table_section(2);
table_section_title(_("Position"));	 
 text_row_ex(_(""), 'pos_held1', 20);
 text_row_ex(_(""), 'pos_held2', 20);
 text_row_ex(_(""), 'pos_held3', 20); 
 
table_section(3);
table_section_title(_("Employment Period"));	 
 text_row_ex(_(""), 'employee1', 15);
 text_row_ex(_(""), 'employee2', 15);
 text_row_ex(_(""), 'employee3', 15);  


table_section(4);
table_section_title(_("Reason For Leaving"));	 
 text_row_ex(_(""), 'reason1', 40);
 text_row_ex(_(""), 'reason2',40);
 text_row_ex(_(""), 'reason3', 40);  
end_outer_table(1);

	div_start('controls');
	if (!$add_id)
	{
		submit_center('addupdate', _("Add New Applicant"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('addupdate', _("Update Applicant"), 
		  _('Update Applicant data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $add_id, _("Select this Applicant and return to document entry."));
		submit_center_last('delete', _("Delete Applicant"), 
		  _('Delete Applicant data if have been never used'), true);
	}
	

	div_end();
}

//--------------------------------------------------------------------------------------------
start_form(true);

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	  applicant_list_cells(_("Select a Applicant: "), 'add_id', null,
		  _('New Applicant'), true, check_value('show_inactive')); 
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('add_id');
		set_focus('add_id');
	}
} 
else 
{
	hidden('add_id');
}



div_start('details');

if (!$add_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer


	
	
supplier_settings($add_id); 

br();
tabbed_content_end();

div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);
?>