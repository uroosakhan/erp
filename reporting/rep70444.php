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
	$customer_multi = $_POST['PARAM_3'];
	$supp_multi = $_POST['PARAM_4'];
    $fromacc = $_POST['PARAM_5'];
//    $fromacc = $_POST['PARAM_6'];
	$toacc = $_POST['PARAM_6'];
	if ($dim == 2)
	{
	    $dimension = $_POST['PARAM_7'];
		$dimension2 = $_POST['PARAM_8'];
		$comments = $_POST['PARAM_9'];
		$orientation = $_POST['PARAM_10'];
		$destination = $_POST['PARAM_11'];
	}
	elseif ($dim == 1)
	{
		$dimension = $_POST['PARAM_7'];
		$comments = $_POST['PARAM_8'];
		$orientation = $_POST['PARAM_9'];
		$destination = $_POST['PARAM_10'];
	}
	else
	{
		$comments = $_POST['PARAM_7'];
		$orientation = $_POST['PARAM_8'];
		$destination = $_POST['PARAM_9'];
	}
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$orientation = ($orientation ? 'L' : 'P');

	$rep = new FrontReport(_('GL Account Transactions'), "GLAccountTransactions", user_pagesize(), 9, $orientation);
	$dec = user_price_dec();

	$cols = array(0, 55, 70, 155, 215, 270, 330, 355, 405, 465, 525);
	//------------0--1---2---3----4----5----6----7----8----9----10-------
	//-----------------------dim1-dim2-----------------------------------
	//-----------------------dim1----------------------------------------
	//-------------------------------------------------------------------
	$aligns = array('left', 'left', 'left',	'left',	'left',	'left',	'left',	'right', 'right', 'right');

	if ($dim == 2)
		$headers = array(_('Date/Memo'),	_('#'), _('Ref'),	_('Type'), _('Dimension')." 1", _('Dimension')." 2",
			_(''), _('Debit'),	_('Credit'), _('Balance'));
	elseif ($dim == 1)
		$headers = array(_('Date/Memo'),	_('#'), _('Ref'),	_('Type'), _('Dimension'), "", _(''),
			_('Debit'),	_('Credit'), _('Balance'));
	else
		$headers = array(_('Date/Memo'),	_('#'), _('Ref'),	_('Type'), "", "", _(''),
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



    foreach($customer_multi as $key => $value) {
        $accounts = get_gl_accounts($fromacc, $toacc,$acc);

        while ($account = db_fetch($accounts)) {

            if (is_account_balancesheet($account["account_code"]))
                $begin = "";
            else {
                $begin = get_fiscalyear_begin_for_date($from);
                if (date1_greater_date2($begin, $from))
                    $begin = $from;
                $begin = add_days($begin, -1);
            }
            $prev_balance = get_gl_balance_from_to($begin, $from, $account["account_code"], $dimension, $dimension2);


// 		$trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension, $dimension2);
            $trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension,
                $dimension2, null, null, null, null, '', '',
                '',
                '', $account['account_type'], $value);
            $rows = db_num_rows($trans);
            if ($prev_balance == 0.0 && $rows == 0)
                continue;
            $rep->Font('bold');
            $rep->TextCol(0, 4, $account['account_code'] . " " . $account['account_name'], -2);
            $rep->TextCol(4, 6, _('Opening Balance'));
            if ($prev_balance > 0.0)
                $rep->AmountCol(9, 10, ($prev_balance), $dec);
            else
                $rep->AmountCol(9, 10, ($prev_balance), $dec);
            $rep->Font();
            $total = $prev_balance;
            $dr_amt = $cr_amt = 0;

            $rep->NewLine(2);
            if ($rows > 0) {
                while ($myrow = db_fetch($trans)) {
                    $total += $myrow['amount'];
                    $rep->DateCol(0, 1, $myrow["tran_date"], true);

                    $reference = get_reference($myrow["type"], $myrow["type_no"]);
                    $rep->TextCol(1, 2, $myrow['type_no'], -2);

                    $rep->TextCol(2, 3, $reference);
                    if ($myrow["type"] == 22) {
                        $supp_id = get_supplier_id_704($myrow['type_no']);
                        $supplier_name = get_supplier_name_704($supp_id);
                        $rep->TextCol(3, 4, $systypes_array[$myrow["type"]]);
                        $rep->NewLine();
                        $rep->TextCol(3, 4, $supplier_name);
                        $rep->NewLine(-1);
                    } else
                        $rep->TextCol(3, 4, $systypes_array[$myrow["type"]], -2);


                    if ($dim >= 1)
                        $rep->TextCol(4, 5, get_dimension_string($myrow['dimension_id']));
                    if ($dim > 1)
                        $rep->TextCol(5, 6, get_dimension_string($myrow['dimension2_id']));
                    $txt = payment_person_name($myrow["person_type_id"], $myrow["person_id"], false);


                    if ($myrow['amount'] > 0.0) {
                        $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
                        $dr_amt += $myrow['amount'];
                    } else {
                        $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                        $cr_amt += $myrow['amount'];
                    }

                    $rep->TextCol(9, 10, number_format2($total, $dec));

                    $memo = get_comments_string($myrow['type'], $myrow['type_no']);
                    if ($memo != "") {
                        $rep->NewLine();
                        $rep->TextCol(0, 10, $memo, -2);
                    }
                    // $memo = $myrow['memo_'];
                    // if ($txt != "")
                    // {

                    // 	if ($memo != "")
                    // 		$txt = $txt."/".$memo;
                    // }
                    // else
                    // 	$txt = $memo;


                    //$rep->Font('i');
                    //$rep->TextColLines(6, 7,	$txt, -2);
                    //$rep->Font('');

                    // if ($txt != "")
                    // {
                    // $rep->NewLine();
                    // }

                    // $rep->Font('i');
                    // $rep->TextCol(0, 7,	_('Memo: ') . '' . $txt, -2);
                    // $rep->Font('');

                    $rep->NewLine(2);
                    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
                        $rep->Line($rep->row - 2);
                        $rep->NewPage();
                    }
                }
                $rep->NewLine();
            }

        }
    }

    //-------------------Supplier Loop-----------------------------------------------------------------
    foreach($supp_multi as $key => $value) {
        $accounts = get_gl_accounts($fromacc, $toacc,$acc);

        while ($account = db_fetch($accounts)) {

            if (is_account_balancesheet($account["account_code"]))
                $begin = "";
            else {
                $begin = get_fiscalyear_begin_for_date($from);
                if (date1_greater_date2($begin, $from))
                    $begin = $from;
                $begin = add_days($begin, -1);
            }
            $prev_balance = get_gl_balance_from_to($begin, $from, $account["account_code"], $dimension, $dimension2);


// 		$trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension, $dimension2);
            $trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension,
                $dimension2, null, null, null, null, '', '',
                '',
                '', $account['account_type'], null,$value);
            $rows = db_num_rows($trans);
            if ($prev_balance == 0.0 && $rows == 0)
                continue;
            $rep->Font('bold');
            $rep->TextCol(0, 4, $account['account_code'] . " " . $account['account_name'], -2);
            $rep->TextCol(4, 6, _('Opening Balance'));
            if ($prev_balance > 0.0)
                $rep->AmountCol(9, 10, ($prev_balance), $dec);
            else
                $rep->AmountCol(9, 10, ($prev_balance), $dec);
            $rep->Font();
            $total = $prev_balance;
            $dr_amt = $cr_amt = 0;

            $rep->NewLine(2);
            if ($rows > 0) {
                while ($myrow = db_fetch($trans)) {
                    $total += $myrow['amount'];
                    $rep->DateCol(0, 1, $myrow["tran_date"], true);

                    $reference = get_reference($myrow["type"], $myrow["type_no"]);
                    $rep->TextCol(1, 2, $myrow['type_no'], -2);

                    $rep->TextCol(2, 3, $reference);
                    if ($myrow["type"] == 22) {
                        $supp_id = get_supplier_id_704($myrow['type_no']);
                        $supplier_name = get_supplier_name_704($supp_id);
                        $rep->TextCol(3, 4, $systypes_array[$myrow["type"]]);
                        $rep->NewLine();
                        $rep->TextCol(3, 4, $supplier_name);
                        $rep->NewLine(-1);
                    } else
                        $rep->TextCol(3, 4, $systypes_array[$myrow["type"]], -2);


                    if ($dim >= 1)
                        $rep->TextCol(4, 5, get_dimension_string($myrow['dimension_id']));
                    if ($dim > 1)
                        $rep->TextCol(5, 6, get_dimension_string($myrow['dimension2_id']));
                    $txt = payment_person_name($myrow["person_type_id"], $myrow["person_id"], false);


                    if ($myrow['amount'] > 0.0) {
                        $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
                        $dr_amt += $myrow['amount'];
                    } else {
                        $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                        $cr_amt += $myrow['amount'];
                    }

                    $rep->TextCol(9, 10, number_format2($total, $dec));

                    $memo = get_comments_string($myrow['type'], $myrow['type_no']);
                    if ($memo != "") {
                        $rep->NewLine();
                        $rep->TextCol(0, 10, $memo, -2);
                    }
                    // $memo = $myrow['memo_'];
                    // if ($txt != "")
                    // {

                    // 	if ($memo != "")
                    // 		$txt = $txt."/".$memo;
                    // }
                    // else
                    // 	$txt = $memo;


                    //$rep->Font('i');
                    //$rep->TextColLines(6, 7,	$txt, -2);
                    //$rep->Font('');

                    // if ($txt != "")
                    // {
                    // $rep->NewLine();
                    // }

                    // $rep->Font('i');
                    // $rep->TextCol(0, 7,	_('Memo: ') . '' . $txt, -2);
                    // $rep->Font('');

                    $rep->NewLine(2);
                    if ($rep->row < $rep->bottomMargin + $rep->lineHeight) {
                        $rep->Line($rep->row - 2);
                        $rep->NewPage();
                    }
                }
                $rep->NewLine();
            }

        }
    }
//
//
//
//
//
//


		$rep->Font('bold');

		//$rep->TextCol(4, 6,	_("Opening Balance"));

		//if ($prev_balance > 0.0)
		//	$rep->AmountCol(7, 8, abs($prev_balance), $dec);
		//else
		//	$rep->AmountCol(8, 9, abs($prev_balance), $dec);

			$rep->NewLine();
		$rep->TextCol(4, 6,	_("Total"));


		if ($prev_balance > 0.0)
			$dr_prev_display= $prev_balance;
		else
			$cr_prev_display= $prev_balance;

			$dr_amt_total = $dr_prev_display+ $dr_amt;	
			$cr_amt_total = $cr_prev_display+ $cr_amt;	

			$rep->AmountCol(7, 8, abs($dr_amt), $dec);
		
			$rep->AmountCol(8, 9, abs($cr_amt), $dec);

			$rep->AmountCol(9, 10, ($total), $dec);

			$rep->NewLine();

		//$rep->TextCol(4, 6,	_("Ending Balance"));
		//if ($total > 0.0)
		//	$rep->AmountCol(7, 8, abs($total), $dec);
		//else
		//	$rep->AmountCol(8, 9, abs($total), $dec);
		$rep->Font();
		$rep->Line($rep->row - $rep->lineHeight + 4);
		$rep->NewLine(2, 1);

	$rep->End();
}

