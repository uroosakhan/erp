<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");

$created_by = $_SESSION["wa_current_user"]->user;

	if (!defined('FLOAT_COMP_DELTA'))	
		define('FLOAT_COMP_DELTA', 0.004);

		$today = date2sql(Today());
		$sql = "SELECT trans.trans_no, trans.reference,	trans.tran_date, trans.due_date, debtor.debtor_no, 
			debtor.name, branch.br_name, debtor.curr_code,
			(trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount)	AS total,  
			(trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount - trans.alloc) AS remainder,
			DATEDIFF('$today', trans.due_date) AS days 	
			FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor, 
				".TB_PREF."cust_branch as branch
			WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
				AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount - trans.alloc) > ".FLOAT_COMP_DELTA." 
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC LIMIT 10";
		$result = db_query($sql);
		$title = db_num_rows($result) . " " . _("overdue Sales Invoices");
		
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/default.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/widget-styles.css" />';
		
		echo "<script language='javascript'>";
		echo get_js_open_window(900, 640);
		echo "</script>";

		display_heading($title);
		br();
		$th = array("#", _("Ref."), _("Date"), _("Due Date"), _("Customer"), _("Branch"), _("Currency"), _("Total"), _("Remainder"),	_("Days"));
		start_table(TABLESTYLE);
		foreach ($th as $label)
			echo "<td class='tableheader'>$label</td>\n";
		 
		$k = 0; //row colour counter
		while ($myrow = db_fetch($result))
		{
	    	alt_table_row_color($k);
	    	echo "<td>" . get_trans_view_str(ST_SALESINVOICE, $myrow["trans_no"]) . "</td>\n";
			echo "<td>" . $myrow['reference'] . "</td>\n";
			echo "<td>" . sql2date($myrow['tran_date']) . "</td>\n";
			echo "<td>" . sql2date($myrow['due_date']) . "</td>\n";
			echo "<td>" . $myrow["debtor_no"]." ".$myrow["name"] . "</td>\n";
			echo "<td>" . $myrow['br_name'] . "</td>\n";
			echo "<td>" . $myrow['curr_code'] . "</td>\n";
			echo "<td nowrap=\"nowrap\" align=\"right\">" . price_format($myrow['total']) . "</td>\n";
		    echo "<td nowrap=\"nowrap\" align=\"right\">" . $myrow['remainder'] . "</td>\n";
		    echo "<td align=\"right\">" . $myrow['days']  . "</td>\n";
			echo "</tr>\n";
		}
		end_table(1);
?>