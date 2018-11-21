<?php
/**********************************************************************
Copyright (C) FrontAccounting, LLC.
Released under the terms of the GNU General Public License, GPL,
as published by the Free Software Foundation, either version 3
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
function add_requisition_detail($requisitionid, $item_code, $purpose, $order_quantity,$estimate_price, $supplier_id)
{

	$sql = "INSERT INTO ".TB_PREF."requisition_details (requisition_id, item_code, purpose, order_quantity, estimate_price, quantity, price, supplier_id) VALUES (".
		db_escape($requisitionid).",".db_escape($item_code).",".db_escape($purpose).",".db_escape($order_quantity).",".db_escape($estimate_price).",".
		db_escape($order_quantity).",".
		db_escape($estimate_price).",".
		db_escape($supplier_id)."
		)";

	db_query($sql, "could not add requisition details");
}

function update_requisition_detail($selected_id, $item_code, $purpose, $order_quantity, $estimate_price, $supplier_id)
{
	$sql = "UPDATE ".TB_PREF."requisition_details SET item_code=".db_escape($item_code)
		.", purpose=".db_escape($purpose)
		.", order_quantity=".db_escape($order_quantity)
		.", estimate_price=".db_escape($estimate_price)
		.", quantity=".db_escape($order_quantity)
		.", price=".db_escape($estimate_price)
		.", supplier_id=".db_escape($supplier_id)
		."WHERE requisition_detail_id=".db_escape($selected_id);

	db_query($sql, "could not update requisition detail");
}

function update_requisition_lpo($selected_id, $supplier_id, $quantity, $price)
{
	$sql = "UPDATE ".TB_PREF."requisition_details SET supplier_id=".db_escape($supplier_id)
		.", quantity=".db_escape($quantity)
		.", price=".db_escape($price)
		."WHERE requisition_detail_id=".db_escape($selected_id);

	db_query($sql, "could not update requisition detail");
}

function get_all_requisition_details($requisitionid)
{
	$sql = "SELECT * FROM ".TB_PREF."item_codes ic INNER JOIN ".TB_PREF."requisition_details rd ON ic.item_code = rd.item_code
	LEFT JOIN ".TB_PREF."requisitions req ON req.requisition_id=rd.requisition_id
	WHERE (req.completed=0 AND rd.requisition_id = ".db_escape($requisitionid).")";

	return db_query($sql, "could not get all requisition_details");
}

function get_open_requisition_details()
{
	$sql = "SELECT * FROM ".TB_PREF."item_codes ic INNER JOIN ".TB_PREF."requisition_details rd ON ic.item_code = rd.item_code
	INNER JOIN ".TB_PREF."requisitions r ON rd.requisition_id = r.requisition_id
	LEFT JOIN ".TB_PREF."suppliers s ON rd.supplier_id = s.supplier_id
	WHERE (r.completed = 1) AND (rd.lpo_id = 0)
	ORDER BY r.requisition_id";

	return db_query($sql, "could not get all requisition_details");
}

function get_open_requisition_details_custom($wo_id)
{
	$sql = "SELECT * FROM ".TB_PREF."item_codes ic
	INNER JOIN ".TB_PREF."requisition_details rd ON ic.item_code = rd.item_code
	INNER JOIN ".TB_PREF."requisitions r ON rd.requisition_id = r.requisition_id
	LEFT JOIN ".TB_PREF."suppliers s ON rd.supplier_id = s.supplier_id
	WHERE (r.completed = 1) AND (rd.lpo_id = 0)";
	$sql .= " AND rd.wo_id = ".db_escape($wo_id);
	$sql .= " ORDER BY r.requisition_id";

	return db_query($sql, "could not get all requisition_details");
}

function get_count_requisition_details()
{
	$sql = "SELECT requisition_detail_id as TotalRecord
			FROM ".TB_PREF."requisition_details 
			ORDER BY requisition_detail_id 
			DESC LIMIT 1";
	$query = db_query($sql, "could not get all requisition_details");
	return db_fetch($query);
}

function update_po_reconcile($reconcile_id, $make_po)
{
	$sql = "UPDATE ".TB_PREF."requisition_details SET make_po=".db_escape($make_po)
		." WHERE requisition_id=".db_escape($reconcile_id);

	db_query($sql, "Can't change reconciliation status");
	// save last reconcilation status (date, end balance)
}

function get_requisition_detail($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."requisition_details WHERE requisition_detail_id=".db_escape($selected_id);

	$result = db_query($sql, "could not get requisition detail");

	return db_fetch($result);
}

function delete_requisition_detail($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."requisition_details WHERE requisition_detail_id=".db_escape($selected_id);

	db_query($sql, "could not delete requisition detail");
}


?>
