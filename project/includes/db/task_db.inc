<?php
//saad 17 may
function add_task1($start_date, $end_date, $description,
	$debtor_no,$branch_id,$task_type,$call_type, $contact_no, $other_cust,$status,$user_id,
				   $plan,$plan1,$actual,$actual1, $remarks, $time,$priority,$progress,$customer_, $amount)
{
	$start_date_db = date2sql($start_date);
	$end_date_db = date2sql($end_date);
	
	$sql = "INSERT INTO ".TB_PREF."task (start_date, end_date, description, debtor_no,branch_id,task_type,call_type,contact_no,other_cust,
		status,user_id,assign_by,plan,plan1,actual,actual1, remarks, Stamp, time, priority,progress,customer_,amount)
		VALUES (".db_escape($start_date_db) . ", "
				 .db_escape($end_date_db) . ", "
				 .db_escape($description) . ", "
				 .db_escape($debtor_no) . ", "
				  .db_escape($branch_id) . ", "
				 .db_escape($task_type) . ", "
				 .db_escape($call_type) . ", "
				 .db_escape($contact_no) . ", "
				 .db_escape($other_cust) . ", "
				 .db_escape($status).", "
				 .db_escape($user_id).", "
		         .db_escape($_SESSION['wa_current_user']->user).", "
				 .db_escape($plan).", "
				 .db_escape($plan1).", "
				 .db_escape($actual).", "
				 .db_escape($actual1).", "
				 .db_escape($remarks).", "
				 .db_escape(date("Y-m-d H:i:s")).", "
				 .db_escape($time).","
				 .db_escape($priority).","
				 .db_escape($priority).","
				 .db_escape($customer_).","
				 .db_escape($amount).")";
   	db_query($sql, "The insert of the task failed");
	
}
function update_setting( $name,$value)
{
	///$sql = " {$pref}settings SET value = ";
	//foreach($params as $name => $value) {
		//if (!db_query($sql. db_escape($value). " WHERE name=".db_escape($name),
			//"The company prefferences could not be updated "))
			//return false;
		// update cached value
		//$_SESSION['SysPrefs']->prefs[$name] = $value;
	//}


	$sql = "UPDATE ".TB_PREF."settings SET 
		value=".db_escape($value)."
		WHERE name = ".db_escape($name);
	db_query($sql,"The update of the task failed");

}
function get_setting($tbpref = TB_PREF)
{
	return get_settings(null, $tbpref);
}
function get_settings($prefs = null, $tbpref = null)
{
	global $SysPrefs, $db_version;

	static $cached; // retrieve values from db once a request. Some values can't be cached between requests
	// to ensure prefs integrity for all usrs (e.g. gl_close_date).

	if (!$cached || !isset($_SESSION['SysPrefs'])) { // cached preferences

		$_SESSION['SysPrefs'] = new sys_prefs();

		if (!isset($tbpref))
			$tbpref = TB_PREF;

		$sql = "SELECT name, value FROM {$tbpref}settings";
		$result = @db_query($sql); // supress errors before 2.3 db structure upgrade

		if(!$result)
			return null;

		while($pref = db_fetch_assoc($result)) {
			$_SESSION['SysPrefs']->prefs[$pref['name']] = $pref['value'];
		}

		$SysPrefs = &$_SESSION['SysPrefs'];

		// update current db status for info in log file
		$SysPrefs->db_ok = $SysPrefs->prefs['version_id'] == $db_version;
		$cached = true;
	}

	$all = $_SESSION['SysPrefs']->prefs;

	if (!$prefs)
		return $all;
	elseif (is_string($prefs))
		return @$all[$prefs];

	$ret = array();
	foreach($prefs as $name)
		$ret[$name] = $all[$name];

	return $ret;
}
/////
function add_multiple($description,$plan,$task_type,$status)
{
	$sql = "INSERT INTO ".TB_PREF."multiple (description,plan,task_type,status) VALUES (".db_escape($description) . ",".db_escape($plan) . ",".db_escape($task_type) . ",".db_escape($status) . ")";
	db_query($sql,"The task could not be added");
}
function get_city_WWWWW($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."multiple";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get task");
}
function get_des_count($id)
{
	$sql = "SELECT COUNT(id) FROM ".TB_PREF."multiple ";

	$result = db_query($sql, "could not get city type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_des_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."multiple WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get city type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_plan2($id)
{
	$sql = "SELECT plan FROM " . TB_PREF . "multiple WHERE id=" . db_escape($id);

	$result = db_query($sql, "could not get city type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_task_type($id)
{
	$sql = "SELECT task_type FROM ".TB_PREF."multiple WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get city type");

	$row = db_fetch_row($result);
	return $row[0];
}
function get_status2($id)
{
	$sql = "SELECT status FROM ".TB_PREF."multiple WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get city type");

	$row = db_fetch_row($result);
	return $row[0];
}
function delete_task_multiple($selected_id)
{
	$sql="TRUNCATE TABLE  `0_multiple`";
	db_query($sql,"The task could not be deleted");
}
//////////////////////////
function add_task_history($task_id,$start_date, $end_date, $description,
				  $debtor_no,$branch_id,$task_type,$call_type,$contact_no,$other_cust, $status,$user_id,$assign_by,$plan,$plan1,$actual,$actual1, $remarks,$time, $customer_,$priority,$progress, $entry_user, $approved, $deleted, $amount)
{
	$start_date_db = date2sql($start_date);
	$end_date_db = date2sql($end_date);

	$sql = "INSERT INTO ".TB_PREF."task_history(task_id,start_date, end_date, description, debtor_no,branch_id,task_type,call_type,contact_no,other_cust,
		status,user_id,assign_by,plan,plan1,actual,actual1, remarks, Stamp, time, customer_,priority,progress, entry_user, approved, deleted, amount)
		VALUES (".db_escape($task_id) . ",".db_escape($start_date_db) . ", "
		.db_escape($end_date_db) . ", "
		.db_escape($description) . ", "
		.db_escape($debtor_no) . ", "
		.db_escape($branch_id) . ", "
		.db_escape($task_type) . ", "
		.db_escape($call_type) . ", "
		.db_escape($contact_no) . ", "
		.db_escape($other_cust) . ", "
		.db_escape($status).", "
		.db_escape($user_id).", "
		.db_escape($assign_by).", "
		.db_escape($plan).", "
		.db_escape($plan1).", "
		.db_escape($actual).", "
		.db_escape($actual1).", "
		.db_escape($remarks).", "
		.db_escape(date("Y-m-d H:i:s")).", "
		.db_escape($time)." ,"
        .db_escape($customer_).","
		.db_escape($priority)." ,"
		.db_escape($progress)." ,"
		.db_escape($_SESSION['wa_current_user']->user).", "
		.db_escape($approved).", "
		.db_escape($deleted).", "
		.db_escape($amount)."

)";

	db_query($sql, "The insert of the task failed");
}
function update_task($selected_id, $start_date, $end_date, $description,$debtor_no,$task_type,$call_type,$contact_no,$other_cust,
		$status, $user_id,$assign_by,$plan,$plan1,$actual,$actual1,$remarks,$time,$priority,$progress,$customer_,$amount)
{
	$start_date_db = date2sql($start_date);
	$end_date_db = date2sql($end_date);

		$sql = "UPDATE ".TB_PREF."task SET start_date=".db_escape($start_date_db) . ",
		end_date=".db_escape($end_date_db) . ",
		description=".db_escape($description) . ",
		debtor_no=".db_escape($debtor_no) . ",
		task_type=".db_escape($task_type) . ",
		call_type=".db_escape($call_type) . ",
		contact_no=".db_escape($contact_no) . ",
		other_cust=".db_escape($other_cust) . ",
		status=".db_escape($status).",
		user_id=".db_escape($user_id).", 
		assign_by=".db_escape($assign_by).", 
		plan=".db_escape($plan).", 
		plan1=".db_escape($plan1).", 
		actual=".db_escape($actual).", 
		actual1=".db_escape($actual1).", 
		remarks=".db_escape($remarks).",
		time=".db_escape($time).",
		priority=".db_escape($priority).",
		progress=".db_escape($progress).",
		customer_=".db_escape($customer_).",
		amount=".db_escape($amount)."
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The update of the task failed");
	return db_insert_id();
}
function update_history($selected_id, $start_date, $end_date, $description, $debtor_no,$task_type,
					 $status, $user_id,$assign_by,$plan,$actual,$remarks)
{
	$start_date_db = date2sql($start_date);
	$end_date_db = date2sql($end_date);

	$sql = "UPDATE ".TB_PREF."task SET start_date=".db_escape($start_date_db) . ",
		end_date=".db_escape($end_date_db) . ",
		description=".db_escape($description) . ",
		debtor_no=".db_escape($debtor_no) . ",
		task_type=".db_escape($task_type) . ",
		status=".db_escape($status).",
		user_id=".db_escape($user_id).", 
		assign_by=".db_escape($assign_by).", 
		plan=".db_escape($plan).", 
		actual=".db_escape($actual).", 
		remarks=".db_escape($remarks)."
		WHERE id = ".db_escape($selected_id);
	db_query($sql,"The update of the task failed");
	return db_insert_id();
}

function delete_task($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."task WHERE id=".db_escape($selected_id);
	db_query($sql,"The task could not be deleted");
}

function get_tasks($show_inactive)
{
	//$sql = "SELECT * FROM ".TB_PREF."task";
	if (!$show_inactive)
		$sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_task($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."task WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get task");
	return db_fetch($result);
}
function get_task_history_count($selected_id)
{
	$sql = "SELECT COUNT(id) AS task_count FROM ".TB_PREF."task_history WHERE task_id=".db_escape($selected_id)." ";
	$result = db_query($sql,"could not get history");
	$row = db_fetch($result);
	return $row[0];
}

function get_history1($selected_id,$allow_null=false)
{
	$sql = "SELECT *
  FROM ".TB_PREF."task_history WHERE task_id=".db_escape($selected_id)." 
	GROUP BY id
	ORDER BY Stamp ASC

";
	return db_query($sql,"could not get history");
}
function get_sett_($name,$allow_null=false)
{
	$sql = "SELECT value  FROM ".TB_PREF."settings WHERE name=".db_escape('task_tout')."";

	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);
	return $myrow[0];
}

function get_users_realname1($row)

{
	$sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($row);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_all_history($selected_id,$allow_null=false)
{
	$sql = "SELECT *  FROM ".TB_PREF."task_history ";

	return db_query($sql,"could not get history");
//	if (!$allow_null && db_num_rows($result) == 0)
//		display_db_error("Could not find history $selected_id", $sql);

	// db_fetch($result);
	//return db_fetch($result);
}

function get_task_description($id)
{
	$sql = "SELECT task_description FROM ".TB_PREF."task WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_task_max_id()
{
	$sql = "SELECT MAX(id) FROM ".TB_PREF."task ";

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}
//======================================================================================================================
function update_multiple($selected_id, $description,$plan,$task_type,$status)
{
	$sql = "UPDATE ".TB_PREF."multiple SET description=".db_escape($description).",
	plan=".db_escape($plan).",
	task_type=".db_escape($task_type).",
	status=".db_escape($status)."
    WHERE id = ".db_escape($selected_id);
	db_query($sql,"The task could not be updated");
}

function delete_multiple($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."multiple WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete task");
}
function get_multiple($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."multiple";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get task");
}
function get_multiple1($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."multiple WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get task");
	return db_fetch($result);
}
//======================================================================================================================
//calender
function get_users_name($row)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($row);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}function get_cust_name($row)
{
	$sql = "SELECT name FROM ".TB_PREF."debtors_master WHERE debtor_no =".db_escape($row);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch_row($result);

	return $row[0];
}
function get_sales_order_information($date)
{
	$sql = "SELECT `debtor_no`,`reference`,`fun_time` FROM `".TB_PREF."sales_orders` 
    WHERE `fun_date`='$date'";

	return db_query($sql,"could not retreive name for branch" );

	//$myrow = db_fetch_row($result);
// return $myrow[0];
}
function get_count_func($date)
{

	$sql = "SELECT ".TB_PREF."task.id as order_no,".TB_PREF."task.debtor_no,".TB_PREF."task.user_id,".TB_PREF."task.description,".TB_PREF."task.time,
	".TB_PREF."task_type.check_type, ".TB_PREF."task.customer_, ".TB_PREF."task.amount
	FROM ".TB_PREF."task ,".TB_PREF."task_type  WHERE ".TB_PREF."task_type.id = ".TB_PREF."task.task_type AND ".TB_PREF."task_type.check_type != 0
	AND ".TB_PREF."task.start_date= ".db_escape($date)."	AND ".TB_PREF."task_type.`check_type` = 1";

	$sql .=" GROUP BY order_no";
	return db_query($sql, "could not get function code");
	/*	$row = db_fetch($result);
        return $row[0];*/

}
//saad 17 may
function get_user_realname($user_id)
{
    $sql = "SELECT real_name FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

    $result = db_query($sql, "could not get sales type");

    $row = db_fetch_row($result);
    return $row[0];
}

//Code written by purpledesign.in Jan 2014
function dateDiff1($date)
{
  $mydate= date("Y-m-d H:i:s");
  $theDiff="";
  //echo $mydate;//2014-06-06 21:35:55
  $datetime1 = date_create($date);
  $datetime2 = date_create($mydate);
  $interval = date_diff($datetime1, $datetime2);
  //echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
  $min=$interval->format('%i');
  $sec=$interval->format('%s');
  $hour=$interval->format('%h');
  $mon=$interval->format('%m');
  $day=$interval->format('%d');
  $year=$interval->format('%y');
  if($interval->format('%i%h%d%m%y')=="00000")
  {
    //echo $interval->format('%i%h%d%m%y')."<br>";
    return $sec." Seconds";

  } 

else if($interval->format('%h%d%m%y')=="0000"){
   return $min." Minutes";
   }


else if($interval->format('%d%m%y')=="000"){
   return $hour." Hours";
   }


else if($interval->format('%m%y')=="00"){
   return $day." Days";
   }

else if($interval->format('%y')=="0"){
   return $mon." Months";
   }

else{
   return $year." Years";
   }

}

function truncate_text($string, $length, $dots = "..") {
    return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
}


?>