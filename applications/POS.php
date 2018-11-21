<?php

class POS_app extends application
{
	function __construct()
	{
		parent::__construct("pos",_("POS"));
		$this->add_module(_("Transactions"));
		//$this->add_lapp_function(0, _("&Quotations"),
		//	"sales/sales_order_entry.php?NewQuotation=Yes", 'SA_SALESQUOTE', MENU_TRANSACTION);

// 		$this->add_lapp_function(0, _("Tables "),
// 			"POS/manage/pre_orders_tables.php?NewOrder=Yes", 'SA_SALESORDER', MENU_TRANSACTION);


    
    $this->add_lapp_function(0, _("POS"),
            "POS/sales_modify_entry.php?NewOrder=Yes", 'SA_SALESORDER', MENU_TRANSACTION);
    

		//$this->add_lapp_function(0, _("Sales &Orders (S.O)"),
		//	"sales/sales_order_entry.php?NewOrder=Yes", 'SA_SALESORDER', MENU_TRANSACTION);
		//$this->add_lapp_function(0, _("&Dispatch Note),
		//"sales/sales_order_entry.php?NewDelivery=0", 'SA_SALESDELIVERY', MENU_TRANSACTION);

//$this->add_lapp_function(0, _("&Delivery Note (D.N)"),
		//	"sales/inquiry/sales_orders_view.php?OutstandingOnly=1", 'SA_SALESDELIVERY', MENU_TRANSACTION);

		$this->add_lapp_function(0, _("&Credit Invoice"),
			"POS/inquiry/sales_deliveries_view.php?OutstandingOnly=1", 'SA_SALESINVOICE', MENU_TRANSACTION);

		$this->add_lapp_function(0, "","");



		//$this->add_rapp_function(0, _("&Template Delivery"),
		//"sales/inquiry/sales_orders_view.php?DeliveryTemplates=Yes", 'SA_SALESDELIVERY', MENU_TRANSACTION);
		//$this->add_rapp_function(0, _("&Template Invoice"),
		//"sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes", 'SA_SALESINVOICE', MENU_TRANSACTION);
		//$this->add_rapp_function(0, _("&Create and Print Recurrent Invoices"),
		//"sales/create_recurrent_invoices.php?", 'SA_SALESINVOICE', MENU_TRANSACTION);
		$this->add_lapp_function(0, _("Cash &Invoice"),
			"POS/sales_order_entry.php?NewInvoice=0", 'SA_SALESINVOICE', MENU_TRANSACTION);

		$this->add_rapp_function(0, "","");
		$this->add_lapp_function(0, _("Receive &Payments"),
			"POS/customer_payments.php?", 'SA_SALESPAYMNT', MENU_TRANSACTION);
		$this->add_rapp_function(0, _("&Sales Return"),
			"POS/credit_note_entry.php?NewCredit=Yes", 'SA_SALESCREDIT', MENU_TRANSACTION);
		//$this->add_rapp_function(0, _("&Allocate Customer Payments or Credit Notes"),
		//	"sales/allocations/customer_allocation_main.php?", 'SA_SALESALLOC', MENU_TRANSACTION);

		$this->add_module(_("Reports"));
		//$this->add_lapp_function(1, _("Quotation &Dashboard"),
		//"sales/inquiry/sales_orders_view.php?type=32", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
		$this->add_lapp_function(1, _("Sales &Order Dashboard"),
			"POS/inquiry/sales_orders_view.php?type=30", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
		$this->add_lapp_function(1, _("Customer &Transaction Dashboard"),
			"POS/inquiry/customer_inquiry.php?", 'SA_SALESTRANSVIEW', MENU_INQUIRY);
		$this->add_lapp_function(1, "","");
		//$this->add_lapp_function(1, _("Customer Allocation Dashboard"),
		//"sales/inquiry/customer_allocation_inquiry.php?", 'SA_SALESALLOC', MENU_INQUIRY);

		$this->add_rapp_function(1, _("Print &Reports"),
			"reporting/reports_main.php?Class=0", 'SA_SALESTRANSVIEW', MENU_REPORT);

		$this->add_module(_("Setup"));
		$this->add_lapp_function(2, _("Add &Tables"),
			"POS/manage/customers.php?", 'SA_CUSTOMER', MENU_ENTRY);
		//$this->add_lapp_function(2, _("Add &Branches"),
		//"sales/manage/customer_branches.php?", 'SA_CUSTOMER', MENU_ENTRY);
		//$this->add_lapp_function(2, _("Sales &Groups"),
		//"sales/manage/sales_groups.php?", 'SA_SALESGROUP', MENU_MAINTENANCE);
		//$this->add_lapp_function(2, _("Recurrent &Invoices"),
		//	"sales/manage/recurrent_invoices.php?", 'SA_SRECURRENT', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("Sales T&ypes"),
			"POS/manage/sales_types.php?", 'SA_SALESTYPES', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("Sales &Persons"),
			"POS/manage/sales_people.php?", 'SA_SALESMAN', MENU_MAINTENANCE);
		$this->add_rapp_function(2, _("Discount"),
			"POS/manage/discount.php?", 'SA_SALESTYPES', MENU_MAINTENANCE);
		//$this->add_rapp_function(2, _("Sales &Areas"),
		//"sales/manage/sales_areas.php?", 'SA_SALESAREA', MENU_MAINTENANCE);
		//$this->add_rapp_function(2, _("Credit &Status Setup"),
		//	"sales/manage/credit_status.php?", 'SA_CRSTATUS', MENU_MAINTENANCE);

		$this->add_extensions();
	}
}


