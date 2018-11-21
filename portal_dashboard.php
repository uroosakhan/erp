<?php

$path_to_root = "../..";
$page_security = 'SA_CUSTOMER';
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/db_pager.inc");

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
	page(_($help_context = "Portal Dashboard"), isset($_GET['debtor_no']), false, "", $js);
}

//------------------------------------------------------------------------------------------------

if (!@$_GET['popup'])
    start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

ref_cells(_("Change ADMIN Password:"), '', '',null, '', true);
submit_cells('SearchOrders', _("Execute"),'',_('Select documents'), 'default');
//date_cells(_("From:"), 'date', '', null, -1865);
//date_cells(_("To:"), 'end_date');

end_row();
end_table();

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


function update_button($row)
{
    $trans_no = "update_button" . $row['id'];
    return $row['Done'] ? '' :
        '<input type="submit"  class="btn btn-primary btn-xs" title="To update text fields" name="' . $trans_no . '" tabIndex="2' . $row['id'] . '" value= "UPDATE"
>'. '<input name="update_button[' . $row['id'] . ']" tabIndex="2' . $row['id'] . '" type="hidden" value="' . $row['id'] . ' 

">';
}

//function selectAll($label=null, $name='', $onchange=''){
//    $str = "";
//
//    if($label != null)
//        $str .= "<label>Suspend</label></br>";
//
//    $str .= "<input type='checkbox' name='".$name."' onchange='".$onchange."'>";
//
//    return $str;
//}

function batch_checkbox($row)
{
    $name = "Sel_" .$row['trans_no'];
    return $row['Done'] ? '' :
        "<input type='checkbox' name='$name' value='1' >"
        ."<input name='Sel_[".$row['trans_no']."]' type='hidden' value='"
        .$row['trans_no']." '>\n";
}

//--------------------------------//
if (isset($_POST['ProcessVoiding']))
{
    $del_count = 0;

    foreach($_POST['Sel_'] as $delivery => $branch) {
        $checkbox = 'Sel_'.$delivery;
        if (check_value($checkbox))	{
            if (!$del_count) {
                $del_branch = $branch;
            }
            else {

            }
            $selected[] = $delivery;
            $del_count++;
        }
    }

    if (!$del_count) {
        display_error(_('To void  you should select at least one item.'.$del_count));
    } else {
        $_SESSION['DeliveryBatch'] = $selected;
        $_SESSION['trans_no']=$selected;
    }
}



function portal_link($label, $url='', $class='', $id='',  $icon=null)
{
    global $path_to_root;

    if ($class != '')
        $class = " class='$class'";

    if ($id != '')
        $class = " id='$id'";

    if ($url != "")
    {
        $pars = access_string($label);
        if (user_graphic_links() && $icon)
            $pars[0] = set_icon($icon, $pars[0]);
        -		$preview_str = "<a target='_blank' $class $id href='$path_to_root/$url' \"$pars[1]>$pars[0]</a>";
    }
    else
        $preview_str = $label;
    return $preview_str;
}

function view_links($row)
{
    $viewer = "admin/";
    $viewer .= "user_locations.php";
    $preview_str = portal_link($row['real_name'], $viewer);
    return  $preview_str;
}

if (isset($_POST['BatchInvoice'])) {
    $del_count = 0;

    foreach ($_POST['Sel_'] as $delivery => $branch) {
        $checkbox = 'Sel_' . $delivery;
        if (check_value($checkbox)) {

            update_suspended_account($delivery, $_POST['de_activate']);
        }
    }
}

function update_suspended_account($deactivate, $deactivate_value)
{
    $sql = "UPDATE ".TB_PREF."sys_prefs SET de_activate=$deactivate"
        ." WHERE value = ".db_escape($deactivate_value);

    db_query($sql, "Can't approve suspended account");
}
////////////ansar

$link = mysql_connect('localhost', 'root', '');
$db_list = mysql_list_dbs($link);
//function company_code_new($code)
//{
//   // display_error($code);
//    return $db_connections["$code"]["name"];
//
//}
//function get_sql_for_portal_dashboard($db_name,$db_code)
//{
//    // display_error($db_code);
//    $sql = "SELECT CONCAT(id,'-','$db_code'),
//    ".TB_PREF."users.`user_id`,
//    ".TB_PREF."users.`real_name`,
//    ".TB_PREF."users.`last_visit_date`,
//    ".TB_PREF."users.`startup_tab`
//	 FROM $db_name.".TB_PREF."users
//
//		WHERE id != 0 ";
//    $sql .= " GROUP BY $db_name.".TB_PREF."users.date_sep";
//
//    return $sql;
//
//}
function get_sql_for_portal_dashboard($db_name,$db_code)
{
    // display_error($db_code);
    $sql = "SELECT CONCAT(id,'-','$db_name'),
    ".TB_PREF."users.`user_id`,
    ".TB_PREF."users.`real_name`,
    ".TB_PREF."users.`last_visit_date`,
    ".TB_PREF."users.`startup_tab`
	 FROM $db_name.".TB_PREF."users
		WHERE id != 0 ";
    $sql .= " GROUP BY $db_name.".TB_PREF."users.date_sep";
    return $sql;
}
$sr =1;
$sql='';
while ($row = mysql_fetch_object($db_list))
{
    echo '<td>';

    echo "<b>";
   // echo $sr;
    echo "</b>";
    echo " ";
   // echo $row->name . "\n";
  //  company_code_new($sr);

    if(strcmp($row->Database,'information_schema') || strcmp($row->Database,'performance_schema') || strcmp($row->Database,'mysql'))continue;
    {
        $sql .= get_sql_for_portal_dashboard($row->Database,$db_connections["$sr"]["name"]);
    }

    echo '</td>';
  //  var_dump($row->Database);
    $sr++;
}
///
 $cols = array(
	 	_("Company Code"),
	 	_("Company Name"),
	 	_("User") => array('fun'=>'view_links'),
	 	_("Last login Date"),
//        _("").selectAll('Select All', 'selectAll', 'checkAll(this)')=>
//        _("Suspended") => array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
        _("Suspended").
            submit('BatchInvoice',_("Update"), false, _("Batch Update")) =>
            array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
     	_("Start Date"),
     	_("Reports")
    );
//------------------------------------------------------------------------------------------------


$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _(""));

$table->width = "85%";

display_db_pager($table);
end_form();



end_page(@$_GET['popup'], true, true);

?>
<script type="text/javascript">
    function checkAll(ele) {
        var checkboxes = document.getElementsByTagName('input');
        if (ele.checked) {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = true;
                }
            }
        } else {
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].type == 'checkbox') {
                    checkboxes[i].checked = false;
                }
            }
        }
    }
</script>
