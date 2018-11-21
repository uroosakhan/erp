<?php

$page_security = 'SA_SUPPLIERPAYMNT';
$path_to_root = "..";
include_once($path_to_root . "/includes/ui/allocation_cart.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

add_js_file('payalloc.js');

page(_($help_context = "Supplier Payment Entry"), false, false, "", $js);

if (isset($_GET['supplier_id']))
{
	$_POST['supplier_id'] = $_GET['supplier_id'];
}

//----------------------------------------------------------------------------------------

check_db_has_suppliers(_("There are no suppliers defined in the system."));

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//----------------------------------------------------------------------------------------

if (!isset($_POST['supplier_id']))
	$_POST['supplier_id'] = get_global_supplier(false);

if (!isset($_POST['DatePaid']))
{
	$_POST['DatePaid'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['DatePaid']))
		$_POST['DatePaid'] = end_fiscalyear();
}

if (isset($_POST['_DatePaid_changed'])) {
  $Ajax->activate('_ex_rate');
}

if (list_updated('supplier_id')) {
	$_POST['amount'] = price_format(0);
	$_SESSION['alloc']->person_id = get_post('supplier_id');
	$Ajax->activate('amount');
} elseif (list_updated('bank_account'))
	$Ajax->activate('alloc_tbl');

//----------------------------------------------------------------------------------------

if (!isset($_POST['bank_account'])) { // first page call
	$_SESSION['alloc'] = new allocation(ST_SUPPAYMENT, 0, get_post('supplier_id'));

	if (isset($_GET['PInvoice'])) {
        //  get date and supplier
        $inv = get_supp_trans($_GET['PInvoice'], $_GET['trans_type']);
        $dflt_act = get_default_bank_account($inv['curr_code']);
        $_POST['bank_account'] = $dflt_act['id'];
        if ($inv) {
            $_SESSION['alloc']->person_id = $_POST['supplier_id'] = $inv['supplier_id'];
            $_SESSION['alloc']->read();
            $_POST['DatePaid'] = sql2date($inv['tran_date']);
            $_POST['memo_'] = $inv['supp_reference'];
            foreach ($_SESSION['alloc']->allocs as $line => $trans) {
                if ($trans->type == $_GET['trans_type'] && $trans->type_no == $_GET['PInvoice']) {
                    $un_allocated = abs($trans->amount) - $trans->amount_allocated;
                    $_SESSION['alloc']->amount = $_SESSION['alloc']->allocs[$line]->current_allocated = $un_allocated;
                    $_POST['amount'] = $_POST['amount' . $line] = price_format($un_allocated);
                    $_POST['fixed_amount'] = $_POST['amount' . $line] = price_format($un_allocated);

                    break;
                }
            }
            unset($inv);
        } else
            display_error(_("Invalid purchase invoice number."));
    }
}
//Ryan :06-05-17
if(isset($_POST['get_final_amount'])) {

	//	display_error($_POST['amount_new']);
	$Ajax->activate('amount');
	$_POST['amount']=$_POST['amount_new'];
}
//---------end------------	
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
	$payment_id = $_GET['AddedID'];

   	display_notification_centered( _("Payment has been sucessfully entered"));

	submenu_print(_("&Print This Remittance"), ST_SUPPAYMENT, $payment_id."-".ST_SUPPAYMENT, 'prtopt');
	submenu_print(_("&Email This Remittance"), ST_SUPPAYMENT, $payment_id."-".ST_SUPPAYMENT, null, 1);

	submenu_view(_("View this Payment"), ST_SUPPAYMENT, $payment_id);
    display_note(get_gl_view_str(ST_SUPPAYMENT, $payment_id, _("View the GL &Journal Entries for this Payment")), 0, 1);

	submenu_option(_("Enter another supplier &payment"), "/purchasing/supplier_payment.php?supplier_id=".$_POST['supplier_id']);
	submenu_option(_("Enter Other &Payment"), "/gl/gl_bank.php?NewPayment=Yes");
	submenu_option(_("Enter &Customer Payment"), "/sales/customer_payments.php");
	submenu_option(_("Enter Other &Deposit"), "/gl/gl_bank.php?NewDeposit=Yes");
	submenu_option(_("Bank Account &Transfer"), "/gl/bank_transfer.php");

	display_footer_exit();
}

//----------------------------------------------------------------------------------------

function check_inputs()
{
	global $Refs;

	if (!get_post('supplier_id')) 
	{
		display_error(_("There is no supplier selected."));
		set_focus('supplier_id');
		return false;
	} 
	
	if (@$_POST['amount'] == "") 
	{
		$_POST['amount'] = price_format(0);
	}

	if (!check_num('amount', 0))
	{
		display_error(_("The entered amount is invalid or less than zero."));
		set_focus('amount');
		return false;
	}

	if (isset($_POST['charge']) && !check_num('charge', 0)) {
		display_error(_("The entered amount is invalid or less than zero."));
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

	if (@$_POST['discount'] == "") 
	{
		$_POST['discount'] = 0;
	}
//////////ryan please resolve properly thanks.
//     if ($_POST['wht_service_amt'] == "")
// 	{
// 		display_error(_("The entered discount is invalid or less than zero."));
// 		set_focus('amount');
// 		return false;
// 	}

	//if (input_num('amount') - input_num('discount') <= 0) 
	if (input_num('amount') <= 0) 
	{
		display_error(_("The total of the amount and the discount is zero or negative. Please enter positive values."));
		set_focus('amount');
		return false;
	}

	if (isset($_POST['bank_amount']) && input_num('bank_amount')<=0)
	{
		display_error(_("The entered bank amount is zero or negative."));
		set_focus('bank_amount');
		return false;
	}
    if (get_account_type_id($_POST['bank_account'])==4)
    {
    if ($_POST['cheque']=="")
    {
        display_error(sprintf(_("You must enter Cheque #")));
        set_focus('code_id');
        return false;
    }
    }

   	if (!is_date($_POST['DatePaid']))
   	{
		display_error(_("The entered date is invalid."));
		set_focus('DatePaid');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_POST['DatePaid'])) 
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('DatePaid');
		return false;
	}

// 	$limit = get_bank_account_limit($_POST['bank_account'], $_POST['DatePaid']);

// 	if (($limit !== null) && (floatcmp($limit, input_num('amount')) < 0))
// 	{
// 		display_error(sprintf(_("The total bank amount exceeds allowed limit (%s)."), price_format($limit)));
// 		set_focus('amount');
// 		return false;
// 	}

	if (!check_reference($_POST['ref'], ST_SUPPAYMENT))
	{
		set_focus('ref');
		return false;
	}

	if (!db_has_currency_rates(get_supplier_currency($_POST['supplier_id']), $_POST['DatePaid'], true))
		return false;



		$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['DatePaid'], 'd');

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

	$diff_futuredays   =  date_diff2($_POST['DatePaid'],date('d-m-Y'), 'd');
			
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

	if($row2 > $now )
		{
			display_error("You are not allowed to enter data $allowed_time pm");
			return false ;
		}

	}

	$_SESSION['alloc']->amount = -input_num('amount');

	if (isset($_POST["TotalNumberOfAllocs"]))
		return check_allocations();
	else
		return true;
}

//----------------------------------------------------------------------------------------

function handle_add_payment()
{

	
	$payment_id = write_supp_payment(0, $_POST['supplier_id'], $_POST['bank_account'],
		$_POST['DatePaid'], $_POST['ref'], input_num('amount'),	input_num('discount'), $_POST['memo_'], 
		input_num('charge'), input_num('bank_amount', input_num('amount')),input_num('gst_wh'),$_POST['wht_supply_tax'], $_POST['wht_service_tax'], $_POST['wht_fbr_tax'],
		$_POST['wht_srb_tax'], input_num('wht_supply_amt'), input_num('wht_service_amt'),
		input_num('wht_fbr_amt'), input_num('wht_srb_amt'), $_POST['write_back'], $_POST['cheque'], $_POST['cheque_date']
		, $_POST['text_1'], $_POST['text_2'], $_POST['text_3'], $_POST['dimension_id'], $_POST['dimension2_id']);
	new_doc_date($_POST['DatePaid']);

	$_SESSION['alloc']->trans_no = $payment_id;
	$_SESSION['alloc']->date_ = $_POST['DatePaid'];
	$_SESSION['alloc']->write();

   	unset($_POST['bank_account']);
   	unset($_POST['DatePaid']);
   	unset($_POST['currency']);
   	unset($_POST['memo_']);
   	unset($_POST['amount']);
   	unset($_POST['discount']);
   	unset($_POST['ProcessSuppPayment']);

	meta_forward($_SERVER['PHP_SELF'], "AddedID=$payment_id&supplier_id=".$_POST['supplier_id']);
}

//----------------------------------------------------------------------------------------

if (isset($_POST['ProcessSuppPayment']))
{
	 /*First off  check for valid inputs */
    if (check_inputs() == true) 
    {
    	handle_add_payment();
    	end_page();
     	exit;
    }
}

//----------------------------------------------------------------------------------------

start_form();

	start_outer_table(TABLESTYLE2, "width='60%'", 5);

	table_section(1);

    supplier_list_row(_("Payment To:"), 'supplier_id', null, false, true);

	if (list_updated('supplier_id') || list_updated('bank_account')) {
	  $_SESSION['alloc']->read();
	  $_POST['memo_'] = $_POST['amount'] = '';
	  $Ajax->activate('alloc_tbl');
	}

	set_global_supplier($_POST['supplier_id']);

	if (!list_updated('bank_account') && !get_post('__ex_rate_changed'))
		$_POST['bank_account'] = get_default_supplier_bank_account($_POST['supplier_id']);
	else
		$_POST['amount'] = price_format(0);

   bank_accounts_list_all_row(_("From Bank Account:"), 'bank_account', null, true);

	bank_balance_row($_POST['bank_account']);
	
	$GetCreditBalance = get_credit_balance_supplier($_POST['supplier_id']);
    supplier_credit_row($_POST['supplier_id'], $GetCreditBalance);

    if (get_company_pref('use_dimension'))
        dimensions_list_row(_('Dimension').':', 'dimension_id', null, true, _('Default'), false, 1);
    if (get_company_pref('use_dimension') == 2)
        dimensions_list_row(_('Dimension 2').':', 'dimension2_id', null, true, _('Default'), false, 2);

	table_section(2);

    date_row(_("Date Paid") . ":", 'DatePaid', '', true, 0, 0, 0, null, true);

    // ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_SUPPAYMENT, null, 
    // 	array('supplier'=>get_post('supplier_id'), 'date'=>get_post('DatePaid'))), false, ST_SUPPAYMENT);
ref_row(_("Reference:"), 'ref', '', null, false, ST_SUPPAYMENT, array('date'=> @$_POST['DatePaid']));
    text_row(_("Cheque"), 'cheque', null,20, 20);

	date_row(_("Cheque Date"), 'cheque_date', '',true, 0, 0, 0);
	table_section(3);

	$comp_currency = get_company_currency();
	$supplier_currency = $_SESSION['alloc']->set_person($_POST['supplier_id'], PT_SUPPLIER);
	if (!$supplier_currency)
			$supplier_currency = $comp_currency;
	$_SESSION['alloc']->currency = $bank_currency = get_bank_account_currency($_POST['bank_account']);

	if ($bank_currency != $supplier_currency) 
	{
		amount_row(_("Bank Amount:"), 'bank_amount', null, '', $bank_currency);
	}

	amount_row(_("Bank Charge:"), 'charge', null, '', $bank_currency);
text_row("Text 1",'text_1');
text_row("Text 2",'text_2');
text_row("Text 3",'text_3');

	end_outer_table(1);

	div_start('alloc_tbl');
	show_allocatable(false);
	div_end();

	start_table(TABLESTYLE, "width='60%'");
wth_tax_type_list_cells(_("Income Tax WH on Supplies (auto calc.)"), 'wht_supply_tax', null,
	_("Select Tax Type"), true,null,null,1); //asad
hidden('wht_supply_percent', $_POST['wht_supply_percent']);
amount_cells_ex(_("WHT on Supplies Amount :"), 'wht_supply_amt',10,50);
end_row();
//Ryan :06-05-17
start_row();
wth_tax_type_list_cells(_("Income Tax WH on Services (auto calc.)"), 'wht_service_tax', null,	_("Select Tax Type"), true,null,null,2); //asad
hidden('wht_service_percent', $_POST['wht_service_percent']);
amount_cells_ex(_("WHT on Services Amount :"), 'wht_service_amt',10,50);
end_row();
//Ryan :06-05-17
start_row();
wth_tax_type_list_cells(_("ST WHT FBR (manual calc.)"), 'wht_fbr_tax', null,	_("Select Tax Type"), true,null,null,3); //asad
amount_cells_ex(_("ST WH FBR Amount :"), 'wht_fbr_amt',10,50);
end_row();

//Ryan :06-05-17
start_row();
wth_tax_type_list_cells(_("St WHT SRB/PRA (manual calc.)"), 'wht_srb_tax', null,
	_("Select Tax Type"), true,null,null,4);
amount_cells_ex(_("ST WH SRB/PRA Amount "), 'wht_srb_amt',10,50);
end_row();

//Ryan :06-05-17
amount_cells_ex(_("Write Off : "), 'discount', 25,50);
$display_discount_percent = percent_format($_POST['pymt_discount']*100) . "%";
hidden('gst_wh', $_POST['gst_wh']);

echo"<tr>";
amount_cells_ex(_("Total Amount:"), 'amount', 25,50);
echo"</tr>";
text_cells_readonly("", 'fixed_amount');
hidden('amount_new',$_POST['amount_new']);
//amount_row(_("Total Calculated value:"), 'amount_new', null, " class='tableheader2' ");

textarea_row(_("Memo:"), 'memo_', null, 80, 5);
//submit_cells('get_final_amount',"Calculate",null,null,true);

end_table(1);

//	amount_row(_("Amount of Discount:"), 'discount', null, '', $supplier_currency);
//	amount_row(_("Amount of Payment:"), 'amount', null, '', $supplier_currency);
//	textarea_row(_("Memo:"), 'memo_', null, 22, 4);
	end_table(1);

	submit_center('ProcessSuppPayment',_("Enter Payment"), true, '', 'default');

end_form();

end_page();
