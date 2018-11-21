<?php
class notification 
{
	public function All_notification()
	{
	   	global $img,$img2,$img3,$img4;
        
	    $path_to_root="././.";
        include_once($path_to_root . "/includes/session.inc");
        include_once($path_to_root . "/includes/date_functions.inc");
        include($path_to_root . "/includes/ui.inc");
        include_once($path_to_root . "/modules/dashboard/charts/charts_utils.php");  
   	    $today = date2sql(Today());
		$sql = "SELECT  trans.due_date, 
			debtor.name,debtor.debtor_no,trans.trans_no,trans.type,
			(trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount - trans.discount1)	AS total	
			    FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor, 
				".TB_PREF."cust_branch as branch
			    WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
				AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount - trans.alloc - trans.discount1) > ".FLOAT_COMP_DELTA." 
                AND trans.due_date>=".$today." 
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY due_date DESC LIMIT 10";
		$result = db_query($sql);
        
        		if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')))

                echo'<li class="dropdown messages-menu">
                <a href="#" class="dropdown-toggle" style="height:50px;"  data-toggle="dropdown">
                  
                  
                  <i ><img src="'.$img.'" style="height:20px;"></i>';
                   
	                // $i = 0;
					// $data = array();
					 //$string = array();  
				  //for count data 
 function get_total_invoice($today)
 {
	$sql = "SELECT COUNT(debtor.debtor_no) AS Icount		
			FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor, 
				".TB_PREF."cust_branch as branch
			WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
				AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount - trans.alloc - trans.discount1) > ".FLOAT_COMP_DELTA." 
                AND trans.due_date>=".$today." 
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY due_date DESC ";
	$result = db_query($sql, "could not get sales type");
	$row = db_fetch_row($result);
	return $row[0];
}
$today = date2sql(Today());
 function get_total_bills($today)
 {
		
		$sql1 = "SELECT  
			COUNT(s.supplier_id) AS Bcount 	
			FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as s 
			WHERE s.supplier_id = trans.supplier_id
				AND trans.type = ".ST_SUPPINVOICE." 
				AND due_date>=".$today." 
				AND (ABS(trans.ov_amount + trans.ov_gst + 
					trans.ov_discount) - trans.alloc) > ".FLOAT_COMP_DELTA."
					";
		$sql1 .= " AND DATEDIFF('$today', trans.due_date) > 0  ";
		$result1 = db_query($sql1);
	$row = db_fetch_row($result1);
	return $row[0];
}

                     
function getROLTransactions()
{
	$sql = "SELECT ".TB_PREF."stock_master.description,
	SUM(IF(".TB_PREF."stock_moves.stock_id IS NULL,0,".TB_PREF."stock_moves.qty)) AS QtyOnHand ,".TB_PREF."loc_stock.reorder_level,  ".TB_PREF."stock_master.stock_id
	FROM (".TB_PREF."stock_master, ".TB_PREF."loc_stock)
	RIGHT JOIN ".TB_PREF."stock_moves ON (".TB_PREF."stock_master.stock_id=".TB_PREF."stock_moves.stock_id)
	WHERE ".TB_PREF."stock_master.stock_id=".TB_PREF."loc_stock.stock_id
	AND ".TB_PREF."stock_moves.loc_code=".TB_PREF."loc_stock.loc_code

	AND (".TB_PREF."stock_master.mb_flag='B' OR ".TB_PREF."stock_master.mb_flag='M') 
	AND ".TB_PREF."loc_stock.reorder_level>0
	GROUP BY ".TB_PREF."stock_master.stock_id ORDER BY QtyOnHand DESC";

    return db_query($sql,"No transactions were returned");
}

$res = getROLTransactions();

function get_reorder_count()
{
    $res = getROLTransactions();
    $ROLcount = 0;
    while ($rol_trans=db_fetch($res))
	{
		if($rol_trans['reorder_level']> $rol_trans['QtyOnHand'])
		{
            $ROLcount += 1;
             
		}
	}
	return $ROLcount;
}
$ROLcount = get_reorder_count();
             if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')))
               echo'   <span class="label label-success">'.get_total_invoice($today).'</span>
                </a>
                <ul class="dropdown-menu">
             
                  <li class="header">Outstanding receipt in next 30 days</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- start message -->';
		else
			echo'   <span class="label label-success">'.'</span>
                </a>
                <ul class="dropdown-menu">
               


                  <li class="header">Outstanding receipt in next 30 days</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li><!-- start message -->
                        
                                               ';
           while ($myrow = db_fetch($result))
		{
             echo ' <a href="/./sales/view/view_invoice.php?debtor_no='.$myrow['debtor_no'].'&trans_no='.$myrow['trans_no'].'&trans_type='.$myrow['type'].'&popup=1"
                               
                               onclick="javascript:openWindow(this.href,this.target); return false;"
                               
                               >
                            <small class="pull-left">'. $myrow["name"] .'</small>
                          <small class="pull-right">'. price_format($myrow['total']) .'</small>
                        </a>';
				}
                         //<!-- end message -->
						 		if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')))

                  echo '
                      </li>  </ul>
                   
                  <li class="footer"><a href="#" data-toggle="modal" data-target="#myModal">View all</a></li>
                </ul>
              </li>';
              $today = date2sql(Today());
			  $next = date("Y-m-d", mktime(0, 0, 0, date("m")+1 , date("d"),date("Y")));
			  $month=date2sql($next);
		$sql1 = "SELECT   trans.tran_date, trans.due_date,
			s.supp_name,s.supplier_id, trans.trans_no,trans.type,
			(trans.ov_amount + trans.ov_gst + trans.ov_discount) AS total  	
			FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as s 
			WHERE s.supplier_id = trans.supplier_id
				AND trans.type = ".ST_SUPPINVOICE." 
				AND due_date>=".$today." 
				AND (ABS(trans.ov_amount + trans.ov_gst + 
					trans.ov_discount) - trans.alloc) > ".FLOAT_COMP_DELTA."
					";
		$sql1 .= " AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY total DESC LIMIT 10";
		$result1 = db_query($sql1);
             // var_dump($result1);
              
              
if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')))

                      
             //supplier start   
             echo ' <!-- Notifications: style can be found in dropdown.less -->
              <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" style="height:50px;"  data-toggle="dropdown">
                  
                  
                  <i ><img src="'.$img2.'" style="height:25px;"></i>
                  
                  
                  <span class="label label-warning">'.get_total_bills($today).'</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">Outstanding payments in next 30 days</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li>'; //supplier end
					   while ($myrow1 = db_fetch($result1))
		              {
                           echo ' <a href="/./purchasing/view/view_supp_invoice.php?debtor_no='.$myrow1['debtor_no'].'&trans_no='.$myrow1['trans_no'].'&trans_type='.$myrow1['type'].'&popup=1"
                               
                               onclick="javascript:openWindow(this.href,this.target); return false;"
                               
                               >
                            <small class="pull-left">'. $myrow1["supp_name"] .'</small>
							<small class="pull-right">'. price_format($myrow1['total']) .'</small>
                        </a>';
						//var_dump($myrow1["total"]);
		               }
		            
if ($_SESSION['wa_current_user']->can_access('SS_RECIEPT_ORDER')||($_SESSION['wa_current_user']->can_access('SS_PAYMENTS')))

                
                		echo '
                      </li> 
                    </ul>
                  </li>
                  <li class="footer"><a href="#" data-toggle="modal" data-target="#myPayModel">View all</a></li>
                </ul>
              </li>
              
              
              <!-- Tasks: style can be found in dropdown.less -->
              <li class="dropdown tasks-menu">
                <a href="#" class="dropdown-toggle" style="height:50px;" data-toggle="dropdown">
                  <i ><img src="'.$img3.'" style="height:22px;"></i>
                  <span class="label label-danger">'.$ROLcount.'</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">Items below order level</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      
                      <li><!-- Task item -->';
 
                $path_to_root="..";
        while ($trans=db_fetch($res))
        	{
        		if($trans['reorder_level']> $trans['QtyOnHand'])
        		{
                               echo ' <a href="/./inventory/inquiry/stock_status.php?stock_id='.$trans['stock_id'].'&popup=1"
                               
                               onclick="javascript:openWindow(this.href,this.target); return false;"
                               
                               >
                               <small class="pull-left">'. $trans["description"] .'</small>
                                <small class="pull-right">
                                <font color="red">
                                '. price_format($trans['QtyOnHand']) .' </font>
                                /
                                <font color="green">
                                '. price_format($trans['reorder_level']) .' </font>
                                </small>
                                </a>';
        						//var_dump($trans['reorder_level']);
        		}
        	}
						
                     echo ' </li><!-- end task item -->
                     
                      
                    </ul>
                  </li>
                  <li class="footer">
                    <a href="#" data-toggle="modal" data-target="#myReOModal">View all</a>
                  </li>
                </ul>
              </li>';
                   
}
}
?>