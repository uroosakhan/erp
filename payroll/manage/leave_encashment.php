<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");



$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Leave encashment"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Leave"), $js);
include($path_to_root . "/payroll/includes/db/attendance.inc");
include($path_to_root . "/payroll/includes/db/advance_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

function get_total_no_of_allowed_leaves()
{
	$sql="SELECT * FROM ".TB_PREF."leave_type where id='2' ";
	$db = db_query($sql,'error');
	return db_fetch($db);
//	return $ft[0];

}
function get_basic_salary($employee_id)
{
	$sql="SELECT basic_salary FROM ".TB_PREF."employee where employee_id='$employee_id' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];

}
function get_no_of_leave($emp_id)
{
	$sql="SELECT COUNT(no_of_leave) FROM ".TB_PREF."leave where emp_id='$emp_id' AND leave_type='2' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_employee_data2($emp_id)
{
	$sql="select * FROM ".TB_PREF."employee where 	employee_id=".$emp_id." ";
	$db = db_query($sql,'Cant get Employee data');
	return db_fetch($db);
}


simple_page_mode(true);

function get_advance_against_leave_encashment($employee_id,$f_year)
{
	$sql="SELECT amount FROM ".TB_PREF."advance where emp_id=".db_escape($employee_id)." 
			AND f_year=".db_escape($f_year)." AND advance_on_base_of='3' AND payroll_id='0'";
	$db = db_query($sql,'Cant get Employee data');
	$ft=  db_fetch($db);
	return $ft[0];
}
function get_leave_encashment_last_id()
{
	$sql="SELECT MAX(id) FROM ".TB_PREF."leave_encashment ";
	$db = db_query($sql,'Cant get Employee data');
	$ft=  db_fetch($db);
	return $ft[0];

}
function add_advance_deduction_leave($employee_id,$advance_amnt,$date,$bank,$f_year,$leave_encashment_id)
{
	$sql="INSERT INTO ".TB_PREF."advance (date,emp_id,amount,approve,f_year,bank_account,leave_encashment_id) VALUE 
			(".db_escape(sql2date($date)).",".db_escape($employee_id).",".db_escape($advance_amnt).",'1',".db_escape($f_year)."
			,".db_escape($bank).",".db_escape($leave_encashment_id).")";
	db_query($sql,'Cant not insert');
}
function add_emp_info_for_earn($emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
{
	$sql = "INSERT INTO ".TB_PREF."leave (emp_id,from_date,to_date,leave_type,no_of_leave,reason,approve,f_year,availed) 
    VALUES ( ".db_escape($emp_id).", ".db_escape(sql2date($from_date)).", ".db_escape(sql2date($to_date)).",
     ".db_escape($leave_type).", ".db_escape($no_of_leave).", ".db_escape($reason).",
     ".db_escape(1) . ",".db_escape($f_year) . ",".db_escape(E) . ")";
	db_query($sql,"The department could not be added");
}
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
			add_leave_encashment( $_POST['encashment_id'], $_POST['emp_id'], $_POST['approve'],
				$_POST['days2'],$fiscal_year,
				$_POST['payment_date'],$_POST['amount'],$_POST['taxable_amount'],
				$_POST['income_tax'],$_POST['mode_of_payment'],$_POST['remarks'],
				$_POST['gl_voucher'],$_POST['check_no'],$_POST['check_date'],$_POST['account_info']);
			add_emp_info_for_earn($_POST['emp_id'],$_POST['payment_date'],$_POST['payment_date'],2,$_POST['days2'],$reason,$approve,$fiscal_year);
			$note = _('Leave encashment  has been added');
			$approval11=$_POST['approve'];
			if($approval11==1)
			{
				global $Refs;
				$amount=($_POST['amount']+$_POST['income_tax']);
				$shedule1=$_POST['payment_shedule'];
				$payment_permonth1=($amount/$shedule1);
				$shedule_payment_month=round2($payment_permonth1);

				$employee_id=$_POST['emp_id'];
				$employee_name=get_employee_name($employee_id);
				$id = get_next_trans_no(ST_JOURNAL);
				$ref = $Refs->get_next(ST_JOURNAL);
				$memo = "Advance paid to $employee_name amounting to $amount, payment schedule $shedule_payment_month per month";

//				$advance_receivable_account=get_sys_pay_pref('advance_receivable');
				$acc = get_employee_data2($_POST['emp_id']);
				$payment_account=get_sys_pay_pref('payment');

				$account_info = get_bank_account($_POST['account_info']);
				$bank = get_bank_account($_POST['account_info']);
				$advance_amnt = get_advance_against_leave_encashment($employee_id,$fiscal_year);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $acc['salary_account'],
					'', '', $memo,
					$amount,0,0,0,0,0,$bank,$_POST['check_no'],
					$_POST['check_date'],$fiscal_year);


				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payment_account,'','', $memo,
					-($amount-$advance_amnt),0,0,0,0,0,$bank,$_POST['check_no'],$_POST['check_date'],$fiscal_year);

				add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $acc['advance_receivable'],'','', $memo,
					-$advance_amnt,0,0,0,0,0,$bank,$_POST['check_no'],$_POST['check_date'],$fiscal_year);

				if($advance_amnt >0)
				{
					$leave_encashment_id = get_leave_encashment_last_id();
					add_advance_deduction_leave($employee_id,$advance_amnt,$_POST['date'],$bank,$fiscal_year,
						$leave_encashment_id);
				}


			//	add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
								add_audit_trail_payroll(ST_JOURNAL, $id, $_POST['date'],"Inserted");

				add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
				$Refs->save(ST_JOURNAL, $id, $ref);
			}
			display_notification(_("Leave Encashment record has been updated. JV Reference $ref."));

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
		display_error(_("Cannot delete this Leave Encashment because customers have been created using this group."));
	}
	if ($cancel_delete == 0)
	{
		delete_leave_encashment($selected_id);
		display_notification(_('Selected Leave Encashment  has been deleted'));
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
	delete_advance_against_leave_encashment($selected_id);
	delete_leave_encashment($selected_id);
	display_notification(_("Leave Encashment record has been deleted."));
}


function  get_yearly_lates($employee_id,$begin1,$end1)
{

$begin = begin_fiscalyear();
	$begin1 = date2sql($begin); 
        $end=end_fiscalyear();
        $end1= date2sql($end);

	$sql="SELECT
			(SUM(CASE WHEN `check_in` >= '09:30:00' AND `check_in` < '11:00:00' THEN .25 ELSE 0 END)  +
			SUM(CASE WHEN `check_in` >= '11:00:00'  THEN .5 ELSE 0 END) ) as rest
			FROM 0_daily_attendance
			WHERE `att_date` between '$begin1' AND '$end1'
			AND employee=".db_escape($employee_id)."
			GROUP BY employee";
	$db = db_query($sql,'Can not get result');
	$ft = db_fetch($db);
	return $ft[0];
}

function  get_yearly_leaves($employee_id,$f_year,$leave_type)
{
	$sql="SELECT no_of_leave FROM 0_leave where `emp_id`=".db_escape($employee_id)." 
			AND f_year=".db_escape($f_year)." AND leave_type=".db_escape($leave_type)."";
	$db = db_query($sql,'Can not get result');
	$ft = db_fetch($db);
	return $ft[0];
}
function  get_yearly_earned_leaves($employee_id,$f_year,$leave_type)
{
	$sql="SELECT SUM(no_of_leave) FROM 0_leave where `emp_id`=".db_escape($employee_id)." 
			AND f_year=".db_escape($f_year)." AND leave_type=".db_escape($leave_type)." AND availed='E' ";
	$db = db_query($sql,'Can not get result');
	$ft = db_fetch($db);
	return $ft[0];
}

function  get_yearly_A_leaves($employee_id,$f_year,$leave_type)
{
	$sql="SELECT SUM(no_of_leave) FROM 0_leave where `emp_id`=".db_escape($employee_id)." 
			AND f_year=".db_escape($f_year)." AND leave_type=".db_escape($leave_type)." AND availed='A' ";
	$db = db_query($sql,'Can not get result');
	$ft = db_fetch($db);
	return $ft[0];
}
if (isset($_POST['delete']))
{
	handle_delete($_POST['id']);
}

function handle_update($selected_id)
{
	$fiscal_year = get_company_pref('f_year');




	update_leave_encashment($selected_id,$_POST['encashment_id'],
		$_POST['emp_id'],$_POST['approved'],$_POST['days'],
		$fiscal_year,$_POST['payment_date'],$_POST['amount'],
		$_POST['taxable_amount'],
		$_POST['income_tax'],$_POST['mode_of_payment'],$_POST['remarks']
		,$_POST['gl_voucher'],$_POST['check_no'],$_POST['check_date'],$_POST['date'],$_POST['account_info']);
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
If($_GET['id']!='' && $_GET['date'])
{
	$myrow = get_leave_encashment($_GET['id']);

	$_POST['encashment_id']  = $myrow["encashment_id"];
	$_POST['employee_id']  = $myrow["emp_name"];

	$_POST['date']  = sql2date($myrow["date"]);
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

//text_cells("Encashment id:",'encashment_id');//hide b/c employee code is main id
employee_list_cells(_("Select a employee: "), 'employee_id',$_POST['employee_id'],
	_('New employee'), true, check_value('show_inactive'),false,2);

if(list_updated('employee_id') ||  $_GET['id']!='')
{
	$total_allowed_days = get_total_no_of_allowed_leaves();
	$no_of_levaes = get_no_of_leave($_POST['employee_id']);
//	$basic_salary = get_basic_salary($_POST['employee_id']);
	$emp_data = get_employee_data2($_POST['employee_id']);

//	$days_remaining = $total_allowed_days['leave_days'] - $no_of_levaes;
	$per_day_salary =  $emp_data['basic_salary'] / 30 ;

	$DOJ = sql2date($emp_data['j_date']);
	$current_date = Today();
	$diff_no_of_days = date_diff2($current_date, $DOJ, "d");
	$f_year = get_current_fiscalyear();

//$total = $diff_no_of_days * 0.08219 * (30/365) ;
	$total = $diff_no_of_days  * (30/365) ;

	$yearly_lates = get_yearly_lates($_POST['employee_id']);

	$yearly_sick_leaves = get_yearly_leaves($_POST['employee_id'],$f_year['id'],3);
	$yearly_casual_leaves = get_yearly_leaves($_POST['employee_id'],$f_year['id'],5);
	$yearly_earned_leaves = get_yearly_earned_leaves($_POST['employee_id'],$f_year['id'],2);

	$result2 = $total - $yearly_lates -  $yearly_earned_leaves-get_yearly_A_leaves($_POST['employee_id'],$f_year['id'],2);
	$days_remaining = round2($result2);
	$advance_to_be_deducted = get_advance_against_leave_encashment($_POST['employee_id'],$f_year['id']);
	$total_encashment = $days_remaining * $per_day_salary - $advance_to_be_deducted;



/*	if($emp_data['tax_deduction'] ==1)
	{
		$yearly_salary =  $emp_data['basic_salary'] * 12;
		$tax_rate = get_tax_amount_empl_vise($yearly_salary);
		$yearly_salary_tax = $total_encashment *$tax_rate/100 ;
//		$_POST['taxable_amount'] = $yearly_salary_tax;
	}*/
	if($emp_data['tax_deduction'] ==1)
	{
//		$yearly_salary =  $emp_data['basic_salary'] * 12;
		$tax_rate = get_tax_amount_empl_vise($total_encashment);

		$yearly_salary_tax = $total_encashment *$tax_rate/100 ;
//		$_POST['taxable_amount'] = $total_encashment;
		$_POST['income_tax'] = $yearly_salary_tax;
	}

	label_row(_("No of days"),$diff_no_of_days);
	label_row(_("Total "),$total);
	//label_row(_("Yearly Sick leaves "),$yearly_sick_leaves);
	//label_row(_("Yearly Casual leaves "),$yearly_casual_leaves);
	label_row(_("Yearly Eraned leaves "),$yearly_earned_leaves);
	label_row(_("Yearly Availed leaves "),get_yearly_A_leaves($_POST['employee_id'],$f_year['id'],2));
	label_row(_("Yearly Late"),$yearly_lates);
	label_row(_("Toal Result"),$result2);
//	


	label_row(_("**********************"),'*************************');
/*	label_row(_("Days Remain"),$days_remaining);
//	label_row(_("Leave days"), $no_of_levaes);
	label_row(_("Basic Salary"),number_format2($per_day_salary));
	label_row(_("Cash Total "), number_format2($days_remaining * $per_day_salary));*/
}

$employee_code=$_POST['employee_id'];

//var_dump($_POST['employee_id']);
//employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true,false);
approve_list_row("Approved by:",'approve');

date_row(_("Date"),'date', null,null, 0, 0, 0, null, true);
text_row("Days For Enchashment:",'days2',round2($days_remaining));
if($_POST['employee_id'] !='')
	submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true);

if (isset($_POST['update'])) {
	$emp_data1 = get_employee_data2($_POST['employee_id']);
	$per =  $emp_data1['basic_salary'] / 30 ;
	$total_encashment = $per * $_POST['days2'] ;
	$annual_salary = $emp_data1['basic_salary'] * 12;
	$anual_gratuaity=(($emp_data1['basic_salary'] * 12)+$total_encashment);
	$tax_rate = get_tax_amount_empl_vise($annual_salary);
	$min_amount=get_tax_min_amount_empl_vise($annual_salary);
	$fix_amount=get_tax_fix_amount_empl_vise($annual_salary);
	$monthly_tax = ((($emp_data1['basic_salary']*12+-$min_amount)*$tax_rate/100)+$fix_amount)/12;

	$tax_rate = get_tax_amount_empl_vise($anual_gratuaity);
	$min_amount=get_tax_min_amount_empl_vise($anual_gratuaity);
	$fix_amount=get_tax_fix_amount_empl_vise($anual_gratuaity);
	$monthly_tax_gra = ((((($emp_data1['basic_salary']*12)+$total_encashment)-$min_amount)*$tax_rate/100)+$fix_amount)/12;
	////

	$annal_tax=$monthly_tax*12;
	$annal_tax_gra=$monthly_tax_gra*12;
	$total_tax=$annal_tax_gra-$annal_tax;


	$Ajax->activate('amount');
	$Ajax->activate('taxable_amount');


	$amnt = $per * $_POST['days2'] ;



	$yearly_salary_tax = $amnt * $tax_rate/100 ;
//		$_POST['taxable_amount'] = $total_encashment;
	$_POST['income_tax'] = $total_tax;
	$Ajax->activate('income_tax');
}

//text_row(_("Deduction months:"), 'payment_shedule', null, 5, 5);
//fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
date_row(_("Payment Date"),'payment_date', null,null, 0, 0, 0, null, true);
hidden("payment", $payment_permonth);
hidden("emp_id", $employee_code);
//hidden("no_of_leave",$interval->format('%R%a days'));
//hidden("no_of_leave",floor($datediff/(60*60*24)));

table_section(2);

?><h2>Accounts use only</h2><?php

//text_row("Account Info:",'account_info', null, 40, 10);
bank_accounts_list_row(_("Company Bank:"), 'account_info');
text_row("Amount", 'amount', round2($total_encashment), 10, 10);
text_row("Taxable amount", 'taxable_amount', $anual_gratuaity, 10, 10);
text_row("Income Tax", 'income_tax', null, 10, 10);
//text_row(_("Deduction months:"), 'payment_shedule', null, 5, 5);
payment_terms_list_row("Mode of payment",'mode_of_payment');
textarea_row(_("Remarks:"), 'remarks', null, 35, 2);
$ref = $Refs->get_next(ST_JOURNAL);
text_row("GL Voucher", 'gl_voucher', $ref, 10, 10);
text_row("Check No", 'check_no', null, 10, 10);

date_row(_("Check Date"),'check_date', null,null, 0, 0, 0, null, true);



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
