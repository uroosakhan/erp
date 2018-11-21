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

//    $date= date("Y-m-d");


    $rs = mysql_query("INSERT INTO 0_task
(                           
                                        debtor_no,
                                        priority,
                                        assign_by,   
                                        description,
                                        remarks,
                                        start_date,
                                        status,
                                        progress,
                                        end_date,
                                        plan,
                                        actual,
                                        Stamp
                                      
                                                                                           
                                                   
                                                        
)
 VALUES(
 
        '" .$debtor_no_id['debtor_nu']  . "', 
            '" . $prority_id['id'] . "', 
              '" .$assign_by_id['id']  . "', 
        '" . mysql_real_escape_string($_POST['models'][0]['description'], $con) . "',
        '" . mysql_real_escape_string($_POST['models'][0]['remarks'], $con) . "',
              '" . mysql_real_escape_string($_POST['models'][0]['start_date'], $con) . "',

        '" .$status_id['id']  . "', 
          '" . $progress_id['id']  . "', 
        '" . mysql_escape_string($_POST['models'][0]['end_date'], $con) . "',
        '" . mysql_real_escape_string($_POST['models'][0]['plan'], $con) . "',
        '" . mysql_real_escape_string($_POST['models'][0]['actual'], $con) . "',
        '" . mysql_real_escape_string($_POST['models'][0]['Stamp'], $con) . "'
 )");
//'" . mysql_real_escape_string(date('Y-m-d H:i:s',strtotime($_POST['models'][0]['start_date'], $con))) . "',
//         date('Y-m-d H:i:s',strtotime($_POST['PlayerBdate']));
    if ($rs) {
        echo json_encode($rs);
    }
    else {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Failed on insert: " . $_POST['models'][0]['description'];
    }

?>