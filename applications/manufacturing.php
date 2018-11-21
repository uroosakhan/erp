<?php

class manufacturing_app extends application
{
	function __construct()
	{
	    



		if (get_company_pref_display('manufacture'))
		//$this->application("manuf", _(get_company_pref_display('manufacture_text')));
            parent::__construct("manuf",_(get_company_pref_display('manufacture_text')));

	$this->add_module(_("Transactions"));
	
	        $this->add_lapp_function(0, _("Work Order Requisition"),
            "manufacturing/work_order_entry_cart_req.php?NewWorkOrder=1", 'SA_WORKORDERENTRYREQ', MENU_TRANSACTION);
        $this->add_lapp_function(0, _("Pending work order requisitions"),
            "manufacturing/search_work_orders_req.php?outstanding_only=1", 'SA_WORKORDERENTRYREQ', MENU_TRANSACTION);

        if (get_company_pref_display('work_order_entry'))
			$this->add_lapp_function(0, _(get_company_pref_display("work_order_entry_text")),
			"manufacturing/work_order_entry.php?WOType=0", 'SA_WORKORDERENTRY', MENU_TRANSACTION);

        if (get_company_pref_display('work_order_entry_unassemble_text'))
            $this->add_lapp_function(0, _(get_company_pref_display("work_order_entry_unassemble_text")),
                "manufacturing/work_order_entry.php?WOType=1", 'SA_WORKORDERENTRY', MENU_TRANSACTION);
//
        if (get_company_pref_display('work_order_entry_advance_manufacture_text'))
            $this->add_lapp_function(0, _(get_company_pref_display("work_order_entry_advance_manufacture_text")),
                "manufacturing/work_order_entry.php?WOType=2", 'SA_WORKORDERENTRY', MENU_TRANSACTION);







		if (get_company_pref_display('work_order_entry'))
			$this->add_lapp_function(0, _(get_company_pref_display('work_order_entry_text')." Multiple"),
				"manufacturing/work_order_entry_cart.php?NewWorkOrder=1", 'SA_WORKORDERENTRY', MENU_TRANSACTION);
		if (get_company_pref_display('outstanding_work_order'))
			$this->add_lapp_function(0, _(get_company_pref_display("outstanding_work_order_text")),
			"manufacturing/search_work_orders.php?outstanding_only=1", 'SA_MANUFTRANSVIEW', MENU_TRANSACTION);

		$this->add_module(_("Inquiries and Reports"));

		if (get_company_pref_display('costed_bill_material'))
			$this->add_lapp_function(1, _(get_company_pref_display("costed_bill_material_text")),
			"manufacturing/inquiry/bom_cost_inquiry.php?", 'SA_WORKORDERCOST', MENU_INQUIRY);




		


		if (get_company_pref_display('inventory_item_where_used'))
			$this->add_lapp_function(1, _(get_company_pref_display("inventory_item_where_used_text")),
			"manufacturing/inquiry/where_used_inquiry.php?", 'SA_WORKORDERANALYTIC', MENU_INQUIRY);

		if (get_company_pref_display('work_order_inquiry'))
			$this->add_lapp_function(1, _(get_company_pref_display("work_order_inquiry_text")),
			"manufacturing/search_work_orders.php?", 'SA_MANUFTRANSVIEW', MENU_INQUIRY);


		if (get_company_pref_display('manufacturing_reports'))
			$this->add_rapp_function(1, _(get_company_pref_display("manufacturing_reports_text")),
			"reporting/reports_main.php?Class=3", 'SA_MANUFTRANSVIEW', MENU_REPORT);

		$this->add_module(_("Maintenance"));
	$this->add_lapp_function(2, _("Add and Manage BOM "),
			"manufacturing/inquiry/bom_inquiry.php?", 'SA_WORKORDERCOST', MENU_INQUIRY);
			
		if (get_company_pref_display('bill_of_material'))
			$this->add_lapp_function(2, _(get_company_pref_display("bill_of_material_text")),
			"manufacturing/manage/bom_edit.php?", 'SA_BOM', MENU_ENTRY);

		if (get_company_pref_display('work_center'))
			$this->add_lapp_function(2, _(get_company_pref_display("work_center_text")),
			"manufacturing/manage/work_centres.php?", 'SA_WORKORDERENTRY', MENU_MAINTENANCE);

		$this->add_extensions();
	}
}


