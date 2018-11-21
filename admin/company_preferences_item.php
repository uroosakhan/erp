<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Company Setup - Item And Inventory "));

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
			get_post( array('inventry_location',
			'inventory_adjustments','inventory_item_movemnets','inventory_item_status',
			'inventory_reports','items','foreign_item_codes','sales_kits','item_categories'
			,'inventory_locations','unit_measure','recorder_levels','import_csv','sales_pricing'
			,'purchase_pricing','standard_cost','item','item_text','inventry_location_text',
			'inventory_adjustments_text','inventory_item_movemnets_text','inventory_item_status_text',
			'inventory_reports_text','items_text','foreign_item_codes_text','sales_kits_text','item_categories_text'
			,'inventory_locations_text','unit_measure_text','recorder_levels_text','import_csv_text','sales_pricing_text'
			,'purchase_pricing_text','standard_cost_text','daily_movement_text','daily_movement','import_opening_balance_text'
                ,'import_opening_balance','add_and_manage_text','add_and_manage','updated_items_text','updated_items' ,
                'add_category_text','add_category','category_status_text','category_status','import_item_text','import_item'
                ,'batch_text','batch','location_transfer_dashboard','location_transfer_dashboard_text')
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

/////////////inventory

$_POST['inventry_location']  = $myrow["inventry_location"];
$_POST['inventory_adjustments']  = $myrow["inventory_adjustments"];
$_POST['inventory_item_movemnets']  = $myrow["inventory_item_movemnets"];
$_POST['inventory_item_status']  = $myrow["inventory_item_status"];
$_POST['inventory_reports']  = $myrow["inventory_reports"];
$_POST['items']  = $myrow["items"];
$_POST['foreign_item_codes']  = $myrow["foreign_item_codes"];
$_POST['sales_kits']  = $myrow["sales_kits"];
$_POST['item_categories']  = $myrow["item_categories"];
$_POST['inventory_locations']  = $myrow["inventory_locations"];
$_POST['unit_measure']  = $myrow["unit_measure"];
$_POST['recorder_levels']  = $myrow["recorder_levels"];
$_POST['import_csv']  = $myrow["import_csv"];
$_POST['sales_pricing']  = $myrow["sales_pricing"];
$_POST['purchase_pricing']  = $myrow["purchase_pricing"];
$_POST['standard_cost']  = $myrow["standard_cost"];
$_POST['daily_movement']  = $myrow["daily_movement"];
$_POST['daily_movement_text']  = $myrow["daily_movement_text"];

$_POST['inventry_location_text']  = $myrow["inventry_location_text"];
$_POST['inventory_adjustments_text']  = $myrow["inventory_adjustments_text"];
$_POST['inventory_item_movemnets_text']  = $myrow["inventory_item_movemnets_text"];
$_POST['inventory_item_status_text']  = $myrow["inventory_item_status_text"];
$_POST['inventory_reports_text']  = $myrow["inventory_reports_text"];
$_POST['items_text']  = $myrow["items_text"];
$_POST['foreign_item_codes_text']  = $myrow["foreign_item_codes_text"];
$_POST['sales_kits_text']  = $myrow["sales_kits_text"];
$_POST['item_categories_text']  = $myrow["item_categories_text"];
$_POST['unit_measure_text']  = $myrow["unit_measure_text"];
$_POST['recorder_levels_text']  = $myrow["recorder_levels_text"];
$_POST['import_csv_text']  = $myrow["import_csv_text"];
$_POST['sales_pricing_text']  = $myrow["sales_pricing_text"];
$_POST['purchase_pricing_text']  = $myrow["purchase_pricing_text"];
$_POST['standard_cost_text']  = $myrow["standard_cost_text"];
$_POST['item']  = $myrow["item"];
$_POST['item_text']  = $myrow["item_text"];
$_POST['inventory_locations_text']  = $myrow["inventory_locations_text"];
$_POST['import_opening_balance_text']  = $myrow["import_opening_balance_text"];
$_POST['import_opening_balance']  = $myrow["import_opening_balance"];
$_POST['daily_inventory_movemnets_text']  = $myrow["daily_inventory_movemnets_text"];
$_POST['daily_inventory_movemnets']  = $myrow["daily_inventory_movemnets"];
$_POST['add_and_manage_text']  = $myrow["add_and_manage_text"];
$_POST['add_and_manage']  = $myrow["add_and_manage"];
$_POST['updated_items_text']  = $myrow["updated_items_text"];
$_POST['updated_items']  = $myrow["updated_items"];
$_POST['add_category_text']  = $myrow["add_category_text"];
$_POST['add_category']  = $myrow["add_category"];
$_POST['category_status_text']  = $myrow["category_status_text"];
$_POST['category_status']  = $myrow["category_status"];
$_POST['import_item_text']  = $myrow["import_item_text"];
$_POST['import_item']  = $myrow["import_item"];
$_POST['batch_text']  = $myrow["batch_text"];
$_POST['batch']  = $myrow["batch"];
$_POST['location_transfer_dashboard_text']  = $myrow["location_transfer_dashboard_text"];
$_POST['location_transfer_dashboard']  = $myrow["location_transfer_dashboard"];


start_outer_table(TABLESTYLE2);


table_section(3);
table_section_title(_("Items and Inventory"));
echo"<tr>";
label_cell(_("Item and Inventory"));
text_cells(_(""), 'item_text', $_POST['item_text'], 50);
check_cells(_(""), 'item', null);
echo"</tr>";

table_section_title(_("Transaction"));
echo"<tr>";
label_cell(_("Inventory Location Transfer"));
text_cells(_(""), 'inventry_location_text', $_POST['inventry_location_text'], 50);
check_cells(_(""), 'inventry_location', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Inventory Adjustments"));
text_cells(_(""), 'inventory_adjustments_text', $_POST['inventory_adjustments_text'], 50);
check_cells(_(""), 'inventory_adjustments', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Import Opening Balances"));
text_cells(_(""), 'import_opening_balance_text', $_POST['import_opening_balance_text'], 50);
check_cells(_(""), 'import_opening_balance', null);
echo"</tr>";

table_section_title(_("Inquiries And Reports"));
echo"<tr>";
label_cell(_("Inventory Item Movements"));
text_cells(_(""), 'inventory_item_movemnets_text', $_POST['inventory_item_movemnets_text'], 50);
check_cells(_(""), 'inventory_item_movemnets', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Inventory Item Status"));
text_cells(_(""), 'inventory_item_status_text', $_POST['inventory_item_status_text'], 50);
check_cells(_(""), 'inventory_item_status', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Daily Inventory Movements"));
text_cells(_(""), 'daily_movement_text', $_POST['daily_movement_text'], 50);
check_cells(_(""), 'daily_movement', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Location Transfer Dashboard"));
text_cells(_(""), 'location_transfer_dashboard_text', $_POST['location_transfer_dashboard_text'], 50);
check_cells(_(""), 'location_transfer_dashboard', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Inventory Reports"));
text_cells(_(""), 'inventory_reports_text', $_POST['inventory_reports_text'], 50);
check_cells(_(""), 'inventory_reports', null);
echo"</tr>";


table_section_title(_("Maintenance"));


echo"<tr>";
label_cell(_("Add And Manage Items"));
text_cells(_(""), 'add_and_manage_text', $_POST['add_and_manage_text'], 50);
check_cells(_(""), 'add_and_manage', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Update Item Code"));
text_cells(_(""), 'updated_items_text', $_POST['updated_items_text'], 50);
check_cells(_(""), 'updated_items', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Foreign Item Codes"));
text_cells(_(""), 'foreign_item_codes_text', $_POST['foreign_item_codes_text'], 50);
check_cells(_(""), 'foreign_item_codes', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Sales Kits"));
text_cells(_(""), 'sales_kits_text', $_POST['sales_kits_text'], 50);
check_cells(_(""), 'sales_kits', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Item Categories"));
text_cells(_(""), 'item_categories_text', $_POST['item_categories_text'], 50);
check_cells(_(""), 'item_categories', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Inventory Locations"));
text_cells(_(""), 'inventory_locations_text', $_POST['inventory_locations_text'], 50);
check_cells(_(""), 'inventory_locations', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Units Of Measure"));
text_cells(_(""), 'unit_measure_text', $_POST['unit_measure_text'], 50);
check_cells(_(""), 'unit_measure', null);
echo"</tr>";



echo"<tr>";
label_cell(_("Re order levels"));
text_cells(_(""), 'recorder_levels_text', $_POST['recorder_levels_text'], 50);
check_cells(_(""), 'recorder_levels', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Import Items"));
text_cells(_(""), 'import_item_text', $_POST['import_item_text'], 50);
check_cells(_(""), 'import_item', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Batch"));
text_cells(_(""), 'batch_text', $_POST['batch_text'], 50);
check_cells(_(""), 'batch', null);
echo"</tr>";


echo"<tr>";
label_cell(_("Category Status"));
text_cells(_(""), 'category_status_text', $_POST['category_status_text'], 50);
check_cells(_(""), 'category_status', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Import CSV Items"));
text_cells(_(""), 'import_csv_text', $_POST['import_csv_text'], 50);
check_cells(_(""), 'import_csv', null);
echo"</tr>";

// echo"<tr>";
// label_cell(_("Items"));
// text_cells(_(""), 'items_text', $_POST['items_text'], 50);
// check_cells(_(""), 'items', null);
// echo"</tr>";

table_section_title(_("Pricing and Costs"));
echo"<tr>";
label_cell(_("Sales Pricing"));
text_cells(_(""), 'sales_pricing_text', $_POST['sales_pricing_text'], 50);
check_cells(_(""), 'sales_pricing', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Purchasing Pricing"));
text_cells(_(""), 'purchase_pricing_text', $_POST['purchase_pricing_text'], 50);
check_cells(_(""), 'purchase_pricing', null);
echo"</tr>";

echo"<tr>";
label_cell(_("Standard Costs"));
text_cells(_(""), 'standard_cost_text', $_POST['standard_cost_text'], 50);
check_cells(_(""), 'standard_cost', null);
echo"</tr>";



end_outer_table(1);

hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');

end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

