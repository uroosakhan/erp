<?php
define ('SS_IMPORTTRANSACTIONS', 101<<8);

class hooks_import_transactions extends hooks {
	var $module_name = 'import_transations'; 

	/*
		Install additonal menu options provided by module
	*/
	function install_options($app) {
		global $path_to_root;

		switch($app->id) {
			case 'GL':
			 //   if (get_company_pref_display('import_transaction'))
				// $app->add_rapp_function(2,  _(get_company_pref_display('import_transaction_text')), 
				// 	$path_to_root.'/modules/import_transactions/import_transactions.php', 'SA_CSVTRANSACTIONS');
					
				 		    if (get_company_pref_display('import_transaction'))
				$app->add_rapp_function(2, _(get_company_pref_display('import_transaction_text')), 
				 	$path_to_root.'/modules/import_transactions/import_transactions.php', 'SA_CSVTRANSACTIONS');
		}
	}

	function install_access()
	{
		$security_sections[SS_IMPORTTRANSACTIONS] =	_("Import Transactions");

		$security_areas['SA_CSVTRANSACTIONS'] = array(SS_IMPORTTRANSACTIONS|101, _("Import Transactions"));

		return array($security_areas, $security_sections);
	}
}
?>
