<?php

$page_security = 'SA_PURCHASEPRICING';

if (@$_GET['page_level'] == 1)
	$path_to_root = "../..";
else	
	$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

$js = "";
if ($SysPrefs->use_popup_windows && $SysPrefs->use_popup_search)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "Supplier Purchasing Data"), false, false, "", $js);

check_db_has_purchasable_items(_("There are no purchasable inventory items defined in the system."));
check_db_has_suppliers(_("There are no suppliers defined in the system."));

//----------------------------------------------------------------------------------------
simple_page_mode(true);
if (isset($_GET['stock_id']))
{
	$_POST['stock_id'] = $_GET['stock_id'];
}

//--------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
    $pref=get_company_prefs();
   	$input_error = 0;
   	if ($_POST['stock_id'] == "" || !isset($_POST['stock_id']))
   	{
      	$input_error = 1;
      	display_error( _("There is no item selected."));
		set_focus('stock_id');
   	}
   	elseif (!check_num('price', 0))
   	{
      	$input_error = 1;
      	display_error( _("The price entered was not numeric."));
	set_focus('price');
   	}

   	elseif(input_num('discount') < 0 || input_num('discount') > 100 && $pref['disc_in_amount'] == 0 ){

		$input_error = 1;

		display_error("Discount should be positive & less than 100 % ");
		set_focus('discount');
	}

   	elseif (!check_num('conversion_factor'))
   	{
      	$input_error = 1;
      	display_error( _("The conversion factor entered was not numeric. The conversion factor is the number by which the price must be divided by to get the unit price in our unit of measure."));
		set_focus('conversion_factor');
   	}
   	elseif ($Mode == 'ADD_ITEM' && get_item_purchasing_data($_POST['supplier_id'], $_POST['stock_id']))
   	{
      	$input_error = 1;
      	display_error( _("The purchasing data for this supplier has already been added."));
		set_focus('supplier_id');
	}
	if ($input_error == 0)
	{
     	if ($Mode == 'ADD_ITEM') 
       	{
			add_item_purchasing_data($_POST['supplier_id'], $_POST['stock_id'], input_num('price',0),
				$_POST['suppliers_uom'], input_num('conversion_factor'), $_POST['supplier_description'],	input_num('discount'));
    		display_notification(_("This supplier purchasing data has been added."));
       	} 
       	else
       	{
       		update_item_purchasing_data($selected_id, $_POST['stock_id'], input_num('price',0),
       			$_POST['suppliers_uom'], input_num('conversion_factor'), $_POST['supplier_description'], input_num('discount'));
    	  	display_notification(_("Supplier purchasing data has been updated."));
       	}
		$Mode = 'RESET';
	}
}

//--------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_item_purchasing_data($selected_id, $_POST['stock_id']);
	display_notification(_("The purchasing data item has been sucessfully deleted."));
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
}

if (isset($_POST['_selected_id_update']) )
{
	$selected_id = $_POST['selected_id'];
	$Ajax->activate('_page_body');
}

if (list_updated('stock_id')) 
	$Ajax->activate('price_table');
//--------------------------------------------------------------------------------------------------

$action = $_SERVER['PHP_SELF'];
if ($page_nested)
	$action .= "?stock_id=".get_post('stock_id');
start_form(false, false, $action);

if (!isset($_POST['stock_id']))
	$_POST['stock_id'] = get_global_stock_item();

if (!$page_nested)
{
	echo "<center>" . _("Item:"). "&nbsp;";
	// All items can be purchased
	echo stock_items_list('stock_id', $_POST['stock_id'], false, true);
	echo "<hr></center>";
}
else
	br(2);

set_global_stock_item($_POST['stock_id']);

$mb_flag = get_mb_flag($_POST['stock_id']);

if ($mb_flag == -1)
{
	display_error(_("Entered item is not defined. Please re-enter."));
	set_focus('stock_id');
}
else
{
	$result = get_items_purchasing_data($_POST['stock_id']);
  	div_start('price_table');
    if (db_num_rows($result) == 0)
    {
    	display_note(_("There is no purchasing data set up for the part selected"));
    }
    else
    {
        start_table(TABLESTYLE, "width='65%'");
        $pref=get_company_prefs();

   if( $pref['disc_in_amount'] == 1) {
       $th = array(_("Supplier"), _("Price"), _("Discount"), _("Currency"),
           _("Supplier's Unit"), _("Conversion Factor"), _("Supplier's Description"), "", "");
   }
   else{
       $th = array(_("Supplier"), _("Price"), _("Discount %"), _("Currency"),
           _("Supplier's Unit"), _("Conversion Factor"), _("Supplier's Description"), "", "");
   }

        table_header($th);

        $k = $j = 0; //row colour counter

        while ($myrow = db_fetch($result))
        {
			alt_table_row_color($k);

            label_cell($myrow["supp_name"]);
            amount_decimal_cell($myrow["price"]);
            amount_cell($myrow["discount"]);
            label_cell($myrow["curr_code"]);
            label_cell($myrow["suppliers_uom"]);
            qty_cell($myrow['conversion_factor'], false, 'max');
            label_cell($myrow["supplier_description"]);
		 	edit_button_cell("Edit".$myrow['supplier_id'], _("Edit"));
		 	delete_button_cell("Delete".$myrow['supplier_id'], _("Delete"));
            end_row();

            $j++;
            If ($j == 12)
            {
            	$j = 1;
        		table_header($th);
            } //end of page full new headings
        } //end of while loop

        end_table();
    }
 div_end();
}

//-----------------------------------------------------------------------------------------------

$dec2 = 6;
if ($Mode =='Edit')
{
	$myrow = get_item_purchasing_data($selected_id, $_POST['stock_id']);

    $supp_name = $myrow["supp_name"];
    $_POST['price'] = price_decimal_format($myrow["price"], $dec2);
    $_POST['discount'] = $myrow["discount"];
    $_POST['suppliers_uom'] = $myrow["suppliers_uom"];
    $_POST['supplier_description'] = $myrow["supplier_description"];
    $_POST['conversion_factor'] = maxprec_format($myrow["conversion_factor"]);
}

br();
hidden('selected_id', $selected_id);

start_table(TABLESTYLE2);

if ($Mode == 'Edit')
{
	hidden('supplier_id');
	label_row(_("Supplier:"), $supp_name);
}
else
{
	supplier_list_row(_("Supplier:"), 'supplier_id', null, false, true);
	$_POST['price'] = $_POST['discount'] = $_POST['suppliers_uom'] = $_POST['conversion_factor'] = $_POST['supplier_description'] = "";
}
amount_row(_("Price:"), 'price', null,'', get_supplier_currency($selected_id), $dec2);
if($pref['disc_in_amount'] == 1)
amount_row(_("Discount:"), 'discount', null,'' );
else
    amount_row(_("Discount % :"), 'discount', null,'' );

    text_row(_("Suppliers Unit of Measure:"), 'suppliers_uom', null, 50, 51);

if (!isset($_POST['conversion_factor']) || $_POST['conversion_factor'] == "")
{
   	$_POST['conversion_factor'] = maxprec_format(1);
}
amount_row(_("Conversion Factor (to our UOM):"), 'conversion_factor', null, null, null, 'max');
text_row(_("Supplier's Code or Description:"), 'supplier_description', null, 50, 51);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
end_page();
