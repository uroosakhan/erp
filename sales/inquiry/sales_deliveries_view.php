<?php

$page_security = 'SA_SALESINVOICE';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include($path_to_root . "/includes/session.inc");

include($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 600);
if (user_use_date_picker())
	$js .= get_js_date_picker();

if (isset($_GET['OutstandingOnly']) && ($_GET['OutstandingOnly'] == true))
{
	$_POST['OutstandingOnly'] = true;
	page(_($help_context = "Search Not Invoiced Deliveries"), false, false, "", $js);
}
else
{
	$_POST['OutstandingOnly'] = false;
	page(_($help_context = "Search All Deliveries"), false, false, "", $js);
}

if (isset($_GET['selected_customer']))
{
	$_POST['customer_id'] = $_GET['selected_customer'];
}
elseif (isset($_POST['selected_customer']))
{
	$_POST['customer_id'] = $_POST['selected_customer'];
}

function get_restrick_gate_pass($gate_pass_no)
{
    $sql = "SELECT delivery_no FROM ".TB_PREF."multiple_gate_pass WHERE delivery_no=$gate_pass_no";

    $result = db_query($sql, "could not get account type");

    $row = db_fetch_row($result);
    return $row[0];
}

if (isset($_POST['BatchInvoice']))
{
	// checking batch integrity
    $del_count = 0;
    foreach($_POST['Sel2_'] as $delivery => $branch) {
	  	$checkbox = 'Sel2_'.$delivery;
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
		display_error(_('For batch invoicing you should
		    select at least one delivery. All items must be dispatched to
		    the same customer branch.'));
    } else {
		$_SESSION['DeliveryBatch'] = $selected;
		meta_forward($path_to_root . '/sales/customer_invoice.php','BatchInvoice=Yes');
    }
}


if (isset($_POST['Gatepass']))
{
    // checking batch integrity
    $del_count = 0;
    foreach($_POST['Sel1_'] as $delivery => $branch) {
        $checkbox = 'Sel1_'.$delivery;
        if (check_value($checkbox))	{
            if (!$del_count) {
                $del_branch = $branch;
            }

            $selected[] = $delivery;
            $del_count++;
        }
    }

    if (!$del_count) {
        display_error(_('For batch invoicing you should
		    select at least one delivery. All items must be dispatched to
		    the same customer branch.'));
    } else {
        $_SESSION['GatePassBatch'] = $selected;
        meta_forward($path_to_root . '/sales/manage/multiple_gate_pass.php','Gatepass=Yes');
    }
}


//-----------------------------------------------------------------------------------
if (get_post('_DeliveryNumber_changed')) 
{
	$disable = get_post('DeliveryNumber') !== '';

	$Ajax->addDisable(true, 'DeliveryAfterDate', $disable);
	$Ajax->addDisable(true, 'DeliveryToDate', $disable);
	$Ajax->addDisable(true, 'StockLocation', $disable);
	$Ajax->addDisable(true, '_SelectStockFromList_edit', $disable);
	$Ajax->addDisable(true, 'SelectStockFromList', $disable);
	// if search is not empty rewrite table
	if ($disable) {
		$Ajax->addFocus(true, 'DeliveryNumber');
	} else
		$Ajax->addFocus(true, 'DeliveryAfterDate');
	$Ajax->activate('deliveries_tbl');
}

//-----------------------------------------------------------------------------------

start_form(false, false, $_SERVER['PHP_SELF'] ."?OutstandingOnly=".$_POST['OutstandingOnly']);

start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("#:"), 'DeliveryNumber', '',null, '', true);
date_cells(_("from:"), 'DeliveryAfterDate', '', null, -user_transaction_days());
date_cells(_("to:"), 'DeliveryToDate', '', null, 1);

locations_list_cells(_("Location:"), 'StockLocation', null, true);
end_row();

end_table();
start_table(TABLESTYLE_NOBORDER);
start_row();

stock_items_list_cells(_("Item:"), 'SelectStockFromList', null, true);

customer_list_cells(_("Select a customer: "), 'customer_id', null, true, true);

submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');

hidden('OutstandingOnly', $_POST['OutstandingOnly']);

end_row();

end_table(1);
//---------------------------------------------------------------------------------------------

function trans_view($trans, $trans_no)
{
	return get_customer_trans_view_str(ST_CUSTDELIVERY, $trans['trans_no']);
}

function batch_checkbox($row)
{
	$name = "Sel2_" .$row['trans_no'];
	return $row['Done'] ? '' :
		"<input type='checkbox' name='$name' value='1' >"
// add also trans_no => branch code for checking after 'Batch' submit
	 ."<input name='Sel2_[".$row['trans_no']."]' type='hidden' value='"
	 .$row['branch_code']."'>\n";
}

function gatepass_checkbox($row)
{
        $name = "Sel1_" .$row['trans_no'];
    if(get_restrick_gate_pass($row['trans_no']) != $row['trans_no'])
    {
        return $row['Done'] ? '' :
        "<input type='checkbox' name='$name' value='1' >"
        ."<input name='Sel1_[".$row['trans_no']."]' type='hidden' value='"
        .$row['debtor_no']."'>\n";
    }
}

function edit_link($row)
{
	return $row["Outstanding"]==0 ? '' :
		trans_editor_link(ST_CUSTDELIVERY, $row['trans_no']);
}

//function prt_link($row)
//{
//	return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
//}


function prt_link($row)
{
    global $SysPrefs;
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3')
        && $SysPrefs->delivery_appr() == 1)
    {
        if ($row['approval'] == 0)
        {
	        return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return "";
        }
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return "";
        }
    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 0)
    {
        return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
    }
    else
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['trans_no'], _("Print"), true, ST_CUSTDELIVERY, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return "";
        }
    }
}



//
//function invoice_link($row)
//{
//	return $row["Outstanding"]==0 ? '' :
//		pager_link(_('Invoice'), "/sales/customer_invoice.php?DeliveryNumber="
//			.$row['trans_no'], ICON_DOC);
//}


function invoice_link($row)
{
    global $SysPrefs;
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {
        if($row['approval'] == 0)
        {
            return $row["Outstanding"]==0 ? '' :
                pager_link(_('Invoice'), "/sales/customer_invoice.php?DeliveryNumber="
                    .$row['trans_no'], ICON_DOC);
        }
        elseif($row['approval'] == 1)
            return '';

    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1)
    {
        if($row['approval'] == 0)
        {
            return $row["Outstanding"]==0 ? '' :
                pager_link(_('Invoice'), "/sales/customer_invoice.php?DeliveryNumber="
                    .$row['trans_no'], ICON_DOC);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 0) {

        return $row["Outstanding"]==0 ? '' :
            pager_link(_('Invoice'), "/sales/customer_invoice.php?DeliveryNumber="
                .$row['trans_no'], ICON_DOC); }

    else{
        if($row['approval'] == 0)
        {
            return $row["Outstanding"]==0 ? '' :
                pager_link(_('Invoice'), "/sales/customer_invoice.php?DeliveryNumber="
                    .$row['trans_no'], ICON_DOC);
        }
        elseif($row['approval'] == 1)
            return '';
    }

}




function check_overdue($row)
{
   	return date1_greater_date2(Today(), sql2date($row["due_date"])) && 
			$row["Outstanding"]!=0;
}
//===============================================================================================
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
    $name = "Sel_" .$row['trans_no'];
    $hidden = 'last['.$row['trans_no'].']';
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
    $sql = "UPDATE ".TB_PREF."debtor_trans SET approval = '$reconcile_value'
			WHERE trans_no = ".db_escape($reconcile_id);
    db_query($sql, "Can't approve task");
}
if (isset($_POST['Approval']))
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
//    meta_forward($_SERVER['PHP_SELF'], "OutstandingOnly=1");


}
//------------------------------------------------------------------------------------------------
$sql = get_sql_for_sales_deliveries_view(get_post('DeliveryAfterDate'), get_post('DeliveryToDate'), get_post('customer_id'),	
	get_post('SelectStockFromList'), get_post('StockLocation'), get_post('DeliveryNumber'), get_post('OutstandingOnly'));

$cols = array(
		_("Delivery #") => array('fun'=>'trans_view', 'align'=>'right'),
		_("Customer"), 
		'branch_code' => 'skip',
		_("Branch") => array('ord'=>''), 
		_("Contact"),
		_("Reference"), 
		_("Cust Ref"), 
		_("Delivery Date") => array('type'=>'date', 'ord'=>''),
		_("Due By") => 'date', 
		_("Delivery Total") => array('type'=>'amount', 'ord'=>''),
		_("Currency") => array('align'=>'center'));
        if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL3') && $SysPrefs->delivery_appr() == 1) {
            array_append($cols, array(
                _("").
                submit('Approval',_("Approval"), false, _("Batch Update"))
                => array('insert'=>true, 'fun'=>'delivery_checkbox', 'align'=>'center'),
                submit('BatchInvoice',_("Batch"), false, _("Batch Invoicing"))
                => array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
                submit('Gatepass',_("Gate Pass"), false, _("Gate Pass"))
                => array('insert'=>true, 'fun'=>'gatepass_checkbox', 'align'=>'center'),
                array('insert'=>true, 'fun'=>'edit_link'),
                array('insert'=>true, 'fun'=>'invoice_link'),
                array('insert'=>true, 'fun'=>'prt_link')));
}
else{
    array_append($cols, array(
        submit('BatchInvoice',_("Batch"), false, _("Batch Invoicing"))
        => array('insert'=>true, 'fun'=>'batch_checkbox', 'align'=>'center'),
        submit('Gatepass',_("Gate Pass"), false, _("Gate Pass"))
        => array('insert'=>true, 'fun'=>'gatepass_checkbox', 'align'=>'center'),
        array('insert'=>true, 'fun'=>'edit_link'),
        array('insert'=>true, 'fun'=>'invoice_link'),
        array('insert'=>true, 'fun'=>'prt_link')));

}
//-----------------------------------------------------------------------------------
if (isset($_SESSION['Batch']))
{
    foreach($_SESSION['Batch'] as $trans=>$del)
    	unset($_SESSION['Batch'][$trans]);
    unset($_SESSION['Batch']);
}

$table =& new_db_pager('deliveries_tbl', $sql, $cols);
$table->set_marker('check_overdue', _("Marked items are overdue."));

//$table->width = "92%";

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

