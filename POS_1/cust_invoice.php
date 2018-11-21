<?php
//---------------------------------------------------------------------------
//
//	Entry/Modify Sales Invoice against single delivery
//	Entry/Modify Batch Sales Invoice against batch of deliveries
//
$page_security = 'SA_SALESINVOICE';
$path_to_root = "..";
include_once($path_to_root . "/POS/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/manufacturing.inc");
include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/POS/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/taxes/tax_calc.inc");





$js = "";
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}

if (isset($_GET['ModifyInvoice'])) {
	$_SESSION['page_title'] = sprintf(_("Modifying Sales Invoice # %d.") ,$_GET['ModifyInvoice']);
	$help_context = "Modifying Sales Invoice";
} elseif (isset($_GET['DeliveryNumber'])) {
	$_SESSION['page_title'] = _($help_context = "Issue an Invoice for Completion Note");
} elseif (isset($_GET['BatchInvoice'])) {
	$_SESSION['page_title'] = _($help_context = "Issue Batch Invoice for Completion Notes");
}

page($_SESSION['page_title'], false, false, "", $js);

//-----------------------------------------------------------------------------
check_edit_conflicts();


if (isset($_GET['AddedID'])) {


	global $path_to_root, $pdf_debug,$def_print_orientation;


	$invoice_no = $_GET['AddedID'];
	$trans_type = ST_SALESINVOICE;
function get_max_invoice()
    {
        $sql = "SELECT MAX(`trans_no`) FROM `".TB_PREF."debtor_trans` WHERE type=10";
        $result = db_query($sql, "could not get customer");
        $row = db_fetch_row($result);
        return $row[0];
    }
//	display_notification(_("Selected invoice has been processed"), true);

	//display_note(print_document_link($invoice_no."-".$trans_type, _("&Print This Invoice"), true, ST_SALESINVOICE));

	$url = $path_to_root.'/reporting/prn_redirect.php?';
	$def_orientation = (isset($def_print_orientation) && $def_print_orientation == 1 ? 1 : 0);
 $rep = get_reports_id($trans_type);
	//$doc_no = $invoice_no."-".$trans_type;
         $doc_no = get_max_invoice();
	/*$rep = $type_no==ST_CUSTCREDIT ? 113 : 107;
	// from, to, currency, email, paylink, comments, orientation
	$ar = array(
		'PARAM_0' => $doc_no,
		'PARAM_1' => $doc_no,
		'PARAM_2' => '',
		'PARAM_3' => $email,
		'PARAM_4' => '',
		'PARAM_5' => '',
		'PARAM_6' => $def_orientation);*/
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('thickboxId').click();

            document.getElementById('thickboxform').click();

        });


    </script>
    <?php
    echo'<a id="thickboxform" href="../POS/sales_modify_entry.php?NewOrder=Yes" target="_self"></a>';
    echo'<a id="thickboxId" href="../reporting/prn_redirect.php?PARAM_0='.$invoice_no.'&PARAM_1='.$invoice_no.'&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6='.$def_orientation.'&REP_ID='.$rep.'" target="_blank"></a>';


//	header("Location: ".$path_to_root."/reporting/prn_redirect.php?PARAM_0=$doc_no&PARAM_1=$doc_no&PARAM_2=&PARAM_3=0&PARAM_4=&PARAM_5=&PARAM_6=$def_orientation&REP_ID=107772");
//	header("Location: ".$path_to_root."/POS/manage/orders_tables.php?");

	/*	display_note(get_customer_trans_view_str($trans_type, $invoice_no, _("&View This Invoice")), 0, 1);


	display_note(print_document_link($invoice_no."-".$trans_type, _("&Email This Invoice"), true, ST_SALESINVOICE, false, "printlink", "", 1),1);

	display_note(get_gl_view_str($trans_type, $invoice_no, _("View the GL &Journal Entries for this Invoice")),1);

	hyperlink_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", _("Select Another &Completion Note For Invoicing"), "OutstandingOnly=1");

	hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$invoice_no");
*/
	display_footer_exit();

}

//-----------------------------------------------------------------------------

if ( (isset($_GET['DeliveryNumber']) && ($_GET['DeliveryNumber'] > 0) )
|| isset($_GET['BatchInvoice'])) {

	processing_start();

	if (isset($_GET['BatchInvoice'])) {
		$src = $_SESSION['DeliveryBatch'];
		unset($_SESSION['DeliveryBatch']);
	} else {
		$src = array($_GET['DeliveryNumber']);
	}

	/*read in all the selected deliveries into the Items cart  */
	$dn = new Cart(ST_CUSTDELIVERY, $src, true);

//	print_r($dn);

//	if ($dn->count_items() == 0) {
//		hyperlink_params($path_to_root . "/sales/inquiry/sales_deliveries_view.php",
//			_("Select a different delivery to invoice asdasdasdasdas"), "OutstandingOnly=1");
//
//		header("Location: ".$path_to_root."/POS/manage/orders_tables.php?");
//
//	//	die ("<br><b>" . _(" sdasdasd There are no delivered items with a quantity left to invoice. There is nothing left to invoice.") . "</b>");
//	}

	$_SESSION['Items'] = $dn;
	//asad

	$newinvoice =  $_SESSION['Items']->trans_no == 0;
	if ($newinvoice)
		new_doc_date($_SESSION['Items']->document_date);


    $_SESSION['Items']->reference = $Refs->get_next(ST_SALESINVOICE);
	$invoice_no = $_SESSION['Items']->write();
	if ($invoice_no == -1)
	{
		display_error(_("The entered reference is already in use."));
		set_focus('ref');
	}
	else
	{
		processing_end();

		if ($newinvoice) {
			meta_forward($_SERVER['PHP_SELF'], "AddedID=$invoice_no");
		} else {
			meta_forward($_SERVER['PHP_SELF'], "UpdatedID=$invoice_no");
		}
	}


	//asad

}elseif (!processing_active()) {
	/* This page can only be called with a delivery for invoicing or invoice no for edit */
	display_error(_("This page can only be opened after delivery selection. Please select delivery to invoicing first."));

	hyperlink_no_params("$path_to_root/sales/inquiry/sales_deliveries_view.php", _("Select Delivery to Invoice"));

	end_page();
	exit;
}
//-----------------------------------------------------------------------------

//-----------------------------------------------------------------------------


//-----------------------------------------------------------------------------


end_page();

?>