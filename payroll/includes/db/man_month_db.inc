<?php

function validate_man_month($selected_id){

    $sql = "SELECT employee_name FROM ".TB_PREF."man_month WHERE employee_name=".db_escape($selected_id);

    $result = db_query($sql,"could not get allowance");
    $row= db_fetch_row($result);
    return $row[0];

}

function add_man_month($employee_name,$project_name,$man_month_value,$month,$employee_id,$division,$location,$f_year)
{

    $sql = "INSERT INTO ".TB_PREF."man_month (employee_name,project_name,man_month_value,month_id,employee_id,f_year,division,location)
     VALUES (".db_escape($employee_name).",".db_escape($project_name) . ","
        .db_escape($man_month_value) .","
        .db_escape($month).",
        ".db_escape($employee_id).",
        ".db_escape($f_year).",
        ".db_escape($division).",
        ".db_escape($location)."
        )";
    db_query($sql,"The field could not be added");
}

function update_man_month($selected_id, $employee_name,$project_name,$man_month_value,$month,$division,$location)
{
    $sql = "UPDATE ".TB_PREF."man_month SET employee_name=".db_escape($employee_name).",
    project_name=".db_escape($project_name).",
	man_month_value=".db_escape($man_month_value).",
	month_id=".db_escape($month).",
	division=".db_escape($division).",
	location=".db_escape($location)."
	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The field could not be updated");
}

function delete_man_month($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."man_month  WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete ");
}

function get_emp_man_month11($id)
{
    $sql = "SELECT * FROM ".TB_PREF."man_month WHERE employee_id=".db_escape($id)." AND employee_name!='' ";
    //if (!$show_inactive) $sql .= " WHERE !inactive";
    $sql .= " ORDER BY id";
    return db_query($sql,"could not get ");
}

function get_emp_man_month($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."man_month WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get ");
    return db_fetch($result);
}


function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}

?>