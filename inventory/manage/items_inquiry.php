<?php

$page_security = 'SA_ITEM';
$path_to_root = "../../";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Items Inquiry"), isset($_GET['customer_id']), false, "", $js);

if (isset($_GET['customer_id']))
{
	$_POST['customer_id'] = $_GET['customer_id'];
}

//------------------------------------------------------------------------------------------------



function get_combo_list_name()
{
	/*Gets the GL Codes relevant to the item account  */
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE item_enable =1
	AND name  IN('combo1','combo2','combo3','combo4','combo5','combo6') ";

$result = db_query($sql, "could not get customer");
	$row = db_fetch($result);
	return $row['label_value'];

//	return db_fetch($sql);
}



function get_cutom_list_name()
{

	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE item_enable = 1
	AND name NOT IN('combo1','combo2','combo3','combo4','combo5','combo6','total_amount','total_combo','total_date','total_text','con_factor','date1','date2','date3','formula','itemwise_discount','item_code_auto','sales_persons','alt_uom')  ";



$result = db_query($sql, "could not get customer");
	$row = db_fetch($result);
	return $row;

//	return db_fetch($sql);
}


$combo =get_combo_list_name();

$custom_fields =get_cutom_list_name();


function get_sales_persons_name()
{
	/*Gets the GL Codes relevant to the item account  */
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE item_enable =1
	AND name  = 'sales_persons' ";

//	return db_query($sql,"retreive stock gl code");
	return db_fetch($sql);
}


$sales_person =get_sales_persons_name();

start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();


items_search_list_cells(_("Search Criteria "), 'items_fields', null, _("All"));

ref_cells(null, 'items_fields_searching', '',null, '', true);

if($custom_fields){
    
    
    item_pref_list_cells(_("Search For Custom Fields"), 'items_enable', null,false);



  ref_cells(null, 'items_enable_searching', '',null, '', false);

    
    
}

if($combo) {


	combos_search_list_cells(_("Search For Combo Fields"), 'combo_fields', null,false,"","","","","","",true);
}


if($_POST['combo_fields']=='combo1'){


	combo1_po_list_cells(null, 'combo_searching', null, true);

}

elseif($_POST['combo_fields']=='combo2'){
	combo2_list_cells(null, 'combo_searching', null, true);

}
elseif($_POST['combo_fields']=='combo3'){

	combo3_list_cells(null, 'combo_searching', null, true);

}
elseif($_POST['combo_fields']=='combo4'){

	combo4_list_cells(null, 'combo_searching', null, true);
}
elseif($_POST['combo_fields']=='combo5'){

	combo5_list_cells(null, 'combo_searching', null, true);

}
elseif($_POST['combo_fields']=='combo6'){

	combo6_list_cells(null, 'combo_searching', null,true);
}



stock_categories_list_cells(_("Category:"), 'category_id', null, true);


end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();
stock_item_types_list_cells(_("Item Type:"), 'mb_flag', null, true);
stock_units_list_cells(_('Units of Measure:'), 'units', null, true);

item_tax_types_list_cells(_("Item Tax Type:"), 'tax_type_id', null, true);

if($sales_person){

	sales_persons_list_cells(_("Sales Person:"), 'salesman', null, true);


}


end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

$dim = get_company_pref('use_dimension');

if($dim==1  && $dim!=0){
    
	dimensions_list_cells("Dimension1 :", 'dimension_id', null, true, " ", 1);

}elseif($dim==2 && $dim!=0){
	dimensions_list_cells("Dimension1 :", 'dimension_id', null, true, " ", 1);

	dimensions_list_cells("Dimension2 :", 'dimension2_id', null, true, " ", 2);
}
elseif($dim==2 && $dim!=0 ){

	dimensions_list_cells("Dimension2 :", 'dimension2_id', null, true, " ", 2);
}
submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');
check_cells(_("Show Gl col. &  inactive:"), 'show_inactive', null, true);
    

end_row();
end_table();

start_table(TABLESTYLE_NOBORDER);
start_row();

if (!@$_GET['popup'])


	if ($trans_type == ST_SALESQUOTE)
		check_cells(_("Show All:"), 'show_all');


hidden('order_view_mode', $_POST['order_view_mode']);
hidden('type', $trans_type);

end_row();


echo '&nbsp;<center><a href="items.php" target="_blank"><input type="button" value="+ADD ITEMS"></a>

&nbsp;&nbsp;
<a href="/modules/import_items/import_items.php?action=import" target="_blank"><input type="button" value="IMPORT"></a>&nbsp;&nbsp;
    <a href="/modules/import_items/import_items.php?action=export" target="_blank"><input type="button" value="EXPORT"></a>


</center>';





end_table(1);

set_global_customer($_POST['customer_id']);

//------------------------------------------------------------------------------------------------


function systype_name($dummy, $type)
{
	global $systypes_array;

	return $systypes_array[$type];
}

function order_view($row)
{
	return $row['order_']>0 ?
		get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
		: "";
}

function trans_view($trans)
{
	return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
	return	$row["type"] == ST_SALESINVOICE	? $row["due_date"] : '';
}

function gl_view($row)
{
	return get_gl_view_str($row["type"], $row["trans_no"]);
}
//ansar 26-08-2017
function fmt_amount($row)
{
	$value =
		$row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type']==ST_JOURNAL ?
			-$row["TotalAmount"] : $row["TotalAmount"];
	return price_format($value);
}
function fmt_debit($row)
{
//dz 16.6.17
	/*
    $value =
            $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    */
	$value =
		$row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV ?
			-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>=0 ? price_format($value) : '';

}

function fmt_credit($row)
{
//dz 16.6.17
	/*
    $value =
            !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL) ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    */
	$value =
		!($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV) ?
			-$row["TotalAmount"] : $row["TotalAmount"];
	return $value>0 ? price_format($value) : '';
}


function credit_link($row)
{
	global $page_nested;

	if ($page_nested)
		return '';
	return $row['type'] == ST_SALESINVOICE && $row["Outstanding"] > 0 ?
		pager_link(_("Credit This") ,
			"/sales/customer_credit_invoice.php?InvoiceNumber=". $row['trans_no'], ICON_CREDIT):'';
}

function edit_link($row)
{
	if (@$_GET['popup'])
		return '';
	global $trans_type;
	$modify = ($trans_type == ST_SALESORDER ? "ModifyOrderNumber" : "ModifyQuotationNumber");
	return pager_link( _("Edit"),
		"/inventory/manage/items.php?stock_id=" . $row['stock_id'], ICON_EDIT);
}

function prt_link($row)
{
	if ($row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT || $row['type'] == ST_CPV || $row['type'] == ST_CRV)
		return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_CUSTPAYMENT, ICON_PRINT);
	elseif ($row['type'] == ST_BANKPAYMENT) // bank payment printout not defined yet.
		return '';
	else
		return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}
function prt_link1($row)
{

		return print_document_link($row['stock_id'], _("Print"), true, RC_INVENTORY, ICON_PRINT);
}


function check_overdue($row)
{
	if($row[inactive]==1)
	$sql = "SELECT * FROM ".TB_PREF."stock_master WHERE inactive=".db_escape(1);
	$z = db_query($sql,"items could not be retreived");
	$r = db_fetch_row($z);
	return $r[0];

}
////-------------------------------marina----------------------------
if($dim){
	function get_dimension_name_item($dimension_id)
	{
		$sql = "SELECT CONCAT(reference,'-',name) FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

		$result = db_query($sql, "could not get customer");

		$row = db_fetch_row($result);

		return $row[0];
	}
	function get_dimension2_name_item($dimension_id)
	{
		$sql = "SELECT CONCAT(reference,'-',name) FROM ".TB_PREF."dimensions WHERE id=".db_escape($dimension_id);

		$result = db_query($sql, "could not get customer");

		$row = db_fetch_row($result);

		return $row[0];
	}

}



function get_category_id_name($category_id)
{
	$sql = "SELECT description FROM ".TB_PREF."stock_category WHERE category_id=".db_escape($category_id['category_id']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_editable_desc($row)
{
	if ($row["editable"] == 0)
	{
		$a='NO';
	}
	else
	{
		$a='YES';
	}
	return $a;
}

function get_exclude_sale($row)
{
	if ($row["no_sale"] == 0)
	{
		$a='NO';
	}
	else
	{
		$a='YES';
	}
	return $a;
}

function get_exclude_purchase($row)
{
	if ($row["no_purchase"] == 0)
	{
		$a='NO';
	}
	else
	{
		$a='YES';
	}
	return $a;
}

function get_salesman_id_name($salesman)
{
	$sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($salesman['salesman']);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_sales_account($sales_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($sales_acc['sales_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_inventory_account($invent_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($invent_acc['inventory_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_cogs_account($cogs_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($cogs_acc['cogs_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_adjustment_account($adjust_acc)
{
	$sql = "SELECT CONCAT(account_code,'-',account_name) AS name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($adjust_acc['adjustment_account']);

	$result = db_query($sql, "could not get gl account");
	$row= db_fetch_row($result);
	return $row[0];
}

function get_item_tax_types($tax_type_id)
{
	$sql = "SELECT name FROM ".TB_PREF."item_tax_types WHERE id=".db_escape($tax_type_id['tax_type_id']);

	$result = db_query($sql, "could not get tax type rate");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_sale_price($stock_id)
{
    $sql="SELECT price FROM ".TB_PREF."prices WHERE stock_id=".db_escape($stock_id['stock_id']);

    $result = db_query($sql, "could not get tax type rate");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_item_unit_name($unit_id)
{
	$sql="SELECT name FROM ".TB_PREF."item_units WHERE abbr=".db_escape($unit_id['units']);

	$result = db_query($sql, "could not get tax type rate");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_item_type($row)
{
	if ($row["mb_flag"] == 'M')
	{
		$a='Manufactured';
	}

	if ($row["mb_flag"] == 'B')
	{
		$a='Purchased';
	}

	if ($row["mb_flag"] == 'D')
	{
		$a='Service';
	}
	

	else {}
	return $a;
}

function view_item_code($type, $trans_no, $label="", $icon=false, $class='', $id='')
{

	$viewer = "inventory/inquiry/stock_status.php?stock_id=$trans_no";
	if ($label == "")
	$label = $trans_no;

	return viewer_link($label, $viewer, $class, $id,  $icon, ("Id"));
}




function get_item_header_name()
{
	/*Gets the GL Codes relevant to the item account  */
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE item_enable =1
	AND name NOT IN('combo1','combo2','combo3','combo4','combo5','combo6','total_amount','total_combo','total_date','total_text','con_factor','date1','date2','date3','formula','itemwise_discount','item_code_auto','sales_persons','alt_uom') ";

	return db_query($sql,"retreive stock gl code");
	//return db_fetch($get);
}
$get_item_header_name = get_item_header_name();
$i = 0;
$data = array();
while ($myrow = db_fetch($get_item_header_name)) {
	$data[$i]=$myrow[0];
	$i++;
}

function get_qoh_on_date_custom($row)
{
    return get_qoh_on_date($row['stock_id'], '', null,'');
}
//------------------------------------------------------------------------------------------------


$sql = get_sql_for_items_log_view($_POST['stock_id'], $_POST['description'],
	$_POST['carton'], $_POST['long_description'], $_POST['category_id'], $_POST['tax_type_id'],
	$_POST['mb_flag'], $_POST['units'], $_POST['editable'], $_POST['no_sale'], $_POST['no_purchase'],
	$_POST['salesman'], $_POST['sales_account'], $_POST['inventory_account'], $_POST['cogs_account'],
	$_POST['adjustment_account'], $_POST['dimension_id'],
	$_POST['dimension2_id'], check_value('show_inactive'),$_POST['items_enable'], $_POST['items_enable_searching'],$_POST['items_fields'], $_POST['items_fields_searching'], $_POST['combo_fields'], $_POST['combo_searching']);


//------------------------------------------------------------------------------------------------
db_query("set @bal:=0");
if ( check_value('show_inactive') !=1) {

	$cols = array(
		array('insert' => true, 'fun' => 'edit_link'),
		_("Item Code") => array('fun' => 'view_item_code', 'ord' => ''),
		_("Name"),
	    _("Packing"),
		_("Description"),
		_("Category") => array('fun' => 'get_category_id_name'),
		_("Item Tax Type") => array('fun' => 'get_item_tax_types'),
		_("Item Type") => array('fun' => 'get_item_type'),
		_("Units of Measure") => array('fun' => 'get_item_unit_name'),
		_("Dimension 1") => array('fun' => 'get_dimension_name_item'),
		_("Dimension 2") => array('fun' => 'get_dimension_name_item'),
		
		_("$data[0]") => array('name' => $data[0],'ord' => ''),
		_("$data[1] ") => array('name' => $data[1]),
		_("$data[2] ") => array('name' => $data[2]),
		_("$data[3]") => array('name' => $data[3]),
		_("$data[4] ") => array('name' => $data[4]),
		_("$data[5]") => array('name' => $data[5]),
		_("$data[6] ") => array('name' => $data[6]),
		_("$data[7] ") => array('name' => $data[7]),
		_("$data[8] ") => array('name' => $data[8]),
		_("$data[9] ") => array('name' => $data[9]),
		_("$data[10] ") => array('name' => $data[10]),
		_("$data[11] ") => array('name' => $data[11]),
		_("$data[12] ") => array('name' => $data[12]),
		_("Sale Price") => array('fun' => 'get_sale_price'),
        _("Quantity On hand") => array('fun' => 'get_qoh_on_date_custom'),
	array('insert' => true, 'fun' => 'prt_link1')
	);



}else {
	$cols = array(
		array('insert' => true, 'fun' => 'edit_link'),
		_("Item Code") => array('fun' => 'view_item_code', 'ord' => ''),
		_("Name"),
		
		_("Packing"),
		_("Description"),
		_("Category") => array('fun' => 'get_category_id_name'),
		_("Item Tax Type") => array('fun' => 'get_item_tax_types'),
		_("Item Type") => array('fun' => 'get_item_type'),
		_("Units of Measure") => array('fun' => 'get_item_unit_name'),
		_("Dimension 1") => array('fun' => 'get_dimension_name'),
		_("Dimension 2") => array('fun' => 'get_dimension2_name'),
		_("SALES") => array('name' => $data[0]),
		_("$data[1] ") => array('name' => $data[1]),
		_("$data[2] ") => array('name' => $data[2]),
		_("$data[3]") => array('name' => $data[3]),
		_("$data[4] ") => array('name' => $data[4]),
		_("$data[5]") => array('name' => $data[5]),
		_("$data[6] ") => array('name' => $data[6]),
		_("$data[7] ") => array('name' => $data[7]),
		_("$data[8] ") => array('name' => $data[8]),
		_("$data[9] ") => array('name' => $data[9]),
		_("$data[10] ") => array('name' => $data[10]),
		_("$data[11] ") => array('name' => $data[11]),
		_("$data[12] ") => array('name' => $data[12]),
		_("Sales Account") => array('fun' => 'get_sales_account'),
		_("Inventory Account") => array('fun' => 'get_inventory_account'),
		_("C.O.G.S. Account") => array('fun' => 'get_cogs_account'),
		_("Inventory Adjustments Account") => array('fun' => 'get_adjustment_account'),
		  	_("Sale Price") => array('fun' => 'get_sale_price'),
        _("Quantity On hand") => array('fun' => 'get_qoh_on_date_custom'),

	
	);
}
$table =& new_db_pager('orders_tbl', $sql, $cols);
$table->set_marker('check_overdue');

$table->width = "80%";


display_db_pager($table);
//submit_center('Update', _("Update"), true, '', null);


if (!@$_GET['popup'])
{
	end_form();
	end_page();
}
?>