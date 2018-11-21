<?php

$page_security = 'SA_ITEMSSTATVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

$js = "";
if ($SysPrefs->use_popup_windows && $SysPrefs->use_popup_search)
	$js .= get_js_open_window(900, 500);

if (isset($_GET['stock_id']))
	$_POST['stock_id'] = $_GET['stock_id'];

page(_($help_context = "Inventory Item Status"), isset($_GET['stock_id']), false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/includes/db/manufacturing_db.inc");

if (list_updated('stock_id')) 
	$Ajax->activate('status_tbl');
//----------------------------------------------------------------------------------------------------

check_db_has_stock_items(_("There are no items defined in the system."));

start_form();
global $Ajax;
if (!isset($_POST['stock_id']))
	$_POST['stock_id'] = get_global_stock_item();

if (!$page_nested)
{
	echo "<center> " . _("Item:"). " ";
	echo stock_costable_items_list('stock_id', $_POST['stock_id'], false, true);
}
if(list_updated('stock_id')){
	$Ajax->activate('batch');
}
$pref=get_company_prefs();
if($pref['batch'] == 1)
batch_list_cells(_("Batch"), $_POST['stock_id'], 'batch', null, true, false, true, true);

echo "<br>";

echo "<hr></center>";

set_global_stock_item($_POST['stock_id']);

$mb_flag = get_mb_flag($_POST['stock_id']);
$kitset_or_service = false;

div_start('status_tbl');
if (is_service($mb_flag))
{
	display_note(_("This is a service and cannot have a stock holding, only the total quantity on outstanding sales orders is shown."), 0, 1);
	$kitset_or_service = true;
}

$loc_details = get_loc_details($_POST['stock_id']);

start_table(TABLESTYLE);

if ($kitset_or_service == true)
{
	$th = array(_("Location"), _("Demand"));
}
else
{
	$pref = get_company_prefs();

	if($pref['batch']  == 1) {
		if ($pref['alt_uom'] == 1) {

			$th = array(_("Location"), _("Quantity On Hand"), _("Sec.Quantity On Hand"),/* _("Re-Order Level"),*/
				/*_("Sec.Re-Order Level"),*//* _("Demand"), _("Sec.Demand"),*/ /*_("Available"),*/
				/*_("Sec.Available"),*//* _("On Order"), _("Sec.On Order")*/);
		}
		else{

			$th = array(_("Location"), _("Quantity On Hand"),/* _("Re-Order Level"),*/
				/*_("Demand"),*/ /*_("Available"),*/
				/*_("On Order")*/);
		}
	}
	else{

		if ($pref['alt_uom'] == 1) {
			$th = array(_("Location"), _("Quantity On Hand"), _("Sec.Quantity On Hand"), _("Re-Order Level"),
				_("Sec.Re-Order Level"), _("Demand"), _("Sec.Demand"), _("Available"),
				_("Sec.Available"), _("On Order")/*, _("Sec.On Order")*/);
		} else {
			$th = array(_("Location"), _("Quantity On Hand"), _("Re-Order Level"),
				_("Demand"), _("Available"),
				_("On Order"));
		}

	}
}
table_header($th);
$dec = get_qty_dec($_POST['stock_id']);
$j = 1;
$k = 0; //row colour counter

while ($myrow = db_fetch($loc_details))
{
$pref = get_company_pref();
	
	alt_table_row_color($k);

	$demand_qty = get_demand_qty($_POST['stock_id'], $myrow["loc_code"]);
	$demand_qty += get_demand_asm_qty($_POST['stock_id'], $myrow["loc_code"]);

	$demand_sec_qty = get_demand_sec_qty($_POST['stock_id'], $myrow["loc_code"]);
	$demand_sec_qty += get_demand_asm_sec_qty($_POST['stock_id'], $myrow["loc_code"]);

	$qoh = get_qoh_on_date($_POST['stock_id'], $myrow["loc_code"],0,$_POST['batch']);
	$qoh1 = get_pry_qoh_on_date($_POST['stock_id'], $myrow["loc_code"],0,$_POST['batch']);

	if ($kitset_or_service == false)
	{
		$qoo = get_on_porder_qty($_POST['stock_id'], $myrow["loc_code"]);
//		$sec_qoo = get_on_porder_sec_qty($_POST['stock_id'], $myrow["loc_code"]);
		$qoo += get_on_worder_qty($_POST['stock_id'], $myrow["loc_code"]);
		label_cell($myrow["location_name"]);
		$pref = get_company_prefs();

		if($pref['batch']  == 1) {
			qty_cell($qoh, false, $dec);
			if($pref['alt_uom'] == 1)
				qty_cell($qoh1, false, $dec);
		}
		else {

			qty_cell($qoh, false, $dec);
			if ($pref['alt_uom'] == 1)
				qty_cell($qoh1, false, $dec);


			qty_cell($myrow["reorder_level"], false, $dec);
			if ($pref['alt_uom'] == 1)
				qty_cell($myrow["reorder_level"], false, $dec);


			qty_cell($demand_qty, false, $dec);
			if ($pref['alt_uom'] == 1)
				qty_cell($demand_sec_qty, false, $dec);


			qty_cell($qoh - $demand_qty, false, $dec);
			if ($pref['alt_uom'] == 1)
				qty_cell($qoh1 - $demand_sec_qty, false, $dec);


			qty_cell($qoo, false, $dec);
//		qty_cell($sec_qoo, false, $dec);
		}
        end_row();

	}
	else
	{
	/* It must be a service or kitset part */
		label_cell($myrow["location_name"]);
		qty_cell($demand_qty, false, $dec);
		end_row();

	}
	$j++;
	If ($j == 12)
	{
		$j = 1;
		table_header($th);
	}
}

end_table();
div_end();
end_form();
end_page();

