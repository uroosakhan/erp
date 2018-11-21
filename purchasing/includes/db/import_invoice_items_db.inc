<?php
//-------------------------------------------------------------------------------------------------------------
function get_material_cost($stock_id)
{
	$sql = "SELECT standard_cost  FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($stock_id)
	;
	$sql .= "  ORDER BY standard_cost DESC LIMIT 1";
	$result = db_query($sql, "could not get ");
	$row = db_fetch_row($result);

	return $row[0];
}
function add_supp_invoice_item_import($supp_trans_type, $supp_trans_no, $stock_id, $description,
	$gl_code, $unit_price, $unit_tax, $quantity, $grn_item_id, $po_detail_item_id, $memo_,
	$err_msg="", $Unit_Amt, $Gross_Amt, $As_Per_B_E,
	$Landing, $Landing_Amt, $Value_invl_Landing,
	$INS, $INS_Amt, $Value_Incl_INC, $F_E_D,
	$F_E_D_Amt, $Duty, $Duty_Amt,
	$Value_And_Duty, $Value_Excl_S_T, $S_T,
	$S_T_Amt, $Amount_Incl_S_T, $I_Tax, $I_Tax_Amt,
	$Add_S_T, $Add_S_T_Amt, $Total_Charges, $Other_Expense,
	$Net_Amount, $Job_Name, $Total_Other_Expenses, $Gross_Amt_new,$po_exchange_rate,$tot_import_expenses)
{

	$qoh = get_qoh_on_date($stock_id);
	
	
	if($qoh != $quantity)
	{
	$qty_hand =$qoh-$quantity;
//	$qty_hand = $qoh;
//	}
//	else{
//	$qty_hand =$qoh-$quantity;
	
	}
	
$mat =get_material_cost($stock_id);
	
	$sql = "INSERT INTO ".TB_PREF."supp_invoice_items(supp_trans_type, supp_trans_no, stock_id, description, gl_code, unit_price, unit_tax, quantity,  	grn_item_id, po_detail_item_id, memo_, Unit_Amt, Gross_Amt, As_Per_B_E,	Landing, Landing_Amt, Value_invl_Landing,
	INS, INS_Amt, Value_Incl_INC, F_E_D,
	F_E_D_Amt, Duty, Duty_Amt,	Value_And_Duty, Value_Excl_S_T, S_T,
	S_T_Amt, Amount_Incl_S_T, I_Tax, I_Tax_Amt,
	Add_S_T, Add_S_T_Amt, Total_Charges, Other_Expense,
	Net_Amount, Job_Name, Total_Other_Expenses, Gross_Amt_new,cur_qoh,prev_qoh,prev_sc,landed_cost_sum,po_exchange_rate,tot_import_expenses) ";
	
	$sql .= " VALUES (".db_escape($supp_trans_type).", ".db_escape($supp_trans_no).", 
		" .db_escape($stock_id). ", ".db_escape($description).", ".db_escape($gl_code).", 
		".db_escape($unit_price) .", ".db_escape($unit_tax).", ".db_escape($quantity).",
		".db_escape($grn_item_id).", ".db_escape($po_detail_item_id).", ".db_escape($memo_).",
		".db_escape($Unit_Amt).", ".db_escape($Gross_Amt).", ".db_escape($As_Per_B_E).",
		".db_escape($Landing).", ".db_escape($Landing_Amt).", ".db_escape($Value_invl_Landing).",
		".db_escape($INS).", ".db_escape($INS_Amt).", ".db_escape($Value_Incl_INC).",
		".db_escape($F_E_D).", ".db_escape($F_E_D_Amt).", ".db_escape($Duty).",
		".db_escape($Duty_Amt).", ".db_escape($Value_And_Duty).", ".db_escape($Value_Excl_S_T).",
		".db_escape($S_T).", ".db_escape($S_T_Amt).", ".db_escape($Amount_Incl_S_T).",
		".db_escape($I_Tax).", ".db_escape($I_Tax_Amt).", ".db_escape($Add_S_T).",
		".db_escape($Add_S_T_Amt).", ".db_escape($Total_Charges).", ".db_escape($Other_Expense).",
		".db_escape($Net_Amount).", ".db_escape($Job_Name).", ".db_escape($Total_Other_Expenses) .", 
		".db_escape($Gross_Amt_new).", 
		".db_escape($qoh).", 
		".db_escape($qty_hand).", 
		".db_escape($mat).",
		".db_escape($_SESSION["supp_trans"]->sum).",
		".db_escape($po_exchange_rate).",
		".db_escape($tot_import_expenses)."
		)";

	if ($err_msg == "")
		$err_msg = "Cannot insert a supplier transaction detail record";

	db_query($sql, $err_msg);
	
	return db_insert_id();
}


//-------------------------------------------------------------------------------------------------------------

function add_supp_invoice_gl_item_import($supp_trans_type, $supp_trans_no, $gl_code, $amount, $memo_, $err_msg="")
{
	return add_supp_invoice_item($supp_trans_type, $supp_trans_no,  "", "", $gl_code, $amount,
		0, 0, /*$grn_item_id*/0, /*$po_detail_item_id*/0, $memo_, $err_msg);
}


//----------------------------------------------------------------------------------------

function get_supp_invoice_items_import($supp_trans_type, $supp_trans_no)
{
	$sql = "SELECT *, unit_price AS FullUnitPrice FROM ".TB_PREF."supp_invoice_items
		WHERE supp_trans_type = ".db_escape($supp_trans_type)."
		AND supp_trans_no = ".db_escape($supp_trans_no)." ORDER BY id";
	return db_query($sql, "Cannot retreive supplier transaction detail records");
}

//----------------------------------------------------------------------------------------

function void_supp_invoice_items_import($type, $type_no)
{
	$sql = "UPDATE ".TB_PREF."supp_invoice_items SET quantity=0, unit_price=0
		WHERE supp_trans_type = ".db_escape($type)." AND supp_trans_no=".db_escape($type_no);
	db_query($sql, "could not void supptrans details");
}

?>