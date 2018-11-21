<?php

/*
	Update main or gl company setup.
*/

function update_company_prefs_new($params)
{
	$sql = "UPDATE ".TB_PREF."sys_prefs_new SET value = ";
	foreach($params as $name => $value) {
		if (!db_query($sql. db_escape($value). " WHERE name=".db_escape($name),
			"The company prefferences could not be updated "))
			return false;
		// update cached value
		$_SESSION['SysPrefs_new']->prefs[$name] = $value;
	}
	return true;
}
function get_company_item_pref_from_name($name)
{
    $sql = "SELECT * FROM ".TB_PREF."item_pref WHERE name = ".db_escape($name);
    $query = db_query($sql, "Error");
    return db_fetch($query);
}

function update_company_prefs($params)
{
	$sql = "UPDATE ".TB_PREF."sys_prefs SET value = ";
	foreach($params as $name => $value) {
		if (!db_query($sql. db_escape($value). " WHERE name=".db_escape($name),
			 "The company prefferences could not be updated "))
			return false;
		// update cached value
		$_SESSION['SysPrefs']->prefs[$name] = $value;
	}
	return true;
}
/*
	Get company preferences. Returns cached values from global variable SysPrefs
	or retrieved from database if SysPrefs values are not set.
	$prefs can be preference name, array of names, or null for all preferences.
	
*/
function get_company_pref($prefs = null)
{
	global $SysPrefs, $db_version;

	if (!isset($SysPrefs->prefs))	 // just after first login or reset
		$SysPrefs->refresh();

	$all = $SysPrefs->prefs;

//	if (!$prefs)
//		return $all;
//	elseif (is_string($prefs))
//		return @$all[$prefs];
    if ($prefs && is_string($prefs))
        return @$all[$prefs];
//	$ret = array();
//	foreach($prefs as $name)
//		$ret[$name] = $all[$name];
//
//	return $ret;
    if (!is_array($all))
        $all = array();

    return $all;
}
function get_company_pref_display($prefs1 = null)
{
	global $SysPrefs, $db_version;

	if (!isset($SysPrefs->prefs1))	 // just after first login or reset
		$SysPrefs->refresh_new();

	$all = $SysPrefs->prefs1;

	if (!$prefs1)
		return $all;
	elseif (is_string($prefs1))
		return @$all[$prefs1];

	$ret = array();
	foreach($prefs1 as $name)
		$ret[$name] = $all[$name];

	return $ret;
}

function get_company_prefs()
{
	return get_company_pref(null);
}
function get_company_prefs_display()
{
	return get_company_pref_display(null);
}
function set_company_check($pref, $value)
{
	$sql = "REPLACE `".TB_PREF."sys_prefs_new` SET `name`=".db_escape($pref).", `value`=".db_escape($value);
	return db_query($sql, "cannot set company pref");
}
function set_company_pref_new($pref, $category, $type, $length, $value)
{
	$sql = "REPLACE `".TB_PREF."sys_prefs_new` SET `name`=".db_escape($pref).", `category`=".db_escape($category)
		.", `type`=".db_escape($type).", `length`=".db_escape($length).", `value`=".db_escape($value);
	return db_query($sql, "cannot set company pref");
}

function set_company_pref($pref, $category, $type, $length, $value)
{
	$sql = "REPLACE `".TB_PREF."sys_prefs` SET `name`=".db_escape($pref).", `category`=".db_escape($category)
		.", `type`=".db_escape($type).", `length`=".db_escape($length).", `value`=".db_escape($value);
	return db_query($sql, "cannot set company pref");
}

function get_base_sales_type()
{
	return get_company_pref('base_sales');
}

function get_company_extensions($id = -1) {
	global $path_to_root;

	$file = $path_to_root.($id == -1 ? '' : '/company/'.(int)$id).'/installed_extensions.php';
	$installed_extensions = array();
	if (is_file($file)) {
		include($file);
	}
	return $installed_extensions;
}

function add_payment_terms($daysOrFoll, $terms, $dayNumber)
{
	if ($daysOrFoll) 
	{
		$sql = "INSERT INTO ".TB_PREF."payment_terms (terms,
			days_before_due, day_in_following_month)
			VALUES (" .
			db_escape($terms) . ", " . db_escape($dayNumber) . ", 0)";
	} 
	else 
	{
		$sql = "INSERT INTO ".TB_PREF."payment_terms (terms,
			days_before_due, day_in_following_month)
			VALUES (" . db_escape($terms) . ",
			0, " . db_escape($dayNumber) . ")";
	}
   	db_query($sql,"The payment term could not be added");
}

function update_payment_terms($selected_id, $daysOrFoll, $terms, $dayNumber)
{
	if ($daysOrFoll) 
	{
		$sql = "UPDATE ".TB_PREF."payment_terms SET terms=" . db_escape($terms) . ",
			day_in_following_month=0,
			days_before_due=" . db_escape($dayNumber) . "
			WHERE terms_indicator = " .db_escape($selected_id);
	} 
	else 
	{
		$sql = "UPDATE ".TB_PREF."payment_terms SET terms=" . db_escape($terms) . ",
			day_in_following_month=" . db_escape($dayNumber) . ",
			days_before_due=0
			WHERE terms_indicator = " .db_escape($selected_id);
	}
   	db_query($sql,"The payment term could not be updated");
}

function delete_payment_terms($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."payment_terms WHERE terms_indicator=".db_escape($selected_id);
	db_query($sql,"could not delete a payment terms");
}

function get_payment_terms($selected_id)
{
	$sql = "SELECT *, (t.days_before_due=0) AND (t.day_in_following_month=0) as cash_sale
	 FROM ".TB_PREF."payment_terms t WHERE terms_indicator=".db_escape($selected_id);

	$result = db_query($sql,"could not get payment term");

	return db_fetch($result);
}

function get_payment_terms_all($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."payment_terms";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get payment terms");
}
/*
	Return number of records in tables, where some foreign key $id is used.
	$id - searched key value
	$tables - array of table names (without prefix); when table name is used as a key, then
		value is name of foreign key field. For numeric keys $stdkey field name is used.
	$stdkey - standard name of foreign key.
*/
function key_in_foreign_table($id, $tables, $stdkey)
{

	if (!is_array($tables))
		$tables = array($tables);

	$sqls = array();
	foreach ($tables as $tbl => $key) {
		if (is_numeric($tbl)) {
			$tbl = $key;
			$key = $stdkey;
		}
		$sqls[] = "(SELECT COUNT(*) as cnt FROM `".TB_PREF."$tbl` WHERE `$key`=".db_escape($id).")\n";
	}

	$sql = "SELECT sum(cnt) FROM (". implode(' UNION ', $sqls).") as counts";

	$result = db_query($sql, "check relations for ".implode(',',$tables)." failed");
	$count =  db_fetch($result);

	return $count[0];
}
////////////////////hareem


////////////////////hareem


function get_company_sales_pref($name)
{
	$sql = "SELECT label_value, so_enable FROM ".TB_PREF."sales_pref 
	WHERE name = ".db_escape($name);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

function get_cart_sales_comb()
{
	$sql = "SELECT label_value FROM ".TB_PREF."sales_pref WHERE name = ".db_escape(total_headers2);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_cart_sales_date()
{
	$sql = "SELECT label_value FROM ".TB_PREF."sales_pref WHERE name = ".db_escape(total_date);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_cart_sales_amount()
{
	$sql = "SELECT label_value FROM ".TB_PREF."sales_pref WHERE name = ".db_escape(total_amount);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
////////////////////////////////////////////////////////////////////////////////


function get_company_purch_pref($name)
{
	$sql = "SELECT label_value, po_enable, grn_enable FROM ".TB_PREF."purch_pref WHERE name = ".db_escape($name);
	
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

/////////////////////////////25-09-2017////////////////////////
function get_company_item_pref($name)
{
	$sql = "SELECT label_value, item_enable,inventory_enable, sale_enable, purchase_enable,s_position ,p_position ,s_width ,p_width,i_position,name ,item_combo_search FROM ".TB_PREF."item_pref WHERE name = ".db_escape($name);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_company_item_pref_from_position($position)
{
	$sql = "SELECT * FROM ".TB_PREF."item_pref WHERE s_position = ".db_escape($position);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_company_inventory_pref_from_position($position)
{
    $sql = "SELECT * FROM ".TB_PREF."item_pref WHERE i_position = ".db_escape($position);
    $query = db_query($sql, "Error");
    return db_fetch($query);
}
function get_company_hf_pref($name)
{
	$sql = "SELECT label_value, enable ,s_position ,p_position ,s_width ,p_width FROM ".TB_PREF."hf_pref WHERE name = ".db_escape($name);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}

function get_company_hf_pref_from_position($position)
{
	$sql = "SELECT * FROM ".TB_PREF."hf_pref WHERE s_position = ".db_escape($position);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_setup_item_values($stock_id)
{
	$sql = "SELECT text1,text2,text3,text4,text5,text6,amount1,amount2,amount3,amount4,amount5,amount6
  ,date1 ,date2,date3,combo1,combo2,combo3,combo4,combo5,combo6 FROM ".TB_PREF."stock_master WHERE stock_id = ".db_escape($stock_id);
	$query = db_query($sql, "Error");
	return db_fetch($query);
}
function get_total_cart_text($name)
{
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE name = ".db_escape(total_text);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_total_cart_amount($name)
{
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE name = ".db_escape(total_amount);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_total_cart_combo($name)
{
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE name = ".db_escape(total_combo);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_total_cart_date($name)
{
	$sql = "SELECT label_value FROM ".TB_PREF."item_pref WHERE name = ".db_escape(total_date);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}



///////
function get_cart_text()
{
	$sql = "SELECT label_value FROM ".TB_PREF."purch_pref WHERE name = ".db_escape(total_headers);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}
function get_cart_comb()
{
	$sql = "SELECT label_value FROM ".TB_PREF."purch_pref WHERE name = ".db_escape(total_headers2);
	$result = db_query($sql, "could not get customer");
	$row = db_fetch_row($result);
	return $row[0];
}



//---------------------------------------------------------------------------------------------
//
// Resets $theme references in users records to 'default'.
//
function clean_user_themes($theme)
{
	global $db_connections, $db;

	$comp = user_company();

	$connections = $db_connections;	// do not use db_connections directly here, or script will hang due to db_connections usage inside the loop
	foreach ($connections as $n => $conn) {
		$db = $_SESSION["wa_current_user"]->set_db_connection($n);
		$sql = "UPDATE {$conn['tbpref']}users SET theme='default' WHERE theme='$theme'";
		if (!db_query($sql, 'Cannot update user theme settings'))
			return false;
	}
	$db = $_SESSION["wa_current_user"]->set_db_connection($comp);
	$_SESSION['wa_current_user']->prefs->theme = 'default';
	return true;
}
