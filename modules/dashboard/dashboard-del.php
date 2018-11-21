<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");

	$resp = array();
	$id=json_decode($_POST["id"]);

	$sql="DELETE FROM ".TB_PREF."widgets WHERE id=" . db_escape($id);
	db_query($sql, "Error deleting widgets");
	
	if (db_error_no() == 0) {
		$resp = array("success" => true, "message" => _("Your record have been deleted successfully."));
	} else {
		$resp = array("error" => false, "message" => _("Sorry, your record cannot be deleted."));
	}		

	//Return the result back to UI
	echo json_encode($resp);
	exit; // only print out the json version of the response
	
?>