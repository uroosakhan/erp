<?php

$page_security = 'SA_LOCATIONTRANSFER';
$path_to_root = "../..";

include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Daily Stock Movement"), false, false, "", $js);

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

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

ref_cells(_("Reference:"), 'Ref', '',null, _('Enter reference fragment or leave empty'));

//locations_list_cells_br(_("To Branch"), 'to_branch', null, _("All Branches"));
locations_list_cells(_("Location ").":", 'to_branch',  null, _("All Locations"), false,
$_SESSION["wa_current_user"]->user);
// asad 12-11-2014

daily_movement_type_list_cells(_("Type:"), "filterType");
check_cells( _("Show closed:"), 'AlsoClosed', null);
end_row();
start_row();
date_cells(_("From:"), 'FromDate', '', null, 0, 0, 0);
date_cells(_("To:"), 'ToDate');



ref_cells(_("Memo:"), 'Memo', '',null, _('Enter memo fragment or leave empty'));
submit_cells('Search', _("Search"), '', '', 'default');
end_row();

end_table();

function from_name($row)
{
	$transfer_items = get_stock_transfer_view($row["trans_no"]);
	$from_trans = $transfer_items[0];

	return $from_name = $from_trans['location_name'];
}

function to_name($row)
{
	$transfer_items = get_stock_transfer_view($row["trans_no"]);
	$to_trans = $transfer_items[1];

	return $to_name = $to_trans['location_name'];
}

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
	return get_trans_view_str($row["type"], $row["trans_no"]);
}

function gl_link($row) 
{
	return get_gl_view_str($row["type"], $row["type_no"]);
}

$editors = array(
	ST_JOURNAL => "/gl/gl_journal.php?ModifyGL=Yes&trans_no=%d&trans_type=%d",
	ST_BANKPAYMENT => "/gl/gl_bank.php?ModifyPayment=Yes&trans_no=%d&trans_type=%d",
	ST_BANKDEPOSIT => "/gl/gl_bank.php?ModifyDeposit=Yes&trans_no=%d&trans_type=%d",
//	4=> Funds Transfer,
   ST_SALESINVOICE => "/sales/customer_invoice.php?ModifyInvoice=%d",
//   11=>
// free hand (debtors_trans.order_==0)
//	"/sales/credit_note_entry.php?ModifyCredit=%d"
// credit invoice
//	"/sales/customer_credit_invoice.php?ModifyCredit=%d"
//	 12=> Customer Payment,
   ST_CUSTDELIVERY => "/sales/customer_delivery.php?ModifyDelivery=%d",
//   16=> Location Transfer,
//   17=> Inventory Adjustment,
//   20=> Supplier Invoice,
//   21=> Supplier Credit Note,
//   22=> Supplier Payment,
//   25=> Purchase Order Delivery,
//   28=> Work Order Issue,
//   29=> Work Order Production",
//   35=> Cost Update,
);

function edit_link($row)
{
/*	global $editors;

	$ok = true;
	if ($row['type'] == ST_SALESINVOICE)
	{
		$myrow = get_customer_trans($row["type_no"], $row["type"]);
		if ($myrow['alloc'] != 0 || get_voided_entry(ST_SALESINVOICE, $row["type_no"]) !== false)
			$ok = false;
	}		
	return isset($editors[$row["type"]]) && !is_closed_trans($row["type"], $row["type_no"]) && $ok ? 
		pager_link(_("Edit"), 
			sprintf($editors[$row["type"]], $row["type_no"], $row["type"]),
			ICON_EDIT) : '';
*/

   			$str = "/inventory/location_transfer_edit.php?trans_no=".$row['trans_no'];
		return pager_link(_('Edit'), $str, ICON_EDIT);
}
function prt_link($row)
{


    if ($row['type'] == ST_LOCTRANSFER)
        return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_LOCTRANSFER, ICON_PRINT);
    else
        return '';

}

function prt_link_1($row)
{


    if ($row['type'] == ST_LOCTRANSFER)
        return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, 304449, ICON_PRINT);
    else
        return '';

}



function get_sql_for_journal_inquiry2($filter, $from, $to, $ref='', $memo='', $alsoclosed=false, $to_branch)
{

	$sql = "SELECT		
		sm.trans_no,
		refs.reference,
		sm.tran_date,
		sm.loc_code,
		sm.type,
		com.memo_,
		IF(ISNULL(u.user_id),'',u.user_id) as user_id
		FROM ".TB_PREF."stock_moves as sm
		 LEFT JOIN ".TB_PREF."audit_trail as a ON
			(sm.type=a.type AND sm.trans_no=a.trans_no)
		 LEFT JOIN ".TB_PREF."comments as com ON
			(sm.type=com.type AND sm.trans_no=com.id)
		 LEFT JOIN ".TB_PREF."refs as refs ON
			(sm.type=refs.type AND sm.trans_no=refs.id)
		 LEFT JOIN ".TB_PREF."users as u ON
			a.user=u.id
		WHERE sm.tran_date >= '" . date2sql($from) . "'
		AND sm.tran_date <= '" . date2sql($to) . "' ";

		$sql .= " AND sm.type IN(16, 17, 13, 25) ";
//		$sql .= " AND sm.qty >= 0";


	if ($ref) {
		$sql .= " AND sm.reference LIKE ". db_escape("%$ref%");
	}
	if ($memo) {
		$sql .= " AND com.memo_ LIKE ". db_escape("%$memo%");
	}

	if ($to_branch) {
		$sql .= " AND sm.loc_code = ". db_escape($to_branch);
	}

	if ($filter != -1) {
		$sql .= " AND sm.type=".db_escape($filter);
	}

	if (!$alsoclosed) {
		$sql .= " AND gl_seq=0";
	}
	$sql .= " GROUP BY sm.tran_date, a.gl_seq, sm.type, sm.trans_no";
	return $sql;
}


$sql = get_sql_for_journal_inquiry2(get_post('filterType', -1), get_post('FromDate'),
	get_post('ToDate'), get_post('Ref'), get_post('Memo'), check_value('AlsoClosed'), get_post('to_branch'));

$cols = array(
	_("ID") => array('fun'=>'view_link'),
	_("Reference"), 
	_("Date") =>array('name'=>'tran_date','type'=>'date','ord'=>'desc'),
	_("Location"), 
	_("Type")  => array('fun'=>'systype_name'), 
	_("Memo"),
	_("User"),
    _("Print") => array('insert' => true, 'fun' => 'prt_link')
    //   _("Print") => array('insert' => true, 'fun' => 'prt_link_1')
);

if (!check_value('AlsoClosed')) {
	$cols[_("#")] = 'skip';
}

$table =& new_db_pager('journal_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();

?>