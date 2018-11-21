<?php
//saad  
function add_query($date, $name, $business_name, $care_of,
	$status, $source_status, $phone1, $phone2, $mobile, $email, $remarks, $user,$stock_id, $package)
{
	//$start_date_db = date2sql($date);
   // $end_date_db = date2sql($end_date);
	$sql = "INSERT INTO ".TB_PREF."query (date, name, business_name, care_of, status,source_status, phone1, phone2, mobile, email, remarks, user,stock_id, package)
		VALUES (".db_escape(date2sql($date)) . ", "
		 // .db_escape($end_date_db) . ", "
		  .db_escape($name) . ", "
		  .db_escape($business_name) . ", "
		  .db_escape($care_of).", "
		  .db_escape($status).", "
		  .db_escape($source_status).", "
		  .db_escape($phone1).", "
		  .db_escape($phone2).", "
		  .db_escape($mobile).", "
		  .db_escape($email).", "
	
		  .db_escape($remarks) . ","
		  .db_escape($user) . ",".db_escape($stock_id)  . ",".db_escape(date2sql($package)) . " 
		  
		  )";
   	db_query($sql,"The insert of the query failed");
}

function update_query($selected_id, $date, $name, $business_name, $care_of, $status,$source_status, $phone1, $phone2, $mobile, $email,  $remarks, $user,$stock_id, $package)
{
	$sql = "UPDATE ".TB_PREF."query SET date=".db_escape(date2sql($date)) . ",
		name=".db_escape($name) . ",
		business_name=".db_escape($business_name) . ",
		care_of=".db_escape($care_of).",
		status=".db_escape($status).",
		source_status=".db_escape($source_status).",
		phone1=".db_escape($phone1).",
		phone2=".db_escape($phone2).",
		mobile=".db_escape($mobile).",
		email=".db_escape($email).",
	
		remarks=".db_escape($remarks).",
		user=".db_escape($user).",
			stock_id=".db_escape($stock_id).",
			package=".db_escape(date2sql($package))."
		
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The update of the query failed");
}

function delete_query($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."query WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}

function get_querys($show_inactive)
{
	//$sql = "SELECT * FROM ".TB_PREF."query";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_query($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."query WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_query_description($id)
{
	$sql = "SELECT query_description FROM ".TB_PREF."query WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

//saad 5 may
?>