<?php
/**********************************************************************
Copyright (C) FrontAccounting, LLC.
Released under the terms of the GNU General Public License, GPL,
as published by the Free Software Foundation, either version 3
of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
 ***********************************************************************/
$page_security = 'SA_REQUISITION_ALLOCATIONS';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
add_access_extensions();

page(_($help_context = "Requisitions"));

include_once($path_to_root . "/modules/requisitions/includes/modules_db.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");
include_once($path_to_root . "/includes/references.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
simple_page_mode(true);
function get_next_reference($type)
{
	$sql = "SELECT next_reference FROM ".TB_PREF."sys_types WHERE type_id = ".db_escape($type);

	$result = db_query($sql,"The last transaction ref for $type could not be retreived");

	$row = db_fetch_row($result);
	return $row[0];
}
if(isset($_GET['AddedPO']))
	display_notification(_("Purchase orders has been generated."));
//-----------------------------------------------------------------------------------
if(isset($_GET['po'])) {
	if($_GET['po'] == 'yes') {
		if (generate_po())
			display_notification(_("Purchase orders has been generated."));
		else
			display_error(_("Purchase orders generation failed."));
	}
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['quantity']) == 0)
	{
		$input_error = 1;
		display_error(_("The quantity of use cannot be empty."));
		set_focus('quantity');
	}
	if (strlen($_POST['price']) == 0)
	{
		$input_error = 1;
		display_error(_("The price cannot be empty."));
		set_focus('price');
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1) {
			update_requisition_lpo($selected_id, $_POST['supplier_id'], input_num('quantity'), input_num('price'));
			display_notification(_('Selected requisition details has been updated.'));
		}
		$Mode = 'RESET';
	}

}

function prt_link($row)
{
	return print_document_link($row['requisition_id'], _("Print"), true, 93, ICON_PRINT);
}
//-----------------------------------------------------------------------------------

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
}
//-----------------------------------------------------------------------------------
global $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO' ||
	$db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'IMEC') {
	$result = get_open_requisition_details_custom($_GET['wo_id']);
}
else
	$result = get_open_requisition_details();

start_form();

hidden('wo_id', $_GET['wo_id']);
start_table(TABLESTYLE, "width=80%");

if(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL5')) {
   $th = array(_("Approve PO"),_("Req#"), _("Point of Use"), _("Request Date"),_("Item Code"), _("Item Name"),
	_("Memo"), _("Quantity"), _("Price"),  _("Supplier"), "",_("Print"));
}
else{
$th = array(_("Req#"), _("Point of Use"), _("Request Date"),_("Item Code"), _("Item Name"),
	_("Memo"), _("Quantity"), _("Price"),  _("Supplier"), "",_("Print"));
}
table_header($th);
$k = 0;
while ($myrow = db_fetch($result))
{
	alt_table_row_color($k);
	if($myrow["supp_name"] != '') {
		if($myrow["price"] == 0)
			$myrow["price"] = get_purchase_price($myrow['supplier_id'],  $myrow["item_code"]);
		$name = "rec_" .$myrow['requisition_detail_id'];
		$hidden = 'last_'.$myrow['requisition_detail_id'];
		$value = $myrow['make_po'] != 0;
		if(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL5'))
        {
            check_cells(null, $name, $value, true, _('Make PO'));
        }/*.
		hidden($hidden, $value, false)*/
		/*. hidden($hidden, $value, false)*/
		label_cell($myrow["requisition_id"]);
		label_cell($myrow["point_of_use"]);
		label_cell(sql2date($myrow["application_date"]));
		label_cell($myrow["item_code"]);
		label_cell($myrow["description"]);
		label_cell($myrow["purpose"]);
		amount_cell_prc_only($myrow["quantity"]);
		amount_cell_prc_only($myrow["price"]);
		label_cell($myrow["supp_name"]);
		edit_button_cell("Edit".$myrow['requisition_detail_id'], _("Edit"));
		//	button_cell("print", _("print"), false, ICON_PRINT, 'selector');
		echo "<td>";
		echo prt_link($myrow);
	}
	else
	{
		if(!$_SESSION["wa_current_user"]->can_access('SA_DISPATCHAPPROVAL5'))
		{
            label_cell('');
        }
		label_cell($myrow["requisition_id"]);
		label_cell($myrow["point_of_use"]);
		label_cell(sql2date($myrow["application_date"]));
		label_cell($myrow["item_code"]);
		label_cell($myrow["description"]);
		label_cell($myrow["purpose"]);
		amount_cell_prc_only($myrow["quantity"]);
		amount_cell_prc_only($myrow["price"]);
		label_cell($myrow["supp_name"]);
		edit_button_cell("Edit".$myrow['requisition_detail_id'], _("Edit"));
//		button_cell("print", _("print"), false, ICON_PRINT, 'selector');
		echo "<td>";
		echo prt_link($myrow);
	}
//	print_document_link($myrow['requisition_id'], _("Print"), true, 93, ICON_PRINT);
	end_row();
}
end_table(1);
//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		//editing an existing status code

		$myrow = get_requisition_detail($selected_id);

		$_POST['supplier_id']  = $myrow["supplier_id"];
		$_POST['item_code']  = $myrow["item_code"];
		$_POST['quantity']  = $myrow["quantity"];
		$_POST['price']  = $myrow["price"];
	}
	hidden('selected_id', $selected_id);
}

if ($selected_id != -1)
{
    supplier_list_row(_("Supplier : "), 'supplier_id', null, true, false);
    $res = get_item_edit_info(get_post('item_code'));
    $dec = $res["decimals"] == '' ? 0 : $res["decimals"];
    $units = $res["units"] == '' ? 0 : $res["units"];
//$_POST['price'] = get_purchase_price($_POST['supplier_id'],  $myrow["item_code"]);
    qty_row(_("Order Quantity:"), 'quantity', number_format2(1, $dec), '', $units, $dec);
    amount_row_prc_only(_("Order Price :"), 'price', null, null, null, 2);
}
end_table(1);
if ($selected_id != -1)
{
    submit_add_or_update_center($selected_id == -1, '', 'both');
}
start_table(TABLESTYLE2);
$id = find_submit('rec_');
update_make_po($id);

function update_make_po($id)
{
	$TotalRecord = get_count_requisition_details();
	for($i = 1; $i <= $TotalRecord['TotalRecord']; $i++)
	{
		$reconcile_value = check_value("rec_".$i) ? '1' : '0';
		$sql = "UPDATE ".TB_PREF."requisition_details SET make_po=".db_escape($reconcile_value)
			."  WHERE requisition_detail_id=".db_escape($i);
		db_query($sql, "Can't change reconciliation status");
	}
}
function get_distinct_supplier_inactive()
{
	$sql="SELECT ".TB_PREF."requisition_details.supplier_id, 
    ".TB_PREF."requisition_details.requisition_id,".TB_PREF."requisition_details.requisition_detail_id,
		sum(".TB_PREF."requisition_details.quantity * ".TB_PREF."requisition_details.price ) As total
	FROM ".TB_PREF."requisition_details
	WHERE (".TB_PREF."requisition_details.lpo_id = 0) AND (".TB_PREF."requisition_details.supplier_id > 0)
	AND ".TB_PREF."requisition_details.make_po = 1
	GROUP BY ".TB_PREF."requisition_details.supplier_id
	ORDER BY ".TB_PREF."requisition_details.supplier_id";
	//  $sql ="SELECT DISTINCT(`supplier`),order_no,id FROM ".TB_PREF."new_pos where inactive =0";
	return  db_query($sql,"Error distinct");
	// $ft = db_fetch($db);
	// return $ft[0];
}
function get_all_data_from_new_pos_inactive($supplier,$order_no)
{
	$sql ="SELECT ".TB_PREF."item_codes.item_code, ".TB_PREF."item_codes.description,
	".TB_PREF."requisition_details.purpose,".TB_PREF."requisition_details.requisition_id,
	".TB_PREF."requisition_details.requisition_detail_id, ".TB_PREF."requisition_details.quantity,
	 ".TB_PREF."requisition_details.price
	FROM ".TB_PREF."requisition_details INNER JOIN ".TB_PREF."item_codes ON ".TB_PREF."requisition_details.item_code = ".TB_PREF."item_codes.item_code
	WHERE (".TB_PREF."requisition_details.lpo_id = 0) AND (".TB_PREF."requisition_details.supplier_id = $supplier
	AND ".TB_PREF."requisition_details.make_po = 1)";
	return  db_query($sql,"Error distinct");
}
//function get_all_data_from_new_pos_inactive($supplier,$order_no)
//{
//    $sql =" SELECT ".TB_PREF."item_codes.item_code, ".TB_PREF."item_codes.description,
//		".TB_PREF."requisition_details.requisition_detail_id, ".TB_PREF."requisition_details.quantity, ".TB_PREF."requisition_details.price
//	FROM ".TB_PREF."requisition_details INNER JOIN ".TB_PREF."item_codes ON ".TB_PREF."requisition_details.item_code = ".TB_PREF."item_codes.item_code
//	WHERE (".TB_PREF."requisition_details.lpo_id = 0) AND (".TB_PREF."requisition_details.supplier_id
//	AND ".TB_PREF."requisition_details.make_po = 1)";
//    return  db_query($sql,"Error distinct");
//    // $ft = db_fetch($db);
//    // return $ft[0];
//}
function get_tax_rate_1()
{
	$sql = "SELECT ".TB_PREF."tax_types.rate FROM ".TB_PREF."tax_types
	 WHERE ".TB_PREF."tax_types.id = 1";
	$result = db_query($sql, 'error');
	return $result;
}
$myrow3 = db_fetch(get_tax_rate_1());
function get_max_purch_order_no($supplier)
{
	$sql ="SELECT MAX(order_no) FROM ".TB_PREF."purch_orders  ";
	$db = db_query($sql,"Error");
	$ft = db_fetch($db);
	return $ft[0];
}
if(isset($_POST['Addpos']))
{
	handle_add_new_pos1();
}
function handle_add_new_pos1()
{
	global $Refs;
	$dist_supplier = get_distinct_supplier_inactive();
//	$sales_tax_amount = (($myrow3['rate']*$myrow1['total'])/100);
	while($myrow1 = db_fetch($dist_supplier))
	{
//        $ref = get_net_reference(ST_PURCHORDER);
		$date = today();
		$ref = $Refs->get_next(ST_PURCHORDER, null,
			array('supplier_id' => $myrow1['supplier_id'], 'date' => $date));
//        $date=today();
		$header_data = get_all_data_from_new_pos_inactive($myrow1['supplier_id'],$myrow1['order_no']);
		$sql = "INSERT INTO ".TB_PREF."purch_orders (supplier_id, Comments, ord_date, reference, 
     	requisition_no, into_stock_location, delivery_address, total, tax_included,receive_ref, 
     	pr, requisition_detail_id, approval) VALUES(";
		$sql .= db_escape($myrow1['supplier_id']) . "," .
			db_escape('') . ",'" .
			date2sql($date) . "', " .
			db_escape($ref) . ", " .
			db_escape('') . ", " .
			db_escape('DEF') . ", " .
			db_escape('address') . ", " .
			db_escape($myrow1['total']). ", " .
			db_escape('address') . ", " .
			db_escape(''). ", " .
			db_escape($myrow1['requisition_id']) . ", " .
			db_escape($myrow1['requisition_detail_id']). ", " .
			db_escape(0) . ")";

		db_query($sql, "The purchase order header record could not be inserted");
		$ord_no = get_max_purch_order_no($myrow1['supplier_id']);

		$Refs->save(ST_PURCHORDER, $ord_no, $ref);

		//add_comments(ST_PURCHORDER, $po_obj->order_no, $po_obj->orig_order_date, $po_obj->Comments);

		add_audit_trail(ST_PURCHORDER, $ord_no, $date);
		// add_purchase_po($myrow1['order_no'],$myrow1['supplier']);

		while($myrow2 = db_fetch($header_data))
		{
//			$a='\n';
//			if($myrow2['purpose'] != '')
//				$purpose=$myrow2['description'].$a.$myrow2['purpose'];
//			else
//				$purpose=$myrow2['description'];
			$sql = "INSERT INTO ".TB_PREF."purch_order_details (order_no, item_code, description, delivery_date, unit_price, quantity_ordered, text1) VALUES (";
			$sql .= $ord_no . ", " . db_escape($myrow2['item_code']). "," .
				db_escape($myrow2['description']). ",'" .
				date2sql($date) . "'," .
				db_escape($myrow2['price']) . ", " .
				db_escape($myrow2['quantity']). ", ".
				db_escape($myrow2['purpose']). ")";
			db_query($sql, "One of the purchase order detail records could not be inserted");
//  		$po_obj->line_items[$line_no]->po_detail_rec = db_insert_id();

			$sql1 = "UPDATE ".TB_PREF."requisition_details SET lpo_id = $ord_no WHERE requisition_detail_id = ".db_escape($myrow2['requisition_detail_id'])."";
			db_query($sql1, "One of the purchase order detail records could not be inserted");
//            add_purchase_deatail($myrow2['stock_id'],$myrow2['description'],$myrow2['quantity'],$myrow2['rates'],
//                $order_no,$myrow1['supplier'],$_POST['ord_date'],$myrow2['id']);
//
//            $sql = "UPDATE ".TB_PREF."requisition_details
//					SET lpo_id=1
//					WHERE requisition_id=".db_escape($order_no)
//                ." AND item_code=".db_escape($myrow2['stock_id']);
//            db_query($sql, "The purchase order could not be updated");
		}
	}
	meta_forward($_SERVER['PHP_SELF'],"AddedPO=$ord_no&wo_id=".$_POST['wo_id']);
}

end_table(1);

submit_cells('Addpos', _("Generate Purchase Orders"), "colspan=2 align='center'",
	_('Add new item to document'), true);

end_form();

//echo "<div align='center'><a href='requisition_allocations.php?po=yes'>"._("Generate Purchase Orders")."</a></div>\n";

//------------------------------------------------------------------------------------

end_page();

?>
