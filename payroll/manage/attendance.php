<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/attendance_db.inc");
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

function employee_leave_attendance_duplication_new($emp_id,$f_year)
{

    $sql ="SELECT COUNT(*) FROM ".TB_PREF."leave i WHERE  NOW() between i.`from_date` AND i.`to_date` AND `emp_id`=$emp_id AND `f_year`=$f_year";

    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function check_office_offday($off_day)
{

    $sql ="SELECT COUNT(*) 
FROM  `".TB_PREF."sys_pay_pref` 
WHERE  `name` LIKE  '%$off_day%'";

    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_emp_info_no_new($emp_id,$from_date)
{

    $sql = "SELECT COUNT(id) FROM ".TB_PREF."leave WHERE 
	emp_id =".db_escape($emp_id)."
	AND
	from_date=".db_escape(sql2date($from_date));
    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
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


function handle_submit($selected_id,$date)
{

	    for($i = 1; $i <= $_POST['count']; $i++)
		{
            $check = $_POST['check_in_hour'.$i];
            $chkh=$_POST['check_in_minute'.$i];
            $checkout = $_POST['check_out_hour'.$i];
            $chkhout=$_POST['check_out_minute'.$i];
             $sick=$_POST['sick'.$i];
            $month1 = date("m",strtotime(date2sql($_POST['date'])));
            $get_day = date("D",strtotime(date2sql($_POST['date'])));
            $off_day=check_office_offday($get_day);
            //display_error($off_day);
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

            $duplication1=employee_leave_attendance_duplication_new($_POST['employee_id'.$i],$_POST['f_year']);
            $duplication2=get_emp_info_no_new($_POST['employee_id'.$i],date2sql($_POST['date']));
           // if($off_day==0) {
                if ($duplication1 > 0 || $duplication2 > 0) {
                    $input_error = 1;
                    display_error(_("This " . $_POST['employee_id' . $i] . " Is Currently on leave"));
                } 
else {
                    add_attendance_neww($_POST['employee_id' . $i], $_POST['employee_dept'],
                        $month, $_POST['date'], $_POST['f_year'], $_POST['division'], $_POST['location'], $check, $chkh, $checkout, $chkhout, $sick);
  display_error(_("Attendance Marked"));
                }
           // }
            //else{
              //  display_error(_("Today is a off day"));
            //}

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

function customer_settings($selected_id, $id_month)
{

	    $check = get_emp_attendance_count_new( $id_month,$_POST['f_year']);
//		if($check == 0)
//		{
		echo "<center><h3>Insert New Record</h3></center>";
		$row = get_employee_that_exist_update($selected_id, $id_month);
		$row = get_emp_through_dept_id_new(0,$_POST['division'],$_POST['location'],$_POST['project'],$_POST['f_year']);


		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr>
        <td colspan='' class='tableheader'> &nbsp; Emp ID &nbsp; </td>		
        <td colspan='' class='tableheader'> &nbsp; Employee Name &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
		<td colspan='' style='width: 145px;' class='tableheader'>&nbsp; In Time &nbsp;</td>
		<td colspan='' style='width: 145px;' class='tableheader'>&nbsp; Out Time &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp;Sick Leave &nbsp;</td>
	<td colspan='' class='tableheader'> &nbsp; Casual Leave &nbsp; </td>
	<td colspan='' class='tableheader'> &nbsp; Pay Leave &nbsp; </td>
		<td colspan='' class='tableheader'> &nbsp; Official Leave&nbsp; </td>
		<td colspan='' class='tableheader'> &nbsp; Absent&nbsp; </td>


		</tr>";
		$a = 1;
		$working_days = get_sys_pay_pref('total_working_days');
		while($myrow = db_fetch($row))
		{


		echo "<tr>";
            label_cell($myrow['employee_id']);
            label_cell(get_employee_name($myrow['employee_id']));

            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"5"), "align='center'");

            intime_list_cells(null, 'check_in_hour'.$a, 'check_in_minute'.$a, 'check_in_am_pm'.$a, null, '', '', false);
            intimepm_list_cells(null, 'check_out_hour'.$a, 'check_out_minute'.$a, 'check_out_am_pm'.$a, null, '', '', false);

            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"1"), "align='center'");///
            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"2"), "align='center'");
            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"3"), "align='center'");
            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"4"), "align='center'");

            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"6"), "align='center'");

		//label_cell($myrow['check']);
		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('employee_dept', $selected_id);
		hidden('monthid', $id_month);
		hidden('divison', $_POST['divison']);
		hidden('location', $_POST['location']);

            hidden('project', $_POST['project']);

		hidden('count', $a);
		$a++;
		echo "</tr>";
		} //while
		end_outer_table(1);
		div_start('controls');
		//if($a != 1)
		submit_center('Addattendance', _("Add Attendance"), true, '', 'default');
		div_end();
	//	}
//		else
//		{
//		echo "<center><h3>Update Record</h3></center>";
////		$row = get_employee_through_dept_id($selected_id);
//		$row = get_employee_that_exist_update($selected_id,$_POST['divison'], $id_month,$_POST['f_year'],$_POST['location']);
//        $emp_acc_dept = get_employees_acc_dept($selected_id);
//		$_POST['count'] =  $emp_acc_dept;
//		start_outer_table(TABLESTYLE2);
//		table_section(1);
//		echo "<tr>
//               <td colspan='' class='tableheader'> &nbsp; Emp ID &nbsp; </td>
//        <td colspan='' class='tableheader'> &nbsp; Employee Name &nbsp; </td>
//		<td colspan='' class='tableheader'>&nbsp; Present &nbsp;</td>
//		<td colspan='' style='width: 145px;' class='tableheader'>&nbsp; In Time &nbsp;</td>
//		<td colspan='' style='width: 145px;' class='tableheader'>&nbsp; Out Time &nbsp;</td>
//		<td colspan='' class='tableheader'>&nbsp;Sick Leave &nbsp;</td>
//	<td colspan='' class='tableheader'> &nbsp; Casual Leave &nbsp; </td>
//	<td colspan='' class='tableheader'> &nbsp; Pay Leave &nbsp; </td>
//		<td colspan='' class='tableheader'> &nbsp; Official Leave&nbsp; </td>
//
//		</tr>";
//		//table_section_title(_("Employee:"));
//		$a = 1;
//		while($myrow = db_fetch($row))
//		{
//		echo "<tr>";
//            label_cell($myrow['employee_id']);
//            label_cell(get_employee_name($myrow['employee_id']));
//
//            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"5"), "align='center'");
//
//            intime_list_cells(null, 'check_in_hour'.$a, 'check_in_minute'.$a, 'check_in_am_pm'.$a, null, '', '', false);
//            intimepm_list_cells(null, 'check_out_hour'.$a, 'check_out_minute'.$a, 'check_out_am_pm'.$a, null, '', '', false);
//
//            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"1"), "align='center'");///
//            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"2"), "align='center'");
//            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"3"), "align='center'");
//            label_cell(radio2(null, 'sick'.$a, true,null,false,null,"4"), "align='center'");
//
//		hidden('employee_id'.$a, $myrow['employee_id']);
//		hidden('project', $selected_id);
//		hidden('monthid', $id_month);
//		hidden('count_update', $a);
//		$a++;
//		echo "</tr>";
//		}//while
//        end_outer_table(1);
//		div_start('controls');
//		submit_center_first('update', _("Update Attendance"),
//		  _('Update customer data'), @$_REQUEST['popup'] ? true : 'default');
//		div_end();
//		}
}

$date = get_post('date','');


start_form();
$f_year=get_post('f_year','');
if (db_has_customers())
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
	dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
	pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
	loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

	month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));

	$f_year = get_current_fiscalyear();
//    fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
	date_cells(_("Date:"), 'date' , '');
    hidden('f_year', $f_year['id']);
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