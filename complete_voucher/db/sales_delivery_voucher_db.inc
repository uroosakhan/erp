<?php
//-----------------------------------------------------------------------------
// insert/update sales delivery
//
function write_sales_delivery_voucher($sales_order, $sales_order_details, $delivery_trans, $delivery_trans_details, $trans_tax_details, $order_no)
{
	global $Refs;
	
	$trans_no = 0;
	if (is_array($trans_no)) $trans_no = key($trans_no);

    if($delivery_trans['reference'] == 'auto')
        $reference = 'auto';
    else
        $reference = $Refs->get_next(ST_CUSTDELIVERY, null, array('date' => $delivery_trans['tran_date'],
            'customer' => $delivery_trans['debtor_no'], 'branch' => $delivery_trans['branch_code']));

	begin_transaction();
	hook_db_prewrite($delivery, ST_CUSTDELIVERY);

	$customer = get_customer($delivery_trans['debtor_no']);
	$delivery_items_total = $delivery_trans['ov_amount'];
	$freight_tax = $delivery_trans['ov_freight_tax'];
	$tax_total = 0;
	$delivery_no = write_customer_trans(ST_CUSTDELIVERY, $trans_no, $delivery_trans['debtor_no'],
	$delivery_trans['branch_code'], sql2date($delivery_trans['tran_date']), $reference/*$delivery_trans['reference']*/,
	$delivery_items_total, 0, $sales_order['tax_included'] ? 0 : $tax_total-$freight_tax, // here is problem
	$sales_order['freight_cost'], $sales_order['tax_included'] ? 0 : $freight_tax,
	$delivery_trans['sales_type'], $order_no,
	$delivery_trans['ship_via'], $delivery_trans['due_date'], 0, 0,
	$delivery_trans['dimension_id'],
	$delivery_trans['dimension2_id'], $delivery_trans['payment'], $delivery_trans['tax_included'], $delivery_trans['prep_amount'],
    $delivery_trans['supply_disc'],$delivery_trans['service_disc'],
    $delivery_trans['fbr_disc'], $delivery_trans['srb_disc'], $delivery_trans['write_back'], $delivery_trans['supply_tax_id'], $delivery_trans['service_tax_id'],
    $delivery_trans['fbr_tax_id'], $delivery_trans['srb_tax_id'], $delivery_trans['fbr_tax_id'], $delivery_trans['discount1'],
    $delivery_trans['discount2'], $delivery_trans['cheque_date'], $delivery_trans['cheque_no'], $delivery_trans['text_1'],
    $delivery_trans['text_2'], $delivery_trans['text_3'], $delivery_trans['salesman'], $delivery_trans['h_amount1'],
    $delivery_trans['so_no'], $delivery_trans['cust_ref']);

	while($myrow = db_fetch($delivery_trans_details))
	{
		$get_src_id = get_delivery_parent_lines(ST_SALESORDER);
		/* add delivery details for all lines */
		write_customer_trans_detail_item(ST_CUSTDELIVERY, $delivery_no, $myrow['stock_id'],
			$myrow['description'], $myrow['quantity'],
			$myrow['unit_price'], $myrow['unit_tax'],
			$myrow['discount_percent'], $myrow['standard_cost'], $get_src_id, 0,$myrow['units_id'],
            $myrow['con_factor'], $myrow['text1'], $myrow['text2'], $myrow['text3'], $myrow['text4'],$myrow['text5'], $myrow['text6'],
            $myrow['amount1'], $myrow['amount2'], $myrow['amount3'], $myrow['amount4'],$myrow['amount5'], $myrow['amount6'],
            $myrow['date1'], $myrow['date2'], $myrow['date3'],
            $myrow['combo1'], $myrow['combo2'], $myrow['combo3'],$myrow['combo4'], $myrow['combo5'],
            $myrow['combo6'],$myrow['batch'],$myrow['item_location']);

		if ($myrow['quantity'] != 0) {
            add_stock_move(ST_CUSTDELIVERY, $myrow['stock_id'], $delivery_no,
                $sales_order['from_stk_loc'], sql2date($delivery_trans['tran_date']), $reference/*$delivery_trans['reference']*/,
                -$myrow['quantity'], $myrow['standard_cost'], $myrow['unit_price'] * (1 - $myrow['discount_percent']),
                $myrow['person_id'], $myrow['discount_percent'], $myrow['text1'], $myrow['text2'], $myrow['text3'],
                $myrow['text4'], $myrow['text5'], $myrow['text6'], $myrow['amount1'], $myrow['amount2'],
                $myrow['amount3'], $myrow['amount4'], $myrow['amount5'], $myrow['amount6'], $myrow['date1'], $myrow['date2'], $myrow['date3'],
                $myrow['combo1'], $myrow['combo2'], $myrow['combo3'], $myrow['combo4'], $myrow['combo5'], $myrow['combo6'],
                $myrow['units_id'], $myrow['con_factor'], $myrow['batch'], $myrow['to_stk_loc']);
            $stock_gl_code = get_stock_gl_code($myrow['stock_id']);
            /* insert gl_trans to credit stock and debit cost of sales at standard cost*/
            if (is_inventory_item($myrow['stock_id']) && $myrow['standard_cost'] != 0) {
                $dim = ($delivery_trans['dimension_id'] != $customer['dimension_id'] ? $delivery_trans['dimension_id'] :
                    ($customer['dimension_id'] != 0 ? $customer["dimension_id"] : $stock_gl_code["dimension_id"]));
                $dim2 = ($delivery_trans['dimension2_id'] != $customer['dimension2_id'] ? $delivery_trans['dimension2_id'] :
                    ($customer['dimension2_id'] != 0 ? $customer["dimension2_id"] : $stock_gl_code["dimension2_id"]));
                add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no,
                    sql2date($delivery_trans['tran_date']), $stock_gl_code["cogs_account"], $dim, $dim2, "",
                    $myrow['standard_cost'] * $myrow['quantity'],
                    PT_CUSTOMER, $delivery_trans['debtor_no'],
                    "The cost of sales GL posting could not be inserted");
                /*now the stock entry*/
                add_gl_trans_std_cost(ST_CUSTDELIVERY, $delivery_no, sql2date($delivery_trans['tran_date']),
                    $stock_gl_code["inventory_account"], 0, 0, "",
                    (-$myrow['standard_cost'] * $myrow['quantity']),
                    PT_CUSTOMER, $delivery_trans['debtor_no'],
                    "The stock side of the cost of sales GL posting could not be inserted");
            }
        }
	}
	close_sales_order($delivery_trans['order_']);
	add_trans_tax_details(ST_CUSTDELIVERY, $delivery_no, $trans_tax_details['tax_type_id'],
	$trans_tax_details['rate'], $trans_tax_details['included_in_price'], $trans_tax_details['amount'],
	$trans_tax_details['net_amount'], $trans_tax_details['ex_rate'], $trans_tax_details['tran_date'], $trans_tax_details['reg_type']);
//		}
//	}

//	Update
	$sql1 = "UPDATE ".TB_PREF."sales_orders SET version=version+1 WHERE order_no=". db_escape($order_no)."
		 AND trans_type=".ST_SALESORDER;
	db_query($sql1, 'Concurrent editing conflict while sales order update');

	$sql2 = "UPDATE ".TB_PREF."debtor_trans SET version=version+1, tpe=tpe+1 WHERE trans_no=". db_escape($delivery_no)."
		 AND type=".ST_CUSTDELIVERY;
	db_query($sql2, 'Concurrent editing conflict while sales order update');

	add_comments(ST_CUSTDELIVERY, $delivery_no, $delivery_trans['tran_date'], $delivery_trans['memo_']);

	if ($trans_no == 0) {
			$Refs->save(ST_CUSTDELIVERY, $delivery_no, $reference);
	}
	hook_db_postwrite($delivery, ST_CUSTDELIVERY);
	commit_transaction();

	return $delivery_no;
}

//--------------------------------------------------------------------------------------------------
function adjust_shipping_charge_voucher(&$delivery, $trans_no)
{
  	$sql = "SELECT sum(ov_freight) as freight FROM ".TB_PREF."debtor_trans WHERE order_ = $trans_no AND type = " . ST_CUSTDELIVERY . " AND debtor_no = " . $delivery->customer_id;
  	$result = db_query($sql, "Can not find delivery notes");
  	$row = db_fetch_row($result);
  	if (!$row[0]) $freight = 0;
  	else $freight = $row[0];
  	if ($freight < $delivery->freight_cost) $delivery->freight_cost = $delivery->freight_cost - $freight;
  	else $delivery->freight_cost = 0;
}

//--------------------------------------------------------------------------------------------------

function void_sales_delivery_voucher($type, $type_no, $transactions=true)
{
	if ($transactions)
		begin_transaction();

	hook_db_prevoid($type, $type_no);
	
	void_gl_trans($type, $type_no, true);

	// reverse all the changes in the sales order
	$items_result = get_customer_trans_details($type, $type_no);

	$order = get_customer_trans_order($type, $type_no);

	if ($order) {
		$auto = (get_reference(ST_SALESORDER, $order) == "auto");
		while ($row = db_fetch($items_result)) {
			update_parent_line(ST_CUSTDELIVERY, $row['src_id'], -$row['quantity'], $auto);
		}
	}

	// clear details after they've been reversed in the sales order
	void_customer_trans_details($type, $type_no);

	void_trans_tax_details($type, $type_no);

	void_cust_allocations($type, $type_no);

	// do this last because other voidings can depend on it
	// DO NOT MOVE THIS ABOVE VOIDING or we can end up with trans with alloc < 0
	void_customer_trans($type, $type_no);

	if ($transactions)
		commit_transaction();
}

function get_taxes_voucher($delivery_trans, $sales_order, $sales_order_details)
{
	$items = array();
	$prices = array();
//	if($shipping_cost==null)
		$shipping_cost = $sales_order['freight_cost'];

//	foreach ($this->line_items as $ln_itm)
	while($myrow = db_fetch($sales_order_details))
	{
		$items[] = $myrow['stock_id'];
		$prices[] = round(((
			$sales_order['trans_type']==ST_SALESORDER ? $myrow['quantity'] : $myrow['qty_done']) *
			$myrow['unit_price'] * (1 - $myrow['discount_percent'])),  user_price_dec());
	}

	$taxes = get_tax_for_items($items, $prices, $shipping_cost,
		$this->tax_group_id, $this->tax_included,  $this->tax_group_array);

	// Adjustment for swiss franken, we always have 5 rappen = 1/20 franken
	if ($this->customer_currency == 'CHF') {
		$val = $taxes['1']['Value'];
		$val1 = (floatval((intval(round(($val*20),0)))/20));
		$taxes['1']['Value'] = $val1;
	}
	return $taxes;
}
?>