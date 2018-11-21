<?php
//saad 17 may

function add_kb1($category, $title, $description,$trans_no,$date,$filename, $filesize, $filetype)

{
	$sql = "INSERT INTO ".TB_PREF."knowledge_base(category,title,description,trans_no,date,filename
	,filesize,filetype)
		VALUES ("
		.db_escape($category) . ", "
		.db_escape($title) . ", "
		.db_escape($description) . ","
		.db_escape($trans_no) . ","
		.db_escape($date) . ","
		.db_escape($filename) . ","
		.db_escape($filesize) . ","
		.db_escape($filetype) . ")";

	db_query($sql, "The insert of the knowledge base failed");
}
function get_gk_nw1($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."knowledge_base WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get gk");
	return db_fetch($result);
}
function update_kb1($selected_id,$category, $title, $description,$filename, $filesize, $filetype,$date,$trans_no)
{
//	$start_date_db = date2sql($start_date);
//	$end_date_db = date2sql($end_date);

	$sql = "UPDATE ".TB_PREF."knowledge_base SET category=".db_escape($category) . ",
		title=".db_escape($title) . ",
		description=".db_escape($description) . ",
	
		filename=".db_escape($filename) . ",
		filesize=".db_escape($filesize) . ",
		filetype=".db_escape($filetype) . ",
		date=".date2sql(db_escape($date)) . ",
		trans_no=".db_escape($trans_no) . "
		

		WHERE id = ".db_escape($selected_id);
	db_query($sql,"The update of the knowledge base failed");
	return db_insert_id();
}
function delete_kb1($selected_id)
{
	$sql="DELETE FROM 0_knowledge_base WHERE id=".db_escape($selected_id);
	db_query($sql,"This knowledge base could not be deleted");
}

function delete_kb($selected_id)
{
	$sql="DELETE FROM ".TB_PREF."knowledge_base WHERE id=".db_escape($selected_id);
	db_query($sql,"The knowledge base could not be deleted");
}

function get_kb1($selected_id)
{
	$sql = "SELECT *  FROM  0_knowledge_base WHERE id=".db_escape($selected_id);
	$result = db_query($sql,"could not get sales person");
	return db_fetch($result);
}
//function get_attachment_kb($id)
//{
//	$sql = "SELECT * FROM ".TB_PREF."knowledge_base WHERE id=".db_escape($id);
//	$result = db_query($sql, "Could not retrieve attachments");
//	return db_fetch($result);
//}

function update_knowledge_base($selected_id, $category, $title, $description,$date,$filename)
{
	$sql = "UPDATE ".TB_PREF."knowledge_base SET category=".db_escape($category) . ",
		title=".db_escape($title).",
		description=".db_escape($description).",
		date=".db_escape($date).",
		filename=".db_escape($filename)."
		
		WHERE id = ".db_escape($selected_id);
	db_query($sql,"The update of the knowledge base failed");
	return db_insert_id();
}
function dateDiff_kb($date)
{
	$mydate= date("Y-m-d H:i:s");
	$theDiff="";
	//echo $mydate;//2014-06-06 21:35:55
	$datetime1 = date_create($date);
	$datetime2 = date_create($mydate);
	$interval = date_diff($datetime1, $datetime2);
	//echo $interval->format('%s Seconds %i Minutes %h Hours %d days %m Months %y Year    Ago')."<br>";
	$min=$interval->format('%i');
	$sec=$interval->format('%s');
	$hour=$interval->format('%h');
	$mon=$interval->format('%m');
	$day=$interval->format('%d');
	$year=$interval->format('%y');
	if($interval->format('%i%h%d%m%y')=="00000")
	{
		//echo $interval->format('%i%h%d%m%y')."<br>";
		return $sec." Seconds";

	}

	else if($interval->format('%h%d%m%y')=="0000"){
		return $min." Minutes";
	}


	else if($interval->format('%d%m%y')=="000"){
		return $hour." Hours";
	}


	else if($interval->format('%m%y')=="00"){
		return $day." Days";
	}

	else if($interval->format('%y')=="0"){
		return $mon." Months";
	}

	else{
		return $year." Years";
	}

}


function get_knowledgebase($selected_id)
{
	$sql = "SELECT *  FROM ".TB_PREF."knowledge_base WHERE id=".db_escape($selected_id);

	$result = db_query($sql,"could not get knowledge base");
	return db_fetch($result);
}

?>