<?php

class setup_app extends application
{
	function __construct()
	{
	if (get_company_pref_display('setup'))
			//$this->application("system", _(get_company_pref_display('setup_text')));
        parent::__construct("system",_(get_company_pref_display('setup_text')));

		$this->add_module(_("Company Setup"));


				if (get_company_pref_display('company_setup'))
			$this->add_lapp_function(0, _(get_company_pref_display('company_setup_text')),
				"admin/company_preferences.php?", 'SA_SETUPCOMPANY', MENU_SETTINGS);


// 		$this->add_lapp_function(0, _("Form - Display"),
// 			"admin/company_preferences_new.php?", 'SA_SETUPCOMPANY', MENU_SETTINGS);



        if (get_company_pref_display('user_account_setup'))
			$this->add_lapp_function(0, _(get_company_pref_display('user_account_setup_text ')),
				"admin/users.php?", 'SA_USERS', MENU_SETTINGS);


		if (get_company_pref_display('user_account_setup')==1)
		{
			$this->add_lapp_function(0, _(get_company_pref_display('user_account_setup_text')),
				"admin/users.php?", 'SA_USERS', MENU_SETTINGS);
		}


		if (get_company_pref_display('access_setup'))
			$this->add_lapp_function(0, _(get_company_pref_display('access_setup_text')),
				"admin/security_roles.php?", 'SA_SECROLES', MENU_SETTINGS);

		if (get_company_pref_display('user_location'))
			$this->add_lapp_function(0, _(get_company_pref_display('user_location_text')),
				"admin/user_locations.php?", 'SA_SECROLES', MENU_SETTINGS);
				
        if (get_company_pref_display('user_dimension'))
			$this->add_lapp_function(0, _(get_company_pref_display('user_dimension_text')),
				"admin/user_dimension.php?", 'SA_SECROLES', MENU_SETTINGS);
				
	    if (get_company_pref_display('user_accounts'))
			$this->add_lapp_function(0, _(get_company_pref_display('user_accounts_text')),
				"admin/user_accounts.php?", 'SA_SECROLES', MENU_SETTINGS);
				
        if (get_company_pref_display('user_bank_access'))
		$this->add_lapp_function(0,  _(get_company_pref_display('user_bank_access_text')),
 			"admin/user_banks.php?", 'SA_SECROLES', MENU_SETTINGS);
		

		if (get_company_pref_display('display_setup'))
			$this->add_lapp_function(0, _(get_company_pref_display('display_setup_text')),
				"admin/display_prefs.php?", 'SA_SETUPDISPLAY', MENU_SETTINGS);

		if (get_company_pref_display('transaction_ref'))
			$this->add_lapp_function(0, _(get_company_pref_display('transaction_ref_text')),
				"admin/forms_setup.php?", 'SA_FORMSETUP', MENU_SETTINGS);

		if (get_company_pref_display('taxes'))
			$this->add_rapp_function(0, _(get_company_pref_display('taxes_text')),
				"taxes/tax_types.php?", 'SA_TAXRATES', MENU_MAINTENANCE);

		if (get_company_pref_display('tax_group'))
			$this->add_rapp_function(0, _(get_company_pref_display('tax_group_text')),
				"taxes/tax_groups.php?", 'SA_TAXGROUPS', MENU_MAINTENANCE);

		if (get_company_pref_display('item_tax_type'))
			$this->add_rapp_function(0, _(get_company_pref_display('item_tax_type_text')),
				"taxes/item_tax_types.php?", 'SA_ITEMTAXTYPE', MENU_MAINTENANCE);

//		if (get_company_pref_display('system_gl'))
			$this->add_rapp_function(0, _(get_company_pref_display('system_gl_text')),
				"admin/gl_setup.php?", 'SA_GLSETUP', MENU_SETTINGS);

		if (get_company_pref_display('fiscal_years'))
			$this->add_rapp_function(0, _(get_company_pref_display('fiscal_years_text')),
				"admin/fiscalyears.php?", 'SA_FISCALYEARS', MENU_MAINTENANCE);


		if (get_company_pref_display('print_profile'))
			$this->add_rapp_function(0, _(get_company_pref_display('print_profile_text')),
				"admin/print_profiles.php?", 'SA_PRINTPROFILE', MENU_MAINTENANCE);



		if (get_company_pref_display('report_form'))
		$this->add_rapp_function(0,  _(get_company_pref_display('report_form_text')),
			"admin/print_from_setup.php?", 'SA_PRINTPROFILE', MENU_MAINTENANCE);



		$this->add_module(_("Miscellaneous"));
		if (get_company_pref_display('payment_terms'))
			$this->add_lapp_function(1, _(get_company_pref_display('payment_terms_text')),
				"admin/payment_terms.php?", 'SA_PAYTERMS', MENU_MAINTENANCE);

		if (get_company_pref_display('shipping_company'))
			$this->add_lapp_function(1, _(get_company_pref_display('shipping_company_text')),
				"admin/shipping_companies.php?", 'SA_SHIPPING', MENU_MAINTENANCE);

		if (get_company_pref_display('point_sale'))
			$this->add_rapp_function(1, _(get_company_pref_display('point_sale_text')),
				"sales/manage/sales_points.php?", 'SA_POSSETUP', MENU_MAINTENANCE);

        if (get_company_pref_display('printers'))
		$this->add_rapp_function(1,  _(get_company_pref_display('printers_text')),
		"admin/printers.php?", 'SA_PRINTERS', MENU_MAINTENANCE);

        if (get_company_pref_display('contact_categories'))
            $this->add_rapp_function(1,  _(get_company_pref_display('contact_categories_text')),
		"admin/crm_categories.php?", 'SA_CRMCATEGORY', MENU_MAINTENANCE);


$this->add_rapp_function(1, _("Reports Preference"),
            "sales/manage/reports_preference.php?",'SA_SALESAREA', MENU_MAINTENANCE);


		$this->add_module(_("Maintenance"));

		if (get_company_pref_display('void_transaction'))
			$this->add_lapp_function(2, _(get_company_pref_display('void_transaction_text')),
				"admin/void_transaction.php?", 'SA_VOIDTRANSACTION', MENU_MAINTENANCE);

		if (get_company_pref_display('print_transaction'))
			$this->add_lapp_function(2, _(get_company_pref_display('print_transaction_text')),
				"admin/view_print_transaction.php?", 'SA_VIEWPRINTTRANSACTION', MENU_MAINTENANCE);

        if (get_company_pref_display('attach_document'))
		$this->add_lapp_function(2,  _(get_company_pref_display('attach_document_text')),
			"admin/attachments.php?filterType=20", 'SA_ATTACHDOCUMENT', MENU_MAINTENANCE);

        if (get_company_pref_display('system_diagnostics'))
            $this->add_lapp_function(2, _(get_company_pref_display('system_diagnostics_text')),
		"admin/system_diagnostics.php?", 'SA_SOFTWAREUPGRADE', MENU_SYSTEM);


        if (get_company_pref_display('backup'))
			$this->add_rapp_function(2, _(get_company_pref_display('backup_text')),
                "admin/backups.php?", 'SA_BACKUP', MENU_SYSTEM);

        if (get_company_pref_display('update_companies'))
			$this->add_rapp_function(2,  _(get_company_pref_display('update_companies_text')),
		"admin/create_coy.php?", 'SA_CREATECOMPANY', MENU_UPDATE);

        if (get_company_pref_display('update_language'))
            $this->add_rapp_function(2, _(get_company_pref_display('update_language_text')),
		"admin/inst_lang.php?", 'SA_CREATELANGUAGE', MENU_UPDATE);

        if (get_company_pref_display('activate_extensions'))
            $this->add_rapp_function(2,  _(get_company_pref_display('activate_extensions_text')),
		"admin/inst_module.php?", 'SA_CREATEMODULES', MENU_UPDATE);

        if (get_company_pref_display('install_activate_themes'))
            $this->add_rapp_function(2, _(get_company_pref_display('install_activate_themes_text')),
		"admin/inst_theme.php?", 'SA_CREATEMODULES', MENU_UPDATE);

        if (get_company_pref_display('activate_account'))
            $this->add_rapp_function(2, _(get_company_pref_display('activate_account_text')),
		"admin/inst_chart.php?", 'SA_CREATEMODULES', MENU_UPDATE);

        if (get_company_pref_display('software_upgrade'))
			$this->add_rapp_function(2,_(get_company_pref_display('software_upgrade_text')),
		"admin/inst_upgrade.php?", 'SA_SOFTWAREUPGRADE', MENU_UPDATE);

		$this->add_extensions();
	}
}


