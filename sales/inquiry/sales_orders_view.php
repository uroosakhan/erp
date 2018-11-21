<?php
$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");



set_page_security( @$_POST['order_view_mode'],
	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
			'InvoiceTemplates' => 'SA_SALESINVOICE',
			'DeliveryTemplates' => 'SA_SALESDELIVERY',
			'PrepaidOrders' => 'SA_SALESINVOICE'),
	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
			'InvoiceTemplates' => 'SA_SALESINVOICE',
			'DeliveryTemplates' => 'SA_SALESDELIVERY',
			'PrepaidOrders' => 'SA_SALESINVOICE')
);

if (get_post('type'))
	$trans_type = $_POST['type'];
elseif (isset($_GET['type']) && $_GET['type'] == ST_SALESQUOTE)
	$trans_type = ST_SALESQUOTE;
else
	$trans_type = ST_SALESORDER;

if ($trans_type == ST_SALESORDER)
{
	if (isset($_GET['OutstandingOnly']) && ($_GET['OutstandingOnly'] == true))
	{
		$_POST['order_view_mode'] = 'OutstandingOnly';
		$_SESSION['page_title'] = _($help_context = "Search Outstanding Sales Orders");
	}
	elseif (isset($_GET['InvoiceTemplates']) && ($_GET['InvoiceTemplates'] == true))
	{
		$_POST['order_view_mode'] = 'InvoiceTemplates';
		$_SESSION['page_title'] = _($help_context = "Search Template for Invoicing");
	}
	elseif (isset($_GET['DeliveryTemplates']) && ($_GET['DeliveryTemplates'] == true))
	{
		$_POST['order_view_mode'] = 'DeliveryTemplates';
		$_SESSION['page_title'] = _($help_context = "Select Template for Delivery");
	}
	elseif (isset($_GET['PrepaidOrders']) && ($_GET['PrepaidOrders'] == true))
	{
		$_POST['order_view_mode'] = 'PrepaidOrders';
		$_SESSION['page_title'] = _($help_context = "Invoicing Prepayment Orders");
	}
	elseif (!isset($_POST['order_view_mode']))
	{
		$_POST['order_view_mode'] = false;
		$_SESSION['page_title'] = _($help_context = "Search All Sales Orders");
	}
}
else
{
	$_POST['order_view_mode'] = "Quotations";
	$_SESSION['page_title'] = _($help_context = "Search All Sales Quotations");
}

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 600);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page($_SESSION['page_title'], false, false, "", $js);
//---------------------------------------------------------------------------------------------
//	Query format functions
//
function check_overdue($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESQUOTE)
		return (date1_greater_date2(Today(), sql2date($row['delivery_date'])));
	else
		return ($row['type'] == 0
			&& date1_greater_date2(Today(), sql2date($row['delivery_date']))
			&& ($row['TotDelivered'] < $row['TotQuantity']));
}

function view_link($dummy, $order_no)
{
	global $trans_type;
	return  get_customer_trans_view_str($trans_type, $order_no);
}
function get_dimension_string_new($row, $html=false, $space=' ')
{
    

		$row1 = get_dimension($row['dimension_id'], true);
		$dim = $row1['name'];


	return $dim;
}

function prt_link($row)
{
	global $SysPrefs,$trans_type;
	
	global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'IMEC' )
	{
	if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') 
	&& $SysPrefs->order_appr() == 1)
	{
		if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
		{
			return print_document_link_new($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
		}
		elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
		{
			return "";
		}
	}
	elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
	{
	    if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
			{
				return print_document_link_new($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
			}
			elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
			{
				return "";
			}
	}
	elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0)
	{
	return print_document_link_new($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
	}
	else
		{
			if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
			{
				return print_document_link_new($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
			}
			elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
			{
				return "";
			}
		}
		
		
	}
	elseif($trans_type == ST_SALESORDER)
	{
	    
	    	if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') 
	&& $SysPrefs->order_appr() == 1)
	{
		if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
		{
			return print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
		}
		elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
		{
			return "";
		}
	}
	elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
	{
	    	if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
			{
				return print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
			}
			elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
			{
				return "";
			}
	}
	elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0)
	{
	return print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
	}
	else
		{
			if ($trans_type == ST_SALESORDER && $row['approval'] == 0)
			{
				return print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
			}
			elseif ($trans_type == ST_SALESORDER && $row['approval'] == 1)
			{
				return "";
			}
		}
	}
	else{
	    	return print_document_link($row['order_no'], _("Print"), true, $trans_type, ICON_PRINT);
	    
	}
}


function edit_link($row)
{
    global $page_nested;

    if (is_prepaid_order_open($row['order_no'])) //ansar 26-08-2017
        return '';

    return $page_nested ? '' : trans_editor_link($row['trans_type'], $row['order_no']);
}
function dispatch_link($row)
{
	global $SysPrefs,$trans_type;
	if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
	{
		if($row['approval'] == 0)
		{
			if ($row['ord_payments'] + $row['inv_payments'] < $row['prep_amount'])
				return '';

			if ($trans_type == ST_SALESORDER)
				return pager_link( _("Dispatch"),
					"/sales/customer_delivery.php?OrderNumber=" .$row['order_no'], ICON_DOC);
			else
				return pager_link( _("Sales Order"),
					"/sales/sales_order_entry.php?OrderNumber=" .$row['order_no'], ICON_DOC);
		}
		elseif($row['approval'] == 1)
			return '';

	}
	elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
	{
	    	if($row['approval'] == 0)
			{
			if ($trans_type == ST_SALESORDER)
				return pager_link(_("Dispatch"),
					"/sales/customer_delivery.php?OrderNumber=" . $row['order_no'], ICON_DOC);
			else
				return pager_link(_("Sales Order"),
					"/sales/sales_order_entry.php?OrderNumber=" . $row['order_no'], ICON_DOC);
			}
			elseif($row['approval'] == 1)
				return '';
	}
	elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 0) {

			if ($row['ord_payments'] + $row['inv_payments'] < $row['prep_amount'])
				return '';

			if ($trans_type == ST_SALESORDER)
				return pager_link(_("Dispatch"),
					"/sales/customer_delivery.php?OrderNumber=" . $row['order_no'], ICON_DOC);
			else
				return pager_link(_("Sales Order"),
					"/sales/sales_order_entry.php?OrderNumber=" . $row['order_no'], ICON_DOC);
		}

		else{
			if($row['approval'] == 0)
			{
			if ($trans_type == ST_SALESORDER)
				return pager_link(_("Dispatch"),
					"/sales/customer_delivery.php?OrderNumber=" . $row['order_no'], ICON_DOC);
			else
				return pager_link(_("Sales Order"),
					"/sales/sales_order_entry.php?OrderNumber=" . $row['order_no'], ICON_DOC);
			}
			elseif($row['approval'] == 1)
				return '';
		}

}



function auto_delivery_link($row)
{
    global $SysPrefs,$trans_type;
  global $SysPrefs,$db_connections;
if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && ($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='VETZ'
|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO'))
{
                return pager_link(_("Dispatch"),
                    "/sales/customer_delivery.php?OrderNumber=" . $row['order_no']."&&AUTODELIVERY=1", ICON_DOC);
}

}

function invoice_link($row)
{
	global $trans_type;
	if ($trans_type == ST_SALESORDER)
  		return pager_link( _("Invoice"),
			"/sales/sales_order_entry.php?NewInvoice=" .$row["order_no"], ICON_DOC);
	else
		return '';
}

function delivery_link($row)
{
  return pager_link( _("Delivery"),
	"/sales/sales_order_entry.php?NewDelivery=" .$row['order_no'], ICON_DOC);
}

function order_link($row)
{
  return pager_link( _("Sales Order"),
	"/sales/sales_order_entry.php?NewQuoteToSalesOrder=" .$row['order_no'], ICON_DOC);
}

function tmpl_checkbox($row)
{
	global $trans_type, $page_nested;

	if ($trans_type == ST_SALESQUOTE || !check_sales_order_type($row['order_no']))
		return '';

	if ($page_nested)
		return '';
	$name = "chgtpl" .$row['order_no'];
	$value = $row['type'] ? 1:0;

// save also in hidden field for testing during 'Update'

 return checkbox(null, $name, $value, true,
 	_('Set this order as a template for direct deliveries/invoices'))
	. hidden('last['.$row['order_no'].']', $value, false);
}

function invoice_prep_link($row)
{
	// invoicing should be available only for partially allocated orders
	return 
		$row['inv_payments'] < $row['total'] ?
		pager_link($row['ord_payments']  ? _("Prepayment Invoice") : _("Final Invoice"),
		"/sales/customer_invoice.php?InvoicePrepayments=" .$row['order_no'], ICON_DOC) : '';
}

$id = find_submit('_chgtpl');
if ($id != -1)
{
	sales_order_set_template($id, check_value('chgtpl'.$id));
	$Ajax->activate('orders_tbl');
}

if (isset($_POST['Update']) && isset($_POST['last'])) {
	foreach($_POST['last'] as $id => $value)
		if ($value != check_value('chgtpl'.$id))
			sales_order_set_template($id, !check_value('chgtpl'.$id));
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

function delivery_checkbox($row)
{
	if ($row['ord_payments'] + $row['inv_payments'] < $row['prep_amount'])
		return '';
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
function update_check($reconcile_id, $reconcile_value)
{
	$sql = "UPDATE ".TB_PREF."sales_orders SET approval = '$reconcile_value'
			WHERE order_no = ".db_escape($reconcile_id);
	db_query($sql, "Can't approve task");
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
			update_check($id, $active);

	}
	meta_forward($_SERVER['PHP_SELF'], "OutstandingOnly=1");
}
//==============================================================================
$show_dates = !in_array($_POST['order_view_mode'], array('InvoiceTemplates', 'DeliveryTemplates'));
//---------------------------------------------------------------------------------------------
//	Order range form
//
if (get_post('_OrderNumber_changed') || get_post('_OrderReference_changed')) // enable/disable selection controls
{
	$disable = get_post('OrderNumber') !== '' || get_post('OrderReference') !== '';

  	if ($show_dates) 
{
		$Ajax->addDisable(true, 'OrdersAfterDate', $disable);
		$Ajax->addDisable(true, 'OrdersToDate', $disable);
	}

	$Ajax->activate('orders_tbl');
}

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("Order #:"), 'OrderNumber', '',null, '', true);
ref_cells(_("Ref"), 'OrderReference', '',null, '', true);
if ($show_dates)
{
  	date_cells(_("Order Date:"), 'OrdersAfterDate', '', null, -205);
  	date_cells(_("Required Date:"), 'OrdersToDate', '', null, 159);
}
locations_list_cells(_("Location:"), 'StockLocation', null, true, true);

if($show_dates) {
	end_row();
	end_table();

	start_table(TABLESTYLE_NOBORDER);
	start_row();
}
stock_items_list_cells(_("Item:"), 'SelectStockFromList', null, true, true);

if (!$page_nested)
	{
	customer_list_cells(_("Select a customer: "), 'customer_id', null, true, true);

	customer_branches_list_cells(_("Branch:"),
	$_POST['customer_id'], 'branch_id', null, true, true);
	    
	}
if ($trans_type == ST_SALESQUOTE)
	check_cells(_("Show All:"), 'show_all');
		dimensions_list_cells(_("Dimension")." 1:", 'Dimension', null, true, " ", false, 1);
	

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
hidden('order_view_mode', $_POST['order_view_mode']);
hidden('type', $trans_type);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------
//	Orders inquiry table
//
	if (isset($_GET['OutstandingOnly']) && ($_GET['OutstandingOnly'] == true))
    {
hyperlink_params("$path_to_root/sales/inquiry/sales_orders_view_itemised.php?OutstandingOnly=1", _("Itemised Inquiry"), "filterType=$trans_type");
}
else
    {
hyperlink_params("$path_to_root/sales/inquiry/sales_orders_view_itemised.php", _("Itemised Inquiry"), "filterType=$trans_type");
}

$sql = get_sql_for_sales_orders_view($trans_type, get_post('OrderNumber'), get_post('order_view_mode'),
	get_post('SelectStockFromList'), get_post('OrdersAfterDate'), get_post('OrdersToDate'), get_post('OrderReference'), get_post('StockLocation'),
	get_post('customer_id'),get_post('branch_id'),get_post('Dimension'));

if ($trans_type == ST_SALESORDER)
	$cols = array(
		_("Order #") => array('fun'=>'view_link', 'ord' => '','align'=>'right'),
		_("Ref") => array('type' => 'sorder.reference', 'ord' => '') ,
		_("Customer") => array('type' => 'debtor.name' , 'ord' => '') ,
		_("Branch"), 
		_("Cust Order Ref"),
		_("Order Date") => array('type' =>  'date', 'ord' => ''),
		_("Required By") =>array('type'=>'date', 'ord'=>''),
		_("Delivery To"), 
		_("Order Total") => array('type'=>'amount', 'ord'=>''),
		'Type' => 'skip',
		_("Currency") => array('align'=>'center'),
		_("Order No")=>	array('insert'=>true, 'fun'=>'get_dimension_string_new')
	);
else
	$cols = array(
		_("Quote #") => array('fun'=>'view_link', 'ord' => '','align'=>'right'),
		_("Ref"),
		_("Customer"),
		_("Branch"), 
		_("Cust Order Ref"),
		_("Quote Date") => 'date',
		_("Valid until") =>array('type'=>'date', 'ord'=>''),
		_("Delivery To"), 
		_("Quote Total") => array('type'=>'amount', 'ord'=>''),
		'Type' => 'skip',
		_("Currency") => array('align'=>'center'),
		_("Order No")=>array('insert'=>true, 'fun'=>'get_dimension_string_new')
	);
 	if ($trans_type == ST_SALESORDER)
 	{
// array_append($cols, array(
//      ("Invoice")  =>  array('insert'=>true, 'fun'=>'auto_delivery_link')
//     ));
}
	
	
if ($_POST['order_view_mode'] == 'OutstandingOnly') {
if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL') && $SysPrefs->order_appr() == 1)
{
	array_append($cols, array(
		_("").
		submit('BatchInvoice',_("Approval"), false, _("Batch Update"))
		=> array('insert'=>true, 'fun'=>'delivery_checkbox', 'align'=>'center'),
		array('insert'=>true, 'fun'=>'dispatch_link'),
		array('insert'=>true, 'fun'=>'edit_link')));
}
else
	{
		array_append($cols, array(
			/*_("").
			submit('BatchInvoice',_("Add to Delivery"), false, _("Batch Update"))
			=> array('insert'=>true, 'fun'=>'delivery_checkbox', 'align'=>'center'),*/
			array('insert'=>true, 'fun'=>'dispatch_link'),
			array('insert'=>true, 'fun'=>'edit_link')));
	}

}elseif ($_POST['order_view_mode'] == 'InvoiceTemplates') {
	array_substitute($cols, 4, 1, _("Description"));
	array_append($cols, array( array('insert'=>true, 'fun'=>'invoice_link')));

} else if ($_POST['order_view_mode'] == 'DeliveryTemplates') {
	array_substitute($cols, 4, 1, _("Description"));
	array_append($cols, array(
			array('insert'=>true, 'fun'=>'delivery_link'))
	);
} else if ($_POST['order_view_mode'] == 'PrepaidOrders') {
	array_append($cols, array(
			array('insert'=>true, 'fun'=>'invoice_prep_link'))
	);

} elseif ($trans_type == ST_SALESQUOTE) {
	 array_append($cols,array(
					array('insert'=>true, 'fun'=>'edit_link'),
					array('insert'=>true, 'fun'=>'order_link'),
					array('insert'=>true, 'fun'=>'prt_link')));
} elseif ($trans_type == ST_SALESORDER) {
	 array_append($cols,array(
// 			_("Tmpl") => array('insert'=>true, 'fun'=>'tmpl_checkbox'),
					array('insert'=>true, 'fun'=>'edit_link'),
					array('insert'=>true, 'fun'=>'prt_link')));
};


$table =& new_db_pager('orders_tbl', $sql, $cols);
$table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "80%";

display_db_pager($table);
submit_center('Update', _("Update"), true, '', null);

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
