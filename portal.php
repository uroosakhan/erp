<?php
include_once("config_db.php");

echo '
<style>
.myTable { 
  width: 100%;
  text-align: left;
  background-color: lemonchiffon;
  border-collapse: collapse; 
  }
  
  
  .a{
      background:#cecdcd
  }
.myTable th { 
  background-color: goldenrod;
  color: white; 
  }
.myTable td, 
.myTable th { 
  
  border: 1px solid goldenrod; 
  }
.cd{
width: 100px;
}

.val{
width: 290px;
}

.usr{
width: 90px;
}

.lst{
width: 155px;
}

.prs{
width: 75px;
}

.rep{
width: 365px;
}

.cname{
width: 300px;
}



</style>

';
    global $tb_pref_counter, $SysPrefs;
    $counter = '';

    $total_companies = $tb_pref_counter-1;
    $table_name = '0_debtor_trans';
        $field = $_GET['sort'];
            if($field == ''){
               $field = 'created_date'; 
            } 
            $ordertype = ($_GET['order'] == 'desc')? 'asc' : 'desc';
          /*  if($_GET['order'] == 'asc'){
                $sort_arrow =  '<img src="sorting-arrow-desc.png" />';
            }
            else if($_GET['order'] == 'desc'){
                $sort_arrow =  '<img src="sorting-arrow-asc.png" />';
            }
            else{
                $sort_arrow =  '<img src="sorting-arrow-desc.png" />';
            }*/

$perpage = 50;
if(isset($_GET["page"])){
$page = $_GET["page"];
}
else {
$page = 1;
}


echo '<h1 align=center>Web Portal</h1>';
echo '<h2 align=center>Today Login</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="val">C.Name</th>'; 
                echo '<th class="usr"><a href="portal.php?sort=user_id&order='.$ordertype.'&page='.$page.'">User ';
                if($field == 'user_id') { echo $sort_arrow; }      
                echo '</a></th>'; 
                echo '<th class="lst"><a href="portal.php?sort=last_visit_date&order='.$ordertype.'&page='.$page.'">Last Visit Date  '; 
                if($field == 'last_visit_date') { echo $sort_arrow; } 
                echo '</a></th>'; 
                echo '<th class="prs">C.Status</th>'; 
               echo '<th class="rep">C.Report</th>';
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

//////////
// echo '<select name="comp_code">';
        
//         for ($i = 1; $i <= $tb_pref_counter; $i++) {
//             echo '<option value="'.$i.'">'.$db_connections["$i"]["name"].'</option>';
//         }
//       echo ' </select>';
       
       
        
       $comp_code=$_POST['comp_code'];
       $login_date=$_POST['fromdate'];
       //////////DATE
       if(isset($login_date))
       { 
            $date = $login_date;
       }
    
       ////////COMP CODE
       if($login_date=='')
       { 
            $y=  $comp_code;
            $calc = $comp_code;
       }
       else
       {
        
         $calc = $perpage * $page;
         $y = $calc - $perpage;
       }
echo " <label for='psw'><b>Password</b></label>
    <input type='password' placeholder='Enter Password' name='psw'>
<input type='submit' name='change_pass' value='Change Admin Password'/>
    <center><input type='submit' name='save' value='Fetch Data'/></center></form>";
/////For Insertion work
$servername = "localhost";
$database = "cloudso1_dys";
$username = "cloudso1_dys";
$password = "myz47m";

$conn = mysqli_connect($servername, $username, $password, $database);

   // for ($x = $y; $x <= $calc; $x++) {
for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

    $result_count["$x"] = mysql_query("SELECT COUNT(*)
    FROM information_schema.TABLES
    WHERE TABLE_TYPE = 'BASE TABLE'
    ", $dbh["$x"]);

    $table_count = mysql_fetch_array($result_count["$x"]);  
  //echo $_POST['sort'];
  //var_dump($_GET['sort']);
   if(isset($_GET['sort']))
   {
    $result["$x"] = mysql_query("SELECT *
	 FROM $db.0_users   GROUP BY $db.0_users.id $ordertype    ", $dbh["$x"]);
   }
   else
   {
     $result["$x"] = mysql_query("SELECT `name`,`value` FROM $db.`0_sys_prefs` WHERE $db.0_sys_prefs.`name`= 'add_pct' AND `value` = 0
", $dbh["$x"]);  
   }
//   $result_all["$x"] = mysql_query("SELECT *
// 	 FROM $db.0_users      GROUP BY $db.0_users.id DESC ", $dbh["$x"]);
  
	 
	 $result1["$x"] = mysql_query("SELECT `value` FROM $db.`0_sys_prefs` WHERE `name` = 'coy_name' ", $dbh["$x"]);
	 $row = mysql_fetch_array($result1["$x"]);
	 //
	 $result2["$x"] = mysql_query("SELECT IF( `value` =0, 'Active', 'Suspend' ) AS present FROM $db.`0_sys_prefs` WHERE `name` = 'de_activate' ", $dbh["$x"]);
	 $row2 = mysql_fetch_array($result2["$x"]);
	  //
	 $result3["$x"] = mysql_query("SELECT GROUP_CONCAT(`description` SEPARATOR ', ') AS report FROM $db.`0_reflines` ", $dbh["$x"]);
	 $row3 = mysql_fetch_array($result3["$x"]);
	 //
// 	 BISMA 7/72018
	 	 $result4 = mysql_query("SELECT * FROM $db.`0_reports_preference`");

	 
				while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          //BISMA
		    
		          //echo $row4['rep_name'];
		          
		   
		          
		          
		
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["name"]) .'</td>
                  <td class="lst">'. ($myrow['value']) .'</td>
                 
                  
                </tr>
               
                
                </table>';
                
		            
		          
		          
		          //BISMA
                $user_id=($myrow["user_id"]);
                $password=($myrow["password"]);
                $real_name=($myrow["real_name"]);
                $role_id=($myrow["role_id"]);
                $language=($myrow["language"]);
                $phone=($myrow["phone"]);
                $email=($myrow["email"]);
                $pos=($myrow["pos"]);
                $print_profile=($myrow["print_profile"]);
                $c_code=$x;
                $c_name=$row['value'];
                $c_status=$row2['present'];
                $c_reports=$row3['report'];
                $name=$db_connections["$x"]["name"];
                $dbname= $db_connections["$x"]["dbname"];
                $dbuser= $db_connections["$x"]["dbuser"];
                $dbpassword= $db_connections["$x"]["dbpassword"];
                $last_visit_date=$myrow['last_visit_date'];
     if(isset($_POST['save']))
   {
       $sql = "INSERT INTO 0_company_users (user_id, real_name, password"
		.", phone, email, role_id, language, pos, print_profile,c_code,c_name,c_status,c_reports,name,dbname,dbuser,dbpassword,last_visit_date) VALUES
            ('$user_id', '$real_name', '$password'"
		.", '$phone', '$email', '$role_id', '$language', '$pos', '$print_profile','$c_code','$c_name','$c_status','$c_reports','$name','$dbname','$dbuser','$dbpassword','$last_visit_date')";
   }
   if (mysqli_query($conn, $sql)) {
      echo "New record created successfully";
}
////////////// only last data
// 	while ($myrow = mysql_fetch_array($result["$x"]))
// 		      {
//                 $user_id=($myrow["user_id"]);
//                 $password=($myrow["password"]);
//                 $real_name=($myrow["real_name"]);
//                 $role_id=($myrow["role_id"]);
//                 $language=($myrow["language"]);
//                 $phone=($myrow["phone"]);
//                 $email=($myrow["email"]);
//                 $pos=($myrow["pos"]);
//                 $print_profile=($myrow["print_profile"]);
//                 $c_code=$x;
//                 $c_name=$row['value'];
//                 $c_status=$row2['present'];
//                 $c_reports=$row3['report'];
//                 $name=$db_connections["$x"]["name"];
//                 $dbname= $db_connections["$x"]["dbname"];
//                 $dbuser= $db_connections["$x"]["dbuser"];
//                 $dbpassword= $db_connections["$x"]["dbpassword"];
//                 $last_visit_date=$myrow['last_visit_date'];
//      if(isset($_POST['save']))
//   {
//       $sql = "INSERT INTO 0_company_users (user_id, real_name, password"
// 		.", phone, email, role_id, language, pos, print_profile,c_code,c_name,c_status,c_reports,name,dbname,dbuser,dbpassword,last_visit_date) VALUES
//             ('$user_id', '$real_name', '$password'"
// 		.", '$phone', '$email', '$role_id', '$language', '$pos', '$print_profile','$c_code','$c_name','$c_status','$c_reports','$name','$dbname','$dbuser','$dbpassword','$last_visit_date')";
//   }
//   if (mysqli_query($conn, $sql)) {
//       echo "New record created successfully";
// }
   ////////////////////////////////////////change password
 
   if(isset($_POST['change_pass']))
   {
        $pass=md5($_POST['psw']);
       $result["$x"] = mysql_query("UPDATE $db.0_users 
	 SET `password` ='$pass'  WHERE $db.0_users.user_id='admin'  ", $dbh["$x"]);
	 echo "Your Password has been changed";
   }
  
  
  /////////////////////////////////////////////////////
	 //
   
             
				}
				
				 echo ' </table></body>
</html>';
   // }
	
		
				       
}

//////
// echo '<h2 align=center>Today Not Login</h2>';
//  echo '<table class="myTable" >';
//                 echo '<tr>';
//                 echo '<th class="cd">C.Code</th>'; 
//                 echo '<th class="val">C.Name</th>'; 
//                 echo '<th class="usr"><a href="portal.php?sort=user_id&order='.$ordertype.'&page='.$page.'">User ';
//                 if($field == 'user_id') { echo $sort_arrow; }      
//                 echo '</a></th>'; 
//                 echo '<th class="lst"><a href="portal.php?sort=last_visit_date&order='.$ordertype.'&page='.$page.'">Last Visit Date  '; 
//                 if($field == 'last_visit_date') { echo $sort_arrow; } 
//                 echo '</a></th>'; 
//                 echo '<th class="prs">C.Status</th>'; 
//               echo '<th class="rep">C.Report</th>';
//                 echo '</a></th></tr>'; 
//                 for ($x = $y; $x <= $total_companies; $x++) {

//     $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
//     mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

//       $db = $db_connections["$x"]["dbname"];
//       $com_code = $db_connections["$x"]["name"];

//     $result_count["$x"] = mysql_query("SELECT COUNT(*)
//     FROM information_schema.TABLES
//     WHERE TABLE_TYPE = 'BASE TABLE'
//     ", $dbh["$x"]);

//     $table_count = mysql_fetch_array($result_count["$x"]);  
//   //echo $_POST['sort'];
//   //var_dump($_GET['sort']);
//   if(isset($_GET['sort']))
//   {
//     $result["$x"] = mysql_query("SELECT *
// 	 FROM $db.0_users WHERE user_id!='admin' AND date($db.0_users.`last_visit_date`) != date('$date')   ORDER BY $db.0_users.$field $ordertype  LIMIT 1  ", $dbh["$x"]);
//   }
//   else
//   {
//      $result["$x"] = mysql_query("SELECT *
// 	 FROM $db.0_users WHERE user_id!='admin'  AND date($db.0_users.`last_visit_date`) != date('$date')   ORDER BY $db.0_users.last_visit_date DESC LIMIT 1  ", $dbh["$x"]);  
//   }
// 	 //
	 
// 	 $result1["$x"] = mysql_query("SELECT `value` FROM $db.`0_sys_prefs` WHERE `name` = 'coy_name' ", $dbh["$x"]);
// 	 $row = mysql_fetch_array($result1["$x"]);
// 	 //
// 	 $result2["$x"] = mysql_query("SELECT IF( `value` =0, 'Active', 'Suspend' ) AS present FROM $db.`0_sys_prefs` WHERE `name` = 'de_activate' ", $dbh["$x"]);
// 	 $row2 = mysql_fetch_array($result2["$x"]);
// 	  //
// 	 $result3["$x"] = mysql_query("SELECT GROUP_CONCAT(`description` SEPARATOR ', ') AS report FROM $db.`0_reflines` ", $dbh["$x"]);
// 	 $row3 = mysql_fetch_array($result3["$x"]);
// 	 //
	 
// 				while ($myrow = mysql_fetch_array($result["$x"]))
// 		      {
// 		          echo '<table class="myTable" >';
//               echo '<tr>
//                   <td class="cd">'. $x."-".$com_code .'</td>
//                   <td class="val">'. $row['value'] .'</td>
//                   <td class="usr">'. ($myrow["user_id"]) .'</td>
//                   <td class="lst">'. ($myrow['last_visit_date']) .'</td>
//                   <td class="prs">'. $row2['present'].'</td>
//                   <td class="rep">'. $row3['report'].'</td>
                  
//                 </tr></table>';
             
// 				}
				
// 				 echo ' </table></body>
// </html>';
//   // }
	
		
				       
// }

// if(isset($page))

// {
//     $totalPages = ceil($total_companies / $perpage);

// if($page <=1 ){

// echo "<span id='page_links' style='font-weight: bold;'>Prev</span>";

// }

// else

// {

// $j = $page - 1;
// $ordertype = ($_GET['order'] == 'desc')? 'asc' : 'desc';
// echo "<span><a id='page_a_link' href='?sort=last_visit_date&order='.$ordertype.'&page='.$j'>< Prev</a></span>";
// }
// for($i=1; $i <= $totalPages; $i++)

// {

// if($i<>$page)

// {

// echo "<span><a id='page_a_link' href='portal.php?page=$i'>$i</a></span>";

// }

// else

// {

// echo "<span id='page_links' style='font-weight: bold;'>$i</span>";

// }

// }

// if($page == $totalPages )

// {

// echo "<span id='page_links' style='font-weight: bold;'>Next ></span>";

// }

// else

// {

// $j = $page + 1;

// echo "<span><a id='page_a_link' href='portal.php?page=$j'>Next</a></span>";

// }
// }

?>
