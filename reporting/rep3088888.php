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

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $currency = $_POST['PARAM_2'];
    $email = $_POST['PARAM_3'];
    $comments = $_POST['PARAM_4'];
    $orientation = $_POST['PARAM_5'];


//    if ($destination)
//        include_once($path_to_root . "/reporting/includes/excel_report.inc");
//    else
    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ('L');
    $dec = 0;

    $cols = array(4, 50, 180, 230, 280, 340, 380, 430, 480, 540);

    // $headers in doctext.inc
    $aligns = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');
//    $headers = array(_(' Account'), _(' Account'), _(''),_('Centre'), _('Rs'), _('Rs'));

//    $aligns = array('left',	'left',	'left','left','right','right');

    $params = array();

    $rep = new FrontReport(_(''), "ChartOfAccounts", 'A5_BNT', 7, $orientation);

//    if ($orientation == 'L')
//        recalculate_cols($cols);

    $rep->Font();
    $rep->SetHeaderType('Header3088888');
    $rep->Info($params, $cols, null, $aligns);
    $rep->NewPage();

    $rep->Font();


    for ($i = $from; $i <= $to; $i++) {
        $myrow = get_sales_order_header($i,ST_SALESORDER);
        $bank_amount = "";
        $result = get_sales_order_details($i,ST_SALESORDER);
        while ($myrow2 = db_fetch($result)) {
            $data = get_purchase_data($myrow['supplier_id'], $myrow2['stk_code']);
            if ($data !== false)
            {
                if ($data['supplier_description'] != "")
                    $myrow2['description'] = $data['supplier_description'];
                if ($data['suppliers_uom'] != "")
                    $myrow2['units'] = $data['suppliers_uom'];
                if ($data['conversion_factor'] != 1)
                {
                    $myrow2['unit_price'] = round2($myrow2['unit_price'] * $data['conversion_factor'], user_price_dec());
                    $myrow2['quantity'] = round2($myrow2['quantity'] / $data['conversion_factor'], user_qty_dec());
                }
            }
            $Net = round2(($myrow2["unit_price"] * ($myrow2["amount3"] )), user_price_dec());



            $prices[] = $Net;
            $items[] = $myrow2['stk_code'];

            $dec2 = 0;
            $DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);

 $item = get_item($myrow2['stk_code']);
            $pref=get_company_prefs();
            if($pref['alt_uom'] == 1 ) {
                // $item = get_item($myrow2['stk_code']);



                $DisplayQty = number_format2($myrow2["quantity"]  ,get_qty_dec($myrow2['stk_code']));

                if($item['units'] != $myrow2['units_id'] ){
                    $Net = round2(($myrow2["unit_price"] * ($myrow2["quantity"]  * $item['con_factor'] )), user_price_dec());
                }
                else{
                    $Net = round2(($myrow2["unit_price"] * ($myrow2["quantity"]   )), user_price_dec());
                }


            }
            $SubTotal += $Net;

// 			$DisplayQty = number_format2($myrow2["quantity_ordered"]  ,get_qty_dec($myrow2['item_code']));
            $DisplayNet = number_format2($Net,$dec);



            $rep->TextCol(6, 7,	$myrow2['units_id'], -2);


            $rep->TextCol(2, 3,	$item['amount2'].	$item['text3'], -2);

            $rep->TextCol(3, 4,	$item['text4'].	$item['text1'], -2);


            $csv = str_replace('MICRON', 'MIC', $item['text2']);

            $rep->TextCol(4, 5,	$item['amount6'].	$csv, -2);
            // $rep->TextCol(4, 5,	$item['amount6'].	$item['text2'], -2);


            if($item['units'] != $myrow2['units_id'] ){
                $rep->TextCol(5, 6,	number_format2($myrow2["quantity"]  * $item['con_factor'],2), -2);
            }
            else{
                $rep->TextCol(5, 6,	number_format2($myrow2["quantity"]  ,2), -2);
            }

            $rep->TextCol(7, 8,	number_format2($myrow2["unit_price"]), -2);
            $rep->TextCol(8, 9,	$DisplayNet, -2);
            $rep->TextCol(9, 10,	sql2date($myrow2['date3']), -2);

            $rep->TextCol(0, 1,	$myrow2['stk_code'], -2);
            $rep->TextCollines(1, 2,	"".get_category_name($item['category_id']), -2);





            if ($rep->row < $rep->bottomMargin + (12 * $rep->lineHeight))
                $rep->NewPage();

        }

$rep->font('b');
$rep->MultiCell(600, 150, "",1, 'C', 0, 2, 20,73, true);
//----------First Line-----------------------------
$rep->MultiCell(120, 20, "Proforma Invoice Number: ",1, 'C', 0, 2, 20,73, true);
$rep->MultiCell(120, 20, "Date:",1, 'C', 0, 2, 140,73, true);
$rep->MultiCell(120, 20, "Terms of Delivery:",1, 'C', 0, 2, 260,73, true);
$rep->MultiCell(120, 20, "Country of Origin::",1, 'C', 0, 2, 380,73, true);
$rep->MultiCell(120, 20, "Port of Loading:",1, 'C', 0, 2, 500,73, true);


$rep->MultiCell(120, 50, "",1, 'C', 0, 2, 20,73, true);
$rep->MultiCell(120, 50, "",1, 'C', 0, 2, 140,73, true);
$rep->MultiCell(120, 50, "",1, 'C', 0, 2, 260,73, true);
$rep->MultiCell(120, 50, "",1, 'C', 0, 2, 380,73, true);
$rep->MultiCell(120, 50, "",1, 'C', 0, 2, 500,73, true);

//-------------values-------------------------
$rep->MultiCell(120, 50, $myrow['order_no'],0, 'C', 0, 2, 10,95, true);
$rep->MultiCell(120, 50, $myrow['ord_date'],0, 'C', 0, 2, 140,95, true);
$rep->MultiCell(120, 50, get_f_combo1_name($myrow['f_combo1']),0, 'C', 0, 2, 260,95, true);
$rep->MultiCell(120, 50,  $myrow['payment_terms'],0, 'C', 0, 2, 380,95, true);
$rep->MultiCell(120, 50,  $myrow['h_text3'],0, 'C', 0, 2, 500,95, true);


//----------Second Line-----------------------------


$rep->MultiCell(160, 20, "Port of Discharge:",1, 'C', 0, 2, 20,123, true);
$rep->MultiCell(160, 20, "Notify Party: ",1, 'C', 0, 2, 180,123, true);
$rep->MultiCell(160, 20, "Other References: ",1, 'C', 0, 2, 340,123, true);
$rep->MultiCell(120, 20, "Terms of Payment:",1, 'C', 0, 2, 500,123, true);
$rep->MultiCell(120, 20, "Additional Information: ",0, 'C', 0, 2, 10,173, true);

$rep->MultiCell(120, 20, "Currency: ",0, 'C', 0, 2, 90,173, true);
$rep->MultiCell(120, 20, "Bank Name and address: ",0, 'C', 0, 2, 200,173, true);
$rep->MultiCell(120, 20, "Account Details: ",0, 'C', 0, 2, 400,173, true);




$rep->MultiCell(160, 45, "",1, 'C', 0, 2, 20,123, true);
$rep->MultiCell(160, 45, "",1, 'C', 0, 2, 180,123, true);
$rep->MultiCell(160, 45, "",1, 'C', 0, 2, 340,123, true);
$rep->MultiCell(120, 45, "",1, 'C', 0, 2, 500,123, true);


//----------------Values ------------------------------

$rep->MultiCell(160, 45, $myrow['h_text2'],0, 'C', 0, 2, 20,148, true);
$rep->MultiCell(160, 45, "",0, 'C', 0, 2, 180,148, true);
$rep->MultiCell(160, 45, $myrow['f_comment2'],0, 'C', 0, 2, 340,148, true);
$rep->MultiCell(120, 45,get_payment_term_name($myrow['payment_terms']),0, 'C', 0, 2, 500,148, true);

//$this->MultiCell(120, 65, "",1, 'C', 0, 2, 500,123, true);



$rep->MultiCell(420, 60, "
1. Shipment within 1 month after receipt of Signed Contract / Letter of Credit whichever applies.
2. To be advised through Santander UK PLC bank, swift code ABBYGB2L
3. To allow Trans-shipment and Partial shipment
4. To be open for negotiation with any UK Bank
5. Quantity tolerance +/- 10%
6. Reimbursement charges on account of applicant
7. Proforma valid for 7 working days",0, 'L', 0, 2, 10,408, true);
        $rep->End();
        $rep->NewLine(2);


    }
}
?>