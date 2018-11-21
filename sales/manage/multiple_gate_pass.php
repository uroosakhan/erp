<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Gate Pass"));

include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/data_checks.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
page(_($help_context = "Gate Pass"), false, false, "", $js);
//include_once($path_to_root . "/sales/includes/sales_ui.inc");
simple_page_mode(true);

$trans_no = get_next_trans_no(ST_JOURNAL);



function get_gatepass_deliveryno($gate_pass_no)
{
    $sql = "SELECT GROUP_CONCAT(delivery_no SEPARATOR ', ')  FROM ".TB_PREF."multiple_gate_pass 
    WHERE gate_pass_no =" .db_escape($gate_pass_no);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}
function get_gl_typeno($gate_pass_no)
{
    $sql = "SELECT type_no FROM ".TB_PREF."gl_trans 
    WHERE text_1 =" .db_escape($gate_pass_no);
    $result = db_query($sql, 'error');
    $row = db_fetch_row($result);
    return $row[0];
}
if($_GET['Added'])
{

    $id=$_GET['Added'];
    $sql = "SELECT gate_pass_no  FROM 0_multiple_gate_pass WHERE id=$id";
    $query = db_query($sql, "Error");
    $fetch = db_fetch_row($query);
    $order_no = $fetch[0];
    display_note(get_gl_view_str(ST_JOURNAL, get_gl_typeno($order_no), _("&View this Journal Entry")));
    submenu_print(_("&Print Gate Pass 1"), 119, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 2"), 1190, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 3"), 1191, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 4"), 1194, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 5"), 1194, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 6"), 1194, $order_no, 'prtopt');
    display_footer_exit();
}
if($_GET['Update'])
{

    $order_no=$_GET['Update'];
//    $sql = "SELECT gate_pass_no  FROM 0_multiple_gate_pass WHERE id=$id";
//    $query = db_query($sql, "Error");
//    $fetch = db_fetch_row($query);
//    $order_no = $fetch[0];
    display_note(get_gl_view_str(ST_JOURNAL, get_gl_typeno($order_no), _("&View this Journal Entry")));
    submenu_print(_("&Print Gate Pass 1"), 119, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 2"), 1190, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 3"), 1191, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 4"), 1194, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 5"), 1194, $order_no, 'prtopt');
    submenu_print(_("&Print Gate Pass 6"), 1194, $order_no, 'prtopt');
    display_footer_exit();
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

    $input_error = 0;

    if ($input_error != 1)
    {
        if($_POST['gate_pass_no1'] != -1)
        {
            $type_no = get_gl_typeno($_POST['gate_pass_no']);
            update_multiple_gate_pass($_POST['gate_pass_no'], $_POST['gate_pass_amount'], $_POST['gate_pass_debit'],
                $_POST['gate_pass_credit'], $_POST['driver_name'], $_POST['vehicle_no'], $_POST['gate_pass_date']);
            update_gl_trans_debit($_POST['gate_pass_no'],input_num('gate_pass_amount'),$_POST['gate_pass_debit'], $_POST['gate_pass_date']);
            update_gl_trans_crebit($_POST['gate_pass_no'],-input_num('gate_pass_amount'),$_POST['gate_pass_credit'], $_POST['gate_pass_date']);
            update_journal_data($type_no,input_num('gate_pass_amount'), $_POST['gate_pass_date']);
            meta_forward($path_to_root . '/sales/manage/multiple_gate_pass.php', 'Update=' . $_POST['gate_pass_no']);

        }
        else
        {
            $gatepass_id = get_multiple_gatepass_id();
            foreach ($_SESSION['GatePassBatch'] as $GatePassBatch) {
                $order_no = add_multiple_gate_pass($GatePassBatch, $_POST['gate_pass_no'], $_POST['driver_name'], $_POST['vehicle_no'], sql2date($_POST['gate_pass_date']), $_POST['type'], $_POST['gate_pass_amount'], $_POST['gate_pass_debit'], $_POST['gate_pass_credit']);
            }
            $note = _('New Gate Pass has been added');
            $id = get_next_trans_no(ST_JOURNAL);
            $ref = $Refs->get_next(ST_JOURNAL, null, $_POST['gate_pass_date']);
            add_gl_trans($type, $id, ($_POST['gate_pass_date']), $_POST['gate_pass_debit'], 0, 0, "",
                input_num('gate_pass_amount'),
                0,
                0, 0, 0, 0,
                0, 0, 0, $_POST['gate_pass_no'], 0, 0);

            add_gl_trans($type, $id, ($_POST['gate_pass_date']),
                $_POST['gate_pass_credit'], 0, 0, "", -input_num('gate_pass_amount'), 0, 0, 0, 0, "",
                0, 0, 0,
                $_POST['gate_pass_no'], 0, 0);
            add_journal(ST_JOURNAL, $id, input_num('gate_pass_amount'), $_POST['gate_pass_date'], get_company_currency(), $ref, '', 1, $_POST['gate_pass_credit'], $_POST['gate_pass_amount']);

            add_audit_trail(ST_JOURNAL, $id, $_POST['gate_pass_date']);
            add_comments(ST_JOURNAL, $id, $_POST['gate_pass_date'], $memo);
            $Refs->save(ST_JOURNAL, $id, $ref);


            unset($_SESSION['GatePassBatch']);
            meta_forward($path_to_root . '/sales/manage/multiple_gate_pass.php', 'Added=' . $order_no);
        }
        display_notification($note);
        $Mode = 'RESET';
    }
}

if ($Mode == 'Delete')
{

    $cancel_delete = 0;

    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

    if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
    {
        $cancel_delete = 1;
        display_error(_("Cannot delete this area because customer branches have been created using this area."));
    }
    if ($cancel_delete == 0)
    {
        delete_multiple_gate_pass($selected_id);

        display_notification(_('Selected sales area has been deleted'));
    } //end if Delete area
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}
//function gate_pass_dimension1($dimension_id)
//{
//  $sql = "SELECT COUNT(*) as TOTAL FROM 0_gate_pass1 WHERE dimension_id =". db_escape($dimension_id)."";
//
//	$result = db_query($sql, "Cannot retreive a debtor transaction");
//
//	return db_fetch($result);
//}
//$myrow=gate_pass_dimension1($_GET['dimension_id']);
if($myrow['TOTAL'] != 0)
{
    display_error("Duplicate entry against this Dimension");
    display_footer_exit();
}

//-------------------------------------------------------------------------------------------------

//$result = get_sales_areas(check_value('show_inactive'));

start_form();
if(isset($_GET['gate_pass_no']))
    $selected_id = $_GET['gate_pass_no'];

start_table(TABLESTYLE, "width='30%'");

//$th = array(_("Area Name"), "", "");
inactive_control_column($th);

//table_header($th);
$k = 0;

//while ($myrow = db_fetch($result))
//{
//
////	alt_table_row_color($k);
//
////	label_cell($myrow["description"]);
//
////	inactive_control_cell($myrow["area_code"], $myrow["inactive"], 'areas', 'area_code');
//
//// 	edit_button_cell("Edit".$myrow["area_code"], _("Edit"));
//// 	delete_button_cell("Delete".$myrow["area_code"], _("Delete"));
//	end_row();
//}
//
//inactive_control_row($th);
//end_table();
//echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
// 	if ($Mode == 'Edit')
    {
        //editing an existing area
        $myrow = get_gate_pass_data($selected_id);
//

        $gate_pass_no = $myrow['gate_pass_no'];
        $_POST['driver_name']  = $myrow['driver_name'];
        $_POST['vehicle_no']  = $myrow['vehicle_no'];
        $_POST['gate_pass_amount']  = $myrow['gate_pass_amount'];
        $_POST['gate_pass_date']  = sql2date($myrow['gate_pass_date']);
        $_POST['gate_pass_debit']  = $myrow['gate_pass_debit'];
        $_POST['gate_pass_credit']  = $myrow['gate_pass_credit'];
    }
//	hidden("selected_id", $selected_id);
}

hidden('trans_no',$_GET['trans_no']);
hidden('gate_pass_no1',$selected_id);

hidden('type',$_GET['Type']);

table_section_title(_("Multiple Gate Pass"));
$Delivery = '';
foreach ($_SESSION['GatePassBatch'] as $GatePassBatch)
{
    if($Delivery != '') $Delivery .= ',';
    $Delivery .= $GatePassBatch;
}
$gatepass_deliveryno = get_gatepass_deliveryno($_GET['gate_pass_no']);

if($selected_id == -1)
{
    label_cells(_("Delivery No: "), $Delivery);
    $sql = "SELECT gate_pass_no  FROM 0_multiple_gate_pass ORDER BY id DESC";
    $query = db_query($sql, "Error");
    $fetch = db_fetch_row($query);
    $increment = $fetch[0] + 1;

    label_row(_("Gate Pass# "), $increment);
    hidden('gate_pass_no', $increment);

}
else
{
    label_cells(_("Delivery No: "), $gatepass_deliveryno);

    label_row(_("Gate Pass# "), $_GET['gate_pass_no']);
    hidden('gate_pass_no', $_GET['gate_pass_no']);
}

text_row_ex(_("Driver name"), 'driver_name', 30);
text_row_ex(_("vehicle No"), 'vehicle_no', 30);
amount_row(_("Amount :"), 'gate_pass_amount', null, " ");
date_row(_("Gate Pass Date"), 'gate_pass_date', '');

echo gl_all_gate_pass_accounts_list_row(_("Gate Pass Debit"),'gate_pass_debit', null, $skip_bank, true, _('[Select account]'), true, false);
echo gl_all_gate_pass_accounts_list_row(_("Gate Pass Credit"),'gate_pass_credit', null, $skip_bank, true, _('[Select account]'), true, false);



end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
