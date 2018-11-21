<?php
/**
 * Created by PhpStorm.
 * User: sheikh_salman
 * Date: 5/18/16
 * Time: 10:16 AM
 */

$page_security = 'SA_SUPPLIER';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "WHT Types"));
//echo "112";
include($path_to_root . "/includes/ui.inc");
include($path_to_root . "/admin/db//wht_types_db.inc");
include($path_to_root . "/sales/includes/db/wht_tax_category_db.inc");


simple_page_mode(true);

$parent_id = get_post('parent_id', 0);

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if(get_post('parent_id') == 0){
//		$input_error = 1;
//		display_error(_("Head is not selected."));
//		set_focus('parent_id');

	}
	else if (strlen($_POST['description']) == 0)
	{
		$input_error = 1;
		display_error(_("The wht type description cannot be empty."));
		set_focus('description');
	}

	if ($input_error != 1)
	{
		if ($selected_id != -1)
		{
			update_wht_type($selected_id, $_POST['description'], input_num('tax_percent'), $_POST['co_account'],
				$_POST['wth_tax_category'],$_POST['co_account_supplier'],$_POST['label_name']);
			$note = _('Selected wht type has been updated');
		}
		else
		{
			add_wht_type($_POST['description'], input_num('tax_percent'), $_POST['co_account'],
				$_POST['parent_id'],$_POST['wth_tax_category'],$_POST['co_account_supplier'],$_POST['label_name']);
			$note = _('New wht type has been added');
		}

		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*  if (key_in_foreign_table($selected_id, 'cust_branch', 'group_no'))
      {
          $cancel_delete = 1;
          display_error(_("Cannot delete this group because customers have been created using this group."));
      }*/
	if ($cancel_delete == 0)
	{
		delete_wht_type($selected_id);
		display_notification(_('Selected wht type has been deleted'));
	} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
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
        
        <td><a class="hvr-float-shadow" href="meta_forward.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT PREFERENCES</a></td>

        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>
	</center>



	</body>
	</html>


<?php

//-------------------------------------------------------------------------------------------------

$result = get_wht_types();

start_form();
start_table(TABLESTYLE, "width=80%", 2, 0, true);
$th = array(/*_("ID"),*/ _("WHT Type"), _("%"), _("A/C Sales"), _("A/C Supplier"), _("WHT Tax Type "), "");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result))
{

	alt_table_row_color($k);

//	label_cell($myrow["id"]);
	label_cell($myrow["description"]);
//	label_cell($myrow["label_name"]);
	//label_cell($myrow["head"]);
	label_cell($myrow["tax_percent"]." %");
	$acc = get_gl_account($myrow["co_account"]);
	$acc1 = get_gl_account($myrow["co_account_supplier"]);
	label_cell($myrow["co_account"]." - ".$acc["account_name"]);
	label_cell($myrow["co_account_supplier"]." - ".$acc1["account_name"]);
	label_cell(get_wht_tax_category_name($myrow["wth_tax_category"]));
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'wth_tax_types', 'id');
	edit_button_cell("Edit".$myrow["id"], _("Edit"));
	// delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}

//inactive_control_row($th);
end_table(1);

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2, "", 2, 0, true);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		//editing an existing group
		$myrow = get_wht_type($selected_id);

		$_POST['description']  = $myrow["description"];
		$_POST['label_name']  = $myrow["label_name"];
		$_POST['wth_tax_category']  = $myrow["wth_tax_category"];
		$_POST['tax_percent']  = $myrow["tax_percent"];
		$_POST['co_account']  = $myrow["co_account"];
		$_POST['co_account_supplier']  = $myrow["co_account_supplier"];



	}
	hidden("selected_id", $selected_id);
	label_row(_("ID"), $myrow["id"]);
	hidden('parent_id',$myrow["id"]);
}

//if ($selected_id == -1) {
//	wth_tax_type_list_row(_("Tax Head :"), 'parent_id', null,
//		_("Select Tax Type"), true, true); //asad
//
//
//}
wht_tax_category_list_row('WTH Tax Type','wth_tax_category');
text_row_ex( _("Wht Type:"), 'description', 30);
//text_row_ex( _("Name:"), 'label_name', 30);
amount_row(_("% :"), 'tax_percent');
gl_all_accounts_list_row(_("COA Sales:"), 'co_account');
gl_all_accounts_list_row(_("COA Supplier:"), 'co_account_supplier');


end_table(1);

//if ($selected_id != -1)
submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
?>
