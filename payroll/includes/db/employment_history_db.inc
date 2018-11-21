<?php

function validate_employment_history($selected_id){
    $sql = "SELECT company_name FROM ".TB_PREF."employment_history WHERE company_name=".db_escape($selected_id);

    $result = db_query($sql,"could not get allowance");
    $row= db_fetch_row($result);
    return $row[0];

}


function add_employment_history($company_name,$date_from,$date_to,$designation,$remarks,$employee_id)
{
    $sqlFromDate = date2sql($date_from);
    $sqlToDate = date2sql($date_to);

    $sql = "INSERT INTO ".TB_PREF."employment_history (company_name, date_from,date_to,designation,remarks,employee_id)
     VALUES (".db_escape($company_name) . ", 
     '$sqlFromDate',
        '$sqlToDate' ,"
        .db_escape($designation).",  "
        .db_escape($remarks).",  "
        .db_escape($employee_id).

        ")";
    db_query($sql,"The company could not be added");
}

function update_employment_history($selected_id, $company_name,$date_from,$date_to,$designation,$remarks)
{
    $sqlFromDate = date2sql($date_from);
    $sqlToDate = date2sql($date_to);

    $sql = "UPDATE ".TB_PREF."employment_history SET 
    company_name=".db_escape($company_name).",
    date_from= '$sqlFromDate',
	date_to= '$sqlToDate',
	designation=".db_escape($designation).",
	remarks=".db_escape($remarks)."
	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The company could not be updated");
}

function delete_employment_history($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."employment_history  WHERE id=" .db_escape($selected_id);
    db_query($sql,"could not delete the company");
}

function get_employment_history_all($id)
{
    $sql = "SELECT * FROM ".TB_PREF."employment_history WHERE employee_id=" .db_escape($id);
    //if (!$show_inactive) $sql .= " WHERE !inactive";
    $sql .= " ORDER BY id";
    return db_query($sql,"could not get company.");
}

function get_employment_history($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employment_history WHERE id=" .db_escape($selected_id);

    $result = db_query($sql,"could not get company.");
    return db_fetch($result);
}


function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}

?>