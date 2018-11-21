<?php



$page_security = 'SA_SALESPRICE';
if (!@$_GET['popup'])
    $path_to_root = "..";
else
    $path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/inventory/includes/db/bonus_quantity_db.inc");

if (!@$_GET['popup'])
    page(_($help_context = "Inventory Item Sales prices"));

//---------------------------------------------------------------------------------------------------

check_db_has_stock_items(_("There are no items defined in the system."));

check_db_has_sales_types(_("There are no sales types in the system. Please set up sales types befor entering pricing."));

simple_page_mode(true);
//---------------------------------------------------------------------------------------------------
$input_error = 0;

if (isset($_GET['stock_id']))
{
    $_POST['stock_id'] = $_GET['stock_id'];
}
if (isset($_GET['Item']))
{
    $_POST['stock_id'] = $_GET['Item'];
}

if (!isset($_POST['curr_abrev']))
{
    $_POST['curr_abrev'] = get_company_currency();
}

//---------------------------------------------------------------------------------------------------

$action = $_SERVER['PHP_SELF'];
if (@$_GET['popup'])
    $action .= "?stock_id=".get_post('stock_id');
start_form(false, false, $action);

if (!isset($_POST['stock_id']))
    $_POST['stock_id'] = get_global_stock_item();

if (!@$_GET['popup'])
{
    echo "<center>" . _("Item:"). "&nbsp;";
    echo sales_items_list('stock_id', $_POST['stock_id'], false, true, '', array('editable' => false));
    echo "<hr></center>";
}
else
    br(2);
set_global_stock_item($_POST['stock_id']);

//----------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

//	if (!check_num('qty', 0))
//	{
//		$input_error = 1;
//		display_error( _("The price entered must be numeric."));
//		set_focus('qty');
//	}
//   	elseif ($Mode == 'ADD_ITEM' && get_stock_price_type_currency($_POST['stock_id'], $_POST['sales_type_id'], $_POST['curr_abrev'],$_POST['customer_id']))
//   	{
//      	$input_error = 1;
//      	display_error( _("The sales pricing for this item, sales type and currency has already been added."));
//		set_focus('supplier_id');
//	}

    if ($input_error != 1)
    {

        if ($selected_id != -1)
        {
            //editing an existing price
            update_bonus_qty($selected_id, $_POST['minimum_qty'],$_POST['maximum_qty'],
                $_POST['bonus_qty']);

            $msg = _("This Quantity has been updated.");
        }
        else
        {

            add_bonus_qty($_POST['stock_id'], $_POST['minimum_qty'],$_POST['maximum_qty'],
                $_POST['bonus_qty']);

            $msg = _("The new Quantity has been added.");
        }
        display_notification($msg);
        $Mode = 'RESET';
    }

}

//------------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
    //the link to delete a selected record was clicked
    delete_bonus($selected_id);
    display_notification(_("The selected Field has been deleted."));
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
}

if (list_updated('stock_id')) {
    $Ajax->activate('price_table');
    $Ajax->activate('price_details');
}
if (list_updated('stock_id') || isset($_POST['_curr_abrev_update']) || isset($_POST['_sales_type_id_update'])|| isset($_POST['_customer_id_update'])) {
    // after change of stock, currency or salestype selector
    // display default calculated price for new settings.
    // If we have this price already in db it is overwritten later.
    unset($_POST['price']);
    $Ajax->activate('price_details');
}

//---------------------------------------------------------------------------------------------------
$prices_list = get_bonus($_POST['stock_id']);



div_start('price_table');
start_table(TABLESTYLE, "width=30%");

$th = array(("Minimum Quantity"),_("Maximum Quantity"), _("Bonus Quantity"), "", "");
table_header($th);
$k = 0; //row colour counter
$calculated = false;
while ($myrow = db_fetch($prices_list))
{
//
    alt_table_row_color($k);


    label_cell($myrow["minimum_qty"]);
    label_cell($myrow["maximum_qty"]);
    label_cell($myrow["bonus_qty"]);
//    label_cell (get_customer_name($myrow["customer_id"]));

//    amount_cell($myrow["price"]);
    edit_button_cell("Edit".$myrow['id'], _("Edit"));
    delete_button_cell("Delete".$myrow['id'], _("Delete"));
    end_row();

}
end_table();
if (db_num_rows($prices_list) == 0)
{
//	if (get_company_pref('add_pct') != -1)
//		$calculated = true;
//	display_note(_("There are no prices set up for this part."), 1);
}
div_end();
//------------------------------------------------------------------------------------------------

echo "<br>";

if ($Mode == 'Edit')
{
    $myrow = get_bonus1($selected_id);
    $_POST['minimum_qty'] = $myrow["minimum_qty"];
    $_POST['maximum_qty'] = $myrow["maximum_qty"];
    $_POST['bonus_qty'] = $myrow["bonus_qty"];
//	$_POST['customer_id'] = $myrow["customer_id"];

//    $_POST['price'] = price_format($myrow["price"]);
}

hidden('selected_id', $selected_id);
if (@$_GET['popup'])
{
    hidden('_tabs_sel', get_post('_tabs_sel'));
    hidden('popup', @$_GET['popup']);
}
div_start('price_details');
start_table(TABLESTYLE2);
small_amount_row(_("Minimum Qty:"), 'minimum_qty', null);
small_amount_row(_("Maximum Qty:"), 'maximum_qty', null);
small_amount_row(_("Bonus Qty:"), 'bonus_qty', null);

end_table(1);
if ($calculated)
    display_note(_("The price is calculated."), 0, 1);

submit_add_or_update_center($selected_id == -1, '', 'both');
div_end();

end_form();
if (!@$_GET['popup'])
    end_page(@$_GET['popup'], false, false);
?>
