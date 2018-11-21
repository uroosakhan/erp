<?php

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");

//--------------------------------------------------------------------------------------------------

function copy_from_cart()
{
	$cart = &$_SESSION['PO'];

	$_POST['supplier_id'] = $cart->supplier_id;
	$_POST['OrderDate'] = $cart->orig_order_date;
	if ($cart->trans_type == ST_SUPPINVOICE)
		$_POST['due_date'] = $cart->due_date;
    $_POST['supp_ref'] = $cart->supp_ref;
    $_POST['pr'] = $cart->pr;

    $_POST['h_text1'] = $cart->h_text1;
    $_POST['h_text2'] = $cart->h_text2;
    $_POST['h_text3'] = $cart->h_text3;
    $_POST['h_text4'] = $cart->h_text4;
    $_POST['h_text5'] = $cart->h_text5;
    $_POST['h_text6'] = $cart->h_text6;
    $_POST['h_comb1'] = $cart->h_comb1;
    $_POST['h_comb2'] = $cart->h_comb2;
    $_POST['h_comb3'] = $cart->h_comb3;
    $_POST['delivery_term'] = $cart->delivery_term;
    $_POST['sales_tax'] = $cart->sales_tax;
    $_POST['ref'] = $cart->reference;
	$_POST['Comments'] = $cart->Comments;
    $_POST['Comments1'] = $cart->Comments1;
    $_POST['Comments2'] = $cart->Comments2;
    $_POST['Comments3'] = $cart->Comments3;
    $_POST['Comments4'] = $cart->Comments4;
    $_POST['Comments5'] = $cart->Comments5;
    $_POST['StkLocation'] = $cart->Location;
    $_POST['delivery_address'] = $cart->delivery_address;
    $_POST['invoice_to'] = $cart->invoice_to;
	$_POST['prep_amount'] = price_format($cart->prep_amount);
	$_POST['_ex_rate'] = $cart->ex_rate;
	$_POST['cash_account'] = $cart->cash_account;
    $_POST['dimension'] = $cart->dimension;
    $_POST['dimension2'] = $cart->dimension2;
    $_POST['payments'] = $cart->payments;
    $_POST['sale_order_ref'] = $cart->sale_order_ref;
	$_POST['transaction_type'] = $cart->transaction_type;
    $_POST['discount1'] = price_format($cart->discount1);
    $_POST['discount2'] = price_format($cart->discount2);
    $_POST['disc1'] = price_format($cart->disc1);
    $_POST['disc2'] = price_format($cart->disc2);

    foreach($cart->tax_overrides as $id => $value)
	    $_POST['mantax'][$id] = price_format($value);
}

function copy_to_cart()
{
	$cart = &$_SESSION['PO'];

	$cart->supplier_id = $_POST['supplier_id'];	
	$cart->orig_order_date = $_POST['OrderDate'];
	if ($cart->trans_type == ST_SUPPINVOICE)
	{
		$cart->due_date = $_POST['due_date'];
		$cart->cash_account = get_post('cash_account');
	}
	$cart->reference = $_POST['ref'];
	$cart->supp_ref = $_POST['supp_ref'];

	$cart->pr = $_POST['pr'];

    $cart->h_text1 = $_POST['h_text1'];
    $cart->h_text2 = $_POST['h_text2'];
    $cart->h_text3 = $_POST['h_text3'];
    $cart->h_text4 = $_POST['h_text4'];
	$cart->h_text5 = $_POST['h_text5'];
	$cart->h_text6 = $_POST['h_text6'];
    $cart->h_comb1 = $_POST['h_comb1'];
    $cart->h_comb2 = $_POST['h_comb2'];
    $cart->h_comb3 = $_POST['h_comb3'];


    $cart->delivery_term = $_POST['delivery_term'];
	$cart->sales_tax = $_POST['sales_tax'];
	$cart->Comments = $_POST['Comments'];
    $cart->Comments1 = $_POST['Comments1'];
    $cart->Comments2 = $_POST['Comments2'];
    $cart->Comments3 = $_POST['Comments3'];
    $cart->Comments4 = $_POST['Comments4'];
    $cart->Comments5 = $_POST['Comments5'];
	$cart->Location = $_POST['StkLocation'];
	$cart->delivery_address = $_POST['delivery_address'];
	  $cart->invoice_to = $_POST['invoice_to'];
   	$cart->dimension = @$_POST['dimension'];
	$cart->dimension2 =  @$_POST['dimension2'];
	$cart->prep_amount = input_num('prep_amount', 0);
	$cart->payments = $_POST['payments'];
	$cart->sale_order_ref = $_POST['sale_order_ref'];
    $cart->discount1 = input_num('discount1');
    $cart->discount2 = input_num('discount2');
    $cart->disc1 = input_num('disc1');
    $cart->disc2 = input_num('disc2');

	$cart->ex_rate = input_num('_ex_rate', null);
	$cart->transaction_type = $_POST['transaction_type'];

    if (isset($_POST['mantax'])) {
		foreach($_POST['mantax'] as $id => $tax) {
			$cart->tax_overrides[$id] = user_numeric($_POST['mantax'][$id]); }
	}
}

//---------------------------------------------------------------------------------------------------

function create_new_po($trans_type, $trans_no)
{
	global $Refs;

	if (isset($_SESSION['PO']))
		unset ($_SESSION['PO']->line_items, $_SESSION['PO']);

	$cart = new purch_order;
	$_POST['OrderDate'] = new_doc_date();
	if (!is_date_in_fiscalyear($_POST['OrderDate']))
		$_POST['OrderDate'] = end_fiscalyear();
	$cart->due_date = $cart->orig_order_date = $_POST['OrderDate'];

	$cart->trans_type = $trans_type;
	$cart->order_no = $trans_no;
	/*read in all the selected order into the Items cart  */
	if ($trans_no) {
		read_po($trans_no, $cart);
		$cart->order_no = $trans_no;
	} else
		$cart->reference = $Refs->get_next($trans_type, null,
			array('supplier_id' => $cart->supplier_id, 'date' => get_post('OrderDate')));
	$_SESSION['PO'] = &$cart;
}

//---------------------------------------------------------------------------------------------------

function display_po_header(&$order)
{
	global $Ajax, $Refs;

	$editable = ($order->order_no == 0);

	start_outer_table(TABLESTYLE2, "width='80%'");

	table_section(1);
    if ($editable)
    {
        if (!isset($_POST['supplier_id']) && (get_global_supplier() != ALL_TEXT))
        	$_POST['supplier_id'] = get_global_supplier();
       $supp_category = get_company_pref('supp_category');
        if($supp_category == 1 ) {

        supp_category_list_row(_("Supplier Category:"), 'combo1', null, true);
         supplier_list_row(_("Supplier:"), 'supplier_id', null, false, false, false, true,$_POST['combo1']);

       }
       else
       {
    	supplier_list_row(_("Supplier:"), 'supplier_id', null, false, true, false, true);
       }
        if(list_updated('supplier_id'))
            unset($_SESSION['PO']->line_items);
             global $SysPrefs;
                if ($SysPrefs->show_text_qty2()== 1) {
    	submit_cells('getitems', _("Fetch History"), "colspan=2 align='center'",
            _('Add new item to document'), true);
            }
    }
	else
	{
		hidden('supplier_id', $order->supplier_id);
		label_row(_("Supplier:"), $order->supplier_name);
    }
    
    
transaction_list_cells(_("Transaction type:"), 'transaction_type', null, "", "", false);


	if ($order->supplier_id != get_post('supplier_id',-1)) {
		$old_supp = $order->supplier_id;
		get_supplier_details_to_order($order, $_POST['supplier_id']); 
	    get_duedate_from_terms($order);
		$_POST['due_date'] = $order->due_date;

		// supplier default price update
		foreach ($order->line_items as $line_no=>$item) {
			$line = &$order->line_items[$line_no];
			$line->price =  get_purchase_price ($order->supplier_id, $line->stock_id);
			$line->quantity =
				$line->quantity/get_purchase_conversion_factor ($old_supp, $line->stock_id)
					*get_purchase_conversion_factor ($order->supplier_id, $line->stock_id);
		}
	    $Ajax->activate('items_table');
	    $Ajax->activate('due_date');
	}
	set_global_supplier($_POST['supplier_id']);

	date_row($order->trans_type==ST_PURCHORDER ? _("Order Date:") :
		($order->trans_type==ST_SUPPRECEIVE ? _("Delivery Date:") : _("Invoice Date:")),
		'OrderDate', '', true, 0, 0, 0, null, true);

	if (isset($_POST['_OrderDate_changed'])) {
		$order->orig_order_date = $_POST['OrderDate'];
	    get_duedate_from_terms($order);
	    $_POST['due_date'] = $order->due_date;
		$Ajax->activate('due_date');
	}
	supplier_credit_row($order->supplier_id, $order->credit);

	if (!is_company_currency($order->curr_code))
	{
		label_row(_("Supplier Currency:"), $order->curr_code);
		exchange_rate_display(get_company_currency(), $order->curr_code,
			$_POST['OrderDate']);
	}

    if ($editable)
    {
    	ref_row(_("Reference:"), 'ref', '', null, false, $order->trans_type, array('date'=> @$_POST['OrderDate']));
    }
    else
    {
    	hidden('ref', $order->reference);
    	label_row(_("Reference:"), $order->reference);
    }
global  $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IMEC'){
		sales_order_purchase_cells(_("Sales Order Reference:"), 'sale_order_ref', null, true, false);

	}

	table_section(2);
global $leftmenu_save, $db_connections;
	if ($order->trans_type==ST_SUPPINVOICE)
		date_row(_("Due Date:"), 'due_date', '', false, 0, 0, 0, null, true);

  	text_row(_("Supplier's Reference:"), 'supp_ref', null, 16, 25);

	if (get_company_pref('use_dimension'))
		dimensions_list_row(_('Dimension').':', 'dimension', null, true, _('Default'), false, 1);
	if (get_company_pref('use_dimension') == 2)
		dimensions_list_row(_('Dimension 2').':', 'dimension2', null, true, _('Default'), false, 2);
	locations_list_row(_("Receive Into:"), 'StkLocation', null, false, true, $order->fixed_asset);
    sale_payment_list_cells(_('Payment:'), 'payments' ,null, $order->payments,false,false);
    
      if ($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BNT2')
    {
        combo1_po_list_row(_("Term's Of Delivery :"), 'h_comb1', null, true, false, false);
//        text_row(_("Chasis No:"), 'h_text1', null, 16, 15);
//        text_row(_("Engin No:"), 'h_text2', null, 16, 15);
    }

	table_section(3);

    if (!isset($_POST['StkLocation']) || $_POST['StkLocation'] == "" ||
    	isset($_POST['_StkLocation_update']) || !isset($_POST['delivery_address']) ||
    	$_POST['delivery_address'] == "")
    {
    	/*If this is the first time the form loaded set up defaults */

		$loc_row = get_item_location(get_post('StkLocation'));
        if ($loc_row)
        {
    	  	$_POST['delivery_address'] = $loc_row["delivery_address"];
			$Ajax->activate('delivery_address');
    	  	$_SESSION['PO']->Location = $_POST['StkLocation'];
    	  	$_SESSION['PO']->delivery_address = $_POST['delivery_address'];

        }
        else
        { /*The default location of the user is crook */
    	  	display_error(_("The default stock location set up for this user is not a currently defined stock location. Your system administrator needs to amend your user record."));
        }
    }

//  deliver_invoice_list_row("SELECT ",'deliver_invoice',null,null,null,true);
	global $leftmenu_save, $db_connections;
  $myrow1 = get_company_purch_pref('h_combo1');
    $myrow2 = get_company_purch_pref('h_combo2');
    $myrow3 = get_company_purch_pref('h_combo3');
 if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'BNT2')
 {
    if($myrow1["po_enable"] == 1) {
        yesno_list_row2(_($myrow1["label_value"]), 'h_comb2', null, _("Deliver to"), _("Invoice To"), true);
    }
 }
 else{
     if($myrow1["po_enable"] == 1) {
         yesno_list_row(_($myrow1["label_value"]), 'h_comb2', null, _("Yes"), _("No"), true);
    }
 }
    
    if($myrow3["po_enable"] == 1) {
        yesno_list_row(_($myrow1["label_value"]), 'h_comb3', null, _("Yes"), _("No"), true);
    }

    $myrow = get_company_prefs();
    //  $_POST['invoice_to'] = $myrow['coy_name'] ;
    if($_POST['h_comb2'] == 0) {
        textarea_row(_("Deliver to:"), 'delivery_address', $_POST['delivery_address'], 35, 4);
    }
    elseif ($_POST['h_comb2'] == 1){
        textarea_row(_("Invoice to:"), 'invoice_to', $myrow['coy_name'], 35, 4);
        hidden('delivery_address',$_POST['delivery_address']);
    }
    else{
        textarea_row(_("Deliver to:"), 'delivery_address', $_POST['delivery_address'], 35, 4);
        textarea_row(_("Invoice to:"), 'invoice_to',$myrow['coy_name'], 35, 4);
    }
    
	text_row(_("Purchase Requisition:"), 'pr', null, 16, 15);
	

	

    $supp = get_supplier($_POST['supplier_id']);
    if(!$order->trans_no) {
        $_POST['h_comb1'] = $supp['combo1'];
    }
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'IMEC')
    {
        text_row(_("Quotation No:"), 'h_text1', null, 16, 15);
        text_row(_("Quotation Date:"), 'h_text2', null, 16, 15);
        text_row(_("Shipping Instruction::"), 'h_text3', null, 16, 15);
        text_row(_("Contact Person:"), 'delivery_term', null, 16, 15);
        text_row(_("Phone No:"), 'sales_tax', null, 16, 15);
    }
    //-----------MARINA--------------//
 
    //-----------MARINA Khaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaan--------------//
	//comment by ansar
   // text_row(_("Text1:"), 'h_text1', null, 16, 15);
   // text_row(_("Text2:"), 'h_text2', null, 16, 15);
   // text_row(_("Text3:"), 'h_text3', null, 16, 15);
   // yesno_list_row(_("Comb1"), 'h_comb1', null, _("Yes"), _("No"), true);
   // yesno_list_row(_("Comb2"), 'h_comb2', null, _("Yes"), _("No"), true);
   // yesno_list_row(_("Comb3"), 'h_comb3', null, _("Yes"), _("No"), true);
    global $leftmenu_save, $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL' || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'RPL2')
    {
        text_row(_("Delivery Terms:"), 'delivery_term', null, 16, 15);
        text_row(_("Sales Tax:"), 'sales_tax', null, 16, 15);
    }
	end_outer_table(); // outer table
}

//---------------------------------------------------------------------------------------------------

function display_po_items(&$order, $editable=true)
{
global  $db_connections;
    display_heading(_("Order Items"));

    div_start('items_table');
    start_table(TABLESTYLE, "width='80%'");
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
	
	$currency =$order->curr_code;
	global $SysPrefs;
	if ($SysPrefs->show_view_purch() == 1) 
	{
		$th = array(_("Item Code"), _("Item Description"), _("View History"));
	}
	else
	{
		$th = array(_("Item Code"), _("Item Description"));
	}

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
	///Conversion Factor
	$myrow233 = get_company_item_pref('con_factor');
	$pref = get_company_prefs();

    if( $pref['disc_in_amount'] == 1) {
        $disc = "Discount ";
    }
    else{
        $disc = "Discount %";
    }
    global $SysPrefs;
    if ($SysPrefs->show_text_qty2()== 0) {
        if ($myrow233['purchase_enable'] == 1 && $pref['alt_uom'] == 1) {
            $myrow_factor = $myrow233['label_value'];
            if ($order->trans_type == 20) {
                $pref = get_company_prefs();
                if ($pref['batch'] == 1) {
                    array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
                        _("Received"), _("Unit"), $myrow_factor,
                        _("Required Delivery Date"), $order->tax_included ? _("Price after Tax  .($currency) ") : _("Price before Tax  .($currency)"), $disc, _("Line Total"), _("")));
                } else {
                    array_append($th, array(_("Quantity"),
                        _("Received"), _("Unit"), $myrow_factor,
                        _("Required Delivery Date"), $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"),$disc,  _("Line Total"), _("")));

                }
            } else {
                array_append($th, array(_("Quantity"),
                    _("Received"), _("Unit"), $myrow_factor,
                    _("Required Delivery Date"), $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"),$disc,  _("Line Total"), _("")));

            }
        } else {

            if ($order->trans_type == 20) {
                $pref = get_company_prefs();
                if ($pref['batch'] == 1) {
                    array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
                        _("Received"), _("Unit"),
                        $order->tax_included ? _("Price after Tax") : _("Price before Tax"), $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"),$disc      ,  _("Line Total"), _("")));
                } else {
                    array_append($th, array(_("Quantity"),
                        _("Received"), _("Unit"),
                        $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"), $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"),$disc,  _("Line Total"), _("")));

                }
            } else {
                array_append($th, array(_("Quantity"),
                    _("Received"), _("Unit"),
                    _("Required Delivery Date"), $order->tax_included ? _("Price after Tax .($currency)") : _("Price before Tax .($currency)"),$disc,  _("Line Total"), _("")));

            }
        }
    }
    else
        {
            if ($myrow233['purchase_enable'] == 1 && $pref['alt_uom'] == 1) {
                $myrow_factor = $myrow233['label_value'];
                if ($order->trans_type == 20) {
                    $pref = get_company_prefs();
                    if ($pref['batch'] == 1) {
                        array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
                            _("Unit")));
                    } else {
                        array_append($th, array(_("Quantity"),
                           _("Unit")));

                    }
                } else {
                    array_append($th, array(_("Quantity"),
                       _("Unit") ));

                }
            } else {

                if ($order->trans_type == 20) {
                    $pref = get_company_prefs();
                    if ($pref['batch'] == 1) {
                        array_append($th, array(_("Quantity"), _("Batch"), _("Exp.Date"),
                             _("Unit")));
                    } else {
                        array_append($th, array(_("Quantity"),
                             _("Unit")
                            ));

                    }
                } else {
                    array_append($th, array(_("Quantity"),
                        _("Unit")));

                }
            }
        }
    if($pref['batch'] == 1 && $pref['alt_uom'] != 1 ) {
		if ($order->trans_type != ST_PURCHORDER)
			array_remove($th, 7);
	}
	elseif ($pref['batch'] == 1 && $pref['alt_uom'] == 1 )
	{
		if ($order->trans_type != ST_PURCHORDER)
			array_remove($th, 8);
	}
	elseif ($pref['batch'] != 1 && $pref['alt_uom'] == 1 )
	{
		if ($order->trans_type != ST_PURCHORDER)
			array_remove($th, 6);
	}
	else{
		if ($order->trans_type != ST_PURCHORDER)
			array_remove($th, 6);
	}

	if (count($order->line_items)) $th[] = '';

   	table_header($th);

	$id = find_submit('Edit');
	$total = 0;
	$k = 0;
   	foreach ($order->line_items as $line_no => $po_line)
   	{

        $TotalQty = $po_line->quantity;
        $prefs=get_company_prefs();
        if( $prefs['disc_in_amount'] == 1) {

            $line_total = round(  ($po_line->price  - $po_line->discount_percent) * $po_line->quantity  , user_price_dec());
        }
        else{
            $line_total = round($po_line->quantity * $po_line->price * (1 - $po_line->discount_percent), user_price_dec());
        }

    	if (!$editable || ($id != $line_no))
		{
    		alt_table_row_color($k);
        	view_stock_status_cell($po_line->stock_id);
    		label_cell($po_line->item_description);
    		global $path_to_root, $SysPrefs;

			$price = get_last_price_purch($po_line->stock_id, $order->supplier_id);
			if ($SysPrefs->show_view_purch() == 1)
			{
				echo "<td>";

				echo "<a target='_blank' href='$path_to_root/purchasing/view/view_item_history.php?stock_id=" . $po_line->stock_id . "&customer_id=" . $order->supplier_id . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\" >" . number_format2($price, 2) . "</a>";
			}
			//text boxes labels
			if($myrow_1['purchase_enable'])
			{
				label_cell($po_line->$myrow_1['name']);
			}
			if($myrow_2['purchase_enable'])
			{
				label_cell($po_line->$myrow_2['name']);
			}
			if($myrow_3['purchase_enable'])
			{
				label_cell($po_line->$myrow_3['name']);
			}
			if($myrow_4['purchase_enable'])
			{
				label_cell($po_line->$myrow_4['name']);
			}
			if($myrow_5['purchase_enable'])
			{
				label_cell($po_line->$myrow_5['name']);
			}
			if($myrow_6['purchase_enable'])
			{
				label_cell($po_line->$myrow_6['name']);
			}
			if($myrow_7['purchase_enable'])
			{
				label_cell($po_line->$myrow_7['name']);
			}
			if($myrow_8['purchase_enable'])
			{
				label_cell($po_line->$myrow_8['name']);
			}
			if($myrow_9['purchase_enable'])
			{
				label_cell($po_line->$myrow_9['name']);
			}
			if($myrow_10['purchase_enable'])
			{
				label_cell($po_line->$myrow_10['name']);
			}
			if($myrow_11['purchase_enable'])
			{
				label_cell($po_line->$myrow_11['name']);
			}
			if($myrow_12['purchase_enable'])
			{
				label_cell($po_line->$myrow_12['name']);
			}

			///combo inputs
			if($myrow_13['purchase_enable'])
			{
				label_cell($po_line->$myrow_13['name']);
			}
			if($myrow_14['purchase_enable'])
			{
				label_cell($po_line->$myrow_14['name']);
			}
			if($myrow_15['purchase_enable'])
			{
				label_cell($po_line->$myrow_15['name']);
			}
			if($myrow_16['purchase_enable'])
			{
				label_cell($po_line->$myrow_16['name']);
			}
			if($myrow_17['purchase_enable'])
			{
				label_cell($po_line->$myrow_17['name']);
			}
			if($myrow_18['purchase_enable'])
			{
				label_cell($po_line->$myrow_18['name']);
			}
			if($myrow_19['purchase_enable'])
			{
				label_cell($po_line->$myrow_19['name']);
			}
			if($myrow_20['purchase_enable'])
			{
				label_cell($po_line->$myrow_20['name']);
			}
			if($myrow_21['purchase_enable'])
			{
				label_cell($po_line->$myrow_21['name']);
			}
            if ($SysPrefs->show_text_qty2()== 0) {
                if ($order->fixed_asset)
                    label_cell(1);
                else
                    qty_cell($po_line->quantity, false, get_qty_dec($po_line->stock_id));
            }
            else
            {
                $_POST['qty'.$line_no] = number_format2($po_line->average_order_qty);
                if ($order->fixed_asset)
                    label_cell(1);
                else
                    qty_cells('','qty'.$line_no);
            }
			if($order->trans_type == 20 && $pref['batch']) {
                label_cell($po_line->grn_batch);
                label_cell($po_line->exp_date);
			}
            if ($SysPrefs->show_text_qty2()== 0) {
                qty_cell($po_line->qty_received, false, get_qty_dec($po_line->stock_id));
            }
			if($pref['alt_uom'] == 1) {
				label_cell($po_line->units_id);
				$myrow233 = get_company_item_pref('con_factor');
				if($myrow233['purchase_enable'] == 0){}
				else
				{
					label_cell($po_line->con_factor);
				}
			}
			else {
				label_cell($po_line->units);
			}
            if ($SysPrefs->show_text_qty2()== 0) {
                if ($order->trans_type == ST_PURCHORDER)
                    label_cell($po_line->req_del_date);

                amount_decimal_cell($po_line->price);
                $prefs=get_company_prefs();
                if( $prefs['disc_in_amount'] == 1) {
                    percent_cell($po_line->discount_percent );
                }
                else{
                    percent_cell($po_line->discount_percent * 100);
                }
                amount_cell($line_total);
            }
			if ($editable)
			{
                global $SysPrefs;
                if ($SysPrefs->show_text_qty2() == 0) {
                    edit_button_cell("Edit$line_no", _("Edit"),
                        _('Edit document line'));
                }
					delete_button_cell("Delete$line_no", _("Delete"),
						_('Remove line from document'));
			}
		end_row();
		}
		else
		{
			po_item_controls($order, $k, $line_no);
		}
		$total += $line_total;
        $totalqty += $TotalQty;
    }

	if ($id==-1 && $editable)
		po_item_controls($order, $k);

	$colspan = count($th)-2;
	if (count($order->line_items))
		$colspan--;

	$display_sub_total = price_format($total);
    label_row(_("Total Qty:"), $totalqty, "colspan=2 align=right","align=right", 2);
    hidden('total_qty', $totalqty);
	label_row(_("Sub-total"), $display_sub_total, "colspan=$colspan align=right","align=right", 2);
    hidden('sub_total',$display_sub_total);

	$taxes = $order->get_taxes(input_num('freight_cost'));
	
	$tax_total = display_edit_tax_items($taxes, $colspan, $order->tax_included, 2, $order->trans_type==ST_SUPPINVOICE);

	$display_total = price_format(($total + input_num('freight_cost') + $tax_total));

	start_row();
	label_cells(_("Amount Total"), $display_total, "colspan=$colspan align='right'","align='right'");
	$order->trans_type == ST_SUPPINVOICE
	 ? submit_cells('update', _("Update"), "colspan=2 align='center'", _("Refresh"), true)
		: label_cell('', "colspan=2");







    echo "<tr>";
    if($order->trans_no != 0)
        echo "<td>";


    label_cell(_("Discount%"), "colspan=4 align=right");

    $AllowLineDiscount = get_company_pref('discount_algorithm');
    if(!isset($_POST['update']) && $order->trans_no == 0)
    {
        if($AllowLineDiscount == 7)
            $_POST['disc3'] = percent_format($order->default_discount * 100);
        else
            $_POST['disc3'] = percent_format(0 * 100);
    }
    elseif($order->trans_no != 0)
    {
        if($AllowLineDiscount == 7)
            $_POST['disc3'] = percent_format($order->disc1);
        else
            $_POST['disc3'] = percent_format(0 * 100);
    }

    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'XPLORE' )

    {
        text_read_cells(null, 'disc1', $_POST['disc3'], 5, 50, false, "", "", "");

    }elseif($db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'IMEC' AND $db_connections[$_SESSION["wa_current_user"]->company]["name"] != 'BNW')

    {
        text_cells_new4(null, 'disc1', $_POST['disc3'], 5, 50, false, "", "", "");
    }


    $TotalPercent = $_POST['disc1']/100;
    echo '<td>Disc. Amt.';
    $_POST['discount1'] = number_format2($total*$TotalPercent);
    global $Ajax;
    $Ajax->activate('discount1');
    text_cells_non_searchable(null, 'discount1', null, 7);

    echo "<td/>";
    $discount_amount1 = /*$total*input_num('discount1')/100*/input_num('discount1');
    echo "<tr>";

	end_row();

	end_table(1);
	div_end();
}

//---------------------------------------------------------------------------------------------------

function display_po_summary(&$po, $is_self=false, $editable=false)
{
    start_table(TABLESTYLE, "width='90%'");
    if($po->transaction_type==1){


        $transaction ="Import";
    }
    else{

        $transaction = "Local";


    }
    start_row();
    label_cells(_("Reference"), $po->reference, "class='tableheader2'");

    label_cells(_("Supplier"), $po->supplier_name, "class='tableheader2'");
    label_cells(_("Text1"), $po->h_text1, "class='tableheader2'");
    label_cells(_("Text2"), $po->h_text2, "class='tableheader2'");
    label_cells(_("Text3"), $po->h_text3, "class='tableheader2'");
     label_cells(_("Text4"), $po->h_text4, "class='tableheader2'");
    label_cells(_("Text5"), $po->h_text5, "class='tableheader2'");
    label_cells(_("Text6"), $po->h_text6, "class='tableheader2'");
    label_cells(_("Text6"), $transaction, "class='tableheader2'");

    if (!is_company_currency($po->curr_code))
    	label_cells(_("Order Currency"), $po->curr_code, "class='tableheader2'");

    if (!$is_self)
    {
    	label_cells(_("Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no),
    		"class='tableheader2'");
    }
	end_row();
	start_row();
    label_cells(_("Date"), $po->orig_order_date, "class='tableheader2'");

    if ($editable)
    {
        if (!isset($_POST['Location']))
        	$_POST['Location'] = $po->Location;
        label_cell(_("Deliver Into Location"), "class='tableheader2'");
        locations_list_cells(null, 'Location', $_POST['Location']);
    }
    else
    {
    	label_cells(_("Deliver Into Location"), get_location_name($po->Location),
    		"class='tableheader2'");
    }

    if ($po->supp_ref != "")
    	label_cells(_("Supplier's Reference"), $po->supp_ref, "class='tableheader2'");
    label_cells(_("Comb1"), $po->h_comb1, "class='tableheader2'");
    label_cells(_("Comb2"), $po->h_comb2, "class='tableheader2'");
    label_cells(_("Comb3"), $po->h_comb3, "class='tableheader2'");
    end_row();

    if (!$editable)
    	label_row(_("Delivery Address"), $po->delivery_address, "class='tableheader2'",
    		"colspan=9");

	if (!$editable && ($po->prep_amount > 0))
	{
		start_row();
		label_cells(_("Required Pre-Payment"), price_format($po->prep_amount), "class='tableheader2'");
		label_cells(_("Pre-Payments Allocated"), price_format($po->alloc), "class='tableheader2'");
		end_row();
	}

    if ($po->Comments != "")
    	label_row(_("Order Comments"), $po->Comments, "class='tableheader2'",
    		"colspan=9");
    if ($po->Comments1 != "")
        label_row(_("Order Comments1"), $po->Comments1, "class='tableheader2'",
            "colspan=9");
    if ($po->Comments2 != "")
        label_row(_("Order Comments2"), $po->Comments2, "class='tableheader2'",
            "colspan=9");
    end_table(1);
}

//--------------------------------------------------------------------------------

function po_item_controls(&$order, &$rowcounter, $line_no=-1)
{
   global $Ajax, $SysPrefs;
global  $db_connections;
	alt_table_row_color($rowcounter);
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
	$dec2 = 0;
	$id = find_submit('Edit');
	if (($id != -1) && $line_no == $id)
	{

		$_POST['stock_id'] = $order->line_items[$id]->stock_id;
		$dec = get_qty_dec($_POST['stock_id']);
        $prefs = get_company_prefs();
		$_POST['qty'] = qty_format($order->line_items[$id]->quantity, $_POST['stock_id'], $dec);
        if( $prefs['disc_in_amount'] == 1) {
            $_POST['Disc'] = percent_format($order->line_items[$id]->discount_percent );
        }
        else{
            $_POST['Disc'] = percent_format($order->line_items[$id]->discount_percent * 100);
        }
		$_POST['price'] = price_decimal_format($order->line_items[$id]->price, $dec2);
		if ($order->trans_type == ST_PURCHORDER)
			$_POST['req_del_date'] = $order->line_items[$id]->req_del_date;

		$_POST['units_id'] = $order->line_items[$id]->units_id;
		$_POST['batch'] = $order->line_items[$id]->grn_batch;
		$_POST['exp_date'] = $order->line_items[$id]->exp_date;
		$_POST['item_description'] = $order->line_items[$id]->item_description;
		$_POST['text1'] = $order->line_items[$id]->text1;
		$_POST['text2'] = $order->line_items[$id]->text2;
		$_POST['text3'] = $order->line_items[$id]->text3;
		$_POST['text4'] = $order->line_items[$id]->text4;
		$_POST['text5'] = $order->line_items[$id]->text5;
		$_POST['text6'] = $order->line_items[$id]->text6;
		$_POST['amount1'] = price_format($order->line_items[$id]->amount1);
		$_POST['amount2'] = price_format($order->line_items[$id]->amount2);
		$_POST['amount3'] = price_format($order->line_items[$id]->amount3);
		$_POST['amount4'] = price_format($order->line_items[$id]->amount4);
		$_POST['amount5'] = price_format($order->line_items[$id]->amount5);
		$_POST['amount6'] = price_format($order->line_items[$id]->amount6);
		$_POST['date1'] = ($order->line_items[$id]->date1);
		$_POST['date2'] = ($order->line_items[$id]->date2);
		$_POST['date3'] = ($order->line_items[$id]->date3);
		$_POST['combo1'] = $order->line_items[$id]->combo1;
		$_POST['combo2'] = $order->line_items[$id]->combo2;
		$_POST['combo3'] = $order->line_items[$id]->combo3;
		$_POST['combo4'] = $order->line_items[$id]->combo4;
		$_POST['combo5'] = $order->line_items[$id]->combo5;
		$_POST['combo6'] = $order->line_items[$id]->combo6;
		$_POST['con_factor'] = $order->line_items[$id]->con_factor;
		hidden('stock_id', $_POST['stock_id']);
		label_cell($_POST['stock_id']);

		if ($order->line_items[$id]->descr_editable)
			text_cells(null,'item_description', null, 45, 150);
		else {
			hidden('item_description', $_POST['item_description']);
			label_cell($order->line_items[$id]->item_description);
		}

	    $Ajax->activate('items_table');
		$qty_rcvd = $order->line_items[$id]->qty_received;
	}
	else
	{
        $AllowKit = get_company_pref('allow_sales_kit_in_po');
        $supp_item = get_company_pref('supp_item');

		$AllowKit1 = 0;
		if($AllowKit == 1)
		$AllowKit1 = 1;

		// Manufactured item can be purchased
		if ($order->fixed_asset)
			stock_purchasable_fa_list_cells(null, 'stock_id', null, false, true, false, true, $order->line_items);
		else{
			if($AllowKit1 == 0 && $supp_item == 0)
				stock_items_list_cells(null, 'stock_id', null, false, true, false, true, array('editable' => 30),1);
			elseif($AllowKit1 == 1)
				sales_items_list_cells(null, 'stock_id', null, false, true, true);
				if($supp_item == 1)
            {
               
                supp_items_list_cells(null, 'stock_id', null,
                    false, true, true, $_POST['supplier_id']);
            }
		}

		if (list_updated('stock_id')) {
			    $Ajax->activate('price');
			$Ajax->activate('text1');
			$Ajax->activate('text2');
			$Ajax->activate('text3');
			$Ajax->activate('text4');
			$Ajax->activate('text5');
			$Ajax->activate('text6');
			$Ajax->activate('amount1');
			$Ajax->activate('amount2');
			$Ajax->activate('amount3');
			$Ajax->activate('amount4');
			$Ajax->activate('amount5');
			$Ajax->activate('amount6');
			$Ajax->activate('combo1');
			$Ajax->activate('combo2');
			$Ajax->activate('combo3');
			$Ajax->activate('combo4');
			$Ajax->activate('combo5');
			$Ajax->activate('combo6');
			$Ajax->activate('date1');
			$Ajax->activate('date2');
			$Ajax->activate('date3');
			    $Ajax->activate('units_id');
			    $Ajax->activate('qty');
			    $Ajax->activate('req_del_date');
			    $Ajax->activate('line_total');
			$Ajax->activate('con_factor');
			$Ajax->activate('hi');
            $Ajax->activate('Disc');
		}
    	$item_info = get_item_edit_info($_POST['stock_id']);
    	$purch_data = get_purchase_data($_POST['supplier_id'],$_POST['stock_id']);
		$_POST['units_id'] = $item_info["units"];
		$_POST['con_factor'] = price_format($item_info["con_factor"]);
        $_POST['amount1'] = price_format($item_info["amount1"]);
		$_POST['amount2'] = price_format($item_info["amount2"]);
		$_POST['amount3'] = price_format($item_info["amount3"]);
		$_POST['amount4'] = price_format($item_info["amount4"]);
		$_POST['amount5'] = price_format($item_info["amount5"]);
		$_POST['amount6'] = price_format($item_info["amount6"]);
	    $_POST['Disc'] = price_format($purch_data["discount"]);

   		$dec = $item_info["decimals"];
        if($SysPrefs->show_text_qty2()== 0){
        $_POST['qty'] = number_format2(get_purchase_conversion_factor($order->supplier_id, $_POST['stock_id']), get_qty_dec($_POST['stock_id']));
    }
    else {
        $_POST['qty'] = number_format2(0, get_qty_dec($_POST['stock_id']));

    }
        $_POST['price'] = price_decimal_format(get_purchase_price ($order->supplier_id, $_POST['stock_id']), $dec2);
		if ($order->trans_type == ST_PURCHORDER)
			$_POST['req_del_date'] = add_days(Today(), $SysPrefs->default_receival_required_by());
		$qty_rcvd = '';
	}
	global $path_to_root, $SysPrefs;

	$price = get_last_price_purch($_POST['stock_id'], $order->supplier_id);
	if ($SysPrefs->show_view_purch() == 1) {
		echo "<td>";
		div_start('hi');
		echo "<a target='_blank' href='$path_to_root/purchasing/view/view_item_history.php?stock_id=" . $_POST['stock_id'] . "&supplier_id=" . $_POST['supplier_id'] . "&popup=1' onclick=\"javascript:openWindow(this.href,this.target); return false;\" >" . number_format2($price, 2) . "</a>";

		div_end();
	}
	//text boxes entry
	if($myrow_1['purchase_enable'])
	{
		if($myrow_1['type'] == 1)
			small_amount_cells(null, $myrow_1['name']);
		elseif($myrow_1['type'] == 2)
			combo1_list_cells(null, $myrow_1['name'],$item_info['$myrow_1["name"]']);
		elseif($myrow_1['type'] == 3)
			date_cells(null, $myrow_1['name'],null);
		elseif($myrow_1['type'] == 4)
			text_cells(null, $myrow_1['name'], $item_info['$myrow_1["name"]'], $myrow_1["s_width"], 400);
	}
	else{
	hidden($myrow_1['name'],$_POST['amount1']);
	}
	if($myrow_2['purchase_enable'])
	{
		if($myrow_2['type'] == 1)
			small_amount_cells(null, $myrow_2['name']);
		elseif($myrow_1['type'] == 2)
			combo1_list_cells(null, $myrow_2['name'],$item_info['$myrow_2["name"]']);
		elseif($myrow_2['type'] == 3)
			date_cells(null, $myrow_2['name'],null);
		elseif($myrow_2['type'] == 4)
			text_cells(null, $myrow_2['name'], $item_info['$myrow_2["name"]'], $myrow_2["s_width"], 40);
	}

	if($myrow_3['purchase_enable'])
	{
		if($myrow_3['type'] == 1)
			small_amount_cells(null, $myrow_3['name']);
		elseif($myrow_3['type'] == 2)
			combo1_list_cells(null, $myrow_3['name'],$item_info['$myrow_3["name"]']);
		elseif($myrow_3['type'] == 3)
			date_cells(null, $myrow_3['name'],null);
		elseif($myrow_3['type'] == 4)
			text_cells(null, $myrow_3['name'], $item_info['$myrow_3["name"]'], $myrow_3["s_width"], 40);
	}
	else{
	hidden($myrow_3['name'],$_POST['amount3']);
	
	}
	if($myrow_4['purchase_enable'])
	{
		if($myrow_4['type'] == 1)
			small_amount_cells(null, $myrow_4['name']);
		elseif($myrow_4['type'] == 2)
			combo1_list_cells(null, $myrow_4['name'],$item_info['$myrow_4["name"]']);
		elseif($myrow_4['type'] == 3)
			date_cells(null, $myrow_4['name'],null);
		elseif($myrow_4['type'] == 4)
			text_cells(null, $myrow_4['name'], $item_info['$myrow_4["name"]'], $myrow_4["s_width"], 40);
	}
	if($myrow_5['purchase_enable'])
	{
		if($myrow_5['type'] == 1)
			small_amount_cells(null, $myrow_5['name']);
		elseif($myrow_5['type'] == 2)
			combo1_list_cells(null, $myrow_5['name'],$item_info['$myrow_5["name"]']);
		elseif($myrow_5['type'] == 3)
			date_cells(null, $myrow_5['name'],null);
		elseif($myrow_5['type'] == 4)
			text_cells(null, $myrow_5['name'], $item_info['$myrow_5["name"]'], $myrow_5["s_width"], 40);
	}
	if($myrow_6['purchase_enable'])
	{
		if($myrow_6['type'] == 1)
			small_amount_cells(null, $myrow_6['name']);
		elseif($myrow_6['type'] == 2)
			combo1_list_cells(null, $myrow_6['name'],$item_info['$myrow_6["name"]']);
		elseif($myrow_6['type'] == 3)
			date_cells(null, $myrow_6['name'],null);
		elseif($myrow_6['type'] == 4)
			text_cells(null, $myrow_6['name'], $item_info['$myrow_6["name"]'], $myrow_6["s_width"], 40);
	}
	if($myrow_7['purchase_enable'])
	{
		if($myrow_7['type'] == 1)
			small_amount_cells(null, $myrow_7['name']);
		elseif($myrow_7['type'] == 2)
			combo1_list_cells(null, $myrow_7['name'],$item_info['$myrow_7["name"]']);
		elseif($myrow_7['type'] == 3)
			date_cells(null, $myrow_7['name'],null);
		elseif($myrow_7['type'] == 4)
			text_cells(null, $myrow_7['name'], $item_info['$myrow_7["name"]'], $myrow_7["s_width"], 40);
	}
	if($myrow_8['purchase_enable'])
	{
		if($myrow_8['type'] == 1)
			small_amount_cells(null, $myrow_8['name']);
		elseif($myrow_8['type'] == 2)
			combo1_list_cells(null, $myrow_8['name'],$item_info['$myrow_8["name"]']);
		elseif($myrow_8['type'] == 3)
			date_cells(null, $myrow_8['name'],null);
		elseif($myrow_8['type'] == 4)
			text_cells(null, $myrow_8['name'], $item_info['$myrow_8["name"]'], $myrow_8["s_width"], 40);
	}
	if($myrow_9['purchase_enable'])
	{
		if($myrow_9['type'] == 1)
			small_amount_cells(null, $myrow_9['name']);
		elseif($myrow_9['type'] == 2)
			combo1_list_cells(null, $myrow_9['name'],$item_info['$myrow_9["name"]']);
		elseif($myrow_9['type'] == 3)
			date_cells(null, $myrow_9['name'],null);
		elseif($myrow_9['type'] == 4)
			text_cells(null, $myrow_9['name'], $item_info['$myrow_9["name"]'], $myrow_9["s_width"], 40);
	}
	if($myrow_10['purchase_enable'])
	{
		if($myrow_10['type'] == 1)
			small_amount_cells(null, $myrow_10['name']);
		elseif($myrow_10['type'] == 2)
			combo1_list_cells(null, $myrow_10['name'],$item_info['$myrow_10["name"]']);
		elseif($myrow_10['type'] == 3)
			date_cells(null, $myrow_10['name'],null);
		elseif($myrow_10['type'] == 4)
			text_cells(null, $myrow_10['name'], $item_info['$myrow_10["name"]'], $myrow_10["s_width"], 40);
	}
	if($myrow_11['purchase_enable'])
	{
		if($myrow_11['type'] == 1)
			small_amount_cells(null, $myrow_11['name']);
		elseif($myrow_11['type'] == 2)
			combo1_list_cells(null, $myrow_11['name'],$item_info['$myrow_11["name"]']);
		elseif($myrow_11['type'] == 3)
			date_cells(null, $myrow_11['name'],null);
		elseif($myrow_11['type'] == 4)
			text_cells(null, $myrow_11['name'], $item_info['$myrow_11["name"]'], $myrow_11["s_width"], 40);
	}
	if($myrow_12['purchase_enable'])
	{
		if($myrow_12['type'] == 1)
			small_amount_cells(null, $myrow_12['name']);
		elseif($myrow_12['type'] == 2)
			combo1_list_cells(null, $myrow_12['name'],$item_info['$myrow_12["name"]']);
		elseif($myrow_12['type'] == 3)
			date_cells(null, $myrow_12['name'],null);
		elseif($myrow_12['type'] == 4)
			text_cells(null, $myrow_12['name'], $item_info['$myrow_12["name"]'], $myrow_12["s_width"], 40);
	}
	if($myrow_13['purchase_enable'])
	{
		if($myrow_13['type'] == 1)
			small_amount_cells(null, $myrow_13['name']);
		elseif($myrow_13['type'] == 2)
			combo1_list_cells(null, $myrow_13['name'],$item_info['$myrow_13["name"]']);
		elseif($myrow_13['type'] == 3)
			date_cells(null, $myrow_13['name'],null);
		elseif($myrow_13['type'] == 4)
			text_cells(null, $myrow_13['name'], $item_info['$myrow_13["name"]'], $myrow_13["s_width"], 40);
	}
	if($myrow_14['purchase_enable'])
	{
		if($myrow_14['type'] == 1)
			small_amount_cells(null, $myrow_14['name']);
		elseif($myrow_14['type'] == 2)
			combo1_list_cells(null, $myrow_14['name'],$item_info['$myrow_14["name"]']);
		elseif($myrow_14['type'] == 3)
			date_cells(null, $myrow_14['name'],null);
		elseif($myrow_14['type'] == 4)
			text_cells(null, $myrow_14['name'], $item_info['$myrow_14["name"]'], $myrow_14["s_width"], 40);
	}
	if($myrow_15['purchase_enable'])
	{
		if($myrow_15['type'] == 1)
			small_amount_cells(null, $myrow_15['name']);
		elseif($myrow_15['type'] == 2)
			combo1_list_cells(null, $myrow_15['name'],$item_info['$myrow_15["name"]']);
		elseif($myrow_15['type'] == 3)
			date_cells(null, $myrow_15['name'],null);
		elseif($myrow_15['type'] == 4)
			text_cells(null, $myrow_15['name'], $item_info['$myrow_15["name"]'], $myrow_15["s_width"], 40);
	}
	if($myrow_16['purchase_enable'])
	{
		if($myrow_16['type'] == 1)
			small_amount_cells(null, $myrow_16['name']);
		elseif($myrow_16['type'] == 2)
			combo1_list_cells(null, $myrow_16['name'],$item_info['$myrow_16["name"]']);
		elseif($myrow_16['type'] == 3)
			date_cells(null, $myrow_16['name'],null);
		elseif($myrow_16['type'] == 4)
			text_cells(null, $myrow_16['name'], $item_info['$myrow_16["name"]'], $myrow_16["s_width"], 40);
	}
	if($myrow_17['purchase_enable'])
	{
		if($myrow_17['type'] == 1)
			small_amount_cells(null, $myrow_17['name']);
		elseif($myrow_17['type'] == 2)
			combo1_list_cells(null, $myrow_17['name'],$item_info['$myrow_17["name"]']);
		elseif($myrow_17['type'] == 3)
			date_cells(null, $myrow_17['name'],null);
		elseif($myrow_17['type'] == 4)
			text_cells(null, $myrow_17['name'], $item_info['$myrow_17["name"]'], $myrow_17["s_width"], 40);
	}
	if($myrow_18['purchase_enable'])
	{
		if($myrow_18['type'] == 1)
		{
			small_amount_cells(null, $myrow_18['name']);
		}
		elseif($myrow_18['type'] == 2)
		{	combo1_list_cells(null, $myrow_18['name'],$item_info['$myrow_18["name"]']);
		}
		elseif($myrow_18['type'] == 3)
			date_cells(null, $myrow_18['name'],null);
		elseif($myrow_18['type'] == 4)
		{
			text_cells(null, $myrow_18['name'], $item_info['$myrow_18["name"]'], $myrow_18["s_width"], 20);
		}
	}
	if($myrow_19['purchase_enable'])
	{
		if($myrow_19['type'] == 1)
		{
			small_amount_cells(null, $myrow_19['name']);
		}
		elseif($myrow_19['type'] == 2)
		{	combo1_list_cells(null, $myrow_19['name'],$item_info['$myrow_19["name"]']);
		}
		elseif($myrow_19['type'] == 3)
			date_cells(null, $myrow_19['name'],null);
		elseif($myrow_19['type'] == 4)
		{	text_cells(null, $myrow_19['name'], $item_info['$myrow_19["name"]'], $myrow_19["s_width"], 20);
		}
	}
	if($myrow_20['purchase_enable'])
	{
		if($myrow_20['type'] == 1)
			small_amount_cells(null, $myrow_20['name']);
		elseif($myrow_20['type'] == 2)
			combo1_list_cells(null, $myrow_20['name'],$item_info['$myrow_20["name"]']);
		elseif($myrow_20['type'] == 3)
			date_cells(null, $myrow_20['name'],null);
		elseif($myrow_20['type'] == 4)
			text_cells(null, $myrow_20['name'], $item_info['$myrow_20["name"]'], $myrow_20["s_width"], 40);
	}
	if($myrow_21['purchase_enable'])
	{
		if($myrow_21['type'] == 1)
			small_amount_cells(null, $myrow_21['name']);
		elseif($myrow_21['type'] == 2)
			combo1_list_cells(null, $myrow_21['name'],$item_info['$myrow_21["name"]']);
		elseif($myrow_21['type'] == 3)
			date_cells(null, $myrow_21['name'],null);
		elseif($myrow_21['type'] == 4)
			text_cells(null, $myrow_21['name'], $item_info['$myrow_21["name"]'], $myrow_21["s_width"], 40);
	}

	if ($order->fixed_asset) {
		label_cell(1, '', 'qty');
		hidden('qty', 1);
	} else
		qty_cells(null, 'qty', null, null, null, get_qty_dec($_POST['stock_id']));
	$pref=get_company_prefs();
	if($pref['batch'] == 1) {
		if ($order->trans_type == 20){
			text_cells("", 'batch');
			date_cells("",'exp_date');
			}
	}
    global $SysPrefs;
    if ($SysPrefs->show_text_qty2()== 0) {
        qty_cell($qty_rcvd, false, $dec);


	if($pref['alt_uom'] == 1) {
		stock_units_list_cell(null, 'units_id', $_POST['units_id'],1,$_POST['stock_id']);
		$myrow_factors = get_company_item_pref('con_factor');
		if($myrow_factors['purchase_enable'] == 0){}
		else {
			qty_cells(null, $myrow_factors['name'], $_POST['con_factor']);
		}
	}
	else
	{
		label_cell($_POST['units_id'], '', 'units_id');
		hidden('units_id',$_POST['units_id']);
	}

        if ($order->trans_type == ST_PURCHORDER)
            date_cells(null, 'req_del_date', '', null, 0, 0, 0);

        if ($qty_rcvd > 0) {
            amount_decimal_cell($_POST['price']);
            hidden('price', $_POST['price']);
        } else
            amount_cells_prc_only(null, 'price', null, null, null, user_prices_dec_only());

        small_amount_cells(null, 'Disc', percent_format($_POST['Disc']), null, null, user_percent_dec());
	$line_total = round(input_num('qty') * input_num('price'),  user_price_dec());
	amount_cell($line_total, false, '','line_total');
    }
    else
        {

//        qty_cell($qty_rcvd, false, $dec);


        if($pref['alt_uom'] == 1) {
            stock_units_list_cell(null, 'units_id', $_POST['units_id'],1,$_POST['stock_id']);
            $myrow_factors = get_company_item_pref('con_factor');
            if($myrow_factors['purchase_enable'] == 0){}
            else {
                qty_cells(null, $myrow_factors['name'], $_POST['con_factor']);
            }
        }
        else
        {
            label_cell($_POST['units_id'], '', 'units_id');
            hidden('units_id',$_POST['units_id']);
        }

//        if ($order->trans_type == ST_PURCHORDER)
//            date_cells(null, 'req_del_date', '', null, 0, 0, 0);
        hidden('req_del_date', date('d-m-Y'));
//        if ($qty_rcvd > 0) {
//            amount_decimal_cell($_POST['price']);
//            hidden('price', $_POST['price']);
//        } else
//            amount_cells_prc_only(null, 'price', null, null, null, user_prices_dec_only());
//
//        $line_total = round(input_num('qty') * input_num('price'),  user_price_dec());
//        amount_cell($line_total, false, '','line_total');
        hidden('price', $_POST['price']);

        }
	if ($id!=-1)
	{
		button_cell('UpdateLine', _("Update"),
				_('Confirm changes'), ICON_UPDATE);
		button_cell('CancelUpdate', _("Cancel"),
				_('Cancel changes'), ICON_CANCEL);
		hidden('line_no', $line_no);
		set_focus('qty');
	}
	else
	{
		submit_cells('EnterLine', _("Add Item"), "colspan=2 align='center'",
		    _('Add new item to document'), true);
	}

	end_row();
}

//---------------------------------------------------------------------------------------------------

//function display_po_footer(&$trans)
//{
//	global $Ajax;
//
//start_table(TABLESTYLE2);
//
//if ($trans->trans_type == ST_SUPPINVOICE) {
//	cash_accounts_list_row(_("Payment:"), 'cash_account', null, false, _('Delayed'));
//} elseif ($trans->trans_type == ST_PURCHORDER)
//	amount_row(_("Pre-Payment Required:"), 'prep_amount');
//
//textarea_row(_("Memo:"), 'Comments', null, 70, 4);
//    if ($trans->trans_type == ST_PURCHORDER) {
//        textarea_row(_("Memo1:"), 'Comments1', null, 70, 4);
//        textarea_row(_("Memo2:"), 'Comments2', null, 70, 4);
//    }
//
//end_table();
//}
//---------------------------------------------------------------------------------------------------