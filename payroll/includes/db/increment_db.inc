<?php
function add_emp_increament($increment_code,$emp_id,$valid_from,$increment_date,$increament_amount,$last_salary,$remarks,$fiscal
,$division,$project,$location)
{
	$sql="INSERT INTO ".TB_PREF."increment (increment_code,emp_id,valid_from,increment_date,increament_amount,last_salary,remarks,f_year,division,project,location) 
		 VALUES(".db_escape($increment_code).",
		 		".db_escape($emp_id).",
		 		".db_escape(sql2date($valid_from)).",
		 		".db_escape(sql2date($increment_date)).",
		 		".db_escape($increament_amount).",
		 		".db_escape($last_salary).",
		 		".db_escape($remarks).",
		 		".db_escape($fiscal).",
".db_escape($division).",
".db_escape($project).",
".db_escape($location)."
		 		
		 		)";
    add_audit_trail_payroll(ST_INCREMENT, $_POST['emp_id'], Today(), _("Inserted."));

	db_query($sql,"The increament could not be added");
}
function update_emp_basic_salary($emp_id,$increament_amount,$current_salary)
{
	$sql = "UPDATE ".TB_PREF."employee
	 SET basic_salary = basic_salary +".db_escape($increament_amount).",
	 prev_salary=".db_escape($current_salary)."
	
	 WHERE 	employee_id = ".db_escape($emp_id);
	db_query($sql,"The basic salary could not update");
}





function add_leave_encashment($encashment_id,$emp_id,$approve,
							  $days,$f_year,$payment_date,$amount,
							  $taxable_amount,$income_tax,
							  $mode_of_payment,$remarks,$gl_voucher,
							  $check_no,$check_date,$date,$account_info
)  


{
	$sql = " INSERT INTO ".TB_PREF."leave_encashment (	encashment_id,emp_name,
	approve,days,f_year,payment_date,
	amount,taxable_amount,	income_tax,
	mode_of_payment,remarks,gl_voucher,check_no,check_date,date,account_info) 
	VALUES ( ".db_escape($encashment_id).",
	 ".db_escape($emp_id).", 
	 ".db_escape($approve).",
	  ".db_escape($days).",
	   ".db_escape($f_year).",
	   ".db_escape(sql2date($payment_date)).",
	     ".db_escape($amount).",
	     ".db_escape($taxable_amount).",
	      ".db_escape($income_tax).",
	   
	     ".db_escape($mode_of_payment).",
	   ".db_escape($remarks).",
	 
	  ".db_escape($gl_voucher).",
	    ".db_escape($check_no).",
	     ".db_escape(date2sql($check_date)).",
	      ".db_escape(date2sql($date)).",
	   ".db_escape($account_info) . ")";

	db_query($sql,"The department could not be added");
}


function add_advance($date,$emp_id,$amount,$type,$remarks,$approve,$payroll_id,$payment_shedule,$payment,$f_year)
{
	$sql = "INSERT INTO ".TB_PREF."advance (date,emp_id,amount,type,remarks,approve,payroll_id,
    payment_shedule,payment,f_year) VALUES ( ".db_escape(sql2date($date)).", ".db_escape($emp_id).",
     ".db_escape($amount).", ".db_escape($type).", ".db_escape($remarks).",".db_escape($approve).",
     ".db_escape($payroll_id).",".db_escape($payment_shedule).",".db_escape($payment). ",".db_escape($f_year). ")";
   	db_query($sql,"The department could not be added");
}

function update_advance($selected_id,$date,$emp_id,$amount,$type,$remarks,$approve,$payroll_id,$payment_shedule,$payment,$f_year)
{
	$sql = "UPDATE ".TB_PREF."advance SET date=".db_escape(date2sql($date)).",
	emp_id=".db_escape($emp_id).",
	amount=".db_escape($amount).",
	type=".db_escape($type).",
	remarks=".db_escape($remarks).",
	approve=".db_escape($approve).",
	payroll_id=".db_escape($payroll_id).",
	payment_shedule=".db_escape($payment_shedule).",
	payment=".db_escape($payment).",
    f_year=".db_escape($f_year)."
	 WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The department could not be updated");
	}



function update_leave_encashment(
	$selected_id,
	$encashment_id,$emp_id,$approve,
	$days,$f_year,$payment_date,$amount,
	$taxable_amount,$income_tax,
	$mode_of_payment,$remarks,$gl_voucher,
	$check_no,$check_date,$date,$account_info



)

{
	$sql = " UPDATE ".TB_PREF."leave_encashment SET
	 date=".db_escape(date2sql($date)).",
	emp_name=".db_escape($emp_id).",
	amount=".db_escape($amount).",
	encashment_id=".db_escape($encashment_id).",
	remarks=".db_escape($remarks).",
	approve=".db_escape($approve).",
	days=".db_escape($days).",
	taxable_amount=".db_escape($taxable_amount).",
	income_tax=".db_escape($income_tax).",
	gl_voucher=".db_escape($gl_voucher).",
	check_no=".db_escape($check_no).",
	check_date=".db_escape(date2sql($check_date)).",
	payment_date=".db_escape(sql2date($payment_date)).",
	mode_of_payment=".db_escape($mode_of_payment).",
    f_year=".db_escape($f_year).",
     account_info=".db_escape($account_info)."
	 WHERE id = ".db_escape($selected_id);
	 
	 
	db_query($sql,"The department could not be updated");
}
	
function delete_increment($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."increment WHERE
	 id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}


function delete_leave_encashment($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."leave_encashment WHERE
	 id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}


function get_advance_no($emp_id,$date)
{
	
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."leave WHERE 
	emp_id =".db_escape($emp_id)."
	AND
	date=".db_escape(sql2date($date));
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_advances($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."advance";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get department");
}

function get_increment_data($selected_id)
{
	$sql = " SELECT  ".TB_PREF."employee.basic_salary,".TB_PREF."emp_allowance.emp_id,".TB_PREF."emp_allowance.allow_id 
	FROM ".TB_PREF."employee
	INNER JOIN ".TB_PREF."emp_allowance
	ON ".TB_PREF."employee.employee_id = ".TB_PREF."emp_allowance.emp_id where ".TB_PREF."employee.employee_id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	$row= db_fetch_row($result);
	return $row;
}
// -----------------------
function get_employee_allowances($selected_id){
	$sql = "SELECT amount  FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get group");

	$row= db_fetch_row($result);
	return $row[0];

}

function get_emp_name($selected_id){

	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get group");

	$row= db_fetch_row($result);
	return $row[0];

}
//-----------------------

function get_count($selected_id){

	$sql = "SELECT count(allow_id)  FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get group");
$row=db_fetch_row($result);
	return $row[0];
	
	
}


function get_increment($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."increment WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

//
//function get_advance($selected_id)
//{
//	$sql = "SELECT * FROM ".TB_PREF."advance WHERE id=".db_escape($selected_id);
//
//	$result = db_query($sql,"could not get department");
//	return db_fetch($result);
//}




function get_leave_encashment($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."leave_encashment WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}



function get_advance_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}

function get_employee_name($employee_id)
{
	$sql = "SELECT  emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
} 
//-----------------
function get_employee_inc($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}


?>