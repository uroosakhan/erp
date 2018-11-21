<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
//include_once($path_to_root . "/payroll/includes/db/attendance_db.inc"); //
include_once($path_to_root . "/payroll/includes/db/bulk_wise_gratuity.inc"); //

include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/payroll/includes/db/gl_setup_db.inc"); //
include($path_to_root . "/payroll/includes/db/gratuity_db.inc");
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Bulk Wise Leave Encashment Entry"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

if (isset($_GET['debtor_no']))
{
	$_POST['project'] = $_GET['debtor_no'];
}

$selected_id = get_post('project','');
$emp_dept = get_post('emp_dept','');

//--------------------------------------------------------------------------------------------
/*function can_process()
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
//			if (strlen($_POST['present'.$i]) == 0)
//			{
//				display_error(_("The present field cannot be empty."));
//				set_focus('present'.$i);
//				return false;
//			}
//
//			if (strlen($_POST['absent'.$i]) == 0)
//			{
//				display_error(_("The absent cannot be empty."));
//				set_focus('absent'.$i);
//				return false;
//			}
//
		}
	return true;
}*/
//--------------------------------------------------------------------------------------------

function handle_submit(&$selected_id,$date)
{
	global  $Refs;
	    for($i = 1; $i <= $_POST['count']; $i++) {
			if ($_POST['amount'.$i] > 0)
			{

				$id = get_next_trans_no(ST_JOURNAL);
				$ref = $Refs->get_next(ST_JOURNAL);

				$tax_rate = get_tax_amount_empl_vise($_POST['taxable_amount'.$i]);
				$yearly_salary_tax = $_POST['taxable_amount' . $i] * $tax_rate / 100;
				$income_tax = round2($yearly_salary_tax);

				add_gratuity($_POST['encashment_id' . $i], $_POST['employee_id' . $i], $_POST['approve' . $i],
					$_POST['days' . $i], $_POST['f_year'],
					$_POST['payment_date' . $i], $_POST['amount' . $i], $_POST['taxable_amount' . $i],
					$income_tax, $_POST['mode_of_payment' . $i], $_POST['remarks' . $i],
					$ref, $_POST['check_no'.$i], $_POST['check_date' . $i],
					$_POST['date' . $i], $_POST['tax_installments' . $i], $_POST['gl_it' . $i], $_POST['account_info' . $i]);


				if ($_POST['approve' . $i] == 1) {


					global $Refs;
					$amount = ($_POST['amount'.$i] + $income_tax);
//				$shedule1=$_POST['payment_shedule'];
//				$payment_permonth1=($amount/$shedule1);
//				$shedule_payment_month=round2($payment_permonth1);

					$employee_id = $_POST['employee_id' . $i];
					$employee_name = get_employee_name($employee_id);

					$memo = "Gratuity paid to $employee_name amounting to $amount";
					//$advance_receivable_account=get_sys_pay_pref('advance_receivable');
					$acc = get_employee_data3($employee_id);
					$payment_account = get_sys_pay_pref('payment');

					$bank = get_bank_account($_POST['account_info' . $i]);
					$f_year = get_current_fiscalyear();

					$advance_amnt = get_advance_against_gratuity($employee_id, $f_year['id']);

					add_gl_trans(ST_JOURNAL, $id, $_POST['date'.$i], $acc['salary_account'], '', '', $memo,
						$amount, 0, 0, 0, 0, 0, $bank, $_POST['check_no'.$i],
						$_POST['check_date'.$i], $_POST['f_year'], $ref, $_POST['gl_it'.$i]);

					add_gl_trans(ST_JOURNAL, $id, $_POST['date'.$i], $payment_account, '', '', $memo,
						-($amount - $_POST['income_tax'] - $advance_amnt), 0, 0, 0, 0, 0, $bank, $_POST['check_no'.$i],
						$_POST['check_date'.$i], $_POST['f_year'], $ref, $_POST['gl_it'.$i]);

					add_gl_trans(ST_JOURNAL, $id, $_POST['date'.$i], $acc['tax_liability'], '', '', $memo,
						-$income_tax, 0, 0, 0, 0, 0, $bank, $_POST['check_no'.$i],
						$_POST['check_date'.$i], $_POST['f_year'], $ref, $_POST['gl_it'.$i]);

					add_gl_trans(ST_JOURNAL, $id, $_POST['date'.$i], $acc['advance_receivable'], '', '', $memo,
						-$advance_amnt, 0, 0, 0, 0, 0, $bank, $_POST['check_no'.$i],
						$_POST['check_date'.$i], $_POST['f_year'], $ref, $_POST['gl_it'.$i]);


					if ($advance_amnt > 0) {
						$gratuity_id = get_gratuity_last_id();
						add_advance_deduction($employee_id, $advance_amnt, $_POST['date'], $bank, $f_year['id'],
							$gratuity_id);
					}

					add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
					add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);

				}


				$Refs->save(ST_JOURNAL, $id, $ref);
			}
			display_notification(_("Employee record has been added."));
		}

}
//--------------------------------------------------------------------------------------------

if (isset($_POST['add_gratuities']))
{
	  /*if(can_process())
	  {
		$check = check_month_duplication($selected_id, $month_id);
		if($check > 0)
		{
		 display_error(_("Aleardy Inserted"));
		 set_focus('project');
		}*/

		handle_submit($selected_id);
	  //}
}

if(isset($_POST['update']))
{
		 if(can_process())
		  {
			    for($i =1; $i <= $_POST['count_update']; $i++)
				{
				update_attendance($_POST['present'.$i], $_POST['overtime'.$i], $_POST['absent'.$i], $_POST['employee_id'.$i], $_POST['monthid'],
					$_POST['date'],$_POST['f_year'],$_POST['divison'],$selected_id,$_POST['location']);
				}
				 display_notification(_("Employee record has been added.".$_POST['count_update']));
				 set_focus('project');

		  }
}

function customer_settings($selected_id, $emp_dept)
{
	    $data = get_emp_by_department($emp_dept,'leave_encashment');
	
		//if($check == 0)
		{
		echo "<center><h3>Insert New Record</h3></center>";
		start_outer_table(TABLESTYLE2,"width=100%");
		table_section(1);
		echo "<tr><td colspan='' class='tableheader'> &nbsp; Employee Name &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp; Approval &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Date &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Days For Enchashment</td>
		<td colspan='' class='tableheader'>&nbsp; Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Taxable Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Income Tax &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Payment Date &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Company Bank &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Mode of payment &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Remarks &nbsp;</td>
<!--		<td colspan='' class='tableheader'>&nbsp; GL Voucher &nbsp;</td>-->
		<td colspan='' class='tableheader'>&nbsp; Cheque No &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Cheque Date &nbsp;</td></tr>
		
		";
		$a = 1;
		$working_days = get_sys_pay_pref('total_working_days');

		while($myrow = db_fetch($data))
		{
			$total_allowed_days = get_total_no_of_allowed_leaves2();
			$no_of_levaes = get_no_of_leave2($myrow['employee_id']);
			$emp_data = get_employee_data3($myrow['employee_id']);

			$days_remaining = $total_allowed_days['leave_days'] - $no_of_levaes;
			$working_days = get_sys_pay_pref('total_working_days');
			$per_day_salary =  $emp_data['basic_salary'] / $working_days ;
			$DOJ = sql2date($emp_data['j_date']);
			$current_date = Today();
			$no_of_days = date_diff2($current_date, $DOJ, "d");

			$final = $no_of_days / 365 * $emp_data['basic_salary']  ;
			$f_year = get_current_fiscalyear();
			$advance_against_gatuity = get_advance_against_gratuity($myrow['employee_id'],$f_year['id']);
			$after_gratuity = $final - $advance_against_gatuity;

			if($emp_data['tax_deduction'] ==1)
			{
//		$yearly_salary =  $emp_data['basic_salary'] * 12;
				$tax_rate = get_tax_amount_empl_vise($after_gratuity);

				$yearly_salary_tax = $after_gratuity *$tax_rate/100 ;
				$_POST['taxable_amount'] = $after_gratuity;
				$income_tax = $yearly_salary_tax;
			}

			echo "<tr>";
			echo'<td>';
			employee_list_row(null, 'employee_id'.$a,$myrow['employee_id']);
			echo'</td>';

			echo'<td>';

			approve_list_cells_new('','approve'.$a);
			echo'</td>';
			
//			echo'<td>';
			date_cells('','date'.$a, null,null, 0, 0, 0, null, true);
//			echo'</td>';

//			echo'<td>';
			text_cells('','tax_installments'.$a,$_POST['tax_installments']);
//			echo'</td>';

//			echo'<td>';
			text_cells(null, 'amount'.$a, round2($after_gratuity), 20, 10);
//			echo'</td>';

//			echo'<td>';
			text_cells(null, 'taxable_amount'.$a,round2($after_gratuity), 20, 10);
//			echo'</td>';



//			echo'<td>';
			text_cells(null, 'income_tax'.$a,$income_tax, 10, 10);
//			echo'</td>';

//			echo'<td>';
			date_cells(null,'payment_date'.$a, null,null, 0, 0, 0, null, true);
//			echo'</td>';

//			echo'<td>';
			bank_accounts_list_cells( null, 'account_info'.$a, null, false);
//			echo'</td>';

//			echo'<td>';
			payment_terms_list_cells(null,'mode_of_payment'.$a);
//			echo'</td>';

//			echo'<td>';
			textarea_cells(null, 'remarks'.$a, null, 35, 2);
//			echo'</td>';

//			echo'<td>';
			text_cells(null, 'check_no'.$a, null, 10, 10);
//			echo'</td>';
			
//			echo'<td>';
			date_cells(null,'check_date'.$a, null,null, 0, 0, 0, null, true);
//			echo'</td>';

			echo "</tr>";

			hidden('count', $a);
			$a++;


		}
		end_outer_table(1);
		div_start('controls');
		//if($a != 1)
		submit_center('add_gratuities', _("Add Gratuities"), true, '', 'default');
		div_end();
		}

}

//--------------------------------------------------------------------------------------------

$date = get_post('date','');


start_form();
$f_year=get_post('f_year','');
if (db_has_customers())
{
	start_table(TABLESTYLE_NOBORDER);
	start_row();


	//emp_dept_row(_("Division:"), 'emp_dept', null,true,true);
  dimensions_list_cells(_("Division"), 'emp_dept', null, 'All division', "", false, 1,true);

	$f_year = get_current_fiscalyear();

    hidden('f_year', $f_year['id']);

	end_row();
	end_table();
//var_dump(get_post('f_year'));
	if (get_post('_show_inactive_update')) {
		$Ajax->activate('project');
		set_focus('project');
	}
}
else
{
	hidden('project');
}


/*if(!$selected_id)
{
set_focus('project');
}
else
{*/
	if(!$emp_dept)
	set_focus('month');
	else
    customer_settings($selected_id, $emp_dept);
//}
br();

hidden('popup', @$_REQUEST['popup']);
end_form();
//echo "selected_id ".$selected_id."<br /> month_id ".$month_id;
end_page(@$_REQUEST['popup']);

?>