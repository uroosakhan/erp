<?php
//saad  
function add_cust_info($name, $title, $contact_person, $credit_days, $phone,
                       $phone2, $credit_limit, $email, $address, $address2, $city,
                       $state, $country, $str_no, $payment_terms, $dimension_id)
{
	//$start_date_db = date2sql($date);
   // $end_date_db = date2sql($end_date);
	$sql = "INSERT INTO ".TB_PREF."cust_info (name, title, contact_person, credit_days, phone,
	phone2, credit_limit, email, address, address2, city, state, country, str_no, payment_terms,
	dimension_id)
		VALUES (".db_escape($name) . ", "
		  .db_escape($title) . ", "
		  .db_escape($contact_person) . ", "
		  .db_escape($credit_days) . ", "
		  .db_escape($phone).", "
		  .db_escape($phone2).", "
		  .db_escape($credit_limit).", "
		  .db_escape($email).", "
		  .db_escape($address).", "
		  .db_escape($address2).", "
		  .db_escape($city).", "
		  .db_escape($state) . ","
		  .db_escape($country) . ","
          .db_escape($str_no)  . ","
          .db_escape($payment_terms)  . ","
          .db_escape($dimension_id) . " 
		  )";
   	db_query($sql,"The insert of the customer info failed");
}

function update_cust_info($selected_id, $name, $title, $contact_person, $credit_days, $phone, $phone2,
                      $credit_limit, $email, $address, $address2, $city, $state, $country, $str_no,
                      $payment_terms, $dimension_id)
{
	$sql = "UPDATE ".TB_PREF."cust_info SET name=".db_escape($name) . ",
		title=".db_escape($title) . ",
		contact_person=".db_escape($contact_person) . ",
		credit_days=".db_escape($credit_days).",
		phone=".db_escape($phone).",
		phone2=".db_escape($phone2).",
		credit_limit=".db_escape($credit_limit).",
		email=".db_escape($email).",
		address=".db_escape($address).",
		address2=".db_escape($address2).",
		city=".db_escape($city).",
		state=".db_escape($state).",
		country=".db_escape($country).",
		str_no=".db_escape($str_no)."
		payment_terms=".db_escape($payment_terms)."
		dimension_id=".db_escape($dimension_id)."
		
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The update of the customer info failed");
}

function delete_cust_info($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."cust_info WHERE id=".db_escape($selected_id);
	db_query($sql,"The customer info could not be deleted");
}

function get_querys($show_inactive)
{
	//$sql = "SELECT * FROM ".TB_PREF."query";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_cust_infor($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."cust_info WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get customer info");
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