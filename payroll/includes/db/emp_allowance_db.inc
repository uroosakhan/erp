<?php
function validate_allowance($selected_id){
	$sql = "SELECT allow_id FROM ".TB_PREF."emp_allowance WHERE allow_id=".db_escape($selected_id);

	$result = db_query($sql,"could not get allowance");
	$row= db_fetch_row($result);
return $row[0];

}
function add_emp_allowance11($emp_id,$allow_id,$amount)
{
	$sql = " INSERT INTO ".TB_PREF."emp_allowance (emp_id,allow_id,amount) VALUES (".db_escape($emp_id) . ",".db_escape($allow_id) . ",".db_escape($amount) . ")";
   	db_query($sql,"The allowance could not be added");
}

function update_emp_allowance11($selected_id, $emp_id,$allow_id,$amount,$dept_id,$month)
{
	$sql = "UPDATE ".TB_PREF."emp_allowance SET emp_id=".db_escape($emp_id).",allow_id=".db_escape($allow_id).",
	amount=".db_escape($amount)."
	WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The allowance could not be updated");
	}
	
	function delete_emp_allowance11($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."emp_allowance WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function get_emp_allowances11($emp_id)
{
	$sql = " SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($emp_id);
	//if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY id";
	return db_query($sql,"could not get allowance");
}
//for checking
function add_payroll_allowance_for_pay($payroll_id,$allow_id,$amount,$emp_id)
{
	$sql = "INSERT INTO ".TB_PREF."payroll_allowance (payroll_id,allow_id,amount,emp_id) 
	VALUES ( ".db_escape($payroll_id).", ".db_escape($allow_id).",".db_escape($amount) . ",".db_escape($emp_id) . ")";

   	db_query($sql,"The department could not be added");
}
function get_payroll_allowances11($emp_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($emp_id);
	//if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY id";
	return db_query($sql,"could not get allowance");
}
function get_payroll_allowances11_count($emp_id)
{
	$sql = "SELECT COUNT(allow_id) FROM ".TB_PREF."emp_allowance
 WHERE emp_id =".db_escape($emp_id);

	$result = db_query($sql, "Could not get employees.");
	$myrow = db_fetch($result);
	return $myrow['0'];
}

function get_emp_allowance11($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get allowance");
	return db_fetch($result);
}

function get_emp_allowance_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."allowance WHERE id = ".db_escape($dept_id);
	$result = db_query($sql, "could not get allowance");
	$row = db_fetch($result);
	return $row[0];
}
function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function refresh($loc)
{
echo "<meta http-equiv='refresh' content='0'>";
}

?>