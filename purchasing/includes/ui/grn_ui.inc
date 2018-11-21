<?php

//---------------------------------------------------------------------------------------------------

function display_grn_summary(&$po)
{
 global $SysPrefs;
   start_table(TABLESTYLE2, "width='90%'");
   
   
    if($po->transaction_type==1){


        $transactions ="Import";
    }
    else{

        $transactions = "Local";


    }

    start_row();
	label_cells(_("Supplier"), $po->supplier_name, "class='tableheader2'");

    if (!is_company_currency($po->curr_code))
    	label_cells(_("Order Currency"), $po->curr_code, "class='tableheader2'");
  if ($SysPrefs->show_doc_ref() == 0) {
        label_cells(_("For Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no),
            "class='tableheader2'");
    }
    else
        {
        label_cells(_("For Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no, get_reference(ST_PURCHORDER,$po->order_no)),
            "class='tableheader2'");
    }
	label_cells(_("Ordered On"), $po->orig_order_date, "class='tableheader2'");
	end_row();

   	start_row();
   	label_cells(_("Reference"), $po->reference, "class='tableheader2'");
   	label_cells(_("Deliver Into Location"), get_location_name($po->Location), "class='tableheader2'");
label_cells(_("Supplier's Reference"), $po->supp_ref, "class='tableheader2'");

label_cells(_("Lading No"), $po->lading_no, "class='tableheader2'");



		label_cells(_("Transaction Type"), $transactions, "class='tableheader2'");
    label_cells(_("Text1"), $po->h_text1, "class='tableheader2'");
    label_cells(_("Text2"), $po->h_text2, "class='tableheader2'");
    label_cells(_("Text3"), $po->h_text3, "class='tableheader2'");

    end_row();

   	label_row(_("Delivery Address"), $po->delivery_address, "class='tableheader2'", "colspan=9");
    label_cells(_("Comb1"), $po->h_comb1, "class='tableheader2'");
    label_cells(_("Comb2"), $po->h_comb2, "class='tableheader2'");
    label_cells(_("Comb3"), $po->h_comb3, "class='tableheader2'");
    if ($po->Comments != "")
    	label_row(_("Order Comments"), $po->Comments, "class='tableheader2'", "colspan=9");
    end_table(1);
}

function edit_grn_summary(&$po)
{
	global $Refs;
	global $SysPrefs;
	
	start_outer_table(TABLESTYLE2, "width='80%'");

	table_section(1);
		label_row(_("Supplier"), $po->supplier_name);

    	if (!is_company_currency($po->curr_code))
    		label_row(_("Order Currency"), $po->curr_code);

if ($SysPrefs->show_doc_ref() == 0) {
        label_cells(_("For Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no));
    }
    else
    {
        label_cells(_("For Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po->order_no, get_reference(ST_PURCHORDER,$po->order_no)));
    }
//	    label_row(_("For Purchase Order"), get_trans_view_str(ST_PURCHORDER, $po//->order_no));

		label_row(_("Ordered On"), $po->orig_order_date);

    textarea_row(_("Memo1:"), 'Comments1', $po->Comments1, 70, 4);
    
    textarea_row(_("Memo2:"), 'Comments2', $po->Comments2, 70, 4);
	table_section(2);
    	if (!isset($_POST['ref']))
    	    $_POST['ref'] = $Refs->get_next(ST_SUPPRECEIVE, null, array('supplier' => $po->supplier_id, 'date' => Today()));
    	ref_row(_("Reference"), 'ref', '', null);

        if (!isset($_POST['Location']))
        	$_POST['Location'] = $po->Location;

        locations_list_row(_("Deliver Into Location"), "Location", $_POST['Location']);

    	if (!isset($_POST['DefaultReceivedDate']))
     	  	$_POST['DefaultReceivedDate'] = new_doc_date();

    	date_row(_("Date Items Received"), 'DefaultReceivedDate', '', true, 0, 0, 0, '', true);
//-------marina-------//

    if($po->transaction_type==1){


        $transaction ="Import";
    }
    else{

        $transaction = "Local";


    }
    	global $leftmenu_save, $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'SRC' )
    {
        text_row(_("Vehicle No"), 'h_text1', $po->h_text1, 16, 15);
		text_row(_("Weighbridge"), 'h_text2', $po->h_text2, 16, 15);
    }
    else{
        text_row(_("Text1"), 'h_text1', $po->h_text1, 16, 15);
        text_row(_("Text2"), 'h_text2', $po->h_text2, 16, 15);
    }
    text_row(_("DC No"), 'dc_no', $po->dc_no, 16, 15);
    
    
    
    label_cells(_("Transaction type"), $transaction, "class='tableheader2'");
        label_row(_("Lading No"), $po->receive_ref, "class='tableheader2'");

    
    
    
    hidden('transaction_type',$po->transaction_type);

//-------marina-------//
	table_section(3);
		text_row(_("Supplier's Reference"), 'sup_ref',$po->supp_ref,'',null,true);

   // text_row(_("Text1"), 'h_text1',$po->h_text1,'',null,true);
   // text_row(_("Text2"), 'h_text2',$po->h_text2,'',null,true);
    text_row(_("Text3"), 'h_text3',$po->h_text3,'',null,true);
    yesno_list_row(_("Comb1"), 'h_comb1',$po->h_comb1, _("Yes"), _("No"), false);
    yesno_list_row(_("Comb2"), 'h_comb2',$po->h_comb2, _("Yes"), _("No"), false);
    yesno_list_row(_("Comb3"), 'h_comb3',$po->h_comb3, _("Yes"), _("No"), false);

	   	label_row(_("Delivery Address"), $po->delivery_address);

    	if ($po->Comments != "")
    		label_row(_("Order Comments"), $po->Comments, "class='tableheader2'", "colspan=9");

	    if (!is_company_currency($po->curr_code))
			exchange_rate_display(get_company_currency(), $po->curr_code, get_post('DefaultReceivedDate'));
	end_outer_table(1);
}

