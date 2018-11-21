<?php
class invoiceswidgets
{
public function AllInvoices()
{
  $path_to_root = "././.";
  $today = date2sql(Today());
//  $begin1 = date2sql($begin);
//		$week = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-7,date("Y")));
//		$yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")-1,date("Y")));
//		$year = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"),date("Y")-1));
//		$month = date("Y-m-d", mktime(0, 0, 0, date("m")-1 , date("d"),date("Y")));
//		$pyear = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d"),date("Y")-2));
//		if($_GET['w1']=='')
//		{
			$datechart=$today;
//		}
//		else
//		{
//			$datechart=$_GET['w1'];
//		}

		$sql = "SELECT trans.trans_no, trans.reference, trans.tran_date, trans.due_date, s.supplier_id, 
			s.supp_name, s.curr_code,
			(trans.ov_amount + trans.ov_gst + trans.ov_discount) AS total,  
			(trans.ov_amount + trans.ov_gst + trans.ov_discount - trans.alloc) AS remainder,
			DATEDIFF('$datechart', trans.due_date) AS days 	
			FROM ".TB_PREF."supp_trans as trans, ".TB_PREF."suppliers as s 
			WHERE s.supplier_id = trans.supplier_id
				AND trans.type = ".ST_SUPPINVOICE." AND (ABS(trans.ov_amount + trans.ov_gst + 
					trans.ov_discount) - trans.alloc) > ".FLOAT_COMP_DELTA."
					";
		//if ($convert)
		$sql .= " AND DATEDIFF('$datechart', trans.due_date) > 0 ORDER BY days DESC LIMIT 10";
		$result = db_query($sql);

   echo'<div class="col-xs-12">
          <div class="box">
            <div class="box-header">
			
              <h1 class="box-title col-md-9" style="margin-top:6px;font-weight:bolder">Top 10 Overdue Purchase Invoices <p type="hidden" id="valu11"></p></h1>
			 
  <meta charset="utf-8">

 
<script type="text/javascript" charset="utf-8">
function displayVals() {
  var singleValues = $( "#single" ).val();
  
  var multipleValues = $( "#multiple" ).val() || [];
  $( "#valu11" ).html( "<b>Single:</b> " + singleValues +
    " <b></b> " + multipleValues.join( ", " ) );
	
	var date = document.getElementById("single").value;
	window.location = "http://localhost/theme/theme/index.php?w1="+date;
	
	
}
</script>
';
//var_dump($datechart);

           echo '</div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
			
              <table id="tb" class="table table-hover">
                <tbody><tr>
                  <th>#</th>
                  <th>Ref</th>
                  <th>Date</th>
                  <th>Due Date</th>
                  <th>Supplier</th>
                  <th>Currency</th>
                  <th>Total</th>
                  <th>Remainder</th>
				  <th>Days</th>				  				  
                </tr>';
				while ($myrow = db_fetch($result))
		{
              echo '<tr>
                  <td>'. get_trans_view_str(ST_SUPPINVOICE, $myrow["trans_no"]) .'</td>
                  <td>'. $myrow['reference'] .'</td>
                  <td>'. sql2date($myrow['tran_date']) .'</td>
                  <td>'. sql2date($myrow['due_date']) .'</td>
                  <td>'. $myrow["supplier_id"]." ".$myrow["supp_name"] .'</td>
                  <td>'. $myrow['curr_code'] .'</td>
                  <td><span class="label label-success">'. price_format($myrow['total']) .'</span></td>
                  <td><span class="label label-warning">'. price_format($myrow['remainder']) .'</span></td>
                  <td>'. $myrow['days']  .'</td>
                </tr>';
				}

              echo '<tr style="background-color:white;"><td colspan="9">';
			  //echo'';
			  echo'<div class="box-footer clearfix" style="border-top-style:none;">
              <a href='.$path_to_root . '/purchasing/po_entry_items.php?NewInvoice=Yes class="btn btn-sm btn-info btn-flat pull-left">Place New  Purchase Invoice</a>
              
              
              <a href="#" class="btn btn-sm btn-default btn-flat pull-right" data-toggle="modal" data-target="#OverduePurchaseInvoices">View All</a>
              
              
            </div>';
			  echo'</td></tr></tbody></table>
			  
			  <script>
function change(){
    document.getElementById("tb").submit();
}
</script>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>';

$today = date2sql(Today());
		$sql = "SELECT trans.trans_no, trans.reference,	trans.tran_date, trans.due_date, debtor.debtor_no, 
			debtor.name, branch.br_name, debtor.curr_code,
			(ov_amount+ov_gst+ov_freight+ov_freight_tax-trans.discount1-trans.discount2)	AS total,  
			(trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount-trans.discount1-trans.discount2 - trans.alloc) AS remainder,
			DATEDIFF('$today', trans.due_date) AS days 	
			FROM ".TB_PREF."debtor_trans as trans, ".TB_PREF."debtors_master as debtor, 
				".TB_PREF."cust_branch as branch
			WHERE debtor.debtor_no = trans.debtor_no AND trans.branch_code = branch.branch_code
				AND trans.type = ".ST_SALESINVOICE." AND (trans.ov_amount + trans.ov_gst + trans.ov_freight 
				+ trans.ov_freight_tax + trans.ov_discount -trans.discount1-trans.discount2 - trans.alloc) > ".FLOAT_COMP_DELTA." 
				AND DATEDIFF('$today', trans.due_date) > 0 ORDER BY days DESC LIMIT 10";
		$result = db_query($sql);
   echo'<div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h1 class="box-title col-md-9" style="margin-top:6px;font-weight:bolder">
			  	Top 10 Overdue Sales Invoices
			  </h1>
			  
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>#</th>
                  <th>Ref</th>
                  <th>Date</th>
                  <th>Due Date</th>
                  <th>Client</th>
				  <th>Branch</th>
                  <th>Currency</th>
                  <th>Total</th>
                  <th>Remainder</th>
				  <th>Days</th>				  				  
                </tr>';
      while ($myrow = db_fetch($result))
		{
              echo '<tr>
                  <td>'. get_trans_view_str(ST_SALESINVOICE, $myrow["trans_no"]) .'</td>
                  <td>'. $myrow['reference'] .'</td>
                  <td>'. sql2date($myrow['tran_date']) .'</td>
                  <td>'. sql2date($myrow['due_date']) .'</td>
                  <td>'. $myrow["debtor_no"]." ".$myrow["name"] .'</td>
				  <td>'. $myrow['br_name'] .'</td>
                  <td>'. $myrow['curr_code'] .'</td>
                  <td><span class="label label-success">'. price_format($myrow['total']) .'</span></td>
                  <td><span class="label label-warning">'. price_format($myrow['remainder']) .'</span></td>
                  <td>'. $myrow['days']  .'</td>
                </tr>';
				}

              echo '<tr style="background-color:white;"><td colspan="10">';
			  //echo'';
			  echo'<div class="box-footer clearfix" style="border-top-style:none;">
              <a href='.$path_to_root . '/sales/sales_order_entry.php?NewInvoice=0 class="btn btn-sm btn-info btn-flat pull-left">Place New  Invoice</a>
              
                
             <a href="#" class="btn btn-sm btn-default btn-flat pull-right" data-toggle="modal" data-target="#OverdueSalesInvoices">View All</a>
            
            </div>';
			  echo'</td></tr></tbody></table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>';

		$date_after = date2sql(add_days(Today(), -30));
	$date_to =date2sql(add_days(Today(), 1));

	$sql = "SELECT 
  		trans.type, 
		trans.trans_no, 
		trans.order_, 
		trans.reference,
		trans.tran_date, 
		trans.due_date, 
		debtor.name, 
		branch.br_name,
		debtor.curr_code,
		(trans.ov_amount + trans.ov_gst + trans.ov_freight 
			+ trans.ov_freight_tax + trans.ov_discount-trans.discount1-trans.discount2)	AS TotalAmount, ";
		$sql .= "trans.alloc AS Allocated,
		((trans.type = ".ST_SALESINVOICE.")
			AND trans.due_date < '" . date2sql(Today()) . "') AS OverDue ,
		Sum(line.quantity-line.qty_done) AS Outstanding
		FROM "
			.TB_PREF."debtor_trans as trans
			LEFT JOIN ".TB_PREF."debtor_trans_details as line
				ON trans.trans_no=line.debtor_trans_no AND trans.type=line.debtor_trans_type,"
			.TB_PREF."debtors_master as debtor, "
			.TB_PREF."cust_branch as branch
		WHERE debtor.debtor_no = trans.debtor_no
			AND trans.tran_date >= '$date_after'
			AND trans.tran_date <= '$date_to'
			AND trans.branch_code = branch.branch_code";

   			$sql .= " AND (trans.type = ".ST_SALESINVOICE.") ";

    		$today =  date2sql(Today());
    		//$sql .= " AND trans.due_date < '$today'
			$sql .= " AND (trans.ov_amount + trans.ov_gst + trans.ov_freight_tax + 
				trans.ov_freight + trans.ov_discount-trans.discount1-trans.discount2 - trans.alloc > 0) ";

		$sql .= " GROUP BY trans.trans_no, trans.type";
		$sql .= "  ORDER BY tran_date DESC LIMIT 10";

		$result = db_query($sql);


   echo'<div class="col-xs-12">
          <div class="box">
            <div class="box-header">
               <h1 class="box-title col-md-9" style="margin-top:6px;font-weight:bolder">
			  	Top 10 Recent Sales Invoices
			  </h1>
			  
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>#</th>
                  <th>Ref</th>
                  <th>Client</th>
                  <th>Branch</th>
                  <th>Date</th>
                  <th>Due Date</th>
                  <th>Currency</th>
                  <th>Total</th> 				  				  
                </tr>';
               while ($myrow = db_fetch($result))
		      {
              echo '<tr>
                  <td>'. get_trans_view_str(ST_SALESINVOICE, $myrow["trans_no"]) .'</td>
                  <td>'. $myrow['reference'] .'</td>
                  <td>'. $myrow["name"] .'</td>
                  <td>'. $myrow['br_name'].'</td>
                  <td>'. sql2date($myrow['tran_date']).'</td>
				  <td>'. sql2date($myrow['due_date']) .'</td>
                  <td>'. $myrow['curr_code'] .'</td>
                  <td><span class="label label-success">'. price_format($myrow['TotalAmount']) .'</span></td>
                 
                  
                 </tr>';
				}

              echo '<tr style="background-color:white;"><td colspan="9">';
			  //echo'';
			  echo'<div class="box-footer clearfix" style="border-top-style:none;">
              <a href='.$path_to_root . '/sales/sales_order_entry.php?NewInvoice=0 class="btn btn-sm btn-info btn-flat pull-left">Place New Invoice</a>
              
              <a href="#" class="btn btn-sm btn-default btn-flat pull-right" data-toggle="modal" data-target="#RecentSalesInvoices">View All</a>
           
            </div>';
			  echo'</td></tr></tbody></table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>';
		/////////////

function get_sql_for_sales_orders_view_for_dashboard($selected_customer, $trans_type, $trans_no, $filter,
	$stock_item=null, $from='', $to='', $ref='', $location='', $customer_id=ALL_TEXT)
{

	$sql = "SELECT 
			sorder.order_no,
			sorder.reference,
			debtor.name,
			branch.br_name,"
			.($filter=='InvoiceTemplates'
				|| $filter=='DeliveryTemplates' ?
			 "sorder.comments, " : "sorder.customer_ref, ")
			."sorder.ord_date,
			sorder.delivery_date,
			sorder.deliver_to,
			Sum(line.unit_price*line.quantity*(1-line.discount_percent))+freight_cost AS OrderValue,
			sorder.type,
			debtor.curr_code,
			Sum(line.qty_sent) AS TotDelivered,
			Sum(line.quantity) AS TotQuantity
		FROM ".TB_PREF."sales_orders as sorder, "
			.TB_PREF."sales_order_details as line, "
			.TB_PREF."debtors_master as debtor, "
			.TB_PREF."cust_branch as branch
			WHERE sorder.order_no = line.order_no
			AND sorder.trans_type = line.trans_type
			AND line.quantity > 0
			AND sorder.trans_type = ".db_escape($trans_type)."
			AND sorder.debtor_no = debtor.debtor_no
			AND sorder.branch_code = branch.branch_code
			AND debtor.debtor_no = branch.debtor_no";

	if (isset($trans_no) && $trans_no != "")
	{
		// search orders with number like
		$number_like = "%".$trans_no;
		$sql .= " AND sorder.order_no LIKE ".db_escape($number_like);
//				." GROUP BY sorder.order_no";
	}
	elseif ($ref != "")
	{
		// search orders with reference like
		$number_like = "%".$ref."%";
		$sql .= " AND sorder.reference LIKE ".db_escape($number_like);
//				." GROUP BY sorder.order_no";
	}
	else	// ... or select inquiry constraints
	{
		if ($filter!='DeliveryTemplates' && $filter!='InvoiceTemplates' && $filter!='OutstandingOnly')
		{
			$date_after = date2sql($from);
			$date_before = date2sql($to);

			$sql .=  " AND sorder.ord_date >= '$date_after'"
					." AND sorder.ord_date <= '$date_before'";
		}
	}
		if ($trans_type == ST_SALESQUOTE && !check_value('show_all'))
			$sql .= " AND sorder.delivery_date >= '".date2sql(Today())."' AND line.qty_sent=0"; // show only outstanding, not realized quotes

		if ($selected_customer != -1)
			$sql .= " AND sorder.debtor_no=".db_escape($selected_customer);

		if (isset($stock_item))
			$sql .= " AND line.stk_code=".db_escape($stock_item);

		if ($location)
			$sql .= " AND sorder.from_stk_loc = ".db_escape($location);

		if ($filter=='OutstandingOnly')
			$sql .= " AND line.qty_sent < line.quantity";

		elseif ($filter=='InvoiceTemplates' || $filter=='DeliveryTemplates')
			$sql .= " AND sorder.type=1";

		//Chaiatanya : New Filter
		if ($customer_id != ALL_TEXT)
			$sql .= " AND sorder.debtor_no = ".db_escape($customer_id);

		$sql .= " GROUP BY sorder.order_no,
					sorder.debtor_no,
					sorder.branch_code,
					sorder.customer_ref,
					sorder.ord_date,
					sorder.deliver_to";
	return $sql;
}

		$sql = get_sql_for_sales_orders_view_for_dashboard(-1, ST_SALESORDER, '', '',null, add_days(Today(), -30), add_days(Today(), 1));
	$sql .= "  ORDER BY ord_date DESC LIMIT 10";
		$result = db_query($sql);

   echo'<div class="col-xs-12">
          <div class="box">
            <div class="box-header">
               <h1 class="box-title col-md-9" style="margin-top:6px;font-weight:bolder">
			  	Top 10 Recent Sale Order
			  </h1>
			  
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>Order #</th>
                  <th>Ref</th>
                  <th>Client</th>
                  <th>Branch</th>
                  <th>Order Date</th>
                  <th>Required By</th>
                  <th>Currency</th>
                  <th>Order Total</th>
				  <th>Delivery To</th>				  				  
                </tr>';
               while ($myrow = db_fetch($result))
		      {
              echo '<tr>
                  <td>'. get_trans_view_str(ST_SALESORDER, $myrow["order_no"]) .'</td>
                  <td>'. $myrow['reference'] .'</td>
                  <td>'. $myrow["name"] .'</td>
                  <td>'. $myrow['br_name'].'</td>
                  <td>'. sql2date($myrow['ord_date']).'</td>
				  <td>'. sql2date($myrow['delivery_date']) .'</td>
                  <td>'. $myrow['curr_code'] .'</td>
                  <td><span class="label label-success">'. price_format($myrow['OrderValue']) .'</span></td>
                  <td><span class="label label-warning">'. price_format($myrow['TotQuantity']) .'</span></td>
                  
                 </tr>';
				}

              echo '<tr style="background-color:white;"><td colspan="9">';
			  //echo'';
			  echo'<div class="box-footer clearfix" style="border-top-style:none;">
              <a href='.$path_to_root . '/sales/sales_order_entry.php?NewOrder=Yes class="btn btn-sm btn-info btn-flat pull-left">Place New Order</a>
             
              <a href="#" class="btn btn-sm btn-default btn-flat pull-right" data-toggle="modal" data-target="#RecentSaleOrder">View All</a>
             
            </div>';
			  echo'</td></tr></tbody></table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>';
    }
}