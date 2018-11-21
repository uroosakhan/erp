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

function update_employee_accounts($selected_id, $loan_account,$advance_account,$salary_account,$bonus_account,$payroll_expenses
,$payroll_liabilty,$advance_receivable,$payment_account,$tax_liability,$deduction_account)
{
    $sql = "UPDATE ".TB_PREF."employee SET 
    loan_account=".db_escape($loan_account).",
    advance_account=".db_escape($advance_account).",
	salary_account=".db_escape($salary_account).",
	bonus_account=".db_escape($bonus_account).",
	payroll_expenses=".db_escape($payroll_expenses).",
	payroll_liabilty=".db_escape($payroll_liabilty).",
	advance_receivable=".db_escape($advance_receivable).",
	payment_account=".db_escape($payment_account).",
	tax_liability=".db_escape($tax_liability).",
	deduction_account=".db_escape($deduction_account)."
	
	WHERE employee_id = ".db_escape($selected_id);
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

function get_employee_account($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($selected_id);

    $result = db_query($sql,"could not get employee nomination");
    return db_fetch($result);
}


function refresh($loc)
{
    echo "<meta http-equiv='refresh' content='0'>";
}

?>