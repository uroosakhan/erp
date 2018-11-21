<?php
include_once("config_db.php");
//$dbh1 = mysql_connect(localhost, USERNAME, PASSWORD);  
//mysql_select_db('DBNAME', $dbh1); 
    echo '
    <style>
    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width = 100%
    }
    td, th {
        border: 1px solid #FF0000;
        text-align: left;
        padding: 1px;
        white-space:nowrap;
    }
    tr:nth-child(even) {
        background-color: #dddddd;
    }
    </style>
    ';

    global $tb_pref_counter;
    $counter = '';

    $total_companies = $tb_pref_counter-1;

        $dbh_main = mysql_connect(localhost, 'cloudso1', '#hnK}bwz$JmnBa52');  
        $today = date("d-m-Y");
        mysql_select_db(information_schema, $dbh_main); 

        $result_count = mysql_query("SELECT count(*) FROM (
      SELECT DISTINCT TABLE_SCHEMA FROM information_schema.SCHEMA_PRIVILEGES WHERE GRANTEE LIKE '%cloudso1%' GROUP BY TABLE_SCHEMA
    ) AS baseview;", $dbh_main);

            $row_count = mysql_fetch_array($result_count); 
            echo "<b>";
            echo "Date: " . $today . "<br>";
            echo "</br>";
            
            echo "<b>";
            echo "TOTAL INSTALATIONS = ";
            echo "<font color='red'> ";
            echo "$total_companies";
            echo "</font>";
            echo "</br>";
            echo "TOTAL DBs";
            echo "<b>";
            echo " = ";
            echo "<font color='red'> ";
            echo $row_count[0];
            echo "</font>";


    echo "</br>";
    
    $link = mysql_connect('localhost', 'cloudso1', '#hnK}bwz$JmnBa52');
    $db_list = mysql_list_dbs($link);
    echo '<table bgcolor=#FFA500>';

    while ($row = mysql_fetch_object($db_list))
    {
        echo '<td>';
        $sr +=1;
        echo "<b>";
        echo $sr;
        echo "</b>";
        echo " ";        
        echo $row->Database . "\n";
        echo '</td>';

    }

    echo '</table>';

    echo "</br>";
            echo "<a href ='http://www.erp30.com/db_check.php' target= _blank> CHECK DB</a>";

    echo "</br>";

//  Company Heading
    for ($x = 0; $x <= $total_companies; $x++) {
    echo "</br>";
 //   echo "</br>";
    echo  '<table bgcolor="#FF1493">';
    echo  '<th>';

    echo "<b>";
    echo "TB PREF = ";
    echo "$x" ;
    echo " / COMPANY = ";
    echo $db_connections["$x"]["name"];
    echo " / DB NAME = "; 
    echo $db_connections["$x"]["dbname"];
    echo "</b>";

    echo  '</th>';
    echo  '<table>';
    
 //   echo "</br>";

    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  

    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

    $get_users["$x"] = mysql_query("SELECT * from 0_users ORDER BY last_visit_date DESC", $dbh["$x"]);

    $get_last_trans["$x"] = mysql_query("SELECT type, trans_no, stamp from 0_audit_trail ORDER BY stamp DESC LIMIT 1", $dbh["$x"]);

    $get_chart_classes["$x"] = mysql_query("SELECT cid, class_name from 0_chart_class", $dbh["$x"]);

    $get_rep_no["$x"] = mysql_query("SELECT * from 0_reflines WHERE description != '' ORDER BY id", $dbh["$x"]);

    $get_sys_prefs["$x"] = mysql_query("SELECT value from 0_sys_prefs WHERE name = 'alt_uom' ", $dbh["$x"]);

    //get users data
    echo '
    <table bgcolor=	#AFEEEE>';
    
    while ($row = mysql_fetch_array($get_users["$x"])) {  

    $date = strtotime($row["last_visit_date"]);
    $date_display = date('d-m-Y H:i:s', $date);
    $only_date = date('d-m-Y', $date); //date without showing time

    echo "<td>";
        

    if ($today == $only_date)
    {
    echo "<font color='red'> ";
    echo "<b>";
    echo $row['user_id'];
    echo "</b> ";
    echo $date_display ;  
    echo "</font> ";

    echo " ";
    }
    else
    {
    echo $row['user_id'];
    echo " ";    
    echo $date_display ;  
    }
    echo "</b>";
    echo " ";
    echo "<i>";
    echo $row['theme'] ;  
    echo "</i>";
    echo "</td>";
    
    $counter = $x;

}  
    echo '
    </table>';
    
    //Reports Numbers
    echo  '<table bgcolor=#FFFF00>';
    while ($row = mysql_fetch_array($get_rep_no["$x"])) {  

    echo "<td>";
    //echo "Type = ";
    echo $row['trans_type'] ;
    echo " / ";
    echo $row['prefix'];
    echo " / ";
    echo "<b>";
    echo "rep";
    echo $row['description'];
    echo "</b>";
    echo "</td>";
    }  
    echo  '</table>';

    //Last Transaction Data
    while ($row = mysql_fetch_array($get_last_trans["$x"])) {  

    $date = strtotime($row["stamp"]);
    $date_display = date('d-m-Y H:i:s', $date);


    echo "<i>";
    echo "Latest transaction entered on $date_display ";
    echo "Type = ";
    echo $row['type'] ;
    echo " Trans No. = ";
    echo $row['trans_no'];

    echo "</i>";
    }

    echo  '</br>';
    //chart classes
    echo  '<table bgcolor=#42f46b>';
  while ($row = mysql_fetch_array($get_chart_classes["$x"])) {  

    echo "<td>";
    echo "<i>";
    echo "Class ID = ";
    echo $row['cid'] ;
    echo " Class Name = ";
    echo $row['class_name'];

    echo "</i>";
    echo "</td>";
    }
         echo  '</table>';
    echo  '</br>';

  while ($row = mysql_fetch_array($get_sys_prefs["$x"])) {  

    echo "<td>";
    echo "<i>";
    echo "Alternate UoM = ";
    if($row['value'] == 1)
    $enable = Enabled;
    else
    $enable = Disabled;

    echo $enable;

    echo "</i>";
    echo "</td>";
    }


} 




?>