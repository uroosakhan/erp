<?php
//saad  
function add_final_result($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."final_result (description,inactive)
			VALUES ("
		  .db_escape($description) . ", "
		  .db_escape($inactive)
		  . ")";
   	db_query($sql,"The Description of the query failed");
}


function update_final_result($selected_id,$description, $inactive)
{
	$sql = "UPDATE ".TB_PREF."final_result SET description=".db_escape($description).",
		inactive=".db_escape($inactive)."	
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The Description Name could not be updated");
}

function delete_final_result($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."final_result WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}
function get_final_results($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."final_result WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_final_result($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."final_result";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function check_ref_duplication($ref_id)
{
	$sql = "SELECT COUNT(description) FROM ".TB_PREF."final_result WHERE description=".db_escape($ref_id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
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