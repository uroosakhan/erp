<?php

include_once($path_to_root . "/includes/ui.inc");

//--------------------------------------------------------------------------------------
function processing_start_wo()
{
    page_processing(false);
    processing_end_wo();
    $_SESSION['Processing'] = $_SERVER['PHP_SELF'];
}
function processing_end_wo()
{
    page_processing(true);
    unset($_SESSION['Processing']);
    if ( isset($_SESSION['WorkOrderReq']) ) {
        unset($_SESSION['WorkOrderReq']->line_item_wo);
        unset($_SESSION['WorkOrderReq']);
    }
}
function display_bom($item_check)
{
    $result = get_bom($item_check);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("The bill of material for this item is empty."), 0, 1);
    }
    else
    {

        start_table(TABLESTYLE);
        $th = array(_("Component"), _("Description"), _("Work Centre"),
        	_("From Location"), _("Quantity"), _("Unit Cost"), _("Cost"));

        table_header($th);

        $j = 1;
        $k = 0; //row colour counter

        $total_cost = 0;

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

			label_cell($myrow["component"]);
			label_cell($myrow["description"]);
			label_cell($myrow["WorkCentreDescription"]);
			label_cell($myrow["location_name"]);
			qty_cell($myrow["quantity"], false, get_qty_dec($myrow["component"]));
			amount_cell($myrow["ProductCost"]);
			amount_cell($myrow["ComponentCost"]);
			end_row();
        	$total_cost += $myrow["ComponentCost"];

        	$j++;
        	if ($j == 12)
        	{
        		$j = 1;
        		table_header($th);
        	}
		}

		$item = get_item($item_check);
		if ($item['labour_cost'] != 0)
		{
			$total_cost += $item['labour_cost'];
			alt_table_row_color($k);
	        label_cells(_("Standard Labour Cost"), number_format2($item['labour_cost'],user_price_dec()),
    	    	"colspan=6 align=left", "class=odd_row nowrap align=right");
    	    end_row();
		}
		if ($item['overhead_cost'] != 0)
		{
			$total_cost += $item['overhead_cost'];
			alt_table_row_color($k);
	        label_cells(_("Standard Overhead Cost"), number_format2($item['overhead_cost'],user_price_dec()),
    	    	"colspan=6 align=left", "nowrap align=right");
    	    end_row();
		}
        label_row("<b>" . _("Total Cost") . "</b>", "<b>" . number_format2($total_cost,user_price_dec()) ."</b>",
        	"colspan=6 align=right", "nowrap align=right");
		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_requirements($woid, $quantity, $show_qoh=false, $date=null)
{
	global $SysPrefs;

    $result = get_wo_requirements($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Requirements for this Order."), 1, 0);
    }
    else
    {

        start_table(TABLESTYLE, "width='80%'");
        $th = array(_("Component"), _("From Location"), _("Work Centre"),
        	_("Unit Quantity"), _("Total Quantity"), _("Units Issued"), _("On Hand"));

        table_header($th);

        $k = 0; //row colour counter
		$has_marked = false;

		if ($date == null)
			$date = Today();

        while ($myrow = db_fetch($result))
        {

//display_error($myrow["stock_id"]."/" .$myrow["loc_code"]."/" .$date);


			$qoh = 0;
			$show_qoh = true;
			// if it's a non-stock item (eg. service) don't show qoh
			if (!has_stock_holding($myrow["mb_flag"]))
				$show_qoh = false;
				
	

			if ($show_qoh)
				$qoh = get_qoh_on_date($myrow["stock_id"], $myrow["loc_code"], $date);

			if ($show_qoh && ($myrow["units_req"] * $quantity > $qoh) &&
				!$SysPrefs->allow_negative_stock())
			{
				// oops, we don't have enough of one of the component items
				start_row("class='stockmankobg'");
				$has_marked = true;
			}
			else
				alt_table_row_color($k);

        	if (user_show_codes())
        		label_cell($myrow["stock_id"]. " - " . $myrow["description"]);
        	else
        		label_cell($myrow["description"]);

			label_cell($myrow["location_name"]);
			label_cell($myrow["WorkCentreDescription"]);
			$dec = get_qty_dec($myrow["stock_id"]);
				global  $db_connections;
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT' ||
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNT2' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTC' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX' || 
            $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='UIX') {
		 qty_cell($myrow["amount3"], false, $dec);
		}
		else{
		 qty_cell($myrow["units_req"], false, $dec);
		}
			
           
			qty_cell($myrow["units_req"] * $quantity, false, $dec);
            qty_cell($myrow["units_issued"], false, $dec);
			if ($show_qoh)
				qty_cell($qoh, false, $dec);
			else
				label_cell("");
			end_row();

		}

		end_table();

		if ($has_marked)
			display_note(_("Marked items have insufficient quantities in stock."), 0, 0, "class='red'");
    }
}

//--------------------------------------------------------------------------------------

function display_wo_productions($woid)
{
	global $path_to_root;

    $result = get_work_order_productions($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Productions for this Order."), 1, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Reference"), _("Date"), _("Quantity"));

        table_header($th);

        $k = 0; //row colour counter
		$total_qty = 0;

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

			$total_qty += $myrow['quantity'];

        	label_cell(get_trans_view_str(ST_MANURECEIVE, $myrow["id"]));
			label_cell($myrow['reference']);
			label_cell(sql2date($myrow["date_"]));
			qty_cell($myrow['quantity'], false, get_qty_dec($myrow['reference']));
			end_row();
		}//end of while

		label_row(_("Total"), number_format2($total_qty,user_qty_dec()),
			"colspan=3", "nowrap align=right");

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_issues($woid)
{
	global $path_to_root;

    $result = get_work_order_issues($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no Issues for this Order."), 0, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Reference"), _("Date"));

        table_header($th);

        $k = 0; //row colour counter

        while ($myrow = db_fetch($result))
        {

			alt_table_row_color($k);

        	label_cell(get_trans_view_str(ST_MANUISSUE, $myrow["issue_no"]));
			label_cell($myrow['reference']);
			label_cell(sql2date($myrow["issue_date"]));
			end_row();
		}

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_payments($woid)
{
	global $path_to_root, $wo_cost_types;

    $result = get_wo_costing($woid);

    if (db_num_rows($result) == 0)
    {
    	display_note(_("There are no additional costs for this Order."), 0, 1);
    }
    else
    {
        start_table(TABLESTYLE);
        $th = array(_("#"), _("Type"), _("Date"), _("Amount"));

        table_header($th);

        $k = 0;
        while ($myrow = db_fetch($result))
        {
			alt_table_row_color($k);

        	label_cell(get_gl_view_str( $myrow["trans_type"], $myrow["trans_no"], $myrow["trans_no"]));
        	label_cell($wo_cost_types[$myrow['cost_type']]);
        	$date = sql2date($myrow["tran_date"]);
        	label_cell($date);
			amount_cell($myrow['amount']);
			end_row();
		}

		end_table();
    }
}

//--------------------------------------------------------------------------------------

function display_wo_details($woid, $suppress_view_link=false)
{
	global $wo_types_array;

	$myrow = get_work_order($woid);

    if (strlen($myrow[0]) == 0)
    {
    	display_note(_("The work order number sent is not valid."));
    	exit;
    }

   	start_table(TABLESTYLE, "width='80%'");

    $prefs = get_company_prefs();
    if($prefs['batch'] == 1) {
        if ($myrow["released"] == true)
            $th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
                _("Into Location"), _("Batch"), _("Expiry Date"), _("Date"), _("Required By"), _("Quantity Required"),
                _("Released Date"), _("Manufactured"));
        else
            $th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
                _("Into Location"),  _("Batch"), _("Expiry Date"),_("Date"), _("Required By"), _("Quantity Required"));
    }
    else{
	if ($myrow["released"] == true)
		$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
			_("Into Location"), _("Date"), _("Required By"), _("Quantity Required"),
			_("Released Date"), _("Manufactured"));
	else
		$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
			_("Into Location"), _("Date"), _("Required By"), _("Quantity Required"));
    }

	table_header($th);
	start_row();
	if ($suppress_view_link)
		label_cell($myrow["id"]);
	else
		label_cell(get_trans_view_str(ST_WORKORDER, $myrow["id"]));
	label_cell($myrow["wo_ref"]);
	label_cell($wo_types_array[$myrow["type"]]);
	view_stock_status_cell($myrow["stock_id"], $myrow["StockItemName"]);
	label_cell($myrow["location_name"]);
    if($prefs['batch'] == 1) {
        $batch = get_batch_by_id($myrow["batch"]);
        label_cell($batch['name']);
        label_cell(sql2date($batch["exp_date"]));
    }
	label_cell(sql2date($myrow["date_"]));
	label_cell(sql2date($myrow["required_by"]));
	$dec = get_qty_dec($myrow["stock_id"]);
	if($suppress_view_link)
	qty_cell($myrow["units_reqd"], false, $dec);
	else
	    text_cells(null,'qty_req',$myrow["units_reqd"]);

	if ($myrow["released"] == true)
	{
		label_cell(sql2date($myrow["released_date"]));
		qty_cell($myrow["units_issued"], false, $dec);
	}
	end_row();

	comments_display_row(ST_WORKORDER, $woid);

	end_table();

    if ($myrow["closed"] == true)
    {
    	display_note(_("This work order is closed."));
    }
    return $myrow["stock_id"];
}

//--------------------------------------------------------------------------------------

function display_wo_details_quick($woid, $suppress_view_link=false)
{
	global $wo_types_array;

	$myrow = get_work_order($woid);
	$myrow1 = get_work_order_for_view($woid);
	
  $myrow_3 = get_company_item_pref_from_position(3);
    if (strlen($myrow[0]) == 0)
    {
    	display_note(_("The work order number sent is not valid."));
    	exit;
    }

   	start_table(TABLESTYLE, "width='80%'");
    $prefs = get_company_prefs();
    if($prefs['batch'] == 1) {
        $th = array(_("#"), _("Reference"), _("Type"), _("Manufactur1212ed Item"),
            _("Into Location"), _("Batch"), _("Expiry Date"), _("Date"));

    }
    else{
	$th = array(_("#"), _("Reference"), _("Type"), _("Manufactured Item"),
		_("Into Location"), _("Date"));
    }


    if($myrow_3['purchase_enable'] && $myrow_3['sale_enable']) {
        array_append($th, array($myrow_3['label_value']._("")) );
    }
    array_append($th, array(_("Quantity")));
	table_header($th);

	start_row();
	if ($suppress_view_link)
		label_cell($myrow["id"]);
	else
		label_cell(get_trans_view_str(ST_WORKORDER, $myrow["id"]));
	label_cell($myrow["wo_ref"]);
	label_cell($wo_types_array[$myrow["type"]]);
	view_stock_status_cell($myrow["stock_id"], $myrow["StockItemName"]);
	label_cell($myrow["location_name"]);

    if($prefs['batch'] == 1) {
        $batch = get_batch_by_id($myrow["batch"]);
        label_cell($batch['name']);
        label_cell(sql2date($batch["exp_date"]));
    }
	label_cell(sql2date($myrow["date_"]));
	 if($myrow_3['purchase_enable'] && $myrow_3['sale_enable']) {

        qty_cell($myrow1["amount3"], false, get_qty_dec($myrow["stock_id"]));
    }

	qty_cell($myrow["units_issued"], false, get_qty_dec($myrow["stock_id"]));

	end_row();

	comments_display_row(ST_WORKORDER, $woid);

	end_table();

    if ($myrow["closed"] == true)
    {
    	display_note(_("This work order is closed."));
    }
}

