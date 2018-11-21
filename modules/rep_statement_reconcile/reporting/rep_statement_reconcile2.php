<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
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
function get_bank_balance_to_reconsile($to, $account)
{
	$to = date2sql($to);
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act='$account'
	AND trans_date < '$to' ";
	$result = db_query($sql, "The starting balance on hand could not be calculated");
	$row = db_fetch_row($result);
	return $row[0];
}

function get_bank_balance_to_no_reconsile($to, $account)
{
	$to = date2sql($to);
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act='$account'
	AND trans_date < '$to' AND reconciled=''";
	$result = db_query($sql, "The starting balance on hand could not be calculated");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_bank_balance_to($to, $account)
{
	$to = date2sql($to);
	$sql = "SELECT SUM(amount) FROM ".TB_PREF."bank_trans WHERE bank_act='$account'
	AND trans_date < '$to'";
	$result = db_query($sql, "The starting balance on hand could not be calculated");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_bank_transactions_reconsile($from, $to, $account)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT ".TB_PREF."bank_trans.*, ".TB_PREF."comments.memo_
			FROM ".TB_PREF."bank_trans LEFT JOIN ".TB_PREF."comments ON 
			(".TB_PREF."bank_trans.type = ".TB_PREF."comments.type
			AND ".TB_PREF."bank_trans.trans_no = ".TB_PREF."comments.id)
		WHERE ".TB_PREF."bank_trans.bank_act = '$account'
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		
		
		ORDER BY trans_date,".TB_PREF."bank_trans.id";

	return db_query($sql,"The transactions for '$account' could not be retrieved");
}
function get_bank_transactions_no_reconsile($from, $to, $account)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT ".TB_PREF."bank_trans.*, ".TB_PREF."comments.memo_
			FROM ".TB_PREF."bank_trans LEFT JOIN ".TB_PREF."comments ON 
			(".TB_PREF."bank_trans.type = ".TB_PREF."comments.type
			AND ".TB_PREF."bank_trans.trans_no = ".TB_PREF."comments.id)
		WHERE ".TB_PREF."bank_trans.bank_act = '$account'
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		
		
		ORDER BY trans_date,".TB_PREF."bank_trans.id";

	return db_query($sql,"The transactions for '$account' could not be retrieved");
}
function get_bank_transactions($from, $to, $account)
{
	$from = date2sql($from);
	$to = date2sql($to);
	$sql = "SELECT ".TB_PREF."bank_trans.*, ".TB_PREF."comments.memo_
			FROM ".TB_PREF."bank_trans LEFT JOIN ".TB_PREF."comments ON 
			(".TB_PREF."bank_trans.type = ".TB_PREF."comments.type
			AND ".TB_PREF."bank_trans.trans_no = ".TB_PREF."comments.id)
		WHERE ".TB_PREF."bank_trans.bank_act = '$account'
		AND trans_date >= '$from'
		AND trans_date <= '$to'
		ORDER BY trans_date,".TB_PREF."bank_trans.id";

	return db_query($sql,"The transactions for '$account' could not be retrieved");
}

function print_bank_transactions()
{
	global $path_to_root, $systypes_array;

	$acc = $_POST['PARAM_0'];
	$from = $_POST['PARAM_1'];
	$to = $_POST['PARAM_2'];
	$comments = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	$no_zero = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$rep = new FrontReport(_('Bank Statement'), "BankStatement", user_pagesize(), 9, "L");
	$dec = user_price_dec();

	$cols = array(0, 60, 80, 150, 225, 285, 400, 480, 550, 620, 720);

	$aligns = array('left',	'left',	'left',	'left',	'left','left',	'right', 'right', 'right', 'center', 'left');

	$headers = array(_('Date'),	_('#'),	_('Reference'), _('Cheq.#'),_('Cheq.Date'), _('Person/Item'),
		_('Debit'),	_('Credit'), _('Balance'), _('Reco Date'), _('Narration'));

	$account = get_bank_account($acc);
	$act = $account['bank_account_name']." - ".$account['bank_curr_code']." - ".$account['bank_account_number'];
   	$params =   array( 	0 => $comments,
	    1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
	    2 => array('text' => _('Bank Account'),'from' => $act,'to' => ''));

	$rep->Font();
	$rep->Info($params, $cols, $headers, $aligns);
	$rep->NewPage();

if($no_zero==1)
{
	$prev_balance = get_bank_balance_to_reconsile($from, $account["id"]);
	$trans = get_bank_transactions_reconsile($from, $to, $account['id']);
}
elseif($no_zero==2)
{
	$prev_balance = get_bank_balance_to_no_reconsile($from, $account["id"]);
	$trans = get_bank_transactions_no_reconsile($from, $to, $account['id']);
}
else
{
$prev_balance = get_bank_balance_to($from, $account["id"]);
$trans = get_bank_transactions($from, $to, $account['id']);
}

	$rows = db_num_rows($trans);
	if ($prev_balance != 0.0 || $rows != 0)
	{
		$rep->Font('bold');
		$rep->TextCol(0, 3,	$act);
		$rep->TextCol(3, 5, _('Opening Balance'));
		if ($prev_balance > 0.0)
			$rep->AmountCol(5, 6, abs($prev_balance), $dec);
		else
			$rep->AmountCol(6, 7, abs($prev_balance), $dec);
		$rep->Font();
		$total = $prev_balance;
		$rep->NewLine(2);
		if ($rows > 0)
		{
			// Keep a running total as we loop through
			// the transactions.
			$total_debit = $total_credit = 0;			
			
			while ($myrow=db_fetch($trans))
			{
			    
			    if($myrow['amount'] == 0)continue;
			    
if($no_zero ==1  && $myrow["reconciled"] == '' || $no_zero ==2  && $myrow["reconciled"] != ''  )continue;

				$total += $myrow['amount'];

				$rep->DateCol(0, 1, $myrow["trans_date"],true);
				$rep->TextCol(1, 2,	$myrow['trans_no']);
				$rep->TextCol(2, 3,	$myrow['ref']);
				$rep->TextCol(3, 4,	$myrow["cheque"]);
					$rep->DateCol(4, 5,	$myrow["cheque_date"], true);
				$rep->TextCol(5, 6,	payment_person_name($myrow["person_type_id"],$myrow["person_id"], false));
				
				if ($myrow['amount'] > 0.0)
				{
					$rep->AmountCol(6, 7, abs($myrow['amount']), $dec);
					$total_debit += abs($myrow['amount']);
				}
				else
				{
					$rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
					$total_credit += abs($myrow['amount']);
				}
				$rep->AmountCol(8, 9, $total, $dec);
				if ($myrow["reconciled"] && $myrow["reconciled"] != '0000-00-00')
					$rep->DateCol(9, 10,	$myrow["reconciled"], true);
				$rep->TextCol(10, 11, $myrow['memo_']);
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
		$rep->AmountCol(6, 7, $total_debit, $dec);
		$rep->AmountCol(7,8, $total_credit, $dec);
		$rep->NewLine(2);

		$rep->Font('bold');
		$rep->TextCol(3, 5,	_("Ending Balance"));
		if ($total > 0.0)
			$rep->AmountCol(6,7, abs($total), $dec);
		else
			$rep->AmountCol(7, 8, abs($total), $dec);
		$rep->Font();
		$rep->NewLine(2);	
		
		// Print the difference between starting and ending balances.
		$net_change = ($total - $prev_balance); 
		$rep->TextCol(3, 5, _("Net Change"));
		if ($total > 0.0)
			$rep->AmountCol(6, 7, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
		else
			$rep->AmountCol(7, 8, $net_change, $dec, 0, 0, 0, 0, null, 1, True);
		$rep->Font();
		$rep->NewLine(2);	
		
		// Calculate Bank Balance as per reco
		$date = date2sql($to);
		$sql = "SELECT SUM(IF(reconciled<='$date' AND reconciled !='0000-00-00', amount, 0)) as reconciled,
				 SUM(amount) as books_total
			FROM ".TB_PREF."bank_trans trans
			WHERE bank_act=".db_escape($account['id'])."
			AND trans_date <= '$date'";	
			
		//	." AND trans.reconciled IS NOT NULL";
		//display_notification($sql);
		$t_result = db_query($sql,"Cannot retrieve reconciliation data");

		if ($t_row = db_fetch($t_result)) {
			$books_total = $t_row['books_total'];
			$reconciled = $t_row['reconciled'];
		}			
		$difference = $books_total - $reconciled;		
		
		// Bank Balance (by Reco)
		$rep->Font('bold');
		$rep->TextCol(3, 5,	_("Bank Balance"));
		if ($reconciled > 0.0)
			$rep->AmountCol(6, 7, abs($reconciled), $dec);
		else
			$rep->AmountCol(7, 8, abs($reconciled), $dec);
		$rep->Font();
		$rep->NewLine(2);	

		// Reco Difference
		$rep->Font('bold');
		$rep->TextCol(3, 5,	_("Difference"));
		if ($difference > 0.0)
			$rep->AmountCol(5, 6, abs($difference), $dec);
		else
			$rep->AmountCol(6, 7, abs($difference), $dec);
		$rep->Font();
		$rep->NewLine(2);	
			
		$rep->Line($rep->row - $rep->lineHeight + 4);
		$rep->NewLine(2, 1);			
			
	}
	$rep->End();
}

