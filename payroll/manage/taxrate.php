<?php
$page_security = 'SA_PAYROLL_TAX_SETUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Tax Rates"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	//$input_error = 0;

	function nBetween($varToCheck, $high, $low) {
		
		if($varToCheck < $low) return false;
		if($varToCheck > $high) return false;
		return true;
	}
	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
			update_tax($selected_id,$_POST['minamount'],$_POST['maxamount'],$_POST['taxrate'],$_POST['fixamount']);
			$note = _('Selected amount has been updated');
    	} 
    	else 
    	{
			add_tax( $_POST['minamount'],$_POST['maxamount'],$_POST['taxrate'],$_POST['fixamount']);
			$note = _('New values has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
/*
	if (key_in_foreign_table($selected_id, 'industry', 'name'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this area because customer branches have been created using this area."));
	} */
	if ($cancel_delete == 0) 
	{
		delete_tax($selected_id);

		display_notification(_('Selected value has been deleted'));
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

$result = get_tax1(check_value('show_inactive'));

start_form();
start_table(TABLESTYLE, "width=30%");

$th =  array(_("MIN AMOUNT"),_("MAX AMOUNT"), _("TAX RATE"),_("FIXED AMOUNT"), "","");
inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);



	label_cell($myrow["minamount"]);
	label_cell($myrow["maxamount"]);
	label_cell($myrow["taxrate"]);
	label_cell($myrow["fixamount"]);


	inactive_control_cell($myrow["id"], $myrow["inactive"], 'taxrate', 'minamount');

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
		$myrow = get_tax($selected_id);


		$_POST['minamount']  = $myrow["minamount"];
		$_POST['maxamount']  = $myrow["maxamount"];
		$_POST['taxrate']  = $myrow["taxrate"];
		$_POST['fixamount']  = $myrow["fixamount"];

	}
	hidden("selected_id", $selected_id);
} 


text_row_ex(_("Min Amount:"), 'minamount', 30);
text_row_ex(_("Max Amount:"), 'maxamount', 30);
text_row_ex(_("Tax Rate:"), 'taxrate', 30);
text_row_ex(_("Fixed Amount:"), 'fixamount', 30);

end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
