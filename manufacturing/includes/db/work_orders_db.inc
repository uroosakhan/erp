<?php

/*
	Common entry procedure for all work order types
*/

function add_work_order($wo_ref, $loc_code, $units_reqd, $stock_id, $type, $date_, $required_by, $memo_,
                        $costs, $cr_acc, $labour, $cr_lab_acc, $bom_qty,$amount3,$sale_order, $WorkOrder, $AllowCart,$amount3_req,$batch,
                        $exp_date)
{

    global $Refs;

    begin_transaction();
    $args = func_get_args();
    $args = (object)array_combine(array('wo_ref', 'loc_code', 'units_reqd', 'stock_id',
        'type', 'date_', 'required_by', 'memo_', 'costs', 'cr_acc', 'labour', 'cr_lab_acc'), $args);

    $args->woid = 0;
    hook_db_prewrite($args, ST_WORKORDER);

    if ($type != WO_ADVANCED)
        $required_by = $date_;

   
	$batch_row = get_batch_by_name($batch);

if($type != 1 && $batch_row['name'] == '' ) {

    if ($batch != '')
        add_batch($batch, $exp_date);
    $batch_id = db_insert_id();
}
elseif($type != 1 && $batch_row['name'] != '' ){
    $batch_id =  $batch_row['id'];
}
elseif ($type == 0 && $batch_row['name'] != '')
{
    $batch_id =  $batch_row['id'];
}
else{
    $batch_id = $batch;
}


    $date = date2sql($date_);
    $required = date2sql($required_by);
    $sql = "INSERT INTO ".TB_PREF."workorders (wo_ref, loc_code, units_reqd, stock_id,
		type, date_, required_by, sale_order,amount3_req,batch)
    	VALUES (".db_escape($wo_ref).", ".db_escape($loc_code).", "
        .db_escape($units_reqd).", ".db_escape($stock_id).",
		".db_escape($type).", '$date', ".db_escape($required).", ".db_escape($sale_order).",
		 ".db_escape($amount3).",
		 ".db_escape($batch_id).")";
    db_query($sql, "could not add work order");

    $woid = db_insert_id();

    add_comments(ST_WORKORDER, $woid, $required_by, $memo_);

    $Refs->save(ST_WORKORDER, $woid, $wo_ref);
    $Refs->save(ST_BATCH, $woid, $batch);
    add_audit_trail(ST_WORKORDER, $woid, $date_);

    $args->woid = $woid;
    hook_db_postwrite($args, ST_WORKORDER);

    if ($type != WO_ADVANCED)
    {
        $stockitem = get_item($stock_id);
        release_work_order($woid, $date_, '', $units_reqd, $type, $WorkOrder, $AllowCart);
        if ($costs != 0)
            add_wo_costs_journal($woid, $costs, WO_OVERHEAD, $cr_acc, $date_, $stockitem["dimension_id"], $stockitem["dimension2_id"]);

        if ($labour != 0)
            add_wo_costs_journal($woid, $labour, WO_LABOUR, $cr_lab_acc, $date_, $stockitem["dimension_id"], $stockitem["dimension2_id"]);

        if ($type == WO_UNASSEMBLY)
            $units_reqd = -$units_reqd;

        $ref = $Refs->get_next(ST_MANURECEIVE, null, $date_);
        work_order_produce($woid, $ref, $units_reqd, $date_, '', true, $amount3, $costs, $labour,$batch_id);
    }

    commit_transaction();

    return $woid;
}

//--------------------------------------------------------------------------------------

function update_work_order($woid, $loc_code, $units_reqd, $stock_id,
                           $date_, $required_by, $memo_, $sale_order)
{
    begin_transaction();
    $args = func_get_args();
    $args = (object)array_combine(array('woid', 'loc_code', 'units_reqd', 'stock_id',
        'date_', 'required_by', 'memo_'), $args);
    hook_db_prewrite($args, ST_WORKORDER);

    $date = date2sql($date_);
    $required = date2sql($required_by);

    $sql = "UPDATE ".TB_PREF."workorders SET loc_code=".db_escape($loc_code).",
		units_reqd=".db_escape($units_reqd).", stock_id=".db_escape($stock_id).",
		required_by=".db_escape($required).",
		date_='$date',
		sale_order=".db_escape($sale_order)."
		WHERE id = ".db_escape($woid);

    db_query($sql, "could not update work order");

    update_comments(ST_WORKORDER, $woid, null, $memo_);
    add_audit_trail(ST_WORKORDER, $woid, $date_, _("Updated."));

    hook_db_postwrite($args, ST_WORKORDER);
    commit_transaction();
}

function delete_work_order($woid, $stock_id, $qty, $date)
{
    begin_transaction();
    hook_db_prevoid(ST_WORKORDER, $woid);

    // delete the work order requirements
    delete_wo_requirements($woid);

    // delete the actual work order
    $sql = "DELETE FROM ".TB_PREF."workorders WHERE id=".db_escape($woid);
    db_query($sql,"The work order could not be deleted");

    delete_comments(ST_WORKORDER, $woid);
    add_audit_trail(ST_WORKORDER, $woid, $date, _("Canceled."));

    commit_transaction();
}

//--------------------------------------------------------------------------------------

function get_work_order_for_view($woid, $allow_null=false)
{
    $sql = "SELECT wo.*,st.description As StockItemName,l.location_name,
    		l.delivery_address,l.email, l.contact, st.inventory_account, st.wip_account,sm.amount3
		FROM ".TB_PREF."workorders wo, 
		" .TB_PREF."stock_master st, 
		" .TB_PREF."stock_moves sm, 
		" .TB_PREF."locations l
		WHERE st.stock_id=wo.stock_id
		AND	l.loc_code=wo.loc_code
		AND	wo.id = sm.trans_no
		AND	sm.type = 26
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


function get_work_order($woid, $allow_null=false)
{
    $sql = "SELECT wo.*,st.description As StockItemName,l.location_name,
    		l.delivery_address,l.email, l.contact, st.inventory_account, st.con_factor,st.wip_account
		FROM ".TB_PREF."workorders wo, 
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

//--------------------------------------------------------------------------------------

function work_order_has_productions($woid)
{
    $sql = "SELECT COUNT(*) FROM ".TB_PREF."wo_manufacture WHERE workorder_id=".db_escape($woid);
    $result = db_query($sql, "query work order for productions");

    $myrow = db_fetch_row($result);
    return ($myrow[0] > 0);
}


//--------------------------------------------------------------------------------------

function work_order_has_issues($woid)
{
    $sql = "SELECT COUNT(*) FROM ".TB_PREF."wo_issues WHERE workorder_id=".db_escape($woid);
    $result = db_query($sql, "query work order for issues");

    $myrow = db_fetch_row($result);
    return ($myrow[0] > 0);
}

//--------------------------------------------------------------------------------------

function work_order_has_payments($woid)
{
    $result = get_gl_wo_cost_trans($woid);

    return (db_num_rows($result) != 0);
}

//--------------------------------------------------------------------------------------

function release_work_order($woid, $releaseDate, $memo_, $units_reqd, $type, $WorkOrder, $AllowCart)
{
    begin_transaction();

    $myrow = get_work_order($woid);
    $stock_id = $myrow["stock_id"];

    $date = date2sql($releaseDate);

    $sql = "UPDATE ".TB_PREF."workorders SET released_date='$date',
		released=1 WHERE id = ".db_escape($woid);
    db_query($sql, "could not release work order");

    // create wo_requirements records according to current BOM
    if($type != WO_ADVANCED)
        create_wo_requirements_($woid, $stock_id, $units_reqd, $WorkOrder, $AllowCart);
    elseif($type == WO_ADVANCED)
        create_wo_requirements($woid, $stock_id);
    add_comments(ST_WORKORDER, $woid, $releaseDate, $memo_);
    add_audit_trail(ST_WORKORDER, $woid, sql2date($myrow['date_']), _("Released."));

    commit_transaction();
}

//--------------------------------------------------------------------------------------

function close_work_order($woid)
{
    $sql = "UPDATE ".TB_PREF."workorders SET closed=1 WHERE id = ".db_escape($woid);
    db_query($sql, "could not close work order");
}

//--------------------------------------------------------------------------------------

function work_order_is_closed($woid)
{
    $sql = "SELECT closed FROM ".TB_PREF."workorders WHERE id = ".db_escape($woid);
    $result = db_query($sql, "could not query work order");
    $row = db_fetch_row($result);
    return ($row[0] > 0);
}

//--------------------------------------------------------------------------------------
/*
	Update finished items quantity in work order, and close order either if all the order is produced,
	or on user demand. Returns calculated unit cost on close, or null otherwise.
*/
function work_order_update_finished_quantity($woid, $quantity, $force_close=0,$amount3)
{

    $sql = "UPDATE ".TB_PREF."workorders SET units_issued = units_issued + ".db_escape($quantity).",
	amount3_issued = amount3_issued + ".db_escape($amount3)."
		WHERE id = ".db_escape($woid). " AND !closed";

    db_query($sql, "The work order issued quantity couldn't be updated");

    $sql = "UPDATE ".TB_PREF."workorders SET closed = ((units_issued >= units_reqd) OR ".db_escape($force_close).")
		WHERE id = ".db_escape($woid);

    db_query($sql, "The work order couldn't be closed");

    return db_num_affected_rows();	// returns 1 if WO has been closed
}

function reopen_work_order($woid)
{
    // FIXME: update_material_cost
//		update_material_cost($work_order['stock_id'], -$work_order['units_issued'], ... );

    void_gl_trans(ST_WORKORDER, $woid);

    void_stock_move(ST_WORKORDER, $woid);

    $sql = "UPDATE ".TB_PREF."workorders SET closed=0 WHERE id = "
        .db_escape($woid);

    db_query($sql, "The work order couldn't be reopened");
}

//--------------------------------------------------------------------------------------
/*
	Void all work order related operations
*/
function void_work_order($woid)
{
    begin_transaction();
    hook_db_prevoid(ST_WORKORDER, $woid);

    $work_order = get_work_order($woid);

    if ($work_order['closed'])
        reopen_work_order($woid);

    if ($work_order['units_issued'])
    {
        $prods = get_work_order_productions($woid);
        while ($prod = db_fetch($prods))
            void_work_order_produce($prod['id']);
    }

    // void addtional material issues
    $issues = get_work_order_issues($woid);
    while($issue = db_fetch($issues))
        void_work_order_issue($issue['issue_no']);

    // void all related stock moves
    void_stock_move(ST_WORKORDER, $woid);

    // void any related costing records
    void_wo_costing($woid);

    // clear the requirements units received
    void_wo_requirements($woid);

    // close workorder



    //Commented Query changed by Ryan (07-08-18)(Discussion required with Mr.Ansar on this )
// 		$sql = "UPDATE ".TB_PREF."workorders SET closed=1,units_reqd=0,units_issued=0 WHERE id = "
// 		.db_escape($woid);

    $sql = "UPDATE ".TB_PREF."workorders SET units_reqd=0,units_issued=0,amount3_req=0,amount3_issued=0
		WHERE id = "
        .db_escape($woid);

    db_query($sql, "The work order couldn't be voided");

    commit_transaction();
}

function get_sql_for_work_orders($outstanding_only, $from, $to, $stock_id, $location = ALL_TEXT,
                                 $order_no='', $order_ref = '', $overdue = false, $order_type = '',
                                 $sale_order = '', $memo= '', $batch= '')
{

    $date_after = date2sql($from);
    $date_to = date2sql($to);

    $sql = "SELECT
		workorder.id,
		workorder.wo_ref,
		workorder.date_,
		so.reference,
		'',
		workorder.type,
		location.location_name,
		item.description,
		workorder.units_reqd,
		workorder.units_issued,
		workorder.batch,
		workorder.required_by,
		com.memo_,
		workorder.released_date,
		workorder.closed,
		workorder.released,
		workorder.stock_id,
		unit.decimals
		FROM ".TB_PREF."workorders as workorder
		
		LEFT JOIN " .TB_PREF."stock_master as item
		ON workorder.stock_id = item.stock_id 
		
		LEFT JOIN " .TB_PREF."item_units as unit
		ON item.units=unit.abbr
		
		LEFT JOIN " .TB_PREF."locations as location
		ON workorder.loc_code=location.loc_code
		
		LEFT JOIN " .TB_PREF."sales_orders as so
        ON workorder.sale_order=so.order_no
		
		LEFT JOIN " .TB_PREF."comments as com
		ON workorder.id=com.id AND com.type = 26
		
		LEFT JOIN " .TB_PREF."batch as batch
		ON workorder.batch=batch.id 
		
		WHERE workorder.date_ >= '$date_after'
			AND workorder.date_ <= '$date_to'
			";

    if (check_value('OpenOnly') || $outstanding_only != 0)
    {
        $sql .= " AND workorder.closed=0";
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

    if ($sale_order != '')
    {
        $sql .= " AND so.reference LIKE ".db_escape('%'.$sale_order.'%');
    }

    if ($order_type != 0)
    {
        $sql .= " AND workorder.type=".db_escape($order_type);
    }

    if ($stock_id != ALL_TEXT)
    {
        $sql .= " AND workorder.stock_id=".db_escape($stock_id);
    }

    if ($memo != ALL_TEXT)
    {
        $sql .= " AND com.memo_ LIKE ".db_escape('%'.$memo.'%');
    }

    if ($overdue)
    {
        $Today = date2sql(Today());

        $sql .= " AND workorder.required_by < '$Today' ";
    }
    
    if ($batch != ALL_TEXT)
    {
        $sql .= " AND batch.name LIKE ".db_escape('%'.$batch.'%');
    }
    
    $sql .= " ORDER BY workorder.id DESC";
    return $sql;
}

function get_sql_for_where_used($stock_id)
{
    $sql = "SELECT 
			bom.parent,
			workcentre.name As WorkCentreName,
			location.location_name,
			bom.quantity,
			parent.description
			FROM ".TB_PREF."bom as bom, "
        .TB_PREF."stock_master as parent, "
        .TB_PREF."workcentres as workcentre, "
        .TB_PREF."locations as location
			WHERE bom.parent = parent.stock_id 
				AND bom.workcentre_added = workcentre.id
				AND bom.loc_code = location.loc_code
				AND bom.component=".db_escape($stock_id);
    return $sql;
}
//--------------------------------------------------------------------------------------
function get_gl_wo_cost($woid, $cost_type)
{
    $cost = 0;
    $result = get_gl_wo_cost_trans($woid, $cost_type);
    while ($row = db_fetch($result))
        $cost += -$row['amount'];
    return $cost;
}

