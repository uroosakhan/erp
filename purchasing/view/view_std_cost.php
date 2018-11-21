<?php

$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";
//include($path_to_root . "/purchasing/includes/po_class.inc");

include($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
//include($path_to_root . "/purchasing/includes/purchasing_ui.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
page(_($help_context = "Formula For Standard Cost"), true, false, "", $js);


if (!isset($_GET['stock_id']))
{
    die ("<br>" . _("This page must be called with a purchase order number to review."));
}
function get_standard_cost_items($stock_id)
{
    $sql = "SELECT ".TB_PREF."supp_invoice_items.cur_qoh,".TB_PREF."supp_invoice_items.prev_qoh,
    ".TB_PREF."supp_invoice_items.prev_sc,
	".TB_PREF."supp_invoice_items.stock_id,".TB_PREF."supp_invoice_items.quantity,
	".TB_PREF."supp_invoice_items.landed_cost_sum
			FROM ".TB_PREF."supp_trans,".TB_PREF."supp_invoice_items
			WHERE  ".TB_PREF."supp_trans.trans_no=".TB_PREF."supp_invoice_items.supp_trans_no
			AND ".TB_PREF."supp_invoice_items.stock_id=".db_escape($stock_id)."
			";
    $result = db_query($sql, "could not process Requisition to Purchase Order");
    $row = db_fetch($result);
    return $row ;

}






display_heading(_("Formula For Standard Cost") . " #" . $_GET['stock_id']);


//display_po_summary($purchase_order, true);

start_table(TABLESTYLE, "width='90%'", 6);
echo "<tr><td valign=top>"; // outer table


start_table(TABLESTYLE, "width='100%'");

echo"<h3>((prev QOH x prev SC)+(cur.QTY x price)) / Cur.QOH)</h3>";



$values= get_standard_cost_items($_GET['stock_id']);


$price =($values['landed_cost_sum']/ $values['quantity']);



label_cell("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(".$values['prev_qoh'] ."x". $values['prev_sc'].")"."+"."(". $values['quantity']." x ".$price.")".") / ".$values['cur_qoh']." )");


//----------------------------------------------------------------------------------------------------

$k = 0;


// if (db_num_rows($grns_result) > 0)
// {

//     echo "</td><td valign=top>"; // outer table

//     display_heading2(_("Deliveries"));
//     start_table(TABLESTYLE);
//     $th = array(_("#"), _("Reference"), _("Delivered On"));
//     table_header($th);
//     while ($myrow = db_fetch($grns_result))
//     {
// 		alt_table_row_color($k);

//     	label_cell(get_trans_view_str(ST_SUPPRECEIVE,$myrow["id"]));
//     	label_cell($myrow["reference"]);
//     	label_cell(sql2date($myrow["delivery_date"]));

//     	end_row();
//     }
//     end_table();
// }

//$invoice_result = get_po_invoices_credits($_GET['trans_no']);

$k = 0;

// if (db_num_rows($invoice_result) > 0)
// {

//     echo "</td><td valign=top>"; // outer table

//     display_heading2(_("Invoices/Credits"));
//     start_table(TABLESTYLE);
//     $th = array(_("#"), _("Date"), _("Total"));
//     table_header($th);
//     while ($myrow = db_fetch($invoice_result))
//     {
//     	alt_table_row_color($k);

//     	label_cell(get_trans_view_str($myrow["type"],$myrow["trans_no"]));
//     	label_cell(sql2date($myrow["tran_date"]));
//     	amount_cell($myrow["Total"]);
//     	end_row();
//     }
//     end_table();
// }

echo "</td></tr>";

end_table(1); // outer table

//display_allocations_to(PT_SUPPLIER, $purchase_order->supplier_id, ST_PURCHORDER, $purchase_order->order_no, $total + $tax_total);

//----------------------------------------------------------------------------------------------------

end_page(true, false, false,  $_GET['stock_id']);

