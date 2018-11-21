<?php

$page_security = 'SA_SALESTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

$js = "";
if ($SysPrefs->use_popup_windows)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
page(_($help_context = "Offer Inquiry"), isset($_GET['customer_id']), false, "", $js);

if (isset($_GET['customer_id']))
{
	$_POST['customer_id'] = $_GET['customer_id'];
}
if (isset($_GET['Added']))
    display_notification_centered(sprintf( _("Offer has been entered.")));
if (isset($_GET['Updated']))
    display_notification_centered(sprintf( _("Offer has been updated.")));
//------------------------------------------------------------------------------------------------

start_form();

if (!isset($_POST['customer_id']))
	$_POST['customer_id'] = get_global_customer();
submenu_option(_("Add New Offer"), "/sales/offer_order_entry.php?AddOffers=Yes");
start_table(TABLESTYLE_NOBORDER);
start_row();
ref_cells(_("Offer Code"),'offer_code',null,null,false,null,null);
ref_cells(_("Offer Title:"),'title',null,null,false,null,null);
date_cells(_("From:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'TransToDate', '', null);
submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');
end_row();
end_table();

set_global_customer($_POST['customer_id']);

//------------------------------------------------------------------------------------------------
function get_customer_by_ref1($row)
{
	$sql = "SELECT customer_ref FROM ".TB_PREF."sales_orders WHERE order_no=".db_escape($row['order_']);

	$result = db_query($sql, "could not get customer");

	$myrow= db_fetch_row($result);
	return $myrow[0];
}
function display_customer_summary($customer_record)
{
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $past1;
    if ($customer_record["dissallow_invoices"] != 0)
    {
    	echo "<center><font color=red size=4><b>" . _("CUSTOMER ACCOUNT IS ON HOLD") . "</font></b></center>";
    }

	$nowdue = "1-" . $past1 . " " . _('Days');
	$pastdue1 = $past1 + 1 . "-" . $past2 . " " . _('Days');
	$pastdue2 = _('Over') . " " . $past2 . " " . _('Days');

    start_table(TABLESTYLE, "width='80%'");
    $th = array(_("Currency"), _("Terms"), _("Current"), $nowdue,
    	$pastdue1, $pastdue2, _("Total Balance"));
    table_header($th);

	start_row();
    label_cell($customer_record["curr_code"]);
    label_cell($customer_record["terms"]);
	amount_cell($customer_record["Balance"] - $customer_record["Due"]);
	amount_cell($customer_record["Due"] - $customer_record["Overdue1"]);
	amount_cell($customer_record["Overdue1"] - $customer_record["Overdue2"]);
	amount_cell($customer_record["Overdue2"]);
	amount_cell($customer_record["Balance"]);
	end_row();

	end_table();
}
//------------------------------------------------------------------------------------------------

div_start('totals_tbl');
if ($_POST['customer_id'] != "" && $_POST['customer_id'] != ALL_TEXT)
{
	$customer_record = get_customer_details($_POST['customer_id'], $_POST['TransToDate']);
//    display_customer_summary($customer_record);
    echo "<br>";
}
div_end();
function get_sql_for_offer_inquiry($from, $to, $title, $offer_code)
{
    $date_after = date2sql($from);
    $date_to = date2sql($to);
    $sql = "SELECT 
  		trans.offer_code, 
		trans.title, 
		trans.date, 
		trans.enable,
		trans.stock_id,
		trans.inn, 
		trans.values_, 
		trans.date_from, 
		trans.date_to, 
		trans.offer_status, 
		trans.status, 
		trans.offer_on, 
		trans.offer_calc_level, 
		trans.type, 
		trans.trans_no
		FROM ".TB_PREF."offer_details trans ";
    /*if($_POST['chalan_paid'] ==0)
            $sql .= " AND trans.chalan = 0";
        elseif($_POST['chalan_paid'] == 1)
            $sql .= " AND trans.chalan != 0";

        if ($cust_id != ALL_TEXT)
            $sql .= " AND trans.debtor_no = ".db_escape($cust_id);
    */
    /* if ($filter != ALL_TEXT)
     {
         if ($filter == '1')
         {
             $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
         }
         elseif ($filter == '2')
         {
             $sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
         }
         elseif ($filter == '3')
         {
             $sql .= " AND (trans.type = " . ST_CUSTPAYMENT
                 ." OR trans.type = ".ST_BANKDEPOSIT." OR trans.type = ".ST_BANKPAYMENT." OR trans.type = ".ST_CPV." OR trans.type = ".ST_CRV.") ";
         }
         elseif ($filter == '4')
         {

             $today =  date2sql(Today());
             $sql .= " AND trans.type = ".ST_CUSTCREDIT." ";
         }
         elseif ($filter == '5')
         {
             $sql .= " AND trans.type = ".ST_CUSTDELIVERY." ";
         }

         if ($filter == '2')
         {
             $today =  date2sql(Today());
             $sql .= " AND trans.due_date < '$today'
                 AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax +
                 trans.ov_freight + trans.ov_discount+ trans.gst_wh
 + trans.supply_disc + trans.service_disc + trans.fbr_disc + trans.srb_disc - trans.alloc > 0) ";
         }
 //         if ($cust_reference != '') {
 // 			$sql .= " AND trans.cust_reference LIKE " . db_escape("%" . $cust_reference . "%");
 // 		}
     }*/

    $sql .= " WHERE trans.date >= '$date_after'
             AND trans.date <= '$date_to'";

    if ($title != '')
        $sql .= " AND trans.title LIKE ".db_escape( "%".$title."%");

    if ($offer_code != '')
        $sql .= " AND trans.offer_code LIKE ".db_escape( "%".$offer_code."%");

    $sql .= " GROUP BY trans.trans_no, trans.type 
            ORDER BY trans.trans_no, trans.type";

    return $sql;
}
if(get_post('RefreshInquiry'))
{
	$Ajax->activate('totals_tbl');
}
function edit_link($row)
{
    return  trans_editor_link($row['type'], $row['trans_no']);
}
function enable_name($row)
{
    $result = '';
    if($row['enable'] == 0)
        $result = 'Enable';
    elseif($row['enable'] == 1)
        $result = 'Disable';
    return $result;
}
function description_name($row)
{
    $result = get_description_name($row['stock_id']);
    return $result;
}
function item_brand_name($row)
{
    $result = '';
    if($row['item_brand'] == 0)
        $result = 'No Body Soul';
    elseif($row['item_brand'] == 1)
        $result = 'Body Soul';
    return $result;
}
function in_name($row)
{
    $result = '';
    if($row['inn'] == 0)
        $result = 'Fixed';
    elseif($row['inn'] == 1)
        $result = '%';
    return $result;
}
function offer_status_name($row)
{
    $result = '';
    if($row['offer_status'] == 0)
        $result = 'Inactive';
    elseif($row['offer_status'] == 1)
        $result = 'Active';
    return $result;
}
function status_name($row)
{
    $result = '';
    if($row['status'] == 0)
        $result = 'Invoice Level';
    elseif($row['status'] == 1)
        $result = 'Item Level';
    return $result;
}
function offer_on_name($row)
{
    $result = '';
    if($row['offer_on'] == 0)
        $result = 'Special';
    elseif($row['status'] == 1)
        $result = 'All';
    return $result;
}
function offer_calc_level_name($row)
{
    $offer_array = array (
    1 => _("Level-1"),
    2 => _("Level-2"),
    3 => _("Level-3"),
    4 => _("Level-4"),
    5 => _("Level-5"),
    6 => _("Level-6"),
    7 => _("Level-7"),
    8 => _("Level-8"),
    9 => _("Level-9"),
    10 => _("Level-10"));
    return $offer_array[$row['offer_calc_level']];
}


//------------------------------------------------------------------------------------------------
$sql = get_sql_for_offer_inquiry($_POST['TransAfterDate'], $_POST['TransToDate'], $_POST['title'], $_POST['offer_code']);
//------------------------------------------------------------------------------------------------
$cols = array(
    _("Offer Code"),
    _("Title"),
    _("Date") => array('type' => 'date'),
    _("Enable") => array('fun' => 'enable_name'),
    _("Description") => array('fun' => 'description_name'),
//    _("Item Brand") => array('fun' => 'item_brand_name'),
    _("In") => array('fun' => 'in_name'),
    _("Values"),
    _("Date From") => array('type' => 'date'),
    _("Date To") => array('type' => 'date'),
    _("Offer Status") => array('fun' => 'offer_status_name'),
    _("Status") => array('fun' => 'status_name'),
    _("Offer ON") => array('fun' => 'offer_on_name'),
    _("Offer Calc. Level") => array('fun' => 'offer_calc_level_name'),
    _("Edit") => array('fun'=>'edit_link')
);

//	array_append($cols, array(
//		_("SMS") => array('insert' => true, 'fun' => 'sms_link'),
//		array('insert' => true, 'fun' => 'gl_view'),
//		array('insert' => true, 'fun' => 'credit_link'),
//		array('insert' => true, 'fun' => 'edit_link'),
//		array('insert' => true, 'fun' => 'prt_link2'),
//		array('insert' => true, 'fun' => 'prt_link')
//	));
//if ($_POST['customer_id'] != ALL_TEXT) {
//	$cols[_("Customer")] = 'skip';
//	$cols[_("Currency")] = 'skip';
//}
//if ($_POST['filterType'] == ALL_TEXT)
//	$cols[_("RB")] = 'skip';
$table =& new_db_pager('trans_tbl', $sql, $cols);
//$table->set_marker('check_overdue', _("Marked items are overdue."));
$table->width = "85%";
display_db_pager($table);

end_form();
end_page();
