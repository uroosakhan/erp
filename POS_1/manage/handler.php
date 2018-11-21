<?php
$page_security = 'SA_SALESTYPES';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");

page(_($help_context = "Tables "));

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/POS/includes/db/sales_types_db.inc");


$id = $_GET['id']; // Get cash received amount
$order_no = $_GET['order_no'];
$sql = "UPDATE `".TB_PREF."sales_orders` SET `cash_recieved`=".$id."  WHERE order_no=".$order_no;
db_query($sql,'asaddsa');

?>