<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/dayattendance_db.inc"); //
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Attendance"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

if (isset($_GET['debtor_no'])) 
{
	$_POST['emp_dept'] = $_GET['debtor_no'];
}

$selected_id = get_post('emp_dept','');



//-----------------------------------------------------------------------------------------
function can_process() 
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
		$input_error=0;
	if (!is_date($_POST['date1']))
	{
		display_error(_("The entered date for the payment is invalid."));
		set_focus('date1');
		$input_error = 1;
		return false;
	}
	
		}
	return true;
}
//-----------------------------------------------------------------------------------------










function handle_submit(&$selected_id)
{
	
	  for($i = 1; $i <= $_POST['count']; $i++)
		{
		add_attendance($_POST['present'.$i], $_POST['overtime'], $_POST['absent'], 

$_POST['employee_id'.$i], $_POST['employee_dept'], $_POST['monthid'], $_POST['date1'], 

$_POST['remark'.$i]);
	
		}
		display_notification(_("Employee record has been added."));
	    
		set_focus('emp_dept');
}
//-----------------------------------------------------------------------------------------


if (isset($_POST['Addattendance'])) 
{
	

	$error=0;
	  if(can_process())
	  {
		$check = check_month_duplication($selected_id,$_POST['date1'], $_POST

['remark']); 
		if($check > 0)
		{
		 display_error(_("Aleardy Inserted"));
		 $error=1;
		 set_focus('emp_dept');
		} 
		if($error==0)
		handle_submit($selected_id,$_POST['date1'], $_POST['remark']);
		
	  }
}



function customer_settings($selected_id) 
{

	    $check = get_emp_attendance_count($selected_id);
		//echo "Check value = ".$check ;
		
		if($check == 0)
		{
		echo "<center><h3>Insert New Record</h3></center>";			
//		
		$row = get_employee_through_dept_id($selected_id);
		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
	    <td colspan='' class='tableheader'>&nbsp; Remarks &nbsp;</td>";
		$a = 1;
		while($myrow = db_fetch($row))
		{
		echo "<tr>";
		
		label_cell(get_employee_name($myrow['employee_id']));
		check_cells(null, 'present'.$a, $myrow['present'] ? $myrow

['present']:'','');
		text_cells( null,'remark'.$a, $myrow['remark'] ? $myrow['remark'] :'','');
		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('employee_dept', $selected_id);
		
		hidden('count',$a);
		$a++;
		echo "</tr>";
		} //while
		//for select all
	

	
//end of select all
		end_outer_table(1);
		div_start('controls');
		if($a != 1)
		submit_center('Addattendance', _("Add Attendance"), true, '', 'default');
	 
		div_end();
		}
		else
		{
echo "<center><h3>Insert New Record</h3></center>";			

		$row = get_employee_through_dept_id($selected_id);
		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
	     <td colspan='' class='tableheader'>&nbsp; Remarks &nbsp;</td>";
		$a = 1;
		while($myrow = db_fetch($row))
		{
		echo "<tr>";
		$presentinfo=employee_present_attendance_detail($myrow['employee_id']);
		
		label_cell(get_employee_name($myrow['employee_id']));
			check_cells(null, 'present'.$a , $myrow['present'] ? $myrow

['present']:'','');
			text_cells( null,'remark'.$a, $myrow['remark'] ? $myrow['remark'] : 

'', '');
			//label_cell( $presentinfo[0]);
    //label_cell( $presentinfo['basic_salary']);
	//$monthstart =$_POST['date1'];// date("m/d/Y", strtotime($_POST['date1'].'/01/'.date('Y').' 00:00:00'));  
    //$monthend = date("m/d/Y", strtotime('-1 second',strtotime('+1 month',strtotime($_POST['date1'].'/01/'.date('Y').' 00:00:00')))); 


		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('employee_dept', $selected_id);
		
		hidden('count',$a );
		$a++;
		echo "</tr>";
		} //while
		//echo $monthstart;
//echo $monthend;
		end_outer_table(1);
		div_start('controls');
		if($a != 1)
		submit_center('Addattendance', _("Add Attendance"), true, '', 'default');
		
		div_end();
		}
}

//-----------------------------------------------------------------------------------------




start_form();


if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
    emp_dept_cells( _("Department:"), 'emp_dept', null,  _("Select department: "), 
	true, check_value('show_inactive'));
	  date_cells(_("Date:"), 'date1' , '');

	//text_cells( null,'date', $myrow['date'] ? $myrow['date'] : '', 20, 60, false,'','','', 'Date');
	end_row();
	end_table();

	if (get_post('_show_inactive_update')) {
		$Ajax->activate('emp_dept');
		set_focus('emp_dept');
	}
} 
else 
{
	hidden('emp_dept');
}


if(!$selected_id)
{
set_focus('emp_dept');
}
else
{
    customer_settings($selected_id); 
}
br();

hidden('popup', @$_REQUEST['popup']);

end_form();
end_page(@$_REQUEST['popup']);


?>