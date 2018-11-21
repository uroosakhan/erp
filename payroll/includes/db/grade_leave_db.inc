<?php
//saad  
function add_grade_leav($description,$pl,$sl,$cl,$inactive)
{
	$sql = "INSERT INTO ".TB_PREF."grade_leave_setup (description,pl,sl,cl,inactive)
			VALUES ("
		  .db_escape($description) . ", "
		  .db_escape($pl) . ", "
		  .db_escape($sl) . ", "
		  .db_escape($cl) . ", "
		  .db_escape($inactive)
		  . ")";
   	db_query($sql,"The Description of the query failed");
}


function update_grade_leav($selected_id,$description,$pl,$sl,$cl, $inactive)
{
	$sql = "UPDATE ".TB_PREF."grade_leave_setup SET description=".db_escape($description).",
	    pl=".db_escape($pl).",
		sl=".db_escape($sl).",
		cl=".db_escape($cl).", 
		inactive=".db_escape($inactive)."	
		WHERE id = ".db_escape($selected_id);
   	db_query($sql,"The Description Name could not be updated");
}

function delete_grade_leav($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."grade_leave_setup WHERE id=".db_escape($selected_id);
	db_query($sql,"The query could not be deleted");
}
function get_grade_leav12($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."grade_leave_setup WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}

function get_grade_leav($show_inactive)
{
	$sql = "SELECT * FROM ".TB_PREF."grade_leave_setup";
	if (!$show_inactive) $sql .= " WHERE !inactive";
	return db_query($sql,"could not get sales persons");
}

function get_grade_name($id)
{
	$sql = "SELECT description FROM ".TB_PREF."grade WHERE id=".db_escape($id);

	$result = db_query($sql, "could not get sales type");

	$row = db_fetch_row($result);
	return $row[0];
}

?>