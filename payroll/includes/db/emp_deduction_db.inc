<?php

function validate_deduction($emp_id,$selected_id){
	$sql = "SELECT deduc_id FROM ".TB_PREF."emp_deduction WHERE deduc_id=".db_escape($selected_id)." AND emp_id=".db_escape($emp_id)."";

	$result = db_query($sql,"could not get allowance");
	$row= db_fetch_row($result);
	return $row[0];

}

function add_emp_deduction11($emp_id,$deduc_id,$amount)
{
	$sql = "INSERT INTO ".TB_PREF."emp_deduction (emp_id,deduc_id,amount) VALUES (".db_escape($emp_id) . ",".db_escape($deduc_id) . ",".db_escape($amount) . ")";
   	db_query($sql,"The allowance could not be added");
}

function update_emp_deduction11($selected_id, $emp_id,$deduc_id,$amount)
{
	$sql = "UPDATE ".TB_PREF."emp_deduction SET emp_id=".db_escape($emp_id).",deduc_id=".db_escape($deduc_id).",amount=".db_escape($amount)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The allowance could not be updated");
	}
	
	function delete_emp_deduction11($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."emp_deduction WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete department");
}

function get_emp_deductions11($emp_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_deduction WHERE emp_id=".db_escape($emp_id);
	//if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY id";
	return db_query($sql,"could not get allowance");
}

function get_emp_deduction11($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."emp_deduction WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get allowance");
	return db_fetch($result);
}

function get_emp_deduction_name($dept_id)
{
	$sql = "SELECT description FROM ".TB_PREF."deduction WHERE id = ".db_escape($dept_id);
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