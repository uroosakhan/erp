<?php

$page_security = 'SA_GLANALYTIC';
$path_to_root="../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
$js = "";


//$cookie_name = "my_cookie";
//$cookie_value = $_GET['search_id'];
//if(isset($_GET['search_id']))
//
//setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

page(_($help_context = "Journal Inquiry"), false, false, "", $js);
//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Search'))
{
	$Ajax->activate('journal_tbl');
}

//--------------------------------------------------------------------------------------
if (!isset($_POST['filterType']))
	$_POST['filterType'] = -1;




//$_POST['Ref']=$_COOKIE[$cookie_name];
//if(!isset($_COOKIE[$cookie_name])) {
//	echo "Cookie named '" . $cookie_name . "' is not set!";
//} else {
//	echo "Cookie '" . $cookie_name . "' is set!<br>";
//	echo "Value is: " . $_COOKIE[$cookie_name];
//	$_POST['Ref'] = $_COOKIE[$cookie_name];
//}
//display_error($_COOKIE['my_cookie']);

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
$_SESSION['amount'] = 0;

search_list_cells(_("Search"), 'searching', null, "", "", '','',true);


ref_cells(null, 'search_value',null,  $_GET['search_id']);

	gl_all_accounts_list_cells(_("Account:"), 'account', $_POST['account'], false, false, _("All Accounts"));



date_cells(_("From:"), 'FromDate', '', null, 0, -1, 0);
date_cells(_("To:"), 'ToDate');

end_row();
start_row();
journal_types_list_cells(_("Type:"), "filterType");
if($_SESSION["wa_current_user"]->can_access('SA_VOUCHERAPPROVAL') && get_company_pref('gl_approval'))
	custom1_list_cells("Status", 'status', null, "", "", false);
users_list_cells(_("User:"), 'userid', null, true);
if (get_company_pref('use_dimension') && isset($_POST['dimension'])) // display dimension only, when started in dimension mode
	dimensions_list_cells(_('Dimension:'), 'dimension', null, true, null, true);
check_cells( _("Show closed:"), 'AlsoClosed', null);
submit_cells('Search', _("Search"), '', '', true);
end_row();
end_table();

function journal_pos($row)
{
	return $row['gl_seq'] ? $row['gl_seq'] : '-';
}

function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function view_link($row)
{
	if($row['trans_type']==ST_JOURNAL)
		return get_trans_view_str($row["trans_type"], $row["trans_no"], "", false, '', '', $row['approval']);
	else
		return get_trans_view_str($row["trans_type"], $row["trans_no"], "", false, '', '', 0);
}
function prt_link($row)
{
	return print_document_link($row['trans_no'], _("Print"), true, $row['trans_type'], ICON_PRINT);
}
function prt_link2($row)
{
//    if ($row['trans_type'] == ST_CUSTPAYMENT)
	if($row['trans_type'] == ST_BANKPAYMENT || $row['trans_type'] == ST_BANKDEPOSIT || $row['trans_type']==ST_BANKTRANSFER
		|| $row['trans_type'] == ST_CPV || $row['trans_type'] == ST_CRV)
		return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTPAYMENT_A5, ICON_PRINT,null,null,null,null,$row['trans_type']);


//    if ($row['type'] == ST_SALESINVOICE)
//        return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_SALESTAX, ICON_PRINT);
	else
		return '';
	//eturn print_document_link($row['trans_no'], _("Print"), true, $row['trans_type'], ICON_PRINT);
	//if ($row['type'] == ST_CUSTPAYMENT)
	//    return print_document_link($row['trans_no']."-".$row['trans_type'], _("Print Receipt"), true, ST_CUSTPAYMENT_A5, ICON_PRINT);

}
function gl_link($row)
{
	return get_gl_view_str($row["trans_type"], $row["trans_no"], "", false, '', '', $row['approval']);
}

function edit_link($row)
{

	$ok = true;
	if ($row['trans_type'] == ST_SALESINVOICE)
	{
		$myrow = get_customer_trans($row["trans_no"], $row["trans_type"]);
		if ($myrow['alloc'] != 0 || get_voided_entry(ST_SALESINVOICE, $row["trans_no"]) !== false)
			$ok = false;
	}
	return $ok ? trans_editor_link($row["trans_type"], $row["trans_no"], $row["approval"]) : '';
}

function invoice_supp_reference($row)
{
	return $row['supp_reference'];
}

function sum_amount($row)
{
	$_SESSION['amount'] += $row['amount'];
	return $row['amount'];
}

function custom_checkbox($label, $name, $value=null, $submit_on_change=false, $title=false)
{
	global $Ajax;

	$str = '';

	if ($label)
		$str .= $label . "  ";
	if ($submit_on_change !== false) {
		if ($submit_on_change === true)
			$submit_on_change =
				"JsHttpRequest.request(\"_{$name}_update\", this.form);";
	}
	if ($value === null)
		$value = get_post($name, 0);

	$str .= "<input class='sendSms'"
		.($value == 1 ? ' checked':'')
		." type='checkbox' name='$name' value='1'"
		.($submit_on_change ? " onclick='$submit_on_change'" : '')
		.($title ? " title='$title'" : '')
		." >\n";

	$Ajax->addUpdate($name, $name, $value);
	return $str;
}

function batch_checkbox($row)
{
	if($row['approval'] == 0)
		$active = 1;
	elseif($row['approval'] == 1)
		$active = 0;

	$name = "Sel_" .$row['trans_no'].'-'.$row['trans_type'];
	return custom_checkbox(null, $name, $active, false, _('Approve/Unapprove this voucher'))
//        "<input type='checkbox' name='$name' value='$active' class='sendSms' >"
// add also trans_no => branch code for checking after 'Batch' submit
	."<input name='Sel_[".$row['trans_no'].'-'.$row['trans_type']."]' type='hidden' value='" .$active."'>\n";
}

function selectAll($label=null, $name='', $class='', $onchange=''){
	$str = "";
	if($label != null)
		$str .= "<label>Select All</label>";
	$str .= "<input type='checkbox' name='".$name."' class='".$class."' onchange='".$onchange."'>";
	return $str;
}

if (isset($_POST['BatchApprove']))
{
	foreach($_POST['Sel_'] as $delivery => $branch)
	{

		$value = explode("-",$delivery); // separate type and trans_no
		$checkbox = 'Sel_'.$delivery; // make checkbox name
		$inactive = check_value($checkbox);//  get checkbox value 0/1

		if($inactive == 0)
			$active = 1;
		elseif($inactive == 1)
			$active = 0;

		if($active == 0)
		{
			$sql = "UPDATE ".TB_PREF."gl_trans SET approval=".db_escape($active)." 
                    WHERE type = ".db_escape($value[1])."
                    AND type_no = ".db_escape($value[0]);
			db_query($sql, "The voucher could not be activated");
		}
		elseif($active == 1)
		{
			$sql = "UPDATE ".TB_PREF."gl_trans SET approval=".db_escape($active)." 
                    WHERE type = ".db_escape($value[1])."
                    AND type_no = ".db_escape($value[0]);
			db_query($sql, "The voucher could not be activated");
		}
	}
}

$sql = get_sql_for_journal_inquiry(get_post('filterType', -1), get_post('FromDate'),
	get_post('ToDate'), get_post('Ref'), get_post('Memo'), check_value('AlsoClosed'),get_post('userid'),
	null, null, get_post('status'), get_post('account'), $_POST['trans_no'], get_post('cheque_no'),$_POST['searching'],$_POST['search_value']);
db_query("set @bal:=0");


if($_SESSION["wa_current_user"]->can_access('SA_VOUCHERAPPROVAL') && get_company_pref('gl_approval')) {
	$cols = array(
		_("#") => array('fun' => 'journal_pos', 'align' => 'center', 'ord' => ''),
		_("Date") => array('name' => 'tran_date', 'type' => 'date', 'ord' => 'desc'),
		_("Type") => array('fun' => 'systype_name', 'ord' => ''),
		_("Trans #") => array('fun' => 'view_link', 'ord' => ''),
		_("Counterparty") => array('ord' => ''),
		_("Supplier's Reference") => 'skip',
		_("Reference") => array('ord' => ''),
	_("Cheque No.") => array('ord' => ''),
	
		_("Amount") => array('fun' => 'sum_amount', 'type' => 'amount'),
		_("Memo"),
		_("User"),
		_("RB") => array('type' => 'amount', 'ord' => 'desc'),
		_("View") => array('insert' => true, 'fun' => 'gl_link'),
		_("A4") => array('insert' => true, 'fun' => 'prt_link'),
		_("A5") => array('insert' => true, 'fun' => 'prt_link2'),
		array('insert' => true, 'fun' => 'edit_link'),
		submit('BatchApprove',_("Submit"), false, _("Batch Approved")).selectAll('Select All', 'selectAll',
			'selectAll', 'checkAll(this)') => array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),

	);
}
else
{
	$cols = array(
		_("#") => array('fun' => 'journal_pos', 'align' => 'center', 'ord' => ''),
		_("Date") => array('name' => 'tran_date', 'type' => 'date', 'ord' => 'desc'),
		_("Type") => array('fun' => 'systype_name', 'ord' => ''),
		_("Trans #") => array('fun' => 'view_link', 'ord' => ''),
		_("Counterparty") => array('ord' => ''),
		_("Supplier's Reference") => 'skip',
		_("Reference") => array('ord' => ''),
	_("Cheque No.") => array('ord' => ''),
	
	
		_("Amount") => array('fun' => 'sum_amount', 'type' => 'amount'),
		_("Memo"),
		_("User"),
		_("RB") => array('type' => 'amount', 'ord' => 'desc'),
		_("View") => array('insert' => true, 'fun' => 'gl_link'),
		_("A4") => array('insert' => true, 'fun' => 'prt_link'),
		_("A5") => array('insert' => true, 'fun' => 'prt_link2'),
		array('insert' => true, 'fun' => 'edit_link')
	);
}
if (!check_value('AlsoClosed')) {
	$cols[_("#")] = 'skip';
}

if($_POST['filterType'] == ST_SUPPINVOICE) //add the payment column if shown supplier invoices only
{
	$cols[_("Supplier's Reference")] = array('fun'=>'invoice_supp_reference', 'align'=>'center');
}

$table =& new_db_pager('journal_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager_total_amount($table);

end_form();
end_page();

?>
<script type="text/javascript">
	// asad 15-07-2015
	function checkAll(ele) {

		var checkboxes =  '';

		if(ele.className == 'selectAll')
		{
			checkboxes = document.getElementsByClassName('sendSms');
		}
		else if(ele.className == 'emailAll')
		{
			checkboxes = document.getElementsByClassName('email');
		}

		if(checkboxes != '')
		{
			if (ele.checked) {
				for (var i = 0; i < checkboxes.length; i++) {
					if (checkboxes[i].type == 'checkbox') {
						checkboxes[i].checked = true;
					}//if
				}//for
			}//if
			else {
				for (var i = 0; i < checkboxes.length; i++) {
					if (checkboxes[i].type == 'checkbox') {
						checkboxes[i].checked = false;
					}//if
				}//for
			}//else
		} // if(checkboxes != '')
	}



</script>