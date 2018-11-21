<?php
//saad  
function add_gaz_holiday($description,$date, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."gazetted_holidays (description,date,inactive)
			VALUES ("
		  .db_escape($description) . ", "
		  .db_escape(date2sql($date)) . ", "
		  .db_escape($inactive)
		  . ")";
   	db_query($sql,"The Description of the query failed");
}


function update_gaz_holiday($selected_id,$description,$date)
{
	$sql = "UPDATE ".TB_PREF."gazetted_holidays SET description=".db_escape($description).",
		date=".db_escape(date2sql($date)) ."	
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The Description Name could not be updated");
}

function delete_gaz_holiday($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."gazetted_holidays WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}
function get_gaz_holidays($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."gazetted_holidays WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_gaz_holiday($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."gazetted_holidays";
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