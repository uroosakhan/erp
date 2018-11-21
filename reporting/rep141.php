<?php
$page_security = 'SA_OPEN';

$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/payroll/includes/db/suppliers_db2.inc");
include_once($path_to_root . "/payroll/includes/db/dept_db.inc");
//----------------------------------------------------------------------------------------------------

print_employee_balances();
//
function getTransactions($divison, $project,$location,$employee,$month)
{
	$sql = " SELECT ".TB_PREF."employee.emp_name,".TB_PREF."emp_attendance.empl_id ,".TB_PREF."emp_attendance.month_id,
    ".TB_PREF."emp_attendance.fiscal_year , 
   
    
    concat(".TB_PREF."emp_attendance.01_type,'-',".TB_PREF."emp_attendance.01_in,'  ',".TB_PREF."emp_attendance.01_out) as Record01,
    concat(".TB_PREF."emp_attendance.02_type,'-',".TB_PREF."emp_attendance.02_in,'  ',".TB_PREF."emp_attendance.02_out) as Record02,
    concat(".TB_PREF."emp_attendance.03_type,'-',".TB_PREF."emp_attendance.03_in,'  ',".TB_PREF."emp_attendance.03_out) as Record03,
    concat(".TB_PREF."emp_attendance.04_type,'-',".TB_PREF."emp_attendance.04_in,'  ',".TB_PREF."emp_attendance.04_out) as Record04,
    concat(".TB_PREF."emp_attendance.05_type,'-',".TB_PREF."emp_attendance.05_in,'  ',".TB_PREF."emp_attendance.05_out) as Record05,
    concat(".TB_PREF."emp_attendance.06_type,'-',".TB_PREF."emp_attendance.06_in,'  ',".TB_PREF."emp_attendance.06_out) as Record06,
    concat(".TB_PREF."emp_attendance.07_type,'-',".TB_PREF."emp_attendance.07_in,'  ',".TB_PREF."emp_attendance.07_out) as Record07,
    concat(".TB_PREF."emp_attendance.08_type,'-',".TB_PREF."emp_attendance.08_in,'  ',".TB_PREF."emp_attendance.08_out) as Record08,
    concat(".TB_PREF."emp_attendance.09_type,'-',".TB_PREF."emp_attendance.09_in,'  ',".TB_PREF."emp_attendance.09_out) as Record09,
    concat(".TB_PREF."emp_attendance.10_type,'-',".TB_PREF."emp_attendance.10_in,'  ',".TB_PREF."emp_attendance.10_out) as Record10,
    concat(".TB_PREF."emp_attendance.11_type,'-',".TB_PREF."emp_attendance.11_in,'  ',".TB_PREF."emp_attendance.11_out) as Record11,
    concat(".TB_PREF."emp_attendance.12_type,'-',".TB_PREF."emp_attendance.12_in,'  ',".TB_PREF."emp_attendance.12_out) as Record12,
    concat(".TB_PREF."emp_attendance.13_type,'-',".TB_PREF."emp_attendance.13_in,'  ',".TB_PREF."emp_attendance.13_out) as Record13,
    concat(".TB_PREF."emp_attendance.14_type,'-',".TB_PREF."emp_attendance.14_in,'  ',".TB_PREF."emp_attendance.14_out) as Record14,
    concat(".TB_PREF."emp_attendance.15_type,'-',".TB_PREF."emp_attendance.15_in,'  ',".TB_PREF."emp_attendance.15_out) as Record15,
    concat(".TB_PREF."emp_attendance.16_type,'-',".TB_PREF."emp_attendance.16_in,'  ',".TB_PREF."emp_attendance.16_out) as Record16,
    concat(".TB_PREF."emp_attendance.17_type,'-',".TB_PREF."emp_attendance.17_in,'  ',".TB_PREF."emp_attendance.17_out) as Record17,
    concat(".TB_PREF."emp_attendance.18_type,'-',".TB_PREF."emp_attendance.18_in,'  ',".TB_PREF."emp_attendance.18_out) as Record18,
    concat(".TB_PREF."emp_attendance.19_type,'-',".TB_PREF."emp_attendance.19_in,'  ',".TB_PREF."emp_attendance.19_out) as Record19,
    concat(".TB_PREF."emp_attendance.20_type,'-',".TB_PREF."emp_attendance.20_in,'  ',".TB_PREF."emp_attendance.20_out) as Record20,
    concat(".TB_PREF."emp_attendance.21_type,'-',".TB_PREF."emp_attendance.21_in,'  ',".TB_PREF."emp_attendance.21_out) as Record21,
    concat(".TB_PREF."emp_attendance.22_type,'-',".TB_PREF."emp_attendance.22_in,'  ',".TB_PREF."emp_attendance.22_out) as Record22,
    concat(".TB_PREF."emp_attendance.23_type,'-',".TB_PREF."emp_attendance.23_in,'  ',".TB_PREF."emp_attendance.23_out) as Record23,
    concat(".TB_PREF."emp_attendance.24_type,'-',".TB_PREF."emp_attendance.24_in,'  ',".TB_PREF."emp_attendance.24_out) as Record24,
    concat(".TB_PREF."emp_attendance.25_type,'-',".TB_PREF."emp_attendance.25_in,'  ',".TB_PREF."emp_attendance.25_out) as Record25,
    concat(".TB_PREF."emp_attendance.26_type,'-',".TB_PREF."emp_attendance.26_in,'  ',".TB_PREF."emp_attendance.26_out) as Record26,
    concat(".TB_PREF."emp_attendance.27_type,'-',".TB_PREF."emp_attendance.27_in,'  ',".TB_PREF."emp_attendance.27_out) as Record27,
    concat(".TB_PREF."emp_attendance.28_type,'-',".TB_PREF."emp_attendance.28_in,'  ',".TB_PREF."emp_attendance.28_out) as Record28,
    concat(".TB_PREF."emp_attendance.29_type,'-',".TB_PREF."emp_attendance.29_in,'  ',".TB_PREF."emp_attendance.29_out) as Record29,
    concat(".TB_PREF."emp_attendance.30_type,'-',".TB_PREF."emp_attendance.30_in,'  ',".TB_PREF."emp_attendance.30_out) as Record30,
    concat(".TB_PREF."emp_attendance.31_type,'-',".TB_PREF."emp_attendance.31_in,'  ',".TB_PREF."emp_attendance.31_out) as Record31
    
    
    FROM ".TB_PREF."emp_attendance

INNER JOIN ".TB_PREF."employee ON ".TB_PREF."emp_attendance.`empl_id` =".TB_PREF."employee.`employee_id`


 WHERE ".TB_PREF."emp_attendance.empl_id  != 0";

//if ($divison != 0)
//$sql .= " AND ".TB_PREF."emp_attendance.divison =".db_escape($divison);
//
//if ($project != 0)
//$sql .= " AND ".TB_PREF."emp_attendance.project =".db_escape($project);
//if ($location != 0)
//$sql .= " AND ".TB_PREF."emp_attendance.location =".db_escape($location);

if ($employee != ALL_TEXT)
$sql .= " AND ".TB_PREF."emp_attendance.empl_id =".db_escape($employee);


if ($month !='')
$sql .= " AND ".TB_PREF."emp_attendance.month_id =".db_escape($month);

    $TransResult = db_query($sql,"No transactions were returned");

    return $TransResult;
}
function get_employee_name11($employee_id)
{
	$sql = "SELECT emp_name FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_employe_name ($employee_id)
{
	$sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

	$result = db_query($sql, "could not get supplier");

	$fetch =db_fetch($result);
	return $fetch;
}
function get_division_name($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE id=".db_escape($id);
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}

function get_location_name2($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE main_project=".db_escape($id)." AND type_='2' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_location_name21($id)
{
	$sql = "SELECT name FROM ".TB_PREF."dimensions 
	WHERE main_project=".db_escape($id)." AND type_='3' ";
	$result = db_query($sql, "Could't get employee name");
	$myrow = db_fetch($result);
	return $myrow[0];
}
//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;

//    	$divison = $_POST['PARAM_0'];
//    	$project = $_POST['PARAM_1'];
//    	$location  = $_POST['PARAM_2'];
		$month = $_POST['PARAM_0'];
        $employee = $_POST['PARAM_1'];
    	$comments = $_POST['PARAM_2'];
	    $orientation = $_POST['PARAM_3'];
	    $destination = $_POST['PARAM_4'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	/*
	if ($banks == ALL_TEXT)
		$bank = _('All');
	else
		$bank_name1 = get_bank_account($banks);
		$bank_name = $bank_name1['bank_account_name'];		
*/
//
//if ($divison == ALL_TEXT)
//		$div = _('All');
//	else
//		$div = get_division_name ($divison);
//
//	if ($project == ALL_TEXT)
//		$pro = _('All');
//	else
//		$pro = get_location_name2($project);
//
//  if ($location == ALL_TEXT)
//		$pro = _('All');
//	else
//		$pro = get_location_name21($location);

  if ($employee == ALL_TEXT)
		$emp = _('All');
	else
		$emp = get_employee_name11($employee);
    	$dec = user_price_dec();


  if ($month == ALL_TEXT)
		$mon = _('All');
	else
		$mon= get_month_name($month);





		//$month_name = get_month_name($month);


	if ($no_zeros) $nozeros = _('Yes');
	else $nozeros = _('No');

    $cols = array(0, 20, 100, 150,200,250,300,350,400,450,500,550,600,650,700,750,800,
        850,900,950,1000,1050,1100,1150,1200,1250,1300,1350,1400,1450,1500,1550,1600,
        1650,1700);

    $date = '2017-11-01';
    $end = '2017-11-' . date('t', strtotime($date));
    $array = array();
    while(strtotime($date) <= strtotime($end)) {
        $day_num = date('d', strtotime($date));
        $day_name = date('l', strtotime($date));
        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
        $array[] = $day_name . ' ' . $day_num;
    }

    $headers = array(
        _("Employee ID"),
        _("Employee Name"),
        _("Leave Days"),
        _("Working Days"),

        $array[0],
        $array[1],
        $array[2],
        $array[3],
        $array[4],
        $array[5],
        $array[6],
        $array[7],
        $array[8],
        $array[9],
        $array[10],
        $array[11],
        $array[12],
        $array[13],
        $array[14],
        $array[15],
        $array[16],
        $array[17],
        $array[18],
        $array[19],
        $array[20],
        $array[21],
        $array[22],
        $array[23],
        $array[24],
        $array[25],
        $array[26],
        $array[27],
        $array[28],
        $array[29],
        $array[30],


    );

    $aligns = array('left','left','left','left','left','left','left','left','left',
        'left','left','left','left','left','left','left','left','left','left','left','left',
        'left','left','left','left','left','left','left','left','left','left','left','left','left','left');

    $params =   array( 	0 => $comments,
    			1 => array('text' => _('Divison'), 'from' => $divison, 'to' => $to),
    			2 => array('text' => _('Project'), 'from' => $project, 'to' => $to),
    			3 => array('text' => _('Location'), 'from' => $location, 'to' => $to),
				4 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
				5 => array('text' => _('Employee'), 'from' => $month, 'to' => $to)

			);
$orientation = 'L';
    $rep = new FrontReport(_('Attendance Inquiry Sheet'), "MonthlySalarySheet", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);

    $rep->NewPage();

	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];


   	$rep->NewLine(1);


// $sqll = "SELECT id, description FROM ".TB_PREF."month ";
	//if ($month_name != ALL_TEXT)
		//$sqll .= "WHERE id=".db_escape($month_name);
	//$sqll .= " ORDER BY id";
	//$result2 = db_query($sqll, "The customers could not be retrieved");

//	$sql = "SELECT id, description FROM ".TB_PREF."dept ";
//	//if ($dept != ALL_TEXT)
//		//$sql .= "WHERE id=".db_escape($dept);
//	$sql .= " ORDER BY description";
//	$result1 = db_query($sql, "The customers could not be retrieved");

	//$num_lines = 0;

//while ($myrow2 = db_fetch($result2))
//{

		//if (db_num_rows($result) == 0) continue;

	$rep->fontSize += 2;
    	$rep->Font(b);
	//$rep->TextCol(0, 5, $myrow2['description']);
    	$rep->Font();
	$rep->fontSize -= 2;
    	$rep->NewLine();
//$result = getTransactions($myrow2['id'],$dept,$employee);

	//while ($myrow1 = db_fetch($result1))
//{
	
		//if (db_num_rows($result) == 0) continue;

	//$rep->fontSize += 2;
    //	$rep->Font(b);
	//$rep->TextCol(0, 5, $myrow1['description']);
    //	$rep->Font();
	//$rep->fontSize -= 2;
    //	$rep->NewLine();

	function get_designation_names($id)
	{
		$sql="SELECT description FROM 0_desg where id=".db_escape($id)." ";
		$db = db_query($sql,'Can not get Designation name');
		$ft = db_fetch($db);
		return $ft[0];
	}

    function get_employee_namee_new($employee_id)
    {

        $sql = "SELECT ".TB_PREF."employee.emp_name FROM ".TB_PREF."employee 
	WHERE employee_id=".db_escape($employee_id);
        $result = db_query($sql, "Could't get employee name");
        $myrow = db_fetch($result);
        return $myrow['0'];
    }

    function get_advance_name_neww($emp_id)
    {
        $sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id = ".db_escape($emp_id);
        $result = db_query($sql, "could not get group");
        $row = db_fetch($result);
        return $row[0];
    }

    function getsum($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.01_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.01_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum1($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.02_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.02_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum3($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.03_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.03_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum4($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.04_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.04_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }function getsum5($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.05_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.05_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum6($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.06_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.06_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum7($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.07_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.07_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum8($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.08_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.08_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum9($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.09_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.09_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum10($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.10_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.10_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
    function getsum11($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.11_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.11_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum12($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.12_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.12_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum13($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.13_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.13_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum14($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.14_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.14_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum15($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.15_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.15_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum16($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.16_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.16_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }function getsum17($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.17_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.17_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}function getsum18($empl_id)
{

    $sql ="SELECT COUNT(".TB_PREF."emp_attendance.18_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.18_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

    $result = db_query($sql, "Could not get employees.");
    $myrow = db_fetch_row($result);
    return $myrow[0];
}
    function getsum19($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.19_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.19_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum20($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.20_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.20_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }
    function getsum21($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.21_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.21_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }


    function getsum22($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.22_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.22_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }


    function getsum23($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.23_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.23_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum24($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.24_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.24_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum25($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.25_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.25_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum26($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.26_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.26_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum27($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.27_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.27_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum28($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.28_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.28_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum29($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.29_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.29_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum30($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.30_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.30_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    function getsum31($empl_id)
    {

        $sql ="SELECT COUNT(".TB_PREF."emp_attendance.31_type) FROM ".TB_PREF."emp_attendance

        WHERE ".TB_PREF."emp_attendance.31_type in (3,4,5,2)
         AND ".TB_PREF."emp_attendance.empl_id=".db_escape($empl_id)."";

        $result = db_query($sql, "Could not get employees.");
        $myrow = db_fetch_row($result);
        return $myrow[0];
    }

    $result = getTransactions($divison,$project,$location,$employee,$month);
    $d = date("d",strtotime($end));

	while ($myrow=db_fetch($result))
	{
	//if ($no_zeros && db_num_rows($res) == 0) continue;

//		$rep->fontSize += 2;
//        $rep->NewPage();
//		$NetSalary = $myrow['basic_salary'] - $myrow['advance_deduction'] - $myrow['late_deduction'] - $myrow['tax'] + $myrow['Overtime'] ;
		//$SerialNo += 1;
		//$rep->TextCol(0, 1, get_emp_dept_name($myrow['dept_id']));
//		$emp = get_employe_name($myrow['emp_id']);
		$rep->TextCol(0, 1, $myrow['empl_id']);
		$rep->TextCol (1, 2,get_employee_namee_new($myrow['empl_id'])  );

        $ex = explode('-', $myrow['Record01']);
        $leave_name= get_advance_name_neww($ex[0]);
        $myrow['Record01'] = $leave_name.''.$ex[1];


        $ex2 = explode('-', $myrow['Record02']);
        $leave_name= get_advance_name_neww($ex2[0]);
        $myrow['Record02'] = $leave_name.''.$ex2[1];


        $ex3 = explode('-', $myrow['Record03']);
        $leave_name= get_advance_name_neww($ex3[0]);
        $myrow['Record03'] = $leave_name.''.$ex3[1];


        $ex4 = explode('-', $myrow['Record04']);
        $leave_name= get_advance_name_neww($ex4[0]);
        $myrow['Record04'] = $leave_name.''.$ex4[1];

        $ex5 = explode('-', $myrow['Record05']);
        $leave_name= get_advance_name_neww($ex5[0]);
        $myrow['Record05'] = $leave_name.''.$ex5[1];

        $ex6 = explode('-', $myrow['Record06']);
        $leave_name= get_advance_name_neww($ex6[0]);
        $myrow['Record06'] = $leave_name.''.$ex6[1];

        $ex7 = explode('-', $myrow['Record07']);
        $leave_name= get_advance_name_neww($ex7[0]);
        $myrow['Record07'] = $leave_name.''.$ex7[1];

        $ex8 = explode('-', $myrow['Record08']);
        $leave_name= get_advance_name_neww($ex8[0]);
        $myrow['Record08'] = $leave_name.''.$ex8[1];

        $ex9 = explode('-', $myrow['Record09']);
        $leave_name= get_advance_name_neww($ex9[0]);
        $myrow['Record09'] = $leave_name.''.$ex9[1];

        $ex10 = explode('-', $myrow['Record10']);
        $leave_name= get_advance_name_neww($ex10[0]);
        $myrow['Record10'] = $leave_name.''.$ex10[1];


        $ex11 = explode('-', $myrow['Record11']);
        $leave_name= get_advance_name_neww($ex11[0]);
        $myrow['Record11'] = $leave_name.''.$ex11[1];

        $ex12 = explode('-', $myrow['Record12']);
        $leave_name= get_advance_name_neww($ex12[0]);
        $myrow['Record12'] = $leave_name.''.$ex12[1];


        $ex13 = explode('-', $myrow['Record13']);
        $leave_name= get_advance_name_neww($ex13[0]);
        $myrow['Record13'] = $leave_name.''.$ex13[1];


        $ex14 = explode('-', $myrow['Record14']);
        $leave_name= get_advance_name_neww($ex14[0]);
        $myrow['Record14'] = $leave_name.''.$ex14[1];

        $ex15 = explode('-', $myrow['Record15']);
        $leave_name= get_advance_name_neww($ex15[0]);
        $myrow['Record15'] = $leave_name.''.$ex15[1];

        $ex16 = explode('-', $myrow['Record16']);
        $leave_name= get_advance_name_neww($ex16[0]);
        $myrow['Record16'] = $leave_name.''.$ex16[1];

        $ex17 = explode('-', $myrow['Record17']);
        $leave_name= get_advance_name_neww($ex17[0]);
        $myrow['Record17'] = $leave_name.'<br>'.$ex17[1];

        $ex18 = explode('-', $myrow['Record18']);
        $leave_name= get_advance_name_neww($ex18[0]);
        $myrow['Record18'] = $leave_name.''.$ex18[1];

        $ex19 = explode('-', $myrow['Record19']);
        $leave_name= get_advance_name_neww($ex19[0]);
        $myrow['Record19'] = $leave_name.''.$ex19[1];

        $ex20 = explode('-', $myrow['Record20']);
        $leave_name= get_advance_name_neww($ex20[0]);
        $myrow['Record20'] = $leave_name.''.$ex20[1];

        $ex21 = explode('-', $myrow['Record21']);
        $leave_name= get_advance_name_neww($ex21[0]);
        $myrow['Record21'] = $leave_name.''.$ex21[1];

        $ex22 = explode('-', $myrow['Record22']);
        $leave_name= get_advance_name_neww($ex22[0]);
        $myrow['Record22'] = $leave_name.''.$ex22[1];


        $ex23 = explode('-', $myrow['Record23']);
        $leave_name= get_advance_name_neww($ex3[0]);
        $myrow['Record23'] = $leave_name.''.$ex23[1];


        $ex24 = explode('-', $myrow['Record24']);
        $leave_name= get_advance_name_neww($ex24[0]);
        $myrow['Record24'] = $leave_name.''.$ex24[1];

        $ex25 = explode('-', $myrow['Record25']);
        $leave_name= get_advance_name_neww($ex25[0]);
        $myrow['Record25'] = $leave_name.''.$ex25[1];

        $ex26 = explode('-', $myrow['Record26']);
        $leave_name= get_advance_name_neww($ex26[0]);
        $myrow['Record26'] = $leave_name.''.$ex26[1];

        $ex27 = explode('-', $myrow['Record27']);
        $leave_name= get_advance_name_neww($ex27[0]);
        $myrow['Record27'] = $leave_name.''.$ex27[1];

        $ex28 = explode('-', $myrow['Record28']);
        $leave_name= get_advance_name_neww($ex28[0]);
        $myrow['Record28'] = $leave_name.''.$ex28[1];

        $ex29 = explode('-', $myrow['Record29']);
        $leave_name= get_advance_name_neww($ex29[0]);
        $myrow['Record29'] = $leave_name.''.$ex29[1];

        $ex30 = explode('-', $myrow['Record30']);
        $leave_name= get_advance_name_neww($ex30[0]);
        $myrow['Record30'] = $leave_name.''.$ex30[1];

        $ex31 = explode('-', $myrow['Record31']);
        $leave_name= get_advance_name_neww($ex31[0]);
        $myrow['Record31'] = $leave_name.''.$ex31[1];

        $rep->TextCol (2, 3,
            getsum($myrow['empl_id'])
            +getsum1($myrow['empl_id'])
            +getsum3($myrow['empl_id'])
            +getsum4($myrow['empl_id'])
            +getsum5($myrow['empl_id'])
            +getsum6($myrow['empl_id'])
            +getsum7($myrow['empl_id'])
            +getsum8($myrow['empl_id'])
            +getsum9($myrow['empl_id'])
            +getsum10($myrow['empl_id'])
            +getsum11($myrow['empl_id'])
            +getsum12($myrow['empl_id'])
            +getsum13($myrow['empl_id'])
            +getsum14($myrow['empl_id'])
            +getsum15($myrow['empl_id'])
            +getsum16($myrow['empl_id'])
            +getsum17($myrow['empl_id'])
            +getsum18($myrow['empl_id'])
            +getsum19($myrow['empl_id'])
            +getsum20($myrow['empl_id'])
            +getsum21($myrow['empl_id'])
            +getsum22($myrow['empl_id'])
            +getsum23($myrow['empl_id'])
            +getsum24($myrow['empl_id'])
            +getsum25($myrow['empl_id'])
            +getsum26($myrow['empl_id'])
            +getsum27($myrow['empl_id'])
            +getsum28($myrow['empl_id'])
            +getsum29($myrow['empl_id'])
            +getsum30($myrow['empl_id'])
            +getsum31($myrow['empl_id']));

        $rep->TextCol (3, 4, $d);

//
        $rep->TextCol (4, 5, $myrow['Record01']);
        $rep->TextCol (5, 6, $myrow['Record02']);
        $rep->TextCol (6, 7, $myrow['Record03']);
        $rep->TextCol (7, 8, $myrow['Record04']);
        $rep->TextCol (8, 9, $myrow['Record05']);
        $rep->TextCol (9, 10, $myrow['Record06']);
        $rep->TextCol (10, 11, $myrow['Record07']);
        $rep->TextCol (11, 12, $myrow['Record08']);
        $rep->TextCol (13, 14, $myrow['Record09']);
        $rep->TextCol (14, 15, $myrow['Record10']);
        $rep->TextCol (15, 16, $myrow['Record11']);
        $rep->TextCol (16, 17, $myrow['Record12']);
        $rep->TextCol (17, 18, $myrow['Record13']);
        $rep->TextCol (18, 19, $myrow['Record14']);
        $rep->TextCol (19, 20, $myrow['Record15']);
        $rep->TextCol (20, 21, $myrow['Record16']);
        $rep->TextCol (21, 22, $myrow['Record17']);
        $rep->TextCol (22, 23, $myrow['Record18']);
        $rep->TextCol (23, 24, $myrow['Record19']);
        $rep->TextCol (24, 25, $myrow['Record20']);
        $rep->TextCol (25, 26, $myrow['Record21']);
        $rep->TextCol (26, 27, $myrow['Record22']);
        $rep->TextCol (27, 28, $myrow['Record23']);
        $rep->TextCol (28, 29, $myrow['Record24']);
        $rep->TextCol (29, 30, $myrow['Record25']);
        $rep->TextCol (30, 31, $myrow['Record26']);
        $rep->TextCol (31, 32, $myrow['Record27']);
        $rep->TextCol (32, 33, $myrow['Record28']);
        $rep->TextCol (33, 34, $myrow['Record29']);
//        $rep->TextCol (34, 35, $myrow['Record30']);
//        $rep->TextCol (35, 36, $myrow['Record31']);


//	    $rep->AmountCol(3, 4, $myrow['basic_salary'], $dec);
//		$rep->AmountCol(4, 5, $myrow['absent']);
//		$rep->TextCol(5, 6, $myrow['leave']);
//		$rep->TextCol(6, 7, $myrow['Over_time_hour']);
//		$rep->AmountCol(7, 8, $myrow['Overtime'], $dec);
//		$rep->AmountCol(8, 9, $myrow['tax'], $dec);
//		$rep->AmountCol(9, 10, $myrow['tax_rate'], $dec);
//		$rep->AmountCol(10, 11, $myrow['late_deduction'], $dec);
//		$rep->AmountCol(11, 12, $myrow['advance_deduction'], $dec);
//		$rep->AmountCol(12, 13, $myrow['allowance'], $dec);
//		$rep->AmountCol(13, 14, $myrow['deduction'], $dec);
//		$rep->AmountCol(14, 15, $NetSalary, $dec);

//		$TotalBasicSalary += $myrow['basic_salary'];
//		$TotalAdvance += $myrow['Advance'];
//		$TotalPresent += $myrow['absent'];
//		$TotalLeave += $myrow['leave'];
//		//$TotalAbsent += $myrow['absent'];
//		$Totaldutyhours += $myrow['Over_time_hour'];
//		$TotalOver_Time += $myrow['overtime'];
//		$TotalTax += $myrow['tax'];
//		$TotalTax_rate += $myrow['tax_rate'];
//		$TotalLateDeduction += $myrow['late_deduction'];
//		$TotalAdvDeduction += $myrow['advance_deduction'];
//		$TotalEmpAllowances += $myrow['allowance'];
//		$TotalEmpDeductions += $myrow['deduction'];
//		$TotalNetSalary += $NetSalary;
//
//		$GrandBasicSalary += $myrow['basic_salary'];
//		$GrandAdvance += $myrow['Advance'];
//		$GrandPresent += $myrow['absent'];
//		$GrandLeave += $myrow['leave'];
//		//$GrandAbsent += $myrow['absent'];
//		$Granddutyhours += $myrow['Over_time_hour'];
//		$GrandOver_Time += $myrow['overtime'];
//		$GrandTax += $myrow['tax'];
//		$GrandTax_rate += $myrow['tax_rate'];
//		$GrandLateDeduction += $myrow['late_deduction'];
//		$GrandAdvDeduction += $myrow['advance_deduction'];
//		$GrandEmpAllowances += $myrow['allowance'];
//		$GrandEmpDeductions += $myrow['deduction'];
//		$GrandNetSalary += $NetSalary;



    	$rep->NewLine();

	}
//
//	$rep->Font(b);
//		$rep->TextCol(0, 15, _('Total'));
//
//		$rep->AmountCol(2, 3, $TotalBasicSalary, $dec);
//		$rep->AmountCol(3, 4, $TotalAdvance, 0);
//		$rep->AmountCol(4, 5, $TotalPresent, 0);
//		$rep->AmountCol(5, 6, $TotalLeave, 0);
//		$rep->AmountCol(6, 7, $Totaldutyhours, $dec);
//		$rep->AmountCol(7, 8, $TotalOver_Time, $dec);
//		$rep->AmountCol(8, 9, $TotalTax, $dec);
//		$rep->AmountCol(9,10, $TotalTax_rate, $dec);
//		$rep->AmountCol(10, 11, $TotalLateDeduction, $dec);
//		$rep->AmountCol(11, 12, $TotalAdvDeduction, $dec);
//		$rep->AmountCol(12, 13, $TotalEmpAllowances, $dec);
//		$rep->AmountCol(13, 14, $TotalEmpDeductions, $dec);
//		$rep->AmountCol(14, 15, $TotalNetSalary, $dec);
//		$TotalBasicSalary = $TotalPresent = $TotalAbsent = $TotalOvertime  = $TotalOver_Time = $TotalTax = $TotalLateDeduction = $TotalAdvDeduction  = $TotalEmpAllowances = $TotalEmpDeductions = $TotalNetSalary = 0;
//	$rep->Font();
//    	$rep->NewLine(2);
////}
////}
//
//	$rep->Line($rep->row  - 4);
//
//   	$rep->NewLine(2);
//	$rep->Font(b);
//	$rep->fontSize += 2;
//	$rep->TextCol(0, 3,	_('Grand Total'));
//		$rep->AmountCol(2, 3, $GrandBasicSalary, $dec);
//		$rep->AmountCol(3, 4, $GrandAdvance, 0);
//		$rep->AmountCol(4, 5, $GrandPresent, 0);
//		$rep->AmountCol(5, 6, $GrandLeave, 0);
//		$rep->AmountCol(6, 7, $Granddutyhours, $dec);
//		$rep->AmountCol(7, 8, $GrandOver_Time, $dec);
//		$rep->AmountCol(8, 9, $GrandTax, $dec);
//		$rep->AmountCol(9, 10, $GrandTax_rate, $dec);
//		$rep->AmountCol(10, 11, $GrandLateDeduction, $dec);
//		$rep->AmountCol(11, 12, $GrandAdvDeduction, $dec);
//		$rep->AmountCol(12, 13, $GrandEmpAllowances, $dec);
//		$rep->AmountCol(13, 14, $GrandEmpDeductions, $dec);
//	$rep->AmountCol(14, 15,	$GrandNetSalary, $dec);
//	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>