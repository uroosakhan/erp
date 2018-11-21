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
function get_gl_transactions_704($from_date, $to_date, $trans_no=0,
                             $account=null, $dimension=0, $dimension2=0, $filter_type=null,
                             $amount_min=null, $amount_max=null, $person_id=null, $memo='',$ref='',
                             $person_id,$cheque_no,$sub_account,$voided)
{
    global $SysPrefs,$db_connections;

    $from = date2sql($from_date);
    $to = date2sql($to_date);

    $sql = "SELECT gl.*, j.event_date, j.doc_date, a.gl_seq, u.user_id, st.supp_reference,gl.cheque,gl.text_1,gl.memo_ as memo1,
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
		AND gl.tran_date >= '$from'
		AND gl.tran_date <= '$to'";
    $sql .= " AND gl.approval != 1";
    if (isset($SysPrefs->show_voided_gl_trans) && $SysPrefs->show_voided_gl_trans == 0)
        $sql .= " AND gl.amount <> 0";

// 	if ($person_id)
// 		$sql .= " AND gl.person_id=".db_escape($person_id);

    if ($trans_no > 0)
        $sql .= " AND gl.type_no LIKE ".db_escape('%'.$trans_no)."";
    if ($voided == 1){
        $sql .= " AND ISNULL(v.date_)";
        $sql .= " AND gl.amount != 0";}


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

    if ($filter_type != null AND is_numeric($filter_type))
        $sql .= " AND gl.type= ".db_escape($filter_type);

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
function get_supplier_name_704($supplier_id)
{
    $sql = "SELECT supp_name FROM ".TB_PREF."suppliers WHERE supplier_id = ".db_escape($supplier_id)."";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}

function print_GL_transactions()
{
    global $path_to_root, $systypes_array;

    $dim = get_company_pref('use_dimension');

    $dimension = $dimension2 = 0;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $acc = $_POST['PARAM_2'];
    $fromacc = $_POST['PARAM_3'];
    $toacc = $_POST['PARAM_4'];
    if ($dim == 2)
    {
        $dimension = $_POST['PARAM_5'];
        $dimension2 = $_POST['PARAM_6'];
        $voided = $_POST['PARAM_7'];
        $comments = $_POST['PARAM_8'];
        $orientation = $_POST['PARAM_9'];
        $destination = $_POST['PARAM_10'];
    }
    elseif ($dim == 1)
    {
        $dimension = $_POST['PARAM_5'];
        $voided = $_POST['PARAM_6'];
        $comments = $_POST['PARAM_7'];
        $orientation = $_POST['PARAM_8'];
        $destination = $_POST['PARAM_9'];
    }
    else
    {
        $voided = $_POST['PARAM_5'];
        $comments = $_POST['PARAM_6'];
        $orientation = $_POST['PARAM_7'];
        $destination = $_POST['PARAM_8'];
    }
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $orientation = ($orientation ? 'L' : 'P');

    $rep = new FrontReport(_('GL Account Transactions'), "GLAccountTransactions", user_pagesize(), 9, $orientation);
    $dec = user_price_dec();

    $cols = array(0, 55, 75, 100, 155, 215, 270, 330, 355, 405, 465, 525);
    //------------0--1---2---3----4----5----6----7----8----9----10-------
    //-----------------------dim1-dim2-----------------------------------
    //-----------------------dim1----------------------------------------
    //-------------------------------------------------------------------
    $aligns = array('left', 'left', 'left', 'left',	'left',	'left',	'left',	'left',	'right', 'right', 'right');

    if ($dim == 2)
        $headers = array(_('Date/Memo'),	_('#'),	_('GP#'), _('Ref'),	_('Type'), _('Dimension')." 1", _('Dimension')." 2",
            _(''), _('Debit'),	_('Credit'), _('Balance'));
    elseif ($dim == 1)
        $headers = array(_('Date/Memo'),	_('#'),	_('GP#'), _('Ref'),	_('Type'), _('Dimension'), "", _(''),
            _('Debit'),	_('Credit'), _('Balance'));
    else
        $headers = array(_('Date/Memo'),	_('#'),	_('GP#'), _('Ref'),	_('Type'), "", "", _(''),
            _('Debit'),	_('Credit'), _('Balance'));

    if ($dim == 2)
    {
        $params =   array( 	0 => $comments,
            1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            2 => array('text' => _('Accounts'),'from' => $fromacc,'to' => $toacc),
            3 => array('text' => _('Dimension')." 1", 'from' => get_dimension_string($dimension),
                'to' => ''),
            4 => array('text' => _('Dimension')." 2", 'from' => get_dimension_string($dimension2),
                'to' => ''));
    }
    elseif ($dim == 1)
    {
        $params =   array( 	0 => $comments,
            1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            2 => array('text' => _('Accounts'),'from' => $fromacc,'to' => $toacc),
            3 => array('text' => _('Dimension'), 'from' => get_dimension_string($dimension),
                'to' => ''));
    }
    else
    {
        $params =   array( 	0 => $comments,
            1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            2 => array('text' => _('Accounts'),'from' => $fromacc,'to' => $toacc));
    }
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $accounts = get_gl_accounts($fromacc, $toacc,$acc);

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
        $prev_balance = get_gl_balance_from_to($begin, $from, $account["account_code"], $dimension, $dimension2);

// 		$trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension, $dimension2);
        $trans = get_gl_transactions_704($from, $to, -1, $account['account_code'], $dimension, $dimension2,null,
            null,null,null,'','','','',$account['account_type'],$voided);

        $rows = db_num_rows($trans);
        if ($prev_balance == 0.0 && $rows == 0)
            continue;
        $rep->Font('bold');
        $rep->TextCol(0, 4,	$account['account_code'] . " " . $account['account_name'], -2);
        $rep->TextCol(4, 6, _('Opening Balance'));
        if ($prev_balance > 0.0)
            $rep->AmountCol(9, 10, ($prev_balance), $dec);
        else
            $rep->AmountCol(9, 10, ($prev_balance), $dec);
        $rep->Font();
        $total = $prev_balance;
        $dr_amt = $cr_amt = 0;

        $rep->NewLine(2);
        if ($rows > 0)
        {
            while ($myrow=db_fetch($trans))
            {
                $total += $myrow['amount'];
                $rep->DateCol(0, 1,	$myrow["tran_date"], true);

                $reference = get_reference($myrow["type"], $myrow["type_no"]);
                $rep->TextCol(1, 2,	$myrow['type_no'], -2);
                $rep->TextCol(2, 3,	$myrow['text_1'], -2);

                $rep->TextCol(3, 4, $reference);
                if($myrow["type"] == 22)
                {
                    $supp_id = get_supplier_id_704($myrow['type_no']);
                    $supplier_name = get_supplier_name_704($supp_id);
                    $rep->TextCol(4, 5, $systypes_array[$myrow["type"]]);
                    $rep->NewLine();
                    $rep->TextCol(4, 5, $supplier_name);
                    $rep->NewLine(-1);
                }
                else
                    $rep->TextCol(4, 5, $systypes_array[$myrow["type"]], -2);


                if ($dim >= 1)
                    $rep->TextCol(5, 6,	get_dimension_string($myrow['dimension_id']));
                if ($dim > 1)
                    $rep->TextCol(6, 7,	get_dimension_string($myrow['dimension2_id']));
                $txt = payment_person_name($myrow["person_type_id"],$myrow["person_id"], false);

                // $rep->TextCol(5, 6, $myrow['memo_'], -2);

                if ($myrow['amount'] > 0.0)
                {
                    $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                    $dr_amt += $myrow['amount'];
                }
                else
                {
                    $rep->AmountCol(9, 10, abs($myrow['amount']), $dec);
                    $cr_amt += $myrow['amount'];
                }

                $rep->TextCol(10, 11, number_format2($total, $dec));

                $memo = get_comments_string($myrow['type'], $myrow['type_no']);
                if ($memo != "")
                {
                    $rep->NewLine();
                    $rep->TextCol(0, 10, $memo, -2);
                }

                $rep->NewLine(2);
                if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
                {
                    $rep->Line($rep->row - 2);
                    $rep->NewPage();
                }
            }
            $rep->NewLine();
        }
        $rep->Font('bold');

        $rep->NewLine();
        $rep->TextCol(4, 6,	_("Total"));


        if ($prev_balance > 0.0)
            $dr_prev_display= $prev_balance;
        else
            $cr_prev_display= $prev_balance;

        $dr_amt_total = $dr_prev_display+ $dr_amt;
        $cr_amt_total = $cr_prev_display+ $cr_amt;

        $rep->AmountCol(8, 9, abs($dr_amt), $dec);

        $rep->AmountCol(9, 10, abs($cr_amt), $dec);

        $rep->AmountCol(10, 11, ($total), $dec);

        $rep->NewLine();

        $rep->Font();
        $rep->Line($rep->row - $rep->lineHeight + 4);
        $rep->NewLine(2, 1);
    }
    $rep->End();
}

