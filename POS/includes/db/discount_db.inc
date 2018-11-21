<?php
function add_discount_pos($discount, $dis_account, $discount_type, $discount_description)
{
    $sql = "INSERT INTO ".TB_PREF."discount (discount, dis_account, discount_type, discount_description)
		VALUES (".db_escape($discount).',' .db_escape($dis_account).',' .db_escape($discount_type).',' .db_escape($discount_description)  . ")";
    db_query($sql,"The insert discount failed");
}
function update_discount_pos($selected_id, $discount, $dis_account, $discount_type, $discount_description)
{
    $sql = "UPDATE ".TB_PREF."discount 
	SET discount=".db_escape($discount) . ", 
	    dis_account=".db_escape($dis_account). ", 
	    discount_type=".db_escape($discount_type). ", 
	    discount_description=".db_escape($discount_description) . "
	WHERE id = ".db_escape($selected_id);
    db_query($sql,"could not update failed");
}
function get_discounts_pos($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."discount";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get discount");
}
function get_discount_pos($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."discount WHERE id=".db_escape($selected_id);
    $result = db_query($sql,"could not get discount");
    return db_fetch($result);
}
function delete_discount_pos($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."discount WHERE id=".db_escape($selected_id);
    db_query($sql,"The discount could not be deleted");
}

function get_chart_master_discount_pos($account)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($account);
    $query = db_query($sql, "Error");
    $fetch = db_fetch_row($query);
    return $fetch[0];
}
?>