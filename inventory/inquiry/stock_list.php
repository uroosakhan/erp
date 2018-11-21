<?php

/**********************************************************************
  Page for searching item list and select it to item selection
  in pages that have the item dropdown lists.
  Author: bogeyman2007 from Discussion Forum. Modified by Joe Hunt
***********************************************************************/
//$page_security = "SA_ITEM";
$page_security = "SA_ITEMSTRANSVIEW";
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/db/items_db.inc");

$mode = get_company_pref('no_item_list');
if ($mode != 0)
	$js = get_js_set_combo_item();
else
	$js = get_js_select_combo_item();

page(_($help_context = "Items"), false, false, "", $js);

if(get_post("search")) {
  $Ajax->activate("item_tbl");
}

start_form(false, false, $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);

start_table(TABLESTYLE_NOBORDER);

start_row();

text_cells(_("Description"), "description");
submit_cells("search", _("Search"), "", _("Search items"), "default");

end_row();

end_table();

end_form();

div_start("item_tbl");
start_table(TABLESTYLE);

global $SysPrefs;
if($SysPrefs->hide_stock_list() == 0)
{
    $th = array("", _("Item Code"), _("Description"), _("Category"));
}
else
{
    $th = array("", _("Item Code"), _("Description"));

}
table_header($th);

$k = 0;
$name = $_GET["client_id"];
$result = get_items_search(get_post("description"), @$_GET['type']);

while ($myrow = db_fetch_assoc($result))
{
	alt_table_row_color($k);
	$value = $myrow['item_code'];
	if ($mode != 0) {
		$text = $myrow['description'];
		$text = preg_replace("/&#?[a-z0-9]+;/i","",$text);
  		ahref_cell(_("Select"), 'javascript:void(0)', '', 'setComboItem(window.opener.document, "'.$name.'",  "'.$value.'", "'.$text.'")');
	}
	else {
  		ahref_cell(_("Select"), 'javascript:void(0)', '', 'selectComboItem(window.opener.document, "'.$name.'", "'.$value.'")');
	}
  	label_cell($myrow["item_code"]);
	label_cell($myrow["description"]);
	
	if($SysPrefs->hide_stock_list() == 0)
	{
  	label_cell($myrow["category"]);
	}
	end_row();
}

end_table(1);

div_end();
end_page(true);
