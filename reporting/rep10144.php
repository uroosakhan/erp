<?php
$page_security = 'SA_CUSTPAYMREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances Summary
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

//----------------------------------------------------------------------------------------------------

print_aged_customer_analysis();
//
//function get_customer_details_new111($customer_id, $to=null, $all=true,$branch_id)
//{
//    if ($to == null)
//        $todate = date("Y-m-d");
//    else
//        $todate = date2sql($to);
//    $past1 = get_company_pref('past_due_days');
//    $past2 = 2 * $past1;
//    // removed - debtor_trans.alloc from all summations
//
////	$sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT,ST_JOURNAL))."), -1, 1)";
////dz 16.6.17
//    $sign = "IF(`type` IN(".implode(',',  array(ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_BANKDEPOSIT, ST_CRV))."), -1, 1)";
//    if ($all)
//        $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2),0)";
//    /*  else
//          $value = "IFNULL($sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//  + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
//              trans.alloc),0)"; */
//
//    else		//dz 24.7.18
//        $value = "IFNULL(
//    	IF (type=".ST_JOURNAL." && ov_amount < 0,
//    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 +
//    		trans.alloc),
//    	$sign*(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
//+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2 -
//    		trans.alloc)
//    		)
//    		,0)";
//
//    $due = "IF (trans.type=".ST_SALESINVOICE.", trans.due_date, trans.tran_date)";
//    $sql = "SELECT debtor.name, debtor.curr_code, terms.terms, debtor.credit_limit,debtor.credit_allowed,
//    			credit_status.dissallow_invoices, credit_status.reason_description,
//				Sum(IFNULL($value,0)) AS Balance,
//				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > 0,$value,0)) AS Due,
//				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past1,$value,0)) AS Overdue1,
//				Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) > $past2,$value,0)) AS Overdue2
//			FROM ".TB_PREF."debtors_master debtor
//				 LEFT JOIN ".TB_PREF."debtor_trans trans ON trans.tran_date <= '$todate' AND debtor.debtor_no = trans.debtor_no AND trans.type <> ".ST_CUSTDELIVERY.","
//        .TB_PREF."payment_terms terms,"
//        .TB_PREF."credit_status credit_status
//			WHERE
//					debtor.payment_terms = terms.terms_indicator
//	 			AND debtor.credit_status = credit_status.id";
//    if ($customer_id)
//        $sql .= " AND debtor.debtor_no = ".db_escape($customer_id);
//
//    if (!$all)
//        $sql .= " AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount - trans.discount1 - trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA;
//
//    if($dim != 0)
//        $sql .= " AND trans.dimension_id = ".db_escape($dim);
//    $sql .= " GROUP BY
//		  	debtor.name,
//		  	terms.terms,
//		  	terms.days_before_due,
//		  	terms.day_in_following_month,
//		  	debtor.credit_limit,
//		  	credit_status.dissallow_invoices,
//		  	credit_status.reason_description";
//    $result = db_query($sql,"The customer details could not be retrieved");
//
//    $customer_record = db_fetch($result);
//
//    return $customer_record;
//
//}
//function get_cust_detail($cust_id)
//{
//    $sql = "SELECT * FROM `0_crm_persons`
//INNER JOIN 0_crm_contacts ON 0_crm_contacts.person_id=0_crm_persons.id
//INNER JOIN 0_cust_branch ON 0_cust_branch.branch_code=0_crm_contacts.entity_id
//WHERE 0_cust_branch.branch_code=$cust_id
//	";
////    $sql .= " ORDER BY pod.item_code ";
//    $result = db_query($sql, "The customers could not be retrieved");
//    return db_fetch($result);
//}
//----------------------------------------------------------------------------------------------------
function get_sales($type,$start_date,$end_date)
{
    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
            + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS sales,d.debtor_no, d.name
            FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d
            WHERE trans.debtor_no=d.debtor_no
            AND (trans.type = $type )
            AND tran_date >= '$start_date' AND tran_date <= '$end_date' ";


    $salesresult = db_query($sql);
    $salesmyrow = db_fetch($salesresult);
    $salesmyrow = $salesmyrow['sales'];;

    if($salesmyrow > 0)
    {
        return $salesmyrow;
    }
    else
    {
        return $salesmyrow = 0;
    }

}
function get_cheque_recieve($type,$from,$to)
{
    $start_date = date2sql($from);
    $end_date = date2sql($to);

    $sql = "SELECT SUM(amount) as amnt FROM ".TB_PREF."gl_trans WHERE type=$type AND tran_date >='$start_date' 
                AND tran_date <='$end_date' AND amount >0";


    $result = db_query($sql);
    $dt = db_fetch($result);
    $amnt = $dt['amnt'];;

    if($amnt > 0)
    {
        return $amnt;
    }
    else
    {
        return $amnt = 0;
    }

}
function print_aged_customer_analysis()
{
    global $path_to_root, $systypes_array;

    $to = $_POST['PARAM_0'];
    $group_by = $_POST['PARAM_1'];
    $fromcust = $_POST['PARAM_2'];
    $dimension = $_POST['PARAM_3'];
    $area = $_POST['PARAM_4'];
    $folk = $_POST['PARAM_5'];
    $currency = $_POST['PARAM_6'];
    //$show_all = $_POST['PARAM_5'];
    $no_zeros = $_POST['PARAM_7'];
    $graphics = $_POST['PARAM_8'];
    $comments = $_POST['PARAM_9'];
    $orientation = $_POST['PARAM_10'];
    $destination = $_POST['PARAM_11'];



    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $orientation = ($orientation ? 'L' : 'P');

    $rep = new FrontReport(_('Daily Sales Account Summary'), "CustomerBalancesSummary", user_pagesize(), 9, $orientation,null,null,1);

    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->Font();
    $rep->Info($params="", $cols="", $headers="", $aligns="");
    $rep->NewPage();

    $rep->SetFontSize(13);
    $rep->Font('b');
    $rep->MultiCell(250,30,"Total Sales Summary",0,'L', 0, 2,230,137,true);
    $rep->Font();

    $rep->SetFontSize(9);

    $from = Today();
    $to = Today();

    $sales  = get_sales(10,$from,$to);
    $recieved_amount = get_sales(12,$from,$to);
    $total_return_amount = get_sales(11,$from,$to);


    $cash_recieved_amount = get_cheque_recieve(42,$from,$to);
    $cheque_recieved_amount = get_cheque_recieve(2,$from,$to);

    $balance_amount = $sales - $recieved_amount;

//    $rep->Line($rep->row - 22);

    $rep->MultiCell(350,30,"",1,'L', 0, 2,120,130,true); // 1st Box
    $rep->NewLine();
    $rep->MultiCell(350,52,"",1,'L', 0, 2,120,160,true); // 2nd Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,212,true); // 3rd Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,234,true); // 4th Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,256,true); // 5th Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,277,true); // 6th Box
    $rep->MultiCell(350,22,"",1,'L', 0, 2,120,300,true); // 7th Box

    $rep->MultiCell(250,30,"Total Sales Rs :",0,'L', 0, 2,130,167,true);
    $rep->MultiCell(250,30,number_format2($sales,2),0,'R', 0, 2,200,167,true);

    $rep->MultiCell(250,30,"Received Amount :",0,'L', 0, 2,130,181,true);
    $rep->MultiCell(250,30,number_format2($recieved_amount,2),0,'R', 0, 2,200,181,true);

    $rep->MultiCell(250,30,"Balance Amount :",0,'L', 0, 2,130,195,true);
    $rep->MultiCell(250,30,number_format2($balance_amount,2),0,'R', 0, 2,200,195,true);

    $rep->MultiCell(250,30,"Total Cheque Amount :",0,'L', 0, 2,130,217,true);
    $rep->MultiCell(250,30,"Cash Recieved Amount :",0,'L', 0, 2,130,239,true);
    $rep->MultiCell(250,30, number_format2($cash_recieved_amount,2),0,'R', 0, 2,200,240,true);

    $rep->MultiCell(250,30,"Cheque Recieved Amount :",0,'L', 0, 2,130,260,true);
    $rep->MultiCell(250,30, number_format2($cheque_recieved_amount,2),0,'R', 0, 2,200,260,true);



    $rep->MultiCell(250,30,"Online Recieved Amount :",0,'L', 0, 2,130,283,true);

    $rep->MultiCell(250,30,"Total Return Amount :",0,'L', 0, 2,130,305,true);
    $rep->MultiCell(250,30, number_format2($total_return_amount,2),0,'R', 0, 2,200,305,true);


    $rep->NewLine();
    $rep->End();
}

?>