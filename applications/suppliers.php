<?php

class suppliers_app extends application 
{
	function __construct()
	{
	    
	    
	    
	    global $db_connections ;

		if (get_company_pref_display('purchase'))
//			$this->application("AP", _(get_company_pref_display('purchase_text')));
            parent::__construct("AP", _(get_company_pref_display('purchase_text')));

		$this->add_module(_("Transactions"));

        if (get_company_pref_display('requisition_entries'))
		$this->add_lapp_function(0, _(get_company_pref_display('requisition_entries_text')), $path_to_root.'/modules/requisitions/requisitions.php',
					'SA_REQUISITIONS',	MENU_TRANSACTION);



        if (get_company_pref_display('requisition_allocation'))
		$this->add_lapp_function(0, _(get_company_pref_display('requisition_allocation_text')), $path_to_root.'/modules/requisitions/requisition_allocations.php',
					 'SA_REQUISITION_ALLOCATIONS', MENU_TRANSACTION);


		if (get_company_pref_display('purchase_order'))
		$this->add_lapp_function(0, _(get_company_pref_display('purchase_order_text')),
			"purchasing/po_entry_items.php?NewOrder=Yes",'SA_PURCHASEORDER', MENU_TRANSACTION);


		if (get_company_pref_display('opom'))
		$this->add_lapp_function(0, _(get_company_pref_display('opom_text')),
			"purchasing/inquiry/po_search.php?",'SA_GRN', MENU_TRANSACTION);

    




if (get_company_pref_display('direct_grn'))
		  $this->add_lapp_function(0, _(get_company_pref_display('direct_grn_text')),
			"purchasing/po_entry_items.php?NewGRN=Yes", 'SA_DIRECT_GRN', MENU_TRANSACTION);

		if (get_company_pref_display('supplier_invoice'))
            $this->add_lapp_function(0,  _(get_company_pref_display('supplier_invoice_text')),
                "purchasing/supplier_invoice.php?New=1", 'SA_SUPPLIERINVOICE', MENU_TRANSACTION);

        if (get_company_pref_display('import_purchase'))
        $this->add_lapp_function(0, _(get_company_pref_display('import_purchase_text')),
            "purchasing/inquiry/grn_search_completed.php?", 'SA_SUPPTRANSVIEW', MENU_TRANSACTION);

        if (get_company_pref_display('grn_search'))
        $this->add_lapp_function(0, _(get_company_pref_display('grn_search_text')),
            "purchasing/inquiry/grn_search.php?", 'SA_SUPPTRANSVIEW', MENU_TRANSACTION);

       /* if (get_company_pref_display('import_purchase_price'))
		$this->add_lapp_function(0, _(get_company_pref_display('import_purchase_price_text')),
			"purchasing/supplier_invoice_price_import_reg.php?New=1", 'SA_SUPPLIERINVOICE', MENU_TRANSACTION);
*/


       if (get_company_pref_display('direct_supplier_invoice'))
		$this->add_lapp_function(0, _(get_company_pref_display('direct_supplier_invoice_text')),
			"purchasing/po_entry_items.php?NewInvoice=Yes",'SA_SUPPLIERINVOICE', MENU_TRANSACTION);
       // $this->add_lapp_function(0, _(get_company_pref_display('direct_supplier_invoice_text')),
		//	"purchasing/po_entry_items.php?NewInvoice=Yes",'SA_SUPPLIERINVOICE', MENU_TRANSACTION);

		if (get_company_pref_display('payments_suppliers'))
		$this->add_rapp_function(0, _(get_company_pref_display('payments_suppliers_text')),
			"purchasing/supplier_payment.php?",'SA_SUPPLIERPAYMNT', MENU_TRANSACTION);
		$this->add_rapp_function(0, "","");

	/*	if (get_company_pref_display('supplier_invoice'))
		$this->add_rapp_function(0, _(get_company_pref_display('supplier_invoice_text')),
			"purchasing/supplier_invoice.php?New=1",'SA_SUPPLIERINVOICE', MENU_TRANSACTION);*/

		if (get_company_pref_display('supplier_credit'))
		$this->add_rapp_function(0, _(get_company_pref_display('supplier_credit_text')),
			"purchasing/supplier_credit.php?New=1",'SA_SUPPLIERCREDIT', MENU_TRANSACTION);

		if (get_company_pref_display('allocates'))
		$this->add_rapp_function(0, _(get_company_pref_display('allocates_text')),
			"purchasing/allocations/supplier_allocation_main.php?",'SA_SUPPLIERALLOC', MENU_TRANSACTION);

		$this->add_module(_("Inquiries and Reports"));

// 		if (get_company_pref_display('purchase_order_inquiry'))
// 		$this->add_lapp_function(1, _(get_company_pref_display('purchase_order_inquiry_text')),
// 			"purchasing/inquiry/po_search_completed.php?",'SA_SUPPTRANSVIEW', MENU_INQUIRY);
			


		
			    
			    	if (get_company_pref_display('purchase_order_inquiry'))
		$this->add_lapp_function(1, _(get_company_pref_display('purchase_order_inquiry_text')),
			"purchasing/inquiry/po_search_completed.php?",'SA_SUPPTRANSVIEW', MENU_INQUIRY);
		

			
			
			
//$this->add_lapp_function(1, "OutStanding Import GRNs ",
			//"purchasing/inquiry/grn_search_completed.php?",'SA_SUPPTRANSVIEW', MENU_INQUIRY);

			
			
			
			
			
			
			

        if (get_company_pref_display('purchase_requisitions_inquiry'))
		$this->add_lapp_function(1,_(get_company_pref_display('purchase_requisitions_inquiry_text')),
			"purchasing/inquiry/pr_search_completed.php?", 'SA_SUPPTRANSVIEW', MENU_INQUIRY);

		if (get_company_pref_display('supplier_transaction_inquiry'))
		$this->add_lapp_function(1, _(get_company_pref_display('supplier_transaction_inquiry_text')),
			"purchasing/inquiry/supplier_inquiry.php?",'SA_SUPPTRANSVIEW', MENU_INQUIRY);










		if (get_company_pref_display('allocate_inquiry'))
		$this->add_lapp_function(1, _(get_company_pref_display('allocate_inquiry_text')),
			"purchasing/inquiry/supplier_allocation_inquiry.php?",'SA_SUPPLIERALLOC', MENU_INQUIRY);

		if (get_company_pref_display('supplier_reports'))
		$this->add_rapp_function(1, _(get_company_pref_display('supplier_reports_text')),
			"reporting/reports_main.php?Class=1",'SA_SUPPTRANSVIEW', MENU_REPORT);

		$this->add_module(_("Maintenance"));

      
       // $this->add_lapp_function(2, _('Combo 1'),
		//	"purchasing/manage/combo_1.php?",'SA_SUPPLIER', MENU_ENTRY);

	//	$this->add_lapp_function(2, _('Combo 2'),
		//	"purchasing/manage/combo_2.php?",'SA_SUPPLIER', MENU_ENTRY);

	//	$this->add_lapp_function(2, _('Combo 3'),
			//"purchasing/manage/combo_3.php?",'SA_SUPPLIER', MENU_ENTRY);


	if (get_company_pref_display('manage_supplier'))
			$this->add_lapp_function(2,_(get_company_pref_display('manage_supplier_text')),
				"purchasing/manage/suppliers_inquiry.php?",'SA_SUPPLIER', MENU_ENTRY);

  $this->add_lapp_function(2, _('Supplier Category'),
			"purchasing/manage/supplier_category.php?",'SA_SUPPLIER', MENU_ENTRY);


        if (get_company_pref_display('import_supplier'))
            $this->add_lapp_function(2, _(get_company_pref_display('import_supplier_text')),
				"modules/import_items/import_suppliers.php?",'SA_SUPPLIER', MENU_ENTRY);


        // if (get_company_pref_display('import_sup'))
            $this->add_lapp_function(2, _(get_company_pref_display('import_sup_text')),
				"modules/import_supp_OB/import_supp_OB.php?",'SA_SUPPLIER', MENU_ENTRY);

        if (get_company_pref_display('purchase_return'))
            $this->add_lapp_function(2,  _(get_company_pref_display('purchase_return_text')),
				"modules/import_purchase_return/import_purchase_return.php?",'SA_SUPPLIER', MENU_ENTRY);


        if (get_company_pref_display('purchase_email'))
				$this->add_lapp_function(2, _(get_company_pref_display('purchase_email_text')),
			"purchasing/manage/purchase_email.php?",'SA_SUPPLIER', MENU_ENTRY);

		$this->add_extensions();
	}
}


