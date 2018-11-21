<?php

//function add_portal($date, $customer, $server, $support,
//					$industry, $server_url, $id_no, $password, $db_user,
//					$db_password,$memo,$terms,$contact,$care_of,
//					$alt_contact,$email,$reg_address,$month,$salesman)
//{
//	$sql = "INSERT INTO ".TB_PREF."portal (date, customer, server, support,
//		industry,salesman, server_url, id_no, password, db_user, db_password,memo,terms,
//		contact,alt_contact,email,reg_address)
//		VALUES (".db_escape($date) . ", "
//		  .db_escape($customer) . ", "
//		  .db_escape($server) . ", "
//		  .db_escape($support) . ", "
//		.db_escape($industry) . ", "
//		.db_escape($server_url) . ", "
//		.db_escape($id_no) . ", "
//		.db_escape($password) . ", "
//		.db_escape($db_user) . ", "
//		.db_escape($db_password).
//		", "
//		.db_escape($memo).
//		", "
//		.db_escape($terms).
//		", "
//		.db_escape($contact).
//		", "
//		.db_escape($care_of).
//		", "
//		.db_escape($alt_contact).
//		", "
//		.db_escape($email).
//		", "
//		.db_escape($reg_address).
//		", ".db_escape($salesman).
//")";
//   	db_query($sql,"The insert of the portal failed");
//}
/*
function update_portal($selected_id, $salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $provision, $break_pt, $provision2)
{
	$sql = "UPDATE ".TB_PREF."salesman SET salesman_name=".db_escape($salesman_name) . ",
		salesman_phone=".db_escape($salesman_phone) . ",
		salesman_fax=".db_escape($salesman_fax) . ",
		salesman_email=".db_escape($salesman_email) . ",
		provision=".$provision.",
		break_pt=".$break_pt.",
		provision2=".$provision2."
		WHERE salesman_code = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}
*/
function delete_portal($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."portal WHERE id=".db_escape($selected_id);
	db_query($sql,"This Portal could not be deleted");
}

/*function get_portal()
{
	$sql = "SELECT * FROM ".TB_PREF."portal";
	//$sql .= " ";
	return db_query($sql,"could not get portal");
}*/

function get_portal_new($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."portal WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}
function get_portals()
{
	$sql = "SELECT * FROM ".TB_PREF."portal";
	
	return db_query($sql,"could not get sales persons");
}
//--------------------------------------------

function update_portal($id, $date, $customer, $server, $support, $industry, $server_url,
						 $id_no, $password, $db_user, $db_password,
					   $memo,$terms,$contact ,$careof,$alt_contact,
					   $email,$reg_address,
					   $month,$salesman)
{
	$sql = " UPDATE ".TB_PREF."portal SET date=".db_escape($date) . ",
		customer=".db_escape($customer) . ",
		server=".db_escape($server) . ",
		support=".db_escape($support) . ",
		industry=".db_escape($industry) .",
		server_url=".db_escape($server_url) .",
		id_no=".db_escape($id_no).",
		password=".db_escape($password).",
		db_user=".db_escape($db_user).",
		db_password=".db_escape($db_password).",
memo=".db_escape($memo).",
terms=".db_escape($terms).",
contact=".db_escape($contact).",
care_of=".db_escape($careof).",
alt_contact=".db_escape($alt_contact).",
email=".db_escape($email).",
reg_address=".db_escape($reg_address).",
salesman=".db_escape($salesman)."

		WHERE id = ".db_escape($id);
	db_query($sql,"The update of the Portal failed");
}

/*
function get_salesman($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_salesman_name($id)
{
	$sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

*/

?>