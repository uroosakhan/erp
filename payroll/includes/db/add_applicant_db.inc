<?php
		
function check_applicant_code_duplication($applicant_code)
{
	$sql = "SELECT COUNT(applicant_code) FROM ".TB_PREF."add_applicant WHERE 
        applicant_code=".db_escape($applicant_code);
	$result = db_query($sql, "Could't get payroll");
	$myrow =  db_fetch($result);
	return $myrow[0];
}



function add_applicant($applicant_code,$date_applicant,$full_name, $date_of_birth, $nationality,$present_address, $permanent_address, $telephone_no, $mobile_no, $gender,$institution1,$institution2,$institution3,$institution4,$institution5,$year_attended1,$year_attended2,$year_attended3,$year_attended4,$year_attended5,$year_attended4,$degree_diploma1,$degree_diploma2,$degree_diploma3,$degree_diploma4,$degree_diploma5,$major_subjects1,$major_subjects2,$major_subjects3,$major_subjects4,$major_subjects5,$prof_inst1,$prof_inst2,$prof_inst3,$prof_year1,$prof_year2,$prof_year3,$prof_cour1,$prof_cour2,$prof_cour3,$organ1,$organ2,$organ3,$pos_held1,$pos_held2,$pos_held3,$employee1,$employee2,$employee3,$reason1,$reason2,$reason3)
{
	
	
	$sql = "INSERT INTO ".TB_PREF."add_applicant (applicant_code,date_applicant,full_name,date_of_birth, nationality,present_address,permanent_address,telephone_no,mobile_no,gender,institution1,institution2,institution3,institution4,institution5,year_attended1,year_attended2,year_attended3,year_attended4,year_attended5,degree_diploma1,degree_diploma2,degree_diploma3,degree_diploma4,degree_diploma5,major_subjects1,major_subjects2,major_subjects3,major_subjects4,major_subjects5,prof_inst1,prof_inst2,prof_inst3,prof_year1,prof_year2,prof_year3,prof_cour1,prof_cour2,prof_cour3,organ1,organ2,organ3,pos_held1,pos_held2,pos_held3,employee1,employee2,employee3,reason1,reason2,reason3)
		VALUES (
		".db_escape($applicant_code). ",
                ".db_escape(date2sql($date_applicant)). ",
		".db_escape($full_name). ", 
		".db_escape(date2sql($date_of_birth)). ",
		".db_escape($nationality). ",
		".db_escape($present_address). ",
		".db_escape($permanent_address). ", 
		".db_escape($telephone_no). ", 
		".db_escape($mobile_no). ", 
		".db_escape($gender). ",
		".db_escape($institution1). ",
		".db_escape($institution2). ",
		".db_escape($institution3). ",
		".db_escape($institution4). ",
		".db_escape($institution5). ",
		".db_escape($year_attended1). ",
		".db_escape($year_attended2). ",
		".db_escape($year_attended3). ",
		".db_escape($year_attended4). ",
		".db_escape($year_attended5). ",
		".db_escape($degree_diploma1). ",
		".db_escape($degree_diploma2). ",
		".db_escape($degree_diploma3). ",
		".db_escape($degree_diploma4). ",
		".db_escape($degree_diploma5). ",
		".db_escape($major_subjects1). ",
		".db_escape($major_subjects2). ",
		".db_escape($major_subjects3). ",
		".db_escape($major_subjects4). ",
		".db_escape($major_subjects5). ",
		".db_escape($prof_inst1). ",
		".db_escape($prof_inst2). ",
		".db_escape($prof_inst3). ",
		".db_escape($prof_year1). ",
		".db_escape($prof_year2). ",
		".db_escape($prof_year3). ",
		".db_escape($prof_cour1). ",
		".db_escape($prof_cour2). ",
		".db_escape($prof_cour3). ",
		".db_escape($organ1). ",
		".db_escape($organ2). ",
		".db_escape($organ3). ",
		".db_escape($pos_held1). ",
		".db_escape($pos_held2). ",
		".db_escape($pos_held3). ",
		".db_escape($employee1). ",
		".db_escape($employee2). ",
		".db_escape($employee3). ",
		".db_escape($reason1). ",
		".db_escape($reason2). ",
		".db_escape($reason3). ")";

	db_query($sql,"The Add could not be added");
}

function update_applicant($add_id,$applicant_code,$date_applicant, $full_name, $date_of_birth, $nationality,$present_address, $permanent_address, $telephone_no, $mobile_no,$gender,$institution1,$institution2,$institution3,$institution4,$institution5,$year_attended1,$year_attended2,$year_attended3,$year_attended4,$year_attended5,$degree_diploma1,$degree_diploma2,$degree_diploma3,$degree_diploma4,$degree_diploma5,$major_subjects1,$major_subjects2,$major_subjects3,$major_subjects4,$major_subjects5,$prof_inst1,$prof_inst2,$prof_inst3,$prof_year1,$prof_year2,$prof_year3,$prof_cour1,$prof_cour2,$prof_cour3,$organ1,$organ2,$organ3,$pos_held1,$pos_held2,$pos_held3,$employee1,$employee2,$employee3,$reason1,$reason2,$reason3)
{
	$sql = "UPDATE ".TB_PREF."add_applicant 
	    SET applicant_code=".db_escape($applicant_code) . ",
                date_applicant=".db_escape(sql2date($date_applicant)) . ",
		full_name=".db_escape($full_name) . ", 
		date_of_birth=".db_escape(sql2date($date_of_birth)) . ",
		nationality=".db_escape($nationality) . ",
		present_address=".db_escape($present_address) . ",
		permanent_address=".db_escape($permanent_address) . ",
		telephone_no=".db_escape($telephone_no) . ",
		mobile_no=".db_escape($mobile_no) . ",
		gender=".db_escape($gender) . ",
		institution1=".db_escape($institution1) . ",
		institution2=".db_escape($institution2) . ",
		institution3=".db_escape($institution3) . ",
		institution4=".db_escape($institution4) . ",
		institution5=".db_escape($institution5) . ",
		year_attended1=".db_escape($year_attended1) . ",
		year_attended2=".db_escape($year_attended2) . ",
		year_attended3=".db_escape($year_attended3) . ",
		year_attended4=".db_escape($year_attended4) . ",
		year_attended5=".db_escape($year_attended5) . ",
		degree_diploma1=".db_escape($degree_diploma1) . ",
		degree_diploma2=".db_escape($degree_diploma2) . ",
		degree_diploma3=".db_escape($degree_diploma3) . ",
		degree_diploma4=".db_escape($degree_diploma4) . ",
		degree_diploma5=".db_escape($degree_diploma5) . ",
		major_subjects1=".db_escape($major_subjects1) . ",
		major_subjects2=".db_escape($major_subjects2) . ",
		major_subjects3=".db_escape($major_subjects3) . ",
		major_subjects4=".db_escape($major_subjects4) . ",
		major_subjects5=".db_escape($major_subjects5) . ",
		prof_inst1=".db_escape($prof_inst1) . ",
		prof_inst2=".db_escape($prof_inst2) . ",
		prof_inst3=".db_escape($prof_inst3) . ",
		prof_year1=".db_escape($prof_year1) . ",
		prof_year2=".db_escape($prof_year2) . ",
		prof_year3=".db_escape($prof_year3) . ",
		prof_cour1=".db_escape($prof_cour1) . ",
		prof_cour2=".db_escape($prof_cour2) . ",
		prof_cour3=".db_escape($prof_cour3) . ",
		organ1=".db_escape($organ1) . ",
		organ2=".db_escape($organ2) . ",
		organ3=".db_escape($organ3) . ",
		pos_held1=".db_escape($pos_held1) . ",
		pos_held2=".db_escape($pos_held2) . ",
		pos_held3=".db_escape($pos_held3). ",
		employee1=".db_escape($employee1). ",
		employee2=".db_escape($employee2). ",
		employee3=".db_escape($employee3). ",
		reason1=".db_escape($reason1). ",
		reason2=".db_escape($reason2). ",
		reason3=".db_escape($reason3)
		
		." WHERE add_id = ".db_escape($add_id);

	db_query($sql,"The Advertisment could not be updated");
}


function delete_applicant($add_id)
{
	$sql="DELETE FROM ".TB_PREF."add_applicant WHERE add_id=".db_escape($add_id);
	db_query($sql,"check failed");
}

function get_applicant($add_id)
{
	$sql = "SELECT * FROM ".TB_PREF."add_applicant WHERE add_id=".db_escape($add_id);

	$result = db_query($sql, "could not get employee");

	return db_fetch($result);
}

function is_new_applicant($id)
{
	$tables = array('supp_trans', 'grn_batch', 'purch_orders', 'purch_data');

	return !key_in_foreign_table($id, $tables, 'supplier_id');
}

?>