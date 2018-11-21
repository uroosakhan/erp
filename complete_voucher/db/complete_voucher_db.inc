<?php

function get_vouchers_from_bank_trans($trans_type, $order_no)
{
	$sql = "SELECT trans.*
			FROM 0_bank_trans trans
		WHERE trans.type = " . db_escape($trans_type) ."
		AND trans.trans_no = " . db_escape($order_no);

	$result = db_query($sql, "Error");

	return db_fetch($result);
}
function get_vouchers_from_debtor_trans($trans_type, $order_no)
{
	$sql = "SELECT trans.*, master.name
			FROM 0_debtor_trans trans, 0_debtors_master master
		WHERE trans.debtor_no = master.debtor_no
		AND trans.type = " . db_escape($trans_type) ."
		AND trans.trans_no = " . db_escape($order_no);

	$result = db_query($sql, "Error");

	return db_fetch($result);
}

function get_vouchers_from_supp_trans($trans_type, $order_no)
{
	$sql = "SELECT trans.*, master.supp_name
			FROM 0_supp_trans trans, 0_suppliers master
		WHERE trans.supplier_id = master.supplier_id
		AND trans.type = " . db_escape($trans_type) ."
		AND trans.trans_no = " . db_escape($order_no);

	$result = db_query($sql, "Error");

	return db_fetch($result);
}

function get_data_for_journal_inquiry($filter, $from, $to, $ref='', $memo='', $alsoclosed=false,
									 $user_id=null, $contractor_id=null, $dimension=null)
{
	$sql = "SELECT IFNULL(a.gl_seq,0) as gl_seq,
		gl.tran_date,
		gl.type as trans_type,
		
		IFNULL(MAX(supp.supp_name), MAX(cust.name)) as name,
		IF(ISNULL(st.supp_reference), '', st.supp_reference) AS supp_reference,
		refs.reference,"
		.($dimension ? " -SUM(IF(dim.dimension in(gl.dimension_id,gl.dimension2_id), gl.amount, 0)) as amount,":" SUM(IF(gl.amount>0, gl.amount,0)) as amount,")
		."com.memo_,
		IF(ISNULL(u.user_id),'',u.user_id) as user_id,";

	if ($contractor_id > 0) {
		$sql.= ", st.supplier_id, dt.debtor_no ";
	}

	$sql.= " gl.type_no as trans_no FROM 0_gl_trans as gl
		 LEFT JOIN 0_audit_trail as a ON
			(gl.type=a.type AND gl.type_no=a.trans_no)
		 LEFT JOIN 0_comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
		 LEFT JOIN 0_refs as refs ON
			(gl.type=refs.type AND gl.type_no=refs.id)
		 LEFT JOIN 0_users as u ON
			a.user=u.id
	 	 LEFT JOIN 0_debtor_trans dt ON dt.type=gl.type AND gl.type_no=dt.trans_no
	 	 LEFT JOIN 0_debtors_master cust ON gl.person_type_id=2 AND gl.person_id=cust.debtor_no
	 	 LEFT JOIN 0_supp_trans st ON st.type=gl.type AND gl.type_no=st.trans_no
	 	 LEFT JOIN 0_suppliers supp ON gl.person_type_id=3 AND gl.person_id=supp.supplier_id"
		.($dimension ?
			" LEFT JOIN (SELECT type, type_no, MAX(IFNULL(dimension_id, dimension2_id)) dimension FROM 0_gl_trans GROUP BY type, type_no) dim 
		 		ON gl.type=dim.type AND gl.type_no=dim.type_no" : '')
		." WHERE gl.tran_date >= '" . date2sql($from) . "'
		AND gl.tran_date <= '" . date2sql($to) . "'
		AND gl.amount!=0 
		AND gl.inactive=0";
	if ($ref) {
		$sql .= " AND refs.reference LIKE ". db_escape("%$ref%");
	}
	if ($memo) {
		$sql .= " AND com.memo_ LIKE ". db_escape("%$memo%");
	}
	//$sql .= " AND gl.type IN(".ST_JOURNAL.", ".ST_BANKPAYMENT.", ".ST_BANKDEPOSIT.", ".ST_BANKTRANSFER.", ".ST_SALESINVOICE.", ".ST_CUSTCREDIT.", ".ST_SUPPINVOICE.", ".ST_CPV.", ".ST_CRV.")";
	$sql .= " AND gl.type IN(".ST_JOURNAL.", ".ST_BANKPAYMENT.", ".ST_BANKDEPOSIT.", ".ST_BANKTRANSFER.", ".ST_SALESINVOICE.", ".ST_CUSTCREDIT.", ".ST_SUPPINVOICE.", ".ST_CPV.", ".ST_CRV.", ".ST_CUSTPAYMENT.", ".ST_SUPPAYMENT.")";
	if ($filter != -1)
	{
		$sql .= " AND gl.type=".db_escape($filter);
	}
	if (!$alsoclosed) {
		$sql .= " AND gl_seq=0";
	}
	else
		$sql .= " AND NOT ISNULL(a.gl_seq)";

	if ($user_id != null)
		$sql .= " AND user_id = ".db_escape($user_id);

	if ($contractor_id > 0) {
		$sql.= " AND (dt.debtor_no =".$contractor_id;
		$sql.= " OR st.supplier_id =".$contractor_id.") ";
	}

	if ($dimension != null)
		$sql .= " AND dim.dimension = ".db_escape($dimension);

	$sql .= " GROUP BY gl.tran_date, a.gl_seq, gl.type, gl.type_no";

	return $sql;
}
//--------------------------------------------------------------------------------

function get_gl_trans_for_complete_voucher($type, $trans_id)
{
    $sql = "SELECT gl.*, cm.account_name, IF(ISNULL(refs.reference), '', refs.reference) AS reference FROM 
                      0_gl_trans as gl
            LEFT JOIN 0_chart_master as cm ON gl.account = cm.account_code
            LEFT JOIN 0_refs as refs ON (gl.type=refs.type AND gl.type_no=refs.id)"
        ." WHERE gl.type= ".db_escape($type)
        ." AND gl.type_no = ".db_escape($trans_id)
        ." AND gl.amount <> 0"
        ." ORDER BY counter";
	return db_query($sql, "The gl transactions could not be retrieved");
}

//----------------------------------------------------------------------------------------


function get_bank_trans_for_complete_invoice($type, $trans_no=null/*, $person_type_id=null, $person_id=null*/)
{
    $sql = "SELECT *, bank_account_name, account_code, bank_curr_code
		FROM 0_bank_trans, 0_bank_accounts
		WHERE 0_bank_accounts.id=0_bank_trans.bank_act ";
//    if ($type != null)
        $sql .= " AND type=".db_escape($type);
//    if ($trans_no != null)
        $sql .= " AND 0_bank_trans.trans_no = ".db_escape($trans_no);
//    if ($person_type_id != null)
//        $sql .= " AND ".TB_PREF."bank_trans.person_type_id = ".db_escape($person_type_id);
//    if ($person_id != null)
//        $sql .= " AND ".TB_PREF."bank_trans.person_id = ".db_escape($person_id);
    $sql .= " ORDER BY trans_date, 0_bank_trans.id";

    return db_query($sql, "query for bank transaction");
}

//----------------------------------------------------------------------------------

function get_bank_account_currency_for_c_i($id)
{
	$sql= "SELECT bank_curr_code FROM 0_bank_accounts WHERE id=".db_escape($id);
	$result = db_query($sql, "retreive bank account currency");
	return db_fetch($result);
}
//---------------------------------------------------------------------------------------------

function get_bank_gl_account_for_c_i($id)
{
	$sql = "SELECT account_code FROM 0_bank_accounts WHERE id=".db_escape($id);

	$result = db_query($sql, "could not retreive bank account for $id");

	$bank_account = db_fetch($result);

	return $bank_account['account_code'];
}

//--------------------------------------------------------------------------------------

function get_comments_string_for_C_I($type, $type_no)
{
	$str_return = "";
	$result = get_comments_for_C_I($type, $type_no);
	while ($comment = db_fetch($result))
	{
		if (strlen($str_return))
			$str_return = $str_return . " \n";
		$str_return = $str_return . $comment["memo_"];
	}
	return $str_return;
}

//--------------------------------------------------------------------------------------------------

function get_comments_for_C_I($type, $type_no)
{
	$sql = "SELECT * FROM 0_comments WHERE type="
		.db_escape($type)." AND id=".db_escape($type_no);

	return db_query($sql, "could not query comments transaction table");
}
//--------------------------------------------------------------------------------
function has_attachment_for_C_I($type, $id)
{
	$sql = "SELECT DISTINCT * FROM 0_attachments WHERE type_no=".db_escape($type)." AND trans_no=".db_escape($id);
	$result = db_query($sql, "Could not retrieve attachments");
	$myrow = db_fetch($result);
	if ($myrow === false)
		return 0;
	else
		return $myrow['id'];
}
//--------------------------------------------------------------------------------
function exchange_from_to_for_C_I($amount, $from_curr_code, $to_curr_code, $date_)
{
	$ex_rate = get_exchange_rate_from_to($from_curr_code, $to_curr_code, $date_);
	return $amount / $ex_rate;

}
function inactive_gl($type, $trans_no)
{
	$sql = "UPDATE 0_gl_trans SET inactive = 1 
			WHERE type_no = ". db_escape($trans_no)."
			AND type =".db_escape($type);
	db_query($sql, 'Error');
}

function get_stock_moves_voucher($type, $type_no)
{
    $sql = "SELECT 0_stock_moves.*
		FROM 0_stock_moves
		WHERE type =".db_escape($type)." 
		AND trans_no=".db_escape($type_no);

    return db_query($sql, "Could not get stock moves");
}

function get_supplier_name_voucher($supplier_id)
{
	$sql = "SELECT supp_name AS name FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
//--------------------------------------------------------------------------------
function get_trans_tax_details_record($type, $trans_no)
{
	$sql = "SELECT * FROM 0_trans_tax_details 
			WHERE trans_type = ".db_escape($type)."
			AND trans_no = ".db_escape($trans_no);
	$result = db_query($sql, "Error");
	return db_fetch($result);
}

function get_customer_voucher($customer_id)
{
	$sql = "SELECT * FROM 0_debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	return db_fetch($result);
}

function get_branch_accounts_voucher($branch_id)
{
	$sql = "SELECT receivables_account,sales_account, sales_discount_account, payment_discount_account 
		FROM 0_cust_branch WHERE branch_code=".db_escape($branch_id);

	$result = db_query($sql, "Cannot retreive a customer branch");

	return db_fetch($result);
}

function add_bank_transactions($val)
{
	global $Refs, $systypes_array, $SysPrefs;
	
	$invoice_detail = array();

	$fydetail = get_current_fiscalyear();
	$tax_rate = $fydetail['tax_rate'];

	if ($_SESSION['Type'] != '')
	{
		$Type = $_SESSION['Type'];

		/*if ($_SESSION['Type'] == ST_SALESINVOICE)
            $myrow = get_vouchers_from_debtor_trans($Type, $val);
        else*/
		if($_SESSION['Type'] == ST_BANKPAYMENT  ||
			$_SESSION['Type'] == ST_BANKDEPOSIT ||
			$_SESSION['Type'] == ST_CPV         ||
			$_SESSION['Type'] == ST_CRV         ||
			$_SESSION['Type'] == ST_CUSTPAYMENT ||
			$_SESSION['Type'] == ST_SUPPAYMENT)
			$myrow = get_vouchers_from_bank_trans($Type, $val);

		$company_data = get_company_prefs();
        $date_ = sql2date($myrow['trans_date']);
		$ref = $Refs->get_next($Type, null, $date_);
		$Total_Amount = round($myrow['amount']);
//		$date_ = sql2date($myrow['trans_date']);
//		$trans_no = write_customer_trans($Type, 0, $myrow['debtor_no'],
//		$myrow['branch_code'], $date_, $ref, $Total_Amount);

		$cart = new items_cart($Type);

//		$cart->order_id = $trans_no;
		$do_exchange_variance = false;
		$bank_trans = db_fetch(get_bank_trans_for_complete_invoice($Type, $val));
		$currency = get_bank_account_currency_for_c_i($bank_trans['bank_act']);
		$bank_gl_account = get_bank_gl_account_for_c_i($bank_trans['bank_act']);
		$memo_ = get_comments_string_for_C_I($Type, $val);
		$aid = 0;

//		if ($trans_no)
//		{
//			$old_trans = $trans_no;
////			$Refs->restore_last($Type, $val);
//			$aid = has_attachment_for_C_I($Type, $val);
//		}
//		else
//			$old_trans = false;

		$result = get_gl_trans_for_complete_voucher($Type, $val);
		if ($result) {
			while ($row = db_fetch($result)) {
				if (is_bank_account($row['account'])) {
					// date exchange rate is currenly not stored in bank transaction,
					// so we have to restore it from original gl amounts
					$ex_rate = $bank_trans['amount'] / $row['amount'];
				} else {
					$cart->add_gl_item($row['account'], $row['dimension_id'],
						$row['dimension2_id'], $row['amount'], $row['memo_']);
				}
			}
		}
		$total_amount = $cart->gl_items_total();

		if ($bank_trans['person_type_id'] == PT_CUSTOMER)
		{
			// we need to add a customer transaction record
			// convert to customer currency
			if (!isset($settled_amount)) // leaved for backward/ext compatibility
				$cust_amount = exchange_from_to(abs($total_amount), $currency, get_customer_currency($bank_trans['person_id']), $date_);
			else
				$cust_amount = $total_amount;

			if ($Type == ST_BANKPAYMENT)
				$cust_amount = -$total_amount;
			$sql = "SELECT * FROM 0_debtor_trans WHERE type = ".db_escape($Type)." AND trans_no = ".db_escape($val);
			$query = db_query($sql, "Error");
			$fetch = db_fetch($query);
			$trans_no = write_customer_trans($Type, 0, $fetch['debtor_no'], $fetch['branch_code'], $date_,
				$ref, $cust_amount);


//			if ($old_trans)
//				move_trans_attachments($Type, $old_trans, $trans_no);
		}
		elseif($bank_trans['person_type_id'] == PT_SUPPLIER)
		{
			// we need to add a supplier transaction record
			// convert to supp currency
			if (!isset($settled_amount)) // leaved for for backward/ext compatibility
				$supp_amount = exchange_from_to_for_C_I(abs($total_amount), $currency, get_supplier_currency($bank_trans['person_id']), $date_);
			else
				$supp_amount = $settled_amount;

			if ($Type == ST_BANKPAYMENT/* || $Type == ST_CPV*/)
				$supp_amount = -$supp_amount;

			$trans_no = write_supp_trans($Type, 0, $bank_trans['person_id'], $date_, '',
				$ref, "", $supp_amount, 0, 0);
//			if ($old_trans)
//				move_trans_attachments($Type, $old_trans, $trans_no);
		}
		else
		{
			$trans_no = get_next_trans_no($Type);
			$do_exchange_variance = $SysPrefs->auto_currency_revaluation();
			if ($do_exchange_variance)
				$trans_no1 = get_next_trans_no(ST_JOURNAL);
		}

		add_bank_trans($Type, $trans_no, $bank_trans['bank_act'], $ref,
			$date_, -$total_amount,
			$bank_trans['person_type_id'], $bank_trans['person_id'],
			$currency,
			"Cannot insert a source bank transaction");

		$exchanged = false;
		$total = 0;
		foreach ($cart->gl_items as $gl_item)
		{
			$is_bank_to = is_bank_account($gl_item->code_id);

			if ($Type == ST_BANKPAYMENT AND $is_bank_to)
			{
				// we don't allow payments to go to a bank account. use transfer for this !
				display_db_error("invalid payment entered. Cannot pay to another bank account", "");
			}
			// do the destination account postings
			$total += add_gl_trans($Type, $trans_no, $date_, $gl_item->code_id,
				$gl_item->dimension_id, $gl_item->dimension2_id, $gl_item->reference,
				$gl_item->amount, $currency, $bank_trans['person_type_id'], $bank_trans['person_id']);

			/*if ($is_bank_to)
            {
                add_bank_trans($Type, $trans_no, $is_bank_to, $ref,
                    $date_, $gl_item->amount,
                    $bank_trans['person_type_id'], $bank_trans['person_id'], $currency,
                    "Cannot insert a destination bank transaction");
                if ($do_exchange_variance)
                {
                    add_exchange_variation($trans_no1, $date_, $is_bank_to, $gl_item->code_id,
                        $currency, $bank_trans['person_type_id'], $bank_trans['person_id']);
                }
            }*/
			// store tax details if the gl account is a tax account

			$amount = $gl_item->amount;
			$ex_rate = get_exchange_rate_from_home_currency($currency, $date_);

			add_gl_tax_details($gl_item->code_id, $Type, $trans_no, -$amount,
				$ex_rate, $date_, $memo_);
            inactive_gl($Type, $bank_trans['trans_no']);
		}

		add_gl_trans($Type, $trans_no, $date_, $bank_gl_account, 0, 0, $memo_,
			-$total, null, $bank_trans['person_type_id'], $bank_trans['person_id']);
		if ($do_exchange_variance)
		{
			if ($exchanged || add_exchange_variation($trans_no1, $date_, $bank_trans['bank_act'], $bank_gl_account,
					$currency, $bank_trans['person_type_id'], $bank_trans['person_id']))
			{
				$ref1 = $Refs->get_next(ST_JOURNAL, null, $date_);
				$Refs->save(ST_JOURNAL, $trans_no1, $ref1);
				add_audit_trail(ST_JOURNAL, $trans_no1, $date_);
			}
		}
		add_comments($Type, $trans_no, $date_, $memo_);

		$Refs->save($Type, $trans_no, $ref);
		add_audit_trail($Type, $trans_no, $date_);

		$invoice_detail[$trans_no] = $ref;
	}
	else
		display_error("Please go back and make a batch again.");
	return $trans_no;

}
//-----------------------------------------------------------------------
function get_vouchers_from_journal($trans_type, $trans_no)
{
	$sql = "SELECT * FROM 0_gl_trans
			WHERE type = " . db_escape($trans_type) ."
			AND type_no = " . db_escape($trans_no);
	$sql .= " GROUP BY type_no ";

	$result = db_query($sql, "Error");
	return db_fetch($result);
}

//-----------------------------------------------------------------------

function get_delivery_parent_lines($trans_type)
{
	$sql = "SELECT COUNT(*) as TotalRecord 
			FROM ".TB_PREF."sales_order_details 
			WHERE trans_type = ".db_escape($trans_type);

	$query = db_query($sql, "Error");
	$result = db_fetch_row($query);
	return $result[0];
}

//-----------------------------------------------------------------------

function get_invoice_parent_lines($trans_type)
{
	$sql = "SELECT COUNT(*) as TotalRecord 
			FROM ".TB_PREF."debtor_trans_details 
			WHERE debtor_trans_type 
			IN(".db_escape($trans_type).", ".db_escape(ST_SALESINVOICE).")";
	$query = db_query($sql, "Error");
	$result = db_fetch_row($query);
	return $result[0];
}

function get_supp_invoice_items_for_get_po_detail_no($supp_trans_no)
{
	$sql = "SELECT po_detail_item_id FROM 0_supp_invoice_items 
			WHERE supp_trans_no = ".db_escape($supp_trans_no);
	$sql .= " ORDER BY id LIMIT 1";
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

function get_po_no_in_po_details($po_detail_item_id)
{
	$sql = "SELECT order_no FROM 0_purch_order_details 
			WHERE po_detail_item = ".db_escape($po_detail_item_id);
	$sql .= " ORDER BY po_detail_item LIMIT 1";
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

?>
