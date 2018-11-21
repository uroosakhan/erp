<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();
	
page(_($help_context = "Advance"), @$_REQUEST['popup'], false, "", $js);

//page(_($help_context = "Leave"), $js);

include($path_to_root . "/payroll/includes/db/advance_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc"); 
include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
function get_current_fiscalyear1()
{
	global $path_to_root;
	include_once($path_to_root . "/admin/db/company_db.inc");
	$year = get_company_pref('f_year');

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year WHERE id=".db_escape($year);

	$result = db_query($sql, "could not get current fiscal year");

	return db_fetch($result);
}
function add_emp_info_for_earn_n($emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
{
	$sql = "INSERT INTO ".TB_PREF."leave (emp_id,from_date,to_date,leave_type,no_of_leave,reason,approve,f_year,availed) 
    VALUES ( ".db_escape($emp_id).", ".db_escape(sql2date($from_date)).", ".db_escape(sql2date($to_date)).",
     ".db_escape($leave_type).", ".db_escape($no_of_leave).", ".db_escape($reason).",
     ".db_escape(1) . ",".db_escape($f_year) . ",".db_escape(E) . ")";
	db_query($sql,"The department could not be added");
}
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
	if (!is_numeric($_POST['payment_shedule']))
	{
		$input_error = 1;
		display_error( _("Value must be numeric."));
		set_focus('payment_shedule');
	}
	if(strlen($_POST['payment_shedule']) >= 120)
	{
		$input_error = 1;
		display_error( _("Plz select less than 120 valu"));
		set_focus('payment_shedule');
	}


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

			$f_year = get_current_fiscalyear1();

    		add_advance( $_POST['date'], $_POST['emp_id'], $_POST['amount'], $_POST['type'],
				$_POST['remarks'],$_POST['approve'], $_POST['payroll_id'],
				$_POST['payment_shedule'], $_POST['amount'],$f_year['id'],$_POST['mode_of_payment'],
				$_POST['cheque_no'],$_POST['cheque_date'],$_POST['bank_account'],$_POST['advance_on_base_of'],$_POST['month']);
			add_emp_info_for_earn_n($_POST['emp_id'],$_POST['date'],$_POST['date'],2,$_POST['leave_days'],$reason,$approve,$f_year['id']);

			global $Refs;
				$advance_amount=$_POST['amount'];
				$shedule1=$_POST['payment_shedule'];
				$payment_permonth1=($advance_amount/$shedule1);
				$shedule_payment_month=round2($payment_permonth1);

				$employee_id=$_POST['emp_id'];
				$employee_name=get_employee_name($employee_id);
				$id = get_next_trans_no(ST_JOURNAL);
				$ref = $Refs->get_next(ST_JOURNAL);
				$memo = "Advance paid to $employee_name amounting to $advance_amount, payment schedule $shedule_payment_month per month";

//				$advance_receivable_account=get_sys_pay_pref('advance_receivable');
//				$payment_account=get_sys_pay_pref('payment');
			
				$pay = get_bank_account($_POST['bank_account']);
				$emp_advance_bank = get_employee_advance_bank_account($employee_id);



				// add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $emp_advance_bank,'', '', $memo, $advance_amount);
				// add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $pay['account_code'],'','', $memo, -$advance_amount);

				// //add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
				// 				add_audit_trail_payroll(ST_JOURNAL, $id, $_POST['date'],"Inserted");

				// add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
				// $Refs->save(ST_JOURNAL, $id, $ref);

			$note = _('New Advance  has been added');
    	}

		display_notification($note);
		display_notification(_("Advance record has been updated. JV Reference $ref."));
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
		display_error(_("Cannot delete this Advance because customers have been created using this group."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_advance($selected_id);
		display_notification(_('Selected Advance  has been deleted'));
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
	delete_advance($selected_id);
   display_notification(_("Advance record has been deleted."));	
}

if (isset($_POST['delete'])) 
{		
	handle_delete($_POST['id']);
}
function handle_update($selected_id)
{
	$f_year = get_current_fiscalyear1();
	update_advance($selected_id,$_POST['date'],$_POST['emp_id'],$_POST['amount'],
		$_POST['type'],$_POST['remarks'],$_POST['approve'],$_POST['payroll_id'],
		$_POST['payment_shedule'],$_POST['payment'],$f_year['id'],$_POST['mode_of_payment'],
		$_POST['cheque_no'],$_POST['cheque_date'],$_POST['bank_account'],$_POST['month']);

$approval11=$_POST['approve'];
if($approval11==1)
{
			global $Refs;
            $advance_amount=$_POST['amount'];
			$shedule1=$_POST['payment_shedule'];
            $payment_permonth1=($advance_amount/$shedule1);
			$shedule_payment_month=round2($payment_permonth1);
			
			$employee_id=$_POST['emp_id'];
			$employee_name=get_employee_name($employee_id);
			$id = get_next_trans_no(ST_JOURNAL);
			$ref = $Refs->get_next(ST_JOURNAL);
			$memo = "Advance paid to $employee_name amounting to $advance_amount, payment schedule $shedule_payment_month per month";
//			$advance_receivable_account=get_sys_pay_pref('advance_receivable');
//			$payment_account=get_sys_pay_pref('payment');

// 	$pay = get_bank_account($_POST['bank_account']);
// 	$emp_advance_bank = get_employee_advance_bank_account($employee_id);

// 			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $emp_advance_bank,'', '', $memo, $advance_amount);
// 			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $pay['account_code'],'','', $memo, -$advance_amount);
							
// 			//add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
// 							add_audit_trail_payroll(ST_JOURNAL, $id, $_POST['date'],"Inserted");

		//	add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
		//	$Refs->save(ST_JOURNAL, $id, $ref);	
}
   display_notification(_("Advance record has been updated. JV Reference $ref."));	
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
		$myrow = get_advance($_GET['id']);
if($myrow["emp_id"] != '')
{
	$_POST['date']  = sql2date($myrow["date"]);
	$_POST['emp_id']  = $myrow["emp_id"];
	$_POST['advance_on_base_of']  = $myrow["advance_on_base_of"];
	$_POST['amount']  = $myrow["amount"];
	$_POST['type']  = $myrow["type"];
	$_POST['remarks']  = $myrow["remarks"];
	$_POST['approve']  = $myrow["approve"];
	$_POST['payroll_id']  = $myrow["payroll_id"];
	$_POST['payment_shedule']  = $myrow["payment_shedule"];
	$_POST['f_year']  = $myrow["f_year"];


	$_POST['mode_of_payment']  = $myrow["mode_of_payment"];
	$_POST['cheque_no']  = $myrow["cheque_no"];
	$_POST['cheque_date']  = sql2date($myrow["cheque_date"]);
	$_POST['bank_account']  = $myrow["bank_account"];



	//$_POST['payment']  = $myrow["payment"];

	//}
	hidden("payment", $payment_permonth);
	hidden("selected_id", $selected_id);
	hidden("id", $id);

}

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
advance_base_list_row(_("Advacne On Base Of :"), 'advance_on_base_of', $_POST['advance_on_base_of'],true,true, false,  false);

if($_POST['advance_on_base_of'] == 3)
{
	$applicable = 2;
	employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true,null,null,null,$applicable);
}
elseif($_POST['advance_on_base_of'] == 1)
{
	$applicable = 1;
	employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true,null,null,null,$applicable);
}
else{
	employee_list_row(_("Employee Name:"), 'emp_id', $_POST['emp_id'],true,null,null,null,$applicable);
}

if($_POST['advance_on_base_of']==3)
{
	text_row("Leave days",'leave_days', null  , 30, 30);
}

elseif($_POST['advance_on_base_of']==2)
{
	//month_list_row( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));
	month_list_row( "Advance apply from Month", 'month', null, true);
}
text_row("Amount", 'amount', null, 10, 10);
date_row(_("Date"),'date', null,null, 0, 0, 0, null, true);
text_row(_("Deduction months:"), 'payment_shedule', null, 5, 5);

yesno_list_cells(_("Mode of Payment "),'mode_of_payment',$_POST['mode_of_payment'],'Cheque','Cash',true);

if($_POST['mode_of_payment'] == 1)
{
	text_row("Cheque No",'cheque_no', null  , 30, 30);
	date_row(_("Cheque Date"),'cheque_date', null,null, 0, 0, 0, null, true);
}
bank_accounts_list_row( "Bank", 'bank_account', null, true);

//fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
textarea_row(_("Remarks:"), 'remarks', null, 35, 5); 

hidden("payment", $payment_permonth);
//hidden("no_of_leave",$interval->format('%R%a days')); 
//hidden("no_of_leave",floor($datediff/(60*60*24)));
end_table(1);


$emp_advance_bank = get_employee_advance_bank_account($_POST['emp_id']);
$pay = get_bank_account($_POST['bank_account']);

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
		gl_all_accounts_list_row(_("Advance Receivable Account:"), 'advance_receivable', $emp_advance_bank);
		gl_all_accounts_list_row(_("Payment Account:"), 'payment_account', $pay);
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
	  
	 
	   
     end_table(1);
	}




end_form();

end_page();
?>
