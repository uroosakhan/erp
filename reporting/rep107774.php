<?php
$page_security = 'SA_GLREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Chart of GL Accounts
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------



print_Chart_of_Accounts();

//----------------------------------------------------------------------------------------------------
function get_account_chart_master_reciept($account_code)
{
	$sql = "SELECT account_name
			FROM 0_chart_master masters, 0_chart_types chart
			WHERE masters.account_type = chart.id
			AND masters.account_code = '$account_code'";
	$result = db_query($sql, "could not process Requisition to Purchase Order");
	$row = db_fetch($result);
	return $row[0] ;

}


function get_customer_detail_balance($customer_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
	// removed - debtor_trans.alloc from all summations

//	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
//dz 16.6.17
	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
	if ($all)
		$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";

	else

		$value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
    		trans.alloc),0)";

	$due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
	$sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
    			credit_status.dissallow_invoices, credit_status.reason_description,
				Sum(IFNULL($value,0)) AS Balance,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
			FROM ".TB_PREF."debtors_master debtor
				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
		.TB_PREF."payment_terms terms,"
		.TB_PREF."credit_status credit_status
			WHERE
					debtor.payment_terms = terms.terms_indicator
	 			AND debtor.credit_status = credit_status.id";
	if ($customer_id)
		$sql .= " AND debtor.debtor_no = ".db_escape($customer_id);

	if (!$all)
		$sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax +
		 trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;
	$sql .= " GROUP BY
			  	debtor.name,
			  	terms.terms,
			  	terms.days_before_due,
			  	terms.day_in_following_month,
			  	debtor.credit_limit,
			  	credit_status.dissallow_invoices,
			  	credit_status.reason_description";
	$result = db_query($sql,"The customer details could not be retrieved");

	$customer_record = db_fetch($result);

	return $customer_record;

}



function convert_number_to_words($number) {

	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ', ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'Zero',
		1                   => 'One',
		2                   => 'Two',
		3                   => 'Three',
		4                   => 'Four',
		5                   => 'Five',
		6                   => 'Six',
		7                   => 'Seven',
		8                   => 'Eight',
		9                   => 'Nine',
		10                  => 'Ten',
		11                  => 'Eleven',
		12                  => 'Twelve',
		13                  => 'Thirteen',
		14                  => 'Fourteen',
		15                  => 'Fifteen',
		16                  => 'Sixteen',
		17                  => 'Seventeen',
		18                  => 'Eighteen',
		19                  => 'Nineteen',
		20                  => 'Twenty',
		30                  => 'Thirty',
		40                  => 'Fourty',
		50                  => 'Fifty',
		60                  => 'Sixty',
		70                  => 'Seventy',
		80                  => 'Eighty',
		90                  => 'Ninety',
		100                 => 'Hundred',
		1000                => 'Thousand',
		1000000             => 'Million',
		1000000000          => 'Billion',
		1000000000000       => 'Trillion',
		1000000000000000    => 'Quadrillion',
		1000000000000000000 => 'Quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . convert_number_to_words(abs($number));
	}

	$string = $fraction = null;

	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . convert_number_to_words($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			$string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= convert_number_to_words($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return $string;
}


function get_gl_trans11($type, $trans_id)
{
	$sql = "SELECT SUM(dt.ov_amount+dt.ov_freight) AS OutStanding,gl.*, cm.account_name, IFNULL(refs.reference, '') AS reference, user.real_name, 
			COALESCE(st.tran_date, dt.tran_date, bt.trans_date, grn.delivery_date, gl.tran_date) as doc_date,bt.cheque,st.cheque,
			IF(ISNULL(st.supp_reference), '', st.supp_reference) AS supp_reference
	FROM ".TB_PREF."gl_trans as gl
		LEFT JOIN ".TB_PREF."chart_master as cm ON gl.account = cm.account_code
		LEFT JOIN ".TB_PREF."refs as refs ON (gl.type=refs.type AND gl.type_no=refs.id)
		LEFT JOIN ".TB_PREF."audit_trail as audit ON (gl.type=audit.type AND gl.type_no=audit.trans_no AND NOT ISNULL(gl_seq))
		LEFT JOIN ".TB_PREF."users as user ON (audit.user=user.id)
	# all this below just to retrieve doc_date :>
		LEFT JOIN ".TB_PREF."supp_trans st ON gl.type_no=st.trans_no AND st.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=st.supplier_id)
		LEFT JOIN ".TB_PREF."grn_batch grn ON grn.id=gl.type_no AND gl.type=".ST_SUPPRECEIVE." AND gl.person_id=grn.supplier_id
		LEFT JOIN ".TB_PREF."debtor_trans dt ON gl.type_no=dt.trans_no AND dt.type=gl.type AND (gl.type!=".ST_JOURNAL." OR gl.person_id=dt.debtor_no)
		LEFT JOIN ".TB_PREF."bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no AND bt.amount!=0
			 AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id
		LEFT JOIN ".TB_PREF."journal j ON j.type=gl.type AND j.trans_no=gl.type_no"

		." WHERE gl.type= ".db_escape($type)
		." AND gl.type_no = ".db_escape($trans_id)
		." AND gl.amount <> 0"

		." ORDER BY tran_date, counter";

	return db_query($sql, "The gl transactions could not be retrieved");
}
function get_bank_trans_new($type, $trans_no=null, $person_type_id=null, $person_id=null)
{
	$sql = "SELECT dt.debtor_no, bt.*, act.*,
(bt.amount - (+ st.supply_disc + st.service_disc + st.fbr_disc +  st.srb_disc)) as amount,
		IFNULL(abs(dt.ov_amount), 
		IFNULL(ABS(st.ov_amount), bt.amount)) settled_amount,
		IFNULL(abs(dt.ov_amount/bt.amount), 
		IFNULL(ABS(st.ov_amount/bt.amount), 1)) settle_rate,
		IFNULL(debtor.curr_code,
		 IFNULL(supplier.curr_code,
		  act.bank_curr_code)) settle_curr
		FROM ".TB_PREF."bank_trans bt
        LEFT JOIN ".TB_PREF."debtor_trans dt ON dt.type=bt.type AND dt.trans_no=bt.trans_no
        LEFT JOIN ".TB_PREF."debtors_master debtor ON debtor.debtor_no = dt.debtor_no
        LEFT JOIN ".TB_PREF."supp_trans st ON st.type=bt.type AND st.trans_no=bt.trans_no
        LEFT JOIN ".TB_PREF."suppliers supplier ON supplier.supplier_id = st.supplier_id,
			 ".TB_PREF."bank_accounts act
		WHERE act.id=bt.bank_act ";

	if ($type != null)
		$sql .= " AND bt.type=".db_escape($type);

	if ($trans_no != null)
		$sql .= " AND bt.trans_no = ".db_escape($trans_no);

	if ($person_type_id != null)
		$sql .= " AND bt.person_type_id = ".db_escape($person_type_id);

	if ($person_id != null)
		$sql .= " AND bt.person_id = ".db_escape($person_id);

	$sql .= " ORDER BY trans_date, bt.id";

	return db_query($sql, "query for bank transaction");
}

function get_account_chart_master_($account_code)
{
	$sql = "SELECT account_name
			FROM 0_chart_master masters, 0_chart_types chart
			WHERE masters.account_type = chart.id
			AND masters.account_code = '$account_code'";
	$result = db_query($sql, "could not process Requisition to Purchase Order");
	$row = db_fetch($result);
	return $row[0] ;

}
function get_dimension_new($id)
{
	$sql = "SELECT reference FROM " . TB_PREF . "dimensions	WHERE id=" . db_escape($id);

	$result = db_query($sql, "The dimension could not be retrieved");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_branch_name_od($branch_id)
{
	$sql = "SELECT br_name FROM ".TB_PREF."cust_branch 
		WHERE branch_code = ".db_escape($branch_id);

	$result = db_query($sql,"could not retreive name for branch" . $branch_id);

	$myrow = db_fetch_row($result);
	return $myrow[0];
}

function get_customer_accounts_($debtor_no)
{
	$sql = "SELECT receivables_account,sales_account, sales_discount_account, payment_discount_account 
		FROM ".TB_PREF."cust_branch WHERE debtor_no=".db_escape($debtor_no);

	$result = db_query($sql, "Cannot retreive a customer branch");

	return db_fetch($result);
}

function print_Chart_of_Accounts()
{
	global $path_to_root, $systypes_array;

	$trans_no = $_POST['PARAM_0'];
	$type = $_POST['PARAM_1'];
	$comments = $_POST['PARAM_2'];
	$orientation = $_POST['PARAM_3'];
	$destination = $_POST['PARAM_4'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	$dec = 0;

	$cols = array(5, 40, 125, 160, 200 , 300);

	$headers1 = array(_('Code'), _('Title Account'), _('Debit'), _('Credit'));
//    $headers = array(_(' Account'), _(' Account'), _(''),_('Centre'), _('Rs'), _('Rs'));

	$aligns = array('left',	'left',	'left','left','right');

	$params = array();

	$rep = new FrontReport(_(''), "ChartOfAccounts", 'POS3', 9);
	if ($orientation == 'L')
		recalculate_cols($cols);

	$rep->Font();
	$rep->SetHeaderType('Header107774');
	$rep->Info($params, $cols, $headers1, $aligns);
	$rep->NewPage();

	$rep->Font();

	$result1 = get_bank_trans_new($type, $trans_no);

	$from_trans = db_fetch($result1);

	$trans_name = $systypes_array[$from_trans['type']];

	$rep->MultiCell(190, 20, "" ,0, 'L', 0, 2, 100,140, true);
//    $rep->MultiCell(210, 30, "".$from_trans['account_code']." - ". $from_trans['bank_account_name'] ,0, 'L', 0, 2, 100,125, true);
//    $rep->MultiCell(160, 20, "". $from_trans["ref"] ,0, 'L', 0, 2, 110,110, true);

//    $rep->MultiCell(200, 60, "".htmlspecialchars_decode(get_branch_name_od($from_trans['person_id'])) ,0, 'L', 0, 2, 110,155, true);
	//$words = price_in_words($reciept['amount'], ST_BANKDEPOSIT);

//    $rep->MultiCell(60, 20, sql2date($from_trans['trans_date']),0, 'C', 0, 2, 500,100, true);
//    $rep->MultiCell(60, 20, $from_trans['ref'],0, 'C', 0, 2, 90,100, true);
//    $rep->MultiCell(100, 20, $from_trans['name'],0, 'C', 0, 2, 160,150, true);
//    $rep->MultiCell(150, 20, $from_trans['address1'],0, 'C', 0, 2, 150,180, true);


	//  $rep->MultiCell(280, 20,get_comments_string($from_trans['type'], //$from_trans['trans_no']),0, 'C', 0, 2, 186,287, true);



//    $rep->getFontSize(12);


//	$rep->MultiCell(160, 20, "Date:  ".sql2date($from_trans['trans_date']) ,0, 'L', 0, 2, 50,100, true);

//	$rep->MultiCell(160, 20, "Number :  ". $from_trans["trans_no"] ,0, 'L', 0, 2, 50,90, true);

	//  $from_trans['person_id']$from_trans['account_code']." - ". $from_trans['bank_account_name']

	if($from_trans['type'] == 42)
	{
		$rep->MultiCell(160, 20, "Cash Code:  ".$from_trans['account_code'] ,0, 'L', 0, 2, 7,115, true);
		$rep->MultiCell(170, 30, "Payment Mode:  ".htmlspecialchars_decode($from_trans['bank_account_name']) ,0, 'L', 0, 2, 7,130, true);
	}
//	else
//	{
//		$rep->MultiCell(160, 20, "Bank Code:  ".$from_trans['account_code'] ,0, 'L', 0, 2, 50,140, true);
//		$rep->MultiCell(170, 30, "Bank Title:  ".htmlspecialchars_decode($from_trans['bank_account_name']) ,0, 'L', 0, 2, 50,155, true);
//
//		$rep->MultiCell(160, 20, "Cheque Ref:  ".$from_trans['cheque'] ,0, 'L', 0, 2, 435,135, true);
//		$rep->MultiCell(160, 20, "Cheque Date:".sql2date($from_trans['cheque_date']) ,0, 'L', 0, 2, 435,145, true);
//	}
    $res=get_gl_trans11($from_trans['type'],$from_trans['trans_no']);

	$partyname = db_fetch($res);

	$rep->MultiCell(160, 20, "Voucher Receipt#: ". $from_trans["ref"] ,0, 'L', 0, 2, 8,100, true);

	$rep->MultiCell(525, 15,"Party Name:",0, 'L', 0, 0, 8,88, true);

	$rep->MultiCell(525, 15, get_customer_name($from_trans["debtor_no"]),0, 'L', 0, 2, 62, 87, true);
	$rep->MultiCell(200, 20, get_account_chart_master_reciept($from_trans),0, 'C', 0, 2, 180,630, true);

//    $rep->MultiCell(170, 30, $trans_no."++++".$type ,0, 'L', 0, 2, 90,199, true);


	$res = get_gl_trans11($from_trans['type'],$from_trans['trans_no']);
	$bank_amount="";
	while ($myrow1 = db_fetch($res)) {
		// display_error($myrow1["person_type_id"]);
		if($myrow1["person_type_id"] == 3)
		{
			$party = get_supplier_accounts($myrow1["person_id"]);
			$party_account = $party['payable_account'];
		}
		elseif($myrow1["person_type_id"] == 2)
		{
			$party = get_customer_accounts_($myrow1["person_id"]);
			$party_account = $party['receivables_account'];
		}
		$counterpartyname = get_subaccount_name($party_account, $myrow1["person_id"]);

		$counterpartyname = get_subaccount_name($party_account, $myrow1["person_id"]);

		$rep->TextCol(0, 1,	   $myrow1['account'], -2);
		$rep->TextCollines(1, 2,	get_account_chart_master_($myrow1['account']), -2);
		$rep->NewLine(-1);


		$as_dimen = get_dimension_new($myrow1['dimension_id']);

		$rep->TextCol(3, 4,	get_dimension_new($myrow1['dimension_id']), -2);



		if($myrow1['amount'] >0)
		{
			$rep->TextCol(2, 3,	number_format2($myrow1['amount']), -2);
			$dr += ($myrow1['amount']);
		}
		else
		{
			if($from_trans['account_code'] == $myrow1['account'] )
				$bank_amount += $myrow1['amount'];

			$rep->TextCol(3, 4,	number_format2($myrow1['amount']*-1), -2);
			$dr += ($myrow1['amount']);
		}



//
//		if($myrow1['amount'] >0)
//		{
//			$rep->TextCol(4, 3,	number_format2($myrow1['amount']), -2);
//			$dr += ($myrow1['amount']);
//		}
//		else
//		{
//			if($from_trans['account_code'] == $myrow1['account'] )
//				$bank_amount += $myrow1['amount'];
//
//			$rep->TextCol(3, 4,	number_format2($myrow1['amount']*-1), -2);
//			$rep->NewLine();
//
//			$rep->TextCol(0, 4,	"..............................................................................", -2);
//			$dr += ($myrow1['amount']);
//		}
				$rep->NewLine();



		$rep->setfontsize(15);

		$rep->MultiCell(220, 20, "".$trans_name ." "."Voucher" ,0, 'C', 0, 2, 4,45, true);
		$rep->setfontsize(10);

//            $rep->font('');
// 	$rep->row = $newrow;
		if ($rep->row < $rep->bottomMargin + (22 * $rep->lineHeight))
			$rep->NewPage();
		// $rep->NewLine();

		 $rep->TextCol(0, 1,	$myrow['amount'], -2);
//        alt_table_row_color($k);
	}
	/* if($from_trans['type']==22){

           $rep->TextCollines(2, 3, get_comments_string($from_trans['type'], $from_trans['trans_no']), -2);


     }*/
//
//	$res=get_gl_trans11($from_trans['type'],$from_trans['trans_no']);
//$partyname=db_fetch($res);
//	$rep->MultiCell(525, 15,"",1, 'L', 0, 2, 40,516, true);
//	$rep->MultiCell(525, 15,"Party Name:",0, 'L', 0, 0, 8,88, true);

//	$rep->MultiCell(525, 15,get_customer_name($partyname["person_id"]),0, 'L', 0, 2, 65,87, true);
//	$rep->MultiCell(48, 15,"",1, 'L', 0, 2, 40,516, true);
//	$rep->MultiCell(132, 15,"",1, 'L', 0, 2, 88,516, true);
//	$rep->MultiCell(198, 15,"Total   ",1, 'R', 0, 2, 220,516, true);
//	$rep->MultiCell(62, 15,"",1, 'L', 0, 2, 418,516, true);
//	$rep->MultiCell(40, 15,"".number_format2($dr),0, 'R', 0, 2, 100,300, true);
//	$rep->MultiCell(45, 15," ".number_format2($dr),0, 'R', 0, 2, 100,516, true);
//	global  $db_connections;
//	if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL' || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL2'){
//		$rep->MultiCell(350, 20,"Amount in Words:" ."  ".convert_number_to_words(-$bank_amount) ." "."Only",1, 'L', 0, 2, 60,560, true);
//		$rep->MultiCell(120, 20, number_format2(abs(-$bank_amount)),1, 'C', 0, 2, 420,560, true);
//	} else {
//		$rep->MultiCell(350, 20,"Amount in Words:" ."  ".convert_number_to_words($dr) ." "."Only",1, 'L', 0, 2, 60,560, true);
//		$rep->MultiCell(120, 20, number_format2(abs($dr)),1, 'R', 0, 2, 410,560, true);
//	}
	$sql = "SELECT
			SUM(ov_amount+ov_freight) AS OutStanding
			FROM ".TB_PREF."debtor_trans 
			WHERE debtor_no = ".db_escape($from_trans['debtor_no'])."
			AND type =10	";
	$result = db_query($sql,"No transactions were returned");
	$bal2 = db_fetch($result);
//Credit

	$sql = "SELECT
			SUM(ov_amount) AS Payments
			FROM ".TB_PREF."debtor_trans 
			WHERE debtor_no = ".db_escape($from_trans['debtor_no'])."
			AND type IN (10,12 ,2, 42)";
	$result = db_query($sql,"No transactions were returned");
	$bal3 = db_fetch($result);

	$TotalCredit = round2($bal3['Payments'], $dec); //Total credit side balance
	$TotalDebit = round2($bal2['OutStanding'], $dec); // Total debit side balance
	
	
$customer_record=	get_customer_detail_balance($from_trans['debtor_no'], $to=null, $all=true);
	
	
//	$CurrentAmount = number_format2($SubTotal+$myrow["ov_freight"]);
	$PreviousBalance = price_format($TotalDebit-$TotalCredit);
	//$customer_record = get_customer_detail_balance($myrow1["person_id"], $date);
	$total_balance = abs($dr);


	$rep->MultiCell(150, 25, "Previous Balance:" ,0, 'L', 0, 2, 7,300, true);
		$rep->MultiCell(150, 25,$customer_record['Balance']+$total_balance,0, 'L', 0, 2, 150,300, true);


$PREVO=$customer_record['Balance']+$total_balance;
	$rep->MultiCell(60, 15,"".price_format($total_balance),0, 'L', 0, 2, 148,315, true);
	$rep->MultiCell(150, 25, "Amount Received" ,0, 'L', 0, 2, 7,315, true);



	$total =ABS($PREVO)-$total_balance;

	$rep->MultiCell(150, 25, "Remaining Balance:" ,0, 'L', 0, 2, 7,335, true);
	$rep->MultiCell(150, 25, price_format($total) ,0, 'L', 0, 2, 148,335, true);
	//$rep->MultiCell(100, 25, "Prepared By" ,0, 'C', 0, 2, 8,670, true);
//	$rep->MultiCell(100, 25, "Approved  By" ,0, 'C', 0, 2, 170,770, true);
//	$rep->MultiCell(100, 25, "Checked  By" ,0, 'C', 0, 2, 300,770, true);
	$rep->MultiCell(200, 25, "Signature ....................................." ,0, 'C', 0, 2, 6,440, true);
	$rep->font('b');
	$rep->MultiCell(200, 25, "Thank You For Your Timely Payment" ,0, 'C', 0, 2, 7.5,480, true);

	// $rep->font('b');

	// $rep->font('');

	// $rep->NewLine(2);
	$memo = get_comments_string($from_trans['type'], $from_trans['trans_no']);

	if ($memo != "")
	{
		// $rep->NewLine(-1);
		// $rep->MultiCell(500, 316, "".$memo,0, 'L', 0, 2, 260,280, true);
		//$rep->TextColLines(2, 3, $memo, -2);
	}
	$rep->End();
}
?>