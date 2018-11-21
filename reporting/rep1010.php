<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Detailed
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");

//----------------------------------------------------------------------------------------------------

// trial_inquiry_controls();
print_customer_balances();

/*
function get_open_balance($debtorno, $to, $convert)
{
	if($to)
		$to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 0)) AS charges,
    	SUM(IF(t.type <> ".ST_SALESINVOICE.",
    	(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= " * -1, 0)) AS credits,
		SUM(t.alloc";
	if ($convert)
		$sql .= " * rate";
	$sql .= ") AS Allocated,
		SUM(IF(t.type = ".ST_SALESINVOICE.",
			(t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ", 
    	((t.ov_amount + t.ov_gst + t.ov_freight + t.ov_freight_tax + t.ov_discount) * -1 + t.alloc)";
    if ($convert)
    	$sql .= " * rate";
    $sql .= ")) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
		." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
    	$sql .= " AND t.tran_date < '$to'";
	$sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}
*/
//function get_invoice_footer_fields_data($order_no)
//{
//    $sql = "SELECT *
//    FROM ".TB_PREF."sales_orders
//    WHERE order_no = ".db_escape($order_no)."
//    ";
//    $result = db_query($sql, 'Error');
//    $fetch = db_fetch($result);
//    return $fetch;
//}
function get_open_balance($debtorno, $to)
{
    if($to)
        $to = date2sql($to);

    $sql = "SELECT SUM(IF(t.type = ".ST_SALESINVOICE." OR (t.type = ".ST_JOURNAL." AND t.ov_amount>0) OR t.type = ". ST_BANKPAYMENT." OR t.type = ". ST_CPV.",
     	-abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc+ t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2), 0)) AS charges,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0) AND NOT (t.type = ". ST_BANKPAYMENT.") AND NOT (t.type = ". ST_CPV."),
     	abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) * -1, 0)) AS credits,";
    $sql .= "SUM(IF(t.type != ".ST_SALESINVOICE." AND NOT(t.type = ".ST_JOURNAL." AND t.ov_amount>0), t.alloc * -1, t.alloc)) AS Allocated,";

    $sql .=	"SUM(IF(t.type = ".ST_SALESINVOICE.", 1, -1) *
 			(abs(t.ov_amount + t.ov_gst + supply_disc + service_disc + fbr_disc +  srb_disc + t.ov_freight + t.ov_freight_tax + t.ov_discount - t.discount1 - t.discount2) - abs(t.alloc))) AS OutStanding
		FROM ".TB_PREF."debtor_trans t
    	WHERE t.debtor_no = ".db_escape($debtorno)
        ." AND t.type <> ".ST_CUSTDELIVERY;
    if ($to)
        $sql .= " AND t.tran_date < '$to'";
    $sql .= " GROUP BY debtor_no";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function get_transactions($debtorno, $from, $to, $sys_type)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*,
	(".TB_PREF."debtor_trans.ov_amount +".TB_PREF."debtor_trans.supply_disc + 
	".TB_PREF."debtor_trans.service_disc + ".TB_PREF."debtor_trans.fbr_disc +
	".TB_PREF."debtor_trans.srb_disc+".TB_PREF."debtor_trans.ov_gst +
	".TB_PREF."debtor_trans.ov_freight +".TB_PREF."debtor_trans.ov_freight_tax + 
	".TB_PREF."debtor_trans.ov_discount - ".TB_PREF."debtor_trans.discount1 - 
	".TB_PREF."debtor_trans.discount2) AS TotalAmount, ".TB_PREF."debtor_trans.alloc AS Allocated";
    if ($sys_type == -1)
        $sql .= ", ((".TB_PREF."debtor_trans.type = ".ST_SALESINVOICE.")
		AND ".TB_PREF."debtor_trans.due_date < '$to') AS OverDue";
    $sql .=  " FROM ".TB_PREF."debtor_trans
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans.type <> ".ST_CUSTDELIVERY;
    if ($sys_type != -1)
        $sql .= " AND ".TB_PREF."debtor_trans.type =".db_escape($sys_type);
    $sql .=  " ORDER BY ".TB_PREF."debtor_trans.tran_date";


    return db_query($sql,"No transactions were returned");
}

function get_memo_($type,$type_no)
{
    $sql = "SELECT * FROM ".TB_PREF."gl_trans
    	WHERE type = ".db_escape($type) ." AND type_no = ".db_escape($type_no)  ." ORDER BY counter LIMIT 1";
    $result = db_query($sql,"No transactions were returned");
    $data = db_fetch($result);
    return $data;
}
function get_transactions2($debtorno, $from, $to, $transno)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*, ".TB_PREF."debtor_trans_details.*
		
    	FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtor_trans_details
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans_details.debtor_trans_type  =  ".TB_PREF."debtor_trans.type
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no 
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".db_escape($transno)."
	AND ".TB_PREF."debtor_trans_details.debtor_trans_type = 10	
    	ORDER BY ".TB_PREF."debtor_trans.tran_date ";


    return db_query($sql,"No transactions were returned");
}
function get_transactions3($debtorno, $from, $to, $transno)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."debtor_trans.*, ".TB_PREF."debtor_trans_details.*
		
    	FROM ".TB_PREF."debtor_trans, ".TB_PREF."debtor_trans_details
    	WHERE ".TB_PREF."debtor_trans.tran_date >= '$from'
		AND ".TB_PREF."debtor_trans.tran_date <= '$to'
		AND ".TB_PREF."debtor_trans.debtor_no = ".db_escape($debtorno)."
		AND ".TB_PREF."debtor_trans_details.debtor_trans_type  =  ".TB_PREF."debtor_trans.type
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".TB_PREF."debtor_trans.trans_no 
		AND ".TB_PREF."debtor_trans_details.debtor_trans_no =  ".db_escape($transno)."
	AND ".TB_PREF."debtor_trans_details.debtor_trans_type = 11	
    	ORDER BY ".TB_PREF."debtor_trans.tran_date";


    return db_query($sql,"No transactions were returned");
}


//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromcust = $_POST['PARAM_2'];
    $dimension = $_POST['PARAM_3'];
    $dimension2 = $_POST['PARAM_4'];
    $sys_type = $_POST['PARAM_5'];
    $area = $_POST['PARAM_6'];
    $folk = $_POST['PARAM_7'];
    $currency = $_POST['PARAM_8'];
    $no_zeros = $_POST['PARAM_9'];
    $comments = $_POST['PARAM_10'];
    $orientation = $_POST['PARAM_11'];
    $destination = $_POST['PARAM_12'];

    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");
    $orientation = ($orientation ? 'L' : 'P');
    if ($fromcust == ALL_TEXT)
        $cust = _('All');
    else
        $cust = get_customer_name($fromcust);
    $dec = user_price_dec();

    if ($area == ALL_NUMERIC)
        $area = 0;

    if ($area == 0)
        $sarea = _('All Areas');
    else
        $sarea = get_area_name($area);

    if ($folk == ALL_NUMERIC)
        $folk = 0;

    if ($folk == 0)
        $salesfolk = _('All Sales Man');
    else
        $salesfolk = get_salesman_name($folk);

    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');
    if($sys_type!=10)
    {
        $cols = array(0, 80, 150, 200,  230, 260, 290, 335, 400, 460, 530, 600);

        $headers = array(_('No.'), _('Date/Narration'), _(''), _('Rate'), _('Qty'), _('Disc.'),
            _('Total'), 	_('Dr'), _('Cr'), _('Balance'));

        $aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right');

    }
    else
    {
        $cols = array(0, 60, 65, 70,  250, 260, 290, 335, 400, 460, 530, 600);

        $headers = array(_('INV.#'), _(''), _(''), _('Product'), _(''), _(''),
            _('Qty in Crtn'), 	_('Qty in Pack'), _('Rate'), _('Amount'));

        $aligns = array('left',	'left',	'left',	'left',	'right', 'right', 'right', 'right', 'right', 'right', 'right');

        $params =   array( 	0 => $comments,
            1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to));
    }

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
        2 => array('text' => _('Customer'), 'from' => $cust,   	'to' => ''),
        3 => array('text' => _('Zone'), 		'from' => $sarea, 		'to' => ''),
        4 => array('text' => _('Sales Man'), 		'from' => $salesfolk, 	'to' => ''),
        5 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
        6 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));
    $rep = new FrontReport(_('Customer Statements'), "CustomerBalancesDetailed1", user_pagesize());
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

    $grandtotal = array(0,0,0,0);

    $deb_sum_total_grandtotal = array(0,0,0,0);
    $a = 0;


    $sql = "SELECT ".TB_PREF."debtors_master.debtor_no AS DebtorNo,
			".TB_PREF."debtors_master.name AS Name
		FROM ".TB_PREF."debtors_master
		INNER JOIN ".TB_PREF."cust_branch
			ON ".TB_PREF."debtors_master.debtor_no=".TB_PREF."cust_branch.debtor_no
		INNER JOIN ".TB_PREF."areas
			ON ".TB_PREF."cust_branch.area = ".TB_PREF."areas.area_code			
		INNER JOIN ".TB_PREF."salesman
			ON ".TB_PREF."cust_branch.salesman=".TB_PREF."salesman.salesman_code";

    $sql .= " WHERE ".TB_PREF."cust_branch.inactive != 1";

    if ($fromcust != ALL_TEXT )
    {
        if ($area != 0 || $folk != 0)
            continue;
        $sql .= " AND ".TB_PREF."debtors_master.debtor_no=".db_escape($fromcust);
    }

    elseif ($dimension != 0 )

    {
        $sql .= " AND ".TB_PREF."debtors_master.dimension_id=".db_escape($dimension);
    }

    elseif ($dimension2 != 0 )

    {
        $sql .= " AND ".TB_PREF."debtors_master.dimension2_id=".db_escape($dimension2);
    }

    elseif ($area != 0)
    {
        if ($folk != 0)
            $sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk)."
						AND ".TB_PREF."areas.area_code=".db_escape($area);
        else
            $sql .= " WHERE ".TB_PREF."areas.area_code=".db_escape($area);
    }
    elseif ($folk != 0 )
    {
        $sql .= " WHERE ".TB_PREF."salesman.salesman_code=".db_escape($folk);
    }



    $sql .= " GROUP BY Name ORDER BY Name";
    $result = db_query($sql, "The customers could not be retrieved");
    $num_lines = 0;

    while ($myrow = db_fetch($result))
    {
//		if (!$convert && $currency != $myrow['curr_code']) continue;
        $accumulate = 0;
        $deb_sum_total = array(0,0,0,0);
        $deb_sum = array(0,0,0,0);

        $bal = get_open_balance($myrow['DebtorNo'], $from, $convert);
        $init[0] = $init[1] = 0.0;
        $init[0] = round2(abs($bal['charges']), $dec);
        $init[1] = round2(Abs($bal['credits']), $dec);
        $init[2] = round2($bal['OutStanding'], $dec);;

        $init[2] = $init[0] - $init[1];
        $accumulate += $init[2];

        $res = get_transactions($myrow['DebtorNo'], $from, $to, $sys_type);
        if ($no_zeros && db_num_rows($res) == 0) continue;

        $num_lines++;
        $rep->fontSize += 2;
        if($sys_type!=10)
        {
            $rep->Font('bold');
            $rep->TextCol(0, 3, $myrow['Name']);
            $rep->Font();
        }

        $rep->fontSize -= 2;
        if($sys_type!=10)
        {
            $rep->TextCol(3, 5,	_("Open Balance"));
            $rep->AmountCol(7, 8, $init[0], $dec);
            $rep->AmountCol(8, 9, $init[1], $dec);
            $rep->AmountCol(9, 10, $init[2], $dec);
        }
        $total =  array(0,0,0,0);
        $grandtotal = array(0,0,0,0);
        for ($i = 0; $i < 3; $i++)
        {
            $total[$i] += $init[$i];
            $grandtotal[$i] +=$total[$i];
        }
        $rep->NewLine(1, 2);
        if (db_num_rows($res)==0)
            continue;
        $rep->Line($rep->row + 4);
        while ($trans = db_fetch($res))
        {
            if ($no_zeros && floatcmp($trans['TotalAmount']) == 0) continue;

            if($sys_type!=10)
            {
                $rep->Line($rep->row - 2); //new added
                $rep->NewLine(1, 2);

                $rep->Font('bold');
                $rep->TextCol(0, 1,	$trans['reference']);
                $rep->Font();

                $rep->DateCol(1, 2,	$trans['tran_date'], true);
            }

            $item[0] = $item[1] = $item[3] = 0.0;
            if ($convert)
                $rate = $trans['rate'];
            else
                $rate = 1.0;
            if ($trans['type'] == ST_CUSTCREDIT || $trans['type'] == ST_CUSTPAYMENT || $trans['type'] == ST_BANKDEPOSIT || $trans['type'] == ST_CRV)
                $trans['TotalAmount'] *= -1;
            $a =get_memo_($trans['type'], $trans['trans_no']);
            //	$rep->TextColLines(2, 3, _('') . " ". $a['memo_']);
            // 	$rep->NewLine(-1);
            $sum_totalamount=0;
            if ($trans['TotalAmount'] > 0.0)
            {
                $foo = true;
                $a = 1;
                if($sys_type!=10)
                {
                    $item[0] = round2(abs($trans['TotalAmount']) * $rate, $dec);
                    $rep->TextCol(2, 4, $systypes_array[$trans['type']]);
                    $rep->Font('bold');
                    $rep->AmountCol(7, 8, $item[0], $dec);
                    $accumulate += $item[0];
                    $rep->Font();
                    $rep->NewLine();
                }
                $res2 = get_transactions2($myrow['DebtorNo'], $from, $to, $trans['trans_no']);
                if($sys_type==10)
                { $invoice_data=get_sales_order_header($trans['order_'], ST_SALESORDER);
                $date= sql2date($trans['tran_date']);
                    $rep->NewLine();
                    $rep->font('b');
                    $rep->TextCol(0, 1, $trans['reference']);
                    $rep->TextCol(2, 11,	$date ."  ".$trans['text_1']."/".$trans['cheque_date']."  ".$invoice_data['f_text2']."  ".$invoice_data['f_text3']);
//                    $rep->TextCol(5, 7,$trans['text_1']."/".$trans['cheque_date']."  ".$invoice_data['f_text2']."  ".$invoice_data['f_text3']);

                    $rep->font('');
                }
                while ($trans2 = db_fetch($res2))
                {

                    if($sys_type==10)
                    {
                        $item=get_item($trans2['stock_id']);
                        $cartons=$trans2["quantity"]/$item["con_factor"];
                        if($trans2['quantity']==0)continue;
                        $rep->NewLine();
                        $str =  $trans2['description'];
                        // if (strlen($str) > 15)
                        //             $str = substr($str, 0, 41).'...';
                        $rep->TextCol(3, 7, $str, $dec);

                        // $rep->TextCol(1, 3, $myrow['Name']);
                        $rep->AmountCol(8, 9, $trans2['unit_price'], $dec);
                        $rep->AmountCol(6, 7, $cartons, $dec);
                        $tot_cartons += $cartons;
                        $grand_cartons_total += $cartons;
                        $rep->AmountCol(7, 8, $trans2['quantity'], $dec);
                        $DiscountAmount= (($trans2['unit_price'] * $trans2['quantity']) * $trans2['discount_percent']);

                        $TotalAmount = (($trans2['unit_price'] * $trans2['quantity']) - $DiscountAmount);
                        $invoice_total +=$TotalAmount;
                        $grand_invoice_total +=$TotalAmount;
                        $rep->AmountCol(9, 10, $TotalAmount, $dec);
                        $rep->NewLine();

                        $rep->font('i');
//                        if($trans['text_1']!='')
//                        $rep->TextCol(0, 3,	"(".$trans['text_1']."/".$trans['cheque_date'].")");
//                        else
//                            $rep->TextCol(0, 3,	$trans['text_1']."/".$trans['cheque_date']);
//
//                        if($invoice_data['f_text2']!='')
//                        $rep->TextCol(3, 6,	"(".$invoice_data['f_text2'].")");
//                        else
//                            $rep->TextCol(3, 6,	$invoice_data['f_text2']);
//
//                        if($invoice_data['f_text3']!='')
//                        $rep->TextCol(6, 8,	 "(".$invoice_data['f_text3'].")");
//                        else
//                            $rep->TextCol(6, 8,	 $invoice_data['f_text3']);

                        $rep->font('');
                        $rep->NewLine(0.2);
                        $rep->TextCol(0, 11, _("................................................................................................................................................................................................................."), $dec);
                    }
                    else
                    {
                        $rep->NewLine();
                        $str =  $trans2['description'];
                        if (strlen($str) > 15)
                            $str = substr($str, 0, 41).'...';
                        $rep->TextCol(0, 4, $str, $dec);
                        $rep->AmountCol(3, 4, $trans2['unit_price'], $dec);
                        $rep->AmountCol(4, 5, $trans2['quantity'], $dec);
                        $DiscountAmount= (($trans2['unit_price'] * $trans2['quantity']) * $trans2['discount_percent']);
                        $rep->AmountCol(5, 6, $DiscountAmount , $dec);
                        $TotalAmount = (($trans2['unit_price'] * $trans2['quantity']) - $DiscountAmount);
                        $rep->AmountCol(6, 7, $TotalAmount, $dec);
                        $sum_totalamount += $TotalAmount;



                        $rep->NewLine(0.2);
                        $rep->TextCol(0, 11, _("................................................................................................................................................................................................................."), $dec);
// 		$rep->Line($rep->row - 2); //new added				

                        $deb_sum['0'] = $trans2['unit_price'];
                        $deb_sum['1'] = $trans2['quantity'];
                        $deb_sum['2'] = $DiscountAmount;
                        $deb_sum['3'] = $TotalAmount;

                        for ($i = 0; $i < 4; $i++)
                        {
                            $deb_sum_total[$i] += $deb_sum[$i];
                            $deb_sum_total_grandtotal[$i] += $deb_sum[$i];
                        }
                        $disc1 =$trans2['discount1'];
                        $disc2 =$trans2['discount2'];

                    }//while
                    if($sys_type!=10)
                    {
                        $rep->NewLine();
                        $rep->TextCol(0, 1, "Sub Total", $dec);
                        $rep->AmountCol(6, 7, $sum_totalamount, $dec);

                        if($disc1)
                        {
                            $rep->NewLine();
                            $rep->TextCol(0, 1, "Discount 1", $dec);
                            $rep->AmountCol(6, 7, $disc1, $dec);
                            $rep->TextCol(5, 6, ($disc1/$sum_totalamount)*100 ."%");
                        }

                        if($disc2)
                        {
                            $rep->NewLine();
                            $rep->TextCol(0, 1, "Discount 2", $dec);
                            $rep->AmountCol(6, 7,  $disc2, $dec);

                        }
                        $rep->NewLine();
                        $rep->TextCol(0, 1, "Grand Total", $dec);
                        $rep->AmountCol(6, 7, $sum_totalamount - $disc1 - $disc2, $dec);
                    }
                }
                if($sys_type==10)
                {
                    $rep->NewLine();
                    $rep->Font('bold');
                    $rep->SetTextColor(255, 0, 0);
                    $rep->TextCol(0, 3, _('Total'));
                    $rep->AmountCol(9, 10,$invoice_total, $dec);
                    $rep->AmountCol(6, 7,$tot_cartons, $dec);
                    $rep->SetTextColor(0, 0, 0);
                    $rep->Font('');
                    $invoice_total=0;
                    $tot_cartons=0;
                    $rep->NewLine();
                    $rep->Line($rep->row + 4);
                }

            }//if
            else
                if($sys_type!=10)
                {
                    {
                        $res2 = get_transactions3($myrow['DebtorNo'], $from, $to, $trans['trans_no']);

                        while ($trans2 = db_fetch($res2))
                        {
                            $rep->NewLine();
                            $str =  $trans2['description'];
                            if (strlen($str) > 15)
                                $str = substr($str, 0, 41).'...';
                            $rep->TextCol(0, 4, $str, $dec);



                        }//while
                        $rep->TextCol(2, 4, $systypes_array[$trans['type']]);
                        /*if ($trans['type'] == ST_CUSTPAYMENT)
                            $item[1] = round2(Abs($trans['TotalAmount']) * $rate  -( $trans['service_disc'] + $trans['fbr_disc'] + $trans['srb_disc']+ $trans['supply_disc']), $dec);
                        else*/
                        $item[1] = round2(Abs($trans['TotalAmount']) * $rate, $dec);
//				$rep->Font('bold');
                        $rep->AmountCol(8, 9, $item[1], $dec);
                        $accumulate -= $item[1];
//				$rep->Font();							
                    } //else

                    if ($trans['type'] == ST_CUSTPAYMENT)
                    {
                        if ($trans['supply_disc'] != 0 )
                            $wh1 = 'WHT on Supplies Amount: ' . $trans['supply_disc']." ";
                        if ($trans['service_disc'] != 0)
                            $wh2 = 'WHT on Services Amount: '.$trans['service_disc']." ";
                        if ($trans['fbr_disc'] != 0)
                            $wh3 = 'ST WH FBR Amount: '.$trans['fbr_disc']." ";
                        if ($trans['srb_disc'] != 0)
                            $wh4 = 'ST WH SRB/PRA Amount: '.$trans['srb_disc']." ";
                        $rep->NewLine();
                        $rep->TextCollines(0, 9, $wh1.$wh2.$wh3.$wh4, -2);

                    }
                    $memo = get_comments_string($trans['type'], $trans['trans_no']);
                    if ($memo != "")
                    {
                        $rep->NewLine();
                        $rep->TextColLines(0, 10,"Memo: ".$memo, -2);
                    }
                    if ($trans['type'] == ST_SALESINVOICE || $trans['type'] == ST_BANKPAYMENT || $trans['type'] == ST_CPV)
                        $item[2] = $item[0] - $item[1] ; // previously $item[0] + $item[1] dz
                    else
                        $item[2] = $item[0] - $item[1] ;

                    for ($i = 0; $i < 3; $i++)
                    {
                        $total[$i] += $item[$i];
                        $grandtotal[$i] += $item[$i];//$total[$i];
                    }

                    $total[2] = $total[0] - $total[1];
                    for ($i = 2; $i < 3; $i++)
                        $rep->AmountCol(9 ,10, $total[$i], $dec); //Balance
                    $rep->Font();
                }
        }

        //------------------------
        //	 for ($i = 0; $i < 4; $i++)
        //	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total[$i], $dec);

        //------------------------
        if($sys_type!=10)
        {
            $rep->Line($rep->row - 2);
            $rep->NewLine(2);
            $rep->Font('bold');
            $rep->TextCol(0, 3, _('Total'));
            for ($i = 0; $i < 3; $i++)
            {
                $rep->AmountCol($i + 7, $i + 8, $total[$i], $dec);
                $grandtotal1 +=$total[0];
            }


            $rep->Font();
            $rep->Line($rep->row  - 4);
            $rep->NewLine(2);
        }

    }
    if($sys_type!=10)
    {
        $rep->fontSize += 2;
        $rep->Font('bold');
        $rep->TextCol(0, 3, _('Grand Total'));

        $rep->fontSize -= 2;

        $grandtotal[2] = $grandtotal[0] - $grandtotal[1];



        for ($i = 0; $i < 3; $i++)
            $rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);
        $rep->Font();
        $rep->NewLine();
    }
else
    {$rep->SetTextColor(255, 0, 0);
        $rep->NewLine(2);
        $rep->fontSize += 2;
        $rep->Font('bold');
        $rep->TextCol(0, 3, _('Grand Total'));
        $rep->fontSize -= 2;
        $rep->AmountCol(9, 10, $grand_invoice_total, $dec);
        $rep->AmountCol(6, 7, $grand_cartons_total, $dec);
        $rep->Font();
        $rep->SetTextColor(0, 0, 0);
//        $rep->NewLine();
}

    /*
        for ($i = 0; $i < 3; $i++)
        {

        //	$rep->AmountCol($i + 3, $i + 4, $deb_sum_total_grandtotal[$i], $dec);
            $rep->AmountCol($i + 7, $i + 8, $grandtotal[$i], $dec);
        }
    */
    $rep->Font();
    $rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}

?>