<?php
$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

include_once($path_to_root . "/includes/ui.inc");
include($path_to_root . "/purchasing/includes/po_class.inc");
if (!@$_GET['popup'])
{
	$js = "";
	if ($use_popup_windows)
		$js .= get_js_open_window(800, 500);
	if ($use_date_picker)
		$js .= get_js_date_picker();
	page(_($help_context = "Inventory Item Movement"), @$_GET['popup'], false, "", $js);
}
//------------------------------------------------------------------------------------------------

function get_stock_movements_before($stock_id, $StockLocation, $AfterDate)
{
	$after_date = date2sql($AfterDate);
	$sql = "SELECT SUM(qty) FROM ".TB_PREF."stock_moves WHERE stock_id=".db_escape($stock_id) . "
		AND loc_code=".db_escape( $StockLocation) . "
		AND tran_date < '" . $after_date . "'";
	$before_qty = db_query($sql, "The starting quantity on hand could not be calculated");

	$before_qty_row = db_fetch_row($before_qty);
	return $before_qty_row[0];
}
function get_sales_date($trans_no, $type)
{	$sql = "SELECT tran_date
 FROM ".TB_PREF."debtor_trans
		 WHERE trans_no=".db_escape($trans_no)."
		 AND type=".db_escape($type);

	$result = db_query($sql, 'Date Fetching');
	$row = db_fetch($result);
	return $row[0];
}

function get_po_no($trans_no)
{	$sql = "SELECT purch_order_no
 FROM ".TB_PREF."grn_batch
		 WHERE id=".db_escape($trans_no);

	$result = db_query($sql, 'PO Fetching');
	$row = db_fetch($result);
	return $row[0];
}


check_db_has_stock_items(_("There are no items defined in the system."));

if(get_post('ShowMoves'))
{
	$Ajax->activate('doc_tbl');
}

if (isset($_GET['stock_id']))
{
	$_POST['stock_id'] = $_GET['stock_id'];
}

if (!@$_GET['popup'])
	start_form();

if (!isset($_POST['stock_id']))
	$_POST['stock_id'] = get_global_stock_item();

start_table(TABLESTYLE_NOBORDER);
start_row();
if (!@$_GET['popup'])
	stock_costable_items_list_cells(_("Item:"), 'stock_id', $_POST['stock_id']);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

locations_list_cells(_("From Location:"), 'StockLocation', null);

date_cells(_("From:"), 'AfterDate', '', null, -30);
date_cells(_("To:"), 'BeforeDate');

submit_cells('ShowMoves',_("Show Movements"),'',_('Refresh Inquiry'), 'default');
end_row();
end_table();
if (!@$_GET['popup'])
	end_form();

set_global_stock_item($_POST['stock_id']);

$before_date = date2sql($_POST['BeforeDate']);
$after_date = date2sql($_POST['AfterDate']);

$result = get_stock_movements($_POST['stock_id'], $_POST['StockLocation'],
	$_POST['BeforeDate'], $_POST['AfterDate']);

div_start('doc_tbl');
start_table(TABLESTYLE);
$th = array(_("Type"), _("#"), _("DN/GRN"),  _("Date"), _("INV Ref"), _("INV Date"), _("Detail"),
	_("Quantity In"), _("Cost"), _("Total"), _("Quantity Out"), _("Cost"), _("Total"),
	_("Quantity On Hand"), _("Cost"), _("Total"));

table_header($th);

$before_qty = get_stock_movements_before($_POST['stock_id'], $_POST['StockLocation'], $_POST['AfterDate']);

$after_qty = $before_qty;

/*
if (!isset($before_qty_row[0]))
{
	$after_qty = $before_qty = 0;
}
*/
start_row("class='inquirybg'");
label_cell("<b>"._("Quantity on hand before") . " " . $_POST['AfterDate']."</b>", "align=center colspan=5");
label_cell("&nbsp;", "colspan=2");
$dec = get_qty_dec($_POST['stock_id']);
qty_cell($before_qty, false, $dec);
end_row();

$j = 1;
$k = 0; //row colour counter

$total_in = 0;
$total_out = 0;
$total_std_in_bal =0;

while ($myrow = db_fetch($result))
{
//var_dump($myrow);
	alt_table_row_color($k);

	$trandate = sql2date($myrow["tran_date"]);

	$type_name = $systypes_array[$myrow["type"]];
	//$cost += $myrow["standard_cost"];
	if ($myrow["qty"] > 0)
	{
		$quantity_formatted = number_format2($myrow["qty"], $dec);
		$total_in += $myrow["qty"];
		$total_std_in += $myrow["price"];
		$total_std_in_bal += ($myrow["price"]*$myrow["qty"]);

	}
	else
	{
		$quantity_formatted = number_format2(-$myrow["qty"], $dec);
		$total_out += -$myrow["qty"];
		$total_std_out += $myrow["standard_cost"];
		$total_std_out_bal += -($myrow["standard_cost"]*$myrow["qty"]);
	}
	$after_qty += $myrow["qty"];
	$std_after = $myrow["standard_cost"];
	//$std_after_bal += $myrow["qty"]*$myrow["standard_cost"];
	label_cell($type_name);

	label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"]));
	label_cell(get_trans_view_str($myrow["type"], $myrow["trans_no"], $myrow["reference"]));

	$deliveries = get_sales_child_numbers($myrow["type"], $myrow["trans_no"]);


	foreach($deliveries as $n => $delivery) {
		$deliveries[$n] = get_reference(ST_SALESINVOICE, $delivery);
		$deliveries1[$n] = $delivery;
	}



	$purchase_order = new purch_order;
	$po_no = get_po_no($myrow["trans_no"]);

	$invoice_result = get_po_invoices_credits($po_no);
	$inv2 = db_fetch($invoice_result );


	label_cell($trandate);

	if($myrow["type"] == '13')
	{
		$invoice_no = (implode(',', $deliveries));
		label_cell(get_trans_view_str(10, $delivery ,$invoice_no));
		label_cell(sql2date(get_sales_date($delivery, ST_SALESINVOICE)));
	}
	if($myrow["type"] == '25' || $myrow["type"] == '11')
	{
		label_cell(get_trans_view_str(20, $inv2['trans_no'] ,$inv2['reference']));
		label_cell(sql2date($inv2['tran_date']));
	}
	$person = $myrow["person_id"];
	$gl_posting = "";

	if (($myrow["type"] == ST_CUSTDELIVERY) || ($myrow["type"] == ST_CUSTCREDIT))
	{
		$cust_row = get_customer_details_from_trans($myrow["type"], $myrow["trans_no"]);

		if (strlen($cust_row['name']) > 0)
			$person = $cust_row['name'] . " (" . $cust_row['br_name'] . ")";

	}
	elseif ($myrow["type"] == ST_SUPPRECEIVE || $myrow['type'] == ST_SUPPCREDIT)
	{
		// get the supplier name
		$supp_name = get_supplier_name($myrow["person_id"]);

		if (strlen($supp_name) > 0)
			$person = $supp_name;
	}
	elseif ($myrow["type"] == ST_LOCTRANSFER || $myrow["type"] == ST_INVADJUST)
	{
		// get the adjustment type
		$movement_type = get_movement_type($myrow["person_id"]);
		$person = $movement_type["name"];
	}
	elseif ($myrow["type"]==ST_WORKORDER || $myrow["type"] == ST_MANUISSUE  ||
		$myrow["type"] == ST_MANURECEIVE)
	{
		$person = "";
	}

	label_cell($person);
	if($myrow["qty"] >= 0)
	{
		label_cell((($myrow["qty"] >= 0) ? $quantity_formatted : ""), "nowrap align=right");
		label_cell(($myrow["price"] ), "nowrap align=right");
		label_cell(($myrow["price"]*$myrow["qty"] ), "nowrap align=right");
		label_cell("");
		label_cell("");
		label_cell("");
	}
	elseif($myrow["qty"] < 0)
	{
		label_cell("");
		label_cell("");
		label_cell("");
		label_cell((($myrow["qty"] < 0) ? $quantity_formatted : ""), "nowrap align=right");
		label_cell(($myrow["standard_cost"] ), "nowrap align=right");
		label_cell(($myrow["standard_cost"]*$myrow["qty"]*-1 ), "nowrap align=right");

	}
	//else
	{
		qty_cell($after_qty, false, $dec);
		label_cell(($myrow["standard_cost"] ), "nowrap align=right");
		label_cell(($myrow["standard_cost"]*$after_qty ), "nowrap align=right");
	}



	end_row();
	$j++;
	If ($j == 12)
	{
		$j = 1;
		table_header($th);
	}
//end of page full new headings if
}
//end of while loop

start_row("class='inquirybg'");
label_cell("<b>"._("Quantity on hand after") . " " . $_POST['BeforeDate']."</b>", "align=center colspan=7");

//if($total_in >= 0)
{
	qty_cell($total_in, false, $dec);
	//qty_cell($total_std_in, false, $dec);
	label_cell("");
	qty_cell($total_std_in_bal, false, $dec);
//	label_cell("");
//	label_cell("");
//	label_cell("");

}
//elseif(-1*$total_out <= 0)
{
//	label_cell("");
//	label_cell("");
//	label_cell("");
	qty_cell($total_out, false, $dec);
	//qty_cell($total_std_out, false, $dec);
	label_cell("");
	qty_cell($total_std_out_bal, false, $dec);

}

qty_cell($after_qty, false, $dec);
qty_cell($std_after, false, $dec);
qty_cell($after_qty*$std_after, false, $dec);
end_row();

end_table(1);
div_end();
if (!@$_GET['popup'])
	end_page(@$_GET['popup'], false, false);

?>