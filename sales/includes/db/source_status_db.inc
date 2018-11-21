<?php

//saad

function add_source_status($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."source_status(description, inactive)
		VALUES (".db_escape($description) . ","
		         .db_escape($inactive) . ")";
   	db_query($sql,"The insert of the source failed");
}

function update_source_status($selected_id,$description)
{
	$sql = "UPDATE ".TB_PREF."source_status 
	SET description=".db_escape($description) . "
	WHERE ".TB_PREF."source_status.id = ".db_escape($selected_id);
   	db_query($sql,"The update of the source failed");
}

function delete_source_status($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."source_status WHERE id=".db_escape($selected_id);
	db_query($sql,"The source could not be deleted");
}

function get_source_statuses($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."source_status";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get source");
}

function get_source_status($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."source_status WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_description1($id)
{
	$sql = "SELECT description FROM ".TB_PREF."source_status WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
?>