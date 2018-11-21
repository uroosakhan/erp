<?php

if (!isset($path_to_root) || isset($_GET['path_to_root']) || isset($_POST['path_to_root']))
	die("Restricted access");
	include_once($path_to_root . '/applications/application.php');
	include_once($path_to_root . '/applications/customers.php');
	include_once($path_to_root . '/applications/suppliers.php');
	include_once($path_to_root . '/applications/inventory.php');
	include_once($path_to_root . '/applications/fixed_assets.php');
	include_once($path_to_root . '/applications/manufacturing.php');
	include_once($path_to_root . '/applications/dimensions.php');
	include_once($path_to_root . '/applications/generalledger.php');
    include_once($path_to_root . '/applications/reporting.php');
	include_once($path_to_root . '/applications/setup.php');
	include_once($path_to_root . '/installed_extensions.php');
	include_once($path_to_root . '/applications/dashboard.php');
    include_once($path_to_root . '/applications/complete_invoice.php');
    include_once($path_to_root . '/applications/payroll_setup.php');
    include_once($path_to_root . '/applications/payroll.php');
    include_once($path_to_root . '/applications/setup.php');
    include_once($path_to_root . '/applications/project_mgmt.php');
    include_once($path_to_root . '/applications/POS.php');

	class front_accounting
	{
		var $user;
		var $settings;
		var $applications;
		var $selected_application;

		var $menu;

//		function front_accounting()
//		{
//		} new build
		function add_application(&$app)
		{	
			if ($app->enabled) // skip inactive modules
				$this->applications[$app->id] = &$app;
		}
		function get_application($id)
		{
			 if (isset($this->applications[$id]))
				return $this->applications[$id];
			 return null;
		}
		function get_selected_application()
		{
			if (isset($this->selected_application))
				 return $this->applications[$this->selected_application];
			foreach ($this->applications as $application)
				return $application;
			return null;
		}
		function display()
		{
			global $path_to_root;
			
			include_once($path_to_root . "/themes/".user_theme()."/renderer.php");

			$this->init();
			$rend = new renderer();
			$rend->wa_header();

			$rend->display_applications($this);

			$rend->wa_footer();
			$this->renderer =& $rend;
		}
		function init()
		{
			global $SysPrefs;
            global  $db_connections;
			$this->menu = new menu(_("Main  Menu"));
			$this->menu->add_item(_("Main  Menu"), "index.php");
			$this->menu->add_item(_("Logout"), "/account/access/logout.php");
			$this->applications = array();
			$this->add_application(new dashboard_app());			
			$this->add_application(new customers_app());
            if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IC' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='ARKISH' 
            || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RMS'|| $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CW'
            || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CB' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='IYSH' 
            || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='CB2' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BALOCH1')
            $this->add_application(new POS_app());
			if (get_company_pref('use_crm'))            
			$this->add_application(new project_app()); //CRM
			$this->add_application(new suppliers_app());
			$this->add_application(new inventory_app());
			if (get_company_pref('use_manufacturing'))
				$this->add_application(new manufacturing_app());
			if (get_company_pref('use_fixed_assets'))
			    $this->add_application(new assets_app());
	    	$this->add_application(new dimensions_app());
			$this->add_application(new general_ledger_app());
			$this->add_application(new reporting_app());
			
	        if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='RPLT' || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='EUROTAX' 
	        || $db_connections[$_SESSION["wa_current_user"]->company]["name"]=='BNTTAX')
		    	$this->add_application(new complete_invoice_app());
			if (get_company_pref('use_hr'))
            $this->add_application(new payroll_setup_app());
			if (get_company_pref('use_payroll'))            
            $this->add_application(new payroll());
           
			hook_invoke_all('install_tabs', $this);

			$this->add_application(new setup_app());
		}
	}
