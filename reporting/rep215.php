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
		AND ".TB_PREF."supp_trans.trans_no = ".db_escape($trans_no);
   	$result = db_query($sql, "The order cannot be retrieved");
    return db_fetch($result);
}
///-------------------------------
function get_po_details2($supp_trans_no)
{
	$sql = "SELECT ".TB_PREF."supp_invoice_items.*, ".TB_PREF."stock_master.units
		FROM ".TB_PREF."supp_invoice_items
		LEFT JOIN ".TB_PREF."stock_master
		ON ".TB_PREF."supp_invoice_items.stock_id=".TB_PREF."stock_master.stock_id
		WHERE supp_trans_no =".db_escape($supp_trans_no)." ";
	$sql .= " ORDER BY supp_trans_no";
	return db_query($sql, "Retreive order Line Items");
}


//function get_gl_details($type_no)
//{
//    $sql = "SELECT * FROM  0_gl_trans
//		WHERE type_no =".db_escape($type_no)." AND type=20";
////    $sql .= " ORDER BY account";
//    return db_query($sql, "Retreive order Line Items");
//}
///---------------------------------------------------
function get_gl_details2($type_no)
{
    $sql = "SELECT ".TB_PREF."gl_trans.*, ".TB_PREF."chart_master.account_name
		FROM ".TB_PREF."gl_trans
		LEFT JOIN ".TB_PREF."chart_master
		ON ".TB_PREF."gl_trans.account=".TB_PREF."chart_master.account_code
		WHERE type_no =".db_escape($type_no)."  AND type=20 ";
//    $sql .= " ORDER BY supp_trans_no";
    return db_query($sql, "Retreive order Line Items");
}

///----------------------------------------------------------
function get_ref_for_order_no($trans_no)
{
    $sql = "SELECT ".TB_PREF."purch_orders.reference 
    FROM ".TB_PREF."purch_orders, ".TB_PREF."purch_order_details, ".TB_PREF."grn_items,
    ".TB_PREF."supp_invoice_items
   WHERE ".TB_PREF."purch_orders.order_no = ".TB_PREF."purch_order_details.order_no
   AND ".TB_PREF."purch_order_details.po_detail_item = ".TB_PREF."grn_items.po_detail_item
   AND ".TB_PREF."grn_items.id = ".TB_PREF."supp_invoice_items.grn_item_id
   AND ".TB_PREF."supp_invoice_items.supp_trans_no = ".db_escape($trans_no)."
   ";
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}

function get_ref_for_grn_no($trans_no)
{
    $sql = "SELECT ".TB_PREF."grn_batch.reference 
    FROM ".TB_PREF."grn_batch, ".TB_PREF."grn_items,
    ".TB_PREF."supp_invoice_items
   WHERE ".TB_PREF."grn_batch.id = ".TB_PREF."grn_items.grn_batch_id
   AND ".TB_PREF."grn_items.id = ".TB_PREF."supp_invoice_items.grn_item_id
   AND ".TB_PREF."supp_invoice_items.supp_trans_no = ".db_escape($trans_no)."
   ";
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}

function print_po()
{
	global $path_to_root, $show_po_item_codes;

	include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$from = $_POST['PARAM_0'];
	$to = $_POST['PARAM_1'];
	$currency = $_POST['PARAM_2'];
	$email = $_POST['PARAM_3'];
	$comments = $_POST['PARAM_4'];
	$orientation = $_POST['PARAM_5'];

	if (!$from || !$to) return;

	$orientation = ($orientation ? 'L' : 'P');
	$dec = user_price_dec();

	$cols = array(4,  120, 400, 480);

	// $headers in doctext.inc
	$aligns = array('left',	'left', 'left', 'left');

	$params = array('comments' => $comments);

	$cur = get_company_Pref('curr_default');

	if ($email == 0)
		$rep = new FrontReport(_('PURCHASE ORDER'), "PurchaseOrderBulk", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

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
		$rep->SetHeaderType('Header215');
		$rep->currency = $cur;
		$rep->Font();
		$rep->Info($params, $cols, null, $aligns);

		$contacts = get_supplier_contacts($myrow['supplier_id'], 'order');
		$rep->SetCommonData($myrow, null, $myrow, $baccount, ST_PURCHORDER, $contacts);
		$rep->NewPage();
        $total_debit = $total_credit = 0;
		$result = get_gl_details2($i);
		$SubTotal = 0;
		$items = $prices = array();
		while ($myrow2=db_fetch($result))
		{


            $rep->TextCol(0, 1,	$myrow2['account'], -2);
            $rep->TextCol(1, 2,	$myrow2['account_name'], -2);
            if ($myrow2['amount'] > 0.0) {
                $rep->AmountCol(2, 3, abs($myrow2['amount']), $dec);
                $total_debit += abs($myrow2['amount']);
            }
            else {
                $rep->AmountCol(3, 4, abs($myrow2['amount']), $dec);
                $total_credit += abs($myrow2['amount']);
            }

		$rep->NewLine(1);
			if ($rep->row < $rep->bottomMargin + (15 * $rep->lineHeight))
				$rep->NewPage();

            if ($rep->row < $rep->bottomMargin + ($rep->lineHeight)) {
                $rep->LineTo($rep->leftMargin, 43.4 * $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->leftMargin, 43.4* $rep->lineHeight ,$rep->leftMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-245,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-245, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-445, 43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-445, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-205,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-205, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-120, 43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-120, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-498,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-498, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-172.3,  43.4 * $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-172.3, $rep->row);
                $rep->LineTo($rep->pageWidth - $rep->rightMargin-65,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-65, $rep->row);
                $rep->Line($rep->row);

                $rep->NewPage();
            }


		}
        $rep->LineTo($rep->leftMargin, 43.4* $rep->lineHeight ,$rep->leftMargin, $rep->row);
        $rep->LineTo($rep->pageWidth - $rep->rightMargin,  43.4 * $rep->lineHeight,$rep->pageWidth - $rep->rightMargin, $rep->row);

        $rep->LineTo($rep->pageWidth - $rep->rightMargin-445, 43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-445, $rep->row);

        $rep->LineTo($rep->pageWidth - $rep->rightMargin-140, 43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-140, $rep->row);

        $rep->LineTo($rep->pageWidth - $rep->rightMargin-65,  43.4* $rep->lineHeight ,$rep->pageWidth - $rep->rightMargin-65, $rep->row);

        $rep->Line($rep->row);

		$doctype = ST_PURCHORDER;




        $rep->NewLine();
        $rep->TextCol(0, 3, _("Total Debit / Credit"));
        $rep->AmountCol(2, 3, $total_debit, $dec);
        $rep->AmountCol(3, 4, $total_credit, $dec);
			$rep->NewLine();

		$rep->NewLine();

		$words = price_in_words($total_credit, ST_PURCHORDER);
        $amount_in_words = _number_to_words($total_credit);
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



    }
    $rep->NewLine();
    $rep->font('b');
    $rep->TextCol(0, 1,	"Amount In Words", -2);
    $rep->TextCol(1, 5,"Rupees ".$amount_in_words." Only",  -2);
    //$rep->NewLine(-15);
    //$rep->multicell(400, 15,"Rupees ".$amount_in_words." Only", 0, 'L', 0, 0, 125, 720);
    $rep->font('');
    $rep->NewLine(17);
    $rep->TextCol(0, 25,"______________________", -2);

    $rep->TextCol(2, 7,"______________________", -2);
    $rep->NewLine(-17);
    $rep->NewLine(18);
    $rep->TextCol(0, 5,"          Prepared By", -2);
    $rep->TextCol(1, 25,"                                                                                                                         Approved By", -2);
    $rep->NewLine(-18);


    if ($email == 0)
		$rep->End();
}

?>