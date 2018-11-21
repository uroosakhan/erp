<?php

/*
	Retrieve value of POST variable(s).
	For $name passed as array $dflt is not used, 
	default values can be passed as values with non-numeric keys instead.
	If some field have user formatted numeric value, pass float default value to
	convert automatically to POSIX.
*/
function get_post($name, $dflt='')
{
	if (is_array($name)) {
		$ret = array();
		foreach($name as $key => $dflt)
			if (!is_numeric($key)) {
				$ret[$key] = is_float($dflt) ? input_num($key, $dflt) : get_post($key, $dflt);
			} else {
				$ret[$dflt] = get_post($dflt, null);
			}
		return $ret;
	} else
		return is_float($dflt) ? input_num($name, $dflt) :
				((!isset($_POST[$name]) /*|| $_POST[$name] === ''*/) ? $dflt : $_POST[$name]);
}
//---------------------------------------------------------------------------------
$form_nested = -1;
//Ramsha Ather---->>For Making This Page Responsive
function start_outer_table_task($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
    if ($br)
        br();
    start_table($class, $extra, $padding, $spacing);
    echo "<tr valign=top><td>\n"; // outer table


    echo '<style>

	@media only screen and (max-width: 768px) {
	 
   table, thead, tbody, th, td, tr { 
		display: block; 
	}
    /* For mobile phones: */
   
    }
</style>';


}
function start_form($multi=false, $dummy=false, $action="", $name="")
{
	// $dummy - leaved for compatibility with 2.0 API
	global $form_nested;

	if (++$form_nested) return;

	if ($name != "")
		$name = "name='$name'";
	if ($action == "")
		$action = $_SERVER['PHP_SELF'];

	if ($multi)
		echo "<form enctype='multipart/form-data' method='post' action='$action' $name  onSubmit='return false;'>\n";
	else
		echo "<form method='post' action='$action' $name   onSubmit='return false;'>\n";

}

/*
	Flush hidden fields buffer.
*/
function output_hidden()
{
	global $hidden_fields;

	if (is_array($hidden_fields))
		echo implode('', $hidden_fields);
	$hidden_fields = array();
}
//---------------------------------------------------------------------------------

function end_form($breaks=0)
{
	global $Ajax, $form_nested, $hidden_fields;

	if ($form_nested-- > 0) return;

	$_SESSION['csrf_token'] =random_id();// hash('sha256', uniqid(mt_rand(), true));
	if ($breaks)
		br($breaks);
	hidden('_focus');
	hidden('_modified', get_post('_modified', 0));
	hidden('_confirmed'); // helper for final form confirmation
	hidden('_token', $_SESSION['csrf_token']);

	output_hidden();
	echo "</form>\n";
	$Ajax->activate('_token');
	$Ajax->activate('_confirmed');
}

function check_csrf_token()
{
	if ($_SESSION['csrf_token'] != @$_POST['_token'])
	{
		display_error(_("Request from outside of this page is forbidden."));
		error_log(_("CSRF attack detected from: ").@$_SERVER['HTTP_HOST'].' ('.@$_SERVER['HTTP_REFERER'].')');
		return false;
	}
	return true;
}

function start_table($class=false, $extra="", $padding='2', $spacing='0')
{
	if(user_theme()=="premium")
	{
	   echo"<section style=''>";
	   echo "<center style='width:;margin-top:5px;padding-bottom:5px;margin-left :4px;'><div class='table-responsive'><table";
	   if ($class == TABLESTYLE_NOBORDER)
		  echo " class='C1'";
	   elseif ($class == TABLESTYLE2)
		  echo " class='C2 table'";
	   elseif ($class == TABLESTYLE)
		  echo " class='table'";
	   if ($extra != "")
		  echo "";
	   echo " cellpadding=$padding  cellspacing=$spacing>\n";
	}
	else
	{
		echo "<center><table";
		if ($class == TABLESTYLE_NOBORDER)
			echo " class='tablestyle_noborder'";
		elseif ($class == TABLESTYLE2)
			echo " class='tablestyle2'";
		elseif ($class == TABLESTYLE)
			echo " class='tablestyle'";
		if ($extra != "")
			echo " $extra";
		echo " cellpadding='$padding' cellspacing='$spacing'>\n";
	}

}
function start_table1($class=false, $extra="", $padding='2', $spacing='0')
{
	if(user_theme()=="premium")
	{
	   echo"<section style=''>";
	   echo "<center style='width:;margin-top:5px;padding-bottom:5px;margin-left :4px; float: right;'><div class=''><table";
	   if ($class == TABLESTYLE_NOBORDER)
		  echo " class='C1'";
	   elseif ($class == TABLESTYLE2)
		  echo " class='C2 table'";
	   elseif ($class == TABLESTYLE)
		  echo " class='table'";
	   if ($extra != "")
		  echo "";
	   echo " cellpadding=$padding  cellspacing=$spacing>\n";
	}
	else
	{
		echo "<center><table";
		if ($class == TABLESTYLE_NOBORDER)
			echo " class='tablestyle_noborder'";
		elseif ($class == TABLESTYLE2)
			echo " class='tablestyle2'";
		elseif ($class == TABLESTYLE)
			echo " class='tablestyle'";
		if ($extra != "")
			echo " $extra";
		echo " cellpadding='$padding' cellspacing='$spacing'>\n";
	}

}
function start_table_custom($class=false, $extra="", $padding='2', $spacing='0')
{
	if(user_theme()=="premium")
	{
	   echo"<section style=''>";
	   echo "<center style='width:900px; margin-top:5px;padding-bottom:5px;'><div class='table-responsive'><table";
	   if ($class == TABLESTYLE_NOBORDER)
		  echo " class='C1'";
	   elseif ($class == TABLESTYLE2)
		  echo " class='C2 table'";
	   elseif ($class == TABLESTYLE)
		  echo " class='table'";
	   if ($extra != "")
		  echo "";
	   echo " cellpadding=$padding  cellspacing=$spacing>\n";
	}
	else
	{
		echo "<center><table";
		if ($class == TABLESTYLE_NOBORDER)
			echo " class='tablestyle_noborder'";
		elseif ($class == TABLESTYLE2)
			echo " class='tablestyle2'";
		elseif ($class == TABLESTYLE)
			echo " class='tablestyle'";
		if ($extra != "")
			echo " $extra";
		echo " cellpadding='$padding' cellspacing='$spacing'>\n";
	}

}
function start_table_custom1($class=false, $extra="", $padding='2', $spacing='0')
{
	if(user_theme()=="premium")
	{
	   echo"<section style=''>";
	   echo "<center style='width:590px; margin-top:-395px; float: right;margin-right: 20px;'><div class='table-responsive'><table";
	   if ($class == TABLESTYLE_NOBORDER)
		  echo " class='C1'";
	   elseif ($class == TABLESTYLE2)
		  echo " class='C2 table'";
	   elseif ($class == TABLESTYLE)
		  echo " class='table'";
	   if ($extra != "")
		  echo "";
	   echo " cellpadding=$padding  cellspacing=$spacing>\n";
	}
	else
	{
		echo "<center><table";
		if ($class == TABLESTYLE_NOBORDER)
			echo " class='tablestyle_noborder'";
		elseif ($class == TABLESTYLE2)
			echo " class='tablestyle2'";
		elseif ($class == TABLESTYLE)
			echo " class='tablestyle'";
		if ($extra != "")
			echo " $extra";
		echo " cellpadding='$padding' cellspacing='$spacing'>\n";
	}

}
function start_table_new($class=false, $extra="", $padding='2', $spacing='0')
{
    echo"<section style=''>";
    echo "<center style='width:;margin-top:5px;padding-bottom:5px;margin-left :4px;'><div class='box-body table-responsive no-padding'><table ";

    echo "<center><table";
    if ($class == TABLESTYLE_NOBORDER)
        echo " class='tablestyle_noborder'";
    elseif ($class == TABLESTYLE2)
        echo " class='tablestyle2' ";
    elseif ($class == TABLESTYLE)
        echo " class='tablestyle' ";

    if ($extra != "")
        echo " $extra";
    echo " cellpadding=$padding cellspacing=$spacing>\n";
}
function end_table($breaks=0)
{
	echo "</table></center>\n";
	output_hidden();
	if ($breaks)
		br($breaks);
}

function start_outer_table($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
	if ($br)
		br();
	start_table($class, $extra, $padding, $spacing);
	echo "<tr valign=top><td>\n"; // outer table
}
function start_outer_table1($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
	if ($br)
		br();
	start_table1($class, $extra, $padding, $spacing);
	echo "<tr valign=top><td>\n"; // outer table
}

function table_section($number=1, $width=false)
{
	if ($number > 1)
	{
		echo "</table>\n";
		output_hidden();
		$width = ($width ? "width='$width'" : "");
		echo "</td><td style='border-left:1px solid #cccccc;' $width>\n"; // outer table
	}
	echo "<table class='tablestyle_inner table'>\n";
}	

function end_outer_table($breaks=0, $close_table=true)
{
	if ($close_table)
	{
		echo "</table>\n";
		output_hidden();
	}
	echo "</td></tr>\n";
	end_table($breaks);
}
//
//  outer table spacer
//
function vertical_space($params='')
{
	echo "</td></tr><tr><td valign=center $params>";
}

function meta_forward($forward_to, $params="", $timeout=0, $return=false)
{
    global $Ajax;
	echo "<meta http-equiv='Refresh' content='".$timeout."; url=$forward_to?$params'>\n";
	echo "<center><br>" . _("You should automatically be forwarded.");
	echo " " . _("If this does not happen") . " " . "<a href='$forward_to?$params'>" . _("click here") . "</a> " . _("to continue") . ".<br><br></center>\n";
	if ($params !='') $params = '?'.$params;
	$Ajax->redirect($forward_to.$params);
	//exit;
	if (!$return) exit;
}

//-----------------------------------------------------------------------------------
// Find and replace hotkey marker.
// if $clean == true marker is removed and clean label is returned 
// (for use in wiki help system), otherwise result is array of label 
// with underlined hotkey letter and access property string.
//
function access_string($label, $clean=false)
{
	$access = '';
	$slices = array();

	if (preg_match('/(.*)&([a-zA-Z0-9])(.*)/', $label, $slices))	
	{
		$label = $clean ? $slices[1].$slices[2].$slices[3] :
			$slices[1].'<u>'.$slices[2].'</u>'.$slices[3];
		$access = " accesskey='".strtoupper($slices[2])."'";
	}
	
	$label = str_replace( '&&', '&', $label);

	return $clean ? $label : array($label, $access);
}

function hyperlink_back($center=true, $no_menu=true, $type_no=0, $trans_no=0, $final=false)
{
	global $path_to_root;

	if ($center)
		echo "<center>";
	$id = 0;	
	if ($no_menu && $trans_no != 0)
	{
		include_once($path_to_root."/admin/db/attachments_db.inc");
		$id = has_attachment($type_no, $trans_no);
		//$attach = get_attachment_string($type_no, $trans_no);
    	//echo $attach; //ansar 26-8-17
	}
	$width = ($id != 0 ? "30%" : "20%");	
	start_table(false, "width='$width'");
	start_row();
	if ($no_menu)
	{
        if ($id != 0)
            echo "<td align=center><a class='btn btn-info' href='$path_to_root/admin/attachments.php?vw=$id' target='blanc_'>"._("View Attachment")."</a></td>\n";
		echo "<td align=center><a class='btn btn-info' href='javascript:window.print();'>"._("Print")."</a></td>\n";
	}	
	    echo "<td align=center><a Class='btn btn-info' href='javascript:goBack(".($final ? '-2' : '').");'>".($no_menu ? _("Close") : _("Back"))."</a></td>\n";


	end_row();
	end_table();
	if ($center)
		echo "</center>";
	echo "<br>";
}

function hyperlink_no_params($target, $label, $center=true)
{
	$id = default_focus();
	$pars = access_string($label);
	if ($target == '')
		$target = $_SERVER['PHP_SELF'];
	if ($center)
		echo "<br><center>";
	echo "<a href='$target' id='$id' $pars[1]>$pars[0]</a>\n";
	if ($center)
		echo "</center>";
}

function hyperlink_no_params_td($target, $label)
{
	echo "<td>";
	hyperlink_no_params($target, $label);
	echo "</td>\n";
}

function viewer_link($label, $url='', $class='', $id='',  $icon=null)
{
	global $path_to_root;
	
	if ($class != '')
		$class = " class='$class'";

	if ($id != '')
		$class = " id='$id'";

	if ($url != "")
	{
		$pars = access_string($label);
		if (user_graphic_links() && $icon)
			$pars[0] = set_icon($icon, $pars[0]);
-		$preview_str = "<a target='_blank' $class $id href='$path_to_root/$url' onclick=\"javascript:openWindow(this.href,this.target); return false;\"$pars[1]>$pars[0]</a>";
	}
	else
		$preview_str = $label;
 return $preview_str;
}

function menu_link($url, $label, $id=null)
{
	global $path_to_root;

	$id = default_focus($id);
	$pars = access_string($label);

	if ($url[0] != '/')
		$url = '/'.$url;
	$url = $path_to_root.$url;

	return "<a href='$url' class='menu_option' id='$id' $pars[1]>$pars[0]</a>";
}
//ansar 26-08-17
function submenu_option($title, $url, $id=null)
{
    display_note( menu_link($url, $title, $id), 0, 1);
}
function submenu_view($title, $type, $number, $id=null)
{
	display_note(get_trans_view_str($type, $number, $title, false, 'viewlink', $id), 0, 1);
}

function submenu_print($title, $type, $number, $id=null, $email=0, $extra=0)
{
	display_note(print_document_link($number, $title, true, $type, false, 'printlink', $id, $email, $extra), 0, 1);
}
//-----------------------------------------------------------------------------------

function hyperlink_params($target, $label, $params, $center=true)
{
	$id = default_focus();
	
	$pars = access_string($label);
	if ($target == '')
		$target = $_SERVER['PHP_SELF'];
	if ($center)
		echo "<br><center>";
	echo "<a id='$id' href='$target?$params'$pars[1]>$pars[0]</a>\n";
	if ($center)
		echo "</center>";
}

function hyperlink_params_td($target, $label, $params)
{
	echo "<td>";
	hyperlink_params($target, $label, $params, false);
	echo "</td>\n";
}

//-----------------------------------------------------------------------------------

function hyperlink_params_separate($target, $label, $params, $center=false)
{
	$id = default_focus();

	$pars = access_string($label);
	if ($center)
		echo "<br><center>";
	echo "<a target='_blank' id='$id' href='$target?$params' $pars[1]>$pars[0]</a>\n";
	if ($center)
		echo "</center>";
}

function hyperlink_params_separate_td($target, $label, $params)
{
	echo "<td>";
	hyperlink_params_separate($target, $label, $params);
	echo "</td>\n";
}

//--------------------------------------------------------------------------------------------------
//ansar 26-08-17
function alt_table_row_color(&$k, $extra_class=null)
{
    $classes = $extra_class ? array($extra_class) : array();
    if ($k == 1)
    {
        array_push($classes, 'oddrow');
        $k = 0;
    }
    else
    {
        array_push($classes, 'evenrow');
        $k++;
    }
    echo "<tr class='".implode(' ', $classes)."'>\n";
}

function table_section_title($msg, $colspan=7)
{
	echo "<tr><td colspan=$colspan class='tableheader'>$msg</td></tr>\n";
}
function table_section_title2($msg, $colspan=1)
{
	echo "<tr><td  colspan=$colspan class='tableheader'>$msg</td></tr>\n";
}

function table_section_title1($msg, $colspan=4)
{
	$str = "&nbsp;&nbsp;";
	echo "<tr><td  colspan=$colspan class='tableheader'>$msg,$str,$msg</td></tr>\n";
}


function table_header($labels, $params='')
{
	start_row();
	foreach ($labels as $label)
		labelheader_cell($label, $params);
	end_row();
}
function table_header_new($labels, $params='')
{
    start_row();
    foreach ($labels as $label)
        labelheader_cell_new($label, $params);
    end_row();
}
//-----------------------------------------------------------------------------------

function start_row($param="")
{
	if ($param != "")
		echo "<tr $param>\n";
	else
		echo "<tr>\n";
}

function end_row()
{
	echo "</tr>\n";
}

function br($num=1)
{
	for ($i = 0; $i < $num; $i++)
		echo "<br>";
}

$ajax_divs = array();

function div_start($id='', $trigger=null, $non_ajax=false)
{
    global $ajax_divs;

	if ($non_ajax) { // div for non-ajax elements
   		array_push($ajax_divs, array($id, null));
   		echo "<div style='display:none;' class='js_only' ".($id !='' ? "id='$id'" : '').">";
	} else { // ajax ready div
   		array_push($ajax_divs, array($id, $trigger===null ? $id : $trigger));
   		echo "<div  style='z-index:1;' ". ($id !='' ? "id='$id'" : '').">";
   		ob_start();
	}
}

function
div_start_new($id='', $trigger=null, $non_ajax=false)
{
    global $ajax_divs;

    if ($non_ajax) { // div for non-ajax elements
        array_push($ajax_divs, array($id, null));
        echo "<div style='display:none;' class='js_only' ".($id !='' ? "id='$id'" : '').">";
    } else { // ajax ready div
        array_push($ajax_divs, array($id, $trigger===null ? $id : $trigger));
        echo "<div  style='width:940px;height:400px;overflow-y:auto;float:left;margin-left:-500px;' ". ($id !='' ? "id='$id'" : '').">";
        ob_start();
    }
}

function div_end()
{
    global $ajax_divs, $Ajax;

	output_hidden();
    if (count($ajax_divs))
    {
		$div = array_pop($ajax_divs);
		if ($div[1] !== null)
			$Ajax->addUpdate($div[1], $div[0], ob_get_flush());
    }
	echo "</div>";
}

//-----------------------------------------------------------------------------
//	Tabbed area:
// 	$name - prefix for widget internal elements:
//		Nth tab submit name:  {$name}_N
//		div id:	_{$name}_div
//		sel (hidden) name: _{$name}_sel
// $tabs - array of tabs; string: tab title or array(tab_title, enabled_status)

function tabbed_content_start($name, $tabs, $dft='') {
    global $Ajax;

    $selname = '_'.$name.'_sel';
	$div = '_'.$name.'_div';

	$sel = find_submit($name.'_', false);
	if($sel==null)
		$sel = get_post($selname, (string)($dft==='' ? key($tabs) : $dft));

	if ($sel!==@$_POST[$selname])
		$Ajax->activate($name);

	$_POST[$selname] = $sel;

	div_start($name);
	$str = "<ul class='ajaxtabs' rel='$div'>\n";
	foreach($tabs as $tab_no => $tab) {
		
		$acc = access_string(is_array($tab) ? $tab[0] : $tab);
		$disabled = (is_array($tab) && !$tab[1])  ? 'disabled ' : '';
		$str .= ( "<li>"
			."<button type='submit' name='{$name}_".$tab_no
			."' class='".((string)$tab_no===$sel ? 'current':'ajaxbutton')."' $acc[1] $disabled>"
			."<span>$acc[0]</span>"
			."</button>\n"
			."</li>\n" );
	}

	$str .= "</ul>\n";
	$str .= "<div class='spaceBox'></div>\n";
	$str .= "<input type='hidden' name='$selname' value='$sel'>\n";
	$str .= "<div class='contentBox' id='$div'>\n";
	echo $str;
}

function tabbed_content_end() {
	output_hidden();
	echo "</div>"; // content box (don't change to div_end() unless div_start() is used above)
	div_end(); // tabs widget
}

function tab_changed($name)
{
	$to = find_submit("{$name}_", false);
	if (!$to) return null;

	return array('from' => $from = get_post("_{$name}_sel"),
		'to' => $to);
}
/*
	Check whether tab has been just switched on
*/
function tab_opened($name, $tab)
{
	return (get_post('_'.$name.'_sel') != $tab) && (find_submit($name.'_', false) == $tab);
}
/*
	Check whether tab has been just switched off
*/
function tab_closed($name, $tab)
{
	return (get_post('_'.$name.'_sel') == $tab) && (find_submit($name.'_', false) != $tab);
}
/*
	Check whether tab is visible on current page
*/
function tab_visible($name, $tab)
{
	$new = find_submit($name.'_', false);
	return (get_post('_'.$name.'_sel') == $tab && !$new) || $new==$tab;
}

/* Table editor interfaces. Key is editor type
	0 => url of editor page
	1 => hotkey code
	2 => context help
*/
$popup_editors = array(
	'customer' => array('/sales/manage/customers.php?debtor_no=', 
		113,	_("Customers"), 900, 500),
	'branch' => array('/sales/manage/customer_branches.php?SelectedBranch=', 
		114, _("Branches"), 900, 700),
	'supplier' => array('/purchasing/manage/suppliers.php?supplier_id=', 
		113, _("Suppliers"), 900, 700),
	'item' => array('/inventory/manage/items.php?stock_id=', 
		115, _("Items"), 800, 600),
	'fa_item' => array('/inventory/manage/items.php?FixedAsset=1&stock_id=', 
		115, _("Items"), 800, 600)
);
/*
	Bind editors for various selectors.
	$type - type of editor
	$input - name of related input field
	$caller - optional function key code (available values F1-F12: 112-123,
		true: default)
*/
function set_editor($type, $input, $caller=true)
{
	global $path_to_root, $Editors, $popup_editors, $Pagehelp;

	$key = $caller===true ? $popup_editors[$type][1] : $caller;

	$Editors[$key] = array( $path_to_root . $popup_editors[$type][0], $input, 
		$popup_editors[$type][3], $popup_editors[$type][4]);
	
	$help = 'F' . ($key - 111) . ' - ';
	$help .= $popup_editors[$type][2];
	$Pagehelp[] = $help;
}
//------------------------------------------------------------------------------
// Procedures below are now obsolete. Preserved for eventual future use.

/*
	External page call with saving current context.
	$call - url of external page
	$ctx - optional. name of SESSION context object or array of names of POST 
		variables saved on call
*/
function context_call($call, $ctx='')
{
	if (is_array($ctx)) 
	{
		foreach($ctx as $postname)
		{
		 	$context[$postname] = get_post($postname);
		}
	} else 
		$context = isset($_SESSION[$ctx]) ? $_SESSION[$ctx] : null;

	array_unshift($_SESSION['Context'], array('name' => $ctx, 
		'ctx' => $context,
		'caller' => $_SERVER['PHP_SELF'],
		'ret' => array()));
	meta_forward($call);
}
/*
	Restores context after external page call and
	returns array of data passed by external page.
*/
function context_restore()
{
	if ( count($_SESSION['Context'])) {
		if ($_SERVER['PHP_SELF'] == $_SESSION['Context'][0]['caller']) {
			$ctx = array_shift($_SESSION['Context']);
			if ($ctx) {
				if (is_array($ctx['ctx'])) {
					foreach($ctx['ctx'] as $name => $val) 
					{
						$_POST[$name] = $val;
					}
				} else
					if ($ctx['name']!='')
						$_SESSION[$ctx['name']] = $ctx['ctx'];
				return $ctx['ret'];
			}
		}
	}
	return false;
}

/*
	Return to caller page if the page was called from external context.
*/
function context_return($ret)
{
	if ( count($_SESSION['Context'])) {
		$ctx = &$_SESSION['Context'][0];
		$ctx['ret'] = $ret;
		meta_forward( $ctx['caller'] );
	}
}
/*
	Clearing context stack after page cancel.
*/
function context_reset()
{
	$_SESSION['Context'] = array();
}
/*
	Context stack initialization
*/
if (!isset($_SESSION['Context'])) {
		context_reset();
}
/*
	Redirector for selector F4 calls.
	$sel_editors is array of selname=>editor_page
*/
function editor_redirect($sel_editors, $save_fun='') {
	foreach ($sel_editors as $selname=>$editor)
		if (isset($_POST['_'.$selname.'_editor'])) {
			if (function_exists($save_fun))
				$save_fun();
			unset($_POST['_'.$selname.'_editor']);
			context_call($editor, array_keys($_POST));
		}
}
/*
	Return procedure for selector F4 calls
*/
function editor_return($vars, $restore_fun='') {
	if (function_exists($restore_fun))
		$restore_fun();

	if ($ret = context_restore()) {
		foreach ($vars as $postname=>$retname)
			if (isset($ret[$retname])) {
				$_POST[$postname] = $ret[$retname];
				set_focus($postname);
			}
	}
}

function confirm_dialog($submit, $msg) {
	if (find_post($submit)) {
		display_warning($msg);
		br();
		submit_center_first('DialogConfirm', _("Proceed"), '', true);
		submit_center_last('DialogCancel', _("Cancel"), '', 'cancel');
		return 0;
	} else
		return get_post('DialogConfirm', 0);
}
/*
	Confirm dialog to be used optionally in final form checking routine.
	Displays warning conditionally unless it was displayed
*/
function display_confirmation($msg)
{
	global $Ajax;

	if (!get_post('_confirmed'))
	{
		$_POST['_confirmed'] = 1;
		display_warning($msg);
		return false;
	} else
		return true;
}
/*
	Block menu/shortcut links during transaction procesing.
*/
function page_processing($msg = false)
{
	global $Ajax;

	if ($msg === true)
		$msg = _("Entered data has not been saved yet.\nDo you want to abandon changes?");

	$js = "_validate._processing=" . (
		$msg ? '\''.strtr($msg, array("\n"=>'\\n')) . '\';' : 'null;');
	if (in_ajax()) {
		$Ajax->addScript(true, $js);
	} else
		add_js_source($js);
}

function page_modified($status = true)
{
	global $Ajax;

	$js = "_validate._modified=" . ($status ? 1:0).';';
	if (in_ajax()) {
		$Ajax->addScript(true, $js);
	} else
		add_js_source($js);
}
function start_outer_table_header($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
    if ($br)
        br();
    start_table_header($class, $extra, $padding, $spacing);
    echo "<tr valign=top><td>\n"; // outer table
}
function start_table_header($class=false, $extra="", $padding='2', $spacing='0')
{
    if(user_theme()=="premium")
    {
        echo"<section style=''>";
        echo "<center style='width:;margin-top:5px;padding-bottom:5px;margin-left :4px;border:2px solid white;'><div class='table-responsive' style='width: 450px;'><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='C1'";
        elseif ($class == TABLESTYLE2)
            echo " class='C2 table'";
        //elseif ($class == TABLE_ITEMS)
        //   echo " class='table'";
        // elseif ($class == STOCK_MOVEMENTS)
        //    echo " class='table'";
        elseif ($class == TABLESTYLE)
            echo " class='table'";
        //echo " class='table'";

        if ($extra != "")
            echo "";
        echo " cellpadding=$padding  cellspacing=$spacing>\n";
    }
    else

    {
        echo "<center><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='tablestyle_noborder'";
        elseif ($class == TABLESTYLE2)
            echo " class='tablestyle2'";
        elseif ($class == TABLESTYLE)
            echo " class='tablestyle'";
        if ($extra != "")
            echo " $extra";
        echo " cellpadding='$padding' cellspacing='$spacing'>\n";
    }

}
function start_outer_table_footer($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
    if ($br)
        br();
    start_table_footer($class, $extra, $padding, $spacing);
    echo "<tr valign=top><td>\n"; // outer table
}

//------------------------------------------------------------------------
function start_table_footer($class=false, $extra="", $padding='2', $spacing='0')
{
    if(user_theme()=="premium")
    {
        echo"<section style='display: none;'>";
        echo "<center style='width:;margin-top:5px;padding-bottom:5px;margin-left :4px;'><div class='table-responsive'><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='C1'";
        elseif ($class == TABLESTYLE2)
            echo " class='C2 table'";
        //elseif ($class == TABLE_ITEMS)
        //   echo " class='table'";
        // elseif ($class == STOCK_MOVEMENTS)
        //    echo " class='table'";
        elseif ($class == TABLESTYLE)
            echo " class='table'";
        //echo " class='table'";

        if ($extra != "")
            echo "";
        echo " cellpadding=$padding  cellspacing=$spacing>\n";
    }
    else

    {
        echo "<center><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='tablestyle_noborder'";
        elseif ($class == TABLESTYLE2)
            echo " class='tablestyle2'";
        elseif ($class == TABLESTYLE)
            echo " class='tablestyle'";
        if ($extra != "")
            echo " $extra";
        echo " cellpadding='$padding' cellspacing='$spacing'>\n";
    }

}

function start_table_pos_running_orders($class=false, $extra="", $padding='2', $spacing='0')
{
    if(user_theme()=="premium")
    {
        echo"<section style=''>";
        echo "<center style='width:900px;margin-top:-50px;padding-bottom:5px;margin-left :4px; float: left;'><div class='table-responsive' style='height: 400px; '><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='C1'";
        elseif ($class == TABLESTYLE2)
            echo " class='C2 table'";
        //elseif ($class == TABLE_ITEMS)
        //   echo " class='table'";
        // elseif ($class == STOCK_MOVEMENTS)
        //    echo " class='table'";
        elseif ($class == TABLESTYLE)
            echo " class='table'";
        //echo " class='table'";

        if ($extra != "")
            echo "";
        echo " cellpadding=$padding  cellspacing=$spacing>\n";
    }
    else

    {
        echo "<center><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='tablestyle_noborder'";
        elseif ($class == TABLESTYLE2)
            echo " class='tablestyle2'";
        elseif ($class == TABLESTYLE)
            echo " class='tablestyle'";
        if ($extra != "")
            echo " $extra";
        echo " cellpadding='$padding' cellspacing='$spacing'>\n";
    }

}
function start_outer_table_pos2($class=false, $extra="", $padding='2', $spacing='0', $br=false)
{
    if ($br)
        br();
    start_table_pos2($class, $extra, $padding, $spacing);
    echo "<tr valign=top style=\"margin: 0; display: inline-block; float: left;\"><td>\n"; // outer table
}

function start_table_pos2($class=false, $extra="", $padding='2', $spacing='0')
{
    if(user_theme()=="premium")
    {
        echo"<section style=''>";
        echo "<center style='position: relative;width:700px;margin-top:440px;padding-bottom:5px;margin-left :4px;'><div class=' '><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='C1'";
        elseif ($class == TABLESTYLE2)
            echo " class='C2 table'";
        //elseif ($class == TABLE_ITEMS)
        //   echo " class='table'";
        // elseif ($class == STOCK_MOVEMENTS)
        //    echo " class='table'";
        elseif ($class == TABLESTYLE)
            echo " class='table'";
        //echo " class='table'";

        if ($extra != "")
            echo "";
        echo " cellpadding=$padding  cellspacing=$spacing>\n";
    }
    else

    {
        echo "<center><table";
        if ($class == TABLESTYLE_NOBORDER)
            echo " class='tablestyle_noborder'";
        elseif ($class == TABLESTYLE2)
            echo " class='tablestyle2'";
        elseif ($class == TABLESTYLE)
            echo " class='tablestyle'";
        if ($extra != "")
            echo " $extra";
        echo " cellpadding='$padding' cellspacing='$spacing'>\n";
    }

}

function combo_pos_input($name, $selected_id, $sql, $valfield, $namefield,
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
        $selector = "<select  style='width:120px;' autocomplete='off' ".($multi ? "multiple" : '')
            . ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
            . "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
            . $opts['sel_hint']."' $rel>".$selector."</select>\n";
    if ($by_id && ($search_box != false || $opts['editable']) ) {
        // on first display show selector list
        if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
            $selector = "<input    type='hidden' name='$name' value='".$_POST[$name]."'>"
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


function combo_pos_input_size($name, $selected_id, $sql, $valfield, $namefield,
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
        $selector = "<select  style='width:60px;' autocomplete='off' ".($multi ? "multiple" : '')
            . ($opts['height']!==false ? ' size="'.$opts['height'].'"' : '')
            . "$disabled name='$name".($multi ? '[]':'')."' class='$class' title='"
            . $opts['sel_hint']."' $rel>".$selector."</select>\n";
    if ($by_id && ($search_box != false || $opts['editable']) ) {
        // on first display show selector list
        if (isset($_POST[$search_box]) && $opts['editable'] && $edit) {
            $selector = "<input    type='hidden' name='$name' value='".$_POST[$name]."'>"
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