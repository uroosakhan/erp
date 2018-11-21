<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Purchase Pref Setup"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/access_levels.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{
	global $Ajax;
	{
		$sql = "UPDATE `0_purch_pref` SET 
						label_value =".db_escape(get_post('label_value'))." ,
						po_enable =".db_escape(get_post('po1_enable'))." ,
						grn_enable=".db_escape(get_post('grn_enable'))." 
						WHERE  name='cart1'";
		db_query($sql,"The sales group could not be updated");

		$sql1 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value2'))." ,
						po_enable =".db_escape(get_post('po_enable2'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='cart2'";
		db_query($sql1,"The sales group could not be updated");

		$sql2 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value3'))." ,
						po_enable =".db_escape(get_post('po_enable3'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='cart3'";
		db_query($sql2,"The sales group could not be updated");

		$sql3 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value4'))." ,
						po_enable =".db_escape(get_post('po_enable4'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='cart4'";
		db_query($sql3,"The sales group could not be updated");

		$sql4 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value5'))." ,
						po_enable =".db_escape(get_post('po_enable5'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='cart5'";
		db_query($sql4,"The sales group could not be updated");

		$sql5 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value6'))." ,
						po_enable =".db_escape(get_post('po_enable6'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='cart6'";
		db_query($sql5,"The sales group could not be updated");


		$sql6 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value7'))." ,
						po_enable =".db_escape(get_post('po_enable7'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text1'";
		db_query($sql6,"The sales group could not be updated");


		$sql7 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value8'))." ,
						po_enable =".db_escape(get_post('po_enable8'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text2'";
		db_query($sql7,"The sales group could not be updated");


		$sql8 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value16'))." ,
						po_enable =".db_escape(get_post('po_enable16'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text3'";
		db_query($sql8,"The sales group could not be updated");


		$sql9 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value17'))." ,
						po_enable =".db_escape(get_post('po_enable17'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text4'";
		db_query($sql9,"The sales group could not be updated");



		$sq20 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value18'))." ,
						po_enable =".db_escape(get_post('po_enable18'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text5'";
		db_query($sq20,"The sales group could not be updated");



		$sq21 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value19'))." ,
						po_enable =".db_escape(get_post('po_enable19'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='footer_long_text6'";
		db_query($sq21,"The sales group could not be updated");


		$sq22 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value9'))." ,
						po_enable =".db_escape(get_post('po_enable9'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='header_long_text'";
		db_query($sq22,"The sales group could not be updated");

		$sq23 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value10'))." ,
						po_enable =".db_escape(get_post('po_enable10'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='header_text1'";
		db_query($sq23,"The sales group could not be updated");

		$sq24 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value11'))." ,
						po_enable =".db_escape(get_post('po_enable11'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='header_text2'";
		db_query($sq24,"The sales group could not be updated");


		$sq25 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value12'))." ,
						po_enable =".db_escape(get_post('po_enable12'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='header_text3'";
		db_query($sq25,"The sales group could not be updated");


		$sq26 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value13'))." ,
						po_enable =".db_escape(get_post('po_enable13'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='comb1'";
		db_query($sq26,"The sales group could not be updated");


		$sq27 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value14'))." ,
						po_enable =".db_escape(get_post('po_enable14'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='comb2'";
		db_query($sq27,"The sales group could not be updated");

		$sq28 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value15'))." ,
						po_enable =".db_escape(get_post('po_enable15'))." ,
						grn_enable=".db_escape(0)."
						WHERE  name='comb3'";
		db_query($sq28,"The sales group could not be updated");

		$sq29 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value22')).",
						po_enable =".db_escape(get_post('po_enable14'))."
						WHERE  name='total_headers'";
		db_query($sq29,"The sales group could not be updated");

		$sq30 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value21')).",
						po_enable =".db_escape(get_post('po_enable21'))."
						WHERE  name='total_headers2'";
		db_query($sq30,"The sales group could not be updated");



		$sq31 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value22')).",
						 po_enable =".db_escape(get_post('po_enable22'))."
						WHERE  name='h_combo1'";
		db_query($sq31,"The sales group could not be updated");

		$sq32 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value33')).",
						 po_enable =".db_escape(get_post('po_enable33'))."
						WHERE  name='h_combo2'";
		db_query($sq32,"The sales group could not be updated");


		$sq33 = "UPDATE `0_purch_pref` SET
						label_value =".db_escape(get_post('label_value44')).",
						 po_enable =".db_escape(get_post('po_enable44'))."
						WHERE  name='h_combo3'";
		db_query($sq33,"The sales group could not be updated");


//		update_purch_pref(
//			get_post( array('cart1','grn_enable','po_enable','label_value'
//
//
//
//				)
//			)
//
//		);

//		$_SESSION['wa_current_user']->timeout = $_POST['login_tout'];
		display_notification_centered(_("Purchase Pref setup has been updated."));
	}
	set_focus('label_value');
	$Ajax->activate('_page_body');
	meta_forward($_SERVER['PHP_SELF']);
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
				padding:20px;
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
		<td><a class="hvr-float-shadow" href="#"><i class="fa fa-shopping-cart" style="margin-right: 5px; font-size: large;"></i> PURCHASE PREF</a></td>

		<td><a class="hvr-float-shadow" href="item_pref.php"><i class="fa fa-barcode" style="margin-right: 5px; font-size: large;"></i> ITEM PREF</a></td>
		<td><a class="hvr-float-shadow" href="company_preferences_new.php"><i class="fa fa-circle-o" style="font-size: large; margin-right: 5px;"></i> FORM DISPLAY</a></td>
		<td><a class="hvr-float-shadow" href="print_from_setup.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT DISPLAY</a></td>

		<td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
		<td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
		<td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>
		<td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> HEADER</a></td>

	</center>



	</body>
	</html>


<?php

start_form(true);
div_start('_page_body');
$myrow1 = get_company_purch_pref('cart1');
$_POST['label_value'] = $myrow1["label_value"];
$_POST['grn_enable'] = $myrow1["grn_enable"];
$_POST['po1_enable'] = $myrow1["po_enable"];

$myrow2 = get_company_purch_pref('cart2');
$_POST['label_value2'] = $myrow2["label_value"];
$_POST['po_enable2'] = $myrow2["po_enable"];

$myrow3 = get_company_purch_pref('cart3');
$_POST['label_value3'] = $myrow3["label_value"];
$_POST['po_enable3'] = $myrow3["po_enable"];

$myrow4 = get_company_purch_pref('cart4');
$_POST['label_value4'] = $myrow4["label_value"];
$_POST['po_enable4'] = $myrow4["po_enable"];

$myrow5 = get_company_purch_pref('cart5');
$_POST['label_value5'] = $myrow5["label_value"];
$_POST['po_enable5'] = $myrow5["po_enable"];

$myrow6 = get_company_purch_pref('cart6');
$_POST['label_value6'] = $myrow6["label_value"];
$_POST['po_enable6'] = $myrow6["po_enable"];

//////////////////////for footer

$myrow7 = get_company_purch_pref('footer_long_text1');
$_POST['label_value7'] = $myrow7["label_value"];
$_POST['po_enable7'] = $myrow7["po_enable"];


$myrow8 = get_company_purch_pref('footer_long_text2');
$_POST['label_value8'] = $myrow8["label_value"];
$_POST['po_enable8'] = $myrow8["po_enable"];



$myrow8 = get_company_purch_pref('footer_long_text3');
$_POST['label_value16'] = $myrow8["label_value"];
$_POST['po_enable16'] = $myrow8["po_enable"];



$myrow8 = get_company_purch_pref('footer_long_text4');
$_POST['label_value17'] = $myrow8["label_value"];
$_POST['po_enable17'] = $myrow8["po_enable"];



$myrow8 = get_company_purch_pref('footer_long_text5');
$_POST['label_value18'] = $myrow8["label_value"];
$_POST['po_enable18'] = $myrow8["po_enable"];



$myrow8 = get_company_purch_pref('footer_long_text6');
$_POST['label_value19'] = $myrow8["label_value"];
$_POST['po_enable19'] = $myrow8["po_enable"];



///////////////////header label text//////////
$myrow9 = get_company_purch_pref('header_long_text');
$_POST['label_value9'] = $myrow9["label_value"];
$_POST['po_enable9'] = $myrow9["po_enable"];


$myrow10 = get_company_purch_pref('header_text1');
$_POST['label_value10'] = $myrow10["label_value"];
$_POST['po_enable10'] = $myrow10["po_enable"];


$myrow11 = get_company_purch_pref('header_text2');
$_POST['label_value11'] = $myrow11["label_value"];
$_POST['po_enable11'] = $myrow11["po_enable"];


$myrow12 = get_company_purch_pref('header_text3');
$_POST['label_value12'] = $myrow12["label_value"];
$_POST['po_enable12'] = $myrow12["po_enable"];



$myrow13 = get_company_purch_pref('comb1');
$_POST['label_value13'] = $myrow13["label_value"];
$_POST['po_enable13'] = $myrow13["po_enable"];



$myrow14 = get_company_purch_pref('comb2');
$_POST['label_value14'] = $myrow14["label_value"];
$_POST['po_enable14'] = $myrow14["po_enable"];



$myrow15 = get_company_purch_pref('comb3');
$_POST['label_value15'] = $myrow15["label_value"];
$_POST['po_enable15'] = $myrow15["po_enable"];


$myrow17 = get_company_purch_pref('total_headers');
$_POST['label_value22'] = $myrow17["label_value"];


$myrow18 = get_company_purch_pref('total_headers2');
$_POST['label_value21'] = $myrow18["label_value"];




$myrow22 = get_company_purch_pref('h_combo1');
$_POST['label_value22'] = $myrow22["label_value"];
$_POST['po_enable22'] = $myrow22["po_enable"];


$myrow33 = get_company_purch_pref('h_combo2');
$_POST['label_value33'] = $myrow33["label_value"];
$_POST['po_enable33'] = $myrow33["po_enable"];


$myrow44 = get_company_purch_pref('h_combo3');
$_POST['label_value44'] = $myrow44["label_value"];
$_POST['po_enable44'] = $myrow44["po_enable"];


start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Purchase Order Cart GRN/PO"));
//table_section_title(_("GRN / PO"));
text_cells(_(""), 'label_value22', $_POST['label_value22'], 20);

// cart1//
echo'<tr>';
//echo "<br/>";
check_cells(_("Show header 1 "), 'grn_enable1', $_POST['grn_enable1']);
check_cells(_(""), ' po1_enable', $_POST['po1_enable']);
echo"<tr>";
text_cells(_(""), 'label_value', $_POST['label_value'], 40);
echo"</tr>";
// cart2//

echo"<tr>";
check_cells(_("Show Header 2"), 'grn_enable2', $_POST['grn_enable2']);
check_cells(_(""),'po_enable2', null);
echo"</tr>";
text_cells(_(""), 'label_value2', $_POST['label_value2'], 40);
// cart3//


echo"<tr>";
check_cells(_("Show Header 3"), 'grn_enable3', $_POST['grn_enable3']);
check_cells(_(""), 'po_enable3', null);
echo"</tr>";
text_cells(_(""), 'label_value3', $_POST['label_value3'], 40);
// cart4//
echo"<tr>";
check_cells(_("Show Header 4"), 'grn_enable4', $_POST['grn_enable4']);
check_cells(_(""), 'po_enable4', null);
echo"</tr>";
text_cells(_(""), 'label_value4', $_POST['label_value4'], 40);


echo"<tr>";
check_cells(_("Show Header 5"), 'grn_enable5', $_POST['grn_enable5']);
check_cells(_(""), 'po_enable5', null);
echo"</tr>";
text_cells(_(""), 'label_value5', $_POST['label_value5'], 40);

echo"<tr>";
check_cells(_("Show Header 6"), 'grn_enable6', $_POST['grn_enable6']);
check_cells(_(""), 'po_enable6', null);
echo"</tr>";
text_cells(_(""), 'label_value6', $_POST['label_value6'], 40);

table_section_title(_("Purchase Order Footer"));
echo"<tr>";
check_cells(_("Show Footer 1"), 'grn_enable7', $_POST['grn_enable7']);
check_cells(_(""), 'po_enable7', null);
echo"</tr>";
text_cells(_(""), 'label_value7', $_POST['label_value7'], 40);

echo"<tr>";
check_cells(_("Show Footer 2"), 'grn_enable8', $_POST['grn_enable8']);
check_cells(_(""), 'po_enable8', null);
echo"</tr>";
text_cells(_(""), 'label_value8', $_POST['label_value8'], 40);

echo"<tr>";
check_cells(_("Show Footer 3"), 'grn_enable16', $_POST['grn_enable16']);
check_cells(_(""), 'po_enable16', null);
echo"</tr>";
text_cells(_(""), 'label_value16', $_POST['label_value16'], 40);

echo"<tr>";
check_cells(_("Show Footer 4"), 'grn_enable17', $_POST['grn_enable17']);
check_cells(_(""), 'po_enable17', null);
echo"</tr>";
text_cells(_(""), 'label_value17', $_POST['label_value17'], 40);

echo"<tr>";
check_cells(_("Show Footer 5"), 'grn_enable18', $_POST['grn_enable18']);
check_cells(_(""), 'po_enable18', null);
echo"</tr>";
text_cells(_(""), 'label_value18', $_POST['label_value18'], 40);


echo"<tr>";
check_cells(_("Show Footer 6"), 'grn_enable19', $_POST['grn_enable19']);
check_cells(_(""), 'po_enable19', null);
echo"</tr>";
text_cells(_(""), 'label_value19', $_POST['label_value19'], 40);


table_section_title(_("Purchase Order header"));

//echo'<tr>';
//check_cells(_("Show Long Text Description"), 'grn_enable9', $_POST['grn_enable9']);
//check_cells(_(""), 'po_enable9', null);
//echo'</tr>';
//text_cells(_(""), 'label_value9', $_POST['label_value9'], 40);


echo'<tr>';
check_cells(_("Show Header Label text 1"), 'grn_enable10', $_POST['grn_enable10']);
check_cells(_(""), 'po_enable10', null);
echo'</tr>';
text_cells(_(""), 'label_value10', $_POST['label_value10'], 40);


echo'<tr>';
check_cells(_("Show Header Label text 2"), 'grn_enable11', $_POST['grn_enable11']);
check_cells(_(""), 'po_enable11', null);
echo'</tr>';
text_cells(_(""), 'label_value11', $_POST['label_value11'], 40);

echo'<tr>';
check_cells(_("Show Header Label text 3"), 'grn_enable12', $_POST['grn_enable12']);
check_cells(_(""), 'po_enable12', null);
echo'</tr>';
text_cells(_(""), 'label_value12', $_POST['label_value12'], 40);



table_section(3);
table_section_title(_("Purchase Order Cart Combo Box"));
text_cells(_(""), 'label_value21', $_POST['label_value21'], 20);

//label_cell(_("Combo 1"));

echo'<tr>';
echo'<td>';
echo'<h4>Combo1</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/combo_1.php?OutstandingOnly=1", _("Combo 1 Setup"));
check_row(_(""), 'po_enable13', null);
text_cells(_(""), 'label_value13', $_POST['label_value13'], 40);
echo '</td>';
echo'</tr>';

echo'<tr>';
echo'<td>';
echo'<h4>Combo 2</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/combo_2.php?OutstandingOnly=1", _("Combo 2 Setup"));
check_row(_(""), 'po_enable14', null);
text_cells(_(""), 'label_value14', $_POST['label_value14'], 40);
echo '</td>';
echo'</tr>';

echo'<td>';
echo'<h4>Combo 3</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/combo_3.php?OutstandingOnly=1", _("Combo 3 Setup"));
check_row(_(""), 'po_enable15', null);
text_cells(_(""), 'label_value15', $_POST['label_value15'], 40);
echo '</td>';



table_section_title(_("Purchase Order header Combo Box"));
//text_cells(_(""), 'label_value21', $_POST['label_value21'], 20);

//label_cell(_("Combo 1"));

echo'<tr>';
echo'<td>';
echo'<h4>Combo 1</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/ph_combo1.php?OutstandingOnly=1", _("Combo 1 Setup"));
check_row(_(""), 'po_enable22', null);
text_cells(_(""), 'label_value22', $_POST['label_value22'], 40);
echo '</td>';
echo'</tr>';

echo'<tr>';
echo'<td>';
echo'<h4>Combo 2</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/ph_combo2.php?OutstandingOnly=1", _("Combo 2 Setup"));
check_row(_(""), 'po_enable33', null);
text_cells(_(""), 'label_value33', $_POST['label_value33'], 40);
echo '</td>';
echo'</tr>';

echo'<td>';
echo'<h4>Combo 3</h4>';
hyperlink_params_separate("$path_to_root/purchasing/manage/ph_combo3.php?OutstandingOnly=1", _("Combo 3 Setup"));
check_row(_(""), 'po_enable44', null);
text_cells(_(""), 'label_value44', $_POST['label_value44'], 40);
echo '</td>';




end_outer_table(1);

//hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');
div_end();
end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

