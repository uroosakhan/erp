<?php

function get_month_days($month_id)
{
	$sql = "SELECT ".TB_PREF."month.days FROM ".TB_PREF."month
	WHERE id=".db_escape($month_id);
	$result = db_query($sql, "Could't get month days");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
function get_month_id($month_id)
{
	$sql = "SELECT id FROM ".TB_PREF."month
	WHERE id=".db_escape($month_id);
	$result = db_query($sql, "Could't get month days");
	$myrow = db_fetch($result);
	return $myrow['0'];
}
?>