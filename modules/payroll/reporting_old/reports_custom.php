<?php

global $reports, $dim;

$reports->addReportClass(_('Payroll'), RC_PAYROLL);	
$reports->addReport(RC_PAYROLL, 2023, _('Employee Salary Advice'),
	array(	_('Month') => 'MONTH',
			_('Employee') => 'EMPLOYEE',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));		
$reports->addReport(RC_PAYROLL, 2012, _('Bank Letters'),
	array(	_('Month') => 'MONTH',			
			_('Banks') => 'BANK_ACCOUNTS',
			_('Letter Date') => 'DATE',
			_('Cheque') => 'TEXT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
/*
$reports->addReport(RC_PAYROLL, 2013, _('Salary Sheet'),
	array(	_('Month') => 'MONTH',			
			_('Department') => 'DEPT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
*/
$reports->addReport(RC_PAYROLL, 2014, _('Salary Sheet of Payroll'),
	array(	_('Division') => 'DIVISION',
			_('Project') => 'PROJECT_NEW',
			_('Location') => 'LOCATION',
			_('Employee') => 'EMPLOYEE',
			_('Month') => 'MONTH',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 20145, _('Salary-Difference Sheet of Payroll'),
	array(	_('Month') => 'MONTH',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2024, _('Employee Tax Report'),
	array(	_('Month') => 'MONTH',			
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2015, _('Sheet of Advance'),
	array(	_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',	
	       _('Approval') => 'UPROVE',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_PAYROLL, 2016, _('Sheet of Attendance'),
      array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));	
$reports->addReport(RC_PAYROLL, 2017, _('Sheet of Overtime'),
  		array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2018, _('Sheet of Leave'),
  		array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',
			 _('Approval') => 'UPROVE',			
			//_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2019, _('Employee Master Sheet'),
 			array( _('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));		
$reports->addReport(RC_PAYROLL, 2020, _('List of Departments'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2021, _('List of Designations'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 2022, _('List of Grades'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20222, _('List of employees'),
	array(

//		_('DOB') => 'DATEENDTAX',
		_('Employee') => 'EMPLOYEE',
		_('Designation') => 'DESIGNATION',
		_('Orientation') => 'ORIENTATION',

		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20223, _('List of man month'),
	array(
		_('Division') => 'DIVISION',
		_('Project') => 'PROJECT_NEW',
		_('Location') => 'LOCATION',
		_('Employee') => 'EMPLOYEE',
		_('Month') => 'MONTH',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(RC_PAYROLL, 20224, _('Employee Increment History'),
	array(
		_('Employee') => 'EMPLOYEE',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20225, _('Salary Payment Voucher'),
	array(
			_('Employee') => 'EMPLOYEE',

			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));



$reports->addReport(RC_PAYROLL, 20226, _('Leave Encashment Report'),
	array(
			_('Employee') => 'EMPLOYEE',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20227, _('Full & Final Settlement Report'),
	array(
			_('Employee') => 'EMPLOYEE',

			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20228, _('Online Bank Transfer - Salary '),
	array(
		_('Employee') => 'EMPLOYEE',

		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_PAYROLL, 20229, _('PO Monthly List - Employee'),
	array(
//		_('Employee') => 'EMPLOYEE',
		_('Accounts') => 'BANK_ACCOUNTS_NAME',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));


?>
