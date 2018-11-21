<?php

/*function add_sales_group($description)
{
	$sql = "INSERT INTO ".TB_PREF."title (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales group could not be added");
}

function update_sales_group($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."title SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales group could not be updated");
}

function delete_sales_group($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales group");
}

function get_sales_groups($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."title";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get title");
}

function get_sales_group($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get group");
	return db_fetch($result);
}

function get_sales_group_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}
 
function add_sales_area($description)
{
	$sql = "INSERT INTO ".TB_PREF."areas (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales area could not be added");
}

function update_sales_area($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."areas SET description=".db_escape($description)." WHERE area_code = ".db_escape($selected_id);
   	db_query($sql,"The sales area could not be updated");
}

function delete_sales_area($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);
	db_query($sql,"could not delete sales area");
}

function get_sales_areas($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."areas";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get areas");
}

function get_sales_area($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."areas WHERE area_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get area");
	return db_fetch($result);
}

function get_area_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."areas WHERE area_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function add_salesman($salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $provision, $break_pt, $provision2)
{
	$sql = "INSERT INTO ".TB_PREF."salesman (salesman_name, salesman_phone, salesman_fax, salesman_email,
		provision, break_pt, provision2)
		VALUES (".db_escape($salesman_name) . ", "
		  .db_escape($salesman_phone) . ", "
		  .db_escape($salesman_fax) . ", "
		  .db_escape($salesman_email) . ", ".
		$provision.", ".$break_pt.", "
		.$provision2.")";
   	db_query($sql,"The insert of the sales person failed");
}

function update_salesman($selected_id, $salesman_name, $salesman_phone, $salesman_fax,
	$salesman_email, $provision, $break_pt, $provision2)
{
	$sql = "UPDATE ".TB_PREF."salesman SET salesman_name=".db_escape($salesman_name) . ",
		salesman_phone=".db_escape($salesman_phone) . ",
		salesman_fax=".db_escape($salesman_fax) . ",
		salesman_email=".db_escape($salesman_email) . ",
		provision=".$provision.",
		break_pt=".$break_pt.",
		provision2=".$provision2."
		WHERE salesman_code = ".db_escape($selected_id);
   	db_query($sql,"The update of the sales person failed");
}

function delete_salesman($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);
	db_query($sql,"The sales-person could not be deleted");
}

function get_salesmen($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."salesman";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_salesman($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_salesman_name($id)
{
	$sql = "SELECT salesman_name FROM ".TB_PREF."salesman WHERE salesman_code=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_phone($id)
{

$sql = "SELECT phone FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}


function get_fax($id)
{

$sql = "SELECT fax FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'
AND 	".TB_PREF."crm_contacts.entity_id = ".db_escape($id)
;

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_mobile($id)
{
$sql = "SELECT phone2 FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_email($id)
{
$sql = "SELECT email FROM 
		".TB_PREF."crm_persons,
		".TB_PREF."crm_contacts
WHERE 	".TB_PREF."crm_persons.id= ".TB_PREF."crm_contacts.id
AND 	".TB_PREF."crm_contacts.entity_id = $id
AND     ".TB_PREF."crm_contacts.action = 'general'
AND 	".TB_PREF."crm_contacts.type = 'cust_branch'";

	

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

function get_shipper_name($id)
{
	$sql = "SELECT shipper_name FROM ".TB_PREF."shippers WHERE shipper_id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

//------------------------------------------------------------
function get_sales_agents($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."sale_agent";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY id";
	return db_query($sql,"could not get sale_agent");
}

function add_sales_agent($name)
{
	$sql = "INSERT INTO ".TB_PREF."sale_agent (name) VALUES (".db_escape($name) . ")";
   	db_query($sql,"The sales agent could not be added");
}


function update_sales_agent($selected_id, $name)
{
	$sql = "UPDATE ".TB_PREF."sale_agent SET name=".db_escape($name)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales agent could not be updated");
}

function delete_sales_agent($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."sale_agent WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales agent");
}



function get_sales_agent($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."sale_agent WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get agent");
	return db_fetch($result);
}

function get_sales_agent_name($agent_no)
{
	$sql = "SELECT name FROM ".TB_PREF."sale_agent WHERE id = ".db_escape($agent_no);
	$result = db_query($sql, "could not get agent");
	$row = db_fetch($result);
	return $row[0];
}
*/

function add_emp_title($description)
{
	$sql = "INSERT INTO ".TB_PREF."title (description) VALUES (".db_escape($description) . ")";
   	db_query($sql,"The sales group could not be added");
}

function update_emp_title($selected_id, $description)
{
	$sql = "UPDATE ".TB_PREF."title SET description=".db_escape($description)." WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The sales group could not be updated");
	}
	
	function delete_emp_title($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);
	db_query($sql,"could not delete sales group");
}

function get_emp_titles($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."title";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	$sql .= " ORDER BY description";
	return db_query($sql,"could not get title");
}

function get_emp_title($selected_id)
{
	$sql = "SELECT * FROM ".TB_PREF."title WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get group");
	return db_fetch($result);
}

function get_emp_title_name($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."title WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get group");
	$row = db_fetch($result);
	return $row[0];
}



?>