<?php

function add_emp_project($description)
{
	$sql = "INSERT INTO ".TB_PREF."project(description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The project could not be added");
}

function update_emp_project($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."project SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The project could not be updated");
	}
	
	function delete_emp_project($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."project WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete project");
}

function get_emp_project($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."project";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get project");
}

function get_emp_project_($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."project WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get project");
	return db_fetch($result);
}

function get_emp_project_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."project WHERE id= ".db_escape($dept_id);
	$result = db_query($sql, "could not get project");
	$row = db_fetch($result);
	return $row[0];
}



?>