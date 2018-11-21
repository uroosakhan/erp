<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Gate Pass"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

//	if (strlen($_POST['description']) == 0)
	{
//		$input_error = 1;
//		display_error(_("The area description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{


			add_gate_pass($_POST['id'],$_POST['dimension_id'],$_POST['spare_enable'],
				$_POST['owners_enable'],$_POST['cigrette_enable'],$_POST['floor_enable'],
				$_POST['tool_enable'],$_POST['remote_enable'],$_POST['number_enable'],
				$_POST['keysqty_enable'],$_POST['warranty_enable'],$_POST['card_enable'],
				$_POST['utility_enable'],$_POST['address_enable'],
				$_POST['tel_enable'],$_POST['receiver_enable'],$_POST['reg_enable']);
			$note = _('New Gate Pass has been added');

    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this area because customer branches have been created using this area."));
	} 
	if ($cancel_delete == 0) 
	{
		delete_gate_pass($selected_id);

		display_notification(_('Selected sales area has been deleted'));
	} //end if Delete area
	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}



function gate_pass_dimension1($dimension_id)
{
  $sql = "SELECT COUNT(*) as TOTAL FROM 0_gate_pass1 WHERE dimension_id =". db_escape($dimension_id)."";

	$result = db_query($sql, "Cannot retreive a debtor transaction");

	return db_fetch($result);
}
$myrow=gate_pass_dimension1($_GET['dimension_id']);
if($myrow['TOTAL'] != 0)
{
    display_error("Duplicate entry against this Dimension");
        display_footer_exit();

}

//-------------------------------------------------------------------------------------------------

$result = get_sales_areas(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

//$th = array(_("Area Name"), "", "");
inactive_control_column($th);

//table_header($th);
$k = 0; 
//
//while ($myrow = db_fetch($result))
//{
//
//	alt_table_row_color($k);
//
////	label_cell($myrow["description"]);
//
////	inactive_control_cell($myrow["area_code"], $myrow["inactive"], 'areas', 'area_code');
//
//// 	edit_button_cell("Edit".$myrow["area_code"], _("Edit"));
//// 	delete_button_cell("Delete".$myrow["area_code"], _("Delete"));
//	end_row();
//}
//
//inactive_control_row($th);
end_table();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

//if ($selected_id != -1)
//{
// 	if ($Mode == 'Edit') {
//		//editing an existing area
////		$myrow = get_sales_area($selected_id);
//
////		$_POST['description']  = $myrow["description"];
//	}
////	hidden("selected_id", $selected_id);
//}

label_cells(_("Dimension: "),$_GET['dimension_id'], true, ' ', false, 1, false);
//customer_list_cells(_("Select a customer: "), tomer_id'], true, true);
hidden('dimension_id',$_GET['dimension_id']);
table_section_title(_("Gate Pass"));

echo"<tr>";
check_cells(_("Spare Wheel"), 'spare_enable', $_POST['spare_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Owner's Manual"), 'owners_enable', $_POST['owners_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Cigrette Lighter/Ash Tray"), 'cigrette_enable', $_POST['cigrette_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Floor Mats"), 'floor_enable', $_POST['floor_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Tool Kit With Jack"), 'tool_enable', $_POST['tool_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Remote Of Player"), 'remote_enable', $_POST['remote_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("2 Number Plates(Original/Temporary)"), 'number_enable', $_POST['number_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Keys(Qty)"), 'keysqty_enable', $_POST['keysqty_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Warranty Book"), 'warranty_enable', $_POST['warranty_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Warranty Card Of Battery"), 'card_enable', $_POST['card_enable']);
echo"</tr>";
echo"<tr>";
check_cells(_("Utility Packages"), 'utility_enable', $_POST['utility_enable']);
echo"</tr>";
echo"<tr>";
echo"<tr>";
text_cells(_("Receiver's Name"), 'receiver_enable', null, 50, 50);

echo"</tr>";
echo"<tr>";

echo"<tr>";
text_cells(_("Receiver Tel#"), 'tel_enable', null, 50, 50);
echo"</tr>";
echo"<tr>";

echo"<tr>";
text_row_ex(_("Address"), 'address_enable', 50);
echo"</tr>";
echo"<tr>";


echo"<tr>";
text_row_ex(_("Reg #"), 'reg_enable', 50);
echo"</tr>";
echo"<tr>";
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
