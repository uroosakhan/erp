<?php


function get_fixed_asset_classes()
{
	$sql="SELECT * FROM ".TB_PREF."stock_fa_class";

	$result = db_query($sql,"fixed asset classes could not be retrieved");

	return $result;
}

function get_fixed_asset_classname($class)
{
	$sql="SELECT description FROM ".TB_PREF."stock_fa_class WHERE fa_class_id = ".db_escape($class);;

	$result = db_query($sql,"fixed asset class name could not be retrieved");

	$row = db_fetch_row($result);
	
	return $row[0];
}

function update_fixed_asset_class($fa_class_id, $parent_id, $description, $long_description, $depreciation_rate)
{
	$sql = "UPDATE ".TB_PREF."stock_fa_class SET
		parent_id=".db_escape($parent_id).",
		description=".db_escape($description).",
		long_description=".db_escape($long_description).",
    depreciation_rate=".db_escape($depreciation_rate)."
    WHERE fa_class_id=".db_escape($fa_class_id);

	db_query($sql, "The fixed asset class could not be updated");
}

function add_fixed_asset_class($fa_class_id, $parent_id, $description, $long_description, $depreciation_rate)
{
  $sql = "INSERT INTO ".TB_PREF."stock_fa_class (fa_class_id, parent_id, description, long_description,
     depreciation_rate) VALUES ("
    .db_escape($fa_class_id).", ".db_escape($parent_id).", "
	.db_escape($description).", ".db_escape($long_description).", "
    .db_escape($depreciation_rate).")";

	db_query($sql, "The fixed asset class could not be added");
}

function delete_fixed_asset_class($fa_class_id)
{
  $sql = "DELETE FROM ".TB_PREF."stock_fa_class WHERE fa_class_id=".db_escape($fa_class_id);

	db_query($sql, "The fixed asset class could not be deleted");
}
