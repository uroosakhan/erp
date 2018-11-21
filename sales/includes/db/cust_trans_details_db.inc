<?php

//----------------------------------------------------------------------------------------

function get_customer_trans_details($debtor_trans_type, $debtor_trans_no)
{
if (!is_array($debtor_trans_no))
	$debtor_trans_no = array( 0=>$debtor_trans_no );

	$sql = "SELECT line.*,item.category_id,item.text3,item.text4, item.amount2 AS Width,
		item.amount5 AS Length, line.amount6 AS Thickness,
		line.unit_price+line.unit_tax AS FullUnitPrice,
		line.description As StockDescription,
		line.stock_id, line.quantity,	line.text1,
		item.units, item.mb_flag, item.carton,item.text2 as part_no
		FROM "
			.TB_PREF."debtor_trans_details line,"
			.TB_PREF."stock_master item
		WHERE (";

	$tr=array();
	foreach ($debtor_trans_no as $trans_no)
		$tr[] = 'debtor_trans_no='.db_escape($trans_no);

	$sql .= implode(' OR ', $tr);


	$sql.=	") AND debtor_trans_type=".db_escape($debtor_trans_type)."
		AND item.stock_id=line.stock_id
		ORDER BY id";
	return db_query($sql, "The debtor transaction detail could not be queried");
}

//----------------------------------------------------------------------------------------
function get_customer_trans_details_for_invoice($debtor_trans_type, $debtor_trans_no)
{
	if (!is_array($debtor_trans_no))
		$debtor_trans_no = array( 0=>$debtor_trans_no );

	$sql = "SELECT ".TB_PREF."debtor_trans_details.*,
		".TB_PREF."debtor_trans_details.unit_price+".TB_PREF."debtor_trans_details.unit_tax AS FullUnitPrice,
		SUM(".TB_PREF."debtor_trans_details.quantity) As quantity_,
		".TB_PREF."debtor_trans_details.description As StockDescription,
		".TB_PREF."stock_master.units, ".TB_PREF."stock_master.carton, ".TB_PREF."stock_master.mb_flag
		FROM ".TB_PREF."debtor_trans_details, ".TB_PREF."stock_master
		WHERE (";

	$tr=array();
	foreach ($debtor_trans_no as $trans_no)
		$tr[] = 'debtor_trans_no='.db_escape($trans_no);

	$sql .= implode(' OR ', $tr);


	$sql.=	") AND debtor_trans_type=".db_escape($debtor_trans_type)."
		AND ".TB_PREF."stock_master.stock_id=".TB_PREF."debtor_trans_details.stock_id
		GROUP BY stock_id
		ORDER BY id";
	return db_query($sql, "The debtor transaction detail could not be queried");
}

//----------------------------------------------------------------------------------------

function void_customer_trans_details($type, $type_no)
{
	$sql = "UPDATE ".TB_PREF."debtor_trans_details SET quantity=0, unit_price=0,
		unit_tax=0, discount_percent=0, standard_cost=0, src_id=0
		WHERE debtor_trans_no=".db_escape($type_no)."
		AND debtor_trans_type=".db_escape($type);

	db_query($sql, "The debtor transaction details could not be voided");

	// clear the stock move items
	void_stock_move($type, $type_no);
}
//----------------------------------------------------------------------------------------

function write_customer_trans_detail_item($debtor_trans_type, $debtor_trans_no, $stock_id, $description,
	$quantity, $unit_price, $unit_tax, $discount_percent, $std_cost, $src_id, $line_id=0,$units_id,
	  $con_factor, $text1, $text2, $text3, $text4, $text5, $text6,
  $amount1, $amount2, $amount3, $amount4, $amount5, $amount6,
  $date1, $date2, $date3,
  $combo1, $combo2, $combo3,$combo4, $combo5, $combo6,$batch,$item_location, $text7,$bonus)
{


	if ($line_id!=0)
		$sql = "UPDATE ".TB_PREF."debtor_trans_details SET
			stock_id=".db_escape($stock_id).",
			description=".db_escape($description).",
			quantity=".db_escape($quantity).",
			unit_price=".db_escape($unit_price).",
			unit_tax=".db_escape($unit_tax).",
			discount_percent=".db_escape($discount_percent).",
			standard_cost=".db_escape($std_cost).",
			src_id=".db_escape($src_id).",
			units_id=".db_escape($units_id).",
			con_factor=".db_escape($con_factor).",
			text1=".db_escape($text1).",
			text2=".db_escape($text2).",
			text3=".db_escape($text3).",
			text4=".db_escape($text4).",
			text5=".db_escape($text5).",
			text6=".db_escape($text6).",
			amount1=".db_escape($amount1).",
			amount2=".db_escape($amount2).",
			amount3=".db_escape($amount3).",
			amount4=".db_escape($amount4).",
			amount5=".db_escape($amount5).",
			amount6=".db_escape($amount6).",
			date1=".db_escape(date2sql($amount2)).",
			date2=".db_escape(date2sql($amount3)).",
			date3=".db_escape(date2sql($amount4)).",
			combo1=".db_escape($combo1).",
			combo2=".db_escape($combo2).",
			combo3=".db_escape($combo3).",
			combo4=".db_escape($combo4).",
			combo5=".db_escape($combo5).",
			combo6=".db_escape($combo6).",
			batch=".db_escape($batch).",
			item_location=".db_escape($item_location).",
			text7=".db_escape($text7).",
			bonus=".db_escape($bonus)."
			WHERE
			id=".db_escape($line_id);
	else
			$sql = "INSERT INTO ".TB_PREF."debtor_trans_details (debtor_trans_no,
				debtor_trans_type, stock_id, description, quantity, unit_price,
				unit_tax, discount_percent, standard_cost, src_id,units_id,con_factor,
				text1, text2, text3, text4,text5, text6,
				amount1, amount2, amount3, amount4,amount5, amount6,
				date1, date2, date3,
				combo1, combo2, combo3,combo4, combo5, combo6,batch,item_location, text7,bonus)
			VALUES (".db_escape($debtor_trans_no).", ".db_escape($debtor_trans_type).", ".db_escape($stock_id).
			", ".db_escape($description).",
				".db_escape($quantity).", ".db_escape($unit_price).",
				".db_escape($unit_tax)." , 
			".db_escape($discount_percent).",
			".db_escape($std_cost)." ,
			".db_escape($src_id).",
				".db_escape($units_id).",
				".db_escape($con_factor).",
				".db_escape($text1).",
				".db_escape($text2).",
				".db_escape($text3).",
				".db_escape($text4).",
				".db_escape($text5).",
				".db_escape($text6).",
				".db_escape($amount1).",
				".db_escape($amount2).",
				".db_escape($amount3).",
				".db_escape($amount4).",
				".db_escape($amount5).",
				".db_escape($amount6).",
				".db_escape(date2sql($date1)).",
				".db_escape(date2sql($date2)).",
				".db_escape(date2sql($date3)).",
				".db_escape($combo1).",
				".db_escape($combo2).",
				".db_escape($combo3).",
				".db_escape($combo4).",
				".db_escape($combo5).",
				".db_escape($combo6).",
				".db_escape($batch).",
				".db_escape($item_location).",
				".db_escape($text7).",
				".db_escape($bonus).")";

	db_query($sql, "The debtor transaction detail could not be written");
}
