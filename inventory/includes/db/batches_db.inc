<?php
function get_batches($show_inactive,$batch)
{
    $sql = "SELECT * FROM ".TB_PREF."batch  
    WHERE id != -1 ";
    if (!$show_inactive) $sql .= " AND !inactive";
    if ($batch != '') $sql .= " AND id =  ".db_escape($batch). " ";
    return db_query($sql,"could not get combo");
}

function get_batchess($unit)
{
    $sql="SELECT * FROM ".TB_PREF."batch WHERE id=".db_escape($unit);

    $result = db_query($sql,"an unit of measure could not be retrieved");

    return db_fetch($result);
}

function update_batch($selected_id, $description,$exp_date)
{
    $sql = "UPDATE ".TB_PREF."batch SET name =".db_escape($description). ",
     exp_date =".db_escape(date2sql($exp_date)). "
     
     WHERE id = ".db_escape($selected_id);
    db_query($sql,"The Batch  could not be updated");

}
function add_batches($description,$exp_date)
{
    $sql = "INSERT INTO ".TB_PREF."batch (name,exp_date) VALUES (".db_escape($description) . ",".db_escape($exp_date) . ")";
    db_query($sql,"The combo could not be added");
}


