<?php

$page_security = 'SS_PAYROLL';
$path_to_root = "../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/db/connect_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/manufacturing.inc");
include_once($path_to_root . "/includes/data_checks.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_db.inc");
include_once($path_to_root . "/manufacturing/includes/manufacturing_ui.inc");

page(_($help_context = "Customer Detail"));
simple_page_mode(true);

$employee_id = $_GET['id'];
$update = $_GET['add_update'];


function get_file_extension($file_name) 
{
	//$ext =  strrchr($file_name,'.');
	$ext = pathinfo(company_path().'/images/');
	$matches = glob(company_path().'/images/'.$file_name.'.*');
	return $matches;
	
}

//------------------------------------------------------------
if(isset($_POST['upload']))
{
 
  if(isset($_FILES['filename']))
  {
  $errors = array();
  $allowed_ext = array('jpg', 'jpeg', 'gif', 'zip', 'pdf');
 
  $file_name = $_FILES['filename']['name'];
  $file_ext = strtolower(end(explode('.',  $file_name)));
 
  $file_size = $_FILES['filename']['size'];
  $file_temp = $_FILES['filename']['tmp_name'];
  
  if (in_array($file_ext, $allowed_ext) === false)
  {
  $errors[] = 'Extension are not allowed';
  display_warning(_('file extension of .jpg /.pdf /.zip is expected'));
  }
  
  if($file_size > (150000*1024))
  {
   $errors[] = 'Size is too large';
  display_warning(_('The file size is over the maximum allowed.'));
  }
  
  if(file_exists($file_name))
  {
  //print_r($_FILES['filename']);
  //unlink($file_name);
  }
  
  
  if(empty($errors))
  {
  //upload Code Here 
   $name_ = $_POST['employee_id'].".".$file_ext;
   //print_r($_FILES['filename']);
   $to = company_path().'/images/'.$name_;
   move_uploaded_file( $file_temp,  $to);
   display_notification(_("Image Uploaded."));
  }
  else
  {
  foreach ($errors as $error)
       {
	   echo $error, '</br>';
	   }//foreach
  }  //else
  } //if(isset($_FILES['filename']))
 
 
}

/*
if(isset($_POST['delete']))
{
  $file_del = "uploads/".$debtor_no.".jpg";
  
  if(file_exists($file_del))
  {
  // unlink($file_del);
  //header("Location:".$path_to_root."sales/manage/cust_pic_upload.php");
  // header('Location: '.$_SERVER['REQUEST_URI']);
 
  }
}
*/
//-----------------------------------------------------------------------------------------------

echo '<br><br>';

//get the q parameter from URL
//$q = $_REQUEST["q"];

echo "<form action='' method='post' enctype='multipart/form-data'>";
 
start_table(TABLESTYLE2);
hidden('employee_id', $employee_id);
file_row(_("Attached File .jpg/ .pdf/ .zip ") . ":", 'filename', 'filename');
$stock_img_link = "";
	$check_remove_image = false;
if (isset($_GET['id']) && file_exists(company_path().'/images/'
		.trim($_GET['id']).".jpg")) 
	{
	 // 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
		
		$stock_img_link .= "<img id='item_img' alt = '[".$_GET['id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_GET['id']).
			".jpg?nocache=".rand()."'"." height='300' border='0'>";
			
		
		
		$check_remove_image = true;
	} 
	else 
	{
		$stock_img_link .= _("No image");
	}

	
				
end_table();
echo "<br>";
div_start('controls');
		submit_center('upload', _("Upload File"), true, '', 'default');	 
		//label_row($stock_img_link);
		echo "<center>$stock_img_link</center>";	
div_end();
end_form();
start_table(TABLESTYLE2);
/*	
$stock_img_link1 = "";
$check_remove_image1 = false;

if(isset($_GET['id']) && file_exists(company_path().'/images/'.$employee_id.".jpg"))
{	
echo "<br>"; 
start_row();
//$stock_img_link1 .= "<img src='".company_path().'/images/'.$employee_id.".jpg"." alt='Smiley face' height='160' width='142'>";
$stock_img_link1 .= "<img id='item_img' alt = '[".$_GET['id'].".jpg".
			"]' src='".company_path().'/images/'.trim($_GET['id']).
			".jpg?nocache=".rand()."'"." height='300' border='0'>";
echo $stock_img_link1;
end_row();

}
end_table();*/
//image show end


$extension = get_file_extension($employee_id);
$j = count($extension); //loop

echo "<br>";


start_table(TABLESTYLE2);
	
		$serial = 1;
		for($i=0; $i < $j; $i++ )
		{
		$name = substr($extension[$i], -3);	
        //label_row("Ext:", $name);
        //
		//echo $extension[$i];
		//  	if(file_exists(company_path().'/images/'.$employee_id.".*"))
        //	{		
			start_row();
			echo '<td class="label">'.$serial.' :</td>';
	  		hyperlink_params_td( $extension[$i],'<b>'.$name.'</b>');
			end_row();
			$serial++; 
		//	}
		}
end_table();
echo "<br>";
end_page();
?>