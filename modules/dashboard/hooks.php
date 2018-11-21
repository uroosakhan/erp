<?php

//define ('SS_DASHBOARD', 1<<8);

include_once($path_to_root . '/applications/dashboard.php');

class hooks_dashboard extends hooks {
	var $module_name = 'Dashboard'; 

	/*
		Install new menu tab
	*/
	function install_tabs($app)
	{
        $app->add_application(new dashboard_app());
	}
	
	/*
		Install new menu options provided by module
	*/
//	function install_options($app) {
//		global $path_to_root;
//
//		switch($app->id) {
//			case 'dashboard':
//				$app->add_lapp_function(1, _('My Dashboard'), 
//					$path_to_root.'/modules/dashboard/dashboard.php', 'SS_DASHBOARD');
//		}
//	}

	function install_access()
	{
		$security_sections[SS_DASHBOARD] =	_("Dashboard");

		$security_areas['SS_DASHBOARD'] = array(SS_DASHBOARD|1, _("Dashboard"));

		return array($security_areas, $security_sections);
	}
}
?>