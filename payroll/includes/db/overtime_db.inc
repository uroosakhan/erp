<?php


function add_overtime( $over_time, $employee_id, $emp_dep,$date)
{
   
		$sql = "INSERT INTO  ".TB_PREF."overtime (`employee_id` ,
		   `over_time`,`emp_dept`,`date`)
		VALUES ( ".db_escape($employee_id).",".db_escape($over_time).",".db_escape($emp_dep).",".db_escape(date2sql($date)).");";
		db_query($sql, "could not get customer");
		
	
}
function delete_overtime($id)
{
	$sql="DELETE FROM ".TB_PREF."overtime WHERE id=".db_escape($id);
	
	db_query($sql,"The payroll could not be deleted");
}

function add_checkall($checkbox)
{
   
		$sql = "INSERT INTO 0_presence(`present`) VALUES (".db_escape($checkbox).")";
		db_query($sql, "could not get All");
		
	
}
function payroll_detail($emp_id)
{
	
	$sql = "SELECT ".TB_PREF."payroll.*  FROM ".TB_PREF."payroll 
	WHERE emp_id=".db_escape($emp_id);
	$result = db_query($sql, "Could't get payroll detail.");
	$myrow = db_fetch($result);
	return $myrow;
}

function update_overtime($present, $overtime, $absent, $employee_id, $monthid,$date)
{
	$sql = "UPDATE ".TB_PREF."overtime 
	SET  `over_time` =  ".db_escape($overtime)."
	WHERE
	employee_id=".db_escape($employee_id)." 
	AND date =".db_escape(date2sql($date));
			   
		db_query($sql, "could not get customer");

}


function get_employee_through_dept_id($dept_id)
{
	
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE emp_dept=".db_escape($dept_id);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}

function get_presence_through_dept_id($dept_id,$date)
{
	
	$sql = "SELECT * FROM ".TB_PREF."overtime WHERE emp_dept=".db_escape($dept_id)."
	AND
	date=".db_escape($date);
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}
function get_employees_acc_dept($dept)
{
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE emp_dept=".db_escape($dept);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}
function delete_presence($dept_id,$date)
{
	$sql="DELETE FROM ".TB_PREF."overtime WHERE emp_dept=".db_escape($dept_id)."
	AND  date=".db_escape($date) ;

	db_query($sql,"The payroll could not be deleted");
}

function check_month_duplication($emp_dept,$date)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."overtime WHERE emp_dept=".db_escape($emp_dept)."
	AND date =".db_escape(date2sql($date));
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function get_employee_that_exist_update($dept_id,$customer_id)
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
    ".TB_PREF."presence.emp_dept=".db_escape($dept_id) ;
	
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


function employee_attendance_detail($emp_id)
{
	
	$sql = "SELECT ".TB_PREF."presence.* FROM ".TB_PREF."presence 
	WHERE employee_id=".db_escape($emp_id);
	
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow;
}
//ANSAR
function employee_present_attendance_detail($emp_id)
{
	
	$sql ="SELECT   COUNT(`present`) As absent,`".TB_PREF."payroll`.*,`".TB_PREF."presence`.* FROM `".TB_PREF."presence`,".TB_PREF."payroll
WHERE ".TB_PREF."payroll.emp_id=".TB_PREF."presence.employee_id
AND
".TB_PREF."presence.`present`=".db_escape('0')."
AND
	".TB_PREF."presence.`date`>=".db_escape(2014-04-08)."
         AND 
       ".TB_PREF."presence.`date`<=".db_escape('2014-04-23');
	
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


function get_emp_rec_no($emp_dept)
{
	
	$sql = "SELECT COUNT(emp_id) FROM ".TB_PREF."payroll WHERE 
	dept_id =".db_escape($emp_dept);
	
	
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_attendance_count($date)
{
	$sql = "SELECT COUNT(date) FROM ".TB_PREF."presence WHERE 
	date=".db_escape($date);
	$result = db_query($sql, "Could not get attendance count.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function get_attendance_count($id)
{
	
	$sql = "SELECT ".TB_PREF."presence.id FROM ".TB_PREF."presence WHERE 
	id=".db_escape($id);
	$result = db_query($sql, "Could not get attendance count.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

?>