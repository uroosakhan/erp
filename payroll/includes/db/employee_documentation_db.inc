<?php
function add_employee_documentations_data($employee_id,$document_name,
                                          $document_type,$expiry_date,$remarks,$doc_name,$filename,$filesize,$filetype)
{
    $sql = "INSERT INTO ".TB_PREF."employee_doc(employee_id,document_name,document_type,
                                                expiry_date, remarks,doc_upload_date,
                                                img_name,unique_name,tran_date,filename,filesize,filetype)
     VALUES (".db_escape($employee_id) . ",".db_escape($document_name) . ",".db_escape($document_type) . ","
        .db_escape(sql2date($expiry_date)) .","
        .db_escape($remarks).","
        .db_escape(sql2date(Today())).","
        .db_escape($doc_name).","
        .db_escape($doc_name).","
        .db_escape(sql2date(Today())).","
        .db_escape($filename).","
        .db_escape($filesize).","
        .db_escape($filetype)." )";
    db_query($sql,"The employee document could not be added");
}
function update_employee_documentations_data($selected_id,$employee_id,$document_name,$document_type,
                                          $expiry_date,$remarks,$doc_name,$filename,$filesize,$filetype)
{
    $sql = "Update ".TB_PREF."employee_doc SET
            document_name='$document_name',
            document_type='$document_type',
            expiry_date = ".db_escape(sql2date($expiry_date)).",
            remarks='$remarks',
            unique_name='$doc_name',
            filename='$filename',
            filesize='$filesize',
            filetype='$filetype'
             WHERE employee_id='$employee_id' AND id='$selected_id' ";
    db_query($sql,"The employee document could not be added");
}
function get_employee_documents_all($id)
{
    $sql = "SELECT  `id`, `employee_id`, `document_name`, 
                 `expiry_date`, `remarks`, `doc_upload_date` 
                 FROM ".TB_PREF."employee_doc WHERE employee_id=".db_escape($id);

    //if (!$show_inactive) $sql .= " WHERE !inactive";
//    $sql .= " ORDER BY id";
//    return db_query($sql,"could not get employee Documents");
    return $sql;
}
function get_employee_documentations($selected_id)
{
    $sql = "SELECT * FROM ".TB_PREF."employee_doc WHERE id=".db_escape($selected_id);

    $result = db_query($sql,"could not get employee nomination");
    return db_fetch($result);
}
function update_employee_documentation($selected_id, $employee_id,$document_name,$document_type,$expiry_date,$remarks,$doc_name)
{
    $sql = "UPDATE ".TB_PREF."employee_doc SET 
    document_name=".db_escape($document_name).",
    document_type=".db_escape($document_type).",
	expiry_date=".db_escape(sql2date($expiry_date)).",
	remarks=".db_escape($remarks).",
	img_name=".db_escape($doc_name)."
	
	WHERE  employee_id=".db_escape($employee_id)." AND  id = ".db_escape($selected_id)."  ";
    db_query($sql,"The employee nomination could not be updated");
}
?>