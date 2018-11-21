<?php

$page_security = 'SA_MANUFTRANSVIEW';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Generate PO"));

include($path_to_root . "/manufacturing/includes/manufacturing_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------

$id = find_submit('Sel_');

if($id != -1) {
    update_temporary_bom($_POST['parent'.$id], $id);

    generate_purchase_order($id, $_POST['supplier_id'.$id], $_POST['purpose'.$id], input_num('quantity'.$id), $_POST['parent'.$id], $_POST['units_reqd']);

}
//display_error($_POST['AddedItem']);
if(isset($_GET['AddedID'])) {
    display_notification_centered(sprintf( _("Purchase Order Has been Added Against these Item")));
}

//-----------------------------------------------------------------------------------
if ($Mode == 'RESET') {
    $selected_id = -1;
    $sav = get_post('show_inactive');
    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

if(isset($_POST['cancel'])) {
    echo "<script type='text/javascript'>";
    echo "window.close();";
    echo "</script>";
}
function batch_checkbox($row)
{
//    display_error($supplier);
    $name = "Supp_" .$row['id'];
    return $row['Done'] ? '' :
        "<input type='checkbox' name='$name' value='1' >"
// add also trans_no => branch code for checking after 'Batch' submit
        ."<input name='Supp[".$row['id']."]' type='hidden' value='"
        .$row['id']."'>\n";
}
//-----------------------------------------------------------------------------------

$result = get_bom($_GET['item_code']);
if(!isset($_GET['AddedID']))
    while ($myrow = db_fetch($result)) {
        delete_temporary_bom($myrow['id']);
        add_temporary_bom($myrow['id'], $_GET['item_code'], $myrow['component'], $myrow['ID'],
            $myrow['LocationCode'], $myrow["quantity"]);
    }
$result1 = get_temporary_bom($_GET['item_code']);
$supplier_id = find_submit('supplier_id');
//display_error($_POST['supplier_id'.$supplier_id]);
//if(list_updated('supplier_id'.$supplier_id))
//    $Ajax->activate(Refresh);


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

start_form();
global $Ajax;

//$Ajax->activate('Refresh');
//echo "<center>";
//supplier_list_cells('Select Supplier :', 'supplier_id'.$myrow["id"], null, false, true);
echo "</center><br>";
start_table(TABLESTYLE2, "width='90%'");


$th = array(_("Code"), _("Description"), _("Location"),
    _("Work Centre"), _("Quantity"), _("Units"), _("Supplier")/*, _("Unit Price")*/, _("Purpose"), _("Make PO"));
//$th[""] =  /*submit('BatchInvoice',_("Batch"), false, _("Batch Invoicing"))."<br>".*/check_box("select_all", 0, "marked_all_checkbox(this)");
table_header($th);

$k = 0;
hidden('units_reqd', $_GET['units_reqd']);
while ($myrow = db_fetch($result1)) {
//    $qoo = get_on_porder_qty($myrow["component"], $myrow["loc_code"]);
//    $qoh = get_qoh_on_date($myrow["component"], $myrow["loc_code"]);
//    $demand_qty = get_demand_asm_qty($myrow["component"], $myrow["loc_code"]);
//    $RequiredQty = $demand_qty - $qoo - $qoh;
//    if($RequiredQty <= 0)
//        continue;
    $qoh = get_qoh_on_date($myrow["component"], null, null);
    $quantity = ($myrow["quantity"] - $qoh)*$_GET['units_reqd'];
    $short_qty = 0;
    if($quantity > 0)
        $short_qty = $quantity;

    alt_table_row_color($k);
    label_cell($myrow["component"]);
    label_cell($myrow["description"]);
    label_cell($myrow["location_name"]);
    label_cell($myrow["WorkCentreDescription"]);
    qty_cell($short_qty, false, get_qty_dec($myrow["component"]));
    hidden('quantity'.$myrow["id"], $short_qty);
    hidden('parent'.$myrow["id"], $myrow["parent"]);
    label_cell($myrow["units"]);
    echo "<td><center>";
    supplier_list_cells('', 'supplier_id'.$myrow["id"], null, false, true);

//    amount_cells(null, 'unit_price'.$myrow["id"], null);
    text_cells(null, 'purpose'.$myrow["id"], null, 40, 80);
//	echo "</td></center>";
    echo "<td><center>";
    $name = 'Sel_'.$myrow['id'];
    echo custom_checkbox(null, $name, null, true, _('Approved/Unapproved this generate'));
    echo "</td></center>";
//    echo "<td><center>";
//    echo /*check_box("select_".$myrow["id"], 1, "set_value(this)", true).*/
//        batch_checkbox($myrow)."</td>";
//	echo "</td></center>";

    end_row();

}
div_end();
end_table(1);
//-----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1)
//{
// 	if ($Mode == 'Edit') {
//		//editing an existing status code
////		$myrow = get_work_centre($selected_id);
//
////		$_POST['name']  = $myrow["name"];
////		$_POST['description']  = $myrow["description"];
//	}
//	hidden('selected_id', $selected_id);
//}

//text_row_ex(_("Name:"), 'name', 40);
//text_row_ex(_("Description:"), 'description', 50);
//supplier_list_cells("Supplier:", 'supplier_id', null, false);
end_table(1);
//submit_add_or_update_center($selected_id == -1, '', 'both');
//if($selected_id == -1)
//echo "<td>";
//	submit_center_first('cancel', "Close");
//	submit_center_	last('cancel', "Close");
end_form();
//------------------------------------------------------------------------------------
end_page();
?>