<?php

$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root="../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Search Purchase Orders"), false, false, "", $js);

//---------------------------------------------------------------------------------------------
function trans_view($trans)
{
	return get_trans_view_str(ST_PURCHORDER, $trans["order_no"]);
}

function edit_link($row) 
{
	global $page_nested;

	return $page_nested || !$row['isopen'] ? '' :
		trans_editor_link(ST_PURCHORDER, $row["order_no"]);
}

function prt_link($row)
{
    global $SysPrefs,$db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IMEC' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
    {
    if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
        if ($row['approval'] == 0) {
            return print_document_link_new($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return '';
        }
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
        if ($row['approval'] == 0)
        {
            return print_document_link_new($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0)
    {
        return print_document_link_new($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
    }
    else
    {
        if ($row['approval'] == 0)
        {
            return print_document_link_new($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    }
    else{
        
        if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
        if ($row['approval'] == 0) {
            return print_document_link($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return '';
        }
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
         if ($row['approval'] == 0)
        {
            return print_document_link($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0)
    {
        return print_document_link($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
    }
    else
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
        
        
        
        
    }
//    return print_document_link($row['order_no'], _("Print"), true, ST_PURCHORDER, ICON_PRINT);
}

if (isset($_GET['order_number']))
{
	$_POST['order_number'] = $_GET['order_number'];
}

//-----------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('SearchOrders')) 
{
	$Ajax->activate('orders_tbl');
} elseif (get_post('_order_number_changed')) 
{
	$disable = get_post('order_number') !== '';

	$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
	$Ajax->addDisable(true, 'OrdersToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	$Ajax->addDisable(true, 'SelectStockFromList', $disable);

	if ($disable) {
		$Ajax->addFocus(true, 'order_number');
	} else
		$Ajax->addFocus(true, 'OrdersAfterDate');

	$Ajax->activate('orders_tbl');
}
//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
   
transaction_list_cells2(_("Transaction type:"), 'transaction_type', null,
    "", "", "", true);

ref_cells(_("PO #:"), 'po_number', '',null, '', true);

ref_cells(_("Refrence:"), 'order_number', '',null, '', true);

if(get_post('transaction_type')==1)
{
ref_cells(_("Bill Of Lading#:"), 'receive_ref', '',null, '', true);
}

date_cells(_("from:"), 'OrdersAfterDate', '', null, -user_transaction_days());
date_cells(_("to:"), 'OrdersToDate');

locations_list_cells(_("into location:"), 'StockLocation', null, true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(_("for item:"), 'SelectStockFromList', null, true);

if (!$page_nested)
	supplier_list_cells(_("Select a supplier: "), 'supplier_id', null, true, true);

check_cells(_('Also closed:'), 'also_closed', check_value('also_closed'));

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_row();
end_table(1);


if (isset($_POST['order_number'])&& ($_POST['order_number'] != ""))
{
	$order_number = $_POST['order_number'];
}

if (isset($_POST['receive_ref'])&& ($_POST['receive_ref'] != ""))
{
	$lading_no = $_POST['receive_ref'];
} 


if (isset($_POST['po_number'])&& ($_POST['po_number'] != ""))
{
	$po_number = $_POST['po_number'];
}
if (isset($_POST['transaction_type'])&& ($_POST['transaction_type'] != ""))
{
    $transaction_type = $_POST['transaction_type'];
}






if (isset($_POST['supplier_id']))
{
	$supplier_id = $_POST['supplier_id'];
}

if (isset($_POST['SelectStockFromList']) &&	($_POST['SelectStockFromList'] != "") &&
	($_POST['SelectStockFromList'] != ALL_TEXT))
{
 	$selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
	unset($selected_stock_item);
}
//---------------------------------------------------------------------------------------------
//if(get_post('transaction_type')==0){
//$sql = get_sql_for_po_search_completed_local(get_post('OrdersAfterDate'), get_post('OrdersToDate'),
//	get_post('supplier_id'), get_post('StockLocation'), get_post('order_number'),
//	get_post('SelectStockFromList'), get_post('also_closed'), get_post('po_number'));
//
//$cols = array(
//		_("PO #") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
//		_("Reference"),
//		_("Supplier") => array('ord'=>''),
//		_("Location"),
//		_("Supplier's Reference"),
//		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
//		_("Currency") => array('align'=>'center'),
//// 		_("Order Total") => 'amount',
//// 		array('insert'=>true, 'fun'=>'edit_link'),
//// 		array('insert'=>true, 'fun'=>'prt_link'),
//);
//
//if(!user_check_access('SA_SUPPPRICES')) {
//
//	 array_append($cols,array(
//	_("Order Total") => 'amount',));
//
//}
//
// array_append($cols,array(
//	array('insert'=>true, 'fun'=>'edit_link'),
//		array('insert'=>true, 'fun'=>'prt_link'),));
//
//if (get_post('StockLocation') != ALL_TEXT) {
//	$cols[_("Location")] = 'skip';}
//}
//elseif(get_post('transaction_type')==1){
$sql = get_sql_for_po_search_completed_import();
    $cols = array(
        _("PO#") => array('fun' => 'trans_view', 'ord' => '', 'align' => 'right'),
        _("Reference"),
        _("LC Reference"),
        _("Bill Of Lading#"),
        _("Item Description"),
        _("Supplier") => array('ord' => ''),
        _("Location"),
        _("Supplier's Reference"),
        _("Order Date") => array('name' => 'ord_date', 'type' => 'date', 'ord' => 'desc'),
        _("Arrival Date") => array('name' => 'arrival_date', 'type' => 'date', 'ord' => 'desc'),
        _("Currency") => array('align' => 'center'),
// 		_("Order Total") => 'amount',
// 		array('insert'=>true, 'fun'=>'edit_link'),
// 		array('insert'=>true, 'fun'=>'prt_link'),
    );

//elseif(get_post('transaction_type')==0) {
//    $cols = array(
//		_("PO #") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
//		_("Reference"),
//		_("Supplier") => array('ord'=>''),
//		_("Location"),
//		_("Supplier's Reference"),
//		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
//		_("Currency") => array('align'=>'center'),
//// 		_("Order Total") => 'amount',
//// 		array('insert'=>true, 'fun'=>'edit_link'),
//// 		array('insert'=>true, 'fun'=>'prt_link'),
//);
//}
//elseif(get_post('transaction_type')==2) {
//    $cols = array(
//		_("PO#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
//		_("Supplier") => array('ord'=>''),
//		_("Location"),
//		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
//// 		_("Order Total") => 'amount',
//// 		array('insert'=>true, 'fun'=>'edit_link'),
//// 		array('insert'=>true, 'fun'=>'prt_link'),
//);
//}
	if(!user_check_access('SA_SUPPPRICES')) {

	 array_append($cols,array(
	_("Order Total") => 'amount',));
    
//}
        if(get_post('transaction_type')==2)
        {
            array_remove($cols, 1);
            array_remove($cols, 1);
            array_remove($cols, 1);
            array_remove($cols, 1);
            array_remove($cols, 3);
            array_remove($cols, 4);
//            array_remove($cols, 4);
//            array_remove($cols, 4);

        }
        elseif(get_post('transaction_type')==0)
        {
            array_remove($cols, 2);
            array_remove($cols, 2);
            array_remove($cols, 2);
            array_remove($cols, 6);
        }
        elseif(get_post('transaction_type')==1)
        {
            array_remove($cols, 1);
            array_remove($cols, 6);
//            array_remove($cols, 2);
//            array_remove($cols, 6);
        }
 array_append($cols,array(
	array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link'),));

if (get_post('StockLocation') != ALL_TEXT) {
	$cols[_("Location")] = 'skip';
}
    
//}elseif(get_post('transaction_type')==2){
//$sql = get_sql_for_po_search_completed_all(get_post('OrdersAfterDate'), get_post('OrdersToDate'),
//    get_post('supplier_id'), get_post('StockLocation'), get_post('order_number'),
//    get_post('SelectStockFromList'), get_post('also_closed'), get_post('po_number'));
//
//$cols = array(
//		_("PO#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
//		_("Supplier") => array('ord'=>''),
//		_("Location"),
//		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
//// 		_("Order Total") => 'amount',
//// 		array('insert'=>true, 'fun'=>'edit_link'),
//// 		array('insert'=>true, 'fun'=>'prt_link'),
//);
//
//
//	if(!user_check_access('SA_SUPPPRICES')) {
//
//	 array_append($cols,array(
//	_("Order Total") => 'amount',));
//
//}

 array_append($cols,array(
	array('insert'=>true, 'fun'=>'edit_link'),
		array('insert'=>true, 'fun'=>'prt_link'),));

if (get_post('StockLocation') != ALL_TEXT) {
	$cols[_("Location")] = 'skip';
}

}

//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();
