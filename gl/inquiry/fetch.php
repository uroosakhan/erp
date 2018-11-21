<?php

header("Content-type: application/json");
include_once "connection.php";

$res = mysql_query("SELECT	IF(ISNULL(a.gl_seq),0,a.gl_seq) as gl_seq,
		gl.tran_date,
		gl.type,
		gl.type_no,
		refs.reference,
		SUM(IF(gl.amount>0, gl.amount,0)) as amount,
		com.memo_,
		u.user_id,
		t.name
		FROM ".$tbpref."gl_trans as gl
		 LEFT JOIN ".$tbpref."audit_trail as a ON
			(gl.type=a.type AND gl.type_no=a.trans_no)
		 LEFT JOIN ".$tbpref."comments as com ON
			(gl.type=com.type AND gl.type_no=com.id)
		 LEFT JOIN ".$tbpref."refs as refs ON
			(gl.type=refs.type AND gl.type_no=refs.id)
		 LEFT JOIN ".$tbpref."users as u ON
			a.user=u.id
		  LEFT JOIN ".$tbpref."types as t ON
			a.type=t.id
			
			 GROUP BY gl.tran_date, a.gl_seq, gl.type, gl.type_no



");
while($obj = mysql_fetch_array($res)) {



    $arr[] = array(


        'tran_date' => $obj["tran_date"],
        'type' => str_replace("\'", "'", $obj["type"]),
        'type_no' => str_replace("\'", "'", $obj["type_no"]),
        'reference' => str_replace("\'", "'", $obj["reference"]),
        'amount' => str_replace("\'", "'", $obj["amount"]),
        'memo_' => str_replace("\'", "'", $obj["memo_"]),
        'user_id' => str_replace("\'", "'", $obj["user_id"]),
        'name' => str_replace("\'", "'", $obj["name"])
//        'contact_no' => str_replace("\'", "'", $obj["contact_no"]),
//        'other_cust' => str_replace("\'", "'", $obj["other_cust"]),
//        'status' => str_replace("\'", "'", $obj["status"]),
//        'user_id' => str_replace("\'", "'", $obj["user_id"]),
//        'assign_by' => str_replace("\'", "'", $obj["assign_by"]),
//        'remarks' => str_replace("\'", "'", $obj["remarks"]),
//        'plan' => str_replace("\'", "'", $obj["plan"]),
//        'plan1' => str_replace("\'", "'", $obj["plan1"]),
//        'actual' => str_replace("\'", "'", $obj["actual"]),
//        'actual1' => str_replace("\'", "'", $obj["actual1"]),
//        'plan_t' => str_replace("\'", "'", $obj["plan_t"]),
//        'actual_t' => str_replace("\'", "'", $obj["actual_t"]),
//        'last_update' => str_replace("\'", "'", $obj["last_update"]),
//        'time' => str_replace("\'", "'", $obj["time"]),
//        'priority' => str_replace("\'", "'", $obj["priority"]),
//        'progress' => str_replace("\'", "'", $obj["progress"]),
//        'inactive' => str_replace("\'", "'", $obj["inactive"])

    );
}

mysql_close($con);

print json_encode($arr);

?>