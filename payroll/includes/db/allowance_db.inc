<?php

function add_emp_allowance($description)
{
	$sql = "INSERT INTO ".TB_PREF."allowance (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The allowance could not be added");
}

function update_emp_allowance($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."allowance SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The allowance could not be updated");
	}
	
	function delete_emp_allowance($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."allowance WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function get_emp_allowances($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."allowance";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get allowance");
}

function get_emp_allowance($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."allowance WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get allowance");
	return db_fetch($result);
}

function get_emp_dept_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function refresh($loc)
{
echo "<meta http-equiv='refresh' content='0'>";
}


?>