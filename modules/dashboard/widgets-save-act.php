<?php

$page_security = 'SS_DASHBOARD';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");

$created_by = $_SESSION["wa_current_user"]->user;


	$error = '';
	
	$id = trim($_POST['Id']);
	$column_id = "1";	
	
	$resp = array();
	
	$sql = "INSERT INTO ".TB_PREF."widgets (user_id,column_id,sort_no,collapsed,url,title,source,width,height,is_system,created_by,dt_created) SELECT '$created_by','$column_id',sort_no,collapsed,url,title,source,width,height,is_system,'$created_by',now() FROM ".TB_PREF."widgets_template WHERE id = " . db_escape($id);
	
	db_query($sql, "Error insertion widgets");
	
	if (db_error_no() == 0) {
		$resp = array("success" => true, "message" => _("Your widgets have been added successfully."));
	} else {		
		$resp = array("error" => false, "message" => _("Sorry, your widgets cannot be added."));
	}
		
	//Return the result back to UI
	echo json_encode($resp);
	exit;
?>