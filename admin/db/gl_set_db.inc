<?php

function  update_sys_pay ($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).", 
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	WHERE name = ".db_escape('Landing_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_account($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).",
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit =".db_escape($unit_cost).",
	header_enable =".db_escape($header_enable).",
	as_per_be_enable =".db_escape($as_per_be_enable)."



	WHERE name = ".db_escape('INS_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys ($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).",
	 text_name=".db_escape($text_name).",
	 default_value=".db_escape($default_name).",
	 gl_entry=".db_escape($gl_entry)." ,
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	 WHERE name = ".db_escape('F_E_D_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_list($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).",
	
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry)."
	,
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	WHERE name = ".db_escape('Duty_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}

function update_sys_pay_taxes($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).", 
	
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	WHERE name = ".db_escape('S_T_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_i_taxes($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).", 
	
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	WHERE name = ".db_escape('I_Tax_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_add_taxes($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).", 
	
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."

	WHERE name = ".db_escape('Add_S_T_Amt');

    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_other_expense($account,$name,$text_name,$default_name,$gl_entry,$unit_cost,$header_enable,$as_per_be_enable)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref_new SET account=".db_escape($account).", 
	
	text_name=".db_escape($text_name).",
	default_value=".db_escape($default_name).",
	gl_entry=".db_escape($gl_entry).",
	cost_unit=".db_escape($unit_cost).",
	header_enable=".db_escape($header_enable).",
	as_per_be_enable=".db_escape($as_per_be_enable)."


	WHERE name = ".db_escape('Other_Expense');

    db_query($sql,"The Sys Pref could not be updated");
}

function get_sys_pay_pref($name)
{
    $sql = "SELECT account FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
//
function get_sys_pay_pref_field($name)
{
    $sql = "SELECT text_name FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}function get_sys_pay_pref_gl_entry($name)
{
    $sql = "SELECT gl_entry FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_sys_pay_pref_cost_unit($name)
{
    $sql = "SELECT cost_unit  FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}

function get_sys_pay_pref_header($name)
{
    $sql = "SELECT header_enable  FROM ".TB_PREF."sys_pay_pref_new
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}


function get_sys_pay_pref_as_per_be($name)
{
    $sql = "SELECT as_per_be_enable  FROM ".TB_PREF."sys_pay_pref_new
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}

function get_sys_pay_pref_field_n($name)
{
    $sql = "SELECT default_value FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}


function get_sys_pay_pref_field_default ($name)
{
    $sql = "SELECT  default_value  FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}


function get_sys_pay_pref_default ($name)
{
    $sql = "SELECT default_value FROM ".TB_PREF."sys_pay_pref_new 
	WHERE name = ".db_escape($name);

    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
?>