<?php
$path_to_root = "..";

include_once($path_to_root . "/includes/ui/items_cart_bulk.inc");

include_once($path_to_root . "/includes/session.inc");

$page_security = isset($_GET['NewPayment']) ||
	@($_SESSION['pay_items']->trans_type==ST_BANKPAYMENT)
 ? 'SA_PAYMENT' : 'SA_DEPOSIT';

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/gl/includes/ui/gl_bank_ui_bulk.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/gl/includes/gl_ui.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();

if (isset($_GET['NewPayment'])) {
	$_SESSION['page_title'] = _($help_context = "Bank Account Payment Entry");

	create_cart(ST_BANKPAYMENT, 0);
} else if(isset($_GET['NewDeposit'])) {
	$_SESSION['page_title'] = _($help_context = "Bank Account Deposit Entry");
	create_cart(ST_BANKDEPOSIT, 0);
} else if(isset($_GET['ModifyPayment'])) {
	$_SESSION['page_title'] = _($help_context = "Modify Bank Account Entry")." #".$_GET['trans_no'];
	create_cart(ST_BANKPAYMENT, $_GET['trans_no']);
} else if(isset($_GET['ModifyDeposit'])) {
	$_SESSION['page_title'] = _($help_context = "Modify Bank Deposit Entry")." #".$_GET['trans_no'];
	create_cart(ST_BANKDEPOSIT, $_GET['trans_no']);
}
page($_SESSION['page_title'], false, false, '', $js);

//-----------------------------------------------------------------------------------------------
check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------
if (list_updated('PersonDetailID')) {
	$br = get_branch(get_post('PersonDetailID'));
	$_POST['person_id'] = $br['debtor_no'];
	$Ajax->activate('person_id');
}

//--------------------------------------------------------------------------------------------------
function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_code_id_edit');
}

//-----------------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
	$trans_no = $_GET['AddedID'];
	$trans_type = ST_BANKPAYMENT;

   	display_notification_centered(sprintf(_("Payment %d has been entered"), $trans_no));

	display_note(get_gl_view_str($trans_type, $trans_no, _("&View the GL Postings for this Payment")));

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another &Payment"), "NewPayment=yes");

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter A &Deposit"), "NewDeposit=yes");

	hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$trans_no");

	display_footer_exit();
}

if (isset($_GET['UpdatedID']))
{
	$trans_no = $_GET['UpdatedID'];
	$trans_type = ST_BANKPAYMENT;

   	display_notification_centered(sprintf(_("Payment %d has been modified"), $trans_no));

	display_note(get_gl_view_str($trans_type, $trans_no, _("&View the GL Postings for this Payment")));

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another &Payment"), "NewPayment=yes");

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter A &Deposit"), "NewDeposit=yes");

	display_footer_exit();
}

if (isset($_GET['AddedDep']))
{
	$trans_no = $_GET['AddedDep'];
	$trans_type = ST_BANKDEPOSIT;

   	display_notification_centered(sprintf(_("Deposit %d has been entered"), $trans_no));

	display_note(get_gl_view_str($trans_type, $trans_no, _("View the GL Postings for this Deposit")));

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another Deposit"), "NewDeposit=yes");

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter A Payment"), "NewPayment=yes");

	display_footer_exit();
}
if (isset($_GET['UpdatedDep']))
{
	$trans_no = $_GET['UpdatedDep'];
	$trans_type = ST_BANKDEPOSIT;

   	display_notification_centered(sprintf(_("Deposit %d has been modified"), $trans_no));

	display_note(get_gl_view_str($trans_type, $trans_no, _("&View the GL Postings for this Deposit")));

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another &Deposit"), "NewDeposit=yes");

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter A &Payment"), "NewPayment=yes");

	display_footer_exit();
}

if (isset($_POST['_date__changed'])) {
	$Ajax->activate('_ex_rate');
}
//--------------------------------------------------------------------------------------------------

function create_cart($type, $trans_no)
{
	global $Refs;

	if (isset($_SESSION['pay_items']))
	{
		unset ($_SESSION['pay_items']);
	}

	$cart = new items_cart_bulk($type);
    $cart->order_id = $trans_no;

	if ($trans_no)
	{
		$bank_trans = db_fetch(get_bank_trans($type, $trans_no));
		$_POST['bank_account'] = $bank_trans["bank_act"];
		$_POST['PayType'] = $bank_trans["person_type_id"];

		if ($bank_trans["person_type_id"] == PT_CUSTOMER)
		{
			$trans = get_customer_trans($trans_no, $type);
			$_POST['person_id'] = $trans["debtor_no"];
			$_POST['PersonDetailID'] = $trans["branch_code"];
		}
		elseif ($bank_trans["person_type_id"] == PT_SUPPLIER)
		{
			$trans = get_supp_trans($trans_no, $type);
			$_POST['person_id'] = $trans["supplier_id"];
		}
		elseif ($bank_trans["person_type_id"] == PT_MISC)
			$_POST['person_id'] = $bank_trans["person_id"];
		elseif ($bank_trans["person_type_id"] == PT_QUICKENTRY)
			$_POST['person_id'] = $bank_trans["person_id"];
		else
			$_POST['person_id'] = $bank_trans["person_id"];

		$cart->memo_ = get_comments_string($type, $trans_no);
		$cart->tran_date = sql2date($bank_trans['trans_date']);
		$cart->reference = $Refs->_get($type, $trans_no);
		$cart->cheque_no = $bank_trans['cheque_no'];
		$cart->cheque_date = $bank_trans['cheque_date'];
		$cart->original_amount = $bank_trans['amount'];
		$result = get_gl_trans($type, $trans_no);
		if ($result) {
			while ($row = db_fetch($result)) {
				if (is_bank_account($row['account'])) {
					// date exchange rate is currenly not stored in bank transaction,
					// so we have to restore it from original gl amounts
					$ex_rate = $bank_trans['amount']/$row['amount'];
				} else {
					$date = $row['tran_date'];
					$cart->add_gl_item( $row['account'], $row['dimension_id'],
						$row['dimension2_id'],$row['dimension3_id'], $row['amount'], $row['memo_']);
				}
			}
		}

		// apply exchange rate
		foreach($cart->gl_items as $line_no => $line)
			$cart->gl_items[$line_no]->amount *= $ex_rate;

	}
	else
	{
		$cart->reference = $Refs->get_next($cart->trans_type);
		$cart->tran_date = new_doc_date();
		//if (!is_date_in_fiscalyear($cart->tran_date))
			$cart->tran_date = end_fiscalyear();
	}

	$_POST['memo_'] = $cart->memo_;
	$_POST['ref'] = $cart->reference;
	$_POST['date_'] = $cart->tran_date;
	$_POST['cheque_no'] = $cart->cheque_no;
	$_POST['cheque_date'] = $cart->cheque_date;
    $_POST['reference2'] = $cart->reference2;
    $_POST['serial'] = $cart->serial;
    $_POST['division'] = $cart->division;
	$_SESSION['pay_items'] = &$cart;
}
//-----------------------------------------------------------------------------------------------

function check_duplication_entry($division,$month,$pament_mode,$fiscal)
{
	$sql = "SELECT COUNT(*) FROM `0_bank_trans` WHERE dimension_id=$division AND `month`=$month AND `pament_mode`=$pament_mode AND `fiscal`=$fiscal";
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}

function check_amount_duplication_entry($division,$month,$pament_mode,$fiscal)
{
	$sql = "SELECT SUM(`amount`)*-1 FROM `0_bank_trans` WHERE dimension_id=$division AND `month`=$month AND `pament_mode`=$pament_mode AND `fiscal`=$fiscal";
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function check_amount_duplication_entry_new($division,$month,$pament_mode,$fiscal)
{
    $sql = "SELECT SUM(`amount`)*-1 FROM `0_bank_trans` WHERE dimension_id=$division AND `month`=$month AND `pament_mode`=$pament_mode AND `fiscal`=$fiscal";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_last_sales_items_total($division,$month,$mod_payment,$f_year)
{
    $sql = "SELECT SUM(0_payroll.`net_salary`) AS net_salary 
FROM  `0_payroll` , 0_employee
WHERE 0_employee.employee_id = 0_payroll.emp_id
AND 0_payroll.`divison` =$division
AND 0_payroll.`f_year` =$f_year
AND 0_payroll.`month` =$month
AND 0_employee.salary =$mod_payment
AND 0_employee.`mb_flag` IN('N','S')";
    $result = db_query($sql, "Could not get account.");
    $row = db_fetch_row($result);
    return $row[0];
}
if (isset($_POST['Process']))
{

	$input_error = 0;

	if ($_SESSION['pay_items']->count_gl_items() < 1) {
		display_error(_("You must enter at least one payment line."));
		set_focus('code_id');
		$input_error = 1;
	}

	if ($_SESSION['pay_items']->gl_items_total() == 0.0) {
		display_error(_("The total bank amount cannot be 0."));
		set_focus('code_id');
		$input_error = 1;
	}

	$limit = get_bank_account_limit($_POST['bank_account'], $_POST['date_']);

	$amnt_chg = -$_SESSION['pay_items']->gl_items_total()-$_SESSION['pay_items']->original_amount;

	if ($limit != null && ($limit + $amnt_chg < 0))
	{
		display_error(sprintf(_("The total bank amount exceeds allowed limit (%s)."), price_format($limit-$_SESSION['pay_items']->original_amount)));
		set_focus('code_id');
		$input_error = 1;
	}
	if ($trans = check_bank_account_history($amnt_chg, $_POST['bank_account'], $_POST['date_'])) {

		display_error(sprintf(_("The bank transaction would result in exceed of authorized overdraft limit for transaction: %s #%s on %s."),
			$systypes_array[$trans['type']], $trans['trans_no'], sql2date($trans['trans_date'])));
		set_focus('amount');
		$input_error = 1;
	}
//	if (!$Refs->is_valid($_POST['ref']))
//	{
//		display_error( _("You must enter a reference."));
//		set_focus('ref');
//		$input_error = 1;
//	}
//	elseif ($_POST['ref'] != $_SESSION['pay_items']->reference && !is_new_reference($_POST['ref'], $_SESSION['pay_items']->trans_type))
//	{
//		display_error( _("The entered reference is already in use."));
//		set_focus('ref');
//		$input_error = 1;
//	}
	if (!is_date($_POST['date_']))
	{
		display_error(_("The entered date for the payment is invalid."));
		set_focus('date_');
		$input_error = 1;
	}
	elseif (!is_date_in_fiscalyear($_POST['date_']))
	{
		display_error(_("The entered date is not in fiscal year. / or the entered date is older than a month"));
		set_focus('date_');
		$input_error = 1;
	}

	if (get_post('PayType')==PT_CUSTOMER && (!get_post('person_id') || !get_post('PersonDetailID'))) {
	display_error(_("You have to select customer and customer branch."));
	set_focus('person_id');
	$input_error = 1;
}
	$f_year = get_current_fiscalyear();
	$tamount=$_SESSION['pay_items']->gl_items_total();
	$p_amount=check_amount_duplication_entry_new($_POST['division'],$_POST['month'],$_POST['mod_payment'],$f_year['id']);
    $total_salary=.1+get_last_sales_items_total($_POST['division'],$_POST['month'],$_POST['mod_payment'],$f_year['id']);
$current_amount=$tamount+$p_amount;
	display_error($current_amount."-".$total_salary."-".$p_amount);

	if ($current_amount >= $total_salary) {
		display_error(_("Your amount should be  less than total amount".$tvalue));
		set_focus('amount');
		$input_error = 1;
	}
	$check=check_duplication_entry($_POST['division'],$_POST['mod_payment'],$_POST['month'],$f_year['id']);
	if ($check > 0) {
		display_error(_("Already Exist"));
		$input_error = 1;
	}

	if (!db_has_currency_rates(get_bank_account_currency($_POST['bank_account']), $_POST['date_'], true))
		$input_error = 1;

	if ($input_error == 1)
		unset($_POST['Process']);
}

if (isset($_POST['Process']))
{
	begin_transaction();

	$_SESSION['pay_items'] = &$_SESSION['pay_items'];
	$new = $_SESSION['pay_items']->order_id == 0;
	foreach ($_SESSION['pay_items']->gl_items as $gl_item) {

		$trans = write_bank_transaction_new(
			$_SESSION['pay_items']->trans_type,
			$_SESSION['pay_items']->order_id,
			$_POST['bank_account'],
			$_SESSION['pay_items'],
			$_POST['date_'],
			$_POST['PayType'],
			$gl_item->person_id,
			$gl_item->PersonDetailID,
            $_POST['ref'],
			$_POST['memo_'],
			true,
			$gl_item->code_id,
			$gl_item->person_id,
			$gl_item->amount,
            $abc,
		$_POST['cheque_no_line'],	
		$_POST['cheque_date_line'],	
            $gl_item->dimension_id,
            $gl_item->dimension2_id,
			$gl_item->dimension3_id,
            $gl_item->month,
            $gl_item->mod_payment,
            $_POST['reference2'],
            $_POST['serial'],
            $_POST['division'],
			$gl_item->fiscal
			
			);


	}
//$gl_item->cheque_no_line,$gl_item->cheque_date_line,
	/*$trans = write_bank_transaction(
		$_SESSION['pay_items']->trans_type, $_SESSION['pay_items']->order_id, $_POST['bank_account'],
		$_SESSION['pay_items'], $_POST['date_'],
		$_POST['PayType'], $_POST['person_id'], get_post('PersonDetailID'),
		$_POST['ref'], $_POST['memo_'], true, $_POST['cheque_no'], $_POST['cheque_date']);*/

	$trans_type = $trans[0];
   	$trans_no = $trans[1];
	new_doc_date($_POST['date_']);

	$_SESSION['pay_items']->clear_items();
	unset($_SESSION['pay_items']);

	commit_transaction();

	if ($new)
		meta_forward($_SERVER['PHP_SELF'], $trans_type==ST_BANKPAYMENT ?
			"AddedID=$trans_no" : "AddedDep=$trans_no");
	else
		meta_forward($_SERVER['PHP_SELF'], $trans_type==ST_BANKPAYMENT ?
			"UpdatedID=$trans_no" : "UpdatedDep=$trans_no");

}

//-----------------------------------------------------------------------------------------------

function check_item_data()
{
	if (!check_num('amount', 0))
	{
		display_error( _("The amount entered is not a valid number or is less than zero."));
		set_focus('amount');
		return false;
	}

	return true;
}
function check_item_data_duplication()
{
    if (!check_num('amount', input_num('amount')))
    {
        display_error( _("The entered information is duplicate."));
        set_focus('amount');
        return false;
    }

    return true;
}

//-----------------------------------------------------------------------------------------------

function handle_update_item()
{
	$amount = ($_SESSION['pay_items']->trans_type==ST_BANKPAYMENT ? 1:-1) * input_num('amount');
    if($_POST['UpdateItem'] != "" && check_item_data())
    {
    	$_SESSION['pay_items']->update_gl_item($_POST['Index'], $_POST['code_id'],
    	    $_POST['dimension_id'], $_POST['dimension2_id'], $_POST['dimension3_id'], $amount , $_POST['LineMemo'],null,
			$_POST['cheque_no_line'],$_POST['cheque_date_line'],$_POST['person_id'],$_POST['PersonDetailID'],$_POST['month'],$_POST['mod_payment']);
    }
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------

function handle_delete_item($id)
{
	$_SESSION['pay_items']->remove_gl_item($id);
	line_start_focus();
}

//-----------------------------------------------------------------------------------------------
function get_last_sales_items($division,$month,$mod_payment)
{
    $sql = "SELECT 0_payroll.`emp_id`, 0_employee.`mb_flag` , SUM(0_payroll.`tax`) AS tax , SUM(0_payroll.`net_salary`) AS net_salary , SUM(0_payroll.`eobi`) AS eobi 
,0_payroll.`f_year`
FROM  `0_payroll` , 0_employee
WHERE 0_employee.employee_id = 0_payroll.emp_id
AND 0_payroll.`divison` =$division
AND 0_payroll.`month` =$month
AND 0_employee.salary =$mod_payment
AND 0_employee.`mb_flag`='N' ";

    $result = db_query($sql, "Could not get account.");
    $num = db_num_rows($result);
    if ($num < 1)
    {
        display_warning("There Are No Sale Items For This Customer.");
    }
    else
    {
        return $result;
    }
}
function get_last_sales_items_($division,$month,$mod_payment)
{
    $sql = "SELECT 0_payroll.`emp_id`, 0_employee.`mb_flag`, SUM(0_payroll.`tax`) AS tax , SUM(0_payroll.`net_salary`) AS net_salary , SUM(0_payroll.`eobi`) AS eobi 
,0_payroll.`f_year`
FROM  `0_payroll` , 0_employee
WHERE 0_employee.employee_id = 0_payroll.emp_id
AND 0_payroll.`divison` =$division
AND 0_payroll.`month` =$month
AND 0_employee.salary =$mod_payment
 AND 0_employee.`mb_flag`='S'";

    $result = db_query($sql, "Could not get account.");
    $num = db_num_rows($result);
    if ($num < 1)
    {
        display_warning("There Are No Sale Items For This Customer.");
    }
    else
    {
        return $result;
    }
}

/**
 *
 */
function handle_new_item()
{

    $acc_detail = get_last_sales_items($_POST['division'],$_POST['month'],$_POST['mod_payment']);
    $acc_detail1 = get_last_sales_items_($_POST['division'],$_POST['month'],$_POST['mod_payment']);
    while($myrow = db_fetch($acc_detail))
    {

		$fiscal_1=$myrow['f_year'];
		$emp_type=$myrow['mb_flag'];
		if($fiscal_1!=0)
		$check=check_duplication_entry($_POST['division'],$_POST['month'],$_POST['mod_payment'],$fiscal_1);
		//$partial_amount=check_amount_duplication_entry($_POST['division'],$_POST['month'],$_POST['mod_payment'],$fiscal_1);
		//$amo1 = $myrow['net_salary']-$partial_amount;
	//	$check_amount=$partial_amount+$amo1;


    }
    while($myrow1 = db_fetch($acc_detail1))
    {

        $fiscal_2=$myrow1['f_year'];

		$emp_type1=$myrow1['mb_flag'];
		if($fiscal_2!=0) {
			$check1 = check_duplication_entry($_POST['division'],$_POST['month'], $_POST['mod_payment'], $fiscal_2);
			$partial_amount1 = check_amount_duplication_entry($_POST['division'],$_POST['month'], $_POST['mod_payment'], $fiscal_2);
			$amo2=$myrow1['net_salary']-$partial_amount1;
			$check_amount1 = $partial_amount1 + $amo2;
		}
    }


//		$amount = ($_SESSION['pay_items']->trans_type == ST_BANKPAYMENT ? 1 : -1) * $amo1;
//		$payroll_liabilty_account = get_sys_pay_pref('payroll_liabilty');
//		if ($_SESSION['pay_items']->Stop == 0) {
//			if ($amount != 0)
//				$_SESSION['pay_items']->add_gl_item($payroll_liabilty_account, $_POST['dimension_id'],
//					$_POST['dimension2_id'], $_POST['dimension3_id'], $amount, $_POST['LineMemo'], null,
//					$fiscal, $_POST['cheque_date_line'], $myrow['emp_id'], $_POST['PersonDetailID'], $_POST['month'], $_POST['mod_payment'], $fiscal_1);
//			$_SESSION['pay_items']->Stop = 1;
//		}
		$amount1 = ($_SESSION['pay_items']->trans_type == ST_BANKPAYMENT ? 1 : -1) * $amo2;
		$payroll_liabilty_account1 = get_sys_pay_pref('s_payroll_liabilty');
		if ($_SESSION['pay_items']->Stop == 0) {
			if ($amount1 != 0)
				$_SESSION['pay_items']->add_gl_item($payroll_liabilty_account1, $_POST['dimension_id'],
					$_POST['dimension2_id'], $_POST['dimension3_id'], $amount1, $_POST['LineMemo'], null,
					$fiscal, $_POST['cheque_date_line'], $myrow['emp_id'], $_POST['PersonDetailID'], $_POST['month'], $_POST['mod_payment'], $fiscal_2);
			$_SESSION['pay_items']->Stop = 1;
		}
//	if (!check_item_data())
//		return;
//	$amount = ($_SESSION['pay_items']->trans_type==ST_BANKPAYMENT ? 1:-1) * input_num('amount');
//
//	$_SESSION['pay_items']->add_gl_item($_POST['code_id'], $_POST['dimension_id'],
//		$_POST['dimension2_id'], $amount, $_POST['LineMemo'],null,
//		$_POST['cheque_no_line'],$_POST['cheque_date_line'],$_POST['person_id'],$_POST['PersonDetailID']);
	line_start_focus();
}
//-----------------------------------------------------------------------------------------------
$id = find_submit('Delete');
if ($id != -1)
	handle_delete_item($id);

if (isset($_POST['AddItem']))
	handle_new_item();
elseif(isset($_POST['getitems']))
	handle_new_item();


if (isset($_POST['UpdateItem']))
	handle_update_item();

if (isset($_POST['CancelItemChanges']))
	line_start_focus();

if (isset($_POST['go']))
{
	display_quick_entries($_SESSION['pay_items'], $_POST['person_id'], input_num('totamount'),
		$_SESSION['pay_items']->trans_type==ST_BANKPAYMENT ? QE_PAYMENT : QE_DEPOSIT);
	$_POST['totamount'] = price_format(0); $Ajax->activate('totamount');
	line_start_focus();
}
//-----------------------------------------------------------------------------------------------

start_form();

display_bank_header_bulk($_SESSION['pay_items']);

start_table(TABLESTYLE2, "width=90%", 10);
start_row();
echo "<td>";
display_gl_items_bulk($_SESSION['pay_items']->trans_type==ST_BANKPAYMENT ?
	_("Payment Items"):_("Deposit Items"), $_SESSION['pay_items']);
gl_options_controls();
echo "</td>";
end_row();
end_table(1);

submit_center_first('Update', _("Update"), '', null);
submit_center_last('Process', $_SESSION['pay_items']->trans_type==ST_BANKPAYMENT ?
	_("Process Payment"):_("Process Deposit"), '', 'default');

end_form();

//------------------------------------------------------------------------------------------------

end_page();

?>
