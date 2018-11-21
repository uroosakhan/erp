<?php

function check_payroll_duplication($emp_dept, $month,$f_year)
{
	$sql = "SELECT COUNT(trans_no) FROM ".TB_PREF."payroll WHERE 
	dept_id=".db_escape($emp_dept)." 
	AND month=".db_escape($month)." AND f_year=".db_escape($f_year);
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}

function add_payroll($sal, $tax,$tax_rate, $overtime, $late_deduction, $adv_deduction, $emp_id, $emp_dept, $month,$date,
					 $payroll_head,$over_time_hour,$absent_days,$leave_days,$advance,$allowance,$deduction,$emp_cpf,
					 $employer_cpf,$f_year,$division,$location,$project,$man_month,
					 $project_wise_salary,$eobi,$total_deduction,$net_salary,$arrer)
{

	$sql = "INSERT INTO  ".TB_PREF."payroll (`emp_id` , 
		 `tax` ,tax_rate,  `overtime`, `late_deduction` , `advance_deduction`, `dept_id`,  
		 `month`,  `basic_salary`,`date`,payroll_head,over_time_hour,absent_days, leave_days,
		 advance,allowance,deduction,emp_cpf,employer_cpf,f_year,divison,location,
		 project,project_wise_salary,eobi,man_month_value,total_deduction,net_salary,arrer)
		 
		VALUES ( ".db_escape($emp_id).", ".db_escape($tax).", ".db_escape($tax_rate).",
		  ".db_escape($overtime).", ".db_escape($late_deduction).", ".db_escape($adv_deduction).",
         '', ".db_escape($month).",".db_escape($sal).", ".db_escape(date2sql($date)).",
         ".db_escape($payroll_head).",".db_escape($over_time_hour).",".db_escape($absent_days).",
         ".db_escape($leave_days).",".db_escape($advance).",".db_escape($allowance).",".db_escape($deduction).",
         ".db_escape($emp_cpf).",".db_escape($employer_cpf).",".db_escape($f_year).",
         ".db_escape($division).",".db_escape($location).",".db_escape($project).",
         ".db_escape($project_wise_salary).",".db_escape($eobi).",".db_escape($man_month).",
         ".db_escape($total_deduction).",".db_escape($net_salary).",".db_escape($arrer)."); ";
	db_query($sql, "Could't insert payroll.");

}
function add_payroll_head($emp_dept, $month,$date,$gl_id)
{

	$sql = "INSERT INTO  ".TB_PREF."payroll_head (`dept_id`,  `month_id`, `date`,gl_id )
		VALUES (".db_escape($emp_dept).", ".db_escape($month).", ".db_escape(date2sql($date)).", ".db_escape($gl_id).");";
	db_query($sql, "Could't insert payroll.");

}
//
function get_payroll_info_through_trans_no($trans_no)
{

	$sql = "SELECT * FROM ".TB_PREF."payroll WHERE trans_no=".db_escape($trans_no);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}

//for advance detuction
function get_payroll_through_trans_no($emp_id)
{

	$sql = "SELECT trans_no FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($emp_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}


function check_payroll_head_max_trans()
{
	$sql = "SELECT MAX(trans_no) FROM ".TB_PREF."payroll_head ";
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function get_gl_id_frm_payroll($dept_id,$month_id)
{
	$sql = "SELECT gl_id FROM ".TB_PREF."payroll_head 
	WHERE dept_id=".db_escape($dept_id)." 
	AND
	month_id =".db_escape($month_id);;
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}
function get_payroll_max_trans_no()
{

	$sql = "SELECT MAX(trans_no) FROM ".TB_PREF."payroll";

	$myrow = db_query($sql, "could not get customer");
	$trans=db_fetch($myrow);
	if($trans[0]==0)
	{
		$trans_no=$trans[0]+1;
		return $trans_no;
	}
	else
	{
		return $trans[0];
	}
}
function get_last_advance_deduction($date, $emp_id,$max_trans)
{
	$sql = "SELECT MAX(payment) FROM ".TB_PREF."advance where 
				 payroll_id=".db_escape($max_trans-1)." AND emp_id=".db_escape($emp_id)."";
	$db = db_query($sql, "could not get last payment");
	$ft = db_fetch($db);
	return $ft[0];
}
function add_advance($date,$emp_id,$adv_deduction,$type,$remarks,$approve,$payroll_id,$payment, $f_year)
{
	$year = get_company_pref('f_year');
	$sql = "INSERT INTO ".TB_PREF."advance (date,emp_id,amount,type,remarks,approve,payroll_id,payment,f_year) 
	VALUES ( ".db_escape(sql2date($date)).", ".db_escape($emp_id).", ".db_escape(-1*$adv_deduction).",
	 ".db_escape($type).", ".db_escape($remarks).", ".db_escape($approve).",".db_escape($payroll_id) . ",".db_escape($payment) . ",".db_escape($year) . ")";

	db_query($sql,"The department could not be added");
}


function add_leave_encashment($encashment_id,$emp_id,$approve,$days,$f_year,$payment_date,$amount,
							  $taxable_amount,$income_tax,$mode_of_payment,$remarks,$gl_voucher,$check_no,$check_date
)
{
	$sql = "INSERT INTO ".TB_PREF."leave_encashment (	encashment_id,emp_name,
	approve,days,f_year,payment_date,
	amount,taxable_amount,	income_tax,
	mode_of_payment,remarks,gl_voucher,check_no,check_date) 
	VALUES ( ".db_escape($encashment_id).",
	 ".db_escape($emp_id).", 
	 ".db_escape($approve).",
	  ".db_escape($days).",
	   ".db_escape($remarks).",
	   ".db_escape($approve).",
	    ".db_escape($f_year).",
	   ".db_escape($payment_date).",
	   ".db_escape($approve).",
	    ".db_escape($amount).",
	   ".db_escape($taxable_amount).",
	   ".db_escape($income_tax).",
	     ".db_escape($mode_of_payment).",
	   ".db_escape($gl_voucher).",
	    ".db_escape($check_no).",
	   ".db_escape($check_date) . ")";

	db_query($sql,"The department could not be added");
}


//19august
function add_payroll_deduction($payroll_id,$deduct_id,$amount,$emp_id)
{
	$sql = "INSERT INTO ".TB_PREF."payroll_deduction (payroll_id,deduct_id,amount,emp_id) 
	VALUES ( ".db_escape($payroll_id).", ".db_escape($deduct_id).",".db_escape($amount) . ",".db_escape($emp_id) . ")";

	db_query($sql,"The department could not be added");
}
function add_payroll_allowance($payroll_id,$allow_id,$amount,$emp_id)
{
	$sql = "INSERT INTO ".TB_PREF."payroll_allowance (payroll_id,allow_id,amount,emp_id) 
	VALUES ( ".db_escape($payroll_id).", ".db_escape($allow_id).",".db_escape($amount) . ",".db_escape($emp_id) . ")";

	db_query($sql,"The department could not be added");
}

function update_advance_new($payment,$emp_id)
{
	$sql = "UPDATE ".TB_PREF."advance 
	SET payment= payment -".db_escape($payment)."
	
	WHERE  emp_id = ".db_escape($emp_id)." AND type='0' AND payroll_id='0' ";

	db_query($sql,"The department could not be updated");
}


function update_advance($date, $emp_id, $adv_deduction, $payroll_id)
{
	$sql = "UPDATE ".TB_PREF."advance 
	SET date=".db_escape(sql2date($date)).",
	amount=".db_escape(-1*$adv_deduction).",
	type= 1,
	approve= 1
	WHERE  emp_id = ".db_escape($emp_id)."
	AND
	payroll_id = ".db_escape($payroll_id);
	db_query($sql,"The department could not be updated");
}

function delete_advance($dept_id,$payroll)
{
	$sql="DELETE FROM ".TB_PREF."advance WHERE
	 emp_id=".db_escape($dept_id)."
	AND
	 payroll_id=".db_escape($payroll);
	db_query($sql,"could not delete department");
}
//19august
function delete_payroll_deduction($payroll)
{
	$sql="DELETE FROM ".TB_PREF."payroll_deduction WHERE
	 payroll_id=".db_escape($payroll);
	db_query($sql,"could not delete department");
}
function delete_payroll_allowance($payroll)
{
	$sql="DELETE FROM ".TB_PREF."payroll_allowance WHERE
	 payroll_id=".db_escape($payroll);
	db_query($sql,"could not delete department");
}

function payroll_detail($emp_id, $month,$f_year)
{

	$sql = "SELECT ".TB_PREF."payroll.*  FROM ".TB_PREF."payroll 
	WHERE emp_id=".db_escape($emp_id)." 
	AND
	month =$month
    AND
	f_year =".db_escape($f_year);

	$result = db_query($sql, "Could't get payroll detail.");
	$myrow = db_fetch($result);
	return $myrow;
}
function advance_update_detail($emp_id, $payroll_id)
{

	$sql = "SELECT ".TB_PREF."advance.*  FROM ".TB_PREF."advance 
	WHERE emp_id=".db_escape($emp_id)." 
	AND
	type ='1'
	AND
	payroll_id=".db_escape($payroll_id);

	$result = db_query($sql, "Could't get payroll detail.");
	$myrow = db_fetch($result);
	return $myrow[0];
}
//ANSAR
/*function employee_present_attendance_detail($emp_id, $dept_id, $from_date,$to_date)
{
	
$sql ="SELECT COUNT( present ) AS absent,COUNT(no_of_leave) 
FROM ".TB_PREF."presence
INNER JOIN ".TB_PREF."employee ON ".TB_PREF."employee.employee_id = ".TB_PREF."presence.employee_id
INNER JOIN ".TB_PREF."leave ON ".TB_PREF."employee.employee_id = ".TB_PREF."leave.emp_id
WHERE ".TB_PREF."employee.emp_dept = ".TB_PREF."presence.emp_dept
AND ".TB_PREF."presence.present =  '0'
AND ".TB_PREF."presence.employee_id = ".db_escape($emp_id)."
AND ".TB_PREF."presence.emp_dept = ".db_escape($dept_id)."
AND ".TB_PREF."presence.date >=".db_escape($from_date)."
AND ".TB_PREF."presence.date <= ".db_escape($to_date);
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];
	
}*/
function get_employee_through_dept_id($project,$division,$location)
{

	/*$sql = " * FROM ".TB_PREF."employee 
	WHERE project=".db_escape($project)."
	AND division=".db_escape($division)."
	AND location =".db_escape($location)."
	AND inactive !=".db_escape(1);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;*/

	$sql = "SELECT * FROM ".TB_PREF."employee WHERE division = ".db_escape($division)."
            AND ".TB_PREF."employee.inactive = '0'   ";

	if($location !=0)
		$sql .=" AND location=".db_escape($location)."";

	if($project !=0)
		$sql .=" AND  project = ".db_escape($project)."  ";


	$myrow = db_query($sql, "could not get customer");
	return $myrow ;
}
function get_employee_through_dept_id2($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id)."
            AND ".TB_PREF."employee.inactive = '0'   ";

	$myrow = db_query($sql, "could not get customer");
	return $result = db_fetch($myrow);
}
function get_employee_through_dept_id_new($project,$division,$location,$f_year,$month)
{
	$sql="SELECT 0_emp_attendance.`empl_id` FROM `0_emp_attendance` , 0_employee
              WHERE 0_emp_attendance.division = ".db_escape($division)."
               AND 0_emp_attendance.project= ".db_escape($project)."
               AND 0_emp_attendance.location=".db_escape($location)."
                AND 0_emp_attendance.fiscal_year = ".db_escape($f_year)."
               AND  0_emp_attendance.`empl_id` = 0_employee.employee_id 
               AND 0_employee.inactive !=1 
               AND 0_emp_attendance.month_id=$month
               GROUP BY 0_employee.employee_id ";
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}
function check_increment_applicable($emp_id,$date1,$date,$fiscal_year,$month1,$month2,$division,$project,$location)
{
	if($month1<$month2)
	{
		$sql = "SELECT COUNT(*) FROM `".TB_PREF."increment` WHERE  `emp_id`=$emp_id AND `valid_from`>='$date1' and `f_year`=$fiscal_year  AND division=$division AND project=$project AND location=$location";
	}
	else
	{
		$sql = "SELECT COUNT(*) FROM `".TB_PREF."increment` WHERE  `emp_id`=$emp_id AND `valid_from`>='$date1' AND `valid_from`<='$date' and `f_year`=$fiscal_year  AND division=$division AND project=$project AND location=$location";
	}
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}
function get_emplyee_work_duration($id)
{
	$end=end_fiscalyear();
	$end1= date2sql($end);
	$sql="SELECT TIMESTAMPDIFF(MONTH,`j_date`, '$end1')+1 FROm `0_employee` WHERE `employee_id`=$id";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_tax_month($id,$date)
{
	$end=end_fiscalyear();
	$end1= date2sql($end);
	$sql="SELECT TIMESTAMPDIFF(MONTH,'$date', '$end1')+1 FROm `0_employee` WHERE `employee_id`=$id";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_incr_duration($id,$date)
{
	$sql="SELECT  TIMESTAMPDIFF(MONTH,`valid_from`, '$date') FROm `0_increment` WHERE `emp_id`=$id";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_incr_duration_for_tax($id,$f_year)
{
	$sql="SELECT TIMESTAMPDIFF( 
MONTH , valid_from, increment_date) 
FROM  `0_increment` 
WHERE  `emp_id` =$id
AND increament_amount >0
AND f_year=$f_year
GROUP BY emp_id DESC ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_incr_month($id,$f_year)
{
	$sql="SELECT  MONTH(`valid_from`) FROm `0_increment` WHERE `emp_id`=$id AND f_year=$f_year AND 	increment_code!='' ORDER BY id DESC ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_pre_tax($id,$f_year,$month)
{
	$sql="SELECT SUM(`tax` ) 
FROM  `0_payroll` 
WHERE  `emp_id` =$id
AND  `f_year` =$f_year
AND month !=$month ";
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_pre_salary($id,$f_year,$month)
{
	$sql="SELECT SUM(`project_wise_salary`) 
FROM  `0_payroll` 
WHERE  `emp_id` =$id
AND  `f_year` =$f_year
AND month!=$month
";
	//`project_wise_salary`+arrer yhe jab ak month apply hota hy tu us lehaz sy entery ko galt show kerwata hy
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_emplyee_pre_month($id,$f_year,$month)
{
	$sql="SELECT SUM(`man_month_value`) 
FROM  `0_payroll` 
WHERE  `emp_id` =$id
AND  `f_year` =$f_year
AND month!=$month
";
	//`project_wise_salary`+arrer yhe jab ak month apply hota hy tu us lehaz sy entery ko galt show kerwata hy
	$db = db_query($sql,'Can not get Designation name');
	$ft = db_fetch($db);
	return $ft[0];
}
function get_pre_tax_month($id,$fiscal)
{
    $sql="SELECT COUNT(`month`) 
FROM  `0_payroll` 
WHERE  `emp_id` =$id
AND  `f_year` =$fiscal";
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
}
function get_emp_ot_rate($grade_id)
{
    $sql="SELECT  `ot` 
FROM  `0_hourly_rate` 
WHERE  `grade` =$grade_id";
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
}
function get_employee_through_dept_id_($dept_id,$f_year)
{

	$sql = "SELECT * FROM ".TB_PREF."employee 
	WHERE emp_dept=".db_escape($dept_id)."
	AND inactive !=".db_escape(1)."
    AND f_year=".db_escape($f_year);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}
function get_payroll_through_dept_id($dept_id)
{

	$sql = "SELECT trans_no FROM ".TB_PREF."payroll 
	WHERE dept_id=".db_escape($dept_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}

function employee_leave_attendance_detail($emp_id,$date1,$date2,$f_year)
{

    $sql ="SELECT SUM(no_of_leave)
      FROM ".TB_PREF."leave
	WHERE ".TB_PREF."leave.emp_id = ".db_escape($emp_id)."
	AND	".TB_PREF."leave.leave_type != '5'
	AND	".TB_PREF."leave.from_date>=".db_escape($date1)."
	AND ".TB_PREF."leave.from_date<=".db_escape($date2)." AND ".TB_PREF."leave.f_year = ".db_escape($f_year);

    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}


//18august
function employee_deduction($emp_id)
{
	$sql ="SELECT SUM(amount)
       FROM ".TB_PREF."emp_deduction
	WHERE ".TB_PREF."emp_deduction.emp_id = ".db_escape($emp_id);
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function employee_deduction_detail($emp_id)
{
	$sql ="SELECT *
       FROM ".TB_PREF."emp_deduction
	WHERE ".TB_PREF."emp_deduction.emp_id = ".db_escape($emp_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;

}
function employee_allowance_detail($emp_id)
{
	$sql ="SELECT *
       FROM ".TB_PREF."emp_allowance
	WHERE ".TB_PREF."emp_allowance.emp_id = ".db_escape($emp_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;

}
function employee_allowances($emp_id)
{

	$sql ="SELECT SUM(amount)
       FROM ".TB_PREF."emp_allowance
	WHERE ".TB_PREF."emp_allowance.emp_id = ".db_escape($emp_id);
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];

}



/*function employee_advance_attendance_detail($emp_id,$date1,$f_year)
{
	//$sql ="SELECT IF( type ='1' AND payroll_id !='0', SUM(`amount`), SUM(`amount`/`payment_shedule`) ) as amount

	$sql ="SELECT  SUM(amount)
       FROM ".TB_PREF."advance
	WHERE ".TB_PREF."advance.emp_id = ".db_escape($emp_id)."
	AND	".TB_PREF."advance.approve != '0'
	AND ".TB_PREF."advance.date<=".db_escape($date1)."
   AND  ".TB_PREF."advance.f_year = ".db_escape($f_year)." AND advance_on_base_of='2'";
	//AND ".TB_PREF."advance.date<=".db_escape($date2);
	
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];
	
}*/
////change for techno requirment
function employee_advance_attendance_detail($emp_id,$date1,$f_year)
{
	//$sql ="SELECT IF( type ='1' AND payroll_id !='0', SUM(`amount`), SUM(`amount`/`payment_shedule`) ) as amount

	$sql ="SELECT  SUM(amount)
       FROM ".TB_PREF."advance
	WHERE ".TB_PREF."advance.emp_id = ".db_escape($emp_id)."
	AND	".TB_PREF."advance.approve != '0'
	AND ".TB_PREF."advance.date<=".db_escape($date1)."
   AND  ".TB_PREF."advance.f_year = ".db_escape($f_year)." AND advance_on_base_of='2'";
	//AND ".TB_PREF."advance.date<=".db_escape($date2);

	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];

}
//for overtime
function employee_over_time_detail($emp_id,$emp_dep,$month_id,$f_year)
{

	$sql ="SELECT SUM(over_time) 
     FROM ".TB_PREF."presence
	WHERE 
	     ".TB_PREF."presence.employee_id = ".db_escape($emp_id)."
	AND	".TB_PREF."presence.emp_dept =".db_escape($emp_dep)."
	AND ".TB_PREF."presence.month_id=".db_escape($month_id)."
    AND ".TB_PREF."presence.f_year=".db_escape($f_year);

	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];

}
//for advance cutting
function employee_advance_pay_detail($emp_id,$payroll_id,$date1,$date2,$f_year,$month)
{

	$sql ="SELECT SUM(amount/payment_shedule) as payment 
       FROM ".TB_PREF."advance
	WHERE ".TB_PREF."advance.emp_id = ".db_escape($emp_id)."
	AND	".TB_PREF."advance.approve != '0'
	AND ".TB_PREF."advance.payroll_id=".db_escape($payroll_id)."
	AND ".TB_PREF."advance.date<=".db_escape($date2)."
    AND ".TB_PREF."advance.f_year=".db_escape($f_year)." AND advance_on_base_of='2' and month <='$month'";
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];

}
//

/*function employee_present_attendance_detail($emp_id, $dept_id, $from_date,$to_date)
{
	
		$sql ="SELECT COUNT(present) As absent

	FROM ".TB_PREF."presence,".TB_PREF."employee
	WHERE ".TB_PREF."employee.employee_id=".TB_PREF."presence.employee_id
	AND ".TB_PREF."employee.emp_dept=".TB_PREF."presence.emp_dept
	AND	".TB_PREF."presence.present = '0'
	AND	".TB_PREF."presence.employee_id = ".db_escape($emp_id)."
	AND	".TB_PREF."presence.emp_dept =".db_escape($dept_id)."
	AND	".TB_PREF."presence.date>=".db_escape($from_date)."
	AND ".TB_PREF."presence.date<=".db_escape($to_date);
	
	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];
	
}*/
function employee_present_attendance_detail($emp_id, $dept_id, $month_id,$f_year)
{

	$sql ="SELECT SUM(absent) As absent

	FROM ".TB_PREF."presence
	WHERE 
	
	".TB_PREF."presence.employee_id = ".db_escape($emp_id)."
	AND	".TB_PREF."presence.emp_dept =".db_escape($dept_id)."
	AND ".TB_PREF."presence.month_id=".db_escape($month_id)."
    AND ".TB_PREF."presence.f_year=".db_escape($f_year);

	$result = db_query($sql, "Could't get employee absent details");
	$myrow = db_fetch($result);
	return $myrow[0];

}
function update_payroll($trans_no,$tax, $overtime, $deduction, $adv_deduction,$date,$emp_id, $emp_dept,$month)
{       $sql = " UPDATE ".TB_PREF."payroll
		SET
		 `tax`  = ".db_escape($tax)." ,
		 `overtime` = ".db_escape($overtime)." ,
		 `late_deduction`  = ".db_escape($deduction)." ,
		 `advance_deduction`  = ".db_escape($adv_deduction).",
		  date = ".db_escape(date2sql($date))."
		   WHERE
		  trans_no	= ".db_escape($trans_no);
    add_audit_trail_payroll(ST_JOURNAL, $trans_no, Today(), _("Updated."));

	db_query($sql, "Could't update payroll.");
}
function update_payroll_head($trans_no)
{       $sql = " UPDATE ".TB_PREF."payroll_head
		SET
		dept_id  = ".db_escape($emp_dept)."
		month_id	= ".db_escape($month)."
		date = ".db_escape(date2sql($date))."
		   WHERE
		  trans_no	= ".db_escape($trans_no);

	db_query($sql, "Could't update payroll.");
}
/*
function update_payroll( $tax, $overtime, $deduction, $adv_deduction, $emp_id, $emp_dept, $month,$date,$trans_no)
{
   
		$sql = "UPDATE  ".TB_PREF."payroll
		SET
		 `tax`  = ".db_escape($tax)." ,
		 `overtime` = ".db_escape($overtime)." ,
		 `late_deduction`  = ".db_escape($deduction)." ,
		 `advance_deduction`  = ".db_escape($adv_deduction)."  
		   WHERE
		  month	= ".db_escape($month)."
		  AND
		  dept_id  = ".db_escape($emp_dept)."
		  AND
		  emp_id = ".db_escape($emp_id)."
		  AND
		  date = ".db_escape($date)."
		   AND
		  trans_no = ".db_escape($trans_no);
		db_query($sql, "Could't update payroll.");
	
}

*/
function get_advance($empt_id,$payroll_id)
{
	$sql = "SELECT * FROM ".TB_PREF."advance WHERE emp_id=".db_escape($dept_id)."
	payroll_id=".db_escape($payroll_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

/*function delete_payroll($dept_id, $month_id)
{
	$sql="DELETE FROM ".TB_PREF."payroll WHERE dept_id=".db_escape($dept_id)."
	AND  month=".db_escape($month_id) ;

	db_query($sql,"The payroll could not be deleted");
}*/
function delete_payroll($trans_no)
{
	$sql="DELETE FROM ".TB_PREF."payroll WHERE trans_no=".db_escape($trans_no);
	//AND  month=".db_escape($month_id) ;

	db_query($sql,"The payroll could not be deleted");
}

function delete_payroll_head($trans_no)
{
	$sql="DELETE FROM ".TB_PREF."payroll_head WHERE trans_no=".db_escape($trans_no);
	//AND  month=".db_escape($month_id) ;

	db_query($sql,"The payroll could not be deleted");
}
function get_month_name($month_id)
{
	$sql = "SELECT description AS month_name FROM ".TB_PREF."month WHERE id=".db_escape($month_id);

	$result = db_query($sql, "could not get month name");

	$row = db_fetch_row($result);

	return $row[0];
}
function add_emp_increament_new($increment_code,$emp_id,$valid_from,$increment_date,$increament_amount,$last_salary,$remarks,$fiscal,$division,$project,$location)
{
	//$date=date2sql($increment_date);
	$sql="INSERT INTO ".TB_PREF."increment (increment_code,emp_id,valid_from,increment_date,increament_amount,last_salary,remarks,f_year,division,project,location) 
		 VALUES(".db_escape($increment_code).",
		 		".db_escape($emp_id).",
		 		".db_escape($valid_from).",
		 		".db_escape(date2sql($increment_date)).",
		 		".db_escape(-1*$increament_amount).",
		 		".db_escape($last_salary).",
		 		".db_escape($remarks).",
		 		".db_escape($fiscal).",
".db_escape($division).",
".db_escape($project).",
".db_escape($location)."
		 		
		 		)";

	db_query($sql,"The increament could not be added");
}
//////////////////////////////////////
 function get_employee_deduction_days($month,$emp_id)
{
    $sql1="SELECT * 
FROM  `0_attendance_policy` 
INNER JOIN 0_employee ON 0_attendance_policy.`grade` = 0_employee.emp_grade
GROUP BY 0_attendance_policy.`grade` ";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $ded_start=$myrow1['deduction_start_time'];
    $ded_end=$myrow1['deduction_end_time'];
    $ded_val=$myrow1['deduction_value'];
    $ded_h_val=$myrow1['deduction_value_days'];

    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT
			(SUM(CASE WHEN `check_in` >= '$ded_start' AND `check_in` < '$ded_end' THEN $ded_val ELSE 0 END) )  as ded1			FROM 0_emp_attendance
			WHERE `att_date` between '$from' AND '$to'
			AND `empl_id`=$emp_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return floor($myrow['0']/$ded_h_val);
}
function get_employee_ded_h_days($month,$emp_id)
{
    $sql1="SELECT * 
FROM  `0_attendance_policy` 
INNER JOIN 0_employee ON 0_attendance_policy.`grade` = 0_employee.emp_grade
ORDER BY 0_attendance_policy.`id` DESC ";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $ded_start=$myrow1['deduction_start_time'];
    $ded_end=$myrow1['deduction_end_time'];
    $ded_val=$myrow1['deduction_value'];
    $ded_h_val=$myrow1['deduction_value_days'];

    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT
			(SUM(CASE WHEN `check_in` >= '$ded_start' AND `check_in` < '$ded_end' THEN $ded_val ELSE 0 END) )  as ded1			FROM 0_emp_attendance
			WHERE `att_date` between '$from' AND '$to'
			AND `empl_id`=$emp_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return ($myrow['0']/$ded_h_val);
}
function get_employee_absent_days($employee_id,$month,$f_year)
{
    $sql = "SELECT COUNT(*) FROM `0_emp_attendance`
 WHERE `check_in`=0 AND `check_out`=0 
 AND `empl_id`=$employee_id AND `month_id`=$month AND `fiscal_year`=$f_year ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_gazzet_holidays_days($month)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT COUNT( * ) 
FROM  `0_gazetted_holidays` 
WHERE DATE
BETWEEN  '$from'
AND  '$to' ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_emp_t_leave_days($emp_id,$month,$year)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $sql = "SELECT COUNT( * ) 
FROM  `0_leave` 
WHERE  `emp_id` =$emp_id
AND  `from_date` >=  '$from'
AND  `to_date` <=  '$to' 
AND f_year=$year";
/*
$sql = "SELECT SUM(no_of_leave) 
FROM  `0_leave` 
WHERE  `emp_id` =$emp_id
AND f_year=$year";*/
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_off_days($employee_id,$month,$f_year)
{
    $sql = "SELECT COUNT(*) FROM `0_emp_attendance`
 WHERE  DAYNAME(`att_date`) IN ('Sunday') 
 AND `empl_id`=$employee_id AND `month_id`=$month AND `fiscal_year`=$f_year ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_absent_days1($employee_id,$month,$f_year)
{
    $sql = "SELECT COUNT(*) FROM `0_emp_attendance`
 WHERE `check_in`=0 AND `check_out`=0 
 AND `empl_id`=$employee_id AND `month_id`=$month AND `fiscal_year`=$f_year ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_duty_hour($employee_id)
{
    $sql = "SELECT ".TB_PREF."employee.duty_hours FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
/*function get_employee_ot_hours($month,$emp_id,$fiscal_year,$grade)
{
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    $duty_hour=get_employee_duty_hour($emp_id);
    $total_absent=get_employee_absent_days1($emp_id,$month,$fiscal_year);
    $sql1="SELECT SUM(TIMEDIFF(`check_out`,`check_in`) ) AS D1,COUNT(*) AS tday FROM 0_emp_attendance 
WHERE `att_date` >= '$from'  AND `att_date` <= '$to'  
AND empl_id = $emp_id AND `fiscal_year`=$fiscal_year AND check_out>'17:00'";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    $t_hours=$myrow1['D1'];
    $t_days=$myrow1['tday']-$total_absent;
    $t_w_hours=$t_days*$duty_hour;
    $t_ot_val=$t_hours-$t_w_hours;
    $ot_rate=get_emp_ot_rate($grade);

    $converted_time = date('H:i', mktime(0,($t_hours*$ot_rate)));
    return $converted_time;
}*/
function get_employee_set_ot_hour($employee_id)
{
    $sql = "SELECT ".TB_PREF."employee.ot_hours FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_employee_ot_hours($month,$emp_id,$fiscal_year)
{
    $duty_hour=get_employee_set_ot_hour($emp_id);
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    //$duty_hour=get_employee_duty_hour($emp_id);
   // $total_absent=get_employee_absent_days($emp_id,$month,$fiscal_year);
    $sql1="SELECT SUM(HOUR(TIMEDIFF(check_out,'$duty_hour'))) AS thour,(SUM(MINUTE(TIMEDIFF(check_out,'$duty_hour')))) AS tmin  FROM 0_emp_attendance 
WHERE `att_date` >= '$from'  AND `att_date` <= '$to'  
AND empl_id = $emp_id AND `fiscal_year`=$fiscal_year
AND check_out>'$duty_hour'  AND check_out!='00:00:00'";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    
    
    //$t_days=$myrow1['tday'];
    $t_hours= $myrow1['thour'].":".$myrow1['tmin'];
   // $t_w_hours=$t_days*$duty_hour;
   // $t_ot_val=$t_hours-$t_w_hours;

    $converted_time = date('H:i:s', mktime(0,$myrow1['D1']));
  //  display_error($t_hours."---".($converted_time)."total days".$t_w_hours."");
    return $t_hours;
}
/////////////FOR Calculation
function get_employee_ot_hours_calculation($month,$emp_id,$fiscal_year)
{
    $duty_hour=get_employee_set_ot_hour($emp_id);
    $f_year = get_current_fiscalyear();
    if($month==1 ||$month==2 || $month==3 || $month==4 || $month==5 || $month==6)
        $year1 = date('Y', strtotime($f_year['end']));
    else
        $year1 = date('Y', strtotime($f_year['begin']));
    $from = "$year1-".$month."-01";
    $to = "$year1-".$month."-31";
    //$duty_hour=get_employee_duty_hour($emp_id);
   // $total_absent=get_employee_absent_days($emp_id,$month,$fiscal_year);
    $sql1="SELECT SUM(HOUR(TIMEDIFF(check_out,'$duty_hour'))) AS thour,(SUM(MINUTE(TIMEDIFF(check_out,'$duty_hour')))) AS tmin  FROM 0_emp_attendance 
WHERE `att_date` >= '$from'  AND `att_date` <= '$to'  
AND empl_id = $emp_id AND `fiscal_year`=$fiscal_year
AND check_out>'$duty_hour'  AND check_out!='00:00:00'";
    $result1 = db_query($sql1, "Could't get employee name");
    $myrow1 = db_fetch($result1);
    
    
    //$t_days=$myrow1['tday'];
    $t_hours= $myrow1['thour']+$myrow1['tmin']/60;
   // $t_w_hours=$t_days*$duty_hour;
   // $t_ot_val=$t_hours-$t_w_hours;

    $converted_time = date('H:i:s', mktime(0,$myrow1['D1']));
  //  display_error($t_hours."---".($converted_time)."total days".$t_w_hours."");
    return $t_hours;
}



////////get arrerrs through month
function get_arrerr_applicaple($emp_id,$fiscal_year)
{
	$sql = "SELECT sum(`arrer`) FROM `".TB_PREF."payroll` WHERE `emp_id`=$emp_id and `f_year`=$fiscal_year AND `arrer`!=0";
	$result = db_query($sql, "could not get month name");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_arrerr_value($emp_id,$date,$division,$project,$location,$fiscal_year)
{
	$sql = "SELECT  SUM(increament_amount) 
FROM  `0_increment` 
WHERE  `emp_id` =$emp_id
AND  `valid_from` <=  '$date' 
AND division=$division
AND project=$project
AND location=$location
AND f_year=$fiscal_year";
	//$sql = " sum(`increament_amount`)  FROM `".TB_PREF."increment` WHERE `emp_id`=$emp_id AND valid_from <=$date";
	$result = db_query($sql, "could not get month name");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_arrerr_value_for_tax($emp_id,$date,$fiscal_year)
{
	$sql = "SELECT  SUM(increament_amount) 
FROM  `0_increment` 
WHERE  `emp_id` =$emp_id
AND  `valid_from` <=  '$date' 
AND f_year=$fiscal_year
AND increament_amount>0";
	//$sql = " sum(`increament_amount`)  FROM `".TB_PREF."increment` WHERE `emp_id`=$emp_id AND valid_from <=$date";
	$result = db_query($sql, "could not get month name");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_emp_dept_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
function get_reference_through_id($id)
{
	$sql = "SELECT * FROM ".TB_PREF."refs WHERE id=".db_escape($id);

	$result = db_query($sql, "could not query reference table");
	$row = db_fetch($result);
	return $row['reference'];
}
//after lihgt

function get_payroll_allowance_id($emp_id)
{

	$sql = "SELECT COUNT(allow_id) FROM ".TB_PREF."emp_allowance
 WHERE emp_id =".db_escape($emp_id);

	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function get_payroll_emp_id($dept_id)
{
	$sql = "SELECT emp_id FROM ".TB_PREF."payroll
 WHERE dept_id =".db_escape($dept_id);
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

//------------------------
function get_project_wise_salary($employee_id,$f_year,$project,$id_month)
{
	if($employee_id==2)
	{
		$sql="SELECT man_month_value FROM ".TB_PREF."man_month where employee_id=".db_escape($employee_id)." AND month_id=$id_month
	 		AND f_year=".db_escape($f_year)." AND project_name=".db_escape($project)." ";
		$db = db_query($sql,'Can not get');
		$ft = db_fetch($db);
		return $ft[0];

	}else{
		$sql="SELECT man_month_value FROM ".TB_PREF."man_month where employee_id=".db_escape($employee_id)." AND month_id=$id_month
	 		AND f_year=".db_escape($f_year)." AND project_name=".db_escape($project)." ";
		$db = db_query($sql,'Can not get');
		$ft = db_fetch($db);
		return $ft[0];


	}
}
function pay_roll_entry($division,$location,$project,$f_id,$id_month)
{

	$sql = "SELECT COUNT(emp_id) FROM ".TB_PREF."payroll WHERE 
	divison =".db_escape($division)."
	AND
	location =".db_escape($location)." 
	AND
	project=".db_escape($project)." 
	AND f_year=".db_escape($f_id)."
	AND month=$id_month";

	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

?>