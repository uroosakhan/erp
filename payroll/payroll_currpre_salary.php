<?php
$page_security = 'SA_OPEN';
$path_to_root = "..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/payroll/includes/db/attendance_db.inc"); 
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); 
include_once($path_to_root . "/payroll/includes/db/payroll_db.inc"); 
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc"); 
include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

add_js_file('login.js');	
page(_($help_context = "Payroll Entry-New"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

//functions

function get_previous_salary($month_name, $dept, $employee,$fiscal_year)
{
	$sql = "SELECT ".TB_PREF."payroll.* FROM `".TB_PREF."payroll`,".TB_PREF."payroll_head
    WHERE ".TB_PREF."payroll_head.trans_no=".TB_PREF."payroll.`payroll_head`";
    //if ($month_name != ALL_TEXT)
    //$sql .= "AND ".TB_PREF."payroll_head.month_id =".db_escape($month_name);


	if($month_name==1)
	{
		$previous_month = 12;
		$sql .= " AND ".TB_PREF."payroll_head.month_id =".db_escape($previous_month);
	}
	elseif($month_name!=1){
		$previous_month = $month_name -1 ;
		$sql .= " AND ".TB_PREF."payroll_head.month_id =".db_escape($previous_month);
	}

	if ($dept != ALL_TEXT)
		$sql .= "AND ".TB_PREF."payroll_head.dept_id =".db_escape($dept);

	if ($employee != ALL_TEXT)
		$sql .= " AND ".TB_PREF."payroll.emp_id =".db_escape($employee);

		$sql .= " AND ".TB_PREF."payroll.f_year=".db_escape($fiscal_year);

	$TransResult = db_query($sql,"No transactions were returned");

	return db_fetch($TransResult);
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
	
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
		add_payroll($_POST['sal'.$i], $_POST['tax'.$i], $_POST['tax_rate_2'.$i], $_POST['overtime'.$i],
		$_POST['late_deduction'.$i], $_POST['adv_deduction'.$i], $_POST['emp_id'.$i], $_POST['project'],
	    $_POST['month_id_2'], $date,$max_trans_head,$_POST['over_time_hour'.$i],$_POST['absent_days_2'.$i],
		$_POST['leave_days_2'.$i],$_POST['advance_2'.$i],$_POST['allowance'.$i],$_POST['deduction'.$i],
		$_POST['emp_cpf'.$i],$_POST['employer_cpf'.$i],$_POST['f_year']);
		$max_trans=get_payroll_max_trans_no();

		
		if( $max_trans==0)
			{

			add_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i], 1,$_POST['remarks'.$i],
				1,$i, $_POST['advance_2'.$i] - $_POST['adv_deduction'.$i],0, $_POST['f_year']);
				
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
//			$last_advance_deduction = get_last_advance_deduction($_POST['date'], $_POST['emp_id'.$i],$max_trans);
			
			add_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i], 1,
				$_POST['remarks'.$i], 1,$max_trans,0,0, $_POST['f_year']);

//			update_advance_new($last_advance_deduction,$_POST['emp_id'.$i]);

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
		$tax += $_POST['tax'.$i];
		$emp_cpf += $_POST['emp_cpf'.$i];
		$overtime += $_POST['overtime'.$i];
		$late_deduction += $_POST['late_deduction'.$i];
		$adv_deduction += $_POST['adv_deduction'.$i];

		$allowance += $_POST['allowance'.$i];
		$deduction += $_POST['deduction'.$i];
		$eobi += get_sys_pay_pref('eobi_liability');
		
		}
		
		$month_name=get_month_name($month_id );
        $dept_name=get_emp_dept_name($selected_id );
		

		$netexpense = $basic_salary + $overtime - $late_deduction + $allowance;		
		$netpayable = $basic_salary - $tax - $emp_cpf + $overtime - $late_deduction - $adv_deduction + $allowance - $deduction;	

		global $Refs;

			$id = get_next_trans_no(ST_JOURNAL);
			$ref = $Refs->get_next(ST_JOURNAL);
			
//			$stock_gl_code = get_stock_gl_code($stock_id);
			$memo = "Payroll entry of department $dept_name for the month of $month_name.";
			$payroll_expenses_account = get_sys_pay_pref('payroll_expenses');
			$payroll_liabilty_account = get_sys_pay_pref('payroll_liabilty');
			$tax_liability_account = get_sys_pay_pref('tax_liability');
			$eobi_account = get_sys_pay_pref('eobi_liability');
			$advance_receivable_account = get_sys_pay_pref('advance_receivable');
			
			
			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_expenses_account,'', '', $memo, $netexpense);
			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $eobi_account,'', '', $memo, - $deduction); //EOBI
			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $advance_receivable_account,'', '', $memo, -$adv_deduction); //Advance deduction
			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $payroll_liabilty_account,'','', $memo, -$netpayable);
			add_gl_trans(ST_JOURNAL, $id, $_POST['date'], $tax_liability_account,'','', $memo, -$tax);

							
			add_audit_trail(ST_JOURNAL, $id, $_POST['date']);
			add_comments(ST_JOURNAL, $id, $_POST['date'], $memo);
			$Refs->save(ST_JOURNAL, $id, $ref);	
			
	    display_notification(_("Payroll record for department $dept_name has been posted for the month of $month_name. JV Reference $ref. Total payable amount of $netpayable"));
		set_focus('emp_dept');		
}

function handle_update($selected_id, $month_id,$trans_no)
{
	    for($i = 1; $i <= $_POST['count']; $i++)
		{
		update_payroll($_POST['trans_no'.$i],$_POST['tax'.$i], $_POST['overtime'.$i], $_POST['deduction'.$i], $_POST['adv_deduction'.$i], $_POST['date'], $_POST['emp_id'.$i], $_POST['emp_dept_2'.$i], $_POST['month_id_2'.$i]);
		
	     update_advance($_POST['date'], $_POST['emp_id'.$i], $_POST['adv_deduction'.$i],$_POST['trans_no'.$i]);
		}
	    display_notification(_("Employee record has been updated."));
		
		set_focus('emp_dept');
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
function customer_settings($dept_id, $id_month,$trans_no,$date,$f_year,$divison,$location) 
{
	$check = get_emp_rec_no($dept_id, $id_month,$f_year);
	if($check == 0) 
	{

		echo "<center><h3>Insert New Record</h3></center>";	
		$row = get_employee_through_dept_id($dept_id,$divison,$location);
		
		
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
		<!--<td colspan='' class='tableheader'> Previous Salary </td>
		<td colspan='' class='tableheader'> Current Salary </td>-->
		
		
		
		</tr>
		";
		$year=date("Y");
		echo $presentinfo['basic_salary'];
		
		$a = 1;
		
		while($myrow = db_fetch($row))
		{
			$date11 = "1-".$id_month."-$year";
		    $from_date = date2sql(begin_month($date11));
			$to_date = date2sql(end_month($date11));
     //echo $to_date;
			//
			$dateforleave = "1-".$id_month."-$yearforleave";
			$from_dateleave = date2sql(begin_month($dateforleave));
			$to_dateleave = date2sql(end_month($dateforleave));
		$emp = employee_attendance_detail($myrow['employee_id'], $id_month);
		$absentdays = employee_present_attendance_detail($myrow['employee_id'],$myrow['project'],$id_month,$_POST['f_year']);
		//$presentinfo = employee_present_attendance_detail($myrow['employee_id'],$myrow['emp_dept'],$from_date,$to_date);
		$leaveinfo = employee_leave_attendance_detail($myrow['employee_id'],$from_date,$to_date,$_POST['f_year']);
			
		$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$to_date,$_POST['f_year']);
			
		$payroll = payroll_detail($myrow['employee_id'], $id_month,$_POST['f_year']);
       

         $overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow['project'],$id_month,$_POST['f_year']);
			
		$trans_=get_payroll_through_trans_no($myrow['employee_id']);
		$advance_pay=employee_advance_pay_detail($myrow['employee_id'],$payroll['trans_no'],$from_date,$to_date,$_POST['f_year']);
			
		$month_days = get_month_days($id_month);
			$working_days = get_sys_pay_pref('total_working_days');
		$per_day_salary = ($myrow['basic_salary']/ $working_days);

		$emp_man_month_value = get_project_wise_salary($myrow['employee_id'],$_POST['f_year'],$myrow['project'],$id_month);
		$project_wise_salary = $emp_man_month_value * $working_days * $per_day_salary;

		$overtime_amt= ( ($myrow['basic_salary']/$working_days) / ($myrow['duty_hours']) ) * 2 *$overtimeinfo;  //$emp[5] = overtime
		$employee_deduction=employee_deduction($myrow['employee_id']);
		$employee_allowances=employee_allowances($myrow['employee_id']);
		echo "<tr>";
		alt_table_row_color($k);
		label_cell( $myrow['emp_code'], "", $myrow['emp_code']);
		label_cell( $myrow['emp_name'], "", $myrow['emp_name']);
		label_cell( get_designation_name($myrow['emp_desig']));
//		label_cell( number_format2($myrow['basic_salary']));
//		label_cell($project_wise_salary);
		label_cell($myrow['basic_salary']);
		$annual_salary = $myrow['basic_salary'] * 12;
		if($annual_salary > 400000)
		{
			if($annual_salary > 400000 && $annual_salary <  750000)
			 {
			  $tax_rate = 5;
			  $fix_amount = 0;
			  $lower_limit = 400000;

			 } 
			if($annual_salary > 750000 && $annual_salary <  1400000)
			 {
			  $tax_rate = 10;
			  $fix_amount = 17500;
			  $lower_limit = 750000;
			 } 
			if($annual_salary > 1400000 && $annual_salary <  1500000)
			 {
			  $tax_rate = 12.5;
			  $fix_amount = 82500;
			  $lower_limit = 1400000;
			 } 
			if($annual_salary > 1500000 && $annual_salary < 1800000)
			 {
			  $tax_rate = 15;
			  $fix_amount = 95000;
			  $lower_limit = 1500000;
			 } 
			if($annual_salary > 1800000 && $annual_salary < 2500000)
			 {
			  $tax_rate = 17.5;
			  $fix_amount = 140000;
			  $lower_limit = 1800000;

			 } 
			if($annual_salary > 2500000 && $annual_salary = 3000001)
			 {
			  $tax_rate = 20;
			  $fix_amount = 262500;
			  $lower_limit = 2500000;

			 } 
			if($annual_salary > 3000000 && $annual_salary < 3500000)
			 {
			  $tax_rate = 22.5;
			  $fix_amount = 362500;
			  $lower_limit = 3000000;

			 } 
			if($annual_salary > 3500000 && $annual_salary < 4000000)
			 {
			  $tax_rate = 25;
			  $fix_amount = 475000;
			  $lower_limit = 3500000;

			 } 
			if($annual_salary > 4000000 && $annual_salary <  7000000)
			 {
			  $tax_rate = 27.5;
			  $fix_amount = 600000;
			  $lower_limit = 4000000;

			 } 

			if($annual_salary > 7000000)
			 {
			  $tax_rate = 30;
			  $fix_amount = 1425000;
			  $lower_limit = 7000000;
			 } 

		$annual_tax = $fix_amount + ( ($annual_salary - $lower_limit) * $tax_rate/100);
		//$annual_taxable_salary = $annual_salary  - 400000;
		//$annual_tax = $annual_taxable_salary * ($tax_rate/100);
		$monthly_tax = $annual_tax / 12;
		label_cell( $tax_rate  . "%"); // tax rate
		
		text_cells( null,'tax'.$a, round2($monthly_tax), 20, 60, false,'','','', 'Tax');
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

		//label_cell($calculate_emp_cpf);
		//label_cell($calculate_employer_cpf);
//		label_cell($myrow['duty_hours']); // duty hours
//		label_cell($overtimeinfo); // overtime hours
//		text_cells( null,'overtime'.$a, round($overtime_amt), 20, 60, false,'','','', 'Over Time');
//		label_cell($absentdays);
//      label_cell($leaveinfo);
		$trans_no=$payroll['trans_no'];
	/*	if($absentdays>=$leaveinfo)
		{
			$diff_absentleave=$absentdays-$leaveinfo;
			label_cell( $diff_absentleave);
		}
		else
		{
			label_cell( '');
		}*/
		//$late_deduction = $diff_absentleave*$per_day_salary;
		//text_cells( null,'late_deduction'.$a, round($late_deduction), 20, 60, false,'','','', 'Deduction');
		label_cell( round2($advanceinfo));
		if($advanceinfo>0)
		{
//		text_cells( null,'adv_deduction'.$a, round($advance_pay), 20, 60, false,'','','', 'Advance    Deduction');
			text_cells( null,'adv_deduction'.$a, round($advance_pay), 20, 60, false,'','','', 'Advance    Deduction');
               
		}
		else
		{
	    	text_cells( null,'adv_deduction'.$a, 0, 20, 60, false,'','','', 'Advance    Deduction');
		}
    
		$employee_id=$myrow['employee_id'];
$year = get_company_pref('f_year');
 $previous_salary = get_previous_salary($id_month,$dept_id,  $myrow['employee_id'],$year);
		employee_emp_allowance_cell($employee_id,$employee_allowances);
		employee_emp_deduction_cell($employee_id,$employee_deduction);
		$NetSalary = $myrow['basic_salary'] - $advance_pay - $late_deduction - $monthly_tax + $overtime_amt;
			$total_previuos_salary = $previous_salary['basic_salary'] -
			$previous_salary['advance_deduction'] - $previous_salary['late_deduction']
			- $previous_salary['tax'] + $previous_salary['Overtime'];

		$total_deduction = 	'adv_deduction'.$a + 'tax'.$a +get_sys_pay_pref('eobi');
//		label_cell($total_previuos_salary);//previous salary
//		label_cell(round($NetSalary));//currentsalary

		$total_deduction = 	$advance_pay + $monthly_tax + $employee_deduction +get_sys_pay_pref('eobi');
		label_cell(round($total_deduction));//total deduction
		label_cell(round2( $employee_allowances + $project_wise_salary - $total_deduction));//net salary

		$gl_id = get_next_trans_no(ST_JOURNAL);
		hidden('emp_id'.$a, $myrow['employee_id']);
		
		hidden('project', $dept_id);
		hidden('late_deduction'.$a,$late_deduction);
if($advanceinfo>0)
		{
		hidden('adv_deduction'.$a,$advance_pay);
}
else
{
hidden('adv_deduction'.$a,'');
}
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
		hidden('gl_id',$gl_id);
        hidden('f_year',$f_year);
		hidden('count', $a);
		$a++;
		echo "</tr>";
		}
		end_row();
        end_outer_table(1);  
		
	div_start('controls');
	//submit_center('insert', _("Insert"), true, '', 'default');
	div_end();
	}
	
    if($check > 0) 
	{	
	echo "<center><h3>Payroll Record</h3></center>";	
		$row = get_employee_through_dept_id($dept_id);
        $emp_acc_dept = get_employees_acc_dept($dept_id);
		$_POST['count'] =  $emp_acc_dept;
		$k = 0; //row colour counter
		$get_gl_id=get_gl_id_frm_payroll($dept_id, $id_month);
		$gl_ref_no=get_reference_through_id($get_gl_id);
		echo "<center><h4>$gl_ref_no</h4></center>";
		start_outer_table(TABLESTYLE2);
		table_section(1);
	
		echo 
		"<tr>
		<td colspan='' class='tableheader'> &nbsp; Sr. No. &nbsp; </td>
		<td colspan='' class='tableheader'> &nbsp; Employee Code &nbsp; </td>	
		<td colspan='' class='tableheader'>&nbsp;Employee &nbsp;</td>
		<td colspan='' class='tableheader'> &nbsp; Designation &nbsp; </td>			
		<td colspan='' class='tableheader'>&nbsp; Basic Salary &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Rate&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Tax Amount&nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; CPF &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Duty Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Hours &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Over Time Amount &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Leave Days &nbsp;</td> 
		<td colspan='' class='tableheader'>&nbsp; Deductible Days &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Absent Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Advance Deduction &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Allowance &nbsp;</td>
		<td colspan='' class='tableheader'>&nbsp; Deduction &nbsp;</td>
		<td colspan='' class='tableheader'> Previous Salary </td>
		<td colspan='' class='tableheader'> Current Salary </td>
		
		</tr>
		";
		$year=date("Y");
		$a = 1;
		$srno = 1;		
		
	while($myrow = db_fetch($row))
		{
			
		$date11 = "1-".$id_month."-$year";
		$from_date = date2sql(begin_month($date11));
	    $to_date = date2sql(end_month($date11));	
			$payroll = payroll_detail($myrow['employee_id'], $id_month,$_POST['f_year']);
			
		$emp = employee_attendance_detail($myrow['employee_id'], $id_month); //presence table
	    $absentdays = employee_present_attendance_detail($myrow['employee_id'],$myrow['emp_dept'],$id_month,$_POST['f_year']);
		$leaveinfo = employee_leave_attendance_detail($myrow['employee_id'],$from_date,$to_date,$_POST['f_year']);
		//$overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow['emp_dept'],$from_date,$to_date);
		$overtimeinfo=employee_over_time_detail($myrow['employee_id'],$myrow['emp_dept'],$id_month,$_POST['f_year']);
		$trans=get_payroll_through_dept_id($myrow['emp_dept']);
		$advanceinfo = employee_advance_attendance_detail($myrow['employee_id'],$from_date,$to_date,$_POST['f_year']);
        $previous_salary_ = get_previous_salary($id_month,$myrow['emp_dept'],  $myrow['employee_id']);

            $trans_= get_payroll_through_trans_no($myrow['employee_id']);
		echo "<tr>";
			alt_table_row_color($k);

		label_cell($srno, "", $srno);
		$srno += 1;

		label_cell($myrow['emp_code'], "", $myrow['emp_code']);
		label_cell($myrow['emp_name'], "", $myrow['emp_name']);
		label_cell($myrow['emp_desig']);
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
		label_cell($payroll['emp_cpf']);
		label_cell($myrow['duty_hours']);
		label_cell($overtimeinfo); // overtime hours	
//		text_cells( null,'overtime'.$a, $payroll['overtime']? $payroll['overtime'] : 0, 20, 60, false,'','','', 'Over Time');
		label_cell($payroll['overtime']);
		label_cell($absentdays);
		
		label_cell($leaveinfo);
		if($absentdays>=$leaveinfo)
		{
			$diff_absentleave=$absentdays-$leaveinfo;
			label_cell( $diff_absentleave);
		}
		
//		text_cells( null,'deduction'.$a, $payroll['late_deduction']? $payroll['late_deduction'] : 0, 20, 60, false,'','','', 'Deduction');
		label_cell($payroll['late_deduction']);
		label_cell($payroll['advance']);
		
//		text_cells( null,'adv_deduction'.$a, $payroll['advance_deduction']? $payroll['advance_deduction'] : 0, 20, 60, false,'','','', 'Advance Deduction');
		label_cell($payroll['advance_deduction']);
		//20august
		$trans_no=$payroll['trans_no'];
			
		employee_allowance_cell($trans_no,$payroll['allowance']);
		employee_deduction_cell($trans_no,$payroll['deduction']);
//

        $total_previuos_salary_= $previous_salary_['basic_salary'] -
            $previous_salary_['advance_deduction'] - $previous_salary_['late_deduction']
            - $previous_salary_['tax'] + $previous_salary_['overtime'];
        label_cell($total_previuos_salary_);//previous salary

        $NetSalary_ = $myrow['basic_salary'] - $payroll['advance_deduction'] -
            $payroll['late_deduction'] - $payroll['tax'] + $payroll['overtime'] ;

        label_cell($NetSalary_);//currentsalary

        //label_cell($payroll['deduction']);
		
		$payroll_head=$payroll['payroll_head'];

         $duty_hours +=$myrow['duty_hours'];
		 $totalabsentday +=$diff_absentleave;
		 $totalleaveinfo +=$leaveinfo;
		 
		$total_basic_salary += $myrow['basic_salary'];
		$total_tax += $payroll['tax'];
		$total_cpf += $payroll['emp_cpf'];
		$total_overtime +=  $payroll['overtime'];
		$total_late_deduction +=  $payroll['late_deduction'];
		$total_adv_deduction += $payroll['advance_deduction'];

		$total_payroll_allowance += $payroll['allowance'];
		$total_payroll_deduction += $payroll['deduction'];
	
		$netpayable = $total_basic_salary - $total_tax - $total_cpf + $total_overtime -
            $total_late_deduction - $total_adv_deduction + $total_payroll_allowance - $total_payroll_deduction;

		
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
		$display_total_basic_salary = number_format2($total_basic_salary);
		$display_total_tax = number_format2($total_tax);
		$display_cpf = number_format2($total_cpf);
		$display_duty_hours = number_format2($duty_hours);
		$display_total_overtime = number_format2($total_overtime);
		$display_totalabsentday = number_format2($totalabsentday);
		$display_totalleaveinfo = number_format2($totalleaveinfo);
		$display_total_late_deduction = number_format2($total_late_deduction);
		$display_total_adv_deduction = number_format2($total_adv_deduction);
		$display_payroll_allowance = number_format2($total_payroll_allowance);
		$display_payroll_deduction = number_format2($total_payroll_deduction);
		$display_netpayable = number_format2($netpayable);

		label_cell("<span style='font-weight:bold'>$display_total_basic_salary</span>");
		label_cell('');	
		label_cell("<span style='font-weight:bold'>($display_total_tax)</span>");
		label_cell("<span style='font-weight:bold'>($display_cpf)</span>");
		label_cell('');		
		label_cell('');		
		label_cell("<span style='font-weight:bold'>$display_total_overtime</span>");
		label_cell('');	
		label_cell('');	
		label_cell('');	
		label_cell("<span style='font-weight:bold'>($display_total_late_deduction)</span>" );
		label_cell('');				
		label_cell("<span style='font-weight:bold'>($display_total_adv_deduction)</span>");			
		label_cell("<span style='font-weight:bold'>$display_payroll_allowance</span>");			
		label_cell("<span style='font-weight:bold'>($display_payroll_deduction)</span>");

		end_row();

		start_row();
		label_cell("<span style='font-weight:bold'>NET</span>");	
		label_cell('');	
		label_cell('');
		label_cell("<span style='font-weight:bold'>$display_netpayable </span>");
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

echo $date;



if (isset($_POST['insert'])) 
{
	if(can_process())
	{
		
		$check = check_payroll_duplication($selected_id,$month_id,$_POST['f_year']);
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
loc_list_cells(_("Location"), 'location1',null, 'All Locations', "", false, 3,true,$_POST['project']);

month_list_cells( null, 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
date_cells(_("Date:"), 'date' , '');
fiscalyears_list_cells(_("Fiscal Year:"), 'f_year', $_POST['f_year']);

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
    customer_settings($selected_id, $month_id,$trans_no,$date,$_POST['f_year'],$divison,$_POST['location']);
}
br();
hidden('popup', @$_REQUEST['popup']);
end_form();
end_page(@$_REQUEST['popup']);

?>

