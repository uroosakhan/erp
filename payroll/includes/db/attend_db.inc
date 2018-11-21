<?php
	
function add_attend($date, $attend_type, $time_in, $time_out)
{
	
	$sql = "INSERT INTO ".TB_PREF."employee (emp_code, emp_name, emp_father,
	emp_cnic, DOB, j_date, l_date ,emp_reference, emp_home_phone, emp_mobile, emp_email,
	emp_bac, emp_address, notes,emp_title,emp_gen,emp_dept, emp_desig,emp_grade)
		VALUES (
		".db_escape($emp_code). ",
		".db_escape($emp_name). ", 
		".db_escape($emp_father). ", 
		".db_escape($emp_cnic). ",
		".db_escape($DOB). ",
		".db_escape($j_date). ",
		".db_escape($l_date). ",
		".db_escape($emp_reference). ", 
		".db_escape($emp_home_phone). ", 
		".db_escape($emp_mobile). ", 
		".db_escape($emp_email). ", 
		".db_escape($emp_bac). ",  
		".db_escape($emp_address). ",
		".db_escape($notes). ",
		".db_escape($emp_title). ",  
		".db_escape($emp_gen). ",
		".db_escape($emp_dept). ",
		".db_escape($emp_desig). ", 
		".db_escape($emp_grade). ")";

	db_query($sql,"The employee could not be added");
}

function update_employee($employee_id, $emp_code, $emp_name, $emp_father, $emp_cnic,$DOB, $j_date, $l_date, $emp_reference,$emp_home_phone, $emp_mobile, $emp_email, $emp_bac, $emp_address, $notes, $emp_title, $emp_gen, $emp_dept, $emp_desig, $emp_grade)
{
	$sql = "UPDATE ".TB_PREF."employee SET emp_code=".db_escape($emp_code) . ",
		emp_name=".db_escape($emp_name) . ", 
		emp_father=".db_escape($emp_father) . ",
		emp_cnic=".db_escape($emp_cnic) . ",
		DOB=".db_escape($DOB) . ",
		j_date=".db_escape($j_date) . ",
		l_date=".db_escape($j_date) . ",
		emp_reference=".db_escape($emp_reference) . ",
		emp_home_phone=".db_escape($emp_home_phone) . ",
		emp_mobile=".db_escape($emp_mobile) . ",
		emp_email=".db_escape($emp_email) . ",
		emp_desig=".db_escape($emp_desig) . ",
		emp_dept=".db_escape($emp_dept) . ",
		emp_address=".db_escape($emp_address) . ",
		notes=".db_escape($notes).",
		emp_title=".db_escape($emp_title). ",  
		emp_gen=".db_escape($emp_gen). ",
		emp_dept=".db_escape($emp_dept). ",
		emp_desig=".db_escape($emp_desig). ", 
		emp_grade=".db_escape($emp_grade)
		
		." WHERE employee_id = ".db_escape($employee_id);

	db_query($sql,"The employee could not be updated");
}


function delete_employee($employee_id)
{
	$sql="DELETE FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);
	db_query($sql,"check failed");
}

function get_employee($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}

function get_employee_name($employee_id)
{
	$sql = "SELECT  emp_title,emp_gen,emp_dept, emp_desig,emp_grade FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
} 
function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
} 
function get_employee_gender($employee_id)
{
	$sql = "SELECT emp_gen FROM ".TB_PREF."gen WHERE id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

/*
function get_supplier_details($supplier_id, $to=null, $all=true)
{

	if ($to == null)
		$todate = date("Y-m-d");
	else
		$todate = date2sql($to);
	$past1 = get_company_pref('past_due_days');
	$past2 = 2 * $0past1;
	// removed - supp_trans.alloc from all summations

	if ($all)
    	$value = "(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh)";
    else	
    	$value = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_BANKDEPOSIT.",
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh - trans.alloc),
    		(trans.ov_amount + trans.ov_gst + trans.ov_discount  + trans.gst_wh + trans.alloc))";
	$due = "IF (trans.type=".ST_SUPPINVOICE." OR trans.type=".ST_SUPPCREDIT.",trans.due_date,trans.tran_date)";
    $sql = "SELECT supp.supp_name, supp.curr_code, ".TB_PREF."payment_terms.terms,

		Sum(IFNULL($value,0)) AS Balance,

		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= 0,$value,0)) AS Due,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past1,$value,0)) AS Overdue1,
		Sum(IF ((TO_DAYS('$todate') - TO_DAYS($due)) >= $past2,$value,0)) AS Overdue2,
		supp.credit_limit - Sum(IFNULL(IF(trans.type=".ST_SUPPCREDIT.", -1, 1) 
			* (ov_amount + ov_gst + ov_discount + trans.gst_wh),0)) as cur_credit,
		supp.tax_group_id

		FROM ".TB_PREF."suppliers supp
			 LEFT JOIN ".TB_PREF."supp_trans trans ON supp.supplier_id = trans.supplier_id AND trans.tran_date <= '$todate',
			 ".TB_PREF."payment_terms

		WHERE
			 supp.payment_terms = ".TB_PREF."payment_terms.terms_indicator
			 AND supp.supplier_id = $supplier_id ";
	if (!$all)
		$sql .= "AND ABS(trans.ov_amount + trans.ov_gst + trans.ov_discount + trans.gst_wh) - trans.alloc > ".FLOAT_COMP_DELTA." ";  
	$sql .= "GROUP BY
			  supp.supp_name,
			  ".TB_PREF."payment_terms.terms,
			  ".TB_PREF."payment_terms.days_before_due,
			  ".TB_PREF."payment_terms.day_in_following_month";

    $result = db_query($sql,"The customer details could not be retrieved");
    $supp = db_fetch($result);

    return $supp;
}



function get_supplier_accounts($supplier_id)
{
	$sql = "SELECT payable_account,purchase_account,payment_discount_account FROM ".TB_PREF."suppliers WHERE supplier_id=".db_escape($supplier_id);

	$result = db_query($sql, "could not get supplier");

	return db_fetch($result);
}

function get_supplier_contacts($supplier_id, $action=null)
{
	$results = array();
	$res = get_crm_persons('supplier', $action, $supplier_id);
	while($contact = db_fetch($res))
		$results[] = $contact;

	return $results;
}

function get_current_supp_credit($supplier_id)
{
	$suppdet = get_supplier_details($supplier_id);
	return $suppdet['cur_credit'];

}
*/
function is_new_employee($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}

?>