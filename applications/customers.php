<?php

class customers_app extends application
{
	function __construct()
	{
		if (get_company_pref_display('sales'))
		//$this->application("orders",_(get_company_pref_display('sales_text')));
        parent::__construct("orders",_(get_company_pref_display('sales_text')));
		$this->add_module(_("Transactions"));

		if (get_company_pref_display('sales_quotation'))
			$this->add_lapp_function(0,_(get_company_pref_display('sales_quotation_text')),
				"sales/sales_order_entry.php?NewQuotation=Yes",'SA_SALESQUOTE', MENU_TRANSACTION);

		if (get_company_pref_display('sale_order_entry'))
			$this->add_lapp_function(0,_(get_company_pref_display('sale_order_entry_text')),
				"sales/sales_order_entry.php?NewOrder=Yes",'SA_SALESORDER', MENU_TRANSACTION);


        if (get_company_pref_display('sales_order_entry_mobile'))
		$this->add_lapp_function(0, _(get_company_pref_display('sales_order_entry_mobile_text')),
            "sales/sales_order_entry_mobile.php?NewOrder=Yes", 'SA_SALESORDER', MENU_TRANSACTION);		
				
		$this->add_lapp_function(0, "","");

		if (get_company_pref_display('delivery_against'))
			$this->add_lapp_function(0,_(get_company_pref_display('delivery_against_text')),
				"sales/inquiry/sales_orders_view.php?OutstandingOnly=1",'SA_SALESDELIVERY', MENU_TRANSACTION);

		if (get_company_pref_display('invoice_against'))
			$this->add_lapp_function(0,_(get_company_pref_display('invoice_against_text')),
				"sales/inquiry/sales_deliveries_view.php?OutstandingOnly=1",'SA_SALESINVOICE', MENU_TRANSACTION);


        if (get_company_pref_display('direct_delivery'))
		$this->add_lapp_function(0, _(get_company_pref_display('direct_delivery_text')),
			"sales/sales_order_entry.php?NewDelivery=0",'SA_SALESDELIVERY', MENU_TRANSACTION);

        if (get_company_pref_display('pos'))
		$this->add_lapp_function(0,_(get_company_pref_display('pos_text')),
            "sales/pos_order_entry.php?NewInvoice=0",'SA_SALESORDER', MENU_TRANSACTION);


		if (get_company_pref_display('direct_invoice'))
			$this->add_lapp_function(0,_(get_company_pref_display('direct_invoice_text')),
				"sales/sales_order_entry.php?NewInvoice=0",'SA_SALESINVOICE', MENU_TRANSACTION);

		//$this->add_rapp_function(0, _("&Template Delivery"),
		//"sales/inquiry/sales_orders_view.php?DeliveryTemplates=Yes", 'SA_SALESDELIVERY', MENU_TRANSACTION);
		//$this->add_rapp_function(0, _("&Template Invoice"),
		//"sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes", 'SA_SALESINVOICE', MENU_TRANSACTION);
		//$this->add_rapp_function(0, _("&Create and Print Recurrent Invoices"),
		//"sales/create_recurrent_invoices.php?", 'SA_SALESINVOICE', MENU_TRANSACTION);
         if (get_company_pref_display('invoice_prepaid_order'))
		$this->add_lapp_function(0,_(get_company_pref_display('invoice_prepaid_order_text')),
		"sales/inquiry/sales_orders_view.php?PrepaidOrders=Yes", 'SA_SALESINVOICE', MENU_TRANSACTION);



		$this->add_rapp_function(0, "","");
		if (get_company_pref_display('customer_payment'))
			$this->add_rapp_function(0,_(get_company_pref_display('customer_payment_text')),
				"sales/customer_payments.php?",'SA_SALESPAYMNT', MENU_TRANSACTION);
		//$this->add_lapp_function(0,_("Invoice &Prepaid Orders"),
		//"sales/inquiry/sales_orders_view.php?PrepaidOrders=Yes", 'SA_SALESINVOICE', MENU_TRANSACTION);
		
		global $leftmenu_save, $db_connections;
		if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'PETPAL') {
		    //  if (get_company_pref_display('customer_credit_note'))
			$this->add_rapp_function(0,('Sales Return'),
				"sales/credit_note_entry.php?NewCredit=Yes",'SA_SALESCREDIT', MENU_TRANSACTION);
                }
                
                else{
	if (get_company_pref_display('customer_credit_note'))
			$this->add_rapp_function(0,_(get_company_pref_display('customer_credit_note_text')),
				"sales/credit_note_entry.php?NewCredit=Yes",'SA_SALESCREDIT', MENU_TRANSACTION);
                }

		if (get_company_pref_display('allocate'))
			$this->add_rapp_function(0,_(get_company_pref_display('allocate_text')),
				"sales/allocations/customer_allocation_main.php?",'SA_SALESALLOC', MENU_TRANSACTION);


        if (get_company_pref('discount_offer'))
			$this->add_lapp_function(0,_("Add Offers"),
				"sales/offer_order_entry.php?AddOffers=Yes",'SA_SALESINVOICE', MENU_TRANSACTION);


		$this->add_module(_("Inquiries and Reports"));
		if (get_company_pref_display('sales_quotation_inquiry'))
			$this->add_lapp_function(1,_(get_company_pref_display('sales_quotation_inquiry_text')),
				"sales/inquiry/sales_orders_view.php?type=32",'SA_SALESTRANSVIEW', MENU_INQUIRY);

		if (get_company_pref_display('sale_order_inquiry'))
			$this->add_lapp_function(1,_(get_company_pref_display('sale_order_inquiry_text')),
				"sales/inquiry/sales_orders_view.php?type=30",'SA_SALESTRANSVIEW', MENU_INQUIRY);

		if (get_company_pref_display('customer_inquiry'))
			$this->add_lapp_function(1,_(get_company_pref_display('customer_inquiry_text')),
				"sales/inquiry/customer_inquiry.php?",'SA_SALESTRANSVIEW', MENU_INQUIRY);

		if (get_company_pref_display('customer_allocate_inquiry'))
			$this->add_lapp_function(1,_(get_company_pref_display('customer_allocate_inquiry_text')),
				"sales/inquiry/customer_allocation_inquiry.php?",'SA_SALESALLOC', MENU_INQUIRY);


		if (get_company_pref_display('sales_reports'))
			$this->add_rapp_function(1,_(get_company_pref_display('sales_reports_text')),
				"reporting/reports_main.php?Class=0",'SA_SALESTRANSVIEW', MENU_REPORT);
    if (get_company_pref('discount_offer'))
            $this->add_lapp_function(1,_("Offer Details Inquiry"),
                "sales/inquiry/offer_inquiry.php?",'SA_SALESTRANSVIEW', MENU_INQUIRY);


		$this->add_module(_("Maintenance"));
		if (get_company_pref_display('manage_customer'))
			$this->add_lapp_function(2,_(get_company_pref_display('manage_customer_text')),
				"sales/manage/customers_inquiry.php?",'SA_CUSTOMER', MENU_ENTRY);

		if (get_company_pref_display('customer_branches'))
			$this->add_lapp_function(2, _(get_company_pref_display('customer_branches_text')),
				"sales/manage/customer_branches.php?",'SA_CUSTOMER', MENU_ENTRY);

        if (get_company_pref_display('merge_customers'))
		$this->add_lapp_function(2, _(get_company_pref_display('merge_customers_text')),
			"sales/merge_customers.php?",'SA_CUSTOMER', MENU_MAINTENANCE);

		if (get_company_pref_display('sales_group'))
			$this->add_lapp_function(2,_(get_company_pref_display('sales_group_text')),
				"sales/manage/sales_groups.php?",'SA_SALESGROUP', MENU_MAINTENANCE);
		//$this->add_lapp_function(2, _("Recurrent &Invoices"),
		//	"sales/manage/recurrent_invoices.php?",'SA_SRECURRENT', MENU_MAINTENANCE);


		if (get_company_pref_display('sales_type'))
			$this->add_rapp_function(2,_(get_company_pref_display('sales_type_text')),
				"sales/manage/sales_types.php?",'SA_SALESTYPES', MENU_MAINTENANCE);


		if (get_company_pref_display('sales_persons'))
			$this->add_rapp_function(2,_(get_company_pref_display('sales_persons_text')),
				"sales/manage/sales_people.php?",'SA_SALESMAN', MENU_MAINTENANCE);


		if (get_company_pref_display('sales_areas'))
			$this->add_rapp_function(2, _(get_company_pref_display('sales_areas_text')),
				"sales/manage/sales_areas.php?",'SA_SALESAREA', MENU_MAINTENANCE);


        if (get_company_pref_display('sales_email'))
		$this->add_rapp_function(2, _(get_company_pref_display('sales_email_text')),
			"sales/manage/sales_email.php?",'SA_SALESAREA', MENU_MAINTENANCE);

        if (get_company_pref_display('import_customer'))
	$this->add_lapp_function(2,  _(get_company_pref_display('import_customer_text')),
			"modules/import_items/import_customers.php?",'SA_CUSTOMER', MENU_MAINTENANCE);

	if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CB' || $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CB2')
                $this->add_rapp_function(2, _("Upload Customer Invoices"),
			'modules/import_cust_OB/import_cust_invoice.php', 'SA_CUSTOMER', MENU_MAINTENANCE);



        if (get_company_pref_display('import_bulk_invoice'))
		$this->add_rapp_function(2, _(get_company_pref_display('import_bulk_invoice_text')),
			'modules/import_cust_OB/import_cust_opening.php', 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('import_bulk_credit'))
	$this->add_rapp_function(2, _(get_company_pref_display('import_bulk_credit_text')),
			'modules/import_cust_sales_return/import_sales_return.php', 'SA_CUSTOMER', MENU_MAINTENANCE);


        if (get_company_pref_display('gate_pass'))
            $this->add_lapp_function(2, _(get_company_pref_display('gate_pass_text')),
            "sales/manage/gate_pass_dashboard.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);


            
// 		if (get_company_pref_display('wht_types'))
// 			$this->add_rapp_function(2,_(get_company_pref_display('wht_types_text')),
// 				"sales/manage/wht_type.php?",'SA_SALESAREA', MENU_MAINTENANCE);
        if (get_company_pref_display('credit_status_setup'))
		$this->add_rapp_function(2, _(get_company_pref_display('credit_status_setup_text')),
		"sales/manage/credit_status.php?",'SA_CRSTATUS', MENU_MAINTENANCE);
		global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DMNWS' || 
        $db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'DEMO')
        {
            
            if (get_company_pref_display('sales_sms'))
            	$this->add_lapp_function(2, _(get_company_pref_display('sales_sms_text')),
        			"sales/manage/sales_sms.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);

            if (get_company_pref_display('sms_template'))
        		$this->add_lapp_function(2, _(get_company_pref_display('sms_template_text')),
        			"sales/manage/sms_template.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);
        			
        }
        if (get_company_pref('discount_offer')) {
            $this->add_lapp_function(2, _("&Sales Offers"),
                "sales/manage/sales_offers.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);
            $this->add_lapp_function(2, _("&Distribution Network"),
                "sales/manage/dist_network.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);
            $this->add_lapp_function(2,  _("Customers Info"),
                "sales/manage/cust_info.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);
            $this->add_lapp_function(2,  _("Transportation"),
                "sales/manage/transportation.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);
        }
       


		$this->add_extensions();
	}
}


