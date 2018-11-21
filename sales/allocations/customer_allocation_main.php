<?php

$page_security = 'SA_SALESALLOC';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "Customer Allocations"), false, false, "", $js);

//--------------------------------------------------------------------------------

start_form();
/* show all outstanding receipts and credits to be allocated */

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();

echo "<center>" . _("Select a customer: ") . "&nbsp;&nbsp;";
 global $leftmenu_save, $db_connections;
     if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'AAT'  && $_SESSION["wa_current_user"]->access!=2)
     {
echo customer_list1('customer_id', $_POST['customer_id'], true, true);
}
else
echo customer_list('customer_id', $_POST['customer_id'], true, true);
echo "<br>";
check(_("Show Settled Items:"), 'ShowSettled', null, true);
echo "</center><br><br>";

set_global_customer($_POST['customer_id']);

if (isset($_POST['customer_id']) && ($_POST['customer_id'] == ALL_TEXT))
{
	unset($_POST['customer_id']);
}

$settled = false;
if (check_value('ShowSettled'))
	$settled = true;

$customer_id = null;
if (isset($_POST['customer_id']))
	$customer_id = $_POST['customer_id'];

//--------------------------------------------------------------------------------
//this show the data in debtor trans which has wrong branch id
function get_alloc_branch_bugs()
{
	$sql = "SELECT * 
		FROM 
			".TB_PREF."debtor_trans,
			".TB_PREF."debtors_master,
			".TB_PREF."cust_branch

		WHERE
		".TB_PREF."debtors_master.debtor_no = ".TB_PREF."debtor_trans.debtor_no
		AND ".TB_PREF."debtors_master.debtor_no = ".TB_PREF."cust_branch.debtor_no 
		AND ".TB_PREF."cust_branch.branch_code != ".TB_PREF."debtor_trans.branch_code
		AND ".TB_PREF."debtor_trans.ov_amount != 0";

	return db_query($sql, "Cannot retreive a customer allocation");

	//$alloc = db_fetch($result);
	
	//return $result;
}
// these two below function checks the invoice allocations in cust_alloc which are allocated to wrong customers
function get_cust_alloc_table_bugs()
{
	$sql = "SELECT trans_no 
		FROM 
			".TB_PREF."debtor_trans,
			".TB_PREF."cust_allocations

		WHERE ".TB_PREF."debtor_trans.debtor_no = ".TB_PREF."cust_allocations.person_id
		AND ".TB_PREF."debtor_trans.type = ".TB_PREF."cust_allocations.trans_type_from
        AND ".TB_PREF."debtor_trans.trans_no = ".TB_PREF."cust_allocations.trans_no_from
    	AND ".TB_PREF."debtor_trans.ov_amount != 0
     	AND ".TB_PREF."debtor_trans.type = 12
    	";
		
	
	return db_query($sql, "Cannot retreive a customer allocation");

}
function get_cust_alloc_table_bugs_invoices($receipt_no)
{
$sql = "SELECT id, trans_no_to, reference, person_id, trans_no_from, date_alloc
		FROM 
			".TB_PREF."debtor_trans,
			".TB_PREF."cust_allocations

		WHERE ".TB_PREF."cust_allocations.trans_no_from = $receipt_no
     	AND ".TB_PREF."cust_allocations.trans_type_from = 12
     	AND ".TB_PREF."cust_allocations.person_id != ".TB_PREF."debtor_trans.debtor_no
    	AND ".TB_PREF."cust_allocations.trans_no_to = ".TB_PREF."debtor_trans.trans_no
    	AND ".TB_PREF."cust_allocations.trans_type_to = ".TB_PREF."debtor_trans.type   	
    	AND ".TB_PREF."debtor_trans.ov_amount != 0
    	";
		

	return db_query($sql, "Cannot retreive a customer allocation");
} 
function get_wrong_alloc_payment_not_found_in_cust_alloc_table()
{
    $sql = "SELECT trans_no, 
                ".TB_PREF."debtor_trans.debtor_no, 
               name, 
                ".TB_PREF."debtor_trans.reference, 
               ov_amount 
		FROM ".TB_PREF."debtor_trans, 
		".TB_PREF."debtors_master 
		WHERE ".TB_PREF."debtor_trans.trans_no NOT IN (SELECT ".TB_PREF."cust_allocations.trans_no_from FROM ".TB_PREF."cust_allocations) AND ".TB_PREF."debtor_trans.type IN (2, 11,12, 42) 
		AND ".TB_PREF."debtor_trans.ov_amount != 0 
		AND ".TB_PREF."debtor_trans.alloc != 0 
		AND ".TB_PREF."debtor_trans.debtor_no = ".TB_PREF."debtors_master.debtor_no 
		ORDER BY ".TB_PREF."debtors_master.name
    	";
		
	return db_query($sql, "Cannot retreive a customer allocation");
}    

function match_payments_in_both_tables()
{
$sql = "SELECT id, trans_no_from, reference, person_id, date_alloc
		FROM 
			".TB_PREF."debtor_trans,
			".TB_PREF."cust_allocations

		WHERE ".TB_PREF."cust_allocations.trans_type_from = 12
     	AND ".TB_PREF."cust_allocations.person_id != ".TB_PREF."debtor_trans.debtor_no
    	AND ".TB_PREF."cust_allocations.trans_no_from = ".TB_PREF."debtor_trans.trans_no
    	AND ".TB_PREF."cust_allocations.trans_type_from = ".TB_PREF."debtor_trans.type   	
    	AND ".TB_PREF."debtor_trans.ov_amount != 0
    	";
		
	return db_query($sql, "Cannot retreive a customer allocation");
} 

function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function trans_view($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function alloc_link($row)
{
	return pager_link(_("Allocate"),
		"/sales/allocations/customer_allocate.php?trans_no="
			.$row["trans_no"] . "&trans_type=" . $row["type"]. "&debtor_no=" . $row["debtor_no"], ICON_ALLOC);
}
//new build
function amount_total($row)
{
    return price_format($row['type'] == ST_JOURNAL && $row["Total"] < 0 ? -$row["Total"] : $row["Total"]);
}

function amount_left($row)
{
    return price_format(($row['type'] == ST_JOURNAL && $row["Total"] < 0 ? -$row["Total"] : $row["Total"])-$row["alloc"]);
}
//function amount_left($row)
//{
//	return price_format($row["Total"]-$row["alloc"]);
//}

function check_settled($row)
{
	return $row['settled'] == 1;
}


$sql = get_allocatable_from_cust_sql($customer_id, $settled);

$cols = array(
	_("Transaction Type") => array('fun'=>'systype_name'),
	_("#") => array('fun'=>'trans_view', 'align'=>'right'),
	_("Reference"), 
	_("Date") => array('name'=>'tran_date', 'type'=>'date', 'ord'=>'asc'),
	_("Customer") => array('ord'=>''),
	_("Currency") => array('align'=>'center'),
	_("Total") => array('align'=>'right','fun'=>'amount_total'),
	_("Left to Allocate") => array('align'=>'right','insert'=>true, 'fun'=>'amount_left'), 
	array('insert'=>true, 'fun'=>'alloc_link')
	);

if (isset($_POST['customer_id'])) {
	$cols[_("Customer")] = 'skip';
	$cols[_("Currency")] = 'skip';
}

$table =& new_db_pager('alloc_tbl', $sql, $cols);
$table->set_marker('check_settled', _("Marked items are settled."), 'settledbg', 'settledfg');

$table->width = "75%";

display_db_pager($table);

 
    $match_payments_in_both_tables = match_payments_in_both_tables();
    while($myrow4 = db_fetch($match_payments_in_both_tables))
    {
        //echo "cust alloc";
    echo $myrow4['id']." ".$myrow4['trans_no_from']." ". $myrow4['reference']." ".$myrow4['person_id']." ". $myrow4['trans_no_from']." ". $myrow4['date_alloc'];
    echo "</br>";
    }    
    
$wrong_payments_alloc = get_wrong_alloc_payment_not_found_in_cust_alloc_table();
    while($myrow3 = db_fetch($wrong_payments_alloc))
    {
    echo $myrow3['trans_no'] ." ". $myrow3['debtor_no'] ."  ". $myrow3['name'] ." " . $myrow3['reference'] ." ". $myrow3['ov_amount'];
    }

    //dz for checking allocation bugs
    $alloc_branch_bugs = get_alloc_branch_bugs();
    
    while($myrow = db_fetch($alloc_branch_bugs))
    {
       // echo "branches";
    echo $myrow['trans_no']." / ". $myrow['reference'] ." / ".$myrow['name']." / ".$myrow['ov_amount'];
    echo "</br>";
    }

$cust_alloc_table_bugs = get_cust_alloc_table_bugs();

    while($myrow = db_fetch($cust_alloc_table_bugs))
    {
    //echo $myrow['trans_no'];
    $result = $myrow['trans_no'];
    //echo "</br>";
    
    $cust_alloc_table_bugs_invoices = get_cust_alloc_table_bugs_invoices($result);
    $myrow2 = db_fetch($cust_alloc_table_bugs_invoices);
    {
        //echo "cust alloc";
    echo $myrow2['id']." ".$myrow2['trans_no_to']." ". $myrow2['reference']." ".$myrow2['person_id']." ". $myrow2['trans_no_from']." ". $myrow2['date_alloc'];
    }
   
    
    

}   

    


end_form();



end_page();
