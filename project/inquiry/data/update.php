<?php
header("Content-type: application/json");

include_once "connection.php";

    $priority_query = mysql_query("SELECT id FROM `0_priority` where priority='" . mysql_real_escape_string($_POST['models'][0]['priority'], $con) . "'");
    $prority_id = mysql_fetch_array($priority_query);

    $debtor_no_query = mysql_query("SELECT debtor_nu FROM `0_debtors_masterdemo` where debtor_no='" . mysql_real_escape_string($_POST['models'][0]['debtor_no'], $con) . "'");
    $debtor_no_id = mysql_fetch_array($debtor_no_query);

    $assign_by_query = mysql_query("SELECT id FROM `0_usersdemo` where assign_by='" . mysql_real_escape_string($_POST['models'][0]['assign_by'], $con) . "'");
    $assign_by_id = mysql_fetch_array($assign_by_query);

    $status_query = mysql_query("SELECT id FROM `0_pstatus` where status='" . mysql_real_escape_string($_POST['models'][0]['status'], $con) . "'");
    $status_id = mysql_fetch_array($status_query);


    $progress_query = mysql_query("SELECT id FROM `0_progress` where progress='" . mysql_real_escape_string($_POST['models'][0]['progress'], $con) . "'");
    $progress_id = mysql_fetch_array($progress_query);

    $rs = mysql_query("UPDATE 0_task SET
             
            
              debtor_no = '" .$debtor_no_id['debtor_nu']  . "', 
              priority = '" . $prority_id['id'] . "', 
              assign_by = '" .$assign_by_id['id']  . "', 
              description = '" . mysql_real_escape_string($_POST['models'][0]['description'], $con) . "', 
              remarks = '" . mysql_real_escape_string($_POST['models'][0]['remarks'], $con) . "', 
              start_date = '" . mysql_real_escape_string($_POST['models'][0]['start_date'], $con) . "', 
              status = '" .$status_id['id']  . "', 
              progress = '" . $progress_id['id']  . "', 
              end_date = '" . mysql_real_escape_string($_POST['models'][0]['end_date'], $con) . "', 
              plan = '" . mysql_real_escape_string($_POST['models'][0]['plan'], $con) . "',
              actual = '" . mysql_real_escape_string($_POST['models'][0]['actual'], $con) . "', 
              Stamp = '" . mysql_real_escape_string($_POST['models'][0]['Stamp'], $con) . "'

                 WHERE id = " . $_POST['models'][0]['id']);


    if ($rs) {
        echo json_encode($rs);
    }
    else {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Failed on update: " . $_POST['models'][0]['description'];
    }

?>