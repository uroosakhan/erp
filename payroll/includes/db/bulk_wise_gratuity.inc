<?php

function get_emp_by_department($emp_dept,$id)
{

	$sql = "SELECT * FROM ".TB_PREF."employee where emp_dept=".db_escape($emp_dept)." ";
	
	if($id=='gratuity')
		$sql.="AND  applicable=1";

	if($id=='leave_encashment')
		$sql.="AND  leave_applicable=1";


	return db_query($sql, "Could not get attendance count.");

}


?>