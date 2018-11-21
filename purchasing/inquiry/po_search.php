<?php
$page_security = 'SA_GRN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

include($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Search Outstanding Purchase Orders"), false, false, "", $js);

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

transaction_list_cells(_("Transaction type:"), 'transaction_type', null, "", "", true);

  
    ref_cells(_("PO #:"), 'po_no', '',null, '', true);
ref_cells(_("Reference:"), 'order_number', '',null, '', true);

if(get_post('transaction_type')==1){

ref_cells(_("Bill Of Lading#:"), 'receive_ref', '',null, '', true);
ref_cells(_("LC Reference#:"), 'lc_ref', '',null, '', true);

  
}

date_cells(_("from:"), 'OrdersAfterDate', '', null, -user_transaction_days());
date_cells(_("to:"), 'OrdersToDate');

locations_list_cells(_("Location:"), 'StockLocation', null, true);
end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(_("Item:"), 'SelectStockFromList', null, true);

supplier_list_cells(_("Select a supplier: "), 'supplier_id', null, true, true);

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
end_row();
end_table(1);
if (isset($_POST['order_number']) && ($_POST['order_number'] != ""))
{
	$order_number = $_POST['order_number'];
}





if (isset($_POST['receive_ref'])  && ($_POST['receive_ref'] != ""))
{
	$lading_no = $_POST['receive_ref'];
}

if (isset($_POST['lc_ref'])&& ($_POST['lc_ref'] != ""))
{
	$lc_ref = $_POST['lc_ref'];
}

if (isset($_POST['po_no'])  && ($_POST['po_no'] != ""))
{
	$po_number = $_POST['po_no'];
}


if (isset($_POST['SelectStockFromList']) && ($_POST['SelectStockFromList'] != "") &&
	($_POST['SelectStockFromList'] != $all_items))
{
 	$selected_stock_item = $_POST['SelectStockFromList'];
}
else
{
	unset($selected_stock_item);
}

//---------------------------------------------------------------------------------------------
function trans_view($trans)
{
	return get_trans_view_str(ST_PURCHORDER, $trans["order_no"]);
}

function edit_link($row) 
{
	return trans_editor_link(ST_PURCHORDER, $row["order_no"]);
}

function prt_link($row)
{
    global $SysPrefs;
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


function receive_link($row)
{
    global $SysPrefs;
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
        if ($row['approval'] == 0)
        {
            return pager_link(_("Receive"),
                "/purchasing/po_receive_items.php?PONumber=" . $row["order_no"], ICON_RECEIVE);
        }
        elseif ($row['approval'] == 1)
            return '';
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1)
    {
            if ($row['approval'] == 0)
        {
            return pager_link(_("Receive"),
                "/purchasing/po_receive_items.php?PONumber=" . $row["order_no"], ICON_RECEIVE);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 0) {
        return pager_link(_("Receive"),
            "/purchasing/po_receive_items.php?PONumber=" . $row["order_no"], ICON_RECEIVE);
    }
    else
        {
        if ($row['approval'] == 0)
        {
            return pager_link(_("Receive"),
                "/purchasing/po_receive_items.php?PONumber=" . $row["order_no"], ICON_RECEIVE);
        }
        elseif($row['approval'] == 1)
            return '';
    }
}

function check_overdue($row)
{
	return $row['OverDue']==1;
}
//==============================================================================
function custom_checkbox($label, $name, $value=null, $submit_on_change=false, $title=false)
{
    global $Ajax;

    $str = '';

    if ($label)
        $str .= $label . "  ";
    if ($submit_on_change !== false) {
        if ($submit_on_change === true)
            $submit_on_change =
                "JsHttpRequest.request(\"_{$name}_update\", this.form);";
    }
    if ($value === null)
        $value = get_post($name, 0);

    $str .= "<input class='sendSms'"
        .($value == 1 ? ' checked':'')
        ." type='checkbox' name='$name' value='1'"
        .($submit_on_change ? " onclick='$submit_on_change'" : '')
        .($title ? " title='$title'" : '')
        ." >\n";

    $Ajax->addUpdate($name, $name, $value);
    return $str;
}
function purch_checkbox($row)
{
   /* $name = "Sel_" .$row['order_no'];
    $hidden = 'last['.$row['order_no'].']';
    $value = $row['approval'];
    return checkbox_new(null, $name, $value, false,
        _('Approve This Task'))
    .hidden($hidden, $value, false);*/

    $name = "Sel_" .$row['order_no'];
    $hidden = 'last['.$row['order_no'].']';
//	$value = $row['approval'];
    if($row['approval'] == 0)
        $value = 1;
    elseif($row['approval'] == 1)
        $value = 0;

    return custom_checkbox(null, $name, $value, false,
        _('Approve This Task'))
    .hidden($hidden, $value, false);
}
if (isset($_POST['BatchInvoice']))
{
    foreach($_POST['last'] as $id => $value) {
//		display_error($value."++".$id);
        $checkbox = 'Sel_'.$id;
        $inactive = check_value($checkbox);
        if($inactive == 0)
            $active = 1;
        elseif($inactive == 1)
            $active = 0;
//		display_error($value."++".$id);
//		if (!check_value('Sel_'.$id))
//			update_check($id, 0);
//		if (check_value('Sel_'.$id))
        update_check_purch($id, $active);

    }
}
//------------------------------------------------------------------------------
if ($SysPrefs->hide_prices_grn() == 1 && get_post('transaction_type')==0)
{
    $sql = get_sql_for_po_search2(get_post('OrdersAfterDate'), get_post('OrdersToDate'), get_post('supplier_id'), get_post('StockLocation'),
        $_POST['order_number'], get_post('SelectStockFromList'));

//$result = db_query($sql,"No orders were returned");

    /*show a table of the orders returned by the sql */
    $cols = array(
        _("#") => array('fun' => 'trans_view', 'ord' => ''),
        _("Reference"),
        _("Supplier") => array('ord' => ''),
        _("Location"),
        _("Supplier's Reference"),
        _("Order Date") => array('name' => 'ord_date', 'type' => 'date', 'ord' => 'desc'),
        _("Currency") => array('align' => 'center'));
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('BatchInvoice', _("Approved P.O"), false, _("Approved Purchase Order"))
            => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    else
    {
        array_append($cols, array(
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }

}
elseif ($SysPrefs->hide_prices_grn() == 1 && get_post('transaction_type')==1)
{
    $sql = get_sql_for_po_search_outstanding();

//$result = db_query($sql,"No orders were returned");

/*show a table of the orders returned by the sql */
$cols = array(
		_("PO #"), 
		_("LC Reference"), 
        _("Bill Of Lading No."), 
        _("Item Description"), 
        _("Supplier") => array('ord'=>''),
		_("Location"),
		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Arrival Date") => array('name'=>'arrival_date', 'type'=>'date',  'ord'=>'desc'),
          _("Qty Due") , 

		_("Currency") => array('align'=>'center'), 
//		_("Order Total") => 'amount',

);
      if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('BatchInvoice', _("Approved P.O"), false, _("Approved Purchase Order"))
            => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    else
    {
        array_append($cols, array(
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    
}
elseif ($SysPrefs->hide_prices_grn() != 1 && get_post('transaction_type')==0)
{
    
    $sql = get_sql_for_po_search(get_post('OrdersAfterDate'), get_post('OrdersToDate'), get_post('supplier_id'), get_post('StockLocation'),
	$_POST['order_number'], get_post('SelectStockFromList'));

//$result = db_query($sql,"No orders were returned");

/*show a table of the orders returned by the sql */
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''),
		_("Reference"),
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Supplier's Reference"),
		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Currency") => array('align'=>'center'),
	);
		
		
			if(!user_check_access('SA_SUPPPRICES')) {
			    
			     array_append($cols, array(
			    
			    	_("Order Total") => 'amount',));
			}
		
		
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('BatchInvoice', _("Approved P.O"), false, _("Approved Purchase Order"))
            => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    else
        {
            array_append($cols, array(
                /*_("") .
                submit('BatchInvoice', _("Add to Purchase"), false, _("Batch Update"))
                => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center')*/
                array('insert' => true, 'fun' => 'edit_link'),
                array('insert' => true, 'fun' => 'prt_link'),
                array('insert' => true, 'fun' => 'receive_link')));
        }
}
elseif ($SysPrefs->hide_prices_grn() != 1 && get_post('transaction_type')==1)
{
    
   $sql = get_sql_for_po_search_outstanding();

//$result = db_query($sql,"No orders were returned");

/*show a table of the orders returned by the sql */
$cols = array(
		_("PO #") => array('fun'=>'trans_view', 'ord'=>''), 
		_("LC Reference"), 
        _("Bill Of Lading No."), 
        _("Item Description"), 
        _("Supplier") => array('ord'=>''),
		_("Location"),
		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Arrival Date") => array('name'=>'arrival_date', 'type'=>'date',  'ord'=>'desc'),
          _("Qty Due") , 

		_("Currency") => array('align'=>'center'), 

	);
		
			if(!user_check_access('SA_SUPPPRICES')) {
			    
			     array_append($cols, array(
			    
			    	_("Order Total") => 'amount',));
			}
		
		
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('BatchInvoice', _("Approved P.O"), false, _("Approved Purchase Order"))
            => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    else
        {
            array_append($cols, array(
                /*_("") .
                submit('BatchInvoice', _("Add to Purchase"), false, _("Batch Update"))
                => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center')*/
                array('insert' => true, 'fun' => 'edit_link'),
                array('insert' => true, 'fun' => 'prt_link'),
                array('insert' => true, 'fun' => 'receive_link')));
        }
}
else{
    
    
     $sql = get_sql_for_po_search(get_post('OrdersAfterDate'), get_post('OrdersToDate'), get_post('supplier_id'), get_post('StockLocation'),
	$_POST['order_number'], get_post('SelectStockFromList'));

//$result = db_query($sql,"No orders were returned");

/*show a table of the orders returned by the sql */
$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>''),
		_("Reference"),
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Supplier's Reference"),
		_("Order Date") => array('name'=>'ord_date', 'type'=>'date', 'ord'=>'desc'),
		_("Currency") => array('align'=>'center'),
	);
		
		
			if(!user_check_access('SA_SUPPPRICES')) {
			    
			     array_append($cols, array(
			    
			    	_("Order Total") => 'amount',));
			}
		
		
		
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL2') && $SysPrefs->purch_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('BatchInvoice', _("Approved P.O"), false, _("Approved Purchase Order"))
            => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'edit_link'),
            array('insert' => true, 'fun' => 'prt_link'),
            array('insert' => true, 'fun' => 'receive_link')));
    }
    else
        {
            array_append($cols, array(
                /*_("") .
                submit('BatchInvoice', _("Add to Purchase"), false, _("Batch Update"))
                => array('insert' => true, 'fun' => 'purch_checkbox', 'align' => 'center')*/
                array('insert' => true, 'fun' => 'edit_link'),
                array('insert' => true, 'fun' => 'prt_link'),
                array('insert' => true, 'fun' => 'receive_link')));
        }
    
    
}


if (get_post('StockLocation') != ALL_TEXT) {
	$cols[_("Location")] = 'skip';
}

$table =& new_db_pager('orders_tbl', $sql, $cols);
$table->set_marker('check_overdue', _("Marked orders have overdue items."));

$table->width = "80%";

display_db_pager($table);

end_form();
end_page();
?>
<script type="text/javascript">
    // asad 15-07-2015
    function checkAll(ele) {

        var checkboxes =  '';
        checkboxes = document.getElementsByClassName('sendSms');

        if(checkboxes != '')
        {
            if (ele.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = true;
                    }//if
                }//for
            }//if
            else {
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = false;
                    }//if
                }//for
            }//else
        } // if(checkboxes != '')
    }
    <!--</script>-->