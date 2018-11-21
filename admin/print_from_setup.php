<?php

$page_security = 'SA_PRINTPROFILE';
$path_to_root = "..";
include($path_to_root . "/includes/session.inc");
include($path_to_root . "/admin/db/print_form_db.inc");
include($path_to_root . "/includes/ui.inc");

page(_($help_context = "Printing Profiles"));

$selected_id = get_post('profile_id','');

//-------------------------------------------------------------------------------------------------
// Returns array of defined reports
//
function get_reports() {
	global $path_to_root, $SysPrefs;

	if ($SysPrefs->go_debug || !isset($_SESSION['reports'])) {	
	// to save time, store in session.
		$paths = array (
			$path_to_root.'/reporting/',
			company_path(). '/reporting/');
		$reports = array( '' => _('Default printing destination'));

		foreach($paths as $dirno => $path) {
			$repdir = opendir($path);
			while(false !== ($fname = readdir($repdir)))
			{
				// reports have filenames in form rep(repid).php 
				// where repid must contain at least one digit (reports_main.php is not ;)
				if (is_file($path.$fname) 
					&& preg_match('/rep(.*[0-9]+.*)[.]php/', $fname, $match))
				{
					$repno = $match[1];
					$title = '';

					$line = file_get_contents($path.$fname);
					if (preg_match('/.*(FrontReport\()\s*_\([\'"]([^\'"]*)/', $line, $match)) {
						$title = trim($match[2]);
					}
					else // for any 3rd party printouts without FrontReport() class use
					if (preg_match('/.*(\$Title).*[\'"](.*)[\'"].+/', $line, $match)) {
						$title = trim($match[2]);
					}
					$reports[$repno] = $title;
				}
			}
			closedir();
		}
		ksort($reports);
		$_SESSION['reports'] = $reports;
	}
	return $_SESSION['reports'];
}

function clear_form() 
{
	global $selected_id, $Ajax;

	$selected_id = '';
	$_POST['name'] = '';
	$Ajax->activate('_page_body');
}

function check_delete($name)
{
	// check if selected profile is used by any user
	if ($name=='') return 0; // cannot delete system default profile
	return key_in_foreign_table($name, 'users', 'print_profile');
}
//-------------------------------------------------------------------------------------------
if ( get_post('submit'))
{

	$error = 0;

	if ($_POST['profile_id'] == '' && empty($_POST['name']))
	{
		$error = 1;
		display_error( _("Security profile name cannot be empty."));
		set_focus('name');
	} 

	if (!$error)
	{
		$prof = array('' => get_post('Prn')); // store default value/profile name
		foreach (get_reports() as $rep => $descr) {
			$val = get_post('Prn'.$rep);
			$prof[$rep] = $val;
		}
		if ($_POST['profile_id']=='')
			$_POST['profile_id'] = get_post('name');
		
		update_printer_profile($_POST['profile_id'], $prof);
		if ($selected_id == '') {
			display_notification_centered(_('New Security profile has been created'));
			clear_form($selected_id);
		} else {
			display_notification_centered(_('Security profile has been updated'));
		}
	}
}

if(get_post('delete'))
{
 	if (!check_delete(get_post('name'))) {
		delete_printer_profile($selected_id);
		display_notification(_('Selected Security profile has been deleted'));
		clear_form();
 	}
}

if(get_post('_profile_id_update')) {
	$Ajax->activate('_page_body');
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
        <td><a class="hvr-float-shadow" href="print_from_setup.php"><i class="fa fa-pie-chart" style="font-size: large; margin-right: 5px;"></i> REPORT DISPLAY</a></td>

        <td><a class="hvr-float-shadow" href="import_gl_setup.php"><i class="fa fa-ship" style="font-size: large; margin-right: 5px;"></i> IMPORT GL</a></td>
        <td><a class="hvr-float-shadow" href="cashflow_gl.php"><i class="fa fa-area-chart" style="margin-right: 5px; font-size: large;"></i> CASH FLOW</a></td>
        <td><a class="hvr-float-shadow" href="wht_type.php"><i class="fa fa-text-width" style="margin-right: 5px; font-size: large;"></i> WHT GL</a></td>
	</center>



	</body>
	</html>


<?php

start_form();
start_table();
report_profiles_list_row(_('Select Security profile'). ':', 'profile_id', null,
	_('New Security profile'), true);
end_table();
echo '<hr>';
start_table();
if (get_post('profile_id') == '')
	text_row(_("Security Profile Name").':', 'name', null, 30, 30);
else
	label_cells(_("Security Profile Name").':', get_post('profile_id'));
end_table(1);

$result = get_print_profile(get_post('profile_id'));
$prints = array();
while ($myrow = db_fetch($result)) {
	$prints[$myrow['report']] = $myrow['printer'];
}

start_table(TABLESTYLE);
$th = array(_("Report Id"), _("Description"), _("Security Check"));
table_header($th);

$k = 0;
$unkn = 0;
foreach(get_reports() as $rep => $descr)
{
	alt_table_row_color($k);

    label_cell($rep=='' ? '-' : $rep, 'align=center');
    label_cell($descr == '' ? '???<sup>1)</sup>' : _($descr));
	$_POST['Prn'.$rep] = isset($prints[$rep]) ? $prints[$rep] : '';
    echo '<td>';
	//echo printers_list('Prn'.$rep, null,
	//	$rep == '' ? _('Browser support') : _('Default'));
	check_cells(_(""), 'Prn'.$rep, null);
	echo '</td>';
	if ($descr == '') $unkn = 1;
    end_row();
}
end_table();
if ($unkn)
	display_note('<sup>1)</sup>&nbsp;-&nbsp;'._("no title was found in this report definition file."), 0, 1, '');
else
	echo '<br>';

div_start('controls');
if (get_post('profile_id') == '') {
	submit_center('submit', _("Add New Profile"), true, '', 'default');
} else {
	submit_center_first('submit', _("Update Profile"), 
	  _('Update printer profile'), 'default');
	submit_center_last('delete', _("Delete Profile"), 
	  _('Delete printer profile (only if not used by any user)'), true);
}
div_end();

end_form();
end_page();

