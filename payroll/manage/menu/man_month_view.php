<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/man_month_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

$page_security = 'SA_PAYROLL_SETUP';

simple_page_mode(true);
$id=$_GET['employee_id'];

function get_emp_project_name_($dept_id)
{
    $sql = "SELECT id, CONCAT(reference,'  ',name) FROM ".TB_PREF."dimensions WHERE id= ".db_escape($dept_id);
    $result = db_query($sql, "could not get project");
    $row = db_fetch($result);
    return $row[1];
}

function get_employee_name_man_month($employee_id)
{
    $sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_man_month_value($employee_id,$month,$fiscal_yr)
{
	$sql = "SELECT SUM(man_month_value) FROM ".TB_PREF."man_month WHERE employee_id=".db_escape($employee_id)
	." AND month_id=$month  AND f_year=$fiscal_yr";
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
function check_employee_manmonth_duplication($employee_id,$month,$fiscal_yr,$project,$division,$location)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."man_month WHERE employee_id=".db_escape($employee_id)
		." AND month_id=$month  AND f_year=$fiscal_yr AND project_name=$project AND division=$division AND 	location=$location";
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
	$input_error = 0;
	if (strlen($_POST['man_month_value']) == 0 || $_POST['project']==0 || $_POST['division']==0 || $_POST['location']==0)
	{
		$input_error = 1;
		display_error(_("The Remaining Fields cannot be empty."));
		set_focus('man_month_value');
	}
	if($Mode=='ADD_ITEM' ) {
        $f_year = get_current_fiscalyear();
        $month_valu = get_employee_man_month_value($_POST['id'], $_POST['month_id'], $f_year['id']);
        $check = check_employee_manmonth_duplication($_POST['id'], $_POST['month_id'], $f_year['id'], $_POST['project'], $_POST['division'], $_POST['location']);
        $_total = $_POST['man_month_value'] + $month_valu;
        if ($_total >= 1.1) {
            $input_error = 1;
            display_error(_("The $_total man month value sholud be less than 1"));
            set_focus('man_month_value');
        }
        if ($check > 0) {
            $input_error = 1;
            display_error(_("Already Exist"));

        }
    }
    else{
        $f_year = get_current_fiscalyear();
        $month_valu = get_employee_man_month_value($_POST['id'], $_POST['month_id'], $f_year['id']);
         $_total = $_POST['man_month_value'];
        if ($_total >= 1) {
            $input_error = 1;
            display_error(_("The $_total man month value sholud be less than 1"));
            set_focus('man_month_value');
        }
    }
	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			update_man_month($selected_id,$_POST['employee_name'], $_POST['project'],
            $_POST['man_month_value'],$_POST['month_id'],$_POST['division'],$_POST['location']);
			$note = _('Selected field has been updated');
			//refresh('emp_allowance.php');
		}
		else {
			$result = validate_man_month($_POST['employee_name']);

//			if (!$result) {
			$f_year = get_current_fiscalyear();
			add_man_month($_POST['employee_name'] , $_POST['project'], $_POST['man_month_value']
			,$_POST['month_id'], $_POST['id'],$_POST['division'],$_POST['location'],$f_year['id']);
			
				$note = _('New field has been added');
				//refresh('man_month.php');
			//}
//			else{
//
//				display_error("Cannot Add Duplicate man month");
//
//			}
		}
		
		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'payroll_allowance', 'allow_id'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employee have been created using this dept."));
	}
	if ($cancel_delete == 0)
	{*/
	delete_man_month($selected_id);
	display_notification(_('Selected field has been deleted'));
	//refresh('man_month.php');
	//} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}



///=======================================================

$result = get_emp_man_month11($_GET['employee_id']);

start_form();
start_table(TABLESTYLE, "width=50%");
$th = array(_("Employee Name"), _("Employee Project"), _("Man Month Value"),
	_("Month"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) {

	alt_table_row_color($k);

	label_cell($myrow["employee_name"]);
	label_cell(get_emp_project_name_($myrow["project_name"]));
	label_cell($myrow["man_month_value"]);
	label_cell(get_month_name($myrow["month_id"]));


    //$_POST['month']  = $myrow["month"];

	//label_cell(get_employee_name11($myrow["emp_id"]));
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'man_month', 'text_field_1');
	edit_button_cell("Edit" . $myrow["id"], _("Edit"));
	delete_button_cell("Delete" . $myrow["id"], _("Delete"));
	end_row();
}
//inactive_control_row($th);
end_table(1);

//echo $emp_id;
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		$myrow = get_emp_man_month ($selected_id);


		$_POST['employee_name']  = $myrow["employee_name"];
		$_POST['project_name']  = get_emp_project_name_($myrow["project_name"]);
		$_POST['man_month_value']  = $myrow["man_month_value"];
		$_POST['month_id']  = 	$myrow["month_id"];
		$_POST['division']  = 	$myrow["division"];
		$_POST['project']  = 	$myrow["project_name"];
		$_POST['location']  = 	$myrow["location"];
		//$_POST['month']  = $myrow["month"];
	}
	hidden("selected_id", $selected_id);
	hidden("employee_name", $_POST['employee_name']);
	//$myrow["employee_name"]

//	label_row(_("text_field_1"), $myrow["text_field_1"]);
//	label_row(_("text_field_2"), $myrow["text_field_2"]);
//	label_row(_("text_field_3"), $myrow["text_field_3"]);
//	label_row(_("text_field_4"), $myrow["text_field_4"]);

}
//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
hidden("id", $id);
//emp_dept_row( _("Department:"), 'dept_id', null,  _("Select department: "),
//	true, check_value('show_inactive'));
///month_list_cells( _("Month:"), 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
$employee_name=get_employee_name_man_month($_GET['employee_id']);

text_row(_("Employee Name:"), 'employee_name',$employee_name,30,40);
//project_list_row(_("Project:"),'project_name',null,true);

dimensions_list_row(_("Divison"), 'division', null, 'All division', "", false, 1,true);
pro_list_row(_("Project"), 'project',null, 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

amount_row(_("Man Month Value:"), "man_month_value");
month_list_cells( _("Month:"), 'month_id', null, '', true);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');





if (!@$_GET['popup'])
{
	end_form();
	end_page(false);
}
?>