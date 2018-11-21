<?php
define ('SS_PAYROLL', 77<<8);

class payroll_app extends application 
{
	function payroll_app() 
	{
		$this->application("payroll", _($this->help_context = "Payroll"));
	
		$this->add_module(_("Transactions"));
		$this->add_lapp_function(0, _("Import"), '/modules/payroll/import.php', 'SS_PAYROLL');

		$this->add_module(_("Reports"));



		$this->add_module(_("Setup"));

		$this->add_extensions();
	}
}

class hooks_payroll extends hooks {
	var $module_name = 'Payroll'; 

	/*
		Install additonal menu options provided by module
	*/

	function install_tabs($app)
	{
        $app->add_application(new payroll_app());
	}

/*	function install_options($app) {
		global $path_to_root;

		switch($app->id) {
			case 'GL':
				$app->add_rapp_function(2, _('Import 2'), 
					$path_to_root.'/modules/payroll/import.php', 'SS_PAYROLL');
		}
	}
*/
	function install_access()
	{
		$security_sections[SS_PAYROLL] =	_("Payroll");

		$security_areas['SS_PAYROLL'] = array(SS_PAYROLL|77, _("Payroll"));

		return array($security_areas, $security_sections);
	}
}
?>