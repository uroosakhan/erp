<?php
$page_security = 'SA_SUPPLIERANALYTIC';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Supplier Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
//include_once($path_to_root . "/inventory/inquiry/stock_movements_detailed.php");

//----------------------------------------------------------------------------------------------------

print_supplier_balances();

function get_po_no_209088($trans_no)
{	$sql = "SELECT purch_order_no
 FROM ".TB_PREF."grn_batch
		 WHERE id=".db_escape($trans_no);

    $result = db_query($sql, 'PO Fetching');
    $row = db_fetch($result);
    return $row[0];
}
function get_open_balance($supplier_id, $to)
{
    $to = date2sql($to);

    $sql = "SELECT SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS charges,
    	SUM(IF(".TB_PREF."supp_trans.type <> ".ST_SUPPINVOICE." AND ".TB_PREF."supp_trans.type <> ".ST_BANKDEPOSIT.", 
    	(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.gst_wh), 0)) AS credits,
		SUM(".TB_PREF."supp_trans.alloc) AS Allocated,
		SUM(IF(".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE." OR ".TB_PREF."supp_trans.type = ".ST_BANKDEPOSIT.",
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount - ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh),
		(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount + ".TB_PREF."supp_trans.alloc + ".TB_PREF."supp_trans.gst_wh))) AS OutStanding
		FROM ".TB_PREF."supp_trans
    	WHERE ".TB_PREF."supp_trans.tran_date < '$to'
		AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id' GROUP BY supplier_id";

    $result = db_query($sql,"No transactions were returned");
    return db_fetch($result);
}

function getTransactions($supplier_id, $from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);

    $sql = "SELECT ".TB_PREF."supp_trans.*,
				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst + ".TB_PREF."supp_trans.ov_discount +
				 ".TB_PREF."supp_trans.gst_wh)
				AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.")
					AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue
    			FROM ".TB_PREF."supp_trans
    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from' AND ".TB_PREF."supp_trans.tran_date <= '$to' 
    			AND ".TB_PREF."supp_trans.supplier_id = '$supplier_id'
    				ORDER BY ".TB_PREF."supp_trans.tran_date";

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}

function getTransactions2($supplier_id, $from,$to)
{
    $from = date2sql($from);
    $to = date2sql($to);


    $sql = "SELECT trans.*, items.*, supp.supp_name
            FROM 0_supp_trans trans
            INNER JOIN 0_supp_invoice_items items ON items.supp_trans_type = trans.type 
            AND items.supp_trans_no = trans.trans_no 
            LEFT JOIN 0_suppliers supp ON trans.supplier_id = supp.supplier_id
            WHERE  trans.type=".ST_SUPPCREDIT_IMPORT."
            AND trans.ov_amount <> 0
            AND  trans.tran_date >= '$from'
            AND  trans.tran_date <= '$to'
            ORDER BY trans.reference";
    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
//function getTransactions2($supplier_id, $from, $to, $transno)
//{
//	$from = date2sql($from);
//	$to = date2sql($to);
//
//    $sql = " ".TB_PREF."supp_trans.*, ".TB_PREF."supp_invoice_items.*,
//				(".TB_PREF."supp_trans.ov_amount + ".TB_PREF."supp_trans.ov_gst +
//				 ".TB_PREF."supp_trans.ov_discount)
//				AS TotalAmount, ".TB_PREF."supp_trans.alloc AS Allocated,
//				((".TB_PREF."supp_trans.type = ".ST_SUPPINVOICE.")
//					AND ".TB_PREF."supp_trans.due_date < '$to') AS OverDue
//
//    			FROM ".TB_PREF."supp_trans, ".TB_PREF."supp_invoice_items
//
//    			WHERE ".TB_PREF."supp_trans.tran_date >= '$from'
//			AND ".TB_PREF."supp_trans.tran_date <= '$to'
//    			AND ".TB_PREF."supp_trans.supplier_id = ".db_escape($supplier_id)."
//
//		AND ".TB_PREF."supp_invoice_items.supp_trans_type  =  ".TB_PREF."supp_trans.type
//		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".TB_PREF."supp_trans.trans_no
//		AND ".TB_PREF."supp_invoice_items.supp_trans_no =  ".db_escape($transno)."
//
//
//    				ORDER BY ".TB_PREF."supp_trans.tran_date";
//
//    $TransResult = db_query($sql,"No transactions were returned");
//
//    return $TransResult;
//}

function get_item_tax_id($stock_id)
{
    $sql = "SELECT * FROM ".TB_PREF."stock_master WHERE stock_id =".db_escape($stock_id);
    return db_query($sql,"items could not be retreived");

}

function get_item_tax()
{
    $sql = "SELECT * FROM ".TB_PREF."tax_types WHERE id = 1";
    return  db_query($sql,"items could not be retreived");

}

function get_pending_transaction_consignment($from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."purch_orders1.* , ".TB_PREF."purch_order_details1.*  , ".TB_PREF."stock_master.units, 
	".TB_PREF."grn_batch.* , ".TB_PREF."stock_master.part_no AS part , 
	".TB_PREF."stock_master.carton , ".TB_PREF."grn_items.stock_status , ".TB_PREF."stock_master.tax_type_id
			FROM ".TB_PREF."purch_orders1 , ".TB_PREF."purch_order_details1 , 
			".TB_PREF."stock_master ,  ".TB_PREF."grn_batch , ".TB_PREF."grn_items
	 WHERE ".TB_PREF."purch_orders1.order_no = ".TB_PREF."purch_order_details1.order_no
	 AND ".TB_PREF."purch_orders1.order_no = ".TB_PREF."grn_batch.purch_order_no
	 AND ".TB_PREF."grn_items.grn_batch_id = ".TB_PREF."grn_batch.id
	 AND ".TB_PREF."purch_order_details1.item_code=".TB_PREF."stock_master.stock_id
	 AND ord_date >= '$from' 
			 AND ord_date <= '$to'";
    return  db_query($sql, "The order cannot be retrieved");
}

function get_import_register($from, $to)
{
    $from = date2sql($from);
    $to = date2sql($to);
    $sql = "SELECT ".TB_PREF."supp_trans_import.* , ".TB_PREF."supp_invoice_items_import.*  


			FROM ".TB_PREF."supp_trans_import , ".TB_PREF."supp_invoice_items_import 
		
	 WHERE ".TB_PREF."supp_trans_import	.trans_no = ".TB_PREF."supp_invoice_items_import.supp_trans_no
	AND ".TB_PREF."supp_trans_import.type = 60
			 AND tran_date >= '$from' 
			 AND tran_date <= '$to'";
    return  db_query($sql, "The order cannot be retrieved");
}
function get_pending_transaction_detail($order_no)
{
    $sql = "SELECT ".TB_PREF."purch_order_details1.*, ".TB_PREF."stock_master.units, ".TB_PREF."stock_master.part_no
		FROM ".TB_PREF."purch_order_details1
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."purch_order_details1.item_code=".TB_PREF."stock_master.stock_id
		WHERE ".TB_PREF."purch_order_details1.order_no =".db_escape($order_no);
    $sql .= " ORDER BY po_detail_item";
    return db_query($sql, "Retreive order Line Items");
}

//----------------------------------------------------------------------------------------------------

function print_supplier_balances()
{
    global $path_to_root, $systypes_array;

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $fromsupp = $_POST['PARAM_2'];
    $currency = $_POST['PARAM_3'];
    $no_zeros = $_POST['PARAM_4'];
    $comments = $_POST['PARAM_5'];
    $orientation = $_POST['PARAM_6'];
    $destination = $_POST['PARAM_7'];


    if ($destination)
        include_once($path_to_root . "/reporting/includes/excel_report.inc");
    else
        include_once($path_to_root . "/reporting/includes/pdf_report.inc");


    $orientation = 'L';
    if ($fromsupp == ALL_TEXT)
        $supp = _('All');
    else
        $supp = get_supplier_name($fromsupp);
    $dec = user_price_dec();

    if ($currency == ALL_TEXT) {
        $convert = true;
        $currency = _('Balances in Home currency');
    } else
        $convert = false;

    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');


    $cols = array(0, 40, 70, 115, 145, 180, 220, 260, 300, 340, 380, 425, 460, 500, 540, 585, 620, 650, 690, 730,
        770, 810, 850, 890, 930, 970, 1010, 1050, 1090);

    $aligns = array('left', 'left', 'left', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right', 'right',
        'right', 'right', 'right');

    $headers2 = array(_("Serial"), _("Delivery"), _("Item"), _("Date"), _("Price)"), _("PO Ex. rate "), _("Custom Val."),
        _("B/E "), _("Landing Amt %"), _(""), _("INS Amt %"), _(""), _("Duty + Add Duty %"), _("Reg. Duty %"),
        _("Val. Incl Reg. Duty + Custom Duty + Additional Custom Duty(PKR)"), _("Sales Tax %"), _(""), _("Income Tax %"),
        _("Add. Tax%"), _("Total Charges)"), ("Net Amt"), ("Import Expenses"), ("PO Price + Import Expenses"),
        ("Con.Fact"));

    $headers = array(_(''), _('P.O.'), _('Desc'), _('Qty'), _('Amt '), _('GD Ex. rate'), _(''), _('Gross Amt'),
        _('Landing Amt'), _('Value Incl Landing Amt(PKR)'), _('INS Amt'), _('Value Incl INS Amt'), _('Duty + Add  Duty'), _('Reg. Duty'),
        _('Value Excl. Sales Tax'), _('Sales Tax'), _('Amount Incl Sales Tax'), _('Income Tax'),
        _('Add.Tax'), _('Other Expenses'), ("Job Name"), (""), ("Unit Cost"));

    $params = array(0 => $comments,
        //	1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
//    			2 => array('text' => _('Supplier'), 'from' => $supp, 'to' => ''),
        //	2 => array(  'text' => _('Currency'),'from' => $currency, 'to' => ''),
        3 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => ''));

    $rep = new FrontReport(_('Import Register Report'), "SupplierBalances", 'A3', 9, $orientation);

    if ($orientation == 'L')
        recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns, $cols, $headers2, $aligns);
    $rep->NewPage();

    $name = get_company_pref('coy_name');
    $address = get_company_pref('postal_address');
    $coy_no = get_company_pref('coy_no');


    $sql = "SELECT *  FROM " . TB_PREF . "suppliers
     ";
    if ($fromsupp != '')
        $sql .= "WHERE supplier_id=" . db_escape($fromsupp) . " ";

    $myrow2 = db_query($sql, "could not get sales person");


    //header data end------------------------------------
    function get_job_name($id)
    {
        $sql = "SELECT name  FROM " . TB_PREF . "dimensions WHERE id=" . db_escape($id);

        $result = db_query($sql, "could not get sales person");
        return db_fetch($result);
    }


    $serial_no = 0;
    $qty_sum = 0;
    $price_sum = 0;
    $amount_sum = 0;
    $custom_value_sum = 0;
    $as_per_be_Sum = 0;
    $gross_amt_sum = 0;
    $landing_amount_sum = 0;
    $value_incl_landing_amount_sum = 0;
    $ins_amount_sum = 0;
    $value_incl_ins_amount_sum = 0;
    $customduty_amount_sum = 0;
    $value_incl_customduty_amount_sum = 0;
    $duty_amount_sum = 0;
    $value_incl_customduty_plus_duty_amount_sum = 0;
    $value_excl_sales_tax_sum = 0;
    $sales_tax_sum = 0;
    $value_incl_sales_tax_sum = 0;
    $income_tax_sum = 0;
    $value_incl_income_tax_sum = 0;
    $additional_tax_sum = 0;
    $value_incl_additonal_tax_sum = 0;
    $total_charges_sum = 0;
    $other_expenses_sum = 0;
    $net_amount_sum = 0;
    $total_import_expenses_sum = 0;
    $total_import_expenses_plus_po_price_Sum = 0;
    $unit_cost_sum = 0;
    $con_factor_sum = 0;

    $ref = '';
    $supp_name = '';

//    while ($supp = db_fetch($myrow2))
    {


//        display_error($supp['supplier_id']);

        $result = getTransactions2(0, $from,$to);
//        $rows=db_num_rows($result);
//        if($rows == 0)
//            continue ;

        $qty_sum_inv = 0;

        $price_sum_inv = 0;
        $amount_sum_inv = 0;
        $custom_value_sum_inv= 0;
        $as_per_be_Sum_inv = 0;
        $gross_amt_sum_inv = 0;
        $landing_amount_sum_inv = 0;
        $value_incl_landing_amount_sum_inv = 0;
        $ins_amount_sum_inv = 0;
        $value_incl_ins_amount_sum_inv = 0;
        $customduty_amount_sum_inv = 0;
        $value_incl_customduty_amount_sum_inv = 0;
        $duty_amount_sum_inv = 0;
        $value_incl_customduty_plus_duty_amount_sum_inv = 0;
        $value_excl_sales_tax_sum_inv = 0;
        $sales_tax_sum_inv = 0;
        $value_incl_sales_tax_sum_inv = 0;
        $income_tax_sum_inv = 0;
        $value_incl_income_tax_sum_inv = 0;
        $additional_tax_sum_inv = 0;
        $value_incl_additonal_tax_sum = 0;
        $total_charges_sum_inv = 0;
        $other_expenses_sum_inv = 0;
        $net_amount_sum_inv = 0;
        $total_import_expenses_sum_inv= 0;
        $total_import_expenses_plus_po_price_Sum_inv = 0;
        $unit_cost_sum_inv = 0;
        $con_factor_sum_inv = 0;

        while ($myrow = db_fetch($result)) {
            $item_tax = get_item_tax_id($myrow['stock_id']);
            $item_tax_row = db_fetch($item_tax);
            $tax = get_item_tax();
            $tax_row = db_fetch($tax);
            $rate_of_sale_tax = $myrow['Value_Excl_S_T'] / $tax_row['rate'];
            $as_per_be = $myrow['Unit_Amt'] * $myrow['quantity'] * $myrow['Gross_Amt_new'];

            $unit_cost = ($myrow['Total_Charges'] + $myrow['Other_Expense']) + $myrow['Gross_Amt'] / $myrow['quantity'];
            $con_factor = $unit_cost / $myrow['unit_price'];
            $add_sale_tax_three_percent = $myrow['Value_Excl_S_T'] / 0.03;
            $net_amount = $myrow['Total_Charges'] + $myrow['Other_Expense'];
            $value_including_sale_tax = $rate_of_sale_tax + $myrow['Value_Excl_S_T'];
            $exice_duty = $myrow['unit_price'] + $myrow['Value_Excl_S_T'];

            //Totals

            $qty_sum += $myrow['quantity'];
            $price_sum += $myrow['unit_price'];
            $amount_sum += $myrow['unit_price'] * $myrow['quantity'];
            $custom_value_sum += $myrow['Gross_Amt_new'];
            $as_per_be_Sum += $as_per_be;
            $gross_amt_sum += $myrow['Gross_Amt'];
            $landing_amount_sum += $myrow['Landing_Amt'];
            $value_incl_landing_amount_sum += $myrow['Value_invl_Landing'];
            $ins_amount_sum += $myrow['INS_Amt'];
            $value_incl_ins_amount_sum += $myrow['Value_Incl_INC'];
            $customduty_amount_sum += $myrow['F_E_D_Amt'];
//        $value_incl_customduty_amount_sum += $myrow['Gross_Amt'];
            $duty_amount_sum += $myrow['Duty_Amt'];
            $value_incl_customduty_plus_duty_amount_sum += $myrow['Gross_Amt'];
            $value_excl_sales_tax_sum += $myrow['Value_Excl_S_T'];
            $sales_tax_sum += $myrow['S_T_Amt'];
            $value_incl_sales_tax_sum += $myrow['Amount_Incl_S_T'];
            $income_tax_sum += $myrow['I_Tax_Amt'];
            $additional_tax_sum += $myrow['Add_S_T_Amt'];

            $total_charges_sum += $myrow['Total_Charges'];
            $other_expenses_sum += $myrow['Other_Expense'];
            $net_amount_sum += $net_amount;
            $total_import_expenses_sum += $myrow['tot_import_expenses'];
            $total_import_expenses_plus_po_price_Sum += $myrow['tot_import_expenses'] + $myrow['Gross_Amt'];
            $unit_cost_sum += $unit_cost;
            $con_factor_sum += $unit_cost / $myrow['unit_price'];





            $qty_sum_inv += $myrow['quantity'];
            $price_sum_inv += $myrow['unit_price'];
            $amount_sum_inv += $myrow['quantity'] * $myrow['unit_price'];
            $custom_value_sum_inv += $myrow['Gross_Amt_new'];
            $as_per_be_Sum_inv += $as_per_be;
            $gross_amt_sum_inv += $myrow['Gross_Amt'];
            $landing_amount_sum_inv += $myrow['Landing_Amt'];
            $value_incl_landing_amount_sum_inv += $myrow['Value_invl_Landing'];
            $ins_amount_sum_inv += $myrow['INS_Amt'];
            $value_incl_ins_amount_sum_inv += $myrow['Value_Incl_INC'];
            $customduty_amount_sum_inv += $myrow['F_E_D_Amt'];
//            $value_incl_customduty_amount_sum_inv += $myrow['quantity'];
            $duty_amount_sum_inv += $myrow['Duty_Amt'];
            $value_incl_customduty_plus_duty_amount_sum_inv += $myrow['Duty_Amt'] + $myrow['F_E_D_Amt'] + $as_per_be_Sum_inv;
            $value_excl_sales_tax_sum_inv += $myrow['Value_Excl_S_T'];
            $sales_tax_sum_inv += $myrow['S_T_Amt'];
            $value_incl_sales_tax_sum_inv += $myrow['Amount_Incl_S_T'];
            $income_tax_sum_inv += $myrow['I_Tax_Amt'];
            $value_incl_income_tax_sum_inv += $myrow['Add_S_T_Amt'];
            $additional_tax_sum_inv += $myrow['Add_S_T_Amt'];
            $value_incl_additonal_tax_sum += $myrow['quantity'];
            $total_charges_sum_inv += $myrow['Total_Charges'];
            $other_expenses_sum_inv += $myrow['Other_Expense'];
            $net_amount_sum_inv += $net_amount;
            $total_import_expenses_sum_inv += $myrow['tot_import_expenses'];
            $total_import_expenses_plus_po_price_Sum_inv += $myrow['tot_import_expenses'] + $myrow['Gross_Amt'];
            $unit_cost_sum_inv += $unit_cost;
            $con_factor_sum_inv +=  $unit_cost / $myrow['unit_price'];

            //--end
            $serial_no++;
            if($ref != $myrow['reference'])
            {
                if($ref != '')
                {
//                    $rep->NewLine(1);
                    $rep->font('bold');
                    $rep->TextCol(0, 2, "Total", -2);
                    $rep->TextCol(3, 4, $qty_sum_inv, -2);
                    $rep->TextCol(4, 5, $price_sum_inv, -2);
                    $rep->TextCol(6, 7, $custom_value_sum_inv, -2);
                    $rep->TextCol(7, 8, $as_per_be_Sum_inv, -2);
                    $rep->TextCol(14, 15, $value_incl_customduty_plus_duty_amount_sum_inv, -2);
                    $rep->TextCol(19, 20, $total_charges_sum_inv, -2);
                    $rep->TextCol(20, 21, $net_amount_sum_inv, -2);
                    $rep->TextCol(21, 22, $total_import_expenses_sum_inv, -2);
                    $rep->TextCol(22, 23, $total_import_expenses_plus_po_price_Sum_inv, -2);
                    $rep->Newline();

                    $rep->TextCol(3, 4, $amount_sum_inv, -2);






                    $rep->TextCol(7, 8, $gross_amt_sum_inv, -2);
                    $rep->TextCol(8, 9, $landing_amount_sum_inv, -2);
                    $rep->TextCol(9, 10, $value_incl_landing_amount_sum_inv, -2);
                    $rep->TextCol(10, 11, $ins_amount_sum_inv, -2);
                    $rep->TextCol(11, 12, $value_incl_ins_amount_sum_inv, -2);
                    $rep->TextCol(12, 13, $customduty_amount_sum_inv, -2);
//                    $rep->TextCol(13, 14, $value_incl_customduty_amount_sum_inv, -2);
                    $rep->TextCol(13, 14, $duty_amount_sum_inv, -2);

                    $rep->TextCol(14, 15, $value_excl_sales_tax_sum_inv, -2);
                    $rep->TextCol(15, 16, $sales_tax_sum_inv, -2);
                    $rep->TextCol(16, 17, $value_incl_sales_tax_sum_inv, -2);
                    $rep->TextCol(17, 18, $income_tax_sum_inv, -2);
//                    $rep->TextCol(18, 19, $value_incl_income_tax_sum_inv, -2);
                    $rep->TextCol(18, 19, $additional_tax_sum_inv, -2);
//                    $rep->TextCol(20, 21, $value_incl_additonal_tax_sum, -2);

                    $rep->TextCol(19, 20, $other_expenses_sum_inv, -2);


                    $rep->TextCol(22, 23, $unit_cost_sum_inv, -2);
                    $rep->TextCol(23, 24, $con_factor_sum_inv, -2);





                    $rep->Line($rep->row  - 4);





                    $rep->font('');
                    $rep->NewLine(1);
                }

//                $ref = $myrow['reference'];
            }
            if($supp_name != $myrow['supp_name'])
            {
//                if($supp_name != '')
                {
//                    $rep->NewLine(1);
                    $rep->font('bold');
                    $rep->Line($rep->row  - 4);
                    $rep->TextCol(0, 7,"Supplier Name". $myrow['supp_name'], -2);
                    $rep->Line($rep->row  + 12);
                    $rep->font('');
                    $rep->NewLine(1.5);
                }
                $supp_name = $myrow['supp_name'];
            }

            if($ref != $myrow['reference'])
            {
//                if($ref != '')
                {
                    $rep->NewLine(1);
                    $rep->font('bold');
                    $rep->TextCol(0, 2, "Inv # :" . $myrow['reference'], -2);
                    $rep->Line($rep->row  - 4);
//                    $qty_sum_inv = 0;
                    $rep->font('');
                    $rep->NewLine(1);
                }
                $ref = $myrow['reference'];
            }
            $rep->NewLine(2, 1);

            $rep->TextCol(0, 1, $serial_no, -2);
            $grn = get_grn_batch_from_item($myrow['grn_item_id']);
            $po = get_po_no_209088($grn);
            $rep->TextCol(1, 2, ($grn), -2);
            $rep->TextCol(2, 3, $myrow['stock_id'], -2);
            $rep->TextCol(3, 4, sql2date($myrow['tran_date']), -2);
            $rep->TextCol(4, 5, number_format( $myrow['unit_price'],2), -2);
            $rep->TextCol(5, 6, number_format(  $myrow['po_exchange_rate'],2), -2);
            $rep->TextCol(6, 7, number_format($myrow['Gross_Amt_new'],2), -2);
            $rep->TextCol(7, 8, number_format( $as_per_be,2), -2);
            $rep->TextCol(8, 9, $myrow['Landing'], -2);
            $rep->TextCol(9, 10, "", -2);
            $rep->TextCol(10, 11, $myrow['INS'], -2);
            $rep->TextCol(11, 12, "", -2);
            $rep->TextCol(12, 13,  $myrow['F_E_D'], -2);
            $rep->TextCol(13, 14, $myrow['Duty'], -2);
            $rep->TextCol(14, 15, number_format( $myrow['Value_And_Duty'],2), -2);
            $rep->TextCol(15, 16,  $myrow['S_T'], -2);
            $rep->TextCol(16, 17, "", -2);
            $rep->TextCol(17, 18,  $myrow['I_Tax'], -2);
            $rep->TextCol(18, 19,  $myrow['Add_S_T'], -2);
            $rep->TextCol(19, 20, number_format( $myrow['Total_Charges'],2), -2);
            $rep->TextCol(20, 21, number_format( $net_amount,2), -2);
            $rep->TextCol(21, 22, number_format(  $myrow['tot_import_expenses'],2), -2);
            $rep->TextCol(22, 23, number_format(  $myrow['tot_import_expenses'] + $myrow['Gross_Amt'],2), -2);
            $rep->TextCol(23, 24, number_format( $con_factor,2), -2);
            $rep->NewLine();
            $rep->TextCol(1, 2, $po['purch_order_no'], -2);
            $rep->TextCol(2, 3, $myrow['description'], -2);
            $rep->TextCol(3, 4, number_format( $myrow['quantity'],2), -2);
            $rep->TextCol(4, 5, number_format( $myrow['unit_price'] * $myrow['quantity'],2), -2);
            $rep->TextCol(5, 6, number_format( $myrow['Unit_Amt'],2), -2);
            $rep->TextCol(7, 8, number_format( $myrow['Gross_Amt'],2), -2);
            $rep->TextCol(8, 9, number_format( $myrow['Landing_Amt'],2), -2);
            $rep->TextCol(9, 10,number_format(  $myrow['Value_invl_Landing'],2), -2);
            $rep->TextCol(10, 11,number_format(  $myrow['INS_Amt'],2), -2);
            $rep->TextCol(11, 12, number_format( $myrow['Value_Incl_INC'],2), -2);
            $rep->TextCol(12, 13, number_format( $myrow['F_E_D_Amt'],2), -2);
            $rep->TextCol(13, 14, number_format( $myrow['Duty_Amt'],2), -2);
            $rep->TextCol(14, 15, number_format( $myrow['Value_Excl_S_T'],2), -2);
            $rep->TextCol(15, 16,number_format(  $myrow['S_T_Amt'],2), -2);
            $rep->TextCol(16, 17,number_format(  $myrow['Amount_Incl_S_T'],2), -2);
            $rep->TextCol(17, 18,number_format(  $myrow['I_Tax_Amt'],2), -2);
            $rep->TextCol(18, 19,number_format(  $myrow['Add_S_T_Amt'],2), -2);
            $rep->TextCol(19, 20,number_format(  $myrow['Other_Expense'],2), -2);
            $rep->TextCol(19, 20,number_format(  $myrow['Other_Expense'],2), -2);
            $rep->TextCol(22, 23,number_format(  $unit_cost,2), -2);

//            $rep->TextCol(0, 3, _('S Total'));

          //------------------------------------------------------------------------------------------------

            $qty_sum_inv = 0;
            $price_sum_inv = 0;
            $amount_sum_inv = 0;
            $custom_value_sum_inv= 0;
            $as_per_be_Sum_inv = 0;
            $gross_amt_sum_inv = 0;
            $landing_amount_sum_inv = 0;
            $value_incl_landing_amount_sum_inv = 0;
            $ins_amount_sum_inv = 0;
            $value_incl_ins_amount_sum_inv = 0;
            $customduty_amount_sum_inv = 0;
            $value_incl_customduty_amount_sum_inv = 0;
            $duty_amount_sum_inv = 0;
            $value_incl_customduty_plus_duty_amount_sum_inv = 0;
            $value_excl_sales_tax_sum_inv = 0;
            $sales_tax_sum_inv = 0;
            $value_incl_sales_tax_sum_inv = 0;
            $income_tax_sum_inv = 0;
            $value_incl_income_tax_sum_inv = 0;
            $additional_tax_sum_inv = 0;
            $value_incl_additonal_tax_sum = 0;
            $total_charges_sum_inv = 0;
            $other_expenses_sum_inv = 0;
            $net_amount_sum_inv = 0;
            $total_import_expenses_sum_inv= 0;
            $total_import_expenses_plus_po_price_Sum_inv = 0;
            $unit_cost_sum_inv = 0;
            $con_factor_sum_inv = 0;





            // $rep->Line($rep->row  - 4);
            $rep->NewLine(2);
//        $ref = $myrow['reference'];
        }
        $rep->font('bold');
        $rep->TextCol(0, 3, _("Total"));
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);
        $rep->TextCol(3, 4, number_format($qty_sum_inv,2), -2);








        $rep->font('');
        // $rep->Line($rep->row  + 8);

    }
    $rep->NewLine(2);
    $rep->font('bold');
    $rep->TextCol(0, 2, "Grand Total", -2);

    $rep->TextCol(4, 5, number_format($price_sum,2), -2);

    $rep->TextCol(6, 7, number_format($custom_value_sum,2), -2);
    $rep->TextCol(7, 8, number_format($as_per_be_Sum,2), -2);
    $rep->TextCol(19, 20,number_format( $total_charges_sum,2), -2);
    $rep->TextCol(20, 21,number_format( $net_amount_sum,2), -2);
    $rep->TextCol(21, 22,number_format( $total_import_expenses_sum,2), -2);


    $rep->NewLine();
    $rep->TextCol(3, 4, number_format($qty_sum,2), -2);
    $rep->TextCol(4, 5,number_format( $amount_sum,2), -2);
    $rep->TextCol(7, 8,number_format( $gross_amt_sum,2), -2);
    $rep->TextCol(8, 9,number_format( $landing_amount_sum,2), -2);
    $rep->TextCol(9, 10, number_format($value_incl_landing_amount_sum,2), -2);
    $rep->TextCol(10, 11,number_format( $ins_amount_sum,2), -2);
    $rep->TextCol(11, 12,number_format( $value_incl_ins_amount_sum,2), -2);
    $rep->TextCol(12, 13,number_format( $customduty_amount_sum,2), -2);
    $rep->TextCol(13, 14,number_format( $customduty_amount_sum,2), -2);
    $rep->TextCol(14, 15,number_format( $duty_amount_sum,2), -2);
    $rep->TextCol(14, 15,number_format( $duty_amount_sum,2), -2);
    $rep->TextCol(15, 16, number_format($sales_tax_sum,2), -2);
    $rep->TextCol(16, 17,number_format( $value_incl_sales_tax_sum,2), -2);
    $rep->TextCol(17, 18,number_format( $income_tax_sum,2), -2);
    $rep->TextCol(18, 19,number_format( $additional_tax_sum,2), -2);

    $rep->TextCol(19, 20,number_format( $other_expenses_sum,2), -2);


    $rep->TextCol(22, 23,number_format( $unit_cost_sum,2), -2);
    $rep->TextCol(23, 24,number_format( $con_factor_sum,2), -2);

    $rep->Font();
//	$rep->Line($rep->row  - 4);
    $rep->NewLine();
    $rep->End();
}

?>