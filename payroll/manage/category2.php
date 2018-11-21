<?php
$page_security = 'SA_OPEN';
$path_to_root = "../..";
include($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include_once("../includes/db/attendance_db.inc");
include_once($path_to_root . "/payroll/includes/db/month_db.inc"); //
include_once($path_to_root . "/modules/payroll/includes/ui/ui_lists.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/payroll/includes/db/gl_setup_db.inc"); //
$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "Attendance Sheet"), @$_REQUEST['popup'], false, "", $js);

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/contacts_view.inc");

simple_page_mode(true);


$f_year = get_current_fiscalyear();

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The Category description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			/*update_cat2($selected_id, $_POST['description'],$_POST['company_artical'],$_POST['price'],$_POST['gender'],$_POST['stype_id'],$_POST['item_cost'],$_POST['category_id']);
            $note = _('Selected Category has been updated');*/
		}
		else
		{
			add_attendance_neww($_POST['employee_id' . $i], $_POST['employee_dept'],
				$_POST['month'], $_POST['date'], $f_year['id'], $_POST['division'], $_POST['location'], $check, $chkh, 
				$checkout, $chkhout, $sick);
			display_error(_("Attendance Marked"));
		}

		display_notification($note);
		$Mode = 'RESET';
	}
}


//if ($Mode == 'Delete')
//{
	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'emp_attendance', 'empl_id'))
//	{
//		$cancel_delete = 1;
//		display_error(_("Cannot delete this group because items have been created using this group."));
//	}
	if ($_POST['delete'] == 0)
	{
//display_error($_POST['delete']);
		delete_attendances($_POST['month'],$_POST['f_year'],$_POST['division'], $_POST['project'],$_POST['location']);


		//display_notification(_('Selected Category has been deleted'));
	} //end if Delete group
//	$Mode = 'RESET';
//}
//if (isset($_POST['BatchInvoice']))
//{
////$del_count = 0;
//
////	die;
//	foreach($_POST['Sel_'] as $delivery => $branch) {
//		display_error($checkbox."fgfd");
//		$checkbox = 'Sel_' . $delivery;
//		if (check_value($checkbox)) {
//
////		delete_attendance_neww($_POST['employee_id' ]);
//		}
//	}
//}
if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------

$Ajax->activate('orders_tbl');

if (!@$_GET['popup'])
	start_form();

start_table(TABLESTYLE_NOBORDER);
start_row();

//if (!@$_GET['popup'])
//	if ($trans_type == ST_SALESQUOTE)
//		check_cells(_("Show All:"), 'show_all');

//hidden('order_view_mode', $_POST['order_view_mode']);
//hidden('type', $trans_type);

end_row();

end_table(1);
//-----------------------------------------


start_form();
start_table(TABLESTYLE2);
/*if ($selected_id != -1)
{
 	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_cat2($selected_id);
		$_POST['description']  = $myrow["description"];
		$_POST['company_artical']  = $myrow["company_artical"];
        $_POST['price']  = $myrow["price"];
        $_POST['gander']  = $myrow["gander"];
        $_POST['stype_id']  = $myrow["stype_id"];
	    $_POST['item_cost']  = $myrow["item_cost"];
		$_POST['category_id']  = $myrow["category_id"];
	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
	hyperlink_params_td( $myrow["id"],'<b>'.$name.'</b>');
} */
text_row_ex(_("Artical : "), 'description', 30);
text_row_ex(_("Company Article : "), 'company_artical', 30);
text_row_ex(_("Artical Price : "), 'price', 30);
text_row_ex(_("Item Cost : "), 'item_cost', 30);
/*if ($selected_id != -1)  {
	start_row();
	echo '<td class="label">'._('Image').':</td>';
	hyperlink_params_td($path_to_root . "/sales/manage/cust_pic.php",
		'<b>'. (@$_REQUEST['popup'] ?  _("Select or &Add") : _("&Select Image ")).'</b>',
		"id=".$selected_id.(@$_REQUEST['popup'] ? '&popup=1':''));
	end_row();
}*/





end_table(1);

start_table(TABLESTYLE2);
submit_add_or_update_center($selected_id == -1, '', 'both');


dimensions_list_cells(_("Division"), 'division', null, 'All division', "", false, 1,true);
pro_list_cells(_("Project"), 'project',$_POST['project'], 'All Projects', "", false, 2,true,$_POST['division']);
loc_list_cells(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);

month_list_cells( null, 'month', null,  _('Month Entry'), true, check_value('show_inactive'));
submit_cells('SearchOrders', _("Search"),'',_('Select documents'), 'default');


end_table(1);
start_table(TABLESTYLE, "width=50%");

submit_cells('delete', _("Delete"),'',_('Select documents'), 'default');

$th = array(_("ID"), _("Artical"), _("Company Artical"), _("Price"), _("Gender"), "", 
	_("")
);
//inactive_control_column($th);

table_header($th);
$k = 0;






$result = get_employee_attendence($_POST['division'],$_POST['project'],$_POST['location'],$_POST['month'],$f_year['id']);

while ($myrow = db_fetch($result))
{
	alt_table_row_color($k);
	label_cell(get_employee_namee_new($myrow["empl_id"]));
	label_cell($myrow["att_date"]);
	label_cell($myrow["check_in"]);
	label_cell($myrow["check_out"]);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'cat2', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	//delete_button_cell("Delete".$myrow["id"], _("Delete"));

//echo "<input type=\"checkbox\" />";
	//check_cells(null,'batch_check',0,true);
	end_row();
}

$table =& new_db_pager('orders_tbl', $sql, $cols);
//$table->set_marker('check_overdue', _("Marked items are overdue."));
inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);






end_table(1);



end_form();

end_page();
?>
<script type="text/javascript">
	$('#selectAll').change(function() {
//		alert(123);
		$('batch_check').attr('checked', true);
	});
</script>

