<?php
header("Content-type: application/json");
include_once "connection.php";

$res = mysql_query("SELECT
                                ".$tbpref."task.id,
                                ".$tbpref."task.start_date,
                                ".$tbpref."task.end_date,
                                ".$tbpref."task.Stamp,
                                ".$tbpref."task.description,
                                
                                ".$tbpref."debtors_masterdemo.debtor_no,
                                
                                ".$tbpref."task.task_type,
                                ".$tbpref."task.call_type,
                                ".$tbpref."task.contact_no,
                                ".$tbpref."task.other_cust,
                                ".$tbpref."pstatus.status,
                                
                                
                                ".$tbpref."users.user_id as assign,                      
                                
                                ".$tbpref."task.remarks,
                                ".$tbpref."task.plan,
                                ".$tbpref."task.plan1,
                                ".$tbpref."task.actual,
                                ".$tbpref."task.actual1,
                                ".$tbpref."task.plan_t,
                                ".$tbpref."task.actual_t,
                                ".$tbpref."task.last_update,
                                ".$tbpref."task.time,
                                
                                ".$tbpref."priority.priority,
                                
                                 ".$tbpref."progress.progress,
                                 
                                ".$tbpref."task.inactive
                            

	 FROM ".$tbpref."task 
	
      LEFT JOIN 
        ".$tbpref."pstatus
  		 ON 
  		 ".$tbpref."pstatus.id=".$tbpref."task.status

        LEFT JOIN
            ".$tbpref."priority
             ON
             ".$tbpref."priority.id=".$tbpref."task.priority
            
  		
  	         LEFT JOIN 
         ".$tbpref."debtors_masterdemo
  		 ON 
  		  ".$tbpref."debtors_masterdemo.debtor_nu=".$tbpref."task.debtor_no
  		  
  		   LEFT JOIN 
         ".$tbpref."users
  		 ON 
  		  ".$tbpref."users.id=".$tbpref."task.user_id 

 
  		   LEFT JOIN 
         ".$tbpref."progress
  		 ON 
  		  ".$tbpref."progress.id=".$tbpref."task.progress


       WHERE  ".$tbpref."task.inactive =0
    
      AND ".$tbpref."task.status  IN (2,5,6,7) 


");

//
//    inner JOIN
//         ".$tbpref."progress
//  		 ON
//  		  ".$tbpref."progress.id=".$tbpref."task.progress
//
//
//    inner JOIN
//         ".$tbpref."progress
//  		 ON
//  		  ".$tbpref."progress.id=".$tbpref."task.progress
//
//-----
//    $result=mysql_select_db("select priority from ".$tbpref."priority");
//    while($obj2 = mysql_fetch_array($result))
//    {
//        $arry[] = array(
//            'priority' => str_replace("\'", "'", $obj2["priority"])
//        );
//    }
//---
while($obj = mysql_fetch_array($res)) {



    $arr[] = array(
        'id' => $obj["id"],
        'start_date' => str_replace("\'", "'", $obj["start_date"]),
        'end_date' => str_replace("\'", "'", $obj["end_date"]),
        'Stamp' => str_replace("\'", "'", $obj["Stamp"]),
        'description' => str_replace("\'", "'", $obj["description"]),
        'debtor_no' => str_replace("\'", "'", $obj["debtor_no"]),
        'task_type' => str_replace("\'", "'", $obj["task_type"]),
        'call_type' => str_replace("\'", "'", $obj["call_type"]),
        'contact_no' => str_replace("\'", "'", $obj["contact_no"]),
        'other_cust' => str_replace("\'", "'", $obj["other_cust"]),
        'status' => str_replace("\'", "'", $obj["status"]),
        //'user_id' => str_replace("\'", "'", $obj["user_id"]),
        'assign' => str_replace("\'", "'", $obj["assign"]),
        'remarks' => str_replace("\'", "'", $obj["remarks"]),
        'plan' => str_replace("\'", "'", $obj["plan"]),
        'plan1' => str_replace("\'", "'", $obj["plan1"]),
        'actual' => str_replace("\'", "'", $obj["actual"]),
        'actual1' => str_replace("\'", "'", $obj["actual1"]),
        'plan_t' => str_replace("\'", "'", $obj["plan_t"]),
        'actual_t' => str_replace("\'", "'", $obj["actual_t"]),
        'last_update' => str_replace("\'", "'", $obj["last_update"]),
        'time' => str_replace("\'", "'", $obj["time"]),
        'priority' => str_replace("\'", "'", $obj["priority"]),
        'progress' => str_replace("\'", "'", $obj["progress"]),
        'inactive' => str_replace("\'", "'", $obj["inactive"])

    );
}

mysql_close($con);

print json_encode($arr);
//    print json_encode($arry);



?>