<?php

$page_security = 'SA_SALESPAYMNT';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/allocation_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/admin/db/wht_types_db.inc");
include_once($path_to_root . "/sales/includes/db/wht_tax_category_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows) {
    $js .= get_js_open_window(900, 500);
}
if (user_use_date_picker()) {
    $js .= get_js_date_picker();
}
add_js_file('payalloc.js');
add_js_file('payalloc_edit.js');

page(_($help_context = "Customer Payment Entry"), false, false, "", $js);

//----------------------------------------------------------------------------------------------

check_db_has_customers(_("There are no customers defined in the system."));

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------
if (isset($_GET['customer_id']))
{
    $_POST['customer_id'] = $_GET['customer_id'];
}

if (!isset($_POST['bank_account'])) { // first page call
    $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT, 0, get_post('customer_id'));

    if (isset($_GET['SInvoice'])) {
        //  get date and supplier
        $inv = get_customer_trans($_GET['SInvoice'], ST_SALESINVOICE);
        $dflt_act = get_default_bank_account($inv['curr_code']);
        $_POST['bank_account'] = $dflt_act['id'];
        if($inv) {
            $_SESSION['alloc']->person_id = $_POST['customer_id'] = $inv['debtor_no'];
            $_SESSION['alloc']->BranchID = $_POST['BranchID'] = $inv['BranchID'];
            $_SESSION['alloc']->read(null, 0, $_POST['customer_id'],null, $_POST['BranchID']);
            $_POST['BranchID'] = $inv['branch_code'];
            $_POST['DateBanked'] = sql2date($inv['tran_date']);
            foreach($_SESSION['alloc']->allocs as $line => $trans) {
                if ($trans->type == ST_SALESINVOICE && $trans->type_no == $_GET['SInvoice']) {
                    $un_allocated = $trans->amount - $trans->amount_allocated;
                    if ($un_allocated){
                        $_SESSION['alloc']->allocs[$line]->current_allocated = $un_allocated;
                        $_POST['amount'] = $_POST['amount'.$line] = price_format($un_allocated);
                        $_POST['fixed_amount'] = $_POST['amount'.$line] = price_format($un_allocated);
                    }
                    break;
                }
            }
            unset($inv);
        } else
            display_error(_("Invalid sales invoice number."));
    }
}

if (list_updated('BranchID')) {
    // when branch is selected via external editor also customer can change
    $br = get_branch(get_post('BranchID'));
    $_POST['customer_id'] = $br['debtor_no'];
    $_SESSION['alloc']->person_id = $br['debtor_no'];
    $Ajax->activate('customer_id');
}

if (!isset($_POST['customer_id'])) {
    $_SESSION['alloc']->person_id = $_POST['customer_id'] = get_global_customer(false);
    $_SESSION['alloc']->bank_account = $_POST['bank_account'] = get_global_bank(false);
    $_SESSION['alloc']->BranchID = $_POST['BranchID'];
    $_SESSION['alloc']->read(null, 0, null, null, $_POST['BranchID']);
}
if (!isset($_POST['DateBanked'])) {
    $_POST['DateBanked'] = new_doc_date();
    if (!is_date_in_fiscalyear($_POST['DateBanked'])) {
        $_POST['DateBanked'] = end_fiscalyear();
    }
}
if($_GET['trans_no']) {
    $cust_trans = get_customer_trans($_GET['trans_no'], 12);

    $_POST['wht_supply_tax'] = $cust_trans['supply_tax_id'];
    $_POST['wht_service_tax'] = $cust_trans['service_tax_id'];
    $_POST['wht_fbr_tax'] = $cust_trans['fbr_tax_id'];
    $_POST['wht_srb_tax'] = $cust_trans['srb_tax_id'];
//
    $_POST['wht_supply_amt'] = number_format2($cust_trans['supply_disc'], 2);
    $_POST['wht_service_amt'] = number_format2($cust_trans['service_disc'], 2);
    $_POST['wht_fbr_amt'] = number_format2($cust_trans['fbr_disc'], 2);
    $_POST['wht_srb_amt'] = number_format2($cust_trans['srb_disc'], 2);
    $_POST['amount'] = number_format2($cust_trans['ov_amount'], 2);
}
$Ajax->activate('amount');
//ryan
if(list_updated('wht_supply_tax'))
{
    $Ajax->activate('amount');
    //700 -400
    $percentage = get_wht_tax_percentage(get_post('wht_supply_tax'));
    $_POST['wht_supply_percent'] = price_format($percentage);
    $_POST['amount'] =  input_num('fixed_amount') - (input_num('wht_service_amt') + input_num('wht_fbr_amt') + input_num('wht_srb_amt'));
//	display_error(input_num('fixed_amount') ."/".input_num('wht_supply_amt'));
    $_POST['wht_supply_amt'] =  price_format(round2($percentage / 100 * input_num('fixed_amount'),$dec));
    //   $supply =   input_num('wht_supply_amt');
    $_POST['amount'] =  input_num('amount') - input_num('wht_supply_amt');

    $Ajax->activate('wht_supply_percent');
    $Ajax->activate('wht_supply_amt');
    $Ajax->activate('amount');


//   display_error($percentage);

}


//ryan
if(list_updated('wht_service_tax'))
{
    $percentage = get_wht_tax_percentage(get_post('wht_service_tax'));
    $_POST['wht_service_percent'] = price_format($percentage);
//	display_error($percentage /100);
    $_POST['amount'] =  input_num('fixed_amount') - (input_num('wht_supply_amt') + input_num('wht_fbr_amt') + input_num('wht_srb_amt'));
    $_POST['wht_service_amt'] =  price_format(round2($percentage /100 * input_num('fixed_amount'),$dec));
    $_POST['amount'] =  input_num('amount') - input_num('wht_service_amt');
    $Ajax->activate('amount');
    $Ajax->activate('wht_service_percent');
    $Ajax->activate('wht_service_amt');
}
//ryan
if(list_updated('wht_fbr_tax'))
{
    $percentage = get_wht_tax_percentage(get_post('wht_fbr_tax'));
    $_POST['wht_fbr_percent'] = price_format($percentage);
//   $_POST['wht_fbr_amt'] = price_format($percentage) /100 * input_num('amount');


    $Ajax->activate('wht_fbr_percent');
    $Ajax->activate('wht_fbr_amt');
}
?>

<?php
//if(isset($_POST['wht_fbr_amt']))
//{
//
//    $_POST['amount'] =  $_POST['amount'] - $_POST['wht_fbr_amt'];
//    $Ajax->activate('amount');
//}
//ryan
if(list_updated('wht_srb_tax'))
{
    $percentage = get_wht_tax_percentage(get_post('wht_srb_tax'));
    $_POST['wht_srb_percent'] = price_format($percentage);
//    $_POST['amount'] =  $_POST['amount'] - $_POST['wht_srb_amt'];
//    $Ajax->activate('amount');
//   $_POST['wht_srb_amt'] = price_format($percentage) /100 * input_num('amount');
    $Ajax->activate('wht_srb_percent');
    $Ajax->activate('wht_srb_amt');
}
//ryan



if (isset($_GET['AddedID'])) {
    $payment_no = $_GET['AddedID'];

    display_notification_centered(_("The customer payment has been successfully entered."));

    submenu_print(_("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no."-".ST_CUSTPAYMENT, 'prtopt');
    submenu_print(_("&Email This Receipt"), ST_CUSTPAYMENT, $payment_no."-".ST_CUSTPAYMENT, null, 1);

    submenu_view(_("&View this Customer Payment"), ST_CUSTPAYMENT, $payment_no);

    submenu_option(_("Enter Another &Customer Payment"), "/sales/customer_payments.php");
    submenu_option(_("Enter Other &Deposit"), "/gl/gl_bank.php?NewDeposit=Yes");
    submenu_option(_("Enter Payment to &Supplier"), "/purchasing/supplier_payment.php");
    submenu_option(_("Enter Other &Payment"), "/gl/gl_bank.php?NewPayment=Yes");
    submenu_option(_("Bank Account &Transfer"), "/gl/bank_transfer.php");

    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, _("&View the GL Journal Entries for this Customer Payment")));

    display_footer_exit();
}
elseif (isset($_GET['UpdatedID'])) {
    $payment_no = $_GET['UpdatedID'];

    display_notification_centered(_("The customer payment has been successfully updated."));

    submenu_print(_("&Print This Receipt"), ST_CUSTPAYMENT, $payment_no."-".ST_CUSTPAYMENT, 'prtopt');
    submenu_print(_("&Email This Receipt"), ST_CUSTPAYMENT, $payment_no."-".ST_CUSTPAYMENT, null, 1);
    display_note(get_gl_view_str(ST_CUSTPAYMENT, $payment_no, _("&View the GL Journal Entries for this Customer Payment")));

//	hyperlink_params($path_to_root . "/sales/allocations/customer_allocate.php", _("&Allocate this Customer Payment"), "trans_no=$payment_no&trans_type=12");

    hyperlink_no_params($path_to_root . "/sales/inquiry/customer_inquiry.php?", _("Select Another Customer Payment for &Edition"));

    hyperlink_no_params($path_to_root . "/sales/customer_payments.php", _("Enter Another &Customer Payment"));

    display_footer_exit();
}

//----------------------------------------------------------------------------------------------

function can_process()
{
    global $Refs;

    if (!get_post('customer_id'))
    {
        display_error(_("There is no customer selected."));
        set_focus('customer_id');
        return false;
    }

 if (input_num('wht_supply_amt') > 0 && $_POST['wht_supply_tax'] == 0 ||
 input_num('wht_service_amt') > 0 && $_POST['wht_service_tax'] == 0 ||
 input_num('wht_srb_amt') > 0 && $_POST['wht_srb_tax'] == 0  ||
 input_num('wht_fbr_amt') > 0 && $_POST['wht_fbr_tax'] == 0 )
    {
        display_error(_("There is no Gl Account  selected."));
        set_focus('wht_supply_tax');
        return false;
    }
    
$branch = get_cust_branch($_POST['customer_id'],$_POST['BranchID']);
if ($branch['debtor_no'] == '')
{
    display_error(_("This customer's branch is not loaded please select customer again."));
    set_focus('customer_idmselect');
    return false;
}

if (get_account_type_id($_POST['bank_account'])==4)
{
    if ($_POST['cheque_no']=="")
    {
        display_error(sprintf(_("You must enter Cheque #")));
        set_focus('code_id');
        return false;
    }
}

    if (!get_post('BranchID'))
    {
        display_error(_("This customer has no branch defined."));
        set_focus('BranchID');
        return false;
    }

    if (!isset($_POST['DateBanked']) || !is_date($_POST['DateBanked'])) {
        display_error(_("The entered date is invalid. Please enter a valid date for the payment."));
        set_focus('DateBanked');
        return false;
    } elseif (!is_date_in_fiscalyear($_POST['DateBanked'])) {
        display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('DateBanked');
        return false;
    }

    if (!check_reference($_POST['ref'], ST_CUSTPAYMENT, @$_POST['trans_no'])) {
        set_focus('ref');
        return false;
    }

    if (!check_num('amount', 0)) {
        display_error(_("The entered amount is invalid or negative and cannot be processed."));
        set_focus('amount');
        return false;
    }

    if (isset($_POST['charge']) && !check_num('charge', 0)) {
        display_error(_("The entered amount is invalid or negative and cannot be processed."));
        set_focus('charge');
        return false;
    }
    if (isset($_POST['charge']) && input_num('charge') > 0) {
        $charge_acct = get_bank_charge_account($_POST['bank_account']);
        if (get_gl_account($charge_acct) == false) {
            display_error(_("The Bank Charge Account has not been set in System and General GL Setup."));
            set_focus('charge');
            return false;
        }
    }
//Ryan :06-05-17
    if (@$_POST['discount'] == "")
    {
        $_POST['discount'] = 0;
    }
    if ($_POST['gst_wh'] == "")
    {
        $_POST['gst_wh'] = 0;
    }

    if ($_POST['wht_supply_amt'] == "")
    {
        $_POST['wht_supply_amt'] = 0;
    }

    if ($_POST['wht_service_amt'] == "")
    {
        $_POST['wht_service_amt'] = 0;
    }

    if ($_POST['wht_fbr_amt'] == "")
    {
        $_POST['wht_fbr_amt'] = 0;
    }

    if ($_POST['wht_srb_amt'] == "")
    {
        $_POST['wht_srb_amt'] = 0;
    }
    if (!check_num('gst_wh')) {
        display_error(_("The entered GST WH is not a valid number."));
        set_focus('gst_wh');
        return false;
    }
    //------------end-----------------------------

    if (!check_num('discount')) {
        display_error(_("The entered discount is not a valid number."));
        set_focus('discount');
        return false;
    }

    if (input_num('amount') <= 0) {
        display_error(_("The balance of the amount and discount is zero or negative. Please enter valid amounts."));
        set_focus('discount');
        return false;
    }

    if (isset($_POST['bank_amount']) && input_num('bank_amount')<=0)
    {
        display_error(_("The entered payment amount is zero or negative."));
        set_focus('bank_amount');
        return false;
    }
    $row = get_company_pref('back_days');
    $row1 = get_company_pref('future_days');
    $row2 = get_company_pref('deadline_time');
    if($row != '')
    {
        $diff   =  date_diff2(date('d-m-Y'),$_POST['DateBanked'], 'd');

        if($row == 0)

        {
            $allowed_days = 'before yesterday.';
        }

        else
            $allowed_days =  'more than '. $row . ' day old' ;

        if($diff > $row  ){

            display_error("You are not allowed to enter entries $allowed_days");
            return false;
        }

//		else
//		{
//			if($diff < 0 )
//			{
//				display_error("You are not allowed to enter data $row day/s ahead");
//				return false;
//			}

        //}

    }

    if($row1 != '')
    {

        $diff_futuredays   =  date_diff2($_POST['DateBanked'],date('d-m-Y'), 'd');

        if( $diff_futuredays > $row1)
        {
            //	display_error($diff_futuredays);
            display_error("You are not allowed to enter data $row1 day/s ahead");

            return false ;

        }

    }
    if($row2 != '')
    {

        $now = date('h:i:s');

        if($row2 != 0)
        {
            $allowed_time = 'after '. $row2;
        }
        else
            $allowed_time=  '' ;

        if($row2 < $now )
        {
            display_error("You are not allowed to enter data $allowed_time pm");
            return false ;
        }

    }

    if (!db_has_currency_rates(get_customer_currency($_POST['customer_id']), $_POST['DateBanked'], true))
        return false;

    $_SESSION['alloc']->amount = input_num('amount');
//    $_SESSION['alloc']->amount = input_num('fixed_amount');

    if (isset($_POST["TotalNumberOfAllocs"]))
        return check_allocations();
    else
        return true;
}

//----------------------------------------------------------------------------------------------

if (isset($_POST['_customer_id_button'])) {
//	unset($_POST['branch_id']);
    $Ajax->activate('BranchID');
}

//----------------------------------------------------------------------------------------------

if (get_post('AddPaymentItem') && can_process()) {

    new_doc_date($_POST['DateBanked']);

    $new_pmt = !$_SESSION['alloc']->trans_no;
    //Chaitanya : 13-OCT-2011 - To support Edit feature
    //Ryan :06-05-17
    $payment_no = write_customer_payment($_SESSION['alloc']->trans_no, $_POST['customer_id'], $_POST['BranchID'],
        $_POST['bank_account'], $_POST['DateBanked'], $_POST['ref'],
        input_num('amount'), input_num('discount'), $_POST['dimension_id'],$_POST['dimension2_id'],$_POST['memo_'], 0, input_num('charge'),
        input_num('bank_amount'),input_num('gst_wh'),$_POST['wht_supply_tax'], $_POST['wht_service_tax'], $_POST['wht_fbr_tax'],
        $_POST['wht_srb_tax'], input_num('wht_supply_amt'), input_num('wht_service_amt'),
        input_num('wht_fbr_amt'), input_num('wht_srb_amt'), $_POST['write_back'], $_POST['cheque_date']
        , $_POST['cheque_no'], $_POST['text_1'], $_POST['text_2'], $_POST['text_3']
    );
//-----
    $_SESSION['alloc']->trans_no = $payment_no;
    //23-10-2017 date issue thats why
    $_SESSION['alloc']->date_ = $_POST['DateBanked'];

    $_SESSION['alloc']->write();

    unset($_SESSION['alloc']);
    meta_forward($_SERVER['PHP_SELF'], $new_pmt ? "AddedID=$payment_no" : "UpdatedID=$payment_no");
}

//----------------------------------------------------------------------------------------------

function read_customer_data()
{
    global $Refs;

    $myrow = get_customer_habit($_POST['customer_id']);

    $_POST['HoldAccount'] = $myrow["dissallow_invoices"];
    $_POST['pymt_discount'] = $myrow["pymt_discount"];
    // To support Edit feature
    // If page is called first time and New entry fetch the nex reference number
    if (!$_SESSION['alloc']->trans_no && !isset($_POST['charge']))
        $_POST['ref'] = $Refs->get_next(ST_CUSTPAYMENT, null, array(
            'customer' => get_post('customer_id'), 'date' => get_post('DateBanked')));
}

//----------------------------------------------------------------------------------------------
$new = 1;

// To support Edit feature
if (isset($_GET['trans_no']) && $_GET['trans_no'] > 0 )
{
    $_POST['trans_no'] = $_GET['trans_no'];

    $new = 0;
    $myrow = get_customer_trans($_POST['trans_no'], ST_CUSTPAYMENT);
    $_POST['customer_id'] = $myrow["debtor_no"];
    $_POST['customer_name'] = $myrow["DebtorName"];
    $_POST['BranchID'] = $myrow["branch_code"];
    $_POST['bank_account'] = $myrow["bank_act"];
    $_POST['ref'] =  $myrow["reference"];
    $_POST['cheque_no'] =  $myrow["cheque_no"];
    $_POST['cheque_date'] =  sql2date($myrow["cheque_date"]);
    $_POST['text_1'] =  $myrow["text_1"];
    $_POST['text_2'] =  $myrow["text_2"];
    $_POST['text_3'] =  $myrow["text_3"];
    $charge = get_cust_bank_charge(ST_CUSTPAYMENT, $_POST['trans_no']);
    $_POST['charge'] =  price_format($charge);
    $_POST['DateBanked'] =  sql2date($myrow['tran_date']);
    $_POST["amount"] = price_format($myrow['Total'] - $myrow['ov_discount']  - $myrow['supply_disc']  - $myrow['service_disc'] - $myrow['fbr_disc'] - $myrow['srb_disc']);
    $_POST["fixed_amount"] = price_format($myrow['Total'] );
    $_POST["bank_amount"] = price_format($myrow['bank_amount']+$charge);
    $_POST["discount"] = price_format($myrow['ov_discount']);
    $_POST["memo_"] = get_comments_string(ST_CUSTPAYMENT,$_POST['trans_no']);

    //Prepare allocation cart
    if (isset($_POST['trans_no']) && $_POST['trans_no'] > 0 )
        $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT,$_POST['trans_no']);
    else
    {
        $_SESSION['alloc'] = new allocation(ST_CUSTPAYMENT, $_POST['trans_no']);
        $Ajax->activate('alloc_tbl');
    }
}

//----------------------------------------------------------------------------------------------
$new = !$_SESSION['alloc']->trans_no;
start_form();

hidden('trans_no');
//Ryan :06-05-17
if(isset($_POST['get_final_amount'])) {

    //	display_error($_POST['amount_new']);
    $Ajax->activate('amount');
    $_POST['amount']=$_POST['amount_new'];
}
//---------end------------
start_outer_table(TABLESTYLE2, "width='60%'", 5);

table_section(1);

//dz 8.9.17
//bank_accounts_list_row(_("Into Bank Account:"), 'bank_account', null, true);
$type =get_account_types_new($_POST['bank_account']);

bank_accounts_list_all_row(_("Into Bank Account:"), 'bank_account', null, true);

if ($new)
    customer_list_row(_("From Customer:"), 'customer_id', null, false, true);
else {
    label_cells(_("From Customer:"), $_SESSION['alloc']->person_name, "class='label'");
    hidden('customer_id', $_POST['customer_id']);
}

if (list_updated('customer_id') || list_updated('BranchID') || ($new && list_updated('bank_account'))) {
    $_SESSION['alloc']->read(null, 0, null, null, $_POST['BranchID']);
    $_POST['memo_'] = $_POST['amount'] = $_POST['fixed_amount'] = $_POST['discount'] = '';
    $Ajax->activate('alloc_tbl');
}

if (db_customer_has_branches($_POST['customer_id'])) {
    customer_branches_list_row(_("Branch:"), $_POST['customer_id'], 'BranchID', null, false, true, true);
} else {
    hidden('BranchID', ANY_NUMERIC);
}

$GetCreditBalance = get_credit_balance_customer($_POST['customer_id']);
supplier_credit_row($_POST['customer_id'], $GetCreditBalance);

if (list_updated('customer_id') || ($new && list_updated('bank_account'))) {
    $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER,$_POST['BranchID']);
    $_SESSION['alloc']->read(null, 0, null, null, $_POST['BranchID']);
    $_POST['memo_'] = $_POST['amount'] = $_POST['fixed_amount'] = $_POST['discount'] = '';
    $Ajax->activate('alloc_tbl');
}

read_customer_data();

set_global_customer($_POST['customer_id']);
set_global_bank($_POST['bank_account']);

if (isset($_POST['HoldAccount']) && $_POST['HoldAccount'] != 0)
    display_warning(_("This customer account is on hold."));
$display_discount_percent = percent_format($_POST['pymt_discount']*100) . "%";

table_section(2);
date_row(_("Date of Deposit:"), 'DateBanked', '', true, 0, 0, 0, null, true);
if($type!=3) {

date_row(_("Cheque Date:"), 'cheque_date', '', true, 0, 0, 0, null, true);
text_row("Cheque #",'cheque_no');
}
// ref_row(_("Reference:"), 'ref','' , null, '', ST_CUSTPAYMENT);
ref_row(_("Reference:"), 'ref', null, get_post('ref'), false, ST_CUSTPAYMENT, get_post('DateBanked'));


table_section(3);

$comp_currency = get_company_currency();
$cust_currency = $_SESSION['alloc']->set_person($_POST['customer_id'], PT_CUSTOMER,$_POST['BranchID']);
if (!$cust_currency)
    $cust_currency = $comp_currency;
$_SESSION['alloc']->currency = $bank_currency = get_bank_account_currency($_POST['bank_account']);

if ($cust_currency != $bank_currency)
{
    amount_row(_("Payment Amount:"), 'bank_amount', null, '', $bank_currency);
}
if($type!=3) {

amount_row(_("Bank Charge:"), 'charge', null, '', $bank_currency);
text_row("Customer Bank",'text_1');
text_row("Customer Bank Branch",'text_2');
}
text_row("Deposit Slip No.",'text_3');

end_outer_table(1);


div_start('alloc_tbl');
show_allocatable(false);
div_end();
//Ryan :06-05-17
start_table(TABLESTYLE, "width=60%");

label_row(_("Customer prompt payment discount :"), $display_discount_percent);
// text_row(_("Advance Payment:"),'advance_payment');
start_row();
//Ryan :06-05-17
if($_GET['trans_no']) {
    $cust_trans = get_customer_trans($_GET['trans_no'], 12);
//
//	$_POST['wht_supply_tax'] = $cust_trans['supply_tax_id'];
//	$_POST['wht_service_tax'] = $cust_trans['service_tax_id'];
//	$_POST['wht_fbr_tax'] = $cust_trans['fbr_tax_id'];
//	$_POST['wht_srb_tax'] = $cust_trans['srb_tax_id'];
//
    $_POST['wht_supply_amt1'] = number_format2($cust_trans['supply_disc'], 2);
    $_POST['wht_service_amt1'] = number_format2($cust_trans['service_disc'], 2);
    $_POST['wht_fbr_amt1'] = number_format2($cust_trans['fbr_disc'], 2);
    $_POST['wht_srb_amt1'] = number_format2($cust_trans['srb_disc'], 2);
//	$_POST['amount'] = number_format2($cust_trans['ov_amount'], 2);
}

$result = get_wht_types_name();

while ($myrow = db_fetch($result)) {
    label_row($myrow['label_name']);
}

$a=get_wht_tax_category(1);
$b=get_wht_tax_category(2);
$c=get_wht_tax_category(3);
$d=get_wht_tax_category(4);


wth_tax_type_list_cells($a['description'], 'wht_supply_tax', null,
    _("Select Tax Type"), true,null,null,1); //asad
hidden('wht_supply_percent', $_POST['wht_supply_percent']);



amount_cells_ex(_("WHT on Supplies Amount :"), 'wht_supply_amt',10,40);

label_cells("",$_POST['wht_supply_amt1']);
end_row();
//Ryan :06-05-17
start_row();
wth_tax_type_list_cells($b['description'], 'wht_service_tax', null,	_("Select Tax Type"), true,null,null,2); //asad
hidden('wht_service_percent', $_POST['wht_service_percent']);
amount_cells_ex(_("WHT on Services Amount :"), 'wht_service_amt',10,50);
//if($_POST['wht_service_amt'] == 0)
//	hidden('wht_service_amt',$_POST['wht_service_amt1']);

label_cells("",$_POST['wht_service_amt1']);
end_row();
//Ryan :06-05-17
start_row();

wth_tax_type_list_cells($c['description'], 'wht_fbr_tax', null,	_("Select Tax Type"), true,null,null,3); //asad
amount_cells_ex(_("ST WH FBR Amount :"), 'wht_fbr_amt',10,50,null,null,null,null,true);
//amount_cells_ex_searchable(_("ST WH FBR Amount :"), 'wht_fbr_amt',10,50);
label_cells("",$_POST['wht_fbr_amt1']);
end_row();

//Ryan :06-05-17
start_row();
wth_tax_type_list_cells($d['description'], 'wht_srb_tax', null,
    _("Select Tax Type"), true,null,null,4);
//ref_cells("ST WH SRB/PRA Amount :",'wht_srb_amt');
amount_cells_ex(_("ST WH SRB/PRA Amount :"), 'wht_srb_amt',10,50,null,null,null,null,true);
label_cells("",$_POST['wht_srb_amt1']);
end_row();

//Ryan :06-05-17
amount_cells_ex(_("Write Off : "), 'discount',15,40);
$display_discount_percent = percent_format($_POST['pymt_discount']*100) . "%";
hidden('gst_wh', $_POST['gst_wh']);


echo"<tr>";
amount_cells_ex(_("Total Amount:"), 'amount', 25,50);
echo"</tr>";
//text_row_ex(_("Total Amount:"), 'fixed_amount',15,50, null, null," class='tableheader2' ");
if($_GET['trans_no']) {
//    $_POST['fixed_amount'] = $_POST['amount'];

}
text_cells_readonly("", 'fixed_amount');
//hidden('fixed_amount');

hidden('amount_new',$_POST['amount_new']);
//amount_row(_("Total Calculated value:"), 'amount_new', null, " class='tableheader2' ");

hidden('trans_no',$_GET['trans_no']);

textarea_row(_("Memo:"), 'memo_', null, 22, 4);





end_table(1);

if(!$_GET['trans_no']) {
//      echo "<div style='margin: 0px 150px 70px 70px; ' ><input style='background-color: white; font-weight: bold; height: 30px;width:100px;' type='button'  value='Calculate' onclick='calculate_final_amount();'   >
//</div>";
//    submit_calc_cells('', "Calculate", null, null, true);
}
else {


    echo "<tr><td>";
    echo "<a href='#' name=''  onclick='calc();return true;' >Calculate</a>";
    echo "</td></tr>";
}
br();

if ($new)
    submit_center('AddPaymentItem', _("Add Payment"), true, '', 'default');
else
    submit_center('AddPaymentItem', _("Update Payment"), true, '', 'default');

br();

end_form();
end_page();





