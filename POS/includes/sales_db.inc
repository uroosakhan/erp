<?php
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/db/inventory_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_order_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_credit_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_invoice_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_delivery_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_types_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_points_db.inc");
include_once($path_to_root . "/POS/includes/db/sales_groups_db.inc");
include_once($path_to_root . "/POS/includes/db/recurrent_invoices_db.inc");
include_once($path_to_root . "/POS/includes/db/custalloc_db.inc");
include_once($path_to_root . "/POS/includes/db/cust_trans_db.inc");
include_once($path_to_root . "/POS/includes/db/cust_trans_details_db.inc");
include_once($path_to_root . "/POS/includes/db/payment_db.inc");
include_once($path_to_root . "/POS/includes/db/branches_db.inc");
include_once($path_to_root . "/POS/includes/db/customers_db.inc");
include_once($path_to_root . "/POS/includes/db/import_db.inc");
//----------------------------------------------------------------------------------------
// $price in customer's currency
// $quantity is used as is (if it's neg it's neg, if it's pos it's pos)
// $std_cost is in home currency
// $show_or_hide 1 show this item in invoice/credit views, 0 to hide it (used for write-off items)
// $type is 10 (invoice) or 11 (credit)

function add_stock_move_customer($type, $stock_id, $trans_id, $location, $date_, $reference,
	$quantity, $std_cost, $show_or_hide=1, $price=0, $discount_percent=0)
{
	return add_stock_move($type, $stock_id, $trans_id, $location, $date_, $reference,
		$quantity, $std_cost,  $price,0, $discount_percent,
		"The customer stock movement record cannot be inserted");
}

//----------------------------------------------------------------------------------------
// add a debtor-related gl transaction
// $date_ is display date (non-sql)
// $amount is in CUSTOMER'S currency

function add_gl_trans_customer_pos($type, $type_no, $date_, $account, $dimension, $dimension2,
	$amount, $customer_id, $err_msg="", $rate=0)
{
	if ($err_msg == "")
		$err_msg = "The customer GL transaction could not be inserted";

	return add_gl_trans($type, $type_no, $date_, $account, $dimension, $dimension2, "", $amount,
		get_customer_currency($customer_id),
		PT_CUSTOMER, $customer_id, $err_msg, $rate);
}

//----------------------------------------------------------------------------------------

function get_calculated_price_pos($stock_id, $add_pct)
{
	$avg = get_standard_cost($stock_id);
	if ($avg == 0)
		return 0;
	return round2($avg * (1 + $add_pct / 100), user_price_dec());
}

function round_to_nearest_pos($price, $round_to)
{
	if ($price == 0)
		return 0;
	$pow = pow(10, user_price_dec());
	if ($pow >= $round_to)
		$mod = ($pow % $round_to);
	else
		$mod = ($round_to % $pow);
	if ($mod != 0)
		$price = ceil($price) - ($pow - $round_to) / $pow;
	else	
    	$price = ceil($price * ($pow / $round_to)) / ($pow / $round_to);
    return $price;

}

function get_price_pos ($stock_id, $currency, $sales_type_id, $factor=null, $date=null)
{
	if ($date == null)
	    $date = new_doc_date();

	if ($factor === null) 
	{
		$myrow = get_sales_type_pos($sales_type_id);
		$factor = $myrow['factor'];
	}

	$add_pct = get_company_pref('add_pct');
	$base_id = get_base_sales_type();
    $home_curr = get_company_currency();
	//	AND (sales_type_id = $sales_type_id	OR sales_type_id = $base_id)
	$sql = "SELECT price, curr_abrev, sales_type_id
		FROM ".TB_PREF."prices
		WHERE stock_id = ".db_escape($stock_id)."
			AND (curr_abrev = ".db_escape($currency)." OR curr_abrev = ".db_escape($home_curr).")";

	$result = db_query($sql, "There was a problem retrieving the pricing information for the part $stock_id for customer");
	$num_rows = db_num_rows($result);
	$rate = round2(get_exchange_rate_from_home_currency($currency, $date),
	    user_exrate_dec());
	$round_to = get_company_pref('round_to');
	$prices = array();
	while($myrow = db_fetch($result)) 
	{
	    $prices[$myrow['sales_type_id']][$myrow['curr_abrev']] = $myrow['price'];
	}
	$price = false;
	if (isset($prices[$sales_type_id][$currency])) 
	{
	    $price = $prices[$sales_type_id][$currency];
	}
	elseif (isset($prices[$base_id][$currency])) 
	{
	    $price = $prices[$base_id][$currency] * $factor;
	}
	elseif (isset($prices[$sales_type_id][$home_curr])) 
	{
	    $price = $prices[$sales_type_id][$home_curr] / $rate;
	}
	elseif (isset($prices[$base_id][$home_curr])) 
	{
	    $price = $prices[$base_id][$home_curr] * $factor / $rate;
	}
/*
	if (isset($prices[$sales_type_id][$home_curr])) 
	{
	    $price = $prices[$sales_type_id][$home_curr] / $rate;
	}
	elseif (isset($prices[$base_id][$currency])) 
	{
	    $price = $prices[$base_id][$currency] * $factor;
	}
	elseif (isset($prices[$base_id][$home_curr])) 
	{
	    $price = $prices[$base_id][$home_curr] * $factor / $rate;
	}
*/	
	elseif ($num_rows == 0 && $add_pct != -1)
	{
		$price = get_calculated_price($stock_id, $add_pct);
		if ($currency != $home_curr)
			$price /= $rate;
		if ($factor != 0)
			$price *= $factor;
	}	
	if ($price === false)
		return 0;
	elseif ($round_to != 1)	
		return round_to_nearest($price, $round_to);
	else
		return round2($price, user_price_dec());
}
//----------------------------------------------------------------------------------------
//
//	Get price for given item or kit.
//  When $std==true price is calculated as a sum of all included stock items,
//	otherwise all prices set for kits and items are accepted.
//
function get_kit_price_pos($item_code, $currency, $sales_type_id, $factor=null,
	$date=null, $std = false)
{
	$kit_price = 0.00;
	if (!$std) {
		$kit_price = get_price( $item_code, $currency, $sales_type_id, 
			$factor, $date);

		if ($kit_price !== 0) {
			return $kit_price;
		}
	}	
	// no price for kit found, get total value of all items
	$kit = get_item_kit($item_code);
	
	while($item = db_fetch($kit)) {
		if ($item['item_code'] != $item['stock_id']) {
			// foreign/kit code
			$kit_price += $item['quantity'] * get_kit_price( $item['stock_id'],
				$currency, $sales_type_id, $factor, $date, $std);

		} else {
			// stock item
			$kit_price += $item['quantity'] * get_price( $item['stock_id'], 
				$currency, $sales_type_id, $factor, $date);
		}
	}
	return $kit_price;
}

//--------------------------------------------------------------------------------------------------
function update_parent_line_pos($doc_type, $line_id, $qty_dispatched, $auto=false)
{
	$doc_type = get_parent_type($doc_type);

        $qty_dispatched = (float)$qty_dispatched;

//	echo "update line: $line_id, $doc_type, $qty_dispatched";
	if ($doc_type == 0)
		return false;
	else {
		if ($doc_type==ST_SALESORDER || $doc_type==ST_SALESQUOTE)
		{
			$sql = "UPDATE ".TB_PREF."sales_order_details
				SET qty_sent = qty_sent + $qty_dispatched";
			if ($auto)
				$sql .= ", quantity = quantity + $qty_dispatched";
			$sql .= " WHERE id=".db_escape($line_id);
		}
		else
			$sql = "UPDATE ".TB_PREF."debtor_trans_details
				SET qty_done = qty_done + $qty_dispatched
				WHERE id=".db_escape($line_id);
	}
	db_query($sql, "The parent document detail record could not be updated");
	return true;
}

//--------------------------------------------------------------------------------------------------
// find inventory location for given transaction
//
function get_location_pos(&$cart)
{
	$sql = "SELECT ".TB_PREF."locations.* FROM ".TB_PREF."stock_moves,"
		.TB_PREF."locations".
		" WHERE type=".db_escape($cart->trans_type).
		" AND trans_no=".key($cart->trans_no).
		" AND qty!=0 ".
		" AND ".TB_PREF."locations.loc_code=".TB_PREF."stock_moves.loc_code";
	$result = db_query($sql, 'Retreiving inventory location');


	if (db_num_rows($result)) {
		return db_fetch($result);
	}
	return null;
}
//--------------------------------------------------------------------------------------------------
// Generic read debtor transaction into cart
//
//	$trans_no - array of trans nums; special case trans_no==0 - new doc
//
function read_sales_trans_pos($doc_type, $trans_no, &$cart)
{
	if (!is_array($trans_no) && $trans_no)
			$trans_no = array($trans_no);

	$cart->trans_type = $doc_type;
	if (!$trans_no) { // new document
		$cart->trans_no = $trans_no;
	} else {
		// read header data from first document
		$myrow = get_customer_trans_pos($trans_no[0],$doc_type);
		if (count($trans_no)>1)
			$cart->trans_no = get_customer_trans_version_pos($doc_type, $trans_no);
		else
			$cart->trans_no = array($trans_no[0]=>$myrow["version"]);

		$cart->set_sales_type($myrow["tpe"], $myrow["sales_type"], $myrow["tax_included"],0);

		$cart->set_customer($myrow["debtor_no"], $myrow["DebtorName"],
			$myrow["curr_code"], $myrow["discount"], $myrow["payment_terms"]);

		$cart->set_branch($myrow["branch_code"], $myrow["tax_group_id"],
			$myrow["tax_group_name"]);

		$cart->reference = $myrow["reference"];
		$cart->order_no = $myrow["order_"];
		$cart->due_date = sql2date($myrow["due_date"]);
		$cart->document_date = sql2date($myrow["tran_date"]);
		$cart->dimension_id = $myrow['dimension_id']; // added 2.1 Joe Hunt 2008-11-12
		$cart->dimension2_id = $myrow['dimension2_id'];
//		$cart->total_discount = $myrow['total_discount'];
		$cart->total_discount = $myrow['discount1'];
		$cart->total_discount_pos = $myrow['discount2'];
		$cart->ToBankAccount = $myrow['text_3'];
		$cart->total_discount_pos1 = $myrow['text_1'];
		$cart->CashGst = $myrow['text_2'];
		$cart->Comments = '';
		foreach ( $trans_no as $trans ) {
			$cart->Comments .= get_comments_string($doc_type,$trans);
		}

		// FIX this should be calculated sum() for multiply parents

		$cart->set_delivery($myrow["ship_via"], $myrow["br_name"],
		$myrow["br_address"], $myrow["ov_freight"]);

		$location = 0;
		$myrow = get_location($cart); // find location from movement

		if($myrow!=null) {
			$cart->set_location($myrow['loc_code'], $myrow['location_name']);
		}

		$result = get_customer_trans_details_pos($doc_type,$trans_no);
		if (db_num_rows($result) > 0) {
			for($line_no=0; $myrow = db_fetch($result); $line_no++)	{
				$cart->line_items[$line_no] = new line_details(
                $myrow["stock_id"],$myrow["quantity"],
                $myrow["unit_price"], $myrow["discount_percent"],
                $myrow["qty_done"], $myrow["standard_cost"],
                $myrow["StockDescription"],$myrow["id"], $myrow["debtor_trans_no"],
                @$myrow["src_id"],0,$myrow["stk_id"],$myrow["text1"]);
			}
		}
	} // !newdoc

	return true;
}
//----------------------------------------------------------------------------------------

function get_sales_child_lines_pos($trans_type, $trans_no, $lines=true)
{
	if (!($ctype = get_child_type($trans_type)))
		return false;
	if (!is_array($trans_no)) {
		$trans_no = array($trans_no);
	}

	$par_tbl = $trans_type == ST_SALESORDER ? "sales_order_details" : "debtor_trans_details";
	$par_no = $trans_type == ST_SALESORDER ? "trans.order_no" : "trans.debtor_trans_no";

	foreach($trans_no as $n => $trans) {
		$trans_no[$n] = db_escape($trans);
	}
	$sql = "SELECT child.*
			FROM
				".TB_PREF."debtor_trans_details child
			LEFT JOIN ".TB_PREF."$par_tbl trans 
				ON child.src_id=trans.id AND child.debtor_trans_type=$ctype
			WHERE $par_no IN(". implode(',', array_values($trans_no)).")";

	if (!$lines)
		$sql .= " GROUP BY child.debtor_trans_no";

	$sql .= " ORDER BY child.debtor_trans_no";

	return db_query($sql, "can't retrieve child trans");
}

function get_sales_child_numbers_pos($trans_type, $trans_no)
{
	$trans = array();
	$res = get_sales_child_lines($trans_type, $trans_no, false);
	while ($line = db_fetch($res)) {
		$trans[] = $line['debtor_trans_no'];
	}
	return $trans;
}

function get_sales_parent_lines_pos($trans_type, $trans_no, $lines=true)
{
	$partype = get_parent_type($trans_type);

	if (!$partype)
		return false;

	$par_tbl = $partype == ST_SALESORDER ? "sales_order_details" : "debtor_trans_details";
	$par_no = $partype == ST_SALESORDER ? "parent.order_no" : "parent.debtor_trans_no";
	$sql = "SELECT parent.*
			FROM
				".TB_PREF."$par_tbl parent
			LEFT JOIN ".TB_PREF."debtor_trans_details trans 
				ON trans.src_id=parent.id
			WHERE
				trans.debtor_trans_type=".db_escape($trans_type)
				." AND trans.debtor_trans_no=".db_escape($trans_no);
	if (!$lines)
		$sql .= " GROUP BY $par_no";
	
	$sql .= " ORDER BY $par_no";
	
	return db_query($sql, "can't retrieve child trans");

}

function get_sales_parent_numbers_pos($trans_type, $trans_no)
{
	$trans = array();
	$res = get_sales_parent_lines($trans_type, $trans_no, false);
	while ($line = db_fetch($res))
		$trans[] = $line[$trans_type==ST_CUSTDELIVERY ? 'order_no' : 'debtor_trans_no'];
	return $trans;
}

//----------------------------------------------------------------------------------------

function get_sales_child_documents_pos($trans_type, $trans_no)
{
	// FIXME -  credit notes retrieved here should be those linked to invoices containing 
	// at least one line from related invoice

	if (!count($trans_no))
		return false;
	$childs = get_sales_child_numbers($trans_type, $trans_no, false);
	if (!count($childs))
		return false;
		
	$sql = "SELECT * FROM ".TB_PREF."debtor_trans
		WHERE type=".get_child_type($trans_type)." AND trans_no IN(". implode(',', array_values($childs)).")";

	return db_query($sql,"The related credit notes could not be retreived");
}



function get_item_perice($stock_id)
{
    $sql = "SELECT *
		FROM ".TB_PREF."stock_master,".TB_PREF."prices	
		WHERE ".TB_PREF."stock_master.stock_id=".TB_PREF."prices.stock_id
		AND ".TB_PREF."stock_master.stock_id=".db_escape($stock_id);
    $result = db_query($sql,"an item could not be retreived");

    return db_fetch($result);
}


function get_standard_cost($stock_id)
{
    $sql = "SELECT (material_cost + labour_cost + overhead_cost) AS std_cost
		FROM ".TB_PREF."stock_master s WHERE stock_id=".db_escape($stock_id);
    $result = db_query($sql, "The standard cost cannot be retrieved");

    $myrow = db_fetch_row($result);

    return $myrow[0];
}


function get_customer_habit_pos($customer_id)
{
    $sql = "SELECT ".TB_PREF."debtors_master.pymt_discount,
		".TB_PREF."credit_status.dissallow_invoices
		FROM ".TB_PREF."debtors_master, ".TB_PREF."credit_status
		WHERE ".TB_PREF."debtors_master.credit_status = ".TB_PREF."credit_status.id
			AND ".TB_PREF."debtors_master.debtor_no = ".db_escape($customer_id);

    $result = db_query($sql, "could not query customers");

    return db_fetch($result);
}
function adjust_shipping_charge_pos(&$delivery, $trans_no)
{
    $sql = "SELECT sum(ov_freight) as freight FROM ".TB_PREF."debtor_trans WHERE order_ = $trans_no AND type = " . ST_CUSTDELIVERY . " AND debtor_no = " . $delivery->customer_id;
    $result = db_query($sql, "Can not find delivery notes");
    $row = db_fetch_row($result);
    if (!$row[0]) $freight = 0;
    else $freight = $row[0];
    if ($freight < $delivery->freight_cost) $delivery->freight_cost = $delivery->freight_cost - $freight;
    else $delivery->freight_cost = 0;
}
function get_user_bank_account($user)
{
    $sql = "SELECT * FROM ".TB_PREF."users WHERE id = ".db_escape($user);
    $query = db_query($sql, "Error");
    return db_fetch($query);
}
?>