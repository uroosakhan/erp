<?php

//-------------------------------------------------------------------------------------------------------------

function add_stock_transfer($Items, $location_from, $location_to, $date_, $reference, $memo_)
{
	global $SysPrefs, $path_to_root, $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('Items', 'location_from', 'location_to', 
	'date_', 'reference', 'memo_'), $args);
	$args->trans_no = 0;
	hook_db_prewrite($args, ST_LOCTRANSFER);

	$transfer_id = get_next_trans_no(ST_LOCTRANSFER);

	if ($SysPrefs->loc_notification() == 1)
	{
		include_once($path_to_root . "/inventory/includes/inventory_db.inc");
		$st_ids = array();
		$st_names = array();
		$st_num = array();
		$st_reorder = array();
	}
	foreach ($Items as $line_item)
	{
		$pref = get_company_pref();
		if($pref['item_location'] == 1) {
			$location_from = $line_item->FromStockLocation;
				$location_to = $line_item->ToStockLocation;
		}
		else{
			
		}
		if ($SysPrefs->loc_notification() == 1)
			$loc = calculate_reorder_level($location_from, $line_item, $st_ids, $st_names, $st_num, $st_reorder); 
		if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL7') && $SysPrefs->invent_appr() == 1)
{
    add_stock_transfer_item($transfer_id, $line_item->stock_id, $location_from,
        $location_to, $date_, $reference, 0,$line_item->price,$line_item->person_id,$line_item->discount_percent, $line_item->text1, $line_item->text2, $line_item->text3, $line_item->text4, $line_item->text5, $line_item->text6
        ,$line_item->amount1,$line_item->amount2,$line_item->amount3,$line_item->amount4,$line_item->amount5,
        $line_item->amount6,$line_item->date1,$line_item->date2,$line_item->date3
        ,$line_item->combo1,$line_item->combo2,$line_item->combo3,$line_item->combo4,$line_item->combo5,$line_item->combo6,
        $line_item->units_id,$line_item->con_factor,$line_item->batch, $line_item->quantity);
}
else
{
    add_stock_transfer_item($transfer_id, $line_item->stock_id, $location_from,
        $location_to, $date_, $reference, $line_item->quantity,$line_item->price,$line_item->person_id,$line_item->discount_percent, $line_item->text1, $line_item->text2, $line_item->text3, $line_item->text4, $line_item->text5, $line_item->text6
        ,$line_item->amount1,$line_item->amount2,$line_item->amount3,$line_item->amount4,$line_item->amount5,
        $line_item->amount6,$line_item->date1,$line_item->date2,$line_item->date3
        ,$line_item->combo1,$line_item->combo2,$line_item->combo3,$line_item->combo4,$line_item->combo5,$line_item->combo6,
        $line_item->units_id,$line_item->con_factor,$line_item->batch);
}
	}

	add_comments(ST_LOCTRANSFER, $transfer_id, $date_, $memo_);

	$Refs->save(ST_LOCTRANSFER, $transfer_id, $reference);
	add_audit_trail(ST_LOCTRANSFER, $transfer_id, $date_);

	$args->trans_no = $transfer_id;
	hook_db_postwrite($args, ST_LOCTRANSFER);

	commit_transaction();
	
	if ($SysPrefs->loc_notification() == 1 && count($st_ids) > 0)
		send_reorder_email($loc, $st_ids, $st_names, $st_num, $st_reorder);

	return $transfer_id;
}

//-------------------------------------------------------------------------------------------------------------

// add 2 stock_moves entries for a stock transfer
// $date_ is display date (not sql)
// std_cost is in HOME currency
// it seems the standard_cost field is not used at all

function add_stock_transfer_item($transfer_id, $stock_id, $location_from, $location_to,
   $date_, $reference, $quantity,$discount, $src_no, $src_id,
                         $text1, $text2, $text3, $text4, $text5, $text6,
                         $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
                         $date1, $date2, $date3,
                         $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$units_id,$con_factor,$batch, $approval_qty=0)
{
   $max_id=get_max_movement_id();
   $max_id = $max_id + 2;
   add_stock_move(ST_LOCTRANSFER, $stock_id, $transfer_id, $location_from,
           $date_, $reference, -$quantity,$discount,0, $src_no, $src_id,
      $text1, $text2, $text3, $text4, $text5, $text6,
      $amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
      $date1, $date2, $date3,
      $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$units_id,$con_factor,$batch,$location_from,$max_id, 0,-$approval_qty);

   add_stock_move(ST_LOCTRANSFER, $stock_id, $transfer_id, $location_to,
          $date_, $reference, $quantity,$discount,0, $src_no, $src_id,
      $text1, $text2, $text3, $text4, $text5, $text6,
      $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
      $date1, $date2, $date3,
      $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$units_id,$con_factor,$batch,'',0,0,$approval_qty);

}
//--------------------------------------------------------------------------------------------------------------
function get_stock_transfer_view($trans_no)
{
    $result = get_stock_transfer_items($trans_no);
    if (db_num_rows($result) < 2)
    {
        //display_db_error("transfer with less than 2 items : $trans_no", "");
    }

    // this function is very bad that it assumes that 1st record and 2nd record contain the
    // from and to locations - if get_stock_moves uses a different ordering than trans_no then
    // it will bomb
    $move1 = db_fetch($result);
    $move2 = db_fetch($result);

    // return an array of (From, To)
    if ($move1['qty'] < 0)
        return array($move1, $move2);
    else
        return array($move2, $move1);
}
//-------------------------------------------------------------------------------------------------------------
//
//	Get inventory transfer common data (currently this is still stored in stock_moves table).
//
function get_stock_transfer($trans_no)
{
	// retrieve common data from any two from/to move records
	$sql = "SELECT loc_from.*, loc_to.*
		FROM
			(SELECT trans_no, type, tran_date, reference, move.loc_code as from_loc, loc.location_name as from_name
			FROM ".TB_PREF."stock_moves move
				LEFT JOIN ".TB_PREF."locations loc ON loc.loc_code=move.loc_code
			WHERE type=".ST_LOCTRANSFER." AND trans_no=".db_escape($trans_no). " AND qty<0 LIMIT 1) loc_from,

			(SELECT move.loc_code as to_loc, loc.location_name as to_name
			FROM ".TB_PREF."stock_moves move
				LEFT JOIN ".TB_PREF."locations loc ON loc.loc_code=move.loc_code
			WHERE type=".ST_LOCTRANSFER." AND trans_no=".db_escape($trans_no). " AND qty>0 LIMIT 1) loc_to";

	$result = db_query($sql, "Could not get transfer common data");

	$data = db_fetch($result);

	return $data;
}

//-------------------------------------------------------------------------------------------------------------

function get_stock_transfer_items($trans_no)
{
	$result = get_stock_moves(ST_LOCTRANSFER, $trans_no);

	if (db_num_rows($result) == 0)
	{
		return null;
	}

	return $result;
}

//-------------------------------------------------------------------------------------------------------------

function void_stock_transfer($type_no)
{
	hook_db_prevoid(ST_LOCTRANSFER, $type_no);
	void_stock_move(ST_LOCTRANSFER, $type_no);
}
function get_combo_name($stock_id,$name)
{
	$sql = "SELECT description FROM ".TB_PREF."$name  WHERE combo_code =".db_escape($stock_id)."
	";

	$result = db_query($sql, "could not retreive the location name for $stock_id");


	$row = db_fetch_row($result);
	return $row[0];

}

