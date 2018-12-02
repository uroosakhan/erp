<?php
session_start();
$url_array = explode('?', 'http://' . $_SERVER ['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
$url = $url_array[0];

require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_DriveService.php';
$client = new Google_Client();
$client->setClientId('374707886285-6f6afglgn0t9pmepkf37944bh562p315.apps.googleusercontent.com');
$client->setClientSecret('dDSwjZoFoHVOrpPk19zuxy4Z');
$client->setRedirectUri($url);
$client->setScopes(array('https://www.googleapis.com/auth/drive'));
if (isset($_GET['code'])) {
    $_SESSION['accessToken'] = $client->authenticate($_GET['code']);
    header('location:' . $url);
    exit;
} elseif (!isset($_SESSION['accessToken'])) {
    $client->authenticate();
}
//header('Location: ../admin/attachments.php?debtor_no='.$debtor_id, true);
$client->setAccessToken($_SESSION['accessToken']);
$service = new Google_DriveService($client);
function get_types_names_new($selected_id)
{
//    $sql = "SELECT types_name FROM " . TB_PREF . "sms_type_template WHERE id = " . db_escape($selected_id);;
    $sql = "SELECT  debtor_ref FROM ".TB_PREF."debtors_master WHERE debtor_no=" . db_escape($selected_id);;
    $result = db_query($sql, "could not get sms template");
    $row = db_fetch_row($result);
    return $row[0];
}


//----UROOSA KHAN-----//

if (isset($_FILES["fileToUpload"]["name"])) {

    //header('Location: ../admin/attachments.php', true);
    if ($_FILES['fileToUpload']['size'] != 0) {

        global $path_to_root, $systypes_array;
        $arr = explode(".", $_FILES["fileToUpload"]["name"], 2);
        $first =[$_POST["filterType"]]; //$_POST["filterType"];

        $parentId = null;
        $files = $service->files->listFiles();
        $found = false;
        // Go through each one to see if there is already a folder with the specified name
        foreach ($files['items'] as $item) {
            if ($item['title'] == $first) {
                $found = true;
                $parentId = $item['id'];
                break;
            }
        }

        if ($found == false) {
            $folder_mime = "application/vnd.google-apps.folder";
            $folder_name = $first;


            $folder = new Google_DriveFile();
            $folder->setTitle($folder_name);
            $folder->setMimeType($folder_mime);
            $newFolder = $service->files->insert($folder);

            $parentId = $newFolder['id'];


        }
        ///file--insert//
        $myfile = $_FILES['fileToUpload']['tmp_name'];
        $mime_type = mime_content_type($myfile);

        $service = new Google_DriveService($client);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file = new Google_DriveFile();


        if ($parentId != null) {
            $parent = new Google_ParentReference();
            $parent->setId($parentId);
            $file->setParents(array($parent));
        }

        $size = $_FILES['fileToUpload']['size'];

        $path_parts = pathinfo($_FILES["file"]["name"]);
        //$extension = $path_parts['extension'];
        $ext = end((explode(".", $_FILES['fileToUpload']['name'])));


        $file->setTitle($_FILES['fileToUpload']['name']);
        $file->setDescription('This is a ' . $mime_type . ' document');
        $file->setMimeType($mime_type);

        $fileData = $service->files->insert(
            $file,
            array(
                'data' => file_get_contents($myfile),
                'mimeType' => $mime_type
            )
        );
        //to_get_file_into_tables

        $files = $service->files->listFiles();
        $found = false;
        foreach ($files['items'] as $item) {
            if ($item['title'] == $_FILES['fileToUpload']['name']) {
                $found = true;
                $fileid = $item['id'];
                break;
            }
        }


        error_log(print_r($fileid, TRUE));


        add_attachment($_POST['filterType'], $_POST['trans_no'], $_POST['description'],
            $_FILES['fileToUpload']['name'], $fileid, $size, $ext);
        display_notification(_("Attachment has been inserted."));
        //refresh('attachments.php');

        finfo_close($finfo);
        header('location:' . $url);
        exit;
    }
}


$deleteservice = new Google_DriveService($client);

?>