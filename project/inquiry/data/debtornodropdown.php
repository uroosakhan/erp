<?php
header("Content-type: application/json");

include_once "connection.php";

    $res = mysql_query("SELECT   0_debtors_masterdemo.debtor_nu,0_debtors_masterdemo.debtor_no from  0_debtors_masterdemo ");

    while($obj = mysql_fetch_array($res)) {

        $arr[] = array(
            'debtor_nu' => str_replace("\'", "'", $obj["debtor_nu"]),
            'debtor_no' => str_replace("\'", "'", $obj["debtor_no"])


        );
    }

    print json_encode($arr);
//    print json_encode($arry);



?>