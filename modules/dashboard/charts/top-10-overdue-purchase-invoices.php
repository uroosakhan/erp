<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");

$created_by = $_SESSION["wa_current_user"]->user;

	if (!defined('FLOAT_COMP_DELTA'))	
		define('FLOAT_COMP_DELTA', 0.004);
			
	$today = date2sql(Today());
		$sql = "SELECT trans.trans_no, trans.reference, trans.tran_date, trans.due_date, s.supplier_id, 
			s.supp_name, s.curr_code,
			(trans.ov_amount + trans.ov_gst + trans.ov_discount) AS total,  
			(trans.ov_amount + trans.ov_gst + trans.ov_discount - trans.alloc) AS remainder,
			DATEDIFF('$today', trans.due_date) AS days 	
			FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as s 
			WHERE s.supplier_id = trans.supplier_id
				AND trans.type = ".ST_SUPPINVOICE." AND (ABS(trans.ov_amount + trans.ov_gst + 
					trans.ov_discount) - trans.alloc) > ".FLOAT_COMP_DELTA."
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC";
		$result = db_query($sql);
		$title = db_num_rows($result) ." " . _("overdue Purchase Invoices");
		
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/default.css" />';
		echo '<link rel="stylesheet" type="text/css" href="' . $path_to_root . '/themes/'.user_theme(). '/widget-styles.css" />';
		
		echo "<script language='javascript'>";
		echo get_js_open_window(900, 640);
		echo "</script>";

		display_heading($title);
		br();
		$th = array("#", _("Ref."), _("Date"), _("Due Date"), _("Supplier"), _("Currency"), _("Total"),	_("Remainder"),	_("Days"));
		start_table(TABLESTYLE);
		foreach ($th as $label)
			echo "<td class='tableheader'>$label</td>\n";
		 
		$k = 0; //row colour counter
		while ($myrow = db_fetch($result))
		{
	    	alt_table_row_color($k);
	    	echo "<td>" . get_trans_view_str(ST_SUPPINVOICE, $myrow["trans_no"]) . "</td>\n";
			echo "<td>" . $myrow['reference'] . "</td>\n";
			echo "<td>" . sql2date($myrow['tran_date']) . "</td>\n";
			echo "<td>" . sql2date($myrow['due_date']) . "</td>\n";
			echo "<td>" . $myrow["supplier_id"]." ".$myrow["supp_name"] . "</td>\n";
			echo "<td>" . $myrow['curr_code'] . "</td>\n";
			echo "<td nowrap=\"nowrap\" align=\"right\">" . price_format($myrow['total']) . "</td>\n";
		    echo "<td nowrap=\"nowrap\" align=\"right\">" . $myrow['remainder'] . "</td>\n";
		    echo "<td align=\"right\">" . $myrow['days']  . "</td>\n";
			echo "</tr>\n";
		}
		end_table(1);
?>