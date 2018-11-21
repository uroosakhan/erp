<?php
$page_security = 'SA_PAYROLL_GL_SETUP';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Payroll GL Setup"));

include($path_to_root . "/payroll/includes/db/gl_setup_db.inc");

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);


if (isset($_POST['submit']))
{

	$input_error = 0;

	if ($input_error != 1)
	{
    	if ($account != -1) 
    	{
		update_eoboi($_POST['eobi']);
		update_services_($_POST['services']);
		update_sessi($_POST['sessi']);
		update_total_working_days($_POST['total_working_days']);
		update_filer($_POST['filer']);
		update_non_filer($_POST['non_filer']);
            update_mon($_POST['mday']);
            update_tue($_POST['tday']);
            update_wed($_POST['wday']);
            update_thur($_POST['thday']);
            update_fri($_POST['fday']);
            update_sat($_POST['sday']);
            update_sun($_POST['suday']);

			$note = _('Selected gl accounts has been updated');
    	} 
    	else 
    	{
    		//add_emp_dept($_POST['description']);
			//$note = _('New sales group has been added');
    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}
//-------------------------------------------------------------------------------------------------
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

	</style>
</head>

<body>



<center>
	<td><a class="hvr-float-shadow" href="attendance_policy.php"><i class="fa fa-dashboard " style="margin-right: 5px; font-size: large;">  </i> ATTENDANCE POLICY</a></td>

	<td><a class="hvr-float-shadow" href="emp_grade.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> OVERTIME POLICY</a></td>

	<td><a class="hvr-float-shadow" href="division_wise_gl_setup.php"><i class="fa fa-line-chart" style="margin-right: 5px; font-size: large;"></i> GL SETUP NEW POLICY</a></td>



</center>


</body>
</html>


<?php
start_form();
  
//-------------------------------------------------------------------------------------------------

start_outer_table(TABLESTYLE2);
start_outer_table(TABLESTYLE2);
table_section(1);
table_section_title(_("Name and Address"));

text_row(_("EOBI"),'eobi',get_sys_pay_pref('eobi'));
text_row(_("SESSI"),'sessi',get_sys_pay_pref('sessi'));
text_row(_("SERVICE"),'services',get_sys_pay_pref('services')."%");
table_section(2);
table_section_title(_("Name and Address"));
text_row(_("Total Working Days"),'total_working_days',get_sys_pay_pref('total_working_days'));

text_row(_("Filer"),'filer',get_sys_pay_pref('filer'));
text_row(_("Non Filer"),'non_filer',get_sys_pay_pref('non_filer'));

table_section(3);
table_section_title(_("WeekDays"));

check_mon_row(_("Monday"), 'mday', true);
check_tue_row(_("Tuesday"), 'tday', null);
check_wed_row(_("Wednesday"), 'wday', null);
check_thu_row(_("Thursday"), 'thday', null);
check_fri_row(_("Friday"), 'fday', null);
check_sat_row(_("Saturday"), 'sday', null);
check_sun_row(_("Sunday"), 'suday', null);

end_outer_table(1);

submit_center('submit', _("Update"), true, '', 'default');
end_form();

end_page();
?>
