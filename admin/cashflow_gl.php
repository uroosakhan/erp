<?php

$page_security = 'SA_SALESMAN';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/admin/db/cashflow_db.inc");

page(_($help_context = "Cashflow_GL_Account"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);
//------------------------------------------------------------------------------------------------

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	//initialise no input errors assumed initially before we test
//	$input_error = 0;
//
//	if (strlen($_POST['name']) == 0)
//	{
//		$input_error = 1;
//		display_error(_("The cashflow categories name cannot be empty."));
//		set_focus('name');
//	}
//	$pr1 = check_num('provision', 0,100);
//	if (!$pr1 || !check_num('provision2', 0, 100)) {
//		$input_error = 1;
//		display_error( _("Salesman provision cannot be less than 0 or more than 100%."));
//		set_focus(!$pr1 ? 'provision' : 'provision2');
//	}
//	if (!check_num('break_pt', 0)) {
//		$input_error = 1;
//		display_error( _("Salesman provision breakpoint must be numeric and not less than 0."));
//		set_focus('break_pt');
//	}
	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			/*selected_id could also exist if submit had not been clicked this code would not run in this case cos submit is false of course  see the delete code below*/
			update_cashflow_gl($selected_id, $_POST['name'], $_POST['c_type'], $_POST['gl_account']);
		}
		else
		{
			/*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
			add_cashflow_gl($_POST['name'], $_POST['gl_account']);
		}

		if ($selected_id != -1)
			display_notification(_('Selected cashflow_gl_account data have been updated'));
		else
			display_notification(_('New cashflow_gl_account data have been added'));
		$Mode = 'RESET';
	}
}
if ($Mode == 'Delete')
{
	//the link to delete a selected record was clicked instead of the submit button

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'cust_branch', 'cashflow_categories'))
//	{
//		display_error(_("Cannot delete this cashflow categories because categories are set up referring to this cashflow_categories - first alter the categories concerned."));
//	}
//	else
//	{
	delete_cashflow_gl($selected_id);
	display_notification(_('Selected cashflow_gl_account data have been deleted'));
//	}
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}
//------------------------------------------------------------------------------------------------

$result = get_gl_acc_(check_value('show_inactive'));
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
        <td><a  class="hvr-float-shadow " href="gl_setup.php" ><i  class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> MAIN</a></td>

        <td><a class="hvr-float-shadow" href="so_pref.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> SALES PREF</a></td>
        <td><a class="hvr-float-shadow" href="purch_pref.php"><i class="fa fa-shopping-cart" style="margin-right: 5px; font-size: large;"></i> PURCHASE PREF</a></td>

        <td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
        <td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>
        
        <td><a class="hvr-float-shadow" href="meta_forward.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT PREFERENCES</a></td>
        
        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="#"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>
        <!--<td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> HEADER</a></td>-->

    </center>


<br>
</body>
</html>



<?php
start_form();
start_table(TABLESTYLE, "width='50%'");
$th = array(_("Name"), _("Cash Flow Type"), _("GL_Account")  , "", "");
inactive_control_column($th);
table_header($th);

$k = 0;

//marina
while ($myrow = db_fetch($result))
{
//
//
//	if ($myrow["flowtype"] == 0)
//
//	{
//		$a = 'No';
//
//	}
//	else
//	{
//		$a = 'Yes';
//
//	}
////////////
	alt_table_row_color($k);
//    label_cell($myrow["name"]);
	$c_type = get_c_type_id($myrow["cashflow_category_id"]);
	$cflow_type=get_c_types_name($c_type);
	label_cell(get_cash_flow_category($myrow["cashflow_category_id"]));
	label_cell($cflow_type);

	label_cell(get_gl_account22($myrow["gl_account"]));

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'gl_acc', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();

} //END WHILE LIST LOOP

inactive_control_row($th);
end_table();
echo '<br>';

//------------------------------------------------------------------------------------------------

//$_POST['salesman_email'] = "";
if ($selected_id != -1) {
	if ($Mode == 'Edit') {
		//editing an existing Sales-person
		//display_error($selected_id);
		$myrow = get_gl_acc_($selected_id);

		$_POST['name'] = $myrow["name"];
//		$_POST['c_type'] = $myrow["c_type"];
		$_POST['gl_account'] = $myrow["gl_account"];
	}
	hidden('selected_id', $selected_id);
}

start_table(TABLESTYLE2);

//text_row_ex(_("Name:"), 'name', 30);
cashflow_categories_list_row( _("Cashflow Category:"), 'name', null);
gl_all_accounts_list_row(_("GL Account:"), 'gl_account');
//yesno_list_row(_("Flow Type:"), 'flowtype', $_POST['flowtype'] , $name_yes="", $name_no="", false);
//text_row_ex(_("Flow Type:"), 'flowtype', 20);
end_table(1);

?>


	<center> <div id="example" style="background-color: #79B3D4; width: 400px;">
			<div class="demo-section k-content" style="background-color: #79B3D4; " >
				<div id="tabstrip1" style="background-color: #79B3D4; ">
					<ul style="background-color: #79B3D4;">


						<li  style="background-color: #79B3D4;" >
							<a href="cashflow_categories.php"  target="_blank"><i class="fa fa-dashboard " style="margin-right: 5px;">  </i> cash flow Catagries</a>
						</li>


						<li  style="background-color: #79B3D4;">
							<a href="cashflow_type.php"  target="_blank"> <i class="fa fa-line-chart" style="margin-right: 5px;"></i> cash flow type</a>
						</li>


					</ul>

				</div>
			</div>

			<script>
				$(document).ready(function() {
					$("#tabstrip1").kendoTabStrip({
						animation:  {
							open: {
								effects: "fadeIn"
							}
						}
					});
				});
			</script>
		</div></center>
	<br>
<?php
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();

