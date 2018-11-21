<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/sales/includes/db/sales_order_db.inc");

$created_by = $_SESSION["wa_current_user"]->user;

	$sql = get_sql_for_sales_orders_view(-1, ST_SALESORDER, '', '',null, add_days(Today(), -30), add_days(Today(), 1));
	$sql .= "  ORDER BY ord_date DESC LIMIT 10";
		$result = db_query($sql);
		$title = db_num_rows($result) . " " .  _("recent sales order");
		
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/default.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/widget-styles.css" />';
		
		echo "<script language='javascript'>";
		echo get_js_open_window(900, 640);
		echo "</script>";

		display_heading($title);
		br();
		$th = array(
		_("Order #"),
		_("Ref"),
		_("Customer"),
		_("Branch"),		
		_("Order Date"),	
		_("Required By"),
		_("Currency"), 
		_("Order Total"),		
		_("Delivery To")
		);
		start_table(TABLESTYLE);
		foreach ($th as $label)
			echo "<td class='tableheader'>$label</td>\n";
		 
		$k = 0; //row colour counter
		while ($myrow = db_fetch($result))
		{
	    	alt_table_row_color($k);
	    	echo "<td>" . get_trans_view_str(ST_SALESORDER, $myrow["order_no"]) . "</td>\n";
			echo "<td>" . $myrow['reference'] . "</td>\n";
			echo "<td>" . $myrow["name"] . "</td>\n";
			echo "<td>" . $myrow['br_name'] . "</td>\n";
			echo "<td>" . sql2date($myrow['ord_date']) . "</td>\n";
			echo "<td>" . sql2date($myrow['delivery_date']) . "</td>\n";			
			echo "<td>" . $myrow['curr_code'] . "</td>\n";
			echo "<td nowrap=\"nowrap\" align=\"right\">" . price_format($myrow['OrderValue']) . "</td>\n";
		    echo "<td nowrap=\"nowrap\" align=\"right\">" . $myrow['TotQuantity'] . "</td>\n";
			echo "</tr>\n";
		}
		end_table(1);

?>