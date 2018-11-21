<?php
$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/POS/includes/sales_ui.inc");
include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Invoices Inquiry"), isset($_GET['customer_id']), false, "", $js);

if (isset($_GET['customer_id']))
{
	$_POST['customer_id'] = $_GET['customer_id'];
}

if (isset($_GET['type']))
{
	$_POST['filterType'] = $_GET['type'];
}
if (isset($_GET['trans_no']))
{
    $type_no = $_GET['trans_no'];
}
if (isset($_GET['trans_type']))
{
    $type = $_GET['trans_type'];
}

//------------------------------------------------------------------------------------------------
start_form();


$time=date('h:i:s');
$time1=('03:00:00');
$time2=('00:00:00');

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();
	
	echo "</br>";
start_table(TABLESTYLE_NOBORDER);
start_row();
$_SESSION['amount'] = $_SESSION['TotalDiscount'] = $_SESSION['GrossAmount'] = 0;
$trans_no = array();


//if(isset($_GET['type']))
    date_cells(_("From:"), 'TransAfterDate', '', null, 0);
//else
  //  date_cells(_("From:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'TransToDate', '', null);

if (!$page_nested)
	customer_list_cells(_("Select a Customer:"), 'customer_id', null, true, false, false, true);
 ref_cells(_("Reference:"),'reference',null, null, false, null, null);
	
if (!isset($_POST['filterType']))
	$_POST['filterType'] = 0;

bank_accounts_list_all_cells_pos(_("Account:"), 'ToBankAccount', null, true, 'All');

bank_balance_cell($_POST['ToBankAccount']);

end_row();

start_row();


//cust_allocations_list_cells(null, 'filterType', $_POST['filterType'], true);
//dimensions_list_cells(_("Dimension")." 1:", 'Dimension', null, true, " ", false, 1);
//time_invoice_list_cells(_("Time:"), 'ToBankAccount', null, true, 'All');

 yesno_week_list_cells(_("Search Criteria:"), 'time', null, _("Select"), _("Greater Than >"), _("Less Than <"), _("Greater and Equal >="), _("Less and Equal <="), false);

ref_cells(_("Time:"),'time1',null, null, false, null, null);

 users_list_cells_(_("Users:"), 'user_id', null, null, true, true);


submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');
end_row();
end_table();

set_global_customer($_POST['customer_id']);
div_start('totals_tbl');
if ($_POST['customer_id'] != "" && $_POST['customer_id'] != ALL_TEXT)
{
    $customer_record =get_sales_total($_POST['TransAfterDate'],$_POST['TransToDate'], $_POST['customer_id'], $_POST['ToBankAccount'],$_POST['user_id'], $_POST['time'], $_POST['time1']);
    display_customer_summary($customer_record);
    echo "<br>";
}
div_end();
//------------------------------------------------------------------------------------------------

function display_customer_summary($customer_record)
{
    start_table(TABLESTYLE, "width='80%'");
    $th = array(_("No of Transactions"), _("Gross Amount"), _("Discount"), _("Net Amount"), _("Average / Trans"));
    table_header($th);

	start_row();
	amount_cell1($customer_record['total_invoices']);
    amount_cell1($customer_record['total_amount']);
    amount_cell1($customer_record['t_discount']);
    amount_cell1($customer_record['total_amount']-$customer_record['t_discount']);
    $Net_Amount = $customer_record['total_amount'] - $customer_record['t_discount'];
    amount_cell1($Net_Amount / $customer_record['total_invoices']);
	end_row();

	end_table();
}
if(get_post('RefreshInquiry'))
{
    $Ajax->activate('totals_tbl');
}
//------------------------------------------------------------------------------------------------
/*
function display_customer_summary($customer_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
    if ($customer_record["dissallow_invoices"] != 0)
    {
    	echo "<center><font color=red size=4><b>" . _("CUSTOMER ACCOUNT IS ON HOLD") . "</font></b></center>";
    }

	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');

    start_table(TABLESTYLE, "width='80%'");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));
    table_header($th);

	start_row();
    label_cell($customer_record["curr_code"]);
    label_cell($customer_record["terms"]);
	amount_cell($customer_record["Balance"] - $customer_record["Due"]);
	amount_cell($customer_record["Due"] - $customer_record["Overdue1"]);
	amount_cell($customer_record["Overdue1"] - $customer_record["Overdue2"]);
	amount_cell($customer_record["Overdue2"]);
	amount_cell($customer_record["Balance"]);
	end_row();

	end_table();
}*/
//------------------------------------------------------------------------------------------------

//div_start('totals_tbl');
//if ($_POST['customer_id'] != "" && $_POST['customer_id'] != ALL_TEXT)
//{
//	$customer_record = get_customer_details($_POST['customer_id'], $_POST['TransToDate']);
////    display_customer_summary($customer_record);
//    echo "<br>";
//}
//div_end();
//if(get_post('RefreshInquiry'))
//{
//	$Ajax->activate('totals_tbl');
//}
//------------------------------------------------------------------------------------------------

function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function order_view($row)
{
	return $row['order_']>0 ?
		get_customer_trans_view_str(ST_SALESORDER, $row['order_']) : "";
}

function trans_view($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function prt_link($row)
{
 	return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}
function Sum_NetAmount($row)
{
    $_SESSION['amount'] += $row['TotalAmount'];
    return $row['TotalAmount'];
}
function Sum_Discount($row)
{
    $_SESSION['TotalDiscount'] += $row['TotalDiscount'];
    return $row['TotalDiscount'];
}
function Sum_GrossAmount($row)
{
    $_SESSION['GrossAmount'] += $row['total'];
    return $row['total'];
}
function gl_link($row)
{
	return get_gl_view_str(10, $row["trans_no"], "", false, '', '', $row['approval']);
}

function get_time($trans_no)
{
    $sql = "SELECT UNIX_TIMESTAMP(stamp) as unix_stamp
    FROM ".TB_PREF."audit_trail WHERE trans_no=".db_escape($trans_no['trans_no'])."
    AND type =".db_escape($trans_no['type']);
    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return sql2date(date("Y-m-d", $row[0]));
}
// function void_link($row)
// {
//     return pager_link(_("Credit This") ,
//             "/POS/inquiry/pos_inquiry.php?type=1&trans_no=".$row['trans_no']."&trans_type=".$row['type'], ICON_DELETE);
// }
function void_link($row)
{
    return pager_link_js_confirm123( _("Void"),
        "/POS/inquiry/pos_inquiry.php?type=1&trans_no=" .$row['trans_no']."&trans_type=".$row['type'], ICON_DELETE,
        _("You are about to void Invoice.\n Do you want to continue?"));
}
function pager_link_js_confirm123($link_text, $url, $icon=false, $msg) //asad 16-10-2015
{
	global $path_to_root;
	
	if (user_graphic_links() && $icon)
		$link_text = set_icon($icon, $link_text);

	$href = $path_to_root . $url;
	return "<a href='$href' onclick='return confirmUser()'>" . $link_text . "</a>";
}


if($type_no != 0 && $type != 0)
{

    begin_transaction();
    global $Refs;
    $date_ = $memo_ = date("d-m-Y");
    $sql = "SELECT * FROM ".TB_PREF."cust_allocations 
	        WHERE trans_type_to = ".db_escape($type)." 
	        AND trans_no_to = ".db_escape($type_no);
    $query = db_query($sql, "Error");
    while($myrow = db_fetch($query)) {
//        display_error($myrow['trans_type_from']."++". $myrow['trans_no_from']);
        void_bank_trans($myrow['trans_type_from'], $myrow['trans_no_from'], true);
        void_gl_trans($myrow['trans_type_from'], $myrow['trans_no_from'], true);
        void_cust_allocations($myrow['trans_type_from'], $myrow['trans_no_from']);
        void_customer_trans($myrow['trans_type_from'], $myrow['trans_no_from']);
        $trans_type_from = $myrow['trans_type_from'];
        $trans_no_from = $myrow['trans_no_from'];

    }
    add_audit_trail($trans_type_from, $trans_no_from, $date_, _("Voided.")."\n".$memo_);
    $Refs->restore_last($trans_type_from, $trans_no_from);
    add_voided_entry($trans_type_from, $trans_no_from, $date_, $memo_);
    hook_db_prevoid($type, $type_no);
    void_bank_trans($type, $type_no, true);
    void_gl_trans($type, $type_no, true);

    // reverse all the changes in parent document(s)
    $factor = get_cust_prepayment_invoice_factor($type_no);
    if ($factor != 0) {
        $lines = get_customer_trans_details($type, $type_no);
        while($line = db_fetch($lines)) {
            update_prepaid_so_line($line['src_id'], -$factor*$line['quantity']);
        }
    } else {
        $deliveries = get_sales_parent_numbers($type, $type_no);
        if ($deliveries !== 0) {
            if ($type == ST_SALESINVOICE && count($deliveries) == 1 && get_reference(ST_CUSTDELIVERY, $deliveries[0]) == "auto") {
                void_sales_delivery(ST_CUSTDELIVERY, $deliveries[0], false);
                $date_ = Today();
                add_audit_trail(ST_CUSTDELIVERY, $deliveries[0], $date_, _("Voided."));
                add_voided_entry(ST_CUSTDELIVERY, $deliveries[0], $date_, "");
            } else {
                $srcdetails = get_sales_parent_lines($type, $type_no);
                while ($row = db_fetch($srcdetails)) {
                    update_parent_line($type, $row['id'], -$row['quantity']);
                }
            }
        }
    }
    void_customer_trans_details($type, $type_no);
    void_stock_move($type, $type_no);
    void_trans_tax_details($type, $type_no);
    void_customer_trans($type, $type_no);
    void_cust_allocations($type, $type_no);
    add_audit_trail($type, $type_no, $date_, _("Voided.")."\n".$memo_);
    $Refs->restore_last($type, $type_no);
    add_voided_entry($type, $type_no, $date_, $memo_);
    commit_transaction();


    meta_forward('../inquiry/pos_inquiry.php?type=1');
}

$sql = "SELECT alloc.trans_no_to FROM ".TB_PREF."cust_allocations alloc 
        LEFT JOIN ".TB_PREF."bank_trans bank ON bank.trans_no = alloc.trans_no_from
        AND alloc.trans_type_from = bank.type
        LEFT JOIN ".TB_PREF."bank_accounts accounts ON accounts.id = bank.bank_act
        LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.trans_no = alloc.trans_no_to
        AND trans.type = alloc.trans_type_to
        WHERE bank.type = 12 ";
    if($_POST['ToBankAccount'])
        $sql .= " AND accounts.account_code = ".db_escape($_POST['ToBankAccount']);
    $sql .= " AND bank.trans_date >= ".db_escape(date2sql($_POST['TransAfterDate']));
    $sql .= " AND bank.trans_date <= ".db_escape(date2sql($_POST['TransToDate']));
if (get_post('customer_id') != ALL_TEXT)
    $sql .= " AND trans.debtor_no = ".db_escape(get_post('customer_id'));
if ($_POST['reference'] != ALL_TEXT)
    $sql .= " AND trans.reference LIKE ".db_escape("%".$_POST['reference']."%");
$result = db_query($sql, "Error");
$trans_no = array();
while($myrow = db_fetch($result)) {
    $trans_no[] = $myrow['trans_no_to'];
}

 //var_dump($trans_no);

//------------------------------------------------------------------------------------------------
$sql = get_sql_for_inquiry_pos($trans_no,$_POST['user_id'],$_POST['time'],$_POST['time1']);
//------------------------------------------------------------------------------------------------
//db_query("set @bal:=0");
$cols = array(
    );	
 if($_SESSION["wa_current_user"]->can_access('SA_VOIDTRANSACTION') ) 
 {
        array_append($cols, array(	
	_("Void") => array('insert'=>true, 'align'=>'center', 'fun'=>'void_link'),
	_("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'center'),
	_("Reference") => array('ord'=>'', 'align'=>'center'),
	_("Customer") => array('align'=>'center'),
	_("Date") => array('name'=>'tran_date', 'type'=>'date', 'ord'=>'','align'=>'center'),
		_("Tran Date") => array( 'insert' => true, 'fun'=>'get_time', 'ord'=>'','align'=>'center'),

    _("Time") => array('align'=>'center'),
	_("Gross Amount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_GrossAmount'),
	_("Discount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_Discount'),
	_("Net Amount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_NetAmount'),
	_("Cash Tendered") => array('align'=>'right','type'=>'amount'),
    _("Cash Return") => array('align'=>'right','type'=>'amount'),
	_("GL") => array('insert' => true, 'fun' => 'gl_link'),
	_("Print") => array('insert'=> true, 'fun'=>'prt_link', 'align'=>'center')));
}
else
{
           array_append($cols, array(	
	_("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'center'),
	_("Reference") => array('ord'=>'', 'align'=>'center'),
	_("Customer") => array('align'=>'center'),
	_("Date") => array('name'=>'tran_date', 'type'=>'date', 'ord'=>'','align'=>'center'),
		_("Tran Date") => array( 'insert' => true, 'fun'=>'get_time', 'ord'=>'','align'=>'center'),

    _("Time") => array('align'=>'center'),
	_("Gross Amount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_GrossAmount'),
	_("Discount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_Discount'),
	_("Net Amount") => array('align'=>'right', 'type'=>'amount', 'fun' => 'Sum_NetAmount'),
	_("Cash Tendered") => array('align'=>'right','type'=>'amount'),
    _("Cash Return") => array('align'=>'right','type'=>'amount'),
	_("GL") => array('insert' => true, 'fun' => 'gl_link'),
	_("Re-print") => array('insert'=> true, 'fun'=>'prt_link', 'align'=>'center')));
}
$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->width = "85%";
//echo "<h2>Today Sale :</h2>";
// display_db_pager_gross_amount($table);

echo "<center><a href=".$path_to_root."/gl/bank_transfer.php target='_blank'><input type='button' value='Bank Account Transfer'></a> </center> ";

display_db_pager($table);
end_form();
end_page();
?>
<script>
    function confirmUser() {
        var ans = confirm("You are about to void Invoice.\n Do you want to continue?");
        if(ans == true)
            return true;
        else
            return false;
    }
</script>