<?php
//saad  
function add_nationality($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."nationality (description,inactive)
			VALUES ("
		  .db_escape($description) . ", "
		  .db_escape($inactive)
		  . ")";
   	db_query($sql,"The Description of the query failed");
}


function update_nationality($selected_id,$description, $inactive)
{
	$sql = "UPDATE ".TB_PREF."nationality SET description=".db_escape($description).",
		inactive=".db_escape($inactive)."	
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The Description Name could not be updated");
}

function delete_nationality($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."nationality WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}
function get_nationalitys($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."nationality WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_nationality($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."nationality";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}
/*
function get_apprt_name($id)
{
	$sql = "SELECT appartment_name FROM ".TB_PREF."appartment WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
*/
?>