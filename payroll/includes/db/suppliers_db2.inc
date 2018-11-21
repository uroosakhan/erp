<?php
	$DOB = date2sql($DOB);
	if ($DOB == "")
		$DOB = "0000-00-00";
	else
		$DOB = date2sql($DOB);

function add_employee(
	$emp_code,
	$emp_name,
	$emp_father,
	$emp_cnic,
	$DOB,
	$j_date,
	$l_date,
	$dur_period1,
	$dur_period2,
	$emp_reference,
	$emp_home_phone,
	$emp_mobile,
	$emp_email,
	$emp_bank,
	$company_bank,
	$basic_salary,
	$prev_salary,
	$duty_hours,
	$ot_hours,
	$social_sec,
	$emp_ntn,
	$emp_eobi,
	$emp_address,
	$notes,
	$emp_title,
	$emp_gen,
	$emp_dept,
	$emp_desig,
    $emp_grade,
	$cpf,
	$employer_cpf,
	$division,
	$project,
	$age,
	$report,
	$location,
	$vehicle,
	$status,
	$tax_deduction,
	$applicable,
	$leave_applicable,
	$sessi_applicable,
	$eobi_applicable,
	$mb_flag,
	$active_filer,
	$bank_name,
	$bank_branch,
	$salary,
	$cnic_expiry_date,
	$pec_no,
	$pec_expiry_date,
	$license_no,
	$license_expiry_date,
	$text_filer,
	$text_non_filer,
	$loan_account,
	$advance_account,
	$salary_account,
	$bonus_account,
	$payroll_expenses,
	$payroll_liabilty,
	$advance_receivable,
	$payment_account,
	$tax_liability,
	$deduction_account,
	$over_time,
	$blood_group)
{
    global $Refs;
	$DOB = date2sql($DOB);
	$j_date = date2sql($j_date);
	$l_date = date2sql($l_date);
	$dur_period1= date2sql($dur_period1);
    $dur_period2= date2sql($dur_period2);
	
	$sql = "INSERT INTO ".TB_PREF."employee (
	emp_code,
	emp_name,
	emp_father,
	emp_cnic,
	DOB, 
	j_date,
	l_date ,
	dur_period1,
	dur_period2,
	emp_reference,
	emp_home_phone, 
	emp_mobile, 
	emp_email,
	emp_bank, 
	company_bank, 
	basic_salary, 
	prev_salary, 
	duty_hours,
	ot_hours,
	social_sec,
	 emp_ntn, 
	 emp_eobi,
	 emp_address, 
	 notes,
	 emp_title,
	 emp_gen,
	 emp_dept, 
	 emp_desig,
	 emp_grade,
	 cpf,
	 employer_cpf,
	 division,
	 project,
	 age,
     report,
     location,
     vehicle,
     status,
     tax_deduction,
     applicable,
     leave_applicable,
      sessi_applicable,
      eobi_applicable,
      mb_flag,
      active_filer,
      bank_name,
      bank_branch,
      salary,
       cnic_expiry_date,
       pec_no,
       pec_expiry_date,
       license_no,
       license_expiry_date,
       text_filer,
       text_non_filer,
       loan_account,
       advance_account,
       salary_account,
       bonus_account,
       payroll_expenses,
       payroll_liabilty,
       advance_receivable,
       payment_account,
       tax_liability,
       deduction_account,
       over_time,
       blood_group)
		VALUES (
		".db_escape($emp_code). ",
		".db_escape($emp_name). ", 
		".db_escape($emp_father). ", 
		".db_escape($emp_cnic). ",
		".db_escape($DOB). ",
		".db_escape($j_date). ",
		".db_escape($l_date). ",
		".db_escape($dur_period1). ",
		".db_escape($dur_period2). ",
		".db_escape($emp_reference). ", 
		".db_escape($emp_home_phone). ", 
		".db_escape($emp_mobile). ", 
		".db_escape($emp_email). ", 
		".db_escape($emp_bank). ",  
		".db_escape($company_bank). ",  		
		".db_escape($basic_salary). ",
		".db_escape($prev_salary). ",
		".db_escape($duty_hours). ",
		".db_escape($ot_hours). ",
		".db_escape($social_sec). ",
		".db_escape($emp_ntn). ",
		".db_escape($emp_eobi). ",								  		
		".db_escape($emp_address). ",
		".db_escape($notes). ",
		".db_escape($emp_title). ",  
		".db_escape($emp_gen). ",
		".db_escape($emp_dept). ",
		".db_escape($emp_desig). ", 
		".db_escape($emp_grade). ",
		".db_escape($cpf). ",
        ".db_escape($employer_cpf). ",
        ".db_escape($division). ",
        ".db_escape($project). ",
        ".db_escape($age). ",
        ".db_escape($report). ",
        ".db_escape($location). ",
        ".db_escape($vehicle). ",
        ".db_escape($status). ",
        ".db_escape($tax_deduction). ",
        ".db_escape($applicable). ",
        ".db_escape($leave_applicable). ",
        ".db_escape($sessi_applicable). ",
        ".db_escape($eobi_applicable). ",
        ".db_escape($mb_flag). ",
        ".db_escape($active_filer). ",
        ".db_escape($bank_name). ",
        ".db_escape($bank_branch). ",
        ".db_escape($salary). ",
        ".db_escape(sql2date($cnic_expiry_date)). ",
        ".db_escape($pec_no). ",
        ".db_escape(sql2date($pec_expiry_date)). ",
        ".db_escape($license_no). ",
        ".db_escape(sql2date($license_expiry_date)).",
        ".db_escape($text_filer).",
        ".db_escape($text_non_filer).",
        ".db_escape($loan_account).",
        ".db_escape($advance_account).",
        ".db_escape($salary_account).",
        ".db_escape($bonus_account).",
        ".db_escape($payroll_expenses).",
        ".db_escape($payroll_liabilty).",
        ".db_escape($advance_receivable).",
        ".db_escape($payment_account).",
        ".db_escape($tax_liability).",
        ".db_escape($deduction_account).",
        ".db_escape($over_time).",
        ".db_escape($blood_group)."
        
        )";
    $Refs->save(ST_EMPLOYEECODE, 0, $emp_code);
    
        add_audit_trail_payroll(ST_EMPLOYEECODE, $_POST['emp_code'], Today(), _("Inserted."));

	db_query($sql,"The employee could not be added");
}

///-----------------------------------
function add_man_month_dir($employee_name,$project_name,$man_month_value,$month,$employee_id,$division,$location,$f_year)
{
	$sql = "INSERT INTO ".TB_PREF."man_month (employee_name,project_name,man_month_value,month_id,employee_id,f_year,division,location)
     VALUES (".db_escape($employee_name).",".db_escape($project_name) . ","
		.db_escape($man_month_value) .","
		.db_escape($month).",
        ".db_escape($employee_id).",
        ".db_escape($f_year).",
        ".db_escape($division).",
        ".db_escape($location)."
        )";
	db_query($sql,"The field could not be added");
}
//------------------------------------------


//----add employee in "employee Log" table----------------------
function add_employee_log($emp_code, $emp_name, $emp_father, $emp_cnic,$DOB, $j_date, $l_date, $dur_period1, $dur_period2, $emp_reference,
$emp_home_phone, $emp_mobile, $emp_email, $emp_bank, $company_bank, $basic_salary,  $prev_salary, $duty_hours, $ot_hours, $social_sec, $emp_ntn, $emp_eobi, $emp_address, $notes, $emp_title, $emp_gen, $emp_dept, $emp_desig,$emp_grade,$cpf,$employer_cpf,$inactive,$division,$project,$age,$report,$location,$vehicle,$status,$tax_status,$tax_deduction,$applicable,$leave_applicable, $sessi_applicable,$eobi_applicable,$mb_flag,$active_filer,$bank_name,$bank_branch,$salary,$cnic_expiry_date,$pec_no,
		$pec_expiry_date,$license_no,$license_expiry_date,$text_filer,$text_non_filer, $over_time, $blood_group)
{
	//global $Refs;
	$DOB = date2sql($DOB);
	$j_date = date2sql($j_date);
	$l_date = date2sql($l_date);
    $dur_period1 = date2sql($dur_period1);
    $dur_period2 = date2sql($dur_period2);

	$sql = " INSERT  INTO ".TB_PREF."employee_log (emp_code, emp_name, emp_father,
	emp_cnic, DOB, j_date, l_date , dur_period1, dur_period2, emp_reference, emp_home_phone, emp_mobile, emp_email,
	emp_bank, company_bank, basic_salary, prev_salary, duty_hours, ot_hours, social_sec, emp_ntn, emp_eobi,
	 emp_address, notes,emp_title,emp_gen,emp_dept, emp_desig,emp_grade,cpf,employer_cpf,inactive,division,project
     ,age,report,location,vehicle,status,tax_deduction,applicable,leave_applicable,
      sessi_applicable,eobi_applicable,mb_flag,active_filer,bank_name,bank_branch,salary,
       cnic_expiry_date,pec_no,pec_expiry_date,license_no,license_expiry_date,text_filer,text_non_filer, over_time, blood_group)
		VALUES (
		".db_escape($emp_code). ",
		".db_escape($emp_name). ", 
		".db_escape($emp_father). ", 
		".db_escape($emp_cnic). ",
		".db_escape($DOB). ",
		".db_escape($j_date). ",
		".db_escape($l_date). ",
		".db_escape($dur_period1). ",
		".db_escape($dur_period2). ",
		".db_escape($emp_reference). ", 
		".db_escape($emp_home_phone). ", 
		".db_escape($emp_mobile). ", 
		".db_escape($emp_email). ", 
		".db_escape($emp_bank). ",  
		".db_escape($company_bank). ",  		
		".db_escape($basic_salary). ",
		".db_escape($prev_salary). ",
		".db_escape($duty_hours). ",
		".db_escape($ot_hours). ",
		".db_escape($social_sec). ",
		".db_escape($emp_ntn). ",
		".db_escape($emp_eobi). ",								  		
		".db_escape($emp_address). ",
		".db_escape($notes). ",
		".db_escape($emp_title). ",  
		".db_escape($emp_gen). ",
		".db_escape($emp_dept). ",
		".db_escape($emp_desig). ", 
		".db_escape($emp_grade). ",
		".db_escape($cpf). ",
        ".db_escape($employer_cpf). ",
         ".db_escape($inactive). ",
        ".db_escape($division). ",
        ".db_escape($project). ",
        ".db_escape($age). ",
        ".db_escape($report). ",
        ".db_escape($location). ",
        ".db_escape($vehicle). ",
        ".db_escape($status). ",
        ".db_escape($tax_deduction). ",
        ".db_escape($applicable). ",
        ".db_escape($leave_applicable). ",
        ".db_escape($sessi_applicable). ",
        ".db_escape($eobi_applicable). ",
        ".db_escape($mb_flag). ",
        ".db_escape($active_filer). ",
        ".db_escape($bank_name). ",
        ".db_escape($bank_branch). ",
        ".db_escape($salary). ",
        ".db_escape($cnic_expiry_date). ",
        ".db_escape($pec_no). ",
        ".db_escape($pec_expiry_date). ",
        ".db_escape($license_no). ",
        ".db_escape($license_expiry_date ).",
        ".db_escape($text_filer).",
        ".db_escape($text_non_filer).",
        ".db_escape($over_time).",
        ".db_escape($blood_group).

		")";
	//$Refs->save(ST_EMPLOYEECODE, 0, $emp_code);
	db_query($sql,"The employee could not be added");
}
//





function check_emp_code_duplication($emp_code)
{
	$sql = "SELECT COUNT(emp_code) FROM ".TB_PREF."employee WHERE 
        emp_code=".db_escape($emp_code);
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}



function update_employee(
	$employee_id, 
	$emp_code, 
	$emp_name, 
	$emp_father, 
	$emp_cnic,
	$DOB, 
	$j_date, 
	$l_date, 
    $dur_period1,
    $dur_period2,
    $emp_reference,
	$emp_home_phone, 
	$emp_mobile, 
	$emp_email, 
	$emp_bank,  
	$company_bank,
	$basic_salary, 
	$prev_salary, 
	$duty_hours,
	$ot_hours,
	$social_sec, 
	$emp_ntn, 
	$emp_eobi, 
	$emp_address,
	$notes, 
	$emp_title, 
	$emp_gen, 
    $emp_dept, 
	$emp_desig, 
	$emp_grade,
	$cpf,
	$employer_cpf,
	$inactive,
	$division,
	$project,
	$age,
	$report,
	$location,
	$vehicle,
	$status,
	$tax_deduction,
	$applicable,
	$leave_applicable,
	$sessi_applicable,
	$eobi_applicable,
	$mb_flag,
	$active_filer,
	$bank_name,
	$bank_branch,
	$salary,
	$cnic_expiry_date,
	$pec_no,
	$pec_expiry_date,
	$license_no,
	$license_expiry_date,
	$text_filer,
	$text_non_filer,
	$loan_account,
	$advance_account,
	$salary_account,
	$bonus_account,
	$payroll_expenses,
	$payroll_liabilty,
	$advance_receivable,
	$payment_account,
	$tax_liability,
	$deduction_account,
	$over_time,
    $blood_group)
{
	$DOB = date2sql($DOB);
	$j_date = date2sql($j_date);
	$l_date = date2sql($l_date);
	$dur_period1= date2sql($dur_period1);
    $dur_period2= date2sql($dur_period2);
    $cnic_expiry_date=date2sql($cnic_expiry_date);
    $pec_expiry_date=date2sql($pec_expiry_date);
    $license_expiry_date=date2sql($license_expiry_date);



		$sql = " UPDATE ".TB_PREF."employee 
	    SET 
	    emp_code=".db_escape($emp_code) . ",
		emp_name=".db_escape($emp_name) . ", 
		emp_father=".db_escape($emp_father) . ",
		emp_cnic=".db_escape($emp_cnic) . ",
		DOB=".db_escape($DOB) . ",
		j_date=".db_escape($j_date) . ",
		l_date=".db_escape($l_date) . ",
		dur_period1=".db_escape($dur_period1) . ",
		dur_period2=".db_escape($dur_period2) . ",
		emp_reference=".db_escape($emp_reference) . ",
		emp_home_phone=".db_escape($emp_home_phone) . ",
		emp_mobile=".db_escape($emp_mobile) . ",
		emp_email=".db_escape($emp_email) . ",
		emp_bank=".db_escape($emp_bank) . ",	
		company_bank=".db_escape($company_bank) . ",				
		basic_salary=".db_escape($basic_salary) . ",	
		prev_salary=".db_escape($prev_salary) . ",	
		duty_hours=".db_escape($duty_hours) . ",
		ot_hours=".db_escape($ot_hours) . ",	
		social_sec=".db_escape($social_sec) . ",	
		emp_ntn=".db_escape($emp_ntn) . ",										
		emp_eobi=".db_escape($emp_eobi) . ",												
		emp_address=".db_escape($emp_address) . ",
		notes=".db_escape($notes).",
		emp_title=".db_escape($emp_title). ",  
		emp_gen=".db_escape($emp_gen). ",
		emp_dept=".db_escape($emp_dept). ",
		emp_desig=".db_escape($emp_desig). ", 
		emp_grade=".db_escape($emp_grade). ",
		cpf=".db_escape($cpf).",
		employer_cpf=".db_escape($employer_cpf).",
		inactive=".db_escape($inactive).",
		division=".db_escape($division).",
        project=".db_escape($project).",
        age=".db_escape($age).",
        report=".db_escape($report).",
        location=".db_escape($location).",
        vehicle=".db_escape($vehicle).",
        status=".db_escape($status).",
        tax_deduction=".db_escape($tax_deduction).",
        applicable=".db_escape($applicable).",
        leave_applicable=".db_escape($leave_applicable).",
        sessi_applicable=".db_escape($sessi_applicable).",
        eobi_applicable=".db_escape($eobi_applicable).",
        mb_flag=".db_escape($mb_flag).",
        active_filer=".db_escape($active_filer).",
        bank_name=".db_escape($bank_name).",
        bank_branch=".db_escape($bank_branch).",
        salary=".db_escape($salary).",
        cnic_expiry_date=".db_escape($cnic_expiry_date).",
        pec_no=".db_escape($pec_no).",
        pec_expiry_date=".db_escape($pec_expiry_date).",
        license_no=".db_escape($license_no).",
        license_expiry_date=".db_escape($license_expiry_date).",
        text_filer=".db_escape($text_filer).",
        text_non_filer=".db_escape($text_non_filer)." ,
        loan_account=".db_escape($loan_account)." ,
        advance_account=".db_escape($advance_account)." ,
        salary_account=".db_escape($salary_account)." ,
        bonus_account=".db_escape($bonus_account).", 
        
        payroll_expenses=".db_escape($payroll_expenses).", 
        payroll_liabilty=".db_escape($payroll_liabilty).", 
        advance_receivable=".db_escape($advance_receivable).", 
        payment_account=".db_escape($payment_account).", 
        tax_liability=".db_escape($tax_liability).", 
        deduction_account=".db_escape($deduction_account).", 
        over_time=".db_escape($over_time).", 
        blood_group=".db_escape($blood_group)."
         
        WHERE employee_id = ".db_escape($employee_id);
    add_audit_trail_payroll(ST_EMPLOYEECODE, $_POST['emp_code'], Today(), _("Updated."));

	db_query($sql,"The employee could not be updated");
}
//for inactive emplloyee
function update_employee_inactive($employee_id)
{
	$sql = "UPDATE ".TB_PREF."employee 
	SET  inactive=".db_escape(1)
		." WHERE employee_id = ".db_escape($employee_id);

	db_query($sql,"The employee could not be updated");
}


function delete_employee($employee_id)
{
	$sql="DELETE FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);
	
	
	    add_audit_trail_payroll(ST_EMPLOYEECODE, $_POST['emp_code'], Today(), _("Deleted."));

	db_query($sql,"check failed");
}

function get_employee($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}

function get_employee_name($employee_id)
{
	$sql = "SELECT  emp_title,emp_gen,emp_dept, emp_desig,emp_grade FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
} 

function get_employee_gender($employee_id)
{
	$sql = "SELECT emp_gen FROM ".TB_PREF."gen WHERE id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_code($code)
{
	$sql = "SELECT COUNT(emp_code) FROM ".TB_PREF."employee WHERE emp_code=".db_escape($code);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_employee_dept($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id=".db_escape($dept_id);

	$result = db_query($sql, "could not get department");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_employee_desg($desg_id)
{
	$sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($desg_id);

	$result = db_query($sql, "could not get designation");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_month_name($month_id)
{
	$sql = "SELECT description AS month_name FROM ".TB_PREF."month WHERE id=".db_escape($month_id);

	$result = db_query($sql, "could not get month name");

	$row = db_fetch_row($result);

	return $row[0];
}
//ansar22april
function get_week_name($week_id)
{
	$sql = "SELECT description AS week_name FROM ".TB_PREF."week WHERE id=".db_escape($week_id);

	$result = db_query($sql, "could not get week no");

	$row = db_fetch_row($result);

	return $row[0];
}



/*
function get_supplier_details($supplier_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $0past1;
	// removed - supp_trans.alloc from all summations

	if ($all)
    	$value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh)";
    else	
    	$value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT.",
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh - trans.alloc),
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh + trans.alloc))";
	$due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT.",trans.due_date,trans.tran_date)";
    $sql = "SELECT supp.supp_name, supp.curr_code, ".TB_PREF."payment_terms.terms,

		Sum(IFNULL($value,0)) AS Balance,

		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2,
		supp.credit_limit - Sum(IFNULL(IF(trans.type=".ST_SUPPCREDIT.", -1, 1) 
			* (ov_amount + ov_gst + ov_discount + trans.gst_wh),0)) as cur_credit,
		supp.tax_group_id

		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id AND trans.tran_date <= '$todate',
			 ".TB_PREF."payment_terms

		WHERE
			 supp.payment_terms = ".TB_PREF."payment_terms.terms_indicator
			 AND supp.supplier_id = $supplier_id ";
	if (!$all)
		$sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh) - trans.alloc > ".FLOAT_COMP_DELTA." ";  
	$sql .= "GROUP BY
			  supp.supp_name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month";

    $result = db_query($sql,"The customer details could not be retrieved");
    $supp = db_fetch($result);

    return $supp;
}



function get_supplier_accounts($supplier_id)
{
	$sql = "SELECT payable_account,purchase_account,payment_discount_account FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	return db_fetch($result);
}

function get_supplier_contacts($supplier_id, $action=null)
{
	$results = array();
	$res = get_crm_persons('supplier', $action, $supplier_id);
	while($contact = db_fetch($res))
		$results[] = $contact;

	return $results;
}

function get_current_supp_credit($supplier_id)
{
	$suppdet = get_supplier_details($supplier_id);
	return $suppdet['cur_credit'];

}
*/
function is_new_employee($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}

?>