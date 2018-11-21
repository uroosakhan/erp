<?php
		

function add_employee_evaluation($curr_date,$quality_work,$job_knwoledge, $quantity_work, $result_orientation, $depentability ,$initiative, 
$team_work, $adaptability, $conformance, $final_result, $remarks)
{
	
	
$sql = "INSERT INTO ".TB_PREF."employee_evaluation(employee,curr_date,quality_work,job_knwoledge,quantity_work,result_orientation,depentability,initiative,team_work,adaptability,conformance,final_result,remarks)
		VALUES (
		".db_escape(date2sql($curr_date)). ",
		".db_escape($quality_work). ",
		".db_escape($job_knwoledge). ",
		".db_escape($quantity_work). ", 
		".db_escape($result_orientation). ", 
		".db_escape($depentability). ", 
		".db_escape($initiative). ", 
		".db_escape($team_work). ",  
		".db_escape($adaptability). ",  		
		".db_escape($conformance). ",
		".db_escape($final_result). ",
		".db_escape($remarks). ")";

	db_query($sql,"The Employee could not be added");
}

function update_employee_evaluation($employee_id, $curr_date,$quality_work,$job_knwoledge, $quantity_work, $result_orientation, $depentability ,$initiative,$team_work, $adaptability, $conformance, $final_result, $remarks)
{
	$sql = "UPDATE ".TB_PREF."employee_evaluation 
	SET curr_date=".db_escape(sql2date($curr_date)) . ",
		quality_work=".db_escape($quality_work) . ",
		job_knwoledge=".db_escape($job_knwoledge) . ",
		quantity_work=".db_escape($quantity_work) . ",
		result_orientation=".db_escape($result_orientation) . ",
		depentability=".db_escape($depentability) . ",
		initiative=".db_escape($initiative) . ",
		team_work=".db_escape($team_work) . ",	
		adaptability=".db_escape($adaptability) . ",				
		conformance=".db_escape($conformance) . ",	
		final_result=".db_escape($final_result) . ",
		remarks=".db_escape($remarks)
		
		." WHERE employee_id = ".db_escape($employee_id);

	db_query($sql,"The Advertisment could not be updated");
}


function delete_employee_evaluation($employee_id)
{
	$sql="DELETE FROM ".TB_PREF."employee_evaluation WHERE employee_id=".db_escape($employee_id);
	db_query($sql,"check failed");
}

function get_employee_evaluation($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee_evaluation WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}


function is_new_employee_eva($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}
function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_code($employee_id)
{
	$sql = "SELECT emp_code FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_grade($employee_id)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employee_dept($employee_id)
{
	$sql = "SELECT description FROM ".TB_PREF."dept WHERE id=".db_escape($employee_id);

	$result = db_query($sql, "could not get department");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_employee_date($employee_id)
{
	$sql = "SELECT j_date FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
?>