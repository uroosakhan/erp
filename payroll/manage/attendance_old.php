<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/attendance_db.inc"); //
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/payroll/includes/db/gl_setup_db.inc"); //
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Monthly Attendance"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

if (isset($_GET['debtor_no']))
{
	$_POST['project'] = $_GET['debtor_no'];
}

$selected_id = get_post('project','');
$month_id = get_post('month','');

//--------------------------------------------------------------------------------------------
function can_process()
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
//			if (strlen($_POST['present'.$i]) == 0)
//			{
//				display_error(_("The present field cannot be empty."));
//				set_focus('present'.$i);
//				return false;
//			}
//
//			if (strlen($_POST['absent'.$i]) == 0)
//			{
//				display_error(_("The absent cannot be empty."));
//				set_focus('absent'.$i);
//				return false;
//			}
//
		}
	return true;
}
//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id,$date)
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
		add_attendance($_POST['present'.$i], $_POST['overtime'.$i], $_POST['absent'.$i], $_POST['employee_id'.$i], $_POST['employee_dept'],
			$_POST['monthid'], $_POST['date'],$_POST['f_year'],$_POST['division'],$_POST['location']);
		}
	    display_notification(_("Employee record has been added."));
		set_focus('project');
}
//--------------------------------------------------------------------------------------------

if (isset($_POST['Addattendance']))
{
	  if(can_process())
	  {
		$check = check_month_duplication($selected_id, $month_id);
		if($check > 0)
		{
		 display_error(_("Aleardy Inserted"));
		 set_focus('project');
		}

		handle_submit($selected_id);
	  }
}

if(isset($_POST['update']))
{
		 if(can_process())
		  {
			    for($i =1; $i <= $_POST['count_update']; $i++)
				{
				update_attendance($_POST['present'.$i], $_POST['overtime'.$i], $_POST['absent'.$i], $_POST['employee_id'.$i], $_POST['monthid'],
					$_POST['date'],$_POST['f_year'],$_POST['divison'],$selected_id,$_POST['location']);
				}
				 display_notification(_("Employee record has been added.".$_POST['count_update']));
				 set_focus('project');

		  }
}

function customer_settings($selected_id, $id_month)
{

	    $check = get_emp_attendance_count($selected_id, $id_month,$_POST['f_year']);
		//echo "Check value = ".$check ;
		//$check = get_employee_existence($selected_id);
	//NOTE In this project presense table m dep_id use as project id
		if($check == 0)
		{
		echo "<center><h3>Insert New Record</h3></center>";
		$row = get_employee_that_exist_update($selected_id, $id_month);
//		$row = get_emp_through_dept_id($_POST['project'],$_POST['division'],$_POST['location']);
		$row = get_emp_through_dept_id(0,$_POST['division'],$_POST['location'],$_POST['project'],$_POST['f_year']);


		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent &nbsp;</td></tr>";
		$a = 1;
		$working_days = get_sys_pay_pref('total_working_days');
		while($myrow = db_fetch($row))
		{

//            $_POST['present'.$a] = get_info_employee($myrow['employee_id'],$id_month,$_POST['f_year']);
			$working_days = get_sys_pay_pref('total_working_days');
            $_POST['present'.$a] =$myrow['man_month_value']*$working_days;
			$myrow['absent'.$a] = $working_days -$_POST['present'.$a] ;

//            display_error(get_info_employee($myrow['emp_name']));
		echo "<tr>";
		label_cell(get_employee_name($myrow['employee_id']));
		text_cells( null,'present'.$a, $_POST['present'.$a]/*$myrow['present'] ? $myrow['present'] : ''*/, 20, 60, false,'','','', 'Present');
		text_cells( null,'overtime'.$a, $myrow['over_time'] ? $myrow['over_time'] : '', 20, 60, false,'','','', 'Over Time');
		text_cells( null,'absent'.$a, $myrow['absent'.$a], 20, 60, false,'','','', 'Absent');
		//label_cell($myrow['check']);
		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('employee_dept', $selected_id);
		hidden('monthid', $id_month);
		hidden('divison', $_POST['divison']);
		hidden('location', $_POST['location']);

		hidden('count', $a);
		$a++;
		echo "</tr>";
		} //while
		end_outer_table(1);
		div_start('controls');
		//if($a != 1)
		submit_center('Addattendance', _("Add Attendance"), true, '', 'default');
		div_end();
		}
		else
		{
		echo "<center><h3>Update Record</h3></center>";
//		$row = get_employee_through_dept_id($selected_id);
		$row = get_employee_that_exist_update($selected_id,$_POST['divison'], $id_month,$_POST['f_year'],$_POST['location']);
        $emp_acc_dept = get_employees_acc_dept($selected_id);
		$_POST['count'] =  $emp_acc_dept;
		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent &nbsp;</td></tr>";
		//table_section_title(_("Employee:"));
		$a = 1;
		while($myrow = db_fetch($row))
		{
		echo "<tr>";
		label_cell(get_employee_name($myrow['employee_id']));
//		label_cell($myrow['emp_name'], "", $myrow['emp_name']);
		text_cells( null,'present'.$a, $myrow['present'] ? $myrow['present'] : '', 20, 60, false,'','','', 'Present');
		text_cells( null,'overtime'.$a, $myrow['over_time'] ? $myrow['over_time'] : '', 20, 60, false,'','','', 'Over Time');
		text_cells( null,'absent'.$a, $myrow['absent'] ? $myrow['absent'] : '', 20, 60, false,'','','', 'Absent');

		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('project', $selected_id);
		hidden('monthid', $id_month);
		hidden('count_update', $a);
		$a++;
		echo "</tr>";
		}//while
        end_outer_table(1);
		div_start('controls');
		submit_center_first('update', _("Update Attendance"),
		  _('Update customer data'), @$_REQUEST['popup'] ? true : 'default');
		div_end();
		}
}

//--------------------------------------------------------------------------------------------

$date = get_post('date','');


start_form();
$f_year=get_post('f_year','');
if (db_has_customers())
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();

	/*dimensions_list_cells( _("Project:"), 'emp_dept', null,  _("Select department: "),false, 1);
	dimensions_list_cells(_("Divison")." 1", 'divison', null, true, " ", false, 2);
	dimensions_list_cells(_("Location")." 1", 'location', null, true, " ", false, 3);
	*/

	dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
	pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
	loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

	month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));

	$f_year = get_current_fiscalyear();
//    fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
	date_cells(_("Date:"), 'date' , '');
    hidden('f_year', $f_year['id']);
	//emp_month_list_cell('month');
	//check_cells(_("Show inactive:"), 'show_inactive', null, true);
	end_row();
	end_table();
//var_dump(get_post('f_year'));
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('project');
		set_focus('project');
	}
}
else
{
	hidden('project');
}


/*if(!$selected_id)
{
set_focus('project');
}
else
{*/
	if(!$month_id)
	set_focus('month');
	else
    customer_settings($selected_id, $month_id);
//}
br();

hidden('popup', @$_REQUEST['popup']);
end_form();
//echo "selected_id ".$selected_id."<br /> month_id ".$month_id;
end_page(@$_REQUEST['popup']);

?>