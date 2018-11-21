<?php

$page_security = 'SA_SETUPCOMPANY';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Sales Order Setup - Pref "));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/access_levels.inc");
include_once($path_to_root . "/admin/db/company_db.inc");
//-------------------------------------------------------------------------------------------------

if (isset($_POST['update']) && $_POST['update'] != "")
{
	global $Ajax;

	{
		$sql = "UPDATE `0_sales_pref` SET 
						label_value =".db_escape(get_post('label_value'))." ,
						so_enable =".db_escape(get_post('so_enable1'))." 
						WHERE  name='so_cart1'";
		db_query($sql,"The sales group could not be updated");

		$sql1 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value2'))." ,
						so_enable =".db_escape(get_post('so_enable2'))."
	  					WHERE  name='so_cart2'";
		db_query($sql1,"The sales group could not be updated");

		$sql2 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value3'))." ,
						so_enable =".db_escape(get_post('so_enable3'))." 
						WHERE  name='so_cart3'";
		db_query($sql2,"The sales group could not be updated");

		$sql3 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value4'))." ,
						so_enable =".db_escape(get_post('so_enable4'))." 
						WHERE  name='so_cart4'";
		db_query($sql3,"The sales group could not be updated");

		$sql4 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value5'))." ,
						so_enable =".db_escape(get_post('so_enable5'))."
						WHERE  name='so_cart5'";
		db_query($sql4,"The sales group could not be updated");

		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value6'))." ,
						so_enable =".db_escape(get_post('so_enable6'))." 
						WHERE  name='so_cart6'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value7'))." ,
						so_enable =".db_escape(get_post('so_enable7'))." 
						WHERE  name='so_footer_long_text1'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value8'))." ,
						so_enable =".db_escape(get_post('so_enable8'))." 
						WHERE  name='so_footer_long_text2'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value16'))." ,
						so_enable =".db_escape(get_post('so_enable16'))."
						WHERE  name='so_footer_long_text3'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value17'))." ,
						so_enable =".db_escape(get_post('so_enable17'))."
						WHERE  name='so_footer_long_text4'";
		db_query($sql5,"The sales group could not be updated");



		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value18'))." ,
						so_enable =".db_escape(get_post('so_enable18'))."
						WHERE  name='so_footer_long_text5'";
		db_query($sql5,"The sales group could not be updated");



		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value19'))." ,
						so_enable =".db_escape(get_post('so_enable19'))."
						WHERE  name='so_footer_long_text6'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value9'))." ,
						so_enable =".db_escape(get_post('so_enable9'))."
						WHERE  name='so_header_long_text'";
		db_query($sql5,"The sales group could not be updated");

		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value10'))." ,
						so_enable =".db_escape(get_post('so_enable10'))."
						WHERE  name='so_header_text1'";
		db_query($sql5,"The sales group could not be updated");

		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value11'))." ,
						so_enable =".db_escape(get_post('so_enable11'))."
						WHERE  name='so_header_text2'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value12'))." ,
						so_enable =".db_escape(get_post('so_enable12'))."
						WHERE  name='so_header_text3'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value13'))." ,
						so_enable =".db_escape(get_post('so_enable13'))."
						WHERE  name='so_header_combo1'";
		db_query($sql5,"The sales group could not be updated");


		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value14'))." ,
						 	so_enable =".db_escape(get_post('so_enable14'))."
						WHERE  name='so_header_combo2'";
		db_query($sql5,"The sales group could not be updated");

		$sql5 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value15'))." ,
						 	so_enable =".db_escape(get_post('so_enable15'))."
						WHERE  name='so_header_combo3'";
		db_query($sql5,"The sales group could not be updated");

		$sq29 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value22'))." 
						WHERE  name='total_headers'";
		db_query($sq29,"The sales group could not be updated");

		$sq30 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value21'))." 
						WHERE  name='total_headers2'";
		db_query($sq30,"The sales group could not be updated");

		$sq31 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value31'))." ,
						so_enable =".db_escape(get_post('so_enable31'))."
						WHERE  name='text_box_1'";
		db_query($sq31,"The sales group could not be updated");

		$sq32 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value32')).",
						so_enable =".db_escape(get_post('so_enable32'))."
						WHERE  name='text_box_2'";
		db_query($sq32,"The sales group could not be updated");

		$sq33 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value33')).",
						 so_enable =".db_escape(get_post('so_enable33'))."
						WHERE  name='text_box_3'";
		db_query($sq33,"The sales group could not be updated");

		$sq34 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value34')).",
						 so_enable =".db_escape(get_post('so_enable34'))."
						WHERE  name='so_header_combo4'";
		db_query($sq34,"The sales group could not be updated");

		$sq35 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value35')).",
						so_enable =".db_escape(get_post('so_enable35'))."
						WHERE  name='so_header_combo5'";
		db_query($sq35,"The sales group could not be updated");

		$sq36 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value36')).",
						 so_enable =".db_escape(get_post('so_enable36'))."
						WHERE  name='so_header_combo6'";
		db_query($sq36,"The sales group could not be updated");


		$sq37 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value51')).",
					   so_enable =".db_escape(get_post('so_enable51'))."
						WHERE  name='so_header_text4'";
		db_query($sq37,"The sales group could not be updated");


		$sq38 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value52')).",
				        so_enable =".db_escape(get_post('so_enable52'))."
						WHERE  name='so_header_text5'";
		db_query($sq38,"The sales group could not be updated");


		$sq39 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value53')).",
						so_enable =".db_escape(get_post('so_enable53'))."
						WHERE  name='so_header_text6'";
		db_query($sq39,"The sales group could not be updated");


		$sq40 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value54')).",
						 so_enable =".db_escape(get_post('so_enable54'))."
						WHERE  name='text_box_4'";
		db_query($sq40,"The sales group could not be updated");


		$sq41 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value55')).",
						 so_enable =".db_escape(get_post('so_enable55'))."
						WHERE  name='text_box_5'";
		db_query($sq41,"The sales group could not be updated");


		$sq42 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value56')).",
						 so_enable =".db_escape(get_post('so_enable56'))."
						WHERE  name='text_box_6'";
		db_query($sq42,"The sales group could not be updated");


		$sq61 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value61')).",
						 so_enable =".db_escape(get_post('so_enable61'))."
						WHERE  name='amount_1'";
		db_query($sq61,"The sales group could not be updated");


		$sq62 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value62')).",
						 so_enable =".db_escape(get_post('so_enable62'))."
						WHERE  name='amount_2'";
		db_query($sq62,"The sales group could not be updated");


		$sq63 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value63')).",
						 so_enable =".db_escape(get_post('so_enable63'))."
						WHERE  name='amount_3'";
		db_query($sq63,"The sales group could not be updated");


		$sq64 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value64')).",
						 so_enable =".db_escape(get_post('so_enable64'))."
						WHERE  name='amount_4'";
		db_query($sq64,"The sales group could not be updated");


		$sq65 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value65')).",
						 so_enable =".db_escape(get_post('so_enable65'))."
						WHERE  name='amount_5'";
		db_query($sq65,"The sales group could not be updated");


		$sq66 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value66')).",
						 so_enable =".db_escape(get_post('so_enable66'))."
						WHERE  name='amount_6'";
		db_query($sq66,"The sales group could not be updated");



		$sq67 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value91')).",
						 so_enable =".db_escape(get_post('so_enable91'))."
						WHERE  name='h_combo1'";
		db_query($sq67,"The sales group could not be updated");



		$sq68 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value92')).",
						 so_enable =".db_escape(get_post('so_enable92'))."
						WHERE  name='h_combo2'";
		db_query($sq68,"The sales group could not be updated");



		$sq69 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value93')).",
						 so_enable =".db_escape(get_post('so_enable93'))."
						WHERE  name='h_combo3'";
		db_query($sq69,"The sales group could not be updated");




		$sq69 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value94')).",
						 so_enable =".db_escape(get_post('so_enable94'))."
						WHERE  name='f_combo1'";
		db_query($sq69,"The sales group could not be updated");




		$sq69 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value95')).",
						 so_enable =".db_escape(get_post('so_enable95'))."
						WHERE  name='f_combo2'";
		db_query($sq69,"The sales group could not be updated");




		$sq69 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value96')).",
						 so_enable =".db_escape(get_post('so_enable96'))."
						WHERE  name='f_combo3'";
		db_query($sq69,"The sales group could not be updated");



		$sq70 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value100')).",
						 so_enable =".db_escape(get_post('so_enable100'))."
						WHERE  name='date1'";
		db_query($sq70,"The sales group could not be updated");



		$sq71 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value101')).",
						 so_enable =".db_escape(get_post('so_enable101'))."
						WHERE  name='date2'";
		db_query($sq71,"The sales group could not be updated");



		$sq71 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value102')).",
						 so_enable =".db_escape(get_post('so_enable102'))."
						WHERE  name='date3'";
		db_query($sq71,"The sales group could not be updated");


		$sq72 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value111')).",
						 so_enable =".db_escape(get_post('so_enable111'))."
						WHERE  name='total_amount'";
		db_query($sq72,"The sales group could not be updated");



		$sq73 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value222')).",
						 so_enable =".db_escape(get_post('so_enable222'))."
						WHERE  name='total_long_footer'";
		db_query($sq73,"The sales group could not be updated");


		$sq74 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value333')).",
						 so_enable =".db_escape(get_post('so_enable333'))."
						WHERE  name='total_footer_text'";
		db_query($sq74,"The sales group could not be updated");


		$sq75 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value444')).",
						 so_enable =".db_escape(get_post('so_enable444'))."
						WHERE  name='total_header_text'";
		db_query($sq75,"The sales group could not be updated");

		$sq76 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value555')).",
						 so_enable =".db_escape(get_post('so_enable555'))."
						WHERE  name='total_headers_long'";
		db_query($sq76,"The sales group could not be updated");


		$sq77 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value666')).",
						 so_enable =".db_escape(get_post('so_enable666'))."
						WHERE  name='total_date'";
		db_query($sq77,"The sales group could not be updated");


		$sq78 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value777')).",
						 so_enable =".db_escape(get_post('so_enable777'))."
						WHERE  name='total_h_combo'";
		db_query($sq78,"The sales group could not be updated");


		$sq79 = "UPDATE `0_sales_pref` SET
						label_value =".db_escape(get_post('label_value888')).",
						 so_enable =".db_escape(get_post('label_value888'))."
						WHERE  name='total_long_footer'";
		db_query($sq79,"The sales group could not be updated");


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
		display_notification_centered(_("Sales Orders setup has been updated."));
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

		<td><a class="hvr-float-shadow" href="#"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> SALES PREF</a></td>
		<td><a class="hvr-float-shadow" href="purch_pref.php"><i class="fa fa-shopping-cart" style="margin-right: 5px; font-size: large;"></i> PURCHASE PREF</a></td>

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
$myrow1 = get_company_sales_pref('so_cart1');
$_POST['label_value'] = $myrow1["label_value"];
$_POST['so_enable1'] = $myrow1["so_enable"];

$myrow2 = get_company_sales_pref('so_cart2');
$_POST['label_value2'] = $myrow2["label_value"];
$_POST['so_enable2'] = $myrow2["so_enable"];

$myrow3 = get_company_sales_pref('so_cart3');
$_POST['label_value3'] = $myrow3["label_value"];
$_POST['so_enable3'] = $myrow3["so_enable"];

$myrow4 = get_company_sales_pref('so_cart4');
$_POST['label_value4'] = $myrow4["label_value"];
$_POST['so_enable4'] = $myrow4["so_enable"];

$myrow5 = get_company_sales_pref('so_cart5');
$_POST['label_value5'] = $myrow5["label_value"];
$_POST['so_enable5'] = $myrow5["so_enable"];

$myrow6 = get_company_sales_pref('so_cart6');
$_POST['label_value6'] = $myrow6["label_value"];
$_POST['so_enable6'] = $myrow6["so_enable"];

//////////////////////for footer

$myrow7 = get_company_sales_pref('so_footer_long_text1');
$_POST['label_value7'] = $myrow7["label_value"];
$_POST['so_enable7'] = $myrow7["so_enable"];


$myrow8 = get_company_sales_pref('so_footer_long_text2');
$_POST['label_value8'] = $myrow8["label_value"];
$_POST['so_enable8'] = $myrow8["so_enable"];



$myrow8 = get_company_sales_pref('so_footer_long_text3');
$_POST['label_value16'] = $myrow8["label_value"];
$_POST['so_enable16'] = $myrow8["so_enable"];



$myrow8 = get_company_sales_pref('so_footer_long_text4');
$_POST['label_value17'] = $myrow8["label_value"];
$_POST['so_enable17'] = $myrow8["so_enable"];



$myrow8 = get_company_sales_pref('so_footer_long_text5');
$_POST['label_value18'] = $myrow8["label_value"];
$_POST['so_enable18'] = $myrow8["so_enable"];



$myrow8 = get_company_sales_pref('so_footer_long_text6');
$_POST['label_value19'] = $myrow8["label_value"];
$_POST['so_enable19'] = $myrow8["so_enable"];



///////////////////header label text//////////
$myrow9 = get_company_sales_pref('so_header_long_text');
$_POST['label_value9'] = $myrow9["label_value"];
$_POST['so_enable9'] = $myrow9["so_enable"];


$myrow10 = get_company_sales_pref('so_header_text1');
$_POST['label_value10'] = $myrow10["label_value"];
$_POST['so_enable10'] = $myrow10["so_enable"];


$myrow11 = get_company_sales_pref('so_header_text2');
$_POST['label_value11'] = $myrow11["label_value"];
$_POST['so_enable11'] = $myrow11["so_enable"];


$myrow12 = get_company_sales_pref('so_header_text3');
$_POST['label_value12'] = $myrow12["label_value"];
$_POST['so_enable12'] = $myrow12["so_enable"];


$myrow37 = get_company_sales_pref('so_header_text4');
$_POST['label_value51'] = $myrow37["label_value"];
$_POST['so_enable51'] = $myrow37["so_enable"];


$myrow38 = get_company_sales_pref('so_header_text5');
$_POST['label_value52'] = $myrow38["label_value"];
$_POST['so_enable52'] = $myrow38["so_enable"];


$myrow38 = get_company_sales_pref('so_header_text6');
$_POST['label_value53'] = $myrow38["label_value"];
$_POST['so_enable53'] = $myrow38["so_enable"];



$myrow13 = get_company_sales_pref('so_header_combo1');
$_POST['label_value13'] = $myrow13["label_value"];
$_POST['so_enable13'] = $myrow13["so_enable"];



$myrow14 = get_company_sales_pref('so_header_combo2');
$_POST['label_value14'] = $myrow14["label_value"];
$_POST['so_enable14'] = $myrow14["so_enable"];



$myrow15 = get_company_sales_pref('so_header_combo3');
$_POST['label_value15'] = $myrow15["label_value"];
$_POST['so_enable15'] = $myrow15["so_enable"];


$myrow17 = get_company_sales_pref('total_headers');
$_POST['label_value22'] = $myrow17["label_value"];

$myrow111 = get_company_sales_pref('total_amount');
$_POST['label_value111'] = $myrow111["label_value"];


$myrow222 = get_company_sales_pref('total_long_footer');
$_POST['label_value222'] = $myrow222["label_value"];


$myrow333 = get_company_sales_pref('total_footer_text');
$_POST['label_value333'] = $myrow333["label_value"];


$myrow18 = get_company_sales_pref('total_headers2');
$_POST['label_value21'] = $myrow18["label_value"];


$myrow444 = get_company_sales_pref('total_header_text');
$_POST['label_value444'] = $myrow444["label_value"];



$myrow555 = get_company_sales_pref('total_headers_long');
$_POST['label_value555'] = $myrow555["label_value"];



$myrow666 = get_company_sales_pref('total_date');
$_POST['label_value666'] = $myrow666["label_value"];


$myrow777 = get_company_sales_pref('total_h_combo');
$_POST['label_value777'] = $myrow777["label_value"];


$myrow888 = get_company_sales_pref('total_long_footer');
$_POST['label_value888'] = $myrow777["label_value"];


$myrow31 = get_company_sales_pref('text_box_1');
$_POST['label_value31'] = $myrow31["label_value"];
$_POST['so_enable31'] = $myrow31["so_enable"];

$myrow32 = get_company_sales_pref('text_box_2');
$_POST['label_value32'] = $myrow32["label_value"];
$_POST['so_enable32'] = $myrow32["so_enable"];

$myrow33 = get_company_sales_pref('text_box_3');
$_POST['label_value33'] = $myrow33["label_value"];
$_POST['so_enable33'] = $myrow33["so_enable"];

$myrow34 = get_company_sales_pref('so_header_combo4');
$_POST['label_value34'] = $myrow34["label_value"];
$_POST['so_enable34'] = $myrow34["so_enable"];

$myrow35 = get_company_sales_pref('so_header_combo5');
$_POST['label_value35'] = $myrow35["label_value"];
$_POST['so_enable35'] = $myrow35["so_enable"];

$myrow36 = get_company_sales_pref('so_header_combo6');
$_POST['label_value36'] = $myrow36["label_value"];
$_POST['so_enable36'] = $myrow36["so_enable"];


$myrow54 = get_company_sales_pref('text_box_4');
$_POST['label_value54'] = $myrow54["label_value"];
$_POST['so_enable54'] = $myrow54["so_enable"];



$myrow55 = get_company_sales_pref('text_box_5');
$_POST['label_value55'] = $myrow55["label_value"];
$_POST['so_enable55'] = $myrow55["so_enable"];


$myrow56 = get_company_sales_pref('text_box_6');
$_POST['label_value56'] = $myrow56["label_value"];
$_POST['so_enable56'] = $myrow56["so_enable"];



$myrow61 = get_company_sales_pref('amount_1');
$_POST['label_value61'] = $myrow61["label_value"];
$_POST['so_enable61'] = $myrow61["so_enable"];



$myrow62 = get_company_sales_pref('amount_2');
$_POST['label_value62'] = $myrow62["label_value"];
$_POST['so_enable62'] = $myrow62["so_enable"];



$myrow63 = get_company_sales_pref('amount_3');
$_POST['label_value63'] = $myrow63["label_value"];
$_POST['so_enable63'] = $myrow63["so_enable"];



$myrow64 = get_company_sales_pref('amount_4');
$_POST['label_value64'] = $myrow64["label_value"];
$_POST['so_enable64'] = $myrow64["so_enable"];



$myrow65 = get_company_sales_pref('amount_5');
$_POST['label_value65'] = $myrow65["label_value"];
$_POST['so_enable65'] = $myrow65["so_enable"];



$myrow66 = get_company_sales_pref('amount_6');
$_POST['label_value66'] = $myrow66["label_value"];
$_POST['so_enable66'] = $myrow66["so_enable"];



$myrow61 = get_company_sales_pref('h_combo1');
$_POST['label_value91'] = $myrow61["label_value"];
$_POST['so_enable91'] = $myrow61["so_enable"];



$myrow62 = get_company_sales_pref('h_combo2');
$_POST['label_value92'] = $myrow62["label_value"];
$_POST['so_enable92'] = $myrow62["so_enable"];



$myrow63 = get_company_sales_pref('h_combo3');
$_POST['label_value93'] = $myrow63["label_value"];
$_POST['so_enable93'] = $myrow63["so_enable"];



$myrow64 = get_company_sales_pref('f_combo1');
$_POST['label_value94'] = $myrow64["label_value"];
$_POST['so_enable94'] = $myrow64["so_enable"];



$myrow65 = get_company_sales_pref('f_combo2');
$_POST['label_value95'] = $myrow65["label_value"];
$_POST['so_enable95'] = $myrow65["so_enable"];



$myrow66 = get_company_sales_pref('f_combo3');
$_POST['label_value96'] = $myrow66["label_value"];
$_POST['so_enable96'] = $myrow66["so_enable"];



$myrow100 = get_company_sales_pref('date1');
$_POST['label_value100'] = $myrow100["label_value"];
$_POST['so_enable100'] = $myrow100["so_enable"];



$myrow101 = get_company_sales_pref('date2');
$_POST['label_value101'] = $myrow101["label_value"];
$_POST['so_enable101'] = $myrow101["so_enable"];



$myrow102 = get_company_sales_pref('date3');
$_POST['label_value102'] = $myrow102["label_value"];
$_POST['so_enable102'] = $myrow102["so_enable"];



start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Sales Order Cart Text Boxes"));
text_cells(_(""), 'label_value22', $_POST['label_value22'], 20);
echo'<tr>';
check_cells(_("Show Text Box 1"), ' so_enable1', $_POST['so_enable1']);
echo"<tr>";
text_cells(_(""), 'label_value', $_POST['label_value'], 40);
echo"</tr>";


echo"<tr>";
check_cells(_("Show Text Box 2"),'so_enable2', null);
echo"</tr>";
text_cells(_(""), 'label_value2', $_POST['label_value2'], 40);


echo"<tr>";
check_cells(_("Show Text Box 3"), 'so_enable3', null);
echo"</tr>";
text_cells(_(""), 'label_value3', $_POST['label_value3'], 40);
echo"<tr>";
check_cells(_("Show Text Box 4"), 'so_enable4', null);
echo"</tr>";
text_cells(_(""), 'label_value4', $_POST['label_value4'], 40);


echo"<tr>";
check_cells(_("Show Text Box 5"), 'so_enable5', null);
echo"</tr>";
text_cells(_(""), 'label_value5', $_POST['label_value5'], 40);

echo"<tr>";
check_cells(_("Show Text Box 6"), 'so_enable6', null);
echo"</tr>";
text_cells(_(""), 'label_value6', $_POST['label_value6'], 40);
table_section_title(_("Sales Order Footer Long Text"));
text_cells(_(""), 'label_value222', $_POST['label_value222'], 20);
echo"<tr>";
check_cells(_("Show Long Text Box 1"), 'so_enable7', null);
echo"</tr>";
text_cells(_(""), 'label_value7', $_POST['label_value7'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 2"), 'so_enable8', null);
echo"</tr>";
text_cells(_(""), 'label_value8', $_POST['label_value8'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 3"), 'so_enable16', null);
echo"</tr>";
text_cells(_(""), 'label_value16', $_POST['label_value16'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 4"), 'so_enable17', null);
echo"</tr>";
text_cells(_(""), 'label_value17', $_POST['label_value17'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 5"), 'so_enable18', null);
echo"</tr>";
text_cells(_(""), 'label_value18', $_POST['label_value18'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 6"), 'so_enable19', null);
echo"</tr>";
text_cells(_(""), 'label_value19', $_POST['label_value19'], 40);

table_section_title(_("Sales Order Header Text Boxes"));
text_cells(_(""), 'label_value444', $_POST['label_value444'], 20);
echo'<tr>';
check_cells(_("Show text box 1"), 'so_enable10', null);
echo'</tr>';
text_cells(_(""), 'label_value10', $_POST['label_value10'], 40);


echo'<tr>';
check_cells(_("Show text box 2"), 'so_enable11', null);
echo'</tr>';
text_cells(_(""), 'label_value11', $_POST['label_value11'], 40);

echo'<tr>';
check_cells(_("Show text box 3"), 'so_enable12', null);
echo'</tr>';
text_cells(_(""), 'label_value12', $_POST['label_value12'], 40);

///////////////////////////////////////////////////////////////////

table_section(4);
table_section_title(_("Sales Order Cart Amount Box"));
text_cells(_(""), 'label_value111', $_POST['label_value111'], 20);
echo"<tr>";
check_cells(_("Amount 1"), ' so_enable61');
echo"<tr>";
text_cells(_(""), 'label_value61', $_POST['label_value61'], 40);
echo"</tr>";


echo"<tr>";
check_cells(_("Amount 2"),'so_enable62', null);
echo"</tr>";
text_cells(_(""), 'label_value62', $_POST['label_value62'], 40);


echo"<tr>";
check_cells(_("Amount 3"), 'so_enable63', null);
echo"</tr>";
text_cells(_(""), 'label_value63', $_POST['label_value63'], 40);
echo"<tr>";
check_cells(_("Amount 4"), 'so_enable64', null);
echo"</tr>";
text_cells(_(""), 'label_value64', $_POST['label_value64'], 40);


echo"<tr>";
check_cells(_("Amount 5"), 'so_enable65', null);
echo"</tr>";
text_cells(_(""), 'label_value65', $_POST['label_value65'], 40);

echo"<tr>";
check_cells(_("Amount 6"), 'so_enable66', null);
echo"</tr>";
text_cells(_(""), 'label_value66', $_POST['label_value66'], 40);









table_section_title(_("Sales Order footer Text Boxes"));
text_cells(_(""), 'label_value333', $_POST['label_value333'], 20);

echo"<tr>";
check_cells(_("Show Text Box 7"), 'so_enable31', null);
echo"</tr>";
text_cells(_(""), 'label_value31', $_POST['label_value31'], 40);

echo"<tr>";
check_cells(_("Show Text Box 8"), 'so_enable32', null);
echo"</tr>";
text_cells(_(""), 'label_value32', $_POST['label_value32'], 40);

echo"<tr>";
check_cells(_("Show Text Box 9"), 'so_enable33', null);
echo"</tr>";
text_cells(_(""), 'label_value33', $_POST['label_value33'], 40);

echo"<tr>";
check_cells(_("Show Text Box 10"), 'so_enable54', null);
echo"</tr>";
text_cells(_(""), 'label_value54', $_POST['label_value54'], 40);

echo"<tr>";
check_cells(_("Show Text Box 11"), 'so_enable55', null);
echo"</tr>";
text_cells(_(""), 'label_value55', $_POST['label_value55'], 40);

echo"<tr>";
check_cells(_("Show Text Box 12"), 'so_enable56', null);
echo"</tr>";
text_cells(_(""), 'label_value56', $_POST['label_value56'], 40);

echo'<tr>';
table_section_title(_("Sales Order Header Long Text Boxes"));
text_cells(_(""), 'label_value555', $_POST['label_value555'], 20);

echo"<tr>";
check_cells(_("Show Long Text Box 4"), 'so_enable51', null);
echo"</tr>";
text_cells(_(""), 'label_value51', $_POST['label_value51'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 5"), 'so_enable52', null);
echo"</tr>";
text_cells(_(""), 'label_value52', $_POST['label_value52'], 40);

echo"<tr>";
check_cells(_("Show Long Text Box 6"), 'so_enable53', null);
echo"</tr>";
text_cells(_(""), 'label_value53', $_POST['label_value53'], 40);

table_section(4);
table_section_title(_("Sales Order Cart Combo Box"));
text_cells(_(""), 'label_value21', $_POST['label_value21'], 20);

//label_cell(_("Combo 1"));
echo'<tr>';
echo'<td>';
echo'<h4>Combo 1</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_1.php?OutstandingOnly=1", _("Combo 1 Setup"));
check_row(_(""), 'so_enable13', null);
text_cells(_(""), 'label_value13', $_POST['label_value13'], 40);
echo '</td>';
echo'</tr>';

echo'<tr>';
echo'<td>';
echo'<h4>Combo 2</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_2.php?OutstandingOnly=1", _("Combo 2 Setup"));
check_row(_(""), 'so_enable14', null);
text_cells(_(""), 'label_value14', $_POST['label_value14'], 40);
echo '</td>';
echo'</tr>';

echo'<td>';
echo'<h4>Combo 3</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_3.php?OutstandingOnly=1", _("Combo 3 Setup"));
check_row(_(""), 'so_enable15', null);
text_cells(_(""), 'label_value15', $_POST['label_value15'], 40);
echo '</td>';


echo'</tr>';
echo'<td>';
echo'<h4>Combo 4</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_4.php?OutstandingOnly=1", _("Combo 4 Setup"));
check_row(_(""), 'so_enable34', null);
text_cells(_(""), 'label_value34', $_POST['label_value34'], 40);
echo '</td>';


echo'</tr>';
echo'<td>';
echo'<h4>Combo 5</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_5.php?OutstandingOnly=1", _("Combo 5 Setup"));
check_row(_(""), 'so_enable35', null);
text_cells(_(""), 'label_value35', $_POST['label_value35'], 40);
echo '</td>';


echo'</tr>';
echo'<td>';
echo'<h4>Combo 6</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/combo_so_6.php?OutstandingOnly=1", _("Combo 6 Setup"));
check_row(_(""), 'so_enable36', null);
text_cells(_(""), 'label_value36', $_POST['label_value36'], 40);
echo '</td>';

table_section_title(_("Sales Order Cart Date Box"));
text_cells(_(""), 'label_value666', $_POST['label_value666'], 20);

echo"<tr>";
check_cells(_("Show Date Box 1"), 'so_enable100', null);
echo"</tr>";
text_cells(_(""), 'label_value100', $_POST['label_value100'], 40);

echo"<tr>";
check_cells(_("Show Date Box 2"), 'so_enable101', null);
echo"</tr>";
text_cells(_(""), 'label_value101', $_POST['label_value101'], 40);

echo"<tr>";
check_cells(_("Show Date Box 3"), 'so_enable102', null);
echo"</tr>";
text_cells(_(""), 'label_value102', $_POST['label_value102'], 40);




table_section(5);
table_section_title(_("Sales Order header Combo Box"));
text_cells(_(""), 'label_value777', $_POST['label_value777'], 20);

echo'</tr>';
echo'<td>';
echo'<h4>Header Combo 1</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/h_combo1.php?OutstandingOnly=1", _("Combo 1 Setup"));
check_row(_(""), 'so_enable91', null);
text_cells(_(""), 'label_value91', $_POST['label_value91'], 40);
echo '</td>';



echo'</tr>';
echo'<td>';
echo'<h4>Header Combo 2</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/h_combo2.php?OutstandingOnly=1", _("Combo 2 Setup"));
check_row(_(""), 'so_enable92', null);
text_cells(_(""), 'label_value92', $_POST['label_value92'], 40);
echo '</td>';

echo'</tr>';
echo'<td>';
echo'<h4>Header Combo 3</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/h_combo3.php?OutstandingOnly=1", _("Combo 3 Setup"));
check_row(_(""), 'so_enable93', null);
text_cells(_(""), 'label_value93', $_POST['label_value93'], 40);
echo '</td>';



table_section_title(_("Sales Order Footer Combo Box"));
text_cells(_(""), 'label_value888', $_POST['label_value888'], 20);

echo'</tr>';
echo'<td>';
echo'<h4>Footer Combo 1</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/f_combo1.php?OutstandingOnly=1", _("Combo 1 Setup"));
check_row(_(""), 'so_enable94', null);
text_cells(_(""), 'label_value94', $_POST['label_value94'], 40);
echo '</td>';



echo'</tr>';
echo'<td>';
echo'<h4>Footer Combo 2</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/f_combo2.php?OutstandingOnly=1", _("Combo 2 Setup"));
check_row(_(""), 'so_enable95', null);
text_cells(_(""), 'label_value95', $_POST['label_value95'], 40);
echo '</td>';

echo'</tr>';
echo'<td>';
echo'<h4>Footer Combo 3</h4>';
hyperlink_params_separate("$path_to_root/sales/manage/f_combo3.php?OutstandingOnly=1", _("Combo 3 Setup"));
check_row(_(""), 'so_enable96', null);
text_cells(_(""), 'label_value96', $_POST['label_value96'], 40);
echo '</td>';


end_outer_table(1);

//hidden('coy_logo', $_POST['coy_logo']);
submit_center('update', _("Update"), true, '',  'default');
div_end();
end_form(2);
//-------------------------------------------------------------------------------------------------

end_page();

