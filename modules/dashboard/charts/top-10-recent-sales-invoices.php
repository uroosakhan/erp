<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/sales/includes/db/sales_order_db.inc");

$created_by = $_SESSION["wa_current_user"]->user;

	$date_after = date2sql(add_days(Today(), -30));
	$date_to =date2sql(add_days(Today(), 1));
	
	$sql = "SELECT 
  		trans.type, 
		trans.trans_no, 
		trans.order_, 
		trans.reference,
		trans.tran_date, 
		trans.due_date, 
		debtor.name, 
		branch.br_name,
		debtor.curr_code,
		(trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount)	AS TotalAmount, "; 
		$sql .= "trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue ,
		Sum(line.quantity-line.qty_done) AS Outstanding
		FROM "
			.TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."debtor_trans_details as line
				ON trans.trans_no=line.debtor_trans_no AND trans.type=line.debtor_trans_type,"
			.TB_PREF."debtors_master as debtor, "
			.TB_PREF."cust_branch as branch
		WHERE debtor.debtor_no = trans.debtor_no
			AND trans.tran_date >= '$date_after'
			AND trans.tran_date <= '$date_to'
			AND trans.branch_code = branch.branch_code";

   			$sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";
 
    		$today =  date2sql(Today());
    		//$sql .= " AND trans.due_date < '$today'
			$sql .= " AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax + 
				trans.ov_freight + trans.ov_discount - trans.alloc > 0) ";
   	
		$sql .= " GROUP BY trans.trans_no, trans.type";
		$sql .= "  ORDER BY tran_date DESC LIMIT 10";

		$result = db_query($sql);
		$title = db_num_rows($result) . " " . _("recent sales invoices");
		
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/default.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/widget-styles.css" />';
		
		echo "<script language='javascript'>";
		echo get_js_open_window(900, 640);
		echo "</script>";

		display_heading($title);
		br();
		$th = array(
		_("#"),
		_("Ref"),		
		_("Customer"),
		_("Branch"),
		_("Date"),
		_("Due Date"),
		_("Currency"), 		
		_("Total")
		);
		start_table(TABLESTYLE);
		foreach ($th as $label)
			echo "<td class='tableheader'>$label</td>\n";
		 
		$k = 0; //row colour counter
		while ($myrow = db_fetch($result))
		{
	    	alt_table_row_color($k);
	    	echo "<td>" . get_trans_view_str(ST_SALESINVOICE, $myrow["trans_no"]) . "</td>\n";
			echo "<td>" . $myrow['reference'] . "</td>\n";			
			echo "<td>" . $myrow["name"] . "</td>\n";
			echo "<td>" . $myrow['br_name'] . "</td>\n";
			echo "<td>" . sql2date($myrow['tran_date']) . "</td>\n";
			echo "<td>" . sql2date($myrow['due_date']) . "</td>\n";
			echo "<td>" . $myrow['curr_code'] . "</td>\n";
			echo "<td nowrap=\"nowrap\" align=\"right\">" . price_format($myrow['TotalAmount']) . "</td>\n";
			echo "</tr>\n";
		}
		end_table(1);

?>