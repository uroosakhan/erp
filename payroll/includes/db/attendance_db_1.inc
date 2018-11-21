<?php


function add_attendance($present, $over_time, $absent, $employee_id, $emp_dep, $month_id)
{
   
		$sql = "INSERT INTO  ".TB_PREF."presence (`employee_id` ,
		 `present` ,  `over_time`, `absent` , `emp_dept`, `month_id` )
		VALUES ( ".db_escape($employee_id).", ".db_escape($present).",  ".db_escape($over_time).",  ".db_escape($absent).", ".db_escape($emp_dep).", ".db_escape($month_id).");";
		db_query($sql, "could not get customer");
	
}


function update_attendance($present, $overtime, $absent, $id)
{
	$sql = " ".TB_PREF."presence 
	SET   `present` =  ".db_escape($present).",
		  `over_time` =  ".db_escape($overtime).",
		  `absent`  =   ".db_escape($absent)."
	WHERE
	id=".db_escape($id);
			   
		db_query($sql, "could not get customer");

}


/*function get_employee_through_dept_id($dept_id)
{
	
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE emp_dept=".db_escape($dept_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}*/

function get_employees_acc_dept($dept)
{
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE emp_dept=".db_escape($dept);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function check_month_duplication($emp_dept, $month)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."presence WHERE emp_dept=".db_escape($emp_dept)." AND month_id=".db_escape($month);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function get_employee_that_exist_update($dept_id, $month)
{
	
	$sql1 = " SELECT ".TB_PREF."employee.emp_name, ".TB_PREF."presence.* 
	FROM 
	".TB_PREF."employee, ".TB_PREF."presence
    WHERE 
    ".TB_PREF."employee.employee_id= ".TB_PREF."presence.employee_id
    AND
    ".TB_PREF."employee.emp_dept= ".TB_PREF."presence.emp_dept
    AND
    ".TB_PREF."presence.emp_dept=".db_escape($customer_id);
	
	$sql = " SELECT ".TB_PREF."presence.* 
	FROM 
	".TB_PREF."presence
    WHERE 
    ".TB_PREF."presence.emp_dept=".db_escape($dept_id)." AND  ".TB_PREF."presence.month_id=".db_escape($month) ;
	
	$myrow = db_query($sql, "could not get customer");
	
	return $myrow;
}


function get_employee_name($employee_id)
{
	
	$sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow['0'];
}


function employee_attendance_detail($emp_id, $month)
{
	
	$sql = "SELECT ".TB_PREF."presence.* FROM ".TB_PREF."presence 
	WHERE employee_id=".db_escape($emp_id)." AND
	month_id=".db_escape($month);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow;
}

function get_employee_existence($emp_dept)
{
	
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE 
	emp_dept=".db_escape($emp_dept);
	$result = db_query($sql, "could not get customer");
	$myrow = db_fetch($result);
	return $myrow['0'];
}


function get_emp_rec_no($emp_dept, $month)
{
	
	$sql = "SELECT COUNT(emp_id) FROM ".TB_PREF."payroll WHERE 
	dept_id =".db_escape($emp_dept)."
	AND
	month=".db_escape($month);
	
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_attendance_count($emp_dept, $month)
{
	
	$sql = "SELECT COUNT(emp_dept) FROM ".TB_PREF."presence WHERE 
	emp_dept=".db_escape($emp_dept)."
	AND
	month_id=".db_escape($month);
	
	$result = db_query($sql, "Could not get attendance count.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

?>