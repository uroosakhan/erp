<?php
header("Content-type: application/json");

include_once "connection.php";

    $res = mysql_query("SELECT 0_progress.id,0_progress.progress from   0_progress ");

    while($obj = mysql_fetch_array($res)) {

        $arr[] = array(
            'id' => str_replace("\'", "'", $obj["id"]),

            'progress' => str_replace("\'", "'", $obj["progress"])


        );
    }


    print json_encode($arr);
//    print json_encode($arry);


?>