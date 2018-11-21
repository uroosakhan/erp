<?php

function get_wo_requirements($woid)
{
	$sql = "SELECT req.*, item.description, item.units, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (".TB_PREF."wo_requirements req,"
			.TB_PREF."locations loc,"
			.TB_PREF."workcentres center)
			INNER JOIN ".TB_PREF."stock_master item ON req.stock_id=item.stock_id 
		WHERE workorder_id=".db_escape($woid)."
		AND loc.loc_code = req.loc_code
		AND center.id=workcentre";

	return db_query($sql, "The work order requirements could not be retrieved");    
}
function get_wo_requirements_amount3($woid)
{
    $sql = "SELECT req.*, item.description, item.mb_flag, loc.location_name, 
			center.name AS WorkCentreDescription,
			item.material_cost as ComponentCost,
			item.inventory_account,
			item.cogs_account
		FROM (".TB_PREF."wo_requirements req,"
        .TB_PREF."locations loc,"
        .TB_PREF."workcentres center)
			INNER JOIN ".TB_PREF."stock_master item ON req.stock_id=item.stock_id 
		WHERE workorder_id=".db_escape($woid)."
		AND loc.loc_code = req.loc_code
		
		AND center.id=workcentre";

    return db_query($sql, "The work order requirements could not be retrieved");
}


function get_requirements_costs($woid)
{
	$sql = "SELECT SUM(units_req*unit_cost) cost
		FROM ".TB_PREF."wo_requirements
		WHERE workorder_id=".db_escape($woid);

	$result = db_query($sql, "The work order requirements costs could not be retrieved");
	$costs = db_fetch($result);

	return $costs['cost'] ? $costs['cost'] : 0;
}

//--------------------------------------------------------------------------------------

function create_wo_requirements_($woid, $stock_id, $units_reqd, $WorkOrder, $AllowCart=0)
{

	if($AllowCart == 1) {
		foreach ($WorkOrder->line_item_wo as $line_no => $stock_item) {

			$unit_quantity = $stock_item->issue_qty;
			$item_quantity = $stock_item->issue_qty * $units_reqd;
//			display_error($stock_item->issue_qty."+".$units_reqd."+".$item_quantity);
//			die;
			$amount3 = $stock_item->amount33;
			$item = get_item_edit_info($stock_id);
			if($amount3 == "")
				$amount3 = $item['amount3'];
			$sql = "INSERT INTO ".TB_PREF."wo_requirements (workorder_id, stock_id, workcentre, units_req,loc_code, amount3)
			VALUES ($woid, " . "'" . $stock_item->stock_id . "'" . ",
			'". $stock_item->workcentre_added1 . "',
			$unit_quantity, '".$stock_item->loc_code1."', '".$amount3."')";
			db_query($sql, "The work order requirements could not be added");
		}
	} elseif($AllowCart == 0) {

		$result = get_bill_of_material($stock_id);
		while ($bom_item = db_fetch($result)) {
			$unit_quantity = input_num('qty'.$bom_item['id']);
			$amount3 = $_POST['amount3'.$bom_item['id']];
			$item = get_item_edit_info($stock_id);
			if($amount3 == "" )
				$amount3 = $item['amount3'];
			$item_quantity = input_num('qty'.$bom_item['id']) * $units_reqd;
			$sql = "INSERT INTO ".TB_PREF."wo_requirements (workorder_id, stock_id, workcentre, units_req,
			loc_code, amount3)
				VALUES ($woid, " . "'" . $bom_item["component"] . "'" . ",
				'". $bom_item["workcentre_added"] . "',
				$unit_quantity,	'" . $bom_item["loc_code"] . "','$amount3')";
			db_query($sql, "The work order requirements could not be added");
		}

	}
}
//--------------------------------------------------------------------------------------

function create_wo_requirements($woid, $stock_id)
{
	$sql = " INSERT INTO " . TB_PREF . "wo_requirements (workorder_id, stock_id, workcentre, units_req, loc_code)
	SELECT " . db_escape($woid) . ", component, workcentre_added, quantity, loc_code
	FROM " . TB_PREF . "bom WHERE parent=" . db_escape($stock_id);
	db_query($sql, "The work order requirements could not be added");

}
//--------------------------------------------------------------------------------------

function delete_wo_requirements($woid)
{
	$sql="DELETE FROM ".TB_PREF."wo_requirements WHERE workorder_id=".db_escape($woid);
	db_query($sql,"The work order requirements could not be deleted");	
}

//--------------------------------------------------------------------------------------
/*
	Update total component units issued and average component cost
*/
function update_wo_requirement_issued($id, $quantity, $cost)
{

	$sql = "UPDATE ".TB_PREF."wo_requirements SET
				unit_cost = (units_issued*unit_cost+".$quantity*$cost.")/(units_issued+".$quantity."),
				units_issued = units_issued + ".db_escape($quantity)."
			WHERE id = ".db_escape($id);

	db_query($sql, "The work requirements issued quantity couldn't be updated");
}
//--------------------------------------------------------------------------------------

function void_wo_requirements($woid)
{
	$sql = "UPDATE ".TB_PREF."wo_requirements SET units_issued = 0
		WHERE workorder_id = ".db_escape($woid);

	db_query($sql, "The work requirements issued quantity couldn't be voided");	
}

