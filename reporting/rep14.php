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

function getTransactions($from, $to,$employee,$division,$project,$location,$approve)
{
	$fromdate = date2sql($from);
	$todate = date2sql($to);
	$sql = "Select * ,lea.emp_id
FROM  ".TB_PREF."leave AS lea,
".TB_PREF."employee AS emp
WHERE  
	 lea.emp_id = emp.employee_id";

//if ($approve != ALL_TEXT)
//$sql .= " AND lea.approve =".db_escape($approve);
	if ($division != 0)
		$sql .= " AND emp.division =".db_escape($division);
	if ($project != 0)
		$sql .= " AND emp.project =".db_escape($project);
	if ($location != 0)
		$sql .= " AND emp.location =".db_escape($location);
	$sql .= " AND lea.from_date >=".db_escape($fromdate);
	$sql .= " AND lea.from_date <=".db_escape($todate);

	if ($employee !=0)
$sql .= " AND lea.emp_id = ".db_escape($employee);


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
function get_leave_type($id)
{
	$sql = "SELECT description FROM ".TB_PREF."leave_type WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get supplier");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_employeee($employee_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee WHERE employee_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get employee");

    return $result;
}

function get_leave($employee_id)
{
    $sql = "SELECT * FROM ".TB_PREF."leave WHERE emp_id=".db_escape($employee_id);

    $result = db_query($sql, "could not get employee");

    return $result;
}

//----------------------------------------------------------------------------------------------------

function print_employee_balances()
{
    	global $path_to_root, $systypes_array;
        $from = $_POST['PARAM_0'];
	    $to = $_POST['PARAM_1'];
    	//$approve = $_POST['PARAM_2'];
    	//$dept = $_POST['PARAM_2'];
		$employee = $_POST['PARAM_2'];
		$division = $_POST['PARAM_3'];
		$project = $_POST['PARAM_4'];
		$location = $_POST['PARAM_5'];
    	$comments = $_POST['PARAM_6'];
	    $orientation = $_POST['PARAM_7'];
	    $destination = $_POST['PARAM_8'];
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

if ($month_name == ALL_TEXT)
		$month = _('All');
	else
		$month = get_month_name($month_name);
	
	if ($dept == ALL_TEXT)
		$dept_name = _('All');
	else
		$dept_name = get_emp_dept_name($dept);	
			
  if ($employee == ALL_TEXT)
		$emp = _('All');
	else
		$emp = get_employee_name11($employee);	
    	$dec = user_price_dec();

//	if($division!=0)
//		$sql .= " AND division = ".db_escape($division);
//
//	if($project!=0)
//		$sql .= " AND project_name = ".db_escape($project);
//
//	if($location!=0)
//		$sql .= " AND location = ".db_escape($location);
//$month_name = get_month_name($month);

	if ($approve) $approval = _('Approve');
	else $approval = _('Unapprove');

	$cols = array(0, 100, 200, 300,400,500);

	$headers = array(_('Leave Type'), _('Approved By'), _('Date From'), _('Date To'), _('No Of Days'), _('Comp.Date'));

	$aligns = array('left', 'left', 'left', 'left','left', 'right');

//    $params =   array( 	0 => $comments,
//				1 => array('text' => _('Period'), 'from' => $from, 'to' => $to),
//    			//2 => array('text' => _('Department'), 'from' => $dept_name, 'to' => $to),
//    			2 => array('text' => _('Employee'), 'from' => $emp, 'to' => $to),
//				3 => array('text' => _('Approval'), 'from' => $approval, 'to' => $to)
//
//			);
$orientation = 'L';
    $rep = new FrontReport(_('Leave Report'), "LeaveReport", user_pagesize(), 10, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);

			$rep->SetHeaderType('Header14');
			$rep->Info($params, $cols, $headers, $aligns);

    $rep->Font();
  //  $rep->Info($params, $cols, $headers, $aligns);


	$bank_account_details = get_bank_account($banks);
	$bank_account_no = $bank_account_details['bank_account_number'];
	$bank_address = $bank_account_details['bank_address'];

   	$rep->NewLine(1);

 /*$sqll = "SELECT id, description FROM ".TB_PREF."month ";
	if ($month_name != ALL_TEXT)
		$sqll .= "WHERE id=".db_escape($month_name);
	$sqll .= " ORDER BY id";
	$result2 = db_query($sqll, "The customers could not be retrieved");
*/
    $sql="SELECT * FROM ".TB_PREF."employee ";
    if ($employee != '')
        $sql .= " where employee_id=".db_escape($employee);
    $result = db_query($sql, "Error");

    $logo = company_path() . "/	images/" . 1;

    if ($rep->company['coy_logo'] != '' && file_exists($logo))
    {


    }



//    $emp=get_employeee($employee);
    function get_desg1($id)
    {
        $sql = "SELECT description FROM ".TB_PREF."desg WHERE id=".db_escape($id);

        $result = db_query($sql, "could not get supplier");

        $row = db_fetch_row($result);

        return $row[0];
    }
    function get_project($id)
    {
        $sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($id);

        $result = db_query($sql, "could not get supplier");

        $row = db_fetch_row($result);

        return $row[0];
    }
    while($row = db_fetch($result))
    {

	//$num_lines = 0;

//        $incre =get_employeee($employee);

//    while($row = db_fetch($incre))
//    {

        $rep->NewPage();

        $rep->MultiCell(50, 50, "Project: ",0, 'L', 0, 2, 30,100, true);

        $rep->MultiCell(400, 650, "" ,0, 'L', 0, 2, 85,110, true);

        $rep->MultiCell(100, 50, "Name: " ,0, 'L', 0, 2, 30,150, true);
        $rep->MultiCell(500, 50, "" ,0, 'L', 0, 2, 85,150, true);
        $rep->MultiCell(100, 50, "Designation: " ,0, 'L', 0, 2, 30,200, true);
        $rep->MultiCell(500, 650, "" ,0, 'L', 0, 2, 110,200, true);
        $rep->MultiCell(100, 50, "D.O.J: " ,0, 'L', 0, 2, 30,250, true);
        $rep->MultiCell(500, 650, "" ,0, 'L', 0, 2, 110,260, true);



        $rep->MultiCell(400, 650, "".get_project($row['project']) ,0, 'L', 0, 2, 85,100, true);
        $rep->MultiCell(500, 50, "".$row['emp_name'] ,0, 'L', 0, 2, 85,150, true);
        $rep->MultiCell(500, 650, "" .get_desg1($row['emp_desig']),0, 'L', 0, 2, 110,200, true);
        $rep->MultiCell(500, 650, "" .$row['j_date'],0, 'L', 0, 2, 110,250, true);



        //==
        $leave=get_leave($row['employee_id']);
        while ($myrow1 = db_fetch($leave))
        {

            //if (db_num_rows($result) == 0) continue;

            $rep->fontSize += 2;
            $rep->Font();
            $rep->TextCol(0, 1,get_leave_type($myrow1['leave_type']) );


            if($myrow1['approve']==1){
                $approve= "yes";

            }
            else{

                $approve ="No";

            }

            $rep->TextCol(1, 2, $approve);
            $rep->TextCol(2, 3, sql2date($myrow1['from_date']));
            $rep->TextCol(3, 4, sql2date($myrow1['to_date']));
            $rep->TextCol(4, 5, $myrow1['no_of_leave']);
            $rep->TextCol(5, 6, $myrow1['']);


            $rep->Font();
            $rep->fontSize -= 2;
            $rep->Font(b);
            $rep->NewLine();
            //$rep->TextCol(2,3, get_emp_dept_name($myrow1['emp_dept']));
            $rep->Font();

            $rep->Font();
            $rep->NewLine(2);
        }


    }

	$rep->Line($rep->row  - 4);

   	$rep->NewLine(2);
	$rep->Font(b);
	$rep->fontSize += 2;

	$rep->fontSize -= 2;
	$rep->Font();

	$rep->NewLine();
    $rep->End();
}

?>