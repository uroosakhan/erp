<?php

/*
	Common entry procedure for all work order types
*/

function add_work_order_req($wo_ref, $loc_code, $units_reqd, $stock_id, $wo_type, $date_, $required_by, $memo_,
        $costs, $cr_acc, $labour, $cr_lab_acc, $bom_qty,$amount3,$sale_order, $WorkOrder)
{
	begin_transaction();
    global $Refs;
    $sql = "INSERT INTO ".TB_PREF."workorder_req (type, wo_ref, loc_code, units_reqd, stock_id, 
            wo_type, date_, required_by, memo_, costs, cr_acc, labour, cr_lab_acc, bom_qty, amount3, 
            sale_order)
            VALUES (" .db_escape(ST_MANUORDERREQ) . ",
            " . db_escape($wo_ref) .", 
            " .db_escape($loc_code) . ",
            " .db_escape($units_reqd) . ", ".
            db_escape($stock_id) .",".
            db_escape($wo_type) .",".
            db_escape(date2sql($date_)) .",
            ".db_escape(date2sql($required_by)) .", " .
            db_escape($memo_) . ", " .
            db_escape($costs) . ", " .
            db_escape($cr_acc) . ", " .
            db_escape($labour). ", " .
            db_escape($cr_lab_acc). ", " .
            db_escape($bom_qty). ", " .
            db_escape($amount3). ", " .
            db_escape($sale_order). ")";
    db_query($sql, "could not be added in ".TB_PREF."workorder_req_details");
    $workorder_req_id = db_insert_id();
    add_comments(ST_MANUORDERREQ, $workorder_req_id, $required_by, $memo_);
    $Refs->save(ST_MANUORDERREQ, $workorder_req_id, $wo_ref);

    foreach ($WorkOrder->line_item_wo as $line_no => $stock_item) {
        $amount3 = $stock_item->amount33;
        $item = get_item_edit_info($stock_item->stock_id);
        if($amount3 == "")
            $amount3 = $item['amount3'];
        $qty_issue_qty = $stock_item->quantity * $stock_item->issue_qty; // multiply qty X Issue Qty
        $sql = "INSERT INTO ".TB_PREF."workorder_req_details (workorder_id, stock_id, workcentre, 
                units_req, loc_code, amount3, quantity, units, qty_issue_qty)
                VALUES ($workorder_req_id, " . "'" . $stock_item->stock_id . "'" . ",
                '". $stock_item->workcentre_added1 . "',
                $stock_item->issue_qty, '".$stock_item->loc_code1."', '".$amount3."', '".$stock_item->quantity."', 
                '".$stock_item->units."', 
                '".$qty_issue_qty."')";
        db_query($sql, "could not be added in ".TB_PREF."workorder_req_details");
    }
	commit_transaction();

	return $workorder_req_id;
}
//--------------------------------------------------------------------------------------
function update_work_order_req($wo_ref, $loc_code, $units_reqd, $stock_id, $wo_type, $date_, $required_by, $memo_,
        $costs, $cr_acc, $labour, $cr_lab_acc, $bom_qty,$amount3,$sale_order, $WorkOrder)
{
	begin_transaction();

    $sql = "UPDATE ".TB_PREF."workorder_req SET 
        loc_code = " . db_escape($loc_code) . ",
        units_reqd = ". db_escape($units_reqd) .",
        stock_id = ". db_escape($stock_id) .",
        wo_type = ". db_escape($wo_type) .",
        date_ = " . db_escape(date2sql($date_)) . ",
        required_by = " .db_escape($required_by) . ",
        memo_ = " . db_escape($memo_) .",
        costs = " . db_escape($costs) . ",
        cr_acc = " . db_escape($cr_acc) . ",
        labour = " .db_escape($labour) . ",
        cr_lab_acc = " .db_escape($cr_lab_acc) .",
        bom_qty = " .db_escape($bom_qty) .",
        amount3 = " .db_escape($amount3). ",
        sale_order = ".db_escape($sale_order) ." 
        WHERE id = " . db_escape($WorkOrder->order_id) ."
        AND type = ".ST_MANUORDERREQ;
    db_query($sql, "order Cannot be Updated workorder_req");
    $sql = "DELETE FROM ".TB_PREF."workorder_req_details WHERE workorder_id =" . db_escape($WorkOrder->order_id);
    db_query($sql, "Old WO Cannot be Deleted");
    foreach ($WorkOrder->line_item_wo as $line_no => $stock_item) {
        $amount3 = $stock_item->amount33;
        $item = get_item_edit_info($stock_item->stock_id);
        if($amount3 == "")
            $amount3 = $item['amount3'];
        $qty_issue_qty = $stock_item->quantity * $stock_item->issue_qty; // multiply qty X Issue Qty
        $sql = "INSERT INTO ".TB_PREF."workorder_req_details (workorder_id, stock_id, workcentre, 
            units_req, loc_code, amount3, quantity, units, qty_issue_qty)
			VALUES ($WorkOrder->order_id, " . "'" . $stock_item->stock_id . "'" . ",
			'". $stock_item->workcentre_added1 . "',
			$stock_item->issue_qty, '".$stock_item->loc_code1."', '".$amount3."', '".$stock_item->quantity."', 
			'".$stock_item->units."', 
			'".$qty_issue_qty."')";
        db_query($sql, "The work order requirements could not be added");
    }

	commit_transaction();

	return $WorkOrder->order_id;
}
//--------------------------------------------------------------------------------------

function get_sql_for_work_orders_requisition($outstanding_only, $stock_id, $location = ALL_TEXT, $order_no='', $order_ref = '', $overdue = false)
{
    $sql = "SELECT
		workorder.id,
		workorder.wo_ref,
		
		location.location_name,
		item.description,
		workorder.units_reqd,

		workorder.date_,
		workorder.required_by,

		workorder.inactive,
		workorder.stock_id,
		unit.decimals,
		workorder.type
		FROM ".TB_PREF."workorder_req as workorder,"
        .TB_PREF."stock_master as item,"
        .TB_PREF."item_units as unit,"
        .TB_PREF."locations as location
		WHERE workorder.stock_id=item.stock_id 
			AND workorder.loc_code=location.loc_code
			AND item.units=unit.abbr";

    if (check_value('OpenOnly') || $outstanding_only != 0)
    {
        $sql .= " AND workorder.inactive=0";
    }

    if ($location != ALL_TEXT)
    {
        $sql .= " AND workorder.loc_code=".db_escape($location);
    }

    if ($order_no != '')
    {
        $sql .= " AND workorder.id LIKE ".db_escape('%'.$order_no.'%');
    }

    if ($order_ref != '')
    {
        $sql .= " AND workorder.wo_ref LIKE ".db_escape('%'.$order_ref.'%');
    }

    if ($stock_id != ALL_TEXT)
    {
        $sql .= " AND workorder.stock_id=".db_escape($stock_id);
    }

    if ($overdue)
    {
        $Today = date2sql(Today());

        $sql .= " AND workorder.required_by < '$Today' ";
    }
    $sql .= " ORDER BY workorder.id DESC";
    return $sql;
}
//--------------------------------------------------------------------------------------

function get_work_order_requisition($order_no, $trans_type)
{
    $sql = "SELECT
		workorder.id,
		workorder.wo_ref,
		location.location_name,
		item.description,
		workorder.units_reqd,
		workorder.date_,
		workorder.required_by,
		workorder.inactive,
		workorder.stock_id,
		unit.decimals,
		workorder.memo_,
		workorder.costs,
		workorder.cr_acc,
		workorder.labour,
		workorder.cr_lab_acc,
		workorder.sale_order,
		workorder.amount3,
		workorder.loc_code
		FROM ".TB_PREF."workorder_req as workorder,"
        .TB_PREF."stock_master as item,"
        .TB_PREF."item_units as unit,"
        .TB_PREF."locations as location
		WHERE workorder.stock_id=item.stock_id 
			AND workorder.loc_code=location.loc_code
			AND item.units=unit.abbr";
    $sql .= " AND workorder.id = ".db_escape($order_no)."
            AND workorder.type = ".db_escape($trans_type);

    $result = db_query($sql, "order Retreival");

    $num = db_num_rows($result);
    if ($num > 1)
    {
        display_warning("You have duplicate requisition in database: (type:$trans_type, number:$order_no).");
    }
    else if ($num == 1)
    {
        return db_fetch($result);
    }
    else
        display_warning("You have missing or invalid work order requisition in database (type:$trans_type, number:$order_no).");

}
//--------------------------------------------------------------------------------------
function get_work_order_requisition_details($order_no)
{
    $sql = "SELECT details.*, master.material_cost as ComponentCost, master.description
            FROM ".TB_PREF."workorder_req_details details
            INNER JOIN ".TB_PREF."stock_master master ON master.stock_id = details.stock_id
            WHERE details.workorder_id = ".db_escape($order_no);
    return db_query($sql, "Error");
}
//--------------------------------------------------------------------------------------

function read_work_order_requisition($order_no, &$wo_order, $trans_type)
{
    $myrow = get_work_order_requisition($order_no, $trans_type);
    $wo_order->trans_type = $myrow['trans_type'];
    $wo_order->Editable = $order_no;
    $wo_order->wo_type = $myrow["wo_type"];
    $wo_order->stock_id_ = $myrow["stock_id"];
    $wo_order->StockLocation = $myrow["loc_code"];
    $wo_order->sale_order = $myrow["sale_order"];
    $wo_order->amount3 = $myrow["amount3"];
    $wo_order->quantity = $myrow["units_reqd"];
    $wo_order->Labour = $myrow["labour"];
    $wo_order->cr_lab_acc = $myrow["cr_lab_acc"];
    $wo_order->Costs = $myrow["costs"];
    $wo_order->cr_acc = $myrow["cr_acc"];
    $wo_order->memo_ = $myrow["memo_"];
    $wo_order->date_ = sql2date($myrow["date_"]);
    $wo_order->RequDate = sql2date($myrow["RequDate"]);
    $wo_order->wo_ref = $myrow["wo_ref"];

    $result = get_work_order_requisition_details($order_no);
    if (db_num_rows($result) > 0)
    {
        $line_no = 0;
        while ($myrow = db_fetch($result))
        {
//            $pref=get_company_prefs();
//            if($pref['alt_uom'] == 1 ) {
//                $item = get_item($myrow["stk_code"]);
//                $dec = 2;
//                if ($myrow['units_id'] != $item['units']) {
//                    if ($item['con_type'] == 0) {
//                        $qty = round2($myrow["quantity"] * $myrow['con_factor'], $dec);
//                        $qty_done = round2($myrow["qty_done"] * $myrow['con_factor'], $dec);
//                    } else {
//                        $qty = $myrow['con_factor'] / $myrow["quantity"];
//                        $qty_done = $myrow['qty_done'] / $myrow["quantity"];
//                    }
//                } else {
//                    $qty = $myrow["quantity"];
//                    $qty_done = $myrow['qty_done'];
//                }
//            }
//            else {
//                $qty = $myrow["quantity"];
//                $qty_done = $myrow['qty_done'];
//            }
            $wo_order->add_to_cart($line_no, $myrow['stock_id'], $myrow['quantity'], $myrow['loc_code'],
                $myrow['workcentre'], $myrow['units_req'],$myrow['amount3']);
            $line_no++;
        }
    }
    return true;
}

//function update_work_order($woid, $loc_code, $units_reqd, $stock_id,
//					$date_, $required_by, $memo_, $sale_order)
//{
//	begin_transaction();
//	$args = func_get_args();
//	$args = (object)array_combine(array('woid', 'loc_code', 'units_reqd', 'stock_id',
//		'date_', 'required_by', 'memo_'), $args);
//	hook_db_prewrite($args, ST_WORKORDER);
//
//	$date = date2sql($date_);
//	$required = date2sql($required_by);
//
//	$sql = "UPDATE ".TB_PREF."workorders SET loc_code=".db_escape($loc_code).",
//		units_reqd=".db_escape($units_reqd).", stock_id=".db_escape($stock_id).",
//		required_by=".db_escape($required).",
//		date_='$date',
//		sale_order=".db_escape($sale_order)."
//		WHERE id = ".db_escape($woid);
//
//	db_query($sql, "could not update work order");
//
//	update_comments(ST_WORKORDER, $woid, null, $memo_);
//	add_audit_trail(ST_WORKORDER, $woid, $date_, _("Updated."));
//
//	hook_db_postwrite($args, ST_WORKORDER);
//	commit_transaction();
//}
//
function delete_work_order_req($woid, $type, $date)
{
    begin_transaction();
	hook_db_prevoid($type, $woid);
	$sql = "DELETE FROM ".TB_PREF."workorders_req WHERE id=".db_escape($woid);
	db_query($sql,"The work order could not be deleted");
    $sql1 = "DELETE FROM ".TB_PREF."workorder_req_details WHERE workorder_id=".db_escape($woid);
    db_query($sql1,"The work order could not be deleted");
	delete_comments($type, $woid);
	add_audit_trail($type, $woid, sql2date($date), _("Canceled."));
	commit_transaction();
}
//
////--------------------------------------------------------------------------------------
//
//function get_work_order_for_view($woid, $allow_null=false)
//{
//     $sql = "SELECT wo.*,st.description As StockItemName,l.location_name,
//    		l.delivery_address,l.email, l.contact, st.inventory_account, st.wip_account,sm.amount3
//		FROM ".TB_PREF."workorders wo,
//		" .TB_PREF."stock_master st,
//		" .TB_PREF."stock_moves sm,
//		" .TB_PREF."locations l
//		WHERE st.stock_id=wo.stock_id
//		AND	l.loc_code=wo.loc_code
//		AND	wo.id = sm.trans_no
//		AND	sm.type = 26
//		AND wo.id=".db_escape($woid)."
//		GROUP BY wo.id";
//
//	$result = db_query($sql, "The work order issues could not be retrieved");
//
//	if (!$allow_null && db_num_rows($result) == 0)
//	{
//		display_db_error("Could not find work order $woid", $sql);
//		return false;
//	}
//
//	return db_fetch($result);
//}
//
//
function get_work_order_requisition_report($woid, $allow_null=false)
{
     $sql = "SELECT wo.*,st.description As StockItemName,l.location_name,wo.wo_type as type,
    		l.delivery_address,l.email, l.contact, st.inventory_account, st.con_factor,st.wip_account
		FROM ".TB_PREF."workorder_req wo,
		" .TB_PREF."stock_master st,
		" .TB_PREF."locations l
		WHERE st.stock_id=wo.stock_id
		AND	l.loc_code=wo.loc_code
		AND wo.id=".db_escape($woid)."
		GROUP BY wo.id";

	$result = db_query($sql, "The work order issues could not be retrieved");

	if (!$allow_null && db_num_rows($result) == 0)
	{
		display_db_error("Could not find work order $woid", $sql);
		return false;
	}

	return db_fetch($result);
}
//
////--------------------------------------------------------------------------------------
//
//function work_order_has_productions($woid)
//{
//	$sql = "SELECT COUNT(*) FROM ".TB_PREF."wo_manufacture WHERE workorder_id=".db_escape($woid);
//	$result = db_query($sql, "query work order for productions");
//
//	$myrow = db_fetch_row($result);
//	return ($myrow[0] > 0);
//}
//
//
////--------------------------------------------------------------------------------------
//
//function work_order_has_issues($woid)
//{
//	$sql = "SELECT COUNT(*) FROM ".TB_PREF."wo_issues WHERE workorder_id=".db_escape($woid);
//	$result = db_query($sql, "query work order for issues");
//
//	$myrow = db_fetch_row($result);
//	return ($myrow[0] > 0);
//}
//
////--------------------------------------------------------------------------------------
//
//function work_order_has_payments($woid)
//{
//	$result = get_gl_wo_cost_trans($woid);
//
//    return (db_num_rows($result) != 0);
//}
//
////--------------------------------------------------------------------------------------
//
//function release_work_order($woid, $releaseDate, $memo_, $units_reqd, $type, $WorkOrder, $AllowCart)
//{
//	begin_transaction();
//
//	$myrow = get_work_order($woid);
//	$stock_id = $myrow["stock_id"];
//
//	$date = date2sql($releaseDate);
//
//	$sql = "UPDATE ".TB_PREF."workorders SET released_date='$date',
//		released=1 WHERE id = ".db_escape($woid);
//	db_query($sql, "could not release work order");
//
//	// create wo_requirements records according to current BOM
//	if($type != WO_ADVANCED)
//		create_wo_requirements_($woid, $stock_id, $units_reqd, $WorkOrder, $AllowCart);
//	elseif($type == WO_ADVANCED)
//		create_wo_requirements($woid, $stock_id);
//	add_comments(ST_WORKORDER, $woid, $releaseDate, $memo_);
//	add_audit_trail(ST_WORKORDER, $woid, sql2date($myrow['date_']), _("Released."));
//
//	commit_transaction();
//}
//
////--------------------------------------------------------------------------------------
//
//function close_work_order($woid)
//{
//	$sql = "UPDATE ".TB_PREF."workorders SET closed=1 WHERE id = ".db_escape($woid);
//	db_query($sql, "could not close work order");
//}
//
////--------------------------------------------------------------------------------------
//
//function work_order_is_closed($woid)
//{
//	$sql = "SELECT closed FROM ".TB_PREF."workorders WHERE id = ".db_escape($woid);
//	$result = db_query($sql, "could not query work order");
//	$row = db_fetch_row($result);
//	return ($row[0] > 0);
//}
//
////--------------------------------------------------------------------------------------
///*
//	Update finished items quantity in work order, and close order either if all the order is produced,
//	or on user demand. Returns calculated unit cost on close, or null otherwise.
//*/
//function work_order_update_finished_quantity($woid, $quantity, $force_close=0,$amount3)
//{
//
//	$sql = "UPDATE ".TB_PREF."workorders SET units_issued = units_issued + ".db_escape($quantity).",
//	amount3_issued = amount3_issued + ".db_escape($amount3)."
//		WHERE id = ".db_escape($woid). " AND !closed";
//
//	db_query($sql, "The work order issued quantity couldn't be updated");
//
//	$sql = "UPDATE ".TB_PREF."workorders SET closed = ((units_issued >= units_reqd) OR ".db_escape($force_close).")
//		WHERE id = ".db_escape($woid);
//
//	db_query($sql, "The work order couldn't be closed");
//
//	return db_num_affected_rows();	// returns 1 if WO has been closed
//}
//
//function reopen_work_order($woid)
//{
//		// FIXME: update_material_cost
////		update_material_cost($work_order['stock_id'], -$work_order['units_issued'], ... );
//
//		void_gl_trans(ST_WORKORDER, $woid);
//
//		void_stock_move(ST_WORKORDER, $woid);
//
//		$sql = "UPDATE ".TB_PREF."workorders SET closed=0 WHERE id = "
//			.db_escape($woid);
//
//		db_query($sql, "The work order couldn't be reopened");
//}
//
////--------------------------------------------------------------------------------------
///*
//	Void all work order related operations
//*/
//function void_work_order($woid)
//{
//	begin_transaction();
//	hook_db_prevoid(ST_WORKORDER, $woid);
//
//	$work_order = get_work_order($woid);
//
//	if ($work_order['closed'])
//		reopen_work_order($woid);
//
//	if ($work_order['units_issued'])
//	{
//		$prods = get_work_order_productions($woid);
//		while ($prod = db_fetch($prods))
//			void_work_order_produce($prod['id']);
//	}
//
//	// void addtional material issues
//	$issues = get_work_order_issues($woid);
//	while($issue = db_fetch($issues))
//		void_work_order_issue($issue['issue_no']);
//
//	// void all related stock moves
//	void_stock_move(ST_WORKORDER, $woid);
//
//	// void any related costing records
//	void_wo_costing($woid);
//
//	// clear the requirements units received
//	void_wo_requirements($woid);
//
//	// close workorder
//	$sql = "UPDATE ".TB_PREF."workorders SET closed=1,units_reqd=0,units_issued=0 WHERE id = "
//		.db_escape($woid);
//	db_query($sql, "The work order couldn't be voided");
//
//	commit_transaction();
//}
//
//function get_sql_for_work_orders($outstanding_only, $stock_id, $location = ALL_TEXT, $order_no='', $order_ref = '', $overdue = false)
//{
//	$sql = "SELECT
//		workorder.id,
//		workorder.wo_ref,
//		workorder.type,
//		location.location_name,
//		item.description,
//		workorder.units_reqd,
//		workorder.units_issued,
//		workorder.date_,
//		workorder.required_by,
//		workorder.released_date,
//		workorder.closed,
//		workorder.released,
//		workorder.stock_id,
//		unit.decimals
//		FROM ".TB_PREF."workorders as workorder,"
//			.TB_PREF."stock_master as item,"
//			.TB_PREF."item_units as unit,"
//			.TB_PREF."locations as location
//		WHERE workorder.stock_id=item.stock_id
//			AND workorder.loc_code=location.loc_code
//			AND item.units=unit.abbr";
//
//	if (check_value('OpenOnly') || $outstanding_only != 0)
//	{
//		$sql .= " AND workorder.closed=0";
//	}
//
//	if ($location != ALL_TEXT)
//	{
//		$sql .= " AND workorder.loc_code=".db_escape($location);
//	}
//
//	if ($order_no != '')
//	{
//		$sql .= " AND workorder.id LIKE ".db_escape('%'.$order_no.'%');
//	}
//
//	if ($order_ref != '')
//	{
//		$sql .= " AND workorder.wo_ref LIKE ".db_escape('%'.$order_ref.'%');
//	}
//
//	if ($stock_id != ALL_TEXT)
//	{
//		$sql .= " AND workorder.stock_id=".db_escape($stock_id);
//	}
//
//	if ($overdue)
//	{
//		$Today = date2sql(Today());
//
//		$sql .= " AND workorder.required_by < '$Today' ";
//	}
//	$sql .= " ORDER BY workorder.id DESC";
//	return $sql;
//}
//
//function get_sql_for_where_used($stock_id)
//{
//	$sql = "SELECT
//			bom.parent,
//			workcentre.name As WorkCentreName,
//			location.location_name,
//			bom.quantity,
//			parent.description
//			FROM ".TB_PREF."bom as bom, "
//				.TB_PREF."stock_master as parent, "
//				.TB_PREF."workcentres as workcentre, "
//				.TB_PREF."locations as location
//			WHERE bom.parent = parent.stock_id
//				AND bom.workcentre_added = workcentre.id
//				AND bom.loc_code = location.loc_code
//				AND bom.component=".db_escape($stock_id);
//	return $sql;
//}
////--------------------------------------------------------------------------------------
//function get_gl_wo_cost($woid, $cost_type)
//{
//	$cost = 0;
//	$result = get_gl_wo_cost_trans($woid, $cost_type);
//	while ($row = db_fetch($result))
//		$cost += -$row['amount'];
//	return $cost;
//}