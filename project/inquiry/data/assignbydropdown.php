<?php
header("Content-type: application/json");

include_once "connection.php";

$res = mysql_query("SELECT   0_users.id,0_users.user_id from  0_users ");

while($obj = mysql_fetch_array($res)) {

    $arr[] = array(
        'id' => str_replace("\'", "'", $obj["id"]),
        'user_id' => str_replace("\'", "'", $obj["user_id"])


    );
}

print json_encode($arr);
//    print json_encode($arry);



?>