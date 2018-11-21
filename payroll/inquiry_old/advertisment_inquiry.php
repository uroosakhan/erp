<?php
$page_security = 'SS_PAYROLL';
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
	page(_($help_context = "Advertisment Inquiry"), isset($_GET['advertisment_id']), false, "", $js);
}
if (isset($_GET['advertisment_id'])){
	$_POST['advertisment_id'] = $_GET['advertisment_id'];
}
if (isset($_GET['FromDate'])){
	$_POST['date_of_advertisment'] = $_GET['FromDate'];
}
if (isset($_GET['ToDate'])){
	$_POST['close_date'] = $_GET['ToDate'];
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
date_cells(_("From:"), 'date_of_advertisment', '', null, -30);
date_cells(_("To:"), 'close_date');


adv_qualification_cells(_("Qualification:"), 'qualification', null,true);	
adv_experience_cells(_("Year of Experience:"), 'years_of_exp', null,true);	
emp_gen_cells(_("Gender:"), 'gender', null,true);
end_row();


start_row();
adv_age_cells(_("Age:"), 'age', null,true);
adv_salary_cells(_("Salary Range:"), 'salary_range', null,true);
emp_desg_cells(_("Designation:"), 'designation', null,true);
adv_job_cells(_("Job Type:"), 'job_type', null,true);
//adv_travel_cells(_("Travel Required:"), 'travel_required', null,true);
adv_location_cells(_("Location:"), 'location', null,true);



submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();
set_global_supplier($_POST['employee_id']);

//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if (($_POST['advertisment_id'] != "") && ($_POST['advertisment_id'] != ALL_TEXT))
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
		
	$modify = 'advertisment_id';
  return pager_link( _("Edit"),
    "/payroll/manage/advertisment.php?advertisment_id=".$row['advertisment_id'], ICON_EDIT);
}


function get_qualification($row)
{
$sql = "SELECT description FROM ".TB_PREF."qualification WHERE id=".db_escape($row['qualification']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_year_exp($row)
{
$sql = "SELECT description FROM ".TB_PREF."experience WHERE id=".db_escape($row['years_of_exp']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_gender($row)
{
$sql = "SELECT description FROM ".TB_PREF."gen WHERE id=".db_escape($row['gender']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_designation($row)
{
$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($row['designation']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_job_type($row)
{
$sql = "SELECT description FROM ".TB_PREF."job_type WHERE id=".db_escape($row['job_type']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_age12($row)
{
$sql = "SELECT description FROM ".TB_PREF."age WHERE id=".db_escape($row['age']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_salary_range12($row)
{
$sql = "SELECT description FROM ".TB_PREF."salary_range WHERE id=".db_escape($row['salary_range']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_travel_req($row)
{
$sql = "SELECT description FROM ".TB_PREF."travel WHERE id=".db_escape($row['travel_required']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_location123($row)
{
$sql = "SELECT description FROM ".TB_PREF."location_name WHERE id=".db_escape($row['location']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
//------------------------------------------------------------------------------------------------
function get_sql_for_employee_inquiry($datefrm ,$dateto,$qualif, $years_of_exp, $gender, $designation, $job_type,$age, $salary_range, $travel_required,$location)
{
       $sql = "SELECT ".TB_PREF."advertisment.advertisment_code,".TB_PREF."advertisment.description,".TB_PREF."advertisment.no_of_position,".TB_PREF."advertisment.newspaper,".TB_PREF."advertisment.website,".TB_PREF."advertisment.date_of_advertisment,".TB_PREF."advertisment.close_date,".TB_PREF."advertisment.qualification,".TB_PREF."advertisment.years_of_exp,".TB_PREF."advertisment.gender,".TB_PREF."advertisment.designation,".TB_PREF."advertisment.job_type,".TB_PREF."advertisment.age,".TB_PREF."advertisment.salary_range,".TB_PREF."advertisment.travel_required,".TB_PREF."advertisment.location,".TB_PREF."advertisment.required_skills,".TB_PREF."advertisment.advertisment_id
 
FROM ".TB_PREF."advertisment

WHERE 
".TB_PREF."advertisment.`date_of_advertisment`>='$datefrm'
AND
".TB_PREF."advertisment.`close_date`<='$dateto'";

	
	if($qualif != ALL_TEXT)
    $sql .= " AND ".TB_PREF."advertisment.`qualification`=".db_escape($qualif);
	
	
	if($years_of_exp != ALL_TEXT)
    $sql .= " AND ".TB_PREF."advertisment.`years_of_exp`=".db_escape($years_of_exp);
	
	if($gender != ALL_TEXT)
    $sql .= " AND ".TB_PREF."advertisment.`gender`=".db_escape($gender);
	
	if ($designation != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`designation`= ".db_escape($designation);
	
	if ($job_type != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`job_type`= ".db_escape($job_type);
	
	if ($age != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`age`= ".db_escape($age);
	
	if ($salary_range != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`salary_range`= ".db_escape($salary_range);
   
    if ($travel_required != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`travel_required`= ".db_escape($travel_required);
   
	if ($location != ALL_TEXT) 
	$sql .= " AND ".TB_PREF."advertisment.`location`= ".db_escape($location);
	
	
   	return $sql;
}


$sql = get_sql_for_employee_inquiry( date2sql($_POST['date_of_advertisment']), date2sql($_POST['close_date']),$_POST['qualification'],$_POST['years_of_exp'],$_POST['gender'],$_POST['designation'],$_POST['job_type'],$_POST['age'],$_POST['salary_range'],$_POST['travel_required'],$_POST['location']);
 $cols = array(
			_("Code"),
			_("Description"), 
			_("No of position"),
			_("Newspaper"),
			_("Website"),
			_("Start Date") => array('name'=>'date_of_advertisment', 'type'=>'date', 'ord'=>'desc'),
			_("End Date")=> array('name'=>'close_date', 'type'=>'date', 'ord'=>'desc'),
			_("Qualification")=>array( 'fun'=>'get_qualification'),
			_("Years of Experince")=>array( 'fun'=>'get_year_exp'),
			_("Gender")=>array( 'fun'=>'get_gender'),
			_("Designation")=>array( 'fun'=>'get_designation'),
			_("Job Type")=>array( 'fun'=>'get_job_type'),
			_("Age")=>array( 'fun'=>'get_age12'),
			_("Salary Range")=>array( 'fun'=>'get_salary_range12'),
			_("Travel Required")=>array( 'fun'=>'get_travel_req'),
			_("Location")=>array( 'fun'=>'get_location123'),
			_("Required Skills"),

		     );
			 array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));

if ($_POST['advertisment_id'] != ALL_TEXT)
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