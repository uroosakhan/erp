<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");

	$resp = array();
	$data=json_decode(htmlspecialchars_decode($_POST["data"]));
	
	foreach($data->items as $item)
	{
		$col_id=preg_replace('/[^\d\s]/', '', $item->column);
		$widget_id=preg_replace('/[^\d\s]/', '', $item->id);	

		$sql="UPDATE ".TB_PREF."widgets SET column_id=" . db_escape($col_id) . ", sort_no=" . db_escape($item->order) . " WHERE id=" . db_escape($widget_id);
		db_query($sql, "Error updating widgets");
		
		if (db_error_no() == 0) {
			$resp = array("success" => true, "message" => _("Your widgets have been updated successfully."));
		} else {
			$resp = array("error" => false, "message" => _("Sorry, your widgets cannot be updated."));
		}		
	}

	//Return the result back to UI
	echo json_encode($resp);
	exit; // only print out the json version of the response
?>