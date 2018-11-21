<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Attendance"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");


check_db_has_tax_groups(_("There are no tax groups defined in the system. At least one tax group is required before proceeding."));

if (isset($_GET['emp_id'])) 
{
	$_POST['emp_id'] = $_GET['emp_id'];
}

$employee_id = get_post('employee_id'); 
//--------------------------------------------------------------------------------------------
function supplier_settings(&$emp_id)
{

	start_outer_table(TABLESTYLE2);

	table_section(1);

	if ($employee_id) 
	{
		//SupplierID exists - either passed when calling the form or from the form itself
//		$myrow = get_employee($_POST['supplier_id']);
		$myrow = get_employee($_POST['emp_id']);

		
		$_POST['date'] = $myrow["date"];
		$_POST['attend_type']  = $myrow["attend_type"];
		$_POST['time_in']  = $myrow["time_in"];
		$_POST['time_out']  = $myrow["time_out"];
		$_POST['attend_type']  = $myrow["attend_type"];
		
	} 
	else 
	{
		$_POST['date'] = 
		$_POST['attend_type']  = 
		$_POST['time_in'] = 
		$_POST['time_out']  = 
		$_POST['attend_type']  = 
		'';}
		
		


	table_section_title(_("Attendance  Record"));
	
	date_row(_("Date"),'date', null,null, 0, 0, 0, null, true);  
		
	if ($emp_id && !is_new_emp($emp_id)) 
	{
		label_row(_("Type:"), $_POST['attend_type']);
		hidden('attend_type', $_POST['attend_type']);
	} 
	else 
	{
		emp_attend_type_row(_("Type:"), 'attend_type', null,true);
	}
	
/*	function date_row($label, $name, $title=null, $check=null, $inc_days=0, $inc_months=0, 
	$inc_years=0, $params=null, $submit_on_change=false)
{
	echo "<tr><td class='label'>$label</td>";
	date_cells(null, $name, $title, $check, $inc_days, $inc_months, 
		$inc_years, $params, $submit_on_change);
	echo "</tr>\n";
}*/			
		/*	date_row(_("Date of joining"),$date_text, 'j_date',
			$j_date->trans_no==0, 0, 0, 0,'', null, true); 
			
			date_row(_("Date of leaving"),$date_text, 'l_date', 
			$l_date->trans_no==0, 0, 0, 0,'', null, true);  
*/

	text_row(_("Time In:"), 'time_in', null, 42, 40);
	text_row(_("Time Out:"), 'time_out', null, 13, 13);
	
}
	
	
	if ($emp_id)  {
		start_row();
		echo '<td class="label">'._('Click here to').'</td>';
	  	hyperlink_params_separate_td($path_to_root . "/emp2/reporting/prn_redirect.php?PARAM_0=$employee_id&REP_ID=1033",
			'<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("Print")).'</b>');
		end_row();
	}

//http://hisaab.pk/employee/reporting/prn_redirect.php?PARAM_0=298-13&PARAM_1=298-13&PARAM_2=0&PARAM_3=0&PARAM_4=&PARAM_5=0&REP_ID=110
//, "PARAM_0=".$employee_id.(@$_REQUEST['popup'] ? '&popup=1':'')


?>