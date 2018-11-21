<?php

$page_security = 'SA_GLANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	List of Journal Entries
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/includes/ui/ui_view.inc");

//----------------------------------------------------------------------------------------------------

print_list_of_journal_entries();

function get_gl_transactions_itmedvision($from_date, $to_date, $trans_no=0,
							 $account=null, $dimension=0, $dimension2=0, $filter_type=null,
							 $amount_min=null, $amount_max=null, $person_id=null, $memo='',$ref='',$person_id,$cheque_no,$sub_account)
{
	global $SysPrefs,$db_connections;

	$from = date2sql($from_date);
	$to = date2sql($to_date);

	$sql = "SELECT gl.*, j.event_date, j.doc_date, a.gl_seq, u.user_id, st.supp_reference,gl.cheque,gl.memo_ as memo1,
 			gl.person_id subcode,
			IFNULL(IFNULL(sup.supp_name, debt.name), bt.person_id) as person_name, 
			IFNULL(gl.person_id, IFNULL(sup.supplier_id, debt.debtor_no)) as person_id,
			IFNULL(st.tran_date, IFNULL(dt.tran_date, IFNULL(bt.trans_date, 
			IFNULL(grn.delivery_date, gl.tran_date)))) as doc_date,
			coa.account_name, ref.reference,com.memo_,coa_types.id AS IDS 
			 FROM ".TB_PREF."gl_trans gl
			LEFT JOIN ".TB_PREF."voided v ON gl.type_no=v.id AND v.type=gl.type

			LEFT JOIN ".TB_PREF."supp_trans st ON gl.type_no=st.trans_no AND st.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=st.supplier_id)
			LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=gl.type_no AND gl.type=".ST_SUPPRECEIVE." AND gl.person_id=grn.supplier_id
			LEFT JOIN ".TB_PREF."debtor_trans dt ON gl.type_no=dt.trans_no AND dt.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=dt.debtor_no)

			LEFT JOIN ".TB_PREF."suppliers sup ON st.supplier_id=sup.supplier_id OR grn.supplier_id=sup.supplier_id
			LEFT JOIN ".TB_PREF."cust_branch branch ON dt.branch_code=branch.branch_code
			LEFT JOIN ".TB_PREF."debtors_master debt ON dt.debtor_no=debt.debtor_no

			LEFT JOIN ".TB_PREF."bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no 
			AND bt.amount!=0
			AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id

			LEFT JOIN ".TB_PREF."journal j ON j.type=gl.type AND j.trans_no=gl.type_no
			LEFT JOIN ".TB_PREF."audit_trail a ON a.type=gl.type AND a.trans_no=gl.type_no 
			AND NOT ISNULL(gl_seq)
			LEFT JOIN ".TB_PREF."users u ON a.user=u.id
			LEFT JOIN ".TB_PREF."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
			LEFT JOIN ".TB_PREF."refs ref ON ref.type=gl.type AND ref.id=gl.type_no,"
		.TB_PREF."chart_master coa,"
		.TB_PREF."chart_types coa_types
		WHERE coa.account_code=gl.account
		AND coa.account_type=coa_types.id
		AND ISNULL(v.date_)
		AND gl.tran_date >= '$from'
		AND gl.tran_date <= '$to'";
	$sql .= " AND gl.approval != 1";
	if (isset($SysPrefs->show_voided_gl_trans) && $SysPrefs->show_voided_gl_trans == 0)
		$sql .= " AND gl.amount <> 0";

	if ($trans_no > 0)
		$sql .= " AND gl.type_no LIKE ".db_escape('%'.$trans_no)."";

if ($account != null)
		$sql .= " AND gl.account = ".db_escape($account);
	if ($sub_account != '')
		$sql .= " AND coa.account_type = ".db_escape($sub_account );

    if ($person_id != null)
    {
    $type = is_subledger_account($account);
    if($type > 0)
    {
        $sql .= " AND debt.debtor_no = " . db_escape($person_id);
    }
    else
    $sql .= " AND sup.supplier_id = " . db_escape($person_id);
    }

    if ($cheque_no != '')
		$sql .= " AND gl.cheque = ".db_escape($cheque_no);

	if ($dimension > 0)
		$sql .= " AND gl.dimension_id = ".($dimension<0 ? 0 : db_escape($dimension));

	if ($dimension2 > 0)
		$sql .= " AND gl.dimension2_id = ".($dimension2<0 ? 0 : db_escape($dimension2));

	if($filter_type == 10)
    {
        $sql .= " AND gl.type= ".db_escape($filter_type);
        $sql .= " AND gl.account= ".db_escape(1200);
    }
    else 
    {
        if ($filter_type != null AND is_numeric($filter_type))
            $sql .= " AND gl.type= ".db_escape($filter_type);
    }

	if ($amount_min != null)
		$sql .= " AND ABS(gl.amount) >= ABS(".db_escape($amount_min).")";

	if ($amount_max != null)
		$sql .= " AND ABS(gl.amount) <= ABS(".db_escape($amount_max).")";

	if ($memo) {
		$sql .= " AND com.memo_ LIKE  ". db_escape("%$memo%");;
	}

	if ($ref) {
		$sql .= " AND ref.reference LIKE ". db_escape("%$ref%");
	}

	$sql .= " ORDER BY tran_date, counter";

	return db_query($sql, "The transactions for could not be retrieved");
	
}

//----------------------------------------------------------------------------------------------------

function print_list_of_journal_entries()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $systype = $_POST['PARAM_2'];
    $comments = $_POST['PARAM_3'];
	$orientation = $_POST['PARAM_4'];
	$destination = $_POST['PARAM_5'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
    $dec = user_price_dec();

    $cols = array(0, 100, 240, 300, 400, 460, 520, 580);

    $headers = array(_('Type/Account'), _('Reference').'/'._('Account Name'), _('Date/Dim.'),
    	_('Person/Item/Memo'), _('Debit'), _('Credit'));

    $aligns = array('left', 'left', 'left', 'left', 'right', 'right');

    $params =   array( 	0 => $comments,
    				    1 => array('text' => _('Period'), 'from' => $from,'to' => $to),
                    	2 => array('text' => _('Type'), 'from' => 
						$systype == -1 ? _('All') : $systypes_array[$systype],
                            'to' => ''));

    $rep = new FrontReport(_('List of Journal Entries'), "JournalEntries", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    if ($systype == -1)
        $systype = null;

    $trans = get_gl_transactions_itmedvision($from, $to, -1, null, 0, 0, $systype);

    $typeno = $type = 0;
    $debit = $credit = 0.0;
    $totdeb = $totcre = 0.0;
    while ($myrow=db_fetch($trans))
    {
        if ($type != $myrow['type'] || $typeno != $myrow['type_no'])
        {
            if ($typeno != 0)
            {
                $rep->Line($rep->row += 6);
                $rep->NewLine();
            	$rep->AmountCol(4, 5, $debit, $dec);
            	$rep->AmountCol(5, 6, abs($credit), $dec);
            	$totdeb += $debit;
            	$totcre += $credit;
            	$debit = $credit = 0.0;
				$rep->Line($rep->row -= 4);
                $rep->NewLine();
            }
            $typeno = $myrow['type_no'];
            $type = $myrow['type'];
            $TransName = $systypes_array[$myrow['type']];
            $rep->TextCol(0, 1, $TransName . " # " . $myrow['type_no']);
            $rep->TextCol(1, 2, get_reference($myrow['type'], $myrow['type_no']));
            $rep->DateCol(2, 3, $myrow['tran_date'], true);
            $coms =  get_subaccount_name($myrow["account"], $myrow["person_id"]);
            $memo = get_comments_string($myrow['type'], $myrow['type_no']);
            if ($memo != '')
            {
            	if ($coms == "")
            		$coms = $memo;
            	else
            		$coms .= " / ".$memo;
            }		
            $rep->TextColLines(3, 6, $coms);
            $rep->NewLine();
        }
        $rep->TextCol(0, 1, $myrow['account']);
        $rep->TextCol(1, 2, $myrow['account_name']);
        $dim_str = get_dimension_string($myrow['dimension_id']);
        $dim_str2 = get_dimension_string($myrow['dimension2_id']);
        if ($dim_str2 != "")
        	$dim_str .= "/".$dim_str2;
        $rep->TextCol(2, 3, $dim_str);
        $rep->TextCol(3, 4, $myrow['memo_']);
        if ($myrow['amount'] > 0.0) {
        	$debit += $myrow['amount'];
            $rep->AmountCol(4, 5, abs($myrow['amount']), $dec);
        }    
        else {
        	$credit += $myrow['amount'];
            $rep->AmountCol(5, 6, abs($myrow['amount']), $dec);
        }    
        $rep->NewLine(1, 2);
    }
	if ($typeno != 0)
	{
		$rep->Line($rep->row += 6);
		$rep->NewLine();
		$rep->AmountCol(4, 5, $debit, $dec);
		$rep->AmountCol(5, 6, abs($credit), $dec);
		$totdeb += $debit;
		$totcre += $credit;
		$rep->Line($rep->row -= 4);
		$rep->NewLine();
        $rep->TextCol(0, 4, _("Total"));
		$rep->AmountCol(4, 5, $totdeb, $dec);
		$rep->AmountCol(5, 6, abs($totcre), $dec);
		$rep->Line($rep->row -= 4);
	}
    $rep->End();
}

