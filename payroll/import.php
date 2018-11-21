<?php  
$page_security = 'SS_PAYROLL';
$path_to_root="../..";

include($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");
add_access_extensions();

page("Import CSV");



if ($_FILES[csv][size] > 0) { 

    //get the csv file 
    $file = $_FILES[csv][tmp_name]; 
    $handle = fopen($file,"r"); 
     
    //loop through the csv file and insert into database 
    do { 
        if ($data[0]) { 
            mysql_query("INSERT INTO 0_attendance (contact_first, contact_last, contact_email) VALUES 
                ( 
                    '".addslashes($data[0])."', 
                    '".addslashes($data[1])."', 
                    '".addslashes($data[2])."' 
                ) 
            "); 
        } 
    } while ($data = fgetcsv($handle,1000,",","'")); 
    // 

    //redirect 
    header('Location: import.php?success=1'); die; 

} 

start_form(true);

start_table(TABLESTYLE2);


table_section_title("Import Settings");


 if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice 

label_row("Import File:", "<input type='file' id='csv' name='csv'>");


end_table(1);

submit_center('import', "Perform Import");//,true,false,'process',ICON_SUBMIT);

end_form();

end_page();

?> 
