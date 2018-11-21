<?php

$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/sales/includes/cart_class.inc");

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");

include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 600);

if ($_GET['trans_type'] == ST_SALESQUOTE)
{
    page(_($help_context = "View Sales Quotation"), true, false, "", $js);
    display_heading(sprintf(_("Sales Quotation #%d"),$_GET['trans_no']));
}
else
{
    page(_($help_context = "View Sales Order"), true, false, "", $js);
    display_heading(sprintf(_("Sales Order #%d"),$_GET['trans_no']));
}

if (isset($_SESSION['View']))
{
    unset ($_SESSION['View']);
}

$_SESSION['View'] = new Cart($_GET['trans_type'], $_GET['trans_no']);

start_table(TABLESTYLE2, "width='95%'", 5);

if ($_GET['trans_type'] != ST_SALESQUOTE)
{
    echo "<tr valign=top><td>";
    display_heading2(_("Order Information"));
    echo "</td><td>";
    display_heading2(_("Deliveries"));
    echo "</td><td>";
    display_heading2(_("Invoices/Credits"));
    echo "</td></tr>";
}

echo "<tr valign=top><td>";

start_table(TABLESTYLE, "width='95%'");
label_row(_("Customer Name"), $_SESSION['View']->customer_name, "class='tableheader2'",
    "colspan=3");
start_row();
label_cells(_("Customer Order Ref."), $_SESSION['View']->cust_ref, "class='tableheader2'");
label_cells(_("Deliver To Branch"), $_SESSION['View']->deliver_to, "class='tableheader2'");
end_row();
start_row();
label_cells(_("Ordered On"), $_SESSION['View']->document_date, "class='tableheader2'");
if ($_GET['trans_type'] == ST_SALESQUOTE)
    label_cells(_("Valid until"), $_SESSION['View']->due_date, "class='tableheader2'");
else
    label_cells(_("Requested Delivery"), $_SESSION['View']->due_date, "class='tableheader2'");
end_row();
start_row();
label_cells(_("Order Currency"), $_SESSION['View']->customer_currency, "class='tableheader2'");
label_cells(_("Deliver From Location"), $_SESSION['View']->location_name, "class='tableheader2'");
end_row();


if ($_SESSION['View']->payment_terms['days_before_due']<0)
{
    start_row();
    label_cells(_("Payment Terms"), $_SESSION['View']->payment_terms['terms'], "class='tableheader2'");
    label_cells(_("Required Pre-Payment"), price_format($_SESSION['View']->prep_amount), "class='tableheader2'");
    end_row();
    start_row();
    label_cells(_("Non-Invoiced Prepayments"), price_format($_SESSION['View']->alloc), "class='tableheader2'");
    label_cells(_("All Payments Allocated"), price_format($_SESSION['View']->sum_paid), "class='tableheader2'");
    end_row();
} else
    label_row(_("Payment Terms"), $_SESSION['View']->payment_terms['terms'], "class='tableheader2'", "colspan=3");

label_row(_("Delivery Address"), nl2br($_SESSION['View']->delivery_address),
    "class='tableheader2'", "colspan=3");
label_row(_("Reference"), $_SESSION['View']->reference, "class='tableheader2'", "colspan=3");
label_row(_("Telephone"), $_SESSION['View']->phone, "class='tableheader2'", "colspan=3");
;label_row(_("E-mail"), "<a href='mailto:" . $_SESSION['View']->email . "'>" . $_SESSION['View']->email . "</a>",
    "class='tableheader2'", "colspan=3");
label_row(_("Comments"), nl2br($_SESSION['View']->Comments), "class='tableheader2'", "colspan=3");
end_table();

if ($_GET['trans_type'] != ST_SALESQUOTE)
{
    echo "</td><td valign='top'>";

    start_table(TABLESTYLE);
    display_heading2(_("Delivery Notes"));


    $th = array(_("#"), _("Ref"), _("Date"), _("Total"));
    table_header($th);

    $dn_numbers = array();
    $delivery_total = 0;

    if ($result = get_sales_child_documents(ST_SALESORDER, $_GET['trans_no'])) {

        $k = 0;
        while ($del_row = db_fetch($result))
        {

            alt_table_row_color($k);
            $dn_numbers[] = $del_row["trans_no"];
            $this_total = $del_row["ov_freight"]+ $del_row["ov_amount"] + $del_row["ov_freight_tax"]  + $del_row["ov_gst"] - $del_row["discount1"] - $del_row["discount2"] ;
            $delivery_total += $this_total;

            label_cell(get_customer_trans_view_str($del_row["type"], $del_row["trans_no"]));
            label_cell($del_row["reference"]);
            label_cell(sql2date($del_row["tran_date"]));
            amount_cell($this_total);
            end_row();
        }
    }

    label_row(null, price_format($delivery_total), " ", "colspan=4 align=right");

    end_table();
    echo "</td><td valign='top'>";

    start_table(TABLESTYLE);
    display_heading2(_("Sales Invoices"));

    $th = array(_("#"), _("Ref"), _("Date"), _("Total"));
    table_header($th);

    $inv_numbers = array();
    $invoices_total = 0;

    if ($_SESSION['View']->prepaid)
        $result = get_sales_order_invoices($_GET['trans_no']);
    else
        $result = get_sales_child_documents(ST_CUSTDELIVERY, $dn_numbers);

    if ($result) {
        $k = 0;

        while ($inv_row = db_fetch($result))
        {
            alt_table_row_color($k);

            $this_total = $_SESSION['View']->prepaid ? $inv_row["prep_amount"] :
                $inv_row["ov_freight"] + $inv_row["ov_freight_tax"]  + $inv_row["ov_gst"] + $inv_row["ov_amount"] - $inv_row["discount1"] - $inv_row["discount2"];
            $invoices_total += $this_total;

            $inv_numbers[] = $inv_row["trans_no"];
            label_cell(get_customer_trans_view_str($inv_row["type"], $inv_row["trans_no"]));
            label_cell($inv_row["reference"]);
            label_cell(sql2date($inv_row["tran_date"]));
            amount_cell($this_total);
            end_row();
        }
    }
    label_row(null, price_format($invoices_total), " ", "colspan=4 align=right");

    end_table();

    display_heading2(_("Credit Notes"));

    start_table(TABLESTYLE);
    $th = array(_("#"), _("Ref"), _("Date"), _("Total"));
    table_header($th);

    $credits_total = 0;

    if (get_sales_child_documents(ST_SALESINVOICE, $inv_numbers)) {
        $k = 0;

        while ($credits_row = db_fetch($result))
        {

            alt_table_row_color($k);

            $this_total = $credits_row["ov_freight"] + $credits_row["ov_freight_tax"]  + $credits_row["ov_gst"] + $credits_row["ov_amount"] - $credits_row["discount1"] - $credits_row["discount2"];
            $credits_total += $this_total;

            label_cell(get_customer_trans_view_str($credits_row["type"], $credits_row["trans_no"]));
            label_cell($credits_row["reference"]);
            label_cell(sql2date($credits_row["tran_date"]));
            amount_cell(-$this_total);
            end_row();

        }

    }
    label_row(null, "<font color=red>" . price_format(-$credits_total) . "</font>",
        " ", "colspan=4 align=right");

    end_table();

    echo "</td></tr>";

    end_table();
}
echo "<center>";
if ($_SESSION['View']->so_type == 1)
    display_note(_("This Sales Order is used as a Template."), 0, 0, "class='currentfg'");
display_heading2(_("Line Details"));

start_table(TABLESTYLE, "width='95%'");
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
if($myrow_22['sale_enable']) {
    array_append($th, array($myrow_22['label_value']._("")) );
}
$pref = get_company_pref();
$con_factor = get_company_item_pref('con_factor');
if($pref['bonus'] == 1) {
    if ($pref['item_location'] == 1) {
    if ($pref['batch'] == 1) {
        if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
                array_append($th, array(_("Quantity"),_("Bonus"), /*_("Batch"),*/
                    _("Unit"), _("Location"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            } else {
                array_append($th, array(_("Quantity"), /*_("Batch"),*/
                    _("Unit"), _("Location"), /*_("Con.factor"),*/
                    $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            }
        } else {

            if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
                array_append($th, array(_("Quantity"), _("Bonus"),(""),
                    _("Unit"), _("Location"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            } else {
                array_append($th, array(_("Quantity"),_("Bonus"),
                    _("Unit"), _("Location"), /*_("Con.factor"),*/
                    $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            }
        }
    } else {
        if ($pref['batch'] == 1) {
            if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
                array_append($th, array(_("Quantity"), _("Bonus"),/*_("Batch"),*/
                    _("Unit"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            } else {
                array_append($th, array(_("Quantity"),_("Bonus"), /*_("Batch"),*/
                    _("Unit"), /*_("Con.factor"),*/
                    $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            }
        } else {

            if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
                array_append($th, array(_("Quantity"),_("Bonus"),
                    _("Unit"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            } else {
                array_append($th, array(_("Quantity"),_("Bonus"),
                    _("Unit"), /*_("Con.factor"),*/
                    $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
            }
        }
    }
}
else{
    if ($pref['item_location'] == 1) {
        if ($pref['batch'] == 1) {
            if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
            array_append($th, array(_("Quantity"), /*_("Batch"),*/
                    _("Unit"), _("Location"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        } else {
            array_append($th, array(_("Quantity"), /*_("Batch"),*/
                    _("Unit"), _("Location"), /*_("Con.factor"),*/
                $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        }
    } else {

        if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
                array_append($th, array(_("Quantity"), (""),
                    _("Unit"), _("Location"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        } else {
            array_append($th, array(_("Quantity"),
                    _("Unit"), _("Location"), /*_("Con.factor"),*/
                $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        }
    }
    } else {
        if ($pref['batch'] == 1) {
        if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
            array_append($th, array(_("Quantity"), /*_("Batch"),*/
                _("Unit"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        } else {
            array_append($th, array(_("Quantity"), /*_("Batch"),*/
                _("Unit"), /*_("Con.factor"),*/
                $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        }
    } else {

        if ($pref['alt_uom'] == 1 && $con_factor['sale_enable'] == 1) {
            array_append($th, array(_("Quantity"),
                _("Unit"), _("Con.factor"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        } else {
            array_append($th, array(_("Quantity"),
                _("Unit"), /*_("Con.factor"),*/
                $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered")));
        }
    }
}
}



/*
$total_text =get_total_cart_text();
$total_amount=get_total_cart_amount();
$total_comb=get_total_cart_combo();
$total_date=get_total_cart_date();

	$total_label=$total_text+$total_comb+$total_amount+$total_date;

	if($total_label==1) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""), "");
	}
	elseif($total_label==2) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""), "");
	}
	elseif($total_label==3) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""), "");
	}
	elseif($total_label==4) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""), "");
	}
	elseif($total_label==5) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""), "");
	}
	elseif($total_label==6) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""), "");
	}
	elseif($total_label==7) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""), "");
	}
	elseif($total_label==8) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""), "");
	}
	elseif($total_label==9) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""), "");
	}
	elseif($total_label==10) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""), "");
	}
	elseif($total_label==11) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""), "");
	}
	elseif($total_label==12) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""), "");
	}
	elseif($total_label==13) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""), "");
	}
	elseif($total_label==14) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._("Date1"),$myrow13['label_value']._("Date2"),$myrow14['label_value']._("Date3"), "");

	}
	elseif($total_label==15) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),$myrow15['label_value']._(""), "");

	}
	elseif($total_label==16) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),$myrow15['label_value']._(""),$myrow16['label_value']._(""), "");

	}
	elseif($total_label==17) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),
			$myrow15['label_value']._(""),$myrow16['label_value']._(""),$myrow17['label_value']._(""), "");

	}
	elseif($total_label==18) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),
			$myrow15['label_value']._(""),$myrow16['label_value']._(""),$myrow17['label_value']._(""),$myrow18['label_value']._(""), "");

	}
	elseif($total_label==19) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),
			$myrow15['label_value']._(""),$myrow16['label_value']._(""),$myrow17['label_value']._(""),$myrow18['label_value']._(""),$myrow19['label_value']._(""), "");

	}
	elseif($total_label==20) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),
			$myrow15['label_value']._(""),$myrow16['label_value']._(""),$myrow17['label_value']._(""),$myrow18['label_value']._(""),$myrow19['label_value']._(""),$myrow20['label_value']._(""), "");

	}
	elseif($total_label==21) {
		$th = array(_("Item Code"), _("Item Description"), _("Quantity"),

			_("Unit"), $order->tax_included ? _("Price after Tax") : _("Price before Tax"), _("Discount %"), _("Total"), _("Quantity Delivered"),$myrow1['label_value']._(""),$myrow2['label_value']._(""),$myrow3['label_value']._(""),$myrow4['label_value']._(""),$myrow5['label_value']._(""),$myrow6['label_value']._(""),$myrow7['label_value']._(""),$myrow8['label_value']._(""),$myrow9['label_value']._(""),$myrow10['label_value']._(""),$myrow11['label_value']._(""),$myrow12['label_value']._(""),$myrow13['label_value']._(""),$myrow14['label_value']._(""),
			$myrow15['label_value']._(""),$myrow16['label_value']._(""),$myrow17['label_value']._(""),$myrow18['label_value']._(""),$myrow19['label_value']._(""),$myrow20['label_value']._(""),$myrow21['label_value']._(""), "", "");

	}
*/
table_header($th);

$k = 0;  //row colour counter

foreach ($_SESSION['View']->line_items as $stock_item)
{

    $line_total = round2($stock_item->quantity * $stock_item->price * (1 - $stock_item->discount_percent),
        user_price_dec());

    alt_table_row_color($k);

    label_cell($stock_item->stock_id);
    label_cell($stock_item->item_description);
    if($myrow_1['sale_enable'])
    {
        label_cell($stock_item->$myrow_1['name']);
    }
    if($myrow_2['sale_enable'])
    {
        label_cell($stock_item->$myrow_2['name']);
    }
    if($myrow_3['sale_enable'])
    {
        label_cell($stock_item->$myrow_3['name']);
    }
    if($myrow_4['sale_enable'])
    {
        label_cell($stock_item->$myrow_4['name']);
    }
    if($myrow_5['sale_enable'])
    {
        label_cell($stock_item->$myrow_5['name']);
    }
    if($myrow_6['sale_enable'])
    {
        label_cell($stock_item->$myrow_6['name']);
    }
    if($myrow_7['sale_enable'])
    {
        label_cell($stock_item->$myrow_7['name']);
    }
    if($myrow_8['sale_enable'])
    {
        label_cell($stock_item->$myrow_8['name']);
    }
    if($myrow_9['sale_enable'])
    {
        label_cell($stock_item->$myrow_9['name']);
    }
    if($myrow_10['sale_enable'])
    {
        label_cell($stock_item->$myrow_10['name']);
    }
    if($myrow_11['sale_enable'])
    {
        label_cell($stock_item->$myrow_11['name']);
    }
    if($myrow_12['sale_enable'])
    {
        label_cell($stock_item->$myrow_12['name']);
    }

    ///combo inputs
    if($myrow_13['sale_enable'])
    {
        label_cell($stock_item->$myrow_13['name']);
    }
    if($myrow_14['sale_enable'])
    {
        label_cell($stock_item->$myrow_14['name']);
    }
    if($myrow_15['sale_enable'])
    {
        label_cell($stock_item->$myrow_15['name']);
    }
    if($myrow_16['sale_enable'])
    {
        label_cell($stock_item->$myrow_16['name']);
    }
    if($myrow_17['sale_enable'])
    {
        label_cell($stock_item->$myrow_17['name']);
    }
    if($myrow_18['sale_enable'])
    {
        label_cell($stock_item->$myrow_18['name']);
    }
    if($myrow_19['sale_enable'])
    {
        label_cell($stock_item->$myrow_19['name']);
    }
    if($myrow_20['sale_enable'])
    {
        label_cell($stock_item->$myrow_20['name']);
    }
    if($myrow_21['sale_enable'])
    {
        label_cell($stock_item->$myrow_21['name']);
    }
    if($myrow_22['sale_enable'])
    {
        label_cell($stock_item->$myrow_22['name']);
    }


    $dec = get_qty_dec($stock_item->stock_id);
    $prefs=get_company_prefs();
    qty_cell($stock_item->quantity, false, $dec);
    if($prefs['bonus'] == 1) {
        label_cell($stock_item->bonus);
    }

    $con_factor = get_company_item_pref('con_factor');
    if($prefs['batch'] == 1) {
        $batch=get_batch_name_by_id($stock_item->batch);
//		label_cell($batch['name']);
    }
    if($pref['alt_uom'] == 1) {
        label_cell($stock_item->units_id);
        if($con_factor['sale_enable'] == 1)
            amount_cell($stock_item->con_factor);

    }
    else {
        label_cell($stock_item->units);
    }
    if($pref['item_location'] == 1)
        label_cell(get_location_name($stock_item->item_location));

    amount_cell($stock_item->price);
    amount_cell($stock_item->discount_percent * 100);
    amount_cell($line_total);

    qty_cell($stock_item->qty_done, false, $dec);
    /*
        $total_text =get_total_cart_text();
        $total_amount=get_total_cart_amount();
        $total_comb=get_total_cart_combo();
        $total_date=get_total_cart_date();
                if($total_text==1)
                {
                label_cell($stock_item->text1);
                }
                elseif($total_text==2)
                {
                label_cell($stock_item->text1);
                label_cell($stock_item->text2);
                }
                elseif($total_text==3)
                {
                label_cell($stock_item->text1);
                label_cell($stock_item->text2);
                label_cell($stock_item->text3);
                }
                elseif($total_text==4)
                {
                label_cell($stock_item->text1);
                label_cell($stock_item->text2);
                label_cell($stock_item->text3);
                label_cell($stock_item->text4);
                }
                elseif($total_text==5)
                {
                    label_cell($stock_item->text1);
                    label_cell($stock_item->text2);
                    label_cell($stock_item->text3);
                    label_cell($stock_item->text4);
                    label_cell($stock_item->text5);
                }
                elseif($total_text==6)
                {
                    label_cell($stock_item->text1);
                    label_cell($stock_item->text2);
                    label_cell($stock_item->text3);
                    label_cell($stock_item->text4);
                    label_cell($stock_item->text5);
                    label_cell($stock_item->text6);
                }
                ///
                if($total_amount==1)
                {
                label_cell($stock_item->amount1);
                }
                elseif($total_amount==2)
                {
                label_cell($stock_item->amount1);
                label_cell($stock_item->amount2);
                }
                elseif($total_amount==3)
                {
                label_cell($stock_item->amount1);
                label_cell($stock_item->amount2);
                label_cell($stock_item->amount3);
                }
                elseif($total_amount==4)
                {
                label_cell($stock_item->amount1);
                label_cell($stock_item->amount2);
                label_cell($stock_item->amount3);
                label_cell($stock_item->amount4);
                }
                elseif($total_amount==5)
                {
                    label_cell($stock_item->amount1);
                    label_cell($stock_item->amount2);
                    label_cell($stock_item->amount3);
                    label_cell($stock_item->amount4);
                    label_cell($stock_item->amount5);
                }
                elseif($total_amount==6)
                {
                    label_cell($stock_item->amount1);
                    label_cell($stock_item->amount2);
                    label_cell($stock_item->amount3);
                    label_cell($stock_item->amount4);
                    label_cell($stock_item->amount5);
                    label_cell($stock_item->amount6);
                }
                    ///
                if($total_comb==1)
                {
                label_cell($stock_item->combo1);
                }
                elseif($total_comb==2)
                {
                label_cell($stock_item->combo1);
                label_cell($stock_item->combo1);
                }
                elseif($total_comb==3)
                {
                label_cell($stock_item->combo1);
                label_cell($stock_item->combo1);
                label_cell($stock_item->combo1);
                }
                elseif($total_comb==4)
                {
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo4);
                }
                elseif($total_comb==5)
                {
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo4);
                    label_cell($stock_item->combo5);
                }
                elseif($total_comb==6)
                {
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo1);
                    label_cell($stock_item->combo4);
                    label_cell($stock_item->combo5);
                    label_cell($stock_item->combo6);
                }
                    ///
                if($total_date==1)
                {
                label_cell($stock_item->date1);
                }
                elseif($total_date==2)
                {
                label_cell($stock_item->date1);
                label_cell($stock_item->date2);
                }
                elseif($total_date==3)
                {
                label_cell($stock_item->date1);
                label_cell($stock_item->date2);
                label_cell($stock_item->date3);
                }
        */
    /*	label_cell($stock_item->text1);
        label_cell($stock_item->text2);
        label_cell($stock_item->text3);
        label_cell($stock_item->text4);
        label_cell($stock_item->amount1);
        label_cell($stock_item->amount2);
        label_cell($stock_item->amount3);
        label_cell($stock_item->amount4);
        label_cell($stock_item->date1);
        label_cell($stock_item->date2);
        label_cell($stock_item->date3);
        if($stock_item->combo1 == 1)
            $combo1 = "Yes";
        elseif($stock_item->combo1 == 0)
            $combo1 = "No";
        label_cell($combo1);

        if($stock_item->combo2 == 1)
            $combo2 = "Yes";
        elseif($stock_item->combo2 == 0)
            $combo2 = "No";
        label_cell($combo2);

        if($stock_item->combo3 == 1)
            $combo3 = "Yes";
        elseif($stock_item->combo3 == 0)
            $combo3 = "No";
        label_cell($combo3);*/

    end_row();
}

if ($_SESSION['View']->freight_cost != 0.0)
    label_row(_("Shipping"), price_format($_SESSION['View']->freight_cost),
        "align=right colspan=20", "nowrap align=right", 1);

$TotalDiscount = $_SESSION['View']->discount1 + $_SESSION['View']->discount2;

$sub_tot = $_SESSION['View']->get_items_total() + $_SESSION['View']->freight_cost - $TotalDiscount;

$display_sub_tot = price_format($sub_tot);

label_row(_("Discount1 :"), price_format($_SESSION['View']->discount1), "align=right colspan=7",
    "nowrap align=right", 1);
label_row(_("Discount2 :"), price_format($_SESSION['View']->discount2), "align=right colspan=7",
    "nowrap align=right", 1);

label_row(_("Sub Total"), $display_sub_tot, "align=right colspan=7",
    "nowrap align=right", 1);


$taxes = $_SESSION['View']->get_taxes();

$tax_total = display_edit_tax_items($taxes, 8, $_SESSION['View']->tax_included,2);

$display_total = price_format($sub_tot + $tax_total);

start_row();
label_cells(_("Amount Total"), $display_total, "colspan=7 align='right'","align='right'");
label_cell('', "colspan=2");
end_row();
end_table();

display_allocations_to(PT_CUSTOMER, $_SESSION['View']->customer_id, $_GET['trans_type'], $_GET['trans_no'], $sub_tot + $tax_total);

end_page(true, false, false, $_GET['trans_type'], $_GET['trans_no']);

