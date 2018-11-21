<?php
$page_security = 'SA_SALESTYPES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Tables "));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/POS/includes/db/sales_types_db.inc");

include_once($path_to_root . "/POS/includes/sales_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");

simple_page_mode(true);

function hyperlink_no_params_ord_table($target, $label, $center=true)
{
	$id = default_focus();
	$pars = access_string($label);
	if ($target == '')
		$target = $_SERVER['PHP_SELF'];
	if ($center)
		echo "<br><center >";
	echo "<a style='color:white;font-size: 14px;
				font-family:arial, verdana, helvetica, sans-serif;font-weight:bold;' 
				href='$target' id='$id' $pars[1]>$pars[0]</a>\n";
	if ($center)
		echo "</center>";
}

function display_note_settle($msg, $br=0, $br2=0, $extra="")
{
    for ($i = 0; $i < $br; $i++)
        echo "<br>";
    if ($extra != "")
        echo "<center><span $extra>$msg</span></center>\n";
    else
        echo "<center><span >$msg</span></center>\n";
    for ($i = 0; $i < $br2; $i++)
        echo "<br>";


}
//----
function submenu_option_settle($title, $url, $id=null)
{
    global $path_to_root;
    display_note_settle(menu_link_settle($path_to_root . $url, $title, $id), 0, 1);
}
function menu_link_settle($url, $label, $id=null)
{

    $id = default_focus($id);
    $pars = access_string($label);
    return "<a  href='$url' class='menu_option' style='color:white;font-size: 14px;
				font-family:arial, verdana, helvetica, sans-serif;font-weight:bold;' id='$id' $pars[1]>$pars[0]</a>";
}


function submenu_thermal_print($title, $type, $number, $id=null, $email=0, $extra=0)
{
    display_note(print_document_link($number, $title, true, $type, false, 'printlink', $id, $email, $extra), 0, 1);
}

function get_customer_order_qty($customer_id)
{
	$sql = "SELECT SUM(".TB_PREF."sales_order_details.`qty_sent`) AS qty FROM `".TB_PREF."sales_orders`
			INNER JOIN ".TB_PREF."sales_order_details ON ".TB_PREF."sales_orders.order_no=".TB_PREF."sales_order_details.order_no
			WHERE  ".TB_PREF."sales_orders.debtor_no=$customer_id";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_customer_order_max($customer_id)
{
	$sql = "SELECT MAX(`order_no`) FROM `".TB_PREF."sales_orders`
			WHERE  ".TB_PREF."sales_orders.debtor_no=$customer_id";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_customer_order_quantity($customer_id)
{
	$sql = "SELECT SUM(".TB_PREF."sales_order_details.`quantity`) AS qty FROM `".TB_PREF."sales_orders`
			INNER JOIN ".TB_PREF."sales_order_details ON ".TB_PREF."sales_orders.order_no=".TB_PREF."sales_order_details.order_no
			WHERE  ".TB_PREF."sales_orders.debtor_no=$customer_id";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_customer_order_amount($customer_id,$order_no)
{
	$sql = "SELECT (total - (total * total_discount_pos / 100)) FROM `".TB_PREF."sales_orders`
WHERE  ".TB_PREF."sales_orders.debtor_no='$customer_id'
AND ".TB_PREF."sales_orders.order_no='$order_no'";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function check_recieve_payment($order_no, $type)
{
	$sql = "SELECT  SUM(`quantity`) AS qty, SUM(`qty_sent`) AS qty_snt  
			FROM " . TB_PREF . "sales_order_details WHERE `order_no`=" . db_escape($order_no) . " AND 
			`trans_type`=" . db_escape($type) . " ";
	$result = db_query($sql, "Could not get delivery details.");
	$row = db_fetch_row($result);
	$amt = $row['0'] - $row['1'];

	return $amt;
}

function get_cus_for_table()
{
	$sql ="SELECT * FROM `".TB_PREF."debtors_master` where table_type=0  GROUP BY debtor_no ASC";
	$db = db_query($sql,'error');
//	$ft = db_fetch($db);
	return $db;
}

start_table(TABLESTYLE2, "width=80%");
//Main Div

if(user_theme() == 'premium') {
	echo '<style>
.tableone
{
    height:345px;width:200px;background-color:#3EA5F4;
	color:white;border: 1px #e7e7e7 solid;float:left;margin-left:15px;
	margin-top: 10px;font-size: 14px;font-family:arial, verdana, helvetica, sans-serif;
	font-weight:bold;border-radius: 5px;
}
.tabletwo
{
height: 345px;width:200px;border: 1px #e7e7e7 solid;
			float:left;margin-left:15px;margin-top: 10px;background-color:#f32e2e;
			color:white;font-size: 14px;
				font-family:arial, verdana, helvetica, sans-serif;
				font-weight:bold;border-radius: 5px;
}

</style>';
} else {
	echo '<style>
.tableone
{
    height: 285px;width:155px;background-color:#3EA5F4;
	color:white;border: 1px #e7e7e7 solid;float:left;margin-left:15px;
	margin-top: 10px;font-size: 14px;font-family:arial, verdana, helvetica, sans-serif;
	font-weight:bold;border-radius: 5px;
}
.tabletwo
{
height: 285px;width:155px;border: 1px #e7e7e7 solid;
			float:left;margin-left:15px;margin-top: 10px;background-color:#f32e2e;
			color:white;font-size: 14px;
				font-family:arial, verdana, helvetica, sans-serif;
				font-weight:bold;border-radius: 5px;
}

</style>';
}



global $Ajax;
echo'
<script>

function myFunction(a)
{

     var total_amount = document.getElementById("total_amount"+a).value;
     var order_no = document.getElementById("order_num"+a).value;

   
	 var cash_recieved = document.getElementById("cash_recieved"+a).value;
	 var remaining_amount = total_amount - cash_recieved ;

	 document.getElementById(a).value = remaining_amount;
	
	
                var i= 1;
                
                $.ajax({
                         type: "POST",
                         url: "../manage/handler.php?id="+cash_recieved+"&&order_no="+order_no,
                         async: false,
                         success: function (response) 
                             {
                                 var result = response;
                              	 console.log(result);
           					 },
                             failure: function (msg) {
                               alert(msg);
                             }
                         });

}


</script>
';

echo'<div style="font-size: 26px;">Dine In</div>';
echo'<div style="height:;width:90%;" >';

$data = get_cus_for_table();
$num = 1;
while($myrow = db_fetch($data))
{

	$max_order=get_customer_order_max($myrow['debtor_no']);

//table Div
	$amount = 0;
	if(check_recieve_payment($max_order, ST_SALESORDER) != 0)
	{
		$amount = get_customer_order_amount($myrow['debtor_no'], $max_order);
	}


	if($amount != 0 ) {
		echo '<div class="tabletwo">';
	}
	else {
		echo '<div class="tableone" >';
	}


	echo '<div style="margin-top: 35px;text-transform: uppercase;"><label >'.$myrow['name'].'</label></div>';

	



	if($amount == 0)
	{
		hyperlink_no_params_ord_table("../sales_order_entry.php?NewOrder=Yes&&customer_id=".$myrow['debtor_no']."", _("Order"));
	}
	else//if($amounts !=0)
	{
		

		hyperlink_no_params_ord_table("../sales_order_entry.php?ModifyOrderNumber=$max_order&&customer_id=".$myrow['debtor_no']."&&change=YES", _("Modify Order"));
		//hyperlink_no_params_ord_table("../sales_order_entry.php?ModifyOrderNumber=$max_order&&customer_id=".$myrow['debtor_no']."&&ord_new=YES", _("Add New Item"));
//		var_dump($max_order);

		submenu_thermal_print(_("&Print KOT"), ST_SALESORDER2, $max_order, 'prtopt');
		submenu_thermal_print(_("&Print Pre Bill"), ST_SALESORDER6, $max_order, 'prtopt');


if($max_order!='') {

		echo '<div style="margin-top: 10px;"> AMOUNT 
				<label style="font-weight:bold" > :' . $amount . ' </label></div>';
	}else
	{
		echo '<div style="margin-top: 10px;"> AMOUNT 
				<label style="font-weight:bold" > : ' . $amount . ' </label></div>';
	}
	//echo '<div style="margin-top: 2px;">';
		echo"<input type='hidden' value='$amount' id='total_amount$num' name='txt_total_amnt'>";
		echo"<input type='hidden' value='$max_order' id='order_num$num' >";


		echo"<input type='text' style='color:black' placeholder=' Cash Recieved' onfocusout='myFunction($num)' id='cash_recieved$num' name='txt_cash_recieved' >";
		echo"<input type='text' style='color:black' placeholder=' Cash Return'  style='margin-top:5px;' id='$num' name='cash$num' readonly>";
echo'<br/><br/>';

submenu_option_settle(_("Settle"), "/POS/cust_delivery.php?OrderNumber=".$max_order."&&cashrecieved=".$cash, ICON_DOC,
			_("You are about to generate DN and Invoice. Do you want to continue?") );

	//	submenu_print(_("&Print This Order"), ST_SALESORDER, $max_order, 'prtopt');
	}



	echo '

 </div>
	</div>';
//table Div
	$num++;
}

echo'</div>';
//Main Div



end_table(1);



end_page_table();

?>
