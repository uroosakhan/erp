<?php

$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/purchasing/includes/po_class.inc");

include($path_to_root . "/includes/session.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "View Purchase Order Delivery"), true, false, "", $js);

include($path_to_root . "/purchasing/includes/purchasing_ui.inc");

if (!isset($_GET['trans_no']))
{
	die ("<BR>" . _("This page must be called with a Purchase Order Delivery number to review."));
}

$purchase_order = new purch_order;
read_grn($_GET["trans_no"], $purchase_order);

display_heading(_("Purchase Order Delivery") . " #" . $_GET['trans_no']);
echo "<BR>";
display_grn_summary($purchase_order);

display_heading2(_("Line Details"));

start_table(TABLESTYLE, "width='90%'");
$myrow_1 = get_company_item_pref_from_position(1);
$myrow_2 = get_company_item_pref_from_position(2);
$myrow_3 = get_company_item_pref_from_position(3);
$myrow_4 = get_company_item_pref_from_position(4);
$myrow_5 = get_company_item_pref_from_position(5);
$myrow_6 = get_company_item_pref_from_position(6);
$myrow_7 = get_company_item_pref_from_position(7);
$myrow_8 = get_company_item_pref_from_position(8);
$myrow_9 = get_company_item_pref_from_position(9);
$myrow_10 = get_company_item_pref_from_position(10);
$myrow_11 = get_company_item_pref_from_position(11);
$myrow_12 = get_company_item_pref_from_position(12);
$myrow_13 = get_company_item_pref_from_position(13);
$myrow_14 = get_company_item_pref_from_position(14);
$myrow_15 = get_company_item_pref_from_position(15);
$myrow_16 = get_company_item_pref_from_position(16);
$myrow_17 = get_company_item_pref_from_position(17);
$myrow_18 = get_company_item_pref_from_position(18);
$myrow_19 = get_company_item_pref_from_position(19);
$myrow_20 = get_company_item_pref_from_position(20);
$myrow_21 = get_company_item_pref_from_position(21);

$myrow_22 = get_company_item_pref_from_position(22);


$pref=get_company_prefs();

	$th = array(_("Item Code"), _("Item Description"));
if($myrow_1['purchase_enable']) {
	array_append($th, array($myrow_1['label_value']._("")) );
}
if($myrow_2['purchase_enable']) {
	array_append($th, array($myrow_2['label_value']._("")) );
}
if($myrow_3['purchase_enable']) {
	array_append($th, array($myrow_3['label_value']._("")) );
}
if($myrow_4['purchase_enable']) {
	array_append($th, array($myrow_4['label_value']._("")) );
}
if($myrow_5['purchase_enable']) {
	array_append($th, array($myrow_5['label_value']._("")) );
}
if($myrow_6['purchase_enable']) {
	array_append($th, array($myrow_6['label_value']._("")) );
}
if($myrow_7['purchase_enable']) {
	array_append($th, array($myrow_7['label_value']._("")) );
}
if($myrow_8['purchase_enable']) {
	array_append($th, array($myrow_8['label_value']._("")) );
}
if($myrow_9['purchase_enable']) {
	array_append($th, array($myrow_9['label_value']._("")) );
}
if($myrow_10['purchase_enable']) {
	array_append($th, array($myrow_10['label_value']._("")) );
}
if($myrow_11['purchase_enable']) {
	array_append($th, array($myrow_11['label_value']._("")) );
}
if($myrow_12['purchase_enable']) {
	array_append($th, array($myrow_12['label_value']._("")) );
}
if($myrow_13['purchase_enable']) {
	array_append($th, array($myrow_13['label_value']._("")) );
}
if($myrow_14['purchase_enable']) {
	array_append($th, array($myrow_14['label_value']._("")) );
}
if($myrow_15['purchase_enable']) {
	array_append($th, array($myrow_15['label_value']._("")) );
}
if($myrow_16['purchase_enable']) {
	array_append($th, array($myrow_16['label_value']._("")) );
}
if($myrow_17['purchase_enable']) {
	array_append($th, array($myrow_17['label_value']._("")) );
}
if($myrow_18['purchase_enable']) {
	array_append($th, array($myrow_18['label_value']._("")) );
}
if($myrow_19['purchase_enable']) {
	array_append($th, array($myrow_19['label_value']._("")) );
}
if($myrow_20['purchase_enable']) {
	array_append($th, array($myrow_20['label_value']._("")) );
}
if($myrow_21['purchase_enable']) {
	array_append($th, array($myrow_21['label_value']._("")) );
}

if($myrow_22['purchase_enable']) {
	array_append($th, array($myrow_22['label_value']._("")) );
}

{
    if($pref['disc_in_amount'] == 1)
        $disc = "Discount ";
    else
        $disc = "Discount %";

    $con_factor = get_company_item_pref('con_factor');
	if($pref['alt_uom'] == 1 && $con_factor['purchase_enable'] == 1) {
		if ($pref['batch'] == 1)
			array_append($th, array(_("Batch"), _("Exp.Date"), _("Ordered"), _("Units"),("Con factor"),  _("Received"),
				_("Outstanding"), _("This Delivery"), $disc, _("Unit Price"),_("Total")));
		else
			array_append($th, array(_("Ordered"), _("Units"),("Con factor"),  _("Received"),
				_("Outstanding"), _("This Delivery"),$disc, _("Unit Price"), _("Total")));
	}
	else{
		if ($pref['batch'] == 1)
			array_append($th, array(_("Batch"), _("Exp.Date"), _("Ordered"), _("Units"), _("Received"),
				_("Outstanding"), _("This Delivery"),$disc, _("Unit Price"), _("Total")));
		else
			array_append($th, array(_("Ordered"), _("Units"), _("Received"),
				_("Outstanding"), _("This Delivery"),$disc, _("Unit Price"), _("Total")));
	}
		
}


table_header($th);

$total = 0;
$k = 0;  //row colour counter

foreach ($purchase_order->line_items as $stock_item)
{

    
	$batch_name=get_batch_name_by_id($stock_item->grn_batch);
    if($pref['disc_in_amount'] == 1)
	$line_total = ($stock_item->price  - $stock_item->discount_percent ) *  $stock_item->qty_received ;
    else
        $line_total = $stock_item->qty_received * $stock_item->price * (1 - $stock_item->discount_percent );

	alt_table_row_color($k);

	label_cell($stock_item->stock_id);
	label_cell($stock_item->item_description);
	if($myrow_1['purchase_enable'])
	{
		label_cell($stock_item->$myrow_1['name']);
	}
	if($myrow_2['purchase_enable'])
	{
		label_cell($stock_item->$myrow_2['name']);
	}
	if($myrow_3['purchase_enable'])
	{
		label_cell($stock_item->$myrow_3['name']);
	}
	if($myrow_4['purchase_enable'])
	{
		label_cell($stock_item->$myrow_4['name']);
	}
	if($myrow_5['purchase_enable'])
	{
		label_cell($stock_item->$myrow_5['name']);
	}
	if($myrow_6['purchase_enable'])
	{
		label_cell($stock_item->$myrow_6['name']);
	}
	if($myrow_7['purchase_enable'])
	{
		label_cell($stock_item->$myrow_7['name']);
	}
	if($myrow_8['purchase_enable'])
	{
		label_cell($stock_item->$myrow_8['name']);
	}
	if($myrow_9['purchase_enable'])
	{
		label_cell($stock_item->$myrow_9['name']);
	}
	if($myrow_10['purchase_enable'])
	{
		label_cell($stock_item->$myrow_10['name']);
	}
	if($myrow_11['purchase_enable'])
	{
		label_cell($stock_item->$myrow_11['name']);
	}
	if($myrow_12['purchase_enable'])
	{
		label_cell($stock_item->$myrow_12['name']);
	}

	///combo inputs
	if($myrow_13['purchase_enable'])
	{
		label_cell($stock_item->$myrow_13['name']);
	}
	if($myrow_14['purchase_enable'])
	{
		label_cell($stock_item->$myrow_14['name']);
	}
	if($myrow_15['purchase_enable'])
	{
		label_cell($stock_item->$myrow_15['name']);
	}
	if($myrow_16['purchase_enable'])
	{
		label_cell($stock_item->$myrow_16['name']);
	}
	if($myrow_17['purchase_enable'])
	{
		label_cell($stock_item->$myrow_17['name']);
	}
	if($myrow_18['purchase_enable'])
	{
		label_cell($stock_item->$myrow_18['name']);
	}
	if($myrow_19['purchase_enable'])
	{
		label_cell($stock_item->$myrow_19['name']);
	}
	if($myrow_20['purchase_enable'])
	{
		label_cell($stock_item->$myrow_20['name']);
	}
	if($myrow_21['purchase_enable'])
	{
		label_cell($stock_item->$myrow_21['name']);
	}
if($myrow_22['purchase_enable'])
	{
		label_cell($stock_item->$myrow_22['name']);
	}
	if($pref['batch'] == 1) {
		label_cell($batch_name['name']);
		$batch=get_batch_by_id($stock_item->grn_batch);
		label_cell(sql2date($batch['exp_date']));
	}
	label_cell($stock_item->req_del_date, "nowrap align=right");
	$dec = get_qty_dec($stock_item->stock_id);
	$con_factor = get_company_item_pref('con_factor');
	if($pref['alt_uom'] == 1 ) {
		label_cell($stock_item->units_id);
		if($con_factor['purchase_enable'] == 1)
		amount_cell($stock_item->con_factor);
	}
	else
	{
		label_cell($stock_item->units);
	}
	$outstanding = $stock_item->qty_received - $stock_item->quantity;
	qty_cell($stock_item->qty_received, false, $dec);
    qty_cell($outstanding, false, $dec);
    qty_cell($stock_item->quantity, false, $dec);

if(!user_check_access('SA_SUPPPRICES')) {

    if ($SysPrefs->hide_prices_grn() == 1)
    {}
    else {
        if($pref['disc_in_amount'] == 1 ) {
            amount_decimal_cell($stock_item->discount_percent );
        }
        else{
            amount_decimal_cell($stock_item->discount_percent * 100);
        }
        amount_decimal_cell($stock_item->price );
        amount_cell($line_total);
//        qty_cell($stock_item->qty_inv, false, $dec);
      
      
    }
}
    end_row();

	$total += $line_total;
}

$display_sub_tot = number_format2($total,user_price_dec());
if(!user_check_access('SA_SUPPPRICES')) {

if ($SysPrefs->hide_prices_grn() == 1)
{}
else {
    label_row(_("Sub Total"), $display_sub_tot,
        "align=right colspan=6", "nowrap align=right", 1);

    $taxes = $purchase_order->get_taxes();
    $tax_total = display_edit_tax_items($taxes, 6, $purchase_order->tax_included, 1);
    $grn = get_grn_batch( $_GET['trans_no']);
    $display_total = price_format(($total + $tax_total) - $grn['discount1']);
start_row();
end_row();
if($grn['discount1']  > 0)
    label_cells(_("Discount "), price_format($grn['discount1']) , "colspan=6 align='right'","align='right'");

start_row();



    label_cells(_("Amount Total"), $display_total, "colspan=6 align='right'", "align='right'");
    label_cell('');
}
}
end_row();


end_table(1);

is_voided_display(ST_SUPPRECEIVE, $_GET['trans_no'], _("This delivery has been voided."));

end_page(true, false, false, ST_SUPPRECEIVE, $_GET['trans_no']);

