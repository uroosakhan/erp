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

page(_($help_context = "Advertisment"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/payroll/includes/db/advertisment_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");


$user_comp = user_company();


if (isset($_GET['advertisment_id'])) 
{
	$_POST['advertisment_id'] = $_GET['advertisment_id'];
}

$advertisment_id = get_post('advertisment_id','');

function can_process()
{
	$input_error = 0;

	if (strlen($_POST['advertisment_code']) == 0 || $_POST['advertisment_code'] == "") 
	{
		$input_error = 1;
		display_error(_("The Advertisment code must be entered."));
		set_focus('advertisment_code');
	}
	
	
	if ($upload_file == 'No')
	{
	display_error(_("Upload File false."));
	return false;	
	}

	return true;
}


function handle_submit($advertisment_id)
{
	global $path_to_root, $Ajax, $auto_create_branch;


	if (!can_process())
		return;
	
	if (check_value('del_image'))
	{
			$filename = company_path().'/advertisment/'.trim($advertisment_id).".jpg";
			if (file_exists($filename))
				unlink($filename);
	}	
		
	if ($advertisment_id) 
	{
	 	update_advertisment($_POST['advertisment_id'], $_POST['advertisment_code'], $_POST['description'], $_POST['no_of_position'],
			$_POST['newspaper'], $_POST['newspaper2'],$_POST['newspaper3'],$_POST['website'],$_POST['website2'], $_POST['date_of_advertisment'] , $_POST['close_date'] ,
		    $_POST['qualification'], $_POST['years_of_exp'], 
			$_POST['gender'], $_POST['designation'],  $_POST['job_type'], $_POST['age'], $_POST['salary_range'], 
			$_POST['travel_required'], $_POST['location'],  $_POST['required_skills']);
		
		update_record_status($_POST['advertisment_id'], $_POST['inactive'],
			'advertisment', 'advertisment_id');

		$Ajax->activate('advertisment_id'); 
		display_notification(_("Advertisment has been updated."));
		// in case of status change
			
	} 
	else 
	{ 	//it is a new customer

		 begin_transaction();
		add_advertisment(  $_POST['advertisment_code'], $_POST['description'], $_POST['no_of_position'],
			$_POST['newspaper'], $_POST['newspaper2'],$_POST['newspaper3'],$_POST['website'],$_POST['website2'], $_POST['date_of_advertisment'] , $_POST['close_date'] ,
		    $_POST['qualification'], $_POST['years_of_exp'], 
			$_POST['gender'], $_POST['designation'],  $_POST['job_type'], $_POST['age'], $_POST['salary_range'], 
			$_POST['travel_required'], $_POST['location'],  $_POST['required_skills']);
		
		display_notification(_("A New Advertisment has been added."));
		commit_transaction();
		
			$Ajax->activate('_page_body');
		
	}
}

//------------------------------------------------------------------------------------
//Advertisment Image

$upload_file = "";

if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '') 
{
	$advertisment_id = $_POST['advertisment_id'];
	$result = $_FILES['pic']['error'];
 	$upload_file = 'Yes'; //Assume all is well to start off with
	$filename = company_path().'/advertisment/';
	if (!file_exists($filename))
	{
		mkdir($filename);
	}	
	$filename .= "/".trim($advertisment_id).".jpg";
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

	handle_submit($advertisment_id);
	
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
	delete_advertisment($_POST['advertisment_id']);

		unset($_SESSION['advertisment_id']);
		$advertisment_id = '';
		$Ajax->activate('_page_body');
	 //end 
		
		$filename = company_path().'/advertisment/'.trim($advertisment_id).".jpg";
		if (file_exists($filename))
			unlink($filename);
		//display_notification(_("Selected Employee has been deleted."));
		unset($_POST['advertisment_id']);
		$advertisment_id = '';
		$Ajax->activate('_page_body');
	}

}


function supplier_settings($advertisment_id) 
{
	global $pic_height;
	if ($advertisment_id) 
	{
		$myrow = get_advertisment($_POST['advertisment_id']);

		$_POST['advertisment_code'] = $myrow["advertisment_code"];
		$_POST['description'] = $myrow["description"];
		$_POST['no_of_position']  = $myrow["no_of_position"];
		$_POST['newspaper']  = $myrow["newspaper"];
		$_POST['newspaper2']  = $myrow["newspaper2"];
		$_POST['newspaper3']  = $myrow["newspaper3"];
	    $_POST['website'] = $myrow["website"];
		$_POST['website2'] = $myrow["website2"];
	    $_POST['date_of_advertisment'] =sql2date( $myrow["date_of_advertisment"]);
		$_POST['close_date'] = sql2date($myrow["close_date"]);
		$_POST['qualification']  = $myrow["qualification"];
		$_POST['years_of_exp']  = $myrow["years_of_exp"];
		$_POST['gender']  = $myrow["gender"];
		$_POST['designation']  = $myrow["designation"];
		$_POST['job_type'] = $myrow["job_type"];
		$_POST['age'] = $myrow["age"];		
		$_POST['salary_range'] = $myrow["salary_range"];	
		$_POST['travel_required'] = $myrow["travel_required"];
		$_POST['location'] = $myrow["location"];	
		$_POST['required_skills'] = $myrow["required_skills"];
	 	//$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
		$_POST['advertisment_code']  = $_POST['description'] = $_POST['no_of_position'] =
	    $_POST['newspaper'] = $_POST['newspaper2'] = $_POST['newspaper3'] = $_POST['website'] = $_POST['website2'] =$_POST['date_of_advertisment']=$_POST['close_date'] = $_POST['qualification']=$_POST['years_of_exp'] = $_POST['gender'] = $_POST['designation'] = $_POST['job_type']= $_POST['age'] = $_POST['salary_range'] = $_POST['travel_required'] = $_POST['location'] = $_POST['required_skills']  = 
'';} 


	start_outer_table(TABLESTYLE2);

	table_section(1);
	
		table_section_title(_("Advertisment"));
	
	text_row(_("*Code:"), 'advertisment_code', null, 13, 13);
	textarea_row(_("Description:"), 'description', null,  22, 5);
    text_row(_("No of positions :"), 'no_of_position', null, 13, 13);
	text_row(_("Newspaper:"), 'newspaper', null, 13, 13);
	text_row(_(""), 'newspaper2', null, 13, 13);
	text_row(_(""), 'newspaper3', null, 13, 13);
    text_row(_("WebSite:"), 'website', null, 13, 13);
	text_row(_(""), 'website2', null, 13, 13);
	date_row(_("Date of Advertisment :"),'date_of_advertisment', null,null, 0, 0, 0, null, true); 
    date_row(_("Closing Date"),'close_date', null,null, 0, 0, 0, null, true);
	
	if ($advertisment_id && !is_new_advertisment($advertisment_id)) 
	{
		label_row(_("Qualification:"), $_POST['qualification']);
		hidden('qualification', $_POST['qualification']);
	}
	else 
	{
		adv_qualification_row(_("Qualification:"), 'qualification', null,true);
	}
	
	
	
	if ($advertisment_id && !is_new_advertisment($advertisment_id)) 
	{
		label_row(_("Years of Experience:"), $_POST['years_of_exp']);
		hidden('years_of_exp', $_POST['years_of_exp']);
	} 
	else
	{
		adv_experienc_row(_("Years of Experience:"), 'years_of_exp', null,true);
	}
	
	
	if ($advertisment_id && !is_new_advertisment($advertisment_id)) 
	{
		label_row(_("Gender:"), $_POST['gender']);
		hidden('gender', $_POST['gender']);
	} 
	else
	{
		emp_gen_row(_("Gender:"), 'gender', null,true);
	}
	
	


	table_section(2);
	table_section_title(_("Particulars"));	
	if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Designation:"), $_POST['designation']);
		hidden('designation', $_POST['designation']);
	} 
	else 
	{
		emp_desg_row(_("Designation:"), 'designation', null,true);
	}
				
	
	 if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Job Type:"), $_POST['job_type']);
		hidden('job_type', $_POST['job_type']);
	} 
	else 
	{
		adv_job_row(_("Job Type:"), 'job_type', null,true);
	}
	
	 if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Age:"), $_POST['age']);
		hidden('age', $_POST['age']);
	} 
	else 
	{
		adv_age_row(_("Age:"), 'age', null,true);
	}
	
	
	 if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Salary Range::"), $_POST['salary_range']);
		hidden('salary_range', $_POST['salary_range']);
	} 
	else 
	{
		adv_salary_row(_("Salary Range::"), 'salary_range', null,true);
	}
	 if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Travel Required:"), $_POST['travel_required']);
		hidden('travel_required', $_POST['travel_required']);
	} 
	else 
	{
		adv_travel_row(_("Travel Required:"), 'travel_required', null,true);
	}
	 if ($advertisment_id && !is_new_advertisment($advertisment_id))
	{
		label_row(_("Location:"), $_POST['location']);
		hidden('location', $_POST['location']);
	} 
	else 
	{
		adv_location_row(_("Location:"), 'location', null,true);
	}
	
		
	textarea_row(_("Required Skills:"), 'required_skills', null, 22, 5);	
	
	
	if($advertisment_id)
		record_status_list_row(_("Unit status:"), 'inactive');


	/*if ($employee_id)  {
		start_row();
		echo '<td class="label">'._('Click here to').'</td>';
	  	hyperlink_params_separate_td($path_to_root . "/emp2/reporting/prn_redirect.php?PARAM_0=$employee_id&REP_ID=1033",
			'<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Print")).'</b>');
		end_row();
	}
*/
	table_section_title(_("Others"));
	
	//------------------------------------------------------------------------------------
	if($advertisment_id)
	{
	file_row(_("Advertisment Image (.jpg)") . ":", 'pic', 'pic');
	}// Add Image upload for New Item  - by Joe
	$stock_img_link = "";
	$check_remove_image = false;
	
	if (isset($_POST['advertisment_id']) && file_exists(company_path().'/advertisment/'
		.trim($_POST['advertisment_id']).".jpg"))
	{
	$stock_img_link .= "<img id='item_img' alt = '[".$_POST['advertisment_id'].".jpg".
			"]' src='".company_path().'/advertisment/'.trim($_POST['advertisment_id']).
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
	if (!$advertisment_id)
	{
		submit_center('addupdate', _("Add New Advertisment"), true, '', 'default');
	} 
	else 
	{
		submit_center_first('addupdate', _("Update Advertisment"), 
		  _('Update Advetisment data'), @$_REQUEST['popup'] ? true : 'default');
		submit_return('select', $advertisment_id, _("Select this Advertisment and return to document entry."));
		submit_center_last('delete', _("Delete Advertisment"), 
		  _('Delete Advertisment data if have been never used'), true);
	}
	

	div_end();
}

//--------------------------------------------------------------------------------------------
start_form(true);

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	  advertisment_list_cells(_("Select a Advertisment: "), 'advertisment_id', null,
		  _('New advertisment'), true, check_value('show_inactive')); 
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('advertisment_id');
		set_focus('advertisment_id');
	}
} 
else 
{
	hidden('advertisment_id');
}



div_start('details');

if (!$advertisment_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer


	
	
supplier_settings($advertisment_id); 

br();
tabbed_content_end();

div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);
?>