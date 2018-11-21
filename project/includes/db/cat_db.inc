<?php
//saad 17 may

function update_category($selected_id,$category)
{
	$sql = "UPDATE ".TB_PREF."category 
	SET category=".db_escape($category) . "
	WHERE ".TB_PREF."category.id = ".db_escape($selected_id);
	db_query($sql,"The update of the category failed");
}
function add_category($category, $inactive)
{
	$sql = "INSERT INTO ".TB_PREF."category(category, inactive)
		VALUES (".db_escape($category).", "
		.db_escape($inactive).")";
	db_query($sql,"The insert category failed");
}
function delete_category($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."category WHERE id=".db_escape($selected_id);
	db_query($sql,"The category could not be deleted");
}
function get_category_1($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."category";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get call type");
}
function get_category1($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."category WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get category");
	return db_fetch($result);
}
?>