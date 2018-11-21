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

//==============================================================================
$show_dates = !in_array($_POST['order_view_mode'], array('InvoiceTemplates', 'DeliveryTemplates'));
//---------------------------------------------------------------------------------------------

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

start_table(TABLESTYLE);

$th = array(_("No. of Orders Receive"),_("On Time Dispatches"), _("Completion Ratio"), _("Incomplete Orders"));

table_header($th);

$j = 1;
$k = 0; //row colour counter

{

    start_row();


    amount_cell(get_no_of_orders_receive());
    amount_cell(get_on_time_dispatches());
    $completion_ratio = number_format(get_on_time_dispatches() / get_no_of_orders_receive() * 100, 2);
    label_cell($completion_ratio.''. '%', "align='right'");
    amount_cell(get_incomplete_orders());


    end_row();

}


end_table();

end_form();
end_page();
?>

