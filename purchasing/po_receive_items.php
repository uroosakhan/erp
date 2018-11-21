<?php

$page_security = 'SA_GRN';
$path_to_root = "..";
include_once($path_to_root . "/purchasing/includes/po_class.inc");

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Receive Purchase Order Items"), false, false, "", $js);

//---------------------------------------------------------------------------------------------------------------

if (isset($_GET['AddedID']))
{
	$grn = $_GET['AddedID'];
	$trans_type = ST_SUPPRECEIVE;

	display_notification_centered(_("Purchase Order Delivery has been processed"));

	display_note(get_trans_view_str($trans_type, $grn, _("&View this Delivery")));
echo"<br>";
    display_note(print_document_link($grn, _("&Print This GRN"), true, $trans_type), 0, 1);

    $clearing_act = get_company_pref('grn_clearing_act');
	if ($clearing_act)	
		display_note(get_gl_view_str($trans_type, $grn, _("View the GL Journal Entries for this Delivery")), 1);

	hyperlink_params("$path_to_root/purchasing/supplier_invoice.php", _("Entry purchase &invoice for this receival"), "New=1");


// 	hyperlink_params("$path_to_root/purchasing/supplier_invoice_import_reg.php", _("Entry purchase Import &invoice for this receival"), "New=1");




	hyperlink_no_params("$path_to_root/purchasing/inquiry/po_search.php", _("Select a different &purchase order for receiving items against"));

	display_footer_exit();
}

//--------------------------------------------------------------------------------------------------

if ((!isset($_GET['PONumber']) || $_GET['PONumber'] == 0) && !isset($_SESSION['PO']))
{
	die (_("This page can only be opened if a purchase order has been selected. Please select a purchase order first."));
}

//--------------------------------------------------------------------------------------------------

function display_po_receive_items()
{	global  $SysPrefs;
	div_start('grn_items');
    start_table(TABLESTYLE, "colspan=7 width='90%'");
	$myrow_1 = get_company_item_pref_from_position(1);
	$myrow_2 = get_company_item_pref_from_position(2);
	$myrow_3 = get_company_item_pref_from_position(3);
	$myrow_4 = get_company_item_pref_from_position(4);
	$myrow_5 = get_company_item_pref_from_position(5);
	$myrow_6 = get_company_item_pref_from_position(6);
	$myrow_7 = get_company_item_pref_from_position(7);
	$myrow_8 = get_company_item_pref_from_position(8);
	$myrow_9 = get_company_item_pref_from_position(9);
	$myrow_10 = get_company_item_pref_from_position(10);
	$myrow_11 = get_company_item_pref_from_position(11);
	$myrow_12 = get_company_item_pref_from_position(12);
	$myrow_13 = get_company_item_pref_from_position(13);
	$myrow_14 = get_company_item_pref_from_position(14);
	$myrow_15 = get_company_item_pref_from_position(15);
	$myrow_16 = get_company_item_pref_from_position(16);
	$myrow_17 = get_company_item_pref_from_position(17);
	$myrow_18 = get_company_item_pref_from_position(18);
	$myrow_19 = get_company_item_pref_from_position(19);
	$myrow_20 = get_company_item_pref_from_position(20);
	$myrow_21 = get_company_item_pref_from_position(21);
	$myrow_22 = get_company_item_pref_from_position(22);

	$pref=get_company_prefs();
	if($pref['batch'] == 1)
	$th = array(_("Item Code"), _("Item Description"),_("Batch"),_("Exp.Date"));
	else
		$th = array(_("Item Code"), _("Item Description"));

	//Text Boxes Headings

	if($myrow_1['purchase_enable']) {
		array_append($th, array($myrow_1['label_value']._("")) );
	}
	if($myrow_2['purchase_enable']) {
		array_append($th, array($myrow_2['label_value']._("")) );
	}
	if($myrow_3['purchase_enable']) {
		array_append($th, array($myrow_3['label_value']._("")) );
	}
	if($myrow_4['purchase_enable']) {
		array_append($th, array($myrow_4['label_value']._("")) );
	}
	if($myrow_5['purchase_enable']) {
		array_append($th, array($myrow_5['label_value']._("")) );
	}
	if($myrow_6['purchase_enable']) {
		array_append($th, array($myrow_6['label_value']._("")) );
	}
	if($myrow_7['purchase_enable']) {
		array_append($th, array($myrow_7['label_value']._("")) );
	}
	if($myrow_8['purchase_enable']) {
		array_append($th, array($myrow_8['label_value']._("")) );
	}
	if($myrow_9['purchase_enable']) {
		array_append($th, array($myrow_9['label_value']._("")) );
	}
	if($myrow_10['purchase_enable']) {
		array_append($th, array($myrow_10['label_value']._("")) );
	}
	if($myrow_11['purchase_enable']) {
		array_append($th, array($myrow_11['label_value']._("")) );
	}
	if($myrow_12['purchase_enable']) {
		array_append($th, array($myrow_12['label_value']._("")) );
	}
	if($myrow_13['purchase_enable']) {
		array_append($th, array($myrow_13['label_value']._("")) );
	}
	if($myrow_14['purchase_enable']) {
		array_append($th, array($myrow_14['label_value']._("")) );
	}
	if($myrow_15['purchase_enable']) {
		array_append($th, array($myrow_15['label_value']._("")) );
	}
	if($myrow_16['purchase_enable']) {
		array_append($th, array($myrow_16['label_value']._("")) );
	}
	if($myrow_17['purchase_enable']) {
		array_append($th, array($myrow_17['label_value']._("")) );
	}
	if($myrow_18['purchase_enable']) {
		array_append($th, array($myrow_18['label_value']._("")) );
	}
	if($myrow_19['purchase_enable']) {
		array_append($th, array($myrow_19['label_value']._("")) );
	}
	if($myrow_20['purchase_enable']) {
		array_append($th, array($myrow_20['label_value']._("")) );
	}
	if($myrow_21['purchase_enable']) {
		array_append($th, array($myrow_21['label_value']._("")) );
	}
	if($myrow_22['purchase_enable']) {
		array_append($th, array($myrow_22['label_value']._("")) );
	}
	    $con_factor = get_company_item_pref('con_factor');

		if($pref['alt_uom'] == 1  && $con_factor['purchase_enable'] == 1) {
			array_append($th, array( _("Ordered"), _("Units"), _("Con Factor"), _("Received"),
				_("Outstanding"), _("This Delivery"),  _("Discount %"),"",  _("Total")) );
		}
		else {
			array_append($th, array(_("Ordered"), _("Units"), _("Received"),
				_("Outstanding"), _("This Delivery"), _("Discount %"),"",  _("Total")));
		}



/*elseif ($SysPrefs->hide_prices_grn() == 1)
    {
        $th = array(_("Item Code"), _("Description"), _("Ordered"), _("Units"), _("Received"),
            _("Outstanding"), _("This Delivery"));
    }
    else
    {
        $th = array(_("Item Code"), _("Description"), _("Ordered"), _("Units"), _("Received"),
            _("Outstanding"), _("This Delivery"), _("Price"), _("Total"));
    }*/
    
    table_header($th);

    /*show the line items on the order with the quantity being received for modification */

    $total = 0;
    $k = 0; //row colour counter

    if (count($_SESSION['PO']->line_items)> 0 )
    {
       	foreach ($_SESSION['PO']->line_items as $ln_itm)
       	{

			alt_table_row_color($k);

    		$qty_outstanding = $ln_itm->quantity - $ln_itm->qty_received;

 			if (!isset($_POST['Update']) && !isset($_POST['ProcessGoodsReceived']) && $ln_itm->receive_qty == 0)
    	  	{   //If no quantites yet input default the balance to be received
    	    	$ln_itm->receive_qty = $qty_outstanding;
    		}
            $pref = get_company_prefs();
            if($pref['disc_in_amount'] == 1) {

                $line_total = (($ln_itm->price - $ln_itm->discount_percent) * $ln_itm->receive_qty );
            }
            else{
                $line_total = ($ln_itm->receive_qty * $ln_itm->price * (1 - $ln_itm->discount_percent));
            }
    		$total += $line_total;

			label_cell($ln_itm->stock_id);
			if ($qty_outstanding > 0)
				text_cells(null, $ln_itm->stock_id . "Desc", $ln_itm->item_description, 30, 50);
			else
				label_cell($ln_itm->item_description);
			
			if($pref['batch'] == 1 && $qty_outstanding > 0){
			    text_cells(null, $ln_itm->line_no."batch", $ln_itm->grn_batch, 30, 50);
			date_cells(null,$ln_itm->line_no."exp_date", $ln_itm->exp_date, 30, 50);
			}
			elseif($pref['batch'] == 1 && $qty_outstanding == 0){
			    label_cells(null,"");
			    label_cells(null,"");
            }

			if($myrow_1['purchase_enable'])
			{
				if($myrow_1['type'] == 1)
					amount_cells(null, $myrow_1['name'], $ln_itm->$myrow_1['name']);
				elseif($myrow_1['type'] == 2)
					combo1_list_cells(null, $myrow_1['name'], $ln_itm->$myrow_1['name']);
				elseif($myrow_1['type'] == 3)
				{	$_POST[$myrow_1['name']]= $ln_itm->$myrow_1['name'];
					date_cells(null, $myrow_1['name']);}
				elseif($myrow_1['type'] == 4)
					text_cells(null, $ln_itm->line_no.$myrow_1['name'], $ln_itm->$myrow_1['name'], 20, 60);
			}
			if($myrow_2['purchase_enable'])
			{
				if($myrow_2['type'] == 1)
					amount_cells(null, $myrow_2['name'], $ln_itm->$myrow_2['name']);
				elseif($myrow_2['type'] == 2)
					combo1_list_cells(null, $myrow_2['name'], $ln_itm->$myrow_2['name']);
				elseif($myrow_2['type'] == 3)
				{	$_POST[$myrow_2['name']] = $ln_itm->$myrow_2['name'];
					date_cells(null, $myrow_2['name']);}
				elseif($myrow_2['type'] == 4)
					text_cells(null, $myrow_2['name'], $ln_itm->$myrow_2['name'], 20, 60);
			}
			if($myrow_3['purchase_enable'])
			{
				if($myrow_3['type'] == 1)
					amount_cells(null, $ln_itm->line_no.$myrow_3['name'], $ln_itm->$myrow_3['name']);
				elseif($myrow_3['type'] == 2)
					combo1_list_cells(null, $myrow_3['name'], $ln_itm->$myrow_3['name']);
				elseif($myrow_3['type'] == 3)
				{	$_POST[$myrow_3['name']] = $ln_itm->$myrow_3['name'];
					date_cells(null, $myrow_3['name']);}
				elseif($myrow_3['type'] == 4)
					text_cells(null, $myrow_3['name'], $ln_itm->$myrow_3['name'], 20, 60);
			}
			if($myrow_4['purchase_enable'])
			{
				if($myrow_4['type'] == 1)
					amount_cells(null, $myrow_4['name'], $ln_itm->$myrow_4['name']);
				elseif($myrow_4['type'] == 2)
					combo1_list_cells(null, $myrow_4['name'], $ln_itm->$myrow_4['name']);
				elseif($myrow_4['type'] == 3)
				{	$_POST[$myrow_4['name']] = $ln_itm->$myrow_4['name'];
					date_cells(null, $myrow_4['name']);}
				elseif($myrow_4['type'] == 4)
					text_cells(null, $myrow_4['name'], $ln_itm->$myrow_4['name'], 20, 60);
			}
			if($myrow_5['purchase_enable'])
			{
				if($myrow_5['type'] == 1)
					amount_cells(null, $myrow_5['name'], $ln_itm->$myrow_5['name']);
				elseif($myrow_5['type'] == 2)
					combo1_list_cells(null, $myrow_5['name'], $ln_itm->$myrow_5['name']);
				elseif($myrow_5['type'] == 3)
				{	$_POST[$myrow_5['name']] = $ln_itm->$myrow_5['name'];
					date_cells(null, $myrow_5['name']);}
				elseif($myrow_5['type'] == 4)
					text_cells(null, $myrow_5['name'], $ln_itm->$myrow_5['name'], 20, 60);
			}
			if($myrow_6['purchase_enable'])
			{
				if($myrow_6['type'] == 1)
					amount_cells(null, $myrow_6['name'], $ln_itm->$myrow_6['name']);
				elseif($myrow_6['type'] == 2)
					combo1_list_cells(null, $myrow_6['name'], $ln_itm->$myrow_6['name']);
				elseif($myrow_6['type'] == 3)
				{	$_POST[$myrow_6['name']] = $ln_itm->$myrow_6['name'];
					date_cells(null, $myrow_6['name']);}
				elseif($myrow_6['type'] == 4)
					text_cells(null, $myrow_6['name'], $ln_itm->$myrow_6['name'], 20, 60);
			}
			if($myrow_7['purchase_enable'])
			{
				if($myrow_7['type'] == 1)
					amount_cells(null, $myrow_7['name'], $ln_itm->$myrow_7['name']);
				elseif($myrow_7['type'] == 2)
					combo1_list_cells(null, $myrow_7['name'], $ln_itm->$myrow_7['name']);
				elseif($myrow_7['type'] == 3)
				{	$_POST[$myrow_7['name']] = $ln_itm->$myrow_7['name'];
					date_cells(null, $myrow_7['name']);}
				elseif($myrow_7['type'] == 4)
					text_cells(null, $myrow_7['name'], $ln_itm->$myrow_7['name'], 20, 60);
			}
			if($myrow_8['purchase_enable'])
			{
				if($myrow_8['type'] == 1)
					amount_cells(null, $myrow_8['name'], $ln_itm->$myrow_8['name']);
				elseif($myrow_8['type'] == 2)
					combo1_list_cells(null, $myrow_8['name'], $ln_itm->$myrow_8['name']);
				elseif($myrow_8['type'] == 3)
				{	$_POST[$myrow_8['name']] = $ln_itm->$myrow_8['name'];
					date_cells(null, $myrow_8['name']);}
				elseif($myrow_8['type'] == 4)
					text_cells(null, $myrow_8['name'], $ln_itm->$myrow_8['name'], 20, 60);
			}
			if($myrow_9['purchase_enable'])
			{
				if($myrow_9['type'] == 1)
					amount_cells(null, $myrow_9['name'], $ln_itm->$myrow_9['name']);
				elseif($myrow_9['type'] == 2)
					combo1_list_cells(null, $myrow_9['name'], $ln_itm->$myrow_9['name']);
				elseif($myrow_9['type'] == 3)
				{	$_POST[$myrow_9['name']] = $ln_itm->$myrow_9['name'];
					date_cells(null, $myrow_9['name']);}
				elseif($myrow_9['type'] == 4)
					text_cells(null, $myrow_9['name'], $ln_itm->$myrow_9['name'], 20, 60);
			}
			if($myrow_10['purchase_enable'])
			{
				if($myrow_10['type'] == 1)
					amount_cells(null, $myrow_10['name'], $ln_itm->$myrow_10['name']);
				elseif($myrow_10['type'] == 2)
					combo1_list_cells(null, $myrow_10['name'], $ln_itm->$myrow_10['name']);
				elseif($myrow_10['type'] == 3)
				{	$_POST[$myrow_10['name']] = $ln_itm->$myrow_10['name'];
					date_cells(null, $myrow_10['name']);}
				elseif($myrow_10['type'] == 4)
					text_cells(null, $myrow_10['name'], $ln_itm->$myrow_10['name'], 20, 60);
			}
			if($myrow_11['purchase_enable'])
			{
				if($myrow_11['type'] == 1)
					amount_cells(null, $myrow_11['name'], $ln_itm->$myrow_11['name']);
				elseif($myrow_11['type'] == 2)
					combo1_list_cells(null, $myrow_11['name'], $ln_itm->$myrow_11['name']);
				elseif($myrow_11['type'] == 3)
				{	$_POST[$myrow_11['name']] = $ln_itm->$myrow_11['name'];
					date_cells(null, $myrow_11['name']);}
				elseif($myrow_11['type'] == 4)
					text_cells(null, $myrow_11['name'], $ln_itm->$myrow_11['name'], 20, 60);
			}
			if($myrow_12['purchase_enable'])
			{
				if($myrow_12['type'] == 1)
					amount_cells(null, $myrow_12['name'], $ln_itm->$myrow_12['name']);
				elseif($myrow_12['type'] == 2)
					combo1_list_cells(null, $myrow_12['name'], $ln_itm->$myrow_12['name']);
				elseif($myrow_12['type'] == 3)
				{	$_POST[$myrow_12['name']] = $ln_itm->$myrow_12['name'];
					date_cells(null, $myrow_12['name']);}
				elseif($myrow_12['type'] == 4)
					text_cells(null, $myrow_12['name'], $ln_itm->$myrow_12['name'], 20, 60);
			}
			if($myrow_13['purchase_enable'])
			{
				if($myrow_13['type'] == 1)
					amount_cells(null, $myrow_13['name'], $ln_itm->$myrow_13['name']);
				elseif($myrow_13['type'] == 2)
					combo1_list_cells(null, $myrow_13['name'], $ln_itm->$myrow_13['name']);
				elseif($myrow_13['type'] == 3)
				{	$_POST[$myrow_13['name']] = $ln_itm->$myrow_13['name'];
					date_cells(null, $myrow_13['name']);}
				elseif($myrow_13['type'] == 4)
					text_cells(null, $myrow_13['name'], $ln_itm->$myrow_13['name'], 20, 60);
			}
			if($myrow_14['purchase_enable'])
			{
				if($myrow_14['type'] == 1)
					amount_cells(null, $myrow_14['name'], $ln_itm->$myrow_14['name']);
				elseif($myrow_14['type'] == 2)
					combo1_list_cells(null, $myrow_14['name'], $ln_itm->$myrow_14['name']);
				elseif($myrow_14['type'] == 3)
				{	$_POST[$myrow_14['name']] = $ln_itm->$myrow_14['name'];
					date_cells(null, $myrow_14['name']);}
				elseif($myrow_14['type'] == 4)
					text_cells(null, $myrow_14['name'], $ln_itm->$myrow_14['name'], 20, 60);
			}
			if($myrow_15['purchase_enable'])
			{
				if($myrow_15['type'] == 1)
					amount_cells(null, $myrow_15['name'], $ln_itm->$myrow_15['name']);
				elseif($myrow_15['type'] == 2)
					combo1_list_cells(null, $myrow_15['name'], $ln_itm->$myrow_15['name']);
				elseif($myrow_15['type'] == 3)
				{	$_POST[$myrow_15['name']] = $ln_itm->$myrow_15['name'];
					date_cells(null, $myrow_15['name']);}
				elseif($myrow_15['type'] == 4)
					text_cells(null, $myrow_15['name'], $ln_itm->$myrow_15['name'], 20, 60);
			}
			if($myrow_16['purchase_enable'])
			{
				if($myrow_16['type'] == 1)
					amount_cells(null, $myrow_16['name'], $ln_itm->$myrow_16['name']);
				elseif($myrow_16['type'] == 2)
					combo1_list_cells(null, $myrow_16['name'], $ln_itm->$myrow_16['name']);
				elseif($myrow_16['type'] == 3)
				{	$_POST[$myrow_16['name']] = $ln_itm->$myrow_16['name'];
					date_cells(null, $myrow_16['name']);}
				elseif($myrow_16['type'] == 4)
					text_cells(null, $myrow_16['name'], $ln_itm->$myrow_16['name'], 20, 60);
			}
			if($myrow_17['purchase_enable'])
			{
				if($myrow_17['type'] == 1)
					amount_cells(null, $myrow_17['name'], $ln_itm->$myrow_17['name']);
				elseif($myrow_17['type'] == 2)
					combo1_list_cells(null, $myrow_17['name'], $ln_itm->$myrow_17['name']);
				elseif($myrow_17['type'] == 3)
				{	$_POST[$myrow_17['name']] = $ln_itm->$myrow_17['name'];
					date_cells(null, $myrow_17['name']);}
				elseif($myrow_17['type'] == 4)
					text_cells(null, $myrow_17['name'], $ln_itm->$myrow_17['name'], 20, 60);
			}
			if($myrow_18['purchase_enable'])
			{
				if($myrow_18['type'] == 1)
					amount_cells(null, $myrow_18['name'], $ln_itm->$myrow_18['name']);
				elseif($myrow_18['type'] == 2)
					combo1_list_cells(null, $myrow_18['name'], $ln_itm->$myrow_18['name']);
				elseif($myrow_18['type'] == 3)
				{	$_POST[$myrow_18['name']] = $ln_itm->$myrow_18['name'];
					date_cells(null, $myrow_18['name']);}
				elseif($myrow_18['type'] == 4)
					text_cells(null, $myrow_18['name'], $ln_itm->$myrow_18['name'], 20, 60);
			}
			if($myrow_19['purchase_enable'])
			{
				if($myrow_19['type'] == 1)
					amount_cells(null, $myrow_19['name'], $ln_itm->$myrow_19['name']);
				elseif($myrow_19['type'] == 2)
					combo1_list_cells(null, $myrow_19['name'], $ln_itm->$myrow_19['name']);
				elseif($myrow_19['type'] == 3)
				{	$_POST[$myrow_19['name']] = $ln_itm->$myrow_19['name'];
					date_cells(null, $myrow_19['name']);}
				elseif($myrow_19['type'] == 4)
					text_cells(null, $myrow_19['name'], $ln_itm->$myrow_19['name'], 20, 60);
			}
			if($myrow_20['purchase_enable'])
			{
				if($myrow_20['type'] == 1)
					amount_cells(null, $myrow_20['name'], $ln_itm->$myrow_20['name']);
				elseif($myrow_20['type'] == 2)
					combo1_list_cells(null, $myrow_20['name'], $ln_itm->$myrow_20['name']);
				elseif($myrow_20['type'] == 3)
				{	$_POST[$myrow_20['name']] = $ln_itm->$myrow_20['name'];
					date_cells(null, $myrow_20['name']);}
				elseif($myrow_20['type'] == 4)
					text_cells(null, $myrow_20['name'], $ln_itm->$myrow_20['name'], 20, 60);
			}
			if($myrow_21['purchase_enable'])
			{
				if($myrow_21['type'] == 1)
					amount_cells(null, $myrow_21['name'], $ln_itm->$myrow_21['name']);
				elseif($myrow_21['type'] == 2)
					combo1_list_cells(null, $myrow_21['name'], $ln_itm->$myrow_21['name']);
				elseif($myrow_21['type'] == 3)
				{	$_POST[$myrow_21['name']] = $ln_itm->$myrow_21['name'];
					date_cells(null, $myrow_21['name']);}
				elseif($myrow_21['type'] == 4)
					text_cells(null, $myrow_21['name'], $ln_itm->$myrow_21['name'], 20, 60);
			}
			
			
			
			if($myrow_22['purchase_enable'])
			{
				// if($myrow_21['type'] == 1)
				// 	amount_cells(null, $myrow_21['name'], $ln_itm->$myrow_21['name']);
				// elseif($myrow_21['type'] == 2)
				// 	combo1_list_cells(null, $myrow_21['name'], $ln_itm->$myrow_21['name']);
				// elseif($myrow_21['type'] == 3)
				// {	$_POST[$myrow_21['name']] = $ln_itm->$myrow_21['name'];
				// 	date_cells(null, $myrow_21['name']);}
				// elseif($myrow_21['type'] == 4)
textarea_cells('','text7'.$line_no, $ln_itm->$myrow_22['name'],$myrow_22["s_width"], 3);

			}
			

			$dec = get_qty_dec($ln_itm->stock_id);
			qty_cell($ln_itm->quantity, false, $dec);
				$con_factor = get_company_item_pref('con_factor');
			if($pref['alt_uom'] == 1 ){
				label_cell($ln_itm->units_id);
				if($con_factor['purchase_enable'] == 1)
				amount_cell($ln_itm->con_factor);
			}
			else {
				label_cell($ln_itm->units);
			}
			qty_cell($ln_itm->qty_received, false, $dec);
			qty_cell($qty_outstanding, false, $dec);

			if ($qty_outstanding > 0)
				qty_cells(null, $ln_itm->line_no, number_format2($ln_itm->receive_qty, $dec), "align=right", null, $dec);
			else
				label_cell(number_format2($ln_itm->receive_qty, $dec), "align=right");


             if ($SysPrefs->hide_prices_grn() == 1)
            {
                hidden($ln_itm->line_no.'discount_percent',$ln_itm->discount_percent);
            }
            else
            {
                $pref = get_company_prefs();
                if($pref['disc_in_amount'] == 1) {
                    $disc = $ln_itm->discount_percent ;
                }
                else{
                    $disc = $ln_itm->discount_percent * 100;
                }
                label_cell($disc);

                amount_decimal_cell($ln_itm->text1);
                hidden($ln_itm->line_no.'discount_percent',$ln_itm->discount_percent);
                amount_cell($line_total);
            }


            //  qty_cells(null, $ln_itm->text1, number_format2($ln_itm->text1, $dec), "align=right", null, $dec);
          //  qty_cells(null, $ln_itm->text2, number_format2($ln_itm->text2, $dec), "align=right", null, $dec);
           
			end_row();
       	}
    }

	$colspan = count($th)-1;

	$display_sub_total = price_format($total /* + input_num('freight_cost')*/);
    if ($SysPrefs->hide_prices_grn() == 1)
    {}
    else {
        start_row();

        echo "<tr>";
        label_cell(_("Discount  :"), "colspan=$colspan align=right");
        small_amount_cells(null, 'discount1', $_SESSION['Items']->discount1);
        echo "<tr>";

        $TotalDiscount = input_num('discount1');
        $display_sub_total_disc = price_format($total  - $TotalDiscount/* + input_num('freight_cost')*/);
        label_row(_("Sub-total"), $display_sub_total_disc , "colspan=$colspan align=right", "align=right");
        $taxes = $_SESSION['PO']->get_taxes(input_num('freight_cost'), true);

        $tax_total = display_edit_tax_items($taxes, $colspan, $_SESSION['PO']->tax_included);

        $display_total = price_format(($total + input_num('freight_cost') + $tax_total )- $TotalDiscount );

        label_cells(_("Amount Total"), $display_total  , "colspan=$colspan align='right'", "align='right'");
    }
    end_row();
    end_table();
	div_end();
}

//--------------------------------------------------------------------------------------------------

function check_po_changed()
{
	/*Now need to check that the order details are the same as they were when they were read
	into the Items array. If they've changed then someone else must have altered them */
	// Compare against COMPLETED items only !!
	// Otherwise if you try to fullfill item quantities separately will give error.
	$result = get_po_items($_SESSION['PO']->order_no);

	$line_no = 0;
	while ($myrow = db_fetch($result))
	{

		$pref=get_company_prefs();
		if($pref['alt_uom'] == 1 ) {
			$item = get_item($myrow["item_code"]);
			$dec = 2;
			if ($myrow['units_id'] != $item['units']) {
				if ($item['con_type'] == 0) {
					$qty = round2($myrow["quantity_ordered"] * $myrow['con_factor'], $dec);
					$quantity_received = round2($myrow["quantity_received"] * $myrow['con_factor'], $dec);
				} else {

					$qty = $myrow['con_factor'] / $myrow["quantity_ordered"];
					$quantity_received = $myrow['con_factor'] / $myrow["quantity_received"];
				}
			}
			else {
				$qty = $myrow["quantity_ordered"];
				$quantity_received = $myrow["quantity_received"];
			}
		}
		else{
			$qty =$myrow["quantity_ordered"];
			$quantity_received =$myrow["quantity_received"];
		}

		$ln_item = $_SESSION['PO']->line_items[$line_no];
		// only compare against items that are outstanding
		$qty_outstanding = $ln_item->quantity - $ln_item->qty_received;
		if ($qty_outstanding > 0)
		{
    		if ($ln_item->qty_inv != $myrow["qty_invoiced"]	||
    			$ln_item->stock_id != $myrow["item_code"] ||
    			$ln_item->quantity != $qty ||
    			$ln_item->qty_received != $quantity_received)
    		{
    			return true;
    		}
		}
	 	$line_no++;
	} /*loop through all line items of the order to ensure none have been invoiced */

	return false;
}

//--------------------------------------------------------------------------------------------------

function can_process()
{
	global $SysPrefs;
	foreach ($_SESSION['PO']->line_items as $order_line)
	{
	     $qty_outstanding = $order_line->quantity - $order_line->qty_received;
		$itm=get_item($order_line->stock_id);
		$pref=get_company_prefs();
		if($pref['batch'] == 1) {
			if ($itm['batch_status'] != 1 && $order_line->grn_batch == "" && $qty_outstanding > 0) {
				display_error(_("Null Batch not allowed."));
				return false;

			}
		}

	}
	
	foreach ($_SESSION['PO']->line_items as $order_line)
	{
		$itm=get_item($order_line->stock_id);
		/*	if($itm['batch_status'] != 1 && $order_line->batch == "" )
		{
			display_error(_("Null Batch not allowed."));
			return false;
}*/
	}
	
	if (count($_SESSION['PO']->line_items) <= 0)
	{
        display_error(_("There is nothing to process. Please enter valid quantities greater than zero."));
    	return false;
	}

	if (!is_date($_POST['DefaultReceivedDate']))
	{
		display_error(_("The entered date is invalid."));
		set_focus('DefaultReceivedDate');
		return false;
	}
	if (!is_date_in_fiscalyear($_POST['DefaultReceivedDate'])) {
		display_error(_("The entered date is out of fiscal year or is closed for further data entry."));
		set_focus('DefaultReceivedDate');
		return false;
	}

	if (!check_reference($_POST['ref'], ST_SUPPRECEIVE))
	{
		set_focus('ref');
		return false;
	}

	$something_received = 0;
	foreach ($_SESSION['PO']->line_items as $order_line)
	{
	  	if ($order_line->receive_qty > 0)
	  	{
			$something_received = 1;
			break;
	  	}
	}

    // Check whether trying to deliver more items than are recorded on the actual purchase order (+ overreceive allowance)
    $delivery_qty_too_large = 0;
	foreach ($_SESSION['PO']->line_items as $order_line)
	{
	  	if ($order_line->receive_qty+$order_line->qty_received >
	  		$order_line->quantity * (1+ ($SysPrefs->over_receive_allowance() / 100)))
	  	{
			$delivery_qty_too_large = 1;
			break;
	  	}
	}

    if ($something_received == 0)
    { 	/*Then dont bother proceeding cos nothing to do ! */
        display_error(_("There is nothing to process. Please enter valid quantities greater than zero."));
    	return false;
    }
    elseif ($delivery_qty_too_large == 1)
    {
    	display_error(_("Entered quantities cannot be greater than the quantity entered on the purchase order including the allowed over-receive percentage") . " (" . $SysPrefs->over_receive_allowance() ."%)."
    		. "<br>" .
    	 	_("Modify the ordered items on the purchase order if you wish to increase the quantities."));
    	return false;
    }
 
 
 		$row = get_company_pref('back_days');
	$row1 = get_company_pref('future_days');
	$row2 = get_company_pref('deadline_time');
	if($row != '')
	{
		$diff   =  date_diff2(date('d-m-Y'),$_POST['DefaultReceivedDate'], 'd');

		if($row == 0)
	
		{
			$allowed_days = 'before yesterday.';
		}
		
		else
			$allowed_days =  'more than '. $row . ' day old' ;

		if($diff > $row  ){

			display_error("You are not allowed to enter entries $allowed_days");
			return false;
		}

//		else
//		{
//			if($diff < 0 )
//			{
//				display_error("You are not allowed to enter data $row day/s ahead");
//				return false;
//			}

		//}

	}

	if($row1 != '')
	{

	$diff_futuredays   =  date_diff2($_POST['DefaultReceivedDate'],date('d-m-Y'), 'd');
			
				if( $diff_futuredays > $row1)
			{
			//	display_error($diff_futuredays);
		display_error("You are not allowed to enter data $row1 day/s ahead");

       return false ;

			}

	}
	if($row2 != '')
	{

		$now = date('h:i:s');

		if($row2 != 0)
		{
			$allowed_time = 'after '. $row2;
		}
		else
			$allowed_time=  '' ;

	if($row2 > $now )
		{
			display_error("You are not allowed to enter data $allowed_time pm");
			return false ;
		}

	}
 
	return true;
}

//--------------------------------------------------------------------------------------------------

function process_receive_po()
{
	global $path_to_root, $Ajax;

	if (!can_process())
		return;

	if (check_po_changed())
	{
		display_error(_("This order has been changed or invoiced since this delivery was started to be actioned. Processing halted. To enter a delivery against this purchase order, it must be re-selected and re-read again to update the changes made by the other user."));

		hyperlink_no_params("$path_to_root/purchasing/inquiry/po_search.php",
		 _("Select a different purchase order for receiving goods against"));

		hyperlink_params("$path_to_root/purchasing/po_receive_items.php", 
			 _("Re-Read the updated purchase order for receiving goods against"),
			 "PONumber=" . $_SESSION['PO']->order_no);

		unset($_SESSION['PO']->line_items);
		unset($_SESSION['PO']);
		unset($_POST['ProcessGoodsReceived']);
		$Ajax->activate('_page_body');
		display_footer_exit();
	}
	
	$grn = &$_SESSION['PO'];
	$grn->orig_order_date = $_POST['DefaultReceivedDate'];
	$grn->reference = $_POST['ref'];
	$grn->Location = $_POST['Location'];
	$grn->ex_rate = input_num('_ex_rate', null);

    $grn->h_text1 = $_POST['h_text1'];
    $grn->h_text2 = $_POST['h_text2'];
    $grn->h_text3 = $_POST['h_text3'];
    $grn->h_comb1 = $_POST['h_comb1'];
    $grn->h_comb2 = $_POST['h_comb2'];
    $grn->h_comb3 = $_POST['h_comb3'];
    $grn->Comments1 = $_POST['Comments1'];
    $grn->Comments2 = $_POST['Comments2'];
    $grn->requisition_no = $_POST['sup_ref'];
    $grn->dc_no = $_POST['dc_no'];
    $grn->discount1 = input_num('discount1');
    
    //$grn->transaction_type = $_POST['transaction_type'];

	$grn_no = add_grn($grn);

	new_doc_date($_POST['DefaultReceivedDate']);
	unset($_SESSION['PO']->line_items);
	unset($_SESSION['PO']);

	meta_forward($_SERVER['PHP_SELF'], "AddedID=$grn_no");
}

//--------------------------------------------------------------------------------------------------

if (isset($_GET['PONumber']) && $_GET['PONumber'] > 0 && !isset($_POST['Update']))
{
	create_new_po(ST_PURCHORDER, $_GET['PONumber']);
	$_SESSION['PO']->trans_type = ST_SUPPRECEIVE;
	$_SESSION['PO']->reference = $Refs->get_next(ST_SUPPRECEIVE, 
		array('date' => Today(), 'supplier' => $_SESSION['PO']->supplier_id));
	copy_from_cart();
}

//--------------------------------------------------------------------------------------------------

if (isset($_POST['Update']) || isset($_POST['ProcessGoodsReceived']))
{

	/* if update quantities button is hit page has been called and ${$line->line_no} would have be
 	set from the post to the quantity to be received in this receival*/
	foreach ($_SESSION['PO']->line_items as $line)
	{
	 if( ($line->quantity - $line->qty_received)>0) {
		$_POST[$line->line_no] = max($_POST[$line->line_no], 0);
		if (!check_num($line->line_no))
			$_POST[$line->line_no] = number_format2(0, get_qty_dec($line->stock_id));

		if (!isset($_POST['DefaultReceivedDate']) || $_POST['DefaultReceivedDate'] == "")
			$_POST['DefaultReceivedDate'] = new_doc_date();
		 $_SESSION['PO']->line_items[$line->line_no]->receive_qty = input_num($line->line_no);
		 $_SESSION['PO']->line_items[$line->line_no]->grn_batch = $_POST[$line->line_no.'batch'];
		 $_SESSION['PO']->line_items[$line->line_no]->exp_date = $_POST[$line->line_no.'exp_date'];
        $_SESSION['PO']->line_items[$line->line_no]->text1 = $_POST[$line->line_no.'text1'];
         $_SESSION['PO']->line_items[$line->line_no]->text2 = $_POST["text2"];
         $_SESSION['PO']->line_items[$line->line_no]->text3 = $_POST["text3"];
         $_SESSION['PO']->line_items[$line->line_no]->text4 = $_POST["text4"];
         $_SESSION['PO']->line_items[$line->line_no]->text5 = $_POST["text5"];
         $_SESSION['PO']->line_items[$line->line_no]->text6 = $_POST["text6"];

         
        $_SESSION['PO']->line_items[$line->line_no]->text7 =$_POST["text7"];

		 $_SESSION['PO']->line_items[$line->line_no]->amount1 = $_POST[ "amount1"];
		 $_SESSION['PO']->line_items[$line->line_no]->amount2 = $_POST[ "amount2"];
		// $_SESSION['PO']->line_items[$line->line_no]->amount3 = $_POST[ "amount3"];
		 $_SESSION['PO']->line_items[$line->line_no]->amount4 = $_POST[ "amount4"];
		 $_SESSION['PO']->line_items[$line->line_no]->amount5 = $_POST[ "amount5"];
		 $_SESSION['PO']->line_items[$line->line_no]->amount6 = $_POST[ "amount6"];
		 $_SESSION['PO']->line_items[$line->line_no]->date1 = $_POST[ "date1"];
		 $_SESSION['PO']->line_items[$line->line_no]->date2 = $_POST[ "date2"];
		 $_SESSION['PO']->line_items[$line->line_no]->date3 = $_POST[ "date3"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo1 = $_POST[ "combo1"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo2 = $_POST[ "combo2"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo3 = $_POST[ "combo3"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo4 = $_POST[ "combo4"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo5 = $_POST[ "combo5"];
		 $_SESSION['PO']->line_items[$line->line_no]->combo6 = $_POST[ "combo6"];


		   $_SESSION['PO']->line_items[$line->line_no]->amount3 = $_POST[$line->line_no.'amount3'];
         $_SESSION['PO']->line_items[$line->line_no]->discount_percent = $_POST[$line->line_no.'discount_percent'];


		if (isset($_POST[$line->stock_id . "Desc"]) && strlen($_POST[$line->stock_id . "Desc"]) > 0)
		{
			$_SESSION['PO']->line_items[$line->line_no]->item_description = $_POST[$line->stock_id . "Desc"];
		}
	 }
	}
	$Ajax->activate('grn_items');
}

//--------------------------------------------------------------------------------------------------

if (isset($_POST['ProcessGoodsReceived']))
{
	process_receive_po();
}

//--------------------------------------------------------------------------------------------------

start_form();

edit_grn_summary($_SESSION['PO'], true);
display_heading(_("Items to Receive"));
display_po_receive_items();

echo '<br>';
submit_center_first('Update', _("Update"), '', true);
submit_center_last('ProcessGoodsReceived', _("Process Receive Items"), _("Clear all GL entry fields"), 'default');

end_form();

//--------------------------------------------------------------------------------------------------
end_page();
