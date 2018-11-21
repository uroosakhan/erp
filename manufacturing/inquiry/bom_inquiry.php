<?php

$page_security = 'SA_WORKORDERCOST';
$path_to_root = "../..";
include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");


include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/banking.inc");
include_once($path_to_root . "/includes/data_checks.inc");


$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(900, 500);
if (user_use_date_picker())
    $js .= get_js_date_picker();
    page(_($help_context = "Bill Of Material Inquiry"));

// page(_($help_context = "Customer Transactions"), isset($_GET['customer_id']), false, "", $js);

//------------------------------------------------------------------------------------------------



function get_work_center_name($id)
{

    $sql = "SELECT name FROM ".TB_PREF."workcentres WHERE id=".db_escape($id['workcentre_added'])." ";

    $result = db_query($sql, "could not get work center");
    $row = db_fetch_row($result);
    return $row[0];


}

function get_descriptions_name($id)
{

    $sql = "SELECT description FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($id['parent'])." ";

    $result = db_query($sql, "could not get work center");
    $row = db_fetch_row($result);
    return $row[0];
}

function get_units_name($id)
{

    $sql = "SELECT units FROM ".TB_PREF."stock_master WHERE stock_id=".db_escape($id['parent'])." ";

    $result = db_query($sql, "could not get work center");
    $row = db_fetch_row($result);
    return $row[0];


}

function get_locations_name($id)
{

    $sql = "SELECT location_name FROM ".TB_PREF."locations WHERE loc_code=".db_escape($id['loc_code'])." ";

    $result = db_query($sql, "could not get work center");
    $row = db_fetch_row($result);
    return $row[0];


}


start_form();

start_table(TABLESTYLE_NOBORDER);





start_row();



stock_manufactured_items_list_row(_("Select a manufacturable item:"), 'items', null, true, true);



submit_cells('RefreshInquiry', _("Search"),'',_('Refresh Inquiry'), 'default');

end_row();

end_table();


echo '&nbsp;<center><a href="/manufacturing/manage/bom_edit.php?" target="_blank"><input type="button" value="+ADD BOM"></a>';

//------------------------------------------------------------------------------------------------

//------------------------------------------------------------------------------------------------


if(get_post('RefreshInquiry'))
{
    $Ajax->activate('totals_tbl');
}
//------------------------------------------------------------------------------------------------


function trans_view($trans)
{
    return get_trans_view_str($trans["type"], $trans["trans_no"]);
}

function due_date($row)
{
    return	$row["type"] == ST_SALESINVOICE	? $row["due_date"] : '';
}


function edit_link($row)
{
    

    return trans_editor_link(ST_WORKORDER, $row['parent']);
}


function get_sql_for_bom_inquiry
($items)
{

    $sql = "SELECT
   parent,'',loc_code,workcentre_added,SUM(quantity)as qty,id
		";

    $sql .= "
		FROM ".TB_PREF."bom

		WHERE id!=0
		AND parent !=''";


    if ($items != '')
        $sql .= " AND  parent = ".db_escape($items);


    $sql .= " GROUP BY parent ";

    return $sql;
}
//------------------------------------------------------------------------------


$sql = get_sql_for_bom_inquiry( get_post('items'));

//------------------------------------------------------------------------------------------------

    $cols = array(
        _("Code"),
        _("Description")=>array('align' => 'left', 'fun' ,'fun'=> 'get_descriptions_name'),
        _("Location")=>array('fun' => 'get_locations_name'),

        _("Work Centre")=>array('fun' => 'get_work_center_name') ,
        _("Quantity "),
        _("Units.")=>array('fun'=>'get_units_name') ,
       
       
       );//ansar 26-08-2017
  

    array_append($cols, array(
     
        
        array('insert' => true, 'fun' => 'edit_link'),
      
    ));





$table =& new_db_pager('bom_tbl', $sql, $cols);


$table->width = "80%";

display_db_pager_total_amount($table);

end_form();
end_page();