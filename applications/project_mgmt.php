<?php

class project_app extends application
{
	function __construct()
	{
		//$this->application("pmgmt", _($this->help_context = "Project Management"));
		 if (get_company_pref_display('crm'))
        parent::__construct("pmgmt", _(get_company_pref_display('crm_text')));
		$this->add_module(_("Transactions"));

        if (get_company_pref_display('add_task'))
		$this->add_lapp_function(0, _(get_company_pref_display('add_task_text')),
			"project/task.php?type=task", 'SA_CUSTOMER', MENU_TRANSACTION);

//		$this->add_lapp_function(0, _("&Add Knowledge Base"),
//			"project/knowledge_base.php?type=task", 'SA_CUSTOMER', MENU_TRANSACTION);

        if (get_company_pref_display('add_kba'))
		$this->add_lapp_function(0, _(get_company_pref_display('add_kba_text')),
			"project/demo.php?type=task", 'SA_CUSTOMER', MENU_TRANSACTION);

        if (get_company_pref_display('add_call'))
            $this->add_lapp_function(0,  _(get_company_pref_display('add_call_text')),
			"project/task.php?type=call", 'SA_CUSTOMER', MENU_TRANSACTION);


        if (get_company_pref_display('add_event'))
            $this->add_lapp_function(0,  _(get_company_pref_display('add_event_text')),
			"project/task.php?type=event", 'SA_CUSTOMER', MENU_TRANSACTION);

        if (get_company_pref_display('add_query'))
		$this->add_lapp_function(0,  _(get_company_pref_display('add_query_text')),
			"project/query.php?", 'SA_CUSTOMER', MENU_TRANSACTION);
		

		$this->add_lapp_function(0, "","");
		
		
		$this->add_module(_("Reports"));
		//$this->add_lapp_function(1, _("Tasks Inquiry"),
		//	"project/inquiry/task_inquiry.php?unset_all=1", 'SA_CUSTOMER', MENU_INQUIRY);
        if (get_company_pref_display('task_inquiry'))
		$this->add_lapp_function(1,_(get_company_pref_display('task_inquiry_text')),
			"project/inquiry/task_inquiry.php?status=112", 'SA_CUSTOMER', MENU_INQUIRY);

        if (get_company_pref_display('task_grid'))
            $this->add_lapp_function(1, _(get_company_pref_display('task_grid_text')),
            "project/inquiry/task_grid.php?status=112", 'SA_CUSTOMER', MENU_INQUIRY);

        if (get_company_pref_display('knowledge_base'))
            $this->add_lapp_function(1, _(get_company_pref_display('knowledge_base_text')),
			"project/inquiry/kb_inquiry.php?", 'SA_CUSTOMER', MENU_INQUIRY);

        if (get_company_pref_display('query_inquiry'))
            $this->add_lapp_function(1, _(get_company_pref_display('query_inquiry_text')),
			"project/inquiry/query_inquiry.php?status=112", 'SA_CUSTOMER', MENU_INQUIRY);


        if (get_company_pref_display('call_log'))
            $this->add_lapp_function(1, _(get_company_pref_display('call_log_text')),
			"project/inquiry/call_log.php?", 'SA_CUSTOMER', MENU_INQUIRY);

		//$this->add_lapp_function(1, _("Task Inquiry Dashboard"),
		//	"project/inquiry/task_dashboard.php?", 'SA_CUSTOMER', MENU_INQUIRY);
        if (get_company_pref_display('calender'))
		$this->add_lapp_function(1, _(get_company_pref_display('calender_text')),
			"project/inquiry/calender.php?", 'SA_CUSTOMER', MENU_INQUIRY);

        if (get_company_pref_display('print_reports'))
            $this->add_rapp_function(1, _(get_company_pref_display('print_reports_text')),
			"reporting/reports_main.php?Class=0", 'SA_CUSTOMER', MENU_REPORT);

		$this->add_module(_("Setup"));
        if (get_company_pref_display('add_customer'))
            $this->add_lapp_function(2, _(get_company_pref_display('add_customer_text')),
			"sales/manage/customers.php?", 'SA_CUSTOMER', MENU_ENTRY);

        if (get_company_pref_display('knowledge_base_cat'))
            $this->add_lapp_function(2, _(get_company_pref_display('knowledge_base_cat_text')),
			"project/manage/category.php?", 'SA_CUSTOMER', MENU_ENTRY);


        if (get_company_pref_display('task_status'))
            $this->add_rapp_function(2, _(get_company_pref_display('task_status_text')),
			"project/manage/status.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('duration'))
            $this->add_rapp_function(2, _(get_company_pref_display('duration_text')),
			"project/manage/duration.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

		//$this->add_rapp_function(2, _("Add Task Type"),
		//	"project/manage/task_type.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('call_type'))
            $this->add_rapp_function(2, _(get_company_pref_display('call_type_text')),
			"project/manage/call_type.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('query_status'))
            $this->add_rapp_function(2, _(get_company_pref_display('query_status_text')),
			"project/manage/query_status.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('query_source'))
            $this->add_rapp_function(2, _(get_company_pref_display('query_source_text')),
			"project/manage/source_status.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

        if (get_company_pref_display('settings'))
            $this->add_rapp_function(2,  _(get_company_pref_display('settings_text')),
			"project/manage/setting.php?", 'SA_CUSTOMER', MENU_MAINTENANCE);

		$this->add_extensions();
	}
}


?>