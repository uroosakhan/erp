<?php
$page_security = 'SA_CUSTOMER';
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

function get_task_nwww($selected_id)
{
    $sql = "SELECT id  FROM ".TB_PREF."query WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get task");
    return db_fetch($result);
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
	echo "<a href=../inquiry/query_inquiry.php?status=3>&nbsp&nbsp&nbspHot&nbsp<span class='badge'></button1></span>
	</a></span></button>";
}
else
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-thumbs-up'></span>
	<button type='button' class='btn btn-default'>";
	if($hp_count3)
		echo "<button1 data-count=$hp_count3>";
	echo "<a href=../inquiry/query_inquiry.php?status=3>&nbsp&nbsp&nbspHot&nbsp<span class='badge'></span>
	</a></span></button1></button>
	";
}

if($_GET['status'] == 5)
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
	<button type='button' class='btn btn-default active'>";
	if($hp_count4 != 0)
		echo "<button1 data-count=$hp_count4>";
	echo "<a href=../inquiry/query_inquiry.php?status=5>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'></span>
	</a></span></button1></button>";
}
else
{
	echo "
	<span class='icon-input-btn'><span class='glyphicon glyphicon-alert'></span>
	<button type='button' class='btn btn-default'> ";
	if($hp_count4 != 0)
		echo "<button1 data-count=$hp_count4>";
	echo "<a href=../inquiry/query_inquiry.php?status=5>&nbsp&nbsp&nbspDONE&nbsp<span class='badge'></span>
	</a></span></button1></button>";
}


if($_GET['status'] == 2)
{
	echo "<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
	<button type='button' class='btn btn-default active'> ";
	if($hp_count2 != 0)
		echo "<button1 data-count=$hp_count2>";
	echo "<a href=../inquiry/query_inquiry.php?status=2>&nbsp&nbsp&nbspCold&nbsp<span class='badge'></span>
	</a></span></button>";
}
else
{echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-pause'></span>
<button type='button' class='btn btn-default'> "; if($hp_count2 != 0) echo "<button1 data-count=$hp_count2>"; echo "
<a href=../inquiry/query_inquiry.php?status=2>&nbsp&nbsp&nbspCold&nbsp<span class='badge'></span>
</a></span></button>
";}



if($_GET['status'] == 1)
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default active'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/query_inquiry.php?status=1>&nbsp&nbsp&nbspWarm&nbsp<span class='badge'></span></a>
</span></button>";
}

else
{
	echo "
<span class='icon-input-btn'><span class='glyphicon glyphicon-ok'></span>
<button type='button' class='btn btn-default'> "; if($hp_count1 != 0) echo "<button1 data-count=$hp_count1>"; echo "
<a href=../inquiry/query_inquiry.php?status=1>&nbsp&nbsp&nbspWarm&nbsp<span class='badge'></span></a>
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
// status_query_list_cells(_("Status:"), 'status', null,true);
source_status_query_list_cells(_("Source:"), 'source_status', null,true);
$user_name= $_SESSION["wa_current_user"]->user;

users_queryy_list_cells(_("User:"), 'user', null, true);


submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
//ref_cells(_("Name:"), 'name', '',null, _('Enter memo fragment or leave empty'));
ref_cells(_("Name:"), 'name', '',null,'', true );
ref_cells(_("Mobile:"), 'mobile', '',null, '', true);
//ref_cells(_("phone1:"), 'phone1', '',null, '', true);
//ref_cells(_("phone2:"), 'phone2', '',null, '', true);
//ref_cells(_("C/O:"), 'care_of', '',null, '', true);


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

$id = find_submit('update_button');

if ($id !=-1)
{

    foreach ($_POST['update_button'] as $delivery => $branch) {

        $checkbox = 'update_button' . $delivery;

        if (check_value($checkbox))
        {

            if (
                strlen($_POST["date" . $delivery]) != 0||
                strlen($_POST["source_status" . $delivery]) != 0||
                strlen($_POST["name" . $delivery]) != 0 ||
                strlen($_POST["mobile" . $delivery]) != 0 ||
                strlen($_POST["email" . $delivery]) != 0 ||
                strlen($_POST["status" . $delivery]) != 0 ||
                strlen($_POST["items" . $delivery]) != 0 ||
                  strlen($_POST["phone1" . $delivery]) != 0 ||
			  strlen($_POST["phone2" . $delivery]) != 0 ||
			  strlen($_POST["remarks" . $delivery]) != 0 
                )
            {
                $date= $_POST["date" . $delivery];
                $source_status= $_POST["source_status" . $delivery];
                $name = $_POST["name" . $delivery];
                $mobile = $_POST["mobile" . $delivery];
                $email = $_POST["email" . $delivery];
                $status = $_POST["status" . $delivery];
                $items= $_POST["stock_id" . $delivery];
                
                		$phone1= $_POST["phone1" . $delivery];
				$phone2= $_POST["phone2" . $delivery];
				$remarks= $_POST["remarks" . $delivery];
//
                $myrow = get_task_nwww($delivery);
//
//                $start_date_db = date2sql($start_date);
//                $end_date_db = date2sql($end_date);

                if($myrow["source_status"] != $source_status || $myrow["name"] != $name || $myrow["mobile"] != $mobile
                    || $myrow["email"] != $email || $myrow["status"] != $status || $myrow["items"] != $items ||  $myrow["phone1"] != $phone1 || $myrow["phone2"] != $phone2 || $myrow["remarks"] != $remarks
)
                {
//                    add_query($delivery, sql2date($myrow["start_date"]), sql2date($myrow["end_date"]), $description,
//                        $myrow["debtor_no"], $myrow["task_type"], '','','', $status, $myrow["user_id"], $myrow["assign_by"], $myrow["plan"], $myrow["plan1"], $actual, $actual1, $remarks, '', $priority, $progress, $_SESSION['wa_current_user']->user);


    //                 $sql = "UPDATE ".TB_PREF."query SET
    //             source_status=" . db_escape($source_status) . ",
				// name=" . db_escape($name) .",
				// mobile=" . db_escape($mobile) . ",
				// email=" . db_escape($email) .",
				// status=" . db_escape($status) . ",
				// stock_id=" . db_escape($items) . "
				// WHERE id=" . db_escape($delivery) . "";
    //                 db_query($sql, "Error");
                              $sql = "UPDATE ".TB_PREF."query SET
               status=" . db_escape($status) . ",
				stock_id=" . db_escape($items) . ",
				
				phone1=" . db_escape($phone1) . ",
				phone2=" . db_escape($phone2) . ",
				remarks=" . db_escape($remarks) . "
				
				WHERE id=" . db_escape($delivery) . "";
                    db_query($sql, "Error");

                    display_notification(_('Task has been updated.'));
                }
                else
                    display_error(_("No changes have been saved."));


            }

        }
    }
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
function order_link($row)
{
	if (@$_GET['popup'])
		//return '';
		$delete=delete_emp_info($row['id']);
	$modify = 'id';
	return pager_link( _("Edit"),
		"/sales/sales_order_entry.php?NewOrder=Yes", ICON_DOC);

}

//function create_link($row)
//{
//
//	return pager_link( _("Edit"),"../sales/sales_order_entry.php", ICON_DOC);
//
//}

//
//function followup_link($row)
//{
//    $sql = "SELECT id FROM ".TB_PREF."query WHERE id=".db_escape($row['id']);
//    $result = db_query($sql, "could not get customer");
//    $row_ = db_fetch_row($result);
//    $user_name_ =  truncate_text(strtoupper($row_[1]), 10, '');
//    $user_in_process_tasks = get_task_count(0, 1, 7,  $row['id'], 0, 0);
//
//    if($user_in_process_tasks == 0)
//    {
//        $preview_str1 = "<span class='icon-input-btn'>
//<a target='_blank' $class $id href=../inquiry/task_inquiry.php?status=112&user_id=$row_[0]
//onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'  title='Person whom this task to be assigned'>$user_name_
//</a>
//</span>
//";	}
//    else
//    {
//        $preview_str1 = "<span class='icon-input-btn'>
//<a target='_blank' $class $id href=../inquiry/task_inquiry.php?status=112&user_id=$row_[0]
//onclick=\"javascript:openWindow(this.href,this.target); return false;\" class='btn btn-default btn-xs'  title='Person whom this task to be assigned'>$user_name_
//<span class='badge''>$user_in_process_tasks</span>
//</a>
//</span>
//";	}
//
//
//    return $preview_str1;
//}


function followup_link($row)
{

    return viewer_link('',"project/inquiry/follow_up.php?status=112&id=".$row['id']."", "btn btn-default btn-xs",'',("Show customer of this task"));


}
       $user_name= $_SESSION["wa_current_user"]->user;
function get_user_through_id($user_name)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_name);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function update_button($row)
{
    $trans_no = "update_button" . $row['id'];
    return $row['Done'] ? '' :
        '<input type="submit"  class="btn btn-primary btn-xs" title="To update text fields" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value= "UPDATE"
>'. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . ' 

">';
}
function update_names($row)
{

    $trans_no = "name" . $row['id'];
    $description = $row['name'];

    $desc_height = mb_strlen($row['description']);
//    $remarks_height = mb_strlen($row['remarks']);
//
//    if($desc_height > $remarks_height)
//        $box_height = mb_strlen($row['description'])/25;
//    else
//        $box_height = mb_strlen($row['remarks'])/20;

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_b($row)
{

    $trans_no = "business_name" . $row['id'];
    $description = $row['business_name'];

    $desc_height = mb_strlen($row['business_name']);
//    $remarks_height = mb_strlen($row['remarks']);
//
//    if($desc_height > $remarks_height)
//        $box_height = mb_strlen($row['description'])/25;
//    else
//        $box_height = mb_strlen($row['remarks'])/20;

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_c($row)
{

    $trans_no = "care_of" . $row['id'];
    $description = $row['care_of'];

    $desc_height = mb_strlen($row['care_of']);

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_m($row)
{

    $trans_no = "mobile" . $row['id'];
    $description = $row['mobile'];

    $desc_height = mb_strlen($row['mobile']);

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_remarks($row)
{

    $trans_no = "remarks" . $row['id'];
    $description = $row['remarks'];

    $desc_height = mb_strlen($row['remarks']);

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_email($row)
{

    $trans_no = "email" . $row['id'];
    $description = $row['email'];

    $desc_height = mb_strlen($row['email']);

    return $row['Done'] ? '' :
        "<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
        ." title=\"Show or add description of the task\">\n"
        . '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}


function update_phone1($row)
{

	$trans_no = "phone1" . $row['id'];
	$description = $row['phone1'];

	$desc_height = mb_strlen($row['phone1']);

	return $row['Done'] ? '' :
		"<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
		." title=\"Show or add description of the task\">\n"
		. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}
function update_phone2($row)
{

	$trans_no = "phone2" . $row['id'];
	$description = $row['phone2'];

	$desc_height = mb_strlen($row['phone2']);

	return $row['Done'] ? '' :
		"<input type='text' value='$description' rows=$desc_height cols=15 name='$trans_no''"
		." title=\"Show or add description of the task\">\n"
		. '<input name="description[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . '" >';
}


function update_stock_id($row)
{
    $name = "stock_id".$row['id'];
    echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['id']."'>\n";
    return stock_costable_items_list( $name,$row['stock_id'],_("-"),false);
}
function update_source_status($row)
{
    $name = "source_status".$row['id'];
    echo "<input name='source_status[".$row['id']."]'  type='hidden' value='".$row['source_status']."'>\n";
    return source_status_query_list( $name,$row['source_status'],_("-"),false);
}
function update__status($row)
{
    $name = "status".$row['id'];


//    display_error($row['status']);

    echo "<input name='status[".$row['id']."]'  type='hidden' value='".$row['status_']."'>\n";
    return status_query_list( $name,$row['status_'],_("-"),false);
}
function update_followup_date($row)
{
    $name = "package".$row['id'];


//    display_error($row['status']);

    echo "<input name='followup_date[".$row['id']."]'  type='hidden' value='".$row['package']."'>\n";
    $name = $row['package'];
    return date_cells(_(""), $name);
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
function get_sql_for_query_inquiry($date, $end_date, $status, $source_status, $name='', $mobile,$user,$package)
{
	$user_name= $_SESSION["wa_current_user"]->access;

	$sql = "SELECT ".TB_PREF."query.`date`,	".TB_PREF."source_status.description AS  source,
	".TB_PREF."query.`name`,
	".TB_PREF."query.`mobile`,".TB_PREF."query.`email`,".TB_PREF."query_status.status AS status,".TB_PREF."query.`business_name`,
	".TB_PREF."query.`care_of`,
".TB_PREF."query.`phone1`,
	".TB_PREF."query.`phone2`,".TB_PREF."query.`package_final`,
	".TB_PREF."query.`address`,".TB_PREF."query.`remarks`,".TB_PREF."query.`package` ,".TB_PREF."users.`user_id`,".TB_PREF."query.`id`
	,".TB_PREF."query.`stock_id`	,".TB_PREF."query.`source_status`,".TB_PREF."query.`status` as status_	 FROM `".TB_PREF."query` 
		INNER JOIN  ".TB_PREF."query_status ON ".TB_PREF."query_status.id=".TB_PREF."query.`status`
		INNER JOIN  ".TB_PREF."source_status ON ".TB_PREF."source_status.id=".TB_PREF."query.`source_status`
		INNER JOIN  ".TB_PREF."users ON ".TB_PREF."users.id=".TB_PREF."query.`user`
		WHERE ".TB_PREF."query.`date`>='$date'
		AND ".TB_PREF."query.`date`<='$end_date'";


	if($user_name != 2) {
		$sql .= "	AND ".TB_PREF."query.`user` = ".db_escape($_SESSION["wa_current_user"]->user)."";
	}



	if($status != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`status`=".db_escape($status);
	if($source_status != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`source_status`=".db_escape($source_status);
    if($status != ALL_TEXT)
        $sql .= " AND ".TB_PREF."query.`status`=".db_escape($status);
	if($name != ALL_TEXT)
	//$sql .= " AND ".TB_PREF."query.`name`=".db_escape("%$name%");
		$sql .= " AND name LIKE ". db_escape("%$name%");
	if($mobile != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`mobile`=".db_escape($mobile);
//	if($phone1 != ALL_TEXT)
//		$sql .= " AND ".TB_PREF."query.`phone1`=".db_escape($phone1);
//	if($phone2 != ALL_TEXT)
//		$sql .= " AND ".TB_PREF."query.`phone2`=".db_escape($phone2);
	if($user != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`user`=".db_escape($user);
	if($package != ALL_TEXT)
		$sql .= " AND ".TB_PREF."query.`package`=".db_escape($package);
//	if($care_of != ALL_TEXT)
//		$sql .= " AND care_of LIKE ". db_escape("%$care_of%");

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

$sql = get_sql_for_query_inquiry( date2sql($_POST['date']), date2sql($_POST['end_date']),$_SESSION['status'],$_POST['source_status'], $_POST['name'], $_POST['mobile'], $_POST['user']);

//		    _("Status") => array('name'=>'status', type=>'status', 'ord'=>'desc'),
//			_("Source") => array('name'=>'source_status', type=>'source_status', 'ord'=>'desc'),
//			_("Package Finalized") => array('name'=>'package', type=>'package', 'ord'=>'desc'),


 $cols = array(
        _("UPDATE") => array('insert' => true, 'fun' => 'update_button'),
        _("Date") => array('name'=>'date', 'type'=>'date', 'ord'=>'desc'),
        _("Resource") ,
        _("Customer Name") ,
        _("Mobile"),
        _("Email")=> array('fun' => 'update_email', 'align' => 'left'),
        _("Status")=> array('fun' => 'update__status', 'align' => 'left'),
        // _("Followup Date")=> array('type'=>'date', 'fun' => 'update_followup_date'),
        _("Items")=> array('fun' => 'update_stock_id', 'align' => 'left'),


_("C/O") ,



		    _("phone1") => array('fun' => 'update_phone1', 'align' => 'left'),
			_("phone2")=> array('fun' => 'update_phone2', 'align' => 'left') ,
			_("Email") => skip,
			_("Package Qouted") => skip,
			_("Package Finalized")=> skip,
			_("Address") => skip,
			_("Remark")=> array('fun' => 'update_remarks', 'align' => 'left'),
			_("User")
    );


if($_SESSION["wa_current_user"]->access!=2) {


	array_append($cols, array(
		_("Follow up")=>array('insert'=>true, 'fun'=>'followup_link'),

		_("Create Order")=> array('insert'=>true, 'fun'=>'order_link')



	));

}
else {
	array_append($cols, array(
		_("Follow up") => array('insert' => true, 'fun' => 'followup_link'),
		_("Edit Order") => array('insert' => true, 'fun' => 'edit_link'),
		_("Create Order") => array('insert' => true, 'fun' => 'order_link')


	));
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
	end_page(@$_GET['popup'], true, true);
}
?>
