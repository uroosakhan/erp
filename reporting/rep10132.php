<?php
$page_security = 'SA_CUSTPAYMREP';

// ----------------------------------------------------------------
// $ Revision:	2.0 $
// Creator:	Joe Hunt
// date_:	2005-05-19
// Title:	Customer Balances
// ----------------------------------------------------------------
$path_to_root="..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/gl/includes/gl_db.inc");
include_once($path_to_root . "/sales/includes/db/customers_db.inc");
include_once($path_to_root . "/sales/includes/db/query_status_db.inc");
include_once($path_to_root . "/sales/includes/db/source_status_db.inc");

//include_once($path_to_root . "/sales/inquiry/query_inquiry.php");
include_once($path_to_root . "/admin/db/users_db.inc");

//----------------------------------------------------------------------------------------------------

// trial_inquiry_controls();
print_customer_balances();

function get_status_name($id)
{
	$sql = "SELECT status FROM ".TB_PREF."pstatus WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
//
function get_plan_time($id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_plan_minutes($id)
{
	$sql = "SELECT duration FROM ".TB_PREF."duration WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
///
function get_call_type($id)
{
	$sql = "SELECT call_type FROM ".TB_PREF."call_type WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_user_name_task($user_id)
{
    $sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}
function get_customer_name_n($customer_id)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}

function get_transactions($from, $to, $stas,$sour,$usr)
      {
	  $from = date2sql($from);
          $to = date2sql($to);
$sql = "SELECT *
FROM 0_task
WHERE 
".TB_PREF."task.`start_date`>='$from'
AND
".TB_PREF."task.`start_date`<='$to'
";
//AND 0_task.task_type = 0
/*if($stas != ALL_TEXT)
$sql .= " AND ".TB_PREF."query_status.description=".db_escape($stas);

if($sour != ALL_TEXT)
 $sql .= " AND ".TB_PREF."source_status.description=".db_escape($sour);
*/
if($usr != ALL_TEXT)
 $sql .= " AND ".TB_PREF."task.user_id=".db_escape($usr);
		$result = db_query($sql, "The query could not be retrieved");
		return $result;
	$num_lines = 0;
	}


//----------------------------------------------------------------------------------------------------

function print_customer_balances()
{
    	global $path_to_root, $systypes_array;

    	$from = $_POST['PARAM_0'];
    	$to = $_POST['PARAM_1'];
		$status = $_POST['PARAM_2'];
    	$source = $_POST['PARAM_3'];
    	$user = $_POST['PARAM_4'];
    	$comments = $_POST['PARAM_5'];
	$orientation = $_POST['PARAM_6'];
	$destination = $_POST['PARAM_7'];
	if ($destination)
		include_once($path_to_root . "/reporting/includes/excel_report.inc");
	else
		include_once($path_to_root . "/reporting/includes/pdf_report.inc");

	$orientation = ($orientation ? 'L' : 'P');
	
	if ($status == ALL_TEXT)
		$stas = _('');
	else
		$stas = get_description($status);

		
	if ($source == ALL_TEXT)
		$sour = _('');
	else
		$sour = get_description1($source);

    if ($user == ALL_TEXT)
		$usr = _('');
	else
		$usr = get_user_name($user);


	$cols = array(0, 75, 150, 250, 310, 390,435, 480, 600 );

	$headers = array(_('Date'), _('Client'), _('Work Description'), _('Forward To'), _(''), _('P.Time'), _('A.Time'),_('Status'),_(''));

	$aligns = array('left',	'left',	'left',	'left',	'left', 'left', 'left', 'left', 'left', 'left','left','left','right');

    $params =   array( 	0=> $comments,
    				    1 => array('text' => _('Period'), 'from' => $from, 		'to' => $to),
    				    //2 => array('text' => _('customer'), 'from' => $cust,   	'to' => ''),
						2 => array('text' => _('status'), 'from' => $stas,   	'to' => ''),
						3 => array('text' => _('source'), 'from' => $sour,   	'to' => ''),
						4 => array('text' => _('user'), 'from' => $usr,   	'to' => ''),
    				    //6 => array('text' => _('Currency'), 'from' => $currency, 'to' => ''),
						//7 => array('text' => _('Suppress Zeros'), 'from' => $nozeros, 'to' => '')
						);

    $rep = new FrontReport(_('Employee Monthly Performance Report'), "CustomerBalances", user_pagesize(), 9, $orientation);
    if ($orientation == 'L')
    	recalculate_cols($cols);
    $rep->Font();
    $rep->Info($params, $cols, $headers, $aligns);
    $rep->NewPage();

	$grandtotal = array(0,0,0,0);
	
		$sql = "SELECT id, real_name FROM ".TB_PREF."users WHERE inactive=0";
	if ($user != ALL_TEXT)
		$sql .= " AND id=".db_escape($user);
	$sql .= " ORDER BY real_name";
	$res = db_query($sql, "The customers could not be retrieved");
	
	while ($myrow1 = db_fetch($res))
	{
	    	$rep->fontSize += 2;
		  $rep->TextCol(0, 8, $myrow1['real_name']);
			$rep->fontSize -= 2;
		
	$result=get_transactions($from, $to, $stas,$sour,$myrow1['id']);
$rep->NewLine();
	while ($myrow = db_fetch($result))
	{
		

		$num_lines++;
	
		if($myrow['debtor_no']==503)
		{
$rep->SetDrawColor(50, 0, 0, 0);
$rep->SetFillColor(100, 0, 0, 0);
$rep->SetTextColor(100, 0, 0, 0);
	$rep->Font('bold');
		$rep->TextCol(0, 1, sql2date($myrow['start_date']));
		$rep->Font('');
		    $rep->TextCol(1, 3, $myrow['description']);
		    $rep->TextCol(7, 8, get_call_type($myrow['call_type']));
		    
		    $rep->TextCol(3, 4, ($myrow['other_cust']));
		     $rep->TextCol(4, 5, ($myrow['contact_no']));
		     $rep->TextCol(5, 6, get_user_name_task($myrow['user_id']));
		     $rep->SetDrawColor();
$rep->SetFillColor();
$rep->SetTextColor();
		     
		}
		elseif($myrow['user_id']==23)
		{
		    $rep->TextCol(1, 3, $myrow['description']);
		    $rep->TextCol(5, 6, "  ".($myrow['time']));
		    $rep->TextCol(4, 5, ($myrow['contact_no']));
		}
		else
		{  
		    if($myrow['status']==1)
		    {
$rep->SetDrawColor(127, 255, 127);
$rep->SetFillColor(0, 255, 0);
$rep->SetTextColor(0, 255, 0);
		    $rep->Font('bold');
		    	$rep->TextCol(0, 1, sql2date($myrow['start_date']));
		    $rep->TextCol(1, 3, get_customer_name_n($myrow['debtor_no']));
		    $rep->Font('');
		    $rep->TextCol(5, 6, get_plan_time($myrow['plan'])."-".get_plan_minutes($myrow['plan1']));
		
		$rep->TextCol(6, 7, get_plan_time($myrow['actual'])."-".get_plan_minutes($myrow['actual1']));
		$rep->TextCol(7, 8, get_status_name($myrow['status']));
		 $rep->SetDrawColor();
$rep->SetFillColor();
$rep->SetTextColor();
}
else
{
$rep->SetDrawColor(0, 50, 0, 0);
$rep->SetFillColor(0, 100, 0, 0);
$rep->SetTextColor(0, 100, 0, 0);
    $rep->Font('bold');
		    	$rep->TextCol(0, 1, sql2date($myrow['start_date']));
		    $rep->TextCol(1, 3, get_customer_name_n($myrow['debtor_no']));
		    $rep->Font('');
		    $rep->TextCol(5, 6, get_plan_time($myrow['plan'])."-".get_plan_minutes($myrow['plan1']));
	      	$rep->TextCol(6, 7, get_plan_time($myrow['actual'])."-".get_plan_minutes($myrow['actual1']));
		$rep->TextCol(7, 8, get_status_name($myrow['status']));
		  $rep->SetDrawColor();
$rep->SetFillColor();
$rep->SetTextColor();
}
		}
		
			$rep->NewLine(+1);
		$rep->NewLine(+1);
		if($myrow['debtor_no']==503)
		{
			$rep->TextColLines(0, 7, $myrow['remarks']);
		}
		else
		{
		$rep->TextColLines(0, 7, $myrow['description']);
		}
	$rep->Line($rep->row + 4);
			$rep->NewLine();
	}
		
	}

	$rep->NewLine();
    	$rep->End();
}

?>