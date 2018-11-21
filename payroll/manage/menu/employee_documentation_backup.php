<?php
$path_to_root = "../..";

include_once($path_to_root . "/includes/db_pager.inc");
include_once($path_to_root . "/includes/session.inc");
include($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");
include($path_to_root . "/payroll/includes/db/employee_documentation_db.inc");
include($path_to_root . "/payroll/includes/db/employee_nomination_db.inc");


$page_security = 'SA_SALESTRANSVIEW';

//set_page_security( @$_POST['order_view_mode'],
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE'),
//	array(	'OutstandingOnly' => 'SA_SALESDELIVERY',
//			'InvoiceTemplates' => 'SA_SALESINVOICE')
//);
simple_page_mode(true);

$_POST['employee_id'] = $_GET['employee_id'];

if ($Mode=='ADD_ITEM' || $Mode=='UPDATE_ITEM')
{

	$input_error = 0;

	if (strlen($_POST['document_name']) == '')
	{
		$input_error = 1;
		display_error(_("Document Name Can not be empty"));
		set_focus('document_name');
	}

	if ($input_error != 1)
	{

		$upload_file = "";
		if (isset($_FILES['pic']) && $_FILES['pic']['name'] != '')
		{

//	$stock_id = $_POST['NewStockID'];
			$doc_name = $_POST['employee_id'] . "" . $_POST['document_name'];

			$result = $_FILES['pic']['error'];
			$upload_file = 'Yes'; //Assume all is well to start off with
			$filename = company_path() . '/emp_documents';

			if (!file_exists($filename)) {
				mkdir($filename);
			}
			$filename .= "/" . item_img_name($doc_name) . ".jpg";

			//But check for the worst
			if ((list($width, $height, $type, $attr) = getimagesize($_FILES['pic']['tmp_name'])) !== false)
				$imagetype = $type;
			else
				$imagetype = false;

			//$imagetype = exif_imagetype($_FILES['pic']['tmp_name']);
			if ($imagetype != IMAGETYPE_GIF && $imagetype != IMAGETYPE_JPEG && $imagetype != IMAGETYPE_PNG) {    //File type Check
				display_warning(_('Only graphics files can be uploaded'));
				$upload_file = 'No';
			} elseif (@strtoupper(substr(trim($_FILES['pic']['name']), @in_array(strlen($_FILES['pic']['name']) - 3)), array('JPG', 'PNG', 'GIF'))) {
				display_warning(_('Only graphics files are supported - a file extension of .jpg, .png or .gif is expected'));
				$upload_file = 'No';
			} elseif ($_FILES['pic']['size'] > (1000 * 1024)) { //File Size Check
				display_warning(_('The file size is over the maximum allowed. The maximum size allowed in KB is') . ' ' . 1000);
				$upload_file = 'No';
			} elseif (file_exists($filename)) {
				$result = unlink($filename);
				if (!$result) {
					display_error(_('The existing image could not be removed'));
					$upload_file = 'No';
				}
			}

			if ($upload_file == 'Yes') {
				$result = move_uploaded_file($_FILES['pic']['tmp_name'], $filename);
			}
			$Ajax->activate('details');
			/* EOF Add Image upload for New Item  - by Ori */
		}


		if ($selected_id != -1)
		{
			update_employee_documentation($selected_id,$_POST['emp_id'],$_POST['document_name'],$_POST['document_type'],
				$_POST['expiry_date'],$_POST['remarks'],$doc_name);
			$note = _('Selected employee nomination has been updated');
		//	refresh('employee_nomination.php');
		}
		else
		{

			$result=validate_employee_nomination($_POST['nominee_name']);

			if(!$result) {
				add_employee_documentations_data($_POST['emp_id'],$_POST['document_name'],$_POST['document_type'],
					$_POST['expiry_date'],$_POST['remarks'],$doc_name);
				$note = _('Employee New Documents has been added');
				//	refresh('employee_nomination.php');
			}
			else{
				display_error("Cannot Add Duplicate Nominee Name");

			}


			}

		display_notification($note);
		$Mode = 'RESET';
	}
}

if ($Mode == 'Delete')
{

	$cancel_delete = 0;

	// PREVENT DELETES IF DEPENDENT RECORDS IN 'debtors_master'

	/*if (key_in_foreign_table($selected_id, 'payroll_allowance', 'allow_id'))
	{
		$cancel_delete = 1;
		display_error(_("Cannot delete this department because Employee have been created using this dept."));
	}
	if ($cancel_delete == 0)
	{*/
	delete_employee_nomination($selected_id);
	display_notification(_('Selected employee nomination has been deleted'));
	//refresh('employee_nomination.php');
	//} //end if Delete group
	$Mode = 'RESET';
}

if ($Mode == 'RESET')
{
	$selected_id = -1;
	$sav = get_post('show_inactive');
	unset($_POST);
	if ($sav) $_POST['show_inactive'] = 1;
}

function get_document_type_name2($group_no)
{
	$sql = "SELECT description FROM ".TB_PREF."document_type WHERE id = ".db_escape($group_no);
	$result = db_query($sql, "could not get document type");
	$row = db_fetch($result);
	return $row[0];
}


///=======================================================

start_form();

$result1 = get_employee_documents_all($_GET['employee_id']);
start_table(TABLESTYLE, "width=60%");
$th = array(_("Doc Name"), _("Doc Type"), _("Expiry Date"),
	_("Remarks"),_("View Doc"),_("Dowload Doc"),_("Doc Uploaded Date"), "Edit", "Delete");
//inactive_control_column($th);

table_header($th);
$k = 0;

while ($myrow = db_fetch($result1)) {

	alt_table_row_color($k);
	$img = $path_to_root . "/company/0/emp_documents/".$myrow['img_name'].".jpg";
	label_cell($myrow["document_name"]);
	label_cell(get_document_type_name2($myrow["document_type"]));
	label_cell(sql2date($myrow["expiry_date"]));
	label_cell($myrow["remarks"]);
echo'<td><a href="C:/wamp/www/tc/tc_new/company/0/emp_documents/'.$myrow["img_name"].'.jpg" 
target="_blank" onclick="window.open(\' '.$img.' \', \'popup\', \'height=500, width=500\'); 
return false;">View </a></td>';
	echo'<td><a href="'.$img.'" download>Dowload</td>';
	label_cell(sql2date($myrow["doc_upload_date"]));
	//$_POST['month']  = $myrow["month"];

	//label_cell(get_employee_name11($myrow["emp_id"]));
	//inactive_control_cell($myrow["id"], $myrow["inactive"], 'man_month', 'text_field_1');
	edit_button_cell("Edit" . $myrow["id"], _("Edit"));
	delete_button_cell("Delete" . $myrow["id"], _("Delete"));
	end_row();
}
//inactive_control_row($th);
end_table(1);

//echo $emp_id;
//-------------------------------------------------------------------------------------------------

start_table(TABLESTYLE2);

if ($selected_id != -1)
{
	if ($Mode == 'Edit') {
		$myrow = get_employee_documentations($selected_id);

		$_POST['document_name']  = $myrow["document_name"];
		$_POST['document_type']  = $myrow["document_type"];
		$_POST['expiry_date']  = sql2date($myrow["expiry_date"]);
		$_POST['share']  = $myrow["share"];
		$_POST['remarks']  = $myrow["remarks"];
		$_POST['img_name']  = $myrow["img_name"];
		//$_POST['month']  = $myrow["month"];

		$img = $path_to_root . "/company/0/emp_documents/".$myrow['img_name'].".jpg";
	}
	hidden("selected_id", $selected_id);

//	label_row(_("text_field_1"), $myrow["text_field_1"]);
//	label_row(_("text_field_2"), $myrow["text_field_2"]);
//	label_row(_("text_field_3"), $myrow["text_field_3"]);
//	label_row(_("text_field_4"), $myrow["text_field_4"]);

}
//employee_list_row(_("Employee Name:"), 'emp_id', $emp_id,true);
$_POST['emp_id'] = $_GET['employee_id'];
hidden("emp_id", $_POST['emp_id'] );

//emp_dept_row( _("Department:"), 'dept_id', null,  _("Select department: "),
//	true, check_value('show_inactive'));
///month_list_cells( _("Month:"), 'month', null,  _('Month Entry '), true, check_value('show_inactive'));
text_row(_("Document Name :"), "document_name");
emp_document_type_cells(_("Select Document:"), 'document_type', $_POST['document_type'], true, false, false);
end_row();
date_cells(_("Expiry Date:"), 'expiry_date', '', null, 0, 0, 2);
end_row();
text_row_ex(_("Remarks :"), 'remarks', 40);
// Add image upload for New Item  - by Joe
file_row(_("Image File (.jpg)") . ":", 'pic', 'pic');
// Add Image upload for New Item  - by Joe
$stock_img_link = "";
$check_remove_image = false;
if (isset($_POST['img_name']) && file_exists(company_path().'/emp_documents/'
		.item_img_name($_POST['img_name']).".jpg"))
{
	// 31/08/08 - rand() call is necessary here to avoid caching problems. Thanks to Peter D.
	$stock_img_link .= "<img id='img_name' alt = '[".$_POST['img_name'].".jpg".
		"]' src='".company_path().'/emp_documents/'.item_img_name($_POST['img_name']).
		".jpg?nocache=".rand()."'"." height='$pic_height' border='0'>";
	$check_remove_image = true;
}
else
{
	$stock_img_link .= _("No image");
}

label_row("Image", $stock_img_link);
/*text_row(_("Relation with Nominee:"), "relation");
text_row(_("Nominee Age:"), "age");
text_row(_("Nominee Share:"), "share");
text_row(_("Remarks:"), "remarks");*/
end_table(1);

submit_add_or_update_center($selected_id == -1, '', 'both');

end_form();

//end_page();

end_page(false, false, false, ST_BANKPAYMENT, $trans_no);
?>
