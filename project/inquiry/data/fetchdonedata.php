<?php
header("Content-type: application/json");

include_once "connection.php";

    $res = mysql_query("SELECT
                                0_task.id,
                                0_task.start_date,
                                0_task.end_date,
                                0_task.Stamp,
                                0_task.description,
                                
                                0_debtors_masterdemo.debtor_no,
                                
                                0_task.task_type,
                                0_task.call_type,
                                0_task.contact_no,
                                0_task.other_cust,
                                0_pstatus.status,
                                0_task.user_id,
                                
                                0_usersdemo.assign_by,                      
                                
                                0_task.remarks,
                                0_task.plan,
                                0_task.plan1,
                                0_task.actual,
                                0_task.actual1,
                                0_task.plan_t,
                                0_task.actual_t,
                                0_task.last_update,
                                0_task.time,
                                
                                0_priority.priority,
                                
                                 0_progress.progress,
                                 
                                0_task.inactive
                            

	 FROM 0_task 
	
      LEFT JOIN 
        0_pstatus
  		 ON 
  		 0_pstatus.id=0_task.status

        LEFT JOIN
            0_priority
             ON
             0_priority.id=0_task.priority
            
  		
  	         LEFT JOIN 
         0_debtors_masterdemo
  		 ON 
  		  0_debtors_masterdemo.debtor_nu=0_task.debtor_no
  		  
  		   LEFT JOIN 
         0_usersdemo
  		 ON 
  		  0_usersdemo.id=0_task.assign_by

 
  		   LEFT JOIN 
         0_progress
  		 ON 
  		  0_progress.id=0_task.progress


  WHERE  0_task.inactive=0 AND  0_task.status=1


  		    		  
 ");

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
            'user_id' => str_replace("\'", "'", $obj["user_id"]),
            'assign_by' => str_replace("\'", "'", $obj["assign_by"]),
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


    print json_encode($arr);
//    print json_encode($arry);



?>