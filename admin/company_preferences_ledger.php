<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup - General Ledger "));

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
			get_post( array('bank_payments_voucher','bank_deposit_voucher'
			,'cash_payments_voucher','cash_receipt_voucher','bank_account_transfer','journal_entry'
			,'reconcile_account','journal_inquiry','gl','bank_account_inquiry','tax_inquiry','trial_balance',
			'drill_balance','pf_drill'
			,'banking_reports','general_ledger','bank_account','quick_entries','account_tag'
			,'gl_account','gl_account_group','closing1','revaluation','closing','closing1','exchange_rates'
			,'currencies','revenue_cost','reconcile_account
			','gl_account_classes','ledger_text','ledger','bank_payments_voucher_text','bank_deposit_voucher_text'
			,'cash_payments_voucher_text','cash_receipt_voucher_text','bank_account_transfer_text','journal_entry_text'
			,'reconcile_account_text','journal_inquiry_text','gl_text','bank_account_inquiry_text','tax_inquiry_text','trial_balance_text','drill_balance_text','pf_drill_text'
			,'banking_reports_text','general_ledger_text','bank_account_text','quick_entries_text','account_tag_text'
			,'gl_account_text','gl_account_group_text','closing_text1','budget_entry_text','budget_entry','gl_account_transfer_text'
                    ,'gl_account_transfer'
			,'revaluation_text','closing_text','exchange_rates_text','journal_inquiry2_text','journal_inquiry2'
			,'currencies_text','revenue_cost_text','reconcile_account_text','gl_account_classes_text','cash_flow_statment_text','cash_flow_statment','tax_inquiry_cash_text','tax_inquiry_cash')
			,'import_transaction_text','import_transaction','import_multiple_journal_text','import_multiple_journal','outstanding_cheques_inquiry_text','outstanding_cheques_inquiry'
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
    <br/>

<?php
start_form(true);

$myrow = get_company_pref_display();

//////banking and general ledger
$_POST['bank_payments_voucher']  = $myrow["bank_payments_voucher"];
$_POST['bank_deposit_voucher']  = $myrow["bank_deposit_voucher"];
$_POST['cash_payments_voucher']  = $myrow["cash_payments_voucher"];
$_POST['cash_receipt_voucher']  = $myrow["cash_receipt_voucher"];
$_POST['bank_account_transfer']  = $myrow["bank_account_transfer"];
$_POST['journal_entry']  = $myrow["journal_entry"];
$_POST['reconcile_account']  = $myrow["reconcile_account"];
$_POST['journal_inquiry']  = $myrow["journal_inquiry"];

$_POST['gl']  = $myrow["gl"];
$_POST['bank_account_inquiry']  = $myrow["bank_account_inquiry"];
$_POST['tax_inquiry']  = $myrow["tax_inquiry"];
$_POST['trial_balance']  = $myrow["trial_balance"];
$_POST['drill_balance']  = $myrow["drill_balance"];
$_POST['pf_drill']  = $myrow["pf_drill"];
$_POST['banking_reports']  = $myrow["banking_reports"];
$_POST['general_ledger']  = $myrow["general_ledger"];
$_POST['bank_account']  = $myrow["bank_account"];
$_POST['quick_entries']  = $myrow["quick_entries"];
$_POST['account_tag']  = $myrow["account_tag"];
$_POST['gl_account']  = $myrow["gl_account"];
$_POST['gl_account_group']  = $myrow["gl_account_group"];
$_POST['closing']  = $myrow["closing"];
$_POST['revaluation']  = $myrow["revaluation"];
$_POST['closing']  = $myrow["closing"];
$_POST['exchange_rates']  = $myrow["exchange_rates"];
$_POST['currencies']  = $myrow["currencies"];
$_POST['revenue_cost']  = $myrow["revenue_cost"];
$_POST['reconcile_account']  = $myrow["reconcile_account"];
$_POST['gl_account_classes']  = $myrow["gl_account_classes"];
$_POST['bank_payments_voucher_text']  = $myrow["bank_payments_voucher_text"];
$_POST['bank_deposit_voucher_text']  = $myrow["bank_deposit_voucher_text"];
$_POST['cash_payments_voucher_text']  = $myrow["cash_payments_voucher_text"];
$_POST['bank_account_transfer_text']  = $myrow["bank_account_transfer_text"];
$_POST['journal_entry_text']  = $myrow["journal_entry_text"];
$_POST['reconcile_account_text']  = $myrow["reconcile_account_text"];
$_POST['journal_inquiry_text']  = $myrow["journal_inquiry_text"];
$_POST['gl_text']  = $myrow["gl_text"];
$_POST['bank_account_inquiry_text']  = $myrow["bank_account_inquiry_text"];
$_POST['tax_inquiry_text']  = $myrow["tax_inquiry_text"];
$_POST['trial_balance_text']  = $myrow["trial_balance_text"];
$_POST['drill_balance_text']  = $myrow["drill_balance_text"];
$_POST['pf_drill_text']  = $myrow["pf_drill_text"];
$_POST['banking_reports_text']  = $myrow["banking_reports_text"];
$_POST['general_ledger_text']  = $myrow["general_ledger_text"];
$_POST['bank_account_text']  = $myrow["bank_account_text"];
$_POST['quick_entries_text']  = $myrow["quick_entries_text"];
$_POST['account_tag_text']  = $myrow["account_tag_text"];
$_POST['gl_account_text']  = $myrow["gl_account_text"];
$_POST['gl_account_group_text']  = $myrow["gl_account_group_text"];
$_POST['closing_text1']  = $myrow["closing_text1"];
$_POST['revaluation_text']  = $myrow["revaluation_text"];
$_POST['closing_text']  = $myrow["closing_text"];
$_POST['exchange_rates_text']  = $myrow["exchange_rates_text"];
$_POST['currencies_text']  = $myrow["currencies_text"];
$_POST['revenue_cost_text']  = $myrow["revenue_cost_text"];
$_POST['reconcile_account_text']  = $myrow["reconcile_account_text"];
$_POST['gl_account_classes_text']  = $myrow["gl_account_classes_text"];
$_POST['cash_receipt_voucher_text']  = $myrow["cash_receipt_voucher_text"];
$_POST['ledger_text']  = $myrow["ledger_text"];
$_POST['ledger']  = $myrow["ledger"];
$_POST['journal_inquiry2_text']  = $myrow["journal_inquiry2_text"];
$_POST['journal_inquiry2']  = $myrow["journal_inquiry2"];
$_POST['budget_entry_text']  = $myrow["budget_entry_text"];
$_POST['budget_entry']  = $myrow["budget_entry"];
$_POST['gl_account_transfer_text']  = $myrow["gl_account_transfer_text"];
$_POST['gl_account_transfer']  = $myrow["gl_account_transfer"];
$_POST['cash_flow_statement_text']  = $myrow["cash_flow_staetment_text"];
$_POST['cash_flow_statement']  = $myrow["cash_flow_statement"];
$_POST['tax_inquiry_cash_text']  = $myrow["tax_inquiry_cash_text"];
$_POST['tax_inquiry_cash']  = $myrow["tax_inquiry_cash"];
$_POST['import_transaction_text']  = $myrow["import_transaction_text"];
$_POST['import_transaction']  = $myrow["import_transaction"];
$_POST['import_multiple_journal_text']  = $myrow["import_multiple_journal_text"];
$_POST['import_multiple_journal']  = $myrow["import_multiple_journal"];
$_POST['outstanding_cheques_inquiry_text']  = $myrow["outstanding_cheques_inquiry_text"];
$_POST['outstanding_cheques_inquiry']  = $myrow["outstanding_cheques_inquiry"];



//////banking and general ledger
table_section();

table_section_title(_("Banking and General Ledger "));
echo"<tr>";
label_cell(_("General Ledger"));
text_cells(_(""), 'ledger_text', $_POST['ledger_text'], 50);
check_cells(_(""), 'ledger', null);
echo"</tr>";

table_section_title(_("Transactions"));
echo"<tr>";
label_cell(_("Bank Payments Voucher"));
text_cells(_(""), 'bank_payments_voucher_text', $_POST['bank_payments_voucher_text'], 50);
check_cells(_(""), 'bank_payments_voucher', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Bank Deposits Voucher"));
text_cells(_(""), 'bank_deposit_voucher_text', $_POST['bank_deposit_voucher_text'], 50);
check_cells(_(""), 'bank_deposit_voucher', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Cash Payment Voucher"));
text_cells(_(""), 'cash_payments_voucher_text', $_POST['cash_payments_voucher_text'], 50);
check_cells(_(""), 'cash_payments_voucher', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Cash Receipt Voucher"));
text_cells(_(""), 'cash_receipt_voucher_text', $_POST['cash_receipt_voucher_text'], 50);
check_cells(_(""), 'cash_receipt_voucher', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Bank Account Transfer"));
text_cells(_(""), 'bank_account_transfer_text', $_POST['bank_account_transfer_text'], 50);
check_cells(_(""), 'bank_account_transfer', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Journal Entry"));
text_cells(_(""), 'journal_entry_text', $_POST['journal_entry_text'], 50);
check_cells(_(""), 'journal_entry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Budget Entry"));
text_cells(_(""), 'budget_entry_text', $_POST['budget_entry_text'], 50);
check_cells(_(""), 'budget_entry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Reconcile Bank Account"));
text_cells(_(""), 'reconcile_account_text', $_POST['reconcile_account_text'], 50);
check_cells(_(""), 'reconcile_account', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Revenue Cost"));
text_cells(_(""), 'revenue_cost_text', $_POST['revenue_cost_text'], 50);
check_cells(_(""), 'revenue_cost', null);
echo"</tr>";

table_section_title(_("Inquiries and Reports "));
echo"<tr>";
label_cell(_("Journal Inquiry"));
text_cells(_(""), 'journal_inquiry_text', $_POST['journal_inquiry_text'], 50);
check_cells(_(""), 'journal_inquiry', null);
echo"</tr>";


echo"<tr>";
label_cell(_("GL Inquiry"));
text_cells(_(""), 'gl_text', $_POST['gl_text'], 50);
check_cells(_(""), 'gl', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Bank Account Inquiry"));
text_cells(_(""), 'bank_account_inquiry_text', $_POST['bank_account_inquiry_text'], 50);
check_cells(_(""), 'bank_account_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Outstanding Cheques Inquiry"));
text_cells(_(""), 'outstanding_cheques_inquiry_text', $_POST['outstanding_cheques_inquiry_text'], 50);
check_cells(_(""), 'outstanding_cheques_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Tax Inquiry"));
text_cells(_(""), 'tax_inquiry_text', $_POST['tax_inquiry_text'], 50);
check_cells(_(""), 'tax_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Trial Balance"));
text_cells(_(""), 'trial_balance_text', $_POST['trial_balance_text'], 50);
check_cells(_(""), 'trial_balance', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Balance Sheet drilldown"));
text_cells(_(""), 'drill_balance_text', $_POST['drill_balance_text'], 50);
check_cells(_(""), 'drill_balance', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Profit and Loss Drilldown"));
text_cells(_(""), 'pf_drill_text', $_POST['pf_drill_text'], 50);
check_cells(_(""), 'pf_drill', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Banking Reports"));
text_cells(_(""), 'banking_reports_text', $_POST['banking_reports_text'], 50);
check_cells(_(""), 'banking_reports', null);
echo"</tr>";

echo"<tr>";
label_cell(_("General Ledger Reports"));
text_cells(_(""), 'general_ledger_text', $_POST['general_ledger_text'], 50);
check_cells(_(""), 'general_ledger', null);
echo"</tr>";

table_section_title(_("Maintenance"));



echo"<tr>";
label_cell(_("Bank Account"));
text_cells(_(""), 'bank_account_text', $_POST['bank_account_text'], 50);
check_cells(_(""), 'bank_account', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Quick Entries"));
text_cells(_(""), 'quick_entries_text', $_POST['quick_entries_text'], 50);
check_cells(_(""), 'quick_entries', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Account Tags"));
text_cells(_(""), 'account_tag_text', $_POST['account_tag_text'], 50);
check_cells(_(""), 'account_tag', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Currencies"));
text_cells(_(""), 'currencies_text', $_POST['currencies_text'], 50);
check_cells(_(""), 'currencies', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Exchange Rates"));
text_cells(_(""), 'exchange_rates_text', $_POST['exchange_rates_text'], 50);
check_cells(_(""), 'exchange_rates', null);
echo"</tr>";

echo"<tr>";
label_cell(_("GL Accounts"));
text_cells(_(""), 'gl_account_text', $_POST['gl_account_text'], 50);
check_cells(_(""), 'gl_account', null);
echo"</tr>";

echo"<tr>";
label_cell(_("GL Account Groups"));
text_cells(_(""), 'gl_account_group_text', $_POST['gl_account_group_text'], 50);
check_cells(_(""), 'gl_account_group', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Closing GL Transactions"));
text_cells(_(""), 'closing_text', $_POST['closing_text'], 50);
check_cells(_(""), 'closing', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Revaluation"));
text_cells(_(""), 'revaluation_text', $_POST['revaluation_text'], 50);
check_cells(_(""), 'revaluation', null);
echo"</tr>";


echo"<tr>";
label_cell(_("GL Account Transfer"));
text_cells(_(""), 'gl_account_transfer_text', $_POST['gl_account_transfer_text'], 50);
check_cells(_(""), 'gl_account_transfer', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Reconcile Account"));
text_cells(_(""), 'reconcile_account_text', $_POST['reconcile_account_text'], 50);
check_cells(_(""), 'reconcile_account', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Cash Flow Statement"));
text_cells(_(""), 'cash_flow_statement_text', $_POST['cash_flow_statement_text'], 50);
check_cells(_(""), 'cash_flow_statement', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Tax Inquiry(Cash Basis)"));
text_cells(_(""), 'tax_inquiry_cash_text', $_POST['tax_inquiry_cash_text'], 50);
check_cells(_(""), 'tax_inquiry_cash', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Import Transactions"));
text_cells(_(""), 'import_transaction_text', $_POST['import_transaction_text'], 50);
check_cells(_(""), 'import_transaction', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Import Multiple Journal Entries"));
text_cells(_(""), 'import_multiple_journal_text', $_POST['import_multiple_journal_text'], 50);
check_cells(_(""), 'import_multiple_journal', null);
echo"</tr>";
end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

