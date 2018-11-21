<?php

//saad

function add_status($description, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."status (description, inactive)
		VALUES (".db_escape($description) . ", "
		         .db_escape($inactive) . ")";
   	db_query($sql,"The insert of the sales person failed");
}
function add_pstatus($status, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."pstatus (status, inactive)
		VALUES (".db_escape($status) . ", "
		.db_escape($inactive) . ")";
	db_query($sql,"The insert status failed");
}
function add_calltype($call_type, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."call_type (call_type, inactive)
		VALUES (".db_escape($call_type) . ", "
		.db_escape($inactive) . ")";
	db_query($sql,"The insert call type failed");
}
function add_duration( $duration,$inactive)
{
	$sql = "INSERT INTO ".TB_PREF."duration (duration,inactive)
		VALUES (".db_escape($duration) . ", "
		.db_escape($inactive) . ")";
	db_query($sql,"The insert duration failed");
}

//function add_task_type( $task_type,$check,$inactive)
//{
//	$sql = " INTO ".TB_PREF."task_type (task_type,check_type,inactive)
//		VALUES (".db_escape($task_type)  . ",".db_escape($check)  . ",".db_escape($inactive) . ")";
//	if(!$check)
//	db_query($sql,"The insert task type failed");
//}
function add_task_type($task_type,$check)
{
	$sql = "INSERT INTO ".TB_PREF."task_type (task_type,check_type) VALUES (".db_escape($task_type) . ",".db_escape($check) . ")";
	db_query($sql,"The sales group could not be added");
}

function update_status($selected_id,$description)
{
	$sql = "UPDATE ".TB_PREF."status 
	SET description=".db_escape($description) . "
	WHERE ".TB_PREF."status.id = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}
function update_pstatus($selected_id,$status)
{
	$sql = "UPDATE ".TB_PREF."pstatus 
	SET status=".db_escape($status) . "
	WHERE ".TB_PREF."pstatus.id = ".db_escape($selected_id);
	db_query($sql,"The update of the status failed");
}
function update_calltype($selected_id,$call_type)
{
	$sql = "UPDATE ".TB_PREF."call_type 
	SET call_type=".db_escape($call_type) . "
	WHERE ".TB_PREF."call_type.id = ".db_escape($selected_id);
	db_query($sql,"The update of the call type failed");
}

function get_calltype($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."call_type WHERE id=".db_escape($selected_id);
	$result = db_query($sql, "error");

	return db_fetch($result);

}
function update_duration($selected_id,$duration)
{
	$sql = "UPDATE ".TB_PREF."duration 
	SET duration=".db_escape($duration) . "
	WHERE ".TB_PREF."duration.id = ".db_escape($selected_id);
	db_query($sql,"The update duration failed");
}
function update_task_type($selected_id,$task_type,$check_type)
{
	$sql = "UPDATE ".TB_PREF."task_type
	SET task_type=".db_escape($task_type) . ",check_type=".db_escape($check_type) . "
	WHERE ".TB_PREF."task_type.id = ".db_escape($selected_id);
	db_query($sql,"The update task type failed");
}
function delete_duration($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."duration WHERE id=".db_escape($selected_id);
	db_query($sql,"The duration could not be deleted");
}
function delete_task_type($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."task_type WHERE id=".db_escape($selected_id);
	db_query($sql,"The task type could not be deleted");
}
function delete_status($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."status WHERE id=".db_escape($selected_id);
	db_query($sql,"The selected person could not be deleted");
}
function delete_pstatus($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."pstatus WHERE id=".db_escape($selected_id);
	db_query($sql,"The status could not be deleted");
}
function delete_calltype($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."call_type WHERE id=".db_escape($selected_id);
	db_query($sql,"The call type could not be deleted");
}
function get_statuses($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."status";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}
function get_pstatuses($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."pstatus";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get status");
}
function get_calltypes($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."call_type";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get call type");
}
function get_durations($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."duration";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get duration");
}

function get_task_type($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."task_type";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get task type");
}
function get_status($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."status WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}
function get_pstatus($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."pstatus WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get status");
	return db_fetch($result);
}
function get_duration($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."duration WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get duration");
	return db_fetch($result);
}
function get_task_types($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."task_type WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get task type");
	return db_fetch($result);
}
function get_description($id)
{
	$sql = "SELECT description FROM ".TB_PREF."status WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

?>