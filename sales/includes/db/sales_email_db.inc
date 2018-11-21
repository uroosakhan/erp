<?php

function add_sales_email($description)
{
	$sql = "INSERT INTO ".TB_PREF."sales_email (description) VALUES (".db_escape($description) . ")";
	db_query($sql,"The sales email could not be added");
}
function update_sales_email($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."sales_email SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
	db_query($sql,"The sales email could not be updated");
}

function delete_sales_email($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."sales_email WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function get_sales_emails($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."sales_email";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales email");
}

function get_sales_email($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."sales_email WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales email");
	return db_fetch($result);
}

