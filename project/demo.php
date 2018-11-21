<?php
$path_to_root="..";
$page_security = 'SS_CRM_BASE';

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
page(_($help_context = "Calendar"));

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/admin/db/transactions_db.inc");

include_once($path_to_root . "/project/includes/db/kb_db.inc");


//---
$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(900, 500);
if ($use_date_picker)
    $js .= get_js_date_picker();


if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1)
{
    $row = get_attachment_kb($view_id);
    if ($row['filename'] != "")
    {
        if(in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: ".$type);
            header('Content-Length: '.$row['filesize']);
            //if ($type == 'application/octet-stream')
            //	header('Content-Disposition: attachment; filename='.$row['filename']);
            //else
            header("Content-Disposition: inline");
            echo file_get_contents(company_path(). "/attachments/".$row['unique_name']);
            exit();
        }
    }
}
if (isset($_GET['dl']))
    $download_id = $_GET['dl'];
else
    $download_id = find_submit('download');

if ($download_id != -1)
{
    $row = get_attachment_kb($download_id);
    if ($row['filename'] != "")
    {
        if(in_ajax()) {
            $Ajax->redirect($_SERVER['PHP_SELF'].'?dl='.$download_id);
        } else {
            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: ".$type);
            header('Content-Length: '.$row['filesize']);
            header('Content-Disposition: attachment; filename='.$row['filename']);
            echo file_get_contents(company_path()."/attachments/".$row['unique_name']);
            exit();
        }
    }
}

$js = "";
if ($use_popup_windows)
    $js .= get_js_open_window(800, 500);
//page(_($help_context = "Attach Documents"), false, false, "", $js);

simple_page_mode(true);
$selected_id=get_post('id','');
if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM') {
    $input_error = 0;
    {

    }


}

if ($Mode == 'Delete')
{
    //the link to delete a selected record was clicked instead of the submit button

    // PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'
//
//	if (key_in_foreign_table($selected_id, 'task','status'))
//	{
//		//display_error(_("Cannot delete this status because transactions have been entered."));
//	}
//	else
    {

        delete_kb1($_POST['id']);
        display_notification(_('Selected status data have been deleted'));




    }
    $Mode = 'RESET';

}

function handle_update1($selected_id)
{
    update_kb1($selected_id, $_POST['category'], $_POST['title'], $_POST['text'],
        $_POST['filename'], $_POST['filesize'], $_POST['filetype'],
        $_POST['date'],$_POST['trans_no']);

    //add_task_history($selected_id,$_POST['start_date'], $_POST['end_date'], $_POST['description'], $_POST['customer_id'],$_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'],$_POST['assign_by'], $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'], $_SESSION['wa_current_user']->user);

    display_notification(_("Task record has been Updated."));

}
if (isset($_POST['update']))
{


    //display_error($_POST['id']);
    handle_update1($_POST['id']);



}
function handle_delete($selected_id)
{
    delete_task($selected_id);


    add_task_history($selected_id,$_POST['start_date'], $_POST['end_date'], $_POST['text'], $_POST['customer_id'],$_POST['task_type'], $_POST['call_type'],$_POST['contact_no'],$_POST['other_cust'],$_POST['status'], $_POST['user_id'],$_POST['assign_by'], $_POST['plan'],$_POST['plan1'],0,0, $_POST['remarks'], $_POST['time'], $_SESSION['wa_current_user']->user, 0, 1);


    display_notification(_("Task record has been deleted."));

}

//---------------add-------------------------------------------------------------------------
if (isset($_GET['category'])) // catch up external links
    $_POST['category'] = $_GET['category'];
if (isset($_GET['trans_no']))
    $_POST['trans_no'] = $_GET['trans_no'];

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{
    //if (!transaction_exists($_POST['category'], $_POST['title'], $_POST['description'], $_POST['date']));
    //display_error(_("Selected transaction does not exists."));
//	elseif ($Mode == 'ADD_ITEM' && (!isset($_FILES['filename']) || $_FILES['filename']['size'] == 0))
    //	display_error(_("Select attachment file."));
    //else {
    //$content = base64_encode(file_get_contents($_FILES['filename']['tmp_name']));
    $tmpname = $_FILES['filename']['tmp_name'];

    $dir =  company_path()."/attachments";
    if (!file_exists($dir))
    {
        mkdir ($dir,0777);
        $index_file = "<?php\nheader(\"Location: ../index.php\");\n?>";
        $fp = fopen($dir."/index.php", "w");
        fwrite($fp, $index_file);
        fclose($fp);
    }

    $filename = basename($_FILES['filename']['name']);
    $filesize = $_FILES['filename']['size'];
    $filetype = $_FILES['filename']['type'];

    // file name compatible with POSIX
    // protect against directory traversal
    /*if ($Mode == 'UPDATE_ITEM')
    {
        $unique_name = preg_replace('/[^a-zA-Z0-9.\-_]/', '', $_POST['unique_name']);
        if ($filename && file_exists($dir."/".$unique_name))
            unlink($dir."/".$unique_name);
    }
    else
        $unique_name = uniqid('');*/

    //save the file
    move_uploaded_file($tmpname, $dir."/".$unique_name);

    if ($Mode == 'ADD_ITEM')
    {
        add_kb1($_POST['category'], $_POST['title'], $_POST['text'], $_POST['trans_no'], date2sql($_POST['date']),
            $filename, $filesize, $filetype);

        display_notification(_("Attachment has been inserted."));
    }
    else
    {
        update_kb1($selected_id,$_POST['category'], $_POST['title'], $_POST['text'],
            $_POST['filename'], $filesize, $filetype, sql2date($_POST['date']),$_POST['trans_no']);

        display_notification(_("Attachment has been updated."));
    }
}
refresh_pager('trans_tbl');
$Ajax->activate('_page_body');
$Mode = 'RESET';
//}

if ($Mode == 'Delete')
{
    $row = get_attachment_kb($selected_id);
    $dir =  company_path()."/attachments";
    if (file_exists($dir."/".$row['unique_name']))
        unlink($dir."/".$row['unique_name']);
    delete_attachment($selected_id);
    display_notification(_("Attachment has been deleted."));
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    unset($_POST['trans_no']);
//	unset($_POST['text']);
    $selected_id = -1;
}
function viewing_controls()
{
    global $selected_id;

    start_table(TABLESTYLE_NOBORDER);

    start_row();
    //systypes_list_cells(_("Type:"), 'filterType', null, true);

    if (list_updated('filterType'))
        $selected_id = -1;;

    end_row();
    end_table(1);

}

function trans_view($trans)
{
    return get_trans_view_str($trans["type_no"], $trans["trans_no"]);
}

function edit_link($row)
{
    return button('Edit'.$row["id"], _("Edit"), _("Edit"), ICON_EDIT);
}

function view_link($row)
{
    return button('view'.$row["id"], _("View"), _("View"), ICON_VIEW);
}

function download_link($row)
{
    return button('download'.$row["id"], _("Download"), _("Download"), ICON_DOWN);
}

function delete_link($row)
{
    return button('Delete'.$row["id"], _("Delete"), _("Delete"), ICON_DELETE);
}

//--add and update
if (isset($_POST['submit']))
{
    //initialise no input errors assumed initially before we test
    $input_error = 0;
//display_error($_POST['text']);
    if (strlen($_POST['access_level']) == 0)
    {
        $input_error = 1;
        display_error(_("The Form Name Can not be empty."));
        set_focus('access_level');
    }
    if ($input_error != 1) {
//        preg_replace("/[\<]p[\>][\s]+&nbsp;[\<][\/]p[\>]/" , " " , $pre_comment);

        /*Selected group is null cos no item selected on first time round so must be adding a record must be submitting new entries in the new Sales-person form */
        add_alert_demo($_POST['access_level'], $_POST['email_title'], $_POST['text']);
        $note = _('New field has been added');

    }
    display_notification(_('Record added successfully.!!'));
}

//---
start_form();
start_table(TABLESTYLE2);
//$alert_id =  $_GET['alert_id'];
//hidden('alert_id',$alert_id);

//security_roles_list_row(_("Access Level:"), 'access_level', null);
//text_row_ex(_("Email Title:"), 'email_title', 30);


if($_GET['id'] )
{
    //if ($Mode == 'Edit')
    {
        $selected_id=$_GET['id'];;

        $row = get_kb1($id);
        $_POST['category'] = $row['category'];
        $_POST['title'] = $row['title'];
        $_POST['text'] = $row['text'];
        hidden('trans_no', $row['trans_no']);
        $_POST['date'] = $row["date"];
        $_POST['filename'] = $row['filename'];
        //label_row(_("Transaction #"), $row['trans_no']);
    }
    hidden('selected_id', $selected_id);
    hidden('id', $id);
}


date_row(_("Date"), 'date');
text_row(_("Title").':', 'title', 25);



//text_row_ex(_("Description").':', 'description', 40);

file_row(_("Attached File") . ":", 'filename', 'filename');
category_list_cells(_("Category: "), 'category', null, _("Select a Category"), false, false, !@$_GET['popup']);

end_table(1);
?>

<!DOCTYPE HTML>
<html>
	<head>
     <script src="//cdn.ckeditor.com/4.7.0/full/ckeditor.js"></script></head>

	</head>
	<body>
<!--CKEDITOR -->
<!--    <form action="--><?php //echo $_SERVER['PHP_SELF']; ?><!--" method="post">-->
        <textarea name="text"   id="text"><?php

            ?>
</textarea>


        <script>
//            CKEDITOR.replace( 'text' );

            CKEDITOR.replace( 'text', {
                language: 'en',
                uiColor: '#5DADE2',

            });
        </script>




	</body>

</html>;

<?php
//echo'  <input type="submit"  value="Submit">';




if($id==0 || $_GET['Type'] =='cloning')
{

    submit_add_or_update_center(-1, '', 'both');
}

else
{
    start_table(TABLESTYLE2);
    div_start(controls);
    submit_center_last('update', _("Update"), '', '', true);
    submit_center_last('Delete', _("Delete"), '', '', true);
    //submit_center_last('delete', _("Delete"), '', '', true);
//	delete_button_cell("Delete", _("Delete"), _("Delete"));
    div_end();

    end_table(1);
}

end_form();
end_page();

?>
