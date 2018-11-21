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
    if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 1)
    {
        if ($row['approval'] == 0) {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return '';
        }
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 1)
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 0)
    {
        return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
    }
    else
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    }
    else{

        if ($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 1)
    {
        if ($row['approval'] == 0) {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif ($row['approval'] == 1)
        {
            return '';
        }
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 1)
    {
         if ($row['approval'] == 0)
        {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }
    elseif (!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 0)
    {
        return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
    }
    else
    {
        if ($row['approval'] == 0)
        {
            return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);
        }
        elseif($row['approval'] == 1)
            return '';
    }

    }
    return print_document_link($row['id'], _("Print"), true, ST_SUPPRECEIVE, ICON_PRINT);

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
function batch_checkbox($row)
{
    $name = "Sel_" .$row['id'];
    $hidden = 'last['.$row['id'].']';
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
    $sql = "UPDATE ".TB_PREF."grn_batch SET approval = '$reconcile_value'
			WHERE id = ".db_escape($reconcile_id);
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

        update_check($id, $active);

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

$sql = get_sql_for_grn_search(get_post('OrdersAfterDate'), get_post('OrdersToDate'),get_post('supplier_id'), get_post('StockLocation'), get_post('order_number'),	get_post('SelectStockFromList'));

$cols = array(
		_("#") => array('fun'=>'trans_view', 'ord'=>'', 'align'=>'right'),
		_("Reference"),
        _("Item Code") ,
		_("Supplier") => array('ord'=>''),
		_("Location"),
		_("Delivery Date") => array('type'=>'date', 'ord'=>'desc'));
    if($_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL4') && $SysPrefs->grn_appr() == 1) {
        array_append($cols, array(
            _("") .
            submit('Approval', _("Approval"), false, _("Process"))
            => array('insert' => true, 'fun' => 'batch_checkbox', 'align' => 'center'),
            array('insert' => true, 'fun' => 'gl_view'),
            array('insert' => true, 'fun' => 'prt_link')));
    }
    else
    {
        array_append($cols, array(
            array('insert' => true, 'fun' => 'gl_view'),
            array('insert' => true, 'fun' => 'prt_link')));
    }


if (get_post('StockLocation') != ALL_TEXT) {
	$cols[_("Location")] = 'skip';
}

//---------------------------------------------------------------------------------------------------

$table =& new_db_pager('orders_tbl', $sql, $cols);

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