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
		WHERE bank_act = '$account'
		AND trans_date >= '$from'
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
function get_fund_memo_($supplier_id)
{
    $sql = "SELECT memo_ FROM 0_gl_trans WHERE type_no = '$supplier_id' AND type=4";
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
function get_gl_trans_cheque($type_no, $type)
{
    $sql = "SELECT cheque FROM ".TB_PREF."gl_trans WHERE type =12 AND type_no=$type_no AND person_type_id=2  ";
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
    // $dimension = $_POST['PARAM_3'];
    // $dimension2 = $_POST['PARAM_4'];
	$zero = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];
	$destination = $_POST['PARAM_6'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
//
//	$orientation = ( 'L' );
 	$orientation = ($orientation ? 'L' : 'P');
	
	$rep = new FrontReport(_('Bank Statement'), "BankStatement", user_pagesize(), 9, $orientation);
	$dec = user_price_dec();

	$cols = array(0, 23, 50, 118, 159, 212, 359, 422, 480);

	$aligns = array('left',	'left',	'left',	'left',	'left',	'left', 'left', 'left', 'right');

	$headers = array(_('Type'),	_('#'),	_('Reference'), _('Cheq No.'), _('Date'), _('Person/Item'),
		_('Debit'),	_('Credit'), _('Balance'));

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

	$trans = get_bank_transactions($from, $to, $account['id'],$dimension,$dimension2);

	$rows = db_num_rows($trans);
	if ($prev_balance != 0.0 || $rows != 0)
	{
		$rep->Font('bold');
		$rep->TextCol(0, 3,	$act);
		$rep->TextCol(4, 6, _('Opening Balance'));
		if ($prev_balance > 0.0)
			$rep->AmountCol(7, 8, abs($prev_balance), $dec);
		else
			$rep->AmountCol(7, 8, abs($prev_balance), $dec);
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
				$total += $myrow['amount'];
               if($myrow["type"]==1)
               {
                   $type = "BPV";
               }
               elseif($myrow["type"]==2)
               {
                   $type = "BRV";
               }
               elseif($myrow["type"]==12)
               {
                   $type = "CP";
               }
               elseif($myrow["type"]==22)
               {
                   $type = "SP";
               }
               elseif($myrow["type"]==0)
               {
                   $type = "JV";
               }
               elseif($myrow["type"]==4)
               {
                   $type = "FT";
               }
               elseif($myrow["type"]==41)
               {
                   $type = "CPV";
               }
               elseif($myrow["type"]==42)
               {
                   $type = "CRV";
               }
                $rep->TextCol(0, 1,$type);
				//$rep->TextCol(0, 1, $systypes_array[$myrow["type"]]);
				$rep->TextCol(1, 2,	$myrow['trans_no']);
				$rep->TextCol(2, 3,	$myrow['ref']);
				$rep->DateCol(4, 5,	$myrow["trans_date"], true);
			if($myrow["type"]==12)
	
		     	$rep->TextCol(3, 4,	get_gl_trans_cheque($myrow['trans_no']));
		     	else
              $rep->TextCol(3, 4,	$myrow['cheque']);
				$get_gl = get_gl_trans_1($myrow['trans_no'], $myrow["type"]);
			/*	if($get_gl["person_type_id"] == 2)
				     $name = get_customer_name($get_gl["person_id"]);*/
			/*	elseif($get_gl["person_type_id"] == 3)
				     $name = get_supplier_name_($get_gl["person_id"]);*/
            $deliver_to =get_delivery_to_name($myrow["trans_no"],$myrow["type"]);
                if ($myrow['amount'] > 0.0) {
                    $rep->AmountCol(6, 7, abs($myrow['amount']), $dec);
                    $total_debit += abs($myrow['amount']);
                } else {
                    $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
                    $total_credit += abs($myrow['amount']);
                }
			global $db_connections;
                if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'ALI')
                {
                    if ($myrow["type"] == 12 && $myrow["person_id"] == 18) {

                        $rep->TextColLines(5, 6, $deliver_to['deliver_to']);
                    } else
                        $rep->TextColLines(5, 6, get_counterparty_name($myrow["type"], $myrow["trans_no"], false));
                }
                else
                    $rep->NewLine();
                    $rep->TextCol(5, 8, get_counterparty_name($myrow["type"], $myrow["trans_no"], false));

				//$rep->TextCol(4, 5,	$name);

				$memo = get_comments_string($myrow['type'], $myrow['trans_no']);
				if ($memo != "")
				{
                   // $rep->NewLine();
				$rep->TextCol(5, 8, $memo, -2);
				}
				if($myrow['type']==4)
				$rep->TextCol(0, 8, get_fund_memo_($myrow['trans_no']), -2);
				$rep->AmountCol(8, 9, $total, $dec);
				$rep->NewLine();
				$rep->TextCol(0, 1, $deliver_to['customer_ref']);
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
		$rep->TextCol(4, 6, _("Total Debit / Credit"));
		$rep->AmountCol(6, 7, $total_debit, $dec);
		$rep->AmountCol(7, 8, $total_credit, $dec);
		$rep->NewLine(2);

		$rep->Font('bold');
		$rep->TextCol(4, 6,	_("Ending Balance"));
		if ($total > 0.0)
			$rep->AmountCol(6, 8, abs($total), $dec);
		else
			$rep->AmountCol(8, 9, abs($total), $dec);
		$rep->Font();
		$rep->Line($rep->row - $rep->lineHeight + 4);
		$rep->NewLine(2, 1);
		
		// Print the difference between starting and ending balances.
		$net_change = ($total - $prev_balance); 
		$rep->TextCol(4, 6, _("Net Change"));
		if ($total > 0.0)
			$rep->AmountCol(6, 8, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
		else
			$rep->AmountCol(8, 9, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
	}
	$rep->End();
}

