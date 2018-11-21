<?php

function add_man_qualification($emp_name,$degree,$passing_year, $institute,$passing_percent, $remarks, $employee_id)
{
    $sql = "INSERT INTO ".TB_PREF."man_qualification (emp_name,degree,passing_year,institute, 	passing_percent, remarks,employee_id )
     VALUES (".db_escape($emp_name) . ",".db_escape($degree) . ","
        .db_escape($passing_year) .","
        .db_escape($institute).","
        .db_escape($passing_percent).","
        .db_escape($remarks).","
        .db_escape($employee_id).

        ")";
    db_query($sql,"The employee qualification could not be added");
}

function update_man_qualification($selected_id, $emp_name,$degree,$passing_year, $institute,$passing_percent, $remarks)
{
    $sql = "UPDATE ".TB_PREF."man_qualification SET 
    emp_name=".db_escape($emp_name).",
    degree=".db_escape($degree).",
	passing_year=".db_escape($passing_year).",
	institute=".db_escape($institute).",
	passing_percent=".db_escape($passing_percent).",
	remarks=".db_escape($remarks)."
	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The employee qualification could not be updated");
}

function delete_man_qualification($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."man_qualification  WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete the employee qualification ");
}

function get_emp_man_qualification_all($id)
{
    $sql = "SELECT * FROM ".TB_PREF."man_qualification WHERE employee_id=".db_escape($id);
    //if (!$show_inactive) $sql .= " WHERE !inactive";
    $sql .= " ORDER BY id";
    return db_query($sql,"could not get employee qualification ");
}

function get_emp_man_qualification($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."man_qualification WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get employee qualification ");
    return db_fetch($result);
}


function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}

?>