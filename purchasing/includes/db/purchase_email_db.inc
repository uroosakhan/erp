<?php

function add_purchase_email($description)
{
	$sql = "INSERT INTO ".TB_PREF."purchase_email (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The sales email could not be added");
}
function update_purchase_email($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."purchase_email SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
	db_query($sql,"The sales email could not be updated");
}

function delete_purchase_email($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."purchase_email WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function get_purchase_emails($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."purchase_email";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales email");
}

function get_purchase_email($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."purchase_email WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales email");
	return db_fetch($result);
}

