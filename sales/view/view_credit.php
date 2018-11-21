<?php

$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/sales/includes/sales_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "View Credit Note"), true, false, "", $js);

if (isset($_GET["trans_no"]))
{
	$trans_id = $_GET["trans_no"];
}
elseif (isset($_POST["trans_no"]))
{
	$trans_id = $_POST["trans_no"];
}

$myrow = get_customer_trans($trans_id, ST_CUSTCREDIT);

$branch = get_branch($myrow["branch_code"]);

display_heading("<font color=red>" . sprintf(_("CREDIT NOTE #%d"), $trans_id). "</font>");
echo "<br>";

start_table(TABLESTYLE2, "width='95%'");
echo "<tr valign=top><td>"; // outer table

/*Now the customer charged to details in a sub table*/
start_table(TABLESTYLE, "width='100%'");
$th = array(_("Customer"));
table_header($th);

label_row(null, $myrow["DebtorName"] . "<br>" . nl2br($myrow["address"]), "nowrap");

end_table();
/*end of the small table showing charge to account details */

echo "</td><td>"; // outer table

start_table(TABLESTYLE, "width='100%'");
$th = array(_("Branch"));
table_header($th);

label_row(null, $branch["br_name"] . "<br>" . nl2br($branch["br_address"]), "nowrap");
end_table();

echo "</td><td>"; // outer table

start_table(TABLESTYLE, "width='100%'");
start_row();
label_cells(_("Ref"), $myrow["reference"], "class='tableheader2'");
label_cells(_("Date"), sql2date($myrow["tran_date"]), "class='tableheader2'");
label_cells(_("Currency"), $myrow["curr_code"], "class='tableheader2'");
end_row();
start_row();
label_cells(_("Sales Type"), $myrow["sales_type"], "class='tableheader2'");
label_cells(_("Shipping Company"), $myrow["shipper_name"], "class='tableheader2'");
end_row();
comments_display_row(ST_CUSTCREDIT, $trans_id);
end_table();

echo "</td></tr>";
end_table(1); // outer table

$sub_total = 0;

$result = get_customer_trans_details(ST_CUSTCREDIT, $trans_id);

start_table(TABLESTYLE, "width='95%'");

if (db_num_rows($result) > 0)
{
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
	$th = array(_("Item Code"), _("Item Description"));
//Text Boxes Headings

	if($myrow_1['sale_enable']) {
		array_append($th, array($myrow_1['label_value']._("")) );
	}
	if($myrow_2['sale_enable']) {
		array_append($th, array($myrow_2['label_value']._("")) );
	}
	if($myrow_3['sale_enable']) {
		array_append($th, array($myrow_3['label_value']._("")) );
	}
	if($myrow_4['sale_enable']) {
		array_append($th, array($myrow_4['label_value']._("")) );
	}
	if($myrow_5['sale_enable']) {
		array_append($th, array($myrow_5['label_value']._("")) );
	}
	if($myrow_6['sale_enable']) {
		array_append($th, array($myrow_6['label_value']._("")) );
	}
	if($myrow_7['sale_enable']) {
		array_append($th, array($myrow_7['label_value']._("")) );
	}
	if($myrow_8['sale_enable']) {
		array_append($th, array($myrow_8['label_value']._("")) );
	}
	if($myrow_9['sale_enable']) {
		array_append($th, array($myrow_9['label_value']._("")) );
	}
	if($myrow_10['sale_enable']) {
		array_append($th, array($myrow_10['label_value']._("")) );
	}
	if($myrow_11['sale_enable']) {
		array_append($th, array($myrow_11['label_value']._("")) );
	}
	if($myrow_12['sale_enable']) {
		array_append($th, array($myrow_12['label_value']._("")) );
	}
	if($myrow_13['sale_enable']) {
		array_append($th, array($myrow_13['label_value']._("")) );
	}
	if($myrow_14['sale_enable']) {
		array_append($th, array($myrow_14['label_value']._("")) );
	}
	if($myrow_15['sale_enable']) {
		array_append($th, array($myrow_15['label_value']._("")) );
	}
	if($myrow_16['sale_enable']) {
		array_append($th, array($myrow_16['label_value']._("")) );
	}
	if($myrow_17['sale_enable']) {
		array_append($th, array($myrow_17['label_value']._("")) );
	}
	if($myrow_18['sale_enable']) {
		array_append($th, array($myrow_18['label_value']._("")) );
	}
	if($myrow_19['sale_enable']) {
		array_append($th, array($myrow_19['label_value']._("")) );
	}
	if($myrow_20['sale_enable']) {
		array_append($th, array($myrow_20['label_value']._("")) );
	}
	if($myrow_21['sale_enable']) {
		array_append($th, array($myrow_21['label_value']._("")) );
	}

	{	$pref = get_company_prefs();
		if($pref['alt_uom'] == 1) {
			if ($pref['batch'] == 1) {
				array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
					_("Unit"),_("Con factor"), _("Price"), _("Discount %"), _("Total")));

			} else {
				array_append($th, array(_("Quantity"),
					_("Unit"),_("Con factor"), _("Price"), _("Discount %"), _("Total")));
			}
		}
		else{
			if ($pref['batch'] == 1) {
				array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
					_("Unit"), _("Price"), _("Discount %"), _("Total")));

			} else {
				array_append($th, array(_("Quantity"),
					_("Unit"), _("Price"), _("Discount %"), _("Total")));
			}
		}
	}

	table_header($th);

	$k = 0;	//row colour counter
	$sub_total = 0;

	while ($myrow2 = db_fetch($result))
	{
		if ($myrow2["quantity"] == 0) continue;
		alt_table_row_color($k);

		$value = round2(((1 - $myrow2["discount_percent"]) * $myrow2["unit_price"] * $myrow2["quantity"]),
		   user_price_dec());
		$sub_total += $value;

		if ($myrow2["discount_percent"] == 0)
		{
			$display_discount = "";
		}
		else
		{
		   $display_discount = percent_format($myrow2["discount_percent"]*100) . "%";
		}

		label_cell($myrow2["stock_id"]);
		label_cell($myrow2["StockDescription"]);
		if($myrow_1['sale_enable'])
		{
			label_cell($myrow2[$myrow_1['name']]);
		}
		if($myrow_2['sale_enable'])
		{
			label_cell($myrow2[$myrow_2['name']]);
		}
		if($myrow_3['sale_enable'])
		{
			label_cell($myrow2[$myrow_3['name']]);
		}
		if($myrow_4['sale_enable'])
		{
			label_cell($myrow2[$myrow_4['name']]);
		}
		if($myrow_5['sale_enable'])
		{
			label_cell($myrow2[$myrow_5['name']]);
		}
		if($myrow_6['sale_enable'])
		{
			label_cell($myrow2[$myrow_6['name']]);
		}
		if($myrow_7['sale_enable'])
		{
			label_cell($myrow2[$myrow_7['name']]);
		}
		if($myrow_8['sale_enable'])
		{
			label_cell($myrow2[$myrow_8['name']]);
		}
		if($myrow_9['sale_enable'])
		{
			label_cell($myrow2[$myrow_9['name']]);
		}
		if($myrow_10['sale_enable'])
		{
			label_cell($myrow2[$myrow_10['name']]);
		}
		if($myrow_11['sale_enable'])
		{
			label_cell($myrow2[$myrow_11['name']]);
		}
		if($myrow_12['sale_enable'])
		{
			label_cell($myrow2[$myrow_12['name']]);
		}

		///combo inputs
		if($myrow_13['sale_enable'])
		{
			label_cell($myrow2[$myrow_13['name']]);
		}
		if($myrow_14['sale_enable'])
		{
			label_cell($myrow2[$myrow_14['name']]);
		}
		if($myrow_15['sale_enable'])
		{
			label_cell($myrow2[$myrow_15['name']]);
		}
		if($myrow_16['sale_enable'])
		{
			label_cell($myrow2[$myrow_16['name']]);
		}
		if($myrow_17['sale_enable'])
		{
			label_cell($myrow2[$myrow_17['name']]);
		}
		if($myrow_18['sale_enable'])
		{
			label_cell($myrow2[$myrow_18['name']]);
		}
		if($myrow_19['sale_enable'])
		{
			label_cell($myrow2[$myrow_19['name']]);
		}
		if($myrow_20['sale_enable'])
		{
			label_cell($myrow2[$myrow_20['name']]);
		}
		if($myrow_21['sale_enable'])
		{
			label_cell($myrow2[$myrow_21['name']]);
		}
		qty_cell($myrow2["quantity"], false, get_qty_dec($myrow2["stock_id"]));
		if($pref['batch'] == 1) {
			$batch=get_batch_by_id($myrow2["batch"]);
			label_cell($batch["name"], "align=right");
			label_cell(sql2date($batch["exp_date"]), "align=right");
		}
		if($pref['alt_uom'] == 1) {
			label_cell($myrow2["units_id"], "align=right");
			qty_cell($myrow2["con_factor"], "align=right");
		}
		else{
			label_cell($myrow2["units"], "align=right");
		}
		amount_cell($myrow2["unit_price"]);
		label_cell($display_discount, "align=right");
		amount_cell($value);
		end_row();
	} //end while there are line items to print out
}
else
	display_note(_("There are no line items on this credit note."), 1, 2);

$display_sub_tot = price_format($sub_total);

$credit_total = $myrow["ov_freight"]+$myrow["ov_gst"]+$myrow["ov_amount"]+$myrow["ov_freight_tax"];
$display_total = price_format($credit_total);

/*Print out the invoice text entered */
$pref=get_company_prefs();
	if($pref['batch'] ==1)
	$colspan=8;
	else
	$colspan=6;

if ($sub_total != 0)
	label_row(_("Sub Total"), $display_sub_tot, "colspan=".$colspan." align=right",
		"nowrap align=right width='15%'");
if ($myrow["ov_freight"] != 0.0)
{
	$display_freight = price_format($myrow["ov_freight"]);
	label_row(_("Shipping"), $display_freight, "colspan=".$colspan." align=right", "nowrap align=right");
}

$tax_items = get_trans_tax_details(ST_CUSTCREDIT, $trans_id);
display_customer_trans_tax_details($tax_items, 8);

label_row("<font color=red>" . _("TOTAL CREDIT") . "</font",
	"<font color=red>$display_total</font>", "colspan=".$colspan." align=right", "nowrap align=right");
end_table(1);

$voided = is_voided_display(ST_CUSTCREDIT, $trans_id, _("This credit note has been voided."));

if (!$voided)
	display_allocations_from(PT_CUSTOMER,
		$myrow['debtor_no'], ST_CUSTCREDIT, $trans_id, $credit_total);

/* end of check to see that there was an invoice record to print */

end_page(true, false, false, ST_CUSTCREDIT, $trans_id);

