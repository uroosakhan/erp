<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup - Setup "));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/access_levels.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{
//	$input_error = 0;
//	if (!check_num('login_tout', 10))
//	{
//		display_error(_("Login timeout must be positive number not less than 10."));
//		set_focus('login_tout');
//		$input_error = 1;
//	}
//	if (strlen($_POST['coy_name'])==0)
//	{
//		$input_error = 1;
//		//display_error(_("The company name must be entered."));
//		set_focus('coy_name');
//	}
	if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '')
	{
    if ($_FILES['pic']['error'] == UPLOAD_ERR_INI_SIZE) {
			//display_error(_('The file size is over the maximum allowed.'));
			$input_error = 1;
    }
    elseif ($_FILES['pic']['error'] > 0) {
		//	display_error(_('Error uploading logo file.'));
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
		elseif ( $_FILES['pic']['size'] > ($SysPrefs_new->max_image_size * 1024))
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
		update_company_prefs_new(
			get_post( array('company_setup','user_account_setup'
			,'access_setup','user_location','display_setup','transaction_ref','taxes','tax_group'
			,'item_tax_type','system_gl','fiscal_years','print_profile','payment_terms','shipping_company'
			,'point_sale','void_transaction','print_transaction','backup','setup','setup_text','company_setup_text','user_account_setup_text'
			,'access_setup_text','user_location_text','display_setup_text','transaction_ref_text','taxes_text','tax_group_text'
			,'item_tax_type_text','system_gl_text','fiscal_years_text','print_profile_text','payment_terms_text','shipping_company_text'
			,'point_sale_text','void_transaction_text','print_transaction_text','backup_text','complete_voucher', 'complete_voucher_text'
			
			 ,'attach_documents_text','attach_documents','printers_text','printers','contact_categories_text','contact_categories'
                    ,'update_companies_text','update_companies','update_language_text','update_language','activate_extensions_text','activate_extensions'
                    ,'install_activate_themes','install_activate_themes_text','software_upgrade_text','software_upgrade','system_diagnostics_text','system_diagnostics'
                    ,'activate_account','activate_account_text','user_bank_access_text','user_bank_access','report_form_text','report_form',
                    'attach_document','attach_document_text'
	)
			)

		);

//		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
		display_notification_centered(_("Company setup has been updated."));
	}
	set_focus('coy_name');
	$Ajax->activate('_page_body');
} /* end of if submit */
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Shadow and Glow Transitions</title>

        <style>

            [class^="hvr-"] {
                background:#3c8dbc;
                color: #FFFFFF;
                cursor: pointer;
                margin: 0;
                padding:10px;
                text-decoration: none;

            }


            /* SHADOW/GLOW TRANSITIONS */
            /* Glow */
            .hvr-glow {

                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-glow:hover, .hvr-glow:focus, .hvr-glow:active {
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.6);
            }

            /* Shadow */
            .hvr-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-shadow:hover, .hvr-shadow:focus, .hvr-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
            }

            /* Grow Shadow */
            .hvr-grow-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow, transform;
                transition-property: box-shadow, transform;
            }
            .hvr-grow-shadow:hover, .hvr-grow-shadow:focus, .hvr-grow-shadow:active {
                box-shadow: 0 10px 10px -10px rgba(0, 0, 0, 0.5);
                -webkit-transform: scale(1.1);
                transform: scale(1.1);
            }

            /* Box Shadow Outset */
            .hvr-box-shadow-outset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
            }
            .hvr-box-shadow-outset:hover, .hvr-box-shadow-outset:focus, .hvr-box-shadow-outset:active {    color: #000203;
                box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.6);
            }

            /* Box Shadow Inset */
            .hvr-box-shadow-inset {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: box-shadow;
                transition-property: box-shadow;
                box-shadow: inset 0 0 0 rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }
            .hvr-box-shadow-inset:hover, .hvr-box-shadow-inset:focus, .hvr-box-shadow-inset:active {    color: #000203;
                box-shadow: inset 2px 2px 2px rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 0, 0, 0);
                /* Hack to improve aliasing on mobile/tablet devices */
            }


            /* Float Shadow */
            .hvr-float-shadow {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform;
                transition-property: transform;
            }
            .hvr-float-shadow:before {
                pointer-events: none;
                position: absolute;
                z-index: -1;
                content: '';
                top: 100%;
                left: 5%;
                height: 10px;
                width: 90%;
                opacity: 0;
                background: -webkit-radial-gradient(center, ellipse, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at center, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0) 80%);
                /* W3C */
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: transform, opacity;
                transition-property: transform, opacity;
            }

            .hvr-float-shadow:hover, .hvr-float-shadow:focus, .hvr-float-shadow:active {   background:#006699;   color: #000203;
                -webkit-transform: translateY(-5px);
                transform: translateY(-5px);
                /* move the element up by 5px */
            }



            .hvr-float-shadow:hover:before, .hvr-float-shadow:focus:before, .hvr-float-shadow:active:before {
                opacity: 1;
                -webkit-transform: translateY(5px);
                transform: translateY(5px);
                /* move the element down by 5px (it will stay in place because it's attached to the element that also moves up 5px) */
            }

            /* Shadow Radial */
            .hvr-shadow-radial {
                display: inline-block;
                vertical-align: middle;
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
                box-shadow: 0 0 1px rgba(0, 0, 0, 0);
                -webkit-backface-visibility: hidden;
                backface-visibility: hidden;
                -moz-osx-font-smoothing: grayscale;
                position: relative;
            }
            .hvr-shadow-radial:before, .hvr-shadow-radial:after {
                pointer-events: none;
                position: absolute;
                content: '';
                left: 0;
                width: 100%;
                box-sizing: border-box;
                background-repeat: no-repeat;
                height: 5px;
                opacity: 0;
                -webkit-transition-duration: 0.3s;
                transition-duration: 0.3s;
                -webkit-transition-property: opacity;
                transition-property: opacity;
            }
            .hvr-shadow-radial:before {
                bottom: 100%;
                background: -webkit-radial-gradient(50% 150%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% 150%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:after {
                top: 100%;
                background: -webkit-radial-gradient(50% -50%, ellipse, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
                background: radial-gradient(ellipse at 50% -50%, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0) 80%);
            }
            .hvr-shadow-radial:hover:before, .hvr-shadow-radial:focus:before, .hvr-shadow-radial:active:before, .hvr-shadow-radial:hover:after, .hvr-shadow-radial:focus:after, .hvr-shadow-radial:active:after {
                opacity: 1;
            }

        </style>
    </head>

    <body>



   
	<center>
		  <td><a class="hvr-float-shadow" href="gl_setup.php"><i class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> MAIN</a></td>

        <td><a class="hvr-float-shadow" href="hf_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> HEADER/FOOTER</a></td>
        
        <td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
        <td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>
        <td><a class="hvr-float-shadow" href="print_from_setup.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT DISPLAY</a></td>

        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>
	

	</center>
    </body>
    </html>
    <head>
    <br/>

     <center>
                      <td><a class="hvr-float-shadow" href="company_preferences_new.php" style="color: skyblue;"><i class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> Sales</a></td>
          <td><a class="hvr-float-shadow" href="company_preferences_crm.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> CRM </a></td>
          <td><a class="hvr-float-shadow" href="company_preferences_purch.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> Purchases</a></td>
          <td><a class="hvr-float-shadow" href="company_preferences_item.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> Item and Inventory</a></td>
     <td><a class="hvr-float-shadow" href="company_preferences_manufacturing.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> Manufacturing</a></td>

        
          <td><a class="hvr-float-shadow" href="company_preferences_fixed_asset.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> Fixed Asset </a></td>
            <td><a class="hvr-float-shadow" href="company_preferences_dim.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> Dimensions</a></td>
            <td><a class="hvr-float-shadow" href="company_preferences_ledger.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> Banking and General Ledger</a></td>
            <td><a class="hvr-float-shadow" href="company_preferences_rep.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> Reporting </a></td>



            <td><a class="hvr-float-shadow" href="company_preferences_setup.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> Setup</a></td>

     </center>
</head>

<?php
start_form(true);

$myrow = get_company_pref_display();

/////////////setup
$_POST['company_setup']  = $myrow["company_setup"];
$_POST['user_account_setup']  = $myrow["user_account_setup"];
$_POST['access_setup']  = $myrow["access_setup"];
$_POST['user_location']  = $myrow["user_location"];
$_POST['display_setup']  = $myrow["display_setup"];
$_POST['transaction_ref']  = $myrow["transaction_ref"];
$_POST['taxes']  = $myrow["taxes"];
$_POST['tax_group']  = $myrow["tax_group"];
$_POST['item_tax_type']  = $myrow["item_tax_type"];
$_POST['system_gl']  = $myrow["system_gl"];
$_POST['fiscal_years']  = $myrow["fiscal_years"];
$_POST['print_profile']  = $myrow["print_profile"];
$_POST['payment_terms']  = $myrow["payment_terms"];
$_POST['shipping_company']  = $myrow["shipping_company"];
$_POST['point_sale']  = $myrow["point_sale"];
$_POST['void_transaction']  = $myrow["void_transaction"];
$_POST['print_transaction']  = $myrow["print_transaction"];
$_POST['backup']  = $myrow["backup"];

$_POST['company_setup_text']  = $myrow["company_setup_text"];
$_POST['user_account_setup_text']  = $myrow["user_account_setup_text"];
$_POST['access_setup_text']  = $myrow["access_setup_text"];
$_POST['user_location_text']  = $myrow["user_location_text"];
$_POST['transaction_ref_text']  = $myrow["transaction_ref_text"];
$_POST['taxes_text']  = $myrow["taxes_text"];
$_POST['tax_group_text']  = $myrow["tax_group_text"];
$_POST['item_tax_type_text']  = $myrow["item_tax_type_text"];
$_POST['system_gl_text']  = $myrow["system_gl_text"];
$_POST['fiscal_years_text']  = $myrow["fiscal_years_text"];
$_POST['print_profile_text']  = $myrow["print_profile_text"];
$_POST['payment_terms_text']  = $myrow["payment_terms_text"];
$_POST['shipping_company_text']  = $myrow["shipping_company_text"];
$_POST['point_sale_text']  = $myrow["point_sale_text"];
$_POST['void_transaction_text']  = $myrow["void_transaction_text"];
$_POST['print_transaction_text']  = $myrow["print_transaction_text"];
$_POST['backup_text']  = $myrow["backup_text"];
$_POST['display_setup_text']  = $myrow["display_setup_text"];
$_POST['complete_voucher']  = $myrow["complete_voucher"];
$_POST['complete_voucher_text']  = $myrow["complete_voucher_text"];
$_POST['setup']  = $myrow["setup"];
$_POST['setup_text']  = $myrow["setup_text"];
$_POST['attach_documents_text']  = $myrow["attach_documents_text"];
$_POST['attach_documents']  = $myrow["attach_documents"];
$_POST['printers_text']  = $myrow["printers_text"];
$_POST['printers']  = $myrow["printers"];
$_POST['contact_categories_text']  = $myrow["contact_categories_text"];
$_POST['contact_categories']  = $myrow["contact_categories"];
$_POST['update_companies_text']  = $myrow["update_companies_text"];
$_POST['update_companies']  = $myrow["update_companies"];
$_POST['update_language_text']  = $myrow["update_language_text"];
$_POST['update_language']  = $myrow["update_language"];
$_POST['activate_extensions_text']  = $myrow["activate_extensions_text"];
$_POST['activate_extensions']  = $myrow["activate_extensions"];
$_POST['install_activate_themes_text']  = $myrow["install_activate_themes_text"];
$_POST['install_activate_themes']  = $myrow["install_activate_themes"];
$_POST['activate_account_text']  = $myrow["activate_account_text"];
$_POST['activate_account']  = $myrow["activate_account"];
$_POST['software_upgrade_text']  = $myrow["software_upgrade_text"];
$_POST['software_upgrade']  = $myrow["software_upgrade"];
$_POST['system_diagnostics_text']  = $myrow["system_diagnostics_text"];
$_POST['system_diagnostics']  = $myrow["system_diagnostics"];
$_POST['user_bank_access_text']  = $myrow["user_bank_access_text"];
$_POST['user_bank_access']  = $myrow["user_bank_access"];
$_POST['report_form_text']  = $myrow["report_form_text"];
$_POST['report_form']  = $myrow["report_form"];
$_POST['attach_document_text']  = $myrow["attach_document_text"];
$_POST['attach_document']  = $myrow["attach_document"];


start_outer_table(TABLESTYLE2);

/////////////setup
table_section_title(_("Setups"));
echo"<tr>";
label_cell(_("Setup"));
text_cells(_(""), 'setup_text', $_POST['setup_text'], 50);
check_cells(_(""), 'setup', null);
echo"</tr>";

table_section_title(_("Company Setups"));
echo"<tr>";
label_cell(_("Company Setup"));
text_cells(_(""), 'company_setup_text', $_POST['company_setup_text'], 50);
check_cells(_(""), 'company_setup', null);
echo"</tr>";

echo"<tr>";
label_cell(_("User Account Setup"));
text_cells(_(""), 'user_account_setup_text', $_POST['user_account_setup_text'], 50);
check_cells(_(""), 'user_account_setup', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Access Setup"));
text_cells(_(""), 'access_setup_text', $_POST['access_setup_text'], 50);
check_cells(_(""), 'access_setup', null);
echo"</tr>";

echo"<tr>";
label_cell(_("User Locations Access"));
text_cells(_(""), 'user_location_text', $_POST['user_location_text'], 50);
check_cells(_(""), 'user_location', null);
echo"</tr>";

echo"<tr>";
label_cell(_("User Bank Access"));
text_cells(_(""), 'user_bank_access_text', $_POST['user_bank_access_text'], 50);
check_cells(_(""), 'user_bank_access', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Display Setup"));
text_cells(_(""), 'display_setup_text', $_POST['display_setup_text'], 50);
check_cells(_(""), 'display_setup', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Transaction References"));
text_cells(_(""), 'transaction_ref_text', $_POST['transaction_ref_text'], 50);
check_cells(_(""), 'transaction_ref', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Taxes"));
text_cells(_(""), 'taxes_text', $_POST['taxes_text'], 50);
check_cells(_(""), 'taxes', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Tax Group"));
text_cells(_(""), 'tax_group_text', $_POST['tax_group_text'], 50);
check_cells(_(""), 'tax_group', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Item Tax Type"));
text_cells(_(""), 'item_tax_type_text', $_POST['item_tax_type_text'], 50);
check_cells(_(""), 'item_tax_type', null);
echo"</tr>";

echo"<tr>";
label_cell(_("System and General GL Setup"));
text_cells(_(""), 'system_gl_text', $_POST['system_gl_text'], 50);
check_cells(_(""), 'system_gl', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Fiscal Years"));
text_cells(_(""), 'fiscal_years_text', $_POST['fiscal_years_text'], 50);
check_cells(_(""), 'fiscal_years', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Print Profiles"));
text_cells(_(""), 'print_profile_text', $_POST['print_profile_text'], 50);
check_cells(_(""), 'print_profile', null);
echo"</tr>";


echo"<tr>";
label_cell(_("ReportForm Setup"));
text_cells(_(""), 'report_form_text', $_POST['report_form_text'], 50);
check_cells(_(""), 'report_form', null);
echo"</tr>";


table_section_title(_("Miscellaneous"));
echo"<tr>";
label_cell(_("Payment Terms"));
text_cells(_(""), 'payment_terms_text', $_POST['payment_terms_text'], 50);
check_cells(_(""), 'payment_terms', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Shipping Company"));
text_cells(_(""), 'shipping_company_text', $_POST['shipping_company_text'], 50);
check_cells(_(""), 'shipping_company', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Points Of Sale"));
text_cells(_(""), 'point_sale_text', $_POST['point_sale_text'], 50);
check_cells(_(""), 'point_sale', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Printers"));
text_cells(_(""), 'printers_text', $_POST['printers_text'], 50);
check_cells(_(""), 'printers', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Contact Categories"));
text_cells(_(""), 'contact_categories_text', $_POST['contact_categories_text'], 50);
check_cells(_(""), 'contact_categories', null);
echo"</tr>";

table_section_title(_("Maintenance"));
echo"<tr>";
label_cell(_("Void a Transaction"));
text_cells(_(""), 'void_transaction_text', $_POST['void_transaction_text'], 50);
check_cells(_(""), 'void_transaction', null);
echo"</tr>";

echo"<tr>";
label_cell(_("View Or Print Transactions"));
text_cells(_(""), 'print_transaction_text', $_POST['print_transaction_text'], 50);
check_cells(_(""), 'print_transaction', null);
echo"</tr>";

echo"<tr>";
label_cell(_("&Attach Documents"));
text_cells(_(""), 'attach_document_text', $_POST['attach_document_text'], 50);
check_cells(_(""), 'attach_document', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Backup And Restore"));
text_cells(_(""), 'backup_text', $_POST['backup_text'], 50);
check_cells(_(""), 'backup', null);
echo"</tr>";




table_section_title(_("Complete Voucher"));
echo"<tr>";
label_cell(_("Complete voucher"));
text_cells(_(""), 'complete_voucher_text', $_POST['complete_voucher_text'], 50);
check_cells(_(""), 'complete_voucher', null);
echo"</tr>";




// echo"<tr>";
// label_cell(_("Create/Update Companies"));
// text_cells(_(""), 'update_companies_text', $_POST['update_companies_text'], 50);
// check_cells(_(""), 'update_companies', null);
// echo"</tr>";


// echo"<tr>";
// label_cell(_("Install/Update Languages"));
// text_cells(_(""), 'update_language_text', $_POST['update_language_text'], 50);
// check_cells(_(""), 'update_language', null);
// echo"</tr>";

// echo"<tr>";
// label_cell(_("Install/Activate Extensions"));
// text_cells(_(""), 'activate_extensions_text', $_POST['activate_extensions_text'], 50);
// check_cells(_(""), 'activate_extensions', null);
// echo"</tr>";

// echo"<tr>";
// label_cell(_("Install/Activate Themes"));
// text_cells(_(""), 'install_activate_themes_text', $_POST['install_activate_themes_text'], 50);
// check_cells(_(""), 'install_activate_themes', null);
// echo"</tr>";

// echo"<tr>";
// label_cell(_("Install/Activate &Chart of Accounts"));
// text_cells(_(""), 'activate_account_text', $_POST['activate_account_text'], 50);
// check_cells(_(""), 'activate_account', null);
// echo"</tr>";


// echo"<tr>";
// label_cell(_("Software Upgrade"));
// text_cells(_(""), 'software_upgrade_text', $_POST['software_upgrade_text'], 50);
// check_cells(_(""), 'software_upgrade', null);
// echo"</tr>";


// echo"<tr>";
// label_cell(_("System Diagnostics"));
// text_cells(_(""), 'system_diagnostics_text', $_POST['system_diagnostics_text'], 50);
// check_cells(_(""), 'system_diagnostics', null);
// echo"</tr>";

end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

