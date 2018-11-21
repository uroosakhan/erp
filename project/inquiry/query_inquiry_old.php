<?php
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");





 
if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(900, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Query Inquiry"), isset($_GET['debtor_no']), false, "", $js);
}
//if (isset($_GET['debtor_no'])){
	//$_POST['debtor_no'] = $_GET['debtor_no'];
//}
if (isset($_GET['FromDate'])){
	$_POST['date'] = $_GET['date'];
}
if (isset($_GET['ToDate'])){
	$_POST['date'] = $_GET['end_date'];
}
if (isset($_GET['status'])){
	$_POST['status'] = $_GET['status'];
}
if (isset($_GET['source_status'])){
	$_POST['source_status'] = $_GET['source_status'];
}
if (isset($_GET['name'])){
	$_POST['name'] = $_GET['name'];

}
if (isset($_GET['status'])) {
	$_SESSION['status'] = $_GET['status'];
}
if (isset($_GET['id'])){
	$_POST['id'] = $_GET['id'];
}
function get_task_count($inactive, $task_type, $status, $user_id, $date, $last_update, $priority)
{
	$last_update_ = sql2date($last_update);
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."task 
	WHERE inactive IN ($inactive)";
	$sql .= " AND task_type = ".db_escape($task_type);

	if($status != 0)
		$sql .= " AND status =".db_escape($status);
	if($user_id != 0)
		$sql .= " AND user_id=".db_escape($user_id);
	if($date != 0)
		$sql .= " AND start_date=".db_escape($date);
	if($last_update != 0)
		$sql .= " AND Stamp LIKE '%$last_update_%' ";
	if($priority != 0)
		$sql .= " AND priority = ".db_escape($priority);

	$result = db_query($sql, "could not get task type count");
	$row = db_fetch_row($result);
	return $row[0];
}

$user_id_status = $_GET['user_id'];
$user_name1 = strtoupper(get_user_name($user_id_status));
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
$hp_count1 = get_task_count(0, 1, 1,  $user_id, 0, 0, 1);
$hp_count2 = get_task_count(0, 1, 2, $user_id, 0, 0, 1);
$hp_count4 = get_task_count(0, 1, 4, $user_id, 0, 0, 1);
$hp_count5 = get_task_count(0, 1, 5, $user_id, 0, 0, 1);
$hp_count6 = get_task_count(0, 1, 6, $user_id, 0, 0, 1);
$hp_count7 = get_task_count(0, 1, 7, $user_id, 0, 0, 1);
$hp_count112 = get_task_count(0, 1, 0, $user_id, 0, 0, 1);

//------------------------------------------------------------------------------------------------
	echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/project/project.css" />';


	echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">';
	//echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">';
	//echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>';
	//echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>';
	echo '<style type="text/css">
    .bs-example{
    	margin: 20px;
    }
    .icon-input-btn{
        display: inline-block;
        position: relative;
    }
    .icon-input-btn input[type="submit"]{
        padding-left: 2em;
    }
    .icon-input-btn .glyphicon{
        display: inline-block;
        position: absolute;
        left: 0.65em;
        top: 30%;
    }
</style> ';

	echo '<script type="text/javascript">
$(document).ready(function(){
	$(".icon-input-btn").each(function(){
        var btnFont = $(this).find(".btn").css("font-size");
        var btnColor = $(this).find(".btn").css("color");
		$(this).find(".glyphicon").css("font-size", btnFont);
        $(this).find(".glyphicon").css("color", btnColor);
        if($(this).find(".btn-xs").length){
            $(this).find(".glyphicon").css("top", "24%");
        }
	}); 
});
</script> ';



if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['debtor_no']))
	$_POST['debtor_no'] = get_global_supplier();

start_table(TABLESTYLE_NOBORDER);
start_row();


label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-tasks'></span> 
<a href=../task.php?type=task color: black;  class='btn btn-info' '>&nbsp&nbsp&nbsp&nbsp ADD TASK</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-phone-alt'></span> 
<a href=../query.php? color: black;  class='btn btn-primary' '>&nbsp&nbsp&nbsp&nbsp ADD QUERY</a> 
</span></div></center>");


label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-phone'></span> 
<a href=../inquiry/call_log.php?  color: black; class='btn btn-warning' '>&nbsp&nbsp&nbsp&nbsp CALL LOG</a> 
</span></div></center>");

label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-calendar'></span> 
<a href=../inquiry/calender.php?  color: black; class='btn btn-success' '>&nbsp&nbsp&nbsp&nbsp VIEW CALENDAR</a> 
</span></div></center>");

/*
label_cell("<center> <div class='bs-example'>
<span class='icon-input-btn'><span class='glyphicon glyphicon-dashboard'></span> 
<a href=../view/view_all_history.php  color: black;  class='btn btn-default' '>&nbsp&nbsp&nbsp&nbsp VIEW HISTORY</a> 
</span></div></center>");
*/

if($_GET['status'] == 3)
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
	<button type='button' class='btn btn-default active'> ";
	if($hp_count3)
		echo "<button1 data-count=$hp_count3>";
	echo "<a href=../inquiry/query_inquiry.php?status=3>&nbsp&nbsp&nbspNOT ACTIVE&nbsp<span class='badge'></button1></span>
	</a></span></button>";
}
else
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
	<button type='button' class='btn btn-default'>";
	if($hp_count3)
		echo "<button1 data-count=$hp_count3>";
	echo "<a href=../inquiry/query_inquiry.php?status=3>&nbsp&nbsp&nbspNOT ACTIVE&nbsp<span class='badge'></span>
	</a></span></button1></button>
	";
}

if($_GET['status'] == 4)
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
	<button type='button' class='btn btn-default active'>";
	if($hp_count4 != 0)
		echo "<button1 data-count=$hp_count4>";
	echo "<a href=../inquiry/query_inquiry.php?status=4>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'></span>
	</a></span></button1></button>";
}
else
{
	echo "
	<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
	<button type='button' class='btn btn-default'> ";
	if($hp_count4 != 0)
		echo "<button1 data-count=$hp_count4>";
	echo "<a href=../inquiry/query_inquiry.php?status=4>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'></span>
	</a></span></button1></button>";
}


if($_GET['status'] == 2)
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
	<button type='button' class='btn btn-default active'> ";
	if($hp_count2 != 0)
		echo "<button1 data-count=$hp_count2>";
	echo "<a href=../inquiry/query_inquiry.php?status=2>&nbsp&nbsp&nbspPURCHASE ELSEWHERRE&nbsp<span class='badge'></span>
	</a></span></button>";
}
else
{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
<button type='button' class='btn btn-default'> "; if($hp_count2 != 0) echo "<button1 data-count=$hp_count2>"; echo "
<a href=../inquiry/query_inquiry.php?status=2>&nbsp&nbsp&nbspPURCHASE ELSEWHERRE&nbsp<span class='badge'></span>
</a></span></button>
";}



if($_GET['status'] == 1)
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/query_inquiry.php?status=1>&nbsp&nbsp&nbspACTIVE&nbsp<span class='badge'></span></a>
</span></button>";
}

else
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/query_inquiry.php?status=1>&nbsp&nbsp&nbspACTIVE&nbsp<span class='badge'></span></a>
</span></button>";
}

if($_GET['status'] == 112)
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count112 != 0) echo "<button1 data-count=$hp_count112 class=animation>"; echo "
<a href=../inquiry/query_inquiry.php?status=112>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'></span>
</a></span></button>
";
}
else
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-list'></span>
<button type='button' class='btn btn-default'> "; if($hp_count112 != 0) echo "<button1 data-count=$hp_count112 class=animation>"; echo "
<a href=../inquiry/query_inquiry.php?status=112>&nbsp&nbsp&nbsp&nbspALL&nbsp<span class='badge'></span>
</a></span></button>
";
}

echo "
  </div>
</div>
</center> 
";



end_row();
end_table();


start_table(TABLESTYLE_NOBORDER);
start_row();


if (!@$_GET['popup'])
	//employee_list2_cells(_("Select a Employee:"), 'employee_id', null, true, false, false, !@$_GET['popup']);
//customer_list_cells(_("customer:"), 'debtor_no', null,true);	
date_cells(_("From:"), 'date', '', null, -1865);
date_cells(_("To:"), 'end_date');
//status_query_list_cells(_("Status:"), 'status', null,true);
source_status_query_list_cells(_("Source:"), 'source_status', null,true);
users_query_list_cells(_("User:"), 'user', null,true);	
submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("Name:"), 'name', '',null, _('Enter memo fragment or leave empty'));
ref_cells(_("Name:"), 'name', '',null,'', true );
ref_cells(_("Mobile:"), 'mobile', '',null, '', true);
ref_cells(_("phone1:"), 'phone1', '',null, '', true);
ref_cells(_("phone2:"), 'phone2', '',null, '', true);
ref_cells(_("C/O:"), 'care_of', '',null, '', true);


end_row();
end_table();
set_global_supplier($_POST['debtor_no']);

//------------------------------------------------------------------------------------------------
/*
function display_supplier_summary($supplier_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');
	fhj

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
if (($_POST['debtor_no'] != "") && ($_POST['debtor_no'] != ALL_TEXT))
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
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
  return pager_link( _("Edit"),
    "/project/query.php?$modify=" .$row['id'], ICON_EDIT);
	
}
       $user_name= $_SESSION["wa_current_user"]->user;
function get_user_through_id($user_name)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_name);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}



//--------------------------------------saad1----------------------------------------------------------
/*
function get_sql_for_query_inquiry($start_date, $end_date, $status, $debtor_no)
{
$sql = "SELECT ".TB_PREF."query.`start_date`,".TB_PREF."query.`end_date`,".TB_PREF."status.description AS  status,".TB_PREF."debtors_master.debtor_ref,".TB_PREF."query.`description`,".TB_PREF."query.`remarks`,".TB_PREF."query.`id`
 FROM `".TB_PREF."query` 
INNER JOIN  ".TB_PREF."status ON ".TB_PREF."status.id=".TB_PREF."query.`status`
INNER JOIN  ".TB_PREF."debtors_master ON ".TB_PREF."debtors_master.`debtor_no`=".TB_PREF."query.`debtor_no` 
WHERE 
".TB_PREF."query.`start_date`>='$start_date'
AND
".TB_PREF."query.`start_date`<='$end_date'";

if($status != ALL_TEXT)
$sql .= " AND ".TB_PREF."query.`status`=".db_escape($status);
	
	if ($debtor_no != '') 
	{
   		$sql .= " AND ".TB_PREF."query.debtor_no = ".db_escape($debtor_no);
	}

   	return $sql;
	
}
*/
//...........................................saad2.....................
function get_sql_for_query_inquiry($date, $end_date, $status, $source_status, $user, $name='', $mobile, $phone1 ,$phone2 ,$care_of='')
{
	$sql = "SELECT ".TB_PREF."query.`date`,".TB_PREF."query.`name`,".TB_PREF."query.`business_name`,".TB_PREF."query.`care_of`,".TB_PREF."query_status.status AS  status,".TB_PREF."source_status.description AS  source,".TB_PREF."query.`phone1`,".TB_PREF."query.`phone2`,".TB_PREF."query.`mobile`,".TB_PREF."query.`email`,".TB_PREF."query.`package`,".TB_PREF."query.`package_final`,".TB_PREF."query.`address`,".TB_PREF."query.`remarks`,".TB_PREF."users.`user_id`,".TB_PREF."query.`id`
		 FROM `".TB_PREF."query` 
		INNER JOIN  ".TB_PREF."query_status ON ".TB_PREF."query_status.id=".TB_PREF."query.`status`
		INNER JOIN  ".TB_PREF."source_status ON ".TB_PREF."source_status.id=".TB_PREF."query.`source_status`
		INNER JOIN  ".TB_PREF."users ON ".TB_PREF."users.id=".TB_PREF."query.`user`
		WHERE ".TB_PREF."query.`date`>='$date'
		AND ".TB_PREF."query.`date`<='$end_date'";

	if($status != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`status`=".db_escape($status);
	if($source_status != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`source_status`=".db_escape($source_status);
	if($name != ALL_TEXT)
	//$sql .= " AND ".TB_PREF."query.`name`=".db_escape("%$name%");
		$sql .= " AND name LIKE ". db_escape("%$name%");
	if($mobile != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`mobile`=".db_escape($mobile);
	if($phone1 != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`phone1`=".db_escape($phone1);
	if($phone2 != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`phone2`=".db_escape($phone2);
	if($user != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`user`=".db_escape($user);
	if($care_of != ALL_TEXT)
		$sql .= " AND care_of LIKE ". db_escape("%$care_of%");

	//if ($debtor_no != '') 
	//{
   		//$sql .= " AND ".TB_PREF."query.debtor_no = ".db_escape($debtor_no);
	//}

   	return $sql;
	
}
//..........................................saad2..............................
//echo $_POST['status']."-- asad";
/*........................
$sql = get_sql_for_query_inquiry( date2sql($_POST['date']), date2sql($_POST['end_date']), $_POST['description'], $_POST['debtor_no'],  $_POST['status'], $_POST['remarks']);
 $cols = array(
		
			_("Start date"), 
			_("End date"),
		    _("Description"),
		    _("Client"),
		    _("Status"),
		    _("Remarks"),
    );
	array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));


		

if ($_POST['debtor_no'] != ALL_TEXT)
{
	$cols[_("Supplier")] = 'skip';
	$cols[_("Currency")] = 'skip';
}
*/

if($_SESSION['status'] == 112)
	unset($_SESSION['status']);

//$_POST['source_status'],

$sql = get_sql_for_query_inquiry( date2sql($_POST['date']), date2sql($_POST['end_date']),$_SESSION['status'],$_POST['source_status'],$_POST['user'], $_POST['name'], $_POST['mobile'], $_POST['phone1'],$_POST['phone2'],$_POST['care_of']);

//		    _("Status") => array('name'=>'status', type=>'status', 'ord'=>'desc'),
//			_("Source") => array('name'=>'source_status', type=>'source_status', 'ord'=>'desc'),
//			_("Package Finalized") => array('name'=>'package', type=>'package', 'ord'=>'desc'),


 $cols = array(
		
	        _("Date") => array('name'=>'date', 'type'=>'date', 'ord'=>'desc'),
			_("Name"),
		    _("Business Name/Nature"),
			_("C/O"),
		    _("Status") => skip,
			_("Source") => skip,
		    _("phone1") => skip,
			_("phone2") => skip,
		    _("Mobile"),
			_("Email") => skip,
			_("Package Qouted") => array('name'=>'package', type=>'package', 'ord'=>'desc'),
			_("Package Finalized")=> skip,
			_("Address") => skip,
			_("Remark"),
			_("User")
    );
	array_append($cols, array(
		array('insert'=>true, 'fun'=>'edit_link')));


		


//------------------------------------------------------------------------------------------------

/*show a table of the transactions returned by the sql */
$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "85%";


	
display_db_pager($table);

if (!@$_GET['popup'])
{
	end_form();
	end_page(@$_GET['popup'], true, true);
}
?>
