<?php
/**********************************************
Author: Joe Hunt
Author: Tom Moulton - added Export of many types and import of the same
Name: Import of CSV formatted items
Free software under GNU GPL
 ***********************************************/
$page_security = 'SA_CSVIMPORT';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");
add_access_extensions();

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/inventory/includes/inventory_db.inc");
include_once($path_to_root . "/inventory/includes/db/items_codes_db.inc");
include_once($path_to_root . "/dimensions/includes/dimensions_db.inc");
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");

function get_add_workcenter($name) {
	$name = db_escape($name);
	$sql = "SELECT id FROM ".TB_PREF."workcentres WHERE UPPER( name ) = UPPER( $name )";
	$result = db_query($sql, "Can not search workcentres table");
	$row = db_fetch_row($result);
	if (!$row[0]) {
		$sql = "INSERT INTO ".TB_PREF."workcentres (name, description) VALUES ( $name, $name)";
		$result = db_query($sql, "Could not add workcenter");
		$id = db_insert_id();
		display_notification("Added $name as id $id");
	} else $id = $row[0];
	return $id;
}

function check_stock_id($stock_id) {
	$sql = "SELECT * FROM ".TB_PREF."stock_master where stock_id = $stock_id";
	$result = db_query($sql, "Can not look up stock_id");
	$row = db_fetch_row($result);
	if (!$row[0]) return 0;
	return 1;
}

function get_supplier_id($supplier) {
	$sql = "SELECT supplier_id FROM ".TB_PREF."suppliers where supp_name = $supplier";
	$result = db_query($sql, "Can not look up supplier");
	$row = db_fetch_row($result);
	if (!$row[0]) return 0;
	return $row[0];
}

function get_dimension_by_name($name) {
	if ($name = '') return 0;

	$sql = "SELECT * FROM ".TB_PREF."dimensions WHERE name=$name";
	$result = db_query($sql, "Could not find dimension");
	if ($db_num_rows($result) == 0) return -1;
	$row = db_fetch_row($result);
	if (!$row[0]) return -1;
	return $row[0];
}

function download_file($filename, $saveasname='')
{
	if (empty($filename) || !file_exists($filename))
	{
		return false;
	}
	if ($saveasname == '') $saveasname = basename($filename);
	header('Content-type: application/vnd.ms-excel');
	header('Content-Length: '.filesize($filename));
	header('Content-Disposition: attachment; filename="'.$saveasname.'"');
	readfile($filename);

	return true;
}

// change this from file to mysql $result
function download_csv($filename, $saveasname='')
{
	if (empty($filename) || !file_exists($filename))
	{
		return false;
	}
	if ($saveasname == '') $saveasname = basename($filename);
	header('Content-type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="'.$saveasname.'"');
// print all results, converting data as needed
	return true;
}

$action = 'import';
if (isset($_GET['action'])) $action = $_GET['action'];
if (isset($_POST['action'])) $action = $_POST['action'];
page("Import Monthly Attendance");


if (isset($_POST['import'])) {

	if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '') {

		$filename = $_FILES['imp']['tmp_name'];
		$sep = $_POST['sep'];

		$fp = @fopen($filename, "r");
		if (!$fp)
			die("can not open file $filename");

		$lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = $imp=0;
		// type, item_code, stock_id, description, category, units, qty, mb_flag, currency, price
		$f_year = get_current_fiscalyear();
display_error($sep);
		while ($data = fgetcsv($fp, 4096, $sep)) {
			if ($lines++ == 0) continue;
			list($type, $code, $id, $description, $category, $units, $qty, $mb_flag, $currency, $price) = $data;
			$type = strtoupper($type);
			$mb_flag = strtoupper($mb_flag);
			$reference=$Refs->get_next(ST_DIMENSION);

function get_holidays_name($date)
{
	$date_n=sql2date($date);
	$sql = "SELECT count(*) FROM `".TB_PREF."gazetted_holidays` WHERE `date` = '$date_n'";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
				$sql = "SELECT id, name FROM ".TB_PREF."dimensions WHERE name='$units' AND 	type_=1";
				$result = db_query($sql, "could not get stock category");
				$row = db_fetch_row($result);
				if (!$row) {
					add_dimension($reference, $units, 1, $code, $code, $memo_,$division123,$project123);
					$division_n = db_insert_id();
				} else $division_n = $row[0];

			$sql = "SELECT id, name FROM ".TB_PREF."dimensions WHERE name='$qty' AND 	type_=2";
			$result = db_query($sql, "could not get stock category");
			$row = db_fetch_row($result);
			if (!$row) {
				add_dimension($reference, $qty, 2, $code, $code, $memo_,$division_n,$project123);
				$project_n = db_insert_id();
			} else $project_n = $row[0];
			$sql = "SELECT id, name FROM ".TB_PREF."dimensions WHERE name='$mb_flag' AND 	type_=3";
			$result = db_query($sql, "could not get stock category");
			$row = db_fetch_row($result);
			if (!$row) {
				add_dimension($reference, $mb_flag, 3, $code, $code, $memo_,$division_n,$project_n);
				$location_n = db_insert_id();
			} else $location_n = $row[0];
			$sql = "SELECT employee_id, emp_name FROM ".TB_PREF."employee WHERE emp_name='$id'";
			$result = db_query($sql, "could not get stock category");
			$row = db_fetch_row($result);
			if (!$row) {
				add_employee(
					$emp_code,
					$id,
					$emp_father,
					$emp_cnic,
					$DOB,
					$j_date,
					$l_date,
					$emp_reference,
					$emp_home_phone,
					$emp_mobile,
					$emp_email,
					$emp_bank,
					$company_bank,
					$basic_salary,
					$prev_salary,
					$duty_hours,
					$social_sec,
					$emp_ntn,
					$emp_eobi,
					$emp_address,
					$notes,
					$emp_title,
					$emp_gen,
					$emp_dept,
					$emp_desig,
					$emp_grade,
					$cpf,
					$employer_cpf,
					$division_n,
					$project_n,
					$age,
					$report,
					$location_n,
					$vehicle,
					$status,
					$tax_deduction,
					$applicable,
					$leave_applicable,
					$sessi_applicable,
					$eobi_applicable,
					$mb_flag,
					$active_filer,
					$bank_name,
					$bank_branch,
					$salary,
					$cnic_expiry_date,
					$pec_no,
					$pec_expiry_date,
					$license_no,
					$license_expiry_date,
					$text_filer,
					$text_non_filer,
					$loan_account,
					$advance_account,
					$salary_account,
					$bonus_account,
					$payroll_expenses,
					$payroll_liabilty,
					$advance_receivable,
					$payment_account,
					$tax_liability,
					$deduction_account);
				$empployee_id_n = db_insert_id();
			} else $empployee_id_n = $row[0];

			$empployee_id = $empployee_id_n;
			$attendance_date = 	$code;
			$check_in = $description;
			$check_out = $category;
			$f_year = $f_year['id'];
			$division = $division_n;
			$project = $project_n;
			$location = $location_n;
			$day = date("D", mktime(0, 0, 0, date("m") , $attendance_date,date("Y")));
			$holiday=get_holidays_name($attendance_date);
			if($attendance_date !='' && $day!='Sun' && $day!='Sat' && $holiday<1)
			{
				$sql =" INTO 0_daily_attendance (att_date, employee,check_in, check_out, fiscal_year,division,project,location) 
                                VALUES (".db_escape(sql2date($attendance_date)).",
                                        ".db_escape($empployee_id).",
                                        ".db_escape($check_in).",
                                        ".db_escape($check_out).",
                                        ".db_escape($f_year).",
                                        ".db_escape($division).",
                                        ".db_escape($project).",
                                        ".db_escape($location).")";
				db_query($sql,'Unable To insert');
				display_notification("CSV Has been Added ");

			}
			else{
				display_error("input data is wrong");
			}
//$imp++;
		}
//@fclose($fp);

		
		//if ($imp > 0) display_notification("$imp data has been posted.");

	} else display_error("No CSV file selected");
}
{
	start_form(true);

	start_table(TABLESTYLE2, "width=40%");


	label_row("CSV Import File:", "<input type='file' id='imp' name='imp'>");
	echo'<td>Download Attendance CSV : </td><td><a href="'.$path_to_root.'/payroll/daily_attendance_csv.csv" download>Download Sheet</a></td></tr>';

	end_table(1);

	submit_center('import', "Import CSV File");

	end_form();
}
?><?php


end_page();
?>
