<?php

function add_employee_location($description,$division)
{
	$sql = "INSERT INTO ".TB_PREF."employee_location (description,division) VALUES (".db_escape($description) . ",".db_escape($division)." )";
   	db_query($sql,"The division could not be added");
}

function update_employee_location($selected_id, $description,$division)
{
	$sql = "UPDATE ".TB_PREF."employee_location SET description=".db_escape($description).",division=".db_escape($division)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The division could not be updated");
	}
	
	function delete_employee_location($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."employee_location WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete division");
}

function get_employee_location($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_location";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get division");
}

function get_employee_location_123($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_location WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get division");
	return db_fetch($result);
}

function get_employee_location_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."employee_location WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get division");
	$row = db_fetch($result);
	return $row[0];
}



?>