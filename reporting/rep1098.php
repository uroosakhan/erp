<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------

// trial_inquiry_controls();
print_customer_balances_();

/*
function get_open_balance($debtorno, $to, $convert)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight +  t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
//    	((t.ov_amount + t.ov_gst + t.ov_freight +  t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ")) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
*/

function get_bank_account_name1097($id)
{
    $sql = "SELECT bank_name FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($id);

    $result = db_query($sql, "could not retreive bank account");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_bank_account_code1097($id)
{
    $sql = "SELECT account_code FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($id);

    $result = db_query($sql, "could not retreive bank account");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_account_code1097($id)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($id);

    $result = db_query($sql, "could not retreive bank account");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_customer_information1097($debtor_no)
{
    $sql = "SELECT * FROM `0_crm_persons` WHERE `id` IN (
	SELECT person_id FROM `0_crm_contacts` WHERE `type`='cust_branch' AND `action`='general' AND entity_id IN (
	SELECT branch_code FROM `0_cust_branch` WHERE debtor_no = '$debtor_no'))";
    $result = db_query($sql,"Error");
    return db_fetch($result);
}
function get_sql_for_sales_orders_report($dimension_id)
{
    $sql = "SELECT *
			FROM ".TB_PREF."sales_orders as sorder,".TB_PREF."sales_order_details as line
			WHERE sorder.order_no = line.order_no AND sorder.trans_type = line.trans_type
			AND sorder.trans_type = ".db_escape(ST_SALESORDER);
//    if($dimension_id != '')
    $sql.="	AND sorder.dimension_id =".db_escape($dimension_id);
    return db_query($sql, "Error");
}
function get_open_balance($debtorno, $to)
{
    if($to)
        $to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount), 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE." AND t.type <> ".ST_BANKPAYMENT.",
	    	(t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1, 0)) AS credits,
		SUM(t.alloc) AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE." OR t.type = ".ST_BANKPAYMENT.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc),
	    	((t.ov_amount + t.ov_gst + t.gst_wh + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1 + t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
        ." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
        $sql .= " AND t.tran_date < '$to'";
    $sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to,$salesgroup)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = " SELECT ".TB_PREF."debtor_trans.*,".TB_PREF."cust_branch.group_no,
		(".TB_PREF."debtor_trans.ov_amount + ".TB_PREF."debtor_trans.ov_gst + ".TB_PREF."debtor_trans.ov_freight + 
		".TB_PREF."debtor_trans.ov_freight_tax + ".TB_PREF."debtor_trans.ov_discount + ".TB_PREF."debtor_trans.gst_wh)
		AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue
    	FROM ".TB_PREF."debtor_trans,".TB_PREF."cust_branch
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.branch_code = ".TB_PREF."cust_branch.branch_code

		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY." ";

    if ($salesgroup)
        $sql .= " AND  ".TB_PREF."cust_branch.group_no = '$salesgroup'";

    $sql .= " ORDER BY ".TB_PREF."debtor_trans.tran_date";

    return db_query($sql,"No transactions were returned");
}

function get_transactions_cust_payment($dimension_id)
{
    $sql = "SELECT *
            FROM  ".TB_PREF."cust_allocations allocation
            WHERE allocation.dimension_id = ".db_escape($dimension_id);
//    $sql .= " AND allocation.trans_no_to = ".db_escape($order_no);
    $sql .= " AND allocation.trans_type_to IN(30, 0) ";
    return db_query($sql, "No transactions were returned");
}

function get_transactions_supp_payments($trans_no)
{
    $sql = "SELECT *
	    FROM " . TB_PREF . "supp_trans trans
		WHERE trans.type = ".ST_SUPPAYMENT;
    $sql .= " AND trans.trans_no = ".db_escape($trans_no);
    $sql .= " AND trans.supplier_id = ".db_escape(1); // Only HACPL supplier
    $query = db_query($sql, "No transactions were returned");
    return db_fetch($query);
}

function get_transactions_supp_payment($dimension_id)
{
    $sql = "SELECT *
            FROM  ".TB_PREF."supp_allocations allocation
            WHERE allocation.dimension_id = ".db_escape($dimension_id);
//    $sql .= " AND allocation.trans_no_to = ".db_escape($order_no);
    $sql .= " AND allocation.trans_type_to IN(18, 20) ";
    return db_query($sql, "No transactions were returned");
}

function get_transactions_cust_payments($trans_no)
{
    $sql = "SELECT *
	    FROM " . TB_PREF . "debtor_trans trans
		WHERE trans.type = ".ST_CUSTPAYMENT;
    $sql .= " AND trans.trans_no = ".db_escape($trans_no);
    $query = db_query($sql, "No transactions were returned");
    return db_fetch($query);
}

function get_customer_bank_details($type, $type_no)
{
    $sql = "SELECT * FROM ".TB_PREF."bank_trans
            WHERE type = ".db_escape($type)."
            AND trans_no = ".db_escape($type_no);
    $query = db_query($sql, "error");
    return db_fetch($query);
}

function get_cust_invoices($from, $to, $dimension_id)
{
    $sql = "SELECT *
	FROM " . TB_PREF . "debtor_trans
    WHERE " . TB_PREF . "debtor_trans.type = 10
    AND " . TB_PREF . "debtor_trans.tran_date >= '$from'
	AND " . TB_PREF . "debtor_trans.tran_date <= '$to'";
    if($dimension_id != 0)
        $sql .= " AND " . TB_PREF . "debtor_trans.dimension_id =" . db_escape($dimension_id) ." ";
//	$sql .= " GROUP BY tran_date";
    return db_query($sql, "error");
}

function get_purch_details($from, $to, $dimension_id)
{
    $sql = "SELECT " . TB_PREF . "purch_orders.* ," . TB_PREF . "purch_order_details.*
 	FROM " . TB_PREF . "purch_orders ," . TB_PREF . "purch_order_details
 	WHERE " . TB_PREF . "purch_orders.order_no =" . TB_PREF . "purch_order_details.order_no
 	AND " . TB_PREF . "purch_orders.ord_date >= '$from'
	AND " . TB_PREF . "purch_orders.ord_date <= '$to'";

    if($dimension_id != 0)
        $sql .= " AND " . TB_PREF . "purch_orders.dimension =" . db_escape($dimension_id) ." ";

    return db_query($sql, "error");
//	$rep->NewLine(-22);
}


function get_grn_details($dimension_id)
{
    $sql = "SELECT  " . TB_PREF . "purch_order_details.*," . TB_PREF . "grn_items.*,
	" . TB_PREF . "grn_batch.delivery_date AS delivery," . TB_PREF . "grn_items.text1 AS texts," . TB_PREF . "grn_items.text2 AS texts_new,
	" . TB_PREF . "purch_orders.*
	 FROM " . TB_PREF . "grn_items ," . TB_PREF . "grn_batch," . TB_PREF . "purch_orders,
	 " . TB_PREF . "purch_order_details WHERE
	  " . TB_PREF . "purch_orders.order_no =" . TB_PREF . "purch_order_details.order_no

		AND " . TB_PREF . "grn_items.po_detail_item=" . TB_PREF . "purch_order_details.po_detail_item
		AND " . TB_PREF . "grn_batch.purch_order_no=" . TB_PREF . "purch_orders.order_no
		AND " . TB_PREF . "purch_orders.dimension =" . db_escape($dimension_id);
    return db_query($sql, "error");
}

function get_colour_name($combo_code)
{
    $sql = "SELECT description FROM " . TB_PREF . "combo1 WHERE combo_code=" . db_escape($combo_code);
    $db = db_query($sql, "could not get customer");
    $ft = db_fetch($db);
    return $ft [0];
}

function get_category_id($stock_id)
{
    $sql = "SELECT category_id FROM " . TB_PREF . "stock_master WHERE stock_id=" . db_escape($stock_id);
    $db = db_query($sql, "could not get customer");
    $ft = db_fetch($db);
    return $ft [0];
}

function get_category_ids($stock_id)
{
    $sql = "SELECT description FROM " . TB_PREF . "stock_category WHERE category_id=" . db_escape($stock_id);

    $db = db_query($sql, "could not get customer");

    $ft = db_fetch($db);

    return $ft [0];
}

function get_purch_order_no($dimension)
{
    $sql = "SELECT order_no FROM " . TB_PREF . "purch_orders WHERE dimension=" . db_escape($dimension);

    $db = db_query($sql, "could not get customer");

    $ft = db_fetch($db);

    return $ft [0];
}

function get_transactions2($debtorno, $from, $to, $transno)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT " . TB_PREF . "debtor_trans.*, " . TB_PREF . "debtor_trans_details.*

	FROM " . TB_PREF . "debtor_trans, " . TB_PREF . "debtor_trans_details
	WHERE " . TB_PREF . "debtor_trans.tran_date >= '$from'
	AND " . TB_PREF . "debtor_trans.tran_date <= '$to'
	AND " . TB_PREF . "debtor_trans.debtor_no = " . db_escape($debtorno) . "
	AND " . TB_PREF . "debtor_trans_details.debtor_trans_type  =  " . TB_PREF . "debtor_trans.type
	AND " . TB_PREF . "debtor_trans_details.debtor_trans_no =  " . TB_PREF . "debtor_trans.trans_no
	AND " . TB_PREF . "debtor_trans_details.debtor_trans_no =  " . db_escape($transno) . "
	AND " . TB_PREF . "debtor_trans_details.debtor_trans_type = 10
	ORDER BY " . TB_PREF . "debtor_trans.tran_date";

    return db_query($sql, "No transactions were returned");
}


function get_hacpl_invoices($dimension_id)
{
    $sql = "SELECT trans.*
			FROM ".TB_PREF."supp_trans trans, ".TB_PREF."supp_invoice_items invoice
			WHERE invoice.supp_trans_no = trans.trans_no
			AND invoice.supp_trans_type = trans.type
			AND trans.type=" . ST_SUPPINVOICE . "
			AND invoice.dimension_id=" . db_escape($dimension_id) . "
			AND trans.supplier_id=" . db_escape(1) . " /* Only HACPL */
			GROUP BY invoice.supp_trans_no";
    return db_query($sql, "Error");
}


function get_hacpl_payment($dimension_id)
{
    $sql = " SELECT *
			FROM `0_supp_trans`
			WHERE type=" . ST_SUPPAYMENT . "
			AND dimension_id=" . db_escape($dimension_id) . "
			ORDER BY trans_no";
    return db_query($sql, "Error");
}


function get_journal_entry($dimension_id)
{
    $sql = " SELECT " . TB_PREF . "journal.*, " . TB_PREF . "journal.amount AS price," . TB_PREF . "gl_trans.*
		FROM " . TB_PREF . "gl_trans, " . TB_PREF . "journal
		WHERE " . TB_PREF . "gl_trans.type = " . ST_JOURNAL . "
		AND " . TB_PREF . "gl_trans.type = " . TB_PREF . "journal.type
		AND " . TB_PREF . "gl_trans.type_no = " . TB_PREF . "journal.trans_no
		AND  " . TB_PREF . "gl_trans.dimension_id=" . db_escape($dimension_id) . "";
    $sql .= " GROUP BY " . TB_PREF . "gl_trans.type_no ," . TB_PREF . "journal.trans_no ";
    return db_query($sql, "Error");
}


//----------------------------------------------------------------------------------------------------

function get_cust_balance($dimension_id, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT (total) as TotalAmount
            FROM ".TB_PREF."sales_orders orders
            INNER JOIN ".TB_PREF."sales_order_details details
                ON orders.order_no = details.order_no
                AND orders.trans_type = details.trans_type ";
    $sql .= " WHERE orders.ord_date >= ".db_escape($from)."
             AND orders.ord_date <= ".db_escape($to)." ";
    $sql .= " AND orders.dimension_id = ".db_escape($dimension_id);
    $sql .= " GROUP BY orders.dimension_id";
    $result = db_query($sql,"The customer details could not be retrieved");
    $customer_record = db_fetch($result);
    return $customer_record;
}
function get_dimension2_name_($dimension_id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}


//----------------------------------------------------------------------------------------------------

function print_customer_balances_()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    // $fromcust = $_POST['PARAM_2'];
    $dimension_id = $_POST['PARAM_2'];
//    $folk = $_POST['PARAM_4'];
//    $currency = $_POST['PARAM_5'];
//    $no_zeros = $_POST['PARAM_6'];
//    $comments = $_POST['PARAM_7'];
//    $orientation = $_POST['PARAM_8'];
    $destination = $_POST['PARAM_3'];


    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/blank_pdf_report.inc");

    $orientation = 'L';

//    if ($fromcust == ALL_TEXT)
//        $cust = _('All');
//    else
//        $cust = get_customer_name($fromcust);
    $dec = user_price_dec();

//	if ($area == ALL_NUMERIC)
//		$area = 0;
//
//	if ($area == 0)
//		$sarea = _('All Areas');
//	else
//		$sarea = get_area_name($area);

    if ($folk == ALL_NUMERIC)
        $folk = 0;

    if ($folk == 0)
        $salesfolk = _('All Sales Man');
    else
        $salesfolk = get_salesman_name($folk);

    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');

    $cols = array(0, 40, 85, 120, 170, 230, 285, 340, 350, 450);

    $headers = array(_('Ord.No.'), _('Reference'), _('Customer'), _('Branch'), _('Cust.Ord.Ref.'), _('Order Date'),
        _('Required By'), _('Delivery To'), _('Order Total'));

    //	$headers[7] = _('Balance');

    $aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');

//    $params =   array( 	0 => $comments,
//    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
//    				    2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
//    				    3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),
//    				    4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
//    				    5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
//						6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));
    if ($destination)
        $rep = new FrontReport(_('SALES ORDER SUMMARY'), "SALESORDERSUMMARY", user_pagesize(), 9, $orientation);
    else
        $rep = new BlankFrontReport(_('SALES ORDER SUMMARY'), "SALESORDERSUMMARY", user_pagesize(), 9, $orientation);
    if ($orientation = ($orientation ? 'L' : 'P'))
        recalculate_cols($cols);

    $rep->Font();
//    $rep->SetHeaderType('Header1097');
    $rep->Info(null, $cols, null, $aligns);
    $rep->NewPage();


//    if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
//        $rep->NewPage();
    $GetRecord = get_sql_for_sales_orders_report($dimension_id);
    $GetRecord1 = get_sql_for_sales_orders_report($dimension_id);
    $rep->Font('b');
    $rep->fontSize += 2;
    $Fetch = db_fetch($GetRecord1);
    $rep->TextCol(0, 6, "Order#            " . get_dimension2_name_($dimension_id), -2);

    $rep->TextCol(8, 11, "Booking Date            " . sql2date($Fetch['ord_date']), -2);
    $rep->NewLine();
    $rep->TextCol(8, 11, "Sales Person:            " . get_salesman_name($Fetch['salesman']), -2);
    $rep->NewLine();
    $rep->fontSize -= 2;
    $rep->fontSize += 3;
    $rep->TextCol(0, 6, "Customer Details:", -2);
    $rep->fontSize -= 3;
    $rep->NewLine(1.1);
    $rep->Font('');
///...... customer information .......///

    $information = get_customer_information1097($Fetch['debtor_no']);
    $rep->TextCol(0, 6, "Name :            " . get_customer_name($Fetch['debtor_no']), -2);
    $rep->NewLine();
    $rep->TextCol(0, 6, "Address :            " . $information['address'], -2);
    $rep->NewLine();
    $rep->TextCol(0, 6, "Phone#            " . $information['phone'], -2);
    $rep->NewLine();
    $rep->TextCol(0, 6, "Mobile#             " . $information['phone2'], -2);
    $rep->NewLine(2);
    ///......  vehicle information ...............//
    $rep->fontSize += 3;
    $rep->Font('bold');
    $rep->TextCol(0, 6, "Vehicle Order:", -2);
    $rep->Font('');
    $rep->fontSize -= 3;
    $rep->NewLine(1);
    $total_amount = 0;
    while ($vehicle = db_fetch($GetRecord)) {

        $cat_id = get_category_id($vehicle['stk_code']);
        $cat_description = get_category_ids($cat_id);
        $rep->TextCol(0, 2, $cat_description, -2);
        $rep->NewLine();
        $rep->TextCol(0, 2, $vehicle['description'], -2);
        $rep->NewLine();
        $rep->TextCol(0, 1, get_colour_name($vehicle['combo1']), -2);
//		$rep->NewLine();
        $amount = $vehicle['unit_price'];
        $rep->TextCol(8, 9, number_format2($amount), -2);
        $total_amount += $amount;
        $rep->NewLine();
    }
//    $rep->NewLine();
    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(8, 9, number_format2($total_amount));
    $rep->NewLine(2);
///.................... Customer payment ..................................//////

    $rep->fontSize += 3;
    $rep->Font('b');
    $rep->TextCol(0, 2, "Customer Payment", -2);
    $rep->NewLine(2);
    $rep->TextCol(0, 16, "Date          Instrument                                  Bank                                        Voucher            Amount", -2);
    $rep->Font('');
    $rep->fontSize -= 3;
    $rep->NewLine();
    $total_amounts = 0;
    $Outstanding = 0;
    $cust_payment = get_transactions_cust_payment($dimension_id/*, $Fetch['order_no']*/);
    while($Payment1 = db_fetch($cust_payment))
    {
        $GetPayments = get_transactions_cust_payments($Payment1['trans_no_from']);
//        while($myrow = db_fetch($GetPayments))
        {
            $bank_detail = get_customer_bank_details($GetPayments['type'], $GetPayments['trans_no']);
            $rep->TextCol(0, 1, sql2date($Payment1['date_alloc']));
            $rep->TextCol(1, 2, get_bank_account_code1097($bank_detail['bank_act']));
            $rep->TextCol(4, 5, get_bank_account_name1097($bank_detail['bank_act']));
            $rep->TextCol(6, 7, $GetPayments['reference']);
            $rep->TextCol(8, 9, number_format2($Payment1['amt']));
            $total_amounts += $Payment1['amt'];
            $Outstanding += $Payment1['amt'];
            $rep->NewLine();
        }
    }
    $rep->Font('b');
    $rep->TextCol(0, 1, "Total ");
    $rep->TextCol(8, 9, number_format2($total_amounts));
    $rep->Line($rep->row  - 4);
    $rep->Font('');
    $rep->NewLine(2);
    $rep->fontSize += 3;
//.... Sales Invoice ......///
    $rep->Font('b');
    $rep->TextCol(0, 2, "Sales Invoice", -2);
    $rep->NewLine(2);
    $rep->TextCol(0, 16, "Date                                                                                                                                          Amount", -2);
    $rep->Font('');
    $rep->fontSize -= 3;
    $rep->NewLine();
    $cust_invoice = get_cust_invoices($from, $to, $dimension_id);
    $total_amounts = 0;
    while($cust_invoices = db_fetch($cust_invoice)){
        $rep->TextCol(0, 1,	sql2date($cust_invoices['tran_date']));
        $rep->TextCol(6, 7, $cust_invoices['reference']);
        $rep->TextCol(8, 9,	number_format2($cust_invoices['ov_amount']));
        $rep->NewLine();
        $total_amounts += $cust_invoices['ov_amount'];
    }
    $rep->NewLine();
    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(8, 9, number_format2($total_amounts));
    $rep->Font('');
    $rep->NewLine(2);
    $rep->fontSize += 3;
    $rep->Font('b');
    $rep->TextCol(0, 6, "PURCHASE ORDER DETAIL", -2);
    $rep->NewLine(2);
    $rep->TextCol(0, 16, "Date                                                                                                                                          Amount", -2);
    $rep->Font('');
    $rep->fontSize -= 3;
    $rep->NewLine(1);
    $total_amounts = 0;
    $purch_details= get_purch_details($from, $to, $dimension_id);
    while($cust_purch_details = db_fetch($purch_details)){
//      $cat_id = get_category_id($cust_purch_details['item_code']);
//      $cat_description = get_category_ids($cat_id);
//		$rep->NewLine(-22);
//      $rep->TextCol(0, 2, $cat_description, -2);
//		$rep->NewLine();
        $rep->TextCol(0, 2,	sql2date($cust_purch_details['ord_date']));
//        $rep->TextCol(6, 7, $cust_purch_details['reference']);
        $price = $cust_purch_details['unit_price']*$cust_purch_details['quantity_ordered'];
        $rep->TextCol(8, 9, number_format2($price));
        $total_amounts += $price;
        $rep->NewLine();
    }
    $rep->NewLine();
    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(8, 9, number_format2($total_amounts));
    $rep->Font('');
    $rep->NewLine(2);
    //.................GRN Detail ............................../////////

    $grn_details= get_grn_details ($dimension_id);
    while($cust_grn_details = db_fetch($grn_details)){
        $rep->TextCol(0, 6,"Dispatch Date :"."            ".	sql2date($cust_grn_details['delivery']));
        $rep->NewLine();
        $rep->TextCol(0, 6,"Date :"."                           ".	sql2date($cust_grn_details['delivery']));
        $rep->NewLine();
        $rep->TextCol(0, 6,"Chasis#"."                       ".	($cust_grn_details['texts']));
        $rep->NewLine();
        $rep->TextCol(0, 6,"Engine#"."                       ".	($cust_grn_details['texts_new']));
        $rep->NewLine(1);
    }
    $rep->NewLine();
    $rep->Font('b');
    $rep->fontSize += 3;
    $rep->TextCol(0, 6,"PAYMENT TO HACPL ");
    $rep->Font('');
    $rep->NewLine(2);
    $rep->Font('b');
    $rep->TextCol(0, 16, "Date          Instrument                                  Bank                                        Voucher            Amount", -2);
    $rep->Font('');
    $rep->fontSize -= 3;

    $rep->NewLine(2);
//...........HACPL Payment ...........................////////
    $cust_payment = get_transactions_supp_payment($dimension_id);
    while($Payment1 = db_fetch($cust_payment))
    {
        $GetPayments = get_transactions_supp_payments($Payment1['trans_no_from']);
//        while($myrow = db_fetch($GetPayments))
        {
            $bank_detail = get_customer_bank_details($GetPayments['type'], $GetPayments['trans_no']);
            $rep->TextCol(0, 1, sql2date($Payment1['date_alloc']));
            $rep->TextCol(1, 2, get_bank_account_code1097($bank_detail['bank_act']));
            $rep->TextCol(4, 5, get_bank_account_name1097($bank_detail['bank_act']));
            $rep->TextCol(6, 7, $GetPayments['reference']);
            $rep->TextCol(8, 9, number_format2($Payment1['amt']));
            // $total_amounts += $Payment1['amt'];
            // $Outstanding += $Payment1['amt'];
            $rep->NewLine();
        }
    }
    $rep->NewLine(2);
    $rep->Font('b');
    $rep->fontSize += 3;
    $rep->TextCol(0, 6,"HACPL INVOICE ");
    $rep->NewLine(2);
    $rep->TextCol(0, 16,"HACPL INV#             Invoice Date                                                                                  
      Amount");
    $rep->fontSize -= 3;
    $rep->Font('');
    $rep->NewLine(2);
    //.................................... HACPL INVOICE .................................//////
    $total_amounts = 0;
    $hacpl_invoice= get_hacpl_invoices($dimension_id);
    while($hacpl_invoice_details = db_fetch($hacpl_invoice)) {
        $rep->TextCol(0, 1, $hacpl_invoice_details['reference'], -2);
        $rep->TextCol(2, 3, $hacpl_invoice_details['tran_date'], -2);
        $rep->TextCol(8, 9, number_format2($hacpl_invoice_details['ov_amount']), -2);
        $rep->NewLine();
        $total_amounts += $hacpl_invoice_details['ov_amount'];
    }
    $rep->NewLine();
    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(8, 9, number_format2($total_amounts));
    $rep->Font('');
    $rep->NewLine(2);
//......................................Journal entry .................................................//////////
    $rep->Font('b');
    $rep->fontSize += 3;
    $rep->TextCol(0, 6,"JOURNAL ENTRIES");

    $rep->NewLine(2);
    $rep->TextCol(0, 16,"Date        Comment                   Bank                                    Voucher                                   Amount ");
    $rep->fontSize -= 3;
    $rep->Font('');
    $rep->NewLine();
    $journal_entry= get_journal_entry($dimension_id);
    $total = $JournalAmount = 0;
    while($journal_entry_details = db_fetch($journal_entry)) {
        $rep->TextCol(0, 1, sql2date($journal_entry_details['tran_date']), -2);
        $rep->TextCol(1, 3, $journal_entry_details['memo_'], -2);
        $rep->TextCol(3, 5, get_account_code1097($journal_entry_details['account']), -2);
        $rep->TextCol(5, 6, $journal_entry_details['reference'], -2);
        $rep->TextCol(8, 9, $journal_entry_details['price'], -2);
        $total += $journal_entry_details['price'];
        $JournalAmount += $journal_entry_details['price'];
        $rep->NewLine();
    }
    $rep->NewLine();
    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->TextCol(0, 1, "Total");
    $rep->TextCol(8, 9, number_format2($total));
//    $rep->Font('');
//    $rep->Font('b');
    $rep->Line($rep->row  - 4);
    $rep->NewLine(2);
    $TotalInvoice = get_cust_balance($dimension_id, $from, $to);

    $CustOutstanding =  $TotalInvoice['TotalAmount'] + $JournalAmount - $Outstanding;
    $total_bal2 = 0;
//    $total_bal = $total_bal1 - $total_bal2;
    $rep->TextCol(0, 6, "Sale Order Gain/Loss");
    $rep->TextCol(8, 9, 0);
    $rep->NewLine();
    $rep->TextCol(0, 6, "Customer Outstanding");
    $rep->TextCol(8, 9, number_format2($CustOutstanding));
    $rep->NewLine();
    $rep->TextCol(0, 6, "HACPL PAYMENT");
    $rep->Font('');

    $rep->NewLine(-60);
    $rep->End();
}

?>