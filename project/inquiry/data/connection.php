<?php

//$con = mysql_connect("localhost", "root", "");
//if (!$con)
//{
//    die('Could not connect: ' . mysql_error());
//} else {
//    mysql_select_db("myworkle_dys1", $con);
//
//
//}
//////////////////
$db_connections = array (
    0 =>
        array (
            'name' => 'DYS',
            'host' => 'localhost',
            'dbuser' => 'myworkle_dys',
            'dbpassword' => 'myz47m',
            'dbname' => 'myworkle_dys',
            'TB_PREF' => '0_',
        ),

);
foreach($db_connections as $id=>$con) {
    $name = $con['name'];
    $host = $con['host'];
    $dbuser = $con['dbuser'];
    $dbpassword = $con['dbpassword'];
    $database = $con['dbname'];
    $tbpref = $con['TB_PREF'];
    // $tbpref = TB_PREF;
    $con = mysql_connect($host, $dbuser, $dbpassword);
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }else {
        mysql_select_db($database, $con);
//        var_dump($database);
    }
}


?>