<?php
$page_security = 'SA_SUPPTRANSVIEW';
$path_to_root = "../..";
include($path_to_root . "/purchasing/includes/po_class.inc");

include($path_to_root . "/includes/session.inc");
include($path_to_root . "/purchasing/includes/purchasing_ui.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_($help_context = "View Purchase Requisition"), true, false, "", $js);


if (!isset($_GET['trans_no']))
{
	die ("<br>" . _("This page must be called with a purchase order number to review."));
}

display_heading(_("Purchase Requisition") . " #" . $_GET['trans_no']);

echo "<br>";

$result = get_pr_history($_GET['trans_no']);


start_table(TABLESTYLE);

$th = array(_("Reference"), _("Supplier Name"), _("Application Date"),_("Total Quantity"), _("Remarks"));
table_header($th);


while ($myrow = db_fetch($result))
{
	start_row();
	label_cell($myrow["supplier_id"]);
	label_cell($myrow["supp_name"]);
	label_cell(sql2date($myrow["application_date"]));
	if(!user_check_access('SA_SUPPPRICES')) {
	label_cell($myrow["OrderValue"]);
	}
	label_cell($myrow["narrative"]);
	end_row();
}

end_table();

if ($myrow["closed"] == true)
{
	display_note(_("This Cost Centres is closed."));
}

start_form();

end_table();
hidden('trans_no', $id);
end_form();

//------------------------------------------------------------------------------
//------------------------------------------------------------------------------

end_page(true, false, false, ST_PURCHORDER, $_GET['trans_no']);

?>
