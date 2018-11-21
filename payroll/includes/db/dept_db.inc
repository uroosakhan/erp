<?php

function add_emp_dept($description)
{
	$sql = "INSERT INTO ".TB_PREF."dept (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The department could not be added");
}

function update_emp_dept($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."dept SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The department could not be updated");
	}
	
	function delete_emp_dept($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."dept WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function get_emp_depts($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."dept";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get department");
}

function get_emp_dept($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."dept WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get department");
	return db_fetch($result);
}

function get_emp_dept_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}

//--iqra


function add_hourly_rate($dept,$grade,$s_from,$s_to,$ot,$state)
{
    $sql = "INSERT INTO ".TB_PREF."hourly_rate (dept,grade,salary_from,salary_to,ot,state) VALUES (
    ".db_escape($dept) . ",
    ".db_escape($grade) . ",
    ".db_escape($s_from) . ",
    ".db_escape($s_to) . ",
    ".db_escape($ot) . ",
     ".db_escape($state) . "
    
    )";
    db_query($sql,"The department could not be added");
}
//
function update_hourly_rate($selected_id, $dept,$grade,$s_from,$s_to,$ot,$state)
{
    $sql = "UPDATE ".TB_PREF."hourly_rate SET 
        dept=".db_escape($dept)." ,
        grade=".db_escape($grade).", 
        salary_from=".db_escape($s_from).", 
        salary_to=".db_escape($s_to)." ,
        ot=".db_escape($ot)." ,
         state=".db_escape($state)." 
 
        
    WHERE id = ".db_escape($selected_id);
    db_query($sql,"The department could not be updated");
}

function delete_hourly_rate($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."hourly_rate WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete department");
}

function get_hourly_rates($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."hourly_rate";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    $sql .= " ORDER BY id";
    return db_query($sql,"could not get department");
}

function get_hourly_rate($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."hourly_rate WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get department");
    return db_fetch($result);
}
function get_emp_grade($dept_id)
{
    $sql = "SELECT description FROM ".TB_PREF."grade WHERE id = ".db_escape($dept_id);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}


?>