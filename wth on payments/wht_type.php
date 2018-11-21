<?php
/**
 * Created by PhpStorm.
 * User: sheikh_salman
 * Date: 5/18/16
 * Time: 10:16 AM
 */

$page_security = 'SA_SUPPLIER';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "WHT Types"));

include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/sales/includes/db/wht_types_db.inc");

simple_page_mode(true);

$parent_id = get_post('parent_id', 0);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if(get_post('parent_id') == 0){
		$input_error = 1;
		display_error(_("Head is not selected."));
		set_focus('parent_id');

	}
	else if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The wht type description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			update_wht_type($selected_id, $_POST['description'], input_num('tax_percent'), $_POST['co_account'],$_POST['wth_tax_category']);
			$note = _('Selected wht type has been updated');
		}
		else
		{
			add_wht_type($_POST['description'], input_num('tax_percent'), $_POST['co_account'],
				$_POST['parent_id'],$_POST['wth_tax_category']);
			$note = _('New wht type has been added');
		}

		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*  if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
      {
          $cancel_delete = 1;
          display_error(_("Cannot delete this group because customers have been created using this group."));
      }*/
	if ($cancel_delete == 0)
	{
		delete_wht_type($selected_id);
		display_notification(_('Selected wht type has been deleted'));
	} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------

$result = get_wht_types();

start_form();
start_table(TABLESTYLE, "width=80%", 2, 0, true);
$th = array(/*_("ID"),*/ _("WHT Type"), _("Head"),_("%"), _("A/C"), _("WHT Tax Type "), "");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

//	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
	label_cell($myrow["head"]);
	label_cell($myrow["tax_percent"]." %");
	$acc = get_gl_account($myrow["co_account"]);
	label_cell($myrow["co_account"]." - ".$acc["account_name"]);
	label_cell(get_wht_tax_category_name($myrow["wth_tax_category"]));
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'wth_tax_types', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	// delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2, "", 2, 0, true);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_wht_type($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['tax_percent']  = $myrow["tax_percent"];
		$_POST['co_account']  = $myrow["co_account"];


	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
	hidden('parent_id',$myrow["id"]);
}

if ($selected_id == -1) {
	wth_tax_type_list_row(_("Tax Head :"), 'parent_id', null,
		_("Select Tax Type"), true, true); //asad


}
wht_tax_list_row('WTH Tax Type','wth_tax_category');
text_row_ex( _("Name:"), 'description', 30);
amount_row(_("% :"), 'tax_percent');
gl_all_accounts_list_row(_("COA:"), 'co_account');


end_table(1);

//if ($selected_id != -1)
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
