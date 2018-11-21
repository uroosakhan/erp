<?php
//-----------------------------------------------------------------------------
//	Add or update Sales Invoice
//

function add_stock_import_adjustment_item($adj_id, $item, $location, $date_, $type, $reference,
                                   $quantity, $standard_cost, $memo_, $job_card=0, $gl_account_code=ALL_TEXT, $custom_ref=ALL_TEXT, $increase) 
{
    $mb_flag = get_mb_flag($item['stock_id']);

    if (is_service($mb_flag))
    {
        display_db_error("Cannot do inventory adjustment for Service item : ".$item['stock_id']."++".$item['mb_flag'], "");
    }

    update_average_material_cost(null, $item['stock_id'], $standard_cost, $quantity, $date_, false, $location);

    add_stock_move(ST_INVADJUST, $item['stock_id'], $adj_id,
        $location, $date_, $reference,
        $quantity, $standard_cost, 0,
        0, 0,'','','','',
        '','',0,0,0,0,0,
        0,0,0,0,0,0,0,0,
        0,0,$item['units'],0,0, $location);
    if ($standard_cost > 0)
    {
        $stock_gl_codes = get_stock_gl_code($item['stock_id']);
        add_gl_trans_std_cost(ST_INVADJUST, $adj_id, $date_,
            $gl_account_code == ALL_TEXT ? $stock_gl_codes['adjustment_account'] :  $gl_account_code,
            $stock_gl_codes['dimension_id'], $stock_gl_codes['dimension2_id'], $memo_, ($standard_cost * -($quantity)));
        add_gl_trans_std_cost(ST_INVADJUST, $adj_id, $date_,
            $stock_gl_codes['inventory_account'], 0, 0,
            $memo_, ($standard_cost * $quantity));
    }
}

//-------------------------------------------------------------------------------------------------------------


function get_stock_master($stock_id)
{
	$sql = "SELECT *
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($stock_id);
	return db_query($sql, "The standard cost cannot be retrieved");

}

function get_stock_master_info1($stock_id)
{
	$sql = "SELECT *
			FROM ".TB_PREF."stock_master
			WHERE stock_id=".db_escape($stock_id);
	$result = db_query($sql, "The standard cost cannot be retrieved");
	return db_fetch($result);
}


?>