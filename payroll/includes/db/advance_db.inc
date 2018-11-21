<?php

function add_leave_encashment($encashment_id,$emp_id,$approve,
							  $days,$f_year,$payment_date,$amount,
							  $taxable_amount,$income_tax,
							  $mode_of_payment,$remarks,$gl_voucher,
							  $check_no,$check_date,$account_info)
{
	$sql = "INSERT INTO ".TB_PREF."leave_encashment (encashment_id,emp_name,
	approve,f_year,payment_date,
	amount,taxable_amount,	income_tax,
	mode_of_payment,remarks,gl_voucher,check_no,check_date,days,account_info) 
	VALUES ( ".db_escape($encashment_id).",
	 ".db_escape($emp_id).", 
	 ".db_escape($approve).",
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
	      ".db_escape($days).",
	   ".db_escape($account_info) . ")";

	db_query($sql,"The department could not be added");
}


function add_advance($date,$emp_id,$amount,$type,$remarks,$approve,$payroll_id,
					 $payment_shedule,$payment,$f_year,$mode_of_payment,$cheque_no,$cheque_date,$bank_account,$advance_on_base_of,$month)
{
	$sql = "INSERT INTO ".TB_PREF."advance (date,emp_id,amount,type,remarks,approve,payroll_id,
    payment_shedule,payment,f_year,mode_of_payment,cheque_no,cheque_date,bank_account,advance_on_base_of,month)
    VALUES ( ".db_escape(sql2date($date)).", ".db_escape($emp_id).",
     ".db_escape($amount).", ".db_escape($type).", ".db_escape($remarks).",".db_escape(1).",
     ".db_escape($payroll_id).",".db_escape($payment_shedule).",
     ".db_escape($payment). ",".db_escape($f_year). ",".db_escape($mode_of_payment). ",".db_escape($cheque_no). ",
     ".db_escape(sql2date($cheque_date)). ",
     ".db_escape($bank_account). ",".db_escape($advance_on_base_of). ",".db_escape($month). ")";
	
   	db_query($sql,"The department could not be added");
}

function update_advance($selected_id,$date,$emp_id,$amount,$type,$remarks,
						$approve,$payroll_id,$payment_shedule,$payment,$f_year,$mode_of_payment,$cheque_no,$cheque_date,$bank_account)
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
    f_year=".db_escape($f_year).",
    mode_of_payment=".db_escape($mode_of_payment).",
    cheque_no=".db_escape($cheque_no).",
    cheque_date=".db_escape(sql2date($cheque_date)).",
    bank_account=".db_escape($bank_account)."
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
	 
	     add_audit_trail_payroll(ST_JOURNAL, $emp_id, $_POST['date'],"Updated");

	db_query($sql,"The department could not be updated");
}
	
function delete_advance($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."advance WHERE
	 id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function delete_advance_against_leave_encashment($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."advance WHERE
	 leave_encashment_id=".db_escape($selected_id)." ";
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

function get_advance($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."advance WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}


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

function get_employee_advance_bank_account($employee_id)
{
	$sql = "SELECT advance_account FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	$ft = db_fetch($result);
	return $ft[0];
}
?>