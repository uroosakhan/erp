<?php

$page_security = 'SA_BANKTRANSFER';
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/gl/includes/gl_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();

if (isset($_GET['ModifyTransfer'])) {
    $_SESSION['page_title'] = _($help_context = "Modify Bank Account Transfer");
} else {
    $_SESSION['page_title'] = _($help_context = "Bank Account Transfer Entry");
}
page($_SESSION['page_title'], false, false, "", $js);
check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));
if(isset($_GET['trans_no1']))
{
    unset($_SESSION['ParentID']);
    $_SESSION['ParentID'] = $_GET['trans_no1'];
}
if(isset($_GET['trans_type']))
{
    unset($_SESSION['ParentType']);
    $_SESSION['ParentType'] = $_GET['trans_type'];
}

//----------------------------------------------------------------------------------------
//if (isset($_GET['trans_no']))
//{
//    $_POST['trans_no'] = $_GET['trans_no'];
//}
if (isset($_GET['AddedID']))
{
    $trans_no = $_GET['AddedID'];
    $trans_type = ST_BANKTRANSFER;

    display_notification_centered( _("Transfer has been entered"));

    display_note(get_gl_view_str($trans_type, $trans_no, _("&View the GL Journal Entries for this Transfer")));

    hyperlink_no_params($_SERVER['PHP_SELF'], _("Enter &Another Transfer"));

    echo "</br>";
    submenu_print(_("&Print This Payment"), $trans_type, $trans_no, 'prtopt');

    display_footer_exit();
}

if (isset($_POST['_DatePaid_changed'])) {
    $Ajax->activate('_ex_rate');
}

//----------------------------------------------------------------------------------------

function gl_payment_controls($trans_no)
{
    global $Refs;

    if (!in_ajax()) {
        if ($trans_no) {

            $result = get_bank_trans(ST_BANKTRANSFER, $trans_no);

            if (db_num_rows($result) != 2)
                display_db_error("Bank transfer does not contain two records");

            $trans1 = db_fetch($result);
            $trans2 = db_fetch($result);

            if ($trans1["amount"] <= 0)
            {
                $from_trans = $trans1; // from trans is the negative one
                $to_trans = $trans2;
            }
            else
            {
                $from_trans = $trans2;
                $to_trans = $trans1;
            }
            $_POST['DatePaid'] = sql2date($to_trans['trans_date']);
            $_POST['ref'] = $to_trans['ref'];
            $_POST['memo_'] = get_comments_string($to_trans['type'], $trans_no);
            $_POST['FromBankAccount'] = $from_trans['bank_act'];
            $_POST['ToBankAccount'] = $to_trans['bank_act'];
            $_POST['target_amount'] = price_format($to_trans['amount']);
            $_POST['amount'] = price_format(-$from_trans['amount']);
            $_POST['cheque'] = $to_trans['cheque'];
        }

        elseif (!$trans_no && $_GET['trans_no1']) {
            if($_GET['trans_type']==12 || $_GET['trans_type']==2)
                $record = get_dispatch_bank_trans2($_GET['trans_type'],$_GET['trans_no1']);
            else
                $record = get_dispatch_bank_trans($_GET['trans_type'],$_GET['trans_no1']);
            $record2 = get_dispatch_bank_trans3($_GET['trans_type'],$_GET['trans_no1']);
            $_POST['ref'] = $Refs->get_next(ST_BANKTRANSFER);
            $_POST['memo_'] = '';
            if($record['amount']<0)
            {
                $_POST['FromBankAccount'] = get_bank_acc($record2['account']);
                $_POST['ToBankAccount'] = get_bank_acc($record2['account']);
            }
            elseif($record['amount']>0)
            {
                $_POST['FromBankAccount'] = get_bank_acc($record['account']);
                $_POST['ToBankAccount'] = get_bank_acc($record['account']);
            }
            $_POST['amount'] = abs($record['amount']);
            if($record["cheque_no"]!=0)
                $_POST['cheque'] = $record["cheque_no"];
            else
                $_POST['cheque'] = $record["cheque"];
            $_POST['cheque_date'] = sql2date($record["cheque_date"]);
        }
        else
        {
            $_POST['ref'] = $Refs->get_next(ST_BANKTRANSFER);
            $_POST['memo_'] = '';
            $_POST['FromBankAccount'] = 0;
            $_POST['ToBankAccount'] = 0;
            $_POST['amount'] = 0;
        }
    }

    start_form();

    start_outer_table(TABLESTYLE2);

    table_section(1);
    bank_accounts_list_all_row(_("From Account:"), 'FromBankAccount', null, true);
    bank_balance_row($_POST['FromBankAccount']);
    bank_accounts_list_all_row(_("To Account:"), 'ToBankAccount', null, true);

    if (!isset($_POST['DatePaid'])) { // init page
        $_POST['DatePaid'] = new_doc_date();
        if (!is_date_in_fiscalyear($_POST['DatePaid']))
            $_POST['DatePaid'] = end_fiscalyear();
    }
    date_row(_("Transfer Date:"), 'DatePaid', '', true, 0, 0, 0, null, true);

    ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_BANKTRANSFER, null, get_post('DatePaid')), false, ST_BANKTRANSFER,
        array('date' => get_post('DatePaid')));

    if($_GET['trans_no1'])
    {
        date_row_disabled("Cheque Date", 'cheque_date');
    }
    else
        date_row("Cheque Date", 'cheque_date');

    table_section(2);

    $from_currency = get_bank_account_currency($_POST['FromBankAccount']);
    $to_currency = get_bank_account_currency($_POST['ToBankAccount']);
    if ($from_currency != "" && $to_currency != "" && $from_currency != $to_currency)
    {
        if($_GET['trans_no1']) {
            text_cells_ex_disabled(_("Amount:"), 'amount', '', null, null, null,
                null, $from_currency, false, 150);
            hidden('amount',$_POST['amount']);
        }
        else
        {
            amount_row(_("Amount:"), 'amount', null, null, $from_currency);
        }
        amount_row(_("Bank Charge:"), 'charge', null, null, $from_currency);
        amount_row(_("Incoming Amount:"), 'target_amount', null, '', $to_currency, 2);
    }
    else
    {
        if($_SESSION['ParentID'])
            text_cells_ex_disabled(_("Amount:"), 'amount', '', null, null, null,
                null, null, false, 150);
        else
            amount_cells_ex(_("Amount:"), 'amount', 15, 50);

        text_row(_("Bank Charge:"), 'charge',null,15);
    }
    if($_SESSION['ParentID'])
        text_cells_ex_disabled(_("Cheque No:"), 'cheque', '', null, null, null,
            null, null, false, 150);
    else
        text_cells(_("Cheque No:"), 'cheque');
    textarea_row(_("Memo:"), 'memo_', null, 40,4);

    end_outer_table(1); // outer table

    if ($trans_no) {
        hidden('_trans_no', $trans_no);
        submit_center('submit', _("Modify Transfer"), true, '', 'default');
    } else {
        submit_center('submit', _("Enter Transfer"), true, '', 'default');
    }

    end_form();
}

//----------------------------------------------------------------------------------------

function check_valid_entries($trans_no)
{
    global $Refs, $systypes_array;

    if (!is_date($_POST['DatePaid']))
    {
        display_error(_("The entered date is invalid."));
        set_focus('DatePaid');
        return false;
    }
    if (!is_date_in_fiscalyear($_POST['DatePaid']))
    {
        display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
        set_focus('DatePaid');
        return false;
    }

    if (!check_num('amount', 0))
    {
        display_error(_("The entered amount is invalid or less than zero."));
        set_focus('amount');
        return false;
    }
    if (input_num('amount') == 0) {
        display_error(_("The total bank amount cannot be 0."));
        set_focus('amount');
        return false;
    }

if (get_account_type_id($_POST['ToBankAccount'])==4)
{
    if ($_POST['cheque']=="")
    {
        display_error(sprintf(_("You must enter Cheque #")));
        set_focus('code_id');
        return false;
    }
}

    $limit = get_bank_account_limit($_POST['FromBankAccount'], $_POST['DatePaid']);

    $amnt_tr = input_num('charge') + input_num('amount');
    if (get_company_pref('cash_neg_allow')!=1){


        if ($trans_no) {
            $problemTransaction = check_bank_transfer( $trans_no, $_POST['FromBankAccount'], $_POST['ToBankAccount'], $_POST['DatePaid'],
                $amnt_tr, input_num('target_amount', $amnt_tr));

            if ($problemTransaction != null	) {
                if (!array_key_exists('trans_no', $problemTransaction)) {
                    display_error(sprintf(
                        _("This bank transfer change would result in exceeding authorized overdraft limit (%s) of the account '%s'"),
                        price_format(-$problemTransaction['amount']), $problemTransaction['bank_account_name']
                    ));
                } else {
                    display_error(sprintf(
                        _("This bank transfer change would result in exceeding authorized overdraft limit on '%s' for transaction: %s #%s on %s."),
                        $problemTransaction['bank_account_name'], $systypes_array[$problemTransaction['type']],
                        $problemTransaction['trans_no'], sql2date($problemTransaction['trans_date'])
                    ));
                }
                set_focus('amount');
                return false;
            }
        } else {
            if (null != ($problemTransaction = check_bank_account_history(-$amnt_tr, $_POST['FromBankAccount'], $_POST['DatePaid']))) {
                if (!array_key_exists('trans_no', $problemTransaction)) {
                    display_error(sprintf(
                        _("This bank transfer would result in exceeding authorized overdraft limit of the account (%s)"),
                        price_format(-$problemTransaction['amount'])
                    ));
                } else {
                    display_error(sprintf(
                        _("This bank transfer would result in exceeding authorized overdraft limit for transaction: %s #%s on %s."),
                        $systypes_array[$problemTransaction['type']], $problemTransaction['trans_no'], sql2date($problemTransaction['trans_date'])
                    ));
                }
                set_focus('amount');
                return false;
            }
        }

    }
    if (isset($_POST['charge']) && !check_num('charge', 0))
    {
        display_error(_("The entered amount is invalid or less than zero."));
        set_focus('charge');
        return false;
    }
    if (isset($_POST['charge']) && input_num('charge') > 0 && get_bank_charge_account($_POST['FromBankAccount']) == '') {
        display_error(_("The Bank Charge Account has not been set in System and General GL Setup."));
        set_focus('charge');
        return false;
    }

    if (!check_reference($_POST['ref'], ST_BANKTRANSFER, $trans_no)) {
        set_focus('ref');
        return false;
    }

    if ($_POST['FromBankAccount'] == $_POST['ToBankAccount'])
    {
        display_error(_("The source and destination bank accouts cannot be the same."));
        set_focus('ToBankAccount');
        return false;
    }

    if (isset($_POST['target_amount']) && !check_num('target_amount', 0))
    {
        display_error(_("The entered amount is invalid or less than zero."));
        set_focus('target_amount');
        return false;
    }
    if (isset($_POST['target_amount']) && input_num('target_amount') == 0) {
        display_error(_("The incomming bank amount cannot be 0."));
        set_focus('target_amount');
        return false;
    }

    if (!db_has_currency_rates(get_bank_account_currency($_POST['FromBankAccount']), $_POST['DatePaid']))
        return false;

    if (!db_has_currency_rates(get_bank_account_currency($_POST['ToBankAccount']), $_POST['DatePaid']))
        return false;


    $row = get_company_pref('back_days');
    $row1 = get_company_pref('future_days');
    $row2 = get_company_pref('deadline_time');
    if($row != '')
    {
        $diff   =  date_diff2(date('d-m-Y'),$_POST['DatePaid'], 'd');

// 		if($row == 0)

// 		{
// 			$allowed_days = 'before yesterday.';
// 		}

// 		else
// 			$allowed_days =  'more than '. $row . ' day old' ;

        if($diff > $row  ){

            display_error("You are not allowed to enter entries before $row . day old");
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

        $diff_futuredays   =  date_diff2($_POST['DatePaid'],date('d-m-Y'), 'd');

        if( $diff_futuredays > $row1)
        {


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

        if($row2 > $now )
        {
            display_error("You are not allowed to enter data $allowed_time pm");
            return false ;
        }

    }

    return true;
}
//----------------------------------------------------------------------------------------

function bank_transfer_handle_submit()
{
    $trans_no = array_key_exists('_trans_no', $_POST) ?  $_POST['_trans_no'] : null;
    if ($trans_no) {
        $trans_no = update_bank_transfer($trans_no, $_POST['FromBankAccount'], $_POST['ToBankAccount'], $_POST['DatePaid'], input_num('amount'), $_POST['ref'], $_POST['memo_'], input_num('charge'), input_num('target_amount'), $_POST['cheque'], $_SESSION['ParentID'], $_SESSION['ParentType'], $_POST['cheque_date']);

    }
    else
    {

        new_doc_date($_POST['DatePaid']);

        $trans_no = add_bank_transfer($_POST['FromBankAccount'], $_POST['ToBankAccount'], $_POST['DatePaid'], input_num('amount'), $_POST['ref'], $_POST['memo_'], input_num('charge'), input_num('target_amount'), $_POST['cheque'], $_SESSION['ParentID'], $_SESSION['ParentType'], $_POST['cheque_date']);
        unset($_SESSION['ParentID']);
        unset($_SESSION['ParentType']);

    }

    meta_forward($_SERVER['PHP_SELF'], "AddedID=$trans_no");
}

//----------------------------------------------------------------------------------------
if (!$trans_no && isset($_POST['_trans_no'])) {
    $trans_no = $_POST['_trans_no'];
}
if (!$trans_no && isset($_GET['trans_no'])) {
    $trans_no = $_GET["trans_no"];
}

if (isset($_POST['submit'])) {
    if (check_valid_entries($trans_no) == true) {

        bank_transfer_handle_submit();


    }
}

gl_payment_controls($trans_no);

end_page();
