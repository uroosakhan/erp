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
page(_($help_context = "Search Outstanding Import GRNs "), false, false, "", $js);

//------------------------------------------------------------------------------
function trans_view($trans)
{
	return get_trans_view_str(ST_SUPPRECEIVE, $trans["id"]);
}


function gl_view($row)
{
    return get_gl_view_str(ST_SUPPRECEIVE, $row["id"]);
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
function batch_checkbox($row)
{
    $name = "Sel_" .$row['id'];
    return $row['Done'] ? '' :
        "<input type='checkbox' name='$name' value='1' >"
// add also trans_no => branch code for checking after 'Batch' submit
        ."<input name='Sel_[".$row['id']."]' type='hidden' value='"
        .$row['supplier_id']."'>\n";
}
if (isset($_POST['BatchInvoice']))
{
    // checking batch integrity
    $del_count = 0;
    foreach($_POST['Sel_'] as $delivery => $branch) {
        $checkbox = 'Sel_'.$delivery;
        if (check_value($checkbox))	{
            if (!$del_count) {
                $del_branch = $branch;
            }
            else {
                if ($del_branch != $branch)	{
                    $del_count=0;
                    break;
                }
            }
            $selected[] = $delivery;
            $del_count++;
        }
    }

    if (!$del_count) {
        display_error(_('For invoice process you should select at least one transaction. All items must be invoiced to the same Supplier.'));
    } else {
        $_SESSION['ImportBatch'] = $selected;
       meta_forward($path_to_root . "/purchasing/supplier_invoice_import_reg.php","New=1&supplier_id=".$del_branch);
    }
}
//---------------------------------------------------------------------------------------------

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("#:"), 'order_number', '',null, '', true);

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

//---------------------------------------------------------------------------------------------

$sql = get_sql_for_grn_search_completed(get_post('OrdersAfterDate'), get_post('OrdersToDate'),
	get_post('supplier_id'), get_post('StockLocation'), get_post('order_number'),
	get_post('SelectStockFromList'));

$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
		_("Reference"),
        _("Item Code") ,
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Delivery Date") => array('type'=>'date', 'ord'=>'desc'),
    submit('BatchInvoice',_("Process"), false, _("Process"))
    => array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
    array('insert'=>true, 'fun'=>'gl_view'),
    array('insert'=>true, 'fun'=>'receive_link'),
//		array('insert'=>true, 'fun'=>'prt_link'),
);

if (get_post('StockLocation') != ALL_TEXT) {
	$cols[_("Location")] = 'skip';
}

//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();