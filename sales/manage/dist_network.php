<?php

$page_security = 'SA_SALESMAN';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Distribution Network"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//------------------------------------------------------------------------------------------------


//--------------------marina setup forms for dist_network------------------//
function add_dist_network($description, $offer_id, $customer_id)
{
    $sql = "INSERT INTO ".TB_PREF."dist_network (description, offer_id, customer_id)
		VALUES (".db_escape($description) . ", "
        .db_escape($offer_id) . ", "
        .db_escape($customer_id) . ")";
    db_query($sql,"The insert of the distribution network failed");
}

function update_dist_network($selected_id, $description, $offer_id, $customer_id)
{
    $sql = "UPDATE ".TB_PREF."dist_network SET description=".db_escape($description) . ",
		offer_id=".db_escape($offer_id) . "
		customer_id=".db_escape($customer_id) . "
		WHERE id = ".db_escape($selected_id);
    db_query($sql,"The update of the distribution network failed");
}

function delete_dist_network($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."dist_network WHERE id=".db_escape($selected_id);
    db_query($sql,"The distribution network could not be deleted");
}

function get_dist_network($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."dist_network";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get distribution networks");
}

function get_dist_networks($selected_id)
{
    $sql = "SELECT *  FROM ".TB_PREF."dist_network WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get distribution networks");
    return db_fetch($result);
}


if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//initialise no input errors assumed initially before we test
	$input_error = 0;

	if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The distribution network name cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
    		/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_dist_network($selected_id, $_POST['description'], $_POST['offer_id'], $_POST['customer_id']);
    	}
    	else
    	{
    		/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
            add_dist_network($_POST['description'], $_POST['offer_id'], $_POST['customer_id']);
    	}

    	if ($selected_id != -1) 
			display_notification(_('Selected distribution network data have been updated'));
		else
			display_notification(_('New distribution network data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
        delete_dist_network($selected_id);
		display_notification(_('Selected distribution network data have been deleted'));

	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

function get_offer_details_name($id)
{
    $sql = "SELECT CONCAT(offer_code, ' ', title) as Name FROM ".TB_PREF."offer_details WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get offer");
    $row= db_fetch_row($result);
    return $row[0];
}

//------------------------------------------------------------------------------------------------

$result = get_dist_network(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width='60%'");
$th = array(_("ID"),_("Description"), _("Offers"), "", "");
inactive_control_column($th);
table_header($th);

$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

    label_cell($myrow["id"]);
    label_cell($myrow["description"]);
	label_cell(get_offer_details_name($myrow["offer_id"]));
	inactive_control_cell($myrow["id"], $myrow["inactive"],
		'dist_network', 'id');
 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
  	end_row();

} //END WHILE LIST LOOP

inactive_control_row($th);
end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing Sales-person
		$myrow = get_dist_networks($selected_id);
		$_POST['description'] = $myrow["description"];
		$_POST['offer_id'] = $myrow["offer_id"];
	}

	hidden('selected_id', $selected_id);

}

start_table(TABLESTYLE2);

hidden('customer_id', $_GET['customer_id']);

distributor_profile_list_row(_("Distribution Network Name:"), 'description', null);
offers_list_row(_("Offers:"), 'offer_id', null);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

