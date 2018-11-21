<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup - Sales "));

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
			get_post( array('sales_quotation','sale_order_entry','delivery_against'
			,'customer_credit_note','invoice_against','direct_invoice','customer_payment'
			,'allocate','sales_quotation_inquiry','sale_order_inquiry','customer_inquiry'
			,'customer_allocate_inquiry','sales_reports','manage_customer','customer_branches'
			,'sales_group','sales_type','sales_persons','sales_areas','wht_types','invoice_prepaid_order'
			,'sales_quotation_text'
			, 'delivery_against_text', 'invoice_against_text', 'sale_order_entry_text', 'direct_invoice_text'
			, 'customer_payment_text', 'customer_credit_note_text', 'allocate_text', 'sales_quotation_inquiry_text'
			, 'sale_order_inquiry_text', 'customer_inquiry_text', 'customer_allocate_inquiry_text', 'sales_reports_text'
			, 'manage_customer_text', 'customer_branches_text', 'sales_group_text', 'sales_type_text'
			,'sales_persons_text','sales_areas_text','wht_types_text','merge_customers_text'
                    ,'merge_customers','import_customer_text','import_customer','import_customer_opening_text'
                    ,'import_customer_opening','sales_order_entry_mobile_text','sales_order_entry_mobile','direct_delivery_text'
                    ,'direct_delivery','template_delivery_text','template_delivery','template_invoice_text','template_invoice','create_print_recurrent_text'
                    ,'create_print_recurrent','recurrent_invoice_text','recurrent_invoice','credit_status_setup_text','credit_status_setup'
            ,'sales_email_text','sales_email' ,'import_bulk_invoice','import_bulk_invoice_text','import_bulk_credit_text'
                    ,'import_bulk_credit','pos_text','pos'
                    ,'invoice_prepaid_order_text','sales','sales_text','gate_pass_text','gate_pass',
                    'sales_sms_text','sales_sms','sms_template','sms_template_text')
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
		  <td><a class="hvr-float-shadow" href="gl_setup.php"><i class="fa fa-dashboard" style="margin-right: 5px; font-size: large;">  </i> MAIN</a></td>

        <td><a class="hvr-float-shadow" href="hf_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> HEADER/FOOTER</a></td>
        
        <td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
        <td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>
        
        <td><a class="hvr-float-shadow" href="meta_forward.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT PREFERENCES</a></td>

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

$_POST['sales_quotation'] = $myrow["sales_quotation"];
$_POST['sale_order_entry'] = $myrow["sale_order_entry"];
$_POST['delivery_against'] = $myrow["delivery_against"];
$_POST['customer_credit_note'] = $myrow["customer_credit_note"];
$_POST['invoice_against']  = $myrow["invoice_against"];
$_POST['direct_invoice']  = $myrow["direct_invoice"];
$_POST['customer_payment']  = $myrow["customer_payment"];
$_POST['allocate']  = $myrow["allocate"];
$_POST['sales_quotation_inquiry']  = $myrow["sales_quotation_inquiry"];
$_POST['sale_order_inquiry']  = $myrow["sale_order_inquiry"];
$_POST['customer_inquiry']  = $myrow["customer_inquiry"];
$_POST['customer_allocate_inquiry']  = $myrow["customer_allocate_inquiry"];
$_POST['sales_reports']  = $myrow["sales_reports"];
$_POST['manage_customer']  = $myrow["manage_customer"];
$_POST['customer_branches']  = $myrow["customer_branches"];
$_POST['sales_group']  = $myrow["sales_group"];
$_POST['sales_type']  = $myrow["sales_type"];
$_POST['sales_persons']  = $myrow["sales_persons"];
$_POST['sales_areas']  = $myrow["sales_areas"];
$_POST['wht_types']  = $myrow["wht_types"];
$_POST['invoice_prepaid_order']  = $myrow["invoice_prepaid_order"];
$_POST['sales_text']  = $myrow["sales_text"];
$_POST['sales']  = $myrow["sales"];
$_POST['import_bulk_invoice_text']  = $myrow["import_bulk_invoice_text"];
$_POST['import_bulk_invoice']  = $myrow["import_bulk_invoice"];
$_POST['sales_email_text']  = $myrow["sales_email_text"];
$_POST['sales_email']  = $myrow["sales_email"];
$_POST['import_bulk_credit_text']  = $myrow["import_bulk_credit_text"];
$_POST['import_bulk_credit']  = $myrow["import_bulk_credit"];
$_POST['pos_text']  = $myrow["pos_text"];
$_POST['pos']  = $myrow["pos"];

$_POST['sales_quotation_text']  = $myrow["sales_quotation_text"];
$_POST['sale_order_entry_text']  = $myrow["sale_order_entry_text"];
$_POST['delivery_against_text']  = $myrow["delivery_against_text"];
$_POST['invoice_against_text']  = $myrow["invoice_against_text"];
$_POST['direct_invoice_text']  = $myrow["direct_invoice_text"];
$_POST['customer_payment_text']  = $myrow["customer_payment_text"];
$_POST['customer_credit_note_text']  = $myrow["customer_credit_note_text"];
$_POST['allocate_text']  = $myrow["allocate_text"];
$_POST['sales_quotation_inquiry_text']  = $myrow["sales_quotation_inquiry_text"];
$_POST['sale_order_inquiry_text']  = $myrow["sale_order_inquiry_text"];
$_POST['customer_inquiry_text']  = $myrow["customer_inquiry_text"];
$_POST['customer_allocate_inquiry_text']  = $myrow["customer_allocate_inquiry_text"];
$_POST['sales_reports_text']  = $myrow["sales_reports_text"];
$_POST['manage_customer_text']  = $myrow["manage_customer_text"];
$_POST['customer_branches_text']  = $myrow["customer_branches_text"];
$_POST['sales_group_text']  = $myrow["sales_group_text"];
$_POST['sales_type_text']  = $myrow["sales_type_text"];
$_POST['sales_persons_text']  = $myrow["sales_persons_text"];
$_POST['sales_areas_text']  = $myrow["sales_areas_text"];
$_POST['wht_types_text']  = $myrow["wht_types_text"];
$_POST['merge_customers_text']  = $myrow["merge_customers_text"];
$_POST['merge_customers']  = $myrow["merge_customers"];
$_POST['import_customer_text']  = $myrow["import_customer_text"];
$_POST['import_customer']  = $myrow["import_customer"];
$_POST['import_customer_opening_text']  = $myrow["import_customer_opening_text"];
$_POST['import_customer_opening']  = $myrow["import_customer_opening"];
$_POST['sales_order_entry_mobile_text']  = $myrow["sales_order_entry_mobile_text"];
$_POST['sales_order_entry_mobile']  = $myrow["sales_order_entry_mobile"];
$_POST['direct_delivery_text']  = $myrow["direct_delivery_text"];
$_POST['direct_delivery']  = $myrow["direct_delivery"];
$_POST['template_delivery_text']  = $myrow["template_delivery_text"];
$_POST['template_delivery']  = $myrow["template_delivery"];
$_POST['template_invoice_text']  = $myrow["template_invoice_text"];
$_POST['template_invoice']  = $myrow["template_invoice"];
$_POST['create_print_recurrent_text']  = $myrow["create_print_recurrent_text"];
$_POST['create_print_recurrent']  = $myrow["create_print_recurrent"];
$_POST['recurrent_invoice_text']  = $myrow["recurrent_invoice_text"];
$_POST['recurrent_invoice']  = $myrow["recurrent_invoice"];
$_POST['credit_status_setup_text']  = $myrow["credit_status_setup_text"];
$_POST['credit_status_setup']  = $myrow["credit_status_setup"];
$_POST['invoice_prepaid_order_text']  = $myrow["invoice_prepaid_order_text"];
$_POST['gate_pass_text']  = $myrow["gate_pass_text"];
$_POST['gate_pass']  = $myrow["gate_pass"];
$_POST['sales_sms_text']  = $myrow["sales_sms_text"];
$_POST['sales_sms']  = $myrow["sales_sms"];
$_POST['sms_template_text']  = $myrow["sms_template_text"];
$_POST['sms_template']  = $myrow["sms_template"];




start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Sales"));
echo"<tr>";
label_cell(_("Sales"));
text_cells(_(""), 'sales_text', $_POST['sales_text'], 50);
check_cells(_(""), 'sales', null);
echo"</tr>";


table_section_title(_("Transactions"));
label_cell(_("Sale Quotation Entry"));
text_cells(_(""), 'sales_quotation_text', $_POST['sales_quotation_text'], 50,80);
check_cells(null, 'sales_quotation', null);
echo"<tr>";
label_cell(_("Sale Order Entry"));
text_cells(_(""), 'sale_order_entry_text', $_POST['sale_order_entry_text'], 50,80);
check_cells(null, 'sale_order_entry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("POS"));
text_cells(_(""), 'pos_text', $_POST['pos_text'], 50,80);
check_cells(null, 'pos', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sales Order Entry For Mobile"));
text_cells(_(""), 'sales_order_entry_mobile_text', $_POST['sales_order_entry_mobile_text'], 50);
check_cells(_(""), 'sales_order_entry_mobile', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Delivery Against Sales Orders"));
text_cells(null, 'delivery_against_text', $_POST['delivery_against_text'],  50,80);
check_cells(_(""), 'delivery_against', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Invoice Against Sales Delivery"));
text_cells(_(""), 'invoice_against_text', $_POST['invoice_against_text'], 50);
check_cells(_(""), 'invoice_against', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Direct Delivery"));
text_cells(_(""), 'direct_delivery_text', $_POST['direct_delivery_text'], 50);
check_cells(_(""), 'direct_delivery', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Direct Invoice"));
text_cells(_(""), 'direct_invoice_text', $_POST['direct_invoice_text'], 50);
check_cells(_(""), 'direct_invoice', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Invoice Prepaid Orders"));
text_cells(_(""), 'invoice_prepaid_order_text', $_POST['invoice_prepaid_order_text'], 50);
check_cells(_(""), 'invoice_prepaid_order', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Customer Payments"));
text_cells(_(""), 'customer_payment_text', $_POST['customer_payment_text'], 50);
check_cells(_(""), 'customer_payment', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Customer Credit Note"));
text_cells(_(""), 'customer_credit_note_text', $_POST['customer_credit_note_text'], 50);
check_cells(_(""), 'customer_credit_note', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Allocate Customer Payments Or Credit Notes"));
text_cells(_(""), 'allocate_text', $_POST['allocate_text'], 50);
check_cells(_(""), 'allocate', null);
echo"</tr>";




table_section_title(_("Inquiries and Reports"));
echo"<tr>";
label_cell(_("Sale Quotation Inquiry"));
text_cells(_(""), 'sales_quotation_inquiry_text', $_POST['sales_quotation_inquiry_text'], 50);
check_cells(_(""), 'sales_quotation_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Sale Order Inquiry"));
text_cells(_(""), 'sale_order_inquiry_text', $_POST['sale_order_inquiry_text'], 50);
check_cells(_(""), 'sale_order_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Customer Transaction Inquiry"));
text_cells(_(""), 'customer_inquiry_text', $_POST['customer_inquiry_text'], 50);
check_cells(_(""), 'customer_inquiry', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Customer Allocate Inquiry"));
text_cells(_(""), 'customer_allocate_inquiry_text', $_POST['customer_allocate_inquiry_text'], 50);
check_cells(_(""), 'customer_allocate_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Customer And Sales Reports"));
text_cells(_(""), 'sales_reports_text', $_POST['sales_reports_text'], 50);
check_cells(_(""), 'sales_reports', null);
echo"</tr>";


table_section_title(_("Maintenance"));
echo"<tr>";
label_cell(_("Add And Manage Customer"));
text_cells(_(""), 'manage_customer_text', $_POST['manage_customer_text'], 50);
check_cells(_(""), 'manage_customer', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Customer Branches"));
text_cells(_(""), 'customer_branches_text', $_POST['customer_branches_text'], 50);
check_cells(_(""), 'customer_branches', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Merge Customers"));
text_cells(_(""), 'merge_customers_text', $_POST['merge_customers_text'], 50);
check_cells(_(""), 'merge_customers', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sales Groups"));
text_cells(_(""), 'sales_group_text', $_POST['sales_group_text'], 50);
check_cells(_(""), 'sales_group', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Import Customers"));
text_cells(_(""), 'import_customer_text', $_POST['import_customer_text'], 50);
check_cells(_(""), 'import_customer', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sales Type"));
text_cells(_(""), 'sales_type_text', $_POST['sales_type_text'], 50);
check_cells(_(""), 'sales_type', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Sales Persons"));
text_cells(_(""), 'sales_persons_text', $_POST['sales_persons_text'], 50);
check_cells(_(""), 'sales_persons', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Sales Areas"));
text_cells(_(""), 'sales_areas_text', $_POST['sales_areas_text'], 50);
check_cells(_(""), 'sales_areas', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sales email"));
text_cells(_(""), 'sales_email_text', $_POST['sales_email_text'], 50);
check_cells(_(""), 'sales_email', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Gate Pass Dashboard"));
text_cells(_(""), 'gate_pass_text', $_POST['gate_pass_text'], 50);
check_cells(_(""), 'gate_pass', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Sales Sms"));
text_cells(_(""), 'sales_sms_text', $_POST['sales_sms_text'], 50);
check_cells(_(""), 'sales_sms', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sms Template"));
text_cells(_(""), 'sms_template_text', $_POST['sms_template_text'], 50);
check_cells(_(""), 'sms_template', null);
echo"</tr>";




echo"<tr>";
label_cell(_("Import Bulk Invoices"));
text_cells(_(""), 'import_bulk_invoice_text', $_POST['import_bulk_invoice_text'], 50);
check_cells(_(""), 'import_bulk_invoice', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Import Bulk Credits"));
text_cells(_(""), 'import_bulk_credit_text', $_POST['import_bulk_credit_text'], 50);
check_cells(_(""), 'import_bulk_credit', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Credit Status Setup"));
text_cells(_(""), 'credit_status_setup_text', $_POST['credit_status_setup_text'], 50);
check_cells(_(""), 'credit_status_setup', null);
echo"</tr>";

echo"<tr>";
label_cell(_("WHT Types"));
text_cells(_(""), 'wht_types_text', $_POST['wht_types_text'], 50);
check_cells(_(""), 'wht_types', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Import CVS Customers Opening"));
text_cells(_(""), 'import_customer_opening_text', $_POST['import_customer_opening_text'], 50);
check_cells(_(""), 'import_customer_opening', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Template Delivery"));
text_cells(_(""), 'template_delivery_text', $_POST['template_delivery_text'], 50);
check_cells(_(""), 'template_delivery', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Template Invoice"));
text_cells(_(""), 'template_invoice_text', $_POST['template_invoice_text'], 50);
check_cells(_(""), 'template_invoice', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Create and Print Recurrent Invoices"));
text_cells(_(""), 'create_print_recurrent_text', $_POST['create_print_recurrent_text'], 50);
check_cells(_(""), 'create_print_recurrent', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Recurrent &Invoices"));
text_cells(_(""), 'recurrent_invoice_text', $_POST['recurrent_invoice_text'], 50);
check_cells(_(""), 'recurrent_invoice', null);
echo"</tr>";

end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

