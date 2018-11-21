<?php

function update_salary_breakup_formula_basic_salary($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('basic_salary');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_salary_breakup_formula_hr($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('hr');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_salary_breakup_formula_ca($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('ca');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_salary_breakup_formula_adhoc_relief($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('adhoc_relief');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_salary_breakup_formula_others($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('others');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_salary_breakup_formula_medical($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('medical');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
	function update_salary_breakup_formula_eobi($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('eobi');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
	function update_salary_breakup_formula_income_tax($formula,$name)
{
	$sql = "UPDATE ".TB_PREF."salary_breakup_formula SET formula=".db_escape($formula)." WHERE name = ".db_escape('income_tax');
	
   	db_query($sql,"The Sys Pref could not be updated");
	}
	
	
function get_salary_breakup_formula($name)
{
	$sql = "SELECT formula FROM ".TB_PREF."salary_breakup_formula 
	WHERE name = ".db_escape($name);

	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);	
	return $myrow[0];
	
}

?>