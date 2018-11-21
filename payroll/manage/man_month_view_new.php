<?php
$page_security = 'SA_HUMAIN_MAN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/man_month_new_db.inc"); //
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();

page(_($help_context = "Man Month "), @$_REQUEST['popup'], false, "", $js);

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
function can_process_for_man_month()
{
    for($i = 1; $i <= $_POST['count']; $i++)
    {
/*			if ($_POST['project_name'.$i] == 0 )
			{
				display_error(_("Please Select a project"));
				set_focus($_POST['project_name'.$i]);
				return false;
			}
           if ($_POST['man_month_value'.$i] > 1 )
			{
				display_error(_("The value should not be greater than 1"));
				set_focus('man_month_value'.$i);
				return false;
			}*/

//			if (strlen($_POST['absent'.$i]) == 0)
//			{
//				display_error(_("The absent cannot be empty."));
//				set_focus('absent'.$i);
//				return false;
//			}

    }
    return true;
}

    function handle_submit_man_month(&$selected_id,$date)
    {
        for($i = 1; $i <= $_POST['count']; $i++)
        {
           /*if ($_POST['project_name'.$i] != 0 && $_POST['man_month_value'.$i] == '')
            {
                display_error(_("Please insert Main month value"));
                set_focus('man_month_value'.$i);
                return false;
            }*/
            if ($_POST['employee_id'.$i] != 0 && $_POST['man_month_value'.$i] == ''  || $_POST['man_month_value'.$i] > 1)
            {
                display_error(_("The value can not be empty and  should not be greater than 1"));
                set_focus('man_month_value'.$i);
                return false;
            }
            elseif($_POST['employee_id'.$i] != 0 && $_POST['man_month_value'.$i] <= 1 ) {
                add_attendance_new($_POST['employee_name'.$i], $_POST['employee_id'.$i],$_POST['employee_dept'], $_POST['monthid'],
                    $_POST['date'],$_POST['f_year'], $_POST['man_month_value'.$i],$_POST['project'],$_POST['division'],$_POST['location']);
            }
/*            elseif ($_POST['project_name'.$i] == 0 && $_POST['man_month_value'.$i] < 1&& $_POST['man_month_value'.$i] != '')
            {
                display_error(_("Please Select a project"));
                set_focus($_POST['project_name'.$i]);
                return false;
            }*/
//            elseif ($_POST['project_name'.$i] != 0 && $_POST['man_month_value'.$i] > 1)
//            {
//                display_error(_("The value should not be greater than 1"));
//                set_focus('man_month_value'.$i);
//                return false;
//            }

        }
        display_notification(_("Employee record has been added."));
        set_focus('project');
    }

if (isset($_POST['Addattendance']))
{
    if(can_process_for_man_month())
    {
        $check = check_month_duplication_new($selected_id, $month_id);
        if($check > 0)
        {
            display_error(_("Already Inserted"));
            set_focus('project');
        }
        handle_submit_man_month($selected_id);
    }
}

if(isset($_POST['update']))
{
    if(can_process_for_man_month())
    {
        for($i =1; $i <= $_POST['count_update']; $i++)
        {
            update_attendance_new($_POST['employee_id'.$i],$_POST['employee_dept'], $_POST['monthid'],
                $_POST['date'],$_POST['f_year'], $_POST['man_month_value'.$i],$_POST['project'],$_POST['division'],$_POST['location']);
        }
        display_notification(_("Employee record has been added.".$_POST['count_update']));
        set_focus('project');
    }
}
if(isset($_POST['delete']))
{
    if(can_process_for_man_month())
    {
        for($i =1; $i <= $_POST['count_update']; $i++)
        {
            delete_data($_POST['employee_id'.$i], $_POST['monthid'], 
            $_POST['f_year'], $_POST['project'], $_POST['division'], $_POST['location']);
        }
        display_notification(_("Employees  record has been Deleted"));
        set_focus('project');
    }
}
///=======================================================
function customer_settings_new($selected_id, $id_month)
{
    $f_year = get_current_fiscalyear();
    $check = get_emp_attendance_count_new($_POST['project'],$_POST['division'],$_POST['location'],$id_month,$f_year['id']);
    if($check == 0)
    {
        echo "<center><h3>Insert New Record</h3></center>";

        $row = get_emp_through_dept_id_new(0,$_POST['division'],$_POST['location'],$_POST['project'],$f_year['id'],$id_month);
        start_outer_table(TABLESTYLE2);
        table_section(1);
        echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>";
     //	<td colspan='' class='tableheader'>&nbsp; Project Name &nbsp;</td>
		 echo "<td colspan='' class='tableheader'>&nbsp; Previous Man Month &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Man Month Value &nbsp;</td></tr>";
        $a = 1;

        while($myrow = db_fetch($row))
        {
            unset($_POST['man_month_value'.$a]);
            echo "<tr>";
            label_cell(get_employee_name_man_month($myrow['employee_id']));
//            man_month_pro_list(null, 'project_name'.$a, $_POST['project'.$a], 'Select Project',$myrow['employee_id']);

            $pre_month = get_manmont_pre_month($id_month-1,$_POST['f_year'],$myrow['employee_id'],$_POST['division'],$_POST['location'],$_POST['project']);

            if($id_month==1)
            {
                $pre_month = get_manmont_pre_month($id_month+11,$_POST['f_year'],$myrow['employee_id'],$_POST['division'],$_POST['location'],$_POST['project']);
            }

            $curr_month_man = get_manmont_pre_month($id_month,$_POST['f_year'],$myrow['employee_id']);
            label_cell($pre_month);

            if($curr_month_man != '')
                text_cells( null,'man_month_value'.$a, $curr_month_man, 20, 60, false,'','','', 'Over Time');
            else
                text_cells( null,'man_month_value'.$a, $pre_month, 20, 60, false,'','','', 'Over Time');

            hidden('employee_name'.$a, get_employee_name_man_month($myrow['employee_id']));
            hidden('employee_id'.$a, $myrow['employee_id']);
            hidden('monthid', $id_month);
            hidden('count', $a);
            $a++;
            echo "</tr>";
            $check = $myrow['man_month'];
        } //while
        end_outer_table(1);
        div_start('controls');
//        $row = check_entry($_POST['division'],$_POST['location'],$_POST['project'],$f_year['id'],$id_month);

//        if($row != 1)
        submit_center('Addattendance', _("Add "), true, '', 'default');
//        else
//            display_notification("Man month Already Added");

        div_end();
    }
    else
    {
        echo "<center><h3>Update Record</h3></center>";
//		$row = get_employee_through_dept_id($selected_id);
//        $row = get_employee_that_exist_update_new($_POST['project'],$_POST['division'],$_POST['location'],$id_month,$f_year['id']);
        $row = get_emp_through_dept_id_new(0,$_POST['division'],$_POST['location'],$_POST['project'],$f_year['id']);
        $emp_acc_dept = get_employees_acc_dept_new($selected_id);
        $_POST['count'] =  $emp_acc_dept;
        start_outer_table(TABLESTYLE2);
        table_section(1);
        echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Previous Month &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Man Month Value &nbsp;</td></tr>";
        //table_section_title(_("Employee:"));
        $a = 1;
        while($myrow = db_fetch($row))
        {
            echo "<tr>";
            label_cell(get_employee_name_man_month($myrow['employee_id']));
//		    label_cell($myrow['emp_name'], "", $myrow['emp_name']);
//            dimensions_list_cells( null,'project_name'.$a, $myrow['project'] ? $myrow['project'] : '', _("Select"), 60, false,'','','', 'project_name');
            $pre_month1 = get_manmont_pre_month($id_month-1,$_POST['f_year'],$myrow['employee_id'],$_POST['division'],$_POST['location'],$_POST['project']);

            $pre_month = get_manmont_pre_month($id_month,$_POST['f_year'],$myrow['employee_id'],$_POST['division'],$_POST['location'],$_POST['project']);

            if($id_month==1)
            {
                $pre_month1 = get_manmont_pre_month($id_month+11,$_POST['f_year'],$myrow['employee_id'],$_POST['division'],$_POST['location'],$_POST['project']);
            }

            label_cell($pre_month1);
            text_cells( null,'man_month_value'.$a, $pre_month , 20, 60, false,'','','', 'man_month_value');
//            text_cells( null,'absent'.$a, $myrow['absent'] ? $myrow['absent'] : '', 20, 60, false,'','','', 'Absent');
            hidden('employee_id'.$a, $myrow['employee_id']);
            hidden('project', $selected_id);
            hidden('monthid', $id_month);
            hidden('count_update', $a);
            $a++;
            echo "</tr>";
        }//while
        end_outer_table(1);
        div_start('controls');

      // submit_center_first('update', _("Update "), _('Update customer data'), @$_REQUEST['popup'] ? true : 'default');

        submit_center_first('delete', _("Delete Data"),_('Delete Employee data if have been never used'), true);

        div_end();
    }
}
$date = get_post('date', '');
start_form();
$f_year = get_post('f_year', '');
$month_id=get_post('month', '');
if (db_has_customers())
{
    start_table(TABLESTYLE_NOBORDER);
    start_row();
  /*  dimensions_list_cells(_("Divison"), 'divison', null, "Select Devision", " ", false, 1);
    dimensions_list_cells( _("Project:"), 'project', null,  _("Select Project: "), false, 2, 0, true);
    dimensions_list_cells(_("Location"), 'location', null, true, " ", false, 3);*/

    dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
    pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
    loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

    month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));

    $f_id= get_current_fiscalyear();
//   fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);

    date_cells(_("Date:"), 'date' , '');
    hidden('f_year', $f_id['id']);
    end_row();
    end_table();
    if (get_post('_show_inactive_update')) {
        $Ajax->activate('project');
        set_focus('project');
    }
}
else
{
    hidden('project');
}

if(!$selected_id)
{
    set_focus('project');
}
else
{
    if(!$month_id)
        set_focus('month');
    else
        customer_settings_new($selected_id, $month_id);
}
br();

hidden('popup', @$_REQUEST['popup']);
end_form();
//echo "selected_id ".$selected_id."<br /> month_id ".$month_id;
end_page();
?>