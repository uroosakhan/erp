<?php


function add_user($user_id, $real_name, $password, $phone, $email, $role_id, 
	$language, $profile, $rep_popup, $pos)
{
	$sql = "INSERT INTO ".TB_PREF."users (user_id, real_name, password"
		.", phone, email, role_id, language, pos, print_profile, rep_popup)
		VALUES (".db_escape($user_id).", 
		".db_escape($real_name).", ".db_escape($password) .",".db_escape($phone)
		.",".db_escape($email).", ".db_escape($role_id).", ".db_escape($language)
		.", ".db_escape($pos).",".db_escape($profile).",".db_escape($rep_popup)
		." )";

	return db_query($sql, "could not add user for $user_id");
}

function get_user_id($trans_no,$type)
{
	$sql= "SELECT user FROM " . TB_PREF . "audit_trail WHERE type = ".db_escape($type)." AND trans_no =".db_escape($trans_no);
	$result = db_query($sql, "could not process Requisition to Purchase Order");
	$row = db_fetch_row($result);
	return $row[0] ;
}
function get_bank_account_name($id)
{
    $sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE account_code=".db_escape($id);

    $result = db_query($sql, "could not retreive bank account");

    $row = db_fetch_row($result);

    return $row[0];
}
//-----------------------------------------------------------------------------------------------

function update_user_password($id, $user_id, $password)
{
	$sql = "UPDATE ".TB_PREF."users SET password=".db_escape($password) . ",
		user_id = ".db_escape($user_id). " WHERE id=".db_escape($id);

	return db_query($sql, "could not update user password for $user_id");
}

//-----------------------------------------------------------------------------------------------

function update_user($id, $user_id, $real_name, $phone, $email, $role_id, 
	$language, $profile, $rep_popup, $pos)
{
	$sql = "UPDATE ".TB_PREF."users SET real_name=".db_escape($real_name).
	", phone=".db_escape($phone).",
		email=".db_escape($email).",
		role_id=".db_escape($role_id).",
		language=".db_escape($language).",
		print_profile=".db_escape($profile).",
		rep_popup=".db_escape($rep_popup).",
		pos=".db_escape($pos).",
		user_id = " . db_escape($user_id)
		. " WHERE id=" . db_escape($id);
	return db_query($sql, "could not update user for $user_id");
}

//-----------------------------------------------------------------------------------------------

function update_user_prefs($id, $prefs)
{
	$sql = "UPDATE ".TB_PREF."users SET ";
	foreach($prefs as $name => $value) {
		$prefs[$name] = $name.'='. db_escape($value);
	}
	$sql .= implode(',', $prefs) . " WHERE id=".db_escape($id);

	return db_query($sql, "could not update user display prefs for $id");
}

//-----------------------------------------------------------------------------------------------


function get_users($all=false)
{
	$sql = "SELECT u.*, r.role FROM ".TB_PREF."users u, ".TB_PREF."security_roles r
		WHERE u.role_id=r.id";
	if (!$all) $sql .= " AND !u.inactive";
	
	return db_query($sql, "could not get users");
}

//-----------------------------------------------------------------------------------------------

function get_user($id)
{
	$sql = "SELECT * FROM ".TB_PREF."users WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get user $id");

	return db_fetch($result);
}

//-----------------------------------------------------------------------------------------------

function get_user_by_login($user_id)
{
	$sql = "SELECT * FROM ".TB_PREF."users WHERE user_id=".db_escape($user_id);

	$result = db_query($sql, "could not get user $user_id");

	return db_fetch($result);
}

//-----------------------------------------------------------------------------------------------

function get_user_by_email($email)
{
	$sql = "SELECT * FROM ".TB_PREF."users WHERE email=".db_escape($email);

	$result = db_query($sql, "could not get user for email $email");

	if (db_num_rows($result) != 1)
		return false;

	return db_fetch($result);
}

//-----------------------------------------------------------------------------------------------

function delete_user($id)
{
	$sql="DELETE FROM ".TB_PREF."users WHERE id=".db_escape($id);

	db_query($sql, "could not delete user $id");
}

//-----------------------------------------------------------------------------------------------

function get_user_auth($user_id, $password)
{

	$sql = "SELECT * FROM ".TB_PREF."users WHERE user_id = ".db_escape($user_id)." AND"
		." password=".db_escape($password);

	return db_num_rows(db_query($sql, "could not get validate user login for $user_id")) != 0;
}

//-----------------------------------------------------------------------------------------------

function update_user_visitdate($user_id)
{
	$sql = "UPDATE ".TB_PREF."users SET last_visit_date='". date("Y-m-d H:i:s") ."'
		WHERE user_id=".db_escape($user_id);

	db_query($sql, "could not update last visit date for user $user_id");
}

//-----------------------------------------------------------------------------------------------
function check_user_activity($id) 
{
	$sql = "SELECT COUNT(*) FROM ".TB_PREF."audit_trail WHERE audit_trail.user="
		. db_escape($id);
	$result = db_query($sql,"Cant check user activity");
	$ret = db_fetch($result);

	return $ret[0];
}

//-----------------------------------------------------------------------------------------------
function show_users_online()
{
	global $db, $GetText, $SysPrefs;

	if (!isset($SysPrefs->show_users_online) || $SysPrefs->show_users_online == 0 || !defined('TB_PREF') || 
		!isset($GetText) || !isset($db))
		return "";
	$result = db_query("SHOW TABLES LIKE '".TB_PREF."useronline'");	
	if (db_num_rows($result) == 1)
	{
		$timeoutseconds = 120;

		$timestamp=time();
		$timeout=$timestamp-$timeoutseconds;
		/*
		This will find out if user is from behind proxy server. 
		In that case, the script would count them all as 1 user.
		This function tryes to get real IP address.
		*/
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED'];
		}
		elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['HTTP_FORWARDED'])) {
			$ip = $_SERVER['HTTP_FORWARDED'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// Add user to database
		db_query("INSERT INTO ".TB_PREF."useronline (timestamp, ip, file) VALUES ('". $timestamp ."',". db_escape($ip) .",". db_escape($_SERVER['PHP_SELF']) .")");
		//Remove users that were not online within $timeoutseconds.
		db_query("DELETE FROM ".TB_PREF."useronline WHERE timestamp<". $timeout);

		// Select online users
		$result = db_query("SELECT DISTINCT ip FROM ".TB_PREF."useronline");
		$users = db_num_rows($result);
	}
	else
		$users = 1;
	return "$users ".($users == 1 ? _("user online") : _("users online"));

}

//dz 31.5.17
//Locations

function get_users_locations($all=false, $user_id)
{
	$sql = "SELECT 
	loc.* 
	FROM 
	".TB_PREF."users u, ".TB_PREF."user_locations loc
		WHERE u.id=loc.user_id";

	if($user_id != ALL_TEXT)
		$sql .= " AND loc.user_id=".db_escape($user_id);

	if (!$all) $sql .= " AND !loc.inactive";

	return db_query($sql, "Could not get user locations.");
}
function get_users_banks($all=false, $user_id)
{
    $sql = "SELECT 
	loc.* 
	FROM 
	".TB_PREF."users u, ".TB_PREF."user_banks loc
		WHERE u.id=loc.user_id";

    if($user_id != ALL_TEXT)
        $sql .= " AND loc.user_id=".db_escape($user_id);

    if (!$all) $sql .= " AND !loc.inactive";

    return db_query($sql, "Could not get user locations.");
}
function update_users_locations($id, $loc_code, $user_id)
{
	$sql = "UPDATE ".TB_PREF."user_locations SET 
	loc_code=".db_escape($loc_code) . ", user_id = ".db_escape($user_id). " 
	WHERE id=".db_escape($id);

	return db_query($sql, "Could not update user location.");
}
function update_users_banks($id, $loc_code, $user_id)
{
    $sql = "UPDATE ".TB_PREF."user_banks SET 
	loc_code=".db_escape($loc_code) . ", user_id = ".db_escape($user_id). " 
	WHERE id=".db_escape($id);

    return db_query($sql, "Could not update user location.");
}
function add_user_locations($user_id, $loc_code)
{
	$sql = "INSERT INTO ".TB_PREF."user_locations (user_id, loc_code)
		VALUES (".db_escape($user_id).",  ".db_escape($loc_code).")";

	return db_query($sql, "Could not add user's location.");
}
function add_user_banks($user_id, $loc_code)
{
    $sql = "INSERT INTO ".TB_PREF."user_banks (user_id, loc_code)
		VALUES (".db_escape($user_id).",  ".db_escape($loc_code).")";

    return db_query($sql, "Could not add user's location.");
}
function get_user_name($user_id)
{
	$sql = "SELECT user_id FROM ".TB_PREF."users WHERE id=".db_escape($user_id);

	$result = db_query($sql, "could not get user $user_id");

	$myrow = db_fetch($result);

	return $myrow[0];
}

function get_user_location($id)
{
	$sql = "SELECT * FROM ".TB_PREF."user_locations WHERE id=".db_escape($id);
	$result = db_query($sql, "Could not get user # $id");
	return db_fetch($result);
}
function get_user_bank($id)
{
    $sql = "SELECT * FROM ".TB_PREF."user_banks WHERE id=".db_escape($id);
    $result = db_query($sql, "Could not get user # $id");
    return db_fetch($result);
}
function delete_user_location($id)
{
	$sql="DELETE FROM ".TB_PREF."user_locations WHERE id=".db_escape($id);

	db_query($sql, "could not delete user's location # $id");
}
function delete_user_banks($id)
{
    $sql="DELETE FROM ".TB_PREF."user_banks WHERE id=".db_escape($id);

    db_query($sql, "could not delete user's location # $id");
}
function get_location_description($loc_code)
{
	$sql = "SELECT location_name FROM ".TB_PREF."locations WHERE loc_code=".db_escape($loc_code);
	$result = db_query($sql, "Could not get location name # ".$loc_code);
	$myrow = db_fetch($result);
	return $myrow[0];
}
function get_bank_description($loc_code)
{
    $sql = "SELECT bank_account_name FROM ".TB_PREF."bank_accounts WHERE id=".db_escape($loc_code);
    $result = db_query($sql, "Could not get location name # ".$loc_code);
    $myrow = db_fetch($result);
    return $myrow[0];
}
function check_user_location_duplication($user_id, $loc_code)
{
	$sql = "SELECT count(id) FROM ".TB_PREF."user_locations WHERE 
	user_id=".db_escape($user_id)." AND loc_code=".db_escape($loc_code);

	$result = db_query($sql, "Could not get user's location ".$loc_code);
	$myrow = db_fetch($result);
	return $myrow[0];
}
function check_user_banks_duplication($user_id, $loc_code)
{
    $sql = "SELECT count(id) FROM ".TB_PREF."user_banks WHERE 
	user_id=".db_escape($user_id)." AND loc_code=".db_escape($loc_code);

    $result = db_query($sql, "Could not get user's location ".$loc_code);
    $myrow = db_fetch($result);
    return $myrow[0];
}


function current_user_locations_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $current_user=false)
{
	global $all_items;
	$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations";

	return combo_input($name, $selected_id, $sql, 'loc_code', 'location_name',
		array(
			'spec_option' => $all_option === true ? _("All Locations") : $all_option,
			'spec_id' => $all_items,
			'select_submit'=> $submit_on_change
		) );
}

function current_user_locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $current_user)
{
	echo "<tr><td class=''>$label</td>";
	current_user_locations_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $current_user);
	echo "</tr>\n";
}

function current_user_locations_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $current_user)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo current_user_locations_list($name, $selected_id, $all_option, $submit_on_change, $current_user);
	echo "</td>\n";
}

//ansar 23-09-16
function users_list_($name, $selected_id=null, $type=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT id, user_id FROM ".TB_PREF."users";

	return combo_input($name, $selected_id, $sql, 'id', 'user_id',
		array(
			'spec_option' => $special_option===true ? _("All Users") : $special_option,
			'spec_id' => '',
			'order' => 'user_id',
			'select_submit'=> $submit_on_change,
			'async' => false
		) );

}

function users_list_cells_($label, $name, $selected_id=null, $type, $submit_on_change=false,
						  $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo users_list_($name, $selected_id, $type, $submit_on_change, $special_option);
	echo "</td>";
}

function users_list_row_($label, $name, $selected_id=null, $type, $submit_on_change=false,
						$special_option=false)
{
	echo "<tr><td class=''>$label</td>";
	users_list_cells_(null, $name, $selected_id, $type, $submit_on_change, $special_option);
	echo "</tr>\n";
}




function get_user_email()
{
	$sql = "SELECT * FROM ".TB_PREF."users ";

	$result = db_query($sql, "could not get user $id");

	return $result;
}



function check_user_email()
{
	$sql = "SELECT email FROM ".TB_PREF."users ";

	$result = db_query($sql, "could not get user $id");

	$row = db_fetch_row($result);
	return $row[0] ;
}

function check_sales_department_email()
{
	$sql = "SELECT description FROM ".TB_PREF."sales_email ";

	$result = db_query($sql, "could not get user $id");

	$row = db_fetch_row($result);
	return $row[0] ;
}

function get_sales_department_email()
{
$sql = "SELECT * FROM ".TB_PREF."sales_email ";

	$result = db_query($sql, "could not get user $id");

	return $result;
}
//-------------Purchase department emails
function check_purchase_department_email()
{
	$sql = "SELECT description FROM ".TB_PREF."purchase_email ";

	$result = db_query($sql, "could not get user $id");

	$row = db_fetch_row($result);
	return $row[0] ;
}

function get_purchase_department_email()
{
$sql = "SELECT * FROM ".TB_PREF."purchase_email ";

	$result = db_query($sql, "could not get user $id");

	return $result;
}

//dimension
function add_user_dimension($user_id, $dim_id)
{


    $sql = "INSERT INTO " . TB_PREF . "user_dim (user_id, dim_id)
		VALUES (" . db_escape($user_id) . ",  " . db_escape($dim_id) . ")";
    return db_query($sql, "Could not add user's location.");

}

function add_dimension_users($dim_id, $user_id)
{

//        display_error($loc."/".$user_id);
//        if (!$loc) continue;

    $sql = "INSERT INTO " . TB_PREF . "user_dim (dim_id, user_id)
		VALUES (" . db_escape($dim_id) . ",  " . db_escape($user_id) . ")";
    return db_query($sql, "Could not add user's location.");

}

function get_dimension_user_wise($all=false, $user_id)
{
    $sql = "SELECT 
	dim.* 
	FROM 
	".TB_PREF."users u, ".TB_PREF."user_dim dim
		WHERE u.id=dim.user_id";

    if($user_id != ALL_TEXT)
        $sql .= " AND dim.user_id=".db_escape($user_id);

    if (!$all) $sql .= " AND !dim.inactive";

    return db_query($sql, "Could not get user locations.");
}

function get_users_dimension_wise($all=false, $dim)
{
    $sql = "SELECT 
	dim.* 
	FROM 
	".TB_PREF."dimensions d, ".TB_PREF."user_dim dim
		WHERE d.id=dim.dim_id";

    if($dim != ALL_TEXT)
        $sql .= " AND dim.dim_id=".db_escape($dim);

    if (!$all) $sql .= " AND !dim.inactive";

    return db_query($sql, "Could not get user locations.");
}

function get_dimension_description($dim_id)
{
    $sql = "SELECT name FROM ".TB_PREF."dimensions WHERE id=".db_escape($dim_id);
    $result = db_query($sql, "Could not get location name # ".$dim_id);
    $myrow = db_fetch($result);
    return $myrow[0];
}

function delete_user_dimension($id)
{
    $sql="DELETE FROM ".TB_PREF."user_dim WHERE id=".db_escape($id);

    db_query($sql, "could not delete user's location # $id");
}

function check_user_dimension_duplication($user_id, $dim_id)
{
    $sql = "SELECT count(id) FROM ".TB_PREF."user_dim WHERE 
	user_id=".db_escape($user_id)." AND dim_id=".db_escape($dim_id);

    $result = db_query($sql, "Could not get user's location ".$dim_id);
    $myrow = db_fetch($result);
    return $myrow[0];
}

//gl_account
function check_user_account_duplication($user_id, $account_code)
{
    $sql = "SELECT count(id) FROM ".TB_PREF."useraccounts WHERE 
	user_id=".db_escape($user_id)." AND account_code=".db_escape($account_code);

    $result = db_query($sql, "Could not get user's location ".$account_code);
    $myrow = db_fetch($result);
    return $myrow[0];
}

function add_user_account($user_id, $account_code)
{

    $sql = "INSERT INTO " . TB_PREF . "useraccounts (user_id, account_code)
		VALUES (" . db_escape($user_id) . ",  " . db_escape($account_code) . ")";
    return db_query($sql, "Could not add user's location.");
}

function add_accounts_users($account_code, $user_id)
{

//        display_error($loc."/".$user_id);
//        if (!$loc) continue;

    $sql = "INSERT INTO " . TB_PREF . "useraccounts (account_code, user_id)
		VALUES (" . db_escape($account_code) . ",  " . db_escape($user_id) . ")";
    return db_query($sql, "Could not add user's location.");
}

function delete_user_accounts($id)
{
    $sql="DELETE FROM ".TB_PREF."useraccounts WHERE id=".db_escape($id);

    db_query($sql, "could not delete user's location # $id");
}

function get_account_user_wise($all=false, $user_id)
{
    $sql = "SELECT 
	account.* 
	FROM 
	".TB_PREF."users u, ".TB_PREF."useraccounts account
		WHERE u.id=account.user_id";

    if($user_id != ALL_TEXT)
        $sql .= " AND account.user_id=".db_escape($user_id);

    if (!$all) $sql .= " AND !account.inactive";

    return db_query($sql, "Could not get user locations.");
}

function get_users_accounts_wise($all=false, $loc)
{
    $sql = "SELECT 
	account.* 
	FROM 
	".TB_PREF."chart_master chart, ".TB_PREF."useraccounts account
		WHERE chart.account_code=account.account_code";

    if($loc != ALL_TEXT)
        $sql .= " AND account.account_code=".db_escape($loc);

    if (!$all) $sql .= " AND !account.inactive";

    return db_query($sql, "Could not get user locations.");
}

function get_account_description($account_code)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($account_code);
    $result = db_query($sql, "Could not get location name # ".$account_code);
    $myrow = db_fetch($result);
    return $myrow[0];
}


function get_users_id(){
    $sql = "SELECT  *  FROM ".TB_PREF."users ORDER BY id";
    return db_query($sql, "could not get users");
}
