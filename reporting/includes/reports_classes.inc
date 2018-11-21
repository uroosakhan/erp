<?php
function get_reports_id_new_($type_no)
{

    $sql = "SELECT rep_no FROM ".TB_PREF."reports_preference WHERE rep_no=".db_escape($type_no)." AND inactive=0";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}
function get_reports_name_new_($type_no)
{

    $sql = "SELECT rep_name FROM ".TB_PREF."reports_preference WHERE rep_no=".db_escape($type_no)." AND inactive=0";
    $result = db_query($sql, "could not get customer");
    $row = db_fetch_row($result);
    return $row[0];
}

// Standard report classess
define('RC_CUSTOMER', 0);
define('RC_SUPPLIER', 1);
define('RC_INVENTORY', 2);
define('RC_MANUFACTURE', 3);
define('RC_DIMENSIONS', 4);
define('RC_BANKING', 5);
define('RC_GL', 6);
define('RC_FIXEDASSETS', 7);
define('RC_CUSTOMERFORMS', 8);
define('RC_SUPPLIERFORMS', 9);
define('SA_PAYROLL_REPORT', 10);
define('RC_INVENTORY_REPORT', 11);
define('RC_INVENTORY_FORMS_REPORT', 12);

class BoxReports
{
	var $ar_classes; // report class names
	var $ar_reports;
	var $ctrl_handlers = array();

	function ReportClasses()
	{
		$this->ar_classes = array();
	}

	function addReportClass($class_name, $class_id=null)
	{
		if (!$class_id)
			$class_id = count($this->ar_classes);
		$this->ar_classes[$class_id] = $class_name;

		return $class_id;
	}

	function addReport($class, $id, $rep_name, $params=null)
	{
		unset($this->ar_reports[$class][$id]); // unset std report if any
		$this->ar_reports[$class][$id] = new Report($id, $rep_name, $params);
	}

	function getDisplay($class=null)
	{
		global $path_to_root, $SysPrefs, $Ajax;

		if (find_submit('Rep') != -1) {
			include($path_to_root.'/reporting/prn_redirect.php');
			return;
		}

		$temp = array_values($this->ar_classes);
		$display_class = $class==null ? $temp[0] : $this->ar_classes[$class];
		$st_reports = "";
		$st_params = "";
		$st_classes = "<b>" . _("Report Classes:") . "</b><br>";

		if (isset($_GET['Class']))
			set_focus('class'.$_GET['Class']);

		$cur_class = $_REQUEST['Class'];

		foreach($this->ar_classes as $class_id => $name)
		{
			if (!isset($this->ar_reports[$class_id]))
				continue; // e.g. no dimensions

			$acc = access_string($name);
			$st_classes .= "<a href='"
				.$_SERVER['PHP_SELF']."?Class=$class_id'"
				." style='font-weight:". ($cur_class == $class_id ? 'bold' : 'normal')."'"
				." class='repclass_link' id='".'class'.$class_id."'"
				."$acc[1]>$acc[0]</a> <br>";

			$style = $class_id==$cur_class ? '' : "style='display:none'";
			$st_reports .= "<table class='repclass' id='TAB_" . $class_id ."' $style cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td><b>" . _("Reports For Class: ") . "&nbsp;$name</b></td></tr>\n";
			foreach($this->ar_reports[$class_id] as $rep_id => $report)
			{
				$acc = access_string($report->name);
$re_id=get_reports_id_new_($report->id);
$rep_name=get_reports_name_new_($report->id);
$rep_n=$report->id;
if($re_id==$rep_n) {
    if ($re_id) {
        global $db_connections;
        if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'OZONE'  && $_SESSION["wa_current_user"]->access == 4) {
            if ($class_id == 2) {
                $st_reports .= "<tr><td><a class='repopts_link'"
                    . " href='" . $_SERVER['PHP_SELF'] . "?Class=$class_id&REP_ID=$re_id'"
                    . " style='font-weight:" . (@$_REQUEST['REP_ID'] == $re_id ? 'bold' : 'normal') . "'"
                    . " id='" . $id = default_focus() . "'"
                        . "$acc[1]>$rep_name</a><tr><td>\n";
            }
        }
        else{
            $st_reports .= "<tr><td><a class='repopts_link'"
                . " href='" . $_SERVER['PHP_SELF'] . "?Class=$class_id&REP_ID=$re_id'"
                . " style='font-weight:" . (@$_REQUEST['REP_ID'] == $re_id ? 'bold' : 'normal') . "'"
                . " id='" . $id = default_focus() . "'"
                    . "$acc[1]>$rep_name</a><tr><td>\n";
        }
    }


    if (@$_REQUEST['REP_ID'] == $re_id) {
        $Ajax->activate($id);
    }
}

				if (isset($_REQUEST['REP_ID']) && $_REQUEST['REP_ID']==$report->id) {
					// parameters form
					$action = $_SERVER['PHP_SELF'];
					$st_params = "<table><tr><td>\n"
						. "<form method='POST' action='$action' target='_blank'>\n";
					$st_params .= submit('Rep'.$report->id,  
						_("Display: ") . access_string($report->name, true),
						false, '', $SysPrefs->pdf_debug ? false : 'default process') . hidden('REP_ID', $report->id, false).'<br><br>';
					$st_params .= $this->getOptions($report->get_controls(), $report->id);
					$st_params .= "\n<input type=hidden name='Class' value=".$cur_class.">"
						."\n</form></td></tr></table>\n";
					set_focus('Rep'.$report->id);
					$Ajax->addUpdate(true, 'rep_form', $st_params);
				}
			}
			$st_reports .= "</table>";
		}

		$st_params = "<div id='rep_form'>".
			"$st_params</div>";
		
		$st = "<table align='center' width='80%' style='border:1px solid #cccccc;'><tr valign='top'>";
		$st .= "<td width='30%'>$st_classes</td>";
		$st .= "<td width='35%' style='border-left:1px solid #cccccc;border-right:1px solid #cccccc;padding-left:3px;'>$st_reports</td>";
		$st .= "<td width='35%'>$st_params</td>";
		$st .= "</tr></table><br>";

		return $st;
	}

	function getOptions($controls, $id = 0)
	{
		$st = '';
		if ($controls == null)
			return "";

		$cnt = 0;
		foreach($controls as $title => $type)
		{
			$ctrl = '';
			foreach($this->ctrl_handlers as $fun) { // first check for non-standard controls
				$ctrl = $fun('PARAM_'.$cnt, $type);
				if ($ctrl) break;
			}
			if ($ctrl == '') {
				if (($id == 102 || $id == 202) && $cnt == 3) // set default Show Also Allocated to Yes in aged reports 2014-09-19 Joe Hunt
					$_POST['PARAM_'.$cnt] = 1;
				
				if (isset($_COOKIE['select'][$id][$cnt])) // saved parameters 2010-10-06 Joe Hunt
					$_POST['PARAM_'.$cnt] = $_COOKIE['select'][$id][$cnt];
				$ctrl = $this->get_ctrl('PARAM_'.$cnt, $type);
			}
			if ($ctrl != '')  {
				$st .= $title . ':<br>';
				$st .= $ctrl;
				$st .= "<br><br>";
			} else {
				display_error(_('Unknown report parameter type:').$type);
			}
			$cnt++;
		}
		return $st;
	}

	//
	//	Register additional control handler
	// $handle - name of global function f($name, $type) returning html code for control
	function register_controls($handler) {
		$this->ctrl_handlers[] = $handler;
	}
	//
	//	Returns html code for input control $name of type $type 
	//
	function get_ctrl($name, $type)
	{
		global $path_to_root, $SysPrefs,
			$type_shortcuts;

		$st = '';
			switch ($type)
			{
				case 'CURRENCY':
					$sql = "SELECT curr_abrev, concat(curr_abrev,' - ', currency) AS name FROM ".TB_PREF."currencies";
					return combo_input($name, '', $sql, 'curr_abrev', 'name',array('spec_option'=>_("No Currency Filter"),'spec_id'=>ALL_TEXT,'order'=>false));

				case 'DATE':
				case 'DATEBEGIN':
				case 'DATEEND':
				case 'DATEBEGINM':
				case 'DATEENDM':
				case 'DATEBEGINTAX':
				case 'DATEENDTAX':
					if (!isset($_POST[$name]))
					{
						if ($type == 'DATEBEGIN')
							$date = begin_fiscalyear();
						elseif ($type == 'DATEEND')
							$date = end_fiscalyear();
						else
							$date = Today();
						if ($type == 'DATEBEGINM')
							$date = begin_month($date);
						elseif ($type == 'DATEENDM')
							$date = end_month($date);
						elseif ($type == 'DATEBEGINTAX' || $type == 'DATEENDTAX')
						{
							$row = get_company_prefs();
							$edate = add_months($date, -$row['tax_last']);
							$edate = end_month($edate);
							if ($type == 'DATEENDTAX')
								$date = $edate;
							else
							{
								$bdate = begin_month($edate);
								$bdate = add_months($bdate, -$row['tax_prd'] + 1);
								$date = $bdate;
							}
						}
					}
					else // saved parameters
						$date = $_POST[$name];
					$st = "<input type='text' name='$name' value='$date'>";
					if (user_use_date_picker())
					{
						$calc_image = (file_exists("$path_to_root/themes/".user_theme()."/images/cal.gif")) ? 
							"$path_to_root/themes/".user_theme()."/images/cal.gif" : "$path_to_root/themes/default/images/cal.gif";
						$st .= "<a href=\"javascript:date_picker(document.forms[0].$name);\">"
						. "	<img src='$calc_image' style='vertical-align:middle;padding-bottom:4px;width:16px;height:16px;border:0;' alt='"._('Click Here to Pick up the date')."'></a>\n";
					}	
					return $st;
					break;

				case 'YES_NO':
					return yesno_list($name);
					
					
						case 'YES_NO_RECONCILE':
					return yesno_reconcile_list($name, $selected_id=null,"All", "Yes", "No" );
				
					case 'YES_NO_VOIDED':
					return yesno_list($name,null,"NO","YES");

						case 'YESNO_DEFAULT_YES':
					return yesno_bydefault_yes_list($name,null,"NO","YES");
					
					case 'SHOW_VOIDED':
					return yesno_bydefault_yes_list($name,null,"YES","ALL");
					
					case 'YESNO_SHOW_AMOUNT':
                    return yesno_bydefault_yes_list($name,null,"YES","NO");

					   case 'BARCODE_TEMPLATE':
                    return barcode_temp_list($name);
					
				case 'YES_NO_DEFAULT_YES':
					return yesno_list($name, 1);					
					
				case 'YES_NO_BALANCE':
					return yesno_list($name,null,"yes");
				
                case 'PL_SALES':
                    return yesno_list_pl_sales($name,null,"Sales Book","Profit and Loss",true);	
					
				case 'YES_NO_LOGO':
					return yesno_list_logo($name,null,"yes","no",true);

case 'CURR_CLOSE':
                    return yesno_list_curr_close($name,null,"Show Only Closing Balance","All",true);

                case 'PRICE':
                    return price_list_logo($name,null,"Standard Cost","Price",true);

case 'UOM':
                    return yesno_list_logo($name,null,"Yes","No",true);
                    
case 'DUE_INTERVAL':
                    return "<input type='text' name='$name' value='30' size = '1'>"; //ansar 26-08-17

case 'COLUMNS':
                    return yesno_list($name,null,"6 Columns","3 Columns");
case 'INV_DUE':
                    return yesno_list($name,null,"Invoice Date","Due Date");
                    


case 'PRICE2':
                    return price_list_logo1($name,null, "Costed Value","Purchase Price","Sales Price","Standard Cost",false);
                    
                    case 'MONTHLY_SALE_AQ':
                        return yesno_list($name,'','In Amount','In Qty');
                    
                    case 'MONTHLY_SALE_WEEK':
                    return yesno_week_list($name,'','All Month','Week 1', 'Week 2', 'Week 3', 'Week 4', 'All Four Weeks');

                case 'MONTHLY_SALE_AREA_GROUP':
                    return yesno_area_group_list($name,'','Customers','Area', 'Group');
                case 'CUST_INV_SALESMAN':
                    return yesno_area_group_list($name,'','Customers','Salesman/Invoice', 'Salesman/Setup');                    
                case 'CUSTOMER_PRINT_AS':
                        return yesno_list($name,'','Code','Name');
                    
				case 'PAYMENT_LINK':
					return payment_services($name, null);

                case 'PAYMENT_TERMS':
					return sale_payment1_list($name, null,null,null,null,"All");


				case 'DESTINATION':
					$sel = array(_("PDF/Printer"), "Excel");
					$def = 0;
					if (user_def_print_destination() == 1)
						$def = 1;
					return array_selector($name, $def, $sel);

				case 'ORIENTATION':
					$sel = array(_("Portrait"), _("Landscape"));
					$def = 0;
					if (user_def_print_orientation() == 1)
						$def = 1;
					return array_selector($name, $def, $sel);

				case 'COMPARE':
					$sel = array(_("Accumulated"), _("Period Y-1"), _("Budget"));
					return array_selector($name, null, $sel);


case 'ITEM_LOCATIONS':
					return  item_location_list($name, null, true);
				
 case 'GATE_PASS':
                    return  gate_pass_list($name, null, false);


              case 'LC_REF':
					return  lc_ref_list($name, null, _("No Filter"));

            case 'LADING_NO':
					return  lading_no_list($name, null, _("No Filter"));

				case 'GRAPHIC':
					$sel = array(_("No Graphics"), _("Vertical bars"), _("Horizontal bars"), 
								_("Dots"), _("Lines"), _("Pie"), _("Donut"));
					return array_selector($name, null, $sel);

				case 'SYS_TYPES':
					return gl_systypes_list($name, null, _("No Type Filter"));
				
				case 'SYS_TYPES_INVENT':
					return inventory_systypes_list($name, null, _("No Type Filter"));
				
				case 'SYS_TYPES_CUST':
					return cust_bal_systypes_list($name, null, _("No Type Filter"));
					
				case 'SYS_TYPES_TAX':
					return tax_systypes_list($name, null, _("No Type Filter"));
				
				case 'SYS_TYPES_SUPP':
                    return supp_systypes_list($name, null, _("No Type Filter"));
				
				case 'SORTER':
					return sorter_list($name, null);
				
				case 'ITEMS_BARCODE':
					return stock_items_list($name, null, false, false);

				case 'SYS_TYPES_ALL':
					return systypes_list($name, null, _("No Type Filter"));


               case 'INVADJUST':
					$sql = "SELECT moves.trans_no, moves.reference as IName
						FROM ".TB_PREF."stock_moves moves
						WHERE moves.type=".ST_INVADJUST
						." GROUP BY moves.trans_no ORDER BY moves.trans_no DESC";
					return combo_input($name, '', $sql, 'trans_no', 'IName',array('order'=>false));


				case 'TEXT':
					return "<input type='text' name='$name'>";

				case 'TEXTBOX':
                    $value = (isset($_POST[$name]) ? $_POST[$name] : "");
                    return "<textarea rows=4 cols=30 maxlength=130 name='$name'>$value</textarea>"; //ansar 26-08-17

				case 'ACCOUNTS': // not used
					return gl_account_types_list($name, null, _("No Account Group Filter"), true);

				case 'ACCOUNTS_NO_FILTER': // not used
				return gl_account_types_list($name,null,true);

                case 'ACCOUNTS_FILTER': // not used
                return gl_account_types_list($name,1013,false,false,false);

				case 'GL_ACCOUNTS':
					return gl_all_accounts_list($name,null,false,false,true);

				case 'BANK_ACCOUNTS':
					return bank_accounts_list_all($name);
					
				case 'BANK_ALL_ACCOUNTS':
                    return bank_accounts_list_all($name,null,false,true);

				case 'MULTI_CATAGORIES':
                    return stock_purchasable_items_list_multi($name, null,false);
                    
                    case 'MULTI_CUST':
                    return customer_list_multi($name, null,false);

                case 'MULTI_SUPP':
                    return supplier_list_multi($name, null,false);
                    
                    
                case 'MULTI_ITEMS':
                    return all_items_list_multi($name, null,true);
                case 'RESOURCE':
					return source_status_query_list($name, null, _("No Resource"));
					
					
					       case 'SUPP_IMPORT_INVOICE':
					$IV = $type_shortcuts[ST_SUPPCREDIT_IMPORT];
					$ref = ($print_invoice_no == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(".TB_PREF."supp_trans.trans_no, '-',
						".TB_PREF."supp_trans.type) AS TNO, concat('$IV ', 
						".TB_PREF."supp_trans.reference,' ', ".TB_PREF."suppliers.supp_name) as IName
						FROM ".TB_PREF."suppliers, ".TB_PREF."supp_trans WHERE
						type=".ST_SUPPCREDIT_IMPORT." AND ".TB_PREF."suppliers.supplier_id=
						".TB_PREF."supp_trans.supplier_id ORDER BY 
						".TB_PREF."supp_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

					case 'IPI':
              $IV = $type_shortcuts[ST_SUPPCREDIT_IMPORT];
              $ref = ($print_invoice_no == 1 ? "trans_no" : "reference");
              $sql = "SELECT concat(".TB_PREF."supp_trans.trans_no, '-',
".TB_PREF."supp_trans.type) AS TNO, concat('$IV ', 
".TB_PREF."supp_trans.reference,' ', ".TB_PREF."suppliers.supp_name) as IName
FROM ".TB_PREF."suppliers, ".TB_PREF."supp_trans WHERE
type=".ST_SUPPCREDIT_IMPORT." AND ".TB_PREF."suppliers.supplier_id=
".TB_PREF."supp_trans.supplier_id ORDER BY 
".TB_PREF."supp_trans.trans_no DESC";
              return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
              
				     case 'STATUS':
                    return status_query_list($name, null, _("All Status"), false, false, false);
	
					     case 'USER':
                    return users_query_list($name, null, _("All USER"), false, false, false);

                case 'SOURCE':
                    return source_status_query_list($name, null, _("All Source"), false, false, false);

					
				case 'SALESGROUP':
					return sales_groups_list($name, null, _("No Sales Man Filter"));
				case 'DIMENSION':
					return dimensions_list($name, -1,  ' ', false, true, 0);
					
				case 'DIMENSIONS':
					return dimensions_list($name, -1,  _("No Dimension Filter"), false, true, 0);
					
					
					case 'COLOR':
					return combo1_po_list($name, null, true, true);
						
					case 'COMBO3':
					return combo3_list($name, null, true, true);
						

				case 'DIMENSION1':
					return dimensions_list($name, -1,  ' ', false, true, 1);
			
					

				case 'DIMENSIONS1':
					return dimensions_list($name, -1,  _("No Dimension Filter"), false, true, 1);

				case 'DIMENSION2':
					return dimensions_list($name, -1,  ' ', false, true, 2);

				case 'DIMENSIONS2':
					return dimensions_list($name, -1,  _("No Dimension Filter"), false, true, 2);

				case 'CUSTOMERS_NO_FILTER':
				case 'CUSTOMERS':
					$sql = "SELECT debtor_no, name FROM ".TB_PREF."debtors_master";
					if ($type == 'CUSTOMERS_NO_FILTER')
						return customer_list($name, null, _("No Customer Filter"),true);
					else
						return customer_list($name,null,null,true);

				case 'LOCATION_NO_FILTER':
						return customer_list($name, null, _("All"),true);

				case 'CUSTOMERS_NO_FILTER_':
				case 'CUSTOMERS_':
					$sql = "SELECT debtor_no, name FROM ".TB_PREF."debtors_master";
					if ($type == 'CUSTOMERS_NO_FILTER')
						return customer_list($name, null, _("No Customer Filter"));
					else
						return customer_list($name,null, _("No Customer Filter"));
                case 'BRANCH_LIST':
                    return customer_branches_list($_POST['PARAM_2'], $name, true);
                case 'GROUPS':
                    return sales_groups_list($name, null, ("No group Filter") );
				case 'SUPPLIERS_NO_FILTER':
				case 'SUPPLIERS':
					$sql = "SELECT supplier_id, supp_name FROM ".TB_PREF."suppliers";
					if ($type == 'SUPPLIERS_NO_FILTER')
						return supplier_list($name, null, _("No Supplier Filter"));
					else
						return supplier_list($name);
				case 'LOCATION_TRANSFER':
					$IV = $type_shortcuts[ST_LOCTRANSFER];
//					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT debtor_trans.trans_no AS TNO,
								concat('$IV ', debtor_trans.reference) as IName
						FROM ".TB_PREF."stock_moves debtor_trans
						WHERE debtor_trans.type= ".ST_LOCTRANSFER."
						GROUP BY debtor_trans.trans_no
						ORDER BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

				case 'INVOICE':
					$IV = $type_shortcuts[ST_SALESINVOICE];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(debtor_trans.trans_no, '-', debtor_trans.type) AS TNO,
								concat('$IV ', debtor_trans.$ref,' ', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans debtor_trans
						WHERE type=".ST_SALESINVOICE." AND debtor.debtor_no=debtor_trans.debtor_no
						ORDER BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
					
					
					
					
					
					case 'INVOICE_NEW':
					$IV = $type_shortcuts[ST_SALESINVOICE];
					$ref = ($print_invoice_no == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(".TB_PREF."debtor_trans.trans_no, '-',
						".TB_PREF."debtor_trans.type) AS TNO, concat('$IV ', ".TB_PREF."debtor_trans.$ref,' ',
					    ".TB_PREF."debtors_master.name) as IName
						FROM ".TB_PREF."debtors_master, ".TB_PREF."debtor_trans WHERE type=".ST_SALESINVOICE."
						AND ".TB_PREF."debtors_master.debtor_no=".TB_PREF."debtor_trans.debtor_no
						AND ".TB_PREF."debtor_trans.ov_amount != 0
						AND ".TB_PREF."debtor_trans.debtor_no = ".$_POST['PARAM_0']."

					ORDER BY ".TB_PREF."debtor_trans.trans_no DESC";

					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));				

					
					
					
					
					
					
					
				 case 'INVOICE_TAX':
					$IV = $type_shortcuts[ST_SALESINVOICE];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(debtor_trans.trans_no, '-', debtor_trans.type) AS TNO,
							concat('$IV ', debtor_trans.$ref,' ', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans debtor_trans
						WHERE type=".ST_SALESINVOICE." 
						AND debtor.debtor_no=debtor_trans.debtor_no
						AND debtor_trans.tax_group_id = 5
						GROUP BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
					
					case 'INVOICE_TAX2':
					$IV = $type_shortcuts[ST_SALESINVOICE];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(debtor_trans.trans_no, '-', debtor_trans.type) AS TNO,
							concat('$IV ', debtor_trans.$ref,' ', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans debtor_trans
						WHERE type=".ST_SALESINVOICE." 
						AND debtor.debtor_no=debtor_trans.debtor_no
						AND debtor_trans.tax_group_id = 3
						GROUP BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
					
                  case 'PURCHASE_INVOICE':
                    $ref = ($print_invoice_no == 1 ? "trans_no" : "reference");
                    $sql = "SELECT ".TB_PREF."supp_trans.trans_no, concat(".TB_PREF."supp_trans.$ref, '-',
						".TB_PREF."suppliers.supp_name) as IName
						FROM ".TB_PREF."suppliers, ".TB_PREF."supp_trans WHERE ".TB_PREF."suppliers.supplier_id=
						".TB_PREF."supp_trans.supplier_id ORDER BY ".TB_PREF."supp_trans.trans_no DESC";
                    return combo_input($name, '', $sql, 'trans_no', 'IName',array('order'=>false));
               
               
case 'PI':
					$IV = $type_shortcuts[ST_SUPPINVOICE];
					$ref = ($print_invoice_no == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(".TB_PREF."supp_trans.trans_no, '-',
						".TB_PREF."supp_trans.type) AS TNO, concat('$IV ', 
						".TB_PREF."supp_trans.reference,' ', ".TB_PREF."suppliers.supp_name) as IName
						FROM ".TB_PREF."suppliers, ".TB_PREF."supp_trans WHERE
						type=".ST_SUPPINVOICE." AND ".TB_PREF."suppliers.supplier_id=
						".TB_PREF."supp_trans.supplier_id ORDER BY 
						".TB_PREF."supp_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
				case 'CREDIT':
					$CN = $type_shortcuts[ST_CUSTCREDIT];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(debtor_trans.trans_no, '-', debtor_trans.type) AS TNO,
								concat('$CN ', debtor_trans.$ref,' ', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans debtor_trans
						WHERE type=".ST_CUSTCREDIT." AND debtor.debtor_no=debtor_trans.debtor_no
						ORDER BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));
case 'GRN':
					$IV = $type_shortcuts[ST_SUPPCREDIT];
					$ref = ($print_invoice_no == 1 ? "id" : "reference");
					$sql = "SELECT concat(".TB_PREF."grn_batch.id, '-',
						".TB_PREF."grn_batch.reference) AS TNO, concat('$IV ', 
						".TB_PREF."grn_batch.reference,' ', ".TB_PREF."suppliers.supp_name) as IName
						FROM ".TB_PREF."suppliers, ".TB_PREF."grn_batch WHERE
						 ".TB_PREF."suppliers.supplier_id=
						".TB_PREF."grn_batch.supplier_id ORDER BY 
						".TB_PREF."grn_batch.id DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

				case 'DELIVERY':
					$DN = $type_shortcuts[ST_CUSTDELIVERY];
					$sql = "SELECT	concat(debtor_trans.trans_no, '-', debtor_trans.type) AS TNO,
								concat(debtor_trans.reference, ' $DN ', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans debtor_trans
						WHERE type=".ST_CUSTDELIVERY." AND debtor.debtor_no=debtor_trans.debtor_no
						ORDER BY debtor_trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

				case 'ORDERS':
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "order_no" : "reference");
					$sql = "SELECT sorder.order_no, concat(sorder.$ref, '-', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."sales_orders sorder
						WHERE debtor.debtor_no=sorder.debtor_no	AND sorder.trans_type=".ST_SALESORDER
						." ORDER BY sorder.order_no DESC";
					return combo_input($name, '', $sql, 'order_no', 'IName',array('order'=>false));

				case 'QUOTATIONS':
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "order_no" : "reference");
					$sql = "SELECT sorder.order_no, concat(sorder.$ref, '-', debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."sales_orders sorder
						WHERE debtor.debtor_no=sorder.debtor_no	AND sorder.trans_type=".ST_SALESQUOTE
							." ORDER BY sorder.order_no DESC";
					return combo_input($name, '', $sql, 'order_no', 'IName',array('order'=>false));

				case 'PO':
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "order_no" : "reference");
					$sql = "SELECT po.order_no, concat(po.$ref, '-', supplier.supp_name) as IName
						FROM ".TB_PREF."suppliers supplier,"
							.TB_PREF."purch_orders po
						WHERE supplier.supplier_id=po.supplier_id
						ORDER BY po.order_no DESC";
					return combo_input($name, '', $sql, 'order_no', 'IName',array('order'=>false));
					
					
					
					  case 'PURCH':
                    $ref = ($SysPrefs->print_invoice_no() == 1 ? "order_no" : "reference");
                    $sql = "SELECT po.order_no, concat(po.$ref, '-', supplier.supp_name) as IName
						FROM ".TB_PREF."suppliers supplier,"
                        .TB_PREF."purch_orders po
						WHERE supplier.supplier_id=po.supplier_id
						ORDER BY po.order_no DESC";
                    return combo_input($name, '', $sql, 'order_no', 'IName',array('spec_option'=>_("ALL"),'spec_id'=>ALL_TEXT,'order'=>false));
					
					
					

				case 'REMITTANCE':
					$BP = $type_shortcuts[ST_BANKPAYMENT];
					$SP = $type_shortcuts[ST_SUPPAYMENT];
					$CN = $type_shortcuts[ST_SUPPCREDIT];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(trans.trans_no, '-',trans.type) AS TNO,
								concat(trans.$ref, IF(type=".ST_BANKPAYMENT.", ' $BP ', IF(type=".ST_SUPPAYMENT.", ' $SP ', ' $CN ')), supplier.supp_name) as IName
							FROM ".TB_PREF."suppliers supplier, "
								.TB_PREF."supp_trans trans
							WHERE type IN(".ST_BANKPAYMENT.",".ST_SUPPAYMENT.",".ST_SUPPCREDIT.")
								AND supplier.supplier_id=trans.supplier_id
							ORDER BY trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

case 'PR':
                    //$ref = ($print_invoice_no == 1 ? "requisition_id" : "requisition_id");
                    $sql = "SELECT ".TB_PREF."requisition_details.requisition_id, concat(".TB_PREF."requisition_details.requisition_id, '-',
						".TB_PREF."suppliers.supp_name) as IName
						FROM ".TB_PREF."suppliers, ".TB_PREF."requisition_details 
						WHERE ".TB_PREF."requisition_details.supplier_id = ".TB_PREF."suppliers.supplier_id 
						AND ".TB_PREF."requisition_details.supplier_id";
                    return combo_input($name, '', $sql, 'requisition_id', 'IName',array('order'=>false));
case 'ITEMS_I':
					return stock_costable_items_list($name, null, null, true, true);

				case 'BATCH':
					return batch_list($_POST['PARAM_2'], $name, null, true, true, false,  false);


				case 'RECEIPT':
					$BD = $type_shortcuts[ST_BANKDEPOSIT];
					$CP = $type_shortcuts[ST_CUSTPAYMENT];
					$ref = ($SysPrefs->print_invoice_no() == 1 ? "trans_no" : "reference");
					$sql = "SELECT concat(trans.trans_no, '-', trans.type) AS TNO,
								concat(trans.$ref, IF(type=".ST_BANKDEPOSIT.", ' $BD ', ' $CP '), debtor.name) as IName
						FROM ".TB_PREF."debtors_master debtor,"
							.TB_PREF."debtor_trans trans
						WHERE type IN(".ST_BANKDEPOSIT.",".ST_CUSTPAYMENT.",".ST_CUSTCREDIT.") AND debtor.debtor_no=trans.debtor_no
						ORDER BY trans.trans_no DESC";
					return combo_input($name, '', $sql, 'TNO', 'IName',array('order'=>false));

				case 'ITEMS':
					return stock_manufactured_items_list($name);

				case 'ITEMS_ALL':
					return stock_manufactured_items_list($name, null, true);

				case 'ITEMS_P':
					return stock_purchasable_items_list($name, null, true);
				
				
                case 'ITEMS_ALL_':
                    return stock_items_list($name, null, true, false);
				
				case 'WORKORDER':
					$sql = "SELECT wo.id, concat(wo.wo_ref, '-', item.description) as IName
						FROM ".TB_PREF."stock_master item,"
							.TB_PREF."workorders wo
						WHERE item.stock_id=wo.stock_id
						ORDER BY wo.id DESC";
					return combo_input($name, '', $sql, 'id', 'IName',array('order'=>false));

                case 'MONTH':
    				return month_list($name, null,True);
    				
    			case 'EMPLOYEE':
				    return employee_list($name, null, _("All Employees"));

                case 'PROJECT_NEW':
					return pro_list($name, null, 'All Projects','', True, false, 2,$_POST['PARAM_0']);
				
				case 'TAX_GROUPS':
                    return tax_groups_list($name,null, _("All"));
                    
				case 'LOCATION':
					return loc_list($name, null, 'All Locations', ' ',false, false, 3,$_POST['PARAM_1']);

                case 'DEPT':
					return emp_dept($name, null,  _("No Department Filter") );

                case 'UPROVE':
    				return approve_list($name);

				case 'LOCATIONS':
					return  locations_list($name, null, _("No Location Filter"));
				case 'FLOCATIONS':
					return  locations_list($name, null, _("No Location Filter"), false, true);

				case 'CATEGORIES':
					return stock_categories_list($name, null, _("No Category Filter"));
				case 'FCATEGORIES':
					return stock_categories_list($name, null, _("No Category Filter"), false, true);
				case 'FCLASS':
					return fixed_asset_classes_list($name, null, _("No Class Filter"), false);
				case 'SALESTYPES':
					return sales_types_list($name);
                case 'SALESTYPES2':
					return sales_types_list($name, null, null, "Select Type");
				case 'AREAS':
					return sales_areas_list($name);

				case 'ZONES':
					return sales_areas_list($name, null, ("No Area Filter") );

                case 'PURCH_PRICE':
                 return yesno_list($name,null,"Standard Cost","Purchase Price");
                   
				case 'SALESMEN':
					return sales_persons_list($name, null, _("All"));
					
				case 'TRANS_YEARS':
					return fiscalyears_list($name);
					
				case 'YES_NO_DELIVERED':
                    return yesno_reconcile_list($name, $selected_id=null,"All", "Delivered", "Non-Delivered" );
					
				case 'REORDER_LOCATIONS':
                return  reorder_locations_list($name, null, false, false, true);
					
				case 'YESNO_ORDERBY_ITEM':
                return yesno_bydefault_yes_list($name,null,"ITEMS","CUSTOMER");

				case 'USERS':
					$sql = "SELECT id, user_id FROM ".TB_PREF."users";
					return combo_input($name, '', $sql, 'id', 'user_id',array('spec_option'=>_("No Users Filter"),'spec_id'=>ALL_NUMERIC));
					
				case 'SHIPPINGCOMPANY';
                $ref = ($SysPrefs->print_invoice_no() == 1 ? "order_no" : "reference");
                $sql = "SELECT shipper_id, shipper_name, inactive FROM ".TB_PREF."shippers";
                return combo_input($name, '', $sql, 'order_no', 'IName',array('order'=>false));

				case 'ACCOUNTTAGS':
				case 'DIMENSIONTAGS':
					if ($type == 'ACCOUNTTAGS')
						$tag_type = TAG_ACCOUNT;
					else
						$tag_type = TAG_DIMENSION;
					return tag_list($name, 5, $tag_type, true);

			}
		return '';
	}
}

class Report
{
	var $id;
	var $name;
	var $ar_params;
	var $controls;
	
	function Report($id, $name, $ar_params = null)
	{
		$this->id = $id;
		$this->name = $name;
		if ($ar_params) $this->set_controls($ar_params);
	}
	
	function set_controls($ar_params) {
		$this->controls = $ar_params;
	}
	
	function get_controls() {
		return $this->controls;
	}
	
}

//------------------------------------------------------------------------------------------------
function gl_systypes_list($name, $value=null, $spec_opt=false)
{
	global $systypes_array;
	
	$types = $systypes_array;
	
	foreach(array(ST_LOCTRANSFER, ST_PURCHORDER, ST_SUPPRECEIVE, ST_MANUISSUE,
				ST_MANURECEIVE, ST_SALESORDER, ST_SALESQUOTE, ST_DIMENSION) as $type)
		unset($types[$type]);

	return array_selector($name, $value, $types, 
		array( 
			'spec_option'=> $spec_opt,
			'spec_id' => ALL_NUMERIC,
			'async' => false,
			)
	);
}
//==============================================================================
//RA
function inventory_systypes_list($name, $value=null, $spec_opt=false)
{
	global $systypes_array;

	$types = $systypes_array;
//ST_MANUISSUE, ST_MANURECEIVE, ST_SUPPRECEIVE, ST_CUSTCREDIT, ST_INVADJUST, ST_CUSTDELIVERY
	foreach(array(ST_PURCHORDER,ST_CPV ,
                ST_CRV, ST_SALESORDER, ST_DIMENSION,
                ST_SALESQUOTE, ST_JOURNAL, ST_BANKPAYMENT, ST_BANKDEPOSIT, ST_BANKTRANSFER, ST_SALESINVOICE, // ST_CUSTCREDIT,
                ST_CUSTPAYMENT, ST_SUPPINVOICE, ST_WORKORDER, ST_MANUISSUE, ST_LOCTRANSFER, ST_SUPPCREDIT_IMPORT, ST_SUPPAYMENT, ST_COSTUPDATE, ST_EMPLOYEECODE) as $type)
		unset($types[$type]);

	return array_selector($name, $value, $types,
		array(
			'spec_option'=> $spec_opt,
			'spec_id' => ALL_NUMERIC,
			'async' => false,
			)
	);
}

function cust_bal_systypes_list($name, $value=null, $spec_opt=false)
{
    global $systypes_array;

    $types = $systypes_array;
//ST_MANUISSUE, ST_MANURECEIVE, ST_SUPPRECEIVE, ST_CUSTCREDIT, ST_INVADJUST, ST_CUSTDELIVERY
    foreach(array(ST_PURCHORDER, ST_SALESORDER, ST_DIMENSION,
                ST_SALESQUOTE, ST_BANKTRANSFER,
                ST_SUPPINVOICE, ST_WORKORDER, ST_MANUISSUE, ST_LOCTRANSFER, ST_SUPPCREDIT_IMPORT,
                ST_SUPPAYMENT, ST_COSTUPDATE, ST_EMPLOYEECODE, ST_CUSTDELIVERY, ST_SUPPRECEIVE,
                ST_WORKORDER, ST_MANURECEIVE, ST_SUPPCREDIT, ST_INVADJUST, ST_PURCHREQ, ST_FAADJUST) as $type)
        unset($types[$type]);

    return array_selector($name, $value, $types,
        array(
            'spec_option'=> $spec_opt,
            'spec_id' => ALL_NUMERIC,
            'async' => false,
        )
    );
}

function tax_systypes_list($name, $value=null, $spec_opt=false)
{
    global $systypes_array;

    $types = $systypes_array;
//ST_MANUISSUE, ST_MANURECEIVE, ST_SUPPRECEIVE, ST_CUSTCREDIT, ST_INVADJUST, ST_CUSTDELIVERY
    foreach(array(ST_PURCHORDER, ST_SALESORDER, ST_DIMENSION,ST_JOURNAL,ST_BANKDEPOSIT,ST_BANKPAYMENT,ST_FAADJUST,
                ST_SALESQUOTE, ST_BANKTRANSFER, ST_CPV, ST_CRV, ST_CUSTCREDIT, ST_CUSTPAYMENT,
                ST_WORKORDER, ST_MANUISSUE, ST_LOCTRANSFER, ST_SUPPCREDIT_IMPORT,
                ST_SUPPAYMENT, ST_COSTUPDATE, ST_EMPLOYEECODE, ST_CUSTDELIVERY, ST_SUPPRECEIVE,
                ST_WORKORDER, ST_MANURECEIVE, ST_SUPPCREDIT, ST_INVADJUST) as $type)
        unset($types[$type]);

    return array_selector($name, $value, $types,
        array(
            'spec_option'=> $spec_opt,
            'spec_id' => ALL_NUMERIC,
            'async' => false,
        )
    );
}

function supp_systypes_list($name, $value=null, $spec_opt=false)
{
    global $systypes_array;

    $types = $systypes_array;

    foreach(array(ST_LOCTRANSFER, ST_PURCHORDER, ST_SUPPRECEIVE, ST_MANUISSUE,ST_WORKORDER,ST_BANKTRANSFER,
                ST_EMPLOYEECODE,ST_INVADJUST,ST_CUSTDELIVERY,ST_CUSTCREDIT,ST_CUSTPAYMENT,ST_SALESINVOICE,
                ST_MANURECEIVE, ST_SALESORDER, ST_SALESQUOTE, ST_COSTUPDATE,ST_DIMENSION) as $type)
        unset($types[$type]);

    return array_selector($name, $value, $types,
        array(
            'spec_option'=> $spec_opt,
            'spec_id' => ALL_NUMERIC,
            'async' => false,
        )
    );
}
//==============================================================================

/*
	Add extension/custom reports to report manager.
*/
function add_custom_reports(&$reports)
{
	global $installed_extensions, $path_to_root;
	
	// include reports installed inside extension modules
	if (count($installed_extensions) > 0)
	{
		$extensions = $installed_extensions;
		foreach ($extensions as $ext)
			if (($ext['active'] && $ext['type'] == 'extension')) {
				$file = $path_to_root.'/'.$ext['path']."/reporting/reports_custom.php";
				if (file_exists($file)) {
					set_ext_domain($ext['path']);
					include_once($file);
					set_ext_domain();
				}
			}
	}
	$file = company_path()."/reporting/reports_custom.php";
	if (file_exists($file))
		include_once($file);
}


