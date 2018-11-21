<?php
header("Content-type: application/json");

include_once "connection.php";

    $res = mysql_query("SELECT  0_priority.id,0_priority.priority from 0_priority ");

    while($obj = mysql_fetch_array($res)) {

        $arr[] = array(
            'id' => str_replace("\'", "'", $obj["id"]),

            'priority' => str_replace("\'", "'", $obj["priority"])


        );
    }


    print json_encode($arr);
//    print json_encode($arry);



?>