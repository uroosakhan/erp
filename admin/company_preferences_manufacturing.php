<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup - Manufacturing "));

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
            get_post( array('work_order_entry','outstanding_work_order',
                'costed_bill_material','inventory_item_where_used'
            ,'work_order_inquiry','manufacturing_reports','bill_of_material','work_center'
            ,'work_order_entry_text','outstanding_work_order_text','costed_bill_material_text'
            ,'inventory_item_where_used_text'
            ,'work_order_inquiry_text','manufacturing_reports_text',
                'bill_of_material_text','work_center_text','manufacture_text',
                'manufacture','work_order_entry_multiple_text','work_order_entry_multiple','bom_inquiry',
                'bom_inquiry_text','work_order_entry_unassemble_text',
                'work_order_entry_advance_manufacture_text','work_order_entry_unassemble',
                'work_order_entry_advance_manufacture'

            ))

        );

//		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
//		display_notification_centered(_("Company setup has been updated."));
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

        <td><a class="hvr-float-shadow" href="hf_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i>HEADER/FOOTER</a></td>

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

$_POST['work_order_entry'] = $myrow["work_order_entry"];
$_POST['work_order_entry_unassemble'] = $myrow["work_order_entry_unassemble"];
$_POST['work_order_entry_advance_manufacture'] = $myrow["work_order_entry_advance_manufacture"];
$_POST['outstanding_work_order'] = $myrow["outstanding_work_order"];
$_POST['costed_bill_material'] = $myrow["costed_bill_material"];
$_POST['inventory_item_where_used'] = $myrow["inventory_item_where_used"];
$_POST['work_order_inquiry'] = $myrow["work_order_inquiry"];
$_POST['manufacturing_reports'] = $myrow["manufacturing_reports"];
$_POST['bill_of_material'] = $myrow["bill_of_material"];
$_POST['work_center'] = $myrow["work_center"];


$_POST['work_order_entry_text'] = $myrow["work_order_entry_text"];
$_POST['work_order_entry_unassemble_text'] = $myrow["work_order_entry_unassemble_text"];
$_POST['work_order_entry_advance_manufacture_text'] = $myrow["work_order_entry_advance_manufacture_text"];
$_POST['outstanding_work_order_text'] = $myrow["outstanding_work_order_text"];
$_POST['costed_bill_material_text'] = $myrow["costed_bill_material_text"];
$_POST['inventory_item_where_used_text'] = $myrow["inventory_item_where_used_text"];
$_POST['work_order_inquiry_text'] = $myrow["work_order_inquiry_text"];
$_POST['manufacturing_reports_text'] = $myrow["manufacturing_reports_text"];
$_POST['bill_of_material_text'] = $myrow["bill_of_material_text"];
$_POST['work_center_text'] = $myrow["work_center_text"];
$_POST['manufacture_text'] = $myrow["manufacture_text"];
$_POST['manufacture'] = $myrow["manufacture"];
$_POST['work_order_entry_multiple_text'] = $myrow["work_order_entry_multiple_text"];
$_POST['work_order_entry_multiple'] = $myrow["work_order_entry_multiple"];
$_POST['bom_inquiry_text'] = $myrow["bom_inquiry_text"];
$_POST['bom_inquiry'] = $myrow["bom_inquiry"];


start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Manufacturing"));
echo"<tr>";
label_cell(_("manufacturing"));
text_cells(_(""), 'manufacture_text', $_POST['manufacture_text'], 50);
check_cells(_(""), 'manufacture', null);
echo"</tr>";

table_section_title(_("Transactions"));

echo"<tr>";
label_cell(_("Work Order Entry"));
text_cells(_(""), 'work_order_entry_text', $_POST['work_order_entry_text'], 50);
check_cells(_(""), 'work_order_entry', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Work Order Entry Un-Assemble"));
text_cells(_(""), 'work_order_entry_unassemble_text', $_POST['work_order_entry_unassemble_text'], 50);
check_cells(_(""), 'work_order_entry_unassemble_text', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Work Order Entry Advance Manufactured"));
text_cells(_(""), 'work_order_entry_advance_manufacture_text', $_POST['work_order_entry_advance_manufacture_text'], 50);
check_cells(_(""), 'work_order_entry', null);
echo"</tr>";






echo"<tr>";
label_cell(_("Work Orders Entry Multiple"));
text_cells(_(""), 'work_order_entry_multiple_text', $_POST['work_order_entry_multiple_text'], 50);
check_cells(_(""), 'work_order_entry_multiple', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Outstanding Work Orders"));
text_cells(_(""), 'outstanding_work_order_text', $_POST['outstanding_work_order_text'], 50);
check_cells(_(""), 'outstanding_work_order', null);
echo"</tr>";


table_section_title(_("Inquiries And Reports"));
echo"<tr>";
label_cell(_("Costed Bill Of Material Inquiry"));
text_cells(_(""), 'costed_bill_material_text', $_POST['costed_bill_material_text'], 50);
check_cells(_(""), 'costed_bill_material', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Inventory Item Where Used Inquiry"));
text_cells(_(""), 'inventory_item_where_used_text', $_POST['inventory_item_where_used_text'], 50);
check_cells(_(""), 'inventory_item_where_used', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Work Order Inquiry"));
text_cells(_(""), 'work_order_inquiry_text', $_POST['work_order_inquiry_text'], 50);
check_cells(_(""), 'work_order_inquiry', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Manufacturing Reports"));
text_cells(_(""), 'manufacturing_reports_text', $_POST['manufacturing_reports_text'], 50);
check_cells(_(""), 'manufacturing_reports', null);
echo"</tr>";

table_section_title(_("Maintenance"));
echo"<tr>";
label_cell(_("Add and Manage BOM"));
text_cells(_(""), 'bom_inquiry_text', $_POST['bom_inquiry_text'], 50);
check_cells(_(""), 'bom_inquiry', null);
echo"</tr>";




echo"<tr>";
label_cell(_("Bills Of Material"));
text_cells(_(""), 'bill_of_material_text', $_POST['bill_of_material_text'], 50);
check_cells(_(""), 'bill_of_material', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Work Center"));
text_cells(_(""), 'work_center_text', $_POST['work_center_text'], 50);
check_cells(_(""), 'work_center', null);
echo"</tr>";

end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

