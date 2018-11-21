<?php


/*function add_attendance($present, $over_time, $absent, $employee_id, $emp_dep,$month_id,$date)
{
   
		$sql = "INSERT INTO  ".TB_PREF."presence (`employee_id` ,
		 `present` ,  `over_time`, `absent` , `emp_dept`, `month_id` ,date)
		VALUES ( ".db_escape($employee_id).", ".db_escape($present).",  ".db_escape($over_time).",  ".db_escape($absent).", ".db_escape($emp_dep).",".db_escape($month_id).", ".db_escape(date2sql($date)).");";
		db_query($sql, "could not get customer");
	
}*/
function add_attendance_new($emp_name,$employee_id,$emp_dep,$month_id,$date,
                            $f_year,$man_month_value,$project_name,$division,$location)
{   $sql = "SELECT MAX(id) FROM ".TB_PREF."man_month";
	$result = db_query($sql,"could not get max apartment");
	$myrow = db_fetch($result);
	$max_id = $myrow[0];
	$next_id = $myrow[0] + 1;
   
		$sql = "INSERT INTO ".TB_PREF."man_month (id,employee_name,`employee_id` ,
		`emp_dept`,`month_id`,date,f_year, `man_month_value`,`project_name`,division,location,man_month)
		VALUES ( ".db_escape($next_id).",".db_escape($emp_name).",".db_escape($employee_id).",
		 ".db_escape($emp_dep).",".db_escape($month_id).",
         ".db_escape(date2sql($date)).",
          ".db_escape($f_year).",
          ".db_escape($man_month_value).",
          ".db_escape($project_name).",
          ".db_escape($division).",
          ".db_escape($location).",1
         
         )";
         
             add_audit_trail_payroll(ST_MAN_MONTH, $_POST['id'], Today(), _("Inserted."));

		db_query($sql, "could not get customer");
	
}

function update_attendance_new($employee_id,$employee_dept, $monthid,
                    $date,$f_year, $man_month_value,$project,$division,$location)
{
	$sql = "Update ".TB_PREF."man_month 
	SET 
		  `man_month_value`  =   ".db_escape($man_month_value)."
	WHERE
	employee_id=$employee_id 
	AND month_id =".db_escape($monthid)." AND project_name=".db_escape($project)."
	 AND division=".db_escape($division)." AND location=".db_escape($location)."";
    add_audit_trail_payroll(ST_MAN_MONTH, $_POST['id'], Today(), _("Updated."));

		db_query($sql, "could not get customer");

}

function get_employees_acc_dept_new($dept)
{
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE project=".db_escape($dept);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}

function check_month_duplication_new($emp_dept, $month)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."man_month WHERE emp_dept=".db_escape($emp_dept)." 
	AND month_id=".db_escape($month);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}

function get_employee_that_exist_update_new($project,$division, $location,$month,$f_year)
{
	$sql = " SELECT * 
	FROM 
	".TB_PREF."man_month
    WHERE 
    project_name=".db_escape($project)."
    AND 
    division=".db_escape($division)."
    AND 
    location=".db_escape($location)."
    AND 
    month_id=".db_escape($month)." 
    AND f_year=".db_escape($f_year)."
    
   " ;
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}

function get_employee_name_man_month($employee_id)
{
	
	$sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function employee_attendance_detail($emp_id, $month,$f_year)
{
	
	$sql = "SELECT ".TB_PREF."man_month.* FROM ".TB_PREF."man_month 
	WHERE employee_id=".db_escape($emp_id)." AND
	month_id=".db_escape($month)."
    AND f_year=".db_escape($f_year);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow;
}

function get_employee_existence($emp_dept)
{
	
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE 
	emp_dept=".db_escape($emp_dept);
	$result = db_query($sql, "could not get customer");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function get_manmont_pre_month($month,$f_year,$employee_id,$division,$location,$project)
{
	$sql = "SELECT man_month_value FROM ".TB_PREF."man_month WHERE 
	month_id=$month AND f_year=".db_escape($f_year)."
	 AND employee_id=".db_escape($employee_id)." AND division=".db_escape($division)." AND location=".db_escape($location)." 
	 AND project_name=".db_escape($project)."  ";

	$result = db_query($sql, "could not get customer");
	$myrow = db_fetch_row($result);
	return $myrow['0'];
}

function get_emp_rec_no($emp_dept, $month, $f_year)
{
	
	$sql = "SELECT COUNT(emp_id) FROM ".TB_PREF."payroll WHERE 
	dept_id =".db_escape($emp_dept)."
	AND
	month=".db_escape($month)." AND
	f_year=".db_escape($f_year);

	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_attendance_count_new($project,$division ,$location,$month, $f_year)
{

	$sql = "SELECT COUNT(id) FROM ".TB_PREF."man_month WHERE 
	
	project_name=".db_escape($project)."
	AND
	division=".db_escape($division)."
	AND
	location=".db_escape($location)."
	AND
	month_id=".db_escape($month)."
    AND
	f_year=".db_escape($f_year);

	$result = db_query($sql, "Could not get attendance count.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_through_dept_id_new($aa, $division,$location,$project,$f_year)
{
	/*$sql = "SELECT * FROM ".TB_PREF."employee WHERE division = ".db_escape($division)."
            AND ".TB_PREF."employee.inactive = '0' AND  project = ".db_escape($project)."   ";

	if($location !=0)
		$sql .=" AND location=".db_escape($location)."";*/

	$sql="SELECT `employee_id` FROM `0_man_month` 
              WHERE `division`= ".db_escape($division)." 
               AND `project_name`=  ".db_escape($project)."
                AND `f_year`=  ".db_escape($f_year)."
               AND employee_name !=''  AND location=".db_escape($location)." 
               GROUP BY employee_id";

	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}
function check_entry($division,$location,$project,$f_year,$month)
{
	/*$sql = "SELECT * FROM ".TB_PREF."employee WHERE division = ".db_escape($division)."
            AND ".TB_PREF."employee.inactive = '0' AND  project = ".db_escape($project)."   ";

	if($location !=0)
		$sql .=" AND location=".db_escape($location)."";*/

	$sql="SELECT `man_month` FROM `0_man_month` 
              WHERE `division`= ".db_escape($division)." 
               AND `project_name`=  ".db_escape($project)."
                AND `f_year`=  ".db_escape($f_year)."
                AND 	month_id= $month
               AND employee_name ='' 
               AND  location=".db_escape($location)."";

	$myrow = db_query($sql, "could not get customer");
	 $ft = db_fetch($myrow);
	return $ft[0];
}
function get_info_employee_new($employee_name,$monthid, $f_year)
{
    $sql = "SELECT SUM(man_month_value) as value FROM ".TB_PREF."man_month 
            WHERE employee_id = ".db_escape($employee_name)."
            AND month = ".db_escape($monthid);
    $query = db_query($sql, "Error");
    $fetch = db_fetch($query);
    return $fetch['value']*30;
}
function delete_data($employee_id, $monthid,$f_year, $project, $division, $location)
{
	$sql = "DELETE FROM ".TB_PREF."man_month where employee_id=".db_escape($employee_id)."
	 		AND month_id=".db_escape($monthid)." AND f_year=".db_escape($f_year)." 
	 		AND project_name=".db_escape($project)." AND division=".db_escape($division)."
	 		 AND location=".db_escape($location)."	";
    add_audit_trail_payroll(ST_MAN_MONTH, $_POST['id'], Today(), _("Deleted."));

	db_query($sql, "could not Delete");

}

?>