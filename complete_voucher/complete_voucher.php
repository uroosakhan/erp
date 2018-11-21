<?php

$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc"); //asad
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/ui/items_cart.inc");
include_once($path_to_root . "/complete_voucher/db/cust_trans_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/cust_trans_details_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/sales_order_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/sales_delivery_voucher_db.inc");
include_once($path_to_root . "/complete_voucher/db/sales_invoice_voucher_db.inc");
include_once($path_to_root . "/complete_voucher/db/gl_db_banking_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/gl_journal_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/po_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/grn_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/invoice_db_voucher.inc");
include_once($path_to_root . "/complete_voucher/db/sales_credit_db_voucher.inc");

$js = "";
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}

$_SESSION['page_title'] = _($help_context = "Transfer Vouchers");

page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------
global $Refs, $systypes_array, $SysPrefs;

if (isset($_GET['AddedID'])) 
{
	$order_no = $_GET['AddedID'];

	display_notification_centered(sprintf(_($systypes_array[$_SESSION['Type']]." # %d has been entered"), $order_no));

	hyperlink_params($path_to_root."/complete_voucher/inquiry/complete_voucher_inquiry.php", _("Select Another For &Batch"), "");

	display_footer_exit();
}

if (isset($_GET['BatchConfirm']))
{
	$src = $_SESSION['DeliveryBatch'];
}

if(isset($_POST['CancelOrder']))
{
	meta_forward($path_to_root . '/complete_voucher/inquiry/complete_voucher_inquiry.php','');
}

if (isset($_POST['process_invoice']) ) {

	processing_start();
    $AllowURL = 0;
	foreach ($_SESSION['DeliveryBatch'] as $val) {
		if ($_SESSION['Type'] == ST_SALESINVOICE) {
			$debtor_trans = get_customer_trans_voucher($val, ST_SALESINVOICE);
			$sales_order = get_sales_order_header_voucher($debtor_trans['order_'], ST_SALESORDER);
			$sales_order_details = get_sales_order_details_voucher($sales_order['order_no'], $sales_order['trans_type']);
//			Sales Order 1
			$order_no = add_sales_order_voucher($sales_order, $sales_order_details);
			$debtor_trans_details = get_customer_trans_details_voucher($debtor_trans['type'], $debtor_trans['trans_no']);
			$trans_tax_details = get_trans_tax_details_record($debtor_trans['type'], $debtor_trans['trans_no']);
//			Sales Delivery
			$delivery_no = write_sales_delivery_voucher($sales_order, $sales_order_details, $debtor_trans, $debtor_trans_details, $trans_tax_details, $order_no);
//			Sales Invoice
			$debtor_trans_I = get_customer_trans_voucher($val, ST_SALESINVOICE);
			$debtor_trans_details_I = get_customer_trans_details_voucher($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
			$trans_tax_details_I = get_trans_tax_details_record($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
			$get_gl = get_gl_trans_for_complete_voucher($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
            $trans_no = write_sales_invoice_voucher($sales_order, $sales_order_details, $debtor_trans_I, $debtor_trans_details_I, $trans_tax_details_I, $get_gl, $order_no);
			commit_transaction();
		} elseif($_SESSION['Type'] == ST_BANKPAYMENT ||
                $_SESSION['Type'] == ST_BANKDEPOSIT ||
                $_SESSION['Type'] == ST_CPV ||
                $_SESSION['Type'] == ST_CRV ||
                $_SESSION['Type'] == ST_CUSTPAYMENT ||
                $_SESSION['Type'] == ST_SUPPAYMENT) {
			$trans_no = add_bank_transactions($val);
			commit_transaction();
		} elseif($_SESSION['Type'] == ST_JOURNAL) {
			$bank_trans = db_fetch(get_bank_trans_for_complete_invoice($_SESSION['Type'], $val));
			$currency = get_bank_account_currency_for_c_i($bank_trans['bank_act']);
			$bank_gl_account = get_bank_gl_account_for_c_i($bank_trans['bank_act']);
			$memo_ = get_comments_string_for_C_I($_SESSION['Type'], $val);
			$GetGl = get_gl_trans_for_complete_voucher($_SESSION['Type'], $val);
            $TransTax = get_trans_tax_details_voucher($_SESSION['Type'], $val);
            $DebtorTrans = get_customer_transaction_voucher($_SESSION['Type'], $val);
            $SuppTrans = get_supplier_transaction_voucher($_SESSION['Type'], $val);
            $JournalEntries = get_journal_transaction_voucher($_SESSION['Type'], $val);
			$trans_no = write_journal_entries_voucher($bank_trans, $GetGl, $TransTax, $DebtorTrans, $SuppTrans, $memo_, $JournalEntries);
			commit_transaction();
		} elseif($_SESSION['Type'] == ST_BANKTRANSFER) {
            $bank_trans = get_bank_trans_for_complete_invoice($_SESSION['Type'], $val);
            $HeaderBT = db_fetch($bank_trans);
			$currency = get_bank_account_currency_for_c_i($HeaderBT['bank_act']);
			$bank_gl_account = get_bank_gl_account_for_c_i($HeaderBT['bank_act']);
			$memo_ = get_comments_string_for_C_I($_SESSION['Type'], $val);
			$GetGl = get_gl_trans_for_complete_voucher($_SESSION['Type'], $val);
            $bank_trans = get_bank_trans_for_complete_invoice($_SESSION['Type'], $val);
			$trans_no = add_bank_transfer_voucher($bank_trans, $currency, $bank_gl_account, $memo_, $GetGl);
			commit_transaction();
		} elseif($_SESSION['Type'] == ST_CUSTCREDIT) {
            $debtor_trans_I = get_customer_trans_voucher($val, ST_CUSTCREDIT);
            $debtor_trans_details_I = get_customer_trans_details_voucher($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
            $trans_tax_details_I = get_trans_tax_details_record($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
            $get_gl = get_gl_trans_for_complete_voucher($debtor_trans_I['type'], $debtor_trans_I['trans_no']);
            $stock_moves = get_stock_moves_voucher(ST_CUSTCREDIT, $val);
            $memo_ = get_comments_string_for_C_I($_SESSION['Type'], $val);
            $trans_no = write_credit_note_voucher($debtor_trans_I, $debtor_trans_details_I,
            $trans_tax_details_I, $get_gl, $stock_moves, $memo_);
            commit_transaction();
        } elseif($_SESSION['Type'] == ST_SUPPINVOICE) {
//          GET/Add PO
            $GetPODetailsNo = get_supp_invoice_items_for_get_po_detail_no($val);
            $GetPONo = get_po_no_in_po_details($GetPODetailsNo['po_detail_item_id']);
            $Get_PO_Details = read_po_items_voucher($GetPONo['order_no']);
            $Get_PO = read_po_header_voucher($GetPONo['order_no']);
            $PO_no = add_po_voucher($Get_PO, $Get_PO_Details);

//          GET/ADD GRN
            $Get_Curr_PO_Details = read_current_db_po_items_voucher($PO_no); // this function get those data now you loin which company
            $GRN_no = add_grn_voucher($Get_PO, $PO_no, $Get_Curr_PO_Details);

//          GET/ADD Invoice
            $GetST = get_supp_trans_voucher($val, ST_SUPPINVOICE);
            $GetGrnBatchID = get_grn_batch_id($PO_no);
            $GetGrnItemsDetails = get_grn_items_details($GetGrnBatchID['id']);
            $TransTax = get_trans_tax_details_voucher($_SESSION['Type'], $GetST['trans_no']);
            $Get_GL = get_gl_trans_for_complete_voucher($_SESSION['Type'], $GetST['trans_no']);
            $memo_ = get_comments_string_for_C_I($_SESSION['Type'], $val);
            $trans_no = add_supp_invoice_voucher($GetST, $GetGrnItemsDetails, $TransTax, $Get_GL, $memo_);
            commit_transaction();
        } else {
            $AllowURL = 1;
            display_error("Please go back and make batch again.");
		}
	}
	unset($_SESSION['DeliveryBatch']);
    unset($_SESSION['TransToDate']);
	if($AllowURL == 0) {
        meta_forward($_SERVER['PHP_SELF'], "AddedID=".$trans_no);
	}
}

if(count($src) == 0) {
	display_error(_("There are no invoices for processing."));
	display_footer_exit();
	unset($_SESSION['DeliveryBatch']);
	unset($_SESSION['TransToDate']);
}

start_form();

div_start('Items');

display_heading(_($systypes_array[$_SESSION['Type']]));

start_table(TABLESTYLE, "width=60%");
if($_SESSION['Type'] == ST_SALESINVOICE)
	$th = array(_("#"), _("Customer"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_BANKPAYMENT ||
	$_SESSION['Type'] == ST_BANKDEPOSIT ||
                $_SESSION['Type'] == ST_CPV ||
                $_SESSION['Type'] == ST_CRV)
	$th = array(_("#"), _("Customer"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_JOURNAL)
	$th = array(_("#"), _("Customer"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_BANKTRANSFER)
	$th = array(_("#"), _("Accounts"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_CUSTCREDIT)
    $th = array(_("#"), _("Customer"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_SUPPINVOICE)
    $th = array(_("#"), _("Supplier"), _("Date"), _("Amount")/*, _("Batch No")*/);
elseif($_SESSION['Type'] == ST_SUPPAYMENT)
    $th = array(_("#"), _("Supplier"), _("Date"), _("Amount")/*, _("Batch No")*/);

table_header($th);
$k = 0;

foreach($src as $val)
{
	alt_table_row_color($k);

	if($_SESSION['Type'] == ST_SALESINVOICE) {
		$myrow = get_vouchers_from_debtor_trans($_SESSION['Type'], $val);
		label_cell($myrow['trans_no']);
		label_cell($myrow['name']);
		label_cell(sql2date($myrow['tran_date']));
		amount_cell($myrow['ov_amount']);
		$_SESSION['date_'] = sql2date($myrow['trans_date']);
	}
	elseif($_SESSION['Type'] == ST_BANKPAYMENT ||
			$_SESSION['Type'] == ST_BANKDEPOSIT ||
                $_SESSION['Type'] == ST_CPV ||
                $_SESSION['Type'] == ST_CRV) {
		$myrow = get_vouchers_from_bank_trans($_SESSION['Type'], $val);
		label_cell($myrow['trans_no']);
		if($myrow['person_type_id'] == 2)
			label_cell(get_customer_name($myrow['person_id']));
		elseif($myrow['person_type_id'] == 3)
			label_cell(get_supplier_name_voucher($myrow['person_id']));
		else
			label_cell('');
		label_cell(sql2date($myrow['trans_date']));
		amount_cell($myrow['amount']);
		$_SESSION['date_'] = sql2date($myrow['trans_date']);
	}
	elseif($_SESSION['Type'] == ST_JOURNAL) {
		$myrow = get_vouchers_from_journal($_SESSION['Type'], $val);
		label_cell($myrow['type_no']);
		if($myrow['person_type_id'] == 2)
			label_cell(get_customer_name($myrow['person_id']));
		elseif($myrow['person_type_id'] == 3)
			label_cell(get_supplier_name_voucher($myrow['person_id']));
		else
			label_cell('');
		label_cell(sql2date($myrow['tran_date']));
		amount_cell($myrow['amount']);
		$_SESSION['date_'] = sql2date($myrow['tran_date']);
	}
	elseif($_SESSION['Type'] == ST_BANKTRANSFER) {
		$myrow = get_vouchers_from_bank_transfer($_SESSION['Type'], $val);
		label_cell($val);
		label_cell(get_bank_account_name_voucher($myrow['bank_act']));
		label_cell(sql2date($myrow['trans_date']));
		amount_cell($myrow['amount']);
		$_SESSION['date_'] = sql2date($myrow['tran_date']);
	}
    elseif($_SESSION['Type'] == ST_CUSTCREDIT) {
        $myrow = get_vouchers_from_debtor_trans($_SESSION['Type'], $val);
        label_cell($myrow['trans_no']);
        label_cell($myrow['name']);
        label_cell(sql2date($myrow['tran_date']));
        amount_cell($myrow['ov_amount']);
        $_SESSION['date_'] = sql2date($myrow['trans_date']);
    }
    elseif($_SESSION['Type'] == ST_SUPPINVOICE) {
        $myrow = get_vouchers_from_supp_trans($_SESSION['Type'], $val);
        label_cell($myrow['trans_no']);
        label_cell($myrow['supp_name']);
        label_cell(sql2date($myrow['tran_date']));
        amount_cell($myrow['ov_amount']);
        $_SESSION['date_'] = sql2date($myrow['tran_date']);
    }
    elseif($_SESSION['Type'] == ST_SUPPAYMENT) {
        $myrow = get_vouchers_from_supp_trans($_SESSION['Type'], $val);
        label_cell($myrow['trans_no']);
        label_cell($myrow['supp_name']);
        label_cell(sql2date($myrow['tran_date']));
        amount_cell(abs($myrow['ov_amount']));
        $_SESSION['date_'] = sql2date($myrow['tran_date']);
    }
	end_row();
}
end_table(1);
submit_center_first('process_invoice',  _("Process"),
	_('Check entered data and save document'), 'default');
submit_center_last('CancelOrder',  _("Cancel"),
	_('Cancels document entry or removes sales order when editing an old document'));
div_end(); 
end_form();
end_page();

?>