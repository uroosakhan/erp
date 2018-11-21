<?php
$page_security = 'SA_SALESTYPES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Discount"));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/POS/includes/db/discount_db.inc");

simple_page_mode(true);
//----------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{
	$input_error = 0;
    $sql = "SELECT COUNT(*) TotalRecord FROM ".TB_PREF."discount WHERE discount_type = ".db_escape($_POST['discount_type']);
    $query = db_query($sql, "Error");
    $fetch = db_fetch_row($query);
    if ($selected_id == -1)
        if($fetch[0] != 0)
    	{
            display_error( _("Discount type already exist."));
            set_focus('discount_type');
            $input_error = 1;
    	}
    if(!check_num('discount', 0, 100))
	{
        display_error( _("Discount should not be less than 0 and grater than 100."));
        set_focus('discount');
        $input_error = 1;
	}
	if(!$_POST['dis_account'])
	{
        display_error( _("Account must be selected."));
        set_focus('dis_account');
        $input_error = 1;
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_discount_pos($selected_id, $_POST['discount'], $_POST['dis_account'], $_POST['discount_type'], $_POST['discount_description']);
		}
		else
		{
			/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_discount_pos($_POST['discount'], $_POST['dis_account'], $_POST['discount_type'], $_POST['discount_description']);
		}

		if ($selected_id != -1)
			display_notification(_('Selected discount have been updated'));
		else
			display_notification(_('New discount have been added'));
		$Mode = 'RESET';
	}
}
//----------------------------------------------------------------------------------------------------

if ($Mode == 'Delete')
{
	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtor_trans'
//	if (key_in_foreign_table($selected_id, 'debtor_trans', 'tpe'))
//	{
//		display_error(_("Cannot delete this sale type because customer transactions have been created using //this sales type."));
//	}
//	else
//	{
//		if (key_in_foreign_table($selected_id, 'debtors_master', 'sales_type'))
//		{
//			display_error(_("Cannot delete this sale type because customers are currently set up to use //this sales type."));
//		}
//		else
	delete_discount_pos($selected_id);
	display_notification(_('Selected discount has been deleted'));

//	} //end if sales type used in debtor transactions or in customers set up
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
//	$sav = get_post('show_inactive');
	unset($_POST);
//	$_POST['show_inactive'] = $sav;
}
//----------------------------------------------------------------------------------------------------
$result = get_discounts_pos(check_value('show_inactive'));
start_form();
start_table(TABLESTYLE, "width=30%");
$th = array (_('ID'), _('Discount Type'), _('Discount Description'), _('Discount'), _('Dis. Account'), _('Edit'), _('Delete'));
inactive_control_column($th);
table_header($th);
$k = 0;
//$base_sales = get_discounts();

while ($myrow = db_fetch($result))
{
	alt_table_row_color($k);
	label_cell($myrow["id"]);
	label_cell($myrow["discount_type"]);
	label_cell($myrow["discount_description"]);
	label_cell($myrow["discount"]);
	label_cell($myrow["dis_account"]."-".get_chart_master_discount_pos($myrow["dis_account"]));
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'discount', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();

}
inactive_control_row($th);
end_table();

//display_note(_("Marked sales type is the company base pricelist for prices calculations."), 0, 0, "class='overduefg'");

//----------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		//editing an existing Sales-person
		$myrow = get_discount_pos($selected_id);
		$_POST['discount'] = $myrow["discount"];
        $_POST['dis_account'] = $myrow["dis_account"];
        $_POST['discount_type'] = $myrow["discount_type"];
        $_POST['discount_description'] = $myrow["discount_description"];
	}
	hidden('selected_id', $selected_id);
}
text_row_ex(_("Discount Type").':', 'discount_type', 20);
text_row_ex(_("Discount Description").':', 'discount_description', 20);
text_row_ex(_("Discount").':', 'discount', 20);
gl_all_accounts_list_cells(_("Account:"), 'dis_account', null, false, false, _("Select Account"));

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

?>
