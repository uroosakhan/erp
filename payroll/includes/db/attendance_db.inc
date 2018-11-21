<?php

function delete_attendances($month_id,$fiscal_year,$division,$project,$location)
{
	$sql = " DELETE FROM ".TB_PREF."emp_attendance WHERE id!=0 ";
	
	 
	  if($division !=0)
            $sql .=" AND division=".db_escape($division)."";
            if($project !=0)
            $sql .=" AND project=".db_escape($project)."";
            if($location !=0)
            $sql .=" AND location=".db_escape($location)."";
            if($month_id !=0)
            $sql .=" AND month_id=".db_escape($month_id)."";
      if($fiscal_year !=0)
            $sql .=" AND fiscal_year=".db_escape($fiscal_year)."";


	db_query($sql,"This division data has been deleted ");
}
/*function add_attendance($present, $over_time, $absent, $employee_id, $emp_dep,$month_id,$date)
{
   
		$sql = "INSERT INTO  ".TB_PREF."presence (`employee_id` ,
		 `present` ,  `over_time`, `absent` , `emp_dept`, `month_id` ,date)
		VALUES ( ".db_escape($employee_id).", ".db_escape($present).",  ".db_escape($over_time).",  ".db_escape($absent).", ".db_escape($emp_dep).",".db_escape($month_id).", ".db_escape(date2sql($date)).");";
		db_query($sql, "could not get customer");
	
}*/
function add_attendance($present, $over_time, $absent,$employee_id,$emp_dep,$month_id,$date,$f_year,$divison,$location)
{   $sql = "SELECT MAX(id) FROM ".TB_PREF."presence";
	$result = db_query($sql,"could not get max apartment");
	$myrow = db_fetch($result);
	$max_id = $myrow[0];
	$next_id = $myrow[0] + 1;
   
		$sql = "INSERT INTO ".TB_PREF."presence (id,`employee_id` ,
		 `present`,`over_time`,`absent`,`emp_dept`,`month_id`,date,f_year,divison,location)
		VALUES ( ".db_escape($next_id).",".db_escape($employee_id).", ".db_escape($present).",  ".db_escape($over_time).",  ".db_escape($absent).", ".db_escape($emp_dep).",".db_escape($month_id).",
         ".db_escape(date2sql($date)).", ".db_escape($f_year).", ".db_escape($divison).", ".db_escape($location).");";
		db_query($sql, "could not get customer");
	
}


function update_attendance($present, $overtime, $absent, $employee_id, $monthid,$date,$f_year,$division,$selected_id,$location)
{
	$sql = "UPDATE ".TB_PREF."presence 
	SET   `present` =  ".db_escape($present).",
		  `over_time` =  ".db_escape($overtime).",
		  `absent`  =   ".db_escape($absent).",
		  `date`  =   ".db_escape(sql2date($date)).",
		  `location`  =   ".db_escape(($location))."
    
	WHERE
	employee_id=".db_escape($employee_id)." AND month_id =".db_escape($monthid)." AND divison  = ".db_escape($division)."
	AND emp_dept=".db_escape($selected_id)."";
			   
		db_query($sql, "could not get customer");

}




function get_employees_acc_dept($dept)
{
	$sql = "SELECT COUNT(employee_id) FROM ".TB_PREF."employee WHERE emp_dept=".db_escape($dept);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function check_month_duplication($emp_dept, $month)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."presence WHERE emp_dept=".db_escape($emp_dept)." AND month_id=".db_escape($month);
	$result = db_query($sql, "could not get customer");
	$myrow =  db_fetch($result);
	return $myrow[0];
}


function get_employee_that_exist_update($dept_id,$division, $month,$f_year,$location)
{
	$sql = " SELECT * 
	FROM 
	".TB_PREF."presence
    WHERE 
    ".TB_PREF."presence.emp_dept=".db_escape($dept_id)."
    AND ".TB_PREF."presence.divison = ".db_escape($division)."
    AND ".TB_PREF."presence.month_id = ".db_escape($month)."
     AND ".TB_PREF."presence.f_year = ".db_escape($f_year)."
     AND ".TB_PREF."presence.location =".db_escape($location)."
     " ;
	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}



function get_employee_name($employee_id)
{
	
	$sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow['0'];
}


function employee_attendance_detail($emp_id, $month,$f_year)
{
	
	$sql = "SELECT ".TB_PREF."presence.* FROM ".TB_PREF."presence 
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


function get_emp_rec_no($emp_dept, $month,$division,$f_year)
{
	
	$sql = "SELECT COUNT(emp_id) FROM ".TB_PREF."payroll WHERE 
	dept_id =".db_escape($emp_dept)."
	AND
	month=".db_escape($month)." 
	AND
	divison=".db_escape($division)." 
	AND f_year=".db_escape($f_year);
	
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_attendance_count($emp_dept, $month,$f_year)
{
	
	$sql = "SELECT COUNT(emp_dept) FROM ".TB_PREF."presence WHERE 
	emp_dept=".db_escape($emp_dept)."
	AND
	month_id=".db_escape($month)."
    AND
	f_year=".db_escape($f_year);
	
	$result = db_query($sql, "Could not get attendance count.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function get_emp_attendance_count_new($month,$f_year)
{

    $sql = "SELECT COUNT(month_id) FROM ".TB_PREF."emp_attendance WHERE 
	month_id=".db_escape($month)."
    AND
	fiscal_year=".db_escape($f_year);
    $result = db_query($sql, "Could not get attendance count.");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_emp_through_dept_id($aa,$division,$location,$project,$f_year)
{

    /*	$sql = "SELECT * FROM ".TB_PREF."employee WHERE project=".db_escape($project).
            " AND 	division=".db_escape($divsion)." AND ".TB_PREF."employee.inactive = '0'
              AND location=".db_escape($location)." " ;
        $myrow = db_query($sql, "could not get customer");
        return $myrow;*/
    /*
        $sql = "SELECT * FROM ".TB_PREF."employee WHERE division = ".db_escape($division)."
                AND ".TB_PREF."employee.inactive = '0'   ";

        if($location !=0)
            $sql .=" AND location=".db_escape($location)."";

        if($project !=0)
            $sql .=" AND  project = ".db_escape($project)."  ";*/
    $sql="SELECT 0_man_month.* FROM `0_man_month` , 0_employee
              WHERE 0_man_month.division = ".db_escape($division)." 
               AND 0_man_month.project_name=  ".db_escape($project)."
                AND 0_man_month.f_year =  ".db_escape($f_year)."
               AND 0_man_month.employee_name !=''  AND 
               0_man_month.location=".db_escape($location)."
               AND  0_man_month.employee_id = 0_employee.employee_id AND 0_employee.inactive !=1 ";


    $myrow = db_query($sql, "could not get customer");
    return $myrow;
}
function get_emp_through_dept_id_new($aa,$division,$location,$project,$f_year)
{

/*	$sql = "SELECT * FROM ".TB_PREF."employee WHERE project=".db_escape($project).
        " AND 	division=".db_escape($divsion)." AND ".TB_PREF."employee.inactive = '0'
          AND location=".db_escape($location)." " ;
	$myrow = db_query($sql, "could not get customer");
	return $myrow;*/
/*
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE division = ".db_escape($division)."
            AND ".TB_PREF."employee.inactive = '0'   ";

	if($location !=0)
		$sql .=" AND location=".db_escape($location)."";

	if($project !=0)
		$sql .=" AND  project = ".db_escape($project)."  ";*/
	$sql="SELECT 0_employee.* FROM  0_employee
              WHERE 0_employee.division = ".db_escape($division)." 
               AND 0_employee.project=  ".db_escape($project)."
               AND 
               0_employee.location=".db_escape($location)."
               AND 0_employee.inactive !=1 ";


	$myrow = db_query($sql, "could not get customer");
	return $myrow;
}

function get_info_employee($employee_name,$monthid,$f_year)
{
	$working_days = get_sys_pay_pref('total_working_days');
    $sql = "SELECT SUM(man_month_value) as value FROM ".TB_PREF."man_month 
            WHERE employee_id = ".db_escape($employee_name)."
            AND month_id = $monthid ";
           $query = db_query($sql, "Error");
         $fetch = db_fetch($query);
        return $fetch['value']* $working_days;
}
//--iqra
function get_leave_id_new1($id)
{
    $sql = "SELECT id FROM ".TB_PREF."leave_type WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function add_attendance_neww($employee_id,$employee_dept, $monthid, $date, $f_year, $division,$location,$chkeck,$chkh,$checkout,$chkhout,$sick)
{
    $att_date= date2sql($date);
    $monthidd=employee_month_duplication($monthid,$f_year,$employee_id);
    $d = date("d",strtotime($att_date));

    if($sick!=0){
        add_emp_info_attendance($employee_id,$date,$date,$sick,1,'',1,$f_year);
    }

    if ($monthidd == 0) {

        $sql = "INSERT INTO  " . TB_PREF . "emp_attendance (empl_id,dept_id,month_id,att_date,fiscal_year,division,location," . $d . "_in," . $d . "_out," . $d . "_type)
		VALUES ( " . db_escape($employee_id) . "," . db_escape($employee_dept) . ", " . db_escape($monthid) . ", " . db_escape(date2sql($date)) . ",
		 " . db_escape($f_year) . ", " . db_escape($division) . "," . db_escape($location) . "," . db_escape($chkeck . ":" . $chkh) . ",
		 " . db_escape($checkout . ":" . $chkhout) . ", " . db_escape($sick) . ")";
        db_query($sql, "could not get customer");

    }
 else {
        $sql = "UPDATE " . TB_PREF . "emp_attendance
	SET
	att_date =" . db_escape($att_date) . ",
	" . $d . "_in =" . db_escape($chkeck . ":" . $chkh) . ",
	" . $d . "_out=" . db_escape($checkout . ":" . $chkhout) . ",
	" . $d . "_type=" . db_escape($sick) . "

	WHERE
	empl_id=" . db_escape($employee_id) . " AND month_id =" . db_escape($monthid);
        db_query($sql, "could not get customer");
    }

//}
//
//else {
//    display_error("123");
//
//}
}
function add_emp_info_attendance($emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
{
    if($leave_type==2)
    {
        $availed=A;
    }
    $sql = "INSERT INTO ".TB_PREF."leave (emp_id,from_date,to_date,leave_type,no_of_leave,reason,approve,f_year,availed) 
    VALUES ( ".db_escape($emp_id).", ".db_escape(sql2date($from_date)).", ".db_escape(sql2date($to_date)).",
     ".db_escape($leave_type).", ".db_escape($no_of_leave).", ".db_escape($reason).",
     ".db_escape(1) . ",".db_escape($f_year) . ",".db_escape($availed) . ")";
    db_query($sql,"The department could not be added");
}
function employee_month_duplication($month,$fiscal_year,$empl_id)
{

    $sql = "SELECT 	COUNT(month_id)  from ".TB_PREF."emp_attendance WHERE
	month_id=".db_escape($month)."
	AND fiscal_year=$fiscal_year AND empl_id=$empl_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}

//function update_attendance_neww($monthid,$employee_id, $date,$chkeck,$chkh,$checkout,$chkhout,$sick)
//{
//    $att_date= date2sql($date);
//    $sql = "UPDATE ".TB_PREF."kv_empl_attendancee
//	SET
//	`date` =  ".db_escape($att_date).",
//	`1_in` =  ".db_escape($chkeck.":".$chkh).",
//	`1_out` =  ".db_escape($checkout.":".$chkhout).",
//	`1_type` =  ".db_escape($sick)."
//
//
//
//	WHERE
//	empl_id=".db_escape($employee_id)." AND month_id =".db_escape($monthid)."";
//
//    db_query($sql, "could not get customer");
//
//}


//
//function get_employee_namess($employee_id)
//{
//    $sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);
//
//    $result = db_query($sql, "could not get supplier");
//
//    $row = db_fetch_row($result);
//
//    return $row[0];
//}

//function add_attendance_neww($employee_id,$employee_dept, $monthid, $date, $f_year, $division,$location,$chkeck,$chkh,$checkout,$chkhout,$sick)
//{
//
//    $sql = "SELECT MAX(id) FROM ".TB_PREF."kv_empl_attendancee";
//    $result = db_query($sql,"could not get max apartment");
//    $myrow = db_fetch($result);
//    $max_id = $myrow[0];
//    $next_id = $myrow[0] + 1;
//    if($employee_id!=17 && $employee_id!=1729 && $employee_id!=1757 && $employee_id!=880 && $employee_id!=1657 && $employee_id!=32 && $employee_id!=1535 && $employee_id!=180)
//    {
//        $sql = "INSERT INTO  ".TB_PREF."kv_empl_attendancee (empl_id,dept_id,month_id,att_date,fiscal_year,division,location,1_in,1_out,1_type)
//		VALUES ( ".db_escape($employee_id).",".db_escape($employee_dept).", ".db_escape($monthid).", ".db_escape(date2sql($date)).",
//		 ".db_escape($f_year).", ".db_escape($division).",".db_escape($location).",".db_escape($chkeck.":".$chkh).",".db_escape($checkout.":".$chkhout).", ".db_escape($sick).")";
//        db_query($sql, "could not get customer");
//
//    }
//    elseif($employee_id==17 && $employee_id==1729 && $employee_id==1757 && $employee_id==880 && $employee_id==1657 && $employee_id==32 && $employee_id==1535 && $employee_id==180)
//
//    {
//
//        $sql = "UPDATE ".TB_PREF."kv_empl_attendancee
//	SET   `1_in` =  ".db_escape($chkeck.":".$chkh)."
//
//
//	WHERE
//	empl_id=".db_escape($employee_id)." AND month_id =".db_escape($monthid)." AND division  = ".db_escape($division)."
//	";
//
//        db_query($sql, "could not get customer");
//    }
//}
?>