<?php

//function add_emp_info($emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
//{
//	if($leave_type==2)
//	{
//		$availed=A;
//	}
//	$sql = "INSERT INTO ".TB_PREF."leave (emp_id,from_date,to_date,leave_type,no_of_leave,reason,approve,f_year,availed) 
  //  VALUES ( ".db_escape($emp_id).", ".db_escape(sql2date($from_date)).", ".db_escape(sql2date($to_date)).",
    // ".db_escape($leave_type).", ".db_escape($no_of_leave).", ".db_escape($reason).",
     //".db_escape(1) . ",".db_escape($f_year) . ",".db_escape($availed) . ")";
   //	db_query($sql,"The department could not be added");
//}

//iqra--
function get_emp_no_of_leaves($emp_id,$leave_type)
{

    $sql = "SELECT SUM(no_of_leave) FROM ".TB_PREF."leave WHERE 
	emp_id =".db_escape($emp_id)." AND leave_type=$leave_type";
    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function add_emp_info($emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
{
	if($leave_type==2)
	{
		$availed=A;
	}
     $get_leave_count=get_emp_no_of_leaves($emp_id,$leave_type);
    $getaccumleaves=get_emp_max_of_leaves($leave_type);
    $sum= $no_of_leave+$get_leave_count;
   // display_error($sum);
   /* if ($sum > $getaccumleaves) {
        display_error("employee leaves are exceeding from the accumulted leaves");
    }
    else
    {*/
        $sql = "INSERT INTO " . TB_PREF . "leave (emp_id,from_date,to_date,leave_type,no_of_leave,reason,approve,f_year,availed) 
    VALUES ( " . db_escape($emp_id) . ", " . db_escape(sql2date($from_date)) . ", " . db_escape(sql2date($to_date)) . ",
     " . db_escape($leave_type) . ", " . db_escape($no_of_leave) . ", " . db_escape($reason) . ",
     " . db_escape(1) . "," . db_escape($f_year) . "," . db_escape($availed) . ")";


   // }
//
   // add_audit_trail_payroll(ST_LEAVE, $_POST['emp_id'], Today(), _("Inserted.));

   	db_query($sql,"The department could not be added");
}


function get_emp_max_of_leaves($id)
{
    $sql = "SELECT max_accum_leaves FROM ".TB_PREF."leave_type WHERE
	id =".db_escape($id);
    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch($result);
    return $myrow['0'];
}
function get_emp_no_of_fyears($f_year)
{
    $sql = "SELECT no_of_f_years FROM ".TB_PREF."leave_type WHERE
	no_of_f_years =".db_escape($f_year);
    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch($result);
    return $myrow['0'];
}


///
//----
function update_emp_info($selected_id,$emp_id,$from_date,$to_date,$leave_type,$no_of_leave,$reason,$approve,$f_year)
{
	$sql = "UPDATE ".TB_PREF."leave SET emp_id=".db_escape($emp_id).",
	from_date=".db_escape(date2sql($from_date)).",
	to_date=".db_escape(date2sql($to_date)).",
	leave_type=".db_escape($leave_type).",
	no_of_leave=".db_escape($no_of_leave).",
	reason=".db_escape($reason).",
	approve=".db_escape($approve).",
    f_year=".db_escape($f_year)."
	 WHERE id = ".db_escape($selected_id);
	 
	     add_audit_trail_payroll(ST_LEAVE, $_POST['emp_id'], Today(), _("Updated."));

   	db_query($sql,"The department could not be updated");
	}
	
	
function delete_emp_info($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."leave WHERE
	 id=".db_escape($selected_id);
	 
	     add_audit_trail_payroll(ST_LEAVE, $_POST['emp_id'], Today(), _("Deleted"));

	 
	db_query($sql,"could not delete department");
}

function get_emp_info_no($emp_id,$from_date)
{
	
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."leave WHERE 
	emp_id =".db_escape($emp_id)."
	AND
	from_date=".db_escape(sql2date($from_date));
	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function employee_leave_attendance_duplication($emp_id,$f_year)
{

    $sql ="SELECT COUNT(*) FROM ".TB_PREF."leave i WHERE  NOW() between i.`from_date` AND i.`to_date` AND `emp_id`=$emp_id AND `f_year`=$f_year";

    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_emp_infos($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."leave";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get department");
}

function get_emp_info($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."leave WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_emp_info_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}



?>