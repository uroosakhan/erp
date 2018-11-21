<?php

function add_journal_voucher($type, $trans_no, $amount, $tran_date, $currency, $reference, $source_ref='', $rate=1,
	 $event_date='', $doc_date='')
{
	$sql = "INSERT INTO ".TB_PREF."journal(
	   `type`,`trans_no`, `amount`, `currency`, `rate`, `reference`, `source_ref`, `tran_date`,
	   `event_date`, `doc_date`)
	   VALUES("
	  .db_escape($type).","
	  .db_escape($trans_no).","
	  .db_escape($amount).","
	  .db_escape($currency).","
	  .db_escape($rate).","
	  .db_escape($reference).","
	  .db_escape($source_ref).","
	  ."'".date2sql($tran_date)."',"
	  ."'".date2sql($event_date)."',"
	  ."'".date2sql($doc_date)."')";

	return db_query($sql, 'cannot add journal entry');
}

function update_journal_voucher($type, $trans_no, $amount, $tran_date, $currency, $reference, $source_ref='', $rate=1,
	 $event_date='', $doc_date='')
{
	$sql = "UPDATE ".TB_PREF."journal SET "
	  ."`amount`=".db_escape($amount).","
	  ."`currency`=".db_escape($currency).","
	  ."`rate`=".db_escape($rate).","
	  ."`reference`=".db_escape($reference).","
	  ."`source_ref`=".db_escape($source_ref).","
	  ."`tran_date`="."'".date2sql($tran_date)."',"
	  ."`event_date`="."'".date2sql($event_date)."',"
	  ."`doc_date`="."'".date2sql($doc_date)."')
	   WHERE `type`=".db_escape($type)." AND " ."`trans_no`=".db_escape($trans_no);

	return db_query($sql, 'cannot update journal entry');
}

function delete_journal_voucher($type, $trans_no)
{
	$sql = "DELETE FROM ".TB_PREF."journal 
	   WHERE `type`=".db_escape($type)." AND " ."`trans_no`=".db_escape($trans_no);

	return db_query($sql, 'cannot delete journal entry');
}

function get_journal_voucher($type, $trans_no)
{
	$sql = "SELECT * FROM ".TB_PREF."journal 
	   WHERE `type`=".db_escape($type)." AND " ."`trans_no`=".db_escape($trans_no);

	$myrow= db_query($sql, 'cannot retrieve journal entry');
	return db_fetch($myrow);
}

//
// Write headers in debtor_trans for allocation purposes.
// Amount in customer currency
//
function write_cust_journal_voucher($trans_type, $trans_no, $branch_id, $date, $ref, $amount, $rate)
{
	// we have to select any branch_id for given AR_act - in fact w
	$sql = "INSERT INTO ".TB_PREF."debtor_trans (trans_no, type, debtor_no, branch_code, tran_date, reference, ov_amount, rate)
		SELECT "
		.db_escape($trans_no).", ".db_escape($trans_type).", debtor_no,"
		.db_escape($branch_id).",'".date2sql($date)."', ".db_escape($ref).","
		.db_escape($amount).", ".db_escape($rate)."
		FROM ".TB_PREF."cust_branch WHERE branch_code=".db_escape($branch_id);

	return db_query($sql, 'cannot write cust_journal');
}

//
// Write headers in supp_trans for allocation purposes.
// Amount in supplier currency
//
function write_supp_journal_voucher($trans_type, $trans_no, $supp_id, $date, $ref, $amount, $rate, $src_ref)
{
	$sql = "INSERT INTO ".TB_PREF."supp_trans (trans_no, type, supplier_id, tran_date, reference, ov_amount, rate, supp_reference)
		VALUES (".db_escape($trans_no).", ".db_escape($trans_type).", ".db_escape($supp_id).", '".date2sql($date)."',"
		.db_escape($ref).", ".db_escape($amount).",".db_escape($rate).",".db_escape($src_ref).")";

	return db_query($sql, 'cannot write supp_journal');
}

//--------------------------------------------------------------------------------
// Write/update journal entries.
//

// function write_journal_entries_voucher($bank_trans, $GetGl, $TransTax, $debtor_trans, $supp_trans, $memo_)
// {
//     global $Refs;
//     $trans_type = ST_JOURNAL;
//     $order_id = get_next_trans_no($trans_type);
//     $trans_id = $order_id;
//     begin_transaction();
//     if(db_num_rows($GetGl) != 0)
//         while($AddGL = db_fetch($GetGl)) {
//             $ref = $Refs->get_next($trans_type, null, sql2date($AddGL['tran_date']));
//             add_gl_trans($trans_type, $trans_id, sql2date($AddGL['tran_date']), $AddGL['account'],
//                 $AddGL['dimension_id'], $AddGL['dimension2_id'],
//                 $ref, $AddGL['amount']);
//             inactive_gl(ST_JOURNAL, $AddGL['type_no']);
//         }
//     if(db_num_rows($bank_trans) != 0)
//         while($AddBT = db_fetch($bank_trans)) {
//             $ref = $Refs->get_next($trans_type, null, sql2date($AddBT['tran_date']));
//             add_bank_trans($trans_type, $trans_id, $AddBT['bank_act'], $ref,
//                 sql2date($AddBT['tran_date']), $AddBT['amount'],0,"", get_company_currency(),
//                 "Cannot insert a destination bank transaction");
//         }
//     if(db_num_rows($debtor_trans) != 0)
//         while($AddDT = db_fetch($debtor_trans)) {
//             $ref = $Refs->get_next($trans_type, null, sql2date($AddDT['tran_date']));
//             write_cust_journal($AddDT['type'], $trans_id, $AddDT['branch_code'], sql2date($AddDT['tran_date']),
//                 $ref, $AddDT['ov_amount'], $AddDT['rate'], $AddDT['dimension_id'], $AddDT['dimension2_id']);
//         }
//     if(db_num_rows($supp_trans) != 0)
//         while($AddST = db_fetch($supp_trans)) {
//             $ref = $Refs->get_next($trans_type, null, sql2date($AddST['tran_date']));
//             write_supp_journal($AddST['type'], $trans_id, $AddST['supplier_id'], sql2date($AddST['tran_date']),
//                 $ref, -$AddST['ov_amount'], $AddST['rate'], $AddST['supp_reference']);
//         }
//     if(db_num_rows($TransTax) != 0)
//         while($AddTT = db_fetch($TransTax)) {
//             add_trans_tax_details($trans_type, $trans_id, $AddTT['tax_type_id'], $AddTT['rate'], $AddTT['included_in_price'],
//                 $AddTT['amount'], $AddTT['net_amount'], $AddTT['ex_rate'], $AddTT['tran_date'], $AddTT['memo']);
//           $date_ = $AddTT['tran_date'];
//         }

//     $Refs->save($trans_type, $trans_id, $ref);
//     add_comments($trans_type, $trans_id, $date_, $memo_);
//     add_audit_trail($trans_type, $trans_id, $date_);
//     commit_transaction();
//     return $trans_id;
// }

function write_journal_entries_voucher($bank_trans, $GetGl, $TransTax, $debtor_trans, $supp_trans, $memo_, $JournalEntries)
{
    global $Refs;
    $trans_type = ST_JOURNAL;
    begin_transaction();
    if(db_num_rows($JournalEntries) != 0)
        while($AddJT = db_fetch($JournalEntries)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next($trans_type, null, sql2date($AddJT['tran_date']));
            add_journal(ST_JOURNAL, $trans_id, $AddJT['amount'], $AddJT['tran_date'], $AddJT['currency'], $ref,
                $AddJT['source_ref'], $AddJT['rate'], $AddJT['event_date'], $AddJT['doc_date']);
        }
    if(db_num_rows($GetGl) != 0)
        while($AddGL = db_fetch($GetGl)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next($trans_type, null, sql2date($AddGL['tran_date']));
            add_gl_trans($trans_type, $trans_id, sql2date($AddGL['tran_date']), $AddGL['account'],
                $AddGL['dimension_id'], $AddGL['dimension2_id'],
                $ref, $AddGL['amount']);
            inactive_gl(ST_JOURNAL, $AddGL['type_no']);
        }
    if(db_num_rows($bank_trans) != 0)
        while($AddBT = db_fetch($bank_trans)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next($trans_type, null, sql2date($AddBT['tran_date']));
            add_bank_trans($trans_type, $trans_id, $AddBT['bank_act'], $ref,
                sql2date($AddBT['tran_date']), $AddBT['amount'],0,"", get_company_currency(),
                "Cannot insert a destination bank transaction");
        }
    if(db_num_rows($debtor_trans) != 0)
        while($AddDT = db_fetch($debtor_trans)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next($trans_type, null, sql2date($AddDT['tran_date']));
            write_cust_journal($AddDT['type'], $trans_id, $AddDT['branch_code'], sql2date($AddDT['tran_date']),
                $ref, $AddDT['ov_amount'], $AddDT['rate'], $AddDT['dimension_id'], $AddDT['dimension2_id']);
        }
    if(db_num_rows($supp_trans) != 0)
        while($AddST = db_fetch($supp_trans)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next($trans_type, null, sql2date($AddST['tran_date']));
            write_supp_journal($AddST['type'], $trans_id, $AddST['supplier_id'], sql2date($AddST['tran_date']),
                $ref, -$AddST['ov_amount'], $AddST['rate'], $AddST['supp_reference']);
        }
    if(db_num_rows($TransTax) != 0)
        while($AddTT = db_fetch($TransTax)) {
            $trans_id = get_next_trans_no(ST_JOURNAL);
            add_trans_tax_details($trans_type, $trans_id, $AddTT['tax_type_id'], $AddTT['rate'], $AddTT['included_in_price'],
                $AddTT['amount'], $AddTT['net_amount'], $AddTT['ex_rate'], $AddTT['tran_date'], $AddTT['memo']);
           $date_ = $AddTT['tran_date'];
        }
    $Refs->save($trans_type, $trans_id, $ref);
    add_comments($trans_type, $trans_id, $date_, $memo_);
    add_audit_trail($trans_type, $trans_id, $date_);
    commit_transaction();
    return $trans_id;
}

//----------------------------------------------------------------------------------------

function void_journal_trans_voucher($type, $type_no, $use_transaction=true)
{
	if ($use_transaction)
		begin_transaction();

	$sql = "UPDATE ".TB_PREF."journal SET amount=0
		WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	db_query($sql, "could not void journal transaction for type=$type and trans_no=$type_no");

	void_bank_trans($type, $type_no, true);

	if ($use_transaction)
		commit_transaction();
}

function get_trans_tax_details_voucher($trans_type, $trans_no)
{
    $sql = "SELECT 0_trans_tax_details.*, 
        0_tax_types.name AS tax_type_name, 
        0_trans_tax_details.rate AS effective_rate, 
        0_tax_types.rate AS rate
		FROM 0_trans_tax_details,0_tax_types
		WHERE trans_type = ".db_escape($trans_type)."
		AND trans_no = ".db_escape($trans_no)."
		AND (net_amount != 0 OR amount != 0)
		AND 0_tax_types.id = 0_trans_tax_details.tax_type_id";

    return db_query($sql, "The transaction tax details could not be retrieved");
}

function get_customer_transaction_voucher($trans_type, $trans_no)
{
    $sql = "SELECT * FROM 0_debtor_trans
		WHERE type = ".db_escape($trans_type)."
		AND trans_no = ".db_escape($trans_no);
    return db_query($sql, "The transaction tax details could not be retrieved");
}

function get_supplier_transaction_voucher($trans_type, $trans_no)
{
    $sql = "SELECT * FROM 0_supp_trans
		WHERE type = ".db_escape($trans_type)."
		AND trans_no = ".db_escape($trans_no);
    return db_query($sql, "The transaction tax details could not be retrieved");
}

function get_journal_transaction_voucher($trans_type, $trans_no)
{
    $sql = "SELECT * FROM 0_journal
		WHERE type = ".db_escape($trans_type)."
		AND trans_no = ".db_escape($trans_no);
    return db_query($sql, "The transaction tax details could not be retrieved");
}