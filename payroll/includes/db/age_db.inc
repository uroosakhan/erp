<?php
//saad  
function add_age($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."age (description,inactive)
			VALUES ("
		  .db_escape($description) . ", "
		  .db_escape($inactive)
		  . ")";
   	db_query($sql,"The Description of the query failed");
}


function update_age($selected_id,$description, $inactive)
{
	$sql = "UPDATE ".TB_PREF."age SET description=".db_escape($description).",
		inactive=".db_escape($inactive)."	
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The Description Name could not be updated");
}

function delete_age($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."age WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}
function get_ages($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."age WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_age($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."age";
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