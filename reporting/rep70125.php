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
    $sql = "SELECT gl.*, cm.account_name, IFNULL(refs.reference, '') AS reference, user.real_name, 
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
    $sql = "SELECT bt.*, act.*,
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
function get_dimension_new2($id, $allow_null=false)
{
    $sql = "SELECT name FROM " . TB_PREF . "dimensions	WHERE id=" . db_escape($id);

    $result = db_query($sql, "The dimension could not be retrieved");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_dimension_new33($id, $allow_null=false)
{
    $sql = "SELECT name FROM " . TB_PREF . "dimensions	WHERE id=" . db_escape($id);

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

    $orientation = ('L');
    $dec = 0;

    $cols = array(2, 50, 180, 346,397, 440, 470);
    $headers1 = array(_('    Code'), _('   '), _('    '),
    _('Centre'),_('Centre'), _('Rs     '), _('Rs     '));
//    $headers = array(_(' Account'), _(' Account'), _(''),_('Centre'), _('Rs'), _('Rs'));

    $aligns = array('left',	'left',	'left',	'left','left','right','right');

    $params = array();

    $rep = new FrontReport(_(''), "ChartOfAccounts", 'A5_', 8, $orientation);

//    if ($orientation == 'L')
//        recalculate_cols($cols);

    $rep->Font();
    $rep->SetHeaderType('Header70125');
    $rep->Info($params, $cols, $headers1, $aligns);
    $rep->NewPage();

    $rep->Font();

    $result1 = get_bank_trans_new($type, $trans_no);

    $from_trans = db_fetch($result1);

    $trans_name = $systypes_array[$from_trans['type']];

    $rep->MultiCell(190, 20, "" ,0, 'L', 0, 2, 100,140, true);
//    $pagesize=$_SESSION["wa_current_user"]->prefs->get_pagesize();
//    if($pagesize=='A5') {
    $rep->MultiCell(160, 20, "Date:  " . sql2date($from_trans['trans_date']), 0, 'L', 0, 2, 40, 80, true);

    $rep->MultiCell(160, 20, "Number :  " . $from_trans["trans_no"], 0, 'L', 0, 2, 40, 70, true);
//    }
//    elseif($pagesize=='A4'){
//        $rep->MultiCell(160, 20, "Date:  " . sql2date($from_trans['trans_date']), 0, 'L', 0, 2, 50, 100, true);
//
//        $rep->MultiCell(160, 20, "Number :  " . $from_trans["trans_no"], 0, 'L', 0, 2, 50, 90, true);
//
//    }
    //  $from_trans['person_id']$from_trans['account_code']." - ". $from_trans['bank_account_name']
//    if($pagesize=='A5') {

//    elseif($pagesize=='A4') {
//        if ($from_trans['type'] == 41 || $from_trans['type'] == 42) {
//            $rep->MultiCell(160, 20, "Cash Code:  " . $from_trans['account_code'], 0, 'L', 0, 2, 50, 140, true);
//            $rep->MultiCell(170, 30, "Cash Title:  " . htmlspecialchars_decode($from_trans['bank_account_name']), 0, 'L', 0, 2, 50, 150, true);
//        } else {
//            $rep->MultiCell(160, 20, "Bank Code:  " . $from_trans['account_code'], 0, 'L', 0, 2, 50, 140, true);
//            $rep->MultiCell(170, 30, "Bank Title:  " . htmlspecialchars_decode($from_trans['bank_account_name']), 0, 'L', 0, 2, 50, 155, true);
//            $rep->MultiCell(160, 20, "Cheque Ref:  " . $from_trans['cheque'], 0, 'L', 0, 2, 435, 135, true);
//            $rep->MultiCell(160, 20, "Cheque Date:" . sql2date($from_trans['cheque_date']), 0, 'L', 0, 2, 435, 145, true);
//        }
//        $rep->MultiCell(160, 20, "Journal Ref:   ". $from_trans["ref"] ,0, 'L', 0, 2, 435,155, true);
//    }



    $rep->MultiCell(200, 20, get_account_chart_master_reciept($from_trans),0, 'C', 0, 2, 180,630, true);

//    $rep->MultiCell(170, 30, $trans_no."++++".$type ,0, 'L', 0, 2, 90,199, true);


//    $k = 0;
    $res=get_gl_trans11($from_trans['type'],$from_trans['trans_no']);
    $bank_amount="";
    while ($myrow1 = db_fetch($res))
    {

        $oldrow = $rep->row;
        $rep->row = $oldrow;
        $rep->TextCol(0, 1,	   $myrow1['account'], -2);

//        $as_dimen = get_dimension_new2($myrow1['dimension_id']);
        $as_dimen =  get_dimension_new33($myrow1['dimension_id']);
        $as_dimen2 =  get_dimension_new33($myrow1['dimension2_id']);
        $rep->TextCol(3, 4,	$as_dimen, -2);
        $rep->TextCol(4, 5,	$as_dimen2, -2);
//        $rep->NewLine(-1);
//        $as_dimen = get_dimension_new($myrow1['dimension2_id']);
//        $rep->TextCol(3, 4,	$as_dimen, -2);
        if($myrow1['amount'] > 0)
        {
            $rep->TextCol(5, 6,	number_format2($myrow1['amount']), -2);
            $dr += ($myrow1['amount']);
        }
        else
        {
            if($from_trans['account_code'] == $myrow1['account'] )
                $bank_amount += $myrow1['amount'];


            $rep->TextCol(6, 7,	number_format2($myrow1['amount']*-1), -2);
        }
       
        $rep->TextCollines(1, 2,	get_account_chart_master_($myrow1['account']), -2);
        // $rep->NewLine(-1);
        if($myrow1['amount'] > 0)
 {
if($from_trans['type']==22 || $from_trans['type']==12)
{
  $rep->TextCollines(2, 3, get_comments_string($from_trans['type'], $from_trans['trans_no']), -2);
}
else
{$rep->NewLine(-1);
         $rep->TextCollines(2, 3, $myrow1['memo_'], -2);
}
// }else{
   //  $rep->TextCollines(2, 3, $myrow1['memo_'], -2);
     
 }

//        $rep->TextColLines(2, 3,$myrow1['memo_'], -2);
//        $rep->NewLine(1);
//            $rep->font('b');
//            $rep->MultiCell(170, 15, "Account",1, 'C', 0, 2, 40,181, true);
//            $rep->MultiCell(206, 15, "",1, 'C', 0, 2, 210,181, true);
//            $rep->MultiCell(55, 15, "Cost",1, 'C', 0, 2, 416,181, true);
//            $rep->MultiCell(43, 15, "Debit",1, 'C', 0, 2, 471,181, true);
//            $rep->MultiCell(51, 15, "Credit",1, 'C', 0, 2, 514,181, true);
//            $rep->font('');
        // $rep->MultiCell(48, 316, "",1, 'C', 0, 2, 40,196, true);
        // $rep->MultiCell(132, 331, "",1, 'C', 0, 2, 88,181, true);
        // $rep->MultiCell(198, 331, "",1, 'C', 0, 2, 220,181, true);
        // $rep->MultiCell(60, 316, "",1, 'C', 0, 2, 418,196, true);
        // $rep->MultiCell(45, 316, "",1, 'C', 0, 2, 520,196, true);
        // $rep->MultiCell(525, 331, "",1, 'C', 0, 2, 40,181, true);
//            $rep->NewLine(1);
//            $rep->font('b');
        $rep->setfontsize(19);
        $rep->MultiCell(220, 20, "".$trans_name ." "."Voucher" ,0, 'C', 0, 2, 170,70, true);
        $rep->setfontsize(8.5);
//            $rep->font('');

//        if($pagesize=='A5') {
        if ($rep->row < $rep->bottomMargin + (12 * $rep->lineHeight))
            $rep->NewPage();
//        }
//        elseif($pagesize=='A4') {
//            if ($rep->row < $rep->bottomMargin + (25 * $rep->lineHeight))
//                $rep->NewPage();
//        }//        $rep->NewLine();

        // $rep->TextCol(0, 1,	$myrow['amount'], -2);
//        alt_table_row_color($k);
    }
//    $pagesize=$_SESSION["wa_current_user"]->prefs->get_pagesize();
//    if($pagesize=='A4') {
//
//        $rep->MultiCell(525, 15, "", 1, 'L', 0, 2, 40, 572, true);
//        $rep->MultiCell(48, 15, "", 1, 'L', 0, 2, 40, 572, true);
//        $rep->MultiCell(132, 15, "", 1, 'L', 0, 2, 88, 572, true);
//        $rep->MultiCell(159, 15, "Total   ", 1, 'R', 0, 2, 220, 572, true);
//        $rep->MultiCell(42, 15, " " . $dr, 1, 'R', 0, 2, 478, 572, true);
//        $rep->MultiCell(45, 15, " " . $dr, 1, 'R', 0, 2, 520, 572, true);
//
//        $rep->MultiCell(350, 20, "Amount in Words:" . "  " . convert_number_to_words($dr) . " " . "Only", 1, 'L', 0, 2, 60, 620, true);
//        $rep->MultiCell(120, 20, number_format2(abs($dr)), 1, 'C', 0, 2, 420, 620, true);
//
//        $rep->MultiCell(100, 25, "______________________", 0, 'L', 0, 2, 40, 755, true);
//        $rep->MultiCell(100, 25, "______________________", 0, 'L', 0, 2, 170, 755, true);
//        $rep->MultiCell(100, 25, "______________________", 0, 'L', 0, 2, 300, 755, true);
//        $rep->MultiCell(100, 25, "______________________", 0, 'L', 0, 2, 430, 755, true);
//        $rep->MultiCell(100, 25, "Prepered By", 0, 'C', 0, 2, 40, 770, true);
//        $rep->MultiCell(100, 25, "Approved  By", 0, 'C', 0, 2, 170, 770, true);
//        $rep->MultiCell(100, 25, "Checked  By", 0, 'C', 0, 2, 300, 770, true);
//        $rep->MultiCell(100, 25, "Recieved  By", 0, 'C', 0, 2, 430, 770, true);
//    }
    if ($from_trans['type'] == 41 || $from_trans['type'] == 42) {
        $rep->MultiCell(160, 20, "Cash Code:  " . $from_trans['account_code'], 0, 'L', 0, 2, 40, 100, true);
        $rep->MultiCell(170, 30, "Cash Title:  " . htmlspecialchars_decode($from_trans['bank_account_name']), 0, 'L', 0, 2, 40, 110, true);
    } else {
        $rep->MultiCell(160, 20, "Bank Code:  " . $from_trans['account_code'], 0, 'L', 0, 2, 50, 120, true);
        $rep->MultiCell(300, 10, "Bank Title:  " . htmlspecialchars_decode($from_trans['bank_account_name']), 0, 'L', 0, 2, 50, 130, true);
        $rep->MultiCell(160, 20, "Cheque Ref:  " . $from_trans['cheque'], 0, 'L', 0, 2, 435, 110, true);
        $rep->MultiCell(160, 20, "Cheque Date:" . sql2date($from_trans['cheque_date']), 0, 'L', 0, 2, 435, 120, true);
    }
    $rep->MultiCell(160, 20, "Journal Ref:   ". $from_trans["ref"] ,0, 'L', 0, 2, 435,130, true);
//    }
//    elseif($pagesize=='A5') {
    $rep->MultiCell(525, 15, "", 1, 'L', 0, 2, 40, 295, true);
    $rep->MultiCell(48, 15, "", 1, 'L', 0, 2, 40, 295, true);
    $rep->MultiCell(132, 15, "", 1, 'L', 0, 2, 88, 295, true);
    $rep->MultiCell(159, 15, "Total   ", 1, 'R', 0, 2, 220, 295, true);
    $rep->MultiCell(42, 15, " " . number_format2($dr), 1, 'R', 0, 2, 478, 295, true);
    $rep->MultiCell(45, 15, " " . number_format2($dr), 1, 'R', 0, 2, 520, 295, true);
    global  $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL' || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL2') {
        $rep->MultiCell(350, 20, "Amount in Words:" . "  " . convert_number_to_words(-$bank_amount) . " " . "Only", 1, 'L', 0, 2, 60, 324, true);
        $rep->MultiCell(120, 20, number_format2(abs(-$bank_amount)), 1, 'C', 0, 2, 420, 324, true);
    }
    else{
        $rep->MultiCell(350, 20, "Amount in Words:" . "  " . convert_number_to_words($dr) . " " . "Only", 1, 'L', 0, 2, 60, 324, true);
        $rep->MultiCell(120, 20, number_format2(abs($dr)), 1, 'C', 0, 2, 420, 324, true);
    }

    $rep->MultiCell(100, 25, "___________________", 0, 'L', 0, 2, 40, 390, true);
    $rep->MultiCell(100, 25, "___________________", 0, 'L', 0, 2, 170, 390, true);
    $rep->MultiCell(100, 25, "___________________", 0, 'L', 0, 2, 300, 390, true);
    $rep->MultiCell(100, 25, "___________________", 0, 'L', 0, 2, 430, 390, true);
    $rep->MultiCell(100, 25, "Prepered By", 0, 'C', 0, 2, 40, 405, true);
    $rep->MultiCell(100, 25, "Approved  By", 0, 'C', 0, 2, 170, 405, true);
    $rep->MultiCell(100, 25, "Checked  By", 0, 'C', 0, 2, 300, 405, true);
    $rep->MultiCell(100, 25, "Recieved  By", 0, 'C', 0, 2, 430, 405, true);
//    }
    $rep->End();
    $rep->NewLine(2);

    $memo = get_comments_string($from_trans['type'], $from_trans['trans_no']);

    if ($memo != "")
    {
//        $rep->MultiCell(500, 316, "".$memo,0, 'L', 0, 2, 260,280, true);
        $rep->TextColLines(2, 3, $memo, -2);
    }
}
?>