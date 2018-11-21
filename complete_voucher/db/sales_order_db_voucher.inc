<?php

//----------------------------------------------------------------------------------------
function add_sales_order_voucher($sales_order, $sales_order_details)
{
	global $Refs;

	begin_transaction();
	hook_db_prewrite($order, $sales_order['trans_type']);
	$order_no = get_next_trans_no($sales_order['trans_type']);
	$due_date = sql2date($sales_order['delivery_date']);
	$ord_date = sql2date($sales_order['ord_date']);
	$del_date = date2sql($due_date);
	$order_type = 0; // this is default on new order
	if($sales_order['reference'] == 'auto')
		$reference = 'auto';
	else
		$reference = $Refs->get_next(ST_SALESORDER, null, array('date' => $sales_order['ord_date'],
			'customer' => $sales_order['debtor_no'], 'branch' => $sales_order['branch_code']));

	$sql = "INSERT INTO ".TB_PREF."sales_orders (order_no, type, debtor_no, trans_type, branch_code, customer_ref, reference, comments, ord_date,
		order_type, ship_via, deliver_to, delivery_address, contact_phone,
		freight_cost, from_stk_loc, delivery_date, payment_terms, total, prep_amount,sample,supply,dc,invoice,
		application, discount1, discount2, disc1, disc2, po_date, term_cond, 
		h_text1,h_text2,h_text3,h_text4,h_text5,h_text6,h_amount1,h_amount2,h_amount3,h_date1,h_date2,h_date3,
		h_combo1,h_combo2,h_combo3,f_text1,f_text2,f_text3,f_text4,f_text5,f_text6,f_text7,f_text8,f_text9,f_text10,
		f_date1,f_date2,f_date3,f_combo1,f_combo2,f_combo3,f_comment1,f_comment2,f_comment3,salesman,dimension_id,dimension2_id,cash_received,so_advance, QuoteNo)
		VALUES (" .db_escape($order_no) . "," .db_escape($order_type) . "," . db_escape($sales_order['debtor_no']) .
		 ", " .db_escape($sales_order['trans_type']) . "," .db_escape($sales_order['branch_code']) . ", ".
			db_escape($sales_order['customer_ref']) .",".
			db_escape($reference) .",".
			db_escape($sales_order['comments']) .",'" .
			date2sql($ord_date) . "', " .
			db_escape($sales_order['order_type']) . ", " .
			db_escape($sales_order['ship_via'])."," .
			db_escape($sales_order['deliver_to']) . "," .
			db_escape($sales_order['delivery_address']) . ", " .
			db_escape($sales_order['contact_phone']) . ", " .
			db_escape($sales_order['freight_cost']) .", " .
			db_escape($sales_order['from_stk_loc']) .", " .
			db_escape($del_date) . "," .
			db_escape($sales_order['payment_terms']) . "," .
			db_escape($sales_order['total']) . "," .
			db_escape($sales_order['prep_amount']) . "," .
			db_escape($sales_order['sample']) . "," .
			db_escape($sales_order['supply']) . "," .
			db_escape($sales_order['dc']) . "," .
			db_escape($sales_order['invoice']) . "," .
			db_escape($sales_order['application']) . "," .
			db_escape($sales_order['discount1']). "," .
			db_escape($sales_order['discount2']). "," .
			db_escape($sales_order['disc1']). "," .
			db_escape($sales_order['disc2']). "," .
			db_escape($sales_order['po_date']). "," .
			db_escape($sales_order['term_cond']). "," .
			db_escape($sales_order['h_text1']). "," .
			db_escape($sales_order['h_text2']). "," .
			db_escape($sales_order['h_text3']). "," .
			db_escape($sales_order['h_text4']). "," .
			db_escape($sales_order['h_text5']). "," .
			db_escape($sales_order['h_text6']). "," .
			db_escape($sales_order['h_amount1']). "," .
			db_escape($sales_order['h_amount2']). "," .
			db_escape($sales_order['h_amount3']). "," .
			db_escape($sales_order['h_date1']). "," .
			db_escape($sales_order['h_date2']). "," .
			db_escape($sales_order['h_date3']). "," .
			db_escape($sales_order['h_combo1']). "," .
			db_escape($sales_order['h_combo2']). "," .
			db_escape($sales_order['h_combo3']). "," .
			db_escape($sales_order['f_text1']). "," .
			db_escape($sales_order['f_text2']). "," .
			db_escape($sales_order['f_text3']). "," .
			db_escape($sales_order['f_text4']). "," .
			db_escape($sales_order['f_text5']). "," .
			db_escape($sales_order['f_text6']). "," .
			db_escape($sales_order['f_text7']). "," .
			db_escape($sales_order['f_text8']). "," .
			db_escape($sales_order['f_text9']). "," .
			db_escape($sales_order['f_text10']). "," .
			db_escape($sales_order['f_date1']). "," .
			db_escape($sales_order['f_date2']). "," .
			db_escape($sales_order['f_date3']). "," .
			db_escape($sales_order['f_combo1']). "," .
			db_escape($sales_order['f_combo2']). "," .
			db_escape($sales_order['f_combo3']). "," .
			db_escape($sales_order['f_comment1']). "," .
			db_escape($sales_order['f_comment2']). "," .
			db_escape($sales_order['f_comment3']). "," .
			db_escape($sales_order['salesman']). "," .
			db_escape($sales_order['dimension_id']). "," .
			db_escape($sales_order['dimension2_id']). "," .
			db_escape($sales_order['cash_received']). "," .
			db_escape($sales_order['so_advance']). "," .
			db_escape($sales_order['QuoteNo']).")";
	db_query($sql, "order Cannot be Added");

	while($myrow = db_fetch($sales_order_details)) {
		$sql = "INSERT INTO ".TB_PREF."sales_order_details (order_no, trans_type, stk_code, description, 
		        unit_price, quantity, discount_percent,units_id,con_factor,text1, text2, text3, text4,
		        text5, text6,amount1, amount2, amount3, amount4,amount5, amount6,
				date1, date2, date3,combo1, combo2, combo3,combo4, combo5, combo6,batch,item_location) VALUES (";
		$sql .= $order_no . ",".$sales_order['trans_type'] .
            ",".db_escape($myrow['stk_code']).", "
            .db_escape($myrow['description']).", ".db_escape($myrow['unit_price']).",
            ".db_escape($myrow['quantity']).",".db_escape($myrow['discount_percent']).",".db_escape($myrow['units_id']).",
            ".db_escape($myrow['con_factor']).",".db_escape($myrow['text1']).",".db_escape($myrow['text2']).",".db_escape($myrow['text3']).",".db_escape($myrow['text4']).",
            ".db_escape($myrow['text5']).",".db_escape($myrow['text6']).",".db_escape($myrow['amount1']).",".db_escape($myrow['amount2']).",
            ".db_escape($myrow['amount3']).",".db_escape($myrow['amount4']).",".db_escape($myrow['amount5']).",".db_escape($myrow['amount6']).",
            ".db_escape($myrow['date1']).",".db_escape($myrow['date2']).",".db_escape($myrow['date3']).",".db_escape($myrow['combo1']).",
            ".db_escape($myrow['combo2']).",".db_escape($myrow['combo3']).",".db_escape($myrow['combo4']).",".db_escape($myrow['combo5']).",
            ".db_escape($myrow['combo6']).",".db_escape($myrow['batch']).",".db_escape($myrow['item_location']).")";
		db_query($sql, "order Details Cannot be Added");
		if ($sales_order['trans_type'] == ST_SALESORDER && $myrow['id'])
			update_parent_line(ST_SALESORDER, $myrow['id'], $myrow['quantity']); // clear all the quote despite all or the part was ordered
	}
	add_audit_trail($sales_order['trans_type'], $order_no, $ord_date);
	$Refs->save($sales_order['trans_type'], $order_no, $reference, null, false);
	hook_db_postwrite($order, $sales_order['trans_type']);
	commit_transaction();
	return $order_no;
}

//----------------------------------------------------------------------------------------

function get_sales_order_header_voucher($order_no, $trans_type)
{
    $sql = "SELECT sorder.*, "
        ."cust.name, "
        ."cust.credit_limit, "
        ."cust.curr_code, "
        ."loc.location_name, "
        ."cust.discount, "
        ."stype.sales_type, "
        ."stype.id AS sales_type_id, "
        ."stype.tax_included, "
        ."stype.factor, "
        ."ship.shipper_name, "
        ."tax_group.name AS tax_group_name , "
        ."tax_group.id AS tax_group_id, "
        ."cust.tax_id "
        ."FROM 0_sales_orders sorder LEFT JOIN 0_shippers ship ON  ship.shipper_id = sorder.ship_via,
        0_debtors_master cust,
        0_sales_types stype, 
        0_tax_groups tax_group, 
        0_cust_branch branch,
        0_locations loc
	    WHERE sorder.order_type=stype.id
		AND branch.branch_code = sorder.branch_code
		AND branch.tax_group_id = tax_group.id
		AND sorder.debtor_no = cust.debtor_no
		AND loc.loc_code = sorder.from_stk_loc
		AND sorder.trans_type = " . db_escape($trans_type) ."
		AND sorder.order_no = " . db_escape($order_no );

    $result = db_query($sql, "order Retreival");

    $num = db_num_rows($result);
    if ($num > 1)
    {
        display_warning("You have duplicate document in database: (type:$trans_type, number:$order_no).");
    }
    else if ($num == 1)
    {
        return db_fetch($result);
    }
    else
        display_warning("You have missing or invalid sales document in database (type:$trans_type, number:$order_no).");

}

//----------------------------------------------------------------------------------------
function get_sales_order_details_voucher($order_no, $trans_type)
{
	$sql = "SELECT *
			FROM 0_sales_order_details line,
				 0_stock_master item
			WHERE line.stk_code = item.stock_id
				AND order_no =".db_escape($order_no) 
				." AND trans_type = ".db_escape($trans_type) . " ORDER BY id";

	return db_query($sql, "Retreive order Line Items");
}
//----------------------------------------------------------------------------------------

function read_sales_order_voucher($order_no, &$order, $trans_type)
{
	$myrow = get_sales_order_header_voucher($order_no, $trans_type);

	$order->trans_type = $myrow['trans_type'];
	$order->so_type =  $myrow["type"];
	$order->trans_no = array($order_no=> $myrow["version"]);

	$order->set_customer($myrow["debtor_no"], $myrow["name"],
	  $myrow["curr_code"], $myrow["discount"], $myrow["payment_terms"]);

	$order->set_branch($myrow["branch_code"], $myrow["tax_group_id"],
	  $myrow["tax_group_name"], $myrow["contact_phone"]);

	$order->set_sales_type($myrow["sales_type_id"], $myrow["sales_type"], 
	    $myrow["tax_included"], $myrow["factor"]); // no default price calculations on edit

	$order->set_location($myrow["from_stk_loc"], $myrow["location_name"]);

	$order->set_delivery($myrow["ship_via"], $myrow["deliver_to"],
	  $myrow["delivery_address"], $myrow["freight_cost"]);

	$order->cust_ref = $myrow["customer_ref"];
	$order->sales_type =$myrow["order_type"];
	$order->reference = $myrow["reference"];
	$order->Comments = $myrow["comments"];
	$order->due_date = sql2date($myrow["delivery_date"]);
	$order->document_date = sql2date($myrow["ord_date"]);

	$order->prepaid = $myrow["prepaid"];
	$order->alloc = $myrow['alloc'];
	$order->sum_paid = $myrow["sum_paid"]; // sum of all prepayments to so (also invoiced)
	$order->prep_amount = $myrow["prep_amount"];
	$order->prepayments = get_payments_for($order_no, $myrow['trans_type']);

	$result = get_sales_order_details_voucher($order_no, $order->trans_type);
	if (db_num_rows($result) > 0)
	{
		$line_no = 0;
		while ($myrow = db_fetch($result))
		{
			$order->add_to_cart($line_no,$myrow["stk_code"],$myrow["quantity"],
				$myrow["unit_price"], $myrow["discount_percent"],
				$myrow["qty_done"], $myrow["material_cost"], $myrow["description"], $myrow["id"] );
		$line_no++;
		}
	}
	return true;
}

function get_sales_orders_header($type, $order_no)
{
	$sql = "SELECT * FROM 0_sales_order 
			WHERE type = ".db_escape($type)."
			AND order_no = ".db_escape($order_no);
	$result = db_query($sql, "Error");
	return db_fetch($result);
}

function calculate_reorder_level_voucher($location, $myrow, &$st_ids, &$st_names, &$st_num, &$st_reorder)
{
	$sql = "SELECT stock.*, loc.location_name, loc.email
		FROM ".TB_PREF."loc_stock stock,"
		.TB_PREF."locations loc
		WHERE stock.loc_code=loc.loc_code
		AND stock.stock_id = '" . $myrow['stk_code'] . "'
		AND stock.loc_code = '" . $location . "'";
	$res = db_query($sql, "A location could not be retrieved");
	$loc = db_fetch($res);
	if ($loc['email'] != "")
	{
		$qoh = get_qoh_on_date($myrow['stk_code'], $location);
		$qoh -= get_demand_qty($myrow['stk_code'], $location);
		$qoh -= get_demand_asm_qty($myrow['stk_code'], $location);
		$qoh -= $myrow['quantity'];
		if ($qoh < $loc['reorder_level'])
		{
			$st_ids[] = $myrow['stk_code'];
			$st_names[] = $myrow['description'];
			$st_num[] = $qoh - $loc['reorder_level'];
			$st_reorder[] = $loc['reorder_level'];
		}
	}
	return $loc;
}