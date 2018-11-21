<?php

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/ui/wo_items_cart.inc");

//--------------------------------------------------------------------------------

function add_to_order(&$order, $new_item, $new_item_qty,
					  $loc_code1, $workcentre_added1, $issue_qty,$amount33)
{


    if ($order->find_cart_item($new_item))
         display_error(_("For Part :") . $new_item . " " . "This item is already on this document. You can change the quantity on the existing line if necessary.");
	else
         $order->add_to_cart (count($order->line_item_wo), $new_item, $new_item_qty,
			 $loc_code1, $workcentre_added1, $issue_qty,$amount33);
	
}

//--------------------------------------------------------------------------------

function display_order_header(&$order)
{
	global $Refs, $Ajax;

	start_outer_table(TABLESTYLE2, "width='55%'"); // outer table
//	table_section(1);
//	end_outer_table(1); // outer table
	table_section(1);
    if($order->Editable == 0)
	    ref_row(_("Reference:"), 'wo_ref', '', $Refs->get_next(ST_MANUORDERREQ));
    else
        label_cells(_("Reference:"), $_POST['wo_ref']);

	wo_types_list_row(_("Type:"), 'type_', null);
	stock_manufactured_items_list_row(_("Item:"), 'stock_id_', null, true, true);
//	if (list_updated('stock_id') || isset($_POST['Update12']))
//		$Ajax->activate('quantity');
//		$Ajax->activate('bom');
//		$Ajax->activate('hi');

	locations_list_row(_("Destination Location:"), 'StockLocation', null);

	global  $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IMEC' ||$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO' ||$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='NKR'
	||$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CHI'){

		sales_order_cells(_("Sales Order Reference:"), 'sale_order', null, true, false);

	}
	if (!isset($_POST['quantity']))
		$_POST['quantity'] = qty_format(1, $_POST['stock_id'], $dec);
	else
		$_POST['quantity'] = qty_format($_POST['quantity'], $_POST['stock_id'], $dec);





	global  $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BNT' ||
		$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' ||
		$db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO') {
		$myrow_1 = get_company_item_pref_from_position(1);
		$myrow_2 = get_company_item_pref_from_position(2);
		$myrow_3 = get_company_item_pref_from_position(3);
		$myrow_4 = get_company_item_pref_from_position(4);
		$myrow_5 = get_company_item_pref_from_position(5);
		$myrow_6 = get_company_item_pref_from_position(6);
		$item_info = get_item_edit_info($_POST['stock_id']);
		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);
		if (!isset($_POST['amount3'])) {
			$_POST['amount3'] = price_format($item_info["amount3"]);
		}
		$formula = $item_info["formula"];
		$value = explode(",", $formula);
		$maxs = max(array_keys($value));
		$amount3 = $value[0];
		$amount2 = $value[2];


		if ($formula != '') {
			$b = $_POST[$amount3] . "" . $value[1] . "" . $_POST[$amount2];

			$_POST['quantity'] = eval('return ' . $b . ';');
		}
		echo "<tr>";
		echo "<td>";
		ref_row($myrow_3['label_value'], $myrow_3['name'], null, null, true);
		echo "</td>";
		echo "</tr>";
	}
	if ($_POST['type_'] == WO_ADVANCED) {

		if ($_POST['released'])
			label_row(_("Quantity Manufactured:"), number_format($_POST['units_issued'], get_qty_dec($_POST['stock_id'])));
		date_row(_("Date") . ":", 'date_', '', true);
	} else {
		ref_cells("Quantity:", 'quantity', null, null, null, true);
		date_row(_("Date") . ":", 'date_', '', true);

	}
    textarea_row(_("Memo"), 'memo_', null, 50, 3);
//	table_section(1, "50%");
//	ref_row(_("Reference:"), 'ref', '', $Refs->get_next(ST_INVADJUST, null, array('location'=>get_post('StockLocation'), 'date'=>get_post('AdjDate'))),
//		 false, ST_INVADJUST);
//
//	yesno_list_row("Adjustment Type",'adj_type',null,"Negative","Positive",true);

	end_outer_table(1); // outer table
}

//---------------------------------------------------------------------------------

function display_adjustment_items($title, &$order)
{
	display_heading($title);
    div_start('items_table');
	start_table(TABLESTYLE, "width='100%'");
	global  $db_connections;
	$myrow_3 = get_company_item_pref_from_position(3);
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'
		|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO') {
		$th = array(_("Item Code"),
			_("Item Description"), _("Location"),
			_("Work Center"), ($myrow_3['label_value']),_("Quantity"), _("Issue Qty."), _("Qty x I.Qty"), _("Units"), "");
	}
	else{
		$th = array(_("Item Code"),
			_("Item Description"), _("Location"),
			_("Work Center"), _("Quantity"), _("Issue Qty."), _("Qty x I.Qty"), _("Units"), "");
	}

	if ( count($order->line_item_wo)) $th[] = '';

	table_header($th);
	$total = 0;
	$k = 0;  //row colour counter

//	$low_stock = $order->check_qoh($_POST['StockLocation'], $_POST['AdjDate']);
	$id = find_submit('Edit');
	foreach ($order->line_item_wo as $line_no=>$stock_item)
	{
		$total += ($stock_item->standard_cost * $stock_item->quantity);
		if ($id != $line_no)
		{
	    	$stock_item->quantity = input_num('quantity'); // for show 
			/*if (in_array($stock_item->stock_id, $low_stock))
				start_row("class='stockmankobg'");	// notice low stock status
			else */
				alt_table_row_color($k);
//    		if ($order->fixed_asset)
//				label_cell($stock_item->stock_id);
//      		else
				view_stock_status_cell($stock_item->stock_id);
			label_cell($stock_item->stock_id."-".$stock_item->item_description);
			label_cell(get_location_name($stock_item->loc_code1));
			label_cell(get_work_centre_name($stock_item->workcentre_added1));
			if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
               $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' ||
               $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
				qty_cell($stock_item->amount33, false, get_qty_dec($stock_item->stock_id));
			qty_cell($stock_item->quantity, false, get_qty_dec($stock_item->stock_id));
			qty_cell($stock_item->issue_qty);
			qty_cell($stock_item->quantity * $stock_item->issue_qty);
			label_cell($stock_item->units);
//			amount_decimal_cell($stock_item->standard_cost);
//			if ($order->fixed_asset) {
//    	   		$price = get_purchase_value($stock_item->stock_id);
//				amount_cell($price);
//      		} else
//				amount_cell($stock_item->standard_cost * $stock_item->quantity);
      		if (!$order->fixed_asset) {
        		edit_button_cell("Edit$line_no", _("Edit"),	_('Edit document line'));
      		}
			delete_button_cell("Delete$line_no", _("Delete"), _('Remove line from document'));
			end_row();
		}
		else
		{
			adjustment_edit_item_controls($order, $line_no);
		}
	}

	if ($id == -1)
		adjustment_edit_item_controls($order);

//	label_row(_("Total"), number_format2($total, user_price_dec()), "align=right colspan=4", "align=right", 2);
    end_table();
//	if ($low_stock)
//		display_note(_("Marked items have insufficient quantities in stock as on day of adjustment."), 0, 1, "class='stockmankofg'");
	div_end();
}

//---------------------------------------------------------------------------------

function adjustment_edit_item_controls(&$order, $line_no=-1)
{
	global $Ajax;
	start_row();

	$dec2 = 0;
	$id = find_submit('Edit');
	if ($line_no != -1 && $line_no == $id)
	{
		$_POST['stock_id'] = $order->line_item_wo[$id]->stock_id;
		$_POST['amount33'] = $order->line_item_wo[$id]->amount33;
		$_POST['issue_qty'] = qty_format($order->line_item_wo[$id]->issue_qty,
			$order->line_item_wo[$id]->stock_id, $dec);
		//$_POST['std_cost'] = price_format($order->line_item_wo[$id]->standard_cost);
		$_POST['loc_code1'] = $order->line_item_wo[$id]->loc_code1;
		$_POST['workcentre_added1'] = $order->line_item_wo[$id]->workcentre_added1;
//		stock_costable_items_list_cells(null, 'stock_id', null, false, true);
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);
		//label_cell($order->line_item_wo[$id]->item_description, 'nowrap');
	    $Ajax->activate('items_table');
	}
	else
	{
	label_cell("");
//		if ($order->fixed_asset)
//			stock_disposable_fa_list_cells(null, 'stock_id', null, false, true, $order->line_item_wo);
//		else
			stock_manufactured_items_list_cells(null, 'stock_id', null, false, false);

		$myrow_1 = get_company_item_pref_from_position(1);
		$myrow_2 = get_company_item_pref_from_position(2);
		$myrow_3 = get_company_item_pref_from_position(3);
		$myrow_4 = get_company_item_pref_from_position(4);
		$myrow_5 = get_company_item_pref_from_position(5);
		$myrow_6 = get_company_item_pref_from_position(6);

		$item_info = get_item_edit_info($_POST['stock_id']);

		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);


		if (!isset($_POST['amount3'])) {
			$_POST['amount33'] = price_format($item_info["amount3"]);
		}

		$formula = $item_info["formula"];
		$value = explode(",", $formula);
		$maxs = max(array_keys($value));
		$amount3 = $value[0];
		$amount2 = $value[2];
		if ($formula != '') {
			$b = $_POST[$amount3] . "" . $value[1] . "" . $_POST[$amount2."3"];
			$_POST['qty'] = eval('return ' . $b . ';');
		

		}
		if (list_updated('stock_id')) {
			    $Ajax->activate('units_id');
			    $Ajax->activate('issue_qty');
			    $Ajax->activate('loc_code1');
			    $Ajax->activate('workcentre_added1');
		}

    	$item_info = get_item_edit_info($_POST['stock_id']);
		$dec = $item_info['decimals'];
		global  $db_connections;
		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' ) {

		}else{
			$_POST['qty'] = number_format2(1, $dec);
		}
		$_POST['std_cost'] = price_decimal_format($item_info["material_cost"], $dec2);
		$_POST['units_id'] = $item_info["units"];
	}
	locations_list_cells(null, 'loc_code1', null);
	workcenter_list_cells(null, 'workcentre_added1', null);
	global $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
	ref_cells("", "amount33", null, null, true,true);
	label_cell("");
	qty_cells(null, 'issue_qty', null, null, null, $dec);
	label_cell("");
	label_cell("");

	if ($order->fixed_asset) {
		/* TODO: check if this is a correct way of creating a fixed item
			that still has to be submitted in the form */
		hidden('std_cost');
//		label_cell($_POST['std_cost'], "nowrap align=right ", 'std_cost_view');
		$price = get_purchase_value($_POST['stock_id']);
//		amount_cell($price, false, '', 'initial_price');
	}
//	else {
		//amount_cells(null, 'std_cost', $_POST['std_cost']);
//		amount_cells(null, 'std_cost', null, null, null, $dec2);
//		label_cell("&nbsp;");
//	}

	if ($id != -1)
	{
		button_cell('UpdateItem', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelItemChanges', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('LineNo', $line_no);
 		set_focus('qty');
	} else {
		submit_cells('AddItem', _("Add Item"), "colspan=2",
		    _('Add new item to document'), true);
	}
	end_row();
}


//---------------------------------------------------------------------------------

function adjustment_options_controls()
{
	  echo "<br>";
	  start_table();
	  textarea_row(_("Memo"), 'memo_', null, 50, 3);
	  end_table(1);
}

//---------------------------------------------------------------------------------
function display_wo_bom_cart($stock_id, $enable = 0)
{
	$path_to_root = "..";
	end_table(1);
//	----------------------------------------------------------------------
	global  $db_connections;
	$myrow_3 = get_company_item_pref_from_position(3);
	display_heading("Bill of Materials");
	$result = get_bom($stock_id);
	$item_info = get_item_edit_info($stock_id);
	div_start('bom');
	start_table(TABLESTYLE, "width=60%");
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2'
		|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
	{
		$th = array(_("Code"), _("Description"), _("Location"),
			_("Work Centre"),"Enter (".$myrow_3['label_value'].")",_("Quantity"), _("Issued Qty."), _("Units")/*,'',''*/);
	}
	else{
		$th = array(_("Code"), _("Description"), _("Location"),
			_("Work Centre"),_("Quantity"), _("Issued Qty."), _("Units"));
	}


	table_header($th);

	$k = 0;
	

	while ($myrow = db_fetch($result))
	{
		$item_info = get_item_edit_info($myrow["component"]);


		$_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);


		if(!isset($_POST['amount3']) ) {
			$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
		}

		$formula = $item_info["formula"];

		$value = explode(",", $formula);
		$maxs = max(array_keys($value));
		$amount3 = $value[0];
		$amount2 = $value[2];


		if($formula != '')
		{
			if(isset($_POST[$myrow_3['name'].$myrow['id']]) && $_POST[$myrow_3['name'].$myrow['id']] > 0 ) {
				$b = $_POST[$myrow_3['name'].$myrow['id']] . "" . $value[1] . "" . $_POST[$amount3];
				$_POST['qty'.$myrow['id']] = eval('return '.$b.';');
			}
			else{
				$b = $item_info["amount3"] . "" . $value[1] . "" . $_POST[$amount3];
				$_POST['qty'.$myrow['id']] = eval('return '.$b.';');
			}
		}

		alt_table_row_color($k);
		label_cell($myrow["component"]);
		label_cell($myrow["descasdription"]);
		label_cell($myrow["location_name"]);
		label_cell($myrow["WorkCentreDescription"]);

		if(!isset($_POST['amount3'.$myrow['id']]))
		{
			$_POST[$myrow_3['name'].$myrow['id']] = $item_info["amount3"];
		}

		if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='DEMO')
		{
			ref_cells("", $myrow_3['name'].$myrow['id'], null, null, null, true);
		}
		if($enable == 0)
			qty_cells(null, 'qty'.$myrow['id'], number_format2(input_num('quantity'),  get_qty_dec($myrow["component"])));
		elseif($enable == 1)
			qty_cell(input_num('quantity'));
		$IssuedQty = $myrow['quantity']*input_num('quantity');
		qty_cell($IssuedQty);
		label_cell($myrow["units"]);

//		hyperlink_params("$path_to_root/manufacturing/manage/bom_edit.php?", _("Modify BOM"), "filterType=$stype&trans_no=$id");
//		edit_button_cell("Edit".$myrow['id'], _("Edit"));
//		delete_button_cell("Delete".$myrow['id'], _("Delete"));
		end_row();


	} //END WHILE LIST LOOP
//	if(db_num_rows($result) != 0)
//	{
////		echo "<td>";
////		echo "<td>";
////		echo "<td>";
////		echo "<td>";
////		echo "<td>";
////		submit_cells('Update', _("Update"),
////			_('Refresh document page'), true);
//
//	}
	end_table(1);
	div_end();

//	----------------------------------------------------------------------
	start_table(TABLESTYLE, "width=60%");
}
//---------------------------------------------------------------------------------

