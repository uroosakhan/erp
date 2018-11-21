<?php

include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");
include_once($path_to_root . "/includes/Mobile_Detect.php"); // dz 1.9.17
include_once($path_to_root . "/admin/db/fiscalyears_db.inc");//ansar

define('SEARCH_BUTTON', "<input %s type='submit' class='combo_submit' style='border:0;background:url($path_to_root/themes/"
	."%s/images/locate.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Set filter")."'> ");

define('SELECT_BUTTON', "<input %s type='submit' class='combo_select' style='border:0;background:url($path_to_root/themes/"
	."%s/images/button_ok.png) no-repeat;%s' aspect='fallback' name='%s' value=' ' title='"._("Select")."'> ");

//----------------------------------------------------------------------------
//	Universal sql combo generator
//	$sql must return selector values and selector texts in columns 0 & 1
//	Options are merged with defaults.

function combo_input($name, $selected_id, $sql, $valfield, $namefield,
	$options=null, $type=null)
{
global $Ajax, $path_to_root, $SysPrefs ;

$opts = array(		// default options
	'where'=> array(),		// additional constraints
	'order' => $namefield,	// list sort order
		// special option parameters
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
		// submit on select parameters
	'default' => '', // default value when $_POST is not set
	'multi' => false,	// multiple select
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
		// search box parameters
	'sel_hint' => null,
	'search_box' => false, 	// name or true/false
	'type' => 0,	// type of extended selector:
		// 0 - with (optional) visible search box, search by fragment inside id
		// 1 - with hidden search box, search by option text
		// 2 - with (optional) visible search box, search by fragment at the start of id
		// 3 - TODO reverse: box with hidden selector available via enter; this
		// would be convenient for optional ad hoc adding of new item
	'search_submit' => true, //search submit button: true/false
	'size' => 8,	// size and max of box tag
	'max' => 50,
	'height' => false,	// number of lines in select box
	'cells' => false,	// combo displayed as 2 <td></td> cells
	'search' => array(), // sql field names to search
	'format' => null, 	 // format functions for regular options
	'disabled' => false,
	'box_hint' => null, // box/selectors hints; null = std see below
	'category' => false, // category column name or false
	'show_inactive' => false, // show inactive records. 
	'editable' => false, // false, or length of editable entry field
	'editlink' => false	// link to entity entry/edit page (optional)
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	if (!is_array($opts['where']))  $opts['where'] = array($opts['where']);

	$search_box = $opts['search_box']===true ? '_'.$name.'_edit' : $opts['search_box'];
	// select content filtered by search field:
	$search_submit = $opts['search_submit']===true ? '_'.$name.'_button' : $opts['search_submit'];
	// select set by select content field
	$search_button = $opts['editable'] ? '_'.$name.'_button' : ($search_box ? $search_submit : false);

	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	if ($opts['type'] == 0) {
		$by_id = true;
		$class = 'combo';
	} elseif($opts['type'] == 1) {
		$by_id = false;
		$class = 'combo2';
	} else {
		$by_id = true;
		$class = 'combo3';
	}

	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];

		$auto_select_box = true;

// Make sure the auto select box for searching not causing trouble.
if ($auto_select_box == true) {
	// Disable auto select box on mobile device because it has problem
	// with the soft keyboard input.
	$detect = new Mobile_Detect();
	if ($detect->isMobile()) {
		$auto_select_box = false;
	}

	// Disable auto select box when the search feature for item, supplier,
	// or customer is turned on.
	if (get_company_pref('no_item_list') !=0 ||
			get_company_pref('no_customer_list') != 0 ||
			get_company_pref('no_supplier_list') != 0) {
		$auto_select_box = false;
	}
}
	//dz 1.9.17
	if($auto_select_box&&!$multi)$class .=' mbselect';

	
	if(!count($opts['search'])) {
		$opts['search'] = array($by_id ? $valfield : $namefield);
	}
	if ($opts['sel_hint'] === null) 
		$opts['sel_hint'] = $by_id || $search_box==false ?
			'' : _('Press Space tab for search pattern entry');

	if ($opts['box_hint'] === null)
		$opts['box_hint'] = $search_box && $search_submit != false ?
			($by_id ? _('Enter code fragment to search or * for all')
			: _('Enter description fragment to search or * for all')) :'';

	if ($selected_id == null) {
		$selected_id = get_post($name, (string)$opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	$txt = get_post($search_box);
	$rel = '';
	$limit = '';
	if (isset($_POST['_'.$name.'_update'])) { // select list or search box change
		if ($by_id) $txt = $_POST[$name];

		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if (isset($_POST[$search_button])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}
	if ($search_box) {
		// search related sql modifications

		$rel = "rel='$search_box'"; // set relation to list
		if ($opts['search_submit']) {
			if (isset($_POST[$search_button])) {
				$selected_id = array(); // ignore selected_id while search
				if (!$opts['async'])
					$Ajax->activate('_page_body');
				else
					$Ajax->activate($name);
			}
			if ($txt == '') {
				if ($spec_option === false && $selected_id == array())
					$limit = ' LIMIT 1';
				else
					$opts['where'][] = $valfield . "=". db_escape(get_post($name, $spec_id));
			}
			else
				if ($txt != '*') {

					foreach($opts['search'] as $i=> $s)
						$opts['search'][$i] = $s . " LIKE "
							.db_escape(($class=='combo3' ? '' : '%').$txt.'%');
					$opts['where'][] = '('. implode($opts['search'], ' OR ') . ')';
				}
		}
	}

	// sql completion
	if (count($opts['where'])) {
		$where = strpos($sql, 'WHERE')==false ? ' WHERE ':' AND ';
		$where .= '('. implode($opts['where'], ' AND ') . ')';
		$group_pos = strpos($sql, 'GROUP BY');
		if ($group_pos) {
			$group = substr($sql, $group_pos);
			$sql = substr($sql, 0, $group_pos) . $where.' '.$group;
		} else {
			$sql .= $where;
		}
	}
	if ($opts['order'] != false) {
		if (!is_array($opts['order']))
			$opts['order'] = array($opts['order']);
		$sql .= ' ORDER BY '.implode(',',$opts['order']);
	}

	$sql .= $limit;
	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
	$lastcat = null;
	$edit = false;
	$pname = false;
	if (($type === "customer" || $type === "supplier") && !empty($SysPrefs->prefs['shortname_name_in_list']))
		$pname = true;
	if($result = db_query($sql)) {
		while ($contact_row = db_fetch($result)) {
			$value = $contact_row[0];
			$descr = $opts['format']==null ?  $contact_row[1] :
				call_user_func($opts['format'], $contact_row, $pname);
			$sel = '';
 		 	if (get_post($search_button) && ($txt == $value)) {
 		 		$selected_id[] = $value;
 		 	}

 		 	if (in_array((string)$value, $selected_id, true)) {
				$sel = 'selected';
				$found = $value;
				$edit = $opts['editable'] && $contact_row['editable'] 
					&& (@$_POST[$search_box] == $value)
					? $contact_row[1] : false; // get non-formatted description
				if ($edit)
					break;	// selected field is editable - abandon list construction
			}
			// show selected option even if inactive 
			if (!$opts['show_inactive'] && @$contact_row['inactive'] && $sel==='') {
				continue;
			} else 
				$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';

			if ($first_id === false) {
				$first_id = $value;
				$first_opt = $descr;
			}
			$cat = $contact_row[$opts['category']];
			if ($opts['category'] !== false && $cat != $lastcat){
				if ($lastcat!==null)
					$selector .= "</optgroup>";
				$selector .= "<optgroup label='".$cat."'>\n";
				$lastcat = $cat;
			}
			$selector .= "<option $sel $optclass value='$value'>$descr</option>\n";
		}
		if ($lastcat!==null)
			$selector .= "</optgroup>";
		db_free_result($result);
	}

	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
		$sel = $found===false ? 'selected' : '';
		$optclass = @$contact_row['inactive'] ? "class='inactive'" : '';
		$selector = "<option $sel value='$first_id'>$first_opt</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	if ($SysPrefs->use_popup_search)
		$selector = "<select style='width:250px;' id='$name' autocomplete='off' ".($multi ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
		. $opts['sel_hint']."' $rel>".$selector."</select>\n";
	else
		$selector = "<select  style='width:250px;' autocomplete='off' ".($multi ? "multiple" : '')
		. ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
		. "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
		. $opts['sel_hint']."' $rel>".$selector."</select>\n";
	if ($by_id && ($search_box != false || $opts['editable']) ) {
		// on first display show selector list
		if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
			$selector = "<input type='hidden' name='$name' value='".$_POST[$name]."'>"
			."<input type='text' $disabled name='{$name}_text' id='{$name}_text' size='".
				$opts['editable']."' maxlength='".$opts['max']."' $rel value='$edit'>\n";
				set_focus($name.'_text'); // prevent lost focus
		} else if (get_post($search_submit ? $search_submit : "_{$name}_button"))
			set_focus($name); // prevent lost focus
		if (!$opts['editable'])
	 		$txt = $found;
		$Ajax->addUpdate($name, $search_box, $txt ? $txt : '');
	}

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	// span for select list/input field update
	$selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

	 // if selectable or editable list is used - add select button
	if ($select_submit != false || $search_button) {
	// button class selects form reload/ajax selector update
		$selector .= sprintf(SELECT_BUTTON, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
// ------ make combo ----------
	$edit_entry = '';
	if ($search_box != false) {
		$edit_entry = "<input $disabled type='text' name='$search_box' id='$search_box' size='".
			$opts['size']."' maxlength='".$opts['max'].
			"' value='$txt' class='$class' rel='$name' autocomplete='off' title='"
			.$opts['box_hint']."'"
			.(!fallback_mode() && !$by_id ? " style=display:none;":'')
			.">\n";
		if ($search_submit != false || $opts['editable']) {
			$edit_entry .= sprintf(SEARCH_BUTTON, $disabled, user_theme(),
				(fallback_mode() ? '' : 'display:none;'),
				$search_submit ? $search_submit : "_{$name}_button")."\n";
		}
	}
	default_focus(($search_box && $by_id) ? $search_box : $name);

	$img = "";
	if ($SysPrefs->use_popup_search && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']))
	{
		$img_title = "";
		$link = "";
  		$id = $name;
  		if ($SysPrefs->use_popup_windows) {
    		switch (strtolower($type)) {
      			case "stock":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=all&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "stock_manufactured":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=manufactured&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "stock_purchased":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=purchasable&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "stock_sales":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=sales&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "stock_costable":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=costable&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "component":
      				$parent = $opts['parent'];
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=component&parent=".$parent."&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "kits":
        			$link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=kits&client_id=" . $id;
        			$img_title = _("Search items");
        			break;
      			case "customer":
        			$link = $path_to_root . "/sales/inquiry/customers_list.php?popup=1&client_id=" . $id;
        			$img_title = _("Search customers");
        			break;
      			case "branch":
        			$link = $path_to_root . "/sales/inquiry/customer_branches_list.php?popup=1&client_id=" . $id . "#customer_id";
        			$img_title = _("Search branches");
        			break;
      			case "supplier":
        			$link = $path_to_root . "/purchasing/inquiry/suppliers_list.php?popup=1&client_id=" . $id;
        			$img_title = _("Search suppliers");
        			break;
      			case "account":
        			$link = $path_to_root . "/gl/inquiry/accounts_list.php?popup=1&client_id=" . $id;
        			$img_title = _("Search GL accounts");
        			break;
        		case "sales_order_ref":
                    $link = $path_to_root . "/sales/inquiry/saleorder_list.php?popup=1&client_id=" . $id;
                    $img_title = _("Search Sales Order");
                    break;
    		}
  		}

  		if ($link !=="") {
    		$theme = user_theme();
    		$img = '<img src="'.$path_to_root.'/themes/'.$theme.'/images/'.ICON_VIEW.
    			'" style="vertical-align:middle;width:12px;height:12px;border:0;" onclick="javascript:lookupWindow(&quot;'.
    			$link.'&quot;, &quot;&quot;);" title="' . $img_title . '" style="cursor:pointer;" />';
  		}
	}

	if ($opts['editlink'])
		$selector .= ' '.$opts['editlink'];

	if ($search_box && $opts['cells'])
		$str = ($edit_entry!='' ? "<td>$edit_entry</td>" : '')."<td>$selector$img</td>";
	else
		$str = $edit_entry.$selector.$img;
	return $str;
	
}

/*
	Helper function.
	Returns true if selector $name is subject to update.
*/


function get_combo1($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo1 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}

function get_combo2($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo2 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}

function get_combo3($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo3 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}


function get_combo4($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo4 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}

function get_combo5($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo5 WHERE combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}

function get_combo6($selected_id)
{
	$sql = "SELECT description FROM ".TB_PREF."combo6 WHERE
	combo_code=".db_escape($selected_id);

	$result = db_query($sql,"could not get combo");
	$row= db_fetch_row($result);

    return $row[0];
    
}












function list_updated($name)
{
	return isset($_POST['_'.$name.'_update']) || isset($_POST['_'.$name.'_button']);
}

function get_taxgroup_id_name($id)
{
  $sql = "SELECT name FROM ".TB_PREF."tax_groups WHERE id = ".db_escape($id)."";
    $result = db_query($sql, 'Error');
    $fetch = db_fetch_row($result);
    return $fetch[0];
}
///////////////////////hareeem 
function yesno_list_logo($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("No");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_list_cells_logo($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_list_logo($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_list_row_logo($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_list_cells_logo(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}
function combo_ph_1_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."ph_combo1";



	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_ph_1_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_ph_1_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}



function combo_ph_2_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."ph_combo2";



	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_ph_2_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_ph_2_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}
function combo_ph_3_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."ph_combo3";


	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_ph_3_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_ph_3_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}




function combo_supp_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
    $sql = "SELECT supp_code ,description, inactive FROM ".TB_PREF."supp_category";

    return combo_input($name, $selected_id, $sql, 'supp_code', 'description',
        array(
            'select_submit'=> $submit_on_change,
            'async' => true
        ) );

}

function combo_supp_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=false, $prepayments=true)
{
    if ($label != null)
        echo "<td class=''>$label</td>\n";
    echo "<td>";

    echo combo_supp_list($name, $category, $selected_id, $submit_on_change, $prepayments);

    echo "</td>\n";
}
function gl_all_gate_pass_accounts_list($label,$name, $selected_id=null, $skip_bank_accounts=false,
                              $cells=false, $all_option=false, $submit_on_change=false, $all=false)
{
    if ($skip_bank_accounts)
        $sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
			FROM (".TB_PREF."chart_master chart,".TB_PREF."chart_types type) "
            ."LEFT JOIN ".TB_PREF."bank_accounts acc "
            ."ON chart.account_code=acc.account_code
				WHERE acc.account_code  IS NULL
			AND chart.account_type=type.id";
    else
        $sql = "SELECT chart.account_code, chart.account_name, CONCAT(type.id, ' - ' ,type.name), chart.inactive, type.id
			FROM ".TB_PREF."chart_master chart,".TB_PREF."chart_types type
			WHERE chart.account_type=type.id";

    return combo_input($name, $selected_id, $sql, 'chart.account_code', 'chart.account_name',
        array(
            'format' => '_format_account',
            'spec_option' => $all_option===true ?  _("Use Item Sales Accounts") : $all_option,
            'spec_id' => '',
            'type' => 2,
            'order' => array('type.class_id','type.id','account_code'),
            'search_box' => $cells,
            'search_submit' => false,
            'size' => 12,
            'max' => 10,
            'cells' => true,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'category' => 2,
            'show_inactive' => $all
        ), "account" );
}

function _format_gate_pass_account($row)
{
    return $row[0] .  "&nbsp;&nbsp;&nbsp;&nbsp;" . $row[1];
}

function gl_all_gate_pass_accounts_list_cells($label, $name, $selected_id=null,
                                    $skip_bank_accounts=false, $cells=false, $all_option=false,
                                    $submit_on_change=false, $all=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo gl_all_gate_pass_accounts_list($label,$name, $selected_id,
        $skip_bank_accounts, $cells, $all_option, $submit_on_change, $all);
    echo "</td>\n";
}

function gl_all_gate_pass_accounts_list_row($label, $name, $selected_id=null,
                                  $skip_bank_accounts=false, $cells=false, $all_option=false)
{
    echo "<tr><td class=''>$label</td>";
    gl_all_gate_pass_accounts_list_cells(null, $name, $selected_id,
        $skip_bank_accounts, $cells, $all_option);
    echo "</tr>\n";
}

function gate_pass_list($name, $selected_id=null, $all_option=false)
{
    $sql = "SELECT gate_pass_no , gate_pass_no FROM ".TB_PREF."multiple_gate_pass GROUP BY gate_pass_no";
    return combo_input($name, $selected_id, $sql, 'gate_pass_no', 'gate_pass_no',
        array('order'=>array('gate_pass_no'),
        'spec_option' =>$all_option===true ? _("Select Gate Pass") : $all_option,
        'spec_id' => ALL_NUMERIC));
}

function gate_pass_cells($label, $name, $selected_id=null, $all_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo gate_pass_list($name, $selected_id, $all_option);
    echo "</td>\n";
}

function gate_pass_row($label, $name, $selected_id=null, $all_option=false)
{
    echo "<tr><td class='label'>$label</td>";
    gate_pass_cells(null, $name, $selected_id, $all_option);
    echo "</tr>\n";
}

//---------------------------------------------------------------------------------------------------------
function daily_movement_type_list_cells($label, $name, $value=null, $submit_on_change=false)
{
    global $systypes_array;

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";

    $items = $systypes_array;

    // exclude quotes, orders and dimensions

    foreach (array(ST_PURCHORDER,ST_CPV ,
                 ST_CRV, ST_WORKORDER, ST_SALESORDER, ST_DIMENSION,
                 ST_SALESQUOTE, ST_JOURNAL, ST_BANKPAYMENT, ST_BANKDEPOSIT, ST_BANKTRANSFER, ST_SALESINVOICE, // ST_CUSTCREDIT,
                 ST_CUSTPAYMENT, ST_SUPPINVOICE, ST_SUPPCREDIT,ST_SUPPCREDIT_IMPORT, ST_SUPPAYMENT, ST_COSTUPDATE) as $excl)
        unset($items[$excl]);

    echo array_selector($name, $value, $items,
        array(
            'spec_option'=> _("All"),
            'spec_id' => -1,
            'select_submit'=> $submit_on_change,
            'async' => false
        )
    );
    echo "</td>\n";
}
//----------------------------------------------------------------------------------------------
//	Universal array combo generator
//	$items is array of options 'value' => 'description'
//	Options is reduced set of combo_selector options and is merged with defaults.

function array_selector($name, $selected_id, $items, $options=null,$onchange=null )
{
	global $Ajax;

$opts = array(		// default options
	'spec_option'=>false, 	// option text or false
	'spec_id' => 0,		// option id
	'select_submit' => false, //submit on select: true/false
	'async' => true,	// select update via ajax (true) vs _page_body reload
	'default' => '', // default value when $_POST is not set
	'multi'=>false,	// multiple select
		// search box parameters
	'height' => false,	// number of lines in select box
	'sel_hint' => null,
	'disabled' => false
);
// ------ merge options with defaults ----------
	if($options != null)
		$opts = array_merge($opts, $options);
	$select_submit =  $opts['select_submit'];
	$spec_id = $opts['spec_id'];
	$spec_option = $opts['spec_option'];
	$disabled = $opts['disabled'] ? "disabled" : '';
	$multi = $opts['multi'];

	if ($selected_id == null) {
		$selected_id = get_post($name, $opts['default']);
	}
	if(!is_array($selected_id))
		$selected_id = array((string)$selected_id); // code is generalized for multiple selection support

	if (isset($_POST[ '_'.$name.'_update'])) {
		if (!$opts['async'])
			$Ajax->activate('_page_body');
		else
			$Ajax->activate($name);
	}

	// ------ make selector ----------
	$selector = $first_opt = '';
	$first_id = false;
	$found = false;
	foreach($items as $value=>$descr) {
		$sel = '';
		if (in_array((string)$value, $selected_id, true)) {
			$sel = 'selected';
			$found = $value;
		}
		if ($first_id === false) {
			$first_id = $value;
			$first_opt = $descr;
		}
		$selector .= "<option $sel value='$value'>$descr</option>\n";
	}

	if ($first_id!==false) {
		$sel = ($found===$first_id) || ($found===false && ($spec_option===false)) ? "selected='selected'" : '';
	}
	// Prepend special option.
	if ($spec_option !== false) { // if special option used - add it
		$first_id = $spec_id;
		$first_opt = $spec_option;
		$sel = $found===false ? 'selected' : '';
		$selector = "<option $sel value='$spec_id'>$spec_option</option>\n"
			. $selector;
	}

	if ($found===false) {
		$selected_id = array($first_id);
	}
	$_POST[$name] = $multi ? $selected_id : $selected_id[0];

	//dz 1.9.17
	if($auto_select_box&&!$multi)$class ='combo mbselect';else $class='combo';
	


  if($onchange ==null)
        $class = "class='combo'";
    else
        $class = " ";
    $selector = "<select $onchange autocomplete='off' ".($multi  ? "multiple" : '')
        . ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
        . "$disabled  id='$name'  name='$name".($multi ? '[]' : '')."' $class title='"
        . $opts['sel_hint']."'>".$selector."</select>\n";

	$Ajax->addUpdate($name, "_{$name}_sel", $selector);

	$selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

	if ($select_submit != false) { // if submit on change is used - add select button
		$selector .= sprintf(SELECT_BUTTON, $disabled, user_theme(),
			(fallback_mode() ? '' : 'display:none;'),
			 '_'.$name.'_update')."\n";
	}
	default_focus($name);

	return $selector;
}
//----------------------------------------------------------------------------------------------
function array_selector_row($label, $name, $selected_id, $items, $options=null)
{
	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector($name, $selected_id, $items, $options);
	echo "</td></tr>\n";
}

//----------------------------------------------------------------------------------------------
function report_profiles_list_row($label, $name, $selected_id=null, $spec_opt=false,
								  $submit_on_change=true)
{
	$sql = "SELECT profile FROM ".TB_PREF."report_profiles"
		." GROUP BY profile";
	$result = db_query($sql, 'cannot get all profile names');
	$profiles = array();
	while($myrow=db_fetch($result)) {
		$profiles[$myrow['profile']] = $myrow['profile'];
	}

	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $profiles,
		array( 'select_submit'=> $submit_on_change,
			'spec_option'=>$spec_opt,
			'spec_id' => ''
		));

	echo "</td></tr>\n";
}

function _format_add_curr($row, $pname=false)
{
	static $company_currency;

	if ($company_currency == null)
	{
		$company_currency = get_company_currency();
	}
	if (!$pname)
	{
		return $row[1] . ($row[2] == $company_currency ? '' : ("&nbsp;-&nbsp;" . $row[2]));
	}		
	else
	{
		return $row[1] . "&nbsp;-&nbsp;" . $row[2] . ($row[3] == $company_currency ? '' : ("&nbsp;-&nbsp;" . $row[3]));
	}	
}

function add_edit_combo($type)
{
	global $path_to_root, $popup_editors, $SysPrefs;

	if (!isset($SysPrefs->use_icon_for_editkey) || $SysPrefs->use_icon_for_editkey==0)
		return "";
	// Derive theme path
	$theme_path = $path_to_root . '/themes/' . user_theme();

	$key = $popup_editors[$type][1];
	$onclick = "onclick=\"javascript:callEditor($key); return false;\"";
	$img = "<img width='12' height='12' border='0' alt='Add/Edit' title='Add/Edit' src='$theme_path/images/".ICON_EDIT."'>";
	return "<a target = '_blank' href='#' $onclick tabindex='-1'>$img</a>"; 
}

function _format_add_branch ($row, $pname=false)
{
$name=    get_taxgroup_id_name($row[2]);
	
		return $row[1]."-&nbsp;&nbsp;&nbsp;".$name ;

}
function _format_sales_order ($row, $pname=false)
{
	static $company_currency;

//	if ($company_currency == null)
//	{
//		$company_currency = get_company_currency();
//	}
//	if (!$pname)
//	{
		return $row[0] . ($row[1] == $company_currency ? '' : ("&nbsp;-&nbsp;" . $row[1]));
//	}
//	else
//	{
//		return $row[1] . "&nbsp;-&nbsp;" . $row[2] . ($row[1] == $company_currency ? '' : ("&nbsp;-&nbsp;" . $row[2]));
//	}
}
/*
function supplier_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
    global $SysPrefs;
    if (!empty($SysPrefs->prefs['shortname_name_in_list']))
		$sql = "SELECT supplier_id, supp_ref, supp_name, curr_code, inactive FROM ".TB_PREF."suppliers ";
	else	
		$sql = "SELECT supplier_id, supp_ref, curr_code, inactive FROM ".TB_PREF."suppliers ";

	$mode = get_company_pref('no_supplier_list');

	if ($editkey)
		set_editor('supplier', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'supplier_id', 'supp_name',
	array(
		'format' => '_format_add_curr',
	    'order' => array('supp_ref'),
		'search_box' => $mode!=0,
		'type' => 1,
        'search' => array("supp_ref","supp_name","gst_no"),        
		'spec_option' => $spec_option === true ? _("All Suppliers") : $spec_option,
		'spec_id' => ALL_TEXT,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select supplier'),
		'show_inactive'=>$all,
		'editlink' => $editkey ? add_edit_combo('supplier') : false
		), "supplier");
	return $ret;
}

function supplier_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo supplier_list($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function supplier_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class=''>$label</td><td>";
	echo supplier_list($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}*/
function supp_category_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
    {
        $sql = "SELECT supp_code ,description, inactive FROM ".TB_PREF."supp_category";

        return combo_input($name, $selected_id, $sql, 'supp_code', 'description',
            array(
                'spec_option' => $special_option===true ? _("All Sales Types") : $special_option,
                'spec_id' => 0,
                'async' => false,
                'select_submit'=> $submit_on_change,
            ) );
    }

    function supp_category_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
    {
        if ($label != null)
            echo "<td>$label</td>\n";
        echo "<td>";
        echo supp_category_list($name, $selected_id, $submit_on_change, $special_option);
        echo "</td>\n";
    }

    function supp_category_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
    {
        echo "<tr><td class=''>$label</td>";
        supp_category_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
        echo "</tr>\n";
    }
function supplier_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false,$combo1)
{

  global $SysPrefs , $leftmenu_save, $db_connections;
    if (!empty($SysPrefs->prefs['shortname_name_in_list']))
    {

		$sql = "SELECT supplier_id, supp_ref, supp_name, curr_code, inactive FROM ".TB_PREF."suppliers ";
        if($combo1!=0)
            $sql .= " WHERE combo1=".db_escape($combo1);
    }
	else
    {
        $sql = "SELECT supp.supplier_id, supp.supp_ref, supp.curr_code ,supp.inactive FROM ".TB_PREF."suppliers supp
       
        ";

        if( $combo1 != -1 && $combo1 != '') {

            $sql .= " 
             LEFT JOIN ".TB_PREF."suppliers_items si ON supp.supplier_id = si.supplier
             WHERE   si.stock_id =" . db_escape($combo1);



        }
        }
    $mode = get_company_pref('no_supplier_list');

    if ($editkey)
        set_editor('supplier', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'supplier_id', 'supp_name',
        array(
            'format' => '_format_add_curr',
            'order' => array('supp_ref'),
            'search_box' => $mode!=0,
            'type' => 1,
            'search' => array("supp_ref","supp_name","gst_no"),
            'spec_option' => $spec_option === true ? _("All Suppliers") : $spec_option,
            'spec_id' => ALL_TEXT,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select supplier'),
            'show_inactive'=>$all,
            'editlink' => $editkey ? add_edit_combo('supplier') : false
        ), "supplier");
    return $ret;
}


function supplier_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo supplier_list($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function supplier_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false,$combo1)
{

	echo "<tr><td class=''>$label</td><td>";
	echo supplier_list($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey,$combo1);
	echo "</td></tr>\n";
}


//----------------------------------------------------------------------------------------------
////only use for AAT company
function customer_list1($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{

    global $SysPrefs,$db_connections;
     if (!empty($SysPrefs->prefs['user_cust']))
    {
    $current_user = $_SESSION["wa_current_user"]->user;
	if (!empty($SysPrefs->prefs['shortname_name_in_list']))
			$sql="SELECT ".TB_PREF."debtors_master.debtor_no, ".TB_PREF."debtors_master.debtor_ref, ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."debtors_master.inactive FROM ".TB_PREF."debtors_master
INNER JOIN ".TB_PREF."cust_branch ON ".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no
INNER JOIN ".TB_PREF."user_locations ON  ".TB_PREF."cust_branch.default_location=".TB_PREF."user_locations.loc_code
WHERE ".TB_PREF."user_locations.user_id=$current_user";
	else	
		$sql="SELECT ".TB_PREF."debtors_master.debtor_no,  ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."debtors_master.inactive FROM ".TB_PREF."debtors_master
INNER JOIN ".TB_PREF."cust_branch ON ".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no
INNER JOIN ".TB_PREF."user_locations ON  ".TB_PREF."cust_branch.default_location=".TB_PREF."user_locations.loc_code
WHERE ".TB_PREF."user_locations.user_id=$current_user";
}
else
{
	if (!empty($SysPrefs->prefs['shortname_name_in_list']))
	{
 $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive 
        FROM ".TB_PREF."debtors_master ";
        }
        else
        {
        $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive 
        FROM ".TB_PREF."debtors_master ";
        }
}

	$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('customer', $name, $editkey);
        if($mode == 1){
            $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
            array(
                'format' => '_format_add_curr',
                'order' => array('debtor_ref'),
                'search_box' => $mode != 0,
                'type' => 2,
                'size' => 20,
                'search' => array("debtor_ref", "name", "debtor_no", "tax_id"),
                //'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
                'spec_id' => ALL_TEXT,
                'select_submit' => $submit_on_change,
                'async' => false,
                'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                    _('Select customer'),
                'show_inactive' => $show_inactive,
                'editlink' => $editkey ? add_edit_combo('customer') : false
            ), "customer");
        }
        else
        {
            
            $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
            array(
                'format' => '_format_add_curr',
                'order' => array('debtor_ref'),
                'search_box' => $mode != 0,
                'type' => 2,
                'size' => 20,
                'search' => array("debtor_ref", "name", "tax_id"),
                //'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
                'spec_id' => ALL_TEXT,
                'select_submit' => $submit_on_change,
                'async' => false,
                'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                    _('Select customer'),
                'show_inactive' => $show_inactive,
                'editlink' => $editkey ? add_edit_combo('customer') : false
            ), "customer");
        }
            
        return $ret;
}
function customer_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{

    global $SysPrefs,$db_connections;
     if (!empty($SysPrefs->prefs['user_cust']))
    {
    $current_user = $_SESSION["wa_current_user"]->user;
	if (!empty($SysPrefs->prefs['shortname_name_in_list']))
			$sql="SELECT ".TB_PREF."debtors_master.debtor_no, ".TB_PREF."debtors_master.debtor_ref, ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."debtors_master.inactive FROM ".TB_PREF."debtors_master
INNER JOIN ".TB_PREF."cust_branch ON ".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no
INNER JOIN ".TB_PREF."user_locations ON  ".TB_PREF."cust_branch.default_location=".TB_PREF."user_locations.loc_code
WHERE ".TB_PREF."user_locations.user_id=$current_user";
	else	
		$sql="SELECT ".TB_PREF."debtors_master.debtor_no,  ".TB_PREF."debtors_master.name, ".TB_PREF."debtors_master.curr_code, ".TB_PREF."debtors_master.inactive FROM ".TB_PREF."debtors_master
INNER JOIN ".TB_PREF."cust_branch ON ".TB_PREF."cust_branch.debtor_no=".TB_PREF."debtors_master.debtor_no
INNER JOIN ".TB_PREF."user_locations ON  ".TB_PREF."cust_branch.default_location=".TB_PREF."user_locations.loc_code
WHERE ".TB_PREF."user_locations.user_id=$current_user";
}
else
{
	if (!empty($SysPrefs->prefs['shortname_name_in_list']))
	{
 $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive 
        FROM ".TB_PREF."debtors_master ";
        }
        else
        {
        $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive 
        FROM ".TB_PREF."debtors_master ";
        }
}

	$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('customer', $name, $editkey);
        if($mode == 1){
            $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
            array(
                'format' => '_format_add_curr',
                'order' => array('debtor_ref'),
                'search_box' => $mode != 0,
                'type' => 2,
                'size' => 20,
                'search' => array("debtor_ref", "name", "debtor_no", "tax_id"),
                'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
                'spec_id' => ALL_TEXT,
                'select_submit' => $submit_on_change,
                'async' => false,
                'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                    _('Select customer'),
                'show_inactive' => $show_inactive,
                'editlink' => $editkey ? add_edit_combo('customer') : false
            ), "customer");
        }
        else
        {
            
            $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
            array(
                'format' => '_format_add_curr',
                'order' => array('debtor_ref'),
                'search_box' => $mode != 0,
                'type' => 2,
                'size' => 20,
                'search' => array("debtor_ref", "name", "tax_id"),
                'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
                'spec_id' => ALL_TEXT,
                'select_submit' => $submit_on_change,
                'async' => false,
                'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                    _('Select customer'),
                'show_inactive' => $show_inactive,
                'editlink' => $editkey ? add_edit_combo('customer') : false
            ), "customer");
        }
            
        return $ret;
}

function customer_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo customer_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function customer_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{

	echo "<tr><td class=''>$label</td><td nowrap>";
	echo customer_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}

//------------------------------------------------------------------------------------------------

function customer_branches_list($customer_id, $name, $selected_id=null,
	$spec_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{

	$sql = "SELECT branch_code, branch_ref,tax_group_id  FROM ".TB_PREF."cust_branch
		WHERE debtor_no=" . db_escape($customer_id)." ";

	if ($editkey)
		set_editor('branch', $name, $editkey);

	$where = $enabled ? array("inactive = 0") : array();
	$ret = combo_input($name, $selected_id, $sql, 'branch_code', 'branch_ref',
	array(
	    'format' => '_format_add_branch',
		'where' => $where,
		'order' => array('branch_ref'),
		'spec_option' => $spec_option === true ? _('All branches') : $spec_option,
		'spec_id' => ALL_TEXT,
		'select_submit'=> $submit_on_change,
		'sel_hint' => _('Select customer branch'),
		'editlink' => $editkey ? add_edit_combo('branch') : false
	), "branch" );
	return $ret;
}
//------------------------------------------------------------------------------------------------

function combo_item_1_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_1";


	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_1_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<tr><td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_1_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td></tr>\n";
}

function combo_item_2_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_2";


	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_2_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<tr><td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_2_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td></tr>\n";
}


function combo_item_3_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_3";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_3_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_2_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}

function combo_item_4_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_4";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_4_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<tr><td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_4_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td></tr>\n";
}


function combo_item_5_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_5";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_5_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_5_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}


function combo_item_6_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_so_6";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_item_6_list_row($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<tr><td class=''>$label</td>\n";
	echo "<td>";

	echo combo_item_6_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td></tr>\n";
}


function customer_branches_list_cells($label,$customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo customer_branches_list($customer_id, $name, $selected_id, $all_option, $enabled, 
		$submit_on_change, $editkey);
	echo "</td>\n";
}

function customer_branches_list_row($label, $customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	echo "<tr><td class=''>$label</td>";
	customer_branches_list_cells(null, $customer_id, $name, $selected_id, 
		$all_option, $enabled, $submit_on_change, $editkey);
	echo "</tr>";
}

//------------------------------------------------------------------------------------------------

function locations_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	global $all_items;
//	$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations WHERE fixed_asset=".(int)$fixed_asset;

	$current_user = $_SESSION["wa_current_user"]->user;

	$sql = "SELECT loc.loc_code, loc.location_name, loc.inactive FROM 
	 ".TB_PREF."locations AS loc, 
	 ".TB_PREF."user_locations user_loc WHERE loc.loc_code=user_loc.loc_code AND 
	 user_loc.user_id='".$current_user."'";

	//$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations";

	return combo_input($name, $selected_id, $sql, 'loc_code', 'location_name',
		array(
			'spec_option' => $all_option === true ? _("All Locations") : $all_option,
			'spec_id' => ALL_TEXT,
            'order' => 'loc_code',
			'select_submit'=> $submit_on_change
		) );
}

function locations_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo locations_list($name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
	echo "</td>\n";
}

function locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	echo "<tr><td class=''>$label</td>";
	locations_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
	echo "</tr>\n";
}
////////////////////For location to
function locations_to_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	global $all_items;
//	$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations WHERE fixed_asset=".(int)$fixed_asset;

// 	$current_user = $_SESSION["wa_current_user"]->user;

// 	$sql = "SELECT loc.loc_code, loc.location_name, loc.inactive FROM 
// 	 ".TB_PREF."locations AS loc, 
// 	 ".TB_PREF."user_locations user_loc WHERE loc.loc_code=user_loc.loc_code AND 
// 	 user_loc.user_id='".$current_user."'";

	$sql = "SELECT loc_code, location_name, inactive FROM ".TB_PREF."locations";

	return combo_input($name, $selected_id, $sql, 'loc_code', 'location_name',
		array(
			'spec_option' => $all_option === true ? _("All Locations") : $all_option,
			'spec_id' => ALL_TEXT,
            'order' => 'loc_code',
			'select_submit'=> $submit_on_change
		) );
}

function locations_to_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo locations_to_list($name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
	echo "</td>\n";
}

function locations_to_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
	echo "<tr><td class=''>$label</td>";
	locations_to_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
	echo "</tr>\n";
}


//-----------------------------------------------------------------------------------------------

function currencies_list($name, $selected_id=null, $submit_on_change=false, $exclude_home_curr=false)
{
    
        $sql = "SELECT curr_abrev,CONCAT(currency,'--',curr_symbol) as ref, currency, inactive FROM " . TB_PREF . "currencies
	";
    
    
	if ($exclude_home_curr)
		$sql .= " WHERE curr_abrev!='".get_company_currency()."'";
    
        // default to the company currency
        return combo_input($name, $selected_id, $sql, 'curr_abrev', 'ref',
            array(
                'select_submit' => $submit_on_change,
                'default' => get_company_currency(),
                'async' => false
            ));
   
}

function currencies_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo currencies_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function currencies_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	currencies_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}

//---------------------------------------------------------------------------------------------------

function fiscalyears_list($name, $selected_id=null, $submit_on_change=false)
{

	$sql = "SELECT * FROM ".TB_PREF."fiscal_year";

	// default to the company current fiscal year

	return combo_input($name, $selected_id, $sql, 'id', '',
		array(
			'order' => 'begin',
			'default' => get_company_pref('f_year'),
			'format' => '_format_fiscalyears',
			'select_submit'=> $submit_on_change,
			'async' => false
		) );
}

function _format_fiscalyears($row)
{
	return sql2date($row[1]) . "&nbsp;-&nbsp;" . sql2date($row[2])
	. "&nbsp;&nbsp;" . ($row[3] ? _('Closed') : _('Active'));
}

function fiscalyears_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo fiscalyears_list($name, $selected_id);
	echo "</td>\n";
}

function fiscalyears_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	fiscalyears_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}
//------------------------------------------------------------------------------------

global $leftmenu_save, $db_connections;
if($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'PETPAL') {
	function dimensions_list($name, $selected_id = null, $spec_option = false,
							 $submit_on_change = false, $showclosed = false, $showtype = 1)
	{
		$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM " . TB_PREF . "dimensions";

		$options = array(
			'order' => 'reference',
			'spec_option' => $spec_option === true ? _("Select Dimensions") : $spec_option,
	//	'spec_option'=>$no_option ? $showname : false,
			'spec_id' => 0,
			'select_submit' => $submit_on_change,
			'async' => false,
		);

		if (!$showclosed)
			$options['where'][] = "closed=0";
		if ($showtype)
			$options['where'][] = "type_=" . db_escape($showtype);

		return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
	}

	function dimensions_list_cells($label, $name, $selected_id = null, $spec_option = false,
								   $showclosed = false, $submit_on_change = true, $showtype = 0)
	{
		if ($label != null)
			echo "<td>$label</td>\n";
		echo "<td>";
		echo dimensions_list($name, $selected_id, $spec_option, $submit_on_change, $showclosed, $showtype);
		echo "</td>\n";
	}

	function dimensions_list_row($label, $name, $selected_id = null, $spec_option = false,
								 $showclosed = false, $showtype = 0, $submit_on_change = false)
	{
		echo "<tr><td class=''>$label</td>";
		dimensions_list_cells(null, $name, $selected_id, $spec_option,
			$showclosed, $showtype, $submit_on_change);
		echo "</tr>\n";
	}
}
elseif($db_connections[$_SESSION["wa_current_user"]->company]["name"] == 'CB') {
	function dimensions_list($name, $selected_id = null, $spec_option = false,
							 $submit_on_change = false, $showclosed = false, $showtype = 1)
	{
		$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM " . TB_PREF . "dimensions";

		$options = array(
			'order' => 'reference',
			'spec_option' => $spec_option === true ? _("Select location") : $spec_option,
	//	'spec_option'=>$no_option ? $showname : false,
			'spec_id' => 0,
			'select_submit' => $submit_on_change,
			'async' => false,
		);

		if (!$showclosed)
			$options['where'][] = "closed=0";
		if ($showtype)
			$options['where'][] = "type_=" . db_escape($showtype);

		return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
	}

	function dimensions_list_cells($label, $name, $selected_id = null, $spec_option = false,
								   $showclosed = false, $submit_on_change = true, $showtype = 0)
	{
		if ($label != null)
			echo "<td>$label</td>\n";
		echo "<td>";
		echo dimensions_list($name, $selected_id, $spec_option, $submit_on_change, $showclosed, $showtype);
		echo "</td>\n";
	}

	function dimensions_list_row($label, $name, $selected_id = null, $spec_option = false,
								 $showclosed = false, $showtype = 0, $submit_on_change = false)
	{
		echo "<tr><td class=''>$label</td>";
		dimensions_list_cells(null, $name, $selected_id, $spec_option,
			$showclosed, $showtype, $submit_on_change);
		echo "</tr>\n";
	}
}
else
{                           
	function dimensions_list($name, $selected_id = null, $spec_option = false, $submit_on_change = false, $showclosed = false, $showtype = 1)
	{
	     global $SysPrefs;
	    if (!empty($SysPrefs->prefs['enable_userwise_dim_restrict']))
    {
    $current_user = $_SESSION["wa_current_user"]->user;
  $sql = " SELECT ".TB_PREF."dimensions.id, CONCAT(".TB_PREF."dimensions.reference,' ',".TB_PREF."dimensions.name) as ref 
FROM ".TB_PREF."dimensions , ".TB_PREF."user_dim
WHERE ".TB_PREF."dimensions.id=".TB_PREF."user_dim.dim_id
AND ".TB_PREF."user_dim.user_id=$current_user ";
    }
	else
	{
		$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM " . TB_PREF . "dimensions";
	}

		$options = array(
			'order' => 'reference',
			'spec_option' => $spec_option === true ? _("Select Dimensions") : $spec_option,
//		'spec_option'=>$no_option ? $showname : false,
			'spec_id' => 0,
			'select_submit' => $submit_on_change,
			'async' => false,
		);

		if (!$showclosed)
			$options['where'][] = "closed=0";
		if ($showtype)
			$options['where'][] = "type_=" . db_escape($showtype);

		return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
	}

	function dimensions_list_cells($label, $name, $selected_id = null, $spec_option = false,
								   $showclosed = false, $submit_on_change = true, $showtype = 0)
	{
		if ($label != null)
			echo "<td>$label</td>\n";
		echo "<td>";
		echo dimensions_list($name, $selected_id, $spec_option, $submit_on_change, $showclosed, $showtype);
		echo "</td>\n";
	}

	function dimensions_list_row($label, $name, $selected_id = null, $spec_option = false,
								 $showclosed = false, $submit_on_change = false, $showtype = 0)
	{
		echo "<tr><td class=''>$label</td>";
		dimensions_list_cells(null, $name, $selected_id, $spec_option,
			$showclosed, $submit_on_change, $showtype);
		echo "</tr>\n";
	}
}

//---------------------------------------------------------------------------------------------------

//function stock_items_list($name, $selected_id=null, $all_option=false,
//	$submit_on_change=false, $opts=array(), $editkey = false, $type = "stock")
//{
//	$sql = "SELECT s.stock_id, s.description, c.description, s.inactive, s.editable
//			FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c WHERE s.category_id=c.category_id";
//
//	if (isset($opts['fixed_asset']) && $opts['fixed_asset'])
//		$sql .= " AND mb_flag='F'";
//	else
//		$sql .= " AND mb_flag!='F'";
//
//	if ($editkey)
//		set_editor('item', $name, $editkey);
//
//	$ret = combo_input($name, $selected_id, $sql, 's.stock_id', 's.description',
//		array_merge(
//		  array(
//			'format' => '_format_stock_items',
//			'spec_option' => $all_option===true ?  _("All Items") : $all_option,
//			'spec_id' => ALL_TEXT,
//			'search_box' => true,
//			'search' => array("stock_id", "c.description","s.description"),
//			'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
//			'size'=>10,
//			'select_submit'=> $submit_on_change,
//			'category' => 2,
//			'order' => array('c.description','s.stock_id'),
//			'editlink' => $editkey ? add_edit_combo('item') : false,
//			'editable' => false,
//			'max' => 255
//		  ), $opts), $type );
//	return $ret;
//}
function stock_items_list($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $opts=array(), $editkey = false, $type = "stock",$purchase)
{

global $db_connections;
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='KMISTRY') {
      
        $sql = "SELECT stock_id,CONCAT(s.description,'--',i.name) as items, s.description, c.description, s.inactive, s.editable, s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6  ,s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6
			FROM " . TB_PREF . "stock_master s," . TB_PREF . "item_units i," . TB_PREF . "stock_category c 
			WHERE s.category_id=c.category_id
			AND s.units = i.abbr
			";
    }
    else {
       
        $sql = "SELECT stock_id, s.description, c.description, s.inactive,
        s.editable,  s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6 
        ,s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6
			FROM " . TB_PREF . "stock_master s," . TB_PREF . "stock_category c WHERE s.category_id=c.category_id";

    }
    if (isset($opts['fixed_asset']) && $opts['fixed_asset'])
        $sql .= " AND mb_flag='F'";
    else
        $sql .= " AND mb_flag!='F'";

    if ($editkey)
        set_editor('item', $name, $editkey);
        
        
        if($purchase)
        $sql .= " AND !s.no_purchase  ";
    
    if($db_connections[$_SESSION["wa_current_user"]->company]["name"]=='KMISTRY') {
    $ret = combo_input($name, $selected_id, $sql, 'stock_id', 'items',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("stock_id", "c.description","s.description","s.text4","s.text1","s.text3",
                "s.text2","s.text5","s.text6"),
                'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
                'size'=>10,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','stock_id'),
                'editlink' => $editkey ? add_edit_combo('item') : false,
                'editable' => false,
                'max' => 255
            ), $opts), $type );}
            else{
                $ret = combo_input($name, $selected_id, $sql, 'stock_id', 's.description',
                    array_merge(
                        array(
                            'format' => '_format_stock_items',
                            'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                            'spec_id' => ALL_TEXT,
                            'search_box' => true,
                            'search' => array("stock_id", "c.description","s.description","s.text4","s.text1","s.text3",
                "s.text2","s.text5","s.text6"),
                            'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
                            'size'=>10,
                            'select_submit'=> $submit_on_change,
                            'category' => 2,
                            'order' => array('c.description','stock_id'),
                            'editlink' => $editkey ? add_edit_combo('item') : false,
                            'editable' => false,
                            'max' => 255
                        ), $opts), $type == 'kits' ? $type : "stock_sales"   );
            }
    return $ret;
}


function fixed_assets_list($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $opts=array(), $editkey = false, $type = "stock")
{
    $sql = "SELECT stock_id, s.description, c.description, s.inactive, 
    s.editable,s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6 
    ,s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6
    
    
    FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c WHERE s.category_id=c.category_id";

//    if (isset($opts['fixed_asset']) && $opts['fixed_asset'])
    $sql .= " AND mb_flag='F'";
//    else
//        $sql .= " AND mb_flag!='F'";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'stock_id', 's.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("stock_id", "c.description","s.description","s.text4","s.text1","s.text3",
                "s.text2","s.text5","s.text6"),
                'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
                'size'=>10,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','stock_id'),
                'editlink' => $editkey ? add_edit_combo('item') : false,
                'editable' => false,
                'max' => 255
            ), $opts), $type );
    return $ret;
}

       



function _format_stock_items($row)
{
    
        $text_1 = get_company_item_pref('text1');
        $text_2 = get_company_item_pref('text2');
        $text_3 = get_company_item_pref('text3');
        $text_4 = get_company_item_pref('text4');
        $text_5 = get_company_item_pref('text5');
        $text_6 = get_company_item_pref('text6');
        $combo = get_company_item_pref('combo1');
         $combo_1 = get_company_item_pref('combo2');
         $combo_2 = get_company_item_pref('combo3');
	    $combo_3 = get_company_item_pref('combo4');
	 $combo_4 = get_company_item_pref('combo5');
	 $combo_5 = get_company_item_pref('combo6');
	 
	 
	 $combo1= get_combo1($row[11]);
	 
	 $combo2=get_combo2($row[12]);
	 
	$combo3= get_combo3($row[13]);
	$combo4= get_combo3($row[14]);
	$combo5= get_combo3($row[15]);
	$combo6= get_combo3($row[16]);

	
	if($text_1['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[5] ;
	}
	
	elseif($text_1['item_combo_search']==1 && $text_4['item_combo_search']==1){
	  
	
   return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[5]. "-". $row[6] ;

	}
	
	elseif($text_2['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[7] ;
	}
	
	
	elseif($text_3['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[8] ;
	}
	
	elseif($text_4['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[6] ;
	}
	
	
	elseif($text_5['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[9] ;
	}
	
	elseif($text_6['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $row[10] ;
	}
	
	elseif($combo['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo1 ;
	}

    elseif($combo_1['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo2 ;
	}
	
	elseif($combo_2['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo3 ;
	}
	
	elseif($combo_3['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo4;
	}
	
	elseif($combo_4['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo5 ;
	}
	
	elseif($combo_5['item_combo_search']==1){
	    
   	return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1]. "-". $combo6 ;
	}
	
	else{
	  return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
	}
	
	
	
	
}
/*
function stock_items_list($name, $selected_id=null, $all_option=false,
						  $submit_on_change=false, $opts=array(), $editkey = false)
{
	global $all_items;

	$sql = "SELECT s.stock_id, s.description, c.description, s.inactive, s.editable
			FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c
			WHERE s.category_id=c.category_id";

	if ($editkey)
		set_editor('item', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 's.stock_id', 's.description',
		array_merge(
			array(
				'format' => '_format_stock_items',
				'spec_option' => $all_option === true ?  _("All Items") : $all_option,
				'spec_id' => $all_items,
				'search_box' => true,
				'search' => array("s.stock_id", "c.description","s.description"),
				'search_submit' => get_company_pref('no_item_list')!=0,
				'size'=>10,
				'select_submit'=> $submit_on_change,
				'category' => 2,
				'order' => array('c.description','s.stock_id')
			), $opts) );
	if ($editkey)
		$ret .= add_edit_combo('item');
	return $ret;
}
*/


function _format_batch($row)
{
	return (_("No. "). $row[1] . "&nbsp;-&nbsp;")  . _("Qty: ") .$row[2] . 
	 "&nbsp;-&nbsp;" . _("Exp: ") .sql2date($row[3]);
}

function stock_items_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false, $opts= array(),$purchase)
{
	if (isset($opts['fixed_asset']) && $opts['fixed_asset'])
		$editor_item = 'fa_item';
	else
		$editor_item = 'item';

// 	if ($editkey) ??
//		set_editor($editor_item, $name, $editkey);

	if ($label != null)
		echo "<td>$label</td>\n";

// ??
//  $opts = array_merge($options, array('cells'=>true, 'show_inactive'=>$all, 'new_icon' => $editkey ? 'item' : false));
//
//	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change, $opts);

	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array_merge(array('cells'=>true, 'show_inactive'=>$all), $opts), $editkey,null,$purchase);

}
/*
function stock_items_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>\n";
	stock_items_list_cells($label, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
*/
//---------------------------------------------------------------------------------------------------
//
// Select item via foreign code.
//
function sales_items_list($name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $type='', $opts=array())
{
	// all sales codes
	$sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
			 i.inactive, s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6  ,s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6, if(count(*)>1, '0', s.editable) as editable
			FROM
			".TB_PREF."stock_master s,
			".TB_PREF."item_codes i
			LEFT JOIN
			".TB_PREF."stock_category c
			ON i.category_id=c.category_id
			WHERE i.stock_id=s.stock_id
      AND mb_flag != 'F'";

	
	if ($type == 'local')	{ // exclude foreign codes
		$sql .=	" AND !i.is_foreign"; 
	} elseif ($type == 'kits') { // sales kits
		$sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
	}
	$sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale  ";
	$sql .= " GROUP BY i.item_code";

	return combo_input($name, $selected_id, $sql, 'i.item_code', 'c.description',
		array_merge(
		  array(
			'format' => '_format_stock_items',
			'spec_option' => $all_option===true ?  _("All Items") : $all_option,
			'spec_id' => ALL_TEXT, 
			'search_box' => true,
			'search' => array("i.item_code", "c.description", "i.description","s.text4","s.text1","s.text3",
                "s.text2","s.text5","s.text6"),
			'search_submit' => get_company_pref('no_item_list')!=0,
			'size'=>10,
			'select_submit'=> $submit_on_change,
			'category' => 2,
			'order' => array('c.description','i.item_code'),
			'editable' => 30,
			'max' => 255
		  ), $opts), $type == 'kits' ? $type : "stock_sales" );
}



function sales_items_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false)
{
	if ($editkey)
		set_editor('item', $name, $editkey);

	if ($label != null)
		echo "<td>$label</td>\n";
	echo sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'', array('cells'=>true));
}

function sales_kits_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	return sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'kits', array('cells'=>false, 'editable' => false));
}

function sales_local_items_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";
	echo sales_items_list($name, $selected_id, $all_option, $submit_on_change,
		'local', array('cells'=>false, 'editable' => false));
	echo "</td></tr>";
}
//------------------------------------------------------------------------------------

function stock_manufactured_items_list($name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag= 'M'")), false, "stock_manufactured");
}

function stock_manufactured_items_list_cells($label, $name, $selected_id=null,
				$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo stock_manufactured_items_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function stock_manufactured_items_list_row($label, $name, $selected_id=null,
		$all_option=false, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	stock_manufactured_items_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}



//---------------------------------------------------------------------------//
function lc_ref_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	global $all_items;

	$sql = "SELECT reference , reference FROM ".TB_PREF."purch_orders WHERE reference !=''";

	return combo_input($name, $selected_id, $sql, 'reference', 'reference',
		array(
			'spec_option' => $all_option === true ? _("All ") : $all_option,
			'spec_id' => $all_items,
			'select_submit'=> $submit_on_change
		) );
}

function lc_ref_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td >$label</td>\n";
	echo "<td>";
	echo lc_ref_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function lc_ref_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td  class=''>$label</td>";
	lc_ref_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}
//---------------------------------------------//
function lading_no_list($name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	global $all_items;

	$sql = "SELECT receive_ref, receive_ref FROM ".TB_PREF."purch_orders WHERE receive_ref !=''";

	return combo_input($name, $selected_id, $sql, 'receive_ref', 'receive_ref',
		array(
			'spec_option' => $all_option === true ? _("All ") : $all_option,
			'spec_id' => $all_items,
			'select_submit'=> $submit_on_change
		) );
}

function lading_no_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td >$label</td>\n";
	echo "<td>";
	echo lading_no_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n";
}

function lading_no_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	echo "<tr><td  class=''>$label</td>";
	lading_no_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
	echo "</tr>\n";
}





//------------------------------------------------------------------------------------

function stock_component_items_list($name, $parent_stock_id, $selected_id=null,
	$all_option=false, $submit_on_change=false, $editkey = false)
{
	$parent = db_escape($parent_stock_id);
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("s.stock_id != $parent"), 'parent'=> $parent_stock_id), $editkey, "component");
}


function stock_component_items_list_cells($label, $name, $parent_stock_id, 
	$selected_id=null, $all_option=false, $submit_on_change=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	$parent = db_escape($parent_stock_id);
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("stock_id != '$parent_stock_id'"), 'cells'=>true, 'parent'=> $parent_stock_id), $editkey, "component");
}
//------------------------------------------------------------------------------------

function stock_costable_items_list($name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'")), false, "stock_costable");
}

function stock_costable_items_list_cells($label, $name, $selected_id=null, 
	$all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'"), 'cells'=>true), false, "stock_costable");
}
//--iqra
function fixed_assets_list_cells($label, $name, $selected_id=null,
                                         $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo fixed_assets_list($name, $selected_id, $all_option, $submit_on_change,
        array('where'=>array("mb_flag!='D'"), 'cells'=>true), false, "stock_costable");
}
//---------------------------Query page drop down of item---------------------------------------------------------
function stock_itemss_list_new($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $opts=array(), $editkey = false, $type = "stock")
{
    $sql = "SELECT stock_id, s.description, c.description, s.inactive, s.editable
			FROM ".TB_PREF."stock_master s,".TB_PREF."stock_category c WHERE s.category_id=c.category_id";

    if (isset($opts['fixed_asset']) && $opts['fixed_asset'])
        $sql .= " AND mb_flag='F'";
    else
        $sql .= " AND mb_flag!='F'";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'stock_id', 's.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
//                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_option' => $all_option === true ? _("All Users") : $all_option,

                'spec_id' => ALL_TEXT,
                'search_box' => false,
                'search' => array("stock_id", "c.description","s.description",
                "s.text4","s.text1","s.text3", "s.text2","s.text5","s.text6"),
                'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
                'size'=>10,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','stock_id'),
                'editlink' => $editkey ? add_edit_combo('item') : false,
                'editable' => false,
                'max' => 255
            ), $opts), $type );
    return $ret;
}

function stock_costable_items_list_new($name, $selected_id=null,
                                       $all_option=false, $submit_on_change=false)
{
   return stock_itemss_list_new($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("mb_flag!='D'")), false, "stock_costable");
}

function stock_costable_items_list_cells_new($label, $name, $selected_id=null,
                                             $all_option=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo stock_costable_items_list_new($name, $selected_id, $all_option, $submit_on_change);
    echo "</td>\n";
}

function stock_costable_items_list_row_new($label, $name, $selected_id=null,
                                           $all_option=false, $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    stock_costable_items_list_cells_new(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//------------------------------------------
function stock_purchasable_items_list($name, $selected_id=null,	
	$all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
	return stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("NOT no_purchase"), 
			'show_inactive'=>$all), $editkey, "stock_purchased");
}
//
//	This helper is used in PO/GRN/PI entry and supports editable descriptions.
//
function stock_purchasable_items_list_cells($label, $name, $selected_id=null,
			$all_option=false, $submit_on_change=false, $editkey=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		array('where'=>array("NOT no_purchase"), 
			 'editable' => 30,
			 'cells'=>true), $editkey);
}

//------------------------------------------------------------------------------------

function stock_item_types_list_row($label, $name, $selected_id=null, $enabled=true)
{
	global $stock_types;

	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $stock_types, 
		array( 
			'select_submit'=> true, 
			'disabled' => !$enabled) );
	echo "</td></tr>\n";
}

function stock_item_types_list_cells($label, $name, $selected_id=null, $spec_option=false, $enabled=true)
{
	global $stock_types;

	echo "<td>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $stock_types,
		array(
			'select_submit'=> false,
			'spec_option' => $spec_option === true ? _("All Item Types") : $spec_option,
			'disabled' => !$enabled) );
	echo "</td></td>\n";
}
//dz  6/8/18
function stock_units_list_row($label, $name, $value=null, $enabled=true, $submit_on_change=false)
{
	$result = get_all_item_units();
	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	while($unit = db_fetch($result))
		$units[$unit['abbr']] = $unit['name'];

	echo array_selector($name, $value, $units, array( 
	'disabled' => !$enabled,
	'select_submit' => $submit_on_change, //submit on select: true/false

	) );

	echo "</td></tr>\n";
}

function stock_units_list_cells($label, $name, $value=null, $spec_option=false, $enabled=true)
{
	$result = get_all_item_units();
	echo "<td>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	while($unit = db_fetch($result))
		$units[$unit['abbr']] = $unit['name'];

	echo array_selector($name, $value, $units, array(
		'spec_option' => $spec_option === true ? _("All Units") : $spec_option,
		'disabled' => !$enabled) );

	echo "</td></td>\n";
}
///ansar

function stock_units_list_cell($label, $name, $value=null, $enabled=true,$stock_id,$onchange)
{
	$result = get_all_item_units(0,$stock_id);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
		while($unit = db_fetch($result))
		$units[$unit['abbr']] = $unit['name'];

	echo array_selector($name, $value, $units, array( 'disabled' => !$enabled),$onchange );
	echo "</td>\n";
}

//------------------------------------------------------------------------------------

function stock_purchasable_fa_list_cells($label, $name, $selected_id=null, $all_option=false,
	$submit_on_change=false, $all=false, $editkey = false, $exclude_items = array())
{
	// Check if a fixed asset has been bought.
	$where_opts[] = "stock_id NOT IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE type=".ST_SUPPRECEIVE." AND qty!=0 )";

	// exclude items currently on the order.
  	foreach($exclude_items as $item) {
    	$where_opts[] = "stock_id != ".db_escape($item->stock_id);
  	}
  	$where_opts[] = "mb_flag='F'";

	echo stock_items_list_cells($label, $name, $selected_id, $all_option, $submit_on_change, $all, $editkey, 
    array('fixed_asset' => true, 'where' => $where_opts));
}

function stock_disposable_fa_list($name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{
	// Check if a fixed asset has been bought....
	$where_opts[] = "stock_id IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE type=".ST_SUPPRECEIVE." AND qty!=0 )";
	// ...but has not been disposed or sold already.
  	$where_opts[] = "stock_id NOT IN
		( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE (type=".ST_CUSTDELIVERY." OR type=".ST_INVADJUST.") AND qty!=0 )";

	$where_opts[] = "mb_flag='F'";

	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
    array('fixed_asset' => true, 'where' => $where_opts));
}

function stock_disposable_fa_list_cells($label, $name, $selected_id=null,
	$all_option=false, $submit_on_change=false, $exclude_items = array())
{
	// Check if a fixed asset has been bought....
	$where_opts[] = "stock_id IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE type=".ST_SUPPRECEIVE." AND qty!=0 )";
	// ...but has not been disposed or sold already.
	$where_opts[] = "stock_id NOT IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE (type=".ST_CUSTDELIVERY." OR type=".ST_INVADJUST.") AND qty!=0 )";

	$where_opts[] = "mb_flag='F'";

	foreach($exclude_items as $item) {
    	$where_opts[] = "stock_id != ".db_escape($item->stock_id);
	}

	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change, 
    array('fixed_asset' => true, 'cells'=>true, 'where' => $where_opts));
}

function stock_depreciable_fa_list_cells($label, $name, $selected_id=null,
	$all_option=false, $submit_on_change=false)
{

	// Check if a fixed asset has been bought....
	$where_opts[] = "stock_id IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE type=".ST_SUPPRECEIVE." AND qty!=0 )";
	// ...but has not been disposed or sold already.
	$where_opts[] = "stock_id NOT IN
    	( SELECT stock_id FROM ".TB_PREF."stock_moves WHERE (type=".ST_CUSTDELIVERY." OR type=".ST_INVADJUST.") AND qty!=0 )";

	$year = get_current_fiscalyear();
	$y = date('Y', strtotime($year['end']));

	// check if current fiscal year
	$where_opts[] = "depreciation_date < '".$y."-12-01'";
	$where_opts[] = "depreciation_date >= '".($y-1)."-12-01'";

	$where_opts[] = "material_cost > 0";
	$where_opts[] = "mb_flag='F'";

	if ($label != null)
		echo "<td>$label</td>\n";
	echo stock_items_list($name, $selected_id, $all_option, $submit_on_change,
		 array('fixed_asset' => true, 'where' => $where_opts, 'cells'=>true));
}

//------------------------------------------------------------------------------------

function tax_types_list($name, $selected_id=null, $none_option=false, $submit_on_change=false)
{
	$sql = "SELECT id, CONCAT(name, ' (',rate,'%)') as name FROM ".TB_PREF."tax_types";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
		array(
			'spec_option' => $none_option,
			'spec_id' => ALL_NUMERIC,
			'select_submit'=> $submit_on_change,
			'async' => false,
		) );
}

function tax_types_list_cells($label, $name, $selected_id=null, $none_option=false,
	$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo tax_types_list($name, $selected_id, $none_option, $submit_on_change);
	echo "</td>\n";
}

function tax_types_list_row($label, $name, $selected_id=null, $none_option=false,
	$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	tax_types_list_cells(null, $name, $selected_id, $none_option, $submit_on_change);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function tax_groups_list($name, $selected_id=null, $spec_option=true)
{
	$sql = "SELECT id, name FROM ".TB_PREF."tax_groups";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
		array('spec_option' => $spec_option === true ? _("All Tax Group") : $spec_option,
		) );
}

function tax_groups_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo tax_groups_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function tax_groups_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	tax_groups_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function item_tax_types_list($name, $selected_id=null, $spec_option=false)
{
	$sql ="SELECT id, name FROM ".TB_PREF."item_tax_types";
	return combo_input($name, $selected_id, $sql, 'id', 'name', array('order' => 'id',
	'spec_option' => $spec_option === true ? _("Select Tax Type") : $spec_option,) );
}

function item_tax_types_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo item_tax_types_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function item_tax_types_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	item_tax_types_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function shippers_list($name, $selected_id=null)
{
	$sql = "SELECT shipper_id, shipper_name, inactive FROM ".TB_PREF."shippers";
	return combo_input($name, $selected_id, $sql, 'shipper_id', 'shipper_name', 
		array('order'=>array('shipper_name')));
}

function shippers_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo shippers_list($name, $selected_id);
	echo "</td>\n";
}

function shippers_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	shippers_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------------

function sales_persons_list($name, $selected_id=null, $spec_opt=false)
{
    $sql = "SELECT salesman_code, salesman_name, inactive FROM ".TB_PREF."salesman";
    return combo_input($name, $selected_id, $sql, 'salesman_code', 'salesman_name',
        array('order'=>array('salesman_name'),
            'spec_option' => $spec_opt === true ? _("All Sales Person") : $spec_opt,
            'spec_id' => ALL_NUMERIC));
}

function sales_persons_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo sales_persons_list($name, $selected_id, $spec_opt);
    echo "</td>\n";
}

function sales_persons_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
    echo "<tr><td class=''>$label</td>";
    sales_persons_list_cells(null, $name, $selected_id, $spec_opt);
    echo "</tr>\n";
}

//------------------------------------------------------------------------------------
/////////////////hareeem work start 12-sept-2017

function combo_1_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_1";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_1_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_1_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}
//////////////////////////////////combo 2
function combo_2_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_2";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_2_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_2_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}
////////////////////////////combo 3
function combo_3_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT combo_code ,description, inactive FROM ".TB_PREF."combo_3";

//	if ($category == PM_CASH) // only cash
//		$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
//	elseif ($category == PM_CREDIT) // only delayed payments
//		$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
//	elseif (!$prepayments)
//		$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function combo_3_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_3_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}
///////////////// hareem work end
function sales_areas_list($name, $selected_id=null, $spec_opt=false)
{
	$sql = "SELECT area_code, description, inactive FROM ".TB_PREF."areas";
	return combo_input($name, $selected_id, $sql, 'area_code', 'description',
        array('spec_option' => $spec_opt));
}

function sales_areas_list_cells($label, $name, $selected_id=null, $spec_opt=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_areas_list($name, $selected_id, $spec_opt);
	echo "</td>\n";
}

function sales_areas_list_row($label, $name, $selected_id=null, $spec_opt=false)
{
	echo "<tr><td class=''>$label</td>";
	sales_areas_list_cells(null, $name, $selected_id, $spec_opt);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function sales_groups_list($name, $selected_id=null, $special_option=false)
{
	$sql = "SELECT id, description, inactive FROM ".TB_PREF."groups";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array(
		'spec_option' => $special_option===true ? ' ' : $special_option,
		'order' => 'description', 'spec_id' => 0,
	));
}

function sales_groups_list_cells($label, $name, $selected_id=null, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_groups_list($name, $selected_id, $special_option);
	echo "</td>\n";
}

function sales_groups_list_row($label, $name, $selected_id=null, $special_option=false)
{
	echo "<tr><td class=''>$label</td>";
	sales_groups_list_cells(null, $name, $selected_id, $special_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function _format_template_items($row)
{
	return ($row[0] . "&nbsp;- &nbsp;" . _("Amount") . "&nbsp;".$row[1]);
}

function templates_list($name, $selected_id=null, $special_option=false)
{
	$sql = "SELECT sorder.order_no,	Sum(line.unit_price*line.quantity*(1-line.discount_percent)) AS OrderValue
		FROM ".TB_PREF."sales_orders as sorder, ".TB_PREF."sales_order_details as line
		WHERE sorder.order_no = line.order_no AND sorder.type = 1 GROUP BY line.order_no";
	return combo_input($name, $selected_id, $sql, 'order_no', 'OrderValue', array(
		'format' => '_format_template_items',
		'spec_option' => $special_option===true ? ' ' : $special_option,
		'order' => 'order_no', 'spec_id' => 0,
	));
}

function templates_list_cells($label, $name, $selected_id=null, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo templates_list($name, $selected_id, $special_option);
	echo "</td>\n";
}

function templates_list_row($label, $name, $selected_id=null, $special_option=false)
{
	echo "<tr><td class=''>$label</td>";
	templates_list_cells(null, $name, $selected_id, $special_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function workorders_list($name, $selected_id=null)
{
	$sql = "SELECT id, wo_ref FROM ".TB_PREF."workorders WHERE closed=0";
	return combo_input($name, $selected_id, $sql, 'id', 'wo_ref', array());
}

function workorders_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo workorders_list($name, $selected_id);
	echo "</td>\n";
}

function workorders_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	workorders_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function payment_terms_list($name, $selected_id=null)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";
	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms', array());
}

function payment_terms_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo payment_terms_list($name, $selected_id);
	echo "</td>\n";
}

function payment_terms_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	payment_terms_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------

function credit_status_list($name, $selected_id=null)
{
	$sql ="SELECT id, reason_description, inactive FROM ".TB_PREF."credit_status";
	return combo_input($name, $selected_id, $sql, 'id', 'reason_description', array());
}

function credit_status_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo credit_status_list($name, $selected_id);
	echo "</td>\n";
}

function credit_status_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	credit_status_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function sales_types_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT id, sales_type, inactive FROM ".TB_PREF."sales_types";

	return combo_input($name, $selected_id, $sql, 'id', 'sales_type',
	array(
		'spec_option' => $special_option===true ? _("All Sales Types") : $special_option,
		'spec_id' => 0,
		'select_submit'=> $submit_on_change,
	) );
}

function sales_types_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_types_list($name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}

function sales_types_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr><td class=''>$label</td>";
	sales_types_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function _format_date($row)
{
	return sql2date($row['reconciled']);
}

function bank_reconciliation_list($account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT reconciled, reconciled FROM ".TB_PREF."bank_trans
		WHERE bank_act=".db_escape($account)." AND reconciled IS NOT NULL
		GROUP BY reconciled";
	return combo_input($name, $selected_id, $sql, 'id', 'reconciled',
		array(
			'spec_option' => $special_option,
			'format' => '_format_date',
			'spec_id' => '',
			'select_submit'=> $submit_on_change
		) );
}

function bank_reconciliation_list_cells($label,$account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_reconciliation_list($account, $name, $selected_id, $submit_on_change, $special_option);
	echo "</td>\n";
}
/*
function bank_reconciliation_list_row($label, $account, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	echo "<tr>\n";
	bank_reconciliation_list_cells($label, $account, $name, $selected_id, $submit_on_change, $special_option);
	echo "</tr>\n";
}
*/
//-----------------------------------------------------------------------------------------------

function workcenter_list($name, $selected_id=null, $all_option=false)
{

	$sql = "SELECT id, name, inactive FROM ".TB_PREF."workcentres";

	return combo_input($name, $selected_id, $sql, 'id', 'name',
		array(
			'spec_option' =>$all_option===true ? _("All Suppliers") : $all_option,
			'spec_id' => ALL_TEXT,
		) );
}

function workcenter_list_cells($label, $name, $selected_id=null, $all_option=false)
{
	default_focus($name);
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo workcenter_list($name, $selected_id, $all_option);
	echo "</td>\n";
}

function workcenter_list_row($label, $name, $selected_id=null, $all_option=false)
{
	echo "<tr><td class=''>$label</td>";
	workcenter_list_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function bank_accounts_list_all($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{

global $SysPrefs;
 
     $user=$_SESSION["wa_current_user"]->user;
    if (!empty($SysPrefs->prefs['user_bank']))
    {
    $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, ".TB_PREF."bank_accounts.inactive FROM ".TB_PREF."bank_accounts 
    INNER JOIN ".TB_PREF."user_banks ON ".TB_PREF."user_banks.loc_code=".TB_PREF."bank_accounts.id	
		WHERE ".TB_PREF."user_banks.user_id=$user";
    }
    else
        {
           $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts ";
    }

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
		array(
			'format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'spec_option' => $spec_option === true ? _("Select All Banks") : $spec_option,
			'spec_id' => '',
			'async' => false
		) );
}

function bank_accounts_list_all_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_accounts_list_all($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function bank_accounts_list_all_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	bank_accounts_list_all_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
//dz 27/8/18 This drop down shows all bank accounts for Bank Restriction Setup
function bank_accounts_list_for_bank_restriction_setup($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{

          $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts ";


	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
		array(
			'format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'spec_option' => $spec_option,
			'spec_id' => '',
			'async' => false
		) );
}

function bank_accounts_list_for_bank_restriction_setup_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_accounts_list_for_bank_restriction_setup($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function bank_accounts_list_for_bank_restriction_setup_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	bank_accounts_list_for_bank_restriction_setup_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}
//-----------------------------------------------------------------------------------------------

function bank_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{

	global $SysPrefs;
   $user=$_SESSION["wa_current_user"]->user;
   if (!empty($SysPrefs->prefs['user_bank']))
    {
        $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, ".TB_PREF."bank_accounts.inactive FROM ".TB_PREF."bank_accounts 
    INNER JOIN ".TB_PREF."user_banks ON ".TB_PREF."user_banks.loc_code=".TB_PREF."bank_accounts.id	
		WHERE ".TB_PREF."user_banks.user_id=$user AND ".TB_PREF."bank_accounts.account_type !=".BT_CASH;
    }
    else
    {
         $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_accounts.account_type !=".BT_CASH;
    }
   

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
		array(
			'format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'spec_option' => $spec_option,
			'spec_id' => '',
			'async' => false
		) );
}

function bank_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_accounts_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function bank_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	bank_accounts_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}
// For Cash payment and deposit voucher
function cash_bank_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    	global $SysPrefs;
   $user=$_SESSION["wa_current_user"]->user;
   if (!empty($SysPrefs->prefs['user_bank']))
    {
        $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, ".TB_PREF."bank_accounts.inactive FROM ".TB_PREF."bank_accounts 
    INNER JOIN ".TB_PREF."user_banks ON ".TB_PREF."user_banks.loc_code=".TB_PREF."bank_accounts.id	
		WHERE ".TB_PREF."user_banks.user_id=$user AND ".TB_PREF."bank_accounts.account_type =".BT_CASH;
    }
    else
    {
    $sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
		WHERE ".TB_PREF."bank_accounts.account_type=".BT_CASH;
}
    return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
        array(
            'format' => '_format_add_curr',
            'select_submit'=> $submit_on_change,
            'spec_option' => $spec_option,
            'spec_id' => '',
            'async' => false
        ) );
}

function cash_bank_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo cash_bank_accounts_list($name, $selected_id, $submit_on_change);
    echo "</td>\n";
}

function cash_bank_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    cash_bank_accounts_list_cells(null, $name, $selected_id, $submit_on_change);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------

function cash_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false, $all_option=false)
{

	$sql = "SELECT id, bank_account_name, bank_curr_code, inactive
		FROM ".TB_PREF."bank_accounts
		WHERE account_type=".BT_CASH;

	if ($label != null)
		echo "<tr><td class=''>$label</td>\n";
	echo "<td>";
	echo combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
		array(
			'spec_option' => $all_option,
			'spec_id' => ALL_TEXT,
			'format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'async' => true
		) );
	echo "</td></tr>\n";
}
//-----------------------------------------------------------------------------------------------

function pos_list_row($label, $name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{
	$sql = "SELECT id, pos_name, inactive FROM ".TB_PREF."sales_pos";

	default_focus($name);
	echo '<tr>';
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo combo_input($name, $selected_id, $sql, 'id', 'pos_name',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true,
			'spec_option' =>$spec_option,
			'spec_id' => -1,
			'order'=> array('pos_name')
		) );
	echo "</td></tr>\n";

}
//-----------------------------------------------------------------------------------------------
// Payment type selector for current user.
//
function sale_payment_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";

	if ($category == PM_CASH) // only cash
			$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
	elseif ($category == PM_CREDIT) // only delayed payments
			$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
	elseif (!$prepayments)
			$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms',
		array(
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function sale_payment_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo sale_payment_list($name, $category, $selected_id, $submit_on_change, $prepayments);

	echo "</td>\n";
}
//===========================================================================

function sale_payment1_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true, $spec_option=false)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";

	if ($category == PM_CASH) // only cash
			$sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
	elseif ($category == PM_CREDIT) // only delayed payments
			$sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
	elseif (!$prepayments)
			$sql .= " WHERE days_before_due>=0";

	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms',
		array('spec_option' => $spec_option === true ? _("Select All") : $spec_option,
			'select_submit'=> $submit_on_change,
			'async' => true
		) );

}

function sale_payment1_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true, $all_option=false)
{
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo sale_payment1_list($name, $category, $selected_id, $submit_on_change, $prepayments, $all_option);

	echo "</td>\n";
}
//-----------------------------------------------------------------------------------------------

function class_list($name, $selected_id=null, $submit_on_change=false)
{
	$sql = "SELECT cid, class_name FROM ".TB_PREF."chart_class";

	return combo_input($name, $selected_id, $sql, 'cid', 'class_name',
		array(
			'select_submit'=> $submit_on_change,
			'async' => false
		) );

}

function class_list_cells($label, $name, $selected_id=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo class_list($name, $selected_id, $submit_on_change);
	echo "</td>\n";
}

function class_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	class_list_cells(null, $name, $selected_id, $submit_on_change);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function stock_categories_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $fixed_asset=false)
{
	$where_opts = array();
	if ($fixed_asset)
		$where_opts[0] = "dflt_mb_flag='F'";
	else
		$where_opts[0] = "dflt_mb_flag!='F'";

	$sql = "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category";
	return combo_input($name, $selected_id, $sql, 'category_id', 'description',
 		array('order'=>'category_id',
			'spec_option' => $spec_opt === true ? _("All Categories") : $spec_opt,
			'spec_id' => -1,
 			'select_submit'=> $submit_on_change,
 			'async' => true,
			'where' => $where_opts,
 		));
}

function stock_categories_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $fixed_asset=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo stock_categories_list($name, $selected_id, $spec_opt, $submit_on_change, $fixed_asset);
	echo "</td>\n";
}

function stock_categories_list_row($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false, $fixed_asset=false)
{
	echo "<tr><td class=''>$label</td>";
	stock_categories_list_cells(null, $name, $selected_id, $spec_opt, $submit_on_change, $fixed_asset);
	echo "</tr>\n";
}

//-----------------actegory 2 for muslim------------------------------//

function stock_categories2_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    $sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo1";
    return combo_input($name, $selected_id, $sql, 'combo_code', 'description',
        array('order'=>'combo_code',
            'spec_option' => $spec_opt,
            'spec_id' => -1,
            'select_submit'=> $submit_on_change,
            'async' => true
        ));
}
function stock_categories2_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo stock_categories2_list($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}

function stock_categories2_list_row($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    stock_categories2_list_cells(null, $name, $selected_id, $spec_opt, $submit_on_change);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function fixed_asset_classes_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	$sql = "SELECT c.fa_class_id, CONCAT(c.fa_class_id,' - ',c.description) `desc`, CONCAT(p.fa_class_id,' - ',p.description) `class`, c.inactive FROM "
		.TB_PREF."stock_fa_class c LEFT JOIN ".TB_PREF."stock_fa_class p ON c.parent_id=p.fa_class_id";

	return combo_input($name, $selected_id, $sql, 'c.fa_class_id', 'desc',
		array('order'=>'c.fa_class_id',
			'spec_option' => $spec_opt,
			'spec_id' => '-1',
 			'select_submit'=> $submit_on_change,
 			'async' => true,
			'search_box' => true,
			'search' => array("c.fa_class_id"),
		    'search_submit' => false,
		    'spec_id' => '',
		    'size' => 3,
		    'max' => 3,
			'category' => 'class',
 		));
}

function fixed_asset_classes_list_row($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	echo "<td>";
	echo fixed_asset_classes_list($name, $selected_id, $spec_opt, $submit_on_change);
	echo "</td></tr>\n";
}

//-----------------------------------------------------------------------------------------------

function gl_account_types_list($name, $selected_id=null, $all_option=false, $all=true,$submit_on_change=true)
{
    global $all_items;

    $sql = "SELECT id, name FROM ".TB_PREF."chart_types";

    return combo_input($name, $selected_id, $sql, 'id', 'name',
        array(
            'format' => '_format_account',
            'order' => array('class_id', 'id', 'parent'),
            'select_submit'=> $submit_on_change,
          //  'spec_option' =>$all_option,
            'spec_option' => $all_option === true ? _("All ") : $all_option,
            'spec_id' => $all_items,
            'async' => false

        ) );
}

function gl_account_types_list_cells($label, $name, $selected_id=null, $all_option=false, $all=false,$submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo gl_account_types_list($name, $selected_id, $all_option, $all,$submit_on_change);
    echo "</td>\n";
}

function gl_account_types_list_row($label, $name, $selected_id=null, $all_option=false,	$all=false, $submit_on_change=true)
{
    echo "<tr><td class=''>$label</td>";
    gl_account_types_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,
        $all);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function gl_all_accounts_list($name, $selected_id=null, $skip_bank_accounts=false,
	$cells=false, $all_option=false, $submit_on_change=false, $all=false)
{
	if ($skip_bank_accounts)
		$sql = "SELECT chart.account_code, chart.account_name, type.name, chart.inactive, type.id
			FROM (".TB_PREF."chart_master chart,".TB_PREF."chart_types type) "
			."LEFT JOIN ".TB_PREF."bank_accounts acc "
			."ON chart.account_code=acc.account_code
				WHERE acc.account_code  IS NULL
			AND chart.account_type=type.id";
	else
		$sql = "SELECT chart.account_code, chart.account_name, CONCAT(type.id, ' - ' ,type.name), chart.inactive, type.id
			FROM ".TB_PREF."chart_master chart,".TB_PREF."chart_types type
			WHERE chart.account_type=type.id";

	return combo_input($name, $selected_id, $sql, 'chart.account_code', 'chart.account_name',
		array(
			'format' => '_format_account',
			'spec_option' => $all_option===true ?  _("All Accounts") : $all_option,
			'spec_id' => '',
			'type' => 2,
			'order' => array('type.class_id','type.id','account_code'),
			'search_box' => $cells,
			'search_submit' => false,
			'size' => 12,
			'max' => 10,
			'cells' => true,
			'select_submit'=> $submit_on_change,
			'async' => false,
			'category' => 2,
			'show_inactive' => $all
		), "account" );
}

function _format_account($row)
{
	return $row[0] .  "&nbsp;&nbsp;&nbsp;&nbsp;" . $row[1];
}

function gl_all_accounts_list_cells($label, $name, $selected_id=null, 
	$skip_bank_accounts=false, $cells=false, $all_option=false, 
	$submit_on_change=false, $all=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo gl_all_accounts_list($name, $selected_id, 
		$skip_bank_accounts, $cells, $all_option, $submit_on_change, $all);
	echo "</td>\n";
}

function gl_all_accounts_list_row($label, $name, $selected_id=null, 
	$skip_bank_accounts=false, $cells=false, $all_option=false)
{
	echo "<tr><td class=''>$label</td>";
	gl_all_accounts_list_cells(null, $name, $selected_id, 
		$skip_bank_accounts, $cells, $all_option);
	echo "</tr>\n";
}

function yesno_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("No");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}

//bin naeem
function yesno_list11($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("No");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");

	return array_selector($name, $selected_id, $items, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_list_cells11($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_list11($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_list_row11($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_list_cells11(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}





//bin naeem
function yesno_list2($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    
    $items['0'] = _("Deliver To");
    $items['1'] =  _("Invoice To");
$items['2'] = _("Deliver/Invoice To");
    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function yesno_list_cells2($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_list2($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function yesno_list_row2($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_list_cells2(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}







//------------------------------------------------------------------------------------------------

function languages_list($name, $selected_id=null, $all_option=false)
{
	global $installed_languages;

	$items = array();
	if ($all_option)
		$items[''] = $all_option;
	foreach ($installed_languages as $lang)
			$items[$lang['code']] = $lang['name'];
	return array_selector($name, $selected_id, $items);
}

function languages_list_cells($label, $name, $selected_id=null, $all_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo languages_list($name, $selected_id, $all_option);
	echo "</td>\n";
}

function languages_list_row($label, $name, $selected_id=null, $all_option=false)
{
	echo "<tr><td class=''>$label</td>";
	languages_list_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------

function bank_account_types_list($name, $selected_id=null)
{
	global $bank_account_types;

	return array_selector($name, $selected_id, $bank_account_types);
}

function bank_account_types_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_account_types_list($name, $selected_id);
	echo "</td>\n";
}

function bank_account_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	bank_account_types_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------
function payment_person_types_list($name, $selected_id=null, $submit_on_change=false)
{
	global $payment_person_types;

	$items = array();
	foreach ($payment_person_types as $key=>$type)
	{
		if ($key != PT_WORKORDER)
			$items[$key] = $type;
	}		
	return array_selector($name, $selected_id, $items, 
		array( 'select_submit'=> $submit_on_change ) );
}

function payment_person_types_list_cells($label, $name, $selected_id=null, $related=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo payment_person_types_list($name, $selected_id, $related);
	echo "</td>\n";
}

function payment_person_types_list_row($label, $name, $selected_id=null, $related=null)
{
	echo "<tr><td class=''>$label</td>";
	payment_person_types_list_cells(null, $name, $selected_id, $related);
	echo "</tr>\n";
}
//------------------------------------------------------------------------------
function payment_person_types_list1($name, $selected_id=null, $submit_on_change=false)
{
    global $payment_person_types;
   
    $items = array();
    foreach ($payment_person_types as $key=>$type)
    {
        if ($key != PT_WORKORDER)
            $items[$key] = $type;
    }
    return array_selector($name, $selected_id, $items,
        array( 'select_submit'=> $submit_on_change,
            'async' => false) );
}

function payment_person_types_list_cells1($label, $name, $selected_id=null, $related=null)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo payment_person_types_list1($name, $selected_id, $related);
    echo "</td>\n";
}

function payment_person_types_list_row1($label, $name, $selected_id=null, $related=null)
{
    echo "<tr><td class=''>$label</td>";
    payment_person_types_list_cells1(null, $name, $selected_id, $related);
    echo "</tr>\n";
}

//------------------------------------------------------------------------------------------------

function wo_types_list($name, $selected_id=null)
{
	global $wo_types_array;
	
	return array_selector($name, $selected_id, $wo_types_array, 
		array( 'select_submit'=> true, 'async' => true ) );
}

function wo_types_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td><td>\n";
	echo wo_types_list($name, $selected_id);
	echo "</td></tr>\n";
}

//------------------------------------------------------------------------------------------------

function wo_types_list1($name, $selected_id=null, $spec_opt=true)
{
	global $wo_types_array;
	
	return array_selector($name, $selected_id, $wo_types_array,
		array( 'spec_option' => $spec_opt===true ?_("All Types") : $spec_opt,
            'select_submit'=> true,
            'async' => true ) );
}
function wo_types_list_cells1($label, $name, $selected_id=null, $spec_opt=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo wo_types_list1($name, $selected_id, $spec_opt);
    echo "</td>\n";
}
function wo_types_list_row1($label, $name, $selected_id=null, $spec_opt=false)
{
	echo "<tr><td class=''>$label</td><td>\n";
	echo wo_types_list_cells1(null, $name, $selected_id, $spec_opt);
	echo "</td></tr>\n";
}

//------------------------------------------------------------------------------------------------

function dateformats_list_row($label, $name, $value=null)
{
	global $SysPrefs;

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $SysPrefs->dateformats );
	echo "</td></tr>\n";
}

function dateseps_list_row($label, $name, $value=null)
{
	global $SysPrefs;

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $SysPrefs->dateseps );
	echo "</td></tr>\n";
}

function thoseps_list_row($label, $name, $value=null)
{
	global $SysPrefs;

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $SysPrefs->thoseps );
	echo "</td></tr>\n";
}

function decseps_list_row($label, $name, $value=null)
{
	global $SysPrefs;

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $SysPrefs->decseps );
	echo "</td></tr>\n";
}

function themes_list_row($label, $name, $value=null)
{
	global $path_to_root;

	$path = $path_to_root.'/themes/';
	$themes = array();
	$themedir = opendir($path);
	while(false !== ($fname = readdir($themedir)))
	{
		if($fname!='.' && $fname!='..' && $fname!='CVS' && is_dir($path.$fname))
		{
			$themes[$fname] =  $fname;
		}
	}
	ksort($themes);

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $themes );
	echo "</td></tr>\n";
}

function pagesizes_list_row($label, $name, $value=null)
{
	global $SysPrefs;

	$items = array();
	foreach ($SysPrefs->pagesizes as $pz)
		$items[$pz] = $pz;

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $items );
	echo "</td></tr>\n";
}

function systypes_list($name, $value=null, $spec_opt=false, $submit_on_change=false, $exclude=array())
{
	global $systypes_array;

	// emove non-voidable transactions if needed
	$systypes = array_diff_key($systypes_array, array_flip($exclude));
	return array_selector($name, $value, $systypes, 
		array( 
			'spec_option'=> $spec_opt,
			'spec_id' => ALL_NUMERIC,
			'select_submit'=> $submit_on_change,
			'async' => false,
			)
	);
}

function systypes_list_cells($label, $name, $value=null, $submit_on_change=false, $exclude=array())
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo systypes_list($name, $value, false, $submit_on_change, $exclude);
	echo "</td>\n";
}

function systypes_list_row($label, $name, $value=null, $submit_on_change=false, $exclude=array())
{
	echo "<tr><td class=''>$label</td>";
	systypes_list_cells(null, $name, $value, $submit_on_change, $exclude);
	echo "</tr>\n";
}

function journal_types_list_cells($label, $name, $value=null, $submit_on_change=false)
{
	global $systypes_array;

	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";

	$items = $systypes_array;

	// exclude quotes, orders and dimensions
	foreach (array(ST_PURCHORDER, ST_SALESORDER, ST_DIMENSION, ST_SALESQUOTE, ST_LOCTRANSFER) as $excl)
			unset($items[$excl]);
	
	echo array_selector($name, $value, $items, 
		array( 
			'spec_option'=> _("All"),
			'spec_id' => -1,
			'select_submit'=> $submit_on_change,
			'async' => false
			)
	);
	echo "</td>\n";
}

function cust_allocations_list_cells($label, $name, $selected=null,$submit_on_change=false)
{

	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	$allocs = array( 
		ALL_TEXT=>_("All Types"),
		'1'=> _("Sales Invoices"),
		'2'=> _("Overdue Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Delivery Notes")
	);
	echo array_selector($name, $selected, $allocs,
	array(

		'spec_id' => -1,
		'select_submit'=> $submit_on_change,
		'async' => false
	));
	
	
		echo "</td>\n";

	
	
}

function supp_allocations_list_cell($name, $selected=null)
{

	echo "<td>\n";
	$allocs = array( 
		ALL_TEXT=>_("All Types"),
		'1'=> _("Invoices"),
		'2'=> _("Overdue Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Overdue Credit Notes")
	);
	echo array_selector($name, $selected, $allocs);
	echo "</td>\n";
}

function supp_transactions_list_cell($name, $selected=null)
{

	echo "<td>\n";
	$allocs = array( 
		ALL_TEXT=>_("All Types"),
		'6'=>_("GRNs"),
		'1'=> _("Invoices"),
		'2'=> _("Overdue Invoices"),
        '7' => _("Import Invoices"),
		'3' => _("Payments"),
		'4' => _("Credit Notes"),
		'5' => _("Overdue Credit Notes")

	);

	echo array_selector($name, $selected, $allocs);
	echo "</td>\n";
}

function policy_list_cells($label, $name, $selected=null)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo array_selector($name, $selected, 
				array( '' => _("Automatically put balance on back order"),
			 		'CAN' => _("Cancel any quantites not delivered")) );
	echo "</td>\n";
}

function policy_list_row($label, $name, $selected=null)
{
	echo "<tr><td class=''>$label</td>";
	policy_list_cells(null, $name, $selected);
	echo "</tr>\n";
}

function credit_type_list_cells($label, $name, $selected=null, $submit_on_change=false)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo array_selector($name, $selected, 
			array( 'Return' => _("Items Returned to Inventory Location"),
		 		'WriteOff' => _("Items Written Off")),
			array( 'select_submit'=> $submit_on_change ) );
	echo "</td>\n";
}

function credit_type_list_row($label, $name, $selected=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	credit_type_list_cells(null, $name, $selected, $submit_on_change);
	echo "</tr>\n";
}

/*function number_list($name, $selected, $from, $to, $no_option=false)
{
	$items = array();
	for ($i = $from; $i <= $to; $i++)
		$items[$i] = "$i";

	return array_selector($name, $selected, $items,
			array(	'spec_option' => $no_option,
					'spec_id' => ALL_NUMERIC) );
}

function number_list_cells($label, $name, $selected, $from, $to, $no_option=false)
{
	if ($label != null)
		label_cell($label);
	echo "<td>\n";
	echo number_list($name, $selected, $from, $to, $no_option);
	echo "</td>\n";
}

function number_list_row($label, $name, $selected, $from, $to, $no_option=false)
{
	echo "<tr><td class=''>$label</td>";
	echo number_list_cells(null, $name, $selected, $from, $to, $no_option);
	echo "</tr>\n";
}*/

function print_profiles_list_row($label, $name, $selected_id=null, $spec_opt=false,
	$submit_on_change=true)
{
	$sql = "SELECT profile FROM ".TB_PREF."print_profiles"
		." GROUP BY profile";
	$result = db_query($sql, 'cannot get all profile names');
	$profiles = array();
	while($myrow=db_fetch($result)) {
		$profiles[$myrow['profile']] = $myrow['profile'];
	}

	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $profiles, 
		array( 'select_submit'=> $submit_on_change,
			'spec_option'=>$spec_opt,
			'spec_id' => ''
		 ));

	echo "</td></tr>\n";
}

function printers_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=false)
{
	static $printers; // query only once for page display

	if (!$printers) {
		$sql = "SELECT id, name, description FROM ".TB_PREF."printers";	
		$result = db_query($sql, 'cannot get all printers');
		$printers = array();
		while($myrow=db_fetch($result)) {
			$printers[$myrow['id']] = $myrow['name'].'&nbsp;-&nbsp;'.$myrow['description'];
		}
	}
	return array_selector($name, $selected_id, $printers, 
		array( 'select_submit'=> $submit_on_change,
			'spec_option'=>$spec_opt,
			'spec_id' => ''
		 ));
}

//------------------------------------------------------------------------------------------------

function quick_entries_list($name, $selected_id=null, $type=null, $submit_on_change=false)
{
	$where = false;
	$sql = "SELECT id, description FROM ".TB_PREF."quick_entries";
	if ($type != null)
		$sql .= " WHERE type=$type";

	return combo_input($name, $selected_id, $sql, 'id', 'description',
		array(
			'spec_id' => '',
			'order' => 'description',
			'select_submit'=> $submit_on_change,
			'async' => false
		) );

}

function quick_entries_list_cells($label, $name, $selected_id=null, $type, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";	
	echo quick_entries_list($name, $selected_id, $type, $submit_on_change);
	echo "</td>";
}

function quick_entries_list_row($label, $name, $selected_id=null, $type, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	quick_entries_list_cells(null, $name, $selected_id, $type, $submit_on_change);
	echo "</tr>\n";
}


function quick_actions_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $quick_actions;
	
	echo "<tr><td class=''>$label</td><td>";
	echo array_selector($name, $selected_id, $quick_actions, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

function quick_entry_types_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $quick_entry_types;
		
	echo "<tr><td class=''>$label</td><td>";
	echo array_selector($name, $selected_id, $quick_entry_types, 
		array( 
			'select_submit'=> $submit_on_change
			) );
	echo "</td></tr>\n";
}

function record_status_list_row($label, $name) {
	return yesno_list_row($label, $name, null, 	_('Inactive'), _('Active'));
}
function duty_status_list_row($label, $name) {
    return yesno_list_row($label, $name, null, 	_('Inactive'), _('Active'));
}
function deduction_status_list_row($label, $name) {
    return yesno_list_row($label, $name, null, 	_('Inactive'), _('Active'));
}

function class_types_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $class_types;
	
	echo "<tr><td class=''>$label</td><td>";
	echo array_selector($name, $selected_id, $class_types, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

//------------------------------------------------------------------------------------------------

function security_roles_list($name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{

	$sql = "SELECT id, role, inactive FROM ".TB_PREF."security_roles";

	return combo_input($name, $selected_id, $sql, 'id', 'description',
		array(
			'spec_option'=>$new_item ? _("New role") : false,
			'spec_id' => '',
			'select_submit'=> $submit_on_change,
			'show_inactive' => $show_inactive
		) );
}

function security_roles_list_cells($label, $name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo security_roles_list($name, $selected_id, $new_item, $submit_on_change, $show_inactive);
	echo "</td>\n";
}

function security_roles_list_row($label, $name, $selected_id=null, $new_item=false, $submit_on_change=false,
	$show_inactive = false)
{
	echo "<tr><td class=''>$label</td>";
	security_roles_list_cells(null, $name, $selected_id, $new_item, $submit_on_change, $show_inactive);
	echo "</tr>\n";
}

function tab_list_row($label, $name, $selected_id=null)
{
	global $installed_extensions;
	
	$tabs = array();
	foreach ($_SESSION['App']->applications as $app) {
		$tabs[$app->id] = access_string($app->name, true);
	}
	echo "<tr>\n";
	echo "<td class=''>$label</td><td>\n";
	echo array_selector($name, $selected_id, $tabs);
	echo "</td></tr>\n";
}

//-----------------------------------------------------------------------------------------------

function tag_list($name, $height, $type, $multi=false, $all=false, $spec_opt = false)
{
	// Get tags
	global $path_to_root;
	include_once($path_to_root . "/admin/db/tags_db.inc");
	$results = get_tags($type, $all);

	while ($tag = db_fetch($results))
		$tags[$tag['id']] = $tag['name'];
	
	if (!isset($tags)) {
		$tags[''] = $all ? _("No tags defined.") : _("No active tags defined.");
		$spec_opt = false;
	}
	return array_selector($name, null, $tags,
		array(
			'multi' => $multi,
			'height' => $height,
			'spec_option'=> $spec_opt,
			'spec_id' => -1,
		) );
}

function tag_list_cells($label, $name, $height, $type, $mult=false, $all=false, $spec_opt = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>\n";
	echo tag_list($name, $height, $type, $mult, $all, $spec_opt);
	echo "</td>\n";
	
}

function tag_list_row($label, $name, $height, $type, $mult=false, $all=false, $spec_opt = false)
{
	echo "<tr><td class=''>$label</td>";
	tag_list_cells(null, $name, $height, $type, $mult, $all, $spec_opt);
	echo "</tr>\n";	
}

//---------------------------------------------------------------------------------------------
//	List of sets of active extensions 
//
function extset_list($name, $value=null, $submit_on_change=false)
{
	global $db_connections;

	$items = array();
	foreach ($db_connections as $comp)
		$items[] = sprintf(_("Activated for '%s'"), $comp['name']);
	return array_selector( $name, $value, $items,
		array(
			'spec_option'=> _("Available and/or installed"),
			'spec_id' => -1,
			'select_submit'=> $submit_on_change,
			'async' => true
		));
}

function crm_category_types_list($name, $selected_id=null, $filter=array(), $submit_on_change=true)
{

	$sql = "SELECT id, name, type, inactive FROM ".TB_PREF."crm_categories";

	$multi = false;
	$groups = false;
	$where = array();
	if (@$filter['class']) {
		$where[] = 'type='.db_escape($filter['class']);
	} else
		$groups = 'type';
	if (@$filter['subclass']) $where[] = 'action='.db_escape($filter['subclass']);
	if (@$filter['entity']) $where[] = 'entity_id='.db_escape($filter['entity']);
	if (@$filter['multi']) { // contact category selector for person
		$multi = true;
	}

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	 	array(
	 		'multi' => $multi,
	 		'height' => $multi ? 5:1,
			'category' => $groups,
	 		'select_submit'=> $submit_on_change,
 			'async' => true,
 			'where' => $where
	 	));
}

function crm_category_types_list_row($label, $name, $selected_id=null, $filter=array(), $submit_on_change=true)
{
	echo "<tr><td class=''>$label</td><td>";
	echo crm_category_types_list($name, $selected_id, $filter, $submit_on_change);
	echo "</td></tr>\n";
}

function payment_type_list_row($label, $name, $selected_id=null, $submit_on_change=false)
{
	global $pterm_types;
	
	echo "<tr><td class=''>$label</td><td>";
	echo array_selector($name, $selected_id, $pterm_types, 
		array( 
			'select_submit'=> $submit_on_change
		) );
	echo "</td></tr>\n";
}

function coa_list_row($label, $name, $value=null)
{
	global $path_to_root, $installed_extensions;

	$path = $path_to_root.'/sql/';
	$coas = array();
	$sqldir = opendir($path);
	while (false !== ($fname = readdir($sqldir)))
	{
		if (is_file($path.$fname) && substr($fname,-4)=='.sql' && @($fname[2] == '_'))
		{
			$ext = array_search_value($fname, $installed_extensions, 'sql');
			if ($ext!=null) {
				$descr = $ext['name'];
			} elseif ($fname == 'en_US-new.sql') { // two standard COAs
				$descr = _("Standard new company American COA (4 digit)");
			} elseif ($fname == 'en_US-demo.sql') {
				$descr = _("Standard American COA (4 digit) with demo data");
			} else
				$descr = $fname;

			$coas[$fname] =  $descr;
		}
	}
	ksort($coas);

	echo "<tr><td class=''>$label</td>\n<td>";
	echo array_selector( $name, $value, $coas );
	echo "</td></tr>\n";
}

function payment_services($name)
{
	global $payment_services;

	$services = array_combine(array_keys($payment_services), array_keys($payment_services));

	return array_selector($name, null, $services, array(
			'spec_option'=> _("No payment Link"),
			'spec_id' => '',
		));
}

function tax_algorithm_list($name, $value=null, $submit_on_change = false)
{
	global $tax_algorithms;
	
	return array_selector($name, $value, $tax_algorithms, 
		array( 
			'select_submit'=> $submit_on_change,
			'async' => true,
			)
	);
}

function tax_algorithm_list_cells($label, $name, $value=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo tax_algorithm_list($name, $value, $submit_on_change);
	echo "</td>\n";
}

function tax_algorithm_list_row($label, $name, $value=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	tax_algorithm_list_cells(null, $name, $value, $submit_on_change);
	echo "</tr>\n";
}

function refline_list($name, $type, $value=null, $spec_option=false)
{
	$sql = "SELECT id, prefix, inactive FROM ".TB_PREF."reflines";

	$where = array();

	if (isset($type))
		$where = array('`trans_type`='.db_escape($type));

	return combo_input($name, $value, $sql, 'id', 'prefix',
		array(
			'order'=>array('prefix'),
			'spec_option' => $spec_option,
			'spec_id' => '',
			'type' => 2,
			'where' => $where,
			'select_submit' => true,
			)
		);
}

function refline_list_row($label, $name, $type, $selected_id=null, $spec_option=false)
{
	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo refline_list($name, $type, $selected_id, $spec_option);
	echo "</td></tr>\n";
}


//----------------------------------------------------------------------------------------------

function subledger_list($name, $account, $selected_id=null)
{

	$type = is_subledger_account($account);
	if (!$type)
		return '';
		global $SysPrefs;
//display_error($SysPrefs->prefs['shortname_name_in_list']);
if (!empty($SysPrefs->prefs['shortname_name_in_list']))
{
	if($type > 0)
		$sql = "SELECT DISTINCT d.debtor_no as id,  CONCAT(debtor_ref,'  ',name) as name 
		FROM "
		.TB_PREF."debtors_master d,"
		.TB_PREF."cust_branch c
		WHERE d.debtor_no=c.debtor_no AND c.receivables_account=".db_escape($account);
	else
		$sql = "SELECT supplier_id as id, supp_ref as name 
		FROM "
		.TB_PREF."suppliers s
		WHERE s.payable_account=".db_escape($account);
		}
		else
		{
		if($type > 0)
		$sql = "SELECT DISTINCT d.debtor_no as id, debtor_ref as name 
		FROM "
		.TB_PREF."debtors_master d,"
		.TB_PREF."cust_branch c
		WHERE d.debtor_no=c.debtor_no AND c.receivables_account=".db_escape($account);
	else
		$sql = "SELECT supplier_id as id, supp_ref as name 
		FROM "
		.TB_PREF."suppliers s
		WHERE s.payable_account=".db_escape($account);
		}

	$mode = get_company_pref('no_customer_list');

	return combo_input($name, $selected_id, $sql, 'id', 'name',
	array(
		'type' => 1,
		'size' => 20,
		'async' => false,
	) );
}

function subledger_list_cells($label, $name, $account, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo subledger_list($name, $account, $selected_id);
	echo "</td>\n";
}

function subledger_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	echo "<tr><td class=''>$label</td><td nowrap>";
	echo subledger_list($name, $account, $selected_id);
	echo "</td>\n</tr>\n";
}

function accounts_type_list_row($label, $name, $selected_id=null)
{
	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";
	$sel = array(_("Numeric"), _("Alpha Numeric"), _("ALPHA NUMERIC"));
	echo array_selector($name, $selected_id, $sel); 
	echo "</td></tr>\n";
}

function users_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $spec_opt=true)
{
    $where = false;
    $sql = " SELECT user_id, real_name FROM ".TB_PREF."users";

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";

    echo combo_input($name, $selected_id, $sql, 'user_id', 'real_name',
        array(
            'spec_option' => $spec_opt===true ?_("All users") : $spec_opt,
            'spec_id' => '',
            'order' => 'real_name',
            'select_submit'=> $submit_on_change,
            'async' => false
        ) );
    echo "</td>";

}

function collations_list_row($label, $name, $selected_id=null)
{
	global $supported_collations;

	echo "<tr>";
	if ($label != null)
		echo "<td class=''>$label</td>\n";
	echo "<td>";

	echo array_selector($name, $selected_id, $supported_collations, 
		array('select_submit'=> false) );
	echo "</td></tr>\n";
}
//


function wth_tax_type_list($name, $selected_id=null, $special_option=false, $submit_on_change = false,
						   $enabled=false, $level_id = 0,$id)
{
	$sql = "SELECT `id`, `description`,CONCAT(co_account,' -',tax_percent) FROM `".TB_PREF."wth_tax_types`  
	WHERE wth_tax_category = $id";

	$where = $enabled ? array("level_id =".$level_id, ) : array();
	return combo_input($name, $selected_id, $sql, 'id', 'description',array(
		'spec_option' => $special_option===true ? ' ' : $special_option,
		'order' => 'description',
		'where' => $where,
		'spec_id' => 0,
		'format' => '_format_wth_tax_type',
		'select_submit'=> $submit_on_change,
	));
}

function wth_tax_type_list_cells($label, $name, $selected_id=null, $special_option=false, $submit_on_change = false,  $enabled=false, $level_id = 0,$id)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo wth_tax_type_list($name, $selected_id, $special_option, $submit_on_change,  $enabled, $level_id,$id);
	echo "</td>\n";
}

function wth_tax_type_list_row($label, $name, $selected_id=null, $special_option=false, $submit_on_change = false,  $enabled=false, $level_id = 0)
{
	echo "<tr><td class=''>$label</td>";
	wth_tax_type_list_cells(null, $name, $selected_id, $special_option, $submit_on_change,  $enabled, $level_id);
	echo "</tr>\n";
}
function _format_wth_tax_type($row)
{
	return $row[1]." - " .$row[2]."%";
}
//-------------
//Ryan :06-05-17
function wht_tax($name, $selected_id=null, $supply_tax="", $service_tax="", $fbr_tax="", $srb_tax="", $submit_on_change=false)
{
	$items = array();
	$items['1'] = strlen($supply_tax) ? $supply_tax : _("WHT Supply Tax");
	$items['2'] = strlen($service_tax) ? $service_tax : _("WHT Service Tax");
	$items['3'] = strlen($fbr_tax) ? $fbr_tax : _("WHT FBR Tax");
	$items['4'] = strlen($srb_tax) ? $srb_tax : _("WHT SRB Tax");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function wht_tax_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo wht_tax($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function wht_tax_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	wht_tax_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}


// S.H


function discount_algorithm_list($name, $value=null, $submit_on_change = false)
{
	global $Discount_Algorithms;

	return array_selector($name, $value, $Discount_Algorithms,
		array(
			'select_submit'=> $submit_on_change,
			'async' => true,
		)
	);
}

function discount_algorithm_list_cells($label, $name, $value=null, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo discount_algorithm_list($name, $value, $submit_on_change);
	echo "</td>\n";
}

function discount_algorithm_list_row($label, $name, $value=null, $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	discount_algorithm_list_cells(null, $name, $value, $submit_on_change);
	echo "</tr>\n";
}
///marina


function cashflow_categories_list($name, $selected_id=null)
{
	$sql = "SELECT cc.id, cc.name AS name, ct.name AS cname, cc.inactive 
	FROM ".TB_PREF."cashflow_categories cc, ".TB_PREF."cashflow_type ct
	WHERE cc.c_type = ct.id
	
	";
	return combo_input($name, $selected_id, $sql, 'id', 'name', array(
		'async' => true,
		'category' => 2,
		'order' => array('ct.id','cc.name')

	));
}

function cashflow_categories_list_cells($label, $name, $selected_id=null)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo cashflow_categories_list($name, $selected_id);
	echo "</td>\n";
}

function cashflow_categories_list_row($label, $name, $selected_id=null)
{
	echo "<tr><td class=''>$label</td>";
	cashflow_categories_list_cells(null, $name, $selected_id);
	echo "</tr>\n";
}

//---------------------------------    marina   ----------------------------------------------------------------------
/////////////////////////////////////////////////////////
function cashflow_cat_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$show_inactive=false, $editkey = false)
{

//	$sql = "SELECT id,name FROM " . TB_PREF . "cashflow_type";
//
	$sql = "SELECT id, name, inactive FROM " . TB_PREF . "cashflow_type";
	return combo_input($name, $selected_id, $sql, 'id', 'name', array());
	$mode = get_company_pref('no_customer_list');
////
//	if ($editkey)
//		set_editor('customer', $name, $editkey);

//	$ret = combo_input($name, $selected_id, $sql, 'id','name',
//	array(
////	    'format' => '_format_add_curr',
//	    'order' => array('name'),
////		'search_box' => $mode!=0,
////		'type' => 1,
////		'size' => 20,
//        'search' => array("name"),
////		'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
////		'spec_id' => ALL_TEXT,
////		'select_submit'=> $submit_on_change,
//		'async' => false,
////		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
////		_('Select customer'),
//		'show_inactive' => $show_inactive,
////		'editlink' => $editkey ? add_edit_combo('customer') : false
//	), "customer" );
//	return $ret;
}

function cashflow_cat_list_cells($label, $name, $selected_id=null, $all_option=false,
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo cashflow_cat_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function cashflow_cat_list_row($label, $name, $selected_id=null, $all_option = false,
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{

	echo "<tr><td class=''>$label</td><td nowrap>";
	echo cashflow_cat_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}


//-------------------------------------------------------------------------------

function combo1_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{$sql = "SELECT combo_code, 	description, inactive FROM ".TB_PREF."$name";
	return combo_input($name, $selected_id, $sql, 'area_code', 'description',
		array('spec_option' => $spec_opt));
//	$items = array();
//	$items['0'] = strlen($name_no) ? $name_no : _("No");
//	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");
//
//	return array_selector($name, $selected_id, $items,
//		array(
//			'select_submit'=> $submit_on_change,
//			'async' => false ) ); // FIX?
}

function combo1_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo1_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function combo1_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	combo1_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}

//////----------------------for combo1 dropdown for hsfhd-----------------////

function combo1_po_list($name, $selected_id=null, $spec_option=false )
{
    $sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo1";

    return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
        'spec_option' => $spec_option === true ? _("All") : $spec_option,
    ));
}

function combo1_po_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo combo1_po_list($name, $selected_id, $spec_option);
    echo "</td>\n";
}

function combo1_po_list_row($label, $name, $selected_id=null, $spec_option=false)
{
    echo "<tr><td class=''>$label</td>";
    combo1_po_list_cells(null, $name, $selected_id, $spec_option);
    echo "</tr>\n";
}
//-------------------------------------------------------------------------------
// function combo2_list($name, $selected_id=null, $spec_option=false)
// {
// 	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo2";

// 	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
// 		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
// 	));

// }

// function combo2_list_cells($label,$name, $selected_id=null, $spec_option=false)
// {
// 	if ($label != null)
// 		echo "<td>$label</td>\n";
// 	echo "<td>";
// 	echo combo2_list($name, $selected_id, $spec_option);
// 	echo "</td>\n";
// }

// function combo2_list_list_row($label,$name, $selected_id=null, $spec_option=false)
// {
// 	echo "<tr><td class=''>$label</td>";
// 	combo2_list_cells(null, $name, $selected_id, $spec_option);
// 	echo "</tr>\n";
// }

//-------------------------------------------------------------------------------

// function combo3_list($name, $selected_id=null, $spec_option=false)
// {
// 	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo3";

// 	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
// 		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
// 	));

// }

// function combo3_list_cells($label,$name, $selected_id=null, $spec_option=false)
// {
// 	if ($label != null)
// 		echo "<td>$label</td>\n";
// 	echo "<td>";
// 	echo combo3_list($name, $selected_id, $spec_option);
// 	echo "</td>\n";
// }

// function combo3_list_list_row($label,$name, $selected_id=null, $spec_option=false)
// {
// 	echo "<tr><td class=''>$label</td>";
// 	combo3_list_cells(null, $name, $selected_id, $spec_option);
// 	echo "</tr>\n";
// }
// ////

// function combo4_list($name, $selected_id=null, $spec_option=false)
// {
// 	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo4";

// 	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
// 		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
// 	));

// }

// function combo4_list_cells($label,$name, $selected_id=null, $spec_option=false)
// {
// 	if ($label != null)
// 		echo "<td>$label</td>\n";
// 	echo "<td>";
// 	echo combo4_list($name, $selected_id, $spec_option);
// 	echo "</td>\n";
// }

// function combo4_list_list_row($label,$name, $selected_id=null, $spec_option=false)
// {
// 	echo "<tr><td class=''>$label</td>";
// 	combo4_list_cells(null, $name, $selected_id, $spec_option);
// 	echo "</tr>\n";
// }



// function combo5_list($name, $selected_id=null, $spec_option=false)
// {
// 	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo5";

// 	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
// 		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
// 	));

// }

// function combo5_list_cells($label,$name, $selected_id=null, $spec_option=false)
// {
// 	if ($label != null)
// 		echo "<td>$label</td>\n";
// 	echo "<td>";
// 	echo combo5_list($name, $selected_id, $spec_option);
// 	echo "</td>\n";
// }

// function combo5_list_list_row($label,$name, $selected_id=null, $spec_option=false)
// {
// 	echo "<tr><td class=''>$label</td>";
// 	combo5_list_cells(null, $name, $selected_id, $spec_option);
// 	echo "</tr>\n";
// }


// function combo6_list($name, $selected_id=null, $spec_option=false)
// {
// 	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo6";

// 	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
// 		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
// 	));

// }

// function combo6_list_cells($label,$name, $selected_id=null, $spec_option=false)
// {
// 	if ($label != null)
// 		echo "<td>$label</td>\n";
// 	echo "<td>";
// 	echo combo6_list($name, $selected_id, $spec_option);
// 	echo "</td>\n";
// }

// function combo6_list_list_row($label,$name, $selected_id=null, $spec_option=false)
// {
// 	echo "<tr><td class=''>$label</td>";
// 	combo6_list_cells(null, $name, $selected_id, $spec_option);
// 	echo "</tr>\n";
// }

function combo2_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo2";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
	));

}

function combo2_list_cells($label,$name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo2_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function combo2_list_list_row($label,$name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	combo2_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}

//-------------------------------------------------------------------------------

function combo3_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo3";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
	));

}

function combo3_list_cells($label,$name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo3_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function combo3_list_list_row($label,$name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	combo3_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}
////

function combo4_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo4";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
	));

}

function combo4_list_cells($label,$name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo4_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function combo4_list_list_row($label,$name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	combo4_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}



function combo5_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo5";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
	));

}

function combo5_list_cells($label,$name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo5_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function combo5_list_list_row($label,$name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	combo5_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}


function combo6_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT combo_code, description, inactive FROM ".TB_PREF."combo6";

	return combo_input($name, $selected_id, $sql, 'combo_code', 'description', array(
		'spec_option' => $spec_option === true ? _("All ") : $spec_option,
	));

}

function combo6_list_cells($label,$name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combo6_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function combo6_list_list_row($label,$name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	combo6_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}


//////----------------------for sales type list-----------------------//////
function sales_type_list($name, $selected_id=null, $spec_option=false )
{
	$sql = "SELECT id,sales_type, inactive FROM ".TB_PREF."sales_types";

	return combo_input($name, $selected_id, $sql, 'id', 'sales_type', array(
		'spec_option' => $spec_option === true ? _("All Sales Types") : $spec_option,
	));
}

function sales_type_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sales_type_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function sales_type_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	sales_type_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}
//////----------------------for payment terms list-----------------------//////
function payment_term_list($name, $selected_id=null, $spec_option=true)
{
	$sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";

	return combo_input($name, $selected_id, $sql, 'terms_indicator', 'terms', array(
		'spec_option' => $spec_option === true ? _("All Payment Terms") : $spec_option,
	));
}

function payment_term_list_cells($label, $name, $selected_id=null,$spec_option=false)
{
	if ($label != null)

		echo "<td>$label</td>\n";
	echo "<td>";
	echo payment_term_list($name, $selected_id,$spec_option);
	echo "</td>\n";
}

function payment_term_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	payment_term_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}
/////----------------------for Credit Status list-----------------------//////
function credit_stats_list($name, $selected_id=null, $spec_option=false)
{
	$sql = "SELECT id, reason_description, inactive FROM ".TB_PREF."credit_status
";
	return combo_input($name, $selected_id, $sql, 'id', 'reason_description', array(
		'spec_option' => $spec_option === true ? _("All Credit Status") : $spec_option,
	));
}

function credit_stats_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo credit_stats_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function credit_stats_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	credit_stats_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}


//hamza
function check_box($name, $trans_no=1, $onclick=false, $class=false, $class_name='', $value=null){
	$check_box = "<input type='checkbox' ";

	if($value == null)
		$value = get_post($name,0);

	$use_class = $class_name ? $class_name : 'all_boxes';

	if($class)
		$check_box .= " class='$use_class' ";
	$check_box .= " name='$name'  ";

	if($onclick)
		$check_box .= "onClick='".$onclick."' ";

	$check_box .= ($value == 1 ? ' checked':'');

	$check_box .= " >";

	return $check_box;
}
//------------------------------------------------------------------------------------------------

function batch_list($batch, $name, $selected_id=null,
					$spec_option = true, $enabled=true, $submit_on_change=true, $editkey = false,
					$Where,$show_all)
{

	global $all_items;


	if($Where != '') {

		$sql = "SELECT 0_batch.id, 0_batch.name, SUM(0_stock_moves.qty) as quantity, 0_batch.exp_date , stock_id 
                FROM 0_stock_moves,0_batch 
                WHERE 0_stock_moves.batch = 0_batch.id
                AND `stock_id` = " . db_escape($batch) . " 
                AND `loc_code` = " . db_escape($Where) . " 
                GROUP BY batch
                ";
                if($show_all != 1)
               $sql.=" having SUM(qty) > 0
               
                ";
	}
	else {
		$sql = "  SELECT 0_batch.id, 0_batch.name, SUM(0_stock_moves.qty) as quantity, 0_batch.exp_date , stock_id 
                FROM 0_stock_moves,0_batch 
                WHERE 0_stock_moves.batch = 0_batch.id
                AND `stock_id`=" . db_escape($batch) . " 
                group by batch     ";
                
                if($show_all != 1)
               $sql.=" having SUM(qty) > 0
               
                ";
	}

		$ret = combo_input($name, $selected_id, $sql, 'id', 'name',
		array(
			'format' => '_format_batch',
			'order' => array('tran_date'),
			'spec_id' => $all_items,
			'spec_option' => $spec_option === true ? _("All") : $spec_option,
			'select_submit'=> $submit_on_change,
			'async' => false
		) );

	return $ret;
}

function batch_list_cells($label,$customer_id, $name, $selected_id=null,
						  $all_option = true, $enabled=true, $submit_on_change=false, $editkey = false, $Where,$show_all)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo batch_list($customer_id, $name, $selected_id, $all_option, $enabled,
		$submit_on_change, $editkey, $Where,$show_all);
	echo "</td>\n";
}

function batch_list_row($label, $customer_id, $name, $selected_id=null,
						$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	echo "<tr><td class=''>$label</td>";
	batch_list_cells(null, $customer_id, $name, $selected_id,
		$all_option, $enabled, $submit_on_change, $editkey);
	echo "</tr>";
}

function batches_list($batch, $name, $selected_id=null,
	$spec_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	global $all_items;

	$sql = "select batch 
			from 0_stock_moves 
			WHERE `stock_id`=".db_escape($batch)." 
			group by batch ";
	$ret = combo_input($name, $selected_id, $sql, 'batch', 'quantity',
	array(
		'format' => '_format_stock_items',
		'order' => array('batch'),
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
	) );
	
	return $ret;
}

function batches_list_cells($label,$customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo batches_list($customer_id, $name, $selected_id, $all_option, $enabled, 
		$submit_on_change, $editkey);
	echo "</td>\n";
}

function batches_list_row($label, $customer_id, $name, $selected_id=null, 
	$all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
	echo "<tr><td >$label</td>";
	batches_list_cells(null, $customer_id, $name, $selected_id, 
		$all_option, $enabled, $submit_on_change, $editkey);
	echo "</tr>";
}
function yes_no($id)
{
	if($id == 1)
		$name="Yes";
	else
		$name="No";
		
	

	return $name;
	
}
//----------------------marina for dashboards----------------------

function currencies_dashboard_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, $exclude_home_curr=false, $allow=0)
{
	$sql = "SELECT curr_abrev, currency, inactive FROM ".TB_PREF."currencies";
	if ($exclude_home_curr)
		$sql .= " WHERE curr_abrev!='".get_company_currency()."'";

	if($allow == 1)
		return combo_input($name, $selected_id, $sql, 'curr_abrev', 'currency',
			array(
				'spec_option' => $spec_option === true ? _("All Currencies") : $spec_option,
				'select_submit'=> $submit_on_change,
				'async' => false
			) );
	elseif($allow == 0)
		return combo_input($name, $selected_id, $sql, 'curr_abrev', 'currency',
			array(
				'spec_option' => $spec_option === true ? _("All Currencies") : $spec_option,
				'select_submit'=> $submit_on_change,
				'default' => get_company_currency(),
				'async' => false
			) );
}

function currencies_dashboard_list_cells($label, $name, $selected_id=null, $spec_option=false, $submit_on_change=false, $allow=0)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo currencies_dashboard_list($name, $selected_id, $spec_option, $submit_on_change,false, $allow);
	echo "</td>\n";
}

function currencies_dashboard_list_row($label, $name, $selected_id=null, $spec_option=false, $submit_on_change=false, $allow=0)
{
	echo "<tr><td class=''>$label</td>";
	currencies_dashboard_list_cells(null, $name, $selected_id, $spec_option, $submit_on_change, false, $allow);
	echo "</tr>\n";
}

//-----------------------------------------------------------------------


function custom1_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['2'] = strlen($name_no) ? $name_no : _("All");
	$items['0'] = strlen($name_yes) ? $name_yes : _("Approved");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Unapproved");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function custom1_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo custom1_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function custom1_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	custom1_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}
////////////////////////////////////////////////For Payroll//////
//----iqra 2017
function intime_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("AM");
    $items['1'] = strlen($name_yes) ? $name_yes : _("PM");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}
function intimehours_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("0");
    $items['1'] = strlen($name_yes) ? $name_yes : _("1");
    $items['2'] = strlen($name_yes) ? $name_yes : _("2");
    $items['3'] = strlen($name_yes) ? $name_yes : _("3");
    $items['4'] = strlen($name_yes) ? $name_yes : _("4");
    $items['5'] = strlen($name_yes) ? $name_yes : _("5");
    $items['6'] = strlen($name_yes) ? $name_yes : _("6");
    $items['7'] = strlen($name_yes) ? $name_yes : _("7");
    $items['8'] = strlen($name_yes) ? $name_yes : _("8");
    $items['9'] = strlen($name_yes) ? $name_yes : _("9");
    $items['10'] = strlen($name_yes) ? $name_yes : _("10");
    $items['11'] = strlen($name_yes) ? $name_yes : _("11");
    $items['12'] = strlen($name_yes) ? $name_yes : _("12");
    $items['13'] = strlen($name_yes) ? $name_yes : _("13");
    $items['14'] = strlen($name_yes) ? $name_yes : _("14");
    $items['15'] = strlen($name_yes) ? $name_yes : _("15");
    $items['16'] = strlen($name_yes) ? $name_yes : _("16");
    $items['17'] = strlen($name_yes) ? $name_yes : _("17");
    $items['18'] = strlen($name_yes) ? $name_yes : _("18");
    $items['19'] = strlen($name_yes) ? $name_yes : _("19");
    $items['20'] = strlen($name_yes) ? $name_yes : _("20");
    $items['21'] = strlen($name_yes) ? $name_yes : _("21");
    $items['22'] = strlen($name_yes) ? $name_yes : _("22");
    $items['23'] = strlen($name_yes) ? $name_yes : _("23");
    $items['24'] = strlen($name_yes) ? $name_yes : _("24");


    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}
function intimemin_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("0");
    $items['1'] = strlen($name_yes) ? $name_yes : _("1");
    $items['2'] = strlen($name_yes) ? $name_yes : _("2");
    $items['3'] = strlen($name_yes) ? $name_yes : _("3");
    $items['4'] = strlen($name_yes) ? $name_yes : _("4");
    $items['5'] = strlen($name_yes) ? $name_yes : _("5");
    $items['6'] = strlen($name_yes) ? $name_yes : _("6");
    $items['7'] = strlen($name_yes) ? $name_yes : _("7");
    $items['8'] = strlen($name_yes) ? $name_yes : _("8");
    $items['9'] = strlen($name_yes) ? $name_yes : _("9");
    $items['10'] = strlen($name_yes) ? $name_yes : _("10");
    $items['11'] = strlen($name_yes) ? $name_yes : _("11");
    $items['12'] = strlen($name_yes) ? $name_yes : _("12");
    $items['13'] = strlen($name_yes) ? $name_yes : _("13");
    $items['14'] = strlen($name_yes) ? $name_yes : _("14");
    $items['15'] = strlen($name_yes) ? $name_yes : _("15");
    $items['16'] = strlen($name_yes) ? $name_yes : _("16");
    $items['17'] = strlen($name_yes) ? $name_yes : _("17");
    $items['18'] = strlen($name_yes) ? $name_yes : _("18");
    $items['19'] = strlen($name_yes) ? $name_yes : _("19");
    $items['20'] = strlen($name_yes) ? $name_yes : _("20");
    $items['21'] = strlen($name_yes) ? $name_yes : _("21");
    $items['22'] = strlen($name_yes) ? $name_yes : _("22");
    $items['23'] = strlen($name_yes) ? $name_yes : _("23");
    $items['24'] = strlen($name_yes) ? $name_yes : _("24");
    $items['25'] = strlen($name_yes) ? $name_yes : _("25");
    $items['26'] = strlen($name_yes) ? $name_yes : _("26");
    $items['27'] = strlen($name_yes) ? $name_yes : _("27");
    $items['28'] = strlen($name_yes) ? $name_yes : _("28");
    $items['29'] = strlen($name_yes) ? $name_yes : _("29");
    $items['30'] = strlen($name_yes) ? $name_yes : _("30");
    $items['31'] = strlen($name_yes) ? $name_yes : _("31");
    $items['32'] = strlen($name_yes) ? $name_yes : _("32");
    $items['33'] = strlen($name_yes) ? $name_yes : _("33");
    $items['34'] = strlen($name_yes) ? $name_yes : _("34");
    $items['35'] = strlen($name_yes) ? $name_yes : _("35");
    $items['36'] = strlen($name_yes) ? $name_yes : _("36");
    $items['37'] = strlen($name_yes) ? $name_yes : _("37");
    $items['38'] = strlen($name_yes) ? $name_yes : _("38");
    $items['39'] = strlen($name_yes) ? $name_yes : _("39");
    $items['40'] = strlen($name_yes) ? $name_yes : _("40");
    $items['41'] = strlen($name_yes) ? $name_yes : _("41");
    $items['42'] = strlen($name_yes) ? $name_yes : _("42");
    $items['43'] = strlen($name_yes) ? $name_yes : _("43");
    $items['44'] = strlen($name_yes) ? $name_yes : _("44");
    $items['45'] = strlen($name_yes) ? $name_yes : _("45");
    $items['46'] = strlen($name_yes) ? $name_yes : _("46");
    $items['47'] = strlen($name_yes) ? $name_yes : _("47");
    $items['48'] = strlen($name_yes) ? $name_yes : _("48");
    $items['49'] = strlen($name_yes) ? $name_yes : _("49");
    $items['50'] = strlen($name_yes) ? $name_yes : _("50");
    $items['51'] = strlen($name_yes) ? $name_yes : _("51");
    $items['52'] = strlen($name_yes) ? $name_yes : _("52");
    $items['53'] = strlen($name_yes) ? $name_yes : _("53");
    $items['54'] = strlen($name_yes) ? $name_yes : _("54");
    $items['55'] = strlen($name_yes) ? $name_yes : _("55");
    $items['56'] = strlen($name_yes) ? $name_yes : _("56");
    $items['57'] = strlen($name_yes) ? $name_yes : _("57");
    $items['58'] = strlen($name_yes) ? $name_yes : _("58");
    $items['59'] = strlen($name_yes) ? $name_yes : _("59");



    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function intimepm_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("PM");
    $items['1'] = strlen($name_yes) ? $name_yes : _("AM");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function intime_list_cells($label, $name, $name2,$name3, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td style='border-color: #989b9e;'>";
    echo  intimehours_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo  intimemin_list($name2, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo  intime3_list($name3, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function intimepm_list_cells($label, $name,$name2, $name3, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td style='border-color: #989b9e;'>";

    echo  intimehours_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo  intimemin_list($name2, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo  intimepm_list($name3, $selected_id, $name_yes, $name_no, $submit_on_change);

    echo "</td>\n";
}
function  intime_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    intime_list_cells(null, $name, $selected_id, $name_yes, $name_no,$submit_on_change);
    echo "</tr>\n";
}

function intime2_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("AM");
    $items['1'] = strlen($name_yes) ? $name_yes : _("PM");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function  intime2_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo  intime2_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function intime3_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items["AM"] = strlen($name_no) ? $name_no : _("AM");
    $items["PM"] = strlen($name_yes) ? $name_yes : _("PM");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function  intime3_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo  intime3_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}


////iqra


function _format_fiscalyearss($row)
{
    return sql2date($row[1]) . "&nbsp;-&nbsp;" . sql2date($row[2])
        . "&nbsp;&nbsp;" . ($row[3] ? _('Closed') : _('Active'));

}
function fiscalyears_list_neww($name, $selected_id=null, $submit_on_change=false)
{

    $sql = "SELECT * FROM ".TB_PREF."fiscal_year";

    // default to the company current fiscal year

    return combo_input($name, $selected_id, $sql, 'id', '',
        array(
            'order' => 'begin',
            'format' => '_format_fiscalyearss',
            'select_submit'=> $submit_on_change,
            'async' => false
        ) );
}

function fiscalyearss_list_cells($label, $name, $selected_id=null,$submit_on_change=true)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo fiscalyears_list_neww($name, $selected_id,$submit_on_change);
    echo "</td>\n";
}


//function number_list($name, $selected, $from, $to, $no_option=false, //$submit_on_change=false)
//{
  //  $items = array();
    //for ($i = $from; $i <= $to; $i++)
    //{
      //  if($i==0)
        //    $items[$i] ='0';
          //  elseif($i==1)
        //    $items[$i] ='1';
    //    elseif($i==2)
      //      $items[$i] ='2';
//        elseif($i==3)
  //          $items[$i] ='3';
    //    elseif($i==4)
      //      $items[$i] ='4';


    //}


//    return array_selector($name, $selected, $items,
  //      array(	'select_submit'=> $submit_on_change,
    //        'spec_option' => $no_option===true ? 'Select' : $no_option,
      //      'spec_id' => ALL_NUMERIC,
        //    'async' => false) );
//}
function number_list($name, $selected, $from, $to, $no_option=false, $submit_on_change=true)
{
    $items = array();
    for ($i = $from; $i <= $to; $i++)
    {
       if($i==0)
           $items[$i] ='0';
            elseif($i==1)
           $items[$i] ='1';
       elseif($i==2)
            $items[$i] ='2';
       elseif($i==3)
           $items[$i] ='3';
       elseif($i==4)
           $items[$i] ='4';
	   elseif($i==5)
           $items[$i] ='5';
       elseif($i==6)
           $items[$i] ='6';
           elseif($i==7)
           $items[$i] ='7';
           elseif($i==8)
           $items[$i] ='8';
           elseif($i==9)
           $items[$i] ='9';
           elseif($i==10)
           $items[$i] ='10';
    }



    return array_selector($name, $selected, $items,
        array(	'select_submit'=> $submit_on_change,
            'spec_option' => $no_option===true ? 'Select' : $no_option,
            'spec_id' => ALL_NUMERIC,
            'async' => false) );
}
function number_list_cells($label, $name, $selected, $from, $to, $no_option=false, $submit_on_change=false)
{
    if ($label != null)
        label_cell($label);
    echo "<td>\n";
    echo number_list($name, $selected, $from, $to, $no_option,$submit_on_change);
    echo "</td>\n";
}

function number_list_row($label, $name, $selected, $from, $to, $no_option=false, $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    echo number_list_cells(null, $name, $selected, $from, $to, $no_option,$submit_on_change);
    echo "</tr>\n";
}
function division_title($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                        $all=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, name FROM ".TB_PREF."dimensions where type_='1' ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'name',
        array(
            //'format' => '_format_add_curr',
            'order' => array('name'),
            'search_box' => $mode!=0,
            'type' => 1,
            'spec_option' => $spec_option === true ? _("All Divisions") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select employee'),
            'show_inactive'=>$all
        ));
    if ($editkey)
        $ret .= add_edit_combo('supplier');
    return $ret;
}

function division_title_cells($label, $name, $selected_id=null, $all_option=false,
                              $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td><td>\n";
    echo division_title($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td>\n";
}

function division_title_row($label, $name, $selected_id=null, $all_option = false,
                            $submit_on_change=false, $all=false, $editkey = false)
{
    echo "<tr><td class=''>$label</td><td>";
    echo division_title($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td></tr>\n";
}

//////////////////////////////////////////////////////////////////
function project_title($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                       $all=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, name FROM ".TB_PREF."dimensions where type_='2' ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'name',
        array(
            //'format' => '_format_add_curr',
            'order' => array('name'),
            'search_box' => $mode!=0,
            'type' => 1,
            'spec_option' => $spec_option === true ? _("All Projects") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select employee'),
            'show_inactive'=>$all
        ));
    if ($editkey)
        $ret .= add_edit_combo('supplier');
    return $ret;
}

function project_title_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td><td>\n";
    echo project_title($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td>\n";
}

function project_title_row($label, $name, $selected_id=null, $all_option = false,
                           $submit_on_change=false, $all=false, $editkey = false)
{
    echo "<tr><td class=''>$label</td><td>";
    echo project_title($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td></tr>\n";
}
function emp_list_cells($label, $name, $selected_id=null, $all_option=false,
                        $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo emp_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}

function emp_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                  $show_inactive=false, $editkey = false)
{
    global $all_items;

//	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";
    $sql = "SELECT employee_id,emp_code,  emp_name FROM ".TB_PREF."employee ";
    $mode = get_company_pref('no_customer_list');

    /*$sql = "SELECT employee_id, emp_code, emp_name FROM ".TB_PREF."employee ";
    $ret = combo_input($name, $selected_id, $sql,  'employee_id', 'emp_name',*/

    if ($editkey)
        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'employee_id', 'emp_name',
        array(
            'format' => '_format_add_curr',
            'order' => array('employee_id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select customer'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}


function employe_types_list_row($label, $name, $selected_id=null, $enabled=true)
{
    global $employee_types;

    echo "<tr>";
    if ($label != null)
        echo "<td class=''>$label</td>\n";
    echo "<td>";

    echo array_selector($name, $selected_id, $employee_types,
        array(
            'select_submit'=> true,
            'disabled' => !$enabled) );
    echo "</td></tr>\n";
}
function employe_types_list_row2($label, $name, $selected_id=null, $enabled=true,$submit_on_change=false)
{
    global $employee_types;

    echo "<tr>";
    if ($label != null)
        echo "<td class=''>$label</td>\n";
    echo "<td>";

    echo array_selector($name, $selected_id, $employee_types,
        array(
            'select_submit' => $submit_on_change,
            'disabled' => !$enabled) );
    echo "</td></tr>\n";
}
//13-05-2017
function pro_list_row($label, $name, $selected_id=null, $no_option=false, $showname=null,
                      $showclosed=false, $showtype=0, $submit_on_change=false,$division_id)
{
    echo "<tr><td class=''>$label</td>";
    pro_list_cells(null, $name, $selected_id, $no_option, $showname,
        $showclosed, $showtype, $submit_on_change,$division_id);
    echo "</tr>\n";
}
function pro_list_cells($label, $name, $selected_id=null, $no_option=false, $showname=null,
                        $showclosed=false, $showtype=0, $submit_on_change=false,$division_id)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo pro_list($name, $selected_id, $no_option, $showname, $submit_on_change, $showclosed, $showtype,$division_id);
    echo "</td>\n";
}
function pro_list($name, $selected_id=null, $no_option=false, $showname=' ',
                  $submit_on_change=false, $showclosed=false, $showtype=0,$division_id=0)
{

    $user=$_SESSION["wa_current_user"]->user;
   // if(($_SESSION["wa_current_user"]->access)=='2')
    {
        $sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM ".TB_PREF."dimensions where type_=".db_escape($showtype);
    }
   /* else {
        $sql = "SELECT ".TB_PREF."dimensions.id, CONCAT(".TB_PREF."dimensions.reference,' ',".TB_PREF."dimensions.name) as ref 
FROM ".TB_PREF."dimensions , ".TB_PREF."user_salesman WHERE ".TB_PREF."dimensions.id=".TB_PREF."user_salesman.project
AND ".TB_PREF."user_salesman.user_id=$user";
    }*/

    //$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM ".TB_PREF."dimensions where type_=".db_escape($showtype);

    if($division_id !=0)
        $sql.=" AND main_division=".db_escape($division_id)."";

    $options = array(
        'order' => 'reference',
        'spec_option' => $no_option === true ? $no_option : $no_option,
        'spec_id' => 0,
        'select_submit'=> $submit_on_change,
        'async' => false,
    );

    /*	if (!$showclosed)
            $options['where'][] = "closed=0";
        if($showtype)
            $options['where'][] = "type_=".db_escape($showtype);*/


    return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
}
function loc_list_row($label, $name, $selected_id=null, $no_option=false, $showname=null,
                      $showclosed=false, $showtype=0, $submit_on_change=false,$project)
{
    echo "<tr><td class=''>$label</td>";
    loc_list_cells(null, $name, $selected_id, $no_option, $showname,
        $showclosed, $showtype, $submit_on_change,$project);
    echo "</tr>\n";
}
function loc_list_cells($label, $name, $selected_id=null, $no_option=false, $showname=null,
                        $showclosed=false, $showtype=0, $submit_on_change=false,$project)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo loc_list($name, $selected_id, $no_option, $showname, $submit_on_change, $showclosed, $showtype,$project);
    echo "</td>\n";
}
function loc_list($name, $selected_id=null, $no_option=false, $showname=' ',
                  $submit_on_change=false, $showclosed=false, $showtype=0,$project=0)
{
    $user=$_SESSION["wa_current_user"]->user;
   // if(($_SESSION["wa_current_user"]->access)=='2')
    {
        $sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM ".TB_PREF."dimensions where type_=".db_escape($showtype);
    }
  /*  else {
        $sql = "SELECT ".TB_PREF."dimensions.id, CONCAT(".TB_PREF."dimensions.reference,' ',".TB_PREF."dimensions.name) as ref 
FROM ".TB_PREF."dimensions , ".TB_PREF."user_salesman WHERE ".TB_PREF."dimensions.id=".TB_PREF."user_salesman.location
AND ".TB_PREF."user_salesman.user_id=$user";
    }*/
    //$sql = "SELECT id, CONCAT(reference,'  ',name) as ref FROM ".TB_PREF."dimensions where type_=".db_escape($showtype);

    if($project !=0)
        $sql.=" AND main_project=".db_escape($project)."";

    $options = array(
        'order' => 'reference',
        'spec_option' => $no_option === true ? $no_option : $no_option,
        'spec_id' => 0,
        'select_submit'=> $submit_on_change,
        'async' => false,
    );

    /*	if (!$showclosed)
            $options['where'][] = "closed=0";
        if($showtype)
            $options['where'][] = "type_=".db_escape($showtype);*/


    return combo_input($name, $selected_id, $sql, 'id', 'ref', $options);
}
function marital_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("Single");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Married");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function marital_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo marital_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function marital_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    marital_list_cells(null, $name, $selected_id, $name_yes, $name_no,$submit_on_change);
    echo "</tr>\n";
}

function emp_dept($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                  $all=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, description FROM ".TB_PREF."dept ";
    $ret = combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            //'format' => '_format_add_curr',
            'order' => array('description'),
            'search_box' => $mode!=0,
            'type' => 1,
            'spec_option' => $spec_option === true ? _("All Division") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select employee'),
            'show_inactive'=>$all
        ));
    if ($editkey)
        $ret .= add_edit_combo('supplier');
    return $ret;
}

function emp_dept_cells($label, $name, $selected_id=null, $all_option=false,
                        $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td><td>\n";
    echo emp_dept($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td>\n";
}

function emp_dept_row($label, $name, $selected_id=null, $all_option = false,
                      $submit_on_change=false, $all=false, $editkey = false)
{
    echo "<tr><td class=''>$label</td><td>";
    echo emp_dept($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td></tr>\n";
}
function salary_list($name, $selected_id=null, $special_option=false)
{
    $sql = "SELECT id, description, inactive FROM ".TB_PREF."salary";
    return combo_input($name, $selected_id, $sql, 'id', 'description', array(
        'spec_option' => $special_option===true ? ' ' : $special_option,
        'order' => 'description', 'spec_id' => 0,
    ));
}

function salary_list_cells($label, $name, $selected_id=null, $special_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo salary_list($name, $selected_id, $special_option);
    echo "</td>\n";
}

function salary_list_row($label, $name, $selected_id=null, $special_option=false)
{
    echo "<tr><td class=''>$label</td>";
    salary_list_cells(null, $name, $selected_id, $special_option);
    echo "</tr>\n";
}





function yesno_list_row_new($label, $name, $selected_id=null, $name_yes="", $name_no="", $rejoin="",$submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_list_cells_new(null, $name, $selected_id, $name_yes, $name_no,$rejoin, $submit_on_change);
    echo "</tr>\n";
}
function yesno_list_cells_new($label, $name, $selected_id=null, $name_yes="", $name_no="",$rejoin="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo new_yesno_list($name, $selected_id, $name_yes, $name_no,$rejoin, $submit_on_change);
    echo "</td>\n";
}
function new_yesno_list($name, $selected_id=null, $name_yes="", $name_no="",$rejoin="",$submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("No");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Yes");
    $items['2'] = strlen($name_yes) ? $rejoin : _("No");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}
function month_list_cells($label, $name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo month_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}
function month_list_row($label, $name, $selected_id=null, $all_option = false,
                        $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    global $path_to_root;
    if($label!=null)
        echo "<tr><td class=''>$label</td><td nowrap>";
    echo month_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>";

    if($label!=null)
        echo "</tr>";
}

function month_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                    $show_inactive=false, $editkey = false)
{
    global $all_items;
    $f_year = get_current_fiscalyear();
    $year1 = date('Y', strtotime($f_year['end']));
    $year2 = date('Y', strtotime($f_year['begin']));

    $sql = "SELECT id, IF(`description` ='Jan' || `description` ='Feb' || `description` ='March' || `description` ='Apr' || `description` ='May' || `description` ='June',  CONCAT(description, '-', $year1),  CONCAT(description, '-', $year2))  FROM ".TB_PREF."month";
    $ret = combo_input($name, $selected_id, $sql,  'id', 'description',
        array(
            //'format' => '_format_add_curr',
            'order' => array('id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Month") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :
                _('Select employee'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('month');
    return $ret;
}
function employee_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                       $show_inactive=false, $editkey = false,$applicable=0)
{
    global $all_items;
    $sql = "SELECT employee_id, emp_code, emp_name FROM ".TB_PREF."employee where employee_id!=0  ";
    if($applicable == 1)
        $sql.=" AND applicable =1";
    if($applicable == 2)
        $sql.=" AND leave_applicable=1";
    $ret = combo_input($name, $selected_id, $sql,  'employee_id', 'emp_name',
        array(
            'format' => '_format_add_curr',
            'order' => array('emp_name'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Employee") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :
                _('Select employee'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('employee');
    return $ret;
}

function employee_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false,$applicable=0)
{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo employee_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey,$applicable);
    echo "</td>\n";
}

function employee_list_row($label, $name, $selected_id=null, $all_option = false,
                           $submit_on_change=false, $show_inactive=false, $editkey = false,$applicable=0)
{
    global $path_to_root;

    if($label!=null)
        echo "<tr><td class=''>$label</td><td nowrap>";
    echo employee_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey,$applicable);
    echo "</td>";

    if($label!=null)
        echo "</tr>";
}
function bank_accounts_list_emp($name, $selected_id=null, $submit_on_change=false, $spec_option=false,$mode)
{

    $sql = "SELECT ".TB_PREF."bank_accounts.id, ".TB_PREF."bank_accounts.bank_account_name, ".TB_PREF."bank_accounts.bank_curr_code, ".TB_PREF."bank_accounts.account_code, ".TB_PREF."bank_accounts.inactive
FROM ".TB_PREF."bank_accounts, ".TB_PREF."chart_master
WHERE ".TB_PREF."bank_accounts.account_code = ".TB_PREF."chart_master.account_code
AND ".TB_PREF."chart_master.division =$mode";

    return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
        array(
            'format' => '_format_add_curr',
            'select_submit'=> $submit_on_change,
            'spec_option' => $spec_option,
            'spec_id' => '',
            'async' => false
        ) );
}

function bank_accounts_list_cells_emp($label, $name, $selected_id=null, $submit_on_change=false,$mode)
{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo bank_accounts_list_emp($name, $selected_id, $submit_on_change,'',$mode);
    echo "</td>\n";
}

function bank_accounts_list_row_emp($label, $name, $selected_id=null, $submit_on_change=false,$mode)
{

    echo "<tr><td class='label'>$label</td>";
    bank_accounts_list_cells_emp(null, $name, $selected_id, $submit_on_change,$mode);
    echo "</tr>\n";
}
function emp_grade2($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                    $all=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, description FROM ".TB_PREF."grade ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            //'format' => '_format_add_curr',
            'order' => array('description'),
            'search_box' => $mode!=0,
            'type' => 1,
            'spec_option' => $spec_option === true ? _("All Grades") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select employee'),
            'show_inactive'=>$all
        ));
    if ($editkey)
        $ret .= add_edit_combo('supplier');
    return $ret;
}

function emp_grade_cells2($label, $name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td><td>\n";
    echo emp_grade2($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td>\n";
}

function emp_grade_row2($label, $name, $selected_id=null, $all_option = false,
                        $submit_on_change=false, $all=false, $editkey = false)
{
    echo "<tr><td class=''>$label</td><td>";
    echo emp_grade2($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td></tr>\n";
}
function advance_base_list_row($label, $name, $selected_id=null, $all_option = false,
                               $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    global $path_to_root;

    echo "<tr><td class=''>$label</td><td nowrap>";
    echo advance_base_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}
function advance_base_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                           $show_inactive=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, name FROM ".TB_PREF."advance_base ";
    $ret = combo_input($name, $selected_id, $sql,  'id', 'name',
        array(
//			'format' => '_format_add_curr',
            'order' => array('id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("Select ") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :
                _('Select'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('employee');
    return $ret;
}
function approve_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['1'] = strlen($name_yes) ? $name_yes : _("Approved");
    $items['0'] = strlen($name_no) ? $name_no : _("Unapprove");



    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function advance_against_list($label, $name, $selected_id=null, $all_option=false,
                              $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td><td>\n";
    echo against_list($name, $selected_id, $all_option, $submit_on_change,
        $all, $editkey);
    echo "</td>\n";
}
function against_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                      $all=false, $editkey = false)
{
    global $all_items;

    $sql = "SELECT id, name FROM ".TB_PREF."advance_base ";
    $ret = combo_input($name, $selected_id, $sql, 'id', 'name',
        array(
            //'format' => '_format_add_curr',
            'order' => array('id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'spec_option' => $spec_option === true ? _("All") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
                _('Select employee'),
            'show_inactive'=>$all
        ));
    if ($editkey)
        $ret .= add_edit_combo('supplier');
    return $ret;
}
function approve_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";

    echo "<td>";
    echo approve_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}
function approve_list_cells_new($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";

//	echo "<td>";
    echo approve_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
//	echo "</td>\n";
}

function approve_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    approve_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}
//---
function text_cells_rm($label, $name, $size, $max=null, $init=null, $title=null,
					   $labparams=null, $post_label=null, $submit_on_change=false)
{
	global $Ajax;

	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")
	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, $labparams);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	echo "<input $class type=\"text\" name=\"$name\" id='cash_remaining' size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
		.($title ? " title='$title'": '')." readonly >";

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);
}


function text_cells_cr($label, $name, $display_total, $max=null, $init=null, $title=null,
					   $labparams=null, $post_label=null, $submit_on_change=false)
{
	global $Ajax;


	default_focus($name);
	if (!isset($_POST[$name]) || $_POST[$name] == "")
	{
		if ($init)
			$_POST[$name] = $init;
		else
			$_POST[$name] = "";
	}
	if ($label != null)
		label_cell($label, $labparams);

	if (!isset($max))
		$max = $size;

	echo "<td>";
	$class = $submit_on_change ? 'class="searchbox"' : '';
	echo "<input $class type=\"text\" name=\"$name\" id='cash_recieved' onfocusout='myFunction_cash($display_total)' size=\"$size\" maxlength=\"$max\" value=\"" . $_POST[$name]. "\""
		.($title ? " title='$title'": '')." >";

	if ($post_label)
		echo " " . $post_label;

	echo "</td>\n";
	$Ajax->addUpdate($name, $name, $_POST[$name]);

}
//

function sales_order_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{


      $sql = "SELECT reference  FROM ".TB_PREF."sales_orders 
      WHERE reference != 'auto' ";
    $sql .= " GROUP BY order_no";

   //$mode = get_company_pref('no_customer_list');

// if ($editkey)
//    set_editor('customer', $name, $editkey);

   $ret = combo_input($name, $selected_id, $sql, 'order_no', 'reference',
      array(
         'format' => '_format_sales_order',
         'order' => array('order_no', 'reference'),
      
         'size' => 20,
      
         'spec_option' => $spec_option === true ? _("SELECT") : $spec_option,
         'spec_id' => ALL_TEXT,
         'select_submit'=> $submit_on_change,
         'async' => false,
//       'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
//          _('Select customer'),
      // 'show_inactive' => $show_inactive,
         //'editlink' => $editkey ? add_edit_combo('customer') : false
      ), "sales_order_ref" );
   return $ret;
}

function sales_order_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo sales_order_list($name, $selected_id, $all_option, $submit_on_change	);
	echo "</td>\n";
}

function sales_order_row($label, $name, $selected_id=null, $all_option = false,
						   $submit_on_change=false)
{

	echo "<tr><td class=''>$label</td><td nowrap>";
	echo sales_order_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n</tr>\n";
}
//
function sales_order_purchase_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false)
{


	$sql = "SELECT order_no, reference  FROM ".TB_PREF."sales_orders 
		
		WHERE NOT EXISTS (SELECT * FROM ".TB_PREF."purch_orders WHERE 
		".TB_PREF."sales_orders.order_no = ".TB_PREF."purch_orders.sale_order	)
		GROUP BY order_no";

	//$mode = get_company_pref('no_customer_list');

//	if ($editkey)
//		set_editor('customer', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql, 'order_no', 'reference',
		array(
			'format' => '_format_sales_order',
			'order' => array('order_no', 'reference'),
			//'search_box' => $mode!=0,
			//	'type' => 1,
			'size' => 20,
			//'search' => array("debtor_ref","name","tax_id"),
			'spec_option' => $spec_option === true ? _("SELECT") : $spec_option,
			'spec_id' => ALL_TEXT,
			'select_submit'=> $submit_on_change,
			'async' => false,
//			'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
//				_('Select customer'),
			//	'show_inactive' => $show_inactive,
			//'editlink' => $editkey ? add_edit_combo('customer') : false
		), "customer" );
	return $ret;
}

function sales_order_purchase_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo sales_order_purchase_list($name, $selected_id, $all_option, $submit_on_change	);
	echo "</td>\n";
}

function sales_order_purchase_row($label, $name, $selected_id=null, $all_option = false,
						 $submit_on_change=false)
{

	echo "<tr><td class=''>$label</td><td nowrap>";
	echo sales_order_purchase_list($name, $selected_id, $all_option, $submit_on_change);
	echo "</td>\n</tr>\n";
}

function voucher_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    global $systypes_array;
	$items = array();
    $items['-1']            = _("All");
    $items[ST_JOURNAL]      = _($systypes_array[ST_JOURNAL]);
    $items[ST_BANKPAYMENT]  = _($systypes_array[ST_BANKPAYMENT]);
    $items[ST_BANKDEPOSIT]  = _($systypes_array[ST_BANKDEPOSIT]);
    $items[ST_BANKTRANSFER] = _($systypes_array[ST_BANKTRANSFER]);
    $items[ST_CPV] = _($systypes_array[ST_CPV]);
    $items[ST_CRV] = _($systypes_array[ST_CRV]);
    $items[ST_SUPPINVOICE]  = _($systypes_array[ST_SUPPINVOICE]);
    $items[ST_SUPPAYMENT] = _($systypes_array[ST_SUPPAYMENT]);
    $items[ST_CUSTCREDIT]   = _($systypes_array[ST_CUSTCREDIT]);
    $items[ST_SALESINVOICE] = _($systypes_array[ST_SALESINVOICE]);
    $items[ST_CUSTPAYMENT] = _($systypes_array[ST_CUSTPAYMENT]);
    
	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function voucher_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo voucher_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}


function movement_types_list($name, $selected_id=null, $special_option=false)
{
    $sql = "SELECT id, name FROM ".TB_PREF."movement_types";
    return combo_input($name, $selected_id, $sql, 'id', 'name', array(
        'spec_option' => $special_option,
    ));
}

function movement_types_list_cells($label, $name, $selected_id=null, $special_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo movement_types_list($name, $selected_id, $special_option);
    echo "</td>\n";
}

function movement_types_list_row($label, $name, $selected_id=null, $special_option=false)
{
    echo "<tr><td class=''>$label</td>";
    movement_types_list_cells(null, $name, $selected_id, $special_option);
    echo "</tr>\n";
}

//


function subledger_dashboard_list($name, $account, $selected_id=null,$all_option = false)
{

	$type = is_subledger_account($account);
	if (!$type)
		return '';

	if($type > 0)
   $sql = "SELECT d.debtor_no as id/*, debtor_ref as name*/, CONCAT(d.debtor_ref, ' - ', d.name) as name
   FROM "
      .TB_PREF."debtors_master d,"
      .TB_PREF."cust_branch c
   WHERE d.debtor_no=c.debtor_no AND c.receivables_account=".db_escape($account);
else
   $sql = "SELECT supplier_id as id, CONCAT(supp_ref, ' - ', supp_name) as name 
   FROM "
      .TB_PREF."suppliers s
   WHERE s.payable_account=".db_escape($account);

	$mode = get_company_pref('no_customer_list');

	return combo_input($name, $selected_id, $sql, 'id', 'name',
		array(
			'type' => 1,
			'spec_option' => $all_option===true ?  _("All") : $all_option,
			'size' => 20,
			'async' => false,
		) );
}

function subledger_dashboard_list_cells($label, $name, $account, $selected_id=null,$all_option = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo subledger_dashboard_list($name, $account, $selected_id,$all_option );
	echo "</td>\n";
}

function subledger_dashboard_list_row($label, $name, $selected_id=null, $all_option = false,
							$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	echo "<tr><td class=''>$label</td><td nowrap>";
	echo subledger_dashboard_list($name, $account, $selected_id,$all_option );
	echo "</td>\n</tr>\n";
}
function add_new_po($id,$label)
{
	global $path_to_root;

	label_cells(null,
		"<a target='_blank'  href='$path_to_root/sales/manage/ip_role.php?&&ip_id=".$id."'"
		." onclick=\"javascript:openWindow(this.href,this.target); return false;\" >
		$label
		</a>");
}

//////For Project Management
//duration for hours
function duration_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                       $show_inactive=false, $editkey = false)
{
//	$sql = "SELECT id, CAST(duration as  UNSIGNED) as duration, inactive FROM ".TB_PREF."duration";

    global $all_items;
    $sql = "SELECT id,duration, inactive FROM ".TB_PREF."duration";

    $ret = combo_input($name, $selected_id, $sql, 'id', 'duration',
        array(
            'order' => array('duration'),
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Duration") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,

            'show_inactive' => $show_inactive
        ) );

    return $ret;
}
function duration_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo duration_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function duration_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    duration_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//duration for minutes
function duration1_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                        $show_inactive=false, $editkey = false)
{
//	$sql = "SELECT id, CAST(duration as  UNSIGNED) as duration, inactive FROM ".TB_PREF."duration";

    global $all_items;
    $sql = "SELECT id,duration, inactive FROM ".TB_PREF."duration_min";

    $ret = combo_input($name, $selected_id, $sql, 'id', 'duration',
        array(
            'order' => array('duration'),
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Duration") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,

            'show_inactive' => $show_inactive
        ) );

    return $ret;
}
function duration1_list_cells($label, $name, $selected_id=null, $all_option=false,
                              $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo duration1_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function duration1_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    duration1_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//RA
function pstatus_list_cells($label, $name, $selected_id=null, $all_option=false,
                            $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo pstatus_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function pstatus_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                      $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id,status, inactive FROM ".TB_PREF."pstatus";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)
        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'status',
        array(

            'order' => array('status'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Status") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Status'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
function pstatus_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    pstatus_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//progress
function progress_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo progress_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function progress_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                       $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id,progress, inactive FROM ".TB_PREF."progress";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)
        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'progress',
        array(

            'order' => array('id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Progress") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Progress'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
function progress_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    progress_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//priority
function priority_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=true, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo priority_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function priority_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                       $show_inactive=false, $editkey = false)
{
    global $all_items;
    $sql = "SELECT id, priority, inactive FROM ".TB_PREF."priority";


    $mode = get_company_pref('no_customer_list');

    if ($editkey)
        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'priority',
        array(

            'order' => array('id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Priorities") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Priority'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}

function priority_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    priority_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
/////////ansar31 may
function users_query_list_cells($label, $name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo users_query_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function users_query_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                          $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,

            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id, user_id FROM ".TB_PREF."users WHERE inactive = 0";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)


        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'user_id',
        array(

            'order' => array('user_id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Users") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Status'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
//Category
function category_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                       $show_inactive=false, $editkey = false)
{

    global $all_items;
    $sql = "SELECT id, category,inactive  FROM ".TB_PREF."category";

    $ret = combo_input($name, $selected_id, $sql, 'id', 'category',
        array(

            'order' => array('category'),
            'spec_option' => $spec_option === true ? _("Select") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'show_inactive' => $show_inactive
        ) );

    return $ret;
}
function category_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo category_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}

function category_list_row($label, $name, $selected_id=null, $all_option = false,
                           $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    global $path_to_root;

    echo "<tr><td class='label'>$label</td><td nowrap>";
    echo city_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n</tr>\n";
}
function users_query_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    users_query_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//
//Call Type
function call_type_list_cells($label, $name, $selected_id=null, $all_option=false,
                              $submit_on_change=true, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo call_type_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function call_type_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
                        $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id,call_type,inactive FROM ".TB_PREF."call_type";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    //$mode = get_company_pref('no_customer_list');

    if ($editkey)
        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'call_type',
        array(

            'order' => array('call_type'),

            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("Select All") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,

            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
function call_type_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    call_type_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}

function source_status_query_list_cells($label, $name, $selected_id=null, $all_option=false,
                                        $submit_on_change=true, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo source_status_query_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function source_status_query_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                                  $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id, description, inactive FROM ".TB_PREF."source_status";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)


        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'description',
        array(

            'order' => array('description'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Source") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Source'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
function source_status_query_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    source_status_query_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
function status_query_list_cells($label, $name, $selected_id=null, $all_option=false,
                                 $submit_on_change=true, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo status_query_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function status_query_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                           $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'status',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change
        ) );*/

    global $all_items;
    $sql = "SELECT id, status, inactive FROM ".TB_PREF."query_status";
    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)


        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'status',
        array(

            'order' => array('status'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Status") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Status'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
function status_query_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=null)
{
    echo "<tr><td class=''>$label</td>";
    status_query_list_cells(null, $name, $selected_id, $all_option, $submit_on_change);
    echo "</tr>\n";
}
//NUMBER OF RECORDS
function result_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo result_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function result_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $result = array();
//	$result['10'] = 10;
//	$result['20'] = 20;
    $result['30'] = 30;
    $result['50'] = 50;
    $result['100'] = 100;
    $result['200'] = 200;
    $result['500'] = 500;
    $result['1000'] = 1000;

    return array_selector($name, $selected_id, $result,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}



function color_list($name, $selected_id=null, $name_yes="", $name_no="",$name_blue_lite="",$name_blue="",$name_black="",$name_purple="",
                    $name_green="",$name_red="",$name_yellow="",$blacklight="",$yellowlight="",$greenlight="",
                    $submit_on_change=true,$spec_option=true)
{
    $items = array();
    $items['skin-red-light'] = strlen($name_no) ? $name_no : _("skin-red-light");
    $items['skin-purple-light'] = strlen($name_yes) ? $name_yes : _("skin-purple-light");
    $items['skin-blue-light'] = strlen($name_blue_lite) ? $name_blue_lite : _("skin-blue-light");
    $items['skin-blue'] = strlen($name_blue) ? $name_blue : _("skin-blue");
  $items['skin-black'] = strlen($name_black) ? $name_black : _("skin-black");
    $items['skin-purple'] = strlen($name_purple) ? $name_purple : _("skin-purple");
    $items['skin-green'] = strlen($name_green) ? $name_green : _("skin-green");
    $items['skin-red'] = strlen($name_red) ? $name_red : _("skin-red");
    $items['skin-yellow'] = strlen($name_yellow) ? $name_yellow : _("skin-yellow");

    $items['skin-black-light'] = strlen($blacklight) ? $blacklight : _("skin-black-light");
    $items['skin-yellow-light'] = strlen($yellowlight) ? $yellowlight : _("skin-yellow-light");
    $items['skin-green-light'] = strlen($greenlight) ? $greenlight : _("skin-green-light");



    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'spec_option' => $spec_option === true ? _("--Select--") : $spec_option,
            'async' => false ) ); // FIX?
}


function color_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="",$name_blue_lite="",$name_blue="",$name_black="",$name_purple="",
                          $name_green="",$name_red="",$name_yellow="",$blacklight="",$yellowlight="",$greenlight="",$submit_on_change=true,$spec_option=true)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo color_list($name, $selected_id, $name_yes, $name_no,$name_blue_lite,$name_blue,$name_black,$name_purple, $name_green,$name_red,$name_yellow,
        $blacklight,$yellowlight,$greenlight,$submit_on_change,$spec_option);
    echo "</td>\n";
}

function color_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $name_blue_lite="",$name_blue="",$name_black="",$name_purple="",
                        $name_green="",$name_red="",$name_yellow="",$blacklight="",$yellowlight="",$greenlight="",$submit_on_change=true,$spec_option=true)
{
    echo "<tr><td class=''>$label</td>";
    color_list_cells(null, $name, $selected_id, $name_yes, $name_no,$name_blue_lite,$name_blue,$name_black,$name_purple, $name_green,$name_red,$name_yellow,
        $blacklight,$yellowlight,$greenlight,$submit_on_change,$spec_option);
    echo "</tr>\n";
}


//--iqra


//--iqra


function users_queryy_list_cells($label, $name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $show_inactive=false, $editkey = false)

{

    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo users_queryy_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";

}

function users_queryy_list($name, $selected_id=null, $spec_option=false, $submit_on_change=true,
                          $show_inactive=false, $editkey = false)
{
//	global $all_items;


    /*return combo_input($name, $selected_id, $sql, 'id', 'description',
        array(
            'spec_option' => $all_option === true ? _("All Status") : $all_option,
            'spec_id' => $all_items,

            'select_submit'=> $submit_on_change
        ) );*/
    $user_name= $_SESSION["wa_current_user"]->user;

    global $all_items;


    if($_SESSION["wa_current_user"]->access==2) {

        $sql = "SELECT id, user_id FROM " . TB_PREF . "users WHERE inactive = 0   ";

    }
    else{


        $sql = "SELECT id, user_id FROM " . TB_PREF . "users WHERE inactive = 0 
      AND id =".db_escape($user_name)."";

    }


    //	$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

    $mode = get_company_pref('no_customer_list');

    if ($editkey)


        set_editor('customer', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'id', 'user_id',
        array(

            'order' => array('user_id'),
            'search_box' => $mode!=0,
            'type' => 1,
            'size' => 20,
            'spec_option' => $spec_option === true ? _("All Users") : $spec_option,
            'spec_id' => $all_items,
            'select_submit'=> $submit_on_change,
            'async' => false,
            'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
                _('Select Status'),
            'show_inactive' => $show_inactive
        ) );
    if ($editkey)
        $ret .= add_edit_combo('customer');
    return $ret;
}
//RA
function stock_items_list_multi($name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $opts=array(), $editkey = false)
{
    global $all_items;

    $sql = "SELECT category_id, description, inactive FROM ".TB_PREF."stock_category  WHERE dflt_mb_flag != 'F' ";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'category_id', 'description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => $all_items,
                'order' => array('description','category_id'),
                'multi' => true,
                'type' => 1,
                'size' => 20,
                'height' => 20,
                //'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                //'spec_id' => $all_items,
                'search' => array("category_id","description"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'select_submit'=> $submit_on_change,
                'category' => 2,
            ), $opts) );
    if ($editkey)
        $ret .= add_edit_combo('item');
    return $ret;

}

function _format_stock_items_multi($row)
{
    return (user_show_codes() ?  ($row[0] . "&nbsp;-&nbsp;") : "") . $row[1];
}

function stock_items_list_cells_multi($label, $name, $selected_id=null, $all_option=false,
                                      $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo stock_items_list_multi($name, $selected_id, $all_option, $submit_on_change,
        array('cells'=>true, 'show_inactive'=>$all), $editkey);
}
function stock_purchasable_items_list_multi($name, $selected_id=null,
                                            $all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
    return stock_items_list_multi($name, $selected_id, $all_option, $submit_on_change,
        array(
//			'where'=>array("mb_flag!= 'B'"),
            'show_inactive'=>$all), $editkey);
}

function all_bank_accounts_list($name, $selected_id=null, $submit_on_change=false, $spec_option=true, $Where = '')
{
	if($Where == 3)
		$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
            FROM ".TB_PREF."bank_accounts WHERE ".TB_PREF."bank_accounts.account_type = ".db_escape(3);
	else if($Where == 0)
		$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
            FROM ".TB_PREF."bank_accounts WHERE ".TB_PREF."bank_accounts.account_type = ".db_escape(0);
	else
		$sql = "SELECT ".TB_PREF."bank_accounts.id, bank_account_name, bank_curr_code, inactive
		    FROM ".TB_PREF."bank_accounts";

	return combo_input($name, $selected_id, $sql, 'id', 'bank_account_name',
		array(
			'format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'spec_option' => $spec_option===true ? _("All Bank Accounts") : $spec_option,
			'spec_id' => '',
			'async' => false
		) );
}

function all_bank_accounts_list_cells($label, $name, $selected_id=null, $submit_on_change=false,$spec_option=true, $Where='')
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo all_bank_accounts_list($name, $selected_id, $submit_on_change,$spec_option,$Where);
	echo "</td>\n";
}

function all_bank_accounts_list_row($label, $name, $selected_id=null, $submit_on_change=false,$spec_option=true, $Where='')
{
	echo "<tr><td class='label'>$label</td>";
	all_bank_accounts_list_cells(null, $name, $selected_id, $submit_on_change,$spec_option,$Where);
	echo "</tr>\n";
}

//


function search_list($name, $selected_id=null, $Select="",$reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	$items = array();
	if($_GET['search_id']){
// 	$items['0'] = strlen($Select) ? $Select: _("Select");
	$items['1'] = strlen($reference) ? $reference: _("Reference");
	$items['2'] = strlen($trans_no) ? $trans_no : _("Trans #:");
	$items['3'] = strlen($memo) ? $memo : _("Memo :");
	$items['4'] = strlen($cheque) ? $cheque : _("Cheque #:");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
			}
			else{
			    
			$items['0'] = strlen($Select) ? $Select: _("Select");
	$items['1'] = strlen($reference) ? $reference: _("Reference");
	$items['2'] = strlen($trans_no) ? $trans_no : _("Trans #:");
	$items['3'] = strlen($memo) ? $memo : _("Memo :");
	$items['4'] = strlen($cheque) ? $cheque : _("Cheque #:");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?	    
			    
			    
			}
}

function  search_list_cells($label, $name, $selected_id=null,$Select="", $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo search_list($name, $selected_id,$Select="",$reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</td>\n";
}

function search_list_row($label, $name, $selected_id=null,$Select="", $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	search_list_cells(null, $name, $selected_id,$Select="", $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</tr>\n";
}
//////////////////////////////////////////////////////////
function search_portal_list($name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($reference) ? $reference: _("Select");
    $items['1'] = strlen($reference) ? $reference: _("C.Code");
    $items['2'] = strlen($trans_no) ? $trans_no : _("C.Name #:");
    $items['3'] = strlen($memo) ? $memo : _("Report :");
   // $items['4'] = strlen($cheque) ? $cheque : _("Cheque #:");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function  search_portal_list_cells($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo search_portal_list($name, $selected_id,$reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
    echo "</td>\n";
}

function search_portal_list_row($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    search_portal_list_cells(null, $name, $selected_id, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
    echo "</tr>\n";
}


//==============================================================================
function sorter_list($name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($reference) ? $reference: _("Select");
	$items['1'] = strlen($reference) ? $reference: _("Trans Date");
	$items['2'] = strlen($trans_no) ? $trans_no : _("Trans #");
	$items['3'] = strlen($memo) ? $memo : _("Qty");
	$items['4'] = strlen($cheque) ? $cheque : _("Location");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function  sorter_list_cells($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo sorter_list($name, $selected_id,$reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</td>\n";
}

function sorter_list_row($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
    sorter_list_cells(null, $name, $selected_id, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</tr>\n";
}
//==============================================================================

function item_location_list($name, $selected_id=null, $all_option=false)
{
	$sql = "SELECT id, description, inactive FROM ".TB_PREF."item_location";
	return combo_input($name, $selected_id, $sql, 'id', 'description', array('order'=>array('description'),
		'spec_option' =>$all_option===true ? _("No Item Location") : $all_option,
		'spec_id' => ALL_NUMERIC));
}

function item_location_cells($label, $name, $selected_id=null, $all_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo item_location_list($name, $selected_id, $all_option);
	echo "</td>\n";
}

function item_location_row($label, $name, $selected_id=null, $all_option=false)
{
	echo "<tr><td class='label'>$label</td>";
	item_location_cells(null, $name, $selected_id, $all_option);
	echo "</tr>\n";
}



//--IQRA
function sales_items_list_POS($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $type='', $opts=array())
{
    // all sales codes
    $sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
			 i.inactive, if(count(*)>1, '0', s.editable) as editable
			FROM
			".TB_PREF."stock_master s,
			".TB_PREF."item_codes i
			LEFT JOIN
			".TB_PREF."stock_category c
			ON i.category_id=c.category_id
			WHERE i.stock_id=s.stock_id
      AND mb_flag != 'F'";


    if ($type == 'local')	{ // exclude foreign codes
        $sql .=	" AND !i.is_foreign";
    } elseif ($type == 'kits') { // sales kits
        $sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
    }
    $sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale";
    $sql .= " GROUP BY i.item_code";

    return combo_input($name, $selected_id, $sql, 'i.item_code', 'c.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("i.item_code", "c.description", "i.description"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'size'=>5,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','i.item_code'),
                'editable' => 30,
                'max' => 255
            ), $opts), $type == 'kits' ? $type : "stock_sales" );
}

function sales_items_list_cells_POS($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false)
{
    if ($editkey)
        set_editor('item', $name, $editkey);

    if ($label != null)
        echo "<td>$label</td>\n";
    echo sales_items_list_POS($name, $selected_id, $all_option, $submit_on_change,
        '', array('cells'=>true));
}

function combo_input_POS($name, $selected_id, $sql, $valfield, $namefield,
                     $options=null, $type=null)
{
    global $Ajax, $path_to_root, $SysPrefs ;

    $opts = array(		// default options
        'where'=> array(),		// additional constraints
        'order' => $namefield,	// list sort order
        // special option parameters
        'spec_option'=>false, 	// option text or false
        'spec_id' => 0,		// option id
        // submit on select parameters
        'default' => '', // default value when $_POST is not set
        'multi' => false,	// multiple select
        'select_submit' => false, //submit on select: true/false
        'async' => true,	// select update via ajax (true) vs _page_body reload
        // search box parameters
        'sel_hint' => null,
        'search_box' => false, 	// name or true/false
        'type' => 0,	// type of extended selector:
        // 0 - with (optional) visible search box, search by fragment inside id
        // 1 - with hidden search box, search by option text
        // 2 - with (optional) visible search box, search by fragment at the start of id
        // 3 - TODO reverse: box with hidden selector available via enter; this
        // would be convenient for optional ad hoc adding of new item
        'search_submit' => true, //search submit button: true/false
        'size' => 8,	// size and max of box tag
        'max' => 50,
        'height' => false,	// number of lines in select box
        'cells' => false,	// combo displayed as 2 <td></td> cells
        'search' => array(), // sql field names to search
        'format' => null, 	 // format functions for regular options
        'disabled' => false,
        'box_hint' => null, // box/selectors hints; null = std see below
        'category' => false, // category column name or false
        'show_inactive' => false, // show inactive records. 
        'editable' => false, // false, or length of editable entry field
        'editlink' => false	// link to entity entry/edit page (optional)
    );
// ------ merge options with defaults ----------
    if($options != null)
        $opts = array_merge($opts, $options);
    if (!is_array($opts['where']))  $opts['where'] = array($opts['where']);

    $search_box = $opts['search_box']===true ? '_'.$name.'_edit' : $opts['search_box'];
    // select content filtered by search field:
    $search_submit = $opts['search_submit']===true ? '_'.$name.'_button' : $opts['search_submit'];
    // select set by select content field
    $search_button = $opts['editable'] ? '_'.$name.'_button' : ($search_box ? $search_submit : false);

    $select_submit =  $opts['select_submit'];
    $spec_id = $opts['spec_id'];
    $spec_option = $opts['spec_option'];
    if ($opts['type'] == 0) {
        $by_id = true;
        $class = 'combo';
    } elseif($opts['type'] == 1) {
        $by_id = false;
        $class = 'combo2';
    } else {
        $by_id = true;
        $class = 'combo3';
    }

    $disabled = $opts['disabled'] ? "disabled" : '';
    $multi = $opts['multi'];

    $auto_select_box = true;

// Make sure the auto select box for searching not causing trouble.
    if ($auto_select_box == true) {
        // Disable auto select box on mobile device because it has problem
        // with the soft keyboard input.
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            $auto_select_box = false;
        }

        // Disable auto select box when the search feature for item, supplier,
        // or customer is turned on.
        if (get_company_pref('no_item_list') !=0 ||
            get_company_pref('no_customer_list') != 0 ||
            get_company_pref('no_supplier_list') != 0) {
            $auto_select_box = false;
        }
    }
    //dz 1.9.17
    if($auto_select_box&&!$multi)$class .=' mbselect';


    if(!count($opts['search'])) {
        $opts['search'] = array($by_id ? $valfield : $namefield);
    }
    if ($opts['sel_hint'] === null)
        $opts['sel_hint'] = $by_id || $search_box==false ?
            '' : _('Press Space tab for search pattern entry');

    if ($opts['box_hint'] === null)
        $opts['box_hint'] = $search_box && $search_submit != false ?
            ($by_id ? _('Enter code fragment to search or * for all')
                : _('Enter description fragment to search or * for all')) :'';

    if ($selected_id == null) {
        $selected_id = get_post($name, (string)$opts['default']);
    }
    if(!is_array($selected_id))
        $selected_id = array((string)$selected_id); // code is generalized for multiple selection support

    $txt = get_post($search_box);
    $rel = '';
    $limit = '';
    if (isset($_POST['_'.$name.'_update'])) { // select list or search box change
        if ($by_id) $txt = $_POST[$name];

        if (!$opts['async'])
            $Ajax->activate('_page_body');
        else
            $Ajax->activate($name);
    }
    if (isset($_POST[$search_button])) {
        if (!$opts['async'])
            $Ajax->activate('_page_body');
        else
            $Ajax->activate($name);
    }
    if ($search_box) {
        // search related sql modifications

        $rel = "rel='$search_box'"; // set relation to list
        if ($opts['search_submit']) {
            if (isset($_POST[$search_button])) {
                $selected_id = array(); // ignore selected_id while search
                if (!$opts['async'])
                    $Ajax->activate('_page_body');
                else
                    $Ajax->activate($name);
            }
            if ($txt == '') {
                if ($spec_option === false && $selected_id == array())
                    $limit = ' LIMIT 1';
                else
                    $opts['where'][] = $valfield . "=". db_escape(get_post($name, $spec_id));
            }
            else
                if ($txt != '*') {

                    foreach($opts['search'] as $i=> $s)
                        $opts['search'][$i] = $s . " LIKE "
                            .db_escape(($class=='combo3' ? '' : '%').$txt.'%');
                    $opts['where'][] = '('. implode($opts['search'], ' OR ') . ')';
                }
        }
    }

    // sql completion
    if (count($opts['where'])) {
        $where = strpos($sql, 'WHERE')==false ? ' WHERE ':' AND ';
        $where .= '('. implode($opts['where'], ' AND ') . ')';
        $group_pos = strpos($sql, 'GROUP BY');
        if ($group_pos) {
            $group = substr($sql, $group_pos);
            $sql = substr($sql, 0, $group_pos) . $where.' '.$group;
        } else {
            $sql .= $where;
        }
    }
    if ($opts['order'] != false) {
        if (!is_array($opts['order']))
            $opts['order'] = array($opts['order']);
        $sql .= ' ORDER BY '.implode(',',$opts['order']);
    }

    $sql .= $limit;
    // ------ make selector ----------
    $selector = $first_opt = '';
    $first_id = false;
    $found = false;
    $lastcat = null;
    $edit = false;
    $pname = false;
    if (($type === "customer" || $type === "supplier") && !empty($SysPrefs->prefs['shortname_name_in_list']))
        $pname = true;
    if($result = db_query($sql)) {
        while ($contact_row = db_fetch($result)) {
            $value = $contact_row[0];
            $descr = $opts['format']==null ?  $contact_row[1] :
                call_user_func($opts['format'], $contact_row, $pname);
            $sel = '';
            if (get_post($search_button) && ($txt == $value)) {
                $selected_id[] = $value;
            }

            if (in_array((string)$value, $selected_id, true)) {
                $sel = 'selected';
                $found = $value;
                $edit = $opts['editable'] && $contact_row['editable']
                && (@$_POST[$search_box] == $value)
                    ? $contact_row[1] : false; // get non-formatted description
                if ($edit)
                    break;	// selected field is editable - abandon list construction
            }
            // show selected option even if inactive 
            if (!$opts['show_inactive'] && @$contact_row['inactive'] && $sel==='') {
                continue;
            } else
                $optclass = @$contact_row['inactive'] ? "class='inactive'" : '';

            if ($first_id === false) {
                $first_id = $value;
                $first_opt = $descr;
            }
            $cat = $contact_row[$opts['category']];
            if ($opts['category'] !== false && $cat != $lastcat){
                if ($lastcat!==null)
                    $selector .= "</optgroup>";
                $selector .= "<optgroup label='".$cat."'>\n";
                $lastcat = $cat;
            }
            $selector .= "<option $sel $optclass value='$value'>$descr</option>\n";
        }
        if ($lastcat!==null)
            $selector .= "</optgroup>";
        db_free_result($result);
    }

    // Prepend special option.
    if ($spec_option !== false) { // if special option used - add it
        $first_id = $spec_id;
        $first_opt = $spec_option;
        $sel = $found===false ? 'selected' : '';
        $optclass = @$contact_row['inactive'] ? "class='inactive'" : '';
        $selector = "<option $sel value='$first_id'>$first_opt</option>\n"
            . $selector;
    }

    if ($found===false) {
        $selected_id = array($first_id);
    }

    $_POST[$name] = $multi ? $selected_id : $selected_id[0];

    if ($SysPrefs->use_popup_search)
        $selector = "<select id='$name' autocomplete='off' ".($multi ? "multiple" : '')
            . ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
            . "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
            . $opts['sel_hint']."' $rel>".$selector."</select>\n";
    else
        $selector = "<select  style='width:150px;' autocomplete='off' ".($multi ? "multiple" : '')
            . ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
            . "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
            . $opts['sel_hint']."' $rel>".$selector."</select>\n";
    if ($by_id && ($search_box != false || $opts['editable']) ) {
        // on first display show selector list
        if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
            $selector = "<input type='hidden' name='$name' value='".$_POST[$name]."'>"
                ."<input type='text' $disabled name='{$name}_text' id='{$name}_text' size='".
                $opts['editable']."' maxlength='".$opts['max']."' $rel value='$edit'>\n";
            set_focus($name.'_text'); // prevent lost focus
        } else if (get_post($search_submit ? $search_submit : "_{$name}_button"))
            set_focus($name); // prevent lost focus
        if (!$opts['editable'])
            $txt = $found;
        $Ajax->addUpdate($name, $search_box, $txt ? $txt : '');
    }

    $Ajax->addUpdate($name, "_{$name}_sel", $selector);

    // span for select list/input field update
    $selector = "<span id='_{$name}_sel'>".$selector."</span>\n";

    // if selectable or editable list is used - add select button
    if ($select_submit != false || $search_button) {
        // button class selects form reload/ajax selector update
        $selector .= sprintf(SELECT_BUTTON, $disabled, user_theme(),
                (fallback_mode() ? '' : 'display:none;'),
                '_'.$name.'_update')."\n";
    }
// ------ make combo ----------
    $edit_entry = '';
    if ($search_box != false) {
        $edit_entry = "<input $disabled type='text' name='$search_box' id='$search_box' size='".
            $opts['size']."' maxlength='".$opts['max'].
            "' value='$txt' class='$class' rel='$name' autocomplete='off' title='"
            .$opts['box_hint']."'"
            .(!fallback_mode() && !$by_id ? " style=display:none;":'')
            .">\n";
        if ($search_submit != false || $opts['editable']) {
            $edit_entry .= sprintf(SEARCH_BUTTON, $disabled, user_theme(),
                    (fallback_mode() ? '' : 'display:none;'),
                    $search_submit ? $search_submit : "_{$name}_button")."\n";
        }
    }
    default_focus(($search_box && $by_id) ? $search_box : $name);

    $img = "";
    if ($SysPrefs->use_popup_search && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']))
    {
        $img_title = "";
        $link = "";
        $id = $name;
        if ($SysPrefs->use_popup_windows) {
            switch (strtolower($type)) {
                case "stock":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=all&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "stock_manufactured":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=manufactured&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "stock_purchased":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=purchasable&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "stock_sales":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=sales&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "stock_costable":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=costable&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "component":
                    $parent = $opts['parent'];
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=component&parent=".$parent."&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "kits":
                    $link = $path_to_root . "/inventory/inquiry/stock_list.php?popup=1&type=kits&client_id=" . $id;
                    $img_title = _("Search items");
                    break;
                case "customer":
                    $link = $path_to_root . "/sales/inquiry/customers_list.php?popup=1&client_id=" . $id;
                    $img_title = _("Search customers");
                    break;
                case "branch":
                    $link = $path_to_root . "/sales/inquiry/customer_branches_list.php?popup=1&client_id=" . $id . "#customer_id";
                    $img_title = _("Search branches");
                    break;
                case "supplier":
                    $link = $path_to_root . "/purchasing/inquiry/suppliers_list.php?popup=1&client_id=" . $id;
                    $img_title = _("Search suppliers");
                    break;
                case "account":
                    $link = $path_to_root . "/gl/inquiry/accounts_list.php?popup=1&client_id=" . $id;
                    $img_title = _("Search GL accounts");
                    break;
            }
        }

        if ($link !=="") {
            $theme = user_theme();
            $img = '<img src="'.$path_to_root.'/themes/'.$theme.'/images/'.ICON_VIEW.
                '" style="vertical-align:middle;width:12px;height:12px;border:0;" onclick="javascript:lookupWindow(&quot;'.
                $link.'&quot;, &quot;&quot;);" title="' . $img_title . '" style="cursor:pointer;" />';
        }
    }

    if ($opts['editlink'])
        $selector .= ' '.$opts['editlink'];

    if ($search_box && $opts['cells'])
        $str = ($edit_entry!='' ? "<td>$edit_entry</td>" : '')."<td>$selector$img</td>";
    else
        $str = $edit_entry.$selector.$img;
    return $str;

}


//------------------
function item_tax_types_list_cells_new2($label, $name, $selected_id=null, $submit_on_change=false)
{
    if ($label != null)
        echo "<td colspan='1' style='text-align: left;'>$label</td>\n";
    echo "<td>";
    echo item_tax_types_list2($name, $selected_id, $submit_on_change);
    echo "</td>\n";
}
function item_tax_types_list2($name, $selected_id=null, $submit_on_change=false)
{
    $sql ="SELECT id, name FROM ".TB_PREF."tax_groups";

    return combo_input($name, $selected_id, $sql, 'id', 'name',
        array('order' => 'id',
            'select_submit'=> $submit_on_change) );
}
function get_customer_tax_type($customer_id)
{
	$sql = "SELECT 	tax_group_id  FROM ".TB_PREF."cust_branch WHERE debtor_no=".db_escape($customer_id);

	$result = db_query($sql, "could not get customer");

	$row = db_fetch($result);

	return $row[0];
}
//--iqra
function customer_pos_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$show_inactive=false, $editkey = false)
{

    global $SysPrefs;
	if (!empty($SysPrefs->prefs['shortname_name_in_list']))
		$sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive FROM ".TB_PREF."debtors_master ";
	else
		$sql = "SELECT debtor_no, debtor_ref, curr_code, inactive FROM ".TB_PREF."debtors_master ";

	$mode = get_company_pref('no_customer_list');

	if ($editkey)
		set_editor('customer', $name, $editkey);

	$ret = combo_pos_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('debtor_ref'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
        'search' => array("debtor_ref","name","tax_id"),
		'spec_option' => $spec_option === true ? _("All Customers") : $spec_option,
		'spec_id' => ALL_TEXT,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new customer') :
		_('Select customer'),
		'show_inactive' => $show_inactive,
		'editlink' => $editkey ? add_edit_combo('customer') : false
	), "customer" );
	return $ret;
}
function customer_pos_list_cells($label, $name, $selected_id=null, $all_option=false,
                             $submit_on_change=false, $show_inactive=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td nowrap>";
    echo customer_pos_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}

function customer_pos_list_row($label, $name, $selected_id=null, $all_option = false,
                           $submit_on_change=false, $show_inactive=false, $editkey = false)
{

    echo "<td class='' style=\"margin: 0; display: inline-block; float: left;\">$label</td><td nowrap style=\"margin: 0; display: inline-block; float: left;\">";
    echo customer_pos_list($name, $selected_id, $all_option, $submit_on_change,
        $show_inactive, $editkey);
    echo "</td>\n";
}
function customer_pos_branches_list_cells($label,$customer_id, $name, $selected_id=null,
                                      $all_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{
    if ($label != null)
        echo "<td style=\"margin: 0; display: inline-block; float: left;\">$label</td>\n";
    echo "<td style=\"margin: 0; display: inline-block; float: left;\">";
    echo customer_pos_branches_list($customer_id, $name, $selected_id, $all_option, $enabled,
        $submit_on_change, $editkey);
    echo "</td>\n";
}

function customer_pos_branches_list($customer_id, $name, $selected_id=null,
                                $spec_option = true, $enabled=true, $submit_on_change=false, $editkey = false)
{

    $sql = "SELECT branch_code, branch_ref FROM ".TB_PREF."cust_branch
		WHERE debtor_no=" . db_escape($customer_id)." ";

    if ($editkey)
        set_editor('branch', $name, $editkey);

    $where = $enabled ? array("inactive = 0") : array();
    $ret = combo_pos_input($name, $selected_id, $sql, 'branch_code', 'branch_ref',
        array(
            'where' => $where,
            'order' => array('branch_ref'),
            'spec_option' => $spec_option === true ? _('All branches') : $spec_option,
            'spec_id' => ALL_TEXT,
            'select_submit'=> $submit_on_change,
            'sel_hint' => _('Select customer branch'),
            'editlink' => $editkey ? add_edit_combo('branch') : false
        ), "branch" );
    return $ret;
}

function sales_pos_types_list($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
    $sql = "SELECT id, sales_type, inactive FROM ".TB_PREF."sales_types";

    return combo_pos_input($name, $selected_id, $sql, 'id', 'sales_type',
        array(
            'spec_option' => $special_option===true ? _("All Sales Types") : $special_option,
            'spec_id' => 0,
            'select_submit'=> $submit_on_change,
        ) );
}

function sales_pos_types_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
    if ($label != null)
        echo "<td style=\"margin: 0; display: inline-block; float: left;\">$label</td>\n";
    echo "<td style=\"margin: 0; display: inline-block; float: left;\">";
    echo sales_pos_types_list($name, $selected_id, $submit_on_change, $special_option);
    echo "</td>\n";
}

function sales_pos_types_list_row($label, $name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
    echo "<tr><td class=''>$label</td>";
    sales_pos_types_list_cells(null, $name, $selected_id, $submit_on_change, $special_option);
    echo "</tr>\n";
}


function sale_pos_payment_list_cells($label, $name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
    if ($label != null)
        echo "<td class='' style=\"margin: 0; display: inline-block; float: left;\">$label</td>\n";
    echo "<td style=\"margin: 0; display: inline-block; float: left;\">";

    echo sale_pos_payment_list($name, $category, $selected_id, $submit_on_change, $prepayments);

    echo "</td>\n";
}
function sale_pos_payment_list($name, $category, $selected_id=null, $submit_on_change=true, $prepayments=true)
{
    $sql = "SELECT terms_indicator, terms, inactive FROM ".TB_PREF."payment_terms";

    if ($category == PM_CASH) // only cash
        $sql .= " WHERE days_before_due=0 AND day_in_following_month=0";
    elseif ($category == PM_CREDIT) // only delayed payments
        $sql .= " WHERE days_before_due".($prepayments ? '!=': '>')."0 OR day_in_following_month!=0";
    elseif (!$prepayments)
        $sql .= " WHERE days_before_due>=0";

    return combo_pos_input($name, $selected_id, $sql, 'terms_indicator', 'terms',
        array(
            'select_submit'=> $submit_on_change,
            'async' => true
        ) );

}
//--

function sales_items_pos_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false)
{
    if ($editkey)
        set_editor('item', $name, $editkey);

    if ($label != null)
        echo "<td  >$label</td>\n";
    echo sales_items_pos_list($name, $selected_id, $all_option, $submit_on_change,
        '', array('cells'=>true));
}

function sales_items_pos_list($name, $selected_id=null, $all_option=false,
                              $submit_on_change=false, $type='', $opts=array())
{
    // all sales codes
    $sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
			 i.inactive, if(count(*)>1, '0', s.editable) as editable
			FROM
			".TB_PREF."stock_master s,
			".TB_PREF."item_codes i
			LEFT JOIN
			".TB_PREF."stock_category c
			ON i.category_id=c.category_id
			WHERE i.stock_id=s.stock_id
      AND mb_flag != 'F'";


    if ($type == 'local')	{ // exclude foreign codes
        $sql .=	" AND !i.is_foreign";
    } elseif ($type == 'kits') { // sales kits
        $sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
    }
    $sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale";
    $sql .= " GROUP BY i.item_code";

    return combo_pos_input($name, $selected_id, $sql, 'i.item_code', 'c.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("i.item_code", "c.description", "i.description"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'size'=>5,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','i.item_code'),
                'editable' => 30,
                'max' => 25
            ), $opts), $type == 'kits' ? $type : "stock_sales" );
}

function transaction_list($name, $selected_id=null, $name_no="", $name_yes="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("Local");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Import");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function transaction_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo transaction_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function transaction_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	transaction_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}

function transaction_list2($name, $selected_id=null, $name_all="", $name_no="", $name_yes="", $submit_on_change=false)
{
	$items = array();
	$items['2'] = strlen($name_all) ? $name_all : _("All");
	$items['0'] = strlen($name_no) ? $name_no : _("Local");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Import");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function transaction_list_cells2($label, $name, $selected_id=null, $name_all="", $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo transaction_list2($name, $selected_id, $name_all, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function transaction_list_row2($label, $name, $selected_id=null, $name_all="", $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	transaction_list_cells2(null, $name, $selected_id, $name_all, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}

function item_pref_list($name, $selected_id=null, $spec_opt=true,$submit_on_change=false)
{
$sql ="SELECT name,label_value FROM ".TB_PREF."item_pref WHERE name NOT IN('combo1','combo2','combo3','combo4','combo5','combo6','total_amount','total_combo','total_date','total_text')

AND item_enable =1 ";
	return combo_input($name, $selected_id, $sql, 'name', 'label_value',
		array(
			'spec_option' => $spec_opt === true ? _("SELECT") : $spec_opt,
		'select_submit'=> $submit_on_change,));
}

function item_pref_list_cells($label, $name, $selected_id=null, $spec_opt=false,$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo item_pref_list($name, $selected_id, $spec_opt,$submit_on_change);
	echo "</td>\n";
}

function item_pref_list_row($label, $name, $selected_id=null, $spec_opt=false,$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	item_pref_list_cells(null, $name, $selected_id, $spec_opt,$submit_on_change);
	echo "</tr>\n";
}
//////----------------------for sales type list-----------------------//////
function wht_tax_category_list($name, $selected_id=null, $spec_option=false )
{
	$sql = "SELECT id,description, inactive FROM ".TB_PREF."wht_tax_category";

	return combo_input($name, $selected_id, $sql, 'id', 'description', array(
		'spec_option' => $spec_option === true ? _("All Tax Category") : $spec_option,
	));
}

function wht_tax_category_list_cells($label, $name, $selected_id=null, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo wht_tax_category_list($name, $selected_id, $spec_option);
	echo "</td>\n";
}

function wht_tax_category_list_row($label, $name, $selected_id=null, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	wht_tax_category_list_cells(null, $name, $selected_id, $spec_option);
	echo "</tr>\n";
}
//---------------------
function text_cells_ex_disabled($label, $name, $size, $max=null, $init=null, $title=null,
                                $labparams=null, $post_label=null, $submit_on_change=false, $width=70)
{
    global $Ajax;

    default_focus($name);
    if (!isset($_POST[$name]) || $_POST[$name] == "")
    {
        if ($init)
            $_POST[$name] = $init;
        else
            $_POST[$name] = "";
    }
    if ($label != null)
        label_cell($label, $labparams);

    if (!isset($max))
        $max = $size;

    echo "<td>";
    $class = $submit_on_change ? 'class="searchbox"' : '';
    echo "<input readonly style='width:$width'  $class type=\"text\" name=\"$name\" size=\"$size\" maxlength=\"$max\"   value='".$_POST[$name]."' "
        .($title ? " title='$title'": '')." >";
    if ($post_label)
        echo " " . $post_label;

    echo "</td>\n";
    $Ajax->addUpdate($name, $name, $_POST[$name]);
}


function items_search_list($name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($reference) ? $reference: _("Select");
	$items['1'] = strlen($reference) ? $reference: _("Item Code");
	$items['2'] = strlen($trans_no) ? $trans_no : _("Name");
	$items['3'] = strlen($memo) ? $memo : _("Packing");
	$items['4'] = strlen($cheque) ? $cheque : _("Description");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function  items_search_list_cells($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo items_search_list($name, $selected_id,$reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</td>\n";
}

function items_search_list_row($label, $name, $selected_id=null, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	items_search_list_cells(null, $name, $selected_id, $reference="", $trans_no="", $memo='',$cheque='',$submit_on_change);
	echo "</tr>\n";
}




function combos_search_list($name, $selected_id=null,$spec_option=false, $Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=true)
{
	$sql ="SELECT name,label_value FROM ".TB_PREF."item_pref WHERE name  IN('combo1','combo2','combo3','combo4','combo5','combo6')

AND item_enable =1 ";
	return combo_input($name, $selected_id, $sql, 'name', 'label_value',
		array(
			'spec_option' => $spec_option === true ? _("SELECT") : $spec_option,
		'select_submit'=> $submit_on_change,
		'async' => false
		
		));
}

function  combos_search_list_cells($label, $name, $selected_id=null,$spec_option=false,$Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo combos_search_list($name, $selected_id,$spec_option,$Combo1, $Combo2, $Combo3,$Combo4,$Combo5,$Combo6,$submit_on_change);
	echo "</td>\n";
}

function combos_search_list_row($label, $name, $selected_id=null, $spec_option=false,$Combo1="", $Combo2="", $Combo3='',$Combo4='',$Combo5='',$Combo6='',$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	combos_search_list_cells(null, $name, $selected_id, $spec_option,$Combo1, $Combo2, $Combo3,$Combo4,$Combo5,$Combo6,$submit_on_change);
	echo "</tr>\n";
}

function challan_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $rejoin="",$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_list_cells_new(null, $name, $selected_id, $name_yes, $name_no,$rejoin, $submit_on_change);
	echo "</tr>\n";
}
function challan_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="",$rejoin="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo new_yesno_list($name, $selected_id, $name_yes, $name_no,$rejoin, $submit_on_change);
	echo "</td>\n";
}
function challan_list($name, $selected_id=null, $name_yes="", $name_no="",$rejoin="",$submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("No");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");
	$items['2'] = strlen($name_yes) ? $rejoin : _("No");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}
//=============================================================================================
function price_list_logo($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("Price");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Standard Cost");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function price_list_cells_logo($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo price_list_logo($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function price_list_row_logo($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    price_list_cells_logo(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}
//=============================================================================================
function price_list_logo1($name, $selected_id=null, $name4="", $name3="", $name2="", $name1="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name1) ? $name1 : _("Standard Cost");
    $items['1'] = strlen($name2) ? $name2 : _("Sales Price");
    $items['2'] = strlen($name3) ? $name3 : _("Purchase Cost");
    $items['3'] = strlen($name4) ? $name4 : _("Costed Value");
    
    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function price_list_cells_logo1($label, $name, $selected_id=null, $name3="", $name2="", $name1="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo price_list_logo1($name, $selected_id, $name3, $name2, $name1, $submit_on_change);
    echo "</td>\n";
}

function price_list_row_logo1($label, $name, $selected_id=null, $name3="", $name2="", $name1="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    price_list_cells_logo1(null, $name, $selected_id, $name3, $name2, $name1, $submit_on_change);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------------------------------
function bank_accounts_list_all_pos($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    $sql = "SELECT ".TB_PREF."bank_accounts.account_code, bank_account_name, bank_curr_code, inactive
		    FROM ".TB_PREF."bank_accounts ";

	return combo_pos_input($name, $selected_id, $sql, 'account_code', 'bank_account_name',
		array('format' => '_format_add_curr',
			'select_submit'=> $submit_on_change,
			'spec_option' => $spec_option,
			'spec_id' => '',
			'async' => false
		) );
}

function bank_accounts_list_all_cells_pos($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo bank_accounts_list_all_pos($name, $selected_id, $submit_on_change,$spec_option);
	echo "</td>\n";
}

function bank_accounts_list_all_row_pos($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
	echo "<tr><td class=''>$label</td>";
	bank_accounts_list_all_cells_pos(null, $name, $selected_id, $submit_on_change, $spec_option);
	echo "</tr>\n";
}


//S.H.G for SMS
function types_name_list($name, $selected_id=null)
{
    $sql = "SELECT id, types_name FROM ".TB_PREF."sms_type_template";
    return combo_input($name, $selected_id, $sql, 'id', 'types_name', array());
}

function types_name_list_cells($label, $name, $selected_id=null)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo types_name_list($name, $selected_id);
    echo "</td>\n";
}

function types_name_list_row($label, $name, $selected_id=null)
{
    echo "<tr><td class=''>$label</td>";
    types_name_list_cells(null, $name, $selected_id);
    echo "</tr>\n";
}

//---


function yesno_reconcile_list($name, $selected_id=null,$name_all="", $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_all) ? $name_all : _("All");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Yes");
	$items['2'] = strlen($name_no) ? $name_no : _("No");

 	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_reconcile_list_cells($label, $name, $selected_id=null,$name_all="", $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_reconcile_list($name, $selected_id, $name_all,$name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_reconcile_list_row($label, $name, $selected_id=null, $name_all="",$name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_reconcile_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}


//---


// for import invoice

function yesno_nkr_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	$items = array();
	$items['0'] = strlen($name_no) ? $name_no : _("Allocation On Amount");
	$items['1'] = strlen($name_yes) ? $name_yes : _("Allocation On Quantity");

	return array_selector($name, $selected_id, $items,
		array(
			'select_submit'=> $submit_on_change,
			'async' => false ) ); // FIX?
}

function yesno_nkr_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo yesno_nkr_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</td>\n";
}

function yesno_nkr_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	yesno_nkr_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
	echo "</tr>\n";
}
//==============================================================================
function yesno_list_curr_close($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("All");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Show Only Closing Balance");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function yesno_list_cells_curr_close($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_list_curr_close($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function yesno_list_row_curr_close($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_list_cells_curr_close(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}
//-------------------------------------------------------------------------------------------------------------------
function yesno_list_pl_sales($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("Profit and Loss");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Sales Book");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function yesno_list_cells_pl_sales($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_list_pl_sales($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function yesno_list_row_pl_sales($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_list_cells_pl_sales(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}



//sales_enablecustom_fields
function item_pref_sales_list($name, $selected_id=null, $spec_opt=true,$submit_on_change=false)
{
$sql ="SELECT name,label_value FROM ".TB_PREF."item_pref WHERE name NOT IN('formula','itemwise_discount','sales_persons','combo1','combo2','combo3','combo4','combo5','combo6', 'date1', 'date2', 'date3', 'con_factor','total_amount', 'total_combo', 'total_text')

AND sale_enable =1 ";
	return combo_input($name, $selected_id, $sql, 'name', 'label_value',
		array(
			'spec_option' => $spec_opt === true ? _("SELECT") : $spec_opt,
		'select_submit'=> $submit_on_change,));
}

function item_pref_sales_list_cells($label, $name, $selected_id=null, $spec_opt=false,$submit_on_change=false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td>";
	echo item_pref_sales_list($name, $selected_id, $spec_opt,$submit_on_change);
	echo "</td>\n";
}

function item_pref_sales_list_row($label, $name, $selected_id=null, $spec_opt=false,$submit_on_change=false)
{
	echo "<tr><td class=''>$label</td>";
	item_pref_sales_list_cells(null, $name, $selected_id, $spec_opt,$submit_on_change);
	echo "</tr>\n";
}
//--------------------------------------------------------------------------------------------------------------------
function items_list_multi($name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $opts=array(), $editkey = false)
{
    global $all_items;

    $sql = "SELECT stock_id, s.description, s.inactive
    FROM ".TB_PREF."stock_master s";


    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'stock_id', 'description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => $all_items,
                'order' => array('description','stock_id'),
                'multi' => true,
                'type' => 1,
                'size' => 20,
                'height' => 20,
                //'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                //'spec_id' => $all_items,
                'search' => array("stock_id","description"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'select_submit'=> $submit_on_change,
                'category' => 2,
            ), $opts) );
    if ($editkey)
        $ret .= add_edit_combo('item');
    return $ret;

}
function items_list_cells_multi($label, $name, $selected_id=null, $all_option=false,
                                      $submit_on_change=false, $all=false, $editkey = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo items_list_multi($name, $selected_id, $all_option, $submit_on_change,
        array('cells'=>true, 'show_inactive'=>$all), $editkey);
}
function all_items_list_multi($name, $selected_id=null,
                                            $all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
    return items_list_multi($name, $selected_id, $all_option, $submit_on_change,
        array(
//			'where'=>array("mb_flag!= 'B'"),
            'show_inactive'=>$all), $editkey);
}


//-------------------------------------------
function barcode_temp_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("Default");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Without Barcode");
    $items['2'] = strlen($name_yes) ? $name_yes : _("Only Barcode");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function barcode_temp_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo barcode_temp_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function barcode_temp_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    barcode_temp_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}


function deliver_invoice_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("Deliver to");
    $items['1'] = strlen($name_yes) ? $name_yes : _("Invoice to");
    $items['2'] = strlen($name_yes) ? $name_yes : _("Deliver to/Invoice to");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function deliver_invoice_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo deliver_invoice_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function deliver_invoice_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    deliver_invoice_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}
///////////////
function time_invoice_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($name_no) ? $name_no : _("All");
    $items['1'] = strlen($name_yes) ? $name_yes : _(">");
    $items['2'] = strlen($name_yes) ? $name_yes : _("<");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function time_invoice_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo time_invoice_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function time_invoice_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    time_invoice_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}

//-------------------------------------------
function yesno_week_list($name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $allweeks="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($all) ? $all : _("0");
    $items['1'] = strlen($week_1) ? $week_1 : _("1");
    $items['2'] = strlen($week_2) ? $week_2 : _("2");
    $items['3'] = strlen($week_3) ? $week_3 : _("3");
    $items['4'] = strlen($week_4) ? $week_4 : _("4");
    $items['5'] = strlen($allweeks) ? $allweeks : _("5");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
//            'spec_option' =>$all_option===true ? _("All Weeks") : $all_option,
            'async' => false ) ); // FIX?
}

function yesno_week_list_cells($label, $name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $allweeks="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_week_list($name, $selected_id, $all, $week_1, $week_2, $week_3, $week_4, $allweeks, $submit_on_change);
    echo "</td>\n";
}

function yesno_week_list_row($label, $name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $allweeks="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_week_list_cells(null, $name, $selected_id, $all, $week_1, $week_2, $week_3, $week_4, $allweeks, $submit_on_change);
    echo "</tr>\n";
}
///////////////////////////////
function yesno_area_group_list($name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $submit_on_change=false)
{
    $items = array();
    $items['0'] = strlen($all) ? $all : _("0");
    $items['1'] = strlen($week_1) ? $week_1 : _("1");
    $items['2'] = strlen($week_2) ? $week_2 : _("2");
    //$items['3'] = strlen($week_3) ? $week_3 : _("3");
   // $items['4'] = strlen($week_4) ? $week_4 : _("4");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
//            'spec_option' =>$all_option===true ? _("All Weeks") : $all_option,
            'async' => false ) ); // FIX?
}

function yesno_area_group_list_cells($label, $name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_area_group_list($name, $selected_id, $all, $week_1, $week_2, $week_3, $week_4, $submit_on_change);
    echo "</td>\n";
}

function yesno_area_group_list_row($label, $name, $selected_id=null, $all="", $week_1="", $week_2="", $week_3="", $week_4="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_area_group_list_cells(null, $name, $selected_id, $all, $week_1, $week_2, $week_3, $week_4, $submit_on_change);
    echo "</tr>\n";
}

//------------------------types-------------------------------//

function rep_types_list($name, $selected_id=null, $spec_opt=false, $submit_on_change=true)
{
    $sql = "SELECT rep_type, description, inactive FROM ".TB_PREF."rep_types";
    return combo_input($name, $selected_id, $sql, 'rep_type', 'description',
        array('spec_option' => $spec_opt,
        'select_submit'=> $submit_on_change));
}

function rep_types_list_cells($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=true)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo rep_types_list($name, $selected_id, $spec_opt, $submit_on_change);
    echo "</td>\n";
}

function rep_types_list_row($label, $name, $selected_id=null, $spec_opt=false, $submit_on_change=true)
{
    echo "<tr><td class=''>$label</td>";
    rep_types_list_cells(null, $name, $selected_id, $spec_opt, $submit_on_change);
    echo "</tr>\n";
}

//====================================================================================
function sales_items_list2($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $type='', $opts=array(), $customer_id)
{
    // all sales codes

    $sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
			 i.inactive, s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6  ,  s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6 ,if(count(*)>1, '0', s.editable) as editable
			FROM
			".TB_PREF."stock_master s,
			".TB_PREF."item_codes i
			LEFT JOIN
			".TB_PREF."stock_category c
			ON i.category_id=c.category_id
			LEFT JOIN
			".TB_PREF."item_customers ic
			ON i.category_id=ic.stock_id
			WHERE i.stock_id=s.stock_id
      AND mb_flag != 'F'";

if ($customer_id != '')
$sql .=	" AND ic.customer_id=".db_escape($customer_id);

    if ($type == 'local')	{ // exclude foreign codes
        $sql .=	" AND !i.is_foreign";
    } elseif ($type == 'kits') { // sales kits
        $sql .=	" AND !i.is_foreign AND i.item_code!=i.stock_id";
    }
    $sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale  ";
    $sql .= " GROUP BY i.item_code
    ";

    return combo_input($name, $selected_id, $sql, 'i.item_code'
    , 'c.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("i.item_code", "c.description", "i.description","s.text4","s.text1","s.text3",
                "s.text2","s.text5","s.text6"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'size'=>15,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','i.item_code'),
                'editable' => 30,
                'max' => 255
            ), $opts), $type == 'kits' ? $type : "stock_sales" );
}
function sales_items_list_cells2($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false, $customer_id)
{
    if ($editkey)
        set_editor('item', $name, $editkey);

    if ($label != null)
        echo "<td>$label</td>\n";
    echo sales_items_list2($name, $selected_id, $all_option, $submit_on_change,
        '', array('cells'=>true),$customer_id);
}
//----------------------------------------------------------------//
function supp_items_list($name, $selected_id=null, $all_option=false,
                           $submit_on_change=false, $type='', $opts=array(), $supplier_id)
{
    // all sales codes

    $sql = "SELECT i.item_code, i.description, c.description, count(*)>1 as kit,
          i.inactive, s.text1,s.text4,s.text2,s.text3 , s.text5  , s.text6   ,
          s.combo1 ,s.combo2 ,s.combo3 ,s.combo4 ,s.combo5 ,s.combo6, if(count(*)>1, '0', s.editable) as editable
         FROM
         ".TB_PREF."stock_master s,
         ".TB_PREF."item_codes i
         LEFT JOIN
         ".TB_PREF."stock_category c
         ON i.category_id=c.category_id
        	LEFT JOIN
			".TB_PREF."suppliers_items ic
			ON c.category_id=ic.stock_id
         WHERE i.stock_id=s.stock_id
      AND mb_flag != 'F'";

    if ($supplier_id != '')
        $sql .=    " AND ic.supplier=".db_escape($supplier_id);

    if ($type == 'local')  { // exclude foreign codes
        $sql .=    " AND !i.is_foreign";
    } elseif ($type == 'kits') { // sales kits
        $sql .=    " AND !i.is_foreign AND i.item_code!=i.stock_id";
    }
    $sql .= " AND !i.inactive AND !s.inactive AND !s.no_sale  ";
    $sql .= " GROUP BY i.item_code
    ";

    return combo_input($name, $selected_id, $sql, 'i.item_code', 'c.description',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => ALL_TEXT,
                'search_box' => true,
                'search' => array("i.item_code", "c.description", "i.description"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'size'=>15,
                'select_submit'=> $submit_on_change,
                'category' => 2,
                'order' => array('c.description','i.item_code'),
                'editable' => 30,
                'max' => 255
            ), $opts), $type == 'kits' ? $type : "stock_sales" );
}
function supp_items_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $editkey=false, $supplier_id)
{
    if ($editkey)
        set_editor('item', $name, $editkey);

    if ($label != null)
        echo "<td>$label</td>\n";
    echo supp_items_list($name, $selected_id, $all_option, $submit_on_change,
        '', array('cells'=>true),$supplier_id);
}

// Offers drop down
function offers_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    $sql = "SELECT id, CONCAT(offer_code, ' - ', title) as OfferTitle FROM ".TB_PREF."offer_details";
    return combo_input($name, $selected_id, $sql, 'id', 'OfferTitle',
        array('spec_option' => $spec_option,
            'select_submit' => $submit_on_change,
            'async' => false));
}

function offers_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo offers_list($name, $selected_id, $submit_on_change, $spec_option);
    echo "</td>\n";
}

function offers_list_row($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    echo "<tr><td class=''>$label</td>";
    offers_list_cells(null, $name, $selected_id, $submit_on_change, $spec_option);
    echo "</tr>\n";
}
// Offers drop down
function offers_items_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    $sql = "SELECT id, code FROM ".TB_PREF."sales_offers";

    return combo_input($name, $selected_id, $sql, 'id', 'code',
        array('spec_option' => $spec_option,
            'select_submit' => $submit_on_change,
            'async' => false));
}

function offers_items_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo offers_items_list($name, $selected_id, $submit_on_change, $spec_option);
    echo "</td>\n";
}

function offers_items_list_row($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    echo "<tr><td class=''>$label</td>";
    offers_items_list_cells(null, $name, $selected_id, $submit_on_change, $spec_option);
    echo "</tr>\n";
}
// Distributor profile drop down
function distributor_profile_list($name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    $sql = "SELECT id, CONCAT(name, ' ', title) as DistributorName FROM ".TB_PREF."cust_info";

    return combo_input($name, $selected_id, $sql, 'id', 'DistributorName',
        array('spec_option' => $spec_option,
            'select_submit' => $submit_on_change,
            'async' => false
        ));
}

function distributor_profile_list_cells($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo distributor_profile_list($name, $selected_id, $submit_on_change, $spec_option);
    echo "</td>\n";
}

function distributor_profile_list_row($label, $name, $selected_id=null, $submit_on_change=false, $spec_option=false)
{
    echo "<tr><td class=''>$label</td>";
    distributor_profile_list_cells(null, $name, $selected_id, $submit_on_change, $spec_option);
    echo "</tr>\n";
}



function cust_list($name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $opts=array(), $editkey = false)
{
    global $all_items;

    $sql = "SELECT debtor_no, name, inactive FROM ".TB_PREF."debtors_master  ";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'name',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => $all_items,
                'order' => array('name','debtor_no'),
                'multi' => true,
                'type' => 1,
                'size' => 20,
                'height' => 20,
                //'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                //'spec_id' => $all_items,
                'search' => array("debtor_no","name"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'select_submit'=> $submit_on_change,
                'category' => 2,
            ), $opts) );
    if ($editkey)
        $ret .= add_edit_combo('item');
    return $ret;

}
function supp_list($name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $opts=array(), $editkey = false)
{
    global $all_items;

    $sql = "SELECT supplier_id, supp_name, inactive FROM ".TB_PREF."suppliers  ";

    if ($editkey)
        set_editor('item', $name, $editkey);

    $ret = combo_input($name, $selected_id, $sql, 'supplier_id', 'supp_name',
        array_merge(
            array(
                'format' => '_format_stock_items',
                'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                'spec_id' => $all_items,
                'order' => array('supp_name','supplier_id'),
                'multi' => true,
                'type' => 1,
                'size' => 20,
                'height' => 20,
                //'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                //'spec_id' => $all_items,
                'search' => array("supplier_id","supp_name"),
                'search_submit' => get_company_pref('no_item_list')!=0,
                'select_submit'=> $submit_on_change,
                'category' => 2,
            ), $opts) );
    if ($editkey)
        $ret .= add_edit_combo('item');
    return $ret;

}





function supplier_list_multi($name, $selected_id=null,
                             $all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
    return supp_list($name, $selected_id, $all_option, $submit_on_change,
        array(
//			'where'=>array("mb_flag!= 'B'"),
            'show_inactive'=>$all), $editkey);
}



function customer_list_multi($name, $selected_id=null,
                                            $all_option=false, $submit_on_change=false, $all=false, $editkey=false)
{
    return cust_list($name, $selected_id, $all_option, $submit_on_change,
        array(
//			'where'=>array("mb_flag!= 'B'"),
            'show_inactive'=>$all), $editkey);
}

//------------------------FOR BLOOD GROUP (PAYROLL)-----------------------//

function yesno_list_blood_group($name, $selected_id=null, $spec_opt=true, $o_positive="", $o_negative="", $a_positive="", $a_negative="", $b_positive="", $b_negative="" , $ab_positive="", $ab_negative="",  $submit_on_change=false)
{
    $items = array();
    $items['1'] = strlen($o_positive) ? $o_positive : _("O+");
    $items['2'] = strlen($o_negative) ? $o_negative : _("O-");
    $items['3'] = strlen($a_positive) ? $a_positive : _("A+");
    $items['4'] = strlen($a_negative) ? $a_negative : _("A-");
    $items['5'] = strlen($b_positive) ? $b_positive : _("B+");
    $items['6'] = strlen($b_negative) ? $b_negative : _("B-");
    $items['7'] = strlen($ab_positive) ? $ab_positive : _("AB+");
    $items['8'] = strlen($ab_negative) ? $ab_negative : _("AB-");


    return array_selector($name, $selected_id, $items,
        array(
            'spec_option' => $spec_opt === true ? _("SELECT BLOOD GROUP") : $spec_opt,
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function yesno_list_cells_blood_group($label, $name, $selected_id=null, $spec_opt=false, $o_positive="", $o_negative="", $a_positive="", $a_negative="", $b_positive="", $b_negative="" , $ab_positive="", $ab_negative="",  $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_list_blood_group($name, $selected_id, $spec_opt, $o_positive, $o_negative, $a_positive, $a_negative, $b_positive, $b_negative, $ab_positive, $ab_negative, $submit_on_change);
    echo "</td>\n";
}

function yesno_list_row_blood_group($label, $name, $selected_id=null, $spec_opt=false, $o_positive="", $o_negative="", $a_positive="", $a_negative="", $b_positive="", $b_negative="" , $ab_positive="", $ab_negative="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_list_cells_blood_group(null, $name, $selected_id, $spec_opt, $o_positive, $o_negative, $a_positive, $a_negative, $b_positive, $b_negative, $ab_positive, $ab_negative, $submit_on_change);
    echo "</tr>\n";
}

//-----------------------------------------------------------------------//

// New Filter for custoemr

function customer_items_list($name, $selected_id=null, $all_option=false,
                          $submit_on_change=false, $opts=array(), $editkey = false, $type = "stock",$purchase)
{
    $sql = "SELECT debtor_no, debtor_ref, name, curr_code, inactive 
        FROM ".TB_PREF."debtors_master ";
    if ($editkey)
        set_editor('customer', $name, $editkey);
        $ret = combo_input($name, $selected_id, $sql, 'debtor_no', 'debtor_ref',
            array_merge(
                array(
                    'format' => '_format_stock_items',
                    'spec_option' => $all_option===true ?  _("All Items") : $all_option,
                    'spec_id' => ALL_TEXT,
                    'search_box' => true,
                    'search' => array('debtor_no', "debtor_ref", "name"),
                    'search_submit' => get_company_pref('no_item_list')!=0 && (!isset($opts['fixed_asset']) || !$opts['fixed_asset']),
                    'size'=>10,
                    'select_submit'=> $submit_on_change,
//                    'category' => 2,
                    'order' => array('debtor_ref','name'),
                    'editlink' => $editkey ? add_edit_combo('item') : false,
                    'editable' => true,
                    'max' => 255
                ), $opts), $type == 'kits' ? $type : "customer"  );

    return $ret;
}

function customer_items_list_cells($label, $name, $selected_id=null, $all_option=false,
                                $submit_on_change=false, $all=false, $editkey = false, $opts= array(),$purchase)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo customer_items_list($name, $selected_id, $all_option, $submit_on_change,
        array_merge(array('cells'=>true, 'show_inactive'=>$all), $opts), $editkey,null,$purchase);

}


function yesno_bydefault_yes_list($name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    $items = array();
    $items['1'] = strlen($name_no) ? $name_no : _("Yes");
    $items['0'] = strlen($name_yes) ? $name_yes : _("No");

    return array_selector($name, $selected_id, $items,
        array(
            'select_submit'=> $submit_on_change,
            'async' => false ) ); // FIX?
}

function yesno_bydefault_yes_list_cells($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo yesno_bydefault_yes_list($name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</td>\n";
}

function yesno_bydefault_yes_list_row($label, $name, $selected_id=null, $name_yes="", $name_no="", $submit_on_change=false)
{
    echo "<tr><td class=''>$label</td>";
    yesno_bydefault_yes_list_cells(null, $name, $selected_id, $name_yes, $name_no, $submit_on_change);
    echo "</tr>\n";
}
//dimension
function dimensionn_tag_list($name, $height, $id, $multi=false, $all=false, $spec_opt = false)
{
    // Get tags
    global $path_to_root;
    include_once($path_to_root . "/admin/user_dimension.php");
    $results = get_dimension_id();


    while ($user = db_fetch($results))
        $users[$user['id']] = $user['name'];

    if (!isset($users)) {
        $users[''] = $all ? _("No tags defined.") : _("No active tags defined.");
        $spec_opt = false;
    }
    return array_selector($name, null, $users,
        array(
            'multi' => $multi,
            'height' => $height,
            'spec_option'=> $spec_opt,
            'spec_id' => -1,
        ) );
}

function dimensionn_tag_list_cells($label, $name, $height, $id, $mult=false, $all=false, $spec_opt = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo dimensionn_tag_list($name, $height, $id, $mult, $all, $spec_opt);
    echo "</td>\n";

}

function dimensionn_tag_list_row($label, $name ,$height, $id, $mult=false, $all=false, $spec_opt = false)
{
    echo "<tr><td class=''>$label</td>";
    dimensionn_tag_list_cells(null, $name, $height, $id, $mult, $all, $spec_opt);
    echo "</tr>\n";
}

//usertag
function user_tag_list($name, $height, $id, $multi=false, $all=false, $spec_opt = false)
{
    // Get tags
    global $path_to_root;
    include_once($path_to_root . "/admin/db/users_db.inc");
    $results = get_users_id();


    while ($user = db_fetch($results))
        $users[$user['id']] = $user['user_id'];

    if (!isset($users)) {
        $users[''] = $all ? _("No tags defined.") : _("No active tags defined.");
        $spec_opt = false;
    }
    return array_selector($name, null, $users,
        array(
            'multi' => $multi,
            'height' => $height,
            'spec_option'=> $spec_opt,
            'spec_id' => -1,
        ) );
}

function user_tag_list_cells($label, $name, $height, $id, $mult=false, $all=false, $spec_opt = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo user_tag_list($name, $height, $id, $mult, $all, $spec_opt);
    echo "</td>\n";

}

function user_tag_list_row($label, $name ,$height, $id, $mult=false, $all=false, $spec_opt = false)
{
    echo "<tr><td class=''>$label</td>";
    user_tag_list_cells(null, $name, $height, $id, $mult, $all, $spec_opt);
    echo "</tr>\n";
}

//gl_accounts
function account_tag_list($name, $height, $id, $multi=false, $all=false, $spec_opt = false)
{
    // Get tags
    global $path_to_root;
    include_once($path_to_root . "/admin/user_accounts.php");
    $results = get_account_id();


    while ($user = db_fetch($results))
        $users[$user['account_code']] = $user['account_name'];

    if (!isset($users)) {
        $users[''] = $all ? _("No tags defined.") : _("No active tags defined.");
        $spec_opt = false;
    }
    return array_selector($name, null, $users,
        array(
            'multi' => $multi,
            'height' => $height,
            'spec_option'=> $spec_opt,
            'spec_id' => -1,
        ) );
}

function account_tag_list_cells($label, $name, $height, $id, $mult=false, $all=false, $spec_opt = false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo account_tag_list($name, $height, $id, $mult, $all, $spec_opt);
    echo "</td>\n";

}

function account_tag_list_row($label, $name ,$height, $id, $mult=false, $all=false, $spec_opt = false)
{
    echo "<tr><td class=''>$label</td>";
    account_tag_list_cells(null, $name, $height, $id, $mult, $all, $spec_opt);
    echo "</tr>\n";
}

//class_type_for_dashborad_of_gl_inquiry
function gl_class_types_list($name, $selected_id=null, $all_option=false, $all=true,$submit_on_change=true)
{
    global $all_items;

    $sql = "SELECT cid, class_name FROM ".TB_PREF."chart_class";

    return combo_input($name, $selected_id, $sql, 'cid', 'class_name',
        array(
            'format' => '_format_account',
            'order' => array('cid', 'class_name', 'ctype'),
            'select_submit'=> $submit_on_change,
            //  'spec_option' =>$all_option,
            'spec_option' => $all_option === true ? _("All ") : $all_option,
            'spec_id' => $all_items,
            'async' => false

        ) );
}

function gl_class_types_list_cells($label, $name, $selected_id=null, $all_option=false, $all=false,$submit_on_change=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo gl_class_types_list($name, $selected_id, $all_option, $all,$submit_on_change);
    echo "</td>\n";
}

function gl_class_types_list_row($label, $name, $selected_id=null, $all_option=false,	$all=false, $submit_on_change=true)
{
    echo "<tr><td class=''>$label</td>";
    gl_class_types_list_cells(null, $name, $selected_id, $all_option, $submit_on_change,
        $all);
    echo "</tr>\n";
}

function reorder_locations_list($name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
    global $all_items;

    $current_user = $_SESSION["wa_current_user"]->user;

    $sql = "SELECT loc.loc_code, loc.location_name, loc.inactive FROM 
    ".TB_PREF."locations AS loc";

    return combo_input($name, $selected_id, $sql, 'loc_code', 'location_name',
        array(
            'spec_option' => $all_option === true ? _("All Locations") : $all_option,
            'spec_id' => ALL_TEXT,
            'order' => 'loc_code',
            'select_submit'=> $submit_on_change
        ) );
}

function reorder_locations_list_cells($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>";
    echo reorder_locations_list($name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
    echo "</td>\n";
}

function reorder_locations_list_row($label, $name, $selected_id=null, $all_option=false, $submit_on_change=false, $fixed_asset=false)
{
    echo "<tr><td class=''>$label</td>";
    reorder_locations_list_cells(null, $name, $selected_id, $all_option, $submit_on_change, $fixed_asset);
    echo "</tr>\n";
}

