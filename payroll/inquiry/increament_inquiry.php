<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

//include_once($path_to_root . "/payroll/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
if (!@$_GET['popup'])
{
    $js = "";
    if ($SysPrefs->use_popup_windows)
        $js .= get_js_open_window(900, 500);
    if (user_use_date_picker())
        $js .= get_js_date_picker();
    page(_($help_context = "Increament Inquiry"), isset($_GET['emp_id']), false, "", $js);
}
if (isset($_GET['emp_id'])){
    $_POST['emp_id'] = $_GET['emp_id'];
}
if (isset($_GET['FromDate'])){
    $_POST['TransAfterDate'] = $_GET['FromDate'];
}
if (isset($_GET['ToDate'])){
    $_POST['TransToDate'] = $_GET['ToDate'];
}

if (isset($_GET['id'])){
    $_POST['id'] = $_GET['id'];
}

/*

if ($employee_id && !is_new_employee($employee_id))
	{
		label_row(_("Department:"), $_POST['emp_dept']);
		hidden('emp_dept', $_POST['emp_dept']);
	}
	else
	{
		emp_dept_row(_("Department:"), 'emp_dept', null);
	}	*/
//------------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

if (!isset($_POST['emp_id']))
    $_POST['emp_id'] = get_global_supplier();

//for second line
start_table(TABLESTYLE_NOBORDER);
start_row();
if (!@$_GET['popup'])


    /*dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
    pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
    loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);*/
//emp_dept_cells(_("Department:"), 'emp_dept', null,true);
//emp_grade_cells(_("Grade:"), 'emp_grade', null,true);
//approve_list_cells(_("Approval:"),'approve', $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false);
    end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

if (!@$_GET['popup'])
    //employee_list2_cells(_("Select a Employee:"), 'employee_id', null, true, false, false, !@$_GET['popup']);

//date_cells(_("From:"), 'TransAfterDate', '', null, -30);
//date_cells(_("To:"), 'TransToDate');

//supp_transactions_list_cell("filterType", null, true);

    employee_list_cells(_("Employee:"), 'emp_id', null,true);
//	emp_desg_cells(_("Designation:"), 'emp_desig', null,true);
date_cells(_("From:"), 'datefrom', '', null, -30);
date_cells(_("To:"), 'dateto');

submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();
set_global_supplier($_POST['emp_id']);

//------------------------------------------------------------------------------------------------
/*
function display_supplier_summary($supplier_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');


    start_table(TABLESTYLE, "width=80%");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));

	table_header($th);
    start_row();
	label_cell($supplier_record["curr_code"]);
    label_cell($supplier_record["terms"]);
    amount_cell($supplier_record["Balance"] - $supplier_record["Due"]);
    amount_cell($supplier_record["Due"] - $supplier_record["Overdue1"]);
    amount_cell($supplier_record["Overdue1"] - $supplier_record["Overdue2"]);
    amount_cell($supplier_record["Overdue2"]);
    amount_cell($supplier_record["Balance"]);
    end_row();
    end_table(1);
} */

//ansar edit

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if (($_POST['emp_id'] != "") && ($_POST['emp_id'] != ALL_TEXT))
{
    //$supplier_record = get_supplier_details($_POST['supplier_id'], $_POST['TransToDate']);
    //display_supplier_summary($supplier_record);
}
div_end();

if(get_post('RefreshInquiry'))
{
    $Ajax->activate('totals_tbl');
}

//------------------------------------------------------------------------------------------------
function systype_name($dummy, $type)
{
    global $systypes_array;
    return $systypes_array[$type];
}

function trans_view($trans)
{
    return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
    return ($row["type"]== ST_SUPPINVOICE) || ($row["type"]== ST_SUPPCREDIT) ? $row["due_date"] : '';
}

/*function gl_view($row)
{
	return get_gl_view_str($row["type"], $row["trans_no"]);
}

function credit_link($row)
{
	if (@$_GET['popup'])
		return '';
	return $row['type'] == ST_SUPPINVOICE && $row["TotalAmount"] - $row["Allocated"] > 0 ?
		pager_link(_("Credit This"),
			"/purchasing/supplier_credit.php?New=1&invoice_no=".
			$row['trans_no'], ICON_CREDIT)
		: '';
}

function fmt_debit($row)
{
	$value = $row["TotalAmount"];
	return $value>0 ? price_format($value) : '';

}

function fmt_credit($row)
{
	$value = -$row["TotalAmount"];
	return $value>0 ? price_format($value) : '';
}*/
function get_employee_name($row)
{

    $sql = " SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($row['emp_id']);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_month_name($row)
{
    $sql = "SELECT description AS month_name FROM ".TB_PREF."month WHERE id=".db_escape($row['valid_from']);

    $result = db_query($sql, "could not get month name");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_incr_amount($row)
{
    $sql = "SELECT increament_amount FROM ".TB_PREF."increment WHERE emp_id=".db_escape($row['emp_id'])." ORDER BY id  DESC ";

    $result = db_query($sql, "could not get month name");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_last_amount($row)
{
    $sql = "SELECT 	last_salary FROM ".TB_PREF."increment WHERE emp_id=".db_escape($row['emp_id'])." ORDER BY id  DESC ";

    $result = db_query($sql, "could not get month name");

    $row = db_fetch_row($result);

    return $row[0];
}
/*function get_division_name($id,$type)
{

	$sql = "SELECT name FROM ".TB_PREF."dimensions
	WHERE id=".db_escape($id)." AND type_=".db_escape($type)." ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow['0'];
}*/

function emp_name($row)
{
    return get_employee_name($row['emp_id']);
}
function current_salary($row)
{
    $curr = get_employee_data($row['emp_id']);
    return $curr['basic_salary'];
}
/*function emp_division_name($row)
{
	return get_division_name($row['division'],1);
}
function emp_project_name($row)
{
	return get_division_name($row['project'],2);
}
function emp_location_name($row)
{
	return get_division_name($row['location'],3);
}*/

function prt_link($row)
{
    if ($row['type'] == ST_SUPPAYMENT || $row['type'] == ST_BANKPAYMENT || $row['type'] == ST_SUPPCREDIT)
        return print_document_link($row['trans_no']."-".$row['type'], _("Print Remittance"), true, ST_SUPPAYMENT, ICON_PRINT);
}

function check_overdue($row)
{
    return $row['OverDue'] == 1
        && (abs($row["TotalAmount"]) - $row["Allocated"] != 0);
}

//function edit_link($row)
//{
//    if (@$_GET['popup'])
//
//    return pager_link(
//        "/payroll/manage/increment.php??id=" .$row['id']."", ICON_EDIT);
//}
function edit_link($row)
{
    if (@$_GET['popup'])
        //return '';

        $modify = 'id';
    return pager_link( _("Edit"),
        "/payroll/manage/increment_delete.php?id=".$row['id'], ICON_EDIT);
}

function delete_emp_info($delete)
{
    $sql="DELETE FROM ".TB_PREF."leave WHERE id=".db_escape($delete);
    db_query($sql,"could not delete Leave");
    display_notification(_('Selected leave  has been deleted'));
}
//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($emp_id,$datefrom,$dateto)
{
    $datefrom= sql2date($datefrom);
    $dateto= sql2date($dateto);

    $sql = "SELECT `id`, `increment_code`,increment_date,valid_from,remarks ,emp_id
			FROM 0_increment where increment_date>='$datefrom' AND increment_date <='$dateto' ";

    if($emp_id !='')
        $sql.=" AND emp_id=".db_escape($emp_id)."";

    return $sql;

}

$sql = get_sql_for_employee_inquiry($_POST['emp_id'],$_POST['datefrom'],$_POST['dateto']);

$cols = array(
    _("S No"),
    _("Increament Code"),
    _("Increament Date")=> array( 'type'=>'date', 'ord'=>'desc'),
    _("Valid From")=> array( 'type'=>'date', 'ord'=>'desc'),
    _("Employee Name") => array('fun'=>'get_employee_name'),
    _("Increament Amount")=> array('fun'=>'get_incr_amount'),
    _("Last salary")=> array('fun'=>'get_last_amount'),
    _("Current Salary")=> array('fun'=>'current_salary'),

    _("Remarks"),

);
array_append($cols, array(
	array('insert'=>true, 'fun'=>'edit_link')));




if ($_POST['emp_id'] != ALL_TEXT)
{
    $cols[_("Supplier")] = 'skip';
    $cols[_("Currency")] = 'skip';
}


//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "85%";



display_db_pager($table);

if (!@$_GET['popup'])
{
    end_form();
    end_page(@$_GET['popup'], false, false);
}
?>
