<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/admin/db/company_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Gratuity"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Leave"), $js);

include($path_to_root . "/payroll/includes/db/gratuity_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");
include($path_to_root . "/includes/ui.inc");


simple_page_mode(true);

$fiscal_year = get_company_pref('f_year');
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['emp_id']) == 0)
	{
		$input_error = 1;
		display_error(_("The Employee Name cannot be empty."));
		set_focus('emp_id');
	}
	if (!is_numeric($_POST['amount']))
	{
		$input_error = 1;
		display_error( _("Value must be numeric."));
		set_focus('amount');
	}
//	if (!is_numeric($_POST['payment_shedule']))
//	{
//		$input_error = 1;
//		display_error( _("Value must be numeric."));
//		set_focus('payment_shedule');
//	}
//	if(strlen($_POST['payment_shedule']) >= 120)
//	{
//		$input_error = 1;
//		display_error( _("Plz select less than 120 valu"));
//		set_focus('payment_shedule');
//	}


	/*	if (!is_date($_POST['to_date']))
	{
		display_error( _("Invalid END date "));
		set_focus('to_date');
		return false;
	}
	
	if (strlen($_POST['leave_type']) == 0) 
	{
		$input_error = 1;
		display_error(_("The Leave cannot be empty."));
		set_focus('emp_id');
	}
	$date1=$_POST['from_date'];
	$date2=$_POST['to_date'];
	if ($_POST['to_date']<$_POST['from_date']) 
	{
		$input_error = 1;
		display_error(_("From date should be less than To date ."));
		set_focus('to_date');
		
	}
	$get_duplication=get_emp_info_no($_POST['emp_id'],$_POST['from_date']);
   if($get_duplication>0)
    {
	$input_error = 1;
	display_error(_("Already Exist"));
    }*/
	if ($input_error != 1)
	{
		//$login_date = strtotime($_POST['from_date']); // change x with your login date var
		// $current_date = strtotime($_POST['to_date']); // change y with your current date var
		// $datediff = $current_date - $login_date;
		// $days = floor($datediff/(60*60*24));
		//$days1= $days+1;

		if ($selected_id != -1)
		{
			//update_advance($selected_id, $_POST['date'], $_POST['emp_id'], $_POST['amount'], $_POST['type'], $_POST['remarks'],$_POST['approve'],$_POST['payroll_id'],$_POST['payment_shedule'],$_POST['payment']);
			//$note = _('Selected Advance  has been updated');
		}
		else
		{
			$tax_rate = get_tax_amount_empl_vise($_POST['taxable_amount']);
			$yearly_salary_tax = $_POST['taxable_amount'] * $tax_rate / 100;
			$income_tax = round2($yearly_salary_tax);

			add_gratuity( $_POST['encashment_id'], $_POST['emp_id'], $_POST['approve'],
				$_POST['days'],$_POST['f_year'],
				$_POST['payment_date'],$_POST['amount'],$_POST['taxable_amount'],
				$income_tax,$_POST['mode_of_payment'],$_POST['remarks'],
				$_POST['gl_voucher'],$_POST['check_no'],$_POST['check_date'],
				$_POST['date'],$_POST['tax_installments'],$_POST['gl_it'],$_POST['account_info']);
			
			$note = _('Gratuity  has been added');
			$approval11=$_POST['approve'];



			if($approval11==1)
			{
				global $Refs;
				$amount=($_POST['amount'] +$income_tax);
				$shedule1=$_POST['payment_shedule'];
				$payment_permonth1=($amount/$shedule1);
				$shedule_payment_month=round2($payment_permonth1);

				$employee_id=$_POST['emp_id'];
				$employee_name=get_employee_name($employee_id);
				$id = get_next_trans_no(ST_JOURNAL);
				$ref = $Refs->get_next(ST_JOURNAL);
				$memo = "Advance paid to $employee_name amounting to $amount, payment schedule $shedule_payment_month per month";
				//$advance_receivable_account=get_sys_pay_pref('advance_receivable');
				$acc = get_employee_data3($_POST['emp_id']);
				$payment_account=get_sys_pay_pref('payment');

				$bank = get_bank_account($_POST['account_info']);
				$f_year = get_current_fiscalyear();

				$advance_amnt = get_advance_against_gratuity($employee_id,$f_year['id']);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $acc['salary_account'],'', '', $memo,
					$amount,0,0,0,0,0,$bank,$_POST['check_no'],
					$_POST['check_date'],$fiscal_year,$_POST['gl_voucher'],$_POST['gl_it']);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payment_account,'','', $memo,
					- ($amount - $income_tax-$advance_amnt),0,0,0,0,0,$bank,$_POST['check_no'],
					$_POST['check_date'],$fiscal_year,$_POST['gl_voucher'],$_POST['gl_it']);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $acc['tax_liability'],'','', $memo,
					-$income_tax,0,0,0,0,0,$bank,$_POST['check_no'],
					$_POST['check_date'],$fiscal_year,$_POST['gl_voucher'],$_POST['gl_it']);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $acc['advance_receivable'],'','', $memo,
					-$advance_amnt,0,0,0,0,0,$bank,$_POST['check_no'],
					$_POST['check_date'],$fiscal_year,$_POST['gl_voucher'],$_POST['gl_it']);

				if($advance_amnt >0)
				{
					$gratuity_id = get_gratuity_last_id();
					add_advance_deduction($employee_id,$advance_amnt,$_POST['date'],$bank,$f_year['id'],
											$gratuity_id);
				}


			//	add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
							add_audit_trail_payroll(ST_JOURNAL, $id, $_POST['date'],"Inserted");

				add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
				$Refs->save(ST_JOURNAL, $id, $ref);
			}
			display_notification(_("Gratuity record has been updated. JV Reference $ref."));

		}
		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this Gratuity because customers have been created using this group."));
	}
	if ($cancel_delete == 0)
	{
		delete_gratuity($selected_id);
		display_notification(_('Selected Gratuity  has been deleted'));
	} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}



function handle_delete($selected_id)
{
	delete_gratuity($selected_id);
	display_notification(_("Gratuity record has been deleted."));
}

if (isset($_POST['delete']))
{
	handle_delete($_POST['id']);
}
function handle_update($selected_id)
{$fiscal_year = get_company_pref('f_year');
	update_grtuity($selected_id,$_POST['encashment_id'],
		$_POST['emp_id'],$_POST['approved'],
		$_POST['f_year'],$_POST['payment_date'],$_POST['amount'],
		$_POST['taxable_amount'],
		$_POST['income_tax'],$_POST['mode_of_payment'],$_POST['remarks']
		,$_POST['gl_voucher'],$_POST['check_no'],$_POST['check_date'],$_POST['date']
		,$_POST['tax_installments'],$_POST['gl_it'],$_POST['account_info']);
	}

if (isset($_POST['update']))
{
	handle_update($_POST['id']);
}
///=======================================================

//$result = get_emp_leave_types(check_value('show_inactive'));
$id=$_GET['id'];
//echo $id;
start_form();

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1) 
//{
//if ($Mode == 'Edit') {
//editing an existing group
If($_GET['update']=='YES' && $_GET['id'] !='')
{
	$myrow = get_gratuity($_GET['id']);
	$_POST['encashment_id']  = $myrow["encashment_id"];
	$_POST['date']  = sql2date($myrow["date"]);
	$_POST['employee_id']  = $myrow["emp_name"];

	$_POST['days']  = $myrow["days"];
	$_POST['emp_id']  = $myrow["emp_name"];
	$_POST['approved']  = $myrow["approved"];
	$_POST['amount']  = $myrow["amount"];
	$_POST['type']  = $myrow["type"];
	$_POST['remarks']  = $myrow["remarks"];
	$_POST['approve']  = $myrow["approve"];
	$_POST['payroll_id']  = $myrow["payroll_id"];
	$_POST['payment_shedule']  = $myrow["payment_shedule"];
	$_POST['f_year']  = $myrow["f_year"];
	$_POST['payment_date']  = sql2date($myrow["payment_date"]);
	$_POST['taxable_amount']  = $myrow["taxable_amount"];
	$_POST['income_tax']  = $myrow["income_tax"];
	$_POST['mode_of_payment']  = $myrow["mode_of_payment"];
	$_POST['gl_voucher']  = $myrow["gl_voucher"];
	$_POST['check_no']  = $myrow["check_no"];
	$_POST['check_date']  = sql2date($myrow["check_date"]);
	$_POST['tax_installments']  = $myrow["tax_installments"];
	$_POST['gl_it']  = $myrow["gl_it"];
	$_POST['account_info']  = $myrow["account_info"];
}




//$_POST['payment']  = $myrow["payment"];

//}
hidden("payment", $payment_permonth);
hidden("selected_id", $selected_id);
hidden("id", $id);


//label_row(_("ID"), $myrow["id"]);
//} 
/*
	$d1 = new DateTime($_POST['from_date']);
    $d2 = new DateTime($_POST['to_date']);
    $interval = $d1->diff($d2);

echo $interval->format('%R%a days');*/
//
$approval=$_POST['approve'];
$amount=$_POST['amount'];
$shedule=$_POST['payment_shedule'];
$payment_permonth=($amount/$shedule);
//echo $approval;
start_outer_table(TABLESTYLE2);

table_section(1);

//text_cells("Encashment id:",'encashment_id');//client req
employee_list_cells(_("Select a employee: "), 'employee_id',$_POST['employee_id'],
	_('New employee'), true, check_value('show_inactive'),false,1);


if(list_updated('employee_id') || $_GET['update']=='YES' )
{

	$total_allowed_days = get_total_no_of_allowed_leaves2();
	$no_of_levaes = get_no_of_leave2($_POST['employee_id']);
	$emp_data = get_employee_data3($_POST['employee_id']);

	$days_remaining = $total_allowed_days['leave_days'] - $no_of_levaes;
	$working_days = get_sys_pay_pref('total_working_days');
	$per_day_salary =  $emp_data['basic_salary'] / $working_days ;
	$DOJ = sql2date($emp_data['j_date']);
	$current_date = Today();
	$no_of_days = date_diff2($current_date, $DOJ, "d");
	
	$final = $no_of_days / 365 * $emp_data['basic_salary']  ;
	$f_year = get_current_fiscalyear();
	$advance_against_gatuity = get_advance_against_gratuity($_POST['employee_id'],$f_year['id']);
	$after_gratuity = $final - $advance_against_gatuity;

	/*label_row(_("Days Remain"),$days_remaining);
//	label_row(_("Leave days"), $no_of_levaes);
	label_row(_("Basic Salary"),number_format2($per_day_salary));
	label_row(_("Tax"),$emp_data['tax_deduction']);*/
//	$total_encashment = $days_remaining * $per_day_salary;
	if($emp_data['tax_deduction'] ==1)
	{


//		$yearly_salary =  $emp_data['basic_salary'] * 12;
		//$tax_rate = get_tax_amount_empl_vise($after_gratuity);

		$yearly_salary_tax = $after_gratuity *$tax_rate/100 ;
		$taxable_amount = $after_gratuity;
		$income_tax = $yearly_salary_tax;
	}
	label_row(_("advance_against_gatuity"),number_format2($advance_against_gatuity));
	label_row(_("no_of_days"),number_format2($no_of_days));
/*	label_row(_("After Gratuity"),number_format2($after_gratuity));
	label_row(_("Tax Salary"), $tax_rate);
	label_row(_("Yearly Tax salary"), $yearly_salary_tax);
	label_row(_("Cash Total "), number_format2($total_encashment));*/

	$total_encashment = $after_gratuity;
	$taxable_amount = round2($total_encashment);
}


$employee_code=$_POST['employee_id'];
approve_list_row("Approved by:",'approve');

date_row(_("Date"),'date', null,null, 0, 0, 0, null, true);

text_row("Tax Installments:",'tax_installments',$_POST['tax_installments']);
text_row("Amount", 'amount', round2($total_encashment), 20, 10);

text_row("Taxable income", 'taxable_amount',$taxable_amount, 20, 10);
if($_POST['employee_id'] !='')
submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true);

if (isset($_POST['update'])) {

	$emp_salary = get_employee_basic_salary($_POST['employee_id']);
	$annual_salary = $emp_salary * 12;

	$tax_rate = get_tax_amount_empl_vise($annual_salary);
	$min_amount=get_tax_min_amount_empl_vise($annual_salary);
	$fix_amount=get_tax_fix_amount_empl_vise($annual_salary);
	$monthly_tax = ((($emp_salary*12+-$min_amount)*$tax_rate/100)+$fix_amount)/12;
	$anual_gratuaity=(($emp_salary * 12)+$_POST['taxable_amount']);
	$tax_rate1 = get_tax_amount_empl_vise($anual_gratuaity);
	$min_amount=get_tax_min_amount_empl_vise($anual_gratuaity);
	$fix_amount=get_tax_fix_amount_empl_vise($anual_gratuaity);
	$monthly_tax_gra = ((((($emp_salary*12)+$_POST['taxable_amount'])-$min_amount)*$tax_rate1/100)+$fix_amount)/12;
	////
	$annal_tax=$monthly_tax*12;
	$annal_tax_gra=$monthly_tax_gra*12;
	$total_tax=$annal_tax_gra-$annal_tax;
	/////
	//$tax_rate = get_tax_amount_empl_vise($_POST['taxable_amount']);

		//$income_tax = $_POST['taxable_amount'] * $tax_rate / 100;
	$Ajax->activate('income_tax');
}

text_row("Income Tax", 'income_tax',number_format2($total_tax), 10, 10);
date_row(_("Payment Date"),'payment_date', null,null, 0, 0, 0, null, true);
//text_row(_("Deduction months:"), 'payment_shedule', null, 5, 5);
hidden("emp_id", $employee_code);
//hidden("no_of_leave",$interval->format('%R%a days'));
//hidden("no_of_leave",floor($datediff/(60*60*24)));
table_section(2);
//
//date_row(_("Date"),'date', null,null, 0, 0, 0, null, true);
?><h2>Accounts use only</h2><?php
//text_row("Account Info:",'account_info', $_POST['account_info'], 40, 20);
bank_accounts_list_row( "Bank", 'account_info', null, false);
fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
hidden("payment", $payment_permonth);
//text_row(_("Deduction months:"), 'payment_shedule', null, 5, 5);
payment_terms_list_row("Mode of payment",'mode_of_payment');
textarea_row(_("Remarks:"), 'remarks', null, 35, 2);
$ref = $Refs->get_next(ST_JOURNAL);
text_row("GL Voucher", 'gl_voucher', $ref , 10, 10);
text_row("Cheque No", 'check_no', null, 10, 10);
text_row("GL IT", 'gl_it', $_post['gl_it'], 10, 10);
date_row(_("Cheque Date"),'check_date', null,null, 0, 0, 0, null, true);
end_table(2);
if($id==0)
{
	submit_add_or_update_center(-1, '', 'both');
}
else
{
	start_table(TABLESTYLE2);
	if($approval==0)
	{
		approve_list_row(_("Approval:"),'approve', $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false);
		gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', get_sys_pay_pref('advance_receivable'));
		gl_all_accounts_list_row(_("Payment Account:"), 'payment_account', get_sys_pay_pref('payment'));
		//echo $payment_shedule;
		label_cell("Per month deduction");
		label_cell( round2($payment_permonth));
		submit_center_first('update', _("Update"), '', '', true);
		submit_center_last('delete', _("Delete"), '', '', true);
	}
	else
	{
		submit_center_last('delete', _("Delete"), '', '', true);
	}
	// text_row_ex(_("Payment(Per Month):"), 'payment', 10, null, null, $_POST['emp_id'] );
}

table_section(2);
end_outer_table(1);
end_form();

end_page();
?>
