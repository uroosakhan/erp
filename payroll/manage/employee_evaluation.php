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

page(_($help_context = "Employee Evaluation"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/payroll/includes/db/employee_evaluation_db.inc");
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

	
	if ($upload_file == 'No')
	{
	display_error(_("Upload File false."));
	return false;	
	}

	return true;
}


function handle_submit($employee_id)
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
		
	if ($employee_id) 
	{
	 	update_employee_evaluation($_POST['employee_id'],$_POST['curr_date'],$_POST['quality_work'],$_POST['job_knwoledge'], $_POST['quantity_work'] , $_POST['result_orientation'] ,$_POST['depentability'], $_POST['initiative'], $_POST['team_work'], $_POST['adaptability'],  $_POST['conformance'], $_POST['final_result'], $_POST['remarks']);
		
		update_record_status($_POST['employee_id'], $_POST['inactive'],
			'employee_evaluation', 'employee_id');

		$Ajax->activate('employee_id'); 
		display_notification(_("Employee has been updated."));
		// in case of status change
			
	} 
	else 
	{ 	//it is a new customer

		 begin_transaction();
		add_employee_evaluation($_POST['curr_date'],$_POST['quality_work'],$_POST['job_knwoledge'], $_POST['quantity_work'] , $_POST['result_orientation'] ,$_POST['depentability'], $_POST['initiative'], $_POST['team_work'], $_POST['adaptability'],  $_POST['conformance'], $_POST['final_result'], $_POST['remarks']);
		
		display_notification(_("A New Employee has been added."));
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

	handle_submit($employee_id);
	
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
	delete_employee_evaluation($_POST['employee_id']);

		unset($_SESSION['employee_id']);
		$employee_id = '';
		$Ajax->activate('_page_body');
		$Ajax->activate('employee_id'); 
		display_notification(_("Employee has been Deleted."));
	 //end 
		
		
	}

}


function supplier_settings($employee_id) 
{
	global $pic_height;
	if ($employee_id) 
	{
		$myrow = get_employee_evaluation($_POST['employee_id']);

		$_POST['curr_date'] = sql2date($myrow["curr_date"]);
	    $_POST['quality_work'] = $myrow["quality_work"];
		$_POST['job_knwoledge'] = $myrow["job_knwoledge"];
	    $_POST['quantity_work']  = $myrow["quantity_work"];
		$_POST['result_orientation']  = $myrow["result_orientation"];
		$_POST['depentability']  = $myrow["depentability"];
		$_POST['initiative']  = $myrow["initiative"];
		$_POST['team_work'] = $myrow["team_work"];
		$_POST['adaptability'] = $myrow["adaptability"];		
		$_POST['conformance'] = $myrow["conformance"];	
		$_POST['final_result'] = $myrow["final_result"];
		$_POST['remarks'] = $myrow["remarks"];	
		//$_POST['inactive'] = $myrow["inactive"];
	} 
	else 
	{
		$_POST['curr_date'] = $_POST['quality_work'] = $_POST['job_knwoledge'] =$_POST['quantity_work']=$_POST['result_orientation'] = $_POST['depentability']=$_POST['initiative'] = $_POST['team_work'] = $_POST['adaptability'] = $_POST['conformance']= $_POST['final_result'] = $_POST['remarks'] = 
'';} 


	start_outer_table(TABLESTYLE2);

	table_section(1);
	
		table_section_title(_("Employee"));
	
	label_row(_("Employee:"), get_employee_name11($employee_id));
	label_row(_("Code:"), get_employee_code($employee_id));
	label_row(_("Grade:"), get_employee_grade($employee_id));
	label_row(_("Department:"), get_employee_dept($employee_id));
	
	label_row(_("Joining Date:"), sql2date(get_employee_date($employee_id))); 
    date_row(_("Current Date"),'curr_date', null,null, 0, 0, 0, null, true);
		if ($employee_id && !is_new_employee_eva($employee_id)) 
	{
		label_row(_("Quality of Work:"), $_POST['quality_work']);
		hidden('quality_work', $_POST['quality_work']);
	}
	else 
	{
		emp_category_row(_("Quality of Work:"), 'quality_work', null,true);
	}
				
	if ($employee_id && !is_new_employee_eva($employee_id)) 
	{
		label_row(_("Job Knowledge:"), $_POST['job_knwoledge']);
		hidden('job_knwoledge', $_POST['job_knwoledge']);
	}
	else 
	{
		emp_category_row(_("Job Knowledge:"), 'job_knwoledge', null,true);
	}
	
	if ($employee_id && !is_new_employee_eva($employee_id)) 
	{
		label_row(_("Quantity Work:"), $_POST['quantity_work']);
		hidden('quantity_work', $_POST['quantity_work']);
	}
	else 
	{
		emp_category_row(_("Quantity Work:"), 'quantity_work', null,true);
	}
	
	
	
	

	table_section(2);
	table_section_title(_("Particulars"));	

	
	
	 if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Result Orientation:"), $_POST['result_orientation']);
		hidden('result_orientation', $_POST['result_orientation']);
	} 
	else 
	{
		emp_category_row(_("Result Orientation:"), 'result_orientation', null,true);
	}
	
	
	  if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Depentability:"), $_POST['depentability']);
		hidden('depentability', $_POST['depentability']);
	} 
	else 
	{
		emp_category_row(_("Depentability:"), 'depentability', null,true);
	}
	
	
	
	  if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Initiative:"), $_POST['initiative']);
		hidden('initiative', $_POST['initiative']);
	} 
	else 
	{
		emp_category_row(_("Initiative:"), 'initiative', null,true);
	}
	
	if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Team Work:"), $_POST['team_work']);
		hidden('team_work', $_POST['team_work']);
	} 
	else 
	{
		emp_category_row(_("Team Work:"), 'team_work', null,true);
	}
	
	
		if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Adaptability:"), $_POST['adaptability']);
		hidden('adaptability', $_POST['adaptability']);
	} 
	else 
	{
		emp_category_row(_("Adaptability:"), 'adaptability', null,true);
	}
	
	
    if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Conformnace OR Organizational Goal:"), $_POST['conformance']);
		hidden('conformance', $_POST['conformance']);
	} 
	else 
	{
		emp_category_row(_("Conformance OR Organizational Goal:"), 'conformance', null,true);
	}
	
	
	if ($employee_id && !is_new_employee_eva($employee_id))
	{
		label_row(_("Final Result:"), $_POST['final_result']);
		hidden('final_result', $_POST['final_result']);
	} 
	else 
	{
		emp_final_result_row(_("Final Result:"), 'final_result', null,true);
	}
		
	textarea_row(_("Remarks:"), 'remarks', null, 22, 5);	
	
	
	if($employee_id)
		record_status_list_row(_("Employee Evaluation:"), 'inactive');

	end_outer_table(1);


	div_start('controls');
	if ($employee_id!=0)
	{
		submit_center_first('addupdate', _("Submit Evaluation"), 
		  _('Update Employee data'), @$_REQUEST['popup'] ? true : 'default');
	} 
	

	div_end();
}

//--------------------------------------------------------------------------------------------
start_form(true);

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	  employee_list_cells(_("Select a Employee: "), 'employee_id', null,
		  _('New Employee'), true, check_value('show_inactive')); 
	check_cells(_("Show inactive:"), 'show_inactive', null, true);
	
		submit_return('select', $employee_id, _("Select this Employee and return to document entry."));
	end_row();
	end_table();
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	if($employee_id !=0)
	{
//submit_center_first('addupdate', _("Submit Evaluation"), 
	//	  _('Update Employee data'), @$_REQUEST['popup'] ? true : 'default');
	}
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

if (!$employee_id)
	unset($_POST['_tabs_sel']); // force settings tab for new customer


	
	
supplier_settings($employee_id); 

br();
tabbed_content_end();

div_end();
hidden('popup', @$_REQUEST['popup']);
end_form();
//------------------------------------------------------------------------------------

end_page(@$_REQUEST['popup']);
?>