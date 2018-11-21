<?php

include_once($path_to_root . "/admin/db/company_db.inc");

/*
	System and per company prefferences.
	Object is created only with site wide preffernces.
	After user login refresh method have to be called to re-initialize company specific settings.
*/
class sys_prefs 
{
	var $prefs;
	var $db_ok; // check whether database has been upgraded after src update.

	function __construct()
	{
		global $path_to_root;

		// retrieve set system wide preferences
		include $path_to_root.'/config.default.php';
		if (file_exists($path_to_root.'/config.php'))
			include $path_to_root.'/config.php';

		foreach(get_defined_vars() as $name => $value)
			$this->$name = $value;

		if (!@$this->time_zone)
			$this->time_zone = ini_get('date.timezone');

		if (!$this->time_zone)
			$this->time_zone = 'Europe/Berlin';
			
		if (!isset($this->use_popup_search))
			$this->use_popup_search = false;

   	    ini_set('date.timezone', $this->time_zone);
	}

	/*
		Re-initialize company preferrencies.
	*/
	function refresh_new()
	{
		global $db_version;

		$sql = "SELECT name, value FROM ".TB_PREF."sys_prefs_new";
		$result = @db_query($sql); // supress errors before 2.3 db structure upgrade

		if(!$result)
			return null;

		while($pref1 = db_fetch_assoc($result)) {
			$this->prefs1[$pref1['name']] = $pref1['value'];
		}

		// update current db status for info in log file
		//$this->db_ok = $this->prefs1['version_id'] == $db_version;
	}

	function refresh_display()
	{
		global $db_version;

		$sql = "SELECT name, value FROM ".TB_PREF."sys_prefs_new";
		$result = @db_query($sql); // supress errors before 2.3 db structure upgrade

		if(!$result)
			return null;

		while($pref = db_fetch_assoc($result)) {
			$this->prefs[$pref['name']] = $pref['value'];
		}

		// update current db status for info in log file
		//$this->db_ok = $this->prefs['version_id'] == $db_version;
	}



	function refresh()
	{
		global $db_version;

		$sql = "SELECT name, value FROM ".TB_PREF."sys_prefs";
		$result = @db_query($sql); // supress errors before 2.3 db structure upgrade

		if(!$result)
			return null;

		while($pref = db_fetch_assoc($result)) {
			$this->prefs[$pref['name']] = $pref['value'];
		}

		// update current db status for info in log file
		$this->db_ok = $this->prefs['version_id'] == $db_version;
	}

	function allow_negative_stock() 
	{
		return $this->prefs['allow_negative_stock'];
	}

    /* Sherifoz 26.06.03 Proportion by which items can be received over the quantity that is specified in a purchase
    invoice
    The figure entered is interpreted as a percentage ie 10 means 0.1 or 10% not 10 times
    */
	function over_receive_allowance() 
	{
		return $this->prefs['po_over_receive'];
	}
	
    /* Proportion by which a purchase invoice line is an overcharge for a purchase order item received
    is an overcharge. If the overcharge is more than this percentage then an error is reported and
    purchase invoice line cannot be entered
    The figure entered is interpreted as a percentage ie 20 means 0.2 or 20% not 20 times
    */	
	function over_charge_allowance() 
	{
		return $this->prefs['po_over_charge'];
	}
	
	function default_credit_limit() 
	{
		return $this->prefs['default_credit_limit'];
	}				
	
	function default_wo_required_by() 
	{
		return $this->prefs['default_workorder_required'];
	}

	function default_quote_valid_days() 
	{
		return $this->prefs['default_quote_valid_days'];
	}

	function default_delivery_required_by() 
	{
		return $this->prefs['default_delivery_required'];
	}

	function default_receival_required_by() 
	{
		return $this->prefs['default_receival_required'];
	}

	function default_dimension_required_by() 
	{
		return $this->prefs['default_dim_required'];
	}	
	function show_prices_dn()
    {
        return $this->prefs['show_prices_dn'];
    }
    function show_text_qty()
    {
        return $this->prefs['show_text_qty'];
    }
    function show_text_qty2()
    {
        return $this->prefs['show_text_qty2'];
    }
    function no_of_days()
    {
        return $this->prefs['no_of_days'];
    }
    function display_dim_cust_alloc()
    {
        return $this->prefs['display_dim_cust_alloc'];
    }
    function hide_prices_grn()
    {
        return $this->prefs['hide_prices_grn'];
    }
    function show_view_sale()
    {
        return $this->prefs['show_view_sale'];
    }
    function br_search()
    {
        return $this->prefs['br_search'];
    }
     function cust_item()
    {
        return $this->prefs['cust_item'];
    }
    
    function restrict_cust_credit_limit()
    {
        return $this->prefs['rest_cust_credit_limit'];
    }
    
    function edit_qty()
    {
        return $this->prefs['edit_qty'];
    }
    function delivery_appr()
    {
        return $this->prefs['delivery_appr'];
    }
    function show_view_quot()
    {
        return $this->prefs['show_view_quot'];
    }
    function invent_appr()
    {
    return $this->prefs['invent_appr'];
    }
    function show_view_purch()
    {
        return $this->prefs['show_view_purch'];
    }
    function purch_appr()
    {
        return $this->prefs['purch_appr'];
    }
    function order_appr()
    {
        return $this->prefs['order_appr'];
    }
    function grn_appr()
    {
        return $this->prefs['grn_appr'];
    }
    function show_view_delivery()
    {
        return $this->prefs['show_view_delivery'];
    }
    function show_view_invoice()
    {
        return $this->prefs['show_view_invoice'];
    }
    function enable_cogs()
    {
    return $this->prefs['enable_cogs'];
    }

	function auto_currency_revaluation() 
	{
		return $this->prefs['auto_curr_reval'];
	}	
	
	function allocation_settled_allowance()
	{
		return $this->config_allocation_settled_allowance;
	}
  function show_doc_ref()
    {
        return $this->prefs['show_doc_ref'];
    }
	function no_zero_lines_amount() 
	{
		return $this->prefs['no_zero_lines_amount'];
	}

	function show_po_item_codes() 
	{
		return $this->prefs['show_po_item_codes'];
	}

	function accounts_alpha() 
	{
		return $this->prefs['accounts_alpha'];
	}

	function loc_notification() 
	{
		return $this->prefs['loc_notification'];
	}

	function print_invoice_no() 
	{
		return $this->prefs['print_invoice_no'];
	}

	function allow_negative_prices() 
	{
		return $this->prefs['allow_negative_prices'];
	}

	function print_item_images_on_quote() 
	{
		return $this->prefs['print_item_images_on_quote'];
	}

	function alternative_tax_include_on_docs() 
	{
		return $this->prefs['alternative_tax_include_on_docs'];
	}

	function suppress_tax_rates() 
	{
		return $this->prefs['suppress_tax_rates'];
	}
	
	function show_exact_total()
    {
        return $this->prefs['show_exact_total'];
    }
    
    function hide_stock_list()
    {
        return $this->prefs['hide_stock_list'];
    }
	
	function enable_user_gl_restrict()
{
    return $this->prefs['enable_user_gl_restrict'];
}

	function backup_dir($comp=null)
	{
		if (!isset($comp))
			$comp = user_company();

		if (isset($this->backup_path))
			return sprintf($this->backup_path, $comp);
		else
			return $this->comp_path.'/'.$comp."/backup/";
	}
	
}

