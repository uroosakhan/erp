<?php

function update_gate_pass1($selected_id , $gate_pass_no, $driver_name, $vehicle_no , $delivery_no, $gate_pass_date)
{
    $sql = "UPDATE ".TB_PREF."multiple_gate_pass SET gate_pass_no=".db_escape($gate_pass_no) . ",
		driver_name=".db_escape($driver_name) . ",
		vehicle_no=".db_escape($vehicle_no) . ",
		delivery_no=".db_escape($delivery_no) . ",
		gate_pass_date=".db_escape(date2sql($gate_pass_date))."
		WHERE id = ".db_escape($selected_id);
    db_query($sql,"The update of the sales person failed");
}
function delete_gate_pass1($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."multiple_gate_pass WHERE gate_pass_no=".db_escape($selected_id);
    db_query($sql,"The sales-person could not be deleted");
}

function get_gate_pass()
{
    $sql = "SELECT * FROM ".TB_PREF."multiple_gate_pass";
    return db_query($sql,"could not get sales persons");
}

function get_gatepass($selected_id)
{
    //display_error($selected_id);
    $sql = "SELECT *  FROM ".TB_PREF."multiple_gate_pass WHERE id =".db_escape($selected_id);

    $result = db_query($sql,"could not get sales person");
    return db_fetch($result);
}

function add_h_combo3($description)
{
    $sql = "INSERT INTO ".TB_PREF."h_combo3 (description) VALUES (".db_escape($description) . ")";
    db_query($sql,"The combo could not be added");
}

function update_h_combo3($selected_id, $description)
{
    $sql = "UPDATE ".TB_PREF."h_combo3 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
    db_query($sql,"The combo could not be updated");
}

function delete_h_combo3($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."h_combo3 WHERE combo_code=".db_escape($selected_id);
    db_query($sql,"could not delete combo");
}

function get_h_combo3($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo3";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get combo");
}

function get_comboh_code3($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo3 WHERE combo_code=".db_escape($selected_id);

    $result = db_query($sql,"could not get combo");
    return db_fetch($result);
}





function add_h_combo2($description)
{
    $sql = "INSERT INTO ".TB_PREF."h_combo2 (description) VALUES (".db_escape($description) . ")";
    db_query($sql,"The combo could not be added");
}

function update_h_combo2($selected_id, $description)
{
    $sql = "UPDATE ".TB_PREF."h_combo2 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
    db_query($sql,"The combo could not be updated");
}

function delete_h_combo2($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."h_combo2 WHERE combo_code=".db_escape($selected_id);
    db_query($sql,"could not delete combo");
}

function get_h_combo2($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo2";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get combo");
}

function get_comboh_code2($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo2 WHERE combo_code=".db_escape($selected_id);

    $result = db_query($sql,"could not get combo");
    return db_fetch($result);
}


function add_h_combo1($description)
{
    $sql = "INSERT INTO ".TB_PREF."h_combo1 (description) VALUES (".db_escape($description) . ")";
    db_query($sql,"The combo could not be added");
}

function update_h_combo1($selected_id, $description)
{
    $sql = "UPDATE ".TB_PREF."h_combo1 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
    db_query($sql,"The combo could not be updated");
}

function delete_h_combo1($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."h_combo1 WHERE combo_code=".db_escape($selected_id);
    db_query($sql,"could not delete combo");
}

function get_h_combo1($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo1";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get combo");
}

function get_comboh_code1($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."h_combo1 WHERE combo_code=".db_escape($selected_id);

    $result = db_query($sql,"could not get combo");
    return db_fetch($result);
}


function add_supp_1($description)
{
    $sql = "INSERT INTO ".TB_PREF."supp_category (description) VALUES (".db_escape($description) . ")";
    db_query($sql,"The Supplier Category could not be added");
}

function update_supp_1($selected_id, $description)
{
    $sql = "UPDATE ".TB_PREF."supp_category SET description=".db_escape($description)." WHERE supp_code = ".db_escape($selected_id);
    db_query($sql,"The Supplier Category could not be updated");
}

function delete_supp_1($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."supp_category WHERE supp_code=".db_escape($selected_id);
    db_query($sql,"could not delete Supplier Category");
}
function get_supp_1($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."supp_category";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get Supplier Category");
}

function get_supp_code($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."supp_category WHERE supp_code=".db_escape($selected_id);

    $result = db_query($sql,"could not get Supplier Category");
    return db_fetch($result);
}

function add_gate_pass($id,$dimension_id,$spare_enable,$owners_enable,
					   $cigrette_enable,$floor_enable,$tool_enable,
					   $remote_enable,$number_enable,$keysqty_enable,
					   $warranty_enable,$card_enable,$utility_enable,$address_enable,$tel_enable,
                       $receiver_enable,$reg_enable)
{
	$sql = "INSERT INTO ".TB_PREF."gate_pass1 (id,dimension_id,
	spare_enable,owners_enable,cigrette_enable,
	floor_enable,tool_enable,remote_enable,number_enable,
	keysqty_enable,warranty_enable,card_enable,utility_enable,address_enable,tel_enable,receiver_enable,reg_enable)
	 VALUES (".db_escape($id) . ",".db_escape($dimension_id) . ","
		.db_escape($spare_enable) . ", "
		.db_escape($owners_enable) . ", "
		.db_escape($cigrette_enable) . ", "
		.db_escape($floor_enable) . ", "
		.db_escape($tool_enable) . ", "
		.db_escape($remote_enable) . ", "
		.db_escape($number_enable) . ", "
		.db_escape($keysqty_enable) . ", "
		.db_escape($warranty_enable) . ", "
		.db_escape($card_enable) . ", "
		.db_escape($utility_enable) . ", "
		.db_escape($address_enable) . ", "
		.db_escape($tel_enable) . ", "
		.db_escape($receiver_enable) . ", "
		.db_escape($reg_enable) . ")";
	db_query($sql,"The sales area could not be added");
} 

function update_gate_pass($selected_id,$id, $dimension_id,$spare_enable,
						  $owners_enable,$cigrette_enable,$floor_enable,
						  $tool_enable,$remote_enable,$number_enable,$keysqty_enable,
						  $warranty_enable,$card_enable,$utility_enable,$address_enable,
                       $tel_enable,$receiver_enable)
{
	$sql = "UPDATE ".TB_PREF."gate_pass1 SET id=".db_escape($id).",
	dimension_id=".db_escape($dimension_id).",
	spare_enable=".db_escape($spare_enable).",
	owners_enable=".db_escape($owners_enable).",
	cigrette_enable=".db_escape($cigrette_enable).",
	floor_enable=".db_escape($floor_enable).",
	tool_enable=".db_escape($tool_enable).",
	remote_enable=".db_escape($remote_enable).",
	number_enable=".db_escape($number_enable).",
	keysqty_enable=".db_escape($keysqty_enable).",
	warranty_enable=".db_escape($warranty_enable).",
	card_enable=".db_escape($card_enable).",
	utility_enable=".db_escape($utility_enable).",
	address_enable=".db_escape($address_enable).",
	tel_enable=".db_escape($tel_enable).",
	receiver_enable=".db_escape($receiver_enable).",
	 WHERE id = ".db_escape($selected_id);
	db_query($sql,"The gate pass could not be updated");
}

function delete_gate_pass($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."gate_pass1 WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function add_multiple_gate_pass($id,$gate_pass_no,$driver_name,$vehicle_no,$gate_pass_date ,$type ,$gate_pass_amount,$gate_pass_debit,$gate_pass_credit)
{
    // display_error("asdsadadsa".$type);
    $sql = "INSERT INTO ".TB_PREF."multiple_gate_pass (delivery_no,gate_pass_no,
	driver_name,vehicle_no,gate_pass_date ,type ,gate_pass_amount ,gate_pass_debit ,gate_pass_credit)
	 VALUES (".db_escape($id) . ",".db_escape($gate_pass_no) . ","
        .db_escape($driver_name) . ", "
        .db_escape($vehicle_no) . ", "
        .(db_escape($gate_pass_date)) . ","
        .(db_escape($type)) . ","
        .(db_escape($gate_pass_amount)) . ","
        .(db_escape($gate_pass_debit)) . ","
        .(db_escape($gate_pass_credit)) . ")";
    db_query($sql,"The Gate Pass could not be added");
    return db_insert_id();
}
function delete_multiple_gate_pass($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."multiple_gate_pass WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete sales area");
}

function update_multiple_gate_pass($selected_id,$gate_pass_amount,$gate_pass_debit,$gate_pass_credit,$driver_name,$vehicle_no,$gate_pass_date)
{
    $sql = "UPDATE ".TB_PREF."multiple_gate_pass 
    SET gate_pass_amount=".db_escape($gate_pass_amount).",
	gate_pass_debit=".db_escape($gate_pass_debit).",
	gate_pass_credit=".db_escape($gate_pass_credit).",
	driver_name=".db_escape($driver_name).",
	vehicle_no=".db_escape($vehicle_no).",
	gate_pass_date=".db_escape(date2sql($gate_pass_date))."
	 WHERE gate_pass_no = ".db_escape($selected_id);
    db_query($sql,"The gate pass could not be updated");
}

function update_ip($selected_id, $ip_address)
{
	$sql = "UPDATE ".TB_PREF."static_ip SET ip_address=".db_escape($ip_address)." WHERE id = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}
function add_ip($ip_address)
{
	$sql = "INSERT INTO ".TB_PREF."static_ip (ip_address) VALUES (".db_escape($ip_address) . ")";
	db_query($sql,"The combo could not be added");
}
function delete_ip($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."static_ip WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete combo");
}

function get_ip($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."static_ip";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_ip_($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."static_ip WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

////////////////footer combo 1,2,3
function add_f_combo1($description)
{
	$sql = "INSERT INTO ".TB_PREF."f_combo1 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_f_combo1($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."f_combo1 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_f_combo1($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."f_combo1 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo");
}

function get_f_combo1($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo1";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combof_code1($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo1 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

///////////////combo_2
function add_f_combo2($description)
{
	$sql = "INSERT INTO ".TB_PREF."f_combo2 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_f_combo2($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."f_combo2 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_f_combo2($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."f_combo2 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_f_combo2($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo2";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_f_combo_code2($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo2 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
/////////////////////////combo 3

function add_f_combo3($description)
{
	$sql = "INSERT INTO ".TB_PREF."f_combo3 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_f_combo3($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."f_combo3 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_f_combo3($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."f_combo3 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_f_combo3($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo3";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_f_combo_code3($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."f_combo3 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}




function add_ph_combo_1($description)
{
	$sql = "INSERT INTO ".TB_PREF."ph_combo1 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_ph_combo_1($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."ph_combo1 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_ph_combo_1($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."ph_combo1 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo");
}

function get_ph_combo_1($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo1";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_ph_combo_code($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo1 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

///////////////combo_2
function add_ph_combo_2($description)
{
	$sql = "INSERT INTO ".TB_PREF."ph_combo2 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_ph_combo_2($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."ph_combo2 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_ph_combo_2($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."ph_combo2 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_ph_combo_2($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo2";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_ph_combo_code_2($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo2 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
/////////////////////////combo 3

function add_ph_combo_3($description)
{
	$sql = "INSERT INTO ".TB_PREF."ph_combo3 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_ph_combo_3($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."ph_combo3 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_ph_combo_3($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."ph_combo3 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_ph_combo_3($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo3";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_ph_combo_code_3($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."ph_combo3 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

/////////////////////////////////////////////////////////////////


////////////////////////////////hareem
function add_combo_1($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo_1 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_1($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo_1 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_1($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo_1 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo");
}

function get_combo_1($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_1";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_1 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

///////////////combo_2
function add_combo_2($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo_2 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_2($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo_2 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_2($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo_2 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_combo_2($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_2";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_2($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_2 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
/////////////////////////combo 3

function add_combo_3($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo_3 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_3($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo_3 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_3($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo_3 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_combo_3($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_3";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_3($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo_3 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
//////////////////////////hareeeeeem end
////////////////sales order
/////////combo 1,2,3
function add_combo_so_1($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo1 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_1($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo1 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_1($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo1 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo");
}

function get_combo_so_1($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo1";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_1($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo1 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}

function add_combo_so_2($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo2 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_2($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo2 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_2($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo2 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_combo_so_2($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo2";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_2($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo2 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
/////////////////////////combo 3

function add_combo_so_3($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo3 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_3($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo3 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_3($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo3 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_combo_so_3($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo3";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_3($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo3 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
//////////////combo 4
function add_combo_so_4($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo4 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_4($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo4 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_4($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo4 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 2");
}

function get_combo_so_4($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo4";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_4($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo4 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
//////////////combo 5
function add_combo_so_5($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo5 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_5($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo5 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_5($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo5 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 5");
}

function get_combo_so_5($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo5";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_5($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo5 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
//////////////combo 6
function add_combo_so_6($description)
{
	$sql = "INSERT INTO ".TB_PREF."combo6 (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The combo could not be added");
}

function update_combo_so_6($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."combo6 SET description=".db_escape($description)." WHERE combo_code = ".db_escape($selected_id);
	db_query($sql,"The combo could not be updated");
}

function delete_combo_so_6($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."combo6 WHERE combo_code=".db_escape($selected_id);
	db_query($sql,"could not delete combo 6");
}

function get_combo_so_6($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."combo6";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get combo");
}

function get_combo_code_so_6($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."combo6 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	return db_fetch($result);
}
////////////////////////



function add_sales_group($description)
{
	$sql = "INSERT INTO ".TB_PREF."groups (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales group could not be added");
}

function update_sales_group($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."groups SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales group could not be updated");
}

function delete_sales_group($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."groups WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales group");
}

function get_sales_groups($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."groups";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get groups");
}

function get_sales_group($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."groups WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get group");
	return db_fetch($result);
}

function get_sales_group_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."groups WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}

function add_sales_area($description)
{
	$sql = "INSERT INTO ".TB_PREF."areas (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales area could not be added");
}

function update_sales_area($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."areas SET description=".db_escape($description)." WHERE area_code = ".db_escape($selected_id);
   	db_query($sql,"The sales area could not be updated");
}

function delete_sales_area($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function get_sales_areas($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."areas";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get areas");
}

function get_sales_area($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get area");
	return db_fetch($result);
}

function get_area_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function add_salesman($salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $gl_account, $user_id, $provision, $break_pt, $provision2)
{
	$sql = "INSERT INTO ".TB_PREF."salesman (salesman_name, salesman_phone, salesman_fax, salesman_email, gl_account, user_id,
		provision, break_pt, provision2)
		VALUES (".db_escape($salesman_name) . ", "
		  .db_escape($salesman_phone) . ", "
		  .db_escape($salesman_fax) . ", "
		  .db_escape($salesman_email) . ","
		  .db_escape($gl_account) . ", "
		  .db_escape($user_id) . ", " 
		  .db_escape($provision).", "
		  .db_escape($break_pt).", "
		.db_escape($provision2).")";
   	db_query($sql,"The insert of the sales person failed");
}

function update_salesman($selected_id, $salesman_name, $salesman_phone, $salesman_fax, $salesman_email, $gl_account, $user_id, $provision, $break_pt, $provision2)
{
	$sql = "UPDATE ".TB_PREF."salesman SET salesman_name=".db_escape($salesman_name) . ",
		salesman_phone=".db_escape($salesman_phone) . ",
		salesman_fax=".db_escape($salesman_fax) . ",
		salesman_email=".db_escape($salesman_email) . ",
		gl_account=".db_escape($gl_account) . ",
		user_id=".db_escape($user_id) . ",
		provision=".$provision.",
		break_pt=".$break_pt.",
		provision2=".$provision2."
		WHERE salesman_code = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}

function delete_salesman($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);
	db_query($sql,"The sales-person could not be deleted");
}

function get_salesmen($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."salesman";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

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

/////////////---------------------------TAX RATE--------------------------------------/////////////////
function add_tax($description,$maxamount,$taxrate,$fixamount)
{
    $sql = "INSERT INTO ".TB_PREF."taxrate (minamount,maxamount,taxrate,fixamount) VALUES (".db_escape($description) .","
        .db_escape($maxamount).","
        .db_escape($taxrate).","
        .db_escape($fixamount).
        ")";
    db_query($sql,"The sales area could not be added");
}

function update_tax($selected_id,$description,$maxamount,$taxrate,$fixamount)
{
    $sql = "UPDATE ".TB_PREF."taxrate SET minamount=".db_escape($description).",
	  maxamount=	".db_escape($maxamount).",
	  taxrate=".db_escape($taxrate).",
	  fixamount=".db_escape($fixamount)."
	   WHERE id = ".db_escape($selected_id);
    db_query($sql,"The values could not be updated");
}

function delete_tax($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."taxrate WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete values");
}

function get_tax1($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."taxrate";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get taxes");
}

function get_tax($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."taxrate WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get tax ");
    return db_fetch($result);
}

function get_tax_name($id)
{
    $sql = "SELECT minamount FROM ".TB_PREF."taxrate WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get tax");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_tax_amount_empl_vise($emp_amt)
{
    $sql="SELECT `taxrate` 
FROM ".TB_PREF."taxrate
WHERE $emp_amt
BETWEEN  `minamount`
AND `maxamount`";

    $result = db_query($sql, "could not get tax");

    $row = db_fetch_row($result);

    return $row[0];
}

function get_tax_min_amount_empl_vise($emp_amt)
{
    $sql="SELECT `minamount` 
FROM ".TB_PREF."taxrate
WHERE $emp_amt
BETWEEN  `minamount`
AND `maxamount`";

    $result = db_query($sql, "could not get tax");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_tax_fix_amount_empl_vise($emp_amt)
{
    $sql="SELECT `fixamount` 
FROM ".TB_PREF."taxrate
WHERE $emp_amt
BETWEEN  `minamount`
AND `maxamount`";

    $result = db_query($sql, "could not get tax");

    $row = db_fetch_row($result);

    return $row[0];
}

//iqra

function insert_item_code($item_code1,$item_code2,$userid)
{

    $sql = "INSERT INTO ".TB_PREF."item_log (pre_item,new_item,user_id) VALUES (".db_escape($item_code1) . ",".db_escape($item_code2) . ",".db_escape($userid) . ")";
    db_query($sql,"The sales group could not be added");
}


function update_stock_id_neww($item_code1,$item_code2)
{

    $sql =" UPDATE ".TB_PREF."loc_stock
    SET ".TB_PREF."loc_stock.stock_id =".db_escape($item_code2)."
      WHERE    
        
      ".TB_PREF."loc_stock.stock_id=".db_escape($item_code1)."

       
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_stock_moves_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."stock_moves
    SET ".TB_PREF."stock_moves.stock_id =".db_escape($item_code2)."

      WHERE 
   
    
    ".TB_PREF."stock_moves.stock_id=".db_escape($item_code1)."
";

    db_query($sql,"The sales goup could not be updated");
}
function update_item_codes_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."item_codes
    SET ".TB_PREF."item_codes.item_code =".db_escape($item_code2)."


      WHERE     
      
      ".TB_PREF."item_codes.item_code=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_dbtr_trans_det_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."debtor_trans_details
    SET ".TB_PREF."debtor_trans_details.stock_id =".db_escape($item_code2)."
      WHERE 
         
    
    ".TB_PREF."debtor_trans_details.stock_id=".db_escape($item_code1)."
      
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_pur_order_det_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."purch_order_details
    SET ".TB_PREF."purch_order_details.item_code =".db_escape($item_code2)."
      WHERE 
         
      ".TB_PREF."purch_order_details.item_code=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_grn_items_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."grn_items
    SET ".TB_PREF."grn_items.item_code =".db_escape($item_code2)."
      WHERE    
      ".TB_PREF."grn_items.item_code=".db_escape($item_code1)."
      
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_supp_invoice_items_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."supp_invoice_items
    SET ".TB_PREF."supp_invoice_items.stock_id =".db_escape($item_code2)."
      WHERE 
     
    ".TB_PREF."supp_invoice_items.stock_id=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_prices_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."prices
    SET ".TB_PREF."prices.stock_id =".db_escape($item_code2)."
      WHERE 
       
    
    ".TB_PREF."prices.stock_id=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_features_price_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."features_price 
    SET ".TB_PREF."features_price.stock_id =".db_escape($item_code2)."
      WHERE 
        
    
    ".TB_PREF."features_price.stock_id=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_purch_data_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."purch_data 
    SET ".TB_PREF."purch_data.stock_id =".db_escape($item_code2)."
      WHERE     
      
       ".TB_PREF."purch_data.stock_id=".db_escape($item_code1)."
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_workorders_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."workorders
    SET ".TB_PREF."workorders.stock_id =".db_escape($item_code2)."
      WHERE   
      ".TB_PREF."workorders.stock_id=".db_escape($item_code1)."
      
      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_wo_issue_items_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."wo_issue_items
    SET ".TB_PREF."wo_issue_items.stock_id =".db_escape($item_code2)."
      WHERE 
        
          ".TB_PREF."wo_issue_items.stock_id=".db_escape($item_code1)."

      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_wo_requirements_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."wo_requirements 
    SET ".TB_PREF."wo_requirements.stock_id =".db_escape($item_code2)."
      WHERE    
       
            ".TB_PREF."wo_requirements.stock_id=".db_escape($item_code1)."

      ";

    db_query($sql,"The sales goup could not be updated");
}
function update_sales_order_details_neww($item_code1,$item_code2)
{
//    display_error($selected_id);
    $sql =" UPDATE ".TB_PREF."sales_order_details
    SET ".TB_PREF."sales_order_details.stk_code =".db_escape($item_code2)."
      WHERE 
         
            ".TB_PREF."sales_order_details.stk_code=".db_escape($item_code1)."

      ";

    db_query($sql,"The sales goup could not be updated");
}

//---------------------------for reports module------------------------------//

function add_reports_pref($rep_type, $rep_name, $rep_no)
{
    $sql = "INSERT INTO ".TB_PREF."reports_preference (rep_type, rep_name, rep_no) VALUES 
    (".db_escape($rep_type) . ",
    ".db_escape($rep_name) . ", 
    ".db_escape($rep_no) . ")";
    db_query($sql,"The reports preference could not be added");
}

function update_reports_pref($selected_id, $rep_type, $rep_name, $rep_no)
{
    $sql = "UPDATE ".TB_PREF."reports_preference 
    SET rep_type=".db_escape($rep_type).",
     rep_name=".db_escape($rep_name).",
     rep_no=".db_escape($rep_no)."
     WHERE id = ".db_escape($selected_id);
    db_query($sql,"The reports preference could not be updated");
}

function delete_reports_pref($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."reports_preference WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete reports preference");
}

function get_reports_prefs($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."reports_preference";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get reports preference");
}

function get_reports_pref($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."reports_preference WHERE id=".db_escape($selected_id);
    $result = db_query($sql,"could not get reports preference");
    return db_fetch($result);
}
function update_gl_trans_debit($id, $amount, $account, $tran_date)
{
    $sql = "UPDATE ".TB_PREF."gl_trans 
    SET amount=".db_escape($amount).",
	account=".db_escape($account).",
	tran_date=".db_escape(date2sql($tran_date))."
	 WHERE amount > 0 AND text_1 = ".db_escape($id);
    db_query($sql,"The gate pass could not be updated");
}
function update_gl_trans_crebit($id, $amount, $account, $tran_date)
{
    $sql = "UPDATE ".TB_PREF."gl_trans 
    SET amount=".db_escape($amount).",
	account=".db_escape($account).",
	tran_date=".db_escape(date2sql($tran_date))."
	 WHERE amount < 0 AND text_1 = ".db_escape($id);
    db_query($sql,"The gate pass could not be updated");
}
function update_journal_data($type_no,$amount, $tran_date)
{
    $sql = "UPDATE ".TB_PREF."journal 
    SET amount=".db_escape($amount).",
    tran_date=".db_escape(date2sql($tran_date))."
	 WHERE trans_no = ".db_escape($type_no);
    db_query($sql,"The gate pass could not be updated");
}
function get_multiple_gatepass_id()
{
    $sql = "SELECT Max(id)+1 FROM ".TB_PREF."multiple_gate_pass";

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_gate_pass_data($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."multiple_gate_pass WHERE gate_pass_no=".db_escape($selected_id);

    $result = db_query($sql,"could not get area");
    return db_fetch($result);
}
//-----------------------for reports module end------------------------------//

function add_crm_info($spouse_name, $child_name, $birth_date, $customer_id)
{
    $b_date = date2sql($birth_date);

    $sql = "INSERT INTO ".TB_PREF."crm_info (spouse_name, child_name,
    birth_date, customer_id) VALUES (".db_escape($spouse_name) . ", ".db_escape($child_name) . ",
      ".db_escape($b_date) . ",".db_escape($customer_id) . ")";
    db_query($sql,"The sales area could not be added");
}

function update_crm_info($selected_id, $spouse_name, $child_name, $birth_date, $customer_id)
{
    $sql = "UPDATE ".TB_PREF."crm_info SET spouse_name=".db_escape($spouse_name).",
    child_name=".db_escape($child_name).",
    birth_date=".db_escape(date2sql($birth_date)).",
    customer_id=".db_escape($customer_id)."
    WHERE id = ".db_escape($selected_id);
    db_query($sql,"The sales area could not be updated");
}

function delete_crm_info($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."crm_info WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete sales area");
}

function get_crm_info($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."crm_info where customer_id =". db_escape($show_inactive);
    return db_query($sql,"could not get areas");
}

function get_crm_infor($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."crm_info WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get area");
    return db_fetch($result);
}

function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}
