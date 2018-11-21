<?php

$path_to_root="..";
$page_security = 'SA_OPEN';
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reports_classes.inc");
$js = "";
if ($SysPrefs->use_popup_windows && $SysPrefs->use_popup_search)
	$js .= get_js_open_window(900, 500);
if (user_use_date_picker())
	$js .= get_js_date_picker();
 
add_js_file('reports.js');

page(_($help_context = "Reports and Analysis"), false, false, "", $js);

$reports = new BoxReports;
$pref = get_company_prefs();
$dim = get_company_pref('use_dimension');

$reports->addReportClass(_('Customer Reports'), RC_CUSTOMER);
$reports->addReport(RC_CUSTOMER, 101, _('Customer &Balances'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10116, _('Customer &Balances'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10101, _('Customer &Balances'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10115, _('Customer &Balances - CustWise'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Dimension')." 1" =>  'DIMENSIONS1',
        _('Dimension')." 2" =>  'DIMENSIONS2',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Show Balance') => 'YES_NO_DEFAULT_YES',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMER, 1014, _('Customer Balances Summary'),
	array(	_('End Date') => 'DATE',
			_('Group By') => 'MONTHLY_SALE_AREA_GROUP',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',	
			_('Dimension Filter') => 'DIMENSIONS1',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',				
			_('Currency Filter') => 'CURRENCY',
			//_('Show Also Allocated') => 'YES_NO',
			_('Suppress Zeros') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10199, _('Customer &Ledger'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Dimension')." 1" =>  'DIMENSIONS1',
        _('Dimension')." 2" =>  'DIMENSIONS2',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Show Balance') => 'YES_NO_DEFAULT_YES',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMER, 101404, _('Monthly Summay Report'),
    array( _('End Date') => 'DATE',
        _('Group By') => 'MONTHLY_SALE_AREA_GROUP',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Dimension Filter') => 'DIMENSIONS1',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Graphics') => 'GRAPHIC',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));			
$reports->addReport(RC_CUSTOMER, 10144, _('Daily Sales Account Summary'),
    array( _('End Date') => 'DATE',
        _('Group By') => 'MONTHLY_SALE_AREA_GROUP',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Dimension Filter') => 'DIMENSIONS1',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Graphics') => 'GRAPHIC',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMER, 1010,_('Customer Statements'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
			_('Type') => 'SYS_TYPES_CUST',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',			
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO_DEFAULT_YES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 1012, _('Customer Balances - Detailed'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10122, _('Customer Balances - Detailed'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10123, _('Customer Balances - Detailed'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Types') => 'SYS_TYPES_CUST',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10124, _('Sales Return Summary'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 121, _('Agreement Invoice Receive Report'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Dimension')." 1" =>  'DIMENSIONS1',
        _('Dimension')." 2" =>  'DIMENSIONS2',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Show Balance') => 'YES_NO_DEFAULT_YES',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10117, _('Customer Balances - Detailed'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
/*			
$reports->addReport(RC_CUSTOMER, 102, _('&Aged Customer Analysis'),
	array(	_('End Date') => 'DATE',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Sales Man') => 'SALESMEN',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
			_('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
			_('Summary Only') => 'YES_NO',
			_('Suppress Zeros') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
*/
if ($dim > 0)
{

         
         
$reports->addReport(RC_CUSTOMER, 102, _('&Aged Customer Analysis'),
   array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Dimension')." 1" =>  'DIMENSIONS1',
         _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Sales Groups') => 'GROUPS',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
         
$reports->addReport(RC_CUSTOMER, 10211, _('&Aged Customer Analysis - Beta'),
    array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Dimension')." 1" =>  'DIMENSIONS1',
         _('Currency Filter') => 'CURRENCY',
         _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
}

else
{
    $reports->addReport(RC_CUSTOMER, 102, _('&Aged Customer Analysis'),
   array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Sales Groups') => 'GROUPS',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
         
         
$reports->addReport(RC_CUSTOMER, 10211, _('&Aged Customer Analysis - Beta'),
    array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Currency Filter') => 'CURRENCY',
         _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
}

if ($dim > 0)
{

         
         
         $reports->addReport(RC_CUSTOMER, 10223, _('&Aged Customer Analysis - INVOICE DATE'),
   array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Dimension')." 1" =>  'DIMENSIONS1',
         _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
}

else
{
    $reports->addReport(RC_CUSTOMER, 10223, _('&Aged Customer Analysis - INVOICE DATE'),
   array( _('End Date') => 'DATE',
         _('Past Due Days Interval') => 'DUE_INTERVAL',
         _('Orientation') => 'ORIENTATION',
         _('Customer') => 'CUSTOMERS_NO_FILTER_',
         _('Sales Man') => 'SALESMEN',
         _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
         _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
         _('3/6 Columns') => 'COLUMNS',
         _('No. of Days on the basis of:') => 'INV_DUE',
         _('Summary Only') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Graphics') => 'GRAPHIC',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
}


$reports->addReport(RC_CUSTOMER, 10221, _('&Aged Customer Analysis - 2'),
    array(	_('End Date') => 'DATE',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Sales Man') => 'SALESMEN',
        _('Currency Filter') => 'CURRENCY',
        _('Types:') => 'SYS_TYPES_CUST',
        _('Show Also Allocated') => 'YES_NO',
        _('Summary Only') => 'YES_NO',
        _('Suppress Zeros') => 'YES_NO',
        _('Graphics') => 'GRAPHIC',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
        
if ($dim > 0)
{
$reports->addReport(RC_CUSTOMER, 10222, _('&Aged Customer Analysis-Allocated'),
        array( _('End Date') => 'DATE',
            _('Past Due Days Interval') => 'DUE_INTERVAL',
            _('Orientation') => 'ORIENTATION',
            _('Customer') => 'CUSTOMERS_NO_FILTER_',
            _('Sales Man') => 'SALESMEN',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
            _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
            _('Show Differences Only') => 'YES_NO_DEFAULT_YES',
            _('3/6 Columns') => 'COLUMNS',
            _('No. of Days on the basis of:') => 'INV_DUE',
            _('Summary Only') => 'YES_NO',
            _('Suppress Zeros') => 'YES_NO',
            _('Graphics') => 'GRAPHIC',
            _('Comments') => 'TEXTBOX',
            _('Destination') => 'DESTINATION'));
}

else
{
    $reports->addReport(RC_CUSTOMER, 10222, _('&Aged Customer Analysis-Allocated'),
        array( _('End Date') => 'DATE',
            _('Past Due Days Interval') => 'DUE_INTERVAL',
            _('Orientation') => 'ORIENTATION',
            _('Customer') => 'CUSTOMERS_NO_FILTER_',
            _('Sales Man') => 'SALESMEN',
            _('Currency Filter') => 'CURRENCY',
            _('Types') => 'SYS_TYPES_CUST',
            _('Show Also Allocated') => 'YES_NO_DEFAULT_YES',
            _('Show Differences Only') => 'YES_NO_DEFAULT_YES',
            _('3/6 Columns') => 'COLUMNS',
            _('No. of Days on the basis of:') => 'INV_DUE',
            _('Summary Only') => 'YES_NO',
            _('Suppress Zeros') => 'YES_NO',
            _('Graphics') => 'GRAPHIC',
            _('Comments') => 'TEXTBOX',
            _('Destination') => 'DESTINATION'));
}
$reports->addReport(RC_CUSTOMER, 10141, _('Customer Outstanding Summary Report'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Branch') => 'BRANCH_LIST',
        _('Dimension Filter') => 'DIMENSIONS1',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Groupwise') => 'GROUPS',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

	$reports->addReport(RC_CUSTOMER, 10143, _('Customer Outstanding Summary Report - VETZ'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER_',
        _('Branch') => 'BRANCH_LIST',
        _('Dimension Filter') => 'DIMENSIONS1',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Groupwise') => 'GROUPS',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));
        
    $reports->addReport(RC_CUSTOMER, 10555, _('Booking Summary'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Items') => 'ITEMS_ALL_',
        _('Inventory Category') => 'CATEGORIES',
        _('Stock Location') => 'LOCATIONS',
        _('Back Orders Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Group By') => 'YESNO_ORDERBY_ITEM',
        _('Destination') => 'DESTINATION'));
		
$reports->addReport(RC_CUSTOMER, 1015 ,_('Customer Movements'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));

			
$reports->addReport(RC_CUSTOMER, 1023 , _('Customer &Invoices'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Group By') => 'CUST_INV_SALESMAN',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',			
			_('Currency Filter') => 'CURRENCY',
 			_('Summary Only') => 'YES_NO',
 			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',			
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 1016 , _('Customer &Receipts'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',			
			_('Currency Filter') => 'CURRENCY',
 			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',			
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 1026 , _('Customer &Receipts'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',			
			_('Currency Filter') => 'CURRENCY',
 			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',			
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 10160 , _('Customer &Receipts'),
   array( _('Start Date') => 'DATEBEGIN',
         _('End Date') => 'DATEENDM',
         _('Customer') => 'CUSTOMERS_NO_FILTER',
         _('Sales Areas') => 'ZONES',
         _('Sales Man') => 'SALESMEN',
         _('Currency Filter') => 'CURRENCY',
         _('Summary Only') => 'YES_NO',
         _('Comments') => 'TEXTBOX',
         _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMER, 10121,_('POS Daily Sheet'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Location') => 'LOCATION_NO_FILTER',
// 			_('Location') => 'DIMENSIONS1',
			_('Payment Terms') => 'PAYMENT_TERMS',
			_('SLS Group') => 'COMBO3',
			_('Cashier') => 'SALESMEN',			
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO_DEFAULT_YES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 101211,_('Tax Customer Listing'),
	array(
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));			

			
$reports->addReport(RC_CUSTOMER, 1011, _('Cust &Bal - Detailed 2'),
	array(	_('Start Date') => 'DATEBEGIN',

			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER_',
			_('Dimension Filter') => 'DIMENSIONS1',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',
	    	_('Location') => 'LOCATIONS',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	
$reports->addReport(RC_CUSTOMER, 1013,_('Cust Bal - Branch Wise'),
   array( _('Start Date') => 'DATEBEGIN',
         _('End Date') => 'DATEENDM',
         _('Customer') => 'CUSTOMERS_NO_FILTER',
         _('Branch') => 'BRANCH_LIST',
         _('Sales Areas') => 'ZONES',
         _('Sales Man') => 'SALESMEN',        
         _('Currency Filter') => 'CURRENCY',
         _('Suppress Zeros') => 'YES_NO',
         _('Comments') => 'TEXTBOX',
         _('Orientation') => 'ORIENTATION',
         _('Destination') => 'DESTINATION'));
			
// $reports->addReport(RC_CUSTOMER, 101112, _('Customer &Balances - Detailed With Banks'),
// 	array(	_('Start Date') => 'DATEBEGIN',
// 			_('End Date') => 'DATEENDM',
// 			_('Customer') => 'CUSTOMERS_NO_FILTER',
// 			_('Sales Areas') => 'ZONES',
// 			_('Sales Man') => 'SALESMEN',
// 			_('Currency Filter') => 'CURRENCY',
// 			_('Suppress Zeros') => 'YES_NO',
// 			_('Comments') => 'TEXTBOX',
// 			_('Compare to') => 'COMPARE',
// 			_('Account Tags') =>  'ACCOUNTTAGS',
// 			_('Graphics') => 'GRAPHIC',
// 			_('Orientation') => 'ORIENTATION',
// 			_('Destination') => 'DESTINATION'));
			

        
        
$reports->addReport(RC_CUSTOMER, 1021, _('Customer Sales History'),
	array(	_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',				
			_('Currency Filter') => 'CURRENCY',
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));
			
			      
$reports->addReport(RC_CUSTOMER, 1022, _('Customer Sales History 1'),
	array(	
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',				
			_('Currency Filter') => 'CURRENCY',
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 1024, _('Annual Sales &Report'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Sales Areas') => 'ZONES',
        _('Groupwise') => 'GROUPS',
        _('Sales Man') => 'SALESMEN',
        _('Currency Filter') => 'CURRENCY',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));
        
// $reports->addReport(RC_CUSTOMER, 10111, _('Cust &Bal - Detailed 3'),
// 	array(	_('Start Date') => 'DATEBEGIN',
// 			_('End Date') => 'DATEENDM',
// 			_('Customer') => 'CUSTOMERS_NO_FILTER',
// 			_('Dimension Filter') => 'DIMENSIONS1',
// 			_('Sales Areas') => 'ZONES',
// 			_('Sales Man') => 'SALESMEN',			
// 			_('Currency Filter') => 'CURRENCY',
// 			_('Suppress Zeros') => 'YES_NO',
// 			_('Comments') => 'TEXTBOX',
// 			_('Orientation') => 'ORIENTATION',
// 			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMER, 10142, _('Recovery Against Target Report'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Branch') => 'BRANCH_LIST',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Groupwise') => 'GROUPS',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMER, 10114, _('Party Wise Tax Deduction	'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Areas') => 'ZONES',
			_('Sales Man') => 'SALESMEN',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

	$reports->addReport(RC_CUSTOMER, 30432, _('Party Wise Tax Deduction2'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',

		_('Customer') => 'CUSTOMERS_NO_FILTER',
		
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));


$reports->addReport(RC_CUSTOMER, 1017, _('Salesman Balances'),
	       array(	_('Start Date') => 'DATE',
			_('End Date') => 'DATE',	
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMER, 1018, _('&Salesman Recovery - Summary'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Sales Man') => 'SALESMEN',
				_('Dimension Filter') => 'DIMENSIONS1',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Destination') => 'DESTINATION'));
			
// 	$reports->addReport(RC_CUSTOMER, 10188, _('&Salesman Recovery - Summary - dimensions'),
// 	array(	_('Start Date') => 'DATEBEGIN',
// 		_('End Date') => 'DATEENDM',
// 		_('Sales Man') => 'SALESMEN',
// 		_('Dimension Filter') => 'DIMENSIONS1',
// 		_('Currency Filter') => 'CURRENCY',
// 		_('Suppress Zeros') => 'YES_NO',
// 		_('Comments') => 'TEXTBOX',
// 		_('Destination') => 'DESTINATION'));
		$reports->addReport(RC_CUSTOMER, 10181, _('&Salesman Recovery - Sales Target Customer Wise'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Sales Man') => 'SALESMEN',
        _('Dimension Filter') => 'DIMENSIONS1',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));
$reports->addReport(RC_CUSTOMER, 1019, _('Yearly Sales Comparison'),
    array(
        _('Comments') => 'TEXTBOX',
        _('Types') => 'SYS_TYPES_CUST',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMER, 1025, _('Monthly Sales Comparison'),
    array(
        _('Comments') => 'TEXTBOX',
        _('Year') => 'TRANS_YEARS',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
// $reports->addReport(RC_CUSTOMER, 1020, _('Customer &Balances-Summary (New)'),
// 	array(	_('Start Date') => 'DATEBEGIN',
// 		_('End Date') => 'DATEENDM',
// 		_('Customer') => 'CUSTOMERS_NO_FILTER',
// 		_('Dimension Filter') => 'DIMENSIONS1',
// 		_('Show Balance') => 'YES_NO',
// 		_('Currency Filter') => 'CURRENCY',
//     _('Sales Areas') => 'ZONES',
// 		_('Suppress Zeros') => 'YES_NO',
// 		_('Comments') => 'TEXTBOX',
// 		_('Orientation') => 'ORIENTATION',
// 		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMER, 103, _('Customer &Detail Listing'),
	array(	_('Activity Since') => 'DATEBEGIN',
			_('Sales Areas') => 'ZONES',
			_('Sales Folk') => 'SALESMEN',
			_('Activity Greater Than') => 'TEXT',
			_('Activity Less Than') => 'TEXT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_CUSTOMER, 1031, _('New Customer &Detail Listing'),
	array(	_('Activity Since') => 'DATEBEGIN',
		//	_('Sales Areas') => 'AREAS',
		_('Sales Types') => 'SALESTYPES2',
		_('Sales Folk') => 'SALESMEN',
		_('Activity Greater Than') => 'TEXT',
		_('Activity Less Than') => 'TEXT',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		
$reports->addReport(RC_CUSTOMER, 104, _('&Price Listing'),
	array(	_('Currency Filter') => 'CURRENCY',
			_('Inventory Category') => 'CATEGORIES',
			_('Show Pictures') => 'YES_NO',
			_('Show GP %') => 'YES_NO',
			_('Select Price/Standard Cost') => 'PRICE',
			_('Sales Types') => 'SALESTYPES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMER, 105, _('&Order Status Listing'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Items') => 'ITEMS_ALL_',
			_('Inventory Category') => 'CATEGORIES',
			_('Stock Location') => 'LOCATIONS',
			_('Back Orders Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Group By') => 'YESNO_ORDERBY_ITEM',
            _('Delivered Record') => 'YES_NO_DELIVERED',
			_('Destination') => 'DESTINATION'));
			
						
$reports->addReport(RC_CUSTOMER, 106, _('&Salesman Listing'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

			$reports->addReport(RC_CUSTOMER, 114, _('Sales &Summary Report'),
	array(	_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',
			_('Tax Id Only') => 'YES_NO',
            _('Customer') => 'CUSTOMERS_NO_FILTER',
            _('Sales Areas') => 'ZONES',
            _('Sales Man') => 'SALESMEN',
            _('Groupwise') => 'GROUPS',
            _('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

			
			$reports->addReport(RC_CUSTOMER, 1051, _('Bulk Delivery Order Report(Date wise)'),
	array(	_('Start Date') => 'DATE',
			_('End Date') => 'DATE',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Item Location') => 'ITEM_LOCATIONS',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
//S.H
$reports->addReport(RC_CUSTOMER, 10511, _('Bulk Delivery Order(Item Location wise)'),
	array(	
		_('Req. Delivery Date') => 'DATE',
//		_('End Date') => 'DATE',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Item Location') => 'ITEM_LOCATIONS',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
			
			
			
			$reports->addReport(RC_CUSTOMER, 111120, _('Bulk Delivery Order(Item Location wise) (New)'),
	array(	
	_('Req. Delivery Date') => 'DATE',
//		_('End Date') => 'DATE',
		_('Inventory Category') => 'CATEGORIES',
		_('Salesmen') => 'SALESMEN',
        _('Location') => 'LOCATIONS',
		_('Item Location') => 'ITEM_LOCATIONS',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
			
			
			
			
	$reports->addReport(RC_CUSTOMER, 1052, _('Bulk Delivery Order Report(Ref wise)'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReportClass(_('Customer Forms'), RC_CUSTOMERFORMS);
$reports->addReport(RC_CUSTOMERFORMS, 10112, _('Query Inquiry'),
     array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Sale Person') => 'USERS',
        _('Items') => 'ITEMS_P',
        _('Resource') => 'RESOURCE',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
        $reports->addReport(RC_CUSTOMERFORMS, 108899, _('Customer Account Opening'),
    array(
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 107, _('Print &Invoice 1'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
    $reports->addReport(RC_CUSTOMERFORMS, 10779, _('Print &Invoice 1'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
     $reports->addReport(RC_CUSTOMERFORMS, 10780, _('Print &Invoice'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
    $reports->addReport(RC_CUSTOMERFORMS, 10781, _('Print &Invoice'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX'
    ));
    
    $reports->addReport(RC_CUSTOMERFORMS, 10782, _('Print &Invoice'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 1070, _('Print &Invoice 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
    
$reports->addReport(RC_CUSTOMERFORMS, 1071, _('Print &Invoice 3'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1072, _('Print &Invoice 5'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1073, _('Print &Invoice 23'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1074, _('Print &Commercial Invoice'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1075, _('Print &Invoices 7'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1076, _('Print &Invoice 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 1077, _('Print &Invoice 25'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1078, _('Print &Invoices 10'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1079, _('Proforma &Invoice - 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10710, _('Print &Invoice 15'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 10711, _('Print Commercial &Invoice 9'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10712, _('Print &Invoice 2 - barcode'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10713, _('Print Performa &Invoice 1'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10714, _('Print &Invoice 11'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10715, _('Print &Invoice 21 '),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10716, _('Print &Invoice 17'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10717, _('Print &Invoice 25'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10718, _('Print &Invoice 19'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10719, _('Print &Invoices 7_new'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10720, _('Print &Invoices 9'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10721, _('Print &Invoices 14'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10722, _('Print &Invoice 1 - A5'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10723, _('Print &Invoices 13'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10724, _('Print &Invoice 12'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10725, _('Print &Invoices 16'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10726, _('Print Commercial &Invoice 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10727, _('Print Performa &Invoice 2'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 10728, _('&Print Performa Invoice(P)'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10729, _('Print &Invoice 6'),
    array(	_('From') => 'PI',
        _('To') => 'PI',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10730, _('Print &Invoice UOM'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10731, _('Print &Invoice 22'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));


$reports->addReport(RC_CUSTOMERFORMS, 10732, _('Print &Invoice 24'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));


$reports->addReport(RC_CUSTOMERFORMS, 10733, _('Print &Invoice(A5)'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10734, _('Print Sales  &Invoice 27'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10735, _('Commercial &Invoice'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10736, _('Print &Invoices 20'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 10737, _('Print &Invoice 27'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10738, _('Print &Invoice c_'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10739, _('Print &Invoice cc_'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10740, _('Print &Invoice 29'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10741, _('Print &Invoice 18'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10742, _('Print &Invoice 25'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 10743, _('Commercial &Invoice - 4'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 10744, _('Commercial &Invoice - 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10745, _('Print &Invoice 26'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10746, _('Commercial &Invoice - 5'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10747, _('Commercial &Invoice - 3'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 10748, _('Packing List &Invoice - 6'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10749, _('Proforma &Invoice - 1'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 10751, _('Commercial &Invoice - 6'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));



$reports->addReport(RC_CUSTOMERFORMS, 10753, _('Print &Invoice 30'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
$reports->addReport(RC_CUSTOMERFORMS, 10754, _('Print &Invoices'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10756, _('Print &Invoice 36'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
$reports->addReport(RC_CUSTOMERFORMS, 10757, _('Print &Invoice 37'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 10758, _('Print &Invoice 38'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10759, _('Print &Invoice 39 - A5'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10760, _('Print &Invoice 50'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10761, _('Print &Invoice 51'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
    
    $reports->addReport(RC_CUSTOMERFORMS, 10762, _('Print &Invoice 52'),
   	array( _('Customer') => 'CUSTOMERS_NO_FILTER',
		_('From') => 'INVOICE_NEW',
		_('To') => 'INVOICE_NEW',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

    
    
    $reports->addReport(RC_CUSTOMERFORMS, 10763, _('Print &Invoice 53'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    $reports->addReport(RC_CUSTOMERFORMS, 10764, _('Print &Invoice 54'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
       $reports->addReport(RC_CUSTOMERFORMS, 10766, _('Print &Invoice 55'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
 $reports->addReport(RC_CUSTOMERFORMS, 10767, _('Commercial &Invoice 11'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));   
    
$reports->addReport(RC_CUSTOMERFORMS, 10768, _('Commercial &Invoice (Warranty)'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 10769, _('Print &Invoices 49'),
   array( _('From') => 'INVOICE',
      _('To') => 'INVOICE',
      _('Currency Filter') => 'CURRENCY',
      _('email Customers') => 'YES_NO',
      _('Payment Link') => 'PAYMENT_LINK',
      _('Comments') => 'TEXTBOX',
      _('Customer') => 'CUSTOMERS_NO_FILTER',
      _('Orientation') => 'ORIENTATION'
   ));
$reports->addReport(RC_CUSTOMERFORMS, 10775, _('Print &Invoices 7'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    $reports->addReport(RC_CUSTOMERFORMS, 10765, _('Print &Commercial Invoice 10'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 1081, _('Print &Invoice 2'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));

$reports->addReport(RC_CUSTOMERFORMS, 10776, _('Print &Invoice 50'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
    $reports->addReport(RC_CUSTOMERFORMS, 10778, _('Print &Packing List'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
     $reports->addReport(RC_CUSTOMERFORMS, 107322, _('Print &Invoice 51'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
//$reports->addReport(RC_CUSTOMERFORMS, 10722, _('Print &Invoices 17'),
//    array(	_('From') => 'INVOICE',
//        _('To') => 'INVOICE',
//        _('Currency Filter') => 'CURRENCY',
//        _('email Customers') => 'YES_NO',
//        _('Payment Link') => 'PAYMENT_LINK',
//        _('Comments') => 'TEXTBOX',
//        _('Orientation') => 'ORIENTATION'));




//$reports->addReport(RC_CUSTOMERFORMS, 10722, _('Print &Invoices 8'),
//    array(	_('From') => 'INVOICE',
//        _('To') => 'INVOICE',
//        _('Currency Filter') => 'CURRENCY',
//        _('email Customers') => 'YES_NO',
//        _('Payment Link') => 'PAYMENT_LINK',
//        _('Comments') => 'TEXTBOX',
//        _('Orientation') => 'ORIENTATION'));


//petro
//$reports->addReport(RC_CUSTOMERFORMS, 1076, _('Print Sales Tax &Invoices 8'),
//    array(	_('From') => 'INVOICE',
//        _('To') => 'INVOICE',
//        _('Currency Filter') => 'CURRENCY',
//        _('email Customers') => 'YES_NO',
//        _('Payment Link') => 'PAYMENT_LINK',
//        _('Comments') => 'TEXTBOX',
//        _('Orientation') => 'ORIENTATION'));


//$reports->addReport(RC_CUSTOMERFORMS, 10712, _('Print Zero percent Sales Tax '),
//    array(	_('From') => 'INVOICE',
//        _('To') => 'INVOICE',
//        _('Currency Filter') => 'CURRENCY',
//        _('email Customers') => 'YES_NO',
//        _('Payment Link') => 'PAYMENT_LINK',
//        _('Comments') => 'TEXTBOX',
//        _('Orientation') => 'ORIENTATION'));





// $reports->addReport(RC_CUSTOMERFORMS, 10904, _('Print Performa &Invoice SST'),
// 	array(	_('From') => 'ORDERS',
// 			_('To') => 'ORDERS',
// 			_('Currency Filter') => 'CURRENCY',
// 			_('Email Customers') => 'YES_NO',
// 			_('Print as Quote') => 'YES_NO',
// 			_('Comments') => 'TEXTBOX',
// 			_('Orientation') => 'ORIENTATION'));


//petro end
/*
$reports->addReport(RC_CUSTOMERFORMS, 1077, _('Print &Invoices - new'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Orientation') => 'ORIENTATION'
	));
*/

$reports->addReport(RC_CUSTOMERFORMS, 118, _('Print POS &Invoices 4 Inch'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1180, _('Print POS &Invoices 3 Inch'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1181, _('Print POS &Invoices 3 Inch-New'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1182, _('Print POS &Invoices 4 Inch-New'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1183, _('Print POS &Invoices'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 1184, _('Print POS &Invoices 6 (3 Inch)'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
		
		        
$reports->addReport(RC_CUSTOMERFORMS, 1185, _('Print POS &Invoices 7 (3 Inch)'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 107779, _('Print Daily Sales Report'),
	array(	_('From') => 'DATEBEGIN',
		_('To') => 'DATEENDM',
        _('User') => 'USERS'));

$reports->addReport(RC_CUSTOMERFORMS, 116, _('Print Sales Tax &Invoices '),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1160, _('Print Zero percent Sales Tax '),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1161, _('Print Sales Tax &Invoice 1'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));
$reports->addReport(RC_CUSTOMERFORMS, 1162, _('Print Sales Tax &Invoice 2'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('With Logo') => 'YES_NO_LOGO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));
$reports->addReport(RC_CUSTOMERFORMS, 1163, _('Print Sales Tax &Invoice 3'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        

$reports->addReport(RC_CUSTOMERFORMS, 1164, _('Print Sales Tax &Invoice 4'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('With Logo') => 'YES_NO_LOGO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Orientation') => 'ORIENTATION'
	));
$reports->addReport(RC_CUSTOMERFORMS, 1165, _('Print Sales Tax &Invoice 5 - barcode'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Orientation') => 'ORIENTATION'
	));

        
        $reports->addReport(RC_CUSTOMERFORMS, 1166, _('Print Sales Tax &Invoice 6 '),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        $reports->addReport(RC_CUSTOMERFORMS, 1167, _('Print Sales Tax &Invoice 7'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));
        $reports->addReport(RC_CUSTOMERFORMS, 1168, _('Print Sales Tax &Invoice 8'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        $reports->addReport(RC_CUSTOMERFORMS, 1169, _('Print Sales Tax &Invoice 9'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('With Logo') => 'YES_NO_LOGO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));

      $reports->addReport(RC_CUSTOMERFORMS, 1170, _('Print Sales Tax &Invoice 18'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));
        
        $reports->addReport(RC_CUSTOMERFORMS, 11610, _('Print Sales Tax Invoice 10'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));



      $reports->addReport(RC_CUSTOMERFORMS, 11611, _('Print Sales Tax &Invoice 11'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));

$reports->addReport(RC_CUSTOMERFORMS, 11612, _('Print Sales Tax &Invoice 12'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    $reports->addReport(RC_CUSTOMERFORMS, 11613, _('Print Sales Tax &Invoice 13'),
    array(	_('From') => 'INVOICE_TAX',
        _('To') => 'INVOICE_TAX',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
  $reports->addReport(RC_CUSTOMERFORMS, 11614, _('Print Sales Tax &Invoice 14'),
    array(	_('From') => 'INVOICE_TAX2',
        _('To') => 'INVOICE_TAX2',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
    
          
    $reports->addReport(RC_CUSTOMERFORMS, 11615, _('Print Sales Tax &Invoice 15'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('With Logo') => 'YES_NO_LOGO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));

$reports->addReport(RC_CUSTOMERFORMS, 11616, _('Print Sales Tax &UOM'),
    array(	_('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 11617, _('Print Sales Tax &Invoice 16'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 11618, _('Print Sales Tax &Invoice 17'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 11619, _('Print Sales Tax &Invoice 18'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
        
$reports->addReport(RC_CUSTOMERFORMS, 11620, _('Print Sales Tax &Invoice 19'),
    array( _('From') => 'INVOICE',
        _('To') => 'INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Payment Link') => 'PAYMENT_LINK',
        _('Comments') => 'TEXTBOX',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Orientation') => 'ORIENTATION'
    ));
         $reports->addReport(RC_CUSTOMERFORMS, 1173, _('Print Sales Tax &Invoice 7'),
	array(	_('From') => 'INVOICE',
			_('To') => 'INVOICE',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Orientation') => 'ORIENTATION'
));


$reports->addReport(RC_CUSTOMERFORMS, 1195, _('Print Sales Tax &Invoice 8'),
	array(	_('From') => 'INVOICE',
		_('To') => 'INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('email Customers') => 'YES_NO',
		_('With Logo') => 'YES_NO_LOGO',
		_('Payment Link') => 'PAYMENT_LINK',
		_('Comments') => 'TEXTBOX',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Orientation') => 'ORIENTATION'
	));

    
$reports->addReport(RC_CUSTOMERFORMS, 109, _('&Print Sales Orders '),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

	$reports->addReport(RC_CUSTOMERFORMS, 1090, _('&Print Sales Orders 1'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('With Logo') => 'YES_NO_LOGO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			


$reports->addReport(RC_CUSTOMERFORMS, 1091, _('&Print Sales Order - POS '),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1092, _('&Print Sales Orders 2'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1093, _('&Print Sales Orders 3'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1094, _('&Print Sales Orders 4'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
//PETRO REPORTS
$reports->addReport(RC_CUSTOMERFORMS, 1095, _('&Print Sales Orders 5'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 1096, _('&Print Sale &Order 6'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Sales Areas') => 'ZONES',
        _('Sales Man') => 'SALESMEN',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION',
        _('SALES GROUP') => 'SALESGROUP'
    ));
$reports->addReport(RC_CUSTOMERFORMS, 1097, _('&Print Sales Orders 7'),
    array(
        _('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 1098, _('Sale &Order - Summary 8'),
    array(
        _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        //_('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Dimension') => 'DIMENSION',
        _('Destination') => 'DESTINATION',

    ));
$reports->addReport(RC_CUSTOMERFORMS, 1099, _('&Print Sales Orders 9- Barcode '),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10910, _('&Print Sales Orders 10'),
   array( _('From') => 'ORDERS',
         _('To') => 'ORDERS',
         _('Show Amounts/Prices') => 'YES_NO_VOIDED',
         _('Currency Filter') => 'CURRENCY',
         _('Email Customers') => 'YES_NO',
         _('Print as Quote') => 'YES_NO',
         _('Comments') => 'TEXTBOX',
         _('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 10911, _('&Print Sales Orders  11'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10912, _('&Print Sales Orders 12'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10913, _('&Print Sales Orders 13'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10914, _('Print Sales &Orders UOM 14'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));



$reports->addReport(RC_CUSTOMERFORMS, 10915, _('Print Sales &Orders UOM (A5) 15'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10916, _('Sale &Order 16'),
    array(
        _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        //_('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Dimension') => 'DIMENSION',
        _('Destination') => 'DESTINATION',

    ));

$reports->addReport(RC_CUSTOMERFORMS, 10917, _('&Print Sales Orders 17'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 10918, _('&Print Sales Orders 18'),
	array(	_('From') => 'ORDERS',
		_('To') => 'ORDERS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Print as Quote') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10919, _('&Print Sales Orders 19'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10920, _('Total Sales Order'),
    array(
        _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        //_('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Dimension') => 'DIMENSION',
        _('Destination') => 'DESTINATION',

    ));

$reports->addReport(RC_CUSTOMERFORMS, 10921, _('Print Sales &Orders UOM (PROFORMA INVOICE)'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 10923, _('&Print Sales Orders 20'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 10924, _('&Print Sales Orders 21'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10925, _('&Print Sales Orders 21'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10926, _('&Print Sales Orders 22'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 10927, _('&Print Sales Orders 27'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
		
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 10928, _('&Print Sales Orders 28'),
	array(	_('From') => 'ORDERS',
			_('To') => 'ORDERS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Print as Quote') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 10929, _('&Print Sales Orders  - A5'),
    array(	_('From') => 'ORDERS',
        _('To') => 'ORDERS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 10930, _('&Print Sales Orders'),
	array(	_('From') => 'ORDERS',
		_('To') => 'ORDERS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Print as Quote') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 10982, _('Total Sales Order Item Location Wise'),
	array(		    
	    _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		//_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Dimension') => 'DIMENSION',
		_('Destination') => 'DESTINATION',
		));


$reports->addReport(RC_CUSTOMERFORMS, 10132, _('Employee Monthly Performance'),
    array(	_('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Status') => 'STATUS',
        _('Source') => 'SOURCE',
        _('Users') => 'USER',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

/*$reports->addReport(RC_CUSTOMERFORMS, 10984, _('Goods Delivery Challan Location Items'),
	array(		    
	    _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		//_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Dimension') => 'DIMENSION',
		_('Destination') => 'DESTINATION',
		
	));*/
	//S.H
$reports->addReport(RC_CUSTOMER, 10984, _('Goods &Delivery Challan Location Items'),
	array(	_('Req. Delivery Date') => 'DATE',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Branch') => 'BRANCH_LIST',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Print as Quote') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

//
$reports->addReport(RC_CUSTOMERFORMS, 110, _('Print &Deliveries 1'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 1100, _('Print &Deliveries 2'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 1101, _('Print &Deliveries 3'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1102, _('Print &Deliveries 12'),
    array(	_('From') => 'DELIVERY',
        _('To') => 'DELIVERY',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 1103, _('Print &Deliveries 4'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('With Logo') => 'YES_NO_LOGO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11013, _('Print &Deliveries 10'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1104, _('Print &Deliveries 4 - Barcode'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1105, _('Print &Deliveries(R) 7'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
		//PETRO
$reports->addReport(RC_CUSTOMERFORMS, 1106, _('Print &Deliveries8'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1107, _('Print &Deliveries 5'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1108, _('Print &Deliveries 13'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('Dimension') => 'DIMENSION',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 1109, _('Print &Deliveries 6'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11010, _('Print &Deliveries 16'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11011, _('Print &Deliveries(R) 8'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
//worktruck report
$reports->addReport(RC_CUSTOMERFORMS, 11012, _('Print &Deliveries 9'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11014, _('Print &Deliveries 11'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11015, _('Print &Deliveries 14'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11016, _('Print &Deliveries 15'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 11017, _('Print &Deliveries 17'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11018, _('Print &Deliveries 18'),
    array(	_('From') => 'DELIVERY',
        _('To') => 'DELIVERY',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 11019, _('Print &Deliveries 19'),
    array(	_('From') => 'DELIVERY',
        _('To') => 'DELIVERY',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 11020, _('Goods Delivery Challan'),
	array(		    
	    _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		//_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Dimension') => 'DIMENSION',
		_('Destination') => 'DESTINATION',
	));
		
$reports->addReport(RC_CUSTOMERFORMS, 11021, _('Print Delivery Challan'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
			
$reports->addReport(RC_CUSTOMERFORMS, 11022, _('Print &Deliveries 20'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
		$reports->addReport(RC_CUSTOMERFORMS, 11023, _('Print &Deliveries 21'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
	$reports->addReport(RC_CUSTOMERFORMS, 11024, _('Print &Deliveries 22'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11025, _('Print &Deliveries 23'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
			
$reports->addReport(RC_CUSTOMERFORMS, 11026, _('Print &Deliveries 24'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11027, _('Print &Deliveries 25'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11028, _('Print &Deliveries 26'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
			
			
$reports->addReport(RC_CUSTOMERFORMS, 11029, _('Print &Deliveries 27'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11030, _('Print &Deliveries Note'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11031, _('Print &Deliveries 30'),
    array( _('From') => 'DELIVERY',
        _('To') => 'DELIVERY',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 11032, _('Print Delivery Challan (LT)'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('From Location') => 'LOCATIONS',
        _('To Location') => 'LOCATIONS',
        _('Select UOM') => 'UOM',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_CUSTOMERFORMS, 11033, _('Print &Deliveries'),
    array( _('From') => 'DELIVERY',
        _('To') => 'DELIVERY',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 119, _('Print &Gate Pass'),
       array(	_('From') => 'GATE_PASS',
        _('To') => 'GATE_PASS',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 1190, _('Print &Gate Pass 1'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 1191, _('Print &Gate Pass 2'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 1192, _('Print &Gate Pass 3'),
	array(	_('From') => 'DELIVERY',
			_('To') => 'DELIVERY',
			_('email Customers') => 'YES_NO',
			_('Print as Packing Slip') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 1193, _('Print &Gate Pass 4'),
    array(	_('From') => 'GATE_PASS',
        _('To') => 'GATE_PASS',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_CUSTOMERFORMS, 1194, _('Print &Gate Pass 5 '),
    array(	_('From') => 'GATE_PASS',
        _('To') => 'GATE_PASS',
        _('email Customers') => 'YES_NO',
        _('Print as Packing Slip') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        

$reports->addReport(RC_CUSTOMERFORMS, 11005, _('Print Order Confirmation'),
	array(	_('From') => 'DELIVERY',
		_('To') => 'DELIVERY',
		_('email Customers') => 'YES_NO',
		_('Print as Packing Slip') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
	
	$reports->addReport(RC_CUSTOMERFORMS, 111, _('&Print Sales Quotations '),
	array(	_('From') => 'QUOTATIONS',
			_('To') => 'QUOTATIONS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1110, _('&Print Sales Quotations 1'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 1111, _('&Print Sales Quotations 2'),
	array(	_('From') => 'QUOTATIONS',
			_('To') => 'QUOTATIONS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1112, _('&Print Sales Quotations 3'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));



$reports->addReport(RC_CUSTOMERFORMS, 1113, _('&Print Sales Quotations 4'),
	array(	_('From') => 'QUOTATIONS',
			_('To') => 'QUOTATIONS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1114, _('&Print Sales Quotations 5'),
	array(	_('From') => 'QUOTATIONS',
		_('To') => 'QUOTATIONS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1115, _('&Print Sales Quotations 6'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 1116, _('&Print Sales Quotations 7'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 1117, _('&Print Sales Quotations 8 '),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 1118, _('&Print Zero percent Sales Quotations 9 '),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 1119, _('&Print Sales Quotations 10'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 11110, _('&Print Commercial Quotations 11'),
	array(	_('From') => 'QUOTATIONS',
		_('To') => 'QUOTATIONS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_CUSTOMERFORMS, 11111, _('&Print Sales Quotations 12'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_CUSTOMERFORMS, 11112, _('&Print Sales Quotations 13'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 11113, _('&Print Sales Quotations 14'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
		
		$reports->addReport(RC_CUSTOMERFORMS, 11114, _('&Print Sales Quotations 15'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
		
		
		
$reports->addReport(RC_CUSTOMERFORMS, 11115, _('&Print Sales Quotations 16'),
	array(	_('From') => 'QUOTATIONS',
		_('To') => 'QUOTATIONS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
		
		$reports->addReport(RC_CUSTOMERFORMS, 11116, _('&Print Sales Quotations UOM'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
		
				
	 $reports->addReport(RC_CUSTOMERFORMS, 11117, _('&Print Sales Quotations - 17'),
    array(	_('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
    $reports->addReport(RC_CUSTOMERFORMS, 11118, _('&Print Sales Quotations - 18'),
	array(	_('From') => 'QUOTATIONS',
			_('To') => 'QUOTATIONS',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 11119, _('&Print Sales Quotations 19'),
   array( _('From') => 'QUOTATIONS',
      _('To') => 'QUOTATIONS',
      _('Currency Filter') => 'CURRENCY',
      _('Email Customers') => 'YES_NO',
      _('Comments') => 'TEXTBOX',
      _('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11120, _('&Print Sales Quotations 20'),
	array(	_('From') => 'QUOTATIONS',
		_('To') => 'QUOTATIONS',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_CUSTOMERFORMS, 11121, _('&Print Sales Quotations 21'),
    array( _('From') => 'QUOTATIONS',
        _('To') => 'QUOTATIONS',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
		
		
			$reports->addReport(RC_CUSTOMERFORMS, 108, _('Print &Statements'),
	array(	_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Currency Filter') => 'CURRENCY',
			_('Show Also Allocated') => 'YES_NO',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
	$reports->addReport(RC_CUSTOMERFORMS, 120, _('Load Sheet'),
   array(_('Shipping Company') => 'SHIPPINGCOMPANY',
       _('Start Date') => 'DATEBEGINM',
       _('End Date') => 'DATEENDM',
       _('Currency Filter') => 'CURRENCY',
       _('Email Customers') => 'YES_NO',
       _('Print as Quote') => 'YES_NO',
       _('Comments') => 'TEXTBOX',
       _('Orientation') => 'ORIENTATION'));
        
			$reports->addReport(RC_CUSTOMERFORMS, 113, _('Print &Credit Notes'),
	array(	_('From') => 'CREDIT',
			_('To') => 'CREDIT',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
			
			
				$reports->addReport(RC_CUSTOMERFORMS, 1130, _('Print &Credit Notes'),
	array(	_('From') => 'CREDIT',
			_('To') => 'CREDIT',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
		
			
$reports->addReport(RC_CUSTOMERFORMS, 112, _('Print Receipts'),
	array(	_('From') => 'RECEIPT',
			_('To') => 'RECEIPT',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_CUSTOMERFORMS, 115, _('Print &Credit Notes 1'),
	array(	_('From') => 'CREDIT',
			_('To') => 'CREDIT',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_CUSTOMERFORMS, 117, _('Print &Credit Notes 2'),
	array(	_('From') => 'CREDIT',
			_('To') => 'CREDIT',
			_('Currency Filter') => 'CURRENCY',
			_('email Customers') => 'YES_NO',
			_('Payment Link') => 'PAYMENT_LINK',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
$reports->addReportClass(_('Supplier Reports'), RC_SUPPLIER);
$reports->addReport(RC_SUPPLIER, 201, _('Supplier &Balances'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_SUPPLIER, 20101, _('Supplier &Balances New'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Show Balance') => 'YES_NO_DEFAULT_YES',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
    $reports->addReport(RC_SUPPLIER, 20111, _('Supplier &Balance Detail'),
	array(	_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		
	$reports->addReport(RC_SUPPLIER, 2011, _('Supplier &Balances Summary'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Supplier') => 'SUPPLIERS_NO_FILTER',
        _('Show Balance') => 'YES_NO_DEFAULT_YES',
        _('Currency Filter') => 'CURRENCY',
        _('Suppress Zeros') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
        $reports->addReport(RC_SUPPLIER, 2013, _('Purchase &Return Summary'),
	array(	_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
        
      $reports->addReport(RC_SUPPLIER, 202, _('&Aged Supplier Analyses'),
	array(	_('End Date') => 'DATE',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Currency Filter') => 'CURRENCY',

			_('Show Also Allocated') => 'YES_NO',
			_('Summary Only') => 'YES_NO',
			_('Suppress Zeros') => 'YES_NO',
        _('Types:') => 'SYS_TYPES_SUPP',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
    $reports->addReport(RC_SUPPLIER, 203, _('&Payment Report'),
	array(	_('End Date') => 'DATE',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
	$reports->addReport(RC_SUPPLIER, 2010 , _('Supplier &Receipts'),
    array( _('Start Date') => 'DATEBEGIN',
        _('End Date') => 'DATEENDM',
        _('Supplier') => 'SUPPLIERS_NO_FILTER',
        _('Currency Filter') => 'CURRENCY',
        _('Summary Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));


     $reports->addReport(RC_SUPPLIER, 209088, _('Import Register '),
	array(
		_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'
	));

$reports->addReport(RC_SUPPLIER, 20112, _('Job Detail Report'),
	array(	_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Dimension') => 'DIMENSIONS1',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		
$reports->addReport(RC_SUPPLIER, 220112, _('Supplier/Service Provider Performance Monitoring Report'),
	array(	_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		
$reports->addReport(RC_SUPPLIER, 101101, _('Purchase &Order Quantity Report'),
	array(  _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',

        _('PO') => 'PURCH',
        _('Inventory Category') => 'CATEGORIES',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_SUPPLIER, 1011010, _('Purchase &Order Delivery with Unit Price (exclude tax)'),
	array(  _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_SUPPLIER, 1011012, _('Purchase &Order Delivery with Unit Price (include tax)'),
	array(  _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(RC_SUPPLIER, 220111, _('Supplier &Ledger  (Rev)'),
	array(	_('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Currency Filter') => 'CURRENCY',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(RC_SUPPLIER, 204, _('Outstanding &GRNs Report'),
	array(	_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_SUPPLIER, 2004, _('Outstanding &GRNs Report - with date'),
	array(  _('Start Date') => 'DATEBEGIN',
		_('End Date') => 'DATEENDM',
		_('Supplier') => 'SUPPLIERS_NO_FILTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(RC_SUPPLIER, 205, _('Supplier &Detail Listing'),
	array(	_('Activity Since') => 'DATEBEGIN',
			_('Activity Greater Than') => 'TEXT',
			_('Activity Less Than') => 'TEXT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReportClass(_('Supplier Forms'), RC_SUPPLIERFORMS);

$reports->addReport(RC_SUPPLIERFORMS, 206, _('Supplier Account Opening'),
    array(
        // _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Supplier') => 'SUPPLIERS_NO_FILTER',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


$reports->addReport(RC_SUPPLIERFORMS, 209, _('Print Purchase &Orders 1'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2090, _('Print Purchase &Orders 2'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2091, _('Print Purchase &Orders 12'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2092, _('Print Purchase &Orders 5'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2093, _('Print Purchase &Orders 3'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2094, _('Print Purchase &Orders 7'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2095, _('Print Purchase &Orders 8'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2096, _('Print Purchase &Orders 6'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2097, _('Print Purchase &Orders 9'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2098, _('Print Purchase &Orders 10'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2099, _('Print Purchase &Orders 4'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        
        
        
$reports->addReport(RC_SUPPLIERFORMS, 20922, _('Print Purchase &Tax Invoice'),
	array(	_('From') => 'SUPP_IMPORT_INVOICE',
		_('To') => 'SUPP_IMPORT_INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

        
        

$reports->addReport(RC_SUPPLIERFORMS, 20910, _('Print Purchase &Orders 11'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('With Logo') => 'YES_NO_LOGO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20911, _('Print Purchase &Orders 13'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20912, _('Print Purchase &Orders 13'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20913, _('Print Purchase &Orders UOM'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20914, _('Print Purchase &Orders 14'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20915, _('Print Purchase &Orders 15'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20916, _('Print Purchase &Orders 2 - Barcode'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 20917, _('Print Purchase &Orders 0'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 20918, _('Print Purchase &Orders 20'),
	array(	_('From') => 'PO',
			_('To') => 'PO',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
			
$reports->addReport(RC_SUPPLIERFORMS, 20919, _('Print Purchase &Orders 21'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 20920, _('Print Purchase &Orders 22'),
	array(	_('From') => 'PO',
			_('To') => 'PO',
			_('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
        			
$reports->addReport(RC_SUPPLIERFORMS, 209100, _('Print Purchase &Orders 20'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        			
$reports->addReport(RC_SUPPLIERFORMS, 20923, _('Print Purchase &Orders 21'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
    
        			
$reports->addReport(RC_SUPPLIERFORMS, 20924, _('Print Purchase &Orders 22'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
   			
$reports->addReport(RC_SUPPLIERFORMS, 20925, _('Print Purchase &Orders 23'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 20926, _('Print Purchase &Orders 24'),
	array(	_('From') => 'PO',
		_('To') => 'PO',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_SUPPLIERFORMS, 20927, _('Print Purchase &Orders 25'),
    array( _('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 20928, _('Print Purchase &Orders'),
    array(	_('From') => 'PO',
        _('To') => 'PO',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 20921, _('Print Purchase &Orders-Item Wise'),
	array(
		  _('From') => 'DATEBEGIN',
			_('To') => 'DATEENDM',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Item') => 'ITEMS_P',
            _('LC Reference') => 'LC_REF',
            _('Lading No.') => 'LADING_NO',
            _('Currency Filter') => 'CURRENCY',
			_('Email Customers') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION',
			_('Suppress Zeros') => 'YES_NO'));
			
			
$reports->addReport(RC_SUPPLIERFORMS, 211, _('Print Purchase &Invoice'),
	array(	_('From') => 'PURCHASE_INVOICE',
		_('To') => 'PURCHASE_INVOICE',
		_('Currency Filter') => 'CURRENCY',
		_('Email Customers') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_SUPPLIERFORMS, 215, _('Print Purchase &Invoice2'),
    array( _('From') => 'PURCHASE_INVOICE',
        _('To') => 'PURCHASE_INVOICE',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_SUPPLIERFORMS, 208, _('Print Receiving Challan'),
    array(	_('From') => 'GRN',
        _('To') => 'GRN',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2080, _('Print Good Receipt Note'),
    array( _('From') => 'GRN',
        _('To') => 'GRN',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        
$reports->addReport(RC_SUPPLIERFORMS, 2081, _('Print Good Receipt Note - A5'),
    array( _('From') => 'GRN',
        _('To') => 'GRN',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
        
$reports->addReport(RC_SUPPLIERFORMS, 2082, _('Print Good Receipt Note two copies'),
    array( _('From') => 'GRN',
        _('To') => 'GRN',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 207, _('Print Purchase &Requisition'),
    array(	_('From') => 'PR',
        _('To') => 'PR',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 2070, _('Print Purchase &Requisition 2'),
    array(	_('From') => 'PR',
        _('To') => 'PR',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 2111, _('Print Import &Purchase'),
    array( _('From') => 'IPI',
        _('To') => 'IPI',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));

$reports->addReport(RC_SUPPLIERFORMS, 210, _('Print Remi&ttances'),
    array(	_('From') => 'REMITTANCE',
        _('To') => 'REMITTANCE',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_SUPPLIERFORMS, 212, _('Print Supplier Payment'),
    array(	_('From') => 'REMITTANCE',
        _('To') => 'REMITTANCE',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        

$reports->addReport(RC_SUPPLIERFORMS, 213, _('Print Supplier Payment two copies'),
    array(	_('From') => 'REMITTANCE',
        _('To') => 'REMITTANCE',
        _('Currency Filter') => 'CURRENCY',
        _('Email Suppliers') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        

$reports->addReportClass(_('Inventory'), RC_INVENTORY);
$reports->addReport(RC_INVENTORY,  301, _('Inventory &Valuation Report'),
	array(	_('End Date') => 'DATE',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
            _('Select Price/Standard Cost') => 'PRICE2',
            _('Sales Types') => 'SALESTYPES2',
            _('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			$reports->addReport(RC_INVENTORY,  3031, _('Inventory &Valuation Summary'),
    array( _('End Date') => 'DATE',
        _('Items') => 'ITEMS_ALL_',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY,  30122, _('Inventory &Product Details'),
    array(	_('End Date') => 'DATE',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Select Price/Standard Cost') => 'PRICE2',
        _('Sales Types') => 'SALESTYPES2',
        _('Supplier') => 'SUPPLIERS_NO_FILTER',
        _('Summary Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


if ($pref['alt_uom'] == 1) {
			$reports->addReport(RC_INVENTORY,  3011, _('Inventory &Valuation Report(Sec.UOM) '),
	array(	_('End Date') => 'DATE',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
}
			
$reports->addReport(RC_INVENTORY,  302, _('Inventory &Planning Report'),
	array(	_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
            _('Items') => 'ITEMS_P',
            _('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
		//	_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
    
	$reports->addReport(RC_INVENTORY,  3021, _('Inventory Items List Report'),
    array(	_('End Date') => 'DATE',
        _('Inventory Category') => 'CATEGORIES',
        _('Summary Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 13012, _('Monthly Itemized &Sales Report'),
    array(
       _('Date') => 'DATEBEGINM',
        _('Show Figures') => 'MONTHLY_SALE_AQ',
        _('Show Graph') => 'YES_NO',
        _('Weeks') => 'MONTHLY_SALE_WEEK',
        _('Inventory Category') => 'CATEGORIES',
        _('Items') => 'ITEMS_I',
        _('Location') => 'LOCATIONS',
        _('Destination') => 'DESTINATION'
        ));

$reports->addReport(RC_INVENTORY, 303, _('Stock &Check Sheets'),
   array(
       _('Date') => 'DATEENDM',
       _('Inventory Category') => 'CATEGORIES',
         _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_I',

          _('Supplier') => 'SUPPLIERS_NO_FILTER',
         _('Show Inactive') => 'YES_NO',
         _('Show Pictures') => 'YES_NO',
         _('Inventory Column') => 'YES_NO',
         _('Show Shortage') => 'YES_NO',
         _('Suppress Zeros') => 'YES_NO',
         _('Show Qty In Carton/Packing') => 'YES_NO',
         _('Item Like') => 'TEXT',
         _('Comments') => 'TEXTBOX',
         _('Orientation') => 'ORIENTATION',
         _('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY, 3036, _('Stock &Check Sheets-TAX'),
   array(
       _('Date') => 'DATEENDM',
       _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Supplier') => 'SUPPLIERS_NO_FILTER',
        _('Tax') => 'TAX_GROUPS',
        _('Show Inactive') => 'YES_NO',
        _('Show Pictures') => 'YES_NO',
        _('Inventory Column') => 'YES_NO',
        _('Show Shortage') => 'YES_NO',
        _('Suppress Zeros') => 'YES_NO',
        _('Show Qty In Carton/Packing') => 'YES_NO',
        _('Item Like') => 'TEXT',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3030, _('Stock &Check Sheets 2'),
      array(  _('Start Date') => 'DATEBEGINM',
            _('End Date') => 'DATEENDM',
            _('Multiple Inventory Category') => 'MULTI_CATAGORIES',
			_('Location') => 'LOCATIONS',
		    _('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Show Inactive') => 'YES_NO',
			_('Show Pictures') => 'YES_NO',
			_('Inventory Column') => 'YES_NO',
			_('Show Shortage') => 'YES_NO',
			_('Suppress Zeros') => 'YES_NO',
			_('Item Like') => 'TEXT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			if ($pref['batch'] == 1) {
$reports->addReport(RC_INVENTORY, 3033, _('Stock &Check Sheets (Batch)'),
	array(	_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Item Like') => 'ITEMS_I',
		_('Batch') => 'BATCH',
		_('Suppress Zeros') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
			}
$reports->addReport(RC_INVENTORY, 310, _('Location wise reorder report'),
    array(
        _('Inventory Category') => 'CATEGORIES',
        _('Select Standard Cost/Sales Price') => 'PRICE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3100, _('Location wise reorder report 2'),
    array(
        _('Inventory Category') => 'CATEGORIES',
        _('Select Standard Cost/Sales Price') => 'PRICE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3101, _('Location wise reorder report 3'),
    array(
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'REORDER_LOCATIONS',
        _('Item Location') => 'ITEMS_I',
        _('Select Standard Cost/Sales Price') => 'PRICE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 304, _('Inventory &Sales Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
            _('Items') => 'ITEMS_ALL_',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Show Service Items') => 'YES_NO',
			_('Show Customer Reference') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY, 3004, _('Inventory &Sales Scheme Wise Report'),
    array(
        _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_ALL_',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 30044, _('Inventory &Sales Customer Wise Report'),
    array(
        _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_ALL_',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Comments') => 'TEXTBOX',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, 3035, _('Multiple Location Stock Report'),
    array(
        _('Date') => 'DATE',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Show Values') => 'YES_NO',
        _('Destination') => 'DESTINATION',
         _('Suppress Zero') => 'YES_NO'
        
        ));
$reports->addReport(RC_INVENTORY, 3046, _('Profit & Loss/Sales Book Report'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Select Purchase Price') => 'PURCH_PRICE',
        _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_I',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Show Service Items') => 'YES_NO',
        _('Select') => 'PL_SALES',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3047, _('Overall Stock Value Report'),
    array(
        _('End Date') => 'DATE',
        _('Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
//        _('Supplier') => 'SUPPLIERS_NO_FILTER',
//        _('Items') => 'ITEMS_P',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, 30441, _('Inventory Location Transfer'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('From Location') => 'LOCATIONS',
        _('To Location') => 'LOCATIONS',
        _('Select UOM') => 'UOM',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY, 3044410, _('Inventory Location Transfer two copies'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('From Location') => 'LOCATIONS',
        _('To Location') => 'LOCATIONS',
        _('Select UOM') => 'UOM',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));	
$reports->addReport(RC_INVENTORY, 3088, _('Location Transfer Itemized Activity'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Items')=>'ITEMS_BARCODE',
        // _('Inventory Category') => 'CATEGORIES',
        // _('Location') => 'LOCATIONS',
        // _('Type') => 'SYS_TYPES_INVENT',
        // _('Sorter') => 'SORTER',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
            
$reports->addReport(RC_INVENTORY, 10999, _('Inventory Adjustment #2'),
    array( _('From') => 'INVADJUST',
        _('To') => 'INVADJUST',
        _('Currency Filter') => 'CURRENCY',
        _('Email Customers') => 'YES_NO',
        _('Print as Quote') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION'));
        
$reports->addReport(RC_INVENTORY, 10992, _('Inventory Adjustment two copies'),
   array( _('From') => 'INVADJUST',
      _('To') => 'INVADJUST',
      _('Currency Filter') => 'CURRENCY',
      _('Email Customers') => 'YES_NO',
      _('Print as Quote') => 'YES_NO',
      _('Comments') => 'TEXTBOX',
      _('Orientation') => 'ORIENTATION'));
		
$reports->addReport(RC_INVENTORY, 3040, _('Inventory &Sales Report Item Wise'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_I',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Sales Areas') => 'ZONES',
        _('Sales Groups') => 'GROUPS',
        _('Sales Man') => 'SALESMEN',
        _('Show Service Items') => 'YES_NO',
        _('Show Summary') => 'YES_NO_DEFAULT_YES',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3041, _('Inventory &Sales Report - Summary'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Items') => 'ITEMS_I',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Sales Man') => 'SALESMEN',
    		_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, 3049, _('Daily &Sales Report New'),
	array(	_('Start Date') => 'DATE',
		_('End Date') => 'DATE',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Items') => 'ITEMS_P',
        _('Payment') => 'PAYMENT_TERMS',
        _('Dimension') => 'DIMENSIONS',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));		

$reports->addReport(RC_INVENTORY, 3043, _('Supply Register'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Customer') => 'CUSTOMERS_NO_FILTER',
		_('Items') => 'ITEMS_P',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));		
		

		
		
$reports->addReport(RC_INVENTORY, 3048, _('Supply Register2'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Items') => 'MULTI_ITEMS',
        _('Brand') => 'COLOR',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));		
		
		
$reports->addReport(RC_INVENTORY, 30436, _('&Sales Invoice Register Report'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Items') => 'ITEMS_I',
      _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Customer Print as') => 'CUSTOMER_PRINT_AS',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


		
$reports->addReport(RC_INVENTORY, 30437, _('&Sales Journal Report2'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Items') => 'ITEMS_I',
      _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
        _('Customer Print as') => 'CUSTOMER_PRINT_AS',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, 305, _('&GRN Valuation Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_INVENTORY, 30710, _('New Daily Sales Report'),
    array(	_('Start Date') => 'DATE',
        _('End Date') => 'DATE',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 3071, _('Inventory &Status Report'),
    array(
        _('End Date') => 'DATE',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Summary Only') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 30711, _('Inventory &Sales With Balance Stock Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Customer') => 'CUSTOMERS_NO_FILTER',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
        
$reports->addReport(RC_INVENTORY, 306, _('Inventory P&urchasing Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Items') => 'ITEMS_P',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_INVENTORY, 307, _('Inventory &Movement Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Show Qty In Carton/Packing') => 'YES_NO',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_INVENTORY, 30722, _('Inventory &Movement Report2'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Inactive') => 'YES_NO',
			_('Location') => 'LOCATIONS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));	
//dz commented on 25/8/18
/*
$reports->addReport(RC_INVENTORY, 3077, _('Inventory &Movement Report - item-wise'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',
		_('Items')=>'ITEMS_BARCODE',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Type') => 'SYS_TYPES_INVENT',
		_('Sorter') => 'SORTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		*/
		
$reports->addReport(RC_INVENTORY, 3087, _('Inventory &Movement Report - item-wise New'),
array( _('Start Date') => 'DATEBEGINM',
   _('End Date') => 'DATEENDM',
   _('From')=>'ITEMS_I',
   _('To')=>'ITEMS_I',
   _('Inventory Category') => 'CATEGORIES',
   _('Location') => 'LOCATIONS',
   _('Type') => 'SYS_TYPES_INVENT',
   _('Sorter') => 'SORTER',
   _('Show Cost') => 'YESNO_DEFAULT_YES',
   _('Comments') => 'TEXTBOX',
   _('Orientation') => 'ORIENTATION',
   _('Destination') => 'DESTINATION'));
	
		if ($pref['alt_uom'] == 1) {
		$reports->addReport(RC_INVENTORY, 3077777, _('Inventory &Movement Report - item-wise (Sec.Qty)'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',
		_('Items')=>'ITEMS_BARCODE',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Type') => 'SYS_TYPES_INVENT',
		_('Sorter') => 'SORTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
		}
		
		$myrow_3 = get_company_item_pref_from_position(3);
// 	if(	$myrow_3['sale_enable'] || $myrow_3['purchase_enable'] ){
			$reports->addReport(RC_INVENTORY, 307777, _('Inventory &Movement Report - item-wise (Amount3)'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',
		_('Items')=>'ITEMS_BARCODE',
		_('Inventory Category') => 'CATEGORIES',
		_('Location') => 'LOCATIONS',
		_('Type') => 'SYS_TYPES_INVENT',
		_('Sorter') => 'SORTER',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
// 	}

$reports->addReport(RC_INVENTORY, 3073, _('Inventory Activity'),
    array(	_('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
//        _('Comments') => 'TEXTBOX',
//        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

//dz 29/7/18
/*
$reports->addReport(RC_INVENTORY, 3072, _('Inventory Stock &Report'),
	array(	_('Start Date') => 'DATEBEGINM',
		_('End Date') => 'DATEENDM',
		_('Location') => 'LOCATIONS',
		_('Dimension') => 'DIMENSIONS',
        _('Color Filter') => 'COLOR',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
*/
			
$reports->addReport(RC_INVENTORY, 308, _('C&osted Inventory Movement Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));				
$reports->addReport(RC_INVENTORY, 309,_('Item &Sales Summary Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY, 3012,_('Item &Purchase Summary Report'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Inventory Category') => 'CATEGORIES',
			_('Location') => 'LOCATIONS',
			_('Supplier') => 'SUPPLIERS_NO_FILTER',
			_('Items') => 'ITEMS_P',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_INVENTORY, 3010, _('Inventory &Sales Report - new'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Inventory Category') => 'CATEGORIES',
        _('Location') => 'LOCATIONS',
        _('Items') => 'ITEMS_ALL_',
        _('Customer') => 'CUSTOMERS_NO_FILTER',
//        _('Show Service Items') => 'YES_NO',
//        _('Show Customer Reference') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
			
    $reports->addReportClass(_('Inventory Forms'), RC_INVENTORY_FORMS_REPORT);	
					
	$reports->addReport(RC_INVENTORY_FORMS_REPORT, 3032, _('&Multi Barcode Printing'),
	array(	_('Currency Filter') => 'CURRENCY',
	         _('Items Filter') => 'ITEMS_BARCODE',
//	         _('Items Filter') => 'ITEMS_P',
			// _('Items To') => 'ITEMS_BARCODE',
			//_('Image (logo)') => 'LOGO_IMAGE',
			// _('Color Filter') => 'COLOR',
			_('Sales Types') => 'SALESTYPES',
            _('Templates') => 'BARCODE_TEMPLATE',
			//_('Paper Size') => 'PAPER_SIZE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			
			
				$reports->addReport(RC_INVENTORY_FORMS_REPORT, 30324, _('&Multi Barcode Printing '),
	array(	_('Currency Filter') => 'CURRENCY',
	         _('Items Filter') => 'ITEMS_BARCODE',
//	         _('Items Filter') => 'ITEMS_P',
			// _('Items To') => 'ITEMS_BARCODE',
			//_('Image (logo)') => 'LOGO_IMAGE',
			// _('Color Filter') => 'COLOR',
			_('Sales Types') => 'SALESTYPES',
            _('Templates') => 'BARCODE_TEMPLATE',
			//_('Paper Size') => 'PAPER_SIZE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			
	
			
$reports->addReport(RC_INVENTORY_FORMS_REPORT, 30333, _('&Multi Barcode Printing new'),
	array(	_('Currency Filter') => 'CURRENCY',
		_('Items Filter') => 'ITEMS_BARCODE',
//	         _('Items Filter') => 'ITEMS_P',
		// _('Items To') => 'ITEMS_BARCODE',
		//_('Image (logo)') => 'LOGO_IMAGE',
		// _('Color Filter') => 'COLOR',
		_('Sales Types') => 'SALESTYPES',
		//_('Paper Size') => 'PAPER_SIZE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));	
		
if (get_company_pref('use_manufacturing'))
{
	$reports->addReportClass(_('Manufacturing'), RC_MANUFACTURE);
	$reports->addReport(RC_MANUFACTURE, 401, _('&Bill of Material Listing'),
		array(	_('From product') => 'ITEMS',
				_('To product') => 'ITEMS',
				_('Comments') => 'TEXTBOX',
                _('Show Amount') => 'YESNO_SHOW_AMOUNT',
				_('Orientation') => 'ORIENTATION',
				_('Destination') => 'DESTINATION'));
				
	$reports->addReport(RC_MANUFACTURE, 402, _('Work Order &Listing'),
	array(		_('Start Date') => 'DATEBEGINM',
                _('End Date') => 'DATEENDM',
		        _('Items') => 'ITEMS_ALL',
				_('Location') => 'LOCATIONS',
				_('Outstanding Only') => 'YES_NO',
				_('Show Amount') => 'YESNO_SHOW_AMOUNT',
				_('Comments') => 'TEXTBOX',
				_('Destination') => 'DESTINATION'));
				
				
				
	$reports->addReport(RC_MANUFACTURE, 4021, _('Work Order &Listing-Summary'),
	array(		_('Start Date') => 'DATEBEGINM',
                _('End Date') => 'DATEENDM',
		        _('Items') => 'ITEMS_ALL',
				_('Location') => 'LOCATIONS',
				_('Outstanding Only') => 'YES_NO',
				_('Comments') => 'TEXTBOX',
				_('Destination') => 'DESTINATION'));
				
				
				
				
				
$reports->addReport(RC_MANUFACTURE, 4090, _('Print &Production Report'),
	array(	_('From') => 'WORKORDER',
			_('To') => 'WORKORDER',
			_('Email Locations') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
$reports->addReport(RC_MANUFACTURE, 4091, _('Print &Production Report Datewise'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Email Locations') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION'));
	$reports->addReport(RC_MANUFACTURE, 409, _('Print &Work Orders'),
		array(	_('From') => 'WORKORDER',
				_('To') => 'WORKORDER',
				_('Email Locations') => 'YES_NO',
				_('Comments') => 'TEXTBOX',
				_('Orientation') => 'ORIENTATION'));
}
$reports->addReport(RC_MANUFACTURE, 4099, _('Print &Work Orders - new'),
	array(	_('From') => 'WORKORDER',
		_('To') => 'WORKORDER',
		_('Email Locations') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));
		
		$reports->addReport(RC_MANUFACTURE, 40999, _('Print &Work Orders - UOM'),
	array(	_('From') => 'WORKORDER',
		_('To') => 'WORKORDER',
		_('Email Locations') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));


$reports->addReport(RC_MANUFACTURE, 5000, _('Print &Work Orders 1'),
	array(	_('From') => 'WORKORDER',
		_('To') => 'WORKORDER',
		_('Email Locations') => 'YES_NO',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION'));

if (get_company_pref('use_fixed_assets'))
{
	$reports->addReportClass(_('Fixed Assets'), RC_FIXEDASSETS);
	$reports->addReport(RC_FIXEDASSETS, 451, _('&Fixed Assets Valuation'),
		array(	_('End Date') => 'DATE',
				_('Fixed Assets Class') => 'FCLASS',
				_('Fixed Assets Category') => 'FCATEGORIES',
				_('Fixed Assets Location') => 'FLOCATIONS',
				_('Summary Only') => 'YES_NO',
				_('Comments') => 'TEXTBOX',
				_('Orientation') => 'ORIENTATION',
				_('Destination') => 'DESTINATION'));
}				
$reports->addReportClass(_('Dimensions'), RC_DIMENSIONS);
if ($dim > 0)
{
	$reports->addReport(RC_DIMENSIONS, 501, _('Dimension &Summary'),
	array(	_('From Dimension') => 'DIMENSION',
			_('To Dimension') => 'DIMENSION',
			_('Show Balance') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
}
$reports->addReportClass(_('Banking'), RC_BANKING);
	$reports->addReport(RC_BANKING,  601, _('Bank &Statement'),
	array(	_('Bank Accounts') => 'BANK_ACCOUNTS',
			_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Zero values') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_BANKING,  603, _('Bank &Statement'),
	array(	_('Bank Accounts') => 'BANK_ACCOUNTS',
			_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
			_('Zero values') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_BANKING,  604, _('Bank &Statement New'),
    array(	_('Bank Accounts') => 'BANK_ALL_ACCOUNTS',
        _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Zero values') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
    $reports->addReport(RC_BANKING,  605, _('Bank &Summary Report'),
    array(	_('Bank Accounts') => 'BANK_ALL_ACCOUNTS',
        _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Zero values') => 'YES_NO',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
        
    $reports->addReport(RC_BANKING,  606, _('Bank &Statement'),
	array(	_('Bank Accounts') => 'BANK_ACCOUNTS',
			_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Zero values') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReportClass(_('General Ledger'), RC_GL);
$reports->addReport(RC_GL, 701, _('Chart of &Accounts'),
	array(	_('Show Balances') => 'YES_NO',
	        _('Show Inactive') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(RC_GL, 702, _('List of &Journal Entries'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Type') => 'SYS_TYPES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 7022, _('List of &Journal Entries New'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Type') => 'SYS_TYPES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

if ($dim == 2)
{
	$reports->addReport(RC_GL, 704, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Show Voided') => 'YES_NO_DEFAULT_YES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 7017, _('GL Account &Transactions3'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
      _('Customer') => 'CUSTOMERS_NO_FILTER',
      _('Branch') => 'BRANCH_LIST',
      _('Account') => 'ACCOUNTS_NO_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Dimension')." 1" =>  'DIMENSIONS1',
      _('Dimension')." 2" =>  'DIMENSIONS2',
      _('Show Voided') => 'YES_NO_DEFAULT_YES',
      _('Comments') => 'TEXTBOX',
      _('Orientation') => 'ORIENTATION',
      _('Destination') => 'DESTINATION'));
			
			
			$reports->addReport(RC_GL, 7046, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 70444, _('GL Account &Transactions (Multiple)'),
        array(	_('Start Date') => 'DATEBEGINM',
            _('End Date') => 'DATEENDM',
            _('Account') => 'ACCOUNTS_NO_FILTER',
            _('Multiple Customers Category') => 'MULTI_CUST',
            _('Multiple Suppliers Category') => 'MULTI_SUPP',
            _('From Account') => 'GL_ACCOUNTS',
            _('To Account') => 'GL_ACCOUNTS',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Comments') => 'TEXTBOX',
            _('Orientation') => 'ORIENTATION',
            _('Destination') => 'DESTINATION'));

$reports->addReport(RC_GL, 7045, _('AR/AP Consolidated Statement'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
           _('Summary Only') => 'YESNO_DEFAULT_YES',
      _('Account') => 'ACCOUNTS_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Comments') => 'TEXTBOX',
      _('Destination') => 'DESTINATION'));
			
	$reports->addReport(RC_GL, 7044, _('GL Account &Transactions - 1'),
		array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			            
$reports->addReport(RC_GL, 7043, _('GL Account &Transactions - 2'),
    array( _('Start Date') => 'DATEBEGINM',
        _('End Date') => 'DATEENDM',
        _('Account') => 'ACCOUNTS_NO_FILTER',
        _('From Account') => 'GL_ACCOUNTS',
        _('To Account') => 'GL_ACCOUNTS',
        _('Dimension')." 1" =>  'DIMENSIONS1',
        _('Dimension')." 2" =>  'DIMENSIONS2',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
            
	$reports->addReport(RC_GL, 705, _('Annual &Expense Breakdown'),
	array(	_('Year') => 'TRANS_YEARS',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 706, _('&Balance Sheet'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Dimension')." 1" => 'DIMENSIONS1',
			_('Dimension')." 2" => 'DIMENSIONS2',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 707, _('&Profit and Loss Statement'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Compare to') => 'COMPARE',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 708, _('Trial &Balance'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Zero values') => 'YES_NO',
			_('Only balances') => 'YES_NO',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Dimension')." 2" =>  'DIMENSIONS2',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
   $reports->addReport(RC_GL, 7080, _('Trial &Balance2'),
        array(	_('Start Date') => 'DATEBEGINM',
            _('End Date') => 'DATEENDM',
            _('Zero values') => 'YES_NO',
            _('Only balances') => 'YES_NO',
            _('Current/Closing Balance') =>  'CURR_CLOSE',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Comments') => 'TEXTBOX',
            _('Orientation') => 'ORIENTATION',
            _('Destination') => 'DESTINATION'));
}
elseif ($dim == 1)
{
	$reports->addReport(RC_GL, 704, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension') =>  'DIMENSIONS1',
			_('Show Voided') => 'YES_NO_DEFAULT_YES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 7017, _('GL Account &Transactions3'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
      _('Customer') => 'CUSTOMERS_NO_FILTER',
      _('Branch') => 'BRANCH_LIST',
      _('Account') => 'ACCOUNTS_NO_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Dimension')." 1" =>  'DIMENSIONS1',
      _('Show Voided') => 'YES_NO_DEFAULT_YES',
      _('Comments') => 'TEXTBOX',
      _('Orientation') => 'ORIENTATION',
      _('Destination') => 'DESTINATION'));
			
			
			$reports->addReport(RC_GL, 7046, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension') =>  'DIMENSIONS1',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));	
			
			
			
	$reports->addReport(RC_GL, 7044, _('GL Account &Transactions - 1'),
		array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Dimension')." 1" =>  'DIMENSIONS1',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			
$reports->addReport(RC_GL, 7045, _('AR/AP Consolidated Statement'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
           _('Summary Only') => 'YESNO_DEFAULT_YES',
      _('Account') => 'ACCOUNTS_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Comments') => 'TEXTBOX',
      _('Destination') => 'DESTINATION'));
			
	$reports->addReport(RC_GL, 705, _('Annual &Expense Breakdown'),
	array(	_('Year') => 'TRANS_YEARS',
			_('Dimension') =>  'DIMENSIONS1',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 706, _('&Balance Sheet'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Dimension') => 'DIMENSIONS1',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 707, _('&Profit and Loss Statement'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Compare to') => 'COMPARE',
			_('Dimension') => 'DIMENSIONS1',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 708, _('Trial &Balance'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Zero values') => 'YES_NO',
			_('Only balances') => 'YES_NO',
			_('Dimension') => 'DIMENSIONS1',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
   $reports->addReport(RC_GL, 7080, _('Trial &Balance2'),
        array(	_('Start Date') => 'DATEBEGINM',
            _('End Date') => 'DATEENDM',
            _('Zero values') => 'YES_NO',
            _('Only balances') => 'YES_NO',
            _('Current/Closing Balance') =>  'CURR_CLOSE',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Comments') => 'TEXTBOX',
            _('Orientation') => 'ORIENTATION',
            _('Destination') => 'DESTINATION'));
}
else
{
	$reports->addReport(RC_GL, 704, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Show Voided') => 'YES_NO_DEFAULT_YES',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 7017, _('GL Account &Transactions3'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
      _('Customer') => 'CUSTOMERS_NO_FILTER',
      _('Branch') => 'BRANCH_LIST',
      _('Account') => 'ACCOUNTS_NO_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Show Voided') => 'YES_NO_DEFAULT_YES',
      _('Comments') => 'TEXTBOX',
      _('Orientation') => 'ORIENTATION',
      _('Destination') => 'DESTINATION'));
			
			
				$reports->addReport(RC_GL, 7046, _('GL Account &Transactions'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
			
	$reports->addReport(RC_GL, 7044, _('GL Account &Transactions - new'),
		array(_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Account') => 'ACCOUNTS_NO_FILTER',
			_('From Account') => 'GL_ACCOUNTS',
			_('To Account') => 'GL_ACCOUNTS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(RC_GL, 7045, _('AR/AP Consolidated Statement'),
array( _('Start Date') => 'DATEBEGINM',
      _('End Date') => 'DATEENDM',
           _('Summary Only') => 'YESNO_DEFAULT_YES',
      _('Account') => 'ACCOUNTS_FILTER',
      _('From Account') => 'GL_ACCOUNTS',
      _('To Account') => 'GL_ACCOUNTS',
      _('Comments') => 'TEXTBOX',
      _('Destination') => 'DESTINATION'));
			
	$reports->addReport(RC_GL, 705, _('Annual &Expense Breakdown'),
	array(	_('Year') => 'TRANS_YEARS',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 706, _('&Balance Sheet'),
	array(	_('Start Date') => 'DATEBEGIN',
			_('End Date') => 'DATEENDM',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 707, _('&Profit and Loss Statement'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Compare to') => 'COMPARE',
			_('Account Tags') =>  'ACCOUNTTAGS',
			_('Decimal values') => 'YES_NO',
			_('Graphics') => 'GRAPHIC',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	$reports->addReport(RC_GL, 708, _('Trial &Balance'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Zero values') => 'YES_NO',
			_('Only balances') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
   $reports->addReport(RC_GL, 7080, _('Trial &Balance2'),
        array(	_('Start Date') => 'DATEBEGINM',
            _('End Date') => 'DATEENDM',
            _('Zero values') => 'YES_NO',
            _('Only balances') => 'YES_NO',
            _('Current/Closing Balance') =>  'CURR_CLOSE',
            _('Dimension')." 1" =>  'DIMENSIONS1',
            _('Dimension')." 2" =>  'DIMENSIONS2',
            _('Comments') => 'TEXTBOX',
            _('Orientation') => 'ORIENTATION',
            _('Destination') => 'DESTINATION'));
}
$reports->addReport(RC_GL, 709, _('Ta&x Report'),
	array(	_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',
			_('Type') => 'SYS_TYPES_TAX',
			_('Summary Only') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(RC_GL, 710, _('Audit Trail'),
	array(	_('Start Date') => 'DATEBEGINM',
			_('End Date') => 'DATEENDM',
			_('Type') => 'SYS_TYPES_ALL',
			_('User') => 'USERS',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
//Payroll Report
if (get_company_pref('use_payroll'))
{
$reports->addReportClass(_('Payroll'), SA_PAYROLL_REPORT);	
$reports->addReport(SA_PAYROLL_REPORT, 2023, _('Employee Salary Advice'),
	array(	_('Month') => 'MONTH',
			_('Employee') => 'EMPLOYEE',
			_('Currency Filter') => 'CURRENCY',
			_('Suppress Zeros') => 'YES_NO',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 101110, _('Employee Balances'),
    array(	_('Fiscal Year') => 'TRANS_YEARS',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2012, _('Bank Letters'),
	array(	_('Month') => 'MONTH',			
			_('Banks') => 'BANK_ACCOUNTS',
			_('Letter Date') => 'DATE',
			_('Cheque') => 'TEXT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 15, _('Payroll Sheet-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 16, _('PayOrder List Sheet-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 17, _('Cheque List Sheet-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 18, _('Draft List Sheet-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 19, _('Transfer Letter Sheet-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


$reports->addReport(SA_PAYROLL_REPORT, 12, _('Man Month Report With History-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
//        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));



$reports->addReport(SA_PAYROLL_REPORT, 10, _('Nomination-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 14, _('Employee Leave Reports-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 11, _('Employee History'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 13, _('Employee increment History-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


/*
$reports->addReport(SA_PAYROLL_REPORT, 2013, _('Salary Sheet'),
	array(	_('Month') => 'MONTH',			
			_('Department') => 'DEPT',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
*/
$reports->addReport(SA_PAYROLL_REPORT, 2014, _('Salary Sheet of Payroll'),
	array(	_('Division') => 'DIMENSIONS',
			_('Project') => 'PROJECT_NEW',
			_('Location') => 'LOCATION',
			_('Employee') => 'EMPLOYEE',
			_('Month') => 'MONTH',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 12, _('Man Month Report With History-tc'),
    array(	_('Division') => 'DIMENSIONS',
        _('Project') => 'PROJECT_NEW',
        _('Location') => 'LOCATION',
        _('Employee') => 'EMPLOYEE',
        _('Month') => 'MONTH',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


$reports->addReport(SA_PAYROLL_REPORT, 20144, _('Monthly Payroll Sheet'),
	array(	_('Division1') => 'DIMENSIONS',
		_('Project1') => 'PROJECT_NEW',
		_('Location1') => 'LOCATION',
		_('Employee1') => 'EMPLOYEE',
		_('Month') => 'MONTH',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 20145, _('Salary-Difference Sheet of Payroll'),
	array(	_('Month') => 'MONTH',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2024, _('Employee Tax Report'),
	array(	_('Month') => 'MONTH',			
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2015, _('Sheet of Advance'),
	array(	_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',	
	       _('Approval') => 'UPROVE',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
			
$reports->addReport(SA_PAYROLL_REPORT, 2016, _('Sheet of Attendance'),
      array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20162, _('Employee All Data With A/H/D/Q/N'),
      array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
	
$reports->addReport(SA_PAYROLL_REPORT, 2017, _('Sheet of Overtime'),
  		array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',			
			_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2018, _('Sheet of Leave'),
  		array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',
			 _('Approval') => 'UPROVE',			
			//_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Division') => 'DIMENSIONS',
			_('Project') => 'PROJECT_NEW',
			_('Location') => 'LOCATION',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 20180, _('Employee Leave Status'),
  		array(_('Start Date') => 'DATEBEGINTAX',
			_('End Date') => 'DATEENDTAX',
			 _('Approval') => 'UPROVE',
			//_('Department') => 'DEPT',
			_('Employee') => 'EMPLOYEE',
			_('Division') => 'DIMENSIONS',
			_('Project') => 'PROJECT_NEW',
			_('Location') => 'LOCATION',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2019, _('Employee Master Sheet'),
 			array( _('Employee') => 'EMPLOYEE',
			_('Comments') => 'TEXTBOX',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 20199, _('Employee Master Sheet New'),
	array( _('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
//$reports->addReport(RC_PAYROLL, 10777, _('Attendance report'),
//	array(	_('From') => 'INVOICE',
//		_('To') => 'INVOICE',
//		_('Currency Filter') => 'CURRENCY',
//		_('email Customers') => 'YES_NO',
//		_('Payment Link') => 'PAYMENT_LINK',
//		_('Comments') => 'TEXTBOX',
//		_('Orientation') => 'ORIENTATION'));



$reports->addReport(SA_PAYROLL_REPORT, 10770, _('Attendance report'),
array(	_('Start Date') => 'DATEBEGINTAX',
	// _('Month') => 'MONTH',
	_('Department') => 'DEPT',
	_('Employee') => 'EMPLOYEE',
	_('Comments') => 'TEXTBOX',
	_('Orientation') => 'ORIENTATION',
	_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 10888, _('Pay Slip'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 10, _('Nomination-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 14, _('Employee Leave Reports-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 11, _('Employee History'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 13, _('Employee increment History-tc'),
    array(	_('Month') => 'MONTH',
        _('Department') => 'DEPT',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


$reports->addReport(SA_PAYROLL_REPORT, 108810, _('Pay Slip - new - Tc'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 10881, _('Pay Slip New'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));


$reports->addReport(SA_PAYROLL_REPORT, 1088812, _('Employee Information (TC)'),
	array(	
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 10882, _('Pay Slip History'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 10883, _('Pay Slip Qualification'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 10884, _('Pay Slip Nomination'),
	array(	_('Month') => 'MONTH',
		_('Department') => 'DEPT',
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 10889, _('Employee Wise Leave'),
	array(
		_('Employee') => 'EMPLOYEE',
		_('Comments') => 'TEXTBOX',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));


$reports->addReport(SA_PAYROLL_REPORT, 2020, _('List of Departments'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2021, _('List of Designations'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 2022, _('List of Grades'),
 			array( _('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20222, _('List of employees'),
	array(

//		_('DOB') => 'DATEENDTAX',
		_('Employee') => 'EMPLOYEE',
		_('Designation') => 'DESIGNATION',
		_('Orientation') => 'ORIENTATION',

		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20223, _('List of man month'),
	array(
		_('Division') => 'DIMENSIONS',
		_('Project') => 'PROJECT_NEW',
		_('Location') => 'LOCATION',
		_('Employee') => 'EMPLOYEE',
		_('Month') => 'MONTH',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
$reports->addReport(SA_PAYROLL_REPORT, 20224, _('Increment All '),
	array(
		_('Employee') => 'EMPLOYEE',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20225, _('Salary Payment Voucher'),
	array(
			_('Employee') => 'EMPLOYEE',

			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));



$reports->addReport(SA_PAYROLL_REPORT, 20226, _('Leave Encashment Report'),
	array(
			_('Employee') => 'EMPLOYEE',
			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20227, _('Full & Final Settlement Report'),
	array(
			_('Employee') => 'EMPLOYEE',

			_('Orientation') => 'ORIENTATION',
			_('Destination') => 'DESTINATION'));
//--iqra
$reports->addReport(SA_PAYROLL_REPORT, 141, _('Employee Attendance Inquiry Report-tc'),
    array(	_('Month') => 'MONTH',
        _('Employee') => 'EMPLOYEE',
        _('Comments') => 'TEXTBOX',
        _('Orientation') => 'ORIENTATION',
        _('Destination') => 'DESTINATION'));


//--

$reports->addReport(SA_PAYROLL_REPORT, 20228, _('Online Bank Transfer - Salary '),
	array(
		_('Employee') => 'EMPLOYEE',

		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));

$reports->addReport(SA_PAYROLL_REPORT, 20229, _('PO Monthly List - Employee'),
	array(
//		_('Employee') => 'EMPLOYEE',
		_('Accounts') => 'BANK_ACCOUNTS_NAME',
		_('Orientation') => 'ORIENTATION',
		_('Destination') => 'DESTINATION'));
}		
add_custom_reports($reports);

echo $reports->getDisplay();

end_page();
