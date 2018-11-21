<?php
$page_security = 'SS_PAYROLL';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");


$dept  = $_REQUEST['q'];
$sql =  "SELECT 
		".TB_PREF."month.*
		FROM 
		".TB_PREF."month		
		";
		
	$result = db_query($sql,"Could't get month");	
	echo "<tr><td><select style='border: 1px solid #dfe1e2; box-shadow: 1px 0px 1px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7   inset; margin: 1px 0 2px; padding: 6px 4px; font-size: 12px;'
	 name='job_list' id='job' onchange='showUser2(this.value);'>
      <option selected>Select job</option>";
	while ($myrow = db_fetch($result)) 
	{
		echo "<option value=".$myrow['id'].",".$dept.">".$myrow['description']."</option>";
	}
	
echo "</select>
 <div id='txtHint'></div>
</td></tr>";




?>
