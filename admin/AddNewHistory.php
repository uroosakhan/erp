<?php
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/admin/db/company_db.inc");

$id = $_GET['id'];
$user_id = $_GET['user_id'];
// ==============================GET MODULE CODE===================================
$Separate = explode('-', $id);
$sql = "SELECT name FROM 0_sys_prefs_new WHERE value = '$Separate[1]'";
$query = db_query($sql, "Error");
$Fetch = db_fetch_row($query);
// ==============================INSERT TAB HISTORY================================
$sql = "INSERT INTO `0_history_tabs`(`users_id`, `url`, `tab_name`) VALUES ('$user_id','$Separate[0]','$Fetch[0]')";
db_query($sql, "Error");
