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
page(_($help_context = "Customer Transactions"), isset($_GET['customer_id']), false, "", $js);

if (isset($_GET['customer_id']))
{
    $_POST['customer_id'] = $_GET['customer_id'];
}

//------------------------------------------------------------------------------------------------


function get_custom_list_name()
{

    $sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE sale_enable = 1
	AND name NOT IN ('combo1','combo2','combo3','combo4','combo5','combo6','total_amount','total_combo','total_date','total_text','con_factor','date1','date2','date3','formula','itemwise_discount','item_code_auto','sales_persons') ";

    $result = db_query($sql, "could not get customer");
    $row = db_fetch($result);
    return $row;

//	return db_fetch($sql);
}


$custom_fields =get_custom_list_name();
if($_SESSION["wa_current_user"]->can_access('SA_INCOME'))
{
echo '<center><a href="' . $path_to_root . '/themes/' . user_theme() . '/Dashboard_Widgets/IncomeandExpences3.php" class="btn btn-info role="button" target="_blank"> Show Graph Sales and Recovery</a>';
}

echo '<a href="' . $path_to_root .'/sales/inquiry/supply_chain_view.php" class="btn btn-info role="button" target="_blank"> Supply Chain View</a>';


start_form();

if (!isset($_POST['customer_id']))
    $_POST['customer_id'] = get_global_customer();

start_table(TABLESTYLE_NOBORDER);
start_row();

$_SESSION['amount'] = 0;

ref_cells(_("Reference"),'reference',null,null,false,null,null);
ref_cells(_("Customer Reference"),'cust_ref',null,null,false,null,null);

if (!$page_nested)
    customer_list_cells(_("Select a customer: "), 'customer_id', null, true, false, false, true);

date_cells(_("From:"), 'TransAfterDate', '', null, -user_transaction_days());
date_cells(_("To:"), 'TransToDate', '', null);


if (!isset($_POST['filterType']))
    $_POST['filterType'] = 0;

cust_allocations_list_cells(null, 'filterType', $_POST['filterType'], true);

if($_POST['filterType']==3){

    challan_list_cells('Select chalan','chalan_paid',null,'All Chalan','Chalan Un-Paid','Chalan Paid');

}

end_row();

start_row();


if($custom_fields && $_POST['filterType']!=3 && $_POST['filterType'] != ALL_TEXT ){



    item_pref_sales_list_cells(_("Search For Custom Fields"), 'items_enable',null,true);



    ref_cells(null, 'items_enable_searching', '',null, '', false);



}

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
    display_customer_summary($customer_record);
    echo "<br>";
}
div_end();

if(get_post('RefreshInquiry'))
{
    $Ajax->activate('totals_tbl');
}
//------------------------------------------------------------------------------------------------

function systype_name($dummy, $type)
{
    global $systypes_array;

    return $systypes_array[$type];
}

function order_view($row)
{
//    return $row['order_']>0 ?
//        get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
//        : "";
    global $SysPrefs;
    if ($SysPrefs->show_doc_ref() == 0) {
        return $row['order_']>0 ?
            get_customer_trans_view_str(ST_SALESORDER, $row['order_'])
            : "";

    }
    else{
        return $row['order_']>0 ?
            get_customer_trans_view_str(ST_SALESORDER,$row['order_'],get_reference(ST_SALESORDER, $row['order_']))
            : "";
    }
}

function trans_view($trans)
{
    return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
    return	$row["type"] == ST_SALESINVOICE	? $row["due_date"] : '';
}

function gl_view($row)
{
    return get_gl_view_str($row["type"], $row["trans_no"]);
}
//ansar 26-08-2017
function fmt_amount($row)
{
    $value =
        $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type']==ST_JOURNAL ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    $_SESSION['amount'] += $value;
    return price_format($value);
}
function fmt_debit($row)
{
//dz 16.6.17
    /*
    $value =
            $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    */
    $value =
        $row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT  || $row['type'] == ST_CRV ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    return $value>=0 ? price_format($value) : '';

}

function fmt_credit($row)
{
//dz 16.6.17
    /*
    $value =
            !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV || $row['type']==ST_JOURNAL) ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    */
    $value =
        !($row['type']==ST_CUSTCREDIT || $row['type']==ST_CUSTPAYMENT || $row['type']==ST_BANKDEPOSIT || $row['type'] == ST_CRV) ?
            -$row["TotalAmount"] : $row["TotalAmount"];
    return $value>0 ? price_format($value) : '';
}


function credit_link($row)
{
    global $page_nested;

    if ($page_nested)
        return '';
    return $row['type'] == ST_SALESINVOICE && $row["Outstanding"] > 0 ?
        pager_link(_("Credit This") ,
            "/sales/customer_credit_invoice.php?InvoiceNumber=". $row['trans_no'], ICON_CREDIT):'';
}

function edit_link($row)
{
    global $page_nested;

    $str = '';
    if ($page_nested)
        return '';

    return $row['type'] == ST_CUSTCREDIT && $row['order_'] ? '' : 	// allow  only free hand credit notes edition
        trans_editor_link($row['type'], $row['trans_no']);
}

function prt_link($row)
{
    if ($row['type'] == ST_CUSTPAYMENT || $row['type'] == ST_BANKDEPOSIT || $row['type'] == ST_CPV || $row['type'] == ST_CRV)
        return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_CUSTPAYMENT, ICON_PRINT);
    elseif ($row['type'] == ST_BANKPAYMENT) // bank payment printout not defined yet.
        return '';
    else
        return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}
function prt_link2($row)
{
    if ($row['type'] == ST_SALESINVOICE)
        return print_document_link($row['trans_no']."-".$row['type'], _("Print Receipt"), true, ST_SALESTAX, ICON_PRINT);
    else
        return '';
// 	else
// 		return print_document_link($row['trans_no']."-".$row['type'], _("Print"), true, $row['type'], ICON_PRINT);
}

function check_overdue($row)
{
    return $row['OverDue'] == 1
        && floatcmp($row["TotalAmount"], $row["Allocated"]) != 0;
}

if (isset($_POST['size_change'])) //size_change
{

    foreach($_POST['Selod_'] as $delivery => $branch) {

        $checkbox = 'Selod_'.$delivery;

        if (check_value($checkbox) ) {

            if(strlen($_POST["Selod_".$delivery]) != 0) {

                $user_value = $_POST["Selod_" . $delivery];

                $sql ="UPDATE 0_debtor_trans SET chalan=".db_escape($user_value)."
                        where 	trans_no=".db_escape($delivery)." AND type='12'";
                db_query($sql,"Could not Update ");
            }

        }
    }
    
    foreach($_POST['challan_no'] as $delivery => $branch) {

        $checkbox = 'challan_no'.$delivery;

        if (check_value($checkbox) ) {

            if(strlen($_POST["challan_no".$delivery]) != 0) {

                $user_value = $_POST["challan_no" . $delivery];

                $sql ="UPDATE 0_debtor_trans SET st_challan=".db_escape($user_value)."
                        where 	trans_no=".db_escape($delivery)." AND type='12'";
                db_query($sql,"Could not Update ");
            }

        }
    }

    foreach($_POST['date_'] as $delivery => $branch) {

        $checkbox = 'date_'.$delivery;

        if (check_value($checkbox) ) {

            if(strlen($_POST["date_".$delivery]) != 0) {

                $user_value = $_POST["date_" . $delivery];

                $sql ="Update 0_debtor_trans SET chalan_date=".db_escape(date2sql($user_value))."
                        where 	trans_no=".db_escape($delivery)." AND type='12'";
                db_query($sql,"Could not Update ");
                ///display_error($delivery);
            }

        }
    }
    
    
    
    
    
    foreach($_POST['date_s'] as $delivery => $branch) {

        $checkbox = 'date_s'.$delivery;

        if (check_value($checkbox) ) {

            if(strlen($_POST["date_s".$delivery]) != 0) {

                $user_value = $_POST["date_s" . $delivery];

                $sql ="Update 0_debtor_trans SET st_chalan_date=".db_escape(date2sql($user_value))."
                        where 	trans_no=".db_escape($delivery)." AND type='12'";
                db_query($sql,"Could not Update ");
                ///display_error($delivery);
            }

        }
    }
    
    
    
    
    
    
    
    
    
    
    

}


function challan_no($row)
{

    $trans_no = "challan_no" .$row['trans_no'];

    return $row['Done'] ? '' :
        "<input type='text' name='$trans_no' tabIndex='2".$row['trans_no']."' value='' >"

        ."<input name='challan_no[".$row['trans_no']."]' tabIndex='2".$row['trans_no']."' type='hidden' value='"
        .$row['trans_no']."'>\n";

}


function itchalan_date_func($row)
{

    $trans_no = "date_s" .$row['trans_no'];

    $title = null; $check=null; $inc_days=0;
    $inc_months=0; $inc_years=0; $params=null; $submit_on_change=false;


    global $use_date_picker, $path_to_root, $Ajax;

    if (!isset($_POST[$trans_no]) || $_POST[$trans_no] == "")
    {
        if ($inc_years == 1001)
            $_POST[$trans_no] = null;
        else
        {
            $dd = Today();
            if ($inc_days != 0)
                $dd = add_days($dd, $inc_days);
            if ($inc_months != 0)
                $dd = add_months($dd, $inc_months);
            if ($inc_years != 0)
                $dd = add_years($dd, $inc_years);
            $_POST[$trans_no] = $dd;
        }
    }

// 	if (user_use_date_picker())
// 	{
    $calc_image = (file_exists("$path_to_root/themes/".user_theme()."/images/cal.gif")) ?
        "$path_to_root/themes/".user_theme()."/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
    $post_label = "<a tabindex='-1' href=\"javascript:date_picker(document.getElementsByName('$trans_no')[0]);\">"
        . "	<img src='$calc_image' width='16' height='16' border='0' alt='"._('Click Here to Pick up the date')."'></a>\n";
// 	}
// 	else
// 		$post_label = "";

    //if ($label != null)
    //	label_cell($label, $params);

    echo "<td>";

    $class = $submit_on_change ? 'date active' : 'date';

    $aspect = $check ? 'aspect="cdate"' : '';
    if ($check && (get_post($trans_no) != Today()))
        $aspect .= ' style="color:#FF0000"';

    default_focus($trans_no);
    $size = (user_date_format()>3)?11:10;
    echo "<input type=\"text\" name=\"$trans_no\" class=\"$class\" $aspect size=\"$size\" maxlength=\"12\"
	 value='' ".($title ? " title='$title'": '')." > $post_label";
    echo"<input name='date_s[".$row['trans_no']."]' tabIndex='2".$row['trans_no']."' type='hidden' value='"
        .$trans_no."'>";

    echo "</td>\n";
    $Ajax->addUpdate($trans_no, $trans_no, $_POST[$trans_no]);

}


// if (isset($_POST['size_change'])) //size_change
// {

//     foreach($_POST['Selod_'] as $delivery => $branch) {

//         $checkbox = 'Selod_'.$delivery;

//         if (check_value($checkbox) ) {

//             if(strlen($_POST["Selod_".$delivery]) != 0) {

//                 $user_value = $_POST["Selod_" . $delivery];

//                 $sql ="UPDATE 0_debtor_trans SET chalan=".db_escape($user_value)."
//                         where 	trans_no=".db_escape($delivery)." AND type='12'";
//                 db_query($sql,"Could not Update ");
//             }

//         }
//     }

//     foreach($_POST['date_'] as $delivery => $branch) {

//         $checkbox = 'date_'.$delivery;

//         if (check_value($checkbox) ) {

//             if(strlen($_POST["date_".$delivery]) != 0) {

//                 $user_value = $_POST["date_" . $delivery];

//                 $sql ="Update 0_debtor_trans SET chalan_date=".db_escape(date2sql($user_value))."
//                         where 	trans_no=".db_escape($delivery)." AND type='12'";
//                 db_query($sql,"Could not Update ");
//                 ///display_error($delivery);
//             }

//         }
//     }

// }
function size_change($row)
{
    $trans_no = "Selod_" .$row['trans_no'];

    return $row['Done'] ? '' :
        "<input type='text' name='$trans_no' tabIndex='2".$row['trans_no']."' value=''>"
       ."<input name='Selod_[".$row['trans_no']."]' tabIndex='2".$row['trans_no']."' type='hidden' value='"
        .$row['trans_no']."'>\n";
}
function date_func($row)
{

    $trans_no = "date_" .$row['trans_no'];

    $title = null; $check=null; $inc_days=0;
    $inc_months=0; $inc_years=0; $params=null; $submit_on_change=false;


    global $use_date_picker, $path_to_root, $Ajax;

    if (!isset($_POST[$trans_no]) || $_POST[$trans_no] == "")
    {
        if ($inc_years == 1001)
            $_POST[$trans_no] = null;
        else
        {
            $dd = Today();
            if ($inc_days != 0)
                $dd = add_days($dd, $inc_days);
            if ($inc_months != 0)
                $dd = add_months($dd, $inc_months);
            if ($inc_years != 0)
                $dd = add_years($dd, $inc_years);
            $_POST[$trans_no] = $dd;
        }
    }

// 	if (user_use_date_picker())
// 	{
    $calc_image = (file_exists("$path_to_root/themes/".user_theme()."/images/cal.gif")) ?
        "$path_to_root/themes/".user_theme()."/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
    $post_label = "<a tabindex='-1' href=\"javascript:date_picker(document.getElementsByName('$trans_no')[0]);\">"
        . "	<img src='$calc_image' width='16' height='16' border='0' alt='"._('Click Here to Pick up the date')."'></a>\n";
// 	}
// 	else
// 		$post_label = "";

    //if ($label != null)
    //	label_cell($label, $params);

    echo "<td>";

    $class = $submit_on_change ? 'date active' : 'date';

    $aspect = $check ? 'aspect="cdate"' : '';
    if ($check && (get_post($trans_no) != Today()))
        $aspect .= ' style="color:#FF0000"';

    default_focus($trans_no);
    $size = (user_date_format()>3)?11:10;
    echo "<input type=\"text\" name=\"$trans_no\" class=\"$class\" $aspect size=\"$size\" maxlength=\"12\"
	 value='' ".($title ? " title='$title'": '')." > $post_label";
    echo"<input name='date_[".$row['trans_no']."]' tabIndex='2".$row['trans_no']."' type='hidden' value='"
        .$trans_no."'>";

    echo "</td>\n";
    $Ajax->addUpdate($trans_no, $trans_no, $_POST[$trans_no]);

}

//------------------------------------------------------------------------------------------------
function sms_link($row)
{
// 	if (@$_GET['popup'])
// 		return '';
    global $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS' ||
        $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO')
    {
        if($row['type'] == ST_SALESINVOICE){
            return $row['type'] == ST_SALESINVOICE  && $row["Outstanding"] > 0 ?
                pager_link(_("SMS This") ,
                    "/sms/send_sms.php?Order_no=".$row['trans_no']."&type=10", ICON_CREDIT):'';}
        else if($row['type'] == ST_CUSTPAYMENT){
            return $row['type'] == ST_CUSTPAYMENT ?
                pager_link(_("SMS This") ,
                    "/sms/send_sms.php?Order_no=".$row['trans_no']."&type=12", ICON_CREDIT):'';
        }
        else if($row['type'] == ST_CUSTCREDIT){
            return $row['type'] == ST_CUSTCREDIT ?
                pager_link(_("SMS This") ,
                    "/sms/send_sms.php?Order_no=".$row['trans_no']."&type=11", ICON_CREDIT):'';
        }
        else if($row['type'] == ST_CUSTDELIVERY){
            return $row['type'] == ST_CUSTDELIVERY?
                pager_link(_("SMS This") ,
                    "/sms/send_sms.php?Order_no=".$row['trans_no']."&type=13", ICON_CREDIT):'';
        }
    }
}

function get_item_header_name()
{
    /*Gets the GL Codes relevant to the item account  */
    $sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE sale_enable =1
	AND name NOT IN ('combo1','combo2','combo3','combo4','combo5','combo6','total_amount','total_combo','total_date','total_text','con_factor','date1','date2','date3','formula','itemwise_discount','item_code_auto','sales_persons') ";

    return db_query($sql,"Error");
    //return db_fetch($get);
}

if($custom_fields && $_POST['filterType']!=3 && $_POST['filterType']!=ALL_TEXT)
{

    $get_item_header_name = get_item_header_name();
    $i = 0;
    $data = array();
    while ($myrow = db_fetch($get_item_header_name)) {
        $data[$i]=$myrow['label_value'];
        $i++;
    }
}
$sql = get_sql_for_customer_inquiry(get_post('TransAfterDate'), get_post('TransToDate'),
    get_post('customer_id'), get_post('filterType'), get_post('reference'),get_post('cust_ref'),get_post('items_enable'), get_post('items_enable_searching'));

//------------------------------------------------------------------------------------------------
//db_query("set @bal:=0");
if($_POST['filterType']==3) {
    $cols = array(
        _("Type") => array('fun' => 'systype_name', 'ord' => ''),
        _("#") => array('fun' => 'trans_view', 'ord' => '', 'align' => 'right'),
        _("Order") => array('fun' => 'order_view', 'align' => 'right'),
        _("Reference"),
        _("Customer Reference") ,
        _("Date") => array('name' => 'tran_date', 'type' => 'date', 'ord' => 'desc'),
        _("Due Date") => array('type' => 'date', 'fun' => 'due_date'),
        _("Customer") => array('ord' => '') ,
        _("Branch") => array('ord' => ''),
        _("Currency") => array('align'=>'center'),
     
        _("S.T Chalan") ,
        _("S.T Chalan Date")=> array('align'=>'right', 'type'=>'date'),
        _("I.T Chalan") ,
        _("I.T Chalan Date")=> array('align'=>'right', 'type'=>'date'),
        _("Debit") => array('align' => 'right', 'fun' => 'fmt_debit'),
        _("Credit") => array('align' => 'right', 'insert' => true, 'fun' => 'fmt_credit'),
        _("Amount") => array('align' => 'right', 'fun' => 'fmt_amount'),
         _("SMS") => array('insert' => true, 'fun' => 'sms_link'),
        _("S.t Chlallan No.") => array('align' => 'right', 'fun' => 'challan_no'),
      _("Date of S.T chalan") => array('align' => 'right', 'fun' => 'itchalan_date_func'),//ansar 26-08-2017
         array('align' => 'right', 'fun' => 'size_change'),
        _(" I.T chalan No.") => array('align' => 'right', 'fun' => 'date_func'),
         _(" I.T chalan Date.") ,
 submit('size_change', _("Update Chalan"), false, _("size_change"))
     
        );

    array_append($cols, array(
        _("$data[0]") => array('align'=>'left','name' => $data[0]),
        _("$data[1]") => array('align'=>'left','name' => $data[1]),
        _("$data[2]") => array('align'=>'left','name' => $data[2]),
        _("$data[3]") => array('align'=>'left','name' => $data[3]),
        _("$data[4]") => array('align'=>'left','name' => $data[4]),
        _("$data[5]") => array('align'=>'left','name' => $data[5]),
        _("$data[6]") => array('align'=>'left','name' => $data[6]),
        array('insert' => true, 'fun' => 'gl_view'),
        array('insert' => true, 'fun' => 'credit_link'),
        array('insert' => true, 'fun' => 'edit_link'),
        array('insert' => true, 'fun' => 'prt_link2'),
        array('insert' => true, 'fun' => 'prt_link')
    ));
}
else{

    $cols = array(
        _("Type") => array('fun' => 'systype_name', 'ord' => ''),
        _("#") => array('fun' => 'trans_view', 'ord' => '', 'align' => 'right'),

        _("Order") => array('fun' => 'order_view', 'align' => 'right'),
        _("Reference"),
        _("Customer Reference") ,
        _("Date") => array('name' => 'tran_date', 'type' => 'date', 'ord' => 'desc'),
        _("Due Date") => array('type' => 'date', 'fun' => 'due_date'),
        _("Customer"),
        _("Branch") => array('ord' => ''),
        _("Currency") => array('align' => 'center'),
        
       _("S.T Chalan") ,
        _("S.T Chalan Date")=> array('align'=>'right', 'type'=>'date'),
        _("I.T Chalan") ,
        _("I.T Chalan Date")=> array('align'=>'right', 'type'=>'date'),
      
        _("Debit") => array('align' => 'right', 'fun' => 'fmt_debit'),
        _("Credit") => array('align' => 'right', 'insert' => true, 'fun' => 'fmt_credit'),
        _("Amount") => array('align' => 'right', 'fun' => 'fmt_amount'),);//ansar 26-08-2017
    //_("RB") => array('align'=>'right', 'type'=>'amount'), ansar 26-08-2017

    /*submit('date_func',_("Update Chalain date"), false, _("date_func"))
=> array('insert'=>true, 'fun'=>'date_func', 'align'=>'center'),*/

    array_append($cols, array(
        _("SMS") => array('insert' => true, 'fun' => 'sms_link'),
        
         _("$data[0]") => array('align'=>'left','name' => $data[0]),
        _("$data[1]") => array('align'=>'left','name' => $data[1]),
        _("$data[2]") => array('align'=>'left','name' => $data[2]),
        _("$data[3]") => array('align'=>'left','name' => $data[3]),
        _("$data[4]") => array('align'=>'left','name' => $data[4]),
        _("$data[5]") => array('align'=>'left','name' => $data[5]),
        _("$data[6]") => array('align'=>'left','name' => $data[6]),
        array('insert' => true, 'fun' => 'gl_view'),
        array('insert' => true, 'fun' => 'credit_link'),
        array('insert' => true, 'fun' => 'edit_link'),
        array('insert' => true, 'fun' => 'prt_link2'),
        array('insert' => true, 'fun' => 'prt_link')
    ));

    array_remove($cols, 10);
    array_remove($cols, 10);
    array_remove($cols, 10);
    array_remove($cols, 10);

}
if ($_POST['customer_id'] != ALL_TEXT) {
    $cols[_("Customer")] = 'skip';
    $cols[_("Currency")] = 'skip';
}
if ($_POST['filterType'] == ALL_TEXT)
    $cols[_("RB")] = 'skip';

$table =& new_db_pager('trans_tbl', $sql, $cols);
$table->set_marker('check_overdue', _("Marked items are overdue."));

$table->width = "85%";

display_db_pager_total_amount($table);

end_form();
end_page();
