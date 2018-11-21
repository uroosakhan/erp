<?php
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "..";
$page_security = 'SA_SALESORDER';

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/ui/sales_order_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/ui/sms_send.inc");

set_page_security( @$_SESSION['Items']->trans_type,
	array(	ST_SALESORDER=>'SA_SALESORDER',
			ST_SALESQUOTE => 'SA_SALESQUOTE',
			ST_CUSTDELIVERY => 'SA_SALESDELIVERY',
			ST_SALESINVOICE => 'SA_SALESINVOICE'),
	array(	'NewOrder' => 'SA_SALESORDER',
			'ModifyOrderNumber' => 'SA_SALESORDER',
			'AddedID' => 'SA_SALESORDER',
			'UpdatedID' => 'SA_SALESORDER',
			'NewQuotation' => 'SA_SALESQUOTE',
			'ModifyQuotationNumber' => 'SA_SALESQUOTE',
			'NewQuoteToSalesOrder' => 'SA_SALESQUOTE',
			'AddedQU' => 'SA_SALESQUOTE',
			'UpdatedQU' => 'SA_SALESQUOTE',
			'NewDelivery' => 'SA_SALESDELIVERY',
			'AddedDN' => 'SA_SALESDELIVERY', 
			'NewInvoice' => 'SA_SALESINVOICE',
			'AddedDI' => 'SA_SALESINVOICE'
			)
);
$js = '';
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}
$_SESSION['page_title'] = _($help_context = "Send SMS");
page($_SESSION['page_title'], false, false, "", $js);

if(isset($_GET['type']))
{
	$type = $_GET['type'];
	//var_dump($type);
}

function get_branch_to_order3($customer_id) 
{
	$sql = "SELECT ".TB_PREF."cust_branch.receivables_account
			FROM ".TB_PREF."cust_branch
			WHERE ".TB_PREF."cust_branch.debtor_no = ".db_escape($customer_id);
  	$result = db_query($sql, "gl balance could not be get");
	$row = db_fetch($result);
	return $row['0'];
}

/*function get_gl_balance_for_customer3($account_code_for_cutomer)
{
	$sql = "SELECT SUM(0_gl_trans.amount) as amount, 0_chart_master.account_name 
			FROM 0_gl_trans LEFT JOIN 0_voided v ON 0_gl_trans.type_no=v.id 
			AND v.type=0_gl_trans.type,0_chart_master 
			WHERE 0_chart_master.account_code=0_gl_trans.account 
			AND ISNULL(v.date_) 
			AND 0_gl_trans.amount <> 0 
			AND 0_gl_trans.account = '$account_code_for_cutomer' 
			ORDER BY tran_date, counter";
	$result = db_query($sql, "gl balance could not be get");
	return db_fetch($result);
	
}*/
function get_customer_details_balance($customer_id,$code, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations
	if ($all)
    	$value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh),0)";
    else		
    	$value = "IFNULL(IF(trans.type=11 OR trans.type=12 OR trans.type=2, -1, 1) 
    		* (trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - 
    		trans.alloc),0)";
	$due = "IF (trans.type=10, trans.due_date, trans.tran_date)";
    $sql = "SELECT ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."payment_terms.terms,
		".TB_PREF."debtors_master.credit_limit, ".TB_PREF."credit_status.dissallow_invoices, ".TB_PREF."credit_status.reason_description,

		Sum(IFNULL($value,0)) AS Balance,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2

		FROM ".TB_PREF."debtors_master
			 LEFT JOIN ".TB_PREF."debtor_trans trans ON 
			 trans.tran_date <= '$todate' AND ".TB_PREF."debtors_master.debtor_no = trans.debtor_no AND trans.type <> 13
,
			 ".TB_PREF."payment_terms,
			 ".TB_PREF."credit_status

		WHERE
			 ".TB_PREF."debtors_master.payment_terms = ".TB_PREF."payment_terms.terms_indicator
 			 AND ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			 AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id)." 
			 OR ".TB_PREF."debtors_master.debtor_no = ".db_escape($code)." ";
	if (!$all)
		$sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.gst_wh + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount  + trans.gst_wh - trans.alloc) > ".FLOAT_COMP_DELTA." ";  
	$sql .= "GROUP BY
			  ".TB_PREF."debtors_master.name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month,
			  ".TB_PREF."debtors_master.credit_limit,
			  ".TB_PREF."credit_status.dissallow_invoices,
			  ".TB_PREF."credit_status.reason_description";
    $result = db_query($sql,"The customer details could not be retrieved");

    $customer_record = db_fetch($result);

    return $customer_record;

}

function get_supplier_details_balance($supplier_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - supp_trans.alloc from all summations

	if ($all)
    	$value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh)";
    else	
    	$value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT.",
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh - trans.alloc),
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh + trans.alloc))";
	$due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT.",trans.due_date,trans.tran_date)";
    $sql = "SELECT supp.supp_name, supp.curr_code, ".TB_PREF."payment_terms.terms,

		Sum(IFNULL($value,0)) AS Balance,

		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2,
		supp.credit_limit - Sum(IFNULL(IF(trans.type=".ST_SUPPCREDIT.", -1, 1) 
			* (ov_amount + ov_gst + ov_discount + trans.gst_wh),0)) as cur_credit,
		supp.tax_group_id

		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id AND trans.tran_date <= '$todate',
			 ".TB_PREF."payment_terms

		WHERE
			 supp.payment_terms = ".TB_PREF."payment_terms.terms_indicator
			 AND supp.supplier_id = $supplier_id ";
	if (!$all)
		$sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh) - trans.alloc > ".FLOAT_COMP_DELTA." ";  
	$sql .= "GROUP BY
			  supp.supp_name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month";

    $result = db_query($sql,"The customer details could not be retrieved");
    $supp = db_fetch($result);

    return $supp;
}

//-----------------------------------------------------------------------------
if (isset($_POST['ProcessOrder']))
{
    $objWsdl = new callWSDL();
    $objWsdl->SendBulkSMS($_POST['mobile_no'], $_POST['message_']);
	display_notification(_("Sms has been sent successfully."));
	display_footer_exit();
}

$trans_id = $_GET['Order_no'];
if($type == ST_CUSTPAYMENT || $type == ST_SALESINVOICE || $type == ST_CUSTCREDIT || $type == ST_CUSTDELIVERY)
{
	$myrow = get_customer_trans($trans_id, $type);
	$cust = get_customer($myrow['debtor_no']);
	$name = $cust['name'];
	$Reference = $myrow['reference'];
	$Amount = $myrow['ov_amount'];
	$Total = $myrow['Total'];
	$_POST['mobile_no']  = $cust["phone_no"];
}
else
{
	$myrow = get_supp_trans($trans_id, $type);
	$supp = get_supplier($myrow['supplier_id']);
	$Reference = $myrow['reference'];
	$name = $myrow['supplier_name'];
	$Amount = $myrow['ov_amount'];
	$Total = $myrow['Total'];
	$_POST['mobile_no']  = $supp["phone"];
}
if($type == ST_CUSTPAYMENT || $type == ST_SALESINVOICE || $type == ST_CUSTCREDIT || $type == ST_CUSTDELIVERY)
{
	$outstanding = get_customer_details_balance($myrow['debtor_no'],$myrow['debtor_no']);
}
else
{
	$outstanding = get_supplier_details_balance($myrow['supplier_id']);
}
$memo = get_comments_string($type, $trans_id);
$balance = $outstanding["Balance"];


$Date = sql2date($myrow['tran_date']);

$myrows = get_select_template($type);
while($data = db_fetch($myrows))
{
	$Message = $data['template'];
}

if($type == ST_CUSTPAYMENT || $type == ST_SALESINVOICE || $type == ST_CUSTCREDIT || $type == ST_CUSTDELIVERY)
{
	if($type == ST_CUSTPAYMENT)
	{
		$type_name = "Payment";
		$status = "Jama";
	}
	else if($type == ST_SALESINVOICE)
	{
		$type_name = "Invoice";
		$status = "Dispatch";
	}
	else if($type == ST_CUSTCREDIT)
	{
		$type_name = "Credit Invoice";
		$status = "credit";
	}
	else if($type == ST_CUSTDELIVERY)
	{
		$type_name = "Delivery Note";
		$status = "delivery";
	}
}
else
{
	if($type == ST_SUPPAYMENT)
	{
		$type_name = "Payment";
		$status = "Jama";
	}
	else if($type == ST_SUPPINVOICE)
	{
		$type_name = "Invoice";
		$status = "Dispatch";
	}
	else if($type == ST_SUPPCREDIT)
	{
		$type_name = "Credit Invoice";
		$status = "credit";
	}
}



 
//naufil 14 april 2015
$orignal = array('reference', 'name', 'amount', 'date', 'outstanding', 'type', 'status', 'memo', 'delivery');
$replaced = array($Reference, $name,  $Amount,  $Date,  $balance,   $type_name, $status, $memo, $delivery);
$sentence = str_replace($orignal, $replaced, $Message);
$_POST['message_'] = $sentence;
start_form();

start_table(TABLESTYLE, "width=30%");
if($type == ST_CUSTPAYMENT || $type == ST_SALESINVOICE || $type == ST_CUSTCREDIT || $type == ST_CUSTDELIVERY)
	label_row(_("Customer Name:"), $name, 30);
else
	label_row(_("Supplier Name:"), $supp["supp_name"], 30);  
text_row_ex(_("Cell No :"), 'mobile_no', 30);	
textarea_row(_("Message"), 'message_', null, 60, 8);
end_table(1);
submit_center_first('ProcessOrder', _('Send'), _('Send Your Message'), 'default');
end_form();
end_page();

?>