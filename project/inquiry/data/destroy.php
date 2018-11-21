<?php
header("Content-type: application/json");

include_once "connection.php";

    $rs = mysql_query("DELETE  FROM 0_task WHERE id = " . $_POST['models'][0]['id']);
    if ($rs) {
        echo json_encode($rs);
    }
    else {
        header("HTTP/1.1 500 Internal Server Error");
        echo "Failed on delete: " . $_POST['models'][0]['description'];
    }

?>