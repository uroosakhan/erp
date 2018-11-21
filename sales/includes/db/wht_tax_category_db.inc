<?php

function add_wht_tax_category( $description)
{
	$sql = "INSERT INTO ".TB_PREF."wht_tax_category (description) VALUES (".db_escape($description).
		")";
	db_query($sql, "could not add WHT Tax Category");
}

function update_wht_tax_category($id, $description)
{

	$sql = "UPDATE ".TB_PREF."wht_tax_category SET 
	description =".db_escape($description)." WHERE id = ".db_escape($id);
	
	db_query($sql, "could not update WHT Tax Category");
}

function get_all_wht_tax_category($all=false)
{
	$sql = "SELECT * FROM ".TB_PREF."wht_tax_category";
	if (!$all)
		$sql .= " WHERE !inactive";
	
	return db_query($sql, "could not get all WHT Tax Category");
} 

function get_wht_tax_categories($id)
{
	$sql = "SELECT * FROM ".TB_PREF."wht_tax_category";
	if (!$id) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get WHT Tax Category");
}

function get_wht_tax_category_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."wht_tax_category WHERE id=".db_escape($id);
	
	$result = db_query($sql, "could not get WHT Tax Category");
	
	$row = db_fetch_row($result);
	return $row[0];
}

function delete_wht_tax_category($id)
{
	$sql="DELETE FROM ".TB_PREF."wht_tax_category WHERE id=".db_escape($id);
	db_query($sql,"The WHT Tax Category record could not be deleted");


}
function get_wht_tax_category($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."wht_tax_category WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get WHT Tax Category");
	return db_fetch($result);
}


