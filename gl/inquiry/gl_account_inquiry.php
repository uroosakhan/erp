<?php

$page_security = 'SA_GLTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/db_pager.inc");

include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

$js = '';
set_focus('account');
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

page(_($help_context = "General Ledger Inquiry"), false, false, '', $js);

//----------------------------------------------------------------------------------------------------
// Ajax updates
//


if (get_post('Show')) 
{
	$Ajax->activate('trans_tbl');
}

if (isset($_GET["account"]))
	$_POST["account"] = $_GET["account"];
if (isset($_GET["TransFromDate"]))
	$_POST["TransFromDate"] = $_GET["TransFromDate"];
if (isset($_GET["TransToDate"]))
	$_POST["TransToDate"] = $_GET["TransToDate"];
if (isset($_GET["Dimension"]))
	$_POST["Dimension"] = $_GET["Dimension"];
if (isset($_GET["Dimension2"]))
	$_POST["Dimension2"] = $_GET["Dimension2"];
if (isset($_GET["amount_min"]))
	$_POST["amount_min"] = $_GET["amount_min"];
if (isset($_GET["amount_max"]))
	$_POST["amount_max"] = $_GET["amount_max"];

if (!isset($_POST["amount_min"]))
	$_POST["amount_min"] = price_format(0);
if (!isset($_POST["amount_max"]))
	$_POST["amount_max"] = price_format(0);

//----------------------------------------------------------------------------------------------------

function gl_inquiry_controls()
{
	$dim = get_company_pref('use_dimension');
    start_form();

    start_table(TABLESTYLE_NOBORDER);
	start_row();


// 	gl_all_accounts_list_cells(_("Account:"), 'account', null, false, false, _("All Accounts"),true);

// global  $db_connections;

// if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RS'){

// 		gl_account_types_list_cells(_("Account:"), 'account', null, _("All Accounts"), true,true);

// 	}else{
// 		gl_all_accounts_list_cells(_("Account:"), 'account', null, false, false, _("All Accounts"),true);
// 	}



	gl_all_accounts_list_cells(_("GL Account:"), 'account', null, false, false, _("All Accounts"),true);




    gl_account_types_list_cells(_("GL Account Group:"), 'sub_account', null, _("All Accounts"), true,true);



	if (is_subledger_account($_POST['account']))
	{	// for home currency postings show all customers

		subledger_dashboard_list_cells(_("Counter Party"), 'person_id', $_POST['account'], null,true);

	}
	
	date_cells(_("from:"), 'TransFromDate', '', null, -user_transaction_days());
	date_cells(_("to:"), 'TransToDate');
    ref_cells(_("Cheque #"), 'cheque_no');
	end_row();
	end_table();

	start_table(TABLESTYLE_NOBORDER);
	start_row();
	if ($dim >= 1)
		dimensions_list_cells(_("Dimension")." 1:", 'Dimension', null, true, " ", false, 1);
	if ($dim > 1)
		dimensions_list_cells(_("Dimension")." 2:", 'Dimension2', null, true, " ", false, 2);

	small_amount_cells(_("Amount min:"), 'amount_min', null, " ");
	small_amount_cells(_("Amount max:"), 'amount_max', null, " ");
end_row();
start_row();

    ref_cells(_("Reference:"), 'ref', '',null, _('Enter reference fragment or leave empty'));

	ref_cells(_("Trans #:"), 'trans_no');

	ref_cells(_("Memo:"), 'memo', '',null, _('Enter memo fragment or leave empty'));

	submit_cells('Show',_("Show"),'','', 'default');
	end_row();
	end_table();

	echo '<hr>';
    end_form();
}

//----------------------------------------------------------------------------------------------------

function edit_link_in_account_inquiry($row)
{
	$ok = true;
	if ($row['trans_type'] == ST_SALESINVOICE)
	{
		$myrow = get_customer_trans($row["type_no"], $row["type"]);
		if ($myrow['alloc'] != 0 || get_voided_entry(ST_SALESINVOICE, $row["type"]) !== false)
			$ok = false;
	}
	echo "<td>";
	echo $ok ? trans_editor_link( $row["type"], $row["type_no"]) : '';
	echo "</td>";
}
function gl_link_in_account_inquiry($row)
{
	echo "<td>";
	echo get_gl_view_str($row["type"], $row["type_no"], "", false, '', '', $row['approval']);
	echo "</td>";
}
function prt_link_in_account_inquiry($row)
{
	echo "<td>";
	echo print_document_link($row['type_no'], _("Print"), true, $row['type'], ICON_PRINT);
	echo "</td>";
}
function prt_link2_in_account_inquiry($row)
{   echo "<td>";
    global $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]!='NKR') {

        if ($row['type'] == ST_BANKPAYMENT || $row['type'] == ST_BANKDEPOSIT || $row['type'] == ST_BANKTRANSFER
            || $row['type'] == ST_CPV || $row['type'] == ST_CRV || $row['type'] == ST_SUPPAYMENT
        )
            echo print_document_link($row['type_no'], _("Print"), true, ST_CUSTPAYMENT_A5, ICON_PRINT, null, null, null, null, $row['type']);
        else
            echo '';
    }
    else
        {
            if ($row['type'] == ST_BANKPAYMENT || $row['type'] == ST_BANKDEPOSIT || $row['type'] == ST_BANKTRANSFER
                || $row['type'] == ST_CPV || $row['type'] == ST_CRV || $row['type'] == ST_SUPPAYMENT
            )
                echo print_document_link($row['type_no'], _("Print"), true, ST_CUSTPAYMENT_A5_, ICON_PRINT, null, null, null, null, $row['type']);
            else
                echo '';
        }
        echo "</td>";


}
//----------------------------------------------------------------------------------------------------

function show_results()
{
	global $path_to_root, $systypes_array,$db_connections;

	if (!isset($_POST["account"]))
		$_POST["account"] = null;

	//$act_name = $_POST["account"] ? get_gl_account_name($_POST["account"]) : "";
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RS') {
	$act_name = $_POST["account"] ;


	}
	else{

		$act_name = $_POST["account"]? get_gl_account_name($_POST["account"]) : "" ;



	}
	
	$dim = get_company_pref('use_dimension');
    /*Now get the transactions  */
    if (!isset($_POST['Dimension']))
    	$_POST['Dimension'] = 0;
    if (!isset($_POST['Dimension2']))
    	$_POST['Dimension2'] = 0;
	$result = get_gl_transactions($_POST['TransFromDate'], $_POST['TransToDate'], $_POST['trans_no'], 	$_POST["account"], $_POST['Dimension'], $_POST['Dimension2'], null,  	input_num('amount_min'), input_num('amount_max'), null,$_POST['memo'], $_POST['ref'], $_POST['person_id'], $_POST['cheque_no'],$_POST['sub_account']);

	$colspan = ($dim == 2 ? "6" : ($dim == 1 ? "5" : "4"));

	if ($_POST["account"] != null)
		display_heading($_POST["account"]. "&nbsp;&nbsp;&nbsp;".$act_name);

	// Only show balances if an account is specified AND we're not filtering by amounts
	$show_balances = $_POST["account"] != null && 
                     input_num("amount_min") == 0 && 
                     input_num("amount_max") == 0;
		
	start_table(TABLESTYLE);
	
	$first_cols = array(_("Type"), _("#"), _("Date"), _("Reference"), _("Cheque No."));
	
	if ($_POST["account"] == null)
	    $account_col = array(_("Account"));
	else
	    $account_col = array();
	
	if ($dim == 2)
		$dim_cols = array(_("Dimension")." 1", _("Dimension")." 2");
	elseif ($dim == 1)
		$dim_cols = array(_("Dimension"));
	else
		$dim_cols = array();
	
	if ($show_balances)
	    $remaining_cols = array(_("Person/Item"), _("Debit"), _("Credit"), _("Balance"), _("Memo"), _("View"), _("A4"), _("A5"), _(""));
	else
	    $remaining_cols = array(_("Person/Item"), _("Debit"), _("Credit"), _("Memo"), _("View"), _("A4"), _("A5"), _(""));
	    
	$th = array_merge($first_cols, $account_col, $dim_cols, $remaining_cols);
			
	table_header($th);
	if ($_POST["account"] != null && is_account_balancesheet($_POST["account"]))
		$begin = "";
	else
	{
		$begin = get_fiscalyear_begin_for_date($_POST['TransFromDate']);
		if (date1_greater_date2($begin, $_POST['TransFromDate']))
			$begin = $_POST['TransFromDate'];
		$begin = add_days($begin, -1);
	}

	$bfw = 0;
	if ($show_balances) {
	    $bfw = get_gl_balance_from_to($begin, $_POST['TransFromDate'], $_POST["account"], $_POST['Dimension'], $_POST['Dimension2']);
    	start_row("class='inquirybg'");
    	label_cell("<b>"._("Opening Balance")." - ".$_POST['TransFromDate']."</b>", "colspan=$colspan");
    		label_cell("");
    	label_cell("");
    	label_cell("");
    	label_cell("");
    	display_debit_or_credit_cells($bfw, true);
    	label_cell("");
    	label_cell("");
    	end_row();
	}
	
	$running_total = $bfw;
	$j = 1;
    $k = $total_debit = $total_credit = 0; //row colour counter

	while ($myrow = db_fetch($result))
	{

    	alt_table_row_color($k);

    	$running_total += $myrow["amount"];

    	$trandate = sql2date($myrow["tran_date"]);

    	label_cell($systypes_array[$myrow["type"]]);
		label_cell(	get_trans_view_str($myrow["type"], $myrow["type_no"], "", false, '', '', 0));
		
	
		
		
    	label_cell($trandate);
    	label_cell($myrow["reference"]);
if($myrow["cheque"]==0){

			label_cell('');

		}else{

			label_cell($myrow["cheque"]);
		}
    // 	if ($_POST["account"] == null)
    // 	    label_cell($myrow["account"] . ' ' . get_gl_account_name($myrow["account"]));
    
    
    
    if ($_POST["account"] == null && $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RS')
    	    label_cell($myrow["account"].''.get_gl_account_name($myrow["account"]) );

		 elseif($_POST["account"] == null ){

			 label_cell($myrow["account"] . ' ' . get_gl_account_name($myrow["account"]));


		 }

    
    
    
    	
		if ($dim >= 1)
			label_cell(get_dimension_string($myrow['dimension_id'], true));
		if ($dim > 1)
			label_cell(get_dimension_string($myrow['dimension2_id'], true));
			
        // label_cell(payment_person_name($myrow["person_type_id"],$myrow["person_id"]));
        label_cell(get_counterparty_name($myrow['type'], $myrow['type_no']));
        
		display_debit_or_credit_cells($myrow["amount"]);
			if($myrow["amount"] > 0)
			$total_debit += $myrow["amount"];
		elseif($myrow["amount"] < 0)
			$total_credit += $myrow["amount"];
		if ($show_balances)
		    amount_cell($running_total);
if ($myrow['type'] == 22 || $myrow['type'] == 12 || $myrow['type'] == 20|| $myrow['type'] == 4){
			$myrow['memo1'] = get_comments_string($myrow['type'], $myrow['type_no']);
				label_cell($myrow['memo1']);
}else{
    global  $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='3D') {
        if ($myrow['memo1'] == 'Overhead Cost')
            $myrow['memo1'] = get_comments_string($myrow['type'], $myrow['type_no']);
    }
    
    
    	label_cell($myrow['memo1']);
    
}
    
		gl_link_in_account_inquiry($myrow); // for showing GL View
		prt_link_in_account_inquiry($myrow);
		prt_link2_in_account_inquiry($myrow);
		edit_link_in_account_inquiry($myrow);// for showing edit voucher

    	end_row();

    	$j++;
    	if ($j == 12)
    	{
    		$j = 1;
    		table_header($th);
    	}
	}
	//end of while loop

	if ($show_balances) {
    	start_row("class='inquirybg'");
    	label_cell("<b>" . _("Ending Balance") ." - ".$_POST['TransToDate']. "</b>", "colspan=$colspan");
    		label_cell("");
    	label_cell("");	
    
    	amount_cell($total_debit);
		amount_cell($total_credit);
    	amount_cell($running_total);
    	label_cell("");
    	label_cell("");
    	end_row();
	}

	end_table(2);
	if (db_num_rows($result) == 0)
		display_note(_("No general ledger transactions have been created for the specified criteria."), 0, 1);

}

//----------------------------------------------------------------------------------------------------

gl_inquiry_controls();

div_start('trans_tbl');

if (get_post('Show') || get_post('account'))
    show_results();

div_end();

//----------------------------------------------------------------------------------------------------

end_page();

