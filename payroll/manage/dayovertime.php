<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/overtime_db.inc"); //
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Over Time Per Day"), @$_REQUEST['popup'], false, "", $js); 

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

if (isset($_GET['debtor_no'])) 
{
	$_POST['emp_dept'] = $_GET['debtor_no'];
}

$selected_id = $_GET['emp_dept'];
$date =  $_GET['date'];


//-----------------------------------------------------------------------------------------


function can_process() 
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
	
	
		}
	return true;
}
//-----------------------------------------------------------------------------------------
function handle_delete($selected_id,$date)
{
		delete_presence($selected_id,$date);

	    display_notification(_("Over Time record has been deleted."));
		
		set_focus('emp_dept');
}
if (isset($_POST['delete'])) 
{
	if(can_process())
	{
		
		handle_delete($_POST['emp_dept'],$_POST['date']);
		
	}
	
}

function customer_settings($selected_id,$date) 
{

	    echo "<center><h3>Delete Record </h3></center>";			

		$row = get_presence_through_dept_id($selected_id,$date);
		$_POST['date'] =  $date;
		$_POST['emp_dept']=$selected_id;
		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee &nbsp; </td>
		
	     <td colspan='' class='tableheader'>&nbsp; Over Time &nbsp;</td>";
		$a = 1;
		while($myrow = db_fetch($row))
		{
		echo "<tr>";
		$presentinfo=employee_present_attendance_detail($myrow['employee_id']);
		
		label_cell(get_employee_name($myrow['employee_id']));
			//check_cells(null, 'present'.$a , $myrow['present'] ? $myrow['present']:'','');
			text_cells( null,'over_time'.$a, $myrow['over_time'] ? $myrow['over_time'] : '', '');
		hidden('employee_id'.$a, $myrow['employee_id']);
		hidden('emp_dept', $selected_id);
		hidden('date', $date);
		
		hidden('count',$a );
		$a++;
		echo "</tr>";
		} //while
		end_outer_table(1);
		div_start('controls');
		if($a != 1)
		start_outer_table(1);
		submit_center_last('delete', _("Delete"), '', '', true);
		end_outer_table(1);
		div_end();	
}

//-----------------------------------------------------------------------------------------
start_form();

if (db_has_customers()) 
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();
   // emp_dept_cells( _("Department:"), 'emp_dept', null,  _("Select department: "), 
	//true, check_value('show_inactive'));
	//  date_cells(_("Date:"), 'date1' , '');

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

//if(!$selected_id)
//{
//set_focus('emp_dept');
//}
//else
//{
   customer_settings($selected_id,$date); 
//}
br();

hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>