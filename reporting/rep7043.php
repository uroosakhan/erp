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


function get_chart_master($account)
{
    $sql = "SELECT * FROM ".TB_PREF."chart_master WHERE 
    account_code=".db_escape($account);
    $query = db_query($sql, "Error");
    return db_fetch($query);
}
//////////////
function get_gl_trans_account_names($account_code , $type_no ,$type){
    $sql=" SELECT `account_name` FROM `0_chart_master`
INNER JOIN 0_gl_trans ON 0_gl_trans.account=0_chart_master.`account_code`
WHERE 0_gl_trans.account != ".db_escape($account_code)."
AND 0_gl_trans.type_no= ".db_escape($type_no)."
AND 0_gl_trans.type=".db_escape($type);
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
/////////////////






}

function get_account_name(){
    $sql="SELECT  account_name  FROM 0_chart_master ";
    $db = db_query($sql,'Can not get Designation name');
    $ft = db_fetch($db);
    return $ft[0];
}
//----------------------------------------------------------------------------------------------------

print_GL_transactions();

//----------------------------------------------------------------------------------------------------
function get_supplier_id_7044($type_no)
{
    $sql = "SELECT person_id 
    FROM ".TB_PREF."supp_allocations
    WHERE trans_no_from = ".db_escape($type_no)."
    ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}


function get_account_code($type_no,$type)
{
    $sql = "SELECT account
    FROM ".TB_PREF."gl_trans
    WHERE type_no = ".db_escape($type_no)."
    AND type =".db_escape($type)."
    AND amount > 0 ";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}



function get_supplier_name_7044($supplier_id)
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
    $acc_head = $_POST['PARAM_2'];
    $fromacc = $_POST['PARAM_3'];
    $toacc = $_POST['PARAM_4'];
    if ($dim == 2)
    {
        $dimension = $_POST['PARAM_5'];
        $dimension2 = $_POST['PARAM_6'];
        $comments = $_POST['PARAM_7'];
        $orientation = $_POST['PARAM_8'];
        $destination = $_POST['PARAM_9'];
    }
    elseif ($dim == 1)
    {
        $dimension = $_POST['PARAM_5'];
        $comments = $_POST['PARAM_6'];
        $orientation = $_POST['PARAM_7'];
        $destination = $_POST['PARAM_8'];
    }
    else
    {
        $comments = $_POST['PARAM_5'];
        $orientation = $_POST['PARAM_6'];
        $destination = $_POST['PARAM_7'];
    }
    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report_potraite.inc");
    $orientation = ($orientation = 'P');
//    display_error($destination);
    if($destination == 0) {
        $rep = new FrontReport2(_(''), "GLAccountTransactions", user_pagesize(), 7, $orientation);
    }
    else
    {
        $rep = new FrontReport(_(''), "GLAccountTransactions", user_pagesize(), 7, $orientation);

    }	$dec = user_price_dec();


    $cols = array(5, 83, 55, 150, 190,  190, 180, 350, 405, 461 ,522);


    //------------0--1---2---3----4----5----6----7----8----9----10-------
    //-----------------------dim1-dim2-----------------------------------
    //-----------------------dim1----------------------------------------
    //-------------------------------------------------------------------

    $rep->NewLine(2);
    $rep->Font('bold');

    $aligns = array('left', 'left', 'left',	'left',	'left',	'left',	'right','right', 'right', 'center');

    if ($dim == 2)
        $headers = array(_('Date'),	_(''), _('Vou Ref #'),	_('Particulars'), _('')." ", _('')." ",
            _(''), _('Debit'),	_('Credit'), _('Balance'));
    elseif ($dim == 1)
        $headers = array(_('Date'),	_(''), _('Vou Ref #'),	_('Particulars'), _('Dimension'), "", _(''),
            _('Debit'),	_('Credit'), _('Balance'));
    else
        $headers = array(_('Date'),	_(''), _('Vou Ref #'),	_('Particulars'), "", "", _(''),
            _('Debit'),	_('Credit'), _('Balance'));




    $acc = get_chart_master($fromacc);


    if ($dim == 2)
    {
        $params =   array( 	0 => $comments,
            0 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            1 => array('text' => _('Accounts'),'from1' => $acc['account_code'],'to1' => $acc['account_name']),
            2 => array('text' => _('Dimension')." 1", 'from' => get_dimension_string($dimeansion),
                'to' => ''),
            3 => array('text' => _('Dimension')." 2", 'from' => get_dimension_string($dimeansion2),
                'to' => ''));
    }
    elseif ($dim == 1)
    {
        $params =   array( 	0 => $comments,
            0 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            1 => array('text' => _('Accounts'),'from1' => $acc['account_code'],'to1' => $acc['account_name']),
            2 => array('text' => _('Dimension'), 'from' => get_dimension_string($dimensaion),
                'to' => ''));
    }
    else
    {
        $params =   array( 	0 => $comments,
            0 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
            1 => array('text' => _('Accounts'),'from1' => $acc['account_code'],'to1' => $acc['account_name']));
    }

    if ($orientation == 'L')
        recalculate_cols($cols);
    if($destination == 0) {
        $rep->SetFont('trebuchet', 'BI', 15, “, 'false');
    }
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);


    $rep->NewPage();

// 	========================
// 	$rep->fontSize += 5;
// 	$rep->NewLine(-12);
// 	$rep->TextCol(0, 7,"Gujrat Steel (Pvt) Limited", -2);
// 	$rep->SetTextColor(255, 0, 0);
// 	$rep->NewLine(+5.7);
// 	$rep->TextCol(0, 7,"ACCOUNT LEDGER", -2);
// 	$rep->NewLine(-5.7);
// 	$rep->MultiCell(100, 638, "Durations",0, 'C', 0, 2, 420,60, true);//debit
// 	$rep->SetTextColor(0, 0, 0);
// 	$rep->NewLine(+11);
// 	$rep->NewLine();
// 	$rep->fontSize -= 5;
// 	========================



    $accounts = get_gl_accounts($fromacc, $toacc,	$acc_head);

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
        $trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension, $dimension2,null,null,null,null,'','','','',$account['account_type']);
        $rows = db_num_rows($trans);
        if ($prev_balance == 0.0 && $rows == 0)
            continue;
        $rep->Font('bold');
// 		$rep->TextCol(0, 4,	$account['account_code'] . " " . $account['account_name'], -2);
        $rep->TextCol(4, 6, _('Opening Balance'));
        $rep->Line($rep->row - 2);
        if ($prev_balance > 0.0)
            $rep->AmountCol(9, 10, ($prev_balance), $dec);
        else
            $rep->AmountCol(9, 10, ($prev_balance), $dec);

        $rep->Font();
        $total = $prev_balance;
        $dr_amt = $cr_amt = 0;



// 		$rep->MultiCell(200, 657, "".$account['account_name'] . "  A/c  " . $account['account_code'],0, 'L', 0, 2, 40,125, true);

        $rep->NewLine();

        if ($rows > 0)
        {
            while ($myrow=db_fetch($trans))
            {
                //
                global $leftmenu_save, $db_connections;
                //         if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='LGC')
                //         {
                // $rep->LineTo(40, $rep->row - 2 ,40, 695);
                // $rep->LineTo(92, $rep->row - 1 ,92, 695);
                // $rep->LineTo(166, $rep->row - 1 ,166, 692);
                // $rep->LineTo(193, $rep->row - 1 ,193, 692);
                //   $rep->LineTo(384, $rep->row - 1 ,384, 692);
                // $rep->LineTo(508, $rep->row - 1 ,508, 692);
                // $rep->LineTo(565, $rep->row - 1 ,565, 692);
                // $rep->LineTo(440, $rep->row - 1 ,440, 692);
                //         }
                //         else
//                {
//                $rep->LineTo(40, $rep->row - 16.2,40, 463);
//                $rep->LineTo(90, $rep->row - 16.2,90, 463);
//                $rep->LineTo(197, $rep->row - 16.2 ,197 ,463);
//                $rep->LineTo(526, $rep->row - 16.2 ,526, 463);
//                $rep->LineTo(612, $rep->row - 16.2 ,612, 463);
//                $rep->LineTo(712, $rep->row - 16.2,712, 463);
//                $rep->LineTo(812, $rep->row - 16.2 ,812, 463);

//        }

                $total += $myrow['amount'];

                $rep->DateCol(0, 1,	" ".$myrow["tran_date"], true);
                $reference = get_reference($myrow["type"], $myrow["type_no"]);


                //$account_code=	get_account_code ($myrow["type_no"], $myrow["type"]);
                //$rep->TextCol(1, 2,	" ".	$account_code, -2);

                if($myrow["type"] == 22)
                {
                    $supp_id = get_supplier_id_7044($myrow['type_no']);
                    $supplier_name = get_supplier_name_7044($supp_id);
                    $rep->TextCol(2, 3, $systypes_array[$myrow["type"]]);
                    // $rep->NewLine();
                  //  $rep->TextCol(1, 2,$myrow["account_name"]);
                    // $rep->NewLine(-1);
                }
                else{

                    $rep->TextCol(2, 3, $reference);
                    //$rep->NewLine();

                 //   $rep->TextCol(1, 2, get_gl_trans_account_names($myrow["account"],$myrow["type_no"],$myrow["type"]));

                    //display_error($reference.$myrow["account_name"]);

                }

                $txt = payment_person_name($myrow["person_type_id"],$myrow["person_id"], false);

                if ($myrow['amount'] > 0.0)
                {
                    $rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
                    $dr_amt += $myrow['amount'];
                } else {
                    $rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
                    $cr_amt += $myrow['amount'];
                }
                $rep->TextCol(9, 10, number_format2($total, $dec));

                if ($myrow['type'] == 22 || $myrow['type'] == 12 || $myrow['type'] == 41 || $myrow['type'] == 42 || $myrow['type'] == 1 || $myrow['type'] == 2 || $myrow['type'] == 4){
                    $myrow['memo1'] = get_comments_string($myrow['type'], $myrow['type_no']);

                    if ($destination)

                        $rep->TextCol(3, 7, $myrow['memo1'], -2);
                    else
                        $rep->TextColLines(3, 7, $myrow['memo1'], -2);
                } else {
                    if ($destination)
                        $rep->TextCol(3, 7, $myrow['memo1'], -2);
                    else
                        $rep->TextColLines(3, 7, $myrow['memo1'], -2);
                }
                //        if ($dim >= 1)
                // {
                // 	$rep->TextCol(4, 5,	get_dimension_string($myrow['dimension_id']));
                // 	if($myrow['dimension_id']!=0)
                // 	$rep->NewLine();
                // }
                // if ($dim > 1)
                // {
                // 	$rep->TextCol(5, 6,	get_dimension_string($myrow['dimension2_id']));
                // 	if($myrow['dimension2_id']!=0)
                // 	$rep->NewLine();

                // }
                $rep->Line($rep->row + 8);

                $memo = get_comments_string($myrow['type'], $myrow['type_no']);

                $rep->LineTo($rep->leftMargin, 58.4 * $rep->lineHeight, $rep->leftMargin, $rep->row  +6.5);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin, $rep->row +6.5);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin - 63, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 63, $rep->row+6.5 );
                $rep->LineTo($rep->pageWidth - $rep->rightMargin - 117, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 117, $rep->row+6.5 );
                $rep->LineTo($rep->pageWidth - $rep->rightMargin - 172, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 172, $rep->row +6.5);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin - 383, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 383, $rep->row  +6.5);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin - 475, 58.4 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 475, $rep->row  +6.5);


                $rep->NewLine();
                // $rep->Line($rep->row + 6);

            }

            $rep->Font('bold');

// 		$rep->TextCol(4, 6,	_("Opening Balance"));

            //if ($prev_balance > 0.0)
            //	$rep->AmountCol(7, 8, abs($prev_balance), $dec);
            //else
            //	$rep->AmountCol(8, 9, abs($prev_balance), $dec);

            //	$rep->NewLine();

            $rep->fontSize += 0.5;

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
            $rep->fontSize -= 0.5;

            //$rep->TextCol(4, 6,	_("Ending Balance"));
            //if ($total > 0.0)
            //	$rep->AmountCol(7, 8, abs($total), $dec);
            //else
            //	$rep->AmountCol(8, 9, abs($total), $dec);
            $rep->Font();


//		$rep->Line($rep->row - $rep->lineHeight + 4);
//		$rep->NewLine(2, 1);


        }
//        $rep->LineTo($rep->leftMargin, 39* $rep->lineHeight ,$rep->leftMargin, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin,39* $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 200, 39* $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 200, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 100, 39 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 100, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 285, 39 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 285, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 615, 39 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 615, $rep->row-1);
//        $rep->LineTo($rep->pageWidth - $rep->rightMargin - 722, 39 * $rep->lineHeight, $rep->pageWidth - $rep->rightMargin - 722, $rep->row-1);

//
    }

    $rep->Line($rep->row);
// $rep->Line($rep->row - $rep->lineHeight + 4);
    $rep->End();

}

