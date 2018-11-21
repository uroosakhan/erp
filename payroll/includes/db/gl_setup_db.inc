<?php

function update_sys_pay_ex($account,$name)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('payroll_expenses');
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_sys_pay_ad($account,$name)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('advance_receivable');
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_sys_pay_pa($account,$name)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('payment');
   	db_query($sql,"The Sys Pref could not be updated");
	}
function update_sys_pay_li($account,$name)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('payroll_liabilty');
   	db_query($sql,"The Sys Pref could not be updated");
	}

function update_sys_pay_tax($account,$name)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('tax_liability');
   	db_query($sql,"The Sys Pref could not be updated");
	}

function update_sys_pay_eobi($account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('eobi_liability');
   	db_query($sql,"The Sys Pref could not be updated");
}
function update_eoboi($eobi)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($eobi)." 
	WHERE name = ".db_escape('eobi');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_sessi($sessi)
{  $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($sessi)." 
	WHERE name = ".db_escape('sessi');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_total_working_days($total_working_days)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($total_working_days)." 
	WHERE name = ".db_escape('total_working_days');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_services_($services)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($services)." 
	WHERE name = ".db_escape('services');
	db_query($sql,"The Sys Pref could not be updated");
}

function get_sys_pay_pref($name)
{
	$sql = "SELECT account FROM ".TB_PREF."sys_pay_pref 
	WHERE name = ".db_escape($name);
	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);	
	return $myrow[0];
	
}
function get_service_charges_account($employee_id)
{
    $sql = "SELECT payroll_expenses FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_service_charges_payable_account($employee_id)
{
    $sql = "SELECT payroll_liabilty FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_service_income_tax_account($employee_id)
{
    $sql = "SELECT tax_liability FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_service_eobi_account($employee_id)
{
    $sql = "SELECT deduction_account FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}

function get_accounts_name_through_code($account_code)
{
	$sql = "SELECT account_name FROM ".TB_PREF."chart_master 
	WHERE account_code = ".db_escape($account_code);
	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);
	return $myrow[0];
}

function get_sys_pay_pref_eobi($name)
{
	$sql = "SELECT account FROM ".TB_PREF."sys_pay_pref 
	WHERE name = ".db_escape($name);
	$result = db_query($sql,"could not get department");
	$myrow = db_fetch($result);
	return $myrow[0];
}

function update_filer($filer)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($filer)." 
	WHERE name = ".db_escape('filer');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_non_filer($non_filer)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($non_filer)." 
	WHERE name = ".db_escape('non_filer');
	db_query($sql,"The Sys Pref could not be updated");
}

function update_loan_account($loan_account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($loan_account)." 
	WHERE name = ".db_escape('loan_account');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_advance_account($advance_account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($advance_account)." 
	WHERE name = ".db_escape('advance_account');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_salary_account($salary_account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($salary_account)." 
	WHERE name = ".db_escape('salary_account');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_bonus_account($bonus_account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($bonus_account)." 
	WHERE name = ".db_escape('bonus_account');
	db_query($sql,"The Sys Pref could not be updated");
}
function update_deduction_account($deduction_account)
{
	$sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($deduction_account)." 
	WHERE name = ".db_escape('deduction_account');
	db_query($sql,"The Sys Pref could not be updated");
}
///
function update_sys_pay_ex_s($account,$name)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_payroll_expenses');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_ad_s($account,$name)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_advance_receivable');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_pa_s($account,$name)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_payment');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_sys_pay_li_s($account,$name)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_payroll_liabilty');
    db_query($sql,"The Sys Pref could not be updated");
}

function update_sys_pay_tax_s($account,$name)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_tax_liability');
    db_query($sql,"The Sys Pref could not be updated");
}

function update_sys_pay_eobi_s($account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET account=".db_escape($account)." WHERE name = ".db_escape('s_eobi_liability');
    db_query($sql,"The Sys Pref could not be updated");
}

function update_loan_account_s($loan_account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($loan_account)." 
	WHERE name = ".db_escape('s_loan_account');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_advance_account_s($advance_account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($advance_account)." 
	WHERE name = ".db_escape('s_advance_account');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_salary_account_s($salary_account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($salary_account)." 
	WHERE name = ".db_escape('s_salary_account');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_bonus_account_s($bonus_account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($bonus_account)." 
	WHERE name = ".db_escape('s_bonus_account');
    db_query($sql,"The Sys Pref could not be updated");
}
function update_deduction_account_s($deduction_account)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($deduction_account)." 
	WHERE name = ".db_escape('s_deduction_account');
    db_query($sql,"The Sys Pref could not be updated");
}
function get_sys_pay_pref_s($name)
{
    $sql = "SELECT account FROM ".TB_PREF."sys_pay_pref 
	WHERE name = ".db_escape($name);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_service_charges_account_s($employee_id)
{
    $sql = "SELECT payroll_expenses FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];

}
function get_service_charges_payable_account_s($employee_id)
{
    $sql = "SELECT payroll_liabilty FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_service_income_tax_account_s($employee_id)
{
    $sql = "SELECT tax_liability FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}
function get_service_eobi_account_s($employee_id)
{
    $sql = "SELECT deduction_account FROM ".TB_PREF."employee 
	WHERE employee_id = ".db_escape($employee_id);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}

function get_accounts_name_through_code_s($account_code)
{
    $sql = "SELECT account_name FROM ".TB_PREF."chart_master 
	WHERE account_code = ".db_escape($account_code);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}

function get_sys_pay_pref_eobi_s($name)
{
    $sql = "SELECT account FROM ".TB_PREF."sys_pay_pref 
	WHERE name = ".db_escape($name);
    $result = db_query($sql,"could not get department");
    $myrow = db_fetch($result);
    return $myrow[0];
}

//iqra

function update_mon($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('monday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}
function update_tue($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('tuesday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}
function update_wed($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('wednesday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}function update_thur($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('thursday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}function update_fri($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('friday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}function update_sat($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('saturday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}function update_sun($weekday)
{
    $sql = "UPDATE ".TB_PREF."sys_pay_pref SET 
	account=".db_escape($weekday)." 
	WHERE name = ".db_escape('sunday')


    ;
    db_query($sql,"The Sys Pref could not be updated");
}



?>