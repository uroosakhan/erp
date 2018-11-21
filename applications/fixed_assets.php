<?php

class assets_app extends application
{
	function __construct()
	{
		//$this->application("assets", _($this->help_context = "&Fixed Assets"));
			if (get_company_pref_display('assets'))
        parent::__construct("assets", _(get_company_pref_display('assets_text')));
		$this->add_module(_("Transactions"));
        if (get_company_pref_display('fixed_purchase'))
            $this->add_lapp_function(0, _(get_company_pref_display('fixed_purchase_text')),
			"purchasing/po_entry_items.php?NewInvoice=Yes&FixedAsset=1", 'SA_SUPPLIERINVOICE', MENU_TRANSACTION);


        if (get_company_pref_display('fixed_location_transfer'))
		$this->add_lapp_function(0,  _(get_company_pref_display('fixed_location_transfer_text')),
			"inventory/transfers.php?NewTransfer=1&FixedAsset=1", 'SA_ASSETTRANSFER', MENU_TRANSACTION);

        if (get_company_pref_display('fixed_disposal'))
		$this->add_lapp_function(0,  _(get_company_pref_display('fixed_disposal_text')),
			"inventory/adjustments.php?NewAdjustment=1&FixedAsset=1", 'SA_ASSETDISPOSAL', MENU_TRANSACTION);


        if (get_company_pref_display('asset_sale'))
		$this->add_lapp_function(0, _(get_company_pref_display('asset_sale_text')),
			"sales/sales_order_entry.php?NewInvoice=0&FixedAsset=1", 'SA_SALES_FIXEDASSETS', MENU_TRANSACTION);


        if (get_company_pref_display('process_depreciation'))
            $this->add_rapp_function(0, _(get_company_pref_display('process_depreciation_text')),
			"fixed_assets/process_depreciation.php", 'SA_DEPRECIATION', MENU_MAINTENANCE);
    // TODO: needs work
		//$this->add_rapp_function(0, _("Fixed Assets &Revaluation"),
	//		"inventory/cost_update.php?FixedAsset=1", 'SA_STANDARDCOST', MENU_MAINTENANCE);

		$this->add_module(_("Inquiries and Reports"));
        if (get_company_pref_display('asset_movments'))
		$this->add_lapp_function(1, _(get_company_pref_display('asset_movments_text')),
			"inventory/inquiry/stock_movements.php?FixedAsset=1", 'SA_ASSETSTRANSVIEW', MENU_INQUIRY);

        if (get_company_pref_display('asset_inquiry'))
		$this->add_lapp_function(1, _(get_company_pref_display('asset_inquiry_text')),
			"fixed_assets/inquiry/stock_inquiry.php?", 'SA_ASSETSANALYTIC', MENU_INQUIRY);


        if (get_company_pref_display('assets_reports'))
		$this->add_rapp_function(1,_(get_company_pref_display('assets_reports_text')),
			"reporting/reports_main.php?Class=7", 'SA_ASSETSANALYTIC', MENU_REPORT);

		$this->add_module(_("Maintenance"));

        if (get_company_pref_display('fixed_assets'))
            $this->add_lapp_function(2, _(get_company_pref_display('fixed_assets_text')),
			"inventory/manage/items.php?FixedAsset=1", 'SA_ASSET', MENU_ENTRY);


        if (get_company_pref_display('fixed_assets_locations'))
            $this->add_rapp_function(2,  _(get_company_pref_display('fixed_assets_locations_text')),
			"inventory/manage/locations.php?FixedAsset=1", 'SA_INVENTORYLOCATION', MENU_MAINTENANCE);


        if (get_company_pref_display('fixed_assets_categories'))
            $this->add_rapp_function(2,  _(get_company_pref_display('fixed_assets_categories_text')),
			"inventory/manage/item_categories.php?FixedAsset=1", 'SA_ASSETCATEGORY', MENU_MAINTENANCE);

        if (get_company_pref_display('fixed_assets_classes'))
		$this->add_rapp_function(2, _(get_company_pref_display('fixed_assets_classes_text')),
			"fixed_assets/fixed_asset_classes.php", 'SA_ASSETCLASS', MENU_MAINTENANCE);

		$this->add_extensions();
	}
}


?>
