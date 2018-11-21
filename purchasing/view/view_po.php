<?php

$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";
include($path_to_root . "/purchasing/includes/po_class.inc");

include($path_to_root . "/includes/session.inc");
include($path_to_root . "/purchasing/includes/purchasing_ui.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "View Purchase Order"), true, false, "", $js);


if (!isset($_GET['trans_no']))
{
	die ("<br>" . _("This page must be called with a purchase order number to review."));
}

display_heading(_("Purchase Order") . " #" . $_GET['trans_no']);

$purchase_order = new purch_order;

read_po($_GET['trans_no'], $purchase_order);
echo "<br>";
display_po_summary($purchase_order, true);

start_table(TABLESTYLE, "width='90%'", 6);
echo "<tr><td valign=top>"; // outer table

display_heading2(_("Line Details"));

start_table(TABLESTYLE, "width='100%'");
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
	$prefs=get_company_prefs();
	$con_factor = get_company_item_pref('con_factor');
    $prefs=get_company_prefs();
    if( $prefs['disc_in_amount'] == 1) {
     $disc = "Discount ";
    }
    else{
        $disc = "Discount %";
    }
	if($prefs['alt_uom'] && $con_factor['purchase_enable'] == 1) {
		array_append($th, array(_("Quantity"), _("Unit"), _("Con factor"), _("Price"),
			_("Requested By"), $disc,_("Line Total"), _("Quantity Received"), _("Quantity Invoiced")));
	}
	else{
		array_append($th, array(_("Quantity"), _("Unit"), _("Price"),
			_("Requested By"), $disc,_("Line Total"), _("Quantity Received"), _("Quantity Invoiced")));
	}
}
table_header($th);
$total = $k = 0;
$overdue_items = false;

foreach ($purchase_order->line_items as $stock_item)
{
    $prefs=get_company_prefs();
    if( $prefs['disc_in_amount'] == 1) {
        $line_total =  ($stock_item->price - $stock_item->discount_percent) * $stock_item->quantity;
    }
    else
        {
            $line_total = $stock_item->quantity * $stock_item->price * (1 - $stock_item->discount_percent);
        }

	// if overdue and outstanding quantities, then highlight as so
	if (($stock_item->quantity - $stock_item->qty_received > 0)	&&
		date1_greater_date2(Today(), $stock_item->req_del_date))
	{
    	start_row("class='overduebg'");
    	$overdue_items = true;
	}
	else
	{
		alt_table_row_color($k);
	}

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
	$dec = get_qty_dec($stock_item->stock_id);
	qty_cell($stock_item->quantity, false, $dec);
	$con_factor = get_company_item_pref('con_factor');
	
	if($prefs['alt_uom'] ) {
		label_cell($stock_item->units_id);
		if($con_factor['purchase_enable'] == 1)
		amount_cell($stock_item->con_factor);
	}
	else {
		label_cell($stock_item->units);
	}
	if(!user_check_access('SA_SUPPPRICES')) {
	
		amount_decimal_cell($stock_item->price);
}
	label_cell($stock_item->req_del_date);
    if( $prefs['disc_in_amount'] == 1) {
        label_cell(number_format2($stock_item->discount_percent ), 2);
    }
    else{
        label_cell(number_format2($stock_item->discount_percent * 100), 2);
    }
    
    if(!user_check_access('SA_SUPPPRICES')) {
	amount_cell($line_total);
        
    }
	qty_cell($stock_item->qty_received, false, $dec);
	qty_cell($stock_item->qty_inv, false, $dec);
	end_row();

	$total += $line_total;
}

$display_sub_tot = number_format2($total,user_price_dec());

if(!user_check_access('SA_SUPPPRICES')) {
label_row(_("Sub Total"), $display_sub_tot,
	"align=right colspan=6", "nowrap align=right",2);
}
$taxes = $purchase_order->get_taxes();
$tax_total = display_edit_tax_items($taxes, 6, $purchase_order->tax_included,2);

$display_total = price_format(($total + $tax_total - $purchase_order->discount1));

start_row();
end_row();
if($purchase_order->discount1  > 0)
label_cells(_("Discount "), $purchase_order->discount1   , "colspan=6 align='right'","align='right'");

start_row();
if(!user_check_access('SA_SUPPPRICES')) {
label_cells(_("Amount Total"), $display_total   , "colspan=6 align='right'","align='right'");
label_cell('', "colspan=2");}
end_row();

end_table();

if ($overdue_items)
	display_note(_("Marked items are overdue."), 0, 0, "class='overduefg'");

//----------------------------------------------------------------------------------------------------

$k = 0;

$grns_result = get_po_grns($_GET['trans_no']);

if (db_num_rows($grns_result) > 0)
{

    echo "</td><td valign=top>"; // outer table

    display_heading2(_("Deliveries"));
    start_table(TABLESTYLE);
    $th = array(_("#"), _("Reference"), _("Delivered On"));
    table_header($th);
    while ($myrow = db_fetch($grns_result))
    {
		alt_table_row_color($k);

    	label_cell(get_trans_view_str(ST_SUPPRECEIVE,$myrow["id"]));
    	label_cell($myrow["reference"]);
    	label_cell(sql2date($myrow["delivery_date"]));

    	end_row();
    }
    end_table();;
}

$invoice_result = get_po_invoices_credits($_GET['trans_no']);

$k = 0;

if (db_num_rows($invoice_result) > 0)
{

    echo "</td><td valign=top>"; // outer table

    display_heading2(_("Invoices/Credits"));
    start_table(TABLESTYLE);
    $th = array(_("#"), _("Date"), _("Total"));
    table_header($th);
    while ($myrow = db_fetch($invoice_result))
    {
    	alt_table_row_color($k);

    	label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"]));
    	label_cell(sql2date($myrow["tran_date"]));
    	
    	if(!user_check_access('SA_SUPPPRICES')) {
    	amount_cell($myrow["Total"]);
    	    
    	}
    	end_row();
    }
    end_table();
}

echo "</td></tr>";

end_table(1); // outer table

display_allocations_to(PT_SUPPLIER, $purchase_order->supplier_id, ST_PURCHORDER, $purchase_order->order_no, $total + $tax_total);

//----------------------------------------------------------------------------------------------------

end_page(true, false, false, ST_PURCHORDER, $_GET['trans_no']);

