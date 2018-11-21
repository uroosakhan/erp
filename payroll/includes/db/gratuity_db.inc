<?php

function add_gratuity($encashment_id,$emp_id,$approve,
							  $days,$f_year,$payment_date,$amount,
							  $taxable_amount,$income_tax,
							  $mode_of_payment,$remarks,$gl_voucher,
							  $check_no,$check_date,$date,$tax_intallments,$gl_it,$account_info
)  





{
	$sql = " INSERT INTO ".TB_PREF."gratuity (	encashment_id,emp_name,
	approve,f_year,payment_date,
	amount,taxable_amount,	income_tax,
	mode_of_payment,remarks,gl_voucher,check_no,check_date,date,tax_installments,gl_it,account_info) 
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
	      ".db_escape(date2sql($date)).",
	      ".db_escape($tax_intallments).",
	      ".db_escape($gl_it).",
	   ".db_escape($account_info) . ")";

	db_query($sql,"The department could not be added");
}


function add_advance($date,$emp_id,$amount,$type,$remarks,$approve,$payroll_id,$payment_shedule,$payment,$f_year)
{
	$sql = "INSERT INTO ".TB_PREF."advance (date,emp_id,amount,type,
	remarks,approve,payroll_id,
    payment_shedule,payment,f_year)
     VALUES
     ( ".db_escape(sql2date($date)).", ".db_escape($emp_id).",
     ".db_escape($amount).", ".db_escape($type).", ".db_escape($remarks).",
     ".db_escape($approve).",
     ".db_escape($payroll_id).",".db_escape($payment_shedule).",
     ".db_escape($payment). ",".db_escape($f_year). ")";
   	db_query($sql,"The department could not be added");
}

function update_grtuity(
	$selected_id,
	$encashment_id,$emp_id,$approve,
	$f_year,$payment_date,$amount,
	$taxable_amount,$income_tax,
	$mode_of_payment,$remarks,$gl_voucher,
	$check_no,$check_date,$date,$tax_intallments,$gl_it,$account_info


	

)

{
	$sql = " UPDATE ".TB_PREF."gratuity SET
	 date=".db_escape(date2sql($date)).",
	emp_name=".db_escape($emp_id).",
	amount=".db_escape($amount).",
	encashment_id=".db_escape($encashment_id).",
	remarks=".db_escape($remarks).",
	approve=".db_escape($approve).",
	
	taxable_amount=".db_escape($taxable_amount).",
	income_tax=".db_escape($income_tax).",
	gl_voucher=".db_escape($gl_voucher).",
	check_no=".db_escape($check_no).",
	check_date=".db_escape(date2sql($check_date)).",
	payment_date=".db_escape(date2sql($payment_date)).",
	mode_of_payment=".db_escape($mode_of_payment).",
    f_year=".db_escape($f_year).",
    tax_installments=".db_escape($tax_intallments).",
    gl_it=".db_escape($gl_it).",
     account_info=".db_escape($account_info)."
	 WHERE id = ".db_escape($selected_id);
	     add_audit_trail_payroll(ST_JOURNAL, $_POST['emp_name'], $_POST['date'],"Updated");

	 
	db_query($sql,"The department could not be updated");
}




function delete_gratuity($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."gratuity WHERE
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

function get_gratuity($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."gratuity WHERE id=".db_escape($selected_id);

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

function get_total_no_of_allowed_leaves2()
{
	$sql="SELECT * FROM ".TB_PREF."leave_type where id='2' ";
	$db = db_query($sql,'error');
	return db_fetch($db);
//	return $ft[0];

}
function get_no_of_leave2($emp_id)
{
	$sql="SELECT COUNT(no_of_leave) FROM ".TB_PREF."leave where emp_id='$emp_id' AND leave_type='2' ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];

}
function get_employee_data3($emp_id)
{
	$sql="select * FROM ".TB_PREF."employee where 	employee_id=".db_escape($emp_id)." ";
	$db = db_query($sql,'Cant get Employee data');
	return db_fetch($db);
}
function get_advance_against_gratuity($employee_id,$f_year)
{
	$sql="SELECT amount FROM ".TB_PREF."advance where emp_id=".db_escape($employee_id)." 
			AND f_year=".db_escape($f_year)." AND advance_on_base_of='1' AND payroll_id='0'";
	$db = db_query($sql,'Cant get Employee data');
	$ft=  db_fetch($db);
	return $ft[0];
}
function get_gratuity_last_id()
{
	$sql="SELECT MAX(id) FROM ".TB_PREF."gratuity ";
	$db = db_query($sql,'Cant get Employee data');
	$ft=  db_fetch($db);
	return $ft[0];

}
function get_employee_basic_salary($emp_id)
{
	$sql="SELECT basic_salary FROM ".TB_PREF."employee where 	employee_id=".db_escape($emp_id)." ";
	$db = db_query($sql,'Cant get Employee data');
	$ft=  db_fetch($db);
	return $ft[0];

}
function add_advance_deduction($employee_id,$advance_amnt,$date,$bank,$f_year,$gratuity_id,$remarks)
{
	$sql="INSERT INTO ".TB_PREF."advance (date,emp_id,amount,approve,f_year,bank_account,gratuity_id) VALUE 
			(".db_escape(sql2date($date)).",".db_escape($employee_id).",".db_escape($advance_amnt).",'1',".db_escape($f_year)."
			,".db_escape($bank).",".db_escape($gratuity_id).")";
	db_query($sql,'Cant not insert');
}

?>