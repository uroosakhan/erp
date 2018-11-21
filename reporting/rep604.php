<?php

$page_security = 'SA_BANKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Bank Accounts Transactions
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_bank_transactions();

//----------------------------------------------------------------------------------------------------

function get_bank_balance_to($to, $account)
{
	$to = date2sql($to);
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act='$account'
	AND trans_date < '$to'";
	$result = db_query($sql, "The starting balance on hand could not be calculated");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_bank_transactions($from, $to, $account,$dimension,$dimension2)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT * FROM ".TB_PREF."bank_trans
		WHERE trans_date >= '$from'
		AND trans_date <= '$to'
		";
    if ($dimension != 0 )

    {
        $sql .= " AND ".TB_PREF."bank_trans.dimension_id=".db_escape($dimension);
    }
    if ($dimension2 != 0 )

    {
        $sql .= " AND ".TB_PREF."bank_trans.dimension2_id=".db_escape($dimension2);
    }
    if ($account != '')
    {
        $sql .= " AND ".TB_PREF."bank_trans.bank_act=".db_escape($account);
    }
    $sql .= "  ORDER BY trans_date, id " ;

	return db_query($sql,"The transactions for '$account' could not be retrieved");
}
function get_supplier_name_($supplier_id)
{
    $sql = "SELECT supp_name FROM 0_suppliers WHERE supplier_id = '$supplier_id'";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_delivery_to_name($trans_no,$type)
{
    $sql = "SELECT ".TB_PREF."sales_orders.deliver_to, ".TB_PREF."debtor_trans.reference,
    ".TB_PREF."sales_orders.customer_ref
    FROM ".TB_PREF."sales_orders,".TB_PREF."debtor_trans, ".TB_PREF."cust_allocations
    WHERE ".TB_PREF."sales_orders.order_no = ".TB_PREF."debtor_trans.order_
    AND ".TB_PREF."debtor_trans.type = 10
    AND ".TB_PREF."debtor_trans.type = ".TB_PREF."cust_allocations.trans_type_to
    AND ".TB_PREF."debtor_trans.trans_no = ".TB_PREF."cust_allocations.trans_no_to
    AND ".TB_PREF."cust_allocations.trans_no_from=".db_escape($trans_no)."
    AND ".TB_PREF."cust_allocations.trans_type_from=".db_escape($type)."
    ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch($result);
    return $fetch;
}
function get_gl_trans_1($type_no, $type)
{
    $sql = "SELECT * FROM ".TB_PREF."gl_trans
            WHERE type_no = '$type_no'
            AND type = '$type'
            AND amount > 0";
    $query = db_query($sql, 'Error');
    return db_fetch($query);
}
function print_bank_transactions()
{
    global $path_to_root, $systypes_array;

    $acc = $_POST['PARAM_0'];
    $from = $_POST['PARAM_1'];
    $to = $_POST['PARAM_2'];
    $zero = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];
    $destination = $_POST['PARAM_6'];

//    if ($acc == '') {
//        $act = _('All Banks');
//    } else {
//        $account1 = get_bank_account($acc);
//        $act = $account1['bank_account_name'] . " - " . $account1['bank_curr_code'] . " - " . $account1['bank_account_number'];
//    }

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');
    $rep = new FrontReport(_(''), "BankStatement", user_pagesize(), 9, $orientation);
    $dec = user_price_dec();

    $cols = array(4, 65, 130, 270, 450, 595, 683, 770);

    $aligns = array('left', 'left', 'left', 'left', 'right', 'right', 'right');

    $headers = array(_('Date'), _('Reference'), _('Name'), _('Memo'),
        _('Debit'), _('Credit'), _('Balance'));

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Bank Account'), 'from' => $act, 'to' => ''));

//    if ($orientation == 'L')
//    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $sql = "SELECT * FROM " . TB_PREF . "bank_accounts";
    if ($acc != '')
        $sql .= " WHERE id=".db_escape($acc);
    $query = db_query($sql, "Error");

    while ($account = db_fetch($query)) {
        $rep->fontSize += 2;
        $rep->Font('bold');
        $rep->TextCol(0, 5, $account['bank_account_name']);
        $rep->Font('');
        $rep->fontSize -= 2;
        $rep->NewLine();
        $prev_balance = get_bank_balance_to($from, $account["id"]);

        $trans = get_bank_transactions($from, $to, $account['id'], $dimension, $dimension2);

        $rows = db_num_rows($trans);
        if ($prev_balance != 0.0 || $rows != 0) {
            $rep->Font('bold');
//            $rep->TextCol(0, 3, $act);
//            $rep->NewLine();
            $rep->TextCol(3, 5, _('Opening Balance'));
            if ($prev_balance > 0.0)
                $rep->AmountCol(5, 6, abs($prev_balance), $dec);
            else
                $rep->AmountCol(5, 6, abs($prev_balance), $dec);
            $rep->Font();
            $total = $prev_balance;
            $rep->NewLine(2);
            $total_debit = $total_credit = 0;
            if ($rows > 0) {

                while ($myrow = db_fetch($trans)) {
                    $rep->Line($rep->row - 2);
                    if ($zero == 0 && $myrow['amount'] == 0.0)
                        continue;
                    $total += $myrow['amount'];

                    $rep->TextCol(1, 2, $myrow['ref']);
                    $rep->DateCol(0, 1, $myrow["trans_date"], true);

                    $get_gl = get_gl_trans_1($myrow['trans_no'], $myrow["type"]);

                    if ($myrow["person_type_id"] == 2)
                    {
                        $name = get_customer_name($myrow["person_id"]);
                        $rep->TextCol(2, 3, $name);
                    }
                    if ($myrow["person_type_id"] == 3)
                    {
                         $name = get_supplier_name_($myrow["person_id"]);
                         $rep->TextCol(2, 3, $name);
                    }
                       

                    

                    if ($myrow['amount'] > 0.0) {
                        $rep->AmountCol(4, 5, abs($myrow['amount']), $dec);
                        $total_debit += abs($myrow['amount']);
                    } else {
                        $rep->AmountCol(5, 6, abs($myrow['amount']), $dec);
                        $total_credit += abs($myrow['amount']);
                    }
                    $memo = get_comments_string($myrow['type'], $myrow['trans_no']);

                    $rep->AmountCol(6, 7, $total, $dec);
                    if ($memo != "") {
                        $rep->TextColLines(3, 4, $memo);

                    }


                    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
                        $rep->LineTo($rep->leftMargin, 40.6 * $rep->lineHeight, $rep->leftMargin, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 85, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 85, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 175, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 175, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 265, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 265, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 507, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 507, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 645, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 645, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 715, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 715, $rep->row - 2);

                        $rep->Line($rep->row - 2);

                        $rep->NewPage();
                    }

                }

                $rep->NewLine();

            }

            $rep->LineTo($rep->leftMargin, 40.6 * $rep->lineHeight, $rep->leftMargin, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 85, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 85, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 175, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 175, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 265, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 265, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 507, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 507, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 645, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 645, $rep->row - 34);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 715, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 715, $rep->row - 34);

            // Print totals for the debit and credit columns.
            $rep->Font('bold');
            $rep->AmountCol(4, 5, $total_debit, $dec);
            $rep->AmountCol(5, 6, $total_credit, $dec);


            $rep->TextCol(3, 5, _("Total Debit / Credit Ending Balance"));
            if ($total > 0.0)
                $rep->AmountCol(6, 7, abs($total), $dec);
            else
                $rep->AmountCol(6, 7, abs($total), $dec);
            $rep->Font();
            $rep->Line($rep->row - $rep->lineHeight + 4);
            $rep->NewLine(2, 1);

            // Print the difference between starting and ending balances.
            $net_change = ($total - $prev_balance);
            $rep->TextCol(3, 5, _("Net Change"));
            if ($total > 0.0)
                $rep->AmountCol(6, 7, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
            else
                $rep->AmountCol(6, 7, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
            $rep->Line($rep->row - $rep->lineHeight + 2);
            $rep->NewLine(2, 1);
        }

    }
    $rep->End();
}
