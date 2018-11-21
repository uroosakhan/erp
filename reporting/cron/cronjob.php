<?php
$url='https://erp30.com/reporting/prn_redirect.php?PARAM_0=2018-07-01&PARAM_1=2018-07-31&amp;PARAM_2=0&PARAM_3=&PARAM_4=0&REP_ID=3049';
//fopen opens webpage in Binary
// $url1=htmlspecialchars_decode($url);
$url1= str_replace('&amp;','&',$url);
$handle=fopen($url1,"rb");
// initialize
$lines_string="abcd";
// read content line by line
do{
	$data=fread($handle,1024);
	if(strlen($data)==0) {
		break;
	}
	$lines_string.=$data;
}while(true);
//close handle to release resources
fclose($handle);
//output, you can also save it locally on the server
echo $lines_string;
?>