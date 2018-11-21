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
///
function employee_month_duplication_new($month,$fiscal_year,$empl_id)
{

    $sql = "SELECT 	COUNT(month_id)  from ".TB_PREF."emp_attendance WHERE
	month_id=".db_escape($month)."
	AND fiscal_year=$fiscal_year AND empl_id=$empl_id";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
function check_office_offday_import($off_day)
{

    $sql ="SELECT COUNT(*) 
FROM  `".TB_PREF."sys_pay_pref` 
WHERE  `account` LIKE  '%$off_day%'";

    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function check_get_gazzet_import($date)
{

    $sql ="SELECT COUNT(*) 
FROM  `".TB_PREF."gazetted_holidays` 
WHERE  date='$date'";
    $result = db_query($sql, "Could't get employee absent details");
    $myrow = db_fetch($result);
    return $myrow[0];
}
///
function add_employee_import($emp_code, $emp_name, $emp_father, $emp_cnic,$DOB, $j_date, $l_date, $emp_reference,$emp_home_phone,
                             $emp_mobile, $emp_email, $emp_bank, $company_bank, $basic_salary,  $prev_salary, $duty_hours, $social_sec, $emp_ntn, $emp_eobi, $emp_address, $notes, $emp_title, $emp_gen, $emp_dept, $emp_desig, $emp_grade)
{
    $DOB = date2sql($DOB);
    $j_date = date2sql($j_date);
    $l_date = date2sql($l_date);

    $sql = "INSERT INTO ".TB_PREF."employee (emp_code, emp_name, emp_father,
	emp_cnic, DOB, j_date, l_date ,emp_reference, emp_home_phone, emp_mobile, emp_email,
	emp_bank, company_bank, basic_salary, prev_salary, duty_hours, social_sec, emp_ntn, emp_eobi, emp_address, notes,emp_title,emp_gen,emp_dept, emp_desig,emp_grade)
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
		".db_escape($emp_bank). ",  
		".db_escape($company_bank). ",  		
		".db_escape($basic_salary). ",
		".db_escape($prev_salary). ",
		".db_escape($duty_hours). ",
		".db_escape($social_sec). ",
		".db_escape($emp_ntn). ",
		".db_escape($emp_eobi). ",								  		
		".db_escape($emp_address). ",
		".db_escape($notes). ",
		".db_escape($emp_title). ",  
		".db_escape($emp_gen). ",
		".db_escape($emp_dept). ",
		".db_escape($emp_desig). ", 
		".db_escape($emp_grade). ")";

    db_query($sql,"The employee could not be added");
}



$action = 'import';
if (isset($_GET['action'])) $action = $_GET['action'];
if (isset($_POST['action'])) $action = $_POST['action'];

if (isset($_POST['export'])) {
    $etype = 0;
    if (isset($_POST['export_type'])) $etype = $_POST['export_type'];
    $sales_type_id = 0;
    if (isset($_POST['sales_type_id'])) $sales_type_id = $_POST['sales_type_id'];
    $currency = "USD";
    if (isset($_POST['currency'])) $currency = $_POST['currency'];

    if ($etype == 9) {
        $fname = "emp_attendance.csv";

        $sql = "SELECT 'EMP ATTENDANCE' AS 
            type, emp.emp_name,
            a.01_type, a.01_in, a.01_out, a.02_type, a.02_in, a.02_out, a.03_type, a.03_in, a.03_out,
            a.04_type, a.04_in, a.04_out, a.05_type, a.05_in, a.05_out, a.06_type, a.06_in, a.06_out, 
            a.07_type, a.07_in, a.07_out, a.08_type, a.08_in, a.08_out, a.09_type, a.09_in, a.09_out, 
            a.10_type, a.10_in, a.10_out, a.11_type, a.11_in, a.11_out, a.12_type, a.12_in, a.12_out,
            a.13_type, a.13_in, a.13_out, a.14_type, a.14_in, a.14_out, a.15_type, a.15_in, a.15_out, 
            a.16_type, a.16_in, a.16_out, a.17_type, a.17_in, a.17_out, a.18_type, a.18_in, a.18_out, 
            a.19_type, a.19_in, a.19_out, a.20_type, a.20_in, a.20_out, a.21_type, a.21_in, a.21_out,
            a.22_type, a.22_in, a.22_out, a.23_type, a.23_in, a.23_out, a.24_type, a.24_in, a.24_out, 
            a.25_type, a.25_in, a.25_out, a.26_type, a.26_in, a.26_out, a.27_type, a.27_in, a.27_out,
            a.28_type, a.28_in, a.28_out, a.29_type, a.29_in, a.29_out, a.30_type, a.30_in, a.30_out,
            a.31_type, a.31_in, a.31_out, d.description AS division, p.description AS project,
            l.location_name, a.att_date, a.present, a.sick_leave, a.casual_leave, a.pay_leave,
            a.official_leave
           
FROM 0_emp_attendance AS a
LEFT JOIN 0_employee emp ON a.`empl_id` = emp.`employee_id`
LEFT JOIN 0_month m ON a.`month_id` = m.`id`
LEFT JOIN 0_dept dp ON a.`dept_id` = dp.`id`
LEFT JOIN 0_divison d ON a.`division` = d.`id` 
LEFT JOIN 0_project p ON a.`project` = p.`id`
LEFT JOIN 0_locations l ON a.`location` = l.`loc_code`";
    }
    //------------------------------------
    $result = db_query($sql, "Could not select csv data");
    if (db_num_rows($result) > 0) {
        // header('Content-type: application/vnd.ms-excel');
        header('Content-type: text/x-csv');
        header('Content-Disposition: attachment; filename='.$fname);
        $i = 0;
        while ($csv = db_fetch_assoc($result)) {
            $hdr = '';
            $str = '';
            while (list($k, $d) = each($csv)) {
                if ($i == 0) $hdr .= $k . ",";
                $str .= htmlspecialchars_decode($d) . ",";
            }
            if ($i == 0) echo $hdr . "\n";
            echo $str."\n";
            $i++;
        }
        exit;
    } else display_notification("No Results to download.");
}

page("Import of CSV formatted Items");

if (isset($_POST['import'])) {
    if (isset($_FILES['imp']) && $_FILES['imp']['name'] != '') {
        $filename = $_FILES['imp']['tmp_name'];
        $sep = $_POST['sep'];

        $fp = @fopen($filename, "r");

        if (!$fp)
            die("can not open file $filename");

        $lines = $i = $j = $k = $b = $u = $p = $pr = $dm_n = $emp = 0;

        while ($data = fgetcsv($fp, 4096, $sep)) {
            if ($lines++ == 0) continue;
            list($type, $emp_code, $att_date, $chkin, $chkout) = $data;

                //---------for employee name---------------//
                $sql = "SELECT employee_id, emp_code FROM " . TB_PREF . "employee 
				WHERE emp_code='$emp_code'";
                $result = db_query($sql, "could not get customer items");
                $row = db_fetch_row($result);
                if (!$row) {
                    add_employee_import($emp_code, $employee_name, $emp_father, $emp_cnic, $DOB, $j_date, $l_date,
                        $emp_reference, $emp_home_phone, $emp_mobile, $emp_email, $emp_bank, $company_bank,
                        $basic_salary, $prev_salary, $duty_hours, $social_sec, $emp_ntn, $emp_eobi,
                        $emp_address, $notes, $emp_title, $emp_gen, $emp_dept, $emp_desig, $emp_grade, $cpf,
                        $employer_cpf, $division, $project, $age, $report, $location, $vehicle, $status,
                        $tax_deduction, $applicable, $leave_applicable, $sessi_applicable, $eobi_applicable,
                        $mb_flag, $active_filer, $bank_name, $bank_branch, $salary, $cnic_expiry_date, $pec_no,
                        $pec_expiry_date, $license_no, $license_expiry_date, $text_filer, $text_non_filer,
                        $loan_account, $advance_account, $salary_account, $bonus_account, $payroll_expenses,
                        $payroll_liabilty, $advance_receivable, $payment_account, $tax_liability,
                        $deduction_account);
                    $emp_name_id = db_insert_id();
                } else $emp_name_id = $row[0];

                $get_day = date("D",strtotime(date2sql($att_date)));
                $off_day = check_office_offday_import($get_day);
                $gazzet=check_get_gazzet_import(sql2date($att_date));
            $f_year = get_current_fiscalyear();
                if ($type == 'ATTENDANCE') {
                    $month1 = date("m",strtotime($att_date));
                    if($month1==01)
                        $month=1;
                    elseif ($month1==02)
                        $month=2;
                    elseif ($month1==03)
                        $month=3;
                    elseif ($month1==04)
                        $month=4;
                    elseif ($month1==05)
                        $month=5;
                    elseif ($month1==06)
                        $month=6;
                    elseif ($month1==07)
                        $month=7;
                    elseif ($month1==08)
                        $month=8;
                    elseif ($month1==09)
                        $month=9;
                    elseif ($month1==10)
                        $month=10;
                    elseif ($month1==11)
                        $month=11;
                    elseif ($month1==12)
                        $month=12;
                    $date= date2sql($att_date);
                    $d = date("d", strtotime($att_date));
                   // display_error($off_day."-".$gazzet."-".$att_date);
                    //if($off_day<0 || $gazzet<0)
                    {
                        $sql = "INSERT INTO  " . TB_PREF . "emp_attendance (empl_id,dept_id,month_id,att_date,fiscal_year,division,
                        project,location,check_in,check_out,_type)
		                 VALUES ( " . db_escape($emp_name_id) . "," . db_escape($_POST['division']) . ",
		                  " . db_escape($_POST['month']) . ", " . db_escape(sql2date($att_date)) . ",
		                  " . db_escape($f_year['id']) . ", " . db_escape( $_POST['division']) . ",
		                  " . db_escape($_POST['project']) . "," . db_escape($_POST['location']) . ",
		                  " . db_escape($chkin) . ",
		                 " . db_escape($chkout) . ", " . db_escape($sick) . ")";
                        db_query($sql, "could not get customer");
                }
//                    else
//                    {
//                        display_error($off_day." is a off day ");
//                    }
           }
            $emp++;
        }
        @fclose($fp);
        if ($emp > 0) display_notification("$emp Employee Attendance Data added or updated");

    } else display_error("No CSV file selected");
}

if ($action == 'import') echo 'Import';
else hyperlink_params($_SERVER['PHP_SELF'], _("Import"), "action=import", false);
echo '&nbsp;|&nbsp;';
if ($action == 'export') echo 'Export';
else hyperlink_params($_SERVER['PHP_SELF'], _("Export"), "action=export", false);
echo "<br><br>";

if ($action == 'import') {
    start_form(true);

    start_table(TABLESTYLE2, "width=40%");

   // table_section_title("Default GL Accounts");

    $company_record = get_company_prefs();

    if (!isset($_POST['inventory_account']) || $_POST['inventory_account'] == "")
        $_POST['inventory_account'] = $company_record["default_inventory_act"];

    if (!isset($_POST['cogs_account']) || $_POST['cogs_account'] == "")
        $_POST['cogs_account'] = $company_record["default_cogs_act"];

    if (!isset($_POST['sales_account']) || $_POST['sales_account'] == "")
        $_POST['sales_account'] = $company_record["default_inv_sales_act"];

    if (!isset($_POST['adjustment_account']) || $_POST['adjustment_account'] == "")
        $_POST['adjustment_account'] = $company_record["default_adj_act"];

    if (!isset($_POST['wip_account']) || $_POST['wip_account'] == "")
        $_POST['wip_account'] = $company_record["default_wip_act"];
    if (!isset($_POST['sep']))
        $_POST['sep'] = ",";

    table_section_title("Separator, Location, Tax and Sales Type");
    text_row("Field separator:", 'sep', $_POST['sep'], 2, 1);
     month_list_row( "Month", 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
   
    dimensions_list_row(_("Divison"), 'division', $_POST['division'], 'All division', "", false, 1,true);
    pro_list_row(_("Project"), 'project',null, 'All Projects', "", false, 2,true,$_POST['division']);
    loc_list_row(_("Location"), 'location',null, 'All Locations', "", false, 3,true,$_POST['project']);
    label_row("CSV Import File:", "<input type='file' id='imp' name='imp'>");
    end_table(1);
    submit_center('import', "Import CSV File");

    end_form();
}
if ($action == 'export') {
    start_form(true);

    start_table(TABLESTYLE2, "width=40%");

    $company_record = get_company_prefs();
    $currency = $company_record["curr_default"];
    hidden('currency', $currency);

    table_section_title("Export Selection");
    ?>
    <tr>
        <td>Export Type:</td>
        <td><select  name='export_type' class='combo' title='' >
                <option value='9'>Emp Attendance</option>
            </select>
        </td>
    </tr>
    <?php
    end_table(1);

    hidden('action', 'export');
    submit_center('export', "Export CSV File");

    end_form();
}

end_page();
