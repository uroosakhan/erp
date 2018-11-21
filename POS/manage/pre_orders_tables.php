<?php
$page_security = 'SA_SALESTYPES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Main Tables "));

include_once($path_to_root . "/includes/ui.inc");
//include_once($path_to_root . "/POS/includes/db/sales_types_db.inc");

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
	$sql = "SELECT `total` FROM `".TB_PREF."sales_orders`
WHERE  ".TB_PREF."sales_orders.debtor_no=$customer_id
AND ".TB_PREF."sales_orders.order_no=$order_no";

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_cus_for_table()
{
	$sql ="SELECT * FROM `".TB_PREF."debtors_master` ";
	$db = db_query($sql,'error');
//	$ft = db_fetch($db);
	return $db;
}

start_table(TABLESTYLE2, "width=80%");
//Main Div
echo'<style>
.tableone
{
    height: 160px;width:155px;background-color:#3EA5F4;
	color:white;border: 1px #e7e7e7 solid;float:left;margin-left:105px;
	margin-top: 10px;font-size: 14px;font-family:arial, verdana, helvetica, sans-serif;
	font-weight:bold;border-radius: 5px;
}
.tabletwo
{
height: 160px;width:155px;border: 1px #e7e7e7 solid;
			float:left;margin-left:15px;margin-top: 10px;background-color:#f32e2e;
			color:white;font-size: 14px;
				font-family:arial, verdana, helvetica, sans-serif;
				font-weight:bold;border-radius: 5px;
}

</style>';
echo'<div style="font-size: 26px;">Tables</div>';
echo'<div style="height:;width:90%;" >';

echo '<div class="tableone" >';
echo '<div style="margin-top: 35px;text-transform: uppercase;">
		<label >Dine In </label></div>';

hyperlink_no_params_ord_table("../sales_order_entry.php?NewOrder=Yes", _("SELECT TABLE"));
echo'</div>';

// --------------- Take away ---
echo '<div class="tableone" >';
echo '<div style="margin-top: 35px;text-transform: uppercase;">
		<label > Delivery </label></div>';

hyperlink_no_params_ord_table("../manage/orders_tables_delivery.php?NewOrder=Yes", _("Take Order"));

echo'</div>';

// --------------- Delivery ---
echo '<div class="tableone" >';
echo '<div style="margin-top: 35px;text-transform: uppercase;">
		<label> Take Away </label></div>';

hyperlink_no_params_ord_table("../manage/orders_tables_take_away.php?NewOrder=Yes", _("Take Order"));

echo'</div>';


echo'</div></div>';
//Main Div


end_table(1);



end_page_table();

?>
