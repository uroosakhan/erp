<?php

$page_security = 'SA_ITEMSTRANSVIEW';
$path_to_root = "../..";

include($path_to_root . "/includes/session.inc");

page(_($help_context = "View Batch GRNs"), true);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/inventory/includes/inventory_db.inc");

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}

display_heading("Batch ". " #$trans_no");

br(1);
$adjustment_items = get_batch_grns(25, $trans_no);
$k = 0;
$header_shown = false;
while ($adjustment = db_fetch($adjustment_items))
{

	if (!$header_shown)
	{

		start_table(TABLESTYLE2, "width='90%'");
		start_row();
//		label_cells(_("At Location"), $adjustment['location_name'], "class='tableheader2'");
//    	label_cells(_("Reference"), $adjustment['reference'], "class='tableheader2'", "colspan=6");
//		label_cells(_("Date"), sql2date($adjustment['tran_date']), "class='tableheader2'");
		end_row();
		comments_display_row(ST_INVADJUST, $trans_no);

		end_table();
		$header_shown = true;

		echo "<br>";
		start_table(TABLESTYLE, "width='90%'");
		$th = array(_("#"), _("Item Description"));
		//Text Boxes Headings


		{
			$pref=get_company_prefs();

					array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date")));

		}

    	table_header($th);
	}
//$batch = get_batch_name_by_id($trans_no);
    $batch = 	get_batch_by_id($trans_no);
    alt_table_row_color($k);
    label_cell($adjustment['trans_no']);
$stock = get_item($adjustment['stock_id']);
    label_cell($stock['description']);
    label_cell($adjustment['qty']);
    label_cell($batch['name']);
    label_cell(sql2date($batch['exp_date']));
//    label_cell($adjustment['description']);
	//text boxes labels

//    amount_decimal_cell($adjustment['standard_cost']);
    end_row();
}

end_table(1);

//is_voided_display(ST_INVADJUST, $trans_no, _("This adjustment has been voided."));

end_page(true, false, false, ST_INVADJUST, $trans_no);
