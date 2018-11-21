<?php

$page_security = 'SA_GLREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	GL Accounts Transactions
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_GL_transactions();

//----------------------------------------------------------------------------------------------------
function get_supplier_id_704($type_no)
{
    $sql = "SELECT person_id 
    FROM ".TB_PREF."supp_allocations
    WHERE id = ".db_escape($type_no)."
    ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_invoice_ref_text1($type, $trans_no)
{

    $sql = "SELECT ".TB_PREF."debtor_trans_details.text1, ".TB_PREF."debtor_trans_details.date1, ".TB_PREF."debtor_trans_details.date2 
    FROM ".TB_PREF."debtor_trans_details, ".TB_PREF."debtor_trans
    WHERE ".TB_PREF."debtor_trans_details.debtor_trans_no = ".TB_PREF."debtor_trans.trans_no
    AND ".TB_PREF."debtor_trans.type = ".db_escape($type)."
    AND ".TB_PREF."debtor_trans.trans_no =".db_escape($trans_no);

    $result = db_query($sql, "The starting balance for ");
    $row = db_fetch($result);
    return $row;
}
function get_journal_amount($trans_no,$person_id)
{
    $sql = "SELECT `receivables_account` FROM `0_cust_branch`
 INNER JOIN 0_gl_trans ON 0_gl_trans.`person_id`=0_cust_branch.`branch_code`
WHERE 0_gl_trans.type_no=".db_escape($trans_no)."
AND 0_gl_trans.type=0
AND 0_gl_trans.person_id=".db_escape($person_id)."
AND 0_gl_trans.person_type_id=2
AND 0_gl_trans.amount <>0 
";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}

function get_dates($type_no)
{
    $sql = "SELECT date1,date2 
    FROM ".TB_PREF."debtor_trans_details
    WHERE debtor_trans_no = ".db_escape($type_no)."
    AND debtor_trans_type=11 ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch($result);
    return $fetch;
}

function get_supplier_name_704($supplier_id)
{
    $sql = "SELECT supp_name FROM ".TB_PREF."suppliers WHERE supplier_id = ".db_escape($supplier_id)."";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
function get_gl_accounts7045($from=null, $to=null, $type=null, $inactive)
{
    $sql = "SELECT coa.*, act_type.name AS AccountTypeName
		FROM "
        .TB_PREF."chart_master coa,"
        .TB_PREF."chart_types act_type,"
        .TB_PREF."cust_branch cust_br
		WHERE coa.account_type=act_type.id
		AND cust_br.receivables_account = coa.account_code
		";
    if ($from != null)
        $sql .= " AND coa.account_code >= ".db_escape($from);
    if ($to != null)
        $sql .= " AND coa.account_code <= ".db_escape($to);
    if ($type != '')
        $sql .= " AND account_type=".db_escape($type);

    if ($inactive!='')
        $sql .= " AND coa.inactive=$inactive";

    $sql .= " ORDER BY coa.account_name";

    return db_query($sql, "could not get gl accounts");
}
function get_gl_accounts7045_($from=null, $to=null, $type=null, $inactive)
{
    $sql = "SELECT coa.*, act_type.name AS AccountTypeName
		FROM "
        .TB_PREF."chart_master coa,"
        .TB_PREF."chart_types act_type
		WHERE coa.account_type=act_type.id
	
		";
    if ($from != null)
        $sql .= " AND coa.account_code >= ".db_escape($from);
    if ($to != null)
        $sql .= " AND coa.account_code <= ".db_escape($to);
    if ($type != '')
        $sql .= " AND account_type=".db_escape($type);

    if ($inactive!='')
        $sql .= " AND coa.inactive=$inactive";

    $sql .= " ORDER BY account_code";

    return db_query($sql, "could not get gl accounts");
}
function get_gl_balance_from_to7045($from_date, $to_date, $account)
{
    $from = date2sql($from_date);
    $to = date2sql($to_date);

    $sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans
		WHERE account='$account'
		";
    $sql .= " AND approval != 1";
//	if ($from_date != "")
    $sql .= "  AND tran_date > '$from'";
//	if ($to_date != "")
    $sql .= "  AND tran_date < '$to'";
    $result = db_query($sql, "The starting balance for account $account could not be calculated");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_reference7045($type, $trans_no)
{

    $sql = "SELECT `reference` FROM `0_refs` 
    WHERE type = ".db_escape($type)."
    AND id =".db_escape($trans_no);

    $result = db_query($sql, "The starting balance for ");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_gl_transactions7045($from_date, $to_date, $trans_no=0,
                             $account=null, $dimension=0, $dimension2=0, $filter_type=null,
                             $amount_min=null, $amount_max=null, $person_id=null, $memo='',$ref='',$person_id,$cheque_no,$sub_account)
{
    global $SysPrefs,$db_connections;

    $from = date2sql($from_date);
    $to = date2sql($to_date);

    $sql = "SELECT gl.cheque,(gl.amount) as amount,gl.memo_ as memo1,
 			gl.person_id subcode,
			coa.account_name, coa_types.id AS IDS, gl.person_type_id,gl.type_no,gl.person_id,gl.type,gl.tran_date
			FROM ".TB_PREF."gl_trans gl,
			"
        .TB_PREF."chart_master coa,"
        .TB_PREF."chart_types coa_types
		WHERE coa.account_code=gl.account
		AND coa.account_type=coa_types.id
		AND gl.tran_date >= '$from'
		AND gl.tran_date <= '$to'
		";
    $sql .= " AND gl.approval != 1";
    if (isset($SysPrefs->show_voided_gl_trans) && $SysPrefs->show_voided_gl_trans == 0)
        $sql .= " AND gl.amount <> 0";

    if ($person_id)
        $sql .= " AND gl.person_id=".db_escape($person_id);

    if ($trans_no > 0)
        $sql .= " AND gl.type_no LIKE ".db_escape('%'.$trans_no)."";

    if ($account != null)
        $sql .= " AND gl.account = ".db_escape($account);

    if ($sub_account != '')
        $sql .= " AND coa.account_type = ".db_escape($sub_account );

    if ($person_id != null)
        $sql .= " AND gl.person_id = ".db_escape($person_id);


    $sql .= " ORDER BY tran_date, counter";

    return db_query($sql, "The transactions for could not be retrieved");

}

function get_acc_name($account_code)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master 
    WHERE account_code=".db_escape($account_code)." ";

    $result = db_query($sql, "could not get account");

    $row = db_fetch_row($result);

    return $row[0];
}
function print_GL_transactions()
{
	global $path_to_root, $systypes_array;


	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
    $summary_only = $_POST['PARAM_2'];
	$acc = $_POST['PARAM_3'];
    $fromacc = $_POST['PARAM_4'];
	$toacc = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_7'];
    $orientation = $_POST['PARAM_8'];


	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	if($summary_only == 0)
	    $orientation =  'L' ;
	else
        $orientation =  'P' ;


    if ($fromacc == 0)
        $fromacc1 = _('All Customers');
    else
        $fromacc1 = get_acc_name($fromacc);
    if ($toacc == 0)
        $toacc1 = _('All Customers');
    else
        $toacc1 = get_acc_name($toacc);

	$rep = new FrontReport(_('SOA REPORT'), "SOAREPORT", user_pagesize(), 9, $orientation);
	$dec = user_price_dec();

if($summary_only==0)
{

$cols = array(0, 37, 190, 230, 290, 320, 350, 380, 420, 475);
$cols2 = array(0, 40, 190, 230, 320, 470, 480, 500, 580, 700);

$aligns = array('left', 'left', 'left','right',	'right',	'right',	'right',	'right',	'right', 'right');
$aligns2 = array('left', 'left', 'left','right','left',	'left',	'left',	'left',	'left',	'right');

$headers2= array(_('Date'), _('     DESCRIPTION'),_(' '),	_('       REFERENCE'),
    _('                               RECEIVABLES'), "", _(''),	_(''),	_('PAYABLES'), _('Balance'));
$headers = array(_(''), _(''), _(''),  _("INBOUND "),_(''),_('PAYMENT'),
			_(''),	_('OUTBOUND'), _('RECEIPT'), _(''));

}
else
    {
//        $rep->AliasNbPages();
//        display_error($rep->getAliasNbPages());
//        if($rep->pageNumber != $rep->getAliasNbPages())
        {
            $cols = array(0, 40, 145, 260, 300, 400, 450, 515);
            $cols2 = array(0, 40, 145, 260, 300, 400, 450, 515);
            $aligns = array('left', 'left', 'left', 'left', 'right', 'right', 'right');
            $aligns2 = array('left', 'left', 'left', 'left', 'right', 'right', 'right');
            $headers2 = array(_('S.no'), _('PARTNER NAME'), _(''), _('STATUS'), _('RECEIVABLES'), "", _('PAYABLES'));
            $headers = array(_(''), _(''), _(''), _(" "), _(''), _(''), _(''));
        }
}
if($fromacc != $toacc) {
    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Accounts'), 'from' => $fromacc, 'to' => $toacc),
        3 => array('text' => _('Customer Name'), 'from' => ($fromacc1), 'to' => ($toacc1)));
}
else
{
    $params = array(0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
        2 => array('text' => _('Accounts'), 'from' => $fromacc, 'to' => $toacc),
        3 => array('text' => _('Customer Name'), 'from' => ($fromacc1), 'to' => ''));
}

    if ($orientation == 'L')
    	recalculate_cols($cols);
    $rep->SetHeaderType('Header7045',$summary_only);
	$rep->Font();

    $rep->Info($params, $cols, $headers, $aligns, $cols2, $headers2, $aligns2);
	$rep->NewPage();

	$accounts = get_gl_accounts7045($fromacc, $toacc,$acc);
    $catt ='';
    $inbound_amt1=0;
    $inbound_amt2=0;
    $inbound_amt3=0;
    $inbound_amt4=0;
    $receipt_amt1=0;
    $outbound_amt1=0;
    $outbound_amt2=0;
    $outbound_amt3=0;
    $payment_amt1=0;
    $tot_tot=0;
    $tot_tot1=0;
    $tot_tot2=0;
    $amt=0;
    $sr = 1;

	while ($account=db_fetch($accounts))
	{
	    if (is_account_balancesheet($account["account_code"]))
			$begin = "";
	    else
	        {
	            $begin = get_fiscalyear_begin_for_date($from);
                if (date1_greater_date2($begin, $from))
                    $begin = $from;
                $begin = add_days($begin, -1);
	        }

	        $prev_balance = get_gl_balance_from_to7045($begin, $from, $account["account_code"]);
	    $trans = get_gl_transactions7045($from, $to, -1, $account['account_code'], $dimension, $dimension2,null,null,null,null,'','', '', '',$account['account_type']);
	    $trans1 = get_gl_transactions7045($from, $to, -1, $account['account_code'], $dimension, $dimension2,null,null,null,null,'','', '', '',$account['account_type']);
		$rows = db_num_rows($trans);
		if ($prev_balance == 0.0 && $rows == 0)
		    continue;
		$myrow1=db_fetch($trans1);
		if($myrow1["type"] == 10 && $account['account_code'] || $myrow1["type"] == 11 )
		{
		    $total_inbound1 += 100;
		}
		if($summary_only==0)
		{
		    $rep->Font('bold');
            $rep->NewLine(1);
//            $rep->TextCol(0, 4,	$account['account_name'], -2);
            $rep->TextCol(1, 2, _('Opening Balance'));
    // 		if ($prev_balance > 0.0)
                $rep->AmountCol(9,10, ($prev_balance), $dec);
                $tot_prev_balance += $prev_balance;

    // 		else
    // 			$rep->AmountCol(9, 10, ($prev_balance), $dec);

            $rep->Font();
            $total = $prev_balance;
            $dr_amt = $cr_amt = 0;
            $rep->NewLine(2);
            if ($rep->row < $rep->bottomMargin + (3 * $rep->lineHeight))
                $rep->NewPage();
		}

		else
		    {
		        $rep->TextCol(0, 1, $sr++, -2);
		        $rep->TextCol(1, 3, $account['account_name'], -2);

                if ($prev_balance > 0)
                {
//                    $rep->AmountCol(4, 5, ($prev_balance), $dec);
                    $tot_prev_balance1 += $prev_balance;
                }
                elseif ($prev_balance < 0)
                {
//                    $rep->AmountCol(6, 7, ($prev_balance), $dec);
                    $tot_prev_balance2 += $prev_balance;
                }
                $total = $prev_balance;
                if ($account['inactive'] == 0) {
                    $status = "ACTIVE";
                } else {
                    $status = "INACTIVE";
                }
                $rep->TextCol(3, 4, $status, -2);
                $total_recievable1 = 0;
                $inbound_amount1 = 0;
                $inbound_amount2 = 0;
                $inbound_amount3 = 0;
                $payment_amount1 = 0;
                $outbound_amount1 = 0;
                $outbound_amount2 = 0;
                $outbound_amount3 = 0;
                $receipt_amount1 = 0;
                $amount = 0;
                while ($myrow2 = db_fetch($trans))
                {

                    $jv1 = get_journal_amount($myrow2["type_no"], $myrow2['person_id']);
                    $total += $myrow2['amount'];
                    if ($myrow2["type"] == 11)
                    {
                        $inbound_amount1 += ($myrow2['amount']);
                    }
                    elseif ($myrow2["type"] == 21)
                    {
                        $outbound_amount1 += $myrow2['amount'];
                    }
                    if ($myrow2["type"] == 10)
                    {
                        $inbound_amount2 += abs($myrow2['amount']);
                    }
                    elseif ($myrow2["type"] == 22 || $myrow2["type"] == 1 || $myrow2["type"] == 41)
                    {
                        $payment_amount1 += abs($myrow2['amount']);
                    }
                    elseif ($myrow2["type"] == 20 && $myrow2['amount'] < 0.0)
                    {
                        $outbound_amount2 += abs($myrow2['amount']);
                        $amount = abs($outbound_amount2);
                    }
                    elseif ($myrow2["type"] == 12 || $myrow2["type"] == 42 || $myrow2["type"] == 2)
                    {
                        $receipt_amount1 += abs($myrow2['amount']);
                    }
                    elseif ($myrow2["type"] == 0)
                    {
                        if ($jv1 == $myrow2["account"])
                        {
                            if ($myrow2["amount"] > 0)
                            {
                                $inbound_amount3 += abs($myrow2['amount']);
                            } elseif ($myrow2["amount"] < 0)
                            {
                                $outbound_amount3 += abs($myrow2['amount']);
                            }
                        }
                    }
                    $total_recievable1 = (($inbound_amount1 + $inbound_amount2 + $inbound_amount3) - ($payment_amount1 + $outbound_amount3)) - ($receipt_amount1 - ($outbound_amount1 - $amount));
                }
//                if ($total_recievable1 > 0)
//                    $rep->AmountCol(4, 5, ($total_recievable1), $dec);
//                elseif ($total_recievable1 < 0)
//                    $rep->AmountCol(6, 7, abs($total_recievable1), $dec);
            if ($total > 0)
            {
                $rep->AmountCol(4, 5, ($total), $dec);
                if($account['inactive']==0)
                    $total_active_r +=$total;
                else
                    $total_inactive_r +=$total;

                $tot_tot1 += $total;
            }
            elseif ($total < 0)
            {
                if($account['inactive']==0)
                    $total_active_p +=$total;
                else
                    $total_inactive_p +=$total;
                $rep->AmountCol(6, 7, ($total), $dec);
                $tot_tot2 += $total;
            }
//            $rep->NewLine();
            $rep->NewLine();

        }
		    $sign = 1;


		if ($rows > 0)
		{
		    if($summary_only==0)
		    {
		        while ($myrow=db_fetch($trans))
                {
//                    $rep->LineTo(40, $rep->row - 2 ,40, 469);
                    $jv = get_journal_amount($myrow["type_no"],$myrow['person_id']);
                    $reference = get_reference7045($myrow["type"], $myrow["type_no"]);
                    $total += $myrow['amount'];
                    $reference2 = get_invoice_ref_text1($myrow["type"], $myrow["type_no"]);
                    $fno = get_comments_string($myrow["type"], $myrow["type_no"]);

                    $desc = $fno."\n"."From: ".sql2date($reference2['date1'])." To: ".sql2date($reference2['date2']);


                    if($myrow["type"] != 13 )
                    {
                        $txt = payment_person_name($myrow["person_type_id"],$myrow["person_id"], false);
                        $rep->Line($rep->row - $rep->lineHeight- 2);
                        if($myrow["type"] == 11 )
                        {
                            $dates=	get_dates($myrow['type_no']);
                            $rep->SetTextColor(255, 0, 0);
                            $rep->DateCol(0, 1,	$myrow["tran_date"], true);
                            $rep->TextCol(2, 3, $reference);
                            $rep->TextCol(3, 4, " (".(price_format($myrow['amount']*-1)).")");
                            $inbound_amt1 +=($myrow['amount']);
                            $rep->TextCol(1, 2, "From ".sql2date($dates['date1'])." to ".sql2date($dates['date2']), -2);
                            $rep->SetTextColor(0, 0, 0);
                        }
                        elseif($myrow["type"] == 21 )
                        {
                            $rep->SetTextColor(255, 0, 0);
                            $rep->DateCol(0, 1,	$myrow["tran_date"], true);
                            $rep->TextCol(2, 3, $reference);
                            $rep->TextCol(7, 8, "(".price_format($myrow['amount']).")");
                            $outbound_amt1 += $myrow['amount'];
//                            $memo = get_comments_string($myrow['type'], $myrow['type_no']);
//                            $memo = $myrow['memo_'];
//                            if ($txt != "")
//                            {
//                                if ($memo != "")
//                                    $txt = $txt."/".$memo;
//                            }
//                            else
//                                $txt = $memo;
//
//                            if ($memo != "")
//                            {
//                                $rep->TextColLines(1, 2, $memo, -2);
//                            }
                            $rep->SetTextColor(0, 0, 0);
                        }

                        else
                            {
                                $rep->DateCol(0, 1,	$myrow["tran_date"], true);
                                    
                                    if($myrow["type"] == 10 )
                                    {
                                        $rep->TextCol(2, 3,    $reference2['text1'], -2);
                                        $rep->AmountCol(3, 4, ($myrow['amount']), $dec);
                                        $inbound_amt2 +=abs($myrow['amount']);
                                    }
                                    elseif($myrow["type"] == 22 || $myrow["type"] == 1 || $myrow["type"] == 41)
                                    {
                                        $rep->TextCol(2, 3,	$reference, -2);
                                        $rep->AmountCol(5, 6, abs($myrow['amount']), $dec);
//                                        $rep->TextCol(1, 2,get_comments_string($myrow["type"],$myrow["type_no"]), $dec);
                                        $payment_amt1 +=abs($myrow['amount']);
                                    }
                                    elseif($myrow["type"] == 20  && $myrow['amount'] < 0.0 )
                                    {
                                        $rep->TextCol(2, 3,	$reference, -2);
                                        $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);

                                        $outbound_amt2 +=abs($myrow['amount']);
                                        $amt=abs($outbound_amt2);

                                    }
                                    elseif($myrow["type"] == 12 || $myrow["type"] == 42 || $myrow["type"] == 2 )
                                    {
                                        $rep->TextCol(2, 3,	$reference, -2);
                                        $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
//                                        $rep->Newline();
//                                        $rep->TextCol(1, 2,get_comments_string($myrow["type"],$myrow["type_no"]), $dec);
                                        $receipt_amt1 +=abs($myrow['amount']);
                                    }
                                    elseif($myrow["type"] == 0)
                                    {
                                        $rep->TextCol(2, 3,	$reference, -2);
                                        // if($jv == $myrow["account"])
                                        {
                                            if ($myrow["amount"] > 0)
                                            {
                                                $rep->AmountCol(3, 4, abs($myrow['amount']), $dec);
                                                $inbound_amt3 += abs($myrow['amount']);
                                            }
                                            elseif ($myrow["amount"] < 0)
                                            {
                                                $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                                                $outbound_amt3 += abs($myrow['amount']);
                                            }
                                        }
                                    }

//                                    $memo = get_comments_string($myrow['type'], $myrow['type_no']);
//                                    $memo = $myrow['memo_'];
//                                    if ($txt != "")
//                                    {
//                                        if ($memo != "")
//                                            $txt = $txt."/".$memo;
//                                    }
//                                    else
//                                        $txt = $memo;

//                                    if ($memo != "")
//                                    {
//                                        $rep->TextColLines(1, 2, $memo, -2);
//                                    }

                            }

                    }
                    $rep->TextCol(9, 10, number_format2($total, $dec));//running balance
                    if($myrow["type"] != 10 )
                    {
                        $rep->TextCol(1, 2, get_comments_string($myrow["type"], $myrow["type_no"]), $dec);
                    }
                    else{
                        $rep->TextColLines(1, 2, $desc);
                        $rep->NewLine(-1);

                    }
                    $rep->NewLine(2);
                    if ($rep->row < $rep->bottomMargin + (3 * $rep->lineHeight))
                        $rep->NewPage();
                }

                if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
                {
                    $rep->Line($rep->row -2);
                    $rep->NewPage();
                }


            }

		}
		if ($catt != $account['account_code']  )
		{
		    if($summary_only==0)
		    {
		        $rep->Font('bold');


                if($fromacc == $toacc)
                {
                    $rep->MultiCell(773, 15, _("Total"), 1, 'L', 0, 2, 40, 540, true);//S.no
                    $rep->MultiCell(60, 15, price_format(abs($inbound_amt1 + $inbound_amt2 + $inbound_amt3)), 1, 'R', 0, 2, 392, 540, true);//S.no
                    $rep->MultiCell(85, 15, price_format(abs($payment_amt1)), 1, 'R', 0, 2, 452, 540, true);//S.no
                    $rep->MultiCell(95, 15, price_format(abs($outbound_amt1 - $amt)), 1, 'R', 0, 2, 537, 540, true);//S.no
                    $rep->MultiCell(80, 15, price_format(abs($receipt_amt1 + $outbound_amt3)), 1, 'R', 0, 2, 632, 540, true);//S.no
                    $rep->MultiCell(100, 15, price_format($total), 1, 'R', 0, 2, 712, 540, true);//S.no
                }
                else
                {
                    $rep->TextCol(0, 3,	_("Total"));
                    $rep->AmountCol(3, 4, abs($inbound_amt1 + $inbound_amt2 + $inbound_amt3), $dec);
                    $rep->AmountCol(5, 6, abs($payment_amt1), $dec);
                    $rep->AmountCol(7, 8, abs($outbound_amt1-$amt), $dec);
                    $rep->AmountCol(8, 9, abs($receipt_amt1 + $outbound_amt3), $dec);
                }
//                $rep->TextCol(9, 10, number_format2($total, $dec));
                $tot_tot += $total;

                $inbound_amt1=0;
                $inbound_amt2=0;
                $inbound_amt3=0;
                $inbound_amt4=0;
                $receipt_amt1=0;
                $outbound_amt1=0;
                $outbound_amt2=0;
                $outbound_amt3=0;
                $payment_amt1=0;
                $total_payable =$outbound_amt1-$amt+$outbound_amt3+$receipt_amt1;
		    }
		}

	}

	if($summary_only==0)
    {
//        $rep->Line($rep->row  - 4);
//        $rep->NewLine(2);

        $rep->Font('bold');
//        $rep->TextCol(0, 3, _("Grand Total"));
//        $rep->TextCol(9, 10, number_format2($tot_tot, $dec));
        $rep->MultiCell(773, 19, _("Grand Total"), 0, 'L', 0, 2, 40, 557, true);//S.no
        $rep->MultiCell(100, 15, price_format($tot_tot), 0, 'R', 0, 2, 712, 557, true);//S.no

        $rep->Font('');
//        $rep->Line($rep->row  - 4);
    }
    else
    {
        $rep->Line($rep->row  - 4);
        $rep->NewLine(2);

        $rep->Font('bold');
        $rep->TextCol(0, 3, _("Grand Total"));
        $rep->TextCol(4, 5, number_format2($tot_tot1, $dec));
        $rep->TextCol(6, 7, number_format2($tot_tot2, $dec));
        $rep->Font('');
        $rep->Line($rep->row  - 4);
    }

//    $_SESSION['total_active_r'] = $total_active_r;
//    $_SESSION['total_inactive_r'] = $total_inactive_r;
//    $_SESSION['total_active_p'] = $total_active_p;
//    $_SESSION['total_inactive_p'] = $total_inactive_p;
//        $_SESSION['value1'] = $tot_tot1;
//        $_SESSION['value2'] = $tot_tot2;


    if($summary_only == 1)
    {
        $rep->NewPage();
        $rep->MultiCell(524, 25, "", 1, 'L', 0, 2, 40, 155, true);//S.no
        $rep->MultiCell(524, 25, "", 1, 'L', 0, 2, 40, 180, true);//S.no
        $rep->MultiCell(524, 15, "", 1, 'L', 0, 2, 40, 205, true);//S.no
        $rep->MultiCell(524, 15, "", 1, 'L', 0, 2, 40, 220, true);//S.no
        $rep->MultiCell(524, 15, "", 1, 'L', 0, 2, 40, 235, true);//S.no
        $rep->MultiCell(524, 15, "", 1, 'L', 0, 2, 40, 250, true);//S.no

        $rep->font('b');
        $rep->MultiCell(200, 25, "PARTNERS", 1, 'C', 0, 2, 40, 155, true);//S.no
        $rep->MultiCell(200, 25, "STATUS", 1, 'C', 0, 2, 40, 180, true);//S.no
        $rep->MultiCell(200, 15, "ACTIVE", 1, 'C', 0, 2, 40, 205, true);//S.no
        $rep->MultiCell(200, 15, "INACTIVE", 1, 'C', 0, 2, 40, 220, true);//S.no
        $rep->MultiCell(200, 15, "TOTAL", 1, 'C', 0, 2, 40, 235, true);//S.no
//
        $rep->MultiCell(324, 25, "TOTAL", 1, 'C', 0, 2, 240, 155, true);//S.no
        $rep->MultiCell(162, 25, "RECEIVABLE", 1, 'C', 0, 2, 240, 180, true);//S.no
        $rep->MultiCell(162, 25, "PAYABLE", 1, 'C', 0, 2, 402, 180, true);//S.no
        $rep->font('');
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 240, 205, true);//S.no
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 402, 205, true);//S.no
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 240, 220, true);//S.no
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 402, 220, true);//S.no
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 240, 235, true);//S.no
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 402, 235, true);//S.no
        $rep->font('b');
        $rep->MultiCell(162, 15, "NET RECEIVABLE/PAYABLE", 0, 'L', 0, 2, 240, 250, true);//S.no
        $rep->font('');
        $rep->MultiCell(162, 15, "", 1, 'R', 0, 2, 402, 250, true);//S.no



        $rep->MultiCell(162, 15, price_format($total_active_r), 1, 'R', 0, 2, 240, 205, true);//S.no
        $rep->MultiCell(162, 15, price_format($total_active_p), 1, 'R', 0, 2, 402, 205, true);//S.no
        $rep->MultiCell(162, 15, price_format($total_inactive_r), 1, 'R', 0, 2, 240, 220, true);//S.no
        $rep->MultiCell(162, 15, price_format($total_inactive_p), 1, 'R', 0, 2, 402, 220, true);//S.no
        $rep->MultiCell(162, 15, price_format($tot_tot1), 1, 'R', 0, 2, 240, 235, true);//S.no
        $rep->MultiCell(162, 15, price_format($tot_tot2), 1, 'R', 0, 2, 402, 235, true);//S.no
        $rep->MultiCell(162, 15, price_format($tot_tot1 + $tot_tot2), 1, 'R', 0, 2, 402, 250, true);//S.no




//        $rep->MultiCell(135, 15, $tot_tot1, 1, 'R', 0, 2, 120, 180, true);//S.no
//        $rep->MultiCell(135, 15, $tot_tot2, 1, 'R', 0, 2, 255, 180, true);//S.no
//        $rep->MultiCell(135, 15, $tot_tot1 + $tot_tot2, 1, 'R', 0, 2, 255, 195, true);//S.no
//        $rep->MultiCell(135, 15, $total_inactive_r, 1, 'R', 0, 2, 120, 165, true);
//        $rep->MultiCell(135, 15, $total_inactive_p, 1, 'R', 0, 2, 255, 165, true);
//        $rep->MultiCell(135, 15, $total_active_r, 1, 'R', 0, 2, 120, 150, true);//S.no
//        $rep->MultiCell(135, 15, $total_active_p, 1, 'R', 0, 2, 255, 150, true);//S.no

    }
    $rep->End();
}
