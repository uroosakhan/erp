<?php
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/types.inc");
include_once($path_to_root . "/includes/current_user.inc");
include_once($path_to_root . "/includes/ui/ui_lists.inc");


function employee_name($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT employee_id, emp_name  FROM ".TB_PREF."employee ";

	return combo_input($name, $selected_id, $sql, 'employee_id', 'emp_name',
	array(
		 'order' => array('emp_name'),
		 'spec_id' => 0,
		 'select_submit'=> $submit_on_change,
	//	  'async' => false,
	) );
	
} 

function emp_final_result($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."final_result";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		//'spec_option' => $spec_option === true ? _("All Category") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Qualification'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function emp_final_result_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_final_result($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_final_result_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_final_result($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function emp_category($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."category";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		//'spec_option' => $spec_option === true ? _("All Category") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Qualification'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function emp_category_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_category($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_category_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_category($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

//ansar


function employee_list2($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT employee_id,  emp_name FROM ".TB_PREF."employee ";

	//$mode = get_company_pref('no_employee_list');

	//if ($editkey)
	//	set_editor('employee', $name, $editkey);

	$ret = combo_input($name, $selected_id, $sql,  'employee_id', 'emp_name',
	array(
	    //'format' => '_format_add_curr',
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

function employee_list2_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=true, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo employee_list2($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function employee_list2_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo employee_list2($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}
//ansar 22 Sep
function applicant_national($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."nationality";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Nationality") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Nationality'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function applicant_national_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo applicant_national($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function applicant_national_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo applicant_national($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

//=====================------=======-==================-===========-==========================
//ansar 4 july
function emp_document_type($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."document_type ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Title") : $spec_option,
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

function emp_document_type_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_document_type($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_document_type_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_document_type($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
//////////////////////////////////////////////////////////////////
function emp_title($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."title ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Title") : $spec_option,
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

function emp_title_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_title($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_title_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_title($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
//ansasr
function emp_info_leave_type($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."leave_type";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Leave Type") : $spec_option,
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

function  emp_info_leave_type_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_info_leave_type($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_info_leave_type_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_info_leave_type($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

//----------------------------------------------------------------------------------------------
function adv_qualification($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."qualification";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Qualification") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_qualification_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_qualification($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_qualification_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_qualification($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_experience($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."experience";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Experience") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_experience_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_experience($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_experienc_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_experience($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_job($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."job_type";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Job Type") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_job_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_job($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_job_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_job($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_age($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."age";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Age") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_age_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_age($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_age_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_age($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_salary($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."salary_range";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Salary") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_salary_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_salary($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_salary_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_salary($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_travel($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."travel";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Travel") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_travel_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_travel($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_travel_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_travel($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function adv_location($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."location_name";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Location") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select Advertisment'),
		'show_inactive'=>$all
		));
	if ($editkey)
		$ret .= add_edit_combo('supplier');		
	return $ret;
}

function adv_location_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo adv_location($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function adv_location_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo adv_location($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function advertisment_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT advertisment_id, advertisment_code, description,inactive FROM ".TB_PREF."advertisment ";
	$ret = combo_input($name, $selected_id, $sql,  'advertisment_id', 'description',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Advertisment") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment; F2 - entry new employee') :
		_('Select advertisment'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('advertisment');
	return $ret;
}

function advertisment_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo advertisment_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function advertisment_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo advertisment_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}
function emp_gen($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."gen ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Gender") : $spec_option,
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

function emp_gen_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_gen($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_gen_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_gen($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
function applicant_list($name, $selected_id=null, $spec_option=false, $submit_on_change=false, 
	$show_inactive=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT add_id, applicant_code, full_name, inactive FROM ".TB_PREF."add_applicant ";
	$ret = combo_input($name, $selected_id, $sql,  'add_id', 'full_name',
	array(
	    'format' => '_format_add_curr',
	    'order' => array('full_name'),
		'search_box' => $mode!=0,
		'type' => 1,
		'size' => 20,
		'spec_option' => $spec_option === true ? _("All Applicant") : $spec_option,
		'spec_id' => $all_items,
		'select_submit'=> $submit_on_change,
		'async' => false,
		'sel_hint' => $mode ? _('Press Space tab to filter by name fragment') :
		_('Select applicant'),
		'show_inactive' => $show_inactive
	) );
	if ($editkey)
		$ret .= add_edit_combo('advertisment');
	return $ret;
}

function applicant_list_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td>\n";
	echo "<td nowrap>";
	echo applicant_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n";
}

function applicant_list_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $show_inactive=false, $editkey = false)
{
	global $path_to_root;

	echo "<tr><td class='label'>$label</td><td nowrap>";
	echo applicant_list($name, $selected_id, $all_option, $submit_on_change,
		$show_inactive, $editkey);
	echo "</td>\n</tr>\n";
}
//_________________________________________________ ______________________________________

function emp_grade($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
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

function emp_grade_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_grade($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_grade_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_grade($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

//=================+++++++++++++++++++++_____________________________________________

function emp_desg($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."desg ";

//	$mode = get_company_pref('no_supplier_list');

//	if ($editkey)
//		set_editor('supplier', $name, $editkey);
		
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Designation") : $spec_option,
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

function emp_desg_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_desg($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_desg_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_desg($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}

//----------------------------------------------------------------============================
function emp_dept_list_cell($label, $name)
{ 
   $sql = "SELECT id, description FROM ".TB_PREF."dept ";
   $result = db_query( $sql, "Could't get employees");
  //onchange='showUser(this.value)'
   echo "<td>".$label."</td>
   <td><select style='border: 1px solid #dfe1e2; box-shadow: 1px 0px 1px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7   inset; margin: 1px 0 2px; padding: 6px 4px; font-size: 12px;'  
   name=".$name."     >";
   echo "<option value='null'> Select Department </option>";
   while($myrow = db_fetch($result))
   {
   echo "<option value=".$myrow['id']."> ".$myrow['description']."  </option>";
   }
   echo "</select>
   <div id='txtHint'></div>
   </td>";
}

function emp_month_list_cell($name)
{ 
   $sql = "SELECT id, description FROM ".TB_PREF."month ";
   $result = db_query( $sql, "Could't get month");
   
   echo "<td><select style='border: 1px solid #dfe1e2; box-shadow: 1px 0px 1px rgba(0, 0, 0, 0.06) inset, 0 0 1px #95a2a7   inset; margin: 1px 0 2px; padding: 6px 4px; font-size: 12px;'  
   name=".$name."  >";
    echo "<option value='null'> Select Month </option>";
   while($myrow = db_fetch($result))
   {
   echo "<option value=".$myrow['id']."> ".$myrow['description']."  </option>";
   }
   echo "</select>
   <div id='txtHint'></div>
   </td>";
}
//asad


//ansar 18 august


function emp_allowance($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."allowance ";
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Allowance") : $spec_option,
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

function emp_allowance_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_allowance($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_allowance_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=true, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_allowance($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
//
function emp_deduction($name, $selected_id=null, $spec_option=false, $submit_on_change=false,
	$all=false, $editkey = false)
{
	global $all_items;

	$sql = "SELECT id, description FROM ".TB_PREF."deduction ";
	$ret = combo_input($name, $selected_id, $sql, 'id', 'description',
	array(
		//'format' => '_format_add_curr',
	    'order' => array('description'),
		'search_box' => $mode!=0,
		'type' => 1,
		'spec_option' => $spec_option === true ? _("All Deduction") : $spec_option,
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

function emp_deduction_cells($label, $name, $selected_id=null, $all_option=false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	if ($label != null)
		echo "<td>$label</td><td>\n";
		echo emp_deduction($name, $selected_id, $all_option, $submit_on_change, 
		$all, $editkey);
		echo "</td>\n";
}

function emp_deduction_row($label, $name, $selected_id=null, $all_option = false, 
	$submit_on_change=false, $all=false, $editkey = false)
{
	echo "<tr><td class='label'>$label</td><td>";
	echo emp_deduction($name, $selected_id, $all_option, $submit_on_change,
		$all, $editkey);
	echo "</td></tr>\n";
}
//____________________________________________________________________________
//-----------------------asad------------------------------------------------------------
function customer_list_1($name, $selected_id=null, $submit_on_change=false, $special_option=false)
{
	$sql = "SELECT debtor_no, debtor_ref, inactive FROM ".TB_PREF."debtors_master ";

	return combo_input($name, $selected_id, $sql, 'id', 'sales_type',
	array(
		 'order' => array('debtor_ref'),
		 'spec_id' => 0,
		 'select_submit'=> $submit_on_change,
	//	  'async' => false,
	) );
	
}
//----------------------------------------------------------------------------------------

?>