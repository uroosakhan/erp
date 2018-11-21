<?php
$file = 'Welcome.txt';   
// Open File For Reading
$handle = fopen($file, 'r');
$data = fread($handle, filesize($file));
echo $data;
fclose($handle);
?>