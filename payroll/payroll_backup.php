<?php
$page_security = 'SA_SUPPLIER';
$path_to_root = "..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/attendance_db.inc");
include_once($path_to_root . "/payroll/includes/db/month_db.inc");
include_once($path_to_root . "/payroll/includes/db/payroll_db.inc");
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

add_js_file('login.js');
page(_($help_context = "Payroll Entry"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

//functions



function get_max_trans_no_()
{
	$sql="select Max(trans_no) FROM ".TB_PREF."debtor_trans ";
	$db = db_query($sql,'Cant get Employee data');
	$ft = db_fetch($db);
	return $ft[0];
}

function can_process()
{
	for($i = 1; $i <= $_POST['count']; $i++)
	{
		$input_error=0;
		if (!is_date($_POST['date']))
		{
			display_error(_("The entered date for the payment is invalid."));
			set_focus('date');
			$input_error = 1;
			return false;
		}
		/*if (strlen($_POST['tax'.$i]) == 0)
        {
            display_error(_("The tax cannot be empty."));
            set_focus('tax'.$i);
            return false;
        }
        if (strlen($netpayable) == 0)
        {
            $input_error = 1;
            display_error(_("The Net payable  cannot be empty.$netpayable"));
            set_focus($netpayable);
            return false;
        }
        if (!is_numeric($_POST['tax'.$i]))
        {
            $input_error = 1;
            display_error( _("Tax value must be numeric."));
            set_focus('tax');
        }
        if (!is_numeric($_POST['overtime'.$i]))
        {
            $input_error = 1;
            display_error( _("Overtime Amount must be numeric."));
            set_focus('overtime');
        }
        if (!is_numeric($_POST['deduction'.$i]))
        {
            $input_error = 1;
            display_error( _("Absent deduction must be numeric."));
            set_focus('deduction');
        }
        if (!is_numeric($_POST['adv_deduction'.$i]))
        {
            $input_error = 1;
            display_error( _("Advance deduction must be numeric."));
            set_focus('adv_deduction');
        }*/

	}
	return true;
}

function handle_submit($selected_id, $month_id,$date)
{
	$basic_salary = $tax = $overtime = $late_dedcution = $adv_deduction = 0 ;
	add_payroll_head($_POST['project'], $_POST['month_id_2'], $_POST['date'],$_POST['gl_id']);
	$max_trans_head=check_payroll_head_max_trans();

//	$basic_salary = 214000;

	$all_employees[]='';
	$eobi =get_sys_pay_pref('eobi') ;

	for($i = 1; $i <= $_POST['count']; $i++)
	{

		$f_year = get_current_fiscalyear();
		add_payroll($_POST['sal'.$i], $_POST['tax'.$i], $_POST['tax_rate_2'.$i], $_POST['overtime'.$i], $_POST['deduction'.$i],
			$_POST['adv_deduction'.$i], $_POST['emp_id'.$i], $_POST['emp_dept'], $_POST['month_id_2'],
			$date,$max_trans_head,$_POST['over_time_hour'.$i],$_POST['absent_days_2'.$i],
			$_POST['leave_days_2'.$i],$_POST['advance_2'.$i],$_POST['allowance'.$i],
			$_POST['deduction'.$i],$_POST['emp_cpf'.$i],$_POST['employer_cpf'.$i],$f_year['id'],
			$_POST['division'],$_POST['location'],$_POST['project'],$_POST['man_month_value'.$i],
			$_POST['project_wise_salary'.$i],$eobi,$_POST['total_deduction'.$i],$_POST['net_salary'.$i]);
		
		$max_trans=get_payroll_max_trans_no();

		$all_employees[] =  $_POST['emp_id'.$i];

		if( $max_trans==0)
		{
			add_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i], 1,$_POST['remarks'.$i], 1,$i);
			$row2=employee_allowance_detail($_POST['emp_id'.$i]);
			while($myrow2 = db_fetch($row2)) // for getting allowance break up details
			{
				add_payroll_allowance($i, $myrow2['allow_id'], $myrow2['amount'],$myrow2['emp_id']);
			}
			$row3=employee_deduction_detail($_POST['emp_id'.$i]);
			while($myrow3 = db_fetch($row3)) // for getting deduction break up details
			{
				add_payroll_deduction($i, $myrow3['deduc_id'], $myrow3['amount'],$myrow3['emp_id']);
			}
		}
		else{
			add_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i], 1,$_POST['remarks'.$i], 1,$max_trans);
			$row2=employee_allowance_detail($_POST['emp_id'.$i]);
			while($myrow2 = db_fetch($row2)) // for getting allowance break up details
			{
				add_payroll_allowance($max_trans, $myrow2['allow_id'], $myrow2['amount'],$myrow2['emp_id']);
			}
			$row3=employee_deduction_detail($_POST['emp_id'.$i]);
			while($myrow3 = db_fetch($row3)) // for getting deduction break up details
			{
				add_payroll_deduction($max_trans, $myrow3['deduc_id'], $myrow3['amount'],$myrow3['emp_id']);
			}
		}

		$basic_salary += $_POST['sal'.$i];
		$man_month += $_POST['project_wise_salary'.$i];
		$tax += $_POST['tax'.$i];
		$overtime += $_POST['overtime'.$i];
		$late_deduction += $_POST['deduction'.$i];
		$allowance += $_POST['allowance'.$i];
		$adv_deduction += $_POST['adv_deduction'.$i];
		$employye_data = get_employee_data($_POST['emp_id'.$i]);
		$emp_bank=  get_bank_account($employye_data['company_bank']);

/*
$trans = get_max_trans_no_();
		if($trans == '')
			$trans = 1;
		else
		   $trans = $trans+1;

		$ref = get_next_reference(12);*/

//		display_error($_POST['emp_id'.$i]." -- ".$rmp_bank['account_code']);

		/*$trans = write_bank_transaction_new(
			ST_BANKPAYMENT, $trans, $emp_bank['account_code'],
			$employye_data['salary_account'], $_POST['date'],
			$employye_data['salary'], $_POST['emp_id'.$i], $_POST['emp_id'.$i],
			$ref, '', true,$employye_data['basic_salary']);

		$trans_type = $trans[0];
		$trans_no = $trans[1];
		new_doc_date($_POST['date']);*/

//		$_SESSION['pay_items']->clear_items();
//	unset($_SESSION['pay_items']);
//		commit_transaction();
	}

	$month_name=get_month_name($month_id );
	$dept_name=get_emp_dept_name($selected_id );


	$netpayable = $basic_salary - $tax + $overtime - $late_dedcution - $adv_deduction;
	$toal_exp=$man_month+$allowance;
	$total_sal=($man_month+$allowance)-$tax-$adv_deduction-$eobi;
	$total_lab=$total_sal+$tax+$adv_deduction+$eobi;


	global $Refs;


	$ref = $Refs->get_next(ST_JOURNAL);

//			$stock_gl_code = get_stock_gl_code($stock_id);
	$memo = "Payroll entry of department $dept_name for the month of $month_name.";


	$payroll_expenses_account = get_sys_pay_pref('payroll_expenses');
	$payroll_liabilty_account = get_sys_pay_pref('payroll_liabilty');
	$tax_liability_account = get_sys_pay_pref('tax_liability');
	$eobi_account = get_sys_pay_pref('eobi_liability');
	$advance_receivable_account = get_sys_pay_pref('advance_receivable');
	$id = get_next_trans_no(ST_JOURNAL);

	add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_expenses_account,'', '', $memo, $toal_exp);
	add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_liabilty_account,'','', $memo, -$total_sal);
	add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $tax_liability_account,'','', $memo, -$tax);
	add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $eobi_account,'','', $memo, -$eobi);
	add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $advance_receivable_account,'','', $memo, -$adv_deduction);

	write_journal_entries_new($all_employees,$basic_salary ,$_POST['date'],$use_transaction=true);

	add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
	add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
	$Refs->save(ST_JOURNAL, $id, $ref);

	display_notification(_("Payroll record for department $dept_name has been posted for the month of $month_name. JV Reference $ref.$netpayable.........$j,,,$emp_idddd"));
	set_focus('project');
}

function handle_update($selected_id, $month_id,$trans_no)
{
	for($i = 1; $i <= $_POST['count']; $i++)
	{
		update_payroll($_POST['trans_no'.$i],$_POST['tax'.$i], $_POST['overtime'.$i], $_POST['deduction'.$i], $_POST['adv_deduction'.$i], $_POST['date'], $_POST['emp_id'.$i], $_POST['emp_dept_2'.$i], $_POST['month_id_2'.$i]);

		update_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i],$_POST['trans_no'.$i]);
	}
	display_notification(_("Employee record has been updated."));

	set_focus('project');
}

//
function handle_delete($selected_id, $month_id,$emp_id,$trans_no,$payroll_head)
{
	delete_payroll_head($_POST['payroll_head']);
	for($i = 1; $i <= $_POST['count']; $i++)
	{
		delete_payroll($_POST['trans_no'.$i]);
		delete_payroll_deduction($_POST['trans_no'.$i]);
		delete_payroll_allowance($_POST['trans_no'.$i]);
		delete_advance($_POST['emp_id'.$i],$_POST['trans_no'.$i]);
	}

	display_notification(_("Payroll record has been deleted."));

	set_focus('trans_no');
}

function get_designation_name($id)
{
	$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}



function customer_settings($dept_id, $id_month,$trans_no,$date,$divison,$f_year,$location)
{
	$f_year = get_current_fiscalyear();
	$check = pay_roll_entry($_POST['division'],$_POST['location'],$_POST['project'],$f_year['id'],$id_month);
	$f_year = get_current_fiscalyear();
	if($check == 0)
	{

		echo "<center><h3>Insert New Record</h3></center>";
		$row = get_employee_through_dept_id_new($dept_id,$divison,$location,$f_year['id']);//999999999

		$emp_acc_dept = get_employees_acc_dept($dept_id);
		$_POST['count'] =  $emp_acc_dept;
		//for allowances

		$k = 0; //row colour counter
		start_outer_table(TABLESTYLE2);
		table_section(1);
		echo
		"<tr><td colspan='' class='tableheader'> &nbsp; Employee Code &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Employee &nbsp;</td>
    	<td colspan='' class='tableheader'>&nbsp; Designation &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Basic Salary &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Rate&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Amount&nbsp;</td>
       <td colspan='' class='tableheader'>&nbsp; Man Month &nbsp;</td>	
   		<td colspan='' class='tableheader'>&nbsp; Salary as Man Month &nbsp;</td>	
	    <td colspan='' class='tableheader'>&nbsp; EOBI &nbsp;</td>
		<!--<td colspan='' class='tableheader'>&nbsp; Employee CPF &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Employer CPF &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Duty Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Leave Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Deductible Days &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Deduction &nbsp;</td>-->
		<td colspan='' class='tableheader'>&nbsp; Advance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Allowance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Total Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Net Salary &nbsp;</td>
		</tr>
		";
		$year=date("Y");
		echo $presentinfo['basic_salary'];

		$a = 1;

		while($myrow_new = db_fetch($row))
		{
			$myrow = get_employee_through_dept_id2($myrow_new['employee_id']);

			$date11 = "1-".$id_month."-$year";
			$from_date = date2sql(begin_month($date11));
			$to_date = date2sql(end_month($date11));
			//echo $to_date;
			//
			$dateforleave = "1-".$id_month."-$yearforleave";
			$from_dateleave = date2sql(begin_month($dateforleave));
			$to_dateleave = date2sql(end_month($dateforleave));
			$emp = employee_attendance_detail($myrow['employee_id'], $id_month);
			$presentinfo = employee_present_attendance_detail($myrow['employee_id'],$myrow['project'],$from_date,$to_date);
			$leaveinfo = employee_leave_attendance_detail($myrow['employee_id'],$from_date,$to_date);

//			$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$to_date);
			$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$to_date,$_POST['f_year']);

			$payroll = payroll_detail($myrow['employee_id'], $id_month);
			$overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow['project'],$from_date,$to_date);
			$trans_=get_payroll_through_trans_no($myrow['employee_id']);
//			$advance_pay=employee_advance_pay_detail($myrow['employee_id'],$payroll['trans_no'],$from_date,$to_date);
			$advance_pay=employee_advance_pay_detail($myrow['employee_id'],$payroll['trans_no'],$from_date,$to_date,$_POST['f_year']);

			$month_days = get_month_days($id_month);
			$working_days = get_sys_pay_pref('total_working_days');
			$per_day_salary = ($myrow['basic_salary']/ $working_days);

			$emp_man_month_value = get_project_wise_salary($myrow['employee_id'],$_POST['f_year'],$myrow['project'],$id_month);
			$project_wise_salary = $emp_man_month_value * $working_days * $per_day_salary;
			$month_days = get_month_days($id_month);

			$per_day_salary = ($myrow['basic_salary']/ $month_days);
			$overtime_amt= ( ($myrow['basic_salary']/$month_days) / ($myrow['duty_hours']) ) * 1.5 * $emp[5];  //$emp[5] = overtime
			$employee_deduction=employee_deduction($myrow['employee_id']);
			$employee_allowances=employee_allowances($myrow['employee_id']);
			echo "<tr>";
			alt_table_row_color($k);
			label_cell( $myrow['emp_code'], "", $myrow['emp_code']);
			label_cell( $myrow['emp_name'], "", $myrow['emp_name']);
			label_cell( get_designation_name($myrow['emp_desig']));
			label_cell( $myrow['basic_salary']);
			$annual_salary = $myrow['basic_salary'] * 12;
			//$tax_rate=get_tax_amount_empl_vise($annual_salary);
			if($annual_salary > 400000)
			{
				if($annual_salary > 400000 && $annual_salary <  750000)
                 {
                  $tax_rate = 5;
                 }
                if($annual_salary > 750000 && $annual_salary <  1500000)
                 {
                  $tax_rate = 10;
                 }
                if($annual_salary > 1500000 && $annual_salary <  2000000)
                 {
                  $tax_rate = 15;
                 }
                if($annual_salary > 2000000 && $annual_salary <  2500000)
                 {
                  $tax_rate = 17.50;
                 }
                if($annual_salary > 2200000)
                 {
                  $tax_rate = 20;
                 }

				if($myrow['text_filer']==1)
				{
					$tax_rate=get_tax_amount_empl_vise($annual_salary);
					$annual_taxable_salary = $annual_salary  - 400000;
					$annual_tax = $annual_taxable_salary * ($tax_rate/100);
					$monthly_tax = $annual_tax / 12;
				}
				else{
					$tax_rate=0;
					$annual_taxable_salary = $annual_salary  - 400000;
					$annual_tax = $annual_taxable_salary * ($tax_rate/100);
					$monthly_tax = $annual_tax / 12;
				}

				label_cell( $tax_rate  . "%"); // tax rate

				text_cells( null,'tax'.$a, round($monthly_tax), 20, 60, false,'','','', 'Tax');
			}
			else
			{
				label_cell("0%"); // tax rate
				text_cells( null,'tax'.$a, 0, 20, 60, false,'','','', 'Tax');
			}
			$emp_salary=$myrow['basic_salary'];
			$empployee_cpf=$myrow['cpf'];
			$empployer_cpf=$myrow['employer_cpf'];
			if($emp_salary>5000)
			{
				$calculate_emp_cpf=$empployee_cpf*(5000/100);
			}
			else
			{
				$calculate_emp_cpf=$empployee_cpf*($emp_salary/100);
			}
			//employer
			if($emp_salary>5000)
			{
				$calculate_employer_cpf=$empployer_cpf*(5000/100);
			}
			else
			{
				$calculate_employer_cpf=$empployer_cpf*($emp_salary/100);
			}
			label_cell($emp_man_month_value);
			label_cell($project_wise_salary);
			label_cell(get_sys_pay_pref('eobi'));




			/*label_cell($calculate_emp_cpf);
			label_cell($calculate_employer_cpf);
			label_cell($myrow['duty_hours']); // duty hours

			label_cell($overtimeinfo); // overtime hours
			text_cells( null,'overtime'.$a, round($overtime_amt), 20, 60, false,'','','', 'Over Time');
			label_cell($presentinfo);
			label_cell( $leaveinfo);
			$trans_no=$payroll['trans_no'];
			if($presentinfo>=$leaveinfo)
			{
				$diff_absentleave=$presentinfo-$leaveinfo;
				label_cell( $diff_absentleave);
			}
			text_cells( null,'deduction'.$a, round($diff_absentleave*$per_day_salary), 20, 60, false,'','','', 'Deduction');*/
			label_cell( round2($advanceinfo));
			if($advanceinfo>0)
			{
//				text_cells( null,'adv_deduction'.$a, round($advance_pay), 20, 60, false,'','','', 'Advance    Deduction');
				text_cells( null,'adv_deduction'.$a, round($advance_pay), 20, 60, false,'','','', 'Advance    Deduction');

			}
			else
			{
				text_cells( null,'adv_deduction'.$a, 0, 20, 60, false,'','','', 'Advance    Deduction');
			}

			$employee_id=$myrow['employee_id'];
			employee_emp_allowance_cell($employee_id,$employee_allowances);
			employee_emp_deduction_cell($employee_id,$employee_deduction);
			$total_deduction = 	$advance_pay + $monthly_tax + $employee_deduction + get_sys_pay_pref('eobi');

			label_cell(round($total_deduction));//total deduction
			label_cell(round2( $employee_allowances + $project_wise_salary - $total_deduction));//net salary

			$gl_id = get_next_trans_no(ST_JOURNAL);
			hidden('emp_id'.$a, $myrow['employee_id']);

			hidden('total_deduction'.$a,$total_deduction);
			$net_salary  =$employee_allowances + $project_wise_salary - $total_deduction;
			hidden('net_salary'.$a, $net_salary);

			hidden('project', $dept_id);
			hidden('division', $divison);
			hidden('location', $location);
			hidden('f_year', $f_year);
			hidden('adv_deduction'.$a,$advance_pay);
			hidden('sal'.$a, $myrow['basic_salary']);
			hidden('month_id_2', $id_month);
			hidden('date', $date);
			hidden('trans_no'.$a,$payroll['trans_no']);
			hidden('absent_days_2'.$a,$presentinfo);
			hidden('advance_2'.$a,$advanceinfo);
			hidden('over_time_hour'.$a,$overtimeinfo);
			hidden('leave_days_2'.$a,$leaveinfo);
			hidden('allowance'.$a,$employee_allowances);
			hidden('deduction'.$a,$employee_deduction);
			hidden('emp_cpf'.$a,$calculate_emp_cpf);
			hidden('employer_cpf'.$a,$calculate_employer_cpf);
			hidden('tax_rate_2'.$a,$tax_rate);
			hidden('project_wise_salary'.$a,$project_wise_salary);
			hidden('man_month_value'.$a,$emp_man_month_value);


			hidden('gl_id',$gl_id);
			hidden('count', $a);
			$a++;
			echo "</tr>";
		}
		end_row();
		end_outer_table(1);

		div_start('controls');
		submit_center('insert', _("Insert"), true, '', 'default');
		div_end();
	}

	if($check > 0)
	{

		echo "<center><h3>Payroll Record</h3></center>";
//		$row = get_employee_through_dept_id($dept_id,$_POST['division'],$_POST['location']);
		$row = get_employee_through_dept_id_new($dept_id,$divison,$location,$f_year['id']);

		$emp_acc_dept = get_employees_acc_dept($dept_id);
		$_POST['count'] =  $emp_acc_dept;
		$k = 0; //row colour counter
		$get_gl_id=get_gl_id_frm_payroll($dept_id, $id_month);
		$gl_ref_no=get_reference_through_id($get_gl_id);
		echo "<center><h4>$gl_ref_no</h4></center>";
		start_outer_table(TABLESTYLE2);
		table_section(1);

		echo
		"<tr><td colspan='' class='tableheader'> &nbsp; Employee Code &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Employee &nbsp;</td>
    	<td colspan='' class='tableheader'>&nbsp; Designation &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Basic Salary &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Rate&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Amount&nbsp;</td>
       <td colspan='' class='tableheader'>&nbsp; Man Month &nbsp;</td>	
   		<td colspan='' class='tableheader'>&nbsp; Salary as Man Month &nbsp;</td>	
	    <td colspan='' class='tableheader'>&nbsp; EOBI &nbsp;</td>
		<!--<td colspan='' class='tableheader'>&nbsp; Employee CPF &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Employer CPF &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Duty Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Leave Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Deductible Days &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Deduction &nbsp;</td>-->
		<td colspan='' class='tableheader'>&nbsp; Advance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Allowance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Total Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Net Salary &nbsp;</td>
		</tr>
		";
		$year=date("Y");
		$a = 1;


		while($myrow_new = db_fetch($row))
		{
			$myrow = get_employee_through_dept_id2($myrow_new['employee_id']);

			$date11 = "1-".$id_month."-$year";
			$from_date = date2sql(begin_month($date11));
			$to_date = date2sql(end_month($date11));
			$payroll = payroll_detail($myrow['employee_id'], $id_month,$f_year['id']);

			$emp = employee_attendance_detail($myrow['employee_id'], $id_month); //presence table
			$presentinfo = employee_present_attendance_detail($myrow['employee_id'],$myrow['project'],$from_date,$to_date);
			$leaveinfo = employee_leave_attendance_detail($myrow['employee_id'],$from_date,$to_date);
			$overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow['project'],$from_date,$to_date);
			$trans=get_payroll_through_dept_id($myrow['project']);
			$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$from_date,$to_date);
			$working_days = get_sys_pay_pref('total_working_days');
			$per_day_salary = ($myrow['basic_salary']/ $working_days);

			$emp_man_month_value = get_project_wise_salary($myrow['employee_id'],$_POST['f_year'],$myrow['project'],$id_month);
			$project_wise_salary = $emp_man_month_value * $working_days * $per_day_salary;
			$month_days = get_month_days($id_month);

			$trans_=get_payroll_through_trans_no($myrow['employee_id']);
			echo "<tr>";
			alt_table_row_color($k);
			label_cell($myrow['emp_code'], "", $myrow['emp_code']);
			label_cell($myrow['emp_name'], "", $myrow['emp_name']);
			label_cell( get_designation_name($myrow['emp_desig']));
			label_cell( $myrow['basic_salary']);

			$annual_salary = $myrow['basic_salary'] * 12;
			if($annual_salary > 400000)
			{
				if($annual_salary > 400000 && $annual_salary <  750000)
				{
					$tax_rate = 5;
				}
				if($annual_salary > 750000 && $annual_salary <  1500000)
				{
					$tax_rate = 10;
				}
				if($annual_salary > 1500000 && $annual_salary <  2000000)
				{
					$tax_rate = 15;
				}
				if($annual_salary > 2000000 && $annual_salary <  2500000)
				{
					$tax_rate = 17.50;
				}
				if($annual_salary > 2200000)
				{
					$tax_rate = 20;
				}
				$annual_taxable_salary = $annual_salary  - 400000;
				$annual_tax = $annual_taxable_salary * ($tax_rate/100);
				$monthly_tax = $annual_tax / 12;
				label_cell( $tax_rate  . "%"); // tax rate
			}
			else
			{
				label_cell("0%"); // tax rate
			}

//		text_cells( null,'tax'.$a, $payroll['tax'] ? $payroll['tax'] : 0 , 20, 60, false,'','','', 'Tax');
			label_cell($payroll['tax']);
			label_cell($payroll['man_month_value']);
			label_cell($payroll['project_wise_salary']);
			label_cell(get_sys_pay_pref('eobi'));
			/*label_cell($payroll['emp_cpf']);
			label_cell($myrow['duty_hours']);*/

//			label_cell($overtimeinfo); // overtime hours
//		text_cells( null,'overtime'.$a, $payroll['overtime']? $payroll['overtime'] : 0, 20, 60, false,'','','', 'Over Time');
//			label_cell($payroll['overtime']);
//			label_cell($presentinfo);

//			label_cell($leaveinfo);
//			if($presentinfo>=$leaveinfo)
//			{
//				$diff_absentleave=$presentinfo-$leaveinfo;
//				label_cell( $diff_absentleave);
//			}

//		text_cells( null,'deduction'.$a, $payroll['late_deduction']? $payroll['late_deduction'] : 0, 20, 60, false,'','','', 'Deduction');
//			label_cell($payroll['late_deduction']);
			label_cell($payroll['advance']);



//		text_cells( null,'adv_deduction'.$a, $payroll['advance_deduction']? $payroll['advance_deduction'] : 0, 20, 60, false,'','','', 'Advance Deduction');
			label_cell($payroll['advance_deduction']);
			label_cell($payroll['allowance']);
			label_cell($payroll['deduction']);

			label_cell($payroll['total_deduction']);
			label_cell($payroll['net_salary']);

			//20august
			$trans_no=$payroll['trans_no'];

//			employee_allowance_cell($trans_no,$payroll['allowance']);
//			employee_deduction_cell($trans_no,$payroll['deduction']);

			//label_cell($payroll['deduction']);




			$payroll_head=$payroll['payroll_head'];


			$duty_hours +=$myrow['duty_hours'];
			$totalabsentday +=$diff_absentleave;
			$totalleaveinfo +=$leaveinfo;

			$total_basic_salary += $myrow['basic_salary'];
			$total_tax += $payroll['tax'];
			$total_overtime +=  $payroll['overtime'];
			$total_late_deduction +=  $payroll['late_deduction'];
			$total_adv_deduction += $payroll['advance_deduction'];
			$net_sal += $payroll['net_salary'];
			$total_deduction += $payroll['total_deduction'];

			$netpayable = $total_basic_salary - $total_tax + $total_overtime - $total_late_deduction - $total_adv_deduction;


			hidden('emp_id'.$a, $myrow['employee_id']);
			hidden('project'.$a, $dept_id);
			hidden('sal'.$a, $myrow['basic_salary']);
			hidden('month_id_2'.$a, $id_month);
			hidden('trans_no'.$a, $trans_no);
			hidden('payroll_head', $payroll_head);
			hidden('count', $a);
			$a++;
			echo "</tr>";

		}//while
		end_row();
		start_row("class='inquirybg' style='font-weight:bold'");
		label_cell("<span style='font-weight:bold'>TOTAL</span>");
		label_cell('');
		label_cell('');
		label_cell("<span style='font-weight:bold'>$total_basic_salary</span>");
		label_cell('');
		label_cell("<span style='font-weight:bold'>$total_tax</span>");
		label_cell("<span style='font-weight:bold'></span>");
		label_cell('');
		label_cell("<span style='font-weight:bold'></span>");
		label_cell("<span style='font-weight:bold'></span>" );
		label_cell("<span style='font-weight:bold'></span>");
		label_cell('');
		label_cell('');
		label_cell("<span style='font-weight:bold'>$total_deduction</span>" );

		label_cell("<span style='font-weight:bold'>$net_sal</span>");
		end_row();
		end_outer_table(1);
		div_start('controls');
//	submit_center_first('update', _("Update"), '', 'default', true);
		submit_center_first('delete', _("Delete"), '', '', true);
//	submit_center_last('delete', _("Delete"), '', '', true);
		div_end();
	}


}
//echo $trans_no;
//functions end
$selected_id = get_post('project','');
$divison = get_post('division','');
$location = get_post('location','');

$emp_id=get_post('emp_id','');
$month_id = get_post('month','');
$date = get_post('date','');
$f_year = get_post('f_year','');


echo $date;

if (isset($_POST['insert']))
{
	if(can_process())
	{

		$check = check_payroll_duplication($selected_id,$month_id);
		if($check > 0)
		{
			display_error(_("Aleardy Inserted"));
			set_focus('project');
		}else
		{
			handle_submit($selected_id, $month_id,$date);
		}
	}//if(can_process())

}

if (isset($_POST['update']))
{
	if(can_process())
	{

		handle_update($selected_id, $month_id);

	}

}

if (isset($_POST['delete']))
{
	if(can_process())
	{

		handle_delete($selected_id, $month_id,$_POST['emp_id'],$_POST['trans_no']);

	}

}

//--------------------------------------------------------------------------------------------
start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
/*dimensions_list_cells( _("Project:"), 'emp_dept', null,  _("Select department: "),false, 1);
dimensions_list_cells(_("Divison")." 1", 'divison', null, true, " ", false, 2);
dimensions_list_cells(_("Location")." 1", 'location', null, true, " ", false, 3);*/

dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

month_list_cells( null, 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
date_cells(_("Date:"), 'date' , '');
$f_year = get_current_fiscalyear();
//fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
hidden('f_year', $f_year['id']);

if($month_id)
{ label_cell(get_month_days($month_id) . " " . _('Days')); }
end_row();
end_table();

if (get_post('_show_inactive_update')) {
	$Ajax->activate('project');
	set_focus('project');
}


/*if(!$selected_id)
{
	set_focus('project');

}
else*/
{
	if(!$month_id)
		set_focus('month');
	else
		customer_settings($selected_id, $month_id,$trans_no,$date,$divison,$f_year,$_POST['location']);
}
br();
hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>