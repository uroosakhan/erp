<?php

//saad

function add_query_status($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."query_status (description, inactive)
		VALUES (".db_escape($description) . ","
		         .db_escape($inactive) . ")";
   	db_query($sql,"The insert of the sales person failed");
}

function update_query_status($selected_id,$description)
{
	$sql = "UPDATE ".TB_PREF."query_status 
	SET description=".db_escape($description) . "
	WHERE ".TB_PREF."query_status.id = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}

function delete_query_status($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."query_status WHERE id=".db_escape($selected_id);
	db_query($sql,"The sales-person could not be deleted");
}

function get_query_statuses($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."query_status";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_query_status($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."query_status WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_description($id)
{
	$sql = "SELECT description FROM ".TB_PREF."query_status WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
?>