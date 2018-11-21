<?php

/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */

 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
 echo "<form method='post'>
";
$link = mysqli_connect("localhost", $_GET['comp_db'], "myz47m", $_GET['comp_db']);

// Attempt update query execution
if($_GET['comp']==1)
{
    echo "<input type='submit' name='save' value='Active'/>";
echo "<input type='submit' name='save_' value='InActive'/></form>";
if(isset($_POST['save_']))
 {
    
     $sql =" UPDATE `0_sys_prefs` SET `value` = '1' WHERE `0_sys_prefs`.`name` = 'de_activate' ";
if(mysqli_query($link, $sql)){
    echo "Company Has been DeActivate";
} else {
    echo "ERROR: Could not able to execute $sql " . mysqli_error($link);
}
 }
 ////
 if(isset($_POST['save']))
 {
    
      $sql =" UPDATE `0_sys_prefs` SET `value` = '0' WHERE `0_sys_prefs`.`name` = 'de_activate' ";
if(mysqli_query($link, $sql)){
    echo "Company Has been Activate.";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 }
}
/////////////////////////
 if($_GET['comp']==2)
{
    echo "<input type='submit' name='save' value='Payroll Active'/>";
echo "<input type='submit' name='save_' value='Payroll InActive'/></form>";
if(isset($_POST['save_']))
 {
    
     $sql =" UPDATE `0_sys_prefs` SET `value` = '0' WHERE `0_sys_prefs`.`name` = 'use_payroll' ";
if(mysqli_query($link, $sql)){
    echo "Payroll Module Has been DeActivate";
} else {
    echo "ERROR: Could not able to execute $sql " . mysqli_error($link);
}
 }
 ////
 if(isset($_POST['save']))
 {
    
      $sql =" UPDATE `0_sys_prefs` SET `value` = '1' WHERE `0_sys_prefs`.`name` = 'use_payroll' ";
if(mysqli_query($link, $sql)){
    echo "Payroll Module Has been Activate.";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 }
}
////////////////////////
 if($_GET['comp']==3)
{
    echo "<input type='submit' name='save' value='Hr Active'/>";
echo "<input type='submit' name='save_' value='Hr InActive'/></form>";
if(isset($_POST['save_']))
 {
    
     $sql =" UPDATE `0_sys_prefs` SET `value` = '0' WHERE `0_sys_prefs`.`name` = 'use_hr' ";
if(mysqli_query($link, $sql)){
    echo "Hr Module Has been DeActivate";
} else {
    echo "ERROR: Could not able to execute $sql " . mysqli_error($link);
}
 }
 ////
 if(isset($_POST['save']))
 {
    
      $sql =" UPDATE `0_sys_prefs` SET `value` = '1' WHERE `0_sys_prefs`.`name` = 'use_hr' ";
if(mysqli_query($link, $sql)){
    echo "Hr Module Has been Activate.";
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}
 }
}
// Close connection
mysqli_close($link);


?>