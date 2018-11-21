<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");

//----------------------------------------------------------------------------------------------------

print_employee_balances();

function getTransactions($employee)
{
    $sql = "SELECT  ".TB_PREF."employee.*

    			FROM ".TB_PREF."employee
    			WHERE ".TB_PREF."employee.employee_id = ".db_escape($employee);


    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
function get_employee_present($month,$dept,$employee)
{
    $sql = "SELECT present FROM ".TB_PREF."presence WHERE employee_id=".db_escape($employee)."
	AND emp_dept=".db_escape($dept)."
	AND month_id=".db_escape($month);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_allowance_name($allow_id)
{
    $sql = "SELECT description FROM ".TB_PREF."allowance WHERE id=".db_escape($allow_id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_allowances($employee)
{
    $sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

    $result = db_query($sql, "could not get supplier");
    return $result;
}
function get_employee_salary($employee)
{
    $sql = "SELECT SUM(basic_salary) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_allowance($employee)
{
    $sql = "SELECT SUM(allowance) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_tax($employee)
{
    $sql = "SELECT SUM(tax) FROM ".TB_PREF."payroll WHERE emp_id=".db_escape($employee);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_employee_earning($employee)
{
    $sql = "SELECT * FROM ".TB_PREF."emp_allowance WHERE emp_id=".db_escape($employee);

    $result = db_query($sql, "could not get supplier");
    return $result;
}
function get_department_name($id)
{
    $sql = "SELECT description FROM ".TB_PREF."dept WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get supplier");

    $row = db_fetch_row($result);

    return $row[0];
}
function get_emp_title_($group_no)
{
    $sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
    $result = db_query($sql, "could not get group");
    $row = db_fetch($result);
    return $row[0];
}
function get_location_($id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id)." AND type_='3' ";
    $result = db_query($sql, "Could't get employee name");
    $myrow = db_fetch($result);
    return $myrow[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    global $path_to_root, $systypes_array;
    include_once($path_to_root . "/reporting/includes/pdf_report.inc");

    $employee = $_POST['PARAM_0'];
    $comments = $_POST['PARAM_1'];
    $orientation = $_POST['PARAM_2'];
    $destination = $_POST['PARAM_3'];
    if ($destination)
    	include_once($path_to_root . "/reporting/includes/excel_report.inc");
    //else


    $orientation = ($orientation ? 'L' : 'P');
    if ($banks == ALL_TEXT)
        $bank = _('All');
    else
        $bank_name1 = get_bank_account($banks);
    $bank_name = $bank_name1['bank_account_name'];

//    	$dec = user_price_dec();
    $dec = 0;

    $month_name = get_month_name($month);


    if ($no_zeros) $nozeros = _('Yes');
    else $nozeros = _('No');

    $cols = array(0, 20, 120,250, 290,	350,400,450, 510);

    $headers = array(_('S.No'), _('A/C No'), _('Employee Name'), _('Designation'), _('Amount'));

    $aligns = array('left',	'left',	'left',	'left',	'right');

    $params =   array( 	0 => $comments,
        1 => array('text' => _('Month'), 'from' => $month_name, 'to' => $to)
        //2 => array('text' => _('Bank'), 'from' => $bank_name, 'to' => '')

    );

    $rep = new FrontReport(_('Bank Direct Transfer Letter'), "SupplierBalances", user_pagesize(), 9, P);
    if ($orientation == 'L')
        recalculate_cols($cols);

    $rep->SetHeaderType('Header10888');
    $rep->Info($params, $cols, null, $aligns);

    $rep->Font();

    $rep->NewPage();

    $bank_account_details = get_bank_account($banks);
    $bank_account_no = $bank_account_details['bank_account_number'];
    $bank_address = $bank_account_details['bank_address'];

    $rep->Font('b');
    $rep->TextCol(0, 3, $letter_date);
    $rep->NewLine();
    $rep->TextColLines(0, 3, $bank_address);
    $rep->Font();

    $rep->NewLine();
    $rep->NewLine();

    $rep->Font('b');
    $rep->NewLine();
    $rep->Font();

    $rep->NewLine(2);
    $rep->NewLine(1);
    //header
//	$rep->MultiCell(545, 70, "" ,1, 'L', 0, 2, 25,90, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 25,165, true);
//	$rep->MultiCell(175, 400, "" ,1, 'L', 0, 2, 395,165, true);
//	$rep->MultiCell(545, 100, "" ,1, 'L', 0, 2, 25,465, true);
    //$logo = company_path() . "/images/" . 1;

    //if ($rep->company['coy_logo'] != '' && file_exists($logo))
    //{


    //}
    //$rep->multicell(100,100,"".$logo,1,'C',1,0,40,10,true);
    for($i = 0; $i < 2 ; $i++){

        $rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,110, true);
//		$rep->MultiCell(182.5, 15, "PAYSLIP FOR THE MONTH OF " ,0, 'L', 0, 2, 30,90, true);
//		$rep->MultiCell(220, 30, "Designation: ",0, 'L', 0, 2, 320,120.8, true);
//		$rep->MultiCell(50, 15, "Project: ",0, 'L', 0, 2, 30,140, true);
//		$rep->MultiCell(50, 15, "Employee Code: ",0, 'L', 0, 2, 190,110, true);
//		$rep->MultiCell(50, 15, "Date Of Joining :",0, 'L', 0, 2, 190,136, true);
//		$rep->MultiCell(50, 15, "NTN# ",0, 'L', 0, 2, 320,136, true);
        $rep->multicell(545,20," Personal Information",1,'C',1,0,25,160,false);

        $rep->MultiCell(50, 50, "Full Name: " ,0, 'L', 0, 2, 30,210, true);
        $rep->MultiCell(550, 650, "___________________________________________________ " ,0, 'L', 0, 2, 75,212, true);
        $rep->MultiCell(50, 50, "Address   : " ,0, 'L', 0, 2, 30,235, true);
        $rep->MultiCell(550, 650, "____________________________________________________________________________________________________ " ,0, 'L', 0, 2, 75,238, true);
        $rep->MultiCell(150, 50, "Street  Address  " ,0, 'L', 0, 2, 80,248, true);
        $rep->MultiCell(150, 50, "Appartment  #  " ,0, 'L', 0, 2, 480,248, true);

        $rep->MultiCell(150, 50, "Home Phone: " ,0, 'L', 0, 2, 30,265, true);
        $rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 85,268, true);

        $rep->MultiCell(150, 50, "Alternate Phone: " ,0, 'L', 0, 2, 310,265, true);
        $rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 380,268, true);

        $rep->MultiCell(150, 50, "Email Address: " ,0, 'L', 0, 2, 30,295, true);
        $rep->MultiCell(250, 50, "______________________________________________" ,0, 'L', 0, 2, 90,298, true);

        $rep->MultiCell(250, 50, "Social security number of Government ID: " ,0, 'L', 0, 2, 30,320, true);
        $rep->MultiCell(250, 50, "_________________________" ,0, 'L', 0, 2, 200,320, true);

        $rep->MultiCell(150, 50, "Home Phone: " ,0, 'L', 0, 2, 30,345, true);
        $rep->MultiCell(250, 50, "___________________________" ,0, 'L', 0, 2, 90,345, true);


        $rep->MultiCell(150, 50, "Martial Status: " ,0, 'L', 0, 2, 310,345, true);
        $rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 380,345, true);

        $rep->MultiCell(150, 50, "Spouse's Name : " ,0, 'L', 0, 2, 30,370, true);
        $rep->MultiCell(550, 650, "____________________________________________________________________________________________ " ,0, 'L', 0, 2, 105,370	, true);


        $rep->MultiCell(150, 50, "Spouse's Employer: " ,0, 'L', 0, 2, 30,395, true);
        $rep->MultiCell(250, 50, "___________________________" ,0, 'L', 0, 2, 110,395, true);


        $rep->MultiCell(150, 50, "Spouse's Work Phone: " ,0, 'L', 0, 2, 310,395, true);
        $rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 403,395, true);
        $rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,425, true);
        $rep->multicell(545,10," Job Information",1,'C',1,0,25,425,false);
        $rep->MultiCell(50, 50, "Title: " ,0, 'L', 0, 2, 30,450, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 55,450, true);
        $rep->MultiCell(60, 50, "Employee ID: " ,0, 'L', 0, 2, 315,450, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 375,450, true);
        ///
        $rep->MultiCell(50, 50, "Superviser: " ,0, 'L', 0, 2, 30,480, true);
        $rep->MultiCell(250, 50, " _________________________________________" ,0, 'L', 0, 2, 75,480, true);
        $rep->MultiCell(60, 50, "Department: " ,0, 'L', 0, 2, 315,480, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 375,480, true);
///
        $rep->MultiCell(70, 50, "Work Location: " ,0, 'L', 0, 2, 30,510, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 90,510, true);
        $rep->MultiCell(70, 50, "E-mail Address: " ,0, 'L', 0, 2, 310,510, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 375,510, true);
//
        $rep->MultiCell(70, 50, "Work Phone: " ,0, 'L', 0, 2, 30,540, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 90,540, true);
        $rep->MultiCell(60, 50, "Cell Phone: " ,0, 'L', 0, 2, 315,540, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 375,540, true);
        //
        $rep->MultiCell(70, 50, "Start Date: " ,0, 'L', 0, 2, 30,570, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 90,570, true);
        $rep->MultiCell(60, 50, "Salary: " ,0, 'L', 0, 2, 315,570, true);
        $rep->MultiCell(250, 50, "_________________________________________" ,0, 'L', 0, 2, 375,570, true);
        $rep->MultiCell(50, 15, "" ,0, 'L', 0, 2, 30,600, true);
        $rep->multicell(545,10," Emergency Contact Information",1,'C',1,0,25,600,false);

        $rep->MultiCell(50, 50, "Full Name: " ,0, 'L', 0, 2, 30,640, true);
        $rep->MultiCell(550, 650, "___________________________________________________ " ,0, 'L', 0, 2, 75,642, true);
        $rep->MultiCell(50, 50, "Address   : " ,0, 'L', 0, 2, 30,670, true);
        $rep->MultiCell(550, 650, "____________________________________________________________________________________________________ " ,0, 'L', 0, 2, 75,670, true);
        $rep->MultiCell(150, 50, "Street  Address  " ,0, 'L', 0, 2, 80,680, true);
        $rep->MultiCell(150, 50, "Appartment  #  " ,0, 'L', 0, 2, 480,680, true);

        $rep->MultiCell(150, 50, "Primary Phone: " ,0, 'L', 0, 2, 30,710, true);
        $rep->MultiCell(150, 50, " _________________________" ,0, 'L', 0, 2, 90,710, true);

        $rep->MultiCell(150, 50, "Alternate Phone: " ,0, 'L', 0, 2, 310,710, true);
        $rep->MultiCell(150, 50, "_________________________" ,0, 'L', 0, 2, 380,710, true);

        $rep->MultiCell(50, 50, "Relation   : " ,0, 'L', 0, 2, 30,760, true);
        $rep->MultiCell(550, 650, "____________________________________________________________________________________________________ " ,0, 'L', 0, 2, 75,760, true);

    }
    $emp_earn = get_employee_salary($employee);
    $emp_allownace = get_employee_allowance($employee);
    $emp_tax = get_employee_tax($employee);
    $earn = $emp_earn + $emp_allownace;
    $result = getTransactions($employee);
    while ($myrow=db_fetch($result))
    {

        $logo = company_path() . "/images/".$myrow['employee_id'].".jpg";

       $rep->AddImage($logo, 40, 750, 0, 70);

        $NetSalary = $myrow['basic_salary'] + $myrow['allowance'] - $myrow['deduction']- $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['overtime'] ;
        $SerialNo += 1;
        $yr=date('Y');

        //$rep->Font('');
        $rep->MultiCell(550, 650, " ".$myrow['emp_name'] ,0, 'L', 0, 2, 75,211, true);
        $rep->MultiCell(550, 650, " ".$myrow['emp_address'] ,0, 'L', 0, 2, 75,236, true);
        $rep->MultiCell(150, 50,  " ".$myrow['emp_home_phone'] ,0, 'L', 0, 2, 85,265, true);
        $rep->MultiCell(150, 50, " ".$myrow['emp_mobile'] ,0, 'L', 0, 2, 380,265, true);
        $rep->MultiCell(250, 50, " ".$myrow['emp_email'] ,0, 'L', 0, 2, 90,295, true);
        if($myrow['status']==0)
            $status='Single';
        elseif($myrow['status']==1)
            $status='Married';
        $rep->MultiCell(150, 50, " ".$status ,0, 'L', 0, 2, 380,345, true);
        /////
        $rep->MultiCell(250, 50, " ".get_emp_title_($myrow['emp_title']) ,0, 'L', 0, 2, 55,450, true);
        $rep->MultiCell(250, 50, " ".$myrow['emp_code'] ,0, 'L', 0, 2, 375,450, true);
        $rep->MultiCell(250, 50, " ".$myrow['report']  ,0, 'L', 0, 2, 75,480, true);
        $rep->MultiCell(250, 50, " ".get_department_name($myrow['emp_dept'] ) ,0, 'L', 0, 2, 375,480, true);
        $rep->MultiCell(250, 50, " ".get_location_($myrow['location'] ) ,0, 'L', 0, 2, 90,510, true);
        $rep->MultiCell(250, 50, " ".$myrow['emp_email'] ,0, 'L', 0, 2, 375,510, true);
        $rep->MultiCell(250, 50, " ".$myrow['emp_mobile'] ,0, 'L', 0, 2, 90,540, true);
        $rep->MultiCell(250, 50, " ".$myrow['emp_mobile'] ,0, 'L', 0, 2, 375,540, true);
        $rep->MultiCell(250, 50, " ".sql2date($myrow['j_date']) ,0, 'L', 0, 2, 90,570, true);
        $rep->MultiCell(250, 50, " ".number_format($myrow['basic_salary']),0, 'L', 0, 2, 375,570, true);





        //$rep->MultiCell(182.5, 15, $myrow['emp_name'] ,0, 'L', 0, 2, 80,110, true);
//		$rep->MultiCell(220, 30, htmlspecialchars_decode(get_employee_desg($myrow['emp_desig'])) ,0	, 'L', 0, 2, 380,120.8, true);
//		$rep->MultiCell(182.5, 15, $month_name ,0, 'L', 0, 2, 190,90, true);
//		$rep->MultiCell(182.5, 15, $yr ,0, 'L', 0, 2, 220,90, true);
//		$rep->MultiCell(182.5, 15, get_department_name($dept) ,0, 'L', 0, 2, 90,140, true);
//		$rep->MultiCell(182.5, 15, $myrow['emp_code'] ,0, 'L', 0, 2, 250,110, true);
//		$rep->MultiCell(182.5, 15, sql2date($myrow['j_date']) ,0, 'L', 0, 2, 250,136, true);
//		$rep->MultiCell(182.5, 15, $myrow['emp_ntn'] ,0, 'L', 0, 2, 380,136, true);
//		$rep->MultiCell(182.5, 15, "Loan               ".$myrow['advance_deduction'] ,0, 'L', 0, 2, 400,496, true);
//
//
//
//		$rep->Font('');
//		$rep->MultiCell(182.5, 15, "Basic Salary               ".$myrow['basic_salary'] ,0, 'L', 0, 2, 75,215, true);
//
//		$rep->MultiCell(182.5, 15, "EOBI                             ".$myrow['deduction'] ,0, 'L', 0, 2, 200,215, true);
//
//		//$rep->MultiCell(182.5, 15, "Provident Fund             ".$myrow['emp_cpf'] ,0, 'L', 0, 2, 200,227.8, true);
//		$rep->MultiCell(182.5, 15, $myrow['tax'] ,0, 'L', 0, 2, 300,240, true);
//		$rep->MultiCell(182.5, 15, "Earnings                             ".$earn ,0, 'L', 0, 2, 400,215, true);
//		$rep->MultiCell(182.5, 15, "Income Tax Deduction           ".$emp_tax ,0, 'L', 0, 2, 400,235, true);
        $allowances=get_employee_allowances($myrow['employee_id']);

        while ($myrow1=db_fetch($allowances))
        {
            $rep->NewLine(-13);
            $rep->TextCol(1, 2,	"    ".get_employee_allowance_name($myrow1['allow_id']));
            $rep->TextCol(2, 5,	$myrow1['amount']);
            $total_allw +=$myrow1['amount'] ;
            $total1 = $total_allw + $myrow['basic_salary'];
            $rep->NewLine(+13);
            $rep->NewLine();

        }
        $rep->NewLine(-5);
        $rep->Font('bold');
        //$rep->TextCol(0, 2, _("Total Earning"));
        $rep->TextCol(2, 5, $total1);
        $rep->Font('');
        $rep->NewLine(+10);
        $total_dedu +=$myrow['deduction']+$myrow['cash_advance']+$myrow['emp_cpf']+$myrow['tax'];
        $TotalNetSalary += $myrow['gross_salary']+$total1-$total_dedu;



        $rep->NewLine();
    }
    $rep->NewLine(-14);
    $rep->Font('bold');

//	$rep->MultiCell(182.5, 15, "Total Deduction                            ".$total_dedu ,0, 'L', 0, 2, 200,345, true);
//	$rep->MultiCell(182.5, 15, "Total Deduction                            ".$total_dedu ,0, 'L', 0, 2, 30,505, true);
//	$rep->MultiCell(182.5, 15, "Total Earning                              ".$total1,0, 'L', 0, 2, 30,485, true);
    $rep->Font('');
    $rep->NewLine(+14);

    $rep->Font('bold');
    $rep->NewLine(5);
    $rep->fontSize += 2;
    //$rep->TextCol(0, 4,	_('Net Salary '));

    //$rep->MultiCell(182.5, 15,_('Net Salary                                    ').$TotalNetSalary ,0, 'L', 0, 2, 30,535, true);
    $rep->NewLine(-5);
    $rep->Font('');

    $rep->NewLine();
    $rep->End();
}

?>
