<?php

function validate_employee_nomination($selected_id){
    $sql = "SELECT nominee_name FROM ".TB_PREF."employee_nomination WHERE nominee_name=".db_escape($selected_id);

    $result = db_query($sql,"could not get allowance");
    $row= db_fetch_row($result);
    return $row[0];

}

function add_employee_nomination($nominee_name,$relation,$age, $share,$remarks, $employee_id)
{
    $sql = "INSERT INTO ".TB_PREF."employee_nomination ( nominee_name,relation,age, share,remarks,employee_id)
     VALUES (".db_escape($nominee_name) . ",".db_escape($relation) . ","
        .db_escape($age) .","
        .db_escape($share).","
        .db_escape($remarks).","
        .db_escape($employee_id).

        ")";
    db_query($sql,"The employee nomination could not be added");
}

function update_employee_nomination($selected_id, $nominee_name,$relation,$age,$share,$remarks)
{
    $sql = "UPDATE ".TB_PREF."employee_nomination SET 
    nominee_name=".db_escape($nominee_name).",
    relation=".db_escape($relation).",
	age=".db_escape($age).",
	share=".db_escape($share).",
	remarks=".db_escape($remarks)."
	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The employee nomination could not be updated");
}

function delete_employee_nomination($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."employee_nomination  WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete the employee nomination ");
}

function get_employee_nomination_all($id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee_nomination WHERE employee_id=".db_escape($id);
    //if (!$show_inactive) $sql .= " WHERE !inactive";
    $sql .= " ORDER BY id";
    return db_query($sql,"could not get employee nomination");
}

function get_employee_nomination($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee_nomination WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get employee nomination");
    return db_fetch($result);
}


function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}

?>