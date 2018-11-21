<?php

function add_item_location($loc_code, $location_name, $delivery_address, $phone, $phone2, $fax,
                           $email, $contact, $fixed_asset = 0, $dimension_id)
{
	$sql = "INSERT INTO ".TB_PREF."locations (loc_code, location_name, delivery_address, phone, phone2, fax, email, contact, fixed_asset, dimension_id)
		VALUES (".db_escape($loc_code).", ".db_escape($location_name).", ".db_escape($delivery_address).", "
			.db_escape($phone).", ".db_escape($phone2).", ".db_escape($fax).", ".db_escape($email).", "
			.db_escape($contact).", " .db_escape($fixed_asset).", " .db_escape($dimension_id).")";

	db_query($sql,"a location could not be added");

	/* Also need to add loc_stock records for all existing items */
	$sql = "INSERT INTO ".TB_PREF."loc_stock (loc_code, stock_id, reorder_level)
		SELECT ".db_escape($loc_code).", ".TB_PREF."stock_master.stock_id, 0 FROM ".TB_PREF."stock_master";

	db_query($sql,"a location could not be added");
}


//------------------------------------------------------------------------------------

function update_item_location($loc_code, $location_name, $delivery_address, $phone, $phone2, $fax,
                              $email, $contact, $fixed_asset = 0, $dimension_id)

{
    $sql = "UPDATE ".TB_PREF."locations SET location_name=".db_escape($location_name).",
    	delivery_address=".db_escape($delivery_address).",
    	phone=".db_escape($phone).", phone2=".db_escape($phone2).", fax=".db_escape($fax).",
    	email=".db_escape($email).", contact=".db_escape($contact).",
    	fixed_asset=".db_escape($fixed_asset).", dimension_id=".db_escape($dimension_id)."
    	WHERE loc_code = ".db_escape($loc_code);

	db_query($sql,"a location could not be updated");
}

//------------------------------------------------------------------------------------

function delete_item_location($item_location)
{
	$sql="DELETE FROM ".TB_PREF."locations WHERE loc_code=".db_escape($item_location);
	db_query($sql,"a location could not be deleted");

	$sql = "DELETE FROM ".TB_PREF."loc_stock WHERE loc_code =".db_escape($item_location);
	db_query($sql,"a location could not be deleted");
}

//------------------------------------------------------------------------------------

function get_item_location($item_location)
{
	$sql="SELECT * FROM ".TB_PREF."locations WHERE loc_code=".db_escape($item_location);

	$result = db_query($sql,"a location could not be retrieved");

	return db_fetch($result);
}

//------------------------------------------------------------------------------------

function get_item_locations($show_inactive, $fixed_asset = 0)
{
	$sql = "SELECT * FROM ".TB_PREF."locations WHERE fixed_asset = ".db_escape($fixed_asset);
	if (!$show_inactive) $sql .= " AND !inactive";
	return db_query($sql, "could not query locations");
}

//------------------------------------------------------------------------------------

function set_reorder_level($stock_id, $loc_code, $reorder_level)
{
	$sql = "UPDATE ".TB_PREF."loc_stock SET reorder_level = $reorder_level
		WHERE stock_id = ".db_escape($stock_id)." AND loc_code = ".db_escape($loc_code);

	db_query($sql,"an item reorder could not be set");
}

//------------------------------------------------------------------------------------

function get_loc_details($stock_id, $fixed_asset = 0)
{
	$user_id = $_SESSION["wa_current_user"]->user;
	$sql = "SELECT stock.loc_code, stock.location_name, "
	.db_escape($stock_id)." as stock_id, reorders.reorder_level
		FROM ".TB_PREF."locations stock 
		 LEFT JOIN ".TB_PREF."user_locations user_loc
		 ON user_loc.loc_code=stock.loc_code
		LEFT JOIN ".TB_PREF."loc_stock reorders
		 ON reorders.loc_code=stock.loc_code
		AND reorders.stock_id = ".db_escape($stock_id)
	    ." WHERE stock.fixed_asset = ".db_escape($fixed_asset) ." ";

	    if($user_id != '')
	     $sql .=" AND user_loc.user_id = ".db_escape($user_id)."
	     
	     
	    ORDER BY reorders.loc_code";
	return db_query($sql,"an item reorder could not be retreived");
}

//------------------------------------------------------------------------------
function get_dimension_name_for_loc($dimension_id)
{
    $sql = "SELECT name 
    FROM ".TB_PREF."dimensions 
    WHERE id=".db_escape($dimension_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}