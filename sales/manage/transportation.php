<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Transportation"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

//-----------------------for transportation------------------------------//
function add_transportation($name, $address, $contact_no)
{
    $sql = "INSERT INTO ".TB_PREF."transportation (name, address, contact_no) 
    VALUES (".db_escape($name) . ",
    ".db_escape($address) . ",
    ".db_escape($contact_no) . ")";
    db_query($sql,"The transportation data could not be added");
}

function update_transportation($selected_id, $name, $address, $contact_no)
{
    $sql = "UPDATE ".TB_PREF."transportation SET name=".db_escape($name).",
     address=".db_escape($address).",
     contact_no=".db_escape($contact_no)."
     WHERE id = ".db_escape($selected_id);
    db_query($sql,"The transportation data could not be updated");
}

function delete_transportation($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."transportation WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete transportation data");
}

function get_transportation($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."transportation";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get transportation data");
}

function get_transport($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."transportation WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get transportation data");
    return db_fetch($result);
}

//-----------------------for transportation------------------------------//

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

	if (strlen($_POST['name']) == 0)
	{
		$input_error = 1;
		display_error(_("The transportation name cannot be empty."));
		set_focus('name');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
            update_transportation($selected_id, $_POST['name'], $_POST['address'], $_POST['contact_no']);
			$note = _('Selected transportation data has been updated');
    	} 
    	else
    	{
            add_transportation($_POST['name'], $_POST['address'], $_POST['contact_no']);
			$note = _('New transportation data has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	if ($cancel_delete == 0) 
	{
        delete_transportation($selected_id);

		display_notification(_('Selected transportation data has been deleted'));
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

//-------------------------------------------------------------------------------------------------

$result = get_transportation(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("ID"),_("Name"),_("Address"),_("Contact No"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell($myrow["id"]);
	label_cell($myrow["name"]);
	label_cell($myrow["address"]);
	label_cell($myrow["contact_no"]);

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'transportation', 'id');

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}
	
inactive_control_row($th);
end_table();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing area
		$myrow = get_transport($selected_id);

		$_POST['id']  = $myrow["id"];
		$_POST['name']  = $myrow["name"];
		$_POST['address']  = $myrow["address"];
		$_POST['contact_no']  = $myrow["contact_no"];
	}
	hidden("selected_id", $selected_id);
} 

text_row_ex(_("Transportation Name:"), 'name', 30, 200);
text_row_ex(_("Transportation Address:"), 'address', 30, 200);
text_row_ex(_("Contact No:"), 'contact_no', 30, 20);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
