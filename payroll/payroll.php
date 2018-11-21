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
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include($path_to_root . "/payroll/includes/db/division_wise_gl_db.inc");
///////
 //include($path_to_root . "/payroll/inquiry/attendance_inquiry.php");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

add_js_file('login.js');
page(_($help_context = "Payroll Entry"), @$_REQUEST['popup'], false, "", $js);



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

	$f_year = get_current_fiscalyear();
	$tot_eobi=0;
	for($i = 1; $i <= $_POST['count']; $i++)
	{
		add_payroll($_POST['sal'.$i], $_POST['tax'.$i], $_POST['tax_rate_2'.$i], $_POST['overtime'.$i], $_POST['deduction'.$i],
			$_POST['adv_deduction'.$i], $_POST['emp_id'.$i], $_POST['emp_dept'], $_POST['month_id_2'],
			$date,$max_trans_head,$_POST['over_time_hour'.$i],$_POST['absent_days_2'.$i],
			$_POST['leave_days_2'.$i],$_POST['advance_2'.$i],$_POST['allowance'.$i],
			$_POST['deduction'.$i],$_POST['emp_cpf'.$i],$_POST['employer_cpf'.$i],$f_year['id'],
			$_POST['division'],$_POST['location'],$_POST['project'],$_POST['man_month_value'.$i],
			$_POST['project_wise_salary'.$i],$_POST['eobi'.$i],$_POST['total_deduction'.$i],$_POST['net_salary'.$i],$_POST['arrer'.$i]);

		$max_trans=get_payroll_max_trans_no();

		$all_employees[] =  $_POST['emp_id'.$i];

		if( $max_trans==0)
		{
			add_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i], 1,$_POST['remarks'.$i], 1,$i);
			if($_POST['arrer'.$i]!=0)
			{

				add_emp_increament_new($_POST['increment_code'],$_POST['emp_id'.$i],$_POST['increment_date'.$i],
					$_POST['date'],$_POST['arrer'.$i]/$_POST['inc_month'.$i],$_POST['sal'.$i],'',$f_year['id'],$_POST['division'],$_POST['project'],$_POST['location']);
			}

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
			if($_POST['arrer'.$i]!=0)
			{
				add_emp_increament_new($_POST['increment_code'],$_POST['emp_id'.$i],$_POST['increment_date'.$i],
					$_POST['date'],$_POST['arrer'.$i]/$_POST['inc_month'.$i],$_POST['sal'.$i],'',$f_year['id'],$_POST['division'],$_POST['project'],$_POST['location']);
			}
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

            $salary += $_POST['project_wise_salary'.$i];
            $allowance += $_POST['allowance'.$i];
            $overtime += $_POST['overtime'.$i];
            $arrer += $_POST['arrer'.$i];

            $tax += $_POST['tax'.$i];
            $c_deduction += $_POST['deduction'.$i];
            $l_deduction += round($_POST['emp_cpf'.$i]);
            $a_deduction += round($_POST['employer_cpf'.$i]);
            $adv_deduction += $_POST['adv_deduction'.$i];

	}

	$month_name=get_month_name($month_id );
	$dept_name=get_emp_dept_name($selected_id );

    $toal_exp=$salary+$allowance+$overtime+$arrer;
    $total_lab=$salary+$allowance+$overtime+$arrer-$tax-$c_deduction-$l_deduction-$a_deduction-$adv_deduction;

//display_error($salary."-".$allowance."-".$overtime."-".$arrer."-".$tax."-"//.$c_deduction."-".$l_deduction."-".$a_deduction."-".$adv_deduction);
	$f_year = get_current_fiscalyear();

	if($month_id==1 ||$month_id==2 || $month_id==3 || $month_id==4 || $month_id==5 || $month_id==6)
	{
		$year = date('Y', strtotime($f_year['end']));
	}
	else
	{
		$year = date('Y', strtotime($f_year['end']));
	}

	{
        global $Refs;
        $ref = $Refs->get_next(ST_JOURNAL);
        $payroll_expenses_account = get_payroll_expenses_account($_POST['division'],$_POST['project'],$_POST['location']);
        $payroll_alloance_expenses_account = get_payroll_bonus_account($_POST['division'],$_POST['project'],$_POST['location']);
        $payroll_dedeuction_account = get_payroll_deduction_account($_POST['division'],$_POST['project'],$_POST['location']);
        $payroll_liabilty_account = get_payroll_liabilty_account($_POST['division'],$_POST['project'],$_POST['location']);
        $tax_liability_account = get_tax_liability_account($_POST['division'],$_POST['project'],$_POST['location']);
        $eobi_account = get_eobi_liability_account($_POST['division'],$_POST['project'],$_POST['location']);
        $advance_receivable_account = get_advance_receivable_account($_POST['division'],$_POST['project'],$_POST['location']);
        $loan_account = get_loan_account($_POST['division'],$_POST['project'],$_POST['location']);
        $salary_account = get_salary_account($_POST['division'],$_POST['project'],$_POST['location']);
        $payment = get_payment($_POST['division'],$_POST['project'],$_POST['location']);
        $advance_account = get_advance_account($_POST['division'],$_POST['project'],$_POST['location']);

        $memo1 = "Payroll entry of ".get_accounts_name_through_code($salary_account)." for the month of $month_name - $year.";
        $memo2 = "Payroll entry of ".get_accounts_name_through_code($payroll_alloance_expenses_account)." for the month of $month_name - $year.";
        $memo3 = "Payroll entry of ".get_accounts_name_through_code($payroll_expenses_account)." for the month of $month_name - $year.";
        $memo4 = "Payroll entry of ".get_accounts_name_through_code($loan_account)." for the month of $month_name - $year.";
        $memo5 = "Payroll entry of ".get_accounts_name_through_code($tax_liability_account)." for the month of $month_name - $year.";
        $memo6 = "Payroll entry of ".get_accounts_name_through_code($eobi_account)." for the month of $month_name - $year.";
        $memo7 = "Payroll entry of ".get_accounts_name_through_code($payroll_dedeuction_account)." for the month of $month_name - $year.";
        $memo8 = "Payroll entry of ".get_accounts_name_through_code($advance_receivable_account)." for the month of $month_name - $year.";
        $memo9 = "Payroll entry of ".get_accounts_name_through_code($payroll_liabilty_account)." for the month of $month_name - $year.";
        $memo10 = "Payroll entry of ".get_accounts_name_through_code($payment)." for the month of $month_name - $year.";
        // $memo11 = "Payroll entry of ".get_accounts_name_through_code($advance_account)." for the month of $month_name - $year.";

        $id = get_next_trans_no(ST_JOURNAL);
        if($salary!=0)
         add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $salary_account,$_POST['division'],$_POST['project'], $memo1, $salary);
         if($allowance!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_alloance_expenses_account,$_POST['division'], $_POST['project'], $memo2, $allowance);
         if($overtime!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_expenses_account,$_POST['division'], $_POST['project'], $memo3, $overtime);
        if($arrer!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $loan_account,$_POST['division'],$_POST['project'], $memo4, $arrer);
        if($tax!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $tax_liability_account,$_POST['division'],$_POST['project'], $memo5, -$tax);
        if($c_deduction!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $eobi_account,$_POST['division'], $_POST['project'],$memo6, -$c_deduction);
        if($l_deduction!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_dedeuction_account,$_POST['division'],$_POST['project'], $memo7, -$l_deduction);
        if($adv_deduction!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $advance_receivable_account,$_POST['division'],'', $memo8, -$adv_deduction);
        if($a_deduction!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payment,$_POST['division'],$_POST['project'], $memo10, -$a_deduction);
        if($total_lab!=0)
        add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_liabilty_account,$_POST['division'],$_POST['project'], $memo9, -$total_lab);
        add_journal(ST_JOURNAL, $id, $toal_exp, $_POST['date'] , get_company_currency(), $ref, '', 1, $_POST['date'], $_POST['date']);
        $Refs->save(ST_JOURNAL, $id, $ref);
		add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
		add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);

	}



////////for every employee wise jv

// add_journal(ST_JOURNAL, $id, $toal_exp, $_POST['date'] , get_company_currency(), $ref, '', 1, $_POST['date'], $_POST['date']);
//	$Refs->save(ST_JOURNAL, $id, $ref);
//	foreach ($all_employees as $all_employee_id) {
//		if ($all_employee_id != '') {
//			write_journal_entries_new($all_employee_id, $basic_salary, $_POST['date'], $use_transaction = true, $month_id, $f_year['id'],$toal_exp,$total_sal,$tax,$eobi,$adv_deduction);
//		}
//	}
	//add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
	//add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
	//$Refs->save(ST_JOURNAL, $id, $ref);

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
		$row = get_employee_through_dept_id_new($dept_id,$divison,$location,$f_year['id'],$id_month);//999999999

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
		<td colspan='' class='tableheader'>&nbsp; Allowance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; OT Hours&nbsp;</td>
       <td colspan='' class='tableheader'>&nbsp; OT Amount &nbsp;</td>
        <td colspan='' class='tableheader'>&nbsp; Arrear (If Any) &nbsp;</td>	
		<td colspan='' class='tableheader'>&nbsp; Late Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Late Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Days &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Amount &nbsp;</td>		
		<td colspan='' class='tableheader'>&nbsp; Tax Rate&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Amount&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Total Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Total Allowance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Net Salary &nbsp;</td>
		</tr>
		";
		$year1 = date('Y', strtotime($f_year['end']));
		$year2 = date('Y', strtotime($f_year['begin']));
		$a = 1;
		while($myrow_new = db_fetch($row))
		{
			$myrow = get_employee_through_dept_id2($myrow_new['empl_id']);
			$m=$id_month;

			$date11 = "$year1-".$id_month."-01";
			if($id_month==1 ||$id_month==2 || $id_month==3 || $id_month==4 || $id_month==5 || $id_month==6)
			{
				$date13 = "$year1-".$m."-30";
				$date14 = "$year1-".$m."-01";
			}
			else{
				$date13 = "$year2-".$m."-30";
				$date14 = "$year2-".$m."-01";
			}
			$from_date = date2sql(begin_month($date11));
			$to_date = date2sql(end_month($date11));
			$dateforleave = "1-".$id_month."-$yearforleave";
			$from_dateleave = date2sql(begin_month($dateforleave));
			$to_dateleave = date2sql(end_month($dateforleave));
			$emp = employee_attendance_detail($myrow['employee_id'], $id_month);
			$presentinfo = employee_present_attendance_detail($myrow['employee_id'],$myrow_new['project_name'],$from_date,$to_date);
			$leaveinfo = employee_leave_attendance_detail($myrow['employee_id'],$from_date,$to_date);
			$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$date11,$f_year['id']);

			$payroll = payroll_detail($myrow['employee_id'], $id_month);
			$overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow_new['project_name'],$from_date,$to_date);
			$trans_=get_payroll_through_trans_no($myrow['employee_id']);
//			$advance_pay=employee_advance_pay_detail($myrow['employee_id'],$payroll['trans_no'],$from_date,$to_date);
			$advance_pay=employee_advance_pay_detail($myrow['employee_id'],$payroll['trans_no'],$date11,$date11,$f_year['id'],$id_month);

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
			$valid_month=get_emplyee_incr_month($myrow['employee_id'],$f_year['id']);
			if($valid_month==$id_month)
			{
				$check_inc=0;
			}
			else{
				$check_inc=check_increment_applicable($myrow['employee_id'],$date14,$date13,$f_year['id'],$id_month,$valid_month,$_POST['division'],$_POST['project'],$_POST['location']);

			}
			$pre_tax=get_emplyee_pre_tax($myrow['employee_id'],$f_year['id'],$id_month);
			$pre_salary=get_emplyee_pre_salary($myrow['employee_id'],$f_year['id'],$id_month);
			$pre_salary_month=get_emplyee_pre_month($myrow['employee_id'],$f_year['id'],$id_month);

			$working_days = get_sys_pay_pref('total_working_days');

			$emp_man_month_value = get_project_wise_salary($myrow['employee_id'],$f_year['id'],$myrow_new['project_name'],$id_month);
			$month_tax=get_emplyee_tax_month($myrow['employee_id'],$date13);

			if($valid_month==$id_month)
			{
				$inc_month=1;
			}
			else{
				$inc_month=get_emplyee_incr_duration($myrow['employee_id'],$date13);
				$inc_month_tax=get_emplyee_incr_duration_for_tax($myrow['employee_id'],$f_year['id']);
				$arrer1=get_arrerr_value($myrow['employee_id'],$date13,$_POST['division'],$_POST['project'],$_POST['location'],$f_year['id']);
				$arrer_tax=get_arrerr_value_for_tax($myrow['employee_id'],$date13,$f_year['id']);//yhe is liay ky agr band ak sy ziada project m involve hy
			}
			if($check_inc<1 )
			{
				label_cell( $myrow['basic_salary']);
				$salary_amount=$myrow['basic_salary'];//agr koi issue ay tu yahan yahan $salary_amount likha hy wahan wahan $myrow['basic_salary'] ker dian phr set ho jyga
				$project_wise_salary = $salary_amount;
			}
			else
			{
				label_cell( $myrow['prev_salary']);
				$salary_amount=$myrow['prev_salary'];
				$project_wise_salary =  $salary_amount;
			}
            $employee_id=$myrow['employee_id'];
            $employee_allowances = $employee_allowances ;
            employee_emp_allowance_cell($employee_id,$employee_allowances);
			$pre_tax_m=get_pre_tax_month($myrow['employee_id'],$f_year['id']);
			if($inc_month>0 )
			{
				$arrer=$arrer1*($inc_month);
			}
			else{
				$arrer=0;
			}
            $ot_hours=get_employee_ot_hours($id_month,$myrow['employee_id'],$f_year['id'],$myrow['emp_grade']);
            $ot_hours_cal=get_employee_ot_hours_calculation($id_month,$myrow['employee_id'],$f_year['id'],$myrow['emp_grade']);
			$duty_hours=get_employee_duty_hour($myrow['employee_id']);
		//	display_error($duty_hours);
            $t_h_time_amount=round($ot_hours_cal*($myrow['basic_salary']/$month_days)/$duty_hours);
            label_cell($ot_hours);
            text_cells( null,'overtime'.$a,$t_h_time_amount, 5, 20, false,'','','', 'OT Amount');
            if($arrer!=''){
                text_cells( null,'arrer'.$a, $arrer, 10, 20, false,'','','', 'Arrer');
            }
            else{
                text_cells( null,'arrer'.$a,0, 10, 20, false,'','','', 'Arrer');
            }
            $total_zero_days=get_employee_absent_days($myrow['employee_id'],$id_month,$f_year['id']);
            $total_off_days=get_employee_off_days($myrow['employee_id'],$id_month,$f_year['id']);
            $total_gazzet=get_gazzet_holidays_days($id_month);
            $total_emp_leave_days=get_emp_t_leave_days($myrow['employee_id'],$id_month,$f_year['id']);
            $absent_days=($total_zero_days-($total_off_days+$total_gazzet+$total_emp_leave_days));
            $l_ded=get_employee_deduction_days($id_month,$myrow['employee_id']);
			$h_d_ded=get_employee_ded_h_days($id_month,$myrow['employee_id']);
            $t_ded=$l_ded+$h_d_ded;
            text_cells( null,'man_month_value'.$a, $t_ded, 5, 20, false,'','','', 'Late Deduction');
//            if($_POST['man_month_value'.$a]!=0)
//            $t_late_deduction =round($_POST['man_month_value'.$a]*($myrow['basic_salary']/$working_days));
//            else
            $t_late_deduction =round($t_ded*($myrow['basic_salary']/$working_days));
            text_cells( null,'emp_cpf'.$a, $t_late_deduction, 8, 20, false,'','','', 'Late Deduction');


           // label_cell($t_late_deduction);
            $t_absent_deduction=round($absent_days*($myrow['basic_salary']/$month_days));
            label_cell($absent_days);
            text_cells( null,'employer_cpf'.$a, $t_absent_deduction, 5, 20, false,'','','', 'Absent Deduction Amount');

			if($inc_month>0)
			{
				$annual_salary1 = ($salary_amount) * (12-$pre_salary_month);
				$annual_salary=($annual_salary1+($arrer_tax*$inc_month_tax)+$pre_salary);
			}
			else{
				$annual_salary1 = ($salary_amount) * (12-$pre_salary_month);
				$annual_salary=($annual_salary1+($arrer_tax*$inc_month_tax)+$pre_salary);
			}
			$tax_rate=get_tax_amount_empl_vise($annual_salary);
			$min_amount=get_tax_min_amount_empl_vise($annual_salary);
			$fix_amount=get_tax_fix_amount_empl_vise($annual_salary);
			if($myrow['tax_deduction']==1 && $myrow['mb_flag']=='N')
			{
				if($tax_rate > 0)
				{
					$workable_m=get_emplyee_work_duration($myrow['employee_id']);


					if($workable_m<12)
					{
						if($inc_month>0)
						{
							$t_month=$workable_m;
						}
						else
						{
							$t_month=$workable_m;
						}
						$annual_salary1 = ($salary_amount) * ($t_month);
						$tax_rate1=get_tax_amount_empl_vise($annual_salary1);
						$min_amount1=get_tax_min_amount_empl_vise($annual_salary1);
						$fix_amount1=get_tax_fix_amount_empl_vise($annual_salary1);
						$paid_tax = (((((($salary_amount-$arrer1)*($t_month)-$min_amount1)*$tax_rate1/100)+$fix_amount1))/$t_month)*$inc_month;
						$monthly_tax = ((((($salary_amount*($t_month)-$min_amount1)*$tax_rate1/100)+$fix_amount1))-$paid_tax)/($t_month-$inc_month);
						label_cell( $tax_rate1  . "%"); // tax rate

					}
					else{
						if($inc_month>0)
						{

							if($valid_month==$id_month)
								$annual_salary2 = ((($salary_amount*($month_tax)+$myrow['prev_salary']*(12-$month_tax))));
							else
							$annual_salary2 = ((($salary_amount*($month_tax)+($arrer_tax*$inc_month_tax)+$pre_salary)));//yhe september k liay kiya hy
							$tax_rate2=get_tax_amount_empl_vise($annual_salary2);
							$min_amount2=get_tax_min_amount_empl_vise($annual_salary2);
							$fix_amount2=get_tax_fix_amount_empl_vise($annual_salary2);
							$monthly_tax = (((($annual_salary2-$min_amount2)*$tax_rate2/100)+$fix_amount2)-$pre_tax)/($month_tax);
							label_cell( $tax_rate2  . "%"); // tax rate
							$tax_rate=$tax_rate2;

						}
						else{
							$monthly_tax = (((($annual_salary-$min_amount)*$tax_rate/100)+$fix_amount)-$pre_tax)/(12-$pre_salary_month);
							label_cell( $tax_rate  . "%"); // tax rate
						}
					}

					$monthly_tax = $monthly_tax ;
					text_cells( null,'tax'.$a, round2($monthly_tax ), 10, 60, false,'','','', 'Tax');
				}
				else{
					label_cell("0%"); // tax rate
					text_cells( null,'taxaaa'.$a, 0, 10, 60, false,'','','', 'Tax');
				}
			}
			elseif($myrow['tax_deduction']==1 && $myrow['mb_flag']=='S')
			{
				if($myrow['text_filer']==1)
				{
					$tax_rate = get_sys_pay_pref('filer');
					$monthly_tax = ((($myrow['basic_salary'])*$tax_rate/100));
					label_cell( $tax_rate  . "%"); // tax rate
					$monthly_tax1 = $monthly_tax  ;
					text_cells( null,'tax'.$a, round2($monthly_tax1 ), 10, 60, false,'','','', 'Tax');
				}
				else
				{
					$tax_rate = get_sys_pay_pref('non_filer');
					$monthly_tax = ((($myrow['basic_salary'])*$tax_rate/100));
					label_cell( $tax_rate  . "%"); // tax rate
					$monthly_tax1 = $monthly_tax ;
					text_cells( null,'tax'.$a, round2($monthly_tax1 ), 10, 60, false,'','','', 'Tax');
				}
			}
			else
			{
				label_cell("0%"); // tax rate
				text_cells( null,'taxaaa'.$a, 0, 10, 60, false,'','','', 'Tax');
			}

			label_cell( round2($advanceinfo));
			if($advanceinfo>0)
			{
			    $advance_pay1 = $advance_pay ;
				text_cells( null,'adv_deduction'.$a, round($advance_pay1), 10, 60, false,'','','', 'Advance    Deduction');
			}
			else
			{
				text_cells( null,'adv_deduction'.$a, 0, 10, 60, false,'','','', 'Advance    Deduction');
			}
			$employee_deduction = $employee_deduction ;

			employee_emp_deduction_cell($employee_id,$employee_deduction);
				$total_deduction = 	    $advance_pay + $monthly_tax + $employee_deduction+$t_late_deduction+$t_absent_deduction ;
				$total_alloawance=round($employee_allowances+$arrer+$t_h_time_amount);
			label_cell(round($total_deduction));//total deduction
            label_cell($total_alloawance);//total deduction
			label_cell(round2(($project_wise_salary+$total_alloawance)-$total_deduction));//net salary
			$gl_id = get_next_trans_no(ST_JOURNAL);
			hidden('emp_id'.$a, $myrow['employee_id']);
			hidden('total_deduction'.$a,$total_deduction);
			$net_salary  =$employee_allowances + $project_wise_salary - $total_deduction;
			hidden('net_salary'.$a, $net_salary);
			hidden('project', $dept_id);
			hidden('division', $divison);
			hidden('location', $location);
			hidden('f_year', $f_year);
			hidden('adv_deduction'.$a,($advance_pay));
			hidden('sal'.$a, $myrow['basic_salary']);
			hidden('month_id_2', $id_month);
			hidden('date', $date);
          //  hidden('emp_cpf', $t_late_deduction);

			hidden('trans_no'.$a,$payroll['trans_no']);
			hidden('absent_days_2'.$a,$presentinfo);
			hidden('advance_2'.$a,$advanceinfo);
			hidden('leave_days_2'.$a,$leaveinfo);
			hidden('allowance'.$a,$employee_allowances);
			hidden('deduction'.$a,$employee_deduction);
			hidden('increment_date'.$a,$date13);
			hidden('inc_month'.$a,$inc_month);

			if($workable_m<12)
			{hidden('tax_rate_2'.$a,$tax_rate1);}
			else
			{hidden('tax_rate_2'.$a,$tax_rate);}

			hidden('project_wise_salary'.$a,$project_wise_salary);
			//hidden('man_month_value'.$a,$emp_man_month_value);
			if($valid_month==$id_month)
			{
				hidden('arrer'.$a,$project_wise_salary-$myrow['prev_salary']);
			}
			else{
				hidden('arrer'.$a,$arrer);
			}


			$monthly_tax=0;

			hidden('gl_id',$gl_id);
			hidden('count', $a);

			$total_eobi +=$eobi;
			$a++;
			echo "</tr>";
		}

		hidden('total_eobi', $total_eobi);
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
		$row = get_employee_through_dept_id_new($dept_id,$divison,$location,$f_year['id'],$id_month);

		$emp_acc_dept = get_employees_acc_dept($dept_id);
		$_POST['count'] =  $emp_acc_dept;
		$k = 0; //row colour counter
		$get_gl_id=get_gl_id_frm_payroll($dept_id, $id_month);
		$gl_ref_no=get_reference_through_id(1104);
		echo "<center><h4>$gl_ref_no</h4></center>";
		start_outer_table(TABLESTYLE2);
		table_section(1);

		echo
		"<tr><td colspan='' class='tableheader'> &nbsp; Employee Code &nbsp; </td>
		<td colspan='' class='tableheader'>&nbsp;Employee &nbsp;</td>
    	<td colspan='' class='tableheader'>&nbsp; Designation &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Basic Salary &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; OT Amount &nbsp;</td>	
		 <td colspan='' class='tableheader'>&nbsp; Arrer &nbsp;</td>
   		 <td colspan='' class='tableheader'>&nbsp; Late Amount &nbsp;</td>
   		  <td colspan='' class='tableheader'>&nbsp; Absent Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Rate&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Amount&nbsp;</td>
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
			$myrow = get_employee_through_dept_id2($myrow_new['empl_id']);

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
			label_cell($payroll['overtime']);
			label_cell($payroll['arrer']);
			label_cell($payroll['emp_cpf']);
			label_cell($payroll['employer_cpf']);
			label_cell($payroll['tax_rate']." %");
			label_cell($payroll['tax']);
			label_cell($payroll['advance']);
			label_cell($payroll['advance_deduction']);
			label_cell($payroll['allowance']);
			label_cell($payroll['deduction']);
			label_cell($payroll['total_deduction']);
			label_cell($payroll['net_salary']+$payroll['overtime']);
	
		
			
			

		

		

			//20august
			$trans_no=$payroll['trans_no'];
			$payroll_head=$payroll['payroll_head'];
			$t_project_salary +=$payroll['project_wise_salary'];
			$duty_hours +=$myrow['duty_hours'];
			$totalabsentday +=$diff_absentleave;
			$totalleaveinfo +=$leaveinfo;
			$t_advance +=$payroll['advance'];
			$t_eobi +=$payroll['eobi'];
			$t_arrer +=$payroll['arrer'];
			$t_l_deduction +=$payroll['emp_cpf'];
			$t_a_deduction +=$payroll['employer_cpf'];
			$t_allowance +=$payroll['allowance'];
$t_deduction +=$payroll['deduction'];
			$total_basic_salary += $myrow['basic_salary'];
			$total_tax += $payroll['tax'];
			$total_overtime +=  $payroll['overtime'];
			$total_late_deduction +=  $payroll['late_deduction'];
			$total_adv_deduction += $payroll['advance_deduction'];
			$net_sal += ($payroll['net_salary']+$payroll['overtime']);
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
		label_cell("<span style='font-weight:bold'>$total_overtime</span>");
		label_cell("<span style='font-weight:bold'>$t_arrer</span>");
		label_cell("<span style='font-weight:bold'>$t_l_deduction</span>");
		label_cell("<span style='font-weight:bold'>$t_a_deduction</span>");
		label_cell('');
		label_cell("<span style='font-weight:bold'>$total_tax</span>");
		label_cell("<span style='font-weight:bold'>$t_advance</span>");
		label_cell("<span style='font-weight:bold'>$total_adv_deduction</span>");
		label_cell("<span style='font-weight:bold'>$t_allowance</span>");
		label_cell("<span style='font-weight:bold'>$t_deduction</span>");
		label_cell("<span style='font-weight:bold'>$total_deduction</span>" );
		label_cell("<span style='font-weight:bold'>$net_sal</span>");
		end_row();
		end_outer_table(1);
		div_start('controls');
		submit_center_first('delete', _("Delete"), '', '','waqar');
		div_end();
	}
}
$selected_id = get_post('project','');
$divison = get_post('division','');
$location = get_post('location','');
$emp_id=get_post('emp_id','');
$month_id = get_post('month','');
$date = get_post('date','');
$f_year = get_post('f_year','');




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