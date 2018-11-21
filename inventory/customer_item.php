<?php

$page_security = 'SA_ITEM';

if (@$_GET['page_level'] == 1)
	$path_to_root = "../..";
else
	$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/inventory/includes/db/items_customer_db.inc");

$js = "";
if ($SysPrefs->use_popup_windows && $SysPrefs->use_popup_search)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "Customer  Data"), false, false, "", $js);

//check_db_has_purchasable_items(_("There are no purchasable inventory items defined in the system."));
//check_db_has_suppliers(_("There are no suppliers defined in the system."));

//----------------------------------------------------------------------------------------
simple_page_mode(true);
if (isset($_GET['stock_id']))
{
	$_POST['stock_id'] = $_GET['stock_id'];
}

//--------------------------------------------------------------------------------------------------
function get_customersss($stock_id, $customer_id)
{
    $sql = "SELECT COUNT(*) TotalRecords  FROM ".TB_PREF."item_customers WHERE stock_id=".db_escape($stock_id)." AND 
            customer_id=".db_escape($customer_id);

    $result = db_query($sql, "could not get suppliers");

    $row = db_fetch_row($result);

    return $row[0];
}
$customersss = get_customersss($_POST['stock_id'], $_POST['customer_id']);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
     $input_error = 0;
    if ($customersss != 0)
    {
        $input_error = 1;
        display_error(_("The Customer Already Exists."));
        set_focus('customer_id');
    }
    
//    $pref=get_company_prefs();
//   	if ($_POST['stock_id'] == "" || !isset($_POST['stock_id']))
//   	{
//      	$input_error = 1;
//      	display_error( _("There is no item selected."));
//		set_focus('stock_id');
//   	}
//   	elseif (!check_num('price', 0))
//   	{
//      	$input_error = 1;
//      	display_error( _("The price entered was not numeric."));
//	set_focus('price');
//   	}
//
//   	elseif(input_num('discount') < 0 || input_num('discount') > 100 && $pref['disc_in_amount'] == 0 ){
//
//		$input_error = 1;
//
//		display_error("Discount should be positive & less than 100 % ");
//		set_focus('discount');
//	}
//
//   	elseif (!check_num('conversion_factor'))
//   	{
//      	$input_error = 1;
//      	display_error( _("The conversion factor entered was not numeric. The conversion factor is the number by which the price must be divided by to get the unit price in our unit of measure."));
//		set_focus('conversion_factor');
//   	}
//   	elseif ($Mode == 'ADD_ITEM' && get_item_purchasing_data($_POST['supplier_id'], $_POST['stock_id']))
//   	{
//      	$input_error = 1;
//      	display_error( _("The purchasing data for this supplier has already been added."));
//		set_focus('supplier_id');
//	}
	if ($input_error == 0)
	{
     	if ($Mode == 'ADD_ITEM') 
       	{
			add_item_customer_data($_POST['customer_id'], $_POST['stock_id'], $_POST['price']);
    		display_notification(_("This Customer data has been added."));
       	} 
       	else
       	{
       		update_item_customer_data($selected_id, $_POST['stock_id'], $_POST['customer_id'], $_POST['price']);
    	  	display_notification(_("Customer data has been updated."));
       	}
		$Mode = 'RESET';
	}
}

//--------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	delete_item_customer_data($selected_id, $_POST['stock_id']);
	display_notification(_("The Customer item has been sucessfully deleted."));
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
	echo "<center>" . _(""). "&nbsp;";
	// All items can be purchased
	echo stock_categories_list_row(_("Category:"), 'stock_id', null, "Select Category",true);
	echo "<hr></center>";
}
else
	br(2);

//set_global_stock_item($_POST['stock_id']);

//$mb_flag = get_mb_flag($_POST['stock_id']);

//if ($mb_flag == -1)
//{
//	display_error(_("Entered item is not defined. Please re-enter."));
//	set_focus('stock_id');
//}
//else
//{
	$result = get_items_customer_data($_POST['stock_id']);
  	div_start('price_table');
    if (db_num_rows($result) == 0)
    {
    	display_note(_("There is no purchasing data set up for the part selected"));
    }
    else
    {
        start_table(TABLESTYLE, "width='65%'");
        $pref=get_company_prefs();


       $th = array(_("Customer")/*,_("Price")*/, "", "");

        table_header($th);

        $k = $j = 0; //row colour counter

        while ($myrow = db_fetch($result))
        {
			alt_table_row_color($k);

            label_cell($myrow["name"]);
//            amount_decimal_cell($myrow["price"]);
		 	edit_button_cell("Edit".$myrow['id'], _("Edit"));
		 	delete_button_cell("Delete".$myrow['id'], _("Delete"));
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
//}

//-----------------------------------------------------------------------------------------------

$dec2 = 6;
if ($Mode =='Edit')
{
	$myrow = get_item_customer_data($selected_id, $_POST['stock_id']);

    $cust_name = $myrow["name"];
    $_POST['customer_id'] = $myrow["customer_id"];
    $_POST['price'] = price_decimal_format(0, $dec2);

}

br();
hidden('selected_id', $selected_id);

start_table(TABLESTYLE2);

//if ($Mode == 'Edit')
//{
//	hidden('supplier_id');
//	label_row(_("Supplier:"), $supp_name);
//}
//else
//{

	customer_list_row(_("Customer:"), 'customer_id', null, false, true);
//	$_POST['price'] = "";
//}
//amount_row(_("Price:"), 'price', null,'', get_supplier_currency($selected_id), $dec2);
hidden('price',0);
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();
end_page();
