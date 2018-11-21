<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 7/30/2018
 * Time: 4:42 PM
 */


function get_todays_sales($dimension=0,$start_date,$end_date)
{
    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
            + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS sales,d.debtor_no, d.name
            FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d
            WHERE trans.debtor_no=d.debtor_no
            AND (trans.type = ".ST_SALESINVOICE." )
            AND tran_date >= '$start_date' AND tran_date <= '$end_date' ";

    if($dimension !=0)
        $sql.=" AND trans.dimension_id=".$dimension;

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
function get_todays_recovery($start_date,$end_date)
{
    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);
  ;

    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
            + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate) AS recovery
            FROM " . TB_PREF . "debtor_trans AS trans, " . TB_PREF . "debtors_master AS d
            WHERE trans.debtor_no=d.debtor_no
            AND (trans.type = " . ST_BANKDEPOSIT . " OR trans.type = " . ST_CUSTPAYMENT . " OR trans.typ = " . ST_CRV . ")
            AND tran_date >= '$start_date' AND tran_date <= '$end_date' ";


    $recoveryresult = db_query($sql);
    $recoverymyrow = db_fetch($recoveryresult);
    $recovery = $recoverymyrow['recovery'];
    if ($recovery > 0) {
        return $recovery;
    } else {
        return $recovery = 0;
    }



}
function get_todays_sales_order($dimension=0,$start_date,$end_date)
{

    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM(total-(discount1+discount2)) AS sorders
            FROM ".TB_PREF."sales_orders AS so, ".TB_PREF."debtors_master AS d
            WHERE so.debtor_no=d.debtor_no
            AND so.reference != 'auto'
            AND so.trans_type = '30'
            AND so.ord_date >= '$start_date' AND so.ord_date <= '$end_date'";

    if($dimension!=0)
        $sql .=" AND so.dimension_id=".$dimension;

    $sresult = db_query($sql);
    $smyrow = db_fetch($sresult);

    $sorders = $smyrow['sorders'];
    if($sorders > 0)
    {
        return $sorders;
    }
    else
    {  return $sorders1 = 0;
    }
}
function get_todays_purchase_orders($dimension=0,$start_date,$end_date)
{

    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM(total) AS porders
        FROM ".TB_PREF."purch_orders AS po, ".TB_PREF."suppliers AS s
        WHERE po.supplier_id=s.supplier_id
        AND po.reference != 'auto'
        AND po.ord_date >= '$start_date' AND po.ord_date <= '$end_date'";

    if($dimension!=0)
    $sql.=" AND po.dimension=".$dimension;

    $poresult = db_query($sql);
    $pomyrow = db_fetch($poresult);
    $porders = $pomyrow['porders'];
    if($porders > 0)
    {
        return $porders;
    }
    else
    {  return $porders1 = 0;
    }
}
function get_vendor_payments($dimension=0,$start_date,$end_date)
{

    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM((ov_amount) * rate) AS payments
            FROM ".TB_PREF."supp_trans AS strans, ".TB_PREF."suppliers AS s
            WHERE strans.supplier_id=s.supplier_id
            AND (strans.type = ".ST_BANKPAYMENT." OR strans.type = ".ST_SUPPAYMENT." OR strans.type = ".ST_CPV.")
            AND strans.tran_date >= '$start_date' AND strans.tran_date <= '$end_date'";

    if($dimension!=0)
        $sql.=" AND strans.dimension_id=".$dimension;

    $paymentsresult = db_query($sql);
    $paymentsmyrow = db_fetch($paymentsresult);
    $payments = abs($paymentsmyrow['payments']);
    if($payments > 0)
    {
        return $payments;
    }
    else
    {  return $payments1 = 0;
    }
}
function get_todays_sales_return($dimension=0,$start_date,$end_date)
{

    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM((trans.ov_amount + trans.ov_gst + trans.ov_freight + trans.ov_freight_tax + trans.ov_discount + trans.gst_wh
+ trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.discount1 - trans.discount2) * rate*IF(trans.type = ".ST_CUSTCREDIT.", -1, 1)) AS salesreturn,d.debtor_no, d.name
FROM ".TB_PREF."debtor_trans AS trans, ".TB_PREF."debtors_master AS d
WHERE trans.debtor_no=d.debtor_no
AND trans.type = ".ST_CUSTCREDIT."
AND tran_date >= '$start_date' AND tran_date <= '$end_date'";

    if($dimension!=0)
        $sql.=" AND trans.dimension_id=".$dimension;

    $salesreturnresult = db_query($sql);
    $salesreturnmyrow = db_fetch($salesreturnresult);
    $salesreturn = abs($salesreturnmyrow['salesreturn']);
    if($salesreturn > 0)
    {
        return $salesreturn;
    }
    else
    {
        return 0;
    }
}

function get_data_from_systypes($dimension=0,$start_date,$end_date,$sys_type)
{
    $start_date = date2sql($start_date);
    $end_date = date2sql($end_date);

    $sql = "SELECT SUM(gl.amount) AS amnt

FROM 0_gl_trans gl LEFT JOIN 0_voided v ON gl.type_no=v.id 
AND v.type=gl.type LEFT JOIN 0_supp_trans st ON gl.type_no=st.trans_no 
AND st.type=gl.type AND (gl.type!=0 OR gl.person_id=st.supplier_id) 

LEFT JOIN 0_grn_batch grn ON grn.id=gl.type_no AND gl.type=25 
AND gl.person_id=grn.supplier_id LEFT JOIN 0_debtor_trans dt ON gl.type_no=dt.trans_no 
AND dt.type=gl.type AND (gl.type!=0 OR gl.person_id=dt.debtor_no) 
LEFT JOIN 0_suppliers sup ON st.supplier_id=sup.supplier_id OR grn.supplier_id=sup.supplier_id
 LEFT JOIN 0_cust_branch branch ON dt.branch_code=branch.branch_code 
 LEFT JOIN 0_debtors_master debt ON dt.debtor_no=debt.debtor_no 
 LEFT JOIN 0_bank_trans bt ON bt.type=gl.type AND bt.trans_no=gl.type_no 
 AND bt.amount!=0 AND bt.person_type_id=gl.person_type_id AND bt.person_id=gl.person_id 
 LEFT JOIN 0_journal j ON j.type=gl.type AND j.trans_no=gl.type_no 
 LEFT JOIN 0_audit_trail a ON a.type=gl.type AND a.trans_no=gl.type_no 
 
 AND NOT ISNULL(gl_seq) LEFT JOIN 0_users u ON a.user=u.id 
 LEFT JOIN 0_comments AS com ON (gl.type=com.type AND gl.type_no=com.id) 
 LEFT JOIN 0_refs ref ON ref.type=gl.type AND ref.id=gl.type_no,0_chart_master coa,0_chart_types coa_types 
 
 WHERE coa.account_code=gl.account AND coa.account_type=coa_types.id 
 AND ISNULL(v.date_) AND gl.tran_date >= '$start_date' AND gl.tran_date <= '$end_date'
  AND gl.approval != 1 AND gl.type= '$sys_type' AND gl.amount >0 ORDER BY gl.tran_date, gl.counter";

    if($dimension!=0)
        $sql.=" AND gl.dimension_id=".$dimension;

    $sys_types_dt = db_query($sql);
    $sys_types_dt = db_fetch($sys_types_dt);
    $sys_types_dt = abs($sys_types_dt['amnt']);
    if($sys_types_dt > 0)
    {
        return $sys_types_dt;
    }
    else
    {
        return 0;
    }

}


function get_total_voucher_count()
{
    $sql = "SELECT COUNT(DISTINCT type_no) FROM " . TB_PREF . "gl_trans
	WHERE approval = 1
	AND amount >0";
    $result = db_query($sql, "could not get vouchers");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_total_pending_sales_deliveries()
{
    $sql = "SELECT COUNT(DISTINCT sorder.order_no)
	FROM " . TB_PREF . "sales_orders as sorder,
	" . TB_PREF . "sales_order_details as line
	WHERE sorder.order_no = line.order_no
	AND sorder.trans_type = line.trans_type
	AND line.trans_type IN (30, 31)
	AND line.quantity != 0
	AND line.qty_sent=0
	";
    $result = db_query($sql, "could not get debtors");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_total_pending_invoice()
{
    $sql = "SELECT COUNT(DISTINCT d.trans_no)
	FROM " . TB_PREF . "debtor_trans as d,
	" . TB_PREF . "debtor_trans_details as line
	WHERE d.trans_no = line.debtor_trans_no
	AND d.type = line.debtor_trans_type
	AND d.type = 13
	AND line.quantity != 0
	AND line.qty_done=0";
    $result = db_query($sql, "could not get deliveries");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_pending_inv_alloc()
{
    $sql = "SELECT COUNT(DISTINCT d.trans_no)
	FROM " . TB_PREF . "debtor_trans as d,
	" . TB_PREF . "debtors_master as debtor
	WHERE debtor.debtor_no = d.debtor_no
	AND (d.ov_amount + d.ov_gst + d.ov_freight
	+ d.ov_freight_tax + d.ov_discount - d.discount1 - d.discount2 != 0)
	AND (round(IF(d.prep_amount,d.prep_amount, abs(d.ov_amount + d.ov_gst + "
        . "d.ov_freight + d.ov_freight_tax + "
        . "d.ov_discount - d.discount1 - d.discount2)) - d.alloc,6) != 0)";
    $result = db_query($sql, "could not get allocations");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_outstanding_work_order()
{
    $sql = "SELECT COUNT(DISTINCT id) FROM " . TB_PREF . "workorders
	WHERE closed != 1";
    $result = db_query($sql, "could not get work oders");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_unreconsiled_bank_trans()
{
    $sql = "SELECT COUNT(DISTINCT id)
	FROM " . TB_PREF . "bank_trans
	WHERE reconciled IS NULL";
    $result = db_query($sql, "could not get bank reconciliations");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_pending_qoutes()
{
    $today = date2sql(Today());
    $sql = "SELECT COUNT(DISTINCT sorder.order_no)
	FROM " . TB_PREF . "sales_orders as sorder,
	" . TB_PREF . "sales_order_details as line
	WHERE sorder.order_no = line.order_no
	AND sorder.trans_type = line.trans_type
	AND sorder.delivery_date>='$today'
	AND line.trans_type = 32
	AND line.qty_sent=0";
    $result = db_query($sql, "could not get quotations");
    $row = db_fetch_row($result);
    return $row[0];
}
function trans_count($today)
{
    $today = date2sql(Today());
    $sql = "SELECT COUNT(r.id) AS count
            		FROM ".TB_PREF."refs AS r, ".TB_PREF."audit_trail AS a
            	    WHERE r.id=a.trans_no        			
                    AND r.type=a.type
                    AND a.stamp >= '$today 00:00:00'

    		    	";
    $result = db_query($sql);
    $trans_count = db_fetch($result);
    return $trans_count[0];
}
function get_sales_amount($trans_no,$trans_type)
{
    $sql = "SELECT ov_amount FROM ".TB_PREF."debtor_trans WHERE trans_no=".db_escape($trans_no) ." AND type =".db_escape($trans_type) ;

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

function get_gl_sum_account_type($account_type,$from,$to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT SUM(`amount`) as total FROM `0_gl_trans`
            INNER JOIN 0_chart_master ON 0_chart_master.account_code=0_gl_trans.`account`
            WHERE 0_chart_master.account_type=$account_type
            AND `tran_date` >='$from' AND `tran_date` <='$to'";

    $result = db_query($sql, "could not get sums");

    $row = db_fetch($result);
    return $row['total'];
}
function get_item_data($from,$to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql ="SELECT move.qty , item.material_cost , SUM(ROUND(move.qty,2) * item.material_cost) AS total
            FROM `0_stock_moves`  move ,  0_stock_master item
            WHERE 
            move.stock_id = item.stock_id 
            AND item.material_cost > 0
            AND move.tran_date >= '$from'
            AND move.tran_date <= '$to'
            AND item.mb_flag<>'D' AND mb_flag <> 'F' 
            GROUP BY move.stock_id ";
    $result = db_query($sql, "could not get sums");

    while($dtt = db_fetch($result))
    {
          $tot = $dtt['total'];
          $total +=$tot;
    }

    $total = $total;

    return $total ;

}

function get_total_so_approval_waiting()
{
    $sql = "SELECT COUNT(*) FROM (SELECT sorder.order_no, sorder.reference, debtor.name, branch.br_name,
sorder.customer_ref, sorder.ord_date, sorder.delivery_date, sorder.deliver_to, 
sorder.total-sorder.discount1-sorder.discount2 AS total , sorder.type, debtor.curr_code, 
SUM(line.qty_sent) AS TotDelivered, SUM(line.quantity) AS TotQuantity, SUM(line.invoiced) AS TotInvoiced,
 alloc, prep_amount, allocs.ord_payments, inv.inv_payments, sorder.trans_type, 
 
 sorder.approval, sorder.dimension_id 
 
 
 FROM 0_sales_orders AS sorder LEFT JOIN (SELECT trans_no_to, SUM(amt) ord_payments 
 
 FROM 0_cust_allocations WHERE trans_type_to=30 GROUP BY trans_no_to) allocs ON sorder.trans_type=30 
 
 AND allocs.trans_no_to=sorder.order_no LEFT JOIN (SELECT order_, SUM(prep_amount) inv_payments    
 
 FROM 0_debtor_trans WHERE TYPE=10 GROUP BY order_) inv ON sorder.trans_type=30 
 AND inv.order_=sorder.order_no,0_sales_order_details AS line, 0_debtors_master AS debtor, 0_cust_branch AS branch
  WHERE sorder.order_no = line.order_no AND sorder.trans_type = line.trans_type 
  AND sorder.trans_type = '30' AND sorder.debtor_no = debtor.debtor_no 
  AND sorder.branch_code = branch.branch_code AND debtor.debtor_no = branch.debtor_no 
  
  AND line.qty_sent < line.quantity GROUP BY sorder.order_no, sorder.debtor_no, sorder.branch_code,
   sorder.customer_ref, sorder.ord_date, sorder.deliver_to) tmp_count
";

    $result = db_query($sql, "could not get PO Count");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_total_po_approval_waiting()
{
    $sql = "SELECT COUNT(*) FROM (SELECT porder.order_no, porder.reference, supplier.supp_name, 
            location.location_name, porder.requisition_no, porder.ord_date, supplier.curr_code, porder.total AS OrderValue, 
            porder.approval FROM 0_purch_orders AS porder,0_purch_order_details AS line, 
            0_suppliers AS supplier, 0_locations AS location WHERE porder.order_no = line.order_no 
            AND porder.supplier_id = supplier.supplier_id AND location.loc_code = porder.into_stock_location 
            AND (line.quantity_ordered > line.quantity_received) AND porder.transaction_type = 0
            GROUP BY porder.order_no) tmp_count";

    $result = db_query($sql, "could not get PO Count");
    $row = db_fetch_row($result);
    return $row[0];
}