<?php
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "..";
$page_security = 'SA_SALESORDER';

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/db/debtor_log.inc"); // debtor_log
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/ui/sales_order_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include_once($path_to_root . "/includes/ui/sms_send.inc");


set_page_security( @$_SESSION['Items']->trans_type,
	array(	ST_SALESORDER			=>'SA_SALESORDER',
			ST_SALESQUOTE 			=> 'SA_SALESQUOTE',
			ST_CUSTDELIVERY 		=> 'SA_SALESDELIVERY',
			ST_SALESINVOICE 		=> 'SA_SALESINVOICE'),
	array(	'NewOrder' 				=> 'SA_SALESORDER',
			'ModifyOrderNumber' 	=> 'SA_SALESORDER',
			'AddedID' 				=> 'SA_SALESORDER',
			'UpdatedID' 			=> 'SA_SALESORDER',
			'NewQuotation' 			=> 'SA_SALESQUOTE',
			'ModifyQuotationNumber' => 'SA_SALESQUOTE',
			'NewQuoteToSalesOrder' 	=> 'SA_SALESQUOTE',
			'AddedQU' 				=> 'SA_SALESQUOTE',
			'UpdatedQU' 			=> 'SA_SALESQUOTE',
			'NewDelivery' 			=> 'SA_SALESDELIVERY',
			'AddedDN' 				=> 'SA_SALESDELIVERY', 
			'NewInvoice' 			=> 'SA_SALESINVOICE',
			'AddedDI' 				=> 'SA_SALESINVOICE'
			)
);
$js = '';
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}
$_SESSION['page_title'] = _($help_context = "Send SMS");
page($_SESSION['page_title'], false, false, "", $js);

if(isset($_GET['type']))
{
	$_POST['type'] = $_GET['type'];
    $type = $_POST['type'];

}
$trans_no = $_GET['Order_no'];
if (isset($_GET['BatchInvoice'])) {
		$src = $_SESSION['DeliveryBatch'];
}

//-----------------------------------------------------------------------------
//var_dump($_SESSION['DeliveryBatch']);

if ($_POST['ProcessOrder']){
	foreach($_SESSION['DeliveryBatch'] as $val){
		/*if($_POST['type']==41)
		{
			$booking = get_booking($val);
			$cust_booking = get_customer($booking['debtor_no']);
			$_POST['name'] = $cust_booking["name"];
			$_POST['debtor_id'] = $booking['debtor_no']; //get debtor id for debtor_log table 
			$contact_num  = $cust_booking["debtor_ref"];
			$objWsdl = new callWSDL();
			$objWsdl->SendBulkSMS($contact_num, $_POST['message_']);
			add_debtor_log(1,$_POST['type'],$_POST['debtor_id'],$_POST['temp_id'] ,$_POST['Order_no'],        $_POST['message_'],$_POST['mobile_no']);
		}
		elseif($_POST['type']==62)
		{
			$fbooking = get_booking_orders($val);
			$myrow_fbooking = db_fetch($fbooking);
			$cust_id =  $myrow_fbooking["customer"];
			$amount = $myrow_fbooking["net_price"];
			$get_customer_fbooking = get_customer($cust_id);
			$_POST['name'] = $get_customer_fbooking["name"]; 
			$_POST['debtor_id'] = $myrow_fbooking["customer"]; //get debtor id for debtor_log table
			$contact_num  = $get_customer_fbooking["debtor_ref"];
			$objWsdl = new callWSDL();
			$objWsdl->SendBulkSMS($contact_num, $_POST['message_']);
			add_debtor_log(1,$_POST['type'],$_POST['debtor_id'],$_POST['temp_id'] ,$_POST['Order_no'],        $_POST['message_'],$_POST['mobile_no']);
		}*/
		if($_POST['type'] == 12)
		{
			 $myrow = get_customer_trans($val, $_POST['type']);
			 $cust = get_customer($myrow['debtor_no']);
			 $amount = $myrow["Total"];
			 $_POST['name'] = $cust["name"];
			 $_POST['debtor_id'] = $myrow['debtor_no']; //get debtor id for debtor_log table 
			 $contact_num  = $cust["debtor_ref"];
			 $objWsdl = new callWSDL();
			 $objWsdl->SendBulkSMS($contact_num, $_POST['message_']);
	   // add_debtor_log(1,$_POST['type'],$_POST['debtor_id'],$_POST['temp_id'] ,$_POST['Order_no'],        $_POST['message_'],$_POST['mobile_no']);
		}
		elseif($_POST['type'] == 10)
		{
			$myrow = get_customer_trans($val, $_POST['type']);
			 $cust = get_customer($myrow['debtor_no']);
			 $amount = $myrow["Total"];
			 $_POST['name'] = $cust["name"];
			 $_POST['debtor_id'] = $myrow['debtor_no']; //get debtor id for debtor_log table 
			 $contact_num  = $cust["debtor_ref"];
			 $objWsdl = new callWSDL();
			 $objWsdl->SendBulkSMS($contact_num, $_POST['message_']);
	  //  add_debtor_log(1,$_POST['type'],$_POST['debtor_id'],$_POST['temp_id'] ,$_POST['Order_no'],        $_POST['message_'],$_POST['mobile_no']);
		}
	/*	elseif($_POST['type']==2)
		{
			$booking_bankpay = get_booking($val);
			$cust_nakpay = get_customer($booking_bankpay['debtor_no']);
			$_POST['name'] = $cust_nakpay["name"]; 
			$_POST['debtor_id'] = $booking_bankpay['debtor_no']; //get debtor id for debtor_log table
			$contact_num = $cust_nakpay["debtor_ref"]; 
			$objWsdl = new callWSDL();
			$objWsdl->SendBulkSMS($contact_num, $_POST['message_']);
			add_debtor_log(1,$_POST['type'],$_POST['debtor_id'],$_POST['temp_id'] ,$_POST['Order_no'],        $_POST['message_'],$_POST['mobile_no']);
		}*/
	}
    display_notification(_("Sms Has been sent successfully"));
  	display_footer_exit();
}

$myrows = get_select_template($_POST['type']);
while($data = db_fetch($myrows))
{
	$Message = $data['template'];
    $temp_id = $data['id'];
}

//var_dump($_POST['temp_id']);
start_form();
start_table(TABLESTYLE, "width=60%");
$th = array( _("Customer Name"), _("Customer Number"), _("Message"));

table_header($th);
$k = 0;
var_dump($type);
foreach($src as $val){
switch ($type){ 
	/*case 62: //Final booking
        $fbooking = get_booking_orders($val);
        $myrow_fbooking = db_fetch($fbooking);
        $cust_id =  $myrow_fbooking["customer"];
        $amount = $myrow_fbooking["net_price"];
        $get_customer_fbooking = get_customer($cust_id);
        $_POST['name'] = $get_customer_fbooking["name"]; 
        $_POST['debtor_id'] = $myrow_fbooking["customer"]; //get debtor id for debtor_log table
        $contact_num  = $get_customer_fbooking["debtor_ref"];
	break;*/

	case 12: //customer payment
		$myrow = get_customer_trans($val, $type);
		$cust = get_customer($myrow['debtor_no']);
		$amount = $myrow["Total"];
		$_POST['name'] = $cust["name"];
		$_POST['debtor_id'] = $myrow['debtor_no']; //get debtor id for debtor_log table 
		$contact_num  = $cust["mobile_no"]; 
	break;
    
	/*case 41: //booking
        $booking = get_booking($val);
        $cust_booking = get_customer($booking['debtor_no']);
        $_POST['name'] = $cust_booking["name"];
        $_POST['debtor_id'] = $booking['debtor_no']; //get debtor id for debtor_log table 
        $contact_num  = $cust_booking["debtor_ref"]; 
	break;*/
    //case 1 and 2 = refund
    case 1: //bank payment
		$booking_bankdep =  get_customer_trans($val,1);
		$cust_bankdep = get_customer($booking_bankdep['debtor_no']);
		$amount = $booking_bankdep["Total"];
		$_POST['name'] = $cust_bankdep["name"];
		$_POST['debtor_id'] = $booking_bankdep['debtor_no']; //get debtor id for debtor_log table 
		$contact_num  = $cust_bankdep["mobile_no"]; 
	break;
    
  /*  case 2: //bank deposit
    $booking_bankpay = get_booking($val);
    $cust_nakpay = get_customer($booking_bankpay['debtor_no']);
    $_POST['name'] = $cust_nakpay["name"]; 
    $_POST['debtor_id'] = $booking_bankpay['debtor_no']; //get debtor id for debtor_log table
    $contact_num = $cust_nakpay["debtor_ref"]; 
	break;*/
    
	default :
    display_error("Try again ! Invalid entry");
}

    $_POST['mobile_no'] = $contact_num; //fetch num
    $name =  $_POST['name']; //fetch cust name
    $debtor_num = $_POST['debtor_id']; //for debtor_log
    $_POST['temp_id'] = $temp_id;
    
    //replace orignal template 
    $orignal  = array('reference','name','amount','date','outstanding','type','status');
	$replaced = array($Reference,$name,$amount,$Date,$balance,$type_name,$status);
	$sentence = str_replace($orignal,$replaced,$Message);

    $_POST['message_'] = $sentence;	
    
    hidden('message_',$sentence);
	hidden('mobile_no',$contact_num);




	//foreach($src as $val){
	alt_table_row_color($k);

	    label_cell($_POST['name']);
	    label_cell($contact_num);
	    label_cell($sentence);
	    end_row();		
}
end_table(1);
start_table(TABLESTYLE, "width=30%");
/*label_row(_("Customer Name:"),$_POST['name'], 30);
text_row_ex(_("Cell No :"), 'mobile_no', 30);	
textarea_row(_("Message"), 'message_', null, 35, 3);*/
hidden('temp_id',$temp_id);
hidden('debtor_id',$debtor_num);
hidden('type',$type);
hidden('Order_no',$trans_no);
end_table(1);
submit_center_first('ProcessOrder', _('Send'), _('Check entered data and save document'), 'default');
end_form();
end_page();

?>