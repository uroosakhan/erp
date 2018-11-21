<?php

function add_emp_deduction($description)
{
	$sql = "INSERT INTO ".TB_PREF."deduction (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The deduction could not be added");
}

function update_emp_deduction($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."deduction SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The deduction could not be updated");
	}
	
	function delete_emp_deduction($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."deduction WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete deduction");
}

function get_emp_deductions($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."deduction";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get deduction");
}

function get_emp_deduction($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."deduction WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get deduction");
	return db_fetch($result);
}

function get_emp_dept_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."deduction WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get deduction");
	$row = db_fetch($result);
	return $row[0];
}
function refresh($loc)
{
echo "<meta http-equiv='refresh' content='0'>";
}


?>