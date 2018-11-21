<?php

/*function add_sales_group($description)
{
	$sql = "INSERT INTO ".TB_PREF."title (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales group could not be added");
}

function update_sales_group($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."title SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales group could not be updated");
}

function delete_sales_group($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales group");
}

function get_sales_groups($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."title";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get title");
}

function get_sales_group($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get group");
	return db_fetch($result);
}

function get_sales_group_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
 
function add_sales_area($description)
{
	$sql = "INSERT INTO ".TB_PREF."areas (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales area could not be added");
}

function update_sales_area($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."areas SET description=".db_escape($description)." WHERE area_code = ".db_escape($selected_id);
   	db_query($sql,"The sales area could not be updated");
}

function delete_sales_area($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function get_sales_areas($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."areas";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get areas");
}

function get_sales_area($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get area");
	return db_fetch($result);
}

function get_area_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function add_salesman($salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $provision, $break_pt, $provision2)
{
	$sql = "INSERT INTO ".TB_PREF."salesman (salesman_name, salesman_phone, salesman_fax, salesman_email,
		provision, break_pt, provision2)
		VALUES (".db_escape($salesman_name) . ", "
		  .db_escape($salesman_phone) . ", "
		  .db_escape($salesman_fax) . ", "
		  .db_escape($salesman_email) . ", ".
		$provision.", ".$break_pt.", "
		.$provision2.")";
   	db_query($sql,"The insert of the sales person failed");
}

function update_salesman($selected_id, $salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $provision, $break_pt, $provision2)
{
	$sql = "UPDATE ".TB_PREF."salesman SET salesman_name=".db_escape($salesman_name) . ",
		salesman_phone=".db_escape($salesman_phone) . ",
		salesman_fax=".db_escape($salesman_fax) . ",
		salesman_email=".db_escape($salesman_email) . ",
		provision=".$provision.",
		break_pt=".$break_pt.",
		provision2=".$provision2."
		WHERE salesman_code = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}

function delete_salesman($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);
	db_query($sql,"The sales-person could not be deleted");
}

function get_salesmen($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."salesman";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_salesman($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_salesman_name($id)
{
	$sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_phone($id)
{

$sql = "SELECT phone FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}


function get_fax($id)
{

$sql = "SELECT fax FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'
AND 	".TB_PREF."crm_contacts.entity_id = ".db_escape($id)
;

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_mobile($id)
{
$sql = "SELECT phone2 FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_email($id)
{
$sql = "SELECT email FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_shipper_name($id)
{
	$sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

//------------------------------------------------------------
function get_sales_agents($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."sale_agent";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY id";
	return db_query($sql,"could not get sale_agent");
}

function add_sales_agent($name)
{
	$sql = "INSERT INTO ".TB_PREF."sale_agent (name) VALUES (".db_escape($name) . ")";
   	db_query($sql,"The sales agent could not be added");
}


function update_sales_agent($selected_id, $name)
{
	$sql = "UPDATE ".TB_PREF."sale_agent SET name=".db_escape($name)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales agent could not be updated");
}

function delete_sales_agent($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."sale_agent WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales agent");
}



function get_sales_agent($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."sale_agent WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get agent");
	return db_fetch($result);
}

function get_sales_agent_name($agent_no)
{
	$sql = "SELECT name FROM ".TB_PREF."sale_agent WHERE id = ".db_escape($agent_no);
	$result = db_query($sql, "could not get agent");
	$row = db_fetch($result);
	return $row[0];
}
*/

function add_emp_grade($description)
{
	$sql = "INSERT INTO ".TB_PREF."grade (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales group could not be added");
}

function update_emp_grade($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."grade SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales group could not be updated");
	}
	
	function delete_emp_grade($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."grade WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales group");
}

function get_emp_grades($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."grade";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get title");
}

function get_emp_grade($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."grade WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get group");
	return db_fetch($result);
}

function get_emp_tile_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}

/////////----------------------------------------------
//Leave types

function 	add_emp_leave_type_new($description, $leave_days, $emp_grade)
{
	$sql = "INSERT INTO ".TB_PREF."leave_type (description,leave_days,emp_grade) 
	          VALUES (".db_escape($description) . ",".db_escape($leave_days) . ",".db_escape($emp_grade).")";
	db_query($sql, "The department could not be added");
}
function update_emp_leave_type_new($selected_id, $description,$leave_days,$emp_grade)
{
	$sql = "UPDATE ".TB_PREF."leave_type SET description=".db_escape($description).",
	leave_days=".db_escape($leave_days)." , emp_grade=".db_escape($emp_grade)." WHERE id = ".db_escape($selected_id);
	db_query($sql,"The department could not be updated");
}
function get_emp_leave_type_new($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."leave_type WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}
function get_grande_name($selected_id)
{
	$sql = "SELECT description  FROM `0_grade` WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	$ft = db_fetch($result);
	return $ft[0];
}
function get_emp_leave_types_new($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."leave_type";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get department");
}

function delete_emp_leave_type_new($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."leave_type WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

/////////----------------------------------------------
//Attendance Policy

// function add_att_policy($emp_grade, $chkeck,$chkh,$checkout,$chkhout, $deduction_value, $deduction_applicable, $duty_h_aplicable)
// {
// 	$sql = "INSERT INTO ".TB_PREF."attendance_policy (grade,deduction_start_time,deduction_end_time,deduction_value	,deduction_applicable , duty_h_aplicable) 
// 	          VALUES (".db_escape($emp_grade) . "," . db_escape($chkeck . ":" . $chkh) . "," . db_escape($checkout . ":" . $chkhout) . "
// 	          ,".db_escape($deduction_value).",".db_escape($deduction_applicable).",".db_escape($duty_h_aplicable).")";
// 	db_query($sql, "The department could not be added");
// }

function add_att_policy($emp_grade, $chkeck,$chkh,$checkout,$chkhout, $deduction_value, $deduction_applicable, $duty_h_aplicable, $deduction_value_days)
{
	$sql = "INSERT INTO ".TB_PREF."attendance_policy(grade,deduction_start_time,deduction_end_time,deduction_value	,deduction_value_days,deduction_applicable , duty_h_aplicable) 
	          VALUES (".db_escape($emp_grade) . "," . db_escape($chkeck . ":" . $chkh) . "," . db_escape($checkout . ":" . $chkhout) . "
	          ,".db_escape($deduction_value).",".db_escape($deduction_applicable).",".db_escape($duty_h_aplicable).",".db_escape($deduction_value_days).")";
	db_query($sql, "The department could not be added");
}



// function update_attendance_policy($selected_id,$emp_grade, $office_start, $deduction1, $deduction2, $office_end,$mday,$tday,$wday,$thday,$fday,$sday,$suday)
// {
// 	$sql = "UPDATE ".TB_PREF."attendance_policy SET
	 
// 	grade=".db_escape($emp_grade).",
// 	office_start=".db_escape($office_start)." , 
// 	deduction1=".db_escape($deduction1)." ,
// 	deduction2=".db_escape($deduction2)." ,
// 	office_end=".db_escape($office_end)." ,
// 	mday=".db_escape($mday)." ,
// 	tday=".db_escape($tday)." ,
// 	wday=".db_escape($wday)." ,
// 	thday=".db_escape($thday)." ,
// 	fday=".db_escape($fday)." ,
// 	sday=".db_escape($sday)." ,
// 	suday=".db_escape($suday)." 
	
// 	WHERE id = ".db_escape($selected_id);
// 	db_query($sql,"The department could not be updated");
// }
function update_attendance_policy($selected_id,$emp_grade, $chkeck,$chkh,$checkout,$chkhout, $deduction_value, $deduction_applicable, $duty_h_aplicable, $deduction_value_days)
{
	$sql = "UPDATE ".TB_PREF."attendance_policy SET
	 
	grade=".db_escape($emp_grade).",
	deduction_start_time=".db_escape($chkeck . ":" . $chkh)." , 
	deduction_end_time=". db_escape($checkout . ":" . $chkhout)." ,
	deduction_value=".db_escape($deduction_value)." ,
	deduction_value_days=".db_escape($deduction_applicable)." ,
	duty_h_aplicable=".db_escape($duty_h_aplicable)." ,
	duty_h_aplicable=".db_escape($deduction_value_days)." 
	
	WHERE id = ".db_escape($selected_id);
	db_query($sql,"The department could not be updated");
}
function get_attendance_policy($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."attendance_policy";
	if (!$show_inactive) $sql .= " WHERE !inactive";

	return db_query($sql,"could not get department");
}
function get_att_policy($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."attendance_policy WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}
function delete_att_policy($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."attendance_policy WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

/////////----------------------------------------------
//Daily Attendanec

function add_daily_attendance($employee, $check_in, $check_out,$fiscal_year,$division,$project,$location)
{
	$today_date = Today();
	
	$sql = "INSERT INTO ".TB_PREF."daily_attendance (att_date,employee,check_in,check_out,fiscal_year,division,project,location) 
	          VALUES (".db_escape(sql2date($today_date)) . ",
	          ".db_escape($employee) . ",
	          ".db_escape($check_in)."
	          ,".db_escape($check_out).",
	          ".db_escape($fiscal_year)." ,".db_escape($division).",".db_escape($project).",".db_escape($location).")";
	db_query($sql, "The department could not be added");
}
function update_daily_attendance($selected_id,$employee, $check_in, $check_out,$fiscal_year,$division,$project,$location)
{
	$sql = " UPDATE ".TB_PREF."daily_attendance SET
	 
	employee=".db_escape($employee).",
	check_in=".db_escape($check_in)." , 
	check_out=".db_escape($check_out)." ,
	fiscal_year=".db_escape($fiscal_year)." ,
	division=".db_escape($division)." ,
	project=".db_escape($project)." ,
	location=".db_escape($location)." 
	WHERE id = ".db_escape($selected_id);
	db_query($sql,"The department could not be updated");
}
function get_daily_att($emp_id,$date)
{

	$sql = "SELECT * FROM ".TB_PREF."daily_attendance where att_date=".db_escape(($date))."  AND employee=".db_escape($emp_id)."";
	return db_query($sql,"could not get department");
}
function get_employee_name_for_att($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);
	$result = db_query($sql, "could not get supplier");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_all_data_($emp_id,$date)
{
	$sql = "SELECT * FROM ".TB_PREF."daily_attendance WHERE employee=".db_escape($emp_id)." AND att_date=".db_escape(($date))."";

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}


//iqra
function add_emp_leave_type_policy($description, $leave_days, $emp_grade,$encash,$f_years, $division,$location, $project,$maxl)
{
    $sql = "INSERT INTO ".TB_PREF."leave_type (description,leave_days,emp_grade,encash,no_of_f_years,project,location,division,max_accum_leaves) 
	          VALUES (".db_escape($description) . ",".db_escape($leave_days) . ",".db_escape($emp_grade)."
	          
	          ,".db_escape($encash)."
	          ,".db_escape($f_years)."
	          ,".db_escape($division)."
	          ,".db_escape($location)."
	          ,".db_escape($project)."
	           ,".db_escape($maxl)."
	                                            
	          
	          )";
    db_query($sql, "The department could not be added");
}
function update_emp_leave_type_policy($selected_id, $description, $leave_days, $emp_grade,$encash,$f_years, $division,$location, $project,$maxl)
{
    $sql = "UPDATE ".TB_PREF."leave_type SET description=".db_escape($description).",
	leave_days=".db_escape($leave_days)." , emp_grade=".db_escape($emp_grade)." 
	 , encash=".db_escape($encash)." 
	  , no_of_f_years=".db_escape($f_years)." 
	   , division=".db_escape($division)." 
	    , location=".db_escape($location)."   
	    , project=".db_escape($project)." 
	    , max_accum_leaves=".db_escape($maxl)." 
	
	
	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The department could not be updated");
}

?>