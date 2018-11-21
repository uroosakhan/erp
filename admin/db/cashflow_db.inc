<?php

function add_cashflow_type($name)
{
    $sql = "INSERT INTO ".TB_PREF."cashflow_type (name) VALUES (".db_escape($name) . ")";
    db_query($sql,"The cashflow type could not be added");
}

function update_cashflow_type($selected_id, $name)
{
    $sql = "UPDATE ".TB_PREF."cashflow_type SET name=".db_escape($name)." WHERE id = ".db_escape($selected_id);
    db_query($sql,"The cashflow type could not be updated");
}

function delete_cashflow_type($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."cashflow_type WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete cashflow type");
}

function get_cashflow_typee($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."cashflow_type";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get type");
}

function get_cashflow_type($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."cashflow_type WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get type");
    return db_fetch($result);
}

//marina-----------for cashflow_category
function get_cash_flow_type($id)
{
    $sql = "SELECT name FROM ".TB_PREF."cashflow_type WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get cashflow_type");

    $row = db_fetch_row($result);
    return $row[0];
}
//////---------------

//marina-----------for cashflow gl_account
function get_cash_flow_category($id)
{
    $sql = "SELECT name FROM ".TB_PREF."cashflow_categories WHERE id=".db_escape($id);

    $result = db_query($sql, "could not get cashflow categories");

    $row = db_fetch_row($result);
    return $row[0];
}
//////---------------
function add_cash_flow_category($name, $c_type, $flowtype)
{
    $sql = "INSERT INTO ".TB_PREF."cashflow_categories (name, c_type, flowtype)
		VALUES (".db_escape($name) . ", "
        .db_escape($c_type) . ", "
        .db_escape($flowtype) . ") ";

    db_query($sql,"The insert of the cashflow categories failed");
}

function add_cashflow_gl($cashflow_category_id, $gl_account)
{
    $sql = "INSERT INTO ".TB_PREF."cashflow_gl (cashflow_category_id, gl_account)
		VALUES (".db_escape($cashflow_category_id) . ", "

        .db_escape($gl_account) . ") ";

    db_query($sql,"The insert of the cashflow gl_account failed");
}

function update_cashflow_category($selected_id, $name, $c_type, $flowtype)

{
    $sql = "UPDATE ".TB_PREF."cashflow_categories SET name=".db_escape($name) . ",
		c_type=".db_escape($c_type) . ",
		flowtype=".db_escape($flowtype) . "
		WHERE id = ".db_escape($selected_id);
    db_query($sql,"The update of the cashflow categories failed");
}

function update_cashflow_gl($selected_id, $name, $gl_account)

{
    $sql = "UPDATE ".TB_PREF."cashflow_gl SET cashflow_category_id=".db_escape($name) . ",
		
		gl_account=".db_escape($gl_account) . "
		WHERE id = ".db_escape($selected_id);
    db_query($sql,"The update of the cashflow gl_account failed");
}

function delete_cashflow_category($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."cashflow_categories WHERE id=".db_escape($selected_id);
    db_query($sql,"The cashflow categories could not be deleted");
}

function delete_cashflow_gl($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."cashflow_gl WHERE id=".db_escape($selected_id);
    db_query($sql,"The cashflow gl_account could not be deleted");
}

function get_cashflow_category($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."cashflow_categories";
    if (!$show_inactive) $sql .= " WHERE !inactive";

     $sql .= " ORDER BY c_type, id, flowtype ";
    return db_query($sql,"could not get cashflow categories");
}

function get_cashflow_gl($show_inactive)
{
    $sql = "SELECT * FROM ".TB_PREF."cashflow_gl";
    if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get gl_account");
}
function get_gl_acc_($id)
{
    $sql = "SELECT * FROM ".TB_PREF."cashflow_gl";
   // if (!$show_inactive) $sql .= " WHERE !inactive";
    return db_query($sql,"could not get cashflow categories");
   
}
function get_cashflow_category2($selected_id)
{
    $sql = "SELECT *  FROM ".TB_PREF."cashflow_categories WHERE id=".db_escape($selected_id);
    $sql .= " ORDER BY c_type, id, flowtype";
    $result = db_query($sql,"could not get cashflow categories");
    return db_fetch($result);
}

function get_gl_account22($code)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master WHERE account_code=".db_escape($code);

    $result = db_query($sql, "could not get gl account");
    $row= db_fetch_row($result);
    return $row[0];
}

function get_c_type ($id)
{
    $sql = "SELECT c_type FROM ".TB_PREF."cashflow_categories WHERE id='.$id.'";

    $result = db_query($sql, "could not get gl account");
    $row= db_fetch_row($result);
    return $row[0];
    
}

function get_c_type_id ($cat_id)
{
    $sql = "SELECT c_type FROM ".TB_PREF."cashflow_categories WHERE id=$cat_id";

    $result = db_query($sql, "could not get gl account");
    $row= db_fetch_row($result);
    return $row[0];
    
}

function get_c_types_name($c_type)
{
    $sql = "SELECT name FROM ".TB_PREF."cashflow_type WHERE id= ".db_escape($c_type);

    $result = db_query($sql, "could not get gl account");
    $row= db_fetch_row($result);
    return $row[0];

}