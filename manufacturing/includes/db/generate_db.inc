<?php
function generate_purchase_order($id, $supplier_id, $purpose,
                                 $quantity, $parent, $units_reqd)
{
    global $Refs;

    $Total = 0;
    $date = date('Y-m-d');
    $ref = $Refs->get_next(ST_PURCHORDER, null,
        array('supplier_id' => $supplier_id, 'date' => $date));
    $sql = "INSERT INTO ".TB_PREF."purch_orders (supplier_id, Comments, ord_date, reference, 
    requisition_no, into_stock_location, delivery_address, total, tax_included,receive_ref, 
    pr, requisition_detail_id) VALUES(";
    $sql .= db_escape($supplier_id) . "," .
        db_escape('') . "," .
        db_escape($date) . ", " .
        db_escape($ref) . ", " .
        db_escape('') . ", " .
        db_escape('DEF') . ", " .
        db_escape('address') . ", " .
        db_escape($Total). ", " .
        db_escape('address') . ", " .
        db_escape(''). ", " .
        db_escape(0) . ", " .
        db_escape(0) . ")";
    db_query($sql, "The purchase order header record could not be inserted");
    $ord_no = db_insert_id();

    $Refs->save(ST_PURCHORDER, $ord_no, $ref);

    add_audit_trail(ST_PURCHORDER, $ord_no, $date);

    $BOM = get_bom_for_PO($id);
    while($myrow2 = db_fetch($BOM))
    {
        $unit_price = get_purchase_price($supplier_id, $myrow2["component"]);
        $Total = $quantity*$unit_price;
        $sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, 
        item_code, description, delivery_date, unit_price, 
        quantity_ordered, purpose) VALUES (";
        $sql .= $ord_no . ", " . db_escape($myrow2['component']). "," .
            db_escape($myrow2['description']). "," .
            db_escape($date) . "," .
            db_escape($unit_price) . ", " .
            db_escape($quantity). ", ".
            db_escape($purpose). ")";
        db_query($sql, "One of the purchase order detail records could not be inserted");
    }
    $sql1 = "UPDATE ".TB_PREF."purch_orders SET total=".db_escape($Total) . "
		WHERE order_no=".db_escape($ord_no);
    db_query($sql1, "Error");
    meta_forward($_SERVER['PHP_SELF'], "item_code=$parent&units_reqd=$units_reqd&AddedID=$ord_no");
}

function get_bom_for_PO($id)
{
    $sql = "SELECT bom.*, loc.location_name,
		centre.name AS WorkCentreDescription,
    	item.description, item.mb_flag AS ResourceType, 
    	item.material_cost AS ProductCost, units,
    	bom.quantity * item.material_cost AS ComponentCost 
    	FROM ".TB_PREF."workcentres centre,
    		".TB_PREF."locations loc,
    		".TB_PREF."bom bom
	    		INNER JOIN ".TB_PREF."stock_master item ON bom.component = item.stock_id 
    	WHERE bom.id = ".db_escape($id)."
		AND centre.id=bom.workcentre_added
		AND bom.loc_code = loc.loc_code ORDER BY bom.id";

    return db_query($sql, "The bill of material could not be retrieved");
}

function delete_temporary_bom($selected_id)
{
    $sql = "DELETE FROM ".TB_PREF."temporary_bom WHERE id=".db_escape($selected_id);
    db_query($sql,"Could not delete this bom components");
}


//--------------------------------------------------------------------------------------

function add_temporary_bom($id, $selected_parent, $component, $workcentre_added, $loc_code, $quantity, $wo_id)
{
    $sql = "INSERT INTO ".TB_PREF."temporary_bom(id, parent, component, workcentre_added, loc_code, quantity, wo_id)
		VALUES (".db_escape($id).", ".db_escape($selected_parent).", ".db_escape($component) . ","
        .db_escape($workcentre_added) . ", ".db_escape($loc_code) . ", "
        . $quantity .", "
        . $wo_id . ")";

    db_query($sql,"check failed");
}
//-----------------------------------------------------------------------------------
function get_temporary_bom($item, $wo_id)
{
    $sql = "SELECT bom.*, loc.location_name, loc.loc_code as LocationCode,
		centre.name AS WorkCentreDescription, centre.id AS ID,
    	item.description, item.mb_flag AS ResourceType, 
    	item.material_cost AS ProductCost, units,
    	bom.quantity * item.material_cost AS ComponentCost 
    	FROM ".TB_PREF."workcentres centre,
    		".TB_PREF."locations loc,
    		".TB_PREF."temporary_bom bom
	    INNER JOIN ".TB_PREF."stock_master item ON bom.component = item.stock_id 
    	WHERE bom.parent = ".db_escape($item)."
		AND centre.id=bom.workcentre_added
		AND bom.loc_code = loc.loc_code 
		AND bom.make_po = 0 
		AND bom.wo_id = ".db_escape($wo_id)."
		ORDER BY bom.id";

    $query = db_query($sql, "The bill of material could not be retrieved");
    return $query;
}

function update_temporary_bom($selected_parent, $selected_component)
{
    $sql = "UPDATE ".TB_PREF."temporary_bom SET make_po=".db_escape(1) . "
		WHERE parent=".db_escape($selected_parent) . "
		AND id=".db_escape($selected_component);
//	$sql = "UPDATE ".TB_PREF."temporary_bom SET workcentre_added=".db_escape($workcentre_added)
//		. ",loc_code=".db_escape($loc_code) . ",
//		quantity= " . $quantity . "
//		WHERE  id=".db_escape($selected_component);
//	check_db_error("Could not update this bom component", $sql);

    db_query($sql,"could not update bom");
}