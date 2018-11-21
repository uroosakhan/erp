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

function get_bank_transactions($from, $to, $account)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT * FROM ".TB_PREF."bank_trans
		WHERE bank_act = '$account'
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		ORDER BY trans_date, id";

	return db_query($sql,"The transactions for '$account' could not be retrieved");
}

function get_dimension_from_gl($dimension, $dimension2, $type, $trans_no)
{

    $sql = "SELECT *
    FROM ".TB_PREF."gl_trans
    WHERE
     type =".db_escape($type)."
    AND type_no =".db_escape($trans_no)."
    ";
    if ($dimension != 0 )

    {
    $sql .= " AND ".TB_PREF."gl_trans.dimension_id=".db_escape($dimension);
    }

    if ($dimension2 != 0 )

    {
    $sql .= " AND ".TB_PREF."gl_trans.dimension2_id=".db_escape($dimension2);
    }

    $result = db_query($sql, "The starting balance on hand could not be calculated");
    $row = db_fetch($result);
    return $row;
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
    $sql = "SELECT ".TB_PREF."sales_orders.deliver_to 
    FROM ".TB_PREF."sales_orders,".TB_PREF."debtor_trans, ".TB_PREF."cust_allocations
    WHERE ".TB_PREF."sales_orders.order_no = ".TB_PREF."debtor_trans.order_
    AND ".TB_PREF."debtor_trans.order_ = ".TB_PREF."cust_allocations.trans_no_to
    AND ".TB_PREF."debtor_trans.type = ".TB_PREF."cust_allocations.trans_type_to
    AND ".TB_PREF."cust_allocations.id=".db_escape($trans_no)."
    AND ".TB_PREF."cust_allocations.trans_type_from=".db_escape($type)."
    ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
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
function get_cust_ref($debtor_no,$trans_no)
{
    $sql = "SELECT ".TB_PREF."sales_orders.customer_ref,".TB_PREF."sales_orders.deliver_to
    from ".TB_PREF."sales_orders,".TB_PREF."debtor_trans 
    where ".TB_PREF."sales_orders.order_no = ".TB_PREF."debtor_trans.order_
    AND ".TB_PREF."debtor_trans.debtor_no=".db_escape($debtor_no)."
    AND ".TB_PREF."debtor_trans.trans_no=".db_escape($trans_no);
    $db  = db_query($sql,"item prices could not be retreived");
    $ft = db_fetch($db);
    return $ft;
}
function get_dimension_name_601($dimension_id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
    WHERE id=".db_escape($dimension_id);

    $result = db_query($sql, "could not get customer");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_bank_account_601($id)
{
    $sql = "SELECT account_code FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($id);

    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function print_bank_transactions()
{
	global $path_to_root, $systypes_array;

	$acc = $_POST['PARAM_0'];
	$from = $_POST['PARAM_1'];
	$to = $_POST['PARAM_2'];
    $dimension = $_POST['PARAM_3'];
    $dimension2 = $_POST['PARAM_4'];
	$zero = $_POST['PARAM_5'];
	$comments = $_POST['PARAM_6'];
	$orientation = $_POST['PARAM_7'];
	$destination = $_POST['PARAM_8'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ('L');
	$rep = new FrontReport(_('Bank Statement'), "BankStatement", user_pagesize(), 9, $orientation);
	$dec = user_price_dec();

	$cols = array(0, 80, 100, 173, 225, 350, 400, 500, 630, 680, 760);

	$aligns = array('left',	'left',	'left',	'left',	'left',	'right',	'right', 'right', 'right', 'right');
	if($dimension == 0 && $dimension2 == 0)
                {
	$headers = array(_('Type'),	_('#'),	_('Reference'), _('Date'), _('Dimension 1'), _('Dimension 2'), _('Person/Item'),
		_('Debit'),	_('Credit'), _('Balance'));
}
else
{
    	$headers = array(_('Type'),	_('#'),	_('Reference'), _('Date'), _('Dimension 1'), _('Dimension 2'), _('Person/Item'),
		_('Debit'),	_('Credit'), _(''));
}
	$account = get_bank_account($acc);
	$act = $account['bank_account_name']." - ".$account['bank_curr_code']." - ".$account['bank_account_number'];
   	$params =   array( 	0 => $comments,
	    1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
	    2 => array('text' => _('Bank Account'),'from' => $act,'to' => ''));

//    if ($orientation == 'L')
//    	recalculate_cols($cols);
	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);
	$rep->NewPage();

	$prev_balance = get_bank_balance_to($from, $account["id"]);

	$trans = get_bank_transactions($from, $to, $account['id']);

	$rows = db_num_rows($trans);
	if ($prev_balance != 0.0 || $rows != 0)
	{
		$rep->Font('bold');
		$rep->TextCol(0, 3,	$act);
		 if($dimension == 0 && $dimension2 == 0)
                {
		$rep->TextCol(3, 5, _('Opening Balance'));
		if ($prev_balance > 0.0)
			$rep->AmountCol(7, 8, abs($prev_balance), $dec);
		else
			$rep->AmountCol(8, 9, abs($prev_balance), $dec);
                }
		$rep->Font();
		$total = $prev_balance;
		$rep->NewLine(2);
		$total_debit = $total_credit = 0;
		if ($rows > 0)
		{
			// Keep a running total as we loop through
			// the transactions.

			while ($myrow=db_fetch($trans))
			{
				if ($zero == 0 && $myrow['amount'] == 0.0)
					continue;
                if($dimension == 0 && $dimension2 == 0)
				$total += $myrow['amount'];
				$rep->TextCol(0, 1, $systypes_array[$myrow["type"]]);
				$rep->TextCol(1, 2,	$myrow['trans_no']);
				$rep->TextCol(2, 3,	$myrow['ref']);
				$rep->DateCol(3, 4,	$myrow["trans_date"], true);
				$get_gl = get_gl_trans_1($myrow['trans_no'], $myrow["type"]);
                $cust_data = get_cust_ref($myrow['person_id'],$myrow['trans_no']);
			/*	if($get_gl["person_type_id"] == 2)
				     $name = get_customer_name($get_gl["person_id"]);*/
			/*	elseif($get_gl["person_type_id"] == 3)
				     $name = get_supplier_name_($get_gl["person_id"]);*/
                $deliver_to =get_delivery_to_name($myrow["trans_no"],$myrow["type"]);
                global $db_connections;
                if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'ALI')
                {
                    if ($myrow["type"] == 12 && $myrow["person_id"] == 18) {

                        $rep->TextCol(4, 5, $deliver_to);
                    } else
                        $rep->TextCol(4, 5, get_counterparty_name($myrow["type"], $myrow["trans_no"], false));
                }
                else
                    $rep->TextCol(4, 5, get_counterparty_name($myrow["type"], $myrow["trans_no"], false));

                //$rep->TextCol(4, 5,	$name);
                if($dimension == 0 && $dimension2 == 0)
                {
                    if ($myrow['amount'] > 0.0) {
                        $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
                        $total_debit += abs($myrow['amount']);
                    } else {
                        $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                        $total_credit += abs($myrow['amount']);
                    }
                }
                else
                {
                   $amount = get_dimension_from_gl($dimension, $dimension2, $myrow["type"],$myrow["trans_no"]);
                    $total += $amount['amount'];
                   $rep->TextCol(4, 5, get_dimension_name_601($amount['dimension_id']));
                    $rep->TextCol(5, 6, get_dimension_name_601($amount['dimension2_id']));
                    if ($myrow['amount'] > 0.0)
                    {
                        $rep->AmountCol(7, 8, abs($amount['amount']), $dec);
                        $total_debit += abs($amount['amount']);
                    }
                    else
                    {
                        $rep->AmountCol(8, 9, abs($amount['amount']), $dec);
                        $total_credit += abs($amount['amount']);
                    }
                }
				$memo = get_comments_string($myrow['type'], $myrow['trans_no']);
				if ($memo != "")
				{
					$rep->NewLine();
					$rep->TextColLines(4, 5, $memo, -2);
				}
				if($dimension == 0 && $dimension2 == 0)
                {
				$rep->AmountCol(9, 10, abs($total), $dec);
                }
				$rep->NewLine();
				if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
				{
					$rep->Line($rep->row - 2);
					$rep->NewPage();
				}
			}
			$rep->NewLine();
		}
		
		// Print totals for the debit and credit columns.
		$rep->TextCol(3, 5, _("Total Debit / Credit"));
		$rep->AmountCol(7, 8, $total_debit, $dec);
		$rep->AmountCol(8, 9, $total_credit, $dec);
		$rep->NewLine(2);
        if($dimension == 0 && $dimension2 == 0)
        {
		$rep->Font('bold');
		$rep->TextCol(3, 5,	_("Ending Balance"));
		if ($total > 0.0)
			$rep->AmountCol(7, 8, abs($total), $dec);
		else
			$rep->AmountCol(8, 9, abs($total), $dec);
		$rep->Font();
        }
		$rep->Line($rep->row - $rep->lineHeight + 4);
		$rep->NewLine(2, 1);
		
		// Print the difference between starting and ending balances.
		$net_change = ($total - $prev_balance); 
		if($dimension == 0 && $dimension2 == 0)
        {
		$rep->TextCol(3, 5, _("Net Change"));
		if ($total > 0.0)
			$rep->AmountCol(7, 8, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
		else
			$rep->AmountCol(8, 9, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
        }   
	}
	$rep->End();
}

