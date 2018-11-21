<?php

$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");

page(_($help_context = "View Inventory Adjustment"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}

display_heading($systypes_array[ST_INVADJUST] . " #$trans_no");

br(1);
$adjustment_items = get_stock_adjustment_items($trans_no);
$k = 0;
$header_shown = false;
while ($adjustment = db_fetch($adjustment_items))
{

	if (!$header_shown)
	{

		start_table(TABLESTYLE2, "width='90%'");
		start_row();
		label_cells(_("At Location"), $adjustment['location_name'], "class='tableheader2'");
    	label_cells(_("Reference"), $adjustment['reference'], "class='tableheader2'", "colspan=6");
		label_cells(_("Date"), sql2date($adjustment['tran_date']), "class='tableheader2'");
		end_row();
		comments_display_row(ST_INVADJUST, $trans_no);

		end_table();
		$header_shown = true;

		echo "<br>";
		start_table(TABLESTYLE, "width='90%'");
		$myrow_1 = get_company_inventory_pref_from_position(1);
		$myrow_2 = get_company_inventory_pref_from_position(2);
		$myrow_3 = get_company_inventory_pref_from_position(3);
		$myrow_4 = get_company_inventory_pref_from_position(4);
		$myrow_5 = get_company_inventory_pref_from_position(5);
		$myrow_6 = get_company_inventory_pref_from_position(6);
		$myrow_7 = get_company_inventory_pref_from_position(7);
		$myrow_8 = get_company_inventory_pref_from_position(8);
		$myrow_9 = get_company_inventory_pref_from_position(9);
		$myrow_10 = get_company_inventory_pref_from_position(10);
		$myrow_11 = get_company_inventory_pref_from_position(11);
		$myrow_12 = get_company_inventory_pref_from_position(12);
		$myrow_13 = get_company_inventory_pref_from_position(13);
		$myrow_14 = get_company_inventory_pref_from_position(14);
		$myrow_15 = get_company_inventory_pref_from_position(15);
		$myrow_16 = get_company_inventory_pref_from_position(16);
		$myrow_17 = get_company_inventory_pref_from_position(17);
		$myrow_18 = get_company_inventory_pref_from_position(18);
		$myrow_19 = get_company_inventory_pref_from_position(19);
		$myrow_20 = get_company_inventory_pref_from_position(20);
		$myrow_21 = get_company_inventory_pref_from_position(21);
        $myrow_22 = get_company_item_pref_from_name(con_factor);
		$th = array(_("Item Code"), _("Item Description"));
		//Text Boxes Headings

		if($myrow_1['inventory_enable']) {
			array_append($th, array($myrow_1['label_value']._("")) );
		}
		if($myrow_2['inventory_enable']) {
			array_append($th, array($myrow_2['label_value']._("")) );
		}
		if($myrow_3['inventory_enable']) {
			array_append($th, array($myrow_3['label_value']._("")) );
		}
		if($myrow_4['inventory_enable']) {
			array_append($th, array($myrow_4['label_value']._("")) );
		}
		if($myrow_5['inventory_enable']) {
			array_append($th, array($myrow_5['label_value']._("")) );
		}
		if($myrow_6['inventory_enable']) {
			array_append($th, array($myrow_6['label_value']._("")) );
		}
		if($myrow_7['inventory_enable']) {
			array_append($th, array($myrow_7['label_value']._("")) );
		}
		if($myrow_8['inventory_enable']) {
			array_append($th, array($myrow_8['label_value']._("")) );
		}
		if($myrow_9['inventory_enable']) {
			array_append($th, array($myrow_9['label_value']._("")) );
		}
		if($myrow_10['inventory_enable']) {
			array_append($th, array($myrow_10['label_value']._("")) );
		}
		if($myrow_11['inventory_enable']) {
			array_append($th, array($myrow_11['label_value']._("")) );
		}
		if($myrow_12['inventory_enable']) {
			array_append($th, array($myrow_12['label_value']._("")) );
		}
		if($myrow_13['inventory_enable']) {
			array_append($th, array($myrow_13['label_value']._("")) );
		}
		if($myrow_14['inventory_enable']) {
			array_append($th, array($myrow_14['label_value']._("")) );
		}
		if($myrow_15['inventory_enable']) {
			array_append($th, array($myrow_15['label_value']._("")) );
		}
		if($myrow_16['inventory_enable']) {
			array_append($th, array($myrow_16['label_value']._("")) );
		}
		if($myrow_17['inventory_enable']) {
			array_append($th, array($myrow_17['label_value']._("")) );
		}
		if($myrow_18['inventory_enable']) {
			array_append($th, array($myrow_18['label_value']._("")) );
		}
		if($myrow_19['inventory_enable']) {
			array_append($th, array($myrow_19['label_value']._("")) );
		}
		if($myrow_20['inventory_enable']) {
			array_append($th, array($myrow_20['label_value']._("")) );
		}
		if($myrow_21['inventory_enable']) {
			array_append($th, array($myrow_21['label_value']._("")) );
		}
		{
			$pref=get_company_prefs();
			if($pref['alt_uom'] == 1 && $myrow_22['inventory_enable']==1) {

				if ($pref['batch'] == 1) {
					array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
						_("Units"),_("Con factor"), _("Unit Cost")));
				} else {
					array_append($th, array(_("Quantity"),
						_("Units"),_("Con factor"), _("Unit Cost")));
				}
			}
			else{
				if ($pref['batch'] == 1) {
					array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
						_("Units"), _("Unit Cost"),  _("Amount")));
				} else {
					array_append($th, array(_("Quantity"),
						_("Units"), _("Unit Cost"),  _("Amount")));
				}
			}
		}

    	table_header($th);
	}

    alt_table_row_color($k);

    label_cell($adjustment['stock_id']);
    label_cell($adjustment['description']);
	//text boxes labels
	if($myrow_1['inventory_enable'])
	{
		label_cell($adjustment[$myrow_1['name']]);
	}
	if($myrow_2['inventory_enable'])
	{
		label_cell($adjustment[$myrow_2['name']]);
	}
	if($myrow_3['inventory_enable'])
	{
		label_cell($adjustment[$myrow_3['name']]);
	}
	if($myrow_4['inventory_enable'])
	{
		label_cell($adjustment[$myrow_4['name']]);
	}
	if($myrow_5['inventory_enable'])
	{
		label_cell($adjustment[$myrow_5['name']]);
	}
	if($myrow_6['inventory_enable'])
	{
		label_cell($adjustment[$myrow_6['name']]);
	}
	if($myrow_7['inventory_enable'])
	{
		label_cell($adjustment[$myrow_7['name']]);
	}
	if($myrow_8['inventory_enable'])
	{
		label_cell($adjustment[$myrow_8['name']]);
	}
	if($myrow_9['inventory_enable'])
	{
		label_cell($adjustment[$myrow_9['name']]);
	}
	if($myrow_10['inventory_enable'])
	{
		label_cell($adjustment[$myrow_10['name']]);
	}
	if($myrow_11['inventory_enable'])
	{
		label_cell($adjustment[$myrow_11['name']]);
	}
	if($myrow_12['inventory_enable'])
	{
		label_cell($adjustment[$myrow_12['name']]);
	}

	///combo inputs
	if($myrow_13['inventory_enable'])
	{
		label_cell($adjustment[$myrow_13['name']]);
	}
	if($myrow_14['inventory_enable'])
	{
		label_cell($adjustment[$myrow_14['name']]);
	}
	if($myrow_15['inventory_enable'])
	{
		label_cell($adjustment[$myrow_15['name']]);
	}
	if($myrow_16['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_16['name']],$myrow_16['name']));
	}
	if($myrow_17['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_17['name']],$myrow_17['name']));
	}
	if($myrow_18['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_18['name']],$myrow_18['name']));
	}
	if($myrow_19['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_19['name']],$myrow_19['name']));
	}
	if($myrow_20['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_20['name']],$myrow_20['name']));
	}
	if($myrow_21['inventory_enable'])
	{
		label_cell(get_combo_name($adjustment[$myrow_21['name']],$myrow_21['name']));
	}
	$batch=get_batch_by_id($adjustment['batch']);
	$pref=get_company_prefs();
		$item=get_item($adjustment['stock_id']);
    $myrow_22 = get_company_item_pref_from_name(con_factor);
    
    	if($pref['alt_uom'] == 1 && $item['units'] != $adjustment['units_id'])
    	{
    	   
        $qty =	$adjustment['qty'] * $adjustment['con_factor'];
    	}
    else{
        
        $qty = $adjustment['qty'];
    }
    
    qty_cell($qty, false, get_qty_dec($adjustment['stock_id']));
	if($pref['batch'] == 1) {
		label_cell($batch['name']);
		label_cell($batch['exp_date']);
	}
	if($pref['alt_uom'] == 1 ) {
		label_cell($adjustment['units_id']);
		if( $myrow_22['inventory_enable']==1)
		label_cell($adjustment['con_factor']);
	}
	else {
		label_cell($adjustment['units']);
	}
    amount_decimal_cell(round($adjustment['standard_cost'], $dec));
    amount_decimal_cell(round($adjustment['qty']*$adjustment['standard_cost'], $dec));
    $qty_total += $adjustment['qty'];
    $amount_total += $adjustment['qty']*$adjustment['standard_cost'];
    
    end_row();
}
label_cell(Total);
label_cell();
amount_decimal_cell($qty_total);
label_cell();
label_cell();
amount_decimal_cell(round($amount_total, $dec));

end_table(1);

is_voided_display(ST_INVADJUST, $trans_no, _("This adjustment has been voided."));

end_page(true, false, false, ST_INVADJUST, $trans_no);
