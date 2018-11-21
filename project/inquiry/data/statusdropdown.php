<?php
header("Content-type: application/json");

include_once "connection.php";
    $res = mysql_query("SELECT   0_pstatus.id, 0_pstatus.status from  0_pstatus ");

    while($obj = mysql_fetch_array($res)) {

        $arr[] = array(
            'id' => str_replace("\'", "'", $obj["id"]),
            'status' => str_replace("\'", "'", $obj["status"])


        );
    }


    print json_encode($arr);
//    print json_encode($arry);



?>