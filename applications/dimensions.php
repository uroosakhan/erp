<?php

class dimensions_app extends application
{
	function __construct()
	{
		$dim = get_company_pref('use_dimension');
		if (get_company_pref_display('dim'))
			//$this->application("proj", _(get_company_pref_display('dim_text')), $dim);
            parent::__construct("proj", _(get_company_pref_display('dim_text')), $dim);
		if ($dim > 0)
		{
			$this->add_module(_("Transactions"));
			
			if (get_company_pref_display('dimension_entry'))
				$this->add_lapp_function(0, _(get_company_pref_display("dimension_entry_text")),
				"dimensions/dimension_entry.php?", 'SA_DIMENSION', MENU_ENTRY);

			if (get_company_pref_display('outstanding_dimension'))
				$this->add_lapp_function(0, _(get_company_pref_display("outstanding_dimension_text")),
				"dimensions/inquiry/search_dimensions.php?outstanding_only=1", 'SA_DIMTRANSVIEW', MENU_TRANSACTION);


				$this->add_module(_("Inquiries and Reports"));
			if (get_company_pref_display('dimension_inquiry'))
				$this->add_lapp_function(1, _(get_company_pref_display("dimension_inquiry_text")),
				"dimensions/inquiry/search_dimensions.php?", 'SA_DIMTRANSVIEW', MENU_INQUIRY);


			if (get_company_pref_display('dimension_reports'))
				$this->add_rapp_function(1, _(get_company_pref_display("dimension_reports_text")),
				"reporting/reports_main.php?Class=4", 'SA_DIMENSIONREP', MENU_REPORT);
			
			$this->add_module(_("Maintenance"));
			if (get_company_pref_display('dimension_tags'))
			$this->add_lapp_function(2, _(get_company_pref_display("dimension_tags_text")),
				"admin/tags.php?type=dimension", 'SA_DIMTAGS', MENU_MAINTENANCE);

			$this->add_extensions();
		}
	}
}

