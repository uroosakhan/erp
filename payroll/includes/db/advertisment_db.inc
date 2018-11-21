<?php
		

function add_advertisment($advertisment_code, $description, $no_of_position,$newspaper, $newspaper2, $newspaper3, $website, $website2, $date_of_advertisment, $close_date, $qualification,$years_of_exp, 
$gender, $designation, $job_type, $age, $salary_range,  $travel_required, $location, $required_skills)
{
	
	
	$sql = "INSERT INTO ".TB_PREF."advertisment (advertisment_code, description, no_of_position,
	newspaper,newspaper2,newspaper3, website,website2, date_of_advertisment, close_date ,qualification, years_of_exp, gender, designation,job_type,age,salary_range,travel_required,location,required_skills)
		VALUES (
		".db_escape($advertisment_code). ",
		".db_escape($description). ", 
		".db_escape($no_of_position). ", 
		".db_escape($newspaper). ",
		".db_escape($newspaper2). ",
		".db_escape($newspaper3). ",
		".db_escape($website). ",
		".db_escape($website2). ",
		".db_escape(date2sql($date_of_advertisment)). ",
		".db_escape(date2sql($close_date)). ",
		".db_escape($qualification). ", 
		".db_escape($years_of_exp). ", 
		".db_escape($gender). ", 
		".db_escape($designation). ", 
		".db_escape($job_type). ",  
		".db_escape($age). ",  		
		".db_escape($salary_range). ",
		".db_escape($travel_required). ",
		".db_escape($location). ",
		".db_escape($required_skills). ")";

	db_query($sql,"The Advertisment could not be added");
}

function update_advertisment($advertisment_id,$advertisment_code, $description, $no_of_position, $newspaper, $newspaper2, $newspaper3, $website, $website2, $date_of_advertisment, $close_date, $qualification,$years_of_exp, 
$gender, $designation, $job_type, $age, $salary_range,  $travel_required, $location, $required_skills)
{
	$sql = "UPDATE ".TB_PREF."advertisment 
	SET advertisment_code=".db_escape($advertisment_code) . ",
		description=".db_escape($description) . ", 
		no_of_position=".db_escape($no_of_position) . ",
		newspaper=".db_escape($newspaper) . ",
		newspaper2=".db_escape($newspaper2) . ",
		newspaper3=".db_escape($newspaper3) . ",
		website=".db_escape($website) . ",
		website2=".db_escape($website2) . ",
		date_of_advertisment=".db_escape(sql2date($date_of_advertisment)) . ",
		close_date=".db_escape(sql2date($close_date)) . ",
		qualification=".db_escape($qualification) . ",
		years_of_exp=".db_escape($years_of_exp) . ",
		gender=".db_escape($gender) . ",
		designation=".db_escape($designation) . ",
		job_type=".db_escape($job_type) . ",
		age=".db_escape($age) . ",	
		salary_range=".db_escape($salary_range) . ",				
		travel_required=".db_escape($travel_required) . ",	
		location=".db_escape($location) . ",	
		required_skills=".db_escape($required_skills)
		
		." WHERE advertisment_id = ".db_escape($advertisment_id);

	db_query($sql,"The Advertisment could not be updated");
}


function delete_advertisment($advertisment_id)
{
	$sql="DELETE FROM ".TB_PREF."advertisment WHERE advertisment_id=".db_escape($advertisment_id);
	db_query($sql,"check failed");
}

function get_advertisment($advertisment_id)
{
	$sql = "SELECT * FROM ".TB_PREF."advertisment WHERE advertisment_id=".db_escape($advertisment_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
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



*/
function is_new_advertisment($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}

?>