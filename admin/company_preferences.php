<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{
	$input_error = 0;
	if (!check_num('login_tout', 10))
	{
		display_error(_("Login timeout must be positive number not less than 10."));
		set_focus('login_tout');
		$input_error = 1;
	}
	if (strlen($_POST['coy_name'])==0)
	{
		$input_error = 1;
		display_error(_("The company name must be entered."));
		set_focus('coy_name');
	}
// 	if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS')
//     {
//     	if (strlen($_POST['MobileNumber']) == 0)
//     	{
//     		$input_error = 1;
//     		display_error(_("The Mobile number must be entered."));
//     		set_focus('MobileNumber');
//     	}
    	
//     }
	if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '')
	{
    if ($_FILES['pic']['error'] == UPLOAD_ERR_INI_SIZE) {
			display_error(_('The file size is over the maximum allowed.'));
			$input_error = 1;
    }
    elseif ($_FILES['pic']['error'] > 0) {
			display_error(_('Error uploading logo file.'));
			$input_error = 1;
    }
		$result = $_FILES['pic']['error'];
		$filename = company_path()."/images";
		if (!file_exists($filename))
		{
			mkdir($filename);
		}
		$filename .= "/".clean_file_name($_FILES['pic']['name']);

		 //But check for the worst
		if (!in_array( substr($filename,-4), array('.jpg','.JPG','.png','.PNG')))
		{
			display_error(_('Only jpg and png files are supported - a file extension of .jpg or .png is expected'));
			$input_error = 1;
		}
		elseif ( $_FILES['pic']['size'] > ($SysPrefs->max_image_size * 1024))
		{ //File Size Check
			display_error(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . $SysPrefs->max_image_size);
			$input_error = 1;
		}
		elseif ( $_FILES['pic']['type'] == "text/plain" )
		{  //File type Check
			display_error( _('Only graphics files can be uploaded'));
			$input_error = 1;
		}
		elseif (file_exists($filename))
		{
			$result = unlink($filename);
			if (!$result)
			{
				display_error(_('The existing image could not be removed'));
				$input_error = 1;
			}
		}

		if ($input_error != 1)
		{
			$result  =  move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
			$_POST['coy_logo'] = clean_file_name($_FILES['pic']['name']);
			if(!$result) 
				display_error(_('Error uploading logo file'));
		}
	}
	if (check_value('del_coy_logo'))
	{
		$filename = company_path()."/images/".clean_file_name($_POST['coy_logo']);
		if (file_exists($filename))
		{
			$result = unlink($filename);
			if (!$result)
			{
				display_error(_('The existing image could not be removed'));
				$input_error = 1;
			}
		}
		$_POST['coy_logo'] = "";
	}
	if ($_POST['add_pct'] == "")
		$_POST['add_pct'] = -1;
	if ($_POST['round_to'] <= 0)
		$_POST['round_to'] = 1;
	if ($input_error != 1)
	{
/*		update_company_prefs(
			get_post( array('coy_name','coy_no','gst_no','tax_prd','tax_last',
				'postal_address','phone', 'fax', 'email', 'coy_logo', 'domicile',
				'use_dimension', 'curr_default', 'f_year', 
				'no_item_list' => 0, 'no_customer_list' => 0, 
				'no_supplier_list' =>0, 'base_sales', 
				'time_zone' => 0, 'add_pct', 'round_to', 'login_tout', 'auto_curr_reval',
				'bcc_email', 'alternative_tax_include_on_docs', 'suppress_tax_rates',
				'use_manufacturing', 'use_fixed_assets','logo_h','logo_w','bank_details','bank_account'))
*/
		update_company_prefs(
			get_post( array('coy_name','coy_no','gst_no','tax_prd','tax_last',
				'postal_address','phone', 'fax', 'email', 'coy_logo', 'domicile',
				'use_dimension', 'curr_default', 'f_year', 'shortname_name_in_list',
				'no_item_list' => 0, 'no_customer_list' => 0, 
				'no_supplier_list' =>0, 'base_sales', 
				'time_zone' => 0, 'company_logo_report' => 0, 'add_pct', 'round_to', 'login_tout', 'auto_curr_reval',
				'bcc_email', 'alternative_tax_include_on_docs', 'suppress_tax_rates',
				'use_fixed_assets','logo_h','logo_w','back_days','future_days','deadline_time','sst_no','MobileNumber',
				'website','auto_send_sms','legal_text','legal_text','Unassemble_costing_breakup','discount_offer', 'scheme_calculation'))


		);

		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
		display_notification_centered(_("Company setup has been updated."));
	}
	set_focus('coy_name');
	$Ajax->activate('_page_body');
} /* end of if submit */

start_form(true);

$myrow = get_company_prefs();

$_POST['coy_name'] = $myrow["coy_name"];
$_POST['gst_no'] = $myrow["gst_no"];
$_POST['sst_no'] = $myrow["sst_no"];
$_POST['tax_prd'] = $myrow["tax_prd"];
$_POST['tax_last'] = $myrow["tax_last"];
$_POST['coy_no']  = $myrow["coy_no"];
$_POST['postal_address']  = $myrow["postal_address"];
$_POST['phone']  = $myrow["phone"];
$_POST['MobileNumber']  = $myrow["MobileNumber"];
$_POST['fax']  = $myrow["fax"];
$_POST['website']  = $myrow["website"];
$_POST['email']  = $myrow["email"];
$_POST['coy_logo']  = $myrow["coy_logo"];
$_POST['domicile']  = $myrow["domicile"];
$_POST['use_dimension']  = $myrow["use_dimension"];
$_POST['base_sales']  = $myrow["base_sales"];
if (!isset($myrow["shortname_name_in_list"]))
{
	set_company_pref("shortname_name_in_list", "setup.company", "tinyint", 1, '0');
	$myrow["shortname_name_in_list"] = get_company_pref("shortname_name_in_list");
}
$_POST['shortname_name_in_list']  = $myrow["shortname_name_in_list"];
$_POST['auto_send_sms']  = $myrow["auto_send_sms"];
$_POST['Unassemble_costing_breakup']  = $myrow["Unassemble_costing_breakup"];
$_POST['no_item_list']  = $myrow["no_item_list"];
$_POST['no_customer_list']  = $myrow["no_customer_list"];
$_POST['no_supplier_list']  = $myrow["no_supplier_list"];
$_POST['curr_default']  = $myrow["curr_default"];
$_POST['f_year']  = $myrow["f_year"];
$_POST['time_zone']  = $myrow["time_zone"];
if (!isset($myrow["company_logo_report"]))
{
	set_company_pref("company_logo_report", "setup.company", "tinyint", 1, '0');
	$myrow["company_logo_report"] = get_company_pref("company_logo_report");
}
$_POST['company_logo_report']  = $myrow["company_logo_report"];
$_POST['version_id']  = $myrow["version_id"];
$_POST['add_pct'] = $myrow['add_pct'];
$_POST['login_tout'] = $myrow['login_tout'];
if ($_POST['add_pct'] == -1)
	$_POST['add_pct'] = "";
$_POST['round_to'] = $myrow['round_to'];	
$_POST['auto_curr_reval'] = $myrow['auto_curr_reval'];	
$_POST['del_coy_logo']  = 0;
$_POST['bcc_email']  = $myrow["bcc_email"];
$_POST['alternative_tax_include_on_docs']  = $myrow["alternative_tax_include_on_docs"];
$_POST['suppress_tax_rates']  = $myrow["suppress_tax_rates"];
//$_POST['use_manufacturing']  = $myrow["use_manufacturing"];

$_POST['back_days']  = $myrow["back_days"];
$_POST['future_days']= $myrow["future_days"];
$_POST['deadline_time']= $myrow["deadline_time"];

$_POST['use_fixed_assets']  = $myrow["use_fixed_assets"];
$_POST['logo_w']  = $myrow["logo_w"];
$_POST['logo_h']  = $myrow["logo_h"];
$_POST['legal_text']  = $myrow["legal_text"];
$_POST['discount_offer']  = $myrow["discount_offer"];
$_POST['scheme_calculation'] = $myrow["scheme_calculation"];
// $_POST['bank_account']  = $myrow["bank_account"];
// $_POST['bank_details']  = $myrow["bank_details"];

start_outer_table(TABLESTYLE2);

table_section(1);
table_section_title(_("General settings"));

text_row_ex(_("Name (to appear on reports):"), 'coy_name', 50, 50);
textarea_row(_("Address:"), 'postal_address', $_POST['postal_address'], 34, 5);
textarea_row(_("Promotional Message:"), 'legal_text', $_POST['legal_text'], 32, 4);

if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CW')
    text_row_ex(_("Wifi Password:"), 'domicile', 25, 55);
else
    text_row_ex(_("Domicile:"), 'domicile', 25, 55);


text_row_ex(_("Phone Number:"), 'phone', 25, 55);
	if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS' || 
        $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO')
    {
text_row_ex(_("SMS Receive:"), 'MobileNumber', 25, 12);
}
text_row_ex(_("Fax Number:"), 'fax', 25);
//if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CW')
text_row_ex(_("Website:"), 'website', 25);
text_row_ex(_("Logo Width:"), 'logo_w', 4);
text_row_ex(_("Logo height:"), 'logo_h', 4);
email_row_ex(_("Email Address:"), 'email', 50, 55);

email_row_ex(_("BCC Address for all outgoing mails:"), 'bcc_email', 50, 55);

label_row(_("Sales Department Email"),"<a blank_
	href='$path_to_root/sales/manage/sales_email.php?dimension_id=".$_SESSION['Items']->dimension_id."'"
	." onclick=\"javascript:openWindow(this.href,this.target); return false;\" > Click here "
	."</a>");
	
	label_row(_("Purchase Department Email"),"<a blank_
	href='$path_to_root/purchasing/manage/purchase_email.php?dimension_id=".$_SESSION['Items']->dimension_id."'"
	." onclick=\"javascript:openWindow(this.href,this.target); return false;\" > Click here "
	."</a>");

text_row_ex(_("NTN:"), 'coy_no', 25);
text_row_ex(_("GST No:"), 'gst_no', 25);
text_row_ex(_("SST No:"), 'sst_no', 25);
// text_row_ex(_("Bank Account No"), 'bank_account', 25, 55);
// textarea_row(_("Bank Details"), 'bank_details', $_POST['bank_details'], 23);
currencies_list_row(_("Home Currency:"), 'curr_default', $_POST['curr_default']);

label_row(_("Company Logo:"), $_POST['coy_logo']);
file_row(_("New Company Logo (.jpg)") . ":", 'pic', 'pic');
check_row(_("Delete Company Logo:"), 'del_coy_logo', $_POST['del_coy_logo']);

check_row(_("Automatic Revaluation Currency Accounts"), 'auto_curr_reval', $_POST['auto_curr_reval']);
check_row(_("Time Zone on Reports"), 'time_zone', $_POST['time_zone']);
check_row(_("Company Logo on Reports"), 'company_logo_report', $_POST['company_logo_report']);
check_row(_("Auto send SMS"), 'auto_send_sms', $_POST['auto_send_sms']);
check_row(_("Unassemble Costing Break-up:"), 'Unassemble_costing_breakup', $_POST['Unassemble_costing_breakup']);
check_row(_("Discount Offer:"), 'discount_offer', $_POST['discount_offer']);
check_row(_("Scheme Calculation :"), 'scheme_calculation', $_POST['scheme_calculation']);
label_row(_("Database Scheme Version"), $_POST['version_id']);

table_section(2);

table_section_title(_("General Ledger Settings"));
fiscalyears_list_row(_("Fiscal Year:"), 'f_year', $_POST['f_year']);
text_row_ex(_("Tax Periods:"), 'tax_prd', 10, 10, '', null, null, _('Months.'));
text_row_ex(_("Backdate Entries Allowed:"), 'back_days', 10, 10, 'Blank = Disable, 0 = Yesterday Disallowed, 1 = Day Before Yesterday Disallowed', $_POST['back_dys'], null, _('Days Entry.'));


text_row_ex(_("Future date Entries Allowed:"), 'future_days', 10, 10, 'Blank = Disable, 0 = Yesterday Disallowed, 1 = Day Before Yesterday Disallowed', $_POST['future_days'], null, _('Days Entry.'));

text_row_ex(_("Deadline Time :"), 'deadline_time', 10, 10, 'Blank = Disable,require time Disallowed', null, null, _('Time Entry.'));


text_row_ex(_("Tax Last Period:"), 'tax_last', 10, 10, '', null, null, _('Months back.'));
check_row(_("Put alternative Tax Include on Docs"), 'alternative_tax_include_on_docs', null);
check_row(_("Suppress Tax Rates on Docs"), 'suppress_tax_rates', null);

table_section_title(_("Sales Pricing"));
sales_types_list_row(_("Base for auto price calculations:"), 'base_sales', $_POST['base_sales'], false,
    _('No base price list') );

text_row_ex(_("Add Price from Std Cost:"), 'add_pct', 10, 10, '', null, null, "%");
$curr = get_currency($_POST['curr_default']);
text_row_ex(_("Round calculated prices to nearest:"), 'round_to', 10, 10, '', null, null, $curr['hundreds_name']);
label_row("", "&nbsp;");

table_section_title(_("Optional Modules"));
//check_row(_("Manufacturing"), 'use_manufacturing', null); //dz 26.5.17
check_row(_("Fixed Assets"), 'use_fixed_assets', null); 
number_list_row(_("Use Dimensions:"), 'use_dimension', null, 0, 3);

table_section_title(_("User Interface Options"));
check_row(_("Short Name and Name in List"), 'shortname_name_in_list', $_POST['shortname_name_in_list']);
check_row(_("Search Item List"), 'no_item_list', null);
check_row(_("Search Customer List"), 'no_customer_list', null);
check_row(_("Search Supplier List"), 'no_supplier_list', null);
text_row_ex(_("Login Timeout:"), 'login_tout', 10, 10, '', null, null, _('seconds'));

end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------
if (get_company_pref('no_item_list') == 1 &&
    get_company_pref('no_customer_list') == 1 &&
    get_company_pref('no_supplier_list') == 1) {
$use_popup_search = true;
}
else
    $use_popup_search = false;

end_page();

