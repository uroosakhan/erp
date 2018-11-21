<?php

function add_emp_division($description,$project)
{
	$sql = "INSERT INTO ".TB_PREF."divison (description,project) VALUES (".db_escape($description) . ",".db_escape($project)." )";
   	db_query($sql,"The division could not be added");
}

function update_emp_division($selected_id, $description,$project)
{
	$sql = "UPDATE ".TB_PREF."divison SET description=".db_escape($description).",project=".db_escape($project)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The division could not be updated");
	}
	
	function delete_emp_division($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."divison WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete division");
}

function get_division($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."divison";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get division");
}

function get_emp_division_123($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."divison WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get division");
	return db_fetch($result);
}

function get_emp_division_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."divison WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get division");
	$row = db_fetch($result);
	return $row[0];
}



?>