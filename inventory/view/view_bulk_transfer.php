<?php

$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");

page(_($help_context = "View Inventory Transfer"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}

$trans = get_stock_transfer($trans_no);

display_heading($systypes_array[ST_LOCTRANSFER] . " #$trans_no");
$pref = get_company_pref();
echo "<br>";
start_table(TABLESTYLE2, "width='90%'");

start_row();
label_cells(_("Reference"), $trans['reference'], "class='tableheader2'");
label_cells(_("Date"), sql2date($trans['tran_date']), "class='tableheader2'");
end_row();
if($pref['item_location'] != 1) {
    start_row();
    label_cells(_("From Location"), $trans['from_name'], "class='tableheader2'");
    label_cells(_("To Location"), $trans['to_name'], "class='tableheader2'");
}
end_row();

comments_display_row(ST_LOCTRANSFER, $trans_no);

end_table(2);

start_table(TABLESTYLE, "width='90%'");

if($pref['item_location'] == 1) {
    $th = array( _("From Location"), _("To Location"),_("Item Code"), _("Item Description"));
}
else{
    $th = array(_("Item Code"), _("Item Description"));
}

{
    array_append($th, array(_("Quantity"),
        _("Units")));
}

table_header($th);
$transfer_items = get_stock_moves(ST_LOCTRANSFER, $trans_no);
$k = 0;
while ($item = db_fetch($transfer_items))
{
    if($item['qty'] < 0) {
//        if ($item['loc_code'] == $trans['to_loc']) {
            alt_table_row_color($k);
            $to_location = get_to_stk_loc($item['to_stk_loc']);
            $to_stk_loc=get_stock_location(16,$to_location['to_stk_loc']);
            $pref = get_company_pref();
            label_cell(get_location_name($item['loc_code']));
            label_cell(get_location_name($to_stk_loc['loc_code']));
            label_cell($item['stock_id']);
            label_cell($item['description']);
            //text boxes labels
            qty_cell(-$item['qty'], false, get_qty_dec($item['stock_id']));
            label_cell($item['units']);
            end_row();;
//        }
    }
}

end_table(1);

is_voided_display(ST_LOCTRANSFER, $trans_no, _("This transfer has been voided."));

end_page(true, false, false, ST_LOCTRANSFER, $trans_no);
