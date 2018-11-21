<?php
//saad
function add_loaction($description, $inactive)
{
    $sql = "INSERT INTO ".TB_PREF."location_name (description,inactive)
			VALUES ("
        .db_escape($description) . ", "
        .db_escape($inactive)
        . ")";
    db_query($sql,"The Description of the query failed");
}


function update_loaction($selected_id,$description, $inactive)
{
    $sql = "UPDATE ".TB_PREF."location_name SET description=".db_escape($description).",
		inactive=".db_escape($inactive)."	
		WHERE id = ".db_escape($selected_id);
    db_query($sql,"The Description Name could not be updated");
}

function delete_location($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."location_name WHERE id=".db_escape($selected_id);
    db_query($sql,"The query could not be deleted");
}
function delete_division_wise_gl($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."payroll_gl_setup WHERE id=".db_escape($selected_id);
    db_query($sql,"The query could not be deleted");
}
function get_locations12($selected_id)
{
    $sql = "SELECT *  FROM ".TB_PREF."location_name WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get sales person");
    return db_fetch($result);
}
function get_sys_pay_pref1($name)
{
	$sql = "SELECT account FROM ".TB_PREF."sys_pay_pref 
	WHERE name = ".db_escape($name);
	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);	
	return $myrow[0];
	
}

function get_location12($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."location_name";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get sales persons");
}
function get_division_wise_gl2($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."payroll_gl_setup";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get sales persons");
}
/*
function get_apprt_name($id)
{
	$sql = "SELECT appartment_name FROM ".TB_PREF."appartment WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
*/
function get_payroll_expenses_account($division,$project,$location)
{
    $sql = "SELECT  `payroll_expenses` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_payroll_bonus_account($division,$project,$location)
{
    $sql = "SELECT  `bonus_account` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_payroll_deduction_account($division,$project,$location)
{
    $sql = "SELECT  `deduction_account` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_payroll_liabilty_account($division,$project,$location)
{
    $sql = "SELECT  `payroll_liabilty` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_tax_liability_account($division,$project,$location)
{
    $sql = "SELECT  `tax_liability` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_eobi_liability_account($division,$project,$location)
{
    $sql = "SELECT  `eobi_liability` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_advance_receivable_account($division,$project,$location)
{
    $sql = "SELECT  `advance_receivable` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_payment($division,$project,$location)
{
    $sql = "SELECT  `payment` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_loan_account($division,$project,$location)
{
    $sql = "SELECT  `loan_account` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_advance_account($division,$project,$location)
{
    $sql = "SELECT  `advance_account` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_salary_account($division,$project,$location)
{
    $sql = "SELECT  `salary_account` 
FROM  `0_payroll_gl_setup` 
WHERE  `division` =$division
AND  `project` =$project
AND  `location` =$location";
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}

function get_division_wise_gl ($selected_id)
{
    $sql = "SELECT *  FROM ".TB_PREF."payroll_gl_setup WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get sales person");
    return db_fetch($result);
}
function get_dim_name($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)."  ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function update_division_wise_gl($selected_id, $payroll_expenses, $advance_receivable, $payment, $payroll_liabilty,
                                 $tax_liability, $eobi_liability, $loan_account, $advance_account, $salary_account,
                                 $bonus_account, $deduction_account, $division, $project, $location, $inactive)
{
    $sql = "UPDATE ".TB_PREF."payroll_gl_setup 
    SET payroll_expenses=".db_escape($payroll_expenses).",
    advance_receivable=".db_escape($advance_receivable).",
    payment=".db_escape($payment).",
    payroll_liabilty=".db_escape($payroll_liabilty).",
    tax_liability=".db_escape($tax_liability).",
    eobi_liability=".db_escape($eobi_liability).",
    loan_account=".db_escape($loan_account).",
    advance_account=".db_escape($advance_account).",
    salary_account=".db_escape($salary_account).",
    bonus_account=".db_escape($bonus_account).",
    deduction_account=".db_escape($deduction_account).",
    division=".db_escape($division).",
    project=".db_escape($project).",
    location=".db_escape($location).",
	inactive=".db_escape($inactive)."	
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"The Description Name could not be updated");
}

function add_division_wise_gl($payroll_expenses, $advance_receivable, $payment, $payroll_liabilty, $tax_liability,
                              $eobi_liability, $loan_account, $advance_account, $salary_account, $bonus_account,
                              $deduction_account, $division, $project, $location, $inactive)
{
    $sql = "INSERT INTO ".TB_PREF."payroll_gl_setup (payroll_expenses, advance_receivable, payment, payroll_liabilty, 
    tax_liability, eobi_liability, loan_account, advance_account, salary_account, bonus_account, deduction_account, 
    division, project, location, inactive)
			VALUES ("
        .db_escape($payroll_expenses). ", "
        .db_escape($advance_receivable). ", "
        .db_escape($payment). ", "
        .db_escape($payroll_liabilty). ", "
        .db_escape($tax_liability). ", "
        .db_escape($eobi_liability). ", "
        .db_escape($loan_account). ", "
        .db_escape($advance_account). ", "
        .db_escape($salary_account). ", "
        .db_escape($bonus_account). ", "
        .db_escape($deduction_account). ", "
        .db_escape($division). ", "
        .db_escape($project). ", "
        .db_escape($location). ", "
        .db_escape($inactive)
        . ")";
    db_query($sql,"The Description of the query failed");
}
?>