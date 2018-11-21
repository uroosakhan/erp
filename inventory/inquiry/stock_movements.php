<?php

$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

include_once($path_to_root . "/includes/ui.inc");
$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(800, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();

if (isset($_GET['FixedAsset'])) {
	$page_security = 'SA_ASSETSTRANSVIEW';
	$_POST['fixed_asset'] = 1;
	$_SESSION['page_title'] = _($help_context = "Fixed Assets Movement");
} else {
	$_SESSION['page_title'] = _($help_context = "Inventory Item Movement");
}

page($_SESSION['page_title'], isset($_GET['stock_id']), false, "", $js);
//------------------------------------------------------------------------------------------------

if (get_post('fixed_asset') == 1)
	check_db_has_fixed_assets(_("There are no fixed asset defined in the system."));
else
	check_db_has_stock_items(_("There are no items defined in the system."));

if(get_post('ShowMoves'))
{
	$Ajax->activate('doc_tbl');
}

if (list_updated('stock_id'))
{
//	$_POST['stock_id'] = $_GET['stock_id'];
	  $Ajax->activate('batch');
}

start_form();

hidden('fixed_asset');

if (!isset($_POST['stock_id']))
	$_POST['stock_id'] = get_global_stock_item();

start_table(TABLESTYLE_NOBORDER);
start_row();
if (!$page_nested)
{
	if (get_post('fixed_asset') == 1) {
		stock_items_list_cells(_("Item:"), 'stock_id', $_POST['stock_id'],
			false, false, check_value('show_inactive'), false, array('fixed_asset' => true));
		check_cells(_("Show inactive:"), 'show_inactive', null, true);

		if (get_post('_show_inactive_update')) {
			$Ajax->activate('stock_id');
			set_focus('stock_id');
		}
	} else
		stock_costable_items_list_cells(_("Item:"), 'stock_id', $_POST['stock_id'],null,true);
}

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
$pref=get_company_prefs();
if($pref['batch'] == 1)
batch_list_cells(_("Batch"), $_POST['stock_id'], 'batch', null, true, false, true, true, $_POST['StockLocation'],1);
end_row();
start_row();
locations_list_cells(_("From Location:"), 'StockLocation', null, true, false, (get_post('fixed_asset') == 1));

date_cells(_("From:"), 'AfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'BeforeDate');

submit_cells('ShowMoves',_("Show Movements"),'',_('Refresh Inquiry'), 'default');
end_row();
end_table();
end_form();

set_global_stock_item($_POST['stock_id']);

$before_date = date2sql($_POST['BeforeDate']);
$after_date = date2sql($_POST['AfterDate']);
$display_location = !$_POST['StockLocation'];
if (isset($_POST['Gatepass']))
{
    // checking batch integrity
    $del_count = 0;
    foreach($_POST['Sel_'] as $delivery => $branch) {
        $checkbox = 'Sel_'.$delivery;
        if (check_value($checkbox))	{
            if (!$del_count) {
                $del_branch = $branch;
            }

            $selected[] = $delivery;
            $del_count++;
        }
    }

    if (!$del_count) {
        display_error(_('For batch invoicing you should
		    select at least one delivery. All items must be dispatched to
		    the same customer branch.'));
    } else {
        $_SESSION['GatePassBatch'] = $selected;
        meta_forward($path_to_root . '/sales/manage/multiple_gate_pass.php','Gatepass=Yes&Type=16');
    }
}

function gatepass_checkbox($row)
{
    $name = "Sel_" .$row['trans_no'];
    if(get_restrick_gate_pass($row['trans_no']) != $row['trans_no'])
    {
        return
            "<input type='checkbox' name='$name' value='1' >"
            ."<input name='Sel_[".$row['trans_no']."]' type='hidden' value='"
            .$row['type']."'>\n";
    }

}
function get_restrick_gate_pass($gate_pass_no)
{
    $sql = "SELECT delivery_no FROM ".TB_PREF."multiple_gate_pass WHERE delivery_no=$gate_pass_no";

    $result = db_query($sql, "could not get account type");

    $row = db_fetch_row($result);
    return $row[0];
}





if($pref['alt_uom'] == 1) {
    submit_cells('change_uom', _("Secondary Quantity"), "colspan=1 align='center'", _("Refresh"), true);
}

if(isset($_POST['change_uom']))
{
        meta_forward('../inquiry/sec_stock_movements.php?');
}
$result = get_stock_movements($_POST['stock_id'], $_POST['StockLocation'],
	$_POST['BeforeDate'], $_POST['AfterDate'],$_POST['batch']);

div_start('doc_tbl');
start_table(TABLESTYLE);
$th = array(_("Type"), _("#"), _("Reference"));

if ($display_location)
	array_push($th, _("Location"));
$pref = get_company_pref();

	array_push($th, _("Date"), _("Detail"), _("Quantity In"),  _("Quantity Out")
		,  _("Quantity On Hand"),submit('Gatepass',_("Gate Pass"), false, _("")));



table_header($th);

$before_qty = get_qoh_on_date($_POST['stock_id'], $_POST['StockLocation'], add_days($_POST['AfterDate'], -1),$_POST['batch']);

$after_qty = $before_qty;
$after_sec_qty ="";
start_row("class='inquirybg'");


// 	$header_span = $display_location ? 8 : 5;

	$header_span = $display_location ? 8 : 7;

label_cell("<b>"._("Quantity on hand before") . " " . $_POST['AfterDate']."</b>", "align=center colspan=$header_span");
//label_cell("", "colspan=1");
$dec = get_qty_dec($_POST['stock_id']);
qty_cell($before_qty, false, $dec);
end_row();

$j = 1;
$k = 0; //row colour counter

$total_in = 0;
$total_out = 0;

$sec_total_in = 0;
$sec_total_out = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

	$trandate = sql2date($myrow["tran_date"]);

	if (get_post('fixed_asset') == 1 && isset($fa_systypes_array[$myrow["type"]]))
		$type_name = $fa_systypes_array[$myrow["type"]];
	else
		$type_name = $systypes_array[$myrow["type"]];

	if ($myrow["qty"] > 0)
	{
		$quantity_formatted = number_format2($myrow["qty"], $dec);
		$sec_quantity_formatted = number_format2(($myrow["qty"] * $myrow["con_factor"]), $dec);
		$total_in += $myrow["qty"];
		$sec_total_in += $total_in *  $myrow["con_factor"];
	}
	else
	{
		$quantity_formatted = number_format2(-$myrow["qty"], $dec);
		$sec_quantity_formatted = number_format2((-$myrow["qty"] * $myrow["con_factor"]), $dec);
		$total_out += -$myrow["qty"];
		$sec_total_out += -$myrow["qty"] *  $myrow["con_factor"];
	}
	$pref = get_company_pref();
	$after_qty += $myrow["qty"];
	$after_sec_qty += $myrow["qty"] * $myrow["con_factor"];
	label_cell($type_name);
 $company_record = get_company_prefs();
 if($company_record['item_location'] == 1){
     label_cell(get_trans_view_str(117, $myrow["trans_no"]));
 }
 else{
     label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"]));
 }
	

	label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"], $myrow["reference"]));

	if($display_location) {
		label_cell($myrow['loc_code']);
	}
	label_cell($trandate);

	$gl_posting = "";
	label_cell($myrow['name']);
	label_cell((($myrow["qty"] >= 0) ? $quantity_formatted : ""));
//	if($pref['alt_uom'] == 1)
//	label_cell((($myrow["qty"] >= 0) ? $sec_quantity_formatted : ""));



	label_cell((($myrow["qty"] < 0) ? $quantity_formatted : ""));
//	if($pref['alt_uom'] == 1)
//		label_cell((($myrow["qty"] < 0) ? $sec_quantity_formatted : ""));

	qty_cell($after_qty, false, $dec);
	if($myrow["type"]==16){
	       echo "<td>". gatepass_checkbox($myrow);
}
else
	       {
	           label_cell();
	           
	       }

//	if($pref['alt_uom'] == 1)
//		qty_cell($after_sec_qty, false, $dec);
	end_row();

	$j++;
	if ($j == 12)
	{
		$j = 1;
		table_header($th);
	}
}
$pref = get_company_pref();
start_row("class='inquirybg'");
if($pref['alt_uom'] == 1) {
//	if (!$display_location)
// 		label_cell("<b>" . _("Quantity on hand after") . " " . $_POST['BeforeDate'] . "</b>", "align=center colspan=5");
//	else
		label_cell("<b>" . _("Quantity on hand after") . " " . $_POST['BeforeDate'] . "</b>", "align=center colspan=6");

}
else{
    
    if($_POST['StockLocation'])
    {
        label_cell("<b>" . _("Quantity on hand after") . " " . $_POST['BeforeDate'] . "</b>", "align=center colspan=5");
    }
    elseif(!$_POST['StockLocation'])
    {
    label_cell("<b>" . _("Quantity on hand after") . " " . $_POST['BeforeDate'] . "</b>", "align=center colspan=6");
    }
}


//display_error($display_location);
//if (!$display_location)
	


// qty_cell($total_in, false, $dec);
// label_cell("<b>" . _("Quantity on hand after") . " " . $_POST['BeforeDate'] . "</b>", "align=center colspan=6");
label_cell($total_in);

//if($pref['alt_uom'] == 1)
//	qty_cell($sec_total_in, false, $dec);

// qty_cell($total_out, false, $dec);
label_cell($total_out);

//if($pref['alt_uom'] == 1)
//	qty_cell($sec_total_out, false, $dec);

// qty_cell($after_qty, false, $dec);
// label_cell($after_qty);
amount_cell2($after_qty);

//if($pref['alt_uom'] == 1)
//	qty_cell($after_sec_qty, false, $dec);
end_row();

end_table(1);
div_end();
end_page();

