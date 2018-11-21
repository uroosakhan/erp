<?php

function add_employee_doc($employee_id,$document_type,$document_no,$expiry_date,$remarks)
{
	$sql = "INSERT INTO ".TB_PREF."employee_doc (employee_id,document_type,document_no,expiry_date,remarks) VALUES (".db_escape($employee_id) . ",".db_escape($document_type) . ",".db_escape($document_no) . ",".db_escape(sql2date($expiry_date)) . ",".db_escape($remarks) . ")";
   	db_query($sql,"The sales document type could not be added");
}

function update_employee_doc($selected_id,$employee_id,$document_type,$document_no,$expiry_date,$remarks)
{
	$sql = "UPDATE ".TB_PREF."employee_doc SET employee_id=".db_escape($employee_id)." ,
	document_type=".db_escape($document_type)." ,
	document_no=".db_escape($document_no)." ,
	expiry_date=".db_escape($expiry_date).", 
	remarks=".db_escape($remarks)." 
	WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The document type could not be updated");
	}
	
	function delete_employee_doc($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."employee_doc WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete document type");
}

function get_employee_doc($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_doc";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY employee_id";
	return db_query($sql,"could not get title");
}

function get_employee_docs($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_doc WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get document type");
	return db_fetch($result);
}
function get_employee_docs_grid($selected_id)
{
	$sql = "SELECT id,employee_id,document_type,document_no,expiry_date,remarks,inactive FROM ".TB_PREF."employee_doc WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get document type");
	return db_fetch($result);
}

function get_employee_doc_name($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id = ".db_escape($employee_id);
	$result = db_query($sql, "could not get document type");
	$row = db_fetch($result);
	return $row[0];
}
function get_employee_document_type($id)
{
	$sql = "SELECT description FROM ".TB_PREF."document_type WHERE id = ".db_escape($id);
	$result = db_query($sql, "could not get document type");
	$row = db_fetch($result);
	return $row[0];
}



?>