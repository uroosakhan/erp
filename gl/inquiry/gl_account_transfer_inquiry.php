<?php

$page_security = 'SA_GLTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");


include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

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
    gl_all_accounts_list_cells(_("Account From:"), 'account', null, false, false, "All Account");
	gl_all_accounts_list_cells(_("Account To:"), 'account_to', null, false, false, "All Account");
	date_cells(_("from:"), 'TransFromDate', '', null, -user_transaction_days());
	date_cells(_("to:"), 'TransToDate');
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
	submit_cells('Show',_("Show"),'','', 'default');
	end_row();
	end_table();
	submit_center_first('BalanceTransfer', "Balance Transfer",
		_('Transfer balance another account'), 'default');
	echo '<hr>';
    end_form();

}

//----------------------------------------------------------------------------------------------------
/*	Transfer Process */
if (isset($_POST['BalanceTransfer']))
{
	if (strlen($_POST["account"]) != 0)
	{
		if (strlen($_POST["account_to"]) != 0) {
			$Counter1 = current($_SESSION['TotalCounter']);
			$Counter2 = end($_SESSION['TotalCounter']);
			if ($Counter1 && $Counter2 != 0) {
				for ($i = $Counter1; $i <= $Counter2; $i++) {
					if ($_POST['select_' . $i] == 1) {
						$sql = "UPDATE " . TB_PREF . "gl_trans 
						SET account = " . db_escape($_POST['account_to']) . "  
						WHERE counter=" . db_escape($i);
						db_query($sql, 'Concurrent editing conflict while sales order update');

						$Ajax->activate('trans_tbl');

						display_notification("&apos;".$_POST["account"]."-".get_gl_account_name($_POST["account"])."&apos; balances transferred to &apos;".$_POST["account_to"]."-".get_gl_account_name($_POST["account_to"])."&apos;");
					}
				}
			} else
				display_error("Please select at least any one account for transfer another account");

		} else
			display_error("Please select account to.");
	}
	else
		display_error("Please select account.");


}
//--------------Process END----------------------------------------------------------------------------------


function show_results()
{



	global $path_to_root, $systypes_array;

	if (!isset($_POST["account"]))
		$_POST["account"] = null;

	$act_name = $_POST["account"] ? get_gl_account_name($_POST["account"]) : "";
	$dim = get_company_pref('use_dimension');

    /*Now get the transactions  */
    if (!isset($_POST['Dimension']))
    	$_POST['Dimension'] = 0;
    if (!isset($_POST['Dimension2']))
    	$_POST['Dimension2'] = 0;


	$result = get_gl_transactions($_POST['TransFromDate'], $_POST['TransToDate'], -1,
		$_POST["account"], $_POST['Dimension'], $_POST['Dimension2'], null,
		input_num('amount_min'), input_num('amount_max'), null);

/*	
$GetTotalRecords = get_gl_transactions_for_total_records($_POST['TransFromDate'], $_POST['TransToDate'], -1,
    	$_POST["account"], $_POST['Dimension'], $_POST['Dimension2'], null,
    	input_num('amount_min'), input_num('amount_max'), null);
*/
	$colspan = ($dim == 2 ? "6" : ($dim == 1 ? "5" : "4"));

	if ($_POST["account"] != null)
		display_heading($_POST["account"]. "&nbsp;&nbsp;&nbsp;".$act_name);

	// Only show balances if an account is specified AND we're not filtering by amounts
	$show_balances = $_POST["account"] != null && 
                     input_num("amount_min") == 0 && 
                     input_num("amount_max") == 0;
		
	start_table(TABLESTYLE);
	
	$first_cols = array(_("Type"), _("#"), _("Date"));
	
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
	    $remaining_cols = array(_("Person/Item"), _("Debit"), _("Credit"), _("Balance"), _("Memo"), _("Select All").check_box("select_all", 0, "marked_all_checkbox(this)"));
	else
	    $remaining_cols = array(_("Person/Item"), _("Debit"), _("Credit"), _("Memo"), _("Select All").check_box("select_all", 0, "marked_all_checkbox(this)"));
	    
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
    	display_debit_or_credit_cells($bfw, true);
    	label_cell("");
    	label_cell("");
    	end_row();
	}
	
	$running_total = $bfw;
//	$j = 1;
    $k = $total_debit = $total_credit = 0; //row colour counter

//	hidden('TotalRecords', $GetTotalRecords['Counter']);

	while ($myrow = db_fetch($result))
	{
    	alt_table_row_color($k);

    	$running_total += $myrow["amount"];

    	$trandate = sql2date($myrow["tran_date"]);

    	label_cell($systypes_array[$myrow["type"]]);
		label_cell(get_gl_view_str($myrow["type"], $myrow["type_no"], $myrow["type_no"], true));
    	label_cell($trandate);

    	if ($_POST["account"] == null)
    	    label_cell($myrow["account"] . ' ' . get_gl_account_name($myrow["account"]));
    	
		if ($dim >= 1)
			label_cell(get_dimension_string($myrow['dimension_id'], true));
		if ($dim > 1)
			label_cell(get_dimension_string($myrow['dimension2_id'], true));
		label_cell(payment_person_name($myrow["person_type_id"],$myrow["person_id"]));
		display_debit_or_credit_cells($myrow["amount"]);
			if($myrow["amount"] > 0)
			$total_debit += $myrow["amount"];
		elseif($myrow["amount"] < 0)
			$total_credit += $myrow["amount"];
		if ($show_balances)
		    amount_cell($running_total);
		if ($myrow['memo_'] == "")
			$myrow['memo_'] = get_comments_string($myrow['type'], $myrow['type_no']);
    	label_cell($myrow['memo_']);
		echo "<td>" . check_box("select_" . $myrow['counter'], 1, "set_value(this)", true) . "</td>";
		$Counters[] = $myrow['counter'];
		$_SESSION['TotalCounter'] = $Counters;
		

		end_row();

//    	$j++;
//    	if ($j == 12)
//    	{
//    		$j = 1;
//    		table_header($th);
//    	}
	}
	//end of while loop

	if ($show_balances) {
    	start_row("class='inquirybg'");
    	label_cell("<b>" . _("Ending Balance") ." - ".$_POST['TransToDate']. "</b>", "colspan=$colspan");
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



?>
<script>
function marked_all_checkbox(source)
{

var str = source.name;
var res = str.substring(0, 6);


checkboxes = document.getElementsByClassName('all_boxes');
for(var i=0, n=checkboxes.length;i<n;i++) {
checkboxes[i].checked = source.checked;
set_value(checkboxes[i]);
}
}



function set_value(source){
if(source.checked)
source.value = 1;
else
source.value = 0;
}
</script>


