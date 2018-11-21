<?php
//----------------------------------------------------------------------------------------
// Mark changes in debtor_trans_details
//

function update_customer_trans_version_pos($type, $versions) {

	$sql= 'UPDATE '.TB_PREF. 'debtor_trans SET version=version+1
			WHERE type='.db_escape($type).' AND (';

	foreach ($versions as $trans_no=>$version)
		$where[] = 	'(trans_no='.db_escape($trans_no).' AND version='.$version.')';

		$sql .= implode(' OR ', $where) .')';

	return  db_query($sql, 'Concurrent editing conflict');
}

//----------------------------------------------------------------------------------------
// Gets document header versions for transaction set of type $type
// $trans_no = array(num1, num2,...);
// returns array(num1=>ver1, num2=>ver2...)
//
function get_customer_trans_version_pos($type, $trans_no) {
	if (!is_array($trans_no))
		$trans_no = array( $trans_no );

	$sql= 'SELECT trans_no, version FROM '.TB_PREF. 'debtor_trans
			WHERE type='.db_escape($type).' AND (';

	foreach ($trans_no as $key=>$trans)
		$trans_no[$key] = 	'trans_no='.db_escape($trans_no[$key]);

	$sql .= implode(' OR ', $trans_no) . ')';

	$res = db_query($sql, 'document version retreival');
	
	$vers = array();
	while($mysql=db_fetch($res)) {
		$vers[$mysql['trans_no']] = $mysql['version'];
	}
	return $vers;
}

//----------------------------------------------------------------------------------------
function get_max_si_no($trans_type)
{
	$sql = "SELECT MAX(`reference`) FROM ".TB_PREF."debtor_trans where `type`=".db_escape($trans_type)." ";
	$db = db_query($sql,'error');
	$ft = db_fetch($db);
	return $ft[0];
}
function _increment_new($reference, $back=false)
{
	// New method done by Pete. So f.i. WA036 will increment to WA037 and so on.
	// If $reference contains at least one group of digits,
	// extract first didgits group and add 1, then put all together.
	// NB. preg_match returns 1 if the regex matches completely
	// also $result[0] holds entire string, 1 the first captured, 2 the 2nd etc.
	//
	if (preg_match('/^(\D*?)(\d+)(.*)/', $reference, $result) == 1)
	{
		list($all, $prefix, $number, $postfix) = $result;
		$dig_count = strlen($number); // How many digits? eg. 0003 = 4
		$fmt = '%0' . $dig_count . 'd'; // Make a format string - leading zeroes
		$val = intval($number + ($back ? ($number<1 ? 0 : -1) : 1));
		$nextval =  sprintf($fmt, $val); // Add one on, and put prefix back on

		return $prefix.$nextval.$postfix;
	}
	else
		return $reference;
}
// $Total, $Tax, $Freight, $discount all in customer's currency
// date_ is display date (non-sql)
function write_customer_trans_pos($trans_type, $trans_no, $debtor_no, $BranchNo,
	$date_, $reference, $Total, $discount=0, $Tax=0, $Freight=0, $FreightTax=0,
	$sales_type=0, $order_no=0, $ship_via=0, $due_date="",
	$AllocAmt=0, $rate=0, $dimension_id=0, $dimension2_id=0, $payment_terms=null,
	$gst_wh=0,$total_discount,$ToBankAccount,$total_discount_pos,$salesman)
{

	global $Refs ;

	$new = $trans_no==0;
	$curr = get_customer_currency($debtor_no);
	if ($rate == 0)
		$rate = get_exchange_rate_from_home_currency($curr, $date_);

	$SQLDate =  date2sql($date_);
	if ($due_date == "")
		$SQLDueDate = "0000-00-00";
	else
		$SQLDueDate = date2sql($due_date);
	
	if ($trans_type == ST_BANKPAYMENT)
		$Total = -$Total;

    if ($new || !exists_customer_trans_pos($trans_type, $trans_no)) {
		if ($new)
			$trans_no = get_next_trans_no($trans_type);
//			$reference1 = get_max_si_no($trans_type);
//			$ref1 = _increment_new($reference1);
            /* if($ref1 == '')
                           {
                           	if($trans_type == 10)
                           	{
                           		$ref1 = 'SI001';
                           	}elseif($trans_type == 12)
                           	{
                           		$ref1 == 'CRV001';
                           	}elseif($trans_type == 13)
                           	{
                           		$ref1 == 'DN001';
                           	}
                           }*/
			$sql = "INSERT INTO " . TB_PREF . "debtor_trans (
		trans_no, type,
		debtor_no, branch_code,
		tran_date, due_date,
		reference, tpe,
		order_, ov_amount, ov_discount, gst_wh,
		ov_gst, ov_freight, ov_freight_tax,
		rate, ship_via, alloc,
		dimension_id, dimension2_id, payment_terms, discount1, text_3, discount2
		,salesman) VALUES (" . db_escape($trans_no) . ", " . db_escape($trans_type) . ",
		" . db_escape($debtor_no) . ", " . db_escape($BranchNo) . ",
		'$SQLDate', '$SQLDueDate', " . db_escape($reference) . ",
		" . db_escape($sales_type) . ", " . db_escape($order_no) . ", 
		$Total, " . db_escape($discount) . ", " . db_escape($gst_wh) . ", $Tax,
		" . db_escape($Freight) . ",
		$FreightTax, $rate, " . db_escape($ship_via) . ", $AllocAmt,
		" . db_escape($dimension_id) . ", " . db_escape($dimension2_id) . ", "
				. db_escape($payment_terms, true) . ","
				. db_escape($total_discount)  . ","
				. db_escape($ToBankAccount)  . ","
				. db_escape($total_discount_pos). ","
				. db_escape($salesman). ")";
		} else {    // may be optional argument should stay unchanged ?
			$sql = " " . TB_PREF . "debtor_trans SET
		debtor_no=" . db_escape($debtor_no) . " , branch_code=" . db_escape($BranchNo) . ",
		tran_date='$SQLDate', due_date='$SQLDueDate',
		reference=" . db_escape($reference) . ", tpe=" . db_escape($sales_type) . ", order_=" . db_escape($order_no) . ",
		ov_amount=$Total, 
		ov_discount=" . db_escape($discount) . ",
		gst_wh=" . db_escape($gst_wh) . ",
		ov_gst=$Tax,
		ov_freight=" . db_escape($Freight) . ", ov_freight_tax=$FreightTax, rate=$rate,
		ship_via=" . db_escape($ship_via) . ", alloc=$AllocAmt,
		dimension_id=" . db_escape($dimension_id) . ", dimension2_id=" . db_escape($dimension2_id) . ",
		payment_terms=" . db_escape($payment_terms) . ",
		salesman=" . db_escape($salesman) . "
		WHERE trans_no=" . db_escape($trans_no) . " AND type=" . db_escape($trans_type);
		}
		db_query($sql, "The debtor transaction record could not be inserted");


$date1=date("y-m-d");

		add_audit_trail($trans_type, $trans_no, $date1, $new ? '' : _("Updated."));


		return $trans_no;
//	}
}
//----------------------------------------------------------------------------------------

function get_customer_trans_pos($trans_id, $trans_type)
{
	$sql = "SELECT trans.*,"
		."ov_amount+ov_gst+ov_freight+ov_freight_tax+gst_wh+ov_discount AS Total,"
		."cust.name AS DebtorName, cust.address, "
		."cust.curr_code, "
		."cust.tax_id ";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		// Chaitanya : Added bank_act to support Customer Payment Edit
		$sql .= ",bank_act,".TB_PREF."bank_accounts.bank_name, ".TB_PREF."bank_accounts.bank_account_name,
			".TB_PREF."bank_accounts.account_type AS BankTransType ";
	}

	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper and salestype
		$sql .= ", ".TB_PREF."shippers.shipper_name, "
		.TB_PREF."sales_types.sales_type, "
		.TB_PREF."sales_types.tax_included, "
		."branch.*, "
		."cust.discount, "
		.TB_PREF."tax_groups.name AS tax_group_name, "
		.TB_PREF."tax_groups.id AS tax_group_id ";
	}

	$sql .= " FROM ".TB_PREF."debtor_trans trans LEFT JOIN ".TB_PREF."comments com ON trans.type=com.type AND trans.trans_no=com.id
					LEFT JOIN ".TB_PREF."shippers ON ".TB_PREF."shippers.shipper_id=trans.ship_via, 
					".TB_PREF."debtors_master cust";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		$sql .= ", ".TB_PREF."bank_trans, ".TB_PREF."bank_accounts";
	}

	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper, salestypes
		$sql .= ", ".TB_PREF."sales_types, "
		.TB_PREF."cust_branch branch, "
		.TB_PREF."tax_groups ";
	}

	$sql .= " WHERE trans.trans_no=".db_escape($trans_id-1)."
		AND trans.type=".db_escape($trans_type)."
		AND trans.debtor_no=cust.debtor_no";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		$sql .= " AND ".TB_PREF."bank_trans.trans_no =".db_escape($trans_id-1)."
			AND ".TB_PREF."bank_trans.type=$trans_type
			AND ".TB_PREF."bank_trans.amount != 0
			AND ".TB_PREF."bank_accounts.id=".TB_PREF."bank_trans.bank_act ";
	}
	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper
		$sql .= " AND ".TB_PREF."sales_types.id = trans.tpe
			AND branch.branch_code = trans.branch_code
			AND branch.tax_group_id = ".TB_PREF."tax_groups.id ";
	}

	$result = db_query($sql, "Cannot retreive a debtor transaction");

	if (db_num_rows($result) == 0) {
		// can't return nothing
		display_db_error("no debtor trans found for given params", $sql, true);
		exit;
	}

	if (db_num_rows($result) > 1) {
		// can't return multiple
		display_db_error("duplicate debtor transactions found for given params", $sql, true);
		exit;
	}

	$row = db_fetch($result);
	return $row;
}
//////////////////////for jogar
function get_customer_trans_invoice($trans_id, $trans_type)
{
	$sql = "SELECT trans.*,"
		."ov_amount+ov_gst+ov_freight+ov_freight_tax+gst_wh+ov_discount AS Total,"
		."cust.name AS DebtorName, cust.address, "
		."cust.curr_code, "
		."cust.tax_id ";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		// Chaitanya : Added bank_act to support Customer Payment Edit
		$sql .= ",bank_act,".TB_PREF."bank_accounts.bank_name, ".TB_PREF."bank_accounts.bank_account_name,
			".TB_PREF."bank_accounts.account_type AS BankTransType ";
	}

	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper and salestype
		$sql .= ", ".TB_PREF."shippers.shipper_name, "
			.TB_PREF."sales_types.sales_type, "
			.TB_PREF."sales_types.tax_included, "
			."branch.*, "
			."cust.discount, "
			.TB_PREF."tax_groups.name AS tax_group_name, "
			.TB_PREF."tax_groups.id AS tax_group_id ";
	}

	$sql .= " FROM ".TB_PREF."debtor_trans trans LEFT JOIN ".TB_PREF."comments com ON trans.type=com.type AND trans.trans_no=com.id
					LEFT JOIN ".TB_PREF."shippers ON ".TB_PREF."shippers.shipper_id=trans.ship_via, 
					".TB_PREF."debtors_master cust";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		$sql .= ", ".TB_PREF."bank_trans, ".TB_PREF."bank_accounts";
	}

	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper, salestypes
		$sql .= ", ".TB_PREF."sales_types, "
			.TB_PREF."cust_branch branch, "
			.TB_PREF."tax_groups  ";
	}

	$sql .= " WHERE trans.trans_no=".db_escape($trans_id)."
		AND trans.type=".db_escape($trans_type)."
		AND trans.debtor_no=cust.debtor_no";

	if ($trans_type == ST_CUSTPAYMENT) {
		// it's a payment so also get the bank account
		$sql .= " AND ".TB_PREF."bank_trans.trans_no =".db_escape($trans_id)."
			AND ".TB_PREF."bank_trans.type=$trans_type
			AND ".TB_PREF."bank_trans.amount != 0
			AND ".TB_PREF."bank_accounts.id=".TB_PREF."bank_trans.bank_act LIMIT 1";
	}
	if ($trans_type == ST_SALESINVOICE || $trans_type == ST_CUSTCREDIT || $trans_type == ST_CUSTDELIVERY) {
		// it's an invoice so also get the shipper
		$sql .= " AND ".TB_PREF."sales_types.id = trans.tpe
			AND branch.branch_code = trans.branch_code
			AND branch.tax_group_id = ".TB_PREF."tax_groups.id  ";
	}

	$result = db_query($sql, "Cannot retreive a debtor transaction");

	if (db_num_rows($result) == 0) {
		// can't return nothing
		display_db_error("no debtor trans found for given params", $sql, true);
		exit;
	}

	if (db_num_rows($result) > 1) {
		// can't return multiple
		display_db_error("duplicate debtor transactions found for given params", $sql, true);
		exit;
	}

	$row = db_fetch($result);
	return $row;
}

//----------------------------------------------------------------------------------------

function exists_customer_trans_pos($type, $type_no)
{
	$sql = "SELECT trans_no FROM ".TB_PREF."debtor_trans WHERE type=".db_escape($type)."
		AND trans_no=".db_escape($type_no);

	$result = db_query($sql, "Cannot retreive a debtor transaction");

	return (db_num_rows($result) > 0);
}

//----------------------------------------------------------------------------------------

// retreives the related sales order for a given trans

function get_customer_trans_order_pos($type, $type_no)
{
	$sql = "SELECT order_ FROM ".TB_PREF."debtor_trans WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	$result = db_query($sql, "The debtor transaction could not be queried");

	$row = db_fetch_row($result);

	return $row[0];
}

//----------------------------------------------------------------------------------------

function get_customer_details_from_trans_pos($type, $type_no)
{
	$sql = "SELECT ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."cust_branch.br_name
		FROM ".TB_PREF."debtors_master,".TB_PREF."cust_branch,".TB_PREF."debtor_trans
		WHERE ".TB_PREF."debtor_trans.type=".db_escape($type)." AND ".TB_PREF."debtor_trans.trans_no=".db_escape($type_no)."
		AND ".TB_PREF."debtors_master.debtor_no = ".TB_PREF."debtor_trans.debtor_no
		AND	".TB_PREF."cust_branch.branch_code = ".TB_PREF."debtor_trans.branch_code";

	$result = db_query($sql, "could not get customer details from trans");
	return db_fetch($result);
}

//----------------------------------------------------------------------------------------

function void_customer_trans_pos($type, $type_no)
{
	// clear all values and mark as void
	$sql = "UPDATE ".TB_PREF."debtor_trans SET ov_amount=0, ov_discount=0, gst_wh=0, ov_gst=0, ov_freight=0,
		ov_freight_tax=0, alloc=0, version=version+1 WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	db_query($sql, "could not void debtor transactions for type=$type and trans_no=$type_no");
}

//----------------------------------------------------------------------------------------

function clear_customer_trans_pos($type, $type_no)
{
	// Delete
	$sql = "DELETE FROM ".TB_PREF."debtor_trans WHERE type=".db_escape($type)." AND trans_no=".db_escape($type_no);

	db_query($sql, "could not clear debtor transactions for type=$type and trans_no=$type_no");
}
//----------------------------------------------------------------------------------------

function post_void_customer_trans_pos($type, $type_no)
{
	switch ($type) {
		case ST_SALESINVOICE :
		case ST_CUSTCREDIT   :
			void_sales_invoice_pos($type, $type_no);
			break;
		case ST_CUSTDELIVERY :
			void_sales_delivery_pos($type, $type_no);
			break;
		case ST_CUSTPAYMENT :
			void_customer_payment_pos($type, $type_no);
			break;
	}
}

//----------------------------------------------------------------------------------------

/*function get_sql_for_customer_inquiry_pos()
{
    $date_after = date2sql($_POST['TransAfterDate']);
    $date_to = date2sql($_POST['TransToDate']);

  	$sql = " SELECT
  		trans.type, 
		trans.trans_no, 
		trans.order_, 
		trans.reference,
		trans.tran_date, 
		trans.due_date, 
		debtor.name, 
		branch.br_name,
		debtor.curr_code,
		so.card_num,
		(trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount + trans.gst_wh)	AS TotalAmount, "; 
   	if ($_POST['filterType'] != ALL_TEXT)
		$sql .= "@bal := @bal+(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.ov_discount + trans.gst_wh), ";

//	else
//		$sql .= "IF(trans.type=".ST_CUSTDELIVERY.",'', IF(trans.type=".ST_SALESINVOICE." OR trans.type=".ST_BANKPAYMENT.",@bal := @bal+
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount), @bal := @bal-
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount))) , ";
		$sql .= "trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue ,
		Sum(line.quantity-line.qty_done) AS Outstanding
		
		FROM "
			.TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."debtor_trans_details as line
				ON trans.trans_no=line.debtor_trans_no AND trans.type=line.debtor_trans_type,"
			.TB_PREF."debtors_master as debtor,
			 ".TB_PREF."cust_branch as branch,
			 ".TB_PREF."sales_orders as so
			
		WHERE debtor.debtor_no = trans.debtor_no
			AND trans.tran_date >= '$date_after'
			AND trans.tran_date <= '$date_to'
			AND trans.branch_code = branch.branch_code
			AND so.order_no=  trans.order_ ";

   	if ($_POST['customer_id'] != ALL_TEXT)
   		$sql .= " AND trans.debtor_no = ".db_escape($_POST['customer_id']);

   	if ($_POST['filterType'] != ALL_TEXT)
   	{
   		if ($_POST['filterType'] == '1')
   		{
   			$sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
   		}
   		elseif ($_POST['filterType'] == '2')
   		{
   			$sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
   		}
   		elseif ($_POST['filterType'] == '3')
   		{

			$sql .= " AND (trans.type = " . ST_CUSTPAYMENT 
					." OR trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_BANKPAYMENT.") ";
   		}
   		elseif ($_POST['filterType'] == '4')
   		{
			$sql .= " AND trans.type = ".ST_CUSTCREDIT."
			           AND line.stock_id != 'DC' ";
   		}
   		elseif ($_POST['filterType'] == '5')
   		{
			$sql .= " AND trans.type = ".ST_CUSTDELIVERY." ";
   		}
		elseif ($_POST['filterType'] == '6')
   		{
			$sql .= " AND trans.type = ".ST_CUSTCREDIT." 
			          AND line.stock_id = 'DC'";
   		}

    	if ($_POST['filterType'] == '2')
    	{
    		$today =  date2sql(Today());
    		$sql .= " AND trans.due_date < '$today'
				AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax + 
				trans.ov_freight + trans.ov_discount + trans.gst_wh - trans.alloc > 0) ";
    	}
   	}
	$sql .= " GROUP BY trans.trans_no, trans.type";

   	return $sql;
}*/
function get_sql_for_customer_inquiry_pos($from, $to, $cust_id = ALL_TEXT, $filter = ALL_TEXT,$reference,$dimension)
{
    $date_after = date2sql($from);
    $date_to = date2sql($to);

    $sql = "SELECT 
  		trans.type, 
		trans.trans_no, 
		trans.order_, 
		trans.reference,
		trans.dimension_id,
		trans.tran_date, 
		trans.due_date, 
		debtor.name, 
		branch.br_name,
		debtor.curr_code,
		debtor.debtor_no,
		IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount+ trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2)	AS TotalAmount, "
        . "IF(trans.type IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT))."), -1, 1)
				*(IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount)-trans.alloc) Balance, 
		";
//   	if ($filter != ALL_TEXT)
//  		$sql .= "@bal := @bal+IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount), ";

//	else
//		$sql .= "IF(trans.type=".ST_CUSTDELIVERY.",'', IF(trans.type=".ST_SALESINVOICE." OR trans.type=".ST_BANKPAYMENT.",@bal := @bal+
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount), @bal := @bal-
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount))) , ";
    $sql .= "trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE." || trans.type = ".ST_JOURNAL.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue ,
		Sum(line.quantity-line.qty_done) AS Outstanding,
		Sum(line.qty_done) AS HasChild,
		prep_amount
		FROM "
        .TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."debtor_trans_details as line
				ON trans.trans_no=line.debtor_trans_no AND trans.type=line.debtor_trans_type
			LEFT JOIN ".TB_PREF."voided as v
				ON trans.trans_no=v.id AND trans.type=v.type
                        LEFT JOIN ".TB_PREF."audit_trail as audit ON (trans.type=audit.type AND trans.trans_no=audit.trans_no)
                        LEFT JOIN ".TB_PREF."users as user ON (audit.user=user.id)
			LEFT JOIN ".TB_PREF."cust_branch as branch ON trans.branch_code=branch.branch_code,"
        .TB_PREF."debtors_master as debtor
		WHERE debtor.debtor_no = trans.debtor_no
			AND trans.tran_date >= '$date_after'
			AND trans.tran_date <= '$date_to'"
        // exclude voided transactions and self-balancing (journal) transactions:
        . " AND ISNULL(v.date_) AND (trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
        trans.ov_discount+ trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc) != 0";

    if ($cust_id != ALL_TEXT)
        $sql .= " AND trans.debtor_no = ".db_escape($cust_id);

    if ($filter != ALL_TEXT)
    {
        if ($filter == '1')
        {
            $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
        }
        elseif ($filter == '2')
        {
            $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
        }
        elseif ($filter == '3')
        {
            $sql .= " AND (trans.type = " . ST_CUSTPAYMENT
                ." OR trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_BANKPAYMENT." OR trans.type = ".ST_CPV." OR trans.type = ".ST_CRV.") ";
        }
        elseif ($filter == '4')
        {
            $sql .= " AND trans.type = ".ST_CUSTCREDIT." ";
        }
        elseif ($filter == '5')
        {
            $sql .= " AND trans.type = ".ST_CUSTDELIVERY." ";
        }

        if ($filter == '2')
        {
            $today =  date2sql(Today());
            $sql .= " AND trans.due_date < '$today'
				AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax + 
				trans.ov_freight + trans.ov_discount+ trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.alloc > 0) ";
        }
    }
    if ($reference != '')

		//$number_like = "%".$reference."%";
	$sql .= " AND trans.reference LIKE ".db_escape( "%".$reference."%");
	
	
	if($dimension != 0)
		$sql.=" AND trans.dimension_id = ".db_escape($dimension);

    $sql .= " GROUP BY trans.trans_no, trans.type";

    return $sql;
}
function get_sql_for_customer_inquiry111111($from, $to, $cust_id = ALL_TEXT, $filter = ALL_TEXT,$reference)
{
    $date_after = date2sql($from);
    $date_to = date2sql($to);

    $sql = "SELECT 
  		trans.type, 
		trans.trans_no, 
		trans.order_, 
		trans.reference,'',
		trans.tran_date, 
		trans.due_date, 
		debtor.name, 
		branch.br_name,
		debtor.curr_code,
		debtor.debtor_no,
		IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount+ trans.gst_wh-(
trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc) - trans.discount1 - trans.discount2)	AS TotalAmount, "
        . "IF(trans.type IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT))."), -1, 1)
				*(IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc
			+ trans.ov_freight_tax + trans.ov_discount- trans.discount1 - trans.discount2)-trans.alloc) Balance, 
		";
//   	if ($filter != ALL_TEXT)
//  		$sql .= "@bal := @bal+IF(prep_amount, prep_amount, trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount), ";

//	else
//		$sql .= "IF(trans.type=".ST_CUSTDELIVERY.",'', IF(trans.type=".ST_SALESINVOICE." OR trans.type=".ST_BANKPAYMENT.",@bal := @bal+
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount), @bal := @bal-
//			(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount))) , ";
    $sql .= "trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE." || trans.type = ".ST_JOURNAL.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue ,
		Sum(line.quantity-line.qty_done) AS Outstanding,
		Sum(line.qty_done) AS HasChild,
		prep_amount
		FROM "
        .TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."debtor_trans_details as line
				ON trans.trans_no=line.debtor_trans_no AND trans.type=line.debtor_trans_type
			LEFT JOIN ".TB_PREF."voided as v
				ON trans.trans_no=v.id AND trans.type=v.type
                        LEFT JOIN ".TB_PREF."audit_trail as audit ON (trans.type=audit.type AND trans.trans_no=audit.trans_no)
                        LEFT JOIN ".TB_PREF."users as user ON (audit.user=user.id)
			LEFT JOIN ".TB_PREF."cust_branch as branch ON trans.branch_code=branch.branch_code,"
        .TB_PREF."debtors_master as debtor
		WHERE debtor.debtor_no = trans.debtor_no
			AND trans.tran_date >= '$date_after'
			AND trans.tran_date <= '$date_to'"
        // exclude voided transactions and self-balancing (journal) transactions:
        . " AND ISNULL(v.date_) AND (trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + 
        trans.ov_discount+ trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc) != 0";

    if ($cust_id != ALL_TEXT)
        $sql .= " AND trans.debtor_no = ".db_escape($cust_id);

    if ($filter != ALL_TEXT)
    {
        if ($filter == '1')
        {
            $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
        }
        elseif ($filter == '2')
        {
            $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
        }
        elseif ($filter == '3')
        {
            $sql .= " AND (trans.type = " . ST_CUSTPAYMENT
                ." OR trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_BANKPAYMENT." OR trans.type = ".ST_CPV." OR trans.type = ".ST_CRV.") ";
        }
        elseif ($filter == '4')
        {
            
            $today =  date2sql(Today());
            $sql .= " AND trans.type = ".ST_CUSTCREDIT." ";
        }
        elseif ($filter == '5')
        {
            $sql .= " AND trans.type = ".ST_CUSTDELIVERY." ";
        }

        if ($filter == '2')
        {
            $today =  date2sql(Today());
            $sql .= " AND trans.due_date < '$today'
				AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax + 
				trans.ov_freight + trans.ov_discount+ trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.alloc > 0) ";
        }
//         if ($cust_reference != '') {
// 			$sql .= " AND trans.cust_reference LIKE " . db_escape("%" . $cust_reference . "%");
// 		}
    }
   
	$sql .= " AND trans.reference LIKE ".db_escape( "%".$reference."%");

    $sql .= " GROUP BY trans.trans_no, trans.type";

    return $sql;
}
function get_sql_for_sales_deliveries_view_pos($selected_customer, $selected_stock_item=null)
{
	$sql = "SELECT trans.trans_no,
			debtor.name,
			branch.branch_code,
			branch.br_name,
			sorder.deliver_to,
			trans.reference,
			sorder.customer_ref,
			trans.tran_date,
			trans.due_date,
			(ov_amount+ov_gst+ov_freight+ov_freight_tax) AS DeliveryValue,
			debtor.curr_code,
			Sum(line.quantity-line.qty_done) AS Outstanding,
			Sum(line.qty_done) AS Done
		FROM "
		 .TB_PREF."sales_orders as sorder, "
		 .TB_PREF."debtor_trans as trans, "
		 .TB_PREF."debtor_trans_details as line, "
		 .TB_PREF."debtors_master as debtor, "
		 .TB_PREF."cust_branch as branch
			WHERE
			sorder.order_no = trans.order_ AND
			trans.debtor_no = debtor.debtor_no
				AND trans.type = ".ST_CUSTDELIVERY."
				AND line.debtor_trans_no = trans.trans_no
				AND line.debtor_trans_type = trans.type
				AND trans.branch_code = branch.branch_code
				AND trans.debtor_no = branch.debtor_no ";

	if ($_POST['OutstandingOnly'] == true) {
		 $sql .= " AND line.qty_done < line.quantity ";
	}

	//figure out the sql required from the inputs available
	if (isset($_POST['DeliveryNumber']) && $_POST['DeliveryNumber'] != "")
	{
		$delivery = "%".$_POST['DeliveryNumber'];
		$sql .= " AND trans.trans_no LIKE ".db_escape($delivery);
		$sql .= " GROUP BY trans.trans_no";
	}
	else
	{
		$sql .= " AND trans.tran_date >= '".date2sql($_POST['DeliveryAfterDate'])."'";
		$sql .= " AND trans.tran_date <= '".date2sql($_POST['DeliveryToDate'])."'";

		if ($selected_customer != -1)
			$sql .= " AND trans.debtor_no=".db_escape($selected_customer)." ";

		if (isset($selected_stock_item))
			$sql .= " AND line.stock_id=".db_escape($selected_stock_item)." ";

		if (isset($_POST['StockLocation']) && $_POST['StockLocation'] != ALL_TEXT)
			$sql .= " AND sorder.from_stk_loc = ".db_escape($_POST['StockLocation'])." ";

		$sql .= " GROUP BY trans.trans_no ";

	} //end no delivery number selected
	return $sql;
}

function get_sql_for_inquiry_pos($trans_no=0,$user,$time,$time1)
{
//display_error($user);
    if (!is_array($trans_no))
        $trans_no = array( 0=>$trans_no );

    $sql = "SELECT trans.trans_no, 
                   trans.reference,
                   masters.name, 
                   trans.tran_date, 
                   orders.order_time, 
                   orders.total, 
                   orders.discount1 as TotalDiscount, 
                   (orders.total+freight_cost-orders.discount1-orders.discount2) as TotalAmount,
                   orders.h_text1,
                   orders.h_text2,
                   trans.type 
            FROM ".TB_PREF."debtor_trans trans
            LEFT JOIN ".TB_PREF."sales_orders orders ON orders.order_no = trans.order_
            LEFT JOIN ".TB_PREF."debtors_master masters ON masters.debtor_no = trans.debtor_no
            LEFT JOIN ".TB_PREF."bank_trans bank ON bank.trans_no = trans.trans_no AND bank.type = trans.type";
    $sql .= " WHERE trans.type = ".ST_SALESINVOICE;
    if($trans_no[0] != 0) {
        $sql .= " AND ( ";
        $tr = array();
        foreach ($trans_no as $get)
            $tr[] = 'trans.trans_no='.db_escape($get);
        $sql .= implode(' OR ', $tr);
        $sql.=	")";
    } elseif ($trans_no[0] == 0) {
        $sql .= " AND trans.trans_no=0";
    }
if($user!=0)
$sql .= " AND orders.salesman=$user";

if($time==1)
$sql .= ' AND orders.order_time>'.db_escape($time1);
 elseif($time==2)
 $sql .= ' AND orders.order_time<'.db_escape($time1);
 elseif($time==3)
 $sql .= ' AND orders.order_time>='.db_escape($time1);
 elseif($time==4)
 $sql .= ' AND orders.order_time<='.db_escape($time1);

        $sql .= " ORDER BY trans_no DESC";

    return $sql;
}

//-----------------for total---------------------------//
function get_sales_total($date1,$date2, $customer_id, $cash_account,$user, $time,$time1)
{
    $date_after = date2sql($date1);
    $date_to = date2sql($date2);

    $sql = "SELECT SUM(trans.ov_amount) AS total_amount,SUM(trans.discount1+trans.discount2) As t_discount, SUM(1) As total_invoices
    FROM ".TB_PREF."debtor_trans trans
    LEFT JOIN ".TB_PREF."sales_orders orders ON orders.order_no = trans.order_
    WHERE trans.tran_date>='$date_after'
    AND trans.tran_date<='$date_to'
   AND trans.type = ".ST_SALESINVOICE;
    //$sql .= " WHERE trans.type = ".ST_SALESINVOICE;
    
     if ($customer_id != -1)
        $sql .= " AND trans.debtor_no=" . db_escape($customer_id) . " ";

    if ($cash_account != '')
        $sql .= " AND trans.text_3=" . db_escape($cash_account) . " ";
    if ($user != 0)
        $sql .= " AND trans.salesman=" . db_escape($user) . " ";
    if($time==1)
    $sql .= ' AND orders.order_time>'.db_escape($time1);
    elseif($time==2)
    $sql .= ' AND orders.order_time<'.db_escape($time1);
    elseif($time==3)
    $sql .= ' AND orders.order_time>='.db_escape($time1);
    elseif($time==4)
    $sql .= ' AND orders.order_time<='.db_escape($time1);

    $result = db_query($sql,"The customer details could not be retrieved");
    $customer_record = db_fetch($result);
    return $customer_record;
}
//------------------------------------------------------//
?>