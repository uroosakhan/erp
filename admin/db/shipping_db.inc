<?php


//--------------------------------------------------------------------------------------

function add_shipper($shipper_name, $contact, $phone, $phone2, $address, $shipping_account)
{
	$sql = "INSERT INTO ".TB_PREF."shippers (shipper_name, contact, phone, phone2, address, shipping_account)
		VALUES (" . db_escape($shipper_name) . ", " .
		db_escape($contact). ", " .
		db_escape($phone). ", " .
		db_escape($phone2). ", " .
		db_escape($address) . ", " .
		db_escape($shipping_account) . ")";

	db_query($sql,"The Shipping Company could not be added");
}

//--------------------------------------------------------------------------------------

function update_shipper($selected_id, $shipper_name, $contact, $phone, $phone2, $address, $shipping_account)
{
	$sql = "UPDATE ".TB_PREF."shippers SET shipper_name=" . db_escape($shipper_name). " ,
		contact = " . db_escape($contact). " ,
		phone = " . db_escape($phone). " ,
		phone2 = " . db_escape($phone2). " ,
		address =" . db_escape($address). " ,
		shipping_account =" . db_escape($shipping_account). "
		WHERE shipper_id = ".db_escape($selected_id);

	db_query($sql,"The shipping company could not be updated");
}

//--------------------------------------------------------------------------------------

function delete_shipper($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($selected_id);
	
	db_query($sql,"could not delete shipper");
}

//--------------------------------------------------------------------------------------

function get_shippers($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."shippers";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY shipper_id";
	
	return db_query($sql,"could not get shippers");
}

//--------------------------------------------------------------------------------------

function get_shipper($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get shipper");
	return db_fetch($result);
}

//--------------------------------------------------------------------------------------

function get_gl_account_name_for_shippment($code)
{
	$sql = "SELECT account_name from ".TB_PREF."chart_master WHERE account_code=".db_escape($code);

	$result = db_query($sql,"could not retreive the account name for $code");

	$row = db_fetch_row($result);
	return $row[0];

}