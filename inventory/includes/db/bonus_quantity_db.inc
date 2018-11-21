<?php
function add_bonus_qty($stock_id, $minimum_qty,$maximum_qty, $bonus_qty)
{
    $sql = "INSERT INTO ".TB_PREF."bonus (stock_id, minimum_qty,maximum_qty, bonus_qty) 
		VALUES (".db_escape($stock_id).", ".db_escape($minimum_qty).",".db_escape($maximum_qty).",
       " .db_escape($bonus_qty).
        ")";

    db_query($sql,"an item price could not be added");
}

function update_bonus_qty($id, $minimum_qty,$maximum_qty, $bonus_qty)
{
    $sql = "UPDATE ".TB_PREF."bonus SET 
	minimum_qty=".db_escape($minimum_qty).",
	 maximum_qty=".db_escape($maximum_qty).",
		bonus_qty=".db_escape($bonus_qty)."
		 WHERE id=".db_escape($id);

    db_query($sql,"an item price could not be updated");
}

function get_bonus($stk_id)
{

    $sql = "SELECT  ".TB_PREF."bonus.* 
		FROM ".TB_PREF."bonus
		where stock_id = ".db_escape($stk_id)."
		 ORDER BY id";

    return db_query($sql,"item prices could not be retreived");
}


function get_bonus1($id)
{
    $sql = "  SELECT ".TB_PREF."bonus.* 
		FROM ".TB_PREF."bonus
		where id = $id
		
		 ";

    $result = db_query($sql,"item prices could not be retreived");
    $row =db_fetch($result);
    return $row;

}

function delete_bonus($price_id)
{
    $sql="DELETE FROM ".TB_PREF."bonus WHERE id= ".db_escape($price_id);
    db_query($sql,"an item price could not be deleted");
}



?>