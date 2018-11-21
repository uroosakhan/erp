<?php
include_once($path_to_root . "/purchasing/includes/db/invoice_items_db.inc");

//--------------------------------------------------------------------------------------------------

function read_supplier_details_to_trans_voucher(&$supp_trans, $supplier_id)
{
	$sql = "SELECT supp.supp_name, terms.terms, terms.days_before_due,
		terms.day_in_following_month, supp.tax_included,
		supp.tax_group_id, tax_group.name AS tax_group_name,
		supp.credit_limit - Sum(IFNULL(ov_amount + ov_gst + ov_discount,0)) as cur_credit
		FROM ".TB_PREF."suppliers supp 
			LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id, "
			.TB_PREF."payment_terms terms, ".TB_PREF."tax_groups tax_group
		WHERE supp.tax_group_id = tax_group.id
		AND supp.payment_terms=terms.terms_indicator
		AND supp.supplier_id = ".db_escape($supplier_id)." GROUP BY supp.supp_name";

	$result = db_query($sql, "The supplier record selected: " . $supplier_id . " cannot be retrieved");

	$myrow = db_fetch($result);

    $supp_trans->supplier_id = $supplier_id;
    $supp_trans->tax_included = $myrow['tax_included'];
    $supp_trans->supplier_name = $myrow['supp_name'];
	$supp_trans->terms = array( 
		'description' => $myrow['terms'],
		'days_before_due' => $myrow['days_before_due'], 
		'day_in_following_month' => $myrow['day_in_following_month'] );

	$supp_trans->credit = $myrow['cur_credit'];

	$supp_trans->tax_description = $myrow['tax_group_name'];
	$supp_trans->tax_group_id = $myrow['tax_group_id'];

    if ($supp_trans->tran_date == "")
    {
		$supp_trans->tran_date = Today();
		if (!is_date_in_fiscalyear($supp_trans->tran_date))
			$supp_trans->tran_date = end_fiscalyear();
	}
    //if ($supp_trans->due_date=="") {
    //	get_duedate_from_terms($supp_trans);
    //}
    get_duedate_from_terms($supp_trans);
}

//--------------------------------------------------------------------------------------------------

function update_supp_received_items_for_invoice_voucher($id, $po_detail_item, $qty_invoiced, $chg_price=null)
{
	if ($chg_price != null)
	{
		$sql = "SELECT act_price, unit_price FROM ".TB_PREF."purch_order_details WHERE
			po_detail_item = ".db_escape($po_detail_item);
		$result = db_query($sql, "The old actual price of the purchase order line could not be retrieved");
		$row = db_fetch_row($result);
		$ret = $row[0];

		$unit_price = $row[1]; //Added by Rasmus

		$sql = "SELECT delivery_date FROM ".TB_PREF."grn_batch,".TB_PREF."grn_items WHERE
			".TB_PREF."grn_batch.id = ".TB_PREF."grn_items.grn_batch_id AND "
			.TB_PREF."grn_items.id=".db_escape($id);
		$result = db_query($sql, "The old delivery date from the received record cout not be retrieved");
		$row = db_fetch_row($result);
		$date = $row[0];
	}
	else
	{
		$ret = 0;
		$date = "";
		$unit_price = 0; // Added by Rasmus
	}
    $sql = "UPDATE ".TB_PREF."purch_order_details
		SET qty_invoiced = qty_invoiced + ".db_escape($qty_invoiced);

	if ($chg_price != null)
		$sql .= " , act_price = ".db_escape($chg_price);

	$sql .= " WHERE po_detail_item = ".db_escape($po_detail_item);
    db_query($sql, "The quantity invoiced of the purchase order line could not be updated");

    $sql = "UPDATE ".TB_PREF."grn_items
        SET quantity_inv = quantity_inv + ".db_escape($qty_invoiced)."
        WHERE id = ".db_escape($id);
	db_query($sql, "The quantity invoiced off the items received record could not be updated");
	return array($ret, $date, $unit_price);
}

function get_diff_in_home_currency_voucher($supplier, $old_date, $date, $amount1, $amount2)
{
	$dec = user_price_dec();
	price_decimal_format($amount2, $dec);
	$currency = get_supplier_currency($supplier);
	$ex_rate = get_exchange_rate_to_home_currency($currency, $old_date);
	$amount1 = $amount1 / $ex_rate;
	$ex_rate = get_exchange_rate_to_home_currency($currency, $date);
	$amount2 = $amount2 / $ex_rate;
	$diff = $amount2 - $amount1;
	//return round2($diff, $dec);
	return $diff;
}
//----------------------------------------------------------------------------------------

function add_supp_invoice_voucher($GetST, $GetGrnItemsDetails, $GetTransTax, $Get_GL, $memo_)
{
	global $Refs;
	begin_transaction();
	hook_db_prewrite($supp_trans, ST_SUPPINVOICE);
	$trans_type = ST_SUPPINVOICE;
    $date_ = sql2date($GetST['tran_date']);
    $reference = $Refs->get_next($trans_type, null, sql2date($date_));
	$invoice_id = write_supp_trans($trans_type, 0, $GetST['supplier_id'], $date_, sql2date($GetST['due_date']),
	$reference, $GetST['supp_reference'],
	$GetST['ov_amount'], $GetST['ov_gst'], $GetST['gst_wh'], $GetST['ov_discount'], "",
	$GetST['tax_included'], $GetST['supply_disc'], $GetST['service_disc'], $GetST['fbr_disc'], $GetST['srb_disc'], $GetST['write_back'],
	$GetST['supply_tax_id'], $GetST['service_tax_id'], $GetST['fbr_tax_id'], $GetST['srb_tax_id'], $GetST['cheque'], $GetST['cheque_date'],
	$GetST['text_1'],$GetST['text_2'],$GetST['text_3'], $GetST['dimension_id'], $GetST['dimension2_id']);
	$total = 0;
    while($AddGL = db_fetch($Get_GL)) {
        $total += add_gl_trans_supplier($trans_type, $invoice_id, sql2date($AddGL['tran_date']), $AddGL['account'],
            $AddGL['dimension_id'], $AddGL['dimension2_id'],
            $AddGL['amount'], $AddGL['person_id'], "", 0);
    }
    while($AddGrnItemsDetails = db_fetch($GetGrnItemsDetails)) {
        $sql = "UPDATE ".TB_PREF."grn_items SET quantity_inv = ".db_escape($AddGrnItemsDetails['qty_recd'])."
			WHERE id = ". db_escape($AddGrnItemsDetails['id']);
        db_query($sql, "Error");
		if (is_inventory_item($AddGrnItemsDetails['item_code']))
		{
			$mat_cost = update_average_material_cost(null, $AddGrnItemsDetails['item_code'],
				$AddGrnItemsDetails['unit_price'], $AddGrnItemsDetails['qty_recd'], null, true);
		}
		add_or_update_purchase_data($GetST['supplier_id'], $AddGrnItemsDetails['item_code'],
			$AddGrnItemsDetails['unit_price']);
    	// ----------------------------------------------------------------------
		add_supp_invoice_item($trans_type, $invoice_id, $AddGrnItemsDetails['item_code'],
		$AddGrnItemsDetails['description'], 0, 	$AddGrnItemsDetails['unit_price'], 0,
		$AddGrnItemsDetails['qty_recd'], $AddGrnItemsDetails['id'], $AddGrnItemsDetails['po_detail_item'], "",
		$AddGrnItemsDetails['dimension_id'], $AddGrnItemsDetails['dimension2_id'],
		$AddGrnItemsDetails['text1'], $AddGrnItemsDetails['text2'], $AddGrnItemsDetails['text3'], $AddGrnItemsDetails['text4'],
		$AddGrnItemsDetails['text5'], $AddGrnItemsDetails['text6'],
		$AddGrnItemsDetails['amount1'], $AddGrnItemsDetails['amount2'], $AddGrnItemsDetails['amount3'], $AddGrnItemsDetails['amount4'],
		$AddGrnItemsDetails['amount5'], $AddGrnItemsDetails['amount6'],
		$AddGrnItemsDetails['date1'], $AddGrnItemsDetails['date2'], $AddGrnItemsDetails['date3'],
		$AddGrnItemsDetails['combo1'], $AddGrnItemsDetails['combo2'], $AddGrnItemsDetails['combo3'],$AddGrnItemsDetails['combo4'],
		$AddGrnItemsDetails['combo5'], $AddGrnItemsDetails['combo6'], $AddGrnItemsDetails['batch'],
		$AddGrnItemsDetails['con_factor'],$AddGrnItemsDetails['units_id'], $AddGrnItemsDetails['discount_percent']);
    }
	while($AddTransTax = db_fetch($GetTransTax)) {
		add_trans_tax_details($trans_type, $invoice_id,
		$AddTransTax['tax_type_id'], $AddTransTax['rate'], $AddTransTax['included_in_price'],
		$AddTransTax['amount'], $AddTransTax['net_amount'], $AddTransTax['ex_rate'], sql2date($AddTransTax['tran_date']),
		$AddTransTax['memo']);
	}
    inactive_gl(ST_SUPPINVOICE, $GetST['trans_no']);
	add_comments($trans_type, $invoice_id, $date_, $memo_);
	$Refs->save($trans_type, $invoice_id, $reference);
	hook_db_postwrite($supp_trans, ST_SUPPINVOICE);
    commit_transaction();
    return $invoice_id;
}

//----------------------------------------------------------------------------------------
function get_grn_batch_id($purch_order_no)
{
    $sql = "SELECT * FROM ".TB_PREF."grn_batch
		WHERE purch_order_no = ".db_escape($purch_order_no)." ORDER BY id";
    $query = db_query($sql, "Cannot retreive supplier transaction detail records");
	return db_fetch($query);
}
//----------------------------------------------------------------------------------------
function get_grn_items_details($grn_batch_id)
{
    $sql = "SELECT items.*, details.unit_price FROM ".TB_PREF."grn_items items
    INNER JOIN ".TB_PREF."purch_order_details details
    ON items.po_detail_item = details.po_detail_item
		WHERE items.grn_batch_id = ".db_escape($grn_batch_id)." ORDER BY id";
    return db_query($sql, "Cannot retreive supplier transaction detail records");
}
// get all the invoices/credits for a given PO - quite long route to get there !

function get_po_invoices_credits_voucher($po_number)
{
	$sql = "SELECT DISTINCT ".TB_PREF."supp_trans.trans_no, ".TB_PREF."supp_trans.type,
		ov_amount+ov_discount+ov_gst AS Total,
		".TB_PREF."supp_trans.tran_date
		FROM ".TB_PREF."supp_trans, ".TB_PREF."supp_invoice_items, "
		.TB_PREF."purch_order_details, ".TB_PREF."purch_orders
		WHERE ".TB_PREF."supp_invoice_items.supp_trans_no = ".TB_PREF."supp_trans.trans_no
		AND ".TB_PREF."supp_invoice_items.supp_trans_type = ".TB_PREF."supp_trans.type
		AND ".TB_PREF."supp_invoice_items.po_detail_item_id = ".TB_PREF."purch_order_details.po_detail_item
		AND ".TB_PREF."purch_orders.supplier_id = ".TB_PREF."supp_trans.supplier_id
		AND ".TB_PREF."purch_orders.order_no = ".TB_PREF."purch_order_details.order_no
		AND ".TB_PREF."purch_order_details.order_no = ".db_escape($po_number);

	return db_query($sql, "The invoices/credits for the po $po_number could not be retreived");
}

function get_supp_trans_voucher($trans_no, $trans_type=-1)
{
    $sql = "SELECT 0_supp_trans.*, (0_supp_trans.ov_amount+0_supp_trans.ov_gst+0_supp_trans.ov_discount+0_supp_trans.gst_wh) AS Total,
		0_suppliers.supp_name AS supplier_name, 0_suppliers.curr_code AS SupplierCurrCode ";

    if ($trans_type == ST_SUPPAYMENT)
    {
        // it's a payment so also get the bank account
        $sql .= ", 0_bank_accounts.bank_name, 0_bank_accounts.bank_account_name, 0_bank_accounts.bank_curr_code,
			0_bank_accounts.account_type AS BankTransType, 0_bank_trans.amount AS BankAmount,
			0_bank_trans.ref ";
    }

    $sql .= " FROM 0_supp_trans, 0_suppliers ";

    if ($trans_type == ST_SUPPAYMENT)
    {
        // it's a payment so also get the bank account
        $sql .= ", 0_bank_trans, 0_bank_accounts";
    }

    $sql .= " WHERE 0_supp_trans.trans_no=".db_escape($trans_no)."
		AND 0_supp_trans.supplier_id=0_suppliers.supplier_id";

    if ($trans_type > 0)
        $sql .= " AND 0_supp_trans.type=".db_escape($trans_type);

    if ($trans_type == ST_SUPPAYMENT)
    {
        // it's a payment so also get the bank account
        $sql .= " AND 0_bank_trans.trans_no =".db_escape($trans_no)."
			AND 0_bank_trans.type=".db_escape($trans_type)."
			AND 0_bank_accounts.id=0_bank_trans.bank_act ";
    }

    $result = db_query($sql, "Cannot retreive a supplier transaction");

    if (db_num_rows($result) == 0)
    {
        // can't return nothing
        display_db_error("no supplier trans found for given params", $sql, true);
        exit;
    }

    if (db_num_rows($result) > 1)
    {
        // can't return multiple
        display_db_error("duplicate supplier transactions found for given params", $sql, true);
        exit;
    }

    return db_fetch($result);
}

?>
