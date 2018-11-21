<?php

function add_sms_template($source, $mask, $message, $password, $api)
{
	$sql = "INSERT INTO ".TB_PREF."sms (source, mask, message, password, api) VALUES (".db_escape($source) . ", ".db_escape($mask) . ", ".db_escape($message) . ", ".db_escape($password) . ", ".db_escape($api) . ")";
   	db_query($sql,"The sales group could not be added");
}

			
function update_sms_template($selected_id, $source, $mask, $message, $password, $api)
{
	$sql = "UPDATE ".TB_PREF."sms SET 
	source=".db_escape($source).",
	mask=".db_escape($mask).",
	message=".db_escape($message).",
	password=".db_escape($password).",
	api=".db_escape($api)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sms template could not be updated");
}

function delete_sms_template($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."sms WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sms template");
}

function get_sms_templates()
{
	$sql = "SELECT * FROM ".TB_PREF."sms";
	return db_query($sql,"could not get sms template");
}

function get_sms_template($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."sms WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sms");
	return db_fetch($result);
}
//=======  	SMS TEMPLATE ========================== SMS TEMPLATE ========================

function add_template($template, $filterType)
{
	$sql = "INSERT INTO ".TB_PREF."template (template, filterType) 
			VALUES (".db_escape($template).",".db_escape($filterType).")";
   	db_query($sql,"The template could not be added");
}

			
function update_template($selected_id, $template, $filterType)
{
	$sql = "UPDATE ".TB_PREF."template SET
	template=".db_escape($template).", filterType=".db_escape($filterType)."
	WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The template could not be updated");
}

function delete_template($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."template WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sms template");
}

function get_templates()
{
	$sql = "SELECT * FROM ".TB_PREF."template";
	return db_query($sql,"could not get sms template");
}

function get_template($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."template WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sms");
	return db_fetch($result);
}
function get_all_templates()
{
	$sql = "SELECT * FROM ".TB_PREF."template";
	$result = db_query($sql,"could not get sms template");
	return db_fetch_row($result);
	//return db_query($sql,"could not get sms template");
}
function get_types_names($selected_id)
{
	$sql = "SELECT types_name FROM ".TB_PREF."sms_type_template WHERE id = ".db_escape($selected_id);;
	$result = db_query($sql,"could not get sms template");
	$row = db_fetch_row($result);
	return $row[0];

}

?>