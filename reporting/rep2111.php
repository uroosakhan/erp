<?php

$page_security = $_POST['PARAM_0'] == $_POST['PARAM_1'] ?
    'SA_SUPPTRANSVIEW' : 'SA_SUPPBULKREP';
// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Purchase Orders
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/db/crm_contacts_db.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");

//----------------------------------------------------------------------------------------------------

print_po();

//----------------------------------------------------------------------------------------------------
function get_po2($trans_no)
{
    $sql = "SELECT ".TB_PREF."supp_trans.*, ".TB_PREF."suppliers.supp_name,".TB_PREF."supp_trans.supp_reference,  "
        .TB_PREF."suppliers.supp_account_no,".TB_PREF."suppliers.tax_included,".TB_PREF."suppliers.gst_no AS tax_id,
   		".TB_PREF."suppliers.curr_code, ".TB_PREF."suppliers.payment_terms,".TB_PREF."suppliers.ntn_no,
   		".TB_PREF."suppliers.address, ".TB_PREF."suppliers.contact, ".TB_PREF."suppliers.tax_group_id
		FROM ".TB_PREF."supp_trans, ".TB_PREF."suppliers
		WHERE ".TB_PREF."supp_trans.supplier_id = ".TB_PREF."suppliers.supplier_id
		AND ".TB_PREF."supp_trans.type=43
		AND ".TB_PREF."supp_trans.trans_no = ".db_escape($trans_no)."
		
		";
    $result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}

function get_po_details2($supp_trans_no)
{
    $sql = "SELECT  0_supp_invoice_items.*,0_supp_invoice_items.quantity,
  0_supp_invoice_items.unit_price,0_supp_invoice_items.Gross_Amt
  ,0_supp_invoice_items.Landing_Amt, 
  0_supp_invoice_items.Value_invl_Landing,0_supp_invoice_items.INS_Amt,
  0_supp_invoice_items.Value_Incl_INC, 0_supp_invoice_items.F_E_D_Amt,
  0_supp_invoice_items.Duty_Amt,0_supp_invoice_items.Value_Excl_S_T, 
  0_supp_invoice_items.S_T_Amt,0_supp_invoice_items.Amount_Incl_S_T,
  0_supp_invoice_items.I_Tax_Amt, 0_supp_invoice_items.Add_S_T_Amt,
  0_supp_invoice_items.Total_Charges,0_supp_invoice_items.Net_Amount,
   0_supp_invoice_items.Other_Expense, 0_supp_invoice_items.Net_Amount, 0_supp_invoice_items.As_Per_B_E
    FROM 0_supp_trans, 0_supp_invoice_items WHERE 
    0_supp_invoice_items.supp_trans_no=0_supp_trans.trans_no AND 
    0_supp_invoice_items.supp_trans_type=0_supp_trans.type AND 
    0_supp_trans.trans_no=".db_escape($supp_trans_no)." AND 0_supp_invoice_items.supp_trans_type=43 
		       
		    ";
    $sql .= " ORDER BY supp_trans_no";
    return db_query($sql, "Retreive order Line Items");
}

function get_ref_for_order_no($trans_no)
{
    $sql = "SELECT reference 
    FROM ".TB_PREF."purch_orders, ".TB_PREF."purch_order_details, ".TB_PREF."grn_items,
    ".TB_PREF."supp_invoice_items
	WHERE ".TB_PREF."purch_orders.order_no = ".TB_PREF."purch_order_details.order_no
	AND ".TB_PREF."purch_order_details.po_detail_item = ".TB_PREF."grn_items.po_detail_item
	AND ".TB_PREF."grn_items.id = ".TB_PREF."supp_invoice_items.grn_item_id
	AND ".TB_PREF."supp_invoice_items.supp_trans_no = ".db_escape($trans_no)."
	";
    return db_query($sql, "could not retreive default customer currency code");

}

function print_po()
{
    global $path_to_root, $show_po_item_codes;

    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $from = $_POST['PARAM_0'];
    $to = $_POST['PARAM_1'];
    $from_date = $_POST['PARAM_2'];
    $to_date = $_POST['PARAM_3'];
    $currency = $_POST['PARAM_4'];
    $email = $_POST['PARAM_5'];
    $comments = $_POST['PARAM_6'];
    $orientation = $_POST['PARAM_7'];

    if (!$from || !$to) return;
    $fno = explode("-", $from);
    $tno = explode("-", $to);
    $from = min($fno[0], $tno[0]);
    $to = max($fno[0], $tno[0]);
    $orientation = ('L');
    $dec = user_price_dec();

    $cols = array(0,16, 44, 77,120, 168, 205,  207,247,249  ,293 ,315,343,395,454, 495,551,588,618,660,699,740, 800);

    // $headers1 in doctext.inc
    $aligns = array('center',	'center',	'center', 'center', 'center', 'center', 'center',	'center',	'center'
    , 'center', 'center', 'center', 'center',	'center',	'center', 'center', 'center', 'center', 'center',	'center',	'center',	'center',	'center');

    $col2 =  array(0,16, 44, 77,120, 168, 205,  207,247,249 ,293 ,315,343,395,454, 495,551,588,618,660,699,740,800);


    // $headers2 in doctext.inc
    $align2 =array('left',	'left',	'left', 'left', 'left', 'left', 'left',	'left',	'left'
    , 'left', 'left', 'left', 'left',	'left',	'left', 'left', 'left', 'left', 'left',	'left',	'center',	'center');



    $params = array('comments' => $comments);

    $cur = get_company_Pref('curr_default');

    if ($email == 0)
        $rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, 'L');

    $orientation = ($orientation = 'L');

//    if ($orientation == 'L')
//        recalculate_cols($cols);

    for ($i = $from; $i <= $to; $i++)
    {

        $myrow = get_po2($i);
        $baccount = get_default_bank_account($myrow['curr_code']);
        $params['bankaccount'] = $baccount['id'];

        if ($email == 1)
        {
            $rep = new FrontReport("", "", user_pagesize(), 9, $orientation);
            $rep->title = _('PURCHASE ORDER');
            $rep->filename = "PurchaseOrder" . $i . ".pdf";
        }
        $rep->SetHeaderType('Header2111');
        $rep->currency = $cur;
        $rep->Font();
        $rep->Info($params, $cols, null, $aligns, $col2,null,$align2);

        $contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
        $rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
        $rep->NewPage();

        $result = get_po_details2($i,$from_date,$to_date);
        $SubTotal = 0;
        $items = $prices = array();
        while ($myrow2=db_fetch($result))
        {

            $unit_cost=($myrow2['Gross_Amt'] +$myrow2['Landing_Amt']+$myrow2['INS_Amt']+$myrow2['F_E_D_Amt']+$myrow2['Duty_Amt']+$myrow2['I_Tax_Amt']+$myrow2['Other_Expense']
                +$myrow2['Add_S_T_Amt'])/$myrow2['quantity'];
            $Net = round2(($myrow2["unit_price"] * $myrow2["quantity"]), user_price_dec());
            $DutyFed = round2(($myrow2["F_E_D_Amt"] + $myrow2["Duty_Amt"]));
            $prices[] = $Net;
            $items[] = $myrow2['item_code'];
            $SubTotal += $Net;
            $dec2 = 0;
            $DisplayPrice = price_decimal_format($myrow2["unit_price"],$dec2);
            $DisplayQty = number_format2($myrow2["quantity"],get_qty_dec($myrow2['stock_id']));
            $DisplayNet = number_format2($Net,$dec);
            //////////PERCENTAGE
            // $rep->NewLine(+2);
            $rep->TextCol(0, 1,$myrow2['stock_id'], -2);

            // $rep->NewLine(-2);
            $rep->AmountCol( 2, 3,$myrow2['quantity'], -2);
            $total_qty +=$myrow2['quantity'];
            $rep->AmountCol( 3, 4,$myrow2['unit_price'], -2);
            $total_price +=$myrow2['unit_price'];
            $rep->AmountCol( 4, 5,$Net, -2);
            $Totalamount +=$Net;
            $rep->AmountCol( 5, 6,$myrow2['Gross_Amt'], -2);
            $totalgross +=$myrow2['Gross_Amt'];
            // $rep->TextCol( 6, 7,"  ".$myrow2['Landing']."%", -2);
            $rep->AmountCol( 7, 8,$myrow2['Value_invl_Landing'], -2);
            $totallanding +=$myrow2['Value_invl_Landing'];
            // $rep->TextCol( 8,9,	"  ".$myrow2['INS']."%", -2);
            $rep->AmountCol( 9,10,$myrow2['Value_Incl_INC'], -2);
            $totalINC +=$myrow2['Value_Incl_INC'];
//            $rep->NewLine();
            //$rep->TextCol( 10,11,"  ".$myrow2['F_E_D']."%", -2);
            // $rep->TextCol( 11,12,"  ".$myrow2['Duty']."%", -2);
            $rep->AmountCol( 12,13,$DutyFed, -2);
            $totaldutyfed +=$DutyFed;
            $rep->AmountCol( 13,14,$myrow2['Value_Excl_S_T'], -2);
            $totalvaluest +=$myrow2['Value_Excl_S_T'];
//            $rep->NewLine();
            // $rep->TextCol( 14,15,"   ".$myrow2['S_T']."%", -2);
            $rep->AmountCol( 15,16,$myrow2['Amount_Incl_S_T'], -2);
            $totalamountinc +=$myrow2['Amount_Incl_S_T'];
            // $rep->TextCol( 16,17,"   ".$myrow2['I_Tax']."%", -2);
            // $rep->TextCol( 17,18,"   ".$myrow2['Add_S_T']."%", -2);
            $totaladdst +=$myrow2['Add_S_T'];
            $rep->AmountCol( 18,19,$myrow2['Total_Charges'], -2);
            $totalcharges +=$myrow2['Total_Charges'];
            $rep->AmountCol( 19,20,$myrow2['Other_Expense'], -2);
            $totalotherexp +=$myrow2['Other_Expense'];
            $rep->AmountCol( 20,21,$myrow2['Net_Amount'], -2);
            $totalnetamt +=$myrow2['Net_Amount'];
            $rep->AmountCol( 21,22,$unit_cost, -2);
            $totalunitcost +=$unit_cost;

         //   $rep->AmountCol( 6, 7, $myrow2['Landing_Amt'], 0);
            $totallanamt +=$myrow2['Landing_Amt'];
          //  $rep->AmountCol( 8,9,	 $myrow2['INS_Amt'], 0);
            $totalincamt +=$myrow2['INS_Amt'];
            $rep->AmountCol( 10,11,$myrow2['F_E_D_Amt'],0);
            $totalfedamt +=$myrow2['F_E_D_Amt'];
            $rep->AmountCol( 11,12,$myrow2['Duty_Amt'], 0);
            $totaldutyamt +=$myrow2['Duty_Amt'];
            $rep->AmountCol( 14,15,$myrow2['S_T_Amt'], 0);
            $totalstamt +=$myrow2['S_T_Amt'];
            $rep->AmountCol( 16,17,$myrow2['I_Tax_Amt'], 0);
            $totalitaxamt +=$myrow2['I_Tax_Amt'];
            $rep->AmountCol( 17,18,$myrow2['Add_S_T_Amt'], 0);
            $totaladdstamt +=$myrow2['Add_S_T_Amt'];
            $rep->TextCollines(1, 2,$myrow2['description'], -2);
            $rep->multicell(85,15,"",0,'L',0,0,450,57);


            if ($rep->row < $rep->bottomMargin + (11 * $rep->lineHeight))
                $rep->NewPage();
        }
        if ($myrow['comments'] != "")
        {
            $rep->NewLine();
            $rep->TextColLines(1, 5, $myrow['comments'], -2);
        }
        $DisplaySubTot = number_format2($SubTotal,$dec);

        $rep->row = $rep->bottomMargin + (15* $rep->lineHeight);
        $doctype = ST_PURCHORDER;

//		$rep->TextCol(4, 5, _("Sub-total"), -2);
//		$rep->TextCol(5, 6,	$DisplaySubTot, -2);
        $rep->NewLine();

        $rep->NewLine();
        $rep->NewLine();
        $DisplayTotal = number_format2($SubTotal, $dec);
        $rep->Font('bold');
        $rep->NewLine();
        $rep->NewLine();
        $rep->TextCol(1, 2, _("TOTAL"), - 2);

        $words = price_in_words($SubTotal, ST_PURCHORDER);
        $amount_in_words = _number_to_words($SubTotal);
        if ($words != "")
        {
            $rep->NewLine(1);
            $rep->TextCol(1, 7, $myrow['curr_code'] . ": " . $words, - 2);
        }
        $rep->Font();
        if ($email == 1)
        {
            $myrow['DebtorName'] = $myrow['supp_name'];

            if ($myrow['reference'] == "")
                $myrow['reference'] = $myrow['order_no'];
            $rep->End($email);
        }
        $do_no1 =get_ref_for_order_no($myrow["trans_no"]);
        $trans_ = '';
        while($row = db_fetch($do_no1))
        {
            if($trans_ != '')
                $trans_ .= ',';

            $trans_ .=  $row['reference'];
        }

//        $rep->MultiCell(230,13," PO No:              ".$trans_,0,'L', 0, 2,402,115,true);

    }
    $rep->AmountCol(2, 3,	$total_qty, 0);
    $rep->AmountCol(3,4,$total_price,0);
    $rep->AmountCol(4,5,$Totalamount,0);
   $rep->AmountCol(5,6,$totalgross,0);
   $rep->AmountCol(7,8,$totallanding,0);
    $rep->AmountCol(9,10,$totalINC,0);
    $rep->AmountCol(12,13,$totaldutyfed,0);
   $rep->AmountCol(13,14,$totalvaluest,0);
  $rep->AmountCol(15,16,$totalamountinc,0);
  $rep->AmountCol(16,17,$totalitaxamt,0);

    $rep->AmountCol(18,19,$totalcharges,0);
    $rep->AmountCol(19,20,$totalotherexp,0);
   $rep->AmountCol(20,21,$totalnetamt,0);
    $rep->AmountCol(21,22,$totalunitcost,0);

//    $rep->AmountCol(6,7,$totallanamt,0);
//    $rep->AmountCol(8,9,$totalincamt,0);
//    $rep->AmountCol(10,11,$totalfedamt,0);
//    $rep->AmountCol(11,12,$totaldutyamt,0);
//    $rep->AmountCol(14,15,$totalstamt,0);
//    $rep->AmountCol(16,17,$totalitaxamt,0);
//    $rep->AmountCol(17,18,$totaladdstamt,0);
//   $rep->multicell(85,15, $total_qty,1,'L',0,0,85,380);
    $rep->font('b');
    $rep->multicell(85,15,"Amount in words:",0,'L',0,0,40,720);
    $rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 720);
    $rep->font('');

    $rep->multicell(150,15,"________________________",0,'L',0,0,40,800);
    $rep->multicell(150,15,"Prepared by",0,'L',0,0,65,810);
    $rep->multicell(150,15,"________________________",0,'L',0,0,350,800);
    $rep->multicell(150,15,"Approved by",0,'L',0,0,380,810);


    if ($email == 0)
//        $rep->Line($rep->row - $rep->lineHeight + 10);

        $rep->End();
}

?>