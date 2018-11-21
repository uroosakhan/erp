<?php

$page_security = 'SA_SALESGROUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Sales Groups"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
$userid = $_SESSION["wa_current_user"]->user;
//display_error($userid);
if (isset($_POST['update']))
{

	$input_error = 0;

	if (strlen($_POST['item_code1']) == 0)
	{
		$input_error = 1;
		display_error(_("The Item Code 1 cannot be empty."));
		set_focus('item_code1');
	}
    if (strlen($_POST['item_code2']) == 0)
    {
        $input_error = 1;
        display_error(_("The Item Code 2 cannot be empty."));
        set_focus('item_code2');
    }

//    $myrow=get_sales_group_neww($_POST['item_code1']);
//
//    if(!$myrow['stock_id'] )
//    {
//        $input_error = 1;
//        display_error("This item code does not exist");
//        set_focus('item_code1');
//    }

	if ($input_error != 1)
	{
        $check_item=get_sales_group_neww($_POST['item_code1']);
        
        if($check_item!='')
        {
            update_stock_id_neww($_POST['item_code1'], $_POST['item_code2']);
            update_stock_moves_neww($_POST['item_code1'], $_POST['item_code2']);
            update_item_codes_neww($_POST['item_code1'], $_POST['item_code2']);
            update_dbtr_trans_det_neww($_POST['item_code1'], $_POST['item_code2']);
            update_pur_order_det_neww($_POST['item_code1'], $_POST['item_code2']);

            update_grn_items_neww($_POST['item_code1'], $_POST['item_code2']);
            update_supp_invoice_items_neww($_POST['item_code1'], $_POST['item_code2']);
            update_prices_neww($_POST['item_code1'], $_POST['item_code2']);
            update_features_price_neww($_POST['item_code1'], $_POST['item_code2']);
            update_purch_data_neww($_POST['item_code1'], $_POST['item_code2']);
            update_workorders_neww($_POST['item_code1'], $_POST['item_code2']);
            update_wo_issue_items_neww($_POST['item_code1'], $_POST['item_code2']);
            update_wo_requirements_neww($_POST['item_code1'], $_POST['item_code2']);
            update_sales_order_details_neww($_POST['item_code1'], $_POST['item_code2']);
            insert_item_code($_POST['item_code1'], $_POST['item_code2'],$userid);

            $note = _('Selected itemhas been updated');
            display_notification($note);
        }
        else{

            display_error("Selected item not availabe in database".$_POST['item_code1']);
        }




		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

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


start_form();

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);
stock_items_list_cells(_("Select an item:"), 'item_code1', null,
    _('New item'), true, check_value('show_inactive'), false, array('fixed_asset' => get_post('fixed_asset')));


//text_row_ex(_("Item  Code 1:"), 'item_code1', 30);
text_row_ex(_("Item  Code 2:"), 'item_code2', 30);

end_table(1);

submit_center_first('update', _("Update Item Code"),
    _('Update customer data'), @$_REQUEST['popup'] ? true : 'default');

end_form();

end_page();
