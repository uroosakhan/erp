<?php

include_once($path_to_root . "/sales/includes/cart_class.inc");

//----------------------------------------------------------------------------
function add_to_order_offer_discount(&$order, $new_item, $new_item_qty, $price, $discount, $description='')
{

    $order->discount_offer_add_to_cart(count($order->discount_offer_line_items),$new_item,
        $new_item_qty, $price, $discount, 0,0,
        $description, 0, 0 , 0);

}
//---------------------------------------------------------------------------------
//---------------------------------------------------------------------------------

function display_order_summary_offer_discount($title, &$order, $editable_items=false)
{

    display_heading($title);

    div_start('offer_items_table');
    start_table(TABLESTYLE, "width='90%'");
    $th = array(_("Item Code"), _("Item Description"), _("Quantity"), _("Price before Tax"), _("Discount %"), _("Total"), "", "");

//	session_start();
    table_header($th);

    $total = $TotalQty = 0;
    $k = 0;  //row colour counter

    $id = find_submit('Edit');
    foreach ($order->get_items1() as $line_no=>$stock_item)
    {
        $line_total = round($stock_item->qty_dispatched * $stock_item->price * (1 - $stock_item->discount_percent),
            user_price_dec());

        if (!$editable_items || $id != $line_no)
        {
            alt_table_row_color($k);
            view_stock_status_cell($stock_item->stock_id);
            label_cell($stock_item->item_description);
            label_cell($stock_item->qty_dispatched);
            amount_decimal_cell($stock_item->price);
            percent_cell($stock_item->discount_percent * 100);
            amount_cell($line_total);
            if ($editable_items)
            {
                edit_button_cell("Edit$line_no", _("Edit"),
                    _('Edit document line'));
                delete_button_cell("Delete$line_no", _("Delete"),
                    _('Remove line from document'));
            }
            end_row();
        }
        else
        {
            sales_order_item_controls_offer_discount($order, $k,  $line_no);
        }
        $total += $line_total;
    }
    if ($id==-1 && $editable_items)
        sales_order_item_controls_offer_discount($order, $k);
    $colspan = 6;
    if ($order->trans_no != 0)
        ++$colspan;

    start_row();
    end_row();$display_total = price_format(($total + input_num('freight_cost')));
    start_row();
    label_cells(_("Amount Total"), $display_total, "colspan=$colspan align=right","align=right");
    end_row();
    end_table();
    div_end();
}

// ------------------------------------------------------------------------------
//--------------------------------------------------------------------------------

function sales_order_item_controls_offer_discount(&$order, &$rowcounter, $line_no=-1)
{
    global $Ajax;

    alt_table_row_color($rowcounter);
    $id = find_submit('Edit');
    if ($line_no!=-1 && $line_no == $id) // edit old line
    {
        $_POST['stock_id'] = $order->discount_offer_line_items[$id]->stock_id;
        $dec = get_qty_dec($_POST['stock_id']);
        $_POST['qty'] = number_format2($order->discount_offer_line_items[$id]->qty_dispatched, $dec);
        $_POST['price'] = price_format($order->discount_offer_line_items[$id]->price);
        $_POST['Disc'] = percent_format($order->discount_offer_line_items[$id]->discount_percent*100);
        $_POST['item_description'] = $order->discount_offer_line_items[$id]->item_description;
        hidden('stock_id', $_POST['stock_id']);
        label_cell($_POST['stock_id']);

        if ($order->discount_offer_line_items[$id]->descr_editable)
            text_cells(null,'item_description', null, 45, 150);
        else {
            hidden('item_description', $_POST['item_description']);
            label_cell($_POST['item_description']);
        }
        $Ajax->activate('items_table');
        //For updation
    }
    else
    {
        sales_items_list_cells(null,'stock', null, false, true);
        if (list_updated('stock_id')) {
            $Ajax->activate('price');
            $Ajax->activate('units');
            $Ajax->activate('qty');
            $Ajax->activate('line_total');
        }

        $item_info = get_item_edit_info($_POST['stock_id']);
        $units = $item_info["units"];
        $dec = $item_info['decimals'];
        $_POST['qty'] = number_format2(1, $dec);
        $price = get_kit_price($_POST['stock_id'],
            $order->customer_currency, $order->sales_type,
            $order->price_factor, get_post('OrderDate'));
        $_POST['price'] = price_format($price);
        // default to the customer's discount %
        $_POST['Disc'] = percent_format($order->default_discount * 100);
    }

    qty_cells(null, 'qty', $_POST['qty'], null, null, $dec);



//    label_cell($units, '', 'units');

    amount_cells(null, 'price');

    small_amount_cells(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());

    $line_total = input_num('qty') * input_num('price') * (1 - input_num('Disc') / 100);

    amount_cell($line_total, false, '','line_total');


    if ($id != -1) {
        button_cell('UpdateItem', _("Update"),
            _('Confirm changes'), ICON_UPDATE);
        button_cell('CancelItemChanges', _("Cancel"),
            _('Cancel changes'), ICON_CANCEL);
        hidden('LineNo', $line_no);
        set_focus('qty');
    } else {
        submit_cells('AddDiscount', _("Add Item"), "colspan=2 align='center'",
            _('Add new item to document'), true);
    }

    end_row();
}
//--------------------------------------------------------------------------------
