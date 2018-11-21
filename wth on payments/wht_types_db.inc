<?php
/**
 * Created by PhpStorm.
 * User: sheikh_salman
 * Date: 5/18/16
 * Time: 10:19 AM
 */
function add_wht_type($description, $tax_percent, $co_account, $level_id,$wth_tax_category)
{
    $sql = "INSERT INTO ".TB_PREF."wth_tax_types (description, tax_percent, co_account, level_id,wth_tax_category) VALUES 
    (".db_escape($description) . ", ".db_escape($tax_percent) . ", ".db_escape($co_account) . ",".db_escape($level_id).",".db_escape($wth_tax_category).")";
    db_query($sql,"The wht type could not be added");
}

function update_wht_type($selected_id, $description, $tax_percent,$co_account,$wth_tax_category)
{
    $sql = "UPDATE ".TB_PREF."wth_tax_types SET description=".db_escape($description).",
     tax_percent=".db_escape($tax_percent).", co_account=".db_escape($co_account)."
       , wth_tax_category=".db_escape($wth_tax_category)."
       
       WHERE id = ".db_escape($selected_id);
    db_query($sql,"The wht type could not be updated");
}

function delete_wht_type($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."wth_tax_types WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete  wht type.");
}

function get_wht_types()
{
    $sql = "SELECT L2.`id`, L2.`level_id`, L2.`description`,
    L1.`description` AS `head`,
    L2.`tax_percent`,
    L2.`code`, L2.`co_account`, L2.`wth_tax_category`
    FROM `".TB_PREF."wth_tax_types` AS L1
    LEFT JOIN `".TB_PREF."wth_tax_types` AS L2
    ON L1.`id` = L2.`level_id`";
    $sql .= " WHERE !ISNULL(L2.level_id)";
    $sql .= " ORDER BY L1.description";
    return db_query($sql,"Could not get wht types.");
}

function get_wht_type($selected_id)
{
    $sql = "SELECT * FROM  ".TB_PREF."wth_tax_types WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get wht type.");
    return db_fetch($result);
}

//function get_wht_account_code($code)
//{
//    $sql = "SELECT co_account FROM  ".TB_PREF."wth_tax_types
//     WHERE code=".db_escape($code);
//
//    $result = db_query($sql,"could not get wht account.");
//    $myrow = db_fetch($result);
//    return $myrow["co_account"];
//}

function get_wht_tax_category_name($code)
{
    if($code == 1)
    {
        
        $a= "WHT Supply Tax";
    }
    elseif ($code == 2)
    {
        $a= "WHT Service Tax";

    }
    elseif($code == 3)
    {

        $a= "WHT FBR Tax";
    }
    else{

        $a= "WHT SRB Tax";
    }
    return $a;
}

?>