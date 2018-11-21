<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Offers"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

//--------------------marina setup forms for offers------------------//
function add_offers($description, $line_id)
{
    $sql = "INSERT INTO ".TB_PREF."offers (description, line_id) VALUES (".db_escape($description) .",
    ".db_escape($line_id). ")";
    db_query($sql,"The offers could not be added");
}

function update_offers($selected_id, $description)
{
    $sql = "UPDATE ".TB_PREF."offers SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
    db_query($sql,"The offers could not be updated");
}

function delete_offers($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."offers WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete offers");
}


function get_offers($show_inactive, $line_id)
{
    $sql = "SELECT * FROM ".TB_PREF."offers";
    $sql .= " WHERE line_id = ".db_escape($line_id);
    if (!$show_inactive) $sql .= " AND !inactive";

    return db_query($sql,"could not get offers");
}

function get_offer($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."offers WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get offer");
    return db_fetch($result);
}
function get_offer_details_($line_id)
{
    $sql = "SELECT *, code as description 
            FROM ".TB_PREF."offer_details offers , ".TB_PREF."sales_offers sales
            WHERE offers.stock_id = sales.id 
            AND offers.id = ".db_escape($line_id);
    $result = db_query($sql,"could not get offer");
    return db_fetch($result);
}

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{
	$input_error = 0;
	if (strlen($_POST['description']) == 0) 
	{
		$input_error = 1;
		display_error(_("The offers description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) {
    		update_offers($selected_id, $_POST['description']);
			$note = _('Selected offers has been updated');
    	} else {
    		add_offers($_POST['description'], $_POST['line_id']);
			$note = _('New offers has been added');
    	}
        meta_forward($_SERVER['PHP_SELF'], 'line_id='.$_POST['line_id']);
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
    $cancel_delete = 0;
    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
//    if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
//    {
//        $cancel_delete = 1;
//        display_error(_("Cannot delete this area because customer branches have been created using this area."));
//    }
    if ($cancel_delete == 0)
    {
        delete_offers($selected_id);

        display_notification(_('Selected offer has been deleted'));
    } //end if Delete area
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    $selected_id = -1;
    $sav = get_post('show_inactive');
//    unset($_POST);
    $_POST['show_inactive'] = $sav;
}

//-------------------------------------------------------------------------------------------------

$result = get_offers(check_value('show_inactive'), $_GET['line_id']);

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Offers Code"), _("Description"), _("Item Description"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
    $OfferDetails = get_offer_details_($myrow['line_id']);
    $ItemName = get_description_name($myrow["description"]);
	alt_table_row_color($k);
    label_cell($OfferDetails["offer_code"]);

    label_cell($OfferDetails['description']);
	label_cell($ItemName);
	inactive_control_cell($myrow["id"], $myrow["inactive"], 'offers', 'id');
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
		$myrow = get_offer($selected_id);

		$_POST['description']  = $myrow["description"];
	}
	hidden("selected_id", $selected_id);
}
hidden("line_id", $_GET['line_id']);
sales_items_list_cells("Items :",'description', null, false, true, true);
//text_row_ex(_("Offer Name:"), 'description', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
