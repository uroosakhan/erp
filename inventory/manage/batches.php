<?php

$page_security = 'SA_SALESTYPES';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Batches"));

include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/inventory/includes/db/batches_db.inc");

simple_page_mode(false);
//----------------------------------------------------------------------------------

if(get_post('ShowBatches'))
{
    $Ajax->activate('doc_tbl');
}
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

    $input_error = 0;

    if (strlen($_POST['name']) == 0)
    {
        $input_error = 1;
        display_error(_("The combo description cannot be empty."));
        set_focus('name');
    }

    if ($input_error != 1)
    {
        if ($selected_id != -1)
        {
            update_batch($selected_id, $_POST['name'], $_POST['exp_date']);
            $note = _('Selected combo has been updated');
        }
        else
        {
            add_batches($_POST['name'],$_POST['exp_date']);
            $note = _('New combo has been added');
        }

        display_notification($note);
        $Mode = 'RESET';
    }
}

//----------------------------------------------------------------------------------

if ($Mode == 'Delete')
{

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'stock_master'

	if (item_unit_used($selected_id))
	{
		display_error(_("Cannot delete this unit of measure because items have been created using this unit."));

	}
	else
	{
		delete_item_unit($selected_id);
		display_notification(_('Selected unit has been deleted'));
	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = '';
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

//----------------------------------------------------------------------------------


start_form();
echo stock_costable_items_list('stock_id', $_POST['stock_id'], true, true);
if(list_updated('stock_id')){
    $Ajax->activate('batch');
}
batch_list_cells(_("Batch"), $_POST['stock_id'], 'batch', null, true, false, true, true,null,1);
submit_cells('ShowBatches',_("Show Bactches"),'',_('Refresh Inquiry'), 'default');
div_start('doc_tbl');
start_table(TABLESTYLE2, "width='100%'");
$th = array(_('Batch '), _('Expiry Date'), _('GRN #'),_(''), "Edit");
inactive_control_column($th);

table_header($th);
$k = 0; //row colour counter

    $result = get_batches(check_value('show_inactive'), $_POST['batch']);

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

	label_cell($myrow["name"]);
	label_cell(sql2date($myrow["exp_date"]));
    label_cell(get_trans_view_str(1178, $myrow["id"], $myrow["reference"]));
    label_cell(($myrow["decimals"]==-1?_("User Quantity Decimals"):$myrow["decimals"]));
	$id = html_specials_encode($myrow["abbr"]);
	inactive_control_cell($id, $myrow["inactive"], 'item_units', 'abbr');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
// 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

inactive_control_row($th);
end_table(1);

//----------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != '')
{
 	if ($Mode == 'Edit') {
		//editing an existing item category

		$myrow = get_batchess($selected_id);

		$_POST['name'] = $myrow["name"];
		$_POST['exp_date']  = sql2date($myrow["exp_date"]);
		$_POST['decimals']  = $myrow["decimals"];
	}
	hidden('selected_id', $myrow["id"]);
}

    text_row(_("Batch name:"), 'name', null, 20, 20);
date_cells("Expiry Date",'exp_date');
//    hidden('abbr', $_POST['abbr']);
//    text_row(_("Unit Abbreviation:"), 'abbr', null, 20, 20);
//text_row(_("Descriptive Name:"), 'description', null, 40, 40);

//number_list_row(_("Decimal Places:"), 'decimals', null, 0, 6, _("User Quantity Decimals"));

end_table(1);

submit_add_or_update_center($selected_id == '', '', 'both');

end_form();
div_end();
end_page();

