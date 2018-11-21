<?php

$page_security = 'SA_SALESAREA';
$path_to_root = "../..";
include($path_to_root . "/includes/session.inc");

page(_($help_context = "Sales Areas"));

include($path_to_root . "/includes/ui.inc");

simple_page_mode(true);

//--iqra
//$_GET['id']= $_POST['id'];

$id=$_GET['id'];

//if(isset($_GET['id']))
//    $selected_id = $id;
//display_error($id);
//display_error($id);


function get_date($id)
{
    $sql = "select date FROM ".TB_PREF."query  WHERE id=".db_escape($id);
    $result = db_query($sql, "could not get sales type");
    $row = db_fetch_row($result);
    return $row[0];
}

function add_follow_up($date,$days,$time,$id)
{
    $sql = "INSERT INTO ".TB_PREF."follow_ups (date,days,time,query_id) VALUES (
    ".db_escape(date2sql($date)).",
    ".db_escape($days) . ", 
    ".db_escape($time).", ".db_escape($id) . "
    )";
    db_query($sql,"The sales area could not be added");
}

function update_follow_up($selected_id, $date,$days,$time)
{
    $sql = "UPDATE ".TB_PREF."follow_ups SET
     date=".db_escape($date).",
     days=".db_escape($days).",   
       time=".db_escape($time)."

     WHERE id = ".db_escape($selected_id);
    db_query($sql,"The sales area could not be updated");
}

function delete_follow_up($selected_id)
{
    $sql="DELETE FROM ".TB_PREF."follow_ups WHERE id=".db_escape($selected_id);
    db_query($sql,"could not delete sales area");
}

function get_follow_ups($show_inactive,$id)
{
    $sql = "SELECT * FROM ".TB_PREF."follow_ups  WHERE query_id=".db_escape($id);
    if (!$show_inactive) $sql .= " AND !inactive";
    return db_query($sql,"could not get areas");
}

function get_follow_up($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."follow_ups WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get area");
    return db_fetch($result);
}

$getdate=get_date($_POST['id']);

$start = strtotime($getdate);
$end = strtotime($_POST['date']);

$days_between = ceil(abs($end - $start) / 86400);



if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') 
{

	$input_error = 0;

//	if (strlen($_POST['days']) == 0)
//	{
//		$input_error = 1;
//		display_error(_("The area description cannot be empty."));
//		set_focus('days');
//	}

	if ($input_error != 1)
	{
    	if ($selected_id != -1) 
    	{
            update_follow_up($selected_id,$_POST['date'],$days_between,$_POST['time']);
			$note = _('Selected sales area has been updated');
			            meta_forward($_SERVER['PHP_SELF'], "status=112&id=".$_POST['id']."");

    	} 
    	else 
    	{


            add_follow_up($_POST['date'],$days_between,$_POST['time'],$_POST['id']);
			$note = _('New sales area has been added');
			            meta_forward($_SERVER['PHP_SELF'], "status=112&id=".$_POST['id']."");

    	}
    
		display_notification($note);    	
		$Mode = 'RESET';
	}
} 

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

//	if (key_in_foreign_table($selected_id, 'cust_branch', 'area'))
//	{
//		$cancel_delete = 1;
//		display_error(_("Cannot delete this area because customer branches have been created using this area."));
//	}
	if ($cancel_delete == 0) 
	{
        delete_follow_up($selected_id);

		display_notification(_('Selected sales area has been deleted'));
		            meta_forward($_SERVER['PHP_SELF'], "status=112&id=".$_POST['id']."");

	} //end if Delete area
	$Mode = 'RESET';
} 

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	$_POST['show_inactive'] = $sav;
}

//-------------------------------------------------------------------------------------------------

$result = get_follow_ups(check_value('show_inactive'),$_GET['id']);

start_form();
start_table(TABLESTYLE, "width='30%'");

$th = array(_("Date"),_("Time"),_("Days"), "", "");
inactive_control_column($th);

table_header($th);
$k = 0; 

while ($myrow = db_fetch($result)) 
{
	
	alt_table_row_color($k);
		
	label_cell(sql2date($myrow["date"]));
    label_cell($myrow["time"]);
    label_cell($myrow["days"]);

	inactive_control_cell($myrow["id"], $myrow["inactive"], 'follow_ups', 'id');

 	edit_button_cell("Edit".$myrow["id"], _("Edit"));
 	delete_button_cell("Delete".$myrow["id"], _("Delete"));
	end_row();
}
	
inactive_control_row($th);
end_table();
echo '<br>';

//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1) 
{
 	if ($Mode == 'Edit') {
		//editing an existing area
		$myrow = get_follow_up($selected_id);

		$_POST['date']  = $myrow["date"];
        $_POST['time']  = $myrow["time"];

        //$_POST['days']  = $myrow["days"];
	}
	hidden("selected_id", $selected_id);
} 

date_cells(_("Date:"), 'date', '', null, 0, 0, -0);
//text_row_ex(_("Days:"), 'days', 30);
hidden('time',date('h:i:s'));
hidden('id',$_GET['id']);

//display_error(time());
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

end_page();
