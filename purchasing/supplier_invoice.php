<?php

$page_security = 'SA_SUPPLIERINVOICE';
$path_to_root = "..";

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");

include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
//----------------------------------------------------------------------------------------

check_db_has_suppliers(_("There are no suppliers defined in the system."));

//--------------------------------------------------------------------------------------------------

if (isset($_GET['New']))
{
	if (isset( $_SESSION['supp_trans']))
	{
		unset ($_SESSION['supp_trans']->grn_items);
		unset ($_SESSION['supp_trans']->gl_codes);
		unset ($_SESSION['supp_trans']);
	}
	$help_context = "Enter Supplier Invoice";
	$_SESSION['page_title'] = _("Enter Supplier Invoice");

	$_SESSION['supp_trans'] = new supp_trans(ST_SUPPINVOICE);
} else if(isset($_GET['ModifyInvoice'])) {
	$help_context = 'Modifying Purchase Invoice';
	$_SESSION['page_title'] = sprintf( _("Modifying Purchase Invoice # %d"), $_GET['ModifyInvoice']);
	$_SESSION['supp_trans'] = new supp_trans(ST_SUPPINVOICE, $_GET['ModifyInvoice']);
}

page($_SESSION['page_title'], false, false, "", $js);

if (isset($_GET['ModifyInvoice']))
	check_is_editable(ST_SUPPINVOICE, $_GET['ModifyInvoice']);

//---------------------------------------------------------------------------------------------------------------

if (isset($_GET['AddedID'])) 
{
	$invoice_no = $_GET['AddedID'];
	$trans_type = ST_SUPPINVOICE;
    $trans_type1 = 7899;


    echo "<center>";
    display_notification_centered(_("Supplier invoice has been processed."));
    display_note(get_trans_view_str($trans_type, $invoice_no, _("View this Invoice")));

	display_note(get_gl_view_str($trans_type, $invoice_no, _("View the GL Journal Entries for this Invoice")), 1);

	hyperlink_params("$path_to_root/purchasing/supplier_payment.php", _("Entry supplier &payment for this invoice"),
		"PInvoice=".$invoice_no);

	hyperlink_params($_SERVER['PHP_SELF'], _("Enter Another Invoice"), "New=1");

	hyperlink_params("$path_to_root/admin/attachments.php", _("Add an Attachment"), "filterType=$trans_type&trans_no=$invoice_no");
	
	echo "</br>";
submenu_print(_("&Print This Supplier invoice"), $trans_type1, $invoice_no, 'prtopt'); 
	display_footer_exit();
}

//--------------------------------------------------------------------------------------------------

if (isset($_GET['New']))
{
	if (isset( $_SESSION['supp_trans']))
	{
		unset ($_SESSION['supp_trans']->grn_items);
		unset ($_SESSION['supp_trans']->gl_codes);
		unset ($_SESSION['supp_trans']);
	}

	$_SESSION['supp_trans'] = new supp_trans(ST_SUPPINVOICE);
} else if(isset($_GET['ModifyInvoice'])) {
	$_SESSION['supp_trans'] = new supp_trans(ST_SUPPINVOICE, $_GET['ModifyInvoice']);
}

//--------------------------------------------------------------------------------------------------
function clear_fields()
{
	global $Ajax;
	
	unset($_POST['gl_code']);
	unset($_POST['dimension_id']);
	unset($_POST['dimension2_id']);
	unset($_POST['amount']);
	unset($_POST['memo_']);
	unset($_POST['AddGLCodeToTrans']);
	$Ajax->activate('gl_items');
	set_focus('gl_code');
}

function reset_tax_input()
{
	global $Ajax;

	unset($_POST['mantax']);
	$Ajax->activate('inv_tot');
}

//------------------------------------------------------------------------------------------------
//	GL postings are often entered in the same form to two accounts
//  so fileds are cleared only on user demand.
//
if (isset($_POST['ClearFields']))
{
	clear_fields();
}

if (isset($_POST['AddGLCodeToTrans'])){

	$Ajax->activate('gl_items');
	$input_error = false;

	$result = get_gl_account_info($_POST['gl_code']);
	if (db_num_rows($result) == 0)
	{
		display_error(_("The account code entered is not a valid code, this line cannot be added to the transaction."));
		set_focus('gl_code');
		$input_error = true;
	}
	else
	{
		$myrow = db_fetch_row($result);
		$gl_act_name = $myrow[1];
		if (!check_num('amount'))
		{
			display_error(_("The amount entered is not numeric. This line cannot be added to the transaction."));
			set_focus('amount');
			$input_error = true;
		}
	}

	if (!is_tax_gl_unique(get_post('gl_code'))) {
   		display_error(_("Cannot post to GL account used by more than one tax type."));
		set_focus('gl_code');
   		$input_error = true;
	}

	if ($input_error == false)
	{
		$_SESSION['supp_trans']->add_gl_codes_to_trans($_POST['gl_code'], $gl_act_name,
			$_POST['dimension_id'], $_POST['dimension2_id'], 
			input_num('amount'), $_POST['memo_']);
		reset_tax_input();
		set_focus('gl_code');
	}
}

//------------------------------------------------------------------------------------------------

function check_data()
{
	global $Refs;

	if (!$_SESSION['supp_trans']->is_valid_trans_to_post())
	{
		display_error(_("The invoice cannot be processed because the there are no items or values on the invoice.  Invoices are expected to have a charge."));
		return false;
	}

	if (!check_reference($_SESSION['supp_trans']->reference, ST_SUPPINVOICE, $_SESSION['supp_trans']->trans_no))
	{
		set_focus('reference');
		return false;
	}

	if (!is_date( $_SESSION['supp_trans']->tran_date))
	{
		display_error(_("The invoice as entered cannot be processed because the invoice date is in an incorrect format."));
		set_focus('trans_date');
		return false;
	} 
	elseif (!is_date_in_fiscalyear($_SESSION['supp_trans']->tran_date)) 
	{
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('trans_date');
		return false;
	}
	if (!is_date( $_SESSION['supp_trans']->due_date))
	{
		display_error(_("The invoice as entered cannot be processed because the due date is in an incorrect format."));
		set_focus('due_date');
		return false;
	}

	if (trim(get_post('supp_reference')) == false)
	{
		display_error(_("You must enter a supplier's invoice reference."));
		set_focus('supp_reference');
		return false;
	}

	if (is_reference_already_there($_SESSION['supp_trans']->supplier_id, $_POST['supp_reference'], $_SESSION['supp_trans']->trans_no))
	{ 	/*Transaction reference already entered */
		display_error(_("This invoice number has already been entered. It cannot be entered again.") . " (" . $_POST['supp_reference'] . ")");
		set_focus('supp_reference');
		return false;
	}
	$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['tran_date'], 'd');

		if($row == 0)
	
		{
			$allowed_days = 'before yesterday.';
		}
		
		else
			$allowed_days =  'more than '. $row . ' day old' ;

		if($diff > $row  ){

			display_error("You are not allowed to enter entries $allowed_days");
			return false;
		}

//		else
//		{
//			if($diff < 0 )
//			{
//				display_error("You are not allowed to enter data $row day/s ahead");
//				return false;
//			}

		//}

	}

	if($row1 != '')
	{

	$diff_futuredays   =  date_diff2($_POST['tran_date'],date('d-m-Y'), 'd');
			
				if( $diff_futuredays > $row1)
			{
			//	display_error($diff_futuredays);
		display_error("You are not allowed to enter data $row1 day/s ahead");

       return false ;

			}

	}
	if($row2 != '')
	{

		$now = date('h:i:s');

		if($row2 != 0)
		{
			$allowed_time = 'after '. $row2;
		}
		else
			$allowed_time=  '' ;

	if($row2 > $now )
		{
			display_error("You are not allowed to enter data $allowed_time pm");
			return false ;
		}

	}
	return true;
}

//--------------------------------------------------------------------------------------------------

function handle_commit_invoice()
{
	copy_to_trans($_SESSION['supp_trans']);

	if (!check_data())
		return;
	$inv = $_SESSION['supp_trans'];
	$invoice_no = add_supp_invoice($inv);

    $_SESSION['supp_trans']->clear_items();
    unset($_SESSION['supp_trans']);

	meta_forward($_SERVER['PHP_SELF'], "AddedID=$invoice_no");
}

//--------------------------------------------------------------------------------------------------

if (isset($_POST['PostInvoice']))
{
	handle_commit_invoice();
}

function check_item_data($n)
{
	global $SysPrefs;

	if (!check_num('this_quantity_inv'.$n, 0) || input_num('this_quantity_inv'.$n)==0)
	{
		display_error( _("The quantity to invoice must be numeric and greater than zero."));
		set_focus('this_quantity_inv'.$n);
		return false;
	}

	if (!check_num('ChgPrice'.$n))
	{
		display_error( _("The price is not numeric."));
		set_focus('ChgPrice'.$n);
		return false;
	}

	$margin = $SysPrefs->over_charge_allowance();
	if ($SysPrefs->check_price_charged_vs_order_price == True)
	{
		if ($_POST['order_price'.$n]!=input_num('ChgPrice'.$n)) {
		     if ($_POST['order_price'.$n]==0 ||
				input_num('ChgPrice'.$n)/$_POST['order_price'.$n] >
			    (1 + ($margin/ 100)))
		    {
			display_error(_("The price being invoiced is more than the purchase order price by more than the allowed over-charge percentage. The system is set up to prohibit this. See the system administrator to modify the set up parameters if necessary.") .
			_("The over-charge percentage allowance is :") . $margin . "%");
			set_focus('ChgPrice'.$n);
			return false;
		    }
		}
	}

	if ($SysPrefs->check_qty_charged_vs_del_qty == true && ($_POST['qty_recd'.$n] != $_POST['prev_quantity_inv'.$n]))
	{
		if (input_num('this_quantity_inv'.$n) / ($_POST['qty_recd'.$n] - $_POST['prev_quantity_inv'.$n]) >
			(1+ ($margin / 100)))
		{
			display_error( _("The quantity being invoiced is more than the outstanding quantity by more than the allowed over-charge percentage. The system is set up to prohibit this. See the system administrator to modify the set up parameters if necessary.")
			. _("The over-charge percentage allowance is :") . $margin . "%");
			set_focus('this_quantity_inv'.$n);
			return false;
		}
	}

	return true;
}

function commit_item_data($n)
{
	if (check_item_data($n))
	{
    	if (input_num('this_quantity_inv'.$n) >= ($_POST['qty_recd'.$n] - $_POST['prev_quantity_inv'.$n]))
    	{
    		$complete = true;
    	}
    	else
    	{
    		$complete = false;
    	}

	$_SESSION['supp_trans']->add_grn_to_trans($n, $_POST['po_detail_item'.$n],
	$_POST['item_code'.$n], $_POST['item_description'.$n], $_POST['qty_recd'.$n],
	$_POST['prev_quantity_inv'.$n], input_num('this_quantity_inv'.$n),
	$_POST['order_price'.$n], input_num('ChgPrice'.$n), $complete,
	$_POST['std_cost_unit'.$n],$_POST['text1'.$n],$_POST['text2'.$n],$_POST['text3'.$n],$_POST['text4'.$n],$_POST['text5'.$n],
	$_POST['text6'.$n],$_POST['text7'.$n],$_POST['amount1'.$n],$_POST['amount2'.$n],$_POST['amount3'.$n],$_POST['amount4'.$n],$_POST['amount5'.$n],
	$_POST['amount6'.$n], $_POST['date1'.$n],$_POST['date2'.$n],$_POST['date3'.$n],
	$_POST['combo1'.$n],$_POST['combo2'.$n],$_POST['combo3'.$n],$_POST['combo4'.$n],$_POST['combo5'.$n],
	$_POST['combo6'.$n],$_POST['batch'.$n],$_POST['con_factor'.$n],$_POST['units_id'.$n],$_POST['dimension'.$n],
	$_POST['dimension2'.$n],$_POST['discount_percent'.$n],$_POST['grn_id'.$n]);
		reset_tax_input();
	}
}

//-----------------------------------------------------------------------------------------

$id = find_submit('grn_item_id');
if ($id != -1)
{
	commit_item_data($id);
}

if (isset($_POST['InvGRNAll']))
{
   	foreach($_POST as $postkey=>$postval )
    {
		if (strpos($postkey, "qty_recd") === 0)
		{
			$id = substr($postkey, strlen("qty_recd"));
			$id = (int)$id;
			commit_item_data($id);
		}
    }
}	

//--------------------------------------------------------------------------------------------------
$id3 = find_submit('Delete');
if ($id3 != -1)
{
	$_SESSION['supp_trans']->remove_grn_from_trans($id3);
	$Ajax->activate('grn_items');
	reset_tax_input();
}

$id4 = find_submit('Delete2');
if ($id4 != -1)
{
	$_SESSION['supp_trans']->remove_gl_codes_from_trans($id4);
	clear_fields();
	reset_tax_input();
	$Ajax->activate('gl_items');
}
$id2 = -1;
if ($_SESSION["wa_current_user"]->can_access('SA_GRNDELETE'))
{
	$id2 = find_submit('void_item_id');
	if ($id2 != -1) 
	{
		remove_not_invoice_item($id2);
		display_notification(sprintf(_('All yet non-invoiced items on delivery line # %d has been removed.'), $id2));

	}
}
if (isset($_POST['go']))
{
	$Ajax->activate('gl_items');
	display_quick_entries($_SESSION['supp_trans'], $_POST['qid'], input_num('totamount'), QE_SUPPINV);
	$_POST['totamount'] = price_format(0); $Ajax->activate('totamount');
	reset_tax_input();
}

start_form();

invoice_header($_SESSION['supp_trans']);

if ($_POST['supplier_id']=='') 
		display_error(_("There is no supplier selected."));
else {
	display_grn_items($_SESSION['supp_trans'], 1);

    $id = find_submit('grn_item_id');
    $grnn += $_POST['grn_id'.$id];
//    display_error($_POST['grn_id'.$id]."batch");
//
//    display_error("//".$grnn);
	display_gl_items($_SESSION['supp_trans'], 1,$_POST['grn_id'.$id]);

	div_start('inv_tot');
	invoice_totals($_SESSION['supp_trans']);
	div_end();

}

//-----------------------------------------------------------------------------------------

if ($id != -1 || $id2 != -1)
{
	$Ajax->activate('grn_items');
	$Ajax->activate('inv_tot');
}

if (get_post('AddGLCodeToTrans'))
	$Ajax->activate('inv_tot');

br();
submit_center('PostInvoice', _("Enter Invoice"), true, '', 'default');
br();

end_form();

//--------------------------------------------------------------------------------------------------

end_page();
