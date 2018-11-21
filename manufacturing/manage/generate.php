<?php

$page_security = 'SA_MANUFTRANSVIEW';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Generate PO"));

include($path_to_root . "/manufacturing/includes/manufacturing_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//-----------------------------------------------------------------------------------
//         Delete existing PO
$sql = "DELETE FROM ".TB_PREF."requisitions WHERE wo_id = ".db_escape($_GET['wo_id']);
db_query($sql, "could not add requisition details");
$sql = "DELETE FROM ".TB_PREF."requisition_details WHERE wo_id = ".db_escape($_GET['wo_id']);
db_query($sql, "could not add requisition details");

$id = find_submit('Sel_');

if($id != -1) {
//    update_temporary_bom($_POST['parent'.$id], $id);

//    generate_purchase_order($id, $_POST['supplier_id'.$id], $_POST['purpose'.$id], input_num('quantity'.$id), $_POST['parent'.$id], $_POST['units_reqd']);

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
//function batch_checkbox($row)
//{
////    display_error($supplier);
//    $name = "Supp_" .$row['id'];
//    return $row['Done'] ? '' :
//        "<input type='checkbox' name='$name' value='1' >"
//// add also trans_no => branch code for checking after 'Batch' submit
//        ."<input name='Supp[".$row['id']."]' type='hidden' value='"
//        .$row['id']."'>\n";
//}

function batch_checkbox($row)
{
    $name = "Sel_" .$row['id'];
    return custom_checkbox(null, $name, null, false, _('Approve/Unapproved this voucher'))
//        "<input type='checkbox' name='$name' value='$active' class='sendSms' >"
// add also trans_no => branch code for checking after 'Batch' submit
    ."<input name='Sel_[".$row['id']."]' type='hidden' value='1'>\n";
}
//-----------------------------------------------------------------------------------

$result = get_bom($_GET['item_code']);
if(!isset($_GET['AddedID']))
    while ($myrow = db_fetch($result)) {
        delete_temporary_bom($myrow['id']);
        add_temporary_bom($myrow['id'], $_GET['item_code'], $myrow['component'], $myrow['ID'],
            $myrow['LocationCode'], $myrow["quantity"], $_GET['wo_id']);
    }
$result1 = get_temporary_bom($_GET['item_code'], $_GET['wo_id']);
$supplier_id = find_submit('supplier_id');

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

if (isset($_POST['BatchMakePO'])) {
    $allow = 0;
    foreach($_POST['Sel_'] as $delivery => $branch) {
        //    update_temporary_bom($_POST['parent'.$id], $id);
        $point_of_use = "Auto make PO";
        $narrative = "Auto make PO";
        $details = "Auto make PO";
        if(check_value('Sel_'.$delivery)) {
            $sql = "SELECT * FROM ".TB_PREF."temporary_bom WHERE id = ".db_escape($delivery);
            $query = db_query($sql, "Error");
            $fetch = db_fetch($query);
            $sql = "INSERT INTO ".TB_PREF."requisitions (point_of_use, narrative, details, application_date, completed, wo_id) VALUES (".
            db_escape($point_of_use).",".db_escape($narrative).",".db_escape($details).",".db_escape(date('Y-m-d h:i:sa')).", 1, ".$fetch['wo_id'].")";
            db_query($sql, "could not add requisitions");
           $requisitionid = db_insert_id();
//         get purchase item price & supplier in setup form
            $sql = "SELECT * FROM ".TB_PREF."purch_data
                    WHERE stock_id = ".db_escape($fetch['component']);
            $query = db_query($sql, "Error");
            $fetch1 = db_fetch($query);
            $sql = "INSERT INTO ".TB_PREF."requisition_details (requisition_id, item_code, purpose, order_quantity, estimate_price, quantity, price, supplier_id, wo_id) VALUES (".
                db_escape($requisitionid).",".db_escape($fetch['component']).",".db_escape($_POST['purpose' . $delivery]).",".db_escape(input_num('quantity' . $delivery)).",".db_escape($fetch1['unit_price']).",".
                db_escape(input_num('quantity' . $delivery)).",".db_escape($fetch1['price']).",".db_escape($fetch1['supplier_id']).",".db_escape($fetch['wo_id']).")";
            db_query($sql, "could not add requisition details");
//            update_temporary_bom($_POST['parent'.$delivery], $delivery);
            $wo_id = $fetch['wo_id'];
        $allow = 1;
        }
//        $value = explode("-",$delivery); // separate type and trans_no
//        $checkbox = 'Sel_'.$delivery; // make checkbox name
//        $inactive = check_value($checkbox);//  get checkbox value 0/1
//
//        if($inactive == 0)
//            $active = 1;
//        elseif($inactive == 1)
//            $active = 0;
//
//        if($active == 0)
//        {
//            $sql = "UPDATE ".TB_PREF."gl_trans SET approval=".db_escape($active)."
//                    WHERE type = ".db_escape($value[1])."
//                    AND type_no = ".db_escape($value[0]);
//            db_query($sql, "The voucher could not be activated");
//        }
//        elseif($active == 1)
//        {
//            $sql = "UPDATE ".TB_PREF."gl_trans SET approval=".db_escape($active)."
//                    WHERE type = ".db_escape($value[1])."
//                    AND type_no = ".db_escape($value[0]);
//            db_query($sql, "The voucher could not be activated");
//        }
    }
    if($allow == 1)
        header("Location: ".$path_to_root . "/modules/requisitions/requisition_allocations.php?wo_id=".$wo_id);

//    meta_forward($_SERVER['PHP_SELF'], "item_code=$parent&units_reqd=$units_reqd&AddedID=$ord_no");
}

start_form();
global $Ajax;

//$Ajax->activate('Refresh');
//echo "<center>";
//supplier_list_cells('Select Supplier :', 'supplier_id'.$myrow["id"], null, false, true);
echo "</center><br>";
start_table(TABLESTYLE2, "width='90%'");


$th = array(_("Code"), _("Description"), _("Location"),
    _("Work Centre"), _("Quantity"), _("Units")/*, _("Supplier"), _("Unit Price")*/, _("Purpose")/*, _("Make PO")*/);
$th[""] =  submit('BatchMakePO',_("Batch PO"), false, _("Batch For Make PO"))."<br>".check_box("select_all", 0, "marked_all_checkbox(this)");
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

    if($short_qty == 0)
        continue;
    alt_table_row_color($k);
    label_cell($myrow["component"]);
    label_cell($myrow["description"]);
    label_cell($myrow["location_name"]);
    label_cell($myrow["WorkCentreDescription"]);
    qty_cell($short_qty, false, get_qty_dec($myrow["component"]));
    hidden('quantity'.$myrow["id"], $short_qty);
    hidden('parent'.$myrow["id"], $myrow["parent"]);
    label_cell($myrow["units"]);
//    echo "<td><center>";
//    supplier_list_cells('', 'supplier_id'.$myrow["id"], null, false, true);
//  amount_cells(null, 'unit_price'.$myrow["id"], null);

//	echo "</td></center>";
    echo "<center>";
    text_cells(null, 'purpose'.$myrow["id"], null, 40, 80);
    $name = 'Sel_'.$myrow['id'];
//  echo custom_checkbox(null, $name, null, true, _('Approved/Unapproved this generate'));
    echo "</center>";
    echo "<td><center>";
    echo /*check_box("select_".$myrow["id"], 1, "set_value(this)", true).*/
    batch_checkbox($myrow)."</td>";
	echo "</td></center>";
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
<script type="text/javascript">
    function marked_all_checkbox(source)
    {
        var str = source.name;
        var res = str.substring(0, 6);
        checkboxes = document.getElementsByClassName('sendSms');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
            set_value(checkboxes[i]);
        }
    }
    function set_value(source){
        if(source.checked)
            source.value = 1;
        else
            source.value = 0;
    }

    function checkAll(ele) {
        var checkboxes =  '';
        if(ele.className == 'selectAll') {
            checkboxes = document.getElementsByClassName('sendSms');
        }
        else if(ele.className == 'emailAll') {
            checkboxes = document.getElementsByClassName('email');
        }
        if(checkboxes != '') {
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
</script>