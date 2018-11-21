<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/payroll/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 

if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Add Applicant Inquiry"), isset($_GET['add_id']), false, "", $js);
}
if (isset($_GET['add_id'])){
	$_POST['add_id'] = $_GET['add_id'];
}
if (isset($_GET['FromDate'])){
	$_POST['date_of_birth'] = $_GET['date'];
}
if (isset($_GET['ToDate'])){
	$_POST['date_of_birth'] = $_GET['end_date'];
}

//------------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
	start_form();
/*
if (!isset($_POST['employee_id']))
	$_POST['employee_id'] = get_global_supplier();
*/
start_table(TABLESTYLE_NOBORDER);
start_row();
/*
if (!@$_GET['popup'])
	employee_list2_cells(_("Qualifictaion:"), 'qualification', null, true, false, false, !@$_GET['popup']);
	*/
date_cells(_("From:"), 'date', '', null, -30);
date_cells(_("To:"), 'end_date');


applicant_national_cells(_("Nationality:"), 'nationality', null,true);	
emp_gen_cells(_("Gender:"), 'gender', null,true);

start_row();
ref_cells(_("Telephone Number:"),'telephone_no', '',null,'', true );
ref_cells(_("Mobile Number:"),'mobile_no', '',null,'', true );
ref_cells(_("Full Name:"),'full_name', '',null,'', true );

submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();
set_global_supplier($_POST['add_id']);

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if (($_POST['add_id'] != "") && ($_POST['add_id'] != ALL_TEXT))
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

function gl_view($row)
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
}

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
function edit_link($row) 
{
	if (@$_GET['popup'])
		//return '';
		
	$modify = 'add_id';
  return pager_link( _("Edit"),
    "/payroll/manage/add_applicant.php?add_id=".$row['add_id'], ICON_EDIT);
}


function get_gender($row)
{
$sql = "SELECT description FROM ".TB_PREF."gen WHERE id=".db_escape($row['gender']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_nationality($row)
{
$sql = "SELECT description FROM ".TB_PREF."nationality WHERE id=".db_escape($row['nationality']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($date, $end_date,$nationality,$gender,$telephone_no='',$mobile_no='',$full_name='')
{
       $sql = "SELECT ".TB_PREF."add_applicant.applicant_code,".TB_PREF."add_applicant.full_name,".TB_PREF."add_applicant.date_of_birth,".TB_PREF."add_applicant.nationality,".TB_PREF."add_applicant.present_address,".TB_PREF."add_applicant.permanent_address,".TB_PREF."add_applicant.telephone_no,".TB_PREF."add_applicant.mobile_no,".TB_PREF."add_applicant.gender,".TB_PREF."add_applicant.add_id
 
FROM ".TB_PREF."add_applicant

WHERE 
".TB_PREF."add_applicant.`date_of_birth`>='$date'

AND
".TB_PREF."add_applicant.`date_of_birth`<='$end_date'";




	if($nationality != ALL_TEXT)
    $sql .= " AND ".TB_PREF."add_applicant.`nationality`=".db_escape($nationality);
	
	if($gender != ALL_TEXT)
    $sql .= " AND ".TB_PREF."add_applicant.`gender`=".db_escape($gender);	
		
    if($telephone_no != ALL_TEXT)
    $sql .= " AND telephone_no LIKE ". db_escape("%$telephone_no%");
	
	  if($mobile_no != ALL_TEXT)
    $sql .= " AND mobile_no LIKE ". db_escape("%$mobile_no%");
	
	  if($full_name != ALL_TEXT)
    $sql .= " AND full_name LIKE ". db_escape("%$full_name%");		
		
   	return $sql;
}


$sql = get_sql_for_employee_inquiry(date2sql($_POST['date']), date2sql($_POST['end_date']),$_POST['nationality'],$_POST['gender'],$_POST['telephone_no'],$_POST['mobile_no'],$_POST['full_name']);
 $cols = array(
			_("Applicant Code")=> array('name'=>'applicant_code', 'type'=>'name', 'ord'=>'desc'),
			_("Full Name"), 
			_("Date of Birth")=> array('name'=>'date_of_birth', 'type'=>'date'),
			_("Nationality")=>array( 'fun'=>'get_nationality'),
			_("Present Address"),
			_("Permanent Address "),
			_("Telephone No"),
			_("Mobile No"),
			_("Gender")=>array( 'fun'=>'get_gender')
		     );
			 array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));

if ($_POST['add_id'] != ALL_TEXT)
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