<?php

function add_emp_leave_type($description, $leave_days, $emp_grade)
{
	$sql = " INTO ".TB_PREF."leave_type (description,leave_days,emp_grade) 
	          VALUES (".db_escape($description) . ",".db_escape($leave_days) . ",".db_escape($emp_grade).")";
   	db_query($sql, "The department could not be added");
}

function update_emp_leave_type($selected_id, $description,$leave_days,$emp_grade)
{
	$sql = "UPDATE ".TB_PREF."leave_type SET description=".db_escape($description).",
	leave_days=".db_escape($leave_days)." , emp_grade=".db_escape($emp_grade)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The department could not be updated");
}
	
function delete_emp_leave_type($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."leave_type WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function get_emp_leave_types($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."leave_type";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get department");
}

function get_emp_leave_type($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."leave_type WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_emp_leave_type_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}



?>