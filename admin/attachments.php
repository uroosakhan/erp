<?php

$path_to_root="..";
$page_security = 'SA_ATTACHDOCUMENT';

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/admin/db/attachments_db.inc");
include_once($path_to_root . "/admin/db/transactions_db.inc");
include_once($path_to_root . "/admin/fileupload.php");

if(isset($_SESSION["AttachmentDebtorNo"])){
    $debtor_id = $_SESSION["AttachmentDebtorNo"];
    unset($_SESSION["AttachmentDebtorNo"]);
    header('Location: ../admin/attachments.php?debtor_no='.$debtor_id, true);
}

if (isset($_GET['vw']))
    $view_id = $_GET['vw'];
else
    $view_id = find_submit('view');
if ($view_id != -1)
{
    $row = get_attachment($view_id);
    if ($row['filename'] != "")
    {
        if(in_ajax()) {
            $Ajax->popup($_SERVER['PHP_SELF'].'?vw='.$view_id);

            $type = ($row['filetype']) ? $row['filetype'] : 'application/octet-stream';
            header("Content-type: ".$type);
            header('Content-Length: '.$row['filesize']);
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

    $deleteservice->files->get($download_id, array('alt' => 'media'));
    file_put_contents("hello.pdf",$file->getBody());

}

$js = "";
if ($SysPrefs->use_popup_windows)
    $js .= get_js_open_window(800, 500);
page(_($help_context = "Attach Documents"), false, false, "", $js);

simple_page_mode(true);
//----------------------------------------------------------------------------------------
if (isset($_GET['filterType'])) // catch up external links
    $_POST['filterType'] = $_GET['filterType'];
if (isset($_GET['trans_no']))
    $_POST['trans_no'] = $_GET['trans_no'];

if ($Mode == 'ADD_ITEM' || $Mode == 'UPDATE_ITEM')
{
    if (!transaction_exists($_POST['filterType'], $_POST['trans_no']))
        display_error(_("Selected transaction does not exists."));
    elseif ($Mode == 'ADD_ITEM' && !isset($_FILES['filename']))
        display_error(_("Select attachment file."));
    elseif ($Mode == 'ADD_ITEM' && ($_FILES['filename']['error'] > 0)) {
        if ($_FILES['filename']['error'] == UPLOAD_ERR_INI_SIZE)
            display_error(_("The file size is over the maximum allowed."));
        else
            display_error(_("Select attachment file."));
    }
    else {
        //$content = base64_encode(file_get_contents($_FILES['filename']['tmp_name']));
        $tmpname = $_FILES['filename']['tmp_name'];

        $dir =  company_path()."/attachments";
        if (!file_exists($dir))
        {
            mkdir ($dir,0777);
            $index_file = "<?php\nheader(\"Location: ../index.php\");\n";
            $fp = fopen($dir."/index.php", "w");
            fwrite($fp, $index_file);
            fclose($fp);
        }

        $filename = basename($_FILES['filename']['name']);
        $filesize = $_FILES['filename']['size'];
        $filetype = $_FILES['filename']['type'];

        // file name compatible with POSIX
        // protect against directory traversal
        if ($Mode == 'UPDATE_ITEM')
        {
            $row = get_attachment($selected_id);
            if ($row['filename'] == "")
                exit();
            $unique_name = $row['unique_name'];
            if ($filename && file_exists($dir."/".$unique_name))
                unlink($dir."/".$unique_name);
        }
        else
            $unique_name = random_id(); //ansar 26-08-17

        //save the file
        move_uploaded_file($tmpname, $dir."/".$unique_name);

        if ($Mode == 'ADD_ITEM')
        {
            display_error("king kong bundy");
            add_attachment($_POST['filterType'], $_POST['trans_no'], $_POST['description'],
                $filename, $unique_name, $filesize, $filetype);
            display_notification(_("Attachment has been inserted."));
            //refresh11('attachments.php');

        }
        else
        {
            update_attachment($selected_id, $_POST['filterType'], $_POST['trans_no'], $_POST['description'],
                $filename, $unique_name, $filesize, $filetype);
            display_notification(_("Attachment has been updated."));
            //refresh11('attachments.php');
        }
    }
    refresh_pager('trans_tbl');
    $Ajax->activate('_page_body');
    $Mode = 'RESET';
}

if ($Mode == 'Delete')
{



    $row = get_attachment($selected_id);
    $fileId = $row['unique_name'];

    delete_attachment($selected_id);
    $deleteservice->files->delete($fileId);





    display_notification(_("Attachment has been deleted."));
    $Mode = 'RESET';
}

if ($Mode == 'RESET')
{
    unset($_POST['trans_no']);
    unset($_POST['description']);
    $selected_id = -1;
}

function viewing_controls()
{
    global $selected_id;

    start_table(TABLESTYLE_NOBORDER);

    start_row();
    // systypes_list_cells(_("Type:"), 'filterType', null, true);
    customer_list_cells(_("Select a customer: "), 'filterType', $_GET['debtor_no'],
        _('New customer'), true, check_value('show_inactive'));
    if (list_updated('filterType'))
        $selected_id = -1;;

    end_row();
    end_table(1);

}

function trans_view($trans)
{
    return get_trans_view_str($trans["type_no"], $trans["trans_no"]);
}

//function edit_link($row)
//{
//  	return button('Edit'.$row["id"], _("Edit"), _("Edit"), ICON_EDIT);
//}

function view_link($row)
{
    //return button('view'.$row["id"], _("View"), _("View"), ICON_VIEW);
    return  '<a href=https://drive.google.com/open?id='.$row["unique_name"].'>View</a>';
}

//function download_link($row)
//{
//  	return button('download'.$row["unique_name"], _("Download"), _("Download"), ICON_DOWN);
//}

function delete_link($row)
{

    return button('Delete'.$row["id"], _("Delete"), _("Delete"), ICON_DOWN);
}

function display_rows($type)
{

    $sql = get_sql_for_attached_documents($type);
    $cols = array(
        _("#") =>array('name'=>'trans_no'),
        _("Description") => array('name'=>'description'),
        _("Filename") => array('name'=>'filename'),
        _("Size") => array('name'=>'filesize'),
        _("Filetype") => array('name'=>'filetype'),
        _("Date Uploaded") => array('name'=>'tran_date', 'type'=>'date'),
        array('insert'=>true, 'fun'=>'edit_link'),
        array('insert'=>true, 'fun'=>'view_link'),
        array('insert'=>true, 'fun'=>'download_link'),
        array('insert'=>true, 'fun'=>'delete_link')
    );
    $table =& new_db_pager('trans_tbl', $sql, $cols);

    $table->width = "60%";

    display_db_pager($table);
}

//----------------------------------------------------------------------------------------

start_form(true);

viewing_controls();

display_rows($_POST['filterType']);

br(2);

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
    if ($Mode == 'Edit')
    {
        $row = get_attachment($selected_id);
        $_POST['trans_no']  = $row["trans_no"];
        $_POST['description']  = $row["description"];
        hidden('trans_no', $row['trans_no']);
        hidden('unique_name', $row['unique_name']);
        label_row(_("Transaction #"),$row['trans_no']);
    }
    hidden('selected_id', $selected_id);
}
else
    hidden('trans_no',$_GET['debtor_no']);
// text_row_ex(_("Debtor #").':','trans_no', 10,'','',$_GET['debtor_no']);
text_row_ex(_("Description").':', 'description', 40);


end_table(1);
//------Uroosa
echo' <form method="post" action="fileupload.php" enctype="multipart/form-data">
           <h4>Attached Files:</h4>
            <input type="file" name="fileToUpload" id="fileToUpload">
   
        </form>';
submit_add_or_update_center($selected_id == -1, '', 'process');

end_form();

end_page();

