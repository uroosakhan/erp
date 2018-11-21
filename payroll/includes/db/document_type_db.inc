<?php

function add_document_type($description)
{
	$sql = "INSERT INTO ".TB_PREF."document_type (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales document type could not be added");
}

function update_document_type($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."document_type SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The document type could not be updated");
	}
	
	function delete_document_type($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."document_type WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete document type");
}

function get_document_type($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."document_type";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get title");
}

function get_document_types($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."document_type WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get document type");
	return db_fetch($result);
}

function get_document_type_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."document_type WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get document type");
	$row = db_fetch($result);
	return $row[0];
}



?>