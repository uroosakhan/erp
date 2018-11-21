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

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');
    $rep = new FrontReport(_('Bank Summary Report'), "BankStatement", user_pagesize(), 9, $orientation);
    $dec = user_price_dec();

    $cols = array(4, 300, 770);

    $aligns = array('left', 'right');

    $headers = array(_('Bank Name'), _('Balances '));

    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Bank Account'), 'from' => $act, 'to' => ''));

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $sql = "SELECT * FROM " . TB_PREF . "bank_accounts";
    if ($acc != '')
        $sql .= " WHERE id=".db_escape($acc);
    $query = db_query($sql, "Error");

    while ($account = db_fetch($query)) {
        $rep->fontSize += 1;
        $rep->TextCol(0, 1, $account['bank_account_name']);

        $rep->NewLine();
        $prev_balance = get_bank_balance_to($from, $account["id"]);

        $trans = get_bank_transactions($from, $to, $account['id'], $dimension, $dimension2);

        $rows = db_num_rows($trans);
        if ($prev_balance != 0.0 || $rows != 0) {


            $total = $prev_balance;
            $total_debit = $total_credit = 0;
            if ($rows > 0) {

                while ($myrow = db_fetch($trans)) {
                    if ($zero == 0 && $myrow['amount'] == 0.0 && $myrow['amount'] == '')
                        continue;

                    $total += $myrow['amount'];

//                    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
                        $rep->LineTo($rep->leftMargin, 40.6 * $rep->lineHeight, $rep->leftMargin, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row - 2);
                        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 140, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 140, $rep->row - 2);


//                        $rep->NewPage();
//                    }

                }

//                $rep->NewLine();
            }

            $rep->LineTo($rep->leftMargin, 40.6 * $rep->lineHeight, $rep->leftMargin, $rep->row - 46.5);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row - 46.5);
            $rep->LineTo($rep->pageWidth - $rep->rightMargin - 140, 40.6 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 140, $rep->row - 46.5);
//
            if ($total > 0.0)
                $rep->AmountCol(1, 2, abs($total), $dec);
            else
                $rep->AmountCol(1, 2, abs($total), $dec);
            $rep->fontSize -= 1;
            $grand_total += $total;

            $rep->Line($rep->row - $rep->lineHeight + 2);
            $rep->NewLine(2, 1);
        }

    }
    $rep->Font('bold');
    $rep->NewLine();
    $rep->fontSize = +13;
    $rep->TextCol(0, 1, _("Total Balance"));
    if ($total > 0.0)
        $rep->AmountCol(1, 2, abs($grand_total), $dec);
    else
        $rep->AmountCol(1, 2, abs($grand_total), $dec);
    $rep->fontSize = -13;
    $rep->Line($rep->row - $rep->lineHeight + 6);
    $rep->Line($rep->row - $rep->lineHeight + 2);
    $rep->NewLine(2, 1);
    $rep->Font('');
    $rep->End();
}
