<?php

$page_security = 'SA_SETUPDISPLAY';
$path_to_root="..";
include($path_to_root . "/includes/session.inc");

//page(_($help_context = "Display Setup"));
//page(_($help_context = "Display Setup"),false,false,user_color());


page(_($help_context = "Display Setup"),false,false);
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/admin/db/company_db.inc");

//-------------------------------------------------------------------------------------------------

if (isset($_POST['setprefs'])) 
{
	if (!is_numeric($_POST['query_size']) || ($_POST['query_size']<1))
	{
		display_error($_POST['query_size']);
		display_error( _("Query size must be integer and greater than zero."));
		set_focus('query_size');
	} else {
		$_POST['theme'] = clean_file_name($_POST['theme']);
		$chg_theme = user_theme() != $_POST['theme'];
		$chg_lang = $_SESSION['language']->code != $_POST['language'];
		$chg_date_format = user_date_format() != $_POST['date_format'];
		$chg_date_sep = user_date_sep() != $_POST['date_sep'];

		set_user_prefs(get_post( 
			array('prices_dec', 'qty_dec', 'rates_dec', 'percent_dec',
			'date_format', 'date_sep', 'tho_sep', 'dec_sep', 'print_profile', 
			'theme', 'page_size', 'language', 'startup_tab',
			'show_gl' => 0, 'show_codes'=> 0, 'show_hints' => 0,
			'rep_popup' => 0, 'graphic_links' => 0, 'sticky_doc_date' => 0,
			'query_size' => 10.0, 'transaction_days' => 30, 'save_report_selections' => 0,
			'use_date_picker' => 0, 'def_print_destination' => 0, 'def_print_orientation' => 0,'prices_dec_','color')));

		if ($chg_lang)
			$_SESSION['language']->set_language($_POST['language']);
			// refresh main menu

		flush_dir(company_path().'/js_cache');	

		if ($chg_theme && $SysPrefs->allow_demo_mode)
			$_SESSION["wa_current_user"]->prefs->theme = $_POST['theme'];
		if ($chg_theme || $chg_lang || $chg_date_format || $chg_date_sep)
			meta_forward($_SERVER['PHP_SELF']);

		
		if ($SysPrefs->allow_demo_mode)  
			display_warning(_("Display settings have been updated. Keep in mind that changed settings are restored on every login in demo mode."));
		else
			display_notification_centered(_("Display settings have been updated."));
	}
}

start_form();

start_outer_table(TABLESTYLE2);

table_section(1);
table_section_title(_("Decimal Places"));

number_list_row(_("Amounts:"), 'prices_dec', user_price_dec(), 0, 10);
number_list_row(_("Price:"), 'prices_dec_', user_prices_dec_only(), 0, 10);
number_list_row(_("Quantities:"), 'qty_dec', user_qty_dec(), 0, 10);
number_list_row(_("Exchange Rates:"), 'rates_dec', user_exrate_dec(), 0, 10);
number_list_row(_("Percentages:"), 'percent_dec', user_percent_dec(), 0, 10);

table_section_title(_("Date Format and Separators"));

dateformats_list_row(_("Date Format:"), "date_format", user_date_format());

dateseps_list_row(_("Date Separator:"), "date_sep", user_date_sep());

/* The array $dateseps is set up in config.php for modifications
possible separators can be added by modifying the array definition by editing that file */

thoseps_list_row(_("Thousand Separator:"), "tho_sep", user_tho_sep());

/* The array $thoseps is set up in config.php for modifications
possible separators can be added by modifying the array definition by editing that file */

decseps_list_row(_("Decimal Separator:"), "dec_sep", user_dec_sep());

/* The array $decseps is set up in config.php for modifications
possible separators can be added by modifying the array definition by editing that file */

check_row(_("Use Date Picker"), 'use_date_picker', user_use_date_picker());

if (!isset($_POST['language']))
	$_POST['language'] = $_SESSION['language']->code;

table_section_title(_("Reports"));

text_row_ex(_("Save Report Selection Days:"), 'save_report_selections', 5, 5, '', user_save_report_selections());

yesno_list_row(_("Default Report Destination:"), 'def_print_destination', user_def_print_destination(), 
	$name_yes=_("Excel"), $name_no=_("PDF/Printer"));

yesno_list_row(_("Default Report Orientation:"), 'def_print_orientation', user_def_print_orientation(), 
	$name_yes=_("Landscape"), $name_no=_("Portrait"));

table_section(2);

table_section_title(_("Miscellaneous"));

check_row(_("Show hints for new users:"), 'show_hints', user_hints());

check_row(_("Show GL Information:"), 'show_gl', user_show_gl_info());

check_row(_("Show Item Codes:"), 'show_codes', user_show_codes());

themes_list_row(_("Theme:"), "theme", user_theme());

/* The array $themes is set up in config.php for modifications
possible separators can be added by modifying the array definition by editing that file */

pagesizes_list_row(_("Page Size:"), "page_size", user_pagesize());

tab_list_row(_("Start-up Tab"), 'startup_tab', user_startup_tab());

/* The array $pagesizes is set up in config.php for modifications
possible separators can be added by modifying the array definition by editing that file */

if (!isset($_POST['print_profile']))
	$_POST['print_profile'] = user_print_profile();

print_profiles_list_row(_("Printing profile"). ':', 'print_profile', 
	null, _('Browser printing support'));

check_row(_("Use popup window to display reports:"), 'rep_popup', user_rep_popup(),
	false, _('Set this option to on if your browser directly supports pdf files'));

check_row(_("Use icons instead of text links:"), 'graphic_links', user_graphic_links(),
	false, _('Set this option to on for using icons instead of text links'));

check_row(_("Remember last document date:"), 'sticky_doc_date', sticky_doc_date(),
	false, _('If set document date is remembered on subsequent documents, otherwise default is current date'));

text_row_ex(_("Query page size:"), 'query_size',  5, 5, '', user_query_size());

text_row_ex(_("Transaction days:"), 'transaction_days', 5, 5, '', user_transaction_days());

table_section_title(_("Language"));

languages_list_row(_("Language:"), 'language', $_POST['language']);


$color=user_color();

//label_row($color);
//text_row_ex(_("Selected Theme::"), 'SelectedTheme:', 20, 15, '', $color);

color_list_cells(_("Theme Color"), 'color', user_color(),$name_yes="", $name_no="",$name_blue_lite="",$name_blue="",$name_black="",$name_purple="",
    $name_green="",$name_red="",$name_yellow="",$blacklight="",$yellowlight="",$greenlight="",
    $submit_on_change=false,true);


//
//if($_POST['color'] == 'skin-red')
//{
//
//    echo "<td>";
//
//    echo "<img src='red.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//} elseif($_POST['color'] == 'skin-blue') {
//
//    echo "<td>";
//
//    echo "<img src='blue.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-black')
//{
//
//    echo "<td>";
//
//    echo "<img src='black.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-purple')
//{
//
//    echo "<td>";
//
//    echo "<img src='purple.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-green')
//{
//
//    echo "<td>";
//
//    echo "<img src='green.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-yellow')
//{
//
//    echo "<td>";
//
//    echo "<img src='yellow.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-blue-light')
//{
//
//    echo "<td>";
//
//    echo "<img src='blue_lite.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//elseif($_POST['color']=='skin-purple-light')
//{
//
//    echo "<td>";
//
//    echo "<img src='purple_lite.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//
//elseif($_POST['color']=='skin-green-light')
//{
//
//    echo "<td>";
//
//    echo "<img src='green_lite.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//
//elseif($_POST['color']=='skin-red-light')
//{
//
//    echo "<td>";
//
//    echo "<img src='red_lite.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//
//elseif($_POST['color']=='skin-yellow-light')
//{
//
//    echo "<td>";
//
//    echo "<img src='yellow_lite.JPG' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}
//else
//{
//
//    echo "<td>";
//
//    echo "<img src='' alt='No Image Found' height='100px' width='100px' style='background-color: #00a7d0;'>";
//
//    echo "</td>";
//
//}


end_outer_table(1);

submit_center('setprefs', _("Update"), true, '',  'default');

end_form(2);

//-------------------------------------------------------------------------------------------------

end_page();
