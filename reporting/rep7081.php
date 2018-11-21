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
function get_gl_balances($from_date, $to_date, $account, $dimension=0, $dimension2=0)
{
	$from = date2sql($from_date);
	$to = date2sql($to_date);

	$sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans
		WHERE account='$account'";
	$sql .= " AND approval != 1";
	if ($from_date != "")
		$sql .= "  AND tran_date > '$from'";
	if ($to_date != "")
		$sql .= "  AND tran_date < '$to'";
	if ($dimension != 0)
		$sql .= " AND dimension_id = ".($dimension<0 ? 0 : db_escape($dimension));
	if ($dimension2 != 0)
		$sql .= " AND dimension2_id = ".($dimension2<0 ? 0 : db_escape($dimension2));
		
			$sql .= " AND type IN('10','22')";


	$result = db_query($sql, "The starting balance for account $account could not be calculated");

	$row = db_fetch_row($result);
	return $row[0];
}



function get_gl_balances_PAYABLE($from_date, $to_date, $account, $dimension=0, $dimension2=0)
{
	$from = date2sql($from_date);
	$to = date2sql($to_date);

	$sql = "SELECT SUM(amount) FROM ".TB_PREF."gl_trans
		WHERE account='$account'";
	$sql .= " AND approval != 1";
	if ($from_date != "")
		$sql .= "  AND tran_date > '$from'";
	if ($to_date != "")
		$sql .= "  AND tran_date < '$to'";
	if ($dimension != 0)
		$sql .= " AND dimension_id = ".($dimension<0 ? 0 : db_escape($dimension));
	if ($dimension2 != 0)
		$sql .= " AND dimension2_id = ".($dimension2<0 ? 0 : db_escape($dimension2));
		
			$sql .= " AND type IN('20','12')";


	$result = db_query($sql, "The starting balance for account $account could not be calculated");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_inactive( $account)
{

	$sql = "SELECT inactive FROM ".TB_PREF."gl_trans
		WHERE account='$account'";
	$sql .= " AND approval != 1";

		

	$result = db_query($sql, "The starting balance for account $account could not be calculated");

	$row = db_fetch_row($result);
	return $row[0];
}



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
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");
	$orientation = ($orientation ? 'L' : 'P');

	$rep = new FrontReport(_('CONSOLIDATED RA & PA REPORT'), "SOAREPORT", user_pagesize(), 9, $orientation);
	$dec = user_price_dec();

	$cols = array(0, 35, 255, 350, 460, 520);


	//------------0--1---2---3----4----5----6----7----8----9----10-------
	//-----------------------dim1-dim2-----------------------------------
	//-----------------------dim1----------------------------------------
	//-------------------------------------------------------------------
	
	$aligns = array('left', 'left', 'left',	'left',	'left',	'left');


	$headers= array(_('S.NO'),_('Name'), _('STATUS'),  _('RECEIVABLES'),_('PAYABLES'));
	



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
	$rep->Info($params, $cols, $headers, $aligns );
	$rep->NewPage();

	$accounts = get_gl_accounts($fromacc, $toacc,$acc);




	while ($account=db_fetch($accounts))
	{
		
		
// 		        $rep->TextCol(0,1, $serial_no);

	
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
$balances=get_gl_balances($begin, $from, $account["account_code"], $dimension, $dimension2);
 $trans = get_gl_transactions($from, $to, -1, $account['account_code'], $dimension, $dimension2,null,null,null,null,'','', '', '',$account['account_type']);
		
	$payable_bal=	get_gl_balances_PAYABLE($begin, $from, $account["account_code"], $dimension, $dimension2);
		$inactive=get_inactive( $account["account_code"]);
		$rows = db_num_rows($trans);
		if ($prev_balance == 0.0 && $rows == 0)
			continue;
// 		$rep->Font('bold');
								$serial_no++;


if($inactive==0){
    
    $status='ACTIVE';
}
else{
    
    $status='INACTIVE';
}

		$rep->TextCol(0, 1,$serial_no, -2);

		
    	$rep->TextCol(1, 4,$account['account_name'], -2);
    			$rep->TextCol(2, 3,$status, -2);

		$rep->TextCol(3, 4,$balances, -2);
		$rep->TextCol(4, 5,$payable_bal, -2);



			$rep->Line($rep->row - 2);
			$rep->NewLine();

// 		$rep->TextCol(4, 6, _(' Balance'));
// 		if ($prev_balance > 0.0)
// 			$rep->AmountCol(9,10, ($prev_balance), $dec);
// 		else
// 			$rep->AmountCol(9, 10, ($prev_balance), $dec);
			
// 		$rep->Font();
		$total = $prev_balance;
		$dr_amt = $cr_amt = 0;

// 		$rep->NewLine(2);
		if ($rows > 0)
		{
			while ($myrow=db_fetch($trans))
			{
				$total += $myrow['amount'];
				// $rep->DateCol(0, 1,	$myrow["tran_date"], true);

				$reference = get_reference($myrow["type"], $myrow["type_no"]);
				//$rep->TextCol(1, 2,	$myrow['type_no'], -2);

			//	$rep->TextCol(2, 3, $reference);
				// if($myrow["type"] == 22) 
				// {
    //                 $supp_id = get_supplier_id_704($myrow['type_no']);
    //                 $supplier_name = get_supplier_name_704($supp_id);
                    // $rep->TextCol(3, 4, $systypes_array[$myrow["type"]]);
                    // $rep->NewLine();
                    // $rep->TextCol(3, 4, $supplier_name);
                    // $rep->NewLine(-1);
                // }
                // else
                    // $rep->TextCol(3, 4, $systypes_array[$myrow["type"]], -2);


			
// $txt = payment_person_name($myrow["person_type_id"],$myrow["person_id"], false);
// 	if($myrow["type"] == 10 || $myrow["type"] == 11 && $myrow['amount'] > 0.0)
// 				{
// 					$rep->AmountCol(3, 4, abs($myrow['amount']), $dec);
// 				}	
         
//             	elseif($myrow["type"] == 22 )
// 				{
// 					$rep->AmountCol(5, 6, abs($myrow['amount']), $dec);
// 				}
// 				elseif($myrow["type"] == 20 )
// 				{
// 					$rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
// 				}
// 				elseif($myrow["type"] == 12 )
// 				{
// 					$rep->AmountCol(8, 9, abs($myrow['amount']), $dec);
// 				}
			
				
				
				
				// if ($myrow['amount'] > 0.0)
				// {
				// 	$rep->AmountCol(7, 8, abs($myrow['amount']), $dec);
				// 	$dr_amt += $myrow['amount'];
				// }
 		
 		
				
				
				// $rep->TextCol(9, 10, number_format2($total, $dec));

//  $memo = get_comments_string($myrow['type'], $myrow['type_no']);
            
// 				$memo = $myrow['memo_'];
// 				if ($txt != "")
// 				{

// 					if ($memo != "")
// 						$txt = $txt."/".$memo;
// 				}
// 				else
// 					$txt = $memo;

// if ($memo != "")
//             {
//                 // $rep->NewLine();
//                 $rep->TextColLines(1, 2, $memo, -2);
//             }
            
//             $rep->NewLine();

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

				// $rep->NewLine();
				if ($rep->row < $rep->bottomMargin + $rep->lineHeight)
				{
					$rep->Line($rep->row - 2);
					$rep->NewPage();
				}
			}
// 			$rep->NewLine();
		}
// 		$rep->Font('bold');

		//$rep->TextCol(4, 6,	_("Opening Balance"));

// 		if ($prev_balance > 0.0)
// 			$rep->AmountCol(7, 8, abs($prev_balance), $dec);
// 		else
// 			$rep->AmountCol(8, 9, abs($prev_balance), $dec);

// 			$rep->NewLine();
// 		$rep->TextCol(4, 6,	_("Total"));


		if ($prev_balance > 0.0)
			$dr_prev_display= $prev_balance;
		else
			$cr_prev_display= $prev_balance;

// 			$dr_amt_total = $dr_prev_display+ $dr_amt;	
// 			$cr_amt_total = $cr_prev_display+ $cr_amt;	

// 			$rep->AmountCol(7, 8, abs($dr_amt), $dec);
		
// 			$rep->AmountCol(8, 9, abs($cr_amt), $dec);

// 			$rep->AmountCol(8, 9, ($total), $dec);

// 			$rep->NewLine();

// 		$rep->TextCol(4, 6,	_("Ending Balance"));
// 		if ($total > 0.0)
// 			$rep->AmountCol(7, 8, abs($total), $dec);
// 		else
// 			$rep->AmountCol(8, 9, abs($total), $dec);
// 		$rep->Font();
// 		$rep->Line($rep->row - $rep->lineHeight + 4);
// 		$rep->NewLine(2, 1);
	}
	$rep->End();
}