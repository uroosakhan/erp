<?php


function add_stock_adjustment($items, $location, $date_, $reference, $memo_,$batch,$adj_type,$units_id,$con_factor,$fixed_asset)
{
	global $SysPrefs, $path_to_root, $Refs;

	begin_transaction();
	$args = func_get_args();
	$args = (object)array_combine(array('items', 'location', 'date_', 'reference', 'memo_'), $args);
	$args->trans_no = 0;
if($fixed_asset)
    $trans_type = ST_FAADJUST;
else
    $trans_type = ST_INVADJUST;

	hook_db_prewrite($args, $trans_type);

	$adj_id = get_next_trans_no($trans_type);
	


	if ($SysPrefs->loc_notification() == 1)
	{
		include_once($path_to_root . "/inventory/includes/inventory_db.inc");
		$st_ids = array();
		$st_names = array();
		$st_num = array();
		$st_reorder = array();
	}

	foreach ($items as $line_item)
	{
//---------convertion factor----------------
		$item=get_item($line_item->stock_id);
		$pref=get_company_prefs();
		$con_factor = get_company_item_pref('con_factor');
		if($pref['alt_uom'] == 1  ) {
			if ($line_item->units_id != $item['units']) {
				if ($item['con_type'] == 0) {
					if($con_factor['inventory_enable'] ==0){
						$qty = $line_item->quantity / $item['con_factor'];
						$line_item->con_factor = $item['con_factor'];
					}
				
					$qty = $line_item->quantity / $line_item->con_factor;
				} else {
					$qty = $line_item->con_factor / $line_item->quantity;
				}
			} else {
				$qty = $line_item->quantity;

			}
		}
		else{
			$qty = $line_item->quantity;
			$line_item->units_id = $item['units'];

		}
	

		if ($SysPrefs->loc_notification() == 1 && $line_item->qty < 0)
		{
			$chg = $line; $chg->qty = -$chg->qty;	// calculate_reorder_level expect positive qty
			$loc = calculate_reorder_level($location, $line_item, $st_ids, $st_names, $st_num, $st_reorder); 
		}
	if($adj_type == 1){
        $quantity = -$qty;
      //  $line_item->amount3 = -$line_item->amount3;
    }

	else
        $quantity = $qty;
		
		$batch_info =get_batch_by_name($line_item->batch);


		if($adj_type == 0 && $line_item->batch != '' && ($line_item->batch != $batch_info['name']))
		{

			add_batch($line_item->batch,$line_item->exp_date);
			$batch=get_batch_by_name($line_item->batch);
			$last_inserted_id=db_insert_id();
			add_stock_adjustment_item($adj_id, $line_item->stock_id, $location, $date_, $reference,
				$quantity, $line_item->standard_cost, $memo_,$line_item->price,$line_item->person_id,$line_item->discount_percent, $line_item->text1, $line_item->text2, $line_item->text3, $line_item->text4, $line_item->text5, $line_item->text6
				,$line_item->amount1,$line_item->amount2,$line_item->amount3,$line_item->amount4,$line_item->amount5,$line_item->amount6
				,$line_item->date1,$line_item->date2,$line_item->date3
				,$line_item->combo1,$line_item->combo2,$line_item->combo3,$line_item->combo4,$line_item->combo5,$line_item->combo6,$batch['id'],$line_item->units_id,$line_item->con_factor,$line_item->AccountCode,$line_item->dimension_id);

		}

        elseif($adj_type == 0 && $line_item->batch != '' && ($line_item->batch == $batch_info['name']))
        {

            $batch=get_batch_by_name($line_item->batch);
            $last_inserted_id = $line_item->batch;
            add_stock_adjustment_item($adj_id, $line_item->stock_id, $location, $date_, $reference,
                $quantity, $line_item->standard_cost, $memo_,$line_item->price,$line_item->person_id,$line_item->discount_percent, $line_item->text1, $line_item->text2, $line_item->text3, $line_item->text4, $line_item->text5, $line_item->text6
                ,$line_item->amount1,$line_item->amount2,$line_item->amount3,$line_item->amount4,$line_item->amount5,$line_item->amount6
                ,$line_item->date1,$line_item->date2,$line_item->date3
                ,$line_item->combo1,$line_item->combo2,$line_item->combo3,$line_item->combo4,$line_item->combo5,$line_item->combo6,$batch['id'],$line_item->units_id,$line_item->con_factor,$line_item->AccountCode,$line_item->dimension_id);

        }


		else
		{

		$batch=get_batch_by_name($line_item->batch);
			$last_inserted_id = $line_item->batch;
			add_stock_adjustment_item($adj_id, $line_item->stock_id, $location, $date_, $reference,
				$quantity, $line_item->standard_cost, $memo_,$line_item->price,$line_item->person_id,$line_item->discount_percent, $line_item->text1, $line_item->text2, $line_item->text3, $line_item->text4, $line_item->text5, $line_item->text6
				,$line_item->amount1,$line_item->amount2,$line_item->amount3,$line_item->amount4,$line_item->amount5,$line_item->amount6
				,$line_item->date1,$line_item->date2,$line_item->date3
				,$line_item->combo1,$line_item->combo2,$line_item->combo3,$line_item->combo4,$line_item->combo5,$line_item->combo6,$line_item->batch,$line_item->units_id,$line_item->con_factor,$line_item->AccountCode,$line_item->dimension_id);


		}


	}

	add_comments($trans_type, $adj_id, $date_, $memo_);

	$Refs->save($trans_type, $adj_id, $reference);
	add_audit_trail($trans_type, $adj_id, $date_);

	$args->trans_no = $adj_id;
	hook_db_postwrite($args, $trans_type);
	commit_transaction();
	if ($SysPrefs->loc_notification() == 1 && count($st_ids) > 0)
		send_reorder_email($loc, $st_ids, $st_names, $st_num, $st_reorder);

	return $adj_id;
}

//-------------------------------------------------------------------------------------------------------------

function void_stock_adjustment($type_no,$type)
{
    if($type){
        hook_db_prevoid($type, $type_no);
        void_gl_trans($type, $type_no);
        void_stock_move($type, $type_no);
    }
else{
    hook_db_prevoid(ST_INVADJUST, $type_no);
    void_gl_trans(ST_INVADJUST, $type_no);
    void_stock_move(ST_INVADJUST, $type_no);
}
}

//-------------------------------------------------------------------------------------------------------------

function 	get_stock_adjustment_items($trans_no,$type)
{
    if($type) {
        $result = get_stock_moves($type, $trans_no);
    }
    else{
        $result = get_stock_moves(ST_INVADJUST, $trans_no);
    }

	if (db_num_rows($result) == 0)
	{
		return null;
	}

	return $result;
}

//--------------------------------------------------------------------------------------------------

function add_stock_adjustment_item($adj_id, $stock_id, $location, $date_, $reference,
	$quantity, $standard_cost, $memo_,$price,$person_id,$discount_percent,$text1, $text2, $text3, $text4, $text5, $text6,
								   $amount1, $amount2, $amount3, $amount4,$amount5, $amount6,
								   $date1, $date2, $date3,
								   $combo1, $combo2, $combo3,$combo4, $combo5, $comb6,$batch,$units_id,$con_factor,$AccountCode,$dimension_id)
{

	$mb_flag = get_mb_flag($stock_id);

    if (is_service($mb_flag))
    {
    	display_db_error("Cannot do inventory adjustment for Service item : $stock_id", "");
    }

update_average_material_cost(null, $stock_id, $standard_cost, $quantity, $date_);

//	if (is_fixed_asset($mb_flag)) {
//		$sql = "UPDATE ".TB_PREF."stock_master SET inactive=1
//			WHERE stock_id=".db_escape($stock_id);
//		db_query($sql,"The inactive flag for the fixed asset could not be updated");
//	}
    if (is_fixed_asset($mb_flag)) {
        add_stock_move(ST_FAADJUST, $stock_id, $adj_id, $location,
            $date_, $reference, $quantity, $standard_cost, $price, $person_id, $discount_percent, $text1, $text2, $text3, $text4, $text5, $text6,
            $amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
            $date1, $date2, $date3,
            $combo1, $combo2, $combo3, $combo4, $combo5, $comb6, $units_id, $con_factor, $batch);
    }
    else{
        add_stock_move(ST_INVADJUST, $stock_id, $adj_id, $location,
            $date_, $reference, $quantity, $standard_cost, $price, $person_id, $discount_percent, $text1, $text2, $text3, $text4, $text5, $text6,
            $amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
            $date1, $date2, $date3,
            $combo1, $combo2, $combo3, $combo4, $combo5, $comb6, $units_id, $con_factor, $batch);
    }

	$inv_value = $standard_cost * $quantity;
	$adj_value = $standard_cost * -($quantity);

	if (is_fixed_asset($mb_flag)) {
		// get the initial value of the fixed assset.
		$row = get_fixed_asset_move($stock_id, ST_SUPPRECEIVE);
		$inv_value = $row['price'] * $quantity;
		$adj_value = (-($row['price']) + $standard_cost) * $quantity;
	}

    if ($standard_cost > 0 && is_fixed_asset($mb_flag))
    {
        $stock_gl_codes = get_stock_gl_code($stock_id);
        $item = get_item($stock_id);

        add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_,
            /*$stock_gl_codes['adjustment_account']*/ $item['inventory_account'], $stock_gl_codes['dimension_id'], $stock_gl_codes['dimension2_id'], $memo_, $adj_value  );

        add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_, $item['inventory_account'], $dimension_id, 0, $memo_, $inv_value);
    }
    if ($standard_cost < 0 && is_fixed_asset($mb_flag))
    {
        $stock_gl_codes = get_stock_gl_code($stock_id);
        $item = get_item($stock_id);

        add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_,
            /*$stock_gl_codes['adjustment_account']*/ $AccountCode, $stock_gl_codes['dimension_id'], $stock_gl_codes['dimension2_id'], $memo_, $adj_value  );

        add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_, $item['inventory_account'], $dimension_id, 0, $memo_, $inv_value);
    }
    elseif ($standard_cost > 0 && !is_fixed_asset($mb_flag))
    {
            $stock_gl_codes = get_stock_gl_code($stock_id);

            add_gl_trans_std_cost(ST_INVADJUST, $adj_id, $date_,
                /*$stock_gl_codes['adjustment_account']*/$AccountCode, $dimension_id, $stock_gl_codes['dimension2_id'], $memo_, $adj_value  );

            add_gl_trans_std_cost(ST_INVADJUST, $adj_id, $date_, $stock_gl_codes['inventory_account'], 0, 0, $memo_, $inv_value);

    }



	if (is_fixed_asset($mb_flag) && $standard_cost > 0) {
		// Additional gl entry for fixed asset.
		$grn_act = get_company_pref('default_loss_on_asset_disposal_act');
		$item = get_item($stock_id);
		add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_, $AccountCode, 0, 0, $memo_, ($standard_cost * -($quantity)));

	}

    if (is_fixed_asset($mb_flag) && $standard_cost < 0) {
        // Additional gl entry for fixed asset.
        $grn_act = get_company_pref('default_loss_on_asset_disposal_act');
        $item = get_item($stock_id);
        add_gl_trans_std_cost(ST_FAADJUST, $adj_id, $date_, $item['inventory_account'], 0, 0, $memo_, ($standard_cost * -($quantity)));

    }

//    die;
}

