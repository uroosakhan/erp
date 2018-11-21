<?php
include_once($path_to_root . "/includes/date_functions.inc");

include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");

//----------------------------------------------------------------------------
// helper functions for script execution control
//
function processing_start()
{
	page_processing(false);
    processing_end();
    $_SESSION['Processing'] = $_SERVER['PHP_SELF'];
}

function processing_end()
{
	page_processing(true);
    unset($_SESSION['Processing']);
    if ( isset($_SESSION['Items']) ) {
		unset($_SESSION['Items']->line_items);
		unset($_SESSION['Items']);
    }
}

function processing_active()
{
    return (isset($_SESSION['Processing']) && $_SESSION['Processing']==$_SERVER['PHP_SELF']);
}
/*
	Check if the cart was not destroyed during opening the edition page in
	another browser tab.
*/
function check_edit_conflicts($cartname='Items')
{
	global $Ajax, $no_check_edit_conflicts;

	if ((!isset($no_check_edit_conflicts) || $no_check_edit_conflicts==0) && get_post('cart_id') && $_POST['cart_id'] != $_SESSION[$cartname]->cart_id) {
		display_error(_('This edit session has been abandoned by opening sales document in another browser tab. You cannot edit more than one sales document at once.'));
		$Ajax->activate('_page_body');
		display_footer_exit();
	}
}

function find_submit_new($prefix, $numeric=true)
{

    foreach($_POST as $postkey=>$postval )
    {
        if (strpos($postkey, $prefix) === 0)
        {
            $id = substr($postkey, strlen($prefix));
            return $numeric ? $id : $id;
        }
    }
    return $numeric ? -1 : null;
}

function button_new($name, $value, $title=false, $icon=false,  $aspect='')
{
    // php silently changes dots,spaces,'[' and characters 128-159
    // to underscore in POST names, to maintain compatibility with register_globals
    $rel = '';
    if ($aspect == 'selector') {
        $rel = " rel='$value'";
        $value = _("Select");
    }
    if (user_graphic_links() && $icon)
    {
        if ($value == _("Delete")) // Helper during implementation
            $icon = ICON_DELETE;
        return "<button type='submit' class='editbutton' name='"
            .htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
            )))
            ."' value='1'" . ($title ? " title='$title'":" title='$value'")
            . ($aspect ? " aspect='$aspect'" : '')
            . $rel
            ." />".set_icon_new($icon)."</button>\n";
    }
    else
        return "<input type='submit' class='editbutton' name='"
            .htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
            )))
            ."' value='$value'"
            .($title ? " title='$title'":'')
            . ($aspect ? " aspect='$aspect'" : '')
            . $rel
            ." />\n";

}
//*****
function set_icon_new($icon, $title=false)
{
    global $path_to_root;
    if (basename($icon) === $icon) // standard icons does not contain path separator
        $icon = "$path_to_root/themes/".user_theme()."/images/btn_add.png";
    return "<img src='$icon' width='90' height='25' border='0'".($title ? " title='$title'" : "")." />\n";
}


//------------------------------------------------------------------------------------
/*function discount_list($name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    $sql = "SELECT discount, discount, inactive FROM ".TB_PREF."discount WHERE inactive = 0";
    return combo_pos_input_size($name, $selected_id, $sql, 'discount','discount',
        array('order' => array('discount'),
            'spec_option' => $spec_opt,
            'select_submit'=> $select_submit,
            'spec_id' => ALL_NUMERIC));
}*/
function discount_list($name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    $sql = "SELECT disc.id, IF(disc.dis_account ,concat(disc.discount, ' - ',chart.account_name ) , disc.discount) as AName, disc.inactive FROM ".TB_PREF."discount disc
            LEFT JOIN ".TB_PREF."chart_master chart ON chart.account_code = disc.dis_account";
    return combo_pos_input($name, $selected_id, $sql, 'disc.id','AName',
        array('order' => array('disc.discount'),
            'spec_option' => $spec_opt,
            'select_submit'=> $select_submit,
            'spec_id' => ALL_NUMERIC));
}

function discount_list_cells($label, $name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo discount_list($name, $selected_id, $spec_opt, $select_submit);
    echo "</td>\n";
}

function discount_list_row($label, $name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    echo "<tr><td class=''>$label</td>";
    discount_list_cells(null, $name, $selected_id, $spec_opt, $select_submit);
    echo "</tr>\n";
}
function discount_list_line($name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    $sql = "SELECT disc.discount, IF(disc.dis_account ,concat(disc.discount, ' - ',chart.account_name ) , disc.discount) as AName, disc.inactive FROM ".TB_PREF."discount disc
            LEFT JOIN ".TB_PREF."chart_master chart ON chart.account_code = disc.dis_account";
    return combo_pos_input($name, $selected_id, $sql, 'disc.id','AName',
        array('order' => array('disc.discount'),
            'spec_option' => $spec_opt,
            'select_submit'=> $select_submit,
            'spec_id' => ALL_NUMERIC));
}

function discount_list_cells_line($label, $name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    if ($label != null)
        echo "<td>$label</td>\n";
    echo "<td>\n";
    echo discount_list_line($name, $selected_id, $spec_opt, $select_submit);
    echo "</td>\n";
}

function discount_list_row_line($label, $name, $selected_id=null, $spec_opt=false, $select_submit=false)
{
    echo "<tr><td class=''>$label</td>";
    discount_list_cells_line(null, $name, $selected_id, $spec_opt, $select_submit);
    echo "</tr>\n";
}
function pager_link_js_confirm($link_text, $url, $icon=false, $msg) //asad 16-10-2015
{
    global $path_to_root;

    if (user_graphic_links() && $icon)
        $link_text = set_icon($icon, $link_text);

    $href = $path_to_root . $url;
    return "<a href='$href' onclick='return confirmUser()'>" . $link_text . "</a>";
}
//--iqra

function button_new_($name, $value, $title=false, $icon=false,  $aspect='',$src)
{

    // php silently changes dots,spaces,'[' and characters 128-159
    // to underscore in POST names, to maintain compatibility with register_globals
    $rel = '';
    if ($aspect == 'selector') {
        $rel = " rel='$value'";
        $value = _("Select");
    }

    if (user_graphic_links() && $icon)
    {

//display_error($src);
        if ($value == _("Delete")) // Helper during implementation
            $icon = ICON_DELETE;
        return "<button style='display:inline-block' type='submit' class='editbutton' name='"
            .htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
            )))
            ."' value='1'" . ($title ? " title='$title'":" title='$value'")
            . ($aspect ? " aspect='$aspect'" : '')
            . $rel
            ." />"."<img style='float:left;margin-right:0.5em' src='$src' alt='image not found' width='85px' height='60px' /></button>\n";

    }

    else
        return "<input type='submit' class='editbutton' name='"
            .htmlentities(strtr($name, array('.'=>'=2E', '='=>'=3D',// ' '=>'=20','['=>'=5B'
            )))
            ."' value='$value'"
            .($title ? " title='$title'":'')
            . ($aspect ? " aspect='$aspect'" : '')
            . $rel
            ." />\n";

}


function submenu_thermal_print($title, $type, $number, $id=null, $email=0, $extra=0)
{
    display_note(print_document_link($number, $title, true, $type, false, 'printlink', $id, $email, $extra), 0, 1);
}


?>