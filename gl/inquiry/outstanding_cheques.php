<?php

$page_security = 'SA_BANKTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
//page(_($help_context = "Bank Statement"), isset($_GET['bank_account']), false, "", $js);
page(_($help_context = "Bank Statement"), false, false, '', $js);

check_db_has_bank_accounts(_("There are no bank accounts defined in the system."));

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show'))
{
	$Ajax->activate('trans_tbl');
}
//------------------------------------------------------------------------------------------------

if (isset($_GET['bank_account']))
	$_POST['bank_account'] = $_GET['bank_account'];

start_form();
start_table(TABLESTYLE_NOBORDER);
start_row();
//bank_accounts_list_all_cells(_("Account:"), 'bank_account', null);

date_cells(_("From:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'TransToDate');
ref_cells(_("Cheque #"), 'cheque_no');

submit_cells('Show',_("Show"),'','', 'default');
end_row();
end_table();
end_form();

//------------------------------------------------------------------------------------------------

if (!isset($_POST['bank_account']))
	$_POST['bank_account'] = "";

$result = get_bank_trans_for_oustanding_cheques(/*$_POST['bank_account'],*/ $_POST['TransAfterDate'], $_POST['TransToDate'], $_POST['cheque_no']);

div_start('trans_tbl');
$act = get_bank_account($_POST["bank_account"]);
//display_heading($act['bank_account_name']." - ".$act['bank_curr_code']);

start_table(TABLESTYLE);

$th = array(_("Type"), _("#"), _("Reference"), _("Cheque No."), _("Date"),
	_("Debit"), _("Credit"), _("Balance"), _("Person/Item"), _("Memo"));
table_header($th);

$bfw = get_balance_before_for_bank_account($_POST['bank_account'], $_POST['TransAfterDate']);

$credit = $debit = 0;
start_row("class='inquirybg' style='font-weight:bold'");
label_cell(_("Opening Balance")." - ".$_POST['TransAfterDate'], "colspan=4");
label_cell("");
label_cell("");
label_cell("");

display_debit_or_credit_cells($bfw);
label_cell("");
//label_cell("", "colspan=4");

end_row();
$running_total = $bfw;
if ($bfw > 0 ) 
	$debit += $bfw;
else 
	$credit += $bfw;
$j = 1;
$k = 0; //row colour counter
function prt_link($row)
{
	return print_document_link($row['trans_no'], _("Print"), true, $row['type'], ICON_PRINT);

}

function dispatch_link($row)
{
    if ($row["type"] == ST_BANKPAYMENT || $row["type"] == ST_BANKDEPOSIT ||
        $row["type"] == ST_CUSTPAYMENT || $row["type"] == ST_SUPPAYMENT) {
        echo "<td>";
        echo pager_link(_("Dispatch"),
            "/gl/bank_transfer.php?&&" . "trans_no1=" . $row['trans_no'] . "&&trans_type=" . $row['type'], ICON_DOC);
        echo "</td>";
    }
    else
    {
        echo "<td>";
        echo "";
        echo "</td>";
    }
}

function dispatch_link2($row)
{
    if ($row["type"] == ST_BANKPAYMENT || $row["type"] == ST_SUPPAYMENT)
    {
        echo "<td>";
        echo   pager_link( _("Dispatch"),
            "/gl/gl_bank.php?NewDeposit=Yes&&"."trans_no1=".$row['trans_no']."&&trans_type=".$row['type'], ICON_DOC);
        echo "</td>";
    }
    elseif($row["type"] == ST_BANKDEPOSIT ||  $row["type"] == ST_CUSTPAYMENT)
    {
        echo "<td>";
        echo  pager_link( _("Dispatch"),
            "/gl/gl_bank.php?NewPayment=Yes&&"."trans_no1=".$row['trans_no']."&&trans_type=".$row['type'], ICON_DOC);
        echo "</td>";
    }
    else
    {
        echo "<td>";
        echo "";
        echo "</td>";
    }
}

$debit=0;
while ($myrow = db_fetch($result))
{
//    display_error(((get_total_cheque($myrow["trans_no"],$myrow["type"]))/2));
    $cheque_ava=get_total_cheque_ava($myrow["cheque"],$myrow["parent_id"]);
   // display_error($cheque_ava);
    if(($cheque_ava ==1 || $cheque_ava ==2 || $cheque_ava ==3 )) continue ;

    {
        alt_table_row_color($k);
        $running_total += $myrow["amount"];
        $trandate = sql2date($myrow["trans_date"]);
        label_cell($systypes_array[$myrow["type"]]);
        label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"]));
        label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"], $myrow['ref']));
        if ($myrow["cheque"] == 0) {
            label_cell('');

        } else {

            label_cell($myrow["cheque"]);
        }
        // display_error(get_total_cheque($myrow["trans_no"], $myrow["type"]));

        label_cell($trandate);
        display_debit_or_credit_cells($myrow["amount"]);
        amount_cell($running_total);

        label_cell(payment_person_name($myrow["person_type_id"], $myrow["person_id"]));

        label_cell(get_comments_string($myrow["type"], $myrow["trans_no"]));
//    dispatch_link($myrow);// for showing dispatch
//    dispatch_link2($myrow);// for showing vouchers
//	label_cell(get_gl_view_str($myrow["type"], $myrow["trans_no"]));
//    label_cell(prt_link($myrow));
//	label_cell(trans_editor_link($myrow["type"], $myrow["trans_no"]));

        end_row();
        if ($myrow["amount"] > 0)
            $debit += $myrow["amount"];
        else
            $credit += $myrow["amount"];

        if ($j == 12) {
            $j = 1;
            table_header($th);
        }
        $j++;
    }
}
//end of while loop

start_row("class='inquirybg' style='font-weight:bold'");
label_cell(_("Ending Balance")." - ". $_POST['TransToDate'], "colspan=4");
label_cell(_(""));
amount_cell($debit);
amount_cell(-$credit);


//display_debit_or_credit_cells($running_total);
amount_cell($running_total);
label_cell("", "colspan=4");
end_row();
end_table(2);
div_end();
//------------------------------------------------------------------------------------------------

end_page();

