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
//    $total_companies = 22;
    
    $table_name = '0_debtor_trans';
        $field = $_GET['sort'];
            if($field == ''){
               $field = 'created_date'; 
            } 
            $ordertype = ($_GET['order'] == 'desc')? 'asc' : 'desc';
       
$perpage = 50;
if(isset($_GET["page"])){
$page = $_GET["page"];
}
else {
$page = 1;
}


echo '<h1 align=center>Allocations Inquiry</h1>';
echo '<h2 align=center>List of customer payments with wrong filled alloc columns in debtor trans table, and for which no entry is made in cust alloc table</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">tran_date</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">ov_amount</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];


	 $result["$x"] = mysql_query("SELECT $db.0_debtor_trans.`trans_no`,$db.0_debtor_trans.`tran_date`, $db.0_debtor_trans.`debtor_no`,  $db.0_debtors_master.`name`,  $db.0_debtor_trans.`reference`,$db.0_debtor_trans.`ov_amount`
	 FROM $db.`0_debtor_trans`,
    $db.`0_debtors_master`	 
		WHERE $db.0_debtor_trans.trans_no NOT IN (SELECT $db.0_cust_allocations.trans_no_from FROM $db.0_cust_allocations) AND 
		$db.0_debtor_trans.type IN (2, 11,12, 42) 
		AND $db.0_debtor_trans.ov_amount != 0 
		AND $db.0_debtor_trans.alloc != 0 
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
		ORDER BY $db.0_debtors_master.name
		
	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["trans_no"]) .'</td>
                  <td class="lst">'. ($myrow['tran_date']) .'</td>
                  <td class="lst">'. ($myrow['debtor_no']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['ov_amount']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
				       
}


echo '<h1 align=center>Allocations Inquiry</h1>';
echo '<h2 align=center>List of customer invoices with wrong filled alloc columns in debtor trans table, and for which no entry is made in cust alloc table</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">tran_date</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">ov_amount</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];


	 $result["$x"] = mysql_query("SELECT $db.0_debtor_trans.`trans_no`,$db.0_debtor_trans.`tran_date`, $db.0_debtor_trans.`debtor_no`,  $db.0_debtors_master.`name`,  $db.0_debtor_trans.`reference`,$db.0_debtor_trans.`ov_amount`
	 FROM $db.`0_debtor_trans`,
    $db.`0_debtors_master`	 
		WHERE $db.0_debtor_trans.trans_no NOT IN (SELECT $db.0_cust_allocations.trans_no_to FROM $db.0_cust_allocations) AND 
		$db.0_debtor_trans.type IN (10) 
		AND $db.0_debtor_trans.ov_amount != 0 
		AND $db.0_debtor_trans.alloc != 0 
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
		ORDER BY $db.0_debtors_master.name
		
	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["trans_no"]) .'</td>
                  <td class="lst">'. ($myrow['tran_date']) .'</td>
                  <td class="lst">'. ($myrow['debtor_no']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['ov_amount']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
				       
}



echo '<h2 align=center>list of customer receipt transactions in which debtor no and person id is not matching in debtor_trans and cust allocation table</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">cust allocation id</th>'; 
                echo '<th class="cd">trans_no_from</th>'; 
                echo '<th class="cd">person_id</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">date_alloc</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

	 $result["$x"] = mysql_query("SELECT `id`, `trans_no_from`,  `person_id`,$db.0_debtor_trans.debtor_no,`name`,`reference`, `date_alloc`
	 FROM $db.`0_debtor_trans`,
    $db.`0_cust_allocations`,
    $db.`0_debtors_master`	 
	 WHERE  $db.0_cust_allocations.trans_type_from IN (2,42,12)
     	AND $db.0_cust_allocations.person_id != $db.0_debtor_trans.debtor_no
    	AND $db.0_cust_allocations.trans_no_from = $db.0_debtor_trans.trans_no
    	AND $db.0_cust_allocations.trans_type_from = $db.0_debtor_trans.type   	
    	AND $db.0_debtor_trans.ov_amount != 0
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
    	

	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["id"]) .'</td>
                  <td class="lst">'. ($myrow['trans_no_from']) .'</td>
                  <td class="lst">'. ($myrow['person_id']) .'</td>
                  <td class="lst">'. ($myrow['debtor_no']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['date_alloc']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}


echo '<h2 align=center>list of customer invoice transactions in which debtor no and person id is not matching in debtor_trans and cust allocation table</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">cust allocation id</th>'; 
                echo '<th class="cd">trans_no_from</th>'; 
                echo '<th class="cd">trans_no_to</th>'; 
                echo '<th class="cd">person_id</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">date_alloc</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

	 $result["$x"] = mysql_query("SELECT `id`, `trans_no_from`, `trans_no_to`, `person_id`,$db.0_debtor_trans.debtor_no,`name`,`reference`, `date_alloc`
	 FROM $db.`0_debtor_trans`,
    $db.`0_cust_allocations`,
    $db.`0_debtors_master`	 
	 WHERE  $db.0_cust_allocations.trans_type_to IN (10)
     	AND $db.0_cust_allocations.person_id != $db.0_debtor_trans.debtor_no
    	AND $db.0_cust_allocations.trans_no_to = $db.0_debtor_trans.trans_no
    	AND $db.0_cust_allocations.trans_type_to = $db.0_debtor_trans.type   	
    	AND $db.0_debtor_trans.ov_amount != 0
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
    	

	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["id"]) .'</td>
                  <td class="lst">'. ($myrow['trans_no_from']) .'</td>
                  <td class="lst">'. ($myrow['trans_no_to']) .'</td>
                  <td class="lst">'. ($myrow['person_id']) .'</td>
                  <td class="lst">'. ($myrow['debtor_no']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['date_alloc']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}


echo '<h2 align=center>list of customer receipts not posted in bank trans table</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>';  
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">type</th>'; 
                echo '<th class="cd">tran date</th>'; 
                echo '<th class="cd">debtor_ref</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">ov_amount</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

	 $result["$x"] = mysql_query("SELECT `trans_no`, `type`, `tran_date`, $db.0_debtor_trans.debtor_no,`name`,$db.0_debtor_trans.debtor_no,`debtor_ref`,`reference`, `ov_amount`
	 FROM $db.`0_debtor_trans`,
    $db.`0_debtors_master`	 
	 WHERE  $db.0_debtor_trans.trans_no NOT IN (SELECT $db.0_bank_trans.trans_no FROM $db.0_bank_trans)
    	AND $db.0_debtor_trans.type IN (2,42,12)   	
    	AND $db.0_debtor_trans.ov_amount != 0
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
    	

	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["trans_no"]) .'</td>
                  <td class="usr">'. ($myrow["type"]) .'</td>
                  <td class="usr">'. ($myrow["tran_date"]) .'</td>
                  <td class="usr">'. ($myrow["debtor_no"]) .'</td>
                  <td class="lst">'. ($myrow['debtor_ref']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['ov_amount']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}


echo '<h2 align=center>list of cust allocations where trans_no_to is blank/zero</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>';  
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">type</th>'; 
                echo '<th class="cd">tran date</th>'; 
                echo '<th class="cd">debtor_ref</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">ov_amount</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

	 $result["$x"] = mysql_query("SELECT `trans_no`, `type`, `tran_date`, $db.0_debtor_trans.debtor_no,`name`,$db.0_debtor_trans.debtor_no,`debtor_ref`,`reference`, `ov_amount`
	 FROM $db.`0_debtor_trans`,
    $db.`0_debtors_master`,	
    $db.`0_cust_allocations`	 
    
	 WHERE  $db.0_debtor_trans.trans_no = $db.0_cust_allocations.trans_no_from
    	AND $db.0_debtor_trans.type IN (2,42,12)   	
    	AND $db.0_debtor_trans.ov_amount != 0
    	AND $db.0_cust_allocations.trans_no_to = 0
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
    	

	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["trans_no"]) .'</td>
                  <td class="usr">'. ($myrow["type"]) .'</td>
                  <td class="usr">'. ($myrow["tran_date"]) .'</td>
                  <td class="usr">'. ($myrow["debtor_no"]) .'</td>
                  <td class="lst">'. ($myrow['debtor_ref']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['ov_amount']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}


/*
echo '<h2 align=center>list of customer receipt for which the alloc amount increases from ov_amount</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">type</th>'; 
                echo '<th class="cd">tran_date</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">debtor_no</th>'; 
                echo '<th class="cd">name</th>'; 
                echo '<th class="cd">ov_amount</th>'; 
                echo '<th class="cd">alloc</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];


	 $result["$x"] = mysql_query("SELECT `trans_no`, `type`, `tran_date`,  `reference`,$db.0_debtor_trans.`debtor_no`,`name`, `ov_amount`, `alloc`
	 FROM $db.`0_debtor_trans`,
    $db.`0_debtors_master`	 
	 WHERE  $db.0_debtor_trans.ov_amount != 0
		AND $db.0_debtor_trans.debtor_no = $db.0_debtors_master.debtor_no 
		AND $db.0_debtor_trans.alloc > $db.0_debtor_trans.ov_amount 
    	AND  $db.0_debtor_trans.type = 12
    	
	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow['trans_no']) .'</td>
                  <td class="usr">'. ($myrow['type']) .'</td>
                  <td class="lst">'. ($myrow['tran_date']) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['debtor_no']) .'</td>
                  <td class="lst">'. ($myrow['name']) .'</td>
                  <td class="lst">'. ($myrow['ov_amount']) .'</td>
                  <td class="lst">'. ($myrow['alloc']) .'</td>
                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}
*/

/*
echo '<h2 align=center>list of transactions for which debtor_trans branch_code doest not matched with the cust_branch branch_code id</h2>';
 echo '<table class="myTable" >';
                echo '<tr>';
                echo '<th class="cd">C.Code</th>'; 
                echo '<th class="cd">trans_no</th>'; 
                echo '<th class="cd">tran_date</th>'; 
                echo '<th class="cd">reference</th>'; 
                echo '<th class="cd">cust_branch branch_code</th>'; 
                echo '<th class="cd">debtor_trans debtor_no</th>'; 
                echo '<th class="cd">debtor_trans branch_code</th>'; 
                echo '</a></th></tr>'; 
echo "<form method='post'>
";

for ($x = $y; $x <= $total_companies; $x++) {
    $dbh["$x"] = mysql_connect(localhost, $db_connections["$x"]["dbuser"], $db_connections["$x"]["dbpassword"]);  
    mysql_select_db($db_connections["$x"]["dbname"], $dbh["$x"]); 

      $db = $db_connections["$x"]["dbname"];
      $com_code = $db_connections["$x"]["name"];

	 $result["$x"] = mysql_query("SELECT `trans_no`, `tran_date`, `reference`,
	 $db.0_cust_branch.branch_code as custbr,
	 $db.0_debtor_trans.debtor_no as tr_debtor,$db.0_debtor_trans.branch_code tr_br
		FROM 
			$db.0_debtor_trans,
			$db.0_debtors_master,
			$db.0_cust_branch

		WHERE $db.0_debtors_master.debtor_no = $db.0_debtor_trans.debtor_no
		AND $db.0_debtors_master.debtor_no = $db.0_cust_branch.debtor_no 
		AND $db.0_cust_branch.branch_code != $db.0_debtor_trans.branch_code
		AND $db.0_debtor_trans.ov_amount != 0

		SORT BY tran_date DESC


	 ", $dbh["$x"]);

			while ($myrow = mysql_fetch_array($result["$x"]))
		      {    
		          
		          echo '<table class="myTable" >';
              echo '<tr>
                   <td class="cd">'. $x."-".$com_code .'</td>
                  <td class="usr">'. ($myrow["trans_no"]) .'</td>
                  <td class="usr">'. ($myrow["tran_date"]) .'</td>
                  <td class="lst">'. ($myrow['reference']) .'</td>
                  <td class="lst">'. ($myrow['custbr']) .'</td>
                  <td class="lst">'. ($myrow['tr_debtor']) .'</td>
                  <td class="lst">'. ($myrow['tr_br']) .'</td>

                 
                  
                </tr>
                </table>';
				}

				 echo ' </table></body>
</html>';
   // }
	
	
	
				       
}*/
?>
